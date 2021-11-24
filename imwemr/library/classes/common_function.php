<?php
//array that hold appt status
$CommonAppStatusArr[0]='New/Restored';
$query=imw_query("select id,status_name from schedule_status where status=1");
while($data=imw_fetch_object($query))
{
	$CommonAppStatusArr[$data->id]=$data->status_name;
}

$common_order_type = array("1"=>"Meds", "2"=>"Labs", "3"=>"Imaging/Rad", "4"=>"Procedure/Sx",  "5"=>"Information/Instructions");

//function for debuging array to get result in pre tag
function pre($arr, $debug = 0){
	print "<pre>";
	print_r($arr);
	print "</pre>";
	if($debug == 1){
		die("Debugging");
	}
}

/*XSS Protection*/
function xss_rem($string, $method="1", $action="reject"){
	$matches = array();
	$response = "";
	if($string!="" && $method!=""){
		$allow = (string)$method;
		$regex = "";
		/*Selecting Regex on the basic of method selected*/
		switch($method){
			case "1":
				/*blacklisting*/
				$regex = '/(?:\(|\)|"|\/|\%|<|>|<script>|<script|script>|{|}|\=|\;|\')/';
			break;
			case "2":
				/*whitelisting*/
				$regex = '/[^\w\d\-\.\$]+/';
			break;
			case "3":
				/*whitelisting - Numeric Only*/
				$regex = '/[^\d$]+/';
			break;
			case "4":
				/**
				 * Blacklisting
				 * This is identical to case "1":
				 * It only allows slashes and parenthesis from the list of blacklisted characters in case "1"
				 */
				$regex = '/(?:"|\%|<|>|<script>|<script|script>|{|}|\=|\;|\')/';
			break;
		}

		/*Perform action as per input prvided*/
		if($regex!=""){
			if($action=="reject"){
				preg_match_all($regex, $string, $matches);
				$response = ((count($matches[0])>0)?"":$string);
			}
			elseif($action=="sanitize"){
				$response = preg_replace($regex, "", $string);
			}
		}

	}
	return ($response);
}

/*To get info array of browser*/
function get_browser_info(){
	$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if(stristr($browser, 'ipad') == true){
		return 'ipad';
	}else if(stristr($browser, 'MSIE') == true){
		return 'ie';
	}else if(stristr($browser, 'Safari') == true){
		return 'safari';
	}else if(stristr($browser, 'Chrome') == true){
		return 'chrome';
	}
}
/*--GETTING BROWSER INFO--*/
$GLOBALS['gl_browser_name'] = get_browser_info();
// ----------- This function return date format to be used in sql query in date_format function
function get_sql_date_format($date_format='',$yearL='Y',$separator=''){
	$date_format = $date_format == '' ? inter_date_format() : $date_format;
	$separator = ($separator == "")?get_separator_inter($date_format):$separator;
	$date_format = str_replace("-",$separator,$date_format);
	$yearL = ($yearL!="")?$yearL:"Y";
	switch($date_format){
		case "yyyy".$separator."dd".$separator."mm":
			$sqlFormat = '%'.$yearL.$separator.'%d'.$separator.'%m';
		break;
		case "yyyy".$separator."mm".$separator."dd":
			$sqlFormat = '%'.$yearL.$separator.'%m'.$separator.'%d';
		break;
		case "mm".$separator."dd".$separator."yyyy":
			$sqlFormat = '%m'.$separator.'%d'.$separator.'%'.$yearL;
		break;
		case "dd".$separator."mm".$separator."yyyy":

			$sqlFormat = '%d'.$separator.'%m'.$separator.'%'.$yearL;
		break;
		default:
			$sqlFormat = '%m'.$separator.'%d'.$separator.'%'.$yearL;
	}
	return $sqlFormat;
}

function get_date_arr($date, $in_format){

	$in_format = $in_format != ''? $in_format : 'yyyy-mm-dd';
	$separator = get_separator_inter($date);
	$arr_date = explode($separator, $date);
	switch($in_format){
			case "dd".$separator."mm".$separator."yyyy":
				$dd = $arr_date[0];
				$mm = $arr_date[1];
				$yy = $arr_date[2];
			break;
			case "mm".$separator."dd".$separator."yyyy":
				$mm = $arr_date[0];
				$dd = $arr_date[1];
				$yy = $arr_date[2];
			break;
			case "yyyy".$separator."dd".$separator."mm":
				$yy = $arr_date[0];
				$dd = $arr_date[1];
				$mm = $arr_date[2];
			break;
			default: //----yyyy-mm-dd
				$yy = $arr_date[0];
				$mm = $arr_date[1];
				$dd = $arr_date[2];
		}
		return array($yy,$mm,$dd);
}

//----This function takes date and return date in desired format
function get_date_format($date='',$in_format = '',$out_format='',$year_len=4,$separator=''){
	$date_exp=explode(' ',$date);
	$date=$date_exp[0];
	$ArrgetDate = get_date_arr($date,$in_format);
	$yy = $ArrgetDate[0];
	$mm = $ArrgetDate[1];
	$dd = $ArrgetDate[2];
	$date = preg_replace('/[^0-9]/','',$date);
	$date = substr($date,0,10);
	$date_result = '';
	$sub_str_index = ($year_len == 4) ? '0' : '2';
	if(empty($date) == false && $date != '00000000'){
		$out_format = $out_format != ''? $out_format : inter_date_format();
		$separator = ($separator=="")?get_separator_inter($out_format):$separator;
		$out_format = str_replace("-",$separator,$out_format);
		switch($out_format){
			case "dd".$separator."mm".$separator."yyyy":
				$date_result = $dd.$separator.$mm.$separator.substr($yy,$sub_str_index);
			break;
			case "yyyy".$separator."mm".$separator."dd":
				$date_result = substr($yy,$sub_str_index).$separator.$mm.$separator.$dd;
			break;
			case "yyyy".$separator."dd".$separator."mm":
				$date_result = substr($yy,$sub_str_index).$separator.$dd.$separator.$mm;
			break;
			default://--mm-dd-yyyy
				$date_result = $mm.$separator.$dd.$separator.substr($yy,$sub_str_index);
			break;

		}
	}
	return $date_result;
}

function get_separator_inter($date){
		$separator = "-";
		if(strpos($date,'/')!==false) { $separator = "/";}
		else if(strpos($date,'-')!==false){ $separator = "-";}
		else if(strpos($date,'\\')!==false){$separator = "\\";}
		return $separator;
}

/*******************************************
*
* Function make_field_type_array returns
* a array of field names of $table with their type
*
*********************************************/

function make_field_type_array($table)
{
	$query=	"select * from ".$table." LIMIT 0 , 1";
	$sql	=	imw_query($query);
	if(!$sql){
		echo ("Error : ".imw_error());
	}
	$totDataFields = imw_num_fields($sql);
	$fieldData = imw_fetch_fields($sql);
	$dataFields = array();

	if(is_array($fieldData) && count($fieldData) > 0)
	{
		$mysql_data_type_hash = array(
			1=>'tinyint',
			2=>'smallint',
			3=>'int',
			4=>'float',
			5=>'double',
			7=>'timestamp',
			8=>'bigint',
			9=>'mediumint',
			10=>'date',
			11=>'time',
			12=>'datetime',
			13=>'year',
			16=>'bit',
			//252=>'text',
			252=>'blob', //text and blob returns same value 252
			253=>'string', //varchar
			254=>'char',
			246=>'decimal'
		);

		foreach($fieldData as $field)
		{
			foreach ($mysql_data_type_hash as $key => $value) {
				if($key == $field->type){
					$field->type = $value;
					$dataFields[] = array( "DB_Field_Name"=> $field->name,
														 "DB_Field_Type"=> $value);
				}
			}

		}
	}
	if (count($dataFields)>0){
		return $dataFields;
	}
	else{
		return imw_errno();
	}

}

// perportion Image
function show_thumb_image($file_name,$targetWidth=1,$targetHeight=1)
{
	//if(file_exists($file_name ))
	{
		 $img_size	=	getimagesize($file_name);
		 $width			=	$img_size[0];
		 $height		=	$img_size[1];

		 do
		 {
			 if($width > $targetWidth)
			 {
				$width	=	$targetWidth;
				$percent=	$img_size[0]/$width;
				$height	=	$img_size[1]/$percent;
			 }
			 if($height > $targetHeight)
			 {
				$height	=	$targetHeight;
				$percent=	$img_size[1]/$height;
				$width	=	$img_size[0]/$percent;
			 }

		 }while($width > $targetWidth || $height > $targetHeight);

		 return "<img src='".$file_name."' width='".$width."' height='".$height."'>";
	 }
}

// Name Format
// $display_dot = 'yes'/'no' Yes means dot will display with middle name.
function core_name_format($lname, $fname, $mname = "", $pfx = "", $sfx = "", $display_dot='yes')
{
		$return = "";
		if($lname != "" && $fname != "" && $mname != ""){
			$dot_val=($display_dot=='yes')? "." : "";
			$return .= $lname.", ".$fname." ".substr($mname, 0, 1).$dot_val;
		}else if($lname != "" && $fname != ""){
			$return .= $lname.", ".$fname;
		}else if($lname != ""){
			$return .= $lname;
		}
		if($pfx != ""){
			$return = $pfx." ".$return;
		}
		if($sfx != ""){
			$return = $return." ".$sfx;
		}
		return $return;
	}

//to check single / multiple privileges for the logged on user
function core_check_privilege($arr_priv_names, $any_or_all = "all"){ //"any"
	$bl_is_privileged = false;
	if(is_array($arr_priv_names) && count($arr_priv_names) > 0){
		if($any_or_all == "all"){
			foreach($arr_priv_names as $this_priv_name){
				$bl_is_privileged = false;
				if(isset($_SESSION["sess_privileges"][$this_priv_name]) && $_SESSION["sess_privileges"][$this_priv_name] == 1){
					$bl_is_privileged = true;
				}
			}
		}else if($any_or_all == "any"){
			foreach($arr_priv_names as $this_priv_name){
				if(isset($_SESSION["sess_privileges"][$this_priv_name]) && $_SESSION["sess_privileges"][$this_priv_name] == 1){
					$bl_is_privileged = true;
					break;
				}
			}
		}
	}
	return $bl_is_privileged;
}

if( !function_exists('core_phone_format') )
{
	function core_phone_format($phone_number,$format=''){
		$return = "";
		$default_format = $format!=''? $format : $GLOBALS['phone_format'];
		$refined_phone = $default_format == '' ? $phone_number : preg_replace('/[^0-9]/','',$phone_number);


		switch($default_format){
			case "###-###-####"://-------1
				$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $refined_phone);
				break;
			case "(###) ###-####"://-------2
				$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $refined_phone);
				break;
			case "(##) ###-####"://-------3
				$return = preg_replace("/([0-9]{2})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $refined_phone);
				break;
			case "(###) ###-###"://-------4
				$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{3})/", "($1) $2-$3", $refined_phone);
				break;
			case "(####) ######"://-------5
				$return = preg_replace("/([0-9]{4})([0-9]{6})/", "($1) $2", $refined_phone);
				break;
			case "(####) #####"://-------6
				$return = preg_replace("/([0-9]{4})([0-9]{5})/", "($1) $2", $refined_phone);
				break;
			case "(#####) #####"://-------7
				$return = preg_replace("/([0-9]{5})([0-9]{5})/", "($1) $2", $refined_phone);
				break;
			case "(#####) ####"://-------8
				$return = preg_replace("/([0-9]{5})([0-9]{4})/", "($1) $2", $refined_phone);
				break;
			default:
				$return = $refined_phone;
				break;
		}
		return $return;
	}
}

function core_currency_format($value, $format){
	$value = $value > 0 ? '$'.number_format($value,$format) : '';
	return $value;
}

//Returns a row requested from table
function get_extract_record($table,$where_field,$where_value, $field_name = '', $extra_condition='', $order_by = '', $sort_order = 'ASC')
{
	$field_name	=	trim($field_name);
	$field_name	=	empty($field_name) ? '*' : $field_name ;

	if($where_field && $order_by){
		$query = "SELECT ".$field_name." FROM ".$table." WHERE ".$where_field." = '".$where_value."' ".$extra_condition." ORDER BY ".$order_by." ". $sort_order;
	}else{
		$query = "SELECT ".$field_name." FROM ".$table." WHERE ".$where_field." = '".$where_value."' ".$extra_condition;
	}

	$sql = imw_query($query);
	$cnt = imw_num_rows($sql);
	if($cnt)
	{
			$row = imw_fetch_assoc($sql);
			return $row;
	}

}

function get_array_records($table,$where_field,$where_value, $field_name = '', $extra_condition='', $order_by = '', $sort_order = 'ASC')
{
	$return = array();
	$field_name	=	trim($field_name);
	$field_name	=	empty($field_name) ? '*' : $field_name ;

	if($where_field && $order_by){
		$query = "SELECT ".$field_name." FROM ".$table." WHERE ".$where_field." = '".$where_value."' ".$extra_condition." ORDER BY ".$order_by." ". $sort_order;
	}else{
		$query = "SELECT ".$field_name." FROM ".$table." WHERE ".$where_field." = '".$where_value."' ".$extra_condition;
	}

	$sql = imw_query($query);
	$cnt = imw_num_rows($sql);
	if($cnt > 0 )
	{
		while( $row = imw_fetch_assoc($sql))
		{
			$return[] = $row;
		}
	}
	return $return;
}

/*
function: get_relationship_array
purpose: to centralize relationship drop-down values
author: TS
arguments: section name (i.e. general_health, social_history )
return: array.
*/
function get_relationship_array($section='')
{
	$arr_relationship =  array("", "Brother","Daughter","Father","Mother","Sister","Son","Spouse");
	$arr_relationship2 = array("",'self','Father','Mother','Son','Daughter','Spouse','Guardian','POA',"Friend","Aunt","Aunt/Uncle","Brother/Sister","Child:No Fin Responsibility","Dep Child:Fin Responsibility","Donor Live","Donor-Dceased","Employee","Foster Child","Grand Child","Grandparent","Handicapped Dependant","Injured Plantiff","Inlaw","Legal Guardian","Minor Dependent Of a Dependent","Niece/Nephew","Relative","Sponsored Dependent","Step Child","Student","Ward of The Court");

	asort($arr_relationship);
	asort($arr_relationship2);

	switch($section){
		case 'general_health':
			array_push($arr_relationship,"Grandmother");
			array_push($arr_relationship,"Grandfather");
			array_push($arr_relationship,"Other");
			$arr_Rel = array();
			foreach($arr_relationship as $val){
				$arr_Rel[$val] = array($val,'',$val);
			}
			return $arr_Rel;
			break;

		case 'social_history':
			array_push($arr_relationship,"Other");
			return $arr_relationship;
			break;

		case 'emergency_relation':
			array_push($arr_relationship2,"Husband");
			array_push($arr_relationship2,"Wife");
			array_push($arr_relationship2,"Significant Other");
			asort($arr_relationship2);
			array_push($arr_relationship2,"Other");
			return $arr_relationship2;
			break;

		case 'hipaa_relation':
			$arr_hipaa_rel = array_unique(array_merge($arr_relationship,$arr_relationship2));
			$spouse = array_search('Spouse',$arr_hipaa_rel); if($spouse >= 0){unset($arr_hipaa_rel[$spouse]);}
			array_push($arr_hipaa_rel,"Foster Parents");
			array_push($arr_hipaa_rel,"Fin. Guarantor");
			array_push($arr_hipaa_rel,"Surrogate");
			array_push($arr_hipaa_rel,"Parent");

			array_push($arr_hipaa_rel,"Husband");
			array_push($arr_hipaa_rel,"Wife");
			array_push($arr_hipaa_rel,"Significant Other");
			asort($arr_hipaa_rel);
			array_push($arr_hipaa_rel,"Other");
			return $arr_hipaa_rel;
			break;

		case 'insurance':
			array_push($arr_relationship2,"Employee");
			array_push($arr_relationship2,"Husband");
			array_push($arr_relationship2,"Wife");
			array_push($arr_relationship2,"Significant Other");
			asort($arr_relationship2);
			array_push($arr_relationship2,"Other");
			return $arr_relationship2;
			break;
		case 'MUR_firstDegree':
			return $arr_relationship;
			break;
		default:
			//do nothing..
	}
	return $arr_relationship;
}

/* Function internal functions **/
function inter_state_label()
{
	return $GLOBALS['state_label'] ? $GLOBALS['state_label'] : 'state';
}

function inter_state_val()
{
	return $GLOBALS['state_val'] ? $GLOBALS['state_val'] : 'abb';
}

function inter_state_length()
{
	return $GLOBALS['zip_length'] == 8 ? '' : 2;
}

function inter_zip_length()
{
	return $GLOBALS['zip_length'] ? $GLOBALS['zip_length'] : '5';
}

function inter_zip_type()
{
	return isset($GLOBALS['zip_type']) ? $GLOBALS['zip_type'] : 'numeric';
}

function inter_zip_ext()
{
	return (($GLOBALS['zip_ext'] || !isset($GLOBALS['zip_ext'])) ? true : false);
}

function inter_phone_length()
{
	return isset($GLOBALS['phone_length']) ? $GLOBALS['phone_length'] : 10;
}

function inter_country()
{
	return ((isset($GLOBALS['currency']) && $GLOBALS['currency'] == "&pound;") ? 'UK' : 'USA');
}

function inter_date_format()
{
	return $GLOBALS['date_format'] ? $GLOBALS['date_format'] : 'mm-dd-yyyy';
}

function inter_ssn_length(){
	return isset($GLOBALS['ssn_length']) ? $GLOBALS['ssn_length'] : '9';
}

function inter_ssn_format(){
	return isset($GLOBALS['ssn_format']) ? $GLOBALS['ssn_format'] : '###-##-####';
}

function inter_ssn_reg_exp_js(){
	return isset($GLOBALS['ssn_reg_exp_js']) ? $GLOBALS['ssn_reg_exp_js'] : '[^0-9\-+]';
}
//fuction to return currency symbol
function show_currency(){
	if(isset($GLOBALS['currency']) && trim($GLOBALS['currency']) != '')
	return $GLOBALS['currency'];
	else
	return '$';
}


function numberformat($value=0,$format,$show_zero='yes',$currency='',$show_currency='',$remove_comma=''){
		$currency = $currency!="" ? $currency : show_currency();
		if($show_currency=="no"){$currency="";}
		$value=trim(str_replace(',','',$value));
		$value=str_replace('$','',$value);
		$value=str_replace(' ','',$value);
		$value = number_format($value, $format);

		if($remove_comma!=""){
			$value = preg_replace("/,/","",$value);
		}

		if($value > 0){
			$value = $currency.$value;
		}else if($value < 0){
			$value = str_replace('-', '-'.$currency, $value);
		}else{
			if($show_zero!="yes"){
				$value = NULL;
			}else{
				$value = preg_replace("/,/","",$value);
				if(empty($value)===true){
					$value='0.00';
				}
				$value = $currency.$value;
			}
		}
		return $value;
}

function get_patient_last_appointment($patient_id)
{
		$return = array();
		$qry	=	"SELECT sa_doctor_id , sa_facility_id FROM schedule_appointments
							WHERE sa_patient_app_status_id not in (201, 18, 203, 19, 20)
							AND sa_patient_id = '".$patient_id."'
							AND sa_app_start_date <= now()
							ORDER BY sa_app_start_date desc, sa_app_starttime DESC LIMIT 0, 1";
		$sql	=	imw_query($qry);
		$cnt	=	imw_num_rows($sql);
		while($row = imw_fetch_assoc($sql))
		{
			$return[] = $row;
		}

		return $return;
	}

function get_facility_details($id = '0')
{
	$qry	=	"SELECT * FROM facility ";
	if($id == '0'){
		$qry .= "WHERE facility_type  = '1' LIMIT 1";
	}else{
		$qry .= "WHERE id = '".$id."'";
	}
	$sql	= imw_query($qry);
	if(imw_num_rows($sql) > 0)
	{
		$row	= imw_fetch_assoc($sql);
	}
	return $row;
}

function fun_get_field_type($data_field_array,$field)
{
	if($data_field_array[0] != ''){
		foreach($data_field_array as $key => $value){
			if($data_field_array [$key]["DB_Field_Name"] == trim($field)){
				return $data_field_array [$key]["DB_Field_Type"];
			}
		}
	}
}

//to get patient status list - returns false if no result else returns array of status
function core_pt_status_list($order_by = "pt_status_show_seq_id")
{
	$fields	=	'pt_status_id, pt_status_name, pt_status_search_bl';
	$rows	=	get_array_records('patient_status_tbl','1','1',$fields,' AND pt_status_hide_bl = 0',$order_by);
	$return =	(count($rows) > 0 ) ? $rows : false;
	return $return;
}

//to get select options from a give 2 dimensional array
function core_make_select_options($arr_options, $value_name, $label_name, $val_to_match = "")
{
	$str_return = "";
	if(is_array($arr_options) && count($arr_options) > 0)
	{
		foreach($arr_options as $this_option)
		{
			$str_return .= "<option value=\"".$this_option[$value_name]."\" ".(($val_to_match == $this_option[$value_name]) ? "selected": "").">".$this_option[$label_name]."</option>";
		}
	}
	return $str_return;
}

/*
Purpose : to clean input and output.
Returns : clean Data.
*/
function core_refine_user_input($input_val){
	return addslashes(htmlentities($input_val));
}
function core_extract_user_input($refined_input_val){
	return html_entity_decode(stripslashes($refined_input_val));
}

function show_age($dob){
	$yr="";
	if(!empty($dob)&&($dob!="0000-00-00")){
		$dob_time = strtotime($dob);
		$cur_time = strtotime(date("Y-m-d H:i:s"));
		$age_days = floor(($cur_time-$dob_time)/(60*60*24));
		if($age_days >= 365)
		{
			/*The auto text says 62 yrs old the physicians pet peeve the correct term is  yr old. not yrs.*/
			$yrs_tmp = $age_days/365.25;
			$yrs = floor($yrs_tmp);
			$age = $yrs." Yr.";
		}
		else if($age_days >= 30)
		{
			$months = floor($age_days/30);
			$age = $months." Mon.";
		}
		else if($age_days > 0)
		{
			$age = $age_days." Days";
		}
	}
	return $age;
}

function getDateFormatDB($date,$inFormat=''){ //-----This function takes date and return date in mysql format to be inserted in database
	$date = substr($date,0,10);
	$date_format = $inFormat != ''? $inFormat : inter_date_format();
	$separator = get_separator_inter($date);
	$old_format = explode($separator,$date);
	$date = preg_replace('/[^0-9]/','',$date);
	$date_result = '';
	$date = substr($date,0,8);

	if(empty($date) == false && $date != '00000000'){
		if(strlen(end($old_format)) == 2){
			switch($date_format){
				case "yyyy".$separator."dd".$separator."mm":
					$date_result = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/","$1-$3-$2",$date);
				break;
				default://---yyyy-mm-dd
					$date_result = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/","$1-$2-$3",$date);
			}
		}else{
			switch($date_format){
				case "dd".$separator."mm".$separator."yyyy":
					$date_result = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{4})/","$3-$2-$1",$date);
				break;
				default://---mm-dd-yyyy
					$date_result = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{4})/","$3-$1-$2",$date);
			}
		}
	}
	return $date_result;
}

function core_time_format($tm){
	list($hh, $mm) = explode(":", $tm);
	$a = 'AM';
	if($hh >=12){
		$hh = $hh > 12 ? $hh-12 : $hh;
		$a = 'PM';
	}
	return (int)$hh.':'.$mm.' '.$a;
}

function core_date_format($dt, $format = "m/d/Y"){
	$ArrDt = explode("-", $dt);
	$y	= $ArrDt[0];
	$m	= $ArrDt[1];
	$d	= $ArrDt[2];
	if(is_numeric($y) === true){
		return date($format, mktime(0, 0, 0, $m, $d, $y));
	}
}

function show_tooltip($content,$placement='right'){		// To show content in tooltip
	//Arguments =>  ('$content' => 'Content to show in tooltip','$placement' => 'Where to show tooltip [Left,Right,Top,Bottom]')
	$html = '';
	if($content != ''){
		$content=str_replace("'",'',$content);
		$content = '<p style="text-align:left">'.$content.'</p>';
		$html = "data-toggle='tooltip' data-html='true' data-placement='".$placement."' title='".htmlspecialchars_decode($content)."' data-container='body'";
	}
	return $html;
}

function show_popover($content,$placement='right', $trigger='click'){		// To show content in popover
	$html = '';
	if($content != ''){
		$content = '<div class="col-xs-12 pd0">'.$content.'</div>';
		$html = "data-toggle='popover' data-trigger='".$trigger."' data-html='true' data-placement='".$placement."' title='' data-content='".htmlspecialchars_decode($content)."'";
	}
	return $html;
}

function get_heard_about_list($pt_heard = '',$dis_value = false)
{
	$pt_heard = trim($pt_heard);
	$condition= " AND status='0' AND (for_all = 1 ".($pt_heard ? ($dis_value?' AND':' OR') ." heard_id='".$pt_heard."'" : "").")";
	$rows = get_array_records('heard_about_us','1','1', 'DISTINCT heard_options,heard_id', $condition, 'heard_options');
	return $rows;
}

function get_heard_about_suggestions($heard_id)
{
	$return = array();
	$rows	= get_array_records('heard_about_us_desc','heard_id',$heard_id, 'DISTINCT heard_desc');
	if(is_array($rows) && count($rows) > 0 )
	{
		foreach($rows as $row)
		{
			if($row['heard_desc'] <> '')
			{
				$row['heard_desc'] = str_replace('/','_',$row['heard_desc']);
				$return[] = $row['heard_desc'];
			}
		}
	}
	return $return;
}

function get_mandatory_fields($mode)
{
	$mode = trim($mode);
	$mode =	($mode === 'demographics' || $mode === 'insurance') ? $mode : '';
	if(!$mode) return false;

	$return = array();
	if($mode == "demographics")
	{
		$query = "SELECT heardAboutUs as elem_heardAbtUs, name as fname, name as lname, ptMaritalStatus as status, pt_title as title,
										ptSex as sex, ptDOB as dob, address_1 as street, zip as code, city as city, state as state,
										homePhone as phone_home, workPhone as phone_biz, mobilePhone as phone_cell, eMail as email,
										ptCreatedBy as created_by, drivingLicense as dlicence, ptEmergencyContactName as contact_relationship,
										ptEmergencyPhone as phone_contact, provider as providerID, ptReferringPhysician as elem_physicianName,
										ptReferringPhysician as primaryCarePhy, facility as default_facility, miscHippaLanguage as language,
										miscHippaInterpreter as interpretter, miscHippaRace as race, miscHippaEth as ethnicity, employer as ename,
										occupation as occupation, monthlyIncome as monthly_income, occEmpAddress1 as estreet,
										occZipCode as epostal_code, occCity as ecity, occState as estate, loginId as usernm, loginId as pass1,
										loginId as pass2,rPName as fname1, rPName as lname1, rPRelation as relation1, rPMaritalStatus as status1,
										rPDOB as dob1, rPSex as sex1, socialSec as ss1, rPDrivingLicense as dlicence1, rPAddress_1 as street1,
										resZip as rcode, resCity as rcity, resState as rstate, rPHomePhone as phone_home1,
										rPWorkPhone as phone_biz1, rPMobilePhone as phone_cell1, ptsocialSecurityNumber as ss
							FROM demographics_mandatory";
	}
	elseif($mode == "insurance")
	{
		$query	=	"SELECT InsPriProvider as insprovider1, InsPriPolicy as i1policy_number,InsPriGroup as i1group_number,
											InsPriPlanName as i1plan_name, InsPriCoPay as i1copay, InsPriGroup as i1subscriber_employer,
											InsPriCoPay as i1claims_adjustername, InsPriActDate as i1effective_date, InsPriExpDate as i1expiration_date,
											InsPriInsName as i1subscriber_fname,  InsPriInsName as lastName1, InsPriSubRelation as i1subscriber_relationship,
											InsPriSS as i1subscriber_ss, InsPriDOB as i1subscriber_DOB, InsPriSex as i1subscriber_sex,
											InsPriAddress as i1subscriber_street, InsPriZip as code1, InsPriCity as city1, InsPriSate as state1,
											InsPriPhone as i1subscriber_phone, InsPriRefPhysician as ref1_phy, InsPriRefStEffDate as eff1_date,
											InsPriRefEndEffDate as end1_date, InsPriRefVisits as no_ref1, InsPriRefReferral as reffral_no1, InsPriRefRefDate as reff1_date,
											InsPriRefNote as note1, InsSecProvider as insprovider2, InsSecPolicy as i2policy_number,
											InsSecPlanName as i2plan_name, InsSecGroup as i2group_number, InsSecCoPay as i2copay,
											InsSecGroup as i2subscriber_employer, InsSecCoPay as i2claims_adjustername, InsSecActDate as i2effective_date,
											InsSecExpDate as i2expiration_date, InsSecInsName as i2subscriber_fname, InsSecInsName as lastName2,
											InsSecSubRelation as i2subscriber_relationship, InsSecSS as i2subscriber_ss, InsSecDOB as i2subscriber_DOB,
											InsSecSex as i2subscriber_sex, InsSecAddress as i2subscriber_street, InsSecZip as code2, InsSecCity as city2,
											InsSecSate as state2, InsSecPhone as i2subscriber_phone, InsSecRefPhysician as ref2_phy,
											InsSecRefStEffDate as eff2_date, InsSecRefEndEffDate as end2_date, InsSecRefVisits as no_ref2,
											InsSecRefReferral as reffral_no2, InsSecRefRefDate as reff2_date, InsSecRefNote as note2,
											InsTerProvider as insprovider3, InsTerPolicy as i3policy_number, InsTerGroup as i3group_number,
											InsTerCoPay as i3copay, InsTerGroup as i3subscriber_employer, InsTerCoPay as i3copay,
											InsTerPlanName as i3plan_name, InsTerActDate as i3effective_date, InsTerExpDate as i3expiration_date,
											InsTerInsName as i3subscriber_fname, InsTerInsName as lastName3, InsTerSubRelation as i3subscriber_relationship,
											InsTerSS as i3subscriber_ss, InsTerDOB as i3subscriber_DOB, InsTerAddress as i3subscriber_street,
											InsTerZip as code3, InsTerCity as city3, InsTerSate as state3, InsTerPhone as i3subscriber_phone,
											InsTerRefPhysician as ref3_phy, InsTerRefStEffDate as eff3_date, InsTerRefEndEffDate as end3_date,
											InsTerRefVisits as no_ref3, InsTerRefReferral as reffral_no3, InsTerRefRefDate as reff3_date, InsTerRefNote as note3
							FROM demographics_mandatory";
	}
	$sql	= imw_query($query);
	$cnt	=	imw_num_rows($sql);
	if($sql && $cnt > 0)
	{
		$row = imw_fetch_assoc($sql);
		$row['fname'] = $row['lname'] = $row['insprovider1'] = 2;
		//$row = array_keys(array_filter($row));
	}
	return $row;
}

function get_age($dob)
{

	$sql = "SELECT DATEDIFF(CURRENT_DATE(),'$dob') AS differece";
	$result = imw_query($sql);
	$row = imw_fetch_array($result);
	$age = "";
	$ageDays = $row["differece"];
	if($ageDays >= 365)
	{
		$yrs_tmp = $ageDays/365.25;
		$yrs = floor($yrs_tmp);
		$age = $yrs." Yr.";
	}
	else if($ageDays >= 30)
	{
		$months = floor($ageDays/30);
		$age = $months." Mon.";
	}
	else if($ageDays > 0)
	{
		$age = $ageDays." Days";
	}
	return $age;
}
if(!function_exists("dateDiff")){
	function dateDiff($dformat, $endDate, $beginDate)
	{
		$date_parts1=explode($dformat, $beginDate);
		$date_parts2=explode($dformat, $endDate);
		$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
		$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
		return $end_date - $start_date;
	}
}
/*
Function: clean_patient_session
Purpose: to unset patient specific session
MUST call Before patient RESET.
*/
function clean_patient_session(){
	$arr = array("patient","new_casetype","pid","form_id","finalize_id","defSxView",
				 "PT_DOC_ALERT_STATUS","test2edit","document_scan_id","encounter_id","currentCaseid","patient_parent_server","flg_phy_view","PT_EDU_ALERT_STATUS","PT_EDU_ARRAY","PT_EDU_ALERT_ARRAY");
	foreach($arr as $key=>$val){
		$_SESSION[$val]="";
		$_SESSION[$val]=NULL;
		unset($_SESSION[$val]);
	}
}
// FUNTION TO GET ID OF ACCOUNT STATUS ELEMENTS
function get_account_status_id($statusName){
	$id=0;
	$qry="Select id from account_status WHERE LOWER(status_name)=LOWER('".$statusName."')";
	$rs=imw_query($qry);
	$res=imw_fetch_assoc($rs);
	$stsId= $res['id'];
	return $stsId;
}
// FUNTION TO GET ID OF ACCOUNT STATUS ELEMENTS
function get_all_account_status(){
	$arrPatAcctSts=array();
	$qry="Select * from account_status ORDER BY id";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrPatAcctSts[$res['id']] = $res['status_name'];
	}
	return $arrPatAcctSts;
}


