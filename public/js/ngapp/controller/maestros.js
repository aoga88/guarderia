function MaestrosController($scope, $location, $timeout, $routeParams, Apps, Maestro, User)
{
	$scope.maestros = [];
	$scope.maestroId = $routeParams.id;

	Apps.getCurrent()
    .then(function(data) {
    	$scope.currentApp = data.response.app;
    });

    $scope.list = function()
	{
		Maestro.current()
	    .then(function(data){
            angular.forEach(data.response, function(maestro){
                $scope.maestros.push(maestro);
            });
	    });
	}

    $scope.load = function()
    {
    	if ($scope.maestroId === "0")
    	{
    		return false;
    	}

        Maestro.find({_id: $scope.maestroId})
        .then(function(data){
            $scope.actualMaestro = data.response[$scope.maestroId];
        });
    }

	$scope.addTel = function()
	{
		if (typeof $scope.actualMaestro === 'undefined')
		{
			$scope.actualMaestro = {};
		}

		if (typeof $scope.actualMaestro.telefonos === 'undefined')
		{
			$scope.actualMaestro.telefonos = [];
		}

		var index = $scope.actualMaestro.telefonos.indexOf($scope.actualTel);

		if (index !== -1)
		{
			alert("El telefono ya ha sido registrado");
			return false;
		}

		$scope.actualMaestro.telefonos.push($scope.actualTel);
		$scope.actualTel = null;
	}

	$scope.removeTel = function($index)
	{
		$scope.actualMaestro.telefonos.splice($index, 1);
	}

	$scope.save = function()
	{
		$scope.actualMaestro.app = $scope.currentApp;
		Maestro.save($scope.actualMaestro, $scope.maestroId)
		.then(function(data) {
			$location.path('/maestro/' + data.response._id.$id).replace();
			$scope.successSave = true;
			$timeout(function(){
                $scope.successSave = false
            }, 2000);
		});
	}

	$scope.buscaUsuario = function()
	{
		if (typeof $scope.actualMaestro.email === 'undefined')
		{
			return false;
		}

		User.find({_id: $scope.actualMaestro.email, app: $scope.currentApp})
		.then(function(data) {
			var mail = $scope.actualMaestro.email;
			if (typeof data.response[mail] === 'undefined') {
				$scope.actualMaestro = {
					email: mail
				};

				return false;
			}

			roles = data.response[mail].roles;
			
			if (roles.indexOf('maestro') !== -1) {
				$("#error").modal();
				$scope.actualMaestro = {};
				return false;
			}

			$scope.actualMaestro = data.response[mail];
			$scope.actualMaestro.email = data.response[mail]._id;
		});
	}
}