<div class="modal-header">
    <h3 class="modal-title">{{titre}}</h3>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <h4>Documents créés lors de la duplication :</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-condensed table-hover table-responsive">
                <tbody>
                <tr ng-show="documents.quotes">
                    <td>
                        Devis n°{{documents.quotes.numerotation}}
                    </td>
                    <td></td>
                    <td class="text-center">
                        <ze-btn fa="eye" color="info" hint="Consulter" always-on="true" ng-click="goTo('quote', documents.quotes.id)"></ze-btn>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="download" color="primary" hint="Obtenir le PDF" always-on="true" ng-click="pdf('quote', documents.quotes.id)"></ze-btn>
                    </td>
                </tr>
                <tr ng-show="documents.orders">
                    <td>
                        Commande n°{{documents.orders.numerotation}}
                    </td>
                    <td></td>
                    <td class="text-center">
                        <ze-btn fa="eye" color="info" hint="Consulter" always-on="true" ng-click="goTo('order', documents.orders.id)"></ze-btn>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="download" color="primary" hint="Obtenir le PDF" always-on="true" ng-click="pdf('order', documents.orders.id)"></ze-btn>
                    </td>
                </tr>
                <tr ng-show="documents.deliveries">
                    <td>
                        Bon de livraison n°{{documents.deliveries.numerotation}}
                    </td>
                    <td></td>
                    <td class="text-center">
                        <ze-btn fa="eye" color="info" hint="Consulter" always-on="true" ng-click="goTo('delivery', documents.deliveries.id)"></ze-btn>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="download" color="primary" hint="Obtenir le PDF" always-on="true" ng-click="pdf('delivery', documents.deliveries.id)"></ze-btn>
                    </td>
                </tr>
                <tr ng-show="documents.invoices">
                    <td>
                        Facture n°{{documents.invoices.numerotation || "non cloturée"}}
                    </td>
                    <td>
                        <ze-btn fa="lock" color="warning" hint="Cloturer" always-on="true" ng-click="finalize(documents.invoices.id)" ng-if="!documents.invoices.numerotation"></ze-btn>
                    </td>
                    <td class="text-center">
                        <ze-btn fa="eye" color="info" hint="Consulter" always-on="true" ng-click="goTo('invoice', documents.invoices.id)"></ze-btn>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="download" color="primary" hint="Obtenir le PDF" always-on="true" ng-click="pdf('invoice', documents.invoices.id)"></ze-btn>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" ng-click="cancel()">Annuler</button>
</div>