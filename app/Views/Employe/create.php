<?= $this->extend('Inc/layout/main') ?>

<?= $this->section('content') ?>

<section id="page-form-conge" style="margin-top:3rem">
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
                        <div class="user-role">Employé · <?= esc(session()->get('user')['departement_nom'] ?? 'IT') ?>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="topbar-title">Nouvelle demande de congé</div>
                    <div class="topbar-breadcrumb">
                        <a href="<?= base_url('/employee') ?>">Accueil</a>
                        <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande
                    </div>
                </div>
            </div>

            <div class="content">

                <form action="<?= base_url('/employee/soumettre') ?>" method="post">
                    <?= csrf_field() ?>

                    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start"
                        class="form-layout">

                        <!-- Formulaire principal -->
                        <div>
                            <div class="form-section">
                                <h3>Détails de la demande</h3>

                                <div class="f-group" style="margin-bottom:1rem">
                                    <label class="f-label">Type de congé <span
                                            style="color:var(--danger)">*</span></label>
                                    <select name="type_conge_id" class="f-select" id="type_conge" required>
                                        <option value="">-- Choisir un type --</option>
                                        <?php foreach ($typesConges as $type): ?>
                                            <?php
                                            $soldeRestant = $soldes[$type['id']] ?? 0;
                                            $disabled = ($soldeRestant <= 0 && $type['jours_annuels'] > 0) ? 'disabled' : '';
                                            ?>
                                            <option value="<?= $type['id'] ?>" <?= $disabled ?>>
                                                <?= esc($type['nom']) ?>
                                                (<?= $soldeRestant ?> j restants)
                                                <?= $disabled ? ' - Plus de solde' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($validation) && $validation->hasError('type_conge_id')): ?>
                                        <div class="f-error"><i class="bi bi-exclamation-circle"></i>
                                            <?= $validation->getError('type_conge_id') ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-grid-2" style="margin-bottom:1rem">
                                    <div class="f-group">
                                        <label class="f-label">Date de début <span
                                                style="color:var(--danger)">*</span></label>
                                        <input type="date" name="date_debut" class="f-input" id="date_debut"
                                            value="<?= old('date_debut') ?>" required>
                                        <?php if (isset($validation) && $validation->hasError('date_debut')): ?>
                                            <div class="f-error"><i class="bi bi-exclamation-circle"></i>
                                                <?= $validation->getError('date_debut') ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="f-group">
                                        <label class="f-label">Date de fin <span
                                                style="color:var(--danger)">*</span></label>
                                        <input type="date" name="date_fin" class="f-input" id="date_fin"
                                            value="<?= old('date_fin') ?>" required>
                                        <?php if (isset($validation) && $validation->hasError('date_fin')): ?>
                                            <div class="f-error"><i class="bi bi-exclamation-circle"></i>
                                                <?= $validation->getError('date_fin') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Calcul automatique -->
                                <div class="f-computed" id="computed-days">
                                    <div class="f-computed-num" id="nb-jours">0</div>
                                    <div class="f-computed-label">
                                        jours calculés<br>
                                        <span style="font-size:.7rem;opacity:.7" id="periode-detail"></span>
                                    </div>
                                </div>

                                <div class="f-group" style="margin-bottom:1rem">
                                    <label class="f-label">Motif (optionnel)</label>
                                    <textarea name="motif" class="f-textarea"
                                        placeholder="Précisez le motif de votre demande si nécessaire..."><?= old('motif') ?></textarea>
                                    <div class="f-hint">Le motif est visible par le responsable RH.</div>
                                </div>

                                <div class="form-actions">
                                    <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la
                                        demande</button>
                                    <a href="<?= base_url('/employee') ?>" class="btn-secondary"><i class="bi bi-x"></i>
                                        Annuler</a>
                                </div>
                            </div>
                        </div>

                        <!-- Panneau latéral : solde & règles -->
                        <div style="display:flex;flex-direction:column;gap:1rem">
                            <div class="data-card" style="margin:0">
                                <div class="data-card-head">
                                    <h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos
                                        soldes actuels</h3>
                                </div>
                                <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
                                    <?php foreach ($typesConges as $type): ?>
                                        <?php
                                        $soldeRestant = $soldes[$type['id']] ?? 0;
                                        $pourcentage = $type['jours_annuels'] > 0 ? ($soldeRestant / $type['jours_annuels']) * 100 : 0;
                                        $colorClass = $pourcentage < 20 ? 'warn' : '';
                                        ?>
                                        <div>
                                            <div
                                                style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                                                <span
                                                    style="font-size:.8rem;color:var(--ink)"><?= esc($type['nom']) ?></span>
                                                <span
                                                    style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500">
                                                    <?= number_format($soldeRestant, 1) ?> j
                                                </span>
                                            </div>
                                            <div class="solde-bar">
                                                <div class="solde-fill <?= $colorClass ?>"
                                                    style="width: <?= min($pourcentage, 100) ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="flash flash-info" style="margin:0">
                                <i class="bi bi-info-circle-fill"></i>
                                <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre
                                    responsable.</span>
                            </div>
                            <div
                                style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
                                <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem">
                                    <i class="bi bi-clipboard-check"
                                        style="color:var(--forest);margin-right:5px"></i>Rappel des règles
                                </div>
                                <ul
                                    style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
                                    <li>Préavis minimum : 48h avant la date de début</li>
                                    <li>Pas de chevauchement avec une demande en cours</li>
                                    <li>Solde insuffisant = demande refusée automatiquement</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
        </div>

    </div>
</section>

<script>
    // Calcul automatique du nombre de jours
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const nbJoursSpan = document.getElementById('nb-jours');
    const periodeDetail = document.getElementById('periode-detail');

    function calculateDays() {
        if (dateDebut.value && dateFin.value) {
            const debut = new Date(dateDebut.value);
            const fin = new Date(dateFin.value);

            if (debut <= fin) {
                const diffTime = Math.abs(fin - debut);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                nbJoursSpan.textContent = diffDays;

                // Formatage de la période
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                periodeDetail.textContent =
                    `du ${debut.toLocaleDateString('fr-FR', options)} au ${fin.toLocaleDateString('fr-FR', options)}`;
            } else {
                nbJoursSpan.textContent = 'Erreur';
                periodeDetail.textContent = 'La date de fin doit être après la date de début';
            }
        }
    }

    dateDebut.addEventListener('change', calculateDays);
    dateFin.addEventListener('change', calculateDays);

    // Initialiser au chargement
    calculateDays();
</script>

<?= $this->endSection() ?>