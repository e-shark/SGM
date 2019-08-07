<?php

/*Secure page from unauthorised access*/ include 'libauth.php'; session_start(); if( !isAuthorized() ) { header('Location: index.php'); exit(); } 

  require($_SERVER['DOCUMENT_ROOT'] . "/lldirs.php"); 

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){header('Location: index.php');exit();}


//---Setting IP address,mask,gateway
if( isset($_REQUEST[ 'rtuIpAddr' ] ) || isset($_REQUEST[ 'rtuGateway' ] ) || isset($_REQUEST[ 'rtuNetMask'  ] ) )
{
	$chipcmd='sudo shellcommands'.DIRECTORY_SEPARATOR.'chip.pl';
	if( isset($_REQUEST[ 'rtuIpAddr'  ] ) ) { $s[0] = $_REQUEST[ 'rtuIpAddr'  ]; $chipcmd  .= " -a$s[0]"; }
	if( isset($_REQUEST[ 'rtuGateway' ] ) ) { $s[1] = $_REQUEST[ 'rtuGateway'  ]; $chipcmd .= " -g$s[1]"; }
	if( isset($_REQUEST[ 'rtuNetMask' ] ) ) { $s[2] = $_REQUEST[ 'rtuNetMask'  ]; $chipcmd .= " -m$s[2]"; }
	exec( $chipcmd );
	unset($s);
}

//---Go to reboot page...
$_SESSION['rebootmessage'] = 'Configuration was changed. To get changes in effect, reboot the device';
header('Location: vreboot.php');

?>
