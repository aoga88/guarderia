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
            $timeout(defer.reject(data));
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
                        isSuperAdmin: isAdmin
                    },
                      controller: AlumnosController,
                      templateUrl: 'views/alumnos/index.html'
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
                            dateFormat:'dd/mm/yy',
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