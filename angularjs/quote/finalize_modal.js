// declare the modal to the app service
listModuleModalFunction.push({
    module_name:'com_zeapps_crm',
    function_name:'finalize_quote',
    templateUrl:'/com_zeapps_crm/quotes/finalize_modal',
    controller:'ZeAppsCrmModalQuoteFinalizeCtrl',
    size:'lg',
    resolve:{
        titre: function () {
            return 'Clôture du devis';
        }
    }
});

app.controller('ZeAppsCrmModalQuoteFinalizeCtrl', function($scope, $uibModalInstance, zeHttp, titre, option) {
    $scope.titre = titre ;

    $scope.form = {
        order : true
    };

    $scope.cancel = cancel;
    $scope.finalize = finalize;

    function cancel() {
        $uibModalInstance.dismiss('cancel');
    }

    function finalize() {
        $uibModalInstance.close($scope.form);
    }
}) ;