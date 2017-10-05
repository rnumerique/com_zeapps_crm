app.controller("ComZeappsCrmProductViewCtrl", ["$scope", "$location", "zeHttp", "$uibModal", "toasts", "menu",
	function ($scope, $location, zhttp, $uibModal, toasts, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_product");

		$scope.currentBranch = {};
		$scope.tree = {
			branches: []
		};
        $scope.filters = {
            main: [
                {
                    format: 'input',
                    field: 'ref LIKE',
                    type: 'text',
                    label: 'Référence'
                },
                {
                    format: 'input',
                    field: 'name LIKE',
                    type: 'text',
                    label: 'Nom du produit'
                }
            ]
        };
        $scope.filter_model = {};
        $scope.page = 1;
        $scope.pageSize = 15;


        $scope.update = update;
        $scope.loadList = loadList;
        $scope.goTo = goTo;
		$scope.delete = del;
		$scope.delete_category = delete_category;
		$scope.force_delete_category = force_delete_category;

        var showSubCats = false;
		$scope.isSubCatOpen = function(){ return showSubCats; };
		$scope.openSubCats = function(){ showSubCats = true; };
		$scope.closeSubCats = function(){ showSubCats = false; };

		$scope.sortableOptions = {
			stop: sortableStop
		};

		getTree();

		function update(branch){
			$scope.currentBranch = branch;
			loadList();
		}

		function loadList(){
			var id = $scope.currentBranch ? $scope.currentBranch.id : 0;
            var offset = ($scope.page - 1) * $scope.pageSize;
            var formatted_filters = angular.toJson($scope.filter_model);

            zhttp.crm.product.modal(id, $scope.pageSize, offset, formatted_filters).then(function (response) {
                if (response.status == 200) {
                    $scope.products = response.data.data;
                    $scope.total = response.data.total;
                }
            });
		}

        function goTo(id, type){
			var ctrlr = (type == 0 ? 'product' : 'product_compose');
            $location.url('/ng/com_zeapps_crm/'+ctrlr+'/'+id);
        }

		function getTree() {
			zhttp.crm.category.tree().then(function (response) {
				if (response.status == 200) {
					$scope.tree.branches = response.data;
                    $scope.currentBranch = $scope.tree.branches[0];
					loadList();
				}
			});
		}

		function sortableStop(){
			var data = {
				categories: []
			};
			for(var i=0; i < $scope.currentBranch.branches.length; i++){
				$scope.currentBranch.branches[i].sort = i;
				data.categories[i] = $scope.currentBranch.branches[i];
			}
			zhttp.crm.category.update_order(data).then(function(response){
				if (response.status != 200) {
					toasts("danger", "There was an error when trying to access the Server, please try again ! If the problem persists contact the administrator of this website.");
				}
			});
		}
		function del(product){
			var modalInstance = $uibModal.open({
				animation: true,
				templateUrl: "/assets/angular/popupModalDeBase.html",
				controller: "ZeAppsPopupModalDeBaseCtrl",
				size: "lg",
				resolve: {
					titre: function () {
						return "Attention";
					},
					msg: function () {
						return "Souhaitez-vous supprimer définitivement ce produit ?";
					},
					action_danger: function () {
						return "Annuler";
					},
					action_primary: function () {
						return false;
					},
					action_success: function () {
						return "Je confirme la suppression";
					}
				}
			});

			modalInstance.result.then(function (selectedItem) {
				if (selectedItem.action == "danger") {

				} else if (selectedItem.action == "success") {
					zhttp.crm.product.del(product.id).then(function (response) {
						if (response.status == 200) {
							$scope.products.splice($scope.products.indexOf(product), 1);
						}
					});
				}

			}, function () {
				//console.log("rien");
			});

		}

		function delete_category(id) {
			var modalInstance = $uibModal.open({
				animation: true,
				templateUrl: "/assets/angular/popupModalDeBase.html",
				controller: "ZeAppsPopupModalDeBaseCtrl",
				size: "lg",
				resolve: {
					titre: function () {
						return "Attention";
					},
					msg: function () {
						return "Souhaitez-vous supprimer définitivement cette catégorie ?";
					},
					action_danger: function () {
						return "Annuler";
					},
					action_primary: function () {
						return false;
					},
					action_success: function () {
						return "Je confirme la suppression";
					}
				}
			});

			modalInstance.result.then(function (selectedItem) {
				if (selectedItem.action == "danger") {

				} else if (selectedItem.action == "success") {
					zhttp.crm.category.del(id).then(function (response) {
						if (response.status == 200) {
							if(typeof(response.data.error) === "undefined"){
								if(response.data.hasProducts){
									$scope.force_delete_category(id);
								}
								else{
									$scope.currentBranch = response.data;
									getTree();
								}
							}
							else{
								$scope.error = response.data.error;
							}
						}
					});
				}

			}, function () {
				//console.log("rien");
			});

		}

		function force_delete_category(id) {
			var modalInstance = $uibModal.open({
				animation: true,
				templateUrl: "/assets/angular/popupModalDeBase.html",
				controller: "ZeAppsPopupModalDeBaseCtrl",
				size: "lg",
				resolve: {
					titre: function () {
						return "Attention";
					},
					msg: function () {
						return "La catégorie ou l'une de ses sous-catégories possedent toujours des produits. Si vous confirmez la suppresion les produits seront archivés.";
					},
					action_danger: function () {
						return "Annuler";
					},
					action_primary: function () {
						return "Archiver les produits & supprimer la catégorie";
					},
					action_success: function () {
						return "Supprimer les produits & supprimer la catégorie";
					}
				}
			});

			modalInstance.result.then(function (selectedItem) {
				if (selectedItem.action == "danger") {

				} else if (selectedItem.action == "primary") {
					zhttp.crm.category.del(id, false).then(function (response) {
						if (response.status == 200) {
							$scope.currentBranch = response.data;
							getTree();
						}
					});
				} else if (selectedItem.action == "success") {
					zhttp.crm.category.del(id, true).then(function (response) {
						if (response.status == 200) {
							$scope.currentBranch = response.data;
							getTree();
						}
					});
				}

			}, function () {
				//console.log("rien");
			});

		}
	}]);