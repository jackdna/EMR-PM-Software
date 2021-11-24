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
PURPOSE : AR WORKSHEET SEARCH FILTERS
ACCESS TYPE : INCLUDED
*/
$without_pat = "yes";
include_once(dirname(__FILE__)."/../../config/globals.php");
$session_user_id = $_SESSION['authId'];
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;
//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();
$op='l';
$pfx=",";
$drop_option_limit=10;
$action_msg="";
$ord_by_field=$_POST['ord_by_field'];
$ord_by_ascdesc=$_POST['ord_by_ascdesc'];
if($_POST['old_action_type']=="write_off_update"){
	$action_msg="Write Off updated successfully.";
}else if($_POST['old_action_type']=="rebill_update"){
	$action_msg="Rebilled successfully.";
}else if($_POST['old_action_type']=="claim_update"){
	$action_msg="Claim generted successfully.";
}else if($_POST['old_action_type']=="status_update"){
	$action_msg="Claim status updated successfully.";
}else if($_POST['old_action_type']=="statement_update"){
	$action_msg="Statement printed successfully.";
	$update_pat_statement="yes";
	require_once('../reports/update_statement.php');
}else if($_POST['old_action_type']=="assign_to_update"){
	$action_msg="Task assigned successfully.";
}else if($_POST['old_action_type']=="followup_update"){
	$action_msg="Follow Up added successfully.";
}
$please_select="<option value=\"\">Please Select</option>";
if($_POST['form_submitted']){
    $facility_id_sel = array_combine($facility_id, $facility_id);
	$filing_provider_sel = array_combine($filing_provider, $filing_provider);
	$ins_carriers_sel = array_combine($ins_carriers, $ins_carriers);
	$printable_columns_sel = array_combine($printable_columns, $printable_columns);
    $operator_id_sel= array_combine($follow_up_opr_id, $follow_up_opr_id);
	$rej_code_sel= array_combine($rej_code, $rej_code);
    $what_user_sel= array_combine($what_user, $what_user);
    $insuranceGrp_sel = array_combine($insuranceGrp, $insuranceGrp);
    $cpt_code_id = array_combine($cpt_code, $cpt_code);
    $ins_types_sel = array_combine($ins_types, $ins_types);
	$ins_pri_sel= array_combine($ins_priority, $ins_priority);
	$reason_sel = array_combine($appt_reason, $appt_reason);
	$status_sel = array_combine($status, $status);
}
if (sizeof($printable_columns_sel) <= 0) {
	$_POST['printable_columns']= array("Facility", "Patient Name - ID", "DOB", "Ins. ID", "Provider", "DOS", "DOC", "CPT", "ICD10", "Charge","Aging", "Balance");
	$printable_columns_sel = array_combine($_POST['printable_columns'], $_POST['printable_columns']);
}

$fac_ord_by=$ins_ord_by=$prov_ord_by=$pos_fac_ord_by="";
if($_POST['ord_by_field']=="Facility_ord" && $_POST['ord_by_ascdesc']=='DESC'){
	$fac_ord_by=" DESC";
}
if($_POST['ord_by_field']=="POS Facility_ord" && $_POST['ord_by_ascdesc']=='DESC'){
	$pos_fac_ord_by=" DESC";
}
if($_POST['ord_by_field']=="Insurance_ord" && $_POST['ord_by_ascdesc']=='DESC'){
	$ins_ord_by=" DESC";
}
if($_POST['ord_by_field']=="Provider_ord" && $_POST['ord_by_ascdesc']=='DESC'){
	$prov_ord_by=" DESC";
}
if($_POST['ord_by_field']=="pat_name_ord" && $_POST['ord_by_ascdesc']=='DESC'){
	$pat_ord_by=" DESC";
}
	

//GET SCHDEDULE FACILITY
$facArr=$drop_fac_arr=array();
$fac_query = imw_query("select id,name from facility order by name".$fac_ord_by);
while ($fac_res = imw_fetch_assoc($fac_query)) {
    $facArr[$fac_res['id']] = stripslashes($fac_res['name']);
	$drop_fac_arr[$fac_res['id']] = stripslashes($fac_res['name']);
}
if($fac_ord_by!=""){asort($drop_fac_arr, SORT_STRING | SORT_FLAG_CASE);}

$PosFacArr=$drop_fac_arr=array();
//------------------------ POS Facility ------------------------//
$selQry = "select * from pos_facilityies_tbl order by facilityPracCode".$pos_fac_ord_by;
$res = imw_query($selQry);
while($row = imw_fetch_assoc($res)){
	$PosFacArr[$row['pos_facility_id']] = stripslashes($row['facilityPracCode']);
	$drop_pos_fac_arr[$row['pos_facility_id']] = stripslashes($row['facilityPracCode']);
}
if($pos_fac_ord_by!=""){asort($drop_pos_fac_arr, SORT_STRING | SORT_FLAG_CASE);}

//GET ALL OPERATORS DETAILS
$usersArr=$drop_user_arr=array();
$res = imw_query("Select id, lname, fname,mname, delete_status, user_type,user_npi,TaxonomyId from users WHERE lname!='' ORDER BY delete_status asc, lname".$prov_ord_by.", fname".$prov_ord_by);
while ($row = imw_fetch_assoc($res)) 
{
	$usersArr[$row['id']]['user_type']=$row['user_type'];
	$usersArr[$row['id']]['delete_status']=$row['delete_status'];
	$usersArr[$row['id']]['lname']=$row['lname'];
	$usersArr[$row['id']]['fname']=$row['fname'];
	$usersArr[$row['id']]['mname']=$row['mname'];
	$usersArr[$row['id']]['name']=ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname']));
	$usersArrInt[$row['id']]=substr($row['fname'],0,1).substr($row['mname'],0,1).substr($row['lname'],0,1);
	$usersArr[$row['id']]['user_npi']=$row['user_npi'];
	$usersArr[$row['id']]['TaxonomyId']=$row['TaxonomyId'];
	$drop_user_arr[$row['id']] = ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname']));
}
if($prov_ord_by!=""){asort($drop_user_arr, SORT_STRING | SORT_FLAG_CASE);}
/*$reff_qry=imw_query("select LastName,FirstName,MiddleName,NPI,Texonomy from refferphysician where physician_Reffer_id='$reffPhyscianId'");
$reffDetail=imw_fetch_assoc($reff_qry);*/

$reff_phy_arr = array();
$qry =imw_query("select physician_Reffer_id,NPI from refferphysician");
while($row=imw_fetch_assoc($qry)){
	$reff_phy_arr[$row['physician_Reffer_id']]=$row;
}

$insCompArr=$drop_ins_comp_arr=array();
$qry =imw_query("select id,name,in_house_code,phone,fax,email,groupedIn,ins_del_status,claim_filing_days,payment_due_days from insurance_companies order by name".$ins_ord_by);	
while($row=imw_fetch_assoc($qry)){
	$insCompArr[$row['id']]=$row;
	$drop_ins_comp_arr[$row['id']] = $row['name'];
}
if($ins_ord_by!=""){
	asort($drop_ins_comp_arr, SORT_STRING | SORT_FLAG_CASE);
	$drop_ins_comp_arr[0]='Pat Due';
}else{
	$drop_ins_comp_arr[0]='Pat Due';
}

$insCompArr[0]['name']='Pat Due';
$insCompArr[0]['in_house_code']='Patient Due';
//collect ins comp ids as per there assigned group id
$tmp_grp_ins_arr = array();
foreach($insCompArr as $insCompId=>$subArr) {
	if($subArr['groupedIn'])
	{
		$tmp_grp_ins_arr[$subArr['groupedIn']][]=$insCompId;
	}
}
//GET INSURANCE GROUP DROP DOWN
$insGroupArr = array();
$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
while ($row = imw_fetch_assoc($insGroupQryRes)) {
    $ins_grp_id = $row['id'];
    $ins_grp_name = $row['title'];

	if(isset($tmp_grp_ins_arr[$row['id']]))
	{
        $grp_ins_ids = implode(",", $tmp_grp_ins_arr[$row['id']]);
        $insGroupArr[$grp_ins_ids] = $ins_grp_name;
	}
}
$arr_ins_case_type_all = array();
$qry_ins_case_type =imw_query("select case_id,case_name from insurance_case_types where insurance_case_types.status='0' order by case_name");
while($fet_ins_case_type=imw_fetch_assoc($qry_ins_case_type)){
	$arr_ins_case_type_all[$fet_ins_case_type['case_id']]=$fet_ins_case_type['case_name'];
}
//AGING BY
$agingByArr=array("Date of Service","First Claim Date","Last Claim Date");
//STATUS ARRAY
$statusArr = array();
$qry_claim_status =imw_query("SELECT id,status_name FROM claim_status where del_status='0' ORDER BY status_name ASC");
while($fet_claim_status=imw_fetch_assoc($qry_claim_status)){
	$statusArr[$fet_claim_status['id']]=$fet_claim_status['status_name'];
}
//INSURANCE TYPES
$insTypeArr = array('primaryInsuranceCoId' => 'Primary', 'secondaryInsuranceCoId' => 'Secondary', 'tertiaryInsuranceCoId' => 'Tertiary', 'Self Pay' => 'Self Pay');
//INSURANCE PRIORITY
$insPriArr = array('1' => "Primary", '2' =>  "Secondary",  '3' => "Tertiary");
//APPOINTMENT PROCEDURES/REASON
$apptReasonArr=array();
$rs=imw_query("Select id, proc from slot_procedures WHERE source='' and proc<>'' and doctor_id=0");
while($res=imw_fetch_assoc($rs)){
	$apptReasonArr[$res['id']]=$res['proc'];
}
//FILTER ON
$filterOnArr=array("Date of Service","First Claim Date","Last Claim Date");
//columns to be printed in case of detail view
$printableColArr=array("Facility", "Patient Name - ID", "DOB", "Ins. Type", "Ins. ID", "Provider", "CFD", "PD", "DOS", "DOC", "CPT", "ICD10", "Charge", "R", "Aging", "Balance", "1st Claim", "Prt Pt St", "Note", "Reminder Date", "Case Type", "AR Status", "Assign To");
//AGEING CYCLE
$policiesQry = imw_query("Select elem_arCycle from copay_policies where policies_id = '1'");
$polociesDetails = imw_fetch_assoc($policiesQry);
$aggingCycle = $polociesDetails['elem_arCycle'];

