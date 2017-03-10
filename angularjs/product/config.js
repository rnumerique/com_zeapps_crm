app.controller('ComZeappsCrmProductConfigCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_products");

        var attributes = [];

        $scope.form = {};
        $scope.newLine = {};
        $scope.types = {
            'text': 'Texte',
            'number': 'Numérique',
            'textarea': 'Texte Long',
            'checkbox': 'Booléen'
        };

        $scope.createLine = createLine;
        $scope.cancelLine = cancelLine;
        $scope.cancel = cancel;
        $scope.delete = del;
        $scope.success = success;


        zhttp.config.product.get.attr().then(function(response){
            if(response.data && response.data != 'false'){
                attributes = angular.fromJson(response.data.value);

                angular.forEach(attributes, function(attribute){
                    attribute.required = attribute.required == "true";
                });

                $scope.form.attributes = angular.fromJson(response.data.value);
            }
        });

        function createLine(){
            var tmp = angular.fromJson(angular.toJson(attributes));

            tmp.push($scope.newLine);

            var formatted_data = angular.toJson(tmp);
            zhttp.config.product.save.attr(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.newLine.id = response.data;
                    $scope.form.attributes.push(angular.fromJson(angular.toJson($scope.newLine)));
                    attributes.push($scope.newLine);
                    $scope.newLine = {};
                }
            });
        }

        function cancelLine(){
            $scope.newLine = {};
        }

        function cancel(){
            $scope.form.attributes = angular.fromJson(angular.toJson(attributes));
        }

        function del(index){
            var tmp = angular.fromJson(angular.toJson(attributes));

            tmp.splice(index, 1);

            var formatted_data = angular.toJson(tmp);
            zhttp.config.product.save.attr(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    $scope.form.attributes.splice(index, 1);
                    attributes.splice(index, 1);
                }
            });
        }

        function success(){
            var formatted_data = angular.toJson($scope.form.attributes);
            zhttp.config.product.save.attr(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    attributes = angular.fromJson(angular.toJson($scope.form.attributes));
                }
            });
        }

    }]);