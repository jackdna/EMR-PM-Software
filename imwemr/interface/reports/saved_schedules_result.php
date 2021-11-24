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
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');

//$objManageData = new DataManage;
$CLSCommonFunction = new CLSCommonFunction;
$objCLSReports = new CLSReports;

$phpDateFormat = phpDateFormat();

$arrFacilitySel=array();
$arrDoctorSel=array();

$saved=false;
$printFile=false;

$selGroupArr =explode(',', $grp_id);
sort($selGroupArr);
$selSCArr =explode(',', $sc_name);
sort($selSCArr);
$selPhysicianArr =explode(',', $Physician);
sort($selPhysicianArr);

// ------ADD EDIT FIELDS DATA
//--- GET Groups SELECT BOX ----
$group_query = "Select gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
	$arrAllGroups[$group_res['gro_id']] = $group_res['name'];
    $groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}

$facility_name=array_combine($sc_name, $sc_name);
$facilityName = $objCLSReports->getFacilityName($facility_name,'1');

// PHYSICIANS
$physicianName = $CLSCommonFunction->drop_down_providers(implode(',',$Physician),'1','1');

// OPERATORS
$operatorNames = $CLSCommonFunction->drop_down_providers(implode(',',$operator));

// POS FACILITIES
$qry = "Select pos_facilityies_tbl.facilityPracCode as name,
pos_facilityies_tbl.pos_facility_id as id,
pos_tbl.pos_prac_code
from pos_facilityies_tbl
left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
order by pos_facilityies_tbl.headquarter desc,
pos_facilityies_tbl.facilityPracCode";
$rs=imw_query($qry);
$posFacilityArr = array();
while($posQryRes = imw_fetch_array($rs)){
	$id = $posQryRes['id'];
	$name = $posQryRes['name'];
	$pos_prac_code = $posQryRes['pos_prac_code'];

	$posFacilityArr[$id] = $name.' - '.$pos_prac_code;
}						
//GET ALL USERS
$providerNameArr[0] = 'No Provider';
$rs=imw_query("Select id, fname, mname, lname FROM users");	
while($res=imw_fetch_array($rs)){
	$id  = $res['id'];
	$pro_name_arr = array();
	$pro_name_arr["LAST_NAME"] = $res['lname'];
	$pro_name_arr["FIRST_NAME"] = $res['fname'];
	$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
	$pro_name = changeNameFormat($pro_name_arr);
	$providerNameArr[$id] = $pro_name;
}

// GET ALL CPT
$all_cpt_codes=array();
$qry = "Select cpt_fee_id,cpt_prac_code FROM cpt_fee_tbl WHERE delete_status = '0' ORDER BY cpt_prac_code";
$rs = imw_query($qry);
while($result = imw_fetch_array($rs)){
	$sel='';
	if(in_array($result['cpt_fee_id'], $arrProcedures)) { $sel='selected'; }
	$all_cpt_codes[$result['cpt_fee_id']] = $result['cpt_prac_code'];
}

// GET ALL DX
$all_dx_codes = array();
$qry = "Select dx_code FROM diagnosis_code_tbl WHERE trim(dx_code) != '' ORDER BY dx_code asc";
$rs = imw_query($qry);
while($res = imw_fetch_array($rs)){
	$all_dx_codes[$res['dx_code']] = $res['dx_code'];
}

//--- SET ICD10 CODE DROP DOWN ----
//ICD10 Options
$arrICD10Code=$objCLSReports->getICD10Codes($withInvCommas='no');
$all_dx10_code_options = '';
$sel_dx10_code_options = '';
foreach ($arrICD10Code as $dxkey=>$dx10code) {
	$dx10code = str_replace("'", "", $dx10code);
	$sel = (in_array($dxkey,$dxcodes10)) ? 'selected' : '';
    $all_dx10_code_options .= "<option value='" . $dxkey . "' ".$sel.">" . $dx10code . "</opton>";
}
$allDXCount10 = sizeof($arrICD10Code);

//CPT CODES
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl 
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color = '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = $row['cpt_prac_code'];
    if ($row['delete_status'] == 1 || $row['status'] == 'Inactive')
        $color = 'color:#CC0000!important';
		
		$sel = '';
		if (sizeof($cpt_code_id) > 0) {
			if ($cpt_code_id[$cpt_fee_id]) {
				$sel = 'selected';
			}
    }
    $cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
    $cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
}

//GET INSURANCE GROUP DROP DOWN
$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups WHERE delete_status = 0");
$ins_group_arr = array();
$ins_group_options = '';
while($insGQryRes = imw_fetch_array($insGroupQryRes)){
	$id = $insGQryRes['id'];
	$ins_grp_name = $insGQryRes['title'];
	$ins_group_options .= "<option value='" . $id . "' " . $selected . ">" . $ins_grp_name . "</option>";
}

//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
    if ($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
        $ins_id = $insQryRes[$i]['attributes']['insCompId'];
        //$ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
        $ins_name = $insQryRes[$i]['attributes']['insCompName'];
        $ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
        if ($ins_name == '') {
            $ins_name = $insQryRes[$i]['attributes']['insCompName'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }
				
				$sel = '';
				if (sizeof($ins_carriers) > 0) {
					if ($ins_carriers[$ins_id]) {
							$sel = 'selected';
					}
        }

        $ins_comp_arr[$ins_id] = $ins_name;
        if ($insQryRes[$i]['attributes']['insCompStatus'] == 0)
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel.">" . $ins_name . "</option>";
        else
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel." style='color:red'>" . $ins_name . "</option>";
	}
}
$insurance_cnt = sizeof($ins_comp_arr);
// ------END ----------------						
$arrAllReports =array('scheduler_report'=>'Scheduler Report','patients_csv_export'=>'Patients CSV Export','cpt_analysis'=>'CPT Analysis','day_sheet'=>'Day Sheet','front_desk'=>'Front Desk',
'ledger'=>'ledger','net_gross'=>'Net Gross','provider_analytics'=>'Provider Analytics');


// SAVE
if(empty($submit_btn) == false){  

	$searched_criteria = $grp_id_sel.'~~'.$sc_name_sel.'~~'.$Physician_sel.'~~'.$process_sel.'~~'.$operator_sel.'~~'.$cpt_code_sel.'~~'.$dx_code_sel.'~~'.$ins_groups_sel.'~~'.$ins_company_sel.'~~'.$rvu_sel.'~~'.$dx10_code_sel;

	$arrMonths = explode(',', $month_options_sel);
	$arrWeekdays = explode(',', $weekday_options_sel);
	$arrQuarterly = explode(',', $quarterly_sel);
	
	//NEXT EXECUTION DATE
	$arrNextExeTime= $objCLSReports->getNextRunTime($hour_options_sel, $arrWeekdays, $arrMonths, $arrQuarterly);

	$next_execution_date = $arrNextExeTime['0']['year'].'-'.$arrNextExeTime['0']['month'].'-'.$arrNextExeTime['0']['day'];
	$next_execution_time = $arrNextExeTime['0']['hour'].':00:00';
	
	$arrNextExeSerialize = serialize($arrNextExeTime);

	if(strstr($executionPeriod_sel, '~')){
		$arr=explode('~', $executionPeriod_sel);
		$dispFrom = getDateFormatDB(trim($arr[0]));
		$dispTo = getDateFormatDB(trim($arr[1]));
		$executionPeriod_sel=$dispFrom.'~'.$dispTo;
	}
	
	$qryPrefix = "Insert INTO ";
	$colPart  = ", status='active' ";
	$where='';

	if(empty($pkId)===false){
		$qryPrefix = "Update ";
		$colPart  ='';
		$where = " WHERE id='".$pkId."'";
	}
		
	$qry= $qryPrefix." reports_crone_jobs SET user_id='".$_SESSION['authId']."',
	schedule_name='".addslashes($schedule_name)."',
	report='".$report_sel."',
	searched_criteria='".$searched_criteria."',
	executionPeriod='".$executionPeriod_sel."',
	executed='0000-00-00 00:00:00',
	next_execution_date='".$next_execution_date."',
	next_execution_time='".$next_execution_time."',
	next_execution_data='".$arrNextExeSerialize."',
	enteredDate='".date('Y-m-d H:i:s')."',
	hour_options ='".$hour_options_sel."',
	month_options ='".$month_options_sel."',
	weekday_options ='".$weekday_options_sel."',
	quarterly ='".$quarterly_sel."',
	sftp_address ='".$sftp_address."',
	sftp_user ='".$sftp_user."',
	sftp_password ='".$sftp_password."',
	sftp_directory ='".$sftp_directory."',
	sftp_port ='".$sftp_port."',
	output_option ='".$output_option."'"
	.$colPart.$where;
	$rs = imw_query($qry);
	
	if($rs){
		$saved=true;
	}
}

