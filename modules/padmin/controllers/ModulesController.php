<?php
class ModulesController extends Controller
{
	public function actionIndex()
	{
		$this->getHeader(Lang::get('padmin_modules_manager', 'padmin'));
		$this->render('index');
		$this->getFooter();
	}
	
	public function actionList()
	{
		$this->getHeader(Lang::get('padmin_modules_manager', 'padmin'));
		$this->render('list');
		$this->getFooter();
	}
	
	public function listOfModules()
	{
		foreach(Yii::app()->modules as $module => $name)
		{
			if($module != '.' && $module != '..' && $module != '.htaccess')
			{
				$ini = new Ini(APP_ROOT.'/modules/'.$module.'/config.ini');
				echo '<div class="post"><b>'.$ini->read('name').' </b> ('.Lang::get($ini->read('code_name').'_title', $ini->read('code_name')).')<br/>
				'.($ini->read('author') != false ? 'Author: '.$ini->read('author') : false).' '.($ini->read('link') !=false ? '(<a href="'.$ini->read('link').'">'.str_replace('http://', '', $ini->read('link').')').'</a>' : false).'<br/>
				'.($ini->read('version') !=false ? 'Version: '.$ini->read('version') : false).' 
				</div>';
			}
		}
	}
	
	public function actionSettings()
	{
		if(isset($_GET['save']))
		{
			$arrayOfModules = scandir(APP_ROOT.'/modules');
			$moduleString = Filters::input($_GET['save']);
			if($moduleString != 'padmin' && in_array($moduleString, $arrayOfModules))
			{
				$access = Filters::num($_POST['access']);
				$status = Filters::num($_POST['status']);
				if($access > 2 || $access < 0) $access = 1;
				if($status > 1 || $status < 0) $status = 1;
				$ini = new Ini(APP_ROOT.'/modules/'.$moduleString.'/config.ini');
				$ini->write('access', $access);
				$ini->write('open', $status);
				$ini->updateFile();
				$this->redirect('/padmin/modules/settings?'.$moduleString);
			}
		}
		$this->getHeader(Lang::get('padmin_modules_settings', 'padmin'));
		$this->render('settings');
		$this->getFooter();
	}
	
	public function modulesSettings()
	{
		foreach(Yii::app()->modules as $module => $name)
		{
			if($module != '.' && $module != '..' && $module != '.htaccess' && $module !='padmin')
			{
				$ini = new Ini(APP_ROOT.'/modules/'.$module.'/config.ini');
				echo '<div class="post"><a href="/padmin/modules/settings?'.$module.'"><b>'.$ini->read('name').' </b></a> ('.Lang::get($ini->read('code_name').'_title', $ini->read('code_name')).')<br/>';
				if(isset($_GET[Filters::input($module)]))
				{
					echo '<form action="/padmin/modules/settings?save='.$module.'" method="post">';
					echo Lang::get('padmin_modules_status', 'padmin').': <select name="status">
					<option value="1"'.($ini->read('open') == 1 ? ' selected="selected"' : false).'>'. Lang::get('padmin_modules_opened', 'padmin').'</option>
					<option value="0"'.($ini->read('open') == 0 ? ' selected="selected"' : false).'>'. Lang::get('padmin_modules_closed', 'padmin').'</option>
					</select><br/>
					'.Lang::get('padmin_modules_access', 'padmin').':
					<select name="access">
					<option value="0"'.($ini->read('access') == 0 ? ' selected="selected"' : false).'>'. Lang::get('padmin_modules_all', 'padmin').'</option>
					<option value="1"'.($ini->read('access') == 1 ? ' selected="selected"' : false).'>'. Lang::get('padmin_modules_users', 'padmin').'</option>
					<option value="2"'.($ini->read('access') == 2 ? ' selected="selected"' : false).'>'. Lang::get('padmin_modules_admins', 'padmin').'</option>
					</select><br/>
					<input type="submit" value="'.Lang::get('save').'" />';
					echo '</form>';
				}
				echo '</div>';
			}
		}
	}
	
	public function actionInstall()
	{
		$moduleError = '';
		
		if(isset($_GET['upload']) && $_FILES['moduleZip'])
		{
			$zipDir = APP_ROOT.'/tmp/';
			$moduleCName = '';
						
			$moduleZip = new Upload($_FILES['moduleZip']);
			if($moduleZip->uploaded)
			{
				$moduleZip->allowed = array('application/zip');
				$moduleZip->process($zipDir);
				if($moduleZip->processed)
				{
					$zip = new PclZip($zipDir.$moduleZip->file_src_name);
					$config = $zip->extract(PCLZIP_OPT_BY_NAME, "config.ini", PCLZIP_OPT_PATH, APP_ROOT.'/tmp/');
					if($config != 0)
					{
						$zip->extract(PCLZIP_OPT_BY_NAME, "config.ini", PCLZIP_OPT_PATH, APP_ROOT.'/tmp/');
						$ini = new Ini($zipDir."config.ini");
						if($ini->read('code_name') == false)
						{
							$moduleError = 'Error #1: Argument <b>code_name</b> is missed!';
						}
						elseif($ini->read('name') == false)
						{
							$moduleError = 'Error #2: Argument <b>name</b> is missed!';
						}
						elseif($ini->read('access') == false)
						{
							$ini->write('access', 1);
							$ini->updateFile();
						}
						elseif($ini->read('open') == false)
						{
							$ini->write('open', 1);
							$ini->updateFile();
						}
						elseif($ini->read('counter') == false)
						{
							$ini->write('counter', 'false');
							$ini->updateFile();
						}
						
						if($moduleError == '')
						{
							$moduleCName = $ini->read('code_name');
						}
					}
					else
					{
						$moduleError = 'Error #3: Configuration of module missed! ('.$zip->errorInfo(true).')';
					}
					
					$importSql = $zip->extract(PCLZIP_OPT_BY_NAME, $moduleCName'_tables.sql', PCLZIP_OPT_PATH, APP_ROOT.'/tmp/');
					if($importSql != 0)
					{
						$db = PerfDb::init();
						
						$zip->extract(PCLZIP_OPT_BY_NAME, $moduleCName'_tables.sql', PCLZIP_OPT_PATH, APP_ROOT.'/tmp/');
						
						$importTables = file_get_contents(APP_ROOT.'/tmp/'.$moduleCName'_tables.sql');
						
						$imports = explode('-- --------------------------------------------------------', $importTables);
						
						foreach($imports as $import)
						{
							$db->query(trim($import));
						}
						
						unlink($moduleCName'_tables.sql');
					}
					
					if($moduleError == '')
					{
						$zip->extract(PCLZIP_OPT_PATH, APP_ROOT.'/modules/'.$moduleCName);
						unlink($zipDir.$moduleZip->file_src_name);
						unlink($zipDir.'config.ini');
						$moduleZip->clean();
						$this->redirect('/padmin/modules/list#'.$moduleCName);
					}
				}
			}
		}
			
		$this->getHeader(lang::get('padmin_modules_install', 'padmin'));
		$this->render('install', array('errors' => $moduleError));
		$this->getFooter();
	}	
}