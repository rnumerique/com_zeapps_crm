app.controller('ComZeappsCrmOrderViewCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', '$uibModal', 'zeapps_modal', 'Upload',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal, Upload) {

        $scope.$parent.loadMenu("com_ze_apps_sales", "com_zeapps_crm_order");

        $scope.progress = 0;
        $scope.activities = [];
        $scope.documents = [];

        var initNavigation = function() {

            // calcul le nombre de résultat
            $scope.nb_orders = $rootScope.orders.length;


            // calcul la position du résultat actuel
            $scope.order_order = 0;
            $scope.order_first = 0;
            $scope.order_previous = 0;
            $scope.order_next = 0;
            $scope.order_last = 0;

            for (var i = 0; i < $rootScope.orders.length; i++) {
                if ($rootScope.orders[i].id == $routeParams.id) {
                    $scope.order_order = i + 1;
                    if (i > 0) {
                        $scope.order_previous = $rootScope.orders[i - 1].id;
                    }

                    if ((i + 1) < $rootScope.orders.length) {
                        $scope.order_next = $rootScope.orders[i + 1].id;
                    }
                }
            }

            // recherche la première commande de la liste
            if($rootScope.orders[0] != undefined) {
                if ($rootScope.orders[0].id != $routeParams.id) {
                    $scope.order_first = $rootScope.orders[0].id;
                }
            }
            else
            $scope.order_first = 0;

            // recherche la dernière commande de la liste
            if($rootScope.orders[$rootScope.orders.length - 1] != undefined) {
                if ($rootScope.orders[$rootScope.orders.length - 1].id != $routeParams.id) {
                    $scope.order_last = $rootScope.orders[$rootScope.orders.length - 1].id;
                }
            }
            else
                $scope.order_last = 0;


            $scope.first_order = function () {
                if ($scope.order_first != 0) {
                    $location.path("/ng/com_zeapps_crm/order/" + $scope.order_first);
                }
            };
            $scope.previous_order = function () {
                if ($scope.order_previous != 0) {
                    $location.path("/ng/com_zeapps_crm/order/" + $scope.order_previous);
                }
            };
            $scope.next_order = function () {
                if ($scope.order_next) {
                    $location.path("/ng/com_zeapps_crm/order/" + $scope.order_next);
                }
            };
            $scope.last_order = function () {
                if ($scope.order_last) {
                    $location.path("/ng/com_zeapps_crm/order/" + $scope.order_last);
                }
            };

        };

        if($rootScope.orders == undefined || $rootScope.orders[0] == undefined) {
            zhttp.crm.order.get_all().then(function (response) {
                if (response.status == 200) {
                    $rootScope.orders = response.data;

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
            zhttp.crm.order.get($routeParams.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.order = response.data.order;
                    $scope.company = response.data.company;
                    $scope.contact = response.data.contact;
                    $scope.lines = response.data.lines || [];
                    $scope.activities = response.data.activities || [];
                    $scope.documents = response.data.documents || [];

                    $scope.order.date_creation = new Date($scope.order.date_creation);
                    $scope.order.date_limit = new Date($scope.order.date_limit);

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

            var data = $scope.order;

            var formatted_data = angular.toJson(data);

            zhttp.crm.order.save(formatted_data).then(function(response){
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

        $scope.createInvoice = function(){
            zhttp.get('/com_zeapps_crm/orders/createInvoiceFrom/' + $scope.order.id).then(function(response){
                if(response.data && response.data != 'false'){
                    $location.url('/ng/com_zeapps_crm/invoice/' + response.data);
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
                zhttp.crm.order.line.position(formatted_data);
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
                        id_order: $routeParams.id,
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
                    zeHttp.post('/com_zeapps_crm/orders/saveLine', formatted_data).then(function(response){
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
                id_order: $routeParams.id,
                num: 'subTotal',
                sort: $scope.lines.length
            };

            var formatted_data = angular.toJson(subTotal);
            zhttp.crm.order.line.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    subTotal.id = response.data;
                    $scope.lines.push(subTotal);
                }
            });
        };

        $scope.addComment = function(){
            if($scope.comment != ''){
                var comment = {
                    id_order: $routeParams.id,
                    num: 'comment',
                    designation_desc: '',
                    sort: $scope.lines.length
                };
                comment.designation_desc = $scope.comment;

                var formatted_data = angular.toJson(comment);
                zhttp.crm.order.line.save(formatted_data).then(function(response){
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
            zhttp.crm.order.line.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    line.edit = false;
                }
            });
        };

        $scope.deleteLine = function(line){
            if($scope.lines.indexOf(line) > -1){
                zhttp.crm.order.line.del(line.id).then(function(response){
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
            if($scope.order) {
                for (var i = 0; i < $scope.lines.length; i++) {
                    if ($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment') {
                        total += $scope.lines[i].price_unit * $scope.lines[i].qty * ($scope.lines[i].discount / 100);
                    }
                }
                total = total + $scope.totalAvDiscountHT() * ($scope.order.global_discount / 100);
            }
            return total;
        };

        $scope.totalHT = function(){
            var total = 0;
            if($scope.order) {
                for(var i = 0; i < $scope.lines.length; i++){
                    if($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment'){
                        total += $scope.lines[i].price_unit * $scope.lines[i].qty * ( 1 - ($scope.lines[i].discount / 100) );
                    }
                }
                total = total * (1- ($scope.order.global_discount / 100) );
            }
            return total;
        };

        $scope.totalTTC = function(){
            var total = 0;
            if($scope.order) {
                for(var i = 0; i < $scope.lines.length; i++){
                    if($scope.lines[i] != undefined && $scope.lines[i].num != 'subTotal' && $scope.lines[i].num != 'comment'){
                        total += $scope.lines[i].price_unit * $scope.lines[i].qty * ( 1 - ($scope.lines[i].discount / 100) ) * ( 1 + ($scope.lines[i].taxe / 100) );
                    }
                }
                total = total * (1- ($scope.order.global_discount / 100) );
            }
            return total;
        };


        $scope.toggleActivity = function(){
            $scope.activity = {};
            $scope.activity.reminder = new Date();
            $scope.showActivityInput = !$scope.showActivityInput;
        };

        $scope.closeActivity = function(){
            $scope.showActivityInput = false;
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
                data['id_order'] = $routeParams.id;
                data['libelle'] = $scope.activity.libelle;
                data['description'] = $scope.activity.description;
                data['reminder'] = date;

                var formatted_data = angular.toJson(data);
                zhttp.crm.order.activity.save(formatted_data).then(function(response){
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
            $scope.activity = activity;
            $scope.showActivityInput = true;
        };

        $scope.deleteActivity = function(activity){
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
                    zhttp.crm.order.activity.del(activity.id).then(function (response) {
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

            $scope.files = files;
            $scope.progress = 0;

            if (files && files.length) {
                Upload.upload({
                    url: zhttp.crm.order.document.upload() + $routeParams.id,
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
                    zhttp.crm.order.document.del(document.id).then(function(response){
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
            zhttp.crm.order.pdf.make($scope.order.id).then(function(response){
                if(response.data && response.data != 'false'){
                    window.document.location.href = zhttp.crm.order.pdf.get() + angular.fromJson(response.data);
                }
            });
        }


    }]);