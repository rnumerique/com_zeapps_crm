app.run(function(zeHttp, $rootScope){
	zeHttp.crm.modality.get_all().then(function(response){
		if(response.data && response.data != "false"){
			$rootScope.modalities = response.data;
			angular.forEach($rootScope.modalities, function(modality){
				modality.sort = parseInt(modality.sort);
			});
		}
	});
	zeHttp.crm.taxe.get_all().then(function(response){
		if(response.data && response.data != "false"){
			$rootScope.taxes = response.data;
			angular.forEach($rootScope.taxes, function(taxe){
				taxe.value = parseFloat(taxe.value);
			});
		}
	});
});