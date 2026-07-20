<?= $this->extend('Inc/layout/main') ?>

<?= $this->section('content') ?>

<section id="page-liste-rh" style="margin-top:3rem">
    <div class="app-wrap">

        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
                <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
            </div>
            <div class="sidebar-section">Menu</div>
            <ul class="sidebar-nav">
                <li><a href="<?= base_url('/rh') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
                <li>
                    <a href="<?= base_url('/rh/demandes') ?>" class="active">
                        <i class="bi bi-inbox"></i> Demandes à traiter
                        <span class="nav-badge alert"><?= count($demandesEnAttente) ?></span>
                    </a>
                </li>
                <li><a href="<?= base_url('/rh/historique') ?>"><i class="bi bi-archive"></i> Historique</a></li>
                <li><a href="<?= base_url('/rh/employes') ?>"><i class="bi bi-people"></i> Soldes employés</a></li>
            </ul>
            <div class="sidebar-user">
                <div class="s-user-row">
                    <div class="avatar av-blue">
                        <?= strtoupper(substr(session()->get('user')['prenom'] ?? 'U', 0, 1)) . strtoupper(substr(session()->get('user')['nom'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <div class="user-name"><?= esc(session()->get('user')['prenom'] ?? '') ?>
                            <?= esc(session()->get('user')['nom'] ?? 'Utilisateur') ?>
                        </div>
                        <div class="user-role">Responsable RH</div>
                    </div>
                    <a href="<?= base_url('/logout') ?>"
                        style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </div>
        </aside>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="topbar-title">Demandes à traiter</div>
                    <div class="topbar-breadcrumb"><a href="<?= base_url('/rh') ?>">Accueil</a> <i
                            class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
                </div>
                <div class="topbar-actions">
                    <span
                        style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
                        <i class="bi bi-hourglass-split"></i> <?= count($demandesEnAttente) ?> en attente
                    </span>
                </div>
            </div>

            <div class="content">

                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="flash flash-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="flash flash-danger">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <!-- Filtre -->
                <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
                    <button onclick="filterTable('tous')" id="btn-tous"
                        style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--forest);background:var(--forest);color:var(--white);cursor:pointer">Tous
                        (<?= count($demandes) ?>)</button>
                    <button onclick="filterTable('en_attente')" id="btn-attente"
                        style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer">En
                        attente (<?= $nbEnAttente ?>)

                    </button>
                    <button onclick="filterTable('approuve')" id="btn-approuve"
                        style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer">Approuvées
                        (<?= $nbApprouve ?>)</button>
                    <button onclick="filterTable('refuse')" id="btn-refuse"
                        style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer">Refusées
                        (<?= $nbRefuse ?>)</button>
                </div>

                <div class="data-card">
                    <div class="data-card-head">
                        <h3>Toutes les demandes</h3>
                    </div>
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Employé</th>
                                <th>Type</th>
                                <th>Période</th>
                                <th>Durée</th>
                                <th>Solde dispo</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="table-demandes">
                            <?php foreach ($demandes as $demande): ?>
                                <?php
                                $statutClass = '';
                                $statutText = '';
                                switch ($demande['statut']) {
                                    case 'en_attente':
                                        $statutClass = 's-attente';
                                        $statutText = 'en attente';
                                        break;
                                    case 'approuve':
                                        $statutClass = 's-approuvee';
                                        $statutText = 'approuvée';
                                        break;
                                    case 'refuse':
                                        $statutClass = 's-refusee';
                                        $statutText = 'refusée';
                                        break;
                                }
                                ?>
                                <tr class="demande-row" data-statut="<?= $demande['statut'] ?>">
                                    <td>
                                        <div class="profile-row">
                                            <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem">
                                                <?= strtoupper(substr($demande['prenom'] ?? 'U', 0, 1)) . strtoupper(substr($demande['nom'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div class="profile-info">
                                                <div class="pname"><?= esc($demande['prenom'] ?? '') ?>
                                                    <?= esc($demande['nom'] ?? '') ?>
                                                </div>
                                                <div class="pdept"><?= esc($demande['type_nom'] ?? '') ?> ·
                                                    <?= date('d/m', strtotime($demande['date_debut'])) ?> →
                                                    <?= date('d/m', strtotime($demande['date_fin'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="type-badge"><?= esc($demande['type_nom'] ?? '') ?></span></td>
                                    <td class="td-muted" style="font-size:.8rem">
                                        <?= date('d/m/Y', strtotime($demande['date_debut'])) ?> –
                                        <?= date('d/m/Y', strtotime($demande['date_fin'])) ?>
                                    </td>
                                    <td class="td-mono"><?= $demande['nb_jours'] ?> j</td>
                                    <td>
                                        <?php if (isset($demande['solde_actuel'])): ?>
                                            <span
                                                style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--success);font-weight:500"><?= number_format($demande['solde_actuel'], 1) ?>
                                                j</span>
                                            <?php if ($demande['solde_actuel'] < $demande['nb_jours']): ?>
                                                <span style="font-size:.72rem;color:var(--danger)"> ⚠ insuffisant</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span
                                                style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--muted)">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="statut <?= $statutClass ?>"><?= $statutText ?></span></td>
                                    <td>
                                        <?php if ($demande['statut'] == 'en_attente'): ?>
                                            <div class="action-btns">
                                                <button class="btn-sm btn-approve" onclick="approuver(<?= $demande['id'] ?>)">
                                                    <i class="bi bi-check-lg"></i> Approuver
                                                </button>
                                                <button class="btn-sm btn-refuse" onclick="refuser(<?= $demande['id'] ?>)">
                                                    <i class="bi bi-x-lg"></i> Refuser
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="td-muted" style="font-size:.75rem">Traité</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
        </div>

    </div>
</section>

<script>
    // Filtre les demandes par statut
    function filterTable(statut) {
        const rows = document.querySelectorAll('.demande-row');
        rows.forEach(row => {
            if (statut === 'tous') {
                row.style.display = '';
            } else {
                row.style.display = row.getAttribute('data-statut') === statut ? '' : 'none';
            }
        });

        // Changer le style des boutons
        const buttons = ['btn-tous', 'btn-attente', 'btn-approuve', 'btn-refuse'];
        buttons.forEach(id => {
            const btn = document.getElementById(id);
            if (btn) {
                if ((statut === 'tous' && id === 'btn-tous') ||
                    (statut === 'en_attente' && id === 'btn-attente') ||
                    (statut === 'approuve' && id === 'btn-approuve') ||
                    (statut === 'refuse' && id === 'btn-refuse')) {
                    btn.style.background = 'var(--forest)';
                    btn.style.color = 'var(--white)';
                    btn.style.borderColor = 'var(--forest)';
                } else {
                    btn.style.background = 'var(--white)';
                    btn.style.color = 'var(--muted)';
                    btn.style.borderColor = 'var(--border)';
                }
            }
        });
    }

    // Approuver une demande
    function approuver(id) {
        if (confirm('Êtes-vous sûr de vouloir approuver cette demande ?')) {
            fetch('<?= base_url('/rh/approuver') ?>/' + id, {
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
                        alert(data.message || 'Erreur lors de l\'approbation');
                    }
                })
                .catch(error => {
                    alert('Erreur technique, veuillez réessayer');
                });
        }
    }

    // Refuser une demande
    function refuser(id) {
        const motif = prompt('Motif du refus (optionnel) :');
        fetch('<?= base_url('/rh/refuser') ?>/' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: 'motif_refus=' + encodeURIComponent(motif || 'Refusé par RH')
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Erreur lors du refus');
                }
            })
            .catch(error => {
                alert('Erreur technique, veuillez réessayer');
            });
    }
</script>

<?= $this->endSection() ?>