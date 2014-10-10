function TopHeaderController($scope, $http, $q, $location, $interval, Alumno, Notificacion)
{
	$scope.notificaciones = [];
	
	Notificacion.current()
    .then(function(data){
        angular.forEach(data.response, function(notif){
            $scope.notificaciones.push(notif);
        });
    });

    $interval(function(){
        var actual = $scope.notificaciones.length;

        Notificacion.current()
        .then(function(data){
            $scope.notificaciones = [];

            angular.forEach(data.response, function(notif){
                $scope.notificaciones.push(notif);
            });

            if ($scope.notificaciones.length !== actual)
            {
                $('.bottom-right').notify({
                    message: { text: 'Tiene notificaciones nuevas sin leer' }
                }).show();
            }

        });
    }, 10000);

    $scope.logout = function()
    {
        $http.get('/api/logout')
        .success(function() {
            location.reload();
        });
    }

    $scope.getDescriptionNotificacion = function(notificacion)
    {
        var texto = 'Sin descripcion disponible';

        if (notificacion.mensaje)
        {
            texto = notificacion.mensaje;
        }

        return texto;
    }

    $scope.getLinkNotificacion = function(notificacion)
    {
        var link = '/#/alumnos';

        if (notificacion.link)
        {
            link = notificacion.link;
        }

        return link;
    }

    $scope.marcarLeido = function(notificacion)
    {
        Notificacion.leido(notificacion)
        .then(function(data){
            var index = $scope.notificaciones.indexOf(notificacion);
            $scope.notificaciones.splice(index, 1);
        });
    }
}