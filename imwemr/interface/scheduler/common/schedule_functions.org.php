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
//to check privileges
function checkPrivilege($strPrivilege){
	return 0; //use core_check_session function - amit
}
//to convert date into timestamp
function convertToTimestamp($dtTemp){
	$arrDtFrom = explode("-",$dtTemp);
	$yrsFrom = $arrDtFrom[0];
	$mthFrom = $arrDtFrom[1];		
	$dysFrom = $arrDtFrom[2];
	$tsFrom = mktime(0,0,0,$mthFrom,$dysFrom,$yrsFrom);
	return $tsFrom;
}

//to return fetch records in array form
function fetchArray($queryID=-1){
	$arrRecords = array();
	$k = 0;
	while($record = imw_fetch_assoc($queryID)) {
		foreach($record as $key=>$val){
			$arrRecords[$k][$key] = $val;
		}
		$k++;
	}
	return $arrRecords;
}


//to get the week count
function getWeekCount($date){
	$week = ceil($date/7);
	return $week;
}

//getting details of schedule provider, facility etc. w.r.t. to the date and day
function getFacility($dtScheduler, $strFacilityIds){
	
	$arrDtScheduler = explode("-", $dtScheduler);
	$intYear = $arrDtScheduler[0];
	$intMonth = $arrDtScheduler[1];	
	$intDay = $arrDtScheduler[2];
		
	$intWeekNumber = getWeekCount($intDay);
	$intWeekday = date("w", mktime(0,0,0,$intMonth,$intDay,$intYear));
	
	$arrDeletedIds = array();
	$strDeletedId = "";
	
	//------ Query To get The Exception Deleted Data ----------
	$strQry = "SELECT provider, facility, sch_tmp_id, week1, week2, week3, week4, week5, week6 FROM provider_schedule_tmp WHERE today_date = '".$dtScheduler."' AND facility IN (".$strFacilityIds.") AND del_status = '1' AND delete_row = 'no'";
	$rsData = imw_query($strQry);
	if(imw_num_rows($rsData) > 0){
		while($arrRow = imw_fetch_array($rsData)){
			$strQuery = "	SELECT id 
						FROM provider_schedule_tmp 
						WHERE provider = '".$arrRow['provider']."' AND facility = '".$arrRow['facility']."' AND sch_tmp_id = '".$arrRow['sch_tmp_id']."' 
						AND week1 = '".$arrRow['week1']."' AND week2 = '".$arrRow['week2']."' AND week3 = '".$arrRow['week3']."'
						AND week4 = '".$arrRow['week4']."' AND week5 = '".$arrRow['week5']."' AND week6 = '".$arrRow['week6']."'";
			
			$rsSubData = imw_query($strQuery);
			if(imw_num_rows($rsSubData) > 0){
				while($arrSubRow = imw_fetch_array($rsSubData)){
					$arrDeletedIds[] = $arrSubRow['id'];	
				}
			}
		}
	}
						
	//------ Query To get The All Deleted Data ----------
	$strQry = "SELECT provider, facility, sch_tmp_id, week1, week2, week3, week4, week5, week6 FROM provider_schedule_tmp WHERE today_date <= '".$dtScheduler."' AND facility IN (".$strFacilityIds.") AND del_status = '1' AND delete_row = 'all'";
	$rsData = imw_query($strQry);
	if(imw_num_rows($rsData) > 0){
		while($arrRow = imw_fetch_array($rsData)){
			$arrDeletedIds[] = $arrRow['id'];
			$today_date = $res['today_date'];
			$strQuery = "	SELECT id 
							FROM provider_schedule_tmp 
							WHERE provider = '".$arrRow['provider']."' AND facility = '".$arrRow['facility']."' AND sch_tmp_id = '".$arrRow['sch_tmp_id']."' 
							AND week1 = '".$arrRow['week1']."' AND week2 = '".$arrRow['week2']."' AND week3 = '".$arrRow['week3']."'
							AND week4 = '".$arrRow['week4']."' AND week5 = '".$arrRow['week5']."' AND week6 = '".$arrRow['week6']."' 
							AND id < '".$arrRow['id']."'";
			$rsSubData = imw_query($strQuery);
			if(imw_num_rows($rsSubData) > 0){
				while($arrSubRow = imw_fetch_array($rsSubData)){
					$arrDeletedIds[] = $arrSubRow['id'];
				}
			}
		}
	}
	
	if(isset($arrDeletedIds)){
		$strDeletedId = implode(",", $arrDeletedIds);
	}
					
	//------- Query For Exceptional Data -----------
	if($strDeletedId != ""){
		$strQry = "	SELECT provider, facility, sch_tmp_id 
					FROM provider_schedule_tmp 
					WHERE today_date <= '".$dtScheduler."' AND facility IN (".$strFacilityIds.") AND week$intWeekNumber = '".$intWeekday."' AND del_status = 0 AND status='yes' AND delete_row = '' AND id NOT IN (".$strDeletedId.") GROUP BY provider, sch_tmp_id, facility";			
	}else{
		$strQry = "	SELECT provider, facility, sch_tmp_id 
					FROM provider_schedule_tmp 
					WHERE today_date <= '".$dtScheduler."' AND facility IN (".$strFacilityIds.") AND week$intWeekNumber = '".$intWeekday."' AND del_status = 0 AND status='yes' AND delete_row = '' GROUP BY provider, sch_tmp_id, facility";
	}
	$rsData = imw_query($strQry);
	if(imw_num_rows($rsData) > 0){
		$arrFinalResult1 = fetchArray($rsData);
	}
					
	if($strDeletedId != ""){
		$strQry = "	SELECT provider, facility, sch_tmp_id 
					FROM provider_schedule_tmp 
					WHERE today_date = '".$dtScheduler."' AND facility IN (".$strFacilityIds.") AND week$intWeekNumber = '".$intWeekday."' AND del_status = 0 AND status='no' AND delete_row = '' AND id NOT IN (".$strDeletedId.") GROUP BY provider, sch_tmp_id, facility";			
	}else{
		$strQry = "	SELECT provider, facility, sch_tmp_id 
					FROM provider_schedule_tmp 
					WHERE today_date = '".$dtScheduler."' AND facility IN (".$strFacilityIds.") AND week$intWeekNumber = '".$intWeekday."' AND del_status = 0 AND status='no' AND delete_row = '' GROUP BY provider, sch_tmp_id, facility";
					
	}
	$rsData = imw_query($strQry);
	if(imw_num_rows($rsData) > 0){
		$arrFinalResult2 = fetchArray($rsData);
	}
					
	$arrFinalResult = array_merge((array)$arrFinalResult1,(array)$arrFinalResult2);		
	return $arrFinalResult;
}


///////////////
function getSuperBillCharge($fld,$patientId,$phy_id=''){
		if($phy_id){
			$qry = "select sum($fld) from patient_charge_list where del_status='0' and 
					and primaryPhysicianId in ('$phy_id') and patient_id in ('$patientId')";
		}
		else{
			$qry = "select sum($fld) from patient_charge_list where del_status='0' and patient_id in ('$patientId')";

		}
		//echo $qry;
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$qryRes = imw_fetch_array($qryId);
		}
		return $qryRes;
	}
	
	
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
	// end here
	
function checking_procedure_status($id){
	$qr="select active_status  from slot_procedures where id=$id";			
	$m=imw_query($qr);
	if(imw_num_rows($m)>0){
		list($status)=imw_fetch_array($m);
		return $status;
	}
}
	
function gettime($tm_t,$type){
	
	if($tm_t=="" && $type=="end"){
		$h2=explode(":","23:59");
		$hr2=($h2[0]);
		$mins2=$h2[1];
	}else{
		$tm_ts=explode("|",$tm_t);
		if($tm_ts[1]==1){
			$h2=explode(":",$tm_ts[0]);
			$hr2=($h2[0]);
			$mins2=$h2[1];
		
		}else {
			if($tm_ts[0]!=12){
				$h2=explode(":",$tm_ts[0]);
				$hr2=($h2[0])+12; // one hour more for facility to scroll
				$mins2=$h2[1];
			}else{
				$h2=explode(":",$tm_ts[0]);
				$hr2=($h2[0]); // one hour more for facility to scroll
				$mins2=$h2[1];
			}
		
		}
	}
	
	$hr=array($hr2,$mins2);
	return $hr;
}
function addDoubleQuaote($stringVal){
	if($stringVal!=""){
	 $stringVal='"'.$stringVal.'"';
	 }
	 return $stringVal;
}
function showThumbImages($fileName,$targetWidth=1,$targetHeight=1)
{	
	if(file_exists($fileName))
	{	
		 $img_size=getimagesize($fileName);
		 $width=$img_size[0];
		 $height=$img_size[1];
		 
		 do
		 {
			 if($width > $targetWidth)
			 {
				$width=$targetWidth;
				$percent=$img_size[0]/$width;
				$height=$img_size[1]/$percent; 
			 }
			 if($height > $targetHeight)
			 {
				$height=$targetHeight;
				$percent=$img_size[1]/$height;
				$width=$img_size[0]/$percent; 
			 }
			 
		 }while($width > $targetWidth || $height > $targetHeight);
		
		return "<img src='$fileName' width='$width' height='$height' id='imgfrontDeskPatient'>";		
	 }	 
	 return "";
}

function getProcedureName($id){
	$qry="select proc,acronym from slot_procedures where id=$id";
	$r=imw_query($qry);
	if(imw_num_rows($r)>0){
		list($proc,$acronym)=imw_fetch_array($r);
	}
	return $proc;
}

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


 

function getpatient_Insurance($patient_id){
	$insur_Det=array();
	$qry_chkses=imw_query("select ins_caseid from insurance_case where patient_id='".$patient_id."' and  case_status='Open' order by ins_case_type  LIMIT 0 ,1");
	if(imw_num_rows($qry_chkses)>0){
		$res_ses=imw_fetch_array($qry_chkses);
		 $current_caseids=$res_ses["ins_caseid"];
	}
	$typ=array("'primary'","'secondary'","'tertiary'");
	$type=implode(",",$typ);
	$Ins_nameqry=imw_query("SELECT b.case_name FROM `insurance_case` a ,insurance_case_types b WHERE b.case_id=a.ins_case_type and a.ins_caseid=$current_caseids");
	list($ins_name)=imw_fetch_array($Ins_nameqry);	
	$qry="select insurance_data.id,insurance_data.type,
		insurance_data.policy_number,
		insurance_companies.name 
	 	from insurance_data
		JOIN 
		insurance_companies ON insurance_companies.id=insurance_data.provider
		where
	  	insurance_data.pid='$patient_id' and insurance_data.actInsComp=1
		and (insurance_data.type in($type))
		and insurance_data.ins_caseid ='$current_caseids'
		/*and referal_required='Yes'*/  order by insurance_data.type";
	//echo $qry;
	$r=imw_query($qry);
	$instype=array();
	$policy=array();
	if(imw_num_rows($r)>0){
		while($rw=imw_fetch_array($r)){
			
			$insprovider[]=ucwords($rw[3]);
			$policy[]=$rw[2]."#";
		}
		
		$insurance_provider=@implode("<br>",($insprovider));
		$policy_number=@implode("<br>",$policy);
		/*$qry="select reffral_no  from patient_reff where 
			patient_id='$patient_id'
			 and ins_data_id='$id'
			 and  ((patient_reff.end_date >= current_date() and 
			 patient_reff.effective_date <= current_date())
			or(patient_reff.no_of_reffs > 0))
			  order by effective_date desc,reff_id desc limit 0,1";
		//echo $qry;
		$r=imw_query($qry);
		list($reffral_no)=imw_fetch_array($r);	*/ 
		$insur_Det[0]=ucwords($insurance_provider);
		$insur_Det[1]=$policy_number;
	}else{
		$insur_Det=array("N/A","N/A");
	}
	return $insur_Det;
}

