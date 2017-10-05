// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"search_product_stock",
	templateUrl:"/com_zeapps_crm/stock/modal",
	controller:"ZeAppsCrmModalProductStockCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Recherche d'un produit stocké";
		}
	}
});

app.controller("ZeAppsCrmModalProductStockCtrl", ["$scope", "$uibModalInstance", "zeHttp", "titre", "option", function($scope, $uibModalInstance, zhttp, titre, option) {
	$scope.titre = titre ;

	$scope.formCtrl = {};

	$scope.form = {};
	$scope.showForm = false;

	loadList() ;

	$scope.addProductStock = addProductStock;
	$scope.cancel = cancel;
	$scope.loadProductStock = loadProductStock;





	function loadList() {
        zhttp.crm.product_stock.get_all().then(function (response) {
			if (response.status == 200 && response.data != "false") {
				$scope.product_stocks = response.data.product_stocks ;
			}
		});
	}

	function addProductStock(){
		var formatted_data = angular.toJson($scope.form);

        zhttp.crm.product_stock.save(formatted_data).then(function(response){
			if(response.data && response.data != "false"){
				$uibModalInstance.close(response.data.product_stock);
			}
		});
	}

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}

	function loadProductStock(product_stocks) {
		product_stocks = product_stocks || false;
		$uibModalInstance.close(product_stocks);
	}

}]) ;