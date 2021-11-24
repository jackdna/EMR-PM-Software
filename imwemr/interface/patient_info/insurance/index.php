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
include("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/insurance.class.php");
include_once($GLOBALS['srcdir']."/classes/cls_common_function.php");
$patient_id = $_SESSION['patient']; if(!$patient_id) die;
$OBJCommonFunction = new CLSCommonFunction;
$data_obj = new Insurance($patient_id);
$pg_title = 'Insurance';
$data = $data_obj->data;
$defaults	=	$data_obj->defaults;
$library_path = $GLOBALS['webroot'].'/library';
$vocabulary = $defaults['vocabulary'];
$mandatory_flds = array_keys(array_filter($defaults['mandatory_fld']),2);
$advisory_flds = array_keys(array_filter($defaults['mandatory_fld']),1);
$patientDetail = (object) $data['patient_data'];
$patientName = $patientDetail->lname.', '; 
$patientName .= $patientDetail->fname.' '; 
$patientName .= $patientDetail->mname; 
$patientName = trim($patientName);
//pre($_REQUEST);
// Perform Insurance Case Action
$data_obj->insurance_case_action($chooseNewform,$_REQUEST);
// Handle Re Arrange Request
$data_obj->insurance_re_arrange($_REQUEST);
// Handle Copy Insurance Request
$data_obj->insurance_copy($_REQUEST);
//------- Set/Check Responsible Party
$data_obj->res_name = $data_obj->set_resp_party($_REQUEST['inactivePriInsComp']);

$insCaseDataFields = $data['ins_case_field'];
$insDataFields = $data['ins_data_field'];
$patientRefDataFields = $data['pt_reff_field'];
$patientAuthDataFields = $data['pt_auth_field'];
//pre($data);
if($data['policy_status'] == 1)
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
}

if(!$case_status)
{
	if($_SESSION['currentCaseid'])
	{
		$query = "SELECT ins_caseid FROM insurance_case WHERE patient_id = '".$patient_id."' AND case_status = 'Open' AND ins_caseid = '".$_SESSION['currentCaseid']."'";
		$sql	=	imw_query($query);
		$cnt 	= imw_num_rows($sql);
		if($cnt <= 0)
		{
			$_SESSION['currentCaseid'] = '';
			$_SESSION['new_casetype']  = '';
		}
	}
	$case_status = 'Open';
}

//--- Start Query For Patient Who Has No Open Case 
$qry = "SELECT * FROM insurance_case WHERE patient_id = '".$patient_id."' and case_status = 'Open' ORDER BY ins_case_type";
$sql	 = imw_query($qry);
if(imw_num_rows($sql) <= 0)
{
	$qry_pick_normal = "select case_id from insurance_case_types where normal='1' and status='0' limit 1";
	$res_pick_normal = imw_query($qry_pick_normal);
	if(imw_num_rows($res_pick_normal)==1)
	{
		$rs_pick_normal = imw_fetch_assoc($res_pick_normal);
		$case_type = $rs_pick_normal["case_id"];
	}
	else
	{
		$qry_pick_normal1	=	"select case_id from insurance_case_types where status='0' limit 1";
		$res_pick_normal1 = imw_query($qry_pick_normal1);
		if(imw_num_rows($res_pick_normal1)==1)
		{
			$rs_pick_normal1 = imw_fetch_assoc($res_pick_normal1);
			$case_type = $rs_pick_normal1["case_id"];
		}
	}
	$insert_data['case_status'] = 'Open';
	$insert_data['ins_case_type'] = $case_type;
	$insert_data['patient_id'] = $patient_id;
	$insert_data['start_date'] = date('Y-m-d');
	$insert_id = AddRecords($insert_data,'insurance_case');
	$_SESSION['currentCaseid'] = $insert_id;
	$_SESSION['new_casetype'] = $insert_data['ins_case_type'];			
}
//--- End Query For Patient Who Has No Open Case


