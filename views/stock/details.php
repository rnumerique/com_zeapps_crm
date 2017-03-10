<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Stocks</div>
<div id="content">
    <div class="row">
        <div class="col-md-12" ng-hide="shownForm">
            <h3>
                {{ product_stock.ref ? product_stock.ref + ' - ' : '' }}{{product_stock.label }}
                <button type="button" class="btn btn-xs btn-info">
                    <i class="fa fa-fw fa-pencil" ng-click="edit()"></i>
                </button>
            </h3>
        </div>
    </div>
    <div class="well" ng-show="shownForm">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Référence</label>
                    <input class="form-control" type="text" ng-model="form.ref">
                </div>
            </div>
            <div class="col-md-7">
                <div class="form-group">
                    <label>Libellé</label>
                    <input class="form-control" type="text" ng-model="form.label">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Valeur unitaire</label>
                    <input class="form-control" type="number" ng-model="form.value_ht">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <button type="button" class="btn btn-sm btn-default" ng-click="cancel()">
                    Annuler
                </button>
                <button type="button" class="btn btn-sm btn-success" ng-click="success()">
                    Modifier
                </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 form-inline">
            <div class="form-group">
                <label>Entrepôt</label>
                <select class="form-control" ng-change="updateWarehouse()" ng-model="$root.selectedWarehouse">
                    <option value="0">Stock Global</option>
                    <option ng-repeat="warehouse in warehouses" value="{{warehouse.id}}">
                        {{warehouse.label}}
                    </option>
                </select>
            </div>
        </div>
    </div>
    <table class="col-xs-12 text-center postits">
        <tr>
            <td>
                <div class="postit">
                    <h3 ng-class="product_stock.total < 0 ? 'text-danger' : ''">
                        {{ product_stock.total || 0 | number:2 }}
                    </h3>
                    <h5>Stock</h5>
                </div>
            </td>
            <td>
                <div class="postit">
                    <h3>
                        {{ product_stock.value_ht | currency }}
                    </h3>
                    <h5>Valeur Unitaire</h5>
                </div>
            </td>
            <td>
                <div class="postit">
                    <h3>
                        {{ product_stock.value_ht * product_stock.total | currency }}
                    </h3>
                    <h5>Valeur totale du stock</h5>
                </div>
            </td>
            <td>
                <div class="postit">
                    <h3 ng-class="product_stock.classRupture">
                        {{ product_stock.timeleft }}
                        <small>{{ product_stock.dateRupture ? ' (' +  product_stock.dateRupture + ')' : '' }}</small>
                    </h3>
                    <h5>Date prévisionnelle de rupture</h5>
                </div>
            </td>
            <td ng-if="selectedWarehouse > 0">
                <div class="postit">
                    <h3 ng-class="product_stock.classResupply">
                        {{ product_stock.timeResupply }}
                        <small>{{ product_stock.dateResupply ? ' (' +  product_stock.dateResupply + ')' : '' }}</small>
                    </h3>
                    <h5>Commande fournisseur avant</h5>
                </div>
            </td>
        </tr>
    </table>

    <ul class="nav nav-tabs">
        <li ng-class="navigationState === 'chart' ? 'active' : ''">
            <a href="#" ng-click="navigationState = 'chart'">Graphique</a>
        </li>
        <li ng-class="navigationState === 'history' ? 'active' : ''">
            <a href="#" ng-click="navigationState = 'history'">Historique</a>
        </li>
    </ul>

    <div ng-include="'/com_zeapps_crm/stock/' + navigationState"></div>
</div>