<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	session_start();
	include_once('common/conDb.php');
	include_once('admin/classObjectFunction.php');
	$objManageData = new manageData;

	$patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$patientConfirm_tblRes = imw_query($patientConfirm_tblQry) or die(imw_error());
	$patientConfirm_tblRow = imw_fetch_array($patientConfirm_tblRes);
	$finalizeStatus = $patientConfirm_tblRow["finalize_status"];
	$confirm_surgeronID		=	$patientConfirm_tblRow["surgeonId"];
	
	// Get logged in & Assigned Surgeon Practice Match Result if Peer Review Option is on
	$surgeryCenterSettings	=	$objManageData->loadSettings('peer_review');
	$surgeryCenterPeerReview=	$surgeryCenterSettings['peer_review'];
	$practiceNameMatch	=	'';
	if($surgeryCenterPeerReview == 'Y' && $_SESSION['loginUserType'] == 'Surgeon')
	{
		$practiceNameMatch	=	$objManageData->getPracMatchUserId($_SESSION['loginUserId'],$confirm_surgeronID);
	}
	// Get logged in & Assigned Surgeon Practice Match Result if Peer Review Option is on

	
?>
<script>
	top.document.getElementById("footer_button_id").style.display = "none";
	var finalizeStatus = '<?php echo $finalizeStatus;?>';
	var logUsrTyp = '<?php echo $_SESSION['loginUserType'];?>';
	var AD	=	'<?php echo $practiceNameMatch; ?>';
	
	if(top.document.getElementById("footer_signall_button_id")) {
		if(finalizeStatus!='true' && logUsrTyp == 'Surgeon' && AD !== 'yes') {
			top.document.getElementById("footer_signall_button_id").style.display = "inline-block";
		}else {
			top.document.getElementById("footer_signall_button_id").style.display = "none";
		}	
	}
</script>
<script type="text/javascript" src="js/jsFunction.js"></script>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<?php
$dis_sumry_filled = $_REQUEST['dis_sumry_filled']; 
?>
<body onLoad="" onClick="if(document.getElementById('divSaveAlert')) {document.getElementById('divSaveAlert').style.display = 'none';} return top.mainFrame.main_frmInner.hideSliders();"><!-- onClick="return hideSliders();"-->
<table height="385" bgcolor="#ECF1EA" cellpadding="0" cellspacing="0" border="0" align="center" width="100%"  >
	<?php if($dis_sumry_filled=='no') { ?>
			<tr>
				<td align="left" style="padding-left:350px; padding-top:25px;">
					<div id="divSaveAlert" style="position:absolute; left:350px; top:200px;">
						<?php 
							$bgCol = '#BCD2B0';
							$borderCol = '#BCD2B0';
							$saveSuccessfullyMessage='Discharge Summary is not complete.';
							include('saveDivPopUp.php'); 
						?>
					</div>
				</td>
			</tr>
	<?php } ?>			
</table>
</body>