function get_active_ins($patient_id,$type){
		$qry=imw_query("Select id from insurance_data where pid=$patient_id and type='$type' and actInsComp='1'");
		list($id)=imw_fetch_array($qry);
		//for primary
			if($type=="primary"){
				$ref_type=1;
			}else if($type=="secondary"){
				$ref_type=2;
			}else if($type=="tertiary"){
				$ref_type=3;
			}
			
			$qr1=imw_query("select no_of_reffs,reff_id,reff_used from patient_reff where patient_id=$patient_id 
				and ins_data_id='$id' and ins_data_id!='' and reff_type='$ref_type' group by reff_id limit 0,1");
			list($no_of_reffs,$reff_id,$reff_used)=imw_fetch_array($qr1);
			$qr2=imw_query("update patient_reff set no_of_reffs='".($no_of_reffs-1)."' ,reff_used='".($reff_used+1)."' where reff_id=$reff_id");
			
			return $no_of_reffs;
}

function template_name($schtmpid,$times_from){
	$template_chk_qry11="select template_label  from schedule_label_tbl where sch_template_id  in($schtmpid) and start_time <='$times_from' and end_time > '$times_from'";
	$template_chk_res12=imw_query($template_chk_qry11);
	if(imw_num_rows($template_chk_res12)>0){
		while($label_row1=imw_fetch_array($template_chk_res12)){
			$label_name1[]=$label_row1[0];
		}
	}
	$template_chk_qry11="select label  from schedule_templates where id in($schtmpid) and morning_start_time<='$times_from' and morning_end_time > '$times_from'";
	$template_chk_res11=imw_query($template_chk_qry11);
	if(imw_num_rows($template_chk_res11)>0){
		while($label_row=imw_fetch_array($template_chk_res11)){
			$label_name[]=$label_row[0];
		}	
	}
	if(sizeof($label_name1)>0){
		$label=implode(",",$label_name1);
	}else{
		$label=implode(",",$label_name);
	}
	return $label;
}

function patient_name($id){
	$template_chk_qry11="select id,fname,lname,DOB  from patient_data where id=$id"; 
	$template_chk_res11=imw_query($template_chk_qry11);
	$pat_dt=array();
	$label_row=imw_fetch_array($template_chk_res11);
	$pat_dt[0]=$label_row['fname']."&nbsp;".$label_row['lname']." - ".$label_row['id'];
	$pat_dt[1]=$label_row['DOB'];	
	$pat_dt[2]=$label_row['fname']."&nbsp;".$label_row['lname'];
	
	return $pat_dt;
}

function patient_data($id){
	$qry="select * from patient_data where id='$id'";
	$template_chk_res11=imw_query($qry);
	$pat_dts=array();
	$label_row=imw_fetch_array($template_chk_res11);
	$pat_dts[0]=$label_row['fname']."&nbsp;".$label_row['lname'];
	$pat_dts[1]=$label_row['street'];
	$pat_dts[2]=$label_row['street2'];
	
	if($label_row['city']<>"" && $label_row['state']<>"" && $label_row['postal_code']<>""){
		$pat_dts[3]=$label_row['city'].", ".$label_row['state']."&nbsp;".$label_row['postal_code'];
	}
	$pat_dts[4]=$label_row['DOB'];
	$pat_dts[5]=trim($label_row['city']);
	$pat_dts[6]=$label_row['state'];
	$pat_dts[7]=$label_row['postal_code'];

	 $phone_home = $label_row['phone_home'];
	 $phone_cell = $label_row['phone_cell'];
	 $phone_work = $label_row['phone_biz'];
	if($phone_home<>""){ 
		$pat_dts[8]=$phone_home;
	}else if($phone_cell<>""){
		$pat_dts[8]=$phone_cell;	
	}else if($phone_work<>""){
		$pat_dts[8]=$phone_work;
	}
	if($label_row['mname']<>""){
		$pat_dts[9]=$label_row['lname'].", ".$label_row['fname']." ".substr($label_row['mname'],0,1);
	}else{
		$pat_dts[9]=$label_row['lname'].", ".$label_row['fname'];
	}
	return $pat_dts;
}

function sch_data($id){
	$vquery_sa = "select sa_madeby,sa_patient_app_status_id,procedureid,sa_doctor_id,sa_test_id,sa_facility_id,sa_patient_id,sa_patient_name,sa_comments from schedule_appointments where id='$id'/* and sa_patient_app_status_id=201*/";												
	$vsql_sa =imw_query($vquery_sa);
	$vrs_sa=imw_fetch_array($vsql_sa);							
	$sa_commentsAndReason=nl2br(stripslashes($vrs_sa["sa_comments"])); 
	$vquery_sp = "select proc_color,acronym,proc from slot_procedures where id='".$vrs_sa[procedureid]."'";												
	$vsql_sp = imw_query($vquery_sp);
	$vrs_sp=imw_fetch_array($vsql_sp);			
	$procedure=$vrs_sp['proc'];
	$provider_id=$vrs_sa['sa_doctor_id'];
	$tes_id=$vrs_sa['sa_test_id'];
	$fac_id=$vrs_sa['sa_facility_id'];
	$sa_patient_id=$vrs_sa['sa_patient_id'];
	$madeby=$vrs_sa['sa_madeby'];
	
	$tt_provider=" SELECT fname,mname,lname,user_type FROM `users` WHERE id=$provider_id ";							
	$sqltt_provider=imw_query($tt_provider);	
	$vrs_tcurr=imw_fetch_array($sqltt_provider);
	$provider_name=$vrs_tcurr['fname']." ".$vrs_tcurr['lname'];
	$st_id=$vrs_sa['sa_patient_app_status_id'];
	
	$tt_provider1="select fname,mname,lname,id from users where username='$madeby' ";							
	$sqltt_provider1=imw_query($tt_provider1);	
	$vrs_tcurr1=imw_fetch_array($sqltt_provider1);
	/*
	if($vrs_tcurr1['fname']<>"" && $vrs_tcurr1['lname']<>""){
		$operator=$vrs_tcurr1['fname'].", ".substr($vrs_tcurr1['lname'],0,1).".";
	}else{
			$operator=$vrs_tcurr1['fname'];
	}
	*/
	$operator=substr($vrs_tcurr1['lname'],0,1);
	if($vrs_tcurr1['fname']<>"" && $vrs_tcurr1['lname']<>""){
		$operator=substr($vrs_tcurr1['fname'],0,1)."".substr($vrs_tcurr1['lname'],0,1);
	}
	else if($vrs_tcurr1['fname']<>"" && $vrs_tcurr1['lname'] == ""){
		$operator=substr($vrs_tcurr1['fname'],0,1);
	}else{
			$operator=substr(ucfirst($madeby),0,1);
		}
	
	/*$st_provider=" SELECT * FROM `schedule_status` WHERE id=$st_id ";							
	$sqlst_provider=imw_query($st_provider);	
	$vrs_st=imw_fetch_array($sqlst_provider);		
	$icon=$vrs_st['status_name'];*/
	$sch_det=array($procedure,$provider_name,$operator,$provider_id,$st_id,$fac_id,$sa_commentsAndReason);
	return $sch_det;
}

function draw_schedule($ids,$divid,$nms,$divn,$al,$div_slot_height,$div_margin_top,$fc,$pr,$tm,$dt,$setcolumn_width,$sch_tmp_id,$all_columns="0")
{
	//-- Not use Var --
	/*
		$nms  , $divn , $al,$div_slot_height,$div_margin_top,$setcolumn_width
	*/
	
	if(count($ids)>0){
		list($m,$d,$y) = explode('-',$dt);
		$sel_date = $y.'-'.$m.'-'.$d;
		$app_id = join(',',$ids);
//		$qry = "select sch_div_html from scheduler_div_cache where sch_div_id = '$divn'
//				and sch_div_date = '$sel_date' and sch_appointments_id = '$app_id'
//				and sch_tmp_id = '$sch_tmp_id' and sch_div_status = '0' and sch_div_fac_id = '$fc'";
//		$qryId = imw_query($qry);
//		list($sch_div_html) = imw_fetch_array($qryId);
//		if(stripslashes($sch_div_html)){
//			print stripslashes($sch_div_html);
//		}
//		else{
			$divn = substr($divid,4);
			$imgid = "im".$divid;
			$tabid = "tab".$divid;
			$toolid = "tool".$divid;
			$tool_tab = "tool_tab".$divid;
			$patients = sizeof($ids);	
			if(sizeof($ids)>0){
				$setcolumn_width = 514;
				if($all_columns != ''){
					if($all_columns == 2){
						$setcolumn_width = 250;
					}
					else if($all_columns >= 3){
						$setcolumn_width = 161;
					}
				}
				$data .= "<div style='width:$setcolumn_width px; margin-top:$div_margin_top; position:absolute;' id='$divid' class='schedule_slip'>";
				$data .= "<table width='100%' cellpadding='0' border='0' id='$tabid' cellspacing='0'>";			
				for($x=0;$x<count($ids);$x++){	
					//-- Get Schedule Appointment Details ------
					$vquery_sa = "select sa_patient_app_status_id,procedureid,sa_patient_id,sa_patient_name from schedule_appointments where id = '$ids[$x]' order by sa_app_time desc";												
					$vsql_sa = imw_query($vquery_sa);
					$vrs_sa = imw_fetch_array($vsql_sa);
					$tes_id = $vrs_sa['sa_test_id'];
					$sa_patient_id = $vrs_sa['sa_patient_id'];
					//--- Get Procedure Name And Color --						
					$vquery_sp = "select proc_color,acronym from slot_procedures where id = '$vrs_sa[procedureid]'";												
					$vsql_sp = imw_query($vquery_sp);
					$vrs_sp = imw_fetch_array($vsql_sp);
					$slot_color = $vrs_sp['proc_color'];
					$test_proc = $vrs_sp['acronym'];
					//--- Get Patient Emr Status ----
					$emr_chk = imw_query("select EMR from patient_data where id='$sa_patient_id'");
					list($emr) = imw_fetch_array($emr_chk);
					if($emr == 1){
						$emrsymbol = "<b>e</b>";
					}
					//--- Get Status Name -----------
					$st_id = $vrs_sa['sa_patient_app_status_id'];
					$st_provider = " SELECT status_name,status_icon FROM `schedule_status` WHERE id = '$st_id' ";							
					$sqlst_provider = imw_query($st_provider);	
					$vrs_st = imw_fetch_array($sqlst_provider);
						
					if($x >= 3){
						$disp="style='display:none'";
					}
					$divHeight = floor(51/sizeof($ids));
						$data .=  "
							<tr id='disprow' $disp height='$divHeight' bgcolor='$slot_color'>
								<td valign='left' style='cursor::pointer;' bgcolor='$slot_color' width='10'>
									<a href=\"javascript:void(0);\" onClick='edit_schedule(\"".$ids[$x]."\",\"$next\",\"$sch_tmp_id\",\"$usr_id\",\"$fac_id\");'>"."
									<img src='' id='edit_appt_img' alt='Update'>"."</a>
							   </td>";
						$pp_menu = "onMouseDown = \"pop_menu('".$ids[$x]."','$fc','$pr','$tm','$dt');\"";
						$data .= "<td width='25' bgcolor='$slot_color'  id='".$ids[$x]."' class='schedule_text' $pp_menu onclick=\"javascript:refresh_patient_infopage('$sa_patient_id','".$ids[$x]."','".$vrs_sa['sa_doctor_id']."');\" ondblclick=\"drag_name_sch('".$ids[$x]."','$slot_color','$sa_patient_id');\" align='center' valign='middle'>";
						if($vrs_st['status_name']=="Check-in"){
							$data .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">CI</b>&nbsp;";
						}else if($vrs_st['status_name']=="Checked Out"){
							$data .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">CO</b>&nbsp;";
						}elseif($vrs_st['status_name']=="Left without visit"){
							$data .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">LV</b>&nbsp;";
						}elseif($vrs_st['status_name']=="No Show"){
							$data .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">NS</b>&nbsp;";
						}
						elseif($vrs_st['status_name']=="Chart Pulled"){
							$data .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">CP</b>&nbsp;";
						}elseif($vrs_st['status_name']=="Insurance/Financial Issue"){
							$data .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">FI</b>&nbsp;";
						}else if(($vrs_st['status_icon']<>"") && (file_exists("../../images/".$vrs_st[status_icon])))
						{
							$data .= "<img src='../../images/".$vrs_st['status_icon']."' alt='".$vrs_st['status_name']."' align='absmiddle'>";
						}
						$data .= "
							</td>			
							<td bgcolor='$slot_color' style='cursor:pointer;'  id='".$ids[$x]."' class='schedule_text'  $pp_menu  onclick=\"refresh_patient_infopage('$sa_patient_id','".$ids[$x]."','".$vrs_sa['sa_doctor_id']."');\" ondblclick=\"javascript:drag_name_sch('".$ids[$x]."','$slot_color','$sa_patient_id');\" align='left' valign='middle'> ";
								$display_name = $test_proc." - ".$vrs_sa[sa_patient_name];
								if(sizeof($ids)>=3 && $all_columns >= 3){
									$display_name = substr($test_proc." - ".$vrs_sa[sa_patient_name], 0, 12);
								}	
								$data .= '<span class="text_9b">'.$display_name.'</span>
							</Td>	
						</tr>';
				}
				$data .= '</table>
				</div>';
			}
			$qry = "insert into scheduler_div_cache set sch_div_id = '$divn',
					sch_div_date = '$sel_date',sch_appointments_id = '$app_id',
					sch_tmp_id = '$sch_tmp_id',sch_div_status = '0',
					sch_div_fac_id = '$fc',
					sch_div_html = '".addslashes($data)."'";
			imw_query($qry);
			print $data;
//		}
	}
}
// FUNCTION ENDS
//function drag_sch

function drag_sch($ids,$pt_id="",$sel_proc_idR=""){
		if($ids<>""){
			print "<table align='left'>";
			$vquery_sa = "select sa_patient_app_status_id,procedureid,sa_doctor_id,sa_test_id,sa_facility_id,sa_patient_id,sa_app_room,sa_patient_name from schedule_appointments where id='$ids' order by sa_app_time desc";												
			$vsql_sa = imw_query($vquery_sa);
			$vrs_sa=imw_fetch_array($vsql_sa);							
			if($sel_proc_idR<>""){
			$vquery_sp = "select proc_color,acronym from slot_procedures where id=$sel_proc_idR";	
			}else{
			$vquery_sp = "select proc_color,acronym from slot_procedures where id=$vrs_sa[procedureid]";	
			}											
			$vsql_sp = imw_query($vquery_sp);
			$vrs_sp=imw_fetch_array($vsql_sp);	
			
			$usr_id=$vrs_sa['sa_doctor_id'];

			$tt_provider=" SELECT fname,mname,lname,user_type FROM `users` WHERE id=$usr_id ";							
			$sqltt_provider=imw_query($tt_provider);	
			$vrs_tcurr=imw_fetch_array($sqltt_provider);	
			
			$pt_id=$vrs_sa['sa_patient_id'];
			$st_id=$vrs_sa['sa_patient_app_status_id'];
			
			$st_provider=" SELECT * FROM `schedule_status` WHERE id=$st_id ";							
			$sqlst_provider=imw_query($st_provider);	
			$vrs_st=imw_fetch_array($sqlst_provider);							
			
			$tes_id=$vrs_sa['sa_test_id'];
			$fac_id=$vrs_sa['sa_facility_id'];
			
			$tt_provider1=" SELECT * FROM `patient_tests` WHERE id=$tes_id and facility='$fac_id' ";	
			$sqltt_provider1=imw_query($tt_provider1);	
			$vrs_tcurr1=imw_fetch_array($sqltt_provider1);		
			
			$vquery_ci = " select * from previous_status where patient_id='$pt_id'
						  and status IN(5,11,13)								  
						  order by status_date,status_time limit 0,1 ";
			$vsql_ci = imw_query($vquery_ci);
			$vrsi=imw_fetch_array($vsql_ci);
			$vrsinm=imw_num_rows($vsql_ci);
			
			if($vrsinm>0)
			{											
					$edi_time=explode(":",$vrsi['status_time']);	
					if($edi_time[0]>=12)
					{
							if($edi_time[0]==12)
							{								
								//$edi_time=$edi_time[0].":".$edi_time[1]."pm";
							}
							else{
								//$edi_time=($edi_time[0]-12).":".$edi_time[1]."pm";
							}	
					}else{
							if($edi_time[0]==00){
									//$edi_time="12:".$edi_time[1]."am";
							}else{
									//$edi_time=$edi_time[0].":".$edi_time[1]."am";
							}
					}
			}else{
					$edi_time="";	
			}		
					
			
			$rm_number=$vrs_sa['sa_app_room']; // ROOM NUMBER
			
			if($rm_number<>"")
			{
				$rm_number="Room# ".$rm_number." ";
			}
			
			$st_st="";
			if(($tes_id<>0))
			{
				$st_st="( ".$vrs_tcurr1["test_name"]." s) - ";
			}else{
				$st_st="( ".$vrs_sp[acronym]." ) - ";
			}									
								
			echo "<tr height='23'> <td id='$tool_tab' align='left' class='text_9'>";								
				  
				  if(($vrs_st[status_icon]<>"") && (file_exists("../../../images/$vrs_st[status_icon]")))
				  {
					//echo "<img src='../../../images/$vrs_st[status_icon]' alt='$vrs_st[status_name]' align='absmiddle'> ";
				  }	
				  
				  
				  echo $st_st;
				  
				  if($edi_time<>"")
				  {
					 //echo $edi_time." ";
				  }												
					
				  echo $rm_number;
				  echo $vrs_sa[sa_patient_name]." ".patient_phone($vrs_sa['sa_patient_id']);
				// echo "&nbsp;&nbsp;<img align='middle' src='../../../images/cancel.gif' onClick='hide_tool_tip()' style='cursor:hand;'/>";
				 include("referrals.php");
			echo "&nbsp;</td></tr>";
				
	print "</table>";

		}	else if($ids==''){
				$query = "select * from patient_data where id = $pt_id";
				$sql = imw_query($query);
				if(imw_num_rows($sql)>0){
					$result = imw_fetch_assoc($sql);
				
				
				print "<table align='left'>";
											
								
							echo "<tr height='23'> <td align='left' class='text_9'>";								
								 	
								 
									echo "-".$result["fname"]."&nbsp;".$result['mname']."&nbsp;".$result['lname']."&nbsp;".$result['suffix']." ".$result{"phone_home"};
									// echo "&nbsp;&nbsp;<img align='middle' src='../../../images/cancel.gif' onClick='hide_tool_tip()' style='cursor:hand;'/>";
									
									echo "</td></tr>";
									
									print "</table>";
							}
		}		
}
// FUNCTION TO GENERATE TOOLTIP - PARVINDER//
	function draw_tooltip($ids,$divid,$nms,$j,$al)
	{			
			if(sizeof($al)>1)
			{	
					if(sizeof($ids)>0){		
					
							$imgid="im".$divid;
							$tabid="tab".$divid;
							$toolid="tool".$divid;
							$tool_tab="tool_tab".$divid;
							
							$patients=sizeof($ids);
								
								?>
									<div class="tool_tip" id="<?php echo $toolid;?>">
										<table align="left">
											
								<?php	
												
									$ids1=sizeof($ids);
									
									for($x=0;$x<sizeof($ids);$x++){	
										
											$vquery_sa = "select sa_patient_app_status_id,procedureid,sa_doctor_id,sa_test_id,sa_facility_id,sa_patient_id,sa_patient_name from schedule_appointments where id=$ids[$x] order by sa_app_time desc";												
											$vsql_sa = imw_query($vquery_sa);
											$vrs_sa=imw_fetch_array($vsql_sa);							
											
											$vquery_sp = "select * from slot_procedures where id=$vrs_sa[procedureid]";												
											$vsql_sp = imw_query($vquery_sp);
											$vrs_sp=imw_fetch_array($vsql_sp);	
											
											$usr_id=$vrs_sa['sa_doctor_id'];
								
											$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";							
											$sqltt_provider=imw_query($tt_provider);	
											$vrs_tcurr=imw_fetch_array($sqltt_provider);	
											
											$pt_id=$vrs_sa['sa_patient_id'];
											$st_id=$vrs_sa['sa_patient_app_status_id'];
											
											$st_provider=" SELECT * FROM `schedule_status` WHERE id=$st_id ";							
											$sqlst_provider=imw_query($st_provider);	
											$vrs_st=imw_fetch_array($sqlst_provider);	
											
											$tes_id=$vrs_sa['sa_test_id'];
											$fac_id=$vrs_sa['sa_facility_id'];
											
											$tt_provider1=" SELECT * FROM `patient_tests` WHERE id=$tes_id and facility='$fac_id' ";	
											$sqltt_provider1=imw_query($tt_provider1);	
											$vrs_tcurr1=imw_fetch_array($sqltt_provider1);		
											
											$vquery_ci = " select * from previous_status where patient_id='$pt_id'
														   and status IN(5,11,13)								  
														   order by status_date,status_time limit 0,1 
														 ";
											$vsql_ci = imw_query($vquery_ci);
											$vrsi=imw_fetch_array($vsql_ci);
											$vrsinm=imw_num_rows($vsql_ci);
											
											if($vrsinm>0)
											{											
													$edi_time=explode(":",$vrsi['status_time']);	
													if($edi_time[0]>=12)
													{
															if($edi_time[0]==12)
															{								
																//$edi_time=$edi_time[0].":".$edi_time[1]."pm";
															}
															else{
																//$edi_time=($edi_time[0]-12).":".$edi_time[1]."pm";
															}	
													}else{
															if($edi_time[0]==00){
																	//$edi_time="12:".$edi_time[1]."am";
															}else{
																	//$edi_time=$edi_time[0].":".$edi_time[1]."am";
															}
													}
											}else{
													$edi_time="";	
											}		
											
											$rm_number=$vrs_sa['sa_app_room']; // ROOM NUMBER
											
											if($rm_number<>"")
											{
												$rm_number="Room# ".$rm_number." ";
											}															
											
											$st_st="";
											if(($tes_id<>0))
											{
												$st_st="( ".$vrs_tcurr1["test_name"]." ) - ";
											}else{
												$st_st="( ".$vrs_sp[acronym]." ) - ";
											}							
																
											echo "<tr height='23'> <td id='$tool_tab' align='left' class='text_9'>";								
												  

											  if(($vrs_st[status_icon]<>"") && (file_exists("../../../images/$vrs_st[status_icon]")))
											  {
													echo "<img src='../../../images/$vrs_st[status_icon]' alt='$vrs_st[status_name]' align='absmiddle'> ";
											  }	
												  
												if($vrs_tcurr['user_type']<>5)
												{
													$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
												}else{
													$dname=" ";
												} 
												  
												  
												//if($edi_time<>"")
												//{
												
													  echo $st_st;

													  if(($vrs_st[status_icon]<>"") && (file_exists("../../../images/$vrs_st[status_icon]")))
													  {
		
															// echo $edi_time." ";
													  }												  
												 		 echo $rm_number;
												 		 echo $vrs_sa[sa_patient_name].$dname." ".patient_phone($vrs_sa['sa_patient_id']);
														 //echo "<img src='../../../images/$vrs_st[status_icon]' alt='$vrs_st[status_name]' align='absmiddle'> ";
											 	//}	
												
												//echo $edi_time;
												 //echo $rm_number;
												 //echo $vrs_sa[sa_patient_name].$dname; 
												  
												/*
												if($vrs_tcurr['user_type']<>5)
												{
													$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
												}else{
													$dname=" ";
												} 
												  
												  echo $st_st.$vrs_sa[sa_patient_name].$dname;
												*/	
												  include("referrals_small.php");
												  echo "</td></tr>";
									}
									
							   ?>				
											
										</table>				
									</div>	
								<?php
						}	
				}else{
				
						if(sizeof($ids)>0){		
					
							$imgid="im".$divid;
							$tabid="tab".$divid;
							$toolid="tool".$divid;
							$tool_tab="tool_tab".$divid;
							
							$patients=sizeof($ids);
								
								?>
									<div class="tool_tip" id="<?php echo $toolid;?>">
										<table align="left">
											
								<?php	
												
									$ids1=sizeof($ids);
									
									for($x=0;$x<sizeof($ids);$x++){	
										
											$vquery_sa = "select * from schedule_appointments where id=$ids[$x]";												
											$vsql_sa = imw_query($vquery_sa);
											$vrs_sa=imw_fetch_array($vsql_sa);							
											
											$vquery_sp = "select * from slot_procedures where id=$vrs_sa[procedureid]";												
											$vsql_sp = imw_query($vquery_sp);
											$vrs_sp=imw_fetch_array($vsql_sp);	
											
											$usr_id=$vrs_sa['sa_doctor_id'];
								
											$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";							
											$sqltt_provider=imw_query($tt_provider);	
											$vrs_tcurr=imw_fetch_array($sqltt_provider);	
											
											$pt_id=$vrs_sa['sa_patient_id'];
											$st_id=$vrs_sa['sa_patient_app_status_id'];
											
											$st_provider=" SELECT * FROM `schedule_status` WHERE id=$st_id ";							
											$sqlst_provider=imw_query($st_provider);	
											$vrs_st=imw_fetch_array($sqlst_provider);							
											
											$tes_id=$vrs_sa['sa_test_id'];
											$fac_id=$vrs_sa['sa_facility_id'];
											
											$tt_provider1=" SELECT * FROM `patient_tests` WHERE id=$tes_id and facility='$fac_id' ";	
											$sqltt_provider1=imw_query($tt_provider1);	
											$vrs_tcurr1=imw_fetch_array($sqltt_provider1);		
											
											$vquery_ci = " select * from previous_status where patient_id='$pt_id'
														  and status IN(5,11,13)								  
														  order by status_date,status_time limit 0,1 ";
											$vsql_ci = imw_query($vquery_ci);
											$vrsi=imw_fetch_array($vsql_ci);
											$vrsinm=imw_num_rows($vsql_ci);
											
											if($vrsinm>0)
											{											
													$edi_time=explode(":",$vrsi['status_time']);	
													if($edi_time[0]>=12)
													{
															if($edi_time[0]==12)
															{								
																//$edi_time=$edi_time[0].":".$edi_time[1]."pm";
															}
															else{
																//$edi_time=($edi_time[0]-12).":".$edi_time[1]."pm";
															}	
													}else{
															if($edi_time[0]==00){
																	//$edi_time="12:".$edi_time[1]."am";
															}else{
																	//$edi_time=$edi_time[0].":".$edi_time[1]."am";
															}
													}
											}else{
													$edi_time="";	
											}		
													
											
											$rm_number=$vrs_sa['sa_app_room']; // ROOM NUMBER
											
											if($rm_number<>"")
											{
												$rm_number="Room# ".$rm_number." ";
											}
											
											$st_st="";
											if(($tes_id<>0))
											{
												$st_st="( ".$vrs_tcurr1["test_name"]." ) - ";
											}else{
												$st_st="( ".$vrs_sp[acronym]." ) - ";
											}									
																
											echo "<tr height='23'> <td id='$tool_tab' align='left' class='text_9'>";								
												  
												  if(($vrs_st[status_icon]<>"") && (file_exists("../../../images/$vrs_st[status_icon]")))
												  {
													echo "<img src='../../../images/$vrs_st[status_icon]' alt='$vrs_st[status_name]' align='absmiddle'> ";
												  }	
												  
												  
												  echo $st_st;
												  
												  if($edi_time<>"")
												  {
												  	 //echo $edi_time." ";
												  }												
												    
												  echo $rm_number;
												  echo $vrs_sa[sa_patient_name]." ".patient_phone($vrs_sa['sa_patient_id']);
												  include("referrals_small.php");
											echo "</td></tr>";
									}
									
							   ?>				
											
										</table>				
									</div>
										
<?php
						}	
				
				
				
				
				
				}			
		
	}	
	
	function patient_phone($id)	{
		$qry="Select phone_home,phone_biz,phone_contact,phone_cell from patient_data where id='$id'";
		$res=imw_query($qry);
		if(imw_num_rows($res)>0)	{
			$row=imw_fetch_array($res);
			if($row['phone_home']<>"")	{
				return " ".$row['phone_home'];
			}else if($row['phone_contact']<>"")	{
				return " ".$row['phone_contact'];
			}else if($row['phone_biz']<>"")	{
				return " ".$row['phone_biz'];
			}else if($row['phone_cell']<>"")	{
				return " ".$row['phone_cell'];
			}
		}else	{
			
		}
	}
	////////////////////////////////////////////////////
	////  function developed by Parvinder Singh     /////
	////  pass 4 values - month,date,year,weekday  /////
	////  return week dates                        /////
	////////////////////////////////////////////////////
		
	function getWeekDates($m,$d,$y,$w){	
			for($x=0; $x<=8; $x++){
					$j=0;
					$k=1;
					$l=2;
					$mm=3;
					
					$DateArray[$j][$x] = date('l, F d Y',mktime(0,0,0,$m,($d-$w)+$x,$y));
					$DateArray[$k][$x] = date('Y-m-d',mktime(0,0,0,$m,($d-$w)+$x,$y));
					$DateArray[$l][$x] = date('D',mktime(0,0,0,$m,($d-$w)+$x,$y));
					$DateArray[$mm][$x] = date('m-d-Y',mktime(0,0,0,$m,($d-$w)+$x,$y));
					
			}
			return $DateArray;
	}
	
	function weekday_schedule($dt,$sr,$sel_fac,$sel_pro){	
	
			$vquery_curr = "									
			SELECT *
			FROM `schedule_appointments` 
			WHERE ";							
			
			// CURRENT DATE FOR DEFAULT PAGE
			$vquery_curr .= " '$dt' between sa_app_start_date AND sa_app_end_date  ";
			
			if($sel_fac<>"0"){
					$vquery_curr .= " AND sa_facility_id='$sel_fac' ";
			}
			if($sel_pro<>"0"){
					$vquery_curr .= " AND sa_doctor_id='$sel_pro' ";
			}
			
			$vquery_curr .= " AND sa_patient_app_status_id <> 201 order by sa_app_starttime ";
						
			$vsql_curr = imw_query($vquery_curr);
			$vsql_num = imw_num_rows($vsql_curr);																
			
			print "<table width='100%' cellpadding='0' cellspacing='0' style='padding-left:5px;'>";
												
				while($vrs_curr=imw_fetch_array($vsql_curr))
				{
						
						$fac_id=$vrs_curr['sa_facility_id'];
						$usr_id=$vrs_curr['sa_doctor_id'];
						
						$st_time=explode(":",$vrs_curr['sa_app_starttime']);
						$ed_time=explode(":",$vrs_curr['sa_app_endtime']);						 
						
						if($st_time[0]>=12)
						{
								if($st_time[0]==12)
								{								
									$st_tm=$st_time[0].":".$st_time[1]."pm";
								}
								else{
									$st_tm=($st_time[0]-12).":".$st_time[1]."pm";								
								}	
						}else{
									$st_tm=$st_time[0].":".$st_time[1]."am";						
						}
						
						if($ed_time[0]>=12)
						{
								if($ed_time[0]==12)
								{								
									$ed_tm=$ed_time[0].":".$ed_time[1]."pm";
								}
								else{
									$ed_tm=($ed_time[0]-12).":".$ed_time[1]."pm";
								
								}	
						}else{
								$ed_tm=$ed_time[0].":".$ed_time[1]."am";;
						
						}
						
						$usr_id=$vrs_curr['sa_doctor_id'];
						$tes_id=$vrs_curr['sa_test_id'];
						$fac_id=$vrs_curr['sa_facility_id'];
						
						$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";							
						$sqltt_provider=imw_query($tt_provider);	
						$vrs_tcurr=imw_fetch_array($sqltt_provider);
						
						if($tes_id<>0)
						{
							$tt_provider1=" SELECT * FROM `patient_tests` WHERE id=$tes_id and facility='$fac_id' ";
							$sqltt_provider1=imw_query($tt_provider1);	
							$vrs_tcurr1=imw_fetch_array($sqltt_provider1);
						}else{	
							$vquery_sp = "select * from slot_procedures where id=$vrs_curr[procedureid]";												
							$vsql_sp = imw_query($vquery_sp);
							$vrs_sp=imw_fetch_array($vsql_sp);
						}
								
						$text_color="#000000";						
						if(($sel_fac<>"0")&&($sel_pro=="0"))
						{
								//$text_color=$vrs_tcurr['provider_color']; 
								// Provider Color
						}elseif(($sel_fac<>"0")&&($sel_pro<>"0")){						
								if(($tes_id<>0))
								{
									//$text_color=$vrs_tcurr1['test_color'];
								}else{
									//$text_color=$vrs_sp['proc_color'];					
								}
								// Procedure/TEST color												
						}elseif(($sel_fac=="0")&&($sel_pro<>"0")){														
								if(($tes_id<>0))
								{
									//$text_color=$vrs_tcurr1['test_color'];
									//print $vrs_tcurr1['test_color'];
								}else{
									//$text_color=$vrs_sp['proc_color'];
								}
								//$text_color="#ffffff";
								// Procedure/TEST color
								
						}elseif(($sel_fac=="0")&&($sel_pro=="0")){						
								//$text_color=$vrs_tcurr['provider_color']; 
								// Provider Color
						}
						
						$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";	
						$sqltt_provider=imw_query($tt_provider);	
						$vrs_tcurr=imw_fetch_array($sqltt_provider);
						
						$pro_id=$vrs_curr['procedureid'];		
																																				
						$vquery_sp = "select * from slot_procedures where id=$pro_id";												
						$vsql_sp = imw_query($vquery_sp);
						$vrs_sp=imw_fetch_array($vsql_sp);	
												
						$rm_number=$vrs_curr['sa_app_room']; // ROOM NUMBER
						
						if($rm_number<>"")
						{
							$rm_number="Room# ".$rm_number." ";
						}
						
						$vquery_ft = "									
							SELECT *
							FROM `facility` 
							WHERE id = $fac_id 											
						";
						$vsql_ft = imw_query($vquery_ft);	
						$rs_ft = imw_fetch_array($vsql_ft);
						
						$st_st="";
						if(($tes_id<>0))
						{
							$st_st=$vrs_tcurr1["test_name"];
						}else{
							$st_st=$vrs_sp["acronym"];
						}				
						
																													
						print "<tr><td class='text_9' height='16'><font color='$text_color'>";
					   
						print  $st_tm." ".$ed_tm." ".$st_st." - ".$rm_number. $vrs_curr['sa_patient_name'];
						
						if($sel_pro=="0"){
							
							if($vrs_tcurr['user_type']<>5)
							{
								print  " ( Dr. ".$vrs_tcurr['lname'];
								//$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
							}else{
								$dname=" ( ";
							} 
						}
						
						
						
						if($sel_fac=="0"){													
							if($sel_pro=="0")
							{
								print " / ";							
							}else{
								print " ( ";		
							} 
							print  " ".$rs_ft['name'];									
						}
						
						if(($sel_fac=="0") || ($sel_pro=="0"))
						{
							//print " ) ";
							if($vrs_tcurr['user_type']<>5)
							{
								print  " ) ";
								//$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
							}else{
								$dname="  ";
							} 
						}
						
						$pt_id=$vrs_curr['sa_patient_id'];
						include("referrals_small.php");
						
						print "</font></td></tr>";	
						
				}		
				
			print "</table>";
			
	}
	
	function month_schedule($dt,$sr,$sel_fac,$sel_pro){	
			$dt_arr=explode("-",$dt);
			
			$divid="div".$dt_arr[2].$dt_arr[1];
			$tabid="tab".$dt_arr[2].$dt_arr[1];
	
			$vquery_curr = "									
			SELECT *
			FROM `schedule_appointments` 
			WHERE ";							
			
			// CURRENT DATE FOR DEFAULT PAGE			
			$vquery_curr .= " '$dt' between sa_app_start_date and sa_app_end_date  ";
			if($sel_fac<>"0"){
				$vquery_curr .= " AND sa_facility_id='$sel_fac' ";
			}
			if($sel_pro<>"0"){
				$vquery_curr .= " AND sa_doctor_id='$sel_pro' ";
			}
			$vquery_curr .= " AND sa_patient_app_status_id <> 201 order by sa_app_starttime ";
							
			$vsql_curr = imw_query($vquery_curr);
			$vsql_num = imw_num_rows($vsql_curr);
			
			if($vsql_num>0)
			{
				$myheight=$vsql_num*16;
				
				//print "<div id=$divid style='height=5'; onMouseOut='setMouseOutFlag();' onMouseOver='showSummaryInPopup(this,true);'>";									
				print "<div id=$divid style='height=500'; onMouseout='hide_tool_tip();' onMouseOver='show_month_tip($divid,$tabid,$myheight);'>";
			}														
			
			print "<table id=$tabid cellpadding='0' cellspacing='0' style='padding-left:5px;'>";
												
				while($vrs_curr=imw_fetch_array($vsql_curr))
				{
						
						$fac_id=$vrs_curr['sa_facility_id'];
						$usr_id=$vrs_curr['sa_doctor_id'];
						
						$st_time=explode(":",$vrs_curr['sa_app_starttime']);
						$ed_time=explode(":",$vrs_curr['sa_app_endtime']);						 
						
						if($st_time[0]>=12)
						{
								if($st_time[0]==12)
								{								
									$st_tm=$st_time[0].":".$st_time[1]."pm";
								}
								else{
									$st_tm=($st_time[0]-12).":".$st_time[1]."pm";								
								}	
						}else{
									$st_tm=$st_time[0].":".$st_time[1]."am";						
						}
						
						if($ed_time[0]>=12)
						{
								if($ed_time[0]==12)
								{								
									$ed_tm=$ed_time[0].":".$ed_time[1]."pm";
								}
								else{
									$ed_tm=($ed_time[0]-12).":".$ed_time[1]."pm";
								
								}	
						}else{
								$ed_tm=$ed_time[0].":".$ed_time[1]."am";;
						
						}
						
						$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";	
						
						$sqltt_provider=imw_query($tt_provider);	
						$vrs_tcurr=imw_fetch_array($sqltt_provider);
															
						//$text_color=$vrs_tcurr['provider_color'];						
						$pro_id=$vrs_curr['procedureid'];		
																																				
						$vquery_sp = "select * from slot_procedures where id=$pro_id";												
						$vsql_sp = imw_query($vquery_sp);
						$vrs_sp=imw_fetch_array($vsql_sp);	
						
						$vquery_ft = "									
							SELECT *
							FROM `facility` 
							WHERE id = $fac_id 											
						";
						$vsql_ft = imw_query($vquery_ft);	
						$rs_ft = imw_fetch_array($vsql_ft);
						
						$usr_id=$vrs_curr['sa_doctor_id'];
						$tes_id=$vrs_curr['sa_test_id'];
						$fac_id=$vrs_curr['sa_facility_id'];
						
						$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";							
						$sqltt_provider=imw_query($tt_provider);	
						$vrs_tcurr=imw_fetch_array($sqltt_provider);
						
						if($tes_id<>0)
						{
							$tt_provider1=" SELECT * FROM `patient_tests` WHERE id=$tes_id and facility='$fac_id' ";
							$sqltt_provider1=imw_query($tt_provider1);	
							$vrs_tcurr1=imw_fetch_array($sqltt_provider1);
						}else{	
							$vquery_sp = "select * from slot_procedures where id=$vrs_curr[procedureid]";												
							$vsql_sp = imw_query($vquery_sp);
							$vrs_sp=imw_fetch_array($vsql_sp);
						}
								
						$text_color="#000000";						
						if(($sel_fac<>"0")&&($sel_pro=="0"))
						{
								//$text_color=$vrs_tcurr['provider_color']; 
								// Provider Color
						}elseif(($sel_fac<>"0")&&($sel_pro<>"0")){						
								if(($tes_id<>0))
								{
								//	$text_color=$vrs_tcurr1['test_color'];
								}else{
								//	$text_color=$vrs_sp['proc_color'];					
								}
								// Procedure/TEST color												
						}elseif(($sel_fac=="0")&&($sel_pro<>"0")){														
								if(($tes_id<>0))
								{
								//	$text_color=$vrs_tcurr1['test_color'];
									//print $vrs_tcurr1['test_color'];
								}else{
								//	$text_color=$vrs_sp['proc_color'];
								}
								//$text_color="#ffffff";
								// Procedure/TEST color
								
						}elseif(($sel_fac=="0")&&($sel_pro=="0")){						
								//$text_color=$vrs_tcurr['provider_color']; 
								// Provider Color
						}
						if($text_color==""){
							//	$text_color="#000000"; 
						}
						//." ".$ed_tm END TIME
						
						$st_st="";
						if(($tes_id<>0))
						{
							$st_st=$vrs_tcurr1["test_name"];
						}else{
							$st_st=$vrs_sp["acronym"];
						}	
						
						$rm_number=$vrs_curr['sa_app_room']; // ROOM NUMBER
						
						if($rm_number<>"")
						{
							$rm_number="Room# ".$rm_number." ";
						}
																													
						print "<tr><td class='text_9' height='16'><font color='$text_color'>";
						print  $st_tm." ".$st_st." - ".$rm_number. $vrs_curr['sa_patient_name'];
						
						//. " ( Dr. " .$vrs_tcurr['lname']." / ".$rs_ft['name']." ) "
						if($sel_pro=="0"){
							//print  " ( Dr. ".$vrs_tcurr['lname'];
							if($vrs_tcurr['user_type']<>5)
							{
								print  " ( Dr. ".$vrs_tcurr['lname'];
								//$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
							}else{
								$dname=" ( ";
							} 
						}
						if($sel_fac=="0"){													
							if($sel_pro=="0")
							{
								print " / ";							
							}else{
								print " ( ";		
							} 
							print  " ".$rs_ft['name'];									
						}
						if(($sel_fac=="0") || ($sel_pro=="0"))
						{
							//print " ) ";
							if($vrs_tcurr['user_type']<>5)
							{
								print  " ) ";
								//$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
							}else{
								$dname="  ";
							} 
						}
						
						$pt_id=$vrs_curr['sa_patient_id'];
						include("referrals_small.php");
						print "</font></td></tr>";	
						
				}		
			print "</table>";
			if($vsql_num>0)
			{
				print "</div>";
			}	
	}
/*
	function month_schedule_physician($dt,$sr,$sel_fac,$sel_pro){	
			$dt_arr=explode("-",$dt);
			
			$divid="div".$dt_arr[2].$dt_arr[1];
			$tabid="tab".$dt_arr[2].$dt_arr[1];
	
			$vquery_curr = "									
			SELECT *
			FROM `schedule_appointments` 
			WHERE ";							
			
			// CURRENT DATE FOR DEFAULT PAGE			
			$vquery_curr .= " '$dt' between sa_app_start_date and sa_app_end_date  ";
			if($sel_fac<>"0"){
				$vquery_curr .= " AND sa_facility_id='$sel_fac' ";
			}
			if($sel_pro<>"0"){
				$vquery_curr .= " AND sa_doctor_id='$sel_pro' ";
			}
			$vquery_curr .= " AND sa_patient_app_status_id <> 201 order by sa_app_starttime ";
							
			$vsql_curr = imw_query($vquery_curr);
			$vsql_num = imw_num_rows($vsql_curr);
			
			if($vsql_num>0)
			{
				//$myheight=$vsql_num*16;
				
				print "<div id=$divid style='height=2'; onMouseOut='setMouseOutFlag();' onMouseOver='showSummaryInPopup(this,true);'>";									
				//print "<div id=$divid style='height=500'; onMouseout='hide_tool_tip();' onMouseOver='show_month_tip($divid,$tabid,$myheight);'>";
			}														
			
			print "<table id=$tabid cellpadding='0' cellspacing='0' style='padding-left:5px;'>";
												
				while($vrs_curr=imw_fetch_array($vsql_curr))
				{
						
						$fac_id=$vrs_curr['sa_facility_id'];
						$usr_id=$vrs_curr['sa_doctor_id'];
						
						$st_time=explode(":",$vrs_curr['sa_app_starttime']);
						$ed_time=explode(":",$vrs_curr['sa_app_endtime']);						 
						
						if($st_time[0]>=12)
						{
								if($st_time[0]==12)
								{								
									$st_tm=$st_time[0].":".$st_time[1]."pm";
								}
								else{
									$st_tm=($st_time[0]-12).":".$st_time[1]."pm";								
								}	
						}else{
									$st_tm=$st_time[0].":".$st_time[1]."am";						
						}
						
						if($ed_time[0]>=12)
						{
								if($ed_time[0]==12)
								{								
									$ed_tm=$ed_time[0].":".$ed_time[1]."pm";
								}
								else{
									$ed_tm=($ed_time[0]-12).":".$ed_time[1]."pm";
								
								}	
						}else{
								$ed_tm=$ed_time[0].":".$ed_time[1]."am";;
						
						}
						
						$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";	
						
						$sqltt_provider=imw_query($tt_provider);	
						$vrs_tcurr=imw_fetch_array($sqltt_provider);
															
						//$text_color=$vrs_tcurr['provider_color'];						
						$pro_id=$vrs_curr['procedureid'];		
																																				
						$vquery_sp = "select * from slot_procedures where id=$pro_id";												
						$vsql_sp = imw_query($vquery_sp);
						$vrs_sp=imw_fetch_array($vsql_sp);	
						
						$vquery_ft = "									
							SELECT *
							FROM `facility` 
							WHERE id = $fac_id 											
						";
						$vsql_ft = imw_query($vquery_ft);	
						$rs_ft = imw_fetch_array($vsql_ft);
						
						$usr_id=$vrs_curr['sa_doctor_id'];
						$tes_id=$vrs_curr['sa_test_id'];
						$fac_id=$vrs_curr['sa_facility_id'];
						
						$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";							
						$sqltt_provider=imw_query($tt_provider);	
						$vrs_tcurr=imw_fetch_array($sqltt_provider);
						
						if($tes_id<>0)
						{
							$tt_provider1=" SELECT * FROM `patient_tests` WHERE id=$tes_id and facility='$fac_id' ";
							$sqltt_provider1=imw_query($tt_provider1);	
							$vrs_tcurr1=imw_fetch_array($sqltt_provider1);
						}else{	
							$vquery_sp = "select * from slot_procedures where id=$vrs_curr[procedureid]";												
							$vsql_sp = imw_query($vquery_sp);
							$vrs_sp=imw_fetch_array($vsql_sp);
						}
								
						$text_color="#000000";						
						if(($sel_fac<>"0")&&($sel_pro=="0"))
						{
								$text_color=$vrs_tcurr['provider_color']; 
								// Provider Color
						}elseif(($sel_fac<>"0")&&($sel_pro<>"0")){						
								if(($tes_id<>0))
								{
									$text_color=$vrs_tcurr1['test_color'];
								}else{
									$text_color=$vrs_sp['proc_color'];					
								}
								// Procedure/TEST color												
						}elseif(($sel_fac=="0")&&($sel_pro<>"0")){														
								if(($tes_id<>0))
								{
									$text_color=$vrs_tcurr1['test_color'];
									//print $vrs_tcurr1['test_color'];
								}else{
									$text_color=$vrs_sp['proc_color'];
								}
								//$text_color="#ffffff";
								// Procedure/TEST color
								
						}elseif(($sel_fac=="0")&&($sel_pro=="0")){						
								$text_color=$vrs_tcurr['provider_color']; 
								// Provider Color
						}
						if($text_color==""){
								$text_color="#000000"; 
						}
						//." ".$ed_tm END TIME
						
						$st_st="";
						if(($tes_id<>0))
						{
							$st_st=$vrs_tcurr1["test_name"];
						}else{
							$st_st=$vrs_sp["acronym"];
						}	
						
						$rm_number=$vrs_curr['sa_app_room']; // ROOM NUMBER
						
						if($rm_number<>"")
						{
							$rm_number="Room# ".$rm_number." ";
						}
																													
						print "<tr><td class='text_9' height='16'><font color='$text_color'>";
						print  $st_tm." ".$st_st." - ".$rm_number. $vrs_curr['sa_patient_name'];
						
						//. " ( Dr. " .$vrs_tcurr['lname']." / ".$rs_ft['name']." ) "
						if($sel_pro=="0"){
							//print  " ( Dr. ".$vrs_tcurr['lname'];
							if($vrs_tcurr['user_type']<>5)
							{
								print  " ( Dr. ".$vrs_tcurr['lname'];
								//$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
							}else{
								$dname=" ( ";
							} 
						}
						if($sel_fac=="0"){													
							if($sel_pro=="0")
							{
								print " / ";							
							}else{
								print " ( ";		
							} 
							print  " ".$rs_ft['name'];									
						}
						if(($sel_fac=="0") || ($sel_pro=="0"))
						{
							//print " ) ";
							if($vrs_tcurr['user_type']<>5)
							{
								print  " ) ";
								//$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
							}else{
								$dname="  ";
							} 
						}
						
						$pt_id=$vrs_curr['sa_patient_id'];
						include("referrals_small.php");
						print "</font></td></tr>";	
						
				}		
			print "</table>";
			if($vsql_num>0)
			{
				print "</div>";
			}	
	}
*/	

	function month_schedule_physician($dt,$sr,$sel_fac,$sel_pro){	
			$dt_arr=explode("-",$dt);
			
			$divid="div".$dt_arr[2].$dt_arr[1];
			$tabid="tab".$dt_arr[2].$dt_arr[1];
	
			$vquery_curr = "									
			SELECT *
			FROM `schedule_appointments` 
			WHERE ";							
			
			// CURRENT DATE FOR DEFAULT PAGE			
			$vquery_curr .= " '$dt' between sa_app_start_date and sa_app_end_date  ";
			if($sel_fac<>"0"){
				$vquery_curr .= " AND sa_facility_id='$sel_fac' ";
			}
			if($sel_pro<>"0"){
				$vquery_curr .= " AND sa_doctor_id='$sel_pro' ";
			}
			$vquery_curr .= " AND sa_patient_app_status_id <> 201 order by sa_app_starttime ";
							
			$vsql_curr = imw_query($vquery_curr);
			$vsql_num = imw_num_rows($vsql_curr);
			
			if($vsql_num>0)
			{
				//$myheight=$vsql_num*16;
				
				//print "<div id=$divid style='height=500'; onMouseout='hide_tool_tip();' onMouseOver='show_month_tip($divid,$tabid,$myheight);'>";
				print "<div id=$divid style='height=2'; onMouseOut='setMouseOutFlag();' onMouseOver='showSummaryInPopup(this,true);'>";									
			}														
			
			print "<table id=$tabid cellpadding='0' cellspacing='0' style='padding-left:5px;'>";
												
				while($vrs_curr=imw_fetch_array($vsql_curr))
				{
						
						$fac_id=$vrs_curr['sa_facility_id'];
						$usr_id=$vrs_curr['sa_doctor_id'];
						
						$st_time=explode(":",$vrs_curr['sa_app_starttime']);
						$ed_time=explode(":",$vrs_curr['sa_app_endtime']);						 
						
						if($st_time[0]>=12)
						{
								if($st_time[0]==12)
								{								
									$st_tm=$st_time[0].":".$st_time[1]."pm";
								}
								else{
									$st_tm=($st_time[0]-12).":".$st_time[1]."pm";								
								}	
						}else{
									$st_tm=$st_time[0].":".$st_time[1]."am";						
						}
						
						if($ed_time[0]>=12)
						{
								if($ed_time[0]==12)
								{								
									$ed_tm=$ed_time[0].":".$ed_time[1]."pm";
								}
								else{
									$ed_tm=($ed_time[0]-12).":".$ed_time[1]."pm";
								
								}	
						}else{
								$ed_tm=$ed_time[0].":".$ed_time[1]."am";;
						
						}
						
						$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";	
						
						$sqltt_provider=imw_query($tt_provider);	
						$vrs_tcurr=imw_fetch_array($sqltt_provider);
															
						//$text_color=$vrs_tcurr['provider_color'];						
						$pro_id=$vrs_curr['procedureid'];		
																																				
						$vquery_sp = "select * from slot_procedures where id=$pro_id";												
						$vsql_sp = imw_query($vquery_sp);
						$vrs_sp=imw_fetch_array($vsql_sp);	
						
						$vquery_ft = "									
							SELECT *
							FROM `facility` 
							WHERE id = $fac_id 											
						";
						$vsql_ft = imw_query($vquery_ft);	
						$rs_ft = imw_fetch_array($vsql_ft);
						
						$usr_id=$vrs_curr['sa_doctor_id'];
						$tes_id=$vrs_curr['sa_test_id'];
						$fac_id=$vrs_curr['sa_facility_id'];
						
						$tt_provider=" SELECT * FROM `users` WHERE id=$usr_id ";							
						$sqltt_provider=imw_query($tt_provider);	
						$vrs_tcurr=imw_fetch_array($sqltt_provider);
						
						if($tes_id<>0)
						{
							$tt_provider1=" SELECT * FROM `patient_tests` WHERE id=$tes_id and facility='$fac_id' ";
							$sqltt_provider1=imw_query($tt_provider1);	
							$vrs_tcurr1=imw_fetch_array($sqltt_provider1);
						}else{	
							$vquery_sp = "select * from slot_procedures where id=$vrs_curr[procedureid]";												
							$vsql_sp = imw_query($vquery_sp);
							$vrs_sp=imw_fetch_array($vsql_sp);
						}
								
						$text_color="#000000";						
						if(($sel_fac<>"0")&&($sel_pro=="0"))
						{
								$text_color=$vrs_tcurr['provider_color']; 
								// Provider Color
						}elseif(($sel_fac<>"0")&&($sel_pro<>"0")){						
								if(($tes_id<>0))
								{
									$text_color=$vrs_tcurr1['test_color'];
								}else{
									$text_color=$vrs_sp['proc_color'];					
								}
								// Procedure/TEST color												
						}elseif(($sel_fac=="0")&&($sel_pro<>"0")){														
								if(($tes_id<>0))
								{
									$text_color=$vrs_tcurr1['test_color'];
									//print $vrs_tcurr1['test_color'];
								}else{
									$text_color=$vrs_sp['proc_color'];
								}
								//$text_color="#ffffff";
								// Procedure/TEST color
								
						}elseif(($sel_fac=="0")&&($sel_pro=="0")){						
								$text_color=$vrs_tcurr['provider_color']; 
								// Provider Color
						}
						if($text_color==""){
								$text_color="#000000"; 
						}
						//." ".$ed_tm END TIME
						
						$st_st="";
						if(($tes_id<>0))
						{
							$st_st=$vrs_tcurr1["test_name"];
						}else{
							$st_st=$vrs_sp["acronym"];
						}	
						
						$rm_number=$vrs_curr['sa_app_room']; // ROOM NUMBER
						
						if($rm_number<>"")
						{
							$rm_number="Room# ".$rm_number." ";
						}
																													
						print "<tr><td class='text_9' height='16'><font color='$text_color'>";
						print  $st_tm." ".$st_st." - ".$rm_number. $vrs_curr['sa_patient_name'];
						
						//. " ( Dr. " .$vrs_tcurr['lname']." / ".$rs_ft['name']." ) "
						if($sel_pro=="0"){
							//print  " ( Dr. ".$vrs_tcurr['lname'];
							if($vrs_tcurr['user_type']<>5)
							{
								print  " ( Dr. ".$vrs_tcurr['lname'];
								//$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
							}else{
								$dname=" ( ";
							} 
						}
						if($sel_fac=="0"){													
							if($sel_pro=="0")
							{
								print " / ";							
							}else{
								print " ( ";		
							} 
							print  " ".$rs_ft['name'];									
						}
						if(($sel_fac=="0") || ($sel_pro=="0"))
						{
							//print " ) ";
							if($vrs_tcurr['user_type']<>5)
							{
								print  " ) ";
								//$dname=" ( Dr. ".$vrs_tcurr['lname']." ) ";
							}else{
								$dname="  ";
							} 
						}
						
						$pt_id=$vrs_curr['sa_patient_id'];
						include("referrals_small.php");
						print "</font></td></tr>";	
						
				}		
			print "</table>";
			if($vsql_num>0)
			{
				print "</div>";
			}	
	}
	
	function getWeekDay($start_day,$cur_date){
		$c = 1;
		for($f=0;$f<5;$f++){
			for($d=0;$d<7;$d++){
				if($f == 0){
					if($d == $start_day){
						$da = 1;
					}
				}
				if($da > 0)
					$da++;
				if($da == $cur_date){
					$we = $c;
					break;
				}
			}
			$c++;
		}
		$week = $we != '' ? $we : '1';
		return $week;
	}
	
	function month_scheduleprovider2($dt,$sel_week_day,$sel_pro){
		$dt_exs=explode("-",$dt);
		$yrs=$dt_exs[0];
		$mth=$dt_exs[1];
		$dys=$dt_exs[2];			
		$months=date("n",mktime(0,0,0,$mth,$dys,$yrs));		
		$task_date=date("Y-m-d",mktime(0,0,0,$mth,$dys,$yrs));
		$start_day = date('N',mktime(0,0,0,$mth,1,$yrs));
		//$week = getWeekDay($start_day,$dys);
		$week = getWeekCount($dys);
		
		$dt_ex = explode("_",$sel_week_day);	
		$weekday = $dt_ex[1];
		
		//$week =  $dt_ex[0];
		//------ Query To get The Exception Deleted Data ----------
		$qry = "select * from provider_schedule_tmp where today_date = '$dt'
				and provider = '$sel_pro' and del_status = '1' and delete_row = 'no'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and today_date != $today_date";
				//print '<br>';
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}	
			
		//------ Query To get The All Deleted Data ----------
		
		$qry = "select * from provider_schedule_tmp where today_date <= '$dt'
				and provider = '$sel_pro' and del_status = '1' and delete_row = 'all'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$id = $res['id'];
				$DeletedId[] = $id;
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and id < '$id'";
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}
		if(isset($DeletedId)){
			$deletedId = implode($DeletedId,",");
		}
		//------- Query For Exceptional Data -----------
		//print $deletedId;
		if($deletedId){
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date = '$dt' and 
				provider='$sel_pro' and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' and id not in ($deletedId) group by provider,sch_tmp_id,facility";			
		}
		else{
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date = '$dt' and 
				provider='$sel_pro' and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' group by provider,sch_tmp_id,facility";			
		}
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes2 = fetchArray($qryId);
		}
		for($aa=0;$aa< count($finalRes2);$aa++){
			$dataId[] = $finalRes2[$aa]['id'];
		}
		//print_r($dataId);
		if(isset($dataId)){
			$Dataid = implode($dataId,",");
		}
		if($deletedId){
			if($Dataid){
				$Dataid .= ','.$deletedId;
			}
			else{
				$Dataid = $deletedId;
			}
		}
		//------- Query For All Data -----------
		//print $Dataid;
		if($Dataid){
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date <= '$dt'
						  and provider='$sel_pro' and week$week = '$weekday' and del_status = 0
						  and status = 'yes' and id not in($Dataid) group by provider,sch_tmp_id,facility";
		}
		else{
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date <= '$dt' and 
					provider='$sel_pro' and week$week = '$weekday' and del_status = 0 
					and delete_row = '' group by provider,sch_tmp_id,facility";
		}
		//echo $qry.'<br>';
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes1 = fetchArray($qryId);		
		}
		$finalRes = array_merge((array)$finalRes1,(array)$finalRes2);
		//$finalRes=array_unique($finalRess);
		//echo  count($finalRes);
		//print_r($finalRes);
		//--- Start to get the data for display ----------------				
		$ids_nm='nm'.$dys;
		$sch_tmp_id = array();
		for($a = 0;$a < count($finalRes);$a++){	
			$sch_tmp_id[$a]['sch_tmp_id'] = $finalRes[$a]['sch_tmp_id'];
			$sch_tmp_id[$a]['id'] = $finalRes[$a]['id'];
			$sch_tmp_id[$a]['facility'] = $finalRes[$a]['facility'];
		} 
		//--- End to get the data for display ----------------		
		return $sch_tmp_id;
	}
	
	function month_scheduleprovider1($dt,$sel_week_day,$sel_pro){
		//print $dt.','.$sel_week_day.','.$sel_pro;
		$dt_exs=explode("-",$dt);
		$mth=$dt_exs[1];
		$yrs=$dt_exs[0];
		$dys=$dt_exs[2];			
		$months=date("n",mktime(0,0,0,$mth,$dys,$yrs));		
		$task_date=date("Y-m-d",mktime(0,0,0,$mth,$dys,$yrs));
		$start_day = date('N',mktime(0,0,0,$mth,1,$yrs));
		//$week = getWeekDay($start_day,$dys);
		$week = getWeekCount($dys);
		
		$dt_ex = explode("_",$sel_week_day);	
		$weekday = $dt_ex[1];
		//$week =  $dt_ex[0];
		//------ Query To get The Exception Deleted Data ----------
		$qry = "select * from provider_schedule_tmp where today_date = '$dt'
				and provider = '$sel_pro' and del_status = '1' and delete_row = 'no'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and today_date != $today_date";
				//print '<br>';
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}	
			
		//------ Query To get The All Deleted Data ----------
		
		$qry = "select * from provider_schedule_tmp where today_date <= '$dt'
				and provider = '$sel_pro' and del_status = '1' and delete_row = 'all'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$id = $res['id'];
				$DeletedId[] = $id;
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and id < '$id'";
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}		
		if(isset($DeletedId)){
			$deletedId = implode($DeletedId,",");
		}
		//------- Query For Exceptional Data -----------
		//print $deletedId;
		if($deletedId){
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date = '$dt' and 
				provider='$sel_pro' and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' and id not in ($deletedId) group by provider,sch_tmp_id,facility";			
		}
		else{
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date = '$dt' and 
				provider='$sel_pro' and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' group by provider,sch_tmp_id,facility";			
		}
		//if($dys == 1)
		//print $deletedId;
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes2 = fetchArray($qryId);
		}
		for($aa=0;$aa< count($finalRes2);$aa++){
			$dataId[] = $finalRes2[$aa]['id'];
		}
		//print_r($dataId);
		if(isset($dataId)){
			$Dataid = implode($dataId,",");
		}
		if($deletedId){
			if($Dataid){
				$Dataid .= ','.$deletedId;
			}
			else{
				$Dataid = $deletedId;
			}
		}
		//------- Query For All Data -----------
		//print $Dataid;
		if($Dataid){
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date <= '$dt'
					  and provider='$sel_pro' and week$week = '$weekday' and del_status = 0
					  and status = 'yes' and id not in($Dataid) group by provider,sch_tmp_id,facility";
		}
		else{
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date <= '$dt' and 
					provider='$sel_pro' and week$week = '$weekday' and del_status = 0 /*and status = 'yes'*/
					and delete_row = '' group by provider,sch_tmp_id,facility";
		}
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes1 = fetchArray($qryId);		
		}
		$finalRes = array_merge((array)$finalRes1,(array)$finalRes2);
		//$finalRes=array_unique($finalRess);
		//echo  count($finalRes);
		//print_r($finalRes);
		//--- Start to get the data for display ----------------				
		$ids_nm='nm'.$dys;
		$myheight="";
		/*
		if(count($finalRes)>0)
		{
				$myheight=15;								
				print "<div id=$ids_nm style='height=10'; >";				
				print "<table cellpadding='0' cellspacing='0' style='padding-left:2px;'>";
				print "<tr height='10'><td valign='top' class='text_9'>";							
		}
		*/
		for($a = 0;$a < count($finalRes);$a++){	
			$facilityId = $finalRes[$a]['facility'];
			$sch_tmp_id = $finalRes[$a]['sch_tmp_id'];
			$vquery_css = "select schedule_name from schedule_templates where id = $sch_tmp_id";				
			$vsql_css = imw_query($vquery_css);
			$rt_rss = imw_fetch_array($vsql_css);							
			$sch_name = $rt_rss['schedule_name'];			
			$vquery_d = "select name from facility where id = $facilityId";
			$vsql_d = imw_query($vquery_d);
			$rs_d = imw_fetch_array($vsql_d);							
			$name = strtolower($rs_d["name"]);
			if($name == 'whiting'){
				$fac_name = 'wh';
			}
			else if($name == 'brick'){
				$fac_name = 'br';
			}
			else if($name == 'surgery center'){
				$fac_name = 'SC';
			}
			else if($name == 'toms river'){
				$fac_name = 'tr';
			}
			$data[] = " &nbsp;&nbsp; ".strtoupper($fac_name);
		} 
		return $data;
		/*
		if(sizeof($finalRes)>0)
		{
				
				print "</td></tr>";
				print "</table>";	
				print "</div>";
		}
		*/
		//--- End to get the data for display ----------------		
	
	}

	function month_scheduleprovider($dt,$sel_week_day,$sel_pro)
	{														
					
		$dt_exs=explode("-",$dt);
		$mth=$dt_exs[1];
		$yrs=$dt_exs[0];
		$dys=$dt_exs[2];			
		$months=date("n",mktime(0,0,0,$mth,$dys,$yrs));		
		$task_date=date("Y-m-d",mktime(0,0,0,$mth,$dys,$yrs));
		$start_day = date('N',mktime(0,0,0,$mth,1,$yrs));
		//$week = getWeekDay($start_day,$dys);
		$week = getWeekCount($dys);
		
		$dt_ex = explode("_",$sel_week_day);	
		$weekday = $dt_ex[1];
		//$week =  $dt_ex[0];
		//------ Query To get The Exception Deleted Data ----------
		$qry = "select * from provider_schedule_tmp where today_date = '$dt'
				and provider = '$sel_pro' and del_status = '1' and delete_row = 'no'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and today_date != $today_date";
				//print '<br>';
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}	
			
		//------ Query To get The All Deleted Data ----------
		
		$qry = "select * from provider_schedule_tmp where today_date <= '$dt'
				and provider = '$sel_pro' and del_status = '1' and delete_row = 'all'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$id = $res['id'];
				$DeletedId[] = $id;
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and id < '$id'";
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}		
		if(isset($DeletedId)){
			$deletedId = implode($DeletedId,",");
		}
		//------- Query For Exceptional Data -----------
		//print $deletedId;
		if($deletedId){
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date = '$dt' and 
				provider='$sel_pro' and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' and id not in ($deletedId) group by provider,sch_tmp_id,facility";			
		}
		else{
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date = '$dt' and 
				provider='$sel_pro' and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' group by provider,sch_tmp_id,facility";			
		}
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes2 = fetchArray($qryId);
		}
		for($aa=0;$aa< count($finalRes2);$aa++){
			$dataId[] = $finalRes2[$aa]['id'];
		}
		//print_r($dataId);
		if(isset($dataId)){
			$Dataid = implode($dataId,",");
		}
		if($deletedId){
			if($Dataid){
				$Dataid .= ','.$deletedId;
			}
			else{
				$Dataid = $deletedId;
			}
		}
		//------- Query For All Data -----------
		//print $Dataid;
		if($Dataid){
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date <= '$dt'
						  and provider='$sel_pro' and week$week = '$weekday' and del_status = 0
						  and status = 'yes' and id not in($Dataid) group by provider,sch_tmp_id,facility";
		}
		else{
			$qry = "select id,facility,sch_tmp_id from provider_schedule_tmp where today_date <= '$dt' and 
					provider='$sel_pro' and week$week = '$weekday' and del_status = 0 /*and status = 'yes'*/
					and delete_row = '' group by provider,sch_tmp_id,facility";
		}
		//echo $qry;
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes1 = fetchArray($qryId);		
		}
		$finalRes = array_merge((array)$finalRes1,(array)$finalRes2);
		//$finalRes=array_unique($finalRess);
		//echo  count($finalRes);
		//print_r($finalRes);
		//--- Start to get the data for display ----------------				
		$ids_nm='nm'.$dys;
		$myheight="";
		if(count($finalRes)>0)
		{
				$myheight=15;								
				print "<div id=$ids_nm style='height=500'; onMouseout='hide_tool_tip();' onMouseOver='show_month_tip($ids_nm,$myheight);'>";
				
				print "<table cellpadding='0' cellspacing='0' style='padding-left:2px;'>";
				print "<tr><td valign='top' class='text_9'>";							
		}
								
		for($a = 0;$a < count($finalRes);$a++){	
			$facilityId = $finalRes[$a]['facility'];
			$sch_tmp_id = $finalRes[$a]['sch_tmp_id'];
			$vquery_css = "select schedule_name from schedule_templates where id = $sch_tmp_id";				
			$vsql_css = imw_query($vquery_css);
			$rt_rss = imw_fetch_array($vsql_css);							
			$sch_name = $rt_rss['schedule_name'];			
			$vquery_d = "select name from facility where id = $facilityId";
			$vsql_d = imw_query($vquery_d);
			$rs_d = imw_fetch_array($vsql_d);							
			$fac_name = $rs_d["name"];	
			print " &nbsp;&nbsp; ".$fac_name." <b>".$sch_name." &nbsp;&nbsp;&nbsp; </b><br>";						
		} 
		if(sizeof($finalRes)>0)
		{
				
				print "</td></tr>";
				print "</table>";	
				print "</div>";
		}
		//--- End to get the data for display ----------------		
	}
	
