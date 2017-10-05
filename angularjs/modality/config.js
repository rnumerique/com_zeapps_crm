app.controller("ComZeappsCrmModalityConfigCtrl", ["$scope", "$rootScope", "zeHttp", "menu",
	function ($scope, $rootScope, zhttp, menu) {

        menu("com_ze_apps_config", "com_ze_apps_modalities");

        $scope.templateForm = "/com_zeapps_crm/modalities/form_modal";

        $scope.add = add;
        $scope.edit = edit;
		$scope.delete = del;

        function add(modality){
            var formatted_data = angular.toJson(modality);
            zhttp.crm.modality.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    modality.id = response.data;
                    $rootScope.modalities.push(modality);
                }
            });
        }

        function edit(modality){
            var formatted_data = angular.toJson(modality);
            zhttp.crm.modality.save(formatted_data);
        }

		function del(modality){
            zhttp.crm.modality.del(modality.id).then(function (response) {
                if (response.status == 200) {
                    $rootScope.modalities.splice($rootScope.modalities.indexOf(modality), 1);
                }
            });
		}
	}]);