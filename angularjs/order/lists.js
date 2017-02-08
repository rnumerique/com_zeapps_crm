app.controller('ComZeappsCrmOrderListsCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_order");

        $rootScope.orders = {};

        zhttp.crm.order.get_all().then(function(response){
            if(response.data && response.data != 'false'){
                $rootScope.orders = response.data;
                for(var i=0; i<$scope.orders.length; i++){
                    $rootScope.orders[i].date_creation = new Date($rootScope.orders[i].date_creation);
                    $rootScope.orders[i].date_limit = new Date($rootScope.orders[i].date_limit);
                }
            }
        });

        $scope.totalHT = function(order){

            var total = 0;
            for(var i = 0; i < order.lines.length; i++){
                if(order.lines[i] != undefined && order.lines[i].num != 'subTotal' && order.lines[i].num != 'comment'){
                    total += order.lines[i].price_unit * order.lines[i].qty * ( 1 - (order.lines[i].discount / 100) );
                }
            }
            total = total * (1- (order.global_discount / 100) );
            return total.toFixed(2);

        };

        $scope.totalTTC = function(order){

            var total = 0;
            for(var i = 0; i < order.lines.length; i++){
                if(order.lines[i] != undefined && order.lines[i].num != 'subTotal' && order.lines[i].num != 'comment'){
                    total += order.lines[i].price_unit * order.lines[i].qty * ( 1 - (order.lines[i].discount / 100) ) * ( 1 + (order.lines[i].taxe / 100) );
                }
            }
            total = total * (1- (order.global_discount / 100) );
            return total.toFixed(2);

        };

        $scope.delete = function(order){
            zhttp.crm.order.del(order.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.orders.splice($rootScope.orders.indexOf(order), 1);
                }
            });
        }


    }]);