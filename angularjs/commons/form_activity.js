app.controller("ZeAppsCrmModalFormActivityCtrl", ["$scope", "zeHttp", function($scope, zhttp) {
    $scope.activity_types = [];

    $scope.updateType = updateType;

    zhttp.crm.activity_types.all().then(function(response){
        if(response.data && response.data != "false"){
            $scope.activity_types = response.data.activity_types;
        }
    });

    if($scope.form.id){
        $scope.form.deadline = new Date($scope.form.deadline);
    }
    else{
        $scope.form.status = "A faire";
        $scope.form.deadline = new Date();
    }

    function updateType(){
        angular.forEach($scope.activity_types, function(type){
            if(type.id === $scope.form.id_type){
                $scope.form.label_type = type.label;
            }
        });
    }
}]) ;