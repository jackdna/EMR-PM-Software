<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$countRows = count($menuListArr);
//$divWith = $width + 18;
$right_width=255;
$divWith = $width + 90;


//gurleen laser
	$RightOpLaserSlider='Operating Room';
		$primary_procedureSliderRightQry = "SELECT * FROM procedures WHERE name = '".addslashes($SliderRight_patientPrimProc)."' OR procedureAlias = '".addslashes($SliderRight_patientPrimProc)."'";
		$primary_procedureSliderRightRes = imw_query($primary_procedureSliderRightQry);
		$primary_procedureSliderRightNumRow = imw_num_rows($primary_procedureSliderRightRes);
		if($primary_procedureSliderRightNumRow>0){
			$primary_procedureSliderRightRow = imw_fetch_array($primary_procedureSliderRightRes);
			$patient_primary_procedure_categoryRightID = $primary_procedureSliderRightRow['catId'];
			if($patient_primary_procedure_categoryRightID==2){
				$RightOpLaserSlider='Laser Procedure'; 
			}	
		}
//gurleen lasers

if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
         }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
	}	 
$headingsize = "font-size:14px;";
$subHeadingsize = "font-size:13px;";
				
//DELETE IMAGE UPLOADED
$delImg = $_REQUEST['delImg'];
if($delImg){
	$objManageData->delRecord('scan_upload_tbl', 'scan_upload_id', $delImg);
}
unset($userPrivilegesArr);
unset($admin_privilegesArr);
$loginUser = $_SESSION['loginUserId'];
	$authenticationDetails = $objManageData->getRowRecord('users', 'usersId', $loginUser);
	$userType	=	$authenticationDetails->user_type;	
	$userPrivileges = $authenticationDetails->user_privileges;
	$userPrivilegesArr = explode(', ', $userPrivileges);
	$admin_privileges = $authenticationDetails->admin_privileges;
	if($admin_privileges){
		$admin_privilegesArr = explode(', ', $admin_privileges);
	}else{
		$admin_privilegesArr = array();
	}

?>

<script>
var pConfId = <?php echo $_REQUEST["pConfId"]; ?>;
var patient_id = <?php echo $_REQUEST["patient_id"]; ?>;
<?php 
if($_REQUEST["thisId"]){
	?>
	var thisId = <?php echo $_REQUEST["thisId"]; ?>;
	<?php
}else{
	?>
	var thisId = 0;
	<?php
}
if($_REQUEST["innerKey"]){
	?>
	var innerKey = '<?php echo $_REQUEST["innerKey"]; ?>';
	<?php
}
?>
var multiwind = '<?php echo $_REQUEST["multiwin"];?>';
var selectedConsentTemplateId='';
var selectedConsentFormAutoIncrId='';
var selectedConsentFormPurgeStatus='';
function right_link_click(pageName,thisId1,innerKey1,preColor1,patient_id1,pConfId1,ascId1,selectedConsentTemplateId,selectedConsentFormAutoIncrId,selectedConsentFormPurgeStatus) {
	//var pageName = pageName+'?patient_id='+patient_id1+'&pConfId='+pConfId1+'&ascId='+ascId1+'&rightClick=yes';
	top.document.forms[0].thisId.value = thisId1
	top.document.forms[0].innerKey.value = innerKey1;
	top.document.forms[0].preColor.value = preColor1;
	top.document.forms[0].patient_id.value = patient_id1
	top.document.forms[0].pConfId.value = pConfId1;
	top.document.forms[0].ascId.value = ascId1;
	top.document.forms[0].multiwin.value = multiwind;
	top.document.forms[0].frameHref.value = pageName;
	
	if(top.document.forms[0].consentMultipleId && selectedConsentTemplateId) {
		top.document.forms[0].consentMultipleId.value = selectedConsentTemplateId;
	}
	
	if(top.document.forms[0].consentMultipleAutoIncrId && selectedConsentFormAutoIncrId) {
		top.document.forms[0].consentMultipleAutoIncrId.value = selectedConsentFormAutoIncrId;
	}
	if(top.document.forms[0].hiddPurgestatus && selectedConsentFormPurgeStatus){
		
		top.document.forms[0].hiddPurgestatus.value = selectedConsentFormPurgeStatus;
	}
	
	top.document.forms[0].submit();
}
var dosScan = '';
function scanDocFn(pConfirmId, folder, dosScan){
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	
	var L	=	(SW - W ) / 2  ;
    var T	= 	(SH - H ) / 2 - 50 ; 
	window.open('admin/scanPopUp.php?pConfirmId='+pConfirmId+'&folder='+folder+'&dosScan='+dosScan,'scanWin', 'top=10, width='+W+', height='+ H+', scrollbars=1,location=yes,status=yes');
}
function openScanDocFn(pConfirmId, patient_id, dosScan){
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	
	var L	=	(SW - W ) / 2  ;
    var T	= 	(SH - H ) / 2 - 50 ; 
	
	window.open('admin/scanPopUp.php?pConfirmId='+pConfirmId+'&patient_id='+patient_id+'&dosScan='+dosScan,'scanWin', 'top=10, width='+W+', height='+ H+', scrollbars=1,location=yes,status=yes');	
}
function delImage(id,$this){
	
	var ask = confirm("Delete file – Are you sure?")
	
	if(ask == true){
		
		$.ajax({
			url			:	'common/delScanImg.php',
			type		:	'POST',
			dataType	:	'json',
			data 		:	{'recordID':id},	
			beforeSend	:	function()
			{
				parent.top.$(".loader").fadeIn('fast').show('fast'); 
			},
			success: function(data){
				
				if(data.success == true){
					$this.closest('li').remove();
				}
				else{
					alert('Error in Deletion...Please try again')	
				}
			},
			complete: function(res)
			{
				parent.top.$(".loader").fadeOut('slow').hide('slow');
			}
			
		});
		
		
	}
	
	
}



function openImage(id, type,filePath){
	if(filePath) {
		filePath = 'admin/'+filePath;
		window.open(filePath,id, 'menubar=1, top=5, left=10, width=1000, height=650, resizable=1, scrollbars=1');
	}else {
		window.open('imagePopup.php?imageId='+id+'&type='+type,'imagePopUp'+id, 'top=25, width=785, height=600, scrollbars=1, resizable=1,location=yes,status=yes');
	}
}
//Prinfn
function printFn(confId,path)
{
 window.open('printAllChartNotes.php?pConfId='+confId+'&get_http_path='+path);
}
//printFn
</script>

