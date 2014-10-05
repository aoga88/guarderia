function DashboardController($scope, $timeout, $http, Alumno)
{
	$scope.alumnos          = [];
	$scope.alumnoSearch     = '';
	$scope.actualAsistencia = {};
    $scope.isAsistencia     = true;
    $scope.haveSalida       = false;
    $scope.isAdmin          = false;

	Alumno.current()
    .then(function(data){
        angular.forEach(data.response, function(alumno){
            $scope.alumnos.push(alumno);
        });
    });

    $http.get('/api/user/current')
    .success(function(data){
        $scope.user = data.response;
        var roles = data.response.roles;

        if (roles.indexOf('admin') !== -1) {
            $scope.isAdmin = true;
        }
    });

    $scope.alumnoFilter = function(item) {
    	var nombreCompleto = item.nombre + ' ' + item.apPaterno;

    	if (typeof item.apMaterno !== 'undefined')
    	{
    		nombreCompleto = nombreCompleto + ' ' + item.apMaterno;
    	}

    	if (nombreCompleto.toLowerCase().indexOf($scope.alumnoSearch.toLowerCase()) !== -1)
    	{
    		return true;
    	} else {
    		return false;
    	}
    }

    $scope.detalleAlumno = function(alumno) {
        $scope.haveSalida = false;
    	$scope.alumnoDetalle = alumno;
    	$scope.actualAsistencia = {};

        var haveAsistencia = false;
        angular.forEach(alumno.actividades, function(value){
            var date = new Date(value.fecha.sec * 1000);
            var today = new Date();

            var dateStr = date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
            var todayStr = today.getDate() + '/' + (today.getMonth() + 1) + '/' + today.getFullYear();
            
            if (dateStr === todayStr) {
                if (typeof value.type !== 'undefined') {
                    if (value.type === 'asistencia') {
                        haveAsistencia = true;
                    }

                    if (value.type === 'salida') {
                        $scope.haveSalida = true;
                    }
                }
            }
        });

        if (haveAsistencia) {
            $scope.isAsistencia = false;
        } else {
            $scope.isAsistencia = true;
        }
    }

    $scope.seleccionaContacto = function(contacto)
    {
    	$scope.actualAsistencia.contacto_id = contacto._id;
        $scope.actualAsistencia.contacto_name = contacto.name + ' ' + contacto.apPaterno + ' ' + contacto.apMaterno;
    }

    $scope.asistencia = function() {
    	Alumno.asistencia($scope.alumnoDetalle._id.$id, $scope.actualAsistencia)
    	.then(function(){
    		$scope.actualAsistencia = {};
    		$scope.alumnoDetalle = {};

            $scope.alumnos = [];
            Alumno.current()
            .then(function(data){
                angular.forEach(data.response, function(alumno){
                    $scope.alumnos.push(alumno);
                });
            });
    	});
    }

    $scope.salida = function() {
        Alumno.salida($scope.alumnoDetalle._id.$id, $scope.actualAsistencia)
        .then(function(){
            $scope.actualAsistencia = {};
            $scope.alumnoDetalle = {};

            $scope.alumnos = [];
            Alumno.current()
            .then(function(data){
                angular.forEach(data.response, function(alumno){
                    $scope.alumnos.push(alumno);
                });
            });
        });
    }

    $scope.puedeSalida = function()
    {
        var tieneContacto = false;

        if (typeof $scope.actualAsistencia.contacto_id !== 'undefined')
        {
            tieneContacto = true;
        }

        if (tieneContacto == false || $scope.haveSalida == true)
        {
            return true;
        }

        return false;
    }
}