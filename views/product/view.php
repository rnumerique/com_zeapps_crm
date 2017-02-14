<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Produits</div>
<div id="content">

    <div class="row">
        <div class="col-md-3">
            <div class="root">
                <zeapps-happylittletree
                    data-tree="tree.branches"
                    data-active-branch="activeCategory"
                </zeapps-happylittletree>
            </div>
        </div>

        <div class="col-md-9">
            <div class="clearfix">
                <div class="pull-right" ng-show="activeCategory.data.id != -1">
                    <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/product/new_category/{{ activeCategory.data.id || 0 }}'><span class='fa fa-plus' aria-hidden='true'></span> Sous-categorie</a>
                    <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/product/new_product/{{ activeCategory.data.id || 0 }}'><span class='fa fa-plus' aria-hidden='true'></span> Produit</a>
                    <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/product/new_product_compose/{{ activeCategory.data.id || 0 }}'><span class='fa fa-plus' aria-hidden='true'></span> Produit Compose</a>
                </div>
                <h3 class="text-capitalize active-category-title">
                    {{ activeCategory.data.name }}
                    <a class="no-deco faded" ng-href="/ng/com_zeapps_crm/product/category/{{ activeCategory.data.id }}/edit" ng-show="activeCategory.data.id > 0">
                        <span class="fa fa-pencil text-primary"></span>
                    </a>
                    <span class="fa fa-trash text-danger pointer faded" ng-click="delete_category(activeCategory.data.id)" ng-show="activeCategory.data.id > 0"></span>
                </h3>
                <div class="row" ng-show="activeCategory.data.branches">
                    <h5>Sous-Categories</h5>
                    <ul ui-sortable="sortableOptions" id="sortable" class="branch-list list-unstyled col-md-4" ng-model="activeCategory.data.branches">
                        <li id="{{ branch.id }}" class="branch branch-sortable" ng-repeat="branch in activeCategory.data.branches">
                            <span class="glyphicon glyphicon-resize-vertical" aria-hidden="true"></span>
                            {{ branch.name }} <i>({{ branch.nb_products }} produit<span ng-show="branch.nb_products > 1">s</span>)</i>
                        </li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-md-3 pull-right">
                        <input class="form-control" type="text" ng-model="quicksearch" placeholder="Recherche rapide">
                    </div>
                    <h5>Produits</h5>
                    <table class="table table-striped">
                        <tr>
                            <th></th>
                            <th i8n="Référence"></th>
                            <th i8n="Nom du produit"></th>
                            <th i8n="Prix HT"></th>
                            <th>TVA (%)</th>
                            <th>Compte comptable</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        <tr class="leaf" ng-repeat="product in products | filter:quicksearch | orderBy: 'name'">
                            <td>
                                <i class="fa fa-tag" ng-if="product.compose == 0"></i>
                                <i class="fa fa-tags" ng-if="product.compose != 0"></i>
                            </td>
                            <td><a ng-href="/ng/com_zeapps_crm/{{ product.compose == 0 ? 'product' : 'product_compose' }}/{{ product.id }}">{{ product.ref }}</a></td>
                            <td><a ng-href="/ng/com_zeapps_crm/{{ product.compose == 0 ? 'product' : 'product_compose' }}/{{ product.id }}">{{ product.name }}</a></td>
                            <td><a ng-href="/ng/com_zeapps_crm/{{ product.compose == 0 ? 'product' : 'product_compose' }}/{{ product.id }}">{{ product.price_ht | currency }}</a></td>
                            <td><a ng-href="/ng/com_zeapps_crm/{{ product.compose == 0 ? 'product' : 'product_compose' }}/{{ product.id }}">{{ product.value_taxe }}</a></td>
                            <td><a ng-href="/ng/com_zeapps_crm/{{ product.compose == 0 ? 'product' : 'product_compose' }}/{{ product.id }}">{{ product.accounting_number }}</a></td>
                            <td class="text-right">
                                <button type="button" class="btn btn-xs btn-danger" ng-click="delete(product)">
                                    <i class="fa fa-trash fa-fw"></i>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>