<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

require_once(dirname(__FILE__).'/../../../config/globals.php');
$sesionHeightInPtHx="";
$sesionHeightInPtHx=$_SESSION['wn_height']-527;
//create array for facilies
$fac_q=imw_query("SELECT name, id FROM `facility`");
while($fac_d=imw_fetch_object($fac_q))
{
	$fac_arr[$fac_d->id]=$fac_d->name;
}
//create array for providers
$user_q=imw_query("SELECT lname, mname, fname, id, username FROM `users`");
while($user_d=imw_fetch_object($user_q))
{
	$user_arr[$user_d->id]=$user_d->lname.", ".$user_d->fname;
	$username_arr[$user_d->username]['lname']=$user_d->lname;
	$username_arr[$user_d->username]['mname']=$user_d->mname;
	$username_arr[$user_d->username]['fname']=$user_d->fname;
}
?>
	<div class="section" style="height:<?php echo ($sesionHeightInPtHx-35)."px"; ?>; width:100%; overflow-y:scroll; overflow-x:auto;">
		<table class="table table-striped table-bordered table-hover">	
        	<thead>
			<?php			
			$pat_id=$_GET['pat_id'];
			if($pat_id<>""){			
				if($_REQUEST['mode'] != "tiny"){
				?>		
			<tr bgcolor="#4684ab">
				<th colspan="9" class="text-center">&nbsp;Appointment status for the selected patient.&nbsp;</th>
			</tr>
			<?php }?>
			<tr bgcolor="#4684ab">
				<th style="width: 3%">&nbsp;</th>						
				<th style="width: 12%">Date &amp; Time</th>						
				<th style="width: 7%">Check In</th>
				<th style="width: 7%">Check Out</th>
				<th style="width: 14%">Location</th>
				<th style="width: 14%">Provider</th>
				<th style="width: 14%">Procedure</th>
				<th style="width: 24%">Notes</th>	
				<th style="width: 5%">Op.</th>						
			</tr>
            </thead>
            <tbody>
				<?php
				
				$strExpandAllFunctionContent = "";
				$strCollapseAllFunctionContent = "";
				//create array of CI/CO time for selected patient
				//get records from archive table if any
				if($GLOBALS["CHK_ARCHIVE_TABLE"]){
					$qrt=imw_query("SELECT sch_id, status, status_time from previous_status_archive WHERE patient_id='$pat_id' and status IN(11,13) ORDER BY id ASC");
					while($res=imw_fetch_assoc($qrt)){
					$time_t=$appoint_time_to=$time_hourt=$time_minutet="";
					$appoint_time_to=$res['status_time'];
					$time_hourt=substr($appoint_time_to,0,2);
					$time_minutet=substr($appoint_time_to,3,2);
					$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
					$status_arr[$res['sch_id']][$res['status']]=$time_t;
				}
				}
				//get records from live table
				$qrt=imw_query("SELECT sch_id, status, status_time from previous_status WHERE patient_id='$pat_id' and status IN(11,13) ORDER BY id ASC");
				while($res=imw_fetch_assoc($qrt)){
					$time_t=$appoint_time_to=$time_hourt=$time_minutet="";
					$appoint_time_to=$res['status_time'];
					$time_hourt=substr($appoint_time_to,0,2);
					$time_minutet=substr($appoint_time_to,3,2);
					$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
					$status_arr[$res['sch_id']][$res['status']]=$time_t;
				}
				
				$vquery_c = "SELECT 
								schedule_appointments.sa_patient_id, schedule_appointments.sa_patient_app_status_id, 
								schedule_appointments.id,ps.oldMadeBy as oldMadeBy,schedule_appointments.sa_doctor_id,
								schedule_appointments.sa_comments, schedule_appointments.procedureid, 
								date_format( schedule_appointments.sa_app_time, '%m-%d-%y' ) AS sa_app_time, 
								time_format( sa_app_starttime, '%h:%i %p' ) AS sa_app_starttime, 
								time_format( sa_app_endtime, '%h:%i %p' ) AS sa_app_endtime,
								date_format( sa_app_start_date, '".get_sql_date_format()."' ) AS sa_app_start_date, slot_procedures.proc, 
								slot_procedures.acronym, schedule_appointments.sa_facility_id  
							FROM 
								schedule_appointments 
								LEFT JOIN previous_status ps ON (ps.sch_id = schedule_appointments.id AND ps.status=0)
								INNER JOIN slot_procedures ON slot_procedures.id = schedule_appointments.procedureid
								WHERE schedule_appointments.sa_patient_id ='".$pat_id."'    
								GROUP BY schedule_appointments.id ORDER BY schedule_appointments.sa_app_start_date DESC 
							";								 
				$vsql_c = imw_query($vquery_c);
				while($vrs=imw_fetch_array($vsql_c)){
					$all_record[$vrs["id"]]=$vrs;
				}
				
				//get records from archive table if any
				if($GLOBALS["CHK_ARCHIVE_TABLE"]){
					$vquery_c_a = "SELECT 
								sa_arch.sa_patient_id, sa_arch.sa_patient_app_status_id, 
								sa_arch.id,ps.oldMadeBy as oldMadeBy,sa_arch.sa_doctor_id,
								sa_arch.sa_comments, sa_arch.procedureid, 
								date_format( sa_arch.sa_app_time, '%m-%d-%y' ) AS sa_app_time, 
								time_format( sa_app_starttime, '%h:%i %p' ) AS sa_app_starttime, 
								time_format( sa_app_endtime, '%h:%i %p' ) AS sa_app_endtime,
								date_format( sa_app_start_date, '".get_sql_date_format()."' ) AS sa_app_start_date, slot_procedures.proc, 
								slot_procedures.acronym, sa_arch.sa_facility_id  
							FROM 
								schedule_appointments_archive sa_arch
								LEFT JOIN previous_status_archive ps ON (ps.sch_id = sa_arch.id AND ps.status=0)
								INNER JOIN slot_procedures ON slot_procedures.id = sa_arch.procedureid
								WHERE sa_arch.sa_patient_id ='".$pat_id."'    
								GROUP BY sa_arch.id ORDER BY sa_arch.sa_app_start_date DESC 
							";								 
					$vsql_c_a = imw_query($vquery_c_a);
					while($vrs_a=imw_fetch_array($vsql_c_a)){
						if(!$all_record[$vrs_a["id"]])$all_record[$vrs_a["id"]]=$vrs_a;
					}
				}
				
				foreach($all_record as $key=>$vrs)
				{
					$id=$vrs["id"];
					$procedureid=$vrs["procedureid"];
					$doctor_id=$vrs["sa_doctor_id"];
					$facility_id=$vrs["sa_facility_id"];
					$sa_patient_id=$vrs["sa_patient_id"];
					$prc_id=$procedureid;
					$op_fname=$username_arr[$vrs['oldMadeBy']]['fname'];
					$op_mname=$username_arr[$vrs['oldMadeBy']]['mname'];
					$op_lname=$username_arr[$vrs['oldMadeBy']]['lname'];
					$strOpMadeByName ='';
					if($op_mname == ''){
						if($op_fname)$strOpMadeByName = strtoupper(substr($op_fname,0,2));
						if($op_lname)$strOpMadeByName .= ".".strtoupper(substr($op_lname,0,1));
					}else{
						if($op_fname)$strOpMadeByName = strtoupper(substr($op_fname,0,1));
						if($op_mname)$strOpMadeByName .= ".".strtoupper(substr($op_mname,0,1));
						if($op_lname)$strOpMadeByName .= ".".strtoupper(substr($op_lname,0,1));
						
					}
					
					//tr style
					//$onclick = "onclick=\"refresh_patient_infopage('".$sa_patient_id."','".$id."','to_do');\"";
					if($vrs['sa_patient_app_status_id'] == '18' || $vrs['sa_patient_app_status_id'] == '203'){
						$sty="style=color:red;background-color:#CCCCCC;font-weight:bold";
					}elseif($vrs['sa_patient_app_status_id'] == '3'){
						$sty="style=color:orange;background-color:#f3f3f3;font-weight:bold";				
					}else{						
						$sty="";			
					}// style="cursor:pointer;"
					?>
			<tr bgcolor="#ffffff">
				<td onClick="javascript:popUpMe('<?php echo($vrs["id"]);?>','<?php echo($pat_id);?>','');"  style="text-align: center; cursor: pointer" ><?php echo "<img src=\"".$GLOBALS['webroot']."/library/images/history.gif\" title='Click to load status detail for this appointment'>";?></td>						
				<td <?php echo $sty;?>><?php echo($vrs["sa_app_start_date"]." ".$vrs["sa_app_starttime"]);?></td>						
				<td <?php echo $sty;?>>
					<?php echo $status_arr[$vrs["id"]][13];?>	
				</td>
				<td <?php echo $sty;?>>
					<?php echo $status_arr[$vrs["id"]][11];?>
				</td>
				<td <?php echo $sty;?>><?php echo $fac_arr[$facility_id]; ?></td>
				<td <?php echo $sty;?>><?php echo $user_arr[$doctor_id]; ?></td>
				<td <?php echo $sty;?>><?php echo $vrs['proc']; ?></td>
				<td <?php echo $sty;?>><?php echo($vrs['sa_comments']);?></td>	
				<td <?php echo $sty;?>><?php echo($strOpMadeByName);?></td>						
			</tr>
			
				<input type="hidden" name="my_main_appt[]" id="my_main_appt" value="<?php echo $vrs["id"]; ?>">
			<?php }
			}else{		
				?>					
			<tr width='100%' bgcolor="#4684ab">
				<td colspan="9" class="text_b_w" align="center">&nbsp;</td>
			</tr>
		   		<?php
			}
			?>
            </tbody>
		</table>
	</div>