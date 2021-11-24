<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();


if (isset($saved_searched_id) && $saved_searched_id != '') {
    $saved_id = $saved_searched_id;
}

$searchOptions = '';
$srchQry = "Select id, DATE_FORMAT(saved_date, '".$date_format_SQL." %H:%i:%s') as saved_date,search_data, report, report_name, uid FROM 
reports_searches WHERE report='productivity_procedural_criteria' ORDER BY saved_date DESC";
$srchRs = imw_query($srchQry);
while ($srchRes = imw_fetch_array($srchRs)) {
   $sel = '';
   if($saved_id == $srchRes['id']) {
		$searchCriteria =$saved_id = $srchRes['id'];
		$dataParts = explode('~', $srchRes['search_data']);
		
		$groups = explode(',',$dataParts[0]);
		$cpt_cat_id = explode(',',$dataParts[1]);
		$Procedure = explode(',',$dataParts[2]);
		$sc_name = explode(',',$dataParts[3]);
		$Physician = explode(',',$dataParts[4]);
		$ins_carriers = explode(',',$dataParts[5]);
		$DateRangeFor = $dataParts[6];
		$_POST['dayReport'] = $dataParts[7];
		if($_POST['dayReport']=='Selected Date'){ $_POST['dayReport']='Date';}
		$_REQUEST['Start_date'] = $dataParts[8];
		$_REQUEST['End_date'] = $dataParts[9];
		$selTotalMethod = $dataParts[10];
		$_POST['groupby'] = $dataParts[11];
		$process = $dataParts[12];
		//$dispChart = $dataParts[13];
		$includeDelIns = $dataParts[14];
		$includeDelCPT = $dataParts[15];
		$_REQUEST['sort_by'] = $dataParts[16];
		$crediting_provider = explode(',',$dataParts[17]);
		$_POST['chksamebillingcredittingproviders'] = $dataParts[18];
   }

	$sel2 = '';
	$sel2 = ($_POST['savedCriteria'] == $srchRes['id']) ? 'selected' : ''; //SELECTION IS BASED ON SELECTED VALUE OF DD
	$searchOptions .= '<option value="' . $srchRes['id'] . '" title="'.$GLOBALS['webroot'].'/library/images/delete_icon.png" '. $sel2 . '>' . trim($srchRes['report_name']). '</option>';

	$arrSearchName[]=trim($srchRes['report_name']);	
}
json_encode($arrSearchName);

if($_POST['form_submitted']) {
	$groups_sel = array_combine($groups, $groups);
	$sc_name_sel = array_combine($sc_name, $sc_name);
	$strPhysician = implode(',',$Physician);
	$str_credit_physician= implode(',', $crediting_provider);
	$ins_carriers_sel = array_combine($ins_carriers, $ins_carriers);
	$ins_group_sel = array_combine($ins_group, $ins_group);
	$cpt_cat_id_sel = array_combine($cpt_cat_id, $cpt_cat_id);
	$Procedure_sel = array_combine($Procedure, $Procedure);
}

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}
//--- GET Groups SELECT BOX ----
$group_query = "Select gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
	if(in_array($group_id,$grp_id))$sel='SELECTED';
	$groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}
$grp_cnt=sizeof($group_id_arr);

//--- GET FACILITY SELECT BOX ----
$posfacilityName = $CLSReports->getFacilityName($sc_name_sel, '1');
$posfac_cnt = sizeof(explode('</option>', $posfacilityName)) - 1;

//--- GET PHYSICIAN NAME ---
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;

//CREDITING PHYSICIAN
$creditPhysicinName = $CLSCommonFunction->drop_down_providers($str_credit_physician, '1', '1', '', 'report');
$allCrPhyCount = sizeof(explode('</option>', $creditPhysicinName)) - 1;


