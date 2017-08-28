app.controller("ComZeappsCrmWarehouseUserHookCtrl", ["$scope", "zeHttp",
    function ($scope, zhttp) {

        $scope.warehouses = [];

        zhttp.crm.warehouse.get_all().then(function(response){
            if(response.data && response.data != "false"){
                $scope.warehouses = response.data;
            }
        });
    }]);