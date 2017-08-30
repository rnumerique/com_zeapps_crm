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
            },
            {
                id: '3',
                label: 'TVA'
            },
            {
                id: '4',
                label: 'Produit'
            },
            {
                id: '5',
                label: 'Achat'
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