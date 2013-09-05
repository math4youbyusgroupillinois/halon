// app.js
var app = angular.module("halon",['ngResource','ngSanitize']);

app.config(['$routeProvider',function($routeProvider){
  $routeProvider.when('/',{templateUrl:'app/partials/login.html', controller: 'loginController'})
  $routeProvider.when('/printers',{templateUrl:'app/partials/printers.html', controller: 'printerController'})
  $routeProvider.otherwise({redirectTo:'/'})
}])

// controller.js
app.controller('loginController',function($scope, $rootScope, $sanitize, $location, Authenticate){
  $rootScope.location = $location; // used for ActiveTab
  $scope.login = function(){
      Authenticate.save({
          'password': $sanitize($scope.password)
      },function() {
          $scope.flash = ''
          $location.path('/printers');
      },function(response){
          $scope.flash = response.data.flash
      })
  }
})

app.controller('printerController',function($scope, $rootScope, $location){
  $rootScope.location = $location; // used for ActiveTab
})

//services.js
app.factory('Authenticate', function($resource){
    return $resource("/service/authenticate");
})