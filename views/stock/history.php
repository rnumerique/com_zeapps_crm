<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="row">
    <div class="col-md-12 text-right" ng-show="selectedWarehouse > 0 && !isMvtFormOpen()">
        <button type="button" class="btn btn-xs btn-success" ng-click="openMvtForm()">
            <i class="fa fa-fw fa-plus"></i> Ajouter un mouvement de stock
        </button>
    </div>
</div>

<div class="well" ng-if="selectedWarehouse > 0 && isMvtFormOpen()">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-7">
                <div class="form-group">
                    <label>Libellé</label>
                    <input class="form-control" type="text" ng-model="mvtForm.label">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Mouvement</label>
                    <input class="form-control" type="number" ng-model="mvtForm.qty">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Date</label>
                    <input class="form-control" type="datetime-local" ng-model="mvtForm.date_mvt">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <button type="button" class="btn btn-xs btn-default" ng-click="cancelMvt()">
                Annuler
            </button>
            <button type="button" class="btn btn-xs btn-success" ng-click="addMvt()">
                Ajouter
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <span class="text-success">Imports</span> -
        <span class="text-danger">Exports</span><br>
        <span class="text-success"><i class="fa fa-fw fa-eye"></i></span> Mouvements pris en comptes dans les statistiques de rupture de stocks et réapprovisionnement<br>
        <span class="text-danger"><i class="fa fa-fw fa-eye-slash"></i></span> Mouvements ignorés dans les statistiques de rupture de stocks et réapprovisionnement
    </div>
</div>

<div class="text-center" ng-show="plans.length > pageSize">
    <ul uib-pagination total-items="product_stock.movements.length" ng-model="page" items-per-page="pageSize" class="pagination-sm" boundary-links="true"
        previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;">
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed table-responsive">
            <thead>
            <tr>
                <th>Date</th>
                <th>Libellé</th>
                <th class="text-right">Qté</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="movement in product_stock.movements | orderBy:'-date_mvt' | startFrom:(page - 1)*pageSize | limitTo:pageSize"
                ng-class="backgroundOf(movement)"
            >
                <td>{{movement.date_mvt | date:'dd/MM/yyyy'}}</td>
                <td>{{movement.label}}</td>
                <td class="text-right">{{movement.qty}}</td>
                <td class="text-right">
                    <button type="button" class="btn btn-xs btn-success" ng-show="movement.ignored === '0'" ng-click="setIgnoredTo(movement, '1')">
                        <i class="fa fa-fw fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" ng-show="movement.ignored === '1'" ng-click="setIgnoredTo(movement, '0')">
                        <i class="fa fa-fw fa-eye-slash"></i>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="text-center" ng-show="plans.length > pageSize">
    <ul uib-pagination total-items="product_stock.movements.length" ng-model="page" items-per-page="pageSize" class="pagination-sm" boundary-links="true"
        previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;">
    </ul>
</div>