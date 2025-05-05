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
  $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

  if (empty($_SESSION['task_csrf_token'])) {
    echo json_encode(['success' => false, 'error' => 'CSRF token not set in session']);
    exit;
  }

  if (!hash_equals($_SESSION['task_csrf_token'], $csrf_token)) {
    echo json_encode(['success' => false, 'error' => 'CSRF token mismatch']);
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

  if (!isset($data['id'], $data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos']);
    exit;
  }

  $taskId = $data['id'];
  $newStatus = $data['status'];

  $validStatuses = ['pending', 'ongoing', 'completed', 'canceled'];
  if (!in_array($newStatus, $validStatuses, true)) {
    echo json_encode(['success' => false, 'error' => 'Status inválido']);
    exit;
  }

  $tasks = loadTasks();
  if ($tasks === false) {
    echo json_encode(['success' => false, 'error' => 'Falha ao carregar tarefas']);
    exit;
  }

  $updated = false;

  foreach ($tasks as &$task) {
    if ($task['id'] === $taskId) {
      $task['status']     = $newStatus;
      $task['updated_at'] = date('Y-m-d H:i:s');
      $updated = true;
      break;
    }
  }

  if (!$updated) {
    echo json_encode(['success' => false, 'error' => 'Tarefa não encontrada']);
    exit;
  }

  $saveResult = saveTasks($tasks);
  if ($saveResult === false) {
    echo json_encode(['success' => false, 'error' => 'Falha ao salvar dados']);
    exit;
  }

  $_SESSION['task_csrf_token'] = bin2hex(random_bytes(32));

  echo json_encode([
    'success' => true,
    'newCsrfToken' => $_SESSION['task_csrf_token']
  ]);
} catch (Exception $e) {
  error_log('Error in update_task_status: ' . $e->getMessage());
  echo json_encode(['success' => false, 'error' => 'Erro no servidor: ' . $e->getMessage()]);
  exit;
}
