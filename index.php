<?php
require_once 'config.php';

// Check template if exists
$template_key = $_GET['template'] ?? $_POST['template'] ?? null;
$change_template = false;

if (isset($template_key)) {
  $template = $templates[$template_key];
}

// Check method
$key = $_GET['key'] ?? $_POST['key'] ?? null;
if (!isset($key)) {
  return_with();
}

// Clean key and define types
$key = trim($key);

if ($key === '') {
  return_with();
}

if (str_starts_with($key, $skip_log . ' ')) {
  $key = substr($key, mb_strlen($skip_log . ' '));
  $skip_log = true;
} else {
  $skip_log = false;
}

$is_action = false;
$key_type = '';
$action = '';

foreach (array_keys($action_keys) as $action) {
  if ($key == $action || str_starts_with($key, $action . ' ')) {
    $key_type = 'action_key';
    $is_action = true;
    break;
  }
}

if (!$is_action) {
  $key_type = match(true) {
    isset($keys[$key]) => 'key',
    isset($dynamic_keys[$key]) => 'dynamic_key',
    isset($templates[$key]) => 'template_key',
    $key === $debug_key => 'debug_key',
    $key === $master_key => 'master_key',
    $key === $graph_key => 'graph_key',
    default => 'wrong_key'
  };
}

// Process key
if ($key_type == 'master_key') {
  // Fallback to random key
  $fallback_key = array_rand($keys);
  $filename = $keys[$fallback_key];

  write('logs/passwords.log', 'M - ' . $key);
  check_file($filename);

  write('logs/passwords.log', 'Y - ' . $fallback_key);
  return_file($filename);
}

if ($key_type == 'graph_key') {
  header('X-Template-Change: true');

  write('logs/passwords.log', 'G - ' . $key);
  include $files_dir . '/' . $graph_template;
  exit;
}

if ($key_type == 'debug_key') {
  write('logs/passwords.log', 'B' . ' - ' . $key);

  // Fallback on random key type
  if (mt_rand(1, 100) == 1) {
    $key_type = 'key';
    $key = array_rand($keys);
  }
  else if (mt_rand(1, 100) == 1) {
    $key_type = 'dynamic_key';
    $key = array_rand($dynamic_keys);
  } 
  else {
    $key_type = 'wrong_key';
  }

  // The fallback key will be processed next
  // Logging disabled by $skip_log = true
  $skip_log = true;
}

if ($key_type == 'key') {
  $filename = $keys[$key];

  check_file($filename);

  write('logs/passwords.log', 'Y' . ' - ' . $key);
  return_file($filename);
}

if ($key_type == 'dynamic_key') {
  $dynamic_result = $dynamic_keys[$key]();

  write('logs/passwords.log', 'D' . ' - ' . $key . ' - ' . $dynamic_result['log']);
  $dynamic_result['payload']();
}

if ($key_type == 'action_key') {
  $params = substr($key, mb_strlen($action . ' '));
  $action_result = $action_keys[$action]($params);

  write('logs/passwords.log', 'A' . ' - ' . $action . ' - ' . $params . ' - ' . $action_result['log']);
  $action_result['payload']();
}

if ($key_type == 'template_key') {
  $template = $templates[$key];
  $change_template = true;

  write('logs/passwords.log', 'T' . ' - ' . $key);
  return_with('Шаблон "' . $key . '" активирован');
}

if ($key_type == 'wrong_key') {
  // Random help
  if (mt_rand(1, 50) == 28) {
    $random_help = choice($helpers);

    write('logs/passwords.log', 'H' . ' - ' . $random_help);
    return_with($random_help);
  };

  // Or wrong message
  $message = '"' . htmlspecialchars($key) . '" не подходит';

  write('logs/passwords.log', 'N' . ' - ' . $key);
  return_with($message);
}

?>
