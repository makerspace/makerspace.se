#!/usr/bin/php
<?php
$drupal8     = __DIR__ .'/../Drupal8';
$public_html = __DIR__ .'/../public_html';

// Remove old core
system("rm -rf {$public_html}/core/");
system("rm -rf {$public_html}/index.php");

// Load new core
system("cp -r {$drupal8}/core/ {$public_html}/");
system("cp -r {$drupal8}/index.php {$public_html}/");

// Check if there is a diff in robots.txt
system("diff {$drupal8}/robots.txt {$public_html}/robots.txt");
