<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php"); 
$tablename = "pre_nurse_alderate";
$title				=	'Pre-Op Aldrete Scoring System';

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
			$alderateScoringForm = $getLeftLinkDetails->pre_nurse_alderate_form;	
			if($alderateScoringForm=='true'){
				$formArrayRecord['pre_nurse_alderate_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
				
			}
		////// FORM SHIFT TO RIGHT SLIDER
}
elseif($cancelRecord){
		
		$fieldName="pre_nurse_alderate_form";
		
		$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";
		
		include("left_link_hide.php");
	
}	

$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&fieldName='.$fieldName;



if($_REQUEST['saveRecord']=='true')
{
		extract($_REQUEST);
		
		$catCount		=	isset($_REQUEST['catCount'])	?	$_REQUEST['catCount']	:	0	;
		$pointsDetail	=	'';
		if($catCount > 0 )
		{
			$updateCounter	=	0;
			for ( $i = 1; $i<= $catCount ;  $i++)
			{
				$point	=	isset($_REQUEST['chkBoxNa_' . $i])	?	$_REQUEST['chkBoxNa_' . $i]	:	'' ;
				if(!$point)
				{
					$point	=	isset($_REQUEST['question' . $i])	?	$_REQUEST['question' . $i]	:	'' ; 
				}
				
				
				if(!empty($point))
				{
					$pointsDetail	.=	','.$point	;
					$updateCounter++;
				}
			}
			
			$pointsDetail	=	substr($pointsDetail,1);
		}
		
		
		$form_status		=	($updateCounter == $catCount) ?	'completed' : 'not completed' ;
		
		$updateRecord['points_detail']	=	$pointsDetail	;	
		$updateRecord['form_status']		=	$form_status	;	
		
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
$scoringDetails	=	$objManageData->getRowRecord($tablename, "confirmation_id", $pConfId);
if( $scoringDetails)
{
	
	$scoreID			=	$scoringDetails->id;
	$pointsDetail	=	($pointsDetail && $scoringDetails->points_detail <> $pointsDetail) ? $pointsDetail : $scoringDetails->points_detail;
	$formStatus 	=	$scoringDetails->form_status ;
	
	$pointsDetailArr	=	explode(",",$pointsDetail) ;
	
}
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
		$('li[id^="alderateQuestion_"]').click(function(){
			
			var ID	=	$(this).attr('id');
			var arr	=	ID.split("_");
			var Cid	=	arr[1];
			var Qid=	arr[2];
			var Apt=	arr[3];
			
			var pointR	=	'pointReceived'+Cid ; 
			var chkBox			=	'chbx_no' + Qid ;
			var chkBoxChecked = document.getElementById(chkBox).checked;
			if(chkBoxChecked==false) {
				Apt = '';	
			}
			var AptVal = '';
			if(Apt!='') {
				AptVal = Apt + ' Points';
			}
			$("#"+pointR + "" ).html(AptVal);
			checkSingle(chkBox,'question'+Cid);
			UpdatePoints() ;
			
			//$("#" + chkBox + "") .prop('checked', chkBoxChecked);
			
		});
		
		var UpdatePoints	=	function()
		{
			
			var TP		=	$("#TotalPoints") ;
			var TS		=	0;
			
			$('input[type="checkbox"]').each(function() {
					
					if( $(this).is(":checked") && $(this).attr('data-point'))	{
						var P	=	parseInt($(this).attr('data-point') );
						TS			=	TS + P ;
					}
					
			});
		
			TP.html( TS + ' Points') ; 
			
		};
		
		$('[id^="LabelNa"]').click(function(){
			var obj = $(this).find('input[type="checkbox"]') ;
			var arr	=	obj.attr('id').split("_");
				var n	=	'question'+arr[1];
			if(obj.is(":checked"))
				$('input[name="'+n+'"]').attr('disabled','disabled')
			else
				$('input[name="'+n+'"]').removeAttr('disabled')	
			
		})
		
	});
	
</script>

