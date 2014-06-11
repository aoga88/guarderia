define([
	'/js/require.js',
	'angular',
	'app',
	'routes'
], function(requirejs, ng){
	'use strict';

	requirejs(['domReady!'], function(document) {
		ng.bootstrap(document, ['app']);
	});
});