function AlumnosController($scope, $location, $routeParams, Alumno)
{
	$scope.alumnos     = [];
	$scope.currentUser = {};
	$scope.alumnoId    = $routeParams.id;

	$scope.list = function()
	{
		Alumno.current()
	    .then(function(data){
            angular.forEach(data.response, function(alumno){
                $scope.alumnos.push(alumno);
            });
	    });
	}
}