<?php
require_once 'config.php';

function return_with($message = '') {
  include 'template.html';
  exit;
}

function write($file = '', $message = '') {
  global $files_dir;

  $file = $files_dir . '/' . $file;
  $logEntry = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
  file_put_contents($file, $logEntry, FILE_APPEND | LOCK_EX);
}

// Check method
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['key'])) {
  return_with();
}

$key = trim($_GET['key']);
$is_set = isset($passwords[$key]);

// Empty key
if (empty($key)) {
  return_with();
}

// Log key
$status = $is_set ? 'Y' : 'N';
$log = $status . ' - ' . $key;
write('logs/passwords.txt', $log);

// Dyamic keys
// Keys
if ($key == 'keys') {
  return_with('Ключей: ' . count($passwords));
}

// Helpers
if ($key == 'helpers') {
  return_with('Подсказок: ' . count($helpers));
}

// Help
if ($key == 'help') {
  $message = $helpers[mt_rand(0, count($helpers) - 1)];
  return_with($message);
}

// Wrong key
if (!$is_set) {
  $message = '"' . htmlspecialchars($key) . '" не подходит';

  // Generate helper if you are lucky
  if (mt_rand(1, 200) == 42) {
    $message = $helpers[mt_rand(0, count($helpers) - 1)];
  };

  return_with($message);
}

$filename = $passwords[$key];
$filepath = $files_dir . $filename;

// No file
if (!file_exists($filepath)) {
  $log = $key . ' - ' . $filename;
  write('logs/errors.txt', $log);
  return_with(htmlspecialchars($filename) . ' не найден');
}

// Download file
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Content-Length: ' . filesize($filepath));
header('Pragma: no-cache');
header('Expires: 0');
readfile($filepath);
?>
