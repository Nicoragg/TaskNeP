<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

if (!file_exists('../../helpers/functions.php')) {
  echo json_encode(['success' => false, 'error' => 'functions.php not found']);
  exit;
}

require_once '../../helpers/functions.php';

try {
  if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
  }

  $raw = file_get_contents('php://input');
  if (empty($raw)) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
  }

  $data = json_decode($raw, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit;
  }

  $taskId = $data['task_id'] ?? '';
  $message = trim($data['message'] ?? '');
  $csrfToken = $data['csrf_token'] ?? '';

  if (empty($_SESSION['task_csrf_token']) || !hash_equals($_SESSION['task_csrf_token'], $csrfToken)) {
    echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
    exit;
  }

  if (empty($taskId)) {
    echo json_encode(['success' => false, 'error' => 'Task ID is required']);
    exit;
  }

  if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Comment message cannot be empty']);
    exit;
  }

  $author = $_SESSION['user'];

  $tasks = loadTasks();
  if ($tasks === false) {
    echo json_encode(['success' => false, 'error' => 'Falha ao carregar os dados das tarefas']);
    exit;
  }

  $total = 0;
  $added = false;

  foreach ($tasks as &$t) {
    if ($t['id'] === $taskId) {
      if (!isset($t['comments']) || !is_array($t['comments'])) {
        $t['comments'] = [];
      }

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

  if (!$added) {
    echo json_encode(['success' => false, 'error' => 'Tarefa não encontrada']);
    exit;
  }

  if (!saveTasks($tasks)) {
    echo json_encode(['success' => false, 'error' => 'Falha ao salvar os dados das tarefas']);
    exit;
  }

  $_SESSION['task_csrf_token'] = bin2hex(random_bytes(32));

  echo json_encode([
    'success'        => true,
    'comment'        => $newComment,
    'total_comments' => $total,
    'newCsrfToken'   => $_SESSION['task_csrf_token']
  ]);
} catch (Exception $e) {
  error_log('Error in update_task_comments.php: ' . $e->getMessage());
  echo json_encode(['success' => false, 'error' => 'Erro no servidor: ' . $e->getMessage()]);
  exit;
}
