<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="row" ng-controller="ComZeappsCrmWarehouseUserHookCtrl">
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