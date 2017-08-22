// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"transform_quote",
	templateUrl:"/com_zeapps_crm/quotes/transform_modal",
	controller:"ZeAppsCrmModalQuoteTransformCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Convertir le devis";
		}
	}
});

app.controller("ZeAppsCrmModalQuoteTransformCtrl", function($scope, $uibModalInstance, zeHttp, titre, option) {
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