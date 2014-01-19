#!/usr/bin/php
<?php
$patches     = __DIR__ . '/../DrupalCorePatches';
$public_html = __DIR__ . '/../';

system("git apply --directory {$public_html} {$patches}/message.txt");
system("git apply --directory {$public_html} {$patches}/search.txt");
//system("git apply --directory {$public_html} {$patches}/mail.txt");
