app.controller('ComZeappsCrmDeliveryListsPartialCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        if(!$rootScope.deliveries)
            $rootScope.deliveries = [];
        $scope.id_company = 0;

        $scope.$on('comZeappsContact_dataEntrepriseHook', function(event, data){
            if ($scope.id_company !== data.id_company) {
                $scope.id_company = data.id_company;
                zhttp.crm.delivery.get_all($scope.id_company, 'company').then(function (response) {
                    if (response.data && response.data != 'false') {
                        $rootScope.deliveries = response.data;
                        for (var i = 0; i < $rootScope.deliveries.length; i++) {
                            $rootScope.deliveries[i].date_creation = new Date($rootScope.deliveries[i].date_creation);
                            $rootScope.deliveries[i].date_limit = new Date($rootScope.deliveries[i].date_limit);
                        }
                    }
                    else {
                        $rootScope.deliveries = {};
                    }
                });
            }
        });
        $scope.$emit('comZeappsContact_triggerEntrepriseHook', {});

        $scope.$on('comZeappsContact_dataContactHook', function(event, data){
            if ($scope.id_contact !== data.id_contact) {
                $scope.id_contact = data.id_contact;
                zhttp.crm.delivery.get_all($scope.id_contact, 'contact').then(function (response) {
                    if (response.data && response.data != 'false') {
                        $rootScope.deliveries = response.data;
                        for (var i = 0; i < $rootScope.deliveries.length; i++) {
                            $rootScope.deliveries[i].date_creation = new Date($rootScope.deliveries[i].date_creation);
                            $rootScope.deliveries[i].date_limit = new Date($rootScope.deliveries[i].date_limit);
                        }
                    }
                    else {
                        $rootScope.deliveries = {};
                    }
                });
            }
        });
        $scope.$emit('comZeappsContact_triggerContactHook', {});

        $scope.filters = {};

        $scope.delete = function(delivery){
            zhttp.crm.delivery.del(delivery.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.deliveries.splice($rootScope.deliveries.indexOf(delivery), 1);
                }
            });
        }


    }]);