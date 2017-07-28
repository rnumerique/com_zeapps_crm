app.controller("ComZeappsCrmDeliveryListsPartialCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

		if(!$rootScope.deliveries)
			$rootScope.deliveries = [];
		$scope.id_company = 0;
		$scope.filter = {
			model: {},
			options: {
				main: [
					{
						format: 'input',
						field: 'numerotation',
						type: 'text',
						label: 'Numéro'
					},
					{
						format: 'input',
						field: 'libelle',
						type: 'text',
						label: 'Nom'
					},
					{
						format: 'input',
						field: 'client',
						type: 'text',
						label: 'Destinataire'
					}
				],
				secondaries: [
					{
						format: 'input',
						field: 'date_creation_start',
						type: 'date',
						label: 'Date de création : Début',
						size: 3
					},
					{
						format: 'input',
						field: 'date_creation_end',
						type: 'date',
						label: 'Fin',
						size: 3
					},
					{
						format: 'input',
						field: 'date_limite_start',
						type: 'date',
						label: 'Date limite : Début',
						size: 3
					},
					{
						format: 'input',
						field: 'date_limite_end',
						type: 'date',
						label: 'Fin',
						size: 3
					},
					{
						format: 'input',
						field: 'total_ht_floor',
						type: 'text',
						label: 'Total HT : Supérieur à',
						size: 3
					},
					{
						format: 'input',
						field: 'total_ht_ceiling',
						type: 'text',
						label: 'Inférieur à',
						size: 3
					},
					{
						format: 'input',
						field: 'total_ttc_floor',
						type: 'text',
						label: 'Total TTC : Supérieur à',
						size: 3
					},
					{
						format: 'input',
						field: 'total_ttc_ceiling',
						type: 'text',
						label: 'Inférieur à',
						size: 3
					}
				]
			}
		};

		$scope.delete = del;

		$scope.$on("comZeappsContact_dataEntrepriseHook", function(event, data){
			if ($scope.id_company !== data.id_company) {
				$scope.id_company = data.id_company;
				zhttp.crm.delivery.get_all($scope.id_company, "company").then(function (response) {
					if (response.data && response.data != "false") {
						$rootScope.deliveries = response.data;
						for (var i = 0; i < $rootScope.deliveries.length; i++) {
							$rootScope.deliveries[i].date_creation = new Date($rootScope.deliveries[i].date_creation);
							$rootScope.deliveries[i].date_limit = new Date($rootScope.deliveries[i].date_limit);
						}
					}
					else {
						$rootScope.deliveries = {};
					}
				});
			}
		});
		$scope.$emit("comZeappsContact_triggerEntrepriseHook", {});

		$scope.$on("comZeappsContact_dataContactHook", function(event, data){
			if ($scope.id_contact !== data.id_contact) {
				$scope.id_contact = data.id_contact;
				$scope.id_company = data.id_company;
				zhttp.crm.delivery.get_all($scope.id_contact, "contact").then(function (response) {
					if (response.data && response.data != "false") {
						$rootScope.deliveries = response.data;
						for (var i = 0; i < $rootScope.deliveries.length; i++) {
							$rootScope.deliveries[i].date_creation = new Date($rootScope.deliveries[i].date_creation);
							$rootScope.deliveries[i].date_limit = new Date($rootScope.deliveries[i].date_limit);
						}
					}
					else {
						$rootScope.deliveries = {};
					}
				});
			}
		});
		$scope.$emit("comZeappsContact_triggerContactHook", {});

		function del(delivery){
			zhttp.crm.delivery.del(delivery.id).then(function(response){
				if(response.data && response.data != "false"){
					$rootScope.deliveries.splice($rootScope.deliveries.indexOf(delivery), 1);
				}
			});
		}


	}]);