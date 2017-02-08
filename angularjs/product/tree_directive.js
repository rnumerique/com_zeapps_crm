app.directive('zeappsHappylittletree',
    function(){
        return{
            restrict: 'E',
            replace: true,
            scope: {
                tree: '=',
                activeBranch: '='
            },
            template:   '<ul class="tree list-unstyled">' +
                            '<branch ng-repeat="branch in tree" data-branch="branch" data-active-branch="activeBranch"></branch>' +
                        '</ul>'
        }
})

.directive('branch', function($compile, $rootScope){
    return{
        restrict: 'E',
        replace: true,
        scope: {
            branch: '=',
            activeBranch: '='
        },
        template:   "<li class='branch' ng-class='{\"open\": isOpen(), \"text-muted\": !hasBranches() && !hasLeaves()}'>" +
                        "<span class='branch-name text-capitalize'>" +
                            "<i class='fa fa-lg fa-caret-right pull-left' aria-hidden='true' ng-click='toggleBranch()' ng-hide='isOpen() || !hasBranches()'></i>" +
                            "<i class='fa fa-lg fa-caret-down pull-left' aria-hidden='true' ng-click='toggleBranch()' ng-show='isOpen() && hasBranches()'></i>" +
                            "<span class='branch-wrap pull-right' ng-class='{\"selected\": isCurrent(branch.id)}' ng-click='openBranch()'>" +
                                "<span class='fa fa-folder-o' aria-hidden='true'></span>" +
                                " {{ branch.name }}" +
                            "</span>" +
                        "</span>" +
                    "</li>",
        link: function(scope, element, attrs){
            if(angular.isArray(scope.branch.branches)){
                $compile("<zeapps-happylittletree data-tree='branch.branches' data-active-branch='activeBranch'></zeapps-happylittletree>")(scope, function(cloned, scope){
                    element.append(cloned);
                });     
            }


            scope.toggleBranch = function(){
                scope.branch.open = !scope.branch.open;
            };

            scope.openBranch = function(){
                scope.activeBranch.data = scope.branch;
            };

            scope.hasBranches = function(){
                return angular.isArray(scope.branch.branches);
            };

            scope.hasLeaves = function(){
                return parseInt(scope.branch.nb_products);
            };

            scope.isOpen = function(){
                return scope.branch.open;
            };

            scope.isCurrent = function(id){
                return id == scope.activeBranch.data.id;
            };
        }
    }
});