<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$current_form_version = 2;
include_once("common/commonFunctions.php"); 
$tablename = "post_nurse_alderate";
$title				=	'Post-Op Aldrete Scoring System';

?>
<!DOCTYPE html>
<html>
<head>
<title><?=$title?></title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/webtoolkit.aim.js"></script>
<script src="js/dragresize.js"></script>

<?php
$spec = '</head>
<body onClick="document.getElementById(\'divSaveAlert\').style.display = \'none\'; closeEpost();  return top.frames[0].main_frmInner.hideSliders();">';

include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
extract($_GET);

$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
$patient_id = $_REQUEST['patient_id'];
$pConfId = $_REQUEST['pConfId'];

if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
if(!$patient_id) {$patient_id = $_SESSION['patient_id'];  }

$privileges = $_SESSION['userPrivileges'];
$privilegesArr = explode(', ',$privileges);

$cancelRecord = $_REQUEST['cancelRecord'];


//GETTING PATIENTCONFIRMATION DETAILS

	$confirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	$primary_procedure_id = $confirmationDetails->patient_primary_procedure_id;
	$primary_procedure_name = $confirmationDetails->patient_primary_procedure;
	$secondary_procedure_id = $confirmationDetails->patient_secondary_procedure_id;
	$surgeonId = $confirmationDetails->surgeonId;
	$ascId = $confirmationDetails->ascId;
	
//GETTING PATIENTCONFIRMATION DETAILS

//GET LOGGED IN USER TYPE
	unset($conditionArr);
	$conditionArr['usersId'] = $_SESSION["loginUserId"];
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails){
		foreach($surgeonsDetails as $usersDetail)
		{
			$loggedUserType = $usersDetail->user_type;
		}
	}
//END GET LOGGED IN USER TYPE	

