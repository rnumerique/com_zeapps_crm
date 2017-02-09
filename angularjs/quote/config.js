app.controller('ComZeappsCrmQuoteConfigCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_quotes");

        $scope.format = '';
        $scope.frequency = '';

        zhttp.config.quote.get.format().then(function(response){
            if(response.data && response.data != 'false'){
                $scope.format = response.data.value;
            }
        });

        zhttp.config.quote.get.frequency().then(function(response){
            if(response.data && response.data != 'false'){
                $scope.frequency = response.data.value;
            }
        });

        $scope.success = function(){

            var data = {};

            data[0] = {
                id: 'crm_quote_format',
                value: $scope.format
            };
            data[1] = {
                id: 'crm_quote_frequency',
                value: $scope.frequency
            };

            var formatted_data = angular.toJson(data);
            zhttp.config.save(formatted_data);

        };

        $scope.test = function(){
            var data = {};

            data['format'] = $scope.format;
            data['frequency'] = $scope.frequency;

            var formatted_data = angular.toJson(data);
            zhttp.crm.quote.test(formatted_data).then(function(response){
                if(response.data && response.data != false){
                    $scope.result = angular.fromJson(response.data);
                }
            });
        }

    }]);