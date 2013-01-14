<?php
/*
* Controller class
* @package: PerfCMS
*/
class Controller extends CController
{
	public function getHeader($params = array(), $location_id = '/index::site_somewhere_location')
	{
		User::setLocation($location_id, User::Id());
		
		if(is_array($params))
		{
			$this->renderFile(Yii::app()->theme->basePath.'/views/index/header.ptf', $params);
		}
		else
		{
			$this->renderFile(Yii::app()->theme->basePath.'/views/index/header.ptf', array('title' => $params));
		}
		
		$data = User::$data;
		
		if(User::loged() && $data['ban_time'] > time() && $data['ban_time'] != 0)
		{
			$this->render('block');
			$this->getFooter();
			exit;
		}
				
		if(isset($this->module->id))
		{
			$config = new Ini(APP_ROOT.'/modules/'.$this->module->id.'/config.ini');
			if($config->read('open') == 0)
			{
				echo '<div class="error">'. Lang::get('module_closed', 'main') .'</div>';
				echo '<div class="block"> '.System::image('back.png').' <a href="/">'.Lang::get('mainpage').'</a></div>';
				$this->getFooter();
				exit;
			}
			elseif($config->read('access') == 1 && !User::loged())
			{
				echo '<div class="error">'. Lang::get('module_for_authorised', 'main') .'</div>';
				echo '<div class="block"> '.System::image('back.png').' <a href="/">'.Lang::get('mainpage').'</a></div>';
				$this->getFooter();
				exit;
			}
			elseif($config->read('access') == 2 && User::level() < 2)
			{
				echo '<div class="error">'. Lang::get('module_for_administration', 'main') .'</div>';
				echo '<div class="block"> '.System::image('back.png').' <a href="/">'.Lang::get('mainpage').'</a></div>';
				$this->getFooter();
				exit;
			}
		}
	}
	
	public function getFooter($params = array())
	{
		$db = PerfDb::init();
		$users = $db->query("SELECT * FROM `users` WHERE `time` > '".(time()-300)."'")->rowCount();
		$guests = $db->query("SELECT * FROM `guests` WHERE `time` > '".(time()-300)."'")->rowCount();
		$this->renderFile(Yii::app()->theme->basePath.'/views/index/footer.ptf', array('users' => $users, 'guests' => $guests));
	}
	
	public function widgets($type = '')
	{
		if($type != '')
		{
			$widgets = scandir(APP_ROOT.'/protected/widgets');
			foreach($widgets as $widget)
			{
				if($widget != '.' && $widget != '..' && $widgets != '.htaccess')
				{
					$ini = parse_ini_file(APP_ROOT.'/protected/widgets/'.$widget.'/widget.ini');
					if($ini['widget_type'] == $type)
					{
						$this->widget('application.widgets.'.$widget.'.'.$widget);
					}
				}
			}
		}
	}
	
}