function getcheckout_status($schedule_id,$chg_to,$vdate,$loca){
  	 $query = "select status,patient_id from previous_status   where sch_id='$schedule_id' ";												
	$res=imw_query($query);	
	$stat1=array();
	if(imw_num_rows($res)>0){
		while($status=imw_fetch_array($res)){
				$stat1[]=$status[0];
				$patient_id=$status[1];
		}
		$pat=patient_name($patient_id);
	}else{
		$que_update_pid = "select sa_patient_id from schedule_appointments where id='$schedule_id' ";												
		$vsq_update_pid = imw_query($que_update_pid);	
		list($patient_id)=imw_fetch_array($vsq_update_pid);
		$pat=patient_name($patient_id);	
	}
	//return $stat;
	//$stats=implode(",",$stat);
	if(in_array(13,$stat1)){	
		
	}else{
		return "&nbsp;&nbsp;&nbsp;".$pat[2]." is Not Checked-in<br><br>Are you sure you want to Chek out $pat[2]";
	}
		
}
function getProviderLists($dt,$fac,$pro){
	$weeknumber="";
	$output="";
	$dts=explode("-",$dt);
	//$output.=$pro."-".$dt."-".$fac;
	$dated=$dts[2]."-".$dts[0]."-".$dts[1];
	$rfac=getFacility($dated,$weeknumber,$fac);
	
	  	$output.="Provider : <select name=\"sel_pro_desks\" id=\"sel_pro_desks\" class=\"text_9\" style=\"font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#0000FF; font-weight:bold;\" >
		
				<option value=''>&nbsp;--Provider--</option>";
				
				
					
				
				for($i=0;$i<count($rfac);$i++){
						$provider_id_pa2[]=$rfac[$i]['provider'];
				}
				if(sizeof($provider_id_pa2)>0){
							$provider_id121=implode(',',$provider_id_pa2);
							$t_provider= "select id,fname,lname from users where id in($provider_id121) order by lname";							
							$sqlt_provider = imw_query($t_provider);	
							$numt_provider=imw_num_rows($sqlt_provider);
							$str_ids="";
							while($vrst_provider=imw_fetch_array($sqlt_provider))
							{
									$idss=$vrst_provider['id'];
									$provider123 = $vrst_provider['lname']." ".$vrst_provider['fname'];
									/*if($pro == $idss) {
										$select1="SELECTED";
									}else{ 
										$select1="";
									}*/										
								$output.="<option value=$idss $select1>$provider123</option>";
							}
						}
						$output.="</select>";
	 	return $output;  
}


	
	/*************get the date when facility and provider scheduled by Ashwani Sharma********************/
	
	function getFacility_exist_date($dt,$locs){
			
			$get_date=explode("-",$dt);
			$y=$get_date[0];
			$mon=$get_date[1];
			$d=$get_date[2];
			$mktime=mktime(0,0,0,$mon,$d,$y);
			//echo date("Y-m-d",$mktime);
			$mont=date("n",$mktime);
			$week='';
			$r=getFacility($dt,$week,$locs);
			if(sizeof($r)<=0){
				for($i=$mont;$i<=12;$i++){
					$j=1;
					$i."*********<br>";
					$flag=true;
					if($flag==true){
						while($j<=31){
							$mktime2=mktime(0,0,0,$i,$j,$y);
							//echo date("Y-m-d",$mktime2)."--".date("Y-m-d",$mktime)."<br>";
								if(strtotime(date("Y-m-d",$mktime2))>=strtotime(date("Y-m-d",$mktime))){
										$dt2=date("Y-m-d",$mktime2);
										$r=getFacility($dt2,$Week,$locs);
										
										//echo date("l, Y-m-d",$mktime2)." size :".sizeof($r)."<br>";
										//echo sizeof($r);
										if(sizeof($r)>0){
											
											$flag=false;
										}
								}
								if(!$flag){
									break;
								}	
							$j++;
						}
					}
					
					if(!$flag){
						break;
					}	
				}				
						
			}else{
				$dt2=$dt;
			}
			return $dt2;	
				
	}
	
	
	/*****************end here ************************/
	
	
	function month_scheduleprovider_physician($dt,$sel_week_day,$sel_pro)
	{				
		$finalRes = getSchTmpData($dt,$sel_pro);
		$tmpTableIdArr = array();
		for($i=0;$i<count($finalRes);$i++){
			$tmpTableIdArr[] = $finalRes[$i]['id'];
		}
		$tmpTableId = join(',',$tmpTableIdArr);
		$qry = "select provider_schedule_tmp.facility,provider_schedule_tmp.sch_tmp_id,
				provider_schedule_tmp.provider from provider_schedule_tmp
				join users on provider_schedule_tmp.provider = users.id
				where provider_schedule_tmp.id in ($tmpTableId)
				order by users.lname,users.fname";
		$finalRes = ManageData::getQryRes($qry);
		//--- Start to get the data for display ----------------				
		$ids_nm='nm'.$dys;
		$myheight="";
		$output=""; 
		$cnt = 0;
		$cnt1 = 3;
		list($y,$m,$d) = preg_split('/-/',$dt);
		$day = date('w',mktime(0,0,0,$m,$d,$y));
		if($day == 6 || $day == 0){
			$cnt1 = 1;
		}
		
		$providerSchData = array();
		for($a = 0;$a < count($finalRes);$a++){
			$facilityId = $finalRes[$a]['facility'];
			$sch_tmp_id = $finalRes[$a]['sch_tmp_id'];
			$provider  = $finalRes[$a]['provider'];
			$vquery_css = "select schedule_name,morning_start_time,morning_end_time from schedule_templates where id='$sch_tmp_id'";				
			$vsql_css = imw_query($vquery_css);
			$rt_rss=imw_fetch_array($vsql_css);							
			$sch_name=$rt_rss['schedule_name'];	
			//GETTING START TIME AND END TIME ie A & P INSTEAD OF AM AND PM BY SURINDER
			$vquery_css = "select schedule_name,morning_start_time,morning_end_time from schedule_templates where id='$sch_tmp_id'";				
			$vsql_css = imw_query($vquery_css);
			$rt_rss = imw_fetch_array($vsql_css);
			$morning_start_time = $rt_rss['morning_start_time'];		
			list($start_hours,$start_minutes,$start_sec) = explode(":",$morning_start_time);
			$sch_time_start = mktime($start_hours, $start_minutes, $start_sec);
			$sch_start_time = date("g:iA",$sch_time_start);
			$morning_end_time = $rt_rss['morning_end_time'];
			list($start_hours,$start_minutes,$start_sec) = explode(":",$morning_end_time);
			$sch_time_end = mktime($start_hours, $start_minutes, $start_sec);
			$sch_end_time = date("g:iA",$sch_time_end);
			//$sch_end_time_value = $sch_start_time." : ".$sch_end_time;
			$sch_time = $sch_start_time."-".$sch_end_time;
			//END TIME GETTING
			//CODE TO GET FIRST TWO LETTER OF CITY(LOCATION)
			$vquery_d = "select name,city from facility where id='$facilityId'";				
			$vsql_d = imw_query($vquery_d);
			$rs_d = imw_fetch_array($vsql_d);							
			$fac_name=$rs_d["name"];
			$fac_city=$rs_d["city"];
			$fac_name_explode=explode(" ",$fac_name);
			$fac_name_firststring=substr($fac_name_explode{0},0,1);
			$fac_name_secondstring=substr($fac_name_explode{1},0,1);
			if($fac_name_secondstring==""){
				$fac_name_firststring=substr($fac_name_explode{0},0,2);
			}
			if($fac_name_secondstring=="") {  //if city has only single name like 'Colt' THEN GET 'CO'
				$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
			} else {  //if city has not single name like 'Colt Neck' then get 'CN'
				$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
			}	
			//END CODE TO GET FIRST TWO LETTER OF CITY(LOCATION)			
			//CODE TO GET PHYSICIAN INITIAL
			$physician_name_qry = "select fname,lname,provider_color,id from users where id='$provider'";
			$physician_name_res = imw_query($physician_name_qry);
			$physician_name_row = imw_fetch_array($physician_name_res);	
			$physician_name = array();
			$physician_name[] = substr($physician_name_row["fname"],0,1);
			$physician_name[] = substr($physician_name_row["lname"],0,1);
			$physician_intial = join($physician_name);
			if($physician_intial != '' || $fac_namefirst2letters != ''){
				$new_data .= '<tr><td alighn="left" class="text_10b">';
				$new_data .= str_replace('\'','',strtoupper($physician_intial)."-".strtoupper($fac_namefirst2letters).' ');
				$new_data .= $sch_time;
				$new_data .= '</td></tr>';
				if($cnt < $cnt1){
					$providerSchData[$cnt]['name'] = str_replace('\'','',strtoupper($physician_intial)."-".strtoupper($fac_namefirst2letters).' ');
					$providerSchData[$cnt]['provider_color'] = $physician_name_row["provider_color"];
					$providerSchData[$cnt]['id'] = $physician_name_row["id"];
					$providerSchData[$cnt]['facId'] = $facilityId;
					$cnt++;
				}
			}
		}
		//-- Tool Tip Div ----
		if($new_data){
			$output.= "
				<div style='display:none; background-color:#FFCC33; width:160px; z-index:1000; position:absolute;' id='".$dt."' >
					<table cellpadding='0' cellspacing='0'>
					".$new_data."
					</table>
				</div>
			";
		}
		if(count($finalRes)>0)
		{
			$MOUSEOUT="onMouseout='calender_tool_tip(\"".$dt."\",\"none\");'";
			$MOUSEOVER="onMouseOver='calender_tool_tip(\"".$dt."\",\"block\");'";
			$output.="<table cellpadding='0' border='0' $MOUSEOUT $MOUSEOVER cellspacing='0' style='padding-left:2px;'>";
		}
		for($a=0;$a<count($providerSchData);$a++){
			$provider = $providerSchData[$a]['id'];
			$name = $providerSchData[$a]['name'];
			$provider_color = $providerSchData[$a]['provider_color'];
			$facilityId = $providerSchData[$a]['facId'];
			//--- Get Records as per provider and facility and date ---------
			$qry = "select count(*) as rowCount from provider_notes 
					where provider_id = '$provider' and facility_id = '$facilityId' 
					and notes_date = '$dt'";
			$qryId = imw_query($qry);
			list($rowCount) = imw_fetch_array($qryId);
			$class= 'text_9';
			$provider_color1 = '';
			if($rowCount>0){
				$class= 'text_10b';
				$colorArr = array('#ECE9D8','#FEFED5','#FFFFCC','#FFCC33','#FFCC66');
				if(!in_array($provider_color,$colorArr)){
					$provider_color1 = 'style="color:'.$provider_color.'"';
				}
			}
			$output.= "<tr><td valign='top' class='text_9'>";	
			$output .= "<a $provider_color1 class='$class' href='javascript:void(0)' id='".$dt."_id_".$a."' onClick='open_note(\"$provider\",\"$facilityId\",\"$dt\",this.id)'>";
			$output.= $name;
			$output.= "</a></td></tr>";
		}
		if(sizeof($finalRes)>0)
		{
			$output.= "</table>";	
		}
		return $output;									
	} 
	
	function monthly_tooltip($dt,$sel_week_day,$sel_pro)
	{														
		$dt_exs=explode("-",$dt);
		$mth=$dt_exs[1];
		$yrs=$dt_exs[0];
		$dys=$dt_exs[2];			
		$months=date("n",mktime(0,0,0,$mth,$dys,$yrs));		
		$task_date=date("Y-m-d",mktime(0,0,0,$mth,$dys,$yrs));
		
		if($sel_pro!="" || $sel_pro!=0){
			$pro=" and provider='$sel_pro'";
		}elseif($sel_pro=="" || $sel_pro==0){
			$pro="";
		}
		
		$start_day = date('N',mktime(0,0,0,$mth,1,$yrs));
		//$week = getWeekDay($start_day,$dys);
		$week = getWeekCount($dys);
		
		$dt_ex = explode("_",$sel_week_day);	
		$weekday = $dt_ex[1];
		//$week =  $dt_ex[0];
		//------ Query To get The Exception Deleted Data ----------
		$qry = "select * from provider_schedule_tmp where today_date = '$dt'
				$pro and del_status = '1' and delete_row = 'no'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and today_date != $today_date";
				//print '<br>';
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}	
			
		//------ Query To get The All Deleted Data ----------
		
		$qry = "select * from provider_schedule_tmp where today_date <= '$dt'
				$pro and del_status = '1' and delete_row = 'all'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$id = $res['id'];
				$DeletedId[] = $id;
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id,provider from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and id < '$id'";
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}		
		if(isset($DeletedId)){
			$deletedId = implode($DeletedId,",");
		}
		//------- Query For Exceptional Data -----------
		//print $deletedId;
		if($deletedId){
			$qry = "select id,facility,sch_tmp_id,provider from provider_schedule_tmp where today_date = '$dt'  
				$pro and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' and id not in ($deletedId) group by provider,sch_tmp_id,facility";			
		}
		else{
			$qry = "select id,facility,sch_tmp_id,provider from provider_schedule_tmp where today_date = '$dt'  
				$pro and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' group by provider,sch_tmp_id,facility";			
		}
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes2 = fetchArray($qryId);
		}
		for($aa=0;$aa< count($finalRes2);$aa++){
			$dataId[] = $finalRes2[$aa]['id'];
		}
		//print_r($dataId);
		if(isset($dataId)){
			$Dataid = implode($dataId,",");
		}
		if($deletedId){
			if($Dataid){
				$Dataid .= ','.$deletedId;
			}
			else{
				$Dataid = $deletedId;
			}
		}
		//------- Query For All Data -----------
		//print $Dataid;
		if($Dataid){
			$qry = "select id,facility,sch_tmp_id,provider from provider_schedule_tmp where today_date <= '$dt'
					$pro and week$week = '$weekday' and del_status = 0
				 	and status = 'yes' and id not in($Dataid) group by provider,sch_tmp_id,facility";
		}
		else{
			$qry = "select id,facility,sch_tmp_id,provider from provider_schedule_tmp where today_date <= '$dt'  
					$pro and week$week = '$weekday' and del_status = 0 /*and status = 'yes'*/
					and delete_row = '' group by provider,sch_tmp_id,facility";
		}
		//echo $qry;
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes1 = fetchArray($qryId);		
		}
		$finalRes = array_merge((array)$finalRes1,(array)$finalRes2);
			//print_r($finalRes);
					//--- Start to get the data for display ----------------				
					$ids_nm='nm'.$dys;
					$myheight="";
					if(sizeof($finalRes)>0)
					{			$myheight=sizeof($finalRes)*15;								
								
								//print "<div id=$ids_nm style='height=1';'width=100%' onMouseOut='setMouseOutFlag();' onMouseOver='showSummaryInPopup(this,true);'>";									
								print "<table id=$tabid cellpadding='0' border=0 cellspacing='0' style='padding-left:2px;'>";
											
					}
											
					for($a = 0;$a < count($finalRes);$a++){	
					//print "<pre>";
					//print_r($finalRes[$a]);
						$facilityId = $finalRes[$a]['facility'];
						$sch_tmp_id = $finalRes[$a]['sch_tmp_id'];
						$provider  = $finalRes[$a]['provider'];
						$vquery_css = "select schedule_name,morning_start_time,morning_end_time from schedule_templates where id=$sch_tmp_id";				
											$vsql_css = imw_query($vquery_css);
											$rt_rss=imw_fetch_array($vsql_css);							
											$sch_name=$rt_rss['schedule_name'];	
											
											//GETTING START TIME AND END TIME ie A & P INSTEAD OF AM AND PM BY SURINDER
											$morning_start_time=$rt_rss['morning_start_time'];		
			
											$start_explode=explode(":",$morning_start_time);
											$start_hours=$start_explode[0];
											$start_minutes=$start_explode[1];
											$start_sec=$start_explode[2];			
											
											$sch_time_start=@mktime($start_hours, $start_minutes, $start_sec, date($mth)  , date($dys), date($yrs));
											$sch_start_time=date("g:iA",$sch_time_start);
											
											$sch_start_time_explode=explode(":",$sch_start_time);
											$sch_start_time_explode1=$sch_start_time_explode{0};
											$sch_start_time_explode2=$sch_start_time_explode{1};
											$sch_start_time_explode2_value=$sch_start_time_explode2{0}.$sch_start_time_explode2{1}.$sch_start_time_explode2{2};
											$sch_start_time_value=$sch_start_time_explode1.":".$sch_start_time_explode2_value;
											
											$morning_end_time=$rt_rss['morning_end_time'];		
											
											$end_explode=explode(":",$morning_end_time);
											$end_hours=$end_explode[0];
											$end_minutes=$end_explode[1];
											$end_sec=$end_explode[2];			
											
											$sch_time_end=@mktime($end_hours, $end_minutes, $end_sec, date($mth)  , date($dys), date($yrs));
											$sch_end_time=date("g:iA",$sch_time_end);
											
											$sch_end_time_explode=explode(":",$sch_end_time);
											$sch_end_time_explode1=$sch_end_time_explode{0};
											$sch_end_time_explode2=$sch_end_time_explode{1};
											$sch_end_time_explode2_value=$sch_end_time_explode2{0}.$sch_end_time_explode2{1}.$sch_end_time_explode2{2};
											$sch_end_time_value=$sch_end_time_explode1.":".$sch_end_time_explode2_value;
											//if($sch_start_time_value!="12:00A" && $sch_end_time_value!="12:00A"){
												$sch_time=$sch_start_time_value."-".$sch_end_time_value;
											//}else{
												//$sch_time="All day";
											//}
											
											//END TIME GETTING
											
											//CODE TO GET FIRST TWO LETTER OF CITY(LOCATION)
											$vquery_d = "select name,city from facility where id='$facilityId'";				
								$vsql_d = imw_query($vquery_d);
								$rs_d = imw_fetch_array($vsql_d);							
								$fac_name=$rs_d["name"];
								$fac_city=$rs_d["city"];
								$fac_name_explode=explode(" ",$fac_name);
								
								$fac_name_firststring=substr($fac_name_explode{0},0,1);
								$fac_name_secondstring=substr($fac_name_explode{1},0,1);
								if($fac_name_secondstring==""){
									$fac_name_firststring=substr($fac_name_explode{0},0,2);
								}
								if($fac_name_secondstring=="") {  //if city has only single name like 'Colt' THEN GET 'CO'
									$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
								} else {  //if city has not single name like 'Colt Neck' then get 'CN'
									$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
								}	
								//END CODE TO GET FIRST TWO LETTER OF CITY(LOCATION)
								
								//CODE TO GET PHYSICIAN INITIAL
							 	$physician_name_qry="select fname,lname from users where id='$provider'";
							
								$physician_name_res=imw_query($physician_name_qry);
								$physician_name_row = imw_fetch_array($physician_name_res);	
								//$physician_name=$physician_name_row["fname"]." ".$physician_name_row["lname"];
								$physician_fname=$physician_name_row["fname"];
								$physician_lname=$physician_name_row["lname"];
								$physician_fname_intial=$physician_fname{0};
								$physician_lname_intial=$physician_lname{0};
								$physician_intial=trim($physician_fname_intial.$physician_lname_intial);
								//END CODE TO GET PHYSICIAN INITIAL
								
								
								if($provider<>"") {
												//Added by Ram
											if($physician_intial<>"" && $fac_namefirst2letters<>""){
												
											print "<tr  valign=top><td valign='top'  class='text_9'>";	
											echo strtoupper(strtolower($physician_intial))."-".strtoupper(strtolower($fac_namefirst2letters))." ".$sch_time.$img;//."<br>";
											}
								}else {
									//Added by Ram
									if($physician_intial<>"" && $fac_namefirst2letters<>""){
										print "<tr style='$disp' valign=top><td valign='top' class='text_9'>";	
										echo strtoupper(strtolower($physician_intial))."-".strtoupper(strtolower($fac_namefirst2letters))." ".$sch_time.$img;//."<br>";//." ".$sch_time."";		
									}				
										
	
								}
																
					} 
					if(sizeof($finalRes)>0)
					{
							
							print "</td></tr>";
							print "</table>";	
							
					}
						
											
	}

	function countQuarters($begindate, $enddate)
    {
           if (!isset($begindate) || empty($begindate) || !isset($enddate) || empty($enddate))
               return -1;
           
           $countyears = date("Y", strtotime($enddate)) - date("Y", strtotime($begindate));
           $quarters = 0;
           
           if (date("Y", strtotime($enddate)) == date("Y", strtotime($begindate)))
           {
               if (date("m", strtotime($enddate)) != date("m", strtotime($begindate)))
               {
                   if (date("m", strtotime($enddate)) > date("m", strtotime($begindate)))
                   {
                       $difference = date("m", strtotime($enddate)) - date("m", strtotime($begindate));
                       
                       $quarters += ceil((int) $difference / 4);
                   }
                   else
                   {
                       return -1;
                   }
               }
           }
           else
           {
               $quarters = (int) $countyears * 4;
               if (date("m", strtotime($enddate)) != date("m", strtotime($begindate)))
               {
                   if (date("m", strtotime($enddate)) > date("m", strtotime($begindate)))
                   {
                       $difference = date("m", strtotime($enddate)) - date("m", strtotime($begindate));
                       
                       $quarters += ceil((int) $difference / 4);
                   }
                   else
                   {
                       $afterbegin = 12 - (int) date("m", strtotime($begindate));
                       $untilend = date("m", strtotime($enddate));
                       
                       $quarters = ($quarters - 4) + ceil(($afterbegin + $untilend) / 4);
                   }
               }
           }
           
           return $quarters;
       } 
	   
	   
