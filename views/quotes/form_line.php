<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="row" ng-controller="ComZeappsCrmQuoteFormLineCtrl">
    <div class="col-md-4">
        <div class="form-group">
            <label>Référence</label>
            <input type="text" class="form-control" ng-model="form.ref">
        </div>
    </div>
    <div class="col-md-8">
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
            <select ng-model="form.id_taxe" class="form-control" ng-change="updateTaxe()">
                <option ng-repeat="taxe in taxes | filter:{ active : 1 }" value="{{taxe.id}}">
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
    <div class="col-md-6">
        <div class="form-group">
            <label>Compte comptable</label>
            <span   ze-modalsearch="loadAccountingNumber"
                    data-http="accountingNumberHttp"
                    data-model="form.accounting_number"
                    data-fields="accountingNumberFields"
                    data-template-new="accountingNumberTplNew"
                    data-title="Choisir un compte comptable"></span>
        </div>
    </div>
</div>