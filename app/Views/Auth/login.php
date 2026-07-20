<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>

    <link rel="stylesheet" href="/asset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/asset/icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/asset/css/main.css">
    <link rel="stylesheet" href="/asset/css/login.css">
</head>

<body>

    <section id="page-login">
        <div class="auth-page geo-bg">
            <div class="auth-split">

                <!-- Panneau gauche -->
                <div class="auth-left">
                    <div>
                        <p class="auth-left-brand">TechMada RH<span>Gestion des congés</span></p>
                        <p class="auth-left-text" style="margin-top:2rem">
                            <strong>Bienvenue sur votre espace RH.</strong>
                            Gérez vos demandes de congés, consultez votre solde et suivez l'état de vos demandes en
                            temps réel.
                        </p>
                    </div>
                    <div class="auth-roles">
                        <div
                            style="font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.25);margin-bottom:4px">
                            Comptes de démonstration</div>
                        <div class="role-pill">
                            <i class="bi bi-shield-check"></i>
                            <div>
                                <div class="role-pill-name">Administrateur</div>
                                <div class="role-pill-cred">admin@techmada.mg · admin123</div>
                            </div>
                        </div>
                        <div class="role-pill">
                            <i class="bi bi-person-check"></i>
                            <div>
                                <div class="role-pill-name">Responsable RH</div>
                                <div class="role-pill-cred">rh@techmada.mg · rh123</div>
                            </div>
                        </div>
                        <div class="role-pill">
                            <i class="bi bi-person"></i>
                            <div>
                                <div class="role-pill-name">Employé</div>
                                <div class="role-pill-cred">employe@techmada.mg · emp123</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panneau droit -->
                <div class="auth-right">
                    <p class="auth-title">Connexion</p>
                    <p class="auth-sub">Entrez vos identifiants pour accéder à votre espace.</p>

                    <?php if (session('success')): ?>
                        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session('error')): ?>
                        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= session('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="/login" method="POST">
                        <?= csrf_field() ?>
                        <div class="f-group">
                            <label class="f-label">Adresse email</label>
                            <input type="email" class="f-input" placeholder="vous@techmada.mg" value="admin@conges.com"
                                id="email" name="email" />
                        </div>
                        <div class="f-group">
                            <label class="f-label">Mot de passe</label>
                            <input type="password" class="f-input" placeholder="••••••••" value="admin123" id="password"
                                name="password" />
                        </div>
                        <button type="submit" class="btn-primary" style="margin-top:.5rem">
                            Se connecter <i class="bi bi-arrow-right-short"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>
    <script src="/asset/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>