function dateDiffs($dformat, $endDate, $beginDate)
{
$date_parts1=explode($dformat, $beginDate);
$date_parts2=explode($dformat, $endDate);
$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
return $end_date - $start_date;
}

   
function dateDiff_patient($dformat, $endDate, $beginDate)
{
$date_parts1=explode($dformat, $beginDate);
$date_parts2=explode($dformat, $endDate);
$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
return $end_date - $start_date;
}

////////////Function To GET Insurance Case Name Information will return Name of case/////////////
function get_insurance_case_name_schedule($case_id,$returnVision="No"){
	$selqry=imw_query("select *from insurance_case where ins_caseid='".$case_id."'");
	$resarray=imw_fetch_array($selqry);
	$ret_val="";
	if($resarray){		
		$selqrtype=imw_query("select * from insurance_case_types  where case_id='".$resarray["ins_case_type"]."'");
		$resarraytype=imw_fetch_array($selqrtype);
		if($resarraytype){
			if($returnVision=="Yes"){
				$ret_val=$resarraytype["case_name"]."-".$resarray["ins_caseid"]."-".$resarraytype["vision"];		
			}else{
				 $ret_val=$resarraytype["case_name"]."-".$resarray["ins_caseid"];
			}

		}
	}
	return($ret_val);

}
///////End Function To GET Insurance Case Name Information will return Name of case///////////
function FormatDate_insert_frontDesk($dt)
{
	if(!empty($dt))
	{
		//if(ereg("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})",$dt,$regs))
		if(preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/",$dt,$regs))
		{
			$dt=$regs[3]."-".$regs[1]."-".$regs[2];
			return $dt;
		}
	}
		
	return $dt;
}


//********************
//monthly_tooltip($dt,$sel_week_day,$sel_pro)
function getSchedule_tmp($dt,$provider,$sel_week_day,$facility){
					
					$dt_exs=explode("-",$dt);
					$mth=$dt_exs[1];
					$yrs=$dt_exs[0];
					$dys=$dt_exs[2];			
					$months=date("n",mktime(0,0,0,$mth,$dys,$yrs));
					$task_date=date("Y-m-d",mktime(0,0,0,$mth,$dys,$yrs));
					//$dt_ex = explode("_",$sel_week_day);	
					//echo $weekday = $dt_ex[1];
					$weekday=$sel_week_day;
					$start_day = date('N',mktime(0,0,0,$mth,1,$yrs));
					//$week = getWeekDay($start_day,$dys);
					$week = getWeekCount($dys);
					//$week =  $dt_ex[0];
					if($provider<>""){
						$pro=" and provider  in($provider)  "; 
					}else{
						$pro="";
					}
					//------ Query To get The Exception Deleted Data ----------
					$qry = "select * from provider_schedule_tmp where today_date = '$dt'
							$pro and del_status = '1' and facility in($facility) and delete_row = 'no'";
					$qryId = imw_query($qry);
					if(imw_num_rows($qryId)>0){
						while($res = imw_fetch_array($qryId)){
							$provider = $res['provider'];
							$facility = $res['facility'];
							$sch_tmp_id = $res['sch_tmp_id'];
							$week1 = $res['week1'];
							$week2 = $res['week2'];
							$week3 = $res['week3'];
							$week4 = $res['week4'];
							$week5 = $res['week5'];
							$week6 = $res['week6'];
							$today_date = $res['today_date'];
							$query = "select id from provider_schedule_tmp where provider = '$provider'
									and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
									and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
									and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
									and today_date != $today_date";
							//print '<br>';
							$queryRes = imw_query($query);
							if(imw_num_rows($queryRes)>0){
								while($res1 = imw_fetch_array($queryRes)){
									$DeletedId[] = $res1['id'];	
								}
							}
						}
					}	
						
					//------ Query To get The All Deleted Data ----------
					
					$qry = "select * from provider_schedule_tmp where today_date <= '$dt'
							$pro and del_status = '1' and facility in($facility) and delete_row = 'all'";
					$qryId = imw_query($qry);
					if(imw_num_rows($qryId)>0){
						while($res = imw_fetch_array($qryId)){
							$id = $res['id'];
							$DeletedId[] = $id;
							$provider = $res['provider'];
							$facility = $res['facility'];
							$sch_tmp_id = $res['sch_tmp_id'];
							$week1 = $res['week1'];
							$week2 = $res['week2'];
							$week3 = $res['week3'];
							$week4 = $res['week4'];
							$week5 = $res['week5'];
							$week6 = $res['week6'];
							$today_date = $res['today_date'];
							$query = "select id from provider_schedule_tmp where provider = '$provider'
									and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
									and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
									and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
									and id < '$id'";
							//print '<br>';
							$queryRes = imw_query($query);
							if(imw_num_rows($queryRes)>0){
								while($res1 = imw_fetch_array($queryRes)){
									$DeletedId[] = $res1['id'];	
								}
							}
						}
					}		
					if(isset($DeletedId)){
						$deletedId = @implode($DeletedId,",");
					}
					//------- Query For Exceptional Data -----------
					if($deletedId){
						$qry = "select id,facility,sch_tmp_id,provider from provider_schedule_tmp where today_date = '$dt'  
							$pro and week$week = '$weekday' and facility in($facility) and del_status = 0 and status = 'no'
							and delete_row = '' and id not in ($deletedId) group by provider,sch_tmp_id,facility";			
					}
					else{
						$qry = "select id,facility,sch_tmp_id,provider from provider_schedule_tmp where today_date = '$dt'  
							$pro and week$week = '$weekday' and facility in($facility) and del_status = 0 and status = 'no'
							and delete_row = '' group by provider,sch_tmp_id,facility";			
					}
					$qryId = imw_query($qry);
					if(imw_num_rows($qryId)>0){
						$finalRes2 = fetchArray($qryId);
					}
					for($aa=0;$aa< count($finalRes2);$aa++){
						$dataId[] = $finalRes2[$aa]['id'];
					}
					//print_r($dataId);
					if(isset($dataId)){
						$Dataid = @implode($dataId,",");
					}
					if($deletedId){
						if($Dataid){
							$Dataid .= ','.$deletedId;
						}
						else{
							$Dataid = $deletedId;
						}
					}
					//------- Query For All Data -----------
					//print $Dataid;
					if($Dataid){
						$qry = "select id,facility,sch_tmp_id,provider from provider_schedule_tmp where today_date <= '$dt'
									  $pro and week$week = '$weekday' and facility in($facility) and del_status = 0
									  and status = 'yes' and id not in($Dataid) group by provider,sch_tmp_id,facility";
					}
					else{
						$qry = "select id,facility,sch_tmp_id ,provider from provider_schedule_tmp where today_date <= '$dt'  
								$pro and week$week = '$weekday' and facility in($facility) and del_status = 0 /*and status = 'yes'*/
								and delete_row = ''  group by provider,sch_tmp_id,facility";
					}
					//echo $qry;
					$qryId = imw_query($qry);
					if(imw_num_rows($qryId)>0){
						$finalRes1 = fetchArray($qryId);		
					}
					$finalRes = array_merge((array)$finalRes1,(array)$finalRes2);
					
										
					for($a = 0;$a < count($finalRes);$a++){	
					
						$sch_tmp_ids[] = $finalRes[$a]['sch_tmp_id'];
					
														
					} 
					
		//$sch_tm_idss=array_unique($sch_tmp_ids);
		if(isset($sch_tmp_ids)){
			$sch_tmp_id=@implode($sch_tmp_ids,",");
		}else{
		
		}
		return $sch_tmp_id;
}

function find_week_day($dt)
{
	$dt_exs=explode("-",$dt);	
	$mth=number_format($dt_exs[1],0);
	$yrs=$dt_exs[0];
	$dys=$dt_exs[2];	
	$flag=true;		   			
	$counter=1;
	$week=1;
	while(($counter<=32) && ($flag))
	{
		$date_first=@getdate(mktime(0,0,0,$mth,$counter,$yrs));							
		$curr_week=$date_first["wday"];
		
		if($curr_week==0)
		{
			$week=$week+1;
		}							
		if($counter==$dys)
		{
			$send_data[0]=$week;
			$send_data[1]=$curr_week;						
			break;
		}
		if($date_first["mon"] != $mth)
		{						
			$lastday_of_month=$counter-1;
			$flag=false; 
		}							
		$counter++;
	}	
	$iweek=$send_data[0];
	$iwkday=$send_data[1];
	$eff_wday=@date("w",mktime(0, 0, 0, $mth,1,$yrs));						
	if($eff_wday==0)
	{
		$eff_wday=7;
	}
	if($iweek==1){
		$find_week="week1";			
	}
	elseif($iweek==2){										
		if(($eff_wday > $iwkday))
		{
				$find_week="week1";	
		}else{	
				$find_week="week2";	
		}											
	}
	elseif($iweek==3){			
		if(($eff_wday > $iwkday))
		{
				$find_week="week2";	
		}else{	
				$find_week="week3";	
		}			
	}
	elseif($iweek==4){			
		if(($eff_wday > $iwkday))
		{
				$find_week="week3";	
		}else{	
				$find_week="week4";	
		}			
	}
	elseif($iweek==5){			
		if(($eff_wday > $iwkday))
		{
				$find_week="week4";	
		}else{	
				$find_week="week5";	
		}			
	}
	elseif($iweek==6){			
		if(($eff_wday > $iwkday))
		{
				$find_week="week5";	
		}else{	
				$find_week="week6";	
		}			
	}
	$send_data[2]=$find_week;
	return $send_data;
}

function check_provider($dat,$sel_pro){														
		//echo $sel_pro;
		
		$dt_exs=explode("-",$dat);
		$mth=$dt_exs[0];
		$yrs=$dt_exs[2];
		$dys=$dt_exs[1];			
		$months=date("n",mktime(0,0,0,$mth,$dys,$yrs));		
		$task_date=date("Y-m-d",mktime(0,0,0,$mth,$dys,$yrs));
		
		if($sel_pro!="" || $sel_pro!=0){
			$pro=" and provider='$sel_pro'";
		}elseif($sel_pro=="" || $sel_pro==0){
			$pro="";
		}
		
		$week_data=array();
		$week_data=find_week_day($task_date);
		
		$weeknumber=$week_data[0];
		$weekday=$week_data[1];
		$week =getWeekCount($dys);
		$dt=$task_date;
			
		
		//$week =  $dt_ex[0];
		//------ Query To get The Exception Deleted Data ----------
		$qry = "select * from provider_schedule_tmp where today_date = '$dt'
				$pro and del_status = '1' and delete_row = 'no'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and today_date != $today_date";
				//print '<br>';
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}	
			
		//------ Query To get The All Deleted Data ----------
		
		$qry = "select * from provider_schedule_tmp where today_date <= '$dt'
				$pro and del_status = '1' and delete_row = 'all'";
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($res = imw_fetch_array($qryId)){
				$id = $res['id'];
				$DeletedId[] = $id;
				$provider = $res['provider'];
				$facility = $res['facility'];
				$sch_tmp_id = $res['sch_tmp_id'];
				$week1 = $res['week1'];
				$week2 = $res['week2'];
				$week3 = $res['week3'];
				$week4 = $res['week4'];
				$week5 = $res['week5'];
				$week6 = $res['week6'];
				$today_date = $res['today_date'];
				$query = "select id,provider from provider_schedule_tmp where provider = '$provider'
						and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
						and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
						and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
						and id < '$id'";
				$queryRes = imw_query($query);
				if(imw_num_rows($queryRes)>0){
					while($res1 = imw_fetch_array($queryRes)){
						$DeletedId[] = $res1['id'];	
					}
				}
			}
		}		
		if(isset($DeletedId)){
			$deletedId = implode($DeletedId,",");
		}
		//------- Query For Exceptional Data -----------
		//print $deletedId;
		if($deletedId){
			$qry = "select provider from provider_schedule_tmp where today_date = '$dt'  
				$pro and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' and id not in ($deletedId) group by provider,sch_tmp_id,facility";			
		}
		else{
			$qry = "select provider from provider_schedule_tmp where today_date = '$dt'  
				$pro and week$week = '$weekday' and del_status = 0 and status = 'no'
				and delete_row = '' group by provider,sch_tmp_id,facility";			
		}
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes2 = fetchArray($qryId);
		}
		for($aa=0;$aa< count($finalRes2);$aa++){
			$dataId[] = $finalRes2[$aa]['id'];
		}
		//print_r($dataId);
		if(isset($dataId)){
			$Dataid = implode($dataId,",");
		}
		if($deletedId){
			if($Dataid){
				$Dataid .= ','.$deletedId;
			}
			else{
				$Dataid = $deletedId;
			}
		}
		//------- Query For All Data -----------
		//print $Dataid;
		if($Dataid){
			$qry = "select provider from provider_schedule_tmp where today_date <= '$dt'
						 	$pro and week$week = '$weekday' and del_status = 0
						  and status = 'yes' and id not in($Dataid) group by provider,sch_tmp_id,facility";
		}
		else{
			$qry = "select provider from provider_schedule_tmp where today_date <= '$dt'  
					$pro and week$week = '$weekday' and del_status = 0 /* and status = 'yes'*/
					and delete_row = '' group by provider,sch_tmp_id,facility";
		}
		//echo $qry;
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId)>0){
			$finalRes1 = fetchArray($qryId);		
		}
		$finalRes = array_merge((array)$finalRes1,(array)$finalRes2);
		for($i=0;$i<count($finalRes);$i++){
			$provder[]=$finalRes[$i]['provider'];
		}
		//echo sizeof($provder);
		if(count($provder)>0){
			if(in_array($sel_pro,$provder)){
				$ret= "1";
			}else{
				$ret="0";
			}
		}else{
			$ret= "0";
		}
		echo $ret;
}			
	
	
	function procedure_schedule_appointment(){
		$qry="select  max(sa_app_start_date) as max ,min(sa_app_start_date) as min,procedureid ,id from schedule_appointments  GROUP BY schedule_appointments.procedureid"; 
		$qryId=imw_query($qry);
		if(imw_num_rows($qryId)>0){
			while($ids_row=imw_fetch_array($qryId)){
				if($ids_row['max']<>""){
					$maxdate=strtotime($ids_row['max']);
					$mindate=strtotime($ids_row['min']);
					if(($maxdate<=strtotime(date("Y-m-d"))) || ($mindate<=strtotime(date("Y-m-d")))){ 
						$procedure_not_ids[]=$ids_row['procedureid'];
					}
				}
			}
		}
		$procedure_not_id=@implode(",",$procedure_not_ids);
		
		return $procedure_not_id;
	}					


