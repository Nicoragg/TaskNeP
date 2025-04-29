<?php
$page = $_GET["page"] ?? "login";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aula POST</title>
  <link rel="stylesheet" href="./assets/stylesheets/style.css">
</head>

<body>
  <?php
  require_once "./header.php";

  echo "<main>";
  require_once(match ($page) {
    "login" => "./pages/login.php",
    "home" => "./pages/home.php",
    "create" => "./pages/form.php",
    "user" => "./pages/user.php",
    "tasks" => "./pages/tarefas.php",
    default => "./pages/404.php",
  });
  echo "</main>";
  include_once "./footer.php";
  ?>
</body>

</html>
