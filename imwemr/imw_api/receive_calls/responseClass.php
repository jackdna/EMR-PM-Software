<?php
$ignoreAuth = true;
require_once('../../config/globals.php');
require_once('../../library/classes/work_view/ChartAP.php');
require_once("../../library/classes/scheduler/appt_schedule_functions.php");
include_once('../../library/classes/SaveFile.php');
require_once("../../library/classes/common_function.php");

class responseClass{

	private $defTimeSlot=DEFAULT_TIME_SLOT;
	private $arrStatusMapping=array();
	private $arrOtherStatusMapping=array();

	function __construct(){
		$arrStatus[0]='A'; //Pending
		$arrStatus[17]='C'; //Confirmed
		$arrStatus[18]='X'; //Cancelled
		$arrStatus[3]='N'; //No show
		$arrStatus[201]='W'; //Wait Listed (To-Do)
		$arrStatus[271]='W'; //Wait Listed (First Available)
		$arrStatus[203]='B'; //Bumped/Deleted
		$arrStatus[11]='D'; //Completed
		$arrStatus[13]='D'; //Completed
		$arrStatus[202]='S'; //Scheduled  (Re-scheduled)
		$this->arrStatusMapping = $arrStatus;

		//OTHER STATUS
		$arrOtherStatus[0]='Pending';
		$arrOtherStatus[1]='Reminder Done';
		$arrOtherStatus[2]='Chart Pulled';
		$arrOtherStatus[3]='No Show';
		$arrOtherStatus[4]='Arrived';
		$arrOtherStatus[5]='Arrived Late';
		$arrOtherStatus[6]='Left without visit';
		$arrOtherStatus[7]='Insurance/Financial Issue ';
		$arrOtherStatus[8]='Billing Done';
		$arrOtherStatus[9]='Vitals Done';
		$arrOtherStatus[10]='In Exam Room';
		$arrOtherStatus[11]='Checked Out';
		$arrOtherStatus[12]='Coding Done';
		$arrOtherStatus[13]='Check-in';
		$arrOtherStatus[14]='In Waiting Room';
		$arrOtherStatus[15]='With Technician';
		$arrOtherStatus[16]='With Physician';
		$arrOtherStatus[17]='Confirm';
		$arrOtherStatus[18]='Cancelled';
		$arrOtherStatus[21]='Patient-Cancel';
		$arrOtherStatus[22]='Left-Message';
		$arrOtherStatus[23]='Not-Confirm';
		$arrOtherStatus[100]='Waiting for Surgery(W/Sx)';
		$arrOtherStatus[101]='Scheduled For Surgery (S/Sx)';
		$arrOtherStatus[200]='Room Assigned';
		$arrOtherStatus[201]='To-Do-Rescheduled';
		$arrOtherStatus[202]='Reschedule';
		$arrOtherStatus[203]='Deleted';
		$arrOtherStatus[271]='First Available';
		$this->arrOtherStatusMapping = $arrOtherStatus;
	}
	
	public function get_doctors(){
		$resultArr=array();
		$resArr=array();
		$qry="Select id, lname, fname, mname FROM users where lname!='' AND Enable_Scheduler='1' AND delete_status='0'  ORDER BY lname, fname";
		$rs=imw_query($qry);
		$i=0;
		if(imw_num_rows($rs)>0){
			$resultArr['success']=1;
			while($res=imw_fetch_assoc($rs)){
				$resArr[$i]['doctor_id']=$res['id'];
				$resArr[$i]['first_name']=$res['fname'];
				$resArr[$i]['last_name']=$res['lname'];
				$resArr[$i]['middle_name']=$res['mname'];
				$i++;
			}
		}else{
			$resultArr['success']=1;
		}
		
		$resultArr['result']=$resArr;
		return ($resultArr);
	}
	public function get_facilities(){
		$resultArr=array();
		$resArr=array();
		$qry="Select id, name FROM facility ORDER BY name";
		$rs=imw_query($qry);
		$i=0;
		if(imw_num_rows($rs)>0){
			$resultArr['success']=1;
			while($res=imw_fetch_assoc($rs)){
				$resArr[$i]['location_id']=$res['id'];
				$resArr[$i]['name']=$res['name'];
				$i++;
			}
		}else{
			$resultArr['success']=1;
		}
		
		$resultArr['result']=$resArr;
		return ($resultArr);
	}
	
	public function get_appt_proc(){
		$resultArr=array();
		$resArr=array();
		$qry="SELECT id, proc, acronym FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id = 0 AND active_status = 'yes' ORDER BY proc";
		$rs=imw_query($qry);
		$i=0;
		if(imw_num_rows($rs)>0){
			$resultArr['success']=1;
			while($res=imw_fetch_assoc($rs)){
				$resArr[$i]['type_id']=$res['id'];
				$resArr[$i]['name']=$res['proc'];
				$resArr[$i]['acronym']=$res['acronym'];
				$i++;
			}
		}else{
			$resultArr['success']=1;
		}
		
		$resultArr['result']=$resArr;
		return ($resultArr);
	}
	
	public function get_marketing_source(){
		$resultArr=array();
		$resArr=array();
		$qry="Select heard_id, heard_options FROM heard_about_us WHERE for_all='1' AND status='0' ORDER BY heard_options";
		$rs=imw_query($qry);

		if(imw_num_rows($rs)>0){
			$resultArr['success']=1;
			while($res=imw_fetch_assoc($rs)){
				$resArr[]=$res['heard_options'];
			}
		}else{
			$resultArr['success']=1;
		}
		
		$resultArr['result']=$resArr;
		return ($resultArr);
	}	
	
