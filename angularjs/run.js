app.run(function(zeHttp, $rootScope){
    zeHttp.crm.modality.get_all().then(function(response){
        if(response.data && response.data != 'false'){
            $rootScope.modalities = response.data;
        }
    });
});