<?php
/*
* Ini Manager
* @package: PerfCMS
*/
if (!defined('_BR_'))
   define('_BR_',chr(13).chr(10));
   
class Ini 
{
    private static $filename;
	private static $arr = array();
    
    public function __construct($file = '')
    {
        if (!empty($file))
		{
			if (file_exists($file) && is_readable($file))
			{
				self::$filename = $file;
				$this->loadArray($file);
			}
		}
    }
    
    public function loadArray($filename)
    {
        $arr = parse_ini_file($filename, true);
		self::$arr = $arr;
		return $arr;
    }
	
	public function loadFromFile($filename)
    {
        return $this->loadArray($filename);
    }
    
    public function read($key, $path = '', $var = '')
    {
		if(!empty($path))
		{
			$path = APP_ROOT.'/'.str_replace('[var]', $var, $path);
			$this->loadFromFile($path);
		}
		$arr = self::$arr;
        if (isset($arr[$key]))
        {
            return $arr[$key];
        } else
            return false;
    }
    
    public function write($key, $value = '', $path = '')
    {
		if(!empty($path))
		{
			$path = APP_ROOT.'/'.$path;
			$this->loadFromFile($path);
		}
		$arr = self::$arr;
        self::$arr[$key] = $value;
    }
    
    //public function eraseSection($section)
    //{
        //if (isset(self::$arr))
            //unset(self::$arr);
    //}
    
    public function deleteKey($key)
    {
        if (isset(self::$arr[$key]))
            unset(self::$arr[$key]);
    }
    
    //public function readSections(&$array)
    //{
        //$array = array_keys(self::$arr);
        //return $array;
    //}
    
    public function showKeys()
    {
        if (isset(self::$arr))
        {
            $array = array_keys(self::$arr);
            return $array;
        }
        return false;
    }
	
    public function showValues()
    {
        if (isset(self::$arr))
        {
            $array = array_values(self::$arr);
            return $array;
        }
        return false;
    }
    
    public function updateFile()
    {
        $result = '';
        foreach(self::$arr as $key=>$value)
        {
                $result .= $key .' = "'.$value .'";'. _BR_;
        }
		$result .= _BR_;
		file_put_contents(self::$filename, $result);
		
		return true;
    }
    
    // public function __destruct()
    // {
		// if(!empty(self::$filename))	$this->updateFile();
    // }
}
