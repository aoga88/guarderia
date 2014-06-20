function AlumnosController($scope, $location, $routeParams, $timeout, Alumno, User, Apps)
{
	$scope.alumnos      = [];
	$scope.currentUser  = {};
	$scope.alumnoId     = $routeParams.id;
	$scope.tiposSangre  = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
	$scope.actualAlumno = {
		papaTel: [],
		mamaTel: []
	};
	$scope.papaTel      = null;
	$scope.mamaTel      = null;
	$scope.sucessSave   = false;
	$scope.showContactForm = false;

	Apps.getCurrent()
    .then(function(data) {
    	$scope.currentApp = data.response.app;
    });

	$scope.list = function()
	{
		Alumno.current()
	    .then(function(data){
            angular.forEach(data.response, function(alumno){
                $scope.alumnos.push(alumno);
            });
	    });
	}

	$scope.load = function()
    {
    	if ($scope.alumnoId === "0")
    	{
    		$scope.actualAlumno = {
    			contactos: []
    		};
    		return false;
    	}

        Alumno.find({_id: $scope.alumnoId})
        .then(function(data){
            $scope.actualAlumno = data.response[$scope.alumnoId];
        });
    }

	$scope.addTel = function()
	{
		if (typeof $scope.actualContacto === 'undefined')
		{
			$scope.actualContacto = {};
		}

		if (typeof $scope.actualContacto.telefonos === 'undefined')
		{
			$scope.actualContacto.telefonos = [];
		}

		var index = $scope.actualContacto.telefonos.indexOf($scope.actualTel);

		if (index !== -1)
		{
			alert("El telefono ya ha sido registrado");
			return false;
		}

		$scope.actualContacto.telefonos.push($scope.actualTel);
		$scope.actualTel = null;
	}

	$scope.addContacto = function()
	{
		if (typeof $scope.actualAlumno.contactos === 'undefined')
		{
			$scope.actualAlumno.contactos = [];
		}

		var index = $scope.actualAlumno.contactos.indexOf($scope.actualContacto);

		if (index === -1)
		{
			$scope.actualAlumno.contactos.push($scope.actualContacto);	
		}
		
		$scope.actualContacto  = {};
		$scope.showContactForm = false;
	}

	$scope.removeTel = function($index)
	{
		$scope.actualContacto.telefonos.splice($index, 1);
	}

	$scope.buscaUsuario = function()
	{
		if (typeof $scope.actualContacto._id === 'undefined')
		{
			return false;
		}

		User.find({_id: $scope.actualContacto._id, app: $scope.currentApp})
		.then(function(data) {
			$scope.actualContacto = data.response[$scope.actualContacto._id];
		});
	}

	$scope.save = function()
	{
		Alumno.save($scope.actualAlumno, $scope.alumnoId)
		.then(function(data) {
			$location.path('/alumnos/' + data.response._id.$id).replace();
			$scope.successSave = true;
			$timeout(function(){
                $scope.successSave = false
            }, 2000);
		});
	}

	$scope.eliminarContacto = function(index)
	{
		$scope.actualAlumno.contactos.splice(index, 1);
	}

	$scope.verDetalle = function(item)
	{
		$scope.actualContacto = item;
		$scope.showContactForm = true;
	}
}