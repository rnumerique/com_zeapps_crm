<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>Référence</label>
                <input class="form-control" type="text" ng-model="form.ref">
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-group">
                <label>Libellé</label>
                <input class="form-control" type="text" ng-model="form.label">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Valeur unitaire</label>
                <input class="form-control" type="number" ng-model="form.value_ht">
            </div>
        </div>
    </div>
</div>