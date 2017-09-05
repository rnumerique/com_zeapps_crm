<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Attributs des produits</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <ze-btn fa="plus" color="success" hint="Attribut" always-on="true"
                    ze-modalform="add"
                    data-template="templateForm"
                    data-title="Ajouter un attribut"></ze-btn>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-responsive table-hover table-condensed">
                <thead>
                <tr>
                    <th>
                        Nom
                    </th>
                    <th>
                        Type
                    </th>
                    <th class="text-center">
                        Obligatoire
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="attribute in attributes">
                    <td>
                        {{attribute.name}}
                    </td>
                    <td>
                        {{attribute.type}}
                    </td>
                    <td class="text-center">
                        <i class="fa fa-fw" ng-class="attribute.required ? 'fa-check text-success' : 'fa-times text-danger'"></i>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="pencil" color="info" hint="Editer" direction="left"
                                ze-modalform="edit"
                                data-edit="attribute"
                                data-template="templateForm"
                                data-title="Modifier l'attribut"></ze-btn>
                        <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(attribute)" ze-confirmation></ze-btn>
                    </td>

                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>