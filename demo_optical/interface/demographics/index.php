<?php
/*
File: index.php
Coded in PHP7
Purpose: Show Patient Demographics Information
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");

require_once($GLOBALS['DIR_PATH']."/library/classes/functions.php");

require_once($GLOBALS['DIR_PATH']."/library/classes/common_functions.php");

$_SESSION['default_tab']="demographics";

if(isset($_REQUEST['newpt']) || isset($_REQUEST['closept']))
{
	unset($_SESSION['currentCaseid']);
	unset($_SESSION['patient_session_id']);
}

$pat_id = $_SESSION['patient_session_id'];
if($pat_id != ""){
$reslutPatientRow=getPatient_Data($pat_id);
$reslutRespRow=getResp_Data($pat_id);
}
//echo $_SESSION['currentCaseid'];

$arrProductCodes=array();

$msg_stat = "none";
$_SESSION['currentCaseid']='';
// Get Insurance Data
if($_SESSION['patient_session_id'] != ""){
if($_SESSION['currentCaseid'] == ""){
	$qry_ins_case = "select * from insurance_case where patient_id = '".$_SESSION['patient_session_id']."' and case_status = 'open' order by ins_case_type asc limit 0,1";
	$res_ins_case = imw_query($qry_ins_case);
	if(imw_num_rows($res_ins_case)>0){
		$row_ins_case = imw_fetch_assoc($res_ins_case);
		$_SESSION['currentCaseid'] = $row_ins_case['ins_caseid'];
	}
}

$qry_insurance = "select insurance_data.*, Date_Format(insurance_data.effective_date,'%m-%d-%Y') as active_date , 		
					insurance_companies.in_house_code as pracCodeVS, insurance_companies.claim_type as claimType, 
					insurance_companies.name as comp_name,insurance_companies.claim_type as InsClaimType 
					from insurance_data LEFT JOIN insurance_companies on 
					insurance_companies.id = insurance_data.provider
					where 
					insurance_data.pid='".$_SESSION['patient_session_id']."' 
					and insurance_data.ins_caseid = '".$_SESSION['currentCaseid']."' 
					and insurance_data.actInsComp='1' 
					and ( LOWER(insurance_data.type)= 'primary' OR LOWER(insurance_data.type) = 'secondary') ORDER BY date DESC";
$res_insurance = imw_query($qry_insurance);
while($row_insurance = imw_fetch_array($res_insurance))
{
	if($row_insurance["type"] == "primary")
	{	
		$pri_id = $row_insurance["id"];
		$primary_insurance = $row_insurance["comp_name"];
		$pri_prov = $row_insurance["provider"];
		$pri_policy_no = $row_insurance["policy_number"];
		$pri_gro_id = $row_insurance["group_number"];
		$pri_copay = $row_insurance["copay"];
		$pri_active_date = preg_replace('/[^0-9]/','',$row_insurance["active_date"])!="00000000"?$row_insurance["active_date"]:"";
		
	}
	else if($row_insurance["type"] == "secondary")
	{
		$sec_id = $row_insurance["id"];
		$secondary_insurance = $row_insurance["comp_name"];
		
		$sec_prov = $row_insurance["provider"];
		$sec_policy_no = $row_insurance["policy_number"];
		$sec_gro_id = $row_insurance["group_number"];
		$sec_copay = $row_insurance["copay"];
		$sec_active_date = preg_replace('/[^0-9]/','',$row_insurance["active_date"])!="00000000"?$row_insurance["active_date"]:"" ;
	}
}
}
?>

<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />

<link rel="stylesheet" href="../../library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>" />
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>

<script src="../../library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>

<script>
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
$(function() {
	$("#pri_active_date").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
	$("#sec_active_date").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
	$("#pt_dob").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
});
</script>

<script type="text/javascript">
$(document).ready(function() {

$("#emergency_relationship").change(function() {
	if($(this).val()=="Other")
	{
		$(this).hide();
		$(".emergency_other").show();		
		$(".emergency_other_img").show();
	}
});
$(".emergency_other_img").click(function() {
		$(this).hide();
		$(".emergency_other").hide();
		$("#emergency_relationship option[value='']").prop('selected',true);
		$("#emergency_relationship").show();		
		
});


$("#resp_relationship").change(function() {
	if($(this).val()=="Other")
	{
		$(this).hide();
		$(".resp_other").show();		
		$(".resp_other_img").show();
	}
});
$(".resp_other_img").click(function() {
		$(this).hide();
		$(".resp_other").hide();		
		$("#resp_relationship option[value='']").prop('selected',true);
		$("#resp_relationship").show();		
		
});


$("#newCaseDrop").change(function() {
	
	if($("#openCaseUrgent").val()=="1")
	{
		$("#openCaseUrgent").val("");
	}
	
});


//BUTTONS
var mainBtnArr = new Array();
top.btn_show("admin",mainBtnArr);

newpt = function()
{
	document.getElementById("demographicform").reset();
	//top.window.location.href = top.window.location.protocol+"//"+top.window.location.hostname +top.window.location.pathname + "?newpt";
	top.location.href='?newpt';
	//top.window.location.href =WEB_PATH+'/interface/demographics/index.php?newpt';
	
	//top.window.location.href="index.php?newpt";
}

newbtn = function()
{	
	document.getElementById('newCaseDrop').style.display='block';
	/*document.getElementById('casettitle').style.display='block';*/
	document.getElementById('opencase_btn').style.display='block';
	document.getElementById('new_ins_btn').style.display='none';
	$("#userCaseDrop").val("");
	$("#openCaseUrgent").val("1");
	$(".fields_rows input").val("");
}

/*if($("#userCaseDrop").val()!="")
{
			//alert(userCaseDropVal);
			var dataString = 'action=editins&case='+$("#userCaseDrop").val();
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: dataString,
				cache: false,
				success: function(response)
				{
						var dataArr = $.parseJSON(response);
						
						//alert("length " + dataArr.length);
						
						$.each(dataArr, function(i, item) 
						{
							if(item.type=="primary")
							{
								$("#edit_pri_insurance_id").val(item.id);
								$("#ins_primary").val(item.ins_caseid);								
								$("#pri_insurance_id").val(item.ins_caseid);								
								$("#pri_insurance_type").val(item.type);
								$("#pri_policy_no").val(item.policy_number);
								$("#pri_active_date").val(item.idate);
								$("#pri_group").val(item.group_number);
								$("#pri_copay").val(item.copay);
							}
							if(item.type=="secondary")
							{
								$("#edit_sec_insurance_id").val(item.id);
								$("#ins_secondary").val(item.ins_caseid);								
								$("#sec_insurance_id").val(item.ins_caseid);								
								$("#sec_insurance_type").val(item.type);
								$("#sec_policy_no").val(item.policy_number);
								$("#sec_active_date").val(item.idate);
								$("#sec_group").val(item.group_number);
								$("#sec_copay").val(item.copay);
							}
						});
				}
			});
		
}*/

$("#ins_primary").change(function() { 	
	$("#hid_pri_prov").val("0");
});

$("#ins_secondary").change(function() {	
	$("#hid_sec_prov").val("0");
});

