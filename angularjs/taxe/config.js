app.controller("ComZeappsCrmTaxeConfigCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "$uibModal",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal, $uibModal) {

        $scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_taxes");

        $scope.templateForm = "/com_zeapps_crm/taxes/form_modal";

        $scope.add = add;
        $scope.edit = edit;
        $scope.delete = del;

        function add(taxe){
            var formatted_data = angular.toJson(taxe);
            zhttp.crm.taxe.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    taxe.id = response.data;
                    $rootScope.taxes.push(taxe);
                }
            });
        }

        function edit(taxe){
            var formatted_data = angular.toJson(taxe);
            zhttp.crm.taxe.save(formatted_data);
        }

        function del(taxe){
            zhttp.crm.taxe.del(taxe.id).then(function (response) {
                if (response.status == 200) {
                    $rootScope.taxes.splice($rootScope.taxes.indexOf(taxe), 1);
                }
            });
        }
	}]);