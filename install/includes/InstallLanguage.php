<?php

class InstallLanguage
{
	/*
	 * @var array $languages Array with all languages
	 */
	protected $languages = array();
	
	/*
	 * @var string $language Current language
	 */
	protected static $language = 'en';
	
	/*
	 * @var array $data Array with language phrases
	 */
	protected static $data = array();
	
	/*
	 * @var string $category Filename with phrases, default = main
	 */
	protected static $category = 'install';

	/*
	 * @function __construct Initalize language
	 */
	public function __construct($lang) 
	{
		self::$language = $lang;
		if(file_exists(root.'/protected/messages/'. self::$language))
		{
			$dir = opendir(root.'/protected/messages/'. self::$language);
		}
		else
		{
			$dir = opendir(root.'/protected/messages/'. self::$language);
		}
		while ($file = readdir($dir)) {
			if ($file == '.' OR $file == '..' OR $file == 'php.ini') continue;
			$data = include(root.'/protected/messages/'. self::$language .'/'.self::$category.'.php');
			self::$data = array_merge(self::$data, $data);
		}
	}

	/*
	 * @param string $key Key from language file
	 * @return string $data Translated key
	 */
	public static function get($key, $category = 'install') 
	{
		if (empty(self::$data[$key])) $data = $key;
		else $data = self::$data[$key];
		if (is_string($key)) {
			$lgr = include(root.'/protected/messages/'. self::$language .'/'.$category.'.php');
			$data = $lgr[$key];
		}

		return $data;
	}	
}