// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"transform_delivery",
	templateUrl:"/com_zeapps_crm/deliveries/transform_modal",
	controller:"ZeAppsCrmModalDeliveryTransformCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Dupliquer le devis";
		}
	}
});

app.controller("ZeAppsCrmModalDeliveryTransformCtrl", function($scope, $uibModalInstance, zeHttp, titre, option) {
	$scope.titre = titre ;

	$scope.form = {
		order : true
	};

	$scope.cancel = cancel;
	$scope.transform = transform;

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}

	function transform() {
		$uibModalInstance.close($scope.form);
	}
}) ;