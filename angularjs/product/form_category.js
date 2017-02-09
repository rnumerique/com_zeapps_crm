app.controller('ComZeappsCrmProductFormCategoryCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_product");

        $scope.activeCategory = {
            data: ''
        };

        $scope.tree = {
            branches: []
        };

        $scope.form = [];

        $scope.error = '';

        if ($routeParams.id && $routeParams.id > 0) {
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    zhttp.crm.category.get($routeParams.id).then(function (response) {
                        if (response.status == 200) {
                            $scope.form = response.data;
                            zhttp.crm.category.get($scope.form.id_parent).then(function (response) {
                                if (response.status == 200) {
                                    $scope.activeCategory.data = response.data;
                                    zhttp.crm.category.openTree($scope.tree, $scope.activeCategory.data.id);
                                }
                            });
                        }
                    });
                }
            });
        }

        if($routeParams.id_parent && $routeParams.id_parent >= 0){
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    zhttp.crm.category.openTree($scope.tree, $routeParams.id_parent);
                    zhttp.crm.category.get($routeParams.id_parent).then(function (response) {
                        if (response.status == 200) {
                            $scope.activeCategory.data = response.data;
                        }
                    });
                }
            });
        }

        if ($routeParams.id_delete && $routeParams.id_delete > 0) {
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    zhttp.crm.category.get($routeParams.id_delete).then(function (response) {
                        if (response.status == 200) {
                            $scope.form = response.data;
                            zhttp.crm.category.get($scope.form.id_parent).then(function (response) {
                                if (response.status == 200) {
                                    $scope.activeCategory.data = response.data;
                                    zhttp.crm.category.openTree($scope.tree, $scope.activeCategory.data.id);
                                    $scope.delete($routeParams.id_delete);
                                }
                            });
                        }
                    });
                }
            });
        }

        $scope.$watch('activeCategory.data', function(value, old, scope){
            if(typeof(value.id) !== 'undefined'){
                scope.form.id_parent = value.id;
            }
        });

        $scope.success = function () {
            var data = {};

            if ($routeParams.id != 0) {
                data.id = $routeParams.id;
            }

            data.name = $scope.form.name;
            data.id_parent = $scope.form.id_parent;

            var formatted_data = angular.toJson(data);

            zhttp.crm.category.save(formatted_data).then(function (response) {
                if(typeof(response.data.error) === 'undefined') {
                    // pour que la page puisse être redirigé
                    if ($routeParams.url_retour) {
                        $location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp, "/"));
                    } else {
                        $location.path("/ng/com_zeapps_crm/product/category/" + response.data);
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
                $location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_parent);
            }
        };

    }]);