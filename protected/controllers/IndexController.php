<?php
/*
* Mainpage controller
* @package: PerfCMS
*/
class IndexController extends Controller
{
	public function actionIndex()
	{
		// print_r($_COOKIE);
		$lang = new Lang;
		$this->getHeader(array('title' => $lang::get('mainpage')));
		$this->render('main');
		$this->getFooter();
	}
		
	public function actionError()
	{
		$lang = new Lang;
		$this->getHeader(array('title' => $lang::get('error')));
		$this->render('error');
		$this->getFooter();
	}
		
	public function actionLanguage()
	{
		$user = new User();
		if($user->loged())
		{
			header('location: /user/settings/');
			exit;
		}
		if(isset($_GET['lang']) && !empty($_GET['lang']))
		{
			setcookie('lang', Filters::input($_GET['lang']), time()+60*60*24*365, '/');
			header('location: /');
			exit;
		}
		$lang = new Lang;
		$this->getHeader(array('title' => $lang::get('language')));
		$lngdir = scandir(APP_ROOT.'/protected/messages');
		$this->render('language', array('langs' => $lngdir));
		$this->getFooter();
	}
	
	public function actionType()
	{
		$content = (preg_match('/wap|touch|web/i', Filters::input($_GET['content'])) ? Filters::input($_GET['content']) : System::browserType());
		$return = (!empty($_GET['return']) ? filters::input($_GET['return']) : '/');
		setcookie('styleType', $content, time()+60*60*24*365, '/');
		header('location: '.$return);
		exit;
	}
	
	public function actionGuests()
	{
		$lang = new Lang();
		$db = PerfDb::init();
		$this->getHeader(array('title' => $lang::get('guests_online')), '/user/list::guests_online_location');
		$listAm = $db->query("SELECT * FROM `guests` WHERE `time` > '".(time()-300)."'")->rowCount();
		$pages = new Paginator($listAm, System::pages());
		global $start;
		$listArray = $db->query("SELECT * FROM `guests` WHERE `time` > '".(time()-300)."' ORDER BY time DESC LIMIT $start, 15");
		$this->render('guests', array('list' => $listArray, 'pages' => $pages));
		$this->getFooter();
	}
}
