app.directive("zeappsHappylittletree", [
		function(){
			return{
				restrict: "E",
				replace: true,
				scope: {
					tree: "=",
					update_f: "&update"
				},
				template: function(elm, attrs){
					if(attrs.class)
						attrs.class += " tree list-unstyled";
					else
						attrs.class = "tree list-unstyled";

					return 	"<ul "+attrs.text+">"+
                        		"<branch ng-repeat='branch in tree' data-branch='branch' data-update='update_f()'></branch>" +
							"</ul>";
				}
			};
		}
	])

	.directive("branch",["$compile",
		function($compile){
			return{
				restrict: "E",
				replace: true,
				scope: {
					branch: "=",
                    update_f: "&update"
				},
				template:   "<li class='branch' ng-class='{\"open\": isOpen(), \"text-muted\": !hasBranches() && !hasLeaves()}'>" +
								"<span class='branch-name text-capitalize'>" +
									"<i class='fa fa-lg fa-caret-right pull-left' aria-hidden='true' ng-click='toggleBranch()' ng-hide='isOpen() || !hasBranches()'></i>" +
									"<i class='fa fa-lg fa-caret-down pull-left' aria-hidden='true' ng-click='toggleBranch()' ng-show='isOpen() && hasBranches()'></i>" +
									"<span class='branch-wrap pull-right' ng-click='openBranch()'>" +
										"<span class='fa fa-folder-o' aria-hidden='true'></span>" +
										" {{ branch.name }}" +
									"</span>" +
								"</span>" +
							"</li>",
				link: function($scope, element){
					if(angular.isArray($scope.branch.branches)){
						var html = "<zeapps-happylittletree data-tree='branch.branches' data-update='update_f()'></zeapps-happylittletree>";
						$compile(html)($scope, function(cloned){
							element.append(cloned);
						});
					}

					var current = 0;

                    $scope.toggleBranch = toggleBranch;
                    $scope.openBranch = openBranch;
                    $scope.hasBranches = hasBranches;
                    $scope.hasLeaves = hasLeaves;
                    $scope.isOpen = isOpen;

					function toggleBranch(){
						$scope.branch.open = !$scope.branch.open;
					}

					function openBranch(){
						$scope.update_f()($scope.branch);
					}

					function hasBranches(){
						return angular.isArray($scope.branch.branches);
					}

					function hasLeaves(){
						return parseInt($scope.branch.nb_products);
					}

					function isOpen(){
						return $scope.branch.open;
					}
				}
			};
		}
	]);