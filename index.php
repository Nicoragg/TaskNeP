<?php
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['user'])) {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
} else {
  header('Location: admin/index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task Manager</title>
  <link rel="stylesheet" href="./assets/stylesheets/style.css">
</head>


<body>
  <h1>Login</h1>
  <?php if (!empty($_GET['error'])): ?>
    <p style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
  <?php endif; ?>

  <form method="post" action="admin/index.php">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <label for="login">Usuário:</label><br>
    <input type="text" name="login" id="login" required><br><br>

    <label for="senha">Senha:</label><br>
    <input type="password" name="senha" id="senha" required><br><br>

    <button type="submit">Entrar</button>
  </form>
</body>

</html>
