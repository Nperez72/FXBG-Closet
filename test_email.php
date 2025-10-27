<?php

require_once('email.php');

$results = emailAdmins(
    'user',
    'local mail test',
    "Hello admins,\nThis is a local test from XAMPP.\nTime: " . date('c')
);

header('Content-Type: application/json');
echo json_encode([
  'results' => $results,
  'php_ini' => php_ini_loaded_file(),
  'smtp' => ini_get('SMTP'),
  'smtp_port' => ini_get('smtp_port'),
  'sendmail_from' => ini_get('sendmail_from'),
], JSON_PRETTY_PRINT);
