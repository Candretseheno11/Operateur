<?= $this->extend('Inc/layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5>Ajouter un employé</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('/admin/employes/save') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Matricule</label>
                                <input type="text" name="matricule" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Rôle</label>
                                <select name="role" class="form-control" required>
                                    <option value="employee">Employé</option>
                                    <option value="rh">RH</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nom</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Prénom</label>
                                <input type="text" name="prenom" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mot de passe</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Département</label>
                                <select name="departement_id" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    <?php foreach ($departements as $dept): ?>
                                        <option value="<?= $dept['id'] ?>"><?= esc($dept['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Date d'embauche</label>
                                <input type="date" name="date_embauche" class="form-control" required>
                            </div>
                        </div>

                        <hr>
                        <h6>Soldes initiaux</h6>
                        <div class="row">
                            <?php foreach ($typesConges as $type): ?>
                                <?php if ($type['jours_annuels'] > 0): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="soldes[]"
                                                value="<?= $type['id'] ?>" checked>
                                            <label class="form-check-label">
                                                <?= esc($type['nom']) ?> (<?= $type['jours_annuels'] ?> jours)
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <a href="<?= base_url('/admin/employes') ?>" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>