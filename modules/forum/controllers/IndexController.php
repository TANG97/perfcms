<?php
/*
* News Module
* @package: PerfCMS
*/

class IndexController extends Controller
{
	public function actionIndex()
	{
		$db = Yii::app()->db->createCommand();
		$this->getHeader(array('title' => Lang::get('forum_title', 'forum')));
		$forums = $db->select()->from('forum')->order('pos')->queryAll();
		$this->render('main', array('forums' => $forums));
		$this->getFooter();
	}
	
	public function actionAdd_Forum()
	{
		$user = new User;
		if($user->level() < 2)
		{
			$this->redirect('/forum');
		}
		$db = Yii::app()->db->createCommand();
		if(isset($_GET['add']) && !empty($_POST['name']))
		{
			$name = Filters::input($_POST['name']);
			$desc = (!empty($_POST['desc']) ? Filters::input($_POST['desc']) : '');
			if(!empty($name))
			{
				$pos = PerfDb::init()->query("SELECT id FROM `forum`")->rowCount();
				$db->insert('forum', array('name' => $name, 'desc' => $desc, 'pos' => $pos+1));
				$this->redirect('/forum/');
			}
		}
		$this->getHeader(array('title' => Lang::get('forum_add', 'forum')));
		$this->render('add');
		$this->getFooter();
	}
	
	public function actionForum()
	{
		$db = PerfDb::init();
		if(!isset($_GET['id']) or $db->query("SELECT * FROM `forum` WHERE `id` = '".Filters::num($_GET['id'])."'")->rowCount() == 0)
		{
			$this->redirect('/forum/');
		}
		
		$forumsNum = $db->query("SELECT * FROM `forum_c` WHERE `f_id` = '".Filters::num($_GET['id'])."'")->rowCount();
		$pages = new Paginator($forumsNum, System::pages());
		global $start;
		
		$forums = $db->query("SELECT * FROM `forum_c` WHERE `f_id` = '".Filters::num($_GET['id'])."' ORDER BY pos DESC LIMIT $start, ".System::pages()."");
		
		$this->getHeader(array('title' => $db->query("SELECT name FROM `forum` WHERE `id` = '".Filters::num($_GET['id'])."'")->fetchColumn() .' - '. Lang::get('forum_title', 'forum')));
		
		$this->render('forum', array('forums' => $forums, 'forum_title' => $db->query("SELECT name FROM `forum` WHERE `id` = '".Filters::num($_GET['id'])."'")->fetchColumn(), 'fid'=>Filters::num($_GET['id']), 'pages' => $pages));
		
		$this->getFooter();
	}
	
	public function internalCounter($fid = '')
	{
		$db = PerfDb::init();
		if($fid !='')
		{
			$sections = $db->query("SELECT * FROM `forum_c` WHERE `f_id` = '$fid'")->rowCount();
			return '('.$sections.')';
		}
	}
	public function actionAdd_Section()
	{
		$user = new User;
		if($user->level() < 2 || !isset($_GET['id']))
		{
			$this->redirect('/forum');
		}
		$db = Yii::app()->db->createCommand();
		if(isset($_GET['add']) && !empty($_POST['name']))
		{
			$name = Filters::input($_POST['name']);
			$desc = (!empty($_POST['desc']) ? Filters::input($_POST['desc']) : '');
			if(!empty($name))
			{
				$pos = PerfDb::init()->query("SELECT id FROM `forum`")->rowCount();
				$db->insert('forum_c', array('name' => $name, 'desc' => $desc, 'f_id' => Filters::num($_GET['id']), 'pos' => $pos+1));
				$this->redirect('/forum/forum-'.Filters::num($_GET['id']));
			}
		}
		$this->getHeader(array('title' => Lang::get('forum_add', 'forum')));
		$this->render('add_section', array('fid' => Filters::num($_GET['id'])));
		$this->getFooter();
	}
	
