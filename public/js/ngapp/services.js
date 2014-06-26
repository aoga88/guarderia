requirejs([
    'angular',
    'js/ngapp/service/user',
    'js/ngapp/service/apps',
    'js/ngapp/service/alumno',
    'js/ngapp/service/maestro',
    'js/ngapp/service/actividad',
    'js/ngapp/service/grupo',
    'js/ngapp/service/registro',
], function(angular){
    angular.module('appServices', [])
        .factory('User',      ["$http", "$q", User])
        .factory('Apps',      ["$http", "$q", Apps])
        .factory('Alumno',    ["$http", "$q", Alumno])
        .factory('Actividad', ["$http", "$q", Actividad])
        .factory('Maestro',   ["$http", "$q", Maestro])
        .factory('Grupo',     ["$http", "$q", Grupo])
        .factory('Registro',  ["$http", "$q", Registro])
    ;
});