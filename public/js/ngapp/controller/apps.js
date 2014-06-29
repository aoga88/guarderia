function AplicationController($scope, $routeParams, $timeout, $location, Apps, User, Pago)
{
    $scope.noApps          = true;
    $scope.apps            = {};
    $scope.data            = {};
    $scope.appId           = $routeParams.id;
    $scope.sucessSave      = false;
    $scope.errorSave       = false;
    $scope.noUsers         = true;
    $scope.showNewUserForm = false;
    $scope.newUser         = {
        roles: []
    };
    $scope.users         = [];
    $scope.emailRepeated = false;
    $scope.showPagoForm  = false;

    $scope.meses = [
        '01', '02', '03', '04', '05', '07', '08', '09', '10', '11', '12'
    ];

    $scope.anos = [
        '2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'
    ];

    Apps.find({})
    .then(function(data){
        
        if (data.response.length === 0)
        {
            $scope.apps = {};
        } else {
            $scope.apps = data.response;    
        }


        if(data.length !== 0)
        {
            $scope.noApps = false;
        }
    });

    $scope.load = function()
    {
        Apps.find({_id: $scope.appId})
        .then(function(data){
            $scope.entityApp = data.response[$scope.appId];
        });

        User.fromApp($scope.appId)
        .then(function(data){
            if (data.response.length !== 0)
            {
                $scope.noUsers = false;
                angular.forEach(data.response, function(user){
                    $scope.users.push(user);
                });
            }
        });
    }

    $scope.toggleRole = function(role)
    {
        var index = $scope.newUser.roles.indexOf(role);
        
        if (index === -1) {
            $scope.newUser.roles.push(role);
        } else {
            $scope.newUser.roles.splice(index, 1);
        }
    }

    $scope.save = function()
    {
        if ($scope.appId === '0') {
            $scope.entityApp.pagos = [
                 {
                    fecha: $scope.entityApp.fechaCorte, 
                    pago: $scope.entityApp.fechaCorte, 
                    monto: 0.0
                 }
                ];
            delete $scope.entityApp.fechaCorte;
        }

        Apps.save($scope.entityApp, $scope.appId)
        .then(function(data){
            $scope.successSave = true;
            if ($scope.appId == 0)
            {
                $location.path('/guarderias/' + data.response._id.$id).replace();
            }
            $timeout(function(){
                $scope.successSave = false
            }, 2000);
        }, function(data){
            $scope.errorSave = true;
        });
    }

    $scope.inactiveUser = function(userId, user)
    {
        var conf = confirm('¿Desea desactivar el usuario?')

        if (conf)
        {
            User.save({_id: userId, active: false})
            .then(function(data){
                user.active = false;
            });
        }
    }

    $scope.inactiveApp = function(appId, app)
    {
        var conf = confirm('¿Desea desactivar la aplicación? Los usuarios asociados a ella no podrán iniciar sesión.')

        if (conf)
        {
            Apps.save({active: false}, appId)
            .then(function(data) {
                app.active = false;
            });
        }
    }

    $scope.activeApp = function(appId, app)
    {
        var conf = confirm('¿Desea sactivar la aplicación? Los usuarios asociados a ella podrán iniciar sesión.')

        if (conf)
        {
            Apps.save({active: true}, appId)
            .then(function(data) {
                app.active = true;
            });
        }
    }

    $scope.activeUser = function(userId, user)
    {
        var conf = confirm('¿Desea activar el usuario?')

        if (conf)
        {
            User.save({_id: userId, active: true})
            .then(function(data){
                user.active = true;
            });
        }
    }

    $scope.userExists = function(conditions) {
        User.find(conditions)
        .then(function(data) {
            if (data.response.length !== 0)
            {
                $scope.emailRepeated = true;
            }
            else
            {
                $scope.emailRepeated = false;
            }
        });
    }

    $scope.newUserAction = function() {
        $scope.newUser.app = $scope.appId;
        User.new($scope.newUser)
        .then(function(data){
            $scope.showNewUserForm = false;
            $scope.newUser = {};
            $scope.users.push(data.response);
        });
    }

    $scope.formatDate = function(date) {

        if (typeof date === 'undefined') {
            return '';
        }

        var fecha = new Date(date.sec * 1000);
        var day = fecha.getDate();
        var month = fecha.getMonth();

        if (day < 10) {
            day = '0' + day;
        }

        if (month < 10) {
            month = '0' + month;
        }

        var format = day + '/' + month + '/' + fecha.getFullYear();
        return format;
    }

    $scope.showPagar = function(pago, index) {
        console.log(pago);
        $scope.showPagoForm = true;
        $scope.actualPago   = pago;
        $scope.indexPago    = index;
    }

    $scope.realizarPago = function()
    {
        Pago.send($scope.entityApp, $scope.indexPago)
        .then( function(data) {
            console.log(data);
        });
    }

    $scope.getCurrent = function() {
        Apps.current()
        .then(function(data) {
            $scope.entityApp = data.response;
        });
    }

    $scope.cancelaPago = function() {
        $scope.showPagoForm = false;
    }

    $scope.marcarPagado = function(entityApp, indexPago) {
        Pago.registrar(entityApp, indexPago)
        .then(function(data) {
            entityApp.pagos[indexPago] = data.response;
        });
    }
}