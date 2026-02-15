<?php
require_once 'config.php';

function return_with($message = '') {
  include 'template.html';
  exit;
}

// Check method
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['key'])) {
  return_with();
}

$key = trim($_GET['key']);
$is_set = isset($passwords[strtolower($key)]);

// Empty key
if (empty($key)) {
  return_with();
}

// Log key
$passwordsFile = $files_dir . '/passwords.txt';
$status = $is_set ? 'Y' : 'N';
$logEntry = date('Y-m-d H:i:s') . ' - ' . $status . ' - ' . $key . PHP_EOL;
file_put_contents($passwordsFile, $logEntry, FILE_APPEND | LOCK_EX);

// Lower for future
$key = strtolower($key);

// Len
if ($key == 'len') {
  return_with('Ключей: ' . count($passwords));
}

// Wrong key
if (!$is_set) {
  return_with('"' . htmlspecialchars($key) . '" не подходит');
}

$filename = $passwords[$key];
$filepath = $files_dir . $filename;

// No file
if (!file_exists($filepath)) {
  $passwordsFile = $files_dir . '/errors.txt';
  $logEntry = date('Y-m-d H:i:s') . ' - ' . $key . ' - ' . $filename . PHP_EOL;
  file_put_contents($passwordsFile, $logEntry, FILE_APPEND | LOCK_EX);

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