$content_part='';
$row_class=' bg2';
// GET SAVED SEARCHES
$qry="Select *, DATE_FORMAT(next_execution_date, '".get_sql_date_format()."') as 'next_execution_date1' FROM reports_crone_jobs ORDER BY next_execution_date, next_execution_time";
$rs= imw_query($qry);
while($res  =imw_fetch_array($rs)){
	$printFile=true;
	$id = $res['id'];

	$allArr= explode('~~', $res['searched_criteria']);
	$arrGroups = explode(',', $allArr[0]);
	$arrFacility = explode(',', $allArr[1]);
	$arrPhysician = explode(',', $allArr[2]);
	$reportView = ucfirst($allArr[3]);
	$nextExecutionDateTime = $res['next_execution_date1'].'<br>'.getMainAmPmTime($res['next_execution_time']);

	$executionPeriod = ucwords(str_replace('_', ' ', $res['executionPeriod']));

	if(strstr($executionPeriod,'~')){
		$arr=explode('~', $executionPeriod);
		$dispFrom= date($phpDateFormat, strtotime($arr[0]));
		$dispTo= date($phpDateFormat, strtotime($arr[1]));
		$executionPeriod='From '.$dispFrom.' To '.$dispTo;
		$res['executionPeriod']=$dispFrom.'~'.$dispTo;
	}


if($res['next_execution_data']!=''){
	//$arr  = unserialize($res['next_execution_data']);
	//pre($arr);
}
	$arrSelData=array();
	if(empty($allArr[0])===false){
		foreach($arrGroups as $val){
			$arrSelData[] = $arrAllGroups[$val];
		}
		$group = implode(', ', $arrSelData);
	}else{
		$group = 'All';
	}

	$arrSelData=array();
	if(empty($allArr[1])===false){
		foreach($arrFacility as $val){
			$arrSelData[] = $posFacilityArr[$val];
		}
		$facility = implode(', ', $arrSelData);
	}else{
		$facility = 'All';
	}
	
	$arrSelData=array();
	if(empty($allArr[2])===false){
		foreach($arrPhysician as $val){
			$arrSelData[] = $providerNameArr[$val];
		}
		$physician = implode(', ', $arrSelData);
	}else{
		$physician = 'All';
	}
	
	$report  = ucwords(str_replace('_', ' ', str_replace(',', ', ',$res['report'])));

	$rowAllData = $res['report'].'$$'.$res['executionPeriod'].'$$'.$res['searched_criteria'];
	$rowAllData.= '$$'.$res['hour_options'].'$$'.$res['month_options'].'$$'.$res['weekday_options'].'$$'.$res['quarterly'].'$$'.$res['status'].'$$'.$res['schedule_name'];
	$rowAllData.= '$$'.$res['sftp_address'].'$$'.$res['sftp_user'].'$$'.$res['sftp_password'].'$$'.$res['sftp_directory'].'$$'.$res['sftp_port'];
	$rowAllData.= '$$'.$res['output_option'];
	
	$suspColor='';
	if($res['status']=='suspended'){
		$suspColor = '#CC0000';
	}

	$activeSuspend='';
	if($res['status'] == "active"){ 
		$activeSuspend .= '<img id="img'.$id.'" src="../../library/images/active.jpg" title="Suspend" border="0" onclick="javascript:activeDeactive(\''.$id.'\',\''.$res['status'].'\');">';
	}else{ 
		$activeSuspend .= '<img id="img'.$id.'" src="../../library/images/inactive.jpg" title="Activate" border="0" onclick="javascript:activeDeactive(\''.$id.'\',\''.$res['status'].'\');">';
	}
	
	$arrAllSceduleNames[$id] = strtolower($res['schedule_name']);
	$content_part .= '
		<tr style="height:25px" class="link_cursor'.$row_class.'">
			<td onClick="openDiv(\'1\','.$id.');">'.$res['schedule_name'].'<input type="hidden" name="rowAllData'.$id.'" id="rowAllData'.$id.'" value="'.$rowAllData.'"></td>
			<td onClick="openDiv(\'1\','.$id.');">'.$report.'</td>
			<td onClick="openDiv(\'1\','.$id.');">'.$group.'</td>
			<td onClick="openDiv(\'1\','.$id.');">'.$facility.'</td>
			<td onClick="openDiv(\'1\','.$id.');">'.$physician.'</td>
			<td onClick="openDiv(\'1\','.$id.');">'.$reportView.'</td>
			<td onClick="openDiv(\'1\','.$id.');">'.$executionPeriod.'</td>
			<td onClick="openDiv(\'1\','.$id.');">'.$nextExecutionDateTime.'</td>
			<td style="text-align:center; width:atuo;" id="statusTD'.$id.'">'.$activeSuspend.'</td>
		</tr>';			
		if($row_class==' bg2'){$row_class=' alt';}else{$row_class=' bg2';}
}


if($printFile==true){
	
	if(trim($content_part) != ''){				
		
	} 
}
?>
<div class="whtbox">
	<div class="table-responsive respotable adminnw">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="10%">Schedule Name<span></span></th>
					<th width="12%">Report<span></span></th>
					<th width="15%">Selected Groups<span></span></th>
					<th width="15%">Selected Facilities<span></span></th>
					<th width="15%">Selected Physicians<span></span></th>
					<th width="7%">Report View<span></span></th>
					<th width="8%">Date Range<span></span></th>
					<th width="8%">Next Execution<span></span></th>
					<th width="3%">Status<span></span></th>
				</tr>
			</thead>
			<tbody id="result_set"><?php echo $content_part;?></tbody>
		</table>
	</div>
