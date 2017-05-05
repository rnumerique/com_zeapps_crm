app.controller('ComZeappsCrmOrderListsPartialCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        if(!$rootScope.orders)
            $rootScope.orders = [];
        $scope.id_company = 0;

        $scope.$on('comZeappsContact_dataEntrepriseHook', function(event, data){
            if ($scope.id_company !== data.id_company) {
                $scope.id_company = data.id_company;
                zhttp.crm.order.get_all($scope.id_company, 'company').then(function (response) {
                    if (response.data && response.data != 'false') {
                        $rootScope.orders = response.data;
                        for (var i = 0; i < $rootScope.orders.length; i++) {
                            $rootScope.orders[i].date_creation = new Date($rootScope.orders[i].date_creation);
                            $rootScope.orders[i].date_limit = new Date($rootScope.orders[i].date_limit);
                        }
                    }
                    else {
                        $rootScope.orders = {};
                    }
                });
            }
        });
        $scope.$emit('comZeappsContact_triggerEntrepriseHook', {});

        $scope.$on('comZeappsContact_dataContactHook', function(event, data){
            if ($scope.id_contact !== data.id_contact) {
                $scope.id_contact = data.id_contact;
                $scope.id_company = data.id_company;
                zhttp.crm.order.get_all($scope.id_contact, 'contact').then(function (response) {
                    if (response.data && response.data != 'false') {
                        $rootScope.orders = response.data;
                        for (var i = 0; i < $rootScope.orders.length; i++) {
                            $rootScope.orders[i].date_creation = new Date($rootScope.orders[i].date_creation);
                            $rootScope.orders[i].date_limit = new Date($rootScope.orders[i].date_limit);
                        }
                    }
                    else {
                        $rootScope.orders = {};
                    }
                });
            }
        });
        $scope.$emit('comZeappsContact_triggerContactHook', {});

        $scope.filters = {
            finalized: true
        };

        $scope.delete = function(order){
            zhttp.crm.order.del(order.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.orders.splice($rootScope.orders.indexOf(order), 1);
                }
            });
        }


    }]);