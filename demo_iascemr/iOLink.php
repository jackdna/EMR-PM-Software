<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
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
<title>Surgerycenter EMR</title>
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

</script>
</head>

<body>
<!-- DIV TO DISPLAY SURGERY TIME -->
<div id="surgeryTimeDivId" style="position:absolute; width:25px; z-index:1000;background-color:#FFFF99; display:none; "></div>
<!-- DIV TO DISPLAY SURGERY TIME -->

<table width="100%" border="1" cellpadding="0" cellspacing="0" class="all_border">
	<tr>
		<td align="left">
			<div style="height:290px; overflow:auto; ">
				<iframe frameborder="0" name="iOLinkScheduleFrameId" id="iOLinkScheduleFrameId" src="iOLinkScheduler.php" width="410" height="290" scrolling="no"></iframe>
			</div>
		</td>
		<td align="left" rowspan="2" valign="top">
			<div style="height:580px; overflow:auto; ">
				<iframe frameborder="0" name="iOLinkBookSheetFrameId"  id="iOLinkBookSheetFrameId" src="iOLinkBookingSheet.php" width="573" height="580" scrolling="no"></iframe>
			</div>
		</td>
	</tr>
	<tr>
		<td align="left" >
			<div style="height:290px; overflow:auto; ">
				<iframe frameborder="0" name="iOLinkPtDetailFrameId" id="iOLinkPtDetailFrameId" src="iOLinkPtDetail.php" width="410" height="290" scrolling="no"></iframe>
			</div>
		</td>
	</tr>	
</table>
</body>
</html>