/*--FUNCTION TO CHECK REF.PHY STATUS IS DELETED OR NOT--*/
function is_ref_phy_deleted($refid){
	$return = false;
	$q= "SELECT physician_Reffer_id FROM refferphysician WHERE physician_Reffer_id='".$refid."' AND delete_status='1'";
	$r = imw_query($q);
	if($r && imw_num_rows($r)==1){
		$return = true;
	}
	return $return;
}
/*--FUNCTION TO DISPLAY ADDRESS IN GLOBAL FORMAT--*/
function core_address_format($street, $street2, $city, $state, $postalCode){
	$fullAddress='';
	$prevStreet					=	'';
	$prevStreet2				=	'';
	$prevCityStateZip			=	'';
	if(trim($street) || trim($street2) || trim($city) || trim($state) || trim($postalCode)) {
		if(trim($street)) {
			$prevStreet		=	stripslashes($street).', ';
		}
		if(trim($street2)) {
			$prevStreet2		=	stripslashes($street2).', ';
		}
		if(trim($city) || trim($state) || trim($postalCode)) {
			$prevCityStateZip	=	stripslashes($city).', '.stripslashes($state).' '.stripslashes($postalCode).'<br>';
			$prevCityStateZip 	= 	trim($prevCityStateZip);
		}
		$fullAddress	=	trim($prevStreet.$prevStreet2.$prevCityStateZip);
	}
	return $fullAddress;
}
//function to return phone format
function inter_phone_format(){
	if(isset($GLOBALS['phone_format']))
	return $GLOBALS['phone_format'];
	else
	return '###-###-####';
}

//GET BALANCE DEPENDING ON GIVEN SEARCH CRITERIA
function getARBalMonthDue($chargeListId,$patientId,$field,$start,$end=''){

	if($field == "insuranceDue"){
		$qry = "select sum(patient_charge_list_details.pri_due)
								from patient_charge_list
								LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
								where patient_charge_list_details.del_status='0' and patient_charge_list.charge_list_id in($chargeListId)
								and patient_charge_list.patient_id in ($patientId)
								and patient_charge_list_details.pri_due>0
								and (DATEDIFF(NOW(),date_of_service)>=$start)";
		if($end != ''){
			$qry .= " AND (DATEDIFF(NOW(),date_of_service)<=$end)";
		}

		$qryId = imw_query($qry);

		if(imw_num_rows($qryId)>0){
			$pri_qryRes = imw_fetch_array($qryId);
		}

		$qry = "select sum(patient_charge_list_details.sec_due)
								from patient_charge_list
								LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
								where patient_charge_list_details.del_status='0' and patient_charge_list.charge_list_id in($chargeListId)
								and patient_charge_list.patient_id in ($patientId)
								and patient_charge_list_details.sec_due>0
								and IF(from_sec_due_date>0,(DATEDIFF(NOW(), from_sec_due_date)>=$start),
								(DATEDIFF(NOW(),date_of_service)>=$start))";
		if($end != ''){
			$qry .= " and IF(from_sec_due_date>0,(DATEDIFF(NOW(), from_sec_due_date)<=$end),
									(DATEDIFF(NOW(),date_of_service)<=$end))";
		}

		$qryId = imw_query($qry);

		if(imw_num_rows($qryId)>0){
			$sec_qryRes = imw_fetch_array($qryId);
		}

		$qry = "select sum(patient_charge_list_details.tri_due)
								from patient_charge_list
								LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
								where patient_charge_list_details.del_status='0' and patient_charge_list.charge_list_id in($chargeListId)
								and patient_charge_list.patient_id in ($patientId)
								and patient_charge_list_details.tri_due>0
								and IF(from_ter_due_date>0,(DATEDIFF(NOW(), from_ter_due_date)>=$start),
								(DATEDIFF(NOW(),date_of_service)>=$start))";
		if($end != ''){
			$qry .= " and IF(from_ter_due_date>0,(DATEDIFF(NOW(), from_ter_due_date)<=$end),
									(DATEDIFF(NOW(),date_of_service)<=$end))";
		}

		$qryId = imw_query($qry);

		if(imw_num_rows($qryId)>0){
			$tri_qryRes = imw_fetch_array($qryId);
		}

		$qryRes[]=$pri_qryRes[0]+$sec_qryRes[0]+$tri_qryRes[0];
	}
	else{
		$qry = "select sum(patient_charge_list_details.$field)
								from patient_charge_list
								LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
								where patient_charge_list_details.del_status='0' and patient_charge_list.charge_list_id in($chargeListId)
								and patient_charge_list.patient_id in ($patientId)
								and IF(from_pat_due_date>0,(DATEDIFF(NOW(), from_pat_due_date)>=$start),
								(DATEDIFF(NOW(),date_of_service)>=$start))";
		if($end != ''){
			$qry .= " and IF(from_pat_due_date>0,(DATEDIFF(NOW(), from_pat_due_date)<=$end),
									(DATEDIFF(NOW(),date_of_service)<=$end))";
		}

		$qryId = imw_query($qry);

		if(imw_num_rows($qryId)>0){
			$qryRes = imw_fetch_array($qryId);
		}
	}

	return $qryRes;
}

function changeNameFormat($nameArr){
	$return_name = isset($nameArr['TITLE']) ? $nameArr['TITLE'] : '';
	//$return_name .= $nameArr['LAST_NAME'].', ';
	$return_name .= $nameArr['LAST_NAME'];
	if(stristr($nameArr['LAST_NAME'],',')===false) {
		$return_name .= ', ';
	}else {
		$return_name .= ' ';
	}
	$return_name .= $nameArr['FIRST_NAME'].' ';
	if(trim($nameArr['MIDDLE_NAME']) != ''){
		$return_name .= substr(trim($nameArr['MIDDLE_NAME']),0,1).".";
	}
	$return_name = ucfirst(trim($return_name));
	if($return_name[0] == ','){
		$return_name = substr($return_name,1);
	}
	return $return_name;
}
//function to remove any character other than number
function get_number($string){
	$num = preg_replace('/[^0-9]/','',$string);
	return $num;
}


//Common Bootstrap Modal Box
//Arguments
//div_id -> id that will be used for modal box. [string]
//header_cont -> title of the modal box. [string]
//body_cont -> content that will be shown in the modal box. [string]
//footer_cont -> content that will be shown in the footer of modal box. [string]
//size -> defines the size of modal box. [modal-lg / modal-sm / empty -> default size]

function show_modal($div_id, $header_cont = 'imwemr', $body_cont, $footer_cont, $height=300, $size=''){
	$modal_str='<!--modal to show appt history-->
        <div class="common_modal_wrapper">
             <!-- Modal -->
            <div id="'.$div_id.'" class="modal" role="dialog">
                <div class="modal-dialog '.$size.'">
                <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">
                                <span id="setTimeTitle">'.$header_cont.'</span>
                            </h4>
                        </div>
                        <div class="modal-body"><!-- style="max-height:'.$height.'px;overflow-x:auto;"-->
                            '.$body_cont.'
                        </div>
                        <div class="modal-footer ad_modal_footer" id="module_buttons">
							'.$footer_cont.'<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        <!--modal wrapper class end here -->';

	echo $modal_str;
}
function temp_key_gen($size = '6',$pid='') {
	$string = '';
	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for ($i = 0; $i < $size; $i++)
	{
		$string .= $characters[mt_rand(0, (strlen($characters) - 1))];
	}
	return $string;
}
// Generate Hash Password
function hashPassword($pass){
	if(!empty($pass)){
		if(HASH_METHOD){
			if(HASH_METHOD=='MD5' && !is_valid_md5($pass)){
				return md5($pass);
			}
			if((HASH_METHOD=='SHA1' || HASH_METHOD=='SHA2') && !is_valid_sha256($pass)){
				return hash('sha256',$pass);
			}else { return $pass; }
		}else{
			return '';//$pass;
		}
	}
}
/*BELOW TWO FUNCTIONS TO CHECK HASHED STRING (just checks string format; not actual checks that valid hash or not)*/
function is_valid_md5($pass =''){return preg_match('/^[a-f0-9]{32}$/', $pass);}
function is_valid_sha256($pass =''){return preg_match('/^[a-f0-9]{64}$/', $pass);}

// FUNTION TO GET ID's OF ACCOUNT STATUS ELEMENTS
function get_account_status_id_collections(){
	$qry="Select id from account_status WHERE LOWER(status_type)='collection'";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$tempArr[$res['id']]= $res['id'];
	}
	$collectionIds= implode(',', $tempArr);
	return $collectionIds;
}
function imageResize($width, $height, $target) {
	if ($width > $height) {
		$percentage = ($target / $width);
	} else {
		$percentage = ($target / $height);
	}
	$width = round($width * $percentage);
	$height = round($height * $percentage);
	return "width=\"$width\" height=\"$height\"";
}

//--- Get Image Size -----
function newImageResize($imageUrl,$new_width,$new_height='',$return_type=''){
	if(file_exists($imageUrl) && is_dir($imageUrl) == ''){
		$image_size = getimagesize($imageUrl);
		$image_width = $image_size[0];
		$image_height = $image_size[1];
	}
	if (($new_width!=0) && ($new_width<$image_width))
	{
		$image_height=(int)($image_height*($new_width/$image_width));
		$image_width=$new_width;
	}

	if (($new_height!=0) && ($new_height<$image_height))
	{
		$image_width=(int)($image_width*($new_height/$image_height));
		$image_height=$new_height;
	}
	if( trim($return_type) == 'array' )
		return array('width' => $image_width,'height' => $image_height);
	else
		return "width=\"$image_width\" height=\"$image_height\"";
}

// COMMON FUNCTION FOR ADDING RECORD IN DATABASE
function AddRecords($fieldName,$tableName,$boolEntities='true',$chk_dollar = true){
	$inserID=0;
	$fieldName = (array)$fieldName;
	if(is_array($fieldName)){
		foreach($fieldName as $key => $val){
			$chkStr = substr($key,0,3);
			$str2Int = substr($val,0,1);
			if($chkStr != 'txt'){
				if($str2Int == '$' && $chk_dollar)
				{
					$str2Int = substr($val,1,-1);
					$val = (float)$str2Int;
				}
				$fieldsName .= '`'.$key.'`,';
				if($boolEntities=='false') {
					$values .= "'".addslashes(trim($val))."',";
				}else {
					$values .= "'".htmlentities(addslashes(trim($val)))."',";
				}


			}
		}
		$fieldsName = substr($fieldsName,0,-1);
		$values = substr($values,0,-1);
		$insertQuery = "Insert into ".$tableName." ($fieldsName) Values ($values)";
		//print $insertQuery.'<br>';
		$qryId = imw_query($insertQuery) or die(imw_error());
		$inserID = imw_insert_id();
	}
	return $inserID;
}

// COMMON FUNCTION FOR UPDATING RECORD IN DATABASE
function UpdateRecords($id,$chkField,$fieldName,$tableName,$boolEntities='true',$chk_dollar = true){
	foreach($fieldName as $key => $val){
		$chkStr = substr($key,0,3);
		$str2Int = substr($val,0,1);
		if($chkStr != 'txt') {
			if($str2Int == '$' && $chk_dollar)
			{
				$str2Int = substr($val,1);
				$val = (float)$str2Int;
			}
			if($boolEntities=='false') {
				$setValues .= '`'.$key."`='".addslashes(trim($val))."',";
			}else {
				$setValues .= '`'.$key."`='".htmlentities(addslashes(trim($val)))."',";
			}
		}
	}
	$setValues = substr($setValues,0,-1);
	$insertQuery = "update $tableName set $setValues where $chkField = '$id'";
	$qryId = imw_query($insertQuery);
	
	//ERP API CALL
	$erp_error=array();
	if($qryId && isERPPortalEnabled()) {
		try {
			include_once($GLOBALS['srcdir']."/erp_portal/patient_allergies.php");
			$obj_allergy = new patient_allergies();
			$obj_allergy->deleteAllergy($id);
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
	}
	return $id;
}

/*
function: getRecords
purpose: to get a single record matching with particular id.
author: All
*/
function getRecords($tableName,$field,$id){
	$qry = "select * from $tableName where $field = $id";
	$qryId = imw_query($qry);
	if(imw_num_rows($qryId)==1){
		$qryRes = imw_fetch_array($qryId);
		return $qryRes;
	}else if(imw_num_rows($qryId)> 1){
		$array = array();
		while($rs = imw_fetch_array($qryId)){
			$array = $rs;
		}
		return $array;
	}
	return false;
}

function get_obj_query($tableName,$field,$id){
	$query = "SELECT * FROM ".$tableName." WHERE $field = '".$id."'";
	$qryRes = imw_query($query);
	if(imw_num_rows($qryRes)>0){
		$qryRowObj = imw_fetch_object($qryRes);
	}
	return $qryRowObj;
}
/*
function: verify_payment_method
purpose: to check as if this payment method is supported for the installation or not
author: AA
arguments: Payment method name as set in globals.php
return: true/false
*/
function verify_payment_method($pmethod = "MPAY"){
	global $arr_global_payment_opts;
	$return = false;
	if(is_array($arr_global_payment_opts) && count($arr_global_payment_opts) > 0){
		if(in_array($pmethod, $arr_global_payment_opts)){
			$return = true;
		}
	}
	return $return;
}
function get_icd10_desc($icd10,$len){
	$e11_arr=array("1"=>"PDR With ME - 1","2"=>"withTRD macula - 2","3"=>"with TRD no macula - 3","4"=>"with TRD and RD - 4","5"=>"with stable PDR - 5","9"=>"PDR without ME - 9");
	if($len>0){
		$len_cut=$len-3;
	}else{
		$len=10000;
	}
	$icd10_whr="";
	if($icd10!=""){
		$arr_uni_srch=array();
		if(count($icd10)>0){
			for($h=0;$h<=count($icd10);$h++){
				$icd10_exp=explode('.',$icd10[$h]);
				if(count($icd10_exp)>1){
                    $icd10_exp_str=trim($icd10_exp[0].'.'.substr($icd10_exp[1],0,1));
                }else{
                    $icd10_exp_str=trim($icd10_exp[0]);
                }
				if(!empty($icd10_exp_str) && $icd10_exp_str!="." && !in_array($icd10_exp_str, $arr_uni_srch)){
					$arr_uni_srch[]=$icd10_exp_str;
					$icd10_whr_arr[]=" icd10 like '".$icd10_exp_str."%' ";
				}
			}
			$icd10_whr=" and (".implode('or',$icd10_whr_arr).")";
		}else{
			$icd10_exp=explode('.',$icd10[$h]);
			if(count($icd10_exp)>1){
                $icd10_exp_str=trim($icd10_exp[0].'.'.substr($icd10_exp[1],0,1));
            }else{
                $icd10_exp_str=trim($icd10_exp[0]);
            }
			if(!empty($icd10_exp_str) && $icd10_exp_str!="." && !in_array($icd10_exp_str, $arr_uni_srch)){
				$arr_uni_srch[]=$icd10_exp_str;
				$icd10_whr=" and icd10 like '".$icd10_exp_str."%' ";
			}
		}
	}

	$qry_lat = "select * from icd10_laterality where deleted='0'";
	$res_lat = imw_query($qry_lat);
	while($row_lat = imw_fetch_array($res_lat)){
		$lat_id_arr[$row_lat['under']][$row_lat['code']]=$row_lat['id'];
		$lat_desc_arr[$row_lat['under']][$row_lat['code']]=$row_lat['title'];
		$lat_code_arr[$row_lat['under']][]=$row_lat['code'];
	}

	$icd10_desc_arr=array();

	$qry_dx = "select icd10,icd10_desc,laterality,staging,severity from icd10_data where icd10 !='' and deleted='0' $icd10_whr group by icd10";
	$res_dx = imw_query($qry_dx);
	while($row_dx = imw_fetch_array($res_dx)){
		$icd10_desc="";
		$icd10_code="";
		$icd10_crt_stat_arr=array();
		if(in_array($row_dx['icd10'],$icd10)){
			$icd10_desc=$row_dx['icd10_desc'];
			$icd10_code=$row_dx['icd10'];
			if($icd10_desc!=""){
				if(strlen($icd10_desc) > $len){
					$icd10_desc_arr[$icd10_code]=substr($icd10_desc,0,$len_cut)."...";
				}else{
					$icd10_desc_arr[$icd10_code]=$icd10_desc;
				}
			}
		}else{
			$icd10_crt_exp=explode('-',$row_dx['icd10']);
			if($row_dx['laterality']>0){
				foreach($lat_code_arr[$row_dx['laterality']] as $key=>$val){
					$icd10_crt_lat_arr[]=$icd10_crt_exp[0].$val;
				}
				$icd10_crt_exp_stat=$icd10_crt_exp[1];
			}else{
				$icd10_crt_exp_stat='';
				$icd10_crt_lat_arr[]=$icd10_crt_exp[0];
			}

			if($row_dx['staging']==4 or $row_dx['staging']==5 or $row_dx['severity']==3){
				if($row_dx['staging']==4){
					foreach($lat_code_arr[$row_dx['staging']] as $key=>$val){
						for($k=0;$k<=count($icd10_crt_lat_arr);$k++){
							$icd10_crt_stat_arr[]=$icd10_crt_lat_arr[$k].$icd10_crt_exp_stat.$val;
						}
					}
				}else{
					if($row_dx['severity']==3){
						foreach($lat_code_arr[$row_dx['severity']] as $key=>$val){
							for($k=0;$k<=count($icd10_crt_lat_arr);$k++){
								$icd10_crt_stat_arr[]=$icd10_crt_lat_arr[$k].$icd10_crt_exp_stat.$val;
							}
						}
					}else{
						foreach($lat_code_arr[$row_dx['staging']] as $key=>$val){
							for($k=0;$k<=count($icd10_crt_lat_arr);$k++){
								$icd10_crt_stat_arr[]=$icd10_crt_lat_arr[$k].$icd10_crt_exp_stat.$val;
							}
						}
					}
				}
			}else{
				$icd10_crt_stat_arr=$icd10_crt_lat_arr;
			}

			$icd10_crt_title_str=$icd10_full_code=$icd10_full_code2="";
			if($row_dx['laterality']>0 && $row_dx['staging']<=0 && $row_dx['severity']<=0){
				foreach($lat_code_arr[$row_dx['laterality']] as $lat_key=>$lat_val){
					$icd10_full_code=str_replace('-',$lat_val,$row_dx['icd10']);
					$icd10_crt_title_arr[$icd10_full_code]=$lat_desc_arr[$row_dx['laterality']][$lat_val];
				}
			}else if($row_dx['laterality']<=0 && $row_dx['staging']>0 && $row_dx['severity']<=0){
				foreach($lat_code_arr[$row_dx['staging']] as $lat_key=>$lat_val){
					$icd10_full_code=str_replace('-',$lat_val,$row_dx['icd10']);
					$icd10_crt_title_arr[$icd10_full_code]=$lat_desc_arr[$row_dx['staging']][$lat_val];
				}
			}else if($row_dx['laterality']<=0 && $row_dx['staging']<=0 && $row_dx['severity']>0){
				foreach($lat_code_arr[$row_dx['severity']] as $lat_key=>$lat_val){
					$icd10_full_code=str_replace('-',$lat_val,$row_dx['icd10']);
					$icd10_crt_title_arr[$icd10_full_code]=$lat_desc_arr[$row_dx['severity']][$lat_val];
				}
			}else if($row_dx['laterality']>0 && $row_dx['staging']>0 && $row_dx['severity']<=0){
				foreach($lat_code_arr[$row_dx['laterality']] as $lat_key=>$lat_val){
					$icd10_full_code=$icd10_crt_exp[0].$lat_val.$icd10_crt_exp[1];
					$icd10_crt_title_str=$lat_desc_arr[$row_dx['laterality']][$lat_val];
					foreach($lat_code_arr[$row_dx['staging']] as $sev_key=>$sev_val){
						$icd10_full_code2=$icd10_full_code.$sev_val;
						$icd10_crt_title_arr[$icd10_full_code2]=$icd10_crt_title_str.' '.$lat_desc_arr[$row_dx['staging']][$sev_val];
					}
				}
			}else if($row_dx['laterality']>0 && $row_dx['staging']<=0 && $row_dx['severity']>0){
				foreach($lat_code_arr[$row_dx['laterality']] as $lat_key=>$lat_val){
					if((stripos($icd10_crt_exp[0], "E10.35")!==false || stripos($icd10_crt_exp[0], "E11.35")!==false)){
						$e11_title=$e11_arr[$val]." ";
						$icd10_full_code=$icd10_crt_exp[0].$lat_val.$icd10_crt_exp[1];
						$icd10_crt_title_str=$e11_arr[$lat_val];
						foreach($lat_code_arr[$row_dx['severity']] as $sev_key=>$sev_val){
							$icd10_full_code2=$icd10_full_code.$sev_val;
							$icd10_crt_title_arr[$icd10_full_code2]=$icd10_crt_title_str.' '.$lat_desc_arr[$row_dx['laterality']][$sev_val];
						}
					}else{
						$icd10_full_code=$icd10_crt_exp[0].$lat_val.$icd10_crt_exp[1];
						$icd10_crt_title_str=$lat_desc_arr[$row_dx['laterality']][$lat_val];
						foreach($lat_code_arr[$row_dx['severity']] as $sev_key=>$sev_val){
							$icd10_full_code2=$icd10_full_code.$sev_val;
							$icd10_crt_title_arr[$icd10_full_code2]=$icd10_crt_title_str.' '.$lat_desc_arr[$row_dx['severity']][$sev_val];
						}
					}
				}
			}

			if(count($icd10_crt_stat_arr)>0){
				for($j=0;$j<=count($icd10_crt_stat_arr);$j++){
					if(in_array($icd10_crt_stat_arr[$j],$icd10)){
						$icd10_desc=$row_dx['icd10_desc'];
						$icd10_code=$icd10_crt_stat_arr[$j];
						if($icd10_desc!="" && $icd10_desc_arr[$icd10_code]==""){
							if(strlen($icd10_desc) > $len){
								$icd10_desc_arr[$icd10_code]=substr($icd10_desc,0,$len_cut)."...";
							}else{
								$icd10_desc_arr[$icd10_code]=$icd10_desc.'; '.$icd10_crt_title_arr[$icd10_code];
							}
						}
					}
				}
			}
		}
	}
	return $icd10_desc_arr;
}
function unit_format($val){
	$unit_exp=explode('.',$val);
	if($unit_exp[1]>0){
		if(substr($unit_exp[1],1)>0){
			$unit=$val;
		}else{
			$unit=$unit_exp[0].'.'.substr($unit_exp[1],0,1);
		}
	}else{
		$unit=$unit_exp[0];
	}
	return $unit;
}

function get_reffer_physician_id($condition,$value,$condition1,$value1)
{
	$query	=	"SELECT physician_Reffer_id, FirstName, LastName FROM refferphysician WHERE ".$condition." = '".addslashes($value)."' AND ".$condition1." = '".addslashes($value1)."' AND delete_status ='0' ORDER BY FirstName ASC";
	$sql	=	imw_query($query);
	if(imw_num_rows($sql) > 0){
		$return = imw_fetch_assoc($sql);
	}
	return $return;
}
function core_phone_unformat($phone_number){
	return (isset($GLOBALS['phone_format']) && $GLOBALS['phone_format'] == '') ? $phone_number : preg_replace('/[^0-9]/','',$phone_number);
}

function correct_state_name($zipCodeVal){
		$code_name_arr = array("/NEW JERSY/","/NEW YORK/");
		$new_code_arr = array("NJ","NY");
		$zipCodeVal = strtoupper(trim($zipCodeVal));
		$zipCodeVal = preg_replace($code_name_arr,$new_code_arr,$zipCodeVal);
		$zipCodeVal = preg_replace('/[^A-Z]/',"",$zipCodeVal);
		return $zipCodeVal;
}

function convertUcfirst($inStr)
{
	$inStr = trim($inStr);
	$return = '';
	if($inStr) {
		$return = ucwords($inStr);
	}
	return $return;
}

/*
function: core_padd_char()
purpose: to validate the correct format/length for various values
author: TS
arguments: 1: value to be checked, 2: required length, 3: string characters to be prefixed.
return: corrected value..
*/
function core_padd_char($val,$req_len,$padd_char='0',$align='left'){
	if(trim($val) != ''){
		if(strlen($val) < $req_len){	//if passed value is less than the required length
			$diff = $req_len - strlen($val); // getting the length difference.
			$paddText = '';
			for($i=0; $i<$diff; $i++){$paddText .= $padd_char;} // padd text created to prefix in the value.
			if(strtolower($align)=='left'){
				return $paddText.$val;
			}
			else{
				return $val.$paddText;
			}
		}else{
			return $val;
		}
	}else{
		return '';
	}
}

/******** Multi Level Dropdown ********/

function get_simple_menu($arrMenu,$menuId,$elemTextId,$menu_dropdown_height=300,$multi="0",$unique_id=0)
{
	global $$menuId; // reverted back from $menuId to $$menuId - $menuId was getting blank by declaring it global
	$str = "<label class='input-group-btn dropdown_toggle_trigger'>";
	$str .= "<span id='".$menuId."' class='btn btn-default dropdown-toggle' type='button' data-toggle='dropdown'>";
			$str .= "<span class='caret'></span>";
	$str .= "</span>";

		if((count($arrMenu) > 0) && (!isset($$menuId) || empty($$menuId))){
			$c = $menuId."-1";
			$str .= get_menu_options($arrMenu,$menuId,$c,$elemTextId,$menu_dropdown_height,$menuStyle='',$counterw=0,$li_idw=0,$unique_id);
			$$menuId = "1";
		}

	$str .= "<input type=\"hidden\" name=\"elemTargetName\" id=\"elemTargetName".$unique_id."\" value=\"".$elemTextId."\">";
	$str .= "<input type=\"hidden\" name=\"elemMenuMulti\" id=\"elemMenuMulti".$unique_id."\" value=\"".$multi."\">";
	$str .= "</label>";
	return $str;
}

function get_menu_options($arrMenu,$menuId,$c,$elemTextId,$menu_dropdown_height,$menuStyle='',$counterw=0,$li_idw=0,$unique_id=0)
{
	$options_unique_id="";
	$counter = !empty($counterw) ? $counterw : 1;
	$strRet = $str_options = '';
	$over_flow="";
	$li_id= !empty($li_idw) ? $li_idw : 0;
	$li_id = $counter.$li_id;
	if($menu_dropdown_height>0){
		$over_flow="overflow-y:scroll;";
	}
	foreach($arrMenu as $key => $val)
	{
		$optionLabel = $val[0];
		$optionSubMenu = $val[1];
		$optionValue = $val[2];

		if(is_array($optionSubMenu) && count($optionSubMenu)>0)
		{
			$drop_li_txt=$drop_li_id="";
			foreach($optionSubMenu as $keyLast => $valLast)
			{
				$optionSubMenuLast = $valLast[1];
				if(is_array($optionSubMenuLast))
				{
					$menuId_exp=explode('_',$elemTextId);
					$li_menu_id=array_pop($menuId_exp);
					$li_id++;
					$drop_li_txt="id = 'drop_li_".$li_menu_id."_".$li_id."'";
					$drop_li_id=$li_menu_id."_".$li_id;
				}
			}
			if($unique_id>0){
				$options_unique_id=$drop_li_id;
			}
			$menuStyle = '';
			$str_options .= "<li class='dropdown-submenu lead' ".$drop_li_txt.">".
					   "<a class=\"dropdown-multi-submenu\" href=\"javascript:void(0);\" onClick=\"set_val_text('','".$menuId."','".$elemTextId."','".$drop_li_id."')\"><label>".$optionLabel."</label><span class='glyphicon glyphicon-chevron-right pull-right'></span></a>
					   ".get_menu_options($optionSubMenu,$menuId,$c,$elemTextId,$menu_dropdown_height,$menuStyle,$counter,$li_id,$drop_li_id)."
					   ".
					   "</li>";
		}
		else
		{
			if($unique_id>0){
				$options_unique_id='_'.$unique_id.'_'.$key;
			}
			$menuStyle = 'max-height:'.$menu_dropdown_height.'px;'.$over_flow;
			$str_options .= "<li class='lead'>".
					   "<a class=\"\" href=\"javascript:void(0);\" onClick=\"set_val_text(this,'".$menuId."','".$elemTextId."','')\">".$optionLabel."</a>".
					   "<input type=\"hidden\" name=\"menuOptionValue\" id=\"menuOptionValue".$options_unique_id."\" value=\"".$optionValue."\">".
					   "</li>";
		}
		$counter++;
	}
	$strRet = '<ul class="dropdown-menu menu_id_'.$counter.'" id="'.$menuId.'" style="'.$menuStyle.'">'.$str_options.'</ul>';
	return $strRet;
}


function remLineBrk($str){
	return str_replace(array("\r","\n"),array("\\r","\\n"),$str);
}

function merging_array($table,$error)
{
	$mergedArray = array();
	if(count($table) == count($error)){
		for($a=0; $a < count($table); $a++){
			$mergedArray[] = array(
									"Table_Name"=> trim($table[$a]),
									"Error"=> trim($error[$a])
								  );
		}
		return $mergedArray;
	}
}


/*
function: log_patient_update
purpose: to check if any problem arise in patient update query
arguments: array (containg key=>value with all the available data)
return: nothing.
*/
function log_patient_update($arr_data)
{
	if(constant("APP_DEBUG_MODE")==1)
	{

			$file_name = dirname(__FILE__).'/../../data/'.PRACTICE_PATH.'/patient_update_error.log';
			$op = $_SESSION['authProviderName'];
			$pt_sess = $_SESSION['patient'];
			$dt = date("m-d-Y H:i:s");
			$data = '
			Date Time : '.$dt.'
			Patient in Session : '.$pt_sess.'
			Operator : '.$op.'
			Page Ref : '.$_SERVER['HTTP_REFERER'].'
			----QUERY DATA START-----------
			';
			foreach ($arr_data as $key=>$val){
			$data .= $key.' : '.$val.'
			';
			}
			$data .= '
			----QUERY DATA END-------------
			';
			$fp = fopen($file_name,"a");
			fwrite($fp,$data);
			fclose($fp);
	}
}

function getOS()
{
    $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
    $os_platform    =   "Unknown OS Platform";
    $os_array       =   array(
                            '/windows nt 10.0/i'     =>  'Windows 10',
							'/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
							'/windows nt 4.0/i'     =>  'Windows NT 4.0',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
							'/Windows ME/i'         =>  'Windows ME',
							'OpenBSD/i'				=>	'Open BSD',
							'SunOS/i'				=>	'Sun OS',
							'/macintosh|mac os x/i' =>  'Mac',
                            '/mac_powerpc/i'        =>  'Mac',
							'QNX'					=>	'QNX',
							'BeOS'					=>	'BeOS',
							'OS/2'					=>	'OS/2',
							'nuhk'					=>	'Search Bot',
							'Googlebot'				=>	'Search Bot',
							'Yammybot'				=>	'Search Bot',
							'Openbot'				=>	'Search Bot',
							'Slurp/cat'				=>	'Search Bot',
							'msnbot'				=>	'Search Bot',
							'ia_archiver'			=>	'Search Bot',
							'/Mac/i'        		=>  'Mac',
                            '/linux/i'              =>  'Linux',
                            '/X11/i'              	=>  'Linux',
							'/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );
    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }
    }
    return $os_platform;
}

