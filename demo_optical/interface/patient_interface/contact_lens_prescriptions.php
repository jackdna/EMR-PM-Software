<?php 
/*
File: contact_lens_prescriptions.php
Coded in PHP7
Purpose: Contact Lens Prescriptions Information
Access Type: Direct access
*/
require_once("../../config/config.php");
require_once("../../library/classes/functions.php"); 

function escape($str)
{
	return imw_real_escape_string($str);
}
if($_POST['save'])
{
	/*$physicianId	= $_POST['rx_custom_physician'];*/
	$physicianName	= imw_real_escape_string($_POST['rx_custom_physician_name']);
	$physicianId = (int) trim($_POST['rx_custom_physician_id']);
	
	list($month,$day,$year)=explode('-',$_POST['dos']);
	$dos=$year.'-'.$month.'-'.$day;
	$disp_date= $month.'-'.$day.'-'.substr($year,-2);
	$entered_date=date('Y-m-d');
	$entered_time=date('H:i:s');
	
	$query="insert into in_cl_prescriptions set 
			sphere_od='".escape($_POST['sphere_od'])."',
			cylinder_od='".escape($_POST['cylinder_od'])."',
			axis_od='".escape($_POST['axis_od'])."',
			base_od='".escape($_POST['bc_od'])."',
			diameter_od='".escape($_POST['diameter_od'])."',
			add_od='".escape($_POST['add_od'])."',
			rx_make_od='".escape($_POST['make_od'])."',
			
			sphere_os='".escape($_POST['sphere_os'])."',
			cylinder_os='".escape($_POST['cylinder_os'])."',
			axis_os='".escape($_POST['axis_os'])."',
			base_os='".escape($_POST['bc_os'])."',
			diameter_os='".escape($_POST['diameter_os'])."',
			add_os='".escape($_POST['add_os'])."',
			rx_make_os='".escape($_POST['make_os'])."',
			
			physician_id = '".$physicianId."',
			physician_name='".$physicianName."',
			
			entered_date='$entered_date', 
			entered_time='$entered_time', 
			entered_by='$_SESSION[authId]',
			
			patient_id='$_SESSION[patient_session_id]',
			operator_id='".$_SESSION[authId]."',
			rx_dos='$dos',
			custom_rx=1";
	
	imw_query($query);
	$rxBaseDet= $physicianId.'~'.$physicianName.'~'.$patPhone.'~'.$lastExamDate.'~'.$disp_date;//set outsize rx=1 in last parameter
		 
	$rxDetailsOD=$_POST['sphere_od'].'~'.$_POST['cylinder_od'].'~'.$_POST['axis_od'].'~'.$_POST['bc_od'].'~'.$_POST['add_od'].'~'.$_POST['diameter_od'];
	$make_od=$make_od1=$_POST['make_od'];
	$rxDetailsOS=$_POST['sphere_os'].'~'.$_POST['cylinder_os'].'~'.$_POST['axis_os'].'~'.$_POST['bc_os'].'~'.$_POST['add_os'].'~'.$_POST['diameter_os'];
	$make_os=$make_os1=$_POST['make_os'];
	$db_dos=explode('-',$dos);
	//$db_dos_exp=$_POST['dos'];//$db_dos['2'].'-'.$db_dos['0'].'-'.$db_dos['1'];
	//create form for values
	/*$pref_detail='<input type="hidden" name="lens_rxDOS" id="lens_rxDOS_5200" value="'.$_POST['dos'].'">
	  <input type="hidden" name="lens_rxDOSRaw" id="lens_rxDOSRaw_5200" value="'.$db_dos_exp.'">
	  <input type="hidden" name="lens_rxBaseDet" id="lens_rxBaseDet5200" value="'.$rxBaseDet.'">
	  <input type="hidden" name="lens_rxDetailsOD" id="lens_rxDetailsOD5200" value="'.$rxDetailsOD.'">
	  <input type="hidden" name="lens_rxDetailsOS" id="lens_rxDetailsOS5200" value="'.$rxDetailsOS.'">
	  <input type="hidden" name="lens_rxDetailsDX" id="lens_rxDetailsDX5200" value="'.$lens_rxDetailsDX.'">';
	*/
	$pref_detail='<input type="hidden" name="cl_rxBaseDet5200" id="cl_rxBaseDet5200" value="'.$rxBaseDet.'">
      <input type="hidden" name="cl_rxDetailsOD5200" id="cl_rxDetailsOD5200" value="'.$rxDetailsOD.'">
      <input type="hidden" name="cl_rxDetailsOS5200" id="cl_rxDetailsOS5200" value="'.$rxDetailsOS.'">
      <input type="hidden" name="lens_rxDetailsDX5200" id="lens_rxDetailsDX5200" value="'.$lens_rxDetailsDX.'">
	  <input type="hidden" name="make_od_disp_5200" id="make_od_disp_5200" value="'.$make_od1.'">
	  <input type="hidden" name="make_od_5200" id="make_od_5200" value="'.$make_od.'">
	  <input type="hidden" name="make_os_disp_5200" id="make_os_disp_5200" value="'.$make_os1.'">
	  <input type="hidden" name="make_os_5200" id="make_os_5200" value="'.$make_os.'">
	  
	  <input type="hidden" name="manufacturer_5200" id="manufacturer_5200" value="">
	  <input type="hidden" name="brand_5200" id="brand_5200" value="">
	  <input type="hidden" name="manufacturer_5200_os" id="manufacturer_5200_os" value="">
	  <input type="hidden" name="brand_5200_os" id="brand_5200_os" value="">
	  <input type="hidden" name="cl_comments_5200" id="cl_comments_5200" value="">
	  
	  <input type="hidden" name="rx_dos_5200" id="rx_dos_5200" value="'.$db_dos_exp.'">';
		
	//header('Location:contact_lens_prescriptions.php');
	//exit;
}

