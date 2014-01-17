#!/usr/bin/php
<?php
include(__DIR__ . '/../public_html/sites/default/settings.php');

$db = new Mysqli($databases['default']['default']['host'], $databases['default']['default']['username'], $databases['default']['default']['password']) or die(':(');;
$db->select_db($databases['default']['default']['database']) or die(':((');

$db->query('TRUNCATE TABLE cache');
$db->query('TRUNCATE TABLE cache_block');
$db->query('TRUNCATE TABLE cache_bootstrap');
$db->query('TRUNCATE TABLE cache_ckeditor');
$db->query('TRUNCATE TABLE cache_config');
$db->query('TRUNCATE TABLE cache_field');
$db->query('TRUNCATE TABLE cache_menu');
$db->query('TRUNCATE TABLE cache_path');
$db->query('TRUNCATE TABLE cache_tags');
$db->query('TRUNCATE TABLE cache_toolbar');
$db->query('TRUNCATE TABLE cache_views_info');

// Should any of the follwing be cleaned?
// router
// sessions
// config_snapshop
//system('rm /home/chille/public_html/sites/default/files/js/*');
//system('rm /home/chille/public_html/sites/default/files/css/*');

system('rm -rf /home/chille/public_html/sites/default/files/php/');
