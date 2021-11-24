<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("common/commonFunctions.php");
		
	//SAVE NEW NOTES TIME AND DESCRIPTION
			$ajax_newNotesTime = date("H:i:s");
			$ajax_recovery_drugs = $_GET['txt_recovery_drugs'];
			$ajax_recovery_dose = $_GET['txt_recovery_dose'];
			$ajax_recovery_route = $_GET['txt_recovery_route'];
			$ajax_recovery_time = $_GET['txt_recovery_time'];
			$ajax_recovery_initial = $_GET['txt_recovery_initial'];
			
		//recovery_time saved in database
	 
	       $time_splitrecovery = explode(" ",$ajax_recovery_time);
	       
			if($time_splitrecovery[1]=="PM" || $time_splitrecovery[1]=="pm") {
				
				$time_splitrecoverytime = explode(":",$time_splitrecovery[0]);
				$ajax_recovery_timeIncr=$time_splitrecoverytime[0]+12;
				$ajax_recovery_time = $ajax_recovery_timeIncr.":".$time_splitrecoverytime[1].":00";
				
			}elseif($time_splitrecovery[1]=="AM" || $time_splitrecovery[1]=="am") {
				$time_splitrecoverytime = explode(":",$time_splitrecovery[0]);
				$ajax_recovery_time=$time_splitrecoverytime[0].":".$time_splitrecoverytime[1].":00";
				
				if($time_splitrecoverytime[0]=="00" && $time_splitrecoverytime[1]=="00") {
					$ajax_recovery_time=$time_splitrecoverytime[0].":".$time_splitrecoverytime[1].":01";
				}
			}
	   //recovery_time saved in database
	  			
			
			
			
			$SaveRecoveryNotesAjaxQry = "insert into `gen_nursenotes_recovery_meds` set 
										recovery_drugs = '$ajax_recovery_drugs',
										recovery_dose = '$ajax_recovery_dose', 
										recovery_route = '$ajax_recovery_route',
										recovery_time = '$ajax_recovery_time',
										recovery_initial = '$ajax_recovery_initial', 
										confirmation_id='".$_REQUEST["pConfId"]."',
										patient_id = '".$_REQUEST["patient_id"]."'";
										
			$SaveRecoveryNotesAjaxRes = imw_query($SaveRecoveryNotesAjaxQry) or die(imw_error());
	//END SAVE NEW NOTES TIME AND DESCRIPTION
		
	?>
	<!-- <td colspan="5" id="recoveryRoom_id"> -->
									  	
		<?php
			$recoveryRoomNotesQry = "select * from `gen_nursenotes_recovery_meds` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
			$recoveryRoomNotesRes = imw_query($recoveryRoomNotesQry) or die(imw_error()); 
			$recoveryRoomNotesNumRow = imw_num_rows($recoveryRoomNotesRes);
			if($recoveryRoomNotesNumRow>0) {
				while($recoveryRoomNotesRow = imw_fetch_array($recoveryRoomNotesRes)) {
				
					$recovery_drugs = $recoveryRoomNotesRow["recovery_drugs"];
					$recovery_dose = $recoveryRoomNotesRow["recovery_dose"];
					$recovery_route = $recoveryRoomNotesRow["recovery_route"];
					$recovery_timeTemp = $recoveryRoomNotesRow["recovery_time"];
					$recovery_initial = $recoveryRoomNotesRow["recovery_initial"];
					
					//CODE TO SET RECOVERY MEDS TIME									
						$recovery_time_split = explode(":",$recovery_timeTemp);
						if($recovery_time_split[0]>=12) {
							$am_pm_recovery = "PM";
						}else {
							$am_pm_recovery = "AM";
						}
						if($recovery_time_split[0]>=13) {
							$recovery_time_split[0] = $recovery_time_split[0]-12;
							if(strlen($recovery_time_split[0]) == 1) {
								$recovery_time_split[0] = "0".$recovery_time_split[0];
							}
						}else {
							//DO NOTHNING
						}
						$recovery_time = $recovery_time_split[0].":".$recovery_time_split[1]." ".$am_pm_recovery;
					//CODE TO SET RECOVERY MEDS TIME									

			 ?> 
		
			<table class="text_10 table_collapse" style="border-color:<?php echo $border_blue_local_anes; ?> ">
				<?php
						$predefinedHeight = 676;
						$predefinedHeightIncr = $recoveryRoomNotesNumRow*24;
						$predefinedHeight = $predefinedHeight+$predefinedHeightIncr;
						
					?>
				<tr>
				  <td style="border-color:<?php echo $border_blue_local_anes; ?>; width:233px; height:24px;" class="right_border bottom_border valignTop">&nbsp;<?php echo $recovery_drugs;?></td>
				  <td style="border-color:<?php echo $border_blue_local_anes; ?>; width:140px; height:24px;" class="right_border bottom_border valignTop">&nbsp;<?php echo $recovery_dose;?></td>
				  <td style="border-color:<?php echo $border_blue_local_anes; ?>; width:169px; height:24px;" class="right_border bottom_border valignTop">&nbsp;<?php echo $recovery_route;?></td>
				  <td style="border-color:<?php echo $border_blue_local_anes; ?>; width:92px; height:24px;" class="right_border bottom_border valignTop nowrap">&nbsp;<?php echo $recovery_time;?></td>
				  <td style="border-color:<?php echo $border_blue_local_anes; ?>; width:150px; height:24px;" class="right_border bottom_border valignTop"><?php echo $recovery_initial;?></td>
				</tr>
			</table>
		<?php
				}
			}	
			
		?>								    
	<!-- </td> -->
