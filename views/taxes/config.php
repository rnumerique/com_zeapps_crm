<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Config > devis</div>
<div id="content">
    <h4>Listes des taxes</h4>
    <form>

        <div class="col-md-4">
            <label ng-if="!form.id">Ajouter une taxe</label>
            <label ng-if="form.id">Editer la taxe</label>
            <input type="text" class="form-control" ng-model="form.label" ng-required="true">
        </div>
        <div class="col-md-4">
            <label>Taux</label>
            <input type="number" class="form-control" ng-model="form.value" ng-required="true">
        </div>
        <div class="col-md-4">
            <label>Compte Comptable</label>
            <span   ze-modalsearch="loadAccountingNumber"
                    data-http="accountingNumberHttp"
                    data-model="form.accounting_number"
                    data-fields="accountingNumberFields"
                    data-template-new="accountingNumberTplNew"
                    data-title="Choisir un compte comptable"></span>
        </div>
        <div class="col-md-4">
            <label>
                <input type="checkbox" ng-model="form.active">
                Active
            </label>
        </div>

        <table class="table table-stripped table-condensed">
            <thead>
            <tr>
                <th>
                    Compte Comptable
                </th>
                <th>
                    Label
                </th>
                <th class="text-right">
                    Taux
                </th>
                <th class="text-center">
                    Active
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="taxe in taxes">
                <td>
                    {{ taxe.accounting_number }}
                </td>
                <td>
                    {{ taxe.label }}
                </td>
                <td class="text-right">
                    {{ taxe.value | currency:'%':2 }}
                </td>
                <td class="text-center">
                    <i class="fa fa-fw" ng-class="taxe.active == 1 ? 'fa-check text-success' : 'fa-times text-danger'"></i>
                </td>
                <td class="text-right">
                    <ze-btn fa="pencil" color="info" hint="Editer" direction="left" ng-click="edit(taxe)"></ze-btn>
                    <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(taxe)"></ze-btn>
                </td>
            </tr>
            </tbody>
        </table>

        <form-buttons></form-buttons>
    </form>

</div>