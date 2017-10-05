app.controller("ComZeappsCrmOrderConfigCtrl", ["$scope", "zeHttp", "menu",
	function ($scope, zhttp, menu) {

        menu("com_ze_apps_config", "com_ze_apps_orders");

		$scope.format = "";
		$scope.numerotation = 1;

		$scope.success = success;
		$scope.test = test;

		zhttp.config.order.get.format().then(function(response){
			if(response.data && response.data != "false"){
				$scope.format = response.data.value;
			}
		});

		zhttp.config.order.get.numerotation().then(function(response){
			if(response.data && response.data != "false"){
                $scope.numerotation = parseInt(response.data.value);
			}
		});

		function success(){

			var data = {};

			data[0] = {
				id: "crm_order_format",
				value: $scope.format
			};
			data[1] = {
				id: "crm_order_numerotation",
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
			zhttp.crm.order.test(formatted_data).then(function(response){
				if(response.data && response.data != false){
					$scope.result = angular.fromJson(response.data);
				}
			});
		}

	}]);