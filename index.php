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
  <div class="login">
    <h1>Task Manager</h1>
    <?php if (!empty($_GET['error'])): ?>
      <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form method="post" action="admin/index.php">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

      <label for="user">Usuário:</label>
      <input type="text" name="user" id="user" required>

      <label for="password">Senha:</label>
      <input type="password" name="password" id="password" required>

      <button type="submit">Entrar</button>
    </form>
  </div>
</body>

</html>
