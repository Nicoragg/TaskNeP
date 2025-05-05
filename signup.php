<?php
session_start();
session_regenerate_id(true);

require_once "./helpers/functions.php";

if (empty($_SESSION['signup_csrf_token'])) {
  $_SESSION['signup_csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $csrfToken = $_POST['csrf_token'] ?? '';
  if (empty($_SESSION['signup_csrf_token']) || !hash_equals($_SESSION['signup_csrf_token'], $csrfToken)) {
    $message = 'Falha na validação CSRF.';
  } else {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password) || empty($email)) {
      $message = 'Usuário, email e senha são obrigatórios.';
    } elseif ($password !== $confirmPassword) {
      $message = 'As senhas não coincidem.';
    } else {
      $emailValidation = validateEmail($email);
      if (!$emailValidation['valid']) {
        $message = $emailValidation['message'];
      } else {
        $usersFile = __DIR__ . '/data/users.json';

        if (file_exists($usersFile)) {
          $users = json_decode(file_get_contents($usersFile), true);
        } else {
          if (!is_dir(__DIR__ . '/data')) {
            mkdir(__DIR__ . '/data', 0755, true);
          }
          $users = [];
        }

        if (isset($users[$username])) {
          $message = 'Este nome de usuário já está em uso.';
        } else {
          $emailExists = false;
          foreach ($users as $userData) {
            if (isset($userData['email']) && $userData['email'] === $email) {
              $emailExists = true;
              break;
            }
          }

          if ($emailExists) {
            $message = 'Este email já está em uso.';
          } else {
            $users[$username] = [
              'password' => password_hash($password, PASSWORD_DEFAULT),
              'email' => $email,
              'created_at' => date('Y-m-d H:i:s')
            ];

            if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {
              $message = 'Usuário cadastrado com sucesso! Você já pode fazer login.';
              unset($_SESSION['signup_csrf_token']);
              $_SESSION['signup_csrf_token'] = bin2hex(random_bytes(32));

              header('Location: index.php?success=1');
              exit;
            } else {
              $message = 'Erro ao salvar o usuário. Tente novamente.';
            }
          }
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro - Task Manager</title>
  <link rel="stylesheet" href="./assets/stylesheets/style.css">
  <link rel="stylesheet" href="./assets/stylesheets/auth.css">
</head>

<body>
  <div class="login">
    <h1>Cadastre-se</h1>

    <?php if (!empty($message)): ?>
      <div class="<?php echo strpos($message, 'sucesso') !== false ? 'success-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <form method="post" action="signup.php">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['signup_csrf_token']; ?>">

      <label for="username">Nome de usuário:</label>
      <input type="text" name="username" id="username" required>

      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required>

      <label for="password">Senha:</label>
      <input type="password" name="password" id="password" required>

      <label for="confirm_password">Confirmar senha:</label>
      <input type="password" name="confirm_password" id="confirm_password" required>

      <button type="submit">Cadastrar</button>
    </form>

    <p>Já tem uma conta? <a href="index.php">Fazer login</a></p>
  </div>
</body>

</html>
