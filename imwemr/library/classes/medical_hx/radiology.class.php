<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> Radiology Class
 Access Type: Indirect Access.
 
*/
include_once $GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php';
$cls_review_med_hx = new CLSReviewMedHx;

class Radiology extends MedicalHistory
{
	//Public variables
	public $rad_type_arr = array();
	public $status_arr = array();
	public $review_rad_arr = array();
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->radio_vocabulary = $this->get_vocabulary("medical_hx", "radiology");
		
		//Radiology Type Array
		$this->rad_type_arr['395124008'] = 'virology';
		$this->rad_type_arr['363680008'] = 'X-ray';
		$this->rad_type_arr['46680005'] = 'Vital Sign';
		$this->rad_type_arr['16310003'] = 'Ultrasound';
		$this->rad_type_arr['69200006'] = 'Toxicology';
		$this->rad_type_arr['68793005'] = 'Serology';
		$this->rad_type_arr['71388002'] = 'Procedure';
		$this->rad_type_arr['108257001'] = 'Pathology';
		$this->rad_type_arr['371572003'] = 'Nuclear Medicine';
		$this->rad_type_arr['19851009'] = 'Microbiology';
		$this->rad_type_arr['113091000'] = 'MRI';
		$this->rad_type_arr['363679005'] = 'Imaging';
		$this->rad_type_arr['252275004'] = 'Hematology';
		$this->rad_type_arr['275711006'] = 'Chemistry';
		$this->rad_type_arr['77477000'] = 'CT';
		$this->rad_type_arr['77343006'] = 'Angiography';
		$this->rad_type_arr['Other'] = 'Other';
		
		//Radiology review array
		$this->review_rad_arr = array('Pk_Id','Table_Name', 'UI_Filed_Name','Data_Base_Field_Name','Data_Base_Field_Type','Field_Text','Operater_Id','Action','Old_Value','New_Value');
		
