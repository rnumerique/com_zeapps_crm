app.controller('ComZeappsCrmInvoiceListsCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_invoice");

        $rootScope.invoices = {};

        zhttp.crm.invoice.get_all().then(function(response){
            if(response.data && response.data != 'false'){
                $rootScope.invoices = response.data;
                for(var i=0; i<$scope.invoices.length; i++){
                    $rootScope.invoices[i].date_creation = new Date($rootScope.invoices[i].date_creation);
                    $rootScope.invoices[i].date_limit = new Date($rootScope.invoices[i].date_limit);
                }
            }
        });

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