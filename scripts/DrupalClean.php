#!/usr/bin/php
<?php
$db = new Mysqli('localhost', 'root', 'rQ?+BhD,Z9`*_L-bsDHMh2}Zm') or die(':(');;
//$db->select_db('external') or die(':((');
$db->select_db('makerspace.se') or die(':((');

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

/*
system('rm /home/chille/public_html/sites/default/files/js/*.js');
system('rm /home/chille/public_html/sites/default/files/js/*.js.gz');
system('rm /home/chille/public_html/sites/default/files/css/*.css');
system('rm /home/chille/public_html/sites/default/files/css/*.css.gz');
*/

system('rm -rf /home/chille/public_html/sites/default/files/php/');
// router
// sessions
// config_snapshop

