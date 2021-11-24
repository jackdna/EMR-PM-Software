<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Patient Info/Insurance Data
 Access Type: Indirect Access.
 
*/
include_once 'class.language.php';
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
class Insurance extends core_lang
{
	public $data = array();
	public $defaults = array();
	private $allowed_action = array('delete','save','update case','opencase','changestatus');
	public $js_alert_msg = '';	
	private $policy_status = '';
	private $patient_id = '';
	
	public $res_name = ''; public $res_name_comp = ''; 
	public $priInsCompanyName = ''; public $priInsCompanyId = ''; public $priInsInHouseCode = '';
	public $secInsCompanyName = ''; public $secInsCompanyId = ''; public $secInsInHouseCode = '';
	public $terInsCompanyName = ''; public $terInsCompanyId = ''; public $terInsInHouseCode = '';
	public $typeahead_data = '';
	
	public function __construct($patient_id)
	{
		core_lang::__construct();
		$this->patient_id = $patient_id;
		
		$temp_row	=	get_extract_record('audit_policies','policy_id','5','policy_status');
		$this->policy_status = ($temp_row['policy_status']) ? $temp_row['policy_status'] : 0;	
		
		$this->data['policy_status']	=	$this->policy_status;
		$this->data['ins_case_field']	=	($this->policy_status == 1) ? make_field_type_array('insurance_case') : false;
		$this->data['ins_data_field']	=	($this->policy_status == 1) ? make_field_type_array('insurance_data') : false;
		$this->data['pt_reff_field']	=	($this->policy_status == 1) ? make_field_type_array('patient_reff') : false;
		$this->data['pt_auth_field']	=	($this->policy_status == 1) ? make_field_type_array('patient_auth') : false;
		$this->data['patient_data']		=	get_extract_record('patient_data','id',$this->patient_id);
		
		$this->defaults['mandatory_fld']	=	get_mandatory_fields('insurance');
		$temp = parent::get_vocabulary("patient_info","insurance");
		$temp['insprovider1'] = $temp['insprovider'];
		$temp['insprovider2'] = $temp['secInsCompVal'];
		$temp['insprovider3'] = $temp['terInsCompVal'];
		$temp['lastName1'] = $temp['lastName'];
		unset($temp['insprovider']); unset($temp['secInsCompVal']);
		unset($temp['lastName']); unset($temp['terInsCompVal']);
		$this->defaults['vocabulary']			=	$temp;
	}
	
	public function set_resp_party($inactivePriInsComp)
	{
		//------- Start Responsibility Check
		$resName = "";
		if($_SESSION['new_casetype'] == 1)
		{
			$qry = "select fname,lname,mname from resp_party where patient_id ='".$_SESSION["patient"]."'";
			$sql = imw_query($qry);
			$ResDetail = imw_fetch_object($sql);
			if($ResDetail->lname != '' && $ResDetail->fname != ''){
				$resName = $ResDetail->lname.', '.$ResDetail->fname.' '.$ResDetail->mname;
			}else{
				$resName = 'Self';
			}
		}
		else
		{
			if($inactivePriInsComp){
				$qry = "select provider from insurance_data where pid='".$this->patient_id."' 
						and type='primary' and ins_caseid='".$_SESSION["currentCaseid"]."'
						and id = '".$inactivePriInsComp."'";
			}
			else{
				$qry = "select provider from insurance_data where pid='".$this->patient_id."' 
						and type='primary' and ins_caseid='".$_SESSION["currentCaseid"]."'
						and actInsComp = 1";
			}
			$sql = imw_query($qry);
			if(imw_num_rows($sql) > 0){
				$res = imw_fetch_object($sql);
				$resName = $res->provider;		
			}
			else{
				$resName = 'Unassigned';
			}
		}
		
		return $resName;		
	}
	
	public function insurance_prev_cases($case_status)
	{
		$fields = 'IC.ins_case_type, IC.ins_caseid, ICT.case_name,ICT.case_id';
		$table	=	'insurance_case IC LEFT JOIN insurance_case_types ICT on ICT.case_id = IC.ins_case_type';
		$extra	=	" AND IC.case_status = '".$case_status."' AND del_status = 0 ";
		$rows		= get_array_records($table,'IC.patient_id',$this->patient_id,$fields,$extra,'IC.ins_case_type');
		return $rows;
	}
	
	public function insurance_case_types($exisiting = '')
	{
		$exisiting = trim($exisiting);
		$exisiting = trim($exisiting,',');
		
		$fields = 'case_id, case_name';
		$table	=	'insurance_case_types';
		$extra	=	$exisiting ? " AND  case_id not in (".$exisiting.")" : '';
		$rows		= get_array_records($table,'status','0',$fields,$extra,'case_name');
		return $rows;
	}
	
	public function insurance_image_tag($image_name_db, $id, $position = 1,  $title = 'Insurance Document', $type = '')
	{
		$image_name_db = substr($image_name_db,0,1) == '/' ? substr($image_name_db,1) : $image_name_db;
		
		// extracting thumbnail image path
		$tmpArr = array_filter(explode("/",$image_name_db));
		$tmpImageName = "thumbnail/".end($tmpArr);
		array_pop($tmpArr);array_push($tmpArr,$tmpImageName);
		$thumbImageName = implode("/",$tmpArr);
		
		$image_path	=	data_path() . $image_name_db;
		$web_path	= data_path(1);
		$thumb_image_path = data_path().$thumbImageName;
		
		if( !file_exists($image_path)) return ;
		$thumbImageName = file_exists($thumb_image_path) ? $thumbImageName : $image_name_db;
		$image_path = file_exists($thumb_image_path) ? $thumb_image_path : $image_path;
		
		if(file_exists($image_path) && is_dir($image_path) == '')
		{
			$image_size = getimagesize($image_path);
			if($image_size[0] > 20){
				$new_size = newImageResize($image_path,21);
			}
			
			if(strpos($image_name_db, ".pdf")!== false){
				$image = '<img class="pointer" onClick="showPdf(\''.$web_path.$image_name_db.'\')" src="'.$GLOBALS['webroot'].'/library/images/pdfimg.png" title="'.$title.'" width="21" height="21">';
			}
			else{
				//\''.$id.'\','.$position.',\''.$type.'\'
				$image = '<img class="pointer" onClick="show_scanned(this)" src="'.$web_path.$thumbImageName.'" title="'.$title.'" '.$new_size.' data-src="'.addslashes($web_path.$image_name_db).'" style="max-height:21px!important; border:solid 1px #ccc;" data-ins-type="'.$type.'">';
			}
			
		}
		
		return $image;
	}

	public function insurance_re_arrange(&$request)
	{
		//echo __FUNCTION__; pre($request);
		
		if(!$request['re_arrange_btn']) return;
		extract($request);
		
		$ins_arr = array('Primary','Secondary','Tertiary');
		
		for($i=0;$i < count($compId); $i++)
		{
			$id = $compId[$i];
			if(empty($id) == false)
			{
				$name_data_id_arr = preg_split('/__/',$request['name_'.$ins_arr[$i]]);
				$name = $name_data_id_arr[0];
				$data_id = $name_data_id_arr[1];
				
				$insQryRes = get_array_records('insurance_data','id',$data_id,'type,provider,referal_required,auth_required,ins_caseid');
				
				$qry = "update insurance_data set type = '".$name."' where id = '".$data_id."'";
				imw_query($qry);
				switch($name)
				{
					case 'primary':
						$new_reff_type = 1;
					break;
					case 'secondary':
						$new_reff_type = 2;
					break;
					case 'tertiary':
						$new_reff_type = 3;
					break;
					
				}	
				
				if(count($insQryRes) > 0)
				{
					$type = $insQryRes[0]['type'];
					$provider = $insQryRes[0]['provider'];
					$ins_caseid = $insQryRes[0]['ins_caseid'];
					switch($type)
					{
						case 'primary':
							$old_reff_type = 1;
						break;
						case 'secondary':
							$old_reff_type = 2;
						break;
						case 'tertiary':
							$old_reff_type = 3;
						break;
						
					}
					
					//--- REFERRAL PROVIDER SWITCH ------
					if(strtolower($insQryRes[0]['referal_required']) == 'yes')
					{	
						$query = "select reff_id from patient_reff where ins_data_id = '".$data_id."'
												and ins_provider = '".$provider."' and patient_id = '".$this->patient_id."'
												and reff_type = '".$old_reff_type."' order by reff_id desc limit 0,1";
						$sql = imw_query($query);
						$row = imw_fetch_assoc($sql);
						
						$update_ref_id = $row['reff_id'];
						
						if($update_ref_id)
						{
							
							//--- UPDATE PATIENT REFERRAL ---
							$query = "update patient_reff set reff_type = '$new_reff_type' 
													where reff_id = '".$update_ref_id."'";
							imw_query($query);
							
							//--- UPDATE REFERRAL SCAN DATA ----
							$scan_data = $name."_reff";
							$query = "update upload_lab_rad_data set scan_from  = '".$scan_data."'
												where uplaod_primary_id = '".$update_ref_id."'";
							imw_query($query);
						}
					}
					
					//--- AUTH REQUIRED SWITCH ------
					if(strtolower($insQryRes[0]['auth_required']) == 'yes')
					{
						$qry = "update patient_auth set ins_type = '".$new_reff_type."' where 
											patient_id = '".$this->patient_id."' and ins_case_id = '".$ins_caseid."'";
						imw_query($qry);
					}
				}
				
			}
		
		}
	
	}
	
	public function insurance_copy(&$request)
	{
		//echo __FUNCTION__; pre($request);
		extract($request);
		
		if(!$copy_ins_submit_txt) return;
		
		$copy_ins_data_arr = preg_split('/__/',$copy_ins_data_from);
		$ins_com_type = $copy_ins_data_arr[0];
		$ins_data_id = $copy_ins_data_arr[1];
		
		$existsQryRes = get_array_records('insurance_data','id',$ins_data_id);
		
		$insDataArr = array();
		$insDataArr['type'] = $copy_ins_data_to;
		$insDataArr['provider'] = $existsQryRes[0]['provider'];
		$insDataArr['plan_name'] = $existsQryRes[0]['plan_name'];
		$insDataArr['policy_number'] = $existsQryRes[0]['policy_number'];
		$insDataArr['group_number'] = $existsQryRes[0]['group_number'];
		$insDataArr['subscriber_lname'] = $existsQryRes[0]['subscriber_lname'];
		$insDataArr['subscriber_mname'] = $existsQryRes[0]['subscriber_mname'];
		$insDataArr['subscriber_fname'] = $existsQryRes[0]['subscriber_fname'];
		$insDataArr['subscriber_relationship'] = $existsQryRes[0]['subscriber_relationship'];
		$insDataArr['subscriber_ss'] = $existsQryRes[0]['subscriber_ss'];
		$insDataArr['subscriber_DOB'] = $existsQryRes[0]['subscriber_DOB'];
		$insDataArr['subscriber_street'] = $existsQryRes[0]['subscriber_street'];
		$insDataArr['subscriber_street_2'] = $existsQryRes[0]['subscriber_street_2'];
		$insDataArr['subscriber_postal_code'] = $existsQryRes[0]['subscriber_postal_code'];
		$insDataArr['subscriber_city'] = $existsQryRes[0]['subscriber_city'];
		$insDataArr['subscriber_state'] = $existsQryRes[0]['subscriber_state'];
		$insDataArr['subscriber_country'] = $existsQryRes[0]['subscriber_country'];
		$insDataArr['subscriber_phone'] = $existsQryRes[0]['subscriber_phone'];
		$insDataArr['subscriber_biz_phone'] = $existsQryRes[0]['subscriber_biz_phone'];
		$insDataArr['subscriber_mobile'] = $existsQryRes[0]['subscriber_mobile'];
		$insDataArr['subscriber_employer'] = $existsQryRes[0]['subscriber_employer'];
		$insDataArr['subscriber_employer_street'] = $existsQryRes[0]['subscriber_employer_street'];
		$insDataArr['subscriber_employer_postal_code'] = $existsQryRes[0]['subscriber_employer_postal_code'];	
		$insDataArr['subscriber_employer_state'] = $existsQryRes[0]['subscriber_employer_state'];
		$insDataArr['subscriber_employer_country'] = $existsQryRes[0]['subscriber_employer_country'];
		$insDataArr['subscriber_employer_city'] = $existsQryRes[0]['subscriber_employer_city'];
		$insDataArr['copay'] = $existsQryRes[0]['copay'];
		$insDataArr['date'] = $existsQryRes[0]['date'];
		$insDataArr['pid'] = $this->patient_id;
		if($existsQryRes[0]['subscriber_sex']!='')
			$insDataArr['subscriber_sex'] = $existsQryRes[0]['subscriber_sex'];
		$insDataArr['copay_fixed'] = $existsQryRes[0]['copay_fixed'];
		$insDataArr['referal_required'] = 'No';
		
		// start copying images
		$scan_card_path1 = ($existsQryRes[0]['scan_card']) ? data_path().substr($existsQryRes[0]['scan_card'],1) : '';
		$scan_card_path2 = ($existsQryRes[0]['scan_card2']) ? data_path().substr($existsQryRes[0]['scan_card2'],1) : '' ;
		
		$s1 = getimagesize($scan_card_path1);
		$s2 = getimagesize($scan_card_path2);
		
		$save = new SaveFile($this->patient_id);
		$scan_card1_file = $scan_card2_file = '';
		if(file_exists($scan_card_path1)) {
			$scan_card1['name'] = end(explode('/',$existsQryRes[0]['scan_card']));
			$scan_card1['type'] = $s1['mime'];
			$scan_card1['size'] = filesize($scan_card_path1);
			$scan_card1['tmp_name'] = $scan_card_path1;
			$scan_card1_file = $save->copyFile($scan_card1,'','','',1);
		}
		
		if(file_exists($scan_card_path2)) {
			$scan_card2['name'] = end(explode('/',$existsQryRes[0]['scan_card2']));
			$scan_card2['type'] = $s2['mime'];
			$scan_card2['size'] = filesize($scan_card_path2);
			$scan_card2['tmp_name'] = $scan_card_path2;
			$scan_card2_file = $save->copyFile($scan_card2,'','','',1);
		}
		if( $scan_card1_file ) {
			$insDataArr['scan_card'] = $scan_card1_file;
			$insDataArr['scan_label'] = $existsQryRes[0]['scan_label'];
			$insDataArr['cardscan_date'] = $existsQryRes[0]['cardscan_date'];
		}
		
		if( $scan_card2_file ) {
			$insDataArr['scan_card2'] = $scan_card2_file;
			$insDataArr['scan_label2'] = $existsQryRes[0]['scan_label2'];
			$insDataArr['cardscan1_datetime'] = $existsQryRes[0]['cardscan1_datetime'];
		}
		
		$insDataArr['effective_date'] = $existsQryRes[0]['effective_date'];
		$insDataArr['expiration_date'] = $existsQryRes[0]['expiration_date'];
		$insDataArr['ins_caseid'] = $copy_to_ins_case;
		$insDataArr['claims_adjustername'] = $existsQryRes[0]['claims_adjustername'];
		$insDataArr['claims_adjusterphone'] = $existsQryRes[0]['claims_adjusterphone'];
		$insDataArr['responsible_party'] = $existsQryRes[0]['responsible_party'];
		$insDataArr['Sec_HCFA'] = $existsQryRes[0]['Sec_HCFA'];
		$insDataArr['newComDate'] = $existsQryRes[0]['newComDate'];
		$insDataArr['actInsComp'] = $existsQryRes[0]['actInsComp'];
		$insDataArr['actInsCompDate'] = $existsQryRes[0]['actInsCompDate'];
		$insDataArr['auth_required'] = '';
		$insDataArr['self_pay_provider'] = $existsQryRes[0]['self_pay_provider'];
		$insDataArr['cardscan_operator'] = $existsQryRes[0]['cardscan_operator'];
		$insDataArr['cardscan_comments'] = $existsQryRes[0]['cardscan_comments'];
		
		//--- INSERT DATA OR UPDATE INSURANCE DATA TABLE ---------
		$insertInsDataId = AddRecords($insDataArr,'insurance_data');
		if(trim($insertInsDataId) > 0)
		{
			$this->js_alert_msg = 'Insurance company copied successfully.';
		}

	}
	
	public function insurance_auth_hx($ins_type,$ins_caseid,$cur_id)
	{
		$return = array();
		if($ins_type && $ins_caseid)
		{
            switch(strtolower($ins_type)) {
                case 'primary':
                    $ins_type=1;
                    break;
                case 'secondary':
                    $ins_type=2;
                    break;
                case 'tertiary':
                    $ins_type=3;
                    break;
            }
			global $OBJCommonFunction;
			$providerArr = $OBJCommonFunction->drop_down_providers($auth_provider,'','1','true');
			
			$query = "SELECT a_id,auth_name,auth_date,auth_comment,auth_operator,AuthAmount,patient_auth.auth_status,patient_auth.end_date ,patient_auth.no_of_reffs,patient_auth.reff_used, auth_cpt_codes, auth_provider FROM patient_auth where patient_id='".$this->patient_id."' and ins_type='".$ins_type."' and a_id<>'".$cur_id."' and ins_case_id='".$ins_caseid."' and (auth_status='1' OR (end_date < current_date() AND end_date != '0000-00-00') OR reff_used >= no_of_reffs) ORDER BY auth_date desc";
			$sql = imw_query($query);
			while($row = imw_fetch_assoc($sql))
			{
				if($row['auth_date'] <> '0000-00-00'  && $row['auth_date']!='')
				{
					$auth_date1=explode('-',$row['auth_date']);
					$auth_date=$auth_date1[1].'-'.$auth_date1[2].'-'.$auth_date1[0];
				}else
				{
					$auth_date="";
				}
				if($row['auth_operator'])
				{
					$providerDetail = get_extract_record('users','id',$row['auth_operator']);
					$auth_operator=$providerDetail['username'];
				}
				$auth_end_date = get_date_format($row['end_date']);
				$auth_no_of_reffs = $row['no_of_reffs'];
				$auth_reff_used = $row['reff_used'];	
					
				if($auth_no_of_reffs + $auth_reff_used =='0')
				{
        	$auth_visit_value = "";
				}
				else
				{
						if($auth_reff_used > 0)
							$auth_visit_value = $auth_no_of_reffs .'/'.$auth_reff_used;
						else
							$auth_visit_value = $auth_no_of_reffs;
				}	
				
				$data = array();
				$data['a_id'] = $row['a_id'];
				$data['auth_name'] 		= $row['auth_name'];
				$data['auth_provider']= $providerArr[$row['auth_provider']];
				$data['auth_cpt_codes']= $row['auth_cpt_codes'];
				$data['auth_date'] 		= $auth_date;
				$data['end_date'] 		= $auth_end_date;
				$data['auth_visit'] 	= $auth_visit_value;
				$data['auth_amount'] 	= $row['AuthAmount'];
				$data['auth_comment'] = $row['auth_comment'];
				$data['auth_operator']= $auth_operator;
				$data['auth_line'] 		= $row['auth_status'];
				
				$return[] = $data;	
			}
			
		}
		return $return;
	}
	
