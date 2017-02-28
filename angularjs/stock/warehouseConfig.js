app.controller('ComZeappsCrmWarehouseConfigCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_warehouses");

        $scope.form = {};
        $scope.newLine = {};
        var warehouses = [];

        zhttp.crm.warehouse.get_all().then(function(response){
            if(response.data && response.data != 'false'){
                warehouses = response.data;

                angular.forEach(warehouses, function(warehouse){
                    warehouse.resupply_delay = parseInt(warehouse.resupply_delay);
                });

                $scope.form.warehouses = angular.fromJson(angular.toJson(response.data));
            }
        });

        $scope.minus = function(index, field){
            $scope.form.warehouses[index][field] = parseInt($scope.form.warehouses[index][field]) - 1;
        };

        $scope.plus = function(index, field){
            $scope.form.warehouses[index][field] = parseInt($scope.form.warehouses[index][field]) + 1;
        };

        $scope.createLine = function(){
            var formatted_data = angular.toJson($scope.newLine);
            zhttp.crm.warehouse.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.newLine.id = response.data;
                    $scope.form.warehouses.push(angular.fromJson(angular.toJson($scope.newLine)));
                    warehouses.push($scope.newLine);
                    $scope.newLine = {};
                }
            });
        };

        $scope.cancelLine = function(){
            $scope.newLine = {};
        };

        $scope.delete = function(index){
            var id = $scope.form.warehouses[index].id;
            zhttp.crm.warehouse.del(id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.form.warehouses.splice(index, 1);
                    warehouses.splice(index, 1);
                }
            });
        };

        $scope.cancel = function(){
            $scope.form.warehouses = angular.fromJson(angular.toJson(warehouses));
        };

        $scope.success = function(){
            var formatted_data = angular.toJson($scope.form.warehouses);
            zhttp.crm.warehouse.save_all(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    warehouses = angular.fromJson(angular.toJson($scope.form.warehouses));
                }
            });
        };
    }]);