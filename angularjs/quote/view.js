app.controller('ComZeappsCrmQuoteViewCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal', 'Upload', 'crmTotal', 'zeHooks',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal, Upload, crmTotal, zeHooks) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_quote");

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

        $scope.first_quote = first_quote;
        $scope.previous_quote = previous_quote;
        $scope.next_quote = next_quote;
        $scope.last_quote = last_quote;

        $scope.success = success;
        $scope.cancel = cancel;
        $scope.finalize = finalize;

        $scope.toggleEdit = toggleEdit;
        $scope.toggleComment = toggleComment;

        $scope.addLine = addLine;
        $scope.addSubTotal = addSubTotal;
        $scope.addComment = addComment;
        $scope.editLine = editLine;

        $scope.updateSums = updateSums;

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


        if($rootScope.quotes == undefined || $rootScope.quotes[0] == undefined) {
            zhttp.crm.quote.get_all().then(function (response) {
                if (response.status == 200) {
                    $rootScope.quotes = response.data;
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
            zhttp.crm.quote.get($routeParams.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.quote = response.data.quote;
                    $scope.sortable.disabled = !!parseInt($scope.quote.finalized);
                    $scope.company = response.data.company;
                    $scope.contact = response.data.contact;
                    $scope.lines = response.data.lines || [];
                    $scope.activities = response.data.activities || [];
                    $scope.documents = response.data.documents || [];

                    $scope.quote.date_creation = new Date($scope.quote.date_creation);
                    $scope.quote.date_limit = new Date($scope.quote.date_limit);

                    var i;

                    for(i=0;i<$scope.activities.length;i++){
                        $scope.activities[i].reminder = new Date($scope.activities[i].reminder);
                    }

                    for(i=0;i<$scope.documents.length;i++){
                        $scope.documents[i].created_at = new Date($scope.documents[i].created_at);
                    }
                }
            });
        }

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
                zhttp.crm.quote.line.position(formatted_data);
            }
        };


        //////////////////// WATCHERS ////////////////////


        $scope.$watch('lines', function(value, oldValue){
            if(value != oldValue && oldValue != undefined)
                updateTotals();
        }, true);
        $scope.$watch('quote.global_discount', function(value, oldValue){
            if(value != oldValue && oldValue != undefined)
                updateTotals();
        });
        $scope.$watch('navigationState', function(){
            $rootScope.comZeappsCrmLastShowTabQuote = $scope.navigationState ;
        }, true);


        //////////////////// FUNCTIONS ////////////////////

        function broadcast(){
            $rootScope.$broadcast('comZeappsCrm_dataQuoteHook',
                {
                    quote: $scope.quote
                }
            );
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

        function success(){
            if($scope.quote.finalized !== '0')
                return;

            var data = $scope.quote;

            var formatted_data = angular.toJson(data);

            zhttp.crm.quote.save(formatted_data).then(function(response){
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
            zeapps_modal.loadModule("com_zeapps_crm", "finalize_quote", {}, function(objReturn) {
                if (objReturn) {
                    var formatted_data = angular.toJson(objReturn);
                    zhttp.crm.quote.finalize($scope.quote.id, formatted_data).then(function(response){
                        if(response.data && response.data != 'false'){
                            $scope.quote.final_pdf = response.data.nomPDF;
                            $scope.quote.finalized = '1';
                            $scope.sortable.disabled = true;

                            if(objReturn.order){
                                $location.url('/ng/com_zeapps_crm/order/' + response.data.order);
                            }
                        }
                    });
                }
            });
        }

        function toggleEdit(){
            if($scope.quote.finalized !== '0')
                return;

            $scope.edit = !$scope.edit;
        }

        function toggleComment(){
            if($scope.quote.finalized !== '0')
                return;

            $scope.showCommentInput = !$scope.showCommentInput;
        }

        function addLine(){
            if($scope.quote.finalized !== '0')
                return;

            // charge la modal de la liste de produit
            zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function(objReturn) {
                //console.log(objReturn);
                if (objReturn) {
                    var line = {
                        id_quote: $routeParams.id,
                        type: 'product',
                        id_product: objReturn.id,
                        ref: objReturn.ref,
                        designation_title: objReturn.name,
                        designation_desc: objReturn.description,
                        qty: '1',
                        discount: 0.00,
                        price_unit: objReturn.price_ht,
                        taxe: objReturn.tva,
                        sort: $scope.lines.length,
                        total_ht: objReturn.price_ht,
                        total_ttc: (parseFloat(objReturn.price_ht) * (1 + (parseFloat(objReturn.value_taxe) / 100)))
                    };

                    var formatted_data = angular.toJson(line);
                    zhttp.crm.quote.line.save(formatted_data).then(function(response){
                        if(response.data && response.data != 'false'){
                            line.id = response.data;
                            $scope.lines.push(line);
                        }
                    });
                }
            });
        }

        function addSubTotal(){
            if($scope.quote.finalized !== '0')
                return;

            var subTotal = {
                id_quote: $routeParams.id,
                type: 'subTotal',
                sort: $scope.lines.length
            };

            var formatted_data = angular.toJson(subTotal);
            zhttp.crm.quote.line.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    subTotal.id = response.data;
                    $scope.lines.push(subTotal);
                }
            });
        }

        function addComment(){
            if($scope.quote.finalized !== '0')
                return;

            if($scope.comment != ''){
                var comment = {
                    id_quote: $routeParams.id,
                    type: 'comment',
                    designation_desc: '',
                    sort: $scope.lines.length
                };
                comment.designation_desc = $scope.comment;

                var formatted_data = angular.toJson(comment);
                zhttp.crm.quote.line.save(formatted_data).then(function(response){
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
            if($scope.quote.finalized !== '0')
                return;

            if(line.type === 'product')
                line.edit = true;
            else{
                $rootScope.$broadcast('comZeappsCrm_quoteEditTrigger',
                    {
                        line : line
                    }
                );
            }
        }

        function updateSums(line){
            line.total_ht = parseFloat(line.price_unit) * parseFloat(line.qty) * ( 1 - (parseFloat(line.discount) / 100) );
            line.total_ttc = parseFloat(line.price_unit) * parseFloat(line.qty) * ( 1 - (parseFloat(line.discount) / 100) ) * ( 1 + (parseFloat(line.taxe) / 100) );
        }

        function submitLine(line){
            if($scope.quote.finalized !== '0')
                return;

            var formatted_data = angular.toJson(line);
            zhttp.crm.quote.line.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    line.edit = false;
                }
            });
        }

        function deleteLine(line){
            if($scope.quote.finalized !== '0')
                return;

            if($scope.lines.indexOf(line) > -1){
                zhttp.crm.quote.line.del(line.id).then(function(response){
                    if(response.data && response.data != 'false'){
                        $scope.lines.splice($scope.lines.indexOf(line), 1);

                        $rootScope.$broadcast('comZeappsCrm_quoteDeleteTrigger',
                            {
                                id_line : line.id
                            }
                        );
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
                $scope.quote.total_ht = crmTotal.total.HT($scope.lines, $scope.quote.global_discount);
                $scope.quote.total_ttc = crmTotal.total.TTC($scope.lines, $scope.quote.global_discount);

                var data = $scope.quote;

                var formatted_data = angular.toJson(data);

                zhttp.crm.quote.save(formatted_data);
            }
        }

        function toggleActivity(){
            if($scope.quote.finalized !== '0')
                return;

            $scope.activity = {};
            $scope.activity.reminder = new Date();
            $scope.showActivityInput = !$scope.showActivityInput;
        }

        function closeActivity(){
            $scope.showActivityInput = false;
        }

        function addActivity(){
            if($scope.quote.finalized !== '0')
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
                data['id_quote'] = $routeParams.id;
                data['libelle'] = $scope.activity.libelle;
                data['description'] = $scope.activity.description;
                data['reminder'] = date;

                var formatted_data = angular.toJson(data);
                zhttp.crm.quote.activity.save(formatted_data).then(function(response){
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
            if($scope.quote.finalized !== '0')
                return;

            $scope.activity = activity;
            $scope.showActivityInput = true;
        }

        function deleteActivity(activity){
            if($scope.quote.finalized !== '0')
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
                    zhttp.crm.quote.activity.del(activity.id).then(function (response) {
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
            if($scope.quote.finalized !== '0')
                return;

            $scope.files = files;
            $scope.progress = 0;

            if (files && files.length) {
                Upload.upload({
                    url: zhttp.crm.quote.document.upload() + $routeParams.id,
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
            if($scope.quote.finalized !== '0')
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
                    zhttp.crm.quote.document.del(document.id).then(function(response){
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
            if($scope.quote.finalized !== '0'){
                window.document.location.href = zhttp.crm.quote.pdf.get() + $scope.quote.final_pdf;
            }
            else{
                zhttp.crm.quote.pdf.make($scope.quote.id).then(function(response){
                    if(response.data && response.data != 'false'){
                        window.document.location.href = zhttp.crm.quote.pdf.get() + angular.fromJson(response.data);
                    }
                });
            }
        }

        function initNavigation() {

            // calcul le nombre de résultat
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
            if($rootScope.quotes[0] != undefined) {
                if ($rootScope.quotes[0].id != $routeParams.id) {
                    $scope.quote_first = $rootScope.quotes[0].id;
                }
            }
            else
                $scope.quote_first = 0;

            // recherche la dernière facture de la liste
            if($rootScope.quotes[$rootScope.quotes.length - 1] != undefined) {
                if ($rootScope.quotes[$rootScope.quotes.length - 1].id != $routeParams.id) {
                    $scope.quote_last = $rootScope.quotes[$rootScope.quotes.length - 1].id;
                }
            }
            else
                $scope.quote_last = 0;
        }

    }]);