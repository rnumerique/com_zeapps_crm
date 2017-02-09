<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Produits</div>
<div id="content">
    <div class="row">
        <div class="col-md-3">
            <div class="root">
                <zeapps-happylittletree
                    data-tree="tree.branches"
                    data-active-branch="activeCategory"
                </zeapps-happylittletree>
            </div>
        </div>

        <form name="newProduct" class="col-md-9">
            <div class="well">
                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">
                            <label>Nom du produit <span class="required">*</span></label>
                            <input type="text" ng-model="form.name" class="form-control" ng-required="true">
                        </div>

                        <div class="form-group">
                            <label>Compte Comptable <span class="required">*</span></label>
                            <input type="text" ng-model="form.accounting_number" class="form-control" ng-required="true">
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label>Prix HT <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" step="0.01" ng-model="form.price_ht" class="form-control" ng-required="true">
                                <span class="input-group-addon">€</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>TVA <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" step="0.01" ng-model="form.tva" class="form-control" ng-required="true">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-12">

                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" ng-model="form.description" class="form-control">
                        </div>

                    </div>

                </div>
            </div>




            <div class="well">
                <div class="row">

                    <div class="form-group" ng-repeat="attribute in attributes">
                        <label>{{ attribute.name }} <span class="required" ng-if="attribute.required">*</span></label>
                        <input type="{{ attribute.type }}" ng-model="form.extra[attribute.name]" ng-class="attribute.type != 'checkbox' ? 'form-control' : ''" ng-required="attribute.required" ng-if="attribute.type != 'textarea'">
                        <textarea ng-model="form.extra[attribute.name]" class="form-control" rows="3" ng-required="attribute.required" ng-if="attribute.type == 'textarea'"></textarea>
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <span class="required">*</span> champs obligatoires
                </div>
            </div>

            <form-buttons></form-buttons>
        </form>
    </div>

</div>