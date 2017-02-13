app.controller('ComZeappsCrmModalityConfigCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal', '$uibModal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal, $uibModal) {

        $scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_modalities");

        $scope.form = {};

        $scope.success = function(){

            var formatted_data = angular.toJson($scope.form);
            zhttp.crm.modality.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    if($scope.form.id){
                        angular.forEach($rootScope.modalities, function(modality){
                            if(modality.id == $scope.form.id){
                                modality.label = $scope.form.label;
                            }
                        })
                    }
                    else{
                        var modality = {};
                        modality.id = response.data;
                        modality.label = $scope.form.label;
                        $rootScope.modalities.push(modality);
                    }
                    $scope.form = {};
                }
            });

        };

        $scope.cancel = function(){
            $scope.form = {};
        };

        $scope.edit = function(modality){
            $scope.form.id = modality.id;
            $scope.form.label = modality.label;
        };

        $scope.delete = function(modality){
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
                        return 'Souhaitez-vous supprimer définitivement cette modalitée de paiement ?';
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
                    zhttp.crm.modality.del(modality.id).then(function (response) {
                        if (response.status == 200) {
                            $rootScope.modalities.splice($rootScope.modalities.indexOf(modality), 1);
                        }
                    });
                }

            }, function () {
                //console.log("rien");
            });
        }
    }]);