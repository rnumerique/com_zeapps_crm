app.controller('ComZeappsCrmQuoteListsPartialCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        $rootScope.quotes = {};
        $scope.id_company = 0;

        $scope.$emit('comZeappsContact_triggerEntrepriseHook', {});

        $scope.$on('comZeappsContact_dataEntrepriseHook', function(event, data){
            $scope.id_company = data.id_company;
            zhttp.crm.quote.get_all($scope.id_company).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.quotes = response.data;
                    for(var i=0; i<$rootScope.quotes.length; i++){
                        $rootScope.quotes[i].date_creation = new Date($rootScope.quotes[i].date_creation);
                        $rootScope.quotes[i].date_limit = new Date($rootScope.quotes[i].date_limit);
                    }
                }
                else{
                    $rootScope.orders = {};
                }
            });
        });


        $scope.totalHT = function(quote){

            var total = 0;
            for(var i = 0; i < quote.lines.length; i++){
                if(quote.lines[i] != undefined && quote.lines[i].num != 'subTotal' && quote.lines[i].num != 'comment'){
                    total += quote.lines[i].price_unit * quote.lines[i].qty * ( 1 - (quote.lines[i].discount / 100) );
                }
            }
            total = total * (1- (quote.global_discount / 100) );
            return total.toFixed(2);

        };

        $scope.totalTTC = function(quote){

            var total = 0;
            for(var i = 0; i < quote.lines.length; i++){
                if(quote.lines[i] != undefined && quote.lines[i].num != 'subTotal' && quote.lines[i].num != 'comment'){
                    total += quote.lines[i].price_unit * quote.lines[i].qty * ( 1 - (quote.lines[i].discount / 100) ) * ( 1 + (quote.lines[i].taxe / 100) );
                }
            }
            total = total * (1- (quote.global_discount / 100) );
            return total.toFixed(2);

        };

        $scope.delete = function(quote){
            zhttp.crm.quote.del(quote.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.quotes.splice($rootScope.quotes.indexOf(quote), 1);
                }
            });
        }


    }]);