<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />

    <title><?= $title ?? 'TechMada RH' ?></title>


    <link rel="stylesheet" href="/asset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/asset/icons/bootstrap-icons.css">
    <style>

    </style>

</head>

<body>
    <?= $this->include('Inc/partials/header_admin') ?>
    <!-- Contenu -->
    <main class="container mt-4">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="/asset/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>