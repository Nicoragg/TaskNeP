<?php
session_start();
session_regenerate_id(true);

require_once "../helpers/functions.php";

$validUsers = [
  'admin' => password_hash('admin', PASSWORD_DEFAULT),
  'admin2' => password_hash('admin2', PASSWORD_DEFAULT),
  'admin3' => password_hash('admin3', PASSWORD_DEFAULT)
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['user'])) {
  $csrfToken = $_POST['csrf_token'] ?? '';
  if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    die('Falha na validação CSRF.');
  }

  $user = validateInput($_POST['user'] ?? '');
  $password = $_POST['password'] ?? '';

  if (array_key_exists($user, $validUsers) && password_verify($password, $validUsers[$user])) {
    $_SESSION['user'] = $user;
    unset($_SESSION['csrf_token']);
    header('Location: index.php');
    exit;
  } else {
    $error = 'Usuário ou senha inválidos';
  }
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
    "tasks" => "./pages/tarefas.php",
    default => "./pages/404.php",
  });
  echo "</main>";

  include_once "../components/footer.php";
  ?>
</body>

</html>
