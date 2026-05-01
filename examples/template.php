<!-- 

Example of template.html

In $templates of config.php paths are should be 
written relatively to the root folder of app

As example if this file is located on root folder,
then $template can looks like this:

$template = 'template.html';
$templates = [
  'default' =>'template.html'
];

-->
<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>MSG</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="bg-dark">
  <div class="container d-flex justify-content-center align-items-center vh-100 novalidate">
    <form method="GET" action="index.php" autocomplete="off">
      <div class="input-group">
        <input type="text" name="key" class="form-control" placeholder="Ключ" autocomplete="off" spellcheck="false" autofocus>
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-download"></i>
        </button>
      </div>
        <?php if ($message !== ''): ?>
          <div class="text-muted small mt-2 text-center">
            <?php echo $message; ?>
          </div>
        <?php endif; ?>
    </form>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>