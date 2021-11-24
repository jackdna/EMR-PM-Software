<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*
File: appt_page_functions.php
Purpose: page specific scheduler functions
Access Type: Include
*/

########################################################
###################### PAGE :: load_appt_hx.php
########################################################
function getOperatorInitialByUsername($strUsername){
	if(trim($strUsername) != ""){
		$strQry = "SELECT fname, lname FROM users WHERE username = '".$strUsername."'";
		$rsData = imw_query($strQry);
		$arrData = imw_fetch_assoc($rsData);
		return strtoupper(substr($arrData['fname'],0,1)).strtoupper(substr($arrData['lname'],0,1));
	}else{
		return "";
	}
}

function getServerName()
{
	$server_qry = sprintf("SELECT * FROM servers WHERE server='%s'",$GLOBALS["LOCAL_SERVER"]);
	$server_data_obj = imw_query($server_qry);
	$server_data = imw_fetch_assoc($server_data_obj);
	$server_name = trim($server_data["abbre"]);
	return $server_name;
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
function get_facility_name(){
	$arr_facility=$arr_fac_loca=array();
	$qry_facilty="SELECT f.id,f.name,s.abbre FROM `facility` f left join server_location s ON f.server_location=s.id";
	$res_facilty=imw_query($qry_facilty);
	while($row_facility=imw_fetch_assoc($res_facilty)){
		$facility_id=$row_facility['id'];
		$facility_name=$row_facility['name'];
		$server_name=$row_facility['abbre'];
		$arr_facility[$facility_id]=$facility_name;
		$arr_fac_loca[$facility_id]=$server_name;
	}
	return array($arr_facility,$arr_fac_loca);
}

########################################################
###################### PAGE :: insurance_active_case.php
########################################################

//Function To GET Insurance Case Name Information will return Name of case/////////////
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

function fnLineBrk($str){
	return str_replace(array("\r","\n"),array("\\r","\\n"),$str);
}

//Refferal Flag code
function getReferralFlagFrontdesk($type,$id,$reff_type){
		$qry = "select sum(patient_reff.no_of_reffs) as no_of_reffs,
				sum(patient_reff.reff_used) as reff_used,
				patient_reff.effective_date, patient_reff.end_date ,
				insurance_data.type, insurance_data.id 
				from patient_reff left join insurance_data on
				insurance_data.id = '$id'				
				where insurance_data.id = patient_reff.ins_data_id
				and insurance_data.referal_required = 'Yes'
				and insurance_data.provider > 0 
				and patient_reff.reff_type = '$type' and patient_reff.del_status = 0 and 
				(date_format(patient_reff.end_date,'%Y-%m-%d')>= '".date("Y-m-d")."'  OR date_format(patient_reff.end_date,'%Y-%m-%d')='0000-00-00') and date_format(patient_reff.effective_date,'%Y-%m-%d')!='0000-00-00' 
				";
							
		if(empty($reff_type) == false){
			$qry .= " and insurance_data.type = '$reff_type'";
		}
		$qry .= ' group by insurance_data.type ';
		$qry .= " order by patient_reff.end_date desc,patient_reff.reff_id desc					
				limit 0,1";
		$qryId = imw_query($qry);		
		if(imw_num_rows($qryId)>0){
			$qryRes = imw_fetch_array($qryId);
			$no_of_reffs = $qryRes["no_of_reffs"];
			$remaining_reffs = $qryRes["no_of_reffs"];
			$end_date = $qryRes["end_date"];
			$effective_date = $qryRes["effective_date"];
			$curdate = strtotime(date("Y-m-d"));
			$reff_used = $qryRes["reff_used"];
			//echo $end_date1.'----'.$curdate.'----'.$reff_used.'---'.$no_of_reffs;
			if($type == 1){
				if($end_date != "0000-00-00" && $end_date != "" && $end_date != NULL){
					$end_date1 = strtotime($end_date);
					if($end_date1 < $curdate || ($no_of_reffs<=0 && $reff_used >0)){
						$flag = 'red_flagn';
					}
					else if($no_of_reffs == 1 || $end_date1 == $curdate){
						$flag = 'yellow_flagn';
					}
					else{
						$flag = 'green_flagn';
					}
				}else if($effective_date != "0000-00-00" && $arr_ins["end_date"] == "0000-00-00"){
					if($no_of_reffs<=0 && $reff_used >0){
						$flag = 'red_flagn';
					}
					else if($no_of_reffs == 1){
						$flag = 'yellow_flagn';
					}
					else{
						$flag = 'green_flagn';
					}
				}
			}
			else if($type == 2){
				if($end_date != "0000-00-00" && $end_date != "" && $end_date != NULL){
					$end_date1 = strtotime($end_date);
					if($end_date1 < $curdate || ($no_of_reffs<=0 && $reff_used >0)){
						$flag = 'red_flagn';
					}
					else if($no_of_reffs == 1 || $end_date1 == $curdate){
						$flag = 'yellow_flagn';
					}
					else{
						$flag = 'green_flagn';
					}
				}else if($effective_date != "0000-00-00" && $arr_ins["end_date"] == "0000-00-00"){
					if($no_of_reffs<=0 && $reff_used >0){
						$flag = 'red_flagn';
					}
					else if($no_of_reffs == 1){
						$flag = 'yellow_flagn';
					}
					else{
						$flag = 'green_flagn';
					}
				}
			}
			else if($type == 3){
				if($end_date != "0000-00-00" && $end_date != "" && $end_date != NULL){
					$end_date1 = strtotime($end_date);
					if($end_date1 < $curdate || ($no_of_reffs<=0 && $reff_used >0)){
						$flag = 'red_flagn';
					}
					if($no_of_reffs == 1 || $end_date1 == $curdate){
						$flag = 'yellow_flagn';
					}
					else{
						$flag = 'green_flagn';
					}
				}else if($effective_date != "0000-00-00" && $arr_ins["end_date"] == "0000-00-00"){
					if($no_of_reffs<=0 && $reff_used >0){
						$flag = 'red_flagn';
					}
					if($no_of_reffs == 1){
						$flag = 'yellow_flagn';
					}
					else{
						$flag = 'green_flagn';
					}
				}
			}
		}		
		return $flag;
	}

######################################################
###################### PAGE :: base_day_scheduler.php
######################################################

/*iPortal patient registration alert*/
function iportal_demographics_changes_list($pid)
{
	$demo_row_exist=$med_row_exist=false;
	$iportal_ch_qry = "SELECT *, DATE_FORMAT(`dob`, '%m-%d-%Y') AS 'dob' FROM `iportal_register_patient` WHERE `approved`=0";
	$iportal_ch_obj = imw_query($iportal_ch_qry);
	$result_iportal_ch_html = '';
	$result_iportal_ocu_html= '';
	$chRowIdArr = array();
	if(imw_num_rows($iportal_ch_obj) > 0){
		
		while($iportal_ch_row = imw_fetch_assoc($iportal_ch_obj)){
			$data = "";
			$demo_row_exist=true;
			
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">First Name</span>&nbsp;&nbsp;'.$iportal_ch_row["fname"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">Last Name</span>&nbsp;&nbsp;'.$iportal_ch_row["lname"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">Email Id</span>&nbsp;&nbsp;'.$iportal_ch_row["email"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">DOB</span>&nbsp;&nbsp;'.$iportal_ch_row["dob"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">Sex</span>&nbsp;&nbsp;'.$iportal_ch_row["sex"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">Address</span>&nbsp;&nbsp;'.$iportal_ch_row["address"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">City</span>&nbsp;&nbsp;'.$iportal_ch_row["city"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">Zip</span>&nbsp;&nbsp;'.$iportal_ch_row["postal_code"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">Home Phone</span>&nbsp;&nbsp;'.$iportal_ch_row["phone_home"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">Cell Phone</span>&nbsp;&nbsp;'.$iportal_ch_row["phone_cell"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">Work Phone</span>&nbsp;&nbsp;'.$iportal_ch_row["phone_biz"]."<br />";
			$data .='<span style="width:120px;font-weight:bold;display:inline-block;">Work&nbsp;Phone&nbsp;Ext.</span>&nbsp;&nbsp;'.$iportal_ch_row["phone_biz_ext"]."<br />";
			
			$auth_status = ($iportal_ch_row['auth_status'])?'Verified':'Not Verified';
			$result_iportal_ch_html_pat.= '<tr><td>'.$data.'</td><td>'.$auth_status.'</td><td style="vertical-align:top;" id="iportal_approve_'.$iportal_ch_row["id"].'"><button class="dff_button" onClick="top.fmain.approve_operation('.$iportal_ch_row["id"].', this);">Approve</button> <button class="dff_button" onClick="top.fmain.disapprove_operation('.$iportal_ch_row["id"].', this);">Decline</button></td></tr>'; 
			$chRowIdArr[] = $iportal_ch_row["id"];
		}
		
		$result_iportal_ch_html = '<h1 style="font-size:20px;font-weight:normal;margin:0px;padding:0px;">The following patients have been registered through iPortal</h1><h2 style="font-size:14px;font-weight:normal;margin:5px 0px 10px 0px;padding:0px;"> Please take the appropriate action.</h2>';
		$result_iportal_ch_html.= '<div style="max-height:400px;overflow-y:auto;"> <table border="1" cellpadding="5" cellspacing="0" style="width:98%;" class="bg1"> <thead class="section_header"><tr><th width="">Patient Data</th><th width="130">Email Verification</th><th width="160">Action</th></tr></thead> <tbody id="newTbodyBorder"> ';
		if(core_check_privilege(array('priv_pt_fdsk'),'all')==true){
			$result_iportal_ch_html.=$result_iportal_ch_html_pat;	
		}
		if(core_check_privilege(array('priv_pt_clinical'),'all')==true && $result_iportal_ocu_html){
		}
		$div_return='';
		if(($demo_row_exist==true && core_check_privilege(array('priv_pt_fdsk'),'all')==true)|| ($med_row_exist==true && core_check_privilege(array('priv_pt_clinical'),'all')==true)){
			$div_return.='<div>';	
			$div_return.=str_ireplace("\\","",str_ireplace("\\r\\n",'',$result_iportal_ch_html));
			if(count($chRowIdArr)>0) {
				$chRowIdImplode = implode(",",$chRowIdArr);
				$div_return.='<input type="hidden" name="hidd_iportal_approve" id="hidd_iportal_approve" value="'.$chRowIdImplode.'">';	
			}
			$div_return.='</div>';
		}
		return array($div_return,$chRowIdImplode);
	}
	else
	{
		$result_iportal_ch_html = '';
	}
}

//CHECKING CL ORDERS FROM PATIENT PORTAL
function iportal_cl_orders_list(){
	$orderNumArr=array();
	$qry="Select cl_orders.*, pd.fname, pd.mname, pd.lname, DATE_FORMAT(cl_orders.ordered_date, '%m-%d-%Y') as 'orderedDate' 
	FROM iportal_req_orders cl_orders LEFT JOIN patient_data pd ON pd.id=cl_orders.patient_id 
	WHERE cl_orders.order_for='cl' AND cl_orders.is_approved='0' ORDER BY cl_orders.temp_order_num DESC, cl_orders.eye ASC";
	$rs=imw_query($qry);
	if(imw_num_rows($rs)>0){
		while($res=imw_fetch_assoc($rs)){
			$demo_row_exist=true;
			$id=$res['id'];
			$eye=$res['eye'];
			$patName=$res['lname'].', '.$res['fname'].' - '.$res['patient_id'];
			$ordNum=$res['temp_order_num'];
			$supplies=$res['supplies'].' Month';
			if($res['supplies']==12){ $supplies='1 Year'; }
			
			$td_date=$td_patient=$td_approval='';		
			if($eye=='OD'){
				$td_date='<td style="vertical-align:middle; text-align:center" rowspan="2">'.$res['orderedDate'].'</td>';
				$td_patient='<td style="vertical-align:middle; text-align:left" rowspan="2">'.$patName.'</td>';
				$td_approval='<td style="vertical-align:top;" id="iportal_cl_approve_'.$ordNum.'" rowspan="2"><button class="dff_button" onClick="window.top.fmain.approve_cl_operation('.$ordNum.', \'approve\');">Approve</button><button class="dff_button" onClick="window.top.fmain.approve_cl_operation('.$ordNum.', \'decline\');">Decline</button></td>';
			}
			
			$orderNumArr[$ordNum]=$ordNum;
			$result_iportal_ch_html_pat.='<tr>'.$td_patient.$td_date.'<td>'.$eye.'</td><td>'.$res['brand'].'</td><td>'.$res['manufacturer'].'</td><td>'.ucfirst($res['disposable']).'</td>';
			$result_iportal_ch_html_pat.='<td>'.$supplies.'</td><td>'.$res['boxes'].'</td>';
			$result_iportal_ch_html_pat.=$td_approval;
			$result_iportal_ch_html_pat.='</tr>'; 		
		}
		
		$result_iportal_ch_html='<h1 style="font-size:20px;font-weight:normal;margin:0px;padding:0px;">Following are contact lens orders from Patient Portal</h1>';
		$result_iportal_ch_html.='<h2 style="font-size:14px;font-weight:normal;margin:5px 0px 10px 0px;padding:0px;"> Please take the appropriate action.</h2>';
		$result_iportal_ch_html.='<div style="max-height:400px;overflow-y:auto;"><table border="1" cellpadding="5" cellspacing="0" style="width:98%;" class="bg1">';
		$result_iportal_ch_html.='<thead class="section_header"><tr>';
		$result_iportal_ch_html.='<th style="width:150px">Patient</th><th style="width:80px">Date</th><th style="width:30px">Eye</th><th style="width:135px">Brand</th><th style="width:135px">Manufacturer</th><th style="width:50px">Disposable</th>';
		$result_iportal_ch_html.='<th style="width:50px">Supplies</th><th>#Boxes</th><th>Action</th>';
		$result_iportal_ch_html.='</tr></thead><tbody id="newTbodyBorder">';

		if(core_check_privilege(array('priv_pt_fdsk'),'all')==true){
			$result_iportal_ch_html.=$result_iportal_ch_html_pat;	
		}
		$div_return='';
		if($demo_row_exist==true && core_check_privilege(array('priv_pt_fdsk'),'all')==true){
			$div_return.='<div>';	
			$div_return.=str_ireplace("\\","",str_ireplace("\\r\\n",'',$result_iportal_ch_html));
			if(count($orderNumArr)>0) {
				$orderNumImplode = implode(",",$orderNumArr);
				$div_return.='<input type="hidden" name="hidd_iportal_cl_approve" id="hidd_iportal_cl_approve" value="'.$orderNumImplode.'">';	
			}
			$div_return.='</div>';
		}
		return $div_return;		
		
	}else{
		$result_iportal_ch_html = '';
	}
}

#########################################################
###################### PAGE :: ajax_next_appointment.php
#########################################################

// getting facility and provider for given patient
function get_patient_profac($pid){
	$return=array();
	if($pid){$prov_id=$fac_id="";
		$qry_sch="SELECT sa_doctor_id,sa_facility_id from schedule_appointments where sa_patient_app_status_id!='18' and sa_patient_id='".$pid."' order by id desc LIMIT 0,1";
		$res_sch=imw_query($qry_sch);
		if(imw_num_rows($res_sch)>0){
			$row_sch=imw_fetch_assoc($res_sch);
			$prov_id=$row_sch['sa_doctor_id'];
			$fac_id=$row_sch['sa_facility_id'];
		}else{
			$qry_pat="SELECT providerID,(select id from facility where fac_prac_code=default_facility) as fac_id from patient_data where id='".$pid."' LIMIT 0,1";
			$res_pat=imw_query($qry_pat);
			$row_pat=imw_fetch_assoc($res_pat);
			$prov_id=$row_pat['providerID'];
			$fac_id=$row_pat['fac_id'];
		}
	}
	if($prov_id || $fac_id){
		$return['provider_id']=$prov_id;
		$return['facility_id']=$fac_id;
	}
	return $return;
}

//get custom label for given provider 
function get_custom_label($provider_id,$current_date,$date_c,$facility_ids){
	$arr_lbl=array();
	if($provider_id && ($current_date)){
		$qry_facility="";
		if($facility_ids){
			$qry_facility=" and facility IN(".$facility_ids.")";
		}
		list($ct_yy,$ct_mm,$ct_dd)=explode("-",$_REQUEST['current_date']);
		
		if($c_mm==date('m')){
			$current_date=date('Y-m-d');
		}
		$end_date_ct=$ct_yy."-".$ct_mm."-31";
		//create array for lock/block time 
		$qry_locked_lbl="SELECT * from block_times WHERE provider IN(".$provider_id.") and start_date>='".$current_date."' and start_date <='".$end_date_ct."'".$qry_facility;	
		$res_locked_lbl=imw_query($qry_locked_lbl);
		if(imw_num_rows($res_locked_lbl)>0){
			while($row_lckd_label=imw_fetch_assoc($res_locked_lbl)){
				$locked_provider_id=$locked_facility_id=$locked_date=$locked_time_st=$locked_time_nd='';
				$locked_provider_id=$row_lckd_label['provider'];
				$locked_facility_id=$row_lckd_label['facility'];
				$locked_date=$row_lckd_label['start_date'];
				$locked_time_st=strtotime($row_lckd_label['start_time']);
				$locked_time_nd=strtotime($row_lckd_label['end_time']);
				$lockedTime[$locked_date][$locked_provider_id][$locked_facility_id][]=array('start_time'=>$locked_time_st,'end_time'=>$locked_time_nd);
				//$lockedTime[$locked_date][$locked_provider_id][$locked_facility_id]['end_time']=$locked_time_nd;
			}
		}
		
		$qry_cstm_lbl="SELECT id,concat(date_format(start_time,'%H:%i'),'-',date_format(end_time,'%H:%i')) as label_id,l_color,facility,date_format(start_time,'%H:%i')as start_time,date_format(end_time,'%H:%i')as end_time,l_type,l_text,l_show_text,(select name from facility where id=facility) as fac_name,start_date, provider from scheduler_custom_labels WHERE system_action='0' and provider IN(".$provider_id.") and start_date>='".$current_date."' and start_date <='".$end_date_ct."'".$qry_facility;	
		$res_cstm_lbl=imw_query($qry_cstm_lbl);
		if(imw_num_rows($res_cstm_lbl)>0){
			while($row_c_label=imw_fetch_assoc($res_cstm_lbl)){
				
				if($lockedArr=$lockedTime[$row_c_label['start_date']][$row_c_label['provider']][$row_c_label['facility']])
				{
					foreach($lockedArr as $subLockArr)
					{
						$start_time=$subLockArr['start_time'];
						$end_time=$subLockArr['end_time'];
						$this_time=strtotime($row_c_label['start_time']);
						if($this_time>=$start_time && $this_time<=$end_time)continue 2;//skipp this record as it is in the block lock timing;
					}
				}
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['id']			=$row_c_label['label_id'];
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['timing']		=$row_c_label['label_id'];
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['status']		='on';
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['color']		=$row_c_label['l_color'];
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['label']		=$row_c_label['l_show_text'];
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['label_type']	=$row_c_label['l_type'];
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['tmpId']		='';
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['tmp_start_time']=$row_c_label['start_time'];
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['tmp_end_time']=$row_c_label['end_time'];
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['fac_id']		=$row_c_label['facility'];
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['fac_name']	=$row_c_label['fac_name'];
				$arr_lbl[$row_c_label['provider']][$row_c_label['start_date']][$row_c_label['label_id']]['entry']		='fresh';
			}
		}
	}
	return $arr_lbl;
}
//function to get label for given provider
function get_labels_val($provider_id,$current_date,$date_c,$facility_ids){
	$arr_ckh_lbl=array();
	if($provider_id && ($current_date)){
		$qry_facility="";
		if($facility_ids){
			$qry_facility=" and facility IN(".$facility_ids.")";
		}
		list($ct_yy,$ct_mm,$ct_dd)=explode("-",$_REQUEST['current_date']);
		
		if($c_mm==date('m')){
			$current_date=date('Y-m-d');
		}
		$end_date_ct=$ct_yy."-".$ct_mm."-31";
		$qry_cstm_lbl="SELECT concat(date_format(start_time,'%H:%i'),'-',date_format(end_time,'%H:%i')) as label_id,facility,start_date,labels_replaced,if(l_show_text='','all_lbl_filled',l_show_text) as lbl_show_text, provider, l_text, l_show_text  from scheduler_custom_labels WHERE provider IN(".$provider_id.") and start_date>='".$current_date."' and start_date <='".$end_date_ct."'".$qry_facility;	
		$res_cstm_lbl=imw_query($qry_cstm_lbl);
		while($row_cstm_lbl=imw_fetch_assoc($res_cstm_lbl)){
			$l_show_text =$row_cstm_lbl['l_show_text'];
			if($l_show_text!=""){
			$sch_detail_label=explode(":",$row_cstm_lbl['labels_replaced']);
			$key_slot_label=$row_cstm_lbl['label_id'];
			$facility_id=$row_cstm_lbl['facility'];
			$appt_date=$row_cstm_lbl['start_date'];
			$provider=$row_cstm_lbl['provider'];
			$l_text=$row_cstm_lbl['l_text']; 
			$arr_ckh_lbl[$provider][$appt_date][$facility_id][$key_slot_label]=$row_cstm_lbl['lbl_show_text'];
			}
		}
	}
	return $arr_ckh_lbl;
}

function get_labels_val_new($provider_id,$current_date,$date_c,$facility_ids){
	$arr_ckh_lbl=array();
	if($provider_id && ($current_date)){
		$qry_facility="";
		if($facility_ids){
			$qry_facility=" and facility IN(".$facility_ids.")";
		}
		list($ct_yy,$ct_mm,$ct_dd)=explode("-",$_REQUEST['current_date']);
		
		if($c_mm==date('m')){
			$current_date=date('Y-m-d');
		}
		$end_date_ct=$ct_yy."-".$ct_mm."-31";
		$qry_cstm_lbl="SELECT concat(date_format(start_time,'%H:%i'),'-',date_format(end_time,'%H:%i')) as label_id,facility,start_date,labels_replaced,if(l_show_text='','all_lbl_filled',l_show_text) as lbl_show_text, provider, l_text, l_show_text  from scheduler_custom_labels WHERE provider IN(".$provider_id.") and start_date>='".$current_date."' and start_date <='".$end_date_ct."'".$qry_facility;	
		$res_cstm_lbl=imw_query($qry_cstm_lbl);
		while($row_cstm_lbl=imw_fetch_assoc($res_cstm_lbl)){
			$sch_detail_label=explode(":",$row_cstm_lbl['labels_replaced']);
			$key_slot_label=$row_cstm_lbl['label_id'];
			$facility_id=$row_cstm_lbl['facility'];
			$appt_date=$row_cstm_lbl['start_date'];
			$provider=$row_cstm_lbl['provider'];
			$l_text=$row_cstm_lbl['l_text']; 
			$l_show_text =$row_cstm_lbl['l_show_text'];
			
			$arr_ckh_lbl[$provider][$appt_date][$facility_id][$key_slot_label]['l_text']=$l_text;
			
			$arr_ckh_lbl[$provider][$appt_date][$facility_id][$key_slot_label]['l_show_text']=$l_show_text;
		}
	}
	return $arr_ckh_lbl;
}
function get_block_time($start_date_block){
	$arr_block_time=array();
	$qry_block="select b_desc from block_times where start_date>='".$start_date_block."'";
	$res_block=imw_query($qry_block);
	while($row_block=imw_fetch_assoc($res_block)){
		if(trim($row_block['b_desc'])){
			$arr_block_time[strtolower($row_block['b_desc'])]=strtolower($row_block['b_desc']);
		}
	}
	return $arr_block_time;
}
function get_block_time_in_day($from_time,$to_time,$time_slot="10"){
	$arr_block_time_of_day=array();
	$start = new DateTime($from_time);
	$end = new DateTime($to_time);
	$current = clone $start;
	while ($current <= $end) {
		$arr_block_time_of_day[]=$current->format("H:i");
		$current->modify("+".$time_slot." minutes");
	}
	return $arr_block_time_of_day;	
}
function get_lunch_time(){
	$arr_sch_lunch=array();
	$qrysch_tmp="select id,concat(date_format(fldLunchStTm,'%H:%i'),'-',date_format(`fldLunchEdTm`,'%H:%i')) as lunch_time from schedule_templates Where `fldLunchStTm`!='00:00:00'";
	$ressch_tmp=imw_query($qrysch_tmp);
	while($rowsch_tmp=imw_fetch_assoc($ressch_tmp)){
		$arr_sch_lunch[$rowsch_tmp['id']]=$rowsch_tmp['lunch_time'];
	}
	return  $arr_sch_lunch;
}
function get_provider(){
	$array_user=array();
	$qry_user="SELECT id,if(mname!='',concat(lname,', ',fname,' ',substr(mname,1,1)),concat(lname,', ',fname)) as user_name from users where delete_status=0 and fname!='' and lname!=''";
	$res_user=imw_query($qry_user);
	while($row_user=imw_fetch_assoc($res_user)){
		$array_user[$row_user['id']]=$row_user['user_name'];
	}
	return $array_user;
}

function getTime($time,$i)
{
	$addTime=DEFAULT_TIME_SLOT*$i;	
	list($hour,$minute)=explode(":",$time);
	$newTime=date("H:i", mktime($hour, $minute+$addTime));
	return $newTime;
}

function getEndTime($time,$i)
{
	$addTime=DEFAULT_TIME_SLOT*$i;	
	list($hour,$minute)=explode(":",$time);
	$newTime=date("h:i A", mktime($hour, $minute+$addTime));
	return $newTime;
}

#########################################################
###################### PAGE :: load_reasons.php
#########################################################

/*
Purpose: to load reasons in a hidden div
Author: Ravi Kaushal, Prabh
Returns: ARRAY with provider ids / STRING select options DEPENDING UPON THE calling parameter retury_type = "ARRAY" / "OPTIONS"
*/
function load_reasons($return_type = "ARRAY"){
	$return = false;
	$qry = "SELECT reason_name FROM reason_list ORDER BY reason_id";
	$res = imw_query($qry);
	if(imw_num_rows($res) > 0){
		while($tmp_data=imw_fetch_assoc($res))
		{
			$arr[]=$tmp_data;	
		}
		$arr_return = array();
		$str_return = "";
		$int_cnt = 0;
		for($f = 0; $f < count($arr); $f++){
			//options
			$str_return .= "<option value=\"".$arr[$f]["reason_name"]."\">".$arr[$f]["reason_name"]."</option>";
			
			//array
			$arr_return[$int_cnt]["name"] = $arr[$f]["reason_name"];
			$int_cnt++;
		}
		if($return_type == "ARRAY"){
			$arr_return[$int_cnt]["name"] = "Other";
			$return = $arr_return;
		}else if($return_type == "OPTIONS"){
			$str_return .= "<option value=\"Other\">Other</option>";
			$return = $str_return;
		}
	}
	return $return;
}


function weeks_in_month($month, $year) {
 // Start of month
 $start = mktime(0, 0, 0, $month, 1, $year);
 // End of month
 $end = mktime(0, 0, 0, $month, date('t', $start), $year);
 // Start week
 $start_week = date('W', $start);
 // End week
 $end_week = date('W', $end);
 
 if ($end_week < $start_week) { // Month wraps
   return ((52 + $end_week) - $start_week) + 1;
 }
 
 return ($end_week - $start_week) + 1;
}

function getDatesForWK($m,$y)
{
	$wk=1;
	$totalDays = cal_days_in_month(CAL_GREGORIAN, $m, $y);
	
	for($day=1;$day<=$totalDays;$day++)
	{	
		$newDate=date('d',mktime(0,0,0,$m,$day,$y));
		
		if(date('D', mktime(0,0,0,$m,$day,$y))=='Mon' && $day!=1)
		{
			$wk++;
		}
		$weekArr[$wk].=$newDate.", ";
	}
	return $weekArr;
}

#########################################################
###################### PAGE :: to_do_first_avai.php
#########################################################

/*create date array according to year-month-week for 1 year*/
function week_array()
{
	for($month=0;$month<12;$month++)
	{
		$date='';
		unset($weekArr);
		$date=explode('-',date('m-Y',mktime(0,0,0,(date('m')+$month),1,date('Y'))));
		$weekArr=getDatesForWK($date[0],$date[1]);
		foreach($weekArr as $wk=>$dates)
		{
			unset($dateArray,$sbstr);
			$dates=trim($dates);
			//$dates=$date[1].'-'.$date[0].'-'.$dates;
			$sbstr=substr($dates,0,strlen($dates)-1);
			$sbarr=explode(',',$sbstr);
			foreach($sbarr as $dts)
			{
				$dateArray[]=$date[1].'-'.$date[0].'-'.trim($dts);
			}
			$dateArr[$date[1].'-'.$date[0].'-'.$wk]=$dateArray;
		}
	}
	
	return $dateArr;
}

/* function to get patient desired time for 'first available'*/
function desireTime($id,$field_name,$typ,$selField='')
{
	if($id && $typ=='detail')
	{
		//get detail of desired datetime from schedule_first_avail if any
		$qry_to_do_data="select sel_year,sel_month,sel_week,sel_time,sch_id from schedule_first_avail where $field_name=$id and sel_week<>0";
		$desired_data_res=imw_query($qry_to_do_data)or die(imw_error());
		if(imw_num_rows($desired_data_res)>=1)
		{
			$row_data=imw_fetch_assoc($desired_data_res);
			$month_name=($row_data['sel_month'])?date('M',mktime(0,0,0,$row_data['sel_month'],01,0)):0;
			return $dStr='Week '.$row_data['sel_week'].', '.$month_name." - $row_data[sel_year] in $row_data[sel_time]";	
		}
	}elseif($id && $typ=='other')
	{
		//get detail of desired datetime from schedule_first_avail if any
		$qry_to_do_data="select $selField as fie from schedule_first_avail where $field_name=$id and sel_week<>0";
		$desired_data_res=imw_query($qry_to_do_data)or die(imw_error());
		if(imw_num_rows($desired_data_res)>=1)
		{
			$row_data=imw_fetch_assoc($desired_data_res);
			return $row_data['fie'];	
		}
	}
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
	$pat_dts[10]=$label_row['title'];
	$pat_dts[11]=$label_row['fname'];
	$pat_dts[12]=$label_row['mname'];
	$pat_dts[13]=$label_row['lname'];
	$pat_dts[14]=$label_row['state']." ".$label_row['postal_code'];
	$pat_dts[15]=$label_row['hipaa_mail'];
	$pat_dts[16]=$label_row['hipaa_email'];
	$pat_dts[17]=$label_row['hipaa_voice'];
	$pat_dts[18]=$label_row['phone_home'];	//HOME PHONE
	$pat_dts[19]=$label_row['phone_biz'];   //WORK PHONE
	$pat_dts[20]=$label_row['phone_cell'];  //MOBILE PHONE
	$pat_dts[21]=$label_row['External_MRN_1'];  //PATIENT MRN
	$pat_dts[22]=$label_row['External_MRN_2'];  //PATIENT MRN2
	
	$raceShow				= trim($label_row["race"]);
	$otherRace				= trim($label_row["otherRace"]);
	if($otherRace) { 
		$raceShow			= $otherRace;
	}
	$language				= str_ireplace("Other -- ","",$label_row["language"]);
	$ethnicityShow			= trim($label_row["ethnicity"]);			
	$otherEthnicity			= trim($label_row["otherEthnicity"]);
	if($otherEthnicity) { 
		$ethnicityShow		= $otherEthnicity;
	}
	
	$pat_dts[23]=$raceShow;  //RACE
	$pat_dts[24]=$language;  //LANGUAGE
	$pat_dts[25]=$ethnicityShow;  //ETHNICITY
	$pat_dts[26]=$label_row['default_facility'];
	$pat_dts[27]=$label_row['email'];
	$pat_dts[28]=$label_row['phone_home'];
	$pat_dts[29]=$label_row['phone_cell'];
	$pat_dts[30]=$label_row['phone_biz'];
	$pat_dts[31]=$label_row['temp_key'];
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

	$operator=substr($vrs_tcurr1['lname'],0,1);
	if($vrs_tcurr1['fname']<>"" && $vrs_tcurr1['lname']<>""){
		$operator=substr($vrs_tcurr1['fname'],0,1)."".substr($vrs_tcurr1['lname'],0,1);
	}
	else if($vrs_tcurr1['fname']<>"" && $vrs_tcurr1['lname'] == ""){
		$operator=substr($vrs_tcurr1['fname'],0,1);
	}else{
			$operator=substr(ucfirst($madeby),0,1);
		}
	
	$sch_det=array($procedure,$provider_name,$operator,$provider_id,$st_id,$fac_id,$sa_commentsAndReason);
	return $sch_det;
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


#########################################################
###################### PAGE :: to_do.php
#########################################################
function getActiveInsId($type,$patientId){
	$qry = "select insurance_data.id from insurance_data join patient_reff
			on insurance_data.id = patient_reff.ins_data_id
			join insurance_case on insurance_case.ins_caseid = insurance_data.ins_caseid
			where insurance_case.case_status = 'Open'
			and patient_reff.reff_type = '$type' and insurance_data.pid = '$patientId'
			and patient_reff.patient_id = insurance_data.pid
			and insurance_data.referal_required = 'Yes'
			and insurance_data.actInsComp = 1
			order by insurance_case.ins_case_type";
	$qryId = imw_query($qry);			
	if(imw_num_rows($qryId)>0){
		$qryRes = imw_fetch_object($qryId);		
	}
	return $qryRes;
}

function getReferralFlag($type,$id){
	$qry = "select patient_reff.no_of_reffs,patient_reff.end_date,
			insurance_data.actInsComp,insurance_data.type 
			from patient_reff left join insurance_data on
			insurance_data.id = '$id'				
			where insurance_data.id = patient_reff.ins_data_id
			and insurance_data.referal_required = 'Yes'
			and patient_reff.reff_type = '$type'
			and  ((patient_reff.end_date >= current_date() and 
			patient_reff.effective_date <= current_date())
			or(patient_reff.no_of_reffs > 0))
			order by patient_reff.end_date desc,patient_reff.reff_id desc,
			insurance_data.actInsComp desc
			limit 0,1";
	//print $qry;
	$qryId = imw_query($qry);			
	if(imw_num_rows($qryId)>0){
		$qryRes = imw_fetch_object($qryId);
		$no_of_reffs = $qryRes->no_of_reffs;
		$actInsComp = $qryRes->actInsComp;
		$date = false;
		if($qryRes->end_date != '0000-00-00'){
			$curdate = strtotime(date('Y-m-d'));
			$end_date = strtotime($qryRes->end_date);
			if($end_date == $curdate){
				$date = false;
			}
			else{
				$date = true;
			}
		}
		else{
			$date = true;
		}
		if($qryRes->type == 'primary'){
			$priFlag = false;
			if($no_of_reffs == 1 || $date == false){
				$priFlag = true;
				$flag = 'yellow_flagn';
			}
			else{
				$flag = 'green_flagn';
			}
			if($priFlag == false){
				if($actInsComp == 0){
					$flag = 'red_flagn';
				}
			}
		}
		if($qryRes->type == 'secondary'){
			$secFlag = false;
			if($no_of_reffs == 1 || $date == false){
				$secFlag = true;
				$flag = 'yellow_flagn';
			}
			else{
				$flag = 'green_flagn';
			}
			if($secFlag == false){
				if($actInsComp == 0){
					$flag = 'red_flagn';
				}
			}
		}
		if($qryRes->type == 'tertiary'){
			$terFlag = false;
			if($no_of_reffs == 1 || $date == false){
				$terFlag = true;
				$flag = 'yellow_flagn';
			}
			else{
				$flag = 'green_flagn';
			}
			if($terFlag == false){
				if($actInsComp == 0){
					$flag = 'red_flagn';
				}
			}
		}
	}
	return $flag;
}

//this function is being used in add recal from scheduler page
//already in common function file
/*function getProcedureName($id){
	$qry="select proc,acronym from slot_procedures where id=$id";
	$r=imw_query($qry);
	if(imw_num_rows($r)>0){
		list($proc,$acronym)=imw_fetch_array($r);
	}
	return $proc;
}*/
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
/*
FILE: schedule_functions.php
PURPOSE: For {LAST DOS} Vocabulary in Recall Letter
ACCESS TYPE: Include
*/
function get_vocab_lastdos($id){
	 $qry="select date_format(date_of_service,'".get_sql_date_format()."') as dos from `chart_master_table` where patient_id='".$id ."' order by date_of_service desc";
	$r=imw_query($qry);
	if(imw_num_rows($r)>0){
		$row_d=imw_fetch_assoc($r);
		$last_dos=$row_d['dos'];
	}
	return $last_dos;
}
?>