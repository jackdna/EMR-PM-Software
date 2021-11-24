<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include("common_functions.php");
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
// allergies_status_reviewed(table field t be added)
//$patient_id = $_REQUEST['patient_id'];
//$ascId = $_REQUEST['ascId'];
$pConfId = $_REQUEST['pConfId'];
include_once("new_header_print.php");
$tableAmdNote="";
$tableAmdNote.=$head_table."<br>";
$tableAmdNote.='
			<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
				<tr>	
					<td colspan="5" style="width:740px;" class="fheader">Amendments</td>
				</tr>
				<tr>
					<td style="width:240px; height:20px;" class="bold pl5  bgcolor">Amendment Notes</td>
					<td style="width:100px; height:20px;" class="bold pl5  bgcolor">Who</td>
					<td style="width:140px; height:20px;" class="bold pl5  bgcolor">Created by</td>
					<td style="width:100px; height:20px;" class="bold pl5  bgcolor">Date</td>
					<td style="width:100px; height:20px;" class="bold pl5  bgcolor">Time</td>
				</tr>
			
';	
			
			
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
		$getUserTypeLabel = ($getUserType == 'Anesthesiologist') ? 'Anesthesia Provider' : $getUserType ;
		
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
			/*
			$time_split2 = explode(":",$timeAmendment);
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
			
			
				$tableAmdNote.='
					<tr>
						<td style="width:240px; " class="bold pl5 bdrbtm">'.stripslashes($amendmentNotes).'</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">'.$getUserTypeLabel.'</td>
						<td style="width:140px; " class="bold pl5 bdrbtm">'.stripslashes($getUserName).'</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">'.$dateAmendment.'</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">'.$timeAmendment.'</td>
				</tr>';
			}
		}else{
			$tableAmdNote.='
					<tr>
						<td style="width:240px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:140px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
					</tr>
					<tr>
						<td style="width:240px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:140px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
					</tr>
					<tr>
						<td style="width:240px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:140px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
					</tr>
					<tr>
						<td style="width:240px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:140px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
						<td style="width:100px; " class="bold pl5 bdrbtm">&nbsp;</td>
					</tr>';
		}	
$tableAmdNote.='</table>';	
   
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$tableAmdNote);
fclose($fileOpen);
//$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';
/*echo"<script>window.open('testPdf.html?pConfId=','','')</script>";*/
?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
	</tr>
</table>

<body>
<form name="printFrm"  action="new_html2pdf/createPdf.php?op=p" method="post">
</form>		
<script type="text/javascript">
	submitfn();
</script>
</body>