// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"form_activity",
	templateUrl:"/com_zeapps_crm/deliveries/modal_activity",
	controller:"ZeAppsCrmModalDeliveryActivityCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Ajouter une activité";
		}
	}
});


app.controller("ZeAppsCrmModalDeliveryActivityCtrl", function($scope, $uibModalInstance, zeHttp, titre, option) {

	$scope.titre = titre ;

	$scope.cancel = cancel;
	$scope.save = save;

	if(option.activity){
		$scope.form = option.activity;
        $scope.titre = "Modifier une activité";
	}
	else{
		$scope.form = {};
	}

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}

	function save() {
		$uibModalInstance.close($scope.form);
	}

}) ;