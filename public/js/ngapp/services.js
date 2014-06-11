requirejs([
    'angular',
    'js/ngapp/service/user',
    'js/ngapp/service/apps',
], function(angular){
    angular.module('appServices', [])
        .factory('User', ["$http", "$q", User])
        .factory('Apps', ["$http", "$q", Apps])
    ;
});