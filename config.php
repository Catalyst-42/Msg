<?php
$filename = basename($_SERVER['SCRIPT_FILENAME'] ?? '');

// Exit on direct access
if ($filename === 'config.php') {
  header('Content-Type: application/json');
  echo json_encode(['msg' => 'How easy']);
  exit;
}

// Define configuration
$files_dir = __DIR__ . '/files/';
$passwords = [
  'key' => 'file.txt',
];
?>
