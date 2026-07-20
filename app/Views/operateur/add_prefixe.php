<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <div class="row">
        <div class="col-md-6 mx-auto">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold m-0 text-dark">
                        <i class="bi bi-plus-circle me-2 text-primary"></i>Ajouter un préfixe
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Saisissez le code indicatif du réseau (ex: 033, 034, 038).</p>
                </div>

                <div class="card-body p-4">
                    <form action="<?= base_url('/operateur/prefixes/add') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Préfixe réseau</label>
                            <input type="text" name="prefixe" class="form-control font-monospace fs-5"
                                placeholder="ex: 033" maxlength="5" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Statut du préfixe</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="actif" id="actif1" value="1" checked>
                                <label class="form-check-label text-success fw-medium" for="actif1">Actif</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="actif" id="actif0" value="0">
                                <label class="form-check-label text-secondary fw-medium" for="actif0">Inactif</label>
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