function get_operator_type($id)
{
	$ret = "";
	$qry = "SELECT user_type FROM users WHERE id = '".$id."'  ";
	$sql = imw_query($qry);
	$row = imw_fetch_assoc($sql);
	if($row != false){
		$ret = $row["user_type"];
		switch ($ret):
				case 1:
					$userType = "Physician";
				break;
				case 2:
					$userType = "Nurse";
				break;
				case 3:
					$userType = "Technician";
				break;
				case 4:
					$userType = "Staff";
				break;
				case 5:
					$userType = "Test";
				break;
				case 6:
					$userType = "Surgical Coordinator";
				break;
		endswitch;
	}
	return $userType;
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function w($a = '')
{
    if (empty($a)) return array();

    return explode(' ', $a);
}

function browser() {
	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	// you can add different browsers with the same way ..
	if(preg_match('/(chromium)[ \/]([\w.]+)/', $ua))
			$browser = 'chromium';
	elseif(preg_match('/(chrome)[ \/]([\w.]+)/', $ua))
			$browser = 'chrome';
	elseif(preg_match('/(safari)[ \/]([\w.]+)/', $ua))
			$browser = 'safari';
	elseif(preg_match('/(opera)[ \/]([\w.]+)/', $ua))
			$browser = 'opera';
	elseif(preg_match('/(msie)[ \/]([\w.]+)/', $ua))
			$browser = 'msie';
	elseif(preg_match('/(mozilla)[ \/]([\w.]+)/', $ua))
			$browser = 'mozilla';
	preg_match('/('.$browser.')[ \/]([\w]+)/', $ua, $version);

	if(preg_match('/(windows nt)[ \/]([\w.]+)/', $ua) && stripos($ua, 'Trident/7.0; rv:11.0') !== false){
		$browser = 'msie';
		$version[2] = "11";
	}
	return array('name'=>$browser,'version'=>$version[2]);
}

function _browser($a_browser = false, $a_version = false, $name = false)
{
    $browser_list = 'msie firefox konqueror safari netscape navigator opera mosaic lynx amaya omniweb chrome avant camino flock seamonkey aol mozilla gecko';
    $user_browser = strtolower($_SERVER['HTTP_USER_AGENT']);
    $this_version = $this_browser = '';

    $browser_limit = strlen($user_browser);
    foreach (w($browser_list) as $row)
    {
        $row = ($a_browser !== false) ? $a_browser : $row;
        $n = stristr($user_browser, $row);
        if (!$n || !empty($this_browser)) continue;

        $this_browser = $row;
        $j = strpos($user_browser, $row) + strlen($row) + 1;
        for (; $j <= $browser_limit; $j++)
        {
            $s = trim(substr($user_browser, $j, 1));
            $this_version .= $s;

            if ($s === '') break;
        }
    }

    if ($a_browser !== false)
    {
        $ret = false;
        if (strtolower($a_browser) == $this_browser)
        {
            $ret = true;

            if ($a_version !== false && !empty($this_version))
            {
                $a_sign = explode(' ', $a_version);
                if (version_compare($this_version, $a_sign[1], $a_sign[0]) === false)
                {
                    $ret = false;
                }
            }
        }

        return $ret;
    }

    //
    $this_platform = '';
    if (strpos($user_browser, 'linux'))
    {
        $this_platform = 'linux';
    }
    elseif (strpos($user_browser, 'macintosh') || strpos($user_browser, 'mac platform x'))
    {
        $this_platform = 'mac';
    }
    else if (strpos($user_browser, 'windows') || strpos($user_browser, 'win32'))
    {
        $this_platform = 'windows';
    }

    if ($name !== false)
    {
        return $this_browser . ' ' . $this_version;
    }

    return array(
        "browser"      => $this_browser,
        "version"      => $this_version,
        "platform"     => $this_platform,
        "useragent"    => $user_browser
    );
}

/* Function To GET Insurance Case Name Information will return Name of case */
function get_insurance_case_name($case_id,$flg="")
{
	$sql=	imw_query("select *from insurance_case where ins_caseid='".$case_id."'");
	$row=	imw_fetch_assoc($sql);
	$ret_val = "";
	if($row)
	{
		$sql = imw_query("select *from insurance_case_types where case_id='".$row["ins_case_type"]."'");
		$row_type = imw_fetch_assoc($sql);
		if($row_type)
		{
			$caseName = ($row_type["case_name"] == "Workman Comp")	?	'Work Comp' : $row_type["case_name"];
			$ret_val = ($flg=="NoCaseId") ? $caseName : $caseName."-".$row["ins_caseid"];
		}
	}
	return $ret_val;
}

function data_path($web = 0)
{
	return (($web == 1) ? $GLOBALS['webroot'] : $GLOBALS['fileroot'])."/data/".constant('PRACTICE_PATH')."/";
}

function get_insurance_details($id)
{
		$query = "select * from insurance_companies where id = '".$id."'";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);

		if($cnt > 0)
		{
			$row = imw_fetch_object($sql);
		}
		return $row;
	}

/*
function: getData
purpose: to get a single field matching with particular id.
author: All
*/
function getData($field, $table, $baseId, $value){
	$qryStr = "SELECT $field FROM $table WHERE $baseId = '$value'";
	$qryQry = imw_query($qryStr);
	if(imw_num_rows($qryQry)>0){
		$qryRow = imw_fetch_assoc($qryQry);
	}
	return $qryRow[$field];
}

