<?php /*Secure page from unauthorised access*/ 
	include 'libauth.php'; session_start(); if( !isAuthorized() ) { header('Location: index.php'); exit(); } 
?>

<?php require($_SERVER['DOCUMENT_ROOT'] . "/v_header.php");?> 

<?php

//<h1 class="text-center"> SG Monitor </h1>

print <<<HERE
	<div class="panel panel-warning">
		<div class="panel-heading text-center">
			<h3 id="RbtMessage">Reboot device ?</h3>
			<br><br><a id="RbtBtn" class="btn btn-primary" href="javascript: doReboot();"> Reboot the device </a>
			<br><br><a class="btn btn-default" href="index.php"> Home </a>			
		</div>
	</div>
HERE;
?>
<SCRIPT>
	function ping(){
		var res=false;
		$.ajax({
			url:'ajaxutils.php',
			type:'POST',
			data:{'reqfunc':'ping'},
			dataType:'json',
			success:function (data) {
				if( true == data)
					document.location.href = "index.php";
			}
		});
		return res;
	}

/*
	function CheckConnect(){
		var pr = ping();
		console.log(pr);
		if (pr) {
			document.location.href = "index.php";
		}else{
		 setTimeout( CheckConnect, 3000 );
		}
	}
*/

	function doReboot(){
		$.ajax({
			url:'ajaxutils.php',
			type:'POST',
			data:{'reqfunc':'Reboot'},
			dataType:'json',
			success:function (data) {
				$("#RbtMessage").html(data['message']);
				if (true == data['result']){
					//setTimeout( ping, 5000 ); 
					setInterval( ping, 3000 ); 
					$( '#RbtBtn' ).hide();
				}
				//alert( data );
			},
			error:function() {
				alert( "Не удалось перезагрузить устройство!" );
			}
		});
	}
</SCRIPT>

<?php require($_SERVER['DOCUMENT_ROOT'] . "/v_footer.php");?> 