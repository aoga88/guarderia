function GruposController($scope, $location, $timeout, $routeParams, User, Grupo, Alumno, Maestro) {
	$scope.grupos = [];
	$scope.grupoId = $routeParams.id;
    $scope.alumnos = [];
    $scope.maestros = [];
    $scope.actualGrupo = {};
    $scope.isAdmin = false;

    User.getCurrent()
    .then(function(data) {
        $scope.currentApp = data.response.app;
        $scope.isAdmin = data.response.roles.indexOf('admin') !== -1;
    });

    $scope.list = function()
	{
		Grupo.current()
	    .then(function(data){
            angular.forEach(data.response, function(grupo){
                $scope.grupos.push(grupo);
            });
	    });
	}

    $scope.load = function()
    {
        Alumno.current()
        .then(function(data) {
            angular.forEach(data.response, function(alumno){
                $scope.alumnos.push(alumno);

                if (typeof $scope.actualGrupo.alumnos !== 'undefined') {
                    angular.forEach($scope.alumnos, function(alumno) {
                        if ($scope.actualGrupo.alumnos.indexOf(alumno._id.$id) !== -1) {
                            alumno.selected = true;
                        }
                    });
                }
            });
        });

        Maestro.current()
        .then(function(data) {
            angular.forEach(data.response, function(maestro){
                $scope.maestros.push(maestro);

                if (typeof $scope.actualGrupo.maestros !== 'undefined') {
                    angular.forEach($scope.maestros, function(maestro) {
                        if ($scope.actualGrupo.maestros.indexOf(maestro._id) !== -1) {
                            maestro.selected = true;
                        }
                    });
                }
            });
        });

        if ($scope.grupoId === "0")
        {
            return false;
        }

        Grupo.find({_id: $scope.grupoId})
        .then(function(data){
            $scope.actualGrupo = data.response;
            if (typeof $scope.actualGrupo.alumnos !== 'undefined') {
                    angular.forEach($scope.alumnos, function(alumno) {
                        if ($scope.actualGrupo.alumnos.indexOf(alumno._id.$id) !== -1) {
                            alumno.selected = true;
                        }
                    });
                }
            if (typeof $scope.actualGrupo.maestros !== 'undefined') {
                    angular.forEach($scope.maestros, function(maestro) {
                        if ($scope.actualGrupo.maestros.indexOf(maestro._id) !== -1) {
                            maestro.selected = true;
                        }
                    });
                }
        });
    }

    $scope.addAlumno = function(alumno, index) {
        if (typeof $scope.actualGrupo.alumnos === 'undefined') {
            $scope.actualGrupo.alumnos = [];
        }

        $scope.actualGrupo.alumnos.push(alumno._id.$id);
        alumno.selected = true;
    }

    $scope.removeAlumno = function(alumno, index) {
        if (typeof $scope.actualGrupo.alumnos === 'undefined') {
            $scope.actualGrupo.alumnos = [];
        }

        var indexAlumno = $scope.actualGrupo.alumnos.indexOf(alumno._id.$id);

        if (indexAlumno === -1) {
            return false;
        }

        $scope.actualGrupo.alumnos.splice(indexAlumno, 1);
        alumno.selected = false;
    }

    $scope.addMaestro = function(maestro, index) {
        if (typeof $scope.actualGrupo.maestros === 'undefined') {
            $scope.actualGrupo.maestros = [];
        }

        $scope.actualGrupo.maestros.push(maestro._id);
        maestro.selected = true;
    }

    $scope.removeMaestro = function(maestro, index) {
        if (typeof $scope.actualGrupo.maestros === 'undefined') {
            $scope.actualGrupo.maestros = [];
        }

        var indexMaestro = $scope.actualGrupo.maestros.indexOf(maestro._id);

        if (indexMaestro === -1) {
            return false;
        }

        $scope.actualGrupo.maestros.splice(indexMaestro, 1);
        maestro.selected = false;
    }

    $scope.save = function()
    {
        $scope.actualGrupo.app = $scope.currentApp;
        Grupo.save($scope.actualGrupo, $scope.grupoId)
        .then(function(data) {
            $location.path('/grupos').replace();
            $scope.successSave = true;
            $timeout(function(){
                $scope.successSave = false
            }, 2000);
        });
    }
}