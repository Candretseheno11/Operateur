<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <div class="row">
        <div class="col-md-6 mx-auto">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold m-0 text-dark">
                        <i class="bi bi-pencil-square me-2 text-primary"></i>Modifier un préfixe
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Mettez à jour le code indicatif et la règle de commission
                        associée.</p>
                </div>

                <div class="card-body p-4">
                    <form action="<?= base_url('/operateur/prefixes/update/' . $prefixe['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Préfixe réseau</label>
                            <input type="text" name="prefixe" class="form-control font-monospace fs-5"
                                value="<?= esc($prefixe['prefixe']) ?>" maxlength="5" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Statut du préfixe</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="actif" id="actif1" value="1"
                                    <?= (int) ($prefixe['actif'] ?? 0) === 1 ? 'checked' : '' ?> required>
                                <label class="form-check-label text-success fw-medium" for="actif1">Actif</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="actif" id="actif0" value="0"
                                    <?= (int) ($prefixe['actif'] ?? 0) === 0 ? 'checked' : '' ?> required>
                                <label class="form-check-label text-secondary fw-medium" for="actif0">Inactif</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Type de préfixe</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="est_autre_operateur"
                                    id="prefixTypePrincipal" value="0" <?= (int)($prefixe['est_autre_operateur'] ?? 0) === 0 ? 'checked' : '' ?> required>
                                <label class="form-check-label fw-medium" for="prefixTypePrincipal">Opérateur
                                    principal</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="est_autre_operateur"
                                    id="prefixTypeAutre" value="1" <?= (int)($prefixe['est_autre_operateur'] ?? 0) === 1 ? 'checked' : '' ?> required>
                                <label class="form-check-label fw-medium" for="prefixTypeAutre">Autre opérateur</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pourcentage supplémentaire sur les transferts</label>
                            <input type="number" name="pourcentage_extra" class="form-control" min="0" step="0.01"
                                value="<?= esc($prefixe['pourcentage_extra'] ?? 0) ?>" required>
                            <div class="form-text">Exemple : 10 pour ajouter 10% au montant de la commission standard.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('/operateur/prefixes') ?>"
                                class="btn btn-light px-4 rounded-3">Annuler</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Enregistrer
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>