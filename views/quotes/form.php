<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Entreprises</div>
<div id="content">


    <form>
        <div class="well">
            <div class="row">
                <div class="col-md-6">


                    <div class="form-group">
                        <label>Libellé du devis</label>
                        <input type="text" ng-model="form.libelle" class="form-control">
                    </div>


                    <div class="form-group">
                        <label>Société <span class="required">**</span></label>

                        <span   ze-modalsearch="loadCompany"
                                data-http="companyHttp"
                                data-model="form.company.company_name"
                                data-fields="companyFields"
                                data-template-new="companyTplNew"
                                data-title="Choisir une entreprise"></span>
                    </div>


                    <div class="form-group">
                        <label>Compte comptable</label>
                        <span   ze-modalsearch="loadAccountingNumber"
                                data-http="accountingNumberHttp"
                                data-model="form.accounting_number"
                                data-fields="accountingNumberFields"
                                data-template-new="accountingNumberTplNew"
                                data-title="Choisir un compte comptable"></span>
                    </div>

                    <div class="form-group">
                        <label>Date de création <span class="required">*</span></label>
                        <input type="date" ng-model="form.date_creation" ng-change="updateDateLimit()" class="form-control" ng-required="true">
                    </div>


                    <div class="form-group">
                        <label>Modalités de règlement</label>
                        <select ng-model="form.modalities" class="form-control">
                            <option ng-repeat="modality in modalities">
                                {{ modality.label }}
                            </option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label>Entrepôts <span class="required">*</span></label>
                        <select ng-model="form.id_warehouse" class="form-control" ng-required="true">
                            <option ng-repeat="warehouse in warehouses" ng-value="warehouse.id">
                                {{ warehouse.label }}
                            </option>
                        </select>
                    </div>
                </div>


                <div class="col-md-6">

                    <div class="form-group">
                        <label>Gestionnaire du devis <span class="required">*</span></label>

                        <span   ze-modalsearch="loadAccountManager"
                                data-http="accountManagerHttp"
                                data-model="form.name_user_account_manager"
                                data-fields="accountManagerFields"
                                data-title="Choisir une entreprise"></span>
                    </div>


                    <div class="form-group">
                        <label>Contact <span class="required">**</span></label>

                        <span   ze-modalsearch="loadContact"
                                data-http="contactHttp"
                                data-model="form.contact.name"
                                data-fields="contactFields"
                                data-template-new="contactTplNew"
                                data-title="Choisir un contact"></span>
                    </div>


                    <div class="form-group">
                        <label>Remise</label>
                        <input type="number" min="0" ng-model="form.global_discount" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Date de validité <span class="required">*</span></label>
                        <input type="date" ng-model="form.date_limit" class="form-control" ng-required="true">
                    </div>

                    <div class="form-group">
                        <label>Référence client</label>
                        <input type="text" ng-model="form.reference_client" class="form-control">
                    </div>
                </div>

                <div class="col-md-12">
                    <span class="required">** au moins un des deux champs est requis</span>
                </div>
            </div>
        </div>


        <form-buttons></form-buttons>

    </form>


</div>