<?php if ($pager->getPageCount() > 1): ?>
<div class="d-flex align-items-center justify-content-center gap-2 py-4">

    <?php if ($pager->hasPrevious()): ?>
    <a class="pg-btn pg-nav" href="<?= $pager->getPrevious() ?>">
        ← Précédent
    </a>
    <?php else: ?>
    <span class="pg-btn pg-nav pg-disabled">
        ← Précédent
    </span>
    <?php endif; ?>

    <?php foreach ($pager->links() as $link): ?>
    <a class="pg-btn <?= $link['active'] ? 'pg-active' : '' ?>" href="<?= $link['uri'] ?>">
        <?= $link['title'] ?>
    </a>
    <?php endforeach; ?>

    <?php if ($pager->hasNext()): ?>
    <a class="pg-btn pg-nav" href="<?= $pager->getNext() ?>">
        Suivant →
    </a>
    <?php else: ?>
    <span class="pg-btn pg-nav pg-disabled">
        Suivant →
    </span>
    <?php endif; ?>

</div>
<?php endif; ?>