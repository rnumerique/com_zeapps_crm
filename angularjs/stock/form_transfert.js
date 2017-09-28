app.controller("ComZeAppsCrmStockTransfertFormCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "$uibModal", "zeapps_modal",
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal) {

        $scope.form.src = $rootScope.current_warehouse || $rootScope.user.id_warehouse;
        $scope.form.date_mvt = new Date();

        zhttp.crm.warehouse.get_all().then(function (response) {
            if (response.data && response.data != "false") {
                $scope.warehouses = response.data;
            }
        });
    }]);