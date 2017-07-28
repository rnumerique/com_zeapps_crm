<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="ComZeappsCrmOrderListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <h3>Commandes</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <ze-filters data-model="filter.model" data-options="filter.options"></ze-filters>
            <div class="pull-right">
                <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/order/new{{ id_company ?  "/company/" + id_company : "" }}{{ id_contact ?  "/contact/" + id_contact : "" }}'><span class='fa fa-fw fa-plus' aria-hidden='true'></span> Commande</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-condensed table-responsive" ng-show="orders.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Libelle</th>
                    <th>Destinataire</th>
                    <th>Total HT (€)</th>
                    <th>Total TTC (€)</th>
                    <th>Date de création</th>
                    <th>Date limite</th>
                    <th>Responsable</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="order in orders | com_zeapps_crmFilter:filter.model">
                    <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.numerotation}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.libelle}}</a></td>
                    <td>
                        <a href="/ng/com_zeapps_crm/order/{{order.id}}">
                            {{order.company.company_name}}
                            <span ng-if="order.company.company_name && order.contact.last_name">-</span>
                            {{order.contact ? order.contact.first_name[0] + '. ' + order.contact.last_name : ''}}
                        </a>
                    </td>
                    <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.total_ht}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.total_ttc}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.date_creation | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.date_limit | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.user_name}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.finalized === '1' ? 'cloturée' : ''}}</a></td>
                    <td class="text-right">
                        <button type="button" class="btn btn-xs btn-danger" ng-click="delete(order)">
                            <i class="fa fa-trash fa-fw"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>