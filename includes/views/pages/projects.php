<?php include $dir . 'base.php'; ?>

<?php $inheritance->startblock('title'); ?>
<?= $data['title']; ?>
<?php $inheritance->endblock('title'); ?>

<?php $inheritance->startblock('body'); ?>
<h1><?= $data['h1']; ?></h1>
<?php $inheritance->endblock('body'); ?>
