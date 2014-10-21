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
	$scope.actualActividades = {};
	$scope.isAdmin        = false;
	$scope.contactoIndex = 0;

    User.getCurrent()
    .then(function(data) {
    	$scope.currentApp = data.response.app;
    	$scope.isAdmin = data.response.roles.indexOf('admin') !== -1;
    });

	$scope.list = function()
	{
		Alumno.current()
	    .then(function(data){
            angular.forEach(data.response, function(alumno){
            	Alumno.contactos(alumno._id.$id)
            	.then(function(data){
            		alumno.contactos = data.response;
                	$scope.alumnos.push(alumno);
            	});
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

            if (typeof $scope.actualAlumno.actividades !== 'undefined') {
            	angular.forEach($scope.actualAlumno.actividades, function(actividad) {
            		
            		var jsDate  = new Date(actividad.fecha.sec * 1000); 
            		var txtDate = jsDate.getDate() + '/' + ( jsDate.getMonth() + 1) + '/' + jsDate.getFullYear();
            		var hour    = jsDate.getHours() + ':00';
            		
            		if (typeof $scope.actualActividades[txtDate] === 'undefined')
            		{
            			$scope.actualActividades[txtDate] = {};
            		}

            		if (typeof $scope.actualActividades[txtDate][hour] === 'undefined')
            		{
            			$scope.actualActividades[txtDate][hour] = [];
            		}

            		actividad.hora = jsDate.getHours() + ':' + jsDate.getMinutes();
            		$scope.actualActividades[txtDate][hour].push(actividad);
            		
            	});	
            }
        });


		Alumno.contactos($scope.alumnoId)
		.then(function(data){
			$scope.contactos = data.response;
		});

		Alumno.grupos($scope.alumnoId)
		.then(function(data){
			$scope.grupos = data.response;
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
			$scope.contactos[$scope.actualContacto._id] = $scope.actualContacto;
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
			if (typeof data.response[$scope.actualContacto._id] !== 'undefined') {
				$scope.actualContacto = data.response[$scope.actualContacto._id];
			}
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
		var email = $scope.actualAlumno.contactos[index];

		angular.forEach($scope.contactos, function(contacto, index){
			if (contacto._id == email)
			{
				console.log($scope.contactos);
				delete $scope.contactos[index];
			}
		})

		$scope.actualAlumno.contactos.splice(index, 1);

	}

	$scope.verDetalle = function(item)
	{
		$scope.actualContacto = item;
		$scope.showContactForm = true;
	}

	$scope.getActividad = function(fecha, hora) {
		if (typeof $scope.actualActividades[fecha] === 'undefined')
		{
			return [];
		}

		if (typeof $scope.actualActividades[fecha][hora] === 'undefined')
		{
			return [];
		}

		return $scope.actualActividades[fecha][hora];
	}

	$scope.uploadImage = function(contacto) {
		contacto.showImageField = true;
	}

	$scope.cancelImage = function(contacto) {
		contacto.showImageField = false;
	}

	$scope.viewModal = function(index) {
		$scope.contactoIndex = index;
		$("#imgModal").modal();
	}
}