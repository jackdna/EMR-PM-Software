<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
$userPrivileges = $_SESSION['userPrivileges'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Report Tabs</title>
	<meta name="viewport" content="width=device-width, maximum-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style type="text/css">
		a.black:hover{ color:"Red";	text-decoration:none; }
		a.white { color:#FFFFFF; text-decoration:none; }
		.text_tab{
			font-family:Verdana;
			font-size:14px;
			color:#000000;
			font-weight:bold;
		}
	</style>
	<link rel="stylesheet" href="css/simpletree.css" type="text/css" />
	<?php
		$spec = '
		</head>
		<body>';
		 include("common/link_new_file.php");
	?>
	<script type="text/javascript">
	function frameSrc(source){
		top.frames[0].frames[0].location.href = source;				
	}
	function changeMe(tab, tabLink, n){
		for(var i=1; i<=12; i++){
			document.getElementById('Tab'+i).style.background = "#BCD2B0";
			document.getElementById('Tab'+i+'Link').className = "black";
			document.getElementById('img'+i+'Left').src ="images/left.gif";
			document.getElementById('img'+i+'Right').src ="images/right.gif";
		}
		document.getElementById(tab).style.background = "#003300";
		document.getElementById(tabLink).className = "white";
		document.getElementById('img'+n+'Left').src ="images/leftDark.gif";
		document.getElementById('img'+n+'Right').src ="images/rightDark.gif";
	}	
	function closeMe(){
		//CHANGE TAB COLOR OF ADMIN
		if(top.document.getElementById("reportsTab")) {
			top.document.getElementById("reportsTab").className="link_a";
			top.document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
			top.document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
			top.document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
		}
		//END CHANGE TAB COLOR OF ADMIN
		
		<?php 
		if(($userPrivileges=='Admin') || ($userPrivileges=='Super User')){ 
			?>
			top.frames[0].location = 'home_inner_front.php';
			<?php
		}else{
			?>
			//top.location = 'home.php';
			top.frames[0].location = 'home_inner_front.php'; // REQUIREMENT CHANGED
			<?php
		}
		?>
	}
	</script>
	
	<table class="text_printb table_collapse">
		
        <tr>
			<td>
            	<table class="table_pad_bdr">
                    <tr> 
                        <td class="alignLeft nowrap">
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('day_report.php');" style="cursor:pointer;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img1Left" src="images/leftdark.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:155px; background-color:#003300" id="Tab1" class="text_tab alignCenter" onClick="return changeMe('Tab1', 'Tab1Link', '1');"><a id="Tab1Link" href="javascript:frameSrc('day_report.php');" class="white">Day Report</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img1Right" src="images/rightdark.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>	
                        </td>
                        <td class="nowrap">
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('discharge_summary_report.php');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img2Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:250px; background-color:#BCD2B0" id="Tab2" class="text_tab" onClick="return changeMe('Tab2', 'Tab2Link', '2');"><a id="Tab2Link" href="javascript:frameSrc('discharge_summary_report.php');" class="black">Discharge Summary Report</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img2Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table_pad_bdr nowrap">
                                <tr onClick="javascript:frameSrc('procedural_report.php');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img3Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:150px; background-color:#BCD2B0;" id="Tab3" class="text_tab" onClick="return changeMe('Tab3', 'Tab3Link', '3');"><a id="Tab3Link" href="javascript:frameSrc('procedural_report.php');" class="black">Procedural Report</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img3Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('physician_report.php');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img4Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:150px; background-color:#BCD2B0;" id="Tab4" class="text_tab nowrap" onClick="return changeMe('Tab4', 'Tab4Link', '4');"><a id="Tab4Link" href="javascript:frameSrc('physician_report.php');" class="black">Physician Report</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img4Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('unfinalizedpatient_report.php');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img5Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:250px; background-color:#BCD2B0;" id="Tab5" class="text_tab nowrap" onClick="return changeMe('Tab5', 'Tab5Link', '5');"><a id="Tab5Link" href="javascript:frameSrc('unfinalizedpatient_report.php');" class="black">Un-Finalized Patient Report</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img5Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                        
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('unfinalizedpatient_report.php');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"></td>
                                    <td class="text_tab alignRight"><img style="cursor:pointer;" src="images/close.jpg" onClick="return closeMe();"></td>
                                    <td style="width:1px;" class="alignLeft padd0"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
		</tr>
        <tr>
			<td>
            	<table class="table_pad_bdr">
        
                    <tr> 
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('day_anesthesia_chart_report.php');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img6Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:180px; background-color:#BCD2B0;" id="Tab6" class="text_tab" onClick="return changeMe('Tab6', 'Tab6Link', '6');"><a id="Tab6Link" href="javascript:frameSrc('day_anesthesia_chart_report.php');" class="black">Day Anesthesia Chart</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img6Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('day_surgeon_op_notes_report.php');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img7Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:180px; background-color:#BCD2B0;" id="Tab7" class="text_tab" onClick="return changeMe('Tab7', 'Tab7Link', '7');"><a id="Tab7Link" href="javascript:frameSrc('day_surgeon_op_notes_report.php');" class="black">Day Surgeon OP Notes</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img7Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('iol_report');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img8Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:90px; background-color:#BCD2B0;" id="Tab8" class="text_tab" onClick="return changeMe('Tab8', 'Tab8Link', '8');"><a id="Tab8Link" href="javascript:frameSrc('iol_report.php');" class="black">IOL Report</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img8Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('supply_used_report');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img9Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:160px; background-color:#BCD2B0;" id="Tab9" class="text_tab" onClick="return changeMe('Tab9', 'Tab9Link', '9');"><a id="Tab9Link" href="javascript:frameSrc('supply_used_report.php');" class="black">Supply Used Report</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img9Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('proc_phy_report');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img10Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:130px; background-color:#BCD2B0;" id="Tab10" class="text_tab" onClick="return changeMe('Tab10', 'Tab10Link', '10');"><a id="Tab10Link" href="javascript:frameSrc('proc_phy_report.php');" class="black">Proc CSV Report</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img10Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('proc_phy_report');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img11Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:100px; background-color:#BCD2B0;" id="Tab11" class="text_tab" onClick="return changeMe('Tab11', 'Tab11Link', '11');"><a id="Tab11Link" href="javascript:frameSrc('cpr_export.php');" class="black">CPR Export</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img11Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
		</tr>
		<tr>
			<td>
            	<table class="table_pad_bdr">
                    <tr> 
                        <td>
                            <table class="table_pad_bdr">
                                <tr onClick="javascript:frameSrc('patient_monitor_report.php');" style="cursor:hand;">
                                    <td style="width:1px;" class="alignRight padd0"><img id="img12Left" src="images/left.gif" style="width:1px; height:24px;"></td>
                                    <td style="width:155px; background-color:#BCD2B0;" id="Tab12" class="text_tab" onClick="return changeMe('Tab12', 'Tab12Link', '12');"><a id="Tab12Link" href="javascript:frameSrc('patient_monitor_report.php');" class="black">Patient Monitor</a></td>
                                    <td style="width:1px;" class="alignLeft padd0"><img id="img12Right" src="images/right.gif" style="width:1px; height:24px;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
		</tr>
		<tr style="background-color:#003300; height:4px;">
			<td ></td>
		</tr>	
		<tr>
			<td class="alignCenter"  >
				<iframe name="iframereport" src="day_report.php" style="width:100%; height:480px;" frameborder="0" scrolling="no"></iframe>
				<!--<iframe name="iframereport" src="procedural_report.php" width="100%" height="400" frameborder="0" scrolling="no"></iframe>-->
			</td>
		</tr>
	</table>
</body>
</html>