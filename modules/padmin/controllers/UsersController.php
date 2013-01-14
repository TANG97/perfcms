<?php

class UsersController extends Controller
{
		
	public function actionIndex()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 2) 
		{
			header('location: /');
			exit;
		}
		$lang = new Lang;
		$db = PerfDb::init();
		$this->getHeader(array('title' => $lang::get('padmin_users_manager', 'padmin')));
		$usersC = $db->query("SELECT * FROM `users`")->rowCount();
		$pages = new Paginator($usersC, System::pages());
		global $start;
		$users = $db->query("SELECT * FROM `users` ORDER BY id DESC LIMIT $start, ".System::pages()."");
		$this->render('users', array('users' => $users, 'pages' => $pages));
		$this->getFooter();
	}
	
	public function actionEdit()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 2 || !isset($_GET['id'])) 
		{
			header('location: /');
			exit;
		}
		
		$id = filters::num($_GET['id']);
		$lang = new Lang;
		$db = PerfDb::init();
		$user = $db->query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1")->fetch();
		if($user['level'] == 3)
		{
			header('location: /padmin/users/index');
			exit;
		}
		if(isset($_GET['act']) && $_GET['act'] == 'save')
		{
			$uset = EditProfile::model()->findByPk($id);
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
			header('location: /padmin/users/edit?id='.$id);
			exit;
		}

		$this->getHeader(array('title' => $lang::get('edit_profile')));
		$currentYear = date('Y')-6;
		$this->render('edit', array('user' => $user, 'currentYear' => $currentYear));
		$this->getFooter();
	}
	
	public function actionDelete()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 2 || !isset($_GET['id'])) 
		{
			header('location: /');
			exit;
		}
		
		$id = filters::num($_GET['id']);
		$lang = new Lang;
		$db = PerfDb::init();
		$user = $db->query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1")->fetch();
		
		if($user['level'] >= 2)
		{
			header('location: /padmin/users/index');
			exit;
		}
		
		if(isset($_GET['delete']))
		{
			if(isset($_POST['delete_this']))
			{
				$db->query("DELETE FROM `users` WHERE `id` = '$id' LIMIT 1");
				header('location: /padmin/users/index');
				exit;
			}
			else
			{
				header('location: /padmin/users/index');
				exit;
			}
		}
		
		$this->getHeader(array('title' => $lang::get('padmin_users_delete', 'padmin')));
		$this->render('delete', array('user' => $user));
		$this->getFooter();
	}
	
	public function actionChange()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 2 || !isset($_GET['id'])) 
		{
			header('location: /');
			exit;
		}
		
		$id = filters::num($_GET['id']);
		$lang = new Lang;
		$db = PerfDb::init();
		$user = $db->query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1")->fetch();
		
		if($user['level'] > 2)
		{
			header('location: /padmin/users/index');
			exit;
		}
		
		if(isset($_GET['save']))
		{
			$uset = EditProfile::model()->findByPk($id);
			$nick1 = substr(Filters::input($_POST['nickname']), 0, 25);
			$nick = (!empty($nick1) && mb_strlen($nick1) > 3 && mb_strlen($nick1) < 25 && preg_match('/[a-zA-Zà-ÿÀ-ß0-9\_\-\@\.]/i', $nick1) ? $nick1 : false);
			if($db->query("SELECT * FROM `users` WHERE `nick` = '$nick'")->rowCount() == 0 && $nick !=false)
			{
				$uset->nick = $nick;
			}
			$level = substr(Filters::num($_POST['level']), 0, 1);
			$uset->level = ($level < 0 ? 0 : ($level > 2 ? 2 : $level));
			$uset->save();
			header('location: /padmin/users/change?id='.$id);
			exit;
		}
		
		$this->getHeader(array('title' => $lang::get('padmin_users_edit', 'padmin')));
		$this->render('change', array('user' => $user));
		$this->getFooter();
	}
	
	public function actionBlock()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 1 || !isset($_GET['id'])) 
		{
			header('location: /');
			exit;
		}
		
		$id = filters::num($_GET['id']);
		$lang = new Lang;
		$db = PerfDb::init();
		$user = $db->query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1")->fetch();
		
		if($user['level'] > 1)
		{
			header('location: /padmin/users/index');
			exit;
		}
		
		if(isset($_GET['block']))
		{
			$uset = EditProfile::model()->findByPk($id);
			$block = substr(filters::num($_POST['time']), 0, 3);
			$uset->ban_time = time()+60*60*24*$block;
			$uset->ban_text = filters::input($_POST['info']);
			$uset->save();
			header('location: /padmin/users/index');
			exit;
		}
		
		$this->getHeader(array('title' => $lang::get('padmin_users_block', 'padmin')));
		$this->render('block', array('user' => $user));
		$this->getFooter();
	}
}