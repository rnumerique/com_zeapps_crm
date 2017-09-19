<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="breadcrumb">Credits en cours</div>

<div id="content">
    <div class="row">
        <div class="col-md-12">
            <div class="well">
                <table class="table table-responsive table-condensed">
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
                    <tr>
                        <td><a href="/ng/com_zeapps_crm/invoice/{{credit.id_invoice}}">{{ credit.numerotation }}</a></td>
                        <td><a href="/ng/com_zeapps_contact/companies/{{credit.id_company}}">{{ credit.name_company }}</a></td>
                        <td><a href="/ng/com_zeapps_contact/contacts/{{credit.id_contact}}">{{ credit.name_contact }}</a></td>
                        <td class="text-right">{{ credit.total | currency:'€':2 }}</td>
                        <td class="text-right">{{ credit.paid | currency:'€':2 }}</td>
                        <td class="text-right">{{ credit.left_to_pay | currency:'€':2 }}</td>
                        <td class="text-right">{{ credit.due_date | date:'dd/MM/yyyy' }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <ze-btn fa="plus" color="success" hint="paiement" always-on="true"
                    ze-modalform="add"
                    data-template="templateForm"
                    data-title="Modifier un paiement"></ze-btn>
            <table class="table table-responsive table-condensed" ng-if="details.length">
                <thead>
                <tr>
                    <th>Modalité</th>
                    <th class="text-right">Somme payée</th>
                    <th class="text-right">Date du paiement</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="detail in details">
                    <td>{{ detail.label_modality }}</td>
                    <td class="text-right">{{ detail.paid | currency:'€':2 }}</td>
                    <td class="text-right">{{ detail.date_payment | date:'dd/MM/yyyy' }}</td>
                    <td class="text-right">
                        <ze-btn fa="pencil" color="info" direction="left" hint="Editer"
                                ze-modalform="edit"
                                data-edit="detail"
                                data-template="templateForm"
                                data-title="Ajouter un paiement"></ze-btn>
                        <ze-btn fa="trash" color="danger" direction="left" hint="Supprimer" ng-click="delete(detail)" ze-confirmation></ze-btn>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>