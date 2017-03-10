<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Config > devis</div>
<div id="content">

    <a class="btn btn-xs btn-success pull-right" href="/ng/com_zeapps/modalities/new/">
        <i class="fa fa-fw fa-plus"></i> modalité
    </a>

    <h4>Listes des modalités de paiement</h4>

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
        <tr ng-repeat="modality in modalities | orderBy:'sort'">
            <td>
                {{ modality.label }}
            </td>
            <td class="text-right">
                <a class="btn btn-xs btn-info" href="/ng/com_zeapps/modalities/edit/{{modality.id}}">
                    <i class="fa fa-fw fa-pencil"></i>
                </a>
                <button type="button" class="btn btn-xs btn-danger" ng-click="delete(modality)">
                    <i class="fa fa-fw fa-trash"></i>
                </button>
            </td>
        </tr>
        </tbody>
    </table>

</div>