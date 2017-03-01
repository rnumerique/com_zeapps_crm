// declare the modal to the app service
listModuleModalFunction.push({
    module_name:'com_zeapps_crm',
    function_name:'finalize_order',
    templateUrl:'/com_zeapps_crm/orders/finalize_modal',
    controller:'ZeAppsCrmModalOrderFinalizeCtrl',
    size:'lg',
    resolve:{
        titre: function () {
            return 'Clôture de la commande';
        }
    }
});

app.controller('ZeAppsCrmModalOrderFinalizeCtrl', function($scope, $uibModalInstance, zeHttp, titre, option) {
    $scope.titre = titre ;

    $scope.form = {
        delivery : true,
        invoice : true
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