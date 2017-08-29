<div class="modal-header">
    <h3 class="modal-title">{{titre}}</h3>
</div>


<div class="modal-body">
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="button" class="btn btn-xs btn-success" ng-click="showForm = !showForm">
                <i class="fa fa-fw fa-plus"></i> Produit stocké
            </button>
        </div>
    </div>
    <div class="row" ng-if="!showForm">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-condensed table-responsive" ng-if="product_stocks.length > 0">
                <thead>
                <tr>
                    <th>Référence</th>
                    <th>Libellé</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="product_stock in product_stocks">
                    <td><a href="#" ng-click="loadProductStock(product_stock)">{{ product_stock.ref }}</a></td>
                    <td><a href="#" ng-click="loadProductStock(product_stock)">{{ product_stock.label }}</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <form ng-if="showForm" name="formCtrl.productStockForm" id="productStockForm">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Référence</label>
                    <input class="form-control" type='text' ng-model="form.ref">
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label>Libellé</label>
                    <input class="form-control" type='text' ng-model="form.label">
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" ng-click="cancel()">Annuler</button>
    <button type="sumbit" class="btn btn-success" ng-click="addProductStock()" ng-if="showForm" form="productStockForm" ng-disabled='formCtrl.productStockForm.$invalid'>Creer</button>
</div>