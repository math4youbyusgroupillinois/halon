// app.js
var app = angular.module("halon",['ui.bootstrap','ngResource','ngSanitize']);

app.config(['$routeProvider',function($routeProvider){
  $routeProvider.when('/',{templateUrl:'app/partials/login.html', controller: 'loginController'});
  $routeProvider.when('/locations',{templateUrl:'app/partials/locations.html', controller: 'locationController'});
  $routeProvider.when('/admin/manage',{templateUrl:'app/partials/users.html', controller: 'userController'});
  $routeProvider.otherwise({redirectTo:'/'});
}]);

app.config(function($httpProvider){
  var interceptor = function($rootScope,$location,$q,FlashService){
  var success = function(response){
      return response;
  }
  var error = function(response){
      if (response.status == 401){
          delete sessionStorage.authenticated;
          $location.path('/');
          FlashService.add('danger', response.data.flash);
      }
      return $q.reject(response);
  }
      return function(promise){
          return promise.then(success, error);
      }
  }
  $httpProvider.responseInterceptors.push(interceptor);
});

app.run(function($http,CSRF_TOKEN){
  $http.defaults.headers.common['csrf_token'] = CSRF_TOKEN;
});

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
});


app.controller('loginController',function($scope, $rootScope, $sanitize, $location, Authenticate, FlashService){
  $rootScope.location = $location; // used for ActiveTab
  $scope.login = function(){
    Authenticate.save({
      'role': $sanitize($scope.role),
      'password': $sanitize($scope.password)
      },function(data) {
        $location.path('/locations');
        sessionStorage.authenticated = true;
        FlashService.add('success', 'Succesfully Logged In');
      },function(response){
        FlashService.add('danger', response.data.flash);
      }
    );
  };
});

app.controller('locationController',function($scope, $rootScope, $location, Authenticate, Location){
  $rootScope.location = $location; // used for ActiveTab
  Location.query({},function(data) {
    $scope.locations = data;
  });
  $scope.addLocation = function() {
    Location.save({
      'description': $scope.newLocation.description,
      'phone_number': $scope.newLocation.phoneNumber,
      'printer_name': $scope.newLocation.printerName,
      'mar_file_name': $scope.newLocation.marFileName
    },function(data) {
      $scope.locations.push(data);
    });
  };
});

app.controller('userController',function($scope, $rootScope, $sanitize, $location, $resource, Authenticate, User, PasswordService){
  $rootScope.location = $location; // used for ActiveTab
  $scope.users = User.query();

 $scope.regenerate = function() {
  var random_pass = PasswordService.generate(12);
  var usr = $scope.users[this.$index];
  usr.password = $sanitize(random_pass);
  usr.$update({}, function() {
    alert("Password Reset to: " + random_pass);
  })
 }
});

//factories.js
app.factory('Authenticate', function($resource){
    return $resource("/service/authenticate");
});

app.factory('Location', function($resource){
    return $resource("/locations");
});

app.factory('User', function($resource){
    return $resource('/users/:userId', {userId:'@id'}, {update: { method: 'PUT' }});
});

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

app.factory('PasswordService', function () {
  // ref: http://stackoverflow.com/questions/1497481/javascript-password-generator
  var features = {
    generate: function(plength){
      var keylistalpha_low ="abcdefghijklmnopqrstuvwxyz";
      var keylistalpha = keylistalpha_low + keylistalpha_low.toUpperCase();
      var keylistint="123456789";
      var keylistspec="!@#_";
      var temp='';
      var len = plength/2;
      var len = len - 1;
      var lenspec = plength-len-len;

      for (i=0;i<len;i++)
          temp+=keylistalpha.charAt(Math.floor(Math.random()*keylistalpha.length));

      for (i=0;i<lenspec;i++)
          temp+=keylistspec.charAt(Math.floor(Math.random()*keylistspec.length));

      for (i=0;i<len;i++)
          temp+=keylistint.charAt(Math.floor(Math.random()*keylistint.length));

          temp=temp.split('').sort(function(){return 0.5-Math.random()}).join('');

      return temp;
    }
  }
  return features;
});

