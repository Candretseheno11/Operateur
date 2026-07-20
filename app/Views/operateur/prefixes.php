<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">

    <!-- Flash Notifications -->
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
                <i class="bi bi-telephone-plus me-2 text-primary"></i>Préfixes Téléphoniques
            </h4>
            <p class="text-muted small mb-0">Gérez les préfixes autorisés pour les numéros de téléphone du réseau.</p>
        </div>
        <div>
            <a href="<?= base_url('/operateur/prefixes/add') ?>"
                class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-2 fs-6"></i>
                <span class="fw-medium">Nouveau Préfixe</span>
            </a>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom border-0">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small text-muted">#</th>
                            <th class="py-3 text-uppercase small text-muted">Préfixe</th>
                            <th class="py-3 text-uppercase small text-muted">Statut</th>
                            <th class="pe-4 py-3 text-uppercase small text-muted text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (!empty($prefixes)): ?>
                            <?php foreach ($prefixes as $index => $p): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>
                                    <td>
                                        <span class="font-monospace fw-bold fs-6 text-dark bg-light px-2 py-1 rounded">
                                            <?= esc($p['prefixe']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($p['actif'] == 1): ?>
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i> Actif
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2 rounded-pill">
                                                <i class="bi bi-slash-circle me-1"></i> Inactif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="<?= base_url('/operateur/prefixes/edit/' . $p['id']) ?>"
                                            class="btn btn-outline-warning btn-sm rounded-circle p-2 me-1 d-inline-flex align-items-center justify-content-center"
                                            style="width: 34px; height: 34px;" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <a href="<?= base_url('/operateur/prefixes/delete/' . $p['id']) ?>"
                                            class="btn btn-outline-danger btn-sm rounded-circle p-2 d-inline-flex align-items-center justify-content-center"
                                            style="width: 34px; height: 34px;"
                                            onclick="return confirm('Voulez-vous supprimer ce préfixe ?');" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    Aucun préfixe enregistré.
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