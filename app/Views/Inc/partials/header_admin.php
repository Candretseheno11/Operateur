<!-- HEADER (TOP NAVBAR) -->
<header class="top-navbar">
    <div class="d-flex align-items-center">
        <!-- Toggle Mobile -->
        <button class="btn d-lg-none me-3" id="btnToggle">
            <i class="bi bi-list fs-3"></i>
        </button>
        <h5 class="mb-0 fw-bold d-none d-md-block text-dark">Panneau d'administration</h5>
    </div>

    <div class="d-flex align-items-center gap-3">




        <!-- Profil Admin -->
        <div class="dropdown">
            <a class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" href="#" role="button"
                data-bs-toggle="dropdown">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-bold text-dark small" style="line-height: 1;">
                        Administrateur
                    </div>
                    <small class="text-muted" style="font-size: 0.7rem;">Super Admin</small>
                </div>
                <img src="/asset/images/avatar_homme.jpg" class="user-profile-img">
            </a>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 p-2">
                <li><a class="dropdown-item rounded-2 py-2 text-danger" href="/logout"><i
                            class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
            </ul>
        </div>
    </div>
</header>