app.controller('ComZeappsCrmOrderListsCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_order");

        $rootScope.orders = {};

        zhttp.crm.order.get_all().then(function(response){
            if(response.data && response.data != 'false'){
                $rootScope.orders = response.data;
                for(var i=0; i<$rootScope.orders.length; i++){
                    $rootScope.orders[i].date_creation = new Date($rootScope.orders[i].date_creation);
                    $rootScope.orders[i].date_limit = new Date($rootScope.orders[i].date_limit);
                }
            }
        });
    }]);