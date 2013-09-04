<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Halon</title>
  <!-- Add Bootstrap 3.0 -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.min.css" rel="stylesheet">
  <!-- Application Css -->
  <link href="css/app.css" rel="stylesheet">
</head>

  <body ng-app="halon">
    @section('sidebar')
      This is the master sidebar.
    @show

    <div class="container">
      <div ng-view></div>
      @yield('content')
    </div>


    <script src="angular/angular.min.js"></script>
    <script src="angular/angular-resource.min.js"></script>
    <script src="angular/angular-sanitize.min.js"></script>
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/app.js"></script>

<!-- Not Yet Implemented but for better seperation later
    <script src="angular/controllers.js"></script>
    <script src="angular/directives.js"></script>
    <script src="angular/filters.js"></script>
    <script src="angular/services.js"></script>
-->
  </body>
</html>