function generateCalender($sel_month,$year,$df_pro){
	$output="";
	$cur_date=date("j");
	$colrs="";
	$cur_date_color="#FEFED5";
	$title_bg_color="#ECE9D8";
	if($sel_month<>"") {
		$month_number=$sel_month;
		$year=$year;
		$date=getdate(mktime(0,0,0,$month_number,1,$year));
		
	}
	$month_name=$date["month"]; // get month name
		
	$date_first=getdate(mktime(0,0,0,$month_number,1,$year)); //get info of first day of this month
		$first_week_day=$date_first["wday"]; //get first week day as 0 is Sunday and so on
		$last_week_day=$date_first["wday"]; //get first week day as 0 is Sunday and so on
		
		$lastday_of_month = date("t",mktime(0, 0, 0,$month_number,1,$year));
	
	
	
		$output.='<table border="0" width="100%" bgcolor="#FEFED5" cellpadding="0" cellspacing="0" style="border-left:1px solid #F6C67A;">							
				
				
				<tr height="10" bgcolor='.$title_bg_color.'>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Monday</td>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Tuesday</td>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Wednesday</td>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Thursday</td>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Friday</td>								
					<td width="16%" class="text_9b" align="center" style="border-bottom:1px inset #999999;">Sat/Sun</td>								
				</tr>
				';
								
								
					$bg_close_color="#FEFED5";	
					$day=1;							
					/// LAST DAY OF MONTH + ADD ALL WEEK DAYS ////							
					$prev_month_days=date("w",(mktime(0,0,0,$month_number,$day,$year)));							
					$next_month_days=date("w",(mktime(0,0,0,$month_number,$lastday_of_month,$year)));									
					/////////////////////////////////////////////
															
					$firstweek=true;
					if($prev_month_days==0)
					{
						$fwday=(7-$first_week_day); //store for future use as value of $first_week_day is changed then														
						$first_week_day=7-$first_week_day;
						$prev_month_days=7-$prev_month_days;
					}else{
					
						$fwday=($first_week_day-1); //store for future use as value of $first_week_day is changed then														
						$first_week_day=$fwday;
						$prev_month_days=$prev_month_days;
					
					}
					
					//print $next_month_days;
					 $last_day=$lastday_of_month;
					//print $next_month_days;
					if($next_month_days>0)
					{
						$lastday_of_month=$lastday_of_month+(6-$next_month_days);
					}
					//echo $lastday_of_month;
					// Make AS Many TR And TD for storing values
					// Make AS Many TR And TD for storing values
					
					$ex1='col1';
					$ex2='col2';
					$ex3='col3';
					$ex4='col4';							
					$wk=1;
					
					if($prev_month_days >= 6){
						$endLoop = 29;
					}
					else{
						$endLoop = $lastday_of_month;					
					}
					$count = $lastday_of_month - $endLoop;
					if($count > 1){
						if($count == 7){
							$add_date = 2;
						}
						else{
							$add_date = 1;
						}
						$lastDates = $endLoop + $add_date;
					}
					else{
						$lastDates = '';
						$break = true;
					}
					while( $day <= $endLoop)
					{
						if($firstweek)
						{
							$output.= "<tr  height=\"15\">";												
							for($i=1;$i<=$first_week_day;$i++)
							{
								
								$dy=$day-($prev_month_days-$i);										
								$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$dy,$year));	
								$eff_day=date("j",mktime(0, 0, 0, $month_number,$dy,$year));
								$eff_month=date("F",mktime(0, 0, 0, $month_number,$dy,$year));
								$eff_wday=date("w",mktime(0, 0, 0, $month_number,$dy,$year));							
								$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
									
								if($i==6)
								{
										$output.= "<td  colspan=2>";
										$output.="<table cellpadding='0' cellspacing='0'  width='100%' border='0'>";
										$output.= "<tr  height='15'>";
											
												if($i < $first_week_day)
												{
													$output.= "<td align='right'  class='text_9'>";
													
												}else{
														
													$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
													$td_bg_color=$bg_close_color;
													$output.= "<td align='right' valign='top' bgcolor='$td_bg_color' class='text_9'>&nbsp;";
													$output.= "</td>";		
													$output.= "<td align='right' valign='top' bgcolor='$td_bg_color' class='text_9'>&nbsp;";
													
													if($i == $first_week_day)
													{
														$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
														//print $eff_month." ";
													}
													$output.=  $eff_day."</b>&nbsp;";
												}				
												
												
												$output.= "</td></tr>";											
												$output.= "<tr   height='15'>";
										
												if($i < $first_week_day)
												{
													
													$output.= "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp";
													$output.= "</td>";
													$output.= "<td style='border-bottom:2px inset #ffffff;'>&nbsp;";
													
												}else{
														
													$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
													 if($cur_date==$eff_day){
														$style ="style=color:$colrs";
														$td_bg_color=$cur_date_color;
													}else{
														$style="";
														$td_bg_color=$bg_close_color;
													}		
													$output.= "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp";
													$output.= "</td>";			
													
													$output.= "<td valign='top' style='border-bottom:2px inset #ffffff;'>";
													
												}
													
													$output.= "</td>";	
															
										$output.= "</tr>";	
											
										$day=$day+1;
										$i=$i+1;
										$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$dy+1,$year));
										$eff_day=date("j",mktime(0, 0, 0, $month_number,$dy+1,$year));
										$eff_wday=date("w",mktime(0, 0, 0, $month_number,$dy+1,$year));
										
										$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
												
										$output.= "<tr id='color$eff_day'  height='15' >";
										
												if($i < $first_week_day)
												{
														$output.= "<td align='right' bgcolor='#FEF4BC' class='text_9'>&nbsp;";
												}else{
														
														//print $wk++;
														$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
														
														$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
														$td_bg_color=$bg_close_color;
														
														 if($cur_date==$eff_day){
															$style ="style=color:$colrs";
															$td_bg_color=$cur_date_color;
														}else{
															$style="";
															$td_bg_color=$bg_close_color;
														}		
														$output.= "<td colspan='2' id=$eff_day$ex2 valign='top' align='right' class='text_9'>";
													
														if($i == $first_week_day)
														{
															$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
															//$eff_day=date("d",
														}											
														$effday=date('l',mktime(0,0,0,$month_number,$eff_day,$year));
														$output.= "<a $style href=\"javascript:changeDay_physician($month_number,$eff_day,$year,'$effday');\">";
														$output.=  $eff_day."</b>&nbsp;";
														$output.=  "</a>";
														
												}											
										$output.= "</td></tr>";	
										$output.= "<tr id='color$eff_day' height='15'>";
												
												if($i < $first_week_day)
												{
													$output.= "<td bgcolor='#FEF4BC' style='border-bottom:2px inset #ffffff;'> &nbsp;";
												}else{
													$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
													 if($cur_date==$eff_day){
														$style ="style=color:$colrs";
														$td_bg_color=$cur_date_color;
													}else{
														$style="";
														$td_bg_color=$bg_close_color;
													}		
													
													$output.= "<td  id=$eff_day$ex3 valign='top' colspan=2 valign='top'  style='border-bottom:2px inset #ffffff;'>";
													
													$output.= "<div id='month_1_physician'>";
													$output.= "<div id='month_2_physician'>";
													$output.= "<div id='month_3_physician'>";	
													$output.= month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
													$output.= "&nbsp;</div>";
													$output.= "</div>";
													$output.= "</div>";	
												}
												
												$output.= "</td>";							
										$output.= "</tr></table>";									
									$output.= "</td>";
									$output.= "</tr>";								
									$fwday=$fwday-1;
										
								}else{
										if($cur_date==$lastDates){
											$style ="style=color:$colrs;";
											$td_bg_color=$cur_date_color;
										}else{
											$style="";
											$td_bg_color=$bg_close_color;
										 }
									$output.= "<TD valign='top' id=\"color$lastDates\" style='border-right:2px inset #ffffff;border-bottom:2px inset #ffffff;'>";
									$output.= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
									if($lastDates){
										$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$lastDates,$year));
										$eff_day=date("j",mktime(0, 0, 0, $month_number,$lastDates,$year));
										$eff_wday=date("w",mktime(0, 0, 0, $month_number,$lastDates,$year));
										$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$lastDates,$year));
										$eff_day_arr='6'."_".$eff_wday."_".$eff_day;
										
										$output.= "<tr>";
										$output.= "<TD>";
										$output.= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
										$output.= "<tr>";
										$output.= "<td valign='top' id=$day$ex3 align='right' class='text_9'>";
																					
										//print "<tr valign='top'  bgcolor='$td_bg_color'><td id=$day$ex3 colspan='2' bgcolor='$td_bg_color' class='text_9'>";												
										
										$output .= "<div><a class='text_9b' href='javascript:void(0)' onClick='open_note(\"$provider\",\"$facilityId\")'>";
										$output.=  month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
										$output.= "</a></div>";
										$output.= "</TD>";
										$output.= "<td valign='top' id=$day$ex2 align='right' class='text_9'>";
										if(($lastDates==1)||($lastDates == ($last_day+1)))
										{
											$eff_month=date("F",mktime(0, 0, 0, $month_number,$lastDates,$year));
										}
										
										
									
										$effday=date('l',mktime(0,0,0,$month_number,$lastDates,$year));
										$output.=  "<a $style href=\"javascript:changeDay_physician($month_number,$lastDates,$year,'$effday');\">";
										$output.= $lastDates;
										$output.=  "</a>";
										$output.= "</td>";
										
										$output.= "</tr></table>";																			
									}
									else{
										$output.= "<tr >";
										$output.= "<TD valign='top'>";
									}
									if($lastDates < $last_day && $break == false){
										$lastDates++;
										$break = false;
									}
									else{
										$lastDates ='';
										$break = true;
									}
									$output.= "</TD>";
									$output.= "</tr></table></td>";										  
							  }
							}
							$firstweek=false;
						}
						//print $fwday;
						if($fwday == 0)
						{
							$output.= "<TR valign='top'>";
						}	
						
						if($fwday == 5)
						{
							
							$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_day=date("j",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_wday=date("w",mktime(0, 0, 0, $month_number,$day,$year));
							
							$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
							if($cur_date==$eff_day){
								$style ="style=color:$colrs";
								//$td_bg_color=$cur_date_color;
							}else{
								$style="";
								//$td_bg_color=$bg_close_color;
							}		
												
							
								$output.= "<td  valign='top' colspan=2>";
								$output.= "<table  cellpadding='0' vspace=0 cellspacing='0' height='100%' width='100%' border='0'>";
								$output.= "<tr  id='color$eff_day' valign='top'>";
									
										if($day > $last_day)
										{
											
											//print "<td align='center' valign='top' class='text_9'>&nbsp;";
											//	print "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp;";
										$output.= "<td  style='height:25px;border-bottom:2px inset #ffffff;'>&nbsp;";
										
										}else{
											
									
											/// SATURDAY
										$output.= "<td  valign='top' style='height:25px;border-bottom:2px inset #ffffff;'>";
												
										$output.= "<div id='month_1_physician'>";
										$output.= "<div id='month_2_physician'>";
										$output.= "<div id='month_3_physician'>";															
										$output.= month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
										$output.= "&nbsp;</div>";
										$output.= "</div>";
										$output.= "</div>";	
												
											
										}				
										
										
																														
										//print "<tr valign='top'>";
								
										if($day > $last_day)
										{
											
										//	print "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp;";
											$output.= "<td style='height:25px;border-bottom:2px inset #ffffff;'>&nbsp;";
										
										}else{
											$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
											if($cur_date==$eff_day){
												$style ="style=color:$colrs";
												$td_bg_color=$cur_date_color;
											}else{
												$style="";
												$td_bg_color=$bg_close_color;
											}		
									
											$output.= "<td  align='right'   class='text_9' valign='top' style='height:25px;border-bottom:2px inset #ffffff;'>";
										
											//print "<td   align='left' >";													
											if(($day==1)||($day == ($last_day+1)))
											{
												$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
												//print $eff_month." ";
											}			
											$effday=date('l',mktime(0,0,0,$month_number,$eff_day,$year));
											$output.= "<a $style href=\"javascript:changeDay_physician($month_number,$eff_day,$year,'$effday');\" >";
											$output.= "&nbsp;".$eff_day;																
											$output.= "</a>";	
												
										}
										$output.= "</td>";	
										
													
								$output.= "</tr>";	
								// SUNDAY	
								$day=$day+1;
								$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$day,$year));
								$eff_day=date("j",mktime(0, 0, 0, $month_number,$day,$year));
								$eff_wday=date("w",mktime(0, 0, 0, $month_number,$day,$year));
									
								$output.= "<tr id='color$eff_day' height='15' >";
								
										if($day > $last_day)
										{
											//		print "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp;";
										$output.= "<td style='height:25px;border-bottom:2px inset #ffffff;'>&nbsp;";
										
												//print "<td align='right' class='text_9'>&nbsp;";
												
													
										}else{
													//print $wk;
													$wk++;
													$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
													
													$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
													if($cur_date==$eff_day){
														$style ="style=color:$colrs";
														$td_bg_color=$cur_date_color;
													}else{
														$style="";
														$td_bg_color=$bg_close_color;
													}		
											$output.= "<td  id=$day$ex3  style='height:25px;border-bottom:2px inset #ffffff;'>";
											$output.= "<div id='month_1_physician'>";
											$output.= "<div id='month_2_physician'>";
											$output.= "<div id='month_3_physician'>";	
											$output.= month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
											$output.= "&nbsp;</div>";
											$output.= "</div>";
											$output.= "</div>";	
													
													
										}											
								
									
								
								$output.= "</td>";		
									
							//	print "<tr height='15'>";
										if($day > $last_day)
										{
											
											
										$output.= "<td  style='height:25px;border-bottom:2px inset #ffffff;'>&nbsp;";
												
										}else{
												
												$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
												if($cur_date==$eff_day){
														$style ="style=color:$colrs";
														$td_bg_color=$cur_date_color;
													}else{
														$style="";
														$td_bg_color=$bg_close_color;
													}		
																										
												
												   $output.= "<td id=$day$ex2 valign='top' align='right' style='border-right:0px inset #ffffff;border-bottom:2px inset #ffffff;'  class='text_9'>";
													if(($day==1)||($day == ($last_day+1)))
													{
														$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
													}																					
													$effday=date('l',mktime(0,0,0,$month_number,$eff_day,$year));
												$output.= "<a $style href=\"javascript:changeDay_physician($month_number,$eff_day,$year,'$effday');\">";
	
												$output.= "&nbsp;".$eff_day;															
												$output.= "</a>";
													
												
										}
									
							$output.= "</td>";		
							$output.= "</tr></table>";										
							$output.= "</td>";
							$output.= "</tr>";
							
						}else{
						
							$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_day=date("j",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_wday=date("w",mktime(0, 0, 0, $month_number,$day,$year));
							//print $wk;									
							$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
							//print $day.'>'.$last_day;									
							if($day > $last_day)
							{
								$output.= "<TD style='border-right:2px inset #ffffff;border-bottom:2px inset #ffffff;'>&nbsp;";
									
							}else{
								$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
								
								if($cur_date==$eff_day){
									$style ="style=color:$colrs";
									$td_bg_color=$cur_date_color;
								}else{
									$style="";
									$td_bg_color=$bg_close_color;
								}		
													
								$output.= "<TD id='color$eff_day' valign='top'  bgcolor=".$td_bg_color." style='border-right:2px inset #ffffff;border-bottom:2px inset #ffffff;'>";
								$output.= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
								$output.= "<TR ><td valign='top' id=$day$ex2 align='center' class='text_9'>";
										//print $last_day;											
										if(($day==1)||($day == ($last_day+1)))
										{
											$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
										}
																				
									//	print "<tr height='15'><td id=$day$ex3 colspan='2' style='height:2px;' class='text_9'>";												
									$output.=month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
									$output.="</TD><td valign='top' id=$day$ex2 align='right' class='text_9' >";
									
									$effday=date('l',mktime(0,0,0,$month_number,$eff_day,$year));
									$output.= "<a $style href=\"javascript:changeDay_physician($month_number,$eff_day,$year,'$effday');\">";
									$output.='&nbsp;'.$eff_day;
									$output.= "</a>";
									$output.= "</td>";	
								$output.= "</tr></table>";																							
							}											
							$output.= "</td>";
						}	
						$fwday++;
						$fwday = $fwday % 6;											
						$day++;														
					}
							
		
							
		$output.="</table>";
		
	
	

		
	$qry="Select month from schedular_calender where 
	 month='$sel_month' and year='$year' and provider_id='$df_pro'";
	 
	 $res=imw_query($qry);
	 if(imw_num_rows($res)<=0){
		$qry="insert into schedular_calender
		set month='$sel_month',
		year='$year',
		provider_id='$df_pro',
		matter='".addslashes($output)."'
		
		";
		//echo "\n".$qry."\n";
		imw_query($qry);
	
	}else{
		$qry="update schedular_calender
		set month='$sel_month',
		year='$year',
		matter='".addslashes($output)."',
		provider_id='$df_pro'
		where 
		month='$sel_month' and year='$year' and provider_id='$df_pro'";
		imw_query($qry);
	}
	return $output;
}

