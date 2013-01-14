<?php
/*
* System class
* @package: PerfCMS
*/

class System extends PerfSystem
{
	public static function textarea($rows = 5, $cols = 25, $name = 'text', $value = '', $class = '', $id = 'area', $style = '')
	{  
		if(parent::browserType() == 'web')
		{
			Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/assets/js/bbtags.js');
			return '
			<div id="tagspanel" class="textarea">
			<a href="#" title="'. Lang::get('bold_text').'" id="bold" onclick="return bbtags(\'[b]\', \'[/b]\', \'bold\', \''.$id.'\')">'.System::image('bbpanel/bold.png').'<span class="tooltip"></span></a> 
			<a href="#" title="'. Lang::get('italic_text').'" id="italic" onclick="return bbtags(\'[i]\', \'[/i]\', \'italic\', \''.$id.'\')">'.System::image('bbpanel/italic.png').'</a>
			<a href="#" title="'. Lang::get('underline_text').'" id="underline" onclick="return bbtags(\'[u]\', \'[/u]\', \'underline\', \''.$id.'\')">'.System::image('bbpanel/underline.png').'</a>
			<a href="#" title="'. Lang::get('strike_text').'" id="strikethrough" onclick="return bbtags(\'[s]\', \'[/s]\', \'strikethrough\', \''.$id.'\')">'.System::image('bbpanel/strikethrough.png').'</a>
			<a href="#" title="'. Lang::get('quote_text').'" id="blockquote" onclick="return bbtags(\'[quote]\', \'[/quote]\', \'blockquote\', \''.$id.'\')">'.System::image('bbpanel/blockquote.png').'</a>
			<a href="#" title="'. Lang::get('spoiler_text').'" id="spoiler" onclick="return bbtags(\'[spoiler]\', \'[/spoiler]\', \'spoiler\', \''.$id.'\')">'.System::image('bbpanel/spoiler.png').'</a>
			<a href="#" title="'. Lang::get('color_text').'" id="color" onclick="return bbtags(\'[color=]\', \'[/color]\', \'color\', \''.$id.'\')">'.System::image('bbpanel/text_color.png').'</a>
			<a href="#" title="'. Lang::get('url_text').'" id="link" onclick="return bbtags(\'[url=http://]\', \'[/url]\', \'link\', \''.$id.'\')">'.System::image('bbpanel/insert_link.png').'</a>
			<a href="#" id="image" onclick="return bbtags(\'[img]\', \'[/img]\', \'image\', \''.$id.'\')">'.System::image('bbpanel/image.png').'</a>
			<a href="#" title="'. Lang::get('video_text').'" id="video" onclick="return bbtags(\'[video]\', \'[/video]\', \'video\', \''.$id.'\')">'.System::image('bbpanel/video.png').'</a>
			<a href="#" title="'.Lang::get('source_text').'" id="source" onclick="return bbtags(\'[source lang=]\', \'[/source]\', \'source\', \''.$id.'\')">'.System::image('bbpanel/script_code.png').'</a>
			</div>
			<textarea name="'.$name.'" rows="'.$rows.'" cols="'.$cols.'"'.(!empty($class) ? ' class="'.$class.'"' : null).(!empty($style) ? ' style="'.$style.'"' : null).' id="'.$id.'">'.(!empty($value) ? $value : null).'</textarea>';
		}
		else
		{
			return '[<a href="/help/codes">'.Lang::get('bb_codes').'</a> | <a href="/help/smiles">'.Lang::get('smiles').'</a> | <a href="/help/rules">'.Lang::get('rules').'</a> | <a href="/help/">'.Lang::get('help').'</a>]<br/>
			<textarea name="'.$name.'" rows="'.$rows.'" cols="'.$cols.'"'.(!empty($class) ? ' class="'.$class.'"' : null).(!empty($style) ? ' style="'.$style.'"' : null).' id="'.$id.'">'.(!empty($value) ? $value : null).'</textarea>';
		}
	}
	
	public static function pages()
	{
		$user = new User;
		$config = parent::getSettings();
		return (!$user->loged() ? $config['pts'] : $user::$settings['ames']);
	}
	
	public function backLink($class = '', $id = '', $style = '')
	{
		$refer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
		$host = System::Server('http_host');
		if($refer != null)
		{
			if(preg_match('/'.$host.'/i', $refer))
			{
				return '<a'.(!empty($class) ? ' class="'.$class.'"' : null).(!empty($id) ? ' id="'.$id.'"' : null).(!empty($style) ? ' style="'.$style.'"' : null).' href="'.$refer.'">'.Lang::get('back').'</a>';
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public static function getLocation($string = '/index::site_somewhere_location')
	{
		$location = explode('::', $string);
		$lang = new Lang;
		return '<a href="'.$location[0].'">'.$lang::get($location[1], 'locations').'</a>';
	}
	
	public static function notifications($section = false)
	{
		$db = PerfDb::init();
		if($section == 'mail')
		{
			$newInbox = $db->query("SELECT * FROM `mail` WHERE `who_id` = '".User::Id()."' AND `read` = '0'")->rowCount();
			return $newInbox;
		}
	}
}