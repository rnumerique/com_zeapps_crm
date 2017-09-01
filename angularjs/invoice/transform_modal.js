// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"transform_invoice",
	templateUrl:"/com_zeapps_crm/invoices/transform_modal",
	controller:"ZeAppsCrmModalInvoiceTransformCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Dupliquer la facture";
		}
	}
});

app.controller("ZeAppsCrmModalInvoiceTransformCtrl", function($scope, $uibModalInstance, zeHttp, titre, option) {
	$scope.titre = titre ;

	$scope.form = {
		deliveries : true
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