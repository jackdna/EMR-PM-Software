<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
set_time_limit(500);
include_once("common/conDb.php");
include("common/link_new_file.php");
include("common/iOlinkFunction.php");
?>
<html>
<head>
<title>iOLink EMR</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
function iOLink_swap_cal_color(obj,cond){
	if(document.getElementById(obj)){
		if(cond=="Yes"){
			document.getElementById(obj).style.backgroundColor="#FBD78D";
		}else{
			document.getElementById(obj).style.backgroundColor="";
		}
	}
}
function cancelBookedPatient(ptWaitId) {
	if(document.getElementById('del_reason').value=='') {
		alert('Please select reason');
	}else {
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null){
			alert ("Browser does not support HTTP Request")
			return
		 }
		var url="iOLinkPtCancel.php"
		url=url+"?cancelSubmitId="+ptWaitId
		url=url+"&cancelComment="+document.getElementById('del_reason').value;
		xmlHttp.onreadystatechange=iOLinkCancelPatientFun 
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
		
		document.getElementById("ptCancelDiv").style.display='none';
		
	}
}

function iOLinkCancelPatientFun() {
	if(xmlHttp.readyState==1) {
		document.getElementById("PatientInfoIdDiv").innerHTML='<center><img src="images/pdf_load_img.gif"></center>';
	}
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
			//top.iframeHome.iOLinkPtDetailFrameId.location.reload();
			var schedulerFrameObj = top.iframeHome.iOLinkScheduleFrameId;
			alert('Record Canceled');
			top.iframeHome.iOLinkPtDetailFrameId.waitingPatient_info();
			top.iframeHome.iOLinkBookSheetFrameId.location.reload();
			//top.iframeHome.iOLinkScheduleFrameId.location.reload();
			schedulerFrameObj.iOLink_change_month(schedulerFrameObj.document.getElementById('selected_month_number').value,schedulerFrameObj.document.getElementById('year_now').value,schedulerFrameObj.document.getElementById('surgeon_name_id').value);
	} 
}
</script>
</head>

<body>
<!-- DIV TO DISPLAY SURGERY TIME -->
<div id="surgeryTimeDivId" style="position:absolute; width:25px; z-index:1000;background-color:#FFFF99; display:none; "></div>
<!-- DIV TO DISPLAY SURGERY TIME -->

<!-- OPEN CONFIRM POPUP TO CANCEL THE PATIENT  -->
<div style="position:absolute;top:280px;left:400px;display:none;" id="ptCancelDiv"></div>
<!-- OPEN CONFIRM POPUP TO CANCEL THE PATIENT  -->
<table width="99%" border="0" cellpadding="0" cellspacing="0" style="border-top:solid 3px #6FA939; ">
	<tr>
		<td colspan="2" align="left" valign="top">
			<div style="height:290px; ">
				<iframe frameborder="0" name="iOLinkBookSheetFrameId"  id="iOLinkBookSheetFrameId" src="iOLinkBookingSheet.php" style="width:1240px; height:290px;" scrolling="no"></iframe>
			</div>
		</td>
	</tr>
	<tr>
		<td align="left">
			<div style="height:382px;">
				<iframe name="iOLinkScheduleFrameId" id="iOLinkScheduleFrameId" src="iOLinkScheduler.php" style="width:480px; height:382px;" frameborder="0" scrolling="no"></iframe>
			</div>
		</td>
		<td align="left" >
			<div style="height:382px;">
				<iframe name="iOLinkPtDetailFrameId" id="iOLinkPtDetailFrameId" src="iOLinkPtDetail.php"  style="width:760px; height:382px;" frameborder="0" scrolling="no"></iframe>
			</div>
		</td>
	</tr>
</table>
</body>
</html>
