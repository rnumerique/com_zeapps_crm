<div ng-controller="ZeAppsCrmModalInvoiceActivityCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Titre</label>
                <input type="text" class="form-control" ng-model="form.libelle">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Type</label>
                <select class="form-control" ng-model="form.id_type" ng-change="updateType()">
                    <option ng-repeat="type in activity_types" value="{{type.id}}">
                        {{type.label}}
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Echéance</label>
                <input type="date" class="form-control" ng-model="form.deadline">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Statut</label>
                <select class="form-control" ng-model="form.status">
                    <option value="A faire">A faire</option>
                    <option value="Terminé">Terminé</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <textarea class="form-control" ng-model="form.description" rows="10" placeholder="Description..."></textarea>
            </div>
        </div>
    </div>
</div>