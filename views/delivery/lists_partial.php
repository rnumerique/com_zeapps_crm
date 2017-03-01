<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="ComZeappsCrmDeliveryListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/delivery/new/{{ id_company || ""}}''><span class='fa fa-plus' aria-hidden='true'></span> Livraison</a>
            </div>
            <span ng-click="shownFilter = !shownFilter">
                <i class="fa fa-filter"></i> Filtres <i class="fa" ng-class="shownFilter ? 'fa-caret-up' : 'fa-caret-down'"></i>
            </span>
            <h3>Livraisons</h3>
        </div>
    </div>

    <div class="well" ng-if="shownFilter">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Date de création : Début</label>
                    <input type="date" class="form-control" ng-model="filters.date_creation_start">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fin</label>
                    <input type="date" class="form-control" ng-model="filters.date_creation_end">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Date limite : Début</label>
                    <input type="date" class="form-control" ng-model="filters.date_limite_start">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fin</label>
                    <input type="date" class="form-control" ng-model="filters.date_limite_end">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label># :</label>
                    <input type="text" class="form-control" ng-model="filters.numerotation">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Libelle :</label>
                    <input type="text" class="form-control" ng-model="filters.libelle">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Destinataire :</label>
                    <input type="text" class="form-control" ng-model="filters.client">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Total HT : Supérieur à</label>
                    <input type="number" class="form-control" ng-model="filters.total_ht_floor">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Inférieur à</label>
                    <input type="number" class="form-control" ng-model="filters.total_ht_ceiling">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Total TTC : Supérieur à</label>
                    <input type="number" class="form-control" ng-model="filters.total_ttc_floor">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Inférieur à</label>
                    <input type="number" class="form-control" ng-model="filters.total_ttc_ceiling">
                </div>
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
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="delivery in deliveries | com_zeapps_crmFilter:filters">
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