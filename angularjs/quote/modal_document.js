// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"form_document",
	templateUrl:"/com_zeapps_crm/quotes/modal_document",
	controller:"ZeAppsCrmModalQuoteDocumentCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Ajouter un document";
		}
	}
});


app.controller("ZeAppsCrmModalQuoteDocumentCtrl", function($scope, $uibModalInstance, zeHttp, titre, option) {

    $scope.titre = titre ;

    $scope.upload = upload;
    $scope.cancel = cancel;
    $scope.save = save;

    if(option.document){
        $scope.form = option.document;
        $scope.titre = "Modifier un document";
    }
    else{
        $scope.form = {};
    }

    function upload(files) {
    	$scope.form.files = files;

    	if(!$scope.form.label && $scope.form.files[0]){
    	    $scope.form.label = $scope.form.files[0].name;
        }
    }

    function cancel() {
        $uibModalInstance.dismiss("cancel");
    }

    function save() {
        $uibModalInstance.close($scope.form);
    }

}) ;