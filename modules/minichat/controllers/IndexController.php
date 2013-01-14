<?php
/*
* News Module
* @package: PerfCMS
*/

class IndexController extends Controller
{
	public function actionIndex()
	{
		$usr = new User;
		$lang = new Lang;
		$db = PerfDb::init();
		if(isset($_GET['add']))
		{
			$text = (!empty($_POST['text']) ? filters::input($_POST['text']) : null);
			if($text != null)
			{
				$user_id	= $usr::Id();
				$time		= time();
				$db->query("INSERT INTO `minichat` SET `text` = '$text', `user_id` = '$user_id', `time` = '$time'");
				header('location: /minichat');
				exit;
			}
		}
		elseif(isset($_GET['clear']) && $usr->level() > 1)
		{
			$db->query("TRUNCATE TABLE `minichat`");
			header('location: /minichat');
			exit;
		}
		elseif(isset($_GET['delete_message']) && !empty($_GET['this_id']) && $usr->level() > 1)
		{
			$this_id = filters::num($_GET['this_id']);
			$db->query("DELETE FROM `minichat` WHERE `id` = '$this_id'");
			header('location: /minichat');
			exit;
		}
		$this->getHeader(array('title' => $lang::get('minichat_title', 'minichat')), '/minichat::minichat_location');
		$mchatNum = $db->query("SELECT * FROM `minichat`")->rowCount();
		$pages = new Paginator($mchatNum, System::pages());
		global $start;
		$mchatArray = $db->query("SELECT * FROM `minichat` ORDER BY time DESC LIMIT $start, ".System::pages()."");
		$this->render('main', array('pages' => $pages, 'array' => $mchatArray));
		$this->getFooter();
	}
}