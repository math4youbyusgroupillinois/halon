app.factory('Authenticate', function($resource, $sanitize, $location, FlashService){
  var r = $resource("index.php/service/authenticate");

  r.login = function(credentials, callerSuccess, callerFailure) {
    var success = function(data) {
      sessionStorage.authenticated = true;
      sessionStorage.userRole = data['user']['role'];
      
      callerSuccess(data);
    };

    var failure = function(response) {
      callerFailure(response);
    }

    this.save({
      'role': $sanitize(credentials.role),
      'password': $sanitize(credentials.password)
    }, success, failure);
  }

  r.permit = function(role) {
    return role == sessionStorage.userRole;
  };

  r.isAdmin = function(role) {
    return sessionStorage.userRole == 'admin';
  };

  r.isAuthenticated = function() {
    return sessionStorage.authenticated;
  };

  r.logout = function(callerSuccess, callerFailure) {
    var success = function() {
      delete sessionStorage.authenticated;
      delete sessionStorage.userRole;
      callerSuccess();
    };

    var failure = function() {
      callerFailure();
    };

    this.get({}, success, failure);
  }

  r.redirectToLogin = function() {
    var success = function() {
      delete sessionStorage.authenticated;
      delete sessionStorage.userRole;
      $location.path('/login');
      FlashService.add('success', 'Successfully redirected to login page');
    };

    var failure = function () {
      FlashService.add('danger', 'Failed to Redirect');
    };

    this.get({}, success, failure);
  }

  return r;
});

app.factory('AdminLocation', function($resource){
    return $resource("index.php/admin/locations/:id", {id: '@id'}, {update: { method:'PUT' }, create: { method:'POST' }});
});

app.factory('PublicLocation', function($resource){
    return $resource("index.php/locations/:id", {id: '@id'});
});

app.factory('PrinterLocation', function($resource){
    return $resource("index.php/printer/locations/:id", {id: '@id'});
});

app.factory('User', function($resource){
    return $resource('index.php/users/:userId', {userId:'@id'}, {update: { method: 'PUT' }});
});

app.factory('PrintJobCollection', function($resource){
  return $resource("index.php/print_jobs", {}, {create: { method:'POST' }});
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

app.factory('PrintStatusService', function() {
  var displayService = {
    displayStatus: function(last_print_job) {
      printStatus = null;
      if (last_print_job) {
        if (last_print_job.is_enque_successful) {
          printStatus = "Successful";
        } else {
          printStatus = "Failed";
        }
      }
      return printStatus;
    }
  }
  return displayService;
});

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
