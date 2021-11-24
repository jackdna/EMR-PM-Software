<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php require_once('common/conDb.php'); 
//$ascID = $_SESSION['ascId'];
$pConfId = $_SESSION["pConfId"]; 
//$query_rsNotes = "SELECT tblprogress_report.txtNote, tblprogress_report.tTime, users.fname, users.user_type FROM tblprogress_report, users WHERE tblprogress_report.asc_id = '$ascID' AND tblprogress_report.confirmation_id = '$pConfId'  AND  users.usersId = tblprogress_report.usersId ORDER BY dtDateTime, tTime DESC";
$query_rsNotes = "SELECT * from tblprogress_report where confirmation_id = '$pConfId' ORDER BY dtDateTime DESC, tTime DESC"; 
$rsNotes = imw_query($query_rsNotes, $link) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
//$row_rsNotes = imw_fetch_assoc($rsNotes);   
if($totalRows_rsNotes>0) { $evaluationProgressTblHeight = 120;} else { $evaluationProgressTblHeight = 40; }?>
<div id="evaluationProgressDiv" onclick="closeDiv();" style="position:absolute;background-color:#E0E0E0;width:430px; height:<?php echo $evaluationProgressTblHeight;?>;display:none; overflow:auto;">
<?php if($totalRows_rsNotes>0) { ?>
	<table border="0" cellpadding="0"  cellspacing="0" width="100%" >
		<?php
		while($row_rsNotes = imw_fetch_assoc($rsNotes)) {
			$query_rsUsersNotes = "SELECT * from `users` where usersId = '".$row_rsNotes["usersId"]."' "; 
			$rsUsersNotes = imw_query($query_rsUsersNotes) or die(imw_error());
			$row_rsUsersNotes = imw_fetch_assoc($rsUsersNotes);
 				
				$datestring= $row_rsNotes['dtDateTime']; 
				$d=explode("-",$datestring);
						
					$ShowProgressNotesTime = $row_rsNotes['tTime'];
					//CODE TO SET $ShowProgressNotesTime 
						if($ShowProgressNotesTime=="00:00:00" || $ShowProgressNotesTime=="") {
							
						$ShowProgressNotesTime=date("h:i A");
						}else {
							$ShowProgressNotesTime=$ShowProgressNotesTime;
						}
							
						$time_split_ShowProgressNotesTime = explode(":",$ShowProgressNotesTime);
						if($time_split_ShowProgressNotesTime[0]>=12) {
							$am_pm = "PM";
						}else {
							$am_pm = "AM";
						}
						if($time_split_ShowProgressNotesTime[0]>=13) {
							$time_split_ShowProgressNotesTime[0] = $time_split_ShowProgressNotesTime[0]-12;
							if(strlen($time_split_ShowProgressNotesTime[0]) == 1) {
								$time_split_ShowProgressNotesTime[0] = "0".$time_split_ShowProgressNotesTime[0];
							}
						}else {
							//DO NOTHNING
						}
						$ShowProgressNotesTime = $time_split_ShowProgressNotesTime[0].":".$time_split_ShowProgressNotesTime[1]." ".$am_pm;
					
					//END CODE TO SET ShowProgressNotesTime
		
		?>
			<tr>
				<td align="right"><img src="images/left.gif" width="3" height="24"></td>
				<td align="left" bgcolor="#BCD2B0"  class="text_10"><?php echo $row_rsNotes['userType']; ?></td>
				<td align="left" bgcolor="#BCD2B0" class="text_10b" nowrap><?php echo $row_rsUsersNotes['fname']." ".$row_rsUsersNotes['lname']; ?></td>
				<td align="right" bgcolor="#BCD2B0" class="text_10"><?php echo $d[1]."/".$d[2]."/".$d[0];?></td>
				<td align="right" bgcolor="#BCD2B0" class="text_10"><?php echo $ShowProgressNotesTime; ?></td>
				
				<td align="left" valign="top"><img src="images/right.gif" width="3" height="24"></td>
			</tr>
			<tr>
				<td></td>
				<!-- <td colspan="2" width="100%" style="padding-left:2px;" class="text_10" id="td<?php echo $i;//$seq; ?>" onClick="return getInnerHTMLFn(this, '6',document.getElementById('selected_frame_name_id').value)"><?php echo $row_rsNotes['txtNote'];//"Record No. ".$i.' - '."Secondary Record"; //$getRecordSetRows['name']; ?></td> -->
				<td colspan="4" width="100%" style="padding-left:2px;" class="text_10" id="td<?php echo $i; ?>" ><?php echo stripslashes($row_rsNotes['txtNote']);?></td>
			</tr>
		<?php
		}
		?>
	</table>
			<?php
	} 
?>

</div>
