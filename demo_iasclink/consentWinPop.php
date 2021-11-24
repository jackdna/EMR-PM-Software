<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
include("common/link_new_file.php");
$objManageData = new manageData;
$hidd_patient_id=$_REQUEST['hidd_patient_id'];
$hidd_patient_in_waiting_id=$_REQUEST['hidd_patient_in_waiting_id'];
$hidd_selected_template_id=$_REQUEST['hidd_selected_template_id'];
$hidd_consentMultipleTemplateId=$_REQUEST['hidd_consentMultipleTemplateId'];
?>
<html>
<head>
<title>Scan Pop-Up</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<table border="0" cellpadding="0" cellspacing="0" width="99%">
	<tr>
		<td bgcolor="<?php echo $rowcolor_discharge_summary_sheet; ?>" class="text_print" align="left">
			<table border="0" bordercolor="<?php echo $borderCol; ?>" cellpadding="0" cellspacing="0" width="100%" style=" border:0.5px;">
				<tr><td colspan="2" height="3"></td></tr>	
				<tr>
					<td bgcolor="<?php echo $rowcolor_discharge_summary_sheet; ?>" class="text_print" align="left" valign="middle">
						<img src="images/stop.gif" align="bottom" alt="stop">
					</td>
				</tr>
				<tr>
					<td align="left">
						<div>
							<a href="#"  onClick="MM_swapImage('scanConsentBtn','','images/scan_click.gif',1)"  onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('scanConsentBtn','','images/scan_hover.gif',1)" name="scanConsentBtn" width="70" height="25" border="0"><img src="images/scan.gif"  name="scanConsentBtn"  height="25" border="0" id="scanConsentBtn" alt="scan" onClick="javascript:window.open('admin/scanPopUp.php?patient_id='+<?php echo $hidd_patient_id; ?>+'&patient_in_waiting_id='+<?php echo $hidd_patient_in_waiting_id; ?>+'&scaniOLinkConsentId='+<?php echo $hidd_selected_template_id; ?>+'&CONSENTScan=true','scanWinCONSENT', 'width=775, height=650,location=yes,status=yes'); document.getElementById('scan').value = 'true';"/></a>
						</div>
					</td>
				</tr>
					
			</table>		
	
</table>
</body>
</html>