//---- Start Create A new Session Case Id
if($_SESSION['currentCaseid'] == '')
{
	$qry = "select ins_caseid,ins_case_type from insurance_case  
						where patient_id = '$patient_id' and case_status = '$case_status' AND del_status = 0
						order by ins_case_type";
	$qryId = imw_query($qry);
	$sessionDetails = imw_fetch_assoc($qryId);
	$_SESSION['currentCaseid'] = $sessionDetails['ins_caseid'];
	$_SESSION['new_casetype'] = $sessionDetails['ins_case_type'];
}
else if($_SESSION['new_casetype'] == '')
{
	$qry = "select ins_case_type from insurance_case where ins_caseid = '".$_SESSION['currentCaseid']."'";
	$qryId = imw_query($qry);
	$sessionDetails = imw_fetch_assoc($qryId);
	$_SESSION['new_casetype'] = $sessionDetails['ins_case_type'];
}
//---- End Create A new Session Case Id 


//--- Query For Choosed Case Drop Down --------
if($_SESSION['currentCaseid'] != '' && $_SESSION['new_casetype'] != '')
{
	$submitcap	=	"Update Case";
	$selectedData	= get_extract_record('insurance_case','ins_caseid',$_SESSION['currentCaseid']);
	$insurance_case_type = $selectedData['ins_case_type'];
	$caseType = $selectedData['ins_case_type'];
	$Caseid = $selectedData['ins_caseid'];
	if($selectedData['start_date'] != '0000-00-00 00:00:00'){
		$stDate = substr($selectedData['start_date'],0,strpos($selectedData['start_date']," "));
		$start_date = get_date_format(date('Y-m-d',strtotime($stDate)));
	}
	else{
		$start_date = '';
	}
	if($selectedData['end_date'] != '0000-00-00 00:00:00')
	{
		$endDate = substr($selectedData['end_date'],0,strpos($selectedData['end_date']," "));
		$end_date = get_date_format(date('Y-m-d',strtotime($endDate)));
	}
	else{
		$end_date = '';
	}
	$case_status = $selectedData['case_status'];
}
else{
	$submitcap="Open Case";
}
//------- Start To get Insurance Case Type 
if($_SESSION['new_casetype']){
	$caseTypeDetail = get_extract_record('insurance_case_types','case_id',$_SESSION['new_casetype']);
}
//------- End To get Insurance Case Type 





$fields = 'ID.id as insurance_dataId,ID.provider,ID.actInsComp, ID.effective_date,ID.policy_number, ID.expiration_date,IC.id,IC.in_house_code, IC.name, ID.type,IC.ins_accept_assignment';
$table = 'insurance_data ID LEFT JOIN insurance_companies IC ON IC.id = ID.provider ';
$extra = " AND ID.pid = '".$patient_id."'";
$order_by =  " ID.actInsComp Desc, ID.effective_date";
$addComDetail = get_array_records($table,'ID.ins_caseid',$_SESSION['currentCaseid'],$fields,$extra,$order_by,'Desc');

