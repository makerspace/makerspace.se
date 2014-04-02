#!/usr/bin/php
<?php
include(__DIR__ . '/../public_html/sites/default/settings.php');

$date = date('Y-m-dTH:i:s');
$dumpfile = __DIR__ . "/../Database/{$date}.sql";

echo "mysqldump --host={$databases['default']['default']['host']} --user={$databases['default']['default']['username']} --password={$databases['default']['default']['password']} {$databases['default']['default']['database']} > {$dumpfile}\n";
