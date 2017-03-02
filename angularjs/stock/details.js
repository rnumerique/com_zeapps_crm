app.controller('ComZeappsCrmStockDetailsCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_stock");

        $scope.shownForm = false;
        $scope.shownMvtForm = false;
        var scales = {
            month : [],
            dates : [],
            date : [],
            days : []
        };
        $scope.selectedScale = "month";
        $scope.labels = [];
        $scope.data = [
            []
        ];
        $scope.navigationState = 'chart';
        $scope.page = 1;
        $scope.pageSize = 30;
        
        if($rootScope.selectedWarehouse === undefined)
            $rootScope.selectedWarehouse = '0';

        if($routeParams.id) {
            getStocks($routeParams.id, $rootScope.selectedWarehouse);
        }

        $scope.success = success;
        $scope.updateWarehouse = updateWarehouse;
        $scope.changeScaleTo = changeScaleTo;
        $scope.backgroundOf = backgroundOf;
        $scope.setIgnoredTo = setIgnoredTo;
        $scope.openMvtForm = openMvtForm;
        $scope.isMvtFormOpen = isMvtFormOpen;
        $scope.addMvt = addMvt;
        $scope.cancelMvt = cancelMvt;



        function openMvtForm(){
            $scope.mvtForm = {
                date_mvt : new Date()
            };
            $scope.shownMvtForm = true;
        }

        function isMvtFormOpen(){
            return $scope.shownMvtForm;
        }

        function addMvt(){
            var data = {};

            data.label = $scope.mvtForm.label;
            data.qty = $scope.mvtForm.qty;
            data.date_mvt = $scope.mvtForm.date_mvt;

            data.id_stock = $scope.product_stock.id_stock;
            data.id_warehouse = $rootScope.selectedWarehouse;
            data.name_table = "zeapps_stock_movements";
            data.id_table = 0;

            var formatted_data = angular.toJson(data);

            zhttp.crm.product_stock.add_mvt(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.shownMvtForm = false;
                    getStocks($routeParams.id, $rootScope.selectedWarehouse);
                }
            });
        }

        function cancelMvt(){
            $scope.shownMvtForm = false;
        }

        function setIgnoredTo(mvt, value){
            mvt.ignored = value;

            zhttp.crm.product_stock.ignore_mvt(mvt.id, value, $scope.product_stock.id, $rootScope.selectedWarehouse).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.product_stock.avg = response.data;
                    calcTimeLeft($scope.product_stock.total, $scope.product_stock.avg);
                }
            });
        }

        function backgroundOf(mvt){
            return mvt.qty > 0 ? 'bg-success' : 'bg-danger';
        }

        function changeScaleTo(scale){
            $scope.selectedScale = scale;
            parseMovements($scope.product_stock.last[$scope.selectedScale], $scope.product_stock.total);
        }

        function updateWarehouse(){
            getStocks($routeParams.id, $rootScope.selectedWarehouse);
        }

        function success(){
            var formatted_data = angular.toJson($scope.product_stock);

            zhttp.crm.product_stock.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false')
                    $scope.shownForm = false;
            });
        }

        function getStocks(id_stock, id_warehouse){
            zhttp.crm.product_stock.get(id_stock, id_warehouse).then(function (response) {
                if (response.data && response.data != 'false') {
                    $scope.product_stock = response.data.product_stock;
                    $scope.product_stock.value_ht = parseFloat(response.data.product_stock.value_ht);
                    $scope.product_stock.resupply_delay = parseInt(response.data.product_stock.resupply_delay);

                    $scope.warehouses = response.data.warehouses;

                    angular.forEach($scope.product_stock.movements, function(mvt){
                        mvt.date_mvt = new Date(mvt.date_mvt);
                    });

                    calcTimeLeft($scope.product_stock.total, $scope.product_stock.avg);
                    parseMovements($scope.product_stock.last[$scope.selectedScale], $scope.product_stock.total);
                }
            });
        }

        function calcTimeLeft(total, avg){
            if(avg > 0) {
                var timeleft = total / avg;

                if(timeleft > 0) {
                    $scope.product_stock.timeleft = moment().to(moment().add(timeleft, 'days'));
                    $scope.product_stock.dateRupture = moment().add(timeleft, 'days').format('DD/MM/YYYY');
                }
                else{
                    $scope.product_stock.timeleft = 'En rupture';
                    $scope.product_stock.dateRupture = moment().add(timeleft, 'days').format('DD/MM/YYYY');
                }

                if($rootScope.selectedWarehouse > 0) {
                    $scope.product_stock.timeResupply = moment().to(moment().add(timeleft, 'days').subtract($scope.product_stock.resupply_delay, $scope.product_stock.resupply_unit));
                    $scope.product_stock.dateResupply = moment().add(timeleft, 'days').subtract($scope.product_stock.resupply_delay, $scope.product_stock.resupply_unit).format('DD/MM/YYYY');
                }
            }
            else{
                $scope.product_stock.timeleft = 'Indeterminée';
                $scope.product_stock.dateRupture = '';
                $scope.product_stock.timeResupply = 'Indeterminée';
                $scope.product_stock.dateResupply = '';
            }
        }

        function parseMovements(mvts, total){
            if(scales.month.length === 0){
                set12Months();
                set3Months();
                set1Months();
                setDays();
            }

            $scope.labels = scales[$scope.selectedScale];

            var balance = [];
            var i = scales[$scope.selectedScale].length;
            $scope.data[0] = [];
            while (i --> 0){
                balance[i] = 0;
                $scope.data[0].push(0);
            }

            angular.forEach(mvts, function(mvt){
                balance[moment(mvt.date_mvt)[$scope.selectedScale]()] += parseFloat(mvt.qty);
            });

            if($scope.selectedScale !== 'date' && $scope.selectedScale !== 'dates')
                balance.unshift.apply(balance, balance.splice(moment().get($scope.selectedScale) + 1, balance.length));

            var count = scales[$scope.selectedScale].length;
            $scope.data[0][--count] = total || 0;
            while (count --> 0){
                $scope.data[0][count] = parseFloat($scope.data[0][count + 1]) - parseFloat(balance[count + 1]);
            }
        }

        function set12Months(){
            var count = 12;
            while (count --> 0){
                scales.month.push(moment().month(moment().get('month') - count).format("MMMM YYYY"));
            }
        }
        function set3Months(){
            var count = 90;
            while (count --> 0){
                scales.dates.push(moment().date(moment().get('date') - count).format("dddd Do"));
            }
        }
        function set1Months(){
            var count = 30;
            while (count --> 0){
                scales.date.push(moment().date(moment().get('date') - count).format("dddd Do"));
            }
        }
        function setDays(){
            var count = 7;
            while (count --> 0){
                scales.days.push(moment().day(moment().get('day') - count).format("dddd Do"));
            }
        }
    }]);