$priFlags = false;
$secFlags = false;
$terFlags = false;
$blprimaryComDetail = false;
$blsecComDetail = false;
$blterComDetail = false;
$validInsComArr = array();
if(count($addComDetail)>0)
{
	for($i=0;$i<count($addComDetail);$i++)
	{
		switch($addComDetail[$i]['type'])
		{
			case "primary":
				$blprimaryComDetail = true;
				$ins_accept_assignment = $addComDetail[$i]['ins_accept_assignment'];
				if($addComDetail[$i]['in_house_code'] == ''){
					$in_house_code = substr($addComDetail[$i]['name'],0,7);
				}else{
					$in_house_code = $addComDetail[$i]['in_house_code'];
				}
				if($inactivePriInsComp == '' && $addComDetail[$i]['actInsComp'] == 1){		
					$inactivePriInsComp = $addComDetail[$i]['insurance_dataId'];
				}
				$policy_number = $addComDetail[$i]['policy_number'];
				$inactivePriInsSel = '';
				if($inactivePriInsComp == $addComDetail[$i]['insurance_dataId'] && $priFlags == false){
					$priFlags = true;
					$inactivePriInsSel = 'selected';
				}
				if(empty($in_house_code) == true){
					$in_house_code = 'Not set';
				}
				if($addComDetail[$i]['actInsComp'] == 1){
					$style =  'style="color:#006600;"';
					$insName = 'Valid - '.$in_house_code;
					$validInsComArr['primary'] = $addComDetail[$i]['insurance_dataId'];
				}else{
					$style = 'style="color:#CC0000;"';
					if($addComDetail[$i]['effective_date'] == '0000-00-00 00:00:00'){
						$effective_date = 'N/K';
					}else{
						$effective_date = get_date_format(date('Y-m-d',strtotime($addComDetail[$i]['effective_date'])));
					}
					if($addComDetail[$i]['expiration_date'] == '0000-00-00 00:00:00'){
						$expiration_date = 'N/K';
					}else{
						$expiration_date = get_date_format(date('Y-m-d',strtotime($addComDetail[$i]['expiration_date'])));
					}
					$insName = $effective_date.' - '.$expiration_date.' - '.$in_house_code.' / #'.$policy_number;
					if(getOS() == "Mac")$insName .= " - Expired";
				}
				if( $inactivePriInsSel == "selected" && $style == 'style="color:#CC0000;"'){
					$pri_sel_style = "color:#CC0000;";
				}
				$inactivePriIns .= '
					<option value="'.$addComDetail[$i]['insurance_dataId'].'" '.$inactivePriInsSel.' '.$style.'>'.$insName.'</option>
				';	
			break;
			case "secondary":
				$blsecComDetail = true;
				if($addComDetail[$i]['in_house_code'] == ''){
					$in_house_code2 = substr($addComDetail[$i]['name'],0,7);
				}else{
					$in_house_code2 = $addComDetail[$i]['in_house_code'];
				}
				if($inactiveSecInsComp == '' && $addComDetail[$i]['actInsComp'] == 1){					
					$inactiveSecInsComp = $addComDetail[$i]['insurance_dataId'];				
				}
				$policy_number = $addComDetail[$i]['policy_number'];
				$inactiveSecInsSel = '';
				if($inactiveSecInsComp == $addComDetail[$i]['insurance_dataId'] && $secFlags == false){
					$secFlags = true;				
					$inactiveSecInsSel = 'selected';
				}
				if(empty($in_house_code2) == true){
					$in_house_code2 = 'Not set';
				}
				if($addComDetail[$i]['actInsComp'] == 1){
					$style2 =  'style="color:#006600;"';
					$insName2 = 'Valid - '.$in_house_code2;
					$validInsComArr['secondary'] = $addComDetail[$i]['insurance_dataId'];
				}else{
					$style2 = 'style="color:#CC0000;"';
					if($addComDetail[$i]['effective_date'] == '0000-00-00 00:00:00'){
						$effective_date2 = 'N/K';
					}else{
						$effective_date2 = get_date_format(date('Y-m-d',strtotime($addComDetail[$i]['effective_date'])));
					}
					if($addComDetail[$i]['expiration_date'] == '0000-00-00 00:00:00'){
						$expiration_date2 = 'N/K';
					}else{
						$expiration_date2 = get_date_format(date('Y-m-d',strtotime($addComDetail[$i]['expiration_date'])));
					}
					$insName2 = $effective_date2.' - '.$expiration_date2.' - '.$in_house_code2.' / #'.$policy_number;
					if(getOS() == "Mac")$insName2 .= " - Expired";
				}
				if( $inactiveSecInsSel == "selected" && $style2 == 'style="color:#CC0000;"'){
					$sec_sel_style = "color:#CC0000;";
				}
				$inactiveSecIns .= '
					<option value="'.$addComDetail[$i]['insurance_dataId'].'" '.$inactiveSecInsSel.' '.$style2.'>'.$insName2.'</option>
				';	
			break;
			case "tertiary":
				$blterComDetail = true;
				
				if($addComDetail[$i]['in_house_code'] == ''){
					$in_house_code3 = substr($addComDetail[$i]['name'],0,7);
				}else{
					$in_house_code3 = $addComDetail[$i]['in_house_code'];
				}
				if($inactiveTerInsComp == '' && $addComDetail[$i]['actInsComp'] == 1){		
					$inactiveTerInsComp = $addComDetail[$i]['insurance_dataId'];
				}
				$policy_number = $addComDetail[$i]['policy_number'];
				$inactiveTerInsSel = '';
				if($inactiveTerInsComp == $addComDetail[$i]['insurance_dataId'] && $terFlags == false){
					$terFlags = true;
					$inactiveTerInsSel = 'selected';
				}
				
				if(empty($in_house_code3) == true){
					$in_house_code3 = 'Not set';
				}
					
				if($addComDetail[$i]['actInsComp'] == 1){
					$style3 =  'style="color:#006600;"';
					$insName3 = 'Valid - '.$in_house_code3;
					$validInsComArr['tertiary'] = $addComDetail[$i]['insurance_dataId'];
				}else{
					$style3 = 'style="color:#CC0000;"';
					if($addComDetail[$i]['effective_date'] == '0000-00-00 00:00:00'){
						$effective_date3 = 'N/K';
					}else{
						$effective_date3 = get_date_format(date('Y-m-d',strtotime($addComDetail[$i]['effective_date'])));
					}
					if($addComDetail[$i]['expiration_date'] == '0000-00-00 00:00:00'){
						$expiration_date3 = 'N/K';
					}else{
						$expiration_date3 = get_date_format(date('Y-m-d',strtotime($addComDetail[$i]['expiration_date'])));
					}
					$insName3 = $effective_date3.' - '.$expiration_date3.' - '.$in_house_code3.' / #'.$policy_number;
					if(getOS() == "Mac")$insName3 .= " - Expired";
				}
				if( $inactiveTerInsSel == "selected" && $style3 == 'style="color:#CC0000;"'){
					$ter_sel_style = "color:#CC0000;";
				}
				$inactiveTerIns .= '
					<option value="'.$addComDetail[$i]['insurance_dataId'].'" class="bg-success" '.$inactiveTerInsSel.' '.$style3.'>'.$insName3.'</option>
				';
			break;
		}
	}
}

