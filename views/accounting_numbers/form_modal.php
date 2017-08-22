<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="ComZeappsCrmAccountingNumberFormCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Libellé</label>
                <input type="text" class="form-control" ng-model="form.label">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Numéro</label>
                <input type="text" class="form-control" ng-model="form.number">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Type de compte comptable</label>
                <select class="form-control" ng-model="form.type" ng-change="updateType()">
                    <option ng-repeat="type in types" value="type.id">
                        {{ type.label }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</div>