//--- Not in use -------
function getCalender12($sel_month,$year,$df_pro){
		
		
	$output="";
	
	$cur_date=date("j");
	$colrs="";
	//$cur_date_color="#FFCC66";
	$cur_date_color="#FEFED5";
	
	$title_bg_color="#ECE9D8";
	
	if($sel_month<>"") {
		$month_number=$sel_month;
		$year=$year;
		$date=getdate(mktime(0,0,0,$month_number,1,$year));
		
	}
	$month_name=$date["month"]; // get month name
		
	$date_first=getdate(mktime(0,0,0,$month_number,1,$year)); //get info of first day of this month
		$first_week_day=$date_first["wday"]; //get first week day as 0 is Sunday and so on
		$last_week_day=$date_first["wday"]; //get first week day as 0 is Sunday and so on
		
		$lastday_of_month = date("t",mktime(0, 0, 0,$month_number,1,$year));
	
	
	
		$output.='<table border="0" width="100%" bgcolor="#FEFED5" cellpadding="0" cellspacing="0" style="border-left:1px solid #F6C67A;">							
				
				
				<tr height="10" bgcolor='.$title_bg_color.'>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Monday</td>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Tuesday</td>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Wednesday</td>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Thursday</td>
					<td width="16%" class="text_9b" align="center" style="border-right:2px inset #ffffff;border-bottom:1px inset #999999;">Friday</td>								
					<td width="16%" class="text_9b" align="center" style="border-bottom:1px inset #999999;">Sat/Sun</td>								
				</tr>
				';
								
								
					$bg_close_color="#FEFED5";	
					$day=1;							
					/// LAST DAY OF MONTH + ADD ALL WEEK DAYS ////							
					$prev_month_days=date("w",(mktime(0,0,0,$month_number,$day,$year)));							
					$next_month_days=date("w",(mktime(0,0,0,$month_number,$lastday_of_month,$year)));									
					/////////////////////////////////////////////
															
					$firstweek=true;
					if($prev_month_days==0)
					{
						$fwday=(7-$first_week_day); //store for future use as value of $first_week_day is changed then														
						$first_week_day=7-$first_week_day;
						$prev_month_days=7-$prev_month_days;
					}else{
					
						$fwday=($first_week_day-1); //store for future use as value of $first_week_day is changed then														
						$first_week_day=$fwday;
						$prev_month_days=$prev_month_days;
					
					}
					
					//print $next_month_days;
					 $last_day=$lastday_of_month;
					//print $next_month_days;
					if($next_month_days>0)
					{
						$lastday_of_month=$lastday_of_month+(6-$next_month_days);
					}
					//echo $lastday_of_month;
					// Make AS Many TR And TD for storing values
					// Make AS Many TR And TD for storing values
					
					$ex1='col1';
					$ex2='col2';
					$ex3='col3';
					$ex4='col4';							
					$wk=1;
					
					if($prev_month_days >= 6){
						$endLoop = 29;
					}
					else{
						$endLoop = $lastday_of_month;					
					}
					$count = $lastday_of_month - $endLoop;
					if($count > 1){
						if($count == 7){
							$add_date = 2;
						}
						else{
							$add_date = 1;
						}
						$lastDates = $endLoop + $add_date;
					}
					else{
						$lastDates = '';
						$break = true;
					}
					while( $day <= $endLoop)
					{
						if($firstweek)
						{
							$output.= "<tr  height=\"15\">";												
							for($i=1;$i<=$first_week_day;$i++)
							{
								
								$dy=$day-($prev_month_days-$i);										
								$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$dy,$year));	
								$eff_day=date("j",mktime(0, 0, 0, $month_number,$dy,$year));
								$eff_month=date("F",mktime(0, 0, 0, $month_number,$dy,$year));
								$eff_wday=date("w",mktime(0, 0, 0, $month_number,$dy,$year));							
								$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
									
								if($i==6)
								{
										$output.= "<td  colspan=2>";
										$output.="<table cellpadding='0' cellspacing='0'  width='100%' border='0'>";
										$output.= "<tr  height='15'>";
											
												if($i < $first_week_day)
												{
													$output.= "<td align='right'  class='text_9'>";
													
												}else{
														
													$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
													$td_bg_color=$bg_close_color;
													$output.= "<td align='right' valign='top' bgcolor='$td_bg_color' class='text_9'>&nbsp;";
													$output.= "</td>";		
													$output.= "<td align='right' valign='top' bgcolor='$td_bg_color' class='text_9'>&nbsp;";
													
													if($i == $first_week_day)
													{
														$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
														//print $eff_month." ";
													}
													$output.=  $eff_day."</b>&nbsp;";
												}				
												
												
												$output.= "</td></tr>";											
												$output.= "<tr   height='15'>";
										
												if($i < $first_week_day)
												{
													
													$output.= "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp";
													$output.= "</td>";
													$output.= "<td style='border-bottom:2px inset #ffffff;'>&nbsp;";
													
												}else{
														
													$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
													 if($cur_date==$eff_day){
														$style ="style=color:$colrs";
														$td_bg_color=$cur_date_color;
													}else{
														$style="";
														$td_bg_color=$bg_close_color;
													}		
													$output.= "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp";
													$output.= "</td>";			
													
													$output.= "<td valign='top' style='border-bottom:2px inset #ffffff;'>";
													
												}
													
													$output.= "</td>";	
															
										$output.= "</tr>";	
											
										$day=$day+1;
										$i=$i+1;
										$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$dy+1,$year));
										$eff_day=date("j",mktime(0, 0, 0, $month_number,$dy+1,$year));
										$eff_wday=date("w",mktime(0, 0, 0, $month_number,$dy+1,$year));
										
										$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
												
										$output.= "<tr id='color$eff_day'  height='15' >";
										
												if($i < $first_week_day)
												{
														$output.= "<td align='right' bgcolor='#FEF4BC' class='text_9'>&nbsp;";
												}else{
														
														//print $wk++;
														$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
														
														$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
														$td_bg_color=$bg_close_color;
														
														 if($cur_date==$eff_day){
															$style ="style=color:$colrs";
															$td_bg_color=$cur_date_color;
														}else{
															$style="";
															$td_bg_color=$bg_close_color;
														}		
														$output.= "<td colspan='2' id=$eff_day$ex2 valign='top' align='right' class='text_9'>";
													
														if($i == $first_week_day)
														{
															$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
															//$eff_day=date("d",
														}											
														$effday=date('l',mktime(0,0,0,$month_number,$eff_day,$year));
														$output.= "<a $style href=\"javascript:changeDay_physician($month_number,$eff_day,$year,'$effday');\">";
														$output.=  $eff_day."</b>&nbsp;";
														$output.=  "</a>";
														
												}											
										$output.= "</td></tr>";	
										$output.= "<tr id='color$eff_day' height='15'>";
												
												if($i < $first_week_day)
												{
													$output.= "<td bgcolor='#FEF4BC' style='border-bottom:2px inset #ffffff;'> &nbsp;";
												}else{
													$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
													 if($cur_date==$eff_day){
														$style ="style=color:$colrs";
														$td_bg_color=$cur_date_color;
													}else{
														$style="";
														$td_bg_color=$bg_close_color;
													}		
													
													$output.= "<td  id=$eff_day$ex3 valign='top' colspan=2 valign='top'  style='border-bottom:2px inset #ffffff;'>";
													
													$output.= "<div id='month_1_physician'>";
													$output.= "<div id='month_2_physician'>";
													$output.= "<div id='month_3_physician'>";	
													$output.= month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
													$output.= "&nbsp;</div>";
													$output.= "</div>";
													$output.= "</div>";	
												}
												
												$output.= "</td>";							
										$output.= "</tr></table>";									
									$output.= "</td>";
									$output.= "</tr>";								
									$fwday=$fwday-1;
										
								}else{
										if($cur_date==$lastDates){
											$style ="style=color:$colrs;";
											$td_bg_color=$cur_date_color;
										}else{
											$style="";
											$td_bg_color=$bg_close_color;
										 }
									$output.= "<TD valign='top' id=\"color$lastDates\" style='border-right:2px inset #ffffff;border-bottom:2px inset #ffffff;'>";
									$output.= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
									if($lastDates){
										$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$lastDates,$year));
										$eff_day=date("j",mktime(0, 0, 0, $month_number,$lastDates,$year));
										$eff_wday=date("w",mktime(0, 0, 0, $month_number,$lastDates,$year));
										$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$lastDates,$year));
										$eff_day_arr='6'."_".$eff_wday."_".$eff_day;
										
										$output.= "<tr>";
										$output.= "<TD>";
										$output.= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
										$output.= "<tr>";
										$output.= "<td valign='top' id=$day$ex3 align='right' class='text_9'>";
																					
										//print "<tr valign='top'  bgcolor='$td_bg_color'><td id=$day$ex3 colspan='2' bgcolor='$td_bg_color' class='text_9'>";												
										
										$output.=  month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
										$output.= "</TD>";
										$output.= "<td valign='top' id=$day$ex2 align='right' class='text_9'>";
										if(($lastDates==1)||($lastDates == ($last_day+1)))
										{
											$eff_month=date("F",mktime(0, 0, 0, $month_number,$lastDates,$year));
										}
										
										
									
										$effday=date('l',mktime(0,0,0,$month_number,$lastDates,$year));
										$output.=  "<a $style href=\"javascript:changeDay_physician($month_number,$lastDates,$year,'$effday');\">";
										$output.= $lastDates;
										$output.=  "</a>";
										$output.= "</td>";
										
										$output.= "</tr></table>";																			
									}
									else{
										$output.= "<tr >";
										$output.= "<TD valign='top'>";
									}
									if($lastDates < $last_day && $break == false){
										$lastDates++;
										$break = false;
									}
									else{
										$lastDates ='';
										$break = true;
									}
									$output.= "</TD>";
									$output.= "</tr></table></td>";										  
							  }
							}
							$firstweek=false;
						}
						//print $fwday;
						if($fwday == 0)
						{
							$output.= "<TR valign='top'>";
						}	
						
						if($fwday == 5)
						{
							
							$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_day=date("j",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_wday=date("w",mktime(0, 0, 0, $month_number,$day,$year));
							
							$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
							if($cur_date==$eff_day){
								$style ="style=color:$colrs";
								//$td_bg_color=$cur_date_color;
							}else{
								$style="";
								//$td_bg_color=$bg_close_color;
							}		
												
							
								$output.= "<td  valign='top' colspan=2>";
								$output.= "<table  cellpadding='0' vspace=0 cellspacing='0' height='100%' width='100%' border='0'>";
								$output.= "<tr  id='color$eff_day' valign='top'>";
									
										if($day > $last_day)
										{
											
											//print "<td align='center' valign='top' class='text_9'>&nbsp;";
											//	print "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp;";
										$output.= "<td  style='height:25px;border-bottom:2px inset #ffffff;'>&nbsp;";
										
										}else{
											
									
											/// SATURDAY
										$output.= "<td  valign='top' style='height:25px;border-bottom:2px inset #ffffff;'>";
												
										$output.= "<div id='month_1_physician'>";
										$output.= "<div id='month_2_physician'>";
										$output.= "<div id='month_3_physician'>";															
										$output.= month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
										$output.= "&nbsp;</div>";
										$output.= "</div>";
										$output.= "</div>";	
												
											
										}				
										
										
																														
										//print "<tr valign='top'>";
								
										if($day > $last_day)
										{
											
										//	print "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp;";
											$output.= "<td style='height:25px;border-bottom:2px inset #ffffff;'>&nbsp;";
										
										}else{
											$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
											if($cur_date==$eff_day){
												$style ="style=color:$colrs";
												$td_bg_color=$cur_date_color;
											}else{
												$style="";
												$td_bg_color=$bg_close_color;
											}		
									
											$output.= "<td  align='right'   class='text_9' valign='top' style='height:25px;border-bottom:2px inset #ffffff;'>";
										
											//print "<td   align='left' >";													
											if(($day==1)||($day == ($last_day+1)))
											{
												$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
												//print $eff_month." ";
											}			
											$effday=date('l',mktime(0,0,0,$month_number,$eff_day,$year));
											$output.= "<a $style href=\"javascript:changeDay_physician($month_number,$eff_day,$year,'$effday');\" >";
											$output.= "&nbsp;".$eff_day;																
											$output.= "</a>";	
												
										}
										$output.= "</td>";	
										
													
								$output.= "</tr>";	
								// SUNDAY	
								$day=$day+1;
								$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$day,$year));
								$eff_day=date("j",mktime(0, 0, 0, $month_number,$day,$year));
								$eff_wday=date("w",mktime(0, 0, 0, $month_number,$day,$year));
									
								$output.= "<tr id='color$eff_day' height='15' >";
								
										if($day > $last_day)
										{
											//		print "<td valign='top' style='border-bottom:2px inset #ffffff;'>&nbsp;";
										$output.= "<td style='height:25px;border-bottom:2px inset #ffffff;'>&nbsp;";
										
												//print "<td align='right' class='text_9'>&nbsp;";
												
													
										}else{
													//print $wk;
													$wk++;
													$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
													
													$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
													if($cur_date==$eff_day){
														$style ="style=color:$colrs";
														$td_bg_color=$cur_date_color;
													}else{
														$style="";
														$td_bg_color=$bg_close_color;
													}		
											$output.= "<td  id=$day$ex3  style='height:25px;border-bottom:2px inset #ffffff;'>";
											$output.= "<div id='month_1_physician'>";
											$output.= "<div id='month_2_physician'>";
											$output.= "<div id='month_3_physician'>";	
											$output.= month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
											$output.= "&nbsp;</div>";
											$output.= "</div>";
											$output.= "</div>";	
													
													
										}											
								
									
								
								$output.= "</td>";		
									
							//	print "<tr height='15'>";
										if($day > $last_day)
										{
											
											
										$output.= "<td  style='height:25px;border-bottom:2px inset #ffffff;'>&nbsp;";
												
										}else{
												
												$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
												if($cur_date==$eff_day){
														$style ="style=color:$colrs";
														$td_bg_color=$cur_date_color;
													}else{
														$style="";
														$td_bg_color=$bg_close_color;
													}		
																										
												
												   $output.= "<td id=$day$ex2 valign='top' align='right' style='border-right:0px inset #ffffff;border-bottom:2px inset #ffffff;'  class='text_9'>";
													if(($day==1)||($day == ($last_day+1)))
													{
														$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
													}																					
													$effday=date('l',mktime(0,0,0,$month_number,$eff_day,$year));
												$output.= "<a $style href=\"javascript:changeDay_physician($month_number,$eff_day,$year,'$effday');\">";
	
												$output.= "&nbsp;".$eff_day;															
												$output.= "</a>";
													
												
										}
									
							$output.= "</td>";		
							$output.= "</tr></table>";										
							$output.= "</td>";
							$output.= "</tr>";
							
						}else{
						
							$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_day=date("j",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_wday=date("w",mktime(0, 0, 0, $month_number,$day,$year));
							//print $wk;									
							$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
							//print $day.'>'.$last_day;									
							if($day > $last_day)
							{
								$output.= "<TD style='border-right:2px inset #ffffff;border-bottom:2px inset #ffffff;'>&nbsp;";
									
							}else{
								$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
								
								if($cur_date==$eff_day){
									$style ="style=color:$colrs";
									$td_bg_color=$cur_date_color;
								}else{
									$style="";
									$td_bg_color=$bg_close_color;
								}		
													
								$output.= "<TD id='color$eff_day' valign='top'  bgcolor=".$td_bg_color." style='border-right:2px inset #ffffff;border-bottom:2px inset #ffffff;'>";
								$output.= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
								$output.= "<TR ><td valign='top' id=$day$ex2 align='center' class='text_9'>";
										//print $last_day;											
										if(($day==1)||($day == ($last_day+1)))
										{
											$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
										}
																				
									//	print "<tr height='15'><td id=$day$ex3 colspan='2' style='height:2px;' class='text_9'>";												
									$output.=month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$df_pro);
									$output.="</TD><td valign='top' id=$day$ex2 align='right' class='text_9' >";
									
									$effday=date('l',mktime(0,0,0,$month_number,$eff_day,$year));
									$output.= "<a $style href=\"javascript:changeDay_physician($month_number,$eff_day,$year,'$effday');\">";
									$output.='&nbsp;'.$eff_day;
									$output.= "</a>";
									$output.= "</td>";	
								$output.= "</tr></table>";																							
							}											
							$output.= "</td>";
						}	
						$fwday++;
						$fwday = $fwday % 6;											
						$day++;														
					}
							
		
							
		$output.="</table>";
		
	
	return $output;

}



function display_Month($sel_date,$month,$year,$provider, $locs = 0){
	
	if(empty($provider)){
		$provider = 0;
	}	
	$qry = "select matter,provider_notes_id from schedular_calender
			where month='".(int)$month."' and
			year='$year' and provider_id ='$provider'";
	$res = imw_query($qry);	
	list($matter,$provider_notes_id) = imw_fetch_array($res);
	if(imw_num_rows($res)>0 && $provider_notes_id == 0){
$matter = getCalender($sel_date,$month,$year,$provider, $locs);	
		return $matter;
	}else{
		$matter = getCalender($sel_date,$month,$year,$provider, $locs);		
		return $matter;
	}
}

function getCalender($sel_date,$month,$year,$pro=0, $locs = 0){
	//--- Generate a new calander -----
	
	$start_count = date('w',mktime(0,0,0,$month,1,$year));
	$last_month_date = date('t',mktime(0,0,0,$month,1,$year));
	$end_count = date('w',mktime(0,0,0,$month,$last_month_date,$year));
	$dateMonthName=date('F',mktime(0,0,0,$month,1,$year));
	$showDayDiv='<div id="addProviderDiv" style="display:none; width:450px; z-index:1000; background-color:#FFCC00;" class="text_9"><a STYLE="color:#000000; font-size:12px;" href="javascript:openAddproviderOption();" ONMOUSEOVER="Highlight(this);" ONMOUSEOUT="Highlight(this);"><b>  Add Provider  </b></a></div>';
	$cal_html = '
		<table cellpadding="0" cellspacing="0" border="1" width="100%" height="194px" style="border-collapse:collapse;" bordercolor="#cccccc">
			<tr>
				<td class="text_9b" colspan="6" bgcolor="#ECE4D7" align="center">'.$dateMonthName.'</td>
			</tr>
			<tr>
				<td class="text_9b" bgcolor="#ECE9D8" align="center">Mon</td>
				<td class="text_9b" bgcolor="#ECE9D8" align="center">Tue</td>
				<td class="text_9b" bgcolor="#ECE9D8" align="center">Wed</td>
				<td class="text_9b" bgcolor="#ECE9D8" align="center">Thu</td>
				<td class="text_9b" bgcolor="#ECE9D8" align="center">Fri</td>
				<td class="text_9b" bgcolor="#ECE9D8" align="center">Sat/Sun</td>
			</tr>
	';

	if($start_count > 5 && $last_month_date == 31){
		$j = $last_month_date;
	}
	else if($start_count == 5 && $last_month_date == 27){
		$j = $last_month_date;
	}
	else if($start_count == 0 && $last_month_date == 30){
		$j = $last_month_date;
	}
	else if($start_count == 0 && $last_month_date > 30){
		$j = $last_month_date - 1;
	}
	$blank == false;
	for($i=0;$i<5;$i++){
		$cal_html1 = '';
		for($d=0;$d<6;$d++){
			if($i == 0){
				if($j == ''){
					if($start_count > 0 ){
						$j = ($start_count - 1) == $d  ? 1 : '';
					}
				}
			}
			//---- Check for Saturday and Sunday --------
			if($d == 5){
				$cal_html2 = '';				
				for($s=0;$s<2;$s++){
					$phy_data = '';
					if($start_count == 0 && $j == ''){
						$j = $s;
					}
					else if($start_count == 6 && $j == ''){
						$j = $s;
					}
					$j = $j == 0 ? '' : $j;
					$s_data = '';
					if($j >= 0){
						
						$eff_date_add = date('Y-m-d',mktime(0,0,0,$month,$j,$year));
						$eff_day = date("j",mktime(0, 0, 0, $month,$j,$year));
						$eff_wday = date("w",mktime(0, 0, 0, $month,$j,$year));
						$eff_day_arr = ($i+1).'_'.$eff_wday.'_'.$eff_day;
						//Code HighLight Physcican Avaialable Day//
						//$seleCtedFacid=getSelectedFacilityId();
						$seleCtedFacid = $locs;
						if(!empty($seleCtedFacid) && $pro > 0){
							//$returnedArray=getSchTmpData($eff_date_add,$pro,$seleCtedFacid);
							 $phy_data =month_scheduleprovider_Highlight($eff_date_add,$eff_day_arr,$pro,$seleCtedFacid);//"AV";
						}else{
							$phy_data ="";//month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$pro);
						}

						if($j == $sel_date){
							$tdBgColor = '#FEFED5';// '#FFCC66';
							$s_data .= '<input type="hidden" id="defalut_color" name="defalut_color" value="change_clr_id'.$j.$month.$year.'" />';
						}
						else{
							$tdBgColor =  '#FEFED5';
						}
						if($phy_data!=""){
								$tdBgColor =  '#C835E8';//purple
						}
						//Code HighLight Physcican Avaialable Day//
					}
					if($s == 0){
						$s_data .= '
							<tr height="1">
								<td colspan="2" bgcolor="#FFFFFF" width="100%"></td>
							</tr>
							<tr height="1">
								<td colspan="2" bgcolor="#C0C0C0" width="100%"></td>
							</tr>
						';
					}
					if($j!=""){
					$onmouseDown='onMouseDown ="pop_AddproviderDiv(\''.$month.'-'.$j.'-'.$year.'\')"';
					
					}else{
					$onmouseDown="";
					
					}
					//$commonSetDump='setdumpFormValues(\''.date('Y-m-d').'\',\''.$j.'\',\''.$month.'\',\''.$year.'\',\''.date('l',mktime(0,0,0,$month,$j,$year)).'\',\''.mktime(0,0,0,$month,$j,$year).'\');';
					//<td width="0"   style="cursor:hand;" valign="top" align="center" onClick="setdumpFormValues(\''.date('Y-m-d').'\',\''.$j.'\',\''.$month.'\',\''.$year.'\',\''.date('l',mktime(0,0,0,$month,$j,$year)).'\',\''.mktime(0,0,0,$month,$j,$year).'\'); load_sch(\''.$j.'\');" >'.$phy_data.'</td>
			$cal_html2 .= '
						<tr  bgcolor="'.$tdBgColor.'" id="change_clr_id'.$j.$month.$year.'" '.$onmouseDown.'>
							<td width="0">'.$phy_data.'<input type="hidden" name="defalutPreviousColor_id'.$j.$month.$year.'"  id="defalutPreviousColor_id'.$j.$month.$year.'" value="'.$tdBgColor.'"/></td>
							<td class="text_10b"   style="cursor:hand;"  align="center">
								<a href="javascript:void(0);" onClick="setdumpFormValues(\''.date('Y-m-d').'\',\''.$j.'\',\''.$month.'\',\''.$year.'\',\''.date('l',mktime(0,0,0,$month,$j,$year)).'\',\''.mktime(0,0,0,$month,$j,$year).'\');load_sch(\''.$j.'\');">'.$j.'</a>
								
							</td>
						</tr>
						'.$s_data.'
					';
					if($j >= 0 && $j < $last_month_date && $j != ''){
						$j++;
					}
					else{
						$j = '';
					}
				}
				$cal_html1 .= '
					<td   height="28px" bgcolor="#FEFED5" >
						<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
						'.$cal_html2.'
						</table>
					</td>
				';
			}
			else{
				if($j == $sel_date){
					$tdBgColor ='#FEFED5';//  '#FFCC66';
					$cal_html1 .= '<input type="hidden" name="defalut_color" id="defalut_color" value="change_clr_id'.$j.$month.$year.'"/>';
				}
				else{
					$tdBgColor =  '#FEFED5';
				}
				$phy_data = '';
				if($j >= 0){
					$eff_date_add = date('Y-m-d',mktime(0,0,0,$month,$j,$year));
					$eff_day = date("j",mktime(0, 0, 0, $month,$j,$year));
					$eff_wday = date("w",mktime(0, 0, 0, $month,$j,$year));
					$eff_day_arr = ($i+1).'_'.$eff_wday.'_'.$eff_day;
						//$seleCtedFacid=getSelectedFacilityId();
						$seleCtedFacid = $locs;
						if(!empty($seleCtedFacid) && $pro > 0){
						$phy_data =month_scheduleprovider_Highlight($eff_date_add,$eff_day_arr,$pro,$seleCtedFacid);//"AV";
						}else{
							$phy_data ="";//month_scheduleprovider_physician($eff_date_add,$eff_day_arr,$pro);
						}

						if($phy_data!=""){
						$tdBgColor =  '#C835E8';
						}
						//Code HighLight Physcican Avaialable Day//
					
				}
			if($j!=""){
			$onmouseDown='onMouseDown ="pop_AddproviderDiv(\''.$month.'-'.$j.'-'.$year.'\')"';
			}else{
			$onmouseDown="";
			}
				$cal_html1 .= '
					<td class="text_10b" height="28px"   id="change_clr_id'.$j.$month.$year.'" bgcolor="'.$tdBgColor.'" '.$onmouseDown.'>
						<table cellpadding="0" cellspacing="0" border="0">
							<tr >
								<td width="0"  style="cursor:hand;" valign="top" align="center" >'.$phy_data.'<input type="hidden" name="defalutPreviousColor_id'.$j.$month.$year.'"  id="defalutPreviousColor_id'.$j.$month.$year.'" value="'.$tdBgColor.'"/></td>
								<td  class="text_10b" width="10"   style="cursor:hand;" align="center">
									<a href="javascript:void(0);" onClick="setdumpFormValues(\''.date('Y-m-d').'\',\''.$j.'\',\''.$month.'\',\''.$year.'\',\''.date('l',mktime(0,0,0,$month,$j,$year)).'\',\''.mktime(0,0,0,$month,$j,$year).'\');load_sch(\''.$j.'\');">'.$j.'</a>
									<div id="addprovider'.$j.'"></div>
								</td>
							</tr>
						</table>
					</td>
				';
				if($j != ''){
					$j++;
				}
			}
			if($j > $last_month_date){
				$j = '';
			}
		}
		$cal_html .= '
			<tr>
				'.$cal_html1.'
			</tr>
		';
	}
	$cal_html .= '</table>'.$showDayDiv;
	//--- Save In database -------
	$qry = "Select calender_id from schedular_calender where 
			month='$month' and year='$year' and provider_id='$pro'";
	 $qryId = imw_query($qry);
	 list($calender_id) = imw_fetch_array($qryId);
	 if(imw_num_rows($qryId)<=0){
		$qry = "insert into schedular_calender
				set month='$month',year='$year',provider_notes_id = '0',
				provider_id='$pro',matter='".addslashes($cal_html)."'";
	}else{
		$qry = "update schedular_calender
				set month='$month',year='$year',provider_notes_id = '0',
				matter='".addslashes($cal_html)."',provider_id='$pro'
				where calender_id = '$calender_id'";
	}
	imw_query($qry);
	$qry = "update schedular_calender
			set provider_notes_id = '0' WHERE provider_notes_id = '1'";
	imw_query($qry);
	return $cal_html;
}
function getOfficeTimings($dt,$strFacIds,$providerToLoad="", $dirPath=""){
    //echo  "$dt,$providerToLoad"; die;
	list($y,$m,$d) = explode('-',$dt);
	$dt = date('Y-m-d',mktime(0,0,0,$m,$d,$y));
	$finalResArr = getSchTmpData($dt,$providerToLoad,$strFacIds);
    
    //print "<pre>";
    //print_r($finalResArr);
    //die("amit");
	$tmpTableIdArr = array();
	for($i=0;$i<count($finalResArr);$i++){
		$tmpTableIdArr[] = $finalResArr[$i]['id'];
	}
	$tmpTableId = join(',',$tmpTableIdArr);
	$qry = "select provider_schedule_tmp.facility,provider_schedule_tmp.sch_tmp_id,
			provider_schedule_tmp.provider from provider_schedule_tmp
			join users on provider_schedule_tmp.provider = users.id
			where provider_schedule_tmp.id in ($tmpTableId) 
			order by users.user_type,users.lname,users.fname";
    
	$finalResArr = ManageData::getQryRes($qry);
    //print "<pre>";
    //print_r($finalResArr);
    //die("amit");	
	$facilityId = array();
	$providerId = array();
	$mainTmpId = array();
	$sch_tmp_id = array();
	for($i=0;$i<count($finalResArr);$i++){
		$sch_tmp_id[] = $finalResArr[$i]['sch_tmp_id'];
		$facilityId[] = $finalResArr[$i]['facility'];
		$providerId[] = $finalResArr[$i]['provider'];
		$facility_id = $finalResArr[$i]['facility'];
		$FacProviderId[$facility_id][$finalResArr[$i]['provider']] = $finalResArr[$i]['provider'];
		$provider_id = $finalResArr[$i]['provider'];
		$facSchTmpId[$facility_id][] = $finalResArr[$i]['sch_tmp_id'];
		$mainTmpId[$facility_id][$provider_id][] = $finalResArr[$i]['sch_tmp_id'];
	}	
	$sch_tmp_id = array_unique($sch_tmp_id);
	$schTmpId = join(',',$sch_tmp_id);
	$facilityId = array_unique($facilityId);
	$facilityId = array_values($facilityId);
	$providerId = array_unique($providerId);
	$providerId = array_values($providerId);
	$facSchTmp_id = array_keys($facSchTmpId);
	$facSchTmpId = array_values($facSchTmpId);	
	
	for($g=0;$g<count($facilityId);$g++){
		$FacProviderId[$facilityId[$g]] = array_values($FacProviderId[$facilityId[$g]]);
	}
	for($f=0;$f<count($facSchTmpId);$f++){
		$schTmpId = join(',',$facSchTmpId[$f]);
		//---- Start Get Facility Open and close Time ------------
		$qry = "select min(morning_start_time) as morning_start_time 
				from schedule_templates where id in($schTmpId)";
		$startTimeRes = ManageData::getQryRes($qry);
		$slotStartTimeArr = explode(':',substr($startTimeRes[0]['morning_start_time'],0,-3));
		$qry = "select max(morning_end_time) as morning_end_time
				from schedule_templates where id in($schTmpId)";
		$endTimeRes = ManageData::getQryRes($qry);
		$officeCloseTime = substr($endTimeRes[0]['morning_end_time'],0,2);
		$slotEndTime = explode(':',substr($endTimeRes[0]['morning_end_time'],0,-3));
		$j = 1;
		//-- get Office Open Hours ------	
		for($o=$slotStartTimeArr[0];$o<=$officeCloseTime;$o++){
			$j++;
		}
		$hrToMin = ($j * 60) + $slotEndTime[1];		
		$startHrTime = $slotStartTimeArr[0] - 1;
		$startMinTime = $slotStartTimeArr[1];
		$startHrTime = $startHrTime < 10 ? '0'.(int)$startHrTime : $startHrTime;
		$schTmpFacTime[$facSchTmp_id[$f]]['start_time'] = $startHrTime.':'.$startMinTime;
		$schTmpFacTime[$facSchTmp_id[$f]]['hrToMin'] = $hrToMin;
	}	
	//---- End Get Facility Open and close Time ------------
	//--- Get Time Sloat as per facility -----
	/*$qry = "select regular_time_slot from facility where id = '1'";
	$slotRes = ManageData::getQryRes($qry);
	$timeSlotRes = $slotRes[0]['regular_time_slot'];*/
	$timeSlotRes = DEFAULT_TIME_SLOT;
	if(count($facilityId)>0){
		//---- Xml File header Content --------
		$xmlData = '<?xml version="1.0" encoding="UTF-8" ?>
			<schedule>
				<dt value="'.$dt.'">
					<facilities>
		';
		//--- Start Facility loop -------
		for($f=0;$f < count($facilityId);$f++){
			//--- Get Facility Id --------
			$facId = $facilityId[$f];
			$fac_start_time = $schTmpFacTime[$facId]['start_time'];
			$facHrToMin = $schTmpFacTime[$facId]['hrToMin'];
			$xmlData .= '
				<facility id="'.$facilityId[$f].'" start_time="'.$fac_start_time.'" hrToMin="'.$facHrToMin.'">
					<providers>
			';
			//--- Start Provider loop -------
			for($p=0;$p<count($FacProviderId[$facId]);$p++){
				//--- Get Provider Details -------
				$pid = $FacProviderId[$facId][$p];
				$qry = "select provider_color,lname,fname,mname,user_type,max_appoint
						from users where id = '$pid'";
				$phyRes = ManageData::getQryRes($qry);
				$phyLname = $phyRes[0]['lname'];
				$phyFname = $phyRes[0]['fname'];
				$phyMname = $phyRes[0]['mname'];
				$phyName = $phyLname;
				if($phyLname != '' && $phyFname != ''){
					$phyName = $phyLname.', '.$phyFname;
				}
				else if($phyLname == '' && $phyFname != ''){
					$phyName = $phyFname;
				}
				$phyName .= ' '.$phyMname;
				$provider_color = $phyRes[0]['provider_color'];
				$user_type = $phyRes[0]['user_type'];
				$max_appoint = $phyRes[0]['max_appoint'];
				$qry = "select count(*) as rowCount from provider_notes 
						where provider_id = '$pid' and facility_id = '$facId'
						and delete_status = '0' and notes_date = '$dt'";
				$rowRes = ManageData::getQryRes($qry);
				$notesStatus = 0;
				if($rowRes[0]['rowCount'] > 0){
					$notesStatus = 1;
				}
				$xmlData .= '					
						<provider id="'.$pid.'" name="'.trim($phyName).'" color="'.$provider_color.'" type="'.$user_type.'" notes="'.$notesStatus.'" max_appoint="'.$max_appoint.'">
							<slots>
				';
				$schTmpId = join(',',$mainTmpId[$facId][$pid]);
				$slotStartTimeArr = explode(':',$fac_start_time);
				$startHrTime2 = $slotStartTimeArr[0];
				$startTime = $slotStartTimeArr[1];
				//--- Start Time loop -------				
				for($t=0;$t<$facHrToMin;$t++){
					if($startTime == 0){
						$startTime = '00';
					}
					if($startTime == 5 && DEFAULT_TIME_SLOT == 5){
						$startTime = '05';
					}
					if($startTime >= 60){
						$startHrTime2++;
						$startTime = '00';
					}
					if($startHrTime > 12){
						$startHrTime = 1;
					}
					$endHrTime = $startHrTime2;
					if($startTime >= (60 - DEFAULT_TIME_SLOT)){
						$endHrTime++;
						$endTime = '00';
					}
					else{
						$endTime = $startTime+$timeSlotRes;
					}
					if($endTime == 5 && DEFAULT_TIME_SLOT == 5){
						$endTime = '05';
					}
					if($startHrTime2 < 10){
						$startHrTime2 = '0'.(int)$startHrTime2;
					}
					if($endHrTime < 10){
						$endHrTime = '0'.(int)$endHrTime;
					}
					
					//--- Start Get Specific Template Open Time---------
					$times_from = $startHrTime2.':'.$startTime.':00';					
					$qry = "select id,fldLunchStTm,fldLunchEdTm,morning_end_time,morning_start_time 
							from schedule_templates where id in($schTmpId)
							and morning_start_time<='$times_from'
							and morning_end_time > '$times_from' 
							order by morning_end_time desc";
					$tmpRes = ManageData::getQryRes($qry);
					$singleTmpId = '';
					$fldLunchStTm = '';
					$fldLunchEdTm = '';
					$morning_start_time = '';
					$morning_end_time = '';
					for($tp=0;$tp<count($tmpRes);$tp++){
						$singleTmpId = $tmpRes[$tp]['id'];
						$fldLunchStTm = explode(':',$tmpRes[$tp]['fldLunchStTm']);
						$fldLunchEdTm = explode(':',$tmpRes[$tp]['fldLunchEdTm']);
						$morning_start_time = explode(':',$tmpRes[$tp]['morning_start_time']);
						$morning_end_time = explode(':',$tmpRes[$tp]['morning_end_time']);
					}
					$status = 'off';
					$slotColor = DEFAULT_OFFICE_CLOSED_COLOR;
					$qry = "select count(id) as rowCount 
							from schedule_templates where id in($singleTmpId)
							and morning_end_time <= '$times_from'";
					$rowRes = ManageData::getQryRes($qry);
					if($rowRes[0]['rowCount']>0){
						$status = 'off';
					}else{
						$qry = "select count(id) as rowCount 
								from schedule_templates where id in($singleTmpId)
								and morning_start_time <= '$times_from'";
						$rowRes = ManageData::getQryRes($qry);
						if($rowRes[0]['rowCount']>0){
							$status = 'on';
							$slotColor = $provider_color;
						}						
					}
					//---- Block Open Time Check -------
					$qry = "select b_desc,id,time_status from block_times where 
							'$times_from' = start_time and start_date = '$dt'
							and facility in ($facId) and provider in ($pid)";
					$blockQryRes = manageData::getQryRes($qry);
					$label = '';
					if($blockQryRes[0]['id']){
						$slotTempColor = "#000000";
						if($blockQryRes[0]['b_desc']){
							$label = $blockQryRes[0]['b_desc'];
							$status = 'block';
						}
						else{
							$label = 'block';
							$status = 'block';
						}
						$slotColor = $slotTempColor;
						if($singleTmpId == ''){
							$label = '';
							$status = 'off';
							$slotColor = DEFAULT_OFFICE_CLOSED_COLOR;
						}
						if($blockQryRes[0]['time_status'] == 'open'){
							$status = 'on';
							$label = '';
							$slotColor = $provider_color;
						}
					}
					
					if($label == ''){
						if($singleTmpId){
							$qry = "select template_label from schedule_label_tbl
									where '$times_from' between start_time and end_time
									and sch_template_id = '$singleTmpId'";
							$labelRes = manageData::getQryRes($qry);
							$label = $labelRes[0]['template_label'];
						}
						preg_match('/lunch/',$label,$labelcheck);
						if(count($labelcheck) == 0){
							if($startHrTime2 == $fldLunchStTm[0]){
								if($startTime >= $fldLunchStTm[1]){
									$slotColor = $provider_color;
									$label = 'lunch';
								}
								if($startTime >= $fldLunchEdTm[1]){
									$label = '';
								}
							}
						}
					}
					//--- End Get Specific Template ---------				
					
					$xmlData .= '
						<slot timing="'.$startHrTime2.':'.$startTime.'-'.$endHrTime.':'.$endTime.'" status="'.$status.'" color="'.$slotColor.'" label="'.$label.'" tmpId="'.$singleTmpId.'"></slot>
					';
					$startTime += $timeSlotRes;
					$t += $timeSlotRes -1;
				}
				//--- End Time loop -------
				$xmlData .= '
							</slots>
						</provider>					
				';
			}
			//--- End Provider loop -------
			$xmlData .= '
					</providers>
				</facility>
			';
		}
		//--- End Facility loop -------
		//---- Xml File Footer Content --------
		$xmlData .= '
					</facilities>
				</dt>
			</schedule>
		';	
        //echo $dirPath.'load_xml/'.$dt.'_schedulerData.xml';
//        echo $dirPath.'load_xml/'.$dt."_".$providerToLoad.'_schedulerData.xml';
//        die;	
	if($providerToLoad==""){
			if(file_exists( $dirPath.'load_xml/'.$dt.'_schedulerData.xml')){
				unlink( $dirPath.'load_xml/'.$dt.'_schedulerData.xml');
			}		
			$fp = fopen( $dirPath.'load_xml/'.$dt.'_schedulerData.xml','w');
			$data = fputs($fp,$xmlData);
			fclose($fp);
		}
		if($providerToLoad!=""){
				if(file_exists( $dirPath.'load_xml/'.$dt."_".$providerToLoad.'_schedulerData.xml')){
					unlink( $dirPath.'load_xml/'.$dt."_".$providerToLoad.'_schedulerData.xml');
				}		
				$fp = fopen( $dirPath.'load_xml/'.$dt."_".$providerToLoad.'_schedulerData.xml','w');
				$data = fputs($fp,$xmlData);
				fclose($fp);
			}
	}
}

