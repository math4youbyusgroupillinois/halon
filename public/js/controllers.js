app.controller('navController', function($scope, $location, Authenticate, FlashService, $log){
  $scope.permit = function(role) {
    return role == sessionStorage.userRole;
  }
  $scope.authenticated = function() {
    return sessionStorage.authenticated;
  }
  $scope.logout = function(){
    Authenticate.get({}, function() {
      delete sessionStorage.authenticated;
      delete sessionStorage.userRole;
      $location.path('/');
      FlashService.add('success', 'Successfully Signed Out')
    })
  }
});


app.controller('loginController',function($scope, $rootScope, $sanitize, $location, Authenticate, FlashService, $log){
  $rootScope.location = $location; // used for ActiveTab
  $scope.login = function(){
    Authenticate.save({
      'role': $sanitize($scope.role),
      'password': $sanitize($scope.password)
      },function(data) {
        sessionStorage.authenticated = true;
        sessionStorage.userRole = data['user']['role'];
        if (sessionStorage.userRole == 'admin') {
          $location.path('/admin/locations');  
        } else if (sessionStorage.userRole == 'printer') {
          $location.path('/locations');  
        }
        FlashService.add('success', 'Succesfully Logged In');
      },function(response){
        FlashService.add('danger', response.data.flash);
      }
    );
  };
});

app.controller('locationController',function($scope, $rootScope, $location, Authenticate, Location, PrintJobCollection, FlashService, $log){
  $rootScope.location = $location; // used for ActiveTab
  Location.query({},function(data) {
    locations = []
    $scope.data = data;
    for (i in data) {
      container = {
        print: false,
        record: data[i]
      }
      locations.push(container)
    }
    $scope.locations = locations;
  });
  $scope.onPrint = function() {
    toPrint = [];
    for (i in $scope.locations) {
      loc = $scope.locations[i];
      if (loc.print) {
        toPrint.push({
          'printer_name': loc.record.printer_name,
          'file_name': loc.record.mar_file_name,
          'location_id': loc.record.id
        });
      }
    }
    if (toPrint.length > 0) {
      PrintJobCollection.create({'items': toPrint}, function(data) {
        Location.query({},function(data) {
          locations = []
          $scope.data = data;
          for (i in data) {
            container = {
              print: false,
              record: data[i]
            }
            locations.push(container)
          }
          $scope.locations = locations;
        });
        FlashService.add('info', 'Successfully sent MAR files to printers');
      }, function() {
        FlashService.add('info', 'Failed to send MAR files to printers');
      });
    }
  };

  $scope.onSelectAll = function() {
    jQuery.each($scope.locations, function(i, location) {
      location.print = true;
    });
  };

  $scope.onSelectNone = function() {
    jQuery.each($scope.locations, function(i, location) {
      location.print = false;
    });
  };
});

app.controller('locationAdminController',function($scope, $rootScope, $location, Authenticate, Location, $log){
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

app.controller('dashboardController', function($scope, $location, $log, $window, $sanitize, Authenticate){
  $scope.onPrintAll = function() {
    var ans = $window.confirm("Are you sure you want to print all the MARs?");
    $log.info(ans);

    if (ans) {
      var isAuthenticated = sessionStorage.authenticated;
      if (!isAuthenticated) {
        var pass = $window.prompt("What is your password?");
        Authenticate.save({
          'role': 'printer',
          'password': $sanitize(pass)
          },function(data) {
            sessionStorage.authenticated = true;
            sessionStorage.userRole = data['user']['role'];
            $log.info("Successfully logged in as printer");
          },function(response){
            FlashService.add('danger', response.data.flash);
          }
        );
      }
    }

  }
});