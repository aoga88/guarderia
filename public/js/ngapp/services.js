requirejs([
    'angular',
    'js/ngapp/service/user',
    'js/ngapp/service/apps',
    'js/ngapp/service/alumno',
    'js/ngapp/service/actividad',
], function(angular){
    angular.module('appServices', [])
        .factory('User',      ["$http", "$q", User])
        .factory('Apps',      ["$http", "$q", Apps])
        .factory('Alumno',    ["$http", "$q", Alumno])
        .factory('Actividad', ["$http", "$q", Actividad])
    ;
});