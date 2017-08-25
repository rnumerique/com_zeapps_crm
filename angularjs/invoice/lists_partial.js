app.controller("ComZeappsCrmInvoiceListsPartialCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "$timeout", "toasts",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $timeout, toasts) {

		if(!$rootScope.invoices)
			$rootScope.invoices = [];
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
                    field: 'date_limite >',
                    type: 'date',
                    label: 'Date limite : Début',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'date_limite <',
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
        $scope.templateInvoice = '/com_zeapps_crm/invoices/form_modal';

        var src = "invoices";
        var src_id = 0;

        $scope.loadList = loadList;
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

            zhttp.crm.invoice.get_all(src_id, src, $scope.pageSize, offset, context, formatted_filters).then(function (response) {
                if (response.data && response.data != "false") {
                    $rootScope.invoices = response.data.invoices;

                    for (var i = 0; i < $rootScope.invoices.length; i++) {
                        $rootScope.invoices[i].date_creation = new Date($rootScope.invoices[i].date_creation);
                        $rootScope.invoices[i].date_limit = new Date($rootScope.invoices[i].date_limit);
                        $rootScope.invoices[i].global_discount = parseFloat($rootScope.invoices[i].global_discount);
                        $rootScope.invoices[i].probability = parseFloat($rootScope.invoices[i].probability);
                    }

                    $scope.total = response.data.total;

                    $rootScope.invoices.src_id = src_id;
                    $rootScope.invoices.src = src;
                }
            });
        }

        function add(invoice) {
            var formatted_data = angular.toJson(invoice);
            zhttp.crm.invoice.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    $location.url("/ng/com_zeapps_crm/invoice/" + response.data);
                }
            });
        }

        function edit(invoice){
            var data = invoice;

            var y = data.date_creation.getFullYear();
            var M = data.date_creation.getMonth();
            var d = data.date_creation.getDate();

            data.date_creation = new Date(Date.UTC(y, M, d));

            var y = data.date_limit.getFullYear();
            var M = data.date_limit.getMonth();
            var d = data.date_limit.getDate();

            data.date_limit = new Date(Date.UTC(y, M, d));

            var formatted_data = angular.toJson(data);

            zhttp.crm.invoice.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    toasts('success', "Les informations du devis ont bien été mises a jour");
                }
                else{
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour des informations du devis");
                }
            });
        }

		function del(invoice){
			zhttp.crm.invoice.del(invoice.id).then(function(response){
				if(response.data && response.data != "false"){
					$rootScope.invoices.splice($rootScope.invoices.indexOf(invoice), 1);
				}
			});
		}


	}]);