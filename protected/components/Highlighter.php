<?php

class Highlighter
{
	private $source;
	private $lang;
	public $cssOptions = array();
	
	public function __construct($lang, $source)
	{
		$this->source = $this->$lang($source);
	}
	
	public function php($source)
	{
		$code = htmlSpecialChars_decode($source);
		$code = str_replace(array('<br />', '&#039;'), array('', '\''), $code);
		$code = trim($code);
		$code = highlight_string($code);
		$code = str_replace(array('<code>', '</code>'), array('', ''), $code);
		$this->source = $code;
	}
	
	public static function availableLangs()
	{
		return array('php', 'html', 'xml', 'css');
	}
	
	public function view()
	{
		return $this->source;
	}
}