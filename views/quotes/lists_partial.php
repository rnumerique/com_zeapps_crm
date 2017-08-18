<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="ComZeappsCrmQuoteListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/quote/new{{ id_company ?  "/company/" + id_company : "" }}{{ id_contact ?  "/contact/" + id_contact : "" }}'><span class='fa fa-fw fa-plus' aria-hidden='true'></span> Devis</a>
            </div>
            <h3>
                Devis
            </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <ze-filters data-model="filter.model" data-options="filter.options"></ze-filters>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-condensed table-responsive" ng-show="quotes.length">
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
                <tr ng-repeat="quote in quotes | com_zeapps_crmFilter:filter.model">
                    <td><a href="/ng/com_zeapps_crm/quote/{{quote.id}}">{{quote.numerotation}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/quote/{{quote.id}}">{{quote.libelle}}</a></td>
                    <td>
                        <a href="/ng/com_zeapps_crm/quote/{{quote.id}}">
                            {{quote.company.company_name}}
                            <span ng-if="quote.company.company_name && quote.contact.last_name">-</span>
                            {{quote.contact ? quote.contact.first_name[0] + '. ' + quote.contact.last_name : ''}}
                        </a>
                    </td>
                    <td><a href="/ng/com_zeapps_crm/quote/{{quote.id}}">{{quote.total_ht}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/quote/{{quote.id}}">{{quote.total_ttc}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/quote/{{quote.id}}">{{quote.date_creation | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/quote/{{quote.id}}">{{quote.date_limit | date:'dd/MM/yyyy'}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/quote/{{quote.id}}">{{quote.user_name}}</a></td>
                    <td><a href="/ng/com_zeapps_crm/quote/{{quote.id}}">{{quote.finalized === '1' ? 'cloturé' : ''}}</a></td>
                    <td class="text-right">
                        <button type="button" class="btn btn-xs btn-danger" ng-click="delete(quote)">
                            <i class="fa fa-trash fa-fw"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>