$("#userCaseDrop").change(function() {
  
  	var userCaseDropVal = $("#userCaseDrop").val();
	
	if(userCaseDropVal!="")
	{
			//alert(userCaseDropVal);
			var dataString = 'action=editins&case='+userCaseDropVal;
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: dataString,
				cache: false,
				success: function(response)
				{
						var dataArr = $.parseJSON(response);
						$(".fields_rows input").val("");
						//alert("length " + dataArr.length);
						$.each(dataArr, function(i, item) 
						{
							if(item.type=="primary")
							{
								$("#edit_pri_insurance_id").val(item.id);
								$("#ins_primary").val(item.comp_name);								
								//$("#pri_insurance_id").val(item.ins_caseid);		
								$("#hid_pri_prov").val(item.provider);						
								$("#pri_insurance_type").val(item.type);
								$("#pri_policy_no").val(item.policy_number);
								
								if(item.active_date.replace(/[^0-9]/g,"") != '00000000')
								$("#pri_active_date").val(item.active_date);
								else
								$("#pri_active_date").val('');
								$("#pri_group").val(item.group_number);
								$("#pri_copay").val(item.copay);
							}
							if(item.type=="secondary")
							{
								$("#edit_sec_insurance_id").val(item.id);
								$("#ins_secondary").val(item.comp_name);								
								//$("#sec_insurance_id").val(item.ins_caseid);
								$("#hid_sec_prov").val(item.provider);											
								$("#sec_insurance_type").val(item.type);
								$("#sec_policy_no").val(item.policy_number);
								if(item.active_date.replace(/[^0-9]/g,"") != '00000000')
								$("#sec_active_date").val(item.active_date);
								else
								$("#sec_active_date").val('');
								$("#sec_group").val(item.group_number);
								$("#sec_copay").val(item.copay);
							}
						});
				}
			});
	}
  
});



opencase=function(ptid)
{
	var icase = $.trim($("#newCaseDrop").val());
	var ioutput="";
	if(icase=="")
	{
		top.falert("Please Select New Case");	
	}
	else
	{
		var dataString = 'action=opencase&case='+icase+'&pt='+ptid;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: dataString,
			cache: false,
			success: function(response)
			{
				
				if(response=="0")
				{
					 top.falert("Case Already Exist");
				}
				else
				{
					
					ioutput = response.split("--");
					$("#openCaseCreated").val(ioutput[1]);
					$("#userCaseDrop").append("<option selected='selected' value="+ioutput[1]+">"+ioutput[0]+"-"+ioutput[1]+"</option>");
				}
			}
		});  

	}
		
}


});

function closept()
{
//window.location.href='index.php?closept=close_pt';
//top.window.location.href = top.window.location.protocol+"//"+top.window.location.hostname +top.window.location.pathname + "?closept";

	window.location.href =WEB_PATH+'/interface/demographics/index.php?closept';
	top.location =WEB_PATH+'/index2.php?closept';
	//top.location.reload();
}

function validateForm()
{
	
	check = document.demographicform;
if((check.pri_insurance_id.value.replace(/\s/g, "") == "") && check.sec_insurance_id.value.replace(/\s/g, "") != "")
	{
			top.falert("Please enter Primary Insurance First!");
			check.pri_insurance_id.focus();
			return false;
	}
	
	if((check.openCaseUrgent.value == "1"))
	{
			top.falert("Please Select New Case");
			check.newCaseDrop.focus();
			return false;
	}
	
flag = false;
}

$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});

</script>

</head>
<body>
<?php

function checkInsCase()
{
	$qry = "select ins_case_type from insurance_case where ins_case_type = '".$_REQUEST['insCaseDrop']."' and patient_id = '".$_SESSION['patient_session_id']."'";
	$mqry = imw_query($qry);	
	if(imw_num_rows($mqry)>0)
	{
		return false;	
	}
	else
	{
		return true;	
	}
}

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_SESSION['patient_session_id']) && $_POST['edit_id'])
{	
			$pt_qry = "update patient_data set
			title = '".imw_real_escape_string($_POST['pat_title'])."',
			fname = '".imw_real_escape_string($_POST['pat_fname'])."',	
			mname = '".imw_real_escape_string($_POST['pat_mname'])."',
			lname = '".imw_real_escape_string($_POST['pat_lname'])."',
			DOB = '".saveDateFormat(imw_real_escape_string($_POST['pt_dob']))."',
			street = '".imw_real_escape_string($_POST['street'])."',
			street2 = '".imw_real_escape_string($_POST['street2'])."',
			email = '".imw_real_escape_string($_POST['email'])."',
			sex = '".imw_real_escape_string($_POST['sex'])."',
			city = '".imw_real_escape_string($_POST['city'])."',
			state = '".imw_real_escape_string($_POST['state'])."',
			postal_code = '".imw_real_escape_string($_POST['postal_code'])."',		
			zip_ext = '".imw_real_escape_string($_POST['zipext_0'])."',		
			phone_home = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['phone_home']))."',
			phone_biz = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['phone_biz']))."',
			phone_cell = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['phone_cell']))."',
			preferr_contact = '".imw_real_escape_string($_POST['pf_contact'])."',
			primary_care_phy_name = '".imw_real_escape_string($_POST['patient_pcp'])."',
			status = '".imw_real_escape_string($_POST['status'])."',				
			contact_relationship = '".imw_real_escape_string($_POST['pt_emergency_name'])."',
			emergencyRelationship = '".imw_real_escape_string($_POST['emergency_relationship'])."',
			emergencyRelationship_other = '".imw_real_escape_string($_POST['emergency_other'])."',
			phone_contact = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['emergency_phone']))."'
			where id='".$_POST['edit_id']."'
			";
			imw_query($pt_qry);
			//echo $pt_qry;
	/* Changed To Code Start */

	

	//START INSERT CHANGED ENTRY IN patient_previous_data TABLE
	if((trim($hidd_prev_title) != "" && (trim($hidd_prev_title) != trim($pat_title))) || (trim($hidd_prev_fname) != "" && (trim($hidd_prev_fname) != trim($pat_fname))) || (trim($hidd_prev_mname) != "" && (trim($hidd_prev_mname) != trim($pat_mname))) || (trim($hidd_prev_lname) != "" && (trim($hidd_prev_lname) != trim($pat_lname)))){
		//SAVE PATIENT-NAME
		extract($_POST);
		$qry1 = "INSERT INTO patient_previous_data SET
							patient_id = '".$edit_id."', save_date_time = '".date('Y-m-d H:i:s')."',
							operator_id	= '".$_SESSION['authId']."',
							patient_section_name = 'patientName',
							prev_title = '".convertUcfirst($hidd_prev_title)."',
							prev_fname = '".addslashes(convertUcfirst($hidd_prev_fname))."',
							prev_lname = '".addslashes(convertUcfirst($hidd_prev_lname))."',
							prev_mname = '".addslashes(convertUcfirst($hidd_prev_mname))."',							
							new_title = '".convertUcfirst($pat_title)."',
							new_fname = '".addslashes(convertUcfirst($pat_fname))."',
							new_lname = '".addslashes(convertUcfirst($pat_lname))."',
							new_mname = '".addslashes(convertUcfirst($pat_mname))."'";
		imw_query($qry1);
		//echo "<br>".$qry1."<br>";
	}
	
	/*-----START SAVING UPDATED MARITAL STATUS---Code---
	----- Inserting changed data in patient_previous_data TABLE --*/
	
	if(trim($hidd_prev_mstatus) != "" && trim($hidd_prev_mstatus) != trim($status)){
		extract($_POST);
		$qry2 = "INSERT INTO patient_previous_data SET patient_id = '".$edit_id."',
						save_date_time	= '".date('Y-m-d H:i:s')."', operator_id = '".$_SESSION['authId']."',
						patient_section_name= 'patientMstatus', 
						prev_mstatus = '".addslashes(convertUcfirst($hidd_prev_mstatus))."',
						new_mstatus	= '".addslashes(convertUcfirst($status))."'";		
						imw_query($qry2);
		//echo $qry2."<br>";
	}
	/*----end saving MARITAL STATUS---------*/
	
	/*-----START SAVING UPDATE Gender---Code---*/

	if(trim($hidd_prev_sex) != "" && trim($hidd_prev_sex) != trim($sex)){
		extract($_POST);
		$qry3 = "INSERT INTO patient_previous_data SET patient_id = '".$edit_id."',
						save_date_time	= '".date('Y-m-d H:i:s')."', operator_id = '".$_SESSION['authId']."',
						patient_section_name= 'patientGender',
						prev_sex = '".addslashes(convertUcfirst($hidd_prev_sex))."',
						new_sex = '".addslashes(convertUcfirst($sex))."'";		
		imw_query($qry3);
		//echo $qry3."<br>";
	}
	/*----end saving Gender---------*/	
	
	if((trim($hidd_prev_street) != "" && (trim($hidd_prev_street) != trim($street))) || (trim($hidd_prev_street2) != "" && (trim($hidd_prev_street2) != trim($street2))) || (trim($hidd_prev_postal_code) != "" && (trim($hidd_prev_postal_code) != trim($postal_code))) || (trim($hidd_prev_city) != "" && (trim($hidd_prev_city) != trim($city))) || (trim($hidd_prev_state) != "" && (trim($hidd_prev_state) != trim($state)))){
		extract($_POST);
			//SAVE PATIENT-ADDRESS
			$qry4 = "INSERT INTO patient_previous_data SET
								patient_id = '".$edit_id."', save_date_time = '".date('Y-m-d H:i:s')."',
								operator_id = '".$_SESSION['authId']."',
								patient_section_name = 'patientAddress',
								new_street = '".addslashes(convertUcfirst($street))."',
								new_street2 = '".addslashes(convertUcfirst($street2))."',
								new_postal_code = '".$postal_code."',
								new_city = '".addslashes(trim(convertUcfirst($city)))."',
								new_state = '".addslashes(trim(ucwords($state)))."',
								prev_street = '".addslashes(convertUcfirst($hidd_prev_street))."', 
								prev_street2 = '".addslashes(convertUcfirst($hidd_prev_street2))."',
								prev_postal_code = '".$hidd_prev_postal_code."',
								prev_city = '".addslashes(trim(convertUcfirst($hidd_prev_city)))."',
								prev_state = '".addslashes(trim(ucwords($hidd_prev_state)))."'";
			imw_query($qry4);
			//echo $qry4."<br>";
		}

		extract($_POST);
		
		$phone_home = core_phone_unformat($_POST['phone_home']);
		$phone_work = core_phone_unformat($_POST['phone_biz']);

		$phone_cell = core_phone_unformat($_POST['phone_cell']);
							
	if((trim($hidd_prev_phone_home) != "" && (trim($hidd_prev_phone_home) != trim($phone_home))) || (trim($hidd_prev_phone_biz) != "" && (trim($hidd_prev_phone_biz) != trim($phone_work))) || (trim($hidd_prev_phone_cell) != "" && (trim($hidd_prev_phone_cell) != trim($phone_cell))) || (trim($hidd_prev_email) != "" && (trim($hidd_prev_email) != trim($email)))){
		extract($_POST);
		//SAVE PATIENT-CONTACT
		$qry5 = "INSERT INTO patient_previous_data SET patient_id = '".$edit_id."', save_date_time = '".date('Y-m-d H:i:s')."',operator_id = '".$_SESSION['authId']."',";
		if(trim($hidd_prev_phone_home) != "" && trim($hidd_prev_phone_home) != trim($phone_home)) {
			$qry5 .= "prev_phone_home = '".core_phone_unformat($hidd_prev_phone_home)."',
											new_phone_home = '".core_phone_unformat($phone_home)."',";
		}
		
		if(trim($hidd_prev_phone_biz) != "" && trim($hidd_prev_phone_biz) != trim($phone_work)) {
			$qry5 .= " prev_phone_biz = '".core_phone_unformat($hidd_prev_phone_biz)."',
											new_phone_biz = '".core_phone_unformat($phone_work)."',";
		}
		
		if(trim($hidd_prev_phone_cell) != "" && trim($hidd_prev_phone_cell) != trim($phone_cell)){
			$qry5 .= "prev_phone_cell 	= '".core_phone_unformat($hidd_prev_phone_cell)."',
											new_phone_cell 		= '".core_phone_unformat($phone_cell)."',";
		}
		
		if(trim($hidd_prev_email) != "" && trim($hidd_prev_email) != trim($email)){
			$qry5 .= " prev_email = '".addslashes(trim($hidd_prev_email))."',
											new_email = '".addslashes($email)."',";
		}
		$qry5 .= "patient_section_name= 'patientContact'";
		imw_query($qry5);
		//echo $qry5."<br>";
	}

