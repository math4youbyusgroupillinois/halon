// app.js
var app = angular.module("halon",['ui.bootstrap','ngResource','ngSanitize']);

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
      FlashService.add('success', 'Successfully Signed Out')
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
          FlashService.add('success', 'Succesfully Logged In');
      },function(response){
          FlashService.add('danger', response.data.flash);
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

app.factory('FlashService', ['$rootScope', function($rootScope) {
  $rootScope.alerts = [];
  var alertService = {
    add: function(type, msg) {
      $rootScope.alerts = []; // Temp Fix to clear alerts for every new alert
      $rootScope.alerts.push({ type: type, msg: msg, close: function(){alertService.closeAlert(this)}});
    },
    closeAlert: function(alert) {
      this.closeAlertIdx($rootScope.alerts.indexOf(alert));
    },
    closeAlertIdx: function(index) {
      $rootScope.alerts.splice(index, 1);
    }
  }
  return alertService;
}]);

