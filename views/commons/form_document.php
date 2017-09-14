<div ng-controller="ZeAppsCrmModalFormDocumentCtrl">
    <button type="button" class="btn btn-xs btn-success" ngf-select="upload($files)" >
        Choisissez un document
    </button>
    <div class="form-group">
        <label>Titre du document</label>
        <input type="text" class="form-control" ng-model="form.label">
    </div>
    <div class="form-group">
        <textarea class="form-control" ng-model="form.description" rows="6" placeholder="Description..."></textarea>
    </div>
</div>