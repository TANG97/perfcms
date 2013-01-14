<?php
/*
* SimpleWidget class
* @package: PerfCMS
*/
class File
{
	public $file;
	
	public function __construct($filename, $mode='c+', $charset='UTF-8')
	{
		$this->file = fopen($filename, $mode);
	}
	
	public function read()
	{
		$file = file_get_contents($this->file);
		return $file;
	}
	
	public function write($contents)
	{
		fwrite($this->file, $contents);
	}
	
	public function close()
	{
		fclose($this->file);
	}
}
