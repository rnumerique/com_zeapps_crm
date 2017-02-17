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
                        Commande :
                        <span ng-hide="edit">{{ order.libelle }}</span>
                        <input type="text" class="form-control" ng-model="order.libelle" ng-show="edit">
                    </div>
                    <div>
                        n° : {{ order.numerotation }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary btn-xs" ng-click="back()"><span class="fa fa-fw fa-arrow-left"></span></button>
                        <button type="button" class="btn btn-info btn-xs" ng-click="toggleEdit()"><i class="fa fa-fw fa-pencil" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-primary btn-xs" ng-click="print()"><i class="fa fa-fw fa-download" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-success btn-xs" ng-click="createInvoice()" i8n="Creer une facture a partir de la commande"></button>

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
            <div class="row">
                <div class="col-md-12" ng-if="company">
                    <strong>Entreprise :</strong> {{ company.company_name }}<br>
                    <strong>En cours :</strong> {{ company.due | currency:'€':2 }}
                    <button type="button" class="btn btn-xs btn-info" ng-click="showDetailsEntreprise = !showDetailsEntreprise">
                        {{ showDetailsEntreprise ? 'masquer' : 'détails' }}
                    </button>
                    <table class="table table-stripped table-condensed table-responsive" ng-if="showDetailsEntreprise">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Libelle</th>
                            <th>Date limite</th>
                            <th class="text-right">Somme due</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tr ng-repeat="due_line in company.due_lines">
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
                <div class="col-md-12" ng-if="contact && !company">
                    <strong>Contact :</strong> {{ contact.last_name + ' ' + contact.first_name }}<br>
                    <strong>En cours :</strong> {{ contact.due | currency:'€':2 }}
                    <button type="button" class="btn btn-xs btn-info" ng-click="showDetailsContact = !showDetailsContact">détails</button>
                    <table class="table table-stripped table-condensed table-responsive" ng-if="showDetailsContact">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Libelle</th>
                            <th>Date limite</th>
                            <th class="text-right">Somme due</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tr ng-repeat="due_line in contact.due_lines">
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

        <div class="row">
            <div class="col-md-6">
                <div class="well">
                    <strong>Adresse de facturation :</strong><br>
                    {{ company.company_name }}<br ng-if="company.company_name">
                    {{ contact.last_name + ' ' + contact.first_name }}<br ng-if="contact.last_name || contact.first_name">
                    <span ng-hide="edit">{{ order.billing_address_1 }} <span ng-show="order.billing_address_1"></span></span><br ng-if="order.billing_address_1 && !edit">
                    <input type="text" class="form-control" ng-model="order.billing_address_1" ng-show="edit">
                    <span ng-hide="edit">{{ order.billing_address_2 }} <span ng-show="order.billing_address_2"></span></span><br ng-if="order.billing_address_2 && !edit">
                    <input type="text" class="form-control" ng-model="order.billing_address_2" ng-show="edit">
                    <span ng-hide="edit">{{ order.billing_address_3 }} <span ng-show="order.billing_address_3"></span></span><br ng-if="order.billing_address_3 && !edit">
                    <input type="text" class="form-control" ng-model="order.billing_address_3" ng-show="edit">
                    <span ng-hide="edit">{{ order.billing_zipcode + ' ' + order.billing_city }}</span>
                    <input type="text" class="form-control" ng-model="order.billing_zipcode" ng-show="edit">
                    <input type="text" class="form-control" ng-model="order.billing_city" ng-show="edit">
                </div>
            </div>

            <div class="col-md-6">
                <div class="well">
                    <strong>Adresse de livraison :</strong><br>
                    {{ company.company_name }}<br ng-if="company.company_name">
                    {{ contact.last_name + ' ' + contact.first_name }}<br ng-if="contact.last_name && contact.first_name">
                    <span ng-hide="edit">{{ order.delivery_address_1 }} <span ng-show="order.delivery_address_1"></span></span><br ng-if="order.delivery_address_1 && !edit">
                    <input type="text" class="form-control" ng-model="order.delivery_address_1" ng-show="edit">
                    <span ng-hide="edit">{{ order.delivery_address_2 }} <span ng-show="order.delivery_address_2"></span></span><br ng-if="order.delivery_address_2 && !edit">
                    <input type="text" class="form-control" ng-model="order.delivery_address_2" ng-show="edit">
                    <span ng-hide="edit">{{ order.delivery_address_3 }} <span ng-show="order.delivery_address_3"></span></span><br ng-if="order.delivery_address_3 && !edit">
                    <input type="text" class="form-control" ng-model="order.delivery_address_3" ng-show="edit">
                    <span ng-hide="edit">{{ order.delivery_zipcode + ' ' + order.delivery_city }}</span>
                    <input type="text" class="form-control" ng-model="order.delivery_zipcode" ng-show="edit">
                    <input type="text" class="form-control" ng-model="order.delivery_city" ng-show="edit">
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

            <li data-more-menu-item><a class="btn btn-primary"><span class="fa fa-fw fa-menu-down"></span></a>
            </li>
        </ul>


        <div ng-show="navigationState=='body'">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-success btn-xs" ng-click="addLine()">
                        <span class="fa fa-fw fa-tags"></span> produit
                    </button>

                    <!-- HOOK comZeappsCrm_OrderHook -->
                    <span ng-repeat="hook in hooks | orderBy:'sort'" ng-include="hook.template"></span>

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
                                <th></th>
                            </tr>
                        </thead>
                        <tbody ui-sortable="sortable" class="sortableContainer" ng-model="lines">
                            <tr ng-repeat="line in lines" ng-class="[line.type == 'subTotal' ? 'sous-total info' : '', line.type == 'comment' ? 'warning' : '']" data-id="{{ line.id }}">

                                <td ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.ref }}
                                </td>

                                <td ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    <strong>{{ line.designation_title }} :</strong><br>
                                    {{ line.designation_desc }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    <span ng-hide="line.edit">{{ line.qty | number }}</span>
                                    <input type="text" class="form-control" ng-model="line.qty" ng-show="line.edit" ng-change="updateSums(line)">
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.price_unit | currency:'€':2 }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    {{ line.taxe != 0 ? (line.taxe | currency:'%':2) : '' }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    <span ng-hide="line.edit">{{ line.discount != 0 ? ((0-line.discount) | currency:'%':2) : ''}}</span>
                                    <div class="input-group" ng-show="line.edit">
                                        <input type="text" class="form-control" ng-model="line.discount" ng-change="updateSums(line)">
                                        <div class="input-group-addon">%</div>
                                    </div>
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

                                <td colspan="8" ng-if="line.type == 'comment'">
                                    {{ line.designation_desc }}
                                </td>

                                <td class="text-right">
                                    <button type="button" class="btn btn-info btn-xs" ng-click="editLine(line)" ng-hide="line.type == 'subTotal' || line.type == 'comment' || line.type == 'abonnement' || line.edit">
                                        <span class="fa fa-fw fa-pencil"></span>
                                    </button>
                                    <button type="button" class="btn btn-success btn-xs" ng-click="submitLine(line)" ng-hide="line.type == 'subTotal' || line.type == 'comment' || !line.edit">
                                        <span class="fa fa-fw fa-check"></span>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-xs" ng-click="deleteLine(line)">
                                        <span class="fa fa-fw fa-trash"></span>
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

                        <div class="row">
                            <div class="col-md-6">
                                Remise Globale
                            </div>
                            <div class="col-md-6 text-right">
                                <span ng-hide="edit">-{{ order.global_discount | number:2 }}%</span>
                                <div class="input-group" ng-show="edit">
                                    <input type="text" class="form-control" ng-model="order.global_discount">
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                Remise (avant Taxes)
                            </div>
                            <div class="col-md-6 text-right">
                                {{ order.total_discount | currency:'€':2 }}
                            </div>
                        </div>

                        <hr>

                        <div class="row total">
                            <div class="col-md-6">
                                Total HT
                            </div>
                            <div class="col-md-6 text-right">
                                {{ order.total_ht | currency:'€':2 }}
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
            <div ng-hide="edit">{{ order.reference_client }}</span></div>
            <input type="text" class="form-control" ng-model="order.reference_client" ng-show="edit">
            <br/>
            <strong>Date de création de la commande :</strong>
            <div ng-hide="edit">{{ order.date_creation | date:'dd/MM/yyyy' }}</div>
            <input type="date" class="form-control" ng-model="order.date_creation" ng-show="edit">
            <br/>
            <strong>Date de validité de la commande :</strong>
            <div ng-hide="edit">{{ order.date_limit | date:'dd/MM/yyyy' }}</div>
            <input type="date" class="form-control" ng-model="order.date_limit" ng-show="edit">
            <br/>
        </div>

        <div ng-show="navigationState=='condition'">
            <strong>Modalités de paiement :</strong>
            <div ng-hide="edit">{{ order.modalities }}</div>
            <select ng-model="order.modalities" class="form-control" ng-show="edit">
                <option ng-repeat="modality in modalities">
                    {{ modality.label }}
                </option>
            </select>
            <br/>
        </div>

        <div ng-show="navigationState=='activity'">
            <div class="row">
                <div class="col-md-12 text-right">
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
                            <input type="date" class="form-control" ng-model="activity.reminder">
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
                        <strong>
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
                <div class="col-md-12 text-right">
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
                        <button type="button" class="btn btn-danger btn-xs" ng-click="deleteDocument(document)">
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