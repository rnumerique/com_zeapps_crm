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


app.controller("ComZeappsCrmModalSearchProductCtrl", function($scope, $uibModalInstance, zeHttp, titre, option) {
	$scope.titre = titre ;

	$scope.activeCategory = {
		data: ""
	};
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

	$scope.cancel = cancel;
	$scope.select_product = select_product;
    $scope.loadList = loadList;

	getTree();

	$scope.$watch("activeCategory.data", function(value){
		if(typeof(value.id) !== "undefined"){
            loadList(value.id);
		}
	});

    function loadList(id_cat) {
        var offset = ($scope.page - 1) * $scope.pageSize;
        var formatted_filters = angular.toJson($scope.filter_model);

        zeHttp.crm.product.modal(id_cat, $scope.pageSize, offset, formatted_filters).then(function (response) {
            if (response.status == 200) {
                $scope.products = response.data.data;
                $scope.total = response.data.total;
            }
        });
    }

	function getTree() {
		zeHttp.crm.category.tree().then(function (response) {
			if (response.status == 200) {
				var id = $scope.activeCategory.data.id || 0;
				$scope.tree.branches = response.data;
				zeHttp.crm.category.openTree($scope.tree, id);
				zeHttp.crm.category.get(id).then(function (response) {
					if (response.status == 200) {
						$scope.activeCategory.data = response.data;
					}
				});
			}
		});
	}

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}

	function select_product(produit) {
		$uibModalInstance.close(produit);
	}

}) ;