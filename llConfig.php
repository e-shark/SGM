<?php 

//	require($_SERVER['DOCUMENT_ROOT'] . "/llRti.php");	 

//$saveButtonTxt = '<br><div class="row"><div class="col-md-4"></div><div class="col-md-2"><input class="btn btn-primary" type="submit" value="       Save       "></div></div><br>';
$saveButtonTxt = '';
$InuseOn = _t("On"); $InuseOff = _t("Off"); 			
/*			

                            [Inuse] => 1
                            [Name] => line_101-1
                            [Comment] => Any text
                            [comdev] => com1
                            [comdevmode] => 8N1
                            [ResponseTimeout] => 2
*/
//	<input type='hidden' name='par' value='$key_$i'>							


    function makeConfigBlocks( $Blocks,$key, $dmpInfo, &$TreeView,&$PanelView){
	global $saveButtonTxt;
	global $ifDisabled;
	$tmTypes= [0=>'ТИ',1=>'ТС',2=>'ТУ',3=>'ТИИ', ];
	$nblock = count($Blocks);
	$offsets = explode('_',$key);
	$protocolName = $offsets[2];

	for($i = 1; $i <= $nblock; $i++) {
	    $par=$i."_".$key;
	    $idName= "BlockPanel_$par";
	    $BlockTreeLabel = _t('Block');
	    $TreeView .= "<li formname='$idName'><span class='form_avail '><a href='#' > $BlockTreeLabel ".$i."</a></span></li>";
	    $BlockInd = $Blocks[$i-1]['Block']; 
	    $nsignals = getRtiTableInfo($dmpInfo,$par,$rtiTableInfo);
	    $form_title = _t("Block Settings");
	    $block_index = _t("System number");
	    $PanelView .= "<div id='$idName' style='display:none'>
		    <h3>$form_title:</h3>
		    <div class='row'>
			<div class='col-md-4'> $block_index: </div>
			<div class='col-md-2'> <div class='panel-info form-control'> $BlockInd  </div> </div> 					
		    </div>";
	    if (('s104' == $protocolName) || ('m104' == $protocolName) ||
		('s101' == $protocolName) || ('m101' == $protocolName) ) {
			$ASDU = $Blocks[$i-1]['ASDU'];
			$PanelView .= "<div class='row'>
			    <div class='col-md-4'> ASDU: </div>
			    <div class='col-md-2'> <div class=' panel-info form-control'> $ASDU </div> </div>
			</div>";
	    }
	    if ('mModbus' == $protocolName) {
			$DataType = $Blocks[$i-1]['datatype'];
			$PanelView .= "<div class='row'>
			    <div class='col-md-4'> "._t("DataType").": </div>
			    <div class='col-md-2'> <div class=' panel-info form-control'> $DataType </div> </div>
			</div>";
	    }
	    $PanelView .= $saveButtonTxt;
	    $PanelView .= "<br><br>
		    <div id= 'Table_$par' class = 'signal_table'>
			$rtiTableInfo
		    </div>
		</div>";
	}
    }

    function makeConfigDevs( $Devs,$key, &$dmpInfo, &$TreeView,&$PanelView){
	global $saveButtonTxt, $InuseOn, $InuseOff,$ListKaType, $ListKaChannel;
	$ndev = count($Devs);
	$DevLabel = _t("Device Settings");	$DevTreeLabel = _t("Device");
	$DevIndLabel = _t("System number"); $InuseLabel = _t("Device Status"); 
	$NameLabel = _t("Name"); $CommentLabel = _t("Comment");
	$LAddressLabel = _t("Logical Address"); $LinkaddressLabel = _t("Link Address");
	$pollperiodLabel = _t("Poll Period"); $LinkTimeoutLabel = _t("Link Timeout"); $SecText = _t("sec");
	$Command100Label = _t("Main Poll"); $Command101Label = _t("Meter Poll");
	$PotocolText = _t("Protocol");  $TypeText = _t("Type");	$ChannelText = _t("Channel");
	$DuplexText = _t("Duplex"); $HalfDuplexText = _t("Half Duplex"); 
	$KaChannelmodeLabel = _t("Channel Mode"); $YesText = _t("Yes"); $NoText = _t("No");
	$KaInversionLabel = _t("Inversion"); $Ad1Text = _t("Ad1"); $Ad2Text = _t("Ad2");
	$KaChannelSpeedLabel = _t("Baud Rate"); $msek = _t("msec");	$sek = _t("sec");	

	for($i = 1; $i <= $ndev; $i++) {
	    $par=$i."_".$key;
	    $idName= "DevPanel_$par";
	    $TreeView .= "<li formname='$idName'><span class='form_avail mycaret'><a href='#' >$DevTreeLabel ".$i."</a></span><ul class='nested'>
	    ";
	    makeConfigBlocks($Devs[$i-1]['Blocks'], $par, $dmpInfo, $TreeView,$PanelView);
	    $TreeView .= '
		</ul></li>';
	    $DevInd = $Devs[$i-1]['LDev']; $Inuse = $Devs[$i-1]['Inuse'];
	    $InuseOnOff = $Inuse == 1 ? $InuseOn :  $InuseOff; $checked = $Inuse == 1 ? "checked" : "";
	    $Name = $Devs[$i-1]['Name']; $Comment = $Devs[$i-1]['Comment'];
	    $PanelView .= "<div id='$idName' style='display:none'>
		<h3>$DevLabel:</h3>
		<div class='row'>
		    <div class='col-md-2'> <label>	$NameLabel: 	</label> </div>
		    <div class='col-md-10'> <div class=' panel-info form-control'> $Name </div> </div>		
		</div>
		<div class='row'>
		    <div class='col-md-2'> <label> $CommentLabel: 	</label> </div>
		    <div class='col-md-10'> <div class=' panel-info form-control'> $Comment </div> </div>
		</div>
		<div class='row'>
		    <div class='col-md-2'> <label> $DevIndLabel: 	</label> </div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $DevInd </div> </div>
		</div>
		<div class='row'>
		    <div class='col-md-2'> <label> $InuseLabel </label> </div>
		    <div class='col-md-2'> <label> <input type='checkbox' disabled value ='1' name='Inuse_$par' $checked onchange='toggleCheckBoxLabelText(this)'>&emsp;$InuseOnOff</label> </div>
		</div>
		";

	    if(null != ($LAddress =$Devs[$i-1]['LAddress'])){//s104,m104
		$PanelView .= "<div class='row'>
		    <div class='col-md-2'> <label>	$LAddressLabel: </label> </div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $LAddress </div> </div>
		</div>";
	    }
	    if(null != ($Linkaddress =$Devs[$i-1]['LinkAddress'])){//s101,m101,s104,m104
		$PanelView .= "<div class='row'>
		    <div class='col-md-2'> <label> $LinkaddressLabel: </label> </div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $Linkaddress </div> </div>
		</div>
		";
	    }
	    if(null != ($pollperiod =$Devs[$i-1]['Pollperiod'])){
		$PanelView .= "<div class='row'>
		    <div class='col-md-2'>	<label>	$pollperiodLabel: 	</label>		</div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $pollperiod </div> </div>
		</div>";
	    }
	    if(null != ($LinkTimeout =$Devs[$i-1]['LinkTimeout']))
		$PanelView .= "<div class='row'>
		    <div class='col-md-2'>	<label>	$LinkTimeoutLabel($SecText): 	</label>		</div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $LinkTimeout </div> </div>
		</div>";
	    if(null != ($Clocksyncperiod =$Devs[$i-1]['Clocksyncperiod']))
		$PanelView .= "<div class='row'>
		    <div class='col-md-2'>	<label>	$ClocksyncperiodLabel($SecText): 	</label>		</div>
		    <div class='col-md-2'>	<div class=' panel-info form-control'> $Clocksyncperiod </div> </div>
		</div>";

	    if(null != ($Command100 =$Devs[$i-1]['Command100'])){
		$Command100OnOff = $Command100 == 1 ? $InuseOn :  $InuseOff; $checked = $Command100 == 1 ? "checked" : "";
		$PanelView .= "<div class='row'>
		    <div class='col-md-2'>	<label>	$Command100Label: 	</label>		</div>
		    <div class='col-md-2'>	<label> <input type='checkbox' disabled value ='1' name='Command100_$par' $checked onchange='toggleCheckBoxLabelText(this)'>&emsp;$Command100OnOff</label></div>
		</div>";
	    }
	    if(null != ($Command101 =$Devs[$i-1]['Command101'])){
		$Command1001nOff = $Command101 == 1 ? $InuseOn :  $InuseOff; $checked = $Command101 == 1 ? "checked" : "";
		$PanelView .= "<div class='row'>
		    <div class='col-md-2'>	<label>	$Command101Label: 	</label>		</div>
		    <div class='col-md-2'>	<label><input type='checkbox' disabled value ='1' name='Command101_$par' $checked onchange='toggleCheckBoxLabelText(this)'>&emsp;$Command101OnOff</label></div>
		</div>
		";
	    }

	    if(null != ($KaType =$Devs[$i-1]['KaType'])){//mka
		$PanelView .= "<br><div class='row'>
		    <div class='col-md-2'>	<label>	$PotocolText: </label>		</div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $KaType </div> </div>"; 

		$KaChannel =$Devs[$i-1]['KaChannel'];	
		$PanelView .= "
		    <div class='col-md-2'>	<label>	$ChannelText: </label>		</div>
		    <div class='col-md-2'>	<div class=' panel-info form-control'> $KaChannel </div> </div>
		</div>";

		$KaChannelmode = $Devs[$i-1]['KaChannelmode'];
		$KaChannelmodeOnOff = $KaChannelmode == 1 ? $DuplexText : $HalfDuplexText; $checked = $KaChannelmode == 1 ? "checked" : "";
		$fEXtraPars = "\"$DuplexText\",\"$HalfDuplexText\"";
		$PanelView .= "<div class='row'>
			<div class='col-md-2'>	<label>	$KaChannelmodeLabel:	</label>		</div>
			<div class='col-md-2'>	<label><input type='checkbox' disabled value ='1' name='KaChannelmode_$par' $checked onchange='toggleCheckBoxLabelText(this,$fEXtraPars)'>&emsp;$KaChannelmodeOnOff</label>	</div>
		";
		$KaChannelInversion = $Devs[$i-1]['KaChannelinversion'];
		$KaChannelInversionOnOff = $KaChannelInversion == 1 ? $YesText : $NoText; $checked = $KaChannelInversion == 1 ? "checked" : "";
		$fEXtraPars = "\"$YesText\",\"$NoText\"";
		$PanelView .= "
		    <div class='col-md-2'>	<label>	$KaInversionLabel:	</label>		</div>
		    <div class='col-md-2'>	<label><input type='checkbox' disabled value ='1' name='KaChannelInversion_$par' $checked onchange='toggleCheckBoxLabelText(this,$fEXtraPars)'>&emsp;$KaChannelInversionOnOff</label>	</div>
		    </div>
		";

		$t1 = $Devs[$i-1]['t1'];
		$PanelView .= "<div class='row'>
		    <div class='col-md-2'>	<label>	t1 ($msek): </label> </div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $t1 </div> </div> 	
		";
		$t2 = $Devs[$i-1]['t2'];
		$PanelView .= "
		    <div class='col-md-2'> <label> t2 ($sek): </label> </div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $t2 </div> </div> 	
		</div>";

		$KaChannelSpeed = $Devs[$i-1]['KaChannelSpeed'];
		$PanelView .= "<div class='row'>
		    <div class='col-md-2'>	<label>	$KaChannelSpeedLabel: 	</label>		</div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $KaChannelSpeed </div> </div> 
		    ";
		$Ad1 = $Devs[$i-1]['Ad1'];
		$PanelView .= "
		    <div class='col-md-2'>	<label>	$Ad1Text: 	</label>		</div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $Ad1 </div> </div> 	
		    ";
		$Ad2 = $Devs[$i-1]['Ad2'];
		$PanelView .= "
		    <div class='col-md-2'>	<label>	$Ad2Text: 	</label>		</div>
		    <div class='col-md-2'> <div class=' panel-info form-control'> $Ad2 </div> </div> 	
		</div>";
	    }
	    $PanelView .= "$saveButtonTxt 
	    </div>
	    ";
	}
    }

    function makeConfigLines( $lines,$key, &$dmpInfo, &$TreeView,&$PanelView){
	global $saveButtonTxt, $InuseOn, $InuseOff, $ListASDUAL, $ListLAL, $ListCOTL,$ListPort,$ListMode;
	$nline = count($lines);
	$lineTreeLabel = _t("Channel");
	$lineLabel = _t("Channel Settings");
	$lineIndLabel = _t("System number"); $InuseLabel = _t("Channel Status"); 
	$PortLabel = _t("Port"); $NameLabel = _t("Name"); $CommentLabel = _t("Comment");
	$MasterIpLabel = _t("Master IP");
	$ConnectionTableLabel =  _t("Connection table");
	$Address = _t("Address");	$SecText = _t("sec");
	$ClockSyncLabel = _t("Clock Sync");
	$ConcoleText = _t("Console"); $OutText = _t("Out");
	$ModeText = _t("Mode"); $ComDevModeText = _t("Com Mode");
	$ResponseTimeoutText = _t("Timeout"); $LinkTestText=_t("Link Test"); $RetriesText = _t("Retries");

	for($i = 1; $i <= $nline; $i++) {
	    $par=$i."_".$key;
	    $idName= "ChannelPanel_$par";
	    $TreeView .= "<li formname='$idName'><span class='form_avail mycaret'><a href='#' > $lineTreeLabel ".$i."</a></span><ul class='nested'>
	    ";
	    makeConfigDevs($lines[$i-1]['LDevs'], $par, $dmpInfo, $TreeView,$PanelView);

	    $lineInd = $lines[$i-1]['Line']; $Inuse = $lines[$i-1]['Inuse'];
	    $InuseOnOff = $Inuse == 1 ? $InuseOn :  $InuseOff; $checked = $Inuse == 1 ? "checked" : "";
	    $Port = $lines[$i-1]['Port']; $Name = $lines[$i-1]['Name']; $Comment = $lines[$i-1]['Comment'];
	    $TreeView .= '
		</ul></li>';
	    $PanelView .= "<div id='$idName' style='display:none'>
			    <h3>$lineLabel:</h3>
		    <div class='row'>
			<div class='col-md-2'> <label>	$NameLabel: 	</label>		</div>
			<div class='col-md-10'> <div class=' panel-info form-control'> $Name </div> </div>				
		    </div>
		    <div class='row'>
			<div class='col-md-2'> <label>	$CommentLabel: 	</label>		</div>
			<div class='col-md-10'> <div class=' panel-info form-control'> $Comment </div> </div> 
		    </div>
		    <div class='row'>
			<div class='col-md-2'> <label>	$lineIndLabel: 	</label>		</div>
			<div class='col-md-2'> <div class=' panel-info form-control'> $lineInd </div></div>
		    </div>
		    <div class='row'>
			<div class='col-md-2'> <label>	$InuseLabel:	</label>		</div>
			<div class='col-md-2'> <label><input type='checkbox' disabled value ='1' name='Inuse_$par' $checked onchange='toggleCheckBoxLabelText(this)'>&emsp;$InuseOnOff</label>	</div>
		    </div>
		    ";

	    if(isset($lines[$i-1]['Port'])){
		$PanelView .= "
		    <div class='row'>
			<div class='col-md-2'> <label>	$PortLabel: </label>	</div>
			<div class='col-md-2'> <div class=' panel-info form-control'>".$lines[$i-1]['Port']." </div> </div>
		     </div>
		";
	    }
	    if(isset($lines[$i-1]['IpAddress'])){//m104,mka
		$PanelView .= "
		    <div class='row'>
			<div class='col-md-2'> <label>	IP $Address:  </label>	</div>
			<div class='col-md-2'> <div class=' panel-info form-control'>".$lines[$i-1]['IpAddress']."</div> </div>
		     </div>
		";
	    }
	    if(isset($lines[$i-1]['consoleport'])){//mka
		$PanelView .= "
		    <div class='row'>
			<div class='col-md-2'> <label> $ConcoleText $PortLabel: </label> </div>
			<div class='col-md-2'>  <div class=' panel-info form-control'> ".$lines[$i-1]['consoleport']." </div> </div>
		     </div>
		";
	    }
	    if(isset($lines[$i-1]['consoletout'])){//mka
		$PanelView .= "
		    <div class='row'>
			<div class='col-md-2'>	<label>	$ConcoleText $OutText:  </label>	</div>
			<div class='col-md-2'>	 <div class=' panel-info form-control'> ".$lines[$i-1]['consoletout']." </div> </div>
		     </div>
		";
	    }

	    if(isset($lines[$i-1]['ClockSync'])) {
		$ClockSync = $lines[$i-1]['ClockSync'];
		$ClockSyncOnOff = $ClockSync == 1 ? $InuseOn :  $InuseOff; $checked = $ClockSync == 1 ? "checked" : "";
		$PanelView .= "<div class='row'>
			<div class='col-md-2'>	<label>	$ClockSyncLabel:	</label>		</div>
			<div class='col-md-2'>	<label><input type='checkbox' disabled value ='1' name='ClockSync_$par' $checked onchange='toggleCheckBoxLabelText(this)'>&emsp;$ClockSyncOnOff</label>	</div>
		    </div>
		";
	    }

	    if(null != ($comdev =$lines[$i-1]['comdev'])){//s101
		$PanelView .= "<br><div class='row'>
		    <div class='col-md-1'>	<label>	$PortLabel: </label>		</div>
		    <div class='col-md-2'>	 <div class=' panel-info form-control'> $comdev </div> </div>";
		if(null != ($Mode =$lines[$i-1]['Mode'])){//s101
		    $PanelView .= "
			<div class='col-md-1'> <label>	$ModeText: </label>		</div>
			<div class='col-md-2'> <div class=' panel-info form-control'> $Mode </div> </div>";
		}

		if(null != ($comdevmode =$lines[$i-1]['comdevmode'])){//s101 modbus
		    $PanelView .= "
			<div class='col-md-1'>	<label>	$ComDevModeText: </label>		</div>
			<div class='col-md-5'>	<div class=' panel-info form-control'> $comdevmode </div></div>";
		}
		$PanelView .= "</div>
		    ";
		
		$PanelView .= "<div class='row'>
		    <div class='col-md-1'>	<label>	$ResponseTimeoutText ($SecText): </label>		</div>
		    <div class='col-md-2'>	<div class=' panel-info form-control'> ".$lines[$i-1]['ResponseTimeout']." </div></div>";
		if(null != ($LinkTestTimeout =$lines[$i-1]['LinkTestTimeout'])) //s101	
		    $PanelView .= "
		    <div class='col-md-2'>	<label>	$LinkTestText $ResponseTimeoutText ($SecText): </label>		</div>
		    <div class='col-md-1'>	<div class=' panel-info form-control'> ".$LinkTestTimeout." </div></div>";
		if(null != ($Retries =$lines[$i-1]['Retries'])) //s101	
		    $PanelView .= "
			<div class='col-md-1'>	<label>	$RetriesText: </label>		</div>
		    <div class='col-md-2'>	<div class=' panel-info form-control'> ".$Retries." </div></div>";
		$PanelView .= "</div>
		    ";
	    }

	    if(isset($lines[$i-1]['t1'])){
			$PanelView .= "	<br><div class='row'> 
			<div class='col-md-1'> <label> t1,($SecText.): </label> </div>
			<div class='col-md-2'> <div class=' panel-info form-control'> ".$lines[$i-1]['t1']." </div> </div>

			<div class='col-md-1'> <label> t2,($SecText.): </label> </div>
			<div class='col-md-2'> <div class=' panel-info form-control'> ".$lines[$i-1]['t2']." </div> </div>

			<div class='col-md-1'> <label> t3,($SecText.): </label> </div>
			<div class='col-md-2'> <div class=' panel-info form-control'> ".$lines[$i-1]['t3']." </div> </div>
    
		    </div>
		    <div class='row'>
			<div class='col-md-1'> <label>	k :	</label> </div>
			<div class='col-md-2'> <div class=' panel-info form-control'> ".$lines[$i-1]['k']." </div> </div>
		
			<div class='col-md-1'> <label>	w : </label> </div>
			<div class='col-md-2'> <div class=' panel-info form-control'> ".$lines[$i-1]['w']." </div> </div>
		
		    </div>
		    ";
	    }

	    if(null != ($IOAL =$lines[$i-1]['IOAL'])) {		 					
			$PanelView .= "
			    <div class='row'>
				<div class='col-md-1'>	<label>	IOAL: </label>		</div>
				<div class='col-md-2'> <div class=' panel-info form-control'>". $IOAL." </div></div>
			    ";

			if(null != ($ASDUAL =$lines[$i-1]['ASDUAL'])) {		 					
			    $PanelView .= "
				<div class='col-md-1'> <label> ASDUAL: </label> </div>
				<div class='col-md-2'> <div class=' panel-info form-control'> ".$ASDUAL." </div> </div>";
			}	
			if(!isset($lines[$i-1]['LAL'])) $PanelView .= " </div>  ";
	    }
	    
	    if(isset($lines[$i-1]['LAL'])) {//s101
	    	$LAL =$lines[$i-1]['LAL'];
			$PanelView .= "
			    <div class='col-md-1'>	<label>	LAL: </label>		</div>
			    <div class='col-md-2'> <div class=' panel-info form-control'> ".$LAL." </div> </div>";
			$COTL =$lines[$i-1]['COTL'];	
			$PanelView .= "
			    <div class='col-md-1'>	<label>	COTL: </label>		</div>
			    <div class='col-md-2'> <div class=' panel-info form-control'> ".$COTL." </div> </div>";
			$PanelView .= " </div> 
			    ";
	    }

	    if ("s104" == $key){
		$MasterConnections = $lines[$i-1]['MasterConnections']; 
		if(isset($MasterConnections)){								//s104
		    $PanelView .= "<br><div>
			<table class='table  table-striped'>
			<thead > <tr> <th >$MasterIpLabel</th> <th >$Address</th> </tr> </thead>
			<tbody>";
		    foreach($MasterConnections as $conn)	
			$PanelView .= "<tr><td>".$conn['Ip']."</td><td>".$conn['LAddress']."</td></tr>";
		    $PanelView .= "
			</tbody> </table> </div>
			";
		}

		// Таблица текущих подключений (для 104)
		$Port = $lines[$i-1]['Port']; 
		if(isset($Port)){								//s104
		    $PanelView .= "<br><div>
			<h3>$ConnectionTableLabel :</h3>
			<div port= '$Port' class = 'Connection_table'>
			</div></div>";
		}
	    } //if ("s104" == $key)

	    $PanelView .= " $saveButtonTxt 
		</div> 
		";
	}
    }

    //---------------------------------------------------------------------------------------------------
    //
    //---------------------------------------------------------------------------------------------------
    function makeConfigTreeItems( $jamLConfig, &$dmpInfo, &$TreeView,&$PanelView){
	$keys=array_keys($jamLConfig);
	$cnt = count($keys);
	$Сonfig = _t('Configuration');
	$ChannelsText =  _t("Channels");$DevicesText =  _t("Devices"); 
	$PotocolText =  _t("Protocol"); $PotocolsText =  _t("Protocols");

	$TreeView = "<li role='presentation' formname='ConfPanel'> <span class='form_avail mycaret'><a href='#' >$Сonfig</a></span>
    	<ul class='nested'>
        ";

	$nline = $ndev = 0;
	foreach($jamLConfig as $prot){
	    $nline += count($prot['Lines']);
	    foreach($prot['Lines'] as $line) $ndev += count($line['LDevs']);
	} 
	
	$cnt0=(FALSE===array_search('cnfData',$keys))?$cnt:$cnt-1; //20190828,vpr,exlude cnfData from protocols number

	$PanelView = "<div id='ConfPanel'  style='display:none'>
	    <h4> $Сonfig $DevicesText </h4>
	    <div class='row'>
		<div class='col-md-2'> <label> $PotocolsText: </label> </div> 
		<div class='col-md-2'><div class=' panel-info form-control'> $cnt0 </div></div>
	    </div>
	    <div class='row'>
		<div class='col-md-2'> <label> $ChannelsText: </label> </div> 
		<div class='col-md-2'><div class=' panel-info form-control'> $nline </div></div>
	    </div>
	    <div class='row'>
		<div class='col-md-2'> <label> $DevicesText: </label> </div> 
		<div class='col-md-2'><div class=' panel-info form-control'> $ndev </div></div>
	    </div>
	    
	    <br><br>

	    <div class='row'>
		<div class='col-md-2'> <label>"._t("Name").": </label> </div>
		<div class='col-md-8'><div class=' panel-info form-control'>".$jamLConfig['cnfData']['Name']."</div></div>
	    </div>
	    <div class='row'>
		<div class='col-md-2'> <label>"._t("Comment").": </label> </div>
		<div class='col-md-8'><div class=' panel-info form-control'>".$jamLConfig['cnfData']['Comment']."</div></div>
	    </div>
	    <div class='row'>
		<div class='col-md-2'> <label># </label> </div>
		<div class='col-md-8'><div class=' panel-info form-control'>".$jamLConfig['cnfData']['NumbeCromment']."</div></div>
	    </div>
	    <div class='row'>
		<div class='col-md-2'> <label>"._t("Creation date").": </label> </div>
		<div class='col-md-8'><div class=' panel-info form-control'>".$jamLConfig['cnfData']['CreationDate']."</div></div>
	    </div>
	    <div class='row'>
		<div class='col-md-2'> <label>"._t("Editing date").": </label> </div>
		<div class='col-md-8'><div class=' panel-info form-control'>".$jamLConfig['cnfData']['EditingDate']."</div></div>
	    </div>
	    <div class='row'>
		<div class='col-md-2'> <label>"._t("Author").": </label> </div>
		<div class='col-md-8'><div class=' panel-info form-control'>".$jamLConfig['cnfData']['Author']."</div></div>
	    </div>
	    <div class='row'>
		<div class='col-md-2'> <label>"._t("Company").": </label> </div>
		<div class='col-md-8'><div class=' panel-info form-control'>".$jamLConfig['cnfData']['Company']."</div></div>
	    </div>
	    <div class='row'>
		<div class='col-md-2'> <label>"._t("Department").": </label> </div>
		<div class='col-md-8'><div class=' panel-info form-control'>".$jamLConfig['cnfData']['Department']."</div></div>
	    </div>
	    </div>
	    ";

	for($i = 1; $i <= $cnt; $i++) if(0!==strcmp("cnfData",$keys[$i-1])){//20190828,vpr,exclude cnfData from protocol tree
	    $key= $keys[$i-1];
	    $idName= "ChannelProtoPanel_$i";
	    $TreeView .= "<li formname='$idName'><span class='form_avail mycaret'><a href='#' >".$key."</a></span>
	    <ul class='nested'>
	    ";
	    $lines = $jamLConfig[$key]['Lines'];
	    $nline = count($lines);
	    $ndev = 0;
	    foreach($lines as $line) $ndev += count($line['LDevs']);

	    $PanelView .= "<div id='$idName' style='display:none'>
		<h4>$PotocolText $key </h4>
		<div class='row'>
		    <div class='col-md-2'> <label> $ChannelsText: </label> </div>
		    <div class='col-md-2'><div class=' panel-info form-control'> $nline </div></div>
		</div>
		<div class='row'>	
		    <div class='col-md-2'> <label> $DevicesText: </label> </div>
		    <div class='col-md-2'><div class=' panel-info form-control'> $ndev </div></div>
		</div>	
		</div>
	    ";
	    makeConfigLines($lines, $key, $dmpInfo, $TreeView,$PanelView);
	    $TreeView .= '
		</ul></li>';
	 } 
	$TreeView .= '
	    </ul></li>';

	return count($keys);
    }

    //---------------------------------------------------------------------------------------------------
    //
    //---------------------------------------------------------------------------------------------------
    function fillSesDevsFlagsMas($jamLConfig)
    {
	if (isset($_SESSION['DevsParams'])) unset($_SESSION['DevsParams']);

	$DevIndex = 0;
	foreach($jamLConfig as $prot){
	    foreach($prot['Lines'] as $line) {
		$LineInuse = $line['Inuse'] ;
		foreach($line['LDevs'] as $Device) {
		    if ( 0 == $LineInuse ) $DeviceInuse = 0;	// если линия отключена, считаем, что и девайс отключен
		    else $DeviceInuse = $Device['Inuse'];
		    $_SESSION['DevsParams'] [ $DevIndex ]  = [ 'Name'=>$Device['Name'] , 'Inuse'=>$DeviceInuse ];
		    $DevIndex++;
		}

	    }
	}     	
    }

?>