<div class="modal-header">
    <h3 class="modal-title">{{titre}}</h3>
</div>


<div class="modal-body">
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

<div class="modal-footer">
    <button class="btn btn-danger" type="button" ng-click="cancel()" i8n="Annuler"></button>
    <button class="btn btn-success" type="button" ng-click="save()" i8n="Sauvegarder"></button>
</div>