/* Changed To Code End */
						
			
			
			
			
			
			
			
	//		die();
			
			
			
}else if($_SERVER['REQUEST_METHOD'] == "POST" && empty($_SESSION['patient_session_id'])){
		$pt_qry = "insert into patient_data set
			title = '".imw_real_escape_string($_POST['pat_title'])."',
			fname = '".imw_real_escape_string($_POST['pat_fname'])."',	
			mname = '".imw_real_escape_string($_POST['pat_mname'])."',
			lname = '".imw_real_escape_string($_POST['pat_lname'])."',
			DOB = '".saveDateFormat(imw_real_escape_string($_POST['pt_dob']))."',
			street = '".imw_real_escape_string($_POST['street'])."',
			street2 = '".imw_real_escape_string($_POST['street2'])."',
			email = '".imw_real_escape_string($_POST['email'])."',
			sex = '".imw_real_escape_string($_POST['sex'])."',
			city = '".imw_real_escape_string($_POST['city'])."',
			state = '".imw_real_escape_string($_POST['state'])."',
			postal_code = '".imw_real_escape_string($_POST['postal_code'])."',		
			zip_ext = '".imw_real_escape_string($_POST['zipext_0'])."',		
			phone_home = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['phone_home']))."',
			phone_biz = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['phone_biz']))."',
			phone_cell = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['phone_cell']))."',
			preferr_contact = '".imw_real_escape_string($_POST['pf_contact'])."',
			primary_care_phy_name = '".imw_real_escape_string($_POST['patient_pcp'])."',
			status = '".imw_real_escape_string($_POST['status'])."',				
			contact_relationship = '".imw_real_escape_string($_POST['pt_emergency_name'])."',
			emergencyRelationship = '".imw_real_escape_string($_POST['emergency_relationship'])."',
		 	emergencyRelationship_other = '".imw_real_escape_string($_POST['emergency_other'])."',
			phone_contact = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['emergency_phone']))."'
			";
			
			imw_query($pt_qry)or die(imw_error());
			$lastpid = imw_insert_id();
			imw_query("update patient_data set pid = '".$lastpid."' where id = '".$lastpid."' ");
			$_SESSION['patient_session_id']=$lastpid;
			
			$insCaseInsert = "insert into insurance_case set
			ins_case_type = '".imw_real_escape_string($_POST['insCaseDrop'])."',
			patient_id = '".$_SESSION['patient_session_id']."',		
			start_date = '".date('Y-m-d')."',
			case_status = 'Open',
			athenaID = '0'
			";
			
			imw_query($insCaseInsert);
			$_SESSION['currentCaseid'] = imw_insert_id();

}
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_SESSION['patient_session_id']))
{			
			if($_POST['resp_hippa']=="on")
			{
				$resp_hippa_val=1;
			}
			else
			{
				$resp_hippa_val=0;		
			}
			$sql = "SELECT * FROM resp_party WHERE patient_id= '".$_POST['edit_id']."'";
			$res = imw_query($sql);
			if(imw_num_rows($res)>0){
				$resp_qry = "update resp_party set
				title = '".imw_real_escape_string($_POST['resp_title'])."',
				fname = '".imw_real_escape_string($_POST['resp_fname'])."',	
				mname = '".imw_real_escape_string($_POST['resp_mname'])."',
				lname = '".imw_real_escape_string($_POST['resp_lname'])."',
				address = '".imw_real_escape_string($_POST['resp_address1'])."',
				address2 = '".imw_real_escape_string($_POST['resp_address2'])."',
				suffix = '".imw_real_escape_string($_POST['resp_suffix'])."',
				relation = '".imw_real_escape_string($_POST['resp_relation'])."',	
				other1 = '".imw_real_escape_string($_POST['resp_other'])."',	
				hippa_release_status = '".imw_real_escape_string($resp_hippa_val)."',
				marital = '".imw_real_escape_string($_POST['resp_marital'])."',	
				zip = '".imw_real_escape_string($_POST['resp_zip'])."',		
				zip_ext = '".imw_real_escape_string($_POST['zipext_1'])."',		
				city = '".imw_real_escape_string($_POST['resp_city'])."',		
				state = '".imw_real_escape_string($_POST['resp_state'])."',
				
				home_ph = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['resp_phone_home']))."',
				work_ph = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['resp_phone_biz']))."',
				mobile = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['resp_phone_cell']))."',
				email = '".imw_real_escape_string($_POST['resp_email'])."'
				
				where patient_id='".$_POST['edit_id']."'
				";
			
				imw_query($resp_qry);
			}else{
					$resp_qry = "insert into resp_party set
					title = '".imw_real_escape_string($_POST['resp_title'])."',
					fname = '".imw_real_escape_string($_POST['resp_fname'])."',	
					mname = '".imw_real_escape_string($_POST['resp_mname'])."',
					lname = '".imw_real_escape_string($_POST['resp_lname'])."',
					address = '".imw_real_escape_string($_POST['resp_address1'])."',
					address2 = '".imw_real_escape_string($_POST['resp_address2'])."',
					suffix = '".imw_real_escape_string($_POST['resp_suffix'])."',
					relation = '".imw_real_escape_string($_POST['resp_relation'])."',	
					other1 = '".imw_real_escape_string($_POST['resp_other'])."',	
					hippa_release_status = '".imw_real_escape_string($resp_hippa_val)."',
					marital = '".imw_real_escape_string($_POST['resp_marital'])."',	
					zip = '".imw_real_escape_string($_POST['resp_zip'])."',		
					zip_ext = '".imw_real_escape_string($_POST['zipext_1'])."',		
					city = '".imw_real_escape_string($_POST['resp_city'])."',		
					state = '".imw_real_escape_string($_POST['resp_state'])."',
					patient_id='".$_POST[edit_id]."',
					
					home_ph = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['resp_phone_home']))."',
					work_ph = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['resp_phone_biz']))."',
					mobile = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['resp_phone_cell']))."',
					email = '".imw_real_escape_string($_POST['resp_email'])."'
					";

			imw_query($resp_qry);
			}
			
		$qry = "";
		if($_REQUEST['userCaseDrop'] != ""){
			$qry = "select * from insurance_case where ins_caseid = '".$_REQUEST['userCaseDrop']."' and patient_id = '".$_POST[edit_id]."'";
		}
		else if($_REQUEST['insCaseDrop'] != ""){	
			$qry = "select * from insurance_case where ins_case_type = '".$_REQUEST['insCaseDrop']."' and patient_id = '".$_POST[edit_id]."'";
		} 
			$mqry = imw_query($qry);
				
			if(imw_num_rows($mqry)<=0 && !empty($qry))
			{
				$insCaseInsert = "insert into insurance_case set
				ins_case_type = '".imw_real_escape_string($_REQUEST['insCaseDrop'])."',
				patient_id = '".$_POST[edit_id]."',
				start_date = '".date('Y-m-d')."',
				case_status = 'Open',
				athenaID = '0'
				";
				imw_query($insCaseInsert);
				$insCaseLASTID = imw_insert_id();
				$_SESSION['currentCaseid'] = $insCaseLASTID;
			
			}else {
				$ins_case_row = imw_fetch_assoc($mqry);
				$_SESSION['currentCaseid'] = $ins_case_row['ins_caseid'];
			}
			$pri_prov = ($_POST['hid_pri_prov'] != "")? $_POST['hid_pri_prov'] : ins_provider_id($_POST['ins_primary']);
			
			if($_POST['edit_pri_insurance_id']!="")
			{
				//provider='".$pri_prov."', DONT DELETE THIS COMMENT			
				$str3="";
				$strarr3="";
				$strarr3=explode("*",trim($_POST['ins_primary']));
				$str3=$strarr3[1];	
				
				if($_POST['hid_pri_prov']>0)
				{
					$str3=$_POST['hid_pri_prov'];
				}
				
				$pri_ins_qry = "update insurance_data set
				policy_number = '".imw_real_escape_string($_POST['pri_policy_no'])."',		
				effective_date = '".saveDateFormat(imw_real_escape_string($_POST['pri_active_date']))."',
				group_number = '".imw_real_escape_string($_POST['pri_group'])."',
				copay = '".imw_real_escape_string($_POST['pri_copay'])."',	
				provider='".$str3."',
				type = 'primary',
				pid = '".$_POST[edit_id]."',
				actInsComp = '1',
				ins_caseid = '".$_SESSION['currentCaseid']."',
				source = 'Inventory'
				where id = '".$_POST['edit_pri_insurance_id']."'
				";
				//echo $pri_ins_qry;
				//exit();
				imw_query($pri_ins_qry);
			}
			else
			{	
			$check_pri_ins = imw_query("select * from insurance_data where pid = '".$_POST[edit_id]."' and LOWER(type) = 'primary' and ins_caseid = '".$_SESSION['currentCaseid']."' and actInsComp='1' ");
				if(imw_num_rows($check_pri_ins)<=0)
				{
					$str4="";
					$strarr4="";
					$strarr4=explode("*",trim($_POST['ins_primary']));
					$str4=trim($strarr4[1]);
					
					if($_POST['hid_pri_prov']>0)
					{
						$str4=$_POST['hid_pri_prov'];
					}
					
					
					//provider='".$pri_prov."', DONT DELETE THIS COMMENT
					
					if($pri_prov != ""){
					$pri_ins_qry = "insert into insurance_data set
					policy_number = '".imw_real_escape_string($_POST['pri_policy_no'])."',
					effective_date = '".saveDateFormat(imw_real_escape_string($_POST['pri_active_date']))."',
					group_number = '".imw_real_escape_string($_POST['pri_group'])."',
					copay = '".imw_real_escape_string($_POST['pri_copay'])."',	
					provider='".$str4."',
					type = 'primary',
					pid = '".$_POST[edit_id]."',
					actInsComp = '1',
					ins_caseid = '".$_SESSION['currentCaseid']."',
					date = '".date('Y-m-d H:i:s')."',
					source = 'Inventory'
					";
					imw_query($pri_ins_qry);
					}
				}
			}
			
			$sec_prov = ($_POST['hid_sec_prov'] != "")? $_POST['hid_sec_prov'] : ins_provider_id($_POST['ins_secondary']);
			
			if($_POST['edit_sec_insurance_id']!="")
			{
				//provider='".$sec_prov."',	DONT DELETE THIS COMMENT
				$str1="";
				$strarr1="";
				$strarr1=explode("*",trim($_POST['ins_secondary']));					
				$str1=$strarr1[1];
				
				if($_POST['hid_sec_prov']>0)
				{
					$str1=$_POST['hid_sec_prov'];
				}				
				
				$sec_ins_qry = "update insurance_data set
				policy_number = '".imw_real_escape_string($_POST['sec_policy_no'])."',		
				effective_date = '".saveDateFormat(imw_real_escape_string($_POST['sec_active_date']))."',
				group_number = '".imw_real_escape_string($_POST['sec_group'])."',
				copay = '".imw_real_escape_string($_POST['sec_copay'])."',	
				provider='".$str1."',						
				type = 'secondary',
				pid = '".$_POST[edit_id]."',
				actInsComp = '1',
				ins_caseid = '".$_SESSION['currentCaseid']."',
				date = '".date('Y-m-d H:i:s')."',
				source = 'Inventory'
				where id = '".$_POST['edit_sec_insurance_id']."'
				";
				
				imw_query($sec_ins_qry);			
			}
			else{
					$sec_prov = ins_provider_id($_POST['ins_secondary']);
					if($sec_prov != ""){
						
						$str2="";
						$strarr2=explode("*",trim($_POST['ins_secondary']));					
						$str2=$strarr2[1];
						
						if($_POST['hid_sec_prov']>0)
						{
							$str2=$_POST['hid_sec_prov'];
						}							
						
						//provider='".$sec_prov."',	DONT DELETE THIS COMMENT						
						$sec_ins_qry = "insert into insurance_data set
						policy_number = '".imw_real_escape_string($_POST['sec_policy_no'])."',		
						effective_date = '".saveDateFormat(imw_real_escape_string($_POST['sec_active_date']))."',
						group_number = '".imw_real_escape_string($_POST['sec_group'])."',
						copay = '".imw_real_escape_string($_POST['sec_copay'])."',	
						provider='".$str2."',						
						type = 'secondary',
						pid = '".$_POST[edit_id]."',
						actInsComp = '1',
						ins_caseid = '".$_SESSION['currentCaseid']."',
						date = '".date('Y-m-d H:i:s')."',
						source = 'Inventory'
						";
						imw_query($sec_ins_qry);
					}
				}
					
			echo "<script>window.parent.location.href='".$GLOBALS['WEB_PATH']."/index2.php'</script>";
}
?>

