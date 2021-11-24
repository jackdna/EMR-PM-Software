<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> CC & History class
 Access Type: Indirect Access.
 Main class file used to import CCD meds.
*/

include_once $GLOBALS['srcdir'].'/classes/medical_hx/medical_history.class.php';
include_once $GLOBALS['srcdir'].'/classes/SaveFile.php';
include_once $GLOBALS['srcdir'].'/classes/ccd_xml_parser.php';
include_once $GLOBALS['srcdir'].'/classes/CLSAlerts.php';
$cls_alerts = new CLSAlerts;

class Medical_Import extends MedicalHistory
{
	//Public variables
		public $arrUserId = array();
		public $arrProbType = '';
		public $import_error = '';
		
	public function __construct($tab = 'medications',$pid=false)
	{
		parent::__construct($tab);
		if(empty($pid) == false){
			$this->patient_id = $pid;
		}
		$this->get_user_details();
		$this->arrProbType = array("","Condition","Complaint","Diagnosis","Finding","Functional Limitation","Problem","Symptom");
		sort($this->arrProbType);
	}
	
	//Returns scripts dropdown 
	public function get_document_type_arr($return_type='array',$request){
		$xml_id_array = explode(',',$request['xml_id']);
		$qry = imw_query("SELECT * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl 
				WHERE patient_id = '".$this->patient_id."'
					  AND (doc_type LIKE '%xml' OR doc_type = 'xml' OR file_extension = 'csv')
					  AND doc_upload_type = 'upload'
					  AND doc_title like 'CCD-%'
					  AND (CCDA_type = 'Ambulatory_CCDA' OR CCDA_type = 'Inpatient_CCDA')
				ORDER BY scan_doc_id DESC");
		if(imw_num_rows($qry) > 0){
			while($row = imw_fetch_array($qry)){
				$return_arr[] = $row;
			}
		}	
		if($return_type == 'array'){
			return $return_arr;
		}else if($return_type == 'dropdown'){
			$options = '';
			if(count($return_arr) > 0){
				foreach($return_arr as $obj){
					$selected = '';
					if(in_array($obj['scan_doc_id'],$xml_id_array)){
						$selected = 'selected';
					}
					$options .= '<option value="'.$obj['scan_doc_id'].'" '.$selected.'>'.$obj['doc_title'].'</option>';
				}
			}else{
				$options = '<option value="" selected>All</option>';
			}
			
			return $options;
		}
	}
	
	//Returns problem list type array
	public function get_prob_list_type_arr($return_type,$sel_opt){
		if($return_type == 'dropdown'){
			$options = '';
			foreach ($this->arrProbType as $val) {																				
				$sel = '';	
				if(trim($sel_opt) != ''){
					$sel = $val == trim($sel_opt) ? 'selected' : '';
				}
				else{
					$sel = $val == "" ? 'selected' : '';
				}
				$options .="<option value='$val' $sel>$val</option>";																			
			}
			return $options;	
		}else{
			return $this->arrProbType;
		}
		
		
	}	
	
	//Returns extension of the file uploaded
	function get_file_extension($file_name) {
		$returnVal = $pathExt = "";

		$pathExt = pathinfo($file_name, PATHINFO_EXTENSION);
		if(empty($pathExt) == false) $returnVal = $pathExt;

		// $file = new SplFileInfo($file_name);
		// $ext  = $file->getExtension();

		return $returnVal;
		//return substr(strrchr($file_name,'.'),1);
	}	
	
	//--- Uploading Files to the system ---
	public function import_document(&$request,&$files){
		$xml_doc_id = array();	
		foreach($files as $file){
			$file_tmp_name 	= $file['tmp_name'];
			$file_name = $file['name'];
			$file_size = $file['size'];
			$file_type = $file['type'];
			$file_extension = $this->get_file_extension($file_name);
			if(strtolower($file_extension) == "xml" || $file_extension == "csv"){
				$error_str = '';
				$oSaveFile = new SaveFile(1);
				$original_file=array();
				$original_file["name"] = $file_name;
				$original_file["type"] = $file_type;
				$original_file["size"] = $file_size;
				$original_file["tmp_name"] = $file_tmp_name;
				
				$file_pointer = $oSaveFile->copyfile($original_file,"CCD",$db_name);
				//---------CHECK PATIENT------------
				$complete_file_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$file_pointer;
				$objCDA = new CDAXMLParser($complete_file_path);
				$arrXMLData = $objCDA->arrXMLData;
				$arrPatientData = $objCDA->arrPatientData;
				$arrPatientData['date_time'] = $arrXMLData['date_time'];
				$sql = "select id,fname,lname,if(DOB='0000-00-00','',DOB) as DOB,sex,postal_code from patient_data where id = '".$this->patient_id."'";
				$res = imw_query($sql);
				$row_patient = imw_fetch_assoc($res);
				$ccd_xml_id = implode(',',$request['ccd_xml']);
				$val_arr = $this->save_xml_to_ccd($row_patient,$arrPatientData,$file_pointer,$original_file,$file_extension,$ccd_xml_id,false,$this->patient_id);
				if(count($val_arr) > 0){
					if($val_arr['error']){
						$error_str .= $val_arr['error'];
					}else{
						header("Location:index.php?showpage=".$request['showpage']."&upload_done=".$val_arr['upload_done']."&xml_id=".$val_arr['xml_id']."");
					}
				}
			}
			else{
				$upload_done = 'no';
				$error_str  = "<div class='panel panel-danger'>";
				$error_str .= 	"<div class='panel-heading'>
									<span>Please provide either XML or CSV file.</span>
									<button type='button' class='close'><span aria-hidden='true'>×</span><span class='sr-only'>Close</span></button>	
								</div>
								<div class='panel-body'>
									<div class='row'>
										<div class='col-sm-12'>
											<label>Provided file : ".$file['name']." is not a valid file </label>
										</div>
									</div>
								</div>
							</div><br>";
			}	
		}
		$return_arr['xml_doc_id'] = $xml_doc_id;
		$return_arr['upload_done'] = $upload_done;
		$return_arr['error'] = $error_str;
		return $return_arr;
	}
	
	public function import_direct_xml($request){
		$return_arr = array();
		$complete_file_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
		$direct_attach_id = (isset($request['direct_attach_id']) && empty($request['direct_attach_id']) == false) ? $request['direct_attach_id'] : '';
		$file_name = (isset($request['file_name']) && empty($request['file_name']) == false) ? $request['file_name'] : '';
		$zip_name = (isset($request['zip_name']) && empty($request['zip_name']) == false) ? $request['zip_name'] : '';
		$pt_id = (isset($request['pt_id']) && empty($request['pt_id']) == false) ? $request['pt_id'] : '';
		
		//Change file name if Zip name exists
		if(empty($zip_name) == false){
			$zip_file_name = pathinfo($complete_file_path.$zip_name, PATHINFO_BASENAME);
			if(empty($file_name) == false){
				$file_name = str_replace($zip_file_name,$file_name,$zip_name);
			}
		}
		
		//File information
		$file_pointer = $complete_file_path.$file_name;
		$file_extension = $this->get_file_extension($file_pointer);
		
		$original_file['type'] = get_mime($file_pointer);
		$original_file['size'] = filesize($file_pointer);
		
		//XML Content
		$xml_content = file_get_contents($complete_file_path.$file_name);
		$arrPatientData = $this->check_patient_details($xml_content);
		
		//Patient Content
		$sql = "select id,fname,lname,if(DOB='0000-00-00','',DOB) as DOB,sex,postal_code from patient_data where id = '".$pt_id."'";
		$res = imw_query($sql);
		$row_patient = imw_fetch_assoc($res);
		
		//Prev. Xml IDS
		$ccd_xml_id = '';
		if($request['ccd_xml']){
			$ccd_xml_id = implode(',',$request['ccd_xml']);
		}
		
		if(isset($request['chk_ccda']) && empty($request['chk_ccda']) == false){
			if(strtolower($row_patient['fname']) != strtolower($arrPatientData['fname']) || strtolower($row_patient['lname']) != strtolower($arrPatientData['lname']) || $row_patient['DOB'] != $arrPatientData['dob'] || strtolower($row_patient['sex']) != strtolower($arrPatientData['gender']) || $row_patient['postal_code'] != $arrPatientData['zip']){
				//@unlink($complete_file_path);
				//@unlink($complete_file_path_encryp);
				$arrXML = array();
				$error_str  = "<div class='panel panel-danger'>";
				$error_str .= 	"<div class='panel-heading'>
									<span>Selected patient data does not match with imported patient.</span>
								</div>
								<div class='panel-body'>
									<div class='row'>
										<table class='table table-bordered'>
											<tr>
												<th>XML Data</th>
												<th>Patient Data</th>
											</tr>
											<tr>
												<td>First Name : ".$arrPatientData['fname']."</td>
												<td>First Name : ".$row_patient['fname']."</td>
											</tr>	
											<tr>
												<td>Last Name : ".$arrPatientData['lname']."</td>
												<td>Last Name : ".$row_patient['lname']."</td>
											</tr>
											<tr>
												<td>Gender : ".$arrPatientData['gender']."</td>
												<td>Gender : ".$row_patient['sex']."</td>
											</tr>
											<tr>
												<td>DOB : ".$arrPatientData['dob']."</td>
												<td>DOB : ".$row_patient['DOB']."</td>
											</tr>
											<tr>
												<td>Zip : ".$arrPatientData['zip']."</td>
												<td>Zip : ".$row_patient['postal_code']."</td>
											</tr>	
										</table>
									</div>
								</div>
							</div>";
				$return_arr['error'] = $error_str;	
				$return_arr['chk_ccda'] = $request['chk_ccda'];	
			}else{
				$return_arr['chk_ccda'] = $request['chk_ccda'];	
			}
			return $return_arr;
		}else{
			$val_arr = $this->save_xml_to_ccd($row_patient,$arrPatientData,$file_pointer,$original_file,$file_extension,$ccd_xml_id,true,$pt_id);
			if(count($val_arr) > 0){
				return $val_arr;
			}
		}
	}
	
	public function save_xml_to_ccd($row_patient,$arrPatientData,$file_pointer,$file_arr,$file_extension,$ccd_xml_id,$direct_chk = false,$pid){
		$return_arr = array();
		
		$date_file=time();
		$pattern = "/.xml|.csv/";
		$db_name = preg_replace($pattern,"",end(explode('/',$file_pointer)))."_".$date_file.".".$file_extension;
		if($direct_chk == false){
			if(strtolower($row_patient['fname']) != strtolower($arrPatientData['fname']) || strtolower($row_patient['lname']) != strtolower($arrPatientData['lname']) || $row_patient['DOB'] != $arrPatientData['dob'] || strtolower($row_patient['sex']) != strtolower($arrPatientData['gender']) || $row_patient['postal_code'] != $arrPatientData['zip']){
				//@unlink($complete_file_path);
				//@unlink($complete_file_path_encryp);
				$arrXML = array();
				$error_str  = "<div class='panel panel-danger'>";
				$error_str .= 	"<div class='panel-heading'>
									<span>Selected patient data does not match with imported patient.</span>
									<button type='button' class='close'><span aria-hidden='true'>×</span><span class='sr-only'>Close</span></button>	
								</div>
								<div class='panel-body'>
									<div class='row'>
										<div class='col-sm-6'>
											<label>XML Data</label>
											<p>First Name : ".$arrPatientData['fname']."</p>
											<p>Last Name : ".$arrPatientData['lname']."</p>
											<p>Gender : ".$arrPatientData['gender']."</p>
											<p>DOB : ".$arrPatientData['dob']."</p>
											<p>Zip : ".$arrPatientData['zip']."</p>
										</div>
										<div class='col-sm-6'>
											<label>Patient Data</label>
											<p>First Name : ".$row_patient['fname']."</p>	
											<p>Last Name : ".$row_patient['lname']."</p>	
											<p>Gender : ".$row_patient['sex']."</p>	
											<p>DOB : ".$row_patient['DOB']."</p>	
											<p>Zip : ".$row_patient['postal_code']."</p>	
										</div>
									</div>
								</div>
							</div><br>";
				$return_arr['error'] = $error_str;			
			}else{
				$sql = "SELECT "."".constant("IMEDIC_SCAN_DB").".folder_categories.folder_categories_id ".
					"FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  ".
					"WHERE ".constant("IMEDIC_SCAN_DB").".folder_categories.folder_status ='active' ".
					"AND ".constant("IMEDIC_SCAN_DB").".folder_categories.folder_name = 'CCD' ".
					"AND ".constant("IMEDIC_SCAN_DB").".folder_categories.patient_id = '".$pid."'"; 
				$res = imw_query($sql);
				if(imw_num_rows($res) == 0){
					$folder_categories_id = 0;
					$insertSql = "Insert into  ".		
									"".constant("IMEDIC_SCAN_DB").".folder_categories ".
									"(folder_name,folder_status,patient_id)".
									"VALUES ('CCD', 'active', '".$pid."')";
					$rsInsertSql = imw_query($insertSql);	
					$folder_categories_id = imw_insert_id();
				}else{
					$rowSql = imw_fetch_array($res);
					$folder_categories_id = $rowSql['folder_categories_id'];
				}
				if($folder_categories_id > 0){
					$dtNew = date('F d, Y',strtotime($arrPatientData['date_time']));
					$qry_ccd_ins = "Insert into  ".	"".constant("IMEDIC_SCAN_DB").".scan_doc_tbl ".
										"(folder_categories_id,patient_id,doc_title,doc_type,doc_size,doc_upload_type,pdf_url,upload_docs_date,upload_operator_id,file_path,file_extension,CCDA_type,direct_attach_id)".
										"VALUES ('".$folder_categories_id."', '".$pid."', 'CCD-".$db_name."','".$file_arr['type']."','".$file_arr['size']."','upload','CCD-".$dtNew."',now(),'".$_SESSION['authId']."','".str_replace($GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH'),'',$file_pointer)."','".$file_extension."','Ambulatory_CCDA','".$_SESSION['opened_attachment_id']."')"; 
									
					$res_ccd_ins = imw_query($qry_ccd_ins);	
					$scan_doc_id = imw_insert_id();
					$xml_doc_id[] = $scan_doc_id;
				}
				$xml_doc_id_str = implode(',',$xml_doc_id);
				$xml_final_ids = $ccd_xml_id.','.$xml_doc_id_str;
				$upload_done = 'yes';
				
				
				$return_arr['xml_id'] = $xml_final_ids;
				$return_arr['upload_done'] = $upload_done;
			}
		}else{
			$sql = "SELECT "."".constant("IMEDIC_SCAN_DB").".folder_categories.folder_categories_id ".
					"FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  ".
					"WHERE ".constant("IMEDIC_SCAN_DB").".folder_categories.folder_status ='active' ".
					"AND ".constant("IMEDIC_SCAN_DB").".folder_categories.folder_name = 'CCD' ".
					"AND ".constant("IMEDIC_SCAN_DB").".folder_categories.patient_id = '".$pid."'"; 
				$res = imw_query($sql);
				if(imw_num_rows($res) == 0){
					$folder_categories_id = 0;
					$insertSql = "Insert into  ".		
									"".constant("IMEDIC_SCAN_DB").".folder_categories ".
									"(folder_name,folder_status,patient_id)".
									"VALUES ('CCD', 'active', '".$pid."')";
					$rsInsertSql = imw_query($insertSql);	
					$folder_categories_id = imw_insert_id();
				}else{
					$rowSql = imw_fetch_array($res);
					$folder_categories_id = $rowSql['folder_categories_id'];
				}
				if($folder_categories_id > 0){
					$dtNew = date('F d, Y',strtotime($arrPatientData['date_time']));
					$qry_ccd_ins = "Insert into  ".	"".constant("IMEDIC_SCAN_DB").".scan_doc_tbl ".
										"(folder_categories_id,patient_id,doc_title,doc_type,doc_size,doc_upload_type,pdf_url,upload_docs_date,upload_operator_id,file_path,file_extension,CCDA_type,direct_attach_id)".
										"VALUES ('".$folder_categories_id."', '".$pid."', 'CCD-".$db_name."','".$file_arr['type']."','".$file_arr['size']."','upload','CCD-".$dtNew."',now(),'".$_SESSION['authId']."','".str_replace($GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH'),'',$file_pointer)."','".$file_extension."','Ambulatory_CCDA','".$_SESSION['opened_attachment_id']."')"; 
									
					$res_ccd_ins = imw_query($qry_ccd_ins);	
					$scan_doc_id = imw_insert_id();
					$xml_doc_id[] = $scan_doc_id;
				}
				$xml_doc_id_str = implode(',',$xml_doc_id);
				$xml_final_ids = $ccd_xml_id.','.$xml_doc_id_str;
				$upload_done = 'yes';
				
				$return_arr['save_ccda'] = 'yes';
				$return_arr['xml_id'] = $xml_final_ids;
				$return_arr['upload_done'] = $upload_done;
		}
		//$_SESSION['opened_attachment_id'] = '';
		return $return_arr;
	}
	
	public function check_patient_details($xml_file){
		$arr_pt_details_return=array();
		$arr_xml_file_content=array();
		if($xml_file){
			$arr_xml_file_content = simplexml_load_string($xml_file);
			if(count($arr_xml_file_content)>0){
				$arr_pt_details_return['fname']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->name->given;
				$arr_pt_details_return['lname']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->name->family;
				$arr_pt_details_return['gender']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->administrativeGenderCode['displayName'];
				$arr_pt_details_return['dob']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->birthTime['value'];
				$arr_pt_details_return['dob'] = date("Y-m-d", strtotime($arr_pt_details_return['dob']));
				
				$arr_pt_details_return['city']=(string)$arr_xml_file_content->recordTarget->patientRole->addr->city;
				$arr_pt_details_return['state']=(string)$arr_xml_file_content->recordTarget->patientRole->addr->state;
				$arr_pt_details_return['zip']=(string)$arr_xml_file_content->recordTarget->patientRole->addr->postalCode;
				$arr_pt_details_return['date_time']=(string)$arr_xml_file_content->effectiveTime['value'];
			}
			return $arr_pt_details_return;
		}
	}
	
	public function get_user_details(){
		$qryUser = "SELECT id, fname, mname, lname, user_type FROM users where fname!='' and lname!='' AND delete_status = 0 ORDER BY fname,lname ";
		$resUser = imw_query($qryUser);
		if($resUser){
			while($arrUser = imw_fetch_array($resUser)){
				$this->arrUserId[$arrUser["id"]]["fname"] = $arrUser["fname"];
				$this->arrUserId[$arrUser["id"]]["lname"] = $arrUser["lname"];
			}
		}
		return $this->arrUserId;
	}
	
	//Returns allergies data
	public function get_all_allergies_data($type,&$request,$direct=''){
		$form_val_txt = 'index.php';
		if(empty($direct) == false){
			$form_val_txt = 'import_ccda.php';
		}
		
		$qry = imw_query("select *,date_format(begdate,'".get_sql_date_format()."') as begdate from lists where pid = '$this->patient_id' and type in ($type) and allergy_status = 'Active'");
		while($row = imw_fetch_assoc($qry)){
			$row['status'] = $row['allergy_status'];
			$allergies_data_val[] = $row;
		}
		$allergies_data = $allergies_data_val;
		
		//If request is to merge than showing merge data and setting form action to save
		$form_save_action = $form_val_txt.'?showpage='.$request['showpage'].'&page_request=merge&xml_id='.$request['xml_id'].'&pt_id='.$this->patient_id.'';
		if(isset($request['page_request']) && $request['page_request'] == 'merge'){
			$merge_array = array();
			$arr_title = array();
			$form_save_action = $form_val_txt.'?showpage='.$request['showpage'].'&page_request=save_allergies';
			
			$arr_title_tmp = $request['title'];
			foreach($request['chk_allergies'] as $key=>$val){
				$arr_title[$val] = $arr_title_tmp[$val];
			}
			$arr_title =  array_unique($arr_title);
			$arr_title =  array_filter($arr_title);
			if(count($arr_title) > 0){	//Setting CLS alerts if array has something
				$cls_alerts = $this->set_cls_alerts();
			}
			foreach($arr_title as $key => $val){
				$merge_array_val['title'] = $val;
				$merge_array_val['ag_occular_drug'] = $request['ag_occular_drug'][$key];
				$merge_array_val['begdate'] = $request['begdate'][$key];
				$merge_array_val['chk_med_hx_allergies'] = $request['chk_med_hx_allergies'][$key];
				$merge_array_val['ccda_code'] = $request['ccda_code'][$key];
				$merge_array_val['ccda_code_system_name'] = $request['ccda_code_system_name'][$key];
				$merge_array_val['comments'] = $request['comments'][$key];
				$merge_array_val['reaction_code'] = $request['reaction_code'][$key];
				$merge_array_val['severity'] = $request['severity'][$key];
				$merge_array_val['status'] = $request['status'][$key];
				$merge_array_val['id'] = $request['hid_list_id'][$key];
				$merge_array[] = $merge_array_val;	
			}
			//Changing DB Data array with requested merge array
			$allergies_data = $merge_array;
		}
		
		$return_arr['allergy_data'] 	= $allergies_data;
		$return_arr['arrUserId'] 		= $this->get_user_details();
		$return_arr['form_save_action'] = $form_save_action;
		$return_arr['cls_alerts']		= $cls_alerts;
		return $return_arr;
	}
	
	//Saving Allergies data to DB
	public function save_allergies_data(&$request){
		global $cls_review;
		$counter = 0;
		$medDataFields = make_field_type_array("lists");
		$arr_ag_occular_drug = xss_rem($request['ag_occular_drug']);
		$arr_title = xss_rem($request['title']);
		$arr_begdate = xss_rem($request['begdate']);
		$arr_chk_med_hx_allergies = xss_rem($request['chk_med_hx_allergies']);
		$arr_ccda_code = xss_rem($request['ccda_code']);
		$arr_ccda_code_system = xss_rem($request['ccda_code_system']);
		$arr_ccda_code_system_name = xss_rem($request['ccda_code_system_name']);
		$arr_comments 		= xss_rem($request['comments']);
		$arr_reaction_code 	= xss_rem($request['reaction_code']);
		$arr_severity = xss_rem($request['severity']);
		$arr_status = xss_rem($request['status']);	
		$type_val = '3,7';
		$sql = "select *,date_format(begdate,'".get_sql_date_format()."') as begdate from lists where pid = '$this->patient_id' and type in ($type_val) and allergy_status = 'Active'";
		$res = imw_query($sql);					 
		while($row  = imw_fetch_array($res)){
			imw_query("update lists set allergy_status = 'Deleted' WHERE id = '".$row['id']."'");
		}
		$pid = $this->patient_id;
		foreach($request['chk_allergies'] as $key=>$val){
			if($arr_title[$val] != ""){
				if($hid_list_id[$val] != ""){
					$sql = "update lists set allergy_status = 'Active',
								title = '".$arr_title[$val]."',
								ag_occular_drug ='".$arr_ag_occular_drug[$val]."' ,
								begdate ='".getDateFormatDB($arr_begdate[$val])."' ,
								comments ='".$arr_comments[$val]."',
								reaction_code ='".$arr_reaction_code[$val]."',
								severity ='".$arr_severity[$val]."', 
								ccda_code ='".$arr_ccda_code[$val]."' 
								WHERE id = '".$hid_list_id[$val]."'";
								$ag_id=$hid_list_id[$val];
								$status = 'consolidate';
								imw_query($sql);
								$counter = ($counter+imw_affected_rows());
								$arr_status[$val]="Active";
				}else{
				if(trim($arr_status[$val])=="") $arr_status[$val] = 'Active';
				$sql = "insert into lists set allergy_status = '".$arr_status[$val]."',
								title = '".$arr_title[$val]."',
								ag_occular_drug ='".$arr_ag_occular_drug[$val]."' ,
								begdate ='".getDateFormatDB($arr_begdate[$val])."' ,
								comments ='".$arr_comments[$val]."',
								reaction_code ='".$arr_reaction_code[$val]."',
								severity='".$arr_severity[$val]."',
								pid ='".$pid."',
								type ='7',
								ccda_code ='".$arr_ccda_code[$val]."',
								ccda_code_system ='".$arr_ccda_code_system[$val]."',
								ccda_code_system_name ='".$arr_ccda_code_system_name[$val]."'
								";
					imw_query($sql);
					$counter = ($counter+imw_affected_rows());
					$ag_id=imw_insert_id();
					$status = 'consolidate';
				}
				$med_data_arr=array();
				$med_data_arr['type'] ='7';
				$med_data_arr['title'] =trim($arr_title[$val]);
				$med_data_arr['ag_occular_drug'] =trim($arr_ag_occular_drug[$val]);
				$med_data_arr['begdate'] =trim(getDateFormatDB($arr_begdate[$val]));
				$med_data_arr['allergy_status'] = $arr_status[$val];
				$med_data_arr['comments'] =trim($arr_comments[$val]);
				$med_data_arr['pid'] =trim($pid);
				$med_data_exists_arr = $medDataArr[$ag_id];
				
				$arrReview_Allergies = array();
				
				$Review_Allergies_arr = array();		
				$Review_Allergies_arr['Pk_Id'] = $ag_id;
				$Review_Allergies_arr['Table_Name'] = 'lists';
				$Review_Allergies_arr['UI_Filed_Name'] = 'ag_type'.$val;
				$Review_Allergies_arr['Data_Base_Field_Name']= "type";
				$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"type");
				$Review_Allergies_arr['Field_Text'] = '7';
				$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
				$Review_Allergies_arr['Action'] = $status;
				$Review_Allergies_arr['Old_Value'] = $med_data_exists_arr['type'];
				$Review_Allergies_arr['New_Value'] = $med_data_arr['type'];
				$Review_Allergies_arr['ccda_code'] =trim($arr_ccda_code[$val]);
				$Review_Allergies_arr['ccda_code_system'] =trim($arr_ccda_code_system[$val]);
				$Review_Allergies_arr['ccda_code_system_name'] =trim($arr_ccda_code_system_name[$val]);
				$arrReview_Allergies[] = $Review_Allergies_arr;
				
											
				//--- ALLERGY TYPE FOR VIEWED ----
				$Review_Allergies_arr = array();		
				$Review_Allergies_arr['Pk_Id'] = $ag_id;
				$Review_Allergies_arr['Table_Name'] = 'lists';
				$Review_Allergies_arr['UI_Filed_Name'] = 'ag_occular_drug'.$val;
				$Review_Allergies_arr['Data_Base_Field_Name']= "ag_occular_drug";
				$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"ag_occular_drug");
				$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Drug - '.trim($arr_ag_occular_drug[$val]);
				$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
				$Review_Allergies_arr['Action'] = $status;
				$Review_Allergies_arr['Old_Value'] = $med_data_exists_arr['ag_occular_drug'];
				$Review_Allergies_arr['New_Value'] = $med_data_arr['ag_occular_drug'];
				$arrReview_Allergies[] = $Review_Allergies_arr;
				
				//--- ALLERGY NAME FOR VIEWED ----
				$Review_Allergies_arr = array();		
				$Review_Allergies_arr['Pk_Id'] = $ag_id;
				$Review_Allergies_arr['Table_Name'] = 'lists';
				$Review_Allergies_arr['UI_Filed_Name'] = 'ag_title'.$val;
				$Review_Allergies_arr['Data_Base_Field_Name']= "title";
				$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"title");
				$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Name - '.trim($arr_title[$val]);
				$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
				$Review_Allergies_arr['Action'] = $status;
				$Review_Allergies_arr['Old_Value'] = $med_data_exists_arr['title'];
				$Review_Allergies_arr['New_Value'] = $med_data_arr['title'];
				$arrReview_Allergies[] = $Review_Allergies_arr;
				
				//--- ALLERGY BEGIN DATE FOR VIEWED ----
				$Review_Allergies_arr = array();		
				$Review_Allergies_arr['Pk_Id'] = $ag_id;
				$Review_Allergies_arr['Table_Name'] = 'lists';
				$Review_Allergies_arr['UI_Filed_Name'] = 'ag_begindate'.$val;
				$Review_Allergies_arr['Data_Base_Field_Name']= "begdate";
				$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"begdate");
				$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Begin Date - '.trim(getDateFormatDB($arr_begdate[$val]));
				$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
				$Review_Allergies_arr['Action'] = $status;
				$Review_Allergies_arr['Old_Value'] = $med_data_exists_arr['begdate'];
				$Review_Allergies_arr['New_Value'] = $med_data_arr['begdate'];
				$arrReview_Allergies[] = $Review_Allergies_arr;
				
