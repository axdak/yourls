<?php
$config= require './Public/Config/config.ini.php';
$admin_config=array(
	'URL_MODEL'             => 0,
	'TMPL_ACTION_ERROR'     => 'Public:error', 
    'TMPL_ACTION_SUCCESS'   => 'Public:success', 
);
return array_merge($config,$admin_config);
?>