function getOfficeTimings12($dt,$sel_week_day, $strFacIds){
	$dt_exs=explode("-",$dt);
	$mth=$dt_exs[1];
	$yrs=$dt_exs[0];
	$dys=$dt_exs[2];
	$months=date("n",mktime(0,0,0,$mth,$dys,$yrs));		
	$task_date=date("Y-m-d",mktime(0,0,0,$mth,$dys,$yrs));
	
	//$pro = " and provider in ($sel_pro)";
	$start_day = date('N',mktime(0,0,0,$mth,1,$yrs));
	$week = ceil($dys/7);
	
	$dt_ex = explode("_",$sel_week_day);	
	$weekday = $dt_ex[1] == 0 ? 7 : $dt_ex[1];
	//------ Query To get The Exception Deleted Data ----------
	$qry = "select * from provider_schedule_tmp where today_date = '$dt'
			$pro and del_status = '1' and delete_row = 'no' and facility in ($strFacIds)";
	$qryId = imw_query($qry);
	if(imw_num_rows($qryId)>0){
		while($res = imw_fetch_array($qryId)){
			$provider = $res['provider'];
			$facility = $res['facility'];
			$sch_tmp_id = $res['sch_tmp_id'];
			$week1 = $res['week1'];
			$week2 = $res['week2'];
			$week3 = $res['week3'];
			$week4 = $res['week4'];
			$week5 = $res['week5'];
			$week6 = $res['week6'];
			$today_date = $res['today_date'];
			$query = "select id from provider_schedule_tmp where provider = '$provider'
					and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
					and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
					and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
					and today_date != $today_date";
			//print '<br>';
			$queryRes = imw_query($query);
			if(imw_num_rows($queryRes)>0){
				while($res1 = imw_fetch_array($queryRes)){
					$DeletedId[] = $res1['id'];	
				}
			}
		}
	}	
		
	//------ Query To get The All Deleted Data ----------
	
	$qry = "select * from provider_schedule_tmp where today_date <= '$dt'
			$pro and del_status = '1' and delete_row = 'all' and facility in ($strFacIds)";
	$qryId = imw_query($qry);
	if(imw_num_rows($qryId)>0){
		while($res = imw_fetch_array($qryId)){
			$id = $res['id'];
			$DeletedId[] = $id;
			$provider = $res['provider'];
			$facility = $res['facility'];
			$sch_tmp_id = $res['sch_tmp_id'];
			$week1 = $res['week1'];
			$week2 = $res['week2'];
			$week3 = $res['week3'];
			$week4 = $res['week4'];
			$week5 = $res['week5'];
			$week6 = $res['week6'];
			$today_date = $res['today_date'];
			$query = "select id,provider from provider_schedule_tmp where provider = '$provider'
					and facility = $facility and sch_tmp_id = '$sch_tmp_id' 
					and week1 = '$week1' and week2 = '$week2' and week3 = '$week3'
					and week4 = '$week4' and week5 = '$week5' and week6 = '$week6'
					and id < '$id'";
			$queryRes = imw_query($query);
			if(imw_num_rows($queryRes)>0){
				while($res1 = imw_fetch_array($queryRes)){
					$DeletedId[] = $res1['id'];	
				}
			}
		}
	}		
	if(isset($DeletedId)){
		$deletedId = implode(',',$DeletedId);
	}
	//------- Query For Exceptional Data -----------
//	print $deletedId;
	if($deletedId){
		$qry = "select id,facility,sch_tmp_id,provider from provider_schedule_tmp where today_date = '$dt'  
			$pro and week$week = '$weekday' and del_status = 0 and status = 'no'
			and delete_row = '' and id not in ($deletedId)  and facility in ($strFacIds) group by provider,sch_tmp_id,facility";			
	}else{
		$qry = "select id,facility,sch_tmp_id,provider from provider_schedule_tmp where today_date = '$dt'  
			$pro and week$week = '$weekday' and del_status = 0 and status = 'no'
			and delete_row = '' and facility in ($strFacIds) group by provider,sch_tmp_id,facility";			
	}
	
	$finalRes1 = ManageData::getQryRes($qry);
	$dataId = array();
	for($aa=0;$aa< count($finalRes2);$aa++){
		$dataId[] = $finalRes2[$aa]['id'];
	}
	if($deletedId){
		if($Dataid){
			$Dataid .= ','.$deletedId;
		}
		else{
			$Dataid = $deletedId;
		}
	}
	//------- Query For All Data -----------
	if($Dataid){
		$qry = "select id,sch_tmp_id,provider,facility from provider_schedule_tmp where today_date <= '$dt'
				$pro and week$week = '$weekday' and del_status = 0
				and status = 'yes' and id not in($Dataid) and facility in ($strFacIds) order by provider";
	}
	else{
		$qry = "select id,sch_tmp_id,provider,facility from provider_schedule_tmp where today_date <= '$dt'  
				$pro and week$week = '$weekday' and del_status = 0 
				and delete_row = '' and facility in ($strFacIds) order by provider";
	}
	
	$finalRes2 = ManageData::getQryRes($qry);
	$finalRes = array_merge((array)$finalRes1,(array)$finalRes2);
	
	//getting providers per facility	
	$arrProviderReturn = array();
	$arrUsedFacilities = array();
	if(is_array($finalRes) && count($finalRes) > 0){
		foreach ($finalRes as $arrThisElement){
			if(in_array($arrThisElement['facility'],$arrUsedFacilities)){
				$arrTemp = explode("|",$arrProviderReturn[$arrThisElement['facility']]);
				if(in_array($arrThisElement['provider'],$arrTemp) === false){
					$arrProviderReturn[$arrThisElement['facility']] = $arrProviderReturn[$arrThisElement['facility']]."|".$arrThisElement['provider'];
				}
			}else{
				$arrProviderReturn[$arrThisElement['facility']] = $arrThisElement['provider'];
				array_push($arrUsedFacilities,$arrThisElement['facility']);
			}
		}
	}

	//getting provider details per provider
	$arrProviderDetailReturn = array();
	$arrUsedProviders = array();
	if(is_array($finalRes) && count($finalRes) > 0){
		foreach ($finalRes as $arrThisElement){
			if(in_array($arrThisElement['provider'],$arrUsedProviders)){
				$arrTemp = explode("|",$arrProviderDetailReturn[$arrThisElement['provider']]);
				if(in_array($arrThisElement['provider'],$arrTemp) === false){
					$arrProviderDetailReturn[$arrThisElement['provider']] = $arrProviderDetailReturn[$arrThisElement['provider']]."|".$arrThisElement['sch_tmp_id'];
				}
			}else{
				$arrProviderDetailReturn[$arrThisElement['provider']] = $arrThisElement['sch_tmp_id'];
				array_push($arrUsedProviders,$arrThisElement['provider']);
			}
		}
	}
	
	//getting provider more details
	$arrProviderMoreDetail = array();
	$arrUsedMProviders = array();
	if(is_array($finalRes) && count($finalRes) > 0){
		foreach ($finalRes as $arrThisElement){
			if(in_array($arrThisElement['provider'],$arrUsedMProviders) === false){
				$strQry = "SELECT fname, lname, provider_color, user_type FROM users WHERE id = '".$arrThisElement['provider']."'";
				$resProv = imw_query($strQry);
				$arrProv = imw_fetch_array($resProv,imw_ASSOC);
//				print_r($arrProv);
				$arrProviderMoreDetail[$arrThisElement['provider']] = $arrProv;
				array_push($arrUsedMProviders,$arrThisElement['provider']);
			}
		}
	}
	
	//getting office timings
	$arrOfficeTimings = array();
	$tmp_id = array();
	for($i=0;$i<count($finalRes);$i++){
		if(strstr($finalRes[$i]['facility'],$strFacIds)){
			$tmp_id[] = $finalRes[$i]['sch_tmp_id'];
		}
	}
	if($tmp_id){
		$qry = "select DATE_ADD(CONCAT('$dt ',min(morning_start_time)), INTERVAL -1 HOUR) as start_time from schedule_templates
				where id in (".implode(",",$tmp_id).")";
		$time_res = ManageData::getQryRes($qry);
		$qry = "select DATE_ADD(CONCAT('$dt ',max(morning_end_time)), INTERVAL 1 HOUR) as end_time from schedule_templates
				where id in (".implode(",",$tmp_id).")";
		$time_res2 = ManageData::getQryRes($qry);
	}
	$arrOfficeTimings[] = $time_res[0]['start_time'];
	$arrOfficeTimings[] = $time_res2[0]['end_time'];
	
	
//	$delQry = "DELETE FROM scheduler_cache WHERE scDate = '$dt'";
//	imw_query($delQry);
	
	
	
	$arrReturn = array($arrProviderReturn, $arrProviderDetailReturn, $arrOfficeTimings, $arrProviderMoreDetail);
	
//	print "<pre>";
//	print_r($arrReturn);
	return $arrReturn;
}

function saveArrayInDatabase($arrTemp, $strElemSep = "~", $strKeyValSep = "^", $strMode = 1){
	$strReturn = "";
	if(is_array($arrTemp) && count($arrTemp) > 0){
		foreach ($arrTemp as $k => $v){
			if($strMode == 0){
				$strReturn .= $k.$strKeyValSep."[".urlencode(saveArrayInDatabase($v))."]".$strElemSep;
			}else{
				$strReturn .= $k.$strKeyValSep.$v.$strElemSep;
			}
		}
		$strReturn = substr($strReturn,0,"-".strlen($strElemSep));
	}
	return $strReturn;
}

function fetchArrayFromDatabase($strValue, $strElemSep = "~", $strKeyValSep = "^", $strMode = 1){
	
	$arrReturn = array();
	$arrMainReturn = array();
	$arrTempElem = explode($strElemSep, $strValue);
	if($arrTempElem){
		foreach ($arrTempElem as $k=>$strSubValue){
			$arrTempKeyVal = explode($strKeyValSep,$strSubValue);
			
			if(substr($arrTempKeyVal[1],0,1) == "["){
				$strTempVal =  str_replace(array("[","]"),"",urldecode($arrTempKeyVal[1]));

				if(trim($strTempVal) != ""){
					$arrTTemp = fetchArrayFromDatabase($strTempVal);
					$arrReturn[$arrTempKeyVal[0]] = $arrTTemp;
				}else{
					$arrReturn[$arrTempKeyVal[0]] = "";
				}
			}else{
				$arrReturn[$arrTempKeyVal[0]] = $arrTempKeyVal[1];			
			}
				
		}
	}
	return $arrReturn;
}

	function getSchTmpData($selDate,$pro='',$facId=''){
		$dtArr = explode('-',$selDate);
		$weekDay = ceil($dtArr[2]/7);
		$day = date('w',mktime(0,0,0,$dtArr[1],$dtArr[2],$dtArr[0]));
		if($day == 0){
			$day = 7;
		}
		$res = array();
		if($pro){
			$pro12 = " and provider_schedule_tmp.provider = '$pro'";
		}
		if($facId){
			$fac = " and provider_schedule_tmp.facility in($facId)";
		}		
		$qry = "select provider_schedule_tmp.id,provider_schedule_tmp.del_status,
				provider_schedule_tmp.delete_row,provider_schedule_tmp.status,
				provider_schedule_tmp.provider,provider_schedule_tmp.facility,
				provider_schedule_tmp.today_date,provider_schedule_tmp.sch_tmp_id
				from provider_schedule_tmp join users on provider_schedule_tmp.provider = users.id				
				where provider_schedule_tmp.today_date <= '$selDate' $pro12 $fac		
				and provider_schedule_tmp.week$weekDay = '$day' 
				order by provider_schedule_tmp.provider,provider_schedule_tmp.facility, 
				provider_schedule_tmp.del_status";
		$res = ManageData::getQryRes($qry);	
		$count = count($res);
		for($i=0;$i<$count;$i++){
			$del_status = $res[$i]['del_status'];
			$delete_row = $res[$i]['delete_row'];
			$status = $res[$i]['status'];
			$provider = $res[$i]['provider'];
			$facility = $res[$i]['facility'];
			$sch_tmp_id = $res[$i]['sch_tmp_id'];
			$today_date = $res[$i]['today_date'];
			if($del_status == 1){
				$del_arr[] = $res[$i];
				unset($res[$i]);
			}
		}
		
		//--- Display Check for only Future Month --------
		if(count($res)>0){
			$res = array_values($res);
		}
		if(count($del_arr)>0){
			$del_arr = array_values($del_arr);
		}
		$del_cnt = count($del_arr);
		for($i=0;$i<$del_cnt;$i++){
			$provider = $del_arr[$i]['provider'];
			$facility = $del_arr[$i]['facility'];
			$sch_tmp_id = $del_arr[$i]['sch_tmp_id'];
			$today_date = $del_arr[$i]['today_date'];
			$delete_row = $del_arr[$i]['delete_row'];
			$deletedCount = count($res);
			$res = array_values($res);
			for($r=0;$r<$deletedCount;$r++){
				$provider1 = $res[$r]['provider'];
				$facility1 = $res[$r]['facility'];
				$sch_tmp_id1 = $res[$r]['sch_tmp_id'];
				$today_date1 = $res[$r]['today_date'];
				if(strtolower($delete_row) == 'all'){
					if($provider == $provider1 && $facility == $facility1 && $sch_tmp_id == $sch_tmp_id1 && strtotime($today_date) >= strtotime($today_date1) ){
						$del_arr[] = $res[$r];
						unset($res[$r]);
					}
				}
				if(strtolower($delete_row) == 'no'){
					if($provider == $provider1 && $facility == $facility1 && $sch_tmp_id == $sch_tmp_id1 && strtotime($today_date) == strtotime($selDate) ){
						$del_arr[] = $res[$r];
						unset($res[$r]);
					}
				}
			}
		}
		//--- Display Check for only Current Month --------
		if(count($res)>0){
			$res = array_values($res);
		}
		if(count($del_arr)>0){
			$del_arr = array_values($del_arr);
		}
		$count = count($res);
		for($i=0;$i<$count;$i++){
			$today_date = $res[$i]['today_date'];
			$status = $res[$i]['status'];
			if(strtotime($today_date) < strtotime($selDate) && strtolower($status) == 'no'){
				$del_arr[] = $res[$i];
				unset($res[$i]);
			}
		}
		if(count($res)>0){
			$res = array_values($res);
			$schId = array();
			for($i=0;$i<count($res);$i++){
				$schId[] = $res[$i]['id'];
			}
			$schIdStr = join(',',$schId);
			$qry = "select id,facility , provider,sch_tmp_id,today_date
					from provider_schedule_tmp where id in ($schIdStr)
					order by provider,facility ,sch_tmp_id";
			$return = manageData::getQryRes($qry);
			$cnt = count($return);
			for($i=0;$i<$cnt;$i++){
				$provider = $return[$i]['provider'];
				$provider1 = $return[$i+1]['provider'];
				$facility = $return[$i]['facility'];
				$facility1 = $return[$i+1]['facility'];
				$sch_tmp_id = $return[$i]['sch_tmp_id'];
				$sch_tmp_id1 = $return[$i+1]['sch_tmp_id'];
				if($sch_tmp_id == $sch_tmp_id1 && $facility == $facility1 && $provider == $provider1){
					unset($return[$i]);
				}
			}
		}
		$return = array_values($return);
		return $return;		
	}
	
	function adminSchTmpData($selDate,$pro=''){
		$return = array();
		if($pro){
			$res = getSchTmpData($selDate,$pro);
		}
		if(count($res)>0){
			$res = array_values($res);
			$schId = array();
			for($i=0;$i<count($res);$i++){
				$schId[] = $res[$i]['id'];
			}
			$sch_id = join(',',$schId);
			if($sch_id){
				$qry = "select provider_schedule_tmp.facility,provider_schedule_tmp.sch_tmp_id,
						provider_schedule_tmp.provider from provider_schedule_tmp
						join users on provider_schedule_tmp.provider = users.id
						where provider_schedule_tmp.id in ($sch_id)
						order by users.lname,users.fname";
				$qryId = imw_query($qry);
				$res = fetchArray($qryId);
			}			
			for($i=0;$i<count($res);$i++){
				//-- Facility Details ----------
				$qry = "select name from facility where id = '$facility'";
				$facRes = ManageData::getQryRes($qry);
				$facName = trim(strtolower($facRes[0]['name']));
				if($facName == 'whiting'){
					$facName = 'wh';
				}
				else if($facName == 'brick'){
					$facName = 'br';
				}
				else if($facName == 'surgery center'){
					$facName = 'SC';
				}
				else if($facName == 'toms river'){
					$facName = 'tr';
				}
				$return[] = '&nbsp;'.strtoupper($facName).'&nbsp;';
			}
		}
		return $return;
	}
/* Code  By Ram To Make Common Drop doen For Procedures*/

/*function getCommonProcedureOptions($sa_doctor_id="",$procedureid=""){
	$resDRProcedure=array();
	$strOPtions="";
	$res = sqlStatement("select id,proc,acronym from slot_procedures where proc!='' and (procedureId=id || procedureId=0) and  active_status='yes' group by proc  order by proc");				
	if($sa_doctor_id<>""){																															
		$drProceduers="select id,proc,acronym from slot_procedures where proc!=''  and doctor_id='$sa_doctor_id' and  active_status='yes'  order by proc";			
		$resDRProcedure =sqlFetchArray(sqlStatement($drProceduers));
	}
	$sel = "";
	$showDefaultProc = "";
	$blSelected = false;
    while($row = sqlFetchArray($res)){
		extract($row);
		$showProc = $proc;
		if(DEFAULT_PRODUCT == "imwemr"){
			$showProc = $acronym;
		}
		if($proc<>"" && !in_array($proc,$resDRProcedure)){
			$sel = $procedureid == $id ? 'selected' : '';
			$strOPtions.="<option $sel value=\"$id\">$showProc</option>\n";
		}else{
			foreach($resDRProcedure as $variable => $value){
				if($value==$proc){
					$sel = $procedureid == $resDRProcedure["id"] ? 'selected' : '';
					$strOPtions.="<option $sel value=\"".$resDRProcedure["id"]."\">$showProc</option>";	
				}
			}
		}
		if($sel == "selected"){
			$blSelected = true;
		}
	if($blSelected == false && $sa_doctor_id!="" && $procedureid==$id){
			$strOPtionsAppend="<option selected value=\"".$id."\">".$showProc."</option>";	
		}
	}
	
	print $strOPtions.$strOPtionsAppend;
}*/
/* End Code to make common drop down for procedures*/
/* Code  By Ram To Make Common Drop down For Procedures */

function getCommonProcedureOptions($sa_doctor_id="",$procedureid=""){
	$resDRProcedure=array();
	$strOPtions="";
	$blSelected = false;
	$res = sqlStatement("select id,proc,acronym from slot_procedures where proc!='' and (procedureId=id || procedureId=0) and  active_status='yes' group by proc  order by proc");				
	//loop for default
	$arrDefaultName = array();
	$arrDefaultId = array();
    while($row = sqlFetchArray($res)){
		$arrDefaultName[] = $row['proc']."~~".$row['acronym'];
		$arrDefaultId[] = $row['id'];
	}
	$arrDoctorName = array();
	$arrDoctorId = array();
	if($sa_doctor_id<>""){																															
		$drProceduers="select id,proc,acronym from slot_procedures where proc!=''  and doctor_id='$sa_doctor_id' and  active_status='yes'  order by proc";			
		$res = sqlStatement($drProceduers);
		//loop for default
		while($row = sqlFetchArray($res)){
			$arrDoctorName[] = $row['proc']."~~".$row['acronym'];
			$arrDoctorId[] = $row['id'];
		}
	}
	
	if(count($arrDoctorId) > 0){
		foreach($arrDefaultName as $variable => $value){
			if(in_array($value,$arrDoctorName)){	
				unset($arrDefaultName[$variable]);
				unset($arrDefaultId[$variable]);
			}
		}
		$arrResultName = array_merge($arrDefaultName, $arrDoctorName);
		$arrResultId = array_merge($arrDefaultId, $arrDoctorId);
		$arrResult = array_combine($arrResultId, $arrResultName);
	}else{
		$arrResult = array_combine($arrDefaultId, $arrDefaultName);
	}
		asort($arrResult);
		//print_r($arrResult);
	if(count($arrResult) > 0){

		foreach($arrResult as $variable => $value){
			$sel="";
			if($variable==$procedureid)
				{ 
					$sel='selected'; 
					$blSelected = true;
				}
			$vall=explode("~~",$value);
			$showProc =$vall[0];// $proc;
			if(DEFAULT_PRODUCT == "imwemr"){
				$showProc = $vall[1];
			}
			$strOPtions.="<option $sel value=\"$variable\">".$showProc."</option>";
		}

		if($blSelected == false && $sa_doctor_id!="" && $procedureid!=""){
			$resSINGLE = sqlStatement("select id,proc,acronym from slot_procedures where id=$procedureid");// To show Procedure Presvously selected and Not Active Now				
			$rowSingle = sqlFetchArray($resSINGLE);	
			$showProc=$rowSingle["proc"];
		if(DEFAULT_PRODUCT == "imwemr"){
				$showProc = $rowSingle["acronym"];
			}
			$strOPtionsAppend="<option  value=\"".$procedureid."\" selected>".$showProc."</option>";	
		}	
	}else{
		$strOPtions="<option>HI</option>";
	}
	print $strOPtions.$strOPtionsAppend;
}	

