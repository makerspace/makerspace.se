#!/usr/bin/php
<?php
include(__DIR__ . '/../public_html/sites/default/settings.php');

$basedir = __DIR__ . '/../public_html/';

system("chown -R chille:staging {$basedir}");
system("chmod -R 755 {$basedir}");
system("chmod -R 775 {$basedir}/sites/default/files");
system("find {$basedir} -type d -exec chmod +s {} \;");
