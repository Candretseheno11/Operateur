<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <!-- En-tête de page -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold text-dark mb-1">Situation des gains via les différents frais</h4>
            <p class="text-muted small mb-0">Suivi des commissions de retrait et de transfert, avec une séparation entre
                opérateur principal et autres opérateurs.</p>
        </div>
    </div>

    <!-- CARTES KPI -->
    <div class="row g-4 mb-4">
        <!-- Gains Retrait -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-success">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Gains retrait</span>
                            <div class="h3 fw-bold text-success mt-2 mb-0">
                                <?= number_format($gainRetrait ?? 0, 0, ',', ' ') ?> <small
                                    class="fs-6 text-muted">Ar</small>
                            </div>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                            <i class="bi bi-arrow-down-left-circle fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transferts vers l'opérateur principal -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-primary">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Transferts (Opérateur
                                principal)</span>
                            <div class="h3 fw-bold text-primary mt-2 mb-0">
                                <?= number_format($gainTransfertOperateur ?? 0, 0, ',', ' ') ?> <small
                                    class="fs-6 text-muted">Ar</small>
                            </div>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                            <i class="bi bi-arrow-right-left fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transferts vers les autres opérateurs -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-warning">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Transferts (Autres opérateurs)</span>
                            <div class="h3 fw-bold text-warning mt-2 mb-0">
                                <?= number_format($gainTransfertAutresOperateurs ?? 0, 0, ',', ' ') ?> <small
                                    class="fs-6 text-muted">Ar</small>
                            </div>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                            <i class="bi bi-globe fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLEAU 1: MONTANTS À ENVOYER -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <div class="d-flex align-items-center">
                <div class="p-2 bg-primary bg-opacity-10 rounded-3 text-primary me-3">
                    <i class="bi bi-wallet2 fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Montants à envoyer à chaque opérateur</h6>
                    <p class="text-muted small mb-0">Récapitulatif des flux cumulés à répartir par réseau.</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-secondary small text-uppercase">
                        <tr>
                            <th class="py-3">Opérateur</th>
                            <th class="py-3">Type</th>
                            <th class="py-3 text-end">Montant total transféré</th>
                            <th class="py-3 text-end">Commission collectée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($gainBreakdown)): ?>
                            <?php foreach ($gainBreakdown as $item): ?>
                                <tr>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark fs-6 border fw-semibold px-3 py-2">
                                            <?= esc($item['prefixe'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <?php if (!empty($item['est_autre_operateur'])): ?>
                                            <span
                                                class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-2 border border-warning-subtle">
                                                <i class="bi bi-dot me-1"></i>Autre opérateur
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2 border border-primary-subtle">
                                                <i class="bi bi-dot me-1"></i>Opérateur principal
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-end fw-semibold">
                                        <?= number_format((float) ($item['total_montant'] ?? 0), 0, ',', ' ') ?> <small
                                            class="text-muted">Ar</small>
                                    </td>
                                    <td class="py-3 text-end fw-bold text-success">
                                        +<?= number_format((float) ($item['total_frais'] ?? 0), 0, ',', ' ') ?> <small
                                            class="text-muted">Ar</small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-black-50"></i>
                                    Aucune donnée de transfert disponible pour le moment.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TABLEAU 2: STATISTIQUES PAR PRÉFIXE -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <div class="d-flex align-items-center">
                <div class="p-2 bg-primary bg-opacity-10 rounded-3 text-primary me-3">
                    <i class="bi bi-bar-chart-line fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Statistiques détaillées par préfixe</h6>
                    <p class="text-muted small mb-0">Détail des retraits et transferts par préfixe téléphonique.</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-secondary small text-uppercase">
                        <tr>
                            <th class="py-3">Préfixe</th>
                            <th class="py-3">Type</th>
                            <th class="py-3 text-end">Retraits (Vol)</th>
                            <th class="py-3 text-end">Retraits (Frais)</th>
                            <th class="py-3 text-end">Transferts (Vol)</th>
                            <th class="py-3 text-end">Transferts (Frais)</th>
                            <th class="py-3 text-end">Total Frais</th>
                            <th class="py-3 text-center">Nbre Trans.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($gainBreakdownByPrefix)): ?>
                            <?php foreach ($gainBreakdownByPrefix as $item): ?>
                                <tr>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark fs-6 border fw-semibold px-3 py-2">
                                            <?= esc($item['prefixe'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <?php if (!empty($item['est_autre_operateur'])): ?>
                                            <span
                                                class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-2 border border-warning-subtle">Autre</span>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2 border border-primary-subtle">Principal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-end text-muted">
                                        <?= number_format((float) ($item['total_montant_retrait'] ?? 0), 0, ',', ' ') ?> Ar
                                    </td>
                                    <td class="py-3 text-end text-success fw-semibold">
                                        <?= number_format((float) ($item['total_frais_retrait'] ?? 0), 0, ',', ' ') ?> Ar
                                    </td>
                                    <td class="py-3 text-end text-muted">
                                        <?= number_format((float) ($item['total_montant_transfert'] ?? 0), 0, ',', ' ') ?> Ar
                                    </td>
                                    <td class="py-3 text-end text-success fw-semibold">
                                        <?= number_format((float) ($item['total_frais_transfert'] ?? 0), 0, ',', ' ') ?> Ar
                                    </td>
                                    <td class="py-3 text-end fw-bold text-primary fs-6">
                                        <?= number_format((float) ($item['total_frais'] ?? 0), 0, ',', ' ') ?> Ar
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                                            <?= (int) ($item['nombre_transactions'] ?? 0) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-black-50"></i>
                                    Aucune donnée disponible pour le moment.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>