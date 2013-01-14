<?php
/*
* Filters
* @package: PerfCMS
*/
class Filters
{
	/*
	 * @return string Escped string
	 */
	public static function input($var) 
	{
		$db = PerfDb::init();
		return htmlspecialchars(substr($db->quote(trim($var)), 1, -1), ENT_QUOTES, 'UTF-8');
	}
	
	/*
	 * @param string $var Uncoded user password
	 * @return string Encoded password
	 */
	public static function crypt($var) 
	{
		return md5(base64_encode($var) .'_PerfCMS_');
	}
	
	/*
	 * @param string $var
	 * @return int
	 */
	public static function num($var)
	{
		return abs(intval($var));
	}
	
	public static function output($var)
	{
		return self::smiles(self::bbcodes(nl2br($var)));
	}
	
	public static function smiles($var)
	{
		$smiles = array(
			':)' => '<img src="/design/images/smiles/smile.gif" alt="#" />',
			':-)' => '<img src="/design/images/smiles/smile.gif" alt="#" />',
			':(' => '<img src="/design/images/smiles/sad.gif" alt="#" />',
			':-(' => '<img src="/design/images/smiles/sad.gif" alt="#" />',
			';-(' => '<img src="/design/images/smiles/cray.gif" alt="#" />',
			';(' => '<img src="/design/images/smiles/cray.gif" alt="#" />',
			':D' => '<img src="/design/images/smiles/biggrin.gif" alt="#" />',
			':-D' => '<img src="/design/images/smiles/biggrin.gif" alt="#" />',
			':P' => '<img src="/design/images/smiles/blum1.gif" alt="#" />',
			':-P' => '<img src="/design/images/smiles/blum1.gif" alt="#" />',
			':|' => '<img src="/design/images/smiles/bad.gif" alt="#" />',
			'8)' => '<img src="/design/images/smiles/cool.gif" alt="#" />',
			':))' => '<img src="/design/images/smiles/i_am_so_happy.gif" alt="#" />',
			'%)' => '<img src="/design/images/smiles/wacko2.gif" alt="#" />',
			'%P' => '<img src="/design/images/smiles/wacko1.gif" alt="#" />',
			'[:)' => '<img src="/design/images/smiles/music.gif" alt="#" />',
			':@' => '<img src="/design/images/smiles/yahoo.gif" alt="#" />',
			':E' => '<img src="/design/images/smiles/crazy.gif" alt="#" />',
			'xD' => '<img src="/design/images/smiles/sarcastic.gif" alt="#" />',
			'>D' => '<img src="/design/images/smiles/diablo.gif" alt="#" />',
			':lol:' => '<img src="/design/images/smiles/lol.gif" alt="#" />',
			':rofl:' => '<img src="/design/images/smiles/rofl.gif" alt="#" />',
			':blush:' => '<img src="/design/images/smiles/blush.gif" alt="#" />',
			':bye:' => '<img src="/design/images/smiles/bye2.gif" alt="#" />',
			':hi:' => '<img src="/design/images/smiles/hi.gif" alt="#" />',
			':dance:' => '<img src="/design/images/smiles/dance.gif" alt="#" />',
			':dash:' => '<img src="/design/images/smiles/dash2.gif" alt="#" />',
			':beer:' => '<img src="/design/images/smiles/drinks.gif" alt="#" />',
			':gamer:' => '<img src="/design/images/smiles/gamer.gif" alt="#" />',
			':angel:' => '<img src="/design/images/smiles/girl_angel.gif" alt="#" />',
			':heart:' => '<img src="/design/images/smiles/heart.gif" alt="#" />',
			':good:' => '<img src="/design/images/smiles/good.gif" alt="#" />',
			':hang:' => '<img src="/design/images/smiles/hang1.gif" alt="#" />',
			':ireful:' => '<img src="/design/images/smiles/ireful.gif" alt="#" />',
			':mad:' => '<img src="/design/images/smiles/mad.gif" alt="#" />',
			':mail:' => '<img src="/design/images/smiles/mail1.gif" alt="#" />',
			':love:' => '<img src="/design/images/smiles/man_in_love.gif" alt="#" />',
			':mocking:' => '<img src="/design/images/smiles/mocking.gif" alt="#" />',
			':no:' => '<img src="/design/images/smiles/nea.gif" alt="#" />',
			':pardon:' => '<img src="/design/images/smiles/pardon.gif" alt="#" />',
			':head:' => '<img src="/design/images/smiles/scratch_one-s_head.gif" alt="#" />',
			'O_o' => '<img src="/design/images/smiles/shok.gif" alt="#" />',
			'O_O' => '<img src="/design/images/smiles/shok.gif" alt="#" />',
			'o_O' => '<img src="/design/images/smiles/shok.gif" alt="#" />',
			':O' => '<img src="/design/images/smiles/shok.gif" alt="#" />',
			':sorry:' => '<img src="/design/images/smiles/sorry.gif" alt="#" />',
			':unknown:' => '<img src="/design/images/smiles/unknown.gif" alt="#" />',
			':yes:' => '<img src="/design/images/smiles/yes.gif" alt="#" />',
			':kiss:' => '<img src="/design/images/smiles/kiss.gif" alt="#" />',
			':girl_kiss:' => '<img src="/design/images/smiles/kiss3.gif" alt="#" />',
			);
		$var = strtr($var, $smiles);
		return $var;
	}
	
