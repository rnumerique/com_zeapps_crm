app.controller("ZeAppsCrmModalQuoteDocumentCtrl", function($scope) {

    $scope.upload = upload;

    function upload(files) {
        $scope.form.files = files;

        if(!$scope.form.label && $scope.form.files[0]){
            $scope.form.label = $scope.form.files[0].name;
        }
    }
}) ;