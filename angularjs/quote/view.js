app.controller('ComZeappsCrmQuoteViewCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal', 'Upload',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal, Upload) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_quote");

        $scope.progress = 0;
        $scope.activities = [];
        $scope.documents = [];

        var initNavigation = function() {

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

            // recherche le premier devis de la liste
            if($rootScope.quotes[0] != undefined) {
                if ($rootScope.quotes[0].id != $routeParams.id) {
                    $scope.quote_first = $rootScope.quotes[0].id;
                }
            }
            else
                $scope.quote_first = 0;

            // recherche le dernier devis de la liste
            if($rootScope.quotes[$rootScope.quotes.length - 1] != undefined) {
                if ($rootScope.quotes[$rootScope.quotes.length - 1].id != $routeParams.id) {
                    $scope.quote_last = $rootScope.quotes[$rootScope.quotes.length - 1].id;
                }
            }
            else
                $scope.quote_last = 0;


            $scope.first_quote = function () {
                if ($scope.quote_first != 0) {
                    $location.path("/ng/com_zeapps_crm/quote/" + $scope.quote_first);
                }
            };
            $scope.previous_quote = function () {
                if ($scope.quote_previous != 0) {
                    $location.path("/ng/com_zeapps_crm/quote/" + $scope.quote_previous);
                }
            };
            $scope.next_quote = function () {
                if ($scope.quote_next) {
                    $location.path("/ng/com_zeapps_crm/quote/" + $scope.quote_next);
                }
            };
            $scope.last_quote = function () {
                if ($scope.quote_last) {
                    $location.path("/ng/com_zeapps_crm/quote/" + $scope.quote_last);
                }
            };

        };

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
            zhttp.crm.quote.get($routeParams.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.quote = response.data.quote;
                    $scope.company = response.data.company;
                    $scope.contact = response.data.contact;
                    $scope.lines = response.data.lines || [];
                    $scope.activities = response.data.activities || [];
                    $scope.documents = response.data.documents || [];

                    $scope.quote.date_creation = new Date($scope.quote.date_creation);
                    $scope.quote.date_limit = new Date($scope.quote.date_limit);

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

            $scope.edit = false;

            var data = $scope.quote;

            var formatted_data = angular.toJson(data);

            zhttp.crm.quote.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.toasts.push({success:'Les informations du devis ont bien été mises a jour'});
                }
                else{
                    $rootScope.toasts.push({danger:'Il y a eu une erreur lors de la mise a jour des informations du devis'});
                }
            });
        };

        $scope.cancel = function(){
            $scope.edit = false;
        };

        $scope.createOrder = function(){
            zhttp.get('/com_zeapps_crm/quotes/createOrderFrom/' + $scope.quote.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $location.url('/ng/com_zeapps_crm/order/' + response.data);
                }
            })
        };

        $scope.sortable = {
            connectWith: ".sortableContainer",
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

        $scope.toggleEdit = function(){
            $scope.edit = !$scope.edit;
        };

        $scope.toggleComment = function(){
            $scope.showCommentInput = !$scope.showCommentInput;
        };

        $scope.addLine = function(){
            // charge la modal de la liste de produit
            zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function(objReturn) {
                //console.log(objReturn);
                if (objReturn) {
                    var line = {
                        id_quote: $routeParams.id,
                        num: objReturn.ref,
                        designation_title: objReturn.name,
                        designation_desc: objReturn.description,
                        qty: '1',
                        discount: 0.00,
                        price_unit: objReturn.price_ht,
                        taxe: objReturn.tva,
                        sort: $scope.lines.length
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
        };

        $scope.addSubTotal = function(){
            var subTotal = {
                id_quote: $routeParams.id,
                num: 'subTotal',
                sort: $scope.lines.length
            };

            var formatted_data = angular.toJson(subTotal);
            zhttp.crm.quote.line.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    subTotal.id = response.data;
                    $scope.lines.push(subTotal);
                }
            });
        };

        $scope.addComment = function(){
            if($scope.comment != ''){
                var comment = {
                    id_quote: $routeParams.id,
                    num: 'comment',
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
        };

        $scope.editLine = function(line){
            line.edit = true;
        };

        $scope.submitLine = function(line){
            console.log(line.id);
            var formatted_data = angular.toJson(line);
            zhttp.crm.quote.line.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    line.edit = false;
                }
            });
        };

        $scope.deleteLine = function(line){
            if($scope.lines.indexOf(line) > -1){
                zhttp.crm.quote.line.del(line.id).then(function(response){
                    if(response.data && response.data != 'false'){
                        $scope.lines.splice($scope.lines.indexOf(line), 1);
                    }
                });
            }
        };


        $scope.subtotalHT = function(index){
            var total = 0;
            for(var i = index - 1; i >= 0; i--){
                if($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment'){
                    total += $scope.lines[i].price_unit * $scope.lines[i].qty;
                }
                else if($scope.lines[i].num == 'subTotal'){
                    i = -1;
                }
            }
            return total;
        };

        $scope.subtotalTTC = function(index){
            var total = 0;
            for(var i = index - 1; i >= 0; i--){
                if($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment'){
                    total += $scope.lines[i].price_unit * $scope.lines[i].qty * ( 1 + ($scope.lines[i].taxe / 100) );
                }
                else if($scope.lines[i].num == 'subTotal'){
                    i = -1;
                }
            }
            return total;
        };

        $scope.totalAvDiscountHT = function(){
            var total = 0;
            for(var i = 0; i < $scope.lines.length; i++){
                if($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment'){
                    total += $scope.lines[i].price_unit * $scope.lines[i].qty;
                }
            }
            return total;
        };

        $scope.totalAvDiscountTTC = function(){
            var total = 0;
            for(var i = 0; i < $scope.lines.length; i++){
                if($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment'){
                    total += $scope.lines[i].price_unit * $scope.lines[i].qty * ( 1 + ($scope.lines[i].taxe / 100) );
                }
            }
            return total;
        };

        $scope.totalDiscount = function(){
            var total = 0;
            if($scope.quote) {
                for (var i = 0; i < $scope.lines.length; i++) {
                    if ($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment') {
                        total += $scope.lines[i].price_unit * $scope.lines[i].qty * ($scope.lines[i].discount / 100);
                    }
                }
                total = total + $scope.totalAvDiscountHT() * ($scope.quote.global_discount / 100);
            }
            return total;
        };

        $scope.totalHT = function(){
            var total = 0;
            if($scope.quote) {
                for(var i = 0; i < $scope.lines.length; i++){
                    if($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment'){
                        total += $scope.lines[i].price_unit * $scope.lines[i].qty * ( 1 - ($scope.lines[i].discount / 100) );
                    }
                }
                total = total * (1- ($scope.quote.global_discount / 100) );
            }
            return total;
        };

        $scope.totalTTC = function(){
            var total = 0;
            if($scope.quote) {
                for(var i = 0; i < $scope.lines.length; i++){
                    if($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment'){
                        total += $scope.lines[i].price_unit * $scope.lines[i].qty * ( 1 - ($scope.lines[i].discount / 100) ) * ( 1 + ($scope.lines[i].taxe / 100) );
                    }
                }
                total = total * (1- ($scope.quote.global_discount / 100) );
            }
            return total;
        };


        $scope.toggleActivity = function(){
            $scope.showActivityInput = !$scope.showActivityInput;
        };

        $scope.addActivity = function(){
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
                        delete $scope.activity;
                    }
                });
            }
        };

        $scope.editActivity = function(activity){
            $scope.activity = activity;
            $scope.showActivityInput = true;
        };


        $scope.upload = function (files) {

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
        };

        $scope.deleteDocument = function(document){
            zhttp.crm.quote.document.del(document.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.documents.splice($scope.documents.indexOf(document), 1);
                }
            });
        };

        $scope.print = function(){
            zhttp.crm.quote.pdf.make($scope.quote.id).then(function(response){
                if(response.data && response.data != 'false'){
                    window.document.location.href = zhttp.crm.quote.pdf.get() + angular.fromJson(response.data);
                }
            });
        }


    }]);