({
	//- paths are relative to this app.build.js file
	appDir: "./../public/",
	//- this is the directory that the new files will be. it will be created if it doesn't exist
	mainConfigFile: '../public/js/main.js',
	paths: {
		jqueryui:        './../public/js/jquery-ui.min',
		notify:          './../public/js/bootstrap-notify',
		jquery:          './../public/vendor/jquery/dist/jquery',
		angular:         './../public/vendor/angular/angular',
		angularCookies:  './../public/vendor/angular-cookies/angular-cookies',
		angularResource: './../public/vendor/angular-resource/angular-resource',
		angularRoute:    './../public/vendor/angular-route/angular-route',
		twbootstrap:     './../public/vendor/bootstrap/dist/js/bootstrap'
	},
	modules: [
		{
			name: "js/main"
		}
	],
	fileExclusionRegExp: /\.git/
})