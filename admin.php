<?php 
if(is_dir(dirname(__FILE__).'/Install'))
{
	if(!file_exists(dirname(__FILE__).'/Install/install_lock.txt'))
	{
		header('Location:Install/index.php');return;
	}
}
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//define('APP_DEBUG',TRUE); 
define('APP_NAME', 'Admin'); 
define('APP_PATH', './Admin/'); 
require './Core/Core.php';