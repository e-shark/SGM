<?php /*Secure page from unauthorised access*/ include 'libauth.php'; session_start(); if( !isAuthorized() ) { header('Location: index.php'); exit(); } ?>	
<?php 

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

	//phpinfo();
	require($_SERVER['DOCUMENT_ROOT'] . "/lldirs.php"); 
	require($_SERVER['DOCUMENT_ROOT'] . "/v_header.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/llConfig.php"); 
	require($_SERVER['DOCUMENT_ROOT'] . "/llRti.php");	 

?>

<div class="row">
	<div class="col-md-3">
	<ul id='myUL' class ="nav nav-pills nav-stacked">

		<li id = "HomeId"  role="presentation" formname='HomePanel'><span class="form_avail"><a href="#" > <?=_t('Status')?> </a></span></li>

<?php 
	$jamLConfig= yaml_parse_file($jamLConfigFile);
	$dmpInfo = makeRtiView($rtuDumpFileName, $runTimeInfo );
	fillSesDevsFlagsMas( $jamLConfig );
	//echo "<br>--------------<br>".print_r( $_SESSION['DevsParams'] ,true)."<br>-------------------------<br>";
	makeConfigTreeItems( $jamLConfig, $dmpInfo, $TreeView, $PanelView);
	//logger("jamLConfig\n".print_r($jamLConfig,true));
	echo $TreeView;
	$LablelTextOn = "'"._t("On")."'"; $LablelTextOff = "'"._t("Off")."'";
?>
		<li role="presentation" id="navDnldCfg"> <a href="monitor.php" ><?=_t("Monitor")?></a></li>
		<li role="presentation" id="navDnldCfg"> <a href="cdnldcfg.php" ><?=_t("Download Config")?></a></li>
		<li role="presentation" id="navUpldCfg"> <a href="vupldcfg.php" ><?=_t("Upload Config")?></a></li>
		<!-- <li role="presentation" id="navUpload"> <a href="vupload.php" ><?=_t("Upload Firmware")?></a></li> -->
		<li role="presentation" id="navReboot"> <a href="vreboot.php"  ><?=_t("Reboot")?></a></li>
		<li role="presentation" id="navLog"> <a href="vviewlog.php" ><?=_t("View Log")?></a></li>
		<li role="presentation" id="navAdmin"> <a href="vadmin.php" ><?=_t("Administration")?></a></li>
		<li role="presentation" id="navLogout"> <a href="clogin.php?logout" ><?=_t("Logout")?></a></li>
	</ul>
	</div>
	<div class="col-md-9">
	<div id="HomePanel">
	 <?= $runTimeInfo ?>	
	</div>
	<form class="form-horizontal" id="form1" method="post" action="csavecnf.php" >
	 <?= $PanelView ?>	
	</form>	
	</div>
</div> <!-- row -->

<script>

var toggler = document.getElementsByClassName("form_avail");
var cur_panel = document.getElementById("HomeId");
var cur_signal_table = null;
var cur_connect_table = null;

var i;
for (i = 0; i < toggler.length; i++) {
  toggler[i].addEventListener("click", function() {
    var par = this.parentElement;
    var nest = par.querySelector(".nested");
    if(nest !=null) nest.classList.toggle("active");
    if(cur_panel != null && cur_panel != par){
		var idname =cur_panel.getAttribute('formname');
		if(idname != null) $('#'+idname).hide();
    }
	idname = par.getAttribute('formname');
	form = null;
	if(idname != null) { form =  document.getElementById(idname); $('#'+idname).show();}
	if(cur_panel != par) cur_panel = par;
	if(form != null) cur_signal_table = form.querySelector(".signal_table");
//		if(cur_panel != par && nest.className.indexOf('active') + 1) cur_panel = par; else cur_panel = null;
	if(form != null) cur_connect_table = form.querySelector(".Connection_table");
    if(nest !=null) this.classList.toggle("mycaret-down");
  });
}

	function getRti() {
	    $.ajax({
	        url:'ajaxrti.php',
	        type:'POST',
	        data:{'reqfunc':'RTI'},
	        dataType:'json',
	        success:function (data) {
              $("#HomePanel").html(data);
	        },
	        error:function() {
              $("#HomePanel").html('AJAX error!');
    	    }
	    });
	    return false;
	}

	function getSignal() {
	    if(cur_signal_table == null) return;
	    
	    $.ajax({
	        url:'ajaxrti.php',
	        type:'POST',
	        data:{'reqfunc':'Singnals', 'signalInfo':cur_signal_table.id},
	        dataType:'json',
	        success:function (data) {
              cur_signal_table.innerHTML=data;
	        },
	        error:function(data) {
              cur_signal_table.innerHTML='AJAX error!'+data;
    	    }
	    });
	    return false;
	}

	function getConnections() {
		if(cur_connect_table == null) return;
	    
		$.ajax({
			url:'ajaxrti.php',
			type:'POST',
			data:{'PortInfo':cur_connect_table.getAttribute('port'), 'reqfunc':'Connections'},
			dataType:'json',
			success:function (data) {
				cur_connect_table.innerHTML=data;
			},
			error:function(data) {
				cur_connect_table.innerHTML='AJAX error!'+data;
			}
		});
		return false;
	}   

	window.onload = function() {
		setInterval( getRti, 6000 ); 
		setInterval( getSignal, 3000 ); 
		setInterval( getConnections, 5000 ); 
		return true;	// true, чтобы onload распространялся и дальше
	}

    function toggleCheckBoxLabelText(el,onText = <?=$LablelTextOn?>,offText = <?=$LablelTextOff?>) {
        var checked = el.checked;
        var label = el.parentElement;
        var labelHTML = label.innerHTML; 
        if (checked) {
            labelHTML = labelHTML.replace('>'+offText, '>'+onText);
            labelHTML = labelHTML.replace("onchange", " checked onchange");
        }
        else {
           labelHTML = labelHTML.replace( '>'+onText, '>'+offText);
           labelHTML = labelHTML.replace(' checked="" onchange', "onchange");
        }
        label.innerHTML = labelHTML;
        return true;
    }

	function doReboot(){
		$.ajax({
			url:'ajaxutils.php',
			type:'POST',
			data:{'reqfunc':'Reboot'},
			dataType:'json',
			success:function (data) {
				alert( data );
			},
			error:function() {
				alert( "Не удалось перезагрузить устройство!" );
			}
		});
	}

</script>


<?php require($_SERVER['DOCUMENT_ROOT'] . "/v_footer.php");?> 