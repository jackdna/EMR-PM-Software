<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("common/commonFunctions.php");

include("common/linkfile.php");
?>
<table	width="100%" border="0" cellspacing="0" cellpadding="0" id="patient_search_results" style="display:block;">
			<tr bgcolor="#B4C8AC">
				<td height="23" colspan="7" align="left" valign="middle" bgcolor="#FFFFFF" style="font-weight:bold; "><img src="images/tpixel.gif" width="5" height="1">Patient Search Results</td>
			</tr>
			<?php 
			//CODE FOR STUB DATABASE VALUE
				
				$stub_tbl_group_query = "select * from stub_tbl where dos = '$selected_date' group by surgeon_name "; 
				$stub_tbl_group_res = imw_query($stub_tbl_group_query) or die(imw_error());
				
				while($stub_tbl_group_row = imw_fetch_array($stub_tbl_group_res)) {
				
					$stub_tbl_group_surgeon_name = $stub_tbl_group_row['surgeon_name'];
				
			
			?>
		  <tr><td height="1"></td></tr>
			<tr bgcolor="#ECF1EA">
				<td height="23" colspan="7" align="left" valign="middle" class="text_homeb"  style="border-bottom:1px solid #B4C8AC;border-top:1px solid #B4C8AC;"><img src="images/tpixel.gif" width="5" height="1"><?php echo $stub_tbl_group_surgeon_name;?></td>
		  </tr>
		  <?php
					$stub_tbl_query = "select * from stub_tbl where surgeon_name = '".$stub_tbl_group_surgeon_name."' and dos = '$selected_date' order by surgery_time"; 
					$stub_tbl_res = imw_query($stub_tbl_query) or die(imw_error());
					$incr_stub = 0;
					while($stub_tbl_row = imw_fetch_array($stub_tbl_res)) {
						$incr_stub++;
						
						//CODE TO SET THE TIME
						$stub_tbl_surgery_time = $stub_tbl_row['surgery_time'];
						$time_split = explode(":",$stub_tbl_surgery_time);
						if($time_split[0]>=12) {
							$am_pm = "PM";
						}else {
							$am_pm = "AM";
						}
						if($time_split[0]>=13) {
							$time_split[0] = $time_split[0]-12;
							if(strlen($time_split[0]) == 1) {
								$time_split[0] = "0".$time_split[0];
							}
						}else {
							//DO NOTHNING
						}
						$stub_tbl_time = $time_split[0].":".$time_split[1]." ".$am_pm;
						//CODE TO SET THE TIME									
						
						$stub_tbl_stub_id = $stub_tbl_row['stub_id'];
						$stub_tbl_patient_first_name = $stub_tbl_row['patient_first_name'];
						$stub_tbl_patient_middle_name = $stub_tbl_row['patient_middle_name'];
						$stub_tbl_patient_last_name = $stub_tbl_row['patient_last_name'];
						$stub_tbl_patient_status = $stub_tbl_row['patient_status'];
						$stub_tbl_patient_name = $stub_tbl_patient_first_name." ".$stub_tbl_patient_middle_name." ".$stub_tbl_patient_last_name;
						
						
						$stub_tbl_patient_primary_procedure = $stub_tbl_row['patient_primary_procedure'];
						$stub_tbl_patient_secondary_procedure = $stub_tbl_row['patient_secondary_procedure'];
		  
		  ?>
			<tr align="left" valign="middle"  bgcolor="#FFFFFF">
				<td width="9%" height="23" nowrap class="text_homeb" >&nbsp;</td>
				<td width="3%" height="23" ><?php echo $incr_stub;?>.</td>
				<td width="13%" height="23" ><img src="images/tpixel.gif" width="5" height="1"><?php echo $stub_tbl_patient_status;?></td>
			  <td width="16%" height="23" ><div style="padding-top:2px; padding-bottom:2px; ">    <?php echo $stub_tbl_time;?>      </div></td>
				<td nowrap="nowrap" height="23"><a href="patient_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>" target="_top" class="link_home"><?php echo $stub_tbl_patient_name;?></a></td>
				<td  height="23" nowrap >&nbsp;&nbsp;<?php echo $stub_tbl_patient_primary_procedure;?></td>
				<td width="10%" height="23" >&nbsp;</td>
		  </tr>
		  <?php 
					}
					
				}	
			//END CODE FOR STUB DATABASE VALUE
	
		  ?>
					 
	 </table>