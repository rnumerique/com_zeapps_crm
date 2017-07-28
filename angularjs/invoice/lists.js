app.controller("ComZeappsCrmInvoiceListsCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

		$scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_invoice");

		$rootScope.invoices = [];

		zhttp.crm.invoice.get_all().then(function(response){
			if(response.data && response.data != "false"){
				$rootScope.invoices = response.data;
				for(var i=0; i<$rootScope.invoices.length; i++){
					$rootScope.invoices[i].date_creation = new Date($rootScope.invoices[i].date_creation);
					$rootScope.invoices[i].date_limit = new Date($rootScope.invoices[i].date_limit);
				}
			}
		});
	}]);