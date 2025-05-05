<?php
header('Content-Type: application/json');
require_once __DIR__ . '/helpers/functions.php';

$taskId = $_GET['task_id'] ?? '';
$tasks = loadTasks();
foreach ($tasks as $t) {
  if ($t['id'] === $taskId) {
    echo json_encode(['success' => true, 'comments' => $t['comments']]);
    exit;
  }
}
echo json_encode(['success' => false, 'comments' => []]);
