<?= $this->extend('Inc/layout/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Historique des demandes</h3>
        <a href="<?= base_url('/rh') ?>" class="btn btn-secondary btn-sm">
            Retour demandes
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if (empty($historique)): ?>
                <div class="alert alert-info">
                    Aucun historique disponible.
                </div>
            <?php else: ?>

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Jours</th>
                            <th>Statut</th>
                            <th>Commentaire</th>
                            <th>Date MAJ</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($historique as $h): ?>

                            <?php
                            $badge = $h['statut'] === 'approuve'
                                ? 'success'
                                : 'danger';
                            ?>

                            <tr>
                                <td>
                                    <?= esc($h['prenom']) ?>         <?= esc($h['nom']) ?><br>
                                    <small class="text-muted"><?= esc($h['matricule']) ?></small>
                                </td>

                                <td><?= esc($h['type_nom']) ?></td>

                                <td>
                                    <?= date('d/m/Y', strtotime($h['date_debut'])) ?>
                                    →
                                    <?= date('d/m/Y', strtotime($h['date_fin'])) ?>
                                </td>

                                <td><?= $h['nb_jours'] ?> j</td>

                                <td>
                                    <span class="badge bg-<?= $badge ?>">
                                        <?= ucfirst($h['statut']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= esc($h['commentaire'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= date('d/m/Y H:i', strtotime($h['updated_at'])) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>

                </table>

            <?php endif; ?>

        </div>
    </div>

</div>

<?= $this->endSection() ?>