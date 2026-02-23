<?php

direct_access_gate('functions.php', 'f(x) = x^2 + 2x + 1');

function direct_access_gate($filename, $message) {
    $active_file = basename($_SERVER['SCRIPT_FILENAME'] ?? '');

    // Exit on direct access
    if ($active_file === $filename) {
      header('Content-Type: application/json');
      echo json_encode(['msg' => $message]);
      exit;
    }
}

function write($file = '', $message = '') {
  global $files_dir, $skip_log;

  if ($skip_log) {
    return;
  }

  $file = $files_dir . '/' . $file;
  $logEntry = date('Y-m-d H:i:s') . ' - ' . $_SERVER['REMOTE_ADDR'] . ' - ' . $message . PHP_EOL;
  file_put_contents($file, $logEntry, FILE_APPEND | LOCK_EX);
}

function return_with($message = '') {
  include 'template.html';
  exit;
}

function check_file($filename) {
  global $files_dir;
  $filepath = $files_dir . $filename;

  // Stub on missing file
  if (!file_exists($filepath)) {
    write('logs/errors.log', 'F' . ' - ' . $filename);
    return_with(htmlspecialchars($filename) . ' не найден');
  }
}

function return_file($filename) {
  global $files_dir;
  $filepath = $files_dir . $filename;

  // Download file
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
  header('Content-Length: ' . filesize($filepath));
  header('Pragma: no-cache');
  header('Expires: 0');
  readfile($filepath);
  exit;
}

?>
