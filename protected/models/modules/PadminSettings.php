<?php
class PadminSettings extends CActiveRecord
{
	public $pts;
	public $open_site;
	public $open_reg;
	public $timezone;
	public $language;
	public $access_site;
	public $description;
	public $keywords;
	public $wap_theme;
	public $web_theme;
	public $touch_theme;
	public $default_type;
	public $active_switch;
	public $file_types;
	public $copyright;
	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
	public function tableName()
	{
		return 'system';
	}
	
	public function primaryKey()
	{
		return 'system';
	}
}
