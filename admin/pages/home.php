<?php
require_once "../helpers/functions.php";

$tasks = loadTasks();
$logs = loadLogs();

// Count tasks by status
$taskStats = [
  'all' => count($tasks),
  'pending' => 0,
  'ongoing' => 0,
  'completed' => 0,
  'canceled' => 0
];

$upcomingTasks = [];
$now = new DateTime();
$sevenDaysLater = (new DateTime())->modify('+7 days');

foreach ($tasks as $task) {
  // Update statistics
  if (isset($task['status']) && array_key_exists($task['status'], $taskStats)) {
    $taskStats[$task['status']]++;
  }

  if ($task['status'] !== 'completed' && $task['status'] !== 'canceled') {
    try {
      $deadline = new DateTime($task['time']);
      if ($deadline > $now && $deadline <= $sevenDaysLater) {
        $upcomingTasks[] = $task;
      }
    } catch (Exception $e) {
      // Skip tasks with invalid date format
    }
  }
}

usort($upcomingTasks, function ($a, $b) {
  return strtotime($a['time']) - strtotime($b['time']);
});

$recentLogs = array_slice(array_reverse($logs), 0, 5);
?>

<h1>Painel de Controle</h1>

<div class="welcome-banner">
  <h2>Bem-vindo, <?= htmlspecialchars($_SESSION['user']) ?></h2>
  <p>Aqui está um resumo das suas tarefas e atividades recentes</p>
</div>

<div class="dashboard-grid">
  <div class="dashboard-card stats-card">
    <h3>Resumo de Tarefas</h3>
    <div class="stats-container">
      <div class="stat-item">
        <span class="stat-number"><?= $taskStats['all'] ?></span>
        <span class="stat-label">Total</span>
      </div>
      <div class="stat-item pending">
        <span class="stat-number"><?= $taskStats['pending'] ?></span>
        <span class="stat-label">Pendentes</span>
      </div>
      <div class="stat-item ongoing">
        <span class="stat-number"><?= $taskStats['ongoing'] ?></span>
        <span class="stat-label">Em Andamento</span>
      </div>
      <div class="stat-item completed">
        <span class="stat-number"><?= $taskStats['completed'] ?></span>
        <span class="stat-label">Concluídas</span>
      </div>
      <div class="stat-item canceled">
        <span class="stat-number"><?= $taskStats['canceled'] ?></span>
        <span class="stat-label">Canceladas</span>
      </div>
    </div>
  </div>

  <div class="dashboard-card actions-card">
    <h3>Ações Rápidas</h3>
    <div class="quick-actions">
      <a href="?page=create" class="quick-action-btn create">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Nova Tarefa
      </a>
      <a href="?page=tasks" class="quick-action-btn view">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline points="14 2 14 8 20 8"></polyline>
          <line x1="16" y1="13" x2="8" y2="13"></line>
          <line x1="16" y1="17" x2="8" y2="17"></line>
          <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
        Ver Tarefas
      </a>
      <a href="?page=logs" class="quick-action-btn logs">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
          <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
        </svg>
        Registros
      </a>
      <a href="?page=user" class="quick-action-btn profile">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
        Perfil
      </a>
    </div>
  </div>

  <div class="dashboard-card tasks-card">
    <h3>Prazos Próximos</h3>
    <?php if (empty($upcomingTasks)): ?>
      <p class="no-data">Não há tarefas com prazos próximos.</p>
    <?php else: ?>
      <ul class="upcoming-tasks">
        <?php foreach (array_slice($upcomingTasks, 0, 5) as $task): ?>
          <li class="task-item priority-<?= htmlspecialchars($task['priority']) ?>">
            <div class="task-info">
              <h4><?= htmlspecialchars($task['title']) ?></h4>
              <div class="task-meta">
                <span class="deadline">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                  <?= dateParserToBrazilianFormat($task['time']) ?>
                </span>
                <span class="priority"><?= translatePriority($task['priority']) ?></span>
              </div>
            </div>
            <span class="status status-<?= htmlspecialchars($task['status']) ?>">
              <?= $task['status'] === 'pending' ? 'Pendente' : ($task['status'] === 'ongoing' ? 'Em andamento' : ucfirst($task['status'])) ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php if (count($upcomingTasks) > 5): ?>
        <a href="?page=tasks" class="view-all">Ver todas as tarefas</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <div class="dashboard-card activities-card">
    <h3>Atividades Recentes</h3>
    <?php if (empty($recentLogs)): ?>
      <p class="no-data">Nenhuma atividade recente registrada.</p>
    <?php else: ?>
      <ul class="activity-list">
        <?php foreach ($recentLogs as $log): ?>
          <li class="activity-item">
            <div class="activity-icon action-<?= str_replace('_', '-', $log['action']) ?>">
              <?php if ($log['action'] === 'created'): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              <?php elseif ($log['action'] === 'status_updated'): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="9 11 12 14 22 4"></polyline>
                  <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
              <?php elseif ($log['action'] === 'comment_added'): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
              <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
              <?php endif; ?>
            </div>
            <div class="activity-content">
              <p>
                <strong><?= htmlspecialchars($log['user']) ?></strong>
                <?= str_replace('_', ' ', $log['action']) ?>
                <?php if (!empty($log['task_id'])): ?>
                  uma tarefa
                <?php endif; ?>
              </p>
              <span class="activity-time"><?= htmlspecialchars($log['timestamp']) ?></span>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <a href="?page=logs" class="view-all">Ver todos os registros</a>
    <?php endif; ?>
  </div>
</div>

<link rel="stylesheet" href="../assets/stylesheets/dashboard.css">
