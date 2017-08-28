<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Commande</div>
<div id="content">


    <form>
        <div class="well">
            <div class="row">
                <div class="col-md-2">
                    <div class="titleWell">
                        Commande : {{ order.libelle }}
                    </div>
                    <p class="small">
                        n° {{ order.numerotation }}
                    </p>
                    <p>
                        <span class="form-group form-inline">
                            <select class="form-control input-sm" ng-model="order.status" ng-change="updateStatus()">
                                <option>En cours</option>
                                <option>Gagné</option>
                                <option>Perdu</option>
                            </select>
                        </span>
                        ({{ order.probability | number:2 }}%)
                    </p>
                    <button type="button" class="btn btn-xs btn-info" ng-click="showDetailsEntreprise = !showDetailsEntreprise">
                        {{ showDetailsEntreprise ? 'Masquer' : 'Voir' }} en cours
                    </button>
                </div>

                <div class="col-md-3">
                    <strong>Adresse de facturation :</strong><br>
                    {{ company.company_name }}<br ng-if="company.company_name">
                    {{ contact.last_name + ' ' + contact.first_name }}<br ng-if="contact.last_name || contact.first_name">
                    {{ order.billing_address_1 }}<br ng-if="order.billing_address_1">
                    {{ order.billing_address_2 }}<br ng-if="order.billing_address_2">
                    {{ order.billing_address_3 }}<br ng-if="order.billing_address_3">
                    {{ order.billing_zipcode + ' ' + order.billing_city }}
                </div>

                <div class="col-md-3">
                    <strong>Adresse de livraison :</strong><br>
                    {{ company.company_name }}<br ng-if="company.company_name">
                    {{ contact.last_name + ' ' + contact.first_name }}<br ng-if="contact.last_name && contact.first_name">
                    {{ order.delivery_address_1 }}<br ng-if="order.delivery_address_1">
                    {{ order.delivery_address_2 }}<br ng-if="order.delivery_address_2">
                    {{ order.delivery_address_3 }}<br ng-if="order.delivery_address_3">
                    {{ order.delivery_zipcode + ' ' + order.delivery_city }}
                </div>

                <div class="col-md-4">
                    <div class="pull-right">
                        <ze-btn fa="arrow-left" color="primary" hint="Retour" ng-click="back()"></ze-btn>
                        <ze-btn fa="pencil" color="info" hint="Editer"
                                ze-modalform="updateOrder"
                                data-edit="order"
                                data-template="templateEdit"
                                data-title="Modifier la commande"></ze-btn>
                        <ze-btn fa="download" color="primary" hint="PDF" ng-click="print()"></ze-btn>
                        <ze-btn fa="files-o" color="success" hint="Dupliquer" ng-click="transform()"></ze-btn>

                        <div class="btn-group btn-group-xs" role="group" ng-if="nb_orders > 0">
                            <button type="button" class="btn btn-default" ng-class="order_first == 0 ? 'disabled' :''" ng-click="first_order()"><span class="fa fa-fw fa-fast-backward"></span></button>
                            <button type="button" class="btn btn-default" ng-class="order_previous == 0 ? 'disabled' :''" ng-click="previous_order()"><span class="fa fa-fw fa-chevron-left"></span></button>
                            <button type="button" class="btn btn-default disabled">{{order_order}}/{{nb_orders}}</button>
                            <button type="button" class="btn btn-default" ng-class="order_next == 0 ? 'disabled' :''" ng-click="next_order()"><span class="fa fa-fw fa-chevron-right"></span></button>
                            <button type="button" class="btn btn-default" ng-class="order_last == 0 ? 'disabled' :''" ng-click="last_order()"><span class="fa fa-fw fa-fast-forward"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" ng-if="showDetailsEntreprise">
            <div class="col-md-12">
                <div class="well">
                    <strong>En cours :</strong> {{ (company.due || contact.due) | currency:'€':2 }}
                    <table class="table table-stripped table-condensed table-responsive" ng-if="(company.due_lines || contact.due_lines).length > 0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Libelle</th>
                            <th>Date limite</th>
                            <th class="text-right">Somme due</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tr ng-repeat="due_line in (company.due_lines || contact.due_lines)">
                            <td>{{ due_line.numerotation }}</td>
                            <td>{{ due_line.libelle }}</td>
                            <td>{{ due_line.date_limit | date:'dd/MM/yyyy' }}</td>
                            <td class="text-right">{{ due_line.due | currency:'€':2 }}</td>
                            <td class="text-right">
                                <a class="btn btn-xs btn-primary" ng-href="/ng/com_zeapps_crm/invoice/{{ due_line.id }}">
                                    <i class="fa fa-fw fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <ul role="tablist" class="nav nav-tabs">
            <li ng-class="navigationState =='body' ? 'active' : ''"><a href="#" ng-click="setTab('body')">Corps</a></li>
            <li ng-class="navigationState =='header' ? 'active' : ''"><a href="#" ng-click="setTab('header')">Entête</a></li>
            <li ng-class="navigationState =='condition' ? 'active' : ''"><a href="#" ng-click="setTab('condition')">Conditions</a></li>
            <li ng-class="navigationState =='activity' ? 'active' : ''"><a href="#" ng-click="setTab('activity')">Activité</a></li>
            <li ng-class="navigationState =='document' ? 'active' : ''"><a href="#" ng-click="setTab('document')">Documents</a></li>
        </ul>

        <div ng-show="navigationState =='body'">
            <div class="row">
                <div class="col-md-12 text-right">
                    <span class="form-inline">
                        <label>Code produit :</label>
                        <span class="input-group">
                            <input type="text" class="form-control input-sm" ng-model="codeProduct" ng-keypress="keyEventaddFromCode($event)" >
                            <span class="input-group-addon" ng-click="addFromCode()">
                                <i class="fa fa-fw fa-plus text-success"></i>
                            </span>
                        </span>
                    </span>
                    <ze-btn fa="tags" color="success" hint="produit" always-on="true" ng-click="addLine()"></ze-btn>
                    <ze-btn fa="euro" color="info" hint="sous-total" always-on="true" ng-click="addSubTotal()"></ze-btn>
                    <ze-btn fa="commenting" color="warning" hint="commentaire" always-on="true"
                            ze-modalform="addComment"
                            data-title="Ajouter un commentaire"
                            data-template="orderCommentTplUrl"></ze-btn>
                </div>
                <div class="col-md-12">
                    <table class="table table-striped table-condensed table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Désignation</th>
                                <th class="text-right">Qte</th>
                                <th class="text-right">P. Unit. HT</th>
                                <th class="text-right">Taxe</th>
                                <th class="text-right">Remise</th>
                                <th class="text-right">Montant HT</th>
                                <th class="text-right">Montant TTC</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody ui-sortable="sortable" class="sortableContainer" ng-model="lines">
                            <tr ng-repeat="line in lines" ng-class="[line.type == 'subTotal' ? 'sous-total info' : '', line.type == 'comment' ? 'warning' : '']" data-id="{{ line.id }}">

                                <td ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.ref }}
                                </td>

                                <td ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    <strong>{{ line.designation_title }} <span ng-if="line.designation_desc">:</span></strong><br>
                                    <span class="text-wrap">{{ line.designation_desc }}</span>
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.qty | number }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.price_unit | currency }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.id_taxe != 0 ? (line.value_taxe | currency:'%':2) : '' }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.discount != 0 ? ((0-line.discount) | currency:'%':2) : ''}}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.total_ht | currency:'€':2 }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.total_ttc | currency:'€':2 }}
                                </td>

                                <td colspan="6" class="text-right" ng-if="line.type == 'subTotal'">
                                    Sous-Total
                                </td>

                                <td class="text-right" ng-if="line.type == 'subTotal'">
                                    {{ subtotalHT($index) | currency:'€':2 }}
                                </td>
                                <td class="text-right" ng-if="line.type == 'subTotal'">
                                    {{ subtotalTTC($index) | currency:'€':2 }}
                                </td>

                                <td colspan="8" class="text-wrap" ng-if="line.type == 'comment'">
                                    {{ line.designation_desc }}
                                </td>

                                <td class="text-right">
                                    <ze-btn fa="pencil" color="info" direction="left" hint="editer" ng-if="line.type !== 'subTotal' && line.type !== 'comment'"
                                            ze-modalform="editLine"
                                            data-edit="line"
                                            data-title="Editer la ligne de la commande"
                                            data-template="orderLineTplUrl"></ze-btn>
                                    <ze-btn fa="pencil" color="info" direction="left" hint="editer" ng-if="line.type === 'comment'"
                                            ze-modalform="editComment"
                                            data-edit="line"
                                            data-title="Modifier un commentaire"
                                            data-template="orderCommentTplUrl"></ze-btn>
                                    <ze-btn fa="trash" color="danger" direction="left" hint="Supprimer" ng-click="deleteLine(line)" ze-confirmation></ze-btn>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <table class="table table-condensed table-striped">
                        <thead>
                        <tr>
                            <th>Base TVA</th>
                            <th class="text-right">Taux TVA</th>
                            <th class="text-right">Montant TVA</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="tva in tvas">
                            <td>{{ tva.ht | currency:'€':2 }}</td>
                            <td class="text-right">{{ tva.value_taxe | currency:'%':2 }}</td>
                            <td class="text-right">{{ tva.value | currency:'€':2 }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-5 col-md-offset-2">
                    <div class="well well-sm">
                        <div ng-if="order.total_discount > 0">
                            <div class="row">
                                <div class="col-md-6">
                                    Total HT av remise
                                </div>
                                <div class="col-md-6 text-right">
                                    {{ order.total_prediscount_ht | currency:'€':2 }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    Total TTC av remise
                                </div>
                                <div class="col-md-6 text-right">
                                    {{ order.total_prediscount_ttc | currency:'€':2 }}
                                </div>
                            </div>
                            <hr>

                            <div class="row" ng-if="order.global_discount > 0">
                                <div class="col-md-6">
                                    Remise globale
                                </div>
                                <div class="col-md-6 text-right">
                                    -{{ order.global_discount | number:2 }}%
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    Total remises HT
                                </div>
                                <div class="col-md-6 text-right">
                                    {{ order.total_discount | currency:'€':2 }}
                                </div>
                            </div>

                            <hr>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                Total HT
                            </div>
                            <div class="col-md-6 text-right">
                                {{ order.total_ht | currency:'€':2 }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                Total TVA
                            </div>
                            <div class="col-md-6 text-right">
                                {{ order.total_tva | currency:'€':2 }}
                            </div>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                Total TTC
                            </div>
                            <div class="col-md-6 text-right">
                                {{ order.total_ttc | currency:'€':2 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div ng-show="navigationState=='header'">
            <strong>Reference Client :</strong>
            {{ order.reference_client }}
            <br/>
            <strong>Date de création de la commande :</strong>
            {{ order.date_creation | date:'dd/MM/yyyy' }}
            <br/>
            <strong>Date de validité de la commande :</strong>
            {{ order.date_limit | date:'dd/MM/yyyy' }}
            <br/>
        </div>

        <div ng-show="navigationState=='condition'">
            <strong>Modalités de paiement :</strong>
            {{ order.label_modality }}
        </div>

        <div ng-show="navigationState=='activity'">
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-right">
                        <ze-btn data-fa="plus" data-hint="Activité" always-on="true" data-color="success"
                                ze-modalform="addActivity"
                                data-template="orderActivityTplUrl"
                                data-title="Créer une activité"></ze-btn>
                    </div>
                    <div class="card_document" ng-repeat="activity in activities | orderBy:['-date','-id']">
                        <div class="card_document-head clearfix">
                            <div class="pull-right">
                                <ze-btn data-fa="pencil" data-hint="Editer" data-direction="left" data-color="info"
                                        ze-modalform="editActivity"
                                        data-edit="activity"
                                        data-template="orderActivityTplUrl"
                                        data-title="Modifier l'activité"></ze-btn>
                                <ze-btn data-fa="trash" data-hint="Supprimer" data-direction="left" data-color="danger" ng-click="deleteActivity(activity)" ze-confirmation></ze-btn>
                            </div>
                            <strong>{{ activity.libelle }}</strong>
                        </div>
                        <div class="card_document-body" ng-if="activity.description">{{ activity.description }}</div>
                        <div class="card_document-footer text-muted">
                            Créé par <strong>{{ activity.name_user }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div ng-show="navigationState=='document'">
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-right">
                        <ze-btn data-fa="plus" data-hint="Document" always-on="true" data-color="success"
                                ze-modalform="addDocument"
                                data-template="orderDocumentTplUrl"
                                data-title="Ajouter un document"></ze-btn>
                    </div>
                    <div class="card_document" ng-repeat="document in documents | orderBy:['-date','-id']">
                        <div class="card_document-head clearfix">
                            <div class="pull-right">
                                <ze-btn data-fa="pencil" data-hint="Editer" data-direction="left" data-color="info"
                                        ze-modalform="editDocument"
                                        data-edit="document"
                                        data-template="orderDocumentTplUrl"
                                        data-title="Modifier le document"></ze-btn>
                                <ze-btn data-fa="trash" data-hint="Supprimer" data-direction="left" data-color="danger" ng-click="deleteDocument(document)" ze-confirmation></ze-btn>
                            </div>
                            <i class="fa fa-fw fa-file"></i>
                            <a ng-href="{{ document.path }}" class="text-primary" target="_blank">
                                <strong>{{ document.label }}</strong>
                            </a>
                        </div>
                        <div class="card_document-body" ng-if="document.description">{{ document.description }}</div>
                        <div class="card_document-footer text-muted">
                            Envoyé par <strong>{{ document.name_user }}</strong> le <strong>{{ document.date | date:'dd/MM/yyyy' }}</strong> à <strong>{{ document.date | date:'HH:mm' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>