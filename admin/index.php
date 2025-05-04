<?php
session_start();
session_regenerate_id(true);

$usuarioEsperado = 'admin';
$senhaHashEsperada = password_hash('123456', PASSWORD_DEFAULT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['user'])) {
  $tokenEnv = $_POST['csrf_token'] ?? '';
  if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenEnv)) {
    die('Falha na validação CSRF.');
  }

  $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
  $senha = $_POST['senha'] ?? '';

  if ($login === $usuarioEsperado && password_verify($senha, $senhaHashEsperada)) {
    $_SESSION['user'] = $login;
    unset($_SESSION['csrf_token']);
    header('Location: admin/index.php');
    exit;
  } else {
    $erro = 'Usuário ou senha inválidos';
  }
}

if (!isset($_SESSION['user'])) {
  header('Location: ../index.php?error=' . urlencode($erro ?? ''));
  exit;
}

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
