<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Config > devis</div>
<div id="content">
    <h4>Listes des modalités de paiement</h4>
    <form>

        <div class="">
            <label ng-if="!form.id">Ajouter une modalité</label>
            <label ng-if="form.id">Editer la modalité</label>
            <input type="text" class="form-control" ng-model="form.label" ng-required="true">
        </div>

        <table class="table table-stripped table-condensed">
            <thead>
            <tr>
                <th>
                    Label
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="modality in modalities">
                <td>
                    {{ modality.label }}
                </td>
                <td class="text-right">
                    <button type="button" class="btn btn-xs btn-info" ng-click="edit(modality)">
                        <i class="fa fa-fw fa-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" ng-click="delete(modality)">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <form-buttons></form-buttons>
    </form>

</div>