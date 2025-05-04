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

function loadUsers(): array
{
  $usersFile = __DIR__ . '/../data/users.json';
  if (file_exists($usersFile)) {
    return json_decode(file_get_contents($usersFile), true) ?? [];
  }
  return [];
}
