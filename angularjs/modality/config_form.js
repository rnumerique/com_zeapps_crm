app.controller('ComZeappsCrmModalityConfigFormCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal', '$uibModal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal, $uibModal) {

        $scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_modalities");

        $scope.success = success;
        $scope.cancel = cancel;

        if($routeParams.id){
            loadCtxtEdit();
        }
        else{
            $scope.form = {
                type : '0',
                situation : '0',
                settlement_type : '0'
            };
        }


        function loadCtxtEdit(){
            zhttp.crm.modality.get($routeParams.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.form = response.data;
                    $scope.form.settlement_date = parseInt($scope.form.settlement_date);
                    $scope.form.settlement_delay = parseInt($scope.form.settlement_delay);
                    $scope.form.sort = parseInt($scope.form.sort);
                }
            })
        }

        function success(){

            var formatted_data = angular.toJson($scope.form);
            zhttp.crm.modality.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    if($scope.form.id){
                        angular.forEach($rootScope.modalities, function(modality){
                            if(modality.id == $scope.form.id){
                                $rootScope.modalities[$rootScope.modalities.indexOf(modality)] = $scope.form;
                            }
                        })
                    }
                    else{
                        $scope.form.id = response.data;
                        $rootScope.modalities.push($scope.form);
                    }

                    $location.url('/ng/com_zeapps/modalities');
                }
            });

        }

        function cancel(){
            $location.url('/ng/com_zeapps/modalities');
        }
    }]);