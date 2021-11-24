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
function getSuperBillCharge2($fld,$patientId,$phy_id='',$date_app=''){
	if($date_app!=""){
		$whr_dat=" and date_of_service='$date_app'";
	}
	if($phy_id){
		$qry = "select $fld from patient_charge_list where del_status='0' and 
				primaryPhysicianId in ('$phy_id') and patient_id in ('$patientId')
				and copay>0 $whr_dat";
	}
	else{
		$qry = "select $fld from patient_charge_list where del_status='0' and patient_id in ('$patientId') and copay>0 $whr_dat";

	}
	//echo $qry;
	$qryId = imw_query($qry);
	if(imw_num_rows($qryId)>0){
		$qryRes = imw_fetch_array($qryId);
	}			
	return $qryRes;
}

function default_proc_to_doctor_proc($default_procedure_id, $doctor_id){
	$proc_id_qry = "SELECT sp1.id, sp2.times, sp1.proc_mess   
						FROM slot_procedures sp1 
						LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id 
						WHERE sp1.times = '' 
						AND sp1.proc != '' 
						AND sp1.doctor_id = '".$doctor_id."' 
						AND sp1.procedureId = '".$default_procedure_id."'";
	$res_proc_id = imw_query($proc_id_qry);
	$arr_proc_id = array();
	if(imw_num_rows($res_proc_id)> 0){
		$arr_proc_id = imw_fetch_assoc($res_proc_id);
		if($arr_proc_id["times"] != ""){
			return $arr_proc_id["id"]."~".$arr_proc_id["times"]."~".$arr_proc_id["proc_mess"];
		}else{
			$proc_id_qry3 = "SELECT sp1.id, sp2.times, sp1.proc_mess   
						FROM slot_procedures sp1 
						LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id 
						WHERE sp1.times = '' 
						AND sp1.proc != '' 
						AND sp1.doctor_id = '0' 
						AND sp1.id = '".$default_procedure_id."'";
			$res_proc_id3 = imw_query($proc_id_qry3);
			$arr_proc_id3 = array();
			if(imw_num_rows($res_proc_id3)> 0){
				$arr_proc_id3 = imw_fetch_assoc($res_proc_id3);
				//echo "here";
				return $arr_proc_id["id"]."~".$arr_proc_id3["times"]."~".$arr_proc_id["proc_mess"];
			}else{
				//echo "there";
				return $arr_proc_id["id"]."~".$arr_proc_id["times"]."~".$arr_proc_id["proc_mess"];
			}
		}
	}else{
		$proc_id_qry2 = "SELECT sp1.id, sp2.times, sp1.proc_mess   
						FROM slot_procedures sp1 
						LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id 
						WHERE sp1.times = '' 
						AND sp1.proc != '' 
						AND sp1.doctor_id = '0' 
						AND sp1.id = '".$default_procedure_id."'";
		$res_proc_id2 = imw_query($proc_id_qry2);
		$arr_proc_id2 = array();
		if(imw_num_rows($res_proc_id2) > 0){
			$arr_proc_id2 = imw_fetch_assoc($res_proc_id2);
			return $arr_proc_id2["id"]."~".$arr_proc_id2["times"]."~".$arr_proc_id2["proc_mess"];
		}else{
			return false;
		}
	}
}


function get_operator_initial_by_username($strUsername){
	if(trim($strUsername) != ""){
	    $strQry = "SELECT fname, mname, lname FROM users WHERE username = '".$strUsername."'";
		$rsData = imw_query($strQry);
		$arrData = imw_fetch_assoc($rsData);
		$fullName = "";
		$firstName = trim($arrData['fname']);
		$middleName = trim($arrData['mname']);
		$lastName = trim($arrData['lname']);
		if($middleName == ''){
		    $fullName = strtoupper(substr($firstName,0,2)).". ".strtoupper(substr($lastName,0,1));
		}else{
		    $fullName = strtoupper(substr($firstName,0,1)).". ".strtoupper(substr($middleName,0,1)).". ".strtoupper(substr($lastName,0,1));
		}
		return $fullName;
	}else{
		return "";
	}
}