//Audit Functionality 
$arrAuditTrailView=array();
if($data['policy_status'] == 1) {
	$arrAuditTrailView [] = 
				array(
						"Pk_Id"=> $_SESSION['currentCaseid'],
						"Table_Name"=> 'insurance_data',
						"Action"=> "view",
						"Operater_Id"=> $opreaterId,
						"Operater_Type"=> getOperaterType($opreaterId) ,
						"IP"=> $ip,
						"MAC_Address"=> $_REQUEST['macaddrs'],
						"URL"=> $URL,
						"Browser_Type"=> $browserName,
						"OS"=> $os,
						"Machine_Name"=> $machineName,
						"Category"=> "patient_info",
						"Filed_Label"=> 'Patient Insurance Data - '.$_SESSION['patient'],
						"Category_Desc"=> "insurance",
						"pid"=> $_SESSION['patient']					
					);
	$patientViewed = array();
	$demoErrors = "Error : Table 'insurance_data' doesn't exist";
	$table = array('insurance_data');
	$error = array($demoError);
	$mergedArray = merging_array($table,$error);
	if(isset($_SESSION['Patient_Viewed'])){
		$patientViewed = $_SESSION['Patient_Viewed'];	
		if($patientViewed["Insurance"] == 0){
			auditTrail($arrAuditTrailView,$mergedArray);
			$patientViewed["Insurance"] = 1;			
			$_SESSION['Patient_Viewed'] = $patientViewed;
		}
	}
}

