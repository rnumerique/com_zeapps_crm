app.controller('ComZeappsCrmInvoiceListsPartialCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        $rootScope.invoices = {};
        $scope.id_company = 0;

        $scope.$on('comZeappsContact_dataEntrepriseHook', function(event, data){
            if ($scope.id_company !== data.id_company) {
                $scope.id_company = data.id_company;
                zhttp.crm.invoice.get_all($scope.id_company).then(function (response) {
                    if (response.data && response.data != 'false') {
                        $rootScope.invoices = response.data;
                        for (var i = 0; i < $rootScope.invoices.length; i++) {
                            $rootScope.invoices[i].date_creation = new Date($rootScope.invoices[i].date_creation);
                            $rootScope.invoices[i].date_limit = new Date($rootScope.invoices[i].date_limit);
                        }
                    }
                    else {
                        $rootScope.invoices = {};
                    }
                });
            }
        });

        $scope.$emit('comZeappsContact_triggerEntrepriseHook', {});

        $scope.totalHT = function(invoice){

            var total = 0;
            for(var i = 0; i < invoice.lines.length; i++){
                if(invoice.lines[i] != undefined && invoice.lines[i].num != 'subTotal' && invoice.lines[i].num != 'comment'){
                    total += invoice.lines[i].price_unit * invoice.lines[i].qty * ( 1 - (invoice.lines[i].discount / 100) );
                }
            }
            total = total * (1- (invoice.global_discount / 100) );
            return total.toFixed(2);

        };

        $scope.totalTTC = function(invoice){

            var total = 0;
            for(var i = 0; i < invoice.lines.length; i++){
                if(invoice.lines[i] != undefined && invoice.lines[i].num != 'subTotal' && invoice.lines[i].num != 'comment'){
                    total += invoice.lines[i].price_unit * invoice.lines[i].qty * ( 1 - (invoice.lines[i].discount / 100) ) * ( 1 + (invoice.lines[i].taxe / 100) );
                }
            }
            total = total * (1- (invoice.global_discount / 100) );
            return total.toFixed(2);

        };

        $scope.delete = function(invoice){
            zhttp.crm.invoice.del(invoice.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.invoices.splice($rootScope.invoices.indexOf(invoice), 1);
                }
            });
        }


    }]);