<!-- START MODEL FOR PAST OCULAR SURGERY -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" id="pt_ocular_sx_hx">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header text-center">
       			<button aria-label="Close" data-dismiss="modal" class="close" type="button" style="color:white;opacity:1; padding:2px 7px;" ><span aria-hidden="true">x</span></button>
          		<h4 class="modal-title rob"> Past Ocular Surgery  </h4>
			</div>
        
            <div class="modal-body">
              	<Div class="wrap_patient_for_h">
                    <div id="left" class="">
                        <?php
						$ptOcSxHxSiteArr = array("1"=>"Left Eye","2"=>"Right Eye","3"=>"Both Eye","4"=>"Left Upper Lid","5"=>"Left Lower Lid","6"=>"Right Upper Lid","7"=>"Right Lower Lid","8"=>"Bilateral Upper Lid","9"=>"Bilateral Lower Lid",);
						$ptOcSxHxQry = "SELECT pc.patientConfirmationId,pc.dos,pc.patient_primary_procedure,pc.site, DATE_FORMAT(pc.dos,'%m-%d-%Y') as dos_format, st.patient_status FROM patientconfirmation pc 
										INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId)
										WHERE patientId = '".$patient_id."' ORDER BY dos DESC";
						$ptOcSxHxRes = imw_query($ptOcSxHxQry) or die(imw_error());
						$ptOcSxHxNumRow = imw_num_rows($ptOcSxHxRes);
						if($ptOcSxHxNumRow > 0) {
							while($ptOcSxHxRow = imw_fetch_array($ptOcSxHxRes)) {
								$ptOcSxHxConfId = 	$ptOcSxHxRow['patientConfirmationId'];
								$ptOcSxHxConfIdArr[$ptOcSxHxConfId] = $ptOcSxHxRow;
							}
						?>
							<table  class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf table-hover" style="border-collapse:collapse;">
								<tr>
									<th class="text-left col-xs-2" style="border:2px solid #ddd;">DOS</th>
									<th class="text-left col-xs-5" style="border:2px solid #ddd;">Procedure</th>
									<th class="text-left col-xs-3" style="border:2px solid #ddd;">Site</th>
                                    <th class="text-left col-xs-2" style="border:2px solid #ddd;">Status</th>
								</tr>
						<?php                		
	
							$recExist = false;
							foreach($ptOcSxHxConfIdArr as $ptOcSxHxConfIdKey => $ptOcSxHxRows) {
								//START SHOW PAST DOS HISTORY  	
								if($ptOcSxHxRows['dos'] < $ptOcSxHxConfIdArr[$_REQUEST["pConfId"]]['dos']) {
									$recExist =true;
									$ptOcSxHxStatus = $ptOcSxHxRows['patient_status'];
									if($ptOcSxHxStatus == 'Canceled') {
										$ptOcSxHxStatus = 'Cancelled';	
									}
									//echo '<br>hlo '.$ptOcSxHxRows['dos_format'];	
								?>
									<tr>
										<td class="text-left col-xs-2" style="border:2px solid #ddd;"><?php echo $ptOcSxHxRows['dos_format']; ?></td>
										<td class="text-left col-xs-5" style="border:2px solid #ddd;"><?php echo $ptOcSxHxRows['patient_primary_procedure']; ?></td>
										<td class="text-left col-xs-3" style="border:2px solid #ddd;"><?php echo $ptOcSxHxSiteArr[$ptOcSxHxRows['site']]; ?></td>
                                        <td class="text-left col-xs-2" style="border:2px solid #ddd;"><?php echo $ptOcSxHxStatus; ?></td>
									</tr>
								<?php
								}
								//END SHOW PAST DOS HISTORY  	
							}
							if(!$recExist) {?>
									<tr>
										<td colspan="4" class="text-center" >No Past Ocular Surgery</td>
									</tr>
							<?php 		
							}?>
							</table><?php
						}?>
                    </div>
              	</Div>
            </div>
    	</div>
  	</div>
