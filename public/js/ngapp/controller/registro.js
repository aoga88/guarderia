function RegistroController($scope, $location, $timeout, User, Grupo, Alumno, Actividad, Registro)
{
	$scope.isMaestro      = false;
	$scope.isAdmin        = false;
	$scope.isPadre        = false;
	$scope.grupos         = [];
	$scope.alumnos        = [];
	$scope.actividades    = [];
	$scope.actualRegistro = {};
	$scope.showGrupo      = true;
	$scope.showIndividual = false;

	User.getCurrent()
    .then(function(data) {
    	$scope.currentApp = data.response.app;

    	$scope.isMaestro = data.response.roles.indexOf('maestro') !== -1;
    	$scope.isAdmin = data.response.roles.indexOf('admin') !== -1;
    	$scope.isPadre = data.response.roles.indexOf('padre') !== -1;
    });

    $scope.list = function() {
    	Grupo.current()
	    .then(function(data){
            angular.forEach(data.response, function(grupo){
                $scope.grupos.push(grupo);
            });
	    });

	    Alumno.current()
	    .then(function(data){
            angular.forEach(data.response, function(alumno){
                $scope.alumnos.push(alumno);
            });
	    });

	    Actividad.current()
	    .then(function(data){
            angular.forEach(data.response, function(actividad){
                $scope.actividades.push(actividad);
            });
	    });
    }

    $scope.toggleAlumno = function(alumno) {
    	if (typeof $scope.actualRegistro.alumnos === 'undefined') {
    		$scope.actualRegistro.alumnos = [];
    	}

    	if (typeof alumno.selected === 'undefined') {
    		alumno.selected = true;
    		$scope.actualRegistro.alumnos.push(alumno._id.$id);
    		return true;
    	}

    	if (alumno.selected === true) {
    		alumno.selected = false;
    		var index = $scope.actualRegistro.alumnos.indexOf(alumno._id.$id);
    		$scope.actualRegistro.alumnos.splice(index, 1);
    	}else {
    		$scope.actualRegistro.alumnos.push(alumno._id.$id);
    		alumno.selected = true;
    	}
    }

    $scope.toggleGrupo = function(grupo) {
    	if (typeof $scope.actualRegistro.grupos === 'undefined') {
    		$scope.actualRegistro.grupos = [];
    	}

    	if (typeof grupo.selected === 'undefined') {
    		grupo.selected = true;
    		$scope.actualRegistro.grupos.push(grupo._id.$id);
    		return true;
    	}

    	if (grupo.selected === true) {
    		grupo.selected = false;
    		var index = $scope.actualRegistro.grupos.indexOf(grupo._id.$id);
    		$scope.actualRegistro.grupos.splice(index, 1);
    	}else {
    		$scope.actualRegistro.grupos.push(grupo._id.$id);
    		grupo.selected = true;
    	}
    }

    $scope.toggleActividad = function(actividad) {
    	if (typeof $scope.actualRegistro.actividades === 'undefined') {
    		$scope.actualRegistro.actividades = [];
    	}

    	if (typeof actividad.selected === 'undefined') {
    		actividad.selected = true;
    		$scope.actualRegistro.actividades.push(actividad.nombre);
    		return true;
    	}

    	if (actividad.selected === true) {
    		actividad.selected = false;
    		var index = $scope.actualRegistro.actividades.indexOf(actividad.nombre);
    		$scope.actualRegistro.actividades.splice(index, 1);
    	}else {
    		$scope.actualRegistro.actividades.push(actividad.nombre);
    		actividad.selected = true;
    	}
    }

    $scope.registroGrupal = function() {
    	$scope.showIndividual = false;
    	$scope.showGrupo      = true;
    	delete $scope.actualRegistro.alumnos;
    	angular.forEach($scope.alumnos, function(alumno) {
    		delete alumno.selected;
    	});
    }

    $scope.registroIndividual = function() {
    	$scope.showIndividual = true;
    	$scope.showGrupo      = false;
    	delete $scope.actualRegistro.grupos;
    	angular.forEach($scope.grupos, function(grupo) {
    		delete grupo.selected;
    	});
    }

    $scope.save = function() {
    	$scope.actualRegistro.app = $scope.currentApp;
    	Registro.save($scope.actualRegistro)
    	.then(function(data){
    		$scope.showSuccess = true;

            angular.forEach(data.response, function(actividades, alumnoId) {
                angular.forEach(actividades, function(actividad) {
                    notifications[alumnoId].emit('notification', 'Registro de actividad para ' 
                    + actividad.alumno + ': ' + actividad.actividad);
                })
            });

    		$timeout(function(){
                $scope.showSuccess = false;
                $scope.actualRegistro = {};
                angular.forEach($scope.grupos, function(grupo) {
		    		delete grupo.selected;
		    	});
		    	angular.forEach($scope.alumnos, function(alumno) {
		    		delete alumno.selected;
		    	});
		    	angular.forEach($scope.actividades, function(actividad) {
		    		delete actividad.selected;
		    	});
            }, 2000);
    	});
    }
}