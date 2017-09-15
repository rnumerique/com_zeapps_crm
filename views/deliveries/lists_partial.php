<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="ComZeappsCrmDeliveryListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>

            <ze-btn fa="plus" color="success" hint="Bon de livraison" always-on="true"
                    ze-modalform="add"
                    data-template="templateDelivery"
                    data-title="Créer un nouveau bon de livraison"></ze-btn>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-condensed table-responsive" ng-show="deliveries.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Libelle</th>
                    <th>Destinataire</th>
                    <th class="text-right">Total HT</th>
                    <th class="text-right">Total TTC</th>
                    <th>Date de création</th>
                    <th>Responsable</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="delivery in deliveries">
                    <td ng-click="goTo(delivery.id)">{{delivery.numerotation}}</td>
                    <td ng-click="goTo(delivery.id)">{{delivery.libelle}}</td>
                    <td ng-click="goTo(delivery.id)">
                        
                            {{delivery.name_company}}
                            <span ng-if="delivery.name_company && delivery.name_contact">-</span>
                            {{delivery.name_contact ? delivery.name_contact : ''}}
                        
                    </td>
                    <td ng-click="goTo(delivery.id)" class="text-right">{{delivery.total_ht | currency:'€':2}}</td>
                    <td ng-click="goTo(delivery.id)" class="text-right">{{delivery.total_ttc | currency:'€':2}}</td>
                    <td ng-click="goTo(delivery.id)">{{delivery.date_creation || "-" | date:'dd/MM/yyyy'}}</td>
                    <td ng-click="goTo(delivery.id)">{{delivery.name_user_account_manager}}</td>
                    <td class="text-right">
                        <ze-btn fa="pencil" color="info" direction="left" hint="Editer"
                                ze-modalform="edit"
                                data-edit="delivery"
                                data-title="Editer le bon de livraison"
                                data-template="templateDelivery"></ze-btn>
                        <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(delivery)" ze-confirmation></ze-btn>
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

</div>