		//Status Array
		$this->status_arr[1] = "Pending";
		$this->status_arr[2] = "Completed";
	}
	
	//Patient Audit Trail
	function patient_audit_trail(){
		//Audit function removed
		return '';
	}
	
	// GET ALL RADIOLOGY TEST FOR PATIENT
	public function load_radiology_data($filter){	
		$radQryRes = array();
		
		$qry = "select * , date_format(rad_order_date,'".get_sql_date_format()."') as ordered_date,
				date_format(rad_results_date,'".get_sql_date_format()."') as radResultsDate, rad_order_time, rad_results_time
				from rad_test_data where rad_status != '3' and rad_patient_id = '".$this->patient_id."'";
		if(trim($filter) != ''){
			$qry .= " and rad_status = '$filter'";
		}
		$sql_qry = imw_query($qry);
		if(imw_num_rows($sql_qry) > 0){
			while($row = imw_fetch_array($sql_qry)){
				$radQryRes[] = $row;
			}	
		}
		
		return $radQryRes; 
	}
	
	// Set records as deleted
	public function set_test_as_deleted($del_id){
		global $cls_review_med_hx;
		
		$patient_id = $this->patient_id;
		
		$counter = 0;
		//--- REVIEWED CODE ---
		$qry = imw_query("select rad_name from rad_test_data where rad_test_data_id = '$del_id'");
		while($sql_qry = imw_fetch_array($qry)){
			$radQryRes[] = $sql_qry;
		};
		$reviewedDataArr = array();
		$reviewedDataArr[0]["Pk_Id"] = $del_id;
		$reviewedDataArr[0]["Table_Name"] = 'rad_test_data';
		$reviewedDataArr[0]["Field_Text"] = 'Patient Radiology Test';
		$reviewedDataArr[0]["Operater_Id"] = $_SESSION['authId'];
		$reviewedDataArr[0]["Action"] = 'delete';
		$reviewedDataArr[0]["Old_Value"] = $radQryRes[0]['rad_name'];
		//CLSReviewMedHx::reviewMedHx($reviewedDataArr,$_SESSION['authId'],"Radiology",$patient_id,0,0);
		$cls_review_med_hx->reviewMedHx($reviewedDataArr,$_SESSION['authId'],"Radiology",$patient_id,0,0);
		
		//--- CHANGE STATUS ----
		$qry = "update rad_test_data set rad_status = '3' where rad_test_data_id = '$del_id'";
		$sql_qry = imw_query($qry);
		if($sql_qry){
			$counter = 1;
		}
		return $counter;
	}
	
	// Creates tests name typeahead array 
	public function set_radiology_typeahead_arr(){
		//--- SET RADIOLOGY TYPE AHEAD ---
		$qry = imw_query("select distinct(lab_radiology_name) as lab_radiology_name from lab_radiology_tbl 
		where lab_radiology_status = '0' and lab_type = 'Radiology'
		and trim(lab_radiology_name) != '' order by trim(lab_radiology_name)");
		while($row = imw_fetch_array($qry)){
			$rad_qry_res[]  = $row;
		}
		$rad_title_arr = array();
		foreach($rad_qry_res as $obj){
			$lab_radiology_name = addslashes($obj['lab_radiology_name']);
			$rad_title_arr[] = $lab_radiology_name;
		}
		
		$strRadTitle = join(',',$rad_title_arr);
		return $strRadTitle;
	}
	
	// Returns array for review audit
	public function make_audit_review_array($data_arr){
		$default_review_arr = $this->review_rad_arr;
		$return = array();
		for($i=0;$i<count($default_review_arr);$i++){
			$return[$default_review_arr[$i]] = $data_arr[$i];
		}
		return $return; 
	}
	
	// Save Filled Details
	public function save_form_data($request){
		global $cls_review_med_hx;
		
		$arr_info_alert = array();
		$testDataArr = array();
		
		if(isset($request["info_alert"]) && count($request["info_alert"]) > 0){
			$arr_info_alert = unserialize(urldecode($request["info_alert"]));
		}
		
		//--- GET ALREADY EXISTS RADIOLOGY TESTS ----
		$rad_id_arr = array();
		for($i=1;$i<=$request['last_cnt'];$i++){
			if(empty($request['rad_id'.$i]) == false){
				$rad_id_arr[] = $request['rad_id'.$i];
			}
		}
		
		$rad_id_str = join(',',$rad_id_arr);
		
		//Get Test Data
		$rad_qry = imw_query("select * from rad_test_data where rad_test_data_id in ($rad_id_str)");
		while($testQryRes = imw_fetch_array($rad_qry)){
			$rad_test_data_id = $testQryRes['rad_test_data_id'];
			$rad_order_date = $testQryRes['rad_order_date'];
			if($rad_order_date == '0000-00-00'){
				$testQryRes['rad_order_date'] = '';
			}
			$rad_results_date = $testQryRes['rad_results_date'];
			if($rad_results_date == '0000-00-00'){
				$testQryRes['rad_results_date'] = '';
			}
			$testDataArr[$rad_test_data_id] = $testQryRes;
		}
		
		$patient_id = $this->patient_id;
		
		for($i=1;$i<=$request['last_cnt'];$i++){
			$lab_data_arr = array();
			$rad_id = $request['rad_id'.$i];
			$rad_data_arr['rad_fac_name'] = $request['rad_fac_name'.$i];
			$rad_data_arr['rad_address'] = $request['rad_address'.$i];
			$rad_data_arr['rad_patient_id'] = $patient_id;
			$rad_data_arr['rad_name'] = $request['rad_name'.$i];
			$rad_data_arr['rad_indication'] = $request['rad_indication'.$i];	
			$rad_data_arr['rad_results'] = $request['rad_results'.$i];
			$rad_data_arr['rad_results_date'] = getDateFormatDB($request['rad_results_date'.$i]);	
			$rad_data_arr['rad_type'] = $request['rad_type'.$i];
			$rad_data_arr['rad_type_other'] = $request['rad_type_other'.$i];
			$rad_data_arr['rad_loinc'] = $request['rad_loinc'.$i];
			$rad_data_arr['rad_order_date'] = getDateFormatDB($request['rad_order_date'.$i]);	
			$rad_data_arr['rad_instuctions'] = $request['rad_instuctions'.$i];
			$rad_data_arr['rad_order_time'] = $request['rad_order_time'.$i];
			$rad_data_arr['rad_results_time'] = $request['rad_results_time'.$i];
			$rad_data_arr['refusal'] = $request['refusal'.$i];
			$rad_data_arr['refusal_reason'] = $request['refusal_reason'.$i];
			$rad_data_arr['refusal_snomed'] = $request['refusal_snomed'.$i];
						
			if($request['rad_status'.$i]==""){
				$rad_data_arr['rad_status'] = 1;
			}else{
				$rad_data_arr['rad_status'] = $request['rad_status'.$i];
			}
			$rad_data_arr['rad_order_by'] = $request['rad_order_by'.$i];
			$rad_data_arr['rad_performed_date'] = date('Y-m-d');
			$rad_data_arr['rad_entered_by'] = $_SESSION['authId'];
			$rad_data_arr['snowmedCode'] = $request['rad_snowmed'.$i];

			//--- SAVE IN DATABASE ----
			if(empty($rad_data_arr['rad_name']) == false){		
				if(empty($rad_id) == true){
					$test_action = 'add';
					$rad_id = AddRecords($rad_data_arr,'rad_test_data');
				}
				else{
					$test_action = 'update';
					$rad_id = UpdateRecords($rad_id,'rad_test_data_id',$rad_data_arr,'rad_test_data');
				}
				
				//--- GET ALREADY EXISTS RADIOLOGY TEST DATA ---
				$radDataArr = $testDataArr[$rad_id];

				//--- REVIEWED CODE ---
				$RAD_TYPE = array('Select');
				$RAD_TYPE['395124008'] = 'virology';
				$RAD_TYPE['363680008'] = 'X-ray';
				$RAD_TYPE['46680005'] = 'Vital Sign';
				$RAD_TYPE['16310003'] = 'Ultrasound';
				$RAD_TYPE['69200006'] = 'Toxicology';
				$RAD_TYPE['68793005'] = 'Serology';
				$RAD_TYPE['71388002'] = 'Procedure';
				$RAD_TYPE['108257001'] = 'Pathology';
				$RAD_TYPE['371572003'] = 'Nuclear Medicine';
				$RAD_TYPE['19851009'] = 'Microbiology';
				$RAD_TYPE['113091000'] = 'MRI';
				$RAD_TYPE['363679005'] = 'Imaging';
				$RAD_TYPE['252275004'] = 'Hematology';
				$RAD_TYPE['275711006'] = 'Chemistry';
				$RAD_TYPE['77477000'] = 'CT';
				$RAD_TYPE['77343006'] = 'Angiography';
				$RAD_TYPE['Other'] = 'Other';
				$arrReviewRad = array();
				//require_once(dirname(__FILE__).'/../../common/audit_common_function.php');
				$radDataFields = make_field_type_array("rad_test_data");
				//--- RADIOLOGY TEST NAME FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_name'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_name";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_name");
				$review_rad_arr['Field_Text'] = 'Radiology Test name - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_name'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_name'];
				$arrReviewRad[] = $review_rad_arr;	

				//--- RADIOLOGY TYPE RESULT FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_type'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_type";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_type");
				$review_rad_arr['Field_Text'] = 'Radiology Test Type - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $RAD_TYPE[$radDataArr['rad_type']];
				$review_rad_arr['New_Value'] = $RAD_TYPE[$rad_data_arr['rad_type']];
				$arrReviewRad[] = $review_rad_arr;

				//--- RADIOLOGY LOINC CODE RESULT FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_loinc'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_loinc";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_loinc");
				$review_rad_arr['Field_Text'] = 'Radiology LOINC Code - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_loinc'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_loinc'];
				$arrReviewRad[] = $review_rad_arr;

				//--- RADIOLOGY TEST FACILITY NAME FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_fac_name'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_fac_name";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_fac_name");
				$review_rad_arr['Field_Text'] = 'Radiology facility name - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_fac_name'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_fac_name'];
				$arrReviewRad[] = $review_rad_arr;

				//--- RADIOLOGY TEST FACILITY ADDRESS FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_address'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_address";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_address");
				$review_rad_arr['Field_Text'] = 'Radiology facility address - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_address'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_address'];
				$arrReviewRad[] = $review_rad_arr;

				//--- RADIOLOGY TEST RESULT FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_indication'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_indication";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_indication");
				$review_rad_arr['Field_Text'] = 'Radiology Indication - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_indication'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_indication'];
				$arrReviewRad[] = $review_rad_arr;

				//--- RADIOLOGY TEST RESULT FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_results'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_results";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_results");
				$review_rad_arr['Field_Text'] = 'Radiology Results - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_results'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_results'];
				$arrReviewRad[] = $review_rad_arr;

				//--- RADIOLOGY TEST RESULT FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_results_date'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_results_date";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_results_date");
				$review_rad_arr['Field_Text'] = 'Radiology Results Date - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_results_date'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_results_date'];
				$arrReviewRad[] = $review_rad_arr;	

				//--- RADIOLOGY TEST RESULT FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_order_date'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_order_date";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_order_date");
				$review_rad_arr['Field_Text'] = 'Radiology ordered date - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_order_date'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_order_date'];
				$arrReviewRad[] = $review_rad_arr;


				//--- RADIOLOGY TEST RESULT FOR REVIEWED ----
				$test_status = '';
				if($rad_data_arr['rad_status'] == 1){
					$test_status = 'Pending';
				}
				else if($rad_data_arr['rad_status'] == 2){
					$test_status = 'Completed';
				}

				$pre_test_status = '';
				if($radDataArr['rad_status'] == 1){
					$pre_test_status = 'Pending';
				}
				else if($radDataArr['rad_status'] == 2){
					$pre_test_status = 'Completed';
				}

				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_status'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_status";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_status");
				$review_rad_arr['Field_Text'] = 'Radiology status - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $pre_test_status;
				$review_rad_arr['New_Value'] = $test_status;
				$arrReviewRad[] = $review_rad_arr;

				//--- RADIOLOGY INSTRUCTIONS RESULT FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_instuctions'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_instuctions";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_instuctions");
				$review_rad_arr['Field_Text'] = 'Radiology Instructions - '.$_POST['rad_name'.$i];
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_instuctions'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_instuctions'];
				$arrReviewRad[] = $review_rad_arr;


				//--- RADIOLOGY TEST RESULT FOR REVIEWED ----
				$review_rad_arr = array();		
				$review_rad_arr['Pk_Id'] = $rad_id;
				$review_rad_arr['Table_Name'] = 'rad_test_data';
				$review_rad_arr['UI_Filed_Name'] = 'rad_order_by'.$i;
				$review_rad_arr['Data_Base_Field_Name']= "rad_order_by";
				$review_rad_arr['Data_Base_Field_Type']= fun_get_field_type($radDataFields,"rad_order_by");
				$review_rad_arr['Field_Text'] = 'Radiology order by - '.$_POST['rad_name'.$i];
				$review_rad_arr['Depend_Select'] = "select CONCAT_WS(',',lname,fname) as provider";
				$review_rad_arr['Depend_Table'] =  "users";
				$review_rad_arr['Depend_Search'] = "id";
				$review_rad_arr['Operater_Id'] = $_SESSION['authId'];
				$review_rad_arr['Action'] = $test_action;
				$review_rad_arr['Old_Value'] = $radDataArr['rad_order_by'];
				$review_rad_arr['New_Value'] = $rad_data_arr['rad_order_by'];
				$arrReviewRad[] = $review_rad_arr;

				$cls_review_med_hx->reviewMedHx($arrReviewRad,$_SESSION['authId'],"Radiology",$_SESSION['patient'],0,0);
			}
		}
		
		$curr_tab = $request['curr_tab'];
		$next_tab = $request["next_tab"];
		$next_dir = $request["next_dir"];
		if($next_tab != ""){
			$curr_tab = $next_tab;
		}
		if($next_dir != ""){
			$curr_dir = $next_dir;
		}
		$buttons_to_show = $request["buttons_to_show"];
		if(constant("REMOTE_SYNC") == 1){
			$arrTables[] = "rad_test_data";
			$arrMedical = array();
			$arrMedical['medical']['patient'] = $_SESSION['patient'];
			$arrMedical['medical']['enable_parent_local_sync'] = 1;
			$arrMedical['medical']['tables'] = $arrTables;
			$taskArray['patient'] = array_merge($arrMedical);
			sync_remote_server($taskArray);
		}
		$return_array[0] = $curr_tab;
		$return_array[1] = $curr_dir;
		return $return_array;
	}
}
?>