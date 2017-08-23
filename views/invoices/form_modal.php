<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="ComZeappsCrmInvoiceFormCtrl">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Libellé de la facture</label>
                <input type="text" ng-model="form.libelle" class="form-control">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Gestionnaire de la facture <span class="required">*</span></label>
                <span   ze-modalsearch="loadAccountManager"
                        data-http="accountManagerHttp"
                        data-model="form.name_user_account_manager"
                        data-fields="accountManagerFields"
                        data-title="Choisir une entreprise"></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" ng-model="form.status">
                    <option>En cours</option>
                    <option>Gagné</option>
                    <option>Perdu</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Probabilité</label>
                <input type="number" class="form-control" ng-model="form.probability">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Société <span class="required">**</span></label>
                <span   ze-modalsearch="loadCompany"
                        data-http="companyHttp"
                        data-model="form.name_company"
                        data-fields="companyFields"
                        data-template-new="companyTplNew"
                        data-title="Choisir une entreprise"></span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Contact <span class="required">**</span></label>
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
                <label>Compte comptable</label>
                <span   ze-modalsearch="loadAccountingNumber"
                        data-http="accountingNumberHttp"
                        data-model="form.accounting_number"
                        data-fields="accountingNumberFields"
                        data-template-new="accountingNumberTplNew"
                        data-title="Choisir un compte comptable"></span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Remise globale</label>
                <input type="number" min="0" ng-model="form.global_discount" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Date de création <span class="required">*</span></label>
                <input type="date" ng-model="form.date_creation" ng-change="updateDateLimit()" class="form-control" ng-required="true">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Date de validité <span class="required">*</span></label>
                <input type="date" ng-model="form.date_limit" class="form-control" ng-required="true">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Modalités de règlement</label>
                <select ng-model="form.modalities" class="form-control">
                    <option ng-repeat="modality in modalities" value="{{modality.label}}">
                        {{ modality.label }}
                    </option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Référence client</label>
                <input type="text" ng-model="form.reference_client" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Entrepôts <span class="required">*</span></label>
                <select ng-model="form.id_warehouse" class="form-control" ng-required="true">
                    <option ng-repeat="warehouse in warehouses" value="{{warehouse.id}}">
                        {{ warehouse.label }}
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <span class="required">** au moins un des deux champs est requis</span>
        </div>
    </div>
</div>