</div>
<!-- END MODEL FOR PAST OCULAR SURGERY -->

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" id="patient_form">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
       
       	<div class="modal-header text-center">
       		<button aria-label="Close" data-dismiss="modal" class="close" type="button" style="color:white;opacity:1; padding:2px 7px;" ><span aria-hidden="true">x</span></button>
          	<h4 class="modal-title rob"> Patient Forms  </h4>
		</div> <!-- Modal Header -->
        
        <div class="modal-body">
          <Div class="wrap_patient_for_h">
          
          
            <div id="left" class="">
            	
                <input type="hidden" value="false" name="slideOut">
                <input type="hidden" value="false" name="sliderRightOut">
                <input type="hidden" value="0" name="leftMainOpen">
                <input type="hidden" value="<?php echo $leftCounter; ?>" name="leftInnerOpen">
                <input type="hidden" value="0" name="rightMainOpen">
                <input type="hidden" value="1" name="rightInnerOpen">
                
                <ul id="menu-group-1" class="nav menu">
        			
                <?PHP
					function getFormStatus($tablename,$ascId,$pConfId) {					
						$formStatusQry = "select form_status from $tablename where confirmation_id = '".$pConfId."'";
						$formStatusRes = imw_query($formStatusQry) or die(imw_error());
						$formStatusRow = imw_fetch_array($formStatusRes);
						$formStatusName = $formStatusRow["form_status"];
						return $formStatusName;
					}
					
					function getAnotherFormStatus($tablename,$ascId,$pConfId) {
						$formStatusQry = "select form_status from $tablename where patient_confirmation_id = '".$pConfId."'";
						$formStatusRes = imw_query($formStatusQry) or die(imw_error());
						$formStatusRow = imw_fetch_array($formStatusRes);
						$formStatusName = $formStatusRow["form_status"];
						return $formStatusName;
					}
					//END FUNCTION TO GET STATUS OF EACH FORM  AS COMPLETED OR NOT COMPLETED

					//$patientDosQry = "select * from patientconfirmation where ascId = '$ascId' order by dos DESC";
					$patientDosQry = "select * from patientconfirmation where patientId = '$patient_id' order by dos DESC";
					$patientDosRes = imw_query($patientDosQry) or die(imw_error());
					$patientDosNumRow = imw_num_rows($patientDosRes);

					//$chkLatestDatepatientDosQry = "select * from patientconfirmation where ascId = '$ascId' order by dos DESC LIMIT 0,1";
					$chkLatestDatepatientDosQry = "select dos from patientconfirmation where patientId = '$patient_id' order by dos DESC LIMIT 0,1";
					$chkLatestDatepatientDosRes = imw_query($chkLatestDatepatientDosQry) or die(imw_error());
					$chkLatestDatepatientDosNumRow = imw_num_rows($chkLatestDatepatientDosRes);
					
					if($chkLatestDatepatientDosNumRow>0)
					{
						$chkLatestDatepatientDosRow = imw_fetch_array($chkLatestDatepatientDosRes);
						$chkLatestDatepatient_dos_temp = $chkLatestDatepatientDosRow["dos"];
					}
					
					
					if($patientDosNumRow>0)
					{
						$counter	=	0;
						while($patientDosRow = imw_fetch_array($patientDosRes)) 
						{ $counter++;
						
							$patient_confID = $patientDosRow["patientConfirmationId"];
							$patient_dos_temp = $patientDosRow["dos"];
							$SliderRight_patientPrimProc = $patientDosRow['patient_primary_procedure'];
							$SliderRight_patientPrimaryProcedureId = $patientDosRow['patient_primary_procedure_id'];
							$patient_dos_split = explode("-",$patient_dos_temp);
							$patient_dos = $patient_dos_split[1]."/".$patient_dos_split[2]."/".$patient_dos_split[0];	
							
							
							// laser
							$RightOpLaserSlider='Operating Room';
							$primary_procedureSliderRightQry = "SELECT * FROM procedures WHERE name = '".addslashes($SliderRight_patientPrimProc)."' OR procedureAlias = '".addslashes($SliderRight_patientPrimProc)."'";
							$primary_procedureSliderRightRes = imw_query($primary_procedureSliderRightQry);
							$primary_procedureSliderRightNumRow = imw_num_rows($primary_procedureSliderRightRes);
							$patient_primary_procedure_categoryRightID='';
							if($primary_procedureSliderRightNumRow > 0)
							{
								$primary_procedureSliderRightRow = imw_fetch_array($primary_procedureSliderRightRes);
								$patient_primary_procedure_categoryRightID = $primary_procedureSliderRightRow['catId'];
								if($patient_primary_procedure_categoryRightID==2)
								{
									$RightOpLaserSlider='Laser Procedure'; 
								}	
							
							} //lasers
							
							//CODE TO DISPLAY LATEST VISIT DOS IN RIGHT SLIDER
							if($patient_dos_temp == $chkLatestDatepatient_dos_temp) {
								//$display_visit = "open";  
								$display_visit = "";
							}else { 
								$display_visit = "";
							}
							//END CODE TO DISPLAY LATEST VISIT DOS IN RIGHT SLIDER
							
							//CODE TO GET FORM STATUS
							$surgicalCheckListFormStatus = getFormStatus("surgical_check_list",$ascId,$patient_confID);
							$surgeryConsentFormStatus = getFormStatus("surgery_consent_form",$ascId,$patient_confID);
							$HippaConsentFormStatus = getFormStatus("hippa_consent_form",$ascId,$patient_confID);
							$BenefitConsentFormStatus = getFormStatus("benefit_consent_form",$ascId,$patient_confID);
							$InsuranceConsentFormStatus = getFormStatus("insurance_consent_form",$ascId,$patient_confID);
							$dischargeSummaryFormStatus = getFormStatus("dischargesummarysheet",$ascId,$patient_confID);
							$preopHealthQuestFormStatus = getFormStatus("preophealthquestionnaire",$ascId,$patient_confID);
							$historyPhysicalFormStatus = getFormStatus("history_physicial_clearance",$ascId,$patient_confID);		
							$preopNursingFormStatus = getFormStatus("preopnursingrecord",$ascId,$patient_confID);
							$preNurseAlderateFormStatus = getFormStatus("pre_nurse_alderate",$ascId,$patient_confID);
							$postopNursingFormStatus = getFormStatus("postopnursingrecord",$ascId,$patient_confID);	
							$postNurseAlderateFormStatus = getFormStatus("post_nurse_alderate",$ascId,$patient_confID);	
							$macRegionalAnesthesiaFormStatus = getFormStatus("localanesthesiarecord",$ascId,$patient_confID);
							$preopGenralAnesthesiaFormStatus = getFormStatus("preopgenanesthesiarecord",$ascId,$patient_confID);
							$GenralAnesthesiaFormStatus = getFormStatus("genanesthesiarecord",$ascId,$patient_confID);
							$genralAnesthesiaNursesNotesFormStatus = getFormStatus("genanesthesianursesnotes",$ascId,$patient_confID);		
							$OpRoomRecordFormStatus = getFormStatus("operatingroomrecords",$ascId,$patient_confID);
							$laserProcedureFormStatus = getFormStatus("laser_procedure_patient_table",$ascId,$patient_confID);
							$injectionMiscFormStatus = getFormStatus("injection",$ascId,$patient_confID);
							$surgical_operative_record_form = getFormStatus("operativereport",$ascId,$patient_confID);
							$AmendmentsNotesFormStatus = getFormStatus("amendment",$ascId,$patient_confID);		
							$preopPhysicianFormStatus = getAnotherFormStatus("preopphysicianorders",$ascId,$patient_confID);
							$postopPhysicianFormStatus = getAnotherFormStatus("postopphysicianorders",$ascId,$patient_confID);
							$InstructionSheetFormStatus = getAnotherFormStatus("patient_instruction_sheet",$ascId,$patient_confID);
							$TransferFollowupsFormStatus = getFormStatus("transfer_followups",$ascId,$patient_confID);
							//END CODE TO GET FORM STATUS
							
							$patientIdDos = $patientDosRow["patientId"];
							$rightNavQry = "select * from left_navigation_forms where confirmationId = '$patient_confID'";
							$rightNavRes = imw_query($rightNavQry) or die(imw_error());
							$rightNavRow = imw_fetch_array($rightNavRes);
							
							$right_surgical_check_list_form  = $rightNavRow["surgical_check_list_form"];
							$right_surgery_form  = $rightNavRow["surgery_form"];
							$right_hippa_form  = $rightNavRow["hippa_form"];
							$right_assign_benifits_form  = $rightNavRow["assign_benifits_form"];
							$right_insurance_card_form  = $rightNavRow["insurance_card_form"];
							$right_pre_op_health_ques_form  = $rightNavRow["pre_op_health_ques_form"];
							$right_history_physical_form  = $rightNavRow["history_physical_form"];
							$right_pre_op_nursing_form  = $rightNavRow["pre_op_nursing_form"];
							$right_pre_nurse_alderate_form  = $rightNavRow["pre_nurse_alderate_form"];
							$right_post_op_nursing_form  = $rightNavRow["post_op_nursing_form"];
							$right_post_nurse_alderate_form  = $rightNavRow["post_nurse_alderate_form"];
							$right_pre_op_physician_order_form  = $rightNavRow["pre_op_physician_order_form"];
							$right_post_op_physician_order_form  = $rightNavRow["post_op_physician_order_form"];
							$right_mac_regional_anesthesia_form  = $rightNavRow["mac_regional_anesthesia_form"];
							$right_pre_op_genral_anesthesia_form  = $rightNavRow["pre_op_genral_anesthesia_form"];
							$right_genral_anesthesia_form  = $rightNavRow["genral_anesthesia_form"];
							$right_genral_anesthesia_nurses_notes_form  = $rightNavRow["genral_anesthesia_nurses_notes_form"];
							$right_intra_op_record_form  = $rightNavRow["intra_op_record_form"];
							$right_laser_procedure_form  = $rightNavRow["laser_procedure_form"];
							$right_surgical_operative_record_form  = $rightNavRow["surgical_operative_record_form"];
							$right_qa_check_list_form  = $rightNavRow["qa_check_list_form"];
							$right_discharge_summary_form  = $rightNavRow["discharge_summary_form"];
							$right_post_op_instruction_sheet_form  = $rightNavRow["post_op_instruction_sheet_form"];
							$right_transfer_and_followups_form  = $rightNavRow["transfer_and_followups_form"];
							$right_physician_amendments_form  = $rightNavRow["physician_amendments_form"];
							$right_injection_misc_form  = $rightNavRow["injection_misc_form"];
							//END CODE TO DISPLAY LATEST VISIT DOS IN RIGHT SLIDER
							
							//$finalizeStatusQry = "select * from patientconfirmation where ascId = '$ascId' and patientConfirmationId = '".$pConfId."'";
							$stubD	=	$objManageData->getRowRecord('stub_tbl','patient_confirmation_id',$patient_confID,'stub_id','ASC','stub_id');
							
							$unfinalizedClick	=	"";
							$unfinalizedDisplay	=	'displayNone';
							
							$historyClick		=	"";
							$historyDisplay		=	'displayNone';

							$finalizeStatusQry = "select finalize_status, surgeonId from patientconfirmation where patientId = '$patient_id' and patientConfirmationId = '".$patient_confID."'";
							$finalizeStatusRes = imw_query($finalizeStatusQry) or die(imw_error());
							$finalizeStatusRow = imw_fetch_array($finalizeStatusRes);
							$finalizeStatusName = $finalizeStatusRow["finalize_status"];
							
							$confirmationSurgeonID	=	$finalizeStatusRow["surgeonId"];
							if($_SESSION['loginUserId'] == $confirmationSurgeonID) {
								$hasAdminPrv = 1; //OVERWRITE FROM mainpage.php
							}
							if($finalizeStatusName== "true"){
								$finalized="Finalized";

								if($hasAdminPrv == 1 )
								{
									$unfinalizedClick	=	"javascript:top.unfinalize('".$patient_confID."','".$patientIdDos."', '".$ascId."', '".$stubD->stub_id."');";
									$unfinalizedDisplay	=	'displayInlineBlock';
						
								}
								else
								{
									$unfinalizedClick	=	'';
									$unfinalizedDisplay	=	'displayNone';	
								}

							}
							else
							{
								$finalized			=	'';
								$unfinalizedClick	=	'';
								$unfinalizedDisplay	=	'displayNone';
							}
							$FHistory	=	$objManageData->getArrayRecords('finalize_history', 'patient_confirmation_id', $patient_confID,  'finalize_history_id', 'DESC');
				
							
							if( count($FHistory) > 0 && $hasAdminPrv == 1)
							{
								
								$historyClick	=	"javascript:top.unfinalizeHistory('".$patient_confID."','".$patientIdDos."','".$ascId."','".$stubD->stub_id."','".$hasAdminPrv."');";
								$historyDisplay	=	'displayInlineBlock';
							}
							
							$surgeryCenterSettings	=	$objManageData->loadSettings('peer_review');
							$surgeryCenterPeerReview=	$surgeryCenterSettings['peer_review'];
							$practiceNameMatchSlider	=	'';
							if($surgeryCenterPeerReview == 'Y' && $userType == 'Surgeon')
							{
								$practiceNameMatchSlider	=	$objManageData->getPracMatchUserId($loginUser,$confirmationSurgeonID);
							}
							
				?>
                
                			<li class="item-1 deeper parent active treeview">
								
                                <a class="" href="#">
                                	<span data-toggle="collapse" class="sign">
                                    	<i class="fa-plus fa"></i>
                                   	</span>
                                	<span class="lbl">
										<?php echo $patient_dos; ?>&nbsp;
										<b class="finalized" id="finalized<?=$patient_confID?>"><?php echo $finalized;?></b>
                                        <b onclick="printFn('<?php echo $patient_confID; ?>','<?php echo $get_http_path; ?>');"
                                        	class="glyphicon glyphicon-print" style="margin-right:-5px; margin-top:2px; float:right; padding:1px 5px;" >
                                       	</b>
										<b title="Chart Finalize History" class="fa fa-history unfinalized <?=$historyDisplay?> " onclick="<?=$historyClick?>"></b>
                                        
										<b title="Unfinalize Chart" id="unfinalized<?=$patient_confID?>" class="fa fa-times-circle-o unfinalized <?=$unfinalizedDisplay?>" onclick="<?=$unfinalizedClick?>" ></b>
                                    
                                    </span>
                                    
                               	</a>
                                
                                <ul class="children nav-child small collapse" id="sub-item-<?=$counter?>" rel="<?php echo $display_visit;?>">
                                	<li class="item-1 deeper parent active" >
                                        
                                        <a class="" href="#">
                                            <span 	data-toggle="collapse" 
                                            		class="sign"
                                            >
                                            	<i class="fa-plus fa"></i>
                                           	</span>
                                            <span class="lbl">
                                            	Scan Documents
                                                <img onClick="openScanDocFn('<?php echo $patient_confID; ?>', '<?php echo $patient_id; ?>','<?php echo $patient_dos_temp; ?>');" src="images/scanicon.png"  title ="Scan/Upload Documents" />
                                          	</span>
                                        </a>
                                    	
										<ul class="children nav-child unstyled small collapse" id="sub-sub-item-<?=$counter?>" >												
                                        <?php
											
											$scanFolderDetailLists = $objManageData->getArrayRecords('scan_documents', 'confirmation_id', $patient_confID, 'document_id');
											if(count($scanFolderDetailLists)>0)
											{
												foreach($scanFolderDetailLists as $FolderList)
												{
													$document_id = $FolderList->document_id;
													$document_name = $FolderList->document_name;
													if($document_name=='Anesthesia Consent') {$document_name='A.Consent';  }
													
													//SHOW FOLDER OR NOT 
													unset($conditionArr);
													$conditionArr['confirmation_id'] = $patient_confID;
													$conditionArr['document_id'] = $document_id;														
													$getImagesOrNotDetails = $objManageData->getMultiChkArrayRecords('scan_upload_tbl', $conditionArr);
														
													
													// Folders List 	
										?>
                                        			<li class="item-1 deeper parent active">
                                                        <a class="" href="#">
                                                        <span
                                                        	data-toggle="collapse" 
                                            				class="sign"
                                                           ><i class="fa-plus fa"></i>
                                                       	</span>
                                                        <span class="lbl"> <?php echo $document_name; ?></span> 
                                                            
                                                        </a>
                                                        
                                                        <ul class="children nav-child unstyled small collapse" >
                                                        	
                                                            <?php
																		if(count($getImagesOrNotDetails)>0)
																		{	$start4	=	0;
																			foreach($getImagesOrNotDetails as $imageDetails)
																			{	$start4++;
																				$imageName = $imageDetails->document_name;
																				$image_type = $imageDetails->image_type;
																				$pdfFilePathDB = $imageDetails->pdfFilePath;
																				$parent_sub_doc_id = $imageDetails->parent_sub_doc_id;
																				
																				if($parent_sub_doc_id != 0) continue;
																				
																				if($image_type=='application/pdf') $image_type = 'pdf';
																				
																				$scan_upload_id = $imageDetails->scan_upload_id;
																				
																				$lenOfName = strlen($imageName);
																				
																				if($lenOfName>15)
																				{
																					$imageName = substr($imageName, 0, 16).'..';
																				}
																				
																				
																				$delImageOnclick	=	"return delImage('".$scan_upload_id."',this);";
																				if($practiceNameMatchSlider  == 'yes')
																				{
																					$delImageOnclick	=	"accessAlert(); return false;";	
																				}
															?>
                                                            					 <li class="item-1">
                                                                                    <a class="" href="#">
                                                                                       <span >
                                                                     	                   <span class="sign" onclick="return openImage('<?php echo $scan_upload_id; ?>', '<?php echo $image_type; ?>','<?php echo $pdfFilePathDB; ?>');"><i class="fa-play fa"></i></span>
                                                                        	               <span class="lbl">
																						   		<b onclick="return openImage('<?php echo $scan_upload_id; ?>', '<?php echo $image_type; ?>','<?php echo $pdfFilePathDB; ?>');"><?php echo urldecode($imageName); ?></b>
                                                                                                <?php if(strtolower($finalized)!='finalized') {?>
                                                                                                		<b class="fa fa-close" onclick="<?=$delImageOnclick?>"></b>
                                                                                                <?php }?>    
																								
                                                                                           </span>
                                                                                           
                                                                            			</span>
                                                                                  	</a>
                                                                                    
                                                                                    
                                                                                </li>
																				
															<?php
																				
																				
																			}
																		}
																		else
																		{
																			// No Record Found So Nothing to do Here.
																		}
															
															
															?>
                                                        
                                                        </ul>
                                                        
                                                    </li>
                                        
                                        
																
															<?php
														//}
													}
												}
												//Consent, Ophthalmologist, Internist													
												?>
											</ul>
										</li>
										
										<?php	
										if($_REQUEST["pConfId"]<>$patient_confID){ //DO NOT DISPLAY TODAY'S VISIT
											//GET ARRAY NUMBER FOR MULTIPLE CONSENT FORMS
											$consentFormTemplateSelectQry = "select consent_id,consent_alias,consent_delete_status from `consent_forms_template` order by consent_id";
											$consentFormTemplateSelectRes = imw_query($consentFormTemplateSelectQry) or die(imw_error()); 
											$consentFormTemplateSelectNumRow = imw_num_rows($consentFormTemplateSelectRes);
											$consentFormAliasArr = array();
											if($consentFormTemplateSelectNumRow>0) {
												while($consentFormTemplateSelectRow = imw_fetch_array($consentFormTemplateSelectRes)) {
													
													$consentFormTemplateSelectConsentAlias = $consentFormTemplateSelectRow['consent_alias'];	
													$consentFormTemplateDeleteStatus = $consentFormTemplateSelectRow['consent_delete_status'];
													
													//DO NOT DISPLAY CONSENT FORM FOR NEW SURGERY IN LEFT SLIDER
													if($consentFormTemplateDeleteStatus=='true') {
														$consentFormTemplateSelectConsentAlias='';
													}
													//DO NOT DISPLAY CONSENT FORM FOR NEW SURGERY IN LEFT SLIDER
													
													$consentFormSelectQry = "select surgery_consent_alias from `consent_multiple_form` where  confirmation_id = '".$patient_confID."' AND consent_template_id='".$consentFormTemplateSelectRow['consent_id']."'";
													$consentFormSelectRes = imw_query($consentFormSelectQry) or die(imw_error()); 
													$consentFormSelectNumRow = imw_num_rows($consentFormSelectRes);
													$consentFormSelectRow = imw_fetch_array($consentFormSelectRes);
													$consentFormSelectConsentAlias = $consentFormSelectRow['surgery_consent_alias'];	
													if(!$consentFormSelectConsentAlias) {
														if($consentFormTemplateSelectConsentAlias!='') {
															$consentFormSelectConsentAlias=$consentFormTemplateSelectConsentAlias;
														}
													}
													if($consentFormSelectConsentAlias!='') {
														$consentFormAliasArr[] = $consentFormSelectConsentAlias;
														$consentFormTemplateSelectConsentId[] = $consentFormTemplateSelectRow['consent_id'];
													}	
												}
												
											}
											$seqArrTemp=1;
											if($consentFormAliasArr) {
												foreach($consentFormAliasArr as $consentFormAliasRightName) {
													$seqArrTemp++;
												}
											}	
											$seqArrTemp5 = $seqArrTemp;
											$seqArrTemp6 = $seqArrTemp+1;
											$seqArrTemp7 = $seqArrTemp+2;
											$seqArrTemp8 = $seqArrTemp+3;
											$seqArrTemp9 = $seqArrTemp+4;
											$seqArrTemp10 = $seqArrTemp+5;
											$seqArrTemp11 = $seqArrTemp+6;
											$seqArrTemp12 = $seqArrTemp+7;
											$seqArrTemp13 = $seqArrTemp+8;
											$seqArrTemp14 = $seqArrTemp+9;
											$seqArrTemp15 = $seqArrTemp+10;
											$seqArrTemp16 = $seqArrTemp+11;
											$seqArrTemp17 = $seqArrTemp+12;
											$seqArrTemp18 = $seqArrTemp+13;
											$seqArrTemp19 = $seqArrTemp+14;
											$seqArrTemp20 = $seqArrTemp+15;
											$seqArrTemp21 = $seqArrTemp+16;
											
											//GET ARRAY NUMBER FOR MULTIPLE CONSENT FORMS
											
											
											
											if($right_physician_amendments_form=='false'){
												
												if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))) {
													
													$href	=	"javascript:right_link_click('amendments_notes.php',10,'".$seqArrTemp20."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";	
												}
												else
												{
													$href	=	"javascript:void(0);";	
												}
													
													
												if($AmendmentsNotesFormStatus=='completed')
												{
													$flag	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($AmendmentsNotesFormStatus=='not completed')
												{
													$flag	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}					
												
												
						?>
												<li class = "item-1 deeper parent">
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Physician Notes</span>
                                        			</a>
                                        
                                        
                                                    
                                                	<ul class="children nav-child small collapse" >
														
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$href?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Amendments
                                                                        <?=$flag?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
													</ul>
												</li>
												<?php
											}
											
											if($right_surgical_check_list_form == 'false')
											{
												$showCheckList = ( !$showCheckListAdmin && ($surgicalCheckListFormStatus == 'completed' || $surgicalCheckListFormStatus == 'not completed')) ? true : $showCheckListAdmin;
												$showCheckListStatus = 	$objManageData->getChartShowStatus($patient_confID,'checklist');
												$showCheckList = $showCheckListStatus ? ($showCheckListStatus == 1 ? true : ($showCheckListStatus == 2 ? false : $showCheckList)) : $showCheckList;
												
												if( $showCheckList )  
												{	
													if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)))
													{
														
														$href	=	"javascript:right_link_click('check_list.php','','','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";	
														
													}
													else
													{
														$href	=	"javascript:void(0);";	
													}
													
													
													if($surgicalCheckListFormStatus=='completed')
													{
														$flag	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
													}	
												
													if($surgicalCheckListFormStatus=='not completed')
													{
														$flag	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
													}	
				?>
												<li class = "item-1 deeper parent">
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Check List</span>
                                        			</a>
                                                	
													<ul class="children nav-child small collapse">
														<li class="item-1 " >
                                                            <a class="" href="<?=$href?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Check List
                                                                        <?=$flag?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                    </ul>
												</li>
												<?php
												}
											}
											if($right_pre_op_health_ques_form == 'false' || $right_history_physical_form == 'false')
											{
												if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)))
												{
													
													$hrefPRE	=	"javascript:right_link_click('pre_op_health_quest.php',1,'".$seqArrTemp5."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefPRE	=	"javascript:void(0);";	
												}
												
												
												if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)))
												{
													
													$hrefHST	=	"javascript:right_link_click('history_physicial_clearance.php',1,'".$seqArrTemp6."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefHST	=	"javascript:void(0);";	
												}
												
												
													
													
												if($preopHealthQuestFormStatus=='completed')
												{
													$flagPRE	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($preopHealthQuestFormStatus=='not completed')
												{
													$flagPRE	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}
												
												
												if($historyPhysicalFormStatus=='completed')
												{
													$flagHST	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($historyPhysicalFormStatus=='not completed')
												{
													$flagHST	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}	
												
												
			?>
												<li class = "item-1 deeper parent">
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Pre Op Health Ques</span>
                                        			</a>
                                                	<ul class="children nav-child small collapse">
														<?php if($right_pre_op_health_ques_form=='false'){?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefPRE?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Health Questionnaire
                                                                        <?=$flagPRE?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>  
                                                        <?php if($right_history_physical_form=='false'){?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefHST?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	H & P Clearance
                                                                        <?=$flagHST?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>  
                                                  	
                                                    
                                                    </ul>
                                                    
                                                    
													
												</li>
												<?php
											}
											
											if( $right_pre_op_nursing_form == 'false' || $right_post_op_nursing_form=='false' || 
												$right_pre_nurse_alderate_form == 'false'  || $right_post_nurse_alderate_form == 'false' )
											{
												if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													
													$hrefPRE	=	"javascript:right_link_click('pre_op_nursing_record.php',2,'".$seqArrTemp7."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefPRE	=	"javascript:void(0);";	
												}
												
												
												if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													
													$hrefPST	=	"javascript:right_link_click('post_op_nursing_record.php',2,'".$seqArrTemp8."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefPST	=	"javascript:void(0);";	
												}
												
												if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													
													$hrefPRA	=	"javascript:right_link_click('pre_nurse_alderate_record.php',2,'".$seqArrTemp8."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefPRA	=	"javascript:void(0);";	
												}
												
												if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													
													$hrefPSA	=	"javascript:right_link_click('post_nurse_alderate_record.php',2,'".$seqArrTemp8."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefPSA	=	"javascript:void(0);";	
												}
												
												
												
													
													
												if($preopNursingFormStatus=='completed')
												{
													$flagPRE	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($preopNursingFormStatus=='not completed')
												{
													$flagPRE	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}
												
												if($preNurseAlderateFormStatus=='completed')
												{ 
													$flagPRA	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($preNurseAlderateFormStatus=='not completed')
												{
													$flagPRA	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}
												
												
												
												if($postopNursingFormStatus=='completed')
												{
													$flagPST	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($postopNursingFormStatus=='not completed')
												{
													$flagPST	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}	
												
												if($postNurseAlderateFormStatus=='completed')
												{ 
													$flagPSA	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($postNurseAlderateFormStatus=='not completed')
												{
													$flagPSA	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}
												
			?>
												<li class="item-1 deeper parent ">
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Nursing Record</span>
                                        			</a>
                                                	
                                                    <ul class="children nav-child small collapse">
														<?php if($right_pre_op_nursing_form == 'false' ){?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefPRE?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Pre-Op
                                                                        <?=$flagPRE?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>  
                                                        
														<?php if($right_pre_nurse_alderate_form=='false'){?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefPRA?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Pre-Op Aldrete
                                                                        <?=$flagPRA?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>
                                                        
                                                        <?php if($right_post_op_nursing_form=='false'){?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefPST?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Post-Op
                                                                        <?=$flagPST?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>
                                                        
                                                        <?php if($right_post_nurse_alderate_form=='false'){?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefPSA?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Post-Op Aldrete
                                                                        <?=$flagPSA?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>  
                                                  	
                                                    
                                                    </ul>
                                              	</li>
											<?php 
											}  
											
											
											if(($right_pre_op_physician_order_form=='false') || ($right_post_op_physician_order_form=='false'))
											{
												if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													
													$hrefPRE	=	"javascript:right_link_click('pre_op_physician_orders.php',3,'".$seqArrTemp9."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefPRE	=	"javascript:void(0);";	
												}
												
												
												if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													
													$hrefPST	=	"javascript:right_link_click('post_op_physician_orders.php',3,'".$seqArrTemp10."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefPST	=	"javascript:void(0);";	
												}
												
												
													
													
												if($preopPhysicianFormStatus=='completed')
												{
													$flagPRE	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($preopPhysicianFormStatus=='not completed')
												{
													$flagPRE	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}
												
												
												if($postopPhysicianFormStatus=='completed')
												{
													$flagPST	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($postopPhysicianFormStatus=='not completed')
												{
													$flagPST	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}	
												
												
			?>
												<li class = "item-1 deeper parent">
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Physician Orders</span>
                                        			</a>
                                                    
                                                    <ul class="children nav-child small collapse">
														<?php if($right_pre_op_physician_order_form == 'false' ){?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefPRE?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Pre-Op Order
                                                                        <?=$flagPRE?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>  
                                                        
														<?php if($right_post_op_physician_order_form == 'false'){?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefPST?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Post-Op Order
                                                                        <?=$flagPST?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>  
                                                  	
                                                    
                                                    </ul>
                                                    
                                                </li>
											<?php
											}
											
											
											
											if($right_mac_regional_anesthesia_form=='false' || $right_pre_op_genral_anesthesia_form=='false' || $right_genral_anesthesia_form=='false' || $right_genral_anesthesia_nurses_notes_form=='false')
											{
					?>
                    							<li class = "item-1 deeper parent" >
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Anesthesia</span>
                                        			</a>
                                                    
                                                	
													<ul class="children nav-child small collapse">
                                                    
														<?php
                                                        	if($right_mac_regional_anesthesia_form == 'false')
															{
																if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
																{
																	
																	$href	=	"javascript:right_link_click('local_anes_record.php',4,'".$seqArrTemp11."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
																}
																else
																{
																	$href	=	"javascript:void(0);";	
																}
																
																
																if($macRegionalAnesthesiaFormStatus=='completed')
																{
																	$flag	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
																}	
											
																if($macRegionalAnesthesiaFormStatus=='not completed')
																{
																	$flag	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
																}	
																
														?>
															
                                                            
                                                            <li class = "item-1" >
                                                            	<a class="" href="<?=$href?>">
                                                                    <span >
                                                                        <span class="sign"><i class="fa-play fa"></i></span>
                                                                        <span class="lbl">
                                                                            MAC/Regional
                                                                            <?=$flag?>
                                                                        </span>
                                                                        
                                                                    </span>
                                                                    
                                                                </a>
																 
																		
																		
																
															</li>
														<?php } ?>
                                                        
														<?php
                                                        	if($right_pre_op_genral_anesthesia_form=='false')
															{
																if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
																{
																	
																	$href	=	"javascript:right_link_click('pre_op_general_anes.php',4,'".$seqArrTemp12."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
																}
																else
																{
																	$href	=	"javascript:void(0);";	
																}
																
																
																if($preopGenralAnesthesiaFormStatus=='completed')
																{
																	$flag	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
																}	
											
																if($preopGenralAnesthesiaFormStatus=='not completed')
																{
																	$flag	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
																}
														?>
																
                                                                <li class = "item-1" >
                                                                    <a class="" href="<?=$href?>">
                                                                        <span >
                                                                            <span class="sign"><i class="fa-play fa"></i></span>
                                                                            <span class="lbl">
                                                                                Pre-Op General
                                                                                <?=$flag?>
                                                                            </span>
                                                                            
                                                                        </span>
                                                                        
                                                                    </a>
                                                                 </li>
                                                               
														<?php } ?>
														<?php
                                                        	if($right_genral_anesthesia_form=='false')
															{
																if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
																{
																	
																	$href	=	"javascript:right_link_click('gen_anes_rec.php',4,'".$seqArrTemp13."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
																}
																else
																{
																	$href	=	"javascript:void(0);";	
																}
																
																
																if($GenralAnesthesiaFormStatus=='completed')
																{
																	$flag	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
																}	
											
																if($GenralAnesthesiaFormStatus=='not completed')
																{
																	$flag	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
																}
														?>
																<li class = "item-1" >
                                                                    <a class="" href="<?=$href?>">
                                                                        <span >
                                                                            <span class="sign"><i class="fa-play fa"></i></span>
                                                                            <span class="lbl">
                                                                                General
                                                                                <?=$flag?>
                                                                            </span>
                                                                            
                                                                        </span>
                                                                        
                                                                    </a>
                                                                 </li>
                                                                 
                                                                
														<?php } ?>
                                                        
														<?php
                                                        	if($right_genral_anesthesia_nurses_notes_form=='false')
															{
																if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
																{
																	
																	$href	=	"javascript:right_link_click('gen_anes_nurse_notes.php',4,'".$seqArrTemp14."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
																}
																else
																{
																	$href	=	"javascript:void(0);";	
																}
																
																
																if($genralAnesthesiaNursesNotesFormStatus=='completed')
																{
																	$flag	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
																}	
											
																if($genralAnesthesiaNursesNotesFormStatus=='not completed')
																{
																	$flag	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
																}
																
														?>
                                                        		<li class = "item-1" >
                                                                    <a class="" href="<?=$href?>">
                                                                        <span >
                                                                            <span class="sign"><i class="fa-play fa"></i></span>
                                                                            <span class="lbl">
                                                                                General Nurse Notes
                                                                                <?=$flag?>
                                                                            </span>
                                                                            
                                                                        </span>
                                                                        
                                                                    </a>
                                                                 </li>
                                                       	<?php } ?>
													</ul>
												</li>
												<?php 
											}
											
											
											
											
											if($right_intra_op_record_form=='false' || $right_laser_procedure_form=='false' || $right_injection_misc_form == 'false')
											{
												if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													
													$hrefOP	=	"javascript:right_link_click('op_room_record.php',5,'".$seqArrTemp15."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefOP	=	"javascript:void(0);";	
												}
												
												if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													
													$hrefINJ	=	"javascript:right_link_click('injection_misc.php',5,'".$seqArrTemp22."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefINJ	=	"javascript:void(0);";	
												}
												
												
												if(($patient_primary_procedure_categoryRightID =='2' || !$SliderRight_patientPrimaryProcedureId) && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													
													$hrefLP	=	"javascript:right_link_click('laser_procedure.php',5,'".$seqArrTemp16."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";
												}
												else
												{
													$hrefLP	=	"javascript:void(0);";	
												}
												
												
													
													
												if($OpRoomRecordFormStatus=='completed')
												{
													$flagOP	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($OpRoomRecordFormStatus=='not completed')
												{
													$flagOP	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}
												
												
												if($injectionMiscFormStatus =='completed')
												{
													$flagINJ	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($injectionMiscFormStatus=='not completed')
												{
													$flagINJ	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}
												
												
												if($laserProcedureFormStatus=='completed')
												{
													$flagLP	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
												}	
											
												if($laserProcedureFormStatus=='not completed')
												{
													$flagLP	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
												}	
												
												
				?>
												<li class = "item-1 deeper parent">
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl"><?php echo $RightOpLaserSlider; ?></span>
                                        			</a>
                                                    
                                                    <ul class="children nav-child small collapse">
														<?php if($right_intra_op_record_form == 'false' ){?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefOP?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Intra-Op Record
                                                                        <?=$flagOP?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>
                        		<?php if($right_injection_misc_form == 'false' ){?>
                            	<li class="item-1 " >
                              	<a class="" href="<?=$hrefINJ?>">
                                	<span >
                                  	<span class="sign"><i class="fa-play fa"></i></span>
                                    <span class="lbl">Injection/Miscellaneous <?=$flagINJ?></span>
                                  </span>
                               	</a>
                             	</li>
                           	<?php } ?>
                                                        
														<?php if($right_laser_procedure_form == 'false') { ?>
                                                        <li class="item-1 " >
                                                            <a class="" href="<?=$hrefLP?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Laser Procedure
                                                                        <?=$flagLP?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>
                                                        </li>
                                                        <?php } ?>  
                                                  	
                                                    
                                                    </ul>
                                               	</li>
											<?php
											}

											if($right_surgical_operative_record_form == 'false')
											{
												if(($patient_primary_procedure_categoryRightID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))))
												{
													$href	=	"javascript:right_link_click('operative_record.php',6,'".$seqArrTemp17."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";	
												}
												else
												{
													$href	=	"javascript:void(0);";	
												}
												
												if($surgical_operative_record_form == 'completed')
												{ 
													$flag	=	'<img src="images/green_flag.png" width="12" height="14" border="0">';
												}
												
												if($surgical_operative_record_form == 'not completed') 
												{
													$flag	=	'<img src="images/red_flag.png" width="12" height="14" border="0">';
												}
												
				?>
												<li class = "item-1 deeper parent" >
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Surgical</span>
                                        			</a>
                                                		
													<ul class="children nav-child small collapse">
														<li class = "item-1" >
															<a class="" href="<?=$href?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Operative Report
                                                                        <?=$flag?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>	
														</li>
													</ul>
												</li>
												<?php
											} 
											
											
											if($right_discharge_summary_form=='false')
											{
												if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)))
												{
													$href	=	"javascript:right_link_click('discharge_summary_sheet.php',7,'".$seqArrTemp18."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";	
												}
												else
												{
													$href	=	"javascript:void(0);";	
												}
												
												if($dischargeSummaryFormStatus == 'completed')
												{ 
													$flag	=	'<img src="images/green_flag.png" width="12" height="14" border="0">';
												}
												
												if($dischargeSummaryFormStatus == 'not completed') 
												{
													$flag	=	'<img src="images/red_flag.png" width="12" height="14" border="0">';
												}
												
												
				?>
                								<li class = "item-1 deeper parent" >
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Discharge Summary</span>
                                        			</a>
                                                		
													<ul class="children nav-child small collapse">
														<li class = "item-1" >
															<a class="" href="<?=$href?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Discharge Summary Sheet
                                                                        <?=$flag?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>	
														</li>
													</ul>
												</li>
                                                
					<?php
											} 
											
											
											if($right_post_op_instruction_sheet_form == 'false')
											{
												if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)) || in_array("Billing", $userPrivilegesArr))
												{
													$href	=	"javascript:right_link_click('instructionsheet.php',8,'".$seqArrTemp19."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";	
												}
												else
												{
													$href	=	"javascript:void(0);";	
												}
												
												if($InstructionSheetFormStatus == 'completed')
												{ 
													$flag	=	'<img src="images/green_flag.png" width="12" height="14" border="0">';
												}
												
												if($InstructionSheetFormStatus == 'not completed') 
												{
													$flag	=	'<img src="images/red_flag.png" width="12" height="14" border="0">';
												}
												
												
				?>
                								<li class = "item-1 deeper parent" >
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Post Op Inst. Sheet</span>
                                        			</a>
                                                		
													<ul class="children nav-child small collapse">
														<li class = "item-1" >
															<a class="" href="<?=$href?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Instruction Sheet
                                                                        <?=$flag?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>	
														</li>
													</ul>
												</li>
                                                		
												
												<?php
											} 
											
											if($right_transfer_and_followups_form == 'false')
											{
												if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)) || in_array("Billing", $userPrivilegesArr))
												{
													$href	=	"javascript:right_link_click('transfer_followups.php',9,'".$seqArrTemp21."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."');";	
												}
												else
												{
													$href	=	"javascript:void(0);";	
												}
												
												if($TransferFollowupsFormStatus == 'completed')
												{ 
													$flag	=	'<img src="images/green_flag.png" width="12" height="14" border="0">';
												}
												
												if($TransferFollowupsFormStatus == 'not completed') 
												{
													$flag	=	'<img src="images/red_flag.png" width="12" height="14" border="0">';
												}
												
												
				?>
                								<li class = "item-1 deeper parent" >
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Transfer and Followup</span>
                                        			</a>
                                                		
													<ul class="children nav-child small collapse">
														<li class = "item-1" >
															<a class="" href="<?=$href?>">
                                                                <span >
                                                                    <span class="sign"><i class="fa-play fa"></i></span>
                                                                    <span class="lbl">
                                                                    	Transfer and Followups
                                                                        <?=$flag?>
                                                                 	</span>
                                                                    
                                                                </span>
                                                                
                                                            </a>	
														</li>
													</ul>
												</li>
                                                		
												
												<?php
											} 
											
											//start		
											$category = "select confirmation_id from `consent_multiple_form`
														WHERE  confirmation_id = '".$patient_confID."' 
														AND left_navi_status = 'false'";
											$categoryRes = imw_query($category) or die(imw_error()); 
											$categoryRow = imw_num_rows($categoryRes);
											$seqArr=0;
											
											//$cntIdArr=0;
					
											if($categoryRow>0)				
											{
												$seqArrNew=$seqArr;
											?>					
												<li class = "item-1 deeper parent">
                                                	<a class="" href="#">
                                                        <span 	data-toggle="collapse" 
                                                                class="sign"
                                                        >
                                            				<i class="fa-plus fa"></i>
                                           				</span>
                                            			<span class="lbl">Forms</span>
                                        			</a>
                                                    
													<ul class="children nav-child small collapse" >
								
											<?php				
												$categoryRightSelectQry = "select * from `consent_category`  order by category_id";
												$categoryRightSelectRes = imw_query($categoryRightSelectQry) or die(imw_error()); 
												$categoryRightSelectNumRow = imw_num_rows($categoryRightSelectRes);
												while($categoryRightSelectRow = imw_fetch_array($categoryRightSelectRes)) 
												{		
											
											
													$cat_id=$categoryRightSelectRow['category_id'];
													$category_name=$categoryRightSelectRow['category_name'];
													$consentFormTemplateRightSelectQry = "select consent_id,consent_alias,consent_delete_status from `consent_forms_template` where consent_category_id='$cat_id' order by consent_id";
													$consentFormTemplateRightSelectRes = imw_query($consentFormTemplateRightSelectQry) or die(imw_error()); 
													$consentFormTemplateRightSelectNumRow = imw_num_rows($consentFormTemplateRightSelectRes);
													//$consentFormAliasRightArr = array();
													$consentFormSelectRightConsentTemplateId=array();
													//$CheckconsentFormAliasRightArr = array();
													$consentFormRightSelectCategoryQry = "select surgery_consent_id from `consent_multiple_form` 
																										WHERE  confirmation_id = '".$patient_confID."' 
																										AND consent_category_id ='".$cat_id."' 
																										AND left_navi_status='false'
																									";
													$consentFormRightSelectCategoryRes = imw_query($consentFormRightSelectCategoryQry) or die(imw_error()); 
													$consentFormRightSelectCategoryNumRow = imw_num_rows($consentFormRightSelectCategoryRes);
													if($consentFormRightSelectCategoryNumRow>0){
														if($consentFormTemplateRightSelectNumRow>0) {
															
										?>					
                                        						<li class = "item-1 deeper parent" >
																	<a class="" href="#">
                                                                        <span 	data-toggle="collapse" 
                                                                                class="sign"
                                                                        >
                                                                            <i class="fa-plus fa"></i>
                                                                        </span>
                                                                        <span class="lbl"><?php echo ucfirst($category_name);?></span>
                                        							</a>
																	
																<ul class="children nav-child small collapse">
													<?php			while($consentFormTemplateRightSelectRow = imw_fetch_array($consentFormTemplateRightSelectRes)){
																		
																		$consentFormSelectRightConsentTempleteId = $consentFormTemplateRightSelectRow['consent_id'];	
																		$consentFormSelectRightConsentTempleteAlias = $consentFormTemplateRightSelectRow['consent_alias'];	
																		$consentFormRightSelectQry = "select * from `consent_multiple_form` 
																											WHERE  confirmation_id = '".$patient_confID."' 
																											AND consent_template_id='".$consentFormSelectRightConsentTempleteId."'
																									";
																		$consentFormRightSelectRes = imw_query($consentFormRightSelectQry) or die(imw_error()); 
																		$consentFormRightSelectNumRow = imw_num_rows($consentFormRightSelectRes);
																		if($consentFormRightSelectNumRow>0) {
																			while($consentFormRightSelectRow = imw_fetch_array($consentFormRightSelectRes)){
																				$seqArrNew++;
																				$consentFormSelectRightConsentAlias = $consentFormRightSelectRow['surgery_consent_alias'];	
																				$selectedConsentFormAutoIncrId = $consentFormRightSelectRow['surgery_consent_id'];
																				$selectedConsentFormStatus = $consentFormRightSelectRow['form_status'];	
																				$selectedConsentFormleftStatus = $consentFormRightSelectRow['left_navi_status'];	
																				$selectedConsentFormPurgeStatus = $consentFormRightSelectRow['consent_purge_status'];	
																				if($selectedConsentFormPurgeStatus){
																					$strikeConsentPurgeRight = "background-image:url(images/strike_image.jpg); background-repeat:repeat-x; background-position:center;";
																				}else{
																					$strikeConsentPurgeRight = "";
																				}
																				if($consentFormSelectRightConsentAlias==""){
																					$consentFormSelectRightConsentAlias=$consentFormSelectRightConsentTempleteAlias;
																				}
																				if(($consentFormSelectRightConsentAlias)&& ($selectedConsentFormleftStatus=='false')){
																					if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)))
																					{
																						$href	=	"javascript:right_link_click('consent_multiple_form.php',0,'".$seqArrNew."','#D1E0C9','".$patientIdDos."','".$patient_confID."','".$ascId."','".$consentFormSelectRightConsentTempleteId."','".$selectedConsentFormAutoIncrId."','".$selectedConsentFormPurgeStatus."');";	
																					}
																					else
																					{
																						$href	=	"javascript:void(0);";	
																					}
																					
																					if($selectedConsentFormStatus=='completed')
																					{
																						$flag	=	'<img src="images/green_flag.png" style="width:16px; height:18px; border:none;">';
																					}
																					
																					if($selectedConsentFormStatus == 'not completed')
																					{
																						$flag	=	'<img src="images/red_flag.png" style="width:16px; height:18px; border:none;">';
																					}
																					
																					
																?>
																					<li class = "item-1" >
                                                                                    	<a class="" href="<?=$href?>">
                                                                                        	<span >
                                                                                                <span class="sign"><i class="fa-play fa"></i></span>
                                                                                                <span class="lbl">
                                                                                                   <?php echo stripslashes(ucfirst($consentFormSelectRightConsentAlias)).$purge_form;?>
                                                                                                    <?=$flag?>
                                                                                                </span>
                                                                                                
                                                                                            </span>
                                                                
                                                            							</a>
																					</li>
																			
															<?php				}
																			}
																		} 
																	}
												?>				</ul>
															</li>									
											<?php			
														}
													}else{ /*DO NOTHING*/}	 
												}
											//}	
											?>
										</ul>
									</li>		
				<?php				
									}
				//END GET MULTIPLE CONSENT FORMS
								}	
				?>
							</ul>
							
                            </li>
                
                <?PHP			
							
						} // End While
						
						
					} // End IF $patientDosNumRow > 0
				
				?>
                    
				</ul>
          	</div>
		</Div>
        	
		</div> <!--  End Modal Body -->       
                
            
    </div><!-- End Modal Content -->
  
  </div> <!-- End Modal Dialog -->
  
</div> <!-- End Modal Box -->

<script type="text/javascript">
ddtreemenu.createTree('treemenu1', false);
</script>