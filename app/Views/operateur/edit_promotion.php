<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <div class="row">
        <div class="col-md-8 mx-auto">

            <!-- Messages d'erreur -->
            <?php if (session('error')): ?>
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= session('error') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold m-0">
                        <i class="bi bi-pencil-square me-2 text-warning"></i>Modifier la Promotion
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Ajustez le pourcentage de promotion pour le préfixe <?= $promotion['prefixe'] ?>.</p>
                </div>

                <div class="card-body p-4">
                    <form action="<?= base_url('/operateur/promotions/update/' . $promotion['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Préfixe (lecture seule) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Préfixe de l'opérateur</label>
                            <input type="text" class="form-control" value="<?= $promotion['prefixe'] ?>" readonly>
                        </div>

                        <!-- Pourcentage extra -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pourcentage de promotion (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="pourcentage_extra" 
                                    class="form-control" value="<?= $promotion['pourcentage_extra'] ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">
                                <?php if ($promotion['pourcentage_extra'] > 0): ?>
                                    <span class="text-success"><i class="bi bi-info-circle me-1"></i>Promotion active</span>
                                <?php else: ?>
                                    <span class="text-muted"><i class="bi bi-info-circle me-1"></i>Promotion désactivée (0%)</span>
                                <?php endif; ?>
                                - Mettez 0 pour désactiver la promotion.
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('/operateur/promotions') ?>"
                                class="btn btn-light px-4 rounded-3">Annuler</a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-4 rounded-3 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Mettre à jour
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