//function to display All details for One Appointment//
function get_one_schdeule_details($pat_id,$schid="",$showAll="", $mode = ""){
	$counterP=0;
	$vquery_c = "SELECT 
			ps.sch_id, ps.dateTime, ps.patient_id, ps.status_time, ps.status_date, 
			ps.status, ps.old_status, 
			ps.statusComments, ps.oldStatusComments, 
			DATE_FORMAT(ps.new_appt_date,'".get_sql_date_format('','y','/')."') AS new_appt_date, DATE_FORMAT(ps.old_date,'".get_sql_date_format('','y','/')."') AS old_appt_date, 
			TIME_FORMAT(ps.new_appt_start_time,'%h:%i %p') AS new_appt_start_time, TIME_FORMAT(ps.old_time,'%h:%i %p') AS old_appt_start_time, 
			TIME_FORMAT(ps.new_appt_end_time,'%h:%i %p') AS new_appt_end_time, TIME_FORMAT(ps.old_appt_end_time,'%h:%i %p') AS old_appt_end_time, 
			ps.new_facility, ps.old_facility, 
			ps.new_provider, ps.old_provider, 
			ps.new_procedure_id, ps.old_procedure_id, 
			ps.statusChangedBy, ps.oldMadeBy, 
			oldsp.proc AS oldProc, newsp.proc AS newProc,
			oldsp.acronym AS oldProcA, newsp.acronym AS newProcA,
			oldf.name AS oldFac, newf.name AS newFac, 
			oldU.fname AS oldProvFN, newU.fname AS newProvFN,  							
			oldU.lname AS oldProvLN, newU.lname AS newProvLN,
			change_reason 
		FROM 
			previous_status AS ps 
		LEFT JOIN slot_procedures AS oldsp ON oldsp.id = ps.old_procedure_id 
		LEFT JOIN slot_procedures AS newsp ON newsp.id = ps.new_procedure_id 
		LEFT JOIN facility AS oldf ON oldf.id = ps.old_facility 
		LEFT JOIN facility AS newf ON newf.id = ps.new_facility 
		LEFT JOIN users AS oldU ON oldU.id = ps.old_provider 
		LEFT JOIN users AS newU ON newU.id = ps.new_provider 
		WHERE 
			ps.sch_id = $schid 
			AND ps.patient_id ='".$pat_id."' 
		ORDER BY ps.id DESC"; 
	
	/*AND ps.status IN ( 1, 3, 4, 5, 6, 11, 18, 13,17,2,0,21,22,23,'', 201,202,203, 271) */
	
	$vsql_c = imw_query($vquery_c);
	$vsql_c1 = imw_query($vquery_c);
	
	$intResetCnt = 0;
	?>
	<table class="table table-striped table-bordered table-hover">
	<?php 
	$intZeroCnt = 0;
	while($vrs1=imw_fetch_array($vsql_c1)){
		if($vrs1['status'] == 0){
			$intZeroCnt++;
		}
	}
	while($vrs=imw_fetch_array($vsql_c)){
		
		if ($vrs['status_date'] && get_number($vrs['status_date']) != "0") {									
			$tmp_date = $vrs['status_date'];
			//list($year, $month, $day) = split('-',$tmp_date);
			//$create_date = $month."/".$day."/".$year;
			$create_date=get_date_format($tmp_date);
		}
		$appoint_time_to=$vrs['status_time'];
		$time_hourt=substr($appoint_time_to,0,2);
		$time_minutet=substr($appoint_time_to,3,2);
		$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
		$strOpChangedByName = get_operator_initial_by_username($vrs['statusChangedBy']);
		
		$st_id=$vrs['status'];			
		
		if($st_id == 201){
			$stttt_us = "Moved to Re-Schedule";
		}else if($st_id == 271){
			$stttt_us = "First Available";
		}else if($st_id == 18)	{
			$stttt_us = "Cancelled";
		}else if($st_id=='')	{
			$stttt_us = "Deleted";
		}else if($st_id == '0'){
			if($intZeroCnt > 1){
				$stttt_us = "Restored";
				if($intResetCnt == ($intZeroCnt-1)){
					$stttt_us = "Created";
				}
			}else{
				$stttt_us = "Created";
			}
			$intResetCnt++;
		}elseif ($st_id == 202){
			$stttt_us = "Rescheduled";
		}
		elseif($st_id == 203)
		{
			$stttt_us = "Deleted";			
		}
		else{
			$vquery_st = "SELECT status_name FROM `schedule_status` WHERE id = $st_id";
			$vsql_st = imw_query($vquery_st);	
			$rs_st = imw_fetch_array($vsql_st);
			$stttt_us=$rs_st['status_name'];
		}		
		?>
		
			<tr>
				<td colspan="2">
					<table class="table table-striped table-bordered table-hover">
						<tr class="info">
							<td class="text-left col-lg-2 col-md-2">
								<strong><?php echo($stttt_us);?></strong>
							</td>
							<td class="text-left col-lg-2 col-md-2">
								<?php echo($create_date);?>
							</td>
							<td class="text-right col-lg-2 col-md-2">
								<?php echo($time_t);?>
							</td>
							<td class="text-center col-lg-5 col-md-5">
								<?php echo stripslashes($vrs["change_reason"]);?>
							</td>
							<td class="text-center col-lg-1 col-md-1">
								<?php echo($strOpChangedByName);?>
							</td>
							
						</tr>
					</table>
				</td>
			</tr>
		<?php 
			//not used from scheduler
			if($mode == "tiny"){
				?>
			<tr>
				<td colspan="2">&nbsp;<i>Old Details</i>&nbsp;
					<table class="table table-striped table-bordered table-hover">	
                    <thead>
						<tr bgcolor="#C0D7E2" height="20">
							<td class="text-center" nowrap="nowrap">Date</td>
							<td class="text-center" nowrap="nowrap">Time</td>
							<td class="text-left">Phy.</td>
							<td class="text-left">Loc</td>
							<td class="text-left">Pro.</td>
							<td class="text-left">Comments</td>		
						</tr>			
                    </thead>
                    <tbody>
						<tr valign="top">
							<td align="left" nowrap="nowrap" class="text_9"><?php echo $vrs['old_appt_date'];?></td>
							<td align="left" nowrap="nowrap" class="text_9"><?php echo  $vrs['old_appt_start_time'];?></td>							
							<td  align="left" class="text_9"><?php echo strtoupper(substr($vrs['oldProvFN'],0,1))." ".strtoupper(substr($vrs['oldProvLN'],0,1));?></td>
							<td align="left" class="text_9"> <?php echo getFacilityNameInitial($vrs['oldFac']);?></td>
							<td align="left" class="text_9"><?php echo $vrs['oldProcA'];?></td>	
							<td align="left" class="text_9" nowrap="nowrap"><?php echo $vrs['oldStatusComments']; ?></td>
						</tr>
                    </tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;<i>New Details</i>&nbsp;
					<table  class="table table-striped table-bordered table-hover">	
                    <thead>
						<tr>
							<td width="15%" align="center" nowrap="nowrap" class="text_9b">Date</td>
							<td  width="15%" align="center" nowrap="nowrap" class="text_9b">Time</td>
							<td  width="5%" align="left" class="text_9b">Phy.</td>
							<td  width="5%" align="left" class="text_9b">Loc</td>
							<td  width="5%" align="left" class="text_9b">Pro.</td>
							<td  width="43%" align="left" class="text_9b">Comments</td>							
						</tr>				
                  	</thead>
                    <tbody>
						<tr  valign="top">
							<td align="left" nowrap="nowrap"><?php echo $vrs['new_appt_date'];?></td>
							<td align="left" nowrap="nowrap"><?php echo  $vrs['new_appt_start_time'];?></td>							
							<td lign="left"> <?php echo getFacilityNameInitial($vrs['newFac']);?></td>
							<td align="left"><?php echo $vrs['newProcA'];?></td>	
							<td align="left" nowrap="nowrap"><?php echo $vrs['statusComments']; ?></td>
						</tr>
                    </tbody>
					</table>
				</td>
			</tr>
			<?php
			}else{
			?>
			<tr>
				<td class="text-left col-lg-6 col-md-6"><?php if($mode != "print"){ ?>&nbsp;<i>Old Details</i>&nbsp;<?php } ?>
					<table  class="table table-striped table-bordered table-hover">	
                    <thead>
						<tr>
							<th class="text-left">Date</th>
							<th class="text-left">Time</th>
							<th class="text-left">Phy.</th>
							<th class="text-left">Loc</th>
							<th class="text-left">Pro.</th>
							<th class="text-left">Comments</th>		
						</tr>			
                   </thead>
                   <tbody>
						<tr valign="top">
							<td class="text-left" nowrap="nowrap"><?php echo $vrs['old_appt_date'];?></td>
							<td class="text-left" nowrap="nowrap"><?php echo  $vrs['old_appt_start_time'];?></td>							
							<td class="text-left"><?php echo $vrs['oldProvFN']." ".$vrs['oldProvLN'];?></td>
							<td class="text-left"> <?php echo $vrs['oldFac'];?></td>
							<td class="text-left"><?php echo $vrs['oldProc'];?></td>	
							<td class="text-left" nowrap="nowrap"><?php echo $vrs['oldStatusComments']; ?></td>
						</tr>
                    </tbody>
					</table>
				</td>
				<td class="text-left col-lg-6 col-md-6"><?php if($mode != "print"){ ?>&nbsp;<i>New Details</i>&nbsp;<?php } ?>
					<table  class="table table-striped table-bordered table-hover">	
                    <thead>
						<tr>
							<th class="text-center" nowrap="nowrap" >Date</th>
							<th class="text-center" nowrap="nowrap" >Time</th>
							<th class="text-left">Phy.</th>
							<th class="text-left">Loc</th>
							<th class="text-left">Pro.</th>
							<th class="text-left">Comments</th>							
						</tr>
                   </thead>
                   <tbody>				
						<tr valign="top">
							<td align="left" nowrap="nowrap"><?php echo $vrs['new_appt_date'];?></td>
							<td align="left" nowrap="nowrap"><?php echo  $vrs['new_appt_start_time'];?></td>							
							<td align="left"><?php echo $vrs['newProvFN']." ".$vrs['newProvLN'];?></td>
							<td align="left"> <?php echo $vrs['newFac'];?></td>
							<td align="left"><?php echo $vrs['newProc'];?></td>	
							<td align="left" nowrap="nowrap"><?php echo $vrs['statusComments']; ?></td>
						</tr>
                    </tbody>
					</table>
				</td>
			</tr>
			<?php
			}
//		}
		?>
		
		<?php
		$counterP=1;
	}
	
	if($counterP==0 && $mode != "print"){
		?>		
			<tr>
				<td colspan="2">No History Found.</td>
			</tr>		
		<?php
	}
	?>
	</table>
	<?php
	
}
function getCheckin_time($schid){
	$time_t="";
	$qrt=imw_query("SELECT previous_status.*,date_format( previous_status.status_date, '%m-%d-%y' ) AS status_dateMDY from previous_status WHERE sch_id='$schid' and status=13 ORDER BY previous_status.id DESC LIMIT 1");
	$res=imw_fetch_array($qrt);
	if ($res['status_date'] && $res['status_date'] != "00-00-0000") {
		$appoint_time_to=$res['status_time'];
		$time_hourt=substr($appoint_time_to,0,2);
		$time_minutet=substr($appoint_time_to,3,2);
		$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
	}
	return $time_t;
}

