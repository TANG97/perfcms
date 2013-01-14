<?php
class Widget extends CWidget
{
	public function getConfig($class = '')
	{
		if($class != '')
		{
			$ini = parse_ini_file(APP_ROOT.'/protected/widgets/'.$class.'/widget.ini');
			return $ini;
		}
	}
}