<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Config > Entrepôts</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <h3>Entrepôts</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form>
                <table class="table table-responsive table-condensed table-stripped">
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
                    <tr>
                        <th>
                            <input type="text" ng-model="newLine.label" class="form-control">
                        </th>
                        <th class="text-center">
                            <input type="number" ng-model="newLine.resupply_delay" class="form-control">
                        </th>
                        <th class="text-center">
                            <select ng-model="newLine.resupply_unit" class="form-control">
                                <option value="hours">Heures</option>
                                <option value="days">Jours</option>
                                <option value="weeks">Semaines</option>
                                <option value="months">Mois</option>
                            </select>
                        </th>
                        <td class="text-right">
                            <button type="button" class="btn btn-success btn-xs" ng-click="createLine()">
                                <i class="fa fa-fw fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-xs" ng-click="cancelLine()">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </td>
                    </tr>
                    <tr ng-repeat="warehouse in form.warehouses">
                        <td>
                            <input type="text" ng-model="warehouse.label" class="form-control">
                        </td>
                        <td class="text-right">
                            <input type="number" ng-model="warehouse.resupply_delay" class="form-control">
                        </td>
                        <td class="text-left">
                            <select ng-model="warehouse.resupply_unit" class="form-control">
                                <option value="hours">Heures</option>
                                <option value="days">Jours</option>
                                <option value="weeks">Semaines</option>
                                <option value="months">Mois</option>
                            </select>
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