<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="ComZeappsCrmTaxeConfigFormModalCtrl">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Libellé</label>
                <input type="text" class="form-control" ng-model="form.label" ng-required="true">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Compte Comptable</label>
                <span   ze-modalsearch="loadAccountingNumber"
                        data-http="accountingNumberHttp"
                        data-model="form.accounting_number"
                        data-fields="accountingNumberFields"
                        data-template-new="accountingNumberTplNew"
                        data-title="Choisir un compte comptable"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Taux</label>
                <input type="number" class="form-control" ng-model="form.value" ng-required="true">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>
                    <input type="checkbox" ng-model="form.active">
                    Active
                </label>
            </div>
        </div>
    </div>
</div>