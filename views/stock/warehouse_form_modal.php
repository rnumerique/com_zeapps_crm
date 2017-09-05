<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>Libellé</label>
            <input type="text" ng-model="form.label" class="form-control">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Estimation du temps de réapprovisionnement</label>
            <input type="number" ng-model="form.resupply_delay" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label></label>
            <select ng-model="form.resupply_unit" class="form-control">
                <option value="hours">Heures</option>
                <option value="days">Jours</option>
                <option value="weeks">Semaines</option>
                <option value="months">Mois</option>
            </select>
        </div>
    </div>
</div>