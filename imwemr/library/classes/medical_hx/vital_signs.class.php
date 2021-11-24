<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> Vital sign class
 Access Type: Indirect Access.
 
*/

include_once $GLOBALS['srcdir'].'/classes/CLSAlerts.php';
include_once $GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php';
$cls_review  = new CLSReviewMedHx();
$cls_alerts = new CLSAlerts;
$core = new core_notifications();

class Vital_Sign extends MedicalHistory
{
	//Public variables
	public $vital_dis_arr = array();
	public $arrPain = array();
	public $vs_id_arr = array();
	public $heading_row_display = true;
	public $pkIdAuditTrail = '';
	public $arr_vs = array();
	
	public $bmi_data = array("","BMI is documented within normal parameters","BMI is documented above normal parameters and a follow-up plan is documented","BMI is documented below normal parameters and a follow-up plan is documented","BMI not documented and no reason is given","BMI documented outside normal parameters, no follow-up plan documented");
	public $bmiSelected = 0;
	
	public $bp_data = array("","Normal BP reading documented, follow-up not required","Hypertensive BP reading documented, AND the indicated follow-up is documented","Patient not eligible - known hypertensive","BP reading not documented, reason not given","Hypertensive BP reading documented, indicated follow-up not documented, reason not given");
	public $bpSelected = 0;
	
	
	
	
	public function __construct($tab = 'ocular'){
		parent::__construct($tab);
		$this->vital_vocabulary = $this->get_vocabulary("medical_hx", "vs");
		//Set pain array
		$this->arrPain = array("","Mild","Moderate","Severe");
		for($counter = 0; $counter <= 10; $counter++){
			array_push($this->arrPain,"Scale ".$counter);
		}
		
		//Set Vital sign array
		$sql = "SELECT id,vital_sign FROM vital_sign_limits";
		$res_vsl = imw_query($sql);
		while($row_vsl = imw_fetch_assoc($res_vsl)){
			$id = $row_vsl['id'];
			$vs = $row_vsl['vital_sign'];
			$this->arr_vs[$id] = $vs;
		}
	}
	
	//Delete vital signs
	public function delete_vital_sign($request){
		global $core;
		global $cls_review;
		$counter = 0;
		$sql = "update vital_sign_master set status = '1'
				where patient_id = '".$this->patient_id."' and id = '".$request['del_id']."'";
		imw_query($sql);
		//$counter = ($counter+imw_affected_rules());
		$counter = ($counter+imw_affected_rows());
		$core->update_vitalSign_status();
		
		if($this->policy_status == 1){
			$qry = "select * from vital_sign_master where id =".$request['del_id'];
			$res = imw_query($qry);
			$row_vsm = imw_fetch_assoc($res);
			$reviewImmArr = array();
			$reviewImmArr[0]['Pk_Id'] = $del_id;
			$reviewImmArr[0]['Table_Name'] = 'vital_sign_master';
			$reviewImmArr[0]['Field_Text'] = 'Patient Vital Sign';
			$reviewImmArr[0]['Operater_Id'] = $_SESSION['authId'];
			$reviewImmArr[0]['Action'] = 'delete';
			$reviewImmArr[0]['Old_Value'] = $row_vsm['date_vital'];
			//CLSReviewMedHx::reviewMedHx($reviewImmArr,$_SESSION['authId'],"Vital Sign",$this->patient_id,0,0);
			$cls_review->reviewMedHx($reviewImmArr,$_SESSION['authId'],"Vital Sign",$this->patient_id,0,0);
		}
		return $counter;
	}
	
	//Reviews logged user vital signs
	public function review_logged_user_vs($logged_usr_id){
		global $core;
		$rvw_date = date('Y-m-d H:i:s');
		$rvw_query = "UPDATE vital_sign_master SET phy_reviewed = '".$logged_usr_id."', phy_reviewed_date = '".$rvw_date."' 
					  WHERE phy_reviewed='0' AND phy_reviewed_date='0000-00-00 00:00:00' AND status='0'";
		$rvw_result = imw_query($rvw_query);
		$core->update_vitalSign_status();
	}
	
