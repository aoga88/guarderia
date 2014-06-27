function CalendarioController($scope, Registro, Alumno) {

	$scope.horas = {
		'06:00': {},
		'07:00': {},
		'08:00': {},
		'09:00': {},
		'10:00': {},
		'11:00': {},
		'12:00': {},
		'13:00': {},
		'14:00': {},
		'15:00': {},
		'16:00': {},
		'17:00': {},
		'18:00': {},
		'19:00': {},
		'20:00': {},
		'21:00': {},
		'22:00': {},
	};

	$scope.dias = {
		0: {
			nombre: 'Domingo'
		},
		1: {
			nombre: 'Lunes'
		},
		2: {
			nombre: 'Martes'
		},
		3: {
			nombre: 'Miercoles'
		},
		4: {
			nombre: 'Jueves'
		},
		5: {
			nombre: 'Viernes'
		},
		6: {
			nombre: 'Sabado'
		}
	};

	$scope.fechaActual  = new Date();
	$scope.inicioSemana = new Date();
	var diaSemanaActual = $scope.inicioSemana.getDay();
	inicio = new Date();
	$scope.inicioSemana.setDate(inicio.getDate() - diaSemanaActual);

	$scope.getClass = function(dia, info)
	{
		if(info != null && info.length != 0)
		{
			return "info";
		}
		if(dia == $scope.fechaActual.getDay())
		{
			return "info";
		}else{
			return null;
		}
	}

	$scope.siguiente = function()
	{
		var dia   = $scope.inicioSemana.getDate() + 7;
		var month = $scope.inicioSemana.getMonth();
		var year  = $scope.inicioSemana.getFullYear();
		var next  = new Date(year, month, dia);
		$scope.inicioSemana = new Date(year, month, dia);
		$scope.loadSemana($scope.inicioSemana);
	}

	$scope.anterior = function()
	{
		var dia   = $scope.inicioSemana.getDate() - 7;
		var month = $scope.inicioSemana.getMonth();
		var year  = $scope.inicioSemana.getFullYear();
		var next  = new Date(year, month, dia);
		$scope.inicioSemana = new Date(year, month, dia);
		$scope.loadSemana($scope.inicioSemana);
	}

	$scope.loadSemana = function(inicio)
	{
		if(inicio == null)
		{
			var diaSemanaActual = new Date().getDay();
			inicio = new Date();
			inicio.setDate(inicio.getDate() - diaSemanaActual);
		}

		for(var i = 0 ; i <= 6 ; i++)
		{
			var dia      = $scope.inicioSemana.getDate() + i;
			var month    = $scope.inicioSemana.getMonth();
			var year     = $scope.inicioSemana.getFullYear();
			var newfecha = new Date(year, month, dia);

			$scope.dias[i].fecha = newfecha.getDate() + '/' + parseInt(newfecha.getMonth() + 1) + '/' + newfecha.getFullYear();

		}
	}

	$scope.doAlert = function(str) {
		alert(str);
	}

	$scope.loadSemana();
}