<?php
/*
Pedro Henrique Brugnolo - 34251154
Nicolas Gonçalves de Souza - 33710031
*/
session_start();
session_regenerate_id(true);

require_once "../helpers/functions.php";

$validUsers = loadUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['user'])) {
  $csrfToken = $_POST['csrf_token'] ?? '';
  if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    die('Falha na validação CSRF.');
  }

  $user = validateInput($_POST['user'] ?? '');
  $password = $_POST['password'] ?? '';

  if (array_key_exists($user, $validUsers)) {
    $storedPassword = is_array($validUsers[$user]) ?
      $validUsers[$user]['password'] :
      $validUsers[$user];

    if (password_verify($password, $storedPassword)) {
      $_SESSION['user'] = $user;
      if (is_array($validUsers[$user]) && isset($validUsers[$user]['email'])) {
        $_SESSION['email'] = $validUsers[$user]['email'];
      }
      unset($_SESSION['csrf_token']);
      header('Location: index.php');
      exit;
    }
  }

  $error = 'Usuário ou senha inválidos';
}

if (!isset($_SESSION['user'])) {
  header('Location: ../index.php?error=' . urlencode($error ?? ''));
  exit;
}

$page = getInputSafely('GET', 'page', 'home');
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task Manager</title>
  <link rel="stylesheet" href="../assets/stylesheets/style.css">
  <?php
  if (isset($page) && $page === 'create') {
    echo '<link rel="stylesheet" href="../assets/stylesheets/form.css">';
  }
  if (isset($page) && $page === 'tasks') {
    echo '<link rel="stylesheet" href="../assets/stylesheets/tasks.css">';
  }
  if (isset($page) && $page === 'logs') {
    echo '<link rel="stylesheet" href="../assets/stylesheets/logs.css">';
  }
  if (isset($page) && $page === 'home') {
    echo '<link rel="stylesheet" href="../assets/stylesheets/dashboard.css">';
  }
  ?>
</head>

<body>
  <?php
  require_once "../components/header.php";

  echo "<main>";
  require_once(match ($page) {
    "logout" => "./pages/logout.php",
    "home" => "./pages/home.php",
    "create" => "./pages/form.php",
    "user" => "./pages/user.php",
    "tasks" => "./pages/tasks.php",
    "logs" => "./pages/logs.php",
    default => "./pages/404.php",
  });
  echo "</main>";

  include_once "../components/footer.php";
  ?>
</body>

</html>