// GETTING INSURANCE CASE AND DETAILS DATA
function getInsCompby_pid($pid){
	$qryStr = imw_query("select insurance_companies.* from
			insurance_data join insurance_companies on insurance_companies.id = insurance_data.provider
			where insurance_data.pid = '$pid' and insurance_data.provider > '0' and insurance_data.actInsComp ='1'
			and (insurance_companies.frontdesk_desc='1' or insurance_companies.billing_desc='1')
			order by insurance_data.actInsComp desc");
	while($row = imw_fetch_assoc($qryStr))
	{
		$return[] = $row;
	}
	return $return;
}

function getInsCom($charge_list_id,$field,$printHcfa=''){
	$id = implode($charge_list_id,",");
	$type = substr($field,0,strpos($field,"y")+1);
	$qryStr = "select a.charge_list_id from patient_charge_list a, insurance_data b where
			a.del_status='0' and a.charge_list_id in($id) and a.$field > 0 and a.case_type_id = b.ins_caseid and b.type = '$type'";
	if($printHcfa!=""){
		$qryStr .="and b.provider = a.$field";
	}else{
		$qryStr .="and b.actInsComp = 1 and b.policy_number != '' ";
	}
	$qryId = imw_query($qryStr);
	$row = imw_fetch_assoc($qryId);
	$return = $row;
	return $return;
}

//Recent 5 Patient includes js function--
function core_refresh_recent_five(){
	//include_once($GLOBALS['srcdir']."/recent5patient.inc.php");
}
//Recent 5 Patient includes js function--

function pdf_css(){
	$css = "<style type='text/css'>
		.text_b_w{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
			background-color:#BCD5E1;
			height:15px;
		}
		.text_10b{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			background-color:#FFFFFF;
		}
		.text_10{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#FFFFFF;
		}
		.gray_bg{
			background-color:#CCCCCC;
		}
		.heading{
			border-bottom:3px groove Gainsboro;
			vertical-align:bottom;
		}
		.red_border{
			border:1px solid #F00;
		}
		.text_b{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
			background-color:#ffffff;
			border-style:solid;
			border-color:#FFFFFF;
			border-width: 1px;
		}
		.text_10ab{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			color:#000000;
			background-color:#ffffff;
		}
		.text_10ab_white{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			color:#000000;
			background-color:#ffffff;
		}
	</style>";
	return $css;
}

function write_html($file_content,$file_name='',$chk_file_append=''){
	$obj_user_save = new SaveFile($_SESSION['authUserID'],1);
	if($file_name==""){
		$file_name="new_pdf.html";
	}
	$css="";
	$final_location = "/tmp/".$file_name;
	if(strpos($file_name,'.txt')===false && strpos($file_name,'.csv')===false && $chk_file_append==""){
		$css = pdf_css();
	}
	$print_content = $css.$file_content;
	$file_pointer = $obj_user_save->cr_file($final_location,$print_content,$chk_file_append);
	return $file_pointer;
}

function get_provider_id($val){
	$val=addslashes($val);
	$val_exp=explode(', ',$val);
	$Providermname="";
	$whr_pro_mname="";
	$Providerlname=trim($val_exp[0]);

	if(strripos($val_exp[1],'.')>0){
		$val_exp2=explode(' ',$val_exp[1]);
		$Providerfname=trim($val_exp2[0]);
		//$Providermname=trim(str_replace('.','',$val_exp2[1]));
		$Providermname=trim(substr(trim($val_exp2[1]),0,-1));
	}else{
		$Providerfname=$val_exp[1];
	}
	if($Providermname!=""){
		$whr_pro_mname="and trim(mname)='$Providermname'";
	}
	$prov_qry=imw_query("select id from users where trim(fname)='$Providerfname' and trim(lname)='$Providerlname' $whr_pro_mname order by delete_status asc,user_type ASC");
	if(imw_num_rows($prov_qry)==0){
		if(strripos($val_exp[1],'.')>0){
			$val_exp2=array();
			$val_exp2=explode(' ',$val_exp[1]);
			if(count($val_exp2)>2){
				$Providerfname=trim($val_exp2[0].' '.$val_exp2[1]);
			}
			$Providermname=trim(substr(trim($val_exp2[2]),0,-1));
			if($Providermname!=""){
				$whr_pro_mname="and trim(mname)='$Providermname'";
			}
			$prov_qry=imw_query("select id from users where trim(fname)='$Providerfname' and trim(lname)='$Providerlname' $whr_pro_mname order by delete_status asc,user_type ASC");
		}
		if(imw_num_rows($prov_qry)==0){
			$Providerfname=$val_exp[1];
			$prov_qry=imw_query("select id from users where trim(fname)='$Providerfname' and trim(lname)='$Providerlname' order by delete_status asc,user_type ASC");
			if(imw_num_rows($prov_qry)==0 && count($val_exp)>2){
				$Providerlname=trim($val_exp[0]).', '.trim($val_exp[1]);
				$val_exp2=array();
				$val_exp2=explode(' ',$val_exp[2]);
				$Providerfname=trim($val_exp2[0]);
				$prov_qry=imw_query("select id from users where trim(fname)='$Providerfname' and trim(lname)='$Providerlname' order by delete_status asc,user_type ASC");
			}
		}
	}
	$prov_row=imw_fetch_array($prov_qry);

	return $prov_row['id'];
}

// Insurance Provider Related Functions
function insurance_provider_xml_extract()
{
	$xml_file_name = (constant("EXTERNAL_INS_MAPPING") == "YES") ? 'Insurance_Comp_Cross_Map' : 'Insurance_Comp';
	$insCompXMLFile =	data_path() . "xml/".$xml_file_name.".xml";

	$temp_obj = new CLSCommonFunction;
	if(file_exists($insCompXMLFile)){
		$insCompXMLFileExits = true;
	}
	else
	{
		if(constant("EXTERNAL_INS_MAPPING") == "YES"){
			$temp_obj->createInsCompXMLCrossMap();
		}
		else{
			$temp_obj->createInsCompXML();
		}
		if(file_exists($insCompXMLFile)){
			$insCompXMLFileExits = true;
		}
	}

	$return = array();
	if($insCompXMLFileExits == true)
	{
		$XML	= file_get_contents($insCompXMLFile);
		$return = $temp_obj->xml_to_array($XML);
	}

	return $return;
}

function insurance_provider_detail($id,$providerRCOId,$doFrom = false)
{
		$data = array();

		if(constant("EXTERNAL_INS_MAPPING") == "YES")
		{
			$qryGetIdxInvRCOId = "SELECT invision_plan_code, invision_plan_description, IDX_description, IDX_FSC
																FROM idx_invision_rco WHERE id = '".$providerRCOId."' LIMIT 1";
			$rsGetIdxInvRCOId = imw_query($qryGetIdxInvRCOId);
			if(imw_num_rows($rsGetIdxInvRCOId) > 0)
			{
				$dbInvisionPlanCode = $dbInvisionPlanDescription = $dbIDXDescription = $dbIDXFSC = "";
				$rowGetIdxInvRCOId = imw_fetch_row($rsGetIdxInvRCOId);
				$dbInvisionPlanCode = $rowGetIdxInvRCOId[0];
				$dbInvisionPlanDescription = $rowGetIdxInvRCOId[1];
				$dbIDXDescription = $rowGetIdxInvRCOId[2];
				$dbIDXFSC = $rowGetIdxInvRCOId[3];
			}
		}

		$insuranceDetail = get_insurance_details($id);
		if($insuranceDetail)
		{
			if($dofrom && $dofrom == 'acc_reviewpt')
			{
				$q1 = "SELECT * FROM insurance_data WHERE provider ='".$id."' AND pid ='".$_SESSION['patient']."'";
				$r1 = imw_query($q1);
				if($r1 && imw_num_rows($r1)>0){
					$rs1 = imw_fetch_assoc($r1);
					$data['policy'] = $rs1['policy_number'];
				}
			}

			if($insuranceDetail->City){
				$city = $insuranceDetail->City.', '.$insuranceDetail->State.' '.$insuranceDetail->Zip;
			}

			if(constant("EXTERNAL_INS_MAPPING") == "YES"){
				$data['name'] = $dbInvisionPlanCode.' - '.$dbInvisionPlanDescription.' - '.$dbIDXDescription.' - '.$dbIDXFSC;
			}
			else{
				$data['name'] = $insuranceDetail->in_house_code.' - '.$insuranceDetail->name;
			}

			if($insuranceDetail->contact_address){
				$address = $insuranceDetail->contact_address;
			}
			$address .= ($city) ? ' - '.$city : '';

			$data['address'] = $address;
			$data['phone'] = ($insuranceDetail->phone != '') ? $insuranceDetail->phone : '';
		}

		return $data;

}

function insurance_provider($i_type = '1', $xml_data = array(), $request = 'all', $provider_id = '' , $provider_name = '', $res_name = '', $paging = false,$page = 1, $per_page = 500)
{
	$dropdown_pri = $dropdown_sec = $dropdown_ter = $return = $typeahead	= array();
	$matched = $matched_res = false;

	$xml_data = (is_array($xml_data) && count($xml_data) > 0 ) ? $xml_data : insurance_provider_xml_extract();
	$counter = 0;
	$start = $page > 1 ? $page * $per_page : 0;
	$end = $start + $per_page;
	$max = count($xml_data);
	$end = $end > $max ? $max : $end;
	foreach($xml_data as $key => $val)
	{
			if( ($val["tag"] =="insCompInfo") && ($val["type"]=="complete") && ($val["level"]=="2") )
			{
				$counter++;

				$insCompId = $insCompINHouseCode = $insCompName = $insCompAdd = $insCompCity = $insCompState = $insCompZip = "";
				$crossMapIdxInvRCOId = $crossMapInvisionPlanCode = $crossMapInvisionPlanDescription = $crossMapIDXDescription = $crossMapIDXFSC = "";

				$insCompId = $val["attributes"]["insCompId"];
				$insCompINHouseCode = str_replace("'","",$val["attributes"]["insCompINHouseCode"]);
				$insCompName = str_replace("'","",$val["attributes"]["insCompName"]);
				$insCompAdd = str_replace("'","",$val["attributes"]["insCompAdd"]);
				$insCompCity = str_replace("'","",$val["attributes"]["insCompCity"]);
				$insCompState = str_replace("'","",$val["attributes"]["insCompState"]);
				$insCompZip = str_replace("'","",$val["attributes"]["insCompZip"]);

				if(constant("EXTERNAL_INS_MAPPING") == "YES")
				{
					$crossMapIdxInvRCOId = str_replace("'","",$val["attributes"]["dbIdxInvRCOId"]);
					$crossMapInvisionPlanCode = str_replace("'","",$val["attributes"]["dbInvisionPlanCode"]);
					$crossMapInvisionPlanDescription = str_replace("'","",$val["attributes"]["dbInvisionPlanDescription"]);
					$crossMapIDXDescription = str_replace("'","",$val["attributes"]["dbIDXDescription"]);
					$crossMapIDXFSC = str_replace("'","",$val["attributes"]["dbIDXFSC"]);
				}

				//setting In House Code For All Ins.
				$insRtName = ($insCompINHouseCode) ? $insCompINHouseCode : substr($insCompName,0,4).'....' ;


				// Creating Array for typeahead
				if(stripos($request,'typeahead') !== false || $request == 'all')
				{
					$sep = (empty($insCompINHouseCode) == false) ? ' - ' : '';
					$typeahead_str = '';
					if(constant("EXTERNAL_INS_MAPPING") == "YES")
					{
						$typeahead_str = $crossMapInvisionPlanCode." - ".$crossMapInvisionPlanDescription." - ".$crossMapIDXDescription." - ".$crossMapIDXFSC." - ".$insCompName." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip." * $insCompId-$crossMapIdxInvRCOId";
					}
					else
					{
						if(trim($insCompINHouseCode) && trim($insCompName))
						{
							$typeahead_str	=	$insCompINHouseCode." ".$sep." ".$insCompName." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip." * $insCompId";
						}
						elseif((trim($insCompINHouseCode) == "") && (trim($insCompName) != ""))
						{
							$typeahead_str = $insCompName." ".$sep." ".$insCompINHouseCode." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip." * $insCompId";
						}
					}
					if($typeahead_str)
						array_push($typeahead,$typeahead_str);

				}

				// Creating array for Dropdown
				if(stripos($request,'dropdown') !== false || $request == 'all')
				{

					if(constant("EXTERNAL_INS_MAPPING") == "YES")
					{
						$insName = $crossMapInvisionPlanCode." - ".$crossMapInvisionPlanDescription." - ".$crossMapIDXDescription." - ".$crossMapIDXFSC." - ".$insCompName;
					}
					else
					{
						$insName	= ($insRtName) ? $insRtName : '';
					}

					if( !$paging || ($paging && $counter >  $start) ) {
						$on_click1 = 'onclick="FillName(\''.addslashes($insRtName).'\',\''.$insCompId.'\',\''.$crossMapInvisionPlanCode.'\',\''.$crossMapIdxInvRCOId.'\',\'1\')"';
						$on_click2 = 'onclick="FillName(\''.addslashes($insRtName).'\',\''.$insCompId.'\',\''.$crossMapInvisionPlanCode.'\',\''.$crossMapIdxInvRCOId.'\',\'2\')"';
						$on_click3 = 'onclick="FillName(\''.addslashes($insRtName).'\',\''.$insCompId.'\',\''.$crossMapInvisionPlanCode.'\',\''.$crossMapIdxInvRCOId.'\',\'3\')"';

						$d = insurance_provider_detail($insCompId,$crossMapIdxInvRCOId);
						$tooltip_content = '';
						if(is_array($d) && count($d) > 0 )
						{
							foreach($d as $key => $val)
							{
								$tooltip_content .= '<b>'.ucfirst($key).': </b>'.$val.'<br>';
							}
						}
						//$popover = 'data-toggle="popover" data-trigger="hover" data-placement="right" data-content="'.$popover_content.'" data-html="true" ';

						$tooltip = show_tooltip($tooltip_content);
						$temp_pri = '<li class="list-group-item pointer" '.$on_click1.' '.$tooltip.'>'.trim($insName).'</li>';
						$temp_sec = '<li class="list-group-item pointer" '.$on_click2.' '.$tooltip.'>'.trim($insName).'</li>';
						$temp_ter = '<li class="list-group-item pointer" '.$on_click3.' '.$tooltip.'>'.trim($insName).'</li>';

						if($temp_pri) array_push($dropdown_pri,$temp_pri);
						if($temp_sec) array_push($dropdown_sec,$temp_sec);
						if($temp_ter) array_push($dropdown_ter,$temp_ter);
					}

				}


				// Matched Element For Insurance Company name || ID
				if(!$matched && (stripos($request,'match_provider') !== false || $request == 'all') )
				{
					if($insCompId == $provider_id)
					{
						$company_name 	= ($insCompName == "") ? 'Unassigned' : $insCompName;
						$in_house_code	= $insRtName;
						$ins_company_id = $insCompId;
						$matched = true;
					}
					if(empty($company_name) == true && $provider_id > 0)
					{
						if(strlen($provider_name) > 12){
							$company_name = substr($provider_name,0,12).'....';
						}else{
							$company_name = $provider_name;
						}
					}
					$return['company_name'] 	= $company_name;
					$return['in_house_code'] 	= $in_house_code;
					$return['ins_company_id'] = $ins_company_id;
				}


				// Matched Element For Responsible Party
				if(!$matched_res && (stripos($request,'resp_comp') !== false || $request == 'all') )
				{
					if(is_numeric($res_name) == true)
					{
						if(trim($insCompId) == trim($res_name) )
						{
							$res_name_comp = (strlen($insCompName) > 12) ?	substr($insCompName,0,12).'....'	:	$insCompName;
						}
						$matched_res = true;
						$res_name_comp = trim($res_name_comp);
						$return['res_name_comp'] = $res_name_comp;
					}
				}

		}

		if( $paging && $counter == $end ) break;
	}

	if(stripos($request,'typeahead') !== false || $request == 'all')
		$return['typeahead']	= $typeahead;
	if(stripos($request,'dropdown') !== false || $request == 'all')	{
		$return['dropdown_pri'] 	= $dropdown_pri;
		$return['dropdown_sec'] 	= $dropdown_sec;
		$return['dropdown_ter'] 	= $dropdown_ter;
		$return['loaded'] 				= ($paging) ? ($end==$max?true:false) :true;
		$return['page'] 				= $page;
	}


	return $return;
}

//User FirstName
function getUserFirstName($id,$flgFull=0,$usrType="0"){
	$ret = "";
	if(!empty($id)){
		$sql = "SELECT fname,lname,mname FROM users WHERE id='".$id."' ";
		if($usrType!="0") $sql .= " AND user_type IN (".$usrType.") ";
		$qry = imw_query($sql);
		$row = imw_fetch_array($qry);
		if( $row != false ){
			$ret = $row["fname"];
			if($flgFull == 1){
				$ret = $row["lname"].", ".$row["fname"]." ".$row["mname"];
			}else if($flgFull == 2){
				$ret = array();
				$ret[] = $row["lname"].", ".$row["fname"]." ".$row["mname"];
				$ret[] = substr($row["fname"],0,1)."".substr($row["lname"],0,1);
				$ret[] = substr($row["fname"],0,1)."".substr($row["mname"],0,1)."".substr($row["lname"],0,1);
				$ret[] = $row["fname"]." ".$row["mname"]." ".$row["lname"];
			}else if($flgFull == 3){
				$tmp = "";
				$tmp .= !empty($row["fname"]) ? $row["fname"]." " : "";
				$tmp .= !empty($row["mname"]) ? $row["mname"]." " : "";
				$tmp .= !empty($row["lname"]) ? $row["lname"]." " : "";
				$ret = $tmp;
			}
		}
	}
	return $ret;
}

function get_set_pat_rel_values_retrive($dbValue,$methodFor,$delimiter = "~|~",$hifenOptional= "")
{
	$dbValue 	= trim($dbValue);
	$methodFor 	= trim($methodFor);
	$delimiter	= trim($delimiter);

	if($methodFor <> 'pat' && $methodFor <> 'rel') return;

	$value = ($methodFor == "pat") ? $dbValue : '';

	if(stristr($dbValue,$delimiter))
	{
		list($pat,$rel) = explode($delimiter,$dbValue);
		$value = $$methodFor;
	}

	if($value) { $value = $hifenOptional.$value; }//FOR FACESHEET PDF

	return $value;
}

function get_set_pat_rel_values_save($dbValue,$postValue,$methodFor,$delimiter)
{
	$dbValue 	= trim($dbValue);
	$postValue 	= trim($postValue);
	$methodFor 	= trim($methodFor);
	$delimiter	= trim($delimiter);

	if($methodFor == "pat"){
		if(stristr($dbValue,$delimiter)){
			list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
			$postValue = $postValue.$delimiter.$strTxtRel;
		}
		else{
			$postValue = $postValue.$delimiter;
		}
	}
	elseif($methodFor == "rel"){
		if(stristr($dbValue,$delimiter)){
			list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
			$postValue = $strTxtPat.$delimiter.$postValue;
		}
		else{
			$postValue = $dbValue.$delimiter.$postValue;
		}
	}

	return $postValue;
}

/*--GETTING USER/PHYSICIAN COMMON DETAILS--*/
function getUserDetails($id,$defOpts = " id, user_group_id, username, fname, lname, mname, pro_title, pro_suffix, user_type, default_group, default_facility, user_npi, TaxonomyId, TaxId, superuser, locked, passCreatedOn, HIPPA_STATUS, hippa_date, SLA, sla_date, eRx_user_name, Enable_Scheduler, session_timeout, delete_status, sch_facilities ")
{
	$query="SELECT $defOpts FROM users WHERE id = '$id'";
	$result = imw_query($query);
	if(imw_num_rows($result)==1){
		$rs = imw_fetch_assoc($result);
		return $rs;
	}else{
		return false;
	}
}

function getRecentPatient($userId){
	$qry = imw_query("select recent_users.patient_id, patient_data.lname,
			patient_data.fname,
			patient_data.mname,patient_data.id from recent_users
			join patient_data on patient_data.id = recent_users.patient_id
			where recent_users.provider_id = '$userId'
			order by recent_users.enter_date,trim(patient_data.lname),
			trim(patient_data.fname)");
	$searchOption = '';
	while($qryRes=imw_fetch_assoc($qry)){
		$patient_id = $qryRes['patient_id'];
		$patient_name = $qryRes['lname'].', ';
		$patient_name .= $qryRes['fname'].' ';
		$patient_name .= $qryRes['mname'];
		$patient_name = ucwords(trim($patient_name));
		if($patient_name[0] == ','){
			$patient_name = substr($patient_name,1);
		}
		$searchOption .= '
			<option value = "'.$patient_id.':'.$patient_name.'">'.$patient_name.' - '.$patient_id.'</option>
		';
	}
	return $searchOption;
}

function core_get_patient_search_controls($userId,$loadingImge,$patientTextBox = "patient",$selectBox = "findBy",$path = '../common/core_search_functions.php',$patientIdHidden = "patientId",$from = "default",$anchorText = "Search", $label_text = "Patient:", $withClass=""){

	require_once($GLOBALS['fileroot'].'/library/classes/class.app_base.php');
	$app_base			= new app_base();
	$optionStr = "";
	$tdSelectBoxFindBy = "";
	$patientTextBoxJSFunction = "";
	$patientSelectBox = "";
	$patientSelectBoxWidth = "style=\"width:160px;\"";
	if($from == "default"){}
	elseif($from == "scheduler"){
		//$optionStr = core_show_recent_search($userId, $return_mode = "string",3);
		$tdSelectBoxFindBy = "id=\"activeListPatients\"";
		$patientTextBoxJSFunction = "selPatient_frontdesk();";
		$patientSelectBoxJSFunction = "searchPatientInFrontDesk(this);";
		$patientSelectBoxWidth = "";
		$data = '
		<input type="text" class="form-control '.$withClass.'"  onBlur="core_pat_check(this.value,document.getElementById(\''.$patientIdHidden.'\'));" tabindex="1" onKeyPress="{if (event.keyCode==13)return '.$patientTextBoxJSFunction.' }" name="'.$patientTextBox.'" id="'.$patientTextBox.'"><input class="form-control '.$withClass.'" disabled type="text" id="findByShow" name="findByShow" value="Active"/><input type="hidden" id="findBy" name="findBy" value="Active"/><input type="hidden" name="'.$patientIdHidden.'" id="'.$patientIdHidden.'" value="" />';
		//$patientSelectBox
		$data .= '<div class="dropdown">
			<a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-primary" data-target="#"><span class="caret"></span></a>
			<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu" id="main_search_dd">';
		$returnArray=$app_base->get_iconbar_status('update_recent_search');
		$data .=$returnArray['recent_search'];
		$data .='</ul>
		</div>
		<button type="submit" class="btn tsearch" onClick="'.$patientTextBoxJSFunction.'" tabindex="3"  onkeypress="{if (event.keyCode==13)return '.$patientTextBoxJSFunction.'}"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>';

	}
	return $data;
}

// Return TRUE IF ALLSCRIPTS API Enable
function is_allscripts($type = ''){

	$isAllScriptsEnabled = ( defined( 'ALLSCRIPTS' ) && ALLSCRIPTS === true );

	switch ($type)
	{

		// case 'enabled' || !$isAllScriptsEnabled:
		case 'enabled':
		case !$isAllScriptsEnabled:
				return $isAllScriptsEnabled;
			break;

		default:
				return ( isset($_SESSION['as_user_id']) && $_SESSION['as_user_id'] !='' );
			break;
	}
}

/*--FUNCTION TO CHECK REF.PHY STATUS IS DELETED OR NOT--*/
function is_refPhy_deleted($refid)
{
	$return = false;
	$q= "SELECT physician_Reffer_id FROM refferphysician WHERE physician_Reffer_id='".$refid."' AND delete_status='1'";
	$r = imw_query($q);
	if($r && imw_num_rows($r)==1){
		$return = true;
	}
	return $return;
}
function jQueryIntDateFormat(){
	if(isset($GLOBALS['date_format']))
	return str_replace('yyyy','yy',inter_date_format());
	else
	return 'mm-dd-yy';
}

function get_erx_status($pid)
{
	$query = "select Allow_erx_medicare from copay_policies where policies_id = '1'";
	$sql = imw_query($query);
	$row = imw_fetch_assoc($sql);

	$eRx = false;
	if(strtolower($row['Allow_erx_medicare']) == 'yes')
	{
		$query = "select erx_entry, erx_patient_id from patient_data where id = '".$pid."'";
		$sql = imw_query($query);
		$row = imw_fetch_assoc($sql);

		if($row['erx_patient_id'] != '' && $row['erx_patient_id'] != 'null')
			return $row;
		else
			return false;

	}
}

function check_two_array($firstArr,$secondArr)
{
	$return  = false;
	if(count($firstArr) > 0){
		foreach($firstArr as $key => $val){
			if($secondArr[$key] == '0'){
				$secondArr[$key] = '';
			}
			if(trim($secondArr[$key]) != trim($val)){
				$return = true;
				break;
			}
		}
	}
	return $return;
}


/***************************
Purpose: To check existence on disc for patient related files (i.e. scan card, rte request/response etc)
Coded in PHP 7
Conditionally Returns: Web URL of image for <img> tag if file exists
***************************/
function check_pt_file_exists($pt_db_file_url,$return_web_url = false){
	$disc_file_root = data_path();
	$web_file_root = $GLOBALS['webroot'].'/data/'.constant('PRACTICE_PATH');
	$path_to_check = str_replace('//','/',$disc_file_root.$pt_db_file_url);
	if(file_exists($path_to_check) && is_file($path_to_check)){
		if(!$return_web_url || ($return_web_url && $return_web_url!='file' && $return_web_url!='web')) return true;
		else if($return_web_url=='file'){
			return $path_to_check;
		}
		else if($return_web_url=='web'){
			return $web_path_to_return = str_replace('//','/',$web_file_root.$pt_db_file_url);
		}
	}
	return false;
}

function format_date($dt,$syr=0,$tm=0, $op="show")
{
		if(!empty($dt))
		{
			$format = inter_date_format();
			if($op == "insert")
			{
				$odt = $dt;
				if(preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/",$dt,$regs))
				{
					if($format=="dd-mm-yyyy"){
						$dt=$regs[3]."-".$regs[2]."-".$regs[1];
					}else{
						$dt=$regs[3]."-".$regs[1]."-".$regs[2];
					}
					//return $dt;
				}
				else if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/",$dt,$regs)){
					$dt=$regs[1]."-".$regs[2]."-".$regs[3];
				}

				//time
				if($tm == 3){
					if(preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/",$odt,$regs)){
						$tmp = $regs[1].":".$regs[2].":".$regs[3];
						$dt .= (!empty($tmp) && ($tmp != "00:00:00")) ? " ".$tmp : "";
					}
				}

			}
			else
			{
				$odt = $dt;
				if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/",$dt,$regs))
				{
					if($format=="dd-mm-yyyy"){
						$dt=$regs[3]."-".$regs[2]."-";
						$dt .= ($syr == 1) ? substr($regs[1], 2) : $regs[1];
					}else{
						$dt=$regs[2]."-".$regs[3]."-";
						$dt .= ($syr == 1) ? substr($regs[1], 2) : $regs[1];
					}
					//return $dt;
				}
				//time
				if($tm == 1){
					if(preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/",$odt,$regs)){
						$tmp = $regs[1].":".$regs[2].":".$regs[3];
						$dt .= (!empty($tmp) && ($tmp != "00:00:00")) ? " ".$tmp : "";
					}
				}
				else if($tm == 2){
					if(preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/",$odt,$regs)){
						$tmp = $regs[1].":".$regs[2];
						$dt .= (!empty($tmp) && ($tmp != "00:00")) ? " ".$tmp : "";
					}
				}
			}
		}
		return $dt;
}
function dose_details_exist($imznId,$doseNumber,$mod="Single",$whereQuery=""){
	$selquery="select * from immunization_dosedetails where imnzn_id='".$imznId."' and dose_number='".$doseNumber."' $whereQuery";
	$result=imw_query($selquery);
	$returnRow=0;
	if($result){
		$numRows=imw_num_rows($result);
		if($numRows>0){
			$resultrow=imw_fetch_array($result);
			if($mod=="Single"){
				$returnRow=$resultrow["dose_id"];
			}
			if($mod=="All"){
				$returnRow=$resultrow;
			}
		}
	}
	return $returnRow;
}

function phpDateFormat(){
	$ARRAY_FIND=array("YYYY","MM","DD");
	$ARRAY_REPL=array("Y","m","d");
	if(isset($GLOBALS['date_format']))
	return str_ireplace($ARRAY_FIND,$ARRAY_REPL,inter_date_format());
	else
	return 'm-d-Y';
}

function core_pt_secret_phrase($AESPatientID, $AESPatientLName, $AESPatientDOB){//AESPatientDOB date format will be "YYYY-MM-DD"
	$AESPatientLName = core_user_refine_input($AESPatientLName);
	$arrAESPatientDOB = explode("-",$AESPatientDOB);
	$AESPatientDOBMonth = $arrAESPatientDOB[1];
	$AESPatientDOBDay = $arrAESPatientDOB[2];
	$AESPatientIDLen = strlen($AESPatientID);
	if($AESPatientIDLen < 10){
		$restLen = 10 - $AESPatientIDLen;
		for($intCounter = 1; $intCounter <= (int)$restLen; $intCounter++){
			$AESPatIdLeadZero .= "0";
		}
	}
	$AESPatientID = $AESPatIdLeadZero.$AESPatientID;

	$AESPatientLNameLen = strlen($AESPatientLName);
	if($AESPatientLNameLen > 2){
		$AESPatientLName = substr($AESPatientLName,0,2);
	}
	else{
		$restLen = 2 - $AESPatientLNameLen;
		for($intCounter = 1; $intCounter <= $restLen; $intCounter++){
			$AESPatientLNameLeadX .= "x";
		}
	}
	$AESPatientLName = $AESPatientLNameLeadX.$AESPatientLName;

	for($intCounter = 0; $intCounter < 2; $intCounter++){
		if($AESPatientDOBMonth[$intCounter] == ""){
			$AESPatientDOBMonthLeadX .= "x";
		}
		if($AESPatientDOBDay[$intCounter] == ""){
			$AESPatientDOBDayLeadX .= "x";
		}
	}
	$AESPatientDOBMonth = $AESPatientDOBMonthLeadX.$AESPatientDOBMonth;
	$AESPatientDOBDay = $AESPatientDOBDayLeadX.$AESPatientDOBDay;
	$ASEKEY = $AESPatientID.$AESPatientLName.$AESPatientDOBMonth.$AESPatientDOBDay;
	return strtoupper($ASEKEY);
}

function core_user_refine_input($string){
	 // Replace other special chars
	$specialCharacters = array('#' => '','$' => '','%' => '','&' => '','@' => '','.' => '',
								'+' => '','=' => '','\\' => '','/' => '',' ' => '');

	$cleanString = "";
	for($strCounter=0;$strCounter<strlen($string);$strCounter++){
		foreach($specialCharacters as $key => $value){
			if(substr($string,$strCounter,1) == $key){
				$string = str_replace($key,$value,$string);
			}
		}
	}

	$string = preg_replace('/[^a-zA-Z0-9\-]/', '', $string);
	$string = preg_replace('/^[\-]+/', '', $string);
	$string = preg_replace('/[\-]+$/', '', $string);
	$string = preg_replace('/[\-]{2,}/', '', $string);
	return $string;
}

function commonNoMedicalHistoryAddEdit($moduleName,$moduleValue,$mod="save" )
	{
		$query = "select common_id, no_value,comments from commonNoMedicalHistory
							where patient_id='".$_SESSION["patient"]."' and module_name='".$moduleName."'";
		$sql=imw_query($query)or die(imw_error());
		$returnVal = "";
		if($sql)
		{
			$numRows=imw_num_rows($sql);
			if($mod=="save")
			{
				$pkId = 0;
				$action = $oldVal = $newVal = $revModuleName = "";
				if($numRows>0)
				{
					$reslutRow=imw_fetch_assoc($sql);
					$oldVal = $reslutRow["no_value"];
					$oldValComments = $reslutRow["comments"];
					$pkId = $reslutRow["common_id"];
					$query = "update commonNoMedicalHistory set patient_id='".$_SESSION["patient"]."', module_name='".$moduleName."',no_value='".$moduleValue."',date_time=now(),operator_id='".$_SESSION["authId"]."', compliant='".$_POST['compliant']."', comments='".addslashes($_POST['comments'])."' where common_id='".$reslutRow["common_id"]."'";
					$updateRes=imw_query($query)or die(imw_error());
					$action = "update";
				}
				else
				{
					$query = "insert into commonNoMedicalHistory set patient_id='".$_SESSION["patient"]."', module_name='".$moduleName."',no_value='".$moduleValue."',date_time=now(),operator_id='".$_SESSION["authId"]."', comments='".addslashes($_POST['comments'])."'";
					$updateRes=imw_query($query)or die(imw_error());
					$pkId = imw_insert_id();
					$action = "add";
				}

				// Audit Functionality
                $newVal = $moduleValue;
                require_once(dirname(__FILE__).'/class.cls_review_med_hx.php');
                $OBJReviewMedHx = new CLSReviewMedHx;
                //pre($OBJReviewMedHx,1);
                $noReviewRrr = array();
                $noReviewRrr[0]["Pk_Id"] = $pkId;
                $noReviewRrr[0]["Table_Name"] = "commonNoMedicalHistory";
                $noReviewRrr[0]["Field_Text"] = "Patient No ".$moduleName;
                $noReviewRrr[0]["Operater_Id"] = $_SESSION['authId'];
                $noReviewRrr[0]["Action"] = $action;
                if($moduleName == "Medication"){
                    $revModuleName = "Medications";
                    if(empty($oldVal) == true){
                        $oldVal = "Medications";
                    }
                    else{
                        $oldVal = "No Medications";
                    }
                    $noReviewRrr[0]["Old_Value"] = $oldVal;
                    if(empty($moduleValue) == true){
                        $newVal = "Medications";
                    }
                    else{
                        $newVal = "No Medications";
                    }
                    $noReviewRrr[0]['New_Value'] = $newVal;
                }
                elseif($moduleName == "Surgery"){
                    $revModuleName = "Sx/Procedure";
                    if(empty($oldVal) == true){
                        $oldVal = "Surgeries";
                    }
                    else{
                        $oldVal = "No Surgeries";
                    }
                    $noReviewRrr[0]["Old_Value"] = $oldVal;
                    if(empty($moduleValue) == true){
                        $newVal = "Surgeries";
                    }
                    else{
                        $newVal = "No Surgeries";
                    }
                    $noReviewRrr[0]['New_Value'] = $newVal;
                }
                elseif($moduleName == "Allergy"){
                    $revModuleName = "Allergies";
                    if(empty($oldVal) == true){
                        $oldVal = "Allergies";
                    }
                    else{
                        $oldVal = "No Allergies";
                    }
                    $noReviewRrr[0]["Old_Value"] = $oldVal;
                    if(empty($moduleValue) == true){
                        $newVal = "Allergies";
                    }
                    else{
                        $newVal = "No Allergies";
                    }
                    $noReviewRrr[0]['New_Value'] = $newVal;
                }
                elseif($moduleName == "Immunizations"){
                    $revModuleName = "Immunizations";
                    if(empty($oldVal) == true){
                        $oldVal = "Immunizations";
                    }
                    else{
                        $oldVal = "No Immunizations";
                    }
                    $noReviewRrr[0]["Old_Value"] = $oldVal;
                    if(empty($moduleValue) == true){
                        $newVal = "Immunizations";
                    }
                    else{
                        $newVal = "No Immunizations";
                    }
                    $noReviewRrr[0]['New_Value'] = $newVal;
                }
                if($moduleName == "Medication"){
                    $noReviewRrr[1]["Pk_Id"] = $pkId;
                    $noReviewRrr[1]["Table_Name"] = "commonNoMedicalHistory";
                    $noReviewRrr[1]["Field_Text"] = "Patient No ".$moduleName." Comments";
                    $noReviewRrr[1]["Operater_Id"] = $_SESSION['authId'];
                    $noReviewRrr[1]["Action"] = $action;
                    $noReviewRrr[1]["Old_Value"] = $oldValComments;
                    $noReviewRrr[1]['New_Value'] = addslashes($_POST['comments']);
                }
                $OBJReviewMedHx->reviewMedHx($noReviewRrr,$_SESSION['authId'],$revModuleName,$_SESSION['patient'],0,0);

			}
			else if($mod=="get" && $numRows>0)
			{
				$reslutRow=imw_fetch_array($sql);
				if(trim($reslutRow["no_value"]) != "")
				{
					$returnVal="checked";//will check the check box//
				}
			}
		}
		return $returnVal;
	}

	function get_active_ins_id($type,$pid)
	{
		$return= '';
		$query= "select insurance_data.id from insurance_data join patient_reff
						 				on insurance_data.id = patient_reff.ins_data_id
										join insurance_case on insurance_case.ins_caseid = insurance_data.ins_caseid
										where insurance_case.case_status = 'Open'
										and patient_reff.reff_type = '".$type."' and insurance_data.pid = '".$pid."'
										and patient_reff.patient_id = insurance_data.pid
										and insurance_data.referal_required = 'Yes'
										and insurance_data.actInsComp = '1'
										and insurance_data.provider > '0'
										order by insurance_case.ins_case_type";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		if($cnt > 0){
			$row = imw_fetch_object($sql);
			$return = $row->id;
		}
		return $return;
	}


function get_array_records_query($query)
{
	$return = array();
	$sql = imw_query($query);
	$cnt = imw_num_rows($sql);
	if($cnt > 0 )
	{
		while( $row = imw_fetch_assoc($sql))
		{
			$return[] = $row;
		}
	}
	return $return;
}

function get_array_records_obj($obj)
{
	$return = array();
	if( is_object($obj) )
	{
		$cnt = imw_num_rows($obj);
		if($cnt > 0 )
		{
			while( $row = imw_fetch_assoc($obj) )
			{
				$return[] = $row;
			}
		}
	}
	return $return;
}

function get_row_record_query($query)
{
	$row = false;
	$sql = imw_query($query);
	$cnt = imw_num_rows($sql);
	if($cnt){
		$row = imw_fetch_assoc($sql);
	}
	return $row;
}
function isProcedureNoteFinalized($chart_procedure_id){
	$sql = "SELECT count(*) AS num FROM chart_procedures WHERE id='".$chart_procedure_id."' AND finalized_status='1' ";
	$row = sqlQuery($sql);
	if($row!=false && $row["num"]>0){
		return true;
	}else{

	$sql ="SELECT count(*) AS num FROM chart_procedures c1
			LEFT JOIN chart_master_table c2 ON c1.form_id = c2.id
			WHERE c1.id='".$chart_procedure_id."' AND c2.finalize = '1' ";
	$row = get_row_record_query($sql);
	if($row!=false && $row["num"]>0){
		return true;
	}

	}
	return false;
}

/**
 * Check if Updox Credentials exists
 * @useCase = To be checked for
 * */
function is_updox( $useCase='' ){

	$useCase = strtolower($useCase);

	$status = true;//return $status;
	if( !defined('UPDOX_APP_ID') || UPDOX_APP_ID === '' ||
		!defined('UPDOX_APP_PASSWORD') || UPDOX_APP_PASSWORD === ''
	)
		$status = false;

	/*Check Account Credentials*/
	$credentials = array('account_id'=>'', 'fax_no'=>'', 'fax_name'=>'');
	$sql = 'SELECT `account_id`, `fax_no`, `fax_name` FROM `updox_credentials`';
	$resp = imw_query($sql);
	if($resp && imw_num_rows($resp)>0)
		$credentials = imw_fetch_assoc($resp);

	if($credentials['account_id']==='' && $useCase!=='admin')
		$status = false;

	if( !$status )
		return $status;

	switch($useCase)
	{
		case 'fax':
			if( $credentials['fax_no'] === '' || $credentials['fax_name'] === '')
				$status = false;
			break;
		case 'admin':
			break;
		case 'direct':
			if( (!defined('UPDOX_DIRECT') || UPDOX_DIRECT !== true) )
				$status = false;
			break;
		case 'telemedicine_appt':
			$status = true;
			break;
		case 'telemedicine':
			/* Check if the provider has permission to use Updox telemedicne portal */
			if( !empty($_SESSION['updox_user_id']) && core_check_privilege(['priv_sch_telemedicine']) )
			{
				$status = true;
			}
			else
			{
				$status = false;
			}
			break;
		default:
			$status = false;
			break;
	}

	return $status;
}

function is_interfax(){

	$status = true;

	if( !defined('fax_username') || fax_username === '' ||
		!defined('fax_password') || fax_password === ''
	)
		$status = false;

	return $status;
}

function copy_file_new($source, $destination, $source_f_name, $dest_f_name){
	if( !is_dir($destination) ){
		mkdir($destination, 0755, true);
		chown($destination, 'apache');
	}

	if(file_exists($source.'/'.$source_f_name)){
		copy($source.'/'.$source_f_name, $destination.'/'.$dest_f_name);
	}
}
function getGroupofRefPhy($refID){
	$grp_id = false;
	$arr_grpWisePhy = getArrGrpRefPhy();
	$grp_id = matchGrpRefPhy($refID,$arr_grpWisePhy);
	return $grp_id;
}
function getArrGrpRefPhy(){
	$result = imw_query("SELECT * FROM ref_group_tbl WHERE ref_group_status='0'");//MATCHING IN ACTIVE GROUPS ONLY.
	$arr_grpPhy = array();
	if($result && imw_num_rows($result)>0){
		while($rs = imw_fetch_array($result)){
			$grpID = $rs['ref_group_id'];
			$refIDs = $rs['ref_id'];
			$arr_grpPhy[$grpID] = $refIDs;
		}
	}
	return $arr_grpPhy;
}
function matchGrpRefPhy($refid,$arr_grp){
	foreach($arr_grp as $key=>$val){
		$arr_thisKeyVals = explode(',',$val);
		if(in_array($refid,$arr_thisKeyVals)){
			return $key;
		}
	}
	return false;
}
function update_ref_phy_group($refid,$newGrp){
	$newGrp = trim($newGrp);
	$existing_ref_phy_grp = getGroupofRefPhy($refid);
	del_add_refphy_in_groups($refid,$existing_ref_phy_grp,$newGrp);
}
function del_add_refphy_in_groups($rid,$gid,$newgrp){
	$arr_grp_refphy = getArrGrpRefPhy();
	if($gid>0){
		$val = $arr_grp_refphy[$gid];
		$arr_val = explode(',',$val);
		$arr_val_tmp = $arr_val;
		if(count($arr_val_tmp) > 0){
			foreach($arr_val_tmp as $k => $v){
				if($v == $rid)
					unset($arr_val[$k]);
			}
		}
		$val2 = implode(',',$arr_val);
		$query = "UPDATE ref_group_tbl SET ref_id = '$val2' WHERE ref_group_id='$gid'";
		$result = imw_query($query);
	}
	if($newgrp>0){
		$arr_grp_refphy = getArrGrpRefPhy();
		$valnew = $arr_grp_refphy[$newgrp];
		$valnew .= $rid.',';
		$query2 = "UPDATE ref_group_tbl SET ref_id = '$valnew' WHERE ref_group_id='$newgrp'";
		$resultnew = imw_query($query2);
	}
}

function core_get_patient_name($id){
	$getPatientName = "SELECT id,fname, lname, mname FROM patient_data WHERE id = '$id' ";
	$rsGetPatientName = imw_query($getPatientName);
	if(imw_num_rows($rsGetPatientName)>0) {
		$rowGetPatientName = imw_fetch_array($rsGetPatientName);
		$ptId = $rowGetPatientName['id'];
		$ptFName = $rowGetPatientName['fname'];
		$ptLName = $rowGetPatientName['lname'];
		$ptMName = $rowGetPatientName['mname'];
		$headerPtInfo = $ptLName.',&nbsp;'.$ptFName.'&nbsp;-&nbsp;'.$ptId;
	}
	($rsGetPatientName) ? imw_free_result($rsGetPatientName) : "";
	return array($ptId,$ptFName,$ptLName,$ptMName,$headerPtInfo);
}

function downloadFiles($file,$content){
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
	if(strpos($file,'.csv')>0){
		if($content==""){
			header("Content-Length: ".@filesize($file));
			readfile($file) or die("File not found.");
		}else{
			header('Content-Length: ' . strlen($content));
			echo($content);
		}
	}else{
		header('Content-Length: ' . strlen($content));
		echo($content);
	}
    exit;
}

function isHtml5OK()
{
	$ret=0;
	$strUserAgent = $_SERVER['HTTP_USER_AGENT'];
	if(stristr($strUserAgent, 'Safari') == true || stristr($strUserAgent, 'Chrome') == true ) {
		$ret=1;
	}elseif(stristr($strUserAgent, 'MSIE') == true){
		$pos = strpos($strUserAgent, 'MSIE');
		(int)substr($strUserAgent,$pos + 5, 3);
		if((int)substr($strUserAgent,$pos + 5, 3) > 8){
			$ret=1;
		}
	}else if(stristr($_SERVER['HTTP_USER_AGENT'],"rv:11") !== false){ //IE 11
		$ret=1;
	}
	return $ret;
}

function addRefPhysician($uid, $fname, $lname)
{
	$refQry = "Select physician_Reffer_id from refferphysician where FirstName ='".addslashes($fname)."' AND LastName ='".addslashes($lname)."'";
	$refRs= imw_query($refQry) or die(imw_error());

	if(imw_num_rows($refRs)>0){
		$refRes = imw_fetch_row($refRs);
		$refPhyID = $refRes[0];
	}else{
		$userQry="Select * from users WHERE id='".$uid."' and user_type='1' and delete_status='0'";
		$userRs= imw_query($userQry);
		$userRes = imw_fetch_array($userRs);
		if(imw_num_rows($userRs)>0){
			$insQry= "Insert into refferphysician SET
			FirstName='".addslashes($userRes['fname'])."',
			MiddleName='".addslashes($userRes['mname'])."',
			LastName='".addslashes($userRes['lname'])."',
			NPI='".$userRes['user_npi']."',
			Texonomy='".$userRes['TaxonomyId']."',
			status='1',
			created_date='".date('Y-m-d')."'";

			$insRs = imw_query($insQry);
			$refPhyID = imw_insert_id();
		}
	}
	return $refPhyID;
}


//to make DML query
#action:	add_update, delete
#pk_id:	primary key id of the table (if value is null, insert query will be formed else update query)
#table_name:	name of table
#arrCols:	Array of table columns
#arrVals:	Array of column values
#where_condition:	where condition WITHOUT WHERE KEYWORD
#addslashes:	yes/no
#htmlentities:	yes/no
#debug:	if 1 then query is printed and execution gets stopped, default is 0
function make_query($action = "add_update", $pk_id = "", $table_name, $arrCols, $arrVals, $where_condition = "", $addslashes = "yes", $htmlentities = "yes", $debug = 0)
{
		$query_action = (($action == "add_update") ? (($pk_id == "") ? "INSERT" : "UPDATE") : (($action == "delete" ) ? "DELETE" : ""));
		if($query_action != ""){
			$intColsCnt = count($arrCols);
			$intValsCnt = count($arrVals);
			$strCols = "";
			if(is_array($arrCols) && $intColsCnt > 0 && $intColsCnt == $intValsCnt){
				for($i = 0; $i < $intColsCnt; $i++){
					$this_value = ($addslashes == "yes") ? addslashes($arrVals[$i]) : $arrVals[$i];
					$this_value = ($htmlentities == "yes") ? htmlentities($this_value) : $this_value;
					if($i == $intColsCnt-1){
						$strCols .= $arrCols[$i]." = '".$this_value."' ";
					}else{
						$strCols .= $arrCols[$i]." = '".$this_value."', ";
					}
				}

				switch($query_action){
					case "INSERT":
						$qry = "INSERT INTO ".$table_name." ";
						$key = " SET ";
						$where_clause = "" ;
					break;
					case "UPDATE":
						$qry = "UPDATE ".$table_name." ";
						$key = " SET ";
						$where_clause = (trim($where_condition) != "") ? " WHERE ".$where_condition." " : "" ;
					break;
					case "DELETE":
						$qry = "DELETE FROM ".$table_name." ";
						$key = "";
						$where_clause = (trim($where_condition) != "") ? " WHERE ".$where_condition." " : "" ;
					break;
				}

				$qry .= $key.$strCols.$where_clause;
				if($debug == 1){
					die($qry);
				}else{
					return $qry;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
}

function set_users_log_details($table_name,$server_arr)
{
	global $webServerRootDirectoryName,$web_RootDirectoryName;
	$dir_log = data_path() . "log";
	if(!is_dir($dir_log)){
		$oct_dir=mkdir($dir_log,0777);
	}
	if(is_dir($dir_log)){
		$date_f=date("Y_m_d");
		$dir_log_file=$dir_log."/".$date_f.".txt";
		$file = fopen($dir_log_file,"a");
		$seprator="\r\n\r\n";
		fwrite($file,$seprator.$server_arr);
		fclose($file);
		(int)$month_c=date("m");
		(int)$year_c=date("Y");
		foreach(glob(data_path().'log/*.txt') as $get_txt_file_name){
			list($file_name_key)=explode(".",end(explode("/",$get_txt_file_name)));
			list($f_yy,$f_mm,$f_dd)=explode("_",$file_name_key);
			$file_get_path=trim($get_txt_file_name);
			if($month_c>$f_mm || $year_c>$f_yy){
				unlink($file_get_path);
			}
		}
	}
}


//START CODE TO CHECK RECORD EXISTS IN PATIENT-DOCUMENT
function ptDocExistFun($patient_id) {//$dBaseName = constant("IMEDIC_SCAN_DB")
	$ChkAnyDocExistsNumRow=0;
	$andCatIdQry='';

	if($patient_id) {
		$qryChkAnyDocExists="SELECT * from document_patient_rel WHERE p_id='".$patient_id."'";
		$resChkAnyDocExists= imw_query($qryChkAnyDocExists);
		$ChkAnyDocExistsNumRow = imw_num_rows($resChkAnyDocExists);
	}
	return $ChkAnyDocExistsNumRow;

}
//END CODE TO CHECK RECORD EXISTS IN PATIENT-DOCUMENT

//START CODE TO CHECK SCAN/UPLOAD EXISTS IN PATIENT-DOCUMENT
function scnUploadGivenToExistFun($doc_id,$scan_from,$patientId) {
	$chkEduTstScnUpldNumRow=0;
	if($doc_id) {
		$chkEduTstScnUpldQry="SELECT upload_lab_rad_data_id,givenToEduMultiPtId FROM upload_lab_rad_data WHERE uplaod_primary_id='".$doc_id."' AND scan_from='".$scan_from."' AND givenToEduMultiPtId LIKE \"%\'".$patientId."\'%\"";
		$chkEduTstScnUpldRes = imw_query($chkEduTstScnUpldQry) or die(imw_error());
		$chkEduTstScnUpldNumRow = imw_num_rows($chkEduTstScnUpldRes);
	}
	return $chkEduTstScnUpldNumRow;

}
//END CODE TO CHECK SCAN/UPLOAD EXISTS IN PATIENT-DOCUMENT

//START CODE TO CHECK SCAN/UPLOAD EXISTS IN PATIENT-DOCUMENT
function scnUploadPtEduExistFun($doc_id,$scan_from) {
	$chkEduTstScnUpldNumRow=0;
	if($doc_id) {
		$chkEduTstScnUpldQry="SELECT upload_lab_rad_data_id FROM upload_lab_rad_data WHERE uplaod_primary_id='".$doc_id."' AND scan_from='".$scan_from."' AND upload_status='0'";
		$chkEduTstScnUpldRes = imw_query($chkEduTstScnUpldQry) or die(imw_error());
		$chkEduTstScnUpldNumRow = imw_num_rows($chkEduTstScnUpldRes);
	}
	return $chkEduTstScnUpldNumRow;

}
//END CODE TO CHECK SCAN/UPLOAD EXISTS IN PATIENT-DOCUMENT


function getPtEduCondition($patient_id,$form_id,$operator_id)
{

	if(!$patient_id) 	{$patient_id 	= $_SESSION["patient"]; }
	if(!$operator_id) 	{$operator_id 	= $_SESSION['authId'];	}

	$form_id='';
	$ptEduCondArr 			= array();
	$eduResourceExist 		= '';
	$eduGivenToAllStatus 	= '';
	if($form_id=="" || !$form_id){
		$form_id=$_SESSION['form_id'];
	}
	if($patient_id) {
		//visit shown start
		$arr_all_visit=array();
		$arr_pt_all_vist="Select tech_id,ptVisit from tech_tbl";
		$res_pt_all_vist=imw_query($arr_pt_all_vist);
		while($row_pt_all_vist=imw_fetch_assoc($res_pt_all_vist)){
			$tech_admin_id=$row_pt_all_vist['tech_id'];
			$tech_admin_visit=$row_pt_all_vist['ptVisit'];
			$arr_all_visit[$tech_admin_visit]=$tech_admin_id;
		}
		$tech_id=array();
		$sel_visit=imw_query("select ptVisit from chart_master_table
			where
			patient_id='$patient_id' and id='".$form_id."'");
		$row_visit=imw_fetch_array($sel_visit);
		if($row_visit['ptVisit']){
			$arr_visit=explode(",",$row_visit['ptVisit']);
			foreach($arr_visit as $pt_visits){
				$tech_id[]=$arr_all_visit[$pt_visits];
			}

		}

		$tech_id_imp=implode(', ',$tech_id);
		//visit shown end

		//plan shown start
			$plan_exp_final = array();
			//tests shown start
			$VFTest = $HRTTest = $OCTTest = $PachyTest = $IVFATest = $FUNDUSTest = $ExternalAnteriorTest = $TopographyTest = $ophthaTest = false;
			$sqlGetVFTest = imw_query("SELECT vf_id FROM vf WHERE patientId = '$patient_id' and purged='0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetVFTest)>0){
				$plan_exp_final[] = "visual field";
			}
			$sqlGetHRTTest = imw_query("SELECT nfa_id FROM nfa WHERE patient_id = '$patient_id' and purged='0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetHRTTest)>0){
				$plan_exp_final[] = "hrt";
			}
			$sqlGetOCTTest = imw_query("SELECT oct_id FROM oct WHERE patient_id = '$patient_id' and purged='0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetOCTTest)>0){
				$plan_exp_final[] = "oct";
			}
			$sqlGetPachyTest = imw_query("SELECT pachy_id FROM pachy WHERE patientId = '$patient_id' and purged='0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetPachyTest)>0){
				$plan_exp_final[] = "pachy";
			}
			$sqlGetIVFATest = imw_query("SELECT vf_id FROM ivfa WHERE patient_id = '$patient_id' and purged='0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetIVFATest)>0){
				$plan_exp_final[] = "ivfa";
			}
			$sqlGetFUNDUSTest = imw_query("SELECT disc_id FROM disc WHERE patientId = '$patient_id' and purged='0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetFUNDUSTest)>0){
				$plan_exp_final[] = "fundus";
			}
			$sqlGetExternalAnteriorTest = imw_query("SELECT fundusDiscPhoto FROM disc_external WHERE patientId = '$patient_id'
																	and purged='0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetExternalAnteriorTest)>0){
				$resultEAtest = imw_fetch_array($sqlGetExternalAnteriorTest);
				if($resultEAtest['fundusDiscPhoto'] == '1'){
				$plan_exp_final[] = "external";
				} else if($resultEAtest['fundusDiscPhoto']== '2'){
				$plan_exp_final[] = "anterior";
				}
			}
			$sqlGetBscanTest = imw_query("SELECT test_bscan_id  FROM test_bscan WHERE patientId = '$patient_id' and purged='0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetBscanTest)>0){
				$plan_exp_final[] = "b-scan";
			}
			$sqlGetCellcntTest = imw_query("SELECT test_cellcnt_id  FROM test_cellcnt WHERE patientId = '$patient_id' and purged='0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetCellcntTest)>0){
				$plan_exp_final[] = "cell count";
			}

			$sqlGetOphthaTest = imw_query("SELECT ophtha_id FROM ophtha WHERE patient_id = '$patient_id' and purged='0' ");
			if(imw_num_rows($sqlGetOphthaTest)>0){
				$plan_exp_final[] = "ophthalmoscopy";
			}
			 $plan_imp=implode(', ',$plan_exp_final);

			//medication shown start
			$sel_med=imw_query("select title  from lists where pid='".$patient_id."'");
			$med=array();
			while($row_med=imw_fetch_array($sel_med)){
				$med[]=strtolower($row_med['title']);
			}
			$med_exp_final=array_unique($med);
			$med_imp_final=implode(', ',$med_exp_final);
			//medication shown end


		//dx and cpt shown start
		$sel_dx=imw_query("select pi.dx1,pi.dx2,pi.dx3,pi.dx4, pi.dx5,pi.dx6,pi.dx7,pi.dx8,pi.dx9,pi.dx10,pi.dx11,pi.dx12, pi.cptCode  from superbill as sb,procedureinfo as pi
												where pi.idSuperBill=sb.idSuperBill and pi.delete_status='0' AND sb.patientId='".$patient_id."' ");
		$dx_array=array();
		$cpt_array=array();
		while($row_dx=imw_fetch_array($sel_dx)){
			if($row_dx['dx1']){
				$dx_array[]=$row_dx['dx1'];
			}
			if($row_dx['dx2']){
				$dx_array[]=$row_dx['dx2'];
			}
			if($row_dx['dx3']){
				$dx_array[]=$row_dx['dx3'];
			}
			if($row_dx['dx4']){
				$dx_array[]=$row_dx['dx4'];
			}

			if($row_dx['dx5']){
				$dx_array[]=$row_dx['dx5'];
			}
			if($row_dx['dx6']){
				$dx_array[]=$row_dx['dx6'];
			}
			if($row_dx['dx7']){
				$dx_array[]=$row_dx['dx7'];
			}
			if($row_dx['dx8']){
				$dx_array[]=$row_dx['dx8'];
			}

			if($row_dx['dx9']){
				$dx_array[]=$row_dx['dx9'];
			}
			if($row_dx['dx10']){
				$dx_array[]=$row_dx['dx10'];
			}
			if($row_dx['dx11']){
				$dx_array[]=$row_dx['dx11'];
			}
			if($row_dx['dx12']){
				$dx_array[]=$row_dx['dx12'];
			}

			if($row_dx['cptCode']){
				$cpt_array[]=$row_dx['cptCode'];
			}
		}

		//START GET DX-CODE FROM PT PROBLEM LIST
		$dx_problem_exp_final = array();
		$sqlDxQry = imw_query("SELECT problem_name FROM pt_problem_list where pt_id = '".$patient_id."' AND status='Active' ") or die(imw_error());
		$problemNameArr = array();
		if(imw_num_rows($sqlDxQry)>0){
			while($row_sqlDx=imw_fetch_array($sqlDxQry)){
				if(strstr($row_sqlDx['problem_name'],"-")){
					$problemNameExp = explode('-',$row_sqlDx['problem_name']);
				}else if(stristr($row_sqlDx['problem_name'],"(")){
					$problemNameExp = explode('(',str_ireplace(')',"",$row_sqlDx['problem_name']));
				}
				$problemNameArr[]=is_array($problemNameExp) ? trim(end($problemNameExp)) : "" ;
			}

		}
		$dx_problem_exp_final=array_unique($problemNameArr);

		//END GET DX-CODE FROM PT PROBLEM LIST

		$cpt_exp_final=array_unique($cpt_array);
		$dx_exp_final=array_unique(array_merge($dx_array,$dx_problem_exp_final));
		//add ICD10 formats --
		if(count($dx_exp_final)>0){
			$t=array();
			foreach($dx_exp_final as $k => $v){
				if(!empty($v)){
					$v2 = substr($v, 0, -1);
					$v2.="-";
					$v3 = substr($v, 0, -2);
					$v3.="--";
					$v4 = substr($v, 0, -3);
					$v4.="-x-";
					$t[] = $v;
					$t[] = $v2;
					$t[] = $v3;
					$t[] = $v4;
				}
			}
			$dx_exp_final = $t;
		}
		//add ICD10 formats --
		$cpt_imp_final=implode(', ',$cpt_exp_final);
		$dx_imp_final=implode(', ',$dx_exp_final);
		//dx and cpt shown end
		$sel_scan=imw_query("select scan_id from ".constant("IMEDIC_SCAN_DB").".scans where image_form='chartnoteDocumentsRel'  and patient_id='$patient_id'");
		if(imw_num_rows($sel_scan)>0){
			while($rel_row=imw_fetch_array($sel_scan)){
				$name_doc="document_".$rel_row['scan_id'];
				$scan_id=$rel_row['scan_id'];
			}
		}
		//print_r($lab_exp_final);
		$frmIdQry='';
		$arr_pt_edu_docs = array();
		$arr_pt_tes_docs = array();
		$sel_doc=imw_query("select id,name,scan_id,visit,tests,txt_lab_name,lab_criteria,lab_result,dx,cpt,medications,pt_edu,pt_test,andOrCondition from document where status='0' order by name asc");
		while($row=imw_fetch_array($sel_doc)){
			$visit="";
			$tests="";
			$txt_lab_name="";
			$dx="";
			$cpt="";
			$medications="";
			$andOrCondition="";
			$doc_id=$row['id'];
			$name=$row['name'];
			$scan_id=$row['scan_id'];
			$visit=$row['visit'];
			$tests=strtolower($row['tests']);
			$andOrCondition = $row['andOrCondition'];
			$txt_lab_name=strtolower($row['txt_lab_name']);
			$lab_criteria=$row['lab_criteria'];
			$lab_result=$row['lab_result'];
			$dx=$row['dx'];
			$cpt=$row['cpt'];
			$medications=strtolower($row['medications']);

			$medicationsComma=strtolower($row['medications']);
			$medExist=false;
			$medArr = array();
			if(stristr($medicationsComma,',') || is_numeric($medicationsComma)) {
				$medExist=true;
				$medDataRes = imw_query("SELECT medicine_name FROM `medicine_data` WHERE id IN(".$medicationsComma.") AND del_status = '0'");
				while($medDataRow = imw_fetch_array($medDataRes)) {
					$medArr[] = strtolower($medDataRow["medicine_name"]);
				}
			}
			$pt_edu_arr[$doc_id]=$row['pt_edu'];
			$pt_test_arr[$doc_id]=$row['pt_test'];

			$visit_arr=array();
			$tests_arr=array();
			$txt_lab_name_arr=array();
			$dx_arr=array();
			$med_arr=array();

			$visit_arr = explode(',',$visit);
			$tests_arr = explode(',',$tests);
			$txt_lab_name_arr = explode(',',$txt_lab_name);
			$dx_arr = explode(',',$dx);
			$cpt_arr = explode(',',$cpt);

			$med_arr_pre = explode('<br />',$medications);
			if($medExist==true && count($medArr)>0) {
				$med_arr_pre = $medArr;
			}
			foreach($med_arr_pre as $val){
			$med_arr[] = trim($val);

			}

			//LAB TESTS
			$lab_txt_whr='';
			if($lab_result){
				if($lab_criteria=='greater'){
					$lab_txt_whr=" AND lor.result > '".$lab_result."'";
				}else if($lab_criteria=='greater_equal'){
					$lab_txt_whr=" AND lor.result >= '".$lab_result."'";
				}else if($lab_criteria=='equalsto'){
					$lab_txt_whr=" AND lor.result='".$lab_result."'";
				}else if($lab_criteria=='less_equal'){
					$lab_txt_whr=" AND lor.result <= '".$lab_result."'";
				}else if($lab_criteria=='less'){
					$lab_txt_whr=" AND lor.result < '".$lab_result."'";
				}
			}

			$labQry = "SELECT ltd.lab_patient_id FROM lab_test_data ltd
						INNER JOIN lab_observation_result lor ON (lor.lab_test_id=ltd.lab_test_data_id)
						WHERE ltd.lab_status <= '4' AND ltd.lab_patient_id = '$patient_id' AND lor.observation ='".$txt_lab_name."' AND lor.observation !='' $lab_txt_whr";
			$labRes=imw_query($labQry) or die(imw_error());
			$labNumRow = imw_num_rows($labRes);

			//LAB TESTS
			if($andOrCondition=='O') {
				if((array_intersect($tech_id,$visit_arr) && $visit!="") 			||
				   (array_intersect($plan_exp_final,$tests_arr) && $tests!="")			||
				   (array_intersect($cpt_exp_final,$cpt_arr) && $cpt!="")			||
				   (array_intersect($dx_exp_final,$dx_arr) && $dx!="")				||
				   (array_intersect($med_exp_final,$med_arr) && $medications!="")	||
				   ($labNumRow>0 && $txt_lab_name!="")
				  ){

						if($row['pt_edu'] == 1 || $row['pt_test'] == 1){
							$qrynew1 = "SELECT id from document_patient_rel where p_id='".$patient_id."' AND doc_id='".$doc_id."' AND status = 0 ORDER BY date_time desc" ;
							$resnew1 = imw_query($qrynew1) or die(imw_error());
							$numRownew1 = imw_num_rows($resnew1);
							if($numRownew1==0) {
								$eduResourceExist = 'yes';
								if($row['pt_edu'] == 1){
									$arr_pt_edu_docs[] = $doc_id;
									$arr_pt_edu_docs_name[$doc_id] = $name;
									//$eduResourceExist = 'yes';
								}
								if($row['pt_test'] == 1){
									$arr_pt_tes_docs[] = $doc_id;
									$arr_pt_tes_docs_name[$doc_id] = $name;
									//$eduResourceExist = 'yes';
								}
							}else {
								$eduGivenToAllStatus = 'yes';
							}
						}
				}
			}else {
				if($visit!="" || $tests!="" || $cpt!="" || $dx!="" || $medications!="" || $txt_lab_name!="") {
					if((array_intersect($tech_id,$visit_arr) || ($visit==""))){
						if((array_intersect($plan_exp_final,$tests_arr) || ($tests==""))){
							if((array_intersect($cpt_exp_final,$cpt_arr) || ($cpt==""))){
								if((array_intersect($dx_exp_final,$dx_arr) || ($dx==""))){
									if((array_intersect($med_exp_final,$med_arr) || ($medications==""))){
										if($labNumRow>0 || $txt_lab_name==""){

											if($row['pt_edu'] == 1 || $row['pt_test'] == 1){
												$qrynew1 = "SELECT id from document_patient_rel where p_id='".$patient_id."' AND doc_id='".$doc_id."' AND status = 0 ORDER BY date_time desc" ;
												$resnew1 = imw_query($qrynew1) or die(imw_error());
												$numRownew1 = imw_num_rows($resnew1);
												if($numRownew1==0) {
													$eduResourceExist = 'yes';
													if($row['pt_edu'] == 1){
														$arr_pt_edu_docs[] = $doc_id;
														$arr_pt_edu_docs_name[$doc_id] = $name;
														//$eduResourceExist = 'yes';
													}
													if($row['pt_test'] == 1){
														$arr_pt_tes_docs[] = $doc_id;
														$arr_pt_tes_docs_name[$doc_id] = $name;
														//$eduResourceExist = 'yes';
													}
												}else {
													$eduGivenToAllStatus = 'yes';
												}
											}
										}
									}

								}
							}

						}
					}
				}
			}
		}
		$eduResourceGivenToAll='';
		if($eduResourceExist!='yes' && $eduGivenToAllStatus=='yes') {
			$eduResourceGivenToAll='yes';
		}
		$ptEduCondArr[0] = $arr_pt_edu_docs;
		$ptEduCondArr[1] = $arr_pt_tes_docs;
		$ptEduCondArr[2] = $eduResourceExist;
		$ptEduCondArr[3] = $eduResourceGivenToAll;
		$ptEduCondArr[4] = $arr_pt_edu_docs_name;
		$ptEduCondArr[5] = $arr_pt_tes_docs_name;

	}
	return $ptEduCondArr;
}

/*
Function: get_pt_edu_alert
Purpose: to get pt edu alert using ajax (on save/ change of any relevant information in the application)
Returns: STRING CONTAINING - DOCUMENT ALERT SHOW: Y/NULL, DOCUMENT ICON IMAGE TYPE: N/Y/G
*/
function get_pt_edu_alert(){
	$ptEduArr = getPtEduCondition($_SESSION["patient"], "0", $_SESSION["authId"]);

	$documentImgSrcYN = "N"; //no document available
	$documentImgSrc = $GLOBALS["webroot"].'/images/icons_progNts.png'; //no document available
	if($ptEduArr[3] == "yes") { 	//all given
		$documentImgSrcYN = "G";
		$documentImgSrc = $GLOBALS["webroot"].'/images/icons_progNts_green.png';
	}else if($ptEduArr[2] == "yes") { 	//to be given
		$documentImgSrcYN = "Y";
		$documentImgSrc = $GLOBALS["webroot"].'/images/icons_progNts_active.png';
	}
	$existing_documents = $_SESSION['PT_DOC_NUMBER'];
	$int_doc_cnt = (!count($ptEduArr[0])) ? 0 : count($ptEduArr[0]);
	if($int_doc_cnt=="0") {
		$int_doc_cnt = (!count($ptEduArr[1])) ? 0 : count($ptEduArr[1]);
	}
	$_SESSION['PT_DOC_NUMBER'] =  $int_doc_cnt;
	$_SESSION['PT_DOC_ALERT_STATUS'] = $documentImgSrc;

	$contentEdu = '';
	$countPtEdu = is_array($ptEduArr[4]) ? count($ptEduArr[4]) : 0 ;
	$countPtTest = is_array($ptEduArr[5]) ? count($ptEduArr[5]) : 0 ;
	$countEduTest = (int)($countPtEdu + $countPtTest);
	$mergEduArr = array();

	//START IF NEW ALERT ADDED THEN EDUCATION POPUP WILL APPEAR EVEN AFTER PRESSING CANCEL BUTTON
	$ptEduMrgArr = array();
	$ptEduMrgArr = $ptEduArr[4];
	if(is_array($ptEduArr[5]) && count($ptEduArr[5])>0) {
		$ptEduMrgArr = array_unique(array_merge($ptEduArr[4],$ptEduArr[5]));
	}
	$pt_edu_new_add_status = '';
	if(is_array($ptEduMrgArr)){
	foreach($ptEduMrgArr as $ptEduMrgKey => $ptEduMrgVal) {
		if(!is_array($_SESSION['PT_EDU_ALERT_ARRAY']) || !in_array($ptEduMrgVal,$_SESSION['PT_EDU_ALERT_ARRAY'])) {
			$pt_edu_new_add_status = 1;
		}
	}
	}
	//END IF NEW ALERT ADDED THEN EDUCATION POPUP WILL APPEAR EVEN AFTER PRESSING CANCEL BUTTON

	if(($countPtEdu > 0 || $countPtTest > 0) && (!$_SESSION['PT_EDU_ALERT_STATUS'] || $pt_edu_new_add_status==1)) {
		$mergEduArr = array_merge($ptEduArr[0],$ptEduArr[1]);
		$contentEdu.= '<div id="divConPtEdu" class="panel-heading" style="cursor:move; width:400px; z-index:1000;">imwemr</div><div class="panel-body" style="max-height:400px;overflow-y:auto;"><p style="text-align:left;background-color:#FFFFFF;" >Patient education material is available</p><table border="0" cellpadding="2" cellspacing="0" style="width:98%;" class=""><tbody id="newTbodyBorderEdu"> ';
		if($countPtEdu > 0) {
			foreach($ptEduArr[4] as $ptEduKey => $ptEduVal) {
				$contentEdu.= '<tr><td width="20" class="pl10"><div class="checkbox"><input type="checkbox" name="chbx_pt_edu_'.$ptEduKey.'" id="chbx_pt_edu_'.$ptEduKey.'" value="'.$ptEduKey.'" checked="checked" /><label for="chbx_pt_edu_'.$ptEduKey.'">'.$ptEduVal.'</label></div></td></tr>';
			}
		}
		if($countPtTest > 0) {
			foreach($ptEduArr[5] as $ptTstKey => $ptTstVal) {
				$contentEdu.= '<tr><td width="20" class="pl10"><div class="checkbox"><input type="checkbox" name="chbx_pt_edu_'.$ptTstKey.'"  id="chbx_pt_edu_'.$ptTstKey.'" value="'.$ptTstKey.'" checked="checked" /><label for="chbx_pt_edu_'.$ptTstKey.'">'.$ptTstVal.'</label></div></td></tr>';
			}
		}
		$implEduArr = implode(',',$mergEduArr);
		$_SESSION['PT_EDU_ARRAY'] = $ptEduMrgArr;
		$contentEdu.= '</tbody></table></div>';
		$contentEdu.= '<div class="panel-footer text-center" ><input type="hidden" name="hiddCntEdu" id="hiddCntEdu" value="'.$countEduTest.'"><input type="hidden" name="hiddEduId" id="hiddEduId" value="'.$implEduArr.'">';
		$contentEdu.= ' &nbsp; <input type="button" value="Given" onClick="top.given_pt_edu();" class="btn btn-success">';
		$contentEdu.= ' &nbsp; <input type="button" value="Cancel" onClick="top.cancel_pt_edu_alert()" class="btn btn-danger">';
		$contentEdu.= '</div>';
	}


	echo $ptEduArr[2]."~~".$documentImgSrcYN."~~".$existing_documents."~~".$contentEdu;
}

function __getApptInfo($patient_id,$providerIds=0,$report_start_date,$report_end_date){
		$appStrtDate = $appStrtTime = $doctorName = $facName = $procName = $andSchProvQry = "";
		$schDataQryRes=array();
		if($providerIds) { $andSchProvQry = "AND sc.sa_doctor_id IN($providerIds)";}

		if($report_start_date || $report_end_date){
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
						sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext, fac.phone as facPhone,slp.proc as procName, sc.sa_comments
						FROM schedule_appointments sc
						LEFT JOIN users us ON us.id = sc.sa_doctor_id
						LEFT JOIN facility fac ON fac.id = sc.sa_facility_id
						LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid
						WHERE sa_patient_id = '".$patient_id."'
						AND sc.sa_app_start_date BETWEEN '".$report_start_date."' AND '".$report_end_date."'
						AND sc.sa_patient_app_status_id NOT IN('18','203')
						$andSchProvQry
						ORDER BY sc.sa_app_start_date DESC
						LIMIT 0,1";
			$schDataQryRes = get_array_records_query($schDataQry);
		}

		if(count($schDataQryRes)<=0) {
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext,fac.phone as facPhone,slp.proc as procName, sc.sa_comments
							FROM schedule_appointments sc
							LEFT JOIN users us ON us.id = if(sc.facility_type_provider!='0',sc.facility_type_provider,sc.sa_doctor_id)
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid
							WHERE sa_patient_id = '".$patient_id."'
							AND (DATE_FORMAT(concat(sc.sa_app_start_date,' ',sc.sa_app_starttime),'%Y-%m-%d %H:%i')>='".date("Y-m-d H:i")."')
							AND sc.sa_patient_app_status_id NOT IN('18','203')
							AND sc.sa_patient_app_status_id IN('0','13','17','202')
							$andSchProvQry
							ORDER BY sc.sa_app_start_date ASC
							LIMIT 0,1";
			$schDataQryRes = get_array_records_query($schDataQry);
		}
		if(count($schDataQryRes)<=0) {
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext ,fac.phone as facPhone,slp.proc as procName, sc.sa_comments
							FROM schedule_appointments sc
							LEFT JOIN users us ON us.id = if(sc.facility_type_provider!='0',sc.facility_type_provider,sc.sa_doctor_id)
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid
							WHERE sa_patient_id = '".$patient_id."'
							AND sc.sa_app_start_date <= current_date()
							AND sc.sa_patient_app_status_id NOT IN('18','203')
							$andSchProvQry
							ORDER BY sc.sa_app_start_date DESC
							LIMIT 0,1";
			$schDataQryRes = get_array_records_query($schDataQry);
		}
		if(count($schDataQryRes)<=0) {
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."'') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext ,fac.phone as facPhone,slp.proc as procName, sc.sa_comments
							FROM schedule_appointments sc
							LEFT JOIN users us ON us.id = sc.sa_doctor_id
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid
							WHERE sa_patient_id = '".$patient_id."'
							AND sc.sa_app_start_date <= current_date()
							$andSchProvQry
							ORDER BY sc.sa_app_start_date DESC
							LIMIT 0,1";
			$schDataQryRes = get_array_records_query($schDataQry);
		}
		if(count($schDataQryRes)>0) {
			for($i=0;$i<count($schDataQryRes);$i++){
				$appStrtDate 			= $schDataQryRes[$i]['appStrtDate'];
				$appStrtDate_FORMAT 	= $schDataQryRes[$i]['appStrtDate_FORMAT'];
				$facName 				= $schDataQryRes[$i]['facName'];
				$facStreet 				= $schDataQryRes[$i]['facStreet'];
				$facCity 				= $schDataQryRes[$i]['facCity'];
				$facState 				= $schDataQryRes[$i]['facState'];
				$facPostal_code			= $schDataQryRes[$i]['facPostal_code'];
				$faczip_ext				= $schDataQryRes[$i]['faczip_ext'];
				$facPhone 				= $schDataQryRes[$i]['facPhone'];
				$facPhoneFormat			= $facPhone;
				if(trim($facPhoneFormat)) {
					$facPhoneFormat = str_ireplace("-","",$facPhoneFormat);
					$facPhoneFormat = "(".substr($facPhoneFormat,0,3).") ".substr($facPhoneFormat,3,3)."-".substr($facPhoneFormat,6);
				}

				$procName 				= $schDataQryRes[$i]['procName'];
				$doctorName 			= $schDataQryRes[$i]['doctorName'];
				$doctorLastName 		= $schDataQryRes[$i]['doctorLastName'];

				$appSite 				= ucfirst($schDataQryRes[$i]['appSite']);
				$appSiteShow 			= $appSite;
				if($appSite == "Bilateral") {$appSiteShow="Both"; }

				$appStrtTime 			= $schDataQryRes[$i]['appStrtTime'];
				if($appStrtTime[0]=="0") { $appStrtTime = substr($appStrtTime, 1); }

				$appComments 			= $schDataQryRes[$i]['sa_comments'];
				$appComments 			= htmlentities($appComments);
				$appcasetypeid			= $schDataQryRes[$i]['casetypeid'];
			}
		}
		$appInfo = array($appStrtDate,$appStrtDate_FORMAT,$facName,$facPhoneFormat,$procName,$doctorName,$doctorLastName,$appSiteShow,$appStrtTime,$appComments,$facStreet,$facCity,$facState,$facPostal_code,$faczip_ext,$appcasetypeid);
		return $appInfo;
	}

function showCurrency(){
	if(isset($GLOBALS['currency'])){
		return $GLOBALS['currency'];
	}else{
		return '$';
	}
}
function interPhoneMinLength(){
	if(isset($GLOBALS['phone_min_length']))
	return $GLOBALS['phone_min_length'];
	else
	return 10;
}
function clearBrace($val)
{
	if($val)
	{
		if(strpos($val,"("))
		{
			$ret_val_exp = explode("(",$val);
			$ret_val = $ret_val_exp[1];
			$ret_val_1 = explode(")",$ret_val);
			if(is_numeric($ret_val_1[0]))
			{
				return $ret_val_exp[0];
			}
			else
			{
				return $val."<br>";
			}
		}
		else
		{
			return $val."<br>";
		}
	}
}
function get_reffphysician_detail($reff_phy_id,$return_type = '', $extraField = ''){
	$reff_phy_full_name=$reff_fax_no=$reff_email_id="";
	$extraField = trim($extraField);
	$return_type= trim($return_type);
	$return_arr = array();
	if($reff_phy_id){

		$extraField = ($extraField) ? (substr($extraField,0,1) == ',' ? $extraField : ','.$extraField) : '';
		$qryReffPhysician="Select FirstName,LastName,MiddleName,physician_fax,physician_phone ".$extraField." from refferphysician WHERE physician_Reffer_id='".$reff_phy_id."' LIMIT 1";
		$resReffPhysician=imw_query($qryReffPhysician)or die(imw_error());

		if(imw_num_rows($resReffPhysician)>0){
			$rowReffPhysician=imw_fetch_assoc($resReffPhysician);
			$reff_last_name=str_ireplace("'","",$rowReffPhysician['LastName']);
			$reff_first_name=str_ireplace("'","",$rowReffPhysician['FirstName']);
			$reff_middle_name=str_ireplace("'","",$rowReffPhysician['MiddleName']);
			$reff_fax_no=$rowReffPhysician['physician_fax'];
			$reff_phone_no=$rowReffPhysician['physician_phone'];
			$reff_phy_full_name=$reff_last_name.", ".$reff_first_name." ".$reff_middle_name;
			$rowReffPhysician['full_name'] = trim($reff_phy_full_name);
			$rowReffPhysician['phone'] = $reff_phone_no;
			$return_arr = $rowReffPhysician;
		}
		return ($return_type == 'array' ? $return_arr : $reff_phy_full_name."@@".$reff_fax_no);
	}
}

function get_reffphysician_detail_email($reff_phy_id){
	$reff_phy_full_name=$reff_email_id="";
	if($reff_phy_id){
		$qryReffPhysician="Select FirstName,LastName,MiddleName,physician_email from refferphysician WHERE physician_Reffer_id='".$reff_phy_id."' LIMIT 1";
		$resReffPhysician=imw_query($qryReffPhysician)or die(imw_error());

		if(imw_num_rows($resReffPhysician)>0){
			$rowReffPhysician=imw_fetch_assoc($resReffPhysician);
			$reff_last_name=$rowReffPhysician['LastName'];
			$reff_first_name=$rowReffPhysician['FirstName'];
			$reff_middle_name=$rowReffPhysician['MiddleName'];
			$reff_email_id=$rowReffPhysician['physician_email'];
			$reff_phy_full_name=$reff_last_name.", ".$reff_first_name." ".$reff_middle_name;
		}
		return $reff_phy_full_name."@@".$reff_email_id;
	}
}

//make uri of remote server correct
function checkUrl4Remote($file=""){
	global $phpHTTPProtocol, $zOnParentServer;
	if($zOnParentServer==1){		//!empty($file) &&
		$file = $phpHTTPProtocol.$GLOBALS["ptParentServerInfo"]["SERVER_NAME"].$file;
	}
	return $file;
}

function getNumber($string){
	$num = preg_replace('/[^0-9]/','',$string);
	return $num;
}

// FUNCTION ACCEPT TIME AND CONVERT IT TO AM PM TIME
function getMainAmPmTime($time)
{
	if($time!=""){
		list($hour, $min)= explode(":", $time);
		$retun_str='';
		$ampm = "AM";
		if($hour > 12){
			$hour = (int)$hour - 12;
			if(strlen($hour) < 2){
				$hour = "0".$hour;
			}
			$ampm = "PM";
		}

		if($hour==12){ $ampm="PM"; }

		if(strlen($min) < 2){
			$min = "0".$min;
		}
		$return_str=$hour.":".$min." ".$ampm;
		return $return_str;
	}
}

//FUNCTION to add double quotes on both sides of string
function addDoubleQuotes($stringVal){
	return $stringVal='"'.$stringVal.'"';
}

//function to get procedure name
function getProcedureName($procId){
	$proc_name = "";
	$procId = (int) $procId;
	if($procId)
	{
		$qry = "select proc from slot_procedures where id = ".$procId." ";
		$sql = imw_query($qry);
		$row = imw_fetch_assoc($sql);
		$proc_name = $row['proc'];
	}
  return $proc_name;
}

//function to get user last date_of_service
function LastDOS($pt_id)
{
	if($pt_id)
	{
		$globalDateQry = get_sql_date_format();
		$qry = "select DATE_FORMAT(date_of_service, '".$globalDateQry."') as date_of_service_n,del_status from patient_charge_list where patient_id=$pt_id order by date_of_service desc limit 0,1";
		$qryRes = get_array_records_query($qry);

		if($qryRes[0]['date_of_service_n']){
			$dos=($qryRes[0]['del_status']==0) ? $qryRes[0]['date_of_service_n'] : '<span class="text-strike">'.$qryRes[0]['date_of_service_n'].'</span>';
		}
		unset($qryRes);
	}
	return ($dos) ? $dos : '00/00/0000';
}

function getPhysicianMenuArray($f=0,$flgCn="")
{
	if($flgCn == "cn"){
		$utId = implode(",",$GLOBALS['arrValidCNPhy']);
	}else{
		$utId = "1";
	}

	$arr=array();
	$provQry = "select lname,fname,mname,id from users where user_type IN (".$utId.") and delete_status = 0 order by lname,fname";
	$provSql = imw_query($provQry);
	while($provRt = imw_fetch_assoc($provSql))
	{
		$mrProviderName = $provRt['lname'].",&nbsp;".$provRt['fname']."&nbsp;".$provRt['mname'];
		$mrProviderName = (strlen($mrProviderName) > 30) ? substr($mrProviderName,0,28).".." : $mrProviderName;
		$id = $provRt['id'];
		if($f == 0){
			$arr[$mrProviderName] = array($mrProviderName,$arrEmpty,$mrProviderName."-".$id);
		}else if($f == 1){
			$arr[$id] = $mrProviderName;
		}
	}
	return $arr;
}

function get_mime($file) {
  if (function_exists("finfo_file")) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
    $mime = finfo_file($finfo, $file);
    finfo_close($finfo);
    return $mime;
  } else if (function_exists("mime_content_type")) {
    return mime_content_type($file);
  } else if (!stristr(ini_get("disable_functions"), "shell_exec")) {
    $file = escapeshellarg($file);
    $mime = shell_exec("file -bi " . $file);
    return $mime;
  } else {
    return false;
  }
}

function sexual_orientation(){

	$arr = array(
					"Lesbian, gay or homosexual" => array("value" => "Lesbian, gay or homosexual", "code" => "38628009"),
					"Straight or heterosexual" => array("value" => "Straight or heterosexual", "code" => "20430005"),
					"Bisexual" => array("value" => "Bisexual", "code" => "42035005"),
					"Other" => array("value" => "Other", "code" => "OTH"),
					"Unknown" => array("value" => "Unknown", "code" => "UNK"),
					"Declined to Specify" => array("value" => "Declined to Specify", "code" => "ASKU") );
	return $arr;
}

function gender_identity(){

	$arr = array(
					"Male" => array("value" => "Male", "code" => "446151000124109"),
					"Female" => array("value" => "Female", "code" => "446141000124107"),
					"Transgender Male" => array("value" => "Transgender Male", "code" => "407377005"),
					"Transgender Female" => array("value" => "Transgender Female", "code" => "407376001"),
					"Genderqueer" => array("value" => "Genderqueer", "code" => "446131000124102"),
					"Other" => array("value" => "Other", "code" => "OTH"),
					"Declined to Specify" => array("value" => "Declined to Specify", "code" => "ASKU") );
		return $arr;
}

function ag_severity(){

	$arr = array(
					"fatal" => array("value" => "Fatal", "code" => "399166001"),
					"mild" => array("value" => "Mild", "code" => "255604002"),
					"mild to moderate" => array("value" => "Mild to Moderate", "code" => "371923003"),
					"moderate" => array("value" => "Moderate", "code" => "6736007"),
					"moderate to severe" => array("value" => "Moderate to Severe", "code" => "371924009"),
					"severe" => array("value" => "Severe", "code" => "24484000"));
		return $arr;
}

function gender() {

	$qry = "Select * From gender_code Where is_deleted = 0  Order By gender_id";
	$sql = imw_query($qry);
	$cnt = imw_num_rows($sql);

	$return = array();
	if( $cnt > 0 )
	{
		while($row = imw_fetch_assoc($sql) )
		{
			$return[$row['gender_name']] = $row['gender_code'];
		}
	}

	return $return;
}

function marital_status() {

	$qry = "Select * From marital_status Where is_deleted = 0  Order by mstatus_id";
	$sql = imw_query($qry);
	$cnt = imw_num_rows($sql);

	$return = array();
	if( $cnt > 0 )
	{
		while($row = imw_fetch_assoc($sql) )
		{
			$return[] = $row['mstatus_name'];
		}
	}

	return $return;
}

function get_snomed_code($diagCodeForSnoMed,$tmpProblemDesc) {
	$snowmedCtCode = "";
	$alphaMatch =false;
	if(trim($diagCodeForSnoMed)) {
		$query = "SELECT d.snowmed_ct AS snowmed_ct_code FROM diagnosis_code_tbl d WHERE d.d_prac_code = '".$diagCodeForSnoMed."' ORDER BY diagnosis_id LIMIT 0,1";
		if(preg_match("/[a-z]/i", $diagCodeForSnoMed)){ //IF DIAGNOSIS CODE IS ICD10
			$alphaMatch = true;
			$query = "SELECT d.snowmed_ct AS snowmed_ct_code FROM icd10_data a INNER JOIN diagnosis_code_tbl d ON (d.d_prac_code = a.icd9) WHERE a.icd10 = '".$diagCodeForSnoMed."' ORDER BY a.id LIMIT 0,1";
		}
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		if($cnt <= 0 && $alphaMatch == true) {
			$query = "SELECT d.snowmed_ct AS snowmed_ct_code FROM icd10_data a INNER JOIN diagnosis_code_tbl d ON (d.d_prac_code = a.icd9) WHERE a.icd10_desc = '".$tmpProblemDesc."' ORDER BY a.id LIMIT 0,1";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
		}
		if($cnt > 0 )
		{
			$row = imw_fetch_assoc($sql);
			$snowmedCtCode = $row['snowmed_ct_code'];

		}
	}
	return $snowmedCtCode;
}

function get_checksum_key_val($file_pointer = '',$algo_type = 'sha256'){
	$return_str = '';
	if(empty($file_pointer)) return $return_str;
	$enable_sha = (isset($GLOBALS['SHA_FILE_VAL']) && empty($GLOBALS['SHA_FILE_VAL']) == false && (bool)$GLOBALS['SHA_FILE_VAL'] == true) ? $GLOBALS['SHA_FILE_VAL'] : false;
	if($enable_sha){
		if(file_exists($file_pointer)){
			$return_str = hash_file($algo_type,$file_pointer);
		}
	}
	return $return_str;
}

function get_operators()
{
	$qryUser = "SELECT id, fname, mname, lname, user_type FROM users where fname!='' and lname!='' AND delete_status = 0 ORDER BY fname,lname";
	$resUser = imw_query($qryUser);
	if($resUser)
	{
		while($arrUser = imw_fetch_array($resUser))
		{
			$name = $arrUser["lname"].', '.$arrUser["fname"].($arrUser["mname"] ? ' '.$arrUser["mname"] : '');
			$users[$arrUser['id']] = $name;
		}
	}
	return $users;
}

/******USED IN ATTACHMENT VIEWER TO DELETE UNZIPPED CONTENTS****/
function MakeDirectoryEmpty($path) {
	$files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? MakeDirectoryEmpty($file) : unlink($file);
	}
	rmdir($path);
	return;
}

/*******USED IN ATTACHMENT VIWER TO LIST UNZIPPED CONTENTS***/
function getDirContents($dir, &$results = array()){
    $files = scandir($dir);
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }
    return $results;
}

function getTestScan($patient_id,$form_id,$formName,$tId="",$flgPr="1"){

	//Get Table Fields
	list($field_FormId,$tbl_test,$field_Key,$testName,$f_ptId) = getTestFormFieldsNew($formName);

	//Get Test Id and FormId of Pt  --
	$strTestId = "";
	$strFormId = "";
	$sql = "SELECT ".$field_Key.",".$field_FormId." FROM ".$tbl_test." c1 ".
		    "WHERE c1.".$f_ptId."='".$patient_id."' AND purged='0' AND del_status='0' ";

	$rez = imw_query($sql);
	for($i=0;$row=imw_fetch_assoc($rez);$i++){
		//testid
		if(!empty($row[$field_Key])){
			$strTestId .= (!empty($strTestId)) ? ",":"";
			$strTestId .= $row[$field_Key];
		}
		//formId
		if(!empty($row[$field_FormId])){
			$strFormId .= (!empty($strFormId)) ? ",":"";
			$strFormId .= $row[$field_FormId];
		}
	}

	if(!empty($strTestId)){
		$strTestId = " c1.test_id IN (".$strTestId.") ";
	}

	if(!empty($strFormId)){
		$strFormId = "c1.form_id IN (".$strFormId.")";
	}

	$strTest = "";
	if(!empty($strTestId) || !empty($strFormId)){
		$strTest .= (!empty($strTestId)) ? "".$strTestId : "";
		if(!empty($strFormId)){
			$strTest .= (!empty($strTest)) ? " OR " : "";
			$strTest .= $strFormId;
		}
		if(!empty($strTest)){
			$strTest = "AND (".$strTest.")";
		}
	}

	//Get Test Id and FormId of Pt --

	$curScanId=$prevScanId=0;
	if(!empty($form_id) || !empty($tId)){
		//Current
		$str = "";
		$str2 = "";
		$grpBy = "";

		if(!empty($tId)){ //test id
			$str .= "AND test_id = '$tId' ";
			$str2 .= "AND test_id != '$tId' ";
			$grpBy = "GROUP BY test_id ";

			$sql = "SELECT scan_id FROM ".constant("IMEDIC_SCAN_DB").".scans ".
			 "WHERE patient_id = '$patient_id' ".
			 "AND image_form = '$formName' ".
			 "".$str.
			 "ORDER By scan_id DESC ".
			 "LIMIT 0,1 ";
			$row=sqlQuery($sql);
			$curScanId = ($row != false) ? $row["scan_id"] : 0 ;
		}

		if(empty($curScanId) && !empty($form_id)){ //formid
			$str .= "AND c1.form_id = '$form_id' ";
			$str2 .= "AND c1.form_id != '$form_id' ";
			$grpBy = "GROUP BY c1.form_id ";

			$sql = "SELECT c1.scan_id FROM ".constant("IMEDIC_SCAN_DB").".scans c1 ".
					"INNER JOIN ".$tbl_test." c2 ON c2.".$field_FormId." = c1.form_id ".
					"WHERE c1.patient_id = '$patient_id' ".
					 "AND c1.image_form = '$formName' ".
					 "".$str.
					"ORDER By c1.scan_id DESC ".
					"LIMIT 0,1 ";
			$row=sqlQuery($sql);
			$curScanId = ($row != false) ? $row["scan_id"] : 0 ;
		}
	}

	//Prev
	$prevScanId = 0;
	if($flgPr != "0"){
		$sql = "SELECT scan_id FROM ".constant("IMEDIC_SCAN_DB").".scans c1 ".
			   "WHERE c1.patient_id = '$patient_id' ".
			   "AND c1.image_form = '$formName' ".
			   "".$strTest.
			   "".$str2.
			   $grpBy.
			   "ORDER By c1.scan_id DESC ".
			   "LIMIT 0,1 ";
		$row=sqlQuery($sql);
		$prevScanId = ($row != false) ? $row["scan_id"] : 0 ;
	}
	if($formName=='TemplateTests') $prevScanId='';
	return array($curScanId,$prevScanId);
}

function getTestFormFieldsNew($testName,$fAssc="0"){
	switch($testName){
		case "VF":
			$field_FormId = "formId";
			$tbl_test = "vf";
			$field_Key = "vf_id";
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "VF-GL":
			$field_FormId = "formId";
			$tbl_test = "vf_gl";
			$field_Key = "vf_gl_id";
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "NFA":
		case "HRT":
			$field_FormId = "form_id";
			$tbl_test = "nfa";
			$field_Key = "nfa_id";
			$testName = "NFA"; //tst name in scan
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;
		case "OCT":
			$field_FormId = "form_id";
			$tbl_test = "oct";
			$field_Key = "oct_id";
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;

		case "OCT-RNFL":
			$field_FormId = "form_id";
			$tbl_test = "oct_rnfl";
			$field_Key = "oct_rnfl_id";
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;

		case "Pacchy":
		case "Pachy":
			$field_FormId = "formId";
			$tbl_test = "pachy";
			$field_Key = "pachy_id";
			$testName = "Pacchy";  //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "IVFA":
			$field_FormId = "form_id";
			$tbl_test = "ivfa";
			$field_Key = "vf_id";
			$f_ptId = "patient_id";
			$f_edt = "exam_date";

		break;
		case "ICG":
			$field_FormId = "form_id";
			$tbl_test = "icg";
			$field_Key = "icg_id";
			$f_ptId = "patient_id";
			$f_edt = "exam_date";

		break;
		case "Disc":
		case "Fundus":
			$field_FormId = "formId";
			$tbl_test = "disc";
			$field_Key = "disc_id";
			$testName = "Disc"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;

		case "discExternal":
		case "External/Anterior":
		case "External":
			$field_FormId = "formId";
			$tbl_test = "disc_external";
			$field_Key = "disc_id";
			$testName = "discExternal"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "Topogrphy":
		case "Topography":
			$field_FormId = "formId";
			$tbl_test = "topography";
			$field_Key = "topo_id";
			$testName = "Topogrphy"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "TestOther":
		case "Other":
		case "TemplateTests":
			$field_FormId = "formId";
			$tbl_test = "test_other";
			$field_Key = "test_other_id";
			$testName = "testOther"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "Laboratories":
		case "TestLabs":
		case "Labs":
			$field_FormId = "formId";
			$tbl_test = "test_labs";
			$field_Key = "test_labs_id";
			$testName = "testLabs"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "A/Scan":
		case "Ascan":
		case "A-Scan":
			$field_FormId = "form_id";
			$tbl_test = "surgical_tbl";
			$field_Key = "surgical_id";
			$testName = "Ascan"; //tst name in scan
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;

		case "iOLMaster":
		case "IOL Master":
		case "IOL_Master":
			$field_FormId = "form_id";
			$tbl_test = "iol_master_tbl";
			$field_Key = "iol_master_id";
			$testName = "IOL_Master"; //tst name in scan
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;

		case "B-Scan":
		case "BScan":
			$field_FormId = "formId";
			$tbl_test = "test_bscan";
			$field_Key = "test_bscan_id";
			$testName = "BScan"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";
		break;

		case "Cell Count":
		case "CellCount":
			$field_FormId = "formId";
			$tbl_test = "test_cellcnt";
			$field_Key = "test_cellcnt_id";
			$testName = "CellCount"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";
		break;
		case "GDX":
			$field_FormId = "form_id";
			$tbl_test = "test_gdx";
			$field_Key = "gdx_id";
			$f_ptId = "patient_id";
			$f_edt = "examDate";
		break;

		default:
			exit("NOT Defined: ".$testName);
		break;

	}

	//Test
	if($fAssc == 1){
		return array("formId"=>$field_FormId,"tbl"=>$tbl_test,"keyId"=>$field_Key,"testNm"=>$testName,"ptId"=>$f_ptId,"eDt"=>$f_edt);
	}else{
		return array($field_FormId,$tbl_test,$field_Key,$testName,$f_ptId,$f_edt);
	}
}

function insertScans($scanId_but,$scanId_prev,$tw,$winW,$winH,$formNm,$printstr=false){

	/*Check Templatebased tests*/
	$res_chkTemplate = imw_query("SELECT image_form,test_id FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id = '$scanId_but'");
	$rs_chkTemplate = imw_fetch_assoc($res_chkTemplate);
	$where_query_part = "";
	if($rs_chkTemplate['image_form']=='TemplateTests'){
		$where_query_part = " AND c2.test_id='".$rs_chkTemplate['test_id']."' ";
	}


	global $oSaveFile;
	global $STRPRINT;
	global $webServerRootDirectoryName;
	global $web_RootDirectoryName;
	//$hgt = (!empty($scanId_but) && !empty($scanId_prev)) ? "50%" : "100%";
	$hgt = "100%"; //"50%";

	$str = "";
	$str ="".
		"<!-- Images -->".
		"<table width=\"".$tw."\" height=\"100%\" valign=\"top\" border=\"0\" style=\"cursor:hand;\" >";

			$tmp="No Current Scan";
			if(!empty($scanId_but)) {
				//$tmp = getScanType($scanId_but);
				//$src = "logoImg.php?from=scanImage&scan_id=".$scanId_but;
				//$tmp = ($tmp == "application/pdf") ? "<iframe src=\"".$src."\" width='".$tw."' height='100%' ></iframe>" : "<img src=\"".$src."\" width=\"".$tw."\" height=\"100%\" alt=\"current scan\" >";
				$tw = $tw;
				$tmp="";
				$tmp.="<div style=\"width:".$tw.";\">";

				$sql= "SELECT DATE_FORMAT(c2.doc_upload_date, '".get_sql_date_format()." %H:%i:%s') docUploadDate, DATE_FORMAT(c2.rename_date, '".get_sql_date_format()."') reNamedDate,".
						"c2.file_type, c2.scan_id, c2.file_path, c2.multi_doc_upload_comment as cmnts, ".
						"DATE_FORMAT(c2.created_date, '".get_sql_date_format()." %H:%i:%s') created_date, c2.testing_docscan as cmnts2, ".
						"c2.scan_or_upload, c2.image_form, c2.test_id, c2.image_name as fileName ".
						"FROM ".constant("IMEDIC_SCAN_DB").".scans c1 ".

					"LEFT JOIN ".constant("IMEDIC_SCAN_DB").".scans c2
					ON ((c2.form_id = c1.form_id AND c1.form_id != 0) OR (c2.test_id = c1.test_id AND c1.test_id != 0))
					AND c2.image_form=c1.image_form AND c1.patient_id = c2.patient_id ".
					"WHERE c1.scan_id='".$scanId_but."' ".
					"AND c1.image_form='".$formNm."' ".$where_query_part.
					"AND c1.patient_id = '".$_SESSION["patient"]."' ".
					"ORDER BY CASE LOCATE('DICOM_FILES',c2.file_path) WHEN 0 THEN 0  ELSE c2.created_date END ASC, c2.created_date DESC, c2.scan_id DESC ";

				$arrFileToDelete = array();
				$rez = imw_query($sql);
				for($i=0;$row=imw_fetch_assoc($rez);$i++){

					//
					set_time_limit(10);

					$docUploadDate = $cmnts = $imageForm = $testId = $testDos = $dbFileName = $strFileName = "";
					$upType = $row["scan_or_upload"];
					$fileType = $row["file_type"];
					$scan_id = $row["scan_id"];
					$testId = $row["test_id"];
					$imageForm = trim($row["image_form"]);
					$testDos = trim($row["reNamedDate"]);
					$dbFileName = urldecode(trim($row["fileName"]));
					$aFileNameParts = explode(".", $dbFileName);
					$sFileExtension = strtolower(end($aFileNameParts));
					if(strlen($dbFileName) > 22){
						$strFileName = substr($dbFileName,0,22);
					}
					else{
						$strFileName = $dbFileName;
					}
					if(preg_replace('/[^0-9]/','',$testDos) == "00000000"){
						$qryGetDOSTest = "";
						switch ($imageForm):
							case "VF":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from vf where vf_id = '".$testId."'";
								break;
							case "VF-GL":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from vf_gl where vf_gl_id = '".$testId."'";
								break;
							case "NFA":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from nfa where nfa_id = '".$testId."'";
								break;
							case "OCT":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from oct where oct_id = '".$testId."'";
								break;
							case "OCT-RNFL":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from oct_rnfl where oct_rnfl_id = '".$testId."'";
								break;
							case "GDX":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from test_gdx where gdx_id = '".$testId."'";
								break;
							case "Pacchy":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from pachy where pachy_id = '".$testId."'";
								break;
							case "IVFA":
								$qryGetDOSTest = "select DATE_FORMAT(exam_date, '".get_sql_date_format()."') as testDOS from ivfa where vf_id = '".$testId."'";
								break;
							case "ICG":
								$qryGetDOSTest = "select DATE_FORMAT(exam_date, '".get_sql_date_format()."') as testDOS from icg where icg_id = '".$testId."'";
								break;
							case "Disc":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from disc where disc_id = '".$testId."'";
								break;
							case "discExternal":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from disc_external where disc_id = '".$testId."'";
								break;
							case "Topogrphy":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from topography where topo_id = '".$testId."'";
								break;
							case "CellCount":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from test_cellcnt where test_cellcnt_id = '".$testId."'";
								break;
							case "TestLabs":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from test_labs where test_labs_id = '".$testId."'";
								break;
							case "BScan":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from test_bscan where test_bscan_id = '".$testId."'";
								break;
							case "Ascan":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from surgical_tbl where surgical_id = '".$testId."'";
								break;
							case "IOL_Master":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from iol_master_tbl where iol_master_id = '".$testId."'";
								break;
							case "TestOther":
								$qryGetDOSTest = "select DATE_FORMAT(examDate, '".get_sql_date_format()."') as testDOS from test_other where test_other_id = '".$testId."'";
								break;
						endswitch;
						$testDos = "";
						if(empty($qryGetDOSTest) == false){
							$rsGetDOSTest = imw_query($qryGetDOSTest);
							if($rsGetDOSTest){
								if(imw_num_rows($rsGetDOSTest) > 0){
									$rowGetDOSTest = imw_fetch_array($rsGetDOSTest);
									$testDos = $rowGetDOSTest['testDOS'];
								}
								imw_free_result($rsGetDOSTest);
							}
						}
					}

					if($upType == "upload"){
						$docUploadDate = $row["docUploadDate"];
						$cmnts = $row["cmnts"];
					}else{
						$docUploadDate = $row["created_date"];
						$cmnts = $row["cmnts2"];
					}

					//if($fileType == "application/pdf"){$
					if($sFileExtension == "pdf"){
						$pathTopdf = "".$row["file_path"];
						$pathTopdf = $oSaveFile->getFilePath($pathTopdf, "i");
						if(file_exists($pathTopdf) == true){
							$imgW = $tw-20;

							//$src="images/test_pdf_Icon.png";
							$src = $GLOBALS['rootdir']."/chart_notes/common/pdf_thumb.php?pdf=".$pathTopdf."&size=".$imgW;

							//Remote server --
							if(constant("REMOTE_SYNC") == 1){
								if(!empty($_SESSION['patient_parent_server'])){
									//$src = checkUrl4Remote($src);
									global $phpHTTPProtocol;
									$src = $phpHTTPProtocol.$_SERVER["SERVER_NAME"].$src;
								}
							}
							//Remote server --


							$tmp.="<div><img src=\"".$src."\"    alt=\"current scan\" style=\"border:1px solid #CCC;\" onclick=\"showScansPop('".$scan_id."','".$scanId_prev."','".$_SESSION['wn_height']."','".$formNm."')\"></div>";
							if(preg_replace('[^0-9]','',$docUploadDate)!="00000000000000"){
								if($_SESSION['logged_user_type'] == 1  || $_SESSION['logged_user_type'] == 3){
									$tmp .= "<div id=dateDiv".$i." class=\"txt_11\" style=\"text-align:center; width:100%; background-color:#f3f3f3;position:relative;\">
											<input type=\"hidden\" id=\"hidScanId_".$i."\" name=\"hidScanId_".$i."\" value=\"$scan_id\" />
											<table cellpadding=\"0\" cellspacing=\"2\" border=\"0\">
												<tr>
													<td id=\"dateCbkSpan_".$i."\">
														<input type=\"checkbox\" name=\"del_test_img[]\" id=\"del_test_img[]\" value=\"".$row['scan_id']."\">
													</td>
													<td id=\"dateSpan_".$i."\" class=\"hand_cur\" onclick=\"show_rename_text_box(this)\">".$testDos."</td>".
													/*
													<td id=dateTextLabelSpan_".$i." style=\"display:none;\">
														<input class=\"txt_11\" type=\"text\" id=\"dateTextBox_".$i."\" style=\"width:75px;\" name=\"dateTextBox_".$i."\" value='".$testDos."' onblur=\"checkdate(this); save_hide_text_box(this,'".$testDos."');\"/>
													</td>
													<td id=\"dateRenameSaveTD_".$i."\" style=\"display:none;\"><img src=\"../../images/save_btn.png\" onclick=\"rename_process(document.getElementById('dateTextBox_".$i."'),document.getElementById('hidScanId_".$i."'))\"/></td>
													<td id=\"dateRenameProgressTD_".$i."\" style=\"display:none;\"><img src=\"../../images/ajax_rename.gif\"/></td>
													*/
													"<td id=\"fileSpan_".$i."\" title=\"".$dbFileName."\" align=\"left\">&nbsp;File: ".$strFileName."</td>
												</tr>
											</table>

											<div id=\"divedit\" style=\"display:none;border:1px solid black;position:absolute;top:0px;padding:2px;background-color:#f3f3f3;vertical-align:middle;\">
												<input class=\"txt_11\" type=\"text\" id=\"dateTextBox_".$i."\" style=\"width:75px;\" name=\"dateTextBox_".$i."\" value='".$testDos."' onblur=\"checkdate(this);\"/>
												<input class=\"txt_11\" type=\"text\" id=\"fileTextBox_".$i."\" style=\"width:75px;\" name=\"fileTextBox_".$i."\" value='".$strFileName."' />
												<img src=\"../../images/save_btn.png\" title=\"save\" class=\"hand_cur\" onclick=\"rename_process('".$i."')\" align=\"center\" />
												<strong title=\"cancel\" onclick=\"$('#divedit').hide();\" class=\"hand_cur\" >X</strong>
												<img src=\"../../images/ajax_rename.gif\" id=\"dateRenameProgressTD_".$i."\" style=\"display:none;\" />
											</div>

										</div>
										";
								}
								else{
									$tmp .= "<div id=dateDiv".$i." class=\"txt_11\" style=\"text-align:center; width:100%; background-color:#f3f3f3;\">
											<table cellpadding=\"0\" cellspacing=\"2\" border=\"0\">
												<tr>
													<td><input type=\"checkbox\" name=\"del_test_img[]\" id=\"del_test_img[]\" value=\"".$row['scan_id']."\"></td>
													<td>".$testDos."</td>
													<td title=\"".$dbFileName."\" align=\"left\">&nbsp;File: ".$strFileName."</td>
												</tr>
											</table>
										</div>
										";
								}
							}
						}
					}else{
						//$src = "logoImg.php?from=scanImage&scan_id=".$scan_id;
						$pathPrint ='';
						$imgW = $tw-20;
						$imgH = $tw;
						$pathToImages = "".$row["file_path"];
						$pathForPrint = $pathToImages;   // Variable Path for PDF Print

						$pathToImages = $oSaveFile->getFilePath($pathToImages, "i");
						$tempImgWH = "";

						// GET Folder Name and Large File Name for PDF Printing
						if(file_exists($pathToImages) == true){
							$imgDir = $oSaveFile->getFileDir($pathToImages);
							$imgPathInfo = pathinfo($pathToImages);
							$imgName = $imgPathInfo['basename'];
							if(!is_dir($imgDir."/thumb")){
								mkdir($imgDir."/thumb",0777,true);
							}
							$thumbPath = realpath($imgDir."/thumb")."/".$imgName;//die();

							$pathThumb = $oSaveFile->createThumbs($pathToImages,$thumbPath,$imgW,$imgH);

							$imgDim = getimagesize($pathToImages);
							$width = $imgDim[0];
							$height = $imgDim[1];
							$thumbWidth =	600;
							$thumbHeight = 	600;

							if($imgDim[0] > 600 || $imgDim[1] > 600) {
								if($thumbWidth < $thumbHeight){
									// calculate thumbnail size
									$height = floor( $height * ( $thumbWidth / $width ) );
									$width = $thumbWidth;
									if($height > $thumbHeight){
										$flgH = false;
									}
								}
								else{
									// calculate thumbnail size
									$width = floor( $width * ( $thumbHeight / $height ) );
									$height = $thumbHeight;
									if($width > $thumbWidth){
										$flgW = false;
									}
								}
							}
							// --------------------------------------------------

							if(is_array($pathThumb) == true){
								$src = $oSaveFile->getFilePath($row["file_path"], "w");
								$tempImgWH = "style=\"width:".$pathThumb["imgWidth"]."px; height:".$pathThumb["imgHeight"]."px;\"";
							}
							else{
								$pathThumb = "".$pathThumb;
								$pathThumb = $oSaveFile->getFilePath($pathThumb, "w2");
								$src = "".$pathThumb;
							}

							$pathPrint = $printstr ? data_path().$pathForPrint : data_path(1).$pathForPrint;

							//Remote server --
							if(constant("REMOTE_SYNC") == 1){
								if(!empty($_SESSION['patient_parent_server'])){
									global $phpHTTPProtocol;
									$pathPrint = $phpHTTPProtocol.$_SERVER["SERVER_NAME"].$pathPrint;
								}
							}
							//Remote server --

							$tmp.="<div style=\"text-align:left;\" ><img src=\"".$src."\"  $tempImgWH alt=\"current scan\" onclick=\"showScansPop('".$scan_id."','".$scanId_prev."','".$_SESSION['wn_height']."','".$formNm."');\"></div>";
							$tmpPrint.="<div style=\"text-align:center\"><img src=\"".$pathPrint."\" height=\"".$height."\" widht=\"".$width."\" ></div>";
							if(preg_replace('/[^0-9]/','',$docUploadDate)!="00000000000000"){
								if($_SESSION['logged_user_type'] == 1 || $_SESSION['logged_user_type'] == 3){
									$tmp .= "<div id=testDateDiv".$i." class=\"txt_11\" style=\"text-align:center; width:100%; background-color:#f3f3f3;position:relative;\">
												<input type=\"hidden\" id=\"hidScanId_".$i."\" name=\"hidScanId_".$i."\" value=\"$scan_id\" />
												<table cellpadding=\"0\" cellspacing=\"2\" border=\"0\">
													<tr>
														<td id=\"dateCbkSpan_".$i."\">
														<input type=\"checkbox\" name=\"del_test_img[]\" id=\"del_test_img[]\" value=\"".$row['scan_id']."\">
														</td>".
														"<td id=\"dateSpan_".$i."\"  class=\"hand_cur\" onclick=\"show_rename_text_box(this)\">".$testDos."</td>".
														/*
														"<td id=dateTextLabelSpan_".$i." style=\"display:none;\"><input class=\"txt_11\" type=\"text\" id=\"dateTextBox_".$i."\" style=\"width:75px;\" name=\"dateTextBox_".$i."\" value='".$testDos."' onblur=\"checkdate(this); save_hide_text_box(this,'".$testDos."');\"/></td>
														<td id=\"dateRenameSaveTD_".$i."\" style=\"display:none;\"><img src=\"../../images/save_btn.png\" onclick=\"rename_process(document.getElementById('dateTextBox_".$i."'),document.getElementById('hidScanId_".$i."'))\"/></td>
														<td id=\"dateRenameProgressTD_".$i."\" style=\"display:none;\"><img src=\"../../images/ajax_rename.gif\"/></td>".
														*/
														"<td id=\"fileSpan_".$i."\" title=\"".$dbFileName."\" align=\"left\">&nbsp;File: ".$strFileName."</td>
													</tr>
												</table>
												<div id=\"divedit\" style=\"display:none;border:1px solid black;position:absolute;top:0px;padding:2px;background-color:#f3f3f3;vertical-align:middle;\">
												<input class=\"txt_11\" type=\"text\" id=\"dateTextBox_".$i."\" style=\"width:75px;\" name=\"dateTextBox_".$i."\" value='".$testDos."' onblur=\"checkdate(this);\"/>
												<input class=\"txt_11\" type=\"text\" id=\"fileTextBox_".$i."\" style=\"width:75px;\" name=\"fileTextBox_".$i."\" value='".$strFileName."' />
												<img src=\"../../images/save_btn.png\" title=\"save\" class=\"hand_cur\" onclick=\"rename_process('".$i."')\" align=\"center\" />
												<strong title=\"cancel\" onclick=\"$('#divedit').hide();\" class=\"hand_cur\" >X</strong>
												<img src=\"../../images/ajax_rename.gif\" id=\"dateRenameProgressTD_".$i."\" style=\"display:none;\" />
												</div>
											</div>
											";
									$tmpPrint .= "<div style=\"width:100%; padding-top:2px;\">".$testDos."&nbsp;File: ".$strFileName."</div>";

								}
								else{
									$tmp .= "<div id=testDateDiv".$i." class=\"txt_11\" style=\"text-align:center; width:100%; background-color:#f3f3f3;\">
												<input type=\"hidden\" id=\"hidScanId_".$i."\" name=\"hidScanId_".$i."\" value=\"$scan_id\" />
												<table cellpadding=\"0\" cellspacing=\"2\" border=\"0\">
													<tr>
														<td><input type=\"checkbox\" name=\"del_test_img[]\" id=\"del_test_img[]\" value=\"".$row['scan_id']."\"></td>
														<td>".$testDos."</td>
														<td title=\"".$dbFileName."\" align=\"left\">&nbsp;File: ".$strFileName."</td>
													</tr>
												</table>
											</div>
											";
									$tmpPrint .= "<div style=\"width:100%; padding-top:2px;\">".$testDos."&nbsp;File: ".$strFileName."</div>";
								}

							}
						}
					}

					//Comments
					if(!empty($cmnts)){
						$tmp .= "<div id=cmntsDiv".$i." class=\"txt_11\" style=\"text-align:center; width:100%;
								background-color:#f3f3f3;\">
								".$cmnts."
								</div>
								";
						$tmpPrint .= "<div style=\"text-align:center; width:100%;
								background-color:#f3f3f3;\">
								".$cmnts."
								</div>
								";
					}

				}
				$tmp.="</div>";
			}

			$str .="<tr><td height=\"".$hgt."\" align=\"center\" valign=\"middle\" >".$tmp."</td></tr>";

			//Stopped as per #863 mantis --
			/*
			$tmp="No Previous Scan";
			if(!empty($scanId_prev)) {
				$tmp = getScanType($scanId_prev);
				$src = ($tmp == "application/pdf") ? "images/icon-pdf.png" : "logoImg.php?from=scanImage&scan_id=".$scanId_prev; //"<iframe src=\"".$src."\" width='".$tw."' height='100%' ></iframe>"
				$tmp = "<img src=\"".$src."\" width=\"".$tw."\" height=\"100%\" alt=\"previous scan\" onclick=\"showScansPop('".$scanId_but."','".$scanId_prev."')\">";
			}
			$str .="<tr><td height=\"".$hgt."\" align=\"center\" valign=\"middle\" >".$tmp."</td></tr>";
			*/
			// Stopped as per #863 mantis --

	$str .="</table>	".
		"<!-- Images -->";
	/*
	if(!empty($scanId_but) && $winW > 0){
		$str.="<script>".
			"window.resizeTo(parseInt(".$winW."+".$tw."+10),parseInt(".$winH."+100));".
			"</script>".
			"";
	}
	*/

	$hieght = $_SESSION['wn_height'] - 255;
	$str = "<div style=\"overflow-x:hidden; overflow:auto; width:290px; height:".$hieght."px\"><div>".$str."</div></div>";
	$strPrint = $str;
	$STRPRINT = $tmpPrint;	// THIS VARIABLE IS USED FOR PRINT OF DOCUMENT
	return ($tmp != "No Current Scan") ? $str : "" ;
}

function FormatDate_show($dt){
	if(!empty($dt))
	{
		//if(ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})",$dt,$regs))
		if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt,$regs))
		{
			$dt=$regs[2]."-".$regs[3]."-".$regs[1];
			return $dt;
		}
	}
	return $dt;
}

function FormatDate_insert($dt){
	if(!empty($dt)){
		if(preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/",$dt,$regs))
		{
			$dt=$regs[3]."-".$regs[1]."-".$regs[2];
		}else if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/",$dt,$regs)){
			$dt=$regs[1]."-".$regs[2]."-".$regs[3];
		}
	}

	return $dt;
}


function odLable($flgret="0"){
 	$ret='<font class="text_blue">OD</font>';
	if($flgret==1){return $ret;}else{print($ret);}
}
function osLable($flgret="0"){
	$ret='<font class="text_green">OS</font>';
 	if($flgret==1){return $ret;}else{print($ret);}
}
function ouLable($flgret="0"){
	$ret='<font class="text_purple">OU</font>';
 	if($flgret==1){return $ret;}else{print($ret);}
}

//Doctor Name
function showDoctorName($id,$flg="0")
{
	if(($id != 0) && !empty($id))
	{
		$sql = "SELECT lname, mname, fname, pro_suffix,id
				FROM users
				WHERE id = '$id';
				";
		$rez = sqlQuery($sql);

		$lname=trim($rez["lname"]);
		$mname=trim($rez["mname"]);
		$fname=trim($rez["fname"]);
		$pro_suffix = trim($rez["pro_suffix"]);

		$lname = !empty($lname) ? $lname.",&nbsp;" : "";
		$mname = !empty($mname) ? $mname."&nbsp;" : "";
		$fname = !empty($fname) ? $fname."&nbsp;" : "";
		$ps = $pro_suffix;

		if($flg=="2"){
			$name = $lname.$fname.$mname.$ps;
		}else if($flg=="1"){
			$name = $fname;
			$name .= !empty($lname) ? "&nbsp;".strtoupper(substr($lname,0,1))."" : "" ;
			$name = (strlen($name) > 30) ? substr($name,0,28).".." : $name;
		}else {
			$name = $lname.$fname.$mname;
		}

		return $name;
	}
	return "";
}

function SingleTdData($data,$flgret="0"){
	$ret = '<table style="width:750px;" class="border" cellpadding="0" cellspacing="0">
			<tr>
			<td style="width:750px;" >'.$data.'</td>
			</tr>
		</table>';

	if($flgret==1){return $ret;}else{print($ret);}
}
function DoubleTdData($lable,$data,$flgret="0"){
	$ret = '<table style="width:100%;"  class="paddingTop border" cellspacing="0" cellpadding="0">
			<tr>
					<td  style="width:15%;" class="text_lable" align="left" valign="top" nowrap><b>'.$lable.'</b></td>
					<td  class="text_value" align="left" valign="top" style="width:85%;">'.$data.'</td>
			</tr>
		</table>';
	if($flgret==1){return $ret;}else{print($ret);}
}

/****FUNCTION TO LOG ccd INCORPORATION *****/
function log_ccd_incorporaton($scan_doc_id,$patient_id,$section_name){
	$q = "INSERT INTO ccd_incorporate_log SET
		  scan_doc_tbl_id	= '".$scan_doc_id."',
		  patient_id		= '".$patient_id."',
		  section_done		= '".$section_name."',
		  done_by			= '".$_SESSION['authId']."',
		  done_on			= '".date('Y-m-d H:i:s')."'
		  ";
	//echo '<hr>'.$q.'<hr>';
	imw_query($q);
}

function getFolderDoc_sd($fol_cat_id,$pid){
	$rett = "";
	$fol_cat_id = (int)$fol_cat_id;
	$pid = (int)$pid;
	$sql= "SELECT ".

		" sdt.scan_doc_id, ".
		" sdt.doc_title, ".
		" sdt.doc_type, ".
		" sdt.pdf_url, ".
		" sdt.task_physician_id ".
		" FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt ".
		" WHERE sdt.folder_categories_id='".$fol_cat_id."' AND sdt.patient_id='".$pid."' ".
		" ORDER BY (sdt.upload_docs_date + sdt.upload_date) DESC,sdt.scan_doc_id DESC ";
	$rez = imw_query($sql);
	for($i=1;$row=imw_fetch_assoc($rez);$i++){

		if(!empty($row["scan_doc_id"])){
			// Scan doc table
			$scan_doc_id = $row["scan_doc_id"];
			$doc_title = $row["doc_title"];
			$doc_type = $row["doc_type"];
			$pdf_url = $row["pdf_url"];
			$tmpScanfile = (!empty($pdf_url)) ? $pdf_url : $doc_title ;
			$scnDocUnReadImage = '';
			if(intval($_SESSION['logged_user_type'])==1){//echo $row["task_physician_id"];echo "::".$_SESSION['authId']."<br>";
				if(($row["task_physician_id"]=='' || $row["task_physician_id"]=='0') ||  $row["task_physician_id"] == $_SESSION['authId'])
				$scnDocUnReadImage = scnDocUnReadImageFun($pid,'scan',$_SESSION['authId'],$row["scan_doc_id"],$fol_cat_id);
			}
			//$scnDocUnReadImage = scnDocUnReadImageFun($pid,'scan',$_SESSION['authId'],$row["scan_doc_id"],$fol_cat_id);
			$spanUnReadImg =  "<span id=\"spnUnreadDocNaviId".$row['scan_doc_id']."\">".$scnDocUnReadImage."</span>";
			if($scnDocUnReadImage) {
			/*$rett.= "
            <script>
				if(document.getElementById('unReadCatId$fol_cat_id')){
					document.getElementById('unReadCatId$fol_cat_id').innerHTML = '".$scnDocUnReadImage."';
				}
			</script>";*/
			?>
            <?php
			}
			$tmpScanfile = substr($tmpScanfile,0,15);
			//$tmpScanfile .= (strlen($tmpScanfile) > 15) ? ".." : "";//
			//if(!empty($scan_doc)){
			$spanUnReadImg =  "<span id=\"spnUnreadDocNaviId".$row['scan_doc_id']."\">".$scnDocUnReadImage."</span>";
			if(!empty($doc_title)){
				$rett.="<li class=\"sub_li naviFileName\" onclick=\"javascript:showFile('".$scan_doc_id."','".$doc_type."');hideUnRdDoc('".$row['scan_doc_id']."');\">".
						"<b class=\"bullsize glyphicon pdf-icon\"></b>".$spanUnReadImg./*$tmpScanfile*/ $doc_title.
						"</li>";
			}else if((strpos($pdf_url,'.jpg') === false) && ($pdf_url != '') && (!$doc_type)) {
				$rett.="<li class=\"sub_li naviFileName\" onClick=\"javascript:showFile_pdf_new('".$pdf_url."','".$pid."','".$scan_doc_id."');hideUnRdDoc('".$row['scan_doc_id']."');\">".
						"<b class=\"bullsize glyphicon pdf-icon\"></b>".$spanUnReadImg.$tmpScanfile.
						"</li>";
			}else if((strpos($pdf_url,'.jpg') === false) && ($pdf_url != '')) {
				$rett.="<li class=\"sub_li naviFileName\" onClick=\"javascript:showFile_pdf('".$pdf_url."','".$pid."');hideUnRdDoc('".$row['scan_doc_id']."');\">".
						"<b class=\"bullsize glyphicon pdf-icon\"></b>".$spanUnReadImg.$tmpScanfile.
						"</li>";
			}
			//}

		}

	}
	return $rett;

}

function getScanDocsCount($patient_id,$folder_id='',$check_fav = false){

	$patient_id = (int) $patient_id;
	if (!$patient_id ) return false;
	$folder_id = (int) $folder_id;

	$fav_qry = ($check_fav === true) ? " And fc.favourite = 1 " : "";
	$folder_qry = $folder_id ? " And sdt.folder_categories_id = ".$folder_id." " : "";

	//fc.folder_name, fc.folder_categories_id,
	//Group By sdt.folder_categories_id Having count(sdt.folder_categories_id) > 0
	$qry = "Select sdt.scan_doc_id From ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt
												Inner Join ".constant("IMEDIC_SCAN_DB").".folder_categories fc on (fc.folder_categories_id = sdt.folder_categories_id And fc.folder_status = 'active')
												Where sdt.patient_id='".$patient_id."' ".$folder_qry.$fav_qry."
												LIMIT 0, 1";
	$sql = imw_query($qry) or die(imw_error());
	$cnt = imw_num_rows($sql);

	return ($cnt > 0 ) ? true : false;
}

function getScanTree_sd($patient_id){
	$patient_id = (int) $patient_id;
	include_once($GLOBALS['fileroot']."/interface/common/docs_name_common.php");
	$divNavi = "";
	if(empty($patient_id)){return $divNavi;}
	$divNavi.="<ul id=\"treemenuScan\" class=\"treeview\" style=\"margin:0px;margin-left:-14px;margin-top:-7px;\">";

	//$c=1;
	$sql = "SELECT ".

		 "".constant("IMEDIC_SCAN_DB").".folder_categories.folder_name, ".
		 "".constant("IMEDIC_SCAN_DB").".folder_categories.folder_categories_id, ".
		 "".constant("IMEDIC_SCAN_DB").".folder_categories.parent_id, ".
		 "".constant("IMEDIC_SCAN_DB").".folder_categories.patient_id ".


		 "FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  ".
		 "WHERE ".constant("IMEDIC_SCAN_DB").".folder_categories.folder_status ='active' ".
		 "AND ((".constant("IMEDIC_SCAN_DB").".folder_categories.patient_id = '".$patient_id."' AND ".constant("IMEDIC_SCAN_DB").".folder_categories.parent_id = 0) OR (".constant("IMEDIC_SCAN_DB").".folder_categories.patient_id = 0 AND ".constant("IMEDIC_SCAN_DB").".folder_categories.parent_id = 0)) ".
		 "ORDER BY ".constant("IMEDIC_SCAN_DB").".folder_categories.folder_name  ";

	$rez = imw_query($sql);
	$dir_num = imw_num_rows($rez);
	$scan_doc_id_prev="";

	//START TREE STRUCTURE ABOVE THE SCAN DOCS
	$idUpper = "";
	foreach($pgTitleMainArr as $pgTitleMainName => $pgTitleRedirectScript) {
		$idUpper = str_ireplace(" ","_",$pgTitleMainName);
		if($pgTitleMainName=='Scan Docs') { break; }
		$folder_class = $docExistArray[$pgTitleMainName] ? $docExistArray[$pgTitleMainName] : 'submenu';
		$divNavi .="<li class=\"".$folder_class." pointer sub_li navifoldername\" title=\"".$pgTitleMainName."\" id=\"li_upper_".$idUpper."\">";
		$divNavi.="	<a class=\"naviFolderName \" onclick=\"".$pgTitleRedirectScript."\"><span id=\"unReadCatId".$cat_id."\"></span>".$pgTitleMainName." </a>";
		$divNavi.="	</li>";
	}
	//END TREE STRUCTURE ABOVE THE SCAN DOCS

	//START TREE STRUCTURE OF SCAN DOCS
	$divNavi .="<li grp=\"scanLi\" class=\"submenu sub_li navifoldername\" title=\"Scan Docs\" id=\"li\">";
	$divNavi.="	<a class=\"naviFolderName \" ><span id=\"unReadCatId".$cat_id."\"></span>Scan Docs </a>";
	$divNavi.="	<ul style=\"margin:0px;\">";
	for($i=1;$row = imw_fetch_assoc($rez);$i++){
		$folder_name = $row['folder_name'];
		$cat_id = (int)$row['folder_categories_id'];
		$parent_id = $row['parent_id'];
		//$patient_id_cat = $row['patient_id'];
		//$folder_name = substr($folder_name,0,15);
		//$folder_name .= (strlen($folder_name) > 15) ? ".." : "";

		$divNavi .="<li grp=\"scanLi\" class=\"sub_li navifoldername\" title=\"".$scan_file."\" id=\"li".$cat_id."\">";

		$divNavi.="	<a class=\"naviFolderName \" onclick=\"top.fmain.showFolder('".$cat_id."')\"><span id=\"unReadCatId".$cat_id."\"></span>".
					$folder_name.
				"</a>";

		$divNavi.="	<ul style=\"margin:0px;margin-left:-5px;\">";
		$divNavi.=getFolderDoc_sd($cat_id,$patient_id);
		$divNavi.=getScanTree_sd_sub($patient_id,$cat_id);
		$divNavi.="	</ul>";

		$divNavi.="	</li>";

	}
	$divNavi.="</ul></li>";
	//END TREE STRUCTURE OF SCAN DOCS

	//START TREE STRUCTURE BELOW THE SCAN DOCS
	$idLower = "";
	foreach($pgTitleMainArr as $pgTitleMainName=>$pgTitleRedirectScript) {
		$idLower = str_ireplace(" ","_",$pgTitleMainName);
		if($pgTitleMainName=='Scan Docs' || $showTNme==true) {
			$folder_class = $docExistArray[$pgTitleMainName] ? $docExistArray[$pgTitleMainName] .' pointer' : 'submenu';
			if($pgTitleMainName=='Scan Docs') {
				$showTNme=true;
				continue;
			}
			if($pgTitleMainName=='Consent Templates') {
				$divNavi .="<li grp=\"scanLi\" class=\"submenu sub_li navifoldername\" title=\"Templates \" id=\"li_lower_".$idLower."\">";
				$divNavi.="	<a class=\"naviFolderName \" ><span id=\"unReadCatId".$cat_id."\"></span>Templates </a>";
				$divNavi.="	<ul style=\"margin:0px;\">";
			}
			$divNavi .="<li grp=\"scanLi\" class=\"".$folder_class." sub_li navifoldername\" title=\"".$pgTitleMainName."\" id=\"li_lower_".$idLower."\">";
			$divNavi.="	<a class=\"naviFolderName \" onclick=\"".$pgTitleRedirectScript."\"><span id=\"unReadCatId".$cat_id."\"></span>".$pgTitleMainName." </a>";
			$divNavi.="	</li>";
			if($pgTitleMainName=='Pt. Instruction Templates') {
				$divNavi.="</ul></li>";
			}
		}
	}
	//END TREE STRUCTURE BELOW THE SCAN DOCS

	$divNavi.="</ul>";
	return $divNavi;

}


function getScanTree_sd_sub($ptID,$parent_id){
	$divNavi_sub = "";
		$sql = "SELECT ".

			 "".constant("IMEDIC_SCAN_DB").".folder_categories.folder_name, ".
			 "".constant("IMEDIC_SCAN_DB").".folder_categories.folder_categories_id, ".
			 "".constant("IMEDIC_SCAN_DB").".folder_categories.parent_id, ".
			 "".constant("IMEDIC_SCAN_DB").".folder_categories.patient_id ".


			 "FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  ".
			 "WHERE ".constant("IMEDIC_SCAN_DB").".folder_categories.folder_status ='active' ".
			 "AND ((".constant("IMEDIC_SCAN_DB").".folder_categories.patient_id = 0 AND ".constant("IMEDIC_SCAN_DB").".folder_categories.parent_id = ".$parent_id."  AND ".constant("IMEDIC_SCAN_DB").".folder_categories.parent_id != 0) OR(".constant("IMEDIC_SCAN_DB").".folder_categories.patient_id = '".$ptID."' AND ".constant("IMEDIC_SCAN_DB").".folder_categories.parent_id = ".$parent_id."  AND ".constant("IMEDIC_SCAN_DB").".folder_categories.parent_id != 0)) ".
			 "ORDER BY ".constant("IMEDIC_SCAN_DB").".folder_categories.folder_name  ";


		$rez = imw_query($sql);
		$dir_num = imw_num_rows($rez);
		$scan_doc_id_prev="";
		for($i=1;$row = imw_fetch_assoc($rez);$i++){
			$folder_name = $row['folder_name'];
			$cat_id = (int)$row['folder_categories_id'];
			$parent_id = $row['parent_id'];
			$patient_id_cat = $row['patient_id'];
			$divNavi_sub .="<li style=\"margin-left:5px;\" grp=\"scanLi\"  class=\"sub_li navifoldername\" title=\"".$scan_file."\" id=\"li".$cat_id."\">";

			$divNavi_sub.="	<a class=\"naviFolderName\" onclick=\"showFolder('".$cat_id."')\"><span id=\"unReadCatId".$cat_id."\"></span>".
						$folder_name.
					"</a>";
			$divNavi_sub.="	<ul style=\"margin:0px;\">";

			$divNavi_sub.=getFolderDoc_sd($cat_id,$ptID);

			$divNavi_sub.=getScanTree_sd_sub($ptID,$cat_id);

			$divNavi_sub.="	</ul>";
			$divNavi_sub.="	</li>";
			$prevCatId=$cat_id;

		}
	return $divNavi_sub;

}

function scnDocParentFolderIDs($fol_cat_id){
	$arrParentFolTmp = array();
	$parent_id = 0;
	do{
		$qry = "SELECT parent_id, alertPhysician, alertPhysician FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE folder_categories_id ='".$fol_cat_id."'";
		$res = imw_query($qry);
		while($row = imw_fetch_assoc($res)){
			 if(count($arrParentFolTmp)<=0){ //-----FOR FIRST TIME ONLY
			  	$arrParentFolTmp[] = $fol_cat_id;
			  }
			 $arrParentFolTmp[] = $fol_cat_id = $parent_id = $row['parent_id'];
		}
	}while($parent_id !=0 );
	return $arrParentFolTmp;
}
function scnDocUnReadImageFun($patient_id,$section_name,$session_provider_id,$scan_doc_id,$fol_cat_id='') {
	$scnDocUnReadImage = '';
	$qry = "SELECT alertPhysician FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE folder_categories_id ='".$fol_cat_id."'";
	$res = imw_query($qry);
	$row = imw_fetch_assoc($res);
	if($row['alertPhysician'] != 1){
		return '';
	}
	$arrParentFolIDs = scnDocParentFolderIDs($fol_cat_id);
	if($patient_id) {
		/*$qryScnId="SELECT scan_doc_id
					FROM provider_view_log_tbl
					WHERE patient_id='".$patient_id."'
						AND section_name='".$section_name."'
						AND provider_id='".$session_provider_id."'
						AND scan_doc_id = '".$scan_doc_id."'";*/
		$qryScnId="SELECT plt.scan_doc_id
					FROM provider_view_log_tbl plt
					JOIN  ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt  ON (plt.scan_doc_id 	= sdt.scan_doc_id)
					WHERE sdt.patient_id='".$patient_id."'
						AND sdt.scan_doc_id = '".$scan_doc_id."'
						AND plt.section_name='".$section_name."'
						AND plt.provider_id =  '".$session_provider_id."'
						";
		$resScnId=imw_query($qryScnId);
		$scnIdNumRow = imw_num_rows($resScnId);
		$scnDocUnReadImageStr = '';
		if($scnIdNumRow <=0) {
			$unReadImage = $GLOBALS['webroot'].'/library/images/sign.gif';
			$scnDocUnReadImage = '<img src="'.$unReadImage.'" height="13" vspace="0" border="0" align="middle" title="Unread Document">';
			for($i=0; $i<count($arrParentFolIDs); $i++){
				$parent_fol_cat_id = $arrParentFolIDs[$i];
				$scnDocUnReadImageStr1 .= "if(document.getElementById('unReadCatId$parent_fol_cat_id')){
											document.getElementById('unReadCatId$parent_fol_cat_id').innerHTML = '".$scnDocUnReadImage."';
											}
											";
			}
		}
		if(!empty($scnDocUnReadImageStr1)){
		$scnDocUnReadImageStr = "
            <script>$scnDocUnReadImageStr1
			</script>";
		}
	}
	return $scnDocUnReadImageStr.$scnDocUnReadImage;
}

