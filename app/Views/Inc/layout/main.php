<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Regime APP' ?></title>
    <link rel="stylesheet" href="/asset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/asset/icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/asset/css/header.css">
    <link rel="stylesheet" href="/asset/css/imc.css">
    <link rel="stylesheet" href="/asset/css/footer.css">
    <link rel="stylesheet" href="/asset/css/main.css">
    <link rel="stylesheet" href="/asset/css/profile.css">

</head>

<body>

    <!-- Header -->

    <?= $this->include('Inc/partials/header') ?>
    <!-- Contenu -->
    <main class="container mt-4">
        <?= $this->renderSection('content') ?>
    </main>


    <!-- Footer -->
    <?= $this->include('Inc/partials/footer') ?>
</body>
<script src="/asset/bootstrap/js/bootstrap.min.js"></script>
<script src="/asset/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/asset/js/modify.js"></script>

<?= $this->renderSection('scripts') ?>

</html>