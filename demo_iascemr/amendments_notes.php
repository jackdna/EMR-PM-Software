<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Amemdment Notes</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
	.drsElement {
		position: absolute;
		border: 1px solid #333;
	}
	.drsMoveHandle {
		height: 20px;
		background-color: #CCC;
		border-bottom: 1px solid #666;
	}
</style>
<script type="text/javascript" src="js/epost.js"></script>
<script src="js/dragresize.js"></script>
<?php
$spec = '</head>
<body onLoad="top.changeColor(\''.$bgcolor_Amendments_notes.'\');" onClick="closeEpost(); return top.frames[0].main_frmInner.hideSliders();" >';
include("common/link_new_file.php");
//include "common/fckeditor.php";
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
$patient_id = $_REQUEST['patient_id'];
$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
if(!$patient_id) {$patient_id = $_SESSION['patient_id'];  }

$cancelRecord = $_REQUEST['cancelRecord'];
$finalize = $_REQUEST['finalize'];
$amendment_finalize = $_REQUEST['amendment_finalize'];
$delId = $_REQUEST['delId'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$tablename = "amendment";
// GETTING LOGIN USER NAME
	$str = "SELECT * FROM users
			WHERE usersId = '$userLoginId'";
	$qry = imw_query($str);
	$fetchRows = imw_fetch_array($qry);
	 $user_fname = $fetchRows['fname'];
	 $user_mname = $fetchRows['mname'];
	 $user_lname = $fetchRows['lname'];
	 $user_name = $user_lname.", ".$user_fname." ".$user_mname;
	 $userdesignation = $fetchRows['user_type'];
	 $userdesignationLabel = ($userdesignation == 'Anesthesiologist') ? 'Anesthesia Provider' : $userdesignation  ;
	 $userSubdesignation = $fetchRows['user_sub_type'];
	 $usersign = $fetchRows['signature'];
	$usersignature ="Yes";
// GETTING LOGIN USER NAME


if($delId){
	$objManageData->delRecord('amendment', 'amendmentId', $delId);
}

//GETTING SURGEONDETAILS SIGN YES OR NO
	$confirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	$finalizeStatus = $confirmationDetails->finalize_status;
	$surgeonId = $confirmationDetails->surgeonId;
	unset($conditionArr);
	$conditionArr['usersId'] = $surgeonId;
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails) {	
		foreach($surgeonsDetails as $usersDetail){
			$signatureOfSurgeon=$usersDetail->signature;
			$fname_surgeon=$usersDetail->fname;
			$mname_surgeon=$usersDetail->mname;
			$lname_surgeon=$usersDetail->lname;
			$surgeon_name=$fname_surgeon."".$mname_surgeon."".$lname_surgeon;
		}
	}	
//GETTING SURGEONDETAILS SIGN YES OR NO

// FINALIZE CURR PATIENT CONFIRMATION
if($finalize){
	unset($arrayRecord);
	$arrayRecord['finalize_status'] = $finalize;
	$objManageData->updateRecords($arrayRecord, 'patientconfirmation', 'patientConfirmationId', $pConfId);

	//DESTROY ALL EPOST-IT
		$epostConfirmDetails = $objManageData->getRowRecord('eposted', 'patient_conf_id', $pConfId);		
		if($epostConfirmDetails) {	
			$objManageData->delRecord('eposted', 'patient_conf_id', $pConfId);	
		}	
	//END DESTROY ALL EPOST-IT
}
// FINALIZE CURR PATIENT CONFIRMATION

// FINALIZE AMENDEMNT FORM OF CURR PATIENT
if($amendment_finalize){
	unset($arrayRecord);
	$arrayRecord['amendment_finalize_status'] = $amendment_finalize;
	$objManageData->updateRecords($arrayRecord, 'patientconfirmation', 'patientConfirmationId', $pConfId);	
}
// END FINALIZE AMENDEMNT FORM OF CURR PATIENT	


