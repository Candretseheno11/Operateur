<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Tableau de bord Utilisateur' ?></title>
    <link rel="stylesheet" href="/asset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/asset/icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/asset/css/admin_layout.css">
    <link rel="stylesheet" href="/asset/css/admin.css">

</head>

<body>
    <aside class="sidebar" id="sidebar">
        <a href="/operateur" class="sidebar-brand">
            <i class="bi bi-shield-check-fill text-primary"></i>
            <span>Operateur Mobile Money <br><span class="text-primary">Admin</span></span>
        </a>

        <div class="nav-menu">
            <div class="menu-label">Principal</div>

            <a href="/operateur" class="nav-link-custom <?= current_url(true)->getSegment(2) == '' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i>
                <span>Tableau de bord</span>
            </a>

            <a href="/operateur/comptes"
                class="nav-link-custom <?= current_url(true)->getSegment(2) == 'comptes' ? 'active' : '' ?>">
                <i class="bi bi-"></i>
                <span>Comptes</span>
            </a>

            <a href="/operateur/transactions"
                class="nav-link-custom <?= current_url(true)->getSegment(2) == 'transactions' ? 'active' : '' ?>">
                <i class="bi bi-money"></i>
                <span>Transaction</span>
            </a>


            <a href="/operateur/gold/pending"
                class="nav-link-custom <?= current_url(true)->getSegment(2) == 'gold' ? 'active' : '' ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>Golden User</span>
            </a>
            <div class="menu-label mt-4">CRUD</div>

            <a href="/operateur/bareme"
                class="nav-link-custom <?= current_url(true)->getSegment(2) == 'wallet-requests' ? 'active' : '' ?>">
                <i class="bi bi-wallet2"></i>
                <span>Baremes</span>
                <?php if (isset($pending_count) && $pending_count > 0): ?>
                    <span class="badge bg-danger ms-auto rounded-pill"><?= $pending_count ?></span>
                <?php endif; ?>
            </a>

            <a href=" /operateur/code"
                class="nav-link-custom <?= current_url(true)->getSegment(2) == 'code' ? 'active' : '' ?>">
                <i class="bi bi-credit-card-2-front"></i>
                <span>Gestion Code</span>
                <?php if (isset($pending_count) && $pending_count > 0): ?>
                    <span class="badge bg-danger ms-auto rounded-pill"><?= $pending_count ?></span>
                <?php endif; ?>
            </a>
            <div class="menu-label mt-4">Système</div>

            <a href="/logout" class="nav-link-custom text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <div class="main-wrapper">
        <!-- Header -->
        <?= $this->include('Inc/partials/header_admin') ?>

        <!-- Contenu -->
        <main class="container mt-4">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

</body>
<script src="/asset/bootstrap/js/bootstrap.min.js"></script>
<script src="/asset/bootstrap/js/bootstrap.bundle.min.js"></script>

</html>