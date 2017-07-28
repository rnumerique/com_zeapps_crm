app.controller("ComZeappsCrmDeliveryListsCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

		$scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_delivery");

		$rootScope.deliveries = [];

		zhttp.crm.delivery.get_all().then(function(response){
			if(response.data && response.data != "false"){
				$rootScope.deliveries = response.data;
				for(var i=0; i<$rootScope.deliveries.length; i++){
					$rootScope.deliveries[i].date_creation = new Date($rootScope.deliveries[i].date_creation);
					$rootScope.deliveries[i].date_limit = new Date($rootScope.deliveries[i].date_limit);
				}
			}
		});
	}]);