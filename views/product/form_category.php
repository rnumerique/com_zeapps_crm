<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Produits</div>
<div id="content">

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger" ng-show="error">{{ error }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="root">
                <zeapps-happylittletree
                    data-tree="tree.branches"
                    data-active-branch="activeCategory"
                </zeapps-happylittletree>
            </div>
        </div>

        <form name="newCategory" class="col-md-9">
            <div class="well">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nom de la catégorie <span class="required">*</span></label>
                            <input type="text" ng-model="form.name" class="form-control" ng-required="true">
                            <input type="hidden" ng-model="form.id_parent">
                        </div>
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