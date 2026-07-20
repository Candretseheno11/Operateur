<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <div class="row">
        <div class="col-md-8 mx-auto">

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
                    <h5 class="fw-bold m-0 text-dark">
                        <i class="bi bi-pencil-square me-2 text-warning"></i>Modifier le barème
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Ajustez les seuils de montants ou les frais applicables.</p>
                </div>

                <div class="card-body p-4">
                    <!-- Action pointant vers la route POST : /operateur/bareme/update/ID -->
                    <form action="<?= base_url('/operateur/bareme/update/' . $bareme['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Type d'opération -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type d'opération</label>
                            <select name="id_type_operation" class="form-select" required>
                                <option value="1" <?= $bareme['id_type_operation'] == 1 ? 'selected' : '' ?>>Transfert
                                </option>
                                <option value="2" <?= $bareme['id_type_operation'] == 2 ? 'selected' : '' ?>>Retrait
                                </option>
                            </select>
                        </div>

                        <!-- Tranches de montant -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Montant minimum (Ar)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="montant_min" class="form-control"
                                        value="<?= $bareme['montant_min'] ?>" required>
                                    <span class="input-group-text">Ar</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Montant maximum (Ar)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="montant_max" class="form-control"
                                        value="<?= $bareme['montant_max'] ?>" required>
                                    <span class="input-group-text">Ar</span>
                                </div>
                            </div>
                        </div>

                        <!-- Frais -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Frais de commission (Ar)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="frais" class="form-control"
                                    value="<?= $bareme['frais'] ?>" required>
                                <span class="input-group-text">Ar</span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('/operateur/bareme') ?>"
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