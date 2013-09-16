
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
