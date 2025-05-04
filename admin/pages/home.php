<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = $_POST['user'] ?? '';
  $password = $_POST['password'] ?? '';

  if ($user === 'admin' && $password === '1234') {
    $_SESSION['usuario'] = $user;
    header("Location: index.php?page=home");
    exit;
  } else {
    $erro = "Usuário ou senha inválidos.";
    include "./pages/login.php";
    exit;
  }
}

if (!isset($_SESSION['usuario'])) {
  header("Location: index.php?page=login");
  exit;
}

$tarefas = $_SESSION['tarefas'] ?? [];
$total = count($tarefas);
$altas = count(array_filter($tarefas, fn($t) => $t['prioridade'] === 'high' || $t['prioridade'] === 'very-high'));
?>

<h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?></h1>

<p>Total de tarefas: <strong><?= $total ?></strong></p>
<p>Tarefas de alta prioridade: <strong><?= $altas ?></strong></p>

<a href="index.php?page=create">Criar Nova Tarefa</a><br>
<a href="index.php?page=tasks">Ver Tarefas</a><br><br>

<a href="index.php?logout=true">Sair</a>
