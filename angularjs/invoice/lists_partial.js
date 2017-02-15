app.controller('ComZeappsCrmInvoiceListsPartialCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        if(!$rootScope.invoices)
            $rootScope.invoices = [];
        $scope.id_company = 0;

        $scope.$on('comZeappsContact_dataEntrepriseHook', function(event, data){
            if ($scope.id_company !== data.id_company) {
                $scope.id_company = data.id_company;
                zhttp.crm.invoice.get_all($scope.id_company, 'company').then(function (response) {
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

        $scope.$on('comZeappsContact_dataContactHook', function(event, data){
            if ($scope.id_contact !== data.id_contact) {
                $scope.id_contact = data.id_contact;
                zhttp.crm.invoice.get_all($scope.id_contact, 'contact').then(function (response) {
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

        $scope.$emit('comZeappsContact_triggerContactHook', {});

        $scope.filters = {};

        $scope.delete = function(invoice){
            zhttp.crm.invoice.del(invoice.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.invoices.splice($rootScope.invoices.indexOf(invoice), 1);
                }
            });
        }


    }]);