function getCheckout_time($schid){
	$time_t="";
	$qrt=imw_query("SELECT previous_status.*,date_format( previous_status.status_date, '%m-%d-%y' ) AS status_dateMDY from previous_status WHERE sch_id='$schid' and status=11 ORDER BY previous_status.id DESC LIMIT 1");
	$res=imw_fetch_array($qrt);
	if ($res['status_date'] && $res['status_date'] != "00-00-0000"){
		$appoint_time_to=$res['status_time'];
		$time_hourt=substr($appoint_time_to,0,2);
		$time_minutet=substr($appoint_time_to,3,2);
		$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
	}
	return $time_t;
}	
function getFacility_name($intFacId, $strMode = ""){
//	echo "SELECT name FROM `facility` WHERE id=$intFacId";
	$qrt=imw_query("SELECT name FROM `facility` WHERE id=$intFacId");
	list($name)=imw_fetch_array($qrt);
	if($strMode == "tiny"){
		$arrName = explode(" ",$name);
		$intNameParts = count($arrName);
		if($intNameParts <= 0){
			return "";
		}elseif($intNameParts == 1){
			return strtoupper(substr($arrName[0],0,2));
		}else{
			return strtoupper(substr($arrName[0],0,1)).strtoupper(substr($arrName[1],0,1));
		}
	}else{
		return $name;
	}
}

