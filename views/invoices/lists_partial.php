<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="ComZeappsCrmInvoiceListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>

            <ze-btn fa="plus" color="success" hint="Facture" always-on="true"
                    ze-modalform="add"
                    data-template="templateInvoice"
                    data-title="Créer une nouvelle facture"></ze-btn>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-condensed table-responsive" ng-show="invoices.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Libelle</th>
                    <th>Destinataire</th>
                    <th class="text-right">Total HT</th>
                    <th class="text-right">Total TTC</th>
                    <th>Date de création</th>
                    <th>Date limite</th>
                    <th>Responsable</th>
                    <th class="text-right">%</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="invoice in invoices">
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.numerotation}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.libelle}}</a></td>
                    <td>
                        <a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">
                            {{invoice.name_company}}
                            <span ng-if="invoice.name_company && invoice.name_contact">-</span>
                            {{invoice.name_contact ? invoice.name_contact : ''}}
                        </a>
                    </td>
                    <td class="text-right"><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.total_ht | currency:'€':2}}</a></td>
                    <td class="text-right"><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.total_ttc | currency:'€':2}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.date_creation | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.date_limit | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.name_user_account_manager}}</a></td>
                    <td class="text-right"><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.probability | number:2}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.status}}</a></td>
                    <td class="text-right">
                        <ze-btn fa="pencil" color="info" direction="left" hint="Editer"
                                ze-modalform="edit"
                                data-edit="invoice"
                                data-title="Editer la facture"
                                data-template="templateInvoice"></ze-btn>
                        <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(invoice)" ze-confirmation></ze-btn>
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