<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php 
/*
File: electronic_billing_functions.php
Coded in PHP 7
Purpose: Contains functions used in electronic billing.
Access Type: Include file 
*/
require_once(dirname(__FILE__)."/class.electronic_billing.php");

class ERXClass extends ElectronicBilling{
	public $copayPolicies,$cookie_path;
	public $patientId;
	function __construct(){ //constructor
		$this->authId 			= intval($_SESSION['authId']);
		$this->user_type		= intval($_SESSION['logged_user_type']);
		$this->patientId		= intval($_SESSION['patient']);
		$this->cookie_path 		= data_path().'users/UserId_'.$_SESSION['authId'].'/erx';
		if(!file_exists($this->cookie_path) && !is_dir($this->cookie_path)){mkdir($this->cookie_path,0777,true);}
	}
	
	/***********GET ErX STATUS AND URL***********/
	function get_erx_status_and_url(){
		$q = "select Allow_erx_medicare, EmdeonUrl from copay_policies LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs;
		}
		return false;
	}
	
	/***********GET Provider's erX details***********/
	function get_provider_erx_auth($providerid){
		$q = "SELECT eRx_user_name, erx_password, eRx_prescriber_id , lname, fname, mname 
				FROM users 
				WHERE id = '$providerid'";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs;
		}
		return false;
	}
	
	/***********GET PATIENT DETAILS************/
	function get_patient_details($cols='*'){
		if($patient_id=='') $patient_id=$this->patientId;
		$r = imw_query("SELECT $cols FROM patient_data WHERE id='$patient_id' LIMIT 1");
		if($r && imw_num_rows($r)==1) return imw_fetch_assoc($r);
		else return false;		
	}
	
	/***********GET PATIENT ERX REGISTRATION DETAILS******/
	function get_patient_erx_details_from_db($patient_id=''){
		if($patient_id=='') $patient_id=$this->patientId;
		$q = "SELECT * FROM patient_erx_prescription WHERE patient_eRx_Patient_id = '$patient_id'";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs;
		}else return false;
	}
	
	/************GET PATIENT ERX HSI PERSON DETAILS FROM EMDEON******/
	function put_patient_erx_details_to_db($url,$user,$pass,$facId,$patient_id){
		//--- Get eRx Patient Id ------
		$pt_details = $this->get_patient_details('erx_patient_id');
		$erx_patient_id = $pt_details['erx_patient_id'];
		$xml_data = "<?xml version='1.0' ?><REQUEST userid='$user' password='$pass' facility='$facId'><OBJECT name='person' op='search_hsi'><organization>$facId</organization><hsi_value>$patient_id</hsi_value></OBJECT></REQUEST>";
		$URL = "$url/servlet/XMLServlet";
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "request=$xml_data");
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		$lastError = curl_error($ch); 
		curl_close($ch);
		$this->write_erx_log($URL,$xml_data,$output,$lastError);

		//--- get object from xml data ---------- 
		$data = @simplexml_load_string($output);
		$ObjectArr = (array)$data->OBJECT;
		if(count($ObjectArr)>0){
			$dataArr = array();
			if(count($ObjectArr['@attributes'])>0){
				$dataArr['patient_eRx_prescription_attributes'] = join(',',$ObjectArr['@attributes']);
			}
			$dataArr['patient_eRx_first_name'] = $ObjectArr['first_name'];
			$dataArr['patient_eRx_last_name'] = $ObjectArr['last_name'];
			$dataArr['patient_eRx_middle_name'] = $ObjectArr['middle_name'];
			$dataArr['patient_eRx_last_name_soundex'] = $ObjectArr['last_name_soundex'];
			$ArrDOB	= preg_split('/\//',$ObjectArr['birth_date']);
			$m	= $ArrDOB[0];
			$d	= $ArrDOB[1];
			$y	= $ArrDOB[2];
			$dataArr['patient_eRx_birth_date'] = $y.'-'.$m.'-'.$d;
			$dataArr['patient_eRx_sex'] = $ObjectArr['sex'];
			$dataArr['patient_eRx_home_phone_number'] = $ObjectArr['home_phone_number'];
			$dataArr['patient_eRx_home_phone_area_code'] = $ObjectArr['home_phone_area_code'];
			$dataArr['patient_eRx_address_1'] = $ObjectArr['address_1'];
			$dataArr['patient_eRx_address_2'] = $ObjectArr['address_2'];
			$dataArr['patient_eRx_city'] = $ObjectArr['city'];
			$dataArr['patient_eRx_state'] = $ObjectArr['state'];
			$dataArr['patient_eRx_zip'] = $ObjectArr['zip'];
			$dataArr['patient_eRx_suffix'] = $ObjectArr['suffix'];
			$dataArr['patient_eRx_ssn'] = $ObjectArr['ssn'];		
			$dataArr['patient_eRx_person_hsi'] = $ObjectArr['person_hsi'];
			$dataArr['patient_eRx_leadperson'] = $ObjectArr['leadperson'];
			$dataArr['patient_eRx_person'] = $ObjectArr['person'];
			$dataArr['patient_eRx_label_name'] = $ObjectArr['label_name'];
			$dataArr['patient_eRx_hsi_value'] = $ObjectArr['hsi_value'];
			$dataArr['patient_eRx_Patient_id'] = $patient_id;
			$dataArr['patient_eRx_created_date'] = date('Y-m-d');
			$dataArr['patient_eRx_operator_id'] = $_SESSION['authId'];
			$dataArr['patient_eRx_display_status'] = '0';
			
			
			$existing_res	= $this->get_patient_erx_details_from_db($patient_id);
			//imw_query("SELECT patient_eRx_prescription_id FROM patient_erx_prescription WHERE patient_eRx_Patient_id = '$patient_id'");
			if(!$existing_res){
				$insertId = AddRecords($dataArr,'patient_erx_prescription');
			}
			else if($existing_res && is_array($existing_res) && count($existing_res)>0){
				$id = $existing_res['patient_eRx_prescription_id'];
				$insertId = UpdateRecords($id,'patient_eRx_prescription_id',$dataArr,'patient_erx_prescription');
			}
			return $this->get_patient_erx_details_from_db($patient_id);
		}
	}

	function write_erx_log($url,$request_data='',$response_data='',$error=''){
		$q = "INSERT INTO erx_log SET 
			  curl_url			= '".$url."', 
			  request_data		= '".addslashes($request_data)."', 
			  response_data		= '".addslashes($response_data)."', 
			  user_id			= '".$this->authId."', 
			  patient_id		= '".$this->patientId."', 
			  request_datetime	= '".date('Y-m-d H:i:s')."', 
			  error				= '".$error."'";
		imw_query($q);//echo $q.'<br>'.imw_error().'<hr>';
	}
	
	function get_patient_erx_prescription($url,$user,$pass,$facId,$patient_id,$person,$personhsi,$start_date){
		$cookie_file = $this->cookie_path.'/cookie_'.$user.'.txt';
		$cur = curl_init();
		$LoginURL = $url."/servlet/DxLogin?userid=".$user."&PW=".$pass."&target=html/LoginSuccess.html&testLogin=true";
		curl_setopt($cur, CURLOPT_URL,$LoginURL);
		curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
		$data = curl_exec($cur);
		curl_close($cur);
		preg_match('/Login Success/',$data,$loginSuccessArr);
		
		if(count($loginSuccessArr)){			
			$URL = "$url/servlet/DxLogin?userid=$user&PW=$pass&hdnBusiness=$facId";
			$URL.= "&target=servlet/servlets.apiRxServlet&actionCommand=patientrxhistory&apiLogin=true&textError=true&patient=$patient_id&rxStatus=Active";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$URL);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie_file);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$output = curl_exec($ch);
			$lastError = curl_error($ch); 
			curl_close($ch);
			$this->write_erx_log($URL,$xml_data,$output,$lastError);		
			preg_match("/<-- ERROR :/",$output,$errorArr);
			if(count($errorArr) == 0){
				$output = preg_replace('/<--BEGIN RX>/','',$output);
				$output = trim(preg_replace('/<--END RX>/','',$output));
				$erx_data_arr  = explode("^",$output);
				//pre($erx_data_arr);
			}

			if(count($erx_data_arr)>0){
				$arrData = array();
				foreach($erx_data_arr as $prescription){
					$arrTmp = array();
					$arr = explode('|',$prescription);
					if(is_array($arr) && count($arr)>15){
						$rx_transmission_status = $arr[0];
						$rx_index 				= $arr[1];// Index of record in resultset.
						$rx_issue_date			= $arr[2];// auth_Denied_Date.
						$rx_discontinued_date	= $arr[3];// modified date. Value if blank if rx_status is not equal to Void or Discontinued.
						$rx_issue_type 			= $arr[4];// Electronic / Hand Written
						$rx_prescriber 			= $arr[5];
						$rx_drug_name 			= str_replace(array("\r", "\n","<br>","<br/>","<br />"), '',$arr[6]);
						$rx_sig					= str_replace(array("\r", "\n","<br>","<br/>","<br />"), '',$arr[7]);
						$rx_quantity			= $arr[8];
						$rx_refill				= $arr[9];
						$rx_status				= $arr[10];// Active/Lapsed
						$rx_days_left			= $arr[11];
						$rx_type				= $arr[12];
						$rx_pharmacy_name		= $arr[13];
						$rx_pharmacy_address	= $arr[14];
						$rx_person				= $arr[15]; // Yes/No.
						$rx_auth_status			= $arr[16];// AUTHORIZED
						$rx_objid 				= $arr[17];// Internal identifier
						$rx_created_by			= $arr[18];// user who created erx.
	//					echo strtolower($rx_status)."=='active' && ".strtolower($rx_auth_status)."=='authorized' && ".strtolower($rx_issue_type)."=='electronic'".'<br>';
						if(strtolower($rx_status)=='active' && strtolower($rx_auth_status)=='authorized'){
							$arrTmp['erx_id'] = $rx_objid;
							$arrTmp['emdeon_rx_id'] = $rx_objid;
							$arrDate = explode(" ",$rx_issue_date);
							$arrDateTmp = explode("/",$arrDate[0]);
							$mm = core_padd_char($arrDateTmp[0],2);
							$dd = core_padd_char($arrDateTmp[1],2);
							$yyyy = core_padd_char($arrDateTmp[2],2);
							$arrTmp['begdate'] = $yyyy."-".$mm."-".$dd;
							if(trim($rx_discontinued_date)!=''){
								$temp_begin_dt_arr = explode(' ',trim($rx_discontinued_date));
								$temp_begin_dt = trim($temp_begin_dt_arr[0]);
								unset($temp_begin_dt_arr);
								if(strlen($temp_begin_dt) >= 8 && strlen($temp_begin_dt) <= 10 && strpos($temp_begin_dt,'/') > 0){
									$temp_begin_dt_array = explode('/',$temp_begin_dt);
									if(count($temp_begin_dt_array)==3){
										$mon = strlen($temp_begin_dt_array[0])==2 ? $temp_begin_dt_array[0] : '0'.$temp_begin_dt_array[0];
										$dat = strlen($temp_begin_dt_array[1])==2 ? $temp_begin_dt_array[1] : '0'.$temp_begin_dt_array[1];
										$yyy = $temp_begin_dt_array[2];
						//				$arrTmp['begdate'] = $yyy."-".$mon."-".$dat;
									}
								}
							}
							$arrTmp['erx_modified_date'] = $rx_discontinued_date;
							$arrTmp['title'] = $rx_drug_name;
							$arrTmp['sig'] = $rx_sig;
							$arrTmp['comments'] = $rx_sig;
							$arrTmp['destination'] = $rx_quantity;
							$arrTmp['user'] = $_SESSION['authId'];
							$arrTmp['allergy_status'] =  $rx_status;//($rx_auth_status == "AUTHORIZED")?"Active":$arr->rx_status;
							$arrData[] = $arrTmp;
						}
					}
				}
				return $arrData;
			}else return false;
		}else return false;
	}
	
	//Function below not in use now; code commit on December 10,2019.
	function get_patient_erx_prescription_xml($url,$user,$pass,$facId,$patient_id,$person,$personhsi,$start_date){
		$xml_data = "<?xml version='1.0'?><REQUEST userid='$user' password='$pass' facility='$facId'><OBJECT name='rx' op='search_patient_history_api'><organization>$facId</organization><defer>N</defer><person>$person</person><personhsi>$personhsi</personhsi><start_date>$start_date</start_date></OBJECT></REQUEST>";
		$URL = "$url/servlet/XMLServlet";
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "request=$xml_data");
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		$lastError = curl_error($ch); 
		curl_close($ch);
		$this->write_erx_log($URL,$xml_data,$output,$lastError);
		
		$arrXml = simplexml_load_string($output);
		if($arrXml){
			$arrData = array();
			foreach($arrXml->OBJECT as $key=>$arr){
				$arrTmp = array();
				$arrTmp['erx_id'] = $arr->drug_id;
				$arrTmp['emdeon_rx_id'] = $arr->rx;
				$arrDate = explode(" ",$arr->curr_date);
				$arrDateTmp = explode("/",$arrDate[0]);
				$mm = core_padd_char($arrDateTmp[0],2);
				$dd = core_padd_char($arrDateTmp[1],2);
				$yyyy = core_padd_char($arrDateTmp[2],2);
				$arrTmp['begdate'] = $yyyy."-".$mm."-".$dd;
				if(trim($arr->auth_denied_date)!=''){
					$temp_begin_dt_arr = explode(' ',trim($arr->auth_denied_date));
					$temp_begin_dt = trim($temp_begin_dt_arr[0]);
					unset($temp_begin_dt_arr);
					if(strlen($temp_begin_dt) >= 8 && strlen($temp_begin_dt) <= 10 && strpos($temp_begin_dt,'/') > 0){
						$temp_begin_dt_array = explode('/',$temp_begin_dt);
						if(count($temp_begin_dt_array)==3){
							$mon = strlen($temp_begin_dt_array[0])==2 ? $temp_begin_dt_array[0] : '0'.$temp_begin_dt_array[0];
							$dat = strlen($temp_begin_dt_array[1])==2 ? $temp_begin_dt_array[1] : '0'.$temp_begin_dt_array[1];
							$yyy = $temp_begin_dt_array[2];
							$arrTmp['begdate'] = $yyy."-".$mon."-".$dat;
						}
					}
				}
				$arrTmp['erx_modified_date'] = $arr->modified_date;
				$arrTmp['title'] = $arr->drug_name;
				$arrTmp['sig'] = $arr->sig;
				$arrTmp['comments'] = $arr->sig;
				$arrTmp['destination'] = $arr->quantity;
				$arrTmp['user'] = $_SESSION['authId'];
				$arrTmp['allergy_status'] =  ($arr->rx_status == "AUTHORIZED")?"Active":$arr->rx_status;
				$arrData[] = $arrTmp;
			}
			return $arrData;
		}else return false;
	}
	
	function get_patient_erx_allergy($url,$user,$pass,$facId,$patient_id,$person){
		$xml_data = "<?xml version='1.0' ?><REQUEST userid='$user' password='$pass' facility='$facId'><OBJECT name='personallergy' op='search_gui'><person>".$person."</person></OBJECT></REQUEST>";
		$URL = "$url/servlet/XMLServlet";
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "request=$xml_data");
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		$lastError = curl_error($ch); 
		curl_close($ch);
		$this->write_erx_log($URL,$xml_data,$output,$lastError);
		
		$arrXml = simplexml_load_string($output);
		if($arrXml){
			$arrData = array();
			foreach($arrXml->OBJECT as $key=>$arr){
				$arrTmp = array();
				$arrTmp['eRx_allergyId'] = $arr->allergy_id;
				$arrTmp['personallergy'] = $arr->personallergy;
				$arrDate = explode(" ",$arr->creation_date);
				$arrDateTmp = explode("/",$arrDate[0]);
				$mm = core_padd_char($arrDateTmp[0],2);
				$dd = core_padd_char($arrDateTmp[1],2);
				$yyyy = core_padd_char($arrDateTmp[2],2);
				$arrTmp['begdate'] = $yyyy."-".$mm."-".$dd;
				$arrTmp['title'] = $arr->allergy_name;
				$arrTmp['ag_occular_drug'] = $arr->type;
				$arrTmp['comments'] = $arr->description;
				$arrTmp['user'] = $_SESSION['authId'];
				$arrTmp['allergy_status'] = ($arr->expiration_date == "")?"Active":"Suspended";
				$arrData[] = $arrTmp;
			}
			return $arrData;
		}else return false;
	}
	
	/****GET DOWNLOADED PRESCRIPTION ARRAY AND PROCESS IT (SAVE/UPDATE, OPTIONAL RETURN ITEMS TO MAKE HTML)***/
	function process_erx_prescription($arrMedications){
		if($arrMedications){
			
			/*****Getting IDs of existing De-active Meds*********/
			$Deleted_IDs = $this->get_deleted_ids();
			
			
			/*****SUSPEND ALL EXISTING ERX MEDICATION FOR THIS PATIENT****/
			$this->discontinue_erx_meds();
			$ocular_med_array	= $this->get_admin_ocular_meds();
			
			foreach($arrMedications as $medication){
				$noRecord = false;
				$title = (is_object($medication->title))?$medication->title : $medication['title'];
				$title = str_replace('  ',' ',$title);
				
				$erx_id = (is_object($medication->erx_id))?$medication->erx_id : $medication['erx_id'];
				$emdeon_rx_id = (is_object($medication->emdeon_rx_id))?$medication->emdeon_rx_id : $medication['emdeon_rx_id'];
				
				$begdate = (is_object($medication->begdate))?$medication->begdate : $medication['begdate'];
				$erx_modified_date = (is_object($medication->erx_modified_date))?$medication->erx_modified_date : $medication['erx_modified_date'];
				$sig = (is_object($medication->sig))?$medication->sig : $medication['sig'];
				$comments = (is_object($medication->comments))?$medication->comments : $medication['comments'];
				$destination = (is_object($medication->destination))?$medication->destination : $medication['destination'];
				$user = (is_object($medication->user))?$medication->user : $medication['user'];
				$allergy_status = (is_object($medication->allergy_status))?$medication->allergy_status : $medication['allergy_status'];
				$allergy_status = ($allergy_status == "DISCONTINUED")?"Discontinue":$allergy_status;
				
				
				$arrUsrName = getUserDetails($this->authId,' fname,lname,mname');
				$fname = $arrUsrName['fname'];
				$lname = $arrUsrName['lname'];
				
				/****LOGIC BELOW TO SPLIT DOSAGE***/
				$drug_name_array = array(); $dosage_name_array = array(); $dosage = false;
				$temp_drug_name_array = explode(' ',$title);
				foreach($temp_drug_name_array as $temp_drug_name_part){
					$first_char_is_number = isset($temp_drug_name_part[0]) ? is_numeric($temp_drug_name_part[0]) : false;;
					if($first_char_is_number || count($dosage_name_array)>0){
						$dosage_name_array[] = $temp_drug_name_part;
					}else{
						$drug_name_array[] = $temp_drug_name_part;
					}
				}
				
				if(count($drug_name_array)>0){
					$title = implode(' ',$drug_name_array);
				}
				if(count($dosage_name_array)>0){
					$dosage = implode(' ',$dosage_name_array);
				}
				/*********Dosage split logi end*************/
				
				$insertArr = array();
				$insertArr['type'] = '1';// systemic
				if(in_array($title,$ocular_med_array)){
					$insertArr['type'] = '4';// ocular
				}
				$insertArr['compliant'] = '1';
				$insertArr['pid'] = $this->patientId;
				$insertArr['title'] = $title;
				if($begdate != "")$insertArr['begdate'] = $begdate;
				if($erx_modified_date != "")$insertArr['erx_modified_date'] = $erx_modified_date;
				if($sig != '')$insertArr['sig'] = $sig;
				
				$insertArr['allergy_status'] = $allergy_status;
				$insertArr['eRx_drug_status'] = 1;
				$insertArr['eRx_by'] = $this->authId;
				$insertArr['erx_id'] = $erx_id;
				$insertArr['emdeon_rx_id'] = $emdeon_rx_id;
				$insertArr['user'] = $user;
				if($comments != "")$insertArr['comments'] = $comments;
				if($dosage){
					$insertArr['destination'] = $dosage;
				}else if($destination != "" && !in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman','okei','mage'))){
					$insertArr['destination'] = $destination;
				}
				
				if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('shoreline'))){
					$q_existing = "SELECT id,erx_modified_date FROM lists WHERE type IN ('1','4') AND pid = '".$this->patientId."' AND title = '$title' AND begdate='$begdate' LIMIT 1";
				}else if(trim($erx_id)=='' && trim($emdeon_rx_id)!=''){
					$q_existing = "SELECT id,erx_modified_date FROM lists WHERE type IN ('1','4') AND pid = '".$this->patientId."' AND emdeon_rx_id = '$emdeon_rx_id' LIMIT 1";
				}else if(trim($erx_id)!=''){
					$q_existing = "SELECT id,erx_modified_date FROM lists WHERE type IN ('1','4') AND pid = '".$this->patientId."' AND erx_id = '$erx_id' LIMIT 1";
				}else{
					//IF no erx_id and emdeon_rx_id founf, then don't process this record; move to top.
					continue;
				}
				$res_existing 	= imw_query($q_existing);
				$id				= '';
				if($res_existing && imw_num_rows($res_existing)>0){
					$rs_existing	= imw_fetch_assoc($res_existing);
					$id = $rs_existing['id'];
				}
				
				if(empty($id) == true){
					$insertArr['date'] = date("Y-m-d H:i:s");
					$insertId = AddRecords($insertArr,'lists');
				}
				else{
					if(strtotime($rs_existing['erx_modified_date']) <= strtotime($insertArr['erx_modified_date'])){
						$insertId = UpdateRecords($id,'id',$insertArr,'lists');
					}
				}
			}
			//--- END PATIENT RX HISTORY (MEDICATIONS) ------
			$this->re_delete_ids_after_download($Deleted_IDs);
			
		}else return false;
		
	}
	
	/****GET DOWNLOADED ALLERGIES ARRAY AND PROCESS IT (SAVE/UPDATE, OPTIONAL RETURN ITEMS TO MAKE HTML)***/
	function process_erx_allergies($arrAllergies){
		if($arrAllergies){
			foreach($arrAllergies as $allergy){
				$noRecord = false;
				$title = (is_object($allergy->title))?$allergy->title : $allergy['title'];
				$eRx_allergyId = (is_object($allergy->eRx_allergyId))?$allergy->eRx_allergyId : $allergy['eRx_allergyId'];
				$begdate = (is_object($allergy->begdate))?$allergy->begdate : $allergy['begdate'];
				$comments = (is_object($allergy->comments))?$allergy->comments : $allergy['comments'];
				$ag_occular_drug = (is_object($allergy->ag_occular_drug))?$allergy->ag_occular_drug : $allergy['ag_occular_drug'];
				$allergy_status = $allergy['allergy_status'];
				$allergy_status = ($allergy_status == "DISCONTINUED")?"Discontinue":$allergy_status;
				
				$arrUsrName = getUserDetails($this->authId,' fname,lname,mname');
				$fname = $arrUsrName['fname'];
				$lname = $arrUsrName['lname'];
				$allery_type = ($ag_occular_drug == "fdbATAllergenGroup")?"Allergen":(($ag_occular_drug == "fdbATIngredient")?"Ingredient":(($ag_occular_drug == "fdbATDrugName")?"Drug":""));
				$showbegdate = substr($begdate,5,2).'-'.substr($begdate,8,2).'-'.substr($begdate,0,4);
				
				$insertArr = array();
				$insertArr['type'] = '7';
				$insertArr['pid'] = $this->patientId;
				$insertArr['title'] = $title;
				if($begdate != "")$insertArr['begdate'] = $begdate;
				if($comments != "")$insertArr['comments'] = $comments;
				$insertArr['ag_occular_drug'] = $ag_occular_drug;
				$insertArr['eRx_allergyId'] = $eRx_allergyId;
				$insertArr['allergy_status'] = $allergy_status;
				$insertArr['erx_id'] = $eRx_allergyId;
				$insertArr['user'] = $this->authId;
				$insertArr['eRx_drug_status'] = 1;
				
				$q_existing = "SELECT id FROM lists WHERE type = '7' AND pid = '".$this->patientId."' AND (erx_id = '".$eRx_allergyId."' || fdb_id = '".$eRx_allergyId."')";
				$res_existing 	= imw_query($q_existing);
				$id				= '';
				if($res_existing && imw_num_rows($res_existing)==1){
					$rs_existing	= imw_fetch_assoc($res_existing);
					$id = $rs_existing['id'];
				}
				
				if(empty($id) == true){
					$insertArr['date'] = date("Y-m-d H:i:s");
					$insertId = AddRecords($insertArr,'lists');
				}
				else{
					$insertId = UpdateRecords($id,'id',$insertArr,'lists');
				}
			}
		}else return false;
	}

	function discontinue_erx_meds(){
		$q = "UPDATE lists SET allergy_status = 'Discontinue' 
				WHERE type IN ('1','4') 
				AND pid = '".$this->patientId."' 
				AND (erx_id != '' OR emdeon_rx_id != '')";
		imw_query($q);
	}
	
	function get_deleted_ids(){
		$del_Ids = false;
		$q = "SELECT id FROM lists 
				WHERE allergy_status = 'Deleted' 
				AND type IN ('1','4') 
				AND pid = '".$this->patientId."' 
				AND (erx_id != '' OR emdeon_rx_id != '')";
		$r = imw_query($q);
		if($r && imw_num_rows($r)>0){
			$del_Ids = array();
			while($rs = imw_fetch_assoc($r)){
				$del_Ids[] = $rs['id'];
			}
		}
		return $del_Ids;
	}
	
	function re_delete_ids_after_download($id_array){
		if(!is_array($id_array) || count($id_array)==0) return;
		$ids = implode(',',$id_array);
		$q = "UPDATE lists SET allergy_status = 'Deleted' 
				WHERE type IN ('1','4') 
				AND pid = '".$this->patientId."' 
				AND (erx_id != '' OR emdeon_rx_id != '') 
				AND id IN ($ids)";
		$r = imw_query($q);
	}
	
	
	
	function get_admin_ocular_meds(){
		$ocular_med_array = array();
		$res = imw_query("SELECT CONCAT (medicine_name,';',alias) AS ad_ocu_med FROM medicine_data WHERE ocular='1' AND del_status='0'");
		echo imw_error();
		while($rs = imw_fetch_assoc($res)){
			$rs['ad_ocu_med'] = str_replace('; ',';',$rs['ad_ocu_med']);
			$med_array_temp = explode(';',$rs['ad_ocu_med']);
			foreach($med_array_temp as $m){
				if(trim($m)!='') $ocular_med_array[] = trim($m);
			}
		}
		return $ocular_med_array;
	}

	function fetch_erx_meds_from_db(){
		$q = "SELECT * FROM lists WHERE type IN ('1','4') AND pid = '".$this->patientId."' AND eRx_by != '' AND (emdeon_rx_id!='' OR erx_id != '') AND allergy_status != 'Deleted' ";
		$res = imw_query($q);
		$rx_array = false;
		if($res && imw_num_rows($res)>0){
			$rx_array = array();
			while($rs = imw_fetch_assoc($res)){
				$rx_array[] = $rs;
			}
		}
		return $rx_array;
	}
	
	function fetch_erx_allergies_from_db(){
		$q = "SELECT * FROM lists WHERE type IN ('7') AND pid = '".$this->patientId."' AND erx_id != '' AND eRx_allergyId != ''";
		$res = imw_query($q);
		$rx_array = false;
		if($res && imw_num_rows($res)>0){
			$rx_array = array();
			while($rs = imw_fetch_assoc($res)){
				$allergy_type = ($rs['ag_occular_drug'] == "fdbATAllergenGroup")?"Allergen":(($rs['ag_occular_drug'] == "fdbATIngredient")?"Ingredient":(($rs['ag_occular_drug'] == "fdbATDrugName")?"Drug":""));
				$rs['allergen_type'] = $allergy_type;
				$rx_array[] = $rs;
			}
		}
		return $rx_array;
	}
	
	function fetch_pt_active_allergies_from_db(){
		$q = "SELECT id, title, ag_occular_drug, fdb_id,comments FROM lists WHERE pid = '".$this->patientId."' AND fdb_id != '' AND type IN (1,7) AND LOWER(allergy_status) = 'active'";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$allergy = array();
			while($rs = imw_fetch_assoc($res)){
				$ar = array();
				$ar['id']		= $rs['id'];
				$ar['name']		= $rs['title'];
				$ar['type'] 	= $rs['ag_occular_drug'];
				$ar['fdb_id'] 	= $rs['fdb_id'];
				$ar['comments'] 	= $rs['comments'];
				$allergy[] 		= $ar;
				unset($ar);
			}
			return $allergy;
		}
		return false;		
	}
	
	function update_allergy_erx_objid($alg_id,$obj_id){
		imw_query($q = "UPDATE lists SET emdeon_rx_id = '$obj_id' WHERE id = $alg_id LIMIT 1");
	}
	
	function create_allergy_on_erx($EmdeonUrl,$eRx_user_name,$erx_password,$eRx_facility_id){
		$allergies = $this->fetch_pt_active_allergies_from_db();
		if($allergies && is_array($allergies)){
			foreach($allergies as $allergy){
				$allergy_type = '';//Drug, Ingredient or Allergen
				if($allergy['type']=='fdbATDrugName') 		$allergy_type = 'Drug';
				if($allergy['type']=='fdbATIngredient') 	$allergy_type = 'Ingredient';
				if($allergy['type']=='fdbATAllergenGroup') 	$allergy_type = 'Allergen';
				
				$erx_url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&apiLogin=true&textError=true";
				$erx_url.= "&target=servlet/servlets.apiRxServlet&actionCommand=createallergy&patient=".$this->patientId."";
				$erx_url.= "&allergyname=".urlencode($allergy['name'])."&allergytype=".$allergy_type."&allergyid=".$allergy['fdb_id']."&allergydescription=".urlencode($allergy['comments']);
				
				$cur = curl_init();
				curl_setopt($cur, CURLOPT_URL,$erx_url);
				curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
				curl_setopt($cur, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
				$erx_data = curl_exec($cur);//<--ALLERGY SAVED 3004505543>
				$lastError = curl_error($cur); 
				$this->write_erx_log($erx_url,'',$erx_data,$lastError);
				
				if(!$lastError || empty($lastError)){
					preg_match('/ALLERGY SAVED/',$erx_data,$allergysaved);
					if($allergysaved){
						$erx_status_arr = preg_split('/ /',str_replace(array('<--','>'),'',$erx_data));
						$erx_allergy_objid = trim($erx_status_arr[2]);
						$this->update_allergy_erx_objid($allergy['id'],$erx_allergy_objid);
					}
				}				
			}
		}
	}
	
}//end of class.
?>
