<?php
$config= require './Public/Config/config.ini.php';
$web_config=array(
'TOKEN_ON'=>true,
'URL_MODEL'             => 0,
'TMPL_ACTION_ERROR'     => 'Public:error', 
'TMPL_ACTION_SUCCESS'   => 'Public:success', 
'TAGLIB_PRE_LOAD' => 'dwz',
'URL_ROUTER_ON'   => true,
 'URL_CASE_INSENSITIVE' =>true,
);
return array_merge($config,$web_config);
?>