if($_SESSION['patient_session_id']!=""){
	$p_name_qry = imw_query("select lname,fname,mname from patient_data where id = '".$_SESSION['patient_session_id']."' ");
	$p_name_row = imw_fetch_assoc($p_name_qry);
	$patient_name_id = $p_name_row['lname'].", ".$p_name_row['fname']." ".$p_name_row['mname']." - ".$_SESSION['patient_session_id'];
}
$qry_proc=imw_query("select cpt_fee_id from cpt_fee_tbl where cpt_prac_code='92015' or cpt4_code='92015'");
while($row_proc=imw_fetch_array($qry_proc)){
	$enc_proc_arr[$row_proc['cpt_fee_id']]=$row_proc['cpt_fee_id'];
}
$enc_proc_imp=implode(',',$enc_proc_arr);
$rxCount = $_REQUEST['rxCount'];

// GET CL CHARGES
$arrCLChargesAdmin=array();
$chargeRS=imw_query("Select * from cl_charges WHERE del_status=0 ORDER BY cl_charge_id");
while($chargeRES = imw_fetch_array($chargeRS)){
	$chargeRES['cpt_fee_id'].'<br>';	
	$cptPrice = $arrDefaultCPTFee[$chargeRES['cpt_fee_id']];
	$cptPrice = ($cptPrice<=0) ? '0' : $cptPrice; 
	$arrCLCharges[$chargeRES['name']] = $chargeRES['name'];
}

