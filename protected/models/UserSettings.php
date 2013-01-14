<?php
/*
* UserSettings
* @package: PerfCMS
*/
class UserSettings extends CActiveRecord
{
	public $fast_mess;
	public $lang;
	public $signature;
	public $timezone;
	public $ames;
	public $theme;
	public $web_theme;
	public $touch_theme;
	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
	public function tableName()
	{
		return 'settings';
	}
	
	public function primaryKey()
	{
		return 'user_id';
	}
}