if(!$cancelRecord)
{
			////// FORM SHIFT TO RIGHT SLIDER
			$getLeftLinkDetails 		= $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$alderateScoringForm = $getLeftLinkDetails->post_nurse_alderate_form;	
			if($alderateScoringForm=='true'){
				$formArrayRecord['post_nurse_alderate_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
				
			}
		////// FORM SHIFT TO RIGHT SLIDER
}
elseif($cancelRecord){
		
		$fieldName="post_nurse_alderate_form";
		
		$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";
		
		include("left_link_hide.php");
	
}	

$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&fieldName='.$fieldName;

if($_REQUEST['saveRecord']=='true')
{
		extract($_REQUEST);
		$catCount = isset($_REQUEST['catCount']) ? $_REQUEST['catCount'] : 0;
		$recordCount = isset($_REQUEST['recordCount']) ? $_REQUEST['recordCount'] : 1;
		$del_records = isset($_REQUEST['del_records']) ? $_REQUEST['del_records'] : '';
		$del_records_arr = array_filter(explode(",",$del_records));

		if( is_array($del_records_arr) && count($del_records_arr) > 0 ) {
			// to deleted requested grid 
			foreach($del_records_arr as $key => $scoreId) {
				$arrayRecord = array();	
				$arrayRecord['is_deleted'] = 1;
				$objManageData->updateRecords($arrayRecord,$tablename.'_data','id',$scoreId);
			}
		}
		$form_status = 'completed';
		$currDtTime = date('Y-m-d H:i:s');
		for($in = 0; $in < $recordCount; $in++ ) 
		{	
			$scoreID = isset($_REQUEST['scoreID_'.$in]) ? $_REQUEST['scoreID_'.$in] : 0;
			// to prevent not submitted request
			if( !isset($_REQUEST['scoreID_'.$in]) ) continue;

			// Start savinf data into database
			if( $catCount > 0 ) {
				$updateCounter = 0;
				$pointsDetail = '';
				for ( $i = 1; $i<= $catCount ;  $i++)
				{
					$point = isset($_REQUEST['question_'.$in.'_'.$i]) ? $_REQUEST['question_'.$in.'_'.$i] : ''; 
					
					if(!empty($point))
					{
						$pointsDetail .= ','.$point;
						$updateCounter++;
					}
				}
				$pointsDetail = substr($pointsDetail,1);
				if( $form_status == 'completed' && $updateCounter <> $catCount ) {
					$form_status = 'not completed';
				}

				
				$arrayRecord = array();
				$arrayRecord['points_detail'] = $pointsDetail;
				if( $scoreID ) {
					$arrayRecord['modified_on'] = $currDtTime;
					$arrayRecord['modified_by'] = $_SESSION['loginUserId'];
					$arrayRecord['scoring_comments'] = $_REQUEST['scoring_comments_'.$in];
					$objManageData->updateRecords($arrayRecord,$tablename.'_data','id',$scoreID);
				} 
				else {
					$arrayRecord['confirmation_id'] = $pConfId;
					$arrayRecord['created_on'] = $currDtTime;
					$arrayRecord['created_by'] = $_SESSION['loginUserId'];
					$objManageData->addRecords($arrayRecord,$tablename.'_data');
				}

			}
		}
		
		// update chart status
		$updateRecord = array();

		$chkScoringDetails = $objManageData->getRowRecord($tablename, 'confirmation_id', $pConfId);
		if($chkScoringDetails) {
			$chk_form_status	= $chkScoringDetails->form_status;
			$chk_versionNum		= $chkScoringDetails->version_num;
			$chk_versionDateTime= $chkScoringDetails->version_date_time;
		}
		$version_num = $chk_versionNum;
		if(!$chk_versionNum)
		{
			$version_date_time 	= $chk_versionDateTime;
			if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
			{
				$version_date_time	=	date('Y-m-d H:i:s');
			}
					
			if($chk_form_status == 'completed' || $chk_form_status=='not completed'){
				$version_num 	= 1;
			}else{
				$version_num	= $current_form_version;
			}
			
			$updateRecord['version_num']		=	$version_num;
			$updateRecord['version_date_time']	=	$version_date_time;
			
		}
		
		$updateRecord['form_status']		=	$form_status;	
		$objManageData->updateRecords($updateRecord,$tablename,'confirmation_id',$pConfId);
		
		//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
			if($form_status == "completed" && ($formStatus=="" || $formStatus=="not completed")) {
				echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
			}else if($form_status=="not completed" && ($formStatus==""  || $formStatus=="completed")) {
				echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
			}
		//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
	
}

//Getting Details From table
$scoringDetails		= $objManageData->getRowRecord($tablename, "confirmation_id", $pConfId);
$scoringDetailsArr 	= $objManageData->getArrayRecords($tablename.'_data', "confirmation_id", $pConfId,'created_on','ASC', " AND is_deleted = 0 ");
$formStatus 		= $scoringDetails->form_status ;
$version_num 		= $scoringDetails->version_num;
$version_date_time 	= $scoringDetails->version_date_time;
if(!($version_num) && ($formStatus == 'completed' || $formStatus == 'not completed')) 	{ $version_num	=	1; }
else if(!($version_num) && $formStatus <> 'completed' && $formStatus <> 'not completed'){ $version_num	=	$current_form_version; }

$totalPostRecord= (is_array($scoringDetailsArr) && count($scoringDetailsArr) > 0 ) ? count($scoringDetailsArr) : 1;
// Getting Details From table
$ScoringCategories		=	$objManageData->getArrayRecords('alderate_scoring_categories','','','id','ASC');
$ScoringCatCount		=	count($ScoringCategories);
?>


<div id="post" style="display:none; position:absolute;"></div>
<?php

// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmationFinalize->finalize_status;
// GETTING FINALIZE STATUS


?>
<script type="text/javascript">
	dragresize.apply(document);
</script>

<script>
	$(function(){
		$('body').on('click','li[id^="alderateQuestion_"]',function(){
			
			var ID	= $(this).attr('id');
			var arr	= ID.split("_");
			var Ind = arr[1];
			var Cid	= arr[2];
			var Qid = arr[3];
			var Apt = arr[4];
			
			var pointR	=	'pointReceived_'+Ind+"_"+Cid ; 
			var chkBox	=	'chbx_no_' + Ind + "_" + Qid ;
			//document.getElementById(chkBox).checked=true;
			var chkBoxChecked = document.getElementById(chkBox).checked;
			if(chkBoxChecked==false) {
				Apt = '';	
			}
			var AptVal = '';
			if(Apt!='') {
				AptVal = Apt + ' Points';
			}
			$("#"+pointR + "" ).html(AptVal);
			checkSingle(chkBox,'question_'+Ind+"_"+Cid);
			UpdatePoints(Ind) ;
			
			//$("#" + chkBox + "") .prop('checked', chkBoxChecked);
			
		});
		
		var UpdatePoints	=	function(indx)
		{
			if( typeof indx == 'undefined') return false;
			var indx = parseInt(indx);
			var TP = $("#TotalPoints_"+indx) ;
			var TS = 0;
			
			$('div#grid_'+indx+' input[type="checkbox"]').each(function() {
				if( $(this).is(":checked") && $(this).attr('data-point'))	{
					var P =	parseInt($(this).attr('data-point') );
					TS = TS + P ;
				}
			});
		
			TP.html( TS + ' Points') ; 
			
		};
	});
	
</script>

<form class="wufoo topLabel" enctype="multipart/form-data" action="post_nurse_alderate_record.php?saveRecord=true" name="post_nurse_alderate_form" method="post" style="margin:0px;">	
	
    <input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="frmAction" id="frmAction" value="post_nurse_alderate_record.php">
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">
	<input type="hidden" name="catCount" id="catCount" value="<?=$ScoringCatCount?>">
	<input type="hidden" name="recordCount" id="recordCount" value="<?=$totalPostRecord?>">
	<input type="hidden" name="formStatus" id="formStatus" value="<?=$formStatus?>">
	<input type="hidden" name="go_pageval" value="<?php echo $tablename;?>">
	<input type="hidden" name="del_records" id="del_records" value="">

    
	<!--slider_content-->
    <div class=" scheduler_table_Complete slider_content" >
    	<?php
				$epost_table_name = $tablename;
				include("./epost_list.php");
		?>
        <div id="divSaveAlert" style="position:absolute; left:40%; display:none; z-index:1000; padding:10px; display:none">
			
            <?php include('saveDivPopUp.php'); ?>
        </div>
		<!--<span class="add-btn" title="Add New"><i class="fa fa-plus-circle add-remove-btn" data-action="add"></i></span>-->
			<?PHP
				//START CODE TO SHOW ASSESSMENT POINTS OF SAVED RECORD WITH DATE/TIME AND COMMENTS
				if($version_num > 1 && $formStatus != '') {
					$assessmentPointsArr = array();
					$scoringQuest = $objManageData->getArrayRecords('alderate_scoring_questions', '', '', 'category_id,id', 'ASC' );  	
					if( is_array($scoringQuest) && count($scoringQuest) > 0 ) {
						foreach($scoringQuest as $scoringQuestObj) {
								$assessmentPointsArr[$scoringQuestObj->category_id.'-'.$scoringQuestObj->id] = $scoringQuestObj->assessment_point;
						}
					}
	
					if( is_array($scoringDetailsArr) && count($scoringDetailsArr) > 0 )
					{
					
					?>
						<div class="panel panel-default bg_panel_qint" style="border:none;">
							<div class="row">
								<div class="col-xs-12">
									<div class="scanner_win new_s">
										<h4><span>Summary - Post-Op Aldrete Score</span></h4>
									</div>
								</div>
							</div>
	
							<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
							   <div class="scheduler_table_Complete ">
								  <div class="col-xs-12 full_width ">
									 <div class="row">
										<table class="col-xs-12 padding_0 table-bordered  table-condensed cf  table-striped" style="padding-right:15px !important;">
										   <thead class="cf">
											  <tr>
												 <th class="text-left col-md-1 col-lg-1 col-sm-1 col-xs-1">S.No.</th>
												 <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">Date</th>
												 <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">Time</th>
												 <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">Score</th>
												 <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">Recorded by</th>
												 <th class="text-left col-md-3 col-lg-3 col-sm-3 col-xs-3" colspan="3" style="border-right:none;">Comment</th>
											  </tr>
										   </thead>
										</table>
									 </div>
								  </div>
								  <div class="col-xs-12 max-height-adjust-post full_width ">
									 <div class="row" >
										<table class=" col-xs-12 padding_0 table-bordered  table-condensed cf  table-striped ">
										   <tbody>
											  <?php
												 //print_r($scoringDetailsArr);
												 for( $loop = 0; $loop < $totalPostRecord; $loop++ ) {
													$scoringDtTm 		= $objManageData->getFullDtTmFormat($scoringDetailsArr[$loop]->created_on);
													list($scoringDt, $scoringTm, $scoringAmPm) = explode(' ',$scoringDtTm);
													$totalPointsEarned 	= 0;
													$pointsDtl 			= $scoringDetailsArr[$loop]->points_detail;
													$pointsDtlArr 		= explode(',',$pointsDtl);
													if(count($pointsDtlArr)>0) {
														foreach($pointsDtlArr as $pointsDtlVal) {
															$totalPointsEarned += $assessmentPointsArr[$pointsDtlVal];
														}
													}
													$recordedByUsr 		= getUsrNm($scoringDetailsArr[$loop]->created_by,true);
													$scoringComments	= $scoringDetailsArr[$loop]->scoring_comments;
													
												 ?>
											  <tr id="tbl_row_<?php echo ($loop);?>">
												 <td class="text-left col-md-1 col-lg-1 col-sm-1 col-xs-1"><?php echo ($loop+1);?></td>
												 <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2"><?php echo $scoringDt;?></td>
												 <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2"><?php echo trim($scoringTm.' '.$scoringAmPm);?></td>
												 <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2"><?php echo $totalPointsEarned;?></td>
												 <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2"><?php echo $recordedByUsr;?></td>
												 <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">
													<textarea class="form-control" style="resize:none;" rows="1" name="scoring_comments_<?php echo $loop;?>" id="scoring_comments_<?php echo $loop;?>"><?php echo stripslashes($scoringComments); ?></textarea>
												 </td>
												 <td class="text-left col-md-1 col-lg-1 col-sm-1 col-xs-1" colspan="1">&nbsp;</td>
											  </tr>
											  <?php
												 }
												 ?>
										   </tbody>
										</table>
									 </div>
								  </div>
								  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix padding_0">
									 <br>
									 <div class="clearfix border-dashed margin_adjustment_only"></div>
								  </div>
							   </div>
							</div>
						</div>
					<?php	
					}
				}
				//END CODE TO SHOW ASSESSMENT POINTS OF SAVED RECORD WITH DATE/TIME AND COMMENTS				
				
				$jsTmpQues = array();
				for( $loop = 0; $loop < $totalPostRecord; $loop++ ) 
				{
					if( is_array($ScoringCategories) && count($ScoringCategories) > 0 )
					{
						$TotalPoints = 0;
						$record_details = '';
						$scoreID = $scoringDetailsArr[$loop]->id;
						$pointsDetail = $scoringDetailsArr[$loop]->points_detail;
						$pointsDetailArr = explode(",",$pointsDetail);

						$recorded_at = $objManageData->getFullDtTmFormat($scoringDetailsArr[$loop]->created_on);
						$recorded_by = getUsrNm($scoringDetailsArr[$loop]->created_by,true);
						if( $recorded_by && $recorded_at ) {
							$record_details = "Recorded by <b>".$recorded_by. '</b> on <b>'.$recorded_at."</b>";
						}
						
						$addBtn = '<i title="Add" class="fa fa-plus-circle add-remove-btn" data-action="add"></i>';
						$crossBtn = '<i title="Remove" class="fa fa-times-circle add-remove-btn" data-action="remove" data-index="'.$loop.'"></i>';
						$collapse = '<i class="glyphicon glyphicon-chevron-up pointer" data-toggle="collapse" data-target="#grid_body_'.$loop.'"></i>&nbsp;';

						$btn = $loop > 0 ? $crossBtn : $addBtn;   
						echo '<div class="panel panel-default bg_panel_qint" id="grid_'.$loop.'" style="border:none;">';
						echo '<input type="hidden" name="scoreID_'.$loop.'" id="scoreID_'.$loop.'" value="'.$scoreID.'" />';
						echo '<div class="row"><div class="col-xs-12">';
						//echo '<span class="clickable" data-toggle="collapse" data-target="#grid_'.$loop.'"><i class="glyphicon glyphicon-chevron-up"></i></span>';
						echo '<div class="scanner_win new_s"><h4><span>'.$collapse.'<b class="badge">'.($loop+1).'</b></span>'.$btn.'</h4></div>';
						echo '</div></div><div class="clearfix margin_adjustment_only "></div>';

						echo '<div class="panel-body collapse in" id="grid_body_'.$loop.'">';
							
						foreach($ScoringCategories as $key=>$cats)
						{
							$ScoringQuestions = $objManageData->getArrayRecords('alderate_scoring_questions', 'category_id', $cats->id, 'id', 'ASC' );  	
								
							if( is_array($ScoringQuestions) && count($ScoringQuestions) > 0 )
							{
								$jsTmpQues[$cats->id] = $ScoringQuestions;
				?>
									<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 ">
									
										<div class="panel-heading  ">
													
												<div class="col-md-8 col-sm-9 col-xs-9 col-lg-9 ">
													<h3 class="panel-title rob "> <?php echo $cats->categoryName; ?> </h3>
												</div>
												
												<div class="col-md-4 col-sm-3 col-xs-3 col-lg-3 ">      
													<h3 class="panel-title rob" style="float:none; width:100%; text-align:center;">Point(s) Earned</h3>
												</div>
										</div>
											
										<div class="panel-body " style="border:solid 1px #DDD; ">
											
											<div class="col-md-8 col-sm-9 col-xs-9 col-lg-9 " style="border-right:solid 1px #DDD; ">
												
												<ul class="list-group checked-list-box " >
												<?PHP					
													$pointsEarned	=	'';
													foreach($ScoringQuestions as $key=>$question)
													{
														$rowID		= $loop."_".$question->category_id."_".$question->id.'_'.$question->assessment_point;
														$chkBoxID	= 'chbx_no_'.$loop."_" . $question->id;
														$chkBoxVal	= $question->category_id . '-' . $question->id;
														$chkBoxName = "question_".$loop."_".$question->category_id;
														$txt = $question->question ; 
														if(in_array($chkBoxVal,$pointsDetailArr)) {
															$pointsEarned = $question->assessment_point . ' Point(s)'	;
															$TotalPoints += $question->assessment_point	;		
														}
													
												?>
														<li class="list-group-item full_width"  id="alderateQuestion_<?=$rowID?>"  >
															<input type="checkbox" <?=(in_array($chkBoxVal,$pointsDetailArr) ? 'checked' : '' )?> value="<?=$chkBoxVal?>" name="<?=$chkBoxName?>" id="<?=$chkBoxID?>" data-point="<?=$question->assessment_point?>"  />
															&nbsp;<label for="<?=$chkBoxID?>"><?=$txt?></label>
														</li>
												<?PHP
													}
												?>
												</ul>
											</div>
											<div class=" col-md-4 col-sm-3 col-xs-3 col-lg-3  "  style="text-align:center; height:inherit "  id="pointReceived_<?=$loop?>_<?=$cats->id?>"><?=$pointsEarned?></div>
													
										</div>
				
									</div>
			<?PHP									
								}
						}
			?>
						<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6" style="margin-top:65px;">
							<div class="panel-heading ">
								<div class="col-md-8 col-sm-9 col-xs-9 col-lg-9 "><h3 class="panel-title rob"> Total Point(s) Earned </h3></div>
								<div class="col-md-4 col-sm-3 col-xs-3 col-lg-3 "><h3 class="panel-title rob" style="float:none !important; text-align:center; width:100%" id="TotalPoints_<?=$loop?>"><?=($TotalPoints > 0 ? $TotalPoints.' Point(s)' : '-' )?></h3></div>
							</div>
							<div class="panel-body ">
								<?php echo $record_details; ?>
							</div>
						</div>
						
						<div class="clearfix margin_adjustment_only"></div>
						
			<?PHP	
						echo '</div></div>';		
					}
					
				}
			?>
		
		</div>
        
</form>

<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" method="post" action="post_nurse_alderate_record.php?cancelRecord=true">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->	

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "post_nurse_alderate_record.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM
if($finalizeStatus !='true' ){
	?>
	<script>
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();	

		$(function(){
		$("body").on('click','.add-remove-btn',function(){
			var action = $(this).data('action');
			var index = parseInt($("#recordCount").val());
			if( action == 'remove' )
				index = $(this).data('index');
			
			if( action == 'add') 
			{ 
				var h = quesHTML(index);
				$(".slider_content").append(h);
				$("#recordCount").val((index+1));
			}
			else if( action == 'remove' ) {
				//alert($("div[id^=grid_]").length);
				if( $("form[name=post_nurse_alderate_form] div[id^=grid_]").length <= 2 ){
					alert('Not allowed...!!! Atleast one scoring grid required');
					return false;
				}

				if( confirm('Are you sure to delete ?') ) {
					removeGRID(index);
				}
				else return false;
			}
			
			return false;

		});

		var removeGRID = function(index){
			if( $("#scoreID_"+index).val() > 0 ) {
					var dr = $("#del_records").val();
					dr += $("#scoreID_"+index).val()+',';
					$("#del_records").val(dr);
			}
			else {
				var rc = parseInt($("#recordCount").val());
				if((rc-1) === index ) $("#recordCount").val((rc-1));
			}
			$("#grid_"+index).remove();
			$("#tbl_row_"+index).remove();
		}
	});

	// Start code to recod multiple post op aldrete section
	var categories = <?php echo json_encode($ScoringCategories);?>;
	var questions = <?php echo json_encode($jsTmpQues);?>;

	function quesHTML(index){

		if( typeof index === 'undefined' ) return false;
		index = parseInt(index);

		var html = '';
		var $_totalPoints = 0 ;

		if( categories.length > 0 ) {

			var crossBtn = '<i class="fa fa-times-circle add-remove-btn" data-action="remove" data-index="'+index+'"></i>';
			var collapse = '<i class="glyphicon glyphicon-chevron-up pointer" data-toggle="collapse" data-target="#grid_body_'+index+'"></i>&nbsp;';
			html += '<div class="panel panel-default bg_panel_qint" id="grid_'+index+'" style="border:none;">';
			html += '<input type="hidden" name="scoreID_'+index+'" id="scoreID_'+index+'" value="0" />';
			html += '<div class="row"><div class="col-xs-12">';
			html += '<div class="scanner_win new_s"><h4><span>'+collapse+'<b class="badge">'+(index+1)+'</b></span>'+crossBtn+'</h4></div>';
			html += '</div></div><div class="clearfix margin_adjustment_only "></div>';
			
			html += '<div class="panel-body collapse in" id="grid_body_'+index+'">';
			for( var i  in categories) {
				var curCatArr = categories[i];
				var tmpQues = questions[curCatArr.id];
				if( tmpQues.length > 0 )
				{
					html += '<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 ">';
					// Start Header
					html += '<div class="panel-heading  ">';
					html += '<div class="col-md-8 col-sm-9 col-xs-9 col-lg-9 ">';
					html += '<h3 class="panel-title rob ">'+curCatArr.categoryName+'</h3>';
					html += '</div>';
					html += '<div class="col-md-4 col-sm-3 col-xs-3 col-lg-3 ">';
					html += '<h3 class="panel-title rob" style="float:none; width:100%; text-align:center;">Point(s) Earned</h3>';
					html += '</div>';
					html += '</div>';
					// End header 

					// Start Body
					html += '<div class="panel-body " style="border:solid 1px #DDD; ">';
					html += '<div class="col-md-8 col-sm-9 col-xs-9 col-lg-9 " style="border-right:solid 1px #DDD; ">';
					html += '<ul class="list-group checked-list-box " >';

					var $_pointsEarned = '';
					for ( var k in tmpQues )
					{
						var curQuesArr = tmpQues[k];
						var $rowID = index+"_"+curQuesArr.category_id + "_" + curQuesArr.id + '_' + curQuesArr.assessment_point;
						var $chkBoxID =	'chbx_no_' + index +"_"+ curQuesArr.id;
						var $chkBoxVal = curQuesArr.category_id + '-' + curQuesArr.id;
						var $chkBoxName = "question_"+index+"_"+curQuesArr.category_id;
						var $txt = curQuesArr.question; 
						
						$_totalPoints	=	0;		
						
						html += '<li class="list-group-item full_width"  id="alderateQuestion_'+$rowID+'"  >';
						html += '<input type="checkbox" value="'+$chkBoxVal+'" name="'+$chkBoxName+'" id="'+$chkBoxID+'" data-point="'+curQuesArr.assessment_point+'"  />';
						html += '&nbsp;<label for="'+$chkBoxID+'">&nbsp;'+$txt+'</label>';
						html += '</li>';
					}

					html += '</ul>';
					html += '</div>';
					
					html += '<div class=" col-md-4 col-sm-3 col-xs-3 col-lg-3  "  style="text-align:center; height:inherit "  id="pointReceived_'+index+"_"+curCatArr.id+'">'+$_pointsEarned+'</div>';

					html += '</div>';
					// End panel body

					html += '</div>';

				}
			}

			html += '<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6" style="margin-top:65px;">';
			html += '<div class="panel-heading ">';
			html += '<div class="col-md-8 col-sm-9 col-xs-9 col-lg-9 "><h3 class="panel-title rob"> Total Point(s) Earned </h3></div>';
			html += '<div class="col-md-4 col-sm-3 col-xs-3 col-lg-3 "><h3 class="panel-title rob" style="float:none !important; text-align:center; width:100%" id="TotalPoints_'+index+'">'+($_totalPoints > 0  ? $_totalPoints+' Point(s)' : '-' )+'</h3></div>';
			html += '</div>';
			html += '</div>';

			html += '<div class="clearfix margin_adjustment_only"></div>';
			html += '</div>';
			html += '</div>';
		}
		
		return html;

	}
	// End code to recod multiple post op aldrete section
	</script>
	<?php
}else{
	?>
	<script>
		top.frames[0].setPNotesHeight();		
		top.document.getElementById('footer_button_id').style.display = 'none';
	</script>
	<?php
}


?>
<?php
if($finalizeStatus !='true' ){
	include('privilege_buttons.php');
}
include("print_page.php");
?>
</body>
</html>