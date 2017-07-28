app.controller("ComZeappsCrmQuoteListsPartialCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

		if(!$rootScope.quotes)
			$rootScope.quotes = [];
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

		$scope.$on("comZeappsContact_dataEntrepriseHook", function(event, data) {
			if ($scope.id_company !== data.id_company){
				$scope.id_company = data.id_company;
				zhttp.crm.quote.get_all($scope.id_company, "company").then(function (response) {
					if (response.data && response.data != "false") {
						$rootScope.quotes = response.data;
						for (var i = 0; i < $rootScope.quotes.length; i++) {
							$rootScope.quotes[i].date_creation = new Date($rootScope.quotes[i].date_creation);
							$rootScope.quotes[i].date_limit = new Date($rootScope.quotes[i].date_limit);
						}
					}
					else {
						$rootScope.orders = {};
					}
				});
			}
		});
		$scope.$emit("comZeappsContact_triggerEntrepriseHook", {});

		$scope.$on("comZeappsContact_dataContactHook", function(event, data) {
			if ($scope.id_contact !== data.id_contact){
				$scope.id_contact = data.id_contact;
				$scope.id_company = data.id_company;
				zhttp.crm.quote.get_all($scope.id_contact, "contact").then(function (response) {
					if (response.data && response.data != "false") {
						$rootScope.quotes = response.data;
						for (var i = 0; i < $rootScope.quotes.length; i++) {
							$rootScope.quotes[i].date_creation = new Date($rootScope.quotes[i].date_creation);
							$rootScope.quotes[i].date_limit = new Date($rootScope.quotes[i].date_limit);
						}
					}
					else {
						$rootScope.orders = {};
					}
				});
			}
		});
		$scope.$emit("comZeappsContact_triggerContactHook", {});

		function del(quote){
			zhttp.crm.quote.del(quote.id).then(function(response){
				if(response.data && response.data != "false"){
					$rootScope.quotes.splice($rootScope.quotes.indexOf(quote), 1);
				}
			});
		}


	}]);