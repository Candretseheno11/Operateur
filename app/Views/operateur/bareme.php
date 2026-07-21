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
                <i class="bi bi-sliders me-2 text-primary"></i>Barèmes de Frais
            </h4>
            <p class="text-muted small mb-0">Consultez et gérez les commissions appliquées selon les tranches de
                montant.</p>
        </div>
        <div>
            <a href="<?= base_url('/operateur/bareme/add') ?>"
                class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-2 fs-6"></i>
                <span class="fw-medium">Nouveau Barème</span>
            </a>
        </div>
    </div>

    <!-- CARTE DE FILTRES -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="row g-3 align-items-end">

                <!-- Recherche rapide -->
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Recherche rapide</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control bg-light border-0"
                            placeholder="Rechercher...">
                    </div>
                </div>

                <!-- Filtre par Type d'opération -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-uppercase text-muted">Type d'opération</label>
                    <select id="filterType" class="form-select bg-light border-0">
                        <option value="all">Tous les types</option>
                        <?php foreach ($baremes as $b): ?>
                            <?php if (!isset($typeLibelles[$b['id_type_operation']])): ?>
                                <?php $typeLibelles[$b['id_type_operation']] = $b['type_libelle']; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (isset($typeLibelles)): ?>
                            <?php foreach ($typeLibelles as $id => $libelle): ?>
                                <option value="<?= $id ?>"><?= $libelle ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Filtre par Montant -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-uppercase text-muted">Montant cible (Ar)</label>
                    <input type="number" id="filterMontant" class="form-control bg-light border-0"
                        placeholder="ex: 15000">
                </div>

                <!-- Réinitialiser -->
                <div class="col-md-2">
                    <button type="button" id="resetFilters" class="btn btn-light w-100 fw-medium">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Effacer
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- TABLEAU DES BARÈMES -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="baremesTable">
                    <thead class="table-light border-bottom border-0">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small text-muted">#</th>
                            <th class="py-3 text-uppercase small text-muted">Type Opération</th>
                            <th class="py-3 text-uppercase small text-muted">Tranche Minimum</th>
                            <th class="py-3 text-uppercase small text-muted">Tranche Maximum</th>
                            <th class="py-3 text-uppercase small text-muted">Frais Appliqués</th>
                            <th class="pe-4 py-3 text-uppercase small text-muted text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (!empty($baremes)): ?>
                        <?php foreach ($baremes as $index => $b): ?>
                        <?php
                                $typeName = $b['type_libelle'] ?? 'Inconnu';
                                $isTransfert = (strtolower($typeName) === 'transfert');
                                $badgeClass = $isTransfert ? 'bg-info bg-opacity-10 text-info border-info' : 'bg-success bg-opacity-10 text-success border-success';
                                $iconClass = $isTransfert ? 'bi-arrow-left-right' : 'bi-cash-stack';
                                ?>
                        <tr class="bareme-row" data-type="<?= $b['id_type_operation'] ?>"
                            data-min="<?= $b['montant_min'] ?>" data-max="<?= $b['montant_max'] ?>">

                            <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>

                            <td>
                                <span class="badge border <?= $badgeClass ?> px-3 py-2 rounded-pill fw-semibold fs-7">
                                    <i class="bi <?= $iconClass ?> me-1"></i> <?= $typeName ?>
                                </span>
                            </td>

                            <td class="fw-semibold text-dark">
                                <?= number_format($b['montant_min'], 0, ',', ' ') ?> <small
                                    class="text-muted">Ar</small>
                            </td>

                            <td class="fw-semibold text-dark">
                                <?= number_format($b['montant_max'], 0, ',', ' ') ?> <small
                                    class="text-muted">Ar</small>
                            </td>

                            <td>
                                <span class="fw-bold text-primary fs-6">
                                    <?= number_format($b['frais'], 0, ',', ' ') ?> Ar
                                </span>
                            </td>

                            <td class="pe-4 text-end">
                                <!-- Redirection Modif -->
                                <a href="<?= base_url('/operateur/bareme/edit/' . $b['id']) ?>"
                                    class="btn btn-outline-warning btn-sm rounded-circle p-2 me-1 d-inline-flex align-items-center justify-content-center"
                                    style="width: 34px; height: 34px;" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <!-- Redirection Suppr -->
                                <a href="<?= base_url('/operateur/bareme/delete/' . $b['id']) ?>"
                                    class="btn btn-outline-danger btn-sm rounded-circle p-2 d-inline-flex align-items-center justify-content-center"
                                    style="width: 34px; height: 34px;"
                                    onclick="return confirm('Voulez-vous vraiment supprimer ce barème ?');"
                                    title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                Aucun barème n'a été enregistré pour le moment.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?= $pager->links('default', 'bootstrap_full') ?>

</div>

<!-- SCRIPT JS DE FILTRAGE INSTANTANÉ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterType = document.getElementById('filterType');
    const filterMontant = document.getElementById('filterMontant');
    const resetButton = document.getElementById('resetFilters');
    const rows = document.querySelectorAll('.bareme-row');

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedType = filterType.value;
        const targetMontant = parseFloat(filterMontant.value);

        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            const min = parseFloat(row.getAttribute('data-min'));
            const max = parseFloat(row.getAttribute('data-max'));
            const textContent = row.textContent.toLowerCase();

            const matchesQuery = query === '' || textContent.includes(query);
            const matchesType = (selectedType === 'all') || (rowType === selectedType);
            let matchesMontant = isNaN(targetMontant) || (targetMontant >= min && targetMontant <= max);

            if (matchesQuery && matchesType && matchesMontant) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', applyFilters);
    filterType.addEventListener('change', applyFilters);
    filterMontant.addEventListener('input', applyFilters);

    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        filterType.value = 'all';
        filterMontant.value = '';
        applyFilters();
    });
});
</script>

<?= $this->endSection() ?>