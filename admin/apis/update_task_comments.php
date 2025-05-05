<?php
session_start();
header('Content-Type: application/json');
require_once '../../helpers/functions.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$taskId = $data['task_id'] ?? '';
$message = trim($data['message'] ?? '');

if (!$taskId || !$message) {
  echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
  exit;
}

// usuário logado como autor
$author = $_SESSION['user'] ?? 'anônimo';

$tasks = loadTasks();
$total = 0;
$added = false;
foreach ($tasks as &$t) {
  if ($t['id'] === $taskId) {
    $newComment = [
      'author'     => $author,
      'message'    => htmlspecialchars($message, ENT_QUOTES),
      'created_at' => date('Y-m-d H:i:s'),
    ];
    array_unshift($t['comments'], $newComment);
    $t['updated_at'] = date('Y-m-d H:i:s');
    $added = true;
    $total = count($t['comments']);
    break;
  }
}

if ($added && saveTasks($tasks)) {
  echo json_encode([
    'success'        => true,
    'comment'        => $newComment,
    'total_comments' => $total
  ]);
} else {
  echo json_encode(['success' => false, 'error' => 'Falha ao salvar']);
}
