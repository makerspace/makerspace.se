#!/usr/bin/php
<?php
include(__DIR__ . '/../public_html/sites/default/settings.php');

$basedir = __DIR__ . '/../public_html/';

system("chown -R staging:staging {$basedir}");
system("chmod -R 664 {$basedir}");
system("chmod -R 664 {$basedir}/sites/default/files");
system("find {$basedir} -type d -exec chmod +xs {} \;");
