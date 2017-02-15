app.controller('ComZeappsCrmInvoiceViewCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal', 'Upload', 'crmTotal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal, Upload, crmTotal) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_invoice");

        $scope.progress = 0;
        $scope.activities = [];
        $scope.documents = [];

        var initNavigation = function() {

            // calcul le nombre de résultat
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
            if($rootScope.invoices[0] != undefined) {
                if ($rootScope.invoices[0].id != $routeParams.id) {
                    $scope.invoice_first = $rootScope.invoices[0].id;
                }
            }
            else
                $scope.invoice_first = 0;

            // recherche la dernière facture de la liste
            if($rootScope.invoices[$rootScope.invoices.length - 1] != undefined) {
                if ($rootScope.invoices[$rootScope.invoices.length - 1].id != $routeParams.id) {
                    $scope.invoice_last = $rootScope.invoices[$rootScope.invoices.length - 1].id;
                }
            }
            else
                $scope.invoice_last = 0;

            $scope.first_invoice = function () {
                if ($scope.invoice_first != 0) {
                    $location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_first);
                }
            };
            $scope.previous_invoice = function () {
                if ($scope.invoice_previous != 0) {
                    $location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_previous);
                }
            };
            $scope.next_invoice = function () {
                if ($scope.invoice_next) {
                    $location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_next);
                }
            };
            $scope.last_invoice = function () {
                if ($scope.invoice_last) {
                    $location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_last);
                }
            };

        };

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

        // pour détecter les changements sur le models
        $scope.$watch('navigationState', function(){
            $rootScope.comZeappsCrmLastShowTabQuote = $scope.navigationState ;
        }, true);
        /******* FIN : gestion de la tabs *********/

        $scope.edit = false;
        $scope.showCommentInput = false;
        $scope.showActivityInput = false;
        $scope.comment = '';

        $scope.lines = [];

        if($routeParams.id && $routeParams.id > 0){
            zhttp.crm.invoice.get($routeParams.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.invoice = response.data.invoice;
                    $scope.sortable.disabled = !!parseInt($scope.invoice.finalized);
                    $scope.company = response.data.company;
                    $scope.contact = response.data.contact;
                    $scope.lines = response.data.lines || [];
                    $scope.activities = response.data.activities || [];
                    $scope.documents = response.data.documents || [];

                    $scope.invoice.date_creation = new Date($scope.invoice.date_creation);
                    $scope.invoice.date_limit = new Date($scope.invoice.date_limit);

                    for(var i=0;i<$scope.activities.length;i++){
                        $scope.activities[i].reminder = new Date($scope.activities[i].reminder);
                    }

                    for(var i=0;i<$scope.documents.length;i++){
                        $scope.documents[i].created_at = new Date($scope.documents[i].created_at);
                    }
                }
            });
        }

        $scope.success = function(){
            if($scope.invoice.finalized !== '0')
                return;

            var data = $scope.invoice;

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
        };

        $scope.cancel = function(){
            $scope.edit = false;
        };

        $scope.finalizeInvoice = function(){
            zhttp.crm.invoice.finalize($scope.invoice.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.invoice.final_pdf = response.data.nomPDF;
                    $scope.invoice.numerotation = response.data.numerotation;
                    $scope.invoice.finalized = '1';
                    $scope.sortable.disabled = true;
                }
            })
        };

        $scope.sortable = {
            connectWith: ".sortableContainer",
            disabled: false,
            axis: 'y',
            stop: function( event, ui ) {

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
        };

        $scope.toggleEdit = function(){
            if($scope.invoice.finalized !== '0')
                return;

            $scope.edit = !$scope.edit;
        };

        $scope.toggleComment = function(){
            if($scope.invoice.finalized !== '0')
                return;

            $scope.showCommentInput = !$scope.showCommentInput;
        };

        $scope.addLine = function(){
            if($scope.invoice.finalized !== '0')
                return;

            // charge la modal de la liste de produit
            zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function(objReturn) {
                //console.log(objReturn);
                if (objReturn) {
                    var line = {
                        id_invoice: $routeParams.id,
                        type: 'product',
                        ref: objReturn.ref,
                        designation_title: objReturn.name,
                        designation_desc: objReturn.description,
                        qty: '1',
                        discount: 0.00,
                        price_unit: objReturn.price_ht,
                        taxe: objReturn.tva,
                        sort: $scope.lines.length
                    };

                    var formatted_data = angular.toJson(line);
                    zhttp.crm.invoice.line.save(formatted_data).then(function(response){
                        if(response.data && response.data != 'false'){
                            line.id = response.data;
                            $scope.lines.push(line);
                        }
                    });
                }
            });
        };

        $scope.addSubTotal = function(){
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
                }
            });
        };

        $scope.addComment = function(){
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
        };

        $scope.editLine = function(line){
            if($scope.invoice.finalized !== '0')
                return;

            line.edit = true;
        };

        $scope.submitLine = function(line){
            if($scope.invoice.finalized !== '0')
                return;

            var formatted_data = angular.toJson(line);
            zhttp.crm.invoice.line.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    line.edit = false;
                }
            });
        };

        $scope.deleteLine = function(line){
            if($scope.invoice.finalized !== '0')
                return;

            if($scope.lines.indexOf(line) > -1){
                zhttp.crm.invoice.line.del(line.id).then(function(response){
                    if(response.data && response.data != 'false'){
                        $scope.lines.splice($scope.lines.indexOf(line), 1);
                    }
                });
            }
        };

        $scope.subtotalHT = function(index){
            return crmTotal.sub.HT($scope.lines, index);
        };

        $scope.subtotalTTC = function(index){
            return crmTotal.sub.TTC($scope.lines, index);
        };
        $scope.$watch('lines', function(value, oldValue){
            if(value != oldValue && oldValue != undefined)
                updateTotals();
        }, true);
        $scope.$watch('invoice.global_discount', function(value, oldValue){
            if(value != oldValue && oldValue != undefined)
                updateTotals();
        });
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

        $scope.toggleActivity = function(){
            if($scope.invoice.finalized !== '0')
                return;

            $scope.activity = {};
            $scope.activity.reminder = new Date();
            $scope.showActivityInput = !$scope.showActivityInput;
        };

        $scope.closeActivity = function(){
            $scope.showActivityInput = false;
        };

        $scope.addActivity = function(){
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
        };

        $scope.editActivity = function(activity){
            if($scope.invoice.finalized !== '0')
                return;

            $scope.activity = activity;
            $scope.showActivityInput = true;
        };

        $scope.deleteActivity = function(activity){
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
        };


        $scope.upload = function (files) {
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
        };

        $scope.deleteDocument = function(document){
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
                    zhttp.crm.invoice.document.del(document.id).then(function(response){
                        if(response.data && response.data != 'false'){
                            $scope.documents.splice($scope.documents.indexOf(document), 1);
                        }
                    });
                }

            }, function () {
                //console.log("rien");
            });
        };

        $scope.print = function(){
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


    }]);