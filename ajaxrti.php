<?php
/********************************************************
 * Platform: php5.6/bootstrap3.3.7/jquery1.11.1
*/
	require($DOCUMENT_ROOT . "lldirs.php"); 
	require($DOCUMENT_ROOT . "llRti.php"); 

	if( isset( $_POST['rtiInfo'])  || isset( $_POST['signalInfo']) ) 
	{
	    header("Content-type: text/txt; charset=UTF-8");
	    if( $_POST['rtiInfo'] == '1' ) 
		{
			makeRtiView($rtuDumpFileName, $runTimeInfo );
	    }
	    else if ( !empty($_POST['signalInfo'])) {
	    	$key = str_replace('Table_','',$_POST['signalInfo']);
   			$dmpInfo = file_get_contents( $rtuDumpFileName,FALSE, NULL, 0);	
			$dmpInfo = array_slice(unpack('C*', "\0".$dmpInfo), 1);
			getRtiTableInfo($dmpInfo,$key,$runTimeInfo);
	    }
	    else echo json_encode('Error request for getting run-time info');

		echo json_encode($runTimeInfo);
	}
?>