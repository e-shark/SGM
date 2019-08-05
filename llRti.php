<?php 


define( "GREENLITEICON",  "<img src='Images/green_light.ico' style='width:25px;height:25px;'>" );
define( "REDLITEICON",    "<img src='Images/red_light.ico' 	style='width:25px;height:25px;'>" );
define( "YELLOWLITEICON", "<img src='Images/yellow_light.ico' 	style='width:25px;height:25px;'>" );
define( "GRAYLITEICON", "<img src='Images/gray_light.ico' 	style='width:25px;height:25px;'>" );
	
const CFSIGN = 0x5654;
const TIME_MS=6;
const MAXPORTINITSTRS=3;
const MAXBOARDS = 16;
const MAXINITSTR = 100;
const MAXDIAG = 20;
const MAXSTAT = 20;

		
const TCHHEADER_SIZE=64;
const TPORTDEF_SIZE =448; //name[50]+port(1)+baud_ndx(1)+params(1)+flags(1)+type(1) + tout(4)+tconnect4)+initstr(300)+ protocol(1) +  elapsedtime(4) + stat(80)
const KP_TABLE_REC_SIZE =635; //name(50)+skpnum(2)+kpnum(2)+ type(1) + port(1) + outpmask(2)+nboards(1)+bnum(16)+bsnum(32)+ btype(16)+bfrom(64)+bpactime(64)+btime(16*6)+bflags(16)+tout(2)+treq(2)+texch(2)+callstr(100)+diag[20]+KPf(2)+elapsedtime(4)+stat[20*4]+ipn(2*20)+ipntype(20)
const TM_TABLE_REC_SIZE=30; //vtype(1)+value(4) + flag(1) +type(8)+ipn(2*8)

const HDR_PORTS_POS=54;
const HDR_KPTS_POS=58;
const HDR_TMS_POS=60;

const PORTDEF_FLAG_POS = 53;
const PORTDEF_PROTOCOL_POS = 363;

const KP_NAME_SIZE=50; 
const KP_TYPE_POS= 54; 
const KP_PORT_POS= KP_TYPE_POS + 1; 
const KP_NBOARD_POS= KP_PORT_POS+3; 
const KP_DIAG_POS=469; 
const DUMP_FLAG = 1;
const JOURNAL_FLAG = 1<<6;
const DEBUG_FLAG = 1<<7;
const PORT_ON_FLAG = 1;

function getTextString($arr,$offset, $size)
{
   return implode(array_map("chr",array_slice($arr,$offset,$size)));
}                                

function getShort($arr,$offset)
{
   return ($arr[$offset+1]<<8) + $arr[$offset];
//   return  unpack("S",pack("C*",$arr[$offset+1],$arr[$offset]));      
}                                

function getInt($arr,$offset)
{
   return ($arr[$offset+3]<<24) + ($arr[$offset+2]<<16)+ ($arr[$offset+1]<<8) + $arr[$offset];
}                                

//--------------------------------------------------------------------------------------
// dmpInfo - 
// key - адрес блока вида 1_1_1_s104
// rtiTableInfo
//--------------------------------------------------------------------------------------
function  getRtiTableInfo(&$dmpInfo, $key,&$rtiTableInfo){
	$protocolTypes = ['s101' => 160,'m101'=> 60,'s104'=>162, 'm104'=> 62, 'mModbus' =>45];
	$mkaProtocols = [102,3,103,4,104,5,105,6,106,7,107,8,108,9,109,10,110,11,111,12,112,13,113,14,114,15,115,16];
//	logger("key = $key");
	$offsets = explode('_',$key);
    $protocolName = $offsets[3];
    $port = $offsets[2];                // номер канала
    $devNr = $offsets[1];               
    $devInd = $devNr-1;                 // ID устройства (0..n)
    $blockInd = $offsets[0]-1;			// ID блока (0..n)

    $nport = getShort($dmpInfo, HDR_PORTS_POS);
    $ndev = getShort($dmpInfo,HDR_KPTS_POS);
    $ntm = getInt($dmpInfo,HDR_TMS_POS);
    $offsetTKP = TCHHEADER_SIZE+ $nport*TPORTDEF_SIZE;
    $offsetTM = TCHHEADER_SIZE+ $nport*TPORTDEF_SIZE + $ndev*KP_TABLE_REC_SIZE;

	for($idev=0; $idev < $ndev; $idev++) {
		$cur_type = $dmpInfo[$offsetTKP+$idev*KP_TABLE_REC_SIZE + KP_TYPE_POS];
		if(($protocolName == 'mka' && in_array($cur_type, $mkaProtocols)) || $protocolTypes[$protocolName] == $cur_type) {$devInd += $idev; break;}
	}

	$offsetNBoard = $offsetTKP + $devInd*KP_TABLE_REC_SIZE+KP_NBOARD_POS;
	$nboard = $dmpInfo[$offsetNBoard];
//	logger("nport = $nport ndev = $ndev ntm = $ntm devInd = $devInd blockInd = $blockInd nboard = $nboard");
//	logger($protocolName."=".$protocolTypes[$protocolName]." devInd = $devInd blockInd = $blockInd nboard = $nboard");
	$offsetBNum = $offsetNBoard + 1 + $blockInd;
	$bnum =  $dmpInfo[$offsetBNum];
	$offsetBSNum = $offsetNBoard + 1 + MAXBOARDS+ 2*$blockInd;
	$bsnum =  getShort($dmpInfo,$offsetBSNum);
	$offsetBType = $offsetNBoard + 1 + 3*MAXBOARDS + $blockInd;
	$btype = $dmpInfo[$offsetBType];
 	$offsetBFrom = $offsetNBoard + 1 + 4*MAXBOARDS + 4*$blockInd;
	$bfrom = getInt($dmpInfo,$offsetBFrom);
//	logger("offsetBSNum = $offsetBSNum bsnum=$bsnum bfrom = $bfrom btype = $btype");
	$offsetBPactime = $offsetNBoard + 1 + 8*MAXBOARDS + 4*$blockInd;
	$bpactime = getInt($dmpInfo,$offsetBPactime);
	$vals = array();	
	$shift = 0;
	switch($btype) {
		case 1:case 2:
		$nbyte = $bsnum;
		$nsignal=$bsnum * 8;
		$cur_byte = 0;
		$curTMVal = $dmpInfo[$offsetTM+$bfrom*TM_TABLE_REC_SIZE + 1];
//		logger("bfrom = $bfrom curTMVal = $curTMVal");
		$shift = 0;
		for($itm=0; $itm < $nsignal; $itm++) {
			if($shift >= 8){
				$cur_byte++;
				$curTMVal = $dmpInfo[$offsetTM+$offsetBFrom + 1+ $cur_byte];
				$shift = 0;
			}
			$vals[$cur_byte*8+ 7 - $shift] = ($curTMVal & (1 << $shift)) ? 1 : 0; 
			$shift++;
		}
		break;

		case 0:case 3:
		$nsignal=$bsnum;
		for($itm=0; $itm < $bsnum; $itm++) {
			$vals[] = $dmpInfo[$offsetTM + $bfrom*TM_TABLE_REC_SIZE + 1 + $itm];
		}
	}

	$rtiTableInfo ="<table class='table  table-striped'>
		<thead >
	  		<tr>
			    <th >Address</th>
			    <th >Value</th>
			    <th >Timestamp</th>
		  	</tr>
		</thead>
		<tbody>";

	$prefix = "$protocolName:$port:".($devNr).":".($blockInd+1);
	$delay= mktime() - $bpactime;
	$bkgStyle = 'background-color: '.($delay>10 ? 'LightSalmon;' : 'Chartreuse');
	for($itm= 0; $itm < $nsignal; $itm++) {
		$address= "$prefix:".($itm+1);
		$rtiTableInfo.="<tr><td style= \"$bkgStyle\">$address</td><td>".$vals[$itm]."</td><td >".(empty($bpactime)? '': date('Y-m-d h:i:s',$bpactime))."</td></tr>";
	}
	$rtiTableInfo.="</tbody></table>";
}

