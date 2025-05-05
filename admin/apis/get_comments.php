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

  $taskId = $_GET['task_id'] ?? '';

  if (empty($taskId)) {
    echo json_encode(['success' => false, 'error' => 'Task ID is required']);
    exit;
  }

  $tasks = loadTasks();
  if ($tasks === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to load tasks data']);
    exit;
  }

  $found = false;
  foreach ($tasks as $t) {
    if ($t['id'] === $taskId) {
      $found = true;
      echo json_encode([
        'success' => true,
        'comments' => isset($t['comments']) ? $t['comments'] : []
      ]);
      exit;
    }
  }

  if (!$found) {
    echo json_encode(['success' => false, 'error' => 'Task not found', 'comments' => []]);
  }
} catch (Exception $e) {
  error_log('Error in get_comments.php: ' . $e->getMessage());
  echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
  exit;
}
