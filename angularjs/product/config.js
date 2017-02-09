app.controller('ComZeappsCrmProductConfigCtrl', ['$scope', '$route', '$routeParams', '$location', '$rootScope', 'zeHttp', 'zeapps_modal',
    function ($scope, $route, $routeParams, $location, $rootScope, zhttp, zeapps_modal) {

        $scope.$parent.loadMenu("com_ze_apps_config", "com_ze_apps_products");

        $scope.attributes = [];
        $scope.form = {};

        $scope.types = {
            'text': 'Texte',
            'number': 'Numérique',
            'textarea': 'Texte Long',
            'checkbox': 'Booléen'
        };

        zhttp.config.product.get.attr().then(function(response){
            if(response.data && response.data != 'false'){
                $scope.attributes = angular.fromJson(response.data.value);
            }
        });

        $scope.addAttribute = function(){
            var attribute = {
                name: 'Nouvel Attribut',
                type: 'Texte',
                required: false
            };
            $scope.attributes.push(attribute);
        };

        $scope.edit = function(attribute){
            $scope.form.name = attribute.name;
            $scope.form.type = attribute.type;
            $scope.form.required = attribute.required;
            $scope.form.index = $scope.attributes.indexOf(attribute);
        };

        $scope.validate = function(attribute){
            attribute.name = $scope.form.name;
            attribute.type = $scope.form.type;
            attribute.required = $scope.form.required;
            $scope.form = {};
        };

        $scope.cancel = function(){
            $scope.form = {};
        };

        $scope.del = function(index){
            $scope.attributes.splice(index, 1);
        };

        $scope.success = function(){

            var data = {};

            data['id'] = 'crm_product_attributes';
            data['value'] = angular.toJson($scope.attributes);

            var formatted_data = angular.toJson(data);
            zhttp.config.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.toasts.push({'success':'Les attributs ont bien été sauvegardés'})
                }
            })
        };

    }]);