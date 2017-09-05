<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Libellé</label>
            <input type="text" ng-model="form.name" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Type</label>
            <select class="form-control" ng-model="form.type">
                <option value="text">Texte</option>
                <option value="number">Numérique</option>
                <option value="textarea">Texte Long</option>
                <option value="checkbox">Booléen</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>
                <input type="checkbox" ng-model="form.required">
                obligatoire
            </label>
        </div>
    </div>
</div>