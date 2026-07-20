<?= $this->extend('Inc/layout/main') ?>

<?= $this->section('content') ?>

<section id="page-admin-employes" style="margin-top:3rem">
    <div class="app-wrap">

        <aside class="sidebar">...</aside>

        <div class="main">
            <div class="topbar">
            </div>

            <div class="content">

                <form action="<?= site_url('admin/creerEmploye') ?>" method="post" class="form-section">
                    <?= csrf_field() ?>
                    <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employé
                    </h3>
                    <div class="form-grid-2" style="margin-bottom:1rem">
                        <div class="f-group">
                            <label class="f-label">Prénom</label>
                            <input type="text" name="prenom" class="f-input" required placeholder="Jean" />
                        </div>
                        <div class="f-group">
                            <label class="f-label">Nom</label>
                            <input type="text" name="nom" class="f-input" required placeholder="Rakoto" />
                        </div>
                        <div class="f-group">
                            <label class="f-label">Email</label>
                            <input type="email" name="email" class="f-input" required
                                placeholder="jean.rakoto@techmada.mg" />
                        </div>
                        <div class="f-group">
                            <label class="f-label">Mot de passe initial</label>
                            <input type="password" name="password" class="f-input" required />
                        </div>
                        <div class="f-group">
                            <label class="f-label">Département</label>
                            <select name="departement_id" class="f-select">
                                <?php foreach ($departements as $dep): ?>
                                    <option value="<?= $dep['id'] ?>"><?= $dep['nom'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="f-group">
                            <label class="f-label">Rôle</label>
                            <select name="role" class="f-select">
                                <option value="employee">Employé</option>
                                <option value="rh">Responsable RH</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <div class="f-group">
                            <label class="f-label">Date d'embauche</label>
                            <input type="date" name="date_embauche" class="f-input" value="<?= date('Y-m-d') ?>" />
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-forest"><i class="bi bi-plus"></i> Créer l'employé</button>
                    </div>
                </form>

                <div class="data-card">
                    <div class="data-card-head">
                        <h3>Tous les employés</h3>
                    </div>
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Employé</th>
                                <th>Département</th>
                                <th>Rôle</th>
                                <th>Embauche</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($employes)): ?>
                                <tr>
                                    <td colspan="5" class="text-center p-4">Aucun employé trouvé.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($employes as $e): ?>
                                    <tr>
                                        <td>
                                            <div class="profile-row">
                                                <div class="avatar av-green" style="width:32px;height:32px;font-size:.68rem">
                                                    <?= strtoupper(substr($e['prenom'], 0, 1) . substr($e['nom'], 0, 1)) ?>
                                                </div>
                                                <div class="profile-info">
                                                    <div class="pname"><?= $e['prenom'] . ' ' . $e['nom'] ?></div>
                                                    <div class="pdept"><?= $e['email'] ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="td-muted"><?= $e['dep_nom'] ?? 'Aucun' ?></td>
                                        <td><span class="type-badge"><?= $e['role'] ?></span></td>
                                        <td class="td-muted td-mono" style="font-size:.78rem"><?= $e['date_embauche'] ?></td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="<?= site_url('admin/edit/' . $e['id']) ?>"
                                                    class="btn-sm btn-edit">Éditer</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</section>
<script>
    function supprimer(id, nom) {
        if (confirm(`Supprimer ${nom} ?`)) {
            fetch('<?= base_url('/admin/employes/delete') ?>/' + id, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        }
    }
</script>

<?= $this->endSection() ?>