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
            <button class="btn-comments" data-task-id="<?= $task['id'] ?>">
              Comentários (<?= count($task['comments']) ?>)
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div id="comments-modal" class="modal" style="display:none;">
  <div class="modal-content">
    <h3>Comentários</h3>
    <div id="comments-list"></div>
    <form id="comment-form">
      <textarea name="message" rows="3" required placeholder="Escreva seu comentário…"></textarea>
      <button type="submit">Enviar</button>
    </form>
    <button class="modal-close">Fechar</button>
  </div>
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

        fetch('./apis/update_task_status', {
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

<script>
  const modal = document.getElementById('comments-modal');
  const listEl = document.getElementById('comments-list');
  const form = document.getElementById('comment-form');
  let currentTaskId = null;

  document.querySelectorAll('.btn-comments').forEach(btn => {
    btn.addEventListener('click', () => {
      currentTaskId = btn.dataset.taskId;
      listEl.innerHTML = '';
      fetch(`./apis/get_comments.php?task_id=${currentTaskId}`)
        .then(res => res.json())
        .then(data => {
          data.comments.forEach(c => {
            const div = document.createElement('div');
            div.classList.add('comment');
            div.innerHTML = `<strong>${c.author}</strong>
                           <small>${c.created_at}</small>
                           <p>${c.message}</p>`;
            listEl.appendChild(div);
          });
          modal.style.display = 'block';
        });
    });
  });

  document.querySelector('.modal-close').addEventListener('click', () => {
    modal.style.display = 'none';
  });

  form.addEventListener('submit', e => {
    e.preventDefault();
    const msg = form.message.value.trim();
    if (!msg) return;
    fetch('./apis/update_task_comments.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          task_id: currentTaskId,
          message: msg
        })
      })
      .then(r => r.json())
      .then(resp => {
        if (resp.success) {
          const c = resp.comment;
          const div = document.createElement('div');
          div.classList.add('comment');
          div.innerHTML = `<strong>${c.author}</strong>
                       <small>${c.created_at}</small>
                       <p>${c.message}</p>`;
          listEl.prepend(div);
          form.reset();
          const badge = document.querySelector(`.btn-comments[data-task-id="${currentTaskId}"]`);
          badge.textContent = `Comentários (${resp.total_comments})`;
        } else {
          alert('Erro: ' + resp.error);
        }
      });
  });
</script>
