<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

if ($_POST['form_submitted']) {
    $facility_id = array_combine($facility_id, $facility_id);
    $strPhysician = implode(',', $physician);
    $insuranceGrp = array_combine($insuranceGrp, $insuranceGrp);
    $ins_carriers = array_combine($ins_carriers, $ins_carriers);
    $cpt_code_id = array_combine($cpt, $cpt);
    $dx_code10 = array_combine($icd10_codes, $icd10_codes);
}

//--- GET FACILITY SELECT BOX ----
$facility_id= array_combine($_REQUEST['facility_id'],$_REQUEST['facility_id']);
$facilityName = $CLSReports->getFacilityName($facility_id, '1');
$allFacCount = sizeof(explode('</option>', $facilityName)) - 1;

//--- GET PHYSICIAN NAME ---
$strPhysician = implode(',',$_REQUEST['physician']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;

//CPT CODES
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl 
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color = '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = $row['cpt_prac_code'];
    if($row['delete_status'] == 1 || $row['status'] == 'Inactive')
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


//SET ICD10 CODE DROP DOWN
$arrICD10Code = $CLSReports->getICD10Codes();
$all_dx10_code_options = '';
$sel_dx10_code_options = '';
foreach ($arrICD10Code as $dx10code) {
	$dx10code = str_replace("'", "", $dx10code);
	$sel = ($dx_code10[$dx10code]) ? 'selected' : '';
    $all_dx10_code_options .= "<option value='" . $dx10code . "' ".$sel.">" . $dx10code . "</option>";
}
$allDXCount10 = sizeof($arrICD10Code);

//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
    if ($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
        $ins_id = $insQryRes[$i]['attributes']['insCompId'];
        $ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
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

        if ($insuranceGrp[$grp_ins_ids])
            $selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}

$dbtemp_name = 'Consult Letters';
//CSV NAME
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";

$xml_path=data_path(1).'xml/refphy';

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
            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
			<div class=" container-fluid">
                <div class="anatreport">
					<div id="select_drop" style="position:absolute;bottom:0px;"></div>
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Practice Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        
										<div class="row">
											<div class="col-sm-6">
												<label>Facility</label>
                                                <select name="facility_id[]" id="facility_id" class="selectpicker" data-container="#select_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $facilityName; ?>
                                                </select>
											</div>
											<div class="col-sm-6">
                                                <label>Provider</label>
												<select name="physician[]" id="physician" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $physicianName; ?>
												</select>
                                            </div>
											<div class="col-sm-12">
												<label for="reff_physician"><span class="text_purple">Referring Physicians</span></label>
												<input class="form-control" id="reff_physician" name="reff_physician" value="<?php echo $_REQUEST['reff_physician']; ?>" />
												<input type="hidden" name="id_reff_physician" id="id_reff_physician" value="<?php echo $_REQUEST['id_reff_physician']; ?>" />
											</div>
                                            <div class="col-sm-12">
                                              <label>Period</label>
                                              <div id="dateFieldControler">
                                                <select name="dayReport" id="dayReport" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['date_range']))?'disabled':''; ?> data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
                                                <option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'SELECTED'; ?>>Daily</option>
                                                <option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'SELECTED'; ?>>Weekly</option>
                                                <option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'SELECTED'; ?>>Monthly</option>
                                                <option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'SELECTED'; ?>>Quarterly</option>
                                                <option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'SELECTED'; ?>>Date Range</option>
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
										</div>
                                    </div>
                                </div>

								<div class="appointflt">
                      	<div class="anatreport"><h2>Analytic Filter</h2></div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
                          	
                            <div class="col-sm-4">
                            	<label for="insuranceGrp">Insurance Group</label>
                              <select name="insuranceGrp[]" id="insuranceGrp" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['ins_group']))?'disabled':''; ?> onChange="checkFields();">
                             		<?php echo $ins_group_options; ?>
                            	</select>
                          	</div>
                            
                            <div class="col-sm-4" >
                            	<label for="ins_carriers">Ins. Carriers</label>
                              <select name="ins_carriers[]" id="ins_carriers" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['ins_carriers']))?'disabled':''; ?> onChange="checkFields();">
                              <?php echo $insComName_options; ?>
                              </select>
                           	</div>

                            <div class="col-sm-4" >
                            	<label for="cpt">CPT</label>
                              <select name="cpt[]" id="cpt" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['cpt']))?'disabled':''; ?> onChange="checkFields();">
                               <?php echo $cpt_code_options; ?>
                              </select>
                           	</div>

                            <div class="col-sm-4" >
                            	<label for="icd10_codes">ICD10 Codes</label>
                              <select name="icd10_codes[]" id="icd10_codes" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['icd10_codes']))?'disabled':''; ?> onChange="checkFields();">
                               <?php echo $all_dx10_code_options; ?>
                              </select>
                           	</div>
							<!-- <div class="col-sm-4" >
								<label for="phy_type">Physicians Type</label>
								<select name="phy_type" id="phy_type" data-container="#select_drop" class="selectpicker" data-width="100%" data-title="All">
									<option value="all" <?php echo ($phy_type == 'all' ? 'selected' : '');?>>All</option>  
									<option value="pcp" <?php echo ($phy_type == 'pcp' ? 'selected' : '');?>>Primary Care Physician</option>
								</select>
							</div> -->
                            
							<div class="col-sm-4">
                                <label for="age">Age</label>
                                <input type="text" name="age_from" id="age_from" placeholder="From" value="<?php echo $_REQUEST['age_from'];?>" class="form-control" />
                            </div>                          
                            <div class="col-sm-4">
                                <label for="age"></label>
                                <input type="text" name="age_to" id="age_to"  placeholder="To" value="<?php echo $_REQUEST['age_to'];?>" class="form-control" />
                            </div>                          

							<div class="clearfix"></div>

                            <div class="col-sm-4 mt5">
                                <label class="checkbox checkbox-inline pointer">
                                    <input style="cursor:pointer; margin-left:2px"  type="checkbox" name="no_letter" class="letter_status" id="no_letter" value="1" <?php if ($_POST['no_letter'] == '1') echo 'CHECKED'; ?>/>
                                    <label for="no_letter">No Letter</label>
                                </label>
                            </div>	
                            
                            <div class="col-sm-4 mt5" >
                                <label class="checkbox checkbox-inline pointer">
                                    <input style="cursor:pointer; margin-left:2px"  type="checkbox" name="unsent" class="letter_status" id="unsent" value="1" <?php if ($_POST['unsent'] == '1') echo 'CHECKED'; ?>/>
                                    <label for="unsent">Unsent</label>
                                </label>
                            </div>	                            
                            
                            <div class="col-sm-4 mt5">
                                <label class="checkbox checkbox-inline pointer">
                                    <input style="cursor:pointer; margin-left:2px"  type="checkbox" name="resend" class="letter_status" id="resend" value="1" <?php if ($_POST['resend'] == '1') echo 'CHECKED'; ?>/>
                                    <label for="resend">Resend</label>
                                </label>
                            </div>	
                                                        
                          	<!-- Add Sort By Filter for practice analytics report only -->
                          	<?php if ( $dbtemp_name == 'Practice Analytics') { ?>
                          	<div class="col-sm-4" >
                            	<label for="sort_by">Sort By</label>
                              <select name="sort_by" id="sort_by" class="selectpicker pull-right" data-width="100%" data-title="Sort By" >
																<option value="patient" <?php echo ($sort_by == 'patient' ? 'selected' : '');?>>Patient</option>  
			                          <option value="cpt" <?php echo ($sort_by == 'cpt' ? 'selected' : '');?>>CPT Code</option>  
			                          <option value="dos" <?php echo (!in_array($sort_by,array('patient','cpt')) ? 'selected' : '');?>>Date of Service</option>  
                              </select>
                          	</div>
                          	<?php } ?>
                            
                        	</div>
                       	</div>
                    	</div>                                
                        
								<div class="grpara">
									<div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
                                        <button class="savesrch" id="btn_search" type="button" onClick="top.fmain.get_report()">Search</button>
                                    </div>
                                </div>                                                                                        
                            </div>
                        </div>
                        <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
                            </div>
							<div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
                                        <?php
										if($_POST['form_submitted']) {
											include('consult_letter_report_result.php');
										}else{
                                            echo '<div id="page_div" class="text-center alert alert-info">No Search Done.</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
            </div>
        </form>

        <div id="send_fax_div" style="display:none;" class="modal in" role="dialog">
        <form action="" method="post" name="frm_send_fax" id="frm_send_fax" onSubmit="sendMultipleConsultLetterFax(); return false;">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="javascript:document.getElementById('send_fax_div').style.display='none';">&times;</button>
                <h4 class="modal-title">Send Fax</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tr>
                        <td><b>Subject:</b></td>
                        <td colspan="3"><input type="text" name="send_fax_subject" id="send_fax_subject" class="form-control" value=""/></td>
                    </tr>
                    <tr>    
                        <td>Ref.&nbsp;Phy:</td>
                        <td>
                        <input type="hidden" name="hiddselectReferringPhy" id="hiddselectReferringPhy">
                        <input type="text" name="selectReferringPhy"  id="selectReferringPhy" autocomplete="off" onKeyUp="top.loadPhysicians(this,'hiddselectReferringPhy', '<?php echo $xml_path;?>','send_fax_number','','','','','',top.fmain);">
                        </td>
                        <td>Fax&nbsp;No.:</td>
                        <td><input type="text" name="send_fax_number" id="send_fax_number" onChange="set_fax_format(this,'');" autocomplete="off"></td>
                   </tr>
                   <tr>    
                        <td>Cc1:</td>
                        <td>
                        <input type="hidden" name="hiddselectReferringPhyCc1" id="hiddselectReferringPhyCc1">
                        <input type="text" name="selectReferringPhyCc1"  id="selectReferringPhyCc1" autocomplete="off" onKeyUp="top.loadPhysicians(this,'hiddselectReferringPhyCc1', '<?php echo $xml_path;?>','send_fax_numberCc1','','','','','',top.fmain);">
                        </td>
                       
                        <td>Fax&nbsp;No.:</td>
                        <td><input type="text" name="send_fax_numberCc1" id="send_fax_numberCc1" autocomplete="off" onChange="set_fax_format(this,'');"></td>
                   </tr>
                   <tr>    
                        <td>Cc2:</td>
                        <td>
                        <input type="hidden" name="hiddselectReferringPhyCc2" id="hiddselectReferringPhyCc2">
                        <input type="text" name="selectReferringPhyCc2" id="selectReferringPhyCc2" onKeyUp="top.loadPhysicians(this,'hiddselectReferringPhyCc2', '<?php echo $xml_path;?>','send_fax_numberCc2','','','','','',top.fmain);">
                        </td>
                       
                        <td>Fax&nbsp;No.:</td>
                        <td><input type="text" name="send_fax_numberCc2" id="send_fax_numberCc2" autocomplete="off" onChange="set_fax_format(this,'');"></td>
                   </tr>
                   <tr>    
                        <td>Cc3:</td>
                        <td>
                        <input type="hidden" name="hiddselectReferringPhyCc3" id="hiddselectReferringPhyCc3">
                        <input type="text" name="selectReferringPhyCc3" id="selectReferringPhyCc3" autocomplete="off" onKeyUp="top.loadPhysicians(this,'hiddselectReferringPhyCc3', '<?php echo $xml_path;?>','send_fax_numberCc3','','','','','',top.fmain);">
                        </td>
                       
                        <td>Fax&nbsp;No.:</td>
                        <td><input type="text" name="send_fax_numberCc3" id="send_fax_numberCc3" autocomplete="off" onChange="set_fax_format(this,'');"></td>
                   </tr>
                </table>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="form-group">
                    <input type="submit" value="Send Fax"  class="btn btn-success" />
                    <input type="button" onclick="javascript:document.getElementById('send_fax_div').style.display='none';" value="Close" id="fax_cancel_btn" class="btn btn-danger">
                    
                    <input type="hidden" name="sendFaxCase" id="sendFaxCase" value="1">
                    <input type="hidden" name="str_ids" id="str_ids" value="">
                    </div>
                </div>
            </div>
            <div id="div_load_image" style="left:50px;top:0px; width:200px; position:absolute; display:none; z-index:1000; ">
                <img src="../../library/images/loading_image.gif">
            </div>
            </div>
            </div>
            </form>
        </div>
                            
		<?php $csvName = preg_replace('/\s+/', '_', $dbtemp_name); ?>
		<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
			<input type="hidden" name="csv_text" id="csv_text">	
			<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $csvName; ?>.csv" />
		</form>
		<div id="counsult_letter_patient" style="width:770px; top:115px; left:350px; position:absolute; overflow:hidden; display:none;"></div>
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	//var arrForAjax=<?php echo json_encode($arrForAjax);?>;
	
	$(function () { $('[data-toggle="tooltip"]').tooltip(); });
	
	function checkFields(){
		var insuranceGrp = $('#insuranceGrp option:selected').text();
		var ins_carriers = $('#ins_carriers option:selected').text();
		var icd10_codes = $('#icd10_codes option:selected').text();
		var cpt = $('#cpt option:selected').text();
		if(insuranceGrp != '' || ins_carriers != '' || icd10_codes != '' || cpt != ''){
			$("#no_letter").prop("disabled", false);
			$("#unsent").prop("disabled", false);
			$("#resend").prop("disabled", false);
		} else {
			$("#no_letter").prop("disabled", true);
			$("#unsent").prop("disabled", true);
			$("#resend").prop("disabled", true);
		}
	}
	
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
		$("body").on("click","#check_all",function() {
			if(this.checked){
			   $("input[name=letter_patients]").prop('checked', true);
			}else{
			  $("input[name=letter_patients]").prop('checked', false);
			}
		});
		checkFields();
	});

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	
	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("sent", "Send Letter", "top.fmain.send_letter();");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
		mainBtnArr[2] = new Array("sebd_fax", "Send Fax", "top.fmain.send_fax();");
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	

	
	$(function() {
		$("#reff_physician").click(function(){window.open('refPhy.php?name_field=reff_physician&id_field=id_reff_physician','RefPhy','height=400, width=400, top=200, left=300, location=no, toolbar=0, menubar=0, status=0,resizable=1');});	
		$("#reff_physician").blur(function(){checkVal('reff_physician');});
	});
	
	function checkVal(ctrlId){
		if(document.getElementById(ctrlId).value==''){
			$('#id_reff_physician').val(''); 
		}
	}
	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		document.frm_reports.submit();
	}
	function send_fax(){
		$('#send_fax_div').css('display', 'block');
	}
	
	function send_letter(){
		var i=0;	
		var arr=new Array();
		var str_ids='';
		$("input[name='letter_patients']").each( function () {
		   if($(this).is(':checked')){
			    con_id=$(this).val();
				//if(arrForAjax[con_id]=='n'){
					arr[i]= con_id;
					i++;
				//}
		   }
		});
		
		if(arr.length>0){
			str_ids=arr.join(',');

			$.ajax({
				type: "POST",
				url: 'consult_letters_update.php',
				data: "str_ids="+str_ids,
				success: function(resp){
					if(resp){
						top.show_loading_image('hide');
						var result=resp.split('~~');
						
						if (result[0] != '') {
							top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
							top.html_to_pdf(result[0], 'p');
							window.close();
							
							if(result[1]=='1'){ //AGAIN SEARCH ONLY DO IF ANY DATABASE UPDATE DONE.
								$('#btn_search').click();
							}
						}
						
					}
				}
			});
			
		}else{
			alert('No patient selected.');
		}
	}
	
	function getConsultOtherProvider(fromDate, toDate){
	var fromDate = fromDate;
	var toDate = toDate;	
	var url="get_counsult_other_provider.php?fromDate="+fromDate+"&toDate="+toDate;
	$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				if(resp){
					top.show_loading_image("none");
					document.getElementById("counsult_letter_patient").style.display = "block";		
					document.getElementById("counsult_letter_patient").innerHTML = resp;
				}else{
					document.getElementById("counsult_letter_patient").innerHTML = "";
					document.getElementById("counsult_letter_patient").style.display = "none";		
					var fromDate = document.getElementById("from_date").value;
					var toDate = document.getElementById("to_date").value;
					var intIncSentDate = 0;
					if(document.getElementById("cbkInculdeSentDate").checked == true){
						intIncSentDate = 1;
					}
					getConsultPatient(fromDate, toDate, intIncSentDate);
				}
			}
		});
	}
	
	function getConsultPatient(fromDate, toDate, intIncSentDate){
		var fromDate = fromDate;
		var toDate = toDate;		
		var intIncSentDate = intIncSentDate;
		var selectedFac = "";
		var selectedProvider = "";
		var sel_fac_id_arr = new Array;
		$('#comboFac option').each(function(id,elem){
		if($(elem).is(':selected')){
			var value = $(elem).val();
				sel_fac_id_arr.push(value);
			}
		});
		if(sel_fac_id_arr.length > 0) {
			selectedFac = sel_fac_id_arr.join(','); 
		}
		
		var sel_pro_id_arr = new Array;
		$('#comboProvider option:selected').each(function(id,elem){
		var value = $(elem).val();
			sel_pro_id_arr.push(value);
		});
		if(sel_pro_id_arr.length > 0) {
			selectedProvider = sel_pro_id_arr.join(','); 
		}
		var url = 'get_counsult_letter_patient.php?fromDate='+fromDate+'&toDate='+toDate+'&intIncSentDate='+intIncSentDate+'&selectedFac='+selectedFac+'&selectedProvider='+selectedProvider;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				if(resp){
					top.show_loading_image("none");
					document.getElementById("counsult_letter_patient").style.display = "block";		
					document.getElementById("counsult_letter_patient").innerHTML = resp;
					document.getElementById("page_div").innerHTML = "Result populate in popup.";
				} else{
					document.getElementById("counsult_letter_patient").innerHTML = "";
					document.getElementById("counsult_letter_patient").style.display = "none";		
					top.show_loading_image("none");
					fAlert("Sorry no record(s) found!");
				}
			}
		});
	}
	
	function closeCousultDiv(){
		$("#counsult_letter_patient").hide();
		document.getElementById("page_div").innerHTML = "Please search.";
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

		$('.letter_status').click(function() {
			$('.letter_status').not(this).prop('checked', false);  
		});
		
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
	
	function closeOthProDiv(){
		var fromDate; 
		if(document.getElementById("from_date").value == ""){
			alert("Please enter date.");
			return false;
		}				
		else{
			var fromDate = document.getElementById("from_date").value;
			var toDate = document.getElementById("to_date").value;
			var intIncSentDate = 0;
			if(document.getElementById("cbkInculdeSentDate").checked == true){
				intIncSentDate = 1;
			}
			getConsultPatient(fromDate, toDate, intIncSentDate);
		}				
		//document.getElementById("counsult_letter_patient").style.display = "none";
	}
	function registerOtherprovider(){		
		var otherProviderVal = "";
		var field = document.getElementsByName("cbkOthPro");		
		if(document.getElementById("otherProvider").value == ""){
			for (var i = 0; i < field.length; i++){
				if(field[i].checked == true){								
					otherProviderVal += field[i].value+"~";
				}	
			}	
		}

		if(otherProviderVal != ""){			
			var strLen = otherProviderVal.length; 
			otherProviderVal = otherProviderVal.slice(0,strLen-1); 			
			document.getElementById("otherProvider").value = otherProviderVal;			
			//alert(document.getElementById("otherProvider").value);					
			document.frmRegOtherProvider.submit();
			var fromDate = document.getElementById("from_date").value;
			var toDate = document.getElementById("to_date").value;
			var intIncSentDate = 0;
			if(document.getElementById("cbkInculdeSentDate").checked == true){
				intIncSentDate = 1;
			}
			getConsultPatient(fromDate, toDate, intIncSentDate);
		}
		else if(document.getElementById("otherProvider").value != ""){	
			otherProviderVal =	document.getElementById("otherProvider").value;
			var strLen = otherProviderVal.length; 
			if(otherProviderVal.substring(strLen-1,strLen) == "~"){
				otherProviderVal = otherProviderVal.slice(0,strLen-1); 			
			}
			document.getElementById("otherProvider").value = otherProviderVal;			
			//alert(document.getElementById("otherProvider").value);					
			document.frmRegOtherProvider.submit();
			fromDate = document.getElementById("from_date").value;
			var toDate = document.getElementById("to_date").value;
			var intIncSentDate = 0;
			if(document.getElementById("cbkInculdeSentDate").checked == true){
				intIncSentDate = 1;
			}
			getConsultPatient(fromDate, toDate, intIncSentDate);
		}
		else{
			fAlert("Please select provider(s) to register them.");
		}
	}	

	
	function sendMultipleConsultLetterFax(){
		var arr=new Array();


		var i=0;	
		$("input[name='letter_patients']").each( function () {
		   if($(this).is(':checked')){
			    var con_id=$(this).val();
				arr[i]= con_id;
				i++;
		   }
		});


	
		var SendFaxValue="";
		SendFaxObj=document.getElementById('send_fax_number');
		string=SendFaxObj.value;
		TrimStringValue=string.replace(/^\s+|\s+$/g,"");
		SendFaxObj.value=TrimStringValue;
		
/*		if(SendFaxObj.value!=""){
			if(document.getElementById("div_load_image")) {
				document.getElementById("div_load_image").style.display="inline-block";
			}
		}	
*/		
		if(arr.length>0){
			str_ids=arr.join(',');
			
			if(SendFaxObj.value!=""){	
				document.getElementById('str_ids').value=str_ids;
				var serialize=$("#frm_send_fax").serializeArray();
					
				$.ajax({
					type: "POST",
					url: 'consult_letters_update.php',
					data: serialize,
					success: function(resp){
						if(resp){
							data = jQuery.parseJSON(resp);
							
							sndFxFun(data['faxnumber'],data['faxnumberCc1'],data['faxnumberCc2'],data['faxnumberCc3'],data['getIP'],data['rqHidConsultLeterId'],data['refPhyId'],data['send_fax_subject'], data['phy'],data['phycc1'],data['phycc2'],data['phycc3']);
						}
					}
				});				
				
			}else{top.fAlert("please enter the fax number");SendFaxObj.focus();return false;}	
		}
		else{
			top.fAlert("Please select patient(s) to get their report.");
		}
		
		return false;
	}

 
  function sndFxFun(fxNmbr,fxNmbrCc1,fxNmbrCc2,fxNmbrCc3,serverIp,hid_consult_letter_ids,refPhyId,send_fax_subject, phy,phycc1,phycc2,phycc3){

		var u_path="<?php echo $GLOBALS['webroot']; ?>/library/html_to_pdf/createPdf.php?saveOption=fax&pdf_name="+serverIp.replace('.html', '')+"&file_location="+serverIp;
		
		$.ajax({
			type: "GET",
			url: u_path,
			success: function(resp){
				$.ajax({
					type: "GET",
					url: "consult_letters_report_fax.php?send_fax_number="+fxNmbr+"&txtFaxNoCc1="+fxNmbrCc1+"&txtFaxNoCc2="+fxNmbrCc2+"&txtFaxNoCc3="+fxNmbrCc3+"&txtFaxPdfName=faxConsultLetterReportPdf_"+serverIp+"&consult_letter_ids="+hid_consult_letter_ids+"&ref_phy_id="+refPhyId+"&send_fax_subject="+send_fax_subject+'&phyname='+phy+'&phycc1name='+phycc1+'&phycc2name='+phycc2+'&phycc3name='+phycc3,
					success: function(resp){
						resp = $.trim(resp);
						resp = $.parseJSON(resp);
						var alertSuccess = '';
						var alertError = '';
						//=================UPDOX FAX WORKS GET DATA FROM SENDFAX.PHP FILE=================
						//PRIMARY RECIPENT DATA
						if( typeof(resp.primary.fax_id) !== 'undefined' ){ //resp.primary.
							alertSuccess += 'Primary: '+resp.primary.fax_id+'\n';
						}  
						else if( typeof(resp.primary.error) !== 'undefined' ) alertError += 'primary: '+resp.primary.error+'\n';
						//CC1 RECIPENT DATA
						if( typeof(resp.cc1.fax_id) !== 'undefined' ){
							alertSuccess += 'CC1: '+resp.cc1.fax_id+'\n';
						}
						else if( typeof(resp.cc1.error) !== 'undefined' ) alertError += 'CC1: '+resp.cc1.error+'\n';
						//CC2 RECIPENT DATA
						if( typeof(resp.cc2.fax_id) !== 'undefined' ){ 
							alertSuccess += 'CC2: '+resp.cc2.fax_id+'\n';
						}
						else if( typeof(resp.cc2.error) !== 'undefined' ) alertError += 'CC2: '+resp.cc2.error+'\n';
						//CC3 RECIPENT DATA	
						if( typeof(resp.cc3.fax_id) !== 'undefined' ){
							alertSuccess += 'CC3: '+resp.cc3.fax_id+'\n';
						}
						else if( typeof(resp.cc3.error) !== 'undefined' ) alertError += 'CC3: '+resp.cc3.error+'\n';
						
						var alertMsg = '';
						if(alertSuccess!=='')
							alertMsg += 'Fax sent successfully:  \n'+alertSuccess+'\n';  //<br />'+alertSuccess+"<br />"
						if(alertError!=='')
							alertMsg += 'Fax sending Failed: \n'+alertError+'\n';   // <br />'+alertError+"<br />"
						
						alert(alertMsg);
						if(document.getElementById("div_load_image")) {
							document.getElementById("div_load_image").style.display="none";
							document.getElementById("hiddselectReferringPhy").value="";
							document.getElementById("selectReferringPhy").value="";
							document.getElementById("send_fax_number").value="";
							document.getElementById("send_fax_div").style.display="none";
						}
						/* alert(r); */
					}
				});
			}
		});
	}	 
			
	function set_fax_format(obj, format){
	console.log(obj);
	fax_min_length = top.phone_min_length;
	default_format = format || top.phone_format;
	phone_reg_exp_js = "[^0-9+]";
	
	regExp = new RegExp(phone_reg_exp_js,'g');
	var refinedPh = obj.value;
	refinedPh = refinedPh.trim();
	if(refinedPh==''){
		return;
	}
	refinedPh = refinedPh.replace(regExp,"");
	if(refinedPh.length < fax_min_length){
		invalid_input_msg(obj, "Please Enter a valid Fax number");return;
	}else{
			switch(default_format){
				case "###-###-####":
					obj.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6);
				break;
				case "(###) ###-####":
					obj.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6);
				break;
				case "(##) ###-####":
					obj.value = "("+refinedPh.substring(0,2)+") "+refinedPh.substring(2,5)+"-"+refinedPh.substring(5);
				break;
				case "(###) ###-###":
					obj.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6);
				break;
				case "(####) ######":
					obj.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4);
				break;
				case "(####) #####":
					obj.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4);
				break;
				case "(#####) #####":
					obj.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5);
				break;
				case "(#####) ####":
					obj.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5);
				break;
				default:
					obj.value = refinedPh;
				break;
			}
	}
	//changeClass(obj);
}