$aggingDrop_options_to=$aggingDrop_options_from="";
for($i=0;$i<180;$i++){
	$j = $i == 0 ? '00' : $i + 1;
	$aggingDrop[$j] = $j;
	$sel = ($j == $_POST['aging_from']) ? 'selected' : '';
	$aggingDrop_options_from .= "<option value='" . $j . "' ".$sel.">" . $aggingDrop[$j] . "</option>";
	$i = $i == 0 ? '00' : $i;
	if($i > 0){
		$aggingDrop1[$i] = $i;
		$sel = ($i == $_POST['aging_to']) ? 'selected' : '';
		$aggingDrop_options_to .= "<option value='" . $i . "' ".$sel.">" . $aggingDrop1[$i] . "</option>";

	}
	$i += ($aggingCycle - 1);
}
//GROUP BY
$groupByArr=array("Insurance","Provider","POS Facility");
//LAST STATUS
$lastStatusArr=array("0"=>"Pending","1"=>"Done");
//REJECTION STATUS
$rejStatusArr=array("Pending","Done");
//REJECTION CODE
$qryReasonCode=imw_query("select cas_id,cas_code,cas_desc,cas_action_type from cas_reason_code order by cas_code");
while ($row = imw_fetch_assoc($qryReasonCode)) {
	if($row['cas_action_type']=="Denied"){
		//$rejCodeArr[$row['cas_id']]=$row['cas_code'];
	}
	if($row['cas_action_type']=="Write Off"){
		$cas_code_data[$row['cas_id']]=$row;
	}
	$reasonCodeArr[$row['cas_id']]=$row['cas_code'];
}
//TASK STATUS
$showTaskArr=array("Assigned","Unassigned");
//PRINT STATUS
$printStsArr=array("Yes","No");
//OVER DUE DAYS
$overdueDaysArr=array("Payment Days","Claim Filing Days");
//CPT CODES
$res = imw_query("Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl WHERE cpt_prac_code != '' order by cpt_prac_code asc");
while ($row = imw_fetch_assoc($res)) {
	$color = '';
	$cpt_fee_id = $row['cpt_fee_id'];
	$cptDetailsArr[$cpt_fee_id]['prac_code'] = $row['cpt_prac_code'];
	$cptDetailsArr[$cpt_fee_id]['delete_status'] = $row['delete_status'];
	$cptDetailsArr[$cpt_fee_id]['status'] =$row['status'];
}

//patient acount status
$qry = "Select id,status_name from account_status where del_status=0 order by status_name asc";
$res = imw_query($qry);
while ($row = imw_fetch_assoc($res)) {
	$patientAsArr[$row['id']] = $row['status_name'];
}

if($_POST['filter_on_from'])$filter_on_from=getDateFormatDB($_POST['filter_on_from']);
	if($_POST['filter_on_to'])$filter_on_to=getDateFormatDB($_POST['filter_on_to']);


if($_POST['f_sts_from'])$f_sts_from=getDateFormatDB($_POST['f_sts_from']);
if($_POST['f_sts_to'])$f_sts_to=getDateFormatDB($_POST['f_sts_to']);
if($_POST['l_sts_from'])$l_sts_from=getDateFormatDB($_POST['l_sts_from']);
if($_POST['l_sts_to'])$l_sts_to=getDateFormatDB($_POST['l_sts_to']);

$prv_st_qry="";
if($f_sts_from && $f_sts_to){
	$prv_st_qry.=" AND created_date BETWEEN '".$f_sts_from."' AND '".$f_sts_to."'";
}else if($f_sts_from){
	$prv_st_qry.=" AND created_date >='".$f_sts_from."'";
}else if($f_sts_to){
	$prv_st_qry.=" AND created_date <='".$f_sts_to."'";
}
if($l_sts_from && $l_sts_to){
	$prv_st_qry.=" AND created_date BETWEEN '".$l_sts_from."' AND '".$l_sts_to."'";
}else if($l_sts_from){
	$prv_st_qry.=" AND created_date >='".$l_sts_from."'";
}else if($l_sts_to){
	$prv_st_qry.=" AND created_date <='".$l_sts_to."'";
}

if($prv_st_qry!=""){
	//Previous Statment
	$qry = "Select patient_id,created_date from previous_statement where statement_acc_status=1 order by created_date";
	$res = imw_query($qry);
	while ($row = imw_fetch_assoc($res)) {
		$patientStmArr[$row['patient_id']][] = $row['created_date'];
	}
}

//Setting > Billing > Phrases Typeahead
$phraseArr = array();
$sel_rec_comm=imw_query("select * from int_ext_comment where status='0' order by comment");
while($sel_comm=imw_fetch_assoc($sel_rec_comm)){
	$coment = addslashes($sel_comm['comment']);
 	array_push($phraseArr, $coment);
}
if(count($phraseArr) > 0) $phraseArr = json_encode($phraseArr);

$letterArr = array();
$sel_col_let=imw_query("select id, collection_name from collection_letter_template order by collection_name");
while($sel_col=imw_fetch_assoc($sel_col_let)){
	$letterArr[$sel_col['id']]=$sel_col['collection_name'];
}

$wrt_id_arr=array();
$sel_rec=imw_query("select w_id,w_code,w_default from write_off_code ORDER BY w_code");
while($sel_write=imw_fetch_assoc($sel_rec)){
	$write_off_code_data[$sel_write['w_id']]=$sel_write;
}

$wrt_drop ='<select name="show_write_code[]" id="show_write_code" multiple class="selectpicker" data-width="95%" data-size="20" data-dropup-auto="false" data-live-search="true" data-title="Please Select">
	<optgroup label="Write off Code" data-max-options="1">';
		foreach($write_off_code_data as $d_key=>$d_val){
			$sel_write=$write_off_code_data[$d_key];
			$val_id=$sel_write['w_id'].'_wrt';
			$wrt_drop.="<option value='".$val_id."'>".$sel_write['w_code']."</option>";
		}
	$wrt_drop.='</optgroup>
	<optgroup label="Reason Code">';
		foreach($cas_code_data as $r_key=>$r_val){
			$sel_adj=$cas_code_data[$r_key];
			$val_id=$sel_adj['cas_id'].'_cas';
			$cas_desc=str_replace("'","",$sel_adj['cas_desc']);
			if(strlen($cas_desc)>50){
				$cas_desc=substr($cas_desc,0,50).'...';
			}
			$wrt_drop.="<option value='".$val_id."'>".$sel_adj['cas_code'].' - '.$cas_desc."</option>";
		}
	$wrt_drop.='</optgroup>
</select>';

