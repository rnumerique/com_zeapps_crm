<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Stocks</div>
<div id="content">

    <div class="row">
        <div class="col-md-10">
            <select class="form-control" ng-change="updateWarehouse()" ng-model="selectedWarehouse">
                <option value="0">Tous</option>
                <option ng-repeat="warehouse in warehouses" value="{{warehouse.id}}">
                    {{warehouse.label}}
                </option>
            </select>
        </div>
        <div class="col-md-2 text-center">
            <a href="/ng/com_zeapps_contact/contacts/new" class="btn btn-success btn-xs">
                <i class="fa fa-fw fa-plus"></i> Produit stocké
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <span ng-click="shownFilter = !shownFilter">
                <i class="fa fa-filter"></i> Filtres <i class="fa" ng-class="shownFilter ? 'fa-caret-up' : 'fa-caret-down'"></i>
            </span>
        </div>
    </div>

    <div class="well" ng-if="shownFilter">
        <div class="row">
            <div class="col-md-12">
                <div class="checkbox">
                    <label>
                        <input type="checkbox">
                        Afficher les stocks à 0
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-condensed table-responsive" ng-show="product_stocks.length">
                <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Qté</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="product_stock in product_stocks">
                    <td><a href="/ng/com_zeapps_crm/stock/{{product_stock.id_stock}}">{{product_stock.label}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/stock/{{product_stock.id_stock}}">{{product_stock.total || 0}}</a></td>
                    <td class="text-right">
                        <button type="button" class="btn btn-danger btn-xs" ng-click="delete(product_stock)">
                            <i class="fa fa-fw fa-trash"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>