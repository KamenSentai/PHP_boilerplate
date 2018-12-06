<?php include $dir . 'base.php'; ?>

<?php $inheritance->startblock('title'); ?>
  <?= $data['title']; ?>
<?php $inheritance->endblock('title'); ?>

<?php $inheritance->startblock('body'); ?>
  <h1>404 not found</h1>
<?php $inheritance->endblock('body'); ?>
