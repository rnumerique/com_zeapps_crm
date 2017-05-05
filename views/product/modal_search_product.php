<div class="modal-header">
    <h3 class="modal-title">{{titre}}</h3>
</div>


<div class="modal-body">
    <div class="row">

        <div class="col-md-3">
            <div class="root modal-root">
                <zeapps-happylittletree
                    data-tree="tree.branches"
                    data-active-branch="activeCategory"
                </zeapps-happylittletree>
            </div>
        </div>

        <div class="col-md-9">
            <div class="col-md-5 pull-right">
                <input class="form-control" type="text" ng-model="quicksearch" placeholder="Recherche rapide">
            </div>
            <h3 class="text-capitalize active-category-title">
                {{ activeCategory.data.name }}
            </h3>
            <table class="table table-striped">
                <tr>
                    <th></th>
                    <th i8n="Référence"></th>
                    <th i8n="Nom du produit"></th>
                    <th i8n="Prix HT"></th>
                </tr>
                <tr class="leaf" ng-repeat="product in products | filter:quicksearch | orderBy: 'name'" ng-click="select_product(product)">
                    <td>
                        <i class="fa fa-tag" ng-if="product.compose == 0"></i>
                        <i class="fa fa-tags" ng-if="product.compose != 0"></i>
                    </td>
                    <td>{{ product.ref }}</td>
                    <td>{{ product.name }}</td>
                    <td>{{ product.compose == 0 ? ( product.price_ht | currency ) : '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-danger" type="button" ng-click="cancel()">Annuler</button>
</div>