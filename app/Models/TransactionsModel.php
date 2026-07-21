<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionsModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_compte_source', 'id_compte_destination', 'id_type_operation', 'montant', 'date_transaction', 'frais'];
    protected $useTimestamps = false;

    private TypeOperationModel $typeOperationModel;

    public function __construct()
    {
        parent::__construct();
        $this->typeOperationModel = new TypeOperationModel();
    }

    private function getTypeIdByLibelle(string $libelle): ?int
    {
        $id = $this->typeOperationModel->getIdByLibelle($libelle);
        return $id ? (int) $id : null;
    }

    public function getTransactionsByCompte($id_compte)
    {
        return $this->select('transactions.*, types_operations.libelle as type_operation')
            ->join('types_operations', 'types_operations.id = transactions.id_type_operation')
            ->where('id_compte_source', $id_compte)
            ->orWhere('id_compte_destination', $id_compte)
            ->orderBy('date_transaction', 'DESC')
            ->findAll();
    }

    public function getTransactionsByCompteAndType($id_compte, $id_type_operation)
    {
        return $this->select('transactions.*, types_operations.libelle as type_operation')
            ->join('types_operations', 'types_operations.id = transactions.id_type_operation')
            ->where('id_compte_source', $id_compte)
            ->orWhere('id_compte_destination', $id_compte)
            ->where('id_type_operation', $id_type_operation)
            ->orderBy('date_transaction', 'DESC')
            ->findAll();
    }

    public function getGainByTransfert()
    {
        $id = $this->getTypeIdByLibelle('Transfert');
        return $id ? $this->selectSum('frais')->where('id_type_operation', $id)->first() : null;
    }

    public function getGainByRetrait()
    {
        $id = $this->getTypeIdByLibelle('Retrait');
        return $id ? $this->selectSum('frais')->where('id_type_operation', $id)->first() : null;
    }

    public function getGainByTransfertForOperatorType(int $isOtherOperator): array
    {
        $id = $this->getTypeIdByLibelle('Transfert');
        if (!$id) {
            return ['frais' => 0];
        }
        return $this->selectSum('transactions.frais', 'frais')
            ->join('comptes as compte_destination', 'compte_destination.id = transactions.id_compte_destination', 'left')
            ->join('clients as client_destination', 'client_destination.id = compte_destination.id_client', 'left')
            ->join('prefixes', 'prefixes.id = client_destination.id_prefixe', 'left')
            ->where('transactions.id_type_operation', $id)
            ->where('prefixes.est_autre_operateur', $isOtherOperator)
            ->first();
    }

    public function getTransferGainBreakdown(): array
    {
        $id = $this->getTypeIdByLibelle('Transfert');
        if (!$id) {
            return [];
        }
        return $this->select('prefixes.prefixe, prefixes.est_autre_operateur, COALESCE(SUM(transactions.frais), 0) as total_frais, COALESCE(SUM(transactions.montant), 0) as total_montant')
            ->join('comptes as compte_destination', 'compte_destination.id = transactions.id_compte_destination', 'left')
            ->join('clients as client_destination', 'client_destination.id = compte_destination.id_client', 'left')
            ->join('prefixes', 'prefixes.id = client_destination.id_prefixe', 'left')
            ->where('transactions.id_type_operation', $id)
            ->groupBy('prefixes.id, prefixes.prefixe, prefixes.est_autre_operateur')
            ->orderBy('prefixes.prefixe', 'ASC')
            ->findAll();
    }

    /**
     * Statistiques complètes par préfixe : retraits (basés sur le compte source)
     * et transferts (basés sur le compte destination), fusionnés par préfixe.
     */
    public function getGainBreakdownByPrefix(): array
    {
        $idRetrait = $this->getTypeIdByLibelle('Retrait');
        $idTransfert = $this->getTypeIdByLibelle('Transfert');
        
        $retraits = $idRetrait ? $this->select("
                prefixes.id as prefixe_id,
                prefixes.prefixe as prefixe,
                prefixes.est_autre_operateur as est_autre_operateur,
                COALESCE(SUM(transactions.frais), 0) as total_frais_retrait,
                COALESCE(SUM(transactions.montant), 0) as total_montant_retrait,
                COUNT(transactions.id) as nombre_retrait
            ")
            ->join('comptes as compte_source', 'compte_source.id = transactions.id_compte_source', 'left')
            ->join('clients as client_source', 'client_source.id = compte_source.id_client', 'left')
            ->join('prefixes', 'prefixes.id = client_source.id_prefixe', 'left')
            ->where('transactions.id_type_operation', $idRetrait)
            ->groupBy('prefixes.id, prefixes.prefixe, prefixes.est_autre_operateur')
            ->findAll() : [];

        $transferts = $idTransfert ? $this->select("
                prefixes.id as prefixe_id,
                prefixes.prefixe as prefixe,
                prefixes.est_autre_operateur as est_autre_operateur,
                COALESCE(SUM(transactions.frais), 0) as total_frais_transfert,
                COALESCE(SUM(transactions.montant), 0) as total_montant_transfert,
                COUNT(transactions.id) as nombre_transfert
            ")
            ->join('comptes as compte_destination', 'compte_destination.id = transactions.id_compte_destination', 'left')
            ->join('clients as client_destination', 'client_destination.id = compte_destination.id_client', 'left')
            ->join('prefixes', 'prefixes.id = client_destination.id_prefixe', 'left')
            ->where('transactions.id_type_operation', $idTransfert)
            ->groupBy('prefixes.id, prefixes.prefixe, prefixes.est_autre_operateur')
            ->findAll() : [];

        $merged = [];

        foreach ($retraits as $r) {
            $key = $r['prefixe_id'] ?? 'inconnu';
            $merged[$key] = [
                'prefixe' => $r['prefixe'] ?? 'N/A',
                'est_autre_operateur' => $r['est_autre_operateur'] ?? 0,
                'total_frais_retrait' => (float) $r['total_frais_retrait'],
                'total_montant_retrait' => (float) $r['total_montant_retrait'],
                'nombre_retrait' => (int) $r['nombre_retrait'],
                'total_frais_transfert' => 0.0,
                'total_montant_transfert' => 0.0,
                'nombre_transfert' => 0,
            ];
        }

        foreach ($transferts as $t) {
            $key = $t['prefixe_id'] ?? 'inconnu';
            if (!isset($merged[$key])) {
                $merged[$key] = [
                    'prefixe' => $t['prefixe'] ?? 'N/A',
                    'est_autre_operateur' => $t['est_autre_operateur'] ?? 0,
                    'total_frais_retrait' => 0.0,
                    'total_montant_retrait' => 0.0,
                    'nombre_retrait' => 0,
                    'total_frais_transfert' => 0.0,
                    'total_montant_transfert' => 0.0,
                    'nombre_transfert' => 0,
                ];
            }
            $merged[$key]['total_frais_transfert'] = (float) $t['total_frais_transfert'];
            $merged[$key]['total_montant_transfert'] = (float) $t['total_montant_transfert'];
            $merged[$key]['nombre_transfert'] = (int) $t['nombre_transfert'];
        }

        foreach ($merged as &$row) {
            $row['total_frais'] = $row['total_frais_retrait'] + $row['total_frais_transfert'];
            $row['total_montant'] = $row['total_montant_retrait'] + $row['total_montant_transfert'];
            $row['nombre_transactions'] = $row['nombre_retrait'] + $row['nombre_transfert'];
        }
        unset($row);

        usort($merged, fn($a, $b) => strcmp((string) $a['prefixe'], (string) $b['prefixe']));

        return array_values($merged);
    }

    public function getGainByTransfertByPrefixeId($prefixe_id)
    {
        $id = $this->getTypeIdByLibelle('Transfert');
        if (!$id) {
            return null;
        }
        return $this->selectSum('frais')
            ->join('comptes', 'comptes.id = transactions.id_compte_source')
            ->where('id_type_operation', $id)
            ->where('comptes.id_client LIKE', $prefixe_id . '%')
            ->first();
    }

    public function getGainByRetraitByPrefixeId($prefixe_id)
    {
        $id = $this->getTypeIdByLibelle('Retrait');
        if (!$id) {
            return null;
        }
        return $this->selectSum('frais')
            ->join('comptes', 'comptes.id = transactions.id_compte_source')
            ->where('id_type_operation', $id)
            ->where('comptes.id_client LIKE', $prefixe_id . '%')
            ->first();
    }

}