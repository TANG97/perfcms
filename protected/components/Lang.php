<?php
/*
* System class
* @package: PerfCMS
*/
class Lang
{
	public static function get($key = '', $category = 'main', $params = '')
	{
		return Yii::t($category, $key, $params);
	}
}
