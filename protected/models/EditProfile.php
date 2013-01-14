<?php
/*
* EditProfile
* @package: PerfCMS
*/
class EditProfile extends CActiveRecord
{
	public $name;
	public $surname;
	public $gender;
	public $day;
	public $month;
	public $year;
	public $icq;
	public $phone;
	public $site;
	public $info;
	public $email;
	public $password;
	public $level;
	public $nick;
	public $ban_time;
	public $ban_text;
	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
	public function tableName()
	{
		return 'users';
	}
	
	public function primaryKey()
	{
		return 'id';
	}
}
