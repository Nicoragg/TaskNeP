<?php
require_once "../helpers/functions.php";

if (empty($_SESSION['task_csrf_token'])) {
  $_SESSION['task_csrf_token'] = bin2hex(random_bytes(32));
}

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
      <textarea name="message" rows="3" required placeholder="Escreva seu comentário..."></textarea>
      <div>
        <button type="submit">Enviar</button>
      </div>
    </form>
    <button class="modal-close">Fechar</button>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
  const filterResponsible = document.getElementById('filter-responsible');
  const filterPriority = document.getElementById('filter-priority');
  const filterStatus = document.getElementById('filter-status');
  const cards = document.querySelectorAll('.board .card');
  const modal = document.getElementById('comments-modal');
  const listEl = document.getElementById('comments-list');
  const form = document.getElementById('comment-form');
  let csrfToken = '<?= $_SESSION['task_csrf_token'] ?? "" ?>';
  let currentTaskId = null;

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

  document.querySelectorAll('.column .cards').forEach(columnEl => {
    new Sortable(columnEl, {
      group: 'tasks',
      animation: 150,
      onEnd: event => {
        const cardEl = event.item;
        const taskId = cardEl.dataset.id;
        const newStatus = cardEl.closest('.column').dataset.statusColumn;

        fetch('./apis/update_task_status.php', {
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
                throw new Error(`Erro ao atualizar status da tarefa: ${response.status}: ${text}`);
              });
            }
            return response.json();
          })
          .then(data => {
            if (!data.success) {
              alert('Erro ao atualizar status da tarefa: ' + data.error);
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

  document.querySelectorAll('.btn-comments').forEach(btn => {
    btn.addEventListener('click', () => {
      currentTaskId = btn.dataset.taskId;
      listEl.innerHTML = '';
      fetch(`./apis/get_comments.php?task_id=${currentTaskId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            data.comments.forEach(c => {
              const div = document.createElement('div');
              div.classList.add('comment');
              div.innerHTML = `<strong>${c.author}</strong>
                             <small>${new Date(c.created_at).toLocaleString('pt-BR')}</small>
                             <p>${c.message}</p>`;
              listEl.appendChild(div);
            });
            modal.style.display = 'flex';
          } else {
            alert('Erro ao carregar comentários: ' + data.error);
          }
        })
        .catch(error => {
          console.error('Erro na requisição:', error);
          alert('Falha na comunicação com o servidor');
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
          message: msg,
          csrf_token: csrfToken
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const c = data.comment;
          const div = document.createElement('div');
          div.classList.add('comment');
          div.innerHTML = `<strong>${c.author}</strong>
                       <small>${c.created_at}</small>
                       <p>${c.message}</p>`;
          listEl.prepend(div);
          form.reset();

          const badge = document.querySelector(`.btn-comments[data-task-id="${currentTaskId}"]`);
          badge.textContent = `Comentários (${data.total_comments})`;

          if (data.newCsrfToken) {
            csrfToken = data.newCsrfToken;
          }
        } else {
          alert('Erro: ' + data.error);
        }
      })
      .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Falha na comunicação com o servidor');
      });
  });

  window.addEventListener('click', event => {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });
</script>
