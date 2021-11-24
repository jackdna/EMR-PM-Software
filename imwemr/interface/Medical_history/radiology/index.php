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

include_once($GLOBALS['srcdir']."/classes/medical_hx/radiology.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$radiology = new Radiology($medical->current_tab);
$cls_common_function = new CLSCommonFunction();
$arr_info_alert = $radiology->radio_vocabulary;
$patient_id = $_SESSION['patient'];

//--- CHANGE RADIOLOGY TEST STATUS AS DELETED ----
if(empty($mode) == false && isset($del_id)){
	$delete_status = $radiology->set_test_as_deleted($del_id);
	echo $delete_status;
	exit();	
}


//--- Saving Form Data ---
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'saveFormData'){
	$save_status = $radiology->save_form_data($_REQUEST);
	if(trim($save_status[0]) != ''){
		$curr_tab = $save_status[0];
		$curr_dir = $save_status[1];
		?>
		<script type="text/javascript">
			var curr_tab = "<?php echo $curr_tab; ?>";
			top.show_loading_image("show", 100);
			if(top.document.getElementById('medical_tab_change')) {
				if(top.document.getElementById('medical_tab_change').value!='yes') {
					top.alert_notification_show('<?php echo $arr_info_alert["save"];?>');
				}
				if(top.document.getElementById('medical_tab_change').value=='yes') {
					top.chkConfirmSave('yes','set');		
				}
				top.document.getElementById('medical_tab_change').value='';
			}
			top.fmain.location.href = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/index.php?showpage='+curr_tab;
			top.show_loading_image("hide");
		</script>
		<?php
	}
}

//--- SET RADIOLOGY TYPE ---
$RAD_TYPE = $radiology->rad_type_arr;

//--- SET GLOBAL PATH FOR RADIOLOGY ---
$GLOBAL_PATH = $GLOBALS['webroot'];
//$objManageData->Smarty->assign('RAD_TYPE',$RAD_TYPE);
$type_options = '';
foreach($RAD_TYPE as $rd_type => $val){
	$sel = '';
	$sel = $rd_type == $rad_type ? 'selected="selected"' : '';
	$type_options .= '<option value="'.$rd_type.'" '.$sel.'>'.$val.'</option>';
}
$DOM_RAD_TYPE = $type_options;

//--- GET ALL RADIOLOGY TEST FOR PATIENT ----
$radQryRes = $radiology->load_radiology_data($filter_val);
$radDataArr = array();

