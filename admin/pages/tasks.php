<?php
require_once "../helpers/functions.php";
$tasks = loadTasks();
$responsibles = array_unique(array_column($tasks, 'responsible'));
$priorities  = array_unique(array_column($tasks, 'priority'));
?>

<h1>Quadro de Tarefas</h1>

<div class="filters">
  <label>
    Responsável:
    <select id="filter-responsible">
      <option value=""></option>
      <?php foreach ($responsibles as $res): ?>
        <option value="<?= htmlspecialchars($res) ?>"><?= htmlspecialchars($res) ?></option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>
    Prioridade:
    <select id="filter-priority">
      <option value=""></option>
      <?php foreach ($priorities as $p): ?>
        <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars(ucfirst($p)) ?></option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>
    Status:
    <select id="filter-status">
      <option value=""></option>
      <option value="pending">Pendente</option>
      <option value="ongoing">Em andamento</option>
      <option value="completed">Concluída</option>
      <option value="canceled">Cancelada</option>
    </select>
  </label>
</div>

<div class="board">
  <?php
  $columns = [
    'pending'   => 'Pendente',
    'ongoing'   => 'Em andamento',
    'completed' => 'Concluída',
    'canceled'  => 'Cancelada'
  ];
  foreach ($columns as $statusKey => $statusLabel): ?>
    <div class="column" data-status-column="<?= $statusKey ?>">
      <h2><?= $statusLabel ?></h2>
      <div class="cards">
        <?php foreach ($tasks as $task):
          if ($task['status'] !== $statusKey) continue;
        ?>
          <div class="card"
            data-responsible="<?= htmlspecialchars($task['responsible']) ?>"
            data-priority="<?= htmlspecialchars($task['priority']) ?>"
            data-status="<?= htmlspecialchars($task['status']) ?>">
            <h3><?= htmlspecialchars($task['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($task['description'])) ?></p>
            <small>Prazo: <?= htmlspecialchars(dateParserToBrazilianFormat($task['time'])) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<script>
  const filterResponsible = document.getElementById('filter-responsible');
  const filterPriority = document.getElementById('filter-priority');
  const filterStatus = document.getElementById('filter-status');
  const cards = document.querySelectorAll('.board .card');

  function applyFilters() {
    const resVal = filterResponsible.value;
    const priVal = filterPriority.value;
    const staVal = filterStatus.value;

    cards.forEach(card => {
      const matchResponsible = !resVal || card.dataset.responsible === resVal;
      const matchPriority = !priVal || card.dataset.priority === priVal;
      const matchStatus = !staVal || card.dataset.status === staVal;

      card.style.display = (matchResponsible && matchPriority && matchStatus) ? '' : 'none';
    });
  }

  filterResponsible.addEventListener('change', applyFilters);
  filterPriority.addEventListener('change', applyFilters);
  filterStatus.addEventListener('change', applyFilters);
</script>
