<?php
/*
 * PerfSystem
 *
 * Global System functions
 *
 * @author: Artas
 * @link: http://perfcms.org.ua
 * @package: PerfCMS
 * @scince: 2.0
 */
class PerfSystem
{
	/*
	 * @var array $settings: Array with system settings
	 */
	public static $settings; 
	
	/*
	 * @return string: version of PerfCMS
	 */
	public static function getVersion()
	{
		return file_get_contents(APP_SYS.'/data/info/version.txt');
	}
	
	/*
	 * @return string: Browser Type
	 */
	public static function browserType() 
	{
		$useragent	=	$_SERVER['HTTP_USER_AGENT'];
		if(isset($_COOKIE['styleType']) and preg_match('/wap|web|touch/i', $_COOKIE['styleType']))
		{
			return $_COOKIE['styleType'];
		}
		elseif(!isset($_COOKIE['styleType']))
		{
			if(preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|msie|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|windows (ce|phone)|xda|xiino|ios|ipad|touch|ucweb/i',
			$useragent))
			{
				return 'touch';
			}
			elseif(preg_match('/1207|wap|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',
			substr($useragent,0,4)))
			{
				return 'wap';
			}
			else 
			{
				return 'web';
			}
		}
	}
	
	/*
	 * @return string $theme Return current theme from settings
	 */
	public static function getTheme()
	{
		$user = new PerfUser;
		$sets = self::getSettings();
		if(self::browserType() == 'wap')
		{
			if($user->loged())
			{
				$theme = $user::$settings['theme'];
			}
			elseif(!$user->loged())
			{
				$theme = $sets['wap_theme'];
			}
		}
		elseif(self::browserType() == 'web')
		{
			if($user->loged())
			{
				$theme = $user::$settings['web_theme'];
			}
			elseif(!$user->loged())
			{
				$theme = $sets['web_theme'];
			}
		}
		elseif(self::browserType() == 'touch')
		{
			if($user->loged())
			{
				$theme = $user::$settings['touch_theme'];
			}
			elseif(!$user->loged())
			{
				$theme = $sets['touch_theme'];
			}
		}
		return $theme;
	}
	
	public static function CurrentLang()
	{
		$user = new PerfUser;
		$sets = self::getSettings();
		if($user::loged())
		{
			return $user::$settings['lang'];
		}
		elseif(!$user::loged() && isset($_COOKIE['lang']))
		{
			return $_COOKIE['lang'];
		}
		else
		{
			return self::getLocale();
		}
	}
	
	/*
	 * @return array $settings Return global site settings
	 */
	public static function getSettings($optionValue = null)
	{
		$db = PerfDb::init();
		if($optionValue == null)
		{
			self::$settings = $db->query("SELECT * FROM `system`")->fetch();
			return self::$settings;
		}
		else
		{
			$settings = $db->query("SELECT * FROM `system`")->fetch();
			if(!in_array($optionValue, $settings))
			{
				return $settings[$optionValue];
			}
			else
			{
				echo 'Can\'t find attribute';
			}
		}
	}	
	
	/*
	 * @return string $language User browser language
	 */
	public static function getLocale()
	{
		if ( ! $_SERVER['HTTP_ACCEPT_LANGUAGE'])
		{
			$language = self::getSettings('language');
		}
		else
		{
			$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		}
		return $language;
	}
	
	/*
	 * @return string Image
	 */
	public static function image($name)
	{
		if(file_exists(APP_ROOT.'/design/images/'.$name))
		{
			return '<img src="/design/images/'.$name.'" alt="Image" />';
		}
		else
		{
			return '<img src="/design/images/no_ico.png" alt="Icon" />';
		}
	}
	
	/*
	 * @return string Active user modules
	 */
	public static function interfaceMenu()
	{
		$modules_dir = opendir(APP_ROOT .'/modules');
		while ($module = readdir($modules_dir)) 
		{
			if ($module == '.' || $module == '..' || $module == '.htaccess') 
			continue;
			$module_data = parse_ini_file(APP_ROOT .'/modules/'.$module.'/config.ini');
			if($module_data['open'] !=0 && $module_data['access'] !=2)
			{
				echo '<div class="menu">'.self::image($module_data['code_name'].'.png').' <a href="/'. $module .'/index">'.Lang::get($module_data['code_name'].'_title', $module_data['code_name']).'</a>'.($module_data['counter'] != 'false' ? require_once(APP_ROOT.'/modules/'.$module.'/components/counter.php') : false).'</div>';
			}
		}
	}
	
	/*
	 * Timezone selector from WordPress
	 */
	
	public static function timezoneChoiceUsortCallback( $a, $b )
	{
		// Don't use translated versions of Etc
		if ( 'Etc' === $a['continent'] && 'Etc' === $b['continent'] ) 
		{
			// Make the order of these more like the old dropdown
			if ( 'GMT+' === substr( $a['city'], 0, 4 ) && 'GMT+' === substr( $b['city'], 0, 4 ) ) 
			{
				return -1 * ( strnatcasecmp( $a['city'], $b['city'] ) );
			}
			if ( 'UTC' === $a['city'] ) 
			{
				if ( 'GMT+' === substr( $b['city'], 0, 4 ) ) 
				{
                return 1;
				}
				return -1;
			}
			if ( 'UTC' === $b['city'] ) 
			{
				if ( 'GMT+' === substr( $a['city'], 0, 4 ) ) 
				{
					return -1;
				}
				return 1;
			}
			return strnatcasecmp( $a['city'], $b['city'] );
		}
		if ( $a['t_continent'] == $b['t_continent'] ) 
		{
			if ( $a['t_city'] == $b['t_city'] ) 
			{
				return strnatcasecmp( $a['t_subcity'], $b['t_subcity'] );
			}
			return strnatcasecmp( $a['t_city'], $b['t_city'] );
		} 
		else 
		{
			// Force Etc to the bottom of the list
			if ( 'Etc' === $a['continent'] ) 
			{
				return 1;
			}
			if ( 'Etc' === $b['continent'] ) 
			{
				return -1;
			}
			return strnatcasecmp( $a['t_continent'], $b['t_continent'] );
		}
	}

	/**
	* Gives a nicely formatted list of timezone strings // temporary! Not in final
	*
	* @param $selected_zone string Selected Zone
	*
	*/
	public static function timezoneChoice( $selected_zone ) 
	{
		$continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');

		// Load translations for continents and cities

		$zonen = array();
		foreach ( timezone_identifiers_list() as $zone ) 
		{
			$zone = explode( '/', $zone );
			if ( !in_array( $zone[0], $continents ) ) 
			{
				continue;
			}

			// This determines what gets set and translated - we don't translate Etc/* strings here, they are done later
			$exists = array(
				0 => ( isset( $zone[0] ) && $zone[0] ) ? true : false,
				1 => ( isset( $zone[1] ) && $zone[1] ) ? true : false,
				2 => ( isset( $zone[2] ) && $zone[2] ) ? true : false
			);
			$exists[3] = ( $exists[0] && 'Etc' !== $zone[0] ) ? true : false;
			$exists[4] = ( $exists[1] && $exists[3] ) ? true : false;
			$exists[5] = ( $exists[2] && $exists[3] ) ? true : false;

			$zonen[] = array(
				'continent'   => ( $exists[0] ? $zone[0] : '' ),
				'city'        => ( $exists[1] ? $zone[1] : '' ),
				'subcity'     => ( $exists[2] ? $zone[2] : '' ),
				't_continent' => ( $exists[3] ? str_replace( '_', ' ', $zone[0] ) : '' ),
				't_city'      => ( $exists[4] ? str_replace( '_', ' ', $zone[1] ) : '' ),
				't_subcity'   => ( $exists[5] ? str_replace( '_', ' ', $zone[2] ) : '' )
			);
		}
		usort( $zonen, 'self::timezoneChoiceUsortCallback' );

		$structure = array();

		if ( empty( $selected_zone ) ) 
		{
			$structure[] = '<option selected="selected" value="">' . 'Select a city' . '</option>';
		}

		foreach ( $zonen as $key => $zone ) 
		{
			// Build value in an array to join later
			$value = array( $zone['continent'] );

			if ( empty( $zone['city'] ) ) 
			{
				// It's at the continent level (generally won't happen)
				$display = $zone['t_continent'];
			} 
			else 
			{
				// It's inside a continent group

				// Continent optgroup
				if ( !isset( $zonen[$key - 1] ) || $zonen[$key - 1]['continent'] !== $zone['continent'] ) 
				{
					$label = $zone['t_continent'];
					$structure[] = '<optgroup label="'.$label .'">';
				}

				// Add the city to the value
				$value[] = $zone['city'];

				$display = $zone['t_city'];
				if ( !empty( $zone['subcity'] ) ) 
				{
					// Add the subcity to the value
					$value[] = $zone['subcity'];
					$display .= ' - ' . $zone['t_subcity'];
				}
			}

			// Build the value
			$value = join( '/', $value );
			$selected = '';
			if ( $value === $selected_zone ) 
			{
				$selected = 'selected="selected" ';
			}
			$structure[] = "<option $selected value='$value'>$display</option>";

			// Close continent optgroup
			if ( !empty( $zone['city'] ) && ( !isset($zonen[$key + 1]) || (isset( $zonen[$key + 1] ) && $zonen[$key + 1]['continent'] !== $zone['continent']) ) ) 
			{
				$structure[] = '</optgroup>';
			}
		}

		// Do UTC
		$structure[] = '<optgroup label="'. 'UTC' .'">';
		$selected = '';
		if ( 'UTC' === $selected_zone )
			$selected = 'selected="selected" ';
		$structure[] = "<option $selected value='UTC'>UTC</option>";
		$structure[] = '</optgroup>';

		// Do manual UTC offsets
		$structure[] = '<optgroup label="'. 'Manual Offsets' .'">';
		$offset_range = array (-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5,						-4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
							0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14);
		foreach ( $offset_range as $offset ) 
		{
			if ( 0 <= $offset )
				$offset_name = '+' . $offset;
			else
				$offset_name = (string) $offset;

			$offset_value = $offset_name;
			$offset_name = str_replace(array('.25','.5','.75'), array(':15',':30',':45'), $offset_name);
			$offset_name = 'UTC' . $offset_name;
			$offset_value = 'UTC' . $offset_value;
			$selected = '';
			if ( $offset_value === $selected_zone )
				$selected = 'selected="selected" ';
			$structure[] = "<option $selected value='$offset_value'>$offset_name</option>";

		}
		$structure[] = '</optgroup>';

		return join( "\n", $structure );
	}
	
	public function styleSwitcher()
	{
		require_once(APP_ROOT.'/protected/components/Filters.php');
		$settings = $this->getSettings();
		if($settings['active_switch'] == 0)
		{
			if(isset($_COOKIE['styleType']))
			{
				if($_COOKIE['styleType'] == 'wap')
				{
					return '<b>WAP</b>|<a href="/index/type?content=touch&return='.Filters::input($_SERVER['REQUEST_URI']).'">Touch</a>|<a href="/index/type?content=web&return='.Filters::input($_SERVER['REQUEST_URI']).'">WEB</a>';
				}
				elseif($_COOKIE['styleType'] == 'touch')
				{
					return '<a href="/index/type?content=wap&return='.Filters::input($_SERVER['REQUEST_URI']).'">WAP</a>|<b>Touch</b>|<a href="/index/type?content=web&return='.Filters::input($_SERVER['REQUEST_URI']).'">WEB</a>';
				}
				elseif($_COOKIE['styleType'] == 'web')
				{
					return '<a href="/index/type?content=wap&return='.Filters::input($_SERVER['REQUEST_URI']).'">WAP</a>|<a href="/index/type?content=touch&return='.Filters::input($_SERVER['REQUEST_URI']).'">Touch</a>|<b>WEB</b>';
				}
			}
			elseif(!isset($_COOKIE['styleType']) || !preg_match('/wap|web|touch/i', $_COOKIE['styleType']))
			{
				if(self::browserType() == 'wap')
				{
					return '<b>WAP</b>|<a href="/index/type?content=touch&return='.Filters::input($_SERVER['REQUEST_URI']).'">Touch</a>|<a href="/index/type?content=web&return='.Filters::input($_SERVER['REQUEST_URI']).'">WEB</a>';
				}
				elseif(self::browserType() == 'touch')
				{
				return '<a href="/index/type?content=wap&return='.Filters::input($_SERVER['REQUEST_URI']).'">WAP</a>|<b>Touch</b>|<a href="/index/type?content=web&return='.Filters::input($_SERVER['REQUEST_URI']).'">WEB</a>';
				}
				elseif(self::browserType() == 'web')
				{
					return '<a href="/index/type?content=wap&return='.Filters::input($_SERVER['REQUEST_URI']).'">WAP</a>|<a href="/index/type?content=touch&return='.Filters::input($_SERVER['REQUEST_URI']).'">Touch</a>|<b>WEB</b>';
				}
			}
		}
	}
	
	public static function Server($parametr)
	{
		if(!empty($parametr) || in_array($parametr, $_SERVER))
		{
			return $_SERVER[strtoupper($parametr)];
		}
		else
		{
			return '<div class="error">Error! Request parameter can\'t be find in <b>$_SERVER</b></div>';
		}
	}
		
	public static function getLanguage()
	{	
		$user = new PerfUser;
		$config = self::getSettings();
		if($user->loged())
		{
			return 	$user::$settings['lang'];
		}
		else
		{
			return $config['language'];
		}
	}
	
	public static function browser($agent) 
	{
		require_once(APP_ROOT.'/protected/components/Filters.php');
		if(empty($agent)) 
		{ 
			$agent = $_SERVER['HTTP_USER_AGENT']; 
		}
		if (stripos($agent, 'Avant Browser') !== false) 
		{
			return 'Avant Browser';
		} 
		elseif (stripos($agent, 'Acoo Browser') !== false) 
		{
			return 'Acoo Browser';
		} 
		elseif (stripos($agent, 'MyIE2') !== false) 
		{
			return 'MyIE2';
		} 
		elseif (preg_match('|Iron/([0-9a-z\.]*)|i', $agent, $pocket)) 
		{
			return 'SRWare Iron ' . filters::subtok($pocket[1], '.', 0, 2);
		} 
		elseif (preg_match('|Chrome/([0-9a-z\.]*)|i', $agent, $pocket)) 
		{
			return 'Chrome ' . filters::subtok($pocket[1], '.', 0, 3);
		} 
		elseif (preg_match('#(Maxthon|NetCaptor)( [0-9a-z\.]*)?#i', $agent, $pocket)) 
		{
			return $pocket[1] . $pocket[2];
		} 
		elseif (stripos($agent, 'Safari') !== false && preg_match('|Version/([0-9]{1,2}.[0-9]{1,2})|i', $agent, $pocket)) 
		{
			return 'Safari ' . filters::subtok($pocket[1], '.', 0, 3);
		} 
		elseif (preg_match('#(NetFront|K-Meleon|Netscape|Galeon|Epiphany|Konqueror|Safari|Opera Mini|Opera Mobile/Opera Mobi)/([0-9a-z\.]*)#i', $agent, $pocket)) 
		{
			return $pocket[1] . ' ' . filters::subtok($pocket[2], '.', 0, 2);
		} 
		elseif (stripos($agent, 'Opera') !== false && preg_match('|Version/([0-9]{1,2}.[0-9]{1,2})|i', $agent, $pocket)) 
		{
			return 'Opera ' . $pocket[1];
		} 
		elseif (preg_match('|Opera[/ ]([0-9a-z\.]*)|i', $agent, $pocket)) 
		{
			return 'Opera ' . filters::subtok($pocket[1], '.', 0, 2);
		} 
		elseif (preg_match('|Orca/([ 0-9a-z\.]*)|i', $agent, $pocket)) 
		{
			return 'Orca ' . filters::subtok($pocket[1], '.', 0, 2);
		} 
		elseif (preg_match('#(SeaMonkey|Firefox|GranParadiso|Minefield|Shiretoko)/([0-9a-z\.]*)#i', $agent, $pocket)) 
		{
			return $pocket[1] . ' ' . filters::subtok($pocket[2], '.', 0, 3);
		}
		elseif (preg_match('|rv:([0-9a-z\.]*)|i', $agent, $pocket) && strpos($agent, 'Mozilla/') !== false) 
		{
			return 'Mozilla ' . filters::subtok($pocket[1], '.', 0, 2);
		} 
		elseif (preg_match('|Lynx/([0-9a-z\.]*)|i', $agent, $pocket)) 
		{
			return 'Lynx ' . filters::subtok($pocket[1], '.', 0, 2);
		} 
		elseif (preg_match('|MSIE ([0-9a-z\.]*)|i', $agent, $pocket)) 
		{
			return 'IE ' . filters::subtok($pocket[1], '.', 0, 2);
		} 
		elseif (preg_match('|Googlebot/([0-9a-z\.]*)|i', $agent, $pocket)) 
		{
			return 'Google Bot ' . filters::subtok($pocket[1], '/', 0, 2);
		} 
		elseif (preg_match('|Yandex|i', $agent)) 
		{
			return 'Yandex Bot ';
		} 
		elseif (preg_match('|Nokia([0-9a-z\.\-\_]*)|i', $agent, $pocket)) 
		{
			return 'Nokia '.$pocket[1];
		} 
		else 
		{
			$agent = preg_replace('|http://|i', '', $agent);
			$agent = strtok($agent, '/ ');
			$agent = substr($agent, 0, 22);
			$agent = filters::subtok($agent, '.', 0, 2);

			if (!empty($agent)) 
			{
				return $agent;
			} 
		} 
		return 'Unknown';
	}
}
