app.controller('navController', function($scope, $location, Authenticate, FlashService, $log){
  $scope.permit = function(role) {
    return Authenticate.permit(role);
  }
  $scope.authenticated = function() {
    return Authenticate.isAuthenticated();
  }
  $scope.logout = function() {
    var success = function() {
      $location.path('/');
      FlashService.add('success', 'Successfully Signed Out');
    }
    var failure = function () {
      FlashService.add('danger', 'Failed to Sign Out');
    }
    Authenticate.logout(success, failure);
  }

  $scope.linkToAdmin = function() {
    $location.path('/admin');
  }
});


app.controller('loginController',function($scope, $rootScope, $sanitize, $location, Authenticate, FlashService, $log){
  $rootScope.location = $location; // used for ActiveTab
  $scope.login = function() {
    var success = function() {
      if (sessionStorage.userRole == 'admin') {
        $location.path('/admin/locations');
      } else if (sessionStorage.userRole == 'printer') {
        $location.path('/printer/locations');
      }
      FlashService.add('success', 'Succesfully Logged In');
    };

    var failure = function(response) {
     var msg = response.data.flash;
      if (!msg) {
        msg = "Failed to login"
      }
      FlashService.add('danger', msg);
    };

    Authenticate.login({
      'role': $sanitize($scope.role),
      'password': $sanitize($scope.password)},
      success, failure
    );
  };
});

