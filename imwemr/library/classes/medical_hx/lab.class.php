<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> Radiology Class
 Access Type: Indirect Access.
 
*/

include_once $GLOBALS['srcdir'].'/classes/CLSAlerts.php';
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$cls_alerts = new CLSAlerts;

class Lab extends MedicalHistory
{
	public $operator_id;
	public $zonelist;
	
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->operator_id = $_SESSION['authId'];
		$this->zonelist = array('Kwajalein' => '-12.00', 'Pacific/Midway' => '-11.00', 'Pacific/Honolulu' => '-10.00', 'America/Anchorage' => '-09.00', 'America/Los_Angeles' => '-08.00', 'America/Denver' => '-07.00', 'America/Tegucigalpa' => '-06.00', 'America/New_York' => '-05.00', 'America/Caracas' => '-04.30', 'America/Halifax' => '-04.00', 'America/St_Johns' => '-03.30', 'America/Argentina/Buenos_Aires' => '-03.00', 'America/Sao_Paulo' => '-03.00', 'Atlantic/South_Georgia' => '-02.00', 'Atlantic/Azores' => '-01.00', 'Europe/Dublin' => '00.00', 'Europe/Belgrade' => '+01.00', 'Europe/Minsk' => '+02.00', 'Asia/Kuwait' => '+03.00', 'Asia/Tehran' => '+03.30', 'Asia/Muscat' => '+04.00', 'Asia/Yekaterinburg' => '+05.00', 'Asia/Kolkata' => '+05.30', 'Asia/Calcutta' => '+05.30', 'Asia/Katmandu' => '+05.45', 'Asia/Dhaka' => '+06.00', 'Asia/Rangoon' => '+06.30', 'Asia/Krasnoyarsk' => '+07.00', 'Asia/Brunei' => '+08.00', 'Asia/Seoul' => '+09.00', 'Australia/Darwin' => '+09.30', 'Australia/Canberra' => '+10.00', 'Asia/Magadan' => '+11.00', 'Pacific/Fiji' => '+12.00', 'Pacific/Tongatapu' => '+13.00');
	}
	
	public function get_abnormal_flag($return_type = 'array'){
		//$return_type = array:option 
		$abnormal_flag = array('A' => 'Abnormal (applies to non-numeric results)','&gt;' => 'Above absolute high-off instrument scale',
			'H' => 'Above high normal','HH' => 'Above upper panic limits','&lt;' => 'Below absolute low-off instrument scale',
			'L' => 'Below low normal','LL' => 'Below lower panic limits','B' => 'Better--use when direction not relevant',
			'I' => 'Intermediate. Indicates for microbiology susceptibilities only.','MS' => 'Moderately susceptible. Indicates for microbiology susceptibilities only.',
			'null' => 'No range defined, or normal ranges don\'t apply','N' => 'Normal (applies to non-numeric results)',
			'R' => 'Resistant. Indicates for microbiology susceptibilities only.','D' => 'Significant change down','U' => 'Significant change up');
		
		if($return_type == 'array'){
			return $abnormal_flag;
		}else{
			foreach($abnormal_flag as $key => $val){
				$key=addslashes($key);
				$val=addslashes($val);
				$abnormal_drop_opt.="<option value='$key'>$key - $val</option>";
			}
			return $abnormal_drop_opt;
		}
		return $abnormal_flag;
	}
	
	//Load lab test data
	function load_lab_test_data(){
		$data_return = array();
		$lab_test_id=5;
		$lab_qry = imw_query("select * from lab_test_data where lab_patient_id='".$this->patient_id."' AND lab_status!='3'");
		if(imw_num_rows($lab_qry) > 0){
			while($row = imw_fetch_array($lab_qry)){
				$data_return[] = $row;
			}
		}
		
		return $data_return;
	}
	
	//Returns provider typeahead arry
	public function get_providers_typeahead_arr(){
		$GLOBALS['arrValidCNPhy'] = array(1,10,11,12,19,21);
		$phy_id_cn=implode(',',$GLOBALS['arrValidCNPhy']);
		$sql = "select id,fname,lname,mname from users WHERE user_type in($phy_id_cn) and delete_status='0' order by lname, fname ASC";
		$rez = imw_query($sql);	

		$returnArr = array();
		while($row=imw_fetch_array($rez)){
			$phyId = $row["id"];
			$phyName = core_name_format($row['lname'], $row['fname'], $row['mname']);

			if(!in_array($phyName, $returnArr['phyName'])){
				$returnArr['phyId'][$phyName] = $phyId;
				$returnArr['phyName'][] = $phyName;
			}
		}		
		//$stringAllProvider = substr($stringAllProvider,0,-1);
		return $returnArr;
	}

	//Returns Loinc json array
	public function get_loinc_arr(){
		$sql = "select * from lab_radiology_tbl WHERE lab_radiology_status!='2' and lab_type='Lab' order by lab_radiology_name ASC";
		$rez = imw_query($sql);	
		while($row=imw_fetch_array($rez)){
			$id = $row["lab_radiology_tbl_id"];
			$lab_loinc = $row["lab_loinc"];
			$lab_radiology_name = $row["lab_radiology_name"];
			$stringAllObserv.="'".addslashes($lab_radiology_name)."',";
			$loinc_arr[$row["lab_radiology_name"]]=$row["lab_loinc"];
		}		
		$stringAllObserv = substr($stringAllObserv,0,-1);
		$loinc_arr_js['string_arr'] = $stringAllObserv;
		$loinc_arr_js['val_arr'] = $loinc_arr;
		return $loinc_arr_js;
	}	
	
	//Returns Json data array for Modal box
	public function get_modal_box_data($id){
		$modal_data_arr = array();
		$lab_test_id = $id;
		
		//Get Order Date,Time,Timezone
		$lab_order_datetime_qry = imw_query("select * from lab_test_data where lab_test_data_id='$lab_test_id' and lab_status!='3'");
		$lab_order_detail = imw_fetch_array($lab_order_datetime_qry);
		$default_timezone = date_default_timezone_get();
		$current_time_zone = $this->zonelist[$default_timezone];
		if($lab_test_id>0){
			$order_time_zone=$lab_order_detail['order_time_zone'];
		}else{
			$order_time_zone=$current_time_zone;
		}
		$provider1_name="";
		$lab_test_order_by_name_exp=explode('(',$lab_order_detail['lab_test_order_by_name']);
		$provider1_name=trim($lab_test_order_by_name_exp[0]);

		$order_no="";
		if($lab_order_detail['hl7_mu_id']>0){
			$order_no=$lab_order_detail['impoted_order_id'];
		}else{
			if($lab_order_detail['lab_test_data_id']>0){
				$order_no='iDoc'.$lab_order_detail['lab_test_data_id'];
			}
		}
		if($lab_test_id>0){
			$lab_order_date=$lab_order_detail['lab_order_date'];
			$lab_order_time=$lab_order_detail['lab_order_time'];
		}else{
			$lab_order_date=date('Y-m-d');
			$lab_order_time=date('H:i:s');
		}
		$modal_data_arr['order_date_time']['order_no'] = $order_no;
		$modal_data_arr['order_date_time']['provider1_name'] = $provider1_name;
		$modal_data_arr['order_date_time']['order_provider_id'] = $this->getPhyIdByName($provider1_name);
		$modal_data_arr['order_date_time']['lab_order_date'] = get_date_format($lab_order_date);
		$modal_data_arr['order_date_time']['lab_order_time'] = $lab_order_time;
		$modal_data_arr['order_date_time']['order_time_zone'] = $order_time_zone;
        
		$modal_data_arr['order_date_time']['dss_collection_type'] = $lab_order_detail['dss_collection_type'];
		$modal_data_arr['order_date_time']['dss_urgency'] = $lab_order_detail['dss_urgency'];
		$modal_data_arr['order_date_time']['dss_schedules'] = $lab_order_detail['dss_schedules'];
		
		//Get lab_observation_requested data
		$modal_data_arr['lab_obser_req'] = $this->get_lab_obser_data($lab_test_id,'lab_observation_requested');
		
		//Get lab_specimen data
		$modal_data_arr['lab_specimen'] = $this->get_lab_obser_data($lab_test_id,'lab_specimen');	
        
		//Get lab_specimen data
		$modal_data_arr['lab_sample'] = $this->get_lab_obser_data($lab_test_id,'lab_sample');	
		
		//get lab_observation_result
		$modal_data_arr['lab_obser_result'] = $this->get_lab_obser_data($lab_test_id,'lab_observation_result');	
		
		//get abnormal array
		$modal_data_arr['abnormal_arr'] = $this->get_abnormal_flag();
		
		return $modal_data_arr;
	}
	
	//Returns lab observation related data
	public function get_lab_obser_data($id,$table_name){
		$return_arr = array();
		$qry = imw_query("select * from  $table_name where lab_test_id='$id' and lab_test_id > 0 and del_status = '0'");
		if(imw_num_rows($qry) > 0){
			while($row = imw_fetch_assoc($qry)){
				$return_arr[] = $row;
			}
		}
		return $return_arr;
	}
	
	
	//Delete Lab record
	public function del_lab_record($del_id, $labOrder){
		global $cls_review;
		$counter = 0;
		$del_date = date('Y-m-d');
		$del_operator_id = $this->operator_id;
		imw_query("update lab_test_data set lab_status='3' where lab_test_data_id='$del_id'");
		$counter = ($counter+(imw_affected_rows()));
		imw_query("update lab_observation_requested set del_status='1',del_date='$del_date',del_operator_id='$del_operator_id' where lab_test_id='$del_id'");
		$counter = ($counter+(imw_affected_rows()));
		imw_query("update lab_observation_result set del_status='1',del_date='$del_date',del_operator_id='$del_operator_id' where lab_test_id='$del_id'");
		$counter = ($counter+(imw_affected_rows()));
		imw_query("update lab_specimen set del_status='1',del_date='$del_date',del_operator_id='$del_operator_id' where lab_test_id='$del_id'");
		$counter = ($counter+(imw_affected_rows()));
		imw_query("update lab_sample set del_status='1',del_date='$del_date',del_operator_id='$del_operator_id' where lab_test_id='$del_id'");
		$counter = ($counter+(imw_affected_rows()));
		
		if($this->policy_status == 1){
			$reviewImmArr = array();
			$reviewImmArr[0]['Pk_Id'] = $del_id;
			$reviewImmArr[0]['Table_Name'] = 'lab_test_data';
			$reviewImmArr[0]['Field_Text'] = 'Lab - (ID:#'.$del_id.')';
			$reviewImmArr[0]['Operater_Id'] = $_SESSION['authId'];
			$reviewImmArr[0]['Action'] = 'delete';
			$reviewImmArr[0]['Old_Value'] = $labOrder;
			$cls_review->reviewMedHx($reviewImmArr,$_SESSION['authId'],"Lab",$this->patient_id,0,0);
		}
		return $counter;
	}
	
	
	//Delete Observation records
	public function del_obser_rec($request_id,$request_from){
		global $cls_review;
		$counter = 0;
		$selection = $table = $old_val = $field_txt = '';
		
		if($request_from == 'request'){				//Observation Request
			$selection = 'service,lab_test_id';
			$table = 'lab_observation_requested';
		}else if($request_from == 'specimen'){		//Observation Speciemen
			$selection = 'collection_type,lab_test_id';
			$table = 'lab_specimen';
		}else if($request_from == 'sample'){		//Observation Sample
			$selection = 'smp_collection_type,lab_test_id';
			$table = 'lab_sample';
		}else if($request_from == 'result'){		//Observation Result
			$selection = 'observation,lab_test_id';
			$table = 'lab_observation_result';
		}
		
		if($request_id != ""){
			$sql = "SELECT ".$selection." FROM ".$table." WHERE id=".$request_id;
			$res = imw_query($sql);
			$row_lor = imw_fetch_assoc($res);
			$del_date=date('Y-m-d');
			$del_operator_id = $this->operator_id;
			imw_query("update ".$table." set del_status='1',del_date='$del_date',del_operator_id='$del_operator_id' where id='$request_id'");
			$counter = ($counter+imw_affected_rows());
			
			//Result case only
			if($request_from == 'result' && $row_lor['lab_test_id']>0){
				$counter = 0;
				$qry_chk = "select * from  lab_observation_result where lab_test_id  ='".$row_lor['lab_test_id']."' and del_status='0' and result!=''";
				$res_chk = imw_query($qry_chk);
				if(imw_num_rows($res_chk)>0){
					imw_query("update lab_test_data set lab_status='2' where lab_test_data_id='".$row_lor['lab_test_id']."'");
					$counter = ($counter+imw_affected_rows());
				}else{
					imw_query("update lab_test_data set lab_status='1' where lab_test_data_id='".$row_lor['lab_test_id']."'");
					$counter = ($counter+imw_affected_rows());
				}
			}
			
			if($this->policy_status == 1){
				$reviewImmArr = array();
				$reviewImmArr[0]['Pk_Id'] = $request_id;
				$reviewImmArr[0]['Table_Name'] = $table;
				$reviewImmArr[0]['Field_Text'] = 'Lab Observ - (ID:#'.$row_lor['lab_test_id'].')';
				$reviewImmArr[0]['Operater_Id'] = $_SESSION['authId'];
				$reviewImmArr[0]['Action'] = 'delete';
				$reviewImmArr[0]['Old_Value'] = $row_lor['observation'];
				$cls_review->reviewMedHx($reviewImmArr,$_SESSION['authId'],"Lab",$this->patient_id,0,0);
			}
		}
		return $counter;
	}
	
	public function request_function($lab_id){
		$service_arr = array();
		$qry = imw_query("select * from lab_observation_requested where lab_test_id='$lab_id' and del_status='0'");
		while($row = imw_fetch_array($qry)){
			if($row['service']!=""){
				$service_arr[] = '<div class="row">
					<div class="col-sm-10 text-left"><span class="pull-left"><img src="../../library/images/bullet_icn.png"></span>&nbsp;<span onClick="javascript:new_lab_order(\''.$lab_id.'\')">'.$row['service'].'</span></div><div class="col-sm-1 text-right"><span class="glyphicon glyphicon-info-sign" id="info_prob_$row_id_number" title="Info Button" onclick="open_window(\''.$row["loinc"].'\')"></span></div></div>';
			}
		}
		if(count($service_arr)>0){
			$service_imp=implode('',$service_arr);
		}
		return $service_imp;
	}
	
	function result_function($lab_id){
		$result_arr = array();
		$qry = imw_query("select observation,result,result_loinc from lab_observation_result where lab_test_id='$lab_id' and del_status='0'");
		while($row = imw_fetch_array($qry)){
			if($row['result']!=""){
				if(trim($row['observation'])!=''){$row['result'] = $row['observation'].': '.$row['result'];}
				
				$result_arr[] = '<div class="row">
					<div class="col-sm-10 text-left"><span class="pull-left"><img src="../../library/images/bullet_icn.png"></span>&nbsp;<span onClick="javascript:new_lab_order(\''.$lab_id.'\')">'.$row['result'].'</span></div><div class="col-sm-1 text-right"><span class="glyphicon glyphicon-info-sign" id="info_prob_$row_id_number" title="Info Button" onclick="open_window(\''.$row["result_loinc"].'\');"></span></div></div>';
			}
		}
		if(count($result_arr)>0){
			$result_imp=implode('',$result_arr);
		}
		return $result_imp;
	}
	
	function specimen_function($lab_id){
		$collection_type_arr=array();
		$qry=imw_query("select * from lab_specimen where lab_test_id='$lab_id' and del_status='0'");
		while($row=imw_fetch_array($qry)){
			if($row['collection_type']!=""){
				if(trim($row['collection_rejection'])!=''){
					$row['collection_type'] = $row['collection_type'].'<br><span class="text-red">&nbsp;&nbsp;&nbsp;'.$row['collection_rejection'].'</span>';
				}
				$collection_type_arr[]="<div class='row'><div class='col-sm-12 text-left'><span class='pull-left'><img src='../../library/images/bullet_icn.png'></span>&nbsp;".$row['collection_type']."</div></div>";
			}
		}
		if(count($collection_type_arr)>0){
			$collection_type_imp=implode('',$collection_type_arr);
		}
		return $collection_type_imp;
	}
	
	function sample_function($lab_id){
		$collection_type_arr=array();
		$qry=imw_query("select * from lab_sample where lab_test_id='$lab_id' and del_status='0'");
		while($row=imw_fetch_array($qry)){
			if($row['smp_collection_type']!=""){
				if(trim($row['sample_rejection'])!=''){
					$row['smp_collection_type'] = $row['smp_collection_type'].'<br><span class="text-red">&nbsp;&nbsp;&nbsp;'.$row['sample_rejection'].'</span>';
				}
				$collection_type_arr[]="<div class='row'><div class='col-sm-12 text-left'><span class='pull-left'><img src='../../library/images/bullet_icn.png'></span>&nbsp;".$row['smp_collection_type']."</div></div>";
			}
		}
		if(count($collection_type_arr)>0){
			$collection_type_imp=implode('',$collection_type_arr);
		}
		return $collection_type_imp;
	}
	
	function lab_observation_result($lab_test_id){
		$return = '';
		$qry_chk = "select result from  lab_observation_result where lab_test_id  ='".$lab_test_id."' and del_status='0' and result!=''";
		$res_chk = imw_query($qry_chk);
		if(imw_num_rows($res_chk)>0){
			$return = "Done";
		}else{
			$return = "Pending";
		}
		return $return;
	}
	
	public function set_lab_cls_alerts(){
		global $cls_alerts;
		$return_str= '';
		if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
			$alertToDisplayAt = "admin_specific_chart_note_med_hx";
			$return_str .= $cls_alerts->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$form_id,"350px");
			$alertToDisplayAt = "patient_specific_chart_note_med_hx";
			$return_str .= $cls_alerts->getPatSpecificAlert($_SESSION['patient'],$alertToDisplayAt,"350px");
			$return_str .= $cls_alerts->autoSetDivLeftMargin("140","265");
			$return_str .= $cls_alerts->autoSetDivTopMargin("250","30");
			$return_str .= $cls_alerts->writeJS();
		}
		return $return_str;
	}
	
	
	//Saving New lab orders
	public function save_new_lab_ord($request){
		global $cls_review;
		$counter = 0;
		if(isset($request['save']) && trim($request['save']) != ''){
			$enter_date=date('Y-m-d');
			$lab_test_id=$request['lab_test_id'];
			$row_ltd = '';
			if($lab_test_id>0){
				$qry = "select lab_test_order_by_name, lab_order_date, lab_order_time, order_time_zone, dss_lab_order_number
						from lab_test_data where lab_test_data_id  =".$lab_test_id;
				$res = imw_query($qry);
				$row_ltd = imw_fetch_assoc($res);
				$lab_qry="update lab_test_data";
				$lab_whr_qry=" where lab_test_data_id='$lab_test_id'";
				$ltd_action = "update";
			}else{
				$lab_qry="insert into lab_test_data";
				$lab_entered_by_qry=" ,lab_entered_by='$operator_id'";
			}

            if(isDssEnable()) {
                $dss_collection_type=$_REQUEST['dss_collection_type'];
                $dss_urgency=$_REQUEST['dss_urgency'];
                $dss_schedules=$_REQUEST['dss_schedules'];
                $lab_entered_by_qry.=" ,dss_collection_type='$dss_collection_type',dss_urgency='$dss_urgency',dss_schedules='$dss_schedules'  ";
            } else {
                $lab_entered_by_qry.=" ,dss_collection_type='',dss_urgency='',dss_schedules=''  ";
            }
			
			$order_by=$request['order_by'];
			if($order_by!=""){
				$primaryProviderText = addslashes($_REQUEST['order_by']);
				list($primaryProviderlname, $primaryProviderfname) = explode(', ', $primaryProviderText);		
				$pri_prov_qry = imw_query("select id from users where fname='$primaryProviderfname' and lname='$primaryProviderlname' order by delete_status asc");
				$proId = imw_fetch_array($pri_prov_qry);
				$primaryProviderId= $proId[0]['id'];
			}
			$order_by_id = ($request['order_by_prov_id']) ? $request['order_by_prov_id'] : $primaryProviderId;
			
			$order_date=getDateFormatDB($request['order_date']);
			$order_time=$request['order_time'];
			$order_time_zone=$request['order_time_zone'];
			$patient_id=$_SESSION['patient'];
			$lab_qry.=" set order_time_zone='$order_time_zone',lab_test_order_by='$order_by_id',lab_test_order_by_name='".imw_real_escape_string($order_by)."',lab_order_date='$order_date',lab_order_time='$order_time',lab_patient_id='$patient_id' $lab_entered_by_qry $lab_whr_qry";
			imw_query($lab_qry);
			$counter = ($counter+imw_affected_rows());
			if($lab_test_id<=0){
				$ltd_action = "add";
				$lab_test_id=imw_insert_id();
			}
			if($this->policy_status == 1){
				$arrLTD_fld = make_field_type_array("lab_test_data");
				$arrReview_lab = array();
				$review_lab_arr = array();		
				$review_lab_arr['Pk_Id'] = $lab_test_id;
				$review_lab_arr['Table_Name'] = 'lab_test_data';
				$review_lab_arr['UI_Filed_Name'] = 'order_by';
				$review_lab_arr['Data_Base_Field_Name']= "lab_test_order_by_name";
				$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLTD_fld,"lab_test_order_by_name");
				$review_lab_arr['Field_Text'] = 'Order By - (ID#'.$lab_test_id.')';
				$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
				$review_lab_arr['Action'] = $ltd_action;
				$review_lab_arr['Old_Value'] = $row_ltd['lab_test_order_by_name'];
				$review_lab_arr['New_Value'] = imw_real_escape_string($order_by);
				$arrReview_lab[] = $review_lab_arr;

				$review_lab_arr = array();
				$review_lab_arr['Pk_Id'] = $lab_test_id;
				$review_lab_arr['Table_Name'] = 'lab_test_data';
				$review_lab_arr['UI_Filed_Name'] = 'order_date';
				$review_lab_arr['Data_Base_Field_Name']= "lab_order_date";
				$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLTD_fld,"lab_order_date");
				$review_lab_arr['Field_Text'] = 'Order Date - (ID#'.$lab_test_id.')';
				$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
				$review_lab_arr['Action'] = $ltd_action;
				$review_lab_arr['Old_Value'] = $row_ltd['lab_order_date'];
				$review_lab_arr['New_Value'] = $order_date;
				$arrReview_lab[] = $review_lab_arr;

				$review_lab_arr = array();
				$review_lab_arr['Pk_Id'] = $lab_test_id;
				$review_lab_arr['Table_Name'] = 'lab_test_data';
				$review_lab_arr['UI_Filed_Name'] = 'order_time';
				$review_lab_arr['Data_Base_Field_Name']= "lab_order_time";
				$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLTD_fld,"lab_order_time");
				$review_lab_arr['Field_Text'] = 'Order Time - (ID#'.$lab_test_id.')';
				$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
				$review_lab_arr['Action'] = $ltd_action;
				$review_lab_arr['Old_Value'] = $this->getTimeFormatAudit($row_ltd['lab_order_time']);
				$review_lab_arr['New_Value'] = $this->getTimeFormatAudit($order_time);
				$arrReview_lab[] = $review_lab_arr;

				$review_lab_arr = array();
				$review_lab_arr['Pk_Id'] = $lab_test_id;
				$review_lab_arr['Table_Name'] = 'lab_test_data';
				$review_lab_arr['UI_Filed_Name'] = 'order_time_zone';
				$review_lab_arr['Data_Base_Field_Name']= "order_time_zone";
				$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLTD_fld,"order_time_zone");
				$review_lab_arr['Field_Text'] = 'Order Time Zone - (ID#'.$lab_test_id.')';
				$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
				$review_lab_arr['Action'] = $ltd_action;
				$review_lab_arr['Old_Value'] = $row_ltd['order_time_zone'];
				$review_lab_arr['New_Value'] = $order_time_zone;
				$arrReview_lab[] = $review_lab_arr;
				$cls_review->reviewMedHx($arrReview_lab,$_SESSION['authId'],"Lab",$_SESSION['patient'],0,0);
			}
			for($i=1;$i<=$request['request_cont'];$i++){
				if($request['service_'.$i]!=""){
					if($request['requested_id_'.$i]>0){
						$requested_id=$request['requested_id_'.$i];
						
						$qry = "select * from  lab_observation_requested where id  =".$requested_id;
						$res = imw_query($qry);
						$row_lor = imw_fetch_assoc($res);
						
						$lor_action = "update";
						$request_qry="update lab_observation_requested";
						$request_whr_qry=" where id='$requested_id'";
						$request_entered_by_qry="";
					}else{
						$requested_id="";
						$request_whr_qry="";
						$request_qry="insert into lab_observation_requested";
						$request_entered_by_qry=" ,request_entered_by='$operator_id',request_entered_date='$enter_date'";
					}
					
					$service=$request['service_'.$i];
					$loinc=$request['loinc_'.$i];
					$start_date=getDateFormatDB($request['start_date_'.$i]);
					$start_time=$request['start_time_'.$i];
					$end_date=getDateFormatDB($request['end_date_'.$i]);
					$end_time=$request['end_time_'.$i];
					$clinical_info=$request['clinical_info_'.$i];
					$request_qry.=" set lab_test_id='$lab_test_id',service='$service',loinc='$loinc',start_date='$start_date',start_time='$start_time',
					end_date='$end_date',end_time='$end_time',clinical_info='$clinical_info' $request_entered_by_qry $request_whr_qry";
					imw_query($request_qry);
					$counter = ($counter+imw_affected_rows());
					if($requested_id<=0){
						$lor_action = "add";
						$requested_id=imw_insert_id();
					}
					if($this->policy_status == 1){
						$arrLOR_fld = make_field_type_array("lab_observation_requested");
						$arrReview_lab = array();
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $requested_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_requested';
						$review_lab_arr['UI_Filed_Name'] = $_POST['service_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "service";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLOR_fld,"service");
						$review_lab_arr['Field_Text'] = 'Service- (ID#'.$lab_test_id.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lor_action;
						$review_lab_arr['Old_Value'] = $row_lor['service'];
						$review_lab_arr['New_Value'] = $service;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $requested_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_requested';
						$review_lab_arr['UI_Filed_Name'] = $_POST['loinc_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "loinc";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLOR_fld,"loinc");
						$review_lab_arr['Field_Text'] = 'Service Loinc - (ID#'.$lab_test_id.' - '.$service.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lor_action;
						$review_lab_arr['Old_Value'] = $row_lor['loinc'];
						$review_lab_arr['New_Value'] = $loinc;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();
						$review_lab_arr['Pk_Id'] = $requested_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_requested';
						$review_lab_arr['UI_Filed_Name'] = $_POST['start_date_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "start_date";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLOR_fld,"start_date");
						$review_lab_arr['Field_Text'] = 'Service Start Date - (ID#'.$lab_test_id.' - '.$service.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lor_action;
						$review_lab_arr['Old_Value'] = $row_lor['start_date'];
						$review_lab_arr['New_Value'] = $start_date;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();
						$review_lab_arr['Pk_Id'] = $requested_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_requested';
						$review_lab_arr['UI_Filed_Name'] = $_POST['start_time_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "start_time";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLOR_fld,"start_time");
						$review_lab_arr['Field_Text'] = 'Service Start Time - (ID#'.$lab_test_id.' - '.$service.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lor_action;
						$review_lab_arr['Old_Value'] = $this->getTimeFormatAudit($row_lor['start_time']);
						$review_lab_arr['New_Value'] = $this->getTimeFormatAudit($start_time);
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();
						$review_lab_arr['Pk_Id'] = $requested_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_requested';
						$review_lab_arr['UI_Filed_Name'] = $_POST['end_date_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "end_date";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLOR_fld,"end_date");
						$review_lab_arr['Field_Text'] = 'Service End Date - (ID#'.$lab_test_id.' - '.$service.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lor_action;
						$review_lab_arr['Old_Value'] = $row_lor['end_date'];
						$review_lab_arr['New_Value'] = $end_date;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();
						$review_lab_arr['Pk_Id'] = $requested_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_requested';
						$review_lab_arr['UI_Filed_Name'] = $_POST['end_time_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "end_time";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLOR_fld,"end_time");
						$review_lab_arr['Field_Text'] = 'Service End Time - (ID#'.$lab_test_id.' - '.$service.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lor_action;
						$review_lab_arr['Old_Value'] = $this->getTimeFormatAudit($row_lor['end_time']);
						$review_lab_arr['New_Value'] = $this->getTimeFormatAudit($end_time);
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();
						$review_lab_arr['Pk_Id'] = $requested_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_requested';
						$review_lab_arr['UI_Filed_Name'] = $_POST['clinical_info_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "clinical_info";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLOR_fld,"clinical_info");
						$review_lab_arr['Field_Text'] = 'Service Clinical Info - (ID#'.$lab_test_id.' - '.$service.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lor_action;
						$review_lab_arr['Old_Value'] = $row_lor['clinical_info'];
						$review_lab_arr['New_Value'] = $clinical_info;
						$arrReview_lab[] = $review_lab_arr;

						$cls_review->reviewMedHx($arrReview_lab,$_SESSION['authId'],"Lab",$_SESSION['patient'],0,0);
					}
				}
			}
            $dss_collection_id='';
			for($i=1;$i<=$request['specimen_cont'];$i++){
				if($request['collection_type_'.$i]){
					if($request['specimen_id_'.$i]>0){
						$specimen_id=$request['specimen_id_'.$i];
						
						$qry = "select * from  lab_specimen where id  =".$specimen_id;
						$res = imw_query($qry);
						$row_ls = imw_fetch_assoc($res);
						
						$ls_action = 'update';
						$specimen_qry="update lab_specimen";
						$specimen_whr_qry=" where id='$specimen_id'";
						$specimen_entered_by_qry="";
					}else{
						$specimen_id="";
						$specimen_whr_qry="";
						$specimen_qry="insert into lab_specimen";
						$specimen_entered_by_qry=" ,specimen_entered_by='$operator_id',specimen_entered_date='$enter_date'";
					}
					$collection_type=$request['collection_type_'.$i];
					$dss_collection_id=$request['hidden_collection_type_'.$i];
					$collection_start_date=getDateFormatDB($request['collection_start_date_'.$i]);
					$collection_start_time=$request['collection_start_time_'.$i];
					$collection_end_date=getDateFormatDB($request['collection_end_date_'.$i]);
					$collection_end_time=$request['collection_end_time_'.$i];
					$collection_condition=$request['collection_condition_'.$i];
					$collection_rejection=$request['collection_rejection_'.$i];
					$collection_comments=$request['collection_comments_'.$i];
					$specimen_qry.=" set lab_test_id='$lab_test_id',collection_type='$collection_type',dss_collection_id='$dss_collection_id',collection_start_date='$collection_start_date',collection_start_time='$collection_start_time',
					collection_end_date='$collection_end_date',collection_end_time='$collection_end_time',collection_condition='$collection_condition',collection_rejection='$collection_rejection',
					collection_comments='$collection_comments' $specimen_entered_by_qry $specimen_whr_qry";
					imw_query($specimen_qry);
					$counter = ($counter+imw_affected_rows());
					if($specimen_id<=0){
						$ls_action = "add";
						$specimen_id=imw_insert_id();
					}
					if($this->policy_status == 1){
						$arrLS_fld = make_field_type_array("lab_specimen");

						$arrReview_lab = array();
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $specimen_id;
						$review_lab_arr['Table_Name'] = 'lab_specimen';
						$review_lab_arr['UI_Filed_Name'] = $_POST['collection_type_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "collection_type";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"collection_type");
						$review_lab_arr['Field_Text'] = 'Specimen Type - (ID#'.$lab_test_id.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['collection_type'];
						$review_lab_arr['New_Value'] = $collection_type;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $specimen_id;
						$review_lab_arr['Table_Name'] = 'lab_specimen';
						$review_lab_arr['UI_Filed_Name'] = $_POST['collection_start_date_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "collection_start_date";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"collection_start_date");
						$review_lab_arr['Field_Text'] = 'Specimen Start Date - (ID#'.$lab_test_id.' - '.$collection_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['collection_start_date'];
						$review_lab_arr['New_Value'] = $collection_start_date;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $specimen_id;
						$review_lab_arr['Table_Name'] = 'lab_specimen';
						$review_lab_arr['UI_Filed_Name'] = $_POST['collection_start_time_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "collection_start_time";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"collection_start_time");
						$review_lab_arr['Field_Text'] = 'Specimen Start Time - (ID#'.$lab_test_id.' - '.$collection_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $this->getTimeFormatAudit($row_ls['collection_start_time']);
						$review_lab_arr['New_Value'] = $this->getTimeFormatAudit($collection_start_time);
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $specimen_id;
						$review_lab_arr['Table_Name'] = 'lab_specimen';
						$review_lab_arr['UI_Filed_Name'] = $_POST['collection_end_date_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "collection_end_date";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"collection_end_date");
						$review_lab_arr['Field_Text'] = 'Specimen End Date - (ID#'.$lab_test_id.' - '.$collection_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['collection_end_date'];
						$review_lab_arr['New_Value'] = $collection_end_date;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $specimen_id;
						$review_lab_arr['Table_Name'] = 'lab_specimen';
						$review_lab_arr['UI_Filed_Name'] = $_POST['collection_end_time_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "collection_end_time";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"collection_end_time");
						$review_lab_arr['Field_Text'] = 'Specimen End Time - (ID#'.$lab_test_id.' - '.$collection_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $this->getTimeFormatAudit($row_ls['collection_end_time']);
						$review_lab_arr['New_Value'] = $this->getTimeFormatAudit($collection_end_time);
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $specimen_id;
						$review_lab_arr['Table_Name'] = 'lab_specimen';
						$review_lab_arr['UI_Filed_Name'] = $_POST['collection_condition_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "collection_condition";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"collection_condition");
						$review_lab_arr['Field_Text'] = 'Specimen Condition - (ID#'.$lab_test_id.' - '.$collection_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['collection_condition'];
						$review_lab_arr['New_Value'] = $collection_condition;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $specimen_id;
						$review_lab_arr['Table_Name'] = 'lab_specimen';
						$review_lab_arr['UI_Filed_Name'] = $_POST['collection_rejection_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "collection_rejection";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"collection_rejection");
						$review_lab_arr['Field_Text'] = 'Specimen Condition - (ID#'.$lab_test_id.' - '.$collection_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['collection_rejection'];
						$review_lab_arr['New_Value'] = $collection_rejection;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $specimen_id;
						$review_lab_arr['Table_Name'] = 'lab_specimen';
						$review_lab_arr['UI_Filed_Name'] = $_POST['collection_comments_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "collection_comments";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"collection_comments");
						$review_lab_arr['Field_Text'] = 'Specimen Condition - (ID#'.$lab_test_id.' - '.$collection_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['collection_comments'];
						$review_lab_arr['New_Value'] = $collection_comments;
						$arrReview_lab[] = $review_lab_arr;
						$cls_review->reviewMedHx($arrReview_lab,$_SESSION['authId'],"Lab",$_SESSION['patient'],0,0);
					}
				}
			}
            
            $dss_sample_id='';
            if(isDssEnable()){
            for($i=1;$i<=$request['sample_cont'];$i++){
				if($request['smp_collection_type_'.$i]){
					if($request['sample_id_'.$i]>0){
						$sample_id=$request['sample_id_'.$i];
						
						$qry = "select * from  lab_sample where id  =".$sample_id;
						$res = imw_query($qry);
						$row_ls = imw_fetch_assoc($res);
						
						$ls_action = 'update';
						$sample_qry="update lab_sample";
						$sample_whr_qry=" where id='$sample_id'";
						$sample_entered_by_qry="";
					}else{
						$sample_id="";
						$sample_whr_qry="";
						$sample_qry="insert into lab_sample";
						$sample_entered_by_qry=" ,sample_entered_by='$operator_id',sample_entered_date='$enter_date'";
					}
					$smp_collection_type=$request['smp_collection_type_'.$i];
					$dss_sample_id=$request['hidden_smp_collection_type_'.$i];
					$sample_start_date=getDateFormatDB($request['sample_start_date_'.$i]);
					$sample_start_time=$request['sample_start_time_'.$i];
					$sample_end_date=getDateFormatDB($request['sample_end_date_'.$i]);
					$sample_end_time=$request['sample_end_time_'.$i];
					$sample_condition=$request['sample_condition_'.$i];
					$sample_rejection=$request['sample_rejection_'.$i];
					$sample_comments=$request['sample_comments_'.$i];
					$sample_qry.=" set lab_test_id='$lab_test_id',smp_collection_type='$smp_collection_type',dss_sample_id='$dss_sample_id',sample_start_date='$sample_start_date',sample_start_time='$sample_start_time',
					sample_end_date='$sample_end_date',sample_end_time='$sample_end_time',sample_condition='$sample_condition',sample_rejection='$sample_rejection',
					sample_comments='$sample_comments' $sample_entered_by_qry $sample_whr_qry";
					imw_query($sample_qry);
					$counter = ($counter+imw_affected_rows());
					if($sample_id<=0){
						$ls_action = "add";
						$sample_id=imw_insert_id();
					}
					if($this->policy_status == 1){
						$arrLS_fld = make_field_type_array("lab_sample");

						$arrReview_lab = array();
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $sample_id;
						$review_lab_arr['Table_Name'] = 'lab_sample';
						$review_lab_arr['UI_Filed_Name'] = $_POST['smp_collection_type_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "smp_collection_type";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"smp_collection_type");
						$review_lab_arr['Field_Text'] = 'Sample Type - (ID#'.$lab_test_id.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['smp_collection_type'];
						$review_lab_arr['New_Value'] = $smp_collection_type;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $sample_id;
						$review_lab_arr['Table_Name'] = 'lab_sample';
						$review_lab_arr['UI_Filed_Name'] = $_POST['sample_start_date_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "sample_start_date";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"sample_start_date");
						$review_lab_arr['Field_Text'] = 'Sample Start Date - (ID#'.$lab_test_id.' - '.$sample_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['sample_start_date'];
						$review_lab_arr['New_Value'] = $sample_start_date;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $sample_id;
						$review_lab_arr['Table_Name'] = 'lab_sample';
						$review_lab_arr['UI_Filed_Name'] = $_POST['sample_start_time_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "sample_start_time";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"sample_start_time");
						$review_lab_arr['Field_Text'] = 'Sample Start Time - (ID#'.$lab_test_id.' - '.$sample_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $this->getTimeFormatAudit($row_ls['sample_start_time']);
						$review_lab_arr['New_Value'] = $this->getTimeFormatAudit($sample_start_time);
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $sample_id;
						$review_lab_arr['Table_Name'] = 'lab_sample';
						$review_lab_arr['UI_Filed_Name'] = $_POST['sample_end_date_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "sample_end_date";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"sample_end_date");
						$review_lab_arr['Field_Text'] = 'Sample End Date - (ID#'.$lab_test_id.' - '.$sample_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['sample_end_date'];
						$review_lab_arr['New_Value'] = $sample_end_date;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $sample_id;
						$review_lab_arr['Table_Name'] = 'lab_sample';
						$review_lab_arr['UI_Filed_Name'] = $_POST['sample_end_time_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "sample_end_time";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"sample_end_time");
						$review_lab_arr['Field_Text'] = 'Sample End Time - (ID#'.$lab_test_id.' - '.$sample_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $this->getTimeFormatAudit($row_ls['sample_end_time']);
						$review_lab_arr['New_Value'] = $this->getTimeFormatAudit($sample_end_time);
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $sample_id;
						$review_lab_arr['Table_Name'] = 'lab_sample';
						$review_lab_arr['UI_Filed_Name'] = $_POST['sample_condition_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "sample_condition";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"sample_condition");
						$review_lab_arr['Field_Text'] = 'Sample Condition - (ID#'.$lab_test_id.' - '.$sample_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['sample_condition'];
						$review_lab_arr['New_Value'] = $sample_condition;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $sample_id;
						$review_lab_arr['Table_Name'] = 'lab_sample';
						$review_lab_arr['UI_Filed_Name'] = $_POST['sample_rejection_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "sample_rejection";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"sample_rejection");
						$review_lab_arr['Field_Text'] = 'Sample Condition - (ID#'.$lab_test_id.' - '.$sample_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['sample_rejection'];
						$review_lab_arr['New_Value'] = $sample_rejection;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $sample_id;
						$review_lab_arr['Table_Name'] = 'lab_sample';
						$review_lab_arr['UI_Filed_Name'] = $_POST['sample_comments_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "sample_comments";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLS_fld,"sample_comments");
						$review_lab_arr['Field_Text'] = 'Sample Condition - (ID#'.$lab_test_id.' - '.$sample_type.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $ls_action;
						$review_lab_arr['Old_Value'] = $row_ls['sample_comments'];
						$review_lab_arr['New_Value'] = $sample_comments;
						$arrReview_lab[] = $review_lab_arr;
						$cls_review->reviewMedHx($arrReview_lab,$_SESSION['authId'],"Lab",$_SESSION['patient'],0,0);
					}
				}
			}
        	}
            
            
			for($i=1;$i<=$request['result_cont'];$i++){
				if($request['observation_'.$i]!=""){
					$row_lore = '';
					if($request['result_id_'.$i]>0){
						$result_id=$request['result_id_'.$i];
						
						$qry = "select * from  lab_observation_result where id  =".$result_id;
						$res = imw_query($qry);
						$row_lore = imw_fetch_assoc($res);
						
						$lore_action = "update";
						$result_qry="update lab_observation_result";
						$result_whr_qry=" where id='$result_id'";
						$result_entered_by_qry="";
					}else{
						$result_id="";
						$result_whr_qry="";
						$result_qry="insert into lab_observation_result";
						$result_entered_by_qry=" ,result_entered_by='$operator_id',result_entered_date='$enter_date'";
					}
					$observation=$request['observation_'.$i];
					$result_loinc=$request['result_loinc_'.$i];
					$result=$request['result_'.$i];
					$uom=$request['uom_'.$i];
					$result_range=$request['result_range_'.$i];
					$abnormal_flag=htmlentities($request['abnormal_flag_'.$i]);
					$status=$request['status_'.$i];
					$result_date=getDateFormatDB($request['result_date_'.$i]);
					$result_time=$request['result_time_'.$i];
					$result_comments=$request['result_comments_'.$i];
					$result_qry.=" set lab_test_id='$lab_test_id',observation='$observation',result_loinc='$result_loinc',result='$result',uom='$uom',result_range='$result_range',
					abnormal_flag='$abnormal_flag',status='$status',result_date='$result_date',result_time='$result_time',
					result_comments='$result_comments' $result_entered_by_qry $result_whr_qry";
					imw_query($result_qry);
					$counter = ($counter+imw_affected_rows());
					if($result_id<=0){
						$lore_action = "add";
						$result_id=imw_insert_id();
					}
					if($this->policy_status == 1){
						$arrLORE_fld = make_field_type_array("lab_observation_result");
						$arrReview_lab = array();
						$review_lab_arr = array();		
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['observation_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "observation";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"observation");
						$review_lab_arr['Field_Text'] = 'Observation - (ID#'.$lab_test_id.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['observation'];
						$review_lab_arr['New_Value'] = $observation;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_loinc_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result_loinc";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result_loinc");
						$review_lab_arr['Field_Text'] = 'Observ Loinc - (ID#'.$lab_test_id.'-'.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result_loinc'];
						$review_lab_arr['New_Value'] = $result_loinc;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result");
						$review_lab_arr['Field_Text'] = 'Observ Result - (ID#'.$lab_test_id.'-'.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result'];
						$review_lab_arr['New_Value'] = $result;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['uom_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "uom";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"uom");
						$review_lab_arr['Field_Text'] = 'Observ UOM - (ID#'.$lab_test_id.'-'.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['uom'];
						$review_lab_arr['New_Value'] = $uom;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_range_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result_range";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result_range");
						$review_lab_arr['Field_Text'] = 'Observ Range - (ID#'.$lab_test_id.'-'.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result_range'];
						$review_lab_arr['New_Value'] = $result_range;
						$arrReview_lab[] = $review_lab_arr;
						
						$abnormal_flag_old=$abnormal_flag_new="";
						if(trim($row_lore['abnormal_flag'])!="") { $abnormal_flag_old = html_entity_decode($row_lore['abnormal_flag']).' - '.$get_abnormal_flag_arr[$row_lore['abnormal_flag']];}
						if(trim($abnormal_flag)!="") 			 { $abnormal_flag_new = html_entity_decode($abnormal_flag).' - '.$get_abnormal_flag_arr[$abnormal_flag];}
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['abnormal_flag_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "abnormal_flag";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"abnormal_flag");
						$review_lab_arr['Field_Text'] = 'Observ Abn Flg - (ID#'.$lab_test_id.'-'.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $abnormal_flag_old;
						$review_lab_arr['New_Value'] = $abnormal_flag_new;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['status_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "status";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"status");
						$review_lab_arr['Field_Text'] = 'Observ Status - (ID#'.$lab_test_id.'-'.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['status'];
						$review_lab_arr['New_Value'] = $status;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_date_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result_date";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result_date");
						$review_lab_arr['Field_Text'] = 'Observ Res Date - (ID#'.$lab_test_id.'-'.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result_date'];
						$review_lab_arr['New_Value'] = $result_date;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_time_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result_time";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result_time");
						$review_lab_arr['Field_Text'] = 'Observ Res Time - (ID#'.$lab_test_id.'-'.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result_time'];
						$review_lab_arr['New_Value'] = $result_time;
						$arrReview_lab[] = $review_lab_arr;
					
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_loinc_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result_loinc";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result_loinc");
						$review_lab_arr['Field_Text'] = 'Observ Loinc - (ID#'.$lab_test_id.' - '.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result_loinc'];
						$review_lab_arr['New_Value'] = $result_loinc;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result");
						$review_lab_arr['Field_Text'] = 'Observ Result - (ID#'.$lab_test_id.' - '.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result'];
						$review_lab_arr['New_Value'] = $result;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['uom_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "uom";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"uom");
						$review_lab_arr['Field_Text'] = 'Observ UOM - (ID#'.$lab_test_id.' - '.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['uom'];
						$review_lab_arr['New_Value'] = $uom;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_range_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result_range";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result_range");
						$review_lab_arr['Field_Text'] = 'Observ Range - (ID#'.$lab_test_id.' - '.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result_range'];
						$review_lab_arr['New_Value'] = $result_range;
						$arrReview_lab[] = $review_lab_arr;
						
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['status_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "status";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"status");
						$review_lab_arr['Field_Text'] = 'Observ Status - (ID#'.$lab_test_id.' - '.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['status'];
						$review_lab_arr['New_Value'] = $status;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_date_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result_date";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result_date");
						$review_lab_arr['Field_Text'] = 'Observ Res Date - (ID#'.$lab_test_id.' - '.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result_date'];
						$review_lab_arr['New_Value'] = $result_date;
						$arrReview_lab[] = $review_lab_arr;
						
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_time_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result_time";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result_time");
						$review_lab_arr['Field_Text'] = 'Observ Res Timee - (ID#'.$lab_test_id.' - '.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $this->getTimeFormatAudit($row_lore['result_time']);
						$review_lab_arr['New_Value'] = $this->getTimeFormatAudit($result_time);;
						$arrReview_lab[] = $review_lab_arr;
					
						$review_lab_arr = array();	
						$review_lab_arr['Pk_Id'] = $result_id;
						$review_lab_arr['Table_Name'] = 'lab_observation_result';
						$review_lab_arr['UI_Filed_Name'] = $_POST['result_comments_'.$i];
						$review_lab_arr['Data_Base_Field_Name']= "result_comments";
						$review_lab_arr['Data_Base_Field_Type']= fun_get_field_type($arrLORE_fld,"result_comments");
						$review_lab_arr['Field_Text'] = 'Observ Res Time - (ID#'.$lab_test_id.' - '.$observation.')';
						$review_lab_arr['Operater_Id'] = $_SESSION['order_by'];
						$review_lab_arr['Action'] = $lore_action;
						$review_lab_arr['Old_Value'] = $row_lore['result_comments'];
						$review_lab_arr['New_Value'] = $result_comments;
						$arrReview_lab[] = $review_lab_arr;
						$cls_review->reviewMedHx($arrReview_lab,$_SESSION['authId'],"Lab",$_SESSION['patient'],0,0);
					}
				}
			}
			
			if($lab_test_id>0){
				$qry_chk = "select * from  lab_observation_result where lab_test_id  ='".$lab_test_id."' and del_status='0' and result!=''";
				$res_chk = imw_query($qry_chk);
				if(imw_num_rows($res_chk)>0){
					imw_query("update lab_test_data set lab_status='2' where lab_test_data_id='$lab_test_id'");
				}else{
					imw_query("update lab_test_data set lab_status='1' where lab_test_data_id='$lab_test_id'");
				}
			}
            
            if(isDssEnable() && $row_ltd['dss_lab_order_number'] == "") {
                $sqlrs=imw_query('select External_MRN_5 from patient_data where id='.$patient_id.' ');
                $dss_row=imw_fetch_assoc($sqlrs);
                $dss_dfn=$dss_row['External_MRN_5'];

                if(empty($dss_dfn)==false && $dss_dfn!=NULL && $dss_dfn!='') {
                    include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
                    $obj = new Dss_medical_hx();
                    
                    try
                    {
                        $dss_collection_type=$_REQUEST['dss_collection_type'];
                        $dss_urgency=$_REQUEST['dss_urgency'];
                        $dss_schedules=$_REQUEST['dss_schedules'];
                
                        $dss_loginDUZ='';
                        if(isset($_SESSION['dss_loginDUZ']) && $_SESSION['dss_loginDUZ']!='')
                            $dss_loginDUZ=$_SESSION['dss_loginDUZ'];

                        $dss_location='';
                        if(isset($_SESSION['dss_location']) && $_SESSION['dss_location']!='')
                            $dss_location=$_SESSION['dss_location'];

                        $labId = '';
                		$sql_lab = imw_query("SELECT `dss_lab_id` FROM `lab_radiology_tbl` WHERE `lab_radiology_name` = '".$request['service_1']."'");
						if(imw_num_rows($sql_lab) > 0) {
							$result = imw_fetch_assoc($sql_lab);
							$labId = $result['dss_lab_id'];
						} else {
							throw new Exception( 'Error : Lab id not found.' );
						}

                        $params=array();
                        $params['patient'] = $dss_dfn;
                        $params['provider'] = $dss_loginDUZ;
                        $params['location'] = $dss_location;
                        $params['lab'] = $labId;
                        $params['sample'] = $dss_sample_id;
                        $params['specimen'] = $dss_collection_id;
                        $params['urgency'] = $dss_urgency;
                        $params['orderPrompt'] = array();
                        $params['collection'] = $dss_collection_type;
                        $params['collectionDt'] = "NOW";
                        $params['schedule'] = $dss_schedules;
                        $params['duration'] = "0";
                        $params['orderChecks'] = array();

                        $result=$obj->LabSave($params);
                        if(isset($result[0]['code']) && $result[0]['code']=='1'){
                            $orderNumber=$result[0]['orderNumber'];
                            $oNumber = explode(';', $orderNumber);
							$sql = "update lab_test_data set dss_lab_order_number='".preg_replace("/[^0-9]/", "", $oNumber[0])."' where lab_test_data_id='$lab_test_id'";
                            imw_query($sql);
                        } else {
                            throw new Exception( 'Error : '.$result[0]['message'] );
                        }
                    } catch (Exception $e) {
                        echo '<script>top.fAlert("'.$e->getMessage().'","", \'top.fmain.location.href="../Medical_history/index.php?showpage=lab"\' );</script>';
                        die;
                    }
                }
            }
		}
		return $counter;
	}
	Public function getTimeFormatAudit($tm) {
		$tm_audit = $tm;
		if(count(explode(":",$tm))==3){
			$tm_audit=date("h:i A",strtotime($tm)); 
		}
		if($tm=="00:00:00") { $tm_audit="";}
		return $tm_audit;	
	}

	// Returns phy id based on name
	public function getPhyIdByName($name){
		if(!$name) return 0;

		$providerArr = $this->get_providers_typeahead_arr();
		return ($providerArr['phyId'][$name]) ? $providerArr['phyId'][$name] : 0;
	}


	
	
}
?>