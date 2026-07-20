<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-hash" content="<?= csrf_hash() ?>">
    <title>E-Money Client - Espace Personnel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .modal-transition {
            transition: opacity 0.25s ease-out, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>

<body class="h-full text-slate-900 font-sans antialiased bg-slate-50">

    <div class="flex h-screen overflow-hidden">

        <!-- DESKTOP SIDEBAR -->
        <aside class="hidden md:flex md:flex-shrink-0 flex-col w-64 bg-white border-r border-slate-200">
            <div class="flex items-center gap-2 px-6 py-5 border-b border-slate-100">
                <div
                    class="h-10 w-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-indigo-200">
                    <i class="bi bi-wallet2 text-xl"></i>
                </div>
                <span class="font-extrabold text-lg text-slate-800 tracking-tight">E-Money Client</span>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="<?= site_url('clients/dashboard') ?>"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl bg-indigo-50 text-indigo-600 transition-all">
                    <i class="bi bi-grid-1x2-fill"></i> Tableau de bord
                </a>
                <a href="#" onclick="openModal('modalTransfert')"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <i class="bi bi-arrow-left-right"></i> Transférer de l'argent
                </a>
                <a href="#" onclick="openModal('modalDepot')"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <i class="bi bi-arrow-down-circle"></i> Faire un dépôt
                </a>
                <a href="#" onclick="openModal('modalRetrait')"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <i class="bi bi-arrow-up-circle"></i> Faire un retrait
                </a>
                <a href="<?= site_url('logout') ?>"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </nav>
        </aside>

        <!-- CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100 md:px-8">
                <div class="flex items-center gap-3 md:hidden">
                    <div class="h-9 w-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white">
                        <i class="bi bi-wallet2 text-lg"></i>
                    </div>
                    <span class="font-extrabold text-slate-900">E-Money</span>
                </div>

                <div class="hidden md:block">
                    <h1 class="text-xl font-bold text-slate-800">Mon espace financier</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Gérez vos dépôts, retraits et transferts instantanément</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2.5 pl-2 border-l border-slate-100">
                        <img src="https://placehold.co/100x100/4f46e5/ffffff?text=<?= esc(strtoupper(substr($client['nom'] ?? 'C', 0, 1))) ?>"
                            alt="Profil" class="h-9 w-9 rounded-full object-cover border-2 border-slate-200">
                        <div class="hidden sm:block text-left">
                            <span
                                class="block text-xs font-semibold text-slate-400 leading-none"><?= esc($client['nom'] ?? '') ?></span>
                            <span
                                class="text-[10px] font-bold text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded mt-0.5 inline-block">Compte
                                Actif</span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-5 md:p-8 space-y-6 pb-24 md:pb-8 no-scrollbar">

                <!-- Alertes dynamiques -->
                <div id="alertContainer" class="hidden transform translate-y-2 opacity-0 transition-all duration-300">
                    <div id="alertBox" class="p-4 rounded-xl shadow-sm flex items-center justify-between gap-3 border">
                        <div class="flex items-center gap-3">
                            <i id="alertIcon" class="bi text-lg"></i>
                            <p id="alertMessage" class="text-sm font-semibold"></p>
                        </div>
                        <button onclick="dismissAlert()" class="text-slate-400 hover:text-slate-600"><i
                                class="bi bi-x-lg text-xs"></i></button>
                    </div>
                </div>

                <!-- ZONE SOLDE -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div
                        class="lg:col-span-2 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden flex flex-col justify-between min-h-[190px]">
                        <div class="absolute -right-10 -bottom-10 h-40 w-40 bg-indigo-500/10 rounded-full blur-2xl">
                        </div>

                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-semibold tracking-wider text-indigo-200/80 uppercase">Solde
                                    disponible</span>
                                <div class="flex items-center gap-3 mt-1.5">
                                    <h2 id="soldeClient"
                                        class="text-3xl md:text-4xl font-extrabold tracking-tight transition-all duration-300"
                                        data-solde="<?= (float) $compte['solde'] ?>">
                                        <?= number_format((float) $compte['solde'], 2, ',', ' ') ?> Ar
                                    </h2>
                                    <button onclick="toggleSoldeVisibility()"
                                        class="text-indigo-200 hover:text-white transition focus:outline-none p-1.5 rounded-lg bg-white/5">
                                        <i id="eyeIcon" class="bi bi-eye-slash-fill text-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <span
                                class="text-xs bg-indigo-500/20 text-indigo-300 border border-indigo-400/20 px-2.5 py-1 rounded-full font-medium font-mono">Malagasy
                                Ariary</span>
                        </div>

                        <div class="flex justify-between items-end mt-6 border-t border-white/5 pt-4">
                            <div class="text-xs text-indigo-200/60">
                                <div>Numéro de compte lié</div>
                                <div class="font-mono text-white/90 font-semibold mt-0.5">
                                    <?= esc($client['telephone'] ?? '') ?>
                                </div>
                            </div>
                            <div class="h-6 opacity-30"><i class="bi bi-credit-card-2-front text-2xl"></i></div>
                        </div>
                    </div>

                    <div
                        class="bg-white rounded-2xl p-6 border border-slate-200 flex flex-col justify-between shadow-sm">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm uppercase tracking-wider mb-2">Mon compte</h4>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl"><i
                                        class="bi bi-shield-check text-xl"></i></div>
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Compte
                                        n°<?= (int) $compte['id'] ?></span>
                                    <span class="text-xs text-slate-400">Client depuis la création du compte</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OPÉRATIONS RAPIDES -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Opérations Rapides</h3>
                    <div class="grid grid-cols-3 gap-3 md:gap-4">
                        <button onclick="openModal('modalDepot')"
                            class="flex flex-col items-center justify-center p-4 bg-white rounded-xl border border-slate-100 shadow-sm hover:border-indigo-400 hover:bg-slate-50 transition group focus:outline-none">
                            <div
                                class="h-12 w-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-2 group-hover:scale-105 transition-all">
                                <i class="bi bi-box-arrow-in-down"></i>
                            </div>
                            <span class="text-xs md:text-sm font-bold text-slate-700">Dépôt</span>
                            <span class="hidden sm:inline text-[10px] text-slate-400 mt-0.5">Automatique</span>
                        </button>
                        <button onclick="openModal('modalRetrait')"
                            class="flex flex-col items-center justify-center p-4 bg-white rounded-xl border border-slate-100 shadow-sm hover:border-orange-400 hover:bg-slate-50 transition group focus:outline-none">
                            <div
                                class="h-12 w-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-xl mb-2 group-hover:scale-105 transition-all">
                                <i class="bi bi-box-arrow-up"></i>
                            </div>
                            <span class="text-xs md:text-sm font-bold text-slate-700">Retrait</span>
                            <span class="hidden sm:inline text-[10px] text-slate-400 mt-0.5">Automatique</span>
                        </button>
                        <button onclick="openModal('modalTransfert')"
                            class="flex flex-col items-center justify-center p-4 bg-white rounded-xl border border-slate-100 shadow-sm hover:border-emerald-400 hover:bg-slate-50 transition group focus:outline-none">
                            <div
                                class="h-12 w-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-2 group-hover:scale-105 transition-all">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                            <span class="text-xs md:text-sm font-bold text-slate-700">Transfert</span>
                            <span class="hidden sm:inline text-[10px] text-slate-400 mt-0.5">Vers un destinataire</span>
                        </button>
                    </div>
                </div>

                <!-- HISTORIQUE -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div
                        class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">Transactions récentes</h3>
                            <p class="text-xs text-slate-400">Historique de vos dernières opérations financières</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <i class="bi bi-search absolute left-3 top-2.5 text-xs text-slate-400"></i>
                                <input type="text" id="txnSearch"
                                    class="pl-8 pr-4 py-1.5 text-xs bg-slate-50 rounded-xl border-none focus:ring-1 focus:ring-indigo-500 w-full sm:w-48 text-slate-700"
                                    placeholder="Rechercher...">
                            </div>
                            <select id="txnFilter"
                                class="py-1.5 px-3 text-xs bg-slate-50 rounded-xl border-none text-slate-600 font-medium">
                                <option value="all">Tout</option>
                                <option value="depot">Dépôts</option>
                                <option value="retrait">Retraits</option>
                                <option value="transfert">Transferts</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="hidden md:table w-full align-middle text-left">
                            <thead class="bg-slate-50 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-6">ID Transaction</th>
                                    <th class="py-3 px-6">Type d'opération</th>
                                    <th class="py-3 px-6">Frais appliqués</th>
                                    <th class="py-3 px-6">Date d'effet</th>
                                    <th class="py-3 px-6 text-right">Montant brut</th>
                                </tr>
                            </thead>
                            <tbody id="txnTableBody" class="divide-y divide-slate-100 text-sm">
                                <?php foreach ($transactions as $t): ?>
                                    <?php
                                    $estSortant = (int) $t['id_compte_source'] === (int) $compte['id'];
                                    $type = $t['type_operation'] ?? '';
                                    ?>
                                    <tr class="hover:bg-slate-50/50 transition" data-type="<?= esc($type) ?>"
                                        data-search="<?= esc(strtolower($t['id'] . ' ' . $type)) ?>">
                                        <td class="py-4 px-6 font-mono text-xs font-semibold text-slate-400">
                                            TXN<?= str_pad((string) $t['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                        <td class="py-4 px-6"><span
                                                class="font-bold text-slate-800"><?= esc(ucfirst($type)) ?></span></td>
                                        <td class="py-4 px-6 text-xs text-slate-400 font-medium">
                                            <?= number_format((float) $t['frais'], 2, ',', ' ') ?> Ar
                                        </td>
                                        <td class="py-4 px-6 text-xs text-slate-400">
                                            <?= date('d M H:i', strtotime($t['date_transaction'])) ?>
                                        </td>
                                        <td
                                            class="py-4 px-6 text-right font-extrabold <?= $estSortant ? 'text-rose-600' : 'text-emerald-600' ?>">
                                            <?= $estSortant ? '-' : '+' ?>
                                            <?= number_format((float) $t['montant'], 2, ',', ' ') ?>
                                            Ar
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div id="noTxnState" class="hidden py-8 text-center text-slate-400 text-sm">
                        <i class="bi bi-slash-circle text-2xl d-block mb-1.5"></i>
                        Aucune transaction ne correspond aux filtres.
                    </div>
                </div>

            </main>

            <!-- MODAL DÉPÔT -->
            <div id="modalDepot"
                class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 transition-all duration-300">
                <div
                    class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden transform translate-y-full sm:translate-y-0 sm:scale-95 modal-transition">
                    <div class="px-6 py-4 bg-indigo-600 text-white flex justify-between items-center">
                        <h3 class="font-bold text-lg"><i class="bi bi-box-arrow-in-down me-2"></i>Dépôt Automatique</h3>
                        <button onclick="closeModal('modalDepot')" class="text-white/80 hover:text-white p-1"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                    <form id="formDepot" onsubmit="handleFormSubmit(event, 'depot')" class="p-6 space-y-4">
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Montant
                                du dépôt (Ar)</label>
                            <div class="relative rounded-xl shadow-sm">
                                <input type="number" id="amountDepot" min="100" step="1"
                                    class="w-full pl-4 pr-12 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold"
                                    placeholder="ex: 50000" required>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-sm font-bold text-slate-400">
                                    Ar</div>
                            </div>
                        </div>
                        <div class="pt-4 flex gap-3">
                            <button type="button" onclick="closeModal('modalDepot')"
                                class="flex-1 py-3 text-sm font-semibold text-slate-500 bg-slate-50 rounded-xl hover:bg-slate-100 transition">Annuler</button>
                            <button type="submit"
                                class="flex-1 py-3 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition">Valider
                                le dépôt</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL RETRAIT -->
            <div id="modalRetrait"
                class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 transition-all duration-300">
                <div
                    class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden transform translate-y-full sm:translate-y-0 sm:scale-95 modal-transition">
                    <div class="px-6 py-4 bg-orange-500 text-white flex justify-between items-center">
                        <h3 class="font-bold text-lg"><i class="bi bi-box-arrow-up me-2"></i>Retrait Automatique</h3>
                        <button onclick="closeModal('modalRetrait')" class="text-white/80 hover:text-white p-1"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                    <form id="formRetrait" onsubmit="handleFormSubmit(event, 'retrait')" class="p-6 space-y-4">
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Montant
                                du retrait (Ar)</label>
                            <div class="relative rounded-xl shadow-sm">
                                <input type="number" id="amountRetrait" min="100" step="1"
                                    class="w-full pl-4 pr-12 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 font-bold"
                                    placeholder="ex: 5000" required>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-sm font-bold text-slate-400">
                                    Ar</div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Frais calculés selon le barème en vigueur.</p>
                        </div>
                        <div class="pt-4 flex gap-3">
                            <button type="button" onclick="closeModal('modalRetrait')"
                                class="flex-1 py-3 text-sm font-semibold text-slate-500 bg-slate-50 rounded-xl hover:bg-slate-100 transition">Annuler</button>
                            <button type="submit"
                                class="flex-1 py-3 text-sm font-semibold text-white bg-orange-500 rounded-xl hover:bg-orange-600 shadow-lg shadow-orange-100 transition">Valider
                                le retrait</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL TRANSFERT -->
            <div id="modalTransfert"
                class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 transition-all duration-300">
                <div
                    class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden transform translate-y-full sm:translate-y-0 sm:scale-95 modal-transition">
                    <div class="px-6 py-4 bg-emerald-600 text-white flex justify-between items-center">
                        <h3 class="font-bold text-lg"><i class="bi bi-arrow-left-right me-2"></i>Faire un transfert</h3>
                        <button onclick="closeModal('modalTransfert')" class="text-white/80 hover:text-white p-1"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                    <form id="formTransfert" onsubmit="handleFormSubmit(event, 'transfert')" class="p-6 space-y-4">
                        <div id="recipientsContainer">
                            <div class="flex items-center gap-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Numéros
                                    de téléphone destinataires</label>
                                <button type="button" onclick="addRecipient()"
                                    class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 mb-1.5">
                                    <i class="bi bi-plus-circle me-1"></i>Ajouter un numéro
                                </button>
                            </div>
                            <div class="recipient-group" data-index="0">
                                <div class="relative rounded-xl shadow-sm">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="bi bi-phone"></i>
                                    </div>
                                    <input type="text" class="phone-input w-full pl-10 pr-12 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-semibold"
                                        placeholder="ex: 0331234567" required>
                                    <button type="button" onclick="removeRecipient(this)"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-red-500">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium">Doit commencer par le même préfixe que votre numéro (même opérateur) : 032, 033, 034, 037, ou 038.</p>
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Montant total
                                à transférer (Ar)</label>
                            <div class="relative rounded-xl shadow-sm">
                                <input type="number" id="amountTransfert" min="100" step="1"
                                    class="w-full pl-4 pr-12 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold"
                                    placeholder="ex: 20000" required>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-sm font-bold text-slate-400">
                                    Ar</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="includeFees"
                                class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500">
                            <label for="includeFees" class="text-sm font-medium text-slate-700">Inclure les frais dans le montant</label>
                        </div>
                        <p class="text-[10px] text-slate-400">Si coché, le montant total est divisé par le nombre de destinataires. Les frais seront déduits de votre solde en plus.</p>
                        <div class="pt-4 flex gap-3">
                            <button type="button" onclick="closeModal('modalTransfert')"
                                class="flex-1 py-3 text-sm font-semibold text-slate-500 bg-slate-50 rounded-xl hover:bg-slate-100 transition">Annuler</button>
                            <button type="submit"
                                class="flex-1 py-3 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition">Confirmer
                                l'envoi</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- BARRE MOBILE -->
            <nav
                class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200/80 px-6 py-2.5 flex justify-between items-center z-40 shadow-[0_-4px_12px_rgba(0,0,0,0.03)]">
                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
                    class="flex flex-col items-center gap-1 text-indigo-600 focus:outline-none">
                    <i class="bi bi-grid-1x2-fill text-lg"></i><span class="text-[10px] font-bold">Dashboard</span>
                </button>
                <button onclick="openModal('modalTransfert')"
                    class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i class="bi bi-arrow-left-right text-lg"></i><span class="text-[10px] font-medium">Transfert</span>
                </button>
                <button onclick="openModal('modalDepot')"
                    class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i class="bi bi-box-arrow-in-down text-lg"></i><span class="text-[10px] font-medium">Dépôt</span>
                </button>
                <button onclick="openModal('modalRetrait')"
                    class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i class="bi bi-box-arrow-up text-lg"></i><span class="text-[10px] font-medium">Retrait</span>
                </button>
            </nav>

        </div>
    </div>

    <script>
        // --- Jeton CSRF (nécessaire car les formulaires sont envoyés en AJAX / fetch) ---
        const CSRF_NAME = document.querySelector('meta[name="csrf-token-name"]').content;
        let csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

        let isSoldeVisible = true;
        let recipientIndex = 1;

        function formatAriary(amount) {
            return Number(amount).toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + " Ar";
        }

        function toggleSoldeVisibility() {
            isSoldeVisible = !isSoldeVisible;
            renderBalance();
        }

        function renderBalance() {
            const soldeElement = document.getElementById('soldeClient');
            const eyeIcon = document.getElementById('eyeIcon');
            const solde = parseFloat(soldeElement.dataset.solde);

            if (isSoldeVisible) {
                soldeElement.textContent = formatAriary(solde);
                eyeIcon.className = "bi bi-eye-slash-fill text-lg";
            } else {
                soldeElement.textContent = "• • • • • • Ar";
                eyeIcon.className = "bi bi-eye-fill text-lg";
            }
        }

        function setSolde(nouveauSolde) {
            document.getElementById('soldeClient').dataset.solde = nouveauSolde;
            renderBalance();
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            setTimeout(() => {
                const inner = modal.querySelector('.modal-transition');
                inner.classList.remove('translate-y-full', 'sm:scale-95');
                inner.classList.add('translate-y-0', 'sm:scale-100');
            }, 10);
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            const inner = modal.querySelector('.modal-transition');
            inner.classList.remove('translate-y-0', 'sm:scale-100');
            inner.classList.add('translate-y-full', 'sm:scale-95');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }

        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alertBox = document.getElementById('alertBox');
            const alertIcon = document.getElementById('alertIcon');
            const alertMessage = document.getElementById('alertMessage');

            alertMessage.textContent = message;

            if (type === 'success') {
                alertBox.className =
                    "p-4 rounded-xl shadow-sm flex items-center justify-between gap-3 border bg-emerald-50 border-emerald-200 text-emerald-800";
                alertIcon.className = "bi bi-check-circle-fill text-emerald-600";
            } else {
                alertBox.className =
                    "p-4 rounded-xl shadow-sm flex items-center justify-between gap-3 border bg-rose-50 border-rose-200 text-rose-800";
                alertIcon.className = "bi bi-exclamation-triangle-fill text-rose-600";
            }

            alertContainer.classList.remove('hidden');
            setTimeout(() => alertContainer.classList.remove('translate-y-2', 'opacity-0'), 10);
            setTimeout(() => dismissAlert(), 4000);
        }

        function dismissAlert() {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => alertContainer.classList.add('hidden'), 300);
        }

        function addRecipient() {
            const container = document.getElementById('recipientsContainer');
            const newGroup = document.createElement('div');
            newGroup.className = 'recipient-group mt-3';
            newGroup.dataset.index = recipientIndex;
            newGroup.innerHTML = `
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-phone"></i>
                    </div>
                    <input type="text" class="phone-input w-full pl-10 pr-12 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-semibold"
                        placeholder="ex: 0331234567" required>
                    <button type="button" onclick="removeRecipient(this)"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-red-500">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;
            container.appendChild(newGroup);
            recipientIndex++;
        }

        function removeRecipient(button) {
            const container = document.getElementById('recipientsContainer');
            const groups = container.querySelectorAll('.recipient-group');
            if (groups.length > 1) {
                button.closest('.recipient-group').remove();
            } else {
                showAlert('Vous devez avoir au moins un destinataire', 'danger');
            }
        }

        // Filtrage / recherche côté client sur les lignes déjà rendues par PHP
        function filterTransactions() {
            const query = document.getElementById('txnSearch').value.toLowerCase().trim();
            const filterType = document.getElementById('txnFilter').value;
            const rows = document.querySelectorAll('#txnTableBody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const matchesQuery = query === '' || row.dataset.search.includes(query);
                const matchesFilter = filterType === 'all' || row.dataset.type === filterType;
                const visible = matchesQuery && matchesFilter;
                row.classList.toggle('hidden', !visible);
                if (visible) visibleCount++;
            });

            document.getElementById('noTxnState').classList.toggle('hidden', visibleCount !== 0);
        }

        // --- Appels réels au ClientController (remplace la simulation JS de la maquette) ---
        async function handleFormSubmit(event, type) {
            event.preventDefault();

            const endpoints = {
                depot: "<?= site_url('client/depot') ?>",
                retrait: "<?= site_url('client/retrait') ?>",
                transfert: "<?= site_url('client/transfert') ?>",
            };

            const formData = new FormData();
            formData.append(CSRF_NAME, csrfHash);

            if (type === 'depot') {
                formData.append('montant', document.getElementById('amountDepot').value);
            } else if (type === 'retrait') {
                formData.append('montant', document.getElementById('amountRetrait').value);
            } else if (type === 'transfert') {
                // Collect all phone numbers
                const phoneInputs = document.querySelectorAll('.phone-input');
                const phones = [];
                phoneInputs.forEach(input => {
                    if (input.value.trim()) phones.push(input.value.trim());
                });
                formData.append('telephones', JSON.stringify(phones));
                formData.append('montant', document.getElementById('amountTransfert').value);
                formData.append('includeFees', document.getElementById('includeFees').checked ? '1' : '0');
            }

            try {
                const response = await fetch(endpoints[type], {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                // CodeIgniter renouvelle le hash CSRF à chaque requête : on le met à jour
                const newHash = response.headers.get('X-CSRF-TOKEN');
                if (newHash) csrfHash = newHash;

                if (data.success) {
                    showAlert(data.message, 'success');
                    setSolde(data.solde);
                    document.getElementById('form' + type.charAt(0).toUpperCase() + type.slice(1)).reset();
                    // Reset recipients container to 1
                    const container = document.getElementById('recipientsContainer');
                    const groups = container.querySelectorAll('.recipient-group');
                    groups.forEach((group, index) => {
                        if (index > 0) group.remove();
                    });
                    recipientIndex = 1;
                    closeModal('modal' + type.charAt(0).toUpperCase() + type.slice(1));
                    // Recharge la page pour afficher la nouvelle transaction dans l'historique
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    showAlert(data.message || 'Une erreur est survenue.', 'danger');
                }
            } catch (err) {
                showAlert('Erreur réseau, veuillez réessayer.', 'danger');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderBalance();
            document.getElementById('txnSearch').addEventListener('input', filterTransactions);
            document.getElementById('txnFilter').addEventListener('change', filterTransactions);
        });
    </script>

</body>

</html>