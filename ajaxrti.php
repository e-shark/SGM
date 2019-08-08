<?php
/********************************************************
 * Platform: php5.6/bootstrap3.3.7/jquery1.11.1
*/
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

	require($_SERVER['DOCUMENT_ROOT'] . "/lldirs.php"); 
	require($_SERVER['DOCUMENT_ROOT'] . "/llRti.php"); 

	if( isset( $_POST['rtiInfo'])  || isset( $_POST['signalInfo']) ) 
	{
	    header("Content-type: text/txt; charset=UTF-8");
	    if( $_POST['rtiInfo'] == '1' ) 
		{
			makeRtiView($rtuDumpFileName, $runTimeInfo );
	    }
	    else if ( !empty($_POST['signalInfo'])) {							// signalInfo имеет вид 'Table_1_1_1_s104'
	    	$key = str_replace('Table_','',$_POST['signalInfo']);				
   			$dmpInfo = file_get_contents( $rtuDumpFileName,FALSE, NULL, 0);	
			$dmpInfo = array_slice(unpack('C*', "\0".$dmpInfo), 1);
			//logger(print_r($dmpInfo,true));   			
			getRtiTableInfo($dmpInfo,$key,$runTimeInfo);
	    }
	    else echo json_encode('Error request for getting run-time info');

		echo json_encode($runTimeInfo);
	}else{
		echo json_encode('Error request.');		
	}
?>