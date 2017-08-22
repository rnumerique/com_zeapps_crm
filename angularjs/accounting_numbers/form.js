app.controller("ComZeappsCrmAccountingNumberFormCtrl", ["$scope",
	function ($scope) {

		$scope.types = [
            {
                id: '1',
                label: 'Client'
            },
            {
                id: '2',
                label: 'Fournisseur'
            }
        ];

		$scope.updateType = updateType;

		function updateType() {
		    angular.forEach($scope.types, function(type){
		        if(type.id === $scope.form.type){
		            $scope.form.type_label = type.label;
                }
            })
		}
	}]);