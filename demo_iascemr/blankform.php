<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once('common/conDb.php'); 
?>
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<link rel="stylesheet" href="css/simpletree.css" type="text/css" />
<script type="text/javascript" src="js/simpletreemenu.js"></script>
<?php
//include("common/linkfile.php");
//START INCLUDE PREDEFINE FUNCTIONS
include_once("common/pre_define_medication.php"); //FOR LOCAL ANES (ALSO FOR PRE-OP HEALTH QUEST, PRE-OP GEN ANES)
include_once("common/patient2takehome.php"); //PREDEFINE FOR POST OP  PHYSICIAN

include("common/laserpredefine_chief_complaint_pop.php"); //LASER PROCEDURE
include_once("common/laserpredefine_past_medicalHX_pop.php"); //LASER PROCEDURE
include_once("common/laserpredefine_present_illness_hx_pop.php"); //LASER PROCEDURE
include_once("common/laserpredefine_medication_pop.php"); //LASER PROCEDURE
//END INCLUDE PREDEFINE FUNCTIONS

include("common/link_new_file.php");
//include("common/mainslider.php");
$pConfId = $_GET['pConfId'];
$patient_id = $_GET['patient_id'];
$frameHref = $_GET['frameHref'];
$thisId = $_GET['thisId'];
$innerKey = $_GET['innerKey'];
$preColor = $_GET['preColor'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$finalize_alert = $_REQUEST['finalize_alert'];
$dis_sumry_filled =$_REQUEST['dis_sumry_filled'];
$consentMultipleId =$_REQUEST['consentMultipleId'];
$stub_id = $_REQUEST['stub_id'];
$myPage = $_REQUEST['myPage'];
if($consentMultipleId) {
	$consentMultipleIdLink = '&consentMultipleId='.$consentMultipleId;
}
//PURGE
$consentMultipleAutoIncrId = $_REQUEST['consentMultipleAutoIncrId'];
if($consentMultipleAutoIncrId) {
	$consentMultipleAutoIncrId = '&consentMultipleAutoIncrId='.$consentMultipleAutoIncrId;
}
//PURGE

$chk = $_GET['chk'] == true ? '?' : '&';
if(!$frameHref){
	if($myPage){		
		 $frameHref = $myPage.'?pConfId='.$pConfId.'&stub_id='.$stub_id.'&dis_sumry_filled='.$dis_sumry_filled.$consentMultipleIdLink.$consentMultipleAutoIncrId;
	}
	else{
		$frameHref = 'blank_mainform.php?pConfId='.$pConfId.'&stub_id='.$stub_id.'&dis_sumry_filled='.$dis_sumry_filled;
	}
}else {
	//$frameHref = $frameHref.'?'.$chk.'thisId='.$thisId.'&innerKey='.$innerKey.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&ascId='.$ascId.'&SaveForm_alert='.$SaveForm_alert.'&Save_Print_alert='.$Save_Print_alert;
	$frameHref = $frameHref.'?thisId='.$thisId.'&innerKey='.$innerKey.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&stub_id='.$stub_id.'&ascId='.$ascId.'&SaveForm_alert='.$SaveForm_alert.'&finalize_alert='.$finalize_alert.'&Save_Print_alert='.$Save_Print_alert.$consentMultipleIdLink.$consentMultipleAutoIncrId;
}
?>
<script>
function setPNotesHeight()
{	
	//SET SCROLL HEIGHT OF FORMS
	var obj = top.mainFrame.main_frmInner;
	var hgt = obj.document.body.scrollHeight;
	//hgt = (hgt > 442) ? 442 : hgt;
	hgt = (hgt > 400) ? 400 : hgt;
	
	//RESET SCROLL HEIGHT FOR SOME CHART NOTES
	if(top.mainFrame.main_frmInner) {
		if(top.mainFrame.main_frmInner.document.forms[0]) {
			if(top.mainFrame.main_frmInner.document.forms[0].frmAction) {
				if(top.mainFrame.main_frmInner.document.forms[0].frmAction.value) {
					var formActionName = top.mainFrame.main_frmInner.document.forms[0].frmAction.value;
					if(formActionName=='local_anes_record.php' || formActionName=='gen_anes_rec.php') {
						hgt=410;
					}
				}
			}
		}
	}
	//END RESET SCROLL HEIGHT FOR SOME CHART NOTE
	
	if(document.getElementById("main_frmInner_id")) {
		document.getElementById("main_frmInner_id").height = hgt +"px";	
	}
	//END SET SCROLL HEIGHT OF FORMS
	
}
function displayMainFooter() {
	var frmObj = top.frames[0].frames[0].document.forms[0];
	var  val_url;
	if(frmObj.go_pageval) {
		val_url=frmObj.go_pageval.value;
	}
	top.document.getElementById("footer_button_id").style.display = "inline-block";
	if(top.document.getElementById("footer_signall_button_id")) {
		top.document.getElementById("footer_signall_button_id").style.display = "none";
	}
	if(top.document.getElementById("footer_print_button_id")) {
		top.document.getElementById("footer_print_button_id").style.display = "none";
	}	
	if(top.document.getElementById('saveBtn').style.display == 'none') {
		top.document.getElementById('saveBtn').style.display = 'inline-block';
	}
	if(top.document.getElementById('CancelBtn').style.display == 'none') {
		top.document.getElementById('CancelBtn').style.display = 'inline-block';
	}
	if(top.document.getElementById('SavePrintBtn').style.display == 'none') {
		top.document.getElementById('SavePrintBtn').style.display = 'inline-block';
	}
	if(top.document.getElementById('Finalized').style.display == 'none') {
		top.document.getElementById('Finalized').style.display = 'inline-block';
	}
	if(top.document.getElementById('AmendmentFinalized').style.display == 'inline-block') {
		top.document.getElementById('AmendmentFinalized').style.display = 'none';
	}
	
	top.document.getElementById('PrintMedsBtn').style.display = 'none';
	if(val_url=='dischargesummarysheet') {
		top.document.getElementById('PrintMedsBtn').style.display = 'inline-block';
	}	
}
function displayFooterPrintButton() {
	var frmObj = top.frames[0].frames[0].document.forms[0];
	var  val_url;
	if(frmObj.go_pageval) {
		val_url=frmObj.go_pageval.value;
	}
	if(top.document.getElementById("footer_print_button_id")) {
		top.document.getElementById("footer_print_button_id").style.display = "inline-block";
	}
	if(val_url=='dischargesummarysheet') {
		top.document.getElementById('PrintMedsBtnNew').style.display = 'inline-block';
	}else {
		top.document.getElementById('PrintMedsBtnNew').style.display = 'none';	
	}
		
}

function remove_save_buttons() {
	if(top.document.getElementById('saveBtn')) {
		top.document.getElementById('saveBtn').style.display = 'none';
	}
	if(top.document.getElementById('SavePrintBtn')) {
		top.document.getElementById('SavePrintBtn').style.display = 'none';
	}	
}
//DO NOT DISPLAY PRINT BUTTON ON BLANK FORM AFTER FINALIZE
	if(top.document.getElementById("footer_print_button_id")) {
		top.document.getElementById("footer_print_button_id").style.display = "none";
	}	
//DO NOT DISPLAY PRINT BUTTON ON BLANK FORM
</script>
<body ><!-- onClick="return hideSliders();"-->
<table  style="height:100%;" bgcolor="#ECF1EA" cellpadding="0" cellspacing="0" border="0" align="center" width="99%"  >
	<tr>
		<td valign="top">
			
			 <iframe style="height:100%;" id="main_frmInner_id" name="main_frmInner" src="<?php echo $frameHref;?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" scrolling="Yes" ></iframe> 
		</td>
	</tr>
</table>
</body>
<form name="mainFrmForm" method="post">
	<input type="hidden" value="<?php echo $innerKey; ?>" name="innerKeyText">
</form>

<script>
	//top.setPNotesHeight();
</script>