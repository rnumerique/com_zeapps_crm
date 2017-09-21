<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Commandes probables</div>
<div id="content">
<form>

    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-condensed table-responsive" ng-show="orders.length">
                <thead>
                <tr>
                    <th>Entreprise</th>
                    <th>Contact</th>
                    <th>Derniere facture</th>
                    <th class="text-right">Date de la derniere facture</th>
                    <th class="text-right">Temps moyen entre 2 commande</th>
                    <th class="text-right">Date estimée de la prochaine commande</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="order in orders">
                    <td>
                        <a href="/ng/com_zeapps_contact/companies/{{order.id_company}}">{{ order.name_company }}</a>
                    </td>
                    <td>
                        <a href="/ng/com_zeapps_contact/contacts/{{order.id_contact}}">{{ order.name_contact }}</a>
                    </td>
                    <td>
                        <a href="/ng/com_zeapps_crm/invoice/{{order.id}}">{{ order.numerotation + " - " + order.libelle }}</a>
                    </td>
                    <td class="text-right">{{ order.date_creation | date:"dd/MM/yyyy" }}</td>
                    <td class="text-right">{{ order.avg | number:0 }}</td>
                    <td class="text-right">{{ order.date_next | date:"dd/MM/yyyy" }}</td>
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