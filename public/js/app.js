// app.js
var app = angular.module("halon",[ ]);

app.config(['$routeProvider',function($routeProvider){
  $routeProvider.when('/',{templateUrl:'app/partials/login.html', controller: 'loginController'})
  $routeProvider.when('/printers',{templateUrl:'app/partials/printers.html', controller: 'printerController'})
  $routeProvider.otherwise({redirectTo:'/'})
}])

// controller.js
app.controller('loginController',function($scope, $rootScope, $location){
  $rootScope.location = $location; // used for ActiveTab
})

app.controller('printerController',function($scope, $rootScope, $location){
  $rootScope.location = $location; // used for ActiveTab
})