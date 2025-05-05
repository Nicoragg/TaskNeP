<?php
require_once "../helpers/functions.php";
$logs = loadLogs();
?>

<h1>Registro de Atividades</h1>

<?php if (empty($logs)): ?>
  <h2>Ainda não há nada para exibir.</h2>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Horário</th>
        <th>Usuário</th>
        <th>Ação</th>
        <th>Tarefa</th>
        <th>Detalhes</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (array_reverse($logs) as $log): ?>
        <tr>
          <td><?= htmlspecialchars($log['timestamp']) ?></td>
          <td><?= htmlspecialchars($log['user']) ?></td>
          <td><?= htmlspecialchars(str_replace('_', ' ', $log['action'])) ?></td>
          <td>
            <?php if ($log['task_id']): ?>
              <?= htmlspecialchars($log['task_id']) ?>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td>
            <pre><?= htmlspecialchars(json_encode($log['details'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
