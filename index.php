<?php
/********************************************************
 
 */
?>
<?php include 'libauth.php'; session_start(); if( authorizeFromCookie() ) {	header('Location: vmain.php'); exit(); } ?>
<?php unset($badusrmessage);	if( !empty( $_SESSION['badusrmessage'] ) ) { $badusrmessage = $_SESSION['badusrmessage']; unset($_SESSION['badusrmessage']); } ?>

<?php require($DOCUMENT_ROOT . "v_header.php");?> 

	<form action="clogin.php" method="post">
		<br>
		<div class="row">
			<div class="col-md-2">		User name:		</div>
			<div class="col-md-2">		<input class="form-control" type="text"  	name="login" > 		</div>
		</div>
		<div class="row">
			<div class="col-md-2">		Password:		</div>
			<div class="col-md-2">		<input class="form-control" type="password" name="password" > 	</div>
		</div>
		<div class="row">
			<div class="col-md-2">		</div>
			<div class="col-md-4"><label><?php echo $badusrmessage ?></label></div>
		</div>
		<div class="row">
			<div class="col-md-2">		Remember me:		</div>
			<div class="col-md-2">		<input   type="checkbox"  name="remember"></div>
		</div>
		<br><div class="row"><div class="col-md-2"></div><div class="col-md-2"><input class="btn btn-primary" type="submit" value="       Enter       "></div></div><br>
	</form>
<?php require($DOCUMENT_ROOT . "v_footer.php");?> 