if($blprimaryComDetail == false){
	$inactivePriInsComp = NULL;
}
if($blsecComDetail == false){
	$inactiveSecInsComp = NULL;	
}
if($blterComDetail == false){
	$inactiveTerInsComp = NULL;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo 'Insurance :: imwemr ::';?></title>
    <!-- Bootstrap -->
    <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap Selctpicker CSS -->
    <link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
    <!-- Application Common CSS -->
    <link href="<?php echo $library_path; ?>/css/common.css?<?php echo filemtime('../../../library/css/common.css');?>" rel="stylesheet">
    <!-- Insurance Page CSS -->
    <link href="<?php echo $library_path; ?>/css/insurance.css?<?php echo filemtime('../../../library/css/insurance.css');?>" rel="stylesheet">
    <!-- Messi Plugin for fancy alerts CSS -->
		<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
    <!-- DateTime Picker CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
    <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
        <link href="<?php echo $library_path; ?>/css/imw_css.css" rel="stylesheet">
    <?php } ?>
    <style>input[readonly] {pointer-events: none;}</style>
   	<script>
			// Default JS Variables 
			var web_root = '<?php echo $GLOBALS['webroot']; ?>';
			var mandatory = <?php echo json_encode($defaults['mandatory_fld']); ?>;
			var mandatory_fld = <?php echo json_encode($mandatory_flds); ?>;
			var advisory_fld = <?php echo json_encode($advisory_flds); ?>;
			var vocabulary = <?php echo json_encode($vocabulary); ?>;
			var patient_info = <?php echo json_encode($patientDetail); ?>;
			var phone_format = '<?php echo $GLOBALS['phone_format'] ?>';
			var operator = '<?php echo $defaults['operator_name']; ?>';
			var change_flag, _this, $_this = false;
			var js_today_date = '<?php echo get_date_format(date('Y-m-d')); ?>';
			var js_alert_msg = '<?php echo trim($data_obj->js_alert_msg); ?>';
			if(js_alert_msg) { top.fAlert(js_alert_msg); 
				if ( window.history.replaceState ) { window.history.replaceState( null, null, window.location.href ); }
			}
		</script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body onUnload="$('#hidChkInsTabDbStatus',top.document).val('');">
  	<div class="container-fluid" id="body_div">
    	<form name="insuranceCaseFrm" id="insuranceCaseFrm" enctype="multipart/form-data" method="post">
      	
        <!-- Hidden Fields Section Start -->
        <input type="hidden" id="chooseNewform" name="chooseNewform" value="">
        <input type="hidden" id="ins_caseid" name="ins_caseid" value="<?php echo $_SESSION['currentCaseid'];?>">
		<input type="hidden" id="show_ins_scan_in_modal" value="<?php echo defined("SHOW_INS_SCAN_IN_MODAL_POPUP")?constant("SHOW_INS_SCAN_IN_MODAL_POPUP"):"";?>">
        <input type="hidden" id="patientName" name="patientName" value="<?php echo $patientName ?>">
        <input type="hidden" id="patientDob" name="patientDob" value="<?php echo $patientDetail->DOB; ?>">
        <input type="hidden" id="patientSex" name="patientSex" value="<?php echo $patientDetail->sex; ?>">
        <input type="hidden" id="patientStreet" name="patientStreet" value="<?php echo $patientDetail->street; ?>">
        <input type="hidden" id="todayDate" name="todayDate"  value="<?php echo get_date_format(date("Y-m-d"));?>" >
        <input type="hidden" id="copy_ins_name" name="copy_ins_name" value="copy_ins_name" >
        <input type="hidden" id="hid_pat_steet" name="hid_pat_steet" value="<?php echo ucfirst($patientDetail->street); ?>">
        <input type="hidden" id="hid_pat_steet_2" name="hid_pat_steet_2" value="<?php echo ucfirst($patientDetail->street2); ?>">
        <input type="hidden" id="hid_pat_zip_code" name="hid_pat_zip_code" value="<?php echo $patientDetail->postal_code; ?>">
        <input type="hidden" id="hid_pat_city" name="hid_pat_city" value="<?php echo ucfirst($patientDetail->city); ?>">
        <input type="hidden" id="hid_pat_state" name="hid_pat_state" value="<?php echo ucfirst($patientDetail->state); ?>">
        <input type="hidden" id="hid_pat_id" name="hid_pat_id" value="<?php echo $patientDetail->id; ?>">
        <input type="hidden" id="hidInsChangeOption" name="hidInsChangeOption" value="0">
        <input type="hidden" id="divOpen" name="divOpen" value="">
        <input type="hidden" id="preObjBack" name="preObjBack" value=""/>
        <!-- Hidden Fields Section End -->
      	
        <div class="whitebox insurancepanel">
          <div class="row">
            <div class="col-sm-2">
            	<?php require_once(getcwd()."/insurance_cases.php"); ?>
   					</div>
            
            <div class="col-sm-10">
              <?php require_once(getcwd()."/insurance_caseview.php"); ?>
           	</div>
    
          </div>
        </div>  
        
        <?php include_once 'insurance_modal.php'; ?>
    	</form>
      
      
      <!-- Copy Insurance Modal Div -->
      <div id="copy_ins_comp_id" class="modal">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
          
            <form name="copy_ins_form" id="copy_ins_form" method="post">
                <div class="modal-header bg-primary">
                  <button type="button" class="close" data-dismiss="modal">×</button>
                  <h4 class="modal-title" id="modal_title">Copy Insurance Company</h4>
                </div>
            
                <div class="modal-body">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="col-sm-2 nowrap">Case From</div>
                      <div class="col-sm-4">
                        <select name="copy_from_ins_case" id="copy_from_ins_case" class="selectpicker" data-width="100%" onChange="change_case_chk(this.value);" title="<?php echo imw_msg('drop_sel');?>">
                          <?php echo $ins_case_drop_down; ?>
                        </select>
                      </div>
                      
                      <div class="col-sm-2 nowrap">Case To</div>
                      <div class="col-sm-4">
                        <select name="copy_to_ins_case" id="copy_to_ins_case" class="selectpicker" data-width="100%" title="<?php echo imw_msg('drop_sel');?>">
                          <?php echo $copy_to_ins_case; ?>
                        </select>
                      </div>
                      
                      <div class="clearfix">&nbsp;</div>
                      
                      <div class="col-sm-2 nowrap">Company From</div>
                      <div class="col-sm-4" id="change_ins_comp">
                        <?php
                        $ins_arr = array('primary','secondary','tertiary');
                        $ins_name_arr = array($data_obj->priInsInHouseCode,$data_obj->secInsInHouseCode,$data_obj->terInsInHouseCode);
                        $ins_copy_drop = '';
                        for($i=0;$i<count($ins_arr);$i++){
                          $ins_name = $ins_arr[$i];
                          $insurance_data_id = $ins_name."__".$validInsComArr[$ins_name];
                          if(trim($validInsComArr[$ins_name]) != ''){
                            $ins_name .= ' - '.$ins_name_arr[$i];
                            $ins_name = ucwords($ins_name);
                            $ins_copy_drop .= '<option value="'.$insurance_data_id.'">'.$ins_name.'</option>';
                          }
                        }
                        ?>
                        <select name="copy_ins_data_from" id="copy_ins_data_from" class="selectpicker" data-width="100%" title="<?php echo imw_msg('drop_sel');?>">
                          <?php echo $ins_copy_drop; ?>
                        </select>
                      </div>
                      <div class="col-sm-2 nowrap">Company To</div>
                      <div class="col-sm-4">
                        <?php
                        $ins_arr = array('primary','secondary','tertiary');
                        $ins_copy_drop = '';
                        for($i=0;$i<count($ins_arr);$i++){
                          $ins_name = ucfirst($ins_arr[$i]);
                          $ins_copy_drop .= '<option value="'.$ins_arr[$i].'">'.$ins_name.'</option>';
                        }
                        ?>
                        <select name="copy_ins_data_to" id="copy_ins_data_to" class="selectpicker" data-width="100%" title="<?php echo imw_msg('drop_sel');?>">
                          <?php echo $ins_copy_drop; ?>
                        </select>
                      </div>
                      
                    </div>
                  </div>
                  
                </div>
              
                <div id="module_buttons" class="modal-footer ad_modal_footer">
                    <input type="hidden" id="copy_ins_submit_txt" name="copy_ins_submit_txt" value="">
                    <button type="button" id="copy_ins_btn" class="btn btn-success" name="copy_ins_btn" onClick="exist_ins_check('<?php echo $patient_id; ?>');" >Copy</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_close">Close</button>
                </div>
              
            </form>
          </div>
        </div>
			</div>
      <!-- Copy Insurance Modal Div -->
      
      
      <!-- ReArrange Insurance Modal Div -->
      <div id="re_arrange_id" class="modal">
      	<div class="modal-dialog modal-md">

        	<div class="modal-content">
          	
            <form name="reArrangeFrm" method="post">
            
              <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title" id="modal_title">Re-arrange insurance provider</h4>
              </div>
        		
            	<div class="modal-body">
              	<input type="hidden" id="new_case_id" name="new_case_id" value="<?php echo $ins_caseid; ?>" >
                <input type="hidden" name="compId[]" value="<?php echo $data_obj->priInsCompanyId; ?>" >
                <input type="hidden" name="compId[]" value="<?php echo $data_obj->secInsCompanyId; ?>" >
                <input type="hidden" name="compId[]" value="<?php echo $data_obj->terInsCompanyId; ?>" >	
                <table class="table_collapse cllBorder4">
                <?php	
									$tableData = '<div class="row">';
									$ins_arr = array('Primary'=>$data_obj->priInsCompanyName,'Secondary'=>$data_obj->secInsCompanyName,'Tertiary'=>$data_obj->terInsCompanyName);
									$ins_arr_keys = array_keys($ins_arr);
									for($i=0;$i < count($ins_arr_keys);$i++)
									{
										$val = $ins_arr_keys[$i];
										$com_name = $ins_arr[$val];
										$pri_sel = '';
										if($i == 0 and $com_name != ''){
											$pri_sel = 'checked="checked"';
										}
										$sec_sel = '';
										if($i == 1 and $com_name != ''){
											$sec_sel = 'checked="checked"';
										}
										$ter_sel = '';
										if($i == 2 and $com_name != ''){
											$ter_sel = 'checked="checked"';
										}
										$insurance_data_id = $validInsComArr[strtolower($val)];
										//if(empty($com_name) == false){
              			$tableData .= '
											<div class="col-sm-12 table_grid">
												<div class="row">
													<div class="col-sm-5" >
														<div class="row">
															<div class="col-sm-7" >'.$val.' Ins.</div>
															<div class="col-sm-5" >'.$com_name.'</div>
														</div>
													</div>
													<div class="col-sm-7" >
														<div class="row">
															<div class="col-sm-1"> </div>
															<div class="col-sm-3 radio radio-inline">
																<input type="radio" name="name_'.$val.'" id="name_'.$val.'_1" '.$pri_sel.' value="primary__'.$insurance_data_id.'" onClick="switch_ins(\''.$val.'\',\'Primary\');" class="css-checkbox"><label for="name_'.$val.'_1" >Primary&nbsp;</label>
															</div>
															<div class="col-sm-4 radio radio-inline">
																<input type="radio" name="name_'.$val.'" '.$sec_sel.' id="name_'.$val.'_2" value="secondary__'.$insurance_data_id.'" onClick="switch_ins(\''.$val.'\',\'Secondary\');" class="css-checkbox"><label for="name_'.$val.'_2" >Secondary&nbsp;</label>
															</div>
															<div class="col-sm-3 radio radio-inline">		
																<input type="radio" name="name_'.$val.'" '.$ter_sel.' id="name_'.$val.'_3" value="tertiary__'.$insurance_data_id.'" onClick="switch_ins(\''.$val.'\',\'Tertiary\');" class="css-checkbox"><label for="name_'.$val.'_3" >Tertiary&nbsp;</label>
															</div>
														</div>
													</div>	
												</div>
											</div>
											<div class="clearfix"></div>
											';
            				}
										$tableData .= '</div>';
            				echo $tableData;
          			?>
                </table>
              </div>
              
              <div id="module_buttons" class="modal-footer ad_modal_footer">
              		<input type="submit" class="btn btn-success" name="re_arrange_btn" id="re_arrange_btn" value="Submit" />
              		<input type="button" class="btn btn-danger" data-dismiss="modal" id="btn_close2" value="Close" />
             	</div>
              
           	</form>   
        
        	</div>
        </div>
      </div>
     	<!-- ReArrange Insurance Modal Div --> 
  	</div>
  
  
  <div id="scan_card_show" class="movable_modal" role="dialog" >
		<div class="modal-dialog" >
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header drag_cursor bg-primary">
					<button type="button" class="close" onClick="$('#scan_card_show').fadeOut('fast');" >×</button>
					<h4 class="modal-title" id="modal_title"></h4>
				</div>

				<div class="modal-body" style="max-height:650px; overflow:hidden; overflow-y:auto; "></div>

				<div id="module_buttons" class="modal-footer ad_modal_footer">
					<button type="button" class="btn btn-danger" onClick="$('#scan_card_show').fadeOut('fast');">Close</button>
				</div>

			</div>
		</div>
	</div>

