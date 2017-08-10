// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"search_invoice",
	templateUrl:"/com_zeapps_crm/invoices/modal",
	controller:"ZeAppsCrmModalInvoiceCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Recherche d'une facture";
		}
	}
});


app.controller("ZeAppsCrmModalInvoiceCtrl", function($scope, $uibModalInstance, zeHttp, titre, option) {

	$scope.titre = titre ;

	$scope.cancel = cancel;
	$scope.loadInvoice = loadInvoice;

	loadList() ;

	function loadList() {
		zeHttp.crm.invoice.get_all().then(function (response) {
			if (response.status == 200) {
				$scope.invoices = response.data ;
			}
		});
	}

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}

	function loadInvoice(invoice) {
		$uibModalInstance.close(invoice);
	}

}) ;