app.controller("ComZeappsCrmQuoteFormCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

		$scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_quote");

		$scope.form = {};

        $scope.accountManagerHttp = zhttp.app.user.modal;
        $scope.accountManagerFields = [
            {label:'Prénom',key:'firstname'},
            {label:'Nom',key:'lastname'}
        ];

        $scope.companyHttp = zhttp.contact.company.modal;
        $scope.companyFields = [
            {label:'Nom',key:'company_name'},
            {label:'Téléphone',key:'phone'},
            {label:'Ville',key:'billing_city'},
            {label:'Gestionnaire du compte',key:'name_user_account_manager'}
        ];

        $scope.contactHttp = zhttp.contact.contact.modal;
        $scope.contactFields = [
            {label:'Nom',key:'last_name'},
            {label:'Prénom',key:'first_name'},
            {label:'Entreprise',key:'name_company'},
            {label:'Téléphone',key:'phone'},
            {label:'Ville',key:'city'},
            {label:'Gestionnaire du compte',key:'name_user_account_manager'}
        ];

		$scope.updateDateLimit = updateDateLimit;
		$scope.success = success;
		$scope.cancel = cancel;
		$scope.loadAccountManager = loadAccountManager;
		$scope.loadCompany = loadCompany;
		$scope.loadContact = loadContact;

		Initform();

		if($routeParams.id_company && $routeParams.id_company != 0){
			zhttp.contact.company.get($routeParams.id_company).then(function(response){
				if(response.data && response.data != "false"){
					$scope.form.company = response.data;
					$scope.form.accounting_number = $scope.form.company.accounting_number || $scope.form.accounting_number;
				}
			});
		}
		if($routeParams.id_contact && $routeParams.id_contact != 0){
			zhttp.contact.contact.get($routeParams.id_contact).then(function(response){
				if(response.data && response.data != "false"){
					$scope.form.contact = response.data;
					$scope.form.contact.name = $scope.form.contact.last_name + " " + $scope.form.contact.first_name;
					$scope.form.accounting_number = $scope.form.accounting_number || $scope.form.contact.accounting_number;
				}
			});
		}
		zhttp.crm.warehouse.get_all().then(function(response){
			if(response.data && response.data != "false"){
				$scope.warehouses = response.data;
			}
		});

		function Initform(){
			$scope.form.id_user_account_manager = $rootScope.user.id;
			$scope.form.name_user_account_manager = $rootScope.user.firstname + " " + $rootScope.user.lastname;
			$scope.form.date_creation = new Date();
			$scope.form.date_limit = new Date();
			$scope.form.date_limit.setDate($scope.form.date_limit.getDate() + 30);
		}

		function updateDateLimit(){
			$scope.form.date_limit = new Date($scope.form.date_creation);
			$scope.form.date_limit.setDate($scope.form.date_limit.getDate() + 30);
		}

		function success(){
			var data = {};

			data["libelle"] = $scope.form.libelle;
			data["id_user"] = $scope.form.id_user_account_manager;
			data["id_warehouse"] = $scope.form.id_warehouse;
			data["id_company"] = $scope.form.company ? ($scope.form.company.id || 0) : 0;
			data["id_contact"] = $scope.form.contact ? ($scope.form.contact.id || 0) : 0;
			data["accounting_number"] = $scope.form.accounting_number;
			data["global_discount"] = $scope.form.global_discount;
			if($scope.form.date_creation) {
				var y = $scope.form.date_creation.getFullYear();
				var M = $scope.form.date_creation.getMonth();
				var d = $scope.form.date_creation.getDate();

				data["date_creation"] = new Date(Date.UTC(y, M, d));
			}
			else
				data["date_creation"] = 0;
			if($scope.form.date_limit) {
				var y2 = $scope.form.date_limit.getFullYear();
				var M2 = $scope.form.date_limit.getMonth();
				var d2 = $scope.form.date_limit.getDate();

				data["date_limit"] = new Date(Date.UTC(y2, M2, d2));
			}
			else
				data["date_limit"] = 0;
			data["modalities"] = $scope.form.modalities;
			data["reference_client"] = $scope.form.reference_client;

			var formatted_data = angular.toJson(data);

			zhttp.crm.quote.save(formatted_data).then(function(response){
				if(response.data && response.data != "false"){
					$location.url("/ng/com_zeapps_crm/quote/" + angular.fromJson(response.data));
				}
			});
		}

		function cancel(){
			$location.url("/ng/com_zeapps_crm/quote/");
		}

        function loadAccountManager(user) {
            if (user) {
                $scope.form.id_user_account_manager = user.id;
                $scope.form.name_user_account_manager = user.firstname + " " + user.lastname;
            } else {
                $scope.form.id_user_account_manager = 0;
                $scope.form.name_user_account_manager = "";
            }
        }

        function loadCompany(company) {
            if (company) {
                $scope.form.company = company;
                $scope.form.accounting_number = company.accounting_number || $scope.form.accounting_number;
            } else {
                if($scope.form.accounting_number === $scope.form.company.accounting_number){
                    if($scope.form.contact !== undefined){
                        $scope.form.accounting_number = $scope.form.contact.accounting_number;
                    }
                    else
                        delete $scope.form.accounting_number;
                }
                delete $scope.form.company;
            }
        }

        function loadContact(contact) {
            if (contact) {
                $scope.form.contact = contact;
                $scope.form.contact.name = contact.last_name + " " + contact.first_name;
                $scope.form.accounting_number = $scope.form.accounting_number || contact.accounting_number;
				if(contact.id_company !== "0" && $scope.form.company === undefined){
					zhttp.contact.company.get(contact.id_company).then(function(response){
						if(response.data && response.data != "false"){
                            loadCompany(response.data.company);
						}
					})
				}
            } else {
                if($scope.form.accounting_number === $scope.form.contact.accounting_number){
                    delete $scope.form.accounting_number;
                }
                delete $scope.form.contact;
            }
        }

	}]);