function default_proc_to_doctor_proc($default_procedure_id, $doctor_id){
	$objDB = $GLOBALS['adodb']['db'];
	
	$proc_id_qry = "SELECT sp1.id, sp2.times, sp1.proc_mess   
						FROM slot_procedures sp1 
						LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id 
						WHERE sp1.times = '' 
						AND sp1.proc != '' 
						AND sp1.doctor_id = '".$doctor_id."' 
						AND sp1.procedureId = '".$default_procedure_id."'";
	$res_proc_id = $objDB->Execute($proc_id_qry);
	$arr_proc_id = array();
	if($res_proc_id->_numOfRows > 0){
		$arr_proc_id = $res_proc_id->GetArray();
		if($arr_proc_id[0]["times"] != ""){
			return $arr_proc_id[0]["id"]."~".$arr_proc_id[0]["times"]."~".$arr_proc_id[0]["proc_mess"];
		}else{
			$proc_id_qry3 = "SELECT sp1.id, sp2.times, sp1.proc_mess   
						FROM slot_procedures sp1 
						LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id 
						WHERE sp1.times = '' 
						AND sp1.proc != '' 
						AND sp1.doctor_id = '0' 
						AND sp1.id = '".$default_procedure_id."'";
			$res_proc_id3 = $objDB->Execute($proc_id_qry3);
			$arr_proc_id3 = array();
			if($res_proc_id3->_numOfRows > 0){
				$arr_proc_id3 = $res_proc_id3->GetArray();
				//echo "here";
				return $arr_proc_id[0]["id"]."~".$arr_proc_id3[0]["times"]."~".$arr_proc_id[0]["proc_mess"];
			}else{
				//echo "there";
				return $arr_proc_id[0]["id"]."~".$arr_proc_id[0]["times"]."~".$arr_proc_id[0]["proc_mess"];
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
		$res_proc_id2 = $objDB->Execute($proc_id_qry2);
		$arr_proc_id2 = array();
		if($res_proc_id2->_numOfRows > 0){
			$arr_proc_id2 = $res_proc_id2->GetArray();
			return $arr_proc_id2[0]["id"]."~".$arr_proc_id2[0]["times"]."~".$arr_proc_id2[0]["proc_mess"];
		}else{
			return false;
		}
	}
}

function doctor_proc_to_default_proc($appt_procedure_id){
	$objDB = $GLOBALS['adodb']['db'];
	$proc_id_qry = "SELECT procedureId FROM slot_procedures WHERE id = '".$appt_procedure_id."'";
	$res_proc_id = $objDB->Execute($proc_id_qry);
	$arr_proc_id = array();
	if($res_proc_id->_numOfRows > 0){
		$arr_proc_id = $res_proc_id->GetArray();
	}
	if($arr_proc_id[0]["procedureId"] == 0){
		$default_procedure_id = $appt_procedure_id;
	}else{
		$default_procedure_id = $arr_proc_id[0]["procedureId"];
	}
	return $default_procedure_id;
}


/* End Code to make common drop down for procedures*/
function getProcedure_name($id){
	$qrt=imw_query("SELECT proc FROM `slot_procedures` WHERE id=$id");
	list($proc)=imw_fetch_array($qrt);
	return $proc;
}	
function getProvider_name($id, $mode = ""){
	$qrt=imw_query("SELECT fname,lname FROM `users` WHERE id=$id");
	list($fname,$lname)=imw_fetch_array($qrt);
	if($mode == "tiny"){
		return strtoupper(substr($fname,0,1)).strtoupper(substr($lname,0,1));
	}else{
		return $fname." ".$lname;
	}
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
function getCheckout_time($schid){
	$time_t="";
	$qrt=imw_query("SELECT previous_status.*,date_format( previous_status.status_date, '%m-%d-%y' ) AS status_dateMDY from previous_status WHERE sch_id='$schid' and status=11 ORDER BY previous_status.id DESC LIMIT 1");
	$res=imw_fetch_array($qrt);
	if ($res['status_date'] && $res['status_date'] != "00-00-0000"){
		$appoint_time_to=$res['status_time'];
		$time_hourt=substr($appoint_time_to,0,2);
		$time_minutet=substr($appoint_time_to,3,2);
//		$time_t=$res['status_dateMDY']." ".date("h:i A", mktime($time_hourt,$time_minutet));
		$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
	}
	return $time_t;
}	
function getCheckin_time($schid){
	$time_t="";
	$qrt=imw_query("SELECT previous_status.*,date_format( previous_status.status_date, '%m-%d-%y' ) AS status_dateMDY from previous_status WHERE sch_id='$schid' and status=13 ORDER BY previous_status.id DESC LIMIT 1");
	$res=imw_fetch_array($qrt);
	if ($res['status_date'] && $res['status_date'] != "00-00-0000") {
		$appoint_time_to=$res['status_time'];
		$time_hourt=substr($appoint_time_to,0,2);
		$time_minutet=substr($appoint_time_to,3,2);
//		$time_t=$res['status_dateMDY']." ".date("h:i A", mktime($time_hourt,$time_minutet));
		$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
		
	}
	return $time_t;
}

function GetNewAppointmentInfo($pid,$schId){
	if($pid<>""){	
		$schedule_query="SELECT * FROM schedule_appointments where sa_patient_id='$pid' and id='$schId' and sa_patient_app_status_id IN(1,3,5,6,11,18,17,13,'',201,202) order by sa_app_start_date desc";	
		$schedule_result=imw_query($schedule_query) or die(imw_error()); 
		$schedule_numrows =imw_num_rows($schedule_result);
		if($schedule_numrows>0){
		?>	
	
<table width="300px" border="0" align="left" cellpadding="3" cellspacing="2" vspace="0" bordercolor="#CCCCCC">
	
	<TR bgcolor="#C0D7E2" height="20">
		<td width="15%"     align="center" nowrap="nowrap"  class="text_9b">Date</td>
		<td  width="15%"    align="center" nowrap="nowrap"  class="text_9b">Time</td>
		<td  width="5%"    align="left"  class="text_9b">Phy.</td>
		<td  width="5%"    align="left"  class="text_9b">Loc</td>
		<td  width="5%"  align="left"  class="text_9b">Pro.</td>
		<td  width="43%"     align="left"  class="text_9b">Notes</td>
		
	</TR>	
	<?php 
		$i=0;
		while($schedule_row=imw_fetch_array($schedule_result)){
			//--- Var for reschedule patient ---------
			$sch_id = $schedule_row["id"];
			$sa_patient_id=$schedule_row["sa_patient_id"];
			$sa_doctor_id=$schedule_row["sa_doctor_id"];
			list($y,$m,$d)=explode('-',$schedule_row["sa_app_start_date"]);
			$sa_app_start_date = $m.'-'.$d.'-'.$y;
			$current_caseid=$schedule_row['case_type_id'];
			$sa_facility_id=$schedule_row["sa_facility_id"];
			$procedureid=$schedule_row["procedureid"];
			$pat_name=$schedule_row['sa_patient_name'];
		
			  $proc = " select * from slot_procedures where id='$procedureid' ";												
				$proc_res = imw_query($proc);
				$proc_row=imw_fetch_array($proc_res);
				//$max_allowed=$rs_cf1['max_allowed']; 
			
				$proc_acronym=$proc_row['acronym'];
				
			 $sched_id=$schedule_row["id"];
			$sa_patient_app_status_id=$schedule_row["sa_patient_app_status_id"];
			$comments=$schedule_row["sa_comments"];
			$sa_app_start_dates=$schedule_row["sa_app_start_date"];
			list($year_appoint, $month_appoint, $day_appoint) = explode('-',$sa_app_start_dates);
			if($year_appoint<>"") {
				$create_date_appoint = $month_appoint."-".$day_appoint."-".substr($year_appoint,2,2);  //GETTING APPOINTMENT DATE OF PATIENT
			}
			$sa_app_starttime=$schedule_row["sa_app_starttime"];
			
			list($start_hours, $start_minutes, $start_sec) = explode(':',$sa_app_starttime);
				$app_time_start=@mktime($start_hours, $start_minutes, $start_sec, $month_appoint,$day_appoint,$year_appoint);
				$app_start_time=@date("g:i",$app_time_start);
				$app_start_sess=substr(@date("A",$app_time_start),0,1);
			$sa_app_endtime=$schedule_row["sa_app_endtime"];
			list($end_hours, $end_minutes, $end_sec) = explode(':',$sa_app_endtime);
				$app_time_end=@mktime($end_hours, $end_minutes, $end_sec, $month_appoint,$day_appoint,$year_appoint);
				$app_end_time=@date("g:i",$app_time_end);
				$app_end_sess=substr(@date("A",$app_time_end),0,1);					
				$app_time=$app_start_time.$app_start_sess."-".$app_end_time.$app_end_sess;  //GETTING APPOINTTMENT TIME OF PATIENT
			
				//*************************************** Location and Physician************************************/
				 $user_query = "select fname,lname from users where /*user_type IN(1)and*/ id ='$sa_doctor_id'";												
				$user_result = imw_query($user_query);
				if(imw_num_rows($user_result)>0){
					$user_row=imw_fetch_array($user_result); 
					$physician_lname=$user_row["lname"];
					$physician_lname_intial=$physician_lname{0};
					$physician_name=substr($user_row["fname"],0,1).substr($physician_lname_intial,0,1);  //GETTING PHYSICIAN NAME FOR APPOINT OF PATIENT
				}
				$location_query = "select city,name from facility where id=$sa_facility_id";				
				$location_result = imw_query($location_query);
				$location_row = imw_fetch_array($location_result);							
				//$fac_name=$location_row ["name"];
				$fac_name=$location_row ["name"];
				$fac_name_explode=explode(" ",$fac_name);
				
				$fac_name_firststring=substr($fac_name_explode{0},0,1);
				$fac_name_secondstring=substr($fac_name_explode{1},0,1);
				if($fac_name_secondstring==""){
					$fac_name_firststring=substr($fac_name_explode{0},0,2);
				}
				if($fac_name_secondstring=="") {  //if city has only single name like 'Colt' THEN GET 'CO'
					$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
				} else {  //if city has not single name like 'Colt Neck' then get 'CN'
					$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
				}	
				//********************************************End here ****************************************************/
					
	
		$id_name='io'.$i;
															

		if($sa_patient_app_status_id=='18'){
			$onclick="";
			$sty1="style=color:red;background-color:#CCCCCC;font-weight:bold";
			$sty="";
		}else{
			$sty1="";
			//$onclick="onclick=go_to_date('$sa_app_start_dates','$sa_app_starttime','$sa_facility_id');drag_name('$sched_id','$pid')";
			$sty="style=cursor:hand";			
		}												
		echo "<input type=\"hidden\" name=\"sel_proc_id\" id=\"sel_proc_id\" value=\"$procedureid\" \>";
		?>				
		<tr  valign="top"  id=<?php echo $id_name;?>>
		  <td align="left" nowrap="nowrap" <?=$sty1;?> class="text_9" <?=$sty;?> ><?php echo $create_date_appoint;?></td>
		  <td align="left"  nowrap="nowrap" <?=$sty1;?> class="text_9"><?php echo  $app_time;?></td>
	
		  <td  align="left" <?=$sty1;?> class="text_9"><?php echo $physician_name;?></td>
		  <td align="left" <?=$sty1;?> class="text_9"> <?php echo $fac_namefirst2letters;?></td>
		  <td align="left" <?=$sty1;?> class="text_9"><?php echo $proc_acronym;?></td>	
		  <td align="left" <?=$sty1;?> class="text_9" nowrap="nowrap"><?php echo $comments; ?></td>
	  </tr>
		<?php
			$i++;	
			}
print("</table>");
	}else{?>
	<table width="100%" align="center" cellpadding="0" cellspacing="0">
	<tr>
		
		<td  align="left" <?=$sty1;?> class="text_9">No Appointment Found.</td>
		
	</tr>
	</table>
	<?php 
		}
	}?>

<?php 
}

function getOperatorInitialByUsername($strUsername){
	if(trim($strUsername) != ""){
		$strQry = "SELECT fname, lname FROM users WHERE username = '".$strUsername."'";
		$rsData = imw_query($strQry);
		$arrData = imw_fetch_array($rsData,imw_ASSOC);
		return strtoupper(substr(trim($arrData['fname']),0,1)).strtoupper(substr(trim($arrData['lname']),0,1));
	}else{
		return "";
	}
}

//function to display All details for One Appointment//
function GetOneSchdeuleDetails($pat_id,$schid="",$showAll="", $mode = ""){
//	echo $mode;
	$counterP=0;
	$vquery_c = "	SELECT 
							ps.sch_id, ps.dateTime, ps.patient_id, ps.status_time, ps.status_date, 
							ps.status, ps.old_status, 
							ps.statusComments, ps.oldStatusComments, 
							DATE_FORMAT(ps.new_appt_date,'".getSqlDateFormat('','y','/')."') AS new_appt_date, DATE_FORMAT(ps.old_date,'".getSqlDateFormat('','y','/')."') AS old_appt_date, 
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
							AND ps.status IN ( 1, 3, 5, 6, 11, 18, 13,17,2,0,21,22,23,'', 201,202,203, 271) 
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
		
		if ($vrs['status_date'] && getNumber($vrs['status_date']) != "0") {									
			$tmp_date = $vrs['status_date'];
			//list($year, $month, $day) = split('-',$tmp_date);
			//$create_date = $month."/".$day."/".$year;
			$create_date=getDateFormat($tmp_date);
		}
		$appoint_time_to=$vrs['status_time'];
		$time_hourt=substr($appoint_time_to,0,2);
		$time_minutet=substr($appoint_time_to,3,2);
		$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
		$strOpChangedByName = getOperatorInitialByUsername($vrs['statusChangedBy']);
		
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

//function to display All details for One Appointment//
function GetOneSchdeuleDetails_ram($pat_id,$schid="",$showAll=""){
$counterP=0;
if($schid<>""){
	$schQury="schedule_appointments.id=$schid and";
}else{
	$schQury="";
}

$vquery_c = "SELECT previous_status. * ,schedule_appointments.sa_doctor_id,schedule_appointments.sa_comments, schedule_appointments.procedureid, date_format( schedule_appointments.sa_app_time, '%m-%d-%y' ) AS sa_app_time, slot_procedures.proc
FROM previous_status
JOIN schedule_appointments ON previous_status.sch_id = schedule_appointments.id
LEFT JOIN slot_procedures ON slot_procedures.id = schedule_appointments.procedureid
WHERE $schQury  previous_status.status
IN ( 1, 3, 5, 6, 11, 18, 13,17,2,0,'', 201,202 ) 
AND previous_status.patient_id ='".$pat_id."'
AND schedule_appointments.sa_patient_app_status_id
IN ( 1, 3, 5, 6, 11, 18, 13,17,2,0,'', 201,202 ) 
"; 
$vsql_c = imw_query($vquery_c);
echo("<div style=\"display:block;\">");
while($vrs=imw_fetch_array($vsql_c))
{
						
	$id=$vrs["sch_id"];
	$procedureid=$vrs["procedureid"];
	$doctor_id=$vrs["sa_doctor_id"];
	$prc_id=$procedureid;
	$sch_det=sch_data($vrs["sch_id"]);
	if ($vrs['status_date'] && $vrs['status_date'] != "00-00-0000") {									
			$tmp_date = $vrs['status_date'];
			list($year, $month, $day) = explode('-',$tmp_date);													
			$create_date = $month."/".$day."/".$year;			
			//echo $create_date."<br>";
		}
			$appoint_time_to=$vrs['status_time'];
			$time_hourt=substr($appoint_time_to,0,2);
			$time_minutet=substr($appoint_time_to,3,2);
			$time_t=date("h:i A", mktime($time_hourt,$time_minutet));
	
	$st_id=$vrs['status'];	
	
	if($st_id==201)
	{
		$stttt_us="Re-Scheduled";
	}elseif($st_id==18)	{
		$stttt_us="Cancelled";
	}else if($st_id=='')	{
		$stttt_us= "Deleted";
	}else if($st_id=='0')	{
		$stttt_us= "Restore";
	}else{					
		$vquery_st = "SELECT status_name FROM 
				`schedule_status` WHERE id = $st_id";
		$vsql_st = imw_query($vquery_st);	
		$rs_st = imw_fetch_array($vsql_st);
		$stttt_us=$rs_st['status_name'];
	}
?>
<Table cellpadding="0" cellspacing="0" width="100%">
			<tr bgcolor="#4684ab">
				<td  class="text_b_w" nowrap><strong>Appointment Detail</strong> </td>
				<td>&nbsp;</td>				
				<td width="572"  align="right"><span style="color:#FF0000;cursor:hand;" onClick="javascript:collapseMe('<?php echo($vrs["sch_id"]);?>')"><b>X</b></span></td>
			</tr>
			<tr bgcolor="#ffffff" >
				<td  align="left" colspan="3" class="text_9"><strong><?php echo($stttt_us);?></strong> on <?php echo($create_date);?> at <?php echo($time_t);?> <strong><?php echo($sch_det["2"]);?></strong></td>
			</tr>
<?php if($st_id==0 || $st_id==201){?>
			<tr bgcolor="#ffffff" class="text_9">
				<td width="144"  align="center"><?php GetNewAppointmentInfo($pat_id,$vrs["sch_id"]);?></td>
				<td>&nbsp;</td>
				<td width="572"  align="center"><?php GetNewAppointmentInfo($pat_id,$vrs["sch_id"]);?></td>
			</tr>
<?php }?>
		<tr bgcolor="#ffffff" class="text_9">
		<td  align="left" colspan="3">
			<table>
				<tr class="text_9">
				<td  align="left" class="text_9"><strong>Checked-in</strong></td>
				<td class="text_9"><?php echo getCheckin_time($vrs["sch_id"]);?></td>
				<td  align="left" class="text_9"><strong><?php echo getProvider_name($doctor_id);?></strong></td>
			</tr>
		</table>
		</tr>
		<tr bgcolor="#ffffff" class="text_9">
		<td  align="left" colspan="3">
			<table>
				<tr class="text_9">
				<td  align="left" class="text_9"><strong>Checked-out</strong></td>
				<td class="text_9"><?php echo getCheckout_time($vrs["sch_id"]);?></td>
				<td  align="left" class="text_9"><strong><?php echo getProvider_name($doctor_id);?></strong></td>
			</tr>
		</table>
		</tr>
</table>
<?php 
	$counterP=1;
	}
if($counterP==0){
print("
	<table>
	<tr  width='100%' height='10' bgcolor='#4684ab' width='100%'>
	<td colspan='8' class='text_b_w'  align='center'>No Information  found for Past appointment status for the selected appointment.<span style='color:#FF0000;cursor:hand;' onClick='javascript:collapseMe($schid)'><b>X</b></span></td>
</tr>
</table>");

}
echo("</div>");
}	
//function to display All details for One Appointment//

//function to Get Faclity ID using Cookie Var Appointment//
	function getSelectedFacilityId(){
		$returnidval="";
		$vquery_cf = "select id,name from facility order by id asc";	
		$vsql_cf = imw_query($vquery_cf);
			$counter=0;	
			while($vrsf=imw_fetch_array($vsql_cf)){
			$tempVar=substr($_COOKIE["facility"],-1);
			if($tempVar==$counter){
				//print($_COOKIE["facility"]);
				//print($vrsf[1]);
				$returnidval=$vrsf[0];
			}
			$counter++;
		}
	return $returnidval;
	}


function month_scheduleprovider_Highlight($dt,$sel_week_day,$sel_pro,$faclitySel)
	{				
		$finalRes = getSchTmpData($dt,$sel_pro,$faclitySel);
		$tmpTableIdArr = array();
		for($i=0;$i < count($finalRes);$i++){
			$tmpTableIdArr[] = $finalRes[$i]['id'];
		}
		$tmpTableId = join(',',$tmpTableIdArr);
		$qry = "select provider_schedule_tmp.facility,provider_schedule_tmp.sch_tmp_id,
				provider_schedule_tmp.provider from provider_schedule_tmp
				join users on provider_schedule_tmp.provider = users.id
				where provider_schedule_tmp.id in ($tmpTableId)
				order by users.lname,users.fname";
		$finalRes = ManageData::getQryRes($qry);
		//--- Start to get the data for display ----------------				
		$ids_nm='nm'.$dys;
		$myheight="";
		$output=""; 
		$cnt = 0;
		$cnt1 = 3;
		list($y,$m,$d) = preg_split('/-/',$dt);
		$day = date('w',mktime(0,0,0,$m,$d,$y));
		if($day == 6 || $day == 0){
			$cnt1 = 1;
		}
		
		$providerSchData = array();
		for($a = 0;$a < count($finalRes);$a++){
			$facilityId = $finalRes[$a]['facility'];
			$sch_tmp_id = $finalRes[$a]['sch_tmp_id'];
			$provider  = $finalRes[$a]['provider'];
			$vquery_css = "select schedule_name,morning_start_time,morning_end_time from schedule_templates where id='$sch_tmp_id'";				
			$vsql_css = imw_query($vquery_css);
			$rt_rss=imw_fetch_array($vsql_css);							
			$sch_name=$rt_rss['schedule_name'];	
			//GETTING START TIME AND END TIME ie A & P INSTEAD OF AM AND PM BY SURINDER
			$vquery_css = "select schedule_name,morning_start_time,morning_end_time from schedule_templates where id='$sch_tmp_id'";				
			$vsql_css = imw_query($vquery_css);
			$rt_rss = imw_fetch_array($vsql_css);
			$morning_start_time = $rt_rss['morning_start_time'];		
			list($start_hours,$start_minutes,$start_sec) = explode(":",$morning_start_time);
			$sch_time_start = mktime($start_hours, $start_minutes, $start_sec);
			$sch_start_time = date("g:iA",$sch_time_start);
			$morning_end_time = $rt_rss['morning_end_time'];
			list($start_hours,$start_minutes,$start_sec) = explode(":",$morning_end_time);
			$sch_time_end = mktime($start_hours, $start_minutes, $start_sec);
			$sch_end_time = date("g:iA",$sch_time_end);
			//$sch_end_time_value = $sch_start_time." : ".$sch_end_time;
			$sch_time = $sch_start_time."-".$sch_end_time;
			//END TIME GETTING
			//CODE TO GET FIRST TWO LETTER OF CITY(LOCATION)
			$vquery_d = "select name,city from facility where id='$facilityId'";				
			$vsql_d = imw_query($vquery_d);
			$rs_d = imw_fetch_array($vsql_d);							
			$fac_name=$rs_d["name"];
			$fac_city=$rs_d["city"];
			$fac_name_explode=explode(" ",$fac_name);
			$fac_name_firststring=substr($fac_name_explode{0},0,1);
			$fac_name_secondstring=substr($fac_name_explode{1},0,1);
			if($fac_name_secondstring==""){
				$fac_name_firststring=substr($fac_name_explode{0},0,2);
			}
			if($fac_name_secondstring=="") {  //if city has only single name like 'Colt' THEN GET 'CO'
				$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
			} else {  //if city has not single name like 'Colt Neck' then get 'CN'
				$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
			}	
			//END CODE TO GET FIRST TWO LETTER OF CITY(LOCATION)			
			//CODE TO GET PHYSICIAN INITIAL
			$physician_name_qry = "select fname,lname,provider_color,id from users where id='$provider'";
			$physician_name_res = imw_query($physician_name_qry);
			$physician_name_row = imw_fetch_array($physician_name_res);	
			$physician_name = array();
			$physician_name[] = substr($physician_name_row["fname"],0,1);
			$physician_name[] = substr($physician_name_row["lname"],0,1);
			$physician_intial = join($physician_name);
			if($physician_intial != '' || $fac_namefirst2letters != ''){
				$new_data .= '<tr><td alighn="left" class="text_10b" style="background-color:#9C6A9D;">';//purple
				$new_data .= str_replace('\'','',strtoupper($physician_intial)."-".strtoupper($fac_namefirst2letters).' ');
				$new_data .= $sch_time;
				$new_data .= '</td></tr>';
				if($cnt < $cnt1){
					$providerSchData[$cnt]['name'] = str_replace('\'','',strtoupper($physician_intial)."-".strtoupper($fac_namefirst2letters).' ');
					$providerSchData[$cnt]['provider_color'] = $physician_name_row["provider_color"];
					$providerSchData[$cnt]['id'] = $physician_name_row["id"];
					$providerSchData[$cnt]['facId'] = $facilityId;
					$cnt++;
				}
			}
		}
		//-- Tool Tip Div ----
		if($new_data){
			$output.= "
				<div style='display:none; background-color:#FFCC33; width:160px; z-index:1000; position:absolute;' id='".$dt."' >
					<table cellpadding='0' cellspacing='0'>
					".$new_data."
					</table>
				</div>
			";
		}
		if(count($finalRes)>0)
		{
			$MOUSEOUT="onMouseout='calender_tool_tip(\"".$dt."\",\"none\");'";
			$MOUSEOVER="onMouseOver='calender_tool_tip(\"".$dt."\",\"block\");'";
			$output.="<table cellpadding='0' border='0' $MOUSEOUT $MOUSEOVER cellspacing='0' style='padding-left:0px;'>";
		}
		for($a=0;$a < count($providerSchData);$a++){
			$provider = $providerSchData[$a]['id'];
			$name = $providerSchData[$a]['name'];
			$provider_color = $providerSchData[$a]['provider_color'];
			$facilityId = $providerSchData[$a]['facId'];
			//--- Get Records as per provider and facility and date ---------//
			$qry = "select count(*) as rowCount from provider_notes 
					where provider_id = '$provider' and facility_id = '$facilityId' 
					and notes_date = '$dt'";
			$qryId = imw_query($qry);
			list($rowCount) = imw_fetch_array($qryId);
			$class= 'text_9';
			$provider_color1 = '';
			if($rowCount>0){
				$class= 'text_10b';
				$colorArr = array('#ECE9D8','#FEFED5','#FFFFCC','#FFCC33','#FFCC66');
				if(!in_array($provider_color,$colorArr)){
					$provider_color1 = 'style="color:'.$provider_color.'"';
				}
			}
			$output.= "<tr><td valign='top' class='text_9' style=' background-color:#9C6A9D;' >";	
			//$output .= "<a $provider_color1 class='$class' href='javascript:void(0)' id='".$dt."_id_".$a."' onClick='open_note(\"$provider\",\"$facilityId\",\"$dt\",this.id)'>";
			//$output.= $name;
			$output.= "</a></td></tr>";
		}
		if(sizeof($finalRes)>0)
		{
			$output.= "</table>";	
		}else{
			$output="";
		}
		return $output;									
	} 
//function to Get Faclity ID using Cookie Var Appointment//
