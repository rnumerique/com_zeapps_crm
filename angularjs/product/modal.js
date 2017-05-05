// declare the modal to the app service
listModuleModalFunction.push({
    module_name:'com_zeapps_crm',
    function_name:'search_product',
    templateUrl:'/com_zeapps_crm/product/modal_search_product',
    controller:'ComZeappsCrmModalSearchProductCtrl',
    size:'lg',
    resolve:{
        titre: function () {
            return 'Recherche d\'un produit';
        }
    }
});


app.controller('ComZeappsCrmModalSearchProductCtrl', function($scope, $uibModalInstance, zeHttp, titre, option) {
    $scope.titre = titre ;

    $scope.activeCategory = {
        data: ''
    };
    $scope.tree = {
        branches: []
    };
    $scope.quicksearch = "";

    $scope.cancel = cancel;
    $scope.select_product = select_product;

    getTree();

    $scope.$watch('activeCategory.data', function(value, old, scope){
        if(typeof(value.id) !== 'undefined'){
            zeHttp.crm.product.getOf(value.id).then(function (response) {
                if (response.status == 200) {
                    if(!angular.isArray(response.data)){
                        if(response.data != "false") {
                            scope.products = new Array(response.data);
                        }
                        else
                            scope.products = new Array();
                    }
                    else{
                        scope.products = response.data;
                    }
                }
            });
        }
    });

    function getTree() {
        zeHttp.crm.category.tree().then(function (response) {
            if (response.status == 200) {
                var id = $scope.activeCategory.data.id || 0;
                $scope.tree.branches = response.data;
                zeHttp.crm.category.openTree($scope.tree, id);
                zeHttp.crm.category.get(id).then(function (response) {
                    if (response.status == 200) {
                        $scope.activeCategory.data = response.data;
                    }
                });
            }
        });
    }

    function cancel() {
        $uibModalInstance.dismiss('cancel');
    }

    function select_product(produit) {
        $uibModalInstance.close(produit);
    }

}) ;