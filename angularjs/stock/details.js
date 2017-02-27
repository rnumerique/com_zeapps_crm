app.controller('ComZeappsCrmStockDetailsCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_stock");

        moment.locale('fr');

        $scope.labels = [];
        $scope.data = [
            []
        ];

        setMonths();

        if($routeParams.id) {
            zhttp.crm.product_stock.get($routeParams.id).then(function (response) {
                if (response.data && response.data != 'false') {
                    $scope.product_stock = response.data;

                    var timeleft = $scope.product_stock.total / $scope.product_stock.avg;
                    $scope.product_stock.timeleft = moment().to(moment().add(timeleft, 'days'));
                    $scope.product_stock.dateRupture = moment().add(timeleft, 'days').format('DD/MM/YYYY');

                    parseMovements($scope.product_stock.recent_mvts, $scope.product_stock.total);
                }
            });
        }

        function setMonths(){
            var count = 12;
            while (count --> 0){
                $scope.labels.push(moment().month(moment().get('month') - count).format("MMMM YYYY"));
                $scope.data[0].push(0);
            }
        }
        function parseMovements(mvts, total){
            var balancePerMonth = [];
            var i = 12;
            while (i --> 0) balancePerMonth[i] = 0;

            angular.forEach(mvts, function(mvt){
                balancePerMonth[moment(mvt.date_mvt).month()] += parseFloat(mvt.qty);
            });

            balancePerMonth.unshift.apply(balancePerMonth, balancePerMonth.splice(moment().get('month') + 1, balancePerMonth.length));

            var count = 12;
            $scope.data[0][--count] = total || 0;
            while (count --> 0){
                $scope.data[0][count] = parseFloat($scope.data[0][count + 1]) - parseFloat(balancePerMonth[count + 1]);
            }
        }
    }]);