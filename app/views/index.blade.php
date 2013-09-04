@extends("layouts.master")

@section('content')
  <h2>This is an Angular Box in index.blade.php</h2>
  <input type="text" placeholder="I use Angular!" ng-model="bindExampleOne">
  {{bindExampleOne}}
@stop