// app.js
var app = angular.module("halon",['ngResource','ngSanitize']);

app.config(['$routeProvider',function($routeProvider){
  $routeProvider.when('/',{templateUrl:'app/partials/login.html', controller: 'loginController'});
  $routeProvider.when('/locations',{templateUrl:'app/partials/locations.html', controller: 'locationController'});
  $routeProvider.otherwise({redirectTo:'/'});
}]);

// controller.js

app.controller('navController', function($scope, $location, Authenticate, FlashService){
  $scope.authenticated = function() {
    return sessionStorage.authenticated;
  }
  $scope.logout = function(){
    Authenticate.get({}, function() {
      delete sessionStorage.authenticated;
      $location.path('/');
      FlashService.clear();
    })
  }
})


app.controller('loginController',function($scope, $rootScope, $sanitize, $location, Authenticate, FlashService){
  $rootScope.location = $location; // used for ActiveTab
  $scope.login = function(){
      Authenticate.save({
          'password': $sanitize($scope.password)
      },function(data) {
          $location.path('/locations');
          sessionStorage.authenticated = true;
          FlashService.show('Succesfully Logged In');
      },function(response){
          FlashService.show(response.data.flash);
      })
  }
})

app.controller('locationController',function($scope, $rootScope, $location, Authenticate, Location){
  $rootScope.location = $location; // used for ActiveTab
  Location.get({},function(data) {
    $scope.locations = data;
  })
})

//factories.js
app.factory('Authenticate', function($resource){
    return $resource("/service/authenticate");
});

app.factory('Location', function($resource){
    return $resource("/locations");
})

app.factory("FlashService", function($rootScope) {
  return {
    show: function(message) {
      $rootScope.flash = message;
    },
    clear: function() {
      $rootScope.flash = "";
    }
  }
});
