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
				'module'        => 'user',
				'controller'    => 'index',
				'action'        => 'current',
				'requiredLogin' => true,
			],

			'/user/:id' => [
				'module'        => 'user',
				'controller'    => 'index',
				'action'        => 'info',
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

			'/app/current' => [
				'module'		=> 'app',
				'controller'	=> 'index',
				'action'		=> 'current',
				'requiredLogin' => true
			],

			'/app/current-app' => [
				'module'		=> 'app',
				'controller'	=> 'index',
				'action'		=> 'currentApp',
				'requiredLogin' => true
			],

			'/alumno/current' => [
				'module'     => 'alumno',
				'controller' => 'index',
				'action'     => 'current',
				'requiredLogin' => true,
			],

			'/actividad/current' => [
				'module'     => 'actividad',
				'controller' => 'index',
				'action'     => 'current',
				'requiredLogin' => true,
			],

			'/maestro/current' => [
				'module'     => 'maestro',
				'controller' => 'index',
				'action'     => 'current',
				'requiredLogin' => true,
			],

			'/grupo/current' => [
				'module'     => 'grupo',
				'controller' => 'index',
				'action'     => 'current',
				'requiredLogin' => true,
			],

			'/alumno/cfoto/:id/:index/:resolution' => [
				'module'        => 'alumno',
				'controller'    => 'upload',
				'action'        => 'showContacto',
				'requiredLogin' => true
			],

			'/alumno/:id/contactos' => [
				'module'        => 'alumno',
				'controller'    => 'index',
				'action'        => 'contactos',
				'requiredLogin' => true
			],

			'/alumno/cfoto/:id/:index' => [
				'module'        => 'alumno',
				'controller'    => 'upload',
				'action'        => 'showContacto',
				'requiredLogin' => true
			],

			'/notifications'	=> [
				'module'		=> 'user',
				'controller'	=> 'index',
				'action'		=> 'notifications',
				'requiredLogin'	=> true
			],

			'/notifications/:id/read'	=> [
				'module'		=> 'user',
				'controller'	=> 'index',
				'action'		=> 'readNotification',
				'requiredLogin'	=> true
			],

			'/user/:id/picture/:resolution' => [
				'module'		=> 'user',
				'controller'	=> 'index',
				'action'		=> 'getPicture',
				'requiredLogin'	=> true
			]

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

			'/alumno/:id/asistencia' => [
				'module'        => 'alumno',
				'controller'    => 'index',
				'action'        => 'asistencia',
				'requiredLogin' => true
			],

			'/alumno/:id/salida' => [
				'module'        => 'alumno',
				'controller'    => 'index',
				'action'        => 'salida',
				'requiredLogin' => true
			],

			'/alumno/:id/comment' => [
				'module'        => 'alumno',
				'controller'    => 'index',
				'action'        => 'comment',
				'requiredLogin' => true
			],

			'/actividad' => [
				'module'        => 'actividad',
				'controller'    => 'index',
				'action'        => 'save',
				'requiredLogin' => true
			],

			'/maestro' => [
				'module'        => 'maestro',
				'controller'    => 'index',
				'action'        => 'save',
				'requiredLogin' => true
			],

			'/maestro/find' => [
				'module'        => 'maestro',
				'controller'    => 'index',
				'action'        => 'find',
				'requiredLogin' => true
			],

			'/grupo' => [
				'module'        => 'grupo',
				'controller'    => 'index',
				'action'        => 'save',
				'requiredLogin' => true
			],

			'/grupo/find' => [
				'module'        => 'grupo',
				'controller'    => 'index',
				'action'        => 'find',
				'requiredLogin' => true
			],

			'/registro' => [
				'module'        => 'registro',
				'controller'    => 'index',
				'action'        => 'save',
				'requiredLogin' => true
			],

			'/registro/actividades' => [
				'module'        => 'registro',
				'controller'    => 'index',
				'action'        => 'actividades',
				'requiredLogin' => true
			],

			'/pago' => [
				'module'        => 'app',
				'controller'    => 'pago',
				'action'        => 'pagar',
				'requiredLogin' => true
			],

			'/alumno/upload'    => [
				'module'        => 'alumno',
				'controller'    => 'upload',
				'action'        => 'contacto',
				'requiredLogin' => true
			],

			'/user/upload'    => [
				'module'        => 'user',
				'controller'    => 'index',
				'action'        => 'upload',
				'requiredLogin' => true
			],
		],

		'DELETE' => [
		]

	]
];