	public function insurance_case_action($action,&$request)
	{
		$action = strtolower(trim($action));
		if(!in_array($action,$this->allowed_action))
		{
			// To change Selected Case
			if(empty($request['choose_prevcase']) == false && $action != "changeStatus")
			{
				$row = get_extract_record('insurance_case','ins_caseid',$request['choose_prevcase']);
				$_SESSION['new_casetype'] = $row['ins_case_type'];
				$_SESSION['currentCaseid'] = $row['ins_caseid'];	
			}
		}
		else
		{
			$action = str_replace(' ' ,'_',$action);
			$call_function = 'insurance_'.$action;
			$this->$call_function($request);
		}
	}
	
	// Private Functions Starts Here
	private function insurance_delete(&$request)
	{
		//echo __FUNCTION__; pre($request);
		$qry = "UPDATE insurance_case SET del_status = 1, del_datetime = '".date('Y-m-d H:i:s')."', del_operator = '".$_SESSION['authId']."', case_status = 'Close' WHERE ins_caseid = '".$_SESSION['currentCaseid']."'";
		$sql = imw_query($qry);
		
		$qry = "SELECT id FROM insurance_data WHERE ins_caseid = '".$_SESSION['currentCaseid']."'";
		$sql = imw_query($qry);
		while($row = imw_fetch_assoc($sql))
		{
			imw_query("UPDATE insurance_data SET del_status = 1, del_datetime = '".date('Y-m-d H:i:s')."', del_operator = '".$_SESSION['authId']."', actInsComp = 0 WHERE id = '".$row['id']."'" );
		}
						
		$insCaseDataFields = $this->data['ins_case_field'];
		if($this->policy_status == 1)
		{
			$opreaterId = $_SESSION['authId'];	
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];													 
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);													 
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
			// Audit Functionality
			$arrAuditTrailDelInsCase = array();
			$arrAuditTrailDelInsCase [] = 
								array(		
										"Pk_Id"=> $_SESSION['currentCaseid'],
										"Table_Name"=>"insurance_case",								
										"Data_Base_Field_Name"=> "ins_caseid" ,
										"Filed_Label"=> "case_id",
										"Filed_Text"=> "Patient Insurance Case id",								
										"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"ins_caseid") ,
										"IP"=> $ip,
										"MAC_Address"=> $_REQUEST['macaddrs'],
										"URL"=> $URL,
										"Browser_Type"=> $browserName,
										"OS"=> $os,
										"Operater_Id"=> $opreaterId,
										"Operater_Type"=> getOperaterType($opreaterId) ,
										"Machine_Name"=> $machineName,
										"Category"=> "patient_info",
										"Category_Desc"=> "insurence",	
										"Old_Value"=> $_SESSION['currentCaseid'],
										"New_Value"=> "NULL",			
										"Action"=> "delete"																																					
									);
			$table = array("insurance_case");
			$error = array($insCaseError);
			$mergedArray = array();
			if(count($table) == count($error)){
				for($a=0; $a < count($table); $a++){
					$mergedArray[] = array(
											"Table_Name"=> trim($table[$a]),
											"Error"=> trim($error[$a])
										  );
				}	
			}	

