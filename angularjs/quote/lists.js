app.controller('ComZeappsCrmQuoteListsCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_quote");

        $rootScope.quotes = {};

        zhttp.crm.quote.get_all().then(function(response){
            if(response.data && response.data != 'false'){
                $rootScope.quotes = response.data;
                for(var i=0; i<$rootScope.quotes.length; i++){
                    $rootScope.quotes[i].date_creation = new Date($rootScope.quotes[i].date_creation);
                    $rootScope.quotes[i].date_limit = new Date($rootScope.quotes[i].date_limit);
                }
            }
        });
    }]);