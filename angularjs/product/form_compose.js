app.controller('ComZeappsCrmProductComposeFormCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_product");

        $scope.activeCategory = {
            data: ''
        };
        $scope.tree = {
            branches: []
        };
        $scope.form = [];
        $scope.form.lines = [];
        $scope.lineForm = {};

        $scope.loadProductStock = loadProductStock;
        $scope.removeProductStock = removeProductStock;
        $scope.updatePrice = updatePrice;
        $scope.ajouter_ligne = ajouter_ligne;
        $scope.success = success;
        $scope.edit = edit;
        $scope.validate = validate;
        $scope.cancel = cancel;
        $scope.cancelEdit = cancelEdit;




        zhttp.config.product.get.attr().then(function(response){
            if(response.data && response.data != 'false'){
                $scope.attributes = angular.fromJson(response.data.value);
            }
        });

        if ($routeParams.id && $routeParams.id > 0) {
            loadCtxtEdit();
        }

        if ($routeParams.category) {
            loadCtxtNew();
        }

        $scope.$watch('activeCategory.data', function(value, old, scope){
            if(typeof(value.id) !== 'undefined'){
                scope.form.id_cat = value.id;
            }
        });





        function loadCtxtEdit(){
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    zhttp.crm.product.get($routeParams.id).then(function (response) {
                        if (response.status == 200) {
                            $scope.form = response.data;
                            $scope.form.auto = !!parseInt($scope.form.auto);
                            $scope.form.value_taxe = parseFloat($scope.form.value_taxe);
                            $scope.form.price_ttc = parseFloat($scope.form.price_ttc);
                            angular.forEach($scope.form.lines, function(line){
                                line.quantite = parseInt(line.quantite);
                            });
                            zhttp.crm.category.openTree($scope.tree, $scope.form.id_cat);
                            zhttp.crm.category.get($scope.form.id_cat).then(function (response) {
                                if (response.status == 200) {
                                    $scope.activeCategory.data = response.data;
                                }
                            });
                        }
                    });
                }
            });
        }

        function loadCtxtNew(){
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    zhttp.crm.category.openTree($scope.tree, $routeParams.category);
                    zhttp.crm.category.get($routeParams.category).then(function (response) {
                        if (response.status == 200) {
                            $scope.activeCategory.data = response.data;
                        }
                    });
                }
            });
        }

        function loadProductStock() {
            zeapps_modal.loadModule("com_zeapps_crm", "search_product_stock", {}, function(objReturn) {
                if (objReturn) {
                    $scope.form.id_stock = objReturn.id_stock;
                    $scope.form.name_stock = objReturn.ref ? objReturn.ref + ' - ' + objReturn.label : objReturn.label;
                } else {
                    $scope.form.id_stock = 0;
                    $scope.form.name_stock = '';
                }
            });
        }

        function removeProductStock(){
            $scope.form.id_stock = 0;
            $scope.form.name_stock = '';
        }

        function updatePrice(){
            var total = 0;

            angular.forEach($scope.form.lines, function(line){
                total += parseFloat(line.product.price_ttc) * parseFloat(line.quantite);
            });

            if(!$scope.form.price_ttc)
                $scope.form.price_ttc = 0;

            var prorata = (parseFloat($scope.form.price_ttc) * 100 / total).toFixed(2);

            angular.forEach($scope.form.lines, function(line){
                line.prorata = prorata;
            });
        }

        function ajouter_ligne() {
            // charge la modal de la liste de produit
            zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function(objReturn) {
                //console.log(objReturn);
                if (objReturn) {
                    var data = {};
                    data.id = 0;
                    data.id_part = objReturn.id ;
                    data.quantite = 1 ;
                    data.product = objReturn;

                    $scope.form.lines.push(data) ;

                    $scope.updatePrice();
                }
            });
        }

        function success() {
            var data = {};

            if ($routeParams.id != 0) {
                data.id = $routeParams.id;
            }

            data.name = $scope.form.name;
            data.ref = $scope.form.ref;
            data.compose = 1;
            data.id_cat = $scope.form.id_cat;
            data.id_stock = $scope.form.id_stock;
            data.description = $scope.form.description;
            data.price_ttc = $scope.form.price_ttc;
            data.accounting_number = $scope.form.accounting_number;
            data.auto = $scope.form.auto;
            data.extra = angular.toJson($scope.form.extra);
            data.lines = $scope.form.lines;

            var formatted_data = angular.toJson(data);

            zhttp.crm.product.save(formatted_data).then(function (response) {
                if(typeof(response.data.error) === 'undefined') {
                    // pour que la page puisse être redirigé
                    if ($routeParams.url_retour) {
                        $location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp, "/"));
                    } else {
                        $location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
                    }
                }
                else{
                    $scope.error = response.data.error;
                }
            });
        }

        function edit(line){
            $scope.lineForm.quantite = line.quantite;
            $scope.lineForm.index = $scope.form.lines.indexOf(line);
        }

        function validate(line){
            line.quantite = $scope.lineForm.quantite;
            $scope.lineForm = {};
        }

        function cancelEdit(){
            $scope.lineForm = {};
        }

        function cancel() {
            if ($routeParams.url_retour) {
                $location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp,"/"));
            } else {
                $location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
            }
        }
    }]);