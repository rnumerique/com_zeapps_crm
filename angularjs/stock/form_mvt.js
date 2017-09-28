app.controller("ComZeAppsCrmStockMvtFormCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "$uibModal", "zeapps_modal",
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, $uibModal, zeapps_modal) {
        $scope.form.date_mvt = new Date();
    }]);