$dbtemp_name = "AR Worksheet";
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: imwemr ::</title>
		<?php require_once("ar_header.php");?>
        <!-- Bootstrap -->
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
              <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
              <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]--><style>
			 .rptsearch1, .rptsearch2, .rptsearch3{ min-height:105px;}

			@media (min-width: 1400px) and (max-width: 2000px) {
				.rptsearch1 .col-sm-2 {
					width:14%;
				}}
           .pd10.report-content {
                position:relative;
                margin-left:40px;
                background-color: #EAEFF5
            }
            .fltimg {
                position:absolute
            }
            .fltimg span.glyphicon {
                position: absolute;
                top: 170px;
                left: 10px;
                color: #fff
            }
            .reportlft .btn.btn-mkdef {
                padding-top: 6px;
                padding-bottom: 6px
            }
            #content1{
                background-color:#EAEFF5
            }
			.normal
			{
				float: right;
				font-weight: 100 !important;
				text-transform: capitalize !important
			}
			.txt_l{
				text-align: left;
				vertical-align:top;
			}
			.txt_r{
				text-align: right;
				vertical-align:top;
			}
			.txt_c{
				text-align: center;
				vertical-align:top;
			}
			.light_bg{
				background-color:#ebebeb
			}
			.text_b_w {
				font-size: 12px;
				font-weight: bold;
				color: #000;
				background-color: #c7c7c7;
			}
		.yellow_bg{
			background-color:#FFFFCC;
		}
		.notification_span
		{
			box-shadow: 10px 5px 5px #c7c7c7;
		}
		/* Info Image CSS */
		.rptInfoImg{
			background-image: url("../../library/images/infobutton.png");
			width:21px;
			height:21px;
			background-repeat: no-repeat;
			cursor: pointer;
			title:'Report Logic';
		}


		.rptInfoImg:hover {
			-khtml-opacity:.80;
			-moz-opacity:.80;
			-ms-filter:"alpha(opacity=80)";
			filter:alpha(opacity=80);
			filter: progid:DXImageTransform.Microsoft.Alpha(opacity=0.8);
			opacity:.80;
		}
		.infoBox{ border:2px solid #FCA635;  background-color:#F2F2F2; font-family:Verdana, Arial, Helvetica, sans-serif;
		 -moz-border-radius: 8px;
		 -webkit-border-radius: 8px;
		 border-radius: 8px;
		}
		.infoTitle{ size:16px; color:#000;  font-weight:bold; padding:2px 0px 2px 5px; background-color:#E1E1E1;
		 -moz-border-radius: 8px 8px 0px 0px;
		 -webkit-border-radius: 8px 8px 0px 0px;
		 border-radius: 8px 8px 0px 0px;
		}
		.infoTitleLine { border-bottom:1px solid #FCA635;}
		.infoInnerDiv { padding:7px; size:12px; color:#000; line-height:18px}
		.infoSubTitle { size:12px; color:#000; font-weight:bold; margin-right:10px; float:left; }
		.infoDataLine { width:100%; border-bottom:1px solid #CCC; margin-top:3px; margin-bottom:3px;}
		#closeRptInfo{cursor: pointer;}

		#ar_hdr_fxd {
		    position: absolute;
		    display:none;
		    background-color:white;
				z-index:2;
		}

		#html_data_div{position: relative;}

		.rpt_table_det{  font-size:12px;
			font-family:Arial, Helvetica, sans-serif; }
		.rpt_table_det td, th{ padding-left:3px; padding-right:3px; }

		</style>
	<?php
		$style="<style>
		.text_10 {
				font-size: 11px;
				font-family: Arial, Helvetica, sans-serif;
				vertical-align:top;
			}
		.text_b_w {
				font-size: 11px;
				color: #000;
				background-color: #c7c7c7;
				font-family: Arial, Helvetica, sans-serif
			}
		.txt_l{
				text-align: left
			}
			.txt_r{
				text-align: right
			}
			.txt_c{
				text-align: center
			}
			.light_bg{
				background-color:#ebebeb
			}
		</style>";
	?>
    </head>
    <body>
		<div id="div_loading_image" class="text-center" style="display: block;position: fixed;top: 0px;left: 0px;height: 100%;width: 100%;background-color: rgba(0,0,0,0.1);"><div class="loading_container" style="width: auto"><div class="process_loader"></div><div id="div_loading_text" class="text-info">Loading Data....</div></div></div>

		<div class="usersection Reports_header" id="Reports_header" style="min-height: 43px">
			<div class="col-sm-5">
			<ul style="margin-left: -5px;">
			  <li class="elacc demograph title_name">
				<ul>
				  <li><span class="actpagname" id="acc_page_name">AR Worksheet</span></li>
				</ul>
			  </li>
			</ul>
		  </div><div id="div_alert_notifications"><span class="notification_span"></span></div>
		</div>
        <form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">
        	<input type="hidden" name="session_user" id="session_user_id" value="<?php echo $session_user_id; ?>">
            <input name="processReport" id="processReport" autocomplete="off" value="Daily" type="hidden">
			<input type="hidden" name="Submit" id="Submit" value="get reports">
            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="action_type" id="action_type" value="">
            <input type="hidden" name="force_cond" id="force_cond" value="yes">
            <input type="hidden" name="print_pdf" id="print_pdf" value="print">
            <input type="hidden" name="ord_by_field" id="ord_by_field" value="<?php echo $_POST['ord_by_field']; ?>">
			<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="<?php echo $_POST['ord_by_ascdesc']; ?>">
            <div class=" container-fluid">
                <div class="anatreport">
                    <div id="select_drop" style="position:absolute;bottom:0px;"></div>
              	<div class="row" id="row-main">
                	<div class="col-md-3 collapsed" id="sidebar">
                  	<div class="reportlft">
                    	<div class="practbox">
                      	<div class="anatreport" style="border-bottom: 2px solid #3f51b5;"><h2 class="this_rpt_info" style="float: left; border-bottom: none">Practice Filter</h2>

							<!--<div class="checkbox checkbox-inline pointer normal">
                              	<input type="checkbox" name="include_copay" id="include_copay" value="include_copay" <?php if ($_POST['include_copay']) echo 'CHECKED'; ?>  />
                                <label for="include_copay">Include Copay</label>
                            	</div>-->
						<div style="clear: both"></div>
						</div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
                           <div class="col-sm-4">
                                <label>POS Facility</label>
                                <select name="facility_id[]" id="facility_id" data-container="#select_drop" class="selectpicker"  data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All">
                                    <?php 
									foreach($drop_pos_fac_arr as $id=>$name)
									{
										$sel=($facility_id_sel[$id])?'selected':'';
										echo'<option value="'.$id.'" '.$sel.'>' .$name. '</option>';
									}
									?>
                                </select>
                            </div>


                            <div class="col-sm-4">
                              <label>Provider</label>
                              <select name="filing_provider[]" id="filing_provider" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All">
                                  <?php 
								foreach($drop_user_arr as $id=>$val)
								{
									$subArr=$usersArr[$id];
									if($subArr['user_type']==1 || $subArr['user_type']==7 || $subArr['user_type']==12)
									{
										$color = '';
										if ($subArr['delete_status'] == 1 )
										$color = 'color:#CC0000!important';

										$sel = '';
										if (sizeof($filing_provider_sel) > 0) {
											if ($filing_provider_sel[$id]) {
												$sel = 'selected';
											}
										}
										echo "<option value='$id' style='$color' $sel>" . $subArr['name']. "</option>";
									}
								}
								  ?>
                              </select>
                            </div>

							<div class="col-sm-4">
                            	<label for="insuranceGrp">Insurance Group</label>
                              <select name="insuranceGrp[]" id="insuranceGrp" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All">
                             	<?php
								foreach($insGroupArr as $grp_ins_ids=>$groupName)
								{
									$selected = ($insuranceGrp_sel[$grp_ins_ids])?'selected':'';
									echo "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $groupName . "</option>";
								}
								  ?>
                            	</select>
                          	</div>
                            <div class="col-sm-4" >
                            	<label for="ins_carriers">Insurance Company</label>
                              <select name="ins_carriers[]" id="ins_carriers" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All" >
                              <?php 
								  foreach($drop_ins_comp_arr as $ins_id=>$ins_val){
									$sel=$del_col='';
									if (sizeof($ins_carriers_sel) > 0) {
										if ($ins_carriers_sel[$ins_id]) {
											$sel = 'selected';
										}
									}
									if ($insCompArr[$ins_id]['ins_del_status'] > 0) $del_col=" style='color:red'";
									echo "<option value='" . $ins_id . "' ".$sel.$del_col.">" . $ins_val . "</option>";
								  }
							?>
                              </select>
                           	</div>
							<div class="col-sm-8">
								<div class="row">
								<div class="col-sm-12">
								<div class="">
									<!-- Pt. Search -->
									<div class="col-sm-12"><label>Patient</label></div>
									<div class="col-sm-5">
										<input type="hidden" name="patientId" id="patientId" value="<?php echo ($_POST['txt_patient_name'] && $_POST['patientId'])?$_POST['patientId']:'';?>">
										<input class="form-control" type="text" id="txt_patient_name" value="<?php echo $_POST['txt_patient_name'];?>" name="txt_patient_name" onkeypress="{if (event.keyCode==13)return searchPatient()}" >
									</div>
									<div class="col-sm-5">
										<select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control minimal">
											<option value="Active">Active</option>
											<option value="Inactive">Inactive</option>
											<option value="Deceased">Deceased</option>
											<option value="Resp.LN">Resp.LN</option>
											<option value="Ins.Policy">Ins.Policy</option>
										</select>
									</div>
									<div class="col-sm-2 text-center">
										<button class="btn btn-success" type="button" onclick="searchPatient();"><span class="glyphicon glyphicon-search"></span></button>
									</div>
								</div>
							</div>
								</div>
							</div>
							<div class="col-sm-4">
								<label for="aging_by">Aging By</label>
							  <div class="input-group">
								<select name="aging_by" id="aging_by" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>">
                             <?php
								  foreach($filterOnArr as $name)
								  {
									  $sel=($_POST['aging_by']==$name)?'selected':'';
									  echo "<option value='$name' $sel>$name</option>";
								  }
							  ?>
                              </select>
							  </div>
							</div>
							<div class="col-sm-4">
								  <label for="as_of">As of</label>
								  <div class="input-group">
									<input type="text" name="as_of" placeholder="Date" style="font-size: 12px;" id="as_of" value="<?php echo $_POST['as_of']; ?>" class="form-control date-pick">
									<label class="input-group-addon" for="as_of"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								  </div>
							</div>

							<div class="col-sm-4">
                              <label>Status</label>
                              <select name="status[]" id="status" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All">
                              <?php
								  foreach($statusArr as $key=>$name)
								  {
									  $sel = '';
										if (sizeof($status_sel) > 0) {
											if ($status_sel[$key])
												$sel = 'SELECTED';
										}
									  echo "<option value='$key' $sel>$name</option>";
								  }
							  ?>
                              </select>
                            </div>
							<div class="col-sm-4" >
                            	<label for="ins_types">Ins. Types</label>
                              <select name="ins_types[]" id="ins_types" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All">
                               <?php
									foreach ($insTypeArr as $key => $val) {
										$sel = '';
										if (sizeof($ins_types_sel) > 0) {
											if ($ins_types_sel[$key])
												$sel = 'SELECTED';
										}
										echo '<option value="' . $key . '" ' . $sel . '>' . $val . '</option>';
									}
								  ?>
                              </select>
                           	</div>
							<div class="col-sm-4" >
                            	<label for="ins_priority">Ins. Priority</label>
                              <select name="ins_priority[]" id="ins_priority" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All">
                               <?php
									foreach ($insPriArr as $key => $val) {
										$sel = '';
										if (sizeof($ins_pri_sel) > 0) {
											if ($ins_pri_sel[$key])
												$sel = 'SELECTED';
										}
										echo '<option value="' . $key . '" ' . $sel . '>' . $val . '</option>';
									}
								  ?>
                              </select>
                           	</div>
                            <div class="col-sm-4">
                            	<label>Appt. Reason</label>
                              <select name="appt_reason[]" id="appt_reason" data-container="#select_drop" class="selectpicker"  data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All">
                              <?php
								  foreach ($apptReasonArr as $key => $val) {
										$sel = '';
										if (sizeof($reason_sel) > 0) {
											if ($reason_sel[$key])
												$sel = 'SELECTED';
										}
										echo '<option value="' . $key . '" ' . $sel . '>' . $val . '</option>';
									}
								  ?>
                              </select>
                          	</div>
							<div class="col-sm-4">
                            	<label>Filter On</label>
                              <select name="filter_on" id="filter_on" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>">
                             <?php
								  foreach($filterOnArr as $name)
								  {
									  $sel=($_POST['filter_on']==$name)?'selected':'';
									  echo "<option value='$name' $sel>$name</option>";
								  }
							  ?>
                              </select>
                          	</div>
							<div class="col-sm-4">
								  <label for="filter_on_from">From</label>
								  <div class="input-group">
									<input type="text" name="filter_on_from" placeholder="Date" style="font-size: 12px;" id="filter_on_from" value="<?php echo $_POST['filter_on_from']; ?>" class="form-control date-pick">
									<label class="input-group-addon" for="filter_on_from"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								  </div>
							</div>
							<div class="col-sm-4">
								  <label for="filter_on_to">To</label>
								  <div class="input-group">
									<input type="text" name="filter_on_to" placeholder="Date" style="font-size: 12px;" id="filter_on_to" value="<?php echo $_POST['filter_on_to']; ?>" class="form-control date-pick">
									<label class="input-group-addon" for="filter_on_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								  </div>
							</div>
							 <!--<div class="col-sm-6 mt2 hide"><br>

                            	<div class="radio radio-inline pointer">
                              	<input type="radio" class="summary_detail" name="summary_detail" id="summary" value="summary" <?php if ($_POST['summary_detail'] == 'summary' ||  empty($_POST['summary_detail '])) echo 'CHECKED'; ?>  />
                                <label for="summary">Summary</label>
                            	</div>
                              <div class="radio radio-inline pointer">
                              	<input type="radio" class="summary_detail" name="summary_detail" id="detail" value="detail" <?php if ($_POST['summary_detail'] == 'detail') echo 'CHECKED'; ?> />
                                <label for="detail">Detail</label>
                            	</div>
                          	</div>-->
							<div class="col-sm-6">
								<label for="printable_columns">Print columns in detail view PDF</label>
								 <select name="printable_columns[]" id="printable_columns" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All">
									<?php
									  foreach($printableColArr as $name)
									  {
										  	$sel = '';
											if (sizeof($printable_columns_sel) > 0) {
												if ($printable_columns_sel[$name])
													$sel = 'SELECTED';
											}
										  	echo "<option value='$name' $sel>$name</option>";
									  }
								  ?>
								</select>
							</div>

                        </div>
                       	</div>
                    	</div>

                      <div class="appointflt">
                      	<div class="anatreport"><h2>Analytic Filter</h2></div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria2">
                        	<div class="row">
                          	<div class="col-sm-6">
								<label for="aging_from">Aging From</label>
								<select name="aging_from" id="aging_from" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" data-container="#select_drop"> <?php //echo $please_select?>
									<?php
									//print "to" options
									echo $aggingDrop_options_from;
									?>
									<option value="181" <?php if($_POST['aging_from'] == '181') echo 'SELECTED'?> >181</option>
								</select>
							</div>
							<div class="col-sm-6" >
								<label for="aging_to">Aging To</label>
								<select name="aging_to" id="aging_to" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" data-container="#select_drop"> <?php echo $please_select?>
									<?php echo $aggingDrop_options_to; ?>
									<option value="180" <?php if($_POST['aging_to'] == '180') echo 'SELECTED'?> >180</option>
									<option value="181" <?php if(!$_POST['aging_to'] || $_POST['aging_to'] == '181') echo 'SELECTED'?> >181+</option>
								</select>
							</div>
                            <!--<div class="col-sm-4" >
                             	<div class="checkbox pointer"><br/>
                              	<input type="checkbox" name="hide_30" id="hide_30" value="1" <?php echo ($_POST['hide_30'] == '1')?'checked':''; ?>/>
                              	<label for="hide_30">Hide 30</label>
                            	</div>
                          	</div>-->

                            <div class="col-sm-4" >
                            	<label for="cpt_cat">Balance From</label>
                             	<input class="form-control" type="text" id="balance_from" value="<?php echo $_POST['balance_from'];?>" name="balance_from" >
                           	</div>
                            <div class="col-sm-4" >
                            	<label for="cpt_cat">Balance To</label>
                             	<input class="form-control" type="text" id="balance_to" value="<?php echo $_POST['balance_to'];?>" name="balance_to" >
                           	</div>
                            <div class="col-sm-4" >
                            	<label for="cpt">Group By</label>
                              <select name="group_by" id="group_by" class="selectpicker pull-right" data-width="100%" data-size="<?php echo $drop_option_limit;?>">
                              <?php
								  foreach($groupByArr as $name)
								  {
									  $sel=($_POST['group_by']==$name)?'selected':'';
									  echo "<option value='$name' $sel>$name</option>";
								  }
							  ?>
                              </select>
                           	</div>
                        	</div>
                       	</div>
                    	</div>


                      <div class="grpara">
                      	<div class="anatreport" style="border-bottom: 2px solid #3f51b5;"><h2 style="float: left; border-bottom: none">Additional Filters</h2>
							<div class="checkbox checkbox-inline pointer normal">
                              	<input type="checkbox" name="More" id="More" value="More" <?php if ($_POST['More']) echo 'CHECKED'; ?> data-toggle="collapse" data-target="#searchcriteria3" aria-expanded="<?php echo ($_POST['More']) ? 'true':'false'; ?>" aria-controls="searchcriteria3"  />
                                <label for="More">More</label>
                            	</div>
						  <div style="clear: both"></div>
						  </div>
                        <div class="clearfix"></div>
                        <div class="pd5 collapse <?php if($_POST['More'])echo 'in'; ?>" id="searchcriteria3">
                        	<div class="row">
							<div class="col-sm-4">
								  <label for="follow_up_from">Follow Up From</label>
								  <div class="input-group">
									<input type="text" name="follow_up_from" placeholder="Date" style="font-size: 12px;" id="follow_up_from" value="<?php echo $_POST['follow_up_from']; ?>" class="form-control date-pick">
									<label class="input-group-addon" for="follow_up_from"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								  </div>
							</div>
							<div class="col-sm-4">
								  <label for="follow_up_to">Follow Up To</label>
								  <div class="input-group">
									<input type="text" name="follow_up_to" placeholder="Date" style="font-size: 12px;" id="follow_up_to" value="<?php echo $_POST['follow_up_to']; ?>" class="form-control date-pick">
									<label class="input-group-addon" for="follow_up_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								  </div>
							</div>
							<div class="col-sm-4">
							  <label>Follow Up By User</label>
							  <select name="follow_up_opr_id[]" id="follow_up_opr_id"  class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All" data-container="#select_drop">
								<?php 
								foreach($drop_user_arr as $id=>$val)
								{
									$subArr=$usersArr[$id];
									$color = '';
									if ($subArr['delete_status'] == 1 )
									$color = 'color:#CC0000!important';

									$sel = '';
									if (sizeof($operator_id_sel) > 0) {
										if ($operator_id_sel[$id]) {
											$sel = 'selected';
										}
									}
									echo "<option value='$id' style='$color' $sel>" . $subArr['name']. "</option>";
								}
								  ?>
							  </select>
							</div>
							<div class="col-sm-4">
							  <label>Last Status</label>
							  <select name="last_status" id="last_status" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>"> <?php echo $please_select?>
								<?php
								  foreach($lastStatusArr as $key => $name)
								  {
									  $sel=($_POST['last_status']==$name)?'selected':'';
									  echo "<option value='$name' $sel>$name</option>";
								  }
							  ?>
							  </select>
							</div>
							<div class="col-sm-4">
							  <label>Rejection Status</label>
							  <select name="rej_status" id="rej_status" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>"> <?php echo $please_select?>
								<?php
								  foreach($rejStatusArr as $name)
								  {
									  $sel=($_POST['rej_status']==$name)?'selected':'';
									  echo "<option value='$name' $sel>$name</option>";
								  }
							  ?>
							  </select>
							</div>
							<div class="col-sm-4">
							  <label>Rejection Code</label>
							  <select name="rej_code[]" id="rej_code" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All" data-container="#select_drop" data-live-search="true">
								<?php
								  foreach($reasonCodeArr as $name)
								  {
									  $sel = '';
									if (sizeof($rej_code_sel) > 0) {
										if ($rej_code_sel[$name]) {
											$sel = 'selected';
										}
									}
									  echo "<option value='$name' $sel>$name</option>";
								  }
							  ?>
							  </select>
							</div>
							<div class="col-sm-4">
							  <label>Show Task</label>
							  <select name="show_task" id="show_task" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>"> <?php echo $please_select?>
								<?php
								  foreach($showTaskArr as $name)
								  {
									  $sel=($name==$_POST['show_task'])?'selected':'';
									  echo "<option value='$name' $sel>$name</option>";
								  }
							  ?>
							  </select>
							</div>
							<div class="col-sm-4">
							  <label for="what_user">What User(s)?</label>
							  <select name="what_user[]" id="what_user" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All" data-container="#select_drop">
								<?php
								foreach($drop_user_arr as $id=>$val)
								{
									$subArr=$usersArr[$id];
									$color = '';
									if ($subArr['delete_status'] == 1 )
									$color = 'color:#CC0000!important';

									$sel = '';
									if (sizeof($what_user_sel) > 0) {
										if ($what_user_sel[$id]) {
											$sel = 'selected';
										}
									}
									echo "<option value='$id' style='$color' $sel>" . $subArr['name']. "</option>";
								}
								 ?>
							  </select>
							</div>
							<div class="col-sm-4">
                              <label>Print Statement Status</label>
							  <select name="printStsStatus" id="printStsStatus" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" data-container="#select_drop" ><?php echo $please_select?>
								<?php
								 foreach($printStsArr as $id=>$subArr)
								{
									$sel = '';
									if ($_POST['printStsStatus']==$subArr) {
										$sel = 'selected';
									}
									echo "<option value='$subArr' $sel>" . $subArr. "</option>";
								}
								 ?>
							  </select>
							</div>
							 <div class="col-sm-6">
								  <label for="f_sts_from">First Statement From</label>
								  <div class="input-group">
									<input type="text" name="f_sts_from" placeholder="Date" style="font-size: 12px;" id="f_sts_from" value="<?php echo $_POST['f_sts_from']; ?>" class="form-control date-pick">
									<label class="input-group-addon" for="f_sts_from"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								  </div>
							</div>
							<div class="col-sm-6">
								  <label for="f_sts_to">First Statement To</label>
								  <div class="input-group">
									<input type="text" name="f_sts_to" placeholder="Date" style="font-size: 12px;" id="f_sts_to" value="<?php echo $_POST['f_sts_to']; ?>" class="form-control date-pick">
									<label class="input-group-addon" for="f_sts_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								  </div>
							</div>
							 <div class="col-sm-6">
								  <label for="l_sts_from">Last Statement From</label>
								  <div class="input-group">
									<input type="text" name="l_sts_from" placeholder="Date" style="font-size: 12px;" id="l_sts_from" value="<?php echo $_POST['l_sts_from']; ?>" class="form-control date-pick">
									<label class="input-group-addon" for="l_sts_from"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								  </div>
							</div>
							<div class="col-sm-6">
								  <label for="l_sts_to">Last Statement To</label>
								  <div class="input-group">
									<input type="text" name="l_sts_to" placeholder="Date" style="font-size: 12px;" id="l_sts_to" value="<?php echo $_POST['l_sts_to']; ?>" class="form-control date-pick">
									<label class="input-group-addon" for="l_sts_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								  </div>
							</div>
							<div class="col-sm-4">
							  <label>Overdue Days</label>
							  <select name="overdue_days" id="overdue_days" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>"> <?php echo $please_select?>
								<?php
								  foreach($overdueDaysArr as $name)
								  {
									  $sel=($_POST['overdue_days']==$name)?'selected':'';
									  echo "<option value='$name' $sel>$name</option>";
								  }
							  ?>
							  </select>
							</div>
							 <div class="col-sm-4">
								  <label for="overdue_days_from">From</label>
								  <div class="input-group" style="width: 100%">
									<input type="text" name="overdue_days_from" placeholder="5" style="font-size: 12px;" id="overdue_days_from" value="<?php echo $_POST['overdue_days_from']; ?>" class="form-control">
								  </div>
							</div>
							<div class="col-sm-4">
								  <label for="overdue_days_to">To</label>
								  <div class="input-group" style="width: 100%">
									<input type="text" name="overdue_days_to" placeholder="10" style="font-size: 12px;" id="overdue_days_to" value="<?php echo $_POST['overdue_days_to']; ?>" class="form-control">
								  </div>
							</div>
							<div class="col-sm-8" >
                              <label for="cpt_code">Proc/CPT Code</label>
                              <select name="cpt_code[]" id="cpt_code" class="selectpicker" data-width="100%" data-size="<?php echo $drop_option_limit;?>" multiple data-actions-box="true" data-title="Select All">
                               <?php
								 foreach($cptDetailsArr as $cpt_fee_id=>$subArr){
									$color = '';
									$cpt_prac_code = (strlen($subArr['prac_code'])>38)? substr($subArr['prac_code'],0,38).'...': $subArr['prac_code'];
									if ($subArr['delete_status'] == 1 || $subArr['status'] == 'Inactive')
									$color = 'color:#CC0000!important';

									$sel = '';
									if (sizeof($cpt_code_id) > 0) {
										if ($cpt_code_id[$cpt_fee_id]) {
											$sel = 'selected';
										}
									}
									echo "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
								 }
								?>
                              </select>
                           	</div>
							<div class="col-sm-4" >
                            	<label for="cpt">Patient AS</label>
                              <select name="patient_as" id="patient_as" class="selectpicker dropup" data-width="100%" data-size="<?php echo $drop_option_limit;?>"  data-container="#select_drop"><?php echo $please_select?>
								<?php
								 foreach($patientAsArr as $id=>$name){
									$sel =($patient_as==$id)?'selected': '';
									echo "<option value='$id' $sel>$name</option>";
								 }
								?>
                              </select>
                           	</div>

							<div class="col-sm-6" >
                            	<label for="statement_count_from">Statement Count From</label>
								  <div class="input-group" style="width: 100%">
									<input type="text" name="statement_count_from" placeholder="5" id="statement_count_from" value="<?php echo $_POST['statement_count_from']; ?>" class="form-control">
								  </div>
                           	</div>
							<div class="col-sm-6">
                            	<label for="statement_count_to">Statement Count To</label>
								  <div class="input-group" style="width: 100%">
									<input type="text" name="statement_count_to" placeholder="10" id="statement_count_to" value="<?php echo $_POST['statement_count_to']; ?>" class="form-control">
								  </div>
                           	</div>

                          </div>
                      	</div>
                   		</div>

						<div class="clearfix">&nbsp;</div>
                 		</div>

						<div id="module_buttons" class="ad_modal_footer text-center">
                    	<button class="savesrch" type="button" onClick="get_ar('')">Search</button>
                  		</div>

						</div>

					<div class="col-md-12" id="content1">
						<div class="btn-group fltimg pointer" role="group" aria-label="Controls">
							<img class="toggle-sidebar" src="../../library/images/ar_filter.png" alt=""/><span class="toggle-sidebar glyphicon glyphicon-chevron-right"></span>
						</div>

						<div class="pd10 report-content">
							<div class="rptbox">
								<div id="html_data_div" class="row">
									<!-- Header -->
									<table id="ar_hdr_fxd"></table>
									<?php
										include('ar_result.php');
								 	?>
								</div>
							</div>
						</div>
					</div>
					<br clear="both">
					<footer>
						<div class="row">
							<div class="col-sm-1"></div>
							<div class="col-sm-10 text-center" id="page_buttons"></div>
							<div class="col-sm-1"></div>
						</div>
					</footer>

                </div>
            	</div>
            </div>
			<input type="hidden" name="detail_ins_id" id="detail_ins_id" value="<?php echo $_POST['detail_ins_id'];?>">
			<input type="hidden" name="detail_ins_name" id="detail_ins_name" value="<?php echo $_POST['detail_ins_name'];?>">
            <input type="hidden" name="detail_pat_id" id="detail_pat_id" value="<?php echo $_POST['detail_pat_id'];?>">
            <input type="hidden" name="detail_ins_email" id="detail_ins_email" value="<?php echo $_POST['detail_ins_email'];?>">
            <input type="hidden" name="detail_ins_fax" id="detail_ins_fax" value="<?php echo $_POST['detail_ins_fax'];?>">
            <input type="hidden" name="detail_group_by" id="detail_group_by" value="<?php echo $_POST['group_by'];?>">
            <input type="hidden" name="print_paper_type" id="print_paper_type" value="PrintCms">
            <input type="hidden" name="old_action_type" id="old_action_type" value="">

        </form>
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" >
	<input type="hidden" name="csv_text" id="csv_text">
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form>
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" target="iframeD" method ="post" >
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
</form>
<iframe allowtransparency="1" frameborder="0" height="0" hspace="0" width="0" id="iframeD" name="iframeD" style="display: none">this is testing</iframe>
<input type="hidden" name="pat_encounter_id" id="pat_encounter_id" value="<?php echo $str_encounters;?>">

<div id="div_rpt_info" class="infoBox" style="width:250px; height:auto; z-index:9999; position:absolute; display:none">
	<div class="infoTitle" style="height: 22px"><!-- ui-draggable-handle--><!--; cursor: move;-->
		<div id="infoTitleText" style="float:left">popup title will be here</div>
		<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onclick="javascript:hideReportInfo();"></div>
	</div>
	<div class="infoTitleLine"></div>
	<div class="infoInnerDiv">

	</div>
</div>
	<!--modal wrapper class is being used to control modal design-->
	<div class="common_modal_wrapper">
	 <!-- Modal -->
        <div id="write_off_modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Please select Write-off reason</h4>
                    </div>
                    <div class="modal-body">
						<?php echo $wrt_drop;?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="save_write_off" onClick="ar_action('write_off_update','')">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
	</div>
	<!--modal wrapper class end here -->

	<!--modal wrapper class is being used to control modal design-->
	<div class="common_modal_wrapper">
	 <!-- Modal -->
        <div id="claim_status_modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Select an claim status to update</h4>
                    </div>
                    <div class="modal-body">

                        <select name="status_confirm" id="status_confirm" class="form-control minimal">
                        <option value="">Please Select</option>
						<?php foreach($statusArr as $key=>$name)echo "<option value=\"$key\">$name</option>";?></select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="save_claim_status" onClick="ar_action('status_update','')">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
	</div>
	<!--modal wrapper class end here -->

<!--modal wrapper class is being used to control modal design-->
	<div class="common_modal_wrapper">
	 <!-- Modal -->
	<div id="task_followup" class="modal fade" role="dialog">
		<div class="modal-dialog modal-md">
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Add New Task</h4>
				</div>
				<div class="modal-body">
						<div class="row">

							<div class="col-sm-6" id="selectAssignForDiv">
								<label for="selectAssignFor" id="selectAssignHead">Assign selected encounter(s) to</label>
                                <label for="selectAssignFor" id="selectFollowHead">Create follow up Notes/Task for : </label>
								<select class="selectpicker" id="selectAssignFor" name="assignFor[]" data-width="100%" data-actions-box="true" data-live-search="true" data-title="Notes" data-size="5" multiple data-selected-text-format="count > 1">
									<?php 
										foreach($drop_user_arr as $id=>$val)
										{
											$subArr=$usersArr[$id];
											if($subArr['delete_status']==0)
											{
												$sel = '';
												if ($_SESSION['authId']) {
													if ($_SESSION['authId']==$id) {
														$sel = 'selected';
													}
												}
												echo "<option value='$id' $sel>" . $subArr['name']. "</option>";
											}
										}
									?>
								</select>
							</div>
							<div class="col-sm-6" id="selectAssignReminderDate">
								<label for="reminder_date">Reminder Date : </label>
								<div class="input-group">
									<input type="text" name="reminder_date" id="reminder_date" value="<?php echo date('m-d-Y'); ?>" class="form-control date-pick">
									<label class="input-group-addon pointer" for="reminder_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
								</div>
							</div>
							<div class="col-sm-4" id="selectTaskOnReminderDate">
								<label for="notes">Task On Reminder Date</label>
								<div class="checkbox">
									<input type="checkbox" name="task_on_reminder" id="task_on_reminder" value="yes" checked="checked" onClick="task_reminder_date();"/>
									<label for="task_on_reminder">&nbsp;</label>
								</div>
							</div>
							<div class="col-sm-8" id="selectAssignNotes">
								<label for="notes">Note : </label>
								<textarea cols="75" rows="2" name="notes" id="notes" class="form-control"></textarea>
							</div>
						</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" id="task_or_followup" name="task_or_followup" value="">
					<button type="button" class="btn btn-success" id="task_update" onClick="task_action()">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!--modal wrapper class end here -->
<!--modal wrapper class is being used to control modal design-->
	<div class="common_modal_wrapper">
	 <!-- Modal -->
	<div id="letter_modal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-md">
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Send Letter</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12" >
							<label for="selectAssignFor">Letter Template: </label>
							<select class="selectpicker" id="letter_template" name="letter_template" data-width="100%" data-live-search="true" data-title="Please Select" data-size="5">
								<?php
									foreach($letterArr as $id=>$letter)
									{
										echo "<option value='$id'>" . $letter. "</option>";
									}
								?>
							</select>
						</div>
						<div class="col-sm-6">
							<span style="line-height: 30px;">Type :</span><br>
							<div class="radio radio-inline">
								<input type="radio" class="letter_type" name="letter_type" id="letter_type_email" value="email" checked="checked" autocomplete="off">
								<label for="letter_type_email">Email</label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" class="letter_type" name="letter_type" id="letter_type_fax" value="fax"  autocomplete="off">
								<label for="letter_type_fax">Fax</label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" class="letter_type" name="letter_type" id="letter_type_print" value="print"  autocomplete="off">
								<label for="letter_type_print">Print</label>
							</div>
						</div>
						<div class="col-sm-6">
							<span style="line-height: 30px;">To :</span><br>
							<div class="radio radio-inline">
								<input type="radio" name="letter_to" id="letter_to_insurance" value="insurance" checked="checked" autocomplete="off">
								<label for="letter_to_insurance">Insurance</label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" name="letter_to" id="letter_to_patient" value="patient" autocomplete="off">
								<label for="letter_to_patient">Patient</label>
							</div>
						</div>
						<div class="col-sm-12" style="color: #F3090D">
							<br>
							<span id="error_ins_fax">Error! Fax number for "<?php echo $_POST['detail_ins_name'];?>" is not available.</span>
							<span id="error_ins_email">Error! Email ID for "<?php echo $_POST['detail_ins_name'];?>" is not available.</span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="task_update" onClick="ar_action('letter_update','')">Send</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	</div>
	<!--modal wrapper class end here -->


<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	var op='<?php echo $op;?>';
	var output='<?php echo $_POST['output_option'];?>';
	var processReport='<?php echo $processReport; ?>';
	var arrSearchName=[];
	var arrSearchName=<?php echo json_encode($arrSearchName); ?>;

	$(document).ready(function () {
		set_ar_header();
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
	});

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var HTMLCreated=<?php echo ($HTMLCreated)?1:0;?>;


	//BUTTONS
	var mainBtnArr = new Array();
	<?php if($html_body!=""){?>
		if (printFile == '1') {
			var mainBtnArr = [
					   <?php if($_POST['detail_ins_id']>0 || $_POST['detail_ins_id']!=""){?>
					  		["summary","Summary","get_ar('');"],
							<?php if($_POST['detail_pat_id']>0){?>
							["pt_view","Pt View","get_ar('pt_view');"],
							<?php } ?>
					   <?php } ?>
					   ["print","Print PDF","generate_pdf('"+op+"');"]
					  ,["start_process","Export CSV","download_csv();"]
					  <?php if($_POST['detail_ins_id']>0 || $_POST['detail_ins_id']!=""){?>
					  ,["write_off","Write Off","ar_action('write_off_update_alert','');"]
						  <?php if($_POST['detail_pat_id']>0 || $_POST['detail_pat_id']!=""){?>
						  ,["rebill","Re-Submit","ar_action('rebill_update_alert','');"]
						  ,["claim","Claim","ar_action('claim_update_alert','');"]
						  ,["claim_status","Status","ar_action('status_update_alert','');"]
						  <?php } ?>
					   <?php if($_POST['detail_ins_id']<=0){?>
					  ,["statement","Statement","ar_action('statement_update_alert','');"]
					  <?php } ?>
					  	<?php if($_POST['detail_pat_id']>0 || $_POST['detail_pat_id']!=""){?>
						  ,["assign_to","Assign To","ar_action('assign_to_alert','');"]
						  ,["followup","Follow Up","ar_action('followup_alert','');"]
						<?php } ?>  
					  ,["letter","Letter","ar_action('letter_alert','');"]
					  <?php } ?>
					  ];
		}
		btn_show("AR_worksheet", mainBtnArr);
	<?php } ?>
	//-------
/* set AR table header */

	function set_ar_header(){
		if($("#ar_summ_tbl").outerWidth()!=null){
			var tbl_width = $("#ar_summ_tbl").outerWidth();
			var tableOffset = $("#ar_summ_tbl").position().top;
			var header = $("#ar_summ_tbl > thead").clone(true);
			$(header).find("#chkbx_all").prop("id", "chkbx_all1").prop("name", "chkbx_all1");
			$(header).find("label[for=chkbx_all]").prop("for", "chkbx_all1");			
			
			var fixedHeader = $("#ar_hdr_fxd").append(header);
			$("#ar_hdr_fxd").attr("class", $("#ar_summ_tbl").attr("class"));
			//$("#ar_summ_tbl").css({'width':parseInt(tbl_width-20)+"px"});
			//
			if($("#ar_hdr_fxd tr").length==2){
				//$("#ar_hdr_fxd .checkbox").remove();
				$("#ar_hdr_fxd, #ar_summ_tbl").css({'width':tbl_width+"px"});
			}else{
				$("#ar_summ_tbl").css({'width':parseInt(tbl_width-20)+"px"});
			}
	
				var tr_in = $("#ar_hdr_fxd tr").length-1;
				//console.log($("#ar_summ_tbl tr").length);
				var td = $("#ar_summ_tbl tr").eq(tr_in).find("td");
				var th = $("#ar_hdr_fxd tr").eq(tr_in).find("td");
				td.each(function(indx){
					//console.log(this.innerText, $(this).outerWidth(), th.eq(indx).html());
						var tw = $(this).outerWidth();
						th.eq(indx).css({"width":tw+"px"});
				});
	
				//#content1 .report-content
		}
		
		$("#content1 #html_data_div").bind("scroll", function() {
			var offset = $(this).scrollTop();
			if (offset >= tableOffset ) {
				fixedHeader.css({'top':offset}).show();//
			}
			else if (offset < tableOffset) {
				fixedHeader.hide();
			}
		});

	}



	function show_loading_image(state){
		if(state=='show')$("#div_loading_image").show();
		else $("#div_loading_image").hide();
	}
	function chk_all(){				
		if($('#chkbx_all').is(':checked')||$('#chkbx_all1').is(':checked')){
			$(".chk_box_css").prop("checked",true);
		}else{
			$(".chk_box_css").prop("checked",false);
		}
	}

	function ar_ord_by(ord_by){
		ord_by_field = $('#ord_by_field').val();
		ord_by_ascdesc = $('#ord_by_ascdesc').val();
		$('#ord_by_field').val(ord_by);
		if(ord_by==ord_by_field){
			if(ord_by_ascdesc=='ASC'){
				$('#ord_by_ascdesc').val('DESC');
			}else{
				$('#ord_by_ascdesc').val('ASC');
			}
		}else{
			$('#ord_by_ascdesc').val('ASC');
		}
		get_ar('detail');
	}
	
	function get_ar(action_type) {
		show_loading_image('hide');
		show_loading_image('show');
		if(action_type==""){
			$("#detail_ins_id").val('');
			$("#detail_ins_name").val('');
			$("#detail_pat_id").val('');
			$("#detail_ins_email").val('');
			$("#detail_ins_fax").val('');
			$("#detail_group_by").val('');
		}else if(action_type=="pt_view"){
			$("#detail_pat_id").val('');
			$("#detail_group_by").val('');
			$("#old_action_type").val('');
		}else{
			$("#old_action_type").val(action_type);
		}
		document.sch_report_form.submit();
	}

	function ar_action(action_type,ajax_fun_ex_arg){
		show_loading_image('hide');
		var extra_msg_arr=[];
		var extra_msg="";
		if(action_type=="statement_update_alert"){
			$('#html_data_div input.chk_box_css[name="pat_chk_arr[]"]:checkbox:checked').each(function(){
				if($(this).data('statement')=="N"){
					extra_msg_arr.push($(this).val());
					$(this).trigger('click');
				}
			});
			if(extra_msg_arr.length>0){
				extra_msg = "<br>Statement can not be generated for patient id(s):- "+extra_msg_arr;
			}
		}
		if(action_type=="rebill_update_alert" || action_type=="claim_update_alert"){
			$('#html_data_div input.chk_box_css[name="chld_arr[]"]:checkbox:checked').each(function(){
				var encid = $(this).data('encid');
				if($('input.chk_box_css[data-encid="'+encid+'"]:checkbox:not(:checked)').length>0){
					extra_msg_arr.push(encid);
				}
			});
			if(extra_msg_arr.length>0){
				extra_msg = "There are more procedures in the encounter ("+extra_msg_arr+"),";
			}
		}
		if($('#html_data_div input.chk_box_css[name="chld_arr[]"]:checkbox:checked').length==0){
			fAlert('Please select any checkbox.'+extra_msg);
			return false;
		}else if(action_type=="write_off_update_alert"){
			$("#write_off_modal").modal('show');
			return false;
		}else if(action_type=="rebill_update_alert"){
			fancyConfirm(extra_msg+" Are you sure to Re-Submit only selected records?","","ar_action('rebill_update','')");
			return false;
		}else if(action_type=="claim_update_alert"){
			var ask = extra_msg+" Are you sure to print only selected records?";
			ask += '<br><br><div id="hcfa_ub_rad" class="hu_but_td"><div class="col-xs-3"><div class="radio radio-inline"><input type="radio" name="print_hcfa_ub" id="PrintCms_old" value="PrintCms" checked="checked" autocomplete="off" class="hu_css" onClick="$(\'#print_paper_type\').val(\'PrintCms\');"><label for="PrintCms_old">CMS 1500</label></div></div><div class="col-xs-4"><div class="radio radio-inline"><input type="radio" name="print_hcfa_ub" id="PrintCms_white_old" value="PrintCms_white" autocomplete="off" onClick="$(\'#print_paper_type\').val(\'PrintCms_white\');"><label for="PrintCms_white_old">CMS 1500 - Red Form</label></div></div><div class="col-xs-2"><div class="radio radio-inline"><input type="radio" name="print_hcfa_ub" id="printub_old" value="Printub" autocomplete="off" onClick="$(\'#print_paper_type\').val(\'Printub\');"><label for="printub_old">UB-04</label></div></div><div class="col-xs-3"><div class="radio radio-inline"><input type="radio" name="print_hcfa_ub" id="WithoutPrintub_old" value="WithoutPrintub" autocomplete="off" onClick="$(\'#print_paper_type\').val(\'WithoutPrintub\');"><label for="WithoutPrintub_old">UB-04 - Red Form</label></div></div></div>';
			fancyConfirm(ask,"","ar_action('claim_update','')","","670px");
			return false;
		}else if(action_type=="status_update_alert"){
			$("#claim_status_modal").modal('show');
			return false;
		}else if(action_type=="statement_update_alert"){
			//fancyConfirm("Are you sure to print Statement for selected records?"+extra_msg,"","ar_action('statement_update','')");
			fancyConfirm("Are you sure to print Statement for selected records?"+extra_msg,"","process_statements(0,10,'AR Worksheet');");
			return false;
		}else if(action_type=="assign_to_alert"){
			//show user dropdown
			$("#selectAssignForDiv").show();
			$("#selectAssignHead").show();
			$("#selectAssignReminderDate").hide();
			$("#selectAssignNotes").hide();
			$("#selectTaskOnReminderDate").hide();
			$("#selectFollowHead").hide();
			$("#task_or_followup").val('assign_to_update');
			$("#task_followup .modal-title").html('Assign To');
			$("#task_followup").modal('show');
			$('#selectAssignFor option').attr("selected",false);
			$('#selectAssignFor').selectpicker('refresh');
			return false;
		}else if(action_type=="followup_alert"){
			//hide user dropdown
			$("#selectAssignForDiv").show();
			$("#selectAssignHead").hide();
			$("#selectAssignReminderDate").show();
			$("#selectAssignNotes").show();
			$("#selectTaskOnReminderDate").show();
			$("#selectFollowHead").show();
			$("#task_or_followup").val('followup_update');
			$("#task_followup .modal-title").html('Follow Up');
			$("#task_followup").modal('show');
			$.each($("#selectAssignFor option"), function () {
				if($(this).val()==$('#session_user_id').val()){
					$(this).prop('selected', true);
				}
			});
			$('#selectAssignFor').selectpicker('refresh');
			return false;
		}else if(action_type=="letter_alert"){
			$("#error_ins_email").hide();
			$("#error_ins_fax").hide();
			$("#letter_modal").modal('show');
			return false;
		}

		var chld_id_arr=[];
		var enc_chld_arr={};
		var chld_balance_arr={};
		var print_paper_type="";
		var claim_status="";
		var task_users="";
		var task_dated="";
		var task_notes="";
		var task_on_reminder="";
		var show_write_codes={};
		$('#html_data_div input.chk_box_css[name="chld_arr[]"]:checkbox:checked').each(function(){
			var chld_chk_box_id=$(this).attr('id');
			var chld_chk_box_val=$('#'+chld_chk_box_id).val();
			chld_id_arr.push(chld_chk_box_val);
			var chld_chk_box_val_arr={};
			if(action_type!="rebill_update" && action_type!="claim_update"){
				if(chld_chk_box_val.indexOf(',') != -1){
					chld_chk_box_val_arr = chld_chk_box_val.split(',');
					$.each(chld_chk_box_val_arr, function( index, value ){
						enc_chld_arr[value]=$('#enc_chld_'+value).val();
						chld_balance_arr[value]=$('#chld_balance_'+value).val();
					});
				}else{
					enc_chld_arr[chld_chk_box_val]=$('#enc_chld_'+chld_chk_box_val).val();
					chld_balance_arr[chld_chk_box_val]=$('#chld_balance_'+chld_chk_box_val).val();
				}
			}
		});
		var ins_comp_id = $("#detail_ins_id").val();
		var ins_comp_name = $("#detail_ins_name").val();
		var ins_comp_fax = $("#detail_ins_fax").val();
		var ins_comp_email = $("#detail_ins_email").val();
		var group_by = $("#detail_group_by").val();

		if(action_type=="claim_update"){
			print_paper_type = $("#print_paper_type").val();
		}else if(action_type=="write_off_update"){
			show_write_codes = $("#show_write_code").val();
			$("#save_write_off").attr('disabled', true);
			$("#write_off_modal").modal('hide');
		}else if(action_type=="status_update"){
			claim_status = $("#status_confirm").val();
			$("#save_claim_status").attr('disabled', true);
			$("#claim_status_modal").modal('hide');
		}else if(action_type=="assign_to_update"){
			task_users=$("#selectAssignFor").val();
			if(!task_users){
				//fAlert('Please select an user to assign task.');
				//return false;
			}
			task_dated = $('#reminder_date').val();
			if(!$.trim(task_dated))
			{
				fAlert('Please enter Reminder date.');
				return false;
			}
			var myDate = new Date(task_dated);
			var today = new Date('<?php echo date('m-d-Y');?>');
			if ( myDate < today ) {
				fAlert('Past Reminder date is not allowed.');
				return false;
			}
			task_notes = $('#notes').val();
			$("#task_update").attr('disabled', true);
			$("#task_followup").modal('hide');
		}else if(action_type=="followup_update"){
			task_users=$("#selectAssignFor").val();
			if(!task_users){
				fAlert('Please select an user to assign task.');
				return false;
			}
			task_dated = $('#reminder_date').val();
			if(!$.trim(task_dated))
			{
				fAlert('Please enter Reminder date.');
				return false;
			}
			var myDate = new Date(task_dated);
			var today = new Date('<?php echo date('m-d-Y');?>');
			if ( myDate < today ) {
				fAlert('Past Reminder date is not allowed.');
				return false;
			}
			task_notes = $('#notes').val();
			task_on_reminder = $('#task_on_reminder').val();
			$("#task_update").attr('disabled', true);
			$("#task_followup").modal('hide');
		}else if(action_type=="letter_update"){
			$("#error_ins_email").hide();
			$("#error_ins_fax").hide();
			var letter_template=$('#letter_template').val();
			if(!letter_template){
				fAlert('Please select a letter template.');
				return false;
			}
			var letter_type=$('input:radio[name=letter_type]:checked').val();
			var letter_to=$('input:radio[name=letter_to]:checked').val();
			//validate insurance company email and fax number
			if(letter_to=='insurance' && group_by=='Insurance')
			{
				if(letter_type=='email' && ins_comp_email==''){$("#error_ins_email").show();return false;}
				else if(letter_type=='fax' && ins_comp_fax==''){$("#error_ins_fax").show();return false;}
			}
			$("#letter_modal").modal('hide');
		}
		var data = {
			"chld_arr": chld_id_arr,
			"enc_chld": enc_chld_arr,
			"chld_balance": chld_balance_arr,
			"ins_comp_id": ins_comp_id,
			"ins_comp_name": ins_comp_name,
			"ins_comp_fax": ins_comp_fax,
			"ins_comp_email": ins_comp_email,
			"action_type": action_type,
			"print_paper_type": print_paper_type,
			"claim_status": claim_status,
			"show_write_codes": show_write_codes,
			"task_users": task_users,
			"task_dated": task_dated,
			"task_notes": task_notes,
			"task_on_reminder": task_on_reminder,
			"letter_template": letter_template,
			"letter_type": letter_type,
			"letter_to": letter_to,
			"group_by": group_by
		};

		if(action_type!=""){
			show_loading_image('show');
			var url="ar_ajax.php";
			$.ajax({
				type: "POST",
				url: url,
				data: data,
				success: function(resp){
					resp = jQuery.parseJSON(resp);
					if(action_type=="claim_update"){
						if(typeof(resp.claim_path)!= "undefined" && resp.claim_path!=""){
							window.open(resp.claim_path,"PaperClaim","resizable=1,width=650,height=450");
						}else if(typeof(resp.error)!= "undefined" && resp.error!=""){
							show_loading_image('hide');
							fAlert("Charges can not be claimed due to some errors.");
							return false;
						}
					}else if(action_type=="statement_update"){
						if(typeof(resp.claim_path)!= "undefined" && resp.claim_path!=""){
							html_to_pdf(resp.claim_path,'p');
						}
					}else if(action_type=="letter_update"){
						var arrObj=resp.letter_update;
						if(typeof(arrObj.html_file)!= "undefined" && arrObj.html_file!=""  && arrObj.html_file!=null){
							JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
							top.html_to_pdf(arrObj.html_file, 'p');
							show_loading_image("hide");
							return false;
						}
						else if(typeof(arrObj.msg)!= "undefined" && arrObj.msg!=""){
							show_loading_image('hide');
							fAlert(arrObj.msg);
							return false;
						}
					}
					get_ar(action_type);
					/*if(typeof(resp.write_off_update)!= "undefined"){
						fAlert("Write Off updated successfully.");
					}*/
				}
			});
		}

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
		//$('#divLightBox').append('<div id="fade"></div>');
		//$('#fade').css({'filter':'alpha(opacity=50)'}).fadeIn();
		var popuptopmargin = ($(popupid).height() + 10) / 2;
		var popupleftmargin = ($(popupid).width() + 10) / 2;
		$(popupid).css({
			'margin-top': -popuptopmargin,
			'margin-left': -popupleftmargin
		});
	}

	function generate_pdf(op) {
		if (file_location != '') {
			html_to_pdf(file_location, op,'','','','html_to_pdf_reports');
		}
	}


	function searchPatient(){
		var name = document.getElementById("txt_patient_name").value;
		var findBy = document.getElementById("txt_findBy").value;
		$("#patientId").val('');
		var validate = true;
		  if(name.indexOf('-') != -1){
			name = name.replace(' ','');
			name = name.split('-');
			name = name[0]
			validate = false;
		  }
		  if(validate){
			if(isNaN(name)){
				pt_win = window.open("../../interface/scheduler/search_patient_popup.php?sel_by="+findBy+"&txt_for="+name+"&btn_enter="+findBy+"&btn_sub="+name+"&call_from=physician_console","mywindow","width=800,height=500,scrollbars=yes");
			}
			else{
				getPatientName(name);
			}
		  }
		return false;
	}
	function getPatientName(id,obj){
		$.ajax({
			type: "POST",
			url: JS_WEB_ROOT_PATH+"/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
			dataType:'JSON',
			success: function(r){
				if(r.id){
					if(obj){
						set_xml_modal_values(r.id,r.pt_name);
					}else{
						$("#txt_patient_name").val(r.pt_name);
						$("#patientId").val(r.id);
					}
				}else{
					fAlert("Patient doesn't exist.");
					$("#txt_patient_name").val('');
					$("#patientId").val('');
					return false;
				}
			}
		});
	}
	//previous name was getvalue
	function physician_console(id,name){
		document.getElementById("txt_patient_name").value = name;
		document.getElementById("patientId").value = id;
	}

	var old_pt_id=0;
	function showHideReportInfo(e, divWidth, pt_id){
		$(".infoInnerDiv").html(info_arr[pt_id]);
		var openedWinWidth=$(window).width();
		var setWidth= (openedWinWidth*divWidth) / 100;
		$("#infoTitleText").text('Charges can not be Rebilled/Claimed due to following errors :-');
		$("#div_rpt_info").css('width',setWidth);
		$("#div_rpt_info").css('left', e.pageX+10);
		$("#div_rpt_info").css('top', e.pageY+10);
		if(old_pt_id==0 || old_pt_id==pt_id)
		{
			$("#div_rpt_info").toggle("slow");
		}
		if(old_pt_id!=pt_id || old_pt_id==0)old_pt_id=pt_id;
		else old_pt_id=0;
		//$('#div_rpt_info .infoTitle').css('cursor', 'move');
		//$("#div_rpt_info").draggable({ handle: ".infoTitle" });
	}

	function hideReportInfo()
	{
		$('#div_rpt_info').toggle('slow');
		old_pt_id=0;
	}

	function task_action()
	{
		var ar_act=$("#task_or_followup").val();
		if(typeof(ar_act)!='undefined' && ar_act!=''){
			ar_action(ar_act,'');
		}
	}
	
	function process_statements(st,lt,call_from){
		show_loading_image('hide');
		var st_limit = lt;
		var st_chl_ids=[];
		var elements = $('#html_data_div input.chk_box_css[name="pat_chk_arr[]"]:checkbox:checked');
		var tot_pat_len=elements.length;
		elements = elements.slice(st,lt);
		$(elements).each(function(){
			var ptId = $(this).val()
			$('input.chk_box_'+ptId+'_child:checkbox:checked').each(function(){
				var chlid = $(this).data().chlid;
				st_chl_ids.push(chlid);
			});
		});
		var data_var = "&from="+call_from;
		//if(typeof($("#force_cond").val())!="undefined"){data_var +="&force_cond="+$("#force_cond").val();}
		if(typeof($("#print_pdf").val())!="undefined"){data_var +="&print_pdf="+$("#print_pdf").val();}
		html = '<div style="width:600px;"><span id="st_process_id"><p>Please do not close this dialog box until process complete.</p></span><iframe frameborder=0 framespacing=0 style="width:600px; height:150px;" src="../reports/process_statements.php?st_chl_ids='+st_chl_ids+'&st='+st+'&slt='+lt+'&st_limit='+st_limit+'&tot_pat_len='+tot_pat_len+data_var+'"></iframe></div>';
		fancyModal(html,'Process New Statement','650px','150px');
	}
	
	function task_reminder_date(){
		if($('#task_on_reminder').is(':checked')==true){
            $('#task_on_reminder').attr('checked',true);
            $('#task_on_reminder').val('yes');
        }else{
            $('#task_on_reminder').attr('checked',false);
            $('#task_on_reminder').val('no');
        }
	}

	$(document).ready(function (e) {
		//Phrases Typeahead
		var phraseArr = <?php echo $phraseArr; ?>;
		$('#notes').typeahead({source:phraseArr});

		function set_container_height(){
				$_min=($('#Reports_header').outerHeight(true)+$('#module_buttons').outerHeight(true)+50);
				$_hgt = (window.innerHeight) - $_min;
				$('.reportlft').css({
					'height':$_hgt,
					'max-height':$_hgt,
					'overflow-x':'hidden',
					'overflow-y':'auto'
				});
				$('.report-content').css({'height':((window.innerHeight)-$_min)+40,'overflow-x':'hidden','overflow-y':'auto'});
				$('#html_data_div').css({'height':((window.innerHeight)-$_min),'overflow-x':'auto','overflow-y':'auto'});

			if(HTMLCreated==1){
				//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
				if (!$('#sidebar').hasClass('collapsed')) {
				$(".fltimg .toggle-sidebar:first-child").trigger('click');
				}

				if(output=='output_pdf'){
					generate_pdf(op);
					show_loading_image('hide');
				}
				if(output=='output_csv'){
					export_csv();
					show_loading_image('hide');
				}
			}
		}

	$(window).load(function(){
		set_container_height();
	});

	$(window).resize(function(){
		set_container_height();
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);

	//function to make printable field drop down active deactive on behalf of view type
	$(".summary_detail").on('change',function(){
		if($(this).val()=='summary')$("#printable_columns").prop('disabled',true);
		else $("#printable_columns").prop('disabled',false);

		$('#printable_columns').selectpicker('refresh');
	});

		//insurance type on change function
		$("#ins_types").on('change', function() {
			var insTyp = $(this).val();

			if(typeof(insTyp)!=undefined && insTyp!=null)
			{
				$.each( insTyp, function( key, value ) {
					if(value=='Self Pay')
						{
							//uncheck all Insurance Group
							$('#insuranceGrp option').attr("selected",false);
							$('#insuranceGrp').selectpicker('refresh');
							//uncheck all Insurance Plan
							$('#ins_carriers option').attr("selected",false);
							$('#ins_carriers').selectpicker('refresh');
						}
				});
			}
		});
		//insurance type on change function
		$("#ins_types").on('change', function() {
			var insTyp = $(this).val();

			if(typeof(insTyp)!=undefined && insTyp!=null)
			{
				$.each( insTyp, function( key, value ) {
					if(value=='Self Pay')
						{
							//uncheck all Insurance Group
							$('#insuranceGrp option').attr("selected",false);
							$('#insuranceGrp').selectpicker('refresh');
							//uncheck all Insurance Plan
							$('#ins_carriers option').attr("selected",false);
							$('#ins_carriers').selectpicker('refresh');
						}
				});
			}
		});
		//insurance group on change function
		$("#insuranceGrp").on('change', function() {
			var select = document.getElementById("ins_types");
			select.options[3].select=false;
			 $.each($("#ins_types option:selected"), function () {
				if($(this).val()=='Self Pay'){
					$(this).prop('selected', false);
				}
			});
			$('#ins_types').selectpicker('refresh');
		});
		//insurance plan on change function
		$("#ins_carriers").on('change', function() {
			var select = document.getElementById("ins_types");
			select.options[3].select=false;
			 $.each($("#ins_types option:selected"), function () {
				if($(this).val()=='Self Pay'){
					$(this).prop('selected', false);
				}
			});
			$('#ins_types').selectpicker('refresh');
		});
		//function to open detail view
		$(".srh_ins").on('click', function() {
			var d = $(this).data();
			$("#detail_ins_id").val(d.id);
			$("#detail_ins_name").val(d.name);
			$("#detail_ins_email").val(d.email);
			$("#detail_ins_fax").val(d.fax);
			$("#detail_pat_id").val(d.patid);
			//perform on submit validations here
			document.sch_report_form.submit();
		});

		//function to open detail view
		$(".parent").on('click', function() {
			var d = $(this).data();
			var css_class=d.patient+'_child';
			$("."+css_class).toggleClass("hide");

			//manage horizontal expand
			if($(".detail_field").hasClass('hide')){
				$(".detail_field").toggleClass("hide");
				$(".detail_title").toggleClass("hide");
				//manage header fields col spans
				$(".detail_field_header_cal").attr('colspan',9);
				//manage total row pre col span
				var total_cal=$(".detail_field_precal").attr('colspan');
				total_cal=parseInt(total_cal)+9;
				$(".detail_field_precal").attr('colspan',total_cal);
				//manage total row post col span
				$(".detail_field_postcal").attr('colspan',6);
			}
		});
		/*check box related functions*/
		$(".pt_chk_box").on('click', function() {
			var d = $(this).data();
			var sts=$(this).is(":checked");
			var css_class='.chk_box_'+d.patient+'_child';
			$(css_class).prop("checked",sts);
		});
		$(".chk_box_enc_parent").on('click', function() {
			var d = $(this).data();
			var sts=$(this).is(":checked");
			var css_class='.chk_box_enc_'+d.encid+'_child';
			$(css_class).prop("checked",sts);
		});
		$(".chk_box_css").on('click', function() {
			var sts=$(this).is(":checked");
			var d = $(this).data();
			if(sts==true && d.ptid!='')
			{
				$("#pat_chk_arr_"+d.ptid).prop("checked",sts);
			}
		});
		/*letter related functions*/
		$(".letter_type").on('click', function() {
			var val=$(this).val();
			if(val=='fax'){
				if($("#letter_to_patient").is(":checked"))
				{
					$("#letter_to_patient").prop("checked",false);
					$("#letter_to_insurance").prop("checked",true);
				}
				$("#letter_to_patient").prop("disabled",true);
			}
			else $("#letter_to_patient").prop("disabled",false);
		});
	});
	show_loading_image('hide');
	var action_msg = "<?php echo $action_msg; ?>";
	if(action_msg!=""){
		alert_notification_show(action_msg);
	}
	function all_notes(eid){
		var url="../accounting/acc_ajax.php?action_type=enc_payment_comment&encounter_id="+eid;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				resp = jQuery.parseJSON(resp);
				if(typeof(resp.enc_pay_comm)!= "undefined"){
					show_modal('enc_notes_id','Encounter Notes',resp.enc_pay_comm,'','','modal-lg');
				}
			}
		});
	}
</script>
</body>
</html>
