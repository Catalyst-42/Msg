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

// Empty key
if (empty($key)) {
  return_with();
}

// Log key
$passwordsFile = $files_dir . '/passwords.txt';
$logEntry = date('Y-m-d H:i:s') . ' - ' . $key . PHP_EOL;
file_put_contents($passwordsFile, $logEntry, FILE_APPEND | LOCK_EX);

// Wrong key
if (!isset($passwords[$key])) {
  return_with('"' . htmlspecialchars($key) . '" не подходит');
}

$filename = $passwords[$key];
$filepath = $files_dir . $filename;

// No file
if (!file_exists($filepath)) {
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
