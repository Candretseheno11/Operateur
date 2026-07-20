<div class="content-wrapper">

    <nav class="navbar navbar-expand-lg navbar-custom py-3 mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="/profile">
                TechMada RH</a>
            <div class="ms-auto d-flex align-items-center">

                <!-- Ici si c'est un Utilisateur gold donc on affiche Membre gold -->

                <img src="/asset/images/avatar_homme.jpg" class="rounded-circle me-2" width="35">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                        data-bs-toggle="dropdown">

                        <!-- Ici si c'est un homme donc ca afiiche un icone d'homme sinon du sexe inverse -->
                        <span class="fw-medium"> <?= session()->get('user')['nom'] ?> </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
                        <li><a class="dropdown-item" href="/wallet"><i class="bi bi-wallet me-2"></i>
                                Demande</a> </li>
                        <li>
                            <hr class="dropdown-divider">
                            <a class="dropdown-item" href="/viewprofile"><i class="bi bi-person me-2"></i> Profile</a>
                        <li></li>
                        <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="/logout"><i
                                    class="bi bi-box-arrow-right me-2"></i>
                                Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <?= $this->renderSection('content') ?>
    </main>