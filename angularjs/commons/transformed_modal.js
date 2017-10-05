// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"transformed_document",
	templateUrl:"/com_zeapps_crm/crm_commons/transformed_modal",
	controller:"ZeAppsCrmModalDocumentTransformedCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Consulter un document";
		}
	}
});

app.controller("ZeAppsCrmModalDocumentTransformedCtrl", ["$scope", "$location", "$uibModalInstance", "zeHttp", "toasts", "titre", "option",
	function($scope, $location, $uibModalInstance, zhttp, toasts, titre, option) {
	$scope.titre = titre ;

	$scope.documents = option;

	$scope.goTo = goTo;
	$scope.pdf = pdf;
	$scope.finalize = finalize;
	$scope.cancel = cancel;

	function goTo(type, id){
		$location.url('/ng/com_zeapps_crm/' + type + "/" + id);
        cancel();
	}

	function pdf(type, id){
        zhttp.crm[type].pdf.make(id).then(function(response){
			if(response.data && response.data != "false"){
				window.document.location.href = zhttp.crm[type].pdf.get() + angular.fromJson(response.data);
			}
		});
	}

	function finalize(id){
        zhttp.crm.invoice.finalize(id).then(function (response) {
            if (response.data && response.data !== "false") {
                if(response.data.error){
                    toasts('danger', response.data.error);
                }
                else {
                    $scope.documents.invoices.numerotation = response.data.numerotation;
                }
            }
        });
	}

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}
}]) ;