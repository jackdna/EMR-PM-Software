<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
$objManageData 		= new manageData;

	$patient_id = $_SESSION['patient_id'];
	$ascId = $_SESSION['ascId'];
	$pConfId = $_SESSION['pConfId'];
	$time=$_GET['heparin_time'];
	$heparinTime=$objManageData->setTmFormat($time);
	//TIME saved in database
	 /*
	       $time_split = explode(" ",$time);
	       
		if($time_split[1]=="PM" || $time_split[1]=="pm") {
			
			$time_split = explode(":",$time_split[0]);
			$heparinTimeIncr=$time_split[0]+12;
			$heparinTime = $heparinTimeIncr.":".$time_split[1].":00";
			
		}elseif($time_split[1]=="AM" || $time_split[1]=="am") {
		    $time_split = explode(":",$time_split[0]);
			$heparinTime=$time_split[0].":".$time_split[1].":00";
			
			if($time_split[0]=="00" && $time_split[1]=="00") {
				$heparinTime=$time_split[0].":".$time_split[1].":01";
			}
		}
	*/	
	   //TIME saved in database
	imw_query("INSERT INTO heparin_lockout_time set	
				 patient_id=$patient_id ,
				  confirmation_id =$pConfId, 
				  time='$heparinTime'");

?>

					<!-- <table cellpadding="0" cellspacing="0" border="0" width="">
						<tr>
						
						<td class="text_10" id="heparin_time_td"> -->
						<?php
							 $getheparintimeqry=imw_query ("select time from heparin_lockout_time where	
										 patient_id='$patient_id' and
										  confirmation_id ='$pConfId'");
							 $numrows=imw_num_rows($getheparintimeqry); 		
							 if($numrows>0)
							 {
							 $i=1;
							 while($getTime=imw_fetch_array($getheparintimeqry))	
							 {
							 
							  //CODE TO SET HEPARIN TIME
							  $Time=$getTime["time"];
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
						echo $heparinTime = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
						//END CODE TO SET HEPARIN TIME	
							
						?>	
						&nbsp;&nbsp;
							<?php $time=explode(":",$Time); 
								 $timemin=$time[0].":".$time[1]."&nbsp;";
							$i++;
							}
						}	
						
							?>
						  <!-- </td>
							
						</tr>
				</table> -->