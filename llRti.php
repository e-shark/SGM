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

		
const TCHHEADER_SIZE=64;				// размер хидера файла (дампа)
const TPORTDEF_SIZE =448;				// размер записи таблицы описания портов (линий)		name[50]+port(1)+baud_ndx(1)+params(1)+flags(1)+type(1) + tout(4)+tconnect4)+initstr(300)+ protocol(1) +  elapsedtime(4) + stat(80)   
const KP_TABLE_REC_SIZE =635;			// размер записи таблицы описания девайсов (КП)		name(50)+skpnum(2)+kpnum(2)+ type(1) + port(1) + outpmask(2)+nboards(1)+bnum(16)+bsnum(32)+ btype(16)+bfrom(64)+bpactime(64)+btime(16*6)+bflags(16)+tout(2)+treq(2)+texch(2)+callstr(100)+diag[20]+KPf(2)+elapsedtime(4)+stat[20*4]+ipn(2*20)+ipntype(20)
const TM_TABLE_REC_SIZE=30; 			// размер записи таблицы описания сигналов		vtype(1)+value(4) + flag(1) +type(8)+ipn(2*8)

const HDR_PORTS_POS=54;					// где записано кол-во портов (от начала файла)
const HDR_KPTS_POS=58;					// где записано кол-во КП
const HDR_TMS_POS=60;					// где записано кол-во записей таблицы параметров телемеханики

const PORTDEF_FLAG_POS = 53;			// где flags в таблице описания портов
const PORTDEF_PROTOCOL_POS = 363;		// где индекс protocol в таблице описания портов

const KP_NAME_SIZE=50; 					// размер названия в таблице девайсов
const KP_TYPE_POS= 54; 					// где тип (type) в таблице девайсов
define('KP_PORT_POS', KP_TYPE_POS + 1); // где индекс порта связи с КП (port) в таблице девайсов		const KP_PORT_POS= KP_TYPE_POS + 1; 
define('KP_NBOARD_POS', KP_PORT_POS+3); // где количество установленных в КП плат (nboards) в таблице девайсов		const KP_NBOARD_POS= KP_PORT_POS+3; 
const KP_TOUT_POS=363; 					// где таймайт отсутствия связи в таблице девайсов
const KP_DIAG_POS=469; 					// где параметры диагностики КП в таблице девайсов

