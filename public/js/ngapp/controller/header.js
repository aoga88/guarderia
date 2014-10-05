function HeaderController($scope, $http, $q, $location, Alumno)
{
    $scope.menu         = '';
    $scope.isSuperAdmin = false;
    $scope.isAdmin      = false;
    $scope.isPadre      = false;
    $scope.isMaestro    = false;
    $scope.showMenu = false;

    var defer = $q.defer();

    $scope.toggleMenu = function(){
        var menuRes = $('#bs-example-navbar-collapse-1');

        if ($scope.showMenu == true)
        {
            $(menuRes).slideUp(800);
            $scope.showMenu = false;
        } else {
          $(menuRes).slideDown(800);
          $scope.showMenu = true;
        }
      }

    $http.get('/api/user/current')
    .success(function(data){
        defer.resolve(data);

        var roles = data.response.roles;

        $scope.currentUser = data.response;

        if (roles.indexOf('superadmin') !== -1) {
            $scope.isSuperAdmin = true;
        }

        if (roles.indexOf('admin') !== -1) {
            $scope.isAdmin = true;
        }

        if (roles.indexOf('maestro') !== -1) {
            $scope.isMaestro = true;
        }

        if (roles.indexOf('padre') !== -1) {
            $scope.isPadre = true;
        }

        /*Alumno.current()
            .then(function(data){
                angular.forEach(data.response, function(alumno, index){

                    notifications[index] = io.connect(ioServer + '/guarderia_' + index);

                    notifications[index].on('notification', function(msg) {
                        $('.bottom-right').notify({
                            message: { text: msg }
                          }).show(); 
                    });
                });
            })*/

    })
    .error(function(data) {
        $location.path('/login').replace();
        $timeout(defer.reject(data));
    });

    $scope.logout = function()
    {
        $http.get('/api/logout')
        .success(function() {
            //$location.path('/login').replace();
            //location.href = '/#/login';
            location.reload();
        });
    }

    $scope.$watch(function() {
        return $location.path();
     }, function(){
        if ($location.path().indexOf('/guarderias') !== -1)
        {
            $scope.menu = 'guarderias';
        }

        if ($location.path().indexOf('/alumnos') !== -1)
        {
            $scope.menu = 'alumnos';
        }

        if ($location.path().indexOf('/profile') !== -1)
        {
            $scope.menu = 'profile';
        }

        if ($location.path().indexOf('/maestro') !== -1)
        {
            $scope.menu = 'maestro';
        }

        if ($location.path().indexOf('/actividades') !== -1)
        {
            $scope.menu = 'actividades';
        }

        if ($location.path().indexOf('/grupos') !== -1)
        {
            $scope.menu = 'grupos';
        }

        if ($location.path().indexOf('/registro') !== -1)
        {
            $scope.menu = 'registro';
        }

        if ($location.path().indexOf('/pagos') !== -1)
        {
            $scope.menu = 'pagos';
        }

        if (!$scope.menu)
        {
            $scope.menu = 'home';
        }
     });
}