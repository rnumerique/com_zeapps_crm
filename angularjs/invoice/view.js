app.controller("ComZeappsCrmInvoiceViewCtrl", ["$scope", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "Upload", "crmTotal", "zeHooks", "toasts", "menu",
	function ($scope, $routeParams, $location, $rootScope, zhttp, zeapps_modal, Upload, crmTotal, zeHooks, toasts, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_invoice");

		$scope.$on("comZeappsCrm_triggerInvoiceHook", broadcast);
		$scope.hooks = zeHooks.get("comZeappsCrm_InvoiceHook");

		$scope.progress = 0;
		$scope.activities = [];
		$scope.documents = [];

		$scope.invoiceLineTplUrl = "/com_zeapps_crm/invoices/form_line";
        $scope.invoiceCommentTplUrl = "/com_zeapps_crm/crm_commons/form_comment";
        $scope.invoiceActivityTplUrl = "/com_zeapps_crm/crm_commons/form_activity";
        $scope.invoiceDocumentTplUrl = "/com_zeapps_crm/crm_commons/form_document";
		$scope.templateEdit = "/com_zeapps_crm/invoices/form_modal";

		$scope.lines = [];

        $scope.sortable = {
            connectWith: ".sortableContainer",
            disabled: false,
            axis: "y",
            stop: sortableStop
        };

		$scope.setTab = setTab;

		$scope.goToPayment = goToPayment;
		$scope.back = back;
		$scope.first_invoice = first_invoice;
		$scope.previous_invoice = previous_invoice;
		$scope.next_invoice = next_invoice;
		$scope.last_invoice = last_invoice;

		$scope.updateInvoice = updateInvoice;
		$scope.transform = transform;
		$scope.finalize = finalize;

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
		if($rootScope.invoices === undefined || $rootScope.invoices.ids === undefined) {
            $rootScope.invoices = {};
            $rootScope.invoices.ids = [];
		}
		else{
			initNavigation();
		}

		/******* gestion de la tabs *********/
		$scope.navigationState = "body";
		if ($rootScope.comZeappsCrmLastShowTabInvoice) {
			$scope.navigationState = $rootScope.comZeappsCrmLastShowTabInvoice ;
		}

		if($routeParams.id && $routeParams.id > 0){
			zhttp.crm.invoice.get($routeParams.id).then(function(response){
				if(response.data && response.data != "false"){
					$scope.invoice = response.data.invoice;

					if($scope.invoice.finalized === '1'){
						$scope.sortable.disabled = true;
					}

                    $scope.credits = response.data.credits;

                    $scope.activities = response.data.activities || [];
					angular.forEach($scope.activities, function(activity){
						activity.deadline = new Date(activity.deadline);
					});

					$scope.documents = response.data.documents || [];
                    angular.forEach($scope.documents, function(document){
                        document.date = new Date(document.date);
                    });

                    $scope.invoice.global_discount = parseFloat($scope.invoice.global_discount);
                    $scope.invoice.probability = parseFloat($scope.invoice.probability);
					$scope.invoice.date_creation = new Date($scope.invoice.date_creation);
					$scope.invoice.date_limit = new Date($scope.invoice.date_limit);

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

                    crmTotal.init($scope.invoice, $scope.lines, $scope.line_details);
                    $scope.tvas = crmTotal.get.tvas;
                    var totals = crmTotal.get.totals;
                    $scope.invoice.total_prediscount_ht = totals.total_prediscount_ht;
                    $scope.invoice.total_prediscount_ttc = totals.total_prediscount_ttc;
                    $scope.invoice.total_discount = totals.total_discount;
                    $scope.invoice.total_ht = totals.total_ht;
                    $scope.invoice.total_tva = totals.total_tva;
                    $scope.invoice.total_ttc = totals.total_ttc;
				}
			});
		}

		//////////////////// FUNCTIONS ////////////////////

		function broadcast(){
			$rootScope.$broadcast("comZeappsCrm_dataInvoiceHook",
				{
					invoice: $scope.invoice
				}
			);
		}

		function setTab(tab){
            $rootScope.comZeappsCrmLastShowTabInvoice = tab;
            $scope.navigationState = tab;
		}

		function goToPayment(){
		    $location.url("/ng/com_zeapps_crm/credit_balances/" + $scope.invoice.id);
        }

		function back(){
            if ($rootScope.invoices.src === undefined || $rootScope.invoices.src === "invoices") {
                $location.path("/ng/com_zeapps_crm/invoice/");
            }
            else if ($rootScope.invoices.src === 'company') {
                $location.path("/ng/com_zeapps_contact/companies/" + $rootScope.invoices.src_id);
            }
            else if ($rootScope.invoices.src === 'contact') {
                $location.path("/ng/com_zeapps_contact/contacts/" + $rootScope.invoices.src_id);
            }
		}

		function first_invoice() {
			if ($scope.invoice_first != 0) {
				$location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_first);
			}
		}

		function previous_invoice() {
			if ($scope.invoice_previous != 0) {
				$location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_previous);
			}
		}

		function next_invoice() {
			if ($scope.invoice_next) {
				$location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_next);
			}
		}

		function last_invoice() {
			if ($scope.invoice_last) {
				$location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_last);
			}
		}

		function transform(){
			zeapps_modal.loadModule("com_zeapps_crm", "transform_document", {}, function(objReturn) {
				if (objReturn) {
					var formatted_data = angular.toJson(objReturn);
					zhttp.crm.invoice.transform($scope.invoice.id, formatted_data).then(function(response){
						if(response.data && response.data != "false"){
                            zeapps_modal.loadModule("com_zeapps_crm", "transformed_document", response.data);
						}
					});
				}
			});
		}

		function finalize(){
		    if($scope.invoice.id_modality !== '0') {
                zhttp.crm.invoice.finalize($scope.invoice.id).then(function (response) {
                    if (response.data && response.data !== "false") {
                        if(response.data.error){
                            toasts('danger', response.data.error);
                        }
                        else {
                            $scope.invoice.numerotation = response.data.numerotation;
                            $scope.invoice.final_pdf = response.data.final_pdf;
                            $scope.invoice.finalized = '1';
                            $scope.sortable.disabled = true;
                        }
                    }
                });
            }
            else{
		        toasts('warning', "Vous devez renseigner un moyen de paiement pour pouvoir clôturer une facture");
            }
		}

        function keyEventaddFromCode($event){
            if($event.which === 13){
                addFromCode();
            }
        }

		function addFromCode(){
			if($scope.codeProduct !== "" && $scope.invoice.finalized === '0') {
                var code = $scope.codeProduct;
                zhttp.crm.product.get_code(code).then(function (response) {
                    if (response.data && response.data != "false") {
                        var line = {
                            id_invoice: $routeParams.id,
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
                        zhttp.crm.invoice.line.save(formatted_data).then(function (response) {
                            if (response.data && response.data != "false") {
                                line.id = response.data;
                                $scope.lines.push(line);
                                updateInvoice();
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
			if($scope.invoice.finalized === '0') {
                // charge la modal de la liste de produit
                zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function (objReturn) {
                    if (objReturn) {
                        var line = {
                            id_invoice: $routeParams.id,
                            type: "product",
                            id_product: objReturn.id,
                            ref: objReturn.ref,
                            designation_title: objReturn.name,
                            designation_desc: objReturn.description,
                            qty: 1,
                            discount: 0.00,
                            price_unit: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
                            id_taxe: parseFloat(objReturn.id_taxe),
                            value_taxe: parseFloat(objReturn.value_taxe),
                            accounting_number: parseFloat(objReturn.accounting_number),
                            sort: $scope.lines.length
                        };
                        crmTotal.line.update(line);

                        var formatted_data = angular.toJson(line);
                        zhttp.crm.invoice.line.save(formatted_data).then(function (response) {
                            if (response.data && response.data != "false") {
                                line.id = response.data;
                                $scope.lines.push(line);
                                updateInvoice();
                            }
                        });
                    }
                });
            }
		}

		function addSubTotal(){
            if($scope.invoice.finalized === '0') {
                var subTotal = {
                    id_invoice: $routeParams.id,
                    type: "subTotal",
                    sort: $scope.lines.length
                };

                var formatted_data = angular.toJson(subTotal);
                zhttp.crm.invoice.line.save(formatted_data).then(function (response) {
                    if (response.data && response.data != "false") {
                        subTotal.id = response.data;
                        $scope.lines.push(subTotal);
                        updateInvoice();
                    }
                });
            }
		}

        function addComment(comment){
            if($scope.invoice.finalized === '0') {
                if (comment.designation_desc !== "") {
                    var comment = {
                        id_invoice: $routeParams.id,
                        type: "comment",
                        designation_desc: comment.designation_desc,
                        sort: $scope.lines.length
                    };

                    var formatted_data = angular.toJson(comment);
                    zhttp.crm.invoice.line.save(formatted_data).then(function (response) {
                        if (response.data && response.data != "false") {
                            comment.id = response.data;
                            $scope.lines.push(comment);
                        }
                    });
                }
            }
        }

        function editComment(comment){
            if($scope.invoice.finalized === '0') {
                var formatted_data = angular.toJson(comment);
                zhttp.crm.invoice.line.save(formatted_data);
            }
        }

        function editLine(){
            if($scope.invoice.finalized === '0') {
                updateInvoice();
            }
        }

        function updateLine(line){
            if($scope.invoice.finalized === '0') {
                $rootScope.$broadcast("comZeappsCrm_invoiceEditTrigger",
                    {
                        line: line
                    }
                );
            }
        }

		function deleteLine(line){
            if($scope.invoice.finalized === '0') {
                if ($scope.lines.indexOf(line) > -1) {
                    zhttp.crm.invoice.line.del(line.id).then(function (response) {
                        if (response.data && response.data != "false") {
                            $scope.lines.splice($scope.lines.indexOf(line), 1);

                            for(var i = 0; i < $scope.line_details.length; i++){
                                if($scope.line_details[i].id_line === line.id){
                                    $scope.line_details.splice(i, 1);
                                    i--;
                                }
                            }

                            $rootScope.$broadcast("comZeappsCrm_invoiceDeleteTrigger",
                                {
                                    id_line: line.id
                                }
                            );

                            updateInvoice();
                        }
                    });
                }
            }
		}

		function subtotalHT(index){
			return crmTotal.sub.HT($scope.lines, index);
		}

		function subtotalTTC(index){
			return crmTotal.sub.TTC($scope.lines, index);
		}

        function editInvoice(invoice){
            if($scope.invoice.finalized === '0') {
                angular.forEach($scope.invoice, function (value, key) {
                    if (invoice[key])
                        $scope.invoice[key] = invoice[key];
                });

                updateInvoice();
            }
        }

		function updateInvoice(){
            if($scope.invoice.finalized === '0') {
                if ($scope.invoice) {
                    $scope.invoice.global_discount = $scope.invoice.global_discount || 0;

                    angular.forEach($scope.lines, function (line) {
                        crmTotal.line.update(line);
                        if (line.id) {
                            updateLine(line);
                        }
                        var formatted_data = angular.toJson(line);
                        zhttp.crm.invoice.line.save(formatted_data)
                    });

                    angular.forEach($scope.line_details, function(line){
                        crmTotal.line.update(line);
                        if(line.id){
                            updateLine(line);
                        }
                        var formatted_data = angular.toJson(line);
                        zhttp.crm.invoice.line_detail.save(formatted_data)
                    });

                    crmTotal.init($scope.invoice, $scope.lines, $scope.line_details);
                    $scope.tvas = crmTotal.get.tvas;
                    var totals = crmTotal.get.totals;
                    $scope.invoice.total_prediscount_ht = totals.total_prediscount_ht;
                    $scope.invoice.total_prediscount_ttc = totals.total_prediscount_ttc;
                    $scope.invoice.total_discount = totals.total_discount;
                    $scope.invoice.total_ht = totals.total_ht;
                    $scope.invoice.total_tva = totals.total_tva;
                    $scope.invoice.total_ttc = totals.total_ttc;

                    var data = $scope.invoice;

                    var y = data.date_creation.getFullYear();
                    var M = data.date_creation.getMonth();
                    var d = data.date_creation.getDate();

                    data.date_creation = new Date(Date.UTC(y, M, d));

                    var y = data.date_limit.getFullYear();
                    var M = data.date_limit.getMonth();
                    var d = data.date_limit.getDate();

                    data.date_limit = new Date(Date.UTC(y, M, d));

                    var formatted_data = angular.toJson(data);
                    zhttp.crm.invoice.save(formatted_data).then(function (response) {
                        if (response.data && response.data != "false") {
                            toasts('success', "Les informations du devis ont bien été mises a jour");
                        }
                        else {
                            toasts('danger', "Il y a eu une erreur lors de la mise a jour des informations du devis");
                        }
                    });
                }
            }
		}

		function addActivity(activity){
            var y = activity.deadline.getFullYear();
            var M = activity.deadline.getMonth();
            var d = activity.deadline.getDate();

            activity.deadline = new Date(Date.UTC(y, M, d));
            activity.id_invoice = $scope.invoice.id;
            var formatted_data = angular.toJson(activity);

            zhttp.crm.invoice.activity.save(formatted_data).then(function(response){
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

            zhttp.crm.invoice.activity.save(formatted_data);
		}

		function deleteActivity(activity){
            zhttp.crm.invoice.activity.del(activity.id).then(function (response) {
                if (response.status == 200) {
                    $scope.activities.splice($scope.activities.indexOf(activity), 1);
                }
            });
		}

		function addDocument(document) {
            Upload.upload({
                url: zhttp.crm.invoice.document.upload() + $scope.invoice.id,
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
                url: zhttp.crm.invoice.document.upload() + $scope.invoice.id,
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
            zhttp.crm.invoice.document.del(document.id).then(function(response){
                if(response.data && response.data != "false"){
                    $scope.documents.splice($scope.documents.indexOf(document), 1);
                }
            });
		}

		function print(){
			if($scope.invoice.final_pdf === "") {
                zhttp.crm.invoice.pdf.make($scope.invoice.id).then(function (response) {
                    if (response.data && response.data != "false") {
                        window.document.location.href = zhttp.crm.invoice.pdf.get() + angular.fromJson(response.data);
                    }
                });
            }
            else{
                window.document.location.href = zhttp.crm.invoice.pdf.get() + $scope.invoice.final_pdf;
			}
		}

		function initNavigation() {

			// calcul le nombre de résultat
			if($rootScope.invoices) {
				$scope.nb_invoices = $rootScope.invoices.ids.length;


				// calcul la position du résultat actuel
				$scope.invoice_order = 0;
				$scope.invoice_first = 0;
				$scope.invoice_previous = 0;
				$scope.invoice_next = 0;
				$scope.invoice_last = 0;

				for (var i = 0; i < $rootScope.invoices.ids.length; i++) {
					if ($rootScope.invoices.ids[i] == $routeParams.id) {
						$scope.invoice_order = i + 1;
						if (i > 0) {
							$scope.invoice_previous = $rootScope.invoices.ids[i - 1];
						}

						if ((i + 1) < $rootScope.invoices.ids.length) {
							$scope.invoice_next = $rootScope.invoices.ids[i + 1];
						}
					}
				}

				// recherche la première facture de la liste
				if ($rootScope.invoices.ids[0] != undefined) {
					if ($rootScope.invoices.ids[0] != $routeParams.id) {
						$scope.invoice_first = $rootScope.invoices.ids[0];
					}
				}
				else
					$scope.invoice_first = 0;

				// recherche la dernière facture de la liste
				if ($rootScope.invoices.ids[$rootScope.invoices.ids.length - 1] != undefined) {
					if ($rootScope.invoices.ids[$rootScope.invoices.ids.length - 1] != $routeParams.id) {
						$scope.invoice_last = $rootScope.invoices.ids[$rootScope.invoices.ids.length - 1];
					}
				}
				else
					$scope.invoice_last = 0;
			}
			else{
				$scope.nb_invoices = 0;
			}
		}

		function sortableStop( event, ui ) {
            if($scope.invoice.finalized === '0') {
                var data = {};
                var pushedLine = false;
                data.id = $(ui.item[0]).attr("data-id");

                for (var i = 0; i < $scope.lines.length; i++) {
                    if ($scope.lines[i].id == data.id && !pushedLine) {
                        data.oldSort = $scope.lines[i].sort;
                        data.sort = i;
                        $scope.lines[i].sort = data.sort;
                        pushedLine = true;
                    }
                    else if (pushedLine) {
                        $scope.lines[i].sort++;
                    }
                }

                var formatted_data = angular.toJson(data);
                zhttp.crm.invoice.line.position(formatted_data);
            }
		}

	}]);