//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
    if ($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance' && $insQryRes[$i]['attributes']['insCompName']!='') {
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
		if (sizeof($ins_carriers_sel) > 0) {
			if ($ins_carriers_sel[$ins_id]) {
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

//GET INSURANCE GROUP DROP DOWN
$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
$ins_group_arr = array();
$ins_group_options = '';
while ($row = imw_fetch_array($insGroupQryRes)) {
    $ins_grp_id = $row['id'];
    $ins_grp_name = $row['title'];

    $qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $row['id'] . "' ORDER BY id";
    $res = imw_query($qry);
    $tmp_grp_ins_arr = array();
    if (imw_num_rows($res) > 0) {
        while ($det_row = imw_fetch_array($res)) {
            $tmp_grp_ins_arr[] = $det_row['id'];
        }
        $selected = '';
        $grp_ins_ids = implode(",", $tmp_grp_ins_arr);
        $ins_group_arr[$grp_ins_ids] = $ins_grp_name;
		if ($ins_group_sel[$grp_ins_ids])
            $selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}

//--- GET ALL CPT CATEGORIES NAME FOR SEARCH ---
$qry = "select cpt_cat_id, cpt_category from cpt_category_tbl order by cpt_category";
$rs = imw_query($qry);
//$category_data_arr = array();
$category_data='';
$category_data_arr[0] = 'No CPT Category';
while($res=imw_fetch_assoc($rs)){
	$sel='';
	$id = $res['cpt_cat_id'];
	$category_data_arr[$id] = $res['cpt_category'];
	if($cpt_cat_id_sel[$id])$sel='SELECTED';
	$category_data.='<option value="'.$id.'" '.$sel.'>'.$res['cpt_category'].'</option>';
}

//CPT CODES
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl 
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color =$sel= '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = $row['cpt_prac_code'];
    if ($row['delete_status'] == 1 || $row['status'] == 'Inactive'){
        $color = 'color:#CC0000!important';
	}
	$cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
	if($Procedure_sel[$cpt_fee_id])$sel='SELECTED';
	$cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
}
$allCPTCatCount = sizeof($cptDetailsArr);
//CSV NAME
$dbtemp_name = 'CPT Analysis';
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";

$logicWidth = 65;
$logicDiv = reportLogicInfo('cpt_analyses', 'tpl', $logicWidth);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: imwemr ::</title>
        <!-- Bootstrap -->
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
              <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
              <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->

        <style>
            .pd5.report-content {
                position:relative;
                margin-left:40px;

                background-color: #EAEFF5;
            }
            .fltimg {
                position:absolute;
            }
            .fltimg span.glyphicon {
                position: absolute;
                top: 170px;
                left: 10px;
                color: #fff;
            }
            .reportlft .btn.btn-mkdef {
                padding-top: 6px;
                padding-bottom: 6px;
            }
            #content1{
                background-color:#EAEFF5;
            }
			.total-row {
				height: 1px;
				padding: 0px;
				background: #009933;
			}	
		</style>
    </head>
<body>
  <form name="frm_reports" id="frm_reports" action="" method="post">
		<input type="hidden" name="form_submitted" id="form_submitted" value="1">
        <input type="hidden" name="callby_saved_dropdown" id="callby_saved_dropdown" value="<?php echo $_POST['callby_saved_dropdown'];?>">
		<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
		<div class=" container-fluid">
			<div class="anatreport">
				<div id="common_drop" style="position:absolute;bottom:0px;"></div>
				<div class="row" id="row-main">
					<div class="col-md-3" id="sidebar">
						<div class="reportlft" style="height:100%;">
							<div class="practbox">
								<div class="anatreport"><h2>Practice Filter
								<div id="rptInfoImg" style="float:right" class="rptInfoImg" onClick="showHideReportInfo(event, '<?php echo $logicWidth;?>')"></div>
								</h2></div>
								<div class="clearfix"></div>
								<div class="pd5" id="searchcriteria">
									<div class="row">
										<div class="col-sm-6">
											<label>Groups</label>
											<select name="grp_id[]" id="grp_id" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $groupName; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Facility</label>
											<select name="sc_name[]" id="sc_name" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $posfacilityName; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Provider</label>
											<select name="Physician[]" id="Physician" data-container="#common_drop" data-container="#provider_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $physicianName; ?>
											</select>
										</div>
										<div class="col-sm-6">
										  <label>Crediting Provider</label>
										  <select name="crediting_provider[]" id="crediting_provider" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
											  <?php echo $creditPhysicinName; ?>
										  </select>
										</div>										
										<div class="col-sm-12">
											<div class="checkbox pointer" style="padding-top:5px; padding-bottom:10px">
												<input type="checkbox" name="chksamebillingcredittingproviders" id="chksamebillingcredittingproviders" value="1" <?php if($_POST['chksamebillingcredittingproviders']==1)echo 'checked';?>  />
												<label for="chksamebillingcredittingproviders">Exclude where billing and crediting providers are same</label>
											</div>										  
										</div>																				
										<div class="col-sm-4">
										<label>Charges Method</label>
											<select name="total_method" id="total_method" data-container="#common_drop" class="selectpicker" data-width="100%" data-actions-box="false" >
											   <option value="total_charges" <?php if($total_method=='total_charges')echo 'SELECTED';?>>Total Charges</option>
											   <option value="contract_price" <?php if($total_method=='contract_price')echo 'SELECTED';?>>Contract Price</option>
											</select>
										</div>
										<div class="col-sm-8">
											<label>Period</label>
											<div id="dateFieldControler">
												<select name="dayReport" id="dayReport" class="selectpicker" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
													<option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'selected="selected"'; ?>>Daily</option>
													<option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'selected="selected"'; ?>>Weekly</option>
													<option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'selected="selected"'; ?>>Monthly</option>
													<option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'selected="selected"'; ?>>Quarterly</option>
													<option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'selected="selected"'; ?>>Date Range</option>
												</select>
											</div>
											<div class="row" style="display:none" id="dateFields">
												<div class="col-sm-5">
													<div class="input-group">
														<input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick">
														<label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
													</div>
												</div>	
												<div class="col-sm-5">	
													<div class="input-group">
														<input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo $_REQUEST['End_date']; ?>" class="form-control date-pick">
														<label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
													</div>
												</div>
												<div class="col-sm-2">
													<button type="button" class="btn" onclick="DateOptions('x');"><span class="glyphicon glyphicon-arrow-left"></span></button>
												</div>
											</div>
										</div>	
										<div class="col-sm-6">
											<br />
											<div class="radio radio-inline pointer">
												<input type="radio" name="process" id="Summary" value="Summary" checked <?php if ($_POST['process'] == 'Summary') echo 'CHECKED'; ?> /> 
												<label for="Summary">Summary</label>
											</div>
											<div class="radio radio-inline pointer">
												<input type="radio" name="process" id="Detail" value="Detail" <?php if ($_POST['process'] == 'Detail') echo 'CHECKED'; ?> /> 
												<label for="Detail">Detail</label>
											</div>
										</div>
										<div class="col-sm-6"><br />
												<div class="radio radio-inline pointer">
													<input type="radio" name="DateRangeFor" id="date_of_service" value="date_of_service" <?php if ($_POST['DateRangeFor'] == 'date_of_service' || !$_POST['form_submitted']) echo 'CHECKED'; ?> /> 
													<label for="date_of_service">DOS</label>
												</div>
												<div class="radio radio-inline pointer">
													<input type="radio" name="DateRangeFor" id="date_of_payment" value="date_of_payment" <?php if ($_POST['DateRangeFor'] == 'date_of_payment') echo 'CHECKED'; ?> /> 
													<label for="date_of_payment">DOR</label>
												</div>
												<div class="radio radio-inline pointer">
													<input type="radio" name="DateRangeFor" id="transaction_date" value="transaction_date" <?php if ($_POST['DateRangeFor'] == 'transaction_date') echo 'CHECKED'; ?> /> 
													<label for="transaction_date">DOT</label>
												</div>
										</div>
									</div>
								</div>
							</div>
							<div class="appointflt">
								<div class="anatreport"><h2>Analytic Filter</h2></div>
								<div class="clearfix"></div>
								<div class="pd5" id="searchcriteria">
									<div class="row">
										<div class="col-sm-6">
											<label>CPT Category</label>
											<select name="cpt_cat_id[]" id="cpt_cat_id" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
											<?php echo $category_data;?>
											</select>
										</div>
										<div class="col-sm-6">
											<label for="cpt">CPT Code</label>
											<select name="Procedure[]" id="Procedure" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $cpt_code_options; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label for="cpt_cat_2">CPT Category 2</label>
											<select name="cpt_cat_2[]" id="cpt_cat_2" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select All">
												<option value="1" <?php echo (in_array('1',$cpt_cat_2) ? 'selected' : '');?>>Service</option>
												<option value="2" <?php echo (in_array('2',$cpt_cat_2) ? 'selected' : '');?>>Material</option>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Ins. Group</label>
											<select name="ins_group[]" id="ins_group" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $ins_group_options; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Ins. Carrier</label>
											<select name="ins_carriers[]" id="ins_carriers" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $insComName_options; ?>
											</select>
										</div>
										
										<div class="col-sm-6" >
											<label for="sort_by">Sort By</label>
											<select name="sort_by" id="sort_by" class="selectpicker" data-width="100%" data-title="Sort By" >
												<option value="patient" <?php echo ($_REQUEST['sort_by'] == 'patient' ? 'selected' : '');?>>Patient</option>  
												<option value="cpt" <?php echo ($_REQUEST['sort_by'] == 'cpt' ? 'selected' : '');?>>CPT Code</option>  
												<option value="dos" <?php echo (!in_array($_REQUEST['sort_by'],array('patient','cpt')) ? 'selected' : '');?>>Date of Service</option>  
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="grpara">
								<div class="anatreport"><h2>Group BY</h2></div>
								<div class="clearfix"></div>
								<div class="pd5" id="searchcriteria">
									<div class="row">
                                    
										<div class="col-sm-4">
											<div class="radio radio-inline pointer">
												<input type="radio" name="groupby" id="Physicians" value="Physician" <?php if ($_POST['groupby'] == 'Physician' || !$_POST['form_submitted']) echo 'CHECKED'; ?> /> 
												<label for="Physicians">Physician</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="radio radio-inline pointer">
												<input type="radio" name="groupby" id="Facility"  value="Facility" <?php if ($_POST['groupby'] == 'Facility') echo 'CHECKED'; ?>/> 
												<label for="Facility">Facility</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="radio radio-inline pointer">
												<input type="radio" name="groupby" id="Procedures" value="Procedure" <?php if ($_POST['groupby'] == 'Procedure') echo 'CHECKED'; ?>/> 
												<label for="Procedures">Procedure</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="radio radio-inline pointer">
												<input type="radio" name="groupby" id="CPTCategory" value="CPTCategory" <?php if ($_POST['groupby'] == 'CPTCategory') echo 'CHECKED'; ?>/> 
												<label for="CPTCategory">CPT Category</label>
											</div>
										</div>

                                        <div class="col-sm-4">	
                                            <div class="radio radio-inline pointer">
                                            <input type="radio" name="groupby" id="grpby_ins_groups" value="ins_group" <?php if ($_POST['groupby'] == 'ins_group') echo 'CHECKED'; ?>/> 
                                            <label for="grpby_ins_groups">Ins Groups (Pri. Ins)</label>
                                            </div>
                                        </div>

                                        
									</div>
								</div>
							</div>

                          <div class="grpara">
                            <div class="anatreport"><h2>Saved Criteria</h2></div>
                            <div class="clearfix"></div>
                            <div class="pd5" id="searchcriteria">
                                <div class="row">
                                    <div class="col-sm-5">
                                      <label>Saved Searches</label>
                                       <select name="savedCriteria" id="savedCriteria" style="width:100%;" data-maincss="blue" onchange="javascript:dChk=0; callSavedSearch(this.value, 'frm_reports'); saved_functionality('savedCriteria');">
                                         <option value="" >Select</option>
                                          <?php echo $searchOptions; ?>
                                        </select> 
                                        <input type="hidden" name="saved_searched_id" id="saved_searched_id" value="">
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="checkbox pointer" style="padding-top:17px">
                                        <input type="checkbox" name="chkSaveSearch" id="chkSaveSearch" value="1" onClick="javascript:show_saved();" />
                                        <label for="chkSaveSearch">Save</label>
                                        </div>
                                    </div> 
                                    <div class="col-sm-5" id="div_search_name" style="display:none" >
                                        <label>Name of Search</label>
                                        <input type="text" name="search_name" id="search_name" value="" class="form-control" onBlur="javascript: saved_functionality('search_name');" />
                                    </div>                               
                              </div>
                            </div>
                          </div> 
                            
							<div class="grpara">
								<div class="anatreport"><h2>Format</h2></div>
								<div class="clearfix"></div>
								<div class="pd5" id="searchcriteria">
									<div class="row">
									   <div class="col-sm-4">
											<div class="radio radio-inline pointer">
												<input type="radio" name="output_option" id="output_view_only" value="view" <?php if ($_POST['output_option'] == 'view' || !$_POST['form_submitted']) echo 'CHECKED'; ?> />
												<label for="output_view_only">View Only</label>
											</div>
										</div>

										<div class="col-sm-4">
											<div class="radio radio-inline pointer">
												<input type="radio" name="output_option" id="output_pdf" value="output_pdf" <?php if ($_POST['output_option'] == 'output_pdf') echo 'CHECKED'; ?>/> 
												<label for="output_pdf">PDF</label>
											</div>
										</div>                                                
										<div class="col-sm-4">
											<div class="radio radio-inline pointer" style="padding-left: 42%;">
												<input type="radio" name="output_option" id="output_csv" value="output_csv" <?php if ($_POST['output_option'] == 'output_csv') echo 'CHECKED'; ?>/> 
												<label for="output_csv">CSV</label>
											</div>
										</div>  
									</div>
								</div>
							</div>
							<div class="grpara">
								<div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
									<button class="savesrch" type="button" onClick="top.fmain.get_report()">Search</button>
								</div>
							</div>                                                                                        
						</div>
					</div>
					<div class="col-md-9" id="content1">
						<div class="btn-group fltimg" role="group" aria-label="Controls">
							<img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="toggle-sidebar glyphicon glyphicon-chevron-left"></span>
						</div>
						<div class="pd5 report-content">
							<div class="rptbox">
								<div id="html_data_div" class="row">
									<?php
									if($_POST['callby_saved_dropdown']==''){
										if($_POST['form_submitted']) {
											include('ProceduralResult.php');
										}else{
											echo '<div class="text-center alert alert-info">No Search Done.</div>';
										}
									}else{
										echo '<div class="text-center alert alert-info">No Search Done.</div>';
									}

									?>
								</div>
							</div>
						</div>
						<?php echo $logicDiv;?>
					</div>
				</div>
			</div>
		</div>
	</form>
	<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
		<input type="hidden" name="csv_text" id="csv_text">	
		<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
	</form>
	<form name="csvDownloadForm2" id="csvDownloadForm2" action="downloadCSV.php" method ="post" > 
		<input type="hidden" name="file" id="file" value="<?php echo $file;?>" />
		<input type="hidden" name="zipName" id="zipName" value="<?php echo $zipName;?>" />
	</form>	
	<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
</form>
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	$(function () { $('[data-toggle="tooltip"]').tooltip(); });
	var createZip = '<?php echo $createZip; ?>';
	var arrSearchName=[];
	var arrSearchName=<?php echo json_encode($arrSearchName); ?>;

	$(document).ready(function () {
		$(".fltimg").click(function () {
			$("#sidebar").toggleClass("collapsed");
			$("#content1").toggleClass("col-md-12 col-md-9");

			if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
			} else {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
			}
			return false;
		});
		if(createZip == 1){
			document.csvDownloadForm2.submit();
			top.show_loading_image('hide');
		}	
	});
	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
	}
	top.btn_show("PPR", mainBtnArr);

	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		
		$('#callby_saved_dropdown').val('');

		if(dbtemp_name=='Referring Revenue'){
			var ctrlObj=$('#selectedRef');
			if(!ctrlObj.val()){
				top.show_loading_image('hide');
				alert('Please select referring physician groups.');
				return false;
			}
		}

		if ($('#chkSaveSearch').prop('checked') == true) {
			if($('#savedCriteria').val()=='' && $('#search_name').val()==''){
				top.show_loading_image('hide');
				alert('Please select saved search or enter new search name.');
				return false;
			}
			else if($('#search_name').val()!=''){
				for(x in arrSearchName) {
					if (arrSearchName[x] == $('#search_name').val()) {
						top.show_loading_image('hide');
						alert('Search name already exist.');
						return false;
					}
				}
			}
		}
		document.frm_reports.submit();
	}
	
	lightBoxFlag = 0;
	function toggle_lightbox(show_hide_flag) {
		show_hide_flag = show_hide_flag || '';
		if (show_hide_flag == 'hide')
			lightBoxFlag = 1;
		else if (show_hide_flag == 'show')
			lightBoxFlag = 0;

		var popupid = '#divLightBox';
		if (!lightBoxFlag) {
			$(popupid).fadeIn();
			lightBoxFlag = 1;
		} else {
			$(popupid).fadeOut();
			lightBoxFlag = 0;
		}
		$('#report_name').val('');
		var popuptopmargin = ($(popupid).height() + 10) / 2;
		var popupleftmargin = ($(popupid).width() + 10) / 2;
		$(popupid).css({
			'margin-top': -popuptopmargin,
			'margin-left': -popupleftmargin
		});
	}

	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, 'l');
			window.close();
		}
	}

	function addRemoveGroupBy(dateRangeFor) {
		if (dateRangeFor == 'date_of_service') {
			$("#viewBy").append('<option value="operator">Operator</option>');
			$('#without_deleted_amounts').attr('disabled', true);
		} else {
			$("#viewBy option[value='operator']").remove();
			$('#without_deleted_amounts').attr('disabled', false);
		}
	}

   function show_saved(){
	   if($('#chkSaveSearch').is(":checked")){
		   $('#div_search_name').css('display','block');
	   }else{
		   $('#div_search_name').css('display','none');
	   }
   }
	
   function saved_functionality(callfrom){
	   if(callfrom=='search_name'){
		   if($('#search_name').val()!=''){
				$('#savedCriteria_child ul li').removeClass('selected');
				var oDropdown = $("#savedCriteria").msDropdown().data("dd");
				oDropdown.set("selectedIndex", 0);
		   }
	   }else{
		   if($('#savedCriteria').val()!=''){
			   $('#search_name').val('');
			   $('#div_search_name').css('display','none');
		   }else if($('#savedCriteria').val()=='' && $('#chkSaveSearch').is(":checked")){
			   $('#div_search_name').css('display','block');
		   }
	   }
   }


	$(document).ready(function (e) {
		DateOptions('<?php echo $_POST['dayReport'];?>');

		function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
		$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});	

		$("#savedCriteria").msDropdown({roundedBorder: false});
		oDropdown = $("#savedCriteria").msDropdown().data("dd");
		oDropdown.visibleRows(10);
	} 

	$(window).load(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
		//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
		if(printFile==1){
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);
	});
</script>
</body>
</html>