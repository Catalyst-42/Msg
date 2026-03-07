<?php
require_once 'config.php';
// require_once 'functions.php';

// Check method
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['key'])) {
  return_with();
}

// Check template if exists
if (isset($_GET['template']) && isset($templates[$_GET['template']])) {
  $template = $templates[$_GET['template']];
}

// Clean key and define types
$key = trim($_GET['key']);
$key_types = [];

if (empty($key)) {
  array_push($key_types, 'empty');
}

if (str_starts_with($key, $skip_log)) {
  $key = substr($key, strlen($skip_log));
  array_push($key_types, 'skip_log');
}

if ($key === $master_key) {
  array_push($key_types, 'master');
}

if ($key === $graph_key) {
  array_push($key_types, 'graph');
}

if ($key === $debug_key) {
  array_push($key_types, 'debug');
}

if (isset($passwords[$key])) {
  array_push($key_types, 'key');
}

else if (isset($dynamic_passwords[$key])) {
  array_push($key_types, 'dynamic');
}

else if (isset($templates[$key])) {
  array_push($key_types, 'template');
}

else {
  array_push($key_types, 'wrong');
}

// Process key
$skip_log = in_array('skip_log', $key_types);
$change_template = false;

if (in_array('empty', $key_types)) {
  return_with();
}

if (in_array('master', $key_types)) {
  // Fallback to random key
  $fallback_key = array_rand($passwords);
  $filename = $passwords[$fallback_key];

  check_file($filename);

  write('logs/passwords.log', 'M - ' . $fallback_key);
  return_file($filename);
}

if (in_array('graph', $key_types)) {

  write('logs/passwords.log', 'G - ' . $key);
  include $files_dir . '/' . $graph_template;
  exit;
}

if (in_array('debug', $key_types)) {
  // Fallback on random key type
  if (mt_rand(1, 100) == 1) {
    $key_types = ['key'];
    $key = array_rand($passwords);
  }
  else if (mt_rand(1, 100) == 1) {
    $key_types = ['dynamic'];
    $key = array_rand($dynamic_passwords);
  } 
  else {
    $key_types = ['wrong'];
  }

  // The fallback key will be processed next
  // Logging disabled by $skip_log = true
  $skip_log = true;
}

if (in_array('key', $key_types)) {
  $filename = $passwords[$key];

  check_file($filename);

  write('logs/passwords.log', 'Y' . ' - ' . $key);
  return_file($filename);
}

if (in_array('dynamic', $key_types)) {
  $dynamic_result = $dynamic_passwords[$key]();

  write('logs/passwords.log', 'D' . ' - ' . $key . ' - ' . $dynamic_result['log']);
  $dynamic_result['payload']();
}

if (in_array('template', $key_types)) {
  $template = $templates[$key];
  $change_template = true;

  write('logs/passwords.log', 'T' . ' - ' . $key);
  return_with('Шаблон "' . $key . '" активирован');
}

if (in_array('wrong', $key_types)) {
  // Random help
  if (mt_rand(1, 50) == 28) {
    $random_help = $helpers[mt_rand(0, count($helpers) - 1)];

    write('logs/passwords.log', 'H' . ' - ' . $random_help);
    return_with($random_help);
  };

  // Or wrong message
  $message = '"' . htmlspecialchars($key) . '" не подходит';

  write('logs/passwords.log', 'N' . ' - ' . $key);
  return_with($message);
}

?>
