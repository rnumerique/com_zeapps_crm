app.controller("ComZeappsCrmProductFormCtrl", ["$scope", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "menu",
	function ($scope, $routeParams, $location, $rootScope, zhttp, zeapps_modal, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_product");

        $scope.currentBranch = {};
		$scope.tree = {
			branches: []
		};
		$scope.form = [];
		$scope.form.extra = {};
		$scope.error = "";
		$scope.max_length = {
			desc_short: 140,
			desc_long: 1000
		};

        $scope.accountingNumberHttp = zhttp.crm.accounting_number;
        $scope.accountingNumberTplNew = '/com_zeapps_crm/accounting_numbers/form_modal/';
        $scope.accountingNumberFields = [
            {label:'Numero',key:'number'},
            {label:'Libelle',key:'label'},
            {label:'Type',key:'type_label'}
        ];

		$scope.update = update;
		$scope.loadProductStock = loadProductStock;
		$scope.removeProductStock = removeProductStock;
        $scope.loadAccountingNumber = loadAccountingNumber;
		$scope.updateTaxe = updateTaxe;
		$scope.updatePrice = updatePrice;
		$scope.descState = descState;
		$scope.success = success;
		$scope.cancel = cancel;

		zhttp.config.product.get.attr().then(function(response){
			if(response.data && response.data != "false"){
				$scope.attributes = angular.fromJson(response.data.value);
			}
		});

		if ($routeParams.id && $routeParams.id > 0) {
			loadCtxtEdit();
		}

		if ($routeParams.category) {
			loadCtxtNew();
		}

		function update(branch){
            $scope.currentBranch = branch;
            $scope.form.id_cat = branch.id;
		}

		function loadCtxtNew(){
			zhttp.crm.category.tree().then(function (response) {
				if (response.status == 200) {
					$scope.tree.branches = response.data;
					zhttp.crm.category.openTree($scope.tree, $routeParams.category);
					zhttp.crm.category.get($routeParams.category).then(function (response) {
						if (response.status == 200) {
                            $scope.currentBranch = response.data;
						}
					});
				}
			});
		}

		function loadCtxtEdit(){
			zhttp.crm.category.tree().then(function (response) {
				if (response.status == 200) {
					$scope.tree.branches = response.data;
					zhttp.crm.product.get($routeParams.id).then(function (response) {
						if (response.status == 200) {
							$scope.form = response.data;
							$scope.form.price_ht = parseFloat($scope.form.price_ht);
							$scope.form.value_taxe = parseFloat($scope.form.value_taxe);
                            updatePrice('ttc');
							if($scope.form.extra) {
                                $scope.form.extra = angular.fromJson($scope.form.extra);
                            }
                            else{
								$scope.form.extra = {};
							}
							zhttp.crm.category.openTree($scope.tree, $scope.form.id_cat);
							zhttp.crm.category.get($scope.form.id_cat).then(function (response) {
								if (response.status == 200) {
                                    $scope.currentBranch = response.data;
								}
							});
							zhttp.crm.product_stock.get($scope.form.id_stock).then(function (response) {
								if (response.status == 200) {
									var stock = response.data.product_stock;
									$scope.form.name_stock = stock.ref ? stock.ref + " - " + stock.label : stock.label;
								}
							});
						}
					});
				}
			});
		}

		function loadProductStock() {
			zeapps_modal.loadModule("com_zeapps_crm", "search_product_stock", {}, function(objReturn) {
				if (objReturn) {
					$scope.form.id_stock = objReturn.id_stock;
					$scope.form.name_stock = objReturn.ref ? objReturn.ref + " - " + objReturn.label : objReturn.label;
				} else {
					$scope.form.id_stock = 0;
					$scope.form.name_stock = "";
				}
			});
		}

		function removeProductStock() {
			$scope.form.id_stock = 0;
			$scope.form.name_stock = "";
		}

        function loadAccountingNumber(accounting_number) {
            if (accounting_number) {
                $scope.form.accounting_number = accounting_number.number;
            } else {
                $scope.form.accounting_number = "";
            }
        }

		function updateTaxe(){
			angular.forEach($rootScope.taxes, function(taxe){
				if(taxe.id === $scope.form.id_taxe){
					$scope.form.value_taxe = taxe.value;
				}
			});
		}

		function updatePrice(price){
			if($scope.form.value_taxe || $scope.form.value_taxe === 0) {
				if (price === "ht") {
					$scope.form.price_ht = round2(parseFloat($scope.form.price_ttc) / ( 1 + parseFloat($scope.form.value_taxe) / 100));
				}
				if (price === "ttc") {
					$scope.form.price_ttc = round2(parseFloat($scope.form.price_ht) * ( 1 + parseFloat($scope.form.value_taxe) / 100));
				}
			}
		}

		function descState(current, max){
			if(current > max)
				return "text-danger";
			else if(current > Math.ceil(max*0.9) && current < max)
				return "text-warning";
			else
				return "text-success";

		}

		function success() {
			var data = {};

			if ($routeParams.id != 0) {
				data.id = $routeParams.id;
			}

			data.ref = $scope.form.ref;
			data.name = $scope.form.name;
			data.id_cat = $scope.form.id_cat;
			data.id_stock = $scope.form.id_stock;
			data.description = $scope.form.description;
			data.price_ht = $scope.form.price_ht;
			data.price_ttc = $scope.form.price_ttc;
			data.id_taxe = $scope.form.id_taxe;
			data.value_taxe = $scope.form.value_taxe;
			data.accounting_number = $scope.form.accounting_number;
			data.extra = angular.toJson($scope.form.extra);

			var formatted_data = angular.toJson(data);

			zhttp.crm.product.save(formatted_data).then(function (response) {
				if(typeof(response.data.error) === "undefined") {
					// pour que la page puisse être redirigé
					if ($routeParams.url_retour) {
						$location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp, "/"));
					} else {
						$location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
					}
				}
				else{
					$scope.error = response.data.error;
				}
			});
		}

		function cancel() {
			if ($routeParams.url_retour) {
				$location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp,"/"));
			} else {
				$location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
			}
		}

        function round2(num) {
            return +(Math.round(num + "e+2")  + "e-2");
        }
	}]);