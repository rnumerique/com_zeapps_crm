<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/order/new/{{ id_company || ""}}''><span class='fa fa-plus' aria-hidden='true'></span> Commande</a>
        </div>
        <h3>Commandes en cours</h3>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-striped table-condensed table-responsive" ng-show="orders.length">
            <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Contact</th>
                <th>Entreprise</th>
                <th>Total HT</th>
                <th>Total TTC</th>
                <th>Date de création</th>
                <th>Date limite</th>
                <th>Responsable</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="order in orders">
                <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.numerotation}}</a></td>
                <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.libelle}}</a></td>
                <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.contact.first_name[0] + '. ' + order.contact.last_name}}</a></td>
                <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.company.company_name}}</a></td>
                <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{totalHT(order)}}</a></td>
                <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{totalTTC(order)}}</a></td>
                <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.date_creation | date:'dd/MM/yyyy'}}</a></td>
                <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.date_limit | date:'dd/MM/yyyy'}}</a></td>
                <td><a href="/ng/com_zeapps_crm/order/{{order.id}}">{{order.user_name}}</a></td>
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