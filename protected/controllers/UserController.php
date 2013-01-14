<?php
/*
* User controller
* @package: PerfCMS
*/
class UserController extends Controller
{	
	
	public function actionIndex()
	{
		$user = new User;
		if(!$user->loged())
		{
			header('location: /');
			exit();
		}
		$lang = new Lang();
		$this->getHeader(array('title' => $lang::get('panel')), '/index::user_cabinet_location');
		$this->render('main');
		$this->getFooter();
	}
	
	public function actionSettings()
	{
		$user = new User;
		
		if(!$user->loged())
		{
			header('location: /');
			exit();
		}
		
		$lang = new Lang();
		$settings = User::$settings;
		$config = System::getSettings();
		
		if(isset($_GET['act']) && $_GET['act'] == 'save')
		{
			$uset = UserSettings::model()->findByPk(User::Id());
			$uset->fast_mess = Filters::num($_POST['fast_mess']);
			$uset->timezone = Filters::input($_POST['timezone']);
			$uset->lang = (file_exists(APP_ROOT.'/protected/messages/'.Filters::input($_POST['language'])) ? Filters::input($_POST['language']) : $config['language']);
			@setcookie('lang', Filters::input($_POST['language']), time()+60*60*24*2500, '/');
			$uset->signature = (!empty($_POST['signature']) ? str_replace('http://', '', Filters::input($_POST['signature'])) : '');
			$uset->ames = substr(Filters::num($_POST['ames']), 0, 2);
			$uset->save();
			header('location: /user/settings?act=saved');
			exit;
		}
		$ldr = scandir(APP_ROOT.'/protected/messages');
		$this->getHeader(array('title' => $lang::get('general_settings')), '/index::user_cabinet_location');
		$this->render('generalSettings', array('timezone' => $settings['timezone'], 'lngs' => $ldr));
		$this->getFooter();
	}
	
	public function actionEditProfile()
	{
		$user = new User;
		if(!$user->loged())
		{
			header('location: /');
			exit();
		}
		$lang = new Lang();
		if(isset($_GET['act']) && $_GET['act'] == 'save')
		{
			$uset = EditProfile::model()->findByPk(User::Id());
			$uset->name = substr(Filters::input($_POST['name']), 0, 18);
			$uset->surname = substr(Filters::input($_POST['surname']), 0, 36);
			$uset->gender = substr(Filters::num($_POST['gender']), 0, 1);
			$day = substr(Filters::num($_POST['day']), 0, 2);
			$uset->day = (empty($day) || $day < 1 ? 1 : ($day > 31 ? 31 : $day));
			$month = substr(Filters::num($_POST['month']), 0, 2);
			$uset->month = (empty($month) || $month < 1 ? 1 : ($month > 12 ? 12 : $month));
			$year = substr(Filters::num($_POST['year']), 0, 4);
			$uset->year = (empty($year) || $year < 1960 ? 1960 : ($year > (date('Y')-6) ? date('Y')-6 : $year));
			$uset->icq = substr(Filters::num($_POST['icq']), 0, 9);
			$uset->phone = substr(Filters::input($_POST['phone']), 0, 18);
			$site = substr(Filters::input($_POST['site']), 0, 64);
			$uset->site = (preg_match('/http:\/\//i', $site) ? preg_replace('/http:\/\//i', '', $site) : $site);
			$uset->info = substr(Filters::input($_POST['info']), 0, 3500);
			$uset->save();
		header('location: /user/editProfile?act=saved');
		exit;
		}
		$this->getHeader(array('title' => $lang::get('edit_profile')));
		$this->render('editProfile', array('currentYear' => (date('Y')-6)));
		$this->getFooter();
	}
	
	public function actionList()
	{
		$lang = new Lang();
		$db = PerfDb::init();
		$this->getHeader(array('title' => $lang::get('users_list')), '/user/list::user_list_location');
		$listAm = $db->query("SELECT * FROM `users`")->rowCount();
		$pages = new Paginator($listAm, System::pages());
		global $start;
		$listArray = $db->query("SELECT * FROM `users` ORDER BY time ASC LIMIT $start, ".System::pages()."");
		$this->render('list', array('list' => $listArray, 'pages' => $pages));
		$this->getFooter();
	}
	
