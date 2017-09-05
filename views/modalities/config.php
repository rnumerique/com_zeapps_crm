<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">
    Listes des modalités de paiement
</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <ze-btn fa="plus" color="success" hint="Modalité de paiement" always-on="true"
                    ze-modalform="add"
                    data-template="templateForm"
                    data-title="Ajouter une nouvelle modalité de paiement"></ze-btn>
        </div>
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
        <tr ng-repeat="modality in $root.modalities | orderBy:'sort'">
            <td>
                {{ modality.label }}
            </td>
            <td class="text-right">
                <ze-btn fa="pencil" color="info" hint="Editer" direction="left"
                        ze-modalform="edit"
                        data-edit="modality"
                        data-template="templateForm"
                        data-title="Modifier la modalité de paiement"></ze-btn>
                <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(modality)" ze-confirmation></ze-btn>
            </td>
        </tr>
        </tbody>
    </table>
</div>