function setFaxNumber(faxObj,Cc1Obj,Cc2Obj,Cc3Obj){
	var faxName = faxNo = ccName = ccFax = "";
	if(faxObj.value!=0 && faxObj.value!='') {
		var faxDetail = arrRefIdNameFax[faxObj.value];
		var faxDetailArr = new Array();
		if(faxDetail!="" && faxDetail!="-" && faxDetail!="undefined"){
			faxDetailArr= faxDetail.split("@@");
			faxName 	= faxDetailArr[0];
			faxNo 		= faxDetailArr[1];
			document.getElementById("send_fax_number").value=faxNo;	
		}
	}
	for(var i=1;i<=3;i++) {
		if(i==1) 		{ CcObj = Cc1Obj;
		}else if(i==2) 	{ CcObj = Cc2Obj;
		}else if(i==3) 	{ CcObj = Cc3Obj;
		}
		if(CcObj.value!=0 && CcObj.value!='') {
			var ccDetail = arrRefIdNameFax[CcObj.value];
			var ccDetailArr = new Array();
			if(ccDetail!="" && ccDetail!="-" && ccDetail!="undefined"){
				ccDetailArr = ccDetail.split("@@");
				ccName 		= ccDetailArr[0];
				ccFax 		= ccDetailArr[1];
				document.getElementById("send_fax_numberCc"+i).value=ccFax;	
			}
		}
	}
		
}

