function ActividadesController($scope, $location, $timeout, Actividad, Apps, User)
{
	$scope.showForm    = false;
	$scope.actividades = [];

	User.getCurrent()
    .then(function(data) {
    	$scope.currentApp = data.response.app;
    	$scope.isAdmin = data.response.roles.indexOf('admin') !== -1;
    });

    User.getCurrent()
    .then(function(data) {
    	$scope.currentApp = data.response.app;
    	$scope.isAdmin = data.response.roles.indexOf('admin') !== -1;
    });

	$scope.list = function()
	{
		Actividad.current()
	    .then(function(data){
            angular.forEach(data.response, function(actividad){
                $scope.actividades.push(actividad);
            });
	    });
	}

	$scope.newActividad = function()
	{
		$scope.showForm        = true;
		$scope.actualActividad = {};
	}

	$scope.save = function()
	{
		$scope.actualActividad.app = $scope.currentApp;
		Actividad.save($scope.actualActividad)
		.then(function(data) {
			$scope.actividades.push(data.response);
			$scope.actualActividad = {};
			$scope.showForm = false;
		});
	}

	$scope.desactivar = function(actividad, id)
	{
		var entity    = actividad;
		var entityId  = id;
		entity.active = false;
		delete entity._id;

		Actividad.save(entity, entityId)
		.then(function(data) {
			actividad = data.response;
		})
	}

	$scope.activar = function(actividad)
	{
		var entity    = actividad;
		var entityId  = actividad._id.$id;
		entity.active = true;
		delete entity._id;

		Actividad.save(entity, entityId)
		.then(function(data) {
			actividad = data.response;
		});
	}
}