<?php
function validateInput($data): string
{
  return htmlspecialchars(trim($data));
}

function getInputSafely(string $type, string $fieldName, $default = null)
{
  $value = null;

  switch (strtoupper($type)) {
    case 'GET':
      $value = $_GET[$fieldName] ?? null;
      break;
    case 'POST':
      $value = $_POST[$fieldName] ?? null;
      break;
    case 'REQUEST':
      $value = $_REQUEST[$fieldName] ?? null;
      break;
  }

  if ($value === null) {
    return $default;
  }

  return validateInput($value);
}

function validateEmail(string $email): array
{
  $email = validateInput($email);

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return ['valid' => false, 'message' => 'Formato de email inválido.'];
  }

  [$localPart, $domain] = explode('@', $email);

  if (!checkdnsrr($domain, 'MX')) {
    return ['valid' => false, 'message' => 'Domínio de email inválido.'];
  }

  if (strlen($email) > 254) {
    return ['valid' => false, 'message' => 'Email muito longo.'];
  }

  return ['valid' => true];
}

function dateParserToBrazilianFormat($date)
{
  $datetime = new DateTime($date);
  return $datetime->format('d/m/Y H:i');
}

function translatePriority($priority): string
{
  $translations = [
    'very-high' => 'Muito Alta',
    'high' => 'Alta',
    'medium' => 'Média',
    'low' => 'Baixa',
    'very-low' => 'Muito Baixa'
  ];

  return $translations[$priority] ?? ucfirst($priority);
}

function loadUsers(): array
{
  $usersFile = __DIR__ . '/../data/users.json';
  if (file_exists($usersFile)) {
    return json_decode(file_get_contents($usersFile), true) ?? [];
  }
  return [];
}

function loadTasks(): array
{
  $file = __DIR__ . '/../data/tasks.json';
  if (!file_exists($file)) {
    if (!is_dir(dirname($file))) mkdir(dirname($file), 0755, true);
    file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
  }
  $tasks = json_decode(file_get_contents($file), true);
  foreach ($tasks as &$task) {
    if (!isset($task['comments']) || !is_array($task['comments'])) {
      $task['comments'] = [];
    }
  }
  return $tasks;
}

function saveTasks(array $tasks): bool
{
  $file = __DIR__ . '/../data/tasks.json';
  return file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}


function loadLogs(): array
{
  $file = __DIR__ . '/../data/logs.json';
  if (!file_exists($file)) {
    if (!is_dir(dirname($file))) {
      mkdir(dirname($file), 0755, true);
    }
    file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
  }
  $logs = json_decode(file_get_contents($file), true);
  return is_array($logs) ? $logs : [];
}

function addLog(string $user, ?string $taskId, string $action, array $details = []): bool
{
  $logs = loadLogs();
  $logs[] = [
    'timestamp' => date('Y-m-d H:i:s'),
    'user'      => $user,
    'task_id'   => $taskId,
    'action'    => $action,
    'details'   => $details,
  ];
  $file = __DIR__ . '/../data/logs.json';
  return file_put_contents($file, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}
