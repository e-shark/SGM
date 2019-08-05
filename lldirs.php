<?php
/********************************************************
 * 170404, bgt
 *
 *	Project: 
 *		SG Monitor
 *
 * Platform: php5.6/bootstrap3.3.7/jquery1.11.1
 *	Definitions for common dirs & files
 *
 */
$settings = [
	'language'=>'ru',
];

$ListASDUAL =[8,16];
$ListLAL =[0, 8,16];
$ListCOTL =[8,16];
$ListMode =["Unbalanced","Balanced"];
$ListKaType =["MKT2PU","MKT2KP","MKT3PU","MKT3KP","UTK1PU","UTK1KP","UTM7PU","UTM7KP","UTS8PU","UTS8KP","TM8500APU","TM8500AKP","TM800VPUTM8500VKP","GranitPU","GranitKP"];
$ListKaChannel =[1,2,3,4];

$lrturtdir 	  = '/home/vpr/bin';
$uploadFwFile = 'lrtum_fw.fwa';

define( 'FWUPDATE_SCRIPT',"$lrturtdir/fwupdate.sh noreboot" );
define( 'CFGUPDATE_SCRIPT',"$lrturtdir/cfgupdate.sh noreboot" );

if( is_dir( $lrturtdir ) ) {
	$ListPort =['/dev/ttyUSB0','/dev/ttyUSB1','/dev/ttyUSB2','/dev/ttyUSB3','/dev/ttyS1','/dev/tty/ttyS2'];
	$rtuDumpFileName= '/tmp/lrtum.dmp';
	$rtuLogDir = $lrturtdir;
}
else {	/*---For debugging under Windows*/
	$ListPort =['com1','com2','com3','com4','com5','com6',];
	
	$rtuDumpFileName= $_SERVER[DOCUMENT_ROOT]."/kkpio.dmp";
	$rtuLogDir = $_SERVER[DOCUMENT_ROOT].'/Log';
	$jamLConfigFile = $_SERVER[DOCUMENT_ROOT]."/sg.cnf";
}

function logger($message)
{ 
list($usec, $sec) = explode(" ", microtime());
$msec = sprintf("%03d",intval($usec*1000));
$dbgfn = __DIR__."\\Log\\".date('ymd_').basename(__FILE__,".php").".dbg";
  if( FALSE === ( $dbgfp = fopen( $dbgfn, "a" ) ) ) return FALSE;
  fputs($dbgfp, date('H:i:s',$sec).".".$msec.' '.$message."\n");
  fclose($dbgfp);
  return TRUE;
}

function _t($key){
	global $settings;
	$message_table =[
		"Yes"=> ["Да"],
		"No"=> ["Нет"],
		"On"=> ["Вкл"],
		"Off"=> ["Выкл"],
		"Name"=> ["Имя"],
		"Comment"=> ["Комментарий"],
		"Address"=> ["Адрес"],
		"sec"=> ["сек"],
		"msec"=> ["мсек"],
		"Console"=> ["Консольный"],
		"Out"=> ["Выход"],
		"Port"=> ["Порт"],
		"Mode"=> ["Режим"],
		"Timeout"=> ["Таймаут"],
		"Type"=> ["Тип"],
		"Duplex"=> ["Дуплекс"],
		"Half Duplex"=> ["Полудуплекс"],
		"Inversion"=> ["Инверсия"],

		'Home'=> ["Домой"],
		'Configuration'=> ["Конфигурация"],
		"Change password"=> ["Изменить пароль"],
		"Upload Firmware"=> ["Обновить ПО"],
		"Upload Config" => ["Загрузить конфигурацию"],
		"Download Config" => ["Скачать конфигурацию"],
		'View Log'=> ["Просмотр журнала"],
		'Reboot'=> ["Перезагрузка"],
		'Logout'=> ["Выйти из системы"],
		'Administration' => ["Администрирование"],

		"Protocol" => ["Протокол"],
		"Protocols" => ["Протоколов"],
		"Channel" => ["Канал"],
		"Channel Settings"=> ["Установки канала"],
		"Channels"=> ["Каналов"],
		"Channel Index"=> ["Индекс канала"],
		"Channel Status"=> ["Статус канала"],
		"Channel Mode"=> ["Режим канала"],

		"Master IP"=> ["Мастер IP"],
		"Clock Sync"=> ["Синхронизация часов"],
		"Link Aaddress"=> ["Синхронизация часов"],
		"Com Mode"=> ["Режим порта"],
		"Link Test"=> ["Ком. Тест"],
		"Retries"=> ["Повторы"],
			
		"Device" => ["Устройство"],
		"Devices" => ["Устройств"],
		"Device Settings"=> ["Установки устройства"],
		"Device Index"=> ["Индекс устройства"],
		"Device Status"=> ["Статус устройства"],
		"Logical Address"=> ["Логический адрес"],
		"Physical Address"=> ["Физический адрес"],
		"Poll Period"=> ["Период опроса"],
		"Link Timeout"=> ["Таймаут связи"],
		"Main Poll"=> ["Общий опрос"],
		"Meter Poll"=> ["Опрос счетчиков"],
		"Baud Rate"=> ["Символьная скорость"],
		"Ad1"=> ["Ad1"],
		"Ad2"=> ["Ad2"],

		"Block"=> ["Блок"],
		"Block Settings"=> ["Установки блока"],
		"Block Index"=> ["Индекс блока"],
		"Signal Table" => ["Таблица сигналов"],
	];
	
	if($settings['language']=='en') return $key;
	else if ($settings['language']=='ru') return $message_table[$key][0];
}

?>