app.controller('ComZeappsCrmStockViewCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_stock");

        $scope.selectedWarehouse = '0';

        getStocks();

        $scope.updateWarehouse = function(){
            getStocks($scope.selectedWarehouse);
        };





        function getStocks(id){
            zhttp.crm.product_stock.get_all(id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.warehouses = response.data.warehouses;
                    $scope.product_stocks = response.data.product_stocks;
                }
            });
        }
    }]);