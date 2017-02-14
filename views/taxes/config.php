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
            <input type="text" class="form-control" ng-model="form.accounting_number" ng-required="true">
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
                    Label
                </th>
                <th>
                    Taux (%)
                </th>
                <th>
                    Compte Comptable
                </th>
                <th>
                    Active
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="taxe in taxes">
                <td>
                    {{ taxe.label }}
                </td>
                <td>
                    {{ taxe.value }}
                </td>
                <td>
                    {{ taxe.accounting_number }}
                </td>
                <td>
                    <i class="fa fa-fw" ng-class="taxe.active == 1 ? 'fa-check text-success' : 'fa-times text-danger'"></i>
                </td>
                <td class="text-right">
                    <button type="button" class="btn btn-xs btn-info" ng-click="edit(taxe)">
                        <i class="fa fa-fw fa-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" ng-click="delete(taxe)">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <form-buttons></form-buttons>
    </form>

</div>