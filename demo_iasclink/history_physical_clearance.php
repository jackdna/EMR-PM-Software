<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
include_once("common/conDb.php");
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
$patient_id = $_REQUEST['patient_id'];
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
if(!$patient_id) {$patient_id = $_SESSION['patient_id'];  }
$tablename = "iolink_history_physical";
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.11.3.js"></script>
<script src="js/jscript.js" ></script>
<script src="js/epost.js"></script>
<?php
$spec= "
</head>
<body onClick=\"document.getElementById('divSaveAlert').style.display = 'none'; closeEpost();\">";
include("common/link_new_file.php");
include_once("common/commonFunctions.php");
include("common/iOLinkCommonFunction.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
$uid=$_SESSION['iolink_loginUserId'];
$usertypeqry=imw_query("select * from users where usersId='".$uid."'");
$recordId=imw_fetch_array($usertypeqry);
$usertype=$recordId['user_type'];
$nurse=$recordId['fname']." ".$recordId['lname'];
$ascId 				= $_SESSION['ascId'];
$SaveForm_alert 	= $_REQUEST['SaveForm_alert'];
$history_physical_id = $_REQUEST['history_physical_id'];
$cancelRecord 		= $_REQUEST['cancelRecord'];
	
$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;patient_in_waiting_id='.$patient_in_waiting_id.'&amp;ascId='.$ascId.'&amp;fieldName='.$fieldName;
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
	

//START CODE TO GET SUB TYPE OF USER
$usrAllSubTypeArr = array();
$usrAllQry 							= "SELECT usersId,user_sub_type FROM users";
$usrAllRes							= imw_query($usrAllQry);
if(imw_num_rows($usrAllRes)>0) {
	while($usrAllRow				= imw_fetch_array($usrAllRes)) {
		$usrAllId 					= $usrAllRow["usersId"];
		$userAllSubType 			= $usrAllRow["user_sub_type"];
		$usrAllSubTypeArr[$usrAllId]= $userAllSubType;
	}
}
//END CODE TO GET SUB TYPE OF USER

$questionsArr = array('cadMI'=>array('title'=> 'CAD/MIN(W/ WO Stent OR CABG)/PVD)'), 
											'cvaTIA'=> array('title' =>'VA/TIA/ Epilepsy, Neurological'),
											'htnCP'=> array('title' =>'HTN/ +/- CP/SOB on Exertion'),
											'anticoagulationTherapy'=> array('title' =>'Anticoagulation therapy (i.e. Blood Thinners)'),
											'respiratoryAsthma'=> array('title' =>'Respiratory - Asthma / COPD / Sleep Apnea'),
											'arthritis'=> array('title' =>'Arthritis'),
											'diabetes'=> array('title' =>'Diabetes'),
											'recreationalDrug'=> array('title' =>'Recreational Drug Use'),
											'giGerd'=> array('title' =>'GI - GERD / PUD / Liver Disease / Hepatitis'),
											'ocular'=> array('title' =>'Ocular'),
											'kidneyDisease'=> array('title' =>'Kidney Disease, Dialysis, G-U'),
											'hivAutoimmune'=> array('title' =>'HIV, Autoimmune Diseases, Contagious Diseases'),
											'historyCancer'=> array('title' =>'History of Cancer'),
											'organTransplant'=> array('title' =>'Organ Transplant'),
											'badReaction'=> array('title' =>'A Bad Reaction to Local or General Anesthesia'),
											'highCholesterol' => array('title' => 'High Cholesterol'),
											'thyroid' => array('title' => 'Thyroid'),
											'ulcer' => array('title' => 'Ulcers'),
											'heartExam' => array('title' => 'Heart Exam done with stethoscope - Normal','txtFieldOn' => 'No'),
											'lungExam' => array('title' => 'Lung Exam done with stethoscope - Normal','txtFieldOn' => 'No'),
											'discussedAdvancedDirective' => array('title' => 'Discussed Advanced Directives and Patient Rights and Responsibilities','descBoxShow'=>'false'),
											'otherHistoryPhysical'=> array('title' =>'Other'),
										 );
	
if($_REQUEST['saveRecord']=='true'){

	unset($arrayRecord);
	
	//START CODE TO CHECK Form Status IN DATABASE
	$chkUserSignDetails = $objManageData->getRowRecord($tablename, 'pt_waiting_id', $patient_in_waiting_id);
	if($chkUserSignDetails) {
		$chk_form_status = $chkUserSignDetails->form_status;
	}
	//END CODE TO CHECK NURSE SIGN IN DATABASE 

	// Start Saving Pre Define Admin Questions
	for ($c = 0; $c < $_REQUEST['ques_count']; $c++){
		$ques = addslashes($_REQUEST['ques_'.$c]);
		$ques_status = $_REQUEST['chbx_ques_'.$c];
		$ques_desc = ($ques_status == 'Yes') ? addslashes($_REQUEST['ques_desc_'.$c]) : '';
		
		$action = "Insert Into iolink_history_physical_ques Set ";
		$where = "";
		$value = "pt_waiting_id = '".$patient_in_waiting_id."', patient_id = '".$_REQUEST['patient_id']."', ques = '".$ques."', ques_status = '".$ques_status."', ques_desc = '".$ques_desc."' ";
		if( $chk_form_status == 'completed' || $chk_form_status=='not completed' ) {
			$action = "Update iolink_history_physical_ques Set ";
			$where = "Where pt_waiting_id = '".$patient_in_waiting_id."' And ques = '".$ques."' ";
			$value = "ques_status = '".$ques_status."', ques_desc = '".$ques_desc."' ";
		}
		
		$qry = $action.$value.$where;
		$sql = imw_query($qry) or die('Error found at line no. '.(__LINE__).': '.$qry.' --- '.imw_error());
		
	}
	// End Saving Pre Define Admin Questions
	
	
	foreach($questionsArr as $k => $q){
		if( $k == 'otherHistoryPhysical') {
			$arrayRecord['otherHistoryPhysical'] = addslashes($_REQUEST['otherHistoryPhysical']);
			continue;
		}
		
		$fld = 'chbx_'.$k;
		$fld_desc = $k.'Desc';
		
		$txtFieldOn = isset($q['txtFieldOn']) ? $q['txtFieldOn'] : 'Yes';
		$txtFieldOn = strtolower($txtFieldOn);
		
		$arrayRecord[$k] = $_REQUEST[$fld];
		if( isset($_REQUEST[$fld_desc]) ) {
			$arrayRecord[$fld_desc] = $txtFieldOn == strtolower($_REQUEST[$fld]) ? addslashes($_REQUEST[$fld_desc]) : '';
		}
		
	}
	
	$save_date_time = date('Y-m-d H:i:s');
	$save_operator_id = $_SESSION['loginUserId'];
		
	$formStatus='not completed';
	$arrayRecord['form_status'] = $formStatus;
	
	if($history_physical_id){		
		$arrayRecord['save_date_time'] 				= $save_date_time;
		$arrayRecord['save_operator_id'] 			= $save_operator_id;
		$objManageData->updateRecords($arrayRecord, $tablename, 'history_physical_id', $history_physical_id);
	}else{
		$arrayRecord['pt_waiting_id'] = $patient_in_waiting_id;
		$arrayRecord['patient_id'] = $patient_id;
		$arrayRecord['version_num'] = $_REQUEST['version_num'];
		$arrayRecord['version_date_time'] = date('Y-m-d H:i:s');
		$arrayRecord['create_date_time'] 				= $save_date_time;
		$arrayRecord['create_operator_id'] 			= $save_operator_id;
		$history_physical_id = $objManageData->addRecords($arrayRecord, $tablename);
	}
	
	setReSyncroStatus($patient_in_waiting_id,'historyPhysical');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
}

$blockImg = "images/block.gif";
$noneImg = "images/none.gif";
?>
<script src="js/jscript.js"></script>
<div id="post" style="display:none; position:absolute;"></div>
<?php 
	// Chart version number set to 4
	// while creating this page in iASCLink, in iASCEMR chart version number is 4
	$chart_version_num = 4;
	
	if($history_physical_id){
		$getPreOpQuesDetails = $objManageData->getExtractRecord($tablename, "history_physical_id", $history_physical_id, " *, if(date_format(date_of_h_p ,'%m-%d-%Y')='00-00-0000','',date_format(date_of_h_p ,'%m-%d-%Y')) as date_of_h_p_format, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat ");
	}else if($patient_in_waiting_id){
		$getPreOpQuesDetails = $objManageData->getExtractRecord($tablename, "pt_waiting_id", $patient_in_waiting_id, " *, if(date_format(date_of_h_p ,'%m-%d-%Y')='00-00-0000','',date_format(date_of_h_p ,'%m-%d-%Y')) as date_of_h_p_format, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat ");	
	}
	if(is_array($getPreOpQuesDetails)){
		extract($getPreOpQuesDetails);
	}
	
	if( $form_status == 'completed' || $form_status == 'not completed') {
		$getAddQuestions = $objManageData->getAllRecords('iolink_history_physical_ques','', array('pt_waiting_id = '=>$patient_in_waiting_id ), array(),array('ques + 0'=>'ASC'));
	}
	else {
		$getAddQuestions = $objManageData->getAllRecords('predefine_history_physical',array('id','name as ques'), array('deleted = '=>'0'), array(),array('name + 0'=>'ASC'));
	}
	
	$HPQuesHTML_L = ''; $HPQuesHTML_R = '';
	foreach($questionsArr as $fld => $ques){
		
		$HPQuesHTML = '';
		if( $ques['title'] == 'Other' ) {
			
			$HPQuesHTML .= '<tr style="background-color:#FFFFFF; width:350px;">';
			$HPQuesHTML .= '<td colspan="4" class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:20px;">';
			$HPQuesHTML .= '<table class="table_collapse alignCenter" style="border:none;">';
			$HPQuesHTML .= '<tr>';
			$HPQuesHTML .= '<td style=" padding-left:6px; width:1px;">&nbsp;</td>';
			$HPQuesHTML .= '<td class="text_10 pad_top_bottom alignLeft valignMiddle" style="width:30px; padding-right:10px;">Other</td>';
			$HPQuesHTML .= '<td class="text_10 alignLeft" style="width:305px;">';
			$HPQuesHTML .= '<textarea id="'.$fld.'" name="'.$fld.'" class="field textarea justi" style="border:1px solid #cccccc; width:300px;height:40px;"  tabindex="6">'.stripslashes($$fld).'</textarea>';
			$HPQuesHTML .= '</td>';
			$HPQuesHTML .= '</tr>';
			$HPQuesHTML .= '</table>';
			$HPQuesHTML .= '</td>';
			$HPQuesHTML .= '</tr>';
			$HPQuesHTML_L .= $HPQuesHTML;
			continue;
		}
		
		$dFld = $fld.'Desc';
		
		$HPQuesHTML .= '<tr style="background-color:#F1F4F0;">';
		$HPQuesHTML .= '<td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:405px;">';
		$HPQuesHTML .= '<div style="padding-left:2px; ">'.$ques['title'].'</div>';
		
		$onClick_yes = 'checkSingle(\'chbx_'.$fld.'_yes\',\'chbx_'.$fld.'\');changeChbxColor(\'chbx_'.$fld.'\');';
		$onClick_no = 'checkSingle(\'chbx_'.$fld.'_no\',\'chbx_'.$fld.'\');changeChbxColor(\'chbx_'.$fld.'\');';
		$onClick_img = '';
		$img = '';
		
		if( $ques['descBoxShow'] <> 'false' ) {
			if( $ques['txtFieldOn'] == 'No') {
				$onFld = 'no';
				$onClick_yes .= 'disp_none(document.frm_history_physical.chbx_'.$fld.',\'tr_'.$fld.'_'.$onFld.'\');';
				$onClick_no .= 'disp(document.frm_history_physical.chbx_'.$fld.',\'tr_'.$fld.'_'.$onFld.'\');';
			}
			else {
				$onFld = 'yes';
				$onClick_yes .= 'disp(document.frm_history_physical.chbx_'.$fld.',\'tr_'.$fld.'_'.$onFld.'\');';
				$onClick_no .= 'disp_none(document.frm_history_physical.chbx_'.$fld.',\'tr_'.$fld.'_'.$onFld.'\');';
			}
			
			$onClick_img = 'javascript:disp_rev(document.frm_history_physical.chbx_'.$fld.',\'tr_'.$fld.'_'.$onFld.'\',\'img_'.$fld.'\')';
			$img = '<img alt="img_'.$fld.'" src="'.($$fld==ucwords($onFld)?$noneImg:$blockImg).'" style="cursor:pointer; width:11px; height:13px; border:none; " />';
			
			$HPQuesHTML .= '<table class="table_collapse alignCenter" id="tr_'.$fld.'_'.$onFld.'"  style="display:'.(($$fld==ucwords($onFld)||$ques_title=='Other')?'inline-block':'none').';">';
			$HPQuesHTML .= '<tr><td colspan="3" style="padding-left:2px;"></td></tr>';
			$HPQuesHTML .= '<tr>';
			$HPQuesHTML .= '<td style="padding-left:6px; width:1px;">&nbsp;</td>';
			$HPQuesHTML .= '<td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>';
			$HPQuesHTML .= '<td class="text_10  alignLeft" style="width:305px;">';
			$HPQuesHTML .= '<textarea id="'.$dFld.'" class="field textarea justi" name="'.$dFld.'" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6">'. stripslashes($$dFld).'</textarea>';
			$HPQuesHTML .= '</td>';
			$HPQuesHTML .= '</tr>';
			$HPQuesHTML .= '</table>';
		}
		
		$HPQuesHTML .= '</td>';
		
		$HPQuesHTML .= '<td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="'.$onClick_yes.'">';
		$HPQuesHTML .= '<span class="clpChkBx"><span class="colorChkBx" style="'.($$fld?$whiteBckGroundColor:'').'">';
		$HPQuesHTML .= '<input class="field checkbox opctyChkBx" '.($$fld=='Yes'?'Checked':'').' name="chbx_'.$fld.'" type="checkbox" value="Yes" id="chbx_'.$fld.'_yes" tabindex="7" >';
		$HPQuesHTML .= '</span></span>';
		$HPQuesHTML .= '</td>';

		$HPQuesHTML .= '<td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="'.$onClick_no.'">';
		$HPQuesHTML .= '<span class="clpChkBx"><span class="colorChkBx" style="'.($$fld?$whiteBckGroundColor:'').'" >';
		$HPQuesHTML .= '<input class="field checkbox opctyChkBx" '.($$fld=='No'?'Checked':'').' name="chbx_'.$fld.'" type="checkbox" value="No" id="chbx_'.$fld.'_no" tabindex="7" >';

		$HPQuesHTML .= '</span></span>';
		$HPQuesHTML .= '</td>';

		$HPQuesHTML .= '<td id="img_'.$fld.'" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="'.$onClick_img.'" >';
		$HPQuesHTML .= $img;
		$HPQuesHTML .= '</td>';
		
		$HPQuesHTML .= '</tr>';
		$HPQuesHTML_L .= $HPQuesHTML;
		//if( $fld == 'heartExam' || $fld == 'lungExam' || $fld == 'discussedAdvancedDirective' ) $HPQuesHTML_R .= $HPQuesHTML;
		//else $HPQuesHTML_L .= $HPQuesHTML;
	}
	
	$HPCustomQuesHTML = ''; 
	$counter = -1;
	if( $getAddQuestions ) {
		foreach($getAddQuestions as $qArr ) {
			$counter++;
			
			$fld = 'ques_'.$counter;
			$dFld = 'ques_desc_'.$counter;
			
			$status = $qArr->ques_status;
			$desc = $qArr->ques_desc;
			
			$HPCustomQuesHTML .= '<tr style="background-color:#F1F4F0;">';
			$HPCustomQuesHTML .= '<td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:405px;">';
			$HPCustomQuesHTML .= '<input type="hidden" name="ques_'.$counter.'" id="ques_'.$counter.'" value="'.$qArr->ques.'" />';
			$HPCustomQuesHTML .= '<div style="padding-left:2px; ">'.$qArr->ques.'</div>';
		
			$onClick_yes = 'checkSingle(\'chbx_'.$fld.'_yes\',\'chbx_'.$fld.'\');disp(document.frm_history_physical.chbx_'.$fld.',\'tr_'.$fld.'_yes\');';
			$onClick_no = 'checkSingle(\'chbx_'.$fld.'_no\',\'chbx_'.$fld.'\');disp_none(document.frm_history_physical.chbx_'.$fld.',\'tr_'.$fld.'_yes\');';
			
			$onClick_img = 'javascript:disp_rev(document.frm_history_physical.chbx_'.$fld.',\'tr_'.$fld.'_yes\',\'img_'.$fld.'\')';
			$img = '<img alt="img_'.$fld.'" src="'.($status=='Yes'?$noneImg:$blockImg).'" style="cursor:pointer; width:11px; height:13px; border:none; " />';
			
			$HPCustomQuesHTML .= '<table class="table_collapse alignCenter" id="tr_'.$fld.'_yes"  style="display:'.(($status=='Yes'||$ques_title=='Other')?'inline-block':'none').';">';
			$HPCustomQuesHTML .= '<tr><td colspan="3" style="padding-left:2px;"></td></tr>';
			$HPCustomQuesHTML .= '<tr>';
			$HPCustomQuesHTML .= '<td style="padding-left:6px; width:1px;">&nbsp;</td>';
			$HPCustomQuesHTML .= '<td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>';
			$HPCustomQuesHTML .= '<td class="text_10  alignLeft" style="width:305px;">';
			$HPCustomQuesHTML .= '<textarea id="'.$dFld.'" class="field textarea justi" name="'.$dFld.'" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6">'. stripslashes($desc).'</textarea>';
			$HPCustomQuesHTML .= '</td>';
			$HPCustomQuesHTML .= '</tr>';
			$HPCustomQuesHTML .= '</table>';
			
			$HPCustomQuesHTML .= '</td>';
		
			$HPCustomQuesHTML .= '<td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="'.$onClick_yes.'">';
			$HPCustomQuesHTML .= '<span class="clpChkBx">';
			$HPCustomQuesHTML .= '<input class="field checkbox opctyChkBx" '.($status=='Yes'?'Checked':'').' name="chbx_'.$fld.'" type="checkbox" value="Yes" id="chbx_'.$fld.'_yes" tabindex="7" >';
			$HPCustomQuesHTML .= '</span>';
			$HPCustomQuesHTML .= '</td>';

			$HPCustomQuesHTML .= '<td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="'.$onClick_no.'">';
			$HPCustomQuesHTML .= '<span class="clpChkBx">';
			$HPCustomQuesHTML .= '<input class="field checkbox opctyChkBx" '.($status=='No'?'Checked':'').' name="chbx_'.$fld.'" type="checkbox" value="No" id="chbx_'.$fld.'_no" tabindex="7" >';

			$HPCustomQuesHTML .= '</span>';
			$HPCustomQuesHTML .= '</td>';

			$HPCustomQuesHTML .= '<td id="img_'.$fld.'" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="'.$onClick_img.'" >';
			$HPCustomQuesHTML .= $img;
			$HPCustomQuesHTML .= '</td>';

			$HPCustomQuesHTML .= '</tr>';				
		}
	}
	?>
    
    <form name="frm_history_physical" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;" action="history_physical_clearance.php?saveRecord=true&amp;SaveForm_alert=true">
        <input type="hidden" name="divId" id="divId">
        <input type="hidden" name="counter" id="counter">
        <input type="hidden" name="secondaryValues" id="secondaryValues">
        <input type="hidden" name="formIdentity" id="formIdentity" value="frm_history_physical">			
        <input type="hidden" name="selected_frame_name" id="selected_frame_name_id"  value="">			
        <input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
        <input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
        <input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
        <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
        <input type="hidden" name="patient_in_waiting_id" id="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
        <input type="hidden" name="history_physical_id" id="history_physical_id" value="<?php echo $history_physical_id; ?>">
        <input type="hidden" name="getText" id="getText">
        <input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
        <input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
        <input type="hidden" name="frmAction" id="frmAction" value="iolink_pre_op_health_quest.php">
        <input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
        <input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
        <input type="hidden" name="preDefineDivOpenClose" id="preDefineDivOpenClose" value="">
	    	<input type="hidden" id="ques_count" name="ques_count" value="<?php echo count($getAddQuestions); ?>" />
	    	<input type="hidden" id="version_num" name="version_num" value="<?php echo $chart_version_num; ?>" />
	    	
	    	<table class="table_collapse alignCenter" style="border:none;" onDblClick="closePreDefineDiv()" onClick="calCloseFun();preCloseFun('evaluationPreDefineDiv');preCloseFun('evaluationPreDefineMedDiv');" onMouseOver="">
            <tr>
                <td class="valignTop alignCenter">
                    <table class="valignTop" style="width:250px; padding:0px;  border:none; border-collapse:collapse; display:inline-block;">
                        <tr>
                            <td class="valignTop" style=" padding:0px; text-align:right;" ><img src="images/left.gif" alt="" style="width:3px; height:26px; border:none; padding:0px;"></td>
                            <td class="text_10b nowrap alignCenter valignMiddle" style="background-color:#BCD2B0;padding:0px;">History &amp; Physical Clearance</td>
                            <td class="alignLeft valignTop" style=" padding:0px;"><img src="images/right.gif" alt="" style="width:3px; height:26px; border:none;padding:0px;"></td>
                            <td>&nbsp;</td>
                        </tr>
                  </table>
                </td>
            </tr>
            <tr>
                <td class="alignLeft" >
                    <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
                        <?php 
                          $bgCol = '#BCD2B0';
                          $borderCol = '#BCD2B0';
                        	include('saveDivPopUp.php'); 
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="background-color:#ECF1EA;">
                    <table class="all_border alignCenter" style="border:none; width:99%; border-collapse:collapse; background-color:#FFFFFF;">
                        <tr>
                            <td style="padding-left:4px;">&nbsp;</td>
                            <td class="valignTop" style="width:510px;">
                                <table class="table_collapse ">
                                    <tr style="background-color:#D1E0C9;">
                                        <td class="text_10b" style="height:22px; padding-left:8px;"></td>
                                        <td class="text_10b alignLeft" style="height:20px;">Yes</td>
                                        <td class="text_10b alignLeft" style="height:20px; width:7%;">&nbsp;No</td>
                                        <td class="text_10 alignLeft pad_top_bottom"></td>
                                    </tr>
                                    <?php echo $HPQuesHTML_L; ?>
                               	</table>	
                            </td>
                            <td style="padding-left:2px;"></td>
                            <td class="valignTop" style="width:470px;">
                            	<table class="table_collapse ">
                                    <tr style="background-color:#D1E0C9;">
                                        <td class="text_10b" style="height:22px; padding-left:8px;"></td>
                                        <td class="text_10b alignLeft" style="height:20px;">Yes</td>
                                        <td class="text_10b alignLeft" style="height:20px; width:7%;">&nbsp;No</td>
                                        <td class="text_10 alignLeft pad_top_bottom"></td>
                                    </tr>
                                    <?php echo $HPQuesHTML_R; ?>
                                    <?php echo $HPCustomQuesHTML; ?>
                               	</table>
                               		
                            </td>
                            <td style="padding-left:4px;"></td>
                        </tr>
                    </table>
                    
                </td>
            </tr>
			</table>	     
  	</form>
	<!-- WHEN CLICK ON CANCEL BUTTON -->
	
    <form name="frm_return_BlankMainForm" method="post" action="history_physical_clearance.php?cancelRecord=true<?php echo $saveLink;?>">
			<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
			<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
			<input type="hidden" name="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
		</form>
	<!-- END WHEN CLICK ON CANCEL BUTTON -->	
<?php
if($SaveForm_alert == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}
?>
<script>
		if(top.document.getElementById("anchorShow")) {
			top.document.getElementById("anchorShow").style.display = 'block';
		}
		if(top.document.getElementById("deleteSelected")) {
			top.document.getElementById("deleteSelected").style.display = 'none';
		}
		if(top.document.getElementById("PrintBtn")) {
			top.document.getElementById("PrintBtn").style.display = 'none';
		}
		if(top.document.getElementById("iolinkUploadBtn")) {
			top.document.getElementById("iolinkUploadBtn").style.display = 'none';
		}
		if(top.document.getElementById("multiUploadImgBtn")) {
			top.document.getElementById("multiUploadImgBtn").style.display = 'none';
		}
</script>
</body>
</html>