	public static function bbcodes($var)
	{
		$var = preg_replace('/\[s\](.+)\[\/s\]/isU', '<s>$1</s>', $var);
		$var = preg_replace('/\[b\](.+)\[\/b\]/isU', '<b>$1</b>', $var);
		$var = preg_replace('/\[u\](.+)\[\/u\]/isU', '<span style="text-decoration: underline;">$1</span>', $var);
		$var = preg_replace('/\[i\](.+)\[\/i\]/isU', '<i>$1</i>', $var);
		$var = preg_replace('/\[color=(.+)\](.+)\[\/color\]/isU', '<span style="color: $1;">$2</span>', $var);
		$var = preg_replace('/\[acronym=(.+)\](.+)\[\/acronym\]/isU', '<abbr style="border-bottom: dashed 1px;" title="$1">$2</abbr>', $var);
		$var = preg_replace('/\[quote\](.+)\[\/quote\]/isU', '<div class="quote">$1</div>', $var);
		$var = preg_replace_callback('/\[source lang=(php|html|css|javascript|sql|xml|ruby|perl|mysql|java|python|sh|cpp|abap|diff|dtd|vbscript)\](.+)\[\/source\]/isU', 'self::TextHighlight', $var);
		$var = preg_replace_callback("/\[url=(https?:\/\/.+?)\](.+?)\[\/url\]|(https?:\/\/([a-zA-Zа-яА-Я0-9\.\/\[\]\#\;\&\_\-\)\(\:]*))/iu", 'self::LinkParser', $var);
		$var = preg_replace('/([a-zA-Z0-9\_\-\.]*)\@([a-zA-Z0-9\_\-\.]*)\.([a-z]*)\b/i', '<a href="mailto:$1@$2.$3">$1@$2.$3</a>', $var);
		$var = preg_replace_callback('/\[spoiler\](.+)\[\/spoiler\]/i', 'self::ViewSpoiler', $var);
		$var = preg_replace_callback('/\[spoiler=(.+)\](.+)\[\/spoiler\]/i', 'self::ViewSpoiler', $var);
		$var = preg_replace('/\[video](.+)\[\/video\]/i', '<iframe class="youtube-player" type="text/html" width="480" height="385" src="http://www.youtube.com/embed/$1" frameborder="0">
		</iframe>', $var);
		return $var;
	}
	
	private static function TextHighlight($source)
	{
		$code = new CTextHighlighter;
		$code->language = $source[1];
		$source[2] = trim($source[2]);
		$source[2] = htmlspecialchars_decode($source[2]);
		$source[2] = str_replace(array('<br />', '&#039;'), array('', '\''), $source[2]);
		return '<div class="code">'.$code->highlight($source[2]).'</div>';
	}
	
	private static function ViewSpoiler($source)
	{
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/assets/js/spoiler.js');

		return '<div class="spoiler"><a id="spoilerlink" href="#'.substr(md5($source[1]), 0, 8).'" onclick="spoiler(\''.substr(md5($source[1]), 0, 8).'\')">'.Lang::get('spoiler').'</a><br/>
		<div style="display:none;" id="'.mb_substr(md5($source[1]), 0, 8).'">'.$source[1].'</div></div>';
	}
	
	private static function LinkParser($linkInfo)
	{
		if(!$linkInfo[2])
		{
			return '<a target="_blank" href="'.$linkInfo[0].'">'.$linkInfo[0].'</a>';
		}
		else
		{
			return '<a target="_blank" href="'.$linkInfo[1].'">'.$linkInfo[2].'</a>';
		}
	}
	
	public static function viewTime($timestamp, $values = array( true , 'd', 'm', 'Y', 'H', 'i'))
	{
		if(empty($timestamp))
		{
			$timestamp = time();
		}
		$settings = array_values($values);
		$lang = new Lang;
		
		if($settings[0] == true)
		{
			if(date('m', $timestamp) == 1)
			{
				$settings[2] = $lang::get('date_january', 'date');
			}
			elseif(date('m', $timestamp) == 2)
			{
				$settings[2] = $lang::get('date_february', 'date');
			}
			elseif(date('m', $timestamp) == 3)
			{
				$settings[2] = $lang::get('date_march', 'date');
			}
			elseif(date('m', $timestamp) == 4)
			{
				$settings[2] = $lang::get('date_april', 'date');
			}
			elseif(date('m', $timestamp) == 5)
			{
				$settings[2] = $lang::get('date_may', 'date');
			}
			elseif(date('m', $timestamp) == 6)
			{
				$settings[2] = $lang::get('date_june', 'date');
			}
			elseif(date('m', $timestamp) == 7)
			{
				$settings[2] = $lang::get('date_july', 'date');
			}
			elseif(date('m', $timestamp) == 8)
			{
				$settings[2] = $lang::get('date_august', 'date');
			}
			elseif(date('m', $timestamp) == 9)
			{
				$settings[2] = $lang::get('date_september', 'date');
			}
			elseif(date('m', $timestamp) == 10)
			{
				$settings[2] = $lang::get('date_october', 'date');
			}
			elseif(date('m', $timestamp) == 11)
			{
				$settings[2] = $lang::get('date_november', 'date');
			}
			elseif(date('m', $timestamp) == 12)
			{
				$settings[2] = $lang::get('date_december', 'date');
			}
		}
		$dateView = ($settings[0] == true ? $settings[1].' '.$settings[2].' '.$settings[3].',' : $settings[1].'.'.$settings[2].'.'.$settings[3].',');
		$date = date($settings[1].'.'.$settings[2].'.'.$settings[3], $timestamp);
		if(date('d') == date('d', $timestamp))
		{
			$date = date($lang::get('date_today', 'date').$settings[4].':'.$settings[5], $timestamp);
		}
		elseif((date('d')-1) == date('d', $timestamp))
		{
			$date = date($lang::get('date_yesterday', 'date').$settings[4].':'.$settings[5], $timestamp);
		}
		else
		{
			$date = date($dateView.' '.$settings[4].':'.$settings[5], $timestamp);
		}
		return $date;
	}
	
	public static function subtok($string, $chr, $pos, $len = NULL) 
	{
		return implode($chr, array_slice(explode($chr, $string), $pos, $len));
	}
}
