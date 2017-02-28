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
                    <i class="fa fa-fw fa-pencil" ng-click="shownForm = !shownForm"></i>
                </button>
            </h3>
        </div>
    </div>
    <div class="well" ng-show="shownForm">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Référence</label>
                    <input class="form-control" type="text" ng-model="product_stock.ref">
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label>Libellé</label>
                    <input class="form-control" type="text" ng-model="product_stock.label">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-sm btn-success" ng-click="success()">
                        Modifier
                    </button>
                </div>
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
    <div class="row">
        <div class="col-md-12">
            <strong>Date prévu de rupture de stocks : </strong>{{ product_stock.timeleft }}{{ product_stock.dateRupture ? ' (' +  product_stock.dateRupture + ')' : '' }}
        </div>
    </div>
    <div class="row" ng-if="selectedWarehouse > 0">
        <div class="col-md-12">
            <strong>Date limite de réapprivisionnement estimée pour cet entrepôt : </strong>{{ product_stock.timeResupply }}{{ product_stock.dateResupply ? ' (' +  product_stock.dateResupply + ')' : '' }}
        </div>
    </div>

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