<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php"); 
include("common_functions.php");
include("common/commonFunctions.php");
$objManageData = new manageData;
$pConfId= $_REQUEST['pConfId'];

if(!$pConfId) {
	$pConfId= $_SESSION['pConfId'];
}	
include_once("new_header_print.php");
//echo $patient_id = $_SESSION['patient_id'];

//Getting Details From table
$scoringDetails	=	$objManageData->getRowRecord("post_nurse_alderate", "confirmation_id", $pConfId);
$scoringDetailsArr = $objManageData->getArrayRecords("post_nurse_alderate_data", "confirmation_id", $pConfId, 'created_on','ASC', ' AND is_deleted = 0 ');

if( $scoringDetails )
{
	
	//$scoreID			=	$scoringDetails->id;
	//$pointsDetail	=	$scoringDetails->points_detail ;
	$formStatus 	=	$scoringDetails->form_status ;
	$version_num 	=	$scoringDetails->version_num ;
	//$pointsDetailArr	=	explode(",",$pointsDetail) ;
	
}
// Getting Details From table

$tableDSummery.=$head_table."<br>";

//START CODE TO SHOW ASSESSMENT POINTS OF SAVED RECORD WITH DATE/TIME AND COMMENTS
$hSmry = '';
if($version_num > 1) {
	$assessmentPointsArr = array();
	$scoringQuest = $objManageData->getArrayRecords('alderate_scoring_questions', '', '', 'category_id,id', 'ASC' );  	
	if( is_array($scoringQuest) && count($scoringQuest) > 0 ) {
		foreach($scoringQuest as $scoringQuestObj) {
				$assessmentPointsArr[$scoringQuestObj->category_id.'-'.$scoringQuestObj->id] = $scoringQuestObj->assessment_point;
		}
	}
	
	if( is_array($scoringDetailsArr) && count($scoringDetailsArr) > 0 )
	{
		$hSmry.='<table style="width:710px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">';
		$hSmry.='	<tr>
						<td colspan="6" style="width:700px;" class="fheader">Post-Op Aldrete Scoring System</td>
					</tr>
					<tr>
						<th class="bdrbtm bold" colspan="6" height="30" valign="middle" style="background:#333;color:#FFF; padding-left:10px;width:710px;">Summary - Post-Op Aldrete Score</th>
					</tr>
					<tr >
						<th class="bdrbtm bgcolor bold" width="80" height="30" valign="middle" align="center">S. No.</th>
						<th class="bdrbtm bgcolor bold" width="100" valign="middle">Date</th>
						<th class="bdrbtm bgcolor bold" width="100" valign="middle">Time</th>
						<th class="bdrbtm bgcolor bold" width="80"  valign="middle">Score</th>
						<th class="bdrbtm bgcolor bold" width="160" valign="middle">Recorded By</th>
						<th class="bdrbtm bgcolor bold" width="180" valign="middle">Comments</th>
					</tr>';
		$c=0;
		foreach( $scoringDetailsArr as $h_data ) {
			$c++;
			$scoringDtTm 		= $objManageData->getFullDtTmFormat($h_data->created_on);
			list($scoringDt, $scoringTm, $scoringAmPm) = explode(' ',$scoringDtTm);
			$totalPointsEarned 	= 0;
			$pointsDtl 			= $h_data->points_detail;
			$pointsDtlArr 		= explode(',',$pointsDtl);
			if(count($pointsDtlArr)>0) {
				foreach($pointsDtlArr as $pointsDtlVal) {
					$totalPointsEarned += $assessmentPointsArr[$pointsDtlVal];
				}
			}
			$recordedByUsr 		= getUsrNm($h_data->created_by,true);
			$scoringComments 	= stripslashes($h_data->scoring_comments);
	
			$hSmry.='<tr >
						<td class="bdrbtm" width="80" height="30" valign="middle" align="center">'.$c.'</td>
						<td class="bdrbtm" width="100" valign="middle">'.$scoringDt.'</td>
						<td class="bdrbtm" width="100" valign="middle">'.trim($scoringTm.' '.$scoringAmPm).'</td>
						<td class="bdrbtm" width="80"  valign="middle">'.$totalPointsEarned.'</td>
						<td class="bdrbtm" width="160" valign="middle">'.$recordedByUsr.'</td>
						<td class="bdrbtm" width="180" valign="middle">'.$scoringComments.'</td>
					</tr>';
	
		}
		$hSmry.='</table>';
	}
	if(trim($hSmry)) {
		$tableDSummery.=$hSmry."<br>";
	}
}
//END CODE TO SHOW ASSESSMENT POINTS OF SAVED RECORD WITH DATE/TIME AND COMMENTS

