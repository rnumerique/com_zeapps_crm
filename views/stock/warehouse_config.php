<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Entrepôts</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <ze-btn fa="plus" color="success" hint="Entrepôt" always-on="true"
                    ze-modalform="add"
                    data-template="templateForm"
                    data-title="Ajouter un entrepôt"></ze-btn>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-responsive table-condensed table-hover">
                <thead>
                <tr>
                    <th>
                        Libellé
                    </th>
                    <th class="text-right">
                        Estimation du temps
                    </th>
                    <th class="text-left">
                        de réapprovisionnement
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="warehouse in warehouses">
                    <td>
                        {{warehouse.label}}
                    </td>
                    <td class="text-right">
                        {{warehouse.resupply_delay}}
                    </td>
                    <td class="text-left">
                        {{resupply_label[warehouse.resupply_unit]}}
                    </td>
                    <td class="text-right">
                        <ze-btn fa="pencil" color="info" hint="Editer" direction="left"
                                ze-modalform="edit"
                                data-edit="warehouse"
                                data-template="templateForm"
                                data-title="Modifier l'entrepôt"></ze-btn>
                        <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(warehouse)" ze-confirmation></ze-btn>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>