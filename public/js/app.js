// app.js
var app = angular.module("halon",['ngResource','ngSanitize']);

app.config(['$routeProvider',function($routeProvider){
  $routeProvider.when('/',{templateUrl:'app/partials/login.html', controller: 'loginController'});
  $routeProvider.when('/locations',{templateUrl:'app/partials/locations.html', controller: 'locationController'});
  $routeProvider.otherwise({redirectTo:'/'});
}]);

// controller.js

app.controller('navController', function($scope, $location, Authenticate){
  $scope.logout = function(){
    Authenticate.get({}, function() {
      delete sessionStorage.authenticated;
      $location.path('/');
    })
  }
})


app.controller('loginController',function($scope, $rootScope, $sanitize, $location, Authenticate){
  $rootScope.location = $location; // used for ActiveTab
  $scope.login = function(){
      Authenticate.save({
          'password': $sanitize($scope.password)
      },function(data) {
          $scope.flash = '';
          $location.path('/locations');
          sessionStorage.authenticated = true;
      },function(response){
          $scope.flash = response.data.flash;
      })
  }
})

app.controller('locationController',function($scope, $rootScope, $location, Authenticate){
  $rootScope.location = $location; // used for ActiveTab
})

//factories.js
app.factory('Authenticate', function($resource){
    return $resource("/service/authenticate");
})

//services.js
