<?php

require($_SERVER['DOCUMENT_ROOT'] . "/lldirs.php"); 

$RequestFunction = $_POST['function'];
//logger("function: ".$RequestFunction."\n");
if (!empty($RequestFunction)){
	switch($RequestFunction){
		case 'settime':
			$newdate = $_POST['newdate'];
			$newtime = $_POST['newtime'];
			if (isset($newdate) || isset($newtime)){
				$cmd = 'sudo date -s "'.$newdate.'T'.$newtime.'"';
//logger("cmd:".$cmd."\n");
				$rex = exec($cmd, $output, $retval);
//logger("date res:".$retval."\n");
//logger("date out:".print_r($output,true)."\n");
				if (0 == $retval) $result = true;
			}
			break;
	}
}

//if (!empty($result))echo json_encode($result);
//logger("referer:".$_SERVER['HTTP_REFERER']."\n");


if ($result) header('Location: index.php');	// пока просто перенаправление
else header("Location: ".$_SERVER['HTTP_REFERER']);
?>
