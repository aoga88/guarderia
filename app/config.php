<?php
return [
	'appName' => 'Guarderias',
	'auth'    => [
		'requiredLogin' => true,
		'sessionName'   => 'guarderiaosrest'
	],
	'routes'  => [

		'GET' => [
			'/user/current' => [
				'module'     => 'user',
				'controller' => 'index',
				'action'     => 'current',
				'requiredLogin' => true,
			],

			'/user/:id' => [
				'module' => 'user',
				'controller' => 'index',
				'action' => 'info',
				'requiredLogin' => true,
			],

			'/logout' => [
				'module'        => 'user',
				'controller'    => 'index',
				'action'        => 'logout',
				'requiredLogin' => false,
			],

			'/app/:id/users' => [
				'module'		=> 'user',
				'controller'	=> 'app',
				'action'		=> 'getFromApp',
				'requiredLogin' => true
			],

			'/app/:id/admins' => [
				'module'		=> 'user',
				'controller'	=> 'app',
				'action'		=> 'getAdminsFromApp',
				'requiredLogin' => true
			],

			'/alumno/current' => [
				'module'     => 'alumno',
				'controller' => 'index',
				'action'     => 'current',
				'requiredLogin' => true,
			],

		],

		'POST' => [
			'/login' => [
				'module'        => 'user',
				'controller'    => 'index',
				'action'        => 'login',
				'requiredLogin' => false,
			],

			'/app' => [
				'module'        => 'app',
				'controller'    => 'index',
				'action'        => 'save',
				'requiredLogin' => true
			],

			'/user' => [
				'module'        => 'user',
				'controller'    => 'index',
				'action'        => 'save',
				'requiredLogin' => true
			],

			'/user/new' => [
				'module'        => 'user',
				'controller'    => 'index',
				'action'        => 'newUser',
				'requiredLogin' => true
			],

			'/app/find' => [
				'module'        => 'app',
				'controller'    => 'index',
				'action'        => 'find',
				'requiredLogin' => true
			],

			'/user/find' => [
				'module'        => 'user',
				'controller'    => 'index',
				'action'        => 'find',
				'requiredLogin' => true
			],

			'/alumno/find' => [
				'module'        => 'alumno',
				'controller'    => 'index',
				'action'        => 'find',
				'requiredLogin' => true
			],

			'/alumno' => [
				'module'        => 'alumno',
				'controller'    => 'index',
				'action'        => 'save',
				'requiredLogin' => true
			],
		],

		'DELETE' => [
		]

	]
];