	public function actionSection()
	{
		$db = PerfDb::init();
		$lang = new Lang;
		if(!isset($_GET['id']) || $db->query("SELECT * FROM `forum_c` WHERE `id` = '". Filters::num($_GET['id']) ."'")->rowCount() != 1)
		{
			$this->redirect('/forum/');
		}
		$id = Filters::num($_GET['id']);
		
		$forumsNum = $db->query("SELECT * FROM `forum_t` WHERE `cat_id` = '". Filters::num($_GET['id']) ."'")->rowCount();
		$pages = new Paginator($forumsNum, System::pages());
		global $start;
		
		$this->getHeader(array('title' => $db->query("SELECT name FROM `forum_c` WHERE `id` = '".$id."'")->fetchColumn().' - '. $lang::get('forum_title', 'forum')));
		
		$topics = $db->query("SELECT * FROM `forum_t` WHERE `cat_id` = '". $id ."' ORDER BY attach DESC, time_last_post DESC LIMIT $start, ".System::pages()."");
		
		$this->render('section', array('topics' => $topics, 'fid' => $db->query("SELECT `f_id` FROM `forum_c` WHERE `id` = '$id'")->fetchColumn(), 'section_title' => $db->query("SELECT name FROM `forum_c` WHERE `id` = '".$id."'")->fetchColumn(), 'pages' => 'pages', 'id' => $id));
		
		$this->getFooter();
	}
	
	public function topicsCounter($sid = '')
	{
		$db = PerfDb::init();
		if($sid !='')
		{
			$topics = $db->query("SELECT * FROM `forum_t` WHERE `cat_id` = '$sid'")->rowCount();
			return '('.$topics.')';
		}
	}
	
	public function lastActivity($tid = '')
	{
		$db = PerfDb::init();
		if($tid !='')
		{
			$_info = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '".Filters::num($tid)."' ORDER BY time DESC LIMIT 1")->fetch();
			return '[<small class="gray">'.User::tnick($_info['user_id']).' / '. Filters::viewTime($_info['time']).'</small>]';
		}
	}
	
	public function actionAdd_topic()
	{
		$db = PerfDb::init();
		$lang = new Lang;
		
		if(!User::loged())
		{
			$this->redirect('/forum/');
		}
		
		if(!isset($_GET['id']) || $db->query("SELECT * FROM `forum_c` WHERE `id` = '". Filters::num($_GET['id']) ."'")->rowCount() != 1)
		{
			$this->redirect('/forum/');
		}
		
		$id = Filters::num($_GET['id']);
		$error = false;
		if(isset($_GET['add']))
		{
			if(isset($_POST['name']) && isset($_POST['text']))
			{
				$name = Filters::input($_POST['name']);
				$text = Filters::input($_POST['text']);
				
				if(empty($name))
				{
					$error = $lang::get('forum_topic_name_empty', 'forum');
				}
				elseif(mb_strlen($name) < 3 || mb_strlen($name) > 100)
				{
					$error = $lang::get('forum_topic_name_lenght', 'forum');
				}
				elseif(empty($text))
				{
					$error = $lang::get('forum_topic_text_empty', 'forum');
				}
				elseif(mb_strlen($text) < 3 || mb_strlen($text) > 15000)
				{
					$error = $lang::get('forum_topic_text_lenght', 'forum');
				}
			}	
			if($error == false)
			{
				$db->query("INSERT INTO `forum_t` SET `name` = '$name', `time_last_post` = '".time()."', `cat_id` = '$id', `user_last_post` = '". User::Id() ."', `attach` = '0', `closed` = '0', `time` = '".time()."', `user_id` = '". User::Id() ."'");
					
				$lastId = $db->lastInsertId();
					
				$db->query("INSERT INTO `forum_pt`(`name`, `text`, `time`, `user_id`, `cat_id`, `topic_id`, `file`, `file_size`, `edit_time`, `edit_user_id`, `count_edit`) VALUES('".$name."', '". $text ."', '". time() ."', '". User::Id() ."', '". $id ."', '". $lastId ."', '', 0, 0, 0, 0)");
					
				$this->redirect('/forum/topic-'.$lastId);
			}
		}
		$this->getHeader(array('title' => $lang::get('forum_add_topic', 'forum')));
		$this->render('add_topic', array('id' => $id, 'err' => $error));
		$this->getFooter();
	}
	
