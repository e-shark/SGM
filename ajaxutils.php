<?php
session_start();

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 0);
//ini_set('display_startup_errors', 0);


//-------------------------------------------------------------
// 
//-------------------------------------------------------------
function func_GetTime()
{
	//echo (string) round(microtime(true) * 1000);
	return (string) round($_SERVER['REQUEST_TIME_FLOAT'] * 1000);;
}

//-------------------------------------------------------------
// MAIN
//-------------------------------------------------------------
$RequestFunction = $_POST['reqfunc'];
if (!empty($RequestFunction)){
	switch($RequestFunction){

		case 'GtTime':
			$result = func_GetTime();
			break;

		case 'GetTermo':
			$result =[];
			exec("cat /sys/devices/virtual/thermal/thermal_zone0/temp", $out, $retv);
			$result['d0'] = $out[0];
			exec("cat /sys/devices/virtual/thermal/thermal_zone1/temp", $out, $retv);
			$result['d1'] = $out[0];
			break;

		case 'Reboot':
			system('sudo reboot', $retvalue);
			if( 0 != $retvalue ) $result = ['result'=>false,'message'=>'Failed to reboot device'];
			else $result = ['result'=>true, 'message'=>'Device is rebooting...'];
			break;

		case 'ping':
			$result = true;
			break;
			
		default:
			//$result = 'Error request!';
	}
}

if (!empty($result))echo json_encode($result);

?>