const DUMP_FLAG = 1;
define('JOURNAL_FLAG', 1<<6);  			//const JOURNAL_FLAG = 1<<6;
define('DEBUG_FLAG', 1<<7); 			//const DEBUG_FLAG = 1<<7;
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
// Формирует таблицу сигналов для блока
// dmpInfo - массив - дамп kkpio
// key - адрес блока (вида 1_1_1_s104)
// rtiTableInfo - сюда формируется отображение таблицы
//--------------------------------------------------------------------------------------
function  getRtiTableInfo(&$dmpInfo, $key,&$rtiTableInfo){
	$protocolTypes = ['s101' => 160,'m101'=> 60,'s104'=>162, 'm104'=> 62, 'mModbus' =>45];
	$mkaProtocols = [102,3,103,4,104,5,105,6,106,7,107,8,108,9,109,10,110,11,111,12,112,13,113,14,114,15,115,16];
	$signaltypes = [0=>'AI', 1=>'DI', 2=>'DO', 3=>'AC'];
//	logger("key = $key");
	$offsets = explode('_',$key);
    $protocolName = $offsets[3];
    $port = $offsets[2];                // номер канала
    $devNr = $offsets[1];               
    $devInd = $devNr-1;                 // ID устройства (0..n)
    $blockInd = $offsets[0]-1;			// ID блока (0..n)

    $nport = getShort($dmpInfo, HDR_PORTS_POS);									//кол-во портов 
    $ndev = getShort($dmpInfo,HDR_KPTS_POS);									//кол-во КП (девайсов) 
    $ntm = getInt($dmpInfo,HDR_TMS_POS);										//кол-во записей телемеханики 
    $offsetTKP = TCHHEADER_SIZE+ $nport*TPORTDEF_SIZE;							// где начинается таблица девайсов
    $offsetTM = TCHHEADER_SIZE+ $nport*TPORTDEF_SIZE + $ndev*KP_TABLE_REC_SIZE;	// где начинается таблица сигналов

    // Определяем id в таблице девайсов
    // (Сперва находим, с какого индекса ничинаются девайсы данного протокола,
	//  и к нему добавляем номер нашого девайса в протоколе)
	for($idev=0; $idev < $ndev; $idev++) {
		$cur_type = $dmpInfo[$offsetTKP+$idev*KP_TABLE_REC_SIZE + KP_TYPE_POS];	// определяем тип КП
		if(($protocolName == 'mka' && in_array($cur_type, $mkaProtocols)) || $protocolTypes[$protocolName] == $cur_type) {$devInd += $idev; break;}
	}

	$offsetNBoard = $offsetTKP + $devInd*KP_TABLE_REC_SIZE + KP_NBOARD_POS;	// где  лежит кол-во установленных плат (смещение таблицы + id девайса * размер записи девайса + смещение параметра NBOARD) 
	$nboard = $dmpInfo[$offsetNBoard];										// читаем кол-ва плат у девайса

	$offsetTOut = $offsetTKP + $devInd*KP_TABLE_REC_SIZE + KP_TOUT_POS;
	$toutthreshold = getShort($dmpInfo, $offsetTOut);
//logger(" KEY = ".$key."   DevId=".$devInd."   offsetTKP = ".$offsetTKP."    tout = ".$toutthreshold."    addr = ".$offsetTOut );	

//	logger("nport = $nport ndev = $ndev ntm = $ntm devInd = $devInd blockInd = $blockInd nboard = $nboard");
//	logger($protocolName."=".$protocolTypes[$protocolName]." devInd = $devInd blockInd = $blockInd nboard = $nboard");
	$offsetBNum = $offsetNBoard + 1 + $blockInd;							// где лежит "номер" блока
	$bnum =  $dmpInfo[$offsetBNum];											// читаем "номер" блока (но пока он нам не нужен)

	$offsetBSNum = $offsetNBoard + 1 + MAXBOARDS + 2*$blockInd;				// где лежит количество сигналов на данной плате
	$bsnum =  getShort($dmpInfo, $offsetBSNum);								// читаем количество сигналов на данной плате

	$offsetBType = $offsetNBoard + 1 + 3*MAXBOARDS + $blockInd;
	$btype = $dmpInfo[$offsetBType];										// читаем тип платы (0-ТИ 1-ТС 2-ТУ 3-ТИИ)

 	$offsetBFrom = $offsetNBoard + 1 + 4*MAXBOARDS + 4*$blockInd;
	$bfrom = getInt($dmpInfo, $offsetBFrom);									// читаем смещения буферов плат (сигналов) относительно начала таблицы ТМ (т.е. с какого индекса начинаются сигналы для данного блока, в таблице сигналов)

//	logger("offsetBSNum = $offsetBSNum bsnum=$bsnum bfrom = $bfrom btype = $btype");
	$offsetBPactime = $offsetNBoard + 1 + 8*MAXBOARDS + 4*$blockInd;
	$bpactime = getInt($dmpInfo, $offsetBPactime);							// читаем время прихода последнего пакета на плату
	$vals = array();				// массив сигналов
	$shift = 0;

	// debug --------------------------------------
	// $rtiTableInfo = "bxtype : ".$btype.", ";
	// $rtiTableInfo.= "ofset : +".$offsetTM."+".$bfrom."*".TM_TABLE_REC_SIZE."+1=".($offsetTM + $bfrom * TM_TABLE_REC_SIZE + 1);
	// debug --------------------------------------

	$hexs =[];
	$hex= '';
	$vtypes =[];
	$vtype = '';
	switch($btype) {
		case 1: case 2:
			$nbyte = $bsnum;							// кол-во сигналов на плате
			$nsignal=$bsnum * 8;
			$cur_byte = 0;
			$curTMVal = $dmpInfo[$offsetTM + $bfrom * TM_TABLE_REC_SIZE + 1];
	//		logger("bfrom = $bfrom curTMVal = $curTMVal");
			$shift = 0;
			for($itm=0; $itm < $nsignal; $itm++) {
				if($shift >= 8){
					$cur_byte++;
					$curTMVal = $dmpInfo[$offsetTM + $bfrom * TM_TABLE_REC_SIZE + $cur_byte * TM_TABLE_REC_SIZE + 1];
					//$curTMVal = $dmpInfo[$offsetTM + $offsetBFrom + 1+ $cur_byte];
					$shift = 0;
				}
				$vals[$cur_byte*8+ 7 - $shift] = ($curTMVal & (1 << $shift)) ? 1 : 0; 
				$shift++;
			}
			break;

		case 0: case 3:
			$nsignal=$bsnum;							// кол-во сигналов на плате
			for($itm=0; $itm < $bsnum; $itm++) {
				//$vals[] = $dmpInfo[$offsetTM + $bfrom*TM_TABLE_REC_SIZE + 1 + $itm*TM_TABLE_REC_SIZE];
				$ofst = $offsetTM + $bfrom*TM_TABLE_REC_SIZE + $itm*TM_TABLE_REC_SIZE;   // указатель начала записи сигнала TM_Table_Rec
				$binarydata = pack("c*", $dmpInfo[$ofst+1], $dmpInfo[$ofst+2], $dmpInfo[$ofst+3], $dmpInfo[$ofst+4]);
				switch($dmpInfo[$ofst]){
				case 1: 	 										//SHORT 16 бит целое со знаком
					$vtype = 'SHORT';
					$data = unpack("s",$binarydata)[1]; 
					$hex = sprintf("%04x",$data) ; 
					break;
				case 2: 											//WORD 16 бит целое без знака
					$vtype = 'WORD';
					$data = unpack("S",$binarydata)[1]; 
					$hex = sprintf("%04x",$data) ;
					break; 
				case 3: 											//float
					$vtype = 'FLOAT';
					$data = unpack("f",$binarydata)[1]; 
					$hex = sprintf("%02x%02x%02x%02x",$dmpInfo[$ofst+4], $dmpInfo[$ofst+3], $dmpInfo[$ofst+2], $dmpInfo[$ofst+1]) ;
					break; 
				case 4: 
					$vtype = 'INT';
					$data = unpack("I",$binarydata)[1]; 			//INT 32 бит целое со знаком
					$hex = sprintf("%08x",$data) ;
					break; 
				case 5: 											//DWORD 32 бит целое без знака
					$vtype = 'DWORD';
					$data = unpack("L",$binarydata)[1]; 
					$hex = sprintf("%08x",$data) ;
					break; 
				case 6: 											//TIME 32 бит время Ч:М:С:ДС
					$vtype = 'TIME';
					$data = $dmpInfo[$ofst+1].":".$dmpInfo[$ofst+2].":".$dmpInfo[$ofst+3]; 
					$hex = sprintf("%02x%02x%02x",$dmpInfo[$ofst+3], $dmpInfo[$ofst+2], $dmpInfo[$ofst+1]) ;
					break; 

				case 100: 											//CHAR signed
					$vtype = 'CAHR';
					$data = unpack("c",$binarydata)[1]; 
					$hex = sprintf("%02x",$data) ;
					break; 
				case 0: 											//BYTE unsigned
				default:
					$vtype = 'BYTE';
					$data = unpack("C",$binarydata)[1]; 
					$hex = sprintf("%02x",$data) ;
				}
				
				$vals[] = $data;
				$hexs[] = $hex;
				$vtypes[] = $vtype;
			}
			unset($ofst);
			unset($binarydata);
			unset($data);
			unset($hex);
			unset($vtype);
	}

	$table_title = _t("Signal Table");
	$signal_type_title = _t($signaltypes[$btype]);
	$rtiTableInfo ="<h3>$table_title ($signal_type_title):</h3>";
	$rtiTableInfo .="<table class='table  table-striped'>
		<thead >
	  		<tr>
			    <th >"._t("Address")."</th>
			    <th >"._t("Value")."</th>";
	if ((0 == $btype) || (3 == $btype)) $rtiTableInfo .="<th >Hex</th>";
	$rtiTableInfo .="<th >"._t("Timestamp")."</th>
		  	</tr>
		</thead>
		<tbody>";

	//$prefix = "$protocolName:$port:".($devNr).":".($blockInd+1);
	$prefix = "$protocolName:".($devNr).":".($blockInd+1);
	$delay= mktime() - $bpactime;
	$bkgStyle = 'background-color: '.($delay>$toutthreshold ? 'LightSalmon;' : 'Chartreuse');
	for($itm= 0; $itm < $nsignal; $itm++) {
		$address= "$prefix:".($itm+1);
		$rtiTableInfo .= "<tr><td style= \"$bkgStyle\">$address</td><td>".$vals[$itm]."</td>";
		if ((0 == $btype) || (3 == $btype)) $rtiTableInfo .= "<td> (".$vtypes[$itm].") 0x".$hexs[$itm]."</td>";
		$rtiTableInfo .= "<td >".(empty($bpactime)? '': date('Y-m-d H:i:s',$bpactime))."</td></tr>";
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
		if (!$result) return;
	}
	catch(Exception $e) {
		return;
	}
	
	$sa = array_slice(unpack('C*', "\0".$result), 1);
    $sign = getShort($sa,0);
//    $num= getShort($sa,2);
//    $name= getTextString($sa,2+2,50);
//    $flags= getShort($sa,56);
    $ports= getShort($sa, HDR_PORTS_POS);							//кол-во портов 
    $KPtSize= getShort($sa,HDR_KPTS_POS);							//кол-во КП (девайсов) 
    $TMtSize= getInt($sa,HDR_TMS_POS);								//кол-во записей телемеханики 
    $offsetTKP = TCHHEADER_SIZE+ $ports*TPORTDEF_SIZE;				// где начинается таблица девайсов
    $offsetTPort = TCHHEADER_SIZE;									// где начинается таблица портов (линий)
    $curPortNr = $sa[$offsetTKP+KP_PORT_POS];						// индекс порта связи с КП (индекс линии)
	$runTimeInfo = "<div class='row'><div class='col-md-4'><h4>"._t("Device").":</h4></div><div class='col-md-4'><h4>"._t("Device Status").":</h4></div></div>";
	for($i=0; $i < $KPtSize; $i++) {								// перебираем все девайсы, что описаны в v2m (в дампе)
		$name = mb_convert_encoding ( getTextString($sa,$offsetTKP,KP_NAME_SIZE), "UTF-8", "CP1251");
		//$name =  getTextString($sa,$offsetTKP,KP_NAME_SIZE);
		if (0 == $sa[$offsetTKP]) $name='&ltnoname&gt';
		//$portOn = $sa[$offsetTPort+PORTDEF_FLAG_POS]&PORT_ON_FLAG;	// проверяет включен ли порт по флагу PORT_ON (в таблице портов)
//		logger("offsetTPort = $offsetTPort flag$i =".$sa[$offsetTPort+PORTDEF_FLAG_POS]." Port=".$sa[$offsetTKP+KP_PORT_POS]);
		$portOn = $_SESSION['DevsParams'][$i]['Inuse'];
    	if($portOn)	{
			$diag = $sa[$offsetTKP+KP_DIAG_POS];					// читаем байт диагностики из таблицы девайсов
			//logger(">".$i." - [".($offsetTKP+KP_DIAG_POS)."]= ".$sa[$offsetTKP+KP_DIAG_POS]."<");
			if( $diag == 1) 	$rtiStatus = GREENLITEICON." Running";
			else $rtiStatus = REDLITEICON.' Stopped'; 
    	}
    	else 
			$rtiStatus = GRAYLITEICON.' Port off'; 
		$runTimeInfo .= "<div class='row'>
		<div class='col-md-4'> 
			<h4> $name </h4>
		</div>
		<div id='rtiStatus' class='col-md-4'><div class='panel panel-info'><div class='panel-body'> 
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

//--------------------------------------------------------------------------------
//	Распальцовка mmf файла статистики 104 протокола
const CN_NUMCON_POS = 0;			// где лежит кол-во подключений
const CN_SRVPORT_POS = 4;			// где лежит номер порта

const CN_STAT_MAS_SIZE = 10;		// размер таблиц статистики (максимальное кол-во подключений)

define('CN_CSTIME_TABLE_POS', 16);
define('CN_LSOPTIME_TABLE_POS', CN_CSTIME_TABLE_POS + CN_STAT_MAS_SIZE * 4);
define('CN_CONNECTED_TABLE_POS', CN_LSOPTIME_TABLE_POS + CN_STAT_MAS_SIZE * 4);
define('CN_STAT_TABLE_POS', CN_CONNECTED_TABLE_POS + CN_STAT_MAS_SIZE *  4 );
define('CN_ADDR_TABLE_POS', CN_STAT_TABLE_POS + CN_STAT_MAS_SIZE * 50 * 4);
//--------------------------------------------------------------------------------
//	Формирует таблицу сетевых подключений для 104 сервера
//--------------------------------------------------------------------------------
function getConnectionsTableInfo( $dmpInfo, $key, &$rtiTableInfo ){
	$port = $key;
	$numcon = getInt($dmpInfo, CN_NUMCON_POS);
	$rtiTableInfo .= "<br><div>
		<table class='table  table-striped'> 
		<thead > <tr> <th >"._t("Address")."</th> <th>"._t("Еstablish")."</th> <th>"._t("Аctivity")."</th> <th>"._t("frame in")."</th> <th>"._t("frame out")."</th></tr> </thead>
		<tbody> ";

	for($i=0; $i<CN_STAT_MAS_SIZE; $i++){
		$CSTIME = getInt($dmpInfo, CN_CSTIME_TABLE_POS + (4 * $i));                 // Время создания соединения
		$LSOPTIME = getInt($dmpInfo, CN_LSOPTIME_TABLE_POS + (4 * $i));             // Время последней активности
		$CONNECTED = getInt($dmpInfo, CN_CONNECTED_TABLE_POS + (4 * $i));           // Состосяние соединения (1-connected, 0-disconnected)
		$FrameIn = getInt($dmpInfo, CN_STAT_TABLE_POS + (4 * (50 * $i + 2)));       // Кол-во переданых фреймов
		$FrameOut = getInt($dmpInfo, CN_STAT_TABLE_POS + (4 * (50 * $i + 3)));      // Кол-во принятых фреймов
		$ADDR = getTextString($dmpInfo, CN_ADDR_TABLE_POS + (1 * 64 * $i), 64);     // IP адрес клиента
		if (1 == $CONNECTED){
			$rtiTableInfo .= "<tr>
			  <td>".$ADDR."</td>
			  <td>".(empty($CSTIME)? '': date('Y-m-d H:i:s',$CSTIME))."</td>
			  <td>".(empty($LSOPTIME)? '': date('Y-m-d H:i:s',$LSOPTIME))."</td>
			  <td>".$FrameIn."</td>
			  <td>".$FrameOut."</td>
			</tr>";
		}
	}

	$rtiTableInfo .= "</tbody> </table> </div>
	";
}

?>