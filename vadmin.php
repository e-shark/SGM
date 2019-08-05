<?php
/********************************************************
 * 170404, vpr
 *
 *	Project: 
 *		RTU for the elevator's alarming and messaging system
 *		RTU web-configuration tool
 *
 * Platform: php5.4/bootstrap3.3.7/jquery1.11.1
 *
 */
?>
<?php /*Secure page from unauthorised access*/ include 'libauth.php'; session_start(); if( !isAuthorized() ) { header('Location: index.php'); exit(); } ?>
<?php unset($chpswmessage);	if( !empty( $_SESSION['chpswmessage'] ) ) { $chpswmessage = $_SESSION['chpswmessage']; unset($_SESSION['chpswmessage']); } ?>

<!--  Header -->
<?php require($DOCUMENT_ROOT . "v_header.php");?> 
<!--  /Header -->
<div class="container">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-6">		<h1> RTU configuration </h1>		</div>
	</div>
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-6">		<h3>Admin password settings:</h3> 		</div>
	</div>
	
	
	<form action="cadmin.php" method="post">
		<br>
		<div class="row">
			<div class="col-md-2">		Old password:		</div>
			<div class="col-md-2">		<input class="form-control" type="password"  	name="oldpsw" > 		</div>
		</div>
		<div class="row">
			<div class="col-md-2">		New password:		</div>
			<div class="col-md-2">		<input class="form-control" type="password" name="newpsw" > 	</div>
		</div>
		<div class="row">
			<div class="col-md-2">		</div>
			<div class="col-md-4"><label><?php echo $chpswmessage ?></label></div>
		</div>
		<br>
		<div class="row">

			<div class="col-md-2"></div>
			<div class="col-md-1"><a     class="btn btn-primary" href="vmain.php">       Cancel     </a></div>
			<div class="col-md-2"><input class="btn btn-primary" type="submit" value="       Save       "></div>
		</div>
		<br>
	</form>
</div>
<?php require($DOCUMENT_ROOT . "v_footer.php");?> 