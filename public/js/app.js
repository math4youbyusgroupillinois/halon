// app.js
var app = angular.module("halon",[ ]);

app.config(['$routeProvider',function($routeProvider){
        $routeProvider.when('/',{templateUrl:'app/partials/login.html', controller: 'loginController'})
        $routeProvider.otherwise({redirectTo:'/'})
}])

// controller.js
app.controller('loginController',function($scope){
    //controller code will be inserted here
})