function CommentController($scope, $routeParams, Alumno)
{
	var ts = Math.round((new Date()).getTime() / 1000);

	$scope.alumnoId     = $routeParams.id;
	$scope.actualAlumno = {};
	$scope.mensajes     = [];
	$scope.mensaje      = {
		mensaje: null
	};

	Alumno.find({_id: $scope.alumnoId})
        .then(function(data){
            $scope.actualAlumno = data.response[$scope.alumnoId];

            if ($scope.actualAlumno.mensajes)
            {
            	$scope.mensajes = $scope.actualAlumno.mensajes;
            }
        });

    $scope.send = function()
    {
    	Alumno.comentar($scope.alumnoId, $scope.mensaje)
    	.then(function(data){
    		if (data.response)
    		{
    			$scope.mensajes.push(data.response);
    			$scope.mensaje = {
    				mensaje: null
    			};
    		}
    	});
    }

    $scope.formatDate = function(unixtime) {

        var fecha = new Date(unixtime * 1000);
        var day = fecha.getDate();
        var month = fecha.getMonth();

        if (day < 10) {
            day = '0' + day;
        }

        if (month < 10) {
            month = '0' + month;
        }

        var format = day + '/' + month + '/' + fecha.getFullYear() + ' a las  ' + fecha.getUTCHours() + ':' + fecha.getUTCMinutes() + ':' + fecha.getUTCSeconds();
        return format;
    }
}