</div>
<div id="myModal" class="modal" role="dialog">
	<div class="modal-dialog modal_95"> 
		<div class="modal-content">
		<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title" id="modal_title">Modal Header</h4>
		</div>
		<form name="checkInFrm" id="checkInFrm" action="saved_schedules_result.php" method="post" >
		<div class="modal-body">
			<div class="row">
				<div class="col-sm-2">
					<label for="schedule_name">Schedule Name</label>
					<input type="text" class="form-control" name="schedule_name" id="schedule_name" />					
				</div>
				<div class="col-sm-2">
					<label for="hour_options">Hours</label>
					<select type="text" class="form-control minimal" name="hour_options" id="hour_options"></select> 					
				</div>
				<div class="col-sm-2">
					<label for="weekday_options">Weekdays</label>
					<select type="text" class="form-control selectpicker" name="weekday_options[]" id="weekday_options"  data-width="100%" multiple data-actions-box="true" data-title="Select All"></select>
				</div>
				<div class="col-sm-2">
					<label for="month_options">Months</label>
					<select class="form-control selectpicker" id="month_options" name="month_options[]"  data-width="100%" multiple data-actions-box="true" data-title="Select All">
						<option value="1">January (1)</option>
						<option value="2">February (2)</option>
						<option value="3">March (3)</option>
						<option value="4">April (4)</option>
						<option value="5">May (5)</option>
						<option value="6">June (6)</option>
						<option value="7">July (7)</option>
						<option value="8">August (8)</option>
						<option value="9">September (9)</option>
						<option value="10">October (10)</option>
						<option value="11">November (11)</option>
						<option value="12">December (12)</option>
					</select>					
				</div>
				<div class="col-sm-2">
					<label for="quarterly">Quarterly</label>
						<select class="form-control selectpicker" id="quarterly" name="quarterly[]"  data-width="100%" multiple data-actions-box="true" data-title="Select All">
							<option value="every_quarter">Every Quarter</option>
							<option value="q1">Q1-January 1<sup>st</sup></option>
							<option value="q4">Q2-April 1<sup>st</sup></option>
							<option value="q7">Q3-July 1<sup>st</sup></option>
							<option value="q10">Q4-October 1<sup>st</sup></option>
						</select>					
				</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<div class="row">
					<div class="col-sm-6" onKeyUp="check_reports('groups');" onMouseOut="check_reports('groups')">
						<label for="groups">Groups</label>
						<select name="groups[]" id="groups" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select All">
						<?php echo $groupName; ?>
						</select>
						
					</div>
					
					<div class="col-sm-6" onKeyUp="check_reports('sc_name');" onMouseOut="check_reports('sc_name')">
						<label for="sc_name">Facility</label>
						<select name="sc_name[]" id="sc_name" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select All">
						<?php echo $facilityName; ?>
						</select>
					</div>
					<div class="col-sm-6" onKeyUp="check_reports('Physician');" onMouseOut="check_reports('Physician')">
						<label>Physician</label>
						<select name="Physician[]" id="Physician" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
						<?php echo $physicianName; ?>
						</select>			
					</div>
					<div class="col-sm-6" onKeyUp="check_reports('operator');" onMouseOut="check_reports('operator')">
						<label>Operator</label>
						<select name="operator[]" id="operator" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
						<?php echo $operatorNames; ?>
						</select>				
					</div>
					<div class="col-sm-6">
						<div class="col-sm-7">
							<label for="executionPeriod">Date Range</label>
							<select class="form-control minimal" name="executionPeriod" id="executionPeriod" onchange="setDateControls(this)">
								<option value="">Select</option>	
								<option value="today">Today (End of today)</option>
								<option value="last_day">Daily (Yesterday)</option>
								<option value="last_week">Week (Last Week)</option>
								<option value="last_month">Monthy (Last Month)</option>
								<option value="last_quarter">Quarterly (Last Quarter)</option>
							</select>				
						</div>
						<div class="col-sm-5" style="margin-top: 24px; ">
							<div class="checkbox">
								<input type="checkbox" name="year_to_date" id="year_to_date" value="1" onclick="setDateControls(this)" />
								<label for="year_to_date">Year to Date</label>
							</div>		
						</div>
					</div>	

					<div class="col-sm-6">
						<div class="col-sm-6">
							<label>From</label>
							<div class="input-group">
							<input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick" onblur="setDateControls(this)">
							<label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
							</div>
						</div>	
						<div class="col-sm-6">	
							<label>To</label>
							<div class="input-group">
							<input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo $_REQUEST['End_date']; ?>" class="form-control date-pick" onblur="setDateControls(this)">
							<label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
							</div>
						</div>
					 </div>

					 <div class="clearfix"></div>			 					

					<div class="col-sm-6 mt10 mb10">
						<div class="radio radio-inline pointer">
							<input type="radio" name="process" id="summary" value="Summary"/>
							<label for="summary">Summary</label>
						</div>
						<div class="radio radio-inline pointer">
							<input type="radio" name="process" id="detail" value="Detail" />
							<label for="detail">Detail</label>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6" onKeyUp="check_reports('ins_groups');" onMouseOut="check_reports('ins_groups')">
						<label for="ins_groups">Ins. Group</label>
						<select name="ins_groups[]" id="ins_groups" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
							<?php echo $ins_group_options; ?>
						</select>	
					</div>
					<div class="col-sm-6" onKeyUp="check_reports('ins_company');" onMouseOut="check_reports('ins_company')">
						<label for="ins_company">Ins. Company</label>
						<select name="ins_company[]" id="ins_company" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
							<?php echo $insComName_options; ?>
						</select>
					</div>
					<div class="col-sm-6" onKeyUp="check_reports('cpt_codes');" onMouseOut="check_reports('cpt_codes')">
						<label for="cpt_codes">CPT Codes</label>
						<select name="cpt_codes[]" id="cpt_codes" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
							<?php echo $cpt_code_options; ?>
						</select>	
					</div>
					<div class="col-sm-6" onKeyUp="check_reports('dx_codes10');" onMouseOut="check_reports('dx_codes10')" >
						<label for="dx_codes10">ICD10</label>
						<select name="dx_codes10[]" id="dx_codes10" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
						<?php echo $all_dx10_code_options; ?>
						</select>		
					</div>
					<div class="col-sm-6">
						<div class="checkbox">
							<input type="checkbox" name="rvu" id="rvu" value="1" onClick="check_reports('rvu');"/>
							<label for="rvu">RVU</label>
						</div>		
					</div>
					<div class="col-sm-6 mt5">
                        <div class="radio radio-inline pointer">
                          <input type="radio" name="output_option" id="output_csv" value="output_csv" checked/> 
                          <label for="output_csv">CSV</label>
                        </div>
                        <div class="radio radio-inline pointer">
                            <input type="radio" name="output_option" id="output_pdf" value="output_pdf"/> 
                            <label for="output_pdf">PDF</label>
                        </div>
					</div>

                    <div class="col-sm-12" style="margin-top:10px">
						<div class="recbox">	
                            <div class="head">
                                <div class="row">
                                    <div class="col-sm-7"><span>SFTP Details</span></div>
                                    <div class="col-sm-5 text-right">	
                                    </div>
                                </div>
                            </div>
                            <div class="tblBg">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="sftp_address">SFTP Address</label>
                                            <input tabindex="68" name="sftp_address" id="sftp_address" type="text" class="form-control" value="" autocomplete="off">
                                        </div>	
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group ">
                                            <label for="sftp_user" style="visibility:visible">User Name</label>
                                            <input type="text" name="sftp_user" id="sftp_user" class="form-control" value="" style="visibility:visible" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="sftp_password">Password</label>
                                            <input name="sftp_password" id="sftp_password" type="password" class="form-control" value="">
                                        </div>
                                    </div>	
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="sftp_directory">Directory Name</label>
                                            <input name="sftp_directory" id="sftp_directory" type="text" class="form-control" value="">
                                        </div>
                                    </div>	
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="sftp_port">Port</label>
                                            <input name="sftp_port" id="sftp_port" type="text" class="form-control" value="">
                                        </div>
                                    </div>	
                                </div>
                            </div>	
						</div>                    
                    </div>	
				</div>
			</div>
			<div class="col-sm-8">
			<div class="row">
				<div class="col-sm-2">
					<h4>Scheduler</h4>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="scheduler_report" value="scheduler_report"/>
						<label for="scheduler_report">Scheduler Report</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="patients_csv_export" value="patients_csv_export"/>
						<label for="patients_csv_export">Patients CSV Export</label>
					</div>
				</div>
                <div class="col-sm-3">
					<h4>Daily</h4>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="day_sheet" value="day_sheet"/>
						<label for="day_sheet">Day Sheet</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="payments" value="payments"/>
						<label for="payments">Payments</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="front_desk" value="front_desk"/>
						<label for="front_desk">Front Desk</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="copay_recon" value="copay_recon"/>
						<label for="copay_recon">Copay Reconciliation </label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="unapplied_superbills" value="unapplied_superbills"/>
						<label for="unapplied_superbills">Unapplied Superbills </label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="unfinalized_encs" value="unfinalized_encs"/>
						<label for="unfinalized_encs">Unfinalized Encounters</label>
					</div>
					<!-- 
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="unapplied_payments" value="unapplied_payments"/>
						<label for="unapplied_payments">Unapplied Payments</label>
					</div>
					 -->
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="adjustment" value="adjustment"/>
						<label for="adjustment">Adjustment</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="refund" value="refund"/>
						<label for="refund">Refund</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="daily_balance" value="daily_balance"/>
						<label for="daily_balance">Daily Balance</label>
					</div>
					<!-- 
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="fd_collection" value="fd_collection"/>
						<label for="fd_collection">FD Collection</label>
					</div>
					 -->
				</div>
				<div class="col-sm-4">
					<h4>Analytics</h4>
					<div class="row">
						<div class="col-sm-6">
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="practice_analytics" value="practice_analytics"/>
								<label for="practice_analytics">Practice Analytics</label>
							</div>
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="cpt_analysis" value="cpt_analysis"/>
								<label for="cpt_analysis">CPT Analysis</label>
							</div>
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="yearly" value="yearly"/>
								<label for="yearly">Yearly </label>
							</div>
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="ledger" value="ledger"/>
								<label for="ledger">Ledger</label>
							</div>
							<h4>Revenue</h4>
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="provider_revenue" value="provider_revenue"/>
								<label for="provider_revenue">Provider Revenue</label>
							</div>
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="facility_revenue" value="facility_revenue"/>
								<label for="facility_revenue">Facility Revenue</label>
							</div>
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="referring_revenue" value="referring_revenue"/>
								<label for="referring_revenue">Ref. Physician</label>
							</div>
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="provider_analytics" value="provider_analytics"/>
								<label for="provider_analytics">Provider Analytics</label>
							</div>	
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="credit_analysis" value="credit_analysis"/>
								<label for="credit_analysis">Credit Analytics</label>
							</div>

							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="insurance_cases" value="insurance_cases"/>
								<label for="insurance_cases">Ins. Analytics</label>
							</div>
							<!--
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="eid_status" value="eid_status"/>
								<label for="eid_status">EID Status</label>
							</div>
							-->
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="allowable_verify" value="allowable_verify"/>
								<label for="allowable_verify">Allowable Verify</label>
							</div>
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="deferred_vip" value="deferred_vip"/>
								<label for="deferred_vip">Deferred/VIP</label>
							</div>
							<!--
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="rvu_report" value="rvu_report"/>
								<label for="rvu_report">Provider RVU</label>
							</div>
							
							<div class="checkbox">
								<input type="checkbox" name="reports_chk" class="rptChk" id="sx_payment" value="sx_payment"/>
								<label for="sx_payment">Sx Payment</label>
							</div>
							-->                            			
						</div>		
					</div>
			</div>	
				<div class="col-sm-3">
				<h4>Account Receivables</h4>	
				<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="provider_ar" value="provider_ar"/>
						<label for="provider_ar">Provider A/R</label>
					</div>
					<!--
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="net_gross" value="net_gross"/>
						<label for="net_gross">Net/ Gross</label>
					</div>
					
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="ar_reports" value="ar_reports"/>
						<label for="ar_reports">A/R Reports</label>
					</div>
					-->
					<h4>Days In A/R</h4>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="days_in_ar" value="days_in_ar"/>
						<label for="days_in_ar">Days In A/R</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="days_in_patient" value="days_in_patient"/>
						<label for="days_in_patient">Patient</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="days_in_insurance" value="days_in_insurance"/>
						<label for="days_in_insurance">Insurance</label>
					</div>
					<!--
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="ar_trial_balance" value="ar_trial_balance"/>
						<label for="ar_trial_balance">A/R Trial Balance</label>
					</div>
					
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="receivables" value="receivables"/>
						<label for="receivables">Receivables</label>
					</div>-->
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="unworked_ar" value="unworked_ar"/>
						<label for="unworked_ar">Unworked A/R</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="unbilled_claims" value="unbilled_claims"/>
						<label for="unbilled_claims">Unbilled Claims</label>
					</div>
					<div class="checkbox">
						<input type="checkbox" name="reports_chk" class="rptChk" id="top_rej_reasons" value="top_rej_reasons"/>
						<label for="top_rej_reasons">Top Rejection Reasons</label>
					</div>
				</div>
				</div>

				</div>
		</div>
		<br /><br />
		<input type="hidden" name="pkId" id="pkId" value="" >
		<input type="hidden" name="Physician_sel" id="Physician_sel" value="">
		<input type="hidden" name="operator_sel" id="operator_sel" value="">
		<input type="hidden" name="sc_name_sel" id="sc_name_sel" value="">
		<input type="hidden" name="grp_id_sel" id="grp_id_sel" value="">
		<input type="hidden" name="cpt_code_sel" id="cpt_code_sel" value="">
		<input type="hidden" name="dx_code_sel" id="dx_code_sel" value="">
		<input type="hidden" name="dx10_code_sel" id="dx10_code_sel" value="">
		<input type="hidden" name="ins_groups_sel" id="ins_groups_sel" value="">
		<input type="hidden" name="ins_company_sel" id="ins_company_sel" value="">
		<input type="hidden" name="hour_options_sel" id="hour_options_sel" value="">
		<input type="hidden" name="day_options_sel" id="day_options_sel" value="">
		<input type="hidden" name="month_options_sel" id="month_options_sel" value="">
		<input type="hidden" name="weekday_options_sel" id="weekday_options_sel" value="">
		<input type="hidden" name="report_sel" id="report_sel" value="">
		<input type="hidden" name="executionPeriod_sel" id="executionPeriod_sel" value="">
		<input type="hidden" name="process_sel" id="process_sel" value="">
		<input type="hidden" name="rvu_sel" id="rvu_sel" value="">
        <input type="hidden" name="output_option_sel" id="output_option_sel" value="">
		<input type="hidden" name="status_sel" id="status_sel" value="">
		<input type="hidden" name="quarterly_sel" id="quarterly_sel" value="">
		<input type="submit" style="display:none;" name="submit_btn" value="submit" />
		<div id="module_buttons" class="ad_modal_footer modal-footer">
			<button type="button" class="btn btn-success" onClick="submitForm();">Save</button>
			<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		</div>
		</form>
		</div>
	</div>
