<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">

    <!-- Messages Flash de Notification -->
    <?php if (session('success')): ?>
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div><?= session('success') ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Tableau de bord Opérateur</h4>
            <p class="text-muted small mb-0">Bienvenue dans votre espace de gestion des transactions.</p>
        </div>
    </div>

    <!-- SECTION 1 : STATISTIQUES FINANCIÈRES -->
    <div class="row g-4 mb-4">
        <!-- Total Clients -->
        <div class="col-md-3">
            <div
                class="adm-stat-card adm-border-primary h-100 p-3 bg-white rounded-4 shadow-sm border-start border-4 border-primary">
                <i class="bi bi-people adm-stat-icon-bg icon-users text-primary fs-3 mb-2 d-block"></i>
                <div class="adm-stat-label text-muted small text-uppercase fw-bold">Clients Inscrits</div>
                <div class="adm-stat-value h3 fw-bold mb-0"><?= $totalClient ?? 0 ?></div>
                <div class="mt-2 small text-muted">
                    Total des comptes actifs
                </div>
            </div>
        </div>

        <!-- Gains Transfert -->
        <div class="col-md-3">
            <div
                class="adm-stat-card adm-border-info h-100 p-3 bg-white rounded-4 shadow-sm border-start border-4 border-info">
                <i class="bi bi-arrow-left-right adm-stat-icon-bg text-info fs-3 mb-2 d-block"></i>
                <div class="adm-stat-label text-muted small text-uppercase fw-bold">Gains sur Transferts</div>
                <div class="adm-stat-value h3 fw-bold mb-0"><?= number_format(($gainTransfert ?? 0), 0, ',', ' ') ?>
                    <small class="fs-6">Ar</small>
                </div>
                <div class="mt-2 small text-muted">Commissions générées</div>
            </div>
        </div>

        <!-- Gains Retrait -->
        <div class="col-md-3">
            <div
                class="adm-stat-card adm-border-success h-100 p-3 bg-white rounded-4 shadow-sm border-start border-4 border-success">
                <i class="bi bi-cash-stack adm-stat-icon-bg text-success fs-3 mb-2 d-block"></i>
                <div class="adm-stat-label text-muted small text-uppercase fw-bold">Gains sur Retraits</div>
                <div class="adm-stat-value h3 fw-bold mb-0"><?= number_format(($gainRetrait ?? 0), 0, ',', ' ') ?>
                    <small class="fs-6">Ar</small>
                </div>
                <div class="mt-2 small text-muted">Frais de retrait perçus</div>
            </div>
        </div>

        <!-- Gains Totaux -->
        <div class="col-md-3">
            <div
                class="adm-stat-card adm-border-warning h-100 p-3 bg-white rounded-4 shadow-sm border-start border-4 border-warning">
                <i class="bi bi-wallet-fill adm-stat-icon-bg text-warning fs-3 mb-2 d-block"></i>
                <div class="adm-stat-label text-muted small text-uppercase fw-bold">Gains Totaux</div>
                <div class="adm-stat-value h3 fw-bold mb-0 text-dark">
                    <?= number_format(($gainTotal ?? 0), 0, ',', ' ') ?> <small class="fs-6">Ar</small>
                </div>
                <div class="mt-2 small text-muted">Chiffre d'affaires global</div>
            </div>
        </div>
    </div>

    <!-- SECTION 2 : GRAPHIQUE ET ÉTAT DES TRANSACTIONS -->
    <div class="row g-4">

        <!-- Graphique d'évolution des transactions -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold m-0"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Évolution des
                            Opérations (7 derniers jours)</h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px; position: relative;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Récapitulatif des Opérations -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold m-0"><i class="bi bi-list-check me-2 text-primary"></i>Statut des Opérations</h6>
                </div>
                <div class="card-body p-4">

                    <!-- Vous pouvez lier ces chiffres à votre contrôleur si vous souhaitez afficher l'état des demandes -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                    style="width: 30px; height: 30px;">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div class="fw-bold small">Validées</div>
                            </div>
                            <div class="fw-bold text-success"><?= $stats['totalApproved'] ?? 0 ?></div>
                        </div>

                        <div
                            class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-2 bg-light">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                    style="width: 30px; height: 30px;">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div class="fw-bold small">En attente</div>
                            </div>
                            <div class="fw-bold text-warning"><?= $stats['totalPending'] ?? 0 ?></div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                    style="width: 30px; height: 30px;">
                                    <i class="bi bi-x-lg"></i>
                                </div>
                                <div class="fw-bold small">Annulées/Échouées</div>
                            </div>
                            <div class="fw-bold text-danger"><?= $stats['totalRejected'] ?? 0 ?></div>
                        </div>
                    </div>

                    <a href="<?= base_url('operateur/transactions') ?>"
                        class="btn btn-primary w-100 py-2 rounded-3 shadow-sm">
                        Voir l'historique complet <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS : GRAPHIQUE CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('performanceChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        const chartLabels = <?= json_encode(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']) ?>;

        // Volumes de transactions (passez ces données via le contrôleur dans $stats['chartData'])
        const chartDataValues = <?= json_encode($stats['chartData'] ?? [12, 19, 15, 25, 22, 30, 28]) ?>;

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Volume de Transactions',
                    data: chartDataValues,
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>