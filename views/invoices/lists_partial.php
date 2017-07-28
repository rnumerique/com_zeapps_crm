<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="ComZeappsCrmInvoiceListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <h3>Factures</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <ze-filters data-model="filter.model" data-options="filter.options"></ze-filters>
            <div class="pull-right">
                <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/invoice/new{{ id_company ?  "/company/" + id_company : "" }}{{ id_contact ?  "/contact/" + id_contact : "" }}'><span class='fa fa-fw fa-plus' aria-hidden='true'></span> Facture</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-condensed table-responsive" ng-show="invoices.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Libelle</th>
                    <th>Destinataire</th>
                    <th>Contact</th>
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
                <tr ng-repeat="invoice in invoices | com_zeapps_crmFilter:filter.model">
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.numerotation}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.libelle}}</a></td>
                    <td>
                        <a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">
                            {{invoice.company.company_name}}
                            <span ng-if="invoice.company.company_name && invoice.contact.last_name">-</span>
                            {{invoice.contact ? invoice.contact.first_name[0] + '. ' + invoice.contact.last_name : ''}}
                        </a>
                    </td>
                    <td>
                        <a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">
                            {{invoice.contact.mobile || invoice.contact.phone || invoice.contact.other_phone}}
                            <span ng-if="(invoice.contact.mobile || invoice.contact.phone || invoice.contact.other_phone) && quote.contact.email">-</span>
                            {{invoice.contact.email}}
                        </a>
                    </td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.total_ht}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.total_ttc}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.date_creation | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.date_limit | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.user_name}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.finalized === '1' ? 'cloturée' : ''}}</a></td>
                    <td class="text-right">
                        <button type="button" class="btn btn-xs btn-danger" ng-click="delete(invoice)">
                            <i class="fa fa-trash fa-fw"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>