function makeRtiView( $dumpfname, &$runTimeInfo ){
	$runTimeInfo = "<div class='col-md-4'>		Error: run-time data is unavailable		</div>";
	$rtiStatus    = "Failed";
	$rtiGsmState  = 'Disabled';
	$rtiGprsState = 'Disabled';
	$rtiApState   = 'Disabled';
	$rtiGprsTcpClientState   = 'Disabled';
	$rtiGprsFtpClientState   = 'Disabled';
	$rtiTime = strftime("%Y-%m-%d %H:%M:%S");
	$rtiIOinfo = "Disabled";
	$rtiMbusInfo="Disabled";
	try {
		$result = file_get_contents( $dumpfname,FALSE, NULL, 0);	
	}
	catch(Exception $e) {
		return;
	}
	
	$sa = array_slice(unpack('C*', "\0".$result), 1);
    $sign = getShort($sa,0);
//    $num= getShort($sa,2);
//    $name= getTextString($sa,2+2,50);
//    $flags= getShort($sa,56);
    $ports= getShort($sa, HDR_PORTS_POS);
    $KPtSize= getShort($sa,HDR_KPTS_POS);
    $TMtSize= getInt($sa,HDR_TMS_POS);
    $offsetTKP = TCHHEADER_SIZE+ $ports*TPORTDEF_SIZE;
    $offsetTPort = TCHHEADER_SIZE;
    $curPortNr = $sa[$offsetTKP+KP_PORT_POS];
	$runTimeInfo = '';
	for($i=0; $i < $KPtSize; $i++) {
		$name= getTextString($sa,$offsetTKP,KP_NAME_SIZE);
		$portOn = $sa[$offsetTPort+PORTDEF_FLAG_POS]&PORT_ON_FLAG;
//		logger("offsetTPort = $offsetTPort flag$i =".$sa[$offsetTPort+PORTDEF_FLAG_POS]." Port=".$sa[$offsetTKP+KP_PORT_POS]);
    	if($portOn)	{
			$diag = $sa[$offsetTKP+KP_DIAG_POS];
			if( $diag == 1) 	$rtiStatus = GREENLITEICON." Running";
			else $rtiStatus = REDLITEICON.' Stopped'; 
    	}
    	else 
			$rtiStatus = GRAYLITEICON.' Port off'; 
		$runTimeInfo .= "<div class='row'>
		<div class='col-md-4'> 
			<h4>Device $name Status</h4>
		</div>
		<div id='rtiStatus' class='col-md-5'><div class='panel panel-info'><div class='panel-body'> 
			$rtiStatus     
		</div></div></div>
	</div>
	";
		$offsetTKP+=KP_TABLE_REC_SIZE;
		if($sa[$offsetTKP+KP_PORT_POS] != $curPortNr){
			$curPortNr = $sa[$offsetTKP+KP_PORT_POS];
			$offsetTPort += TPORTDEF_SIZE;
		} 
    }
    return $sa;
}
?>