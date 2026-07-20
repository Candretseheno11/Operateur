<?= $this->extend('Inc/layout/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Soldes des employés</h3>
        <span class="text-muted">
            Année <?= date('Y') ?>
        </span>
    </div>

    <?php if (empty($employes)): ?>
        <div class="alert alert-info">
            Aucun employé trouvé.
        </div>
    <?php else: ?>

        <?php foreach ($employes as $e): ?>

            <div class="card mb-3 shadow-sm">

                <!-- HEADER EMPLOYÉ -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>
                            <?= esc($e['prenom']) ?>         <?= esc($e['nom']) ?>
                        </strong>
                        <small class="text-muted">
                            (<?= esc($e['matricule'] ?? 'N/A') ?>)
                        </small>
                    </div>
                </div>

                <!-- BODY SOLDES -->
                <div class="card-body">

                    <?php if (empty($e['soldes'])): ?>
                        <p class="text-muted">Aucun solde disponible</p>
                    <?php else: ?>

                        <div class="row">

                            <?php foreach ($e['soldes'] as $s): ?>
                                <div class="col-md-4 mb-2">

                                    <div class="border rounded p-2 text-center">

                                        <div class="fw-bold">
                                            <?= esc($s['type_nom']) ?>
                                        </div>

                                        <div style="font-size: 1.2rem; color: #198754;">
                                            <?= esc($s['solde']) ?> jours
                                        </div>

                                    </div>

                                </div>
                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>