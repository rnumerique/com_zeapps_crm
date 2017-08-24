// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"transform_order",
	templateUrl:"/com_zeapps_crm/orders/transform_modal",
	controller:"ZeAppsCrmModalOrderTransformCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Dupliquer le devis";
		}
	}
});

app.controller("ZeAppsCrmModalOrderTransformCtrl", function($scope, $uibModalInstance, zeHttp, titre, option) {
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