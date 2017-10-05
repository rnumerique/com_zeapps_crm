app.controller("ComZeappsCrmStockViewCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "menu",
	function ($scope, $location, $rootScope, zhttp, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_stock");

		$rootScope.current_warehouse = $rootScope.current_warehouse || $rootScope.user.id_warehouse;

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
                    field: 'label LIKE',
                    type: 'text',
                    label: 'Libellé'
                },
                {
                    format: 'select',
                    field: 'id_warehouse',
                    type: 'text',
                    label: 'Entrepôt',
                    options: []
                }
            ]
        };
        $scope.filter_model = {
        	'id_warehouse': $rootScope.current_warehouse
		};
        $scope.page = 1;
        $scope.pageSize = 15;
        $scope.total = 0;
        $scope.templateStock = '/com_zeapps_crm/stock/form_modal';

        loadList(true);

		$scope.loadList = loadList;
		$scope.goTo = goTo;
		$scope.add = add;
		$scope.delete = del;

        function loadList(context){
            context = context || "";
            var offset = ($scope.page - 1) * $scope.pageSize;
            var formatted_filters = angular.toJson($scope.filter_model);

            $rootScope.current_warehouse = $scope.filter_model.id_warehouse;

            zhttp.crm.product_stock.get_all($scope.pageSize, offset, context, formatted_filters).then(function(response){
                if(response.data && response.data != "false"){
                    $scope.product_stocks = response.data.product_stocks;
                    angular.forEach($scope.product_stocks, function(product_stock){
                        product_stock.value_ht = parseFloat(product_stock.value_ht);
                        calcTimeLeft(product_stock);
                    });

                    $scope.total = response.data.total;

                    if(context){
                        $scope.filters.main[2].options = response.data.warehouses;
                    }
                }
            });
        }

        function goTo(id){
        	$location.url("/ng/com_zeapps_crm/stock/" + id);
		}

		function add(product_stock){
			var formatted_data = angular.toJson(product_stock);

			zhttp.crm.product_stock.save(formatted_data, $scope.filter_model.id_warehouse).then(function(response){
				if(response.data && response.data != false){
					var product_stock = response.data.product_stock;
					product_stock.value_ht = parseFloat(product_stock.value_ht);
					calcTimeLeft(product_stock);
					$scope.product_stocks.push(product_stock);

					$scope.form = {};
					$scope.shownForm = false;
				}
			});
		}

		function del(product_stock){
            zhttp.crm.product_stock.del(product_stock.id_stock).then(function (response) {
                if (response.status == 200) {
                    $scope.product_stocks.splice($scope.product_stocks.indexOf(product_stock), 1);
                }
            });
		}

		function calcTimeLeft(product_stock){
			if(product_stock.avg > 0) {
				var timeleft = product_stock.total / product_stock.avg;

				if(timeleft > 0) {
					product_stock.timeleft = moment().to(moment().add(timeleft, "days"));
					product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
					product_stock.classRupture = "text-success";
				}
				else{
					product_stock.timeleft = "En rupture";
					product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
					product_stock.classRupture = "text-danger";
				}

				if($scope.filter_model.id_warehouse > 0) {
					product_stock.timeResupply = moment().to(moment().add(timeleft, "days").subtract(product_stock.resupply_delay, product_stock.resupply_unit));
					product_stock.dateResupply = moment().add(timeleft, "days").subtract(product_stock.resupply_delay, product_stock.resupply_unit).format("DD/MM/YYYY");
					product_stock.classResupply = moment().isBefore(moment().add(timeleft, "days").subtract(product_stock.resupply_delay, product_stock.resupply_unit), "day") ? "text-success" : "text-danger";
				}
			}
			else{
				product_stock.timeleft = "-";
				product_stock.dateRupture = "";
				product_stock.timeResupply = "-";
				product_stock.dateResupply = "";
				product_stock.classRupture = "text-info";
			}
		}
	}]);