<script type="text/javascript" src="js_insurance.php"></script>
<script>
	$(document).ready(function(e) {
		var provider_typeahead_src = [];
		provider_typeahead_src = <?php echo json_encode($data_obj->typeahead_data); ?>;
		$('[id^=insprovider]').typeahead({source:provider_typeahead_src,items:8,scrollBar:true,ajax:''});
		$('[id^=auth_cpt_codes]').each(function(id,elem){bind_typeahead($(elem));});
		var paging = true; var loaded = false; var drop_obj = false;
		var cur_page = 0; var drop_down =false; var per_page = 500; 
		function getDropDown(){ 
			cur_page = cur_page+1; 
			var params = paging ? '&paging=true&p='+(cur_page)+'&pp='+per_page : '';
			if( !paging || (paging && !drop_down && !loaded )) {
				drop_down = true;
				if(drop_obj) drop_obj.find('.loader-small').show();
					
				return $.get(top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/insurance/ajax.php?action=insCompsAnchors'+params); 
			}
			return false;
		}
		
		function getAuthProvider(){
			return $.get(top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/insurance/ajax.php?action=authProvider'); 
		}
		
		getDropDown().done(function(r){handle_resp(r);});
		
		getAuthProvider().done(function(r){ r = $.parseJSON(r); auth_provider = r.data;});
		
		$("#scan_card_show").draggable({ handle: ".modal-header" });
		
		if( paging ) {
			$("[id$=InsCompData]").on('scroll',function(){
				if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight && !loaded) {
					drop_obj = $(this);
					getDropDown().done(function(r){handle_resp(r);});	
				}
			});
		}
		
		function handle_resp(r){
			r = $.parseJSON(r);
			$('ul[id=priInsCompData]').append(r.data.dropdown_pri);
			$('ul[id=secInsCompData]').append(r.data.dropdown_sec);
			$('ul[id=terInsCompData]').append(r.data.dropdown_ter);
			$('[data-toggle="tooltip"]').tooltip();
			$(".loader-small").hide();
			cur_page = parseInt(r.data.page);
			drop_down = false; drop_obj = false;
			loaded = r.data.loaded;
		}
		
	});
	top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
	top.btn_show("INS");
	
</script>
</body>
</html>