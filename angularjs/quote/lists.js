app.controller("ComZeappsCrmQuoteListsCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "menu",
	function ($scope, $route, $routeParams, $location, $rootScope, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_quote");

		$rootScope.quotes = [];
	}]);