<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Stocks</div>
<div id="content">

    <div class="row">
        <div class="col-md-12">
            <h3>{{ product_stock.label }}</h3>
        </div>
    </div>

    <div ng-show="product_stock.movements.length">
        <div class="row">
            <div class="col-md-12">
                <h4>Mouvements de stocks</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <strong>Date prévu de rupture de stocks : </strong>{{ product_stock.timeleft + ' (' +  product_stock.dateRupture + ')' }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h5>Visualisation</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <canvas id="base" class="chart-line"
                        chart-data="data"
                        chart-labels="labels">
                </canvas>
            </div>
        </div>
        </div><div class="row">
            <div class="col-md-12">
                <h5>Historique</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-condensed table-responsive">
                    <thead>
                    <tr>
                        <th>Libellé</th>
                        <th>Date</th>
                        <th class="text-right"Qté</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="movement in product_stock.movements | orderBy:'-date_mvt'" ng-class="movement.qty > 0 ? 'bg-success' : 'bg-danger'">
                        <td>{{movement.label}}</td>
                        <td>{{movement.date_mvt}}</td>
                        <td class="text-right">{{movement.qty}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>