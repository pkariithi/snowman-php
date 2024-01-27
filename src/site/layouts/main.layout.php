<!DOCTYPE html>
<html lang="<?php echo $page->lang; ?>">
<head>
  <meta charset="<?php echo $page->charset; ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page->title; ?></title>
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  <?php echo $page->assets->css; ?>
</head>
<body>
  <?php echo $content; ?>
  <script src="assets/js/jquery.min.js"></script>
  <?php echo $page->assets->js; ?>
</body>
</html>