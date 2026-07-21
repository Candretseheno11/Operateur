<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">

    <!-- Notifications Flash -->
    <?php if (session('success')): ?>
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><?= session('success') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div><?= session('error') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- En-tête -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-percent me-2 text-primary"></i>Gestion des Promotions
            </h4>
            <p class="text-muted small mb-0">Configurez les frais de promotion pour les transferts vers d'autres opérateurs.</p>
        </div>
        <div>
            <a href="<?= base_url('/operateur/promotions/add') ?>"
                class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-2 fs-6"></i>
                <span class="fw-medium">Nouvelle Promotion</span>
            </a>
        </div>
    </div>

    <!-- TABLEAU DES PROMOTIONS -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom border-0">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small text-muted">#</th>
                            <th class="py-3 text-uppercase small text-muted">Préfixe</th>
                            <th class="py-3 text-uppercase small text-muted">Statut</th>
                            <th class="py-3 text-uppercase small text-muted">Pourcentage Extra</th>
                            <th class="pe-4 py-3 text-uppercase small text-muted text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (!empty($promotions)): ?>
                        <?php foreach ($promotions as $index => $promo): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border-primary px-3 py-2 rounded-pill fw-semibold fs-7">
                                    <i class="bi bi-hash me-1"></i> <?= $promo['prefixe'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $promo['actif'] ? 'bg-success bg-opacity-10 text-success border-success' : 'bg-secondary bg-opacity-10 text-secondary border-secondary' ?> px-3 py-2 rounded-pill fw-semibold fs-7">
                                    <?= $promo['actif'] ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-warning fs-6">
                                    <?= number_format($promo['pourcentage_extra'], 2, ',', ' ') ?> %
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <!-- Modifier -->
                                <a href="<?= base_url('/operateur/promotions/edit/' . $promo['id']) ?>"
                                    class="btn btn-outline-warning btn-sm rounded-circle p-2 me-1 d-inline-flex align-items-center justify-content-center"
                                    style="width: 34px; height: 34px;" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <!-- Supprimer -->
                                <a href="<?= base_url('/operateur/promotions/delete/' . $promo['id']) ?>"
                                    class="btn btn-outline-danger btn-sm rounded-circle p-2 d-inline-flex align-items-center justify-content-center"
                                    style="width: 34px; height: 34px;"
                                    onclick="return confirm('Voulez-vous vraiment supprimer cette promotion ?');"
                                    title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                Aucune promotion n'a été configurée pour le moment.
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
