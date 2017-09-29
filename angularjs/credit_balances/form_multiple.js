app.controller("ComZeappsCrmCreditBalanceFormMultipleCtrl", ["$scope", "$routeParams", "zeHttp",
	function ($scope, $routeParams, zhttp) {

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

        $scope.$parent.form.date_payment = new Date();
        $scope.$parent.form.lines = {};

        $scope.total = 0;

        $scope.clearForm = clearForm;
        $scope.updateTotal = updateTotal;
        $scope.updateModality = updateModality;
        $scope.loadCompany = loadCompany;
        $scope.loadContact = loadContact;

        if($routeParams.id_contact && $routeParams.id_contact != 0){
            $scope.form.type = "contact";
            $scope.form.src_id = $routeParams.id_contact;
            zhttp.contact.contact.get($routeParams.id_contact).then(function (response) {
                if (response.status == 200) {
                    $scope.form.name_contact = response.data.contact.last_name + " " + response.data.contact.first_name;
                    loadList();
                }
            });
        }
        else if($routeParams.id_company && $routeParams.id_company != 0){
            $scope.form.type = "company";
            $scope.form.src_id = $routeParams.id_company;
            zhttp.contact.company.get($routeParams.id_company).then(function (response) {
                if (response.status == 200) {
                    $scope.form.name_company = response.data.company.company_name;
                    loadList();
                }
            });
        }

        function loadList() {
            zhttp.crm.credit_balance.get_all($scope.form.src_id, $scope.form.type, 1000, 0).then(function (response) {
                if (response.data && response.data !== "false") {
                    $scope.credits = response.data.credits;

                    angular.forEach($scope.credits, function (credit) {
                        credit.due_date = new Date(credit.due_date);
                    });
                }
            });
        }

        function clearForm(){
            $scope.form.src_id = "";
            $scope.form.name_company = "";
            $scope.form.name_contact = "";
        }

        function updateTotal(){
            $scope.total = 0;
            angular.forEach($scope.form.lines, function(line){
                $scope.total += line;
            });
        }

        function updateModality(){
            angular.forEach($scope.modalities, function(modality){
                if(modality.id === $scope.$parent.form.id_modality){
                    $scope.$parent.form.label_modality = modality.label;
                }
            });
        }

        function loadCompany(company) {
            if (company) {
                $scope.form.src_id = company.id;
                $scope.form.name_company = company.company_name;
                loadList();
            } else {
                $scope.form.src_id = "";
                $scope.form.name_company = "";
            }
        }

        function loadContact(contact) {
            if (contact) {
                $scope.form.src_id = contact.id;
                $scope.form.name_contact = contact.last_name + " " + contact.first_name;
                loadList();
            } else {
                $scope.form.src_id = "";
                $scope.form.name_contact = "";
            }
        }
	}]);