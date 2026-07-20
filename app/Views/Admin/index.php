<?= $this->extend('Inc/layout/main') ?>

<?= $this->section('content') ?>

<section id="page-dashboard-admin" style="margin-top:3rem">
    <div class="app-wrap">

        <aside class="sidebar">
            <div class="sidebar-section">Gestion</div>
            <ul class="sidebar-nav">
                <li><a href="<?= site_url('admin/dashboard') ?>" class="active"><i class="bi bi-speedometer2"></i> Vue
                        d'ensemble</a></li>
                <li>
                    <a href="<?= site_url('admin/demandes') ?>">
                        <i class="bi bi-inbox"></i> Toutes les demandes
                        <?php if($demandesEnAttente > 0): ?>
                        <span class="nav-badge alert"><?= $demandesEnAttente ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="<?= site_url('admin/employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
                <li><a href="<?= site_url('admin/departements') ?>"><i class="bi bi-building"></i> Départements</a></li>
                <li><a href="<?= site_url('admin/types-conge') ?>"><i class="bi bi-tags"></i> Types de congé</a></li>
                <li><a href="<?= site_url('admin/soldes') ?>"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
            </ul>
        </aside>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="topbar-title">Vue d'ensemble</div>
                    <div class="topbar-breadcrumb">Administration</div>
                </div>
                <div class="topbar-actions">
                    <a href="<?= site_url('admin/employes/nouveau') ?>" class="btn-forest"
                        style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un
                        employé</a>
                </div>
            </div>

            <div class="content">

                <div class="metrics">
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-icon mi-forest"><i class="bi bi-people"></i></div>
                        </div>
                        <div class="metric-val"><?= $totalEmployes ?></div>
                        <div class="metric-label">Employés actifs</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div>
                        </div>
                        <div class="metric-val"><?= $demandesEnAttente ?></div>
                        <div class="metric-label">Demandes en attente</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div>
                        </div>
                        <div class="metric-val"><?= $approuveesMois ?></div>
                        <div class="metric-label">Approuvées ce mois</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-icon mi-blue"><i class="bi bi-building"></i></div>
                        </div>
                        <div class="metric-val"><?= $totalDeps ?></div>
                        <div class="metric-label">Départements</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div>
                        </div>
                        <div class="metric-val"><?= count($absentsAujourdhui) ?></div>
                        <div class="metric-label">Absents aujourd'hui</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

                    <div class="data-card" style="margin:0">
                        <div class="data-card-head">
                            <h3>Demandes récentes</h3>
                            <a href="<?= site_url('admin/demandes') ?>"
                                style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a>
                        </div>
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Employé</th>
                                    <th>Type</th>
                                    <th>Durée</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recentesDemandes as $req): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:7px">
                                            <div class="avatar av-green"
                                                style="width:28px;height:28px;font-size:.62rem">
                                                <?= substr($req['prenom'], 0, 1) . substr($req['nom'], 0, 1) ?>
                                            </div>
                                            <span class="td-name"
                                                style="font-size:.84rem"><?= $req['prenom'] . ' ' . $req['nom'] ?></span>
                                        </div>
                                    </td>
                                    <td><span class="type-badge"><?= $req['type_conge_id'] ?></span></td>
                                    <td class="td-mono"><?= $req['nb_jours'] ?> j</td>
                                    <td><span class="statut s-<?= $req['statut'] ?>"><?= $req['statut'] ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:1rem">
                        <div class="data-card" style="margin:0">
                            <div class="data-card-head">
                                <h3>Absents aujourd'hui</h3>
                            </div>
                            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
                                <?php if(empty($absentsAujourdhui)): ?>
                                <div style="font-size:.8rem;color:var(--muted)">Tout le monde est présent.</div>
                                <?php else: ?>
                                <?php foreach($absentsAujourdhui as $abs): ?>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div class="avatar av-amber" style="width:30px;height:30px;font-size:.65rem">
                                        <?= substr($abs['prenom'], 0, 1) . substr($abs['nom'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div style="font-size:.83rem;font-weight:500;color:var(--ink)">
                                            <?= $abs['prenom'] ?></div>
                                        <div style="font-size:.72rem;color:var(--muted)"><?= $abs['type_nom'] ?> ·
                                            retour <?= date('d/m', strtotime($abs['date_fin'])) ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($soldesCritiques > 0): ?>
                        <div class="flash flash-warn" style="margin:0">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span style="font-size:.8rem"><?= $soldesCritiques ?> employés ont un solde critique. <a
                                    href="<?= site_url('admin/soldes') ?>"
                                    style="color:var(--warn);font-weight:500">Voir →</a></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>