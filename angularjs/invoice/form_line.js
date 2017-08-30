app.controller("ComZeappsCrmInvoiceFormLineCtrl", ["$scope", "zeHttp",
	function ($scope, zhttp) {

        $scope.accountingNumberHttp = zhttp.crm.accounting_number;
        $scope.accountingNumberTplNew = '/com_zeapps_crm/accounting_numbers/form_modal/';
        $scope.accountingNumberFields = [
            {label:'Numero',key:'number'},
            {label:'Libelle',key:'label'},
            {label:'Type',key:'type_label'}
        ];

        $scope.updateTaxe = updateTaxe;
        $scope.loadAccountingNumber = loadAccountingNumber;

        function updateTaxe(){
            angular.forEach($scope.$parent.taxes, function(taxe){
                if(taxe.id === $scope.form.id_taxe){
                    $scope.form.value_taxe = taxe.value;
                }
            })
        }

        function loadAccountingNumber(accounting_number) {
            if (accounting_number) {
                $scope.form.accounting_number = accounting_number.number;
            } else {
                $scope.form.accounting_number = "";
            }
        }

	}]);