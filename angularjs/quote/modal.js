// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"search_quote",
	templateUrl:"/com_zeapps_crm/quotes/modal",
	controller:"ZeAppsCrmModalQuoteCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Recherche d'un devis";
		}
	}
});


app.controller("ZeAppsCrmModalQuoteCtrl", function($scope, $uibModalInstance, zeHttp, titre, option) {

	$scope.titre = titre ;

	$scope.cancel = cancel;
	$scope.loadQuote = loadQuote;

	loadList() ;

	function loadList() {
		zeHttp.crm.quote.get_all().then(function (response) {
			if (response.status == 200) {
				$scope.quotes = response.data ;
			}
		});
	}

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}

	function loadQuote(quote) {
		$uibModalInstance.close(quote);
	}

}) ;