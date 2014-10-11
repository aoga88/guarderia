function ProfileController($scope, $location, $http, $timeout, User)
{
	$scope.showSuccess = false;
	$scope.showImageField = false;

	$scope.load = function()
	{
        $http.get('/api/user/current')
        .success(function(data){
            User.find({_id: data.response._id, app: data.response.app})
            .then(function(lastData){
            	$scope.newUser = lastData.response[data.response._id];
            });
        });
	}

	$scope.save = function()
	{
		User.save($scope.newUser)
		.then(function(data){
			$scope.showSuccess = true;

			$timeout(function() {
				$scope.showSuccess = false;
			}, 3000);
		});
	}

	$scope.savePassword = function()
	{
		if ($scope.actualUser.password1 !== $scope.actualUser.password2)
		{
			return false;
		}

		$scope.newUser.password = SHA1($scope.actualUser.password1);
		$scope.save();
	}

	$scope.uploadImage = function() {
		$scope.showImageField = true;
	}

	$scope.cancelImage = function() {
		$scope.showImageField = false;
	}
}