<div class="modal-header">
    <h3 class="modal-title">{{titre}}</h3>
</div>


<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-condensed table-responsive" ng-show="invoices.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Libelle</th>
                    <th>Destinataire</th>
                    <th>Total HT (€)</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="invoice in invoices">
                    <td><a href="#" ng-click="loadInvoice(invoice)">{{invoice.numerotation}}</a></td>
                    <td><a href="#" ng-click="loadInvoice(invoice)">{{invoice.libelle}}</a></td>
                    <td>
                        <a href="#" ng-click="loadInvoice(invoice)">
                            {{invoice.company.company_name}}
                            <span ng-if="invoice.company.company_name && invoice.contact.last_name">-</span>
                            {{invoice.contact ? invoice.contact.first_name[0] + '. ' + invoice.contact.last_name : ''}}
                        </a>
                    </td>
                    <td><a href="#" ng-click="loadInvoice(invoice)">{{invoice.total_ht}}</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-danger" type="button" ng-click="cancel()">Annuler</button>
</div>