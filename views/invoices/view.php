<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="breadcrumb">Devis</div>
<div id="content">


    <form>
        <div class="well">
            <div class="row">
                <div class="col-md-6">
                    <div class="titleWell">
                        Facture :
                        <span ng-hide="edit" ng-click="toggleEdit()" class="editable">{{ invoice.libelle }} <span class="glyphicon glyphicon-pencil" ng-hide="invoice.finalized !== '0'"></span></span>
                        <input type="text" class="form-control" ng-model="invoice.libelle" ng-show="edit">
                    </div>
                    <div>
                        n° : {{ invoice.numerotation }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary btn-xs" ng-click="back()"><span class="glyphicon glyphicon-arrow-left"></span></button>
                        <button type="button" class="btn btn-info btn-xs" ng-click="print()"><i class="fa fa-download" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-success btn-xs" ng-click="finalizeInvoice()" ng-hide="invoice.finalized !== '0'" i8n="Finaliser la facture"></button>

                        <div class="btn-group btn-group-xs" role="group" ng-if="nb_invoices > 0">
                            <button type="button" class="btn btn-default" ng-class="invoice_first == 0 ? 'disabled' :''" ng-click="first_invoice()"><span class="glyphicon glyphicon-fast-backward"></span></button>
                            <button type="button" class="btn btn-default" ng-class="invoice_previous == 0 ? 'disabled' :''" ng-click="previous_invoice()"><span class="glyphicon glyphicon-chevron-left"></span></button>
                            <button type="button" class="btn btn-default disabled">{{invoice_order}}/{{nb_invoices}}</button>
                            <button type="button" class="btn btn-default" ng-class="invoice_next == 0 ? 'disabled' :''" ng-click="next_invoice()"><span class="glyphicon glyphicon-chevron-right"></span></button>
                            <button type="button" class="btn btn-default" ng-class="invoice_last == 0 ? 'disabled' :''" ng-click="last_invoice()"><span class="glyphicon glyphicon-fast-forward"></span></button>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="well">
                    <strong>Adresse de facturation :</strong><br>
                    {{ company.company_name }}<br ng-if="company.company_name">
                    {{ contact.last_name + ' ' + contact.first_name }}<br ng-if="contact.last_name || contact.first_name">
                    <span ng-hide="edit" ng-click="toggleEdit()" class="editable">{{ invoice.billing_address_1 }} <span class="glyphicon glyphicon-pencil" ng-show="invoice.billing_address_1 && invoice.finalized === '0'"></span></span><br ng-if="invoice.billing_address_1 && !edit">
                    <input type="text" class="form-control" ng-model="invoice.billing_address_1" ng-show="edit">
                    <span ng-hide="edit" ng-click="toggleEdit()" class="editable">{{ invoice.billing_address_2 }} <span class="glyphicon glyphicon-pencil" ng-show="invoice.billing_address_2 && invoice.finalized === '0'"></span></span><br ng-if="invoice.billing_address_2 && !edit">
                    <input type="text" class="form-control" ng-model="invoice.billing_address_2" ng-show="edit">
                    <span ng-hide="edit" ng-click="toggleEdit()" class="editable">{{ invoice.billing_address_3 }} <span class="glyphicon glyphicon-pencil" ng-show="invoice.billing_address_3 && invoice.finalized === '0'"></span></span><br ng-if="invoice.billing_address_3 && !edit">
                    <input type="text" class="form-control" ng-model="invoice.billing_address_3" ng-show="edit">
                    <span ng-hide="edit" ng-click="toggleEdit()" class="editable">{{ invoice.billing_zipcode + ' ' + invoice.billing_city }} <span class="glyphicon glyphicon-pencil" ng-hide="invoice.finalized !== '0'"></span></span>
                    <input type="text" class="form-control" ng-model="invoice.billing_zipcode" ng-show="edit">
                    <input type="text" class="form-control" ng-model="invoice.billing_city" ng-show="edit">
                </div>
            </div>

            <div class="col-md-6">
                <div class="well">
                    <strong>Adresse de livraison :</strong><br>
                    {{ company.company_name }}<br ng-if="company.company_name">
                    {{ contact.last_name + ' ' + contact.first_name }}<br ng-if="contact.last_name && contact.first_name">
                    <span ng-hide="edit" ng-click="toggleEdit()" class="editable">{{ invoice.delivery_address_1 }} <span class="glyphicon glyphicon-pencil" ng-show="invoice.delivery_address_1 && invoice.finalized === '0'"></span></span><br ng-if="invoice.delivery_address_1 && !edit">
                    <input type="text" class="form-control" ng-model="invoice.delivery_address_1" ng-show="edit">
                    <span ng-hide="edit" ng-click="toggleEdit()" class="editable">{{ invoice.delivery_address_2 }} <span class="glyphicon glyphicon-pencil" ng-show="invoice.delivery_address_2 && invoice.finalized === '0'"></span></span><br ng-if="invoice.delivery_address_2 && !edit">
                    <input type="text" class="form-control" ng-model="invoice.delivery_address_2" ng-show="edit">
                    <span ng-hide="edit" ng-click="toggleEdit()" class="editable">{{ invoice.delivery_address_3 }} <span class="glyphicon glyphicon-pencil" ng-show="invoice.delivery_address_3 && invoice.finalized === '0'"></span></span><br ng-if="invoice.delivery_address_3 && !edit">
                    <input type="text" class="form-control" ng-model="invoice.delivery_address_3" ng-show="edit">
                    <span ng-hide="edit" ng-click="toggleEdit()" class="editable">{{ invoice.delivery_zipcode + ' ' + invoice.delivery_city }} <span class="glyphicon glyphicon-pencil" ng-hide="invoice.finalized !== '0'"></span></span>
                    <input type="text" class="form-control" ng-model="invoice.delivery_zipcode" ng-show="edit">
                    <input type="text" class="form-control" ng-model="invoice.delivery_city" ng-show="edit">
                </div>
            </div>
        </div>


        <ul df-tab-menu menu-control="{{navigationState}}" theme="bootstrap" role="tablist"
            class="df-tab-menu nav nav-tabs">
            <li data-menu-item="body"><a href="#" data-ng-click="navigationState = 'body'">Corps</a></li>
            <li data-menu-item="header"><a href="#" data-ng-click="navigationState = 'header'">Entête</a></li>
            <li data-menu-item="condition"><a href="#" data-ng-click="navigationState = 'condition'">Conditions</a></li>
            <li data-menu-item="activity"><a href="#" data-ng-click="navigationState = 'activity'">Activité</a></li>
            <li data-menu-item="document"><a href="#" data-ng-click="navigationState = 'document'">Documents</a></li>

            <li data-more-menu-item><a class="btn btn-primary"><span class="glyphicon glyphicon-menu-down"></span></a>
            </li>
        </ul>


        <div ng-show="navigationState=='body'">
            <div class="row">
                <div class="col-md-12 text-right"  ng-hide="invoice.finalized !== '0'">
                    <button type="button" class="btn btn-success btn-xs" ng-click="addLine()">
                        <span class="fa fa-fw fa-tags"></span> produit
                    </button>
                    <button type="button" class="btn btn-info btn-xs" ng-click="addSubTotal()">
                        <span class="fa fa-fw fa-euro"></span> sous-total
                    </button>
                    <button type="button" class="btn btn-warning btn-xs" ng-click="toggleComment()">
                        <span class="fa fa-fw fa-commenting"></span> commentaire
                    </button>
                    <div ng-show="showCommentInput">
                        <div class='form-group text-left'>
                            <label>Commentaire</label>
                            <textarea class="form-control" ng-model="comment" rows="3"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-success btn-sm" ng-click="addComment()">
                                Valider
                            </button>
                        </div>
                    </div>
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
                                <th ng-hide="invoice.finalized !== '0'"></th>
                            </tr>
                        </thead>
                        <tbody ui-sortable="sortable" class="sortableContainer" ng-model="lines">
                            <tr ng-repeat="line in lines" ng-class="[line.num == 'subTotal' ? 'sous-total info' : '', line.num == 'comment' ? 'warning' : '']" data-id="{{ line.id }}">

                                <td ng-if="line.num != 'subTotal' && line.num != 'comment'">
                                    {{ line.num }}
                                </td>

                                <td ng-if="line.num != 'subTotal' && line.num != 'comment'">
                                    <strong>{{ line.designation_title }} :</strong><br>
                                    {{ line.designation_desc }}
                                </td>

                                <td class="text-right" ng-if="line.num != 'subTotal' && line.num != 'comment'">
                                    <span ng-hide="line.edit">{{ line.qty | number }}</span>
                                    <input type="text" class="form-control" ng-model="line.qty" ng-show="line.edit">
                                </td>

                                <td class="text-right" ng-if="line.num != 'subTotal' && line.num != 'comment'">
                                    {{ line.price_unit | currency:'€':2 }}
                                </td>

                                <td class="text-right" ng-if="line.num != 'subTotal' && line.num != 'comment'">
                                    {{ line.taxe | number:2 }}%
                                </td>

                                <td class="text-right" ng-if="line.num != 'subTotal' && line.num != 'comment'">
                                    <span ng-hide="line.edit">-{{ line.discount | number:2 }}%</span>
                                    <div class="input-group" ng-show="line.edit">
                                        <input type="text" class="form-control" ng-model="line.discount">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </td>

                                <td class="text-right" ng-if="line.num != 'subTotal' && line.num != 'comment'">
                                    {{ line.price_unit * line.qty | currency:'€':2 }}
                                </td>

                                <td class="text-right" ng-if="line.num != 'subTotal' && line.num != 'comment'">
                                    {{ (line.price_unit * line.qty) * ( 1 + (line.taxe / 100) ) | currency:'€':2 }}
                                </td>

                                <td colspan="6" class="text-right" ng-if="line.num == 'subTotal'">
                                    Sous-Total
                                </td>

                                <td class="text-right" ng-if="line.num == 'subTotal'">
                                    {{ subtotalHT($index) | currency:'€':2 }}
                                </td>
                                <td class="text-right" ng-if="line.num == 'subTotal'">
                                    {{ subtotalTTC($index) | currency:'€':2 }}
                                </td>

                                <td colspan="8" ng-if="line.num == 'comment'">
                                    {{ line.designation_desc }}
                                </td>

                                <td class="text-right" ng-hide="invoice.finalized !== '0'">
                                    <button type="button" class="btn btn-info btn-xs" ng-click="editLine(line)" ng-hide="line.num == 'subTotal' || line.num == 'comment' || line.edit">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                    </button>
                                    <button type="button" class="btn btn-success btn-xs" ng-click="submitLine(line)" ng-hide="line.num == 'subTotal' || line.num == 'comment' || !line.edit">
                                        <span class="glyphicon glyphicon-ok"></span>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-xs" ng-click="deleteLine(line)">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <div class="well well-sm">
                        <div class="row">
                            <div class="col-md-6">
                                Total HT av remise
                            </div>
                            <div class="col-md-6 text-right">
                                {{ totalAvDiscountHT() | currency:'€':2 }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                Total TTC av remise
                            </div>
                            <div class="col-md-6 text-right">
                                {{ totalAvDiscountTTC() | currency:'€':2 }}
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                Remise Globale
                            </div>
                            <div class="col-md-6 text-right">
                                <span ng-hide="edit" ng-click="toggleEdit()" class="editable">-{{ invoice.global_discount | number:2 }}% <span class="glyphicon glyphicon-pencil" ng-hide="invoice.finalized !== '0'"></span></span>
                                <div class="input-group" ng-show="edit">
                                    <input type="text" class="form-control" ng-model="invoice.global_discount">
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                Remise (avant Taxes)
                            </div>
                            <div class="col-md-6 text-right">
                                {{ totalDiscount() | currency:'€':2 }}
                            </div>
                        </div>

                        <hr>

                        <div class="row total">
                            <div class="col-md-6">
                                Total HT
                            </div>
                            <div class="col-md-6 text-right">
                                {{ totalHT() | currency:'€':2 }}
                            </div>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                Total TTC
                            </div>
                            <div class="col-md-6 text-right">
                                {{ totalTTC() | currency:'€':2 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div ng-show="navigationState=='header'">
            <strong>Reference Client :</strong>
            <div ng-hide="edit" ng-click="toggleEdit()"><span class="editable">{{ invoice.reference_client }} <span class="glyphicon glyphicon-pencil" ng-hide="invoice.finalized !== '0'"></span></span></div>
            <input type="text" class="form-control" ng-model="invoice.reference_client" ng-show="edit">
            <br/>
            <strong>Date de création de la facture :</strong>
            <div ng-hide="edit" ng-click="toggleEdit()"><span class="editable">{{ invoice.date_creation | date:'dd/MM/yyyy' }} <span class="glyphicon glyphicon-pencil" ng-hide="invoice.finalized !== '0'"></span></span></div>
            <input type="date" class="form-control" ng-model="invoice.date_creation" ng-show="edit">
            <br/>
            <strong>Date de validité de la facture :</strong>
            <div ng-hide="edit" ng-click="toggleEdit()"><span class="editable">{{ invoice.date_limit | date:'dd/MM/yyyy' }} <span class="glyphicon glyphicon-pencil" ng-hide="invoice.finalized !== '0'"></span></span></div>
            <input type="date" class="form-control" ng-model="invoice.date_limit" ng-show="edit">
            <br/>
        </div>

        <div ng-show="navigationState=='condition'">
            <strong>Modalités de paiement :</strong>
            <div ng-hide="edit" ng-click="toggleEdit()"><span class="editable">{{ invoice.modalities }} <span class="glyphicon glyphicon-pencil" ng-hide="invoice.finalized !== '0'"></span></span></div>
            <input type="text" class="form-control" ng-model="invoice.modalities" ng-show="edit">
            <br/>
        </div>

        <div ng-show="navigationState=='activity'">
            <div class="row">
                <div class="col-md-12 text-right" ng-hide="invoice.finalized !== '0'">
                    <button type="button" class="btn btn-success btn-xs" ng-click="toggleActivity()">
                        <span class="fa fa-fw fa-plus"></span> activité
                    </button>
                    <div ng-show="showActivityInput" class="text-left">
                        <div class="col-md-6 form-group">
                            <label i8n="libelle"></label>
                            <input type="text" class="form-control" ng-model="activity.libelle">
                        </div>
                        <div class="col-md-6 form-group">
                            <label i8n="deadline"></label>
                            <input type="date" class="form-control" ng-model="activity.reminder" ng-required="true">
                        </div>
                        <div class='col-md-12 form-group'>
                            <label i8n="Commentaire"></label>
                            <textarea class="form-control" ng-model="activity.description" rows="3"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-default btn-xs" ng-click="closeActivity()">
                                Annuler
                            </button>
                            <button type="button" class="btn btn-success btn-sm" ng-click="addActivity()">
                                Valider
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" ng-repeat="activity in activities">
                    <div>
                        <strong class="editable">
                            {{ activity.libelle }}
                            <button type="button" class="btn btn-info btn-xs" ng-click="editActivity(activity)">
                                <span class="fa fa-fw fa-pencil"></span>
                            </button>
                            <button type="button" class="btn btn-danger btn-xs" ng-click="deleteActivity()">
                                <span class="fa fa-fw fa-trash"></span>
                            </button>
                        </strong>
                    </div>
                    <div class="text-muted">
                        {{ activity.reminder | date:'dd/MM/yyyy' }}
                    </div>
                    <p>
                        {{ activity.description }}
                    </p>
                </div>
            </div>
        </div>

        <div ng-show="navigationState=='document'">
            <div class="row">
                <div class="col-md-12 text-right" ng-hide="invoice.finalized !== '0'">
                    <button type="button" class="btn btn-xs btn-success" ngf-select="upload($files)" multiple ng-if="!progress">
                        <span class="fa fa-fw fa-plus"></span> document
                    </button>
                    <div class="progress" ng-if="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="{{ progress }}" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;" ng-style="{'width': progress + '%' }">
                            {{ progress }}%
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12" ng-repeat="document in documents">
                    <div>
                        <strong>
                            {{ document.name }}
                        </strong>
                        <button type="button" class="btn btn-danger btn-xs" ng-click="deleteDocument(document)" ng-hide="invoice.finalized !== '0'">
                            <span class="fa fa-fw fa-trash"></span>
                        </button>
                    </div>
                    <div class="text-muted">
                        <span i8n="Ajouté le"></span> {{ document.created_at | date:'dd/MM/yyyy' }}
                    </div>
                </div>
            </div>
        </div>

        <form-buttons ng-show="edit"></form-buttons>

    </form>

</div>