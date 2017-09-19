app.controller("ComZeappsCrmCreditBalanceFormCtrl", ["$scope", "$routeParams",
	function ($scope, $routeParams) {

        $scope.updateModality = updateModality;

        if(!$scope.form.id){
            $scope.form.id_invoice = $routeParams.id;
            $scope.form.date_payment = new Date();
        }

        function updateModality(){
		    angular.forEach($scope.modalities, function(modality){
		        if(modality.id === $scope.form.id_modality){
		            $scope.form.label_modality = modality.label;
                }
            });
        }
	}]);