app.controller("ComZeappsCrmWarehouseConfigCtrl", ["$scope", "zeHttp", "menu",
	function ($scope, zhttp, menu) {

		menu("com_ze_apps_config", "com_ze_apps_warehouses");

        $scope.templateForm = "/com_zeapps_crm/warehouse/form_modal";

        $scope.resupply_label = {
			'days' : 'jours',
			'weeks' : 'semaines',
			'months' : 'mois',
			'hours' : 'heures'
		};

        $scope.add = add;
        $scope.edit = edit;
        $scope.delete = del;

		zhttp.crm.warehouse.get_all().then(function(response){
			if(response.data && response.data != "false"){
				$scope.warehouses = response.data;
                angular.forEach($scope.warehouses, function(warehouse){
                    warehouse.resupply_delay = parseInt(warehouse.resupply_delay);
                });
			}
		});

		function add(warehouse){
			var formatted_data = angular.toJson(warehouse);
			zhttp.crm.warehouse.save(formatted_data).then(function(response){
				if(response.data && response.data != "false"){
                    warehouse.id = response.data;
					$scope.warehouses.push(warehouse);
				}
			});
		}

        function edit(warehouse){
            var formatted_data = angular.toJson(warehouse);
            zhttp.crm.warehouse.save(formatted_data);
        }

		function del(warehouse){
			zhttp.crm.warehouse.del(warehouse.id).then(function(response){
				if(response.data && response.data != "false"){
					$scope.warehouses.splice($scope.warehouses.indexOf(warehouse), 1);
				}
			});
		}
	}]);