function getChild($catID, $level="",$pid="")
{

	$query = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE parent_id=$catID and (patient_id='0' || patient_id='".$pid."') AND folder_status ='active' order by folder_name ";

	$resultSet = imw_query($query) or die('Error while retrieving categories : '.imw_error());

	$level++;

	$mainArr = array();

	$tempArr = array();

	if(imw_num_rows($resultSet))

	{

		while($row = imw_fetch_assoc($resultSet))

		{

			$categoryID = $row['folder_categories_id'];

			$categoryName = ucfirst($row['folder_name']);

			$parentID = $row['parent_id'];

			$space = '&nbsp;';

			for($cntr=0;$cntr<$level;$cntr++)

				$space .= "&nbsp;";

			$mainArr[$categoryID] = $space.'&gt;'.$categoryName;

			$tempArr = getChild($categoryID, $level);

			$mainArr = mergeArr($mainArr, $tempArr);

		}



	}

	return $mainArr;

}

function mergeArr($arr1, $arr2)

{

	$arr3 = array();

	foreach($arr1 as $key => $val)

		$arr3[$key] = $val;

	foreach($arr2 as $key => $val)

		$arr3[$key] = $val;

	return $arr3;

}

function scnfoldrCatIdFunNew($dBaseName,$categoryFolderNme) {
	$scnCatId='';
	$sqlQry = "SELECT folder_categories_id FROM ".$dBaseName.".folder_categories
			   WHERE folder_name='".addslashes($categoryFolderNme)."' AND parent_id='0' AND patient_id='0'
			   ORDER BY folder_categories_id";
	$sqlRes = imw_query($sqlQry) or die(imw_error());
	if(imw_num_rows($sqlRes)>0) {
		$sqlRow = imw_fetch_array($sqlRes);
		$scnCatId=$sqlRow['folder_categories_id'];

	}
	return $scnCatId;

}
function scnDocExistFun($dBaseName,$patient_id,$catId='') {//$dBaseName = constant("IMEDIC_SCAN_DB")
	$ChkAnyDocExistsNumRow=0;
	$andCatIdQry='';
	if($catId) { $andCatIdQry = " AND sdt.folder_categories_id='".$catId."' "; }
	if($patient_id) {
		$qryChkAnyDocExists="SELECT sdt.scan_doc_id FROM ".$dBaseName.".scan_doc_tbl sdt, ".$dBaseName.".folder_categories fc
							 WHERE sdt.folder_categories_id = fc.folder_categories_id
							 AND  sdt.patient_id = '".$patient_id."'
							 AND  fc.alertPhysician = '1'".$andCatIdQry;

		//$qryChkAnyDocExists="SELECT * from ".$dBaseName.".scan_doc_tbl WHERE patient_id='".$patient_id."'".$andCatIdQry;
		$resChkAnyDocExists=imw_query($qryChkAnyDocExists);
		$ChkAnyDocExistsNumRow = imw_num_rows($resChkAnyDocExists);
	}
	return $ChkAnyDocExistsNumRow;

}
function scnDocReadChkFun($patient_id,$section_name,$session_provider_id) {
	$tblNme = 'scan_doc_tbl';
	global $dbase;
	if($section_name=='scan') {
		$scnImgSrc = $GLOBALS['webroot'].'/library/images/scanDcs_green.png';

	}else if($section_name=='tests') {
		$scnImgSrc = $GLOBALS['webroot'].'/library/images/icons_eye_green.png';
		$tblNme = 'scans';
	}
	if($patient_id) {
		//$qryScnId="SELECT scan_doc_id from ".constant("IMEDIC_SCAN_DB").".".$tblNme." WHERE patient_id='".$patient_id."'";
		$qryScnId="SELECT DISTINCT(scan_doc_id) FROM provider_view_log_tbl WHERE patient_id='".$patient_id."' AND section_name='".$section_name."'";
		if($section_name=='scan') {
			$qryScnId = "SELECT DISTINCT(pvlt.scan_doc_id) FROM ".$dbase.".provider_view_log_tbl pvlt, ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt, ".constant("IMEDIC_SCAN_DB").".folder_categories fc
								WHERE pvlt.scan_doc_id=sdt.scan_doc_id
								AND pvlt.patient_id='".$patient_id."'
								AND pvlt.section_name='".$section_name."'
								AND fc.folder_categories_id=sdt.folder_categories_id
								AND fc.alertPhysician='1'";
		}
		$resScnId=imw_query($qryScnId);
		$scnIdNumRow = imw_num_rows($resScnId);
		$andScanDocIdQry = '';
		if($scnIdNumRow >0) {
			$qryChkAnyDocRead="SELECT DISTINCT(scan_doc_id) FROM provider_view_log_tbl WHERE patient_id='".$patient_id."' AND section_name='".$section_name."' AND provider_id='".$session_provider_id."'";
			if($section_name=='scan') {
				$qryChkAnyDocRead = "SELECT DISTINCT(pvlt.scan_doc_id) FROM ".$dbase.".provider_view_log_tbl pvlt, ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt, ".constant("IMEDIC_SCAN_DB").".folder_categories fc
									WHERE pvlt.scan_doc_id=sdt.scan_doc_id
									AND pvlt.patient_id='".$patient_id."'
									AND pvlt.section_name='".$section_name."'
									AND pvlt.provider_id='".$session_provider_id."'
									AND fc.folder_categories_id=sdt.folder_categories_id
									AND fc.alertPhysician='1'";
			}
			$resChkAnyDocRead=imw_query($qryChkAnyDocRead);

			$ChkAnyDocReadNumRow = imw_num_rows($resChkAnyDocRead);
			if($ChkAnyDocReadNumRow >0 && $ChkAnyDocReadNumRow!=$scnIdNumRow) {
				if($section_name=='scan') {
					$scnImgSrc = $GLOBALS['webroot'].'/library/images/scanDcs_active.png';
				}else if($section_name=='tests') {
					$scnImgSrc = $GLOBALS['webroot'].'/images/icons_eye_orange.png';
				}
			}else if($ChkAnyDocReadNumRow ==0) {
				if($section_name=='scan') {
					$scnImgSrc = $GLOBALS['webroot'].'/library/images/scanDcs_active.png';
				}else if($section_name=='tests') {
					$scnImgSrc = $GLOBALS['webroot'].'/library/images/icons_eye_orange.png';
				}
			}
		}
		/*else {
			if($section_name=='scan') {
				$scnImgSrc = $GLOBALS['webroot'].'/library/images/scanDcs_active.png';
			}else if($section_name=='tests') {
				$scnImgSrc = $GLOBALS['webroot'].'/images/icons_eye_orange.png';
			}
		}*/
	}
	return $scnImgSrc;
}
function folder_breadCrumb($topid,$bn)	{
	$qry="Select * from ".constant("IMEDIC_SCAN_DB").".folder_categories  where folder_categories_id='$topid'";
	$res=imw_query($qry);
	$row=imw_fetch_array($res);
	$foldername=$row['folder_name'];
	$pid = $row["parent_id"];
	$bn = "<a href=\"folder_category.php?cat_id=$topid\" class=\"text_10b\"> ".ucfirst($foldername)."</a> > ".$bn;
	if ($pid == 0) {
		$bn = "<a href=\"folder_category.php?cat_id=0\" class=\"text_10b\">Top</a> > ". $bn;
		$bn = substr($bn,0,strlen($bn)-2);
		echo $bn;
	} else {
		folder_breadCrumb($pid,$bn);
	}
}
function operatorIntialFun($oprt_id) {
	$oprtInitial='';
	if($oprt_id) {
		$qry = "SELECT UCASE(SUBSTRING(fname,1,1)) AS fname_firstLtr,UCASE(SUBSTRING(lname,1,1)) AS lname_firstLtr FROM users WHERE id='".$oprt_id."'";
		$res=imw_query($qry);
		$numRow = imw_num_rows($res);
		if($numRow>0) {
			$row = imw_fetch_array($res);
			$fname_firstLtr = $row['fname_firstLtr'];
			$lname_firstLtr = $row['lname_firstLtr'];
			$oprtInitial = $fname_firstLtr.$lname_firstLtr;
		}
	}
	return $oprtInitial;
}
//START FUNCTION TO CREATE LOG OF PROVIDER FOR SCAN/TEST....
function providerViewLogFunNew($scan_doc_id,$provider_id,$patient_id,$section_name) {
	if(isset($section_name) && $section_name!=''){$add_query = " AND section_name='".$section_name."'";}else{$add_query='';}
	$chk_sql= "SELECT id FROM provider_view_log_tbl where scan_doc_id='".$scan_doc_id."' AND provider_id='".$provider_id."' AND patient_id='".$patient_id."'".$add_query;
	$chk_res=imw_query($chk_sql);
	if(imw_num_rows($chk_res)<=0) {
		$insrtScnQry = "INSERT INTO provider_view_log_tbl SET
						  scan_doc_id 	= '".$scan_doc_id."',
						  patient_id 	= '".$patient_id."',
						  provider_id 	= '".$provider_id."',
						  section_name 	= '".$section_name."',
						  date_time 	= '".date('Y-m-d H:i:s')."'";
		$insrtScnRes=imw_query($insrtScnQry);// or die(imw_error());
	}
}
//END FUNCTION TO CREATE LOG OF PROVIDER FOR SCAN/TEST....

