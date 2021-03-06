app.controller('navController', function($scope, $location, Authenticate, FlashService, $log){
  $scope.permit = function() {
    var allow = false;

    for (argIdx in arguments) {
      allow = allow || Authenticate.permit(arguments[argIdx]);
    }

    return allow;
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
  $scope.login = function() {
    $location.path('/login');
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

app.controller('locationController',function($scope, $rootScope, $location, $filter, Authenticate, Location, PrintJob, FlashService, PrinterVerificationPage, $log){
  if (!Authenticate.isAuthenticated()) {
    $location.path('/login');
    return;
  }

  $rootScope.location = $location; // used for ActiveTab

  $scope.testMode = false;

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

  $scope.defaultColumn = 'record.last_mar_print_job.enque_timestamp';
  $scope.reverse = true;

  $scope.isMarMode = function() {
    return !$scope.testMode;
  }
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
          'mar': $scope.isMarMode()
        });
      }
    }
    if (toPrint.length > 0) {
      PrintJob.create({'items': toPrint}, function(data) {
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
      }, function() {
        FlashService.add('danger', 'Unable to contact server');
      });
    }
  };

  $scope.onPrintTestPage = function() {
    $log.info("onPrintTestPage");
    var toCreate = [];
    var printersByLocation = {};
    for (i in $scope.locations) {
      loc = $scope.locations[i];
      if (loc.print) {
        toCreate.push({
          'location_id': loc.record.id,
        });

        printersByLocation[loc.record.id.toString()] = loc.record.printer_name;
      }
    }

    var success = function(pages) {
      pages = pages['pages'];

      var success = function() {
        Location.query({},function(data) {
          locations = []
          $scope.data = data;
          for (i in data) {
            container = {
              print: false,
              record: data[i],
            }
            locations.push(container)
          }
          $scope.locations = locations;
        });
      };

      var failure = function () {
        FlashService.add('danger', 'Unable to contact server');
      };

      $log.info(pages);

      for (o in pages) {
        var page = pages[o];
        page['printer_name'] = printersByLocation[page['location_id'].toString()];
      }

      PrintJob.create({'items': pages}, success, failure);
    };

    var failure = function() {
      FlashService.add('danger', 'Unable to contact server');
    }

    if (toCreate.length > 0) {
      PrinterVerificationPage.create({'pages': toCreate}, success, failure);
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

  $scope.permit = function(roles) {
    var allow = false;

    for (roleIndex in roles) {
      allow = allow || Authenticate.permit(roles[roleIndex]);
    }

    return allow;
  }
});

app.controller('locationAdminController',function($scope, $rootScope, $location, Authenticate, Location, $modal, FlashService, $log, $http){
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
  $scope.newLocation = {};
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
      'todays_mar_file_name': $scope.newLocation.todaysMarFileName,
      'tomorrows_mar_file_name': $scope.newLocation.tomorrowsMarFileName
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

  $http({method: 'GET', url: 'index.php/locations/import_status'}).success(function(data) {
    if (data == "true") {
      FlashService.add('warning', 'Locations file has been changed. Please import.');
    }
  });

  $scope.importLocations = function(){
    var modalInstance = $modal.open({
      templateUrl: 'app/partials/admin/import.html',
      controller: 'importLocationController'
    });

    modalInstance.result.then( function () {
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
    });
  };

  $scope.setAddMode = function() {
    $scope.addMode = true;
  }
});

app.controller('importLocationController',function ($scope, $modalInstance, $upload, FlashService, $http){
  $scope.import = function(){
    $http({method: 'POST', url: 'index.php/locations/import'}).success(function(data) {
      FlashService.add('success', data + " locations are imported");
    }).error(function(data, status, headers, config) {
        FlashService.add('info', data["message"]);
    });
    $modalInstance.close();
  };

  $scope.cancel = function(){
    $modalInstance.dismiss('cancel');
  };
});

app.controller('userController',function($scope, $rootScope, $sanitize, $location, $resource, Authenticate, FlashService, User, PasswordService, $modal, $log){
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

  $scope.changePassword = function() {
    var selectedUser = $scope.users[this.$index];
    $modal.open({
      templateUrl: 'app/partials/admin/changeUserPassword.html',
      controller: 'changeUserPasswordController',
      resolve: {
        user: function() { return   selectedUser;}
      }
    });
  }
});

app.controller('changeUserPasswordController', function($scope, $modalInstance, $sanitize, $log, user, PasswordService) {
  $scope.user = user;
  $scope.password = {};
  $scope.passwordConfirm = {};

  $scope.change = function(){
    if ($scope.password.text !== $scope.passwordConfirm.text) {
      $scope.error = "The two passwords do not match";
    } else {
      $scope.error = "";
      $scope.user.password = $sanitize($scope.password.text);
      $scope.user.$update({},
        function() {
          $modalInstance.dismiss('password changed');
        },
        function() {
          $scope.error = "Failed to change password";
        }
      );
    };
  };

  $scope.suggest = function() {
    $scope.suggested = PasswordService.generate(12);
  }

  $scope.cancel = function(){
    $modalInstance.dismiss('cancel');
  };
});

app.controller('dashboardController', function($scope, $location, $log, $window, $sanitize, Authenticate, FlashService, Location, PrintJob){
  $scope.onPrintAll = function(whichMar) {
    var ans = $window.confirm("Are you sure you want to print all the MARs?");
    if (ans) {
      var printAll = function(data) {
        $log.info("Successfully logged in as printer");

        Location.query({},function(locations) {
          $log.info(locations);

          toPrint = [];
          for (i in locations) {
            $log.info(i);
            loc = locations[i];
            toPrint.push({
              'printer_name': loc.printer_name,
              'file_name': loc[whichMar + '_file_name'],
              'location_id': loc.id,
              'mar': true
            });
          }

          $log.info(toPrint);
          if (toPrint.length > 0) {
            PrintJob.create({'items': toPrint}, function(data) {
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

app.controller('publicLocationController',function($scope, $rootScope, $location, Authenticate, Location, FlashService, $log){
  $rootScope.location = $location; // used for ActiveTab

  Location.query({},function(data) {
    locations = []
    for (i in data) {
      container = {
        record: data[i]
      }
      locations.push(container)
    }
    $scope.locations = locations;
  });
  $scope.defaultColumn = 'record.last_mar_print_job.enque_timestamp';
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


app.controller('alternatePrinterController',function($scope, $rootScope, $location, Authenticate, Location, PrintJob, FlashService, $log){
  if (!Authenticate.isAuthenticated()) {
    $location.path('/login');
    return;
  }
  $rootScope.location = $location; // used for ActiveTab
  Location.query({},function(data) {
    files = []
    $scope.data = data;
    for (i in data) {
      if (data[i].todays_mar_file_name) {
        container = {
          file_name: data[i].todays_mar_file_name,
        }
        files.push(container)
      }

      if (data[i].tomorrows_mar_file_name) {
        container = {
          file_name: data[i].tomorrows_mar_file_name,
        }
        files.push(container)
      }
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
        'file_name': fileName,
        'mar': true
      });
    }

    if (toPrint.length > 0) {
      PrintJob.create({'items': toPrint}, function(data) {
        $scope.hasStatus = true;
        printJobs = [];
        for (i in data.items) {
          container = {
            file_name: data.items[i].file_name,
            status_code: data.items[i].is_enque_successful ? "active" : "danger",
            status_message : data.items[i].is_enque_successful ? "Successful" : data.items[i].enque_failure_message
          }
          printJobs.push(container);
        }
        $scope.printJobs = printJobs;
      }, function() {
        FlashService.add('danger', 'Unable to contact server');
      });
    }
  };
});