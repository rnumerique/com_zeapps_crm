<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Config > devis</div>
<div id="content">

    <div class="row">
        <div class="col-md-12">
            <button type="button" class="btn btn-primary" ng-click="test()">Tester le format</button> {{ result }}
        </div>
    </div>

    <form>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Format de numérotation des devis</label>
                    <input class="form-control" type="text" ng-model="format">
                    <p class="help-block">
                        Les elements de formats sont encadrés par des crochets (ex: [xxxxx][dmY] ou [dmY-XXXXXX]) <br/>
                        --- Numérotation --- <br/>
                        x :  numerotation (sur autant de chiffres que de x), sans 0 initiaux <br/>
                        X :  numerotation (sur autant de chiffres que de X), avec 0 initiaux <br/>
                        --- Jour --- <br/>
                        d : jour du mois avec 0 initiaux <br/>
                        D : jour du mois  sur 3 lettres <br/>
                        j : jour du mois sans 0 initiaux <br/>
                        z : jour de l'année ( 0 a 365 ) <br/>
                        --- Mois --- <br/>
                        m : mois au format numérique avec 0 initiaux <br/>
                        M : mois en 3 lettres en anglais (Jan à Dec) <br/>
                        n : mois au format numérique sans 0 initiaux <br/>
                        --- Année --- <br/>
                        y : année sur 2 chiffres <br/>
                        Y : année sur 4 chiffres <br/>
                        --- Heure --- <br/>
                        g : heure au format 12h, sans 0 initiaux <br/>
                        G : heure au format 12h, avec 0 initiaux <br/>
                        h : heure au format 24h, sans 0 initiaux <br/>
                        H : heure au format 24h, avec 0 initiaux <br/>
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Numérotation du prochain devis</label>
                    <input type="number" class="form-control" ng-model="numerotation">
                </div>
            </div>
        </div>

        <form-buttons></form-buttons>
    </form>

</div>