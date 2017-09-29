<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="ComZeappsCrmCreditBalanceFormMultipleCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Type de client :</label>
                <select ng-model="form.type" class="form-control" ng-change="clearForm()">
                    <option value="contact">Contact</option>
                    <option value="company">Entreprise</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" ng-show="form.type === 'company'">
            <div class="form-group">
                <label>Entreprise</label>
                <span   ze-modalsearch="loadCompany"
                        data-http="companyHttp"
                        data-model="form.name_company"
                        data-fields="companyFields"
                        data-template-new="companyTplNew"
                        data-title="Choisir une entreprise"></span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group" ng-show="form.type === 'contact'">
                <label>Contact</label>
                <span   ze-modalsearch="loadContact"
                        data-http="contactHttp"
                        data-model="form.name_contact"
                        data-fields="contactFields"
                        data-template-new="contactTplNew"
                        data-title="Choisir un contact"></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Modalités de règlement</label>
                <select ng-model="form.id_modality" class="form-control" ng-change="updateModality()">
                    <option ng-repeat="modality in modalities" value="{{modality.id}}">
                        {{ modality.label }}
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Date du paiement</label>
                <input type="date" ng-model="form.date_payment" class="form-control">
            </div>
        </div>
    </div>

    <div class="row" ng-show="credits.length">
        <div class="col-md-12">
            <table class="table table-responsive table-condensed">
                <thead>
                <tr>
                    <th>N° Facture</th>
                    <th class="text-right">Date limite</th>
                    <th class="text-right">Reste à payer</th>
                    <th class="text-right">Paiement</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="credit in credits">
                    <td>{{ credit.numerotation }}</td>
                    <td class="text-right">{{ credit.due_date | date:'dd/MM/yyyy' }}</td>
                    <td class="text-right">{{ credit.left_to_pay | currency:'€':2 }}</td>
                    <td class="text-right">
                        <input type="number" class="form-control" ng-model="form.lines[credit.id_invoice]" ng-change="updateTotal()">
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><b>Total</b></td>
                    <td class="text-right">{{ total | currency:'€':2 }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>