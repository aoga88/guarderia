requirejs.config({
	baseUrl: './',
	paths: {
		jqueryui:        'js/jquery-ui.min',
		notify:          'js/bootstrap-notify',
		jquery:          'vendor/jquery/dist/jquery',
		angular:         'vendor/angular/angular',
		angularCookies:  'vendor/angular-cookies/angular-cookies',
		angularResource: 'vendor/angular-resource/angular-resource',
		angularRoute:    'vendor/angular-route/angular-route',
		twbootstrap:     'vendor/bootstrap/dist/js/bootstrap'
	},

	shim: {
		'angular': {
			exports: 'angular',
		},

		'jquery': {
			exports: '$'
		},

		'jqueryui': {
			deps: ['jquery']
		},

		'angularRoute': {
			deps: ['angular']
		},

		'twbootstrap': {
			deps: ['jquery', 'jqueryui']
		},

		'notify': {
			deps: ['twbootstrap']
		},

		'js/ngapp/ui-date': {
			deps: ['angular']
		}
	},

	deps: ['js/sha1', 'notify', 'js/ngapp/services', 'js/ngapp/controllers', 'js/ngapp/app', 'js/ngapp/ui-date']
});