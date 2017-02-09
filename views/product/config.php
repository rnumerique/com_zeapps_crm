<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Config > Produits</div>
<div id="content">

    <div class="row">
        <div class="col-md-12">
            <h3>Attributs des produits</h3>
            <span class="pull-right text-success pointer" ng-click="addAttribute()"><i class="fa fa-plus"></i> Nouvel Attribut</span>
        </div>
    </div>

    <form>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-stripped table-condensed">
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
                            <th class="text-right">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="attribute in attributes track by $index">
                            <td ng-hide="form.index == $index">
                                {{ attribute.name }}
                            </td>
                            <td ng-hide="form.index == $index">
                                {{ types[attribute.type] }}
                            </td>
                            <td class="text-center" ng-hide="form.index == $index">
                                <span ng-show="attribute.required">Oui</span>
                                <span ng-hide="attribute.required">Non</span>
                            </td>
                            <td class="text-right" ng-hide="form.index == $index">
                                <i class="fa fa-pencil text-primary pointer" ng-click="edit(attribute)" ng-hide="form.index != undefined"></i>
                                <i class="fa fa-trash text-danger pointer" ng-click="del($index)"></i>
                            </td>

                            <td ng-show="form.index == $index">
                                <input type="text" class="form-control input-sm" ng-model="form.name">
                            </td>
                            <td ng-show="form.index == $index">
                                <select class="form-control input-sm" ng-model="form.type">
                                    <option value="{{ value }}" ng-repeat="(value, name) in types">{{ name }}</option>
                                </select>
                            </td>
                            <td class="text-center" ng-show="form.index == $index">
                                <input type="checkbox" ng-model="form.required">
                            </td>
                            <td class="text-right" ng-show="form.index == $index">
                                <i class="fa fa-check text-success pointer" ng-click="validate(attribute)"></i>
                                <i class="fa fa-times text-danger pointer" ng-click="cancel()"></i>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <form-buttons></form-buttons>
    </form>
</div>