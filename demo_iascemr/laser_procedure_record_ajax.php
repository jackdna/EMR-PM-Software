<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
	
	$patient_id = $_REQUEST['patient_id'];
	//$ascId = $_SESSION['ascId'];
   $pConfId = $_REQUEST['pConfId'];
	$time=$_GET['medicationTime'];
	
	//TIME saved in database
	 
	       $time_split = explode(" ",$time);
	       
		if($time_split[1]=="PM" || $time_split[1]=="pm") {
			
			$time_split = explode(":",$time_split[0]);
			$medsTimeIncr=$time_split[0]+12;
			$medsTime = $medsTimeIncr.":".$time_split[1].":00";
			
		}elseif($time_split[1]=="AM" || $time_split[1]=="am") {
		    $time_split = explode(":",$time_split[0]);
			$medsTime=$time_split[0].":".$time_split[1].":00";
			
			if($time_split[0]=="00" && $time_split[1]=="00") {
				$medsTime=$time_split[0].":".$time_split[1].":01";
			}
		}
	   //TIME saved in database
	
	
	imw_query("INSERT INTO medication_time set	
				 patient_id=$patient_id ,
				  confirmation_id =$pConfId, 
				  medication_time='$medsTime'" );
	//$selectrecordqry=imw_query("select medication_time from medication_time");
?>	

<!-- <td colspan="7" id="timeMedicatedId"> -->
	<table  cellpadding="0" cellspacing="0" border="0" width="">
		<tr height="25">
			<td width="14"></td>
			
				<td>0
				<table cellpadding="0" cellspacing="0" border="0" width="">
					<tr>
					<td width="22"  nowrap="nowrap" class="text_10">
						<img src="images/clock.gif" border="0" onClick="return displayTimeAmPm('textTime');"/></td>
					<td width="38" class="text_10">Time</td>
					<td width="77" class="text_10">Medicated</td>
					<td width="99" align="center" id="s1Time"  class="text_10">
							<input type="text" id="textTime" size="8" class="text_10" value="<?php echo date("h:i A"); ?>">		
					</td>
					<td class="text_10" id="time_med">
					<?php
						 $getmedtimeqry=imw_query ("select medication_time from medication_time where	
									 patient_id='$patient_id' and
									  confirmation_id ='$pConfId'");
						 $numrows=imw_num_rows($getmedtimeqry); 		
						 if($numrows>0)
						 {
						 $i=1;
						 while($getTime=imw_fetch_array($getmedtimeqry))	
						 {
						 
						  //CODE TO SET MEDICATION TIME
						  $Time=$getTime["medication_time"];
						$time_split2 = explode(":",$Time);
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
					echo $MedsTime = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
					//END CODE TO SET MEDICATION TIME	
						//echo $Time=$getTime["medication_time"];  
						
					?>	
					&nbsp;&nbsp;
						<?php $time=explode(":",$Time); 
							 $timemin=$time[0].":".$time[1]."&nbsp;&nbsp;&nbsp;";
						$i++;
						}
					}	
					
						?>
					  </td>
						
					</tr>
				</table>
			</td>			
			<td width="100" align="center"><input onClick="return save_medication_time_value();" type="button" value="Save" class="button" style="width:70px; border-color:<?php echo $border_color_physician;?>; " name="save"></td>
			<!-- 						  		<td width="90" align="center" id="s2Time" style="display:none"   class="text_10">&nbsp;</td>
			<td width="709" align="left" id="s3Time" style="display:none"   class="text_10">&nbsp;</td>
	-->							</tr>
	</table>
<!-- </td> -->