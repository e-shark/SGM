<?php
/********************************************************
 * 170507, vpr
 *
 *	Project: 
 *		RTU for the elevator's alarming and messaging system
 *		RTU web-configuration tool
 *
 * Platform: php5.4/bootstrap3.3.7/jquery1.11.1
 *
 * Module: log-file viewer / controller
 */
?>
<?php /*Secure page from unauthorised access*/ include 'libauth.php'; session_start(); if( !isAuthorized() ) { header('Location: index.php'); exit(); } ?>
<?php
require($_SERVER['DOCUMENT_ROOT'] . "/lldirs.php"); 

//print_r($_REQUEST);
//---Get the list of available log files
$loglist    = array();
$loglistdirty = scandir( $rtuLogDir, SCANDIR_SORT_DESCENDING );
foreach ( $loglistdirty  as $filename )
	if ( preg_match( "/.dbg/", $filename ) ) $loglist[] = $filename;
unset( $loglistdirty );
//print_r ( $loglist );

//---Handling the GET request if it is there
if( isset( $_GET[ 'deleteall' ] ) ) {
	foreach ( $loglist  as $logname ) { unlink( $rtuLogDir.DIRECTORY_SEPARATOR.$logname ); }
	unset( $loglist );
}
//---Handling the POST request if it is there, then filling the options  for html-select from the log files list
if( !empty( $loglist ) ) {
	if( isset( $_POST[ 'logfilename' ] ) )	$logfilename = $_POST[ 'logfilename' ];
	else 									$logfilename = $loglist[ 0 ];
	unset($logfilesoptions);
	//---Send the log-file if the submit button pressed was the 'Get Log'
	if( isset( $_POST[ 'getButton' ] ) )	{
		header('Content-Type: text/html; charset=utf-8');
		header("Content-Disposition: attachment; filename=$logfilename");
		print "-------------------SG Log:\n";
		echo "Log uploading date: ".strftime("%Y-%m-%d %H:%M:%S")."\n";
		echo "Log file name     : ".$logfilename."\n";
		echo 'Free space        : '.number_format(disk_free_space($rtuLogDir.DIRECTORY_SEPARATOR), 0 ,'.' ,' ' ).' bytes';
		print "\n\n-------------------SG Configuration:\n";
		readfile($rtuIniFileName); 
		print "\n\n-------------------SG RTI:\n";
		echo exec('cat /proc/cpuinfo | grep -a Serial'); 
		print "\n";
		readfile($rtuDumpFileName); 
		print "\n\n-------------------SG Log:\n";
		readfile($rtuLogDir.DIRECTORY_SEPARATOR.$logfilename);
		exit();
	}
	//---Filling the options  for html-select from the log files list
	foreach ( $loglist  as $logname )
		$logfilesoptions .= 		"\n"."<option value = '".$logname."' ".(($logname == $logfilename)?"selected>":">").$logname."</option>";

}
else unset( $logfilename );
//---Generating the html-page...
?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/v_header.php");?> 
<div class="container">
	<div class="row">
		<div class="col-md-2"><br><a     class="btn btn-primary" href="vmain.php">       Home     </a></div>
		<div class="col-md-2">		<h1> SG log </h1>		</div>
		<?php if( isset( $logfilename) ) print "
		<div class='col-md-2'><br><a     class='btn btn-primary' href='vviewlog.php?deleteall'>       Delete all Logs     </a></div>
		<form class='form-horizontal' id='form1' method='post' action='vviewlog.php' >
		<div class='col-md-3'>		
			<br><select class='form-control' name='logfilename'>   $logfilesoptions  </select>
		</div>
		<div class='col-md-1'>
			<br><input class='btn btn-primary' type='submit' value='       Read Log       '>
		</div>
		<div class='col-md-1'>
			<br><input name='getButton' class='btn btn-primary' type='submit' value='       Get Log       '>
		</div>
		</form>
		";?>
	</div>
	<!--- <div class="row" style="height:500px; overflow:auto"> -->
	<div class="row">
		<span class="label label-default"><?php echo 'Free space:  '.number_format(disk_free_space($rtuLogDir.DIRECTORY_SEPARATOR), 0 ,'.' ,' ' ).' bytes';?></span>
		<pre><?php if( FALSE == @readfile( $rtuLogDir.DIRECTORY_SEPARATOR.$logfilename ) ) print "No Log files are available.\nTo enable logging, set the Logging level different than 'None' in SG configuration panel and reboot the device"?>
		</pre>
	</div>
</div>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/v_footer.php");?> 