<form class="wufoo topLabel" enctype="multipart/form-data" action="pre_nurse_alderate_record.php?saveRecord=true" name="pre_nurse_alderate_form" method="post" style="margin:0px;">	
	
    <input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="scoreID" value="<?php echo $scoreID; ?>">	
	<input type="hidden" name="frmAction" id="frmAction" value="pre_nurse_alderate_record.php">
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">
    <input type="hidden" name="catCount" id="catCount" value="<?=$ScoringCatCount?>">
    <input type="hidden" name="formStatus" id="formStatus" value="<?=$formStatus?>">
    <input type="hidden" name="go_pageval" value="<?php echo $tablename;?>">
    
	<!--slider_content-->
    <div class=" scheduler_table_Complete slider_content" >
    	<?php
				$epost_table_name = $tablename;
				include("./epost_list.php");
			?>
            
        <!--<div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_notes">
            <span class="bg_span_qint"><?=$title?></span>
            
            
        </div>	
       --> 
        <div id="divSaveAlert" style="position:absolute; left:40%; display:none; z-index:1000; padding:10px; display:none">
            <?php 
					include('saveDivPopUp.php'); 
			?>
        </div>
        
        <div class="panel panel-default bg_panel_qint" style="border:none;">
        
        	<?PHP
				
				
				if( is_array($ScoringCategories) && count($ScoringCategories) > 0 )
				{
					
					$TotalPoints		=	0	;
					foreach($ScoringCategories as $key=>$cats)
					{
							
							$ScoringQuestions	=		$objManageData->getArrayRecords('alderate_scoring_questions', 'category_id', $cats->id, 'id', 'ASC' );  	
							
							if( is_array($ScoringQuestions) && count($ScoringQuestions) > 0 )
							{
								
			?>
                                    <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 ">
                                    
                                        <div class="panel-heading  ">
                                                
                                                <div class="col-md-8 col-sm-9 col-xs-9 col-lg-9 ">
                                                    <h3 class="panel-title rob " style="width:100%">
                                                    	<?php echo $cats->categoryName; ?>
                                                  	
                                                    <?php if($cats->categoryName == 'Circulation'): ?>
													<span style="margin-left:50%; cursor:pointer;" id="LabelNa_<?=$cats->id?>">
                                                    	<input type="checkbox" name="chkBoxNa_<?=$cats->id?>" id="chkBoxNa_<?=$cats->id?>" value="<?=$cats->id?>-NA" <?=(in_array($cats->id.'-NA',$pointsDetailArr) ? 'checked' : '' )?> />
                                                        <label for ="chkBoxNa_<?=$cats->id?>">&nbsp;N/A</label>
                                                    </span>
                                                    <?php endif; ?>
                                                    </h3>
                                                </div>
                                                
                                                <div class="col-md-4 col-sm-3 col-xs-3 col-lg-3 ">      
                                                    <h3 class="panel-title rob" style="float:none; width:100%; text-align:center;">Point(s) Earned</h3>
                                                </div>
                                                <!--<span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>-->
                                        </div>
                                        
                                        <div class="panel-body " style="border:solid 1px #DDD; ">
                                            
                                            <div class="col-md-8 col-sm-9 col-xs-9 col-lg-9 " style="border-right:solid 1px #DDD; ">
                                                
                                                <ul class="list-group checked-list-box " >
            
            <?PHP					
                                                $pointsEarned	=	'';
                                                foreach($ScoringQuestions as $key=>$question)
                                                {
                                                        $rowID			=	$question->category_id."_".$question->id.'_'.$question->assessment_point;
                                                        $chkBoxID	=	'chbx_no' . $question->id;
                                                        $chkBoxVal	=	$question->category_id . '-' . $question->id;
                                                        $txt				=	$question->question ; 
                                                        if(in_array($chkBoxVal,$pointsDetailArr))
                                                        {
                                                            $pointsEarned	=	$question->assessment_point . ' Point(s)'	;
                                                            $TotalPoints		+=	$question->assessment_point	;		
                                                        }
														
														$isDisable	=	in_array($question->category_id.'-NA',$pointsDetailArr) ? 'disabled' : '' ;
                                                        
            ?>										
                                                        <li class="list-group-item full_width"  id="alderateQuestion_<?=$rowID?>"  >
                                                            
                                                                <input <?=$isDisable?> type="checkbox" <?=(in_array($chkBoxVal,$pointsDetailArr) ? 'checked' : '' )?> value="<?=$chkBoxVal?>" name="question<?=$question->category_id?>" id="<?=$chkBoxID?>" data-point="<?=$question->assessment_point?>"  />
                                                                &nbsp;<label for="<?=$chkBoxID?>"><?=$txt?></label>
                                                        </li>
            
            <?PHP									
                                                }
                                                
            ?>
                                                </ul>
                                            
                                            </div>
                                                    
                                            <div class=" col-md-4 col-sm-3 col-xs-3 col-lg-3  "  style="text-align:center; height:inherit "  id="pointReceived<?=$cats->id?>"><?=$pointsEarned?></div>
                                                
                                        </div>
            
                                    </div>
            <?PHP									
							}
					}
					
				}
			?>
            
            			<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6" style="margin-top:65px;">
                                    
            					<div class="panel-heading ">
                                        
                                        <div class="col-md-8 col-sm-9 col-xs-9 col-lg-9 "><h3 class="panel-title rob"> Total Point(s) Earned </h3></div>
                                        <div class="col-md-4 col-sm-3 col-xs-3 col-lg-3 "><h3 class="panel-title rob" style="float:none !important; text-align:center; width:100%" id="TotalPoints"><?=($TotalPoints > 0  ? $TotalPoints.' Point(s)' : '-' )?></h3></div>
                                        
                        		</div>
                                
                       	</div>
                        
        
        </div>
        
		
		</div>
        
</form>

<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" method="post" action="pre_nurse_alderate_record.php?cancelRecord=true">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->	

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "pre_nurse_alderate_record.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM
if($finalizeStatus !='true' ){
	?>
	<script>
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();	
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