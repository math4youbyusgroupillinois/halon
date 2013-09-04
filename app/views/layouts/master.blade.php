<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Halon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Add Bootstrap 3.0 -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.min.css" rel="stylesheet">
  <!-- Application Css -->
  <link href="css/app.css" rel="stylesheet">
</head>

  <body ng-app="halon">
    @section('navbar')
    <nav class="navbar navbar-inverse" role="navigation">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Halon</a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
          <li ng-class="{active: location.path() == '/'}"><a href="#">Home</a></li>
          <li ng-class="{active: location.path() == '/printers'}"><a href="#/printers">Printers</a></li>
        </ul>
        <form class="navbar-form navbar-right" role="search">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Search">
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>
    </nav>
    @show

    <div class="container">
      <div ng-view></div>
      @yield('content')
    </div>


    <script src="angular/angular.min.js"></script>
    <script src="angular/angular-resource.min.js"></script>
    <script src="angular/angular-sanitize.min.js"></script>
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/app.js"></script>

<!-- Not Yet Implemented but for better seperation later
    <script src="angular/controllers.js"></script>
    <script src="angular/directives.js"></script>
    <script src="angular/filters.js"></script>
    <script src="angular/services.js"></script>
-->
  </body>
</html>