app.controller("ComZeappsCrmModalityConfigFormModalCtrl", ["$scope",
	function ($scope) {
        if(!$scope.form.id){
            $scope.form = {
                type : "0",
                situation : "0",
                settlement_type : "0"
            };
        }
	}]);