<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="ComZeappsCrmInvoiceListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/invoice/new/{{ id_company || ""}}'><span class='fa fa-plus' aria-hidden='true'></span> Facture</a>
            </div>
            <h3>Factures</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-condensed table-responsive" ng-show="invoices.length">
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
                <tr ng-repeat="invoice in invoices">
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.numerotation}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.libelle}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.contact.first_name[0] + '. ' + invoice.contact.last_name}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.company.company_name}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{totalHT(invoice)}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{totalTTC(invoice)}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.date_creation | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.date_limit | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/invoice/{{invoice.id}}">{{invoice.user_name}}</a></td>
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