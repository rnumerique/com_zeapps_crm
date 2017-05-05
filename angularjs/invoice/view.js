app.controller('ComZeappsCrmInvoiceViewCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal', 'Upload', 'crmTotal', 'zeHooks',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal, Upload, crmTotal, zeHooks) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_invoice");

        $scope.$on('comZeappsCrm_triggerOrderHook', broadcast);
        $scope.hooks = zeHooks.get('comZeappsCrm_OrderHook');

        $scope.progress = 0;
        $scope.activities = [];
        $scope.documents = [];

        $scope.edit = false;
        $scope.showCommentInput = false;
        $scope.showActivityInput = false;
        $scope.comment = '';

        $scope.lines = [];

        $scope.first_invoice = first_invoice;
        $scope.previous_invoice = previous_invoice;
        $scope.next_invoice = next_invoice;
        $scope.last_invoice = last_invoice;

        $scope.success = success;
        $scope.cancel = cancel;
        $scope.finalize = finalize;

        $scope.toggleEdit = toggleEdit;
        $scope.toggleComment = toggleComment;

        $scope.addFromCode = addFromCode;
        $scope.addLine = addLine;
        $scope.addSubTotal = addSubTotal;
        $scope.addComment = addComment;
        $scope.editLine = editLine;

        $scope.updateSums = updateSums;
        $scope.updateTotals = updateTotals;

        $scope.submitLine = submitLine;
        $scope.deleteLine = deleteLine;

        $scope.subtotalHT = subtotalHT;
        $scope.subtotalTTC = subtotalTTC;

        $scope.toggleActivity = toggleActivity;
        $scope.closeActivity = closeActivity;
        $scope.addActivity = addActivity;
        $scope.editActivity = editActivity;
        $scope.deleteActivity = deleteActivity;

        $scope.upload = upload;
        $scope.deleteDocument = deleteDocument;

        $scope.print = print;


        //////////////////// INIT ////////////////////


        if($rootScope.invoices == undefined || $rootScope.invoices[0] == undefined) {
            zhttp.crm.invoice.get_all().then(function (response) {
                if (response.status == 200) {
                    $rootScope.invoices = response.data;
                    initNavigation();
                }
            });
        }
        else{
            initNavigation();
        }

        /******* gestion de la tabs *********/
        $scope.navigationState = 'body';
        if ($rootScope.comZeappsCrmLastShowTabQuote) {
            $scope.navigationState = $rootScope.comZeappsCrmLastShowTabQuote ;
        }


        if($routeParams.id && $routeParams.id > 0){
            zhttp.crm.invoice.get($routeParams.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.invoice = response.data.invoice;
                    $scope.sortable.disabled = !!parseInt($scope.invoice.finalized);
                    $scope.company = response.data.company;
                    $scope.contact = response.data.contact;
                    $scope.activities = response.data.activities || [];
                    $scope.documents = response.data.documents || [];

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
                }
            });
        }

        $scope.sortable = {
            connectWith: ".sortableContainer",
            disabled: false,
            axis: 'y',
            stop: sortableStop
        };


        //////////////////// WATCHERS ////////////////////

        $scope.$watch('navigationState', function(){
            $rootScope.comZeappsCrmLastShowTabQuote = $scope.navigationState ;
        }, true);

        //////////////////// FUNCTIONS ////////////////////


        function broadcast(){
            $rootScope.$broadcast('comZeappsCrm_dataInvoiceHook',
                {
                    invoice: $scope.invoice
                }
            );
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

        function success(){
            if($scope.invoice.finalized !== '0')
                return;

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

            zhttp.crm.invoice.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.toasts.push({success:'Les informations de la commande ont bien été mises a jour'});
                    $scope.edit = false;
                }
                else{
                    $rootScope.toasts.push({danger:'Il y a eu une erreur lors de la mise a jour des informations de la commande'});
                }
            });
        }

        function cancel(){
            $scope.edit = false;
        }

        function finalize(){
            zhttp.crm.invoice.finalize($scope.invoice.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.invoice.final_pdf = response.data.nomPDF;
                    $scope.invoice.finalized = '1';
                    $scope.sortable.disabled = true;
                }
            })
        }

        function toggleEdit(){
            if($scope.invoice.finalized !== '0')
                return;

            $scope.edit = !$scope.edit;
        }

        function toggleComment(){
            if($scope.invoice.finalized !== '0')
                return;

            $scope.showCommentInput = !$scope.showCommentInput;
        }

        function addFromCode(){
            var code = $scope.codeProduct;
            zhttp.crm.product.get_code(code).then(function(response){
                if(response.data && response.data != 'false'){
                    var line = {
                        id_order: $routeParams.id,
                        type: 'product',
                        id_product: response.data.id,
                        ref: response.data.ref,
                        designation_title: response.data.name,
                        designation_desc: response.data.description,
                        qty: '1',
                        discount: 0.00,
                        price_unit: parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc),
                        taxe: ""+parseFloat(response.data.value_taxe),
                        sort: $scope.lines.length,
                        total_ht: parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc),
                        total_ttc: ((parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc)) * (1 + (parseFloat(response.data.value_taxe) / 100)))
                    };

                    var formatted_data = angular.toJson(line);
                    zhttp.crm.order.line.save(formatted_data).then(function(response){
                        if(response.data && response.data != 'false'){
                            line.id = response.data;
                            $scope.lines.push(line);
                            updateTotals();
                        }
                    });
                }
                else{
                    $rootScope.toasts.push({'danger' : 'Aucun produit avec le code ' + code + ' trouvé dans la base de donnée.'})
                }
            })
        }

        function addLine(){
            if($scope.invoice.finalized !== '0')
                return;

            // charge la modal de la liste de produit
            zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function(objReturn) {
                //console.log(objReturn);
                if (objReturn) {
                    var line = {
                        id_invoice: $routeParams.id,
                        type: 'product',
                        id_product: objReturn.id,
                        ref: objReturn.ref,
                        designation_title: objReturn.name,
                        designation_desc: objReturn.description,
                        qty: '1',
                        discount: 0.00,
                        price_unit: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
                        taxe: parseFloat(objReturn.value_taxe),
                        sort: $scope.lines.length,
                        total_ht: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
                        total_ttc: ((parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc)) * (1 + (parseFloat(objReturn.value_taxe) / 100)))
                    };

                    var formatted_data = angular.toJson(line);
                    zhttp.crm.invoice.line.save(formatted_data).then(function(response){
                        if(response.data && response.data != 'false'){
                            line.id = response.data;
                            $scope.lines.push(line);
                            updateTotals();
                        }
                    });
                }
            });
        }

        function addSubTotal(){
            if($scope.invoice.finalized !== '0')
                return;

            var subTotal = {
                id_invoice: $routeParams.id,
                type: 'subTotal',
                sort: $scope.lines.length
            };

            var formatted_data = angular.toJson(subTotal);
            zhttp.crm.invoice.line.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    subTotal.id = response.data;
                    $scope.lines.push(subTotal);
                    updateTotals();
                }
            });
        }

        function addComment(){
            if($scope.invoice.finalized !== '0')
                return;

            if($scope.comment != ''){
                var comment = {
                    id_invoice: $routeParams.id,
                    type: 'comment',
                    designation_desc: '',
                    sort: $scope.lines.length
                };
                comment.designation_desc = $scope.comment;

                var formatted_data = angular.toJson(comment);
                zhttp.crm.invoice.line.save(formatted_data).then(function(response){
                    if(response.data && response.data != 'false'){
                        comment.id = response.data;
                        $scope.lines.push(comment);
                        $scope.comment = '';
                        $scope.showCommentInput = false;
                    }
                });
            }
        }

        function editLine(line){
            if($scope.invoice.finalized !== '0')
                return;

            if(line.type === 'product') {
                line.edit = true;
            }
            else{
                $rootScope.$broadcast('comZeappsCrm_invoiceEditTrigger',
                    {
                        line : line
                    }
                );
            }
        }

        function submitLine(line){
            if($scope.invoice.finalized !== '0')
                return;

            var formatted_data = angular.toJson(line);
            zhttp.crm.invoice.line.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    line.edit = false;
                    updateTotals();
                }
            });
        }

        function updateSums(line){
            line.total_ht = parseFloat(line.price_unit) * parseFloat(line.qty) * ( 1 - (parseFloat(line.discount) / 100) );
            line.total_ttc = parseFloat(line.price_unit) * parseFloat(line.qty) * ( 1 - (parseFloat(line.discount) / 100) ) * ( 1 + (parseFloat(line.taxe) / 100) );
        }

        function deleteLine(line){
            if($scope.invoice.finalized !== '0')
                return;

            if($scope.lines.indexOf(line) > -1){
                zhttp.crm.invoice.line.del(line.id).then(function(response){
                    if(response.data && response.data != 'false'){
                        $scope.lines.splice($scope.lines.indexOf(line), 1);

                        $rootScope.$broadcast('comZeappsCrm_invoiceDeleteTrigger',
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
            if($scope.invoice) {
                $scope.invoice.total_prediscount_ht = crmTotal.preDiscount.HT($scope.lines);
                $scope.invoice.total_prediscount_ttc = crmTotal.preDiscount.TTC($scope.lines);
                $scope.invoice.total_discount = crmTotal.discount($scope.lines, $scope.invoice.global_discount);
                $scope.invoice.total_ht = crmTotal.total.HT($scope.lines, $scope.invoice.global_discount);
                $scope.invoice.total_ttc = crmTotal.total.TTC($scope.lines, $scope.invoice.global_discount);

                var data = $scope.invoice;

                var formatted_data = angular.toJson(data);

                zhttp.crm.invoice.save(formatted_data);
            }
        }

        function toggleActivity(){
            if($scope.invoice.finalized !== '0')
                return;

            $scope.activity = {};
            $scope.activity.reminder = new Date();
            $scope.showActivityInput = !$scope.showActivityInput;
        }

        function closeActivity(){
            $scope.showActivityInput = false;
        }

        function addActivity(){
            if($scope.invoice.finalized !== '0')
                return;

            if($scope.activity != undefined) {
                var data = {};

                var y = $scope.activity.reminder.getFullYear();
                var M = $scope.activity.reminder.getMonth();
                var d = $scope.activity.reminder.getDate();

                var date = new Date(Date.UTC(y, M, d));

                if($scope.activity.id != undefined){
                    data['id'] = $scope.activity.id;
                }
                else{
                    data['deadline'] = date;
                }
                data['id_invoice'] = $routeParams.id;
                data['libelle'] = $scope.activity.libelle;
                data['description'] = $scope.activity.description;
                data['reminder'] = date;

                var formatted_data = angular.toJson(data);
                zhttp.crm.invoice.activity.save(formatted_data).then(function(response){
                    if(response.data && response.data != 'false'){
                        if($scope.activity.id == undefined)
                            $scope.activities.push(response.data);
                        $scope.activity = {};
                        $scope.activity.reminder = new Date();
                    }
                });
            }
        }

        function editActivity(activity){
            if($scope.invoice.finalized !== '0')
                return;

            $scope.activity = activity;
            $scope.showActivityInput = true;
        }

        function deleteActivity(activity){
            if($scope.invoice.finalized !== '0')
                return;

            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: '/assets/angular/popupModalDeBase.html',
                controller: 'ZeAppsPopupModalDeBaseCtrl',
                size: 'lg',
                resolve: {
                    titre: function () {
                        return 'Attention';
                    },
                    msg: function () {
                        return 'Souhaitez-vous supprimer définitivement cette activité ?';
                    },
                    action_danger: function () {
                        return 'Annuler';
                    },
                    action_primary: function () {
                        return false;
                    },
                    action_success: function () {
                        return 'Je confirme la suppression';
                    }
                }
            });

            modalInstance.result.then(function (selectedItem) {
                if (selectedItem.action == 'danger') {

                } else if (selectedItem.action == 'success') {
                    zhttp.crm.invoice.activity.del(activity.id).then(function (response) {
                        if (response.status == 200) {
                            $scope.activities.splice($scope.activities.indexOf(activity), 1);
                        }
                    });
                }

            }, function () {
                //console.log("rien");
            });
        }

        function upload(files) {
            if($scope.invoice.finalized !== '0')
                return;

            $scope.files = files;
            $scope.progress = 0;

            if (files && files.length) {
                Upload.upload({
                    url: zhttp.crm.invoice.document.upload() + $routeParams.id,
                    data: {
                        files: files
                    }
                }).then(
                    function(response){
                        delete $scope.progress;
                        if(response.data && response.data != 'false'){
                            for(var i = 0; i<response.data.length; i++) {
                                $scope.documents.push(response.data[i]);
                            }
                            $rootScope.toasts.push({success: 'Les documents ont bien été mis en ligne'});
                        }
                        else{
                            $rootScope.toasts.push({danger: 'Il y a eu une erreur lors de la mise en ligne des documents'});
                        }
                    },
                    null,
                    function(evt){
                        $scope.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
                    }
                );
            }
        }

        function deleteDocument(document){
            if($scope.invoice.finalized !== '0')
                return;

            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: '/assets/angular/popupModalDeBase.html',
                controller: 'ZeAppsPopupModalDeBaseCtrl',
                size: 'lg',
                resolve: {
                    titre: function () {
                        return 'Attention';
                    },
                    msg: function () {
                        return 'Souhaitez-vous supprimer définitivement ce document ?';
                    },
                    action_danger: function () {
                        return 'Annuler';
                    },
                    action_primary: function () {
                        return false;
                    },
                    action_success: function () {
                        return 'Je confirme la suppression';
                    }
                }
            });

            modalInstance.result.then(function (selectedItem) {
                if (selectedItem.action == 'danger') {

                } else if (selectedItem.action == 'success') {
                    zhttp.crm.invoice.document.del(document.id).then(function(response){
                        if(response.data && response.data != 'false'){
                            $scope.documents.splice($scope.documents.indexOf(document), 1);
                        }
                    });
                }

            }, function () {
                //console.log("rien");
            });
        }

        function print(){
            if($scope.invoice.finalized !== '0'){
                window.document.location.href = zhttp.crm.invoice.pdf.get() + $scope.invoice.final_pdf;
            }
            else{
                zhttp.crm.invoice.pdf.make($scope.invoice.id).then(function(response){
                    if(response.data && response.data != 'false'){
                        window.document.location.href = zhttp.crm.invoice.pdf.get() + angular.fromJson(response.data);
                    }
                });
            }
        }

        function initNavigation() {

            // calcul le nombre de résultat
            if($rootScope.invoices) {
                $scope.nb_invoices = $rootScope.invoices.length;


                // calcul la position du résultat actuel
                $scope.invoice_order = 0;
                $scope.invoice_first = 0;
                $scope.invoice_previous = 0;
                $scope.invoice_next = 0;
                $scope.invoice_last = 0;

                for (var i = 0; i < $rootScope.invoices.length; i++) {
                    if ($rootScope.invoices[i].id == $routeParams.id) {
                        $scope.invoice_order = i + 1;
                        if (i > 0) {
                            $scope.invoice_previous = $rootScope.invoices[i - 1].id;
                        }

                        if ((i + 1) < $rootScope.invoices.length) {
                            $scope.invoice_next = $rootScope.invoices[i + 1].id;
                        }
                    }
                }

                // recherche la première facture de la liste
                if ($rootScope.invoices[0] != undefined) {
                    if ($rootScope.invoices[0].id != $routeParams.id) {
                        $scope.invoice_first = $rootScope.invoices[0].id;
                    }
                }
                else
                    $scope.invoice_first = 0;

                // recherche la dernière facture de la liste
                if ($rootScope.invoices[$rootScope.invoices.length - 1] != undefined) {
                    if ($rootScope.invoices[$rootScope.invoices.length - 1].id != $routeParams.id) {
                        $scope.invoice_last = $rootScope.invoices[$rootScope.invoices.length - 1].id;
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
            zhttp.crm.invoice.line.position(formatted_data);
        }

    }]);