function get_image_prop($image_name,$tw,$th){
   $image_attributes=@getimagesize("$image_name");
   $ow=$image_attributes[0];
   $oh=$image_attributes[1];
//echo($ow."=$tw Ram W".$oh."Ram H".$th);

   if($ow<=$tw && $oh<=$th){
	   $ret[0]=$ow;
	   $ret[1]=$oh;
	   return($ret);
 	}else{
		$pc_width=$tw/$ow;
		$pc_height=$th/$oh;
		$pc_width=number_format($pc_width,2);
		$pc_height=number_format($pc_height,2);
		//echo("Percentage Width=".$pc_width."and Perscentage height=".$pc_height);
		if($pc_width<$pc_height){
			$rd_image_width=number_format(($ow*$pc_width),2);
			$rd_image_height=number_format(($oh*$pc_width),2);
			$ret[0]=$rd_image_width;
			$ret[1]=$rd_image_height;
			return($ret);
		}else if($pc_height<$pc_width){
			$rd_image_width=number_format(($ow*$pc_height),2);
			$rd_image_height=number_format(($oh*$pc_height),2);
			$ret[0]=$rd_image_width;
			$ret[1]=$rd_image_height;
			return($ret);
		}
	}
}

function upload_image_by_guru($pid,$doctitle,$original_file,$filename,$filetype,$filesize,$file_tmp,$vf,$folder_id,$editid,$url,$comments,$task_physician_id){
	global $oSaveFile;
	$comments=addslashes($comments);
	$arr = array(" ","&nbsp;","%20");
	$doctitle = str_replace($arr,"_",urldecode($doctitle));
	$doctitle =str_replace("'","",$doctitle);
	$sPhotoFileName = $filename;
	if ($sPhotoFileName) // file uploaded
	{	$aFileNameParts = explode(".", $sPhotoFileName);
		$sFileExtension = strtolower(end($aFileNameParts));
		if ($sFileExtension != "jpg" && $sFileExtension != "jpeg" && $sFileExtension!="gif" &&  $sFileExtension!="png"  &&  $sFileExtension!="pdf" &&  $sFileExtension!="tif" &&  $sFileExtension!="tiff")
		{
			die ("Choose a JPG/GIF/PNG/PDF/TIF/TIFF for the upload");
		}
	}
	$nPhotoSize = $filesize;
	$nPhototype=$filetype;
	$sTempFileName =$file_tmp;

	//Copy --
	$folderpath = "Folder/id_".$folder_id;
	$file_pointer = $oSaveFile->copyfile($original_file,$folderpath);
	//Copy --

	//$increase = imw_query("SET GLOBAL max_allowed_packet=1000000000");		// 1000MB
	$userauthorized = $_SESSION['authId'];
	if($editid=="")	{
		$query = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl  set
					patient_id='$pid',
					folder_categories_id='$folder_id',
					scandoc_operator_id ='$userauthorized',
					doc_upload_type='scan',
					scandoc_comment='$comments',
					task_physician_id='$task_physician_id',
					task_status ='0',
					upload_date =now(),
					doc_title='$doctitle',
					doc_type='$sFileExtension',
					doc_size='$nPhotoSize',
					vf='$vf',
					file_path = '".$file_pointer."',
					pdf_url = '$url'";
		$res=imw_query($query) or die(imw_error());
		$insertId = imw_insert_id();
	}
	else
	{
	   $query = "update ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl  set
				patient_id='".$pid."',
				folder_categories_id='".$folder_id."',
				doc_upload_type='scan',
				scandoc_comment='".$comments."',
				task_physician_id='".$task_physician_id."',
				upload_date =now(),
				doc_title='".$doctitle."',
				doc_type='".$sFileExtension."',
				doc_upload_type='scan',
				doc_size='".$nPhotoSize."',
				file_path = '".$file_pointer."',
				vf='$vf',pdf_url = '".$url."' where scan_doc_id='".$editid."'";
		$res=imw_query($query) or die(imw_error());
		$insertId =imw_insert_id();
	}
	//START CODE TO CREATE LOG OF SCAN
		$scanProviderLog = providerViewLogFunNew($insertId,$_SESSION['authId'],$_SESSION['patient'],'scan');
	//END CODE TO CREATE LOG OF SCAN
}