function getFax_Num(){
	//timer_refPhyFax = setTimeout("return setFaxNumber(document.getElementById('hiddselectReferringPhy'))",1000);
	timer_refPhyFax = setTimeout("return setFaxNumber(document.getElementById('hiddselectReferringPhy'),document.getElementById('hiddselectReferringPhyCc1'),document.getElementById('hiddselectReferringPhyCc2'),document.getElementById('hiddselectReferringPhyCc3'))",1);
}
//== End Function ===//	

function faxChbxClick(objChBx,RefId,Cc1RefId,Cc2RefId,Cc3RefId,preffered_fax) {
	var refName = refFax = ccName = ccFax = "";
	document.getElementById("selectReferringPhy").value="";
	document.getElementById("send_fax_number").value="";
	if(RefId!=0 && objChBx.checked==true) {
		var refDetail = arrRefIdNameFax[RefId];
		var refDetailArr = new Array();
		if(refDetail!="" && refDetail!="-" && refDetail!="undefined"){
			refDetailArr= refDetail.split("@@");
			refName 	= refDetailArr[0];
			refFax 		= refDetailArr[1];
			document.getElementById("selectReferringPhy").value=refName;
			document.getElementById("send_fax_number").value=refFax;	
		}
	}
	
	for(var i=1;i<=3;i++) {
		if(i==1) 		{ CcRefId = Cc1RefId;
		}else if(i==2) 	{ CcRefId = Cc2RefId;
		}else if(i==3) 	{ CcRefId = Cc3RefId;
		}
		
		document.getElementById("selectReferringPhyCc"+i).value="";
		document.getElementById("send_fax_numberCc"+i).value="";	
		
		if(CcRefId!=0 && objChBx.checked==true) {
			var ccDetail = arrRefIdNameFax[CcRefId];
			var ccDetailArr = new Array();
			if(ccDetail!="" && ccDetail!="-" && ccDetail!="undefined"){
				ccDetailArr = ccDetail.split("@@");
				ccName 		= ccDetailArr[0];
				ccFax 		= ccDetailArr[1];
				document.getElementById("selectReferringPhyCc"+i).value=ccName;
				document.getElementById("send_fax_numberCc"+i).value=ccFax;	
				if(document.getElementById("selectReferringPhy").value=="" && i==1){
					document.getElementById("selectReferringPhy").value=ccName;
					document.getElementById("send_fax_number").value=ccFax;
					document.getElementById("selectReferringPhyCc"+i).value="";
					document.getElementById("send_fax_numberCc"+i).value="";
				}
			}
		}
	}
	if(preffered_fax){
		reff_preff=preffered_fax.split("~||~");
		document.getElementById("selectReferringPhy").value=reff_preff[0];
		document.getElementById("send_fax_number").value=reff_preff[1];
	}	
}

