<?php

namespace App\Controllers;

use App\Models\ActiviteModel;
use App\Models\PurchaseModel;
use App\Models\RegimeModel;
use App\Models\SuggestionModel;
use App\Models\UserObjectifModel;
use App\Models\WalletModel;
use Config\Database;

class Suggestion extends BaseController
{
    public function index(): string
    {
        $userId = session()->get('user')['id'];
        $suggestionModel = new SuggestionModel();

        return view('Suggestions/index', [
            'suggestions' => $suggestionModel->getSuggestionsByUser($userId),
        ]);
    }

    public function generate()
    {
        $userId = session()->get('user')['id'];
        $objectifModel = new UserObjectifModel();
        $objectifs = $objectifModel->getObjectifsByUser($userId);

        if (empty($objectifs)) {
            return redirect()->back()->with('error', 'Veuillez choisir un objectif avant de generer des suggestions.');
        }

        $objectifId = (int) $objectifs[0]['id'];
        $regimes = (new RegimeModel())->getAllRegimes();
        $activites = (new ActiviteModel())->getAllActivites();

        [$shortRegime, $longRegime] = $this->pickRegimes($regimes, $objectifId);
        [$shortActivite, $longActivite] = $this->pickActivites($activites, $objectifId);

        if (!$shortRegime || !$longRegime || !$shortActivite || !$longActivite) {
            return redirect()->back()->with('error', 'Donnees insuffisantes pour generer les suggestions.');
        }

        $suggestionModel = new SuggestionModel();
        $suggestionModel->clearSuggestionsByUser($userId);

        $now = date('Y-m-d H:i:s');
        $suggestionModel->createSuggestion([
            'user_id' => $userId,
            'regime_id' => $shortRegime['id'],
            'activite_id' => $shortActivite['id'],
            'date' => $now,
        ]);
        $suggestionModel->createSuggestion([
            'user_id' => $userId,
            'regime_id' => $longRegime['id'],
            'activite_id' => $longActivite['id'],
            'date' => $now,
        ]);

        return redirect()->to('/profile')->with('success', 'Suggestions generees avec succes.');
    }

    public function show($id)
    {
        $userId = session()->get('user')['id'];
        $suggestionModel = new SuggestionModel();
        $purchaseModel = new PurchaseModel();
        $walletModel = new WalletModel();

        $suggestion = $suggestionModel->getSuggestionForUser((int) $id, $userId);
        if (!$suggestion) {
            return redirect()->to('/profile')->with('error', 'Suggestion introuvable.');
        }

        $basePrice = (float) $suggestion['regime_prix'];
        $discountRate = session()->get('user')['is_gold'] ? 0.15 : 0.0;
        $finalPrice = round($basePrice * (1 - $discountRate), 2);

        return view('Suggestions/detail', [
            'suggestion' => $suggestion,
            'purchase' => $purchaseModel->getPurchaseForSuggestionUser($userId, (int) $id),
            'wallet' => $walletModel->getWalletByUser($userId),
            'pricing' => [
                'base_price' => $basePrice,
                'discount_rate' => $discountRate,
                'final_price' => $finalPrice,
            ],
        ]);
    }

