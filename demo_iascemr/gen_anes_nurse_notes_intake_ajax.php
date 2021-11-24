<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("common/commonFunctions.php");
		
	//SAVE NEW INTAKE
			$ajax_intake_fluids = $_GET['txt_intake_fluids'];
			$ajax_intake_amount_given = $_GET['txt_intake_amount_given'];

	  			
			
			
			
			$SaveIntakeAjaxQry = "insert into `gen_nursenotes_intake_room` set 
										intake_fluids = '$ajax_intake_fluids',
										intake_amount_given = '$ajax_intake_amount_given', 
										confirmation_id='".$_REQUEST["pConfId"]."',
										patient_id = '".$_REQUEST["patient_id"]."'";
										
			$SaveIntakeAjaxRes = imw_query($SaveIntakeAjaxQry) or die(imw_error());
	//END SAVE NEW INTAKE
		
	?>
	<!-- <td colspan="5" id="intakeRoom_id"> -->
									  	
		<?php
			$intakeRoomNotesQry = "select * from `gen_nursenotes_intake_room` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
			$intakeRoomNotesRes = imw_query($intakeRoomNotesQry) or die(imw_error()); 
			$intakeRoomNotesNumRow = imw_num_rows($intakeRoomNotesRes);
			if($intakeRoomNotesNumRow>0) {
				while($intakeRoomNotesRow = imw_fetch_array($intakeRoomNotesRes)) {
				
					$intake_fluids = $intakeRoomNotesRow["intake_fluids"];
					$intake_amount_given = $intakeRoomNotesRow["intake_amount_given"];
			 ?> 
		
			<table class="tableCollapse text_10">
				<tr>
				  
				  <td style="border-color:<?php echo $border_blue_local_anes; ?>; width:375px; height:24px;" class="right_border bottom_border valignTop">&nbsp;<?php echo $intake_fluids;?></td>
				  <td style="border-color:<?php echo $border_blue_local_anes; ?>; width:169px; height:24px;" class="right_border bottom_border valignTop">&nbsp;<?php echo $intake_amount_given;?></td>
				  <td style="border-color:<?php echo $border_blue_local_anes; ?>; width:92px; height:24px;" class="right_border bottom_border valignTop"></td>
				  <td style="border-color:<?php echo $border_blue_local_anes; ?>; width:150px; height:24px; " class="bottom_border pad valignTop"></td>
				</tr>
			</table>
		<?php
				}
			}	
			
		?>								    
 <!-- </td> -->
