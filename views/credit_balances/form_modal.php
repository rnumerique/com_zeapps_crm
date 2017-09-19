<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="ComZeappsCrmCreditBalanceFormCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Somme :</label>
                <input type="number" ng-model="form.paid" class="form-control">
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
</div>