<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	// 'basePath'=>dirname(__FILE__),
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Multichannel Webtool',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'sheep',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('*'),
		),
		
	),

	// application components
	'components'=>array(

		'user'=>array(
			// enable cookie-based authentication
			'class'=>'WebUser',
			'allowAutoLogin'=>true,
		),
		
		'curl' => array(
			'class' => 'ext.Curl',
			// 'options' => array(/.. additional curl options ../)
		),

		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				'<id:(login|logout)>'=>'site/<id>',
			),
			'showScriptName'=>false,
		),
		
		'utils'=>array(
			'class'=>'Utils',
		),

		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/database.php'),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'notAllowedStatus' => "Oops, you don't have permission to perform this task!",
		'list'=>array(
			'perPage'=>10,
			),
		'uploadCouponDir'=>'/var/www/html/multichannel/protected/uploads/coupons/',
		'uploadImageDir'=>'/var/www/html/multichannel/images/',
		'baseUploadUrl' => 'http://104.156.53.150/multichannel/images/',
		'updatePoints'  => 'http://104.156.53.150/multichannel-api/points/update.php',
		'reportPfx'     => 'Cust-Rpt',
		'reportCsv'     => (((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'))?
				     ('C:\wamp\www\Multichannel\protected\downloads'):
				     ('/var/www/html/multichannel/protected/downloads')),
		'api-url'       => array(
					'update_coupon' => 'http://104.156.53.150/multichannel-api/coupon/generate.php',
					'update_raffle' => 'http://104.156.53.150/multichannel-api/raffle_engine/update.php',
				),
	),
);