//delete all the subfolder and scan files for specific patient
function delete_folder($id){
	$qry="Select * from ".constant("IMEDIC_SCAN_DB").".folder_categories where parent_id='$id'";
	$res=imw_query($qry);
	while($row=imw_fetch_array($res)){
		$catid=$row['folder_categories_id'];
		if($row['parent_id']!=0){
			imw_query("Delete from ".constant("IMEDIC_SCAN_DB").".folder_categories where folder_categories_id='".$row['folder_categories_id']."'");
			imw_query("Delete from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where folder_categories_id='".$row['folder_categories_id']."'");
			delete_folder($row['folder_categories_id']);
		}
	}
	return true;
}//end here//end here

function formatDate4display($dt)
{
	if(!empty($dt))
	{
		//YYYY-MM-DD
		if(preg_match("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})",$dt,$regs))
		{
			$dt=$regs[2]."-".$regs[3]."-".$regs[1];
			return $dt;
		}
	}
	return $dt;
}

// GETTING RECORDS INSURANCE DATA
function getRecords_ins_data_con($tableName,$field,$id,$field2,$val2,$field3,$val3,$field4,$val4,$enc_dos){
	$qry = "select * from $tableName where $field = '$id'
		and $field2 = '$val2' and $field3 = '$val3' and $field4 = '$val4'
		and (date_format(effective_date,'%Y-%m-%d')<='$enc_dos')
		and (expiration_date = '0000-00-00 00:00:00'
		or date_format(expiration_date,'%Y-%m-%d') >= '$enc_dos')";
	$qryId = imw_query($qry);
	if(imw_num_rows($qryId)>0){
		$qryRes = imw_fetch_object($qryId);
	}
	return $qryRes;
}

// GETTING INSURANCE COMPANY DETAILS
function getInsGroupNumberState($id,$pid,$type,$date_of_service,$all_ins_comp=""){
	if($all_ins_comp==""){
		$ins_del_chk= " and insurance_companies.ins_del_status  = '0'";
	}
	$qry = "select insurance_data.* from insurance_data join insurance_companies
			on insurance_companies.id = insurance_data.provider
			where insurance_data.ins_caseid = '$id' and insurance_data.pid = '$pid'
			and insurance_data.type = '$type' and insurance_data.provider > '0'
			$ins_del_chk
			and date_format(insurance_data.effective_date,'%Y-%m-%d') <= '$date_of_service'
			and (insurance_data.expiration_date = '0000-00-00 00:00:00'
			or date_format(insurance_data.expiration_date,'%Y-%m-%d') >= '$date_of_service')
			order by insurance_data.actInsComp desc limit 0,1";
	$qryId = imw_query($qry);
	if(imw_num_rows($qryId)>0){
		$qryRes = imw_fetch_object($qryId);
	}
	return $qryRes;
}

function getValHcfaUb($content,$val,$startPos,$endPos,$ajustment,$dataType) {
	$newSpace = "";
	$totalLen = ($endPos - $startPos)+1;
	$valLen = strlen($val);
	$spaceCnt = ($totalLen - $valLen);
	for($q=0;$q<$spaceCnt;$q++) {
		$dataSpace = " ";
		if($dataType=="N") {
			$dataSpace = "0";
		}
		$newSpace .= $dataSpace;
	}
	if(trim($val)!='' && $ajustment == "right") {
		$val = $newSpace.$val;
	}else if(trim($val)!='' && $ajustment == "left") {
		$val = $val.$newSpace;
	}
	$a=0;
	for($i=$startPos; $i<=$endPos;$i++) {
		$content[$i-1] .= $val[$a];
		$a++;
	}
	return $content;
}

function arraySlice($array,$start,$end){
	$res = array_slice($array,$start,$end);
	return $res;
}

function getColumnsList($page='')
{
	$return = false;
	$arr = get_extract_record('users','id',$_SESSION['authId'],'column_settings');

	if( $arr['column_settings'] ) {
		$columnArr = unserialize($arr['column_settings']);
		$return = ($page && $columnArr[$page] ) ? explode(",",$columnArr[$page]) : $columnArr[$page];
	}
	return $return;
}

function consult_path_replace4pdf($consultTemplateData)
{
	$physical_path=data_path();
	$fileroot=$GLOBALS['fileroot'];

	$Host = $_SERVER['HTTP_HOST'];
	if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
	global $myExternalIP, $RootDirectoryName;

	$content = '';if(($topMargin==0 || $topMargin=="") && (strstr($consultTemplateData,"<page_header>"))){$topMargin=5;}
	$content ='<page backtop="'.$topMargin.'" backleft="'.$leftMargin.'" backbottom="15">'.$consultTemplateData.'</page>';

	$content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$content);
	/*if($RootDirectoryName==PRACTICE_PATH){
		$content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/',$physical_path.'/',$content);
	}else{
		//$content = str_ireplace('/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$protocol.$myExternalIP.'/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$content);
		$content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/',$physical_path.'/',$content);
	}
	$content = str_ireplace('/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$content);
	*/
	$content = str_ireplace($GLOBALS['webroot'].'/library/images/',$fileroot.'/library/images/',$content);
	$content = str_ireplace('../../../library/images/',$fileroot.'/library/images/',$content);
	$content = str_ireplace($GLOBALS['webroot']."/library/images/",$fileroot."/library/images/",$content);
	$content = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$content);
	$content = str_ireplace("interface/common/html2pdf/","",$content);
	$content = str_ireplace($GLOBALS['webroot']."/library/common/html_to_pdf/","",$content);
	$content = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$content);
	$content = str_ireplace($GLOBALS['webroot'].'/interface/common/'.$htmlFolder.'/','',$content);
	$content = str_ireplace($GLOBALS['webroot'].'/interface/common/html2pdf/','',$content);
	$content = str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$content);
	$content = str_ireplace('interface/common/html2pdf/','',$content);
	$content = str_ireplace('interface/common/new_html2pdf/','',$content);
	//$content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/',$physical_path.'data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/',$content);
	$content = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$content);
	$content = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/main/uploaddir/',$physical_path.'data/'.PRACTICE_PATH.'/',$content);
	$content = str_ireplace($protocol.$myExternalIP.$GLOBALS['webroot'].'/interface/main/uploaddir/',$physical_path.'data/'.PRACTICE_PATH.'/',$content);
	$content = str_ireplace($webServerRootDirectoryName.$GLOBALS['webroot'].'/interface/main/uploaddir/',$physical_path.'data/'.PRACTICE_PATH.'/',$content);
	$content = str_ireplace($GLOBALS['webroot'].'/interface/main/uploaddir/',$physical_path.'data/'.PRACTICE_PATH.'/',$content);
	$content = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/redactor/images/','',$content);
	$content = str_ireplace($GLOBALS['webroot'].'/redactor/images/','',$content);
	$content = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$content);
	$content = str_ireplace("../../interface/main/uploaddir/",$physical_path."data/".PRACTICE_PATH."/",$content);

	$content = str_ireplace("Â","",$content);
	$content = str_ireplace("�","",$content);
	$content = str_ireplace("&shy;","",$content);
	$content = rawurldecode($content); //For decoding %## codes like %20 => ' '

	return $content;
}

