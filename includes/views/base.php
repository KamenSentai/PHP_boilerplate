<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php $inheritance->startblock('title'); ?><?php $inheritance->endblock(); ?></title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>styles/app.css">
  </head>
  <body>
    <?php $inheritance->startblock('body'); ?><?php $inheritance->endblock(); ?>
    <script src="<?= BASE_URL; ?>scripts/app.js"></script>
  </body>
</html>
