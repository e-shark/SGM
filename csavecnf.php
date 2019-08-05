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
 *		Controller for saving the RTU configuration
 *
 */
?>
<?php /*Secure page from unauthorised access*/ include 'libauth.php'; session_start(); if( !isAuthorized() ) { header('Location: index.php'); exit(); } ?>
<?php 
if ($_SERVER['REQUEST_METHOD'] !== 'POST'){header('Location: vmain.php');exit();}

require($DOCUMENT_ROOT . "lldirs.php"); 
/*
 *
 */

//---Updating ini-file...
//print_r($_REQUEST);
//---Run

$jamLConfig= yaml_parse_file($jamLConfigFile);


function updateConfigBlocks( &$Blocks,$key){
	$nblock = count($Blocks);
	for($i = 1; $i <= $nblock; $i++) {
		$par=$i."_".$key;
		$name= "block";
		$fname= $name."_".$par;
		if( isset($_REQUEST[$fname] ) ) $Blocks[$i-1][$name] = $_REQUEST[$fname];
		$name= "ASDU";
		$fname= $name."_".$par;
		if( isset( $_REQUEST[$fname] ) ) $Blocks[$i-1][$name] = $_REQUEST[$fname];
	}
}

function updateConfigDevs(&$Devs,$key){
	$ndev = count($Devs);
	for($i = 1; $i <= $ndev; $i++) {
		$par=$i."_".$key;
		$name= "LDev";
		$fname= $name."_".$par;
		if( isset($_REQUEST[$fname] ) ) $Devs[$i-1][$name] = $_REQUEST[$fname];
		$name= "Inuse";
		$fname= $name."_".$par;
		if( isset( $_REQUEST[$fname] ) ) $Devs[$i-1][$name] = $_REQUEST[$fname];
		updateConfigDevs($Devs[$i - 1]['blocks'],  $par);
	}
}

function updateConfigLines( &$lines,$key){
	$nline = count($lines);
	for($i = 1; $i <= $nline; $i++) {
		$par=$i."_".$key;
		$name= "Line";
		$fname= $name."_".$par;
		if( isset($_REQUEST[$fname] ) ) $lines[$i-1][$name] = $_REQUEST[$fname];
		$name= "Inuse";
		$fname= $name."_".$par;
		if( isset( $_REQUEST[$fname] ) ) $lines[$i-1][$name] = $_REQUEST[$fname];
		updateConfigDevs($lines[$i - 1]['LDevs'],  $par);
	}
}

function updateConfig(&$jamLConfig)
{
	$cnt = count($jamLConfig);
	$keys=array_keys($jamLConfig);
	for($i = 1; $i <= $cnt; $i++) {
		updateConfigLines($jamLConfig[$key]['Lines'], $keys[$i]);
	}
}

updateConfig($jamLConfig);
yaml_emit_file($jamLConfigFile, $jamLConfig);
logger("jamLConfig\n".print_r($jamLConfig,true));
unset( $jamLConfig );

//---Go to reboot page...
$_SESSION['rebootmessage'] = 'Configuration was changed succeessfully. To get changes in effect, reboot the device';
header('Location: vreboot.php');
?>