function getFacilityNameInitial($name){
	$arrName = explode(" ",$name);
	$intNameParts = count($arrName);
	if($intNameParts <= 0){
		return "";
	}elseif($intNameParts == 1){
		return strtoupper(substr($arrName[0],0,2));
	}else{
		return strtoupper(substr($arrName[0],0,1)).strtoupper(substr($arrName[1],0,1));
	}
}

/*
moved to common function file
function getProcedureName($id){
	$qry="select proc,acronym from slot_procedures where id=$id";
	$r=imw_query($qry);
	if(imw_num_rows($r)>0){
		list($proc,$acronym)=imw_fetch_array($r);
	}
	return $proc;
}*/

/*
moved to appt page function file
 function getUserName($id, $format = 'default'){
 	$qry="select fname,lname from users where id=$id";
	$r=imw_query($qry);
	if(imw_num_rows($r)>0){
		list($fname,$lname)=imw_fetch_array($r);
	}
	if($format == "default")
		return $fname." ".substr($lname,0,1).".";
	elseif($format == "lefttopdropdown")
		return $lname.", ".$fname;
 }
 */
 //function to display All details for One Appointment//
function GetOneSchdeuleDetails($pat_id,$schid="",$showAll="", $mode = "",$CommonAppStatusArr=''){
//	echo $mode;
	$counterP=0;
	$vquery_c = "	SELECT 
							ps.sch_id, ps.dateTime, ps.patient_id, ps.status_time, ps.status_date, 
							ps.status, ps.old_status, 
							ps.statusComments, ps.oldStatusComments, 
							DATE_FORMAT(ps.new_appt_date,'".get_sql_date_format('','y','/')."') AS new_appt_date, DATE_FORMAT(ps.old_date,'".get_sql_date_format('','y','/')."') AS old_appt_date, 
							TIME_FORMAT(ps.new_appt_start_time,'%h:%i %p') AS new_appt_start_time, TIME_FORMAT(ps.old_time,'%h:%i %p') AS old_appt_start_time, 
							TIME_FORMAT(ps.new_appt_end_time,'%h:%i %p') AS new_appt_end_time, TIME_FORMAT(ps.old_appt_end_time,'%h:%i %p') AS old_appt_end_time, 
							ps.new_facility, ps.old_facility, 
							ps.new_provider, ps.old_provider, 
							ps.new_procedure_id, ps.old_procedure_id, 
							ps.statusChangedBy, ps.oldMadeBy, 
							oldsp.proc AS oldProc, newsp.proc AS newProc,
							oldsp.acronym AS oldProcA, newsp.acronym AS newProcA,
							oldf.name AS oldFac, newf.name AS newFac, 
							oldU.fname AS oldProvFN, newU.fname AS newProvFN,  							
							oldU.lname AS oldProvLN, newU.lname AS newProvLN,
							change_reason 
						FROM 
							previous_status AS ps 
						LEFT JOIN slot_procedures AS oldsp ON oldsp.id = ps.old_procedure_id 
						LEFT JOIN slot_procedures AS newsp ON newsp.id = ps.new_procedure_id 
						LEFT JOIN facility AS oldf ON oldf.id = ps.old_facility 
						LEFT JOIN facility AS newf ON newf.id = ps.new_facility 
						LEFT JOIN users AS oldU ON oldU.id = ps.old_provider 
						LEFT JOIN users AS newU ON newU.id = ps.new_provider 
						WHERE 
							ps.sch_id = $schid 
							AND ps.status IN ( 1, 3, 4, 5, 6, 11, 18, 13, 14, 17, 2, 0, 21, 22, 23,'', 201, 202, 203, 271) 
							AND ps.patient_id ='".$pat_id."' 
						ORDER BY ps.id DESC"; 
	
	$vsql_c = imw_query($vquery_c);
	$vsql_c1 = imw_query($vquery_c);
	if($mode != "print"){
		echo("<div style=\"display:block;\">");
	}
	$intResetCnt = 0;
	?>
	<Table cellpadding="0" cellspacing="0" width="100%">
			<tr bgcolor="#4684ab">
				<td colspan="3" <?php if($mode == "print"){ ?>width="740"<?php } ?> <?php if($mode == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_b_w\""; } ?>><strong>History</strong> </td>
			</tr>
	<?php 
	$intZeroCnt = 0;
	while($vrs1=imw_fetch_array($vsql_c1)){
		if($vrs1['status'] == 0){
			$intZeroCnt++;
		}
	}
	while($vrs=imw_fetch_array($vsql_c)){
		
		if ($vrs['status_date'] && get_number($vrs['status_date']) != "0") {									
			$tmp_date = $vrs['status_date'];
			//list($year, $month, $day) = split('-',$tmp_date);
			//$create_date = $month."/".$day."/".$year;
			$create_date=get_date_format($tmp_date);
		}
		$appoint_time_to=$vrs['status_time'];
		$time_hourt=substr($appoint_time_to,0,2);
		$time_minutet=substr($appoint_time_to,3,2);
		$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
		$strOpChangedByName = getOperatorInitialByUsername($vrs['statusChangedBy']);
		$st_id=$vrs['status'];			
		if($CommonAppStatusArr[$st_id])$stttt_us=$CommonAppStatusArr[$st_id];
		else $stttt_us='Unknown Status';
			
		?>
		
			<tr  bgcolor="#FFFFFF" class="text_9">
				<td align="left" <?php if($mode == "print"){ ?>width="740"<?php } ?> <?php if($mode == "tiny"){ echo "class=\"text_9\""; } ?> colspan="3" height="25">
					<table width="100%" border="0" align="left" cellpadding="3" cellspacing="2" vspace="0" bordercolor="#CCCCCC">
						<tr>
							<td width="15%" align="left" class="text_10b">
								<strong><?php echo($stttt_us);?></strong>
							</td>
							<td width="15%" align="left" class="text_10b">
								<?php echo($create_date);?>
							</td>
							<td width="15%" style="text-align:right;" class="text_10b">
								<?php echo($time_t);?>
							</td>
							<td width="40%" style="text-align:center" class="text_10b">
								<?php echo stripslashes($vrs["change_reason"]);?>
							</td>
							<td width="15%" style="text-align:center;"class="text_10b">
								<?php echo($strOpChangedByName);?>
							</td>
							
						</tr>
					</table>
				</td>
			</tr>
		<?php 
//		if($st_id==202){
			if($mode == "tiny"){
				?>
			<tr bgcolor="#ECE9D8" class="text_9">
				<td colspan="3" width="47%"  align="left" class="text_9b">&nbsp;<i>Old Details</i>&nbsp;
					<table width="100%" border="0" align="left" cellpadding="3" cellspacing="2" vspace="0" bordercolor="#CCCCCC">	
						<TR bgcolor="#C0D7E2" height="20">
							<td width="15%" align="center" nowrap="nowrap" class="text_9b">Date</td>
							<td  width="15%" align="center" nowrap="nowrap" class="text_9b">Time</td>
							<td  width="5%" align="left" class="text_9b">Phy.</td>
							<td  width="5%" align="left" class="text_9b">Loc</td>
							<td  width="5%" align="left" class="text_9b">Pro.</td>
							<td  width="43%" align="left" class="text_9b">Comments</td>		
						</TR>			
						<tr  valign="top">
							<td align="left" nowrap="nowrap" class="text_9"><?php echo $vrs['old_appt_date'];?></td>
							<td align="left" nowrap="nowrap" class="text_9"><?php echo  $vrs['old_appt_start_time'];?></td>							
							<td  align="left" class="text_9"><?php echo strtoupper(substr($vrs['oldProvFN'],0,1))." ".strtoupper(substr($vrs['oldProvLN'],0,1));?></td>
							<td align="left" class="text_9"> <?php echo getFacilityNameInitial($vrs['oldFac']);?></td>
							<td align="left" class="text_9"><?php echo $vrs['oldProcA'];?></td>	
							<td align="left" class="text_9" nowrap="nowrap"><?php echo $vrs['oldStatusComments']; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor="#ECE9D8" class="text_9">
				<td width="47%" colspan="3" align="left" class="text_9b">&nbsp;<i>New Details</i>&nbsp;
					<table width="100%" border="0" align="left" cellpadding="3" cellspacing="2" vspace="0" bordercolor="#CCCCCC">	
						<TR bgcolor="#C0D7E2" height="20">
							<td width="15%" align="center" nowrap="nowrap" class="text_9b">Date</td>
							<td  width="15%" align="center" nowrap="nowrap" class="text_9b">Time</td>
							<td  width="5%" align="left" class="text_9b">Phy.</td>
							<td  width="5%" align="left" class="text_9b">Loc</td>
							<td  width="5%" align="left" class="text_9b">Pro.</td>
							<td  width="43%" align="left" class="text_9b">Comments</td>							
						</TR>				
						<tr  valign="top">
							<td align="left" nowrap="nowrap" class="text_9"><?php echo $vrs['new_appt_date'];?></td>
							<td align="left" nowrap="nowrap" class="text_9"><?php echo  $vrs['new_appt_start_time'];?></td>							
							<td  align="left" class="text_9"><?php echo strtoupper(substr($vrs['newProvFN'],0,1))." ".strtoupper(substr($vrs['newProvLN'],0,1));?></td>
							<td align="left" class="text_9"> <?php echo getFacilityNameInitial($vrs['newFac']);?></td>
							<td align="left" class="text_9"><?php echo $vrs['newProcA'];?></td>	
							<td align="left" class="text_9" nowrap="nowrap"><?php echo $vrs['statusComments']; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
			}else{
			?>
			<tr bgcolor="#ECE9D8" class="text_9">
				<td <?php if($mode == "print"){ ?>width="365"<?php }else{ ?>width="47%"<?php } ?> align="left" class="text_9b"><?php if($mode != "print"){ ?>&nbsp;<i>Old Details</i>&nbsp;<?php } ?>
					<table width="100%" border="0" align="left" cellpadding="3" cellspacing="2" vspace="0" bordercolor="#CCCCCC">	
						<TR bgcolor="#C0D7E2" height="20">
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="15%" <?php } ?> align="center" nowrap="nowrap" class="text_9b">Date</td>
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="15%" <?php } ?> align="center" nowrap="nowrap" class="text_9b">Time</td>
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="5%" <?php } ?> align="left" class="text_9b">Phy.</td>
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="5%" <?php } ?> align="left" class="text_9b">Loc</td>
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="5%" <?php } ?> align="left" class="text_9b">Pro.</td>
							<td <?php if($mode == "print"){ ?> width="165" <?php }else{ ?> width="43%" <?php } ?> align="left" class="text_9b">Comments</td>		
						</TR>			
						<tr  valign="top">
							<td align="left" nowrap="nowrap" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>><?php echo $vrs['old_appt_date'];?></td>
							<td align="left" nowrap="nowrap" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>><?php echo  $vrs['old_appt_start_time'];?></td>							
							<td  align="left" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>><?php echo $vrs['oldProvFN']." ".$vrs['oldProvLN'];?></td>
							<td align="left" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>> <?php echo $vrs['oldFac'];?></td>
							<td align="left" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>><?php echo $vrs['oldProc'];?></td>	
							<td align="left" <?php if($mode != "print"){ ?> class="text_9" <?php } ?> nowrap="nowrap"><?php echo $vrs['oldStatusComments']; ?></td>
						</tr>
					</table>
				</td>
				<td <?php if($mode == "print"){ ?>width="10"<?php }else{ ?>width="6%"<?php } ?>>&nbsp;</td>
				<td <?php if($mode == "print"){ ?>width="365"<?php }else{ ?>width="47%"<?php } ?> align="left" class="text_9b"><?php if($mode != "print"){ ?>&nbsp;<i>New Details</i>&nbsp;<?php } ?>
					<table width="100%" border="0" align="left" cellpadding="3" cellspacing="2" vspace="0" bordercolor="#CCCCCC">	
						<TR bgcolor="#C0D7E2" height="20">
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="15%" class="text_9b" <?php } ?> align="center" nowrap="nowrap" >Date</td>
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="15%" class="text_9b" <?php } ?> align="center" nowrap="nowrap" >Time</td>
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="5%" class="text_9b" <?php } ?> align="left" >Phy.</td>
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="5%" class="text_9b" <?php } ?> align="left" >Loc</td>
							<td <?php if($mode == "print"){ ?> width="40" <?php }else{ ?> width="5%" class="text_9b" <?php } ?> align="left" >Pro.</td>
							<td <?php if($mode == "print"){ ?> width="165" <?php }else{ ?> width="43%" class="text_9b" <?php } ?> align="left" >Comments</td>							
						</TR>				
						<tr  valign="top">
							<td align="left" nowrap="nowrap" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>><?php echo $vrs['new_appt_date'];?></td>
							<td align="left" nowrap="nowrap" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>><?php echo  $vrs['new_appt_start_time'];?></td>							
							<td  align="left" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>><?php echo $vrs['newProvFN']." ".$vrs['newProvLN'];?></td>
							<td align="left" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>> <?php echo $vrs['newFac'];?></td>
							<td align="left" <?php if($mode != "print"){ ?> class="text_9" <?php } ?>><?php echo $vrs['newProc'];?></td>	
							<td align="left"<?php if($mode != "print"){ ?> class="text_9" <?php } ?> nowrap="nowrap"><?php echo $vrs['statusComments']; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
			}
