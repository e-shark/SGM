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
		<div class="panel-heading">
      <div class="row">
        <div class="col-md-3"> 
          <h1 class="text-white bg-dark">  SG Monitor  </h1> 
        </div>
        <div class="col-md-8"> </div>
        <div class="col-md-1"><span id="SrvTime"> </span></div>
      </div>
    </div>
		<div class="panel-body"> 
      <div class="row">
        <div class="col-md-1">
          <a  class="badge badge-primary"  href="vmain.php"> <?php echo _t("Home"); ?> </a>
        </div>
        <div class="col-md-8">
          Substation Gateway SG-1 © 2019 Intep Ltd.
        </div>
        <div class="col-md-3">
          ( <?php exec("cat /home/vpr/bin/sg.ver", $out, $retv );  echo $out[0]; unset($retv); unset($out);?> )
        </div>
      </div>
    </div>
	</div>

<script>
  var TimeDiff = 0;
  var HaveDiff = false;

  function getTime()
  {
    $.ajax({
        url:'ajaxutils.php',
        type:'POST',
        data:{'reqfunc':'GtTime'},
        dataType:'json',
        success:function (data) {
          TimeDiff = +Date.now() - data; 
          HaveDiff = true;
        },
        error:function(data) {
            $("#SrvTime").html("?:?:?");
        }
    });
    return false;
  }

  function checkTime(i) {
      if (i < 10) {i = "0" + i};
      return i;
  }

  function printTime()
  {
  if (!HaveDiff) getTime();
  var x = "test";
  var realtime = +Date.now() - TimeDiff; 
  var date = new Date(realtime);
  var hours = checkTime(date.getHours());
  var minutes = checkTime(date.getMinutes());
  var seconds = checkTime(date.getSeconds());
  var year = date.getFullYear();
  var month = checkTime(date.getMonth());
  var day = date.getDate();
  var formattedTime = "&nbsp;" + hours + ':' + minutes + ':' + seconds;      
  var formattedDate = day + '.' + month + '.' + year;     
  $("#SrvTime").html("<div class='row'>" + formattedTime + "</div><div class='row'>"+ formattedDate + "</div>");
  }


  setInterval( printTime, 1000 ); 

</script>

