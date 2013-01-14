<?php
/*
* News Module
* @package: PerfCMS
*/

class IndexController extends Controller
{
	public function actionIndex()
	{
		$lang = new Lang;
		$db = PerfDb::init();
		$this->getHeader(array('title' => $lang::get('news_title', 'news')), '/news/index::news_location');
		$newsAm = $db->query("SELECT * FROM `news`")->rowCount();
		$pages = new Paginator($newsAm, System::pages());
		global $start;
		$newsArray = $db->query("SELECT * FROM `news` ORDER BY time DESC LIMIT $start, ".System::pages()."");
		$this->render('main', array('view' => $pages, 'array' => $newsArray));
		$this->getFooter();
	}
	
	public function actionAdd()
	{
		$usr = new User();
		if(!$usr->loged() && $usr->level() < 2) 
		{
			header('location: /');
			exit;
		}
		$lang = new Lang;
		$db = PerfDb::init();
		if(isset($_GET['create']))
		{
			if(!empty($_POST['name']) && !empty($_POST['text']))
			{
				$name = mb_substr(Filters::input($_POST['name']), 0, 250);
				$text = Filters::input($_POST['text']);
				$category = Filters::num($_POST['category']);
				$time = time();
				
			if(mb_strlen($name) > 2 && mb_strlen($text) > 4)
			{
				$db->query("INSERT INTO `news` SET `name` = '$name', `text` = '$text', `category_id` = '$category', `time` = '". time() ."', `user_id` = '". User::Id() ."', `image` = '". null ."'");
				// var_dump($db->errorInfo());
				if($_FILES['image'])
				{
					$photo = new Upload($_FILES['image']);
					if($photo->uploaded)
					{
						$photo->allowed = array('image/*');
						$photo->file_new_name_body 	= $db->lastInsertId();
						$photo->image_convert 	= 'jpg';
						$photo->image_resize	= true;
						$photo->process(APP_ROOT. '/cache/news/');
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
				}
			}
				header('location: /news/article-'.$db->lastInsertId());
				exit;
			}
		}
		$categories = $db->query("SELECT * FROM `news_categories`");
		$this->getHeader(array('title' => $lang::get('news_title', 'news')), '/news/index::news_location');
		$this->render('add', array('categories' => $categories));
		$this->getFooter();
	}
	
	public function actionArticle()
	{
		$lang = new Lang;
		$db = PerfDb::init();
		if($db->query("SELECT * FROM `news` WHERE `id` = '".Filters::num($_GET['id'])."'")->rowCount() == 0)
		{
			header('location: /index/error');
			exit;
		}
		$newsName = $db->query("SELECT name FROM `news` WHERE `id` = '".Filters::num($_GET['id'])."'")->fetchColumn();
		$newsImage = (file_exists(APP_ROOT.'/cache/news/'.Filters::num($_GET['id']).'.jpg') ? Filters::num($_GET['id']).'.jpg' : null);
		$getComments = $db->query("SELECT * FROM `news_comments` WHERE `news_id` = '".Filters::num($_GET['id'])."'")->rowCount();
		$array = $db->query("SELECT * FROM `news` WHERE `id` = '".Filters::num($_GET['id'])."'");
		$this->getHeader(array('title' => $newsName), '/news/article-'.Filters::num($_GET['id']).'::news_location');
		$this->render('article', array('array' => $array, 'image' => $newsImage, 'comments' => $getComments));
		$this->getFooter();
	}
	
	public function actionAdd_category()
	{
		$usr = new User();
		if(!$usr->loged() && $usr->settings['level'] < 2) 
		{
			header('location: /');
			exit;
		}
		$lang = new Lang;
		$db = PerfDb::init();
		if(isset($_GET['create']) && !empty($_POST['name']))
		{
			$name = substr(Filters::input($_POST['name']), 0, 100);
			$db->query("INSERT INTO `news_categories` SET `name` = '$name'");
			header('location: /news/index/categories');
			exit;
		}
		$this->getHeader(array('title' => $lang::get('news_add_category', 'news')), '/news/index::news_location');
		$this->render('add_category');
		$this->getFooter();
	}
	
	public function actionCategories()
	{
		$lang = new Lang;
		$db = PerfDb::init();
		$this->getHeader(array('title' => $lang::get('news_categories', 'news')), '/news/index::news_location');
		$nums = $db->query("SELECT * FROM `news_categories`")->rowCount();
		$pages = new Paginator($nums, System::pages());
		global $start;
		$array = $db->query("SELECT * FROM `news_categories` ORDER BY name ASC LIMIT $start, ".System::pages()."");
		$this->render('categories', array('array' => $array, 'pages' => $pages));
		$this->getFooter();
	}
	
	public function actionCategory()
	{
		$lang = new Lang;
		$db = PerfDb::init();
		$id = Filters::num($_GET['id']);
		$catname = $db->query("SELECT name FROM `news_categories` WHERE `id` = '$id'")->fetchColumn();
		$catCheck = $db->query("SELECT * FROM `news_categories` WHERE `id` = '$id'")->rowCount();
		$pages = new Paginator($catCheck, System::pages());
		global $start;
		if($catCheck == 0)
		{
			header('location: /index/error');
			exit;
		}
		$this->getHeader(array('title' => $catname), '/news/index::news_location');
		$array = $db->query("SELECT * FROM `news` WHERE `category_id` = '$id' ORDER BY name ASC LIMIT $start, ".System::pages()."");
		$this->render('category', array('array' => $array, 'pages' => $pages, 'name' => $catname, 'catid' => $id));
		$this->getFooter();
	}
	
	public function actionEdit()
	{
		$usr = new User();
		if(!$usr->loged() && $usr->level() < 2) 
		{
			header('location: /');
			exit;
		}
		$db = PerfDb::init();
		if($db->query("SELECT * FROM `news` WHERE `id` = '". Filters::num($_GET['id']) ."'")->rowCount() == 0)
		{
			header('location: /');
			exit;
		}
		$lang = new Lang;
		if(isset($_GET['save']))
		{
			if(!empty($_POST['name']) && !empty($_POST['text']))
			{
				$name = mb_substr(Filters::input($_POST['name']), 0, 250);
				$text = Filters::input($_POST['text']);
				$category = Filters::num($_POST['category']);
				$time = time();
			
				if(mb_strlen($name) > 2 && mb_strlen($text) > 4)
				{
					$db->query("UPDATE `news` SET `name` = '$name', `text` = '$text', `category_id` = '$category' WHERE `id` = '".Filters::num($_GET['id'])."'");
					// print_r($db->errorInfo());
					header('location: /news/article-'.Filters::num($_GET['id']));
					exit;
				}
			}
		}
		elseif(isset($_GET['delete']))
		{
			$db->query("DELETE FROM `news` WHERE `id` = '".Filters::num($_GET['id'])."'");
			if(file_exists(APP_ROOT. '/cache/news/'.Filters::num($_GET['id']).'.jpg'))
			{
				unlink(APP_ROOT. '/cache/news/'.Filters::num($_GET['id']).'.jpg');
			}
			header('location: /news/');
			exit;
		}
		$categories = $db->query("SELECT * FROM `news_categories`");
		$news = $db->query("SELECT * FROM `news` WHERE `id` = '". Filters::num($_GET['id']) ."' LIMIT 1")->fetch();
		$this->getHeader(array('title' => $lang::get('news_edit_article', 'news')), '/news/index::news_location');
		$this->render('editArticle', array('categories' => $categories, 'news' => $news));
		$this->getFooter();
	}
	
	public function actionEdit_category()
	{
		$usr = new User();
		if(!$usr->loged() && $usr->level() < 2) 
		{
			header('location: /');
			exit;
		}
		$db = PerfDb::init();
		if($db->query("SELECT * FROM `news_categories` WHERE `id` = '". Filters::num($_GET['id']) ."'")->rowCount() == 0)
		{
			header('location: /');
			exit;
		}
		$lang = new Lang;
		if(isset($_GET['save']))
		{
			if(!empty($_POST['name']))
			{
				$name = mb_substr(Filters::input($_POST['name']), 0, 250);
			
				if(mb_strlen($name) > 2)
				{
					$db->query("UPDATE `news_categories` SET `name` = '$name' WHERE `id` = '".Filters::num($_GET['id'])."'");
					// print_r($db->errorInfo());
					header('location: /news/category-'.Filters::num($_GET['id']));
					exit;
				}
			}
		}
		elseif(isset($_GET['delete']))
		{
			$db->query("DELETE FROM `news_categories` WHERE `id` = '".Filters::num($_GET['id'])."'");
			$db->query("DELETE FROM `news` WHERE `category_id` = '".Filters::num($_GET['id'])."'");
			header('location: /news/');
			exit;
		}
		$category = $db->query("SELECT * FROM `news_categories` WHERE `id` = '". Filters::num($_GET['id']) ."' LIMIT 1")->fetch();
		$this->getHeader(array('title' => $lang::get('news_edit_category', 'news')), '/news/index::news_location');
		$this->render('editCategory', array('category' => $category));
		$this->getFooter();
	}
	
	public function actionComments()
	{
		$lang = new lang;
		$db = PerfDb::init();
		$id = Filters::num($_GET['id']);
		$usr = new User();
		if(isset($_GET['comment']) && $usr->loged())
		{
			if($_GET['comment'] == 'add' && !empty($_POST['text']))
			{
				$text = Filters::input($_POST['text']);
				$time = time();
				$user_id = $usr->Id();
				$db->query("INSERT INTO `news_comments` SET `user_id` = '$user_id', `text` = '$text', `time` = '$time', `news_id` = '$id'");
				header('location: /news/comments-'.$id);
				exit;
			}
			elseif($_GET['comment'] == 'delete' && isset($_GET['this_id']))
			{
				$this_id = Filters::num($_GET['this_id']);
				$db->query("DELETE FROM `news_comments` WHERE `id` = '$this_id'");
				header('location: /news/comments-'.$id);
				exit;
			}
		}
		$count = $db->query("SELECT * FROM `news_comments` WHERE `news_id` = '$id'")->rowCount();
		$pages = new Paginator($count, System::pages());
		global $start;
		$details = $db->query("SELECT * FROM `news` WHERE `id` = '$id'")->fetch();
		$comments = $db->query("SELECT * FROM `news_comments` WHERE `news_id` = '$id' ORDER BY `time` DESC LIMIT $start, ".System::pages()."");
		$this->getHeader(array('title' => $lang::get('news_comments', 'news').' - '.$details['name']), '/news/comments-'.$id.'::news_location');
		$this->render('comments', array('pages' => $pages, 'array' => $comments, 'details' => $details));
		$this->getFooter();	
	}
}