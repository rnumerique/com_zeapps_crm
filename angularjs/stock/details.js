app.controller("ComZeappsCrmStockDetailsCtrl", ["$scope", "$routeParams", "$rootScope", "zeHttp", "toasts", "menu",
	function ($scope, $routeParams, $rootScope, zhttp, toasts, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_stock");

        $rootScope.current_warehouse = $rootScope.current_warehouse || $rootScope.user.id_warehouse;

        $scope.filters = {
            main: [
                {
                    format: 'select',
                    field: 'id_warehouse',
                    type: 'text',
                    label: 'Entrepôt',
                    options: []
                }
            ]
        };
        $scope.filter_model = {
            'id_warehouse': $rootScope.current_warehouse
        };
        $scope.templateStock = '/com_zeapps_crm/stock/form_modal';
        $scope.templateMvt = '/com_zeapps_crm/stock/form_mvt';
        $scope.templateTransfert = '/com_zeapps_crm/stock/form_transfert';

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
		$scope.navigationState = "chart";
        $scope.page = {
            current: 1
        };
        $scope.pageSize = 15;
        $scope.total = 0;

		$scope.postits = [];

		getStocks();

        $scope.getStocks = getStocks;
        $scope.loadList = loadList;
		$scope.edit = edit;
		$scope.changeScaleTo = changeScaleTo;
		$scope.backgroundOf = backgroundOf;
		$scope.setIgnoredTo = setIgnoredTo;
		$scope.addMvt = addMvt;
		$scope.addTransfert = addTransfert;


        function getStocks(){
            var id_warehouse = $scope.filter_model.id_warehouse || "";

            $rootScope.current_warehouse = $scope.filter_model.id_warehouse;

            zhttp.crm.product_stock.get($routeParams.id, id_warehouse).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.total = response.data.total;
                    $scope.page.current = 1;
                    $scope.product_stock = response.data.product_stock;
                    $scope.product_stock.value_ht = parseFloat(response.data.product_stock.value_ht);
                    $scope.product_stock.resupply_delay = parseInt(response.data.product_stock.resupply_delay);

                    $scope.filters.main[0].options = response.data.warehouses;

                    angular.forEach($scope.product_stock.movements, function(mvt){
                        mvt.date_mvt = new Date(mvt.date_mvt);
                    });

                    calcTimeLeft($scope.product_stock.total, $scope.product_stock.avg);
                    parseMovements($scope.product_stock.last[$scope.selectedScale], $scope.product_stock.total);

                    generatePostits();
                }
            });
        }

        function loadList() {
            var id_warehouse = $scope.filter_model.id_warehouse || "";
            var offset = ($scope.page.current - 1) * $scope.pageSize;

            zhttp.crm.product_stock.get_mvt($routeParams.id, id_warehouse, $scope.pageSize, offset).then(function (response) {
                if (response.data && response.data != "false") {
                	$scope.total = response.data.total;
                    $scope.product_stock.movements = response.data.stock_movements;

                    angular.forEach($scope.product_stock.movements, function(mvt){
                        mvt.date_mvt = new Date(mvt.date_mvt);
                    });
                }
            });
        }

        function edit(){
            var formatted_data = angular.toJson($scope.product_stock);

            zhttp.crm.product_stock.save(formatted_data).then(function(response){
            	if(response.data && response.data != "false"){
                    calcTimeLeft($scope.product_stock.total, $scope.product_stock.avg);
                    parseMovements($scope.product_stock.last[$scope.selectedScale], $scope.product_stock.total);

                    generatePostits();
				}
			});
        }

		function addMvt(stock_mvt){

            stock_mvt.id_warehouse = $scope.filter_model.id_warehouse;
            stock_mvt.id_stock = $scope.product_stock.id_stock;
            stock_mvt.id_table = "0";
            stock_mvt.name_table = "zeapps_stock_movements";

        	if(stock_mvt.date_mvt){
                var y = stock_mvt.date_mvt.getFullYear();
                var M = stock_mvt.date_mvt.getMonth();
                var d = stock_mvt.date_mvt.getDate();

                stock_mvt.date_mvt = new Date(Date.UTC(y, M, d));
			}

			var formatted_data = angular.toJson(stock_mvt);

			zhttp.crm.product_stock.add_mvt(formatted_data).then(function(response){
				if(response.data && response.data != "false"){
					getStocks();
				}
			});
		}

		function addTransfert(transfert){

			if(transfert.src !== transfert.trgt) {
                transfert.id_stock = $scope.product_stock.id_stock;
                transfert.id_table = "0";
                transfert.name_table = "zeapps_stock_movements";

                if (transfert.date_mvt) {
                    var y = transfert.date_mvt.getFullYear();
                    var M = transfert.date_mvt.getMonth();
                    var d = transfert.date_mvt.getDate();

                    transfert.date_mvt = new Date(Date.UTC(y, M, d));
                }

                var formatted_data = angular.toJson(transfert);
                zhttp.crm.product_stock.add_transfert(formatted_data).then(function (response) {
                    if (response.data && response.data != "false") {
                        getStocks();
                    }
                });
            }
            else{
				toasts("warning", "Vous ne pouvez pas faire de transferts vers l'entrepôt de départ");
			}
		}

		function setIgnoredTo(mvt, value){
			mvt.ignored = value;

			zhttp.crm.product_stock.ignore_mvt(mvt.id, value, $scope.product_stock.id, $scope.filter_model.id_warehouse).then(function(response){
				if(response.data && response.data != "false"){
					$scope.product_stock.avg = response.data;
					calcTimeLeft($scope.product_stock.total, $scope.product_stock.avg);
				}
			});
		}

		function backgroundOf(mvt){
			return mvt.qty > 0 ? "bg-success" : "bg-danger";
		}

		function changeScaleTo(scale){
			$scope.selectedScale = scale;
			parseMovements($scope.product_stock.last[$scope.selectedScale], $scope.product_stock.total);
		}

		function calcTimeLeft(total, avg){
			if(avg > 0) {
				var timeleft = total / avg;


				if(timeleft > 0) {
					$scope.product_stock.timeleft = moment().to(moment().add(timeleft, "days"));
					$scope.product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
					$scope.product_stock.colorRupture = "";
				}
				else{
					$scope.product_stock.timeleft = "En rupture";
					$scope.product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
					$scope.product_stock.colorRupture = "#cd0000";
				}

				if($scope.filter_model.id_warehouse > 0) {
					$scope.product_stock.timeResupply = moment().to(moment().add(timeleft, "days").subtract($scope.product_stock.resupply_delay, $scope.product_stock.resupply_unit));
					$scope.product_stock.dateResupply = moment().add(timeleft, "days").subtract($scope.product_stock.resupply_delay, $scope.product_stock.resupply_unit).format("DD/MM/YYYY");
					$scope.product_stock.colorResupply = moment().isBefore(moment().add(timeleft, "days").subtract($scope.product_stock.resupply_delay, $scope.product_stock.resupply_unit), "day") ? "" : "#cd0000";
				}
			}
			else{
				$scope.product_stock.timeleft = "-";
				$scope.product_stock.dateRupture = "";
				$scope.product_stock.timeResupply = "-";
				$scope.product_stock.dateResupply = "";
				$scope.product_stock.colorRupture = "";
				$scope.product_stock.colorResupply = "";
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

			if($scope.selectedScale !== "date" && $scope.selectedScale !== "dates")
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
				scales.month.push(moment().month(moment().get("month") - count).format("MMMM YYYY"));
			}
		}
		function set3Months(){
			var count = 90;
			while (count --> 0){
				scales.dates.push(moment().date(moment().get("date") - count).format("dddd Do"));
			}
		}
		function set1Months(){
			var count = 30;
			while (count --> 0){
				scales.date.push(moment().date(moment().get("date") - count).format("dddd Do"));
			}
		}
		function setDays(){
			var count = 7;
			while (count --> 0){
				scales.days.push(moment().day(moment().get("day") - count).format("dddd Do"));
			}
		}

        function generatePostits(){
            $scope.postits = [
                {
                    value: $scope.product_stock.total || 0,
                    legend: 'Stock',
                    filter: 'number'
                },
                {
                    value: $scope.product_stock.value_ht,
                    legend: 'Valeur Unitaire',
                    filter: 'currency'
                },
                {
                    value: $scope.product_stock.value_ht * $scope.product_stock.total,
                    legend: 'Valeur totale du stock',
                    filter: 'currency'
                },
                {
                    value: 	$scope.product_stock.timeleft +
							"<small>" + ($scope.product_stock.dateRupture ? ' (' +  $scope.product_stock.dateRupture + ')' : '') + "</small>",
					color: $scope.product_stock.colorRupture,
                    legend: 'Date prévisionnelle de rupture'
                }
            ];

			if($scope.filter_model.id_warehouse){
            	$scope.postits.push(
                    {
                        value: 	$scope.product_stock.timeResupply +
								"<small>" + ($scope.product_stock.dateResupply ? ' (' +  $scope.product_stock.dateResupply + ')' : '') + "</small>",
                        color: $scope.product_stock.colorResupply,
                        legend: 'Commande fournisseur avant'
                    }
				);
			}
        }
	}]);