$loop_count = count($radQryRes) > 5 ? count($radQryRes) : 5;
$radDataArr = array();
$rad_test_data_id_arr = array();
for($i=0;$i<$loop_count;$i++){
	$dataArr = array();
	if($radQryRes[$i]['rad_test_data_id'] != 0){
		$rad_test_data_id_arr[] = $radQryRes[$i]['rad_test_data_id'];
	}
	$dataArr['RAD_ID'] = $radQryRes[$i]['rad_test_data_id'];
	$dataArr['RAD_NAME'] = $radQryRes[$i]['rad_name'];
	$rad_type = $radQryRes[$i]['rad_type'];
	$dataArr['RAD_TYPE_OTHER'] = $radQryRes[$i]['rad_type_other'];
	
	$dataArr['RAD_TYPE_DIS'] = 'table-row';
	$dataArr['RAD_TYPE_OTHER_DIS'] = 'none';
	
	if($dataArr['RAD_TYPE_OTHER'] != ''){
		$dataArr['RAD_TYPE_OTHER_DIS'] = 'table-row';
		$dataArr['RAD_TYPE_DIS'] = 'none';
	}
	
	$type_options = '';
	//Unsetting Other key to insert it at the last key of the array for soritng the dropdown
	if(array_key_exists("Other",$RAD_TYPE)){
		unset($RAD_TYPE['Other']);
	}
	asort($RAD_TYPE);
	reset($RAD_TYPE);
	
	foreach($RAD_TYPE as $rd_type => $val){
		$sel = $rd_type == $rad_type ? 'selected="selected"' : '';
		$type = $val;
		$type_options .= '<option value="'.$rd_type.'" '.$sel.'>'.$type.'</option>';
	}
	$type_options .= '<option value="Other">Other</option>';
	$dataArr['RAD_TYPE'] = $type_options;
	
	$dataArr['RAD_LOINC'] = $radQryRes[$i]['rad_loinc'];
	$dataArr['RAD_FAC_NAME'] = $radQryRes[$i]['rad_fac_name'];
	$dataArr['RAD_ADDRESS'] = $radQryRes[$i]['rad_address'];	
	$dataArr['RAD_RESULTS'] = $radQryRes[$i]['rad_results'];
	$dataArr['RAD_INDICATION'] = $radQryRes[$i]['rad_indication'];
	$dataArr['RAD_INSTUCTIONS'] = $radQryRes[$i]['rad_instuctions'];
	
	if($radQryRes[$i]['ordered_date'] != '00-00-0000'){
		$dataArr['RAD_ORDER_DATE'] = $radQryRes[$i]['ordered_date'];
	}
	
	if($radQryRes[$i]['radResultsDate'] != '00-00-0000'){
		$dataArr['RAD_RESULTS_DATE'] = $radQryRes[$i]['radResultsDate'];
	}
	
	$rad_status = $radQryRes[$i]['rad_status'];
	if($rad_status == 1){
		$dataArr['RAD_STATUS_ORDERED'] = 'selected="selected"';
	}
	else if($rad_status == 2){
		$dataArr['RAD_STATUS_COMPLETE'] = 'selected="selected"';
	}
	
	$dataArr['RAD_ORDER_TIME'] = $radQryRes[$i]['rad_order_time'];
	$dataArr['RAD_RESULTS_TIME'] = $radQryRes[$i]['rad_results_time'];
	$dataArr['REFUSAL'] = $radQryRes[$i]['refusal'];
	$dataArr['REFUSAL_REASON'] = $radQryRes[$i]['refusal_reason'];
	$dataArr['REFUSAL_SNOMED'] = $radQryRes[$i]['refusal_snomed'];
	
	//--- SELECT ORDER BY ---
	$rad_order_by = $radQryRes[$i]['rad_order_by'];
	if(empty($rad_order_by) == true){
		$rad_order_by = $_SESSION['authId'];
	}
	$dataArr['RAD_ORDERED_BY'] = $cls_common_function->drop_down_providers($rad_order_by,'');
	$dataArr['RAD_RESULTS_SNOWMED'] = $radQryRes[$i]['snowmedCode'];
	
	$radDataArr[] = $dataArr;
}

//--- AUDIT TRAIL FOR VIEW ONLY -----  Skipped for R8
$policyStatus = $radiology->policy_status;
if(count($rad_test_data_id_arr) > 0 and $policyStatus == 1){
	$auditTrailViewArr = array();
	if(isset($_SESSION['Patient_Viewed']) === true){
		$patientViewed = $_SESSION['Patient_Viewed'];
		if(is_array($patientViewed) && $patientViewed["Medical History"]["Radiology"] == 0){
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];													 
			//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);													 
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
			$auditTrailViewArr[0]['Pk_Id'] = $rad_test_data_id_arr[0];
			$auditTrailViewArr[0]['Table_Name'] = 'rad_test_data';
			$auditTrailViewArr[0]['Action'] = 'view';
			$auditTrailViewArr[0]['Operater_Id'] = $_SESSION['authId'];
			$auditTrailViewArr[0]['Operater_Type'] = getOperaterType($_SESSION['authId']);
			$auditTrailViewArr[0]['IP'] = $ip;
			$auditTrailViewArr[0]['MAC_Address'] = $_REQUEST['macaddrs'];
			$auditTrailViewArr[0]['URL'] = $URL;
			$auditTrailViewArr[0]['Browser_Type'] = $browserName;
			$auditTrailViewArr[0]['OS'] = $os;
			$auditTrailViewArr[0]['Machine_Name'] = $machineName;
			$auditTrailViewArr[0]['Category'] = 'patient_info-medical_history';
			$auditTrailViewArr[0]['Filed_Label'] = 'Patient radiology test data';
			$auditTrailViewArr[0]['Category_Desc'] = 'Radiology';
			$auditTrailViewArr[0]['Old_Value'] = join(' - ',$rad_test_data_id_arr).' - ';
			$auditTrailViewArr[0]['pid'] = $_SESSION['patient'];
			auditTrail($auditTrailViewArr,$mergedArray,0,0,0);
			$patientViewed["Medical History"]["Radiology"] = 1;
			$_SESSION['Patient_Viewed'] = $patientViewed;
		}
	}
}

