app.controller("ComZeappsCrmDeliveryConfigCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

		$scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_delivery");

		$scope.format = "";
		$scope.frequency = "";

		$scope.success = success;
		$scope.test = test;

		zhttp.config.delivery.get.format().then(function(response){
			if(response.data && response.data != "false"){
				$scope.format = response.data.value;
			}
		});

		zhttp.config.delivery.get.frequency().then(function(response){
			if(response.data && response.data != "false"){
				$scope.frequency = response.data.value;
			}
		});

		function success(){

			var data = {};

			data[0] = {
				id: "crm_delivery_format",
				value: $scope.format
			};
			data[1] = {
				id: "crm_delivery_frequency",
				value: $scope.frequency
			};

			var formatted_data = angular.toJson(data);
			zhttp.config.save(formatted_data);

		}

		function test(){
			var data = {};

			data["format"] = $scope.format;
			data["frequency"] = $scope.frequency;

			var formatted_data = angular.toJson(data);
			zhttp.crm.delivery.test(formatted_data).then(function(response){
				if(response.data && response.data != false){
					$scope.result = angular.fromJson(response.data);
				}
			});
		}

	}]);