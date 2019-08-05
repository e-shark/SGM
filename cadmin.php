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
 * Module: Controller for POST request on change user password
 */
?>
<?php
include 'libauth.php';
session_start();
//--------POST-handler for change the password  of the logged in user 
if( isAuthorized() ) 
{
	if( !empty( $_POST['oldpsw'] ) && !empty( $_POST['newpsw'] )  && ( getUserPassword($_SESSION['user'] ) == encodePassword($_POST['oldpsw'] ) ) )
	{   
		changePassword( $_SESSION['user'], $_POST['newpsw'] );
		header('Location: vmain.php');
		exit();
	}
	if( getUserPassword($_SESSION['user'] ) != encodePassword($_POST['oldpsw'] ) ) 
			$_SESSION['chpswmessage'] = 'Wrong old password';
	else 	$_SESSION['chpswmessage'] = 'Enter not empty password';
	header('Location: vadmin.php');
	exit();
}
else
{   
	header('Location: index.php');
	exit();
}
?>