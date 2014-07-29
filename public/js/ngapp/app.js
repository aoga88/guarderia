define([
    'jquery',
    'angular',
    'twbootstrap',
    'angularRoute'
], function($, angular){
    var app;

    function isLoggedIn($http, $q, $timeout, $location) {

        var defer = $q.defer();

        $http.get('/api/user/current')
        .success(function(data){
            defer.resolve(data);
        })
        .error(function(data) {
            $location.path('/login').replace();
            $timeout(defer.reject(data));
        });

        return defer.promise;
    }

    function isSuperAdmin($http, $q, $timeout, $location) {
        var defer = $q.defer();

        $http.get('/api/user/current')
        .success(function(data){
            var roles = data.response.roles;
            defer.resolve(data);
            return;
            if (roles.indexOf('superadmin') === -1)
            {
                $location.path('/login').replace();
                $timeout(defer.reject(data));
                return;
            } else {
                defer.resolve(data);
            }
        })
        .error(function(data) {
            $location.path('/login').replace();
            $timeout(defer.reject(data));
        });

        return defer.promise;
    }

    function isAdmin($http, $q, $timeout, $location) {
        var defer = $q.defer();

        $http.get('/api/user/current')
        .success(function(data){
            var roles = data.response.roles;
            defer.resolve(data);
            return;
            if (roles.indexOf('admin') === -1)
            {
                $location.path('/login').replace();
                $timeout(defer.reject(data));
                return;
            } else {
                defer.resolve(data);
            }
        })
        .error(function(data) {
            $location.path('/login').replace();
            $timeout(defer.reject());
        });

        return defer.promise;
    }

    function isPadre($http, $q, $timeout, $location) {
        var defer = $q.defer();

        $http.get('/api/user/current')
        .success(function(data){
            var roles = data.response.roles;
            defer.resolve(data);
            return;
            if (roles.indexOf('padre') === -1)
            {
                $location.path('/login').replace();
                $timeout(defer.reject(data));
                return;
            } else {
                defer.resolve(data);
            }
        })
        .error(function(data) {
            $location.path('/login').replace();
            $timeout(defer.reject());
        });

        return defer.promise;
    }

    function isMaestro($http, $q, $timeout, $location) {
        var defer = $q.defer();

        $http.get('/api/user/current')
        .success(function(data){
            var roles = data.response.roles;
            defer.resolve(data);
            return;
            if (roles.indexOf('maestro') === -1)
            {
                $location.path('/login').replace();
                $timeout(defer.reject(data));
                return;
            } else {
                defer.resolve(data);
            }
        })
        .error(function(data) {
            $location.path('/login').replace();
            $timeout(defer.reject());
        });

        return defer.promise;
    }

    angular.element(document).ready(function() {
        angular.module('app', ['ngRoute', 'appServices', 'ui.date'])

        .config(['$routeProvider', function($routeProvider) {
            $routeProvider
                  .when('/', {
                    resolve: {
                        isLoggedIn: isLoggedIn
                    },
                      templateUrl: 'views/dashboard.html',
                  })
                  .when('/profile', {
                    resolve: {
                        isLoggedIn: isLoggedIn
                    },
                      controller: ProfileController,
                      templateUrl: 'views/profile/index.html'
                  })
                  .when('/profile/new-password', {
                    resolve: {
                        isLoggedIn: isLoggedIn
                    },
                      controller: ProfileController,
                      templateUrl: 'views/profile/new-password.html'
                  })
                  .when('/login', {
                      controller: LoginController,
                      templateUrl: 'views/login.html'
                  })
                  .when('/guarderias', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isSuperAdmin: isSuperAdmin
                    },
                      controller: AplicationController,
                      templateUrl: 'views/guarderias/index.html'
                  })
                  .when('/guarderias/:id', {
                      resolve: {
                          isLoggedIn: isLoggedIn
                      },
                      controller: AplicationController,
                      templateUrl: 'views/guarderias/edit.html'
                  })
                  .when('/alumnos', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin
                    },
                      controller: AlumnosController,
                      templateUrl: 'views/alumnos/index.html'
                  })
                  .when('/alumnos/:id', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin
                    },
                      controller: AlumnosController,
                      templateUrl: 'views/alumnos/edit.html'
                  })
                  .when('/alumnos/:id/calendario', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin,
                        isPadre: isPadre,
                        isMaestro: isMaestro
                    },
                      controller: AlumnosController,
                      templateUrl: 'views/alumnos/calendario.html'
                  })
                  .when('/alumnos/:id/calendario-mobile', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin,
                        isPadre: isPadre,
                        isMaestro: isMaestro
                    },
                      controller: AlumnosController,
                      templateUrl: 'views/alumnos/calendario-mobile.html'
                  })
                  .when('/actividades', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin
                    },
                      controller: ActividadesController,
                      templateUrl: 'views/actividades/index.html'
                  })
                  .when('/maestro', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin
                    },
                      controller: MaestrosController,
                      templateUrl: 'views/maestro/index.html'
                  })
                  .when('/maestro/:id', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin
                    },
                      controller: MaestrosController,
                      templateUrl: 'views/maestro/edit.html'
                  })
                  .when('/grupos', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin
                    },
                      controller: GruposController,
                      templateUrl: 'views/grupos/index.html'
                  })
                  .when('/grupos/:id', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin
                    },
                      controller: GruposController,
                      templateUrl: 'views/grupos/edit.html'
                  })
                  .when('/registro', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin,
                        isPadre: isPadre,
                        isMaestro: isMaestro
                    },
                      controller: RegistroController,
                      templateUrl: 'views/registro/index.html'
                  })
                  .when('/pagos', {
                      resolve: {
                        isLoggedIn: isLoggedIn,
                        isAdmin: isAdmin
                    },
                      controller: AplicationController,
                      templateUrl: 'views/pagos/index.html'
                  })
                  .otherwise({
                      redirectTo: '/'
                  });
        }])

        .directive('datepicker', function() {
            return {
                restrict: 'A',
                require : 'ngModel',
                link : function (scope, element, attrs, ngModelCtrl) {
                        $(element).datepicker({
                            dateFormat:'yy/mm/dd',
                            changeYear: true,
                            changeMonth: true,
                            onSelect: function (date) {
                                ngModelCtrl.$setViewValue(date);
                                scope.$apply();
                            }
                        });
                }
            }
        });

        angular.bootstrap(document, ['app']);
    })
});