$tableDSummery.='
		<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
			';
if(!$hSmry) {
	$tableDSummery.='			
			<tr>
				<td colspan="4" style="width:740px;" class="fheader">Post-Op Aldrete Scoring System</td>
			</tr>';
}
$ScoringCategories = $objManageData->getArrayRecords('alderate_scoring_categories','','','id','ASC');
if( is_array($scoringDetailsArr) && count($scoringDetailsArr) > 0 ) {
	$cntr = 0;
	foreach( $scoringDetailsArr as $data ) {

		$cntr++;
		$pointsDetail = $data->points_detail;
		$pointsDetailArr = explode(",",$pointsDetail);

		$record_details = '';
		$recorded_at = $objManageData->getFullDtTmFormat($data->created_on);
		$recorded_by = getUsrNm($data->created_by,true);
		if( $recorded_by && $recorded_at ) {
			$record_details = "Recorded by <b>".$recorded_by. '</b> on <b>'.$recorded_at."</b>&nbsp;";
		}

		$tableDSummery.='
			<tr>
				<th class="bdrbtm bold" width="80" height="30" align="center" valign="middle" style="background:#333;color:#FFF;">'.$cntr.'</th>
				<th class="bdrbtm bold" width="650" colspan="3" height="30" style="text-align:right;background:#333;color:#FFF;" valign="middle">'.$record_details.'</th>
			</tr>
			<tr >
					<th class="bdrbtm bgcolor bold" width="80" height="30" align="center" valign="middle">S. No.</th>
					<th class="bdrbtm bgcolor bold" width="120" valign="middle">Category</th>
					<th class="bdrbtm bgcolor bold" width="380" valign="middle">Comment</th>
					<th class="bdrbtm bgcolor bold" width="150" align="center" valign="middle">Point(s) Earned</th>
			</tr>
			';
		
			if( is_array($ScoringCategories) && count($ScoringCategories) > 0 )
			{
				
				$TotalPoints		=	0	;
				$counter			=	0;
				foreach($ScoringCategories as $key=>$cats)
				{
						$tableDSummery.='
								<tr >
									<td class="bdrbtm " height="25" align="center" valign="middle">'.(++$counter).'</td>
									<td class="bdrbtm " valign="middle">'. $cats->categoryName .'</td>
									';
									
						
						$ScoringQuestions	=		$objManageData->getArrayRecords('alderate_scoring_questions', 'category_id', $cats->id, 'id', 'ASC' );  	
						
						if( is_array($ScoringQuestions) && count($ScoringQuestions) > 0 )
						{
								$points	=	'' ;
								$txt			=	'' ;
								foreach($ScoringQuestions as $key=>$question)
								{
										$val	=	$question->category_id . '-' . $question->id;
										
										if(in_array($val,$pointsDetailArr))
										{
												$points		=	$question->assessment_point . ' Point(s)'	;
												$TotalPoints	+=	$question->assessment_point	;	
												$txt				=	$question->question ; 	
										}
										
								}
								
								$tableDSummery.='
												<td class="bdrbtm " valign="middle">'.$txt.'</td>
												<td class="bdrbtm " align="center" valign="middle">'.$points.'</td>
											';
											
						}
						else
						{
							
								$tableDSummery.='
												<td class="bdrbtm ">&nbsp;</td>
												<td class="bdrbtm " >&nbsp;</td>
											';
											
						}
											
						$tableDSummery.='</tr>';
						
				}
		
			}
		
		$tableDSummery.='			
			<tr >
					<th colspan="3" align="right">Total Point(s) Earned</th>
					<th height="40" align="center" valign="middle" >'.$TotalPoints.' Point(s)</th>
			</tr>';
	}
}
$tableDSummery.='</table>';

$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$tableDSummery);
fclose($fileOpen);
$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';


if($formStatus=='completed' || $formStatus=='not completed') {
?>	

 <form name="printAlderateScoringSystem" action="new_html2pdf/createPdf.php?op=p" method="post">
 </form>

<script language="javascript">
	function submitfn(){
		document.printAlderateScoringSystem.submit();
	}
</script>

<script type="text/javascript">
	submitfn();
</script>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>	