			auditTrail($arrAuditTrailDelInsCase,$mergedArray,$insert_id = 0);
		}
		
		if(imw_affected_rows())
		{
			$qry = "select ins_caseid,ins_case_type from insurance_case 
								where patient_id = '".$this->patient_id."' and case_status = 'Open'
								order by ins_case_type";
			$sql = imw_query($qry);
			
			if(imw_num_rows($sql) <= 0)
			{
				$insert_data['case_status'] = 'Open';
				$insert_data['ins_case_type'] = 1;
				$insert_data['patient_id'] = $this->patient_id;
				$insert_data['start_date'] = date('Y-m-d');
				$insert_id = AddRecords($insert_data,'insurance_case');
				$_SESSION['currentCaseid'] = $insert_id;
				$_SESSION['new_casetype'] = $insert_data['ins_case_type'];	
	
				if($this->policy_status == 1)
				{
					// Audit Functionality
					$arrAuditTrailInsCase = array();
					$arrAuditTrailInsCase [] = 
										array(		
												"Pk_Id"=> $insert_id,
												"Table_Name"=>"insurance_case",								
												"Data_Base_Field_Name"=> "ins_caseid" ,
												"Filed_Label"=> "case_id",
												"Filed_Text"=> "Patient Insurance Case id",
												"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"ins_caseid") ,
												"IP"=> $ip,
												"MAC_Address"=> $_REQUEST['macaddrs'],
												"URL"=> $URL,
												"Browser_Type"=> $browserName,
												"OS"=> $os,
												"Operater_Id"=> $opreaterId,
												"Operater_Type"=> getOperaterType($opreaterId) ,
												"Machine_Name"=> $machineName,
												"Category"=> "patient_info",
												"Category_Desc"=> "insurence",	
												"New_Value"=> $insert_id,			
												"Action"=> "add"																																					
											);
					$arrAuditTrailInsCase [] = 
										array(		
												"Pk_Id"=> $insert_id,
												"Table_Name"=>"insurance_case",								
												"Data_Base_Field_Name"=> "ins_case_type" ,
												"Filed_Label"=> "inscasetype",
												"Filed_Text"=> "Patient Insurance Case Type",
												"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"ins_case_type") ,								
												"New_Value"=> $insert_data['ins_case_type'],		
												"Action"=> "add"																																					
											);	
					$arrAuditTrailInsCase [] = 
										array(		
												"Pk_Id"=> $insert_id,
												"Table_Name"=>"insurance_case",								
												"Data_Base_Field_Name"=> "patient_id" ,
												"Filed_Label"=> "patient_id",
												"Filed_Text"=> "Patient Insurance Patient Id",
												"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"patient_id") ,								
												"New_Value"=> $insert_data['patient_id'],		
												"Action"=> "add"																																					
											);	
					$arrAuditTrailInsCase [] = 
										array(		
												"Pk_Id"=> $insert_id,
												"Table_Name"=>"insurance_case",								
												"Data_Base_Field_Name"=> "start_date" ,
												"Filed_Label"=> "case_startdate",
												"Filed_Text"=> "Patient Insurance Case Start Date",
												"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"start_date") ,								
												"New_Value"=> $insert_data['start_date'],		
												"Action"=> "add"																																					
											);
					$arrAuditTrailInsCase [] = 
										array(		
												"Pk_Id"=> $insert_id,
												"Table_Name"=>"insurance_case",								
												"Data_Base_Field_Name"=> "end_date" ,
												"Filed_Label"=> "case_enddate",
												"Filed_Text"=> "Patient Insurance Case End Date",
												"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"end_date") ,								
												"New_Value"=> "0000-00-00 00:00:00",		
												"Action"=> "add"																																					
											);
					$arrAuditTrailInsCase [] = 
										array(		
												"Pk_Id"=> $insert_id,
												"Table_Name"=>"insurance_case",								
												"Data_Base_Field_Name"=> "case_status" ,
												"Filed_Label"=> "case_status",
												"Filed_Text"=> "Patient Insurance Case Status",
												"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"case_status") ,								
												"New_Value"=> $insert_data['case_status'],		
												"Action"=> "add"																																					
											);
					$table = array("insurance_case");
					$error = array($insCaseError);
					$mergedArray = array();

					if(count($table) == count($error)){
						for($a=0; $a < count($table); $a++){
							$mergedArray[] = array(
													"Table_Name"=> trim($table[$a]),
													"Error"=> trim($error[$a])
												  );
						}	
					}

					auditTrail($arrAuditTrailInsCase,$mergedArray,$insert_id);
				}																																												
			}else{
				$sessionDetails = imw_fetch_object($sql);
				$_SESSION['currentCaseid'] = $sessionDetails->ins_caseid;
				$_SESSION['new_casetype'] = $sessionDetails->ins_case_type;
			}
			
			$this->js_alert_msg = imw_msg('del_succ');
			
		}

	}
	
	private function insurance_update_case(&$request)
	{
		extract($request);
		//echo __FUNCTION__; pre($request);	
		
		if(empty($inscasetype) == true){
			$inscasetype = $adminInsCaseTypeId;
		}
		$query = "select ins_caseid from insurance_case where patient_id = '".$this->patient_id."' 
								And case_status = '".$case_status."'";
								
		if($case_status <> "Close")
		{
			$query .= " And ins_case_type = '".$inscasetype."'";
		}
			
		if(empty($choose_prevcase) == false){
			$query .= " And ins_caseid <> '".$choose_prevcase."'";
		}
		
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		
		$caseData = array();
		// ($case_status == "Close") ? $request['adminInsCaseTypeId'] : $inscasetype;
		$caseData['ins_case_type'] = $inscasetype;
		$caseData['start_date'] = getDateFormatDB($case_startdate);
		$caseData['end_date'] = getDateFormatDB($case_enddate);
		$caseData['case_status'] = $case_status;
			
		if( ($cnt <= 0 && $case_status <> 'Close') || $case_status == 'Close')
		{
			$insertId = UpdateRecords($_SESSION['currentCaseid'],'ins_caseid',$caseData,'insurance_case');
			
			if($this->policy_status == 1)
			{
				// Audit Trail Functionality
				$arrAuditTrailInsCase = array();
				$arrAuditTrailInsCase = unserialize(urldecode($request['hidDataInsCase']));

				$table = array("insurance_case");
				$error = array($insCaseError);
				$mergedArray = array();
				if(count($table) == count($error)){
					for($a=0; $a < count($table); $a++){
						$mergedArray[] = array(
												"Table_Name"=> trim($table[$a]),
												"Error"=> trim($error[$a])
											  );
					}	
				}
				auditTrail($arrAuditTrailInsCase,$mergedArray,$_SESSION['currentCaseid'],0,0);
			}
			
			$_SESSION['new_casetype'] = $inscasetype;
			
		}
		else{
			$this->js_alert_msg = 'Case type Already Exists.';
		}
	}
	
	private function insurance_opencase(&$request)
	{
		//echo __FUNCTION__; pre($request);	
		extract($request);
			
		$qry = "select ins_caseid from insurance_case where ins_case_type = ".$inscasetype."
							And patient_id = ".$this->patient_id." And case_status = '".$case_status."' ";
		$sql = imw_query($qry);
		
		if(imw_num_rows($sql) <= 0)
		{		
			$caseData = array();
			$caseData['ins_case_type'] = $inscasetype;
			$caseData['patient_id'] = $this->patient_id;
			$caseData['start_date'] = getDateFormatDB($case_startdate);
			$caseData['end_date'] = getDateFormatDB($case_enddate);
			$caseData['case_status'] = $case_status;
			$insertId = AddRecords($caseData,'insurance_case');
			
			$insCaseDataFields = $this->data['ins_case_field'];
			if($this->policy_status == 1)
			{
				$opreaterId = $_SESSION['authId'];	
				$ip = getRealIpAddr();
				$URL = $_SERVER['PHP_SELF'];													 
				$os = getOS();
				$browserInfoArr = array();
				$browserInfoArr = _browser();
				$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
				$browserName = str_replace(";","",$browserInfo);													 
				$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			
				// Audit Trail Functionality
				$arrAuditTrailInsCase = array();
				$arrAuditTrailInsCase [] = 
									array(		
											"Pk_Id"=> $insertId,
											"Table_Name"=>"insurance_case",								
											"Data_Base_Field_Name"=> "ins_caseid" ,
											"Filed_Label"=> "case_id",
											"Filed_Text"=> "Patient Insurance Case id",
											"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"ins_caseid") ,
											"IP"=> $ip,
											"MAC_Address"=> $_REQUEST['macaddrs'],
											"URL"=> $URL,
											"Browser_Type"=> $browserName,
											"OS"=> $os,
											"Operater_Id"=> $opreaterId,
											"Operater_Type"=> getOperaterType($opreaterId) ,
											"Machine_Name"=> $machineName,
											"Category"=> "patient_info",
											"Category_Desc"=> "insurence",	
											"New_Value"=> $insertId,			
											"Action"=> "add"																																					
										);
				$arrAuditTrailInsCase [] = 
									array(		
											"Pk_Id"=> $insertId,
											"Table_Name"=>"insurance_case",								
											"Data_Base_Field_Name"=> "ins_case_type" ,
											"Filed_Label"=> "inscasetype",
											"Filed_Text"=> "Patient Insurance Case Type",
											"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"ins_case_type") ,								
											"New_Value"=> $caseData['ins_case_type'],		
											"Action"=> "add"																																					
										);	
				$arrAuditTrailInsCase [] = 
									array(		
											"Pk_Id"=> $insertId,
											"Table_Name"=>"insurance_case",								
											"Data_Base_Field_Name"=> "patient_id" ,
											"Filed_Label"=> "patient_id",
											"Filed_Text"=> "Patient Insurance Patient Id",
											"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"patient_id") ,								
											"New_Value"=> $caseData['patient_id'],		
											"Action"=> "add"																																					
										);	
				$arrAuditTrailInsCase [] = 
									array(		
											"Pk_Id"=> $insertId,
											"Table_Name"=>"insurance_case",								
											"Data_Base_Field_Name"=> "start_date" ,
											"Filed_Label"=> "case_startdate",
											"Filed_Text"=> "Patient Insurance Case Start Date",
											"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"start_date") ,								
											"New_Value"=> $caseData['start_date'],		
											"Action"=> "add"																																					
										);
				$arrAuditTrailInsCase [] = 
									array(		
											"Pk_Id"=> $insertId,
											"Table_Name"=>"insurance_case",								
											"Data_Base_Field_Name"=> "end_date" ,
											"Filed_Label"=> "case_enddate",
											"Filed_Text"=> "Patient Insurance Case End Date",
											"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"end_date") ,								
											"New_Value"=> $caseData['end_date'],		
											"Action"=> "add"																																					
										);
				$arrAuditTrailInsCase [] = 
									array(		
											"Pk_Id"=> $insertId,
											"Table_Name"=>"insurance_case",								
											"Data_Base_Field_Name"=> "case_status" ,
											"Filed_Label"=> "case_status",
											"Filed_Text"=> "Patient Insurance Case Status",
											"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"case_status") ,								
											"New_Value"=> $caseData['case_status'],		
											"Action"=> "add"																																					
										);
				$table = array("insurance_case");
				$error = array($insCaseError);
				$mergedArray = array();
				if(count($table) == count($error)){
					for($a=0; $a < count($table); $a++){
						$mergedArray[] = array(
												"Table_Name"=> trim($table[$a]),
												"Error"=> trim($error[$a])
											  );
					}	
				}			
				auditTrail($arrAuditTrailInsCase,$mergedArray,$insertId);
			}
						
			$_SESSION['new_casetype'] = $inscasetype;
			$_SESSION['currentCaseid'] = $insertId;				
		}
		else
		{
			$this->js_alert_msg = 'Case type Already Exists.';
		}

		
	}
	
	private function insurance_changestatus(&$request)
	{
		//echo __FUNCTION__; pre($request);	
		$_SESSION['currentCaseid'] = '';
		$_SESSION['new_casetype'] = '';
	}

	private function insurance_save(&$request)
	{
		//echo __FUNCTION__; pre($request);	
		
		extract($request);
		
		if(!isset($session_patient) || empty($session_patient)){
			$session_patient = $this->patient_id;
		}
		
		if(!isset($session_currentCaseid) || empty($session_currentCaseid))
		{
			$session_currentCaseid = $_SESSION['currentCaseid'];
		}
		
		$primary_saving = false;
		$secondary_saving = false;
		$tertiary_saving = false;
		
		// Remove Patient DIR  CODE
		
		//--- get insurance case type check ----
		$new_casetype = $_SESSION['new_casetype'];
		$caseQryRes = get_array_records('insurance_case_types','case_id',$new_casetype,'vision, normal');
		
		if($this->policy_status == 1)
		{
			// Audit Hidden Fields
			$arrAuditTrailPri = unserialize(urldecode($_REQUEST['hidDataPri']));
			$arrAuditTrailPriRef = unserialize(urldecode($_REQUEST['hidDataPriRef']));

			$arrAuditTrailSec = unserialize(urldecode($_REQUEST['hidDataSec']));
			$arrAuditTrailSecRef = unserialize(urldecode($_REQUEST['hidDataSecRef']));

			$arrAuditTrailTer = unserialize(urldecode($_REQUEST['hidDataTer']));
			$arrAuditTrailTerRef = unserialize(urldecode($_REQUEST['hidDataTerRef']));
		}
		
		$patientRefDataFields = $this->data['pt_reff_field'];
		$patientAuthDataFields = $this->data['pt_auth_field'];

		
		$i1providerRCOCodeV = $i1providerRCOIdV = "";
		if(trim($i1provider) == "")
		{
			$insprovider1 = explode("*",$insprovider1);
			if(constant("EXTERNAL_INS_MAPPING") == "YES")
			{
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $insprovider1[0]);
				$i1providerRCOCodeV = trim($arrTempProRCO[0]);
				$i1provider = trim($insprovider1[1]);
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $i1provider);
				$i1provider = trim($arrTempProRCO[0]);
				$i1providerRCOIdV = trim($arrTempProRCO[1]);
			}
			else
			{
				$i1provider = trim($insprovider1[1]);
			}
		}
		else{
			$i1providerRCOCodeV = $request['i1providerRCOCode'];
			$i1providerRCOIdV = $request['i1providerRCOId'];
		}
		
		$Data1['provider'] = $i1provider;
		$Data1['plan_name'] = $i1plan_name;
		$Data1['policy_number'] = $i1policy_number;
		$Data1['group_number'] = $i1group_number;
		$Data1['co_ins'] = $i1co_ins;
		$Data1['subscriber_fname'] = $i1subscriber_fname;
		$Data1['subscriber_mname'] = $i1subscriber_mname;
		$Data1['subscriber_lname'] = $lastName1;
		$Data1['subscriber_suffix'] = $suffix_rel_pri;
		$Data1['subscriber_relationship'] = $i1subscriber_relationship;
		$Data1['subscriber_ss'] = $i1subscriber_ss;
		$Data1['subscriber_DOB'] = getDateFormatDB($i1subscriber_DOB);
		$Data1['referal_required'] = $i1referalreq;
		$Data1['subscriber_street'] = ucfirst($i1subscriber_street);
		$Data1['subscriber_street_2'] = ucfirst($i1subscriber_street_2);
		$Data1['subscriber_postal_code'] = $i1subscriber_postal_code;
		$Data1['zip_ext'] = $i1subscriber_zip_ext;
		$Data1['subscriber_city'] = ucfirst($i1subscriber_city);
		$Data1['subscriber_state'] = ucfirst($i1subscriber_state);
		$Data1['subscriber_country'] = ucfirst($i1subscriber_country);
		$Data1['subscriber_phone'] = core_phone_unformat($i1subscriber_phone);
		$Data1['subscriber_biz_phone'] = core_phone_unformat($i1subscriber_biz_phone);
		$Data1['subscriber_biz_phone_ext'] = $i1subscriber_biz_phone_ext;
		$Data1['subscriber_mobile'] = core_phone_unformat($i1subscriber_mobile);
		$Data1['subscriber_employer'] = $i1subscriber_employer;
		$Data1['subscriber_employer_street'] = $i1subscriber_employer_street;
		$Data1['subscriber_employer_postal_code'] = $i1subscriber_employer_postal_code;
		$Data1['subscriber_employer_state'] = $i1subscriber_employer_state;
		$Data1['subscriber_employer_country'] = $i1subscriber_employer_country;
		$Data1['subscriber_employer_city'] = $i1subscriber_employer_city;
		$Data1['copay'] = $i1copay;
		$Data1['copay_fixed'] = $i1copay_fixed;
		$Data1['copay_type'] = $pri_copay_type;
		if($i1subscriber_sex != '')
			$Data1['subscriber_sex'] = $i1subscriber_sex;
		$Data1['effective_date'] = getDateFormatDB($i1effective_date);
		$Data1['expiration_date'] = getDateFormatDB($i1expiration_date);
		$Data1['ins_caseid'] = $session_currentCaseid;
		$Data1['claims_adjustername'] = $i1claims_adjustername;
		$Data1['claims_adjusterphone'] = $i1claims_adjusterphone;
		$Data1['responsible_party'] = $i1responsible_party;
		$Data1['Sec_HCFA'] = $secHCFA;
		$Data1['type'] = 'primary';
		$Data1['pid'] = $session_patient;
		$Data1['newComDate'] = date('Y-m-d');
		$Data1['auth_required'] = $i1authreq;
		$Data1['self_pay_provider'] = $self_pay_provider;
		$Data1['comments'] = addslashes($i1comments);
		
		$Data1['rco_code'] = $i1providerRCOCodeV;
		$Data1['rco_code_id'] = $i1providerRCOIdV;
		
		$actDatePri = strtotime(getDateFormatDB($i1effective_date));
		$expDatePri = strtotime(getDateFormatDB($i1expiration_date));
		$curDate = strtotime(date('Y-m-d'));
		if($expPreviousPri != '')
		{
			list($month,$day,$year) = explode("-",$expPreviousPri);
			$expPreviousPriDate = $year.'-'.$month.'-'.($day-1);
		}
		$get_encounter = false;
		$act_date = getDateFormatDB($i1effective_date);
		$query = "select count(encounter_id) as row_count from patient_charge_list
								where del_status='0' and date_of_service >= '".$act_date."' and primaryInsuranceCoId != '".$i1provider."'
								and primaryInsuranceCoId > '0' and patient_id = '".$session_patient."'";
		$sql = imw_query($query);
		$row = imw_fetch_assoc($sql);
			
		if($row['row_count'] > 0) $get_encounter = true;
			
		if($i1expiration_date)
		{
				if($expDatePri <= $curDate){
					$Data1['actInsComp'] = 0;
				}
				else{
					$Data1['actInsComp'] = 1;
				}
		}
		else
		{
			$Data1['actInsComp'] = 1;
		}	
		
		$priDeactive = 1;
		if($primaryId == ''){
			$primaryId = $primaryMainId;
		}
		
		
		//--- get Scan card document for new Primary insurance company -----
		if($i1provider)
		{
			if($pri_scan_documents_id)
			{
				$query = "select * from insurance_scan_documents 
										where scan_documents_id = '".$pri_scan_documents_id."' and document_status = '0'";
				$sql = imw_query($query);
				$scanCardDetails = imw_fetch_assoc($sql);
													
				$Data1['scan_card'] = $scanCardDetails['scan_card'];
				$Data1['scan_label'] = $scanCardDetails['scan_label'];
				$Data1['scan_card2'] = $scanCardDetails['scan_card2'];
				$Data1['scan_label2'] = $scanCardDetails['scan_label2'];
				$Data1['cardscan_date'] = $scanCardDetails['cardscan_date'];
				$Data1['cardscan1_datetime'] = $scanCardDetails['cardscan1_date'];
				
				$query = "update insurance_scan_documents set document_status = '1'
										where scan_documents_id = '".$pri_scan_documents_id."'";
				imw_query($query);
			}
		}
		
		
		$active_case_exist=0;$ret_save_ins=0;
		$qry_check_ins	=	"SELECT id from insurance_data where type='primary' and pid = '".$session_patient."' and actInsComp = '1' 
														and ins_caseid='".$ins_caseid."' and del_status!='1'";
		$res_check_ins = imw_query($qry_check_ins);
		$num_rows_ins = imw_num_rows($res_check_ins);
		if($num_rows_ins>0){
			$active_case_exist=1;
		}
		
		if($primaryMainId)
		{
			$qry_check_current_ins="Select id from insurance_data where id='".$primaryMainId."' and actInsComp='0' LIMIT 0,1";
			$res_check_current_ins=imw_query($qry_check_current_ins);
			if(imw_num_rows($res_check_current_ins)>0 && $active_case_exist==1){
				$this->js_alert_msg .= "Please expire previous primary insurance.<br>";
				$ret_save_ins=1;
			}
			
		}
		
		
		if(($i1provider || $Data1['self_pay_provider']) && $ret_save_ins==0)
		{
				$query = "select provider from insurance_data where ins_caseid = '".$ins_caseid."'
						and pid = '".$session_patient."' and type = 'primary' and provider = '".$i1provider."'
						and actInsComp = '1'";
				if($primaryMainId){
					$query .= " and id != '".$primaryMainId."'";
				}	
				$sql = imw_query($query);
				$row = imw_fetch_assoc($sql);
				$insProvider1 = $row['provider'];
	
				if($insProvider1){
					$this->js_alert_msg .='Primary Provider Already Exists To This Case Id.<br>';
				}
				else
				{
					if($primaryId){
						$query = "select expiration_date from insurance_data where id = '".$primaryId."'";
						$sql = imw_query($query);
						$row = imw_fetch_assoc($sql);
						$expiration_date = $row['expiration_date'];
						if($expiration_date){
							$expPreviousPriDate = $expiration_date;
						}
						$query = "update insurance_data set actInsComp = '".$priDeactive."',
											expiration_date = '".$expPreviousPriDate."' where id = '".$primaryId."'";
						$sql = imw_query($query);
						if($sql){
							$primary_saving = true;
						}
						
					}
					if($newInsComp1)
					{
						$Data1['source'] = core_refine_user_input($_SERVER['HTTP_REFERER']);
						$insertId = AddRecords($Data1,'insurance_data');
						imw_query("UPDATE upload_lab_rad_data SET ins_data_id = '".$insertId."' 
													WHERE uplaod_primary_id = 0 
																AND patient_id = '".$session_patient."'
																AND ins_type = 1 ");	
						if($insertId != "")
						{
							$primary_saving = true;
							// BEGIN ATTACH SCANNED DOCUMENTS WITH INSURANCE_DATA IF DONE IN ONE STEP 
								if(!$pri_scan_documents_id)
								{
									$query = "select scan_documents_id,scan_card,scan_label,scan_card2,scan_label2,cardscan_date,cardscan1_date
																	from insurance_scan_documents 
																	where type = 'primary' and ins_caseid = '".$session_currentCaseid."'
																	and patient_id = '".$session_patient."' and document_status = '0'";
									$sql = imw_query($query);
									$scanPriCardDetails = imw_fetch_assoc($sql);								 
									$arrScanData = array();
									$arrScanData['scan_card'] = $scanPriCardDetails['scan_card'];
									$arrScanData['scan_label'] = $scanPriCardDetails['scan_label'];
									$arrScanData['scan_card2'] = $scanPriCardDetails['scan_card2'];
									$arrScanData['scan_label2'] = $scanPriCardDetails['scan_label2'];
									$arrScanData['cardscan_date'] = $scanPriCardDetails['cardscan_date'];
									$arrScanData['cardscan1_datetime'] = $scanPriCardDetails['cardscan1_date'];
									UpdateRecords($insertId,'id',$arrScanData,'insurance_data','',false);
									unset($arrScanData);
									$query = "update insurance_scan_documents set document_status = '1'
											where scan_documents_id = '".$scanPriCardDetails['scan_documents_id']."'";
									imw_query($query);
								}
							// END ATTACH SCANNED DOCUMENTS WITH INSURANCE_DATA IF DONE IN ONE STEP 
			
						}
						list($month, $day, $year) = explode('-',$i1subscriber_DOB);
						$i1subscriber_DOB = $year."-".$month."-".$day;
						if($i1subscriber_DOB !="--"){
							$request['i1subscriber_DOB'] = $i1subscriber_DOB;
						}
						else{
							$request['i1subscriber_DOB'] = "";
						}
						$action = "add";			
						if($hid_create_acc_pri_ins_sub == "yes")
						{
							$arrRemotePtData = array();
							$arrRemotePtData['ptfname'] = $i1subscriber_fname;
							$arrRemotePtData['ptlname'] = $lastName1;
							$arrRemotePtData['ptdob'] = $Data1['subscriber_DOB'];
							if($i1subscriber_sex != '')
							$arrRemotePtData['ptgender'] = $i1subscriber_sex;
							$arrRemotePtData['ptzip'] = $i1subscriber_postal_code;
							$arrRemotePtData['ptzip_ext'] = $i1subscriber_zip_ext;
							
							$arr_patient_next_id = get_Next_PatientID($arrRemotePtData);
							if($arr_patient_next_id['error']==''){
								$pri_patient_next_id = $arr_patient_next_id['patient_id'];
								$pri_patient_src_server = $arr_patient_next_id['src_server'];
							}else{
								echo $arr_patient_next_id['error']; exit;
							}
							$arrPriNewSepAcc = array();
							$arrPriNewSepAcc["fname"] = $i1subscriber_fname;
							$arrPriNewSepAcc["mname"] = $i1subscriber_mname;
							$arrPriNewSepAcc["lname"] = $lastName1;
							$arrPriNewSepAcc["street"] = $hid_pat_steet;
							$arrPriNewSepAcc["street2"] = $hid_pat_steet_2;
							$arrPriNewSepAcc["city"] = $hid_pat_city;
							$arrPriNewSepAcc["state"] = $hid_pat_state;
							$arrPriNewSepAcc["postal_code"] = $hid_pat_zip_code;
							$arrPriNewSepAcc["country_code"] = "USA";
							$arrPriNewSepAcc["ss"] = $i1subscriber_ss;
							$arrPriNewSepAcc["DOB"] = $i1subscriber_DOB;
							$arrPriNewSepAcc["patientStatus"] = "Active";
							if($i1subscriber_sex != '')
							$arrPriNewSepAcc["sex"] = $i1subscriber_sex;	 			 
							$arrPriNewSepAcc["pid"] = $pri_patient_next_id;	
							$arrPriNewSepAcc["id"] = $pri_patient_next_id;	
							$arrPriNewSepAcc["date"] = date('Y-m-d H:i:s');
							$arrPriNewSepAcc["created_by"] = $_SESSION['authId'];			 		
							if($pri_patient_src_server>0){$arrPriNewSepAcc["src_server"] = $pri_patient_src_server;}
							//--- ADD NEW PATIENT (ability to create an account whenever a New Insurance Subscriber is added)----
							$sub_pri_pat_id_new=AddRecords($arrPriNewSepAcc,"patient_data");
							
							$arr_sub_pat_data=array();
							$arr_sub_ins_data["sub_pat_id"]=$sub_pat_id_new;
							UpdateRecords($insertId,'id',$arr_sub_ins_data,'insurance_data','',false);
						}
						else
						{
							if($sub_pri_pat_id){
								
								$arr_sub_pat_data=array();
								$arr_sub_ins_data["sub_pat_id"]=$sub_pri_pat_id;
								UpdateRecords($insertId,'id',$arr_sub_ins_data,'insurance_data','',false);
											
								$arr_sub_pat_data=array();
								$arr_sub_pat_data['street'] = ucfirst($i1subscriber_street);
								$arr_sub_pat_data['street2'] = ucfirst($i1subscriber_street_2);
								$arr_sub_pat_data['postal_code'] = $i1subscriber_postal_code;
								$arr_sub_pat_data['city'] = ucfirst($i1subscriber_city);
								$arr_sub_pat_data['state'] = ucfirst($i1subscriber_state);
								$arr_sub_pat_data['phone_home'] = core_phone_unformat($i1subscriber_phone);
								$arr_sub_pat_data['phone_biz'] = core_phone_unformat($i1subscriber_biz_phone);
								$arr_sub_pat_data['phone_biz_ext'] = $i1subscriber_biz_phone_ext;
								$arr_sub_pat_data['phone_cell'] = core_phone_unformat($i1subscriber_mobile);
				
								UpdateRecords($sub_pri_pat_id,'id',$arr_sub_pat_data,'patient_data');
							}
						}
					}
					else
					{		
						$insertId = UpdateRecords($primaryMainId,'id',$Data1,'insurance_data','',false);
						list($month, $day, $year) = explode('-',$i1subscriber_DOB);
						$i1subscriber_DOB = $year."-".$month."-".$day;
						if($i1subscriber_DOB !="--"){
							$request['i1subscriber_DOB'] = $i1subscriber_DOB;
						}
						else{
							$request['i1subscriber_DOB'] = "";
						}
					
						/*--UPDATING RECORD WHERE SUBSCRIBER IS SELF PRIMAY SUBSCRIBER WITH ACTIVE INSURANCE POLICY--*/
						$query_subs_update = "UPDATE insurance_data SET 
														subscriber_street = '".$Data1['subscriber_street']."', 
														subscriber_street_2 = '".$Data1['subscriber_street_2']."', 
														subscriber_postal_code = '".$Data1['subscriber_postal_code']."', 
														subscriber_city = '".$Data1['subscriber_city']."', 
														subscriber_state = '".$Data1['subscriber_state']."', 
														subscriber_country = '".$Data1['subscriber_country']."', 
														subscriber_phone = '".$Data1['subscriber_phone']."', 
														subscriber_biz_phone = '".$Data1['subscriber_biz_phone']."',
														subscriber_biz_phone_ext = '".$Data1['subscriber_biz_phone_ext']."',
														subscriber_mobile = '".$Data1['subscriber_mobile']."', 
														WHERE pid = '".$sub_pri_pat_id."' 
														AND subscriber_relationship = 'self' 
														AND type = 'primary'";
						$query = $query_subs_update;
						$qry = imw_query($query);
						/*--UPDATING SUBSCRIBER END--*/
						
						$action = "update";
						if($hid_create_acc_pri_ins_sub == "yes")
						{			
							$arrRemotePtData = array();
							$arrRemotePtData['ptfname'] = $i1subscriber_fname;
							$arrRemotePtData['ptlname'] = $lastName1;
							$arrRemotePtData['ptdob'] = $Data1['subscriber_DOB'];
							if($i1subscriber_sex != '')
							$arrRemotePtData['ptgender'] = $i1subscriber_sex;
							$arrRemotePtData['ptzip'] = $i1subscriber_postal_code;
							$arrRemotePtData['ptzip_ext'] = $i1subscriber_zip_ext;
							
							$arr_patient_next_id = get_Next_PatientID($arrRemotePtData);
							if($arr_patient_next_id['error']==''){
								$pri_patient_next_id = $arr_patient_next_id['patient_id'];
								$pri_patient_src_server = $arr_patient_next_id['src_server'];
							}else{
								echo $arr_patient_next_id['error'];exit;
							}
							
							$arrPriNewSepAcc = array();
							$arrPriNewSepAcc["fname"] = $i1subscriber_fname;
							$arrPriNewSepAcc["mname"] = $i1subscriber_mname;
							$arrPriNewSepAcc["lname"] = $lastName1;
							$arrPriNewSepAcc["street"] = $hid_pat_steet;
							$arrPriNewSepAcc["street2"] = $hid_pat_steet_2;
							$arrPriNewSepAcc["city"] = $hid_pat_city;
							$arrPriNewSepAcc["state"] = $hid_pat_state;
							$arrPriNewSepAcc["postal_code"] = $hid_pat_zip_code;
							$arrPriNewSepAcc["country_code"] = "USA";
							$arrPriNewSepAcc["ss"] = $i1subscriber_ss;
							$arrPriNewSepAcc["DOB"] = $i1subscriber_DOB;
							$arrPriNewSepAcc["patientStatus"] = "Active";
							if($i1subscriber_sex != '')
							$arrPriNewSepAcc["sex"] = $i1subscriber_sex;	 			 
							$arrPriNewSepAcc["pid"] = $pri_patient_next_id;	
							$arrPriNewSepAcc["id"] = $pri_patient_next_id;
							$arrPriNewSepAcc["date"] = date('Y-m-d H:i:s');
							$arrPriNewSepAcc["created_by"] = $_SESSION['authId'];
							if($pri_patient_src_server>0){$arrPriNewSepAcc["src_server"] = $pri_patient_src_server;}
							//--- ADD NEW PATIENT (ability to create an account whenever a New Insurance Subscriber is added)----
							$sub_pri_pat_id_new = AddRecords($arrPriNewSepAcc,"patient_data");
							
							$arr_sub_pat_data=array();
							$arr_sub_ins_data["sub_pat_id"]=$sub_pri_pat_id_new;
							UpdateRecords($primaryMainId,'id',$arr_sub_ins_data,'insurance_data','',false);
						}
						else
						{
							if($sub_pri_pat_id)
							{
								$arr_sub_pat_data=array();
								$arr_sub_ins_data["sub_pat_id"]=$sub_pri_pat_id;
								UpdateRecords($primaryMainId,'id',$arr_sub_ins_data,'insurance_data','',false);
								
								$arr_sub_pat_data=array();
								$arr_sub_pat_data['street'] = ucfirst($i1subscriber_street);
								$arr_sub_pat_data['street2'] = ucfirst($i1subscriber_street_2);
								$arr_sub_pat_data['postal_code'] = $i1subscriber_postal_code;
								$arr_sub_pat_data['zip_ext'] = $i1subscriber_zip_ext;
								$arr_sub_pat_data['city'] = ucfirst($i1subscriber_city);
								$arr_sub_pat_data['state'] = ucfirst($i1subscriber_state);
								$arr_sub_pat_data['phone_home'] = core_phone_unformat($i1subscriber_phone);
								$arr_sub_pat_data['phone_biz'] = core_phone_unformat($i1subscriber_biz_phone);
								$arr_sub_pat_data['phone_biz_ext'] = $i1subscriber_biz_phone_ext;
								$arr_sub_pat_data['phone_cell'] = core_phone_unformat($i1subscriber_mobile);
								UpdateRecords($sub_pri_pat_id,'id',$arr_sub_pat_data,'patient_data');
							}
						}
					}
					
					//--- AUDIT TRIAL CODE ----
					if($this->policy_status == 1){
						
						//Audit Functionality Here
						foreach((array)$arrAuditTrailPri as $key => $value) {
							if($arrAuditTrailPri [$key]["Ins_Type"]=="primary"){
								$arrAuditTrailPri [$key]["Action"] = $action;
							}
						}		
						$table = array("insurance_data");
						$error = array($insError);
						$tableError = array();
						if(count($table) == count($error)){
							for($a=0; $a < count($table); $a++){
								$mergedArray[] = array(
														"Table_Name"=> trim($table[$a]),
														"Error"=> trim($error[$a])
													  );
							}
						}	
						auditTrail($arrAuditTrailPri,$mergedArray,$insertId);
						
					}	
					
				}
				
		}
		
		
		//------ Insert Data For Refferal Required ---------------
		$pri_ref_phy_id_arr = $request['ref1_phyId'];
		if(!is_array($pri_ref_phy_id_arr))
		{
			$pri_ref_phy_id_arr = array($pri_ref_phy_id_arr);	
		}
		
		foreach($pri_ref_phy_id_arr as $key => $ref1_phyId_str)
		{
			
			if(trim($ref1_phyId_str) == "" || trim($ref1_phyId_str) =="0")
			{
				continue;	
			}
			
			$ref_id_pri = $request['ref_id_pri'][$key];
			$ref1_phy_str = $request['ref1_phy'][$key];
			$reffral_no1_str = $request['reffral_no1'][$key];	
			$reff1_date_str = $request['reff1_date'][$key];	
			$end1_date_str = $request['end1_date'][$key];	
			$eff1_date_str = $request['eff1_date'][$key];
			$no_ref1_str = $request['no_ref1'][$key];
			$note1_str = $request['note1'][$key];
			
			if(trim($ref1_phyId_str) == "")
			{
				if($ref1_phy_str)
				{
					$Reffer_physician_arr = preg_split('/,/',$ref1_phy_str);
					$phyLnameArr = explode(' ',trim($Reffer_physician_arr[0]));
					$phylname = trim($phyLnameArr[0]);
					$phyFnameArr = explode(' ',trim($Reffer_physician_arr[1]));
					$phyfname = trim($phyFnameArr[0]);
					$ref_phy_res_id = get_reffer_physician_id('FirstName',$phyfname,'LastName',$phylname);
					$ref1_phyId = $ref_phy_res_id[0]['physician_Reffer_id'];
				}
			}	
			else
			{
				$ref1_phyId = $ref1_phyId_str;
			}
			
			if($caseQryRes[0]['normal'] == 1)
			{
				if($i1referalreq == 'Yes')
				{
					if($reff1_date_str){
						$reff1_date_str = getDateFormatDB($reff1_date_str);
					}
					if($eff1_date_str){
						$eff1_date_str = getDateFormatDB($eff1_date_str);
					}
					if($end1_date_str){
						$end1_date_str = getDateFormatDB($end1_date_str);
					}
					$no_ref_arr = explode('/',$no_ref1_str);
					$request['priNoRef'] = trim($no_ref_arr[0]); 
					$request['priUsedRef'] = trim($no_ref_arr[1]); 
					$no_reff = trim($no_ref_arr[0]) - trim($no_ref_arr[1]);
					$reff_used = $no_ref_arr[1];
					$row_ref = "";
					if($ref_id_pri)
					{	
						$ref_action	= "update";
						$qry = "select *,
										IF(effective_date='0000-00-00','',effective_date) as effective_date,
										IF(end_date='0000-00-00','',end_date) as end_date,
										IF(no_of_reffs=0,'',no_of_reffs) as no_of_reffs,
										IF(reff_date='0000-00-00','',reff_date) as reff_date
										from patient_reff where reff_id = ".$ref_id_pri." ";
		
						$res = imw_query($qry);
						$row_ref = imw_fetch_assoc($res);
						
						if($row_ref['reff_used'] > 0){
							$row_ref['no_of_reffs'] = $row_ref['no_of_reffs']+$row_ref['reff_used'].'/'.$row_ref['reff_used'];
						}
						
						$query = "update patient_reff set patient_id = ".$session_patient.",
												reff_phy_id = '".$ref1_phyId_str."', reff_by = '".addslashes($ref1_phy_str)."',	
												no_of_reffs = '".addslashes($no_reff)."',
												md = '".$mode1."', reffral_no = '".$reffral_no1_str."', reff_date = '".$reff1_date_str."',reff_used = '".$reff_used."',
												effective_date = '".$eff1_date_str."', end_date = '".$end1_date_str."',ins_provider = ".$i1provider.",
												upload_document = '".$fileName1."', insCaseid = '".$ins_caseid."',
												note = '".$note1_str."',ins_data_id = '".$insertId."',reff_type = '1'
												where reff_id = ".$ref_id_pri." ";					
						$qryId = imw_query($query);
						$refId = $ref_id_pri;
						$request['ref1_phyId'] = $ref1_phyId;
						$action = 'update';
					}
					else
					{	
						$ref_action	= "add";		
						$query = "insert into patient_reff set patient_id = ".$session_patient.",
											reff_phy_id = '".$ref1_phyId_str."', reff_by = '".addslashes($ref1_phy_str)."',		
											no_of_reffs = '".$no_reff."',md = '".$mode1."',reff_used = '".$reff_used."',
											reffral_no = '".$reffral_no1_str."',reff_date = '".$reff1_date_str."',
											effective_date = '".$eff1_date_str."', end_date = '".$end1_date_str."',ins_provider = ".$i1provider.",
											upload_document = '".$fileName1."', insCaseid = '".$ins_caseid."',
											note = '".$note1_str."',ins_data_id = '".$insertId."', reff_type = 1";
						$qryId = imw_query($query);			
						$refId = imw_insert_id();
						$request['ref1_phyId'] = $ref1_phyId;	
						$action = 'add';
						//-----UPDATE REFF SCAN/ UPLOAD IN CURRENT SESSION WITH THE REFF ID----------
						imw_query("UPDATE upload_lab_rad_data SET uplaod_primary_id = '".$refId."' 
													WHERE uplaod_primary_id = 0
															AND patient_id = '".$session_patient."'
															AND ins_data_id = '".$insertId."'
															AND ins_type = 1
													");			
					}
					
					
					// ----- REFERREL AUDITING-----------
					
					//audit vars
					$opreaterId = $_SESSION['authId'];	
					$ip = getRealIpAddr();
					$URL = $_SERVER['PHP_SELF'];													 
					//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
					$os = getOS();
					$browserInfoArr = array();
					$browserInfoArr = _browser();
					$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
					$browserName = str_replace(";","",$browserInfo);													 
					$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
					$arrAuditTrailPriRef = array();
					$arrAuditTrailPriRef [] = array(							
									"Ins_Type"=> "primary",			
									"Pk_Id"=> $refId,
									"Table_Name"=>"patient_reff",															
									"Data_Base_Field_Name"=> "reff_phy_id" ,
									"Filed_Label"=> "ref1_phyId",
									"Filed_Text"=> "Patient Primary Referral Ref. Physician",
									"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_phy_id") ,																				
									"Operater_Id"=> $opreaterId,
									"Operater_Type"=> getOperaterType($opreaterId) ,
									"IP"=> $ip,
									"MAC_Address"=> $_REQUEST['macaddrs'],
									"URL"=> $URL,
									"Browser_Type"=> $browserName,
									"OS"=> $os,
									"Machine_Name"=> $machineName,
									"Category"=> "patient_info",
									"Category_Desc"=> "insurence",
									"Depend_Select"=> "select CONCAT_WS(',',FirstName,LastName) as refPhy" ,
									"Depend_Table"=> "refferphysician" ,
									"Depend_Search"=> "physician_Reffer_id" ,
									"Old_Value"=>$row_ref['reff_phy_id'],
									"New_Value"=> $ref1_phyId_str,
									"Action"=>$ref_action,																				
									"pid"=> $_SESSION['patient']
								);
					$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "effective_date" ,
								"Filed_Label"=> "eff1_date",
								"Filed_Text"=> "Patient Primary Referral Start Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"effective_date") ,
								"Old_Value"=> $row_ref['effective_date'],
								"New_Value"=> $eff1_date_str																					
							);
					$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "end_date" ,
								"Filed_Label"=> "end1_date",
								"Filed_Text"=> "Patient Primary Referral End Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"end_date") ,
								"Old_Value"=>$row_ref['end_date'],
								"New_Value"=> $end1_date_str																					
							);
					$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "no_of_reffs" ,
								"Filed_Label"=> "priNoRef",
								"Filed_Text"=> "Patient Primary Referral No. of Visits",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"no_of_reffs") ,
								"Old_Value"=> $row_ref['no_of_reffs'],
								"New_Value"=> trim($no_ref1_str,"/")																				
							);
					$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "reffral_no" ,
								"Filed_Label"=> "reffral_no1",
								"Filed_Text"=> "Patient Primary Referral#",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reffral_no") ,
								"Old_Value"=> $row_ref['reffral_no'],
								"New_Value"=> $reffral_no1_str																					
							);
					$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "reff_date" ,
								"Filed_Label"=> "reff1_date",
								"Filed_Text"=> "Patient Primary Referral Ref. Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_date") ,
								"Old_Value"=> $row_ref['reff_date'],
								"New_Value"=> $reff1_date_str																					
							);
					$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "note" ,
								"Filed_Label"=> "note1",
								"Filed_Text"=> "Patient Primary Referral Notes",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"note") ,
								"Old_Value"=> $row_ref['note'],
								"New_Value"=> $note1_str																						
							);
							//-----------------------------------
							
				}
			}
			
			//--- AUDIT TRAIL CODE ----
			if($this->policy_status == 1)
			{			
				foreach((array)$arrAuditTrailPriRef as $key => $value) {
					if($arrAuditTrailPriRef [$key]["Ins_Type"]=="primary"){
						$arrAuditTrailPriRef [$key]["Action"] = $action;
					}
				}
				
				$table = array("patient_reff");
				$error = array($insError);
				$mergedArray = merging_array($table,$error);
				auditTrail($arrAuditTrailPriRef,$mergedArray,$refId);
			}
		}

		//Start Add Patient Auth
		if($caseQryRes[0]['vision'] == 1 || DEFAULT_PRODUCT == "imwemr" || IDOC_IASC_SAME == 'YES' || $caseQryRes[0]['normal'] == 1)
		{
			if($request['i1authreq'] == 'Yes')
			{
				$arrAuditTrailPriAuth = array();
				for($k=0;$k<=$last_auth_inf_cnt_pri;$k++)
				{
					if($request['auth_nam_pri_'.$k])
					{
						$auth_id = $request['auth_id_pri_'.$k];
						$auth_nam_pri = imw_real_escape_string($request['auth_nam_pri_'.$k]);
						$auth_prov_pri = $request['auth_provider_pri_'.$k];
						$auth_cpt_code_pri = $request['auth_cpt_codes_pri_'.$k];
						$authCptDataPriArr = $this->get_cpt_id($auth_cpt_code_pri);
						$auth_cpt_code_pri = $authCptDataPriArr['cpt_codes'];
						$auth_cpt_id_pri = $authCptDataPriArr['code_id'];
						$auth_dat_pri1 = explode('-',$request['auth_dat_pri_'.$k]);
						$auth_dat_pri = $auth_dat_pri1[2].'-'.$auth_dat_pri1[0].'-'.$auth_dat_pri1[1];
						$auth_comment_pri = imw_real_escape_string($request['auth_comment_pri_'.$k]);
						$ins_case_id = $session_currentCaseid;
						$patient_id = $session_patient;
						$cur_date = date('Y-m-d');
						$auth_oper_pri_nam = $request['auth_user_pri_'.$k];
						$query = "select id from users where username='".$auth_oper_pri_nam."'";
						$sql = imw_query($query);
						$row = imw_fetch_assoc($sql);
						$auth_oper_pri = $row['id'];
						$priAuthAmountArr = preg_split("/\./",$request['priAuthAmount_'.$k]);
						
						$auth_end_date_pri = getDateFormatDB($request['auth_end_dat_pri_'.$k]);
						$auth_visit_value_pri = $request['auth_visit_value_pri_'.$k];
						$arr_auth_visit_value_pri = explode("/",$auth_visit_value_pri);
						$auth_no_of_reffs_pri = $arr_auth_visit_value_pri[0];
						$auth_reff_used_pri = $arr_auth_visit_value_pri[1];
						
						$priAuthAmountArr[0] = preg_replace("/[^0-9]/","",$priAuthAmountArr[0]);
						$priAuthAmount = join('.',$priAuthAmountArr);
						$auth_oper_pri = $request['auth_user_pri_'.$k];
						$row_auth = '';
						if($request['auth_id_pri_'.$k])
						{
							$qry = "select *,
									IF(auth_date='0000-00-00','',auth_date) as auth_date,
									IF( cur_date ='0000-00-00','', cur_date ) as  cur_date,
									IF( AuthAmount ='0.00','', AuthAmount ) as  AuthAmount
									from patient_auth where a_id='".$auth_id."'";
							$res = imw_query($qry);
							$row_auth = imw_fetch_assoc($res);
						
						 $query = "update patient_auth set patient_id='".$session_patient."',auth_name='".$auth_nam_pri."',
													auth_date='".$auth_dat_pri."',
													end_date='".$auth_end_date_pri."',
													no_of_reffs='".$auth_no_of_reffs_pri."',
													reff_used='".$auth_reff_used_pri."',
													auth_comment='".$auth_comment_pri."',auth_operator='".$auth_oper_pri."',
													ins_case_id='".$ins_case_id."',ins_provider='".$i1provider."',ins_data_id='".$insertId."',
													auth_provider='".$auth_prov_pri."', auth_cpt_codes='".$auth_cpt_code_pri."',auth_cpt_codes_id='".$auth_cpt_id_pri."',
													cur_date='".$cur_date."',AuthAmount = '".$priAuthAmount."' where a_id='".$auth_id."' and ins_type='1'";
							imw_query($query);
						}
						else
						{
							$query = "insert into patient_auth set patient_id='".$session_patient."',auth_name='".$auth_nam_pri."',
													auth_date='".$auth_dat_pri."',
													end_date='".$auth_end_date_pri."',
													no_of_reffs='".$auth_no_of_reffs_pri."',
													reff_used='".$auth_reff_used_pri."',
													auth_comment='".$auth_comment_pri."',auth_operator='".$auth_oper_pri."',
													ins_case_id='".$ins_case_id."',ins_provider='".$i1provider."',ins_data_id='".$insertId."',
													auth_provider='".$auth_prov_pri."', auth_cpt_codes='".$auth_cpt_code_pri."',auth_cpt_codes_id='".$auth_cpt_id_pri."',
													AuthAmount = '".$priAuthAmount."', ins_type='1' ";
													imw_query($query);
													$auth_id = imw_insert_id();
						}
						
						// ----- REFERREL AUDITING-----------
						
						//audit vars
						$opreaterId = $_SESSION['authId'];	
						$ip = getRealIpAddr();
						$URL = $_SERVER['PHP_SELF'];													 
						$os = getOS();
						$browserInfoArr = array();
						$browserInfoArr = _browser();
						$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
						$browserName = str_replace(";","",$browserInfo);													 
						$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
						$arrAuditTrailPriAuth [] = array(							
										"Ins_Type"=> "primary",			
										"Pk_Id"=> $auth_id,
										"Table_Name"=>"patient_auth",															
										"Data_Base_Field_Name"=> "a_id" ,
										"Filed_Label"=> 'auth_id_pri_'.$k,
										"Filed_Text"=> "Patient Primary Authorization ID",
										"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"a_id") ,																				
										"Operater_Id"=> $opreaterId,
										"Operater_Type"=> getOperaterType($opreaterId) ,
										"IP"=> $ip,
										"MAC_Address"=> $_REQUEST['macaddrs'],
										"URL"=> $URL,
										"Browser_Type"=> $browserName,
										"OS"=> $os,
										"Machine_Name"=> $machineName,
										"Category"=> "patient_info",
										"Category_Desc"=> "insurence",
										"Depend_Select"=> "select CONCAT_WS(',',FirstName,LastName) as refPhy" ,
										"Depend_Table"=> "refferphysician" ,
										"Depend_Search"=> "physician_Reffer_id" ,
										"Old_Value"=>$row_auth['a_id'],
										"New_Value"=> $auth_id,
										"Action"=>$action,																				
										"pid"=> $_SESSION['patient']
									);
						$arrAuditTrailPriAuth [] = array(							
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "auth_name" ,
									"Filed_Label"=> 'auth_nam_pri_'.$k,
									"Filed_Text"=> "Patient Primary Authorization#",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_name") ,
									"Old_Value"=> $row_auth['auth_name'],
									"New_Value"=> $auth_nam_pri																					
								);
						$arrAuditTrailPriAuth [] = array(							
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "auth_date" ,
									"Filed_Label"=> 'auth_dat_pri_'.$k,
									"Filed_Text"=> "Patient Primary Authorization Date",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_date") ,
									"Old_Value"=> $row_auth['auth_date'],
									"New_Value"=> $auth_dat_pri																					
								);	
						$arrAuditTrailPriAuth [] = array(							
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "auth_comment" ,
									"Filed_Label"=> 'auth_comment_pri_'.$k,
									"Filed_Text"=> "Patient Primary Authorization Comments",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_comment") ,
									"Old_Value"=> $row_auth['auth_comment'],
									"New_Value"=> $auth_comment_pri																					
								);	
						$arrAuditTrailPriAuth [] = array(							
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "AuthAmount" ,
									"Filed_Label"=> 'priAuthAmount_'.$k,
									"Filed_Text"=> "Patient Primary Authorization Amount",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"AuthAmount") ,
									"Old_Value"=> $row_auth['AuthAmount'],
									"New_Value"=> $priAuthAmount																					
								);
						$arrAuditTrailPriAuth [] = array(							
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "auth_provider" ,
									"Filed_Label"=> 'auth_provider_pri_'.$k,
									"Filed_Text"=> "Patient Primary Authorization Provider",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_provider") ,
									"Old_Value"=> $row_auth['auth_provider'],
									"New_Value"=> $auth_prov_pri																					
								);
						
						$arrAuditTrailPriAuth [] = array(							
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "auth_cpt_codes" ,
									"Filed_Label"=> 'auth_cpt_codes_pri_'.$k,
									"Filed_Text"=> "Patient Primary CPT Codes",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_cpt_codes") ,
									"Old_Value"=> $row_auth['auth_cpt_codes'],
									"New_Value"=> $auth_cpt_code_pri																					
								);
						//-----------------------------------
					}
				
				}
				
				if($this->policy_status == 1)
				{			
					// Audit Functionality
					foreach((array)$arrAuditTrailPriAuth as $key => $value) {
						if($arrAuditTrailPriAuth [$key]["Ins_Type"]=="primary"){
							$arrAuditTrailPriAuth [$key]["Action"] = $action;
							//$arrAuditTrailPriRef [$key]["Old_Value"] = "";
						}
					}
					$table = array("patient_auth");
					$error = array($insError);
					$mergedArray = merging_array($table,$error);
					auditTrail($arrAuditTrailPriAuth,$mergedArray,$auth_id);
				}
				
			}
		}

		// HL7 Code Skipped Here
		/*Purpose: Make ADT hl7 messages*/
	if(defined('HL7_ADT_GENERATION') && constant('HL7_ADT_GENERATION') === true && (defined('HL7_ADT_GENERATION_OLD') && constant('HL7_ADT_GENERATION_OLD') === true)){
		
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('boston'))){
			$remote_Facs = check_remote_facility();
			if(is_array($remote_Facs) && $remote_Facs != false){
				require_once( dirname(__FILE__).'/../../hl7sys/old/CLS_makeHL7.php');
				$makeHL7 = new makeHL7();
			}
		}else{
			require_once( dirname(__FILE__).'/../../hl7sys/old/CLS_makeHL7.php');
			$makeHL7 = new makeHL7();
		}
		//logging HL7 messages to send to IDX & Forum.
		if($_REQUEST['isNewPatient'] == 'yes'){
			if($makeHL7){$makeHL7->log_HL7_message($session_patient,'Add_New_Patient');}
		}else{
			if($makeHL7){$makeHL7->log_HL7_message($session_patient,'Update_Patient');}	
		}
	}else if( defined('HL7_ADT_GENERATION') && constant('HL7_ADT_GENERATION') === true ){
			require_once( dirname(__FILE__).'/../../hl7sys/hl7GP/hl7FeedData.php');
			$hl7 = new hl7FeedData();
			$hl7->PD['id'] = $session_patient;
			$hl7->msgtype = "UPDATE_PATIENT";
			if(isset($GLOBALS['HL_RECEIVING']) && is_array($GLOBALS['HL_RECEIVING'])){
				$hl7RecApp = ( isset($GLOBALS['HL_RECEIVING']['APPLICATION']) ) ? $GLOBALS['HL_RECEIVING']['APPLICATION'] : '';
				$hl7RecFac = ( isset($GLOBALS['HL_RECEIVING']['FACILITY']) ) ? $GLOBALS['HL_RECEIVING']['FACILITY'] : '';
				$hl7->setReceivingFacility($hl7RecApp, $hl7RecFac);
			}		
			$hl7->addEVN($hl7->msgtypes['ADT']['trigger_event']);			
			if(isset($GLOBALS['HL7_ADT_SEGMENTS']) && is_array($GLOBALS['HL7_ADT_SEGMENTS'])){
				foreach($GLOBALS['HL7_ADT_SEGMENTS'] as $segment){
					$hl7->insertSegment($segment, 'ADT');
				}
			}			
			$hl7->log_message();
		}
		/*End code*/
		
		//End Add Patient Auth
		
		
		//--- Get Array For Secondary Insurance Company ----------
		$i2providerRCOCodeV = $i2providerRCOIdV = "";
		if(trim($i2provider) == "")
		{
			$insprovider2 = explode("*",$insprovider2);
			if(constant("EXTERNAL_INS_MAPPING") == "YES")
			{
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $insprovider2[0]);
				$i2providerRCOCodeV = trim($arrTempProRCO[0]);
				$i2provider = trim($insprovider2[1]);
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $i2provider);
				$i2provider = trim($arrTempProRCO[0]);
				$i2providerRCOIdV = trim($arrTempProRCO[1]);
			}
			else{
				$i2provider = trim($insprovider2[1]);
			}
		}
		else
		{
			$i2providerRCOCodeV = $_REQUEST["i2providerRCOCode"];
			$i2providerRCOIdV = $_REQUEST["i2providerRCOId"];
		}

		$Data2['actInsCompDate'] = date('Y-m-d');
		$Data2['provider'] = $i2provider;
		$Data2['plan_name'] = $i2plan_name;
		$Data2['policy_number'] = $i2policy_number;
		$Data2['group_number'] = $i2group_number;
		$Data2['subscriber_fname'] = $i2subscriber_fname;
		$Data2['subscriber_mname'] = $i2subscriber_mname;
		$Data2['subscriber_lname'] = $lastName2;
		$Data2['subscriber_suffix'] = $suffix_rel_sec;
		$Data2['subscriber_relationship'] = $i2subscriber_relationship;
		$Data2['subscriber_ss'] = $i2subscriber_ss;
		$Data2['subscriber_DOB'] = getDateFormatDB($i2subscriber_DOB);
		$Data2['referal_required'] = $i2referalreq;
		$Data2['subscriber_street'] = ucfirst($i2subscriber_street);
		$Data2['subscriber_street_2'] = ucfirst($i2subscriber_street_2);
		$Data2['subscriber_postal_code'] = $i2subscriber_postal_code;
		$Data2['zip_ext'] = $i2subscriber_zip_ext;
		$Data2['subscriber_city'] = ucfirst($i2subscriber_city);
		$Data2['subscriber_state'] = ucfirst($i2subscriber_state);
		$Data2['subscriber_country'] = ucfirst($i2subscriber_country);
		$Data2['subscriber_phone'] = core_phone_unformat($i2subscriber_phone);
		$Data2['subscriber_biz_phone'] = core_phone_unformat($i2subscriber_biz_phone);
		$Data2['subscriber_biz_phone_ext'] = $i2subscriber_biz_phone_ext;
		$Data2['subscriber_mobile'] = core_phone_unformat($i2subscriber_mobile);
		$Data2['subscriber_employer'] = $i2subscriber_employer;
		$Data2['subscriber_employer_street'] = $i2subscriber_employer_street;
		$Data2['subscriber_employer_postal_code'] = $i2subscriber_employer_postal_code;
		$Data2['subscriber_employer_state'] = $i2subscriber_employer_state;
		$Data2['subscriber_employer_country'] = $i2subscriber_employer_country;
		$Data2['subscriber_employer_city'] = $i2subscriber_employer_city;
		$Data2['copay'] = $i2copay;
		$Data2['copay_fixed'] = $i2copay_fixed;
		$Data2['copay_type'] = $sec_copay_type;
		if($i2subscriber_sex != '')
			$Data2['subscriber_sex'] = $i2subscriber_sex;
		$Data2['effective_date'] = getDateFormatDB($i2effective_date);
		$Data2['expiration_date'] = getDateFormatDB($i2expiration_date);
		$Data2['ins_caseid'] = $session_currentCaseid;
		$Data2['claims_adjustername'] = $i2claims_adjustername;
		$Data2['claims_adjusterphone'] = $i2claims_adjusterphone;
		$Data2['responsible_party'] = $i2responsible_party;
		$Data2['type'] = 'secondary';
		$Data2['pid'] = $session_patient;
		$Data2['newComDate'] = date('Y-m-d');
		$Data2['auth_required'] =$request['i2authreq'];
		$Data2['comments'] = addslashes($i2comments);
		$Data2['msp_type'] = $msp_type;
		$Data2['rco_code'] = $i2providerRCOCodeV;
		$Data2['rco_code_id'] = $i2providerRCOIdV;
		
		$actDateSec = strtotime(getDateFormatDB($i2effective_date));
		$expDateSec = strtotime(getDateFormatDB($i2expiration_date));
		if($expPreviousSec != '')
		{
			list($month2,$day2,$year2) = explode("-",$expPreviousSec);
			$expPreviousSecDate = $year2.'-'.$month2.'-'.($day2-1);
		}
		$act_date2 = getDateFormatDB($i2effective_date);
		
		$query = "select count(encounter_id) as row_count from patient_charge_list
				where del_status='0' and date_of_service >= '".$act_date2."' and secondaryInsuranceCoId != '".$i2provider."' 
				and secondaryInsuranceCoId > '0' and patient_id = '".$session_patient."'";
		$sql = imw_query($query);
		$row = imw_fetch_assoc($sql);
		if($row['row_count']>0) $get_encounter = true;
		if($i2expiration_date)
		{
			if($expDateSec <= $curDate){
				$Data2['actInsComp'] = 0;
			}
			else{
				$Data2['actInsComp'] = 1;
			}
		}
		else{
			$Data2['actInsComp'] = 1;
		}	
		
		$secDeactive = 1;
		if(!$secondaryId){
			$secondaryId = $secondaryMainId;
		}
		//--- get Scan card document for new Secondary insurance company -----
		if($i2provider)
		{
			if($sec_scan_documents_id)
			{
				$query = "select * from insurance_scan_documents 
						where scan_documents_id = '".$sec_scan_documents_id."'
						and document_status = '0'";
				$sql = imw_query($query);
				$scanCardDetails = imw_fetch_assoc($sql);
				$Data2['scan_card'] = $scanCardDetails['scan_card'];
				$Data2['scan_label'] = $scanCardDetails['scan_label'];
				$Data2['scan_card2'] = $scanCardDetails['scan_card2'];
				$Data2['scan_label2'] = $scanCardDetails['scan_label2'];
				$Data2['cardscan_date'] = $scanCardDetails['cardscan_date'];
				$Data2['cardscan1_datetime'] = $scanCardDetails['cardscan1_date'];
				
				$query = "update insurance_scan_documents set document_status = '1'
										where scan_documents_id = '".$sec_scan_documents_id."'";
				imw_query($query);
			}
		}
		if($i2provider)
		{
			$query = "select provider from insurance_data where ins_caseid = '".$ins_caseid."'
					and pid = ".$session_patient." and type = 'secondary' and provider = '".$i2provider."'
					and actInsComp = 1";
			if($secondaryMainId){
				$query .= " and id != '".$secondaryMainId."'";
			}	
			$sql = imw_query($query);
			$row = imw_fetch_assoc($sql);
			$insProvider2 = $row['provider'];
			if($insProvider2)
			{
				$this->js_alert_msg .= 'Secondary Provider Already Exists To This Case Id.<br>';
			}
			else
			{
				if($secondaryId)
				{
					$query = "select expiration_date from insurance_data where id = '".$secondaryId."'";
					$sql = imw_query($query);
					$row = imw_fetch_assoc($sql);
					$expiration_date = $row['expiration_date'];
					if($expiration_date)
					{
						$expPreviousSecDate = $expiration_date;
					}
					$query = "update insurance_data set actInsComp = '$secDeactive',
											expiration_date = '$expPreviousSecDate' where id = '$secondaryId'";
					$sql = imw_query($query);
					if($sql){
						$secondary_saving  = true;
					}
				}
				
				if($newInsComp2)
				{
					$action = 'add';
					$Data2['source'] = core_refine_user_input($_SERVER['HTTP_REFERER']);
					$insertId2 = AddRecords($Data2,'insurance_data');
					
					imw_query("UPDATE upload_lab_rad_data SET ins_data_id = '".$insertId2."' 
												WHERE uplaod_primary_id = 0
														AND patient_id = '".$_SESSION['patient']."'
														AND ins_type = 2
												");	
					if($insertId2 != "")
					{
						$secondary_saving  = true;
						// BEGIN ATTACH SCANNED DOCUMENTS WITH INSURANCE_DATA IF DONE IN ONE STEP 
						if(!$sec_scan_documents_id)
						{
								$query = "select scan_documents_id,scan_card,scan_label,scan_card2,scan_label2,cardscan_date,cardscan1_date
																from insurance_scan_documents 
																where type = 'secondary' and ins_caseid = '".$session_currentCaseid."'
																and patient_id = '".$session_patient."' and document_status = '0'";
								$sql = imw_query($query);
								$scanSecCardDetails = imw_fetch_assoc($sql);
								$arrSecScanData = array();
								$arrSecScanData['scan_card'] = $scanSecCardDetails['scan_card'];
								$arrSecScanData['scan_label'] = $scanSecCardDetails['scan_label'];
								$arrSecScanData['scan_card2'] = $scanSecCardDetails['scan_card2'];
								$arrSecScanData['scan_label2'] = $scanSecCardDetails['scan_label2'];
								$arrSecScanData['cardscan_date'] = $scanSecCardDetails['cardscan_date'];
								$arrSecScanData['cardscan1_datetime'] = $scanSecCardDetails['cardscan1_date'];
								UpdateRecords($insertId2,'id',$arrSecScanData,'insurance_data','',false);
								unset($arrSecScanData);
								$query = "update insurance_scan_documents set document_status = '1'
													where scan_documents_id = '".$scanSecCardDetails['scan_documents_id']."'";
								imw_query($query);
							}
						// END ATTACH SCANNED DOCUMENTS WITH INSURANCE_DATA IF DONE IN ONE STEP 
		
					}
					
					list($month, $day, $year) = explode('-',$i2subscriber_DOB);
					$i2subscriber_DOB = $year."-".$month."-".$day;
					if($i2subscriber_DOB !="--"){
						$request['i2subscriber_DOB'] = $i2subscriber_DOB;
					}
					else{
						$request['i2subscriber_DOB'] = "";
					}	
					if($hid_create_acc_sec_ins_sub == "yes")
					{			
						$arrRemotePtData2 = array();
						$arrRemotePtData2['ptfname'] = $i2subscriber_fname;
						$arrRemotePtData2['ptlname'] = $lastName2;
						$arrRemotePtData2['ptdob'] = $Data2['subscriber_DOB'];
						if($i2subscriber_sex != '')
						$arrRemotePtData2['ptgender'] = $i2subscriber_sex;
						$arrRemotePtData2['ptzip'] = $i2subscriber_postal_code;
						$arrRemotePtData2['ptzip_ext'] = $i2subscriber_zip_ext;
						$arr_sec_patient_next_id = get_Next_PatientID($arrRemotePtData2);
						if($arr_sec_patient_next_id['error']==''){
							$sec_patient_next_id = $arr_sec_patient_next_id['patient_id'];
							$sec_patient_src_server = $arr_sec_patient_next_id['src_server'];
						}else{
							echo $arr_sec_patient_next_id['error'];exit;
						}
						$arrSecNewSepAcc = array();
						$arrSecNewSepAcc["fname"] = $i2subscriber_fname;
						$arrSecNewSepAcc["mname"] = $i2subscriber_mname;
						$arrSecNewSepAcc["lname"] = $lastName2;
						$arrSecNewSepAcc["street"] = $hid_pat_steet;
						$arrSecNewSepAcc["street2"] = $hid_pat_steet_2;
						$arrSecNewSepAcc["city"] = $hid_pat_city;
						$arrSecNewSepAcc["state"] = $hid_pat_state;
						$arrSecNewSepAcc["postal_code"] = $hid_pat_zip_code;
						$arrSecNewSepAcc["country_code"] = "USA";
						$arrSecNewSepAcc["ss"] = $i2subscriber_ss;
						$arrSecNewSepAcc["DOB"] = $i2subscriber_DOB;
						$arrSecNewSepAcc["patientStatus"] = "Active";
						if($i2subscriber_sex != '')
						$arrSecNewSepAcc["sex"] = $i2subscriber_sex;	 
						$arrSecNewSepAcc["pid"] = $sec_patient_next_id;	
						$arrSecNewSepAcc["id"] = $sec_patient_next_id;
						$arrSecNewSepAcc["date"] = date('Y-m-d H:i:s');
						$arrSecNewSepAcc["created_by"] = $_SESSION['authId'];
						if($sec_patient_src_server>0){$arrSecNewSepAcc["src_server"] = $sec_patient_src_server;}			 		
						//--- ADD NEW PATIENT (ability to create an account whenever a New Insurance Subscriber is added)----
						$sub_sec_pat_id_new = AddRecords($arrSecNewSepAcc,"patient_data");
						
						$arr_sub_pat_data=array();
						$arr_sub_ins_data["sub_pat_id"]=$sub_sec_pat_id_new;
						UpdateRecords($insertId2,'id',$arr_sub_ins_data,'insurance_data','',false);
					}
					else
					{
						if($sub_sec_pat_id)
						{
							$arr_sub_pat_data=array();
							$arr_sub_ins_data["sub_pat_id"]=$sub_sec_pat_id;
							UpdateRecords($insertId2,'id',$arr_sub_ins_data,'insurance_data','',false);
							$arr_sub_pat_data=array();
							$arr_sub_pat_data['street'] = ucfirst($i2subscriber_street);
							$arr_sub_pat_data['street2'] = ucfirst($i2subscriber_street_2);
							$arr_sub_pat_data['postal_code'] = $i2subscriber_postal_code;
							$arr_sub_pat_data['zip_ext'] = $request['i2subscriber_zip_ext'];
							$arr_sub_pat_data['city'] = ucfirst($i2subscriber_city);
							$arr_sub_pat_data['state'] = ucfirst($i2subscriber_state);
							$arr_sub_pat_data['phone_home'] = core_phone_unformat($i2subscriber_phone);
							$arr_sub_pat_data['phone_biz'] = core_phone_unformat($i2subscriber_biz_phone);
							$arr_sub_pat_data['phone_biz_ext'] = $i2subscriber_biz_phone_ext;
							$arr_sub_pat_data['phone_cell'] = core_phone_unformat($i2subscriber_mobile);
							UpdateRecords($sub_sec_pat_id,'id',$arr_sub_pat_data,'patient_data');
						}
					}
				}
				else
				{
					$action = 'update';		
					$insertId2 = UpdateRecords($secondaryMainId,'id',$Data2,'insurance_data','',false);
					list($month, $day, $year) = explode('-',$i2subscriber_DOB);
					$i2subscriber_DOB = $year."-".$month."-".$day;
					if($i2subscriber_DOB !="--"){
						$request['i2subscriber_DOB'] = $i2subscriber_DOB;
					}
					else{
						$request['i2subscriber_DOB'] = "";
					}				
					if($hid_create_acc_sec_ins_sub == "yes")
					{			
						$arrRemotePtData2 = array();
						$arrRemotePtData2['ptfname'] = $i2subscriber_fname;
						$arrRemotePtData2['ptlname'] = $lastName2;
						$arrRemotePtData2['ptdob'] = $Data2['subscriber_DOB'];
						if($i2subscriber_sex != '')
						$arrRemotePtData2['ptgender'] = $i2subscriber_sex;
						$arrRemotePtData2['ptzip'] = $i2subscriber_postal_code;
						$arrRemotePtData2['ptzip_ext'] = $i2subscriber_zip_ext;
						
						$arr_sec_patient_next_id = get_Next_PatientID($arrRemotePtData2);
						if($arr_sec_patient_next_id['error']==''){
							$sec_patient_next_id = $arr_sec_patient_next_id['patient_id'];
							$sec_patient_src_server = $arr_sec_patient_next_id['src_server'];
						}else{
							echo $arr_sec_patient_next_id['error'];exit;
						}
						$arrSecNewSepAcc = array();
						$arrSecNewSepAcc["fname"] = $i2subscriber_fname;
						$arrSecNewSepAcc["mname"] = $i2subscriber_mname;
						$arrSecNewSepAcc["lname"] = $lastName2;
						$arrSecNewSepAcc["street"] = $hid_pat_steet;
						$arrSecNewSepAcc["street2"] = $hid_pat_steet_2;
						$arrSecNewSepAcc["city"] = $hid_pat_city;
						$arrSecNewSepAcc["state"] = $hid_pat_state;
						$arrSecNewSepAcc["postal_code"] = $hid_pat_zip_code;
						$arrSecNewSepAcc["country_code"] = "USA";
						$arrSecNewSepAcc["ss"] = $i2subscriber_ss;
						$arrSecNewSepAcc["DOB"] = $i2subscriber_DOB;
						$arrSecNewSepAcc["patientStatus"] = "Active";
						if($i2subscriber_sex != '')
							$arrSecNewSepAcc["sex"] = $i2subscriber_sex;	 
						$arrSecNewSepAcc["pid"] = $sec_patient_next_id;	
						$arrSecNewSepAcc["id"] = $sec_patient_next_id;
						$arrSecNewSepAcc["date"] = date('Y-m-d H:i:s');
						$arrSecNewSepAcc["created_by"] = $_SESSION['authId'];
						if($sec_patient_src_server>0){$arrSecNewSepAcc["src_server"] = $sec_patient_src_server;}
						//--- ADD NEW PATIENT (ability to create an account whenever a New Insurance Subscriber is added)----
						$sub_sec_pat_id_new = AddRecords($arrSecNewSepAcc,"patient_data");
						
						$arr_sub_pat_data=array();
						$arr_sub_ins_data["sub_pat_id"]=$sub_sec_pat_id_new;
						UpdateRecords($secondaryMainId,'id',$arr_sub_ins_data,'insurance_data','',false);
					}
					else
					{
						if($sub_sec_pat_id)
						{
							
							$arr_sub_pat_data=array();
							$arr_sub_ins_data["sub_pat_id"]=$sub_sec_pat_id;
							UpdateRecords($secondaryMainId,'id',$arr_sub_ins_data,'insurance_data','',false);
							
							$arr_sub_pat_data=array();
							$arr_sub_pat_data['street'] = ucfirst($i2subscriber_street);
							$arr_sub_pat_data['street2'] = ucfirst($i2subscriber_street_2);
							$arr_sub_pat_data['postal_code'] = $i2subscriber_postal_code;
							$arr_sub_pat_data['zip_ext'] = $i2subscriber_zip_ext;
							$arr_sub_pat_data['city'] = ucfirst($i2subscriber_city);
							$arr_sub_pat_data['state'] = ucfirst($i2subscriber_state);
							$arr_sub_pat_data['phone_home'] = core_phone_unformat($i2subscriber_phone);
							$arr_sub_pat_data['phone_biz'] = core_phone_unformat($i2subscriber_biz_phone);
							$arr_sub_pat_data['phone_biz_ext'] = $i2subscriber_biz_phone_ext;
							$arr_sub_pat_data['phone_cell'] = core_phone_unformat($i2subscriber_mobile);
		
							UpdateRecords($sub_sec_pat_id,'id',$arr_sub_pat_data,'patient_data');
						}
					}		
				}
				
				//--- AUDIT TRAIL CODE ----
				if($this->policy_status == 1)
				{	
					// Audit Trail Functionality
					foreach((array)$arrAuditTrailSec as $key => $value) {
						if($arrAuditTrailSec [$key]["Ins_Type"]=="secondary"){
							$arrAuditTrailSec [$key]["Action"] = $action;
							//$arrAuditTrailSec [$key]["Old_Value"] = '';
						}
					}
					auditTrail($arrAuditTrailSec,$mergedArray,$insertId2);
				}
			}	
		}
		//------ Insert Data For Refferal Required ---------------

		$sec_ref_phy_id_arr = $request['ref2_phyId'];
		if(!is_array($sec_ref_phy_id_arr))
		{
			$sec_ref_phy_id_arr = array($sec_ref_phy_id_arr);	
		}
		foreach($sec_ref_phy_id_arr as $key => $ref2_phyId_str)
		{
			if(trim($ref2_phyId_str) == "") 
			{
				continue;	
			}
			$ref_id_sec = $request['ref_id_sec'][$key];
		
			$ref2_phy_str = $request['ref2_phy'][$key];
			$reffral_no2_str = $request['reffral_no2'][$key];	
			$reff2_date_str = $request['reff2_date'][$key];	
			$end2_date_str = $request['end2_date'][$key];	
			$eff2_date_str = $request['eff2_date'][$key];
			$no_ref2_str = $request['no_ref2'][$key];
			$note2_str = $request['note2'][$key];
			
			if(trim($ref2_phyId_str) == "")
			{
				if($ref2_phy_str)
				{
					$Reffer_physician_arr = preg_split('/,/',$ref2_phy_str);
					$phyLnameArr = explode(' ',trim($Reffer_physician_arr[0]));
					$phylname = trim($phyLnameArr[0]);
					$phyFnameArr = explode(' ',trim($Reffer_physician_arr[1]));
					$phyfname = trim($phyFnameArr[0]);				
					$ref_phy_res_id = get_reffer_physician_id('FirstName',$phyfname,'LastName',$phylname);
					$ref2_phyId = $ref_phy_res_id[0]['physician_Reffer_id'];
				}
			}	
			else{
				$ref2_phyId = $ref2_phyId_str;
			}
		
			if($caseQryRes[0]['normal'] == 1)
			{
				if($i2referalreq == 'Yes')
				{
					if($reff2_date_str){
						$reff2_date_str = getDateFormatDB($reff2_date_str);
					}
					if($eff2_date_str){
						$eff2_date_str = getDateFormatDB($eff2_date_str);
					}
					if($end2_date_str){
						$end2_date_str = getDateFormatDB($end2_date_str);
					}
					
					$no_ref_arr = explode('/',$no_ref2_str);
					$request['secNoRef'] = trim($no_ref_arr[0]); 
					$request['secUsedRef'] = trim($no_ref_arr[1]); 
					$no_reff = trim($no_ref_arr[0]) - trim($no_ref_arr[1]);
					$reff_used = $no_ref_arr[1];
					$row_ref = '';
					if($ref_id_sec)
					{
						$qry = "select *,
									IF(effective_date='0000-00-00','',effective_date) as effective_date,
									IF(end_date='0000-00-00','',end_date) as end_date,
									IF(no_of_reffs=0,'',no_of_reffs) as no_of_reffs,
									IF(reff_date='0000-00-00','',reff_date) as reff_date
									from patient_reff where reff_id = ".$ref_id_sec." ";
						$res = imw_query($qry);
						$row_ref = imw_fetch_assoc($res);
		
						if($row_ref['reff_used']>0){
							$row_ref['no_of_reffs']=$row_ref['no_of_reffs']+$row_ref['reff_used'].'/'.$row_ref['reff_used'];
						}				
									
						$query = "update patient_reff set patient_id = ".$session_patient.",
											reff_phy_id = '".$ref2_phyId_str."', reff_by = '".addslashes($ref2_phy_str)."',	no_of_reffs = '".$no_reff."',
											md = '".$mode2."', reffral_no = '".$reffral_no2_str."', reff_date = '".$reff2_date_str."',reff_used = '".$reff_used."',
											effective_date = '".$eff2_date_str."', end_date = '".$end2_date_str."',ins_provider = ".$i2provider.",
											upload_document = '".$fileName2."', insCaseid = '".$ins_caseid."',
											note = '".$note2_str."',ins_data_id = '".$insertId2."',reff_type = '2'
											where reff_id = ".$ref_id_sec." ";					
						$qryId = imw_query($query);
						$refId = $ref_id_sec;
						$request['ref2_phyId'] = $ref2_phyId;
						$action = 'update';		
					}
					else
					{				
						$query = "insert into patient_reff set patient_id = ".$session_patient.",
											reff_phy_id = '".$ref2_phyId_str."', reff_by = '".addslashes($ref2_phy_str)."',	no_of_reffs = '".$no_reff."',
											md = '".$mode2."', reffral_no = '".$reffral_no2_str."', reff_date = '".$reff2_date_str."',reff_used = '".$reff_used."',
											effective_date = '".$eff2_date_str."', end_date = '".$end2_date_str."',ins_provider = ".$i2provider.",
											upload_document = '".$fileName2."', insCaseid = '".$ins_caseid."',
											note = '".$note2_str."',ins_data_id = '".$insertId2."',reff_type = '2' ";					
						$qryId = imw_query($query);			
						$refId = imw_insert_id();
						$request['ref2_phyId'] = $ref2_phyId;	
						$action = 'add';
						imw_query("UPDATE upload_lab_rad_data SET uplaod_primary_id = '".$refId."' 
									WHERE uplaod_primary_id = 0
											AND patient_id = '".$this->patient_id."'
											AND ins_data_id = '".$insertId2."'
											AND ins_type = 2
									");		
					}
					// ----- REFERREL AUDITING-----------
					
					//audit vars
					$opreaterId = $_SESSION['authId'];	
					$ip = getRealIpAddr();
					$URL = $_SERVER['PHP_SELF'];													 
					$os = getOS();
					$browserInfoArr = array();
					$browserInfoArr = _browser();
					$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
					$browserName = str_replace(";","",$browserInfo);													 
					$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
					$arrAuditTrailSecRef = array();
					$arrAuditTrailSecRef [] = array(							
									"Ins_Type"=> "secondary",			
									"Pk_Id"=> $refId,
									"Table_Name"=>"patient_reff",															
									"Data_Base_Field_Name"=> "reff_phy_id" ,
									"Filed_Label"=> "ref2_phyId",
									"Filed_Text"=> "Patient Secondary Referral Ref. Physician",
									"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_phy_id") ,																				
									"Operater_Id"=> $opreaterId,
									"Operater_Type"=> getOperaterType($opreaterId) ,
									"IP"=> $ip,
									"MAC_Address"=> $_REQUEST['macaddrs'],
									"URL"=> $URL,
									"Browser_Type"=> $browserName,
									"OS"=> $os,
									"Machine_Name"=> $machineName,
									"Category"=> "patient_info",
									"Category_Desc"=> "insurence",
									"Depend_Select"=> "select CONCAT_WS(',',FirstName,LastName) as refPhy" ,
									"Depend_Table"=> "refferphysician" ,
									"Depend_Search"=> "physician_Reffer_id" ,
									"Old_Value"=>$row_ref['reff_phy_id'],
									"New_Value"=> $ref1_phyId_str,
									"Action"=>$ref_action,																				
									"pid"=> $_SESSION['patient']
								);
					$arrAuditTrailSecRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "effective_date" ,
								"Filed_Label"=> "eff2_date",
								"Filed_Text"=> "Patient Secondary Referral Start Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"effective_date") ,
								"Old_Value"=> $row_ref['effective_date'],
								"New_Value"=> $eff1_date_str																					
							);
					$arrAuditTrailSecRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "end_date" ,
								"Filed_Label"=> "end2_date",
								"Filed_Text"=> "Patient Secondary Referral End Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"end_date") ,
								"Old_Value"=>$row_ref['end_date'],
								"New_Value"=> $end1_date_str																					
							);
					$arrAuditTrailSecRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "no_of_reffs" ,
								"Filed_Label"=> "secNoRef",
								"Filed_Text"=> "Patient Secondary Referral No. of Visits",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"no_of_reffs") ,
								"Old_Value"=> $row_ref['no_of_reffs'],
								"New_Value"=> trim($no_ref1_str,"/")																				
							);
					$arrAuditTrailSecRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "reffral_no" ,
								"Filed_Label"=> "reffral_no2",
								"Filed_Text"=> "Patient Secondary Referral#",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reffral_no") ,
								"Old_Value"=> $row_ref['reffral_no'],
								"New_Value"=> $reffral_no1_str																					
							);
					$arrAuditTrailSecRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "reff_date" ,
								"Filed_Label"=> "reff2_date",
								"Filed_Text"=> "Patient Secondary Referral Ref. Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_date") ,
								"Old_Value"=> $row_ref['reff_date'],
								"New_Value"=> $reff1_date_str																					
							);
					$arrAuditTrailSecRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "note" ,
								"Filed_Label"=> "note2",
								"Filed_Text"=> "Patient Secondary Referral Notes",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"note") ,
								"Old_Value"=> $row_ref['note'],
								"New_Value"=> $note1_str																						
							);
					//-----------------------------------
				}
			}			
			//--- AUDIT TRIAL CODE ---
			if($this->policy_status == 1)
			{	
				foreach((array)$arrAuditTrailSecRef as $key => $value) {
					if($arrAuditTrailSecRef [$key]["Ins_Type"]=="secondary"){
						$arrAuditTrailSecRef [$key]["Action"] = $action;
						//$arrAuditTrailSecRef [$key]["Old_Value"] = '';					
					}
				}			
				$table = array("patient_reff");
				$error = array($insError);
				$mergedArray = merging_array($table,$error);
				auditTrail($arrAuditTrailSecRef,$mergedArray,$refId,0,0);
			}
		}
		
		//Start Add Patient Auth
		if($caseQryRes[0]['vision'] == 1 || DEFAULT_PRODUCT == "imwemr" || IDOC_IASC_SAME == 'YES' || $caseQryRes[0]['normal'] == 1)
		{
			if($i2authreq == 'Yes')
			{
				for($k=0;$k<=$last_auth_inf_cnt_sec;$k++)
				{
					if($request['auth_nam_sec_'.$k])
					{
						$auth_id_sec = $request['auth_id_sec_'.$k];
						$auth_nam_sec = imw_real_escape_string($request['auth_nam_sec_'.$k]);
						$auth_prov_sec = $request['auth_provider_sec_'.$k];
						$auth_cpt_code_sec = $request['auth_cpt_codes_sec_'.$k];
						$authCptDataSecArr = $this->get_cpt_id($auth_cpt_code_sec);
						$auth_cpt_code_sec = $authCptDataSecArr['cpt_codes'];
						$auth_cpt_id_sec = $authCptDataSecArr['code_id'];
						
						$auth_cpt_id_sec = $request['auth_cpt_id_sec_'.$k];
						$auth_dat_sec1 = explode('-',$request['auth_dat_sec_'.$k]);
						$auth_dat_sec = $auth_dat_sec1[2].'-'.$auth_dat_sec1[0].'-'.$auth_dat_sec1[1];
						$auth_comment_sec = imw_real_escape_string($request['auth_comment_sec_'.$k]);
						$auth_oper_sec_nam = $request['auth_user_sec_'.$k];
						
						$query = "select id from users where username = '".$auth_oper_sec_nam."'";
						$sql = imw_query($query);
						$row = imw_fetch_assoc($sql);
						$auth_oper_sec = $row['id'];
						$ins_case_id2 = $session_currentCaseid;
						$patient_id = $session_patient;
						
						$auth_end_date_sec = getDateFormatDB($_REQUEST['auth_end_dat_sec_'.$k]);
						$auth_visit_value_sec = $request['auth_visit_value_sec_'.$k];
						$arr_auth_visit_value_sec = explode("/",$auth_visit_value_sec);
						$auth_no_of_reffs_sec = $arr_auth_visit_value_sec[0];
						$auth_reff_used_sec = $arr_auth_visit_value_sec[1];
						
						$secAuthAmountArr = preg_split("/\./",$_REQUEST['secAuthAmount_'.$k]);
						$secAuthAmountArr[0] = preg_replace("/[^0-9]/","",$secAuthAmountArr[0]);
						$secAuthAmount = join('.',$secAuthAmountArr);
						$cur_date = date('Y-m-d');
						$auth_oper_sec = $request['auth_user_sec_'.$k];
						
						if($request['auth_id_sec_'.$k])
						{
							$query = "update patient_auth set patient_id='$patient_id',auth_name='$auth_nam_sec',
													auth_date='$auth_dat_sec',
													end_date='$auth_end_date_sec',
													no_of_reffs='$auth_no_of_reffs_sec',
													reff_used='$auth_reff_used_sec',
													auth_comment='$auth_comment_sec',auth_operator='$auth_oper_sec',
													ins_case_id='$ins_case_id2',ins_provider='$i2provider',ins_data_id='$insertId2',
													auth_provider='".$auth_prov_sec."', auth_cpt_codes='".$auth_cpt_code_sec."',auth_cpt_codes_id='".$auth_cpt_id_sec."',
													AuthAmount = '$secAuthAmount'
													where a_id='$auth_id_sec' and ins_type='2'";
						}
						else
						{
							$query = "insert into patient_auth set patient_id='$patient_id',auth_name='$auth_nam_sec',
													auth_date='$auth_dat_sec',
													end_date='$auth_end_date_sec',
													no_of_reffs='$auth_no_of_reffs_sec',
													reff_used='$auth_reff_used_sec',
													auth_comment='$auth_comment_sec',auth_operator='$auth_oper_sec',
													ins_case_id='$ins_case_id2',ins_provider='$i2provider',ins_data_id='$insertId2',ins_type='2',
													auth_provider='".$auth_prov_sec."', auth_cpt_codes='".$auth_cpt_code_sec."',auth_cpt_codes_id='".$auth_cpt_id_sec."',
													cur_date='$cur_date',AuthAmount = '$secAuthAmount'";
						}
						imw_query($query);
					}
				}
			
			}
		}
		//End Add Patient Auth
		
		
		//--- Get Array For Tertiary Insurance Company ----------
		$i3providerRCOCodeV = $i3providerRCOIdV = "";
		if(trim($i3provider) == "")
		{
			$insprovider3 = explode("*",$insprovider3);
			if(constant("EXTERNAL_INS_MAPPING") == "YES")
			{
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $insprovider3[0]);
				$i3providerRCOCodeV = trim($arrTempProRCO[0]);
				$i3provider = trim($insprovider3[1]);
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $i3provider);
				$i3provider = trim($arrTempProRCO[0]);
				$i3providerRCOIdV = trim($arrTempProRCO[1]);		
			}
			else
			{
				$i3provider = trim($insprovider3[1]);
			}
		}
		else
		{
			$i3providerRCOCodeV = $request["i3providerRCOCode"];
			$i3providerRCOIdV = $request["i3providerRCOId"];
		}

		$Data3['actInsCompDate'] = date('Y-m-d');
		$Data3['provider'] = $i3provider;
		$Data3['plan_name'] = $i3plan_name;
		$Data3['policy_number'] = $i3policy_number;
		$Data3['group_number'] = $i3group_number;
		$Data3['subscriber_fname'] = $i3subscriber_fname;
		$Data3['subscriber_mname'] = $i3subscriber_mname;
		$Data3['subscriber_lname'] = $lastName3;
		$Data3['subscriber_suffix'] = $suffix_rel_ter;
		$Data3['subscriber_relationship'] = $i3subscriber_relationship;
		$Data3['subscriber_ss'] = $i3subscriber_ss;
		$Data3['subscriber_DOB'] = getDateFormatDB($i3subscriber_DOB);
		$Data3['referal_required'] = $i3referalreq;
		$Data3['subscriber_street'] = ucfirst($i3subscriber_street);
		$Data3['subscriber_street_2'] = ucfirst($i3subscriber_street_2);
		$Data3['subscriber_postal_code'] = $i3subscriber_postal_code;
		$Data3['zip_ext'] = $i3subscriber_zip_ext;
		$Data3['subscriber_city'] = ucfirst($i3subscriber_city);
		$Data3['subscriber_state'] = ucfirst($i3subscriber_state);
		$Data3['subscriber_country'] = ucfirst($i3subscriber_country);
		$Data3['subscriber_phone'] = core_phone_unformat($i3subscriber_phone);
		$Data3['subscriber_biz_phone'] = core_phone_unformat($i3subscriber_biz_phone);
		$Data3['subscriber_biz_phone_ext'] = $i3subscriber_biz_phone_ext;
		$Data3['subscriber_mobile'] = core_phone_unformat($i3subscriber_mobile);
		$Data3['subscriber_employer'] = $i3subscriber_employer;
		$Data3['subscriber_employer_street'] = $i3subscriber_employer_street;
		$Data3['subscriber_employer_postal_code'] = $i3subscriber_employer_postal_code;
		$Data3['subscriber_employer_state'] = $i3subscriber_employer_state;
		$Data3['subscriber_employer_country'] = $i3subscriber_employer_country;
		$Data3['subscriber_employer_city'] = $i3subscriber_employer_city;
		$Data3['copay'] = $i3copay;
		$Data3['copay_fixed'] = $i3copay_fixed;
		$Data3['copay_type'] = $tri_copay_type;
		if($i3subscriber_sex != '')
			$Data3['subscriber_sex'] = $i3subscriber_sex;
		$Data3['effective_date'] = getDateFormatDB($i3effective_date);
		$Data3['expiration_date'] = getDateFormatDB($i3expiration_date);
		$Data3['ins_caseid'] = $session_currentCaseid;
		$Data3['claims_adjustername'] = $i3claims_adjustername;
		$Data3['claims_adjusterphone'] = $i3claims_adjusterphone;
		$Data3['responsible_party'] = $i3responsible_party;
		$Data3['type'] = 'tertiary';
		$Data3['pid'] = $session_patient;
		$Data3['newComDate'] = date('Y-m-d');
		$Data3['auth_required'] = $_REQUEST['i3authreq'];
		$Data3['comments'] = addslashes($i3comments);
		$Data3['rco_code'] = $i3providerRCOCodeV;
		$Data3['rco_code_id'] = $i3providerRCOIdV;

		//--- get Scan card document for new tertiary insurance company -----
		if($i3provider)
		{
			if($ter_scan_documents_id)
			{
				
					$query = "select * from insurance_scan_documents 
											where scan_documents_id = '".$ter_scan_documents_id."' and document_status = '0'";
					$sql = imw_query($query);
					$scanCardDetails = imw_fetch_assoc($sql);
					$Data3['scan_card'] = $scanCardDetails['scan_card'];
					$Data3['scan_label'] = $scanCardDetails['scan_label'];
					$Data3['scan_card2'] = $scanCardDetails['scan_card2'];
					$Data3['scan_label2'] = $scanCardDetails['scan_label2'];
					$Data3['cardscan_date'] = $scanCardDetails['cardscan_date'];
					$Data3['cardscan1_datetime'] = $scanCardDetails['cardscan1_date'];
					
					$query = "update insurance_scan_documents set document_status = '1'
												where scan_documents_id = '".$ter_scan_documents_id."'";
					imw_query($query);
			}
		}

		$actDateTer = strtotime(getDateFormatDB($i3effective_date));
		$expDateTer = strtotime(getDateFormatDB($i3expiration_date));
		
		if($expPreviousTer != ''){
			list($month3,$day3,$year3) = explode("-",$expPreviousTer);
			$expPreviousTerDate = $year3.'-'.$month3.'-'.($day3-1);
		}
		
		if($i3expiration_date)
		{
			if($expDateTer <= $curDate){
				$Data3['actInsComp'] = 0;
			}
			else{
				$Data3['actInsComp'] = 1;
			}
		}
		else
		{
			$Data3['actInsComp'] = 1;
		}	
		
		$terDeactive = 1;
		
		if(!$tertiaryId){
			$tertiaryId = $tertiaryMainId;
		}
		
		if($i3provider)
		{	
			$query = "select provider from insurance_data where ins_caseid = '".$ins_caseid."'
					and pid = ".$session_patient." and type = 'tertiary' and provider = '".$i3provider."'
					and actInsComp = 1";
					
			if($tertiaryId){
				$query .= " and id != '".$tertiaryId."'";
			}	
			$sql = imw_query($query);
			$row = imw_fetch_assoc($sql);
			$insProvider3 = $row['provider'];
	
			if($insProvider3)
			{
				$this->js_alert_msg .= 'Tertiary Provider Already Exists To This Case Id.<br>';
			}
			else
			{
				if($tertiaryId)
				{
					$query = "select expiration_date from insurance_data where id = '".$tertiaryId."'";
					$sql = imw_query($query);
					$row = imw_fetch_assoc($query);
					$expiration_date = $row['expiration_date'];
					
					if($expiration_date){
						$expPreviousTerDate = $expiration_date;
					}
			
					$query = "update insurance_data set actInsComp = '".$terDeactive."',
											expiration_date = '".$expPreviousTerDate."' where id = '".$tertiaryId."' ";
					$qry = imw_query($query);
					if($qry != ""){
						$tertiary_saving  = true;
					}
				}		
				
				if($newInsComp3)
				{
					$action = 'add';
					$Data3['source'] = core_refine_user_input($_SERVER['HTTP_REFERER']);
					$insertId3 = AddRecords($Data3,'insurance_data');
					imw_query("UPDATE upload_lab_rad_data SET ins_data_id = '".$insertId3."' 
												WHERE uplaod_primary_id = 0
														AND patient_id = '".$_SESSION['patient']."'
														AND ins_type = 3
												");	
					if($insertId3 != "")
					{
						$tertiary_saving  = true;
						// BEGIN ATTACH SCANNED DOCUMENTS WITH INSURANCE_DATA IF DONE IN ONE STEP
						if(!$ter_scan_documents_id || $ter_scan_documents_id == "")
						{
							$query = "select scan_documents_id,scan_card,scan_label,scan_card2,scan_label2,cardscan_date,cardscan1_date
															from insurance_scan_documents 
															where type = 'tertiary' and ins_caseid = '".$session_currentCaseid."'
															and patient_id = '".$session_patient."' and document_status = '0'";
							$sql = imw_query($query);															
							$scanTerCardDetails = imw_fetch_assoc($sql);
							$arrTerScanData = array();
							$arrTerScanData['scan_card'] = $scanTerCardDetails['scan_card'];
							$arrTerScanData['scan_label'] = $scanTerCardDetails['scan_label'];
							$arrTerScanData['scan_card2'] = $scanTerCardDetails['scan_card2'];
							$arrTerScanData['scan_label2'] = $scanTerCardDetails['scan_label2'];
							$arrTerScanData['cardscan_date'] = $scanTerCardDetails['cardscan_date'];
							$arrTerScanData['cardscan1_datetime'] = $scanTerCardDetails['cardscan1_date'];
							UpdateRecords($insertId3,'id',$arrTerScanData,'insurance_data','',false);
							unset($arrTerScanData);
							$query = "update insurance_scan_documents set document_status = '1'
													where scan_documents_id =  '".$scanTerCardDetails['scan_documents_id']."'";
							imw_query($query);
						}
						// END ATTACH SCANNED DOCUMENTS WITH INSURANCE_DATA IF DONE IN ONE STEP 
					}
					
					list($month, $day, $year) = explode('-',$i3subscriber_DOB);
					$i3subscriber_DOB = $year."-".$month."-".$day;
					if($i3subscriber_DOB !="--")
					{
						$request['i3subscriber_DOB'] = $i3subscriber_DOB;
					}
					else{
						$request['i3subscriber_DOB'] = "";
					}
					
					if($hid_create_acc_ter_ins_sub == "yes")
					{
						$arrRemotePtData3 = array();
						$arrRemotePtData3['ptfname'] = $i3subscriber_fname;
						$arrRemotePtData3['ptlname'] = $lastName3;
						$arrRemotePtData3['ptdob'] = $Data3['subscriber_DOB'];
						if($i3subscriber_sex != '')
							$arrRemotePtData3['ptgender'] = $i3subscriber_sex;
						$arrRemotePtData3['ptzip'] = $i3subscriber_postal_code;
						$arrRemotePtData3['ptzip_ext'] = $i3subscriber_zip_ext;
				
						$arr_ter_patient_next_id = get_Next_PatientID($arrRemotePtData3);
						if($arr_ter_patient_next_id['error']=='')
						{
							$ter_patient_next_id = $arr_ter_patient_next_id['patient_id'];
							$ter_patient_src_server = $arr_ter_patient_next_id['patient_id'];
						}else{
							echo $arr_ter_patient_next_id['error'];exit;
						}
				
						$arrTerNewSepAcc = array();
						$arrTerNewSepAcc["fname"] = $i3subscriber_fname;
						$arrTerNewSepAcc["mname"] = $i3subscriber_mname;
						$arrTerNewSepAcc["lname"] = $lastName3;
						$arrTerNewSepAcc["street"] = $hid_pat_steet;
						$arrTerNewSepAcc["street2"] = $hid_pat_steet_2;
						$arrTerNewSepAcc["city"] = $hid_pat_city;
						$arrTerNewSepAcc["state"] = $hid_pat_state;
						$arrTerNewSepAcc["postal_code"] = $hid_pat_zip_code;
						$arrTerNewSepAcc["country_code"] = "USA";
						$arrTerNewSepAcc["ss"] = $i3subscriber_ss;
						$arrTerNewSepAcc["DOB"] = $i3subscriber_DOB;
						$arrTerNewSepAcc["patientStatus"] = "Active";
						if($i3subscriber_sex != '')
							$arrTerNewSepAcc["sex"] = $i3subscriber_sex;	 
						$arrTerNewSepAcc["pid"] = $ter_patient_next_id;	
						$arrTerNewSepAcc["id"] = $ter_patient_next_id;
						$arrTerNewSepAcc["date"] = date('Y-m-d H:i:s');
						$arrTerNewSepAcc["created_by"] = $_SESSION['authId'];
						if($ter_patient_src_server>0){ $arrTerNewSepAcc["src_server"] = $ter_patient_src_server;}		 		
						//--- ADD NEW PATIENT (ability to create an account whenever a New Insurance Subscriber is added)----
						
						$sub_ter_pat_id_new = AddRecords($arrTerNewSepAcc,"patient_data");
				
						$arr_sub_pat_data=array();
						$arr_sub_ins_data["sub_pat_id"]=$sub_ter_pat_id_new;
						
						UpdateRecords($insertId3,'id',$arr_sub_ins_data,'insurance_data','',false);
				}
					else
					{
						if($sub_ter_pat_id)
						{
							$arr_sub_pat_data=array();
							$arr_sub_ins_data["sub_pat_id"]=$sub_ter_pat_id;
							UpdateRecords($insertId3,'id',$arr_sub_ins_data,'insurance_data','',false);
							$arr_sub_pat_data=array();
							$arr_sub_pat_data['street'] = ucfirst($i3subscriber_street);
							$arr_sub_pat_data['street2'] = ucfirst($i3subscriber_street_2);
							$arr_sub_pat_data['postal_code'] = $i3subscriber_postal_code;
							$arr_sub_pat_data['zip_ext'] = $i3subscriber_zip_ext;
							$arr_sub_pat_data['city'] = ucfirst($i3subscriber_city);
							$arr_sub_pat_data['state'] = ucfirst($i3subscriber_state);
							$arr_sub_pat_data['phone_home'] = core_phone_unformat($i3subscriber_phone);
							$arr_sub_pat_data['phone_biz'] = core_phone_unformat($i3subscriber_biz_phone);
							$arr_sub_pat_data['phone_biz_ext'] = $i3subscriber_biz_phone_ext;
							$arr_sub_pat_data['phone_cell'] = core_phone_unformat($i3subscriber_mobile);
							UpdateRecords($sub_ter_pat_id,'id',$arr_sub_pat_data,'patient_data');
						}
					}				
				}
				else
				{
					$action = 'update';		
					$insertId3 = UpdateRecords($tertiaryId,'id',$Data3,'insurance_data','',false);
					list($month, $day, $year) = explode('-',$i3subscriber_DOB);
					$i3subscriber_DOB = $year."-".$month."-".$day;
					if($i3subscriber_DOB !="--")
					{
						$request['i3subscriber_DOB'] = $i3subscriber_DOB;
					}
					else
					{
						$request['i3subscriber_DOB'] = "";
					}	
					if($hid_create_acc_ter_ins_sub == "yes")
					{
						$arrRemotePtData3 = array();
						$arrRemotePtData3['ptfname'] = $i3subscriber_fname;
						$arrRemotePtData3['ptlname'] = $lastName3;
						$arrRemotePtData3['ptdob'] = $Data3['subscriber_DOB'];
						if($i3subscriber_sex != '')
						$arrRemotePtData3['ptgender'] = $i3subscriber_sex;
						$arrRemotePtData3['ptzip'] = $i3subscriber_postal_code;
						$arrRemotePtData3['ptzip_ext'] = $i3subscriber_zip_ext;
				
						$arr_ter_patient_next_id = get_Next_PatientID($arrRemotePtData3);
						if($arr_ter_patient_next_id['error']==''){
							$ter_patient_next_id = $arr_ter_patient_next_id['patient_id'];
							$ter_patient_src_server = $arr_ter_patient_next_id['src_server'];
						}else{
							echo $arr_ter_patient_next_id['error'];exit;
						}
				
						$arrTerNewSepAcc = array();
						$arrTerNewSepAcc["fname"] = $i3subscriber_fname;
						$arrTerNewSepAcc["mname"] = $i3subscriber_mname;
						$arrTerNewSepAcc["lname"] = $lastName3;
						$arrTerNewSepAcc["street"] = $hid_pat_steet;
						$arrTerNewSepAcc["street2"] = $hid_pat_steet_2;
						$arrTerNewSepAcc["city"] = $hid_pat_city;
						$arrTerNewSepAcc["state"] = $hid_pat_state;
						$arrTerNewSepAcc["postal_code"] = $hid_pat_zip_code;
						$arrTerNewSepAcc["country_code"] = "USA";
						$arrTerNewSepAcc["ss"] = $i3subscriber_ss;
						$arrTerNewSepAcc["DOB"] = $i3subscriber_DOB;
						$arrTerNewSepAcc["patientStatus"] = "Active";
						if($i3subscriber_sex != '')
						$arrTerNewSepAcc["sex"] = $i3subscriber_sex;	 
						$arrTerNewSepAcc["pid"] = $ter_patient_next_id;	
						$arrTerNewSepAcc["id"] = $ter_patient_next_id;
						$arrTerNewSepAcc["date"] = date('Y-m-d H:i:s');
						$arrTerNewSepAcc["created_by"] = $_SESSION['authId'];
						if($ter_patient_src_server>0){$arrTerNewSepAcc["src_server"] = $ter_patient_src_server;}				 		
						//--- ADD NEW PATIENT (ability to create an account whenever a New Insurance Subscriber is added)----
						$sub_ter_pat_id_new = AddRecords($arrTerNewSepAcc,"patient_data");
				
						$arr_sub_pat_data=array();
						$arr_sub_ins_data["sub_pat_id"]=$sub_ter_pat_id_new;
						UpdateRecords($tertiaryId,'id',$arr_sub_ins_data,'insurance_data','',false);
					}
					else
					{
						if($sub_ter_pat_id)
						{
							$arr_sub_pat_data=array();
							$arr_sub_ins_data["sub_pat_id"]=$sub_ter_pat_id;
							UpdateRecords($tertiaryId,'id',$arr_sub_ins_data,'insurance_data','',false);
							
							$arr_sub_pat_data=array();
							$arr_sub_pat_data['street'] = ucfirst($i3subscriber_street);
							$arr_sub_pat_data['street2'] = ucfirst($i3subscriber_street_2);
							$arr_sub_pat_data['postal_code'] = $i3subscriber_postal_code;
							$arr_sub_pat_data['zip_ext'] = $i3subscriber_zip_ext;
							$arr_sub_pat_data['city'] = ucfirst($i3subscriber_city);
							$arr_sub_pat_data['state'] = ucfirst($i3subscriber_state);
							$arr_sub_pat_data['phone_home'] = core_phone_unformat($i3subscriber_phone);
							$arr_sub_pat_data['phone_biz'] = core_phone_unformat($i3subscriber_biz_phone);
							$arr_sub_pat_data['phone_biz_ext'] = $i3subscriber_biz_phone_ext;
							$arr_sub_pat_data['phone_cell'] = core_phone_unformat($i3subscriber_mobile);
					
							UpdateRecords($sub_ter_pat_id,'id',$arr_sub_pat_data,'patient_data');
						}
					}
					
				}
				
				
				if($this->policy_status == 1)
				{
					// Audit Functionality Here
					foreach((array)$arrAuditTrailTer as $key => $value) {
						if($arrAuditTrailTer [$key]["Ins_Type"]=="tertiary"){
							$arrAuditTrailTer [$key]["Action"] = $action;
							//$arrAuditTrailTer [$key]["Old_Value"] = "";
						}
					}
					auditTrail($arrAuditTrailTer,$mergedArray,$insertId3);
				}
			}		
		}

		//------ Insert Data For Refferal Required ---------------
		$ter_ref_phy_id_arr = $ref3_phyId;
		
		if(!is_array($ter_ref_phy_id_arr))
		{
			$ter_ref_phy_id_arr = array($ter_ref_phy_id_arr);	
		}
		
		foreach($ter_ref_phy_id_arr as $key => $ref3_phyId_str)
		{
			if(trim($ref3_phyId_str) == "")
			{
				continue;	
			}
			$ref_id_ter= $request['ref_id_ter'][$key];
		
			$ref3_phy_str = $request['ref3_phy'][$key];
			$reffral_no3_str = $request['reffral_no3'][$key];	
			$reff3_date_str = $request['reff3_date'][$key];	
			$end3_date_str = $request['end3_date'][$key];	
			$eff3_date_str = $request['eff3_date'][$key];
			$no_ref3_str = $request['no_ref3'][$key];
			$note3_str = $request['note3'][$key];	
			
			if(trim($ref3_phyId_str) == "")
			{	
				if($ref3_phy_str)
				{
					$Reffer_physician_arr = preg_split('/,/',$ref3_phy_str);
					$phyLnameArr = explode(' ',trim($Reffer_physician_arr[0]));
					$phylname = trim($phyLnameArr[0]);
					$phyFnameArr = explode(' ',trim($Reffer_physician_arr[1]));
					$phyfname = trim($phyFnameArr[0]);
					$ref_phy_res_id = get_reffer_physician_id('FirstName',$phyfname,'LastName',$phylname);
					$ref3_phyId = $ref_phy_res_id[0]['physician_Reffer_id'];
				}
			}
			else
			{
				$ref3_phyId = $ref3_phyId_str;
			}
		
			if($caseQryRes[0]['normal'] == 1)
			{
				if($i3referalreq == 'Yes')
				{
					if($reff3_date_str){
						$reff3_date_str = getDateFormatDB($reff3_date_str);
					}
					if($eff3_date_str){
						$eff3_date_str = getDateFormatDB($eff3_date_str);
					}
					if($end3_date_str){
						$end3_date_str = getDateFormatDB($end3_date_str);
					}
					
					$no_ref_arr = explode('/',$no_ref3_str);
					$request['terNoRef'] = trim($no_ref_arr[0]); 
					$request['terUsedRef'] = trim($no_ref_arr[1]); 
					$no_reff = trim($no_ref_arr[0]) - trim($no_ref_arr[1]);
					$reff_used = $no_ref_arr[1];
					
					$row_ref = '';
					if($ref_id_ter)
					{	
						$qry = "select *,
									IF(effective_date='0000-00-00','',effective_date) as effective_date,
									IF(end_date='0000-00-00','',end_date) as end_date,
									IF(no_of_reffs=0,'',no_of_reffs) as no_of_reffs,
									IF(reff_date='0000-00-00','',reff_date) as reff_date
									from patient_reff where reff_id = ".$ref_id_ter." ";
						$res = imw_query($qry);
						$row_ref = imw_fetch_assoc($res);	
						
						if($row_ref['reff_used']>0)
						{
							$row_ref['no_of_reffs']=$row_ref['no_of_reffs']+$row_ref['reff_used'].'/'.$row_ref['reff_used'];
						}				
							
						$query = "update patient_reff set patient_id = ".$session_patient.",
											reff_phy_id = '".$ref3_phyId_str."', reff_by = '".addslashes($ref3_phy_str)."',	no_of_reffs = '".$no_reff."',
											md = '".$mode3."', reffral_no = '".$reffral_no3_str."', reff_date = '".$reff3_date_str."',reff_used = '".$reff_used."',
											effective_date = '".$eff3_date_str."', end_date = '".$end3_date_str."',ins_provider = ".$i3provider.",
											upload_document = '".$fileName3."', insCaseid = '".$ins_caseid."',
											note = '".$note3_str."',ins_data_id = '".$insertId3."',reff_type = '3'
											where reff_id = ".$ref_id_ter." ";
						$sql = imw_query($query);
						$refId = $ref_id_ter;
						$request['ref3_phyId'] = $ref3_phyId;
						$action = 'update';		
					}
					else
					{				
						$query = "insert into patient_reff set patient_id = ".$session_patient.",
											reff_phy_id = '".$ref3_phyId_str."', reff_by = '".addslashes($ref3_phy_str)."',	no_of_reffs = '".$no_reff."',
											md = '".$mode3."', reffral_no = '".$reffral_no3_str."', reff_date = '".$reff3_date_str."',reff_used = '".$reff_used."',
											effective_date = '".$eff3_date_str."', end_date = '".$end3_date_str."',ins_provider = ".$i3provider.",
											upload_document = '".$fileName3."', insCaseid = '".$ins_caseid."',
											note = '".$note3_str."',ins_data_id = '".$insertId3."',reff_type = '3'";					
						$sql = imw_query($query);		
						$refId = imw_insert_id();
						$request['ref3_phyId'] = $ref3_phyId;	
						$action = 'add';
						imw_query("UPDATE upload_lab_rad_data SET uplaod_primary_id = '".$refId."' 
												WHERE uplaod_primary_id = 0
														AND patient_id = '".$_SESSION['patient']."'
														AND ins_data_id = '".$insertId3."'
														AND ins_type = 3
												");	
					}
					
					// ----- REFERREL AUDITING-----------
					//audit vars
					$opreaterId = $_SESSION['authId'];	
					$ip = getRealIpAddr();
					$URL = $_SERVER['PHP_SELF'];													 
					$os = getOS();
					$browserInfoArr = array();
					$browserInfoArr = _browser();
					$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
					$browserName = str_replace(";","",$browserInfo);													 
					$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
					$arrAuditTrailTerRef = array();
					$arrAuditTrailTerRef [] = array(							
									"Ins_Type"=> "tertiary",			
									"Pk_Id"=> $refId,
									"Table_Name"=>"patient_reff",															
									"Data_Base_Field_Name"=> "reff_phy_id" ,
									"Filed_Label"=> "ref3_phyId",
									"Filed_Text"=> "Patient Tertiary Referral Ref. Physician",
									"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_phy_id") ,																				
									"Operater_Id"=> $opreaterId,
									"Operater_Type"=> getOperaterType($opreaterId) ,
									"IP"=> $ip,
									"MAC_Address"=> $_REQUEST['macaddrs'],
									"URL"=> $URL,
									"Browser_Type"=> $browserName,
									"OS"=> $os,
									"Machine_Name"=> $machineName,
									"Category"=> "patient_info",
									"Category_Desc"=> "insurence",
									"Depend_Select"=> "select CONCAT_WS(',',FirstName,LastName) as refPhy" ,
									"Depend_Table"=> "refferphysician" ,
									"Depend_Search"=> "physician_Reffer_id" ,
									"Old_Value"=>$row_ref['reff_phy_id'],
									"New_Value"=> $ref1_phyId_str,
									"Action"=>$action,																				
									"pid"=> $_SESSION['patient']
								);
					$arrAuditTrailTerRef [] = array(							
								"Ins_Type"=> "tertiary",																
								"Data_Base_Field_Name"=> "effective_date" ,
								"Filed_Label"=> "eff3_date",
								"Filed_Text"=> "Patient Tertiary Referral Start Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"effective_date") ,
								"Old_Value"=> $row_ref['effective_date'],
								"New_Value"=> $eff1_date_str																					
							);
					$arrAuditTrailTerRef [] = array(							
								"Ins_Type"=> "tertiary",																
								"Data_Base_Field_Name"=> "end_date" ,
								"Filed_Label"=> "end3_date",
								"Filed_Text"=> "Patient Tertiary Referral End Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"end_date") ,
								"Old_Value"=>$row_ref['end_date'],
								"New_Value"=> $end1_date_str																					
							);
					$arrAuditTrailTerRef [] = array(							
								"Ins_Type"=> "tertiary",																
								"Data_Base_Field_Name"=> "no_of_reffs" ,
								"Filed_Label"=> "terNoRef",
								"Filed_Text"=> "Patient Tertiary Referral No. of Visits",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"no_of_reffs") ,
								"Old_Value"=> $row_ref['no_of_reffs'],
								"New_Value"=> trim($no_ref1_str,"/")																				
							);
					$arrAuditTrailTerRef [] = array(							
								"Ins_Type"=> "tertiary",																
								"Data_Base_Field_Name"=> "reffral_no" ,
								"Filed_Label"=> "reffral_no3",
								"Filed_Text"=> "Patient Tertiary Referral#",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reffral_no") ,
								"Old_Value"=> $row_ref['reffral_no'],
								"New_Value"=> $reffral_no1_str																					
							);
					$arrAuditTrailTerRef [] = array(							
								"Ins_Type"=> "tertiary",																
								"Data_Base_Field_Name"=> "reff_date" ,
								"Filed_Label"=> "reff3_date",
								"Filed_Text"=> "Patient Tertiary Referral Ref. Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_date") ,
								"Old_Value"=> $row_ref['reff_date'],
								"New_Value"=> $reff1_date_str																					
							);
					$arrAuditTrailTerRef [] = array(							
								"Ins_Type"=> "tertiary",																
								"Data_Base_Field_Name"=> "note" ,
								"Filed_Label"=> "note3",
								"Filed_Text"=> "Patient Tertiary Referral Notes",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"note") ,
								"Old_Value"=> $row_ref['note'],
								"New_Value"=> $note1_str																						
							);
					//-----------------------------------	
				}
			}			
			
			//--- AUDIT TRIAL CODE ---
			if($this->policy_status == 1)
			{	
				// Audit Fucntionality Here
				foreach((array)$arrAuditTrailTerRef as $key => $value) {
					if($arrAuditTrailTerRef [$key]["Ins_Type"]=="tertiary"){
						$arrAuditTrailTerRef [$key]["Action"] = $action;
						//$arrAuditTrailTerRef [$key]["Old_Value"] = "";
					}
				}				
				$table = array("patient_reff");
				$error = array($insError);
				$mergedArray = merging_array($table,$error);
				auditTrail($arrAuditTrailTerRef,$mergedArray,$refId);
			}
		}

		//Start Add Patient Auth
		if($caseQryRes[0]['vision'] == 1 || DEFAULT_PRODUCT == "imwemr" || IDOC_IASC_SAME == 'YES' || $caseQryRes[0]['normal'] == 1)
		{
			if($i3authreq == 'Yes')
			{
				$arrAuditTrailTerAuth = array();
				for($k=0;$k<=$last_auth_inf_cnt_ter;$k++)
				{
					if($request['auth_nam_ter_'.$k])
					{
						$auth_id_ter=$request['auth_id_ter_'.$k];
						$auth_nam_ter=imw_real_escape_string($request['auth_nam_ter_'.$k]);
						$auth_prov_ter = $request['auth_provider_ter_'.$k];
						$auth_cpt_code_ter = $request['auth_cpt_codes_ter_'.$k];
						$authCptDataTerArr = $this->get_cpt_id($auth_cpt_code_ter);
						$auth_cpt_code_ter = $authCptDataTerArr['cpt_codes'];
						$auth_cpt_id_ter = $authCptDataTerArr['code_id'];
						$auth_dat_ter1=explode('-',$request['auth_dat_ter_'.$k]);
						$auth_dat_ter=$auth_dat_ter1[2].'-'.$auth_dat_ter1[0].'-'.$auth_dat_ter1[1];
						$auth_comment_ter=imw_real_escape_string($request['auth_comment_ter_'.$k]);
						$auth_oper_ter=$_SESSION['authId'];
						$ins_case_id3=$session_currentCaseid;
						$patient_id=$session_patient;
						$cur_date=date('Y-m-d');			
						$auth_oper_ter_nam=$request['auth_user_ter_'.$k];
				
						$query = "select id from users where username = '".$auth_oper_ter_nam."'";
						$sql = imw_query($query);
						$row = imw_fetch_assoc($sql);
						$auth_oper_ter = $row['id'];
						$terAuthAmountArr = preg_split("/\./",$request['terAuthAmount_'.$k]);
						$terAuthAmountArr[0] = preg_replace("/[^0-9]/","",$terAuthAmountArr[0]);
						$terAuthAmount = join('.',$terAuthAmountArr);
				
						$auth_end_date_ter = getDateFormatDB($request['auth_end_dat_ter_'.$k]);
						$auth_visit_value_ter = $request['auth_visit_value_ter_'.$k];
						$arr_auth_visit_value_ter = explode("/",$auth_visit_value_ter);
						$auth_no_of_reffs_ter = $arr_auth_visit_value_ter[0];
						$auth_reff_used_ter = $arr_auth_visit_value_ter[1];
				
						$auth_oper_ter = $request['auth_user_ter_'.$k];
						if($request['auth_id_ter_'.$k])
						{
							$qry = "select *,
												IF(auth_date='0000-00-00','',auth_date) as auth_date,
												IF( cur_date ='0000-00-00','', cur_date ) as  cur_date,
												IF( AuthAmount ='0.00','', AuthAmount ) as  AuthAmount 
												from patient_auth where a_id='".$auth_id_ter."'";
							$sql = imw_query($qry);
							$row_auth = imw_fetch_assoc($sql);
					
							$query = "update patient_auth set patient_id='".$patient_id."',auth_name='".$auth_nam_ter."',
													auth_date='".$auth_dat_ter."',
													end_date='".$auth_end_date_ter."',
													no_of_reffs='".$auth_no_of_reffs_ter."',
													reff_used='".$auth_reff_used_ter."',
													auth_comment='".$auth_comment_ter."',auth_operator='".$auth_oper_ter."',
													ins_case_id='".$ins_case_id3."',ins_provider='".$i3provider."',ins_data_id='".$insertId3."',
													auth_provider='".$auth_prov_ter."', auth_cpt_codes='".$auth_cpt_code_ter."',auth_cpt_codes_id='".$auth_cpt_id_ter."',
													AuthAmount = '".$terAuthAmount."'
													where a_id='".$auth_id_ter."' and ins_type='3'";
							imw_query($query);
						}
						else
						{
							$query = "insert into patient_auth set patient_id='".$patient_id."',auth_name='".$auth_nam_ter."',
													auth_date='".$auth_dat_ter."',
													end_date='".$auth_end_date_ter."',
													no_of_reffs='".$auth_no_of_reffs_ter."',
													reff_used='".$auth_reff_used_ter."',
													auth_comment='".$auth_comment_ter."',auth_operator='".$auth_oper_ter."',
													ins_case_id='".$ins_case_id3."',ins_provider='".$i3provider."',ins_data_id='".$insertId3."',
													auth_provider='".$auth_prov_ter."', auth_cpt_codes='".$auth_cpt_code_ter."',auth_cpt_codes_id='".$auth_cpt_id_ter."',
													cur_date='".$cur_date."',AuthAmount = '".$terAuthAmount."', ins_type='3' ";
							imw_query($query);
							$auth_id_ter = imw_insert_id();
						}
						
						// ----- REFERREL AUDITING-----------
						//audit vars
						$opreaterId = $_SESSION['authId'];	
						$ip = getRealIpAddr();
						$URL = $_SERVER['PHP_SELF'];													 
						$os = getOS();
						$browserInfoArr = array();
						$browserInfoArr = _browser();
						$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
						$browserName = str_replace(";","",$browserInfo);													 
						$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);

						$arrAuditTrailTerAuth [] = array(							
										"Ins_Type"=> "tertiary",			
										"Pk_Id"=> $auth_id_tri,
										"Table_Name"=>"patient_auth",															
										"Data_Base_Field_Name"=> "a_id" ,
										"Filed_Label"=> 'auth_id_tri_'.$k,
										"Filed_Text"=> "Patient Tertiary Authorization ID",
										"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"a_id") ,																				
										"Operater_Id"=> $opreaterId,
										"Operater_Type"=> getOperaterType($opreaterId) ,
										"IP"=> $ip,
										"MAC_Address"=> $_REQUEST['macaddrs'],
										"URL"=> $URL,
										"Browser_Type"=> $browserName,
										"OS"=> $os,
										"Machine_Name"=> $machineName,
										"Category"=> "patient_info",
										"Category_Desc"=> "insurence",
										"Depend_Select"=> "select CONCAT_WS(',',FirstName,LastName) as refPhy" ,
										"Depend_Table"=> "refferphysician" ,
										"Depend_Search"=> "physician_Reffer_id" ,
										"Old_Value"=>$row_auth['a_id'],
										"New_Value"=> $auth_id_tri,
										"Action"=>$action,																				
										"pid"=> $_SESSION['patient']
									);
						$arrAuditTrailTerAuth [] = array(							
									"Ins_Type"=> "tertiary",																
									"Data_Base_Field_Name"=> "auth_name" ,
									"Filed_Label"=> 'auth_nam_tri_'.$k,
									"Filed_Text"=> "Patient Tertiary Authorization#",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_name") ,
									"Old_Value"=> $row_auth['auth_name'],
									"New_Value"=> $auth_nam_tri																					
								);
						$arrAuditTrailTerAuth [] = array(							
									"Ins_Type"=> "tertiary",																
									"Data_Base_Field_Name"=> "auth_date" ,
									"Filed_Label"=> 'auth_dat_tri_'.$k,
									"Filed_Text"=> "Patient Tertiary Authorization Date",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_date") ,
									"Old_Value"=> $row_auth['auth_date'],
									"New_Value"=> $auth_dat_tri																				
								);	
						$arrAuditTrailTerAuth [] = array(							
									"Ins_Type"=> "tertiary",																
									"Data_Base_Field_Name"=> "auth_comment" ,
									"Filed_Label"=> 'auth_comment_tri_'.$k,
									"Filed_Text"=> "Patient Tertiary Authorization Comments",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_comment") ,
									"Old_Value"=> $row_auth['auth_comment'],
									"New_Value"=> $auth_comment_tri																					
								);	
						$arrAuditTrailTerAuth [] = array(							
									"Ins_Type"=> "tertiary",																
									"Data_Base_Field_Name"=> "AuthAmount" ,
									"Filed_Label"=> 'terAuthAmount_'.$k,
									"Filed_Text"=> "Patient Tertiary Authorization Amount",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"AuthAmount") ,
									"Old_Value"=> $row_auth['AuthAmount'],
									"New_Value"=> $terAuthAmount																					
								);
						$arrAuditTrailTerAuth [] = array(							
									"Ins_Type"=> "tertiary",																
									"Data_Base_Field_Name"=> "auth_provider" ,
									"Filed_Label"=> 'auth_provider_ter_'.$k,
									"Filed_Text"=> "Patient Tertiary Authorization Provider",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_provider") ,
									"Old_Value"=> $row_auth['auth_provider'],
									"New_Value"=> $auth_prov_ter																					
								);
						
						$arrAuditTrailTerAuth [] = array(							
									"Ins_Type"=> "tertiary",																
									"Data_Base_Field_Name"=> "auth_cpt_codes" ,
									"Filed_Label"=> 'auth_cpt_codes_ter_'.$k,
									"Filed_Text"=> "Patient Tertiary CPT Codes",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_cpt_codes") ,
									"Old_Value"=> $row_auth['auth_cpt_codes'],
									"New_Value"=> $auth_cpt_code_ter																					
								);
						//-----------------------------------
					}
				}
				if($this->policy_status == 1)
				{
					foreach((array)$arrAuditTrailTerAuth as $key => $value) {
						if($arrAuditTrailTerAuth [$key]["Ins_Type"]=="tertiary"){
							$arrAuditTrailTerAuth [$key]["Action"] = $action;
						}
					}
					$table = array("patient_auth");
					$error = array($insError);
					$mergedArray = merging_array($table,$error);
					auditTrail($arrAuditTrailTerAuth,$mergedArray,$auth_id_tri);
				}
			}
		}
		
		//End Add Patient Auth
		
		$inactivePriInsComp = $insertId;
		$inactiveSecInsVal = $insertId2;
		$inactiveTerInsVal = $insertId3;
		
		$scriptSaveMsg = '';
		$scriptSaveMsg = 'parent.document.getElementById("load_image21").style.display = "none";
				  top.chkConfirmSave("yes","set");';
		if($request['hidInsChangeOption'] == "0" && ($primary_saving == true || $secondary_saving == true || $tertiary_saving == true))
		{
			$scriptSaveMsg .='top.alert_notification_show("Record Saved Successfully","top.fmain.display_demo_alert();");';
			/*********NEW HL7 ENGINE START************/
			require_once(dirname(__FILE__)."/../../hl7sys/api/class.HL7Engine.php");
			$objHL7Engine = new HL7Engine();
			$objHL7Engine->application_module = 'demographics';
			$objHL7Engine->msgSubType = 'update_patient';
			$objHL7Engine->source_id = $session_patient;
			$objHL7Engine->generateHL7();
			/*********NEW HL7 ENGINE END*************/
		
		}
		
		// Remote Server Sync Functionality Skipped Here
				
		if($patientDetail->DOB == '0000-00-00'){
			$patientInfoDOBMsg = $patientName.' DOB information required.';
		}
		if($patientDetail->sex == ''){
			$patientInfoSexMsg .= $patientName.' Gender information required.';
		}
		if($patientDetail->street == ''){
			$patientInfoAddMsg .= $patientName.' Address required.';
		}
	
	}
	
	public function get_cpt_id($cptString) {
		$cptString = trim($cptString);
		$cptArr = explode(";",$cptString);
		
		$return = array();
		if( is_array($cptArr) && count($cptArr) > 0 ) {
			$cptString = "'".implode("','",$cptArr)."'";
			$qry = "Select cpt_fee_id, cpt_prac_code From cpt_fee_tbl Where cpt_cat_id > 0 And delete_status = 0 And cpt_prac_code IN (".$cptString.") AND cpt_prac_code <> '' ";
			$sql = imw_query($qry);
			$cnt = imw_num_rows($sql);
			$idArr = $txtArr = array();
			while($row = imw_fetch_assoc($sql) ){
				$idArr[] = $row['cpt_fee_id'];
				$txtArr[] = $row['cpt_prac_code'];
			}
			$return = array('code_id'=>implode(",",$idArr),'cpt_codes'=>implode(";",$txtArr));
		}
		
		return $return;
		
		
		
	}
}


?>