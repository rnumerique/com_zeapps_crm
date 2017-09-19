app.controller("ComZeappsCrmCreditBalanceListsPartialCtrl", ["$scope", "zeHttp", "$location", "$timeout",
	function ($scope, zhttp, $location, $timeout) {

        $scope.id_company = 0;
        $scope.filters = {
            main: [
                {
                    format: 'input',
                    field: 'numerotation LIKE',
                    type: 'text',
                    label: 'N° Facture'
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
            ]
        };
        $scope.filter_model = {};
        $scope.page = 1;
        $scope.pageSize = 15;
        $scope.total = 0;

        var src = "credits";
        var src_id = 0;

        $scope.loadList = loadList;
        $scope.goTo = goTo;
        $scope.isOverdue = isOverdue;

        $scope.$on("comZeappsContact_dataEntrepriseHook", function(event, data) {
            if ($scope.id_company !== data.id_company){
                src = "company";
                src_id = data.id_company;

                loadList() ;
            }
        });
        $scope.$emit("comZeappsContact_triggerEntrepriseHook", {});

        $scope.$on("comZeappsContact_dataContactHook", function(event, data) {
            if ($scope.id_contact !== data.id_contact){
                src = "contact";
                src_id = data.id_contact;

                loadList() ;
            }
        });
        $scope.$emit("comZeappsContact_triggerContactHook", {});

        $timeout(function(){ // Making sure the default call is only triggered after the potential broadcast from a hook
            if(src_id === 0) {
                loadList();
            }
        }, 0);

        function loadList() {
            var offset = ($scope.page - 1) * $scope.pageSize;
            var formatted_filters = angular.toJson($scope.filter_model);

            zhttp.crm.credit_balance.get_all(src_id, src, $scope.pageSize, offset, formatted_filters).then(function (response) {
                if (response.data && response.data !== "false") {
                    $scope.credits = response.data.credits;
                    $scope.total = response.data.total;

                    angular.forEach($scope.credits, function (credit) {
                        credit.due_date = new Date(credit.due_date);
                    });
                }
            });
        }

		function goTo(id){
		    $location.url('/ng/com_zeapps_crm/credit_balances/' + id);
        }

        function isOverdue(credit){
		    return credit.due_date < new Date() ? 'bg-danger' : '';
        }
	}]);