app.controller("ComZeAppsCrmStockTransfertFormCtrl", ["$scope", "$rootScope", "zeHttp",
    function ($scope, $rootScope, zhttp) {

        $scope.form.src = $rootScope.current_warehouse || $rootScope.user.id_warehouse;
        $scope.form.date_mvt = new Date();

        zhttp.crm.warehouse.get_all().then(function (response) {
            if (response.data && response.data != "false") {
                $scope.warehouses = response.data;
            }
        });
    }]);