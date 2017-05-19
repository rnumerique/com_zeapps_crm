app.controller('ComZeappsCrmTaxeConfigCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal', '$uibModal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal, $uibModal) {

        $scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_taxes");

        $scope.form = {};

        $scope.success = success;
        $scope.cancel = cancel;
        $scope.edit = edit;
        $scope.delete = del;
        
        function success(){

            $scope.form.active = $scope.form.active ? 1 : 0;

            var formatted_data = angular.toJson($scope.form);
            zhttp.crm.taxe.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    if($scope.form.id){
                        angular.forEach($rootScope.taxes, function(taxe){
                            if(taxe.id == $scope.form.id){
                                taxe.label = $scope.form.label;
                                taxe.value = $scope.form.value;
                                taxe.accounting_number = $scope.form.accounting_number;
                                taxe.active = $scope.form.active;
                            }
                        })
                    }
                    else{
                        var taxe = {};
                        taxe.id = response.data;
                        taxe.label = $scope.form.label;
                        taxe.value = $scope.form.value;
                        taxe.accounting_number = $scope.form.accounting_number;
                        taxe.active = $scope.form.active;
                        $rootScope.taxes.push(taxe);
                    }
                    $scope.form = {};
                }
            });

        }

        function cancel(){
            $scope.form = {};
        }

        function edit(taxe){
            $scope.form.id = taxe.id;
            $scope.form.label = taxe.label;
            $scope.form.value = taxe.value;
            $scope.form.accounting_number = taxe.accounting_number;
            $scope.form.active = !!parseInt(taxe.active);
        }

        function del(taxe){
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
                        return 'Souhaitez-vous supprimer définitivement cette taxe ?';
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
                    zhttp.crm.taxe.del(taxe.id).then(function (response) {
                        if (response.status == 200) {
                            $rootScope.taxes.splice($rootScope.taxes.indexOf(taxe), 1);
                        }
                    });
                }

            }, function () {
                //console.log("rien");
            });
        }
    }]);