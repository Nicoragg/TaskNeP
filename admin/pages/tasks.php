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
        <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars(translatePriority($p)) ?></option>
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
            data-id="<?= htmlspecialchars($task['id']) ?>"
            data-responsible="<?= htmlspecialchars($task['responsible']) ?>"
            data-priority="<?= htmlspecialchars($task['priority']) ?>"
            data-status="<?= htmlspecialchars($task['status']) ?>">
            <span class="badge priority priority-<?= $task['priority'] ?>">
              <?= translatePriority($task['priority']) ?>
            </span>
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

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
  let csrfToken = '<?= $_SESSION['task_csrf_token'] ?? "" ?>';

  document.querySelectorAll('.column .cards').forEach(columnEl => {
    new Sortable(columnEl, {
      group: 'tasks',
      animation: 150,
      onEnd: event => {
        const cardEl = event.item;
        const taskId = cardEl.dataset.id;
        const newStatus = cardEl.closest('.column').dataset.statusColumn;

        fetch('update.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
              id: taskId,
              status: newStatus
            })
          })
          .then(response => {
            if (!response.ok) {
              return response.text().then(text => {
                throw new Error(`Server returned ${response.status}: ${text}`);
              });
            }
            return response.json();
          })
          .then(data => {
            if (!data.success) {
              alert('Erro ao atualizar tarefa: ' + data.error);
              window.location.reload();
            } else if (data.newCsrfToken) {
              csrfToken = data.newCsrfToken;
            }
          })
          .catch((error) => {
            console.error('Erro na requisição:', error);
            alert('Falha na comunicação com o servidor: ' + error.message);
            window.location.reload();
          });
      }
    });
  });
</script>
