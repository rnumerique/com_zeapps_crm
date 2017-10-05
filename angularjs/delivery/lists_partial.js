app.controller("ComZeappsCrmDeliveryListsPartialCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "$timeout", "toasts",
	function ($scope, $location, $rootScope, zhttp, $timeout, toasts) {

		if(!$rootScope.deliveries)
			$rootScope.deliveries = {};
		$scope.id_company = 0;
		$scope.filters = {
            main: [
                {
                    format: 'input',
                    field: 'numerotation LIKE',
                    type: 'text',
                    label: 'Numéro'
                },
                {
                    format: 'input',
                    field: 'libelle LIKE',
                    type: 'text',
                    label: 'Libellé'
                },
                {
                    format: 'input',
                    field: 'name_company LIKE',
                    type: 'text',
                    label: 'Entreprise'
                },
                {
                    format: 'input',
                    field: 'name_contact LIKE',
                    type: 'text',
                    label: 'Contact'
                }
            ],
            secondaries: [
                {
                    format: 'input',
                    field: 'date_creation >',
                    type: 'date',
                    label: 'Date de création : Début',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'date_creation <',
                    type: 'date',
                    label: 'Fin',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ht >',
                    type: 'text',
                    label: 'Total HT : Supérieur à',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ht <',
                    type: 'text',
                    label: 'Inférieur à',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ttc >',
                    type: 'text',
                    label: 'Total TTC : Supérieur à',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ttc <',
                    type: 'text',
                    label: 'Inférieur à',
                    size: 3
                }
            ]
        };
        $scope.filter_model = {};
        $scope.page = 1;
        $scope.pageSize = 15;
        $scope.total = 0;
        $scope.templateDelivery = '/com_zeapps_crm/deliveries/form_modal';

        var src = "deliveries";
        var src_id = 0;

        $scope.loadList = loadList;
        $scope.goTo = goTo;
		$scope.add = add;
		$scope.edit = edit;
		$scope.delete = del;

		$scope.$on("comZeappsContact_dataEntrepriseHook", function(event, data) {
			if ($scope.id_company !== data.id_company){
				$scope.id_company = data.id_company;
				src = "company";
                src_id = data.id_company;

                loadList(true) ;
			}
		});
		$scope.$emit("comZeappsContact_triggerEntrepriseHook", {});

		$scope.$on("comZeappsContact_dataContactHook", function(event, data) {
			if ($scope.id_contact !== data.id_contact){
				$scope.id_contact = data.id_contact;
				$scope.id_company = data.id_company;
                src = "contact";
                src_id = data.id_contact;

                loadList(true) ;
			}
		});
		$scope.$emit("comZeappsContact_triggerContactHook", {});

        $timeout(function(){ // Making sure the default call is only triggered after the potential broadcast from a hook
        	if(src_id === 0) {
                loadList(true);
            }
        }, 0);

        function loadList(context) {
            context = context || "";
            var offset = ($scope.page - 1) * $scope.pageSize;
            var formatted_filters = angular.toJson($scope.filter_model);

            zhttp.crm.delivery.get_all(src_id, src, $scope.pageSize, offset, context, formatted_filters).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.deliveries = response.data.deliveries;

                    for (var i = 0; i < $scope.deliveries.length; i++) {
                        $scope.deliveries[i].date_creation = $scope.deliveries[i].date_creation !== "0000-00-00 00:00:00" ? new Date($scope.deliveries[i].date_creation) : 0;
                        $scope.deliveries[i].global_discount = parseFloat($scope.deliveries[i].global_discount);
                        $scope.deliveries[i].probability = parseFloat($scope.deliveries[i].probability);
                    }

                    $scope.total = response.data.total;

                    $rootScope.deliveries.ids = response.data.ids;
                    $rootScope.deliveries.src_id = src_id;
                    $rootScope.deliveries.src = src;
                }
            });
        }

        function goTo(id){
            $location.url('/ng/com_zeapps_crm/delivery/'+id);
        }

        function add(delivery) {
            var data = delivery;

            if(data.date_creation) {
                var y = data.date_creation.getFullYear();
                var M = data.date_creation.getMonth();
                var d = data.date_creation.getDate();

                data.date_creation = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_creation = 0;
            }

            var formatted_data = angular.toJson(data);
            zhttp.crm.delivery.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    $rootScope.deliveries.ids.unshift(response.data);
                    $location.url("/ng/com_zeapps_crm/delivery/" + response.data);
                }
            });
        }

        function edit(delivery){
            var data = delivery;

            if(data.date_creation) {
                var y = data.date_creation.getFullYear();
                var M = data.date_creation.getMonth();
                var d = data.date_creation.getDate();

                data.date_creation = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_creation = 0;
            }

            var formatted_data = angular.toJson(data);

            zhttp.crm.delivery.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    toasts('success', "Les informations du devis ont bien été mises a jour");
                }
                else{
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour des informations du devis");
                }
            });
        }

		function del(delivery){
			zhttp.crm.delivery.del(delivery.id).then(function(response){
				if(response.data && response.data != "false"){
                    $scope.deliveries.splice($scope.deliveries.indexOf(delivery), 1);
                    $rootScope.deliveries.ids.splice($rootScope.deliveries.ids.indexOf(delivery.id), 1);
				}
			});
		}


	}]);