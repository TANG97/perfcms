<?php
/*
 * PerfDb Database connection class
 * @link: http://perfcms.org.ua
 * @package: PerfCMS
 * @scince: 2.0
 */
class PerfDb 
{      
	protected static $instance; 
	
	public function __construct() 
	{
		self::init();
	} 
         
	public static function init() 
	{ 
		if(empty(self::$instance)) 
		{ 
			if(file_exists(APP_SYS.'data/ini/db.php'))
			{
				$db_info = include(APP_SYS.'data/ini/db.php'); 
				try 
				{                           
					self::$instance = new PDO("mysql:host=".$db_info['db_host'].';dbname='.$db_info['db_name'], $db_info['db_user'], $db_info['db_pass']); 
					self::$instance->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );   
					self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);  
					self::$instance->query('SET NAMES utf8'); 
				}   
				catch(PDOException $e) 
				{   
					echo $e->getMessage();   
				}
			}
			else
			{
				self::$instance = false;
			}
		} 
		return self::$instance; 
	}
}