if(!$cancelRecord){
	////// FORM SHIFT TO RIGHT SLIDER
		$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
		$physician_amendments_form = $getLeftLinkDetails->physician_amendments_form;
		if($physician_amendments_form=='true'){
			$formArrayRecord['physician_amendments_form'] = 'false';
			$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
		}
		//MAKE AUDIT STATUS VIEW
		
		if($_REQUEST['saveRecord'] != 'true'){
			unset($arrayRecord);
			$arrayRecord['user_id'] = $_SESSION['loginUserId'];
			$arrayRecord['patient_id'] = $patient_id;
			$arrayRecord['confirmation_id'] = $pConfId;
			$arrayRecord['form_name'] = 'physician_amendments_form';
			$arrayRecord['status'] = 'viewed';
			$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
			
			$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
		}
		//MAKE AUDIT STATUS VIEW
		
	////// FORM SHIFT TO RIGHT SLIDER
}
elseif($cancelRecord){
	$fieldName="physician_amendments_form";
	$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";
	include("left_link_hide.php");
}	
	$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&fieldName='.$fieldName;

if($_REQUEST['saveRecord'])
{
	$text = $_REQUEST['getText'];
	$tablename = "amendment";
	Save_eposts($text,$tablename);
	
	unset($arrayRecord);
	//CODE TO SET FORM STATUS 
		$form_status = 'completed';
		if(trim($_REQUEST["amendmentsText"])=="") {
			$form_status = "not completed";
		}
	//CODE TO SET FORM STATUS 
	$amendmentId = $_REQUEST['amendmentId'];
	$arrayRecord['notes'] = trim(addslashes($_REQUEST['amendmentsText']));
	$arrayRecord['finalizeId'] = $pConfId;	
	$arrayRecord['dateAmendment'] = date('Y-m-d');
	$arrayRecord['timeAmendment'] = date('H:i:s');
	$arrayRecord['userId'] = $_SESSION['loginUserId'];
	$arrayRecord['signUser'] = $_REQUEST['elem_signature'];
	$arrayRecord['confirmation_id'] = $pConfId;
	$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['form_status'] = $form_status;
	
	//MAKE AUDIT STATUS CRATED OR MODIFIED
	unset($arrayStatusRecord);
	$arrayStatusRecord['user_id'] = $_SESSION['loginUserId'];
	$arrayStatusRecord['patient_id'] = $patient_id;
	$arrayStatusRecord['confirmation_id'] = $pConfId;
	$arrayStatusRecord['form_name'] = 'physician_amendments_form';	
	$arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
	//MAKE AUDIT STATUS CRATED OR MODIFIED
	
	if($amendmentId){
		$objManageData->updateRecords($arrayRecord, 'amendment', 'amendmentId', $amendmentId);
		//MAKE AUDIT STATUS MODIFIED
		$arrayStatusRecord['status'] = 'modified';
		$objManageData->addRecords($arrayStatusRecord, 'chartnotes_change_audit_tbl');
		//MAKE AUDIT STATUS MODIFIED
	}else{
		if(trim($_REQUEST['amendmentsText'])!=''){
			$insertId = $objManageData->addRecords($arrayRecord, 'amendment');
		}
		//MAKE AUDIT STATUS CREATED
		$arrayStatusRecord['status'] = 'created';		
		$objManageData->addRecords($arrayStatusRecord, 'chartnotes_change_audit_tbl');
		//MAKE AUDIT STATUS CREATED
	}	
}

// GETTTING PRIMARY AND SECONDARY PROCEDURES
	$physicianDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
		extract($physicianDetails);
		
