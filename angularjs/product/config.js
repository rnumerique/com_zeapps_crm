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

        $scope.addAttribute = addAttribute;
        $scope.edit = edit;
        $scope.validate = validate;
        $scope.cancel = cancel;
        $scope.del = del;
        $scope.success = success;


        zhttp.config.product.get.attr().then(function(response){
            if(response.data && response.data != 'false'){
                $scope.attributes = angular.fromJson(response.data.value);
            }
        });



        function addAttribute(){
            var attribute = {
                name: 'Nouvel Attribut',
                type: 'Texte',
                required: false
            };
            $scope.attributes.push(attribute);
        }

        function edit(attribute){
            $scope.form.name = attribute.name;
            $scope.form.type = attribute.type;
            $scope.form.required = attribute.required;
            $scope.form.index = $scope.attributes.indexOf(attribute);
        }

        function validate(attribute){
            attribute.name = $scope.form.name;
            attribute.type = $scope.form.type;
            attribute.required = $scope.form.required;
            $scope.form = {};
        }

        function cancel(){
            $scope.form = {};
        }

        function del(index){
            $scope.attributes.splice(index, 1);
        }

        function success(){

            var data = {};

            data['id'] = 'crm_product_attributes';
            data['value'] = angular.toJson($scope.attributes);

            var formatted_data = angular.toJson(data);
            zhttp.config.save(formatted_data).then(function(response){
                if(response.data && response.data != 'false'){
                    $rootScope.toasts.push({'success':'Les attributs ont bien été sauvegardés'})
                }
            })
        }

    }]);