//		}

		?>
		
		<?php
		$counterP=1;
	}
	
	if($counterP==0 && $mode != "print"){
		?>		
			<tr bgcolor="#ffffff" class="text_9">
				<td width="100%" align="center" <?php if($mode == "tiny"){ echo "class=\"text_9\""; } ?> colspan="3" height="25">No History Found.</td>
			</tr>		
		<?php
	}
	?>
	</table>
	<?php
	if($mode != "print"){
		echo("</div>");
	}
}	

function get_one_schdeule_details_new($pat_id,$schid="",$showAll="", $mode = ""){
	$counterP=0;
	$vquery_c = "SELECT 
			ps.sch_id, ps.dateTime, ps.patient_id, ps.status_time, ps.status_date, 
			ps.status, ps.old_status, 
			ps.statusComments, ps.oldStatusComments, 
			DATE_FORMAT(ps.new_appt_date,'".get_sql_date_format('','y','/')."') AS new_appt_date, DATE_FORMAT(ps.old_date,'".get_sql_date_format('','y','/')."') AS old_appt_date, 
			TIME_FORMAT(ps.new_appt_start_time,'%h:%i %p') AS new_appt_start_time, TIME_FORMAT(ps.old_time,'%h:%i %p') AS old_appt_start_time, 
			TIME_FORMAT(ps.new_appt_end_time,'%h:%i %p') AS new_appt_end_time, TIME_FORMAT(ps.old_appt_end_time,'%h:%i %p') AS old_appt_end_time, 
			ps.new_facility, ps.old_facility, 
			ps.new_provider, ps.old_provider, 
			ps.new_procedure_id, ps.old_procedure_id, 
			ps.statusChangedBy, ps.oldMadeBy, 
			oldsp.proc AS oldProc, newsp.proc AS newProc,
			oldsp.acronym AS oldProcA, newsp.acronym AS newProcA,
			oldf.name AS oldFac, newf.name AS newFac, 
			oldU.fname AS oldProvFN, newU.fname AS newProvFN,  							
			oldU.lname AS oldProvLN, newU.lname AS newProvLN,
			change_reason 
		FROM 
			previous_status AS ps 
		LEFT JOIN slot_procedures AS oldsp ON oldsp.id = ps.old_procedure_id 
		LEFT JOIN slot_procedures AS newsp ON newsp.id = ps.new_procedure_id 
		LEFT JOIN facility AS oldf ON oldf.id = ps.old_facility 
		LEFT JOIN facility AS newf ON newf.id = ps.new_facility 
		LEFT JOIN users AS oldU ON oldU.id = ps.old_provider 
		LEFT JOIN users AS newU ON newU.id = ps.new_provider 
		WHERE 
			ps.sch_id = $schid 
			AND ps.patient_id ='".$pat_id."' 
		ORDER BY ps.id ASC"; 
	
	/*AND ps.status IN ( 1, 3, 4, 5, 6, 11, 18, 13,17,2,0,21,22,23,'', 201,202,203, 271) */
	
	$vsql_c = imw_query($vquery_c);
	if(imw_num_rows($vsql_c)<=0 && $GLOBALS["CHK_ARCHIVE_TABLE"])
	{
		$vquery_c = "SELECT 
			ps.sch_id, ps.dateTime, ps.patient_id, ps.status_time, ps.status_date, 
			ps.status, ps.old_status, 
			ps.statusComments, ps.oldStatusComments, 
			DATE_FORMAT(ps.new_appt_date,'".get_sql_date_format('','y','/')."') AS new_appt_date, DATE_FORMAT(ps.old_date,'".get_sql_date_format('','y','/')."') AS old_appt_date, 
			TIME_FORMAT(ps.new_appt_start_time,'%h:%i %p') AS new_appt_start_time, TIME_FORMAT(ps.old_time,'%h:%i %p') AS old_appt_start_time, 
			TIME_FORMAT(ps.new_appt_end_time,'%h:%i %p') AS new_appt_end_time, TIME_FORMAT(ps.old_appt_end_time,'%h:%i %p') AS old_appt_end_time, 
			ps.new_facility, ps.old_facility, 
			ps.new_provider, ps.old_provider, 
			ps.new_procedure_id, ps.old_procedure_id, 
			ps.statusChangedBy, ps.oldMadeBy, 
			oldsp.proc AS oldProc, newsp.proc AS newProc,
			oldsp.acronym AS oldProcA, newsp.acronym AS newProcA,
			oldf.name AS oldFac, newf.name AS newFac, 
			oldU.fname AS oldProvFN, newU.fname AS newProvFN,  							
			oldU.lname AS oldProvLN, newU.lname AS newProvLN,
			change_reason 
		FROM 
			previous_status_archive AS ps 
		LEFT JOIN slot_procedures AS oldsp ON oldsp.id = ps.old_procedure_id 
		LEFT JOIN slot_procedures AS newsp ON newsp.id = ps.new_procedure_id 
		LEFT JOIN facility AS oldf ON oldf.id = ps.old_facility 
		LEFT JOIN facility AS newf ON newf.id = ps.new_facility 
		LEFT JOIN users AS oldU ON oldU.id = ps.old_provider 
		LEFT JOIN users AS newU ON newU.id = ps.new_provider 
		WHERE 
			ps.sch_id = $schid 
			AND ps.patient_id ='".$pat_id."' 
		ORDER BY ps.id ASC"; 
		$vsql_c = imw_query($vquery_c);
	}
	$vsql_c1 = imw_query($vquery_c);
	
	$intResetCnt = 0;
	$intZeroCnt = 0;
	while($vrs1=imw_fetch_array($vsql_c1)){
		if($vrs1['status'] == 0){
			$intZeroCnt++;
		}
	}
	
	$previousDateTime = '';
	$previousInitials = '';
	$HxArr = array();
	while($vrs=imw_fetch_array($vsql_c)){
		
		if ($vrs['status_date'] && get_number($vrs['status_date']) != "0") {									
			$tmp_date = $vrs['status_date'];
			//list($year, $month, $day) = split('-',$tmp_date);
			//$create_date = $month."/".$day."/".$year;
			$create_date=get_date_format($tmp_date);
		}
		$appoint_time_to=$vrs['status_time'];
		$time_hourt=substr($appoint_time_to,0,2);
		$time_minutet=substr($appoint_time_to,3,2);
		$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
		$strOpChangedByName = get_operator_initial_by_username($vrs['statusChangedBy']);
		
		$st_id=$vrs['status'];			
		
		if($st_id == 201){
			$stttt_us = "Moved to Re-Schedule";
		}else if($st_id == 271){
			$stttt_us = "First Available";
		}else if($st_id == 18)	{
			$stttt_us = "Cancelled";
		}else if($st_id=='')	{
			$stttt_us = "Deleted";
		}else if($st_id == '0'){
			if($intZeroCnt > 1){
				$stttt_us = "Restored";
				if($intResetCnt == 0){
					$stttt_us = "Created";
				}
			}else{
				$stttt_us = "Created";
			}
			$intResetCnt++;
		}elseif ($st_id == 202){
			$stttt_us = "Rescheduled";
		}
		elseif($st_id == 203)
		{
			$stttt_us = "Deleted";			
		}
		else{
			$vquery_st = "SELECT status_name FROM `schedule_status` WHERE id = $st_id";
			$vsql_st = imw_query($vquery_st);	
			$rs_st = imw_fetch_array($vsql_st);
			$stttt_us=$rs_st['status_name'];
		}	
		
		$old_valHTML = '
		<b>Appt Date Time:</b> '.$vrs['old_appt_date'].' '.$vrs['old_appt_start_time'].'<br>
		<b>Phy. :</b>  '.$vrs['oldProvFN']." ".$vrs['oldProvLN'].'<br>
		<b>Loc. :</b> '.$vrs['oldFac'].' <br>
		<b>Proc. :</b>  '.$vrs['oldProc'].'<br>
		<b>Comment:</b>  '.$vrs['oldStatusComments'];
		
		$new_valHTML = '
		<b>Appt Date Time:</b> '.$vrs['new_appt_date'].' '.$vrs['new_appt_start_time'].'<br>
		<b>Phy. :</b>  '.$vrs['newProvFN']." ".$vrs['newProvLN'].'<br>
		<b>Loc. :</b> '.$vrs['newFac'].' <br>
		<b>Proc. :</b>  '.$vrs['newProc'].'<br>
		<b>Comment:</b>  '.$vrs['statusComments'];
		
		if( $stttt_us == 'Created') $old_valHTML = '';
		
		$data = array();
		$data['action'] = $stttt_us;
		$data['reason'] = $vrs["change_reason"];
		$data['old_val'] = $old_valHTML;
		$data['old_date_time'] = $previousDateTime;
		$data['old_initials'] = $previousInitials;
		$data['new_val'] = $new_valHTML;
		$data['new_date_time'] = $create_date.' '.$time_t;
		$data['new_initials'] = $strOpChangedByName;

		$previousDateTime = $create_date.' '.$time_t;
		$previousInitials = $strOpChangedByName;
		
		$HxArr[] = $data;
		$counterP=1;
	}
	
	krsort($HxArr);
	$HxHTML = '';
	$HxHTML .= '<table class="table table-striped table-bordered table-hover">';
	$HxHTML .= '<thead class="header">';
	$HxHTML .= '<tr class="grythead">';
	$HxHTML .= '<th>Action</th>';
	$HxHTML .= '<th>Reason</th>';
	$HxHTML .= '<th>Old Values</th>';
	$HxHTML .= '<th width="10%">Date Time</th>';
	$HxHTML .= '<th width="5%">Initials</th>';
	$HxHTML .= '<th>New Values</th>';
	$HxHTML .= '<th width="10%">Date Time</th>';
	$HxHTML .= '<th width="5%">Initials</th>';
	$HxHTML .= '</tr>';
	$HxHTML .= '</thead>';
	
	$HxHTML .= '<tbody>';
	if( is_array($HxArr) && count($HxArr) > 0 ) {
		foreach($HxArr as $_data) {
			$HxHTML .= '<tr>';
			$HxHTML .= '<td><strong>'.$_data['action'].'</strong></td>';
			$HxHTML .= '<td>'.stripslashes($_data['reason']).'</td>';
			$HxHTML .= '<td>'.$_data['old_val'].'</td>';
			$HxHTML .= '<td>'.$_data['old_date_time'].'</td>';
			$HxHTML .= '<td class="text-center">'.$_data['old_initials'].'</td>';
			$HxHTML .= '<td>'.$_data['new_val'].'</td>';
			$HxHTML .= '<td>'.$_data['new_date_time'].'</td>';
			$HxHTML .= '<td class="text-center">'.$_data['new_initials'].'</td>';
			$HxHTML .= '</tr>';
		}
	}
	else {
		$HxHTML .= '<tr><td colspan="5">No History Found.</td></tr>';
	}
	$HxHTML .= '</tbody>';
	$HxHTML .= '</table>';

	echo $HxHTML;	
}
?>