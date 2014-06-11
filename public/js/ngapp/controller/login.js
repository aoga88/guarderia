function LoginController($scope, $location, User)
{
    $scope.response  = '';
    $scope.showError = false;
    $scope.user = {
        email: '',
        password: ''
    };

    $scope.redirect = {
        'superadmin' : '/apps',
        'admin' : '/dashboard'
    };

    $scope.login = function(view) {
        User.login($scope.user)
        .then(function(data) {
            var role = data.response.roles[0];

            $location.path($scope.redirect[role])
                     .replace();
        }, function(data){
            $scope.response = data.response;
            if (view === 'desktop')
            {
                $('#errorModal').modal('show');
            }

            $scope.showError = true;
        });
    }
}