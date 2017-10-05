// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"search_product",
	templateUrl:"/com_zeapps_crm/product/modal_search_product",
	controller:"ComZeappsCrmModalSearchProductCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Recherche d'un produit";
		}
	}
});


app.controller("ComZeappsCrmModalSearchProductCtrl", ["$scope", "$uibModalInstance", "zeHttp", "titre", "option", function($scope, $uibModalInstance, zhttp, titre, option) {
	$scope.titre = titre ;

    $scope.currentBranch = {};
	$scope.tree = {
		branches: []
	};
    $scope.filters = {
        main: [
        	{
				format: 'input',
				field: 'ref LIKE',
				type: 'text',
				label: 'Référence'
        	},
        	{
				format: 'input',
				field: 'name LIKE',
				type: 'text',
				label: 'Nom du produit'
        	}
        ]
    };
    $scope.filter_model = {};
    $scope.page = 1;
    $scope.pageSize = 15;

    $scope.update = update;
	$scope.cancel = cancel;
	$scope.select_product = select_product;
    $scope.loadList = loadList;

	getTree();

	function update(branch){
        $scope.currentBranch = branch;
        loadList();
	}

    function loadList() {
		var id = $scope.currentBranch ? $scope.currentBranch.id : 0;
        var offset = ($scope.page - 1) * $scope.pageSize;
        var formatted_filters = angular.toJson($scope.filter_model);

        zhttp.crm.product.modal(id, $scope.pageSize, offset, formatted_filters).then(function (response) {
            if (response.status == 200) {
                $scope.products = response.data.data;
                $scope.total = response.data.total;
            }
        });
    }

	function getTree() {
        zhttp.crm.category.tree().then(function (response) {
			if (response.status == 200) {
				$scope.tree.branches = response.data;
                $scope.currentBranch = $scope.tree.branches[0];
                loadList();
			}
		});
	}

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}

	function select_product(produit) {
		$uibModalInstance.close(produit);
	}

}]) ;