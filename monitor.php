<?php /*Secure page from unauthorised access*/ include 'libauth.php'; session_start(); if( !isAuthorized() ) { header('Location: index.php'); exit(); } ?>	

<?php require($_SERVER['DOCUMENT_ROOT'] . "/lldirs.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT']. "/v_header.php");?> 

<div class="container">
	<div class="row">
		<div class="col-md-2"> </div>
		<div class="col-md-6"> <h1> <?php echo _t("Device Monitor");?> </h1> </div>
	</div>

	<hr align="center" color="Red" />
	<?php // ТЕМПЕРАТУРА ПРОЦЕССОРА ?>

	<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-3"> <h3> <?php echo _t("Temperature");?> 1:</h3> </div>
		<div class="col-md-1"> <h3><span id="SrvTermo1">?</span> <sup>o</sup>C</h3> </div>
	</div>

	<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-3"> <h3> <?php echo _t("Temperature");?> 2:</h3> </div>
		<div class="col-md-1"> <h3><span id="SrvTermo2">?</span> <sup>o</sup>C</h3> </div>
	</div>


</div>
<?php 

?>

<script>

	function getTermo()
	{
		$.ajax({
			url:'ajaxutils.php',
			type:'POST',
			data:{'reqfunc':'GetTermo'},
			dataType:'json',
			success:function (data) {
				$("#SrvTermo1").html(data['d0']);
				$("#SrvTermo2").html(data['d1']);
			},
			error:function(data) {
				$("#SrvTermo").html("?");
		    }
		});
		return false;

	}

	window.onload = function() {setInterval( getTermo, 2000 );}

</script>

