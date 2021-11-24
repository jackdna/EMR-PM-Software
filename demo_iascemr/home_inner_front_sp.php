<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("common/commonFunctions.php");

include("common/linkfile.php");
$sel_month_number = $_GET["selected_month_number"];
$sel_year_number = $_GET["sel_year_number"];
if($_GET["day"] <> "") {
	$sel_day_number = date("d",mktime(0,0,0,$sel_month_number,$_GET["day"],$sel_year_number));
}

if($sel_month_number == "") {
	$sel_month_number = date("m");
}
if($sel_year_number == "") {
	$sel_year_number = date("Y");
}
if($sel_day_number == "") {
	$sel_day_number = date("d");
}

$selected_date = $sel_year_number."-".$sel_month_number."-".$sel_day_number;

$display_date = $sel_month_number."/".$sel_day_number."/".$sel_year_number;



?> 
	<style>
	td {font-family:verdana,arial;font-size:11px;}
	</style>
<script>
function change_month(month_number,year_number) {
	//top.iframeHome.iframe_home_inner_front.location="home_inner_front.php?sel_month_number="+month_incr+"&year_now="+curr_year;
	location.href="home_inner_front.php?sel_month_number="+month_number+"&year_now="+year_number+"&display_cal=display&display_patient_sch=none&display_patient_search=none";
}
</script>	
	<?php
	  	if($_GET["display_patient_sch"]=="none") {
			$display_patient_schedule = "none";
		}else {
			$display_patient_schedule = "display";
		}
	
	?>
	<table	width="100%" border="0" cellspacing="0" cellpadding="0" id="patient_info" style="display:<?php echo $display_patient_schedule;?>;">
			<tr bgcolor="#B4C8AC">
				<td height="23" colspan="7" align="left" valign="middle" bgcolor="#FFFFFF" style="font-weight:bold; "><img src="images/tpixel.gif" width="5" height="1"> Patient Surgery Schedule   <?php echo $display_date;?>  </td>
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
			  <td width="16%" height="23" ><div style="padding-top:2px; padding-bottom:2px; ">&nbsp;&nbsp;<?php echo $stub_tbl_time;?>      </div></td>
				<td nowrap="nowrap" height="23"><a href="patient_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>" target="_top" class="link_home"><?php echo $stub_tbl_patient_name;?></a></td>
				<td  height="23" nowrap >&nbsp;&nbsp;<?php echo $stub_tbl_patient_primary_procedure;?></td>
				<td  height="23" >&nbsp;</td>
		  </tr>
		  <?php 
					}
					
				}	
			//END CODE FOR STUB DATABASE VALUE
	
		  ?>
					 
	 </table>
	 <?php
		//CODE START FOR CALENDER
	  	if($_GET["display_cal"]=="display") {
			$display_calender = "display";
		}else {
			$display_calender = "none";
		}
	  ?>
		<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#ECF1EA" id="cal" style="display:<?php echo $display_calender;?>; ">
						
						
				    
					<!-- START TEMPRARY CODING  -->
					<?php 
						if($_REQUEST["sel_month_number"]<>"")
						{
							$selected_month_number=$_REQUEST["sel_month_number"];
							$year_now=$_REQUEST["year_now"];
							if($selected_month_number>12) {
								$selected_month_number = 1;
								$year_now = $year_now+1;
							}
							if($selected_month_number==0) {
								$selected_month_number = 12;
								$year_now = $year_now-1;
							}
						}
						else
						{	
							$selected_month_number = date("m");
							$year_now = date("Y");
							
						}
						
						
						$selected_month_number_IncrByOne = $selected_month_number+1;//date("m",mktime(0,0,0,$selected_month_number+1,1,$year_now));
						$selected_month_number_DecrByOne = $selected_month_number-1;//date("m",mktime(0,0,0,$selected_month_number-1,1,$year_now));
						$year_now_IncrByOne = $year_now+1;//date("Y",mktime(0,0,0,$selected_month_number,1,$year_now+1));
						$year_now_DecrByOne = $year_now-1;//date("Y",mktime(0,0,0,$selected_month_number,1,$year_now-1));
						
						$lastday=date("t",mktime(0,0,0,$selected_month_number,1,$year_now));
						$weekday=date("w",mktime(0,0,0,$selected_month_number,1,$year_now));
						if($weekday==0) {
							$weekday = 7;
						}
						
						$days=array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
						$month_name = date("M",mktime(0,0,0,$selected_month_number,1,$year_now));
					?>
					<tr bgcolor="#ECF1EA" >
							<td height="23" colspan="7" align="center" valign="middle" bgcolor="#FFFFFF"  class="text_homeb all_border" style="border-bottom:1px solid #B4C8AC;border-top:1px solid #B4C8AC;"><img src="images/tpixel.gif" width="5" height="1"><a href="javascript:change_month(<?php echo $selected_month_number;?>,<?php echo $year_now_DecrByOne;?>);"><img border="0" src="images/cal2_back.jpg" width="15" height="14"></a><img src="images/tpixel.gif" width="5" height="1"><a href="javascript:change_month(<?php echo $selected_month_number_DecrByOne;?>,<?php echo $year_now;?>);"><img border="0" src="images/cal_back.jpg" width="16" height="14"></a> <img border="0" src="images/tpixel.gif" width="5" height="1"><?php echo $month_name." ".$year_now?><img src="images/tpixel.gif" width="5" height="1"><a href="javascript:change_month(<?php echo $selected_month_number_IncrByOne;?>,<?php echo $year_now;?>);"><img border="0"  src="images/cal_next.jpg" width="16" height="14"></a><img src="images/tpixel.gif" width="5" height="1"><a href="javascript:change_month(<?php echo $selected_month_number;?>,<?php echo $year_now_IncrByOne;?>);"><img border="0" src="images/cal2_next.jpg" width="15" height="14"></a><img src="images/tpixel.gif" width="5" height="1"></td>
						</tr>
					<tr bgcolor='#F8F9F7'>
						<td colspan="7">
								<table border="0" width="100%" height="100%" align="center" cellpadding="10" cellspacing="2" >
									<Tr bgcolor="#F8F9F7" class="text_homeb">
										<?php for($i=0;$i<7;$i++){?>
										<Td ><?php echo $days[$i];?></Td>
										<?php 
										}
										
										?>
									</Tr>
									<TR align='center' valign='middle' bgcolor='#FFFFFF' >
										<?php 
										
										$j=1;
										$weekday2 = 0;
										$days = false;
										$p = 1;
										while($p<$lastday){
											if($days == true){
												$p++;
											}
											if($j<=7){				
												$weekday2++;
												if($weekday2==$weekday || $days == true){				
													$days = true;
													//echo "<TD height = 15 bgcolor=red>$p</td>";	
													echo "<TD height = 15 ><a class='link_home' href='?day=$p&selected_month_number=$selected_month_number&sel_year_number=$year_now'>$p</a></td>";	
												}
												else{
													echo "<TD  >&nbsp;</td>";	
												}
												if($j%7==0){
													echo "</Tr><tr align='center' valign='middle' bgcolor='#FFFFFF' >";
												}
											}
											else{	
												if($p==date("d")){
													//$color="grey";
													$color="#ECF1EA";
												}else{
													$color="";
												}
																			
												echo "<TD height=15 bgcolor=$color><a class='link_home' href='?day=$p&selected_month_number=$selected_month_number&sel_year_number=$year_now'>$p</a></td>";
												if($j%7==0){
													echo "</Tr><tr align='center' valign='middle' bgcolor='#FFFFFF' >";
												}
											}
											
										$j++;
										}
										?>
	
							
							</table>
							</td>
						</tr>			
					<!-- END TEMPRARY CODING -->
					
					
					</table>
					<?php
				  	//CODE END FOR CALENDER
				  ?>	
					<!-- START DISPLAY PATIENT SEARCH RESULTS-->					
					<?php
							if($_GET["display_patient_search"]=="display") {
								$display_patient_search_result = "display";
							}else {
								$display_patient_search_result = "none";
							} 
					
					?>					
					<table	width="100%" border="0" cellspacing="0" cellpadding="0" id="patient_search_results_id" style="display:<?php echo $display_patient_search_result;?>;">
								<tr bgcolor="#B4C8AC">
									<td height="23" colspan="8" align="left" valign="middle" bgcolor="#FFFFFF" style="font-weight:bold; "><img src="images/tpixel.gif" width="5" height="1">Patient Search Results</td>
								</tr>
								<?php 
								//CODE TO DISPLAY PATIENT SEARCH RESULTS
									
									
									$txt_patient_search = trim($_GET["txt_patient_search_id"]);
									if($txt_patient_search <>"") {
										
										if(is_numeric($txt_patient_search)) {
											
											$getPatientSearchQry = "select * from patient_data_tbl where 
																	patient_id = '$txt_patient_search' 
																	order by `patient_fname` "; 
										} else { 
											$getPatientSearchQry = "select * from patient_data_tbl where 
																	patient_fname LIKE '$txt_patient_search%' 
																	OR patient_lname LIKE '$txt_patient_search%' 
																	order by `patient_fname` "; 
										}							
										$getPatientSearchRes = imw_query($getPatientSearchQry) or die(imw_error());
										$getPatientSearchNumRow = imw_num_rows($getPatientSearchRes);
										if($getPatientSearchNumRow > 0) {
											while($getPatientSearchRow = imw_fetch_array($getPatientSearchRes)) {
											
												$patientSearch_id = $getPatientSearchRow['patient_id'];
												$patientSearch_fname = $getPatientSearchRow['patient_fname'];
												$patientSearch_mname = $getPatientSearchRow['patient_mname'];
												$patientSearch_lname = $getPatientSearchRow['patient_lname'];
												//$patientSearch_name = $patientSearch_fname." ".$patientSearch_mname." ".$patientSearch_lname;
												$patientSearch_name = $patientSearch_lname." ".$patientSearch_fname;
												
												$patientSearch_asc_id  = $getPatientSearchRow['asc_id'];
												$patientSearch_dob_temp = $getPatientSearchRow['date_of_birth'];
													$patientSearch_dob_split = explode("-",$patientSearch_dob_temp);
													$patientSearch_dob = $patientSearch_dob_split[1]."-".$patientSearch_dob_split[2]."-".$patientSearch_dob_split[0];
												
												$getPatientSearchConfirmQry = "select * from patientconfirmation where patientId = '$patientSearch_id' "; 
												$getPatientSearchConfirmRes = imw_query($getPatientSearchConfirmQry) or die(imw_error());						 
												$getPatientSearchConfirmRow = imw_fetch_array($getPatientSearchConfirmRes);
												$patientSearchConfirm_id = $getPatientSearchConfirmRow["patientConfirmationId"];
												$patientSearch_dos_temp = $getPatientSearchConfirmRow["dos"];
													$patientSearch_dos_split = explode("-",$patientSearch_dos_temp);
													$patientSearch_dos = $patientSearch_dos_split[1]."-".$patientSearch_dos_split[2]."-".$patientSearch_dos_split[0];
												$patientSearch_prim_procedure = $getPatientSearchConfirmRow["patient_primary_procedure"];
												$patientSearch_sec_procedure = $getPatientSearchConfirmRow["patient_secondary_procedure"];
												if($patientSearch_prim_procedure<>"") {
													$patientSearch_procedure = $patientSearch_prim_procedure;
												}else {
													$patientSearch_procedure = $patientSearch_sec_procedure;
												}
									?>
								  <tr><td height="1"></td></tr>
									<tr bgcolor="#ECF1EA" class="text_homeb" >
										<td  height="23" nowrap >&nbsp;</td>
										<td nowrap  height="23">Name</td>
										<td nowrap  height="23" ><img src="images/tpixel.gif" width="5" height="1">ASCID</td>
									    <td  height="23" nowrap> DOB</td>
										<td  height="23" nowrap >&nbsp;&nbsp;Procedure</td>
										<td nowrap="nowrap" height="23">DOS</a></td>
										<td  height="23" >&nbsp;ID</td>
										<td  height="23" >&nbsp;</td>
								  
								  </tr>
								 <!--  <a class='link_home' target='_top' style='cursor:hand; ' href='mainpage.php?patient_id=<?php //echo $patientSearch_id;?>&pConfId=<?php //echo $patientSearchConfirm_id;?>&ascId=<?php //echo $patientSearch_asc_id;?>'> -->
								  <?php
								  $ahref_link_start = "<a class='link_home' target='_top' style='cursor:hand; ' href='mainpage.php?patient_id=$patientSearch_id&pConfId=$patientSearchConfirm_id&ascId=$patientSearch_asc_id'>";
								  $ahref_link_end = "</a>";
								  ?>	
									<tr style="cursor:hand;?>" align="left" valign="middle"  bgcolor="#FFFFFF">
										<td  height="23" nowrap class="text_homeb" >&nbsp;</td>
										<td nowrap  height="23" ><?php echo $ahref_link_start.$patientSearch_name.$ahref_link_end;?></td>
										<td nowrap  height="23" ><img src="images/tpixel.gif" width="5" height="1"><?php echo $ahref_link_start.$patientSearch_asc_id.$ahref_link_end;?></td>
									    <td  height="23" nowrap> <?php echo $ahref_link_start.$patientSearch_dob.$ahref_link_end;?></td>
										<!-- <td nowrap="nowrap" height="23"><?php //echo $patientSearch_id;?></a></td> -->
										<td  height="23" nowrap >&nbsp;&nbsp;<?php echo $ahref_link_start.$patientSearch_procedure.$ahref_link_end;?></td>
										<td nowrap="nowrap" height="23"><?php echo $ahref_link_start.$patientSearch_dos.$ahref_link_end;?></td>
										<td  height="23" >&nbsp;<?php echo $ahref_link_start.$patientSearch_id.$ahref_link_end;?></td>
										<td  height="23" >&nbsp;</td>
								  </tr>
								 </a> 
								  <?php 
											} // END WHILE LOOP
										} else {   //END IF NUMROW>0
								 ?>
								  <tr bgcolor="#ECF1EA">
										<td height="23" colspan="8" align="center" valign="middle">
											No Record Found !
										</td>
								  </tr>
								
								<?php		
										
										} //END ELSE PART OF (IF NUMROW>0)
									
									}else {	//END IF PATIENT SEACRH BOX IS NOT EMPTY 
								?>
								
								  <tr bgcolor="#ECF1EA">
										<td height="23" colspan="7" align="center" valign="middle">
											No Record Found !
										</td>
								  </tr>
								<?php		
									}//END ELSE PART OF (IF PATIENT SEACRH BOX IS  EMPTY) 
									
								//END CODE TO DISPLAY PATIENT SEARCH RESULTS
						
							  ?>
										 
					</table>
				<!-- END DISPLAY PATIENT SEARCH RESULT -->				    