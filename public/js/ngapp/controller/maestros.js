function MaestrosController($scope, $location, $timeout, $routeParams, Apps, Maestro, User)
{
	$scope.maestros = [];
	$scope.maestroId = $routeParams.id;

    User.getCurrent()
    .then(function(data) {
    	$scope.currentApp = data.response.app;
    	$scope.isAdmin = data.response.roles.indexOf('admin') !== -1;
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
			$location.path('/maestro/').replace();
			$scope.successSave = true;
			$timeout(function(){
                $scope.successSave = false
            }, 2000);
		});
	}

	$scope.buscaUsuario = function(email)
	{
		if (typeof email === 'undefined')
		{
			return false;
		}

		User.find({_id: email, app: $scope.currentApp})
		.then(function(data) {
			var mail = $scope.actualMaestro._id;
			if (typeof data.response[email] === 'undefined') {
				return false;
			}

			roles = data.response[email].roles;
			
			if ($scope.maestroId === '0' && roles.indexOf('maestro') !== -1) {
				$("#error").modal();
				$scope.actualMaestro = {};
				return false;
			}

			$scope.actualMaestro = data.response[email];
			$scope.actualMaestro.email = data.response[email]._id;
		});
	}
}