function getRespParty($pid){
	$pid = (int) $pid;
	$data = false;

	if( $pid ) {
		$q = "SELECT * from resp_party WHERE patient_id = ".$pid."";
		$r = imw_query($q);
		if($r && imw_num_rows($r) == 1)
			$data = imw_fetch_assoc($r);
	}
	return $data;
}

//Function Remove line breaks
function remove_line_breaks($str)
{
	return preg_replace("(\r\n|\n|\r)", " ", $str);
}

function getMSPTypes($return_type='array'){
	$MSP = array();
	$MSP['12'] 	= 'Working Aged';
	$MSP['13'] 	= 'End-Stage Renal Disease';
	$MSP['14'] 	= 'No-Fault/Auto';
	$MSP['15'] 	= 'Worker\'s Compensation';
	$MSP['16'] 	= 'PHS or Other Federal Agency';
	$MSP['41'] 	= 'Black Lung';
	$MSP['42'] 	= 'Veteran\'s Administration';
	$MSP['43'] 	= 'Disabled';
	$MSP['47'] 	= 'Other Liability';

	if($return_type=='array'){
		return $MSP;
	}else{
		$options = '<option value=""></option>';
		foreach($MSP as $code=>$value){
			$options .= '<option value="'.$code.'">'.$code.' - '.$value.'</option>';
		}
		return $options;
	}
}
//custom label table log for claris vision only
function custom_lbl_log($page)
{
	if(constant('PRACTICE_PATH')=='clarisvision' && date('Y-m-d')<='2018-08-30'){
		imw_query("insert into  scheduler_custom_labels_log set on_page='". imw_real_escape_string($page) ."',
		request='". serialize($_REQUEST) ."',
		session='". serialize($_SESSION) ."',
		datetime='".date('Y-m-d H:i:s')."'");
	}
}
function era_separator_replace($data){
	$data=str_replace('|','*',$data);
	$data=str_replace('^',':',$data);
	return $data;
}
function task_alerts() {

	if( $_SESSION['authId'] ) {
		$fields = "patientid, patient_name, reminder_date, changed_value as task, section_name, if(encounter_id > 0,encounter_id,'') as enc_id ";
		$qry = "Select $fields From tm_assigned_rules Where reminder_date = '".date('Y-m-d')."' And section_name = 'Accounting Notes' And status = 0 And FIND_IN_SET (".$_SESSION['authId'].",notes_users) ";

		$data = get_array_records_query($qry);

		return $data;
	}
	return false;
}

/**********USED IN BOSTON HL7 CODE************/
function check_remote_facility(){//TO CHECK WHETER THE CURRENT LOGIN FACILITY IS BELONGS TO MASTER SERVER OR NOT.
	$login_facility	= $_SESSION['login_facility'];
	$q = "SELECT f.name as facility_name, sl.server_name FROM facility f
		  JOIN server_location sl ON (f.server_location=sl.id) WHERE f.id = '".$login_facility."' AND f.server_location IN (1,2,3,4,5,6,7) AND sl.server_name!=''";
	$res	= imw_query($q);
	if($res && imw_num_rows($res)==1){
		$rs = imw_fetch_assoc($res);
		return $rs;
	}
	return false;
}

function get_abnormal_flag_fun(){
	$abnormal_flag = array('A' => 'Abnormal (applies to non-numeric results)','&gt;' => 'Above absolute high-off instrument scale',
	'H' => 'Above high normal','HH' => 'Above upper panic limits','&lt;' => 'Below absolute low-off instrument scale',
	'L' => 'Below low normal','LL' => 'Below lower panic limits','B' => 'Better--use when direction not relevant',
	'I' => 'Intermediate. Indicates for microbiology susceptibilities only.','MS' => 'Moderately susceptible. Indicates for microbiology susceptibilities only.',
	'null' => 'No range defined, or normal ranges don\'t apply','N' => 'Normal (applies to non-numeric results)',
	'R' => 'Resistant. Indicates for microbiology susceptibilities only.','D' => 'Significant change down','U' => 'Significant change up');

	return $abnormal_flag;
}

function getRecallApptInfo($patient_id,$providerIds=0){
	$appStrtDate = $appStrtTime = $doctorName = $facName = $procName = $andSchProvQry = "";

	if($providerIds) { $andSchProvQry = "AND sc.sa_doctor_id IN($providerIds)";}

	$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
					sc.sa_patient_app_status_id as appStatus, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.phone as facPhone,slp.proc as procName, sc.sa_comments
					FROM schedule_appointments sc
					LEFT JOIN users us ON us.id = sc.sa_doctor_id
					LEFT JOIN facility fac ON fac.id = sc.sa_facility_id
					LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid
					WHERE sa_patient_id = '".$patient_id."'
					AND sc.sa_app_start_date >= current_date()
					AND sc.sa_patient_app_status_id != '18'
					$andSchProvQry
					ORDER BY sc.sa_app_start_date ASC
					LIMIT 0,1";

	//$schDataQryRes = $this->mysqlifetchdata($schDataQry);
	$schDataQryRes[] = @imw_fetch_array(@imw_query($schDataQry));


	if(count($schDataQryRes)<=0) {
		$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
						sc.sa_patient_app_status_id as appStatus, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.phone as facPhone,slp.proc as procName, sc.sa_comments
						FROM schedule_appointments sc
						LEFT JOIN users us ON us.id = sc.sa_doctor_id
						LEFT JOIN facility fac ON fac.id = sc.sa_facility_id
						LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid
						WHERE sa_patient_id = '".$patient_id."'
						AND sc.sa_app_start_date <= current_date()
						AND sc.sa_patient_app_status_id != '18'
						$andSchProvQry
						ORDER BY sc.sa_app_start_date DESC
						LIMIT 0,1";

		//$schDataQryRes = $this->mysqlifetchdata($schDataQry);
		$schDataQryRes[] = @imw_fetch_array(@imw_query($schDataQry));
	}
	if(count($schDataQryRes)<=0) {
		$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
						sc.sa_patient_app_status_id as appStatus, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.phone as facPhone,slp.proc as procName, sc.sa_comments
						FROM schedule_appointments sc
						LEFT JOIN users us ON us.id = sc.sa_doctor_id
						LEFT JOIN facility fac ON fac.id = sc.sa_facility_id
						LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid
						WHERE sa_patient_id = '".$patient_id."'
						AND sc.sa_app_start_date <= current_date()
						$andSchProvQry
						ORDER BY sc.sa_app_start_date DESC
						LIMIT 0,1";

		//$schDataQryRes = $this->mysqlifetchdata($schDataQry);
		$schDataQryRes[] = @imw_fetch_array(@imw_query($schDataQry));
	}
	if(count($schDataQryRes)>0) {
		for($i=0;$i<count($schDataQryRes);$i++){
			$appStrtDate 			= $schDataQryRes[$i]['appStrtDate'];
			$appStrtDate_FORMAT 	= $schDataQryRes[$i]['appStrtDate_FORMAT'];
			$facName 				= $schDataQryRes[$i]['facName'];
			$facPhone 				= $schDataQryRes[$i]['facPhone'];
			$facPhoneFormat			= $facPhone;
			if(trim($facPhoneFormat)) {
				$facPhoneFormat = str_ireplace("-","",$facPhoneFormat);
				$facPhoneFormat = "(".substr($facPhoneFormat,0,3).") ".substr($facPhoneFormat,3,3)."-".substr($facPhoneFormat,6);
			}

			$procName 				= $schDataQryRes[$i]['procName'];
			$doctorName 			= $schDataQryRes[$i]['doctorName'];
			$doctorLastName 		= $schDataQryRes[$i]['doctorLastName'];

			$appSite 				= ucfirst($schDataQryRes[$i]['appSite']);
			$appSiteShow 			= $appSite;
			if($appSite == "Bilateral") {$appSiteShow="Both"; }

			$appStrtTime 			= $schDataQryRes[$i]['appStrtTime'];
			if($appStrtTime[0]=="0") { $appStrtTime = substr($appStrtTime, 1); }

			$appComments 			= $schDataQryRes[$i]['sa_comments'];
			$appComments 			= htmlentities($appComments);
		}
	}
	$appInfo = array($appStrtDate,$appStrtDate_FORMAT,$facName,$facPhoneFormat,$procName,$doctorName,$doctorLastName,$appSiteShow,$appStrtTime,$appComments);
	return $appInfo;
}

function get_Zeiss_details(){
	$forum_params = '';
	if(constant("ZEISS_FORUM")=="YES" && constant("ZEISS_API_PATH") !=""){
		$forum = imw_query("SELECT `zeiss_username`, `zeiss_password` FROM `users` WHERE `id`='".$_SESSION['authId']."'");
		$forum = imw_fetch_assoc($forum);
		if($forum['zeiss_username']!="" && $forum['zeiss_password']!=""){
			$forum_params = "'', '".$forum['zeiss_username']."', '".$forum['zeiss_password']."', '".addslashes(constant("ZEISS_API_PATH"))."'";
		}
	}
	return $forum_params;
}

function enable_web_sig_pad(){

	$browser = browser();

	$enable = false;

	if( $browser['name'] <> 'msie' and $browser['name'] <> 'ie' ) $enable = true;

	//else if( $browser['name'] == 'msie' and $browser['version'] > 10 ) $enable = true;

	return $enable;
}

function Minify_Html($Html)
{
   $Search = array(
    '/(\n|^)(\x20+|\t)/',
    '/(\n|^)\/\/(.*?)(\n|$)/',
    '/\n/',
    '/\<\!--.*?-->/',
    '/(\x20+|\t)/', # Delete multispace (Without \n)
    '/\>\s+\</', # strip whitespaces between tags
    '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
    '/=\s+(\"|\')/'); # strip whitespaces between = "'

   $Replace = array(
    "\n",
    "\n",
    " ",
    "",
    " ",
    "><",
    "$1>",
    "=$1");

$Html = preg_replace($Search,$Replace,$Html);
return $Html;
}

/**
 * Check DSS API is enable or disable
 */
if(!function_exists('isDssEnable')) {
	function isDssEnable() {
		if (defined('DSSAPI') && DSSAPI === true){
			return true;
		}
		return false;
	}
}

/**
 * Check UGA Finance API is enable or disable
 */
if(!function_exists('isUGAEnable')) {
	function isUGAEnable() {
		if (defined('UGAAPI') && UGAAPI === true){
			return true;
		}
		return false;
	}
}

function cpt_typeahead($s){
		$subqry = $subqry1 = '';
		$s = trim($s);
		if( $s ) {
			$subqry = " And (cpt_prac_code like '".$s."%')";
		}
		$return = array();
		$qry = "Select cpt_fee_id, cpt_prac_code, cpt_desc From cpt_fee_tbl Where cpt_cat_id > 0 And delete_status = 0 ".$subqry." Order By cpt_prac_code Asc, delete_status Desc";
		$sql = imw_query($qry);
		$cnt = imw_num_rows($sql);

		while($row=imw_fetch_assoc($sql)) {
			//$return[] = array('id'=>$row['cpt_prac_code'],'title'=>$row['cpt_prac_code'].'-'.$row['cpt_desc']);
			$return[] = $row['cpt_prac_code'];
		}
		return $return;
	}
function get_Next_PatientID($dpr_pt_data = array()){
		$arr_return = array();	$arr_return['error'] = '';	$arr_return['patient_id'] = '';
		if(constant('DIRECT_PT_REGISTRATION')=='NO' && constant('DIRECT_PT_REGISTRATION_URL')!=''){
			$dpr_curl_url = constant('DIRECT_PT_REGISTRATION_URL');
			$dpr_pt_data['providerId'] 	= $_SESSION['authId'];
			if(constant('REMOTE_SYNC')==1){
				$dpr_pt_data['src_server'] 	= remote_get_local_server_id();
			}
			$dpr_curl = curl_init();
			curl_setopt($dpr_curl, CURLOPT_URL, $dpr_curl_url);
			curl_setopt($dpr_curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($dpr_curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($dpr_curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($dpr_curl, CURLOPT_HEADER, FALSE);
			curl_setopt($dpr_curl, CURLOPT_POSTFIELDS, $dpr_pt_data);
			$dpr_output = curl_exec($dpr_curl);
			$dpr_array_result = array();
			$dpr_curl_error = curl_error($dpr_curl);
			if($dpr_curl_error==''){
				$dpr_array_result = unserialize($dpr_output);
			}
			//pre($dpr_array_result);exit;
			$dpr_curl_error_msg = 'Record Not Saved! Synchronization with Master Server is interrupted.';
			//pre($dpr_array_result);
			if($dpr_curl_error != '' || $dpr_array_result['error'] != ''){
				$arr_return['error'] = '<script type="text/javascript">
						top.fAlert(\''.$dpr_curl_error_msg.$dpr_array_result['error'].'\');
					</script>';
			}else if(intval($dpr_array_result['ptid'])>0){
				$arr_return['patient_id'] = intval($dpr_array_result['ptid']);
				$arr_return['src_server'] = intval($dpr_array_result['src_server']);
			}else{
				$arr_return['error'] = '<script type="text/javascript">
						top.fAlert(\''.$dpr_curl_error_msg.'\');
					</script>';
			}
		/*---REMOTE PT.REG CODE END---*/
		}else{
			$q = "select max(id) as patient_next_id from patient_data";
			$r = imw_query($q);
			$rs = imw_fetch_assoc($r);
			$arr_return['patient_id'] = $rs['patient_next_id'] + 1;
			if(constant('REMOTE_SYNC')==1){
				$arr_return['src_server'] 	= remote_get_local_server_id();
			}
		}
		return $arr_return;
	}

	function remote_get_local_server_id(){
		$id = '';
		$qry = "SELECT id FROM servers WHERE LOWER(server) = '".strtolower($GLOBALS["LOCAL_SERVER"])."'";
		$res = imw_query($qry);
		if($res && imw_num_rows($res)==1){
			$row = imw_fetch_assoc($res);
			$id = $row['id'];
		}
		return $id;
	}

/**
 * Return Zip/Postal Code Label
 */
if(!function_exists('getZipPostalLabel')) {
	function getZipPostalLabel() {
		$label = 'Zip Code';
		if (isset($GLOBALS['phone_country_code']) && $GLOBALS['phone_country_code'] == 0){
			$label = 'Post Code';
		}
		echo $label;
	}
}

/**
 * Return No. for UK and # for other
 */
if(!function_exists('getHashOrNo')) {
	function getHashOrNo($echo = true) {
		$return = '#';
		if (isset($GLOBALS['phone_country_code']) && $GLOBALS['phone_country_code'] == 0) {
			$return = 'No.';
		}
		if($echo == true) {
			echo $return;
		} else {
			return $return;
		}
	}
}

function ageCalculator($dob){
	if(!empty($dob)){
        $birthdate = new DateTime($dob);
        $today   = new DateTime('today');
        $age = $birthdate->diff($today);
        return $age;
    }else{
        return 0;
    }
}
//
function check_img_mime($tmpname){

	if( !check_phpExt() ) return false;

	$finfo = finfo_open( FILEINFO_MIME_TYPE );
	$mtype = finfo_file( $finfo, $tmpname );
	if(strpos($mtype, 'image/') === 0){
		return true;
	} else {
		return false;
	}
	finfo_close( $finfo );
}

function check_phpExt(){
	if (!extension_loaded('fileinfo')) {
		// dl() is disabled in the PHP-FPM since php7 so we check if it's available first
		if(function_exists('dl')){
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				if (!dl('fileinfo.dll')) {
					return false;
				} else {
					return true;
				}
			} else {
				if (!dl('fileinfo.so')) {
					return false;
				} else {
					return true;
				}
			}
		} else {
			return false;
		}
	} else {
		return true;
	}
}

function check_txt_mime($tmpname){
	if( !check_phpExt() ) return false;
	$finfo = finfo_open( FILEINFO_MIME_TYPE );
	$mtype = finfo_file( $finfo, $tmpname );
	if($mtype=='text/plain'){
		return true;
	} else {
		return false;
	}
	finfo_close( $finfo );
}

function wv_check_mime($str, $file_tmp){
	$ar = array();
	if($str=="xml"){$ar = array("application/xml","text/xml");}
	else if($str=="img+pdf" || $str=="img" || $str=="img+pdf+doc"){
		$ar = array("image/gif", "image/jpeg", "image/png");
		if($str=="img+pdf" || strpos($str,"+pdf")!==false){$ar[] = "application/pdf";}
		if(strpos($str,"+doc")!==false){
				$ar[] = "application/msword";
				$ar[] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
				$ar[] = "application/zip";
				$ar[] = "text/plain";
				$ar[] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
		}
	}

	$flg_upld=0;
	if(check_phpExt()){
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mtype = finfo_file( $finfo, $file_tmp );
		if(in_array($mtype, $ar)){$flg_upld=1;}
	}

	return $flg_upld;
}

function get_copay_field($fields = '*') {
	$fields = trim($fields);
	$fields = $fields ? $fields : '*';

	$query = "select ".$fields." from copay_policies where policies_id = '1'";
	$sql = imw_query($query);
	$row = imw_fetch_assoc($sql);

	return $row;
}

function patient_monitor_daily($action,$pt='0',$sch_id='0'){//Log only if patient in session and today's appt is checked-in.
	$clientPCName = 'N/A';
	if($_COOKIE['clientPCName']!=""  && empty($_COOKIE['clientPCName']) == false){
		$clientPCName=$_COOKIE['clientPCName'];
	}
	$pt = (empty($pt) || (int)$pt==0) ? $_SESSION['patient'] : $pt;
	$u = $_SESSION["authId"];
	$ut = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
	$today = date('Y-m-d');
	$now = date('Y-m-d H:i:s');
	//------CHECKING TODAY'S CHECKED IN APPT---
	if(!$pt) return;
	$q = "SELECT id FROM schedule_appointments WHERE sa_patient_id='".$pt."' AND sa_patient_app_status_id='13' AND sa_app_start_date = '".$today."'";
	$res=imw_query($q);
	if($sch_id==0 && (!$res || imw_num_rows($res)==0) && !in_array($action,array('PATIENT_LOAD'))) return;
	else{
		if(empty($sch_id) || (int)$sch_id==0){
			while($rs = imw_fetch_assoc($res)){
				$sch_id = $rs['id'];
			}
		}
		if($sch_id || in_array($action,array('PATIENT_LOAD'))){
			$cols = "SET user_id = '".$u."', user_type_id = '".$ut."', scheduler_appt_id = '".$sch_id."', ";
			$cols .= "patient_id = '".$pt."', action_name = '".$action."', action_date_time = '".$now."', app_room='".imw_real_escape_string($clientPCName)."'";
			$q = "INSERT INTO patient_monitor_daily ".$cols;
			$res=imw_query($q);

			$q = "INSERT INTO patient_monitor ".$cols;
			$res=imw_query($q);
		}
	}
}

function getiMMStatus($c){
	switch($c){
		case '1':	$action='READY FOR DOCTOR'; 		break;
		case '2':	$action='READY FOR TECHNICIAN'; 	break;
		case '3':	$action='READY FOR TEST'; 			break;
		case '4':	$action='READY FOR WAITING ROOM'; 	break;
		case '5':	$action='DONE'; 					break;
		case '6':	$action='DONE'; 					break;
		default:	$action='';
	}
	return $action;
}

function getFacilityByDOS($patientId, $dateOfService){
	$facility_name="Not defined";
	$facilityQuery = "select sa_facility_id from schedule_appointments where sa_patient_id='".$patientId."' and sa_app_start_date <= '".$dateOfService."' order by sa_app_start_date desc limit 0, 1";
	$facilityResult = imw_query($facilityQuery) or die(imw_error()." - ".$facilityQuery);
	$row = imw_fetch_assoc($facilityResult);
	$appt_facility=$row['sa_facility_id'];
	if($appt_facility){
		$QryMasterFacility = "SELECT pft.facility_name as facility_name from facility as fc inner join pos_facilityies_tbl as pft on(pft.pos_facility_id = fc.fac_prac_code) where fc.id = '".$appt_facility."' LIMIT 0,1";
		$ResMasterFacility = imw_query($QryMasterFacility);
		if(imw_num_rows($ResMasterFacility)>0){
			$RowMasterFacility = imw_fetch_assoc($ResMasterFacility);
			$facility_name = $RowMasterFacility["facility_name"];
		}
	}
	return $facility_name;
}


/* Return POS Facility Group Enabled/Disabled */
if(!function_exists('isPosFacGroupEnabled')) {
	function isPosFacGroupEnabled() {
		$facility_group = false;
		if (isset($GLOBALS['POS_FACILITY_GROUP']) && $GLOBALS['POS_FACILITY_GROUP'] == 1){
			$facility_group = true;
		}
		return $facility_group;
	}
}

function pos_facility_group($req='',$selected_val=array() ){
    $result=array();
    $options='';
	$q="select pos_fac_grp_id,pos_facility_group from pos_facility_group where delete_status=0";
	$res=imw_query($q);
	if(imw_num_rows($res)>0){
		while($rs=imw_fetch_assoc($res)){
            $sel = ($selected_val!='' && in_array($rs['pos_fac_grp_id'], $selected_val) ) ? "selected" : "";
			$result[$rs['pos_fac_grp_id']]=$rs;
            $options.='<option value="'.$rs['pos_fac_grp_id'].'" '.$sel.'>'.$rs['pos_facility_group'].'</option>';
		}
	}
    if($req=='options') {return $options;} else {return $result;}

}

//The function returns the no. of business days (Mon-Fri) between two dates and it skips the holidays
function getWorkingDaysWithin($startDate,$endDate,$holidays=array()){
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
//    $days = ($endDate - $startDate) / 86400 + 1;
	$days = ($endDate - $startDate) / 86400;// + 1; // NOT INCLUDING 1 BECAUSE NOT COUNTING DATE OF SERVICE. (TS: 10-oct-2019)

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)

        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
}

function write_my_failures($query="", $error=""){//function to log any failed attempt log.
	$f_path = data_path().'MyFailures';
	if(!is_dir($f_path) || !file_exists($f_path)) mkdir($f_path, 755, true);

	$bt = debug_backtrace();

	$content = 'TimeStamp: '.date('Y-m-d H:i:s')."\n";
	$content .= "Files List:\n";

	foreach($bt as $file)
	{
		$details = implode("\n", $file['args']);
		$details = str_replace("\n", "\n\t\t\t", $details);

		$content .= "\tFile: ".$file['file']."\n";
		$content .= "\t\tLine: ".$file['line']."\n";
		$content .= "\t\tFunction: ".$file['function']."\n";
		$content .= "\t\tDetails: ".$details;
		$content .= "\n";
	}

	$error = str_replace("\n", "\n\t", $error);

	$content .= 'Query: '.$query."\n";
	$content .= 'Error Message: '.$error;
	$content .= "\n===================================================\n";

	file_put_contents($f_path.'/Error '.date('d-m-Y').'.txt', $content, FILE_APPEND);
}

function cancel_future_appointments($pid){

	$pid = (int) $pid;
	$return = 0;
	if( $pid )
	{
		$qrySA = "Select * From schedule_appointments Where sa_patient_id = ".(int)$pid." and sa_patient_app_status_id NOT IN (18,11,13) and sa_app_start_date >= '".date('Y-m-d')."' ";
		$sqlSA = imw_query($qrySA) or die(imw_error());
		$apptIDArr = $apptIDArrCancel = $apptIDArrNoShow = array();
		$sch_comment = "Patient marked as deceased.";
		while( $resSA = imw_fetch_assoc($sqlSA)) {
			$prev_status = (int)$resSA['sa_patient_app_status_id'];

			if( $prev_status == 3 && $resSA['sa_app_start_date']==date('Y-m-d') ) continue;

			$status = ($resSA['sa_app_start_date']==date('Y-m-d') && $prev_status <> 201  ) ? 3 : 18;

			if( $status == 3 ) $apptIDArrNoShow[] =  $resSA['id'];
			else $apptIDArrCancel[] = $resSA['id'];
			// Insert record into previous status table
			$insPrevStatusQry = "Insert into previous_status set sch_id = ".$resSA['id'].",
										patient_id = ".$resSA['sa_patient_id'].",
										status_time='".date("H:i:s")."',
										status_date='".date("Y-m-d")."',
										status = ".$status.",
										old_status = ".$prev_status.",
										old_date='".$resSA['sa_app_start_date']."',
										old_time='".$resSA['sa_app_starttime']."',
										old_appt_end_time='".$resSA['sa_app_endtime']."',
										old_provider='".$resSA['sa_doctor_id']."',
										old_facility='".$resSA['sa_facility_id']."',
										oldMadeBy='".$resSA['sa_madeby']."',
										old_procedure_id='".$resSA['procedureid']."',
										statusChangedBy='".addslashes($_SESSION['authUser'])."',
										dateTime='".date("Y-m-d H:i:s")."',
										new_facility='".$resSA['sa_facility_id']."',
										new_provider='".$resSA['sa_doctor_id']."',
										new_appt_date='".$resSA['sa_app_start_date']."',
										new_appt_start_time='".$resSA['sa_app_starttime']."',
										new_appt_end_time='".$resSA['sa_app_endtime']."',
										new_procedure_id='".$resSA['procedureid']."',
										change_reason = '".addslashes($sch_comment)."',
										statusComments = '".$resSA['sa_comments']."',
										oldStatusComments = '".$resSA['sa_comments']."' ";
			$insPrevStatusSql = imw_query($insPrevStatusQry) or die(imw_error());
		}

		$sch_ids1 = $sch_ids2 = "";
		if( is_array($apptIDArrCancel) && count($apptIDArrCancel) > 0 ) {
			$return = 1;
			$sch_ids1 = implode(",",$apptIDArrCancel);
			$updtSAQry = "Update schedule_appointments set sa_patient_app_status_id = 18 , sa_patient_app_show=0 Where id IN (".$sch_ids1.") ";
			$updtSASql = imw_query($updtSAQry) or die(imw_error());

			$delSFAQry = "Delete From schedule_first_avail Where sch_id IN (".$sch_ids1.") ";
			$delSFASql = imw_query($delSFAQry) or die(imw_error());
		}

		if( is_array($apptIDArrNoShow) && count($apptIDArrNoShow) > 0 ) {
			$return = 1;
			$sch_ids2 = implode(",",$apptIDArrNoShow);
			$updtSAQry = "Update schedule_appointments set sa_patient_app_status_id = 3 , sa_patient_app_show=0 Where id IN (".$sch_ids2.") ";
			$updtSASql = imw_query($updtSAQry) or die(imw_error());

			$delSFAQry = "Delete From schedule_first_avail Where sch_id IN (".$sch_ids2.") ";
			$delSFASql = imw_query($delSFAQry) or die(imw_error());

		}
	}

	return $return;

}

function is_deceased($pid){
	$pid = (int)$pid;
	$patientDeceased = false;
	if( $pid ) {
		$ptTempQry = "select patientStatus from patient_data where id = ".(int)$pid." ";
		$ptTempRes = imw_query($ptTempQry);
		if(imw_num_rows($ptTempRes)>0) {
			$ptTempRow = imw_fetch_assoc($ptTempRes);
			if( $ptTempRow["patientStatus"] == 'Deceased' )
				$patientDeceased = true;
		}
	}

	return $patientDeceased;
}


/* Return default logged in user selected in accounting notes Enabled/Disabled */
if(!function_exists('isDefaultUserSelected')) {
	function isDefaultUserSelected() {
		$return = false;
		if (isset($GLOBALS['SELECT_DEFAULT_LOGGED_IN_USER']) && $GLOBALS['SELECT_DEFAULT_LOGGED_IN_USER'] == 1){
			$return = true;
		}
		return $return;
	}
}

function format_ref_data($data) {
	$address = "";
	$address = core_extract_user_input($data['Address1']);
	$address .= ($data['Address2']!= "") ? ($address?", ":'').core_extract_user_input($data['Address2']) : "";
	$address .= ($data['City']!= "") ? ($address?", ":'').core_extract_user_input($data['City']) : "";
	$address .= ($data['State']!= "" || $data['ZipCode'] != "" ) ? ($address?", ":'').core_extract_user_input($data['State'])." ".core_extract_user_input($data['ZipCode']) : "";
	$address .= $address? "<br>" : "";
	$address .= ($data['physician_phone']) ? "Phone: ".$data['physician_phone']."<br>" : "";
	$address .= ($data['physician_fax'] ) ? "Fax: ".$data['physician_fax']."<br>" : "";
	$address .= ($data['PractiseName']) ? "Practice Name: ".$data['PractiseName']."<br>" : "";
	$address .= ($data['comments']!= "") ? "Comments: ".$data['comments']."<br>" : "";

	$address = trim($address);

	return $address;
}


/* ERP Patient Portal check if enabled or disabled */
if(!function_exists('isERPPortalEnabled')) {
    function isERPPortalEnabled() {
        $return = false;
        if (isset($GLOBALS['ERP_API_PATIENT_PORTAL']) && $GLOBALS['ERP_API_PATIENT_PORTAL'] == 1){
            $rabbitMQ = true;
            if (!defined('RABBITMQ_HOST') || RABBITMQ_HOST === '' ||
                    !defined('RABBITMQ_PORT') || RABBITMQ_PORT === '' ||
                    !defined('RABBITMQ_USER') || RABBITMQ_USER === '' ||
                    !defined('RABBITMQ_PASS') || RABBITMQ_PASS === '' ||
                    !defined('RABBITMQ_REQUEST_EXCHANGE') || RABBITMQ_REQUEST_EXCHANGE === '' ||
                    !defined('RABBITMQ_RESPONSE_EXCHANGE') || RABBITMQ_RESPONSE_EXCHANGE === '' ||
                    !defined('RABBITMQ_EXCHANGE_TYPE') || RABBITMQ_EXCHANGE_TYPE === '' ||
                    !defined('RABBITMQ_DURABLE') || RABBITMQ_DURABLE === '' ||
                    !defined('RABBITMQ_REQUEST_ROUTING_KEY') || RABBITMQ_REQUEST_ROUTING_KEY === '' ||
                    !defined('RABBITMQ_RESPONSE_ROUTING_KEY') || RABBITMQ_RESPONSE_ROUTING_KEY === '' ||
                    !defined('RABBITMQ_REQUEST_QUEUE') || RABBITMQ_REQUEST_QUEUE === '' ||
                    !defined('RABBITMQ_RESPONSE_QUEUE') || RABBITMQ_RESPONSE_QUEUE === '') {

                $rabbitMQ = false;
            }
            $sql="select account_id,account_number,synchronization_username,synchronization_password from erp_api_credentials where id=1";
            $rs=imw_query($sql);
            if($rabbitMQ && $rs && imw_num_rows($rs)==1 ){
                $row=imw_fetch_assoc($rs);
                if($row['account_id']!='' && $row['account_number']!='' && $row['synchronization_username']!='' && $row['synchronization_password']!='') {
                    $return = true;
                }
            }
        }
        return $return;
    }
}


function get_refill_direct_users() {
	$return=false;
	$array=array();
	$temp=array();
	$sql="select id,portal_refill_direct_access from users where user_type='1' and delete_status='0' ";
	$rs=imw_query($sql);
	if($rs && imw_num_rows($rs)>0){
		while( $row = imw_fetch_assoc($rs) ) {
			$array[]=$row['id'];
			if( isset($row['portal_refill_direct_access']) && $row['portal_refill_direct_access']!='' ) {
				$temp[]=explode(',',$row['portal_refill_direct_access']);
			}
		}
		foreach($temp as $val) {
			$array=array_merge($array,$val);
		}
		$array=array_unique($array);
	}
	
	if( isset($_SESSION["authId"]) && $_SESSION["authId"]!='' && in_array($_SESSION["authId"],$array) ) {
		$return=true;
	}
	return $return;
}



?>
