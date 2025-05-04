<?php
function validateInput($data): string
{
  return htmlspecialchars(trim($data));
}

/**
 * Safely get and sanitize input from various sources
 *
 * @param string $type Input type (GET, POST, etc)
 * @param string $fieldName Field name to retrieve
 * @param mixed $default Default value if field doesn't exist
 * @return mixed Sanitized input or default value
 */
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
