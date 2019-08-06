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
<?php unset($uploadmessage);	if( !empty( $_SESSION['uploadmessage'] ) ) { $uploadmessage = $_SESSION['uploadmessage']; unset($_SESSION['uploadmessage']); } ?>

<!--  Header -->
<?php require($_SERVER['DOCUMENT_ROOT'] . "/v_header.php");?> 
<!--  /Header -->
<div class="container">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-6">		<h3>Configuration upload</h3> 		</div>
	</div>

	<form action="cupldcfg.php" method="post" enctype="multipart/form-data">
		<br>
       	<div class="row">
			<div class="col-md-2">		Config file to upload:		</div>
			<div class="col-md-8">		<input type="file" name="fileToUpload" id="fileToUpload" class="btn btn-default btn-file">	</div>
		</div>
		<div class="row">
			<div class="col-md-2">		</div>
			<div class="col-md-4"><label><?php echo $uploadmessage ?></label></div>
		</div>
		<br>
       	<div class="row">
       		<div class="col-md-2"></div>
			<div class="col-md-1"><a     class="btn btn-primary" href="vmain.php">       Cancel     </a></div>
			<div class="col-md-2">		<input type="submit" value="Upload" name="submit" class="btn btn-primary"></div>
		</div>
		<br>
	</form>
</div>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/v_footer.php");?> 