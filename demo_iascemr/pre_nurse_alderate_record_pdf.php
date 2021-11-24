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
$objManageData = new manageData;
$pConfId= $_REQUEST['pConfId'];

if(!$pConfId) {
	$pConfId= $_SESSION['pConfId'];
}	
include_once("new_header_print.php");
//echo $patient_id = $_SESSION['patient_id'];

//Getting Details From table
$scoringDetails	=	$objManageData->getRowRecord("pre_nurse_alderate", "confirmation_id", $pConfId);
if( $scoringDetails )
{
	
	$scoreID			=	$scoringDetails->id;
	$pointsDetail	=	$scoringDetails->points_detail ;
	$formStatus 	=	$scoringDetails->form_status ;
	
	$pointsDetailArr	=	explode(",",$pointsDetail) ;
	
}
// Getting Details From table

$tableDSummery.=$head_table."<br>";

$tableDSummery.='
	<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="4" style="width:740px;" class="fheader">Pre-Op Aldrete Scoring System</td>
		</tr>
		<tr >
				<th class="bdrbtm bgcolor bold" width="80" height="35" align="center" valign="middle">Sr. no.</th>
				<th class="bdrbtm bgcolor bold" width="120" valign="middle">Category</th>
				<th class="bdrbtm bgcolor bold" width="380" valign="middle">Comment</th>
				<th class="bdrbtm bgcolor bold" width="150" align="center" valign="middle">Point(s) Earned</th>
		</tr>
		';
		
		$ScoringCategories		=	$objManageData->getArrayRecords('alderate_scoring_categories','','','id','ASC');
		
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
									$NA	=	$question->category_id . '-NA';
									$val	=	$question->category_id . '-' . $question->id;
									if(in_array($NA,$pointsDetailArr))
									{
											$points	=	'N/A' ;
									}
									elseif(in_array($val,$pointsDetailArr))
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
		</tr>
		
	</table>
	';

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

