<?php
/*
* Padmin Module
* @package: PerfCMS
*/

class IndexController extends Controller
{
	public function actionIndex()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 2) 
		{
			header('location: /');
			exit;
		}
		$lang = new Lang();
		$this->getHeader(array('title' => $lang::get('padmin_title', 'padmin')));
		$this->render('index');
		$this->getFooter();
	}
	
	public function actionSettings()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 2) 
		{
			header('location: /');
			exit;
		}
		$lang = new Lang();
		if(isset($_GET['act']) && $_GET['act'] == 'save')
		{
			$saveConf = PadminSettings::model()->findByPk('auto');
			$saveConf->pts = (empty($_POST['pts']) || $_POST['pts'] < 5 ? 10 : substr(Filters::num($_POST['pts']), 0, 2));
			$saveConf->open_site = substr(Filters::num($_POST['open_site']), 0, 1);
			$saveConf->open_reg = substr(Filters::num($_POST['site_signup']), 0, 1);
			$saveConf->access_site = substr(Filters::num($_POST['access_site']), 0, 1);
			$saveConf->language = substr(Filters::input($_POST['language']), 0, 7);
			// $saveConf->copyright = substr(Filters::input($_POST['copyright']), 0, 12);
			$saveConf->timezone = Filters::input($_POST['timezone']);
			$saveConf->keywords = Filters::input($_POST['keywords']);
			$saveConf->description = Filters::input($_POST['description']);
			$saveConf->save();
			//var_dump($saveConf);
			header('location: /padmin/index/settings?');
			exit();
		}
		$system = System::getSettings();
		$this->getHeader(array('title' => $lang::get('padmin_general_settings', 'padmin')));
		$langsDir = scandir(APP_ROOT.'/protected/messages');
		$this->render('settings', array('langs' => $langsDir, 'timezone' => $system['timezone']));
		$this->getFooter();
	}
	
	public function actionInfo()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 2) 
		{
			header('location: /');
			exit;
		}
		$lang = new Lang();
		$serverSoftware = explode('/', System::Server('server_software'));
		$this->getHeader(array('title' => $lang::get('padmin_system_info', 'padmin')));
		$this->render('info', array('YiiVersion' => Yii::getVersion(), 'PHPVersion' => PHP_VERSION, 'server_software' => $serverSoftware));
		$this->getFooter();
	}
	
	public function actionDesign()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 2) 
		{
			header('location: /');
			exit;
		}
		if(isset($_GET['act']) && $_GET['act'] == 'save')
		{
			$saveConf = PadminSettings::model()->findByPk('1');
			$saveConf->wap_theme = Filters::input($_POST['wap_theme']);
			$saveConf->web_theme = Filters::input($_POST['web_theme']);
			$saveConf->touch_theme = Filters::input($_POST['touch_theme']);
			$saveConf->active_switch = substr(Filters::num($_POST['active_switch']), 0, 1);
			$saveConf->default_type = substr(Filters::num($_POST['def_type']), 0, 1);
			$saveConf->save();
			header('location: /padmin/index/design?');
			exit();
		}
		$lang = new Lang();
		$wapDir = scandir(APP_ROOT .'/design/themes/wap');
		$webDir = scandir(APP_ROOT .'/design/themes/web');
		$touchDir = scandir(APP_ROOT .'/design/themes/touch');
		$this->getHeader(array('title' => $lang::get('padmin_design_settings', 'padmin')));
		$this->render('design', array('waps' => $wapDir, 'webs' => $webDir, 'touchs' => $touchDir));
		$this->getFooter();
	}
	
	public function actionStats()
	{
		$usr = new User();
		if(!$usr->loged() || $usr->level() < 2) 
		{
			header('location: /');
			exit;
		}
		$lang = new Lang;
		$db = PerfDb::init();
		$this->getHeader(array('title' => $lang::get('padmin_stats_info', 'padmin')));
		$users = $db->query("SELECT * FROM `users` WHERE `time` > '".(time()-60*60*24)."' LIMIT 5");
		$regs = $db->query("SELECT * FROM `users` LIMIT 5");
		$guests = $db->query("SELECT * FROM `guests` LIMIT 5");
		$all = $db->query("SELECT * FROM `users` WHERE `time` > '".(time()-60*60*24)."'")->rowCount();
		$this->render('stats', array('users' => $users, 'regs' => $regs, 'guests' => $guests, 'all' => $all));
		$this->getFooter();
	}
	
	public function actionThemes()
	{
		$this->getHeader(lang::get('padmin_themes_manager', 'padmin'));
		$this->render('themes');
		$this->getFooter();
	}
	
	public function listOfThemes()
	{
		echo '<div class="post"><a href="/padmin/index/themes?wap"><b>'.lang::get('padmin_themes_wap', 'padmin').'</b></a></br/>';
		if(isset($_GET['wap']))
		{
			$wapThemesDir = scandir(APP_ROOT.'/design/themes/wap');
			foreach($wapThemesDir as $wapTheme)
			{
				if($wapTheme != '.' && $wapTheme != '..' && $wapTheme != '.htaccess')
				{
					$wap = new Ini(APP_ROOT.'/design/themes/wap/'.$wapTheme.'/theme.ini');
					echo (file_exists(APP_ROOT.'/design/themes/wap/'.$wapTheme.'/preview.png') ? '<a href="/design/themes/wap/'.$wapTheme.'/preview.png"><img src="/design/themes/wap/'.$wapTheme.'/preview.png" alt="preview" height="150px" /></a><br/>' : false).'
						Name: '.$wap->read('theme_name').'<br/>
						'.($wap->read('theme_author') != false ? 'Author: '.$wap->read('theme_author') : false).' '.($wap->read('theme_site') !=false ? '(<a href="'.$wap->read('theme_site').'">'.str_replace('http://', '', $wap->read('theme_site').')').'</a>' : false);
				}
			}
		}
		echo '</div>';
		
		echo '<div class="post"><a href="/padmin/index/themes?touch"><b>'.lang::get('padmin_themes_touch', 'padmin').'</b></a><br/>';
		if(isset($_GET['touch']))
		{
			$touchThemesDir = scandir(APP_ROOT.'/design/themes/touch');
			foreach($touchThemesDir as $touchTheme)
			{
				if($touchTheme != '.' && $touchTheme != '..' && $touchTheme != '.htaccess')
				{
					$touch = new Ini(APP_ROOT.'/design/themes/touch/'.$touchTheme.'/theme.ini');
					echo (file_exists(APP_ROOT.'/design/themes/touch/'.$touchTheme.'/preview.png') ? '<a href="/design/themes/touch/'.$touchTheme.'/preview.png"><img src="/design/themes/touch/'.$touchTheme.'/preview.png" alt="preview" height="150px" /></a><br/>' : false).'
						Name: '.$touch->read('theme_name').'<br/>
						'.($touch->read('theme_author') != false ? 'Author: '.$touch->read('theme_author') : false).' '.($touch->read('theme_site') !=false ? '(<a href="'.$touch->read('theme_site').'">'.str_replace('http://', '', $touch->read('theme_site').')').'</a>' : false);
				}
			}
		}
		echo '</div>';
		
		echo '<div class="post"><a href="/padmin/index/themes?web"><b>'.lang::get('padmin_themes_web', 'padmin').'</b></a><br/>';
		if(isset($_GET['web']))
		{
			$webThemesDir = scandir(APP_ROOT.'/design/themes/web');
			foreach($webThemesDir as $webTheme)
			{
				if($webTheme != '.' && $webTheme != '..' && $webTheme != '.htaccess')
				{
					$web = new Ini(APP_ROOT.'/design/themes/web/'.$webTheme.'/theme.ini');
					echo (file_exists(APP_ROOT.'/design/themes/web/'.$webTheme.'/preview.png') ? '<a href="/design/themes/web/'.$webTheme.'/preview.png"><img src="/design/themes/web/'.$webTheme.'/preview.png" alt="preview" height="150px" /></a><br/>' : false).
						'Name: '.$web->read('theme_name').'<br/>
						'.($web->read('theme_author') != false ? 'Author: '.$web->read('theme_author') : false).' '.($web->read('theme_site') !=false ? '(<a href="'.$web->read('theme_site').'">'.str_replace('http://', '', $web->read('theme_site').')').'</a>' : false);
				}
			}
		}
		echo '</div>';
	}
		
	public function actionInstall_theme()
	{
		$themeError = '';
		
		if(isset($_GET['upload']) && $_FILES['themeZip'])
		{
			$zipDir = APP_ROOT.'/tmp/';
			$themeType = '';
			$themeCDir = '';
						
			$themeZip = new Upload($_FILES['themeZip']);
			if($themeZip->uploaded)
			{
				$themeZip->allowed = array('application/zip');
				$themeZip->process($zipDir);
				if($themeZip->processed)
				{
					$zip = new PclZip($zipDir.$themeZip->file_src_name);
					$config = $zip->extract(PCLZIP_OPT_BY_NAME, "theme.ini", PCLZIP_OPT_PATH, APP_ROOT.'/tmp/');
					if($config != 0)
					{
						$zip->extract(PCLZIP_OPT_BY_NAME, "theme.ini", PCLZIP_OPT_PATH, APP_ROOT.'/tmp/');
						$ini = new Ini($zipDir."theme.ini");
						if($ini->read('theme_name') == false)
						{
							$themeError = 'Error #1: Argument <b>theme_name</b> is missed!';
						}
						elseif($ini->read('theme_dir') == false)
						{
							$themeError = 'Error #2: Argument <b>theme_dir</b> is missed!';
						}
						elseif($ini->read('theme_type') == false)
						{
							$themeError = 'Error #3: Argument <b>theme_type</b> is missed!';
						}
						elseif($ini->read('theme_author') == false)
						{
							$ini->write('theme_author', 'Unknown');
							$ini->updateFile();
						}
						
						if($themeError == '')
						{
							$themeType = $ini->read('theme_type');
							$themeCDir = $ini->read('theme_dir');
						}
					}
					else
					{
						$themeError = 'Error #4: Theme\'s descriptor is missed! ('.$zip->errorInfo(true).')';
					}
										
					if($themeError == '')
					{
						$zip->extract(PCLZIP_OPT_PATH, APP_ROOT.'/design/themes/'.$themeType.'/'.$themeCDir);
						unlink($zipDir.$themeZip->file_src_name);
						unlink($zipDir.'theme.ini');
						$themeZip->clean();
						$this->redirect('/padmin/index/themes#'.$themeCDir);
					}
				}
			}
		}
		
		$this->getHeader(lang::get('padmin_themes_manager', 'padmin'));
		$this->render('install_theme');
		$this->getFooter();
	}
}
