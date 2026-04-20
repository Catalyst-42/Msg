<?php
require_once 'config.php';
// require_once 'functions.php';

// Check template if exists
$template_key = $_GET['template'] ?? $_POST['template'] ?? null;
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
$key_types = [];

if ($key === '') {
  array_push($key_types, 'empty');
}

if (str_starts_with($key, $skip_log)) {
  $key = substr($key, strlen($skip_log));
  array_push($key_types, 'skip_log');
}

if (str_starts_with($key, $type_key)) {
  $key = substr($key, strlen($type_key));
  array_push($key_types, 'check_type');
}

if (str_starts_with($key, $apricot_add)) {
  $key = substr($key, strlen($apricot_add));
  array_push($key_types, 'apricot_add');
}

if (str_starts_with($key, $apricot_sub)) {
  $key = substr($key, strlen($apricot_sub));
  array_push($key_types, 'apricot_sub');
}

if ($key === $master_key) {
  array_push($key_types, 'master_key');
}

else if ($key === $graph_key) {
  array_push($key_types, 'graph_key');
}

else if ($key === $debug_key) {
  array_push($key_types, 'debug_key');
}

else if (isset($keys[$key])) {
  array_push($key_types, 'key');
}

else if (isset($dynamic_keys[$key])) {
  array_push($key_types, 'dynamic_key');
}

else if (isset($templates[$key])) {
  array_push($key_types, 'template_key');
}

else {
  array_push($key_types, 'wrong_key');
}

// Process key
$skip_log = in_array('skip_log', $key_types);
$change_template = false;

if (in_array('empty', $key_types)) {
  return_with();
}

if (in_array('check_type', $key_types)) {
  write('logs/passwords.log', 'D - check - ' . $key);
  $key_types = array_diff($key_types, ['check_type']);

  return_with('"' . $key .'"' . ' is ' . join(', ', $key_types));
}

if (in_array('apricot_add', $key_types)) {
  write('logs/passwords.log', 'D - ap add - ' . $key);
  if (!is_numeric($key) || (int)$key != $key || (int)$key <= 0) {
    return_with('Invalid value');
  }

  $apricots = $files_dir . 'logs/apricots.log';
  $message = (int)@file_get_contents($apricots);

  if ($message + $key > $apricot_max) {
    return_with('Apricots doesn\'t fit in warehouse');
  }

  $message += $key;
  file_put_contents($apricots, $message);
  return_with("Apricots: " . number_format($message));
}

if (in_array('apricot_sub', $key_types)) {
  write('logs/passwords.log', 'D - ap sub - ' . $key);
  if (!is_numeric($key) || (int)$key != $key || (int)$key <= 0) {
    return_with('Invalid value');
  }

  $apricots = $files_dir . 'logs/apricots.log';
  $message = (int)@file_get_contents($apricots);

  if ($message - $key < 0) {
    return_with('Apricot number can not be negative');
  }

  $message -= $key;
  file_put_contents($apricots, $message);
  return_with("Apricots: " . number_format($message));
}

if (in_array('master_key', $key_types)) {
  // Fallback to random key
  $fallback_key = array_rand($keys);
  $filename = $keys[$fallback_key];

  write('logs/passwords.log', 'M - ' . $key);
  check_file($filename);

  write('logs/passwords.log', 'Y - ' . $fallback_key);
  return_file($filename);
}

if (in_array('graph_key', $key_types)) {
  header('X-Template-Change: true');

  write('logs/passwords.log', 'G - ' . $key);
  include $files_dir . '/' . $graph_template;
  exit;
}

if (in_array('debug_key', $key_types)) {
  write('logs/passwords.log', 'B' . ' - ' . $key);

  // Fallback on random key type
  if (mt_rand(1, 100) == 1) {
    $key_types = ['key'];
    $key = array_rand($keys);
  }
  else if (mt_rand(1, 100) == 1) {
    $key_types = ['dynamic_key'];
    $key = array_rand($dynamic_keys);
  } 
  else {
    $key_types = ['wrong_key'];
  }

  // The fallback key will be processed next
  // Logging disabled by $skip_log = true
  $skip_log = true;
}

if (in_array('key', $key_types)) {
  $filename = $keys[$key];

  check_file($filename);

  write('logs/passwords.log', 'Y' . ' - ' . $key);
  return_file($filename);
}

if (in_array('dynamic_key', $key_types)) {
  $dynamic_result = $dynamic_keys[$key]();

  write('logs/passwords.log', 'D' . ' - ' . $key . ' - ' . $dynamic_result['log']);
  $dynamic_result['payload']();
}

if (in_array('template_key', $key_types)) {
  $template = $templates[$key];
  $change_template = true;

  write('logs/passwords.log', 'T' . ' - ' . $key);
  return_with('Шаблон "' . $key . '" активирован');
}

if (in_array('wrong_key', $key_types)) {
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
