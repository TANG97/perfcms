<?php

/*
	@author: Plato
	@modified: Artas
	@package: PerfCMS
	@year: 2012
*/
class PerfLang
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
	protected static $category = 'main';

	/*
	 * @function __construct Initalize language
	 */
	public function __construct() 
	{

		$this->get_languages();
		$user = new User;
		$settings = System::getSettings();
		if ($user->loged()) 
		{
			$this->set_language($user::$settings['lang']);
		}
		elseif(!$user->loged() && isset($_COOKIE['lang'])) 
		{
			$this->set_language($_COOKIE['lang']);
		}
		else
		{
			$this->set_language($settings['language']);
		}

		$dir = opendir(APP_SYS.'lang/'. self::$language);

		while ($file = readdir($dir)) {
			if ($file == '.' OR $file == '..' OR $file == 'php.ini') continue;

			$data = include(APP_SYS.'lang/'. self::$language .'/'.self::$category.'.php');


			self::$data = array_merge(self::$data, $data);
		}
	}

	/*
	 * @retrun string Browser language
	 */
	public function getBrowserLanguage() 
	{
		$config = System::getSettings();
		if ( ! $_SERVER['HTTP_ACCEPT_LANGUAGE'])
		{
			$language = $config['language'];
		}
		else
		{
			$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		}
		if ( ! in_array($language, array_keys($this->languages))) {
			$language = $config['language'];
		}
		return $language;
	}

	/*
	 * @param string $language ISO-name of language
	 * @return string self::$language Current language
	 */
	public function set_language($language) 
	{
		if ( ! in_array($language, array_keys($this->languages))) {
			$language = $this->getBrowserLanguage();
		}
		self::$language = $language;
		$_COOKIE['lang'] = $language;
	}

	/*
	 * @param string $key Key from language file
	 * @param string $category Language file with phrases
	 * @return string $data Translated key
	 */
	public static function get($key, $category = 'main') 
	{
		if (empty(self::$data[$key])) $data = $key;
		else $data = self::$data[$key];
		self::$category = $category;
		if (is_string($key)) {
			$lgr = include(APP_SYS.'lang/'. self::$language .'/'.$category.'.php');
			$data = $lgr[$key];
		}

		return $data;
	}

	protected function get_languages() 
	{
		$dir = opendir(APP_SYS.'lang');
		while ($lang = readdir($dir)) {
			if ( ! file_exists(APP_SYS.'lang/'. $lang .'/'.self::$category.'.php')) continue;
			$config = include(APP_SYS.'lang/'. $lang .'/'.self::$category.'.php');
			if (empty($config['lang_name'])) continue;
			$this->languages = array_merge($this->languages, array($lang => $config['lang_name']));
		}
	}
}
