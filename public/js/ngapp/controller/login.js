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
        'admin' : '/dashboard',
        'maestro' : '/dashboard',
        'padre' : '/dashboard'
    };

    $scope.login = function(view) {
        User.login($scope.user)
        .then(function(data) {
            var role = data.response.roles[0];

            $location.path($scope.redirect[role])
                     .replace();
            location.href = '/#/';
            location.reload();
        }, function(data){
            $scope.response = data.response;
            $scope.showError = true;
        });
    }

    $("body").addClass("login-page");
    jQuery(document).ready(function($){

        var min_height = jQuery(window).height();
        jQuery('div.login-page-container').css('min-height', min_height);
        jQuery('div.login-page-container').css('line-height', min_height + 'px');

        //$(".inner", ".boxed").fadeIn(500);
      });
}