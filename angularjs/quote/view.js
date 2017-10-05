app.controller("ComZeappsCrmQuoteViewCtrl", ["$scope", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "Upload", "crmTotal", "zeHooks", "toasts", "menu",
	function ($scope, $routeParams, $location, $rootScope, zhttp, zeapps_modal, Upload, crmTotal, zeHooks, toasts, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_quote");

		$scope.$on("comZeappsCrm_triggerQuoteHook", broadcast);
		$scope.hooks = zeHooks.get("comZeappsCrm_QuoteHook");

		$scope.progress = 0;
		$scope.activities = [];
		$scope.documents = [];

		$scope.quoteLineTplUrl = "/com_zeapps_crm/quotes/form_line";
        $scope.quoteCommentTplUrl = "/com_zeapps_crm/crm_commons/form_comment";
        $scope.quoteActivityTplUrl = "/com_zeapps_crm/crm_commons/form_activity";
        $scope.quoteDocumentTplUrl = "/com_zeapps_crm/crm_commons/form_document";
		$scope.templateEdit = "/com_zeapps_crm/quotes/form_modal";

		$scope.lines = [];

        $scope.sortable = {
            connectWith: ".sortableContainer",
            disabled: false,
            axis: "y",
            stop: sortableStop
        };

		$scope.setTab = setTab;

		$scope.back = back;
		$scope.first_quote = first_quote;
		$scope.previous_quote = previous_quote;
		$scope.next_quote = next_quote;
		$scope.last_quote = last_quote;

		$scope.updateStatus = updateStatus;
		$scope.updateQuote = updateQuote;
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
		if($rootScope.quotes === undefined || $rootScope.quotes.ids === undefined) {
            $rootScope.quotes = {};
            $rootScope.quotes.ids = [];
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

                    $scope.credits = response.data.credits;

                    $scope.activities = response.data.activities || [];
					angular.forEach($scope.activities, function(activity){
						activity.deadline = new Date(activity.deadline);
					});

					$scope.documents = response.data.documents || [];
                    angular.forEach($scope.documents, function(document){
                        document.date = new Date(document.date);
                    });

                    $scope.quote.global_discount = parseFloat($scope.quote.global_discount);
                    $scope.quote.probability = parseFloat($scope.quote.probability);
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

                    var line_details = response.data.line_details || [];
                    angular.forEach(line_details, function(line_detail){
                        line_detail.price_unit = parseFloat(line_detail.price_unit);
                        line_detail.qty = parseFloat(line_detail.qty);
                        line_detail.discount = parseFloat(line_detail.discount);
                    });
                    $scope.line_details = line_details;

                    crmTotal.init($scope.quote, $scope.lines, $scope.line_details);
                    $scope.tvas = crmTotal.get.tvas;
                    var totals = crmTotal.get.totals;
                    $scope.quote.total_prediscount_ht = totals.total_prediscount_ht;
                    $scope.quote.total_prediscount_ttc = totals.total_prediscount_ttc;
                    $scope.quote.total_discount = totals.total_discount;
                    $scope.quote.total_ht = totals.total_ht;
                    $scope.quote.total_tva = totals.total_tva;
                    $scope.quote.total_ttc = totals.total_ttc;
				}
			});
		}

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
            if ($rootScope.quotes.src === undefined || $rootScope.quotes.src === "quotes") {
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

        function updateStatus(){
			var data = {};

			data.id = $scope.quote.id;
			data.status = $scope.quote.status;

			var formatted_data = angular.toJson(data);

			zhttp.crm.quote.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    toasts('success',"Le status du devis a bien été mis à jour.");
                }
                else{
                    toasts('danger',"Il y a eu une erreur lors de la mise a jour du status du devis");
                }
            });
		}

		function transform(){
			zeapps_modal.loadModule("com_zeapps_crm", "transform_document", {}, function(objReturn) {
				if (objReturn) {
					var formatted_data = angular.toJson(objReturn);
					zhttp.crm.quote.transform($scope.quote.id, formatted_data).then(function(response){
						if(response.data && response.data != "false"){
                            zeapps_modal.loadModule("com_zeapps_crm", "transformed_document", response.data);
						}
					});
				}
			});
		}

        function keyEventaddFromCode($event){
            if($event.which === 13){
                addFromCode();
            }
        }

		function addFromCode(){
			if($scope.codeProduct !== "") {
                var code = $scope.codeProduct;
                zhttp.crm.product.get_code(code).then(function (response) {
                    if (response.data && response.data != "false") {
                        var line = {
                            id_quote: $routeParams.id,
                            type: "product",
                            id_product: response.data.id,
                            ref: response.data.ref,
                            designation_title: response.data.name,
                            designation_desc: response.data.description,
                            qty: 1,
                            discount: 0.00,
                            price_unit: parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc),
                            id_taxe: parseFloat(response.data.id_taxe),
                            value_taxe: parseFloat(response.data.value_taxe),
                            accounting_number: parseFloat(response.data.accounting_number),
                            sort: $scope.lines.length
                        };
                        crmTotal.line.update(line);

                        $scope.codeProduct = "";

                        var formatted_data = angular.toJson(line);
                        zhttp.crm.quote.line.save(formatted_data).then(function (response) {
                            if (response.data && response.data != "false") {
                                line.id = response.data;
                                $scope.lines.push(line);
                                updateQuote();
                            }
                        });
                    }
                    else {
                        toasts("danger", "Aucun produit avec le code " + code + " trouvé dans la base de donnée.");
                    }
                });
            }
		}

		function addLine(){
			// charge la modal de la liste de produit
			zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function(objReturn) {
				if (objReturn) {
					var line = {
						id_quote: $routeParams.id,
						type: "product",
						id_product: objReturn.id,
						ref: objReturn.ref,
						designation_title: objReturn.name,
						designation_desc: objReturn.description,
						qty: 1,
						discount: 0.00,
						price_unit: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
						id_taxe: parseFloat(objReturn.id_taxe),
						value_taxe: parseFloat(objReturn.value_taxe),
                        accounting_number: parseFloat(objReturn.accounting_number),
						sort: $scope.lines.length
					};
                    crmTotal.line.update(line);

					var formatted_data = angular.toJson(line);
					zhttp.crm.quote.line.save(formatted_data).then(function(response){
						if(response.data && response.data != "false"){
							line.id = response.data;
							$scope.lines.push(line);
                            updateQuote();
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
                    updateQuote();
				}
			});
		}

        function addComment(comment){
            if(comment.designation_desc !== ""){
                var comment = {
                    id_quote: $routeParams.id,
                    type: "comment",
                    designation_desc: comment.designation_desc,
                    sort: $scope.lines.length
                };

                var formatted_data = angular.toJson(comment);
                zhttp.crm.quote.line.save(formatted_data).then(function(response){
                    if(response.data && response.data != "false"){
                        comment.id = response.data;
                        $scope.lines.push(comment);
                    }
                });
            }
        }

        function editComment(comment){
            var formatted_data = angular.toJson(comment);
            zhttp.crm.quote.line.save(formatted_data);
        }

		function editLine(){
			updateQuote();
		}

		function updateLine(line){
            $rootScope.$broadcast("comZeappsCrm_quoteEditTrigger",
                {
                    line : line
                }
            );
		}

		function deleteLine(line){
			if($scope.lines.indexOf(line) > -1){
				zhttp.crm.quote.line.del(line.id).then(function(response){
					if(response.data && response.data != "false"){
						$scope.lines.splice($scope.lines.indexOf(line), 1);

                        for(var i = 0; i < $scope.line_details.length; i++){
                            if($scope.line_details[i].id_line === line.id){
                                $scope.line_details.splice(i, 1);
                                i--;
                            }
                        }

						$rootScope.$broadcast("comZeappsCrm_quoteDeleteTrigger",
							{
								id_line : line.id
							}
						);

                        updateQuote();
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

		function updateQuote(){
			if($scope.quote) {
				$scope.quote.global_discount = $scope.quote.global_discount || 0;

				angular.forEach($scope.lines, function(line){
                    crmTotal.line.update(line);
                    if(line.id){
                        updateLine(line);
                    }
                    var formatted_data = angular.toJson(line);
                    zhttp.crm.quote.line.save(formatted_data)
				});

                angular.forEach($scope.line_details, function(line){
                    crmTotal.line.update(line);
                    if(line.id){
                        updateLine(line);
                    }
                    var formatted_data = angular.toJson(line);
                    zhttp.crm.quote.line_detail.save(formatted_data)
                });

                crmTotal.init($scope.quote, $scope.lines, $scope.line_details);
                $scope.tvas = crmTotal.get.tvas;
                var totals = crmTotal.get.totals;
				$scope.quote.total_prediscount_ht = totals.total_prediscount_ht;
				$scope.quote.total_prediscount_ttc = totals.total_prediscount_ttc;
				$scope.quote.total_discount = totals.total_discount;
				$scope.quote.total_ht = totals.total_ht;
				$scope.quote.total_tva = totals.total_tva;
				$scope.quote.total_ttc = totals.total_ttc;

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
                        toasts('success',"Les informations du devis ont bien été mises a jour");
                    }
                    else{
                        toasts('danger',"Il y a eu une erreur lors de la mise a jour des informations du devis");
                    }
                });
			}
		}

		function addActivity(activity){
            var y = activity.deadline.getFullYear();
            var M = activity.deadline.getMonth();
            var d = activity.deadline.getDate();

            activity.deadline = new Date(Date.UTC(y, M, d));
            activity.id_quote = $scope.quote.id;
            var formatted_data = angular.toJson(activity);

            zhttp.crm.quote.activity.save(formatted_data).then(function(response){
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

            zhttp.crm.quote.activity.save(formatted_data);
		}

		function deleteActivity(activity){
            zhttp.crm.quote.activity.del(activity.id).then(function (response) {
                if (response.status == 200) {
                    $scope.activities.splice($scope.activities.indexOf(activity), 1);
                }
            });
		}

		function addDocument(document) {
            Upload.upload({
                url: zhttp.crm.quote.document.upload() + $scope.quote.id,
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
                url: zhttp.crm.quote.document.upload() + $scope.quote.id,
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
				$scope.nb_quotes = $rootScope.quotes.ids.length;


				// calcul la position du résultat actuel
				$scope.quote_order = 0;
				$scope.quote_first = 0;
				$scope.quote_previous = 0;
				$scope.quote_next = 0;
				$scope.quote_last = 0;

				for (var i = 0; i < $rootScope.quotes.ids.length; i++) {
					if ($rootScope.quotes.ids[i] == $routeParams.id) {
						$scope.quote_order = i + 1;
						if (i > 0) {
							$scope.quote_previous = $rootScope.quotes.ids[i - 1];
						}

						if ((i + 1) < $rootScope.quotes.ids.length) {
							$scope.quote_next = $rootScope.quotes.ids[i + 1];
						}
					}
				}

				// recherche la première facture de la liste
				if ($rootScope.quotes.ids[0] != undefined) {
					if ($rootScope.quotes.ids[0] != $routeParams.id) {
						$scope.quote_first = $rootScope.quotes.ids[0];
					}
				}
				else
					$scope.quote_first = 0;

				// recherche la dernière facture de la liste
				if ($rootScope.quotes.ids[$rootScope.quotes.ids.length - 1] != undefined) {
					if ($rootScope.quotes.ids[$rootScope.quotes.ids.length - 1] != $routeParams.id) {
						$scope.quote_last = $rootScope.quotes.ids[$rootScope.quotes.ids.length - 1];
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