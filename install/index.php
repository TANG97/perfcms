<?php
define('root', realpath(dirname('../../')));
define('sys', root.'/system/');
mb_internal_encoding('UTF-8');
error_reporting(E_ALL & ~E_NOTICE);
require_once('includes/Helpers.php');
require_once('includes/InstallLanguage.php');

if(!isset($_GET['lang']))
{	
	$language_1 = (!$_SERVER['HTTP_ACCEPT_LANGUAGE'] ? 'en' : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
	header('location: '.(!preg_match('/step|act|connection/', $_SERVER['REQUEST_URI']) ? '/install/?lang='.$language_1 : $_SERVER['REQUEST_URI'].'&lang='.$language_1));
	exit;
}
$language = htmlspecialchars(trim($_GET['lang']));
$lang = new InstallLanguage($language);
switch($_GET['act'])
{
	case 'create_dirs' :
		$dirs = array('assets', 'assets/css', 'assets/js', 'files', 'files/photos', 'cache', 'cache/twig',
		'cache/news', 'tmp');
		foreach($dirs as $dir)
		{
			if(!file_exists(root.'/'.$dir))
			{
				mkdir(root.'/'.$dir);
			}
		}
		header('location: /install?step=system_check');
	break;
	
	case 'chmod' :
		$dirs = array('assets', 'assets/css', 'assets/js', 'files', 'files/photos', 'cache', 'cache/twig',
		'cache/news', 'tmp', 'system/data/ini', 'system/data/info');
		foreach($dirs as $dir)
		{
			if(!is_writable(root.'/'.$dir))
			{
				chmod(root.'/'.$dir, 0777);
			}
		}
		header('location: /install?step=system_check');
	break;
}

if(isset($_GET['step']))
{
	switch($_GET['step'])
	{
		case 'license' :
			install_header($lang::get('install_license'));
			echo '<div class="title">'.$lang::get('install_license').':</div>
				<div class="menu">
				'.$lang::get('install_license_text').'<br/>
				<textarea rows="10" style="width: 90%;">'.file_get_contents('includes/license.txt').'</textarea><br/>
				['.install_link('/install?step=check_install&amp;lang='.$language, $lang::get('install_continue')).']
				</div>';
			echo '<div class="block">
				'.install_img('/design/images/back.png', 'back').' <a href="/install/">'.$lang::get('install_back').'</a></div>';
			install_footer();
		break;
		
		case 'check_install' :
		install_header($lang::get('install_check'));
			echo '<div class="title">'.$lang::get('install_check').':</div>
				<div class="menu">
				'.$lang::get('install_check_text').'<br/>
				'.(file_exists(sys.'data/ini/db.php') ? '<span style="color: red;"><b>'.$lang::get('install_check_installed').'</b></span>' : '<span style="color: green;"><b>'.$lang::get('install_check_succefuly').'</b></span><br/>['.install_link('/install?step=system_check&amp;lang='.$language, $lang::get('install_continue')).']').'
				
				</div>';
			echo '<div class="block">
				'.install_img('/design/images/back.png', 'back').' <a href="/install">'.$lang::get('install_back').'</a></div>';
			install_footer();
		break;
		
		case 'system_check' :
		if(file_exists(sys.'data/ini/db.php'))
		{
			header('location: /');
			exit;
		}
		install_header($lang::get('install_title_check'));
			echo '<div class="title">'.$lang::get('install_title_check').':</div>
				<div class="menu">
				'.$lang::get('install_check_system_text').'<br/>
				</div>
				<div class="post">';
				$dirs = array('assets', 'assets/css', 'assets/js', 'files', 'files/photos', 'cache', 'cache/twig',
				'cache/news', 'tmp');
				foreach($dirs as $dir)
				{
					echo '<table>
					<tr>
						<td style="text-align: center;">'.$lang::get('install_directory').'</td>
						<td>'.$lang::get('install_direxs').'</td>
						<td>'.$lang::get('install_perms').'</td>
					</tr>
					<tr>
						<tr>
							<td style="text-align: center;">/'.$dir.'</td>
						
						<td style="text-align: center;">';
						if(file_exists(root.'/'.$dir))
						{
							echo '<span style="color: green;"><b>OK</b></span>';
						} 
						else
						{	
							$errId = 1;
							echo '<span style="color: red;"><b>ERROR</b></span>';
						}
						echo '</td>
						<td style="text-align: center;">';
						if(is_writable(root.'/'.$dir))
						{
							echo '<span style="color: green;"><b>OK</b></span>';
						} 
						else
						{	
							$errId = 2;
							echo '<span style="color: red;"><b>ERROR</b></span>';
						}
						echo '</td></tr>
					</tr>
					</table>';
				}
				echo '</div>';
				echo '<div class="menu">'.(!isset($errId) ? '[<a href="/install/?step=db&lang='.$language.'">'.$lang::get('install_continue').'</a>]' : ($errId == 1 ? '[<a href="/install/?act=create_dirs&lang='.$language.'">'.$lang::get('install_fix_errors').'</a>]' : ($errId == 2 ? '[<a href="/install/?act=chmod&lang='.$language.'">'.$lang::get('install_fix_errors').'</a>]' : null))).'
				</div>';
			echo '<div class="block">
				'.install_img('/design/images/back.png', 'back').' <a href="/install?step=check_install">'.$lang::get('install_back').'</a></div>';
			install_footer();
		break;
		
		case 'db':
		if(file_exists(sys.'data/ini/db.php'))
		{
			header('location: /');
			exit;
		}
			install_header($lang::get('install_db_title'));
			echo '<div class="title">'.$lang::get('install_db_title').'</div>';
			if(isset($_POST['go']))
			{
				// print_r($_POST);
				$host = htmlspecialchars(trim($_POST['host']));
				$user = htmlspecialchars(trim($_POST['user']));
				$pass = htmlspecialchars(trim($_POST['pass']));
				$name = htmlspecialchars(trim($_POST['name']));
				try 
				{                           
					$db = new PDO("mysql:host=".$host.';dbname='.$name, $user, $pass); 
					$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );   
					$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);  
					$db->query('SET NAMES utf8'); 
				}   
				catch(PDOException $e) 
				{  
					echo $e->getMessage();   
				}
				$install = file_get_contents('install.sql');
				$queries = explode('-- --------------------------------------------------------', $install);
				foreach($queries as $query)
				{
					$db->query(trim($query));
				}
				$dbFile = root.'/system/data/ini/db.php';
				file_put_contents($dbFile, "<?php
				return array(
				'db_host' => '".$host."',
				'db_user' => '".$user."',
				'db_pass' => '".$pass."',
				'db_name' => '".$name."',
				);");
				echo '<div class="post">
					'.$lang::get('install_db_succefuly').'<br/>
					[<a href="/install/?step=admin&lang='.$language.'">'.$lang::get('install_continue').'</a>]
				</div>';
				install_footer();
				exit;
			}
			echo '<div class="menu">
				<form action="/install/?step=db&amp;lang='.$language.'" method="POST">
					'.$lang::get('install_db_host').':<br/>
					<input type="text" value="localhost" name="host" /><br/>
					'.$lang::get('install_db_user').':<br/>
					<input type="text" name="user" /><br/>
					'.$lang::get('install_db_pass').':<br/>
					<input type="text" name="pass" /><br/>
					'.$lang::get('install_db_name').':<br/>
					<input type="text" name="name" /><br/>
					<input type="submit" name="go" value="'.$lang::get('install_continue').'" /><br/>
				</form>
				</div>';
			echo '<div class="block">
				'.install_img('/design/images/back.png', 'back').' <a href="/install?step=system_check">'.$lang::get('install_back').'</a></div>';
			install_footer();
		break;
		
		case 'admin':
		if(!file_exists(sys.'data/ini/db.php'))
		{
			header('location: /');
			exit;
		}
			install_header($lang::get('install_admin_title'));
			echo '<div class="title">'.$lang::get('install_admin_title').'</div>';
			if(isset($_POST['go']))
			{
				if(file_exists(sys.'/data/ini/db.php'))
				{
				$db_info = include(sys.'/data/ini/db.php'); 
				try 
				{                           
					$db = new PDO("mysql:host=".$db_info['db_host'].';dbname='.$db_info['db_name'], $db_info['db_user'], $db_info['db_pass']); 
					$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );   
					$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);  
					$db->query('SET NAMES utf8'); 
				}   
				catch(PDOException $e) 
				{   
					echo $e->getMessage();
				}
				}
				require_once(sys.'/web/PerfSystem.php');
				$nick = htmlspecialchars(substr($db->quote(trim($_POST['nick'])), 1, -1));
				$name = htmlspecialchars(substr($db->quote(trim($_POST['name'])), 1, -1));
				$email = htmlspecialchars(substr($db->quote(trim($_POST['email'])), 1, -1));
				$gender = substr(abs(intval($_POST['gender'])), 0, 1);
				$pass = htmlspecialchars(substr($db->quote(trim($_POST['password'])), 1, -1));
				$repass = htmlspecialchars(substr($db->quote(trim($_POST['trypass'])), 1, -1));
				if(empty($nick) || mb_strlen($nick) > 25 || mb_strlen($nick) < 3 || !preg_match('/[a-zA-Zа-яА-Я0-9\_\-\@\.]/i', $nick))
				{
					$err .= $lang::get('install_admin_nick_error');
				}
				if(empty($name) || mb_strlen($name) > 25 || mb_strlen($name) < 3|| !preg_match('/[a-zA-Zа-яА-Я0-9\_\-\@\.]/i', $name))
				{
					$err .= $lang::get('install_admin_name_error');
				}
				if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$err .= $lang::get('install_admin_email_error');
				}
				if(empty($pass) || empty($repass) || $pass != $repass)
				{
					$err .= $lang::get('install_admin_password_error');
				}
				if(!isset($err))
				{
					$password = md5(base64_encode($pass) .'_PerfCMS_');
					$db->query("INSERT INTO `users` SET 
					`name` = '$name', 
					`nick` = '$nick', 
					`password` = '$password', 
					`email` = '$email', 
					`gender` = '$gender', 
					`level` = '3',
					`active` = '0', 
					`time` = '".time()."', 
					`reg_time` = '".time()."', 
					`surname` = '', 
					`device` = '', 
					`phone` = '', 
					`icq` = '0', 
					`day` = '0', 
					`month` = '0',
					`year` = '0',
					`site` = '',
					`info` = '',
					`city` = '',
					`country` = '',
					`locate` = '',
					`ban_time` = '0',
					`ban_text` = '',
					`ip` = '".$_SERVER['REMOTE_ADDR']."'");
					$lastId = $db->lastInsertId();
					$locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
					$db->query("INSERT INTO `settings` SET
					`user_id` = '". $lastId ."',
					`lang` = '". $locale ."',
					`signature` = '',
					`theme` = 'default',
					`web_theme` = 'default',
					`touch_theme` = 'default',
					`view_profile` = '0',
					`fast_mess` = '0',
					`show_email` = '0',
					`timezone` = 'Europe/Kiev',
					`ames` = '10'");
					// print_r($db->errorInfo());
					// header('location: /user/login?nickname='.$nick.'&password='.$password.'&login');
					echo '<div class="post">'.$lang::get('install_admin_succefuly').'<br/>
						<a href="/user/login?nickname='.$nick.'&password='.$pass.'&login">'.$lang::get('install_admin_login').'</a>
						</div>';
					install_footer();
					exit;
				}
			}
			if(isset($err)) echo '<div class="error">'.$err.'</div>';
			echo '<div class="post">
				<form action="/install/?step=admin&lang='.$language.'" method="post">
					'.$lang::get('install_admin_nick').' (max. 25, min. 3):<br/>
					<input type="text" name="nick" /><br/>
					'.$lang::get('install_admin_name').':<br/>
					<input type="text" name="name" /><br/>
					'.$lang::get('install_admin_gender').':<br/>
					<select name="gender">
						<option value="0">'.$lang::get('install_admin_male').'</option>
						<option value="1">'.$lang::get('install_admin_female').'</option>
					</select>
					<br/>
					E-Mail:<br/>
					<input type="text" name="email" /><br/>
					'.$lang::get('install_admin_password').':<br/>
					<input type="text" name="password" /><br/>
					'.$lang::get('install_admin_trypass').':<br/>
					<input type="text" name="trypass" /><br/>
					<input type="submit" name="go" value="'.$lang::get('install_continue').'" />
				</form>
				</div>';
			install_footer();
		break;
		
		default:
			$title = $lang::get('install_title', 'install');
			install_header($title);
			echo '<div class="title">'.$title.'</div>
			<div class="menu">'.$lang::get('install_greetings').'</div>
			<div class="post">';
			$lngs = scandir(root.'/protected/messages');
			foreach($lngs as $lng)
			{
				if($lng != '.' && $lng != '..' && $lng != '.htaccess')
				{
					echo install_img('/design/images/flags/'.$lng.'.png', 'Flag').' '.install_link('/install/?step=license&amp;lang='.$lng, $lang::get($lng, 'languages')).'<br>';
				}
			}
			echo '</div>';
			install_footer();
		break;
	}
}
else
{
	$title = $lang::get('install_title', 'install');
	install_header($title);
	echo '<div class="title">'.$title.'</div>
	<div class="menu">'.$lang::get('install_greetings').'</div>
	<div class="post">';
	$lngs = scandir(root.'/protected/messages');
	foreach($lngs as $lng)
	{
		if($lng != '.' && $lng != '..' && $lng != '.htaccess')
		{
			echo install_img('/design/images/flags/'.$lng.'.png', 'Flag').' '.install_link('/install/?step=license&amp;lang='.$lng, $lang::get($lng, 'languages')).'<br>';
		}
	}
	echo '</div>';
	install_footer();
}