// GETTTING PRIMARY AND SECONDARY PROCEDURES
?>
<script type="text/javascript">
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
	function delAmendment(id,pConfId){
		var thisId = 11;
		var innerKey = <?php echo $innerKey; ?>;
		var ask = confirm('Are you sure to delete the record.');
		if(ask==true){
			top.frames[0].frames[0].location.href = 'amendments_notes.php?delId='+id+'&pConfId='+pConfId+'&thisId='+thisId+'&innerKey='+innerKey;
		}
	}

	function signUserFun(amendmentSignTypeId,amendmentSignatureId,hiddFieldInput,delSign) {
		if(delSign) {
			document.getElementById(hiddFieldInput).value = "";
			document.getElementById(amendmentSignTypeId).style.display = "block";
			document.getElementById(amendmentSignatureId).style.display = "none";
		}else {
			document.getElementById(hiddFieldInput).value = "userSignDone";
			document.getElementById(amendmentSignTypeId).style.display = "none";
			document.getElementById(amendmentSignatureId).style.display = "block";
		}
	}

	function noSignInAdmin() {
		alert("You have yet to make signature in Admin");
	}
</script>
<script type="text/javascript">dragresize.apply(document);</script>
<div id="post" style="position:absolute; left:50px;display:none;z-index:5;"></div>
<form action="amendments_notes.php?saveRecord=true" name="frm_gen_anes_nurse_notes" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
	<input type="hidden" name="thisId" value="<?php echo $thisId; ?>">
	<input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
	<input type="hidden" name="amendmentId" value="<?php echo $amendmentId; ?>">
	<input type="hidden" name="getText" id="getText">	
	<input type="hidden" name="signOnFileAmendment" id="signOnFileAmendment" value="">
	<input type="hidden" name="go_pageval" value="<?php echo $tablename;?>"/>
	<input type="hidden" name="frmAction" id="frmAction" value="amendments_notes.php">	
	<input type="hidden" name="SaveForm_alert" value="true">			
    <div class=" scheduler_table_Complete" id="" style="">
        <?php
			$epost_table_name	=	'amendment' ; 
			include_once './epost_list.php'; 
		?>
        <!--
        <div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_notes">
            <span class="bg_span_notes">
               Physician Notes
            </span>
            <i id="epostDelId"><?php while($row = imw_fetch_array($rsNotes)) { if($totalRows_rsNotes > 0) { ?><a class="btn-xs btn btn-primary " style="padding-left:1px;" title="ePostIt" onMouseOver="showEpost('<?php echo $row['epost_id'];?>','<?php echo $pConfId;?>')" ><b class="fa fa-comment"></b></a>  <?php } } ?></i>
        </div>	
        -->
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_notes">
            <div class="scanner_win new_s">
             <h4>
                <span>Amendments</span>      
             </h4>
            </div>
        </div> 
        <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:2;">
            <?php 
                $bgCol = $title_Amendments_notes;
                $borderCol = $title_Amendments_notes;
                include('saveDivPopUp.php'); 
            ?>
        </div>
           
        <div class="scheduler_table_Complete p_note_table">
            <div class="my_table_Checkall padding_15">
            
                    <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped  ">
                        <thead class="cf">
                            <tr>
                                <th class="text-left col-md-3 col-sm-3 col-xs-3 col-lg-3">Amendment Notes </th>
                                <th class="text-left col-md-2 col-sm-2 col-xs-2 col-lg-2">Who </th>
                                <th class="text-left col-md-2 col-sm-2 col-xs-2 col-lg-2">Created By </th>
                                <th class="text-left col-md-2 col-sm-2 col-xs-2 col-lg-2">Date</th>
                                <th class="text-left col-md-3 col-sm-3 col-xs-3 col-lg-3">Time</th>
                            </tr>
                        </thead>
                        <tbody style="">
							<?php 
                                $getAmendments = $objManageData->getArrayRecords('amendment', 'confirmation_id', $pConfId);
                                if(is_array($getAmendments)){
                                    foreach($getAmendments as $key => $amendment){
                                        $amendmentId = $amendment->amendmentId;
                                        $amendmentNotes = $amendment->notes;
                                        $dateAmendment = $objManageData->changeDateMDY($amendment->dateAmendment);
                                        $timeAmendment = $amendment->timeAmendment;
                                        $userIdAmendment = $amendment->userId;
                                        $form_status = $amendment->form_status;
                                        
                                        
                                        $getUserNameQry = "SELECT * FROM users
                                                WHERE usersId = '$userIdAmendment'";
                                        
                                        $getUserNameRes = imw_query($getUserNameQry) or die(imw_error());
                                        $getUserNameRow = imw_fetch_array($getUserNameRes);
                                         $getUserFname = $getUserNameRow['fname'];
                                         $getUserMname = $getUserNameRow['mname'];
                                         $getUserLname = $getUserNameRow['lname'];
                                         $getUserName = $getUserFname." ".$getUserMname." ".$getUserLname;
                                         $getUserType = $getUserNameRow['user_type'];
                                        $getUserTypeLabel = ($getUserType == 'Anesthesiologist') ? 'Anesthesia Provider' : $getUserType  ;
                                        
                                        //CODE TO SET AMENDMENT TIME
										if($timeAmendment=="00:00:00" || $timeAmendment=="") {
				$timeAmendment="";
			}else {			
				$time_split2 = explode(":",$timeAmendment);
				if($time_split2[0]=='24') { //to correct previously saved records
					$timeAmendment = "12".":".$time_split2[1].":".$time_split2[2];
				}
				//$timeAmendment = date('h:i A',strtotime($timeAmendment));
				$timeAmendment = $objManageData->getTmFormat($timeAmendment);
			}
                                            /*$time_split2 = explode(":",$timeAmendment);
                                            if($time_split2[0]>12) {
                                                $am_pm2 = "PM";
                                            }else {
                                                $am_pm2 = "AM";
                                            }
                                            if($time_split2[0]>=13) {
                                                $time_split2[0] = $time_split2[0]-12;
                                                if(strlen($time_split2[0]) == 1) {
                                                    $time_split2[0] = "0".$time_split2[0];
                                                }
                                            }else {
                                                //DO NOTHNING
                                            }
                                            $timeAmendment = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
											*/
                                        //END CODE TO SET AMENDMENT TIME	
                                        ?>
                                        <tr>
                                            <td class="text-left"> <?php echo stripslashes($amendmentNotes); ?></td>
                                            <td class="text-left"><a href="javascript:void(0)" class="con"><?php echo $getUserTypeLabel; ?> </a></td>
                                            <td class="text-left"> <a href="javascript:void(0)" class="con"> <?php echo stripslashes($getUserName); ?> </a> </td>
                                            <td class="text-left"><a href="javascript:void(0)" class="con"> <?php echo $dateAmendment; ?> </a> </td>
                                            <td class="text-left padding_right_15"><a href="javascript:void(0)" class="con"> <?php echo $timeAmendment; ?> 	
                                                <a title="Delete" style="color:#FFF;" class="btn btn-danger abs_tbody_cross" href="javascript:void(0)" onClick="javascript:delAmendment('<?php echo $amendmentId; ?>','<?php echo $_REQUEST['pConfId']; ?>')"> X </a> 
                                                </a>
                                            </td>
                                        </tr>
                                        
                                        <?php
                                    }
                                }
                            ?>
                        </tbody>
                </table>
             </div>                
          </div>
          
        <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
             <div class="panel panel-default bg_panel_notes">
                  <div class="panel-heading">
                         <h3 class="panel-title rob"> Amendment Notes  </h3>
                         <div class="right_label rob head_right_panel">
                            <span class="">  <?php echo $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));?>  </span>	 
                         </div>
                  </div>	
                  <div class="panel-body">
                        <div class="clearfix margin_adjustment_only"></div>
                       <Div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                         <textarea id="Field3" name="amendmentsText" class="form-control" style="resize:none;"><?php echo $notes; ?></textarea>
                       </div>		         
                       <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix  margin_adjustment_only">
                            <div class="clearfix border-dashed margin_adjustment_only"></div>
                        </div>                                                  
						<?php
                        $callJavaFun = "signUserFun('amendmentSignTypeId','amendmentSignatureId','signOnFileAmendment');";
                        $prefix="";
                        if(($userdesignation=="Surgeon" || $userdesignation=="Anesthesiologist") && $userSubdesignation!='CRNA') {
                            $prefix = "Dr. ";
                        }
                        
                        $callJavaFunDel = "signUserFun('amendmentSignTypeId','amendmentSignatureId','signOnFileAmendment','delSign');";
                        ?>                        
                        <div class="row">
                            <div class="col-md-4 col-sm-5 col-xs-12 col-lg-4">
                                <div class="inner_safety_wrap">
                                      <span class="rob"> <b> Date</b> <?php echo date("m-d-Y");?></span>
                                </div>
                            
                            </div>
                            <div class="col-md-8 col-sm-7 col-lg-8 col-xs-12 pull-right">
                                <div class="inner_safety_wrap" id="amendmentSignTypeId" style="display:inline-block;" >
                                    <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFun;?>"> <?php echo $userdesignationLabel." Signature";?> </a>
                                </div>
                               
                                <div class="inner_safety_wrap collapse" id="amendmentSignatureId" style="display:none;">
                                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo $userdesignationLabel.": ".$prefix.$user_name;?>  </a></span>	     
                                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $usersignature;?></span>
                                </div>
                            </div>
                       </div>
                  </div>
             </div>
        </div>		                                                
	</div>
