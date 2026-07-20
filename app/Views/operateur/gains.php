<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Situation des gains via les différents frais</h4>
            <p class="text-muted small mb-0">Suivi des commissions de retrait et de transfert, avec une séparation entre
                opérateur principal et autres opérateurs.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-muted small text-uppercase fw-bold">Gains retrait</div>
                    <div class="h3 fw-bold text-success mt-2"><?= number_format($gainRetrait ?? 0, 0, ',', ' ') ?>
                        <small>Ar</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-muted small text-uppercase fw-bold">Transferts vers l’opérateur principal</div>
                    <div class="h3 fw-bold text-primary mt-2">
                        <?= number_format($gainTransfertOperateur ?? 0, 0, ',', ' ') ?> <small>Ar</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-muted small text-uppercase fw-bold">Transferts vers les autres opérateurs</div>
                    <div class="h3 fw-bold text-warning mt-2">
                        <?= number_format($gainTransfertAutresOperateurs ?? 0, 0, ',', ' ') ?> <small>Ar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h6 class="fw-bold m-0"><i class="bi bi-wallet2 me-2 text-primary"></i>Montants à envoyer à chaque opérateur
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Opérateur</th>
                            <th>Type</th>
                            <th>Montant total transféré</th>
                            <th>Commission collectée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($gainBreakdown)): ?>
                            <?php foreach ($gainBreakdown as $item): ?>
                                <tr>
                                    <td><span class="font-monospace fw-bold"><?= esc($item['prefixe'] ?? 'N/A') ?></span></td>
                                    <td>
                                        <?php if (!empty($item['est_autre_operateur'])): ?>
                                            <span class="badge bg-warning text-dark">Autre opérateur</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Opérateur principal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format((float) ($item['total_montant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                                    <td><?= number_format((float) ($item['total_frais'] ?? 0), 0, ',', ' ') ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Aucune donnée de transfert disponible
                                    pour le moment.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>