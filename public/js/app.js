// app.js
var app = angular.module("halon",['ui.bootstrap','ngResource','ngSanitize', 'angularFileUpload']);

app.config(['$routeProvider',function($routeProvider){
  $routeProvider.when('/login',{templateUrl:'app/partials/login.html', controller: 'loginController'});
  $routeProvider.when('/printer/locations',{templateUrl:'app/partials/locations.html', controller: 'locationController'});
  $routeProvider.when('/printer/alternate',{templateUrl:'app/partials/printer.html', controller: 'alternatePrinterController'});
  $routeProvider.when('/admin/locations',{templateUrl:'app/partials/admin/locations.html', controller: 'locationAdminController'});
  $routeProvider.when('/admin/manage',{templateUrl:'app/partials/admin/users.html', controller: 'userController'});
  $routeProvider.when('/',{templateUrl:'app/partials/dashboard.html', controller: 'dashboardController'});
  $routeProvider.when('/locations',{templateUrl:'app/partials/locations.html', controller: 'publicLocationController'});
  $routeProvider.when('/admin',{redirectTo:'/admin/locations'});
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
          delete sessionStorage.userRole;
          $location.path('/login');
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

app.run(function($rootScope, FlashService){
  $rootScope.$on("$routeChangeStart",
    function (event, current, previous, rejection) {
      if (previous) {
        if (previous.$$route.controller != "loginController") {
          FlashService.clear();
        }
      }
    });
});

app.directive('humanReadableBoolean', function() {
  return {
    restrict: 'E',
    replace: true,
    template: '<span>{{humanReadable(status)}}</span>',
    scope: {
      status: '=',
    },
    controller: function($scope) {
      $scope.humanReadable = function(val) {
        return val ? "Successful" : "Failed";
      }
    }
  }
});

app.directive('marModifiedDate', function($filter) {
  return {
    restrict: 'E',
    replace: false,
    template: '<span ng-show="date">{{ date | date:\'short\' }}</span><span ng-hide="date">File not found: {{fileName}}</span>',
    scope: {
      date: '=',
      fileName: '='
    }
  }
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
