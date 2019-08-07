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

<?php 
unset($chpswmessage);	
if( !empty( $_SESSION['chpswmessage'] ) ) { $chpswmessage = $_SESSION['chpswmessage']; unset($_SESSION['chpswmessage']); } 

unset($uploadmessage);	
if( !empty( $_SESSION['uploadmessage'] ) ) { $uploadmessage = $_SESSION['uploadmessage']; unset($_SESSION['uploadmessage']); } 

$rtuIpAddr    	= '"'.exec('shellcommands'.DIRECTORY_SEPARATOR.'readip.pl -a').'"';
$rtuGateway    	= '"'.exec('shellcommands'.DIRECTORY_SEPARATOR.'readip.pl -g').'"';
$rtuNetMask    	= '"'.exec('shellcommands'.DIRECTORY_SEPARATOR.'readip.pl -m').'"';
?>

<!--  Header -->
<?php require($_SERVER['DOCUMENT_ROOT']. "/v_header.php");?> 
<!--  /Header -->
<div class="container">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-6">		<h1> Device configuration </h1>		</div>
	</div>

	<hr align="center" color="Red" />
	<?php // СМЕНА ПАРОЛЯ ?>

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

	<hr align="center" color="Red" />
	<?php // ОБНОВЛЕНИЕ ПРОШИВКИ ?>

	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-6">		<h3>Upload Firmware:</h3> 		</div>
	</div>

	<form action="cupload.php" method="post" enctype="multipart/form-data">
		<br>
       	<div class="row">
			<div class="col-md-2">		Firmware file to upload:		</div>
			<div class="col-md-8">		<input type="file" name="fileToUpload" id="fileToUpload" class="btn btn-default btn-file">	</div>
		</div>
		<div class="row">
			<div class="col-md-2">		</div>
			<div class="col-md-4"><label><?php echo $uploadmessage ?></label></div>
		</div>
		<br>
       	<div class="row">
       		<div class="col-md-2"> </div>
			<div class="col-md-1"><a     class="btn btn-primary" href="vmain.php">       Cancel     </a></div>
			<div class="col-md-2">		<input type="submit" value="Upload" name="submit" class="btn btn-primary"></div>
		</div>
		<br>
	</form>

	<hr align="center" color="Red" />
	<?php // СМЕНА IP АДРЕСА ?>

	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-6">		<h3>Change IP addres:</h3> 		</div>
	</div>

	<form action="cchngip.php" method="post" enctype="multipart/form-data">
		<br>

			<div class="row">
				<div class="col-md-2">		RTU IP address: 			</div>
				<div class="col-md-4">		<input class="form-control" type="text" value = <?php echo $rtuIpAddr; ?> name="rtuIpAddr" > 		</div>
			</div>

			<div class="row">
				<div class="col-md-2">		Default gateway: 			</div>
				<div class="col-md-4">		<input class="form-control" type="text" value = <?php echo $rtuGateway; ?> name="rtuGateway" > 		</div>
			</div>

			<div class="row">
				<div class="col-md-2">		Network mask: 			</div>
				<div class="col-md-4">		<input class="form-control" type="text" value = <?php echo $rtuNetMask; ?> name="rtuNetMask" > 		</div>
			</div>

			<br>
			<div class="row">
				<div class="col-md-2">	</div>
				<div class="col-md-1">		<a class="btn btn-primary" href="vmain.php">       Cancel     </a></div>
				<div class="col-md-2">		<input type="submit" value="Change" name="submit" class="btn btn-primary"></div>
			</div>

		<br>
	</form>

	<br>



</div>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/v_footer.php");?> 