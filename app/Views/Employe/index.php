<?= $this->extend('Inc/layout/main') ?>

<?= $this->section('content') ?>

<section id="page-mes-conges" style="margin-top:3rem">
    <div class="app-wrap">

        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
                <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
            </div>
            <ul class="sidebar-nav" style="margin-top:1rem">
                <ul class="sidebar-nav" style="margin-top:1rem">
                    <li><a href="<?= base_url('/employee') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
                    <li><a href="<?= base_url('/employee/create') ?>"><i class="bi bi-plus-circle"></i> Nouvelle
                            demande</a></li>
                    <li><a href="<?= base_url('/employee/demandes') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a>
                    </li>
                    <li><a href="<?= base_url('/employee/profil') ?>"><i class="bi bi-person"></i> Mon profil</a></li>
                </ul>
                <div class="sidebar-user">
                    <div class="s-user-row">
                        <div class="avatar av-green">
                            <?= strtoupper(substr(session()->get('user')['prenom'] ?? 'U', 0, 1)) . strtoupper(substr(session()->get('user')['nom'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="user-name"><?= esc(session()->get('user')['prenom'] ?? 'Utilisateur') ?>
                                <?= esc(session()->get('user')['nom'] ?? '') ?>
                            </div>
                            <div class="user-role">Employé · <?= esc(session()->get('user')['role'] ?? 'Général') ?>
                            </div>
                        </div>
                    </div>
                </div>
        </aside>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="topbar-title">Mes demandes de congé</div>
                    <div class="topbar-breadcrumb"><a href="<?= base_url('/employee') ?>">Accueil</a> <i
                            class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
                </div>
                <div class="topbar-actions">
                    <a href="<?= base_url('/employee/demandes') ?>" class="btn-forest"
                        style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
                </div>
            </div>

            <div class="content">
                <div class="data-card">
                    <div class="data-card-head">
                        <h3>Toutes mes demandes</h3>
                        <div style="display:flex;gap:6px">
                            <select class="f-select" id="filtreStatut"
                                style="font-size:.8rem;padding:6px 10px;width:auto">
                                <option value="tous">Tous les statuts</option>
                                <option value="en_attente">En attente</option>
                                <option value="approuve">Approuvée</option>
                                <option value="refuse">Refusée</option>
                                <option value="annule">Annulée</option>
                            </select>
                        </div>
                    </div>
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Durée</th>
                                <th>Statut</th>
                                <th>Commentaire RH</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="table-demandes">
                            <?php if (isset($demandes) && !empty($demandes)): ?>
                                <?php foreach ($demandes as $demande): ?>
                                    <tr class="demande-row" data-statut="<?= $demande['statut'] ?>">
                                        <td>
                                            <?php
                                            $typeClass = 't-annuel';
                                            $typeNom = $demande['type_nom'] ?? 'Annuel';
                                            switch (strtolower($typeNom)) {
                                                case 'maladie':
                                                    $typeClass = 't-maladie';
                                                    break;
                                                case 'sans solde':
                                                    $typeClass = 't-sans-solde';
                                                    break;
                                                default:
                                                    $typeClass = 't-annuel';
                                            }
                                            ?>
                                            <span class="type-badge <?= $typeClass ?>"><?= esc($typeNom) ?></span>
                                        </td>
                                        <td class="td-muted"><?= date('d M Y', strtotime($demande['date_debut'])) ?></td>
                                        <td class="td-muted"><?= date('d M Y', strtotime($demande['date_fin'])) ?></td>
                                        <td class="td-mono"><?= $demande['nb_jours'] ?> j</td>
                                        <td>
                                            <?php
                                            $statutClass = 's-attente';
                                            $statutText = 'en attente';
                                            switch ($demande['statut']) {
                                                case 'approuve':
                                                    $statutClass = 's-approuvee';
                                                    $statutText = 'approuvée';
                                                    break;
                                                case 'refuse':
                                                    $statutClass = 's-refusee';
                                                    $statutText = 'refusée';
                                                    break;
                                                case 'annule':
                                                    $statutClass = 's-annulee';
                                                    $statutText = 'annulée';
                                                    break;
                                                default:
                                                    $statutClass = 's-attente';
                                                    $statutText = 'en attente';
                                            }
                                            ?>
                                            <span class="statut <?= $statutClass ?>"><?= $statutText ?></span>
                                        </td>
                                        <td
                                            style="font-size:.78rem;color:<?= $demande['commentaire'] ? 'var(--success)' : 'var(--muted)' ?>">
                                            <?php if (!empty($demande['commentaire'])): ?>
                                                <i class="bi bi-chat-dots"></i> <?= esc($demande['commentaire']) ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($demande['statut'] === 'en_attente'): ?>
                                                <button class="btn-sm btn-cancel" onclick="annulerDemande(<?= $demande['id'] ?>)">
                                                    <i class="bi bi-x"></i> Annuler
                                                </button>
                                            <?php else: ?>
                                                <span class="td-muted" style="font-size:.75rem">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px;">
                                        <i class="bi bi-calendar-x" style="font-size: 48px; color: var(--muted);"></i>
                                        <p style="margin-top: 10px;">Aucune demande de congé trouvée</p>
                                        <a href="<?= base_url('/employee/demandes') ?>" class="btn-forest"
                                            style="display: inline-block; margin-top: 10px;">
                                            Créer ma première demande
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
        </div>

    </div>
</section>

<script>
    // Filtre par statut
    document.getElementById('filtreStatut')?.addEventListener('change', function () {
        const filtre = this.value;
        const rows = document.querySelectorAll('.demande-row');

        rows.forEach(row => {
            if (filtre === 'tous') {
                row.style.display = '';
            } else {
                const statut = row.getAttribute('data-statut');
                if (statut === filtre) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });

    // Annuler une demande
    function annulerDemande(id) {
        if (confirm('Êtes-vous sûr de vouloir annuler cette demande ?')) {
            fetch('<?= base_url('/employee/annuler') ?>/' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Erreur lors de l\'annulation');
                    }
                })
                .catch(error => {
                    alert('Erreur technique, veuillez réessayer');
                });
        }
    }
</script>

<?= $this->endSection() ?>