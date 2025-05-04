<?php
session_start();
$id = $_GET['id'] ?? null;
$tarefa = null;

if ($id && isset($_SESSION['tarefas'])) {
  foreach ($_SESSION['tarefas'] as $t) {
    if ($t['id'] === $id) {
      $tarefa = $t;
      break;
    }
  }
}
?>

<h1>Detalhes da Tarefa</h1>

<?php if ($tarefa): ?>
  <p><strong>Nome:</strong> <?= htmlspecialchars($tarefa["nome"]) ?></p>
  <p><strong>Descrição:</strong> <?= htmlspecialchars($tarefa["descricao"]) ?></p>
  <p><strong>Prazo:</strong> <?= htmlspecialchars($tarefa["prazo"]) ?></p>
  <p><strong>Responsável:</strong> <?= htmlspecialchars($tarefa["responsavel"]) ?></p>
  <p><strong>Prioridade:</strong> <?= htmlspecialchars($tarefa["prioridade"]) ?></p>
  <hr>
  <h3>Comentários:</h3>
  <p>Em breve...</p>
<?php else: ?>
  <p>Tarefa não encontrada.</p>
<?php endif; ?>
