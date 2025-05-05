<?php
require_once "../helpers/functions.php";

if (empty($_SESSION['task_csrf_token'])) {
  $_SESSION['task_csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';

if (isset($_GET['page']) && $_GET['page'] === 'create') {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (
      empty($_SESSION['task_csrf_token'])
      || !hash_equals($_SESSION['task_csrf_token'], $csrfToken)
    ) {
      $message = 'Falha na validação CSRF.';
    } else {
      $title       = trim($_POST['title'] ?? '');
      $description = trim($_POST['description'] ?? '');
      $time    = $_POST['time'] ?? '';
      $status      = $_POST['status'] ?? '';
      $responsible = $_POST['responsible'] ?? '';
      $priority    = $_POST['priority'] ?? '';

      if (!$title || !$description || !$time) {
        $message = 'Título, descrição e prazo são obrigatórios.';
      } else {
        $tasks = loadTasks();

        $tasks[] = [
          'id'          => uniqid('', true),
          'title'       => $title,
          'description' => $description,
          'time'        => $time,
          'status'      => $status,
          'responsible' => $responsible,
          'priority'    => $priority,
          'created_at'  => date('Y-m-d H:i:s'),
        ];

        if (saveTasks($tasks)) {
          unset($_SESSION['task_csrf_token']);
          $_SESSION['task_csrf_token'] = bin2hex(random_bytes(32));
          header('Location: index.php?page=tasks&success=1');
          exit;
        } else {
          $message = 'Erro ao salvar a tarefa. Tente novamente.';
        }
      }
    }
  }
}

$users = loadUsers();
?>

<h1>Criar Nova Tarefa</h1>

<?php if ($message): ?>
  <div class="error-message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="content-wrapper">
  <form method="post" action="?page=create">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['task_csrf_token'] ?>">

    <label for="title">Título:</label>
    <input type="text" name="title" id="title" required>

    <label for="description">Descrição:</label>
    <textarea name="description" id="description" rows="4" required></textarea>

    <label for="time">Prazo:</label>
    <input type="datetime-local" name="time" id="time" required>

    <label for="status">Status:</label>
    <select name="status" id="status">
      <option value="pending">Pendente</option>
      <option value="ongoing">Em andamento</option>
      <option value="completed">Concluída</option>
      <option value="canceled">Cancelada</option>
    </select>

    <label for="priority">Prioridade:</label>
    <select name="priority" id="priority">
      <option value="very-high">Muito Alta</option>
      <option value="high">Alta</option>
      <option value="medium">Média</option>
      <option value="low">Baixa</option>
      <option value="very-low">Muito Baixa</option>
    </select>

    <label for="responsible">Responsável:</label>
    <select name="responsible" id="responsible">
      <option value="<?= $_SESSION['user'] ?>">Para mim (<?= $_SESSION['user'] ?>)</option>
      <?php foreach ($users as $username => $data): ?>
        <?php if ($username !== $_SESSION['user']): ?>
          <option value="<?= htmlspecialchars($username) ?>"><?= htmlspecialchars($username) ?></option>
        <?php endif; ?>
      <?php endforeach; ?>
    </select>

    <button type="submit">Salvar Tarefa</button>
  </form>
</div>