    public function buy($id)
    {
        $userId = session()->get('user')['id'];
        $suggestionModel = new SuggestionModel();
        $purchaseModel = new PurchaseModel();
        $walletModel = new WalletModel();

        $suggestion = $suggestionModel->getSuggestionForUser((int) $id, $userId);
        if (!$suggestion) {
            return redirect()->to('/profile')->with('error', 'Suggestion introuvable.');
        }

        if ($purchaseModel->getPurchaseForSuggestionUser($userId, (int) $id)) {
            return redirect()->to('/suggestions/' . $id)->with('info', 'Suggestion deja achetee.');
        }

        $basePrice = (float) $suggestion['regime_prix'];
        $finalPrice = session()->get('user')['is_gold'] ? round($basePrice * 0.85, 2) : $basePrice;

        $wallet = $walletModel->getWalletByUser($userId);
        if (!$wallet || (float) $wallet['solde'] < $finalPrice) {
            return redirect()->to('/suggestions/' . $id)->with('error', 'Solde insuffisant.');
        }

        $db = Database::connect();
        $db->transStart();

        $deducted = $walletModel->deductMoney($userId, $finalPrice);
        if (!$deducted) {
            $db->transComplete();
            return redirect()->to('/suggestions/' . $id)->with('error', 'Solde insuffisant.');
        }

        $purchaseModel->createPurchase([
            'user_id' => $userId,
            'suggestion_id' => (int) $id,
            'regime_id' => $suggestion['regime_id'],
            'activite_id' => $suggestion['activite_id'],
            'prix_total' => $finalPrice,
            'date' => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->to('/suggestions/' . $id)->with('error', 'Achat non termine.');
        }

        return redirect()->to('/suggestions/' . $id)->with('success', 'Achat effectue avec succes.');
    }

    public function exportPdf($id)
    {
        $userId = session()->get('user')['id'];
        $purchaseModel = new PurchaseModel();
        $purchase = $purchaseModel->getPurchaseDetail($userId, (int) $id);

        if (!$purchase) {
            return redirect()->to('/suggestions/' . $id)->with('error', 'Veuillez acheter la suggestion avant export.');
        }

        if (!class_exists('\Dompdf\\Dompdf')) {
            return redirect()->to('/suggestions/' . $id)
                ->with('error', 'Dompdf manquant. Installez dompdf/dompdf via Composer.');
        }

        $dompdf = new \Dompdf\Dompdf();
        $html = view('Suggestions/pdf', ['purchase' => $purchase]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="suggestion-' . $id . '.pdf"')
            ->setBody($dompdf->output());
    }

    private function pickRegimes(array $regimes, int $objectifId): array
    {
        $filtered = array_filter($regimes, function ($regime) use ($objectifId) {
            if ($objectifId === 1) {
                return (float) $regime['variation_poids'] > 0;
            }
            if ($objectifId === 2) {
                return (float) $regime['variation_poids'] < 0;
            }
            return true;
        });

        if (empty($filtered)) {
            $filtered = $regimes;
        }

        if ($objectifId === 3) {
            usort($filtered, function ($a, $b) {
                return abs((float) $a['variation_poids']) <=> abs((float) $b['variation_poids']);
            });
            $filtered = array_slice($filtered, 0, min(3, count($filtered)));
        }

        usort($filtered, function ($a, $b) {
            return (int) $a['duree'] <=> (int) $b['duree'];
        });

        $short = $filtered[0] ?? null;
        $long = $filtered[count($filtered) - 1] ?? null;

        if ($short && $long && $short['id'] === $long['id'] && count($filtered) > 1) {
            $long = $filtered[1];
        }

        return [$short, $long];
    }

    private function pickActivites(array $activites, int $objectifId): array
    {
        if (empty($activites)) {
            return [null, null];
        }

        usort($activites, function ($a, $b) use ($objectifId) {
            $aCal = (int) $a['calories'];
            $bCal = (int) $b['calories'];

            if ($objectifId === 1) {
                return $aCal <=> $bCal;
            }
            if ($objectifId === 2) {
                return $bCal <=> $aCal;
            }
            return $aCal <=> $bCal;
        });

        $candidateCount = min(3, count($activites));
        $candidates = array_slice($activites, 0, $candidateCount);

        usort($candidates, function ($a, $b) {
            return (int) $a['duree'] <=> (int) $b['duree'];
        });

        $short = $candidates[0] ?? null;
        $long = $candidates[count($candidates) - 1] ?? null;

        if ($short && $long && $short['id'] === $long['id'] && count($activites) > 1) {
            $long = $activites[1];
        }

        return [$short, $long];
    }
}
