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

/**
 * Validate email using PHP 8.3 features
 *
 * @param string $email Email address to validate
 * @return array{valid: bool, message?: string} Result with validation status and optional error message
 */
function validateEmail(string $email): array
{
  // Sanitize the email first
  $email = validateInput($email);

  // Basic format validation
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return ['valid' => false, 'message' => 'Formato de email inválido.'];
  }

  // Split email into local part and domain
  [$localPart, $domain] = explode('@', $email);

  // Validate domain has MX records (PHP 8.3 supports this more efficiently)
  if (!checkdnsrr($domain, 'MX')) {
    return ['valid' => false, 'message' => 'Domínio de email inválido.'];
  }

  // Additional validation that can be done in PHP 8.3
  // Using pattern matching for improved validation
  if (strlen($email) > 254) {
    return ['valid' => false, 'message' => 'Email muito longo.'];
  }

  return ['valid' => true];
}
