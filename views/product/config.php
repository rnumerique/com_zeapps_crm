<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Config > Produits</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <h3>Attributs des produits</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form>

                <table class="table table-responsive table-stripped table-condensed">
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
                    <tr>
                        <th>
                            <input type="text" ng-model="newLine.name" class="form-control">
                        </th>
                        <th class="text-center">
                            <select class="form-control input-sm" ng-model="newLine.type">
                                <option value="{{ value }}" ng-repeat="(value, name) in types">{{ name }}</option>
                            </select>
                        </th>
                        <th class="text-center">
                            <input type="checkbox" ng-model="newLine.required">
                        </th>
                        <td class="text-right">
                            <button type="button" class="btn btn-success btn-xs" ng-click="createLine()">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-xs" ng-click="cancelLine()">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </td>
                    </tr>
                        <tr ng-repeat="attribute in form.attributes track by $index">
                            <td>
                                <input type="text" class="form-control input-sm" ng-model="attribute.name">
                            </td>
                            <td>
                                <select class="form-control input-sm" ng-model="attribute.type">
                                    <option value="{{ value }}" ng-repeat="(value, name) in types">{{ name }}</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" ng-model="attribute.required">
                            </td>
                            <td class="text-right">
                                <button type="button" class="btn btn-danger btn-xs" ng-click="delete($index)">
                                    <i class="fa fa-fw fa-trash"></i>
                                </button>
                            </td>

                        </tr>
                    </tbody>
                </table>

                <form-buttons></form-buttons>
            </form>
        </div>
    </div>
</div>