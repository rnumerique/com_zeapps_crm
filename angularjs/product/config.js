app.controller("ComZeappsCrmProductConfigCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

		$scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_products");

        $scope.templateForm = "/com_zeapps_crm/product/config_modal";

        $scope.add = add;
        $scope.edit = edit;
        $scope.delete = del;

		zhttp.config.product.get.attr().then(function(response){
			if(response.data && response.data != "false"){
				$scope.attributes = angular.fromJson(response.data.value);
			}
		});

		function add(attribute){
			$scope.attributes.push(attribute);

			var formatted_data = angular.toJson($scope.attributes);
			zhttp.config.product.save.attr(formatted_data);
		}

        function edit(){
            var formatted_data = angular.toJson($scope.attributes);
            zhttp.config.product.save.attr(formatted_data);
        }

		function del(attribute){
            $scope.attributes.splice($scope.attributes.indexOf(attribute), 1);

			var formatted_data = angular.toJson($scope.attributes);
			zhttp.config.product.save.attr(formatted_data);
		}

	}]);