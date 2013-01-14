<?php
return array('showScriptName' => false,
        	'urlFormat' => 'path',
        	'rules' => array(
        		'<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
        		'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
        		'<controller:\w+>/' => '<controller>/index',
				'user/profile-<profile:(.*)>' => 'user/profile',
				'news/article-<id:(.*)>' => 'news/index/article',
				'news/category-<id:(.*)>' => 'news/index/category',
				'news/edit-<id:(.*)>' => 'news/index/edit',
				'news/editcat-<id:(.*)>' => 'news/index/edit_category',
				'news/comments-<id:(.*)>' => 'news/index/comments',
				'forum/<action:\w+>-<id:(.*)>' => 'forum/index/<action>',
        	),
        );