//--- SET STATUS FOR RADIOLOGY TESTS ----
$status_val = $radiology->status_arr;

//--- SET RADIOLOGY TYPE AHEAD ---
$strRadTitle = explode(',',$radiology->set_radiology_typeahead_arr());

if(is_array($arr_info_alert) && count($arr_info_alert) > 0){
	$ARR_INFO_ALERT = $arr_info_alert;
	$ARR_INFO_ALERT_SERIALIZED = urlencode(serialize($arr_info_alert));
}else{
	$ARR_INFO_ALERT = "";
	$ARR_INFO_ALERT_SERIALIZED = "";
} 

//--- SET FILTER DROP DOWN ----
$filter_select = $filter_val;

//--- SET LAB TEST DATA ----
$rad_data = $radDataArr;

//--- SET ALL OPERATORS DETAILS ----
$phyOption = $cls_common_function->drop_down_providers($_SESSION['authId'],'');

//--- SET DIV HIEGHT FOR RADIOLOGY TEST ----
$sessionHeightInMH5= $GLOBALS['gl_browser_name']=='ipad' ? $_SESSION["wn_height"] - 62 : $_SESSION['wn_height']-270;
$div_height = $sessionHeightInMH5;

//--- SET JAVASCRIPT ALERTS ----
if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
	$OBJPatSpecificAlert = new CLSAlerts();		
	$alertToDisplayAt = "admin_specific_chart_note_med_hx";
	$getAdminAlert = $OBJPatSpecificAlert->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$form_id,"350px");	
	$alertToDisplayAt = "patient_specific_chart_note_med_hx";
	$getPatSpecificAlert = $OBJPatSpecificAlert->getPatSpecificAlert($_SESSION['patient'],$alertToDisplayAt,"350px");
	$autoSetDivLeftMargin = $OBJPatSpecificAlert->autoSetDivLeftMargin("140","265");
	$autoSetDivTopMargin = $OBJPatSpecificAlert->autoSetDivTopMargin("250","30");
	$writeJS = $OBJPatSpecificAlert->writeJS();
}
$date_format = inter_date_format();


/** Variables to be used in js file **/
$phy_option_js = $cls_common_function->drop_down_providers($_SESSION['authId'],'','',1);

$jquery_var_arr = array();
$jquery_var_arr['global_path'] = $GLOBALS['webroot'];
$jquery_var_arr['date_format'] = $date_format;
$jquery_var_arr['typeahead_title_arr'] = $strRadTitle;
$jquery_var_arr['DOM_RAD_TYPE'] = addslashes($DOM_RAD_TYPE);
$jquery_var_arr['phy_opt'] = $phy_option_js;
$jquery_var_arr['alert_arr'] = $arr_info_alert;
?>
<script>
	// JS variable which include all required php variable for js file	
	var js_php_var = '<?php echo json_encode($jquery_var_arr); ?>';
	
	// Json parsed variable to be used in js file
	var global_js_var = $.parseJSON(js_php_var);
	var phy_opt = '';
	$.each(global_js_var.phy_opt,function(id,val){
		phy_opt += '<option value="'+id+'">'+val+'</option>';
	});
</script>
<?php
//--- GET RADIOLOGY TEMPLATE ---
include_once('radiology_display.php')
?>