</div>
	
	
<script type="text/javascript">	
		<?php if($saved==1){?>
				top.alert_notification_show('Record Saved Successfully!');
		<?php }?>		
		var arrAllGroups = <?php echo json_encode($arrAllGroups);?>;
		var posFacilityArr = <?php echo json_encode($posFacilityArr);?>;
		var physician_options = <?php echo json_encode($physicianName);?>;
		var arrAllReports = <?php echo json_encode($arrAllReports);?>;
		var arrAllSceduleNames = <?php echo json_encode($arrAllSceduleNames);?>;
		var operator_options = <?php echo json_encode($operator_options);?>;
		var all_cpt_codes = <?php echo json_encode($all_cpt_codes);?>;
		var all_dx_codes = <?php echo json_encode($all_dx_codes);?>;
		var all_dx_codes10 = <?php echo json_encode($all_dx_codes10);?>;
		var all_ins_group = <?php echo json_encode($all_ins_group);?>;
		var all_ins_company = <?php echo json_encode($all_ins_company);?>;

		var notHavingGroups = new Array('copay_recon','unfinalized_encs','ref_physician','practice_productivity','top_rej_reasons','patients_csv_export');
		var notHavingFacility = new Array('copay_recon','unfinalized_encs','provider_analytics','patient','top_rej_reasons','sx_payment','patients_csv_export');
		var notHavingPhysician = new Array('copay_recon','patient','days_in_insurance','ar_trial_balance','top_rej_reasons','sx_payment','patients_csv_export');
		var notHavingOperator = new Array('day_sheet','unapplied_superbills','unfinalized_encs','practice_analytics','cpt_analysis','yearly','provider_monthly','ref_phy_monthly','facility_monthly','ref_physician','provider_analytics','credit_analysis','patient','practice_productivity','insurance_cases','eid_status','allowable_verify','insurance_deferred','rvu_report','provider_ar','net_gross','ar_reports','days_in_ar','days_in_patient','days_in_insurance','ar_trial_balance','receivables','unworked_ar','unbilled_claims','sx_payment','patients_csv_export');
		var notHavingIns = new Array('day_sheet','payments','front_desk','copay_recon','unapplied_superbills','unfinalized_encs','unapplied_payments','adjustment','refund','daily_balance','fd_collection','yearly','provider_monthly','ref_phy_monthly','facility_monthly','provider_analytics','credit_analysis','patient','eid_status','allowable_verify','insurance_deferred','rvu_report','provider_ar','net_gross','ar_trial_balance','sx_payment','patients_csv_export');
		var notHavingCPT = new Array('day_sheet','payments','front_desk','copay_recon','unapplied_superbills','unfinalized_encs','unapplied_payments','adjustment','refund','daily_balance','fd_collection','yearly','ledger','provider_monthly','ref_phy_monthly','facility_monthly','provider_analytics','credit_analysis','patient','eid_status','allowable_verify','insurance_deferred','rvu_report','provider_ar','net_gross','days_in_ar','days_in_patient','days_in_insurance','ar_trial_balance','receivables','unworked_ar','unbilled_claims','sx_payment','scheduler_report','patients_csv_export');
		var notHavingDX = new Array('day_sheet','payments','front_desk','copay_recon','unapplied_superbills','unfinalized_encs','unapplied_payments','adjustment','refund','daily_balance','fd_collection','cpt_analysis','yearly','ledger','provider_monthly','ref_phy_monthly','facility_monthly','provider_analytics','credit_analysis','patient','insurance_cases','eid_status','allowable_verify','insurance_deferred','rvu_report','provider_ar','net_gross','days_in_ar','days_in_patient','days_in_insurance','ar_trial_balance','receivables','unworked_ar','unbilled_claims','sx_payment','patients_csv_export');
		var havingRVU = new Array('rvu_report','provider_analytics');

		function openDiv(ed,pkId){
			var modal_title = '';
			document.checkInFrm.reset();

			if(typeof(ed)!='undefined' && ed!=''){
				modal_title = 'Edit Record';
			}else{
				modal_title = 'Add New Record';
				$('#pkId').val('');
			}
			
			$('#myModal .modal-header .modal-title').text(modal_title);
			$('#myModal').modal('show');
			if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}else{fillEditData('');}
		}	
		
		function fillEditData(pkId){
			if(pkId!=''){
				$('#pkId').val(pkId);
				var rowAllData  = $('#rowAllData'+pkId).val();
				var arrAllData  = rowAllData.split('$$');
				var ins_groups = ins_compnay ='';
				var dx_codes10='';
				report  = arrAllData[0];
				executionPeriod  = arrAllData[1];

				rptCriteria = arrAllData[2].split('~~');
				group = rptCriteria[0];
				facility = rptCriteria[1];
				physician = rptCriteria[2];
				reportView = rptCriteria[3];
				operator = rptCriteria[4];
				cpt_codes = rptCriteria[5];
				dx_codes = rptCriteria[6];
				ins_groups = rptCriteria[7];
				ins_compnay = rptCriteria[8];
				rvu = rptCriteria[9];
				if(rptCriteria[10]){
					dx_codes10 = rptCriteria[10];
				}

				hours_selected = arrAllData[3];
				months_selected = arrAllData[4];
				weekdays_selected = arrAllData[5];
				quarterly_selected = arrAllData[6];
				status = arrAllData[7];
				schedule_name = arrAllData[8];
				sftp_address = arrAllData[9];
				sftp_user = arrAllData[10];
				sftp_password = arrAllData[11];
				sftp_directory = arrAllData[12];
				sftp_port = arrAllData[13];
				output_option = arrAllData[14];
				
				n=executionPeriod.indexOf('~');
				if(n>0){
					arr=executionPeriod.split('~');
					if(arr[0]!='')$('#Start_date').val(arr[0]);
					if(arr[1]!='')$('#End_date').val(arr[1]);
				}else if(executionPeriod=='year_to_date'){
					$('#year_to_date').prop("checked", true);
				}else{
					$('#executionPeriod').val(executionPeriod);
				}

				$('#schedule_name').val(schedule_name);
				if(reportView=='Summary'){
					document.checkInFrm.process[0].checked=true;
				}else{
					document.checkInFrm.process[1].checked=true;
				}

				if(output_option=='output_csv' || output_option==''){
					document.checkInFrm.output_option[0].checked=true;
				}else{
					document.checkInFrm.output_option[1].checked=true;
				}

				if(rvu=='1'){
					$('#rvu').prop('checked', true);
				}else{
					$('#rvu').prop('checked', false);
				}
				$('#status').val(status);

				rptArr = report.split(',');
				grpArr = group.split(',');
				facArr = facility.split(',');
				phyArr = physician.split(',');
				operArr = operator.split(',');
				insGrpArr = ins_groups.split(',');
				if(ins_compnay!=''){insCompArr = ins_compnay.split(',');}else{insCompArr=new Array();}

				if(cpt_codes!=''){cptArr = cpt_codes.split(',');}else{cptArr=new Array();}
				if(dx_codes!=''){dxArr = dx_codes.split(',');}else{dxArr=new Array();}
				if(dx_codes10!=''){dx10Arr = dx_codes10.split(',');}else{dx10Arr=new Array();}
				hourArr = hours_selected.split(',');
				monthsArr = months_selected.split(',');
				weekdaysArr = weekdays_selected.split(',');
				quarterlyArr = quarterly_selected.split(',');

			}else{
				var report='';
				var quarterly_selected=sftp_address=sftp_user=sftp_password=sftp_directory=sftp_port=output_option='';
				rptArr = new Array();
				grpArr = new Array();
				facArr = new Array();
				phyArr = new Array();
				operArr = new Array();
				cptArr = new Array();
				dxArr = new Array();
				dx10Arr = new Array();
				insGrpArr = new Array();
				insCompArr = new Array();
				hourArr = new Array();
				monthsArr = new Array();
				weekdaysArr = new Array();
				quarterlyArr = new Array();
			}
			
	
			// SET REPORTS 
			$('#report_sel').val(report);

			// SET GROUP OPTIONS			
			$('#groups').selectpicker('val', grpArr);
			$("#groups").selectpicker('refresh');
		
			// SET FACILITY OPTIONS			
			$('#sc_name').selectpicker('val', facArr);
			$("#sc_name").selectpicker('refresh');
			
			// SET PHYSICIAN OPTIONS	
			$('#Physician').selectpicker('val', phyArr);
			$("#Physician").selectpicker('refresh');
			
			$('#operator').selectpicker('val', operArr);
			$("#operator").selectpicker('refresh');

			$('#cpt_codes').selectpicker('val', cptArr);
			$("#cpt_codes").selectpicker('refresh');
			
			$('#dx_codes10').selectpicker('val', dx10Arr);
			$("#dx_codes10").selectpicker('refresh');
			
			$('#ins_groups').selectpicker('val', insGrpArr);
			$("#ins_groups").selectpicker('refresh');
			
			$('#ins_company').selectpicker('val', insCompArr);
			$("#ins_company").selectpicker('refresh');
		

			// SET HOURS
			var dispNum=0;
			var dispVal='';
			var hour_options='';

			var hourArr1=new Array();
			for(x in hourArr){ hourArr1[hourArr[x]] = parseInt(hourArr[x]);}
			for(i=0; i<=23; i++){
				if(i==0){ dispNum='12'; postFix=' a.m.';}
				if(i>=1 && i<=12) { dispNum=i; postFix=' a.m.';}
				if(i>=12){ postFix=' p.m.';}
				if(i==13){ dispNum=1;}
				if(i>=13){ postFix=' p.m.';}
				
				sel='';
				if(jQuery.inArray(i, hourArr1)!='-1'){ sel='selected';	}
				
				hour_options+='<option value="'+i+'" '+sel+'>'+dispNum+':00'+postFix+'</option>';
				dispNum++;
			}
			$("#hour_options").html(hour_options);

			// SET MONTHS
			var dispVal='';
			var month_options='';
			var monthsArr1=new Array();
			for(x in monthsArr){ monthsArr1[monthsArr[x]] = parseInt(monthsArr[x]);}
			var arrAllMonths =new Array()
			arrAllMonths[1] ='January';
			arrAllMonths[2] ='February';
			arrAllMonths[3] ='March';
			arrAllMonths[4] ='April';
			arrAllMonths[5] ='May';
			arrAllMonths[6] ='June';
			arrAllMonths[7] ='July';
			arrAllMonths[8] ='August';
			arrAllMonths[9] ='September';
			arrAllMonths[10] ='October';
			arrAllMonths[11] = 'November';
			arrAllMonths[12] = 'December';
			for(x in arrAllMonths){
				sel='';
				if(jQuery.inArray(parseInt(x), monthsArr1)!='-1'){ sel='selected';	}
				
				month_options+='<option value="'+x+'" '+sel+'>'+arrAllMonths[x]+'</option>';
			}
			$("#month_options").html(month_options);
			$("#month_options").selectpicker('refresh');
			
			// SET WEEKDAYS
			var weekday_options='';
			var weekdaysArr1=new Array();
			for(x in weekdaysArr){ weekdaysArr1[weekdaysArr[x]] = parseInt(weekdaysArr[x]);}
			var arrAllWeekdays =new Array()
			arrAllWeekdays[1] ='Monday';
			arrAllWeekdays[2] ='Tuesday';
			arrAllWeekdays[3] ='Wednesday';
			arrAllWeekdays[4] ='Thursday';
			arrAllWeekdays[5] ='Friday';
			arrAllWeekdays[6] ='Saturday';
			arrAllWeekdays[7] ='Sunday';			
			for(x in arrAllWeekdays){
				sel='';
				if(jQuery.inArray(parseInt(x), weekdaysArr1)!='-1'){ sel='selected';	}
				
				weekday_options+='<option value="'+x+'" '+sel+'>'+arrAllWeekdays[x]+'</option>';
			}
			$("#weekday_options").html(weekday_options);
			$("#weekday_options").selectpicker('refresh');
			
			// SET QUARTERLY
			var quarter_options='';
			var arrAllQuarterly=new Array();
			arrAllQuarterly[1] ='Q1-January 1<sup>st</sup>';
			arrAllQuarterly[4] ='Q2-April 1<sup>st</sup>';
			arrAllQuarterly[7] ='Q3-July 1<sup>st</sup>';
			arrAllQuarterly[10] ='Q4-October 1<sup>st</sup>';
			for(x in arrAllQuarterly){
				sel='';
				if(jQuery.inArray(x, quarterlyArr)!='-1'){ sel='selected';	}
				
				quarter_options+='<option value="'+x+'" '+sel+'>'+arrAllQuarterly[x]+'</option>';
			}
			$("#quarterly").html(quarter_options);
			$("#quarterly").selectpicker('refresh');

			//REPORTS CHECKBOXES
			var selReports = $('#report_sel').val();
			arrSelReports  =selReports.split(',');
			if(arrSelReports.length>0){
				$(".rptChk").each( function () {
					if(jQuery.inArray(this.value, arrSelReports)!='-1'){
						$(this).prop('checked', true);
					}else{
						$(this).prop('checked', false);
					}
				});				
			}
			fill_all_cpt_codes();
			fill_all_dx_codes();
			fill_all_dx10_codes();
			fill_all_ins_companies();
			
			//SET SFTP DETAILS
			$('#sftp_address').val(sftp_address);
			$('#sftp_user').val(sftp_user);
			$('#sftp_password').val(sftp_password);
			$('#sftp_directory').val(sftp_directory);
			$('#sftp_port').val(sftp_port);

			check_reports('anyVal');			
		}

	function submitForm(){
		var msg='';
		var curFrmObj = document.checkInFrm;
		hourOptions = $('#hour_options').val();

		monthOptions = getOptions($('#month_options'));
		weekdayOptions = getOptions($('#weekday_options'));
		quarterly = getOptions($('#quarterly'));
		
		//REPORT CHECKBOXES
		var checkedArr=[];
		var checkedArrDisp=new Array();
		var i=0;
		$(".rptChk").each( function () {
			if($(this).is(':checked')) {
				val  = this.value;
				checkedArr.push(val);
				i++;
			}
		});
		
		selectedReports = '';
		if(checkedArr.length) selectedReports  = checkedArr.join(',');
			
		if(document.getElementById('schedule_name').value==''){
			msg='- "Schedule Name" field value';
		}		
		if(selectedReports==''){
			msg+='<br>- "Reports" field value';
		}
		if(hourOptions==''){
			msg+='<br>- "Hours" field value';
		}
		if(monthOptions=='' && weekdayOptions=='' && quarterly==''){
			msg+='<br>- "Weekday/Months/Quarterly" field value';
		}
		if((monthOptions!='' && weekdayOptions=='') || (monthOptions=='' && weekdayOptions!='')){
			if(monthOptions==''){
				msg+='<br>- Months should be selected with Weekdays';
			}else{
				msg+='<br>- Weekdays should be selected with months';
			}
		}

		var Start_Date=Date.parse($('#Start_date').val());
		var End_Date=Date.parse($('#End_date').val());
		if($('#executionPeriod').val()=='' && !$('#year_to_date').is(':checked') && (Start_Date=='' || End_Date=='')){
			msg+='<br>- Date range field values';
		}
		if(Start_Date!='' && End_Date!='' && Start_Date>End_Date){
			msg+='<br>- "From date" should be small than "To date"';
		}
				
		if(msg!=''){
			top.show_loading_image("hide");
			top.fAlert('Please fill following field/s value!'+'<br><hr>'+msg);
			return false;
		}
		
		pkId = $('#pkId').val();
		schedule_name = ($('#schedule_name').val()).toLowerCase();


		// CHECK IF NAME ALREADY EXISTS
		var tempAllNames = arrAllSceduleNames;
		if(pkId!=''){
			var tempAllNames = arrAllSceduleNames;
			if(tempAllNames[pkId]){	delete tempAllNames[pkId]; }
		}
		for(x in tempAllNames) {
			if(tempAllNames[x]==schedule_name){ 
				top.show_loading_image("hide");
				fAlert('Schedule Name is already used.<br>Please fill different value!');
				return false;
			} 
		}


		
		sel_phy_str = getOptions($('#Physician'));
		curFrmObj.Physician_sel.value = sel_phy_str;
		
		sel_opr_str = getOptions($('#operator'));
		curFrmObj.operator_sel.value = sel_opr_str;
		
		sel_sc_str = getOptions($('#sc_name'));
		curFrmObj.sc_name_sel.value = sel_sc_str;
		
		sel_gp_str = getOptions($('#groups'));
		curFrmObj.grp_id_sel.value = sel_gp_str;
		
		sel_insgp_str = getOptions($('#ins_groups'));
		curFrmObj.ins_groups_sel.value = sel_insgp_str;
		
		curFrmObj.hour_options_sel.value = hourOptions;
		curFrmObj.month_options_sel.value = monthOptions;
		curFrmObj.weekday_options_sel.value = weekdayOptions;
		curFrmObj.quarterly_sel.value = quarterly;

		var date_range=$('#executionPeriod').val();
		if($('#year_to_date').is(':checked')){
			date_range='year_to_date';
		}else if($('#Start_date').val()!='' && $('#End_date').val()!=''){
			date_range=$('#Start_date').val()+'~'+$('#End_date').val();
		}
		curFrmObj.executionPeriod_sel.value = date_range;
		curFrmObj.status_sel.value = $('#status').val();
		$('#report_sel').val(selectedReports);	

		if(curFrmObj.process[0].checked == true){
			curFrmObj.process_sel.value = "Summary";
		}
		else{
			curFrmObj.process_sel.value = "Detail";
		}

		if($('#rvu').is(':checked')){
			curFrmObj.rvu_sel.value = 1;
		}else{
			curFrmObj.rvu_sel.value = 0;
		}
				
		//CPT
		sel_cpt_str = getOptions($('#cpt_codes'));
		
		//DX
		sel_dx_str = getOptions($('#dx_codes'));
		
		//ICD10		
		sel_icd10_str = getOptions($('#dx_codes10'));		
		
		//INS COMPANY
		sel_ins_str = getOptions($('#ins_company'));	
		
		
		curFrmObj.cpt_code_sel.value = sel_cpt_str;
		curFrmObj.dx_code_sel.value = sel_dx_str;
		curFrmObj.dx10_code_sel.value = sel_icd10_str;
		curFrmObj.ins_company_sel.value = sel_ins_str;
		document.checkInFrm.submit_btn.click();
	}

	function activeDeactive(id,status){
		top.show_loading_image("show");
		var url_dt="saved_schedules_ajax.php?pkId="+id+"&status="+status+"&mode=status"; 
		$.ajax({
			url:url_dt,
			success: function(r){ //a=window.open();a.document.write(r); ///*dataType: "json",*/
				if(r=='done'){ 
					if(status=='active'){
						var newStatus='suspend';
						imgPath = '../../library/images/inactive.jpg';
						title='Activate';
					}else{
						var newStatus='active';
						imgPath = '../../library/images/active.jpg';
						title='Suspend';
					}

					// SET IMAGE
					var imgObj = document.getElementById('img'+id);
					imgObj.title = title;
					imgObj.src = imgPath;
					imgObj.onclick=function(){
						activeDeactive(id,newStatus);
					}
					
					// SET ALL HIDDEN DATA
					var rowAllData = $('#rowAllData'+id).val();
					rowAllData=rowAllData.replace(status, newStatus);
					$('#rowAllData'+id).val(rowAllData);
				}
				top.show_loading_image("hide");
			}
		});
	}
	
	function selectQuarter(ctrlId){
		
		if($('#'+ctrlId).is(':checked')) {
			if(ctrlId=='every_quarter'){
				$('#q1').prop('checked', false);
				$('#q4').prop('checked', false);
				$('#q7').prop('checked', false);
				$('#q10').prop('checked', false);
				
				$('#q1').prop('disabled', true);
				$('#q4').prop('disabled', true);
				$('#q7').prop('disabled', true);
				$('#q10').prop('disabled', true);
			}
		}else{
			if(ctrlId=='every_quarter'){
				$('#q1').prop('disabled', false);
				$('#q4').prop('disabled', false);
				$('#q7').prop('disabled', false);
				$('#q10').prop('disabled', false);
			}
		}
		
		if($('#q1').is(':checked') && $('#q4').is(':checked') && $('#q7').is(':checked') && $('#q10').is(':checked')){
			$('#q1').prop('checked', false);
			$('#q4').prop('checked', false);
			$('#q7').prop('checked', false);
			$('#q10').prop('checked', false);
			
			$('#q1').prop('disabled', true);
			$('#q4').prop('disabled', true);
			$('#q7').prop('disabled', true);
			$('#q10').prop('disabled', true);
			
			$('#every_quarter').prop('checked', true);
		}
				
	}

	function popup_dbl(divid,sourceid,destinationid,act,odiv){
		if(act=="single" || act=="all"){
				if(act=='single')	{
					$("#"+sourceid+" option:selected").appendTo("#"+destinationid);
				}else if(act=="all"){$("#"+sourceid+" option").appendTo("#"+destinationid);}
			}else if(act=="single_remove" || act=="all_remove"){
				if(act=="single_remove"){$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);}
				if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
				$("#"+destinationid).append($("#"+destinationid+" option").remove().sort(function(a, b) {
					var at = $(a).text(), bt = $(b).text();
					return (at > bt)?1:((at < bt)?-1:0);
				}));
				$("#"+destinationid).val('');
			}else{
				$("#"+destinationid+" option").remove();
				$("#"+odiv+" option").clone().appendTo("#"+destinationid);
				$("#"+divid).show("clip");
			}
	}
	function selected_ele_close(divid,sourceid,destinationid,div_cover,action){
			if(action=="done"){
				var sel_cnt=$("#"+sourceid+" option").length;
				$("#"+divid).hide("clip");
				$("#"+destinationid+" option").each(function(){$(this).remove();})
				$("#"+sourceid+" option").appendTo("#"+destinationid);
				$("#"+destinationid+" option").attr({"selected":"selected"});
				$("#"+div_cover).width(parseInt($("#"+destinationid).width())+'px');
				if(sel_cnt>8){
					$("#"+div_cover).width(parseInt($("#"+destinationid).width()-15)+"px");	
				}
			}else if(action=="close"){
				$("#"+divid).hide("clip");
			}
	}

	function fill_all_cpt_codes(){

		var cpt_codes = []; 
		$('#cpt_codes option').each(function(){ 
		  id = $(this).val();
		  cpt_codes[id] = $(this).text(); 
		});

		options_val = '';
		for(x in all_cpt_codes){
			if(typeof(cpt_codes[x])=="undefined")
			options_val+="<option value='"+x+"'>"+all_cpt_codes[x]+"</option>";
 		}
		$("#all_cpt_codes").html(options_val);
	}
		
	function fill_all_dx_codes(){
		var dx_codes = []; 
		$('#dx_codes option').each(function(){ 
		  id = $(this).val();
		  dx_codes[id] = $(this).text(); 
		});

		options_val = '';
		for(x in all_dx_codes){
			if(typeof(dx_codes[x])=="undefined")
			options_val+="<option value='"+x+"'>"+all_dx_codes[x]+"</option>";
 		}
		$("#all_dx_codes").html(options_val);
	}

	function fill_all_dx10_codes(){
		var dx_codes10 = []; 
		$('#dx_codes10 option').each(function(){ 
		  id = $(this).val();
		  dx_codes10[id] = $(this).text(); 
		});

		options_val = '';
		for(x in all_dx_codes10){
			if(typeof(dx_codes10[x])=="undefined")
			options_val+="<option value='"+x+"'>"+all_dx_codes10[x]+"</option>";
 		}
		$("#all_dx_codes10").html(options_val);
	}	

	function fill_all_ins_companies(){
		var ins_company = []; 
		$('#ins_company option').each(function(){ 
		  id = $(this).val();
		  ins_company[id] = $(this).text(); 
		});

		options_val = '';
		for(x in all_ins_company){
			val = all_ins_company[x];
			cArr = val.split('*~|#');
			valDisp = cArr[0];
			
			colStyle = '';
			if(cArr[1]=='1'){ colStyle = 'style="color:#CC0000"'; }
			
			if(typeof(ins_company[x])=="undefined")
			options_val+="<option value='"+x+"' "+colStyle+">"+valDisp+"</option>";
 		}
		$("#all_ins_company").html(options_val);
	}
	
	function toTitleCase(str)
	{
    	return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
	}
	
	function check_reports(ctrlId){
		var str= '';
		var rptVal= '';
		var grpDisableVal=facDisableVal=phyDisableVal=oprDisableVal=insDisableVal=cptDisableVal=dx10DisableVal=false;
		var rvuDisableVal=false; otherDisableVal=false;
		var isDisabled=0;
		
		var vali = $('#groups').val();
		
		if(getOptions($('#groups'))){ grpDisableVal=true;}
		if(getOptions($('#sc_name'))){ facDisableVal=true;}
		if(getOptions($('#Physician'))){ phyDisableVal=true;}
		if(getOptions($('#operator'))){ oprDisableVal=true;}
		if(getOptions($('#ins_groups'))){ insDisableVal=true;}
		if(getOptions($('#ins_company'))){ insDisableVal=true;}
		if(getOptions($('#cpt_codes'))){ cptDisableVal=true;}
		if(getOptions($('#dx_codes10'))){ dx10DisableVal=true;}
		if($('#rvu').is(':checked')){ rvuDisableVal=false; otherDisableVal=true;}

		var str='';
		$(".rptChk").each( function () {
			isDisabled=0;
			rptVal = $(this).val();
			if(jQuery.inArray(rptVal, notHavingGroups)!='-1'){ 
				$(this).prop('disabled', grpDisableVal);
				if(grpDisableVal==true){ $(this).prop('checked', false); isDisabled=1;} 
			}
			if(isDisabled=='0'){
				if(jQuery.inArray(rptVal, notHavingFacility)!='-1'){ 
					$(this).prop('disabled', facDisableVal);
					if(facDisableVal==true){ $(this).prop('checked', false); isDisabled=1;} 
				}
			}
			if(isDisabled=='0'){
				if(jQuery.inArray(rptVal, notHavingPhysician)!='-1'){ 
					$(this).prop('disabled', phyDisableVal);
					if(phyDisableVal==true){ $(this).prop('checked', false); isDisabled=1;} 
				}
			}
			if(isDisabled=='0'){
				if(jQuery.inArray(rptVal, notHavingOperator)!='-1'){ 
					$(this).prop('disabled', oprDisableVal);
					if(oprDisableVal==true){ $(this).prop('checked', false); isDisabled=1;} 
				}
			}
			if(isDisabled=='0'){
				if(jQuery.inArray(rptVal, notHavingIns)!='-1'){ 
					$(this).prop('disabled', insDisableVal);
					if(insDisableVal==true){ $(this).prop('checked', false); isDisabled=1;} 
				}
			}
			if(isDisabled=='0'){
				if(jQuery.inArray(rptVal, notHavingCPT)!='-1'){ 
					$(this).prop('disabled', cptDisableVal);
					if(cptDisableVal==true){ $(this).prop('checked', false); isDisabled=1;} 
				}
			}
					
			if(isDisabled=='0'){
				if(jQuery.inArray(rptVal, notHavingDX)!='-1'){ 
					$(this).prop('disabled', dx10DisableVal);
					if(dx10DisableVal==true){ $(this).prop('checked', false); isDisabled=1;} 
				}
			}						
			
			if(isDisabled=='0'){
				if(jQuery.inArray(rptVal, havingRVU)!='-1'){ 
					$(this).prop('disabled', rvuDisableVal);
				}else{
					$(this).prop('disabled', otherDisableVal);
					if(otherDisableVal==true){ $(this).prop('checked', false);} 
				}
			}			
		});
	}
	
	function getOptions(obj){
		var returnVal = '';
		var optArr = [];
		
		if($(obj).length) obj = $(obj);
		else return returnVal;
		
		$(obj).find('option:selected').each(function(id, elem){
			var elemVal = $(elem).val();
			if(elemVal) optArr.push(elemVal);
		});	
		
		if(optArr.length) returnVal = optArr.join(',');
		return returnVal;
	}

	function setDateControls(ctrl){
		var dateControls=['executionPeriod','year_to_date','Start_date','End_date'];
		var currentCtrlId=$(ctrl).attr('id');
		var ctrl_id='';

		if(currentCtrlId=='year_to_date'){
			if($('#'+currentCtrlId).is(':checked')) {
				for(x in dateControls){
					ctrl_id=dateControls[x];

					if(ctrl_id!=currentCtrlId){
						$('#'+ctrl_id).val('');
					}
				}	
			}
		}else{
			if($('#'+currentCtrlId).val()!=''){
				//IF ANY OF TWO CONTROLS SELECTED THEN REMOVE THEM FROM ARRAY TO AVOID THEM TO BE EMPTY
				if(currentCtrlId=='Start_date' || currentCtrlId=='End_date'){
					dateControls.splice(2, 2);
				}

				for(x in dateControls){
					ctrl_id=dateControls[x];

					if(ctrl_id!=currentCtrlId){
						if(ctrl_id=='year_to_date'){
							$('#'+ctrl_id).prop('checked', false);
						}else{
							$('#'+ctrl_id).val('');
						}
					}
				}			
			}
		}
	}

	$(document).ready(function(){
		var ar = [["add_new","Add New","top.fmain.openDiv();"]];
		top.btn_show("ADMN",ar);
		set_header_title('Saved Schedules');
		top.show_loading_image('none');
		$('.selectpicker').selectpicker();
	});
	
</script>	
	