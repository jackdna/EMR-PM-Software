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
if($_REQUEST['pat_id']){
	$pid=$_REQUEST['pat_id'];
	$arr_fac_pro=array();
	$arr_fac_pro=get_patient_profac($pid);	
}
$slot_available_records=false;
$arr_users=get_provider();
$arr_lunch=array();
$arr_lunch=get_lunch_time();
$arr_slot_proc=$arr_slot_proc_name=array();
$qry_slot_proc="Select id,proc,acronym from slot_procedures";
$res_slot_proc=imw_query($qry_slot_proc);
while($row_slot_proc=imw_fetch_assoc($res_slot_proc)){
	$proc_id=$row_slot_proc['id'];
	$proc_acronym=$row_slot_proc['acronym'];
	$proc_name=$row_slot_proc['proc'];
	$arr_slot_proc[$proc_id]=$proc_acronym;
	$arr_slot_proc_name[$proc_name]=$proc_acronym;
}
if($_REQUEST['current_date'] && $_REQUEST['current_provider']){
	$facility_ids=$_REQUEST['facility_sel'];
	list($c1_yy,$c1_mm,$c1_dd)=explode("-",$_REQUEST['current_date']);
	if($c1_mm>12){$c1_mm=($c1_mm-12);}
	if(strlen($c1_mm)==1){
		$c1_mm="0".$c1_mm;
		$_REQUEST['current_date']=$c1_yy."-".$c1_mm."-".$c1_dd;
	}
	$record=false;$slot_available_records="";$return_slot="";
	if($_REQUEST['sel_label']){
		$expl_label="";$arr_label=array();
		$expl_label=explode(",",$_REQUEST['sel_label']);
		foreach($expl_label as $label_name){
			list($label,$label_type)=explode("~~",$label_name);
			if($arr_slot_proc_name[$label]){
				$arr_label[]=trim($arr_slot_proc_name[$label]);
			}else{
				$arr_label[]=trim($label);
			}
		}
	}
	if($c_mm==date('m')){
		$_REQUEST['current_date']=date('Y-m-d');
	}
	//==============================Check Scheduler appoitment==================//
	list($c_yy,$c_mm,$c_dd)=explode("-",$_REQUEST['current_date']);
	
	$month_v=$c_mm+1;
	$month_g= date("F", mktime(0, 0, 0, $month_v, 0, 0));
	$end_date=$c_yy."-".$c_mm."-31";
	$arr_patient_sch=$arr_patient_sch_label=array();
	$provider_id=$_REQUEST['current_provider'];$file="";$slot_available="";$i=0;
	$fac_qry="";
	$fac_arr=array();
	if($facility_ids){
		if(strstr($facility_ids,",")){
			$fac_arr=explode(",",$facility_ids);
		}else{$fac_arr[]=$facility_ids;}
	}
	
	
	$time_slot=constant("DEFAULT_TIME_SLOT");
	$varaddtime="00:".$time_slot.":00";
	if($facility_ids){$fac_qry=" and sa_facility_id in(".$facility_ids.")";}
	$qry_month_sch="SELECT id,sa_app_starttime,ADDTIME(sa_app_starttime,'".$varaddtime."') as sa_appt_endtime,sa_app_endtime,sa_facility_id,sa_app_start_date,sa_app_duration,procedureid from schedule_appointments WHERE sa_patient_app_status_id!='18' and sa_app_start_date>='".$_REQUEST['current_date']."' and sa_app_start_date<='".$end_date."' and sa_doctor_id IN (".$provider_id.")".$fac_qry;
	$res_month_sch=imw_query($qry_month_sch);
	while($row_month_sch=imw_fetch_assoc($res_month_sch)){
		$sch_id_sub=$row_month_sch['id'];
		$sch_fac=$row_month_sch['sa_facility_id'];
		list($s_h,$s_m)=explode(":",$row_month_sch['sa_app_starttime']);
		list($e_h,$e_m)=explode(":",$row_month_sch['sa_appt_endtime']);
		list($end_hour,$end_min)=explode(":",$row_month_sch['sa_app_endtime']);
		$sch_appt_date=$row_month_sch['sa_app_start_date'];
		$sch_app_duration=$row_month_sch['sa_app_duration'];
		$sch_app_procedure=$arr_slot_proc[$row_month_sch['procedureid']];
		if($sch_app_duration){
			$get_dur_min=($sch_app_duration)/60;
		}
		if($get_dur_min!=$time_slot){
			//echo $get_dur_min;echo "<hr>";
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
				$arr_patient_sch[$sch_appt_date][$sch_fac][$sch_start_time."-".$sch_end_time]=$sch_id_sub;
				$arr_patient_sch_label[$sch_appt_date][$sch_fac][$sch_start_time."-".$sch_end_time][$sch_app_procedure]=$sch_id_sub;
			}
		}else{
			$sch_start_time=$s_h.":".$s_m;
			$sch_end_time=$e_h.":".$e_m;
			$arr_patient_sch[$sch_appt_date][$sch_fac][$sch_start_time."-".$sch_end_time]=$sch_id_sub;		
			$arr_patient_sch_label[$sch_appt_date][$sch_fac][$sch_start_time."-".$sch_end_time][$sch_app_procedure]=$sch_id_sub;
		}
			
	}
	//===========================================================================//
	$block_time_arr=get_block_time($_REQUEST['current_date']);
	$arr_custom_label=array();
	$arr_labels_check=get_labels_val($provider_id,$_REQUEST['current_date'],$date_c,$facility_ids);
	
	$arr_custom_label=get_custom_label($provider_id,$_REQUEST['current_date'],$date_c,$facility_ids);
	if(strstr($provider_id,",")){
		$provider_id_arr=explode(",",$provider_id);
	}else{$provider_id_arr[]=$provider_id;}
	$sch_timing="";
	if($_REQUEST['sch_timing']=='morning' || $_REQUEST['sch_timing']=='afternoon'){
		$sch_timing=$_REQUEST['sch_timing'];
	}
	foreach($provider_id_arr as $provider_id){
		foreach($arrDates as $p_date){
			if($p_date>=$_REQUEST['current_date'])
			{
				list($c_yy,$c_mm,$c_dd)=explode('-',$p_date);
				$current_day=strtolower(date("D", mktime(0, 0, 0, $c_mm, $c_dd, $c_yy)));
				if(!$dir_path)$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common";
				$sch_file=$dir_path.'/load_xml/'.$p_date.'-'.$provider_id.'.sch';
				//	echo $file_p=$sch_file;echo "<br>";
				if(file_exists($sch_file)){
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
						if(count($arr_custom_label[$p_date])>0){
							$sch_tmp_slot=array_merge($sch_tmp_slot,$arr_custom_label[$p_date]);
						}
						
						foreach($sch_tmp_slot as $ket_slot=> $sch_val){
							//skip this slot if status is off
							if($sch_val['status']=='off')continue;
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
							
							if(!in_array("Slot without labels",$arr_label) && strstr($sch_val['label'],";")){
								$arr_explod=explode("; ",$sch_val['label']);
								$h=1;
								$sch_label_concate=array();
								foreach($arr_explod as $label_val){
									if(in_array(trim($label_val),$arr_label)){
										$sch_val['label']=$label_val;break;
									}
								}
								//if(count($sch_label_concate)>0){$sch_val['label']=implode("; ",$sch_label_concate);}
								//echo $sch_val['label'];echo "<hr>";
							}else{
								if(!in_array("Slot without labels",$arr_label) && !in_array($sch_val['label'],$arr_label)){
									continue;
								}
							}
							if(strtolower($sch_val["label_type"])=="reserved" || strtolower($sch_val["label_type"])=="lunch" || strtolower($sch_val['label'])=="blocked" || strtolower($sch_val['label'])=="locked" || $block_time_arr[strtolower($sch_val['label'])]){ continue;}
							$class="alt3";
							$facility_name_label=$sch_val['fac_name'];
							$facility_id_label=$sch_val['fac_id'];
							
							if(count($fac_arr)>0 && (!in_array($facility_id_label,$fac_arr))){
								continue;
							}
							
							list($ket_slot_s,$ket_slot_m)=explode("-",trim($ket_slot));
							list($start_h,$start_m)=explode(":",$ket_slot_s);
							list($end_h,$end_m)=explode(":",$ket_slot_m);
							
							$start_foc_h=$start_h;
							$start_foc_m=$start_m;
							$end_foc_h=$end_h;
							$end_foc_m=$end_m;
							$var_slot=constant("DEFAULT_TIME_SLOT");
							$var_slot_v=($var_slot);
							/*if($start_foc_m<$var_slot_v){
								$start_foc_m=($start_foc_m+$var_slot_v);
							}
							if($start_foc_m<50){
								$end_foc_m=($start_foc_m+$var_slot);
							}*/
							if($start_foc_h){
								$start_foc_h=($start_foc_h+1);
								if(strlen($start_foc_h)==1){$start_foc_h="0".$start_foc_h;}
								//$start_foc_m="00";
								$end_foc_h=$start_foc_h;
								$end_foc_m=$start_foc_m+$var_slot;
							}
							$foc_id_st=($start_foc_h).":".$start_foc_m;//."-".$end_foc_h.":".$end_foc_m;
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
							
							if(trim($sch_val['label']) && !in_array("Slot without labels",$arr_label) && count($arr_label)==1){
								$sch_id_label=$arr_labels_check[$p_date][$facility_id_label][$ket_slot];
								if($sch_id_label){
									$arr_sch_ids=explode("::",$sch_id_label);
									$arr_label_id=$arr_label_name=array();
									foreach($arr_sch_ids as $label_id_name){
										list($sch_id_label,$sch_label_name)=explode(":",$label_id_name);
										if(trim($sch_id_label)){
											$arr_label_id[]=trim($sch_id_label);
											$arr_label_name[]=trim($sch_label_name);
										}
									}	
								}
								//echo trim($sch_val['label']);echo "<br>";					
								$sch_id_sub=$arr_patient_sch[$p_date][$facility_id_label][$ket_slot];
								if(trim($sch_id_sub) && in_array(trim($sch_id_sub),$arr_label_id) && in_array(trim($sch_val['label']),$arr_label_name)){continue;}
								if(!in_array($sch_val['label'],$arr_label)){continue;}
							}else{
								if($arr_patient_sch[$p_date][$facility_id_label][$ket_slot]){continue;}
							}
							$temp_id=$sch_val["tmpId"];
							
							$slot_available_records=false;
							$record=true;
							if($temp_id==""){
								$temp_id=$arr_temp_key[$sch_val['id']]['tmpId'];
							}
							//======================================================//
							list($times_start_from,$times_end)=explode("-",$sch_val['id']);
							$times_from=$times_start_from.":00";
							$eff_date_add_sch = core_date_format($p_date,"m-d-Y");
							//======================================================//
							if(count($array_bolck_time)>0 && in_array($times_start_from,$array_bolck_time)){continue;}
							else
							{
								//store detail of available first slot
								$hover=$slot_available_records=get_date_format($p_date,'yyyy-mm-dd')."&nbsp;-&nbsp;".ucwords($current_day)."&nbsp;-&nbsp;".$ap_start_time." - ".$ap_end_time." ".$s_ampm."&nbsp;-&nbsp;".$facility_name_label."&nbsp;-&nbsp;".$arr_users[$provider_id];	
								$slot_available_records=true; break;	
							}
						
						}
					}
				}
			
			}
		}
	}
	//do time available in desired week or not
	if($slot_available_records==true)$highlight="Available";
	else $highlight='Not';	
}
?>