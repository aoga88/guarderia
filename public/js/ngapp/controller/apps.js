function AplicationController($scope, $routeParams, $timeout, $location, Apps, User)
{
    $scope.noApps = true;
    $scope.apps = {};
    $scope.data = {};
    $scope.appId = $routeParams.id;
    $scope.sucessSave = false;
    $scope.errorSave  = false;
    $scope.noUsers = true;
    $scope.showNewUserForm = false;
    $scope.newUser = {
        roles: []
    };
    $scope.users = [];
    $scope.emailRepeated = false;

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
}