	public function get_patient_details($postArrReceived){
		$resultArr=array();
		$resArr=array();
		if(sizeof($postArrReceived)>0){
			$qry="SELECT id, fname, lname, DOB, LOWER(sex) as 'sex', postal_code, api_id, occupation, co_man_phy, primary_care, primary_care_id, 
			phone_home, phone_biz, phone_cell, preferr_contact, heard_abt_us, email, heard_abt_desc, hipaa_mail,
			hipaa_email, hipaa_voice, hipaa_text, temp_key, language   
			FROM patient_data WHERE";
			if($postArrReceived['patient_id']>0){
				$qry.=" id='".$postArrReceived['patient_id']."'";
			}else{
				$qry.=" 
				fname='".$postArrReceived['first_name']."'
				AND lname='".$postArrReceived['last_name']."'
				AND DOB='".$postArrReceived['dob']."'
				AND LOWER(sex)='".strtolower($postArrReceived['gender'])."'
				AND postal_code='".$postArrReceived['zip']."'";
			}
			$qry.=" LIMIT 0,1";
			
			$rs=imw_query($qry);
			$i=0;
			if(imw_num_rows($rs)>0){
				$resultArr['success']=1;
				while($res=imw_fetch_assoc($rs)){
					$appt_sts=$recall_date=$strDxCodes='';
					
					//GET RECENT LAST APPT STATUS
					$rs1= imw_query("Select sa_patient_app_status_id FROM schedule_appointments WHERE sa_patient_id='".$res['id']."' 
					AND sa_app_start_date<='".date('Y-m-d')."' ORDER BY sa_app_start_date DESC LIMIT 0,1");
					$res1=imw_fetch_assoc($rs1);
					$appt_sts=$res1['sa_patient_app_status_id'];
					if($this->arrStatusMapping[$appt_sts]){
						$appt_sts= $this->arrStatusMapping[$appt_sts];
					}else{
						$appt_sts= $this->arrOtherStatusMapping[$appt_sts];
					}
					
					//GET RECALL INFO
					$rs1= imw_query("Select recalldate FROM patient_app_recall WHERE patient_id='".$res['id']."' 
					AND recalldate>'".date('Y-m-d')."' ORDER BY recalldate ASC LIMIT 0,1");
					$res1=imw_fetch_assoc($rs1);
					$recall_date=$res1['recalldate'];
					
					//GET DIAGNOSES OF ASSESSMENT PLAN-----------------------------------------------
					//GET LAST DONE APPOINTMENT
					$rs1= imw_query("Select sa_app_start_date FROM schedule_appointments WHERE sa_patient_id='".$res['id']."' 
					AND sa_app_start_date<='".date('Y-m-d')."' AND sa_patient_app_status_id IN(13,11) ORDER BY sa_app_start_date DESC LIMIT 0,1");
					$res1=imw_fetch_assoc($rs1);
					$last_done_appt=$res1['sa_app_start_date'];
	
					if(empty($last_done_appt)==false){
						$rs1= imw_query("Select chart_assessment_plans.assess_plan, chart_assessment_plans.form_id  FROM chart_assessment_plans 
						JOIN chart_master_table ON chart_master_table.id =chart_assessment_plans.form_id 
						WHERE chart_assessment_plans.patient_id='".$res['id']."' 
						AND chart_master_table.date_of_service='".$last_done_appt."' ORDER BY chart_assessment_plans.id DESC LIMIT 0,1");
						$res1=imw_fetch_assoc($rs1);
						$ap_xml=$res1['assess_plan'];
						$ap_fid=$res1['form_id'];
						
						//CALL CLASS TO CONVERT XML INTO ARRAY
						$chartApXml = new ChartAP($res['id'], $ap_fid);
						//$ap_array= $chartApXml->getVal_Str($ap_xml);
						$ap_array= $chartApXml->getVal();
						if(sizeof($ap_array)>0){
							$arrDxCodes=array();
							foreach($ap_array['data']['ap'] as $data){
								//preg_match('#\((.*?)\)#', $data['assessment'], $dx_code);
								$arrDxCodes[]=$data['assessment'];
							}
							if(sizeof($arrDxCodes)>0){ $strDxCodes=implode(', ', $arrDxCodes); }
						}
					}
					//------------------------------------------------------------
					
					//GET CONTACT NUMBER
					//$phone_default = $res["phone_home"];
					$prefer_contact = $res["preferr_contact"];
					$preferred_contact_number='';
					if($prefer_contact == 0)
					{
						//if(trim($res["phone_home"]) != ""){$phone_default = $res["phone_home"]; unset($phoneArr['phone_home']); }
						$preferred_contact_number='Home Phone';
					}
					else if($prefer_contact == 1)
					{
						//if(trim($res["phone_biz"]) != ""){$phone_default = $res["phone_biz"]; unset($phoneArr['phone_bissuness']); }				
						$preferred_contact_number='Business Phone';
					}
					else if($prefer_contact == 2)
					{
						//if(trim($res["phone_cell"]) != ""){$phone_default = $res["phone_cell"]; unset($phoneArr['cell']); }				
						$preferred_contact_number='Cell';
					}
					//REMINDER CHOICES
					$reminder_choices='';
					$reminder_choices= ($res['hipaa_mail']=='1') ? 'Postal Mail,' : '';
					$reminder_choices.= ($res['hipaa_email']=='1') ? ' Email,' : '';
					$reminder_choices.= ($res['hipaa_voice']=='1') ? ' Voice,' : '';
					$reminder_choices.= ($res['hipaa_text']=='1') ? ' Text,' : '';		
					$reminder_choices= substr($reminder_choices, 0, -1);
					//GET CALL SUITABLE TIMMINGS
					if($res['hipaa_voice']=='1'){
						$rs1= imw_query("Select DATE_FORMAT(time_from, '%H:%i') as 'time_from', DATE_FORMAT(time_to, '%H:%i') as 'time_to' FROM patient_call_timmings 
						WHERE patient_id='".$res['id']."' AND del_status='0' ORDER BY id");
						while($res1=imw_fetch_assoc($rs1)){
							$arrCallTimmings[]=$res1['time_from'].'-'.$res1['time_to'];
						}
						if(sizeof($arrCallTimmings)>0){
							$strCallTimmings = implode(', ', $arrCallTimmings);
						}
					}
					
					
					$resArr[$i]['patient_id']=$res['id'];
					$resArr[$i]['md_patient_id']=$res['api_id'];
					$resArr[$i]['first_name']=$res['fname'];
					$resArr[$i]['last_name']=$res['lname'];
					$resArr[$i]['gender']=$res['sex'];
					$resArr[$i]['dob']=$res['DOB'];
					$resArr[$i]['zip']=$res['postal_code'];
					$resArr[$i]['home_phone']=$res["phone_home"];
					$resArr[$i]['business_phone']=$res["phone_biz"];
					$resArr[$i]['cell']=$res["phone_cell"];
					$resArr[$i]['preferred_contact']=$preferred_contact_number;				
					$resArr[$i]['email']=$res['email'];
					$resArr[$i]['reminder_choices']=$reminder_choices;
					$resArr[$i]['suitable_voice_timings']=$strCallTimmings;
					$resArr[$i]['heard_by']=$res['heard_abt_us'];
					$resArr[$i]['heard_by_description']=$res['heard_abt_desc'];
					$resArr[$i]['occupation']=$res['occupation'];
					$resArr[$i]['doctor_referral_id']=$res['primary_care_id'];
					$resArr[$i]['doctor_referral']=$res['primary_care'];
					$resArr[$i]['co_managed']=$res['co_man_phy'];
					$resArr[$i]['appointment_status']=$appt_sts;
					$resArr[$i]['recall']=$recall_date;
					$resArr[$i]['diagnoses']=$strDxCodes;
					$resArr[$i]['patient_portal_key']=$res["temp_key"];
					$resArr[$i]['preferred_language']=$res["language"];
					
					$i++;
				}
			}else{
				$resultArr['success']=1;
			}
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=201;
			$resultArr['error_description']='Parameters required.';
		}
		
		$resultArr['result']=$resArr;
		return ($resultArr);
	}

	public function create_patient($postArrReceived){
		$resultArr=array();
		$resArr=array();
	
		if((empty($postArrReceived['first_name'])==false && empty($postArrReceived['last_name'])==false
		 && empty($postArrReceived['dob'])==false && empty($postArrReceived['gender'])==false 
		 && empty($postArrReceived['gender'])==false && empty($postArrReceived['zip'])==false && $postArrReceived['md_patient_id']>0) 
		 || ($postArrReceived['md_patient_id']>0 && $postArrReceived['imw_patient_id']>0)){
			 
			//CHECK IF PATIENT ALREADY EXIST
			$gender=$postArrReceived['gender'];
			if(strtolower($postArrReceived['gender'])=='m')$gender='Male';
			else if(strtolower($postArrReceived['gender'])=='f')$gender='Female';
			
			$qry="SELECT id, mname, sex, api_id FROM patient_data WHERE 1=1";
			if(empty($postArrReceived['first_name'])==false){
				$qry.=" AND fname='".$postArrReceived['first_name']."'";
			}
			if(empty($postArrReceived['last_name'])==false){
				$qry.=" AND lname='".$postArrReceived['last_name']."'";
			}
			if(empty($postArrReceived['dob'])==false){
				$qry.=" AND DOB='".$postArrReceived['dob']."'";
			}
			if(empty($gender)==false){
				$qry.=" AND LOWER(sex)='".strtolower($gender)."'";
			}
			if(empty($postArrReceived['zip'])==false){
				$qry.=" AND postal_code='".$postArrReceived['zip']."'";
			}
			if(empty($postArrReceived['middle_name'])==false){
				$qry.=" AND mname='".$postArrReceived['middle_name']."'";
			}
			if($postArrReceived['imw_patient_id']>0){
				$qry.=" AND id='".$postArrReceived['imw_patient_id']."'";
			}				

			$qry.=" LIMIT 1";
			$rs=imw_query($qry);
			
			if(imw_num_rows($rs)>0){
				$res=imw_fetch_assoc($rs);
				
				if($res['api_id']<=0){ //UPDATING API_ID
					imw_query("Update patient_data SET api_id='".$postArrReceived['md_patient_id']."' WHERE id='".$res['id']."'");
				}
				
				$resultArr['success']=0;
				$resultArr['error_code']=218;
				$resultArr['error_description']='Already exist';

				//IF ALREADY EXIST THEN RESPONSE DATA
				
				$resArr['imw_patient_id']=$res['id'];
				$resArr['md_patient_id']=$postArrReceived['md_patient_id'];
				$resArr['first_name']=$postArrReceived['first_name'];
				$resArr['last_name']=$postArrReceived['last_name'];
				$resArr['middle_name']=$res['mname'];
				$resArr['gender']=$res['sex'];
				$resArr['dob']=$postArrReceived['dob'];
				$resArr['zip']=$postArrReceived['zip'];

			}else{
				
				$qryPart='';
				$heard_id=0;
				$created_date=date('Y-m-d H:i:s');
				
				//GETTING HEARD ABOUT US ID
				if(empty($postArrReceived['marketing_source'])==false){
					$rs_heard=imw_query("Select heard_id FROM heard_about_us WHERE heard_options='".$postArrReceived['marketing_source']."' 
					AND for_all='1' AND status='0' ORDER BY heard_id LIMIT 1");
					if(imw_num_rows($rs_heard)>0){
						$res_heard=imw_fetch_assoc($rs_heard);
						$heard_id=$res_heard['heard_id'];
						$qryPart="
						heard_abt_us='".$heard_id."',
						heard_about_us_date ='".$created_date."',";	
					}
				}
				
				//INSERT RECORD
				$qry="INSERT INTO patient_data SET 
				fname='".$postArrReceived['first_name']."',
				lname='".$postArrReceived['last_name']."',
				mname='".$postArrReceived['middle_name']."',
				DOB='".$postArrReceived['dob']."',
				sex='".ucfirst($gender)."',
				postal_code='".$postArrReceived['zip']."',
				phone_home='".$postArrReceived['phone']."',
				email='".$postArrReceived['email']."',
				street='".$postArrReceived['address']."',
				api_id='".$postArrReceived['md_patient_id']."',
				date='".$created_date."',
				".$qryPart."
				comments ='Added from API',
				created_by ='1'";
				
				imw_query($qry);
				$inserted_id=imw_insert_id();
				
				
				if($inserted_id>0){

					//UPDATING PID COLUMN
					imw_query("UPDATE patient_data SET pid='".$inserted_id."' WHERE id='".$inserted_id."'");
					
					$resultArr['success']=1;

					$resArr['imw_patient_id']=$inserted_id;
					$resArr['md_patient_id']=$postArrReceived['md_patient_id'];
					$resArr['first_name']=$postArrReceived['first_name'];
					$resArr['last_name']=$postArrReceived['last_name'];
					$resArr['middle_name']=$postArrReceived['middle_name'];
					$resArr['gender']=$gender;
					$resArr['dob']=$postArrReceived['dob'];
					$resArr['zip']=$postArrReceived['zip'];
				}
			}
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=201;
			$resultArr['error_description']='Parameters required.';
		}
		
		$resultArr['result']=$resArr;
		return ($resultArr);
	}
	
	public function get_appointments($postArrReceived){
		$resultArr=array();
		$resArr=array();
		
		if(sizeof($postArrReceived)>0){
			$imedic_pid=$imedic_doc_id=$imedic_fac_id=0;
			$notFound=$limit_start=0;
			$sort = ($postArrReceived['sort_order']=='') ? 'asc' : $postArrReceived['sort_order']; //DEFAULT IS ASC
			$limit_end = ($postArrReceived['limit']>0) ? $postArrReceived['limit'] : 1000;    //DEFAULT LIMIT IS 1000
			
			//SETTING OF PAGE NUMBER
			if($postArrReceived['page_number']>0){
				$limit_start = ($limit_end * $postArrReceived['page_number']) - $limit_end;
				$limit_end = $limit_end;
			}
			
			//DATE RANGE SETTINGS
			if($postArrReceived['start_date']!='' || $postArrReceived['end_date']!=''){
				if($postArrReceived['start_date']!='' && $postArrReceived['end_date']==''){
					$postArrReceived['end_date'] = $postArrReceived['start_date'];
				}
				if($postArrReceived['start_date']=='' && $postArrReceived['end_date']!=''){
					$postArrReceived['start_date'] = $postArrReceived['end_date'];
				}
			}
			//MODIFIED DATE RANGE SETTINGS
			if($postArrReceived['modified_date_start']!='' && $postArrReceived['modified_date_end']==''){
				$postArrReceived['modified_date_end'] = date('Y-m-d');
			}
			
			//GETTING PATIENT ID
			//$qry="Select id FROM patient_data WHERE api_id='".$postArrReceived['patient_id']."'";
			//$rs=imw_query($qry);
			//$res=imw_fetch_assoc($rs);
			$imedic_pid=$postArrReceived['patient_id'];
			
			//GETTING DOCTOR ID
			if(empty($postArrReceived['doctor_id'])==false){
				$qry="Select id, CONCAT(lname, ', ', fname) as 'doctor_name' FROM users WHERE id='".$postArrReceived['doctor_id']."'";
				$rs=imw_query($qry);
				if(imw_num_rows($rs)>0){
					$res=imw_fetch_assoc($rs);
					$imedic_doc_id=$res['id'];
					$imedic_doctor_name=$res['doctor_name'];
				}else{
					$notFound=1;
				}
			}
			//GETTING FACILITY ID
			if(empty($postArrReceived['location'])==false){
				$qry="Select id FROM facility WHERE LOWER(name)='".strtolower($postArrReceived['location'])."'";
				$rs=imw_query($qry);
				if(imw_num_rows($rs)>0){
					$res=imw_fetch_assoc($rs);
					$imedic_fac_id=$res['id'];
				}else{
					$notFound=1;
				}
			}
			
			if($imedic_doc_id<=0 && $imedic_fac_id<=0 && $imedic_pid<=0 && $postArrReceived['start_date']=='' && $postArrReceived['end_date']==''
			&& $postArrReceived['modified_date_start']=='' && $postArrReceived['modified_date_end']==''){
				$notFound=1;
			}
			
			if($notFound==0){
				$arrAllDoctors= $this->get_all_doctors();
				
				$qry="SELECT sa.id, sa.sa_app_start_date, sa.sa_app_end_date, sa.sa_app_starttime, sa.sa_app_endtime, sa.sa_patient_app_status_id, 
				sa.procedureid, sa.sa_patient_id, sa.sa_doctor_id, sa.sa_facility_id, slot_procedures.proc as 'proc_name', facility.name as 'facility_name',
				pd.fname, pd.lname, pd.DOB, LOWER(pd.sex) as 'sex', pd.postal_code, pd.api_id, pd.occupation, pd.co_man_phy, pd.primary_care, pd.primary_care_id, 
				pd.phone_home, pd.phone_biz, pd.phone_cell, pd.preferr_contact, pd.heard_abt_us, pd.email, pd.heard_abt_desc, pd.hipaa_mail,
				pd.hipaa_email, pd.hipaa_voice, pd.hipaa_text 
				FROM schedule_appointments sa  
				LEFT JOIN slot_procedures ON slot_procedures.id = sa.procedureid 
				LEFT JOIN facility ON facility.id = sa.sa_facility_id 
				LEFT JOIN patient_data pd ON pd.id = sa.sa_patient_id 
				WHERE 1=1";
				if($imedic_pid>0){
					$qry.=" AND sa.sa_patient_id ='".$imedic_pid."'";
				}
				if($imedic_doc_id>0){
					$qry.=" AND sa.sa_doctor_id='".$imedic_doc_id."'";
				}
				if(empty($postArrReceived['start_date'])==false && empty($postArrReceived['end_date'])==false){
					$qry.=" AND (sa.sa_app_start_date BETWEEN '".$postArrReceived['start_date']."' AND '".$postArrReceived['end_date']."')";
				}
				if(empty($postArrReceived['modified_date_start'])==false && empty($postArrReceived['modified_date_end'])==false){
					$qry.=" AND (DATE_FORMAT(sa.sa_app_time, '%Y-%m-%d') BETWEEN '".$postArrReceived['modified_date_start']."' AND '".$postArrReceived['modified_date_end']."')";
				}
				if(empty($postArrReceived['modified_time_start'])==false && empty($postArrReceived['modified_time_end'])==false){
					$qry.=" AND (DATE_FORMAT(sa.sa_app_time, '%H:%i') BETWEEN '".$postArrReceived['modified_time_start']."' AND '".$postArrReceived['modified_time_end']."')";
				}
				if($imedic_fac_id>0){
					$qry.=" AND sa.sa_facility_id='".$imedic_fac_id."'";
				}
				if(empty($postArrReceived['start_date'])==true){
					$qry.=" AND sa.sa_app_start_date>='".date('Y-m-d')."'";
				}
				$qry.=" ORDER BY sa.sa_app_start_date $sort, sa.sa_app_starttime $sort LIMIT $limit_start, $limit_end";
				$rs=imw_query($qry);
				$i=0;
				if(imw_num_rows($rs)>0){
					while($res=imw_fetch_assoc($rs)){
						$pid=$res['sa_patient_id'];
						
						if(!$tempArr[$res['sa_patient_id']]){
							$strDxCodes=$recall_date='';

							//GET RECALL INFO
							$rs1= imw_query("Select recalldate FROM patient_app_recall WHERE patient_id='".$res['sa_patient_id']."' 
							AND recalldate>'".date('Y-m-d')."' ORDER BY recalldate ASC LIMIT 0,1");
							$res1=imw_fetch_assoc($rs1);
							$recall_date=$res1['recalldate'];

							//GET DIAGNOSES OF ASSESSMENT PLAN-----------------------------------------------
							//GET LAST DONE APPOINTMENT
							$rs1= imw_query("Select sa_app_start_date FROM schedule_appointments WHERE sa_patient_id='".$res['sa_patient_id']."' 
							AND sa_app_start_date<='".date('Y-m-d')."' AND sa_patient_app_status_id IN(13,11) ORDER BY sa_app_start_date DESC LIMIT 0,1");
							$res1=imw_fetch_assoc($rs1);
							$last_done_appt=$res1['sa_app_start_date'];
							
							if(empty($last_done_appt)==false){
								$rs1= imw_query("Select chart_assessment_plans.assess_plan, chart_assessment_plans.form_id FROM chart_assessment_plans 
								JOIN chart_master_table ON chart_master_table.id =chart_assessment_plans.form_id 
								WHERE chart_assessment_plans.patient_id='".$res['sa_patient_id']."' 
								AND chart_master_table.date_of_service='".$last_done_appt."' ORDER BY chart_assessment_plans.id DESC LIMIT 0,1");
								$res1=imw_fetch_assoc($rs1);
								$ap_xml=$res1['assess_plan'];
								$ap_fid=$res1['form_id'];
								
								//CALL CLASS TO CONVERT XML INTO ARRAY
								$chartApXml = new ChartAP($res['sa_patient_id'], $ap_fid);
								//$ap_array= $chartApXml->getVal_Str($ap_xml);
								$ap_array= $chartApXml->getVal();
								if(sizeof($ap_array)>0){
									$arrDxCodes=array();
									foreach($ap_array['data']['ap'] as $data){
										//preg_match('#\((.*?)\)#', $data['assessment'], $dx_code);
										$arrDxCodes[]=$data['assessment'];
									}
									if(sizeof($arrDxCodes)>0){ $strDxCodes=implode(', ', $arrDxCodes); }
								}
							}
							//------------------------------------------------------------

							//GET CONTACT NUMBER
							$preferred_contact_number='';
							//$phone_default = $res["phone_home"];
							$prefer_contact = $res["preferr_contact"];
							if($prefer_contact == 0)
							{
								//if(trim($res["phone_home"]) != ""){$phone_default = $res["phone_home"]; }
								$preferred_contact_number='Home Phone';
							}
							else if($prefer_contact == 1)
							{
								//if(trim($res["phone_biz"]) != ""){$phone_default = $res["phone_biz"]; }				
								$preferred_contact_number='Business Phone';
							}
							else if($prefer_contact == 2)
							{
								//if(trim($res["phone_cell"]) != ""){$phone_default = $res["phone_cell"]; }				
								$preferred_contact_number='Cell';
							}

							//REMINDER CHOICES
							$reminder_choices='';
							$reminder_choices= ($res['hipaa_mail']=='1') ? 'Postal Mail,' : '';
							$reminder_choices.= ($res['hipaa_email']=='1') ? ' Email,' : '';
							$reminder_choices.= ($res['hipaa_voice']=='1') ? ' Voice,' : '';
							$reminder_choices.= ($res['hipaa_text']=='1') ? ' Text,' : '';		
							$reminder_choices= substr($reminder_choices, 0, -1);
							
							//GET CALL SUITABLE TIMMINGS
							$strCallTimmings='';
							if($res['hipaa_voice']=='1'){
								$rs1= imw_query("Select DATE_FORMAT(time_from, '%H:%i') as 'time_from', DATE_FORMAT(time_to, '%H:%i') as 'time_to' FROM patient_call_timmings 
								WHERE patient_id='".$res['sa_patient_id']."' AND del_status='0' ORDER BY id");
								while($res1=imw_fetch_assoc($rs1)){
									$arrCallTimmings[]=$res1['time_from'].'-'.$res1['time_to'];
								}
								if(sizeof($arrCallTimmings)>0){
									$strCallTimmings = implode(', ', $arrCallTimmings);
								}
							}

							$arrPatInfo[$pid]['recall']=$recall_date;
							$arrPatInfo[$pid]['dx_codes']=$strDxCodes;
							$arrPatInfo[$pid]['phone']=$phone_default;
							$arrPatInfo[$pid]['reminder_choices']=$reminder_choices;
							$arrPatInfo[$pid]['suitable_voice_timings']= $strCallTimmings;
						}
						
						$appt_status='';
						if($this->arrStatusMapping[$res['sa_patient_app_status_id']]){
							$appt_status=$this->arrStatusMapping[$res['sa_patient_app_status_id']];
						}else{
							$appt_status=$this->arrOtherStatusMapping[$res['sa_patient_app_status_id']];
						}
						
						//GET CHECK OUT TIME
						$checkout_time='';
						$rs1= imw_query("Select status_time FROM previous_status WHERE sch_id='".$res['id']."' AND status='11' ORDER BY id DESC LIMIT 0,1");
						if(imw_num_rows($rs1)>0){
							$res1=imw_fetch_assoc($rs1);
							$checkout_time=$res1['status_time'];
						}
												
						
						//ACTUAL DATA
						$resArr[$i]['appointment_id']=$res['id'];
						$resArr[$i]['patient_id']=$res['sa_patient_id'];
						$resArr[$i]['md_patient_id']=$res['api_id'];
						$resArr[$i]['first_name']=$res['fname'];
						$resArr[$i]['last_name']=$res['lname'];
						$resArr[$i]['gender']=$res['sex'];
						$resArr[$i]['dob']=$res['DOB'];
						$resArr[$i]['zip']=$res['postal_code'];
						$resArr[$i]['home_phone']= $res['phone_home'];
						$resArr[$i]['business_phone']= $res['phone_biz'];
						$resArr[$i]['cell']= $res['phone_cell'];
						$resArr[$i]['preferred_contact']= $preferred_contact_number;
						$resArr[$i]['email']=$res['email'];
						$resArr[$i]['reminder_choices']= $arrPatInfo[$pid]['reminder_choices'];
						$resArr[$i]['suitable_voice_timings']= $arrPatInfo[$pid]['suitable_voice_timings'];
						$resArr[$i]['heard_by']=$res['heard_abt_us'];
						$resArr[$i]['heard_by_description']=$res['heard_abt_desc'];
						$resArr[$i]['occupation']=$res['occupation'];
						$resArr[$i]['doctor_referral_id']=$res['primary_care_id'];
						$resArr[$i]['doctor_referral']=$res['primary_care'];
						$resArr[$i]['co_managed']=$res['co_man_phy'];
						$resArr[$i]['recall']= $arrPatInfo[$pid]['recall'];
						$resArr[$i]['diagnoses']= $arrPatInfo[$pid]['dx_codes'];					
						$resArr[$i]['start_date']=$res['sa_app_start_date'];
						$resArr[$i]['end_date']=$res['sa_app_end_date'];
						$resArr[$i]['start_time']=$res['sa_app_starttime'];
						$resArr[$i]['end_time']=$res['sa_app_endtime'];
						$resArr[$i]['checkout_time']=$checkout_time;
						$resArr[$i]['visit_type']=$res['proc_name'];
						$resArr[$i]['visit_type_id']=$res['procedureid'];
						$resArr[$i]['appointment_status']= $appt_status;
						$resArr[$i]['imedic_appt_status']= $arrOtherStatusMapping[$res['sa_patient_app_status_id']];
						$resArr[$i]['doctor_id']=$res['sa_doctor_id'];
						$resArr[$i]['doctor_name']=$arrAllDoctors[$res['sa_doctor_id']];
						$resArr[$i]['location_id']=$res['sa_facility_id'];
						$resArr[$i]['location']=$res['facility_name'];
						
						$tempArr[$res['sa_patient_id']]=$res['sa_patient_id'];
						$i++;
					}
				}else{
				}				
			}else{
				$resultArr['success']=0;
				$resultArr['error_code']=201;
				$resultArr['error_description']='Parameters required.';
			}
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=201;
			$resultArr['error_description']='Parameters required.';
		}
		
		unset($arrPatInfo);
		
		$resultArr['result']=$resArr;
		
		return ($resultArr);
	}

	
	//BOOK APPOINTMENT
	public function book_appointment($postArrReceived){

		$resultArr=array();
		$resArr=array();
		$imw_pat_id=$imw_doc_id=$imw_loc_id=$imw_visit_id=0;
		
		//GET PATIENT ID
		if($postArrReceived['patient_id']>0){
			$arrRet=$this->get_api_pat_id($postArrReceived['patient_id']);
			$imw_pat_id=$postArrReceived['patient_id'];
			$api_pat_id=$arrRet['pat_id'];
			$imw_pat_name=$arrRet['pat_name'];
			
			if($imw_pat_id<=0){
				$resultArr['success']=0;
				$resultArr['error_code']=205;
				$resultArr['error_description']='Parameter patient id required.';
				return $resultArr;
			}
			if($api_pat_id<=0){
				$resultArr['success']=0;
				$resultArr['error_code']=204;
				$resultArr['error_description']='Patient id is not mapped.';
				return $resultArr;
			}
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=205;
			$resultArr['error_description']='Parameter patient id required.';
			return $resultArr;
		}	


		//GET DOCTOR ID
		if($postArrReceived['doctor_id']>0){
			$arrRet=$this->get_api_doc_id($postArrReceived['doctor_id']);
			$api_doc_id=$arrRet['doc_id'];
			$imw_doc_id=$postArrReceived['doctor_id'];
			if($imw_doc_id<=0){
				$resultArr['success']=0;
				$resultArr['error_code']=207;
				$resultArr['error_description']='Parameter doctor id required.';
				return $resultArr;
			}
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=207;
			$resultArr['error_description']='Parameter doctor id required.';
			return $resultArr;
		}	
			
		//GET LOCATION ID
		if($postArrReceived['location_id']>0){
			$arrRet=$this->get_api_loc_id($postArrReceived['location_id']);
			$api_loc_id=$arrRet['loc_id'];
			$imw_loc_id=$postArrReceived['location_id'];
			if($imw_loc_id<=0){
				$resultArr['success']=0;
				$resultArr['error_code']=209;
				$resultArr['error_description']='Parameter location id required.';
				return $resultArr;
			}
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=209;
			$resultArr['error_description']='Parameter location id required.';
			return $resultArr;
		}				

		//GET VISIT TYPE ID
		if($postArrReceived['visit_type_id']>0){
			$arrRet=$this->get_api_visit_id($postArrReceived['visit_type_id']);
			$api_visit_id=$arrRet['visit_id'];
			$imw_visit_id=$postArrReceived['visit_type_id'];
			if($imw_visit_id<=0){
				$resultArr['success']=0;
				$resultArr['error_code']=211;
				$resultArr['error_description']='Parameter visit type id required.';
				return $resultArr;
			}
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=211;
			$resultArr['error_description']='Parameter visit type id required.';
			return $resultArr;
		}
		
		//ADDING APPOINTMENT
		if($imw_pat_id>0 && $imw_doc_id>0 && $imw_loc_id>0 && $imw_visit_id>0){
			$notAvailable=0;
			$qry="Select id FROM schedule_appointments WHERE 
			sa_doctor_id='".$imw_doc_id."'
			AND sa_facility_id='".$imw_loc_id."'
			AND sa_app_start_date='".$postArrReceived['appt_start_date']."'
			AND DATE_FORMAT(sa_app_starttime, '%H:%i')='".$postArrReceived['appt_start_time']."' 
			AND sa_patient_app_status_id NOT IN('18','201','203','271')";
			$rs=imw_query($qry);
	
			if(imw_num_rows($rs)>0){
				$resultArr['success']=0;
				$resultArr['error_code']=212;
				$resultArr['error_description']='Appointment slot not available.';
				return $resultArr;
			}

			
			//GET PROCEDURE TIME
			$proc_time='00:00:00';
			$CLSApptSch= new appt_scheduler;
			$str_proc_details=$CLSApptSch->default_proc_to_doctor_proc($imw_visit_id, $imw_doc_id);
			if(empty($str_proc_details)==false){
				$arr_proc_details=explode('~', $str_proc_details);

				$imw_visit_id=$postArrReceived['visit_type_id']=$arr_proc_details[0]; //IF DOCTOR HAS MADE HIS OWN PROECURE THEN THIS IS THAT ID/ OTHERWISE ITS SAME AS RECEIVED IN THIS FUNCTION
				$proc_time=$arr_proc_details[1];
				$proc_time_duration= $proc_time*60;
				$proc_time='00:'.$proc_time.':00';
				$endTime= strtotime($postArrReceived['appt_start_time']) + ($proc_time_duration);
				$end_time_calculated= date('H:i:s',$endTime);			
			}

			
			//CALLING AVAILABLE TIMES FUNCTION
			$passingArray=array(
			'doctor_id'=>$postArrReceived['doctor_id'],
			'location_id'=>$postArrReceived['location_id'],
			'schedule_from_date'=>$postArrReceived['appt_start_date'],
			'schedule_to_date'=>$postArrReceived['appt_start_date']);
			$arrFunRes=$this->get_available_times($passingArray, 'function');
			$arrAvailableTimes=$arrFunRes['result'];

			$t1= strtotime($postArrReceived['appt_start_time']);
			$t2= $endTime;
			$oldStartTime=$postArrReceived['appt_start_time'];
			
			//MAKE ARRAY OF TIME DURATION IN SLOTS
			$defTimeSec= $this->defTimeSlot * 60;
			for($i=$t1; $i<$t2;){
				$i=$i+$defTimeSec;
				$startTime=$oldStartTime;								 
				$endTime=date('H:i', $i);

				$arrAskTimes[$startTime]= $endTime;								
				
				$oldStartTime=$endTime;
			}

			//CHECKING IF ALL ASK SLOTS AVAILABLE OR NOT
			$notAvailable=0;
			$template_id=0;

			foreach($arrAskTimes as $startTime => $endTime){
				$matched=0;
				foreach($arrAvailableTimes[$postArrReceived['appt_start_date']][$imw_doc_id] as $slotData){
					$template_id=$slotData['tempId'];

					if($startTime==$slotData['start_time'] && $endTime==$slotData['end_time']){

						$matched=1;
						//MAKING ARRAY FOR REPLACING LABLES
						$arrSlotLabels[$postArrReceived['appt_start_date']][$startTime]['end_time']=$endTime;
						$arrSlotLabels[$postArrReceived['appt_start_date']][$startTime]['l_text']=$slotData['l_text'];
						$arrSlotLabels[$postArrReceived['appt_start_date']][$startTime]['label_type']=$slotData['label_type'];
						$arrSlotLabels[$postArrReceived['appt_start_date']][$startTime]['color']=$slotData['color'];

						break;
					}
				}
				
				if($matched==0){
					$notAvailable=1;
					break;
				}
			}

			// DEPENDS ON AVAILABLE/NOT AVAILABLE
			if($notAvailable==1){
				$resultArr['success']=0;
				$resultArr['error_code']=212;
				$resultArr['error_description']='Appointment slot not available.';
				return $resultArr;
			}else{
				
				$qry="Select sch_tmp_id FROM provider_schedule_tmp WHERE 
				provider='".$imw_doc_id."'
				AND facility='".$imw_loc_id."'
				AND today_date='".$postArrReceived['appt_start_date']."'";
				$rs=imw_query($qry);
				$res=imw_fetch_assoc($rs);

				
				//ADDING
				$qry="INSERT INTO schedule_appointments SET 
				sa_patient_id='".$imw_pat_id."',
				sa_patient_name='".$imw_pat_name."',
				sa_doctor_id='".$imw_doc_id."',
				sa_facility_id='".$imw_loc_id."',
				procedureid='".$imw_visit_id."',
				sa_app_start_date='".$postArrReceived['appt_start_date']."',
				sa_app_end_date='".$postArrReceived['appt_start_date']."',
				sa_app_starttime='".$postArrReceived['appt_start_time']."',
				sa_app_duration='".$proc_time_duration."',
				sch_template_id='".$template_id."',
				sa_comments='Appointment added by API',
				sa_app_endtime='".$end_time_calculated."'";
				
				if($rs=imw_query($qry)){
					$appt_id=imw_insert_id();

					//GET SAVED APPT END TIME
					$rs1=imw_query("Select sa_app_endtime FROM schedule_appointments WHERE id='".$appt_id."'");
					$res1=imw_fetch_assoc($rs1);
					$appt_end_time=$res1['sa_app_endtime']; 

					//REPLACING OLD LABELS IF EXIST ON SLOT
					foreach($arrSlotLabels as $scheduleDate => $timeData){
						foreach($timeData as $startTime => $timeInfo){

							if(empty($timeInfo['l_text'])==false){
								$l_show_text=$lbl_replaced='';
								$arr_l_show_text= explode(';', $timeInfo['l_text']);
								if(sizeof($arr_l_show_text)>0){
									$lbl_replaced = $arr_l_show_text[0];
									unset($arr_l_show_text[0]);
								}
								if(sizeof($arr_l_show_text)>0){
									$l_show_text=implode('; ', $arr_l_show_text);
								}

								$label_replaced='::'.$appt_id.':'.$lbl_replaced;

								
								$qry1="Insert into scheduler_custom_labels SET
								provider='".$imw_doc_id."',
								facility='".$imw_loc_id."',
								start_date='".$postArrReceived['appt_start_date']."',
								start_time='".$startTime."',
								end_time='".$timeInfo['end_time']."',
								l_type='".$timeInfo['label_type']."',
								l_text='".$timeInfo['l_text']."',
								l_show_text='".$l_show_text."',
								l_color='".$timeInfo['color']."',
								time_status='".date('Y-m-d H:i:s')."',
								labels_replaced='".$label_replaced."',
								system_action='1'";
								//condition added to stop garbage value
								if($timeInfo["label_type"]=='Procedure' || $timeInfo["label_type"]=='Information' || $timeInfo["label_type"]=='Lunch' || $timeInfo["label_type"]=='Reserved')
								{
									imw_query($qry1);
								}else{custom_lbl_log('imwapi\receiveCalls\responseClass.php');}
								
							}else{ //checking custom labels

								//GET CUSTOM LABEL TIMMINGS ADDED FROM FRONT DESK
								$qry1="SELECT DATE_FORMAT(start_time,'%H:%i') as 'start_time', DATE_FORMAT(end_time,'%H:%i') as 'end_time',
								start_date, start_time, end_time, l_type, l_text, l_show_text, l_color, labels_replaced 
								FROM scheduler_custom_labels 
								WHERE l_show_text!='' 
								AND provider ='".$imw_doc_id."' 
								AND facility='".$imw_loc_id."' 
								AND start_date='".$postArrReceived['appt_start_date']."' 
								AND start_time='".$startTime.':00'."'
								AND end_time='".$timeInfo['end_time'].':00'."'";
								$rs1=imw_query($qry1);
								$res1=imw_fetch_assoc($rs1);
								
								$l_show_text=$lbl_replaced='';
								$arr_l_show_text= explode(';', $res1['l_show_text']);
								if(sizeof($arr_l_show_text)>0){
									$lbl_replaced = $arr_l_show_text[0];
									unset($arr_l_show_text[0]);
								}
								if(sizeof($arr_l_show_text)>0){
									$l_show_text=implode('; ', $arr_l_show_text);
								}
								
								$label_replaced= $res1['labels_replaced'].'::'.$appt_id.':'.$lbl_replaced;
								
								//UPDATING CUSTOM LABELS
								$qry2="Update scheduler_custom_labels SET
								l_show_text='".$l_show_text."',
								time_status='".date('Y-m-d H:i:s')."',
								labels_replaced='".$label_replaced."',
								system_action='1' 
								WHERE 
								provider ='".$imw_doc_id."' 
								AND facility='".$imw_loc_id."' 
								AND start_date='".$postArrReceived['appt_start_date']."' 
								AND start_time='".$startTime.':00'."'
								AND end_time='".$timeInfo['end_time'].':00'."'";
								imw_query($qry2);
							}
								
						}
						
					}
		
					//CREATING RESPONSE ARRAY
					$resArr['imw_appt_id']=$appt_id;
					$resArr['patient_id']=$postArrReceived['patient_id'];
					$resArr['doctor_id']=$postArrReceived['doctor_id'];
					$resArr['location_id']=$postArrReceived['location_id'];
					$resArr['visit_type_id']=$postArrReceived['visit_type_id'];
					$resArr['appt_start_date']=$postArrReceived['appt_start_date'];
					$resArr['appt_end_date']=$postArrReceived['appt_start_date'];
					$resArr['appt_start_time']=$postArrReceived['appt_start_time'];
					$resArr['appt_end_time']=$appt_end_time;
					
					$resultArr['success']=1;
				}
			}
		}
		
		$resultArr['result']=$resArr;
		
		return ($resultArr);
	}
	
	//GET AVAILABLE TIMES
	public function get_available_times($postArrReceived, $callFrom=''){
		$resultArr=array();
		$resArr=array();
		$imw_doc_id=$imw_loc_id=0;
		$imw_doc_name=$imw_loc_name='';

		//GET IMW DOC ID
		if($postArrReceived['doctor_id']>0){
			$arrRet=$this->get_api_doc_id($postArrReceived['doctor_id']);
			$imw_doc_id=$postArrReceived['doctor_id'];
			$api_doc_id=$arrRet['doc_id'];
			$imw_doc_name=$arrRet['doc_name'];

			if($imw_doc_id<=0){
				$resultArr['success']=0;
				$resultArr['error_code']=207;
				$resultArr['error_description']='Parameter doctor id required';
				return $resultArr;
			}			
		}
		
		//GET IMW FACILITY ID
		if($postArrReceived['location_id']>0){
			$arrRet=$this->get_api_loc_id($postArrReceived['location_id']);
			$imw_loc_id=$postArrReceived['location_id'];
			$imw_loc_name=$arrRet['loc_name'];

			if($imw_loc_id<=0){
				$resultArr['success']=0;
				$resultArr['error_code']=209;
				$resultArr['error_description']='Parameter location id required';
				return $resultArr;
			}
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=209;
			$resultArr['error_description']='Parameter location id required';
			return $resultArr;
		}


		//CHECK DATE
		//GETTING DAYS DIFFERENCE
		$t1= strtotime($postArrReceived['schedule_from_date']);
		$t2= strtotime($postArrReceived['schedule_to_date']);

		$date_diff= $t2 - $t1;
		$mins= $date_diff/60;
		$hours= $mins/60;
		$diff_days= $hours/24;
		
		
		if(empty($postArrReceived['schedule_from_date'])==true || empty($postArrReceived['schedule_to_date'])==true){
			$resultArr['success']=0;
			$resultArr['error_code']=213;
			$resultArr['error_description']='Parameter schedule date required';
			return $resultArr;
		}else if(strtotime($postArrReceived['schedule_from_date']) > strtotime($postArrReceived['schedule_to_date'])){
			
			$resultArr['success']=0;
			$resultArr['error_code']=216;
			$resultArr['error_description']='"Schedule from date" should be less than "schedule to date".';
			return $resultArr;
		}
		if($diff_days>90){
			$resultArr['success']=0;
			$resultArr['error_code']=217;
			$resultArr['error_description']='Date difference should not greater than 90 days';
			return $resultArr;
		}
		

		//FINAL FUNCTION
		if((($postArrReceived['doctor_id']>0 && $imw_doc_id>0) || $postArrReceived['doctor_id']<=0)
		&& $imw_loc_id>0 && empty($postArrReceived['schedule_from_date'])==false  && empty($postArrReceived['schedule_to_date'])==false){

			//MAKE ARRAY IF REQUESTED FOR MULTI DATES
			$arrScheduleDates[]= $postArrReceived['schedule_from_date'];
			if($diff_days>0){
				for($i=1; $i<=$diff_days; $i=$i+1){
					$previousDate= $arrScheduleDates[count($arrScheduleDates)-1];
					$newDate = date('Y-m-d', strtotime($previousDate.' + 1 day'));
					$arrScheduleDates[]=$newDate;
				}
			}	
	
			
			foreach($arrScheduleDates as $scheduleDate){
				$matches=array();
				
				//GETTING CURRENT SOURCE DIRECTORY
				$realDir=getcwd(); 
				$baseDataPath=data_path();
				$dir =  $baseDataPath."scheduler_common/load_xml";
				chdir($dir);
			
				//GETTING ALL .SCH FILES OF GIVEN DATE			
				if($postArrReceived['doctor_id']>0){
					$matches[]= $scheduleDate.'-'.$imw_doc_id.'.sch';				
				}else{
					$matches = glob($scheduleDate."-[0-9]*.sch");
				}
				
				//COMING BACK TO SOURCE DIRECTORY
				chdir($realDir);
	
				
				foreach($matches as $file_name){
					$arrBookedTimes=array();
					$arrCustomLabelTime=array();
					$arrCustomLabels=array();
					$arrBlockedTimes=array();

					if(file_exists($baseDataPath."scheduler_common/load_xml/".$file_name)){
						$serializeData=file_get_contents($baseDataPath."scheduler_common/load_xml/".$file_name);
						$arrTimeSlots=unserialize($serializeData);
		
						if(sizeof($arrTimeSlots)>0){
							$cnt=0;
							$defTimeSec= $this->defTimeSlot * 60;
							
							//IT MAY THAT PRAMETER DOCTOR ID NOT RECEIVED
							$partArr= explode('-', str_replace('.sch', '', $file_name));
							$imw_doc_id = $partArr[count($partArr)-1];
							
							//GET API DOC ID
							if($postArrReceived['doctor_id']<=0 || $postArrReceived['doctor_id']==''){
								$arrRet=$this->get_api_doc_id($imw_doc_id);
								$api_doc_id=$arrRet['doc_id'];
								$imw_doc_name=$arrRet['doc_name'];
							}
							
							//GET BOOKED APPOINTMENTS
							$qry="SELECT id, sa_doctor_id, sa_facility_id, DATE_FORMAT(sa_app_starttime,'%H:%i') as 'sa_app_starttime', 
							DATE_FORMAT(sa_app_endtime,'%H:%i') as 'sa_app_endtime'
							FROM schedule_appointments 
							WHERE sa_patient_app_status_id NOT IN('18','201','203','271') 
							AND sa_app_start_date='".$scheduleDate."' 
							AND sa_doctor_id= '".$imw_doc_id."'
							AND sa_facility_id= '".$imw_loc_id."'";
							$rs=imw_query($qry);
							while($res=imw_fetch_assoc($rs)){
								$doc_id=$res['sa_doctor_id'];
								$fac_id=$res['sa_facility_id'];
								
								$t1=strtotime($res['sa_app_starttime']);
								$t2=strtotime($res['sa_app_endtime']);
								$diff=$t2-$t1;
								
								//IF MULTIPLE TIME SLOTS AVAILABLE BETWEEN START AND END TIME
								if($diff>$defTimeSec){
									$cnt=0;
									$oldStartTime=$res['sa_app_starttime'];
									
									for($i=$t1; $i<$t2;){
										$i=$i+$defTimeSec;
										$startTime=$oldStartTime;								 
										$endTime=date('H:i', $i);
										
										//IF SLOT TIME IS BETWEEN MID OF SLOT
										//if($i > $t2){
											//$remainder= ($diff/60) % $this->defTimeSlot;
											//$endTime= date('H:i', $i - ($remainder * 60));
											//$arrBookedTimes[$doc_id][$fac_id][$startTime][$endTime]= $startTime;
											//break;
										//}
										
										$arrBookedTimes[$doc_id][$fac_id][$startTime][$endTime]= $startTime;								
										
										$oldStartTime=$endTime;
									}
								}else{
									$endTime=$res['sa_app_endtime'];
									if($diff<$defTimeSec){
										$endTime= date('H:i', strtotime($res['sa_app_starttime']) + $defTimeSec);
									}
										
									$arrBookedTimes[$doc_id][$fac_id][$res['sa_app_starttime']][$endTime]= $res['sa_app_starttime'];							
								}
							}
		
							//GET CUSTOM LABEL TIMMINGS ADDED FROM FRONT DESK
							$qry="SELECT provider, facility, DATE_FORMAT(start_time,'%H:%i') as 'start_time',
							DATE_FORMAT(end_time,'%H:%i') as 'end_time', l_type, l_show_text
							FROM scheduler_custom_labels 
							WHERE system_action='0' and provider ='".$imw_doc_id."' 
							AND facility='".$imw_loc_id."' AND start_date='".$scheduleDate."'";
							//AND LOWER(l_type) IN('reserved', 'lunch', 'blocked', 'locked')";
							$arrayLabels=array('reserved'=>'reserved', 'lunch'=>'lunch', 'blocked'=>'blocked', 'locked'=>'locked');
							$rs=imw_query($qry);
							while($res=imw_fetch_assoc($rs)){
								$doc_id=$res['provider'];
								$fac_id=$res['facility'];
								
								if($arrayLabels[strtolower($res['l_type'])]){
									$arrCustomLabelTime[$doc_id][$fac_id][$res['start_time']][$res['end_time']]=$res['start_time'];
								}else if($res['l_show_text']!=''){
									$arrCustomLabels[$doc_id][$fac_id][$res['start_time']][$res['end_time']]['label']=$res['l_show_text'];
									$arrCustomLabels[$doc_id][$fac_id][$res['start_time']][$res['end_time']]['label_type']=$res['l_type'];
								}
							}
		
							//GET BLOCKED TIMMINGS
							$qry="SELECT provider, facility, DATE_FORMAT(start_time,'%H:%i') as 'start_time', DATE_FORMAT(end_time,'%H:%i') as 'end_time'
							FROM block_times 
							WHERE provider ='".$imw_doc_id."' 
							AND facility='".$imw_loc_id."' AND start_date='".$scheduleDate."' 
							AND LOWER(time_status)='block'";
							$rs=imw_query($qry);
							while($res=imw_fetch_assoc($rs)){
								$doc_id=$res['provider'];
								$fac_id=$res['facility'];
								
								$defTimeSec= $this->defTimeSlot * 60;
								
								$t1=strtotime($res['start_time']);
								$t2=strtotime($res['end_time']);
								$diff=$t2-$t1;
								
								//IF MULTIPLE TIME SLOTS AVAILABLE BETWEEN START AND END TIME
								if($diff>$defTimeSec){
									$oldStartTime=$res['start_time'];
									$i=0;
									for($i=$t1; $i<$t2;){
										$i=$i+$defTimeSec;
										$startTime=$oldStartTime;								 
										$endTime=date('H:i', $i);
		
										$arrBlockedTimes[$doc_id][$fac_id][$startTime][$endTime]= $startTime;								
										
										$oldStartTime=$endTime;
									}
								}else{
									$arrBlockedTimes[$doc_id][$fac_id][$res['start_time']][$res['end_time']]= $res['start_time'];							
								}
		
							}
					
							//FETCHING SLOTS
							foreach($arrTimeSlots as $arrData){
								
								foreach($arrData['slots'] as $timmings => $arrApptInfo){
									$fac_id=$arrApptInfo["fac_id"];
		
									if($imw_loc_id==$fac_id){
		
										$label_type= strtolower($arrApptInfo["label_type"]);
										list($startTime, $endTime) = explode('-', $arrApptInfo["timing"]);
										
										//SLOT SHOULD NOT BOOKED AND LABELLED
										if($label_type!="reserved" && $label_type!="lunch" && $label_type!="blocked" && $label_type!="locked"
										&& !$arrBookedTimes[$imw_doc_id][$fac_id][$startTime][$endTime]
										&& !$arrCustomLabelTime[$imw_doc_id][$fac_id][$startTime][$endTime]
										&& !$arrBlockedTimes[$imw_doc_id][$fac_id][$startTime][$endTime]){
											
											$labels=$label_types='';
											$templblArr=array();
											$templbltypeArr=array();
											$customlbl=$arrCustomLabels[$doc_id][$fac_id][$startTime][$endTime]['label'];
											$customlbltype=$arrCustomLabels[$doc_id][$fac_id][$startTime][$endTime]['label_type'];
										
											//CHECK CUSTOM LABEL
											if($customlbl!='')$templblArr[$customlbl]=$customlbl;
											if($customlbltype!='')$templbltypeArr[$customlbltype]=$customlbltype;
											//ADD XML LABEL IF CUSTOM LABLE NOT ADDED
											if($arrApptInfo["label_type"]!='' && $arrCustomLabels[$doc_id][$fac_id][$startTime][$endTime]['label_type']==''){
												$templbltypeArr[$arrApptInfo["label_type"]]=$arrApptInfo["label_type"];
												if($arrApptInfo["l_text"]!='')$templblArr[$arrApptInfo["l_text"]]=$arrApptInfo["l_text"];
											}
											$labels=implode(";", $templblArr);
											$label_types=implode(";", $templbltypeArr);

											
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['md_doctor_id']= $api_doc_id;
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['imw_doctor_id']= $imw_doc_id;
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['doctor_name']=$imw_doc_name;
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['location_id']=$imw_loc_id;
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['location_name']=$imw_loc_name;
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['start_date']=$scheduleDate;
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['start_time']=$startTime;
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['end_time']=$endTime;
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['label']=$labels;
											$resArr[$scheduleDate][$imw_doc_id][$cnt]['label_type']=$label_types;
		
											if($callFrom=='function'){
												$resArr[$scheduleDate][$imw_doc_id][$cnt]['tempId']=$arrApptInfo["tmpId"];
												$resArr[$scheduleDate][$imw_doc_id][$cnt]['l_text']=$arrApptInfo["l_text"];
												$resArr[$scheduleDate][$imw_doc_id][$cnt]['label_type']=$arrApptInfo["label_type"];
												$resArr[$scheduleDate][$imw_doc_id][$cnt]['color']=$arrApptInfo["color"];
											}
											
										
											$cnt++;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if(sizeof($resArr)>0){
			$resultArr['success']=1;
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=214;
			$resultArr['error_description']='Schedule not available for given doctor, location and for given date range';
		}
		
		$resultArr['result']=$resArr;
		return ($resultArr);
	}	
	
	public function get_doctors_availability($postArrReceived){
		$resultArr=array();
		$resArr=array();
		$imw_loc_id=2;
		
		//GET IMW FACILITY ID
		if($postArrReceived['location_id']>0){
			$arrRet=$this->get_api_loc_id($postArrReceived['location_id']);
			$imw_loc_id=$postArrReceived['location_id'];
			$api_loc_id=$arrRet['loc_id'];
			$imw_loc_name=$arrRet['loc_name'];

			if($imw_loc_id<=0){
				$resultArr['success']=0;
				$resultArr['error_code']=209;
				$resultArr['error_description']='Parameter location id required';
				return $resultArr;
			}
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=209;
			$resultArr['error_description']='Parameter location id required';
			return $resultArr;
		}

		if($postArrReceived['schedule_date']=='0000-00-00' || $postArrReceived['schedule_date']=='0' || empty($postArrReceived['schedule_date'])==true){
			$resultArr['success']=0;
			$resultArr['error_code']=213;
			$resultArr['error_description']='Parameter schedule date required';
			return $resultArr;
		}

		//GETTING FINAL DATA
		if($imw_loc_id>0){
			
			//GETTING CURRENT SOURCE DIRECTORY
			$realDir=getcwd(); 
			$baseDataPath=data_path();
			$dir =  $baseDataPath."scheduler_common/load_xml";
			chdir($dir);
		
			//GETTING ALL .SCH FILES OF GIVEN DATE			
			$matches = glob($postArrReceived['schedule_date']."-[0-9]*.sch");

			//COMING BACK TO SOURCE DIRECTORY
			chdir($realDir);

			if(is_array($matches) && !empty($matches)){
				$i=0; 
				foreach($matches as $matchedFile){
					$facFound=0;
					$serializeData=file_get_contents($dir.'/'.$matchedFile);
					$arrTimeSlots=unserialize($serializeData);
					
					//FETCHING SLOTS
					foreach($arrTimeSlots as $arrData){
						$doctor_id=$arrData['id'];
						$doctor_name=$arrData['hover_name'];
						$cnt=0;

						foreach($arrData['slots'] as $timmings => $arrApptInfo){
							$fac_id=$arrApptInfo["fac_id"];
							$fac_name=$arrApptInfo["fac_name"];
							
							if($imw_loc_id==$fac_id){
								$facFound=1;
								$label_type= strtolower($arrApptInfo["label_type"]);
								list($startTime, $endTime) = explode('-', $arrApptInfo["timing"]);

								//ARRAY FOR RETURN
								$resArr[$i]['imw_doctor_id']=$doctor_id;
								$resArr[$i]['doctor_name']=$doctor_name;
								$resArr[$i]['location_id']=$imw_loc_id;
								$resArr[$i]['location_name']=$fac_name;
								$resArr[$i]['schedule_date']=$postArrReceived['schedule_date'];
								if($cnt==0){
									$resArr[$i]['schedule_start_time']=$startTime;
								}
								$resArr[$i]['schedule_end_time']=$endTime;
								
								$cnt++;	
							}
						}
					}
					if($facFound==1){
						$i++;					
					}
				}
			}
		}
		
		if(sizeof($resArr)>0){
			$resultArr['success']=1;
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=215;
			$resultArr['error_description']='No doctor schedule available for given date and location';
			return $resultArr;
		}
		
		$resultArr['result']=$resArr;
		
		return $resultArr;
		
	}


	//
	public function get_appointments_info($postArrReceived){
		$resultArr=array();
		$resArr=array();
		$global_procedures=$global_procedures_ids='';
		
		$postArrReceived['schedule_from_date']=trim($postArrReceived['schedule_from_date']);
		$postArrReceived['schedule_to_date']=trim($postArrReceived['schedule_to_date']);
		$postArrReceived['procedures']=strtolower(trim($postArrReceived['procedures']));
		if(empty($postArrReceived['schedule_from_date'])==true || empty($postArrReceived['schedule_to_date'])==true){
			$resultArr['success']=0;
			$resultArr['error_code']=213;
			$resultArr['error_description']='Parameter schedule date required';
			return $resultArr;
		}
		
		if($postArrReceived['procedures']!='all'){
			if(sizeof($GLOBALS["API_PROCEDURES"])>0){
				$global_procedures= "'".implode("','", $GLOBALS["API_PROCEDURES"])."'";
				
				$qry="Select id FROM slot_procedures WHERE proc IN(".$global_procedures.")";
				$rs=imw_query($qry);
				while($res=imw_fetch_assoc($rs)){
					$arr_global_proc[$res['id']]= $res['id'];
				}
				if(sizeof($arr_global_proc)>0){
					$global_procedures_ids=  implode(',', $arr_global_proc);
				}
			}else{
				$resultArr['success']=0;
				$resultArr['error_code']=220;
				$resultArr['error_description']='No appointment procedures list available.';
				return $resultArr;
			}
		}
		
		$arrSchIds=array();
		$arrPatIds=array();
		$qry="Select id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_doctor_id, sa_facility_id, sa_patient_app_status_id, procedureid 
		FROM schedule_appointments WHERE (sa_app_start_date BETWEEN '".$postArrReceived['schedule_from_date']."' AND '".$postArrReceived['schedule_to_date']."')"; 
		if($postArrReceived['procedures']!='all'){
			$qry.=" AND procedureid IN(".$global_procedures_ids.")";
		}
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$arrSchIds[$res['id']]=$res['id'];
			$arrPatIds[$res['sa_patient_id']]=$res['sa_patient_id'];
			$arrApptUpdatedInfo[$res['id']]=$res;
		}
		
		if(sizeof($arrSchIds)>0){
			$arrAllDoctors= $this->get_all_doctors();
			$arrAllLocations= $this->get_all_locations();

			$strSchIds=implode(',', $arrSchIds);
			//$postArrReceived['last_modified_from_date']=trim($postArrReceived['last_modified_from_date']);
			//$postArrReceived['last_modified_to_date']=trim($postArrReceived['last_modified_to_date']);

			$qry="Select patient_id, sch_id, old_status, old_date, old_time, old_provider, old_facility, old_procedure_id   
			FROM previous_status WHERE sch_id IN(".$strSchIds.")";
/*			if(empty($postArrReceived['last_modified_from_date'])==false && empty($postArrReceived['last_modified_to_date'])==false){
				$qry.=" AND (status_date BETWEEN '".$postArrReceived['last_modified_from_date']."' AND '".$postArrReceived['last_modified_to_date']."')";
			}
*/			$qry.=" ORDER BY sch_id, id DESC";
			$rs=imw_query($qry);
			$numRows=imw_num_rows($rs);
			$oldSchId=$cnt=$allcnt=0;
			while($res=imw_fetch_assoc($rs)){
				$sch_id=$res['sch_id'];
				$arrApptAddedInfo[$sch_id]=$res;
			}
			
			//GET TOTAL OUTSTANDING BALANCE FOR PATIENT
			$arrPatBalance=array();
			if(sizeof($arrPatIds)>0){
				$strPatIds=implode(',', $arrPatIds);
				$qry_bal="Select chg.patient_id, chg_det.pat_due FROM patient_charge_list chg 
				JOIN patient_charge_list_details chg_det ON chg_det.charge_list_id=chg.charge_list_id 
				WHERE chg.patient_id IN(".$strPatIds.") AND chg_det.del_status='0'";
				$rs_bal=imw_query($qry_bal);
				while($res_bal=imw_fetch_assoc($rs_bal)){
					$arrPatBalance[$res_bal['patient_id']]+=$res_bal['pat_due'];
				}
			}			
			

			//FINAL ARRAY	
			foreach($arrApptUpdatedInfo as $sch_id => $updatedInfo){
				$addedInfo=array();
				$resArr[$cnt]['imw_patient_id']=$updatedInfo['sa_patient_id'];
				$resArr[$cnt]['imw_appt_id']=$sch_id;
				$addedInfo= $arrApptAddedInfo[$sch_id];
				
					
				//APPT DATE
				if($updatedInfo['sa_app_start_date']!= $addedInfo['old_date']){
					$resArr[$cnt]['old_start_date']=$addedInfo['old_date'];
				}
				$resArr[$cnt]['start_date']=$updatedInfo['sa_app_start_date'];
				
				//APPT TIME
				if($updatedInfo['sa_app_starttime']!= $addedInfo['old_time']){
					$resArr[$cnt]['old_start_time']=$addedInfo['old_time'];
				}
				$resArr[$cnt]['start_time']=$updatedInfo['sa_app_starttime'];
				
				//PROVIDER
				if($updatedInfo['sa_doctor_id']!= $addedInfo['old_provider']){
					$resArr[$cnt]['imw_old_doctor_id']=$addedInfo['old_provider'];
				}
				$resArr[$cnt]['imw_doctor_id']=$updatedInfo['sa_doctor_id'];
				$resArr[$cnt]['doctor_name']=$arrAllDoctors[$updatedInfo['sa_doctor_id']];
				
				//FACILITY
				if($updatedInfo['sa_facility_id']!= $addedInfo['old_facility']){
					$resArr[$cnt]['imw_old_location_id']=$addedInfo['old_facility'];
				}
				$resArr[$cnt]['imw_location_id']=$updatedInfo['sa_facility_id'];
				$resArr[$cnt]['location_name']=$arrAllLocations[$updatedInfo['sa_facility_id']];

				//PROCEDURE
				if($updatedInfo['procedureid']!= $addedInfo['old_procedure_id']){
					$resArr[$cnt]['old_procedure_id']=$addedInfo['old_procedure_id'];
				}
				$resArr[$cnt]['procedure_id']=$updatedInfo['procedureid'];
				
				//STATUS
				if($updatedInfo['sa_patient_app_status_id']!= $addedInfo['old_status']){
					if($this->arrStatusMapping[$addedInfo['old_status']]){
						$resArr[$cnt]['old_status']= $this->arrStatusMapping[$addedInfo['old_status']];
					}else{
						$resArr[$cnt]['old_status']= $this->arrOtherStatusMapping[$addedInfo['old_status']];
					}
				}
				
				if($this->arrStatusMapping[$updatedInfo['sa_patient_app_status_id']]){
					$resArr[$cnt]['status']= $this->arrStatusMapping[$updatedInfo['sa_patient_app_status_id']];
				}else{
					$resArr[$cnt]['status']= $this->arrOtherStatusMapping[$updatedInfo['sa_patient_app_status_id']];
				}

				//PATIENT BALANCE
				$resArr[$cnt]['outstanding_amount']=($arrPatBalance[$updatedInfo['sa_patient_id']]>0) ? $arrPatBalance[$updatedInfo['sa_patient_id']] : 0;
				
				$cnt++;
			}
		}
				
		if(sizeof($resArr)>0){
			$resultArr['success']=1;
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=219;
			$resultArr['error_description']='No record found.';
			return $resultArr;
		}
		
		$resultArr['result']=$resArr;
		
		return $resultArr;
		
	}

	//UPDATE APPOINMENT STATUS
	function update_appointment($postArrReceived){
		$resultArr=array();
		$resArr=array();
		$imw_pat_id=$imw_doc_id=$imw_loc_id=$imw_visit_id=0;
		
		if(empty($postArrReceived['patient_id'])==true){
			$resultArr['success']=0;
			$resultArr['error_code']=205;
			$resultArr['error_description']='Parameter patient id required.';
			return $resultArr;
		}	

		if(empty($postArrReceived['appt_date'])==true && empty($postArrReceived['imw_appt_id'])==true){
			$resultArr['success']=0;
			$resultArr['error_code']=221;
			$resultArr['error_description']='Patient id, appointment date or imw appointment id required.';
			return $resultArr;
		}	

		//UPDATE APPOINTMENT
		if($postArrReceived['imw_appt_id']>0 || ($postArrReceived['patient_id']>0 && empty($postArrReceived['appt_date'])==false)){
			$arrStatusMapping_flip= array_flip($this->arrStatusMapping);	
			$arrStatusMapping_flip['D']=11;		//ASSIGN ONLY CHECKED OUT TO COMPLETED APPT.
			$received_appt_status= strtoupper(trim($postArrReceived['appt_status']));

			if(array_key_exists($received_appt_status, $arrStatusMapping_flip)){
				$qry_part='';
				$received_appt_status_id=$arrStatusMapping_flip[$received_appt_status];
				
				//GET ID OF IMWDEV USER
				$qry="Select id FROM users WHERE username='imwdev' OR username='imwhd' ORDER BY username LIMIT 1";
				$rs=imw_query($qry);
				$res=imw_fetch_assoc($rs);
				$imwdev_id=$res['id'];
				
				$status_name=$this->arrOtherStatusMapping[$received_appt_status_id];

				if($postArrReceived['imw_appt_id']>0){
					$qry_part=" AND id='".$postArrReceived['imw_appt_id']."' AND sa_patient_id='".$postArrReceived['patient_id']."'";
				}else if(empty($postArrReceived['appt_date'])==false){
					$qry_part=" AND sa_app_start_date='".$postArrReceived['appt_date']."' AND sa_patient_id='".$postArrReceived['patient_id']."'";
				}
				
				//GETTING EXISTING COMMENT DATA
				$existing_comment=$sa_comment='';
				$qry="Select sa_comments FROM schedule_appointments WHERE 1=1 ".$qry_part." LIMIT 1";
				$rs1=imw_query($qry);
				$res1=imw_fetch_assoc($rs1);
				$existing_comment=trim($res1['sa_comments']);
				
				//$new_comment='Status updated to '.$status_name.' by API';
				$qry_comm_part='';
				$new_comment= trim(addslashes($postArrReceived['appt_comment']));
				
				if($new_comment!='' && empty($new_comment)==false){ 
					$sa_comment=(empty($existing_comment)==false)? $existing_comment.' '.$new_comment : $new_comment;
					$qry_comm_part=",sa_comments='".$sa_comment."'";
				}
				
				$qry="UPDATE schedule_appointments SET 
				sa_patient_app_status_id='".$received_appt_status_id."',
				status_update_operator_id='".$imwdev_id."', 
				sa_app_time='".date('Y-m-d H:i:s')."',
				sa_madeby='".$imwdev_id."'
				".$qry_comm_part."
				WHERE 1=1";
				$qry.=$qry_part." LIMIT 1";

				if($rs=imw_query($qry)){
					
					
					$qry="Select * FROM schedule_appointments WHERE 1=1 ".$qry_part." LIMIT 1";
					$rs1=imw_query($qry);
					$num_rows=imw_num_rows($rs1);
					$res1=imw_fetch_assoc($rs1);
					
					$qry_prev="Select status, statusChangedBy FROM previous_status WHERE sch_id='".$res1['id']."' ORDER BY id DESC LIMIT 1";
					$rs_prev=imw_query($qry_prev);
					$res_prev=imw_fetch_assoc($rs_prev);

					if($num_rows>0){
						
						//UPDATE TO PREVIOUS STATUS TABLE
						$t=$qry2="Insert INTO previous_status SET 
						sch_id='".$res1['id']."',
						patient_id='".$res1['sa_patient_id']."',
						status_time='".date('H:i:s')."',
						status_date='".date('Y-m-d')."',
						status='".$received_appt_status_id."',
						old_date='".$res1['sa_app_start_date']."',
						old_time='".$res1['sa_app_starttime']."',
						old_provider='".$res1['sa_doctor_id']."',
						old_facility='".$res1['sa_facility_id']."',
						statusComments='".$res1['sa_comments']."',
						oldMadeBy='".$res_prev['statusChangedBy']."',
						statusChangedBy='imwdev',
						dateTime='".date('Y-m-d H:i:s')."',
						new_facility='".$res1['sa_facility_id']."',
						new_provider='".$res1['sa_doctor_id']."',
						old_status='".$res_prev['status']."',
						new_appt_date='".$res1['sa_app_start_date']."',
						new_appt_start_time='".$res1['sa_app_starttime']."',
						old_appt_end_time='".$res1['sa_app_endtime']."',
						new_appt_end_time='".$res1['sa_app_endtime']."',
						old_procedure_id='".$res1['procedureid']."',
						old_sec_procedure_id	='".$res1['sec_procedureid']."',
						old_ter_procedure_id='".$res1['tertiary_procedureid']."',
						oldStatusComments='".$res1['sa_comments']."',
						new_procedure_id='".$res1['procedureid']."',
						new_sec_procedure_id='".$res1['sec_procedureid']."',
						new_ter_procedure_id='".$res1['tertiary_procedureid']."'";
						$rs2=imw_query($qry2);

						//UPDATING LABELS IN CASE OF CANCEL AND TO-DO-RESCHEDULED	
						if($received_appt_status_id==18 || $received_appt_status_id==201)
						{
							
							$q = "SELECT sa_doctor_id, sa_facility_id, sa_app_start_date, sa_app_starttime, sa_app_endtime FROM schedule_appointments WHERE id = '".$res1['id']."'";
							$r = imw_query($q) or die(imw_error());	
							$a = imw_fetch_array($r);
							$hv_appt_data=true;
							$sttm = strtotime($a["sa_app_starttime"]);
							$edtm = strtotime($a["sa_app_endtime"]);
	
							for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60)){
								$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);
	
								$start_loop_time = date("H:i:00", $looptm);
								$end_loop_time = date("H:i:00", $edtm2);
	
								$q2 = "SELECT id, provider, facility, start_date, start_time, end_time, labels_replaced, l_text, l_show_text FROM scheduler_custom_labels WHERE provider = '".$a["sa_doctor_id"]."' AND facility = '".$a["sa_facility_id"]."' AND start_date = '".$a["sa_app_start_date"]."' AND start_time = '".$start_loop_time."' AND end_time = '".$end_loop_time."'";
								$r2 = imw_query($q2);
								while($row = imw_fetch_assoc($r2)){
									$new_entry = $row["labels_replaced"];
									$l_text = $row["l_show_text"];
									$lbl_record_id = trim($row['id']);
									$lbl_replaced = trim($row['labels_replaced']);
									if(trim($row["labels_replaced"]) != ""){ 
										$arr_lbl_replaced = explode("::", $row["labels_replaced"]);
										if(count($arr_lbl_replaced) > 0){ 
											foreach($arr_lbl_replaced as $this_lbl_replaced){
												$arr_this_replaced2 = explode(":", $this_lbl_replaced);
												if(trim($arr_this_replaced2[0]) == $res1['id']){ 
													$new_entry = str_replace("::".$arr_this_replaced2[0].":".$arr_this_replaced2[1], "", $row["labels_replaced"]);
	
													if(trim($row["l_show_text"]) != ""){
														$l_text = $row["l_show_text"]."; ".$arr_this_replaced2[1];
													}else{
														$l_text = $arr_this_replaced2[1];
													}
													$upd22 = "UPDATE scheduler_custom_labels SET l_show_text = '".$l_text."', labels_replaced = '".$new_entry."' WHERE id =	'".$row["id"]."'";
													imw_query($upd22);
												}
											}
										}
									}
								}
							}
						}						
					}
					
					$resArr['success']=1;
					$resArr['response']='Appointment status updated successfully.';					
				}

			}else{
				$resultArr['success']=0;
				$resultArr['error_code']=222;
				$resultArr['error_description']='Valid appointment status required.';
				return $resultArr;
			}
		}
			
		$resultArr['result']=$resArr;
		return ($resultArr);		
	}
	
	public function get_statement($postArrReceived){
		$resultArr=array();
		$resArr=array();
		
		$postArrReceived['patient_id']=trim($postArrReceived['patient_id']);
		if(empty($postArrReceived['patient_id'])==true){
			$resultArr['success']=0;
			$resultArr['error_code']=205;
			$resultArr['error_description']='Parameter patient id required';
			return $resultArr;
		}
		
		$arrSchIds=array();
		$arrPatIds=array();
		$html_data='';
		$statement=0;
		$qry="Select st.patient_id, st.created_date, st_det.statement_data FROM previous_statement st
		JOIN previous_statement_detail st_det ON st_det.previous_statement_id=st.previous_statement_id
		WHERE st.patient_id='".$postArrReceived['patient_id']."' ORDER BY st.previous_statement_id DESC LIMIT 1"; 
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$statement=1;
			$statement_date=$res['created_date'];
			$html_data=$res['statement_data'];
		}
		
		if($statement==1){
			//GET TOTAL OUTSTANDING BALANCE FOR PATIENT
			$patient_balance=0;
			$qry_bal="Select chg.patient_id, SUM(chg_det.pat_due) as 'pat_due' FROM patient_charge_list chg 
			JOIN patient_charge_list_details chg_det ON chg_det.charge_list_id=chg.charge_list_id 
			WHERE chg.patient_id='".$postArrReceived['patient_id']."' AND chg_det.del_status='0'";
			$rs_bal=imw_query($qry_bal);
			$res_bal=imw_fetch_assoc($rs_bal);
			$patient_balance=$res_bal['pat_due'];

			if(empty($html_data)==false){
				$now   = time();
				$file_location= write_html(html_entity_decode($html_data), 'statement_api_'.$now.'.html');	
				
				if(file_exists($file_location)){
					$fileInfo = pathinfo($file_location);
					$html_file_name = $fileInfo['filename'];
					$filePath = $fileInfo['dirname'].'/'.$html_file_name.'.pdf';
					$filePath=$this->createSavePDF($filePath, $file_location, 'p');

					if(file_exists($filePath)){
						$file_code=file_get_contents($filePath)	;
						$file_code64=base64_encode($file_code);
						unlink($filePath);

						//MAKING FINAL ARRAY	
						$resArr['patient_id']=$postArrReceived['patient_id'];
						$resArr['statement_date']=$statement_date;
						$resArr['outstanding_amount']=($patient_balance>0) ? $patient_balance : 0;
						$resArr['pdf_file']=$file_code64;
					}
				}
			}else{
				$resultArr['success']=0;
				$resultArr['error_code']=219;
				$resultArr['error_description']='No record found';
				return $resultArr;
			}
		}
				
		if(sizeof($resArr)>0){
			$resultArr['success']=1;
		}else{
			$resultArr['success']=0;
			$resultArr['error_code']=219;
			$resultArr['error_description']='No record found.';
			return $resultArr;
		}
		
		$resultArr['result']=$resArr;
		
		return $resultArr;
		
	}

	//SUPPORT FUNCTIONS
	public function get_imw_pat_id($pat_id){
		$imw_pat_id=0;
		$patient_name='';
		$arrRet=array();
		$qry="Select id, CONCAT(lname,', ',fname,' ',mname) as patient_name FROM patient_data WHERE api_id='".$pat_id."'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);
			$imw_pat_id=$res['id'];
			$patient_name=trim($res['patient_name']);
		}
		$arrRet['pat_id']= $imw_pat_id;
		$arrRet['pat_name']= $patient_name;
		
		return $arrRet;
	}
	public function get_api_pat_id($pat_id){
		$api_pat_id=0;
		$patient_name='';
		$arrRet=array();
		$qry="Select api_id, CONCAT(lname,', ',fname,' ',mname) as patient_name FROM patient_data WHERE id='".$pat_id."'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);
			$api_pat_id=$res['api_id'];
			$patient_name=trim($res['patient_name']);
		}
		$arrRet['pat_id']= $api_pat_id;
		$arrRet['pat_name']= $patient_name;
		
		return $arrRet;
	}	
	public function get_imw_doc_id($doc_id){
		$imw_doc_id=0;
		$doc_name='';
		$arrRet=array();
		$qry="Select id, CONCAT(lname, ' ', fname) as doc_name FROM users WHERE api_id='".$doc_id."'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);
			$imw_doc_id=$res['id'];
			$doc_name=trim($res['doc_name']);
		}
		
		$arrRet['doc_id']= $imw_doc_id;
		$arrRet['doc_name']= $doc_name;
		
		return $arrRet;
	}
	
	public function get_api_doc_id($doc_id){
		$api_doc_id=0;
		$doc_name='';
		$arrRet=array();
		$qry="Select api_id, CONCAT(lname, ' ', fname) as doc_name FROM users WHERE id='".$doc_id."'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);
			$api_doc_id=$res['api_id'];
			$doc_name=trim($res['doc_name']);
		}
		
		$arrRet['doc_id']= $api_doc_id;
		$arrRet['doc_name']= $doc_name;
		
		return $arrRet;
	}	
	
	public function get_imw_loc_id($loc_id){
		$imw_loc_id=0;
		$loc_name='';
		$arrRet=array();
		$qry="Select id, name FROM facility WHERE api_id='".$loc_id."'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);
			$imw_loc_id=$res['id'];
			$loc_name=trim($res['name']);
		}
		
		$arrRet['loc_id']= $imw_loc_id;
		$arrRet['loc_name']= $loc_name;
		
		return $arrRet;
	}

	public function get_api_loc_id($loc_id){
		$api_loc_id=0;
		$loc_name='';
		$arrRet=array();
		$qry="Select api_id, name FROM facility WHERE id='".$loc_id."'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);
			$api_loc_id=$res['api_id'];
			$loc_name=trim($res['name']);
		}
		
		$arrRet['loc_id']= $api_loc_id;
		$arrRet['loc_name']= $loc_name;
		
		return $arrRet;
	}	
	
	public function get_imw_visit_id($visit_id){
		$imw_visit_id=0;
		$visit_name='';
		$arrRet=array();
		$qry="Select id, proc FROM slot_procedures WHERE api_id='".$visit_id."'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);
			$imw_visit_id=$res['id'];
			$visit_name=trim($res['proc']);
		}
		
		$arrRet['visit_id']= $imw_visit_id;
		$arrRet['visit_name']= $visit_name;
		
		return $arrRet;
	}

	public function get_api_visit_id($visit_id){
		$api_visit_id=0;
		$visit_name='';
		$arrRet=array();
		$qry="Select api_id, proc FROM slot_procedures WHERE id='".$visit_id."'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);
			$api_visit_id=$res['api_id'];
			$visit_name=trim($res['proc']);
		}
		
		$arrRet['visit_id']= $api_visit_id;
		$arrRet['visit_name']= $visit_name;
		
		return $arrRet;
	}
	
	//ALL ARRAY FUNCTIONS
	public function get_all_doctors(){
		$arrRet=array();
		$qry="Select id, CONCAT(lname, ' ', fname) as doc_name FROM users";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$arrRet[$res['id']]= $res['doc_name'];
		}
		
		return $arrRet;
	}	
	public function get_all_locations(){
		$arrRet=array();
		$qry="Select id, name FROM facility";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);
			$arrRet[$res['id']]= $res['name'];
		}
		
		return $arrRet;
	}	

	//CREATE PDF FROM HTML
	function createSavePDF($filePath='', $html_file_name, $op='l'){
		global $GLOBALS;
		
		$GLOBALS['php_server']=str_replace('localhost', 'localhost:8080', $GLOBALS['php_server']); //ONLY FOR LOCAL TESTING
		$webadd = $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php';
		
		$pdf_path = $filePath;
		$urlPdfFile	= $webadd."?setIgnoreAuth=true&saveOption=fax&onePage=undefined&op=l&file_location=".$html_file_name."&pdf_name=".$pdf_path;
		$curNew = curl_init();
		curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
		curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
		$data = curl_exec($curNew);
		
		curl_close($curNew);
		if(file_exists($html_file_name)){
			unlink($html_file_name);
		}

		return $filePath;
	}	

}// END CLASS

$objResponse=new responseClass(DEFAULT_TIME_SLOT);
//$result=$objResponse->get_patient_details(array('patient_id'=>'34449'));
/*$fields = array(
		'patient_id' => '74367',	
		'imw_appt_id' => '52055',	//either imw_appt_id or appt_start_date		
		'appt_date' => '2019-03-29', 	//either appt_start_date or imw_appt_id
		'appt_status' => 'D' 	
		);

$result=$objResponse->update_appointment($fields);*/
//$result=$objResponse->get_available_times(array('doctor_id'=>89, 'location_id'=>1, 'schedule_from_date'=>'2017-02-06', 'schedule_to_date'=>'2017-02-06'));
//$result=$objResponse->get_doctors_availability(array('location_id'=>101, 'schedule_date'=>'2016-11-02'));	
//$result=$objResponse->book_appointment(array('patient_id'=>205, 'appt_start_date'=>'2016-11-02', 'appt_start_time'=>'09:40', 'visit_type_id'=>601,'doctor_id'=>101, 'location_id'=>101));

//$result=$objResponse->get_appointments(array('patient_id'=>'68339', 'start_date'=>'2019-10-03', 'end_date'=>'2019-10-03'));
//$result=$objResponse->get_appointments_info(array('schedule_from_date'=>'2017-02-01', 'schedule_to_date'=>'2017-02-13'));
//pre($result);
?>