</form>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" method="post" action="amendments_notes.php?cancelRecord=true">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->	


<!-- AMENDMENT FINALIZE FORM -->
	<form name="amendmentFinalizeMe" method="post" action="amendments_notes.php?amendment_finalize=true">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
		<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
		<input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
		<input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
	</form>
<!-- FINALIZE FORM -->
<?php
if($finalizeStatus!='true' && $amendment_finalize_status!='true'){
	?>
	<script>
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();	
		top.document.getElementById('Finalized').style.display = 'none';			
	</script>
	<?php
	include('privilege_buttons.php');
}else if($finalizeStatus=='true' && $amendment_finalize_status!='true'){
	?>
	<script>
		top.frames[0].setPNotesHeight();		
		top.document.getElementById('footer_button_id').style.display = 'inline-block';
		top.document.getElementById('saveBtn').style.display = 'inline-block';
		top.document.getElementById('CancelBtn').style.display = 'none';
		top.document.getElementById('PrintBtn').style.display = 'inline-block';
		top.document.getElementById('SavePrintBtn').style.display = 'none';
		top.document.getElementById('Finalized').style.display = 'none';
		top.document.getElementById('AmendmentFinalized').style.display = 'inline-block';
		
		if(top.document.getElementById("footer_print_button_id")) {
			top.document.getElementById("footer_print_button_id").style.display = "none";
		}	
		
	</script>
	<?php
	include('privilege_buttons.php');
}else{
	?>
	<script>
		top.frames[0].setPNotesHeight();		
		top.document.getElementById('footer_button_id').style.display = 'none';
		if(top.document.getElementById("footer_print_button_id")) {
			top.document.getElementById("footer_print_button_id").style.display = "inline-block";
		}
	</script>
	<?php
}
	if($SaveForm_alert == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}

?>
<script>
//Start Hide Purge and Reset Button
if(top.document.getElementById("PurgeBtn")) {
	top.document.getElementById("PurgeBtn").style.display = "none";
}
if(top.document.getElementById("ResetBtn")) {
	top.document.getElementById("ResetBtn").style.display = "none";
}
//End Hide Purge and Reset Button
</script>
<?php include("print_page.php");?>
</body>
</html>