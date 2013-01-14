<?php
$getSys = new PerfSystem;
$dbData = include(APP_SYS.'/data/ini/db.php');
return array(
	'name' => 'PerfCMS Application',
	'basePath' => APP_ROOT .'/protected',
	'modulePath' => APP_ROOT .'/modules',
	'runtimePath' => APP_ROOT .'/tmp',
	// 'preload' => array('log'),

	// autoloading model and component classes
	'import' => array(
		'application.components.*',
		'application.models.*',
		'application.models.modules.*',
		'application.widgets.*.*',
	),
	
	'onBeginRequest'=>create_function('$event', 'return ob_start("ob_gzhandler");'),
	'onEndRequest'=>create_function('$event', 'return ob_end_flush();'),
	
	'modules' => require('modules.php'),
	
	'defaultController' => 'Index',

	// application components
	'components' => array(
		'viewRenderer' => array(
			'class'=>'application.extensions.twig.ETwigViewRenderer',
			'fileExtension' => '.ptf',
			'options' => array(
				'autoescape' => true,
			),
			'globals' => array(
				'html' => 'CHtml'
			),
			'functions' => array(
				'rot13' => 'str_rot13',
			),
			'filters' => array(
				'jencode' => 'CJSON::encode',
			),
		),
		'themeManager' => array(
			'basePath' => APP_ROOT.'/design/themes/'.$getSys::browserType(),
			'baseUrl' => '/design/themes/'.$getSys::browserType(),
		),
		'errorHandler' => array(
            'errorAction' => 'index/error',
        ),
		'urlManager' => require('routes.php'),
		'db' => array(
            'class'=>'CDbConnection',
            'connectionString' => 'mysql:host='.$dbData['db_host'].';dbname='.$dbData['db_name'],
            'username' => $dbData['db_user'],
            'password' => $dbData['db_pass'],
            'emulatePrepare' => true,
			'charset' => 'utf8',
			'schemaCachingDuration' => 3600,
        ),
		// 'log' => array(
			// 'class' => 'CLogRouter',
			// 'routes' => array(
				// array(
					// 'class' => 'CFileLogRoute',
					// 'levels' => 'error, warning',
				// ),
			// ),
		// ),
	),
	'theme' => $getSys::getTheme(),
	'sourceLanguage' => PerfSystem::getSettings('language'),
	'language' => PerfSystem::CurrentLang(),
);
