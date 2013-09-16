app.factory('Authenticate', function($resource){
    return $resource("/service/authenticate");
});

app.factory('Location', function($resource){
    return $resource("/admin/locations/:id", {id: '@id'}, {update: { method:'PUT' }, create: { method:'POST' }});
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
