<?php
$modules = scandir(APP_ROOT .'/modules');
if($modules != '.' && $modules !='..' && $modules !='.htaccess' && $modules !='.gitignore')
{
	return $modules;
}