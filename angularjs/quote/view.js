app.controller("ComZeappsCrmQuoteViewCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "$uibModal", "zeapps_modal", "Upload", "crmTotal", "zeHooks",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal, Upload, crmTotal, zeHooks) {

		$scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_quote");

		$scope.$on("comZeappsCrm_triggerOrderHook", broadcast);
		$scope.hooks = zeHooks.get("comZeappsCrm_OrderHook");

		$scope.progress = 0;
		$scope.activities = [];
		$scope.documents = [];

		$scope.edit = false;
		$scope.showCommentInput = false;
		$scope.showActivityInput = false;
		$scope.comment = "";

		$scope.quoteLineTplUrl = "/com_zeapps_crm/quotes/form_line";
		$scope.templateEdit = "/com_zeapps_crm/quotes/form_modal";

		$scope.lines = [];

		$scope.setTab = setTab;

		$scope.back = back;
		$scope.first_quote = first_quote;
		$scope.previous_quote = previous_quote;
		$scope.next_quote = next_quote;
		$scope.last_quote = last_quote;

		$scope.edit = edit;
		$scope.transform = transform;

		$scope.addFromCode = addFromCode;
        $scope.keyEventaddFromCode = keyEventaddFromCode;
		$scope.addLine = addLine;
        $scope.editLine = editLine;
		$scope.addSubTotal = addSubTotal;
		$scope.addComment = addComment;

		$scope.updateSums = updateSums;
		$scope.updateTotals = updateTotals;

		$scope.submitLine = submitLine;
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
		if($rootScope.quotes == undefined || $rootScope.quotes[0] == undefined) {
			zhttp.crm.quote.get_all("0", "quotes", 0, 0, true).then(function (response) {
				if (response.status == 200) {
					$rootScope.quotes = response.data.quotes;
					initNavigation();
				}
			});
		}
		else{
			initNavigation();
		}

		/******* gestion de la tabs *********/
		$scope.navigationState = "body";
		if ($rootScope.comZeappsCrmLastShowTabQuote) {
			$scope.navigationState = $rootScope.comZeappsCrmLastShowTabQuote ;
		}

		if($routeParams.id && $routeParams.id > 0){
			zhttp.crm.quote.get($routeParams.id).then(function(response){
				if(response.data && response.data != "false"){
					$scope.quote = response.data.quote;
					$scope.sortable.disabled = !!parseInt($scope.quote.finalized);
					$scope.company = response.data.company;
					$scope.contact = response.data.contact;
					$scope.activities = response.data.activities || [];
					angular.forEach($scope.activities, function(activity){
						activity.date = new Date(activity.date);
					});

					$scope.documents = response.data.documents || [];
                    angular.forEach($scope.documents, function(document){
                        document.date = new Date(document.date);
                    });

                    $scope.quote.global_discount = parseFloat($scope.quote.global_discount);
					$scope.quote.date_creation = new Date($scope.quote.date_creation);
					$scope.quote.date_limit = new Date($scope.quote.date_limit);

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
				}
			});
		}

		$scope.sortable = {
			connectWith: ".sortableContainer",
			disabled: false,
			axis: "y",
			stop: sortableStop
		};

		//////////////////// FUNCTIONS ////////////////////

		function broadcast(){
			$rootScope.$broadcast("comZeappsCrm_dataQuoteHook",
				{
					quote: $scope.quote
				}
			);
		}

		function setTab(tab){
            $rootScope.comZeappsCrmLastShowTabQuote = tab;
            $scope.navigationState = tab;
		}

		function back(){
            if ($rootScope.quotes.src === undefined) {
                $location.path("/ng/com_zeapps_crm/quote/");
            }
            else if ($rootScope.quotes.src === 'company') {
                $location.path("/ng/com_zeapps_contact/companies/" + $rootScope.quotes.src_id);
            }
            else if ($rootScope.quotes.src === 'contact') {
                $location.path("/ng/com_zeapps_contact/contacts/" + $rootScope.quotes.src_id);
            }
		}

		function first_quote() {
			if ($scope.quote_first != 0) {
				$location.path("/ng/com_zeapps_crm/quote/" + $scope.quote_first);
			}
		}

		function previous_quote() {
			if ($scope.quote_previous != 0) {
				$location.path("/ng/com_zeapps_crm/quote/" + $scope.quote_previous);
			}
		}

		function next_quote() {
			if ($scope.quote_next) {
				$location.path("/ng/com_zeapps_crm/quote/" + $scope.quote_next);
			}
		}

		function last_quote() {
			if ($scope.quote_last) {
				$location.path("/ng/com_zeapps_crm/quote/" + $scope.quote_last);
			}
		}

		function edit(){
			var data = $scope.quote;

			var y = data.date_creation.getFullYear();
			var M = data.date_creation.getMonth();
			var d = data.date_creation.getDate();

			data.date_creation = new Date(Date.UTC(y, M, d));

			var y = data.date_limit.getFullYear();
			var M = data.date_limit.getMonth();
			var d = data.date_limit.getDate();

			data.date_limit = new Date(Date.UTC(y, M, d));

			var formatted_data = angular.toJson(data);

			zhttp.crm.quote.save(formatted_data).then(function(response){
				if(response.data && response.data != "false"){
                    $rootScope.toasts.push({success:"Les informations du devis ont bien été mises a jour"});
				}
				else{
                    $rootScope.toasts.push({danger:"Il y a eu une erreur lors de la mise a jour des informations du devis"});
				}
			});
		}

		function transform(){
			zeapps_modal.loadModule("com_zeapps_crm", "transform_quote", {}, function(objReturn) {
				if (objReturn) {
					var formatted_data = angular.toJson(objReturn);
					zhttp.crm.quote.transform($scope.quote.id, formatted_data).then(function(response){
						if(response.data && response.data != "false"){
							if(objReturn.order){
								$location.url("/ng/com_zeapps_crm/order/" + response.data.order);
							}
						}
					});
				}
			});
		}

		function addFromCode(){
			if($scope.codeProduct !== "") {
                var code = $scope.codeProduct;
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
                            taxe: "" + parseFloat(response.data.value_taxe),
                            sort: $scope.lines.length,
                            total_ht: parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc),
                            total_ttc: ((parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc)) * (1 + (parseFloat(response.data.value_taxe) / 100)))
                        };

                        $scope.codeProduct = "";

                        var formatted_data = angular.toJson(line);
                        zhttp.crm.order.line.save(formatted_data).then(function (response) {
                            if (response.data && response.data != "false") {
                                line.id = response.data;
                                $scope.lines.push(line);
                                updateTotals();
                            }
                        });
                    }
                    else {
                        $rootScope.toasts.push({"danger": "Aucun produit avec le code " + code + " trouvé dans la base de donnée."});
                    }
                });
            }
		}

        function keyEventaddFromCode($event){
            if($event.which === 13){
                addFromCode();
            }
        }

		function addLine(){
			// charge la modal de la liste de produit
			zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function(objReturn) {
				//console.log(objReturn);
				if (objReturn) {
					var line = {
						id_quote: $routeParams.id,
						type: "product",
						id_product: objReturn.id,
						ref: objReturn.ref,
						designation_title: objReturn.name,
						designation_desc: objReturn.description,
						qty: "1",
						discount: 0.00,
						price_unit: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
						taxe: parseFloat(objReturn.value_taxe),
						sort: $scope.lines.length,
						total_ht: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
						total_ttc: ((parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc)) * (1 + (parseFloat(objReturn.value_taxe) / 100)))
					};

					var formatted_data = angular.toJson(line);
					zhttp.crm.quote.line.save(formatted_data).then(function(response){
						if(response.data && response.data != "false"){
							line.id = response.data;
							$scope.lines.push(line);
							updateTotals();
						}
					});
				}
			});
		}

		function addSubTotal(){
			var subTotal = {
				id_quote: $routeParams.id,
				type: "subTotal",
				sort: $scope.lines.length
			};

			var formatted_data = angular.toJson(subTotal);
			zhttp.crm.quote.line.save(formatted_data).then(function(response){
				if(response.data && response.data != "false"){
					subTotal.id = response.data;
					$scope.lines.push(subTotal);
					updateTotals();
				}
			});
		}

		function addComment(){
			if($scope.comment != ""){
				var comment = {
					id_quote: $routeParams.id,
					type: "comment",
					designation_desc: "",
					sort: $scope.lines.length
				};
				comment.designation_desc = $scope.comment;

				var formatted_data = angular.toJson(comment);
				zhttp.crm.quote.line.save(formatted_data).then(function(response){
					if(response.data && response.data != "false"){
						comment.id = response.data;
						$scope.lines.push(comment);
						$scope.comment = "";
						$scope.showCommentInput = false;
					}
				});
			}
		}

		function editLine(line){
			if(line.type === "product")
				updateSums(line);
			else{
				$rootScope.$broadcast("comZeappsCrm_quoteEditTrigger",
					{
						line : line
					}
				);
			}
		}

		function updateSums(line){
			line.total_ht = parseFloat(line.price_unit) * parseFloat(line.qty) * ( 1 - (parseFloat(line.discount) / 100) ) * ( 1 - (parseFloat($scope.quote.global_discount) / 100) );
			line.total_ttc = parseFloat(line.price_unit) * parseFloat(line.qty) * ( 1 - (parseFloat(line.discount) / 100) ) * ( 1 - (parseFloat($scope.quote.global_discount) / 100) ) * ( 1 + (parseFloat(line.taxe) / 100) );
		}

		function submitLine(line){
            updateSums(line);
			var formatted_data = angular.toJson(line);
			zhttp.crm.quote.line.save(formatted_data).then(function(response){
				if(response.data && response.data != "false"){
					line.edit = false;
					updateTotals();
				}
			});
		}

		function deleteLine(line){
			if($scope.lines.indexOf(line) > -1){
				zhttp.crm.quote.line.del(line.id).then(function(response){
					if(response.data && response.data != "false"){
						$scope.lines.splice($scope.lines.indexOf(line), 1);

						$rootScope.$broadcast("comZeappsCrm_quoteDeleteTrigger",
							{
								id_line : line.id
							}
						);

						updateTotals();
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

		function updateTotals(){
			if($scope.quote) {
				$scope.quote.total_prediscount_ht = crmTotal.preDiscount.HT($scope.lines);
				$scope.quote.total_prediscount_ttc = crmTotal.preDiscount.TTC($scope.lines);
				$scope.quote.total_discount = crmTotal.discount($scope.lines, $scope.quote.global_discount);
				$scope.quote.total_ht = crmTotal.total.HT($scope.lines);
				$scope.quote.total_tva = crmTotal.total.TVA($scope.lines);
				$scope.quote.total_ttc = crmTotal.total.TTC($scope.lines);

				var data = $scope.quote;

				var formatted_data = angular.toJson(data);

				zhttp.crm.quote.save(formatted_data);
			}
		}

		function addActivity(){
            var options = {};
            zeapps_modal.loadModule("com_zeapps_crm", "form_activity", options, function(objReturn) {
                if (objReturn) {
                    objReturn.id_quote = $scope.quote.id;
                    var formatted_data = angular.toJson(objReturn);

                    zhttp.crm.quote.activity.save(formatted_data).then(function(response){
                        if(response.data && response.data != "false"){
                            response.data.date = new Date(response.data.date);
                            $scope.activities.push(response.data);
                        }
                    });
                } else {
                }
            });
		}

		function editActivity(activity){
			delete activity.deleted_at;
            var options = {
                activity: angular.fromJson(angular.toJson(activity))
            };
            zeapps_modal.loadModule("com_zeapps_crm", "form_activity", options, function(objReturn) {
                if (objReturn) {
                    var formatted_data = angular.toJson(objReturn);

                    zhttp.crm.quote.activity.save(formatted_data).then(function(response){
                        if(response.data && response.data != "false"){
                            response.data.date = new Date(response.data.date);
                            $scope.activities[$scope.activities.indexOf(activity)] = response.data;
                        }
                    });
                } else {
                }
            });
		}

		function deleteActivity(activity){
            zhttp.crm.quote.activity.del(activity.id).then(function (response) {
                if (response.status == 200) {
                    $scope.activities.splice($scope.activities.indexOf(activity), 1);
                }
            });
		}

		function addDocument() {
            var options = {};
            zeapps_modal.loadModule("com_zeapps_crm", "form_document", options, function(objReturn) {
                if (objReturn) {
                    Upload.upload({
                        url: zhttp.crm.quote.document.upload() + $scope.quote.id,
                        data: objReturn
                    }).then(
                        function(response){
                            $scope.progress = false;
                            if(response.data && response.data != "false"){
                                response.data.date = new Date(response.data.date);
                                response.data.id_user = $rootScope.user.id;
                                response.data.name_user = $rootScope.user.firstname[0] + '. ' + $rootScope.user.lastname;
                                $scope.documents.push(response.data);
                                $rootScope.toasts.push({success: "Les documents ont bien été mis en ligne"});
                            }
                            else{
                                $rootScope.toasts.push({danger: "Il y a eu une erreur lors de la mise en ligne des documents"});
                            }
                        }
                    );
                } else {
                }
            });
		}

		function editDocument(document) {
            delete document.deleted_at;
            var options = {
                document: angular.fromJson(angular.toJson(document))
            };
            zeapps_modal.loadModule("com_zeapps_crm", "form_document", options, function(objReturn) {
                if (objReturn) {
                    Upload.upload({
                        url: zhttp.crm.quote.document.upload() + $scope.quote.id,
                        data: objReturn
                    }).then(
                        function(response){
                            $scope.progress = false;
                            if(response.data && response.data != "false"){
                                response.data.date = new Date(response.data.date);
                                $scope.documents[$scope.documents.indexOf(document)] = response.data;
                                $rootScope.toasts.push({success: "Les documents ont bien été mis à jour"});
                            }
                            else{
                                $rootScope.toasts.push({danger: "Il y a eu une erreur lors de la mise à jour des documents"});
                            }
                        }
                    );
                } else {
                }
            });
		}

		function deleteDocument(document){
            zhttp.crm.quote.document.del(document.id).then(function(response){
                if(response.data && response.data != "false"){
                    $scope.documents.splice($scope.documents.indexOf(document), 1);
                }
            });
		}

		function print(){
			zhttp.crm.quote.pdf.make($scope.quote.id).then(function(response){
				if(response.data && response.data != "false"){
					window.document.location.href = zhttp.crm.quote.pdf.get() + angular.fromJson(response.data);
				}
			});
		}

		function initNavigation() {

			// calcul le nombre de résultat
			if($rootScope.quotes) {
				$scope.nb_quotes = $rootScope.quotes.length;


				// calcul la position du résultat actuel
				$scope.quote_order = 0;
				$scope.quote_first = 0;
				$scope.quote_previous = 0;
				$scope.quote_next = 0;
				$scope.quote_last = 0;

				for (var i = 0; i < $rootScope.quotes.length; i++) {
					if ($rootScope.quotes[i].id == $routeParams.id) {
						$scope.quote_order = i + 1;
						if (i > 0) {
							$scope.quote_previous = $rootScope.quotes[i - 1].id;
						}

						if ((i + 1) < $rootScope.quotes.length) {
							$scope.quote_next = $rootScope.quotes[i + 1].id;
						}
					}
				}

				// recherche la première facture de la liste
				if ($rootScope.quotes[0] != undefined) {
					if ($rootScope.quotes[0].id != $routeParams.id) {
						$scope.quote_first = $rootScope.quotes[0].id;
					}
				}
				else
					$scope.quote_first = 0;

				// recherche la dernière facture de la liste
				if ($rootScope.quotes[$rootScope.quotes.length - 1] != undefined) {
					if ($rootScope.quotes[$rootScope.quotes.length - 1].id != $routeParams.id) {
						$scope.quote_last = $rootScope.quotes[$rootScope.quotes.length - 1].id;
					}
				}
				else
					$scope.quote_last = 0;
			}
			else{
				$scope.nb_quotes = 0;
			}
		}

		function sortableStop( event, ui ) {

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
			zhttp.crm.quote.line.position(formatted_data);
		}

	}]);