<?php
/*
 * PUser class file
 * @author: Artas
 * @link: http://perfcms.org.ua
 * @package: PerfCMS
 * @scince: 2.0
 */

class PerfUser
{	
	/*
	 * @var array $data User data
	 */
	public static $data = array();
	
	/*
	 * @var array $settings User settings
	 */
	public static $settings = array();

	/*
	 * @param string $nick User nickname
	 * @param string $password User password
	 * @return Authorize user
	 */
	public function login($nick, $password)
		{
			$db = PerfDb::init();
			if($db->query("SELECT * FROM `users` WHERE `nick` = '$nick' AND `password` = '$password'")->rowCount() == 1)
			{
				$tempUserData = $db->query("SELECT * FROM `users` WHERE `nick` = '$nick' AND `password` = '$password'")->fetch();
				$_SESSION['user_id'] = $tempUserData['id'];
				$_SESSION['password'] = $tempUserData['password'];
				setcookie('authorized', base64_encode($tempUserData['id'].':'.$tempUserData['password']), time()+60*60*24*365, '/');
			}
			else
			{
				echo 'Authorization failed! User with your password and nickname does not exists!';
			}
		}
	
	/*
	 * @return array User data
	 */
	public static function loged()
		{
			// connection to database
			$db = PerfDb::init();
			// check of active sessions
			if(isset($_SESSION['user_id']) && isset($_SESSION['password']))
			{
				// check exists of users with session data
				if($db->query("SELECT * FROM `users` WHERE `id` = '".intval($_SESSION['user_id'])."' AND `password` = '". $_SESSION['password'] ."'")->rowCount() == 1)
				{
					// fetching user data
					self::$data = $db->query("SELECT * FROM `users` WHERE `id` = '".intval($_SESSION['user_id'])."' AND `password` = '". $_SESSION['password'] ."'")->fetch();
					// fetching user settings
					self::$settings = $db->query("SELECT * FROM `settings` WHERE `user_id` = '". intval($_SESSION['user_id']) ."'")->fetch();
					// set timezone
					ini_set('date.timezone', self::$settings['timezone']);
					// updating user
					$db->query("UPDATE `users` SET `time` = '".time()."', `ip` = '".$_SERVER['REMOTE_ADDR']."' WHERE `id` = '".self::$data['id']."'");
				}
			}
			elseif(isset($_COOKIE['authorized']))
				{
					$cookieData = explode(':', base64_decode($_COOKIE['authorized']));
					if($db->query("SELECT * FROM `users` WHERE `id` = '".$cookieData[0]."' AND `password` = '". $cookieData[1] ."'")->rowCount() == 1)
					{
						self::$data = $db->query("SELECT * FROM `users` WHERE `id` = '".$cookieData[0]."' AND `password` = '". $cookieData[1] ."'")->fetch();
						self::$settings = $db->query("SELECT * FROM `settings` WHERE `user_id` = '". $cookieData[0] ."'")->fetch();
						// set timezone
						ini_set('date.timezone', self::$settings['timezone']);
						// updating user
						$db->query("UPDATE `users` SET `time` = '".time()."', `ip` = '".$_SERVER['REMOTE_ADDR']."'  WHERE `id` = '".self::$data['id']."'");
					}
				}
			else
				{
					self::$data = false;
					self::$settings = false;
				}
			return self::$data;
			return self::$settings;
		}
	/*
	 * @return User logout
	 */
	public function logout()
		{
			$_SESSION['user_id'] = false;
			$_SESSION['password'] = false;
			setcookie('authorized', '', (time()-3600), '/');
		}
		
	/*
	 * @return int User level
	 */
	public static function level()
	{
		if(self::loged())
		{
			return self::$data['level'];
		}
		else
		{
			return -1;
		}
	}
	
	/*
	 * @return string Number of all Users
	 */
	public function count()
	{
		$db = PerfDb::init();
		$users = $db->query("SELECT * FROM `users`")->rowCount();
		$newUsers = $db->query("SELECT * FROM `users` WHERE `reg_time` > '".(time()-60*60*24)."'")->rowCount();
		return '('.$users.''.($newUsers > 0 ? '/<span class="green">+'.$newUsers.'</span>' : NULL).')';
	}
	
	public static function Id()
	{
		if(self::loged())
		{
			return self::$data['id'];
		}
		else
		{
			return false;
		}
	}
	
	public static function nick($user_id)
	{
		$id = Filters::num($user_id);
		$db = PerfDb::init();
		if($db->query("SELECT * FROM `users` WHERE `id` = '$id'")->rowCount() == 1)
		{
			$user = $db->query("SELECT * FROM `users` WHERE `id` = '$id'")->fetch();
			$sign = $db->query("SELECT `signature` FROM `settings` WHERE `user_id` = '$id'")->fetchColumn();
			echo '<table cellpadding="0" cellspacing="0"><tr><td>'.self::photo($user_id, true).'</td> <td>&nbsp;'.PerfSystem::image('gender_'.$user['gender'].'.png').' <a href="/user/profile-'.$user['id'].'">'. $user['nick'] .'</a> '.($user['time'] > (time()-300) ? PerfSystem::image('on.png') : PerfSystem::image('off.png')).''.(!empty($sign) ? '<br/> &nbsp;<em>'.$sign.'</em>' : null).'</td></tr></table>';
		}
		else
		{
			echo 'Deleted';
		}
	}
	
	public static function tnick($user_id)
	{
		$id = Filters::num($user_id);
		$db = PerfDb::init();
		if($db->query("SELECT * FROM `users` WHERE `id` = '$id'")->rowCount() == 1)
		{
			$user = $db->query("SELECT * FROM `users` WHERE `id` = '$id'")->fetch();
			return $user['nick'];
		}
		else
		{
			return 'Deleted';
		}
	}
	
	public static function photo($user_id, $mini = false)
	{
		$photo_id = Filters::num($user_id);
		if(file_exists(APP_ROOT.'/files/photos/'.$photo_id.($mini == true ? '_mini' : null).'.jpg'))
		{
			return '<img src="/files/photos/'.$photo_id.($mini == true ? '_mini' : null).'.jpg" alt="Photo" />';
		}
		else
		{
			return PerfSystem::image('no_photo'.($mini == true ? '_mini' : null).'.jpg');
		}
	}
	
	public static function setLocation($location_id, $user_id)
	{
		$db = PerfDb::init();
		if($db->query("SELECT * FROM `guests`")->rowCount() >=50)
		{
			$db->query("TRUNCATE TABLE `guests`");
		}
		if(self::loged())
		{
			$db->query("UPDATE `users` SET `locate` = '$location_id' WHERE `id` = '$user_id'");
		}
		elseif(!self::loged())
		{
			$browser = PerfSystem::browser(PerfSystem::server('http_user_agent'));
			$ip = PerfSystem::server('remote_addr');
			$refer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
			$from = (!preg_match('/'.$_SERVER['HTTP_HOST'].'/i', $refer) ? $refer : '');
			$time = time();
			if($db->query("SELECT * FROM `guests` WHERE `ip` = '$ip' AND `browser` = '$browser' LIMIT 1")->rowCount() == 1)
			{
				$db->query("UPDATE `guests` SET `time` = '". time() ."' WHERE `ip` = '$ip' AND `browser` = '$browser' LIMIT 1");
			} 
			else 
			{
				$db->query("INSERT INTO `guests` SET `ip` = '$ip', `browser` = '$browser', `time` = '". time() ."'");
			}
		}
	}
}