	//Get single patient vital sign
	public function get_single_pat_vs(){
		$vs_data_arr = array();
		$sql = imw_query("select id,form_id,date_vital,created_on,time_vital,comment,bp_type
			 from vital_sign_master where patient_id = '$this->patient_id' and status != '1' order by date_vital desc");
		while($row = imw_fetch_array($sql)){
			$id = $row['id'];
			$this->vs_id_arr[] = $id;
			
			$dataArr = array();
			$dataArr['vital_id'] = $row['id'];
			$dataArr['date_vital'] = get_date_format($row['date_vital']);
			$dataArr['form_id'] = $row['form_id'];
			$dataArr['created_on'] = $row['created_on'];
			$dataArr['time_vital'] = $row['time_vital'];
			$dataArr['comment'] = $row['comment'];
			
			
			//-------BMI COMMENT DROP DOWN-------
			$bmiDropData = '';
			foreach($this->bmi_data as $obj){
				$sel = ($obj == $row['comment'] && $row['comment']!=='') ? 'selected="selected"' : '';
				$bmiDropData .= "<option value='$obj' $sel>$obj</option>";
			}
			
			$dataArr['comment'] = $bmiDropData;
			//-------BP COMMENT DROP DOWN-------
			$bpDropData = '';
			foreach($this->bp_data as $obj){
				$sel = ($obj == $row['bp_type'] && $row['bp_type']!=='') ? 'selected="selected"' : '';
				$bpDropData .= "<option value='$obj' $sel>$obj</option>";
			}
			$dataArr['bp_type'] = $bpDropData;
			
			$vs_data_arr[$id] = $dataArr;
			
			//--- AUDIT TRAIL ID ---
			$this->pkIdAuditTrail .= $row['id'].'-';
			if($pkIdAuditTrailID == ''){
				$pkIdAuditTrailID = $row['id'];
			}
		}
		//$vs_data_arr['pkIdAuditTrail'] = $this->pkIdAuditTrail;
		//$vs_data_arr['pkIdAuditTrailID'] = $pkIdAuditTrailID;
		return $vs_data_arr;
	}
	
	//Get all vital sign details
	public function get_vs_details(){
		$detail_data = array();
		$vs_id_str = join(',',$this->vs_id_arr);
		$sql = imw_query("select id,range_vital,unit,range_status,vital_sign_id,vital_master_id,inhale_O2
				from vital_sign_patient where vital_master_id in ($vs_id_str)");
		while($row = imw_fetch_array($sql)){
			$vital_master_id = $row['vital_master_id'];
			$dataArr = array();
			$dataArr['id'] = $row['id'];
			$dataArr['range_vital'] = $row['range_vital'];
			$dataArr['unit'] = $row['unit'];
			$dataArr['range_status'] = $row['range_status'];
			$dataArr['vital_sign_id'] = $row['vital_sign_id'];
			$dataArr['inhale_O2'] = $row['inhale_O2'];
			
			$detail_data[$vital_master_id][] = $dataArr;
		}
		return $detail_data;
	}
	
	//Merging vital sign array
	public function merge_vital_sign_arr($vs_arr_1,$vs_arr_2){
		$vs_display_data = array();
		foreach($vs_arr_1 as $key => $val){
			$dataArr = $val;
			$vitalDataArr = array();
			if(count($vs_arr_2[$key]) > 0){
				$vitalDataArr = $vs_arr_2[$key];
			}
			foreach($vitalDataArr as $obj){
				$data_arr = $obj;
				if($data_arr['vital_sign_id'] == 1){
					$dataArr['VS_SYS'] = $data_arr['range_vital'];
					$dataArr['VS_SYS_RANGE'] = $data_arr['range_status'];
					$dataArr['VS_SYS_ID'] = $data_arr['id'];
					$dataArr['VS_SYS_UNIT'] = $data_arr['unit'];
					
					if($data_arr['range_status']=='out'){
						$dataArr['VS_SYS_CLR'] = 'color:#FF0000 !important';	
					}
				}
				else if($data_arr['vital_sign_id'] == 2){
					$dataArr['VS_DIS'] = $data_arr['range_vital'];
					$dataArr['VS_DIS_RANGE'] = $data_arr['range_status'];
					$dataArr['VS_DIS_ID'] = $data_arr['id'];
					$dataArr['VS_DIS_UNIT'] = $data_arr['unit'];
					
					if($data_arr['range_status']=='out'){
						$dataArr['VS_DIS_CLR'] = 'color:#FF0000 !important';	
					}
				}
				else if($data_arr['vital_sign_id'] == 3){
					$dataArr['VS_PULSE'] = $data_arr['range_vital'];
					$dataArr['VS_PULSE_RANGE'] = $data_arr['range_status'];
					$dataArr['VS_PULSE_ID'] = $data_arr['id'];
					$dataArr['VS_PULSE_UNIT'] = $data_arr['unit'];
					
					if($data_arr['range_status']=='out'){
						$dataArr['VS_PULSE_CLR'] = 'color:#FF0000 !important';	
					}
				}
				else if($data_arr['vital_sign_id'] == 4){
					$dataArr['VS_RESP'] = $data_arr['range_vital'];
					$dataArr['VS_RESP_RANGE'] = $data_arr['range_status'];
					$dataArr['VS_RESP_ID'] = $data_arr['id'];
					$dataArr['VS_RESP_UNIT'] = $data_arr['unit'];
					
					if($data_arr['range_status']=='out'){
						$dataArr['VS_RESP_CLR'] = 'color:#FF0000 !important';	
					}
				}
				else if($data_arr['vital_sign_id'] == 5){
					$dataArr['VS_O2SAT'] = $data_arr['range_vital'];
					$dataArr['VS_O2SAT_RANGE'] = $data_arr['range_status'];
					$dataArr['VS_O2SAT_ID'] = $data_arr['id'];
					$dataArr['VS_O2SAT_UNIT'] = $data_arr['unit'];
					
					if($data_arr['range_status']=='out'){
						$dataArr['VS_O2SAT_CLR'] = 'color:#FF0000 !important';	
					}
				}
				else if($data_arr['vital_sign_id'] == 6){
					$dataArr['VS_TEMP'] = $data_arr['range_vital'];
					$dataArr['VS_TEMP_RANGE'] = $data_arr['range_status'];
					
					if($data_arr['range_status']=='out'){
						$dataArr['VS_TEMP_CLR'] = 'color:#FF0000 !important';	
					}
					$dataArr['VS_TEMP_ID'] = $data_arr['id'];
					$dataArr['VS_TEMP_UNIT'] = $data_arr['unit'];
					//echo str_ireplace("Â","",$data_arr['unit']);die();
					$unit_val=str_ireplace("Â","",$data_arr['unit']);
					$unit_val = htmlspecialchars($unit_val);
					$unit_val = strtolower(substr($unit_val,-1));
					if($unit_val == "f"){
						$dataArr['VS_TEMP_F'] = 'selected="selected"';
					}
					else if($unit_val == "c"){
						$dataArr['VS_TEMP_C'] = 'selected="selected"';
					}
				}
				else if($data_arr['vital_sign_id'] == 7){
					$dataArr['VS_HEIGHT'] = $data_arr['range_vital'];
					$dataArr['VS_HEIGHT_RANGE'] = $data_arr['range_status'];
					if($data_arr['range_status']=='out'){
						$dataArr['VS_HEIGHT_CLR'] = 'color:#FF0000 !important';	
					}
					$dataArr['VS_HEIGHT_ID'] = $data_arr['id'];
					$dataArr['VS_HEIGHT_UNIT'] = $data_arr['unit'];			
					if($data_arr['unit'] == 'inch'){
						$dataArr['VS_HEIGHT_INCH'] = 'selected="selected"';
					}
					else if($data_arr['unit'] == 'm'){
						$dataArr['VS_HEIGHT_M'] = 'selected="selected"';
					}
					else if($data_arr['unit'] == 'cm'){
						$dataArr['VS_HEIGHT_CM'] = 'selected="selected"';
					}
					
				}
				else if($data_arr['vital_sign_id'] == 8){
					$dataArr['VS_WIEGHT'] = $data_arr['range_vital'];
					$dataArr['VS_WIEGHT_RANGE'] = $data_arr['range_status'];
					if($data_arr['range_status']=='out'){
						$dataArr['VS_WIEGHT_CLR'] = 'color:#FF0000 !important';	
					}
					$dataArr['VS_WIEGHT_ID'] = $data_arr['id'];
					$dataArr['VS_WIEGHT_UNIT'] = $data_arr['unit'];			
					if($data_arr['unit'] == 'lbs'){
						$dataArr['VS_WIEGHT_LBS'] = 'selected="selected"';
					}
					else if($data_arr['unit'] == 'kg'){
						$dataArr['VS_WIEGHT_KG'] = 'selected="selected"';
					}
					
				}
				else if($data_arr['vital_sign_id'] == 9){
					$dataArr['VS_BMI'] = $data_arr['range_vital'];
					$dataArr['VS_BMI_RANGE'] = $data_arr['range_status'];
					if($data_arr['range_status']=='out'){
						$dataArr['VS_BMI_CLR'] = 'color:#FF0000 !important';	
					}
					$dataArr['VS_BMI_ID'] = $data_arr['id'];
					$dataArr['VS_BMI_UNIT'] = $data_arr['unit'];
					
				}
				else if($data_arr['vital_sign_id'] == 10){
					$painDropData = '';
					foreach($this->arrPain as $key => $val){
						$sel = $key == $data_arr['range_vital'] ? 'selected="selected"' : '';
						$painDropData .= "<option value='$key' $sel>$val</option>";
					}
					$dataArr['VS_PAIN'] = $painDropData;
					$dataArr['VS_PAIN_RANGE'] = $data_arr['range_status'];
					if($data_arr['range_status']=='out'){
						$dataArr['VS_PAIN_CLR'] = 'color:#FF0000 !important';	
					}

					$dataArr['VS_PAIN_ID'] = $data_arr['id'];
					$dataArr['VS_PAIN_UNIT'] = $data_arr['unit'];
					
				}
                else if($data_arr['vital_sign_id'] == 11){
					$dataArr['inhale_O2'] = $data_arr['inhale_O2'];
				}
			}
			
			$vs_display_data[] = $dataArr;
		}
		return $vs_display_data;
	}
	
	//Returns vital sign field status arr
	public function get_vs_field_status(){
		$sql = imw_query("select status,vital_sign from vital_sign_limits");
		while($row = imw_fetch_array($sql)){
			$vital_sign = trim($row['vital_sign']);
			$status = $row['status'];
			if($status == 0){
				switch($vital_sign){
					case 'B/P - Systolic':
						$this->vital_dis_arr['BP_SYS'] = true;
					break;
					case 'B/P - Diastolic':
						$this->vital_dis_arr['BP_DIS'] = true;
					break;
					case 'pulse':
						$this->vital_dis_arr['PULSE'] = true;
					break;
					case 'Respiration':
						$this->vital_dis_arr['RESP'] = true;
					break;
					case 'O2Sat':
						$this->vital_dis_arr['O2SAT'] = true;
					break;
					case 'Temperature':
						$this->vital_dis_arr['TEMP'] = true;
					break;
					case 'Height':
						$this->vital_dis_arr['HEIGHT'] = true;
					break;
					case 'Weight':
						$this->vital_dis_arr['WIEGHT'] = true;
					break;
					case 'BMI':
						$this->vital_dis_arr['BMI'] = true;
					break;
					case 'Pain':
						$this->vital_dis_arr['PAIN'] = true;
					break;
					case 'InhaleO2':
						$this->vital_dis_arr['InhaleO2'] = true;
					break;
				}
			}
		}
		return $this->vital_dis_arr;
	}
	
	//Returns comment box size 
	public function get_comment_box_size(){
		$comment_size = 10;
		$dis_arr_size = count($this->vital_dis_arr);
		switch($dis_arr_size){
			case 1:
				$comment_size = 20;
			break;
			case 2:
				$comment_size = 30;
			break;
			case 3:
				$comment_size = 40;
			break;
			case 4:
				$comment_size = 60;
			break;
			case 5:
				$comment_size = 70;
			break;
			case 6:
				$comment_size = 80;
			break;
			case 7:
				$comment_size = 100;
			break;
			case 8:
				$comment_size = 110;
			break;
			case 9:
				$comment_size = 120;
			break;
			case 10:
				$this->heading_row_display = false;
			break;
		}
		
		$return_arr['comment_size'] = $comment_size;
		$return_arr['heading_row_display'] = $this->heading_row_display;
		return $return_arr;
	}
	
	//Get graph data
	public function get_graph_data($request){
		$id = $this->patient_id;
		$sel_pat = imw_query("select DOB from patient_data where id='$id'");
		$fet_pat = imw_fetch_array($sel_pat);
		$pat_dob = $fet_pat['DOB'];
		$today_dat = date('Y-m-d');
		$higher_dob = $this->getAge_cal($today_dat,$pat_dob);
		if($higher_dob<10){
			$higher_dob=10;
		}
		if($higher_dob<10){
			$higher_dob=10;
			$dob_gap=1;
		}else{
			$dob_gap=$higher_dob/10;
		}
		$dob_gap=round($dob_gap);
		
		if($request['patient']<>'' && $request['sign_id']<>''){
			$sign_id = $request['sign_id'];
			$patient = $request['patient'];
			
			//Fetching section name
			$sel_name = imw_query("select vital_sign 
								 from vital_sign_limits where id='$sign_id'");
			$fet_name = imw_fetch_array($sel_name); 
			$name = ucfirst($fet_name['vital_sign']);			
			
			$sel_vital = imw_query("select id,form_id,date_vital,time_vital,DATE_FORMAT(date_vital,'%m-%d-%Y') as vs_sign_date from vital_sign_master where patient_id='$patient' and status!='1' order by date_vital asc");						 
			$shw_dat_final=array();
			$graph_vals = array();
			$series_arr = array();
			while($row_vital=imw_fetch_array($sel_vital)){
				$id_final=$row_vital['id'];
				$shw_time=$row_vital['time_vital'];
				$shw_dat=explode('-',$row_vital['date_vital']);
				
				$tmpAg = $this->getAge_cal($row_vital['date_vital'],$pat_dob);
				if($tmpAg == ""){
					$tmpAg = "0";
				}
				$sel_vital1=imw_query("select range_vital,unit from vital_sign_patient where vital_master_id='$id_final' and vital_sign_id='$sign_id' and range_vital<>''");
				if(imw_num_rows($sel_vital1)>0){
					
					$shw_dat_final[]=$shw_dat[1].'-'.$shw_dat[2].'-'.substr($shw_dat[0],2).' ('.$tmpAg.' y)';
					while($row_vital1=imw_fetch_array($sel_vital1)){
						
						if($row_vital1['unit']=='kg'){
							$range = round($row_vital1['range_vital']*2.204625);
						}else if($row_vital1['unit']=='inch'){
							$range = $row_vital1['range_vital']*0.0254;
						}else{
							$range = $row_vital1['range_vital'];
						}
						
						$series_arr[$name] = $tmpAg;
						$series_arr['Age'] = $range;
						$series_arr['date'] = $row['vs_sign_date'];
						
						$graph_vals[] = $series_arr;
						if($row_vital1['unit']=='kg' || $row_vital1['unit']=='lbs'){
							$range_unit="(lbs)";
						}else if($row_vital1['unit']=='m' || $row_vital1['unit']=='inch'){
							$range_unit="(m)";
						}
					}
				}
			}
			$axisName = array($name,'Age');
			$graphTitle = $name.$range_unit;
			$imgId="imgGrph_cd";
			$seriesColor = array('#0000FF','#238E23');					
			$return_arr['status'] = '';
			if(count($shw_dat_final)>1){ 
				$return_arr['series'] = $graph_vals;
				$return_arr['axisName'] = $axisName;
				$return_arr['graphTitle'] = $graphTitle;
				$return_arr['seriesColor'] = $seriesColor;
			}else{
				$return_arr['status'] = 'Unable to create Graph. Two or more entries required to plot on Graph';
			}
			return $return_arr;
		}
	}	
	
	public function getAge_cal($cur_dat,$dob){
		$sql = "SELECT DATEDIFF('$cur_dat','$dob') AS differece";
		$result = imw_query($sql);
		$row = imw_fetch_array($result);	
		$age = "";
		$ageDays = $row["differece"];
		if($ageDays >= 365){
			$yrs = round($ageDays/365);
			$age = $yrs;
		}
		else if($ageDays >= 30){
			$months = round($ageDays/30);
			//$age = $months." Mon.";
		}
		else if($ageDays > 0){
			//$age = $ageDays." Days";			
		}
		return $age;		
	}
	
	
	//Saving VS details 
	public function save_vs_details($request){
		global $core;
		global $cls_review;
		$counter = 0;
		for($i=1;$i<=$request['last_cnt'];$i++){
			$vital_id = $request['vital_main_id_'.$i];
			$vitalDataArr = array();
			$vitalDataArr['date_vital'] = getDateFormatDB($request['new_range_dat'.$i]);
			$vitalDataArr['time_vital'] = date('h:i A');
			$vitalDataArr['bp_type'] = $request['new_bp_type'.$i];
			$vitalDataArr['comment'] = $request['new_comment'.$i];
			$vitalDataArr['patient_id'] = $_SESSION['patient'];
			$insert_data_chk = false;
			for($d=1;$d<12;$d++){
				if(trim($request['new_range'.$d.'_'.$i]) != ''){
					if($d == 10 and $request['new_range'.$d.'_'.$i] > 0){
						$insert_data_chk = true;
						break;
					}else if($d < 10){
						$insert_data_chk = true;
						break;
					}
				}
			}
			$row_vsm = "";
			
			if($insert_data_chk == true){
				if(trim($vital_id) > 0){
					$qry = "select * from vital_sign_master where id =".$vital_id;
					$res = imw_query($qry);
					$row_vsm = imw_fetch_assoc($res);
					$vitalDataArr['modified_on'] = date('Y-m-d H:i:s');
					$vitalDataArr['modified_by'] = $_SESSION['authId'];
					$vsm_action = "update";
					UpdateRecords($vital_id,'id',$vitalDataArr,'vital_sign_master');
				}
				else{
					$vsm_action = "add";
					$vitalDataArr['created_on'] = date('Y-m-d H:i:s');
					$vital_id = AddRecords($vitalDataArr,'vital_sign_master');
				}	
			}
			//--- REVIEWED FOR VITAL DATE ---
			if($vital_id != "" && $this->policy_status == 1){
				$med_reviewed_arr = array();
				//$vsmtDataFields = make_field_type_array("select * from vital_sign_master LIMIT 0 , 1");
				$vsmtDataFields = make_field_type_array("vital_sign_master");
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $vital_id;
				$reviewed_data_arr['Table_Name'] = 'vital_sign_master';
				$reviewed_data_arr['UI_Filed_Name'] = 'new_range_dat'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "date_vital";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($vsmtDataFields,"date_vital");
				$reviewed_data_arr['Field_Text'] = 'Vital Sign Date';
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $vsm_action;
				$reviewed_data_arr['Old_Value'] = $row_vsm['date_vital'];
				$reviewed_data_arr['New_Value'] = $vitalDataArr['date_vital'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				//pre($med_reviewed_arr);die();
				//CLSReviewMedHx::reviewMedHx($med_reviewed_arr,$_SESSION['authId'],"Vital Sign",$_SESSION['patient'],0,0);
				$cls_review->reviewMedHx($med_reviewed_arr,$_SESSION['authId'],"Vital Sign",$_SESSION['patient'],0,0);
			}
			//-------------------------------------------------------------------------
			
			//---- SAVE DATA IN VITAL SIGN DETAIL TABLE ---
			if($vital_id > 0 and $insert_data_chk == true){
				for($d=1;$d<12;$d++){
					$detailQryRes = '';
					//---- CHECK ALREADY EXISTS RECORDS ----		
					$sql_qry = imw_query("select id from vital_sign_patient
							where vital_master_id = '$vital_id' and vital_sign_id = '$d'");
					
					$detailQryRes = imw_fetch_array($sql_qry);
					$detail_id = $detailQryRes['id'];
					$sel_val_qry[] = $detail_id;	
					
					$detailDataArr = array();
					$detailDataArr['vital_master_id'] = $vital_id;
					$detailDataArr['vital_sign_id'] = $d;
					$detailDataArr['range_vital'] = trim($request['new_range'.$d.'_'.$i]);
					
					
					//--- LIMIT QUERY ---
					$sql = "select lower_limit as lowerLimit,upper_limit as upperLimit from vital_sign_limits where id = '$d'";
					switch($d){
						case 1:
						case 2:				
							$detailDataArr['unit'] = 'mmHg';
						break;
						case 3:
							$detailDataArr['unit'] = 'beats/minute';
						break;
						case 4:
							$detailDataArr['unit'] = 'breaths/minute';
						break;
						case 5:
							$detailDataArr['unit'] = 'ml/l';
						break;
						case 6:
							$detailDataArr['unit'] = $request['new_unit'.$d.'_'.$i];
							if($detailDataArr['unit'] == '°f'){
								$sql = "select lower_limit_english as lowerLimit,
									upper_limit_english as upperLimit from vital_sign_limits where id = '$d'
									and vital_sign_unit_english = '".htmlentities("&#176;f")."'";							
							}
							else{
								$sql = "select lower_limit_metric as lowerLimit,
									upper_limit_metric as upperLimit from vital_sign_limits where id = '$d'
									and vital_sign_unit_metric = '".htmlentities("&#176;c")."'";
							}									
						break;
						case 7:
							$detailDataArr['unit'] = $request['new_unit'.$d.'_'.$i];
							if($detailDataArr['unit'] == 'inch'){
								$sql = "select lower_limit_english as lowerLimit,
									upper_limit_english as upperLimit from vital_sign_limits where id = '$d'
									and vital_sign_unit_english = 'inch'";
							}
							else{
								$sql = "select lower_limit_metric as lowerLimit,
									upper_limit_metric as upperLimit from vital_sign_limits where id = '$d'
									and vital_sign_unit_metric = 'm'";
							}
						break;
						case 8:
							$detailDataArr['unit'] = $request['new_unit'.$d.'_'.$i];
							if($detailDataArr['unit'] == 'lbs'){
								$sql = "select lower_limit_english as lowerLimit,
									upper_limit_english as upperLimit from vital_sign_limits where id = '$d'
									and vital_sign_unit_english = 'lbs'";
							}
							else{
								$sql = "select lower_limit_metric as lowerLimit,
									upper_limit_metric as upperLimit from vital_sign_limits where id = '$d'
									and vital_sign_unit_metric = 'kg'";
							}
						break;
						case 9:
							$detailDataArr['unit'] = 'kg/sqr. m';
						break;
						case 11:
							$detailDataArr['inhale_O2'] = $request['inhale_O2'.$i];
                            $detailDataArr['range_vital'] = $request['inhale_O2'.$i];
						break;
						
					}
					
					//--- GET LIMITS DETAILS ---
					$qry = imw_query($sql);
					while($row = imw_fetch_array($qry)){
						$limitQryRes[] = $row;
					}
					$detailDataArr['range_vital'] = $detailDataArr['range_vital'] == 0 ? '' : $detailDataArr['range_vital'];
					
					
					if($detailDataArr['range_vital'] != ''){
						if($detailDataArr['range_vital'] >= $limitQryRes['lowerLimit'] and $detailDataArr['range_vital'] <= $limitQryRes['upperLimit']){
							$detailDataArr['range_status'] = 'in';
						}
						else{
							$detailDataArr['range_status'] = 'out';
						}
					}
					$row_vsp = '';
					//--- SAVE IN DATABASE ----
					if($detail_id > 0){
						$qry = "select * from vital_sign_patient where id =".$detail_id;
						$res = imw_query($qry);
						$row_vsp = imw_fetch_assoc($res);
						$vs_action = "update";
						UpdateRecords($detail_id,'id',$detailDataArr,'vital_sign_patient');
						$counter = ($counter+imw_affected_rows());
					}
					else{
						$vs_action = "add";
						$detail_id = AddRecords($detailDataArr,'vital_sign_patient');
						$counter = ($counter+imw_affected_rows());
					}
					
					//--- REVIEWED FOR problem_name ---
					if($this->policy_status == 1){
						$med_reviewed_arr = array();
						//$vsPatDataFields = make_field_type_array("select * from vital_sign_patient LIMIT 0 , 1");
						$vsPatDataFields = make_field_type_array("vital_sign_patient");
						
						$reviewed_data_arr = array();
						$reviewed_data_arr['Pk_Id'] = $detail_id;
						$reviewed_data_arr['Table_Name'] = 'vital_sign_patient';
						$reviewed_data_arr['UI_Filed_Name'] = 'new_range'.$d.'_'.$i;
						$reviewed_data_arr['Data_Base_Field_Name']= "range_vital";
						$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($vsPatDataFields,"range_vital");
						$reviewed_data_arr['Field_Text'] = $this->arr_vs[$detailDataArr['vital_sign_id']].' ( '.$vitalDataArr['date_vital'].' )';
						$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
						$reviewed_data_arr['Action'] = $vs_action;
						$reviewed_data_arr['Old_Value'] = $row_vsp['range_vital'];
						$reviewed_data_arr['New_Value'] = $detailDataArr['range_vital'];
						$med_reviewed_arr[] = $reviewed_data_arr;
						
						//CLSReviewMedHx::reviewMedHx($med_reviewed_arr,$_SESSION['authId'],"Vital Sign",$_SESSION['patient'],0,0);
						$cls_review->reviewMedHx($med_reviewed_arr,$_SESSION['authId'],"Vital Sign",$_SESSION['patient'],0,0);
					}
				}		
				$core->update_vitalSign_status();	
			}	
		}
		return $counter;
	}
	
	//Set CLS Alerts
	public function set_cls_alerts(){
		global $cls_alerts;	
		$return_str= '';
		$alertToDisplayAt = "admin_specific_chart_note_med_hx";
		$return_str .= $cls_alerts->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$form_id,"350px");
		$alertToDisplayAt = "patient_specific_chart_note_med_hx";
		$return_str .= $cls_alerts->getPatSpecificAlert($_SESSION['patient'],$alertToDisplayAt,"350px");
		$return_str .= $cls_alerts->autoSetDivLeftMargin("140","265");
		$return_str .= $cls_alerts->autoSetDivTopMargin("250","30");
		$return_str .= $cls_alerts->writeJS();
		return $return_str;	
	}	
}
?>