<form action="" method="post" name="demographicform" id="demographicform" onSubmit="return validateForm()">

<input type="hidden" name="edit_id" value="<?php echo $_SESSION['patient_session_id']; ?>" />

<?php
if($_SESSION['patient_session_id']=="" && !isset($_REQUEST['newpt']))
{
?>
  <div class="module_heading" style="text-align:center;">
  <div style="height:<?php echo $_SESSION['wn_height']-400;?>px;">
  <div style="padding-top:200px;">Please select Patient to Proceed <br><br> To add new patient click on Add New Patient button below</div>
  </div>
  </div>
<script type="text/javascript">  
$(document).ready(function(e) {
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Add New Patient","top.main_iframe.newpt();");
	top.btn_show("patient",mainBtnArr);        
});
</script>
<?php
}
if(isset($_REQUEST['newpt']) || $_SESSION['patient_session_id']!="")
{
?>
		<div style="height:<?php echo $_SESSION['wn_height']-375;?>px; overflow-y:scroll;">   
        <img id="loading_img" style="display:none; position:absolute; top:20%; left:45%;" src="../../images/loading_image.gif" />     
		<table class="table_collapse">
                <tr class="listheading">
                  <td colspan="5" align="left">Demographics <div class="success_msg" style="display:<?php echo $msg_stat;?>;"><?php echo $msg; ?></div></td>
				  <td align="right"><img style="margin: 2px 10px 0px 0px; cursor: pointer;" onClick="javascript:closept();" src="../../images/delete_record.png" alt="remove_patient"></td>
                </tr>
                <tr>
                      <td>
                       <select name="pat_title" id="pat_title"  style="width:130px;">
                            <option value="" <?php if($reslutPatientRow["title"]==""){echo("selected");}?>></option>
                            <option value="Mr." <?php if($reslutPatientRow["title"]=="Mr."){echo("selected");}?>>Mr.</option>
                            <option value="Mrs." <?php if(($reslutPatientRow["title"]=="married" && $reslutPatientRow["rpPtSex"]=="Female")||$reslutPatientRow["title"]=="Mrs."){echo("selected");}?>>Mrs.</option>
                            <option value="Ms." <?php if($reslutPatientRow["title"]=="Ms."){echo("selected");}?>>Ms.</option>
                            <option value="Dr." <?php if($reslutPatientRow["title"]=="Dr."){echo("selected");}?>>Dr.</option>
                        </select>
                        <input type="hidden" name="hidd_prev_title" id="hidd_prev_title" value="<?php echo $reslutPatientRow["title"];?>">
                    </td> 
                    <td><input  size="17" name="pat_fname" id="pat_fname"  type="text"   value="<?php echo $reslutPatientRow["fname"]; ?>"  /><input type="hidden" name="hidd_prev_fname" id="hidd_prev_fname" value="<?php echo $reslutPatientRow["fname"];?>"></td>
                    <td><input  size="17" name="pat_mname" id="pat_mname"  type="text"  value="<?php echo $reslutPatientRow["mname"]; ?>" /><input type="hidden" name="hidd_prev_mname" id="hidd_prev_mname" value="<?php echo $reslutPatientRow["mname"];?>"></td>	 			
                    <td><input  size="17" name="pat_lname" id="pat_lname" type="text"   value="<?php echo $reslutPatientRow["lname"]; ?>" />
                   <input type="hidden" name="hidd_prev_lname" id="hidd_prev_lname" value="<?php echo $reslutPatientRow["lname"];?>">
                    </td>
					<td><input type="hidden" name="hidd_prev_sex" id="hidd_prev_sex" value="<?php echo $reslutPatientRow["sex"];?>">
                      <select name="sex" id="sex" style="width:140px;">
                        <option value="" <?php if ($reslutPatientRow{"sex"} == '') {echo "selected='selected'";};?>></option>
                        <option value="Male" <?php if ($reslutPatientRow{"sex"} == "Male") {echo "selected='selected'";};?>>Male</option>
                        <option value="Female" <?php if ($reslutPatientRow{"sex"} == "Female") {echo "selected='selected'";};?>>Female</option>
                    </select></td>
                    <td><input type="hidden" name="hidd_prev_mstatus" id="hidd_prev_mstatus" value="<?php echo $reslutPatientRow{"status"};?>">
                      <select name="status" id="status" style="width:140px;">
                        <option value="" <?php if ($reslutPatientRow{"status"} == "") {echo "selected='selected'";};?>></option>
                        <option value="divorced" <?php if ($reslutPatientRow{"status"}=="divorced") {echo "selected='selected'";};?>>Divorced</option>
                        <option value="domestic partner" <?php if ($reslutPatientRow{"status"}=="domestic partner") {echo "selected='selected'";};?>>Domestic Partner</option>
                        <option value="married" <?php if ($reslutPatientRow{"status"}=="married") {echo "selected='selected'";};?>>Married</option>
                        <option value="single" <?php if ($reslutPatientRow{"status"}=="single") {echo "selected='selected'";};?>>Single</option>
                        <option value="separated" <?php if ($reslutPatientRow{"status"}=="separated") {echo "selected='selected'";};?>>Separated</option>
                        <option value="widowed" <?php if ($reslutPatientRow{"status"}=="widowed") {echo "selected='selected'";};?>>Widowed</option>
                    </select></td>
                </tr>
                <tr>
                    <td class="module_label">Title</td>
                    <td class="module_label">First Name</td>
                    <td class="module_label">Middle</td>
                    <td class="module_label">Last Name</td>
					<td class="module_label">Gender</td>
                    <td class="module_label">Marital Status</td>
                </tr>
                <tr>
				  <td><input style="width:130px;"  size="17" type="text"  name="pt_dob" id="pt_dob"  value="<?php echo isset($reslutPatientRow["DOB"])?date("m-d-Y",strtotime($reslutPatientRow["DOB"])):""; ?>"/></td>	
                  <td><input  size="17" name="street" id="street"  type="text"  value="<?php echo $reslutPatientRow["street"]; ?>"  /></td>
                  <td><input  size="17" name="street2" id="street2"  type="text"  value="<?php echo $reslutPatientRow["street2"]; ?>" /></td>
                  <td><input  size="17" name="city" type="text" id="city_0" value="<?php echo $reslutPatientRow['city'];?>"  /></td>
                  <td><input  size="17" name="state" type="text"   id="state_0" value="<?php echo $reslutPatientRow['state'];?>" /></td>
                  <td><input size="5" onBlur="zip_vs_state_length(this,0)" onKeyUp="zip_vs_state(this,0,'dem')" name="postal_code" type="text" id="zip_0" value="<?php echo $reslutPatientRow['postal_code'];?>"   maxlength="5"  />
-
  <input size="4" name="zipext_0" type="text" value="<?php echo $reslutPatientRow['zip_ext'];?>" maxlength="4" /></td>
                </tr>
                <tr>
				  <td class="module_label">DOB</td>
                  <td class="module_label">Street 1</td>
                  <td class="module_label">Street 2</td>
                  <td class="module_label">City</td>
                  <td class="module_label">State</td>
                  <td class="module_label">Zip</td>
                </tr>
                <tr>
                    				
                    <td><input type="hidden" name="hidd_prev_phone_home" id="hidd_prev_phone_home" value="<?php echo $reslutPatientRow['phone_home'];?>">
                    <input  size="17"  name='phone_home' id="phone_home"  type="text"  onChange="javascript:set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($reslutPatientRow['phone_home']));?>" /></td>
                    <td><input type="hidden" name="hidd_prev_phone_biz" id="hidd_prev_phone_biz" value="<?php echo stripslashes(core_phone_format($reslutPatientRow['phone_biz']));?>">
                    <input  size="17"  name='phone_biz' id="phone_biz"  type="text" value="<?php echo stripslashes(core_phone_format($reslutPatientRow['phone_biz']));?>" onChange="javascript:set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" /></td>
					 <td><input  size="17" name='phone_cell' id="phone_cell"  type="text"  onChange="javascript:set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($reslutPatientRow['phone_cell']));?>"  /></td>				
                    <td><input type="hidden" name="hidd_prev_email" id="hidd_prev_email" value="<?php echo trim($reslutPatientRow['email']);?>">
                    <input  size="17" name="email" id="email" type="text"   value="<?php echo $reslutPatientRow["email"]; ?>" /></td>
                    <td><input  size="17" name='patient_pcp' id="patient_pcp"  type="text"   value="<?php echo ($reslutPatientRow["primary_care_phy_name"]); ?>"  /></td>				
                    <td>&nbsp;</td>				
                </tr>
                <tr>
                    <td class="module_label"><label><input type="radio" name="pf_contact" id="cohiceHome" value="0" <?php echo($reslutPatientRow["preferr_contact"]==0)?' checked':'';?> >Home</label></td>
                    <td class="module_label"><label><input type="radio" name="pf_contact" id="cohiceWork" value="1" <?php echo($reslutPatientRow["preferr_contact"]==1)?' checked':'';?>>Work Phone</label></td>
					<td class="module_label"><label><input type="radio" name="pf_contact" id="cohiceMobile" value="2" <?php echo($reslutPatientRow["preferr_contact"]==2)?' checked':'';?>>Mobile</label></td>
                    <td class="module_label">Email</td>
                    <td class="module_label">Primary Care Physician</td>
                    <td class="module_label">&nbsp;</td>
                </tr>                
                
            </table>
            
            <table width="102%" class="table_collapse" style="margin-top:10px;">
                <tr class="listheading">
                  <td colspan="5" align="left">Emergency Contact</td>
              </tr>
               
                <tr>	
                    <td><input  size="17"  name='pt_emergency_name' id="pt_emergency_name"  type="text"  value="<?php echo ($reslutPatientRow["contact_relationship"]); ?>"  /></td>
                    <td><?php $arr_relationship = array("",'self','Father','Mother','Son','Daughter','Spouse','Guardian','POA',"Friend","Aunt","Aunt/Uncle","Brother/Sister","Child:No Fin Responsibility","Dep Child:Fin Responsibility","Donor Live","Donor-Dceased","Employee","Foster Child","Grand Child","Grandparent","Handicapped Dependant","Injured Plantiff","Inlaw","Legal Guardian","Minor Dependent Of a Dependent","Niece/Nephew","Relative","Sponsored Dependent","Step Child","Student","Ward of The Court","Other");?>
                      <?php 
						
						if($reslutPatientRow["emergencyRelationship"]=="Other")
						{
							$display1 = "none";	
							$display2 = "block";	
						}
						else
						{
							$display1 = "block";
							$display2 = "none";
						}						
						?>
                      <select name="emergency_relationship" id="emergency_relationship" style="width:130px; display:<?php echo $display1; ?>">
                        <?php    foreach ($arr_relationship as $s) {
                                if($s == 'Doughter'){
                                    echo "<option value='".$s."'";
                                    if ($s == $reslutPatientRow["emergencyRelationship"])
                                        echo " selected";
                                    echo ">Daughter</option>\n";
            
                                }else{
                                    echo "<option value='".$s."'";
                                    if (strtolower($s) == strtolower($reslutPatientRow["emergencyRelationship"]))
                                        echo " selected";
                                    echo ">".ucfirst($s)."</option>\n";
                                }
                            }
                            ?>
                      </select>
                      <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td><input size="17" style="display:<?php echo $display2; ?>; " class="emergency_other" name="emergency_other" id="emergency_other" type="text" value="<?php echo $reslutPatientRow["emergencyRelationship_other"]; ?>" /></td>
                          <td><img class="emergency_other_img" style="margin:0 5px; cursor:pointer; display:<?php echo $display2; ?>; " src="../../images/icon_back.png" /></td>
                        </tr>
                      </table></td>
                    <td><input  size="17" name='emergency_phone' id="emergency_phone" type="text" value="<?php echo stripslashes(core_phone_format($reslutPatientRow["phone_contact"])); ?>"  onChange="javascript:set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" /></td>				
                    <td>&nbsp;</td>				
                     <td>&nbsp;</td>
                </tr>
                 <tr>
                    <td width="294" class="module_label">Emergency Name</td>
                    <td width="287" class="module_label">Relationship</td>
                    <td width="292" class="module_label">Emergency Tel#</td>
                    <td width="296" class="module_label">&nbsp;</td>
                    <td width="146" class="module_label">&nbsp;</td>
                </tr>
          </table>
            
            <table class="table_collapse" style="margin-top:10px;">
            	<tr class="listheading">
                	<td colspan="11">Responsibile Party / Guarantor</td>
            	</tr>
                <tr>
                    <td width="8%">
                        <select name="resp_title"  style="width:60px;">
                            <option value="" <?php if($reslutRespRow["title"]==""){echo("selected");}?>></option>
                            <option value="Mr." <?php if($reslutRespRow["title"]=="Mr."){echo("selected");}?>>Mr.</option>
                            <option value="Mrs." <?php if(($reslutRespRow["title"]=="married" && $rowGetPatientData["rpPtSex"]=="Female")||$reslutRespRow["title"]=="Mrs."){echo("selected");}?>>Mrs.</option>
                            <option value="Ms." <?php if($reslutRespRow["title"]=="Ms."){echo("selected");}?>>Ms.</option>
                            <option value="Dr." <?php if($reslutRespRow["title"]=="Dr."){echo("selected");}?>>Dr.</option>
                        </select>
                    </td>
                    <td width="12%"><input  style="width:100px;" size="17" type="text" name="resp_fname"  id="resp_fname" value="<?php echo $reslutRespRow["fname"]; ?>"  /></td>
                    <td width="14%"><input style="width:115px;" type="text" name="resp_mname"  id="resp_mname" value="<?php echo $reslutRespRow["mname"]; ?>"  /></td>
                    <td width="14%"><input style="width:115px;" size="17" type="text" name="resp_lname"  id="resp_lname" value="<?php echo $reslutRespRow["lname"]; ?>"  /></td>
                  <td width="14%" class="module_label">
                    <?php 
						
						if($reslutRespRow["relation"]=="Other")
						{
							$display3 = "none";	
							$display4 = "block";	
						}
						else
						{
							$display3 = "block";
							$display4 = "none";
						}						
						?>    
                    
                        <select name="resp_relation" id="resp_relationship" style="display:<?php echo $display3; ?>; width:120px; ">
                          <?php
                          foreach ($arr_relationship as $s) {
                                if($s == 'Doughter'){
                                    echo "<option value='".$s."'";
                                    if ($s == $reslutRespRow["relation"])
                                        echo " selected";
                                    echo ">Daughter</option>\n";
                
                                }else{
                                    echo "<option value='".$s."'";
                                    if (strtolower($s) == strtolower($reslutRespRow["relation"]))
                                        echo " selected";
                                    echo ">".ucfirst($s)."</option>\n";
                                }
                            }
                        ?>
                        </select>
                        
                       
                    <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                    <td> <input size="17" style="width:120px;display:<?php echo $display4; ?>; " class="resp_other" name="resp_other" id="resp_other" type="text" value="<?php echo $reslutRespRow["other1"]; ?>" /></td>
                    <td>
                    <img class="resp_other_img" style="cursor:pointer; display:<?php echo $display4; ?>; margin:4px 10px 0 3px;" src="../../images/icon_back.png" />   
                    </td>
                  </tr></table></td>
                    <td width="14%" class="module_label"><select name="resp_marital"  style="width:162px;">
                      <option value="" <?php if ($reslutRespRow["marital"] == "") {echo "selected='selected'";};?>></option>
                      <option value="Divorced" <?php if($reslutRespRow["marital"]=="Divorced") {echo "selected='selected'";};?>>Divorced</option>
                      <option value="Domestic Partner" <?php if($reslutRespRow["marital"]=="Domestic Partner") {echo "selected='selected'";};?>>Domestic Partner</option>
                      <option value="Married" <?php if($reslutRespRow["marital"]=="Married") {echo "selected='selected'";};?>>Married</option>
                      <option value="Single" <?php if($reslutRespRow["marital"]=="Single") {echo "selected='selected'";};?>>Single</option>
                      <option value="Separated" <?php if($reslutRespRow["marital"]=="Separated") {echo "selected='selected'";};?>>Separated</option>
                      <option value="Widowed" <?php if($reslutRespRow["marital"]=="Widowed") {echo "selected='selected'";};?>>Widowed</option>
                    </select></td>
                             
                  <td width="14%" class="module_label"><input type="checkbox" name="resp_hippa" id="resp_hippa" <?php if($reslutRespRow["hippa_release_status"]=='1'){echo "checked";} ?>>
&nbsp;Release HIPAA Info
                        
                    </td>
					
                </tr>
                <tr>
                    <td class="module_label">Title</td>
                    <td class="module_label">First Name</td>
                    <td class="module_label">Middle</td>
                    <td class="module_label">Last Name</td>
                  <td class="module_label">Relationship</td>
                    <td class="module_label">Marital Status</td>
                    <td class="module_label">&nbsp;</td>
					
                    
                </tr>
                
                <tr>
					<td colspan="2"><input style="width:118px" type="text" name="resp_address1"  id="resp_address1" value="<?php echo $reslutRespRow["address"]; ?>" /></td>
                    <td><input style="width:115px" type="text" name="resp_address2"  id="resp_address2" value="<?php echo $reslutRespRow["address2"]; ?>" /></td>
					<td>
                     
                      <input style="width:115px" type="text" name="resp_city"  id="city_1" value="<?php echo $reslutRespRow["city"]; ?>"  />
                    
                    </td>
					<td><span style="float:left; width:55px;">
					  <input  style="width:115px;" type="text" name="resp_state"  id="state_1" value="<?php echo $reslutRespRow["state"]; ?>" />
					</span></td>
					<td><input onBlur="zip_vs_state_length(this,0)" type="text" size="6" onKeyUp="zip_vs_state(this,1,'dem')" onKeyPress="zip_vs_state(this,1,'dem')"  name="resp_zip"  id="zip_1" value="<?php echo $reslutRespRow["zip"]; ?>" maxlength="5" /> - <input size="4" name="zipext_1" type="text" value="<?php echo $reslutRespRow['zip_ext'];?>" maxlength="4" /></td>
					<td>&nbsp;</td>
					
                </tr>
                <tr>	
                   <td class="module_label" colspan="2">Street 1</td>
                    <td class="module_label">Street 2</td>
					<td class="module_label"> 
                    City
                    </td>
					<td class="module_label">State</td>
					<td class="module_label">Zip Code</td>
					<td class="module_label">&nbsp;</td>
					
                </tr>
                <tr>
                  <td class="module_label" colspan="2"><input   style="width:118px"  name='resp_phone_home' id="resp_phone_home"  type="text"  onChange="javascript:set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($reslutRespRow["home_ph"]));?>" /></td>
                  <td><input  style="width:115px"  name='resp_phone_biz' id="resp_phone_biz"  type="text" value="<?php echo stripslashes(core_phone_format($reslutRespRow['work_ph']));?>" onChange="javascript:set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" /></td>
                  <td><input  style="width:115px" name='resp_phone_cell' id="resp_phone_cell"  type="text"  onChange="javascript:set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($reslutRespRow['mobile']));?>"  /></td>
                  <td><input  style="width:115px" name="resp_email" id="resp_email" type="text"   value="<?php echo $reslutRespRow["email"]; ?>" /></td>
                  <td class="module_label">&nbsp;</td>
                  <td class="module_label">&nbsp;</td>
                </tr>
                <tr>
                  <td class="module_label" colspan="2">Home</td>
                  <td class="module_label">Work Phone</td>
                  <td class="module_label">Mobile</td>
                  <td class="module_label">Email</td>
                  <td class="module_label">&nbsp;</td>
                  <td class="module_label">&nbsp;</td>
                </tr>
            </table>
  
            <table class="table_collapse" style="margin-top:10px;">
            	<tr class="listheading prihead" style="background:#c8e5f5;">
                	<td colspan="5">
                    <div style="width:160px; float:left; position:relative; top:4px;">&nbsp;Primary Insurance</div>
                    <div style="width:600px; display:block; height:32px; float:right;" class="prinscode">
                            <table class="table_collapse prinscodetable" border="0">
                            <tr>
                              <td align="left" width="120">
                              <input type="hidden" name="openCaseUrgent" id="openCaseUrgent" value=""  />                   
                              <input type="hidden" name="openCaseCreated" id="openCaseCreated" value=""  />                   
                              <?php  
                              if($_SESSION['currentCaseid'] != "")
							  {
                        $qry = "select * from insurance_case where ins_caseid = '".$_SESSION['currentCaseid']."'";
                        $chekptin = imw_query($qry);
                              }
                              else if($_SESSION['patient_session_id'] !=""){
                                    $qry = "select * from insurance_case where  patient_id = '".$_SESSION['patient_session_id']."' AND case_status = 'Open' ORDER by ins_case_type ASC,start_date DESC";
                                   $chekptin = imw_query($qry);	
                                   $row = imw_fetch_assoc($chekptin);	
                                   $_SESSION['currentCaseid'] = $row['ins_caseid'];
                              }
                              if(imw_num_rows($chekptin)>0)
                              {
                              ?>            		        
                              <select class="fl" name="userCaseDrop" id="userCaseDrop" onChange="">
                                    <?php
                                    $insCaseQry = imw_query("select * from insurance_case where patient_id = '".$_SESSION['patient_session_id']."' order by ins_caseid desc");			
                                                while($insCaseRow = imw_fetch_array($insCaseQry))
                                                {										
                                                $casen = imw_query("select case_id,case_name from insurance_case_types where case_id = '".$insCaseRow['ins_case_type']."'");								
                                                while($casenrow = imw_fetch_array($casen))
                                                {										
                                                    $sel="";
                                                    if($_SESSION['currentCaseid'] == $insCaseRow['ins_caseid'])
                                                    {
                                                        $sel = 'selected="selected"';									
                                                    }
                                                 ?>
                        <option <?php echo $sel; ?> value="<?php echo $insCaseRow['ins_caseid']; ?>"><?php echo $casenrow['case_name']."-".$insCaseRow['ins_caseid']; ?></option>
                                                <?php 
                                                
                                                }
                                          }
                                    ?>
                                </select>   
                              <?php 
                              } else {
                              ?>  
                                <select class="fl" name="insCaseDrop" id="insCaseDrop">
                                    <?php $insCaseQry = imw_query("select * from insurance_case_types where status = '0' order by case_name ASC ");
                                          while($insCaseRow = imw_fetch_array($insCaseQry))
                                          {
											  	$sel="";
												if($insCaseRow['normal']==1)
												{
													$sel = 'selected="selected"';									
												}
                                    ?>
                                    <option value="<?php echo $insCaseRow['case_id']; ?>" <?php echo $sel; ?>><?php echo $insCaseRow['case_name']; ?></option>
                                    <?php } ?>
                                </select>                    
                              <?php } ?>
                               
                              </td>
                                <td align="left" width="60"><span class="fl"><span class="module_label">
                                  <div class="btn_cls" style="margin:0;padding:0;">
                                 <?php if($_SESSION['currentCaseid'] !=""){?> 
                                  <input style="margin:0px 0 0px 10px; height:24px;" class="newcase_btn_ch" onClick="newbtn()" type="button" name="new_ins_btn"  id="new_ins_btn"  value="New Case"/> <?php } ?>
                                  </div>
                                </span></span></td>
                                 <td align="left" width="60"><select class="fl" name="newCaseDrop" id="newCaseDrop" style="display:none; margin:0 0 0 10px;">
                                    <option value="">Select Case</option>
                                    <?php $insCaseQry = imw_query("select * from insurance_case_types where status = '0' order by case_name ASC");
                                          while($insCaseRow = imw_fetch_array($insCaseQry))
                                          {
											  $sel="";
												if($insCaseRow['normal']==1)
												{
													$sel = 'selected="selected"';									
												}
                                    ?>
                                    <option value="<?php echo $insCaseRow['case_id']; ?>" <?php echo $sel; ?>><?php echo $insCaseRow['case_name']; ?></option>
                                    <?php } ?>
                                </select></td>
                                 <td align="left" class="fl" valign="top">
                                 <div class="btn_cls" style="padding:0;">
               
                                   <input type="button" style="display:none;margin:0 0 0 10px;height:24px !important; position:relative; bottom:4px;" onClick="javascript:opencase('<?php echo $_SESSION['patient_session_id']; ?>');" name="opencase_btn" id="opencase_btn" value="Open Case"/>
                               
                                 </div>
                                 </td>
                                 <td align="left">&nbsp;</td>
                                 <td align="left">&nbsp;</td>
                            </tr>
<!--                            <tr>
                                <td class="module_label">Choose Case</td>
                                <td class="module_label">&nbsp;</td>
                                <td class="module_label"><div style="display:none; margin:0 0 0 10px;" id="casettitle">Case Type</div></td>
                                <td class="module_label">&nbsp;</td>
                                <td class="module_label">&nbsp;</td>
                                <td class="module_label">&nbsp;</td>
                            </tr>            
 -->                        </table>
                    </div>
                    
                    
                    
                    
                    
                    
                    </td>
           	  </tr>                
                <tr class="fields_rows pri_fild">
                    <td>
                        <input type="hidden" name="edit_pri_insurance_id" id="edit_pri_insurance_id" value="<?php echo isset($pri_id)?$pri_id:"";?>" />
<!--                        <input type="hidden" name="pri_insurance_id" id="pri_insurance_id" value="" />
-->                     <input type="hidden" name="pri_insurance_type" id="pri_insurance_type" value="" />
						<input type="hidden" name="hid_pri_prov" id="hid_pri_prov" value="<?php echo isset($pri_prov)?$pri_prov:"";?>" />

<input type="hidden" name="ins_primary_id" id="ins_primary_id" value="" />
                        <?php //echo isset($primary_insurance)?$primary_insurance:"";?>
                        <input style="width:130px;"  type="text" onChange="javascript:document.getElementById('hid_pri_prov').value=0; document.getElementById('pri_insurance_type').value='primary';" name="ins_primary" id="ins_primary" value="<?php echo isset($primary_insurance)?$primary_insurance:""; ?>" autocomplete="off" /><!--document.getElementById('pri_insurance_id').value='1'; -->
                  </td>
                     <td><input style="width:130px;"  size="17" type="text" name="pri_policy_no"  id="pri_policy_no" value="<?php echo isset($pri_policy_no)?$pri_policy_no:"";?>"  /></td>
                    <td><input style="width:130px;"  size="17" type="text"  name="pri_active_date" id="pri_active_date"  value="<?php echo isset($pri_active_date)?$pri_active_date:"";?>"/></td>
                     <td><input style="width:130px;"   size="17" type="text" name="pri_group"  id="pri_group" value="<?php echo isset($pri_gro_id)?$pri_gro_id:"";?>" /></td>				
                    <td><input style="width:130px;"   size="17" type="text" name="pri_copay"  id="pri_copay" value="<?php echo isset($pri_copay)?$pri_copay:"";?>" /></td>
                </tr>
                <tr>
                    <td class="module_label">Ins. Provider</td>
                    <td class="module_label">Policy #</td>
                    <td class="module_label">Activation Date</td>
                    <td class="module_label">Group #</td>
                    <td class="module_label">Copay</td>
                </tr>
  </table>
            <table class="table_collapse" style="margin-top:10px;">
            	<tr class="listheading">
                	<td colspan="5">Secondary Insurance</td>
            	</tr>                
                <tr class="fields_rows">
                    <td>
                    	<input  type="hidden" name="edit_sec_insurance_id" id="edit_sec_insurance_id" value="<?php echo isset($sec_id)?$sec_id:"";?>" />
                        <!--<input type="hidden" name="sec_insurance_id" id="sec_insurance_id" value="" />-->

<input type="hidden" name="ins_secondary_id" id="ins_secondary_id" value="" />

                        <input type="hidden" name="sec_insurance_type" id="sec_insurance_type" value="" />
                        <input type="hidden" name="hid_sec_prov" id="hid_sec_prov" value="<?php echo isset($sec_prov)?$sec_prov:"";?>" />
                        <input style="width:130px;" type="text" onChange="javascript:document.getElementById('hid_sec_prov').value=0;document.getElementById('sec_insurance_id').value='1'; document.getElementById('sec_insurance_type').value='secondary';" id="ins_secondary"  name="ins_secondary" value="<?php echo isset($secondary_insurance)?$secondary_insurance:"";?>" autocomplete="off" />                        
                    </td>
                	<td><input style="width:130px;" size="17" type="text" name="sec_policy_no"  id="sec_policy_no"  value="<?php echo isset($sec_policy_no)?$sec_policy_no:"";?>" /></td>
                	<td><input style="width:130px;" size="17" type="text" name="sec_active_date"  id="sec_active_date"  value="<?php echo isset($sec_active_date)?$sec_active_date:"";?>"  /></td>
               		<td><input style="width:130px;" size="17" type="text" name="sec_group"  id="sec_group" value="<?php echo isset($sec_gro_id)?$sec_gro_id:"";?>" /></td>				
                	<td><input style="width:130px;" size="17" type="text" name="sec_copay"  id="sec_copay" value="<?php echo isset($sec_copay)?$sec_copay:"";?>" /></td>
                </tr>
                <tr>
                    <td class="module_label">Ins. Provider</td>
                    <td class="module_label">Policy #</td>
                    <td class="module_label">Activation Date</td>
                    <td class="module_label">Group #</td>
                    <td class="module_label">Copay</td>
                </tr>
            </table> 
  </div>		   
	<div style="display:none">
        <input type="submit" name="demographic_submit" id="demographic_submit" value="Save"/>                  
    </div>
</form>
<script type="text/javascript">
function reload_window(){
	window.location.href=WEB_PATH+'/interface/demographics/index.php';
}
function form_submit(){
	$('#demographic_submit').click();
}
$(document).ready(function(e) {
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Cancel","top.main_iframe.reload_window();");
	mainBtnArr[1] = new Array("frame","New","top.main_iframe.newpt();");
	mainBtnArr[2] = new Array("frame","Save","top.main_iframe.form_submit();");
	top.btn_show("patient",mainBtnArr);        
});
</script>
<?php }?>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<!-- actb code -->
<script> 


var ins = new Array(<?php echo fnLineBrk(ins_providers());?>);
var insid = new Array();

new actb(document.getElementById('ins_primary'),ins);
new actb(document.getElementById('ins_secondary'),ins);

//new actb(document.getElementById('ins_secondary'),ins,"","",document.getElementById('ins_secondary_id'),insid);

</script>
</body>
</html>