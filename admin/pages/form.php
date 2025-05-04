<?php
require_once "../helpers/functions.php";
$users = loadUsers();
?>

<div class="card-title">
  <h1>Criar Nova Tarefa</h1>
</div>

<form method="post" action="?page=create">
  <input type="hidden" name="action" value="create_task">

  <label for="title">Título:</label>
  <input type="text" name="title" id="title" required>

  <label for="description">Descrição:</label>
  <textarea name="description" id="description" rows="4" required></textarea>

  <label for="time">Prazo:</label>
  <input type="date" name="time" id="time" required>

  <label for="status">Status:</label>
  <select name="status" id="status">
    <option value="pending">Pendente</option>
    <option value="ongoing">Em andamento</option>
    <option value="completed">Concluída</option>
    <option value="canceled">Cancelada</option>
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

  <label for="priority">Prioridade:</label>
  <select name="priority" id="priority">
    <option value="very-high">Muito Alta</option>
    <option value="high">Alta</option>
    <option value="medium">Média</option>
    <option value="low">Baixa</option>
    <option value="very-low">Muito Baixa</option>
  </select>

  <button type="submit">Salvar Tarefa</button>
</form>
