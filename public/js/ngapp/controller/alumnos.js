function AlumnosController($scope, $location, $routeParams, $timeout, Alumno)
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
        Alumno.find({_id: $scope.alumnoId})
        .then(function(data){
            $scope.actualAlumno = data.response[$scope.alumnoId];
        });
    }

	$scope.addPapaTel = function()
	{
		if (typeof $scope.actualAlumno.papaTel === 'undefined')
		{
			$scope.actualAlumno.papaTel = [];
		}

		var index = $scope.actualAlumno.papaTel.indexOf($scope.papaTel);

		if (index !== -1)
		{
			alert("El telefono ya ha sido registrado");
			return false;
		}

		$scope.actualAlumno.papaTel.push($scope.papaTel);
		$scope.papaTel = null;
	}

	$scope.removePapaTel = function($index)
	{
		$scope.actualAlumno.papaTel.splice($index, 1);
	}

	$scope.addMamaTel = function()
	{
		if (typeof $scope.actualAlumno.mamaTel === 'undefined')
		{
			$scope.actualAlumno.mamaTel = [];
		}

		var index = $scope.actualAlumno.mamaTel.indexOf($scope.mamaTel);

		if (index !== -1)
		{
			alert("El telefono ya ha sido registrado");
			return false;
		}

		$scope.actualAlumno.mamaTel.push($scope.mamaTel);
		$scope.mamaTel = null;
	}

	$scope.removeMamaTel = function($index)
	{
		$scope.actualAlumno.mamaTel.splice($index, 1);
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
}