app.controller("ComZeappsCrmOrderViewCtrl", ["$scope", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "Upload", "crmTotal", "zeHooks", "toasts", "menu",
	function ($scope, $routeParams, $location, $rootScope, zhttp, zeapps_modal, Upload, crmTotal, zeHooks, toasts, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_order");

		var code_exists = 0;
		var code = "";

		$scope.$on("comZeappsCrm_triggerOrderHook", broadcast);
		$scope.hooks = zeHooks.get("comZeappsCrm_OrderHook");

		$scope.progress = 0;
		$scope.activities = [];
		$scope.documents = [];

		$scope.orderLineTplUrl = "/com_zeapps_crm/orders/form_line";
        $scope.orderCommentTplUrl = "/com_zeapps_crm/crm_commons/form_comment";
        $scope.orderActivityTplUrl = "/com_zeapps_crm/crm_commons/form_activity";
        $scope.orderDocumentTplUrl = "/com_zeapps_crm/crm_commons/form_document";
		$scope.templateEdit = "/com_zeapps_crm/orders/form_modal";

		$scope.lines = [];

        $scope.sortable = {
            connectWith: ".sortableContainer",
            disabled: false,
            axis: "y",
            stop: sortableStop
        };

		$scope.setTab = setTab;

		$scope.back = back;
		$scope.first_order = first_order;
		$scope.previous_order = previous_order;
		$scope.next_order = next_order;
		$scope.last_order = last_order;

		$scope.updateStatus = updateStatus;
		$scope.updateOrder = updateOrder;
		$scope.transform = transform;

		$scope.addFromCode = addFromCode;
        $scope.keyEventaddFromCode = keyEventaddFromCode;
		$scope.addLine = addLine;
        $scope.editLine = editLine;
		$scope.addSubTotal = addSubTotal;
		$scope.addComment = addComment;
        $scope.editComment = editComment;

		$scope.deleteLine = deleteLine;

		$scope.subtotalHT = subtotalHT;
		$scope.subtotalTTC = subtotalTTC;

		$scope.addActivity = addActivity;
		$scope.editActivity = editActivity;
		$scope.deleteActivity = deleteActivity;

        $scope.addDocument = addDocument;
        $scope.editDocument = editDocument;
		$scope.deleteDocument = deleteDocument;

		$scope.print = print;


		//////////////////// INIT ////////////////////
		if($rootScope.orders === undefined || $rootScope.orders.ids === undefined) {
            $rootScope.orders = {};
            $rootScope.orders.ids = [];
		}
		else{
			initNavigation();
		}

		/******* gestion de la tabs *********/
		$scope.navigationState = "body";
		if ($rootScope.comZeappsCrmLastShowTabOrder) {
			$scope.navigationState = $rootScope.comZeappsCrmLastShowTabOrder ;
		}

		if($routeParams.id && $routeParams.id > 0){
			zhttp.crm.order.get($routeParams.id).then(function(response){
				if(response.data && response.data != "false"){
					$scope.order = response.data.order;

                    $scope.credits = response.data.credits;

                    $scope.activities = response.data.activities || [];
					angular.forEach($scope.activities, function(activity){
						activity.deadline = new Date(activity.deadline);
					});

					$scope.documents = response.data.documents || [];
                    angular.forEach($scope.documents, function(document){
                        document.date = new Date(document.date);
                    });

                    $scope.order.global_discount = parseFloat($scope.order.global_discount);
                    $scope.order.probability = parseFloat($scope.order.probability);
					$scope.order.date_creation = new Date($scope.order.date_creation);
					$scope.order.date_limit = new Date($scope.order.date_limit);

					var i;

					for(i=0;i<$scope.activities.length;i++){
						$scope.activities[i].reminder = new Date($scope.activities[i].reminder);
					}

					for(i=0;i<$scope.documents.length;i++){
						$scope.documents[i].created_at = new Date($scope.documents[i].created_at);
					}

					var lines = response.data.lines || [];
					angular.forEach(lines, function(line){
						line.price_unit = parseFloat(line.price_unit);
						line.qty = parseFloat(line.qty);
						line.discount = parseFloat(line.discount);
					});
					$scope.lines = lines;

					var line_details = response.data.line_details || [];
					angular.forEach(line_details, function(line_detail){
                        line_detail.price_unit = parseFloat(line_detail.price_unit);
                        line_detail.qty = parseFloat(line_detail.qty);
                        line_detail.discount = parseFloat(line_detail.discount);
					});
					$scope.line_details = line_details;

                    crmTotal.init($scope.order, $scope.lines, $scope.line_details);
                    $scope.tvas = crmTotal.get.tvas;
                    var totals = crmTotal.get.totals;
                    $scope.order.total_prediscount_ht = totals.total_prediscount_ht;
                    $scope.order.total_prediscount_ttc = totals.total_prediscount_ttc;
                    $scope.order.total_discount = totals.total_discount;
                    $scope.order.total_ht = totals.total_ht;
                    $scope.order.total_tva = totals.total_tva;
                    $scope.order.total_ttc = totals.total_ttc;
				}
			});
		}

		//////////////////// FUNCTIONS ////////////////////

		function broadcast(event, data){
			if(data.received){
                code_exists++;
			}
			else if(data.found !== undefined && code_exists > 0){
				if(data.found){
					code_exists = 0;
				}
				else{
					code_exists--;
					if(code_exists === 0){
                        toasts("danger", "Aucun produit avec le code " + code + " trouvé dans la base de donnée.");
					}
				}
			}
			else {
                $rootScope.$broadcast("comZeappsCrm_dataOrderHook",
                    {
                        order: $scope.order
                    }
                );
            }
		}
		function broadcast_code(code){
			$rootScope.$broadcast("comZeappsCrm_dataOrderHook",
				{
                    code: code
				}
			);
		}

		function setTab(tab){
            $rootScope.comZeappsCrmLastShowTabOrder = tab;
            $scope.navigationState = tab;
		}

		function back(){
            if ($rootScope.orders.src === undefined || $rootScope.orders.src === "orders") {
                $location.path("/ng/com_zeapps_crm/order/");
            }
            else if ($rootScope.orders.src === 'company') {
                $location.path("/ng/com_zeapps_contact/companies/" + $rootScope.orders.src_id);
            }
            else if ($rootScope.orders.src === 'contact') {
                $location.path("/ng/com_zeapps_contact/contacts/" + $rootScope.orders.src_id);
            }
		}

		function first_order() {
			if ($scope.order_first != 0) {
				$location.path("/ng/com_zeapps_crm/order/" + $scope.order_first);
			}
		}

		function previous_order() {
			if ($scope.order_previous != 0) {
				$location.path("/ng/com_zeapps_crm/order/" + $scope.order_previous);
			}
		}

		function next_order() {
			if ($scope.order_next) {
				$location.path("/ng/com_zeapps_crm/order/" + $scope.order_next);
			}
		}

		function last_order() {
			if ($scope.order_last) {
				$location.path("/ng/com_zeapps_crm/order/" + $scope.order_last);
			}
		}

        function updateStatus(){
			var data = {};

			data.id = $scope.order.id;
			data.status = $scope.order.status;

			var formatted_data = angular.toJson(data);

			zhttp.crm.order.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    toasts('success', "Le status du devis a bien été mis à jour.");
                }
                else{
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour du status du devis");
                }
            });
		}

		function transform(){
			zeapps_modal.loadModule("com_zeapps_crm", "transform_document", {}, function(objReturn) {
				if (objReturn) {
					var formatted_data = angular.toJson(objReturn);
					zhttp.crm.order.transform($scope.order.id, formatted_data).then(function(response){
						if(response.data && response.data != "false"){
                            zeapps_modal.loadModule("com_zeapps_crm", "transformed_document", response.data);
						}
					});
				}
			});
		}

        function keyEventaddFromCode(){
            if($event.which === 13){
                addFromCode();
            }
        }

		function addFromCode(){
			if($scope.codeProduct !== "") {
                code = $scope.codeProduct;
                zhttp.crm.product.get_code(code).then(function (response) {
                    if (response.data && response.data != "false") {
                        var line = {
                            id_order: $routeParams.id,
                            type: "product",
                            id_product: response.data.id,
                            ref: response.data.ref,
                            designation_title: response.data.name,
                            designation_desc: response.data.description,
                            qty: 1,
                            discount: 0.00,
                            price_unit: parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc),
                            id_taxe: response.data.id_taxe,
                            value_taxe: parseFloat(response.data.value_taxe),
                            accounting_number: parseFloat(response.data.accounting_number),
                            sort: $scope.lines.length
                        };
                        crmTotal.line.update(line);

                        $scope.codeProduct = "";

                        var formatted_data = angular.toJson(line);
                        zhttp.crm.order.line.save(formatted_data).then(function (response) {
                            if (response.data && response.data != "false") {
                                line.id = response.data;
                                $scope.lines.push(line);
                                updateOrder();
                            }
                        });
                    }
                    else {
                    	if($scope.hooks.length > 0) {
                            broadcast_code($scope.codeProduct);
                        }
                        else{
                            toasts("danger", "Aucun produit avec le code " + code + " trouvé dans la base de donnée.");
						}
                    }
                });
            }
		}

		function addLine(){
			// charge la modal de la liste de produit
			zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function(objReturn) {
				if (objReturn) {
					var line = {
						id_order: $routeParams.id,
						type: "product",
						id_product: objReturn.id,
						ref: objReturn.ref,
						designation_title: objReturn.name,
						designation_desc: objReturn.description,
						qty: 1,
						discount: 0.00,
						price_unit: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
						id_taxe: objReturn.id_taxe,
						value_taxe: parseFloat(objReturn.value_taxe),
                        accounting_number: parseFloat(objReturn.accounting_number),
						sort: $scope.lines.length
					};
                    crmTotal.line.update(line);

					var formatted_data = angular.toJson(line);
					zhttp.crm.order.line.save(formatted_data).then(function(response){
						if(response.data && response.data != "false"){
							line.id = response.data;
							$scope.lines.push(line);
                            updateOrder();
						}
					});
				}
			});
		}

		function addSubTotal(){
			var subTotal = {
				id_order: $routeParams.id,
				type: "subTotal",
				sort: $scope.lines.length
			};

			var formatted_data = angular.toJson(subTotal);
			zhttp.crm.order.line.save(formatted_data).then(function(response){
				if(response.data && response.data != "false"){
					subTotal.id = response.data;
					$scope.lines.push(subTotal);
                    updateOrder();
				}
			});
		}

        function addComment(comment){
            if(comment.designation_desc !== ""){
                var comment = {
                    id_order: $routeParams.id,
                    type: "comment",
                    designation_desc: comment.designation_desc,
                    sort: $scope.lines.length
                };

                var formatted_data = angular.toJson(comment);
                zhttp.crm.order.line.save(formatted_data).then(function(response){
                    if(response.data && response.data != "false"){
                        comment.id = response.data;
                        $scope.lines.push(comment);
                    }
                });
            }
        }

        function editComment(comment){
            var formatted_data = angular.toJson(comment);
            zhttp.crm.order.line.save(formatted_data);
        }

        function editLine(){
            updateOrder();
        }

        function updateLine(line){
            $rootScope.$broadcast("comZeappsCrm_orderEditTrigger",
                {
                    line : line
                }
            );
        }

		function deleteLine(line){
			if($scope.lines.indexOf(line) > -1){
				zhttp.crm.order.line.del(line.id).then(function(response){
					if(response.data && response.data != "false"){
						$scope.lines.splice($scope.lines.indexOf(line), 1);

						for(var i = 0; i < $scope.line_details.length; i++){
                            if($scope.line_details[i].id_line === line.id){
                                $scope.line_details.splice(i, 1);
                                i--;
                            }
						}

						$rootScope.$broadcast("comZeappsCrm_orderDeleteTrigger",
							{
								id_line : line.id
							}
						);

                        updateOrder();
					}
				});
			}
		}

		function subtotalHT(index){
			return crmTotal.sub.HT($scope.lines, index);
		}

		function subtotalTTC(index){
			return crmTotal.sub.TTC($scope.lines, index);
		}

		function updateOrder(){
			if($scope.order) {
                $scope.order.global_discount = $scope.order.global_discount || 0;

				angular.forEach($scope.lines, function(line){
                    crmTotal.line.update(line);
                    if(line.id){
                        updateLine(line);
                    }
                    var formatted_data = angular.toJson(line);
                    zhttp.crm.order.line.save(formatted_data)
				});

				angular.forEach($scope.line_details, function(line){
                    crmTotal.line.update(line);
                    if(line.id){
                        updateLine(line);
                    }
                    var formatted_data = angular.toJson(line);
                    zhttp.crm.order.line_detail.save(formatted_data)
				});

                crmTotal.init($scope.order, $scope.lines, $scope.line_details);
                $scope.tvas = crmTotal.get.tvas;
                var totals = crmTotal.get.totals;
				$scope.order.total_prediscount_ht = totals.total_prediscount_ht;
				$scope.order.total_prediscount_ttc = totals.total_prediscount_ttc;
				$scope.order.total_discount = totals.total_discount;
				$scope.order.total_ht = totals.total_ht;
				$scope.order.total_tva = totals.total_tva;
				$scope.order.total_ttc = totals.total_ttc;

                var data = $scope.order;

                var y = data.date_creation.getFullYear();
                var M = data.date_creation.getMonth();
                var d = data.date_creation.getDate();

                data.date_creation = new Date(Date.UTC(y, M, d));

                var y = data.date_limit.getFullYear();
                var M = data.date_limit.getMonth();
                var d = data.date_limit.getDate();

                data.date_limit = new Date(Date.UTC(y, M, d));

                var formatted_data = angular.toJson(data);
                zhttp.crm.order.save(formatted_data).then(function(response){
                    if(response.data && response.data != "false"){
                        toasts('success', "Les informations du devis ont bien été mises a jour");
                    }
                    else{
                        toasts('danger', "Il y a eu une erreur lors de la mise a jour des informations du devis");
                    }
                });
			}
		}

		function addActivity(activity){
            var y = activity.deadline.getFullYear();
            var M = activity.deadline.getMonth();
            var d = activity.deadline.getDate();

            activity.deadline = new Date(Date.UTC(y, M, d));
            activity.id_order = $scope.order.id;
            var formatted_data = angular.toJson(activity);

            zhttp.crm.order.activity.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    response.data.deadline = new Date(response.data.deadline);
                    $scope.activities.push(response.data);
                }
            });
		}

		function editActivity(activity){
            var y = activity.deadline.getFullYear();
            var M = activity.deadline.getMonth();
            var d = activity.deadline.getDate();

            activity.deadline = new Date(Date.UTC(y, M, d));
            var formatted_data = angular.toJson(activity);

            zhttp.crm.order.activity.save(formatted_data);
		}

		function deleteActivity(activity){
            zhttp.crm.order.activity.del(activity.id).then(function (response) {
                if (response.status == 200) {
                    $scope.activities.splice($scope.activities.indexOf(activity), 1);
                }
            });
		}

		function addDocument(document) {
            Upload.upload({
                url: zhttp.crm.order.document.upload() + $scope.order.id,
                data: document
            }).then(
                function(response){
                    $scope.progress = false;
                    if(response.data && response.data != "false"){
                        response.data.date = new Date(response.data.date);
                        response.data.id_user = $rootScope.user.id;
                        response.data.name_user = $rootScope.user.firstname[0] + '. ' + $rootScope.user.lastname;
                        $scope.documents.push(response.data);
                        toasts('success', "Les documents ont bien été mis en ligne");
                    }
                    else{
                        toasts('danger', "Il y a eu une erreur lors de la mise en ligne des documents");
                    }
                }
            );
		}

		function editDocument(document) {
            Upload.upload({
                url: zhttp.crm.order.document.upload() + $scope.order.id,
                data: document
            }).then(
                function(response){
                    $scope.progress = false;
                    if(response.data && response.data != "false"){
                        response.data.date = new Date(response.data.date);
                        toasts('success', "Les documents ont bien été mis à jour");
                    }
                    else{
                        toasts('danger', "Il y a eu une erreur lors de la mise à jour des documents");
                    }
                }
            );
		}

		function deleteDocument(document){
            zhttp.crm.order.document.del(document.id).then(function(response){
                if(response.data && response.data != "false"){
                    $scope.documents.splice($scope.documents.indexOf(document), 1);
                }
            });
		}

		function print(){
			zhttp.crm.order.pdf.make($scope.order.id).then(function(response){
				if(response.data && response.data != "false"){
					window.document.location.href = zhttp.crm.order.pdf.get() + angular.fromJson(response.data);
				}
			});
		}

		function initNavigation() {

			// calcul le nombre de résultat
			if($rootScope.orders) {
				$scope.nb_orders = $rootScope.orders.ids.length;


				// calcul la position du résultat actuel
				$scope.order_order = 0;
				$scope.order_first = 0;
				$scope.order_previous = 0;
				$scope.order_next = 0;
				$scope.order_last = 0;

				for (var i = 0; i < $rootScope.orders.ids.length; i++) {
					if ($rootScope.orders.ids[i] == $routeParams.id) {
						$scope.order_order = i + 1;
						if (i > 0) {
							$scope.order_previous = $rootScope.orders.ids[i - 1];
						}

						if ((i + 1) < $rootScope.orders.ids.length) {
							$scope.order_next = $rootScope.orders.ids[i + 1];
						}
					}
				}

				// recherche la première facture de la liste
				if ($rootScope.orders.ids[0] != undefined) {
					if ($rootScope.orders.ids[0] != $routeParams.id) {
						$scope.order_first = $rootScope.orders.ids[0];
					}
				}
				else
					$scope.order_first = 0;

				// recherche la dernière facture de la liste
				if ($rootScope.orders.ids[$rootScope.orders.ids.length - 1] != undefined) {
					if ($rootScope.orders.ids[$rootScope.orders.ids.length - 1] != $routeParams.id) {
						$scope.order_last = $rootScope.orders.ids[$rootScope.orders.ids.length - 1];
					}
				}
				else
					$scope.order_last = 0;
			}
			else{
				$scope.nb_orders = 0;
			}
		}

		function sortableStop(event, ui) {

			var data = {};
			var pushedLine = false;
			data.id = $(ui.item[0]).attr("data-id");

			for(var i=0; i<$scope.lines.length; i++){
				if($scope.lines[i].id == data.id && !pushedLine){
					data.oldSort = $scope.lines[i].sort;
					data.sort = i;
					$scope.lines[i].sort = data.sort;
					pushedLine = true;
				}
				else if(pushedLine){
					$scope.lines[i].sort++;
				}
			}

			var formatted_data = angular.toJson(data);
			zhttp.crm.order.line.position(formatted_data);
		}

	}]);