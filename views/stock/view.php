<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Stocks</div>
<div id="content">
<form>

    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>

            <ze-btn fa="plus" hint="Produit stocké" always-on="true" color="success"
                    ze-modalform="add"
                    data-template="templateStock"
                    data-title="Créer un nouveau produit stocké"></ze-btn>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-condensed table-responsive" ng-show="product_stocks.length">
                <thead>
                <tr>
                    <th>Ref</th>
                    <th>Libellé</th>
                    <th class="text-right">Qté</th>
                    <th class="text-right">Valeur Unitaire</th>
                    <th class="text-right">Valeur du Stock</th>
                    <th class="text-right">Date Rupture</th>
                    <th class="text-right" ng-if="filter_model.id_warehouse">Date Réapprovisionnement estimée</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="product_stock in product_stocks">
                    <td ng-click="goTo(product_stock.id_stock)">
                        {{product_stock.ref}}
                    </td>
                    <td ng-click="goTo(product_stock.id_stock)">
                        {{product_stock.label}}
                    </td>
                    <td class="text-right" ng-click="goTo(product_stock.id_stock)">
                        <span ng-class="product_stock.total < 0 ? 'text-danger' : (product_stock.total > 0 ? 'text-success' : 'text-info')">
                            {{product_stock.total || 0 | number:2}}
                        </span>
                    </td>
                    <td class="text-right" ng-click="goTo(product_stock.id_stock)">
                        {{product_stock.value_ht | currency:'€':2}}
                    </td>
                    <td class="text-right" ng-click="goTo(product_stock.id_stock)">
                        {{product_stock.value_ht * product_stock.total | currency:'€':2}}
                    </td>
                    <td class="text-right" ng-click="goTo(product_stock.id_stock)">
                        <span ng-class="product_stock.classRupture">
                            {{ product_stock.timeleft }}{{ product_stock.dateRupture ? ' (' +  product_stock.dateRupture + ')' : '' }}
                        </span>
                    </td>
                    <td class="text-right" ng-click="goTo(product_stock.id_stock)" ng-if="filter_model.id_warehouse">
                        <span ng-class="product_stock.classResupply">
                            {{ product_stock.timeResupply }}{{ product_stock.dateResupply ? ' (' +  product_stock.dateResupply + ')' : '' }}
                        </span>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(product_stock)" ze-confirmation></ze-btn>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

</form>
</div>