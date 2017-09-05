<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="ComZeappsCrmModalityConfigFormModalCtrl">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Libelle</label>
                <input type="text" class="form-control" ng-model="form.label" ng-required="true">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Libelle document</label>
                <input type="text" class="form-control" ng-model="form.label_doc" ng-required="true">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Type de reglement</label>
                <select class="form-control" ng-model="form.type">
                    <option value="0">Autre mode</option>
                    <option value="1">Cheque</option>
                    <option value="2">Prélèvement</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group" ng-model="form.id_cheque">
                <label>Identifiant remise cheque</label>
                <input type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Situation comptable</label>
                <select class="form-control" ng-model="form.situation">
                    <option value="0">Attente règlement</option>
                    <option value="1">Règlement reçu</option>
                    <option value="2">Espèce</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Compte Comptable</label>
                <input type="text" class="form-control" ng-model="form.accounting_account">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group" ng-model="form.journal">
                <label>Journal</label>
                <input type="text" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Type d'échéance</label>
                <select class="form-control" ng-model="form.settlement_type">
                    <option value="0">Suivant la date de facture</option>
                    <option value="1">Echéance fixe</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Le <small>(jour du mois)</small></label>
                <input type="number" class="form-control" ng-model="form.settlement_date">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Nombre de jours</label>
                <input type="number" class="form-control" ng-model="form.settlement_delay">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Code export</label>
                <input type="text" class="form-control" ng-model="form.export">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Ordre</label>
                <input type="number" class="form-control" ng-model="form.sort">
            </div>
        </div>
    </div>
</div>