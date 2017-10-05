app.controller("ComZeappsCrmDeliveryConfigCtrl", ["$scope", "zeHttp", "menu",
	function ($scope, zhttp, menu) {

        menu("com_ze_apps_config", "com_ze_apps_deliveries");

		$scope.format = "";
		$scope.numerotation = 1;

		$scope.success = success;
		$scope.test = test;

		zhttp.config.delivery.get.format().then(function(response){
			if(response.data && response.data != "false"){
				$scope.format = response.data.value;
			}
		});

		zhttp.config.delivery.get.numerotation().then(function(response){
			if(response.data && response.data != "false"){
				$scope.numerotation = parseInt(response.data.value);
			}
		});

		function success(){

			var data = {};

			data[0] = {
				id: "crm_delivery_format",
				value: $scope.format
			};
			data[1] = {
				id: "crm_delivery_numerotation",
				value: $scope.numerotation
			};

			var formatted_data = angular.toJson(data);
			zhttp.config.save(formatted_data);

		}

		function test(){
			var data = {};

			data["format"] = $scope.format;
			data["numerotation"] = $scope.numerotation;

			var formatted_data = angular.toJson(data);
			zhttp.crm.delivery.test(formatted_data).then(function(response){
				if(response.data && response.data != false){
					$scope.result = angular.fromJson(response.data);
				}
			});
		}

	}]);