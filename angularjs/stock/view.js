app.controller("ComZeappsCrmStockViewCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "$uibModal",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal) {

		$scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_stock");

		if($rootScope.selectedWarehouse === undefined)
			$rootScope.selectedWarehouse = "0";
		$scope.form = {};

		getStocks();

		$scope.updateWarehouse = updateWarehouse;
		$scope.success = success;
		$scope.delete = del;





		function updateWarehouse(){
			getStocks($rootScope.selectedWarehouse);
		}

		function success(){
			var formatted_data = angular.toJson($scope.form);

			zhttp.crm.product_stock.save(formatted_data, $rootScope.selectedWarehouse).then(function(response){
				if(response.data && response.data != false){
					var product_stock = response.data.product_stock;
					product_stock.value_ht = parseFloat(product_stock.value_ht);
					calcTimeLeft(product_stock);
					$scope.product_stocks.push(product_stock);

					$scope.form = {};
					$scope.shownForm = false;
				}
			});
		}

		function del(product_stock){
			var modalInstance = $uibModal.open({
				animation: true,
				templateUrl: "/assets/angular/popupModalDeBase.html",
				controller: "ZeAppsPopupModalDeBaseCtrl",
				size: "lg",
				resolve: {
					titre: function () {
						return "Attention";
					},
					msg: function () {
						return "Souhaitez-vous supprimer définitivement ce produit stocké?";
					},
					action_danger: function () {
						return "Annuler";
					},
					action_primary: function () {
						return false;
					},
					action_success: function () {
						return "Je confirme la suppression";
					}
				}
			});

			modalInstance.result.then(function (selectedItem) {
				if (selectedItem.action == "danger") {

				} else if (selectedItem.action == "success") {
					zhttp.crm.product_stock.del(product_stock.id_stock).then(function (response) {
						if (response.status == 200) {
							$scope.product_stocks.splice($scope.product_stocks.indexOf(product_stock), 1);
						}
					});
				}

			}, function () {
			});
		}

		function getStocks(id){
			zhttp.crm.product_stock.get_all(id).then(function(response){
				if(response.data && response.data != "false"){
					$scope.warehouses = response.data.warehouses;
					$scope.product_stocks = response.data.product_stocks;
					angular.forEach($scope.product_stocks, function(product_stock){
						product_stock.value_ht = parseFloat(product_stock.value_ht);
						calcTimeLeft(product_stock);
					});
				}
			});
		}
		function calcTimeLeft(product_stock){
			if(product_stock.avg > 0) {
				var timeleft = product_stock.total / product_stock.avg;

				if(timeleft > 0) {
					product_stock.timeleft = moment().to(moment().add(timeleft, "days"));
					product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
					product_stock.classRupture = "text-success";
				}
				else{
					product_stock.timeleft = "En rupture";
					product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
					product_stock.classRupture = "text-danger";
				}

				if($rootScope.selectedWarehouse > 0) {
					product_stock.timeResupply = moment().to(moment().add(timeleft, "days").subtract(product_stock.resupply_delay, product_stock.resupply_unit));
					product_stock.dateResupply = moment().add(timeleft, "days").subtract(product_stock.resupply_delay, product_stock.resupply_unit).format("DD/MM/YYYY");
					product_stock.classResupply = moment().isBefore(moment().add(timeleft, "days").subtract(product_stock.resupply_delay, product_stock.resupply_unit), "day") ? "text-success" : "text-danger";
				}
			}
			else{
				product_stock.timeleft = "-";
				product_stock.dateRupture = "";
				product_stock.timeResupply = "-";
				product_stock.dateResupply = "";
				product_stock.classRupture = "text-info";
			}
		}
	}]);