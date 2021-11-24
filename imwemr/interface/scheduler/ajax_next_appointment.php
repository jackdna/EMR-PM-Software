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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');

$date_c=date("Y-m-d");
if($_REQUEST['pat_id']){
	$pid=$_REQUEST['pat_id'];
	$arr_fac_pro=array();
	$arr_fac_pro=get_patient_profac($pid);		
}
if(count($arr_fac_pro)>0){
	if($arr_fac_pro['provider_id']){
		$_REQUEST['current_provider']=$arr_fac_pro['provider_id'];
	}
	if($arr_fac_pro['facility_id']){
		$_REQUEST['facility_sel']=$arr_fac_pro['facility_id'];
	}
}
$arr_users=get_provider();
$arr_lunch=array();
$arr_lunch=get_lunch_time();

//get list of timeing available in procedure
$strProTimingsQry = "SELECT id, times FROM slot_procedures WHERE times != '' and source='' ORDER BY CONVERT(times,SIGNED) ASC";
$rsProTimingsData = imw_query($strProTimingsQry);
$arrProcTimings = array();
if($rsProTimingsData){
	$k = 0;
	while($arrTempProTimings = imw_fetch_array($rsProTimingsData,imw_ASSOC)){
		if(!in_array($arrTempProTimings['times'], $arrProcTimings)){
		  $id = $arrTempProTimings['id'];
		  $arrProcTimings[$id] = $arrTempProTimings['times'];
		}
	}   
} 
										
$arr_slot_proc=$arr_slot_proc_name=array();
$qry_slot_proc="Select id,proc,acronym,labels,proc_time,doctor_id from slot_procedures where source=''";
$res_slot_proc=imw_query($qry_slot_proc);
while($row_slot_proc=imw_fetch_assoc($res_slot_proc)){
	$proc_id=$row_slot_proc['id'];
	$proc_acronym=($row_slot_proc['acronym'])?$row_slot_proc['acronym']:$row_slot_proc['proc'];
	$proc_name=$row_slot_proc['proc'];
	
	$arr_slot_proc[$proc_id]=$proc_acronym;
	if($row_slot_proc['labels']){
	$arr_slot_proc_label[$proc_id]=$row_slot_proc['labels'];
	}
	$arr_slot_proc_time[$row_slot_proc['doctor_id']][$proc_id]=$row_slot_proc['proc_time'];//[$row_slot_proc['doctor_id']]
	$arr_slot_proc_name[$proc_name]=$proc_acronym;
	//being used in next available
	$arr_slot_proc_time_by_acronym[$row_slot_proc['doctor_id']][strtolower($proc_acronym)]=$row_slot_proc['proc_time'];//
	if($row_slot_proc['labels']){
	$arr_slot_proc_label_by_acronym[strtolower($proc_acronym)]=$row_slot_proc['labels'];
	}
}
if($_REQUEST[event_id])
{
	//set this variable empty as we need to over write it with chain event labels
	$_REQUEST['sel_label']='';
	//get labels from chain event
	$q=imw_query("select * from chain_event_detail where event_id=".$_REQUEST[event_id]." and del_status=0 and procedure_id!=0 order by event_det_id asc");
	while($d=imw_fetch_object($q))
	{
		//overwriting this varialbe will not raise issue
		$selectionFlow=$d->master_setting;
		$consolidationTime=$d->consolidation_time;
		
		if($d->provider_id){$pro_id=$d->provider_id; $removeArr[$pro_id]=$pro_id;}
		else $pro_id=$_REQUEST['current_provider'];
		
		if(strstr($pro_id,','))
		{
			$pro_arr=explode(',',$pro_id);	
			foreach($pro_arr as $proID)
			{
				if(!$removeArr[$proID])
				{
					$chain_prov_proc[$proID]=$d->procedure_id;	
					$temp_arr[$proID]=$proID;	
				}
			}
		}
		else
		{
			$chain_prov_proc[$pro_id]=$d->procedure_id;
			$temp_arr[$proID]=$pro_id;
		}
		sort($temp_arr);
		$chain_pro[$d->procedure_id]=implode(',',$temp_arr);
		unset($temp_arr);
		$provider_arr[$pro_id]=$pro_id;
	}
	//re run $chain_pro loop to remove duplicate values
	foreach($chain_pro as $procedure_id=>$provider_id)
	{	
		unset($providerArr, $newProvArr);
		if(strstr($provider_id,','))
		{
			$providerArr=explode(',',$provider_id);
			foreach($providerArr as $id)
			{
				if(!$removeArr[$id])
				{	
					$newProvArr[$id]=$id;
				}
			}
		}
		if(sizeof($newProvArr)>0)
		{
			$new_chain_pro[$procedure_id]=implode(',',$newProvArr);
		}
		else
		{
			$new_chain_pro[$procedure_id]=$provider_id;
		}
		
	}
	$chain_pro=$new_chain_pro;
	
	if(strstr($_REQUEST['current_provider'],",")){
		$pro_arr=explode(",",$_REQUEST['current_provider']);
		foreach($pro_arr as $pid)
		$provider_arr[$pid]=$pid;
	}
	else $provider_arr[$_REQUEST['current_provider']]=$_REQUEST['current_provider'];
	
	foreach($chain_pro as $proc=>$pro)
	{
	
		//over write incoming label array
		if($arr_slot_proc_label[$proc])
		{
			$_REQUEST['sel_label'].=($_REQUEST['sel_label'])?','.$arr_slot_proc_label[$proc].'~~Information':$arr_slot_proc_label[$proc].'~~Information';
			
			$chain_pro_labels[$proc][]=strtolower($arr_slot_proc_label[$proc]);
			$chain_pro_time[$proc][]=$arr_slot_proc_time;
		}
	}
	
	//over write incoming provider array
	$_REQUEST['current_provider']=implode(',',$provider_arr);
	$_REQUEST['sel_label']=str_replace('~:~','~~Information,',$_REQUEST['sel_label']);
}

