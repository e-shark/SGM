<?php /*Secure page from unauthorised access*/ include 'libauth.php'; session_start(); if( !isAuthorized() ) { header('Location: index.php'); exit(); } ?>
<?php unset($rebootmessage);	
if( !empty( $_SESSION['rebootmessage'] ) ) { $rebootmessage = $_SESSION['rebootmessage']; unset($_SESSION['rebootmessage']); } 
	if( (!isset($rebootmessage)) && (!isset($_GET['reboot'] ))){ header('Location: vmain.php'); exit(); } ?>
<?php require($DOCUMENT_ROOT . "v_header.php");?> 
<?php
unset( $rebootBtn );
if( isset($_GET['reboot'] ) ) {
	system('sudo reboot', $retvalue );
	if( 0 != $retvalue )	$rebootmessage = 'Failed to reboot device';
	else 			$rebootmessage = 'Device is rebooting...';
}
else 
	$rebootBtn = '<a     class="btn btn-primary" href="vreboot.php?reboot">     Reboot the device    </a><br><br><a     class="btn btn-default" href="index.php">     Reboot later     </a>';

print <<<HERE
	<h1 class="text-center"> SG Monitor </h1>
	<div class="panel panel-warning">
		<div class="panel-heading text-center">
			<h3>$rebootmessage</h3>
			<br><br>
			$rebootBtn
		</div>
	</div>
HERE;
?>
<?php require($DOCUMENT_ROOT . "v_footer.php");?> 