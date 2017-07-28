<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="ComZeappsCrmDeliveryListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <h3>Livraisons</h3>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <ze-filters data-model="filter.model" data-options="filter.options"></ze-filters>
            <div class="pull-right">
                <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/delivery/new{{ id_company ?  "/company/" + id_company : "" }}{{ id_contact ?  "/contact/" + id_contact : "" }}'><span class='fa fa-fw fa-plus' aria-hidden='true'></span> Livraison</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-condensed table-responsive" ng-show="deliveries.length">
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
                <tr ng-repeat="delivery in deliveries | com_zeapps_crmFilter:filter.model">
                    <td><a href="/ng/com_zeapps_crm/delivery/{{delivery.id}}">{{delivery.numerotation}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/delivery/{{delivery.id}}">{{delivery.libelle}}</a></td>
                    <td>
                        <a href="/ng/com_zeapps_crm/delivery/{{delivery.id}}">
                            {{delivery.company.company_name}}
                            <span ng-if="delivery.company.company_name && delivery.contact.last_name">-</span>
                            {{delivery.contact ? delivery.contact.first_name[0] + '. ' + delivery.contact.last_name : ''}}
                        </a>
                    </td>
                    <td><a href="/ng/com_zeapps_crm/delivery/{{delivery.id}}">{{delivery.total_ht}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/delivery/{{delivery.id}}">{{delivery.total_ttc}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/delivery/{{delivery.id}}">{{delivery.date_creation | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/delivery/{{delivery.id}}">{{delivery.date_limit | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/delivery/{{delivery.id}}">{{delivery.user_name}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/delivery/{{delivery.id}}">{{delivery.finalized === '1' ? 'cloturée' : ''}}</a></td>
                    <td class="text-right">
                        <button type="button" class="btn btn-xs btn-danger" ng-click="delete(delivery)">
                            <i class="fa fa-trash fa-fw"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>