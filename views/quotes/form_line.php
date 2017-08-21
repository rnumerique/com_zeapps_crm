<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>Désignation</label>
            <input type="text" class="form-control" ng-model="form.designation_title">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" ng-model="form.designation_desc" rows="3"></textarea>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Quantité</label>
            <input type="number" class="form-control" ng-model="form.qty">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Prix Unit. HT</label>
            <div class="input-group">
                <input type="number" class="form-control" ng-model="form.price_unit">
                <div class="input-group-addon">€</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Taxe</label>
            <select ng-model="form.taxe" class="form-control">
                <option ng-repeat="taxe in taxes | filter:{ active : 1 }" value="{{taxe.value}}">
                    {{ taxe.label }}
                </option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Remise</label>
            <div class="input-group">
                <input type="number" class="form-control" ng-model="form.discount">
                <div class="input-group-addon">%</div>
            </div>
        </div>
    </div>
</div>