	public function actionTopic()
	{
		$db = PerfDb::init();
		$lang = new Lang;
		if(!isset($_GET['id']) || $db->query("SELECT * FROM `forum_t` WHERE `id` = '".Filters::num($_GET['id'])."'")->rowCount() != 1)
		{
			$this->redirect('/index/error');
		}
		
		$id = Filters::num($_GET['id']);
		
		$postsNum = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". $id ."'")->rowCount();
		$pages = new Paginator($postsNum, System::pages());
		global $start;
		
		$this->getHeader($db->query("SELECT `name` FROM `forum_t` WHERE `id` = '".$id."'")->fetchColumn().' - '. $lang::get('forum_title', 'forum'));
	
		$posts = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". $id ."' ORDER BY time ASC LIMIT $start, ".System::pages()."");
	
		$this->render('topic', array(
		'posts' => $posts,
		'topic' => $db->query("SELECT * FROM `forum_t` WHERE `id` = '".$id."'")->fetch(),
		'pages' => $pages
		));
		
		$this->getFooter();
	}
	
	public function fastForm()
	{
		if(User::loged() && User::$settings['fast_mess'])
		{
			echo '<div class="post">
			<form action="/forum/posting-7?add" method="post">
			<textarea name="text" rows="6" cols="26" id="area"></textarea><br/>
			<input type="submit" value="Додати" />
			</form>
			</div>';
		}
	}
		
	public function countPosts($tid = '')
	{
		$db = PerfDb::init();
		if($tid != '')
		{
			return $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '".$tid."'")->rowCount();
		}
	}
		
	public function topicNav($tid = '')
	{
		$db = PerfDb::init();
		if($tid != '')
		{
			$catId = $db->query("SELECT `cat_id` FROM `forum_t` WHERE `id` = '$tid'")->fetchColumn();
			$forumId = $db->query("SELECT `f_id` FROM `forum_c` WHERE `id` = '$catId'")->fetchColumn();
		
			return System::image('back.png').' <a href="/forum/section-'.$catId.'">'. $db->query("SELECT `name` FROM `forum_c` WHERE `id` = '$catId'")->fetchColumn().'</a><br/>
			'.System::image('back.png').' <a href="/forum/forum-'.$forumId.'">'.$db->query("SELECT `name` FROM `forum` WHERE `id` = '$forumId'")->fetchColumn().'</a><br/>';
		}
	}			
	
	public function actionPosting()
	{
		$db = PerfDb::init();
		$lang = new Lang;
		
		if(!isset($_GET['id']) || $db->query("SELECT * FROM `forum_t` WHERE `id` = '". Filters::num($_GET['id'])."'")->rowCount() !=1 || !User::loged())
		{
			$this->redirect('/index/error');
		}
		
		$id = Filters::num($_GET['id']);
		
		if($db->query("SELECT closed FROM `forum_t` WHERE `id` = '$id'")->fetchColumn() != 0)
		{
			$this->redirect('/forum/topic-'.$id.'?page-end');
		}
		
		if(isset($_GET['add']))
		{
			$text = mb_substr(Filters::input($_POST['text']), 0, 10000);
			if(!empty($text) && $db->query("SELECT * FROM `forum_pt` WHERE `time` > '". (time()-15) ."' AND `text` = '$text'")->rowCount() == 0)
			{
				$db->query("INSERT INTO `forum_pt` SET `text` = '$text', `time` = '". time() ."', `user_id` = '".User::Id()."', `topic_id` = '".$id."', `file` = '', `file_size` = '0', `edit_time` = '0', `edit_user_id` = '0', `count_edit` = '0', `cat_id` = '0', `name` = ''");
				
				$db->query("UPDATE `forum_t` SET `time_last_post` = '".time()."', `user_last_post` = '". User::Id() ."' WHERE `id` = '".$id."'");
				
				$this->redirect('/forum/topic-'.$id.'?page=end');
			}
			else
			{
				$this->redirect('/forum/topic-'.$id.'?page=end');
			}
		}
		
		$r = false;
		if(isset($_GET['to_id']) && !empty($_GET['to_id']))
		{
			$r = '[b]'.User::tnick(Filters::num($_GET['to_id'])).'[/b], ';
		}
		elseif(isset($_GET['quote_id']) && !empty($_GET['quote_id']))
		{
			$q_p = $db->query("SELECT `text` FROM `forum_pt` WHERE `id`='". Filters::num($_GET['quote_id'])."'")->fetchColumn();
			$q_t = $db->query("SELECT `time` FROM `forum_pt` WHERE `id`='". Filters::num($_GET['quote_id'])."'")->fetchColumn();
			$q_u = $db->query("SELECT `user_id` FROM `forum_pt` WHERE `id`='". Filters::num($_GET['quote_id'])."'")->fetchColumn();
			$r = "[quote][i][b]".User::tnick($q_u)."[/b] (".Filters::viewTime($q_t).")[/i]\n$q_p\n[/quote]\n";
		}
		
		$this->getHeader($lang::get('forum_add_post', 'forum').' - '.$db->query("SELECT `name` FROM `forum_t` WHERE `id` = '".$id."'")->fetchColumn().' - '.$lang::get('forum_title', 'forum'));
		
		$this->render('posting', array('id' => $id, 'reply' => $r));
		
		$this->getFooter();
	}
	
