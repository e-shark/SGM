<?php 

$saveButtonTxt = '<br><div class="row"><div class="col-md-4"></div><div class="col-md-2"><input class="btn btn-primary" type="submit" value="       Save       "></div></div><br>';
//$saveButtonTxt = '';
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
		$tmTypes= [0=>'ТИ',1=>'ТС',2=>'ТУ',3=>'ТИИ', ];
		$nblock = count($Blocks);

		for($i = 1; $i <= $nblock; $i++) {
			$par=$i."_".$key;
			$idName= "BlockPanel_$par";
			$BlockTreeLabel = _t('Block');
			$TreeView .= "<li formname='$idName'><span class='form_avail '><a href='#' > $BlockTreeLabel ".$i."</a></span></li>";
			$BlockInd = $Blocks[$i-1]['block']; $ASDU = $Blocks[$i-1]['ASDU'];
			$nsignals = getRtiTableInfo($dmpInfo,$par,$rtiTableInfo);
			$form_title = _t("Block Settings");
			$block_index = _t("Block Index");
			$table_title = _t("Signal Table");
			$PanelView .= "<div id='$idName' style='display:none'>
							<h3>$form_title:</h3>
					<div class='row'>
						<div class='col-md-4'>		$block_index: 			</div>
						<div class='col-md-2'><div class='panel panel-info'><div class='panel-body'> $BlockInd </div></div></div> 					
					</div>
					<div class='row'>
						<div class='col-md-4'>		ASDU: 			</div>
						<div class='col-md-2'>		<input class='form-control' type='text' value ='$ASDU' name='Inuse_$par' > 		</div>
					</div>
					$saveButtonTxt
					<br><br>
					<div id= 'Table_$par' class = 'signal_table'>
						<h3>$table_title:</h3>
						$rtiTableInfo
					</div>
				</div>
			";
		}
	}

	function makeConfigDevs( $Devs,$key, &$dmpInfo, &$TreeView,&$PanelView){
		global $saveButtonTxt, $InuseOn, $InuseOff,$ListKaType, $ListKaChannel;
		$ndev = count($Devs);
		$DevLabel = _t("Device Settings");	$DevTreeLabel = _t("Device");
		$DevIndLabel = _t("Device Index"); $InuseLabel = _t("Device Status"); 
		$NameLabel = _t("Name"); $CommentLabel = _t("Comment");
		$LAddressLabel = _t("Logical Address"); $LinkaddressLabel = _t("Physical Address");
		$pollperiodLabel = _t("Poll Period"); $LinkTimeoutLabel = _t("Link Timeout"); $SecText = _t("sec");
		$Command100Label = _t("Main Poll"); $Command101Label = _t("Meter Poll");
		$PotocolText = _t("Protocol");  $TypeText = _t("Type");	$ChannelText = _t("Channel");
		$DuplexText = _t("Duplex"); $HalfDuplexText = _t("Half Duplex"); 
		$KaChannelmodeLabel = _t("Channel Mode"); $YesText = _t("Yes"); $NoText = _t("No");
		$KaInversionLabel = _t("Inversion"); $Ad1Text = _t("Ad1"); $Ad2Text = _t("Ad2");
		$KaChannelSpeedLabel = _t("Baud Rate"); $msek = _t("msec");	

		for($i = 1; $i <= $ndev; $i++) {
			$par=$i."_".$key;
			$idName= "DevPanel_$par";
			$TreeView .= "<li formname='$idName'><span class='form_avail mycaret'><a href='#' >$DevTreeLabel ".$i."</a></span><ul class='nested'>
			";
			makeConfigBlocks($Devs[$i-1]['blocks'], $par, $dmpInfo, $TreeView,$PanelView);
			$TreeView .= '
				</ul></li>';
			$DevInd = $Devs[$i-1]['LDev']; $Inuse = $Devs[$i-1]['Inuse'];
			$InuseOnOff = $Inuse == 1 ? $InuseOn :  $InuseOff; $checked = $Inuse == 1 ? "checked" : "";
			$Name = $Devs[$i-1]['Name']; $Comment = $Devs[$i-1]['Comment'];
			$PanelView .= "<div id='$idName' style='display:none'>
				<h3>$DevLabel:</h3>
				<div class='row'>
					<div class='col-md-2'>	<label>	$DevIndLabel: 	</label>		</div>
					<div class='col-md-2'><div class=' panel-info form-control'> $DevInd </div></div>
				</div>
				<div class='row'>
					<div class='col-md-2'>	<label>	$NameLabel: 	</label>		</div>
					<div class='col-md-3'>		<input class='form-control' type='text' value ='$Name' name='Name_$par' > 		</div> 					
				</div>
				<div class='row'>
					<div class='col-md-2'>	<label>	$CommentLabel: 	</label>		</div>
					<div class='col-md-10'>		<input class='form-control' type='text' value ='$Comment' name='Comment_$par' > 		</div> 					
				</div>
				<div class='row'>
					<div class='col-md-2'>	<label>	$InuseLabel	</label> </div>
					<div class='col-md-2'>	<label><input type='checkbox' value ='1' name='Inuse_$par' $checked onchange='toggleCheckBoxLabelText(this)'>$InuseOnOff</label></div>
				</div>
				";

			if(null != ($LAddress =$Devs[$i-1]['LAddress'])){//s104,m104
				$PanelView .= "<div class='row'>
					<div class='col-md-2'>	<label>	$LAddressLabel: 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$LAddress' name='LAddress_$par' > 		</div> 	
				</div>";
			}
			if(null != ($Linkaddress =$Devs[$i-1]['Linkaddress'])){//s101,m101,s104,m104
				$PanelView .= "<div class='row'>
					<div class='col-md-2'>	<label>	$LinkaddressLabel: 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$Linkaddress' name='Linkaddress_$par' > 		</div>
				</div>
				";
			}
			if(null != ($pollperiod =$Devs[$i-1]['pollperiod'])){
				$PanelView .= "<div class='row'>
					<div class='col-md-2'>	<label>	$pollperiodLabel: 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$pollperiod' name='pollperiod_$par' > 		</div> 	
				</div>";
			}
			if(null != ($LinkTimeout =$Devs[$i-1]['LinkTimeout']))
				$PanelView .= "<div class='row'>
					<div class='col-md-2'>	<label>	$LinkTimeoutLabel($SecText): 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$LinkTimeout' name='LinkTimeout_$par' > 		</div> 	
				</div>";
			if(null != ($Clocksyncperiod =$Devs[$i-1]['Clocksyncperiod']))
				$PanelView .= "<div class='row'>
					<div class='col-md-2'>	<label>	$ClocksyncperiodLabel($SecText): 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$Clocksyncperiod' name='Clocksyncperiod_$par' > 		</div> 	
				</div>";

			if(null != ($Command100 =$Devs[$i-1]['Command100'])){
				$Command100OnOff = $Command100 == 1 ? $InuseOn :  $InuseOff; $checked = $Command100 == 1 ? "checked" : "";
				$PanelView .= "<div class='row'>
					<div class='col-md-2'>	<label>	$Command100Label: 	</label>		</div>
					<div class='col-md-2'>	<label><input type='checkbox' value ='1' name='Command100_$par' $checked onchange='toggleCheckBoxLabelText(this)'>$Command100OnOff</label></div>
				</div>";
			}
			if(null != ($Command101 =$Devs[$i-1]['Command101'])){
				$Command1001nOff = $Command101 == 1 ? $InuseOn :  $InuseOff; $checked = $Command101 == 1 ? "checked" : "";
				$PanelView .= "<div class='row'>
					<div class='col-md-2'>	<label>	$Command101Label: 	</label>		</div>
					<div class='col-md-2'>	<label><input type='checkbox' value ='1' name='Command101_$par' $checked onchange='toggleCheckBoxLabelText(this)'>$Command101OnOff</label></div>
				</div>
				";
			}

			if(null != ($KaType =$Devs[$i-1]['KaType'])){//mka
				$PanelView .= "<br><div class='row'>
					<div class='col-md-2'>	<label>	$PotocolText: </label>		</div>
					<div class='col-md-2'>	<select class='form-control' name='KaType_$par'>";
				foreach ($ListKaType as $item) {
					$sel = $KaType == $item ? "selected":'';
					$PanelView .= "
						<option value='$item' $sel>$item</option>
						";
				}
				$PanelView .= " </select> </div>
					";

				$KaChannel =$Devs[$i-1]['KaChannel'];	
				$PanelView .= "
					<div class='col-md-2'>	<label>	$ChannelText: </label>		</div>
					<div class='col-md-2'>	<select class='form-control' name='KaChannel_$par'>";
				foreach ($ListKaChannel as $item) {
					$sel = $KaChannel == $item ? "selected":'';
					$PanelView .= "
						<option value='$item' $sel>$item</option>
						";
				}
				$PanelView .= " </select> </div>
					 </div>
					";

				$KaChannelmode = $Devs[$i-1]['KaChannelmode'];
				$KaChannelmodeOnOff = $KaChannelmode == 1 ? $DuplexText : $HalfDuplexText; $checked = $KaChannelmode == 1 ? "checked" : "";
				$fEXtraPars = "\"$DuplexText\",\"$HalfDuplexText\"";
				$PanelView .= "<div class='row'>
						<div class='col-md-2'>	<label>	$KaChannelmodeLabel:	</label>		</div>
						<div class='col-md-2'>	<label><input type='checkbox' value ='1' name='KaChannelmode_$par' $checked onchange='toggleCheckBoxLabelText(this,$fEXtraPars)'>$KaChannelmodeOnOff</label>	</div>
				";
				$KaChannelInversion = $Devs[$i-1]['KaChannelInversion'];
				$KaChannelInversionOnOff = $KaChannelInversion == 1 ? $YesText : $NoText; $checked = $KaChannelInversion == 1 ? "checked" : "";
				$fEXtraPars = "\"$YesText\",\"$NoText\"";
				$PanelView .= "
					<div class='col-md-2'>	<label>	$KaInversionLabel:	</label>		</div>
					<div class='col-md-2'>	<label><input type='checkbox' value ='1' name='KaChannelInversion_$par' $checked onchange='toggleCheckBoxLabelText(this,$fEXtraPars)'>$KaChannelInversionOnOff</label>	</div>
					</div>
				";

				$t1 = $Devs[$i-1]['t1'];
				$PanelView .= "<div class='row'>
					<div class='col-md-2'>	<label>	t1 ($msek): 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$t1' name='t1_$par' > 		</div> 	
				";
				$t2 = $Devs[$i-1]['t2'];
				$PanelView .= "
					<div class='col-md-2'>	<label>	t2 ($msek): 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$t2' name='t2_$par' > 		</div> 	
				</div>";

				$KaChannelSpeed = $Devs[$i-1]['KaChannelSpeed'];
				$PanelView .= "<div class='row'>
					<div class='col-md-2'>	<label>	$KaChannelSpeedLabel: 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$KaChannelSpeed' name='KaChannelSpeed_$par' > 		</div> 	
					";
				$Ad1 = $Devs[$i-1]['Ad1'];
				$PanelView .= "
					<div class='col-md-2'>	<label>	$Ad1Text: 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$Ad1' name='Ad1_$par' > 		</div> 	
					";
				$Ad2 = $Devs[$i-1]['Ad2'];
				$PanelView .= "
					<div class='col-md-2'>	<label>	$Ad2Text: 	</label>		</div>
					<div class='col-md-2'>		<input class='form-control' type='text' value ='$Ad2' name='Ad2_$par' > 		</div> 	
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
		$lineIndLabel = _t("Channel Index"); $InuseLabel = _t("Channel Status"); 
		$PortLabel = _t("Port"); $NameLabel = _t("Name"); $CommentLabel = _t("Comment");
		$MasterIpLabel = _t("Master IP");
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
						<div class='col-md-2'>	<label>	$lineIndLabel: 	</label>		</div>
						<div class='col-md-2'><div class=' panel-info form-control'> $lineInd </div></div>
					</div>
					<div class='row'>
						<div class='col-md-2'>	<label>	$NameLabel: 	</label>		</div>
						<div class='col-md-3'>		<input class='form-control' type='text' value ='$Name' name='Name_$par' > 		</div> 					
					</div>
					<div class='row'>
						<div class='col-md-2'>	<label>	$CommentLabel: 	</label>		</div>
						<div class='col-md-10'>		<input class='form-control' type='text' value ='$Comment' name='Comment_$par' > 		</div> 					
					</div>
					<div class='row'>
						<div class='col-md-2'>	<label>	$InuseLabel:	</label>		</div>
						<div class='col-md-2'>	<label><input type='checkbox' value ='1' name='Inuse_$par' $checked onchange='toggleCheckBoxLabelText(this)'>$InuseOnOff</label>	</div>
					</div>
					";

			if(isset($lines[$i-1]['Port'])){
				$PanelView .= "
					<div class='row'>
						<div class='col-md-2'>	<label>	$PortLabel: </label>	</div>
						<div class='col-md-2'>		<input class='form-control' type='text' value ='".$lines[$i-1]['Port']."' name='Port_$par' > 	</div>
					 </div>
				";
			}
			if(isset($lines[$i-1]['IpAddress'])){//m104,mka
				$PanelView .= "
					<div class='row'>
						<div class='col-md-2'>	<label>	IP $Address:  </label>	</div>
						<div class='col-md-2'>		<input class='form-control' type='text' value ='".$lines[$i-1]['IpAddress']."' name='IpAddress_$par' > 	</div>
					 </div>
				";
			}
			if(isset($lines[$i-1]['consoleport'])){//mka
				$PanelView .= "
					<div class='row'>
						<div class='col-md-2'>	<label>	$ConcoleText $PortLabel:  </label>	</div>
						<div class='col-md-2'>		<input class='form-control' type='text' value ='".$lines[$i-1]['consoleport']."' name='consoleport_$par' > 	</div>
					 </div>
				";
			}
			if(isset($lines[$i-1]['consoletout'])){//mka
				$PanelView .= "
					<div class='row'>
						<div class='col-md-2'>	<label>	$ConcoleText $OutText:  </label>	</div>
						<div class='col-md-2'>		<input class='form-control' type='text' value ='".$lines[$i-1]['consoletout']."' name='consoletout_$par' > 	</div>
					 </div>
				";
			}

			if(isset($lines[$i-1]['ClockSync'])) {
				$ClockSync = $lines[$i-1]['ClockSync'];
				$ClockSyncOnOff = $ClockSync == 1 ? $InuseOn :  $InuseOff; $checked = $ClockSync == 1 ? "checked" : "";
				$PanelView .= "<div class='row'>
						<div class='col-md-2'>	<label>	$ClockSyncLabel:	</label>		</div>
						<div class='col-md-2'>	<label><input type='checkbox' value ='1' name='ClockSync_$par' $checked onchange='toggleCheckBoxLabelText(this)'>$ClockSyncOnOff</label>	</div>
					</div>
				";
			}

			if(null != ($comdev =$lines[$i-1]['comdev'])){//s101
				$PanelView .= "<br><div class='row'>
					<div class='col-md-1'>	<label>	$PortLabel: </label>		</div>
					<div class='col-md-3'>	<select class='form-control' name='comdev_$par'>";
				foreach ($ListPort as $item) {
					$sel = $comdev == $item ? "selected":'';
					$PanelView .= "
						<option value='$item' $sel>$item</option>
						";
				}
				$PanelView .= " </select> </div>
					";
				if(null != ($Mode =$lines[$i-1]['Mode'])){//s101
					$PanelView .= "
						<div class='col-md-1'>	<label>	$ModeText: </label>		</div>
						<div class='col-md-2'>	<select class='form-control' name='Mode_$par'>";
					foreach ($ListMode as $item) {
						$sel = $Mode == $item ? "selected":'';
						$PanelView .= "
							<option value='$item' $sel>$item</option>
							";
					}
					$PanelView .= " </select> </div>
						";
				}

				if(null != ($comdevmode =$lines[$i-1]['comdevmode'])){//s101 modbus
					$PanelView .= "
						<div class='col-md-1'>	<label>	$ComDevModeText: </label>		</div>
						<div class='col-md-4'>	<div class=' panel-info form-control'> $comdevmode </div></div>";
				}
				$PanelView .= "</div>
					";
				
				$PanelView .= "<div class='row'>
					<div class='col-md-1'>	<label>	$ResponseTimeoutText ($SecText): </label>		</div>
					<div class='col-md-2'>	<input class='form-control' type='text' value ='".$lines[$i-1]['ResponseTimeout']."' name='ResponseTimeout_$par' ></div>";
				if(null != ($LinkTestTimeout =$lines[$i-1]['LinkTestTimeout'])) //s101	
					$PanelView .= "
					<div class='col-md-2'>	<label>	$LinkTestText $ResponseTimeoutText ($SecText): </label>		</div>
					<div class='col-md-1'>	<input class='form-control' type='text' value ='".$LinkTestTimeout."' name='LinkTestTimeout_$par' ></div>";
				if(null != ($Retries =$lines[$i-1]['Retries'])) //s101	
					$PanelView .= "
						<div class='col-md-1'>	<label>	$RetriesText: </label>		</div>
					<div class='col-md-2'>	<input class='form-control' type='text' value ='".$Retries."' name='Retries_$par' ></div>";
				$PanelView .= "</div>
					";
			}

			if(isset($lines[$i-1]['t1'])){
				$PanelView .= "	<br><div class='row'> 
						<div class='col-md-1'> <label> t1,($SecText.):</label> </div>
						<div class='col-md-2'>	<input class='form-control' type='text' value ='".$lines[$i-1]['t1']."' id='t1_$par' name='t1_$par'>	</div>		
						<div class='col-md-1'>	<label> t2,($SecText.):</label></div>
						<div class='col-md-2'>	<input class='form-control' type='text' value ='".$lines[$i-1]['t2']."' name='t2$par'>	</div>		
						<div class='col-md-1'>	<label> t3,($SecText.):</label></div>
						<div class='col-md-2'>	<input class='form-control' type='text' value ='".$lines[$i-1]['t3']."' name='t3_$par'>	</div>		
					</div>
					<div class='row'>
						<div class='col-md-1'>	<label>	k :	</label>		</div>
						<div class='col-md-2'>		<input class='form-control' type='text' value ='".$lines[$i-1]['k']."' name='k_$par' > 		</div> 					
						<div class='col-md-1'>	<label>	w : </label>		</div>
						<div class='col-md-2'>		<input class='form-control' type='text' value ='".$lines[$i-1]['w']."' name='w_$par' > 		</div> 					
					</div>
					";
			}

			if(null != ($IOAL =$lines[$i-1]['IOAL'])) {		 					
				$PanelView .= "
					<div class='row'>
						<div class='col-md-1'>	<label>	IOAL: </label>		</div>
						<div class='col-md-2'><div class=' panel-info form-control'>". $IOAL." </div></div>
					";

				if(null != ($ASDUAL =$lines[$i-1]['ASDUAL'])) {		 					
					$PanelView .= "
						<div class='col-md-1'>	<label>	ASDUAL: </label>		</div>
						<div class='col-md-2'>	<select class='form-control' name='ASDUAL_$par'>";
					foreach ($ListASDUAL as $item) {
						$sel = $ASDUAL == $item ? "selected":'';
						$PanelView .= "
							<option value='$item' $sel>$item</option>
							";
					}
					$PanelView .= " </select> </div>
						";
				}	
				if(!isset($lines[$i-1]['LAL'])) $PanelView .= " </div>
					";
			}
			
			if(null != ($LAL =$lines[$i-1]['LAL'])) {//s101
				$PanelView .= "
					<div class='col-md-1'>	<label>	LAL: </label>		</div>
					<div class='col-md-2'>	<select class='form-control' name='LAL_$par'>";
				foreach ($ListLAL as $item) {
					$sel = $LAL == $item ? "selected":'';
					$PanelView .= "
						<option value='$item' $sel>$item</option>
						";
				}
				$PanelView .= " </select> </div>
					";
				$COTL =$lines[$i-1]['COTL'];	
				$PanelView .= "
					<div class='col-md-1'>	<label>	COTL: </label>		</div>
					<div class='col-md-2'>	<select class='form-control' name='COTL_$par'>";
				foreach ($ListCOTL as $item) {
					$sel = $COTL == $item ? "selected":'';
					$PanelView .= "
						<option value='$item' $sel>$item</option>
						";
				}
				$PanelView .= " </select> </div>
					";
				$PanelView .= " </div>
					";
			}

			$MasterConnections = $lines[$i-1]['MasterConnections']; 
			if(isset($MasterConnections)){//s104
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
			$PanelView .= " $saveButtonTxt 
				</div>
				";
		}
	}

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

		$PanelView = "<div id='ConfPanel'  style='display:none'>
			<h4> $Сonfig $DevicesText </h4>
			<div class='row'>
				<div class='col-md-2'> <label> $PotocolsText: </label> </div> 
				<div class='col-md-2'><div class=' panel-info form-control'> $cnt </div></div>
			</div>
			<div class='row'>
				<div class='col-md-2'> <label> $ChannelsText: </label> </div> 
				<div class='col-md-2'><div class=' panel-info form-control'> $nline </div></div>
			</div>
			<div class='row'>
				<div class='col-md-2'> <label> $DevicesText: </label> </div> 
				<div class='col-md-2'><div class=' panel-info form-control'> $ndev </div></div>
			</div> </div>
				";

		for($i = 1; $i <= $cnt; $i++) {
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
?>