if($_REQUEST['current_date'] && $_REQUEST['current_provider']){
	$arr_days_sel=array();
	if($_REQUEST['days_sel']){
		$arr_days_sel=explode(",",$_REQUEST['days_sel']);
	}
	$facility_ids=$_REQUEST['facility_sel'];
	list($c1_yy,$c1_mm,$c1_dd)=explode("-",$_REQUEST['current_date']);
	if($c1_mm>12){$c1_mm=($c1_mm-12);}
	if(strlen($c1_mm)==1){
		$c1_mm="0".$c1_mm;
		$_REQUEST['current_date']=$c1_yy."-".$c1_mm."-".$c1_dd;
	}
	$validate_length=$record=false;$slot_available_records="";$return_slot="";
	
	//==============================Check Scheduler appoitment==================//
	list($c_yy,$c_mm,$c_dd)=explode("-",$_REQUEST['current_date']);
	
	if($c_mm==date('m')){
		$_REQUEST['current_date']=date('Y-m-d');
	}
	$month_v=$c_mm+1;
	$month_g= date("F", mktime(0, 0, 0, $month_v, 0, 0));
	$end_date=$c_yy."-".$c_mm."-31";
	$arr_patient_sch=$arr_patient_sch_label=array();
	$provider_id=$_REQUEST['current_provider'];$file="";$slot_available="";$i=0;
	$fac_qry="";
	
	if($_REQUEST['sel_label']){
		
		$expl_label="";$arr_label=array();
		$expl_label=explode(",",$_REQUEST['sel_label']);
		
		foreach($expl_label as $label_name){
			//do not alter list variable names they are being used on other locations
			list($label,$label_type)=explode("~~",$label_name);
			$label=strtolower($label);
			if($arr_slot_proc_label_by_acronym[$label])
			{
				//get prac_code+labels aganist procedure
				$arr_label=array_merge($arr_label,explode('~:~',strtolower($arr_slot_proc_label_by_acronym[$label])));//getting labels
				$arr_label[]=trim($label);
			}
			else
			{
				$arr_label[]=trim($label);
			}
		}
	}
	foreach($arr_label as $val)
	{
		$tmp_lbl_arr[$val]=$val;
	}
	$arr_label=$tmp_lbl_arr;
	$fac_arr=array();
	if($facility_ids){
		if(strstr($facility_ids,",")){
			$fac_arr=explode(",",$facility_ids);
		}else{$fac_arr[]=$facility_ids;}
	}
	
	
	$time_slot=constant("DEFAULT_TIME_SLOT");
	$varaddtime="00:".$time_slot.":00";
	if($facility_ids){$fac_qry=" and sa_facility_id in(".$facility_ids.")";}
	$qry_month_sch="SELECT id,sa_app_starttime,ADDTIME(sa_app_starttime,'".$varaddtime."') as sa_appt_endtime, sa_app_endtime, sa_facility_id, sa_app_start_date, sa_app_duration, procedureid, sa_doctor_id from schedule_appointments WHERE sa_patient_app_status_id NOT IN('18','201','203') AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_start_date>='".$_REQUEST['current_date']."' and sa_app_start_date<='".$end_date."' and sa_doctor_id IN (".$provider_id.")".$fac_qry;
	$res_month_sch=imw_query($qry_month_sch);
	while($row_month_sch=imw_fetch_assoc($res_month_sch)){
		$sch_id=$row_month_sch['id'];
		$sch_fac=$row_month_sch['sa_facility_id'];
		list($s_h,$s_m)=explode(":",$row_month_sch['sa_app_starttime']);
		list($e_h,$e_m)=explode(":",$row_month_sch['sa_appt_endtime']);
		list($end_hour,$end_min)=explode(":",$row_month_sch['sa_app_endtime']);
		$sch_appt_date=$row_month_sch['sa_app_start_date'];
		$sch_app_duration=$row_month_sch['sa_app_duration'];
		$sch_app_procedure=$arr_slot_proc[$row_month_sch['procedureid']];
		$doc_id=$row_month_sch['sa_doctor_id'];
		if($sch_app_duration){
			$get_dur_min=($sch_app_duration)/60;
		}
		if($get_dur_min!=$time_slot){
			$reminderadd=$get_dur_min%$time_slot;
			if($reminderadd){$get_dur_min=$get_dur_min+$reminderadd;}
			$cntloopend=ceil($get_dur_min/$time_slot);
			for($f=1;$f<=$cntloopend;$f++){
				if($f>1){
					$j=$f-1;
					$s_m=$s_m+($time_slot);
					$s_end_time=$s_m+$time_slot;
				}
				$sch_start_time=date("H:i", mktime($s_h,$s_m, 0, 0, 0, 0));
				$sch_end_time=date("H:i", mktime($s_h,$s_m+$time_slot, 0, 0, 0, 0));
				$arr_patient_sch[$doc_id][$sch_appt_date][$sch_fac][$sch_start_time."-".$sch_end_time]=$sch_id;
			}
		}else{
			$sch_start_time=$s_h.":".$s_m;
			$sch_end_time=$e_h.":".$e_m;
			$arr_patient_sch[$doc_id][$sch_appt_date][$sch_fac][$sch_start_time."-".$sch_end_time]=$sch_id;		
		}
			
	}
	//===========================================================================//
	
	$block_time_arr=get_block_time($_REQUEST['current_date']);
	if($c_mm==date('m')){
		$c_dd=date('d');
	}
	$arr_labels_check=get_labels_val($provider_id,$_REQUEST['current_date'],$date_c,$facility_ids);
	$arr_labels_check_new=get_labels_val_new($provider_id,$_REQUEST['current_date'],$date_c,$facility_ids);
	
	$arr_custom_label=array();
	$arr_custom_label=get_custom_label($provider_id,$_REQUEST['current_date'],$date_c,$facility_ids);
	if(strstr($provider_id,",")){
		$provider_id_arr=explode(",",$provider_id);
	}else{$provider_id_arr[]=$provider_id;}
	$sch_timing="";
	if($_REQUEST['sch_timing']=='morning' || $_REQUEST['sch_timing']=='afternoon'){
		$sch_timing=$_REQUEST['sch_timing'];
	}
	foreach($provider_id_arr as $provider_id){
		if(sizeof($expl_label)==1 && $label_type=='Procedure' && !$_REQUEST['event_id'])
		{
			//check procedure length
			$time_id=($arr_slot_proc_time_by_acronym[$provider_id][$label])?$arr_slot_proc_time_by_acronym[$provider_id][$label]:$arr_slot_proc_time_by_acronym[0][$label];
			if($proc_time_in_minutes[$provider_id]=$arrProcTimings[$time_id])
			{
				$validate_length=true;
			}
		}
	
		for($i=$c_dd;$i<=31;$i++){
			if(strlen($c_mm)==1){$c_mm="0".$c_mm;}
			$moth=$i;
			if(strlen($i)==1){
				$moth="0".$i;
			}
			$p_date=$c_yy."-".$c_mm."-".$moth;	
			$p_date_show= date("m-d-Y", mktime(0, 0, 0, $c_mm, $moth, $c_yy));
			$current_day=strtolower(date("D", mktime(0, 0, 0, $c_mm, $moth, $c_yy)));
			$daya = date("l", mktime(0, 0, 0, $c_mm, $i, $c_yy));
			$sch_file=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').'/scheduler_common/load_xml/'.$p_date.'-'.$provider_id.'.sch';
			if(file_exists($sch_file) && (in_array($current_day,$arr_days_sel)|| count($arr_days_sel)==0)){	
				$sch_file_content=file_get_contents($sch_file);
				$sch_tmp_content=unserialize($sch_file_content);
				if(is_array($sch_tmp_content)){
					$xml_file_appt_date=$sch_tmp_content['dt'];
					$sch_tmp_slot=$sch_tmp_content[$provider_id]['slots'];
					$arr_temp_key=array();
					foreach($sch_tmp_slot as $ket_tmp=> $sch_keyval){
						if($sch_keyval['tmpId']){
							$arr_temp_key[$sch_keyval['id']]['tmpId']=$sch_keyval['tmpId'];
						}
					}
					if(count($arr_custom_label[$provider_id][$p_date])>0){
						$sch_tmp_slot=array_merge($sch_tmp_slot,$arr_custom_label[$provider_id][$p_date]);
					}
					
					foreach($sch_tmp_slot as $ket_slot=> $sch_val){
						$temp_id_to=$arr_temp_key[$sch_val['id']]['tmpId'];
						if($sch_timing=="morning"){
								if(in_array($temp_id,$arr_lunch)){
									if(($arr_lunch[$temp_id_to]==$sch_val['id'])){
										break;
									}
								}else if(strstr($sch_val['id'],"12:00")){
									break;
								}
						}else if($sch_timing=="afternoon"){
							list($timr_s)=explode(":",$sch_val['id']);
							list($time_t)=explode(":",$arr_lunch[$temp_id_to]);
							if(in_array($temp_id,$arr_lunch)){
								if($timr_s<$time_t){
									continue;
								}
							}else if($timr_s<12){
									continue;
							}
						}
						$class="alt3";
						$facility_name_label=$sch_val['fac_name'];
						$facility_id_label=$sch_val['fac_id'];
						
						if(count($fac_arr)>0 && (!in_array($facility_id_label,$fac_arr))){
							continue;
						}
						if($sch_val['status']=='off'){continue;}
						if(!in_array("slot without labels",$arr_label) && strstr($sch_val['label'],";")){
							$arr_explod=explode("; ",$sch_val['label']);
			
							$h=1;
							$sch_label_concate=array();
							//pre($arr_label);
							foreach($arr_explod as $label_val){
								$label_val=trim($label_val);
								if(in_array(strtolower($label_val),$arr_label)){
									$sch_label_concate[]=$label_val;
								}
							}
							
							if(count($sch_label_concate)>0){$sch_val['label']=implode("; ",$sch_label_concate);}
						}else{
							if(!in_array("slot without labels",$arr_label) && !in_array(strtolower($sch_val['label']),$arr_label)){
								continue;
							}
						}
						
						if(strtolower($sch_val["label_type"])=="reserved" || strtolower($sch_val["label_type"])=="lunch" || strtolower($sch_val['label'])=="blocked" || strtolower($sch_val['label'])=="locked" || $block_time_arr[strtolower($sch_val['label'])]){ continue;}
						list($ket_slot_s,$ket_slot_m)=explode("-",trim($ket_slot));
						list($start_h,$start_m)=explode(":",$ket_slot_s);
						list($end_h,$end_m)=explode(":",$ket_slot_m);
						
						$start_foc_h=$start_h;
						$start_foc_m=$start_m;
						$end_foc_h=$end_h;
						$end_foc_m=$end_m;
						$var_slot=constant("DEFAULT_TIME_SLOT");
						$var_slot_v=($var_slot);
						if($start_foc_h){
							$start_foc_h=($start_foc_h+1);
							if(strlen($start_foc_h)==1){$start_foc_h="0".$start_foc_h;}
							$end_foc_h=$start_foc_h;
							$end_foc_m=$start_foc_m+$var_slot;
						}
						$foc_id_st=($start_foc_h).":".$start_foc_m;
						$s_ampm=" AM ";
						if($start_h>12){
							$start_h=($start_h-12);$s_ampm=" PM ";
							if(strlen($start_h)==1){$start_h="0".$start_h;}
						}$e_ampm=" AM ";
						if($end_h>=12){$s_ampm=" PM ";}
						if($end_h>12){
							$end_h=($end_h-12);
							if(strlen($end_h)==1){$end_h="0".$end_h;}
						}
						$ap_start_time=$start_h.":".$start_m;
						$ap_end_time=$end_h.":".$end_m;
						
						$foc_id=str_ireplace(":","_",$foc_id_st);
							
						if(trim($sch_val['label']) && !in_array("slot without labels",$arr_label) ){
							
							$sch_id_label=$arr_labels_check[$provider_id][$p_date][$facility_id_label][$ket_slot];
							
							$l_text=$arr_labels_check_new[$provider_id][$p_date][$facility_id_label][$ket_slot]['l_text'];
							$show_txt=$arr_labels_check_new[$provider_id][$p_date][$facility_id_label][$ket_slot]['l_show_text'];
							
							if($sch_id_label!=''){
								if($l_text==$show_txt)//if we have same labels
								{
									//manualy search for appt for that slot
									if($arr_patient_sch[$provider_id][$p_date][$facility_id_label][$ket_slot]){continue;}
									else {$sch_val['label']=$sch_id_label; }
									
								}
								else
								{
									$sch_val['label']=$sch_id_label;
								}
							}else if($sch_id_label=='all_lbl_filled'){
								continue;
							}else{
								if($arr_patient_sch[$provider_id][$p_date][$facility_id_label][$ket_slot]){continue;}
							}
							//====Check Cache label in Selected Multiple labels Drop Down=====//
							if(strstr($sch_val['label'],";")){
								$arr_expl_lbl=explode(";",$sch_val['label']);
								foreach($arr_expl_lbl as $arr_label_val){
									$arr_label_val=trim($arr_label_val);
									if(!in_array(strtolower($arr_label_val),$arr_label)){continue 2;}
								}
							}else{
								//====Check Cache label in Selected Single labels Drop Down=====//
								if(!in_array(strtolower($sch_val['label']),$arr_label)){continue;}
							}
						}else{
							if($arr_patient_sch[$provider_id][$p_date][$facility_id_label][$ket_slot]){continue;}
						}
						$temp_id=$sch_val["tmpId"];
						
						$record=true;
						if($temp_id==""){
							$temp_id=$arr_temp_key[$sch_val['id']]['tmpId'];
						}
						//======================================================//
						list($times_start_from,$times_end)=explode("-",$sch_val['id']);
						$times_from=$times_start_from.":00";
						$eff_date_add_sch = core_date_format($p_date,"m-d-Y");
						
						if(count($array_bolck_time)>0 && in_array($times_start_from,$array_bolck_time)){continue;}
						
						
						//===================Filter array for chain event ========================//
						if($_REQUEST[event_id] || $validate_length==true)
						{
							unset($sub_array);
							$sub_array['date']=$p_date_show;
							$sub_array['day']=ucwords($current_day);
							$sub_array['start_time']=$ap_start_time;
							$sub_array['end_time']=$ap_end_time;
							$sub_array['ampm']=$s_ampm;
							$sub_array['facility_name_label']=$facility_name_label;
							$sub_array['facility_id_label']=$facility_id_label;
							$sub_array['label']=$sch_val['label'];
							$sub_array['provider_id']=$provider_id;
							$sub_array['times_from']=$times_from;
							$sub_array['temp_id']=$temp_id;
							$sub_array['label_type']=$sch_val["label_type"];
							$sub_array['p_date']=$p_date;
							$sub_array['daya']=$daya;
							$sub_array['foc_id']=$foc_id;
							$sub_array['ket_slot_s']=$ket_slot_s;
							$sub_array['eff_date_add_sch']=$eff_date_add_sch;
							$sub_array['fac_id']=$sch_val["fac_id"];
							$final_array_tmp[$p_date_show][$provider_id][$ket_slot_s]=$sub_array;
						}else{
							
							unset($sel_lbl,$lbl_arr);
							//get first label 
							$lbl_arr=explode(';',$sch_val['label']);
							$sel_lbl=trim($lbl_arr[0]);
							
							//======================================================//
							$slot_available_records.="<tr class=\"link_cursor \" onclick=\"add_appointment_next_sch('".$times_from."','".$eff_date_add_sch."','".$sch_val["fac_id"]."','".$provider_id."','".$temp_id."', '', '".$sch_val["label_type"]."','no','".$p_date."', '".$daya."', '".$foc_id."', '".$sel_lbl."');$('#next_available_slot_div').modal('hide');$('#current_avail_date').val('');\">
								<td>".get_date_format($p_date_show,'mm-dd-yyyy')." </td>
								<td>".ucwords($current_day)." </td>
								<td>".$ap_start_time." - ".$ap_end_time." ".$s_ampm." </td>
								<td>".$facility_name_label."  </td> 
								<td> ".$sch_val['label']."</td>
								<td> ".$arr_users[$provider_id]."</td>
							
							</tr>";	
							
						}
					}
				}
			}
		}
	}
						
	if($_REQUEST['event_id'])
	{
		if(sizeof($final_array_tmp)>=1)
		{
			foreach($final_array_tmp as $date=>$prov_arr)
			{
				unset($prov_arr_new);
				if(sizeof($chain_pro_labels))
				{
				foreach($chain_pro_labels as $proc=>$lbl_arr)
				{
					$required=sizeof($chain_pro_labels);
					$tmp_provider_id_arr=explode(',',$chain_pro[$proc]);
					foreach($tmp_provider_id_arr as $tmp_provider_id)
					{
						$selfFound=0;$selfFoundTemp=0;
						foreach($lbl_arr as $lbl)
						{
							$this_found=false;
							foreach($prov_arr[$tmp_provider_id] as $time=>$time_arr)
							{							
								$sub_lblarr=explode('~:~',strtolower($lbl));
								unset($time_arr_label);
								$time_arr_label=explode('; ',$time_arr['label']);
								$time_arr_label=array_map('strtolower',$time_arr_label);
								if(array_intersect($time_arr_label,$sub_lblarr) && trim($time_arr['label']))
								{
									$lengthy_procedure=true;
									$time_id=($arr_slot_proc_time[$tmp_provider_id][$proc])?$arr_slot_proc_time[$tmp_provider_id][$proc]:$arr_slot_proc_time[0][$proc];
									//check procedure length
									if($time_in_minutes=$arrProcTimings[$time_id])
									{
										//we got number of slots required here
										$slots_required=$time_in_minutes/DEFAULT_TIME_SLOT;	
										for($i=0;$i<$slots_required;$i++)
										{
											//get time
											$checkTime=getTime($time,$i);
											$temp_time_arr=$prov_arr[$tmp_provider_id][$checkTime];
											unset($temp_time_arr_label);
											$temp_time_arr_label=explode('; ',$temp_time_arr['label']);
											$temp_time_arr_label=array_map('strtolower',$temp_time_arr_label);
											if(array_intersect($temp_time_arr_label,$sub_lblarr) && trim($temp_time_arr['label']))
											{
												//its okay let it continue	
											}else 
											{
												$lengthy_procedure=false;
											}
										}
										if($lengthy_procedure==true)
										{
											//calculate end time as per procedure length in min
											$a=$prov_arr[$tmp_provider_id][$time]['start_time'];
											$b=$prov_arr[$tmp_provider_id][$time]['ampm'];	
											list($h,$m)=explode(":",date("H:i", strtotime("$a $b")));
											list($a1,$b1)=explode(" ",date("h:i A",mktime($h,$m+$time_in_minutes)));
											$prov_arr[$tmp_provider_id][$time]['times_to']=mktime($h,$m+$time_in_minutes);
											$prov_arr[$tmp_provider_id][$time]['end_time']=$a1;
											$prov_arr[$tmp_provider_id][$time]['ampm']=$b1;	
											unset($a,$a1,$b,$b1,$h,$m);//free up memory
										}
									}
									if($lengthy_procedure==true)
									{
										$foundTemp++;
										$selfFoundTemp++;
										$this_found=true;
										//break;	
									}else{
										unset($prov_arr[$tmp_provider_id][$time]);
									}
								}
							}
							//removing that due to duplicate results
							//if($this_found==true)break 2;
							if($selfFoundTemp>=1){$selfFound++;$found++;}
						}
						if($selfFound==0){
							$testarr[$tmp_provider_id]=$prov_arr[$tmp_provider_id];unset($prov_arr[$tmp_provider_id]);
							}else{
								$prov_arr_new[$tmp_provider_id]=$prov_arr[$tmp_provider_id];
							}
					}
				}//echo "$required<=$found<br/>";
				if($required<=$found)
				{
					//create final array to display
					$final_array[$date]=$prov_arr_new;unset($prov_arr_new);
				}
				$required=$found=$foundTemp=0;
				}
			}
		}
		else
		{
			$record=false;	
		}
		
		//print final data array
		if(sizeof($final_array)>=1)
		{
			foreach($final_array as $date=>$prov_arr)
			{
				$row++;
				$col=0;
				$slot_available_records_temp.="<tr>";
				$slot_available_records_temp.="<td>".get_date_format($date,'mm-dd-yyyy')." </td>";
				$prov_id_str='';
				$recCounterNew=0;
				list($m,$d,$y)=explode('-',$date);
				$dateToSave="$y-$m-$d";
				unset($approvedID);
				foreach($prov_arr as $prov_id=>$time_arr)
				{
					$col++;
					unset($lbl_arr);
					$recCounterNew++;
					$total_procedures=0;
					$total_procedures=sizeof($chain_pro_labels);
					if(sizeof($chain_pro_labels)>0)
					{
						foreach($chain_pro_labels as $proc=>$lbl_arr)
						{
							$procedure=$proc;
							$chain_pro_tmp=explode(',',$chain_pro[$proc]);
							foreach($chain_pro_tmp as $prov_id_compare)
							{
								if($prov_id_compare==$prov_id)break 2;
							}
						}
						$recCounter[$proc]++;
						if($selectionFlow!=3){
						$option="
						<div class=\"form-group\">
						<label>$arr_users[$prov_id]</label>
						<select id=\"\" class='$procedure form-control minimal ".$row."_".$procedure."' onChange=\"selected('$procedure',this,'timing_$procedure')\" data-width=\"100%\" data-maxProcedure=\"$total_procedures\" data-selectionFlow=\"$selectionFlow\" data-procedurespot=\"".$row."_".$col."\" data-procedureclass=\"".$row."_".$procedure."\">
						<option value=''>Please Select</option>";
						}
						$opt=false;
						foreach($time_arr as $time)
						{
							$fac_id=$time['facility_id_label'];
							$temp_id=$time['temp_id'];

							$label_type=$time['label_type'];
							$p_date=$time['p_date'];
							$daya=$time['daya'];
							$foc_id=$time['foc_id'];
							$lbl_arr=implode('~:~',$lbl_arr);//lbl_arr hav multi keys so we are imploding all these
							$tmp_arr=explode('~:~',$lbl_arr);//now we hv single string and we explode it to an array
							$lbl_arr=array_map('strtolower',$tmp_arr);

							unset($time_label,$lbl_arr_new,$tmp_arr);
							$time_label=explode('; ',$time['label']);
							$time_label=array_map('strtolower',$time_label);

							if(array_intersect($time_label,$lbl_arr))
							{	
								//validation for consolidation filter if its on
								if($selectionFlow==3)
								{
									
									$from_time=$to_time='';
									$from_time=date('H:i:s', strtotime($time[start_time].':00 '.$time[ampm]));
									$to_time_ampm=(strtotime($time[end_time].':00')>=strtotime('12:00:00') && $time[ampm]!='pm')?'pm':$time[ampm];
									$to_time=date('H:i:s', strtotime($time[end_time].':00 '.$to_time_ampm));
										
									$sql="insert into chain_event_temp set user_id='$_SESSION[authUserID]',
									event_id='$_REQUEST[event_id]',
									dated='$dateToSave',
									proc='$procedure',
									provider='$prov_id',
									from_time='$from_time',
									to_time='$to_time',
									ampm='$time[ampm]',
									label='$time[label]',
									template_id='$temp_id',
									facility_id='$fac_id',
									label_type='$label_type',
									timestamp='".date('Y-m-d')."'";
									imw_query($sql)or die(imw_error());
									$record=false;	
								}
								else{
									$option.="<option value=\"$time[times_from]~:~$temp_id~:~$label_type~:~$prov_id~:~$procedure~:~$fac_id\" $sel data-timestamp=\"".strtotime($time[end_time].':00 '.$time[ampm])."\">$time[start_time] - $time[end_time] $time[ampm] ($time[label])</option>";	
									$opt=true;
									$firstSel=true;
									$consolidation_pass=true;//to by pass consilidation condition
								}
							}
						}
						if($option && $selectionFlow!=3)
						{
							$option.="</select></div>";
							$firstSel=true;
							if($opt==true)
							$options[$procedure][]=$option;
						}
					}
				}	
				/*
				check aganist consolidtion if any
				*/
				if($selectionFlow==3)
				{
					$chain_pro_temp='';
					$chain_pro_temp=$chain_pro;
					
					$fixedQueryCheck=" AND user_id='$_SESSION[authUserID]' AND event_id='$_REQUEST[event_id]' AND timestamp='".date('Y-m-d')."' ";
					foreach($chain_pro as $proc=>$prov)
					{
						unset($chain_pro_temp[$proc]);
						//get first record to compare
						$sql=imw_query("select id, to_time from chain_event_temp where provider IN($prov) and proc=$proc $fixedQueryCheck order by id asc");
						while($data=imw_fetch_object($sql))
						{
							//get first time to validate
							$time_range_from=$data->to_time;
							list($h,$i,$s)=explode(':',$data->to_time);
							$time_range_to=date('H:i:s',mktime($h+$consolidationTime,$i,$s));
							//required number of points
							$required=sizeof($chain_pro_temp);
							
							foreach($chain_pro_temp as $sub_proc=>$sub_prov)
							{
								
								$sql_sub=imw_query("select id from chain_event_temp where provider IN($sub_prov) and proc=$sub_proc $fixedQueryCheck
								AND (from_time BETWEEN '$data->to_time' AND '$time_range_to')
								order by id asc");
								if(imw_num_rows($sql_sub)>0)
								{
									//if we have record then add parent id
									$approvedID[$data->id]=$data->id;
								}
								while($sql_sub_data=imw_fetch_object($sql_sub))
								{
									$approvedID[$sql_sub_data->id]=$sql_sub_data->id;
								}
								
							}
						}
						//now break this loop as we only need to know for first provider procedure
						break;
					}
					unset($col);
					if(sizeof($approvedID)>0)
					{
						$record=true;
						$approvedIdStr=implode(',',$approvedID);
						foreach($prov_arr as $prov_id=>$time_arr)
						{
							$col++;
							unset($lbl_arr);
							$recCounterNew++;
							$total_procedures=0;
							$total_procedures=sizeof($chain_pro_labels);
							if(sizeof($chain_pro_labels)>0)
							{
								foreach($chain_pro_labels as $proc=>$lbl_arr)
								{
									$procedure=$proc;
									$chain_pro_tmp=explode(',',$chain_pro[$proc]);
									foreach($chain_pro_tmp as $prov_id_compare)
									{
										if($prov_id_compare==$prov_id)break 2;
									}
								}
								$recCounter[$proc]++;
								
								$option="
								<div class=\"form-group\">
								<label>$arr_users[$prov_id]</label>
								<select id=\"\" class='$procedure form-control minimal ".$row."_".$procedure."' onChange=\"selected('$procedure',this,'timing_$procedure')\" data-width=\"100%\" data-maxProcedure=\"$total_procedures\" data-selectionFlow=\"$selectionFlow\" data-procedurespot=\"".$row."_".$col."\" data-procedureclass=\"".$row."_".$procedure."\">
								<option value=''>Please Select</option>";
								$opt=false;
								//get options from database
								$option_query=imw_query("select * from chain_event_temp where provider IN($prov_id) and proc=$procedure 
								and dated='$dateToSave' $fixedQueryCheck and id IN($approvedIdStr) order by id asc");
								while($time=imw_fetch_assoc($option_query))//foreach($time_arr as $time)
								{
									$fac_id=$time['facility_id'];
									$temp_id=$time['template_id'];
									$label_type=$time['label_type'];

									unset($time_label,$lbl_arr_new,$tmp_arr);
									$time_label=explode('; ',$time['label']);
									$time_label=array_map('strtolower',$time_label);

									$from_time=$to_time='';
									$from_time=date("h:i", strtotime($time[from_time]));
									$to_time=date("h:i", strtotime($time[to_time]));
									$option.="<option value=\"$time[from_time]~:~$temp_id~:~$label_type~:~$prov_id~:~$procedure~:~$fac_id\" data-timestamp=\"".strtotime($time[to_time])."\">$from_time - $to_time $time[ampm] ($time[label])</option>";	
									$opt=true;
									$firstSel=true;
								}
								if($option)
								{
									$option.="</select></div>";
									$firstSel=true;
									if($opt==true)
									$options[$procedure][]=$option;
								}
							}
						}
					}
					else
					{
						$record=false;	
					}
					//removed temporarely saved records for consolidation
					$sql="delete from chain_event_temp where user_id='$_SESSION[authUserID]' 
					AND event_id='$_REQUEST[event_id]' 
					AND timestamp='".date('Y-m-d')."'";
					imw_query($sql)or die(imw_error());
				}
				
				foreach($options as $opt)
				{
					$slot_available_records_temp.="<td>";
					$slot_available_records_temp.=implode('',$opt);
					$slot_available_records_temp.="</td>";		
				}

				$isOptFound=true;
				foreach($chain_pro_labels as $proc1=>$lbl_arr1)
				{
					if($recCounter[$proc1]<=0)$isOptFound=false;
				}
				$isApplicable[$date]=$isOptFound;

				$appt_count=0;
				$appt_count=sizeof($options);
				$procedures=implode(',',array_keys($options));
				$slot_available_records_temp.="<td><button name='add_appt' id='add_appt' class='btn btn-success' onclick=\"add_appointment_next_sch_multi('".$procedures."','".$appt_count."','".$date."','".$fac_id."','no','".$p_date."', '".$daya."', '".$foc_id."');$('#next_available_slot_div').modal('hide');$('#current_avail_date').val('');\">Add Appt</button></td>
				</tr>";	
				if($isOptFound==true)
				{
					$slot_available_records.=$slot_available_records_temp;
				}
				unset($options,$recCounter,$slot_available_records_temp);
			}

			foreach($final_array as $date=>$prov_arr)
			{
				if($isApplicable[$date])
				{
					foreach($prov_arr as $prov_id=>$time_arr)
					{
						$recCounterNew++;
						$header[$arr_slot_proc[$chain_prov_proc[$prov_id]]]=$arr_slot_proc[$chain_prov_proc[$prov_id]];//'.$arr_users[$prov_id].'<br/>
					}break;
				}
			}
			if(sizeof($header)>=1)
			{
				//create header for table
				$slot_header='<thead><tr><th class="text-center">Date</th>';
				foreach($header as $head)
				{$slot_header.='<th class="text-center">'.$head.'</th>';}
				$slot_header.='<th class="text-center">Action</th></tr></thead>';
			}
			unset($header);
		}
		else
		{$record=false;}
	}
	else if($validate_length==true)//code to get valid length procedure
	{
		$label_collection=explode('~:~',strtolower($arr_slot_proc_label_by_acronym[$label]));
		$label_collection[]=strtolower($label);
		foreach($final_array_tmp as $date=>$prov_arr)
		{
			foreach($prov_arr as $pro_id=>$time_arr)
			{
				//we got number of slots required here
				$slots_required=$proc_time_in_minutes[$pro_id]/DEFAULT_TIME_SLOT;
				foreach($time_arr as $time=>$detail_arr)
				{
					$temp_end_time=$checkTime='';
					$lengthy_procedure=true;	
					for($i=0;$i<$slots_required;$i++)
					{
						//get time
						$checkTime=getTime($time,$i);
						$temp_end_time=getEndTime($time,$i+1);
						$temp_time_arr=$prov_arr[$pro_id][$checkTime];
						if(in_array(strtolower($temp_time_arr['label']),$label_collection) && trim($temp_time_arr['label']))
						{
							//its okay let it continue	
						}else 
						{
							$lengthy_procedure=false;
						}
					}
					
					if($lengthy_procedure==true)
					{
						$valid_procedures++;
						unset($sel_lbl,$lbl_arr);
						//get first label 
						$lbl_arr=explode(';',$detail_arr['label']);
						$sel_lbl=trim($lbl_arr[0]);
						
						//======================================================//
						$slot_available_records.="<tr class=\"link_cursor \" onclick=\"add_appointment_next_sch('".$detail_arr['times_from']."','".$detail_arr['eff_date_add_sch']."','".$detail_arr["fac_id"]."','".$pro_id."','".$detail_arr['temp_id']."', '', '".$detail_arr["label_type"]."','no','".$detail_arr['p_date']."', '".$detail_arr['daya']."', '".$detail_arr['foc_id']."', '".$sel_lbl."');$('#next_available_slot_div').modal('hide');$('#current_avail_date').val('');\">
							<td>".get_date_format($detail_arr['date'],'mm-dd-yyyy')." </td>
							<td>".$detail_arr['day']." </td>
							<td>".$detail_arr['start_time']." - ".$temp_end_time." </td>
							<td>".$detail_arr['facility_name_label']."  </td> 
							<td> ".$detail_arr['label']."</td>
							<td> ".$arr_users[$pro_id]."</td>
						
						</tr>";	
					}
				}
			}
		}
	}
	
	if($validate_length==true)
	{
		if(!$valid_procedures)$record=false;	
	}
	else{
		if(!trim($slot_available_records))$record=false;
	}
	if($record==false){
		//removing duplicate records by puting value in array key 
		foreach($arr_label as $key=>$val)
		{
			$newArr[$val]=$val;
		}
		
		$labels_name=str_replace("Slot without labels","Open Time Slot",implode(",",$newArr));
		$return_slot='<div>No slot available for "<b>'.$labels_name.'</b>" label</div>';
	}else{
		if($_REQUEST[event_id])
		{
			
		$return_slot='
		<div id="load_nxt_avial" style="position:absolute; display:none"><img src="'.$GLOBALS['webroot'].'/library/images/sch-loader.gif"></div>
		<table name="nxt_appt" id="nxt_appt" class="table table-striped resultset" >
			'.$slot_header.'
			<tbody id="nxt_avail">
			'.$slot_available_records.$slot_available.'
			</tbody>
		</table>';		
		
		}
		else
		{
		
		$return_slot='
			<script>
				
			function sortTable(td_i,obj){
				var g_r=1;
				var l_r=-1;
				$("#nxt_appt td span").html("");
				//$("#load_nxt_avial").show();
				if($("#"+obj).val()=="1"){
					g_r=-1;
					l_r=1;
					$("#"+obj).val(0);
					$("#"+obj+"span").html(" <img src=\''.$GLOBALS['webroot'].'/library/images/page_block_arrow_up.gif\'>");
				}else{
					$("#"+obj).val(1);
					$("#"+obj+"span").html(" <img src=\''.$GLOBALS['webroot'].'/library/images/page_block_arrow_down.gif\'>");
				}
			  var rows = $("#nxt_appt tbody tr").get();

			  rows.sort(function(a, b) {
			  var A = $(a).children("td").eq(td_i).text().toUpperCase();
			  var B = $(b).children("td").eq(td_i).text().toUpperCase();
				//alert(A)
			  if(A < B) {
				return g_r;
			  }
			 
			  if(A > B) {
				return l_r;
			  }
			  	return 0;
			  });
			  $.each(rows, function(index, row) {
				$("#nxt_appt").children("tbody").append(row);
			  });
			 // $("#load_nxt_avial").hide();
			}
			</script>
		<div id="load_nxt_avial" style="margin:150px 250px;position:absolute; display:none"><img src="'.$GLOBALS['webroot'].'/library/images/sch-loader.gif"></div>
		<table name="nxt_appt" id="nxt_appt" class="table table-striped resultset" >
			<thead>
				<tr>
					<th onclick="sortTable(0,\'date_sort\')">Date<span id="date_sortspan"></span><input size="1" type="hidden" id="date_sort" value="1"></th>
					<th onclick="sortTable(1,\'day_sort\')">Day of the week<span id="day_sortspan"></span><input size="1" type="hidden" id="day_sort" value="0"></th>
					<th onclick="sortTable(2,\'time_sort\')">Time<span id="time_sortspan"></span><input size="1" type="hidden" id="time_sort" value="0"></th>
					<th onclick="sortTable(3,\'fac_sort\')">Facility<span id="fac_sortspan"></span><input size="1" type="hidden" id="fac_sort" value="0"></th>
					<th onclick="sortTable(4,\'label_sort\')">Label<span id="label_sortspan"></span><input size="1" type="hidden" id="label_sort" value="0"></th>
					<th onclick="sortTable(5)">Physician</th>
				</tr>
			</thead>
			<tbody id="nxt_avail">
			'.$slot_available_records.$slot_available.'
			</tbody>
		</table>';		
	
		}
	}
	$curr_mon=$month_g." ".$c_yy;
	$load_date=$c_yy."-".$c_mm."-01";
	$curn_year=date('Y');
	$select_month='<select id="label_month" class="form-control minimal">';
	$current_month=(date('m')+1);
	$loop_end=($current_month+12);
	
	for($mon=$current_month;$mon<=$loop_end;$mon++){
		$month_v=date("F", mktime(0, 0, 0, $mon, 0, $curn_year));
		$c_mon=($mon);
		$c_mon=($c_mon-1);
		if($c_mon>12){$c_mon=($c_mon-12);}
		if($c_mon>12){$c_mon=($c_mon-12);}
		
		if(strlen($c_mon)==1){$c_mon="0".$c_mon;}
		$date_vla=$curn_year."-".$c_mon."-01";
		$sel_load_date="";
		if($load_date==$date_vla){
			$sel_load_date=" SELECTED ";
		}
		
		$select_month.="<option ".$sel_load_date." value=\"".$date_vla."\">".$month_v." ".$curn_year."</option>";
		if(strtolower($month_v)=="december"){
			$curn_year++;
		}
	}
	
	
	$select_month.="</select>"; 
	if($arr_fac_pro['provider_id']){
		$prov_id=$arr_fac_pro['provider_id'];
	}
	if($arr_fac_pro['facility_id']){
		$facility_id=$arr_fac_pro['facility_id'];
	}
	$return_slot.="<script>
	var consolidationTime='".$consolidationTime."';
		function selected(cls,obj,idName)
		{
			$(obj).attr('id',idName);
		//	$('select.'+cls+' option').removeAttr(\"selected\");
			$('select.'+cls).each(function() {
            	$(this).not(obj).find('option').removeAttr(\"selected\");
				$(this).not(obj).removeAttr('id');
        	});
			
			
			var datrArr = $('#'+idName).data();
			var maxProcedure=datrArr.maxprocedure;
			var selectionFlow=datrArr.selectionflow;
			var procedureSpot=datrArr.procedurespot;
			
			if(selectionFlow==2)//waterflow
			{
				var spot_arr=procedureSpot.split('_');
				
				if(spot_arr[1]<maxProcedure)
				{
					//get selected item timestamp
					//var cr_val= $(this).find('option:selected').data('timestamp');
					var cr_timestamp= $('#'+idName).find('option:selected').data('timestamp');
					
					for(var i=parseInt(spot_arr[1])+1; i<=maxProcedure; i++)
					{
						var next_slop = $('select[data-procedurespot^='+spot_arr[0]+'_'+i+']');
						//enable all options by default
						next_slop.find('option').prop('disabled',false);
						
						var datrArrSub = next_slop.data();
						$('select.'+datrArrSub.procedureclass).each(function() {
							$(this).find('option').each(function(id,ele){
							var target_timestamp=$(ele).data('timestamp');
								if(target_timestamp){
									if(target_timestamp<=cr_timestamp)
									{
										$(ele).prop('disabled',true);
									}else
									{
										$(ele).prop('disabled',false);
									}
								}
							});
						});
							
					}
				}
			}else if(selectionFlow==3)//consolidated
			{
				var spot_arr=procedureSpot.split('_');
				if(spot_arr[1]==1)
				{
					//get selected item timestamp
					//var cr_val= $(this).find('option:selected').data('timestamp');
					var cr_timestamp= $('#'+idName).find('option:selected').data('timestamp');
					var max_timestamp=parseInt(cr_timestamp)+parseInt(60*60*consolidationTime);
					for(var i=parseInt(spot_arr[1])+1; i<=maxProcedure; i++)
					{
						var next_slop = $('select[data-procedurespot^='+spot_arr[0]+'_'+i+']');
						//enable all options by default
						next_slop.find('option').prop('disabled',false);
						
						var datrArrSub = next_slop.data();
						$('select.'+datrArrSub.procedureclass).each(function() {
							$(this).find('option').each(function(id,ele){
							var target_timestamp=$(ele).data('timestamp');
								if(target_timestamp){
									if(target_timestamp<cr_timestamp || target_timestamp>=max_timestamp)
									{
										$(ele).prop('disabled',true);
									}else
									{
										$(ele).prop('disabled',false);
									}
								}
							});
						});
							
					}
				}
			}
		}
		$(document).ready(function(){
			get_current_month('".$select_month."','".$prov_id."','".$facility_id."');
			$('#label_month').change(function(){  $('#current_avail_date').val(this.value);get_avaiable_slot();});
			//$('#load_nxt_avial').hide();
			//sortTable(0,'date_sort')
		});
	</script>";
	echo $return_slot;
}


?>