	public function actionEdit_post()
	{
		$db = PerfDb::init();
		$lang = new Lang;
		
		if(!isset($_GET['id']) || $db->query("SELECT * FROM `forum_pt` WHERE `id` = '". Filters::num($_GET['id'])."'")->rowCount() != 1)
		{
			$this->redirect('/index/error/');
		}
		
		$id = Filters::num($_GET['id']);
		
		$postData = $db->query("SELECT * FROM `forum_pt` WHERE `id` = '". Filters::num($_GET['id'])."'")->fetch();
		
		if(User::level() < 1 || User::Id() != $postData['user_id'])
		{
			$this->redirect('/forum/topic-'.$postData['topic_id'].'?page=end');
		}
		
		if(isset($_GET['action']))
		{
			if($_GET['action'] == 'delete')
			{
				$db->query("DELETE FROM `forum_pt` WHERE `id` = '$id' LIMIT 1");
				$_tmp = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` ORDER BY time DESC LIMIT 1")->fetch();
				$db->query("UPDATE `forum_t` SET `time_last_post` = '".time()."', `user_last_post` = '".$_tmp['user_id']."'");
				$this->redirect('/forum/topic-'.$postData['topic_id'].'?page=end');
			}
			elseif($_GET['action'] == 'edit')
			{				
				$text = mb_substr(Filters::input($_POST['text']), 0, 10000);
				if(!empty($text))
				{
					$db->query("UPDATE `forum_pt` SET `text` = '$text', `edit_time` = '". time() ."', `edit_user_id` = '". User::Id()."', `count_edit` = '". ($postData['count_edit']+1) ."' WHERE `id` = '$id'");
					$this->redirect('/forum/topic-'.$postData['topic_id'].'?page=end');
				}
			}
		}
		
		$this->getHeader($lang::get('forum_edit_post', 'forum').' - '.$lang::get('forum_title', 'forum'));
		$this->render('edit_post', array('post' => $postData));
		$this->getFooter();
	}
	
	public function actionTopic_actions()
	{
		$db = PerfDb::init();
		if(!isset($_GET['id']) || $db->query("SELECT * FROM `forum_t` WHERE `id` = '". Filters::num($_GET['id']) ."'")->rowCount() !=1 || !User::loged())
		{
			$this->redirect('/index/error');
		}
		
		$topic = $db->query("SELECT * FROM `forum_t` WHERE `id` = '". Filters::num($_GET['id']) ."'")->fetch();
		
		switch(Filters::input($_GET['do']))
		{
			case 'close':
				if(User::level() > 1 || User::Id() == $topic['user_id'])
				{
					$db->query("UPDATE `forum_t` SET `closed` = 1 WHERE `id` = '".$topic['id']."'");
					$this->redirect('/forum/topic-'.$topic['id'].'?page=end');
				}
			break;
			
			case 'open':
				if(User::level() > 1 || User::Id() == $topic['user_id'])
				{
					$db->query("UPDATE `forum_t` SET `closed` = 0 WHERE `id` = '".$topic['id']."'");
					$this->redirect('/forum/topic-'.$topic['id'].'?page=end');
				}
			break;
			
			case 'pin':
				if(User::level() > 1)
				{
					$db->query("UPDATE `forum_t` SET `attach` = 1 WHERE `id` = '".$topic['id']."'");
					$this->redirect('/forum/topic-'.$topic['id'].'?page=end');
				}
			break;
			
			case 'unpin':
				if(User::level() > 1)
				{
					$db->query("UPDATE `forum_t` SET `attach` = 0 WHERE `id` = '".$topic['id']."'");
					$this->redirect('/forum/topic-'.$topic['id'].'?page=end');
				}
			break;
			
			case 'delete':
				if(User::level() < 1)
				{
					$this->redirect('/forum/topic-'.$topic['id']);
				}
				if(isset($_POST['yes']))
				{
					$db->query("DELETE FROM `forum_t` WHERE `id` = '".$topic['id']."'");
					$db->query("DELETE FROM `forum_pt` WHERE `topic_id` = '".$topic['id']."'");
					$this->redirect('/forum/section-'.$topic['cat_id']);
				}
				elseif(isset($_POST['back']))
				{
					$this->redirect('/forum/topic-'.$topic['id'].'?page=end');
				}
				
				$this->getHeader(Lang::get('forum_delete_topic', 'forum'));
				$this->render('delete_topic', array('topic' => $topic));
				$this->getFooter();
			break;			
			
			case 'edit':
				if(User::level() < 1)
				{
					$this->redirect('/forum/topic-'.$topic['id']);
				}
				
				if(isset($_GET['save']))
				{
					$name = Filters::input($_POST['name']);
					$sect = Filters::num($_POST['section']);
					if(!empty($name) && $db->query("SELECT * FROM `forum_c` WHERE `id` = '$sect'")->rowCount() !=0)
					{
						$db->query("UPDATE `forum_t` SET `name` = '$name', `cat_id` = '".$sect."' WHERE `id` = '".$topic['id']."'");
						$db->query("UPDATE `forum_pt` SET `cat_id` = '".$sect."' WHERE `topic_id` = '".$topic['id']."'");
						$this->redirect('/forum/topic-'.$topic['id']);
					}
				}
				$this->getHeader(Lang::get('forum_edit', 'forum'));
				$sections = $db->query("SELECT * FROM `forum_c`");
				$this->render('edit_topic', array('topic' => $topic, 'sections' => $sections));
				$this->getFooter();
			break;
			
			default:
				$this->redirect('/forum/topic-'.$topic['id'].'?page=end');
			break;
		}
	}

	public function actionSection_actions()
	{
		$db = PerfDb::init();
		if(!isset($_GET['id']) || $db->query("SELECT * FROM `forum_c` WHERE `id` = '".Filters::num($_GET['id'])."'")->rowCount() !=1 || User::level() < 2)
		{
			$this->redirect('/forum/section-'.Filters::num($_GET['id']));
		}
		
		$section = $db->query("SELECT * FROM `forum_c` WHERE `id` = '".Filters::num($_GET['id'])."'")->fetch();
		
		switch(Filters::input($_GET['do']))
		{
			
			case 'edit':
				if(isset($_GET['save']))
				{
					$name = Filters::input($_POST['name']);
					$desc = Filters::input($_POST['desc']);
					$forum = Filters::num($_POST['forum']);
					if(!empty($name) && !empty($desc) && $db->query("SELECT * FROM `forum` WHERE `id` = '".$forum."'")->rowCount() !=0)
					{
						$db->query("UPDATE `forum_c` SET `name` = '".$name."', `desc` = '".$desc."', `f_id` = '".$forum."' WHERE `id` = '".Filters::num($_GET['id'])."'");
						$this->redirect('/forum/forum-'.$forum);
					}
				}
				$this->getHeader(Lang::get('forum_edit_section', 'forum'));
				$forums = $db->query("SELECT * FROM `forum`");
				$this->render('edit_section', array('s' => $section, 'forums' => $forums));
				$this->getFooter();
			break;
			
			case 'delete':
				if(isset($_POST['yes']))
				{
					$db->query("DELETE FROM `forum_pt` WHERE `cat_id` = '".$section['id']."'");
					// print_r($db->errorInfo());
					$db->query("DELETE FROM `forum_t` WHERE `cat_id` = '".$section['id']."'");
					// print_r($db->errorInfo());
					$db->query("DELETE FROM `forum_c` WHERE `id` = '".$section['id']."'");
					// print_r($db->errorInfo());
					$this->redirect('/forum/forum-'.$section['f_id']);
				}
				elseif(isset($_POST['back']))
				{
					$this->redirect('/forum/forum-'.$section['f_id']);
				}
				$this->getHeader(Lang::get('forum_delete_section', 'forum'));
				$this->render('delete_section', array('section' => $section));
				$this->getFooter();
			break;
						
			default:
				$this->redirect('/forum/section-'.Filters::num($_GET['id']));
			break;
		}
	}
	
	public function actionActions()
	{
		$db = PerfDb::init();
		if(!isset($_GET['id']) || $db->query("SELECT * FROM `forum` WHERE `id` = '".Filters::num($_GET['id'])."'")->rowCount() !=1 || User::level() < 2)
		{
			$this->redirect('/forum/forum-'.Filters::num($_GET['id']));
		}
		
		$forum = $db->query("SELECT * FROM `forum` WHERE `id` = '".Filters::num($_GET['id'])."'")->fetch();
		
		switch(Filters::input($_GET['do']))
		{
			
			case 'edit':
				if(isset($_GET['save']))
				{
					$name = Filters::input($_POST['name']);
					$desc = Filters::input($_POST['desc']);
					if(!empty($name) && !empty($desc))
					{
						$db->query("UPDATE `forum` SET `name` = '".$name."', `desc` = '".$desc."' WHERE `id` = '".Filters::num($_GET['id'])."'");
						$this->redirect('/forum/');
					}
				}
				$this->getHeader(Lang::get('forum_edit_forum', 'forum'));
				$this->render('edit_forum', array('forum' => $forum));
				$this->getFooter();
			break;
			
			case 'delete':
				if(isset($_POST['yes']))
				{
					$sections = $db->query("SELECT * FROM `forum_c` WHERE `f_id` = '". $forum['id'] ."'");
					foreach($sections as $section)
					{
						$db->query("DELETE FROM `forum_t` WHERE `cat_id` = '".$section['id']."'");
						$db->query("DELETE FROM `forum_pt` WHERE `cat_id` = '".$section['id']."'");
					}
					
					$db->query("DELETE FROM `forum_c` WHERE `f_id` = '".$forum['id']."'");
					$db->query("DELETE FROM `forum` WHERE `id` = '".$forum['id']."'");
					
					$this->redirect('/forum');
				}
				elseif(isset($_POST['back']))
				{
					$this->redirect('/forum');
				}
				$this->getHeader(Lang::get('forum_delete_forum', 'forum'));
				$this->render('delete_forum', array('forum' => $forum));
				$this->getFooter();
			break;
						
			default:
				$this->redirect('/forum/section-'.Filters::num($_GET['id']));
			break;
		}
	}
	
	public function actionNew()
	{
		$db = PerfDb::init();
		$postsNum = $db->query("SELECT * FROM `forum_pt` WHERE `time` > '". (time()-60*60*24) ."'")->rowCount();
		$pages = new Paginator($postsNum, System::pages());
		global $start;
		
		$posts = $db->query("SELECT * FROM `forum_pt` WHERE `time` > '". (time()-60*60*24) ."' ORDER BY time ASC LIMIT $start, ".System::pages()."");
		
		$this->getHeader(Lang::get('forum_new_posts', 'forum'));
		
		$this->render('new', array(
			'posts' => $posts,
			'pages' => $pages,
			// 'topic' => $db->query("SELECT * FROM `forum_t` WHERE `id` = '".$id."'")->fetch()
			)
		);
		
		$this->getFooter();
	}
	
	public function topicNew($tid = '')
	{
		$db = PerfDb::init();
		if($tid != '')
		{
			$_name = $db->query("SELECT name FROM `forum_t` WHERE `id` = '$tid'")->fetchColumn();
			$_new = $db->query("SELECT * FROM `forum_pt` WHERE `time` > '". (time()-60*60*24) ."' AND `topic_id` = '$tid'")->rowCount();
			return '<a href="/forum/topic-'.$tid.'?page=end">'.$_name.'</a> (<span class="green">+'.$_new.'</span>)';
		}
	}
}