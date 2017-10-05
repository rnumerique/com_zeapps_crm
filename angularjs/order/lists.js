app.controller("ComZeappsCrmOrderListsCtrl", ["$scope", "$rootScope", "menu",
	function ($scope, $rootScope, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_order");

		$rootScope.orders = [];
	}]);