app.controller('locationController',function($scope, $rootScope, $location, $filter, Authenticate, PrinterLocation, PrintJobCollection, FlashService, PrintStatusService, $log){
  if (!Authenticate.isAuthenticated()) {
    $location.path('/login');
    return;
  }

  $rootScope.location = $location; // used for ActiveTab
  PrinterLocation.query({},function(data) {
    locations = []
    $scope.data = data;
    for (i in data) {
      container = {
        print: false,
        record: data[i],
        last_print_status: PrintStatusService.displayStatus(data[i].last_print_job)
      }
      locations.push(container)
    }
    $scope.locations = locations;
  });

  $scope.defaultColumn = 'record.last_print_job.enque_timestamp';
  $scope.reverse = true;

  $scope.sort = function(column) {
    if ($scope.defaultColumn === column) {
      $scope.reverse = !$scope.reverse;
    } else {
      $scope.defaultColumn = column;
      $scope.reverse = true;
    }
  }

  $scope.onPrint = function(whichMar) {
    toPrint = [];
    for (i in $scope.locations) {
      loc = $scope.locations[i];
      if (loc.print) {
        toPrint.push({
          'printer_name': loc.record.printer_name,
          'file_name': loc.record[whichMar + '_file_name'],
          'location_id': loc.record.id,
          'mar': true
        });
      }
    }
    if (toPrint.length > 0) {
      PrintJobCollection.create({'items': toPrint}, function(data) {
        PrinterLocation.query({},function(data) {
          locations = []
          $scope.data = data;
          for (i in data) {
            container = {
              print: false,
              record: data[i],
              last_print_status: PrintStatusService.displayStatus(data[i].last_print_job)
            }
            locations.push(container)
          }
          $scope.locations = locations;
        });
      }, function() {
        FlashService.add('danger', 'Unable to contact server');
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

  $scope.formatMarDateOrErrorMsg = function(date, fileName) {
    var output = 'Unable to find the MAR ' + fileName;
    if (date) {
      output = $filter('date')(date, 'short');
    }
    return output;
  }

  $scope.permit = function(roles) {
    var allow = false;
    
    for (roleIndex in roles) {
      allow = allow || Authenticate.permit(roles[roleIndex]);
    }
    
    return allow;
  }
});

app.controller('locationAdminController',function($scope, $rootScope, $location, Authenticate, AdminLocation, $log){
  if (!Authenticate.isAuthenticated()) {
    $location.path('/login');
    return;
  }

  if (!Authenticate.isAdmin()) {
    Authenticate.redirectToLogin();
    return;
  }

  $rootScope.location = $location; // used for ActiveTab
  $scope.editRecord = {};
  AdminLocation.query({},function(data) {
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
    AdminLocation.create({
      'description': $scope.newLocation.description,
      'phone_number': $scope.newLocation.phoneNumber,
      'printer_name': $scope.newLocation.printerName,
      'todays_mar_file_name': $scope.newLocation.todaysMarFileName,
      'tomorrows_mar_file_name': $scope.newLocation.tomorrowsMarFileName
    },function() {
      AdminLocation.query({},function(data) {
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
        FlashService.add('danger', 'Unable to contact server');
      }
    )
  };
  $scope.editLocation = function(location) {
    location.editMode = true;

    $scope.editRecord.description = location.record.description;
    $scope.editRecord.phoneNumber = location.record.phone_number;
    $scope.editRecord.printerName = location.record.printer_name;
    $scope.editRecord.todaysMarFileName = location.record.todays_mar_file_name;
    $scope.editRecord.tomorrowsMarFileName = location.record.tomorrows_mar_file_name;
  }

  $scope.onCancel = function(location) {
    location.editMode = false;
  }

  $scope.onSave = function(location) {
    location.editMode = false;

    location.record.description = $scope.editRecord.description;
    location.record.phone_number = $scope.editRecord.phoneNumber;
    location.record.printer_name = $scope.editRecord.printerName;
    location.record.todays_mar_file_name = $scope.editRecord.todaysMarFileName;
    location.record.tomorrows_mar_file_name = $scope.editRecord.tomorrowsMarFileName;
    location.record.$update();
  }
});

app.controller('userController',function($scope, $rootScope, $sanitize, $location, $resource, Authenticate, FlashService, User, PasswordService){
  if (!Authenticate.isAuthenticated()) {
    $location.path('/login');
    return;
  }

  if (!Authenticate.isAdmin()) {
    Authenticate.redirectToLogin();
    return;
  }

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

app.controller('dashboardController', function($scope, $location, $log, $window, $sanitize, Authenticate, FlashService, PrinterLocation, PrintJobCollection){
  $scope.onPrintAll = function(whichMar) {
    var ans = $window.confirm("Are you sure you want to print all the MARs?");
    if (ans) {
      var printAll = function(data) {
        $log.info("Successfully logged in as printer");

        PrinterLocation.query({},function(locations) {
          $log.info(locations);

          toPrint = [];
          for (i in locations) {
            $log.info(i);
            loc = locations[i];
            toPrint.push({
              'printer_name': loc.printer_name,
              'file_name': loc[whichMar + '_file_name'],
              'location_id': loc.id
            });
          }

          $log.info(toPrint);
          if (toPrint.length > 0) {
            PrintJobCollection.create({'items': toPrint}, function(data) {
              $location.path('/printer/locations');
            }, function() {
              FlashService.add('danger', 'Unable to contact server');
            });
          }

        }, function() {
          FlashService.add('danger', 'Unable to contact server');
        });
      };

      if (!Authenticate.isAuthenticated()) {
        var pass = $window.prompt("What is your password?");
        var failure =function(response) {
          var msg = response.data.flash;
          if (!msg) {
            msg = "Failed to login"
          }
          FlashService.add('danger', msg);
        }
        if (pass) {
          Authenticate.login(
            {
              'role': 'printer',
              'password': pass
            },
            printAll,
            failure
          );
        }
      } else {
        printAll();
      }
    }
  }

  $scope.onPrintByLocation = function() {
    var toPrintByLocation = function() {
      $location.path('/printer/locations');
    };

    if (!Authenticate.isAuthenticated()) {
      var pass = $window.prompt("What is your password?");
      var failure =function(response) {
        var msg = response.data.flash;
        if (!msg) {
          msg = "Failed to login"
        }
        FlashService.add('danger', msg);
      }

      if (pass) {
        Authenticate.login(
          {
            'role': 'printer',
            'password': pass
          },
          toPrintByLocation,
          failure
        );
      }
    } else {
      toPrintByLocation();
    }
  };

  $scope.onPrintByPrinter = function() {
    var toPrintByPrinter = function() {
      $location.path('/printer/alternate');
    };

    if (!Authenticate.isAuthenticated()) {
      var pass = $window.prompt("What is your password?");
      var failure =function(response) {
        var msg = response.data.flash;
        if (!msg) {
          msg = "Failed to login"
        }
        FlashService.add('danger', msg);
      }

      if (pass) {
        Authenticate.login(
          {
            'role': 'printer',
            'password': pass
          },
          toPrintByPrinter,
          failure
        );
      }
    } else {
      toPrintByPrinter();
    }
  };

  $scope.onCheckStatus = function() {
    $location.path('/locations')
  };
});

app.controller('publicLocationController',function($scope, $rootScope, $location, Authenticate, PublicLocation, PrintJobCollection, FlashService, PrintStatusService, $log){
  $rootScope.location = $location; // used for ActiveTab

  PublicLocation.query({},function(data) {
    locations = []
    for (i in data) {
      container = {
        record: data[i],
        last_print_status: PrintStatusService.displayStatus(data[i].last_print_job)
      }
      locations.push(container)
    }
    $scope.locations = locations;
  });
  $scope.defaultColumn = 'record.last_print_job.enque_timestamp';
  $scope.reverse = true;

  $scope.sort = function(column) {
    if ($scope.defaultColumn === column) {
      $scope.reverse = !$scope.reverse;
    } else {
      $scope.defaultColumn = column;
      $scope.reverse = true;
    }
  }
});


app.controller('alternatePrinterController',function($scope, $rootScope, $location, Authenticate, PrinterLocation, PrintJobCollection, FlashService, PrintStatusService, $log){
  if (!Authenticate.isAuthenticated()) {
    $location.path('/login');
    return;
  }
  $rootScope.location = $location; // used for ActiveTab
  PrinterLocation.query({},function(data) {
    files = []
    $scope.data = data;
    for (i in data) {
      container = {
        file_name: data[i].todays_mar_file_name,
      }
      files.push(container)
      container = {
        file_name: data[i].tomorrows_mar_file_name,
      }
      files.push(container)
    }
    $scope.files = files;
  });
  $scope.filesToPrint = [];
  $scope.printToPrinter = function() {
    toPrint = [];
    for (i in $scope.filesToPrint) {
      fileName = $scope.filesToPrint[i]
      toPrint.push({
        'printer_name': $scope.newPrinter.printerName,
        'file_name': fileName
      });
    }

    if (toPrint.length > 0) {
      PrintJobCollection.create({'items': toPrint}, function(data) {
        msg = data.items[0].enque_failure_message;
        if (msg != null) {
          FlashService.add('danger', msg);
        }
      }, function() {
        FlashService.add('danger', 'Unable to contact server');
      });
    }
  };
});