	public function actionOnline()
	{
		$lang = new Lang();
		$db = PerfDb::init();
		$this->getHeader(array('title' => $lang::get('users_online')), '/user/list::user_online_location');
		$listAm = $db->query("SELECT * FROM `users` WHERE `time` > '".(time()-300)."'")->rowCount();
		$pages = new Paginator($listAm, System::pages());
		global $start;
		$listArray = $db->query("SELECT * FROM `users` WHERE `time` > '".(time()-300)."' ORDER BY time DESC LIMIT $start, ".System::pages()."");
		$this->render('online', array('list' => $listArray, 'pages' => $pages));
		$this->getFooter();
	}
	
	public function actionPhoto()
	{
		$user = new User();
		if(!$user->loged())
		{
			header('location: /');
			exit();
		}
		$lang = new Lang();
		if(isset($_GET['act']) && $_GET['act'] == 'upload' && $_FILES['photo'])
		{
			if(file_exists(APP_ROOT.'/files/photos/'.User::Id().'.jpg'))
			{
				unlink(APP_ROOT.'/files/photos/'.User::Id().'.jpg');
				unlink(APP_ROOT.'/files/photos/'.User::Id().'_mini.jpg');
			}
			$file_info = pathinfo($_FILES['photo']['name']);
			$file_info['extension'] = strtolower($file_info['extension']);
			move_uploaded_file($_FILES['photo']['tmp_name'], APP_ROOT.'/cache/'.$file_info['filename'].'.'.$file_info['extension']);
			copy(APP_ROOT.'/cache/'.$file_info['filename'].'.'.$file_info['extension'], APP_ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
			$photo = new Upload(APP_ROOT.'/cache/'.$file_info['filename'].'.'.$file_info['extension']);
			if($photo->uploaded)
			{
				$photo->allowed = array('image/*');
				$photo->file_new_name_body 	= User::Id().'_mini';
				$photo->image_convert 	= 'jpg';
				$photo->image_resize	= true;
				$photo->image_x		= 36;
				$photo->image_y		= 36;
				$photo->process(APP_ROOT. '/files/photos/');
				if ($photo->processed) 
				{
					// echo 'image resized';
					$photo->clean();
					// header('location: /user/photo/');
					// exit;
				} 
				else 
				{
					// echo 'error : ' . $photo->error;
					// header('location: /user/photo/');
					// exit;
				}
			}
			
			$photo = new Upload(APP_ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
			if($photo->uploaded)
			{
				$photo->allowed = array('image/*');
				$photo->file_new_name_body 	= User::Id();
				$photo->image_convert 	= 'jpg';
				$photo->image_resize	= true;
				$photo->image_x		= 110;
				$photo->image_y		= 135;
				$photo->process(APP_ROOT. '/files/photos/');
				if ($photo->processed) 
				{
					// echo 'image resized';
					$photo->clean();
					header('location: /user/photo/');
					exit;
				} 
				else 
				{
					// echo 'error : ' . $photo->error;
					header('location: /user/photo/');
					exit;
				}
			}
		}
		elseif(isset($_GET['act']) && $_GET['act'] == 'delete')
		{
			if(file_exists(APP_ROOT.'/files/photos/'.User::Id().'.jpg'))
			{
				unlink(APP_ROOT.'/files/photos/'.User::Id().'.jpg');
				unlink(APP_ROOT.'/files/photos/'.User::Id().'_mini.jpg');
			}
			header('location: /user/photo');
			exit;
		}
		$this->getHeader(array('title' => $lang::get('edit_avatar')), '/index::user_cabinet_location');
		$this->render('photo');
		$this->getFooter();
	}
	
	public function is_photo($uid)
	{
		if($uid != '')
		{
			if(file_exists(APP_ROOT.'/files/photos/'.$uid.'.jpg'))
			{
				return true;
			}
		}
	}
	
	public function actionLogin()
	{
		$usr = new User();
		if($usr::loged())
		{
			$this->redirect('/');
		}
		$return = (isset($_GET['return']) ? Filters::input($_GET['return']) : '/');
		if(isset($_GET['login']) || isset($_POST['login']))
		{
			$nick = (isset($_GET['nickname']) ? Filters::input($_GET['nickname']) : Filters::input($_POST['nickname']));
			$pass = (isset($_GET['password']) ? Filters::crypt(Filters::input($_GET['password'])) : Filters::crypt(Filters::input($_POST['password'])));
			$auth = new User;
			$auth->login($nick, $pass);
			header('location: '.$return);
			exit;
		}
		$lang = new Lang;
		$this->getHeader(array('title' => $lang::get('login')), '/user/login::user_login_location');
		$this->render('login', array('return' => $return));
		$this->getFooter();
	}
	
	public function actionCaptcha()
	{
		$captcha = new Captcha();
		return $captcha;
	}
	
	public function actionRegister()
	{
		$usr = new User();
		$db = PerfDb::init();
		$lang = new Lang;
		if($usr->loged())
		{
			header('location: /');
			exit;
		}
		$error = false;
		if(isset($_GET['finaly']))
		{
			$nick = filters::input($_POST['nickname']);
			$name = filters::input($_POST['name']);
			$password = filters::input($_POST['password']);
			$repassword = filters::input($_POST['repassword']);
			$email = filters::input($_POST['email']);
			$gender = filters::num($_POST['gender']);
			if(empty($nick) || mb_strlen($nick) < 3 || mb_strlen($nick) > 25 || !preg_match('/[a-zA-Zа-яА-Я0-9\_\-\@\.]/i', $nick))
			{
				$error .= $lang::get('register_nick_error');
			}
			elseif($db->query("SELECT * FROM `users` WHERE `nick` = '$nick'")->rowCount() != 0)
			{
				$error .= $lang::get('register_nick2_error');;
			}
			elseif(empty($name))
			{
				$error .= $lang::get('register_name_error');;
			}
			elseif(empty($password) || empty($repassword))
			{
				$error .= $lang::get('register_pass_error');;
			}
			elseif($password != $repassword)
			{
				$error .= $lang::get('register_pass2_error');;
			}
			elseif(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$error .= $lang::get('register_email_error');;
			}
			elseif($db->query("SELECT * FROM `users` WHERE `email` = '$email'")->rowCount() != 0)
			{
				$error .= $lang::get('register_email2_error');;
			}
			elseif($_POST['captcha'] != $_SESSION['captcha'])
			{
				$error .= $lang::get('captcha_error');
			}
			
			if($error == false)
			{
				$pass = filters::crypt($password);
				$conf = System::getSettings();
				$db->query("INSERT INTO `users` SET 
				`name` = '$name', 
				`nick` = '$nick', 
				`password` = '$pass', 
				`email` = '$email', 
				`gender` = '$gender', 
				`level` = '0',
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
				$locale = System::getLocale();
				$db->query("INSERT INTO `settings` SET
				`user_id` = '". $lastId ."',
				`lang` = '". $locale ."',
				`theme` = 'default',
				`web_theme` = 'default',
				`touch_theme` = 'default',
				`view_profile` = '0',
				`fast_mess` = '0',
				`show_email` = '0',
				`timezone` = '". $conf['timezone'] ."',
				`ames` = '10',
				`signature` = ''");
				// print_r($db->errorInfo());
				header('location: /user/login?nickname='.$nick.'&password='.$password.'&login');
				exit;
			}
		}
		$this->getHeader(array('title' => $lang::get('register')), '/user/register::user_register_location');
		$this->render('registration', array('err' => $error));
		$this->getFooter();
	}
	
	public function actionLogout()
	{
		$auth = new User;
		$auth->logout();
		$return = (isset($_GET['return']) ? Filters::input($_GET['return']) : '/');
		header('location: '.$return);
	}
	
	public function actionProfile()
	{
		$pid = Filters::num($_GET['profile']);
		$db = PerfDb::init();
		$lang = new Lang;
		if($pid == User::Id() || $pid == 0 || empty($pid) || $db->query("SELECT * FROM `users` WHERE `id` = '$pid'")->rowCount() == 0)
		{
		$profile = $db->query("SELECT * FROM `users` WHERE `id` = '".User::Id()."'")->fetch();
		}
		else
		{
		$profile = $db->query("SELECT * FROM `users` WHERE `id` = '$pid'")->fetch();
		}
		$this->getHeader(array('title' => $lang::get('profile_of').User::tnick($profile['id'])), '/user/profile-'.$pid.'::user_view_profile_location');
		$this->render('profile', array('profile' => $profile));
		$this->getFooter();
	}
	
	public function actionSecurity()
	{
		$usr = new User();
		if(!$usr->loged())
		{
			header('location: /');
			exit();
		}
		if(isset($_GET['act']) && $_GET['act'] == 'change_pass')
		{
			$user = new User;
			if(!empty($_POST['new_pass']) && !empty($_POST['ret_pass']) && !empty($_POST['cur_pass']))
			{
				$nPass = Filters::input($_POST['new_pass']);
				$rPass = Filters::input($_POST['ret_pass']);
				$cPass = Filters::crypt(Filters::input($_POST['cur_pass']));
				if($nPass != $rPass || mb_strlen($nPass) > 16 || mb_strlen($rPass) > 16)
				{
					$err = 'Passwords not true';
				}
				if($cPass != $user->data['password'])
				{
					$err .= 'Not true current Passwords';
				}
				if(!isset($err))
				{
					$usep = EditProfile::model()->findByPk(User::Id());
					$usep->password = Filters::crypt($rPass);
					$usep->save();
					header('location: /user/security');
					exit();
				}
			}
		}
		elseif(isset($_GET['act']) && $_GET['act'] == 'change_email')
		{
			$user = new User;
			if(!empty($_POST['new_email']) && !empty($_POST['cur_email']) && !empty($_POST['cur_pass']))
			{
				$nMail = Filters::input($_POST['new_email']);
				$cMail = Filters::input($_POST['cur_email']);
				$cPass = Filters::crypt(Filters::input($_POST['cur_pass']));
				if($cMail != $user->data['email'])
				{
					$err = 'Current e-mail not true';
				}
				if($cPass != $user->data['password'])
				{
					$err .= 'Not true current Password';
				}
				if(!isset($err))
				{
					$usep = EditProfile::model()->findByPk(User::Id());
					$usep->password = Filters::crypt($rPass);
					$usep->save();
					header('location: /user/security');
					exit();
				}
			}
		}
		$lang = new Lang();
		$this->getHeader(array('title' => $lang::get('security_settings')), '/index::user_cabinet_location');
		$this->render('security');
		$this->getFooter();
	}
	
	public function actionInterface()
	{
		$user = new User;
		if(!$user->loged())
		{
			header('location: /');
			exit();
		}
		$lang = new Lang;
		if(isset($_GET['act']) && $_GET['act'] == 'save')
		{
		$uset = UserSettings::model()->findByPk(User::Id());
		$theme = Filters::input($_POST['wap_theme']);
		$uset->theme = (file_exists(APP_ROOT.'/design/themes/wap/'.$theme) ? $theme : 'default');
		$web_theme = Filters::input($_POST['web_theme']);
		$uset->web_theme = (file_exists(APP_ROOT.'/design/themes/web/'.$web_theme) ? $web_theme : 'default');
		$touch_theme = Filters::input($_POST['touch_theme']);
		$uset->web_theme = (file_exists(APP_ROOT.'/design/themes/touch/'.$touch_theme) ? $touch_theme : 'default');
		$uset->save();
		header('location: /user/interface?act=saved');
		exit;
		}
		$wapDir = scandir(APP_ROOT .'/design/themes/wap');
		$webDir = scandir(APP_ROOT .'/design/themes/web');
		$touchDir = scandir(APP_ROOT .'/design/themes/touch');
		$this->getHeader(array('title' => $lang::get('design_settings')), '/index::user_cabinet_location');
		$this->render('interface', array('waps' => $wapDir, 'webs' => $webDir, 'touchs' => $touchDir));
		$this->getFooter();
	}
	
	public function actionDialogs()
	{	
		$user = new User;
		if(!$user->loged())
		{
			header('location: /');
			exit();
		}
		$this->getHeader(Lang::get('dialogs_title', 'dialogs'), '/index::user_dialogs_location');
		$this->render('dialogs');
		$this->getFooter();
	}
	
	public function dialogs()
	{
		$db = PerfDb::init();
		
		$listAm = $db->query("SELECT * FROM `mail_chat` WHERE `user_id` = '".User::Id()."' OR `who_id` = '".User::Id()."'")->rowCount();
		
		$pages = new Paginator($listAm, System::pages());
		
		global $start;
		
		$listArray = $db->query("SELECT * FROM `mail_chat` WHERE `user_id` = '".User::Id()."' OR `who_id` = '".User::Id()."' ORDER BY time_last_message DESC LIMIT $start, ".System::pages()."");
		
		if($listAm == 0)
		{
			return '<div class="error"> No dialogs have been found </div>';
		}
		else
		{
			foreach($listArray as $dialog)
			{
				$newInbox = $db->query("SELECT * FROM `mail` WHERE `who_id` = '".User::Id()."' AND `read` = '0' AND `mail_chat_id` = '$dialog[id]'")->rowCount();
			
				if($dialog['user_id'] == User::Id())
				{
					return '<div class="menu">
						» <a href="/user/dialog?id='.$dialog['who_id'].'">'.User::tnick($dialog['who_id']).'</a> '.($newInbox > 0 ? '<span class="green">[+'.$newInbox.']</span>' : null).'
					</div>'
					. $pages->view();
				}
				else
				{
					return '<div class="menu">
						» <a href="/user/dialog?id='.$dialog['user_id'].'">'.User::tnick($dialog['user_id']).'</a> '.($newInbox > 0 ? '<span class="green">[+'.$newInbox.']</span>' : null).'
					</div>'
					. $pages->view();
				}
			}
		}
	}
	
	public function actionDialog()
	{
		$db = PerfDb::init();
		if(!isset($_GET['id']) || $_GET['id'] == User::Id() || $_GET['id'] == 0 || !User::loged())
		{
			$this->redirect('/');
		}
		elseif ($db->query("SELECT * FROM `mail_chat` WHERE (`user_id` = '". User::Id() ."' OR `who_id` = '". User::Id() ."') AND (`who_id` = '". Filters::num($_GET['id']) ."' OR `user_id` = '". Filters::num($_GET['id']) ."')")->rowCount() == 0) 
		{ 
			$db->query("INSERT INTO `mail_chat` SET `user_id` = '". User::Id() ."', `who_id` = '". Filters::num($_GET['id']) ."', `time_last_message` = '". time() ."'"); 
			$this->redirect('/user/dialog?id='.$id.'&rand='.rand(1234, 4321));
		}
		
		$id = Filters::num($_GET['id']);
		
		if($db->query("SELECT * FROM `mail_chat` WHERE `user_id` = '". User::Id()."' AND `who_id` = '". $id ."'")->rowCount() != 0)
		{
			$dialog_id = $db->query("SELECT id FROM `mail_chat` WHERE `user_id` = '". User::Id() ."' AND `who_id` = '". $id ."'")->fetchColumn();
		}
		else
		{
			$dialog_id = $db->query("SELECT `id` FROM `mail_chat` WHERE `who_id` = '". User::Id() ."' AND `user_id` = '". $id ."'")->fetchColumn();
		}
		
		if(isset($_GET['message']))
		{
			$text = mb_substr(Filters::input($_POST['text']), 0, 7000);
			if(!empty($text))
			{
				$db->query("INSERT INTO `mail` SET `mail_chat_id` = '$dialog_id', `user_id` = '". User::Id() ."', `who_id` = '". $id ."', `text` = '". $text ."', `time` = '". time() ."', `read`='0'");
				// print_r($db->errorInfo()); 
				$db->query("UPDATE `mail_chat` SET `time_last_message` = '". time() ."' WHERE `id` = '$dialog_id'");
				// print_r($db->errorInfo()); 
				$this->redirect('/user/dialog?id='.$id.'&rand='.rand(1234, 4321));
			}
		}
		
		$this->getHeader(Lang::get('dialogs_dialog', 'dialogs').User::tnick($id));
		$this->render('dialog', array('this_id' => $id));
		$this->getFooter();
	}
	
	public function dialog($id = 0)
	{
		$db = PerfDb::init();
		
		if($db->query("SELECT * FROM `mail_chat` WHERE `user_id` = '". User::Id()."' AND `who_id` = '". $id ."'")->rowCount() != 0)
		{
			$dialog_id = $db->query("SELECT id FROM `mail_chat` WHERE `user_id` = '". User::Id() ."' AND `who_id` = '". $id ."'")->fetchColumn();
		}
		else
		{
			$dialog_id = $db->query("SELECT `id` FROM `mail_chat` WHERE `who_id` = '". User::Id() ."' AND `user_id` = '". $id ."'")->fetchColumn();
		}
		
		$listAm = $db->query("SELECT * FROM `mail` WHERE `mail_chat_id` = '$dialog_id'")->rowCount();
		
		$pages = new Paginator($listAm, System::pages());
		
		global $start;
		
		$listArray = $db->query("SELECT * FROM `mail` WHERE `mail_chat_id` = '$dialog_id' ORDER BY time DESC LIMIT $start, ".System::pages()."");
		
		if($listAm == 0)
		{
			return '<div class="error"> No messages have been found </div>';
		}
		else
		{
			foreach($listArray as $dialog)
			{
				if (User::Id() == $dialog['who_id'])
				{
					$db->query("UPDATE `mail` SET `read` = '1' WHERE `id` = '".$dialog['id']."'");
				}
				echo '<div class="post">
					'.($dialog['read'] == 0 ? '<span style="color:red;">*</span> ': NULL).'
					<a href="/user/profile-'.$dialog['user_id'].'">'. User::tnick($dialog['user_id']) .'</a> 
					 ('. Filters::viewTime($dialog['time']) .')<br/> 
					'. Filters::output($dialog['text']) .' 
				</div>'; 
			}
			$pages->view();
		}
	}
	
	public function actionRecovery()
	{
		if(user::loged())
		{
			$this->redirect('/');
		}
		
		$db = PerfDb::init();
		$alert = (isset($_GET['alert']) ? 1 : 0);
		if(isset($_GET['getNewPass']))
		{
			$nick = Filters::input($_POST['nick']);
			$mail = Filters::input($_POST['mail']);
			// echo $mail;
			if($db->query("SELECT * FROM `users` WHERE `nick` = '$nick' AND `email` = '$mail'")->rowCount() == 1)
			{
				$tmpHash = $db->query("SELECT password FROM `users` WHERE `nick` = '$nick' AND `email` = '$mail'")->fetchColumn();
				$Mailer = new Mailer('UTF-8');
				$Mailer->From('no-reply@'.$_SERVER['HTTP_HOST']);
				$Mailer->To($nick.';'.$mail);
				$Mailer->Subject("Password recovery | ".lang::get('recovery')." - ".$_SERVER['HTTP_HOST']);
				$Mailer->Body("Hello, ".$nick."!\n".
				lang::get('recovery_1')." ".$_SERVER['HTTP_HOST']."\n".
				lang::get('recovery_2')."\n http://".$_SERVER['HTTP_HOST']."/user/recovery?reset&tmphash=".$tmpHash."&email=".$mail."\n ".lang::get('recovery_3')."\n ".
				lang::get('recovery_4')." ".$_SERVER['HTTP_HOST']);
				$Mailer->Priority(3);
				$Mailer->Send();
				// print_r($Mailer->Get());
				$this->redirect('/user/recovery?rand='.rand(1234, 9999).'&alert'); 
			}
		}
		elseif(isset($_GET['reset']))
		{
			$oldPass = filters::input($_GET['tmphash']);
			$email = filters::input($_GET['email']);
			
			
			if($db->query("SELECT * FROM `users` WHERE `password` = '$oldPass' AND `email` = '$email'")->rowCount() == 1)
			{
				$this->getHeader(lang::get('recovery'));
				echo '<div class="post"><form action="/user/recovery?change&tmphash='.$oldPass.'&amp;email='.$email.'" method="post">
				<div class="post">
				'. lang::get('new_password') .'<br/>
				<input type="text" name="new_pass"/><br/>
				'. lang::get('retry_password') .':<br/>
				<input type="text" name="retry_pass"/><br/>
				<input type="submit" value="'. lang::get('save') .'" /><br/>
				</div>
				</form>
				</div>';
				$this->getFooter();
				exit;
			}
		}
		elseif(isset($_GET['change']))
		{
			$oldPass = filters::input($_GET['tmphash']);
			$email = filters::input($_GET['email']);
			$newPass = filters::input($_POST['new_pass']);
			$retryPass = filters::input($_POST['retry_pass']);
			if($newPass == $retryPass && mb_strlen($newPass) < 32)
			{
				$db->query("UPDATE `users` SET `password` = '". filters::crypt($newPass) ."' WHERE `email` = '$email'");
				$this->redirect('/user/login');
			}
		}
		
		$this->getHeader(lang::get('recovery'));
		$this->render('recovery', array('alert' => $alert));
		$this->getFooter();
	}
}