				//--- ALLERGY CEMMENTS FOR VIEWED ----
				$Review_Allergies_arr = array();		
				$Review_Allergies_arr['Pk_Id'] = $ag_id;
				$Review_Allergies_arr['Table_Name'] = 'lists';
				$Review_Allergies_arr['UI_Filed_Name'] = 'ag_comments'.$val;
				$Review_Allergies_arr['Data_Base_Field_Name']= "comments";
				$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"comments");
				$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Comments - '.trim($arr_comments[$val]);
				$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
				$Review_Allergies_arr['Action'] = $status;
				$Review_Allergies_arr['Old_Value'] = $listQryRes['comments'];
				$Review_Allergies_arr['New_Value'] = $dataArr['comments'];
				$arrReview_Allergies[] = $Review_Allergies_arr;
				
				//--- ALLERGY CEMMENTS FOR VIEWED ----
				$Review_Allergies_arr = array();		
				$Review_Allergies_arr['Pk_Id'] = $ag_id;
				$Review_Allergies_arr['Table_Name'] = 'lists';
				$Review_Allergies_arr['UI_Filed_Name'] = 'ag_status'.$val;
				$Review_Allergies_arr['Data_Base_Field_Name']= "allergy_status";
				$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"allergy_status");
				$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Status - '.trim($arr_status[$val]);
				$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
				$Review_Allergies_arr['Action'] = $status;
				$Review_Allergies_arr['Old_Value'] = $listQryRes['allergy_status'];
				$Review_Allergies_arr['New_Value'] = $dataArr['allergy_status'];
				$arrReview_Allergies[] = $Review_Allergies_arr;
				$cls_review->reviewMedHx($arrReview_Allergies,$_SESSION['authId'],"Allergies",$_SESSION['patient'],0,0);
			}
		}
		return $counter;
	}
	
	
	//Returns problem list data from DB
	public function get_all_prob_list_data(&$request,$direct=''){
		$form_val_txt = 'index.php';
		if(empty($direct) == false){
			$form_val_txt = 'import_ccda.php';
		}
		$return_arr = array();
		$sql = imw_query("select *,date_format(onset_date,'".get_sql_date_format()."') as onset_date from pt_problem_list where pt_id = '$this->patient_id' and status = 'Active'");
		while($row = imw_fetch_array($sql)){
			$db_arr[] = $row;
		}
		$return_array = $db_arr;
		
		//If request is to merge than showing merge data and setting form action to save
		$form_save_action = $form_val_txt.'?showpage='.$request['showpage'].'&page_request=merge&xml_id='.$request['xml_id'].'&pt_id='.$this->patient_id.'';
		if(isset($request['page_request']) && $request['page_request'] == 'merge'){
			$merge_array = array();
			$arr_problem_name = array();
			$form_save_action = $form_val_txt.'?showpage='.$request['showpage'].'&page_request=save_problem_list';
			
			
			$arr_problem_name_tmp = $request['problem_name'];
			foreach($request['chk_import'] as $key=>$val){
				$arr_problem_name[$val] = $arr_problem_name_tmp[$val];
			}
			$arr_problem_name =  array_unique($arr_problem_name);
			$arr_problem_name =  array_filter($arr_problem_name);
			if(count($arr_problem_name) > 0){	//Setting CLS alerts if array has something
				$cls_alerts = $this->set_cls_alerts();
			}
			foreach($arr_problem_name as $key => $val){
				$merge_array_val['problem_name'] = $val;
				$merge_array_val['onset_date'] = $request['onset_date'][$key];
				$merge_array_val['OnsetTime'] = $request['OnsetTime'][$key];
				$merge_array_val['ccda_code'] = $request['ccda_code'][$key];
				$merge_array_val['ccda_code_system'] = $request['ccda_code_system'][$key];
				$merge_array_val['ccda_code_system_name'] = $request['ccda_code_system_name'][$key];
				$merge_array_val['prob_type'] = $request['prob_type'][$key];
				$merge_array_val['chk_med_hx'] = $request['chk_med_hx'][$key];
				$merge_array_val['hid_id'] = $request['hid_id'][$key];
				$merge_array_val['status'] = $request['status'][$key];
				$merge_array_val['id'] = $request['hid_list_id'][$key];
				$merge_array[] = $merge_array_val;	
			} 
			$return_array = $merge_array;
		}
		$return_arr['prob_list_data'] = $return_array;
		$return_arr['form_save_action'] = $form_save_action;
		return $return_arr;
	}
	
	//Saving all problem list data
	public function save_problem_list_data(&$request){
		$counter = 0;
		$arr_problem_name = $request['problem_name'];
		$arr_onset_date = $request['onset_date'];
		$arr_OnsetTime = $request['OnsetTime'];
		$arr_ccda_code = $request['ccda_code'];
		$arr_ccda_code_system = $request['ccda_code_system'];
		$arr_ccda_code_system_name = $request['ccda_code_system_name'];
		$arr_prob_type = $request['prob_type'];
		$arr_hid_id = $request['hid_id'];
		$arr_status = $request['status'];
		$pid = $this->patient_id;
		$qry = "select * from pt_problem_list where pt_id = '$pid' and status = 'Active'";
		$res = imw_query($qry);					 
		while($row  = imw_fetch_assoc($res)){
			imw_query("update pt_problem_list set status = 'Deleted' WHERE id = '".$row['id']."'");
		}
		$arr_chk_med_hx = $request['chk_med_hx'];
		foreach($request['chk_import'] as $key=>$val){
			if($arr_problem_name[$val] != ""){
				if($arr_hid_id[$val] != ""){
					$sql = "update pt_problem_list set status = 'Active',
								user_id = '".$_SESSION['authId']."',
								problem_name = '".$arr_problem_name[$val]."',
								onset_date ='".getDateFormatDB($arr_onset_date[$val])."' ,
								OnsetTime ='".$arr_OnsetTime[$val]."' ,
								prob_type ='".$arr_prob_type[$val]."',
								user_id = '".$_SESSION['authId']."'
								WHERE id = '".$arr_hid_id[$val]."'";
					imw_query($sql);
					$counter = ($counter+imw_affected_rows());	
					$sql = "insert into  pt_problem_list_log set status = 'Active',
								user_id = '".$_SESSION['authId']."',
								problem_name = '".$arr_problem_name[$val]."',
								onset_date ='".getDateFormatDB($arr_onset_date[$val])."' ,
								OnsetTime ='".$arr_OnsetTime[$val]."' ,
								prob_type ='".$arr_prob_type[$val]."',
								ccda_code ='".$arr_ccda_code[$val]."',
								ccda_code_system ='".$arr_ccda_code_system[$val]."',
								ccda_code_system_name ='".$arr_ccda_code_system_name[$val]."',
								pt_id ='".$pid."',
								problem_id='".$arr_hid_id[$val]."'
								";	
					imw_query($sql);
					$counter = ($counter+imw_affected_rows());	
				}else{
				$sql = "insert into  pt_problem_list set status = 'Active',
								user_id = '".$_SESSION['authId']."',
								problem_name = '".$arr_problem_name[$val]."',
								onset_date ='".getDateFormatDB($arr_onset_date[$val])."' ,
								OnsetTime ='".$arr_OnsetTime[$val]."' ,
								prob_type ='".$arr_prob_type[$val]."',
								ccda_code ='".$arr_ccda_code[$val]."',
								ccda_code_system ='".$arr_ccda_code_system[$val]."',
								ccda_code_system_name ='".$arr_ccda_code_system_name[$val]."',
								pt_id ='".$pid."'
								";
				imw_query($sql);	
				$counter = ($counter+imw_affected_rows());	
				$pt_prob_ins_id=imw_insert_id();		
				$sql = "insert into  pt_problem_list_log set status = 'Active',
								user_id = '".$_SESSION['authId']."',
								problem_name = '".$arr_problem_name[$val]."',
								onset_date ='".getDateFormatDB($arr_onset_date[$val])."' ,
								OnsetTime ='".$arr_OnsetTime[$val]."' ,
								prob_type ='".$arr_prob_type[$val]."',
								ccda_code ='".$arr_ccda_code[$val]."',
								ccda_code_system ='".$arr_ccda_code_system[$val]."',
								ccda_code_system_name ='".$arr_ccda_code_system_name[$val]."',
								pt_id ='".$pid."',
								problem_id='".$pt_prob_ins_id."'
								";	
				imw_query($sql);
				$counter = ($counter+imw_affected_rows());	
				}
			}
		}
		return $counter;
	}	
	
	//Returns medications data from DB
	public function get_all_medications_data($type,&$request,$direct=''){
		$form_val_txt = 'index.php';
		if(empty($direct) == false){
			$form_val_txt = 'import_ccda.php';
		}
		$return_arr = array();
		$sql_qry = imw_query("select *,date_format(begdate,'".get_sql_date_format()."') as begdate,date_format(enddate,'".get_sql_date_format()."') as enddate from lists where pid = '$this->patient_id' and type in ($type) and allergy_status = 'Active'");
		while($row = imw_fetch_array($sql_qry)){
			$db_arrr[] = $row;
		}
		$return_array = $db_arrr;
		
		$form_save_action = $form_val_txt.'?showpage='.$request['showpage'].'&page_request=merge&xml_id='.$request['xml_id'].'&pt_id='.$this->patient_id.'';
		if(isset($request['page_request']) && $request['page_request'] == 'merge'){
			$form_save_action = $form_val_txt.'?showpage='.$request['showpage'].'&page_request=save_medications';
			$arr_title_tmp = $request['title'];
			foreach($request['chk_box'] as $key=>$val){
				$arr_title[$val] = $arr_title_tmp[$val];
			}
			$arr_title =  array_unique($arr_title);
			$arr_title =  array_filter($arr_title);
			if(count($arr_title) > 0){	//Setting CLS alerts if array has something
				$cls_alerts = $this->set_cls_alerts();
			}
			foreach($arr_title as $key => $val){
				$merge_array_val['title'] = $val;
				$merge_array_val['destination'] = $request['dosage'][$key];
				$merge_array_val['md_occular'] = $request['md_occular'][$key];
				$merge_array_val['sig'] = $request['sig'][$key];
				$merge_array_val['med_route'] = $request['med_route'][$key];
				$merge_array_val['compliant'] = $request['compliant'][$key];
				$merge_array_val['begdate'] = $request['begdate'][$key];
				$merge_array_val['enddate'] = $request['enddate'][$key];
				$merge_array_val['comments'] = $request['comments'][$key];
				$merge_array_val['chk_ocular'] = $request['chk_ocular'][$key];
				$merge_array_val['ccda_code'] = $request['ccda_code'][$key];
				$merge_array_val['ccda_code_system'] = $request['ccda_code_system'][$key];
				$merge_array_val['ccda_code_system_name'] = $request['ccda_code_system_name'][$key];
				$merge_array_val['chk_box'] = $request['chk_box'][$key];
				$merge_array_val['id'] = $request['hid_list_id'][$key];
				$merge_array[] = $merge_array_val;	
			} 
			$return_array = $merge_array;
		}
		
		$return_arr['medications_full_data'] = $return_array;
		$return_arr['form_save_action'] = $form_save_action;
		return $return_arr;	
	}
	
	//Saving medications Data
	public function save_medications_data(&$request){
		global $cls_review;
		$medDataFields = make_field_type_array("lists");
		$arr_title = $request['title'];
		$arr_dosage = $request['dosage'];
		$arr_sites = $request['md_occular'];
		$arr_sig = $request['sig'];
		$arr_route = $request['med_route'];
		$arr_compliant = $request['compliant'];
		$arr_begdate = $request['begdate'];
		$arr_enddate = $request['enddate'];
		$arr_comments = $request['comments'];
		$arr_chk_ocular = $request['chk_ocular'];
		$arr_ccda_code = $request['ccda_code'];
		$arr_ccda_code_system = $request['ccda_code_system'];
		$arr_ccda_code_system_name = $request['ccda_code_system_name'];
		$arr_chk_med_hx = $request['chk_med_hx'];
		$pid = $this->patient_id;
		$type = '1,4';
		$sql = "select *,date_format(begdate,'".get_sql_date_format()."') as begdate from lists where pid = '$pid' and type in ($type) and allergy_status = 'Active'";
		$res = imw_query($sql);					 
		while($row  = imw_fetch_assoc($res)){
			imw_query("update lists set allergy_status = 'Deleted' WHERE id = '".$row['id']."'");
		}
		
		foreach($request['chk_box'] as $key=>$val){
			if($arr_title[$val] != ""){
				if($arr_chk_ocular[$val] != "")
				$type = 4;
				else
				$type = 1;
				if($hid_list_id[$val] != ""){
					$sql = "update lists set allergy_status = 'Active',
								title = '".$arr_title[$val]."',
								destination = '".$arr_dosage[$val]."',
								sites = '".$arr_sites[$val]."',
								sig = '".$arr_sig[$val]."',
								med_route = '".$arr_route[$val]."',
								begdate ='".getDateFormatDB($arr_begdate[$val])."' ,
								enddate ='".getDateFormatDB($arr_enddate[$val])."' ,
								comments ='".$arr_comments[$val]."',
								compliant ='".$arr_compliant[$val]."',
								pid ='".$pid."',
								type = '".$type."'
								WHERE id = '".$hid_list_id[$val]."'";
								$mid=$hid_list_id[$val];
								$md_action = 'consolidate';
								imw_query($sql);
								$counter = ($counter+imw_affected_rows());
				}
				else{
				$ins_dat = date('Y-m-d H:i:s');
				$sql = "insert into lists set allergy_status = 'Active',
								date = '".$ins_dat."',
								title = '".$arr_title[$val]."',
								destination = '".$arr_dosage[$val]."',
								sites = '".$arr_sites[$val]."',
								sig = '".$arr_sig[$val]."',
								med_route = '".$arr_route[$val]."',
								begdate ='".getDateFormatDB($arr_begdate[$val])."' ,
								enddate ='".getDateFormatDB($arr_enddate[$val])."' ,
								comments ='".$arr_comments[$val]."',
								compliant ='".$arr_compliant[$val]."',
								pid ='".$pid."',
								type = '".$type."',
								ccda_code ='".$arr_ccda_code[$val]."',
								ccda_code_system ='".$arr_ccda_code_system[$val]."',
								ccda_code_system_name ='".$arr_ccda_code_system_name[$val]."'
								";
					imw_query($sql);
					$counter = ($counter+imw_affected_rows());
					$mid=imw_insert_id();
					$md_action = 'consolidate';
				}
				$med_data_arr=array();
				$med_data_arr['type'] =trim($type);
				$med_data_arr['title'] =trim($arr_title[$val]);
				$med_data_arr['destination'] =trim($arr_dosage[$val]);
				$med_data_arr['sig'] =trim($arr_sig[$val]);
				$med_data_arr['med_route'] =trim($arr_route[$val]);
				$med_data_arr['begdate'] =trim(getDateFormatDB($arr_begdate[$val]));
				$med_data_arr['enddate'] =trim(getDateFormatDB($arr_enddate[$val]));
				$med_data_arr['allergy_status'] = "Active";
				$med_data_arr['sites'] =trim($arr_sites[$val]);
				$med_data_arr['comments'] =trim($arr_comments[$val]);
				$med_data_arr['compliant'] =trim($arr_compliant[$val]);
				$med_data_arr['pid'] =trim($pid);
				$med_data_arr['ccda_code'] =trim($arr_ccda_code[$val]);
				$med_data_arr['ccda_code_system'] =trim($arr_ccda_code_system[$val]);
				$med_data_arr['ccda_code_system_name'] =trim($arr_ccda_code_system_name[$val]);
				$med_data_exists_arr = $medDataArr[$mid];
				
				$med_reviewed_arr = array();
				
				//--- REVIEWED FOR MEDICATION TYPE ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_occular'.$val;
				$reviewed_data_arr['Data_Base_Field_Name']= "type";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"type");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Ocular - '.trim($type);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['type'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['type'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION TITLE ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_medication'.$val;
				$reviewed_data_arr['Data_Base_Field_Name']= "title";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"title");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Name - '.trim($arr_title[$val]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['title'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['title'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION DOSES ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_dosage'.$val;
				$reviewed_data_arr['Data_Base_Field_Name']= "destination";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"destination");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Doses - '.trim($arr_dosage[$val]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['destination'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['destination'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION SIG ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_sig'.$val;
				$reviewed_data_arr['Data_Base_Field_Name']= "sig";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"sig");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Sig - '.trim($arr_sig[$val]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['sig'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['sig'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				
				//--- REVIEWED FOR MEDICATION BEGIN DATE---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_begindate'.$val;
				$reviewed_data_arr['Data_Base_Field_Name']= "begdate";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"begdate");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Begin Date - '.trim(getDateFormatDB($arr_begdate[$val]));
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['begdate'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['begdate'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION END DATE ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_enddate'.$val;
				$reviewed_data_arr['Data_Base_Field_Name']= "enddate";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"enddate");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication End Date  - '.trim(getDateFormatDB($arr_enddate[$val]));
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['enddate'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['enddate'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION REFERRED BY ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'cbMedicationStatus'.$val;
				$reviewed_data_arr['Data_Base_Field_Name']= "allergy_status";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"allergy_status");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Status  - '.trim('Active');
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['allergy_status'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['allergy_status'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION SITE BY ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_occular'.$val;
				$reviewed_data_arr['Data_Base_Field_Name']= "sites";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"sites");
				$reviewed_data_arr['Field_Text'] = 'Site  - '.trim($arr_sites[$val]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['sites'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['sites'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				//--- REVIEWED FOR MEDICATION REFERRED BY ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_comments'.$val;
				$reviewed_data_arr['Data_Base_Field_Name']= "comments";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"comments");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Comments  - '.trim($arr_comments[$val]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['comments'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['comments'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				$cls_review->reviewMedHx($med_reviewed_arr,$_SESSION['authId'],"Medications",$pid,0,0);
			}
		}
		return $counter;
	}
	
	//Returns Import files data from XML
	public function get_allergies_data_xml(&$request){
		$return_med_arr = array();
		$xml_id = $request['xml_id'];
		$arrXML = explode(",",$xml_id);
		if($handle = opendir(''.$GLOBALS['fileroot'].'/interface/Medical_history/import/import_files/')) {
			foreach($arrXML as $XML_id){
				$sql = "SELECT doc_title,file_path FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl WHERE scan_doc_id = '".$XML_id."'";
				$res = imw_query($sql);
				$row = imw_fetch_assoc($res);
				$file = $row['file_path'];
				$doc_title = $row['doc_title'];
				if( $file != ""){
					$pattern = "/(^\/PatientId_".$this->patient_id."\/CCD\/)|(.xml)/";
					$display_name = preg_replace($pattern,"",$doc_title);
					$complete_file_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$file;
					$objCDA = new CDAXMLParser($complete_file_path);
					if($request['showpage'] == 'allergies'){
						$return_med_arr[$display_name.'~~~'.$XML_id] = $objCDA->arrAllergies;
					}else if($request['showpage'] == 'problem_list'){
						$return_med_arr[$display_name.'~~~'.$XML_id] = $objCDA->arrProblemList;
					}else if($request['showpage'] == 'medications'){
						$return_med_arr[$display_name.'~~~'.$XML_id] = $objCDA->arrMedications;
					}else if($request['showpage'] == 'sx_proc'){
						$return_med_arr[$display_name.'~~~'.$XML_id] = $objCDA->arrSxProc;
					}
				}
			}
			if(!isset($request['page_request'])){
				$return_array['return_med_arr'] = $return_med_arr;
			}
		}
		return $return_array;	
	}

	//Returns medications data from DB
	public function get_all_sx_proc_data($type,&$request,$direct=''){
		$form_val_txt = 'index.php';
		if(empty($direct) == false){
			$form_val_txt = 'import_ccda.php';
		}
		$return_arr = array();
		$query = "select id,title,type,
				if((DAY(begdate)='00' OR DAY(begdate)='0') && YEAR(begdate)='0000' && (MONTH(begdate)='00' OR MONTH(begdate)='0'),'',
					if((DAY(begdate)='00' OR DAY(begdate)='0') && (MONTH(begdate)='00' OR MONTH(begdate)='0'),date_format(begdate, '%Y'),
						if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
							if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
							date_format(begdate,'".get_sql_date_format()."')
				))))as begdate1,
				referredby,comments,sites,ccda_code,referredby_id,proc_type
				from lists where pid = '".$this->patient_id."' and type in (5,6) and allergy_status != 'Deleted' order by begdate desc,id desc";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		$sx_proc_data_arr = array();
		if($cnt > 0){
			while($sxQryRes = imw_fetch_assoc($sql)){
				$dataArr = array();
				
				$dataArr['SX_ID'] = $sxQryRes['id'];
				if($dataArr['SX_ID'] != ''){
					$sx_exists = 'disabled';
				}
				$db_sx_type = $sxQryRes['type'];
				
				$dataArr['SX_TITLE'] = $sxQryRes['title'];
				$dataArr['SX_OCCULAR'] = ($db_sx_type == 6) ? 'checked' : '';
				$dataArr['SX_BEG_DATE'] = $sxQryRes['begdate1'];
				$dataArr['SX_REFFERED_BY'] = $sxQryRes['referredby'];
				$dataArr['SX_COMMENTS'] = $sxQryRes['comments'];
				$dataArr['MED_SITE'] = $sxQryRes['sites'];
				$dataArr['ccda_code'] = $sxQryRes['ccda_code'];
				$dataArr['referredby_id'] = $sxQryRes['referredby_id'];
				$dataArr['ccda_code_system_name'] = $sxQryRes['ccda_code_system_name'];
				$dataArr['ccda_code_system'] = $sxQryRes['ccda_code_system'];
				
				$dataArr['proc_type'] = $sxQryRes['proc_type'];
				
				$sx_proc_data_arr[] = $dataArr;
				
				if($db_sx_type == 6)
					$finalResArr['OCU'][] = $dataArr;
				else if($db_sx_type == 5)
					$finalResArr['SYS'][] = $dataArr;
			}
		}
		
		//Merging XML and DB records
		$form_save_action = $form_val_txt.'?showpage='.$request['showpage'].'&page_request=merge&xml_id='.$request['xml_id'].'&pt_id='.$this->patient_id.'';
		if(isset($request['page_request']) && $request['page_request'] == 'merge'){
			$form_save_action = $form_val_txt.'?showpage='.$request['showpage'].'&page_request=save_sx_proc';
			unset($finalResArr);
			$ocu_sx_plan = $sys_sx_plan = array();
			$row = $request['chk_box'];
			foreach($row as $key => $row_id){
				$tmp_arr = array();
				$type_id = $request['hid_type_id'][$row_id];
				
				$tmp_arr['SX_ID'] = $request['hid_list_id'][$row_id];
				$tmp_arr['SX_TITLE'] = $request['sx_title'][$row_id];
				$tmp_arr['SX_OCCULAR'] = 'checked';
				$tmp_arr['SX_BEG_DATE'] = $request['sx_beg_date'][$row_id];
				$tmp_arr['SX_REFFERED_BY'] = $request['sx_reff_name'][$row_id];
				$tmp_arr['SX_COMMENTS'] = $request['sx_comments'][$row_id];
				$tmp_arr['ccda_code'] = $request['sx_ccda_code'][$row_id];
				$tmp_arr['referredby_id'] = $sxQryRes['referredby_id'];
				$tmp_arr['proc_type'] = $request['sx_surgery_type'][$row_id];
				$tmp_arr['ccda_code_system'] = $request['ccda_code_system'][$row_id];
				$tmp_arr['ccda_code_system_name'] = $request['ccda_code_system_name'][$row_id];
				
				switch($type_id){
					case 6:
						$tmp_arr['MED_SITE'] = $request['sx_occular_'.$row_id.''];
						$finalResArr['OCU'][] = $tmp_arr;
					break;
					
					case 5:
						$finalResArr['SYS'][] = $tmp_arr;
					break;
					//XML Records
					case 0:
						if($request['sx_occular_'.$row_id.'']){
							$tmp_arr['MED_SITE'] = $request['sx_occular_'.$row_id.''];
							$finalResArr['OCU'][] = $tmp_arr;
						}else{
							$finalResArr['SYS'][] = $tmp_arr;
						}
					break;
				}
			}
		}
		
		$return['sx_proc_data'] = $sx_proc_data_arr;
		$return['sx_exists'] = $sx_exists;
		$return['finalResArr'] = $finalResArr;
		$return['form_save_action'] = $form_save_action;
		return $return;
		
	}
	
	//Save problem list 
	public function save_sx_proc_data($request){
		$pid = $this->patient_id;
		$db_type = '5,6';
		$sql = "select *,date_format(begdate,'".get_sql_date_format()."') as begdate from lists where pid = '$pid' and type in ($db_type) and allergy_status = 'Active'";
		$res = imw_query($sql);					 
		while($row_query  = imw_fetch_assoc($res)){
			$query_db = imw_query("update lists set allergy_status = 'Deleted' WHERE id = '".$row_query['id']."'");
			imw_free_result($query_db);
		}
		$arr_title = $request['sx_title'];
		$rec_id = $request['hid_list_id'];
		$type_id = $request['hid_type_id'];
		$begin_date = $request['sx_beg_date'];
		$reffering_name = $request['sx_reff_name'];
		$reffering_id = $request['sx_reff_id'];
		$comments = $request['sx_comments'];
		$sx_type = $request['sx_surgery_type'];
		$ccda_code = $request['ccda_code'];
		$ccda_code_system = $request['ccda_code_system'];
		$cda_code_system_name = $request['ccda_code_system_name'];
		$row = $request['chk_box'];
		$counter = 0;
		foreach($row as $key => $row_id){
			$tmp_arr = array();
			$row_red_id = $rec_id[$row_id];
			$dataArr['title'] = $arr_title[$row_id];
			$dataArr['type'] = $type_id[$row_id];
			$dataArr['begdate'] = getDateFormatDB($begin_date[$row_id]);
			$dataArr['referredby'] = $reffering_name[$row_id];
			$dataArr['comments'] = $comments[$row_id];
			$dataArr['pid'] = $pid;
			$dataArr['ccda_code'] = $ccda_code[$row_id];
			$dataArr['proc_type'] = $sx_type[$row_id];
			$dataArr['referredby_id'] = $reffering_id[$row_id];
			$dataArr['allergy_status'] = 'Active';
			$dataArr['sites'] = ($request['sx_occular_'.$row_id.'']) ? $request['sx_occular_'.$row_id.''] : 0;
			$dataArr['user'] = $_SESSION['authId'];
			if(empty($row_red_id) == false){
				$update_count = $this->updateRecords($dataArr,'lists','id',$row_red_id);
				$counter = $counter + $update_count;
			}else{
				$dataArr['date'] = date('Y-m-d H:i:s');
				$add_count = $this->addRecords($dataArr,'lists');
				$counter = $counter + $add_count;
			}
		}
		return $counter;
	}
	
	public function updateRecords($arrayRecord, $table, $condId, $condValue){
		$counter = 0;
		if(is_array($arrayRecord)){
			$countFields = count($arrayRecord);
			$updateStr = "UPDATE $table SET ";
			foreach($arrayRecord as $field => $value){
				++$seq;
				$updateStr .= "$field = '".addslashes($value)."'";
				if($seq<$countFields){
					$updateStr .= ", ";
				}
			}
			$updateStr .= " WHERE $condId = '$condValue'";
			$updateQry = imw_query($updateStr);
			$counter = $counter + imw_affected_rows();
		}		
		return $counter;
	}
	
	public function addRecords($arrayRecord, $table){
		if(is_array($arrayRecord)){
			$countFields = count($arrayRecord);
			$insertStr = "INSERT INTO $table SET ";
			foreach($arrayRecord as $field => $value){
				++$seq;
				$insertStr .= "$field = '".addslashes($value)."'";
				if($seq<$countFields){
					$insertStr .= ", ";
				}
			}
			$insertQry = imw_query($insertStr);
			$insertId = imw_insert_id();
			return $insertId;
		}		
	}
	
	//get last modified for allergies
	public function get_last_modified($id,$section = 'Allergies',&$request){
		if(!isset($request['page_request'])){
			$qryRevHis = "SELECT DATE_FORMAT(plec.date_time, '".get_sql_date_format()." %I:%i %p') as recDateTime, plec.section_table_primary_key as tablePriKey, ple.operator_id,plec.operator_id as operator_id_plec FROM patient_last_examined ple INNER JOIN patient_last_examined_child plec on ple.patient_last_examined_id = plec.master_pat_last_exam_id WHERE ple.patient_id ='".$this->patient_id."' and (ple.section_name = '$section' or ple.section_name = 'complete') and plec.section_table_primary_key='".$id."' GROUP BY plec.date_time ORDER BY plec.date_time DESC";
			
			$rsRevHis = imw_query($qryRevHis);
			$rowRevHis=imw_fetch_array($rsRevHis);
			$dtRevHis = $opName = "";
			$dtRevHis = $rowRevHis["recDateTime"];
			if($rowRevHis['operator_id_plec']>0){
				$operator_id_plec=$rowRevHis['operator_id_plec'];
			}else{
				$operator_id_plec=$rowRevHis['operator_id'];
			}
			$opName = substr($this->arrUserId[$operator_id_plec]['fname'],0,1).substr($this->arrUserId[$operator_id_plec]['lname'],0,1);
			return $dtRevHis.' '.$opName;
		}else{
			$dtRevHis = date('m-d-Y h:i A');
			$qryUser = "SELECT id, fname, mname, lname, user_type FROM users where fname!='' and lname!='' and id='".$_SESSION['authId']."' ORDER BY fname,lname ";
			$resUser = imw_query($qryUser);
			$arrUser1 = imw_fetch_array($resUser);
			$opName = substr($arrUser1["fname"],0,1).substr($arrUser1["lname"],0,1);
			return $dtRevHis.' '.$opName;
		}
		
	}
	
	//Get last modified for problem list
	public function get_last_modified_pbl($id,&$request){
		if(!isset($request['page_request'])){
			$selQuery = "select * from pt_problem_list_log where `problem_id` = '".$id."' order by id desc";
			$res2 = imw_query($selQuery);
			$resRow2 = imw_fetch_array($res2);
			$dtRevHis = get_date_format(date('Y-m-d',strtotime($resRow2["timestamp"]))).' '.date('h:i A',strtotime($resRow2["timestamp"]));
			$opName = ucfirst(substr(trim($arrUserId[$resRow2["user_id"]]['fname']),0,1)).ucfirst(substr(trim($arrUserId[$resRow2["user_id"]]['lname']),0,1));
			return $dtRevHis.' '.$opName;
		}else{
			$dtRevHis = date('m-d-Y h:i A');
			$qryUser = "SELECT id, fname, mname, lname, user_type FROM users where fname!='' and lname!='' and id='".$_SESSION['authId']."' ORDER BY fname,lname ";
			$resUser = imw_query($qryUser);
			$arrUser = imw_fetch_array($resUser);
			$opName = substr($arrUser["fname"],0,1).substr($arrUser["lname"],0,1);
			return $dtRevHis.' '.$opName;
		}
		
	}
	
	//Setting CLS Alerts
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