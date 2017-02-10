app.controller('ComZeappsCrmProductFormCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_product");

        $scope.activeCategory = {
            data: ''
        };

        $scope.tree = {
            branches: []
        };

        $scope.form = [];
        $scope.form.extra = {};

        $scope.error = '';

        $scope.max_length = {
            desc_short: 140,
            desc_long: 1000
        };

        zhttp.config.product.get.attr().then(function(response){
            if(response.data && response.data != 'false'){
                $scope.attributes = angular.fromJson(response.data.value);
            }
        });

        if ($routeParams.id && $routeParams.id > 0) {
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    zhttp.crm.product.get($routeParams.id).then(function (response) {
                        if (response.status == 200) {
                            $scope.form = response.data;
                            $scope.form.price_ht = parseFloat($scope.form.price_ht);
                            $scope.form.tva = parseFloat($scope.form.tva);
                            $scope.form.price_ttc = parseFloat($scope.form.price_ttc);
                            $scope.form.extra = angular.fromJson($scope.form.extra);
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

        if ($routeParams.category) {
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

        $scope.updatePrice = function(price){
            if($scope.form.tva && $scope.form.tva > 0) {
                if (price === 'ht') {
                    $scope.form.price_ht = parseFloat($scope.form.price_ttc / ( 1 + $scope.form.tva / 100).toFixed(2));
                }
                if (price === 'ttc') {
                    $scope.form.price_ttc = parseFloat($scope.form.price_ht * ( 1 + $scope.form.tva / 100).toFixed(2));
                }
            }
        };

        $scope.$watch('activeCategory.data', function(value, old, scope){
            if(typeof(value.id) !== 'undefined'){
                scope.form.id_cat = value.id;
            }
        });

        $scope.descState = function(current, max){
            if(current > max)
                return 'text-danger';
            else if(current > Math.ceil(max*0.9) && current < max)
                return 'text-warning';
            else
                return 'text-success';

        };

        $scope.delete = function (id) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: '/assets/angular/popupModalDeBase.html',
                controller: 'ZeAppsPopupModalDeBaseCtrl',
                size: 'lg',
                resolve: {
                    titre: function () {
                        return 'Attention';
                    },
                    msg: function () {
                        return 'Souhaitez-vous supprimer définitivement ce produit ?';
                    },
                    action_danger: function () {
                        return 'Annuler';
                    },
                    action_primary: function () {
                        return false;
                    },
                    action_success: function () {
                        return 'Je confirme la suppression';
                    }
                }
            });

            modalInstance.result.then(function (selectedItem) {
                if (selectedItem.action == 'danger') {

                } else if (selectedItem.action == 'success') {
                    zhttp.crm.product.del(id).then(function (response) {
                        if (response.status == 200) {
                            // pour que la page puisse être redirigé
                            if ($routeParams.url_retour) {
                                $location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp, "/"));
                            } else {
                                $location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
                            }
                        }
                    });
                }

            }, function () {
                //console.log("rien");
            });

        };

        $scope.success = function () {
            var data = {};

            if ($routeParams.id != 0) {
                data.id = $routeParams.id;
            }

            data.name = $scope.form.name;
            data.id_cat = $scope.form.id_cat;
            data.description = $scope.form.description;
            data.price_ht = $scope.form.price_ht;
            data.price_ttc = $scope.form.price_ttc;
            data.tva = $scope.form.tva;
            data.accounting_number = $scope.form.accounting_number;
            data.extra = angular.toJson($scope.form.extra);

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
        };

        $scope.cancel = function () {
            if ($routeParams.url_retour) {
                $location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp,"/"));
            } else {
                $location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
            }
        };
    }]);