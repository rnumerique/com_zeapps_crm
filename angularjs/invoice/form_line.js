app.controller("ComZeappsCrmInvoiceFormLineCtrl", ["$scope",
	function ($scope) {

        $scope.updateTaxe = updateTaxe;

        function updateTaxe(){
            angular.forEach($scope.$parent.taxes, function(taxe){
                if(taxe.id === $scope.form.id_taxe){
                    $scope.form.value_taxe = taxe.value;
                }
            })
        }

	}]);