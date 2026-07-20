<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <div class="row">
        <div class="col-md-8 mx-auto">

            <!-- Messages d'erreur (si barème existant ou problème de validation) -->
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
                        <i class="bi bi-plus-circle me-2 text-primary"></i>Ajouter un barème de frais
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Définissez les tranches de montants et les frais applicables
                        par type d'opération.</p>
                </div>

                <div class="card-body p-4">
                    <form action="<?= base_url('/operateur/bareme/add') ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Type d'opération -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type d'opération</label>
                            <select name="id_type_operation" class="form-select" required>
                                <option value="">Sélectionner une opération</option>
                                <?php if (isset($typesOperations)): ?>
                                    <?php foreach ($typesOperations as $type): ?>
                                        <option value="<?= $type['id'] ?>"><?= esc($type['libelle']) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Options par défaut si la liste n'est pas transmise dynamiquement -->
                                    <option value="1">Transfert</option>
                                    <option value="2">Retrait</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Tranche de montant -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Montant minimum (Ar)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="montant_min" class="form-control"
                                        placeholder="ex: 1000" required>
                                    <span class="input-group-text">Ar</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Montant maximum (Ar)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="montant_max" class="form-control"
                                        placeholder="ex: 50000" required>
                                    <span class="input-group-text">Ar</span>
                                </div>
                            </div>
                        </div>

                        <!-- Frais applicables -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Frais de commission (Ar)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="frais" class="form-control"
                                    placeholder="ex: 500" required>
                                <span class="input-group-text">Ar</span>
                            </div>
                            <div class="form-text">Ce montant fixe sera prélevé pour toute transaction dans cette
                                tranche.</div>
                        </div>

                        <hr class="my-4">

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('/operateur/bareme') ?>"
                                class="btn btn-light px-4 rounded-3">Annuler</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Enregistrer le barème
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>