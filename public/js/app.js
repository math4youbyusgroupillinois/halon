// app.js
var app = angular.module("halon",[ ]);

app.config(['$routeProvider',function($routeProvider){
        $routeProvider.when('/',{templateUrl:'app/partials/login.html', controller: 'loginController'})
        $routeProvider.when('/printers',{templateUrl:'app/partials/printers.html', controller: 'printerController'})
        $routeProvider.otherwise({redirectTo:'/'})
}])

// controller.js
app.controller('loginController',function($scope){
    //controller code will be inserted here
})

app.controller('printerController',function($scope){
    // code
})