#!/usr/bin/php
<?php
include(__DIR__ . '/../public_html/sites/default/settings.php');

$host = $databases['default']['default']['host'];
$user = $databases['default']['default']['username'];
$pass = $databases['default']['default']['password'];
$db   = $databases['default']['default']['database'];

$date = date('Y-m-dTH:i:s');
$backupfile = __DIR__ . "/../Database/{$date}.sql";

system("mysqldump --host={$host} --user={$user} --password={$pass} {$db} > {$backupfile}");
