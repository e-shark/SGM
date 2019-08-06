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
 * Module:
 *		Controller for request for upload the file
 */
?>
<?php /*Secure page from unauthorised access*/ include 'libauth.php'; session_start(); if( !isAuthorized() ) { header('Location: index.php'); exit(); } ?>
<?php
require($_SERVER['DOCUMENT_ROOT'] . "/lldirs.php"); 

$uploaddirw = 'uploads/';	

if( is_dir( $lrturtdir ) ) $uploadfile = $lrturtdir.DIRECTORY_SEPARATOR.$uploadFwFile;
else{ 					   $uploadfile = $uploaddirw.date("Ymd-His_").$uploadFwFile; if( !is_dir( $uploaddirw ) ) mkdir( $uploaddirw ); } // For debugging under Windows
 						

unset($_SESSION['uploadmessage']);
if ( 0 == $_FILES['fileToUpload']['size'] ) { 
	$_SESSION['uploadmessage'] = 'Error: Choose not empty file';	
	goto magain; 
}
if ( 0 != $_FILES['userfile']['error'] ) { 
	$_SESSION['uploadmessage'] = 'Error: '.$_FILES['userfile']['error'].' while uploading file';
	goto magain; 
}
if ( !is_uploaded_file( $_FILES['fileToUpload']['tmp_name'] ) ) { 
	$_SESSION['uploadmessage'] = 'Error: the intrusion attempt was detected';
	goto magain; 
}

if ( move_uploaded_file( $_FILES['fileToUpload']['tmp_name'], $uploadfile ) ) { 
	if( FALSE == exec( FWUPDATE_SCRIPT, $output, $retv ) ) { 
		$_SESSION['uploadmessage'] = 'Error: failed while updating the firmware';	
		goto magain; 
	}
	if( $retv != 0 ) { 
		$_SESSION['uploadmessage'] = 'Error: '.$retv.' while updating the firmware';	
		goto magain; 
	}
	$_SESSION['rebootmessage'] = 'Firmware was successfully updated, reboot the device';
    header('Location: vreboot.php');
} 
else {
	$_SESSION['uploadmessage'] = 'Error while moving file';
magain:
	header('Location: vadmin.php');
    exit();
}
/*echo 'Info:';print_r($_FILES);*/
?>