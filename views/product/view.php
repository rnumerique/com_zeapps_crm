<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Produits</div>
<div id="content">

    <div class="row">
        <div class="col-md-3">
            <div class="root">
                <zeapps-happylittletree data-tree="tree.branches" data-update="update"></zeapps-happylittletree>
            </div>
        </div>

        <div class="col-md-9">
            <div class="clearfix">
                <div class="pull-right" ng-show="currentBranch.id != -1">
                    <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/product/new_category/{{ currentBranch.id || 0 }}'><span class='fa fa-plus' aria-hidden='true'></span> Sous-categorie</a>
                    <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/product/new_product/{{ currentBranch.id || 0 }}'><span class='fa fa-plus' aria-hidden='true'></span> Produit</a>
                    <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/product/new_product_compose/{{ currentBranch.id || 0 }}'><span class='fa fa-plus' aria-hidden='true'></span> Produit Compose</a>
                </div>
                <h3 class="text-capitalize active-category-title">
                    {{ currentBranch.name }}
                    <a class="btn btn-info btn-xs" ng-href="/ng/com_zeapps_crm/product/category/{{ currentBranch.id }}/edit" ng-show="currentBranch.id > 0">
                        <span class="fa fa-fw fa-pencil"></span>
                    </a>
                    <button type="button" class="btn btn-xs btn-danger" ng-click="delete_category(currentBranch.id)" ng-show="currentBranch.id > 0">
                        <span class="fa fa-fw fa-trash"></span>
                    </button>
                </h3>
                <div class="row" ng-show="currentBranch.branches">
                    <h5>Sous-Categories</h5>
                    <ul ui-sortable="sortableOptions" id="sortable" class="branch-list list-unstyled col-md-4" ng-model="currentBranch.branches">
                        <li id="{{ branch.id }}" class="branch branch-sortable" ng-repeat="branch in currentBranch.branches">
                            <span class="glyphicon glyphicon-resize-vertical" aria-hidden="true"></span>
                            {{ branch.name }} <i>({{ branch.nb_products }} produit<span ng-show="branch.nb_products > 1">s</span>)</i>
                        </li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-md-12 clearfix">
                        <h5>
                            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>
                            Produits
                        </h5>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center" ng-show="total > pageSize">
                                    <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
                                        class="pagination-sm" boundary-links="true" max-size="9"
                                        previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
                                </div>
                            </div>
                        </div>

                        <table class="table table-striped">
                            <tr>
                                <th></th>
                                <th i8n="Référence"></th>
                                <th i8n="Nom du produit"></th>
                                <th i8n="Prix HT"></th>
                                <th>TVA (%)</th>
                                <th i8n="Prix TTC"></th>
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
                                <td><a ng-href="/ng/com_zeapps_crm/{{ product.compose == 0 ? 'product' : 'product_compose' }}/{{ product.id }}">{{ product.compose == 0 ? ( product.price_ht | currency ) : '-' }}</a></td>
                                <td><a ng-href="/ng/com_zeapps_crm/{{ product.compose == 0 ? 'product' : 'product_compose' }}/{{ product.id }}">{{ product.compose == 0 ? ( product.value_taxe | currency ) : '-' }}</a></td>
                                <td><a ng-href="/ng/com_zeapps_crm/{{ product.compose == 0 ? 'product' : 'product_compose' }}/{{ product.id }}">{{ product.price_ttc | currency }}</a></td>
                                <td><a ng-href="/ng/com_zeapps_crm/{{ product.compose == 0 ? 'product' : 'product_compose' }}/{{ product.id }}">{{ product.accounting_number }}</a></td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-xs btn-danger" ng-click="delete(product)">
                                        <i class="fa fa-trash fa-fw"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center" ng-show="total > pageSize">
                                    <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
                                        class="pagination-sm" boundary-links="true" max-size="9"
                                        previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>