$arrCLCharges['Take Home CL']='Take Home CL';
$arrCLCharges['Current CL']='Current CL';
$arrCLCharges['Final']='Final';
$arrCLCharges['Current Trial']='Current Trial';
$arrCLCharges['Update Trial']='Update Trial';

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<?php include_once("../reports/report_includes.php");?>
<script type="text/javascript">
window.opener = window.opener.main_iframe.admin_iframe;
function fillParentForm(sno){

	var rxBaseDet=document.getElementById('cl_rxBaseDet'+sno).value.split('~');
	var rxDetailsOD=document.getElementById('cl_rxDetailsOD'+sno).value.split('~');
	var rxDetailsOS=document.getElementById('cl_rxDetailsOS'+sno).value.split('~');
	var rxDetailsDX=document.getElementById('lens_rxDetailsDX'+sno).value.split('~');
	// Base
	window.opener.document.getElementById('cl_physician_id_<?php echo $rxCount; ?>').value=rxBaseDet[0];
	window.opener.document.getElementById('cl_physician_name_<?php echo $rxCount; ?>').value=rxBaseDet[1];
	window.opener.document.getElementById('cl_telephone_<?php echo $rxCount; ?>').value=rxBaseDet[2];
	window.opener.document.getElementById('isRXLoaded_<?php echo $rxCount; ?>').value=1;
	// OD VALUES
	window.opener.document.getElementById('cl_sphere_od_<?php echo $rxCount; ?>').value=rxDetailsOD[0];
	window.opener.document.getElementById('cl_cylinder_od_<?php echo $rxCount; ?>').value=rxDetailsOD[1];
	window.opener.document.getElementById('cl_axis_od_<?php echo $rxCount; ?>').value=rxDetailsOD[2];
	window.opener.document.getElementById('cl_base_od_<?php echo $rxCount; ?>').value=rxDetailsOD[3];
	window.opener.document.getElementById('cl_diameter_od_<?php echo $rxCount; ?>').value=rxDetailsOD[5];
	window.opener.document.getElementById('cl_add_od_<?php echo $rxCount; ?>').value=rxDetailsOD[4];
	
	window.opener.document.getElementById('sph_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[0];
	window.opener.document.getElementById('cyl_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[1];
	window.opener.document.getElementById('axis_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[2];
	window.opener.document.getElementById('base_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[3];
	window.opener.document.getElementById('diam_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[5];
	window.opener.document.getElementById('add_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[4];
	
	// OS VALUES
	window.opener.document.getElementById('cl_sphere_os_<?php echo $rxCount; ?>').value=rxDetailsOS[0];
	window.opener.document.getElementById('cl_cylinder_os_<?php echo $rxCount; ?>').value=rxDetailsOS[1];
	window.opener.document.getElementById('cl_axis_os_<?php echo $rxCount; ?>').value=rxDetailsOS[2];
	window.opener.document.getElementById('cl_base_os_<?php echo $rxCount; ?>').value=rxDetailsOS[3];
	window.opener.document.getElementById('cl_diameter_os_<?php echo $rxCount; ?>').value=rxDetailsOS[5];
	window.opener.document.getElementById('cl_add_os_<?php echo $rxCount; ?>').value=rxDetailsOS[4];
	
	window.opener.document.getElementById('sph_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[0];
	window.opener.document.getElementById('cyl_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[1];
	window.opener.document.getElementById('axis_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[2];
	window.opener.document.getElementById('base_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[3];
	window.opener.document.getElementById('diam_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[5];
	window.opener.document.getElementById('add_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[4];
		
	//window.opener.document.getElementById('dx_code_<?php echo $rxCount; ?>').value=rxDetailsDX[0];

	window.opener.document.getElementById('disp_rx_dos_<?php echo $rxCount; ?>').innerHTML=rxBaseDet[4];
	window.opener.document.getElementById('rx_dos_<?php echo $rxCount; ?>').value=rxBaseDet[3];

	//window.opener.activeDeactiveFields();
	//window.opener.set_phone_format(window.opener.document.getElementById('cl_telephone_<?php echo $rxCount; ?>'),'<?php echo $GLOBALS['phone_format'];?>');
	
	/*Make Details*/
		var makeOD = document.getElementById('make_od_'+sno).value;
		var makeOD_disp = document.getElementById('make_od_disp_'+sno).value;
		var makeOS = document.getElementById('make_os_'+sno).value;
		var makeOS_disp = document.getElementById('make_os_disp_'+sno).value;
		
		window.opener.document.getElementById('rx_make_od_<?php echo $rxCount; ?>').value = makeOD;
		window.opener.document.getElementById('rx_make_os_<?php echo $rxCount; ?>').value = makeOS;
		
		window.opener.document.getElementById('rx_make_od_<?php echo $rxCount; ?>_disp').innerHTML=makeOD_disp;
		window.opener.document.getElementById('rx_make_od_<?php echo $rxCount; ?>_disp').title=makeOD;
		
		window.opener.document.getElementById('rx_make_os_<?php echo $rxCount; ?>_disp').innerHTML=makeOS_disp;
		window.opener.document.getElementById('rx_make_os_<?php echo $rxCount; ?>_disp').title=makeOS;
	/*End Make Details*/
	
	/*Change Manufacturer & Brand as per Rx*/
		manufacturer_id =  document.getElementById('manufacturer_'+sno).value;
		if(manufacturer_id>0){
		window.opener.document.getElementById("manufacturer_id_<?php echo $rxCount; ?>").value = manufacturer_id;}
	
		brand_id =  document.getElementById('brand_'+sno).value;
		if(brand_id>0 && manufacturer_id>0){window.opener.change_brand_rx(manufacturer_id,brand_id,<?php echo $rxCount; ?>);}
	
		manufacturer_id_os =  document.getElementById('manufacturer_'+sno+'_os').value;
		if(manufacturer_id_os>0){window.opener.document.getElementById("manufacturer_id_<?php echo $rxCount; ?>_os").value = manufacturer_id_os;}
	
		brand_id_os =  document.getElementById('brand_'+sno+'_os').value;
		if(manufacturer_id_os>0 && brand_id_os>0){window.opener.change_brand_rx(manufacturer_id_os,brand_id_os,'<?php echo $rxCount; ?>_os');}
	
	/*End change Manufacturer & Brand*/
	
	/*Cl Comments*/
	cl_comments = document.getElementById('cl_comments_'+sno).value;
	//if(cl_comments!="")
		window.opener.document.getElementById("item_comment_<?php echo $rxCount; ?>").value = cl_comments;
	
	window.opener.get_rx_dx_code('<?php echo $rxCount; ?>');
	window.close();
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
	
	
function custom(typ){
	if(typ=='form'){
		
		$('#rx_custom').show();
		$('#rx_custom_btn').show();
		$('#rxDataOptions').show();
		$('#rx_listing').hide();
		//$('#rx_listing_head').hide();
		$('#rx_listing_btn').hide();
		//$('#rx_not_found').hide();
	}
	else{
		
		$('#custom_rx_form')[0].reset();
		$('#rx_custom_physician_name').val('');
		$('#rx_custom_physician_id').val('');
		$('#rx_custom_id').val('');
		$('#rx_custom_physician_select').val('');
		/*$('#previousRx').val('');
		*/
		$('#dos').val('<?php echo date('m-d-Y'); ?>');
		$('#rx_custom').hide();
		$('#rx_custom_btn').hide();
		$('#rxDataOptions').hide();
		$('#rx_listing').show();
		//$('#rx_listing_head').show();
		$('#rx_listing_btn').show();
		//$('#rx_not_found').show();	  	  
	}
}
	jQ(document).ready(function(){
	jQ( ".date-pick" ).datepicker({
		changeMonth: true,changeYear: true,
		dateFormat: 'mm-dd-yy',
		onSelect: function() {
		jQ(this).change();
		}
	});
	
});
	
	
function rxPhysicianChange(){
	var value	= jQ('#rx_custom_physician_select').val();
	jQ('#rx_custom_physician_name').val(value);
}

jQ(document).ready(function(){
	/*Typeahead fot Physician*/
	jQ("#rx_custom_physician_select").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'refPhysicianName',
		hidIDelem: document.getElementById('rx_custom_physician_id'),
		showAjaxVals: 'phyName'
	});
});
</script>
</head>
<body>
	<div style="padding:0px; width:100%;">
        <div class="listheading">
            <div class="fl">Contact Lens Prescriptions</div>
            <div class="fl" style="width:auto; color:#03F; text-align:left; margin-left:180px;"><?php echo $patient_name_id;?></div>		
            <div class="fr" style="padding-right:10px;display:none;" id="rxDataOptions">
				<!--<label for="previousRx">Copy Rx:</label>
				<select name="previousRx" id="previousRx" onChange="copy_rx();"></select>&nbsp;&nbsp;-->
				<label for="rx_custom_physician_select">Ref. Physician Name:</label>
				<input type="text" id="rx_custom_physician_select" onChange="rxPhysicianChange();" />
			</div>
        </div>
        <?php

if($_POST['save'])
{
	echo $pref_detail;
	echo'<script type="text/javascript">fillParentForm(5200);</script>';
}
?>
<div id="rx_listing">
<?php
$patPhone='';
$patRs=imw_query("Select phone FROM facility WHERE facility_type='1'");
$patRes=imw_fetch_array($patRs);
if($patRes['phone']!=''){
	$patPhone=$patRes['phone'];
}

$arrRxDetails=array();
include('cl_rx_list.php');
if(count($arrRxDetails)>0){
	
?>
<table class="table_collapse" style="width:99.9.5%">
    <tr class="listheading">
      <td class="alignCenter" style="width:100px">DOS</td>
      <td class="alignCenter" style="width:80px">Type</td>
      <td class="alignCenter" style="width:80px">Sheet</td>
      <td class="alignCenter" style="width:50px">Vision</td>
	  <td class="alignCenter" style="width:80px">Make</td>
      <td class="alignCenter" style="width:80px">Sphere</td>
      <td class="alignCenter" style="width:80px">Cylinder</td>
      <td class="alignCenter" style="width:80px">Axis</td>
      <td class="alignCenter" style="width:80px">BC</td>
      <td class="alignCenter" style="width:80px">Add</td>
      <td class="alignCenter" style="width:80px; padding-right:15px;">Diameter</td>
    </tr>
</table> 
<div style="height:220px; overflow-y:scroll;" >
<table class="table_collapse text14" style="width:99.8%">   
    <?php
	$j=1;
	foreach($arrRxDetails1 as $arrRxDetails){
		foreach($arrRxDetails as $rxDetails){
		$clType=$rxDetails['clType'];
		 $axisOD=$axisOS='';
		 $trial='';
		 $axisOD=str_replace('&deg;', '', $rxDetails['orderHxA']);
		 $axisOS=str_replace('&deg;', '', $rxDetails['orderHxA_OS']);
		 
		 $trial=$rxDetails['clws_type'];
	     if($rxDetails['clws_type']=='Current Trial'){
			 $trial='Trial '.$rxDetails['clws_trial_number'];
		 }
		 
		 $db_dos=explode('-',$rxDetails['DOS']);
		 $db_dos_exp=$db_dos['2'].'-'.$db_dos['0'].'-'.$db_dos['1'];
		 
		 $rxBaseDet=$rxDetails['Provider'].'~'.$rxDetails['ProviderName'].'~'.$patPhone.'~'.$db_dos['2'].'-'.$db_dos['0'].'-'.$db_dos['1'].'~'.$db_dos['0'].'-'.$db_dos['1'].'-'.substr(trim($db_dos['2']),-2,2);
		 $rxDetailsOD=$rxDetails['orderHxS'].'~'.$rxDetails['orderHxC'].'~'.$axisOD.'~'.$rxDetails['orderHxBc'].'~'.$rxDetails['orderHxAdd'].'~'.$rxDetails['orderHxDia'];
		 $rxDetailsOS=$rxDetails['orderHxS_OS'].'~'.$rxDetails['orderHxC_OS'].'~'.$axisOS.'~'.$rxDetails['orderHxBc_OS'].'~'.$rxDetails['orderHxAdd_OS'].'~'.$rxDetails['orderHxDia_OS'];
			
		if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
		$get_dx_qry=imw_query("select patient_charge_list_details.diagnosis_id1,patient_charge_list_details.diagnosis_id2,patient_charge_list_details.diagnosis_id3,
		patient_charge_list_details.diagnosis_id4 from patient_charge_list join patient_charge_list_details 
		on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id 
		where patient_charge_list.patient_id='".$_SESSION['patient_session_id']."' and patient_charge_list_details.procCode in($enc_proc_imp)
		and patient_charge_list.date_of_service='$db_dos_exp'");
		$lens_rxDetailsDX="";
		$customRxLabel = "";
		$contextMenu = "";
		$incorrect = "";
		if($rxDetails['outside_rx']==1){
			$customRxLabel = "Custom Rx";
		}
			
		if(imw_num_rows($get_dx_qry)>0){
			$get_dx_row=imw_fetch_array($get_dx_qry);
			if($get_dx_row['diagnosis_id1']!=""){
				$lens_rxDetailsDX=$get_dx_row['diagnosis_id1'];
			}else if($get_dx_row['diagnosis_id2']!=""){
				$lens_rxDetailsDX=$get_dx_row['diagnosis_id2'];
			}else if($get_dx_row['diagnosis_id3']!=""){
				$lens_rxDetailsDX=$get_dx_row['diagnosis_id3'];
			}else if($get_dx_row['diagnosis_id4']!=""){
				$lens_rxDetailsDX=$get_dx_row['diagnosis_id4'];
			}
		}
		
		$make_od = $rxDetails['make_od'];
		$make_od_disp = (strlen($make_od)>14)?substr($make_od, 0, 14)."...":$make_od;
		
		/*Get Option Manufacturter and Brand/Style Id*/
			$manufacturer_id = 0;
			$brand_id = 0;
			$id_details = ($rxDetails['make_od']!="")?$rxDetails['make_od']:'0';
			$details = explode("-", $id_details);
			
			if(count($details)>0){
				$resp_manuf = imw_query("SELECT `id` FROM `in_manufacturer_details` WHERE `manufacturer_name`='".imw_real_escape_string(trim($details[0]))."' AND `cont_lenses_chk`=1 AND `del_status`=0 LIMIT 1");
				if($resp_manuf && imw_num_rows($resp_manuf)>0){
					$manufacturer_id = imw_fetch_assoc($resp_manuf);
					$manufacturer_id = $manufacturer_id['id'];
				}
				
				$resp_style = imw_query("SELECT `id` FROM `in_contact_brand` WHERE `brand_name`='".imw_real_escape_string(trim($details[1]))."' AND `del_status`=0 LIMIT 1");
				if($resp_style && imw_num_rows($resp_style)>0){
					$brand_id = imw_fetch_assoc($resp_style);
					$brand_id = $brand_id['id'];
				}
			}
		/*End getting optical manufacturer and brand/style Id*/
		
		$make_od1 = explode(" - ", $rxDetails['make_od']);
		$make_od1 = array_shift($make_od1);
		if(strlen($make_od1)>6){
			$make_od1 = explode(" ", $make_od1);
			$make_od1 = array_shift($make_od1);
		}
		$make_od1 = $make_od1?substr($make_od1, 0, 6)."...":$make_od1;
		
		$make_os = $rxDetails['make_os'];
		$make_os_disp = (strlen($make_os)>14)?substr($make_os, 0, 14)."...":$make_os;
		
		
		/*Get Option Manufacturter and Brand/Style Id OS*/
			$manufacturer_id_os = 0;
			$brand_id_os = 0;
			$id_details_os = ($rxDetails['make_os']!="")?$rxDetails['make_os']:'0';
			$details_os = explode("-", $id_details_os);
			
			if(count($details_os)>0){
				$resp_manuf_os = imw_query("SELECT `id` FROM `in_manufacturer_details` WHERE `manufacturer_name`='".imw_real_escape_string(trim($details_os[0]))."' AND `cont_lenses_chk`=1 AND `del_status`=0 LIMIT 1");
				if($resp_manuf_os && imw_num_rows($resp_manuf_os)>0){
					$manufacturer_id_os = imw_fetch_assoc($resp_manuf_os);
					$manufacturer_id_os = $manufacturer_id_os['id'];
				}
				
				$resp_style_os = imw_query("SELECT `id` FROM `in_contact_brand` WHERE `brand_name`='".imw_real_escape_string(trim($details_os[1]))."' AND `del_status`=0 LIMIT 1");
				if($resp_style_os && imw_num_rows($resp_style_os)>0){
					$brand_id_os = imw_fetch_assoc($resp_style_os);
					$brand_id_os = $brand_id_os['id'];
				}
			}
		/*End getting optical manufacturer and brand/style Id OS*/
		
		$make_os1 = explode(" - ", $rxDetails['make_os']);
		$make_os1 = array_shift($make_os1);
		if(strlen($make_os1)>6){
			$make_os1 = explode(" ", $make_os1);
			$make_os1 = array_shift($make_os1);
		}
		$make_os1 = (strlen($make_os1)>6)?substr($make_os1, 0, 6)."...":$make_os1;
		
	?>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td rowspan="2" class="alignCenter" style="width:100px"><a href="javascript:void(0);" onClick="javascript:fillParentForm('<?php echo $j;?>')" class="text_purpule"><?php echo $rxDetails['DOS'];?></a>
      <span class="custom_rx"><?php echo $customRxLabel; ?></span>
      </td>
      <td rowspan="2" class="alignCenter" style="width:80px"><?php echo $clType;?></td>
      <td rowspan="2" class="alignCenter" style="width:80px"><?php echo $trial;?></td>
      <td class="alignCenter blueColor" style="width:50px">OD</td>
	  <td class="alignCenter" style="width:80px; text-align:left;" title="<?php echo $make_od; ?>"><?php echo $make_od_disp; ?></td>
	  <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxS'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxC'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxA'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxBc'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxAdd'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxDia'];?></td>
    </tr>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td class="alignCenter greenColor" style="width:50px">OS</td>
	  <td class="alignCenter" style="width:80px; text-align:left;" title="<?php echo $make_os; ?>"><?php echo $make_os_disp; ?></td>
	  <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxS_OS'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxC_OS'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxA_OS'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxBc_OS'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxAdd_OS'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $rxDetails['orderHxDia_OS'];?>
      <input type="hidden" name="cl_rxBaseDet<?php echo $j;?>" id="cl_rxBaseDet<?php echo $j;?>" value="<?php echo $rxBaseDet;?>">
      <input type="hidden" name="cl_rxDetailsOD<?php echo $j;?>" id="cl_rxDetailsOD<?php echo $j;?>" value="<?php echo $rxDetailsOD;?>">
      <input type="hidden" name="cl_rxDetailsOS<?php echo $j;?>" id="cl_rxDetailsOS<?php echo $j;?>" value="<?php echo $rxDetailsOS;?>">
      <input type="hidden" name="lens_rxDetailsDX<?php echo $j;?>" id="lens_rxDetailsDX<?php echo $j;?>" value="<?php echo $lens_rxDetailsDX;?>">
	  <input type="hidden" name="make_od_disp_<?php echo $j;?>" id="make_od_disp_<?php echo $j;?>" value="<?php echo $make_od1;?>">
	  <input type="hidden" name="make_od_<?php echo $j;?>" id="make_od_<?php echo $j;?>" value="<?php echo $make_od;?>">
	  <input type="hidden" name="make_os_disp_<?php echo $j;?>" id="make_os_disp_<?php echo $j;?>" value="<?php echo $make_os1;?>">
	  <input type="hidden" name="make_os_<?php echo $j;?>" id="make_os_<?php echo $j;?>" value="<?php echo $make_os;?>">
	  
	  <input type="hidden" name="manufacturer_<?php echo $j;?>" id="manufacturer_<?php echo $j;?>" value="<?php echo $manufacturer_id;?>">
	  <input type="hidden" name="brand_<?php echo $j;?>" id="brand_<?php echo $j;?>" value="<?php echo $brand_id;?>">
	  <input type="hidden" name="manufacturer_<?php echo $j;?>_os" id="manufacturer_<?php echo $j;?>_os" value="<?php echo $manufacturer_id_os; ?>">
	  <input type="hidden" name="brand_<?php echo $j;?>_os" id="brand_<?php echo $j;?>_os" value="<?php echo $brand_id_os; ?>">
	  <input type="hidden" name="cl_comments_<?php echo $j;?>" id="cl_comments_<?php echo $j;?>" value="<?php echo $rxDetails['cl_comment'];?>">
      </td>
    </tr>
    <?php $j++;
		 }
	 }?>
</table>
</div>
		
<?php }else{ echo '<div style="height:220px;" class="alignCenter">No Record Exists.</div>';}?>
</div>
<div id="rx_custom" style="height:250px; overflow-y:scroll;display: none">
	<form name="custom_rx_form" id="custom_rx_form" method="post" action="">
		<input type="hidden" name="save" id="save" value="Save">
		<input type="hidden" name="rx_custom_physician_name" id="rx_custom_physician_name" value="" />
		<input type="hidden" name="rx_custom_physician_id" id="rx_custom_physician_id" value="" />
		<input type="hidden" name="rx_custom_id" id="rx_custom_id" />
		<table class="table_collapse" style="width:99%">
			<tr class="listheading">
			  <td class="alignCenter" style="width:100px">DOS</td><!--
			  <td class="alignCenter" style="width:80px">Type</td>
			  <td class="alignCenter" style="width:80px">Sheet</td>-->
			  <td class="alignCenter" style="width:50px">Vision</td>
			  <td class="alignCenter" style="width:80px">Make</td>
			  <td class="alignCenter" style="width:80px">Sphere</td>
			  <td class="alignCenter" style="width:80px">Cylinder</td>
			  <td class="alignCenter" style="width:80px">Axis</td>
			  <td class="alignCenter" style="width:80px">BC</td>
			  <td class="alignCenter" style="width:80px">Add</td>
			  <td class="alignCenter" style="width:80px; padding-right:15px;">Diameter</td>
			</tr>
			<tr class="cellBorder">
			  <td rowspan="2"><input type="text" name="dos" id="dos" value="<?php echo date('m-d-Y'); ?>" style="height:21px;width:80%;background-size:21px 21px;"  class="date-pick" ></td>
			<!--<td rowspan="2">
					<input type="text" name="typ" id="typ" title="Type" value="SCL" readonly style="width: 90%">
				</td>
			<td rowspan="2">
					<select name="sheet" id="sheet" title="Sheet" style="width: 90%">
						<option value="">Please Select</option>
						<?php
						foreach($arrCLCharges as $key=>$name)
						{
							echo "<option value='$key'>$name</option>";
						}
						?>
					</select>
				</td>-->
				
			  <td class="blueColor alignCenter">OD</td>
			  <td>
					<input type="text" name="make_od" id="make_od" title="Make" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="sphere_od" id="sphere_od" title="Sphere" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="cylinder_od" id="cylinder_od" title="Cylinder" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="axis_od" id="axis_od" title="Axis" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="bc_od" id="bc_od" title="BC" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="add_od" id="add_od" title="ADD" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="diameter_od" id="diameter_od" title="Diameter" value="" style="width: 90%">
			  </td>
		  </tr>
			<tr class="cellBorder">
			  <td class="greenColor alignCenter">OS</td>
			  <td>
					<input type="text" name="make_os" id="make_os" title="Make" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="sphere_os" id="sphere_os" title="Sphere" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="cylinder_os" id="cylinder_os" title="Cylinder" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="axis_os" id="axis_os" title="Axis" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="bc_os" id="bc_os" title="BC" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="add_os" id="add_os" title="ADD" value="" style="width: 90%">
			  </td>
			  <td>
					<input type="text" name="diameter_os" id="diameter_os" title="Diameter" value="" style="width: 90%">
			  </td>
		  </tr>
		</table> 
	</form>
</div>
	<div class="btn_cls mt10" style="width: 95%; float: left;">
     <div id="rx_listing_btn">
      <input type="button" name="Cancel" value="Close" onClick="javascript:window.close();"/>
      <input type="button" name="Custom_lst" value="Custom Rx" onClick="custom('form');"/>
      </div>
      
      <div id="rx_custom_btn" style="display:none">
      <input type="button" name="close" value="Close" onClick="javascript:window.close();"/>
      <input type="button" name="cancel" value="Cancel" onClick="custom('list');"/>
      <input type="submit" name="save" id="save" value="Save" onClick="save_cRx();" > 
      </div>
    </div>
     <script type="text/javascript">
	  function save_cRx()
	  {
		  
		if(!$("#make_od").val() && !$("#sphere_od").val() && !$("#cylinder_od").val() && !$("#axis_od").val() && !$("#bc_od").val() && !$("#add_od").val() && !$("#diameter_od").val() && !$("#make_os").val() && !$("#sphere_os").val() && !$("#cylinder_os").val() && !$("#axis_os").val() && !$("#bc_os").val() && !$("#add_os").val() && !$("#diameter_os").val())
		{
			falert('Please enter values');
			return false;
		}
		else
		$("#custom_rx_form").submit();	  
	}
	</script>
 </div>
</body>
</html>