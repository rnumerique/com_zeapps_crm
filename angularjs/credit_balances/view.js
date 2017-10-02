app.controller("ComZeappsCrmCreditBalanceViewCtrl", ["$scope", "zeHttp", "$routeParams", "toasts", "menu",
	function ($scope, zhttp, $routeParams, toasts, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_credit_balance");

        $scope.templateForm = '/com_zeapps_crm/credit_balances/form_modal';

        $scope.add = add;
        $scope.edit = edit;
        $scope.delete = del;

        zhttp.crm.credit_balance.get($routeParams.id).then(function (response) {
            if (response.data && response.data !== "false") {
                $scope.credit = response.data.credit;
                $scope.credit.paid = parseFloat($scope.credit.paid);
                $scope.credit.due_date = new Date($scope.credit.due_date);

                $scope.details = response.data.details;
                angular.forEach($scope.details, function (detail) {
                    detail.date_payment = new Date(detail.date_payment);
                    detail.paid = parseFloat(detail.paid);
                });
            }
        });

        function add(detail) {
            var data = detail;

            if(data.date_payment) {
                var y = data.date_payment.getFullYear();
                var M = data.date_payment.getMonth();
                var d = data.date_payment.getDate();

                data.date_payment = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_payment = 0;
            }

            var formatted_data = angular.toJson(data);
            zhttp.crm.credit_balance.save(formatted_data).then(function(response){
                if(response.data && response.data !== "false"){
                    data.id = response.data;
                    $scope.details.push(data);
                    updateTotal();
                }
            });
        }

        function edit(detail){
            var data = detail;

            if(data.date_payment) {
                var y = data.date_payment.getFullYear();
                var M = data.date_payment.getMonth();
                var d = data.date_payment.getDate();

                data.date_payment = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_payment = 0;
            }

            var formatted_data = angular.toJson(data);

            zhttp.crm.credit_balance.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    updateTotal();
                    toasts('success', "Les informations du paiement ont bien été mises a jour");
                }
                else{
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour des informations du paiement");
                }
            });
        }

        function del(detail){
            zhttp.crm.credit_balance.del(detail.id).then(function(response){
                if(response.data && response.data != "false"){
                    $scope.details.splice($scope.details.indexOf(detail), 1);
                    updateTotal();
                }
            });
        }

        function updateTotal(){
            var total = 0;

            angular.forEach($scope.details, function(detail){
                total += parseFloat(detail.paid);
            });

            $scope.credit.paid = total;
            $scope.credit.left_to_pay = $scope.credit.total - total;
        }
	}]);