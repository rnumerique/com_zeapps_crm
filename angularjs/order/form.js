app.controller("ComZeappsCrmOrderFormCtrl", ["$scope", "$routeParams", "$rootScope", "zeHttp",
	function ($scope, $routeParams, $rootScope, zhttp) {

        $scope.accountManagerHttp = zhttp.app.user;
        $scope.accountManagerFields = [
            {label:'Prénom',key:'firstname'},
            {label:'Nom',key:'lastname'}
        ];

        $scope.companyHttp = zhttp.contact.company;
        $scope.companyTplNew = '/com_zeapps_contact/companies/form_modal/';
        $scope.companyFields = [
            {label:'Nom',key:'company_name'},
            {label:'Téléphone',key:'phone'},
            {label:'Ville',key:'billing_city'},
            {label:'Gestionnaire du compte',key:'name_user_account_manager'}
        ];

        $scope.contactHttp = zhttp.contact.contact;
        $scope.contactTplNew = '/com_zeapps_contact/contacts/form_modal/';
        $scope.contactFields = [
            {label:'Nom',key:'last_name'},
            {label:'Prénom',key:'first_name'},
            {label:'Entreprise',key:'name_company'},
            {label:'Téléphone',key:'phone'},
            {label:'Ville',key:'city'},
            {label:'Gestionnaire du compte',key:'name_user_account_manager'}
        ];

        $scope.accountingNumberHttp = zhttp.crm.accounting_number;
        $scope.accountingNumberTplNew = '/com_zeapps_crm/accounting_numbers/form_modal/';
        $scope.accountingNumberFields = [
            {label:'Numero',key:'number'},
            {label:'Libelle',key:'label'},
            {label:'Type',key:'type_label'}
        ];

		$scope.updateDateLimit = updateDateLimit;
		$scope.loadAccountManager = loadAccountManager;
		$scope.loadCompany = loadCompany;
		$scope.loadContact = loadContact;
        $scope.loadAccountingNumber = loadAccountingNumber;
        $scope.updateModality = updateModality;

		Initform();

		zhttp.crm.warehouse.get_all().then(function(response){
			if(response.data && response.data != "false"){
				$scope.warehouses = response.data;
			}
		});

		zhttp.crm.crm_origin.get_all().then(function(response){
			if(response.data && response.data != "false"){
				$scope.crm_origins = response.data;
			}
		});

		function Initform(){
			if($scope.form.id === undefined) {
                $scope.form.id_user_account_manager = $rootScope.user.id;
                $scope.form.name_user_account_manager = $rootScope.user.firstname + " " + $rootScope.user.lastname;
                $scope.form.id_warehouse = $rootScope.user.id_warehouse;
                $scope.form.date_creation = new Date();
                $scope.form.date_limit = new Date();
                $scope.form.date_limit.setDate($scope.form.date_limit.getDate() + 30);

                if($routeParams.id_company !== undefined && $routeParams.id_company !== 0){
                    zhttp.contact.company.get($routeParams.id_company).then(function(response){
                        if(response.data && response.data != "false"){
                            loadCompany(response.data.company);
                        }
                    });
                }
                if($routeParams.id_contact !== undefined && $routeParams.id_contact !== 0){
                    zhttp.contact.contact.get($routeParams.id_contact).then(function(response){
                        if(response.data && response.data != "false"){
                            loadContact(response.data.contact);
                        }
                    });
                }
            }
		}

		function updateDateLimit(){
			$scope.form.date_limit = new Date($scope.form.date_creation);
			$scope.form.date_limit.setDate($scope.form.date_limit.getDate() + 30);
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
                $scope.form.id_company = company.id;
                $scope.form.name_company = company.company_name;
                $scope.form.accounting_number = company.accounting_number || $scope.form.accounting_number;
                $scope.form.global_discount = parseFloat(company.discount) || $scope.form.global_discount;
                $scope.form.id_modality = company.id_modality || $scope.form.id_modality;
                $scope.form.label_modality = company.label_modality || $scope.form.label_modality;

                if(company.billing_address_1) {
                    $scope.form.billing_address_1 = company.billing_address_1 || "";
                    $scope.form.billing_address_2 = company.billing_address_2 || "";
                    $scope.form.billing_address_3 = company.billing_address_3 || "";
                    $scope.form.billing_city = company.billing_city || "";
                    $scope.form.billing_zipcode = company.billing_zipcode || "";
                    $scope.form.billing_state = company.billing_state || "";
                    $scope.form.billing_country_id = company.billing_country_id || "";
                    $scope.form.billing_country_name = company.billing_country_name || "";
                }

                if(company.delivery_address_1 || company.billing_address_1) {
                    $scope.form.delivery_address_1 = company.delivery_address_1 || company.billing_address_1 || "";
                    $scope.form.delivery_address_2 = company.delivery_address_2 || company.billing_address_2 || "";
                    $scope.form.delivery_address_3 = company.delivery_address_3 || company.billing_address_3 || "";
                    $scope.form.delivery_city = company.delivery_city || company.billing_city || "";
                    $scope.form.delivery_zipcode = company.delivery_zipcode || company.billing_zipcode || "";
                    $scope.form.delivery_state = company.delivery_state || company.billing_state || "";
                    $scope.form.delivery_country_id = company.delivery_country_id || company.billing_country_id || "";
                    $scope.form.delivery_country_name = company.delivery_country_name || company.billing_country_name || "";
                }
            } else {
                $scope.form.id_company = 0;
                $scope.form.name_company = "";
            }
        }

        function loadContact(contact) {
            if (contact) {
                $scope.form.id_contact = contact.id;
                $scope.form.name_contact = contact.last_name + " " + contact.first_name;
                $scope.form.accounting_number = $scope.form.accounting_number || contact.accounting_number;
                $scope.form.global_discount = $scope.form.global_discount || parseFloat(contact.discount);
                $scope.form.id_modality = $scope.form.id_modality || contact.id_modality;
                $scope.form.label_modality = $scope.form.label_modality || contact.label_modality;

                if(contact.address_1) {
                    $scope.form.billing_address_1 = contact.address_1 || "";
                    $scope.form.billing_address_2 = contact.address_2 || "";
                    $scope.form.billing_address_3 = contact.address_3 || "";
                    $scope.form.billing_city = contact.city || "";
                    $scope.form.billing_zipcode = contact.zipcode || "";
                    $scope.form.billing_state = contact.state || "";
                    $scope.form.billing_country_id = contact.country_id || "";
                    $scope.form.billing_country_name = contact.country_name || "";

                    $scope.form.delivery_address_1 = contact.address_1 || "";
                    $scope.form.delivery_address_2 = contact.address_2 || "";
                    $scope.form.delivery_address_3 = contact.address_3 || "";
                    $scope.form.delivery_city = contact.city || "";
                    $scope.form.delivery_zipcode = contact.zipcode || "";
                    $scope.form.delivery_state = contact.state || "";
                    $scope.form.delivery_country_id = contact.country_id || "";
                    $scope.form.delivery_country_name = contact.country_name || "";
                }

                if(contact.id_company !== "0" && ($scope.form.id_company === undefined || $scope.form.id_company === 0)){
					zhttp.contact.company.get(contact.id_company).then(function(response){
						if(response.data && response.data != "false"){
                            loadCompany(response.data.company);
						}
					})
				}
            } else {
                $scope.form.id_contact = 0;
                $scope.form.name_contact = "";
            }
        }

        function loadAccountingNumber(accounting_number) {
            if (accounting_number) {
                $scope.$parent.form.accounting_number = accounting_number.number;
            } else {
                $scope.$parent.form.accounting_number = "";
            }
        }

        function updateModality(){
            angular.forEach($scope.modalities, function(modality){
                if(modality.id === $scope.form.id_modality){
                    $scope.form.label_modality = modality.label;
                }
            });
        }
	}]);