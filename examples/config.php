<?php

// Example of config.php
// This file should be located at the root folder of project

require_once 'functions.php';

direct_access_gate('config.php', 'direct_access_gate');

// Define configuration

// NOTE: all 'file' values are should be placed in $files_dir
// and must be contain path relative to that directory

$files_dir = __DIR__ . '/files/';

$skip_log = 'skip_log';

$debug_key = 'debug_key';

$master_key = 'master_key';

$graph_key = 'graph_key';

$graph_template = 'file';

// Here file are described relative to $files_dir
$template = 'file';

// Here files are described relative to $files_dir
$templates = [
  'template' => 'file',
  // ...
];

$helpers = [
  'helper',
  // ...
];

// Here files are described relative to $files_dir
$passwords = [
  'key' => 'file',
  // ...
];

$dynamic_passwords = [
  'dynamic_key' => function() use ($passwords) {
    $log = 'log';
    $payload = function() { };

    return [
      'log' => $log,
      'payload' => $payload,
    ];
  },
  // ...
];

// Prefix Types:
// t - template
// h - helper
// k - key
// f - file
// d - dynamic key
// a - another
// m - meta

$meta = [
  ['f_file', 'k_key'],
  // ...
]

?>
