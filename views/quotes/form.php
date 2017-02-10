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
                        <label>Société</label>
                        <div class="input-group">
                            <input type="text" ng-model="form.company.company_name" class="form-control" disabled>

                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" ng-click="removeCompany()"
                                        ng-show="form.company.id != 0 && form.company.id != undefined">x
                                </button>
                                <button class="btn btn-default" type="button" ng-click="loadCompany()">...</button>
                            </span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Compte comptable</label>
                        <input type="text" ng-model="form.accounting_number" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Date de création</label>
                        <input type="date" ng-model="form.date_creation" class="form-control">
                    </div>


                    <div class="form-group">
                        <label>Modalités de règlement</label>
                        <input type="text" ng-model="form.modalities" class="form-control">
                    </div>
                </div>


                <div class="col-md-6">

                    <div class="form-group">
                        <label>Gestionnaire du devis</label>
                        <div class="input-group">
                            <input type="text" ng-model="form.name_user_account_manager" class="form-control" disabled>

                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" ng-click="removeAccountManager()"
                                        ng-show="form.id_user_account_manager != 0 && form.id_user_account_manager != undefined">x
                                </button>
                                <button class="btn btn-default" type="button" ng-click="loadAccountManager()">...</button>
                            </span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Contact</label>
                        <div class="input-group">
                            <input type="text" ng-model="form.contact.name" class="form-control" disabled>

                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" ng-click="removeContact()"
                                        ng-show="form.contact.id != 0 && form.contact.id != undefined">x
                                </button>
                                <button class="btn btn-default" type="button" ng-click="loadContact()">...</button>
                            </span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Remise</label>
                        <input type="number" min="0" ng-model="form.global_discount" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Date de validité</label>
                        <input type="date" ng-model="form.date_limit" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Référence client</label>
                        <input type="text" ng-model="form.reference_client" class="form-control">
                    </div>
                </div>
            </div>
        </div>


        <form-buttons></form-buttons>

    </form>


</div>