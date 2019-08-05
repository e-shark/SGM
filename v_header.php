<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Bootstrap -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!--<link rel="shortcut icon" type="image/ico" href="Images/1491646140_elevator.ico" />-->
    <link rel="shortcut icon" type="image/ico" href="Images/favicon.ico" />
  <link href="Assets/css/bootstrap.min.css" rel="stylesheet">
<style>
ul, #myUL {
  list-style-type: none;
}
#myUL {
  margin: 0;
  padding: 0;
}

li {font-size:16px;}

.mycaret {
  cursor: pointer;
  -webkit-user-select: none; /* Safari 3.1+ */
  -moz-user-select: none; /* Firefox 2+ */
  -ms-user-select: none; /* IE 10+ */
  user-select: none;
/*font-weight:bold;*/
/*font-size:160%*/
}

.mycaret::before {
  content: "\25B6";
  color: black;
  display: inline-block;
  margin-right: 6px;
}

.mycaret-down::before {
  -ms-transform: rotate(90deg); /* IE 9 */
  -webkit-transform: rotate(90deg); /* Safari */'
  transform: rotate(90deg);  
}

.nested {
  display: none;
}

.active {
  display: block;
}
</style>
    
    <title> SG Monitor and Configurator</title>
  </head>
  <body>
    <script src="Assets/js/jquery.1.11.1.js"></script>
    <script src="Assets/js/bootstrap.min.js"></script>
<div class="container">
	<br>
	<div class="panel panel-primary">
		<div class="panel-heading"><h1> SG Monitor</h1></div>
		<div class="panel-body"> Checks SG state and represents a hierarchical view of SG configuration editor. © 2019 Intep Ltd.</div>
	</div>



