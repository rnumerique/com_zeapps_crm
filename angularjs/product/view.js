app.controller('ComZeappsCrmProductViewCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_product");

        $scope.activeCategory = {
            data: ''
        };
        $scope.tree = {
            branches: []
        };
        $scope.quicksearch = "";

        $scope.delete = del;
        $scope.delete_category = delete_category;
        $scope.force_delete_category = force_delete_category;

        $scope.sortableOptions = {
            stop: sortableStop
        };

        getTree();


        $scope.$watch('activeCategory.data', function(value, old, scope){
            if(typeof(value.id) !== 'undefined'){
                zhttp.crm.product.getOf(value.id).then(function (response) {
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
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    var id = $scope.activeCategory.data.id || $routeParams.id || 0;
                    $scope.tree.branches = response.data;
                    zhttp.crm.category.openTree($scope.tree, id);
                    zhttp.crm.category.get(id).then(function (response) {
                        if (response.status == 200) {
                            $scope.activeCategory.data = response.data;
                        }
                    });
                }
            });
        }

        function sortableStop(){
            var data = {
                categories: []
            };
            for(var i=0; i < $scope.activeCategory.data.branches.length; i++){
                $scope.activeCategory.data.branches[i].sort = i;
                data.categories[i] = $scope.activeCategory.data.branches[i];
            }
            zhttp.crm.category.update_order(data).then(function(response){
                if (response.status != 200) {
                    $rootScope.toasts.push({'danger':'There was an error when trying to access the Server, please try again ! If the problem persists contact the administrator of this website.'});
                }
            });
        }
        function del(product){
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
                    zhttp.crm.product.del(product.id).then(function (response) {
                        if (response.status == 200) {
                            $scope.products.splice($scope.products.indexOf(product), 1);
                        }
                    });
                }

            }, function () {
                //console.log("rien");
            });

        }

        function delete_category(id) {
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
                        return 'Souhaitez-vous supprimer définitivement cette catégorie ?';
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
                    zhttp.crm.category.del(id).then(function (response) {
                        if (response.status == 200) {
                            if(typeof(response.data.error) === 'undefined'){
                                if(response.data.hasProducts){
                                    $scope.force_delete_category(id);
                                }
                                else{
                                    $scope.activeCategory.data = response.data;
                                    getTree();
                                }
                            }
                            else{
                                $scope.error = response.data.error;
                            }
                        }
                    });
                }

            }, function () {
                //console.log("rien");
            });

        }

        function force_delete_category(id) {
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
                        return 'La catégorie ou l\'une de ses sous-catégories possedent toujours des produits. Si vous confirmez la suppresion les produits seront archivés.';
                    },
                    action_danger: function () {
                        return 'Annuler';
                    },
                    action_primary: function () {
                        return 'Archiver les produits & supprimer la catégorie';
                    },
                    action_success: function () {
                        return 'Supprimer les produits & supprimer la catégorie';
                    }
                }
            });

            modalInstance.result.then(function (selectedItem) {
                if (selectedItem.action == 'danger') {

                } else if (selectedItem.action == 'primary') {
                    zhttp.crm.category.del(id, false).then(function (response) {
                        if (response.status == 200) {
                            $scope.activeCategory.data = response.data;
                            getTree();
                        }
                    });
                } else if (selectedItem.action == 'success') {
                    zhttp.crm.category.del(id, true).then(function (response) {
                        if (response.status == 200) {
                            $scope.activeCategory.data = response.data;
                            getTree();
                        }
                    });
                }

            }, function () {
                //console.log("rien");
            });

        }
    }]);