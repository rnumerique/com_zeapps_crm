app.controller('ComZeappsCrmQuoteListsPartialCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        if(!$rootScope.quotes)
            $rootScope.quotes = [];
        $scope.id_company = 0;
        $scope.filters = {};

        $scope.delete = del;

        $scope.$on('comZeappsContact_dataEntrepriseHook', function(event, data) {
            if ($scope.id_company !== data.id_company){
                $scope.id_company = data.id_company;
                zhttp.crm.quote.get_all($scope.id_company, 'company').then(function (response) {
                    if (response.data && response.data != 'false') {
                        $rootScope.quotes = response.data;
                        for (var i = 0; i < $rootScope.quotes.length; i++) {
                            $rootScope.quotes[i].date_creation = new Date($rootScope.quotes[i].date_creation);
                            $rootScope.quotes[i].date_limit = new Date($rootScope.quotes[i].date_limit);
                        }
                    }
                    else {
                        $rootScope.orders = {};
                    }
                });
            }
        });
        $scope.$emit('comZeappsContact_triggerEntrepriseHook', {});

        $scope.$on('comZeappsContact_dataContactHook', function(event, data) {
            if ($scope.id_contact !== data.id_contact){
                $scope.id_contact = data.id_contact;
                $scope.id_company = data.id_company;
                zhttp.crm.quote.get_all($scope.id_contact, 'contact').then(function (response) {
                    if (response.data && response.data != 'false') {
                        $rootScope.quotes = response.data;
                        for (var i = 0; i < $rootScope.quotes.length; i++) {
                            $rootScope.quotes[i].date_creation = new Date($rootScope.quotes[i].date_creation);
                            $rootScope.quotes[i].date_limit = new Date($rootScope.quotes[i].date_limit);
                        }
                    }
                    else {
                        $rootScope.orders = {};
                    }
                });
            }
        });
        $scope.$emit('comZeappsContact_triggerContactHook', {});

        function del(quote){
            zhttp.crm.quote.del(quote.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.quotes.splice($rootScope.quotes.indexOf(quote), 1);
                }
            });
        }


    }]);