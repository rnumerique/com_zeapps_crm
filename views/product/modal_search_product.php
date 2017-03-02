<div class="modal-header">
    <h3 class="modal-title">{{titre}}</h3>
</div>


<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-condensed table-responsive">
                <thead>
                <tr>
                    <th></th>
                    <th>
                        Code produit<br>
                        <input type="text" class="form-control" ng-model="filtre_code_produit" ng-change="updateList()">
                    </th>
                    <th>
                        Libellé<br>
                        <input type="text" class="form-control" ng-model="filtre_libelle" ng-change="updateList()">
                    </th>
                    <th>Tarif HT</th>
                </tr>
                </thead>
                <tbody>

                <tr ng-repeat="produit in produits"> <!-- | filter:{compose : 0}-->
                    <td>
                        <i class="fa fa-tag" ng-if="produit.compose == 0"></i>
                    </td>
                    <td><a href="#" ng-click="returnProduct(produit.id)">{{produit.ref}}</a></td>
                    <td><a href="#" ng-click="returnProduct(produit.id)">{{produit.name}}</a></td>
                    <td class="text-right"><a href="#" ng-click="returnProduct(produit.id)">{{produit.price_ht | number:2}}</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-danger" type="button" ng-click="cancel()">Annuler</button>
</div>