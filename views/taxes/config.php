<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Liste des taxes</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <ze-btn fa="plus" color="success" hint="Taxe" always-on="true"
                    ze-modalform="add"
                    data-template="templateForm"
                    data-title="Ajouter une nouvelle taxe"></ze-btn>
        </div>
    </div>

    <table class="table table-hover table-condensed">
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
        <tr ng-repeat="taxe in $root.taxes">
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
                <ze-btn fa="pencil" color="info" hint="Editer" direction="left"
                        ze-modalform="edit"
                        data-edit="taxe"
                        data-template="templateForm"
                        data-title="Modifier la taxe"></ze-btn>
                <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(taxe)" ze-confirmation></ze-btn>
            </td>
        </tr>
        </tbody>
    </table>
</div>