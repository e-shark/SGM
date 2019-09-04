<?php
session_start();

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 0);
//ini_set('display_startup_errors', 0);


//-------------------------------------------------------------
//
//-------------------------------------------------------------
/**
 * @return array ['cpu','up','users','mem']
 */
function getSystemUpTime(){
  $uptarray = array( 'cpu'=>'?', 'up'=>'?', 'users'=>'?','mem'=>'?' );

  exec("uptime", $out, $retv);
  if(0==$retv)if(is_array($out)){
    //$t1= "09:50:30 up 1711 days, 17:21,  1 user,  load average: 3.13, 3.14, 3.14";
    preg_match("/.*up\s+([^,]*,?[^,]+),+\s*(\d+)\s+user.*average\s*:\s*([^,]+)/",$out[0]/*$t1*/,$matches);
    if( 4 == count($matches)){
       $uptarray['up']=$matches[1];
       $uptarray['users']=$matches[2];
       $uptarray['cpu']=$matches[3];
    }
  }
  $out=[];$retv=-1;
  exec("free | grep Mem | awk '{printf(\"%2.1f\",$3/$2 * 100)}'",$out,$retv);
  if(0==$retv)if(is_array($out)){
       $uptarray['mem']=$out[0];
  }

  return $uptarray;
}
//---------------------------------------------------------------
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
            $result += getSystemUpTime();
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
