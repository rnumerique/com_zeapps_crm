// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"transform_document",
	templateUrl:"/com_zeapps_crm/crm_commons/transform_modal",
	controller:"ZeAppsCrmModalDocumentTransformCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Dupliquer le document";
		}
	}
});

app.controller("ZeAppsCrmModalDocumentTransformCtrl", ["$scope", "$uibModalInstance", "titre", "option", function($scope, $uibModalInstance, titre, option) {
	$scope.titre = titre ;

	$scope.form = {};

	$scope.cancel = cancel;
	$scope.transform = transform;

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}

	function transform() {
		$uibModalInstance.close($scope.form);
	}
}]) ;