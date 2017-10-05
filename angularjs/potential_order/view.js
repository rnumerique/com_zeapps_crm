app.controller("ComZeappsCrmPotentialOrderViewCtrl", ["$scope", "zeHttp", "menu",
	function ($scope, zhttp, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_potential_orders");

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
                }
            ]
        };
        $scope.filter_model = {};
        $scope.page = 1;
        $scope.pageSize = 15;
        $scope.total = 0;

        loadList(true);

		$scope.loadList = loadList;

        function loadList(context){
            context = context || "";
            var offset = ($scope.page - 1) * $scope.pageSize;
            var formatted_filters = angular.toJson($scope.filter_model);

            zhttp.crm.potential_orders.all($scope.pageSize, offset, context, formatted_filters).then(function(response){
                if(response.data && response.data != "false"){
                    $scope.orders = response.data.orders;
                    angular.forEach($scope.orders, function(order){
                        order.date_creation = new Date(order.date_creation);
                        order.date_next = new Date(order.date_next);
                    });

                    $scope.total = response.data.total;

                    if(context){
                    }
                }
            });
        }
	}]);