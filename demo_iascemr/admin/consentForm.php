<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include("adminLinkfile.php");
include_once("fckeditor/fckeditor.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$consentId = $_REQUEST['consent'];
if($consentId=="") { $consentId = 1; }

if($_REQUEST['sbtConsent']){
	$tmplateData = $_REQUEST['FCKeditor1'];
	$consentid = $_REQUEST['consentid'];	
	$arrayRecord['consent_name'] = $_REQUEST['consent_name'];
	$arrayRecord['consent_data'] = $tmplateData;
	if($consentid){
		$objManageData->updateRecords($arrayRecord, 'consent_forms_template', 'consent_id', $consentid);
	}else{
		$objManageData->addRecords($arrayRecord, 'consent_forms_template');
	}
}
//DELETE SELECTED TEMPLATE
	if($_POST['chkBox']){
		$delChkBoxes = $_POST['chkBox'];
		if(is_array($delChkBoxes)){
			foreach($delChkBoxes as $OpconsentId){
				$objManageData->delRecord('consent_forms_template', 'consent_id', $OpconsentId);
			}
		}
	}
//DELETE SELECTED TEMPLATE


$consentDetails = $objManageData->getRowRecord('consent_forms_template', 'consent_id', $consentId);
$data = $consentDetails->consent_data;
include_once("fckeditor/fckeditor.php") ;
?>
<html>
<head>
<title>Operative Report</title>
</head>
<body><br>
	<form name="frmConsentForm" action="consentForm.php?consent=<?php echo $consentId;?>" method="post">
	<input type="hidden" name="consentid" value="<?php echo $consentId; ?>">
	<input type="hidden" name="sbtConsent" value="">	
	<table align="left" cellpadding="0" cellspacing="0" border="0" width="970">
		<tr>
			<td width="250" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="250">
					<tr height="25">
						<td colspan="2" align="left" class="text_10b" style="font-size:14px;">
							<table border="0" cellpadding="0" cellspacing="0" width="250">
								<tr>
									<td align="right"><img src="../images/left_new.gif" width="3" height="24"></td>
									<td width="225" align="left" valign="middle" bgcolor="#c0aa1e" class="text_10b" style="padding-left:10px;">Consent Form Templates </td>
									<td align="left" valign="top"><img src="../images/right_new.gif" width="3" height="24"></td>
								</tr>
								<tr>
									<td></td>
									<td width="225" align="center" valign="middle">
										<table width="236" height="21" border="0" cellpadding="0" cellspacing="0">
											<?php
											$consentLists = $objManageData->getArrayRecords('consent_forms_template');
											if($consentLists){
												foreach($consentLists as $key => $list){
													++$seq;
													?>
													<tr height="20" bgcolor="<?php if(($seq%2)!=0) echo '#FFFFFF'; ?>">
														<td colspan="2" class="text_10" style="padding-left:10px;">
															<a href="consentForm.php?consent=<?php echo $list->consent_id; ?>" class="black"><?php echo $list->consent_name; ?></a>
														</td>
													</tr>
													<?php
												}
											}
											?>
									  </table>
									</td>
									<td></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td nowrap class="text_10b">Template Name:</td>
						<td width="8"></td>
						<td width="618" align="left" class="text_10b">
							<?php echo $consentDetails->consent_name; ?>
							<input type="hidden" class="text_10" name="consent_name" value="<?php echo $consentDetails->consent_name; ?>" size="25"> 
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<?php
								$oFCKeditor = new FCKeditor('FCKeditor1') ;
								$oFCKeditor->BasePath = 'fckeditor/' ;
								$oFCKeditor->Value = $data;
								$oFCKeditor->Height = 475;
								$oFCKeditor->Create() ;
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td></td>
			<td align="left" class="text_10">*Fields marked in parenthesis {} Will change.</td>
		</tr>
		<tr>
			<td></td>
			<td align="center"></td>
		</tr>
	</table>
	</form>
</body>
</html>