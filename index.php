<?php
/*
 * The Core Initialize
 * @package: PerfCMS
 */
#######
// $start_time = microtime(); 
// $start_array = explode(" ",$start_time); 
// $start_time = $start_array[1] + $start_array[0];
#######

/*
 * System constantes
 */ 
// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
// System
define('APP_ROOT', realpath(dirname(__FILE__)));
define('APP_SYS', APP_ROOT .'/system/');
// Start of sessions
session_start();
mb_internal_encoding('UTF-8');
/*
 * Intitalize Yii framework and System configuration
 */
$yiiInitFile = APP_SYS .'/yii.php';
$globalSystemConfig = APP_ROOT .'/protected/config/main.php';
// Require Yii framework

// check configs. if not exists go install
if(!file_exists(APP_SYS.'/data/ini/db.php'))
{
	header('location: /install/');
	exit;
}

require_once($yiiInitFile);
// Initialize system
Yii::createWebApplication($globalSystemConfig)->run();
################
// $end_time = microtime(); 
// $end_array = explode(" ",$end_time); 
// $end_time = $end_array[1] + $end_array[0]; 
// $time = $end_time - $start_time; 
#############

// echo('<div style="background: #fff; font-size:12px; padding: 2px; border: dashed 1px red;">');
// echo("Сторінка згенерована за ".round($time, 2)." сек.<br/>");
// echo("Використано пам'яті ".round(((memory_get_usage()/1024)/1024), 3)." MiB");
// echo('</div>');
