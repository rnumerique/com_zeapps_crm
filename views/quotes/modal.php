<div class="modal-header">
    <h3 class="modal-title">{{titre}}</h3>
</div>


<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-condensed table-responsive" ng-show="quotes.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Libelle</th>
                    <th>Destinataire</th>
                    <th>Total HT (€)</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="quote in quotes">
                    <td><a href="#" ng-click="loadQuote(quote)">{{quote.numerotation}}</a></td>
                    <td><a href="#" ng-click="loadQuote(quote)">{{quote.libelle}}</a></td>
                    <td>
                        <a href="#" ng-click="loadQuote(quote)">
                            {{quote.company.company_name}}
                            <span ng-if="quote.company.company_name && quote.contact.last_name">-</span>
                            {{quote.contact ? quote.contact.first_name[0] + '. ' + quote.contact.last_name : ''}}
                        </a>
                    </td>
                    <td><a href="#" ng-click="loadQuote(quote)">{{quote.total_ht}}</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-danger" type="button" ng-click="cancel()">Annuler</button>
</div>