function selDeSelAllChkBox(op){	
	if(document.getElementById("cbkSelectAll").checked == false){
		if(op == "sel"){
			op = "deSel";
		}
	}		
	var field = document.getElementsByName("cbk");
	var hidPatientVal = hidConsultLeterId = "";
	if(op == "sel"){
	//	document.getElementById("cbkDeSelectAll").checked = false;
		for (i = 0; i < field.length; i++){
			field[i].checked = true;
			var arr = field[i].value.split("-");					
			var patid = arr[0];
			var consultLeterId = arr[1];
			hidPatientVal += patid+",";
			hidConsultLeterId += consultLeterId+",";
			//hidPatientVal += field[i].value+",";				
		}
		var strLen = hidPatientVal.length; 
		hidPatientVal = hidPatientVal.slice(0,strLen-1); 	
		
		var strLenConLt = hidConsultLeterId.length; 			
		hidConsultLeterId = hidConsultLeterId.slice(0,strLenConLt-1);
		
		document.getElementById("patients").value = hidPatientVal;				
		document.getElementById("hidConsultLeterId").value = hidConsultLeterId;				
	}
	else if(op == "deSel"){
		document.getElementById("cbkSelectAll").style.visibility
		document.getElementById("cbkSelectAll").checked = false;
		for (i = 0; i < field.length; i++){
			if(field[i].style.display == "inline"){ 
				field[i].checked = false;
			}
		}	
		document.getElementById("patients").value = "";				
		document.getElementById("hidConsultLeterId").value = "";
	}
}
	
	function getConsultReport(){
	if(document.getElementById("sendFaxCase")){
		document.getElementById("sendFaxCase").value="";
	}
	var hidPatientVal = hidConsultLeterId = "";
	var field = document.getElementsByName("cbk");		
	if(document.getElementById("patients").value == ""){
		for (var i = 0; i < field.length; i++){
			if(field[i].checked == true){
				var arr = field[i].value.split("-");					
				var patid = arr[0];
				var consultLeterId = arr[1];
				hidPatientVal += patid+",";
				hidConsultLeterId += consultLeterId+",";
			}	
		}	
	}
	
	if(hidPatientVal != ""){			
		var strLen = hidPatientVal.length; 
		hidPatientVal = hidPatientVal.slice(0,strLen-1); 	
		
		var strLenConLt = hidConsultLeterId.length; 			
		hidConsultLeterId = hidConsultLeterId.slice(0,strLenConLt-1);
		
		document.getElementById("patients").value = hidPatientVal;
		document.getElementById("hidConsultLeterId").value = hidConsultLeterId;			
		
		document.frmConsultLetterDiV.submit();
	}
	else if(document.getElementById("patients").value != ""){	
		hidPatientVal =	document.getElementById("patients").value;
		var strLen = hidPatientVal.length; 
		if(hidPatientVal.substring(strLen-1,strLen) == ","){
			hidPatientVal = hidPatientVal.slice(0,strLen-1); 			
		}
		
		hidConsultLeterId =	document.getElementById("hidConsultLeterId").value;
		var strLenConLt = hidConsultLeterId.length; 
		if(hidConsultLeterId.substring(strLenConLt-1,strLenConLt) == ","){
			hidConsultLeterId = hidConsultLeterId.slice(0,strLenConLt-1); 			
		}
		document.getElementById("patients").value = hidPatientVal;			
		document.getElementById("hidConsultLeterId").value = hidConsultLeterId;			
		
		document.frmConsultLetterDiV.submit();
	}
	else{
		fAlert("Please select patient(s) to get their report.");
	}
}
</script>
</body>
</html>