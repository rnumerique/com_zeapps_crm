<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="ComZeappsCrmCreditBalanceListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>
            <ze-btn fa="plus" color="success" hint="paiements" always-on="true"
                    ze-modalform="addPaiements"
                    data-template="templateForm"
                    data-title="Ajouter des paiements"></ze-btn>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

    <table class="table table-responsive table-condensed table-hover">
        <thead>
        <tr>
            <th>N° Facture</th>
            <th>Entreprise</th>
            <th>Contact</th>
            <th class="text-right">Total à payer</th>
            <th class="text-right">Payé</th>
            <th class="text-right">Restant à payer</th>
            <th class="text-right">Date limite</th>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="credit in credits" ng-click="goTo(credit.id_invoice)" ng-class="isOverdue(credit)">
            <td>{{ credit.numerotation }}</td>
            <td>{{ credit.name_company }}</td>
            <td>{{ credit.name_contact }}</td>
            <td class="text-right">{{ credit.total | currency:'€':2 }}</td>
            <td class="text-right">{{ credit.paid | currency:'€':2 }}</td>
            <td class="text-right">{{ credit.left_to_pay | currency:'€':2 }}</td>
            <td class="text-right">{{ credit.due_date | date:'dd/MM/yyyy' }}</td>
        </tr>
        </tbody>
    </table>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>
</div>