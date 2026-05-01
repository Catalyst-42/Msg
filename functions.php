<?php

direct_access_gate('functions.php', 'f(x, y, z) = cos(x)*sin(y) + cos(y)*sin(z) + cos(z)*sin(x) = 0');

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
  global $files_dir, $change_template, $template;

  $is_ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') == 'XMLHttpRequest';

  if ($is_ajax) {
    if ($change_template) {
      header('X-Template-Change: true');
    }

    echo $message;
    exit;
  }

  include $files_dir . '/' . $template;
  exit;
}

function check_file($filename) {
  global $files_dir;
  $filepath = $files_dir . $filename;

  // Stub on missing file
  if (!file_exists($filepath)) {
    write('logs/errors.log', 'F' . ' - ' . $filename);
    return_with(htmlspecialchars(basename($filename)) . ' не найден');
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

// Helpers
function format_bytes($bytes, $lang = 'ru') {
  if ($lang == 'ru') {
    $units = ['Б', 'КБ', 'МБ', 'ГБ'];
  } else {
    $units = ['B', 'KB', 'MB', 'GB'];
  }

  $index = 0;

  while ($bytes >= 1024 && $index < count($units) - 1) {
    $bytes /= 1024;
    $index++;
  }

  return round($bytes) . ' ' . $units[$index];
}

function choice($array) {
  return $array[mt_rand(0, count($array) - 1)];
}

function is_natural($value): bool {
  $int = filter_var($value, FILTER_VALIDATE_INT);
  return $int !== false && $int > 0;
}

// Dynamic function helpers
function create_response($message,  $log = null) {
  if ($log == null) {
    $log = $message;
  }

  return [
    'log' => $log,
    'payload' => function () use ($message) {
      return_with($message);
    }
  ];
}

function redirect($url) {
  return [
    'log' => $url,
    'payload' => function () use ($url) {
      $is_ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') == 'XMLHttpRequest';

      if ($is_ajax) {
        header('X-Redirect-To: ' . $url);
        echo '';
        exit;
      }

      else {
        header('Location: ' . $url);
        exit;
      }
    }
  ];
}

?>
