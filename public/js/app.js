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

app.controller('locationController',function($scope, $rootScope, $location, Authenticate, Location, $log){
  $rootScope.location = $location; // used for ActiveTab
  $scope.editRecord = {};
  Location.query({},function(data) {
    locations = []
    $scope.data = data;
    for (i in data) {
      container = {
        editMode: false,
        record: data[i]
      }
      locations.push(container)
    }
    $scope.locations = locations;
  });
  $scope.addLocation = function() {
    Location.create({
      'description': $scope.newLocation.description,
      'phone_number': $scope.newLocation.phoneNumber,
      'printer_name': $scope.newLocation.printerName,
      'mar_file_name': $scope.newLocation.marFileName
    },function() {
      Location.query({},function(data) {
        locations = []
        $scope.data = data;
        for (i in data) {
          container = {
            editMode: false,
            record: data[i]
          };
          locations.push(container);
        }
        $scope.locations = locations;
      });
      $scope.newLocation = '';
    });
  };
  $scope.deleteLocation = function(collectionIndex) {
    var location = $scope.locations[collectionIndex];
    $log.info(location);
    location.record.$delete({id: location.record.id}, 
      function(){
        $scope.locations.splice(collectionIndex,1);
      },
      function() {
        alert("Failed to delete location");
      }
    )
  };
  $scope.editLocation = function(location) {
    location.editMode = true;

    $scope.editRecord.description = location.record.description;
    $scope.editRecord.phoneNumber = location.record.phone_number;
    $scope.editRecord.printerName = location.record.printer_name;
    $scope.editRecord.marFileName = location.record.mar_file_name;
  }

  $scope.onCancel = function(location) {
    location.editMode = false;
  }

  $scope.onSave = function(location) {
    location.editMode = false;

    location.record.description = $scope.editRecord.description;
    location.record.phone_number = $scope.editRecord.phoneNumber;
    location.record.printer_name = $scope.editRecord.printerName;
    location.record.mar_file_name = $scope.editRecord.marFileName;
    location.record.$update();
  }
});

app.controller('userController',function($scope, $rootScope, $sanitize, $location, $resource, Authenticate, FlashService, User, PasswordService){
  $rootScope.location = $location; // used for ActiveTab
  $scope.users = User.query();

 $scope.regenerate = function() {
  var random_pass = PasswordService.generate(12);
  var usr = $scope.users[this.$index];
  usr.password = $sanitize(random_pass);
  usr.$update({}, function() {
    var pass_msg = "Password Reset to: " + random_pass;
    FlashService.add('info', pass_msg);
  })
 }
});

//factories.js
app.factory('Authenticate', function($resource){
    return $resource("/service/authenticate");
});

app.factory('Location', function($resource){
    return $resource("/locations/:id", {id: '@id'}, {update: { method:'PUT' }});
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

// app.config(function($httpProvider){
//   // underscore logic taken from
//   // https://github.com/FineLinePrototyping/angularjs-rails-resource/blob/master/angularjs-rails-resource.js
//   $httpProvider.defaults.transformRequest = function(data) {
//     if (!angular.isString(key)) {
//       return key;
//     }

//     return key.replace(/[A-Z]/g, function (match, index) {
//         return index === 0 ? match : '_' + match.toLowerCase();
//     });
//   }
// });
