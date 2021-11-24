<?php 
/*
File: lens_prescriptions.php
Coded in PHP7
Purpose: Show Lens Prescription 
Access Type: Direct access
*/
require_once("../../config/config.php");
require_once("../../library/classes/functions.php"); 
function prismNumbers($selected=''){
	$optValues='';
	for($i=0.25; $i<=15;){
		$sel=($i==$selected)? 'selected': '';
		$optValues.='<option value="'.$i.'" '.$sel.'>'.$i.'</option>';

		/* if($i>=8){ */
			$i+=0.25;
		/* }else{
			$i+=0.5;
		} */
	}
	return $optValues;
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

$patPhone='';
$patRs=imw_query("Select phone FROM facility WHERE facility_type='1'");
$patRes=imw_fetch_array($patRs);
if($patRes['phone']!=''){
	$patPhone=$patRes['phone'];
}

$enc_proc_imp=implode(',',$enc_proc_arr);
$rxCount = $_REQUEST['rxCount'];
include('lens_rx_list.php');
$strAllPhy = implode(',', $arrAllPhy);
	
$rs=imw_query("Select id,fname,lname FROM users WHERE id IN(".$strAllPhy.")");
while($res=imw_fetch_array($rs)){
	 if($res['lname']!='' || $res['fname']!=''){
		 $phyName=$res['lname'].', '.$res['fname'];
	 }
	$arrUsers[$res['id']]=$phyName;
}
function strUp($str)
{
	return strtoupper($str);
}
if($_POST['save'])
{
	$patient_id=$_SESSION['patient_session_id'];
	$operator_id=$_SESSION['authId'];
	$entered_date=date('Y-m-d');
	$entered_time=date('H:i:s');
	list($month,$day,$year)=explode('-',$_POST['dos']);
	$dos=$year.'-'.$month.'-'.$day;
	
	$_POST['od_dpd']= $_POST['od_dpd_a'];
	$_POST['os_dpd']= $_POST['os_dpd_a'];
	
	$_POST['od_npd']= $_POST['od_npd_a'];
	$_POST['os_npd']= $_POST['os_npd_a'];
	
	/*$physicianId	= $_POST['rx_custom_physician'];*/
	$physicianName	= imw_real_escape_string($_POST['rx_custom_physician_name']);
	$physicianId = (int) trim($_POST['rx_custom_physician_id']);
	
	$rxId = (int)$_POST['rx_custom_id'];
	
	$rxId;
	
	$where = '';
	$logFields = '';
	if( $rxId > 0 ){
		$qry = 'UPDATE';
		$logFields = "modified_date='$entered_date', 
		modified_time='$entered_time', 
		modified_by='$operator_id',";
		$where = ' WHERE `id`='.$rxId;
	}
	else{
		$qry = 'INSERT INTO';
		$logFields = "entered_date='$entered_date', 
		entered_time='$entered_time', 
		entered_by='$operator_id',";
	}
	
	$qry .=" in_optical_order_form SET 
		patient_id='".$patient_id."',
		operator_id='".$operator_id."',
		
		physician_id = '".$physicianId."',
		physician_name='".$physicianName."',
		
		sphere_od='".strUp($_POST['od_sphere'])."',
		cyl_od='".strUp($_POST['od_cylinder'])."',
		axis_od='".strUp($_POST['od_axis'])."',
		axis_od_va='".strUp($_POST['od_axis_va'])."',
		add_od='".strUp($_POST['od_add'])."',
		add_od_va='".strUp($_POST['od_add_va'])."',
		
		base_od='".strUp($_POST['od_base'])."',
		dist_pd_od='".strUp($_POST['od_dpd'])."',
		near_pd_od='".strUp($_POST['od_npd'])."',
		
		mr_od_p='".strUp($_POST['mr_od_p'])."',
		mr_od_prism='".strUp($_POST['mr_od_prism'])."',
		mr_od_splash='".strUp($_POST['mr_od_splash'])."',
		mr_od_sel='".strUp($_POST['mr_od_sel'])."',
		
		sphere_os='".strUp($_POST['os_sphere'])."',
		cyl_os='".strUp($_POST['os_cylinder'])."',
		axis_os='".strUp($_POST['os_axis'])."',
		axis_os_va='".strUp($_POST['os_axis_va'])."',
		add_os='".strUp($_POST['os_add'])."',
		add_os_va='".strUp($_POST['os_add_va'])."',
				
		base_os='".strUp($_POST['os_base'])."',
		dist_pd_os='".strUp($_POST['os_dpd'])."',
		near_pd_os='".strUp($_POST['os_npd'])."',
		
		mr_os_p='".strUp($_POST['mr_os_p'])."',
		mr_os_prism='".strUp($_POST['mr_os_prism'])."',
		mr_os_splash='".strUp($_POST['mr_os_splash'])."',
		mr_os_sel='".strUp($_POST['mr_os_sel'])."',
		
		seg_od='".strUp($_POST['od_min_seg'])."',
		seg_os='".strUp($_POST['os_min_seg'])."',
		
		oc_od='".strUp($_POST['od_oc_a'])."',
		oc_os='".strUp($_POST['os_oc_a'])."',
		
		rx_dos='$dos',
		".$logFields."
		outside_rx=1,
		custom_rx=1".$where;
		
		$rs=imw_query($qry) or die(imw_error());
		
		/*if($lastDocId>0){
			 $phyName=$arrUsers[$lastDocId];
		}*/
		$rxBaseDet= $physicianId.'~'.$physicianName.'~'.$patPhone.'~'.$lastExamDate.'~1';//set outsize rx=1 in last parameter
		 
		$rxDetailsOD=$_POST['od_sphere'].'~'.$_POST['od_cylinder'].'~'.$_POST['od_axis'].'~'.$_POST['mr_od_p'].'~'.$_POST['mr_od_prism'].'~'.$_POST['mr_od_splash'].'~'.$_POST['mr_od_sel'].'~'.$_POST['od_npd'].'~'.$_POST['od_dpd'].'~'.$_POST['od_add'].'~'.$_POST['od_base'].'~'.$_POST['od_axis_va'].'~'.$_POST['od_add_va'].'~'.$_POST['od_min_seg'].'~'.$_POST['od_oc_a'];
		 
		$rxDetailsOS=$_POST['os_sphere'].'~'.$_POST['os_cylinder'].'~'.$_POST['os_axis'].'~'.$_POST['mr_os_p'].'~'.$_POST['mr_os_prism'].'~'.$_POST['mr_os_splash'].'~'.$_POST['mr_os_sel'].'~'.$_POST['os_npd'].'~'.$_POST['os_dpd'].'~'.$_POST['os_add'].'~'.$_POST['os_base'].'~'.$_POST['os_axis_va'].'~'.$_POST['os_add_va'].'~'.$_POST['os_min_seg'].'~'.$_POST['os_oc_a'];
		
		$db_dos=explode('-',$dos);
		$db_dos_exp=$db_dos['2'].'-'.$db_dos['0'].'-'.$db_dos['1'];
		//create form for values
		$pref_detail='<input type="hidden" name="lens_rxDOS" id="lens_rxDOS_5200" value="'.$_POST['dos'].'">
      <input type="hidden" name="lens_rxDOSRaw" id="lens_rxDOSRaw_5200" value="'.$db_dos_exp.'">
      <input type="hidden" name="lens_rxBaseDet" id="lens_rxBaseDet5200" value="'.$rxBaseDet.'">
      <input type="hidden" name="lens_rxDetailsOD" id="lens_rxDetailsOD5200" value="'.$rxDetailsOD.'">
      <input type="hidden" name="lens_rxDetailsOS" id="lens_rxDetailsOS5200" value="'.$rxDetailsOS.'">
      <input type="hidden" name="lens_rxDetailsDX" id="lens_rxDetailsDX5200" value="'.$lens_rxDetailsDX.'">';
}

$axisAvArr=array("","20/15","20/20","20/25","20/30","20/40","20/50","20/60","20/70","20/80","20/100","20/150","20/200","20/300","20/400","20/600","20/800","CF","CF 1ft","CF 2ft","CF 3ft","CF 4ft","CF 5ft","CF 6ft","HM","LP","LP c p","LP s p","NLP","F&amp;F","F/(F)","2/200","CSM","Enucleation","Prosthetic","Pt Uncoopera","Unable","5/200");

$addAvArr=array("","20/20(J1+)","20/25(J1)","20/30(J2)","20/40(J3)","20/50(J5)","20/70(J7)","20/80","20/100(J10)","20/200(J16)","20/400","20/800","APC 20/30","APC 20/40","APC 20/60","APC 20/80","APC 20/100","APC 20/160","APC 20/200","CSM","(C)SM","C(S)M","CS(M)","C(S)(M)","(C)(S)M","(C)S(M)","(C)(S)(M)","F&amp;F","Unable");
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->
<?php include_once("../reports/report_includes.php");?>
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->
<script type="text/javascript">
window.opener = window.opener.main_iframe.admin_iframe;
function fillParentForm(sno,custom){
	var rxBaseDet=document.getElementById('lens_rxBaseDet'+sno).value.split('~');
	var rxDetailsOD=document.getElementById('lens_rxDetailsOD'+sno).value.split('~');
	var rxDetailsOS=document.getElementById('lens_rxDetailsOS'+sno).value.split('~');
	var rxDetailsDX=document.getElementById('lens_rxDetailsDX'+sno).value.split('~');
	
	for(var kk=0;kk<=14;kk++){
		if($.trim(rxDetailsOD[kk])!=""){
			rxDetailsOD[kk]=$.trim(rxDetailsOD[kk]);
			if(rxDetailsOD[kk]=="20/"){
				rxDetailsOD[kk]="";
			}
		}
		if($.trim(rxDetailsOS[kk])!=""){
			rxDetailsOS[kk]=$.trim(rxDetailsOS[kk]);
			if(rxDetailsOS[kk]=="20/"){
				rxDetailsOS[kk]="";
			}
		}
	}
	
	// Base
	window.opener.document.getElementById('lens_physician_id_lensD_<?php echo $rxCount; ?>').value=rxBaseDet[0];
	window.opener.document.getElementById('lens_physician_name_lensD_<?php echo $rxCount; ?>').value=rxBaseDet[1];
	window.opener.document.getElementById('lens_telephone_lensD_<?php echo $rxCount; ?>').value=rxBaseDet[2];
	window.opener.document.getElementById('lens_last_exam_<?php echo $rxCount; ?>_lensD').value=rxBaseDet[3];
	window.opener.document.getElementById('isRXLoaded_lensD_<?php echo $rxCount; ?>').value=1;
	
	window.opener.document.getElementById('lens_outside_rx_<?php echo $rxCount; ?>_lensD').value=rxBaseDet[4];
	// OD VALUES
	window.opener.document.getElementById('lens_sphere_od_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[0];
	window.opener.document.getElementById('lens_cylinder_od_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[1];
	window.opener.document.getElementById('lens_axis_od_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[2];
	window.opener.document.getElementById('lens_mr_od_p_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[3];
	window.opener.document.getElementById('lens_mr_od_prism_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[4];
	window.opener.document.getElementById('lens_mr_od_splash_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[5];
	window.opener.document.getElementById('lens_mr_od_sel_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[6];
	
	window.opener.document.getElementById('lens_add_od_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[9];
	window.opener.document.getElementById('lens_axis_od_va_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[11];
	window.opener.document.getElementById('lens_add_od_va_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[12];
	window.opener.document.getElementById('sph_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[0];
	window.opener.document.getElementById('cyl_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[1];
	window.opener.document.getElementById('axis_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[2];
	window.opener.document.getElementById('add_text_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[9];
	
	/*only for custom rx block OD start here*/
	if(custom==2){
	window.opener.document.getElementById('lens_npd_od_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[7];
	window.opener.document.getElementById('lens_dpd_od_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[8];
	window.opener.document.getElementById('lens_base_od_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[10];
	window.opener.document.getElementById('lens_seg_od_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[13];
	window.opener.document.getElementById('lens_oc_od_<?php echo $rxCount; ?>_lensD').value=rxDetailsOD[14];
	}
	/*only for custom rx block OD ends here*/
		
	if(rxDetailsOD[3] || rxDetailsOD[6])
		window.opener.document.getElementById('prism_text_1_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[3]+' '+rxDetailsOD[6];
	else
		window.opener.document.getElementById('prism_text_1_od_<?php echo $rxCount; ?>').innerHTML="";
	if(rxDetailsOD[5] || rxDetailsOD[4])
		window.opener.document.getElementById('prism_text_2_od_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[5]+' '+rxDetailsOD[4];
	else
		window.opener.document.getElementById('prism_text_2_od_<?php echo $rxCount; ?>').innerHTML="";
	
	var seperator1 = (((rxDetailsOD[3] ||rxDetailsOD[4]) && (rxDetailsOD[5] || rxDetailsOD[6]))?"/":"");
	window.opener.document.getElementById('prism_text_od_seperator_<?php echo $rxCount; ?>').innerHTML=seperator1;
	
	
	window.opener.document.getElementById('axis_text_od_va_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[11];
	window.opener.document.getElementById('add_text_od_va_<?php echo $rxCount; ?>').innerHTML=rxDetailsOD[12];
	
	// OS VALUES

	window.opener.document.getElementById('lens_sphere_os_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[0];
	window.opener.document.getElementById('lens_cylinder_os_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[1];
	window.opener.document.getElementById('lens_axis_os_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[2];
	window.opener.document.getElementById('lens_mr_os_p_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[3];
	window.opener.document.getElementById('lens_mr_os_prism_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[4];
	window.opener.document.getElementById('lens_mr_os_splash_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[5];
	window.opener.document.getElementById('lens_mr_os_sel_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[6];
	window.opener.document.getElementById('lens_add_os_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[9];
	window.opener.document.getElementById('lens_axis_os_va_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[11];
	window.opener.document.getElementById('lens_add_os_va_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[12];
	
	window.opener.document.getElementById('sph_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[0];
	window.opener.document.getElementById('cyl_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[1];
	window.opener.document.getElementById('axis_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[2];
	window.opener.document.getElementById('add_text_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[9];
	/*only for custom rx block OS start here*/
	if(custom==2){
	window.opener.document.getElementById('lens_npd_os_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[7];
	window.opener.document.getElementById('lens_dpd_os_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[8];
	window.opener.document.getElementById('lens_base_os_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[10];
	window.opener.document.getElementById('lens_seg_os_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[13];
	window.opener.document.getElementById('lens_oc_os_<?php echo $rxCount; ?>_lensD').value=rxDetailsOS[14];
	}
	/*only for custom rx block OS ends here*/
	
	if(rxDetailsOS[3] || rxDetailsOS[6])
		window.opener.document.getElementById('prism_text_1_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[3]+' '+rxDetailsOS[6];
	else
		window.opener.document.getElementById('prism_text_1_os_<?php echo $rxCount; ?>').innerHTML="";
	if(rxDetailsOS[5] || rxDetailsOS[4])
		window.opener.document.getElementById('prism_text_2_os_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[5]+' '+rxDetailsOS[4];
	else
		window.opener.document.getElementById('prism_text_2_os_<?php echo $rxCount; ?>').innerHTML="";
	
	var seperator2 = (((rxDetailsOS[3] ||rxDetailsOS[4]) && (rxDetailsOS[5] || rxDetailsOS[6]))?"/":"");
	window.opener.document.getElementById('prism_text_os_seperator_<?php echo $rxCount; ?>').innerHTML=seperator2;
	
	
	window.opener.document.getElementById('axis_text_os_va_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[11];
	window.opener.document.getElementById('add_text_os_va_<?php echo $rxCount; ?>').innerHTML=rxDetailsOS[12];
	
	window.opener.set_phone_format(window.opener.document.getElementById('lens_telephone_lensD_<?php echo $rxCount; ?>'),'<?php echo $GLOBALS['phone_format'];?>');
	
	window.opener.document.getElementById('lens_rx_dos_<?php echo $rxCount; ?>_lensD').value = document.getElementById('lens_rxDOSRaw_'+sno).value
	
	prescribedBy = '';
	if(rxBaseDet[1]!=''){
		prescribedBy = ', Prescribed By: '+rxBaseDet[1];
	}
	var rxLabel = window.opener.document.getElementById('rx_label_<?php echo $rxCount; ?>');
	$(rxLabel).attr("title", "Lens Prescription - "+document.getElementById('lens_rxDOS_'+sno).value+prescribedBy);
	
	if(<?php echo $rxCount; ?>==1){
		var phyNameDisp = window.opener.document.getElementById('physicianDisp');
		
		if(rxBaseDet[1]=='')
			$(phyNameDisp).text('Physician: '+window.opener.PCP_DISP).attr('title', window.opener.PCP);
		else
			$(phyNameDisp).text('Physician: '+rxBaseDet[1]);
	}
	
	window.opener.document.getElementById('rx_div_<?php echo $rxCount; ?>').style.display = 'block';
	//window.opener.document.getElementById('arrow_image_<?php echo $rxCount; ?>').style.display = 'none';
	//window.opener.document.getElementById('rx_link_<?php echo $rxCount; ?>').style.display = 'none';
	window.opener.get_rx_dx_code('<?php echo $rxCount; ?>');
	//window.opener.document.getElementById('rxDate_<?php echo $rxCount; ?>').style.display = 'none';
	
	/*Reload Values of Seg Type on the Baisi of Rx Change*/
	var selVision = window.opener.$('#lens_vision_<?php echo $rxCount; ?>_lensD').val();
	if(selVision=='os'){
		var designVal = window.opener.$('#design_id_<?php echo $rxCount; ?>_os_lensD').val();
		window.opener.pos_row_display(<?php echo $rxCount; ?>, '2_<?php echo $rxCount; ?>_design_display', designVal, 'in_lens_design', 'os', false);
		
		window.opener.pos_row_display(<?php echo $rxCount; ?>, '2_<?php echo $rxCount; ?>_diopter_display', '', 'in_lens_diopter', 'os', false);
	}
	else{
		var designVal = window.opener.$('#design_id_<?php echo $rxCount; ?>_od_lensD').val();
		window.opener.pos_row_display(<?php echo $rxCount; ?>, '2_<?php echo $rxCount; ?>_design_display', designVal, 'in_lens_design', 'od', false);
		
		window.opener.pos_row_display(<?php echo $rxCount; ?>, '2_<?php echo $rxCount; ?>_diopter_display', '', 'in_lens_diopter', 'od', false);

		designVal = window.opener.$('#design_id_<?php echo $rxCount; ?>_os_lensD').val();
		window.opener.pos_row_display(<?php echo $rxCount; ?>, '2_<?php echo $rxCount; ?>_design_display', designVal, 'in_lens_design', 'os', false);
		
		window.opener.pos_row_display(<?php echo $rxCount; ?>, '2_<?php echo $rxCount; ?>_diopter_display', '', 'in_lens_diopter', 'os', false);
	}
	
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
</script>
<style type="text/css">
.prismContainer{
	width:40%;
	display:inline-block;
}
.prismContainer:first-child{
	text-align:right;
	margin-right:10px;
}
.prismContainer:last-child{
	text-align:left;
	margin-left:10px;
}
.custom_rx{
	color: #03F;
    font-weight: bold;
    font-size: 12px;
}
#contextMenu{
	position: absolute;
    top: 0;
    left: 0;
    z-index: 999;
    background-color: #CECECE;
    padding: 5px;
    border: 1px solid #000000;
	display:none;
}
#contextMenu span{
	width: 100%;
    font-weight: bold;
    text-decoration: none;
    font-size: 18px;
    color: #0004B3;
    display: block;
	cursor:pointer;
}
.incorrectRx a{cursor: default;}
.incorrectRx, .incorrectRx span{
	text-decoration: line-through;
	color: #F30004;
}
.text_purpule{padding-left:0;}
.editIcon{
	cursor: pointer;
	vertical-align: top;
	display: inline-block;
	margin-left: 2px;
}
.listheading span.alignCenter{display:inline-block;}
.editIcon + a{display:inline-block;text-align:center;}
</style>
</head>
<body>
	<div style="padding:0px; width:100%;">
        <div class="listheading">
            <div class="fl">Lens Prescriptions</div>
            <div class="fl" style="width:auto; color:#03F; text-align:left; margin-left:180px;"><?php echo $patient_name_id;?></div>
			<div class="fr" style="padding-right:10px;display:none;" id="rxDataOptions">
				<label for="previousRx">Copy Rx:</label>
				<select name="previousRx" id="previousRx" onChange="copy_rx();"></select>&nbsp;&nbsp;
				<label for="rx_custom_physician_select">Ref. Physician Name:</label>
				<input type="text" id="rx_custom_physician_select" onChange="rxPhysicianChange();" />
			</div>
        </div>
<?php

if($_POST['save'])
{
	echo $pref_detail;
	echo'<script type="text/javascript">fillParentForm(5200,2);</script>';
}
?>
<?php  if(sizeof($arrLensRX)>0 || imw_num_rows($customRxq)>0) { ?>
<table class="table_collapse" width="99.9.5%" id="rx_listing_head">
    <tr class="listheading">
      <td>
		<span class="alignCenter" style="width:118px;">DOS</span>
	  	<span class="alignCenter" style="width:54px;">Vision</span>
	  	<span class="alignCenter" style="width:59px;">Sphere</span>
	  	<span class="alignCenter" style="width:59px;">Cylinder</span>
	  	<span class="alignCenter" style="width:59px;">Axis</span>
	  	<span class="alignCenter" style="width:59px;">Add</span>
	  	<span class="alignCenter" style="width:138px;">Prism</span>
	  	<span class="alignCenter" style="width:59px;">DPD</span>
	  	<span class="alignCenter" style="width:59px;">NPD</span>
	  	<span class="alignCenter" style="width:59px;">OC</span>
	  	<span class="alignCenter" style="width:92px;">Base Curve</span>
	  	<span class="alignCenter" style="width:92px;">Min Seg Ht</span>
	  	<span class="alignCenter" style="width:92px;">RX By</span>
	  </td>
    </tr>
</table> 
<div style="height:220px; overflow-y:auto" id="rx_listing">
<table class="table_collapse text14" style="width:99.8%">   
<?php
	$j=1;
	$customRx = array();
	$rxList = '<option value="">Please Select</option>';
foreach($arrLensRX1 as $arrLensRX){
	foreach($arrLensRX as $key=>$val){
		$i = $key;
		 $phyName='';
		 $vid='';
		 $vid = $arrLensRX[$i]['OD']['VIS_ID'];
		
		 if($lastDocId>0 && $arrLensRX[$i]['OD']['physician_id']!='0' && $arrLensRX[$i]['OD']['physician_id']!=''){
			 $phyName=$arrUsers[$arrLensRX[$i]['OD']['physician_id']];
			 if(isset($arrLensRX[$i]['rx_id']) && $arrLensRX[$i]['rx_id']!="" && $phyName==''){
				$phyName=(isset($arrLensRX[$i]['OD']['physician_name'])) ? $arrLensRX[$i]['OD']['physician_name'] : '';
			 }
		 }
		 else{
			 $phyName=(isset($arrLensRX[$i]['OD']['physician_name'])) ? $arrLensRX[$i]['OD']['physician_name'] : '';
		 }

		 $rxBaseDet=$arrLensRX[$i]['OD']['physician_id'].'~'.$phyName.'~'.$patPhone.'~'.$lastExamDate.'~'.$arrLensVision[$vid]['outside_rx'];
		 
		 $rxDetailsOD=$arrLensRX[$i]['OD']['Sphere'].'~'.$arrLensRX[$i]['OD']['Cylinder'].'~'.$arrLensRX[$i]['OD']['Axis'].'~'.$arrLensPrism[$i]['OD']['mr_od_p'].'~'.$arrLensPrism[$i]['OD']['mr_od_prism'].'~'.$arrLensPrism[$i]['OD']['mr_od_splash'].'~'.$arrLensPrism[$i]['OD']['mr_od_sel'].'~'.$arrLensVision[$vid]['OD']['NPD'].'~'.$arrLensVision[$vid]['OD']['DPD'].'~'.$arrLensRX[$i]['OD']['Add'].'~'.$arrLensRX[$i]['OD']['Base'].'~'.$arrLensRX[$i]['OD']['Axis_VA'].'~'.$arrLensRX[$i]['OD']['Add_VA'].'~'.$arrLensVision[$vid]['OD']['MINSEG'].'~'.$arrLensVision[$vid]['OD']['OC'];
		 
		 $rxDetailsOS=$arrLensRX[$i]['OS']['Sphere'].'~'.$arrLensRX[$i]['OS']['Cylinder'].'~'.$arrLensRX[$i]['OS']['Axis'].'~'.$arrLensPrism[$i]['OS']['mr_os_p'].'~'.$arrLensPrism[$i]['OS']['mr_os_prism'].'~'.$arrLensPrism[$i]['OS']['mr_os_splash'].'~'.$arrLensPrism[$i]['OS']['mr_os_sel'].'~'.$arrLensVision[$vid]['OS']['NPD'].'~'.$arrLensVision[$vid]['OS']['DPD'].'~'.$arrLensRX[$i]['OS']['Add'].'~'.$arrLensRX[$i]['OS']['Base'].'~'.$arrLensRX[$i]['OS']['Axis_VA'].'~'.$arrLensRX[$i]['OS']['Add_VA'].'~'.$arrLensVision[$vid]['OS']['MINSEG'].'~'.$arrLensVision[$vid]['OS']['OC'];
		 
		 if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
		
		$db_dos=explode('-',$arrLensRX[$i]['OD']['DOS']);
		$db_dos_exp=$db_dos['2'].'-'.$db_dos['0'].'-'.$db_dos['1'];
		$get_dx_qry=imw_query("select patient_charge_list_details.diagnosis_id1,patient_charge_list_details.diagnosis_id2,patient_charge_list_details.diagnosis_id3,
		patient_charge_list_details.diagnosis_id4 from patient_charge_list join patient_charge_list_details 
		on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id 
		where patient_charge_list.patient_id='".$_SESSION['patient_session_id']."' and patient_charge_list_details.procCode in($enc_proc_imp)
		and patient_charge_list.date_of_service='$db_dos_exp'");
		$lens_rxDetailsDX="";
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
		
		$customRxLabel = "";
		$contextMenu = "";
		$incorrect = "";
		$onClick = "javascript:fillParentForm('".$j."',1);";
		if(isset($arrLensRX[$i]['rx_id']) && $arrLensRX[$i]['rx_id']!=""){
			$onClick = "javascript:fillParentForm('".$j."',2);";
			
			$customRxLabel = "Custom Rx";
			
			if($arrLensRX[$i]['incorrect']=="1"){
				$incorrect = "incorrectRx";
				$contextMenu = "";
				$onClick = "javascript:void(0);";
			}
			else{
				$contextMenu = 'oncontextmenu="customMenu(event, \''.$arrLensRX[$i]['rx_id'].'\');"';
				$customRx[$arrLensRX[$i]['rx_id']] = $arrLensRX[$i];
				$customRx[$arrLensRX[$i]['rx_id']]['OD'] = array_merge($customRx[$arrLensRX[$i]['rx_id']]['OD'], $arrLensPrism[$i]['OD']);
				$customRx[$arrLensRX[$i]['rx_id']]['OS'] = array_merge($customRx[$arrLensRX[$i]['rx_id']]['OS'], $arrLensPrism[$i]['OS']);
				
				$customRx[$arrLensRX[$i]['rx_id']]['OD'] = array_merge($customRx[$arrLensRX[$i]['rx_id']]['OD'], $arrLensVision[$vid]['OD']);
				$customRx[$arrLensRX[$i]['rx_id']]['OS'] = array_merge($customRx[$arrLensRX[$i]['rx_id']]['OS'], $arrLensVision[$vid]['OS']);
			}
		}
		
		if($incorrect==''){
			$rxList .= '<option value="'.$j.'">'.$arrLensRX[$i]['OD']['DOS'].' - '.addslashes($phyName).'</option>';
		}
?>
    <tr class="<?php echo $rowbg." ".$incorrect;?> cellBorder" style="height:22px;" <?php echo $contextMenu; ?>>
      <td rowspan="2" class="<?php echo ( $customRxLabel !='' && $incorrect == '' )?'alignLeft':'alignCenter' ?>" style="width:120px">
<?php if( $customRxLabel !='' && $incorrect == '' ): ?>
		<img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/edit_record.png" class="editIcon" onClick="editRx(<?php echo $arrLensRX[$i]['rx_id']; ?>)" />
<?php endif; ?>
		<a href="javascript:void(0);" onClick="<?php echo $onClick; ?>" class="text_purpule"><?php echo $arrLensRX[$i]['OD']['DOS'];?><br />
		<span class="custom_rx"><?php echo $customRxLabel; ?></span>
		</a>
	  </td>
      <td class="alignCenter blueColor" style="width:50px">OD</td>
	  <td class="alignCenter" style="width:60px"><?php echo $arrLensRX[$i]['OD']['Sphere'];?></td>
      <td class="alignCenter" style="width:60px"><?php echo $arrLensRX[$i]['OD']['Cylinder'];?></td>
      <td class="alignCenter" style="width:60px"><?php echo $arrLensRX[$i]['OD']['Axis'];?></td>
      <td class="alignCenter" style="width:60px"><?php echo $arrLensRX[$i]['OD']['Add'];?></td>

<td class="alignCenter" style="width:134px"><?php 
		echo '<span class="prismContainer">';
		
			if($arrLensPrism[$i]['OD']['mr_od_p']!="" && $arrLensPrism[$i]['OD']['mr_od_sel']!="")
			echo $arrLensPrism[$i]['OD']['mr_od_p']." ".$arrLensPrism[$i]['OD']['mr_od_sel'];
			else echo $arrLensPrism[$i]['OD']['mr_od_p'];
			echo '</span>';

			if($arrLensPrism[$i]['OD']['mr_od_splash'] && $arrLensPrism[$i]['OD']['mr_od_prism'])
			echo '/<span class="prismContainer">'. $arrLensPrism[$i]['OD']['mr_od_splash'].'&nbsp'.$arrLensPrism[$i]['OD']['mr_od_prism']."</span>";
			else if($arrLensPrism[$i]['OD']['mr_od_splash'])
			echo '/<span class="prismContainer">'. $arrLensPrism[$i]['OD']['mr_od_splash'].'</span>';
		
		if(empty($arrLensPrism[$i]['OD']['mr_od_p']) && empty($arrLensPrism[$i]['OD']['mr_od_splash']))
		echo '<span class="prismContainer">none</span>';
		?></td>
		
		<td class="alignCenter" style="width:60px"><?php echo $arrLensVision[$vid]['OD']['DPD']; ?></td>
		<td class="alignCenter" style="width:60px"><?php echo $arrLensVision[$vid]['OD']['NPD']; ?></td>
		<td class="alignCenter" style="width:60px"><?php echo $arrLensVision[$vid]['OD']['OC']; ?></td>
		<td class="alignCenter" style="width:90px"><?php echo $arrLensRX[$i]['OD']['Base']; ?></td>
		<td class="alignCenter" style="width:90px"><?php echo $arrLensVision[$vid]['OD']['MINSEG']; ?></td>
		<td class="alignCenter" style="width:90px" rowspan="2"><?php echo $phyName; ?></td>
    </tr>
    <tr class="<?php echo $rowbg." ".$incorrect;?> cellBorder" style="height:22px;" <?php echo $contextMenu; ?>>
      <td class="alignCenter greenColor">OS</td>
	  <td class="alignCenter"><?php echo $arrLensRX[$i]['OS']['Sphere'];?></td>
      <td class="alignCenter"><?php echo $arrLensRX[$i]['OS']['Cylinder'];?></td>
      <td class="alignCenter"><?php echo $arrLensRX[$i]['OS']['Axis'];?></td>
      <td class="alignCenter"><?php echo $arrLensRX[$i]['OS']['Add'];?>
      <input type="hidden" name="lens_rxDOS" id="lens_rxDOS_<?php echo $j;?>" value="<?php echo $arrLensRX[$i]['OD']['DOS1'];?>">
      <input type="hidden" name="lens_rxDOSRaw" id="lens_rxDOSRaw_<?php echo $j;?>" value="<?php echo $db_dos_exp;?>">
      <input type="hidden" name="lens_rxBaseDet" id="lens_rxBaseDet<?php echo $j;?>" value="<?php echo $rxBaseDet;?>">
      <input type="hidden" name="lens_rxDetailsOD" id="lens_rxDetailsOD<?php echo $j;?>" value="<?php echo html_entity_decode($rxDetailsOD, ENT_QUOTES, 'UTF-8'); ?>">	  
      <input type="hidden" name="lens_rxDetailsOS" id="lens_rxDetailsOS<?php echo $j;?>" value="<?php echo html_entity_decode($rxDetailsOS, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="lens_rxDetailsDX" id="lens_rxDetailsDX<?php echo $j;?>" value="<?php echo $lens_rxDetailsDX;?>">
	  </td>
	  <td class="alignCenter"><?php 
		echo '<span class="prismContainer">';
		
			if($arrLensPrism[$i]['OS']['mr_os_p']!="" && $arrLensPrism[$i]['OS']['mr_os_sel']!="")
			echo $arrLensPrism[$i]['OS']['mr_os_p']." ".$arrLensPrism[$i]['OS']['mr_os_sel'];
			else echo $arrLensPrism[$i]['OS']['mr_os_p'];
			echo '</span>';

			if($arrLensPrism[$i]['OS']['mr_os_splash'] && $arrLensPrism[$i]['OS']['mr_os_prism'])
			echo '/<span class="prismContainer">'. $arrLensPrism[$i]['OS']['mr_os_splash'].'&nbsp'.$arrLensPrism[$i]['OS']['mr_os_prism']."</span>";
			else if($arrLensPrism[$i]['OS']['mr_os_splash'])
			echo '/<span class="prismContainer">'. $arrLensPrism[$i]['OS']['mr_os_splash'].'</span>';
		
		if(empty($arrLensPrism[$i]['OS']['mr_os_p']) && empty($arrLensPrism[$i]['OS']['mr_os_splash']))
		echo '<span class="prismContainer">none</span>';
		?></td>
		<td class="alignCenter" style="width:60px"><?php echo $arrLensVision[$vid]['OS']['DPD']; ?></td>
		<td class="alignCenter" style="width:60px"><?php echo $arrLensVision[$vid]['OS']['NPD']; ?></td>
		<td class="alignCenter" style="width:60px"><?php echo $arrLensVision[$vid]['OS']['OC']; ?></td>
		<td class="alignCenter" style="width:90px"><?php echo $arrLensRX[$i]['OS']['Base']; ?></td>
		<td class="alignCenter" style="width:90px"><?php echo $arrLensVision[$vid]['OS']['MINSEG']; ?></td>
    </tr>
    <?php 
	$j++;
	}
}
?>
</table>
</div>
<?php  }else{ echo '<div style="height:220px;" class="alignCenter" id="rx_not_found">No Record Exists.</div>';}?>
<div id="rx_custom" style="height:220px; overflow-y:auto; display:none">
<table class="table_collapse" width="99.9.5%">
    <tr class="listheading">
      <td class="alignCenter" style="width:174px;">DOS</td>
      <td class="alignCenter" style="width:110px;">Vision</td>
      <td class="alignCenter" style="width:156px;">Sphere</td>
      <td class="alignCenter" style="width:156px;">Cylinder</td>
      <td class="alignCenter" style="width:234px;">Axis</td>
      <td class="alignCenter" style="padding-right:15px;">Add</td>
    </tr>
</table>
<form name="custom_rx_form" id="custom_rx_form" method="post" action="" autocomplete="off">
<table class="table_collapse text14" style="width:99.8%">   
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td rowspan="2" class="alignCenter" width="100px"><input type="text" name="dos" id="dos" value="<?php echo date('m-d-Y'); ?>" style="height:21px;width:80%;background-size:21px 21px;"  class="date-pick"></td>
      <td class="alignCenter blueColor" width="50px">OD</td>
	  <td class="alignCenter" width="80px"><input type="text" name="od_sphere" id="od_sphere" value="" style="width:80%"></td>
      <td class="alignCenter" width="80px"><input type="text" name="od_cylinder" id="od_cylinder" value="" style="width:80%"></td>
      <td class="alignCenter" width="120px"><input type="text" name="od_axis" id="od_axis" value="" style="width:40%"> <select name="od_axis_va" id="od_axis_va" style="width:40%">
      <?php 
	  foreach($axisAvArr as $axis)
	  echo"<option value='$axis'>$axis</option>";
	  ?>
      </select></td>
      <td class="alignCenter" width="120px"><input type="text" name="od_add" id="od_add" value="" style="width:40%" onChange="copyVal(this,'os_add')"> <select name="od_add_va" id="od_add_va" style="width:40%">
      <?php 
	  foreach($addAvArr as $add)
	  echo"<option value='$add'>$add</option>";
	  ?>
      </select></td>
    </tr>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td class="alignCenter greenColor">OS</td>
	  <td class="alignCenter" width="80px"><input type="text" name="os_sphere" id="os_sphere" value="" style="width:80%"></td>
      <td class="alignCenter" width="80px"><input type="text" name="os_cylinder" id="os_cylinder" value="" style="width:80%"></td>
      <td class="alignCenter" width="120px"><input type="text" name="os_axis" id="os_axis" value="" style="width:40%"> <select name="os_axis_va" id="os_axis_va" style="width:40%">
      <?php 
	  foreach($axisAvArr as $axis)
	  echo"<option value='$axis'>$axis</option>";
	  ?>
      </select></td>
      <td class="alignCenter" width="120px"><input type="text" name="os_add" id="os_add" value="" style="width:40%"> <select name="os_add_va" id="os_add_va" style="width:40%">
      <?php 
	  foreach($addAvArr as $add)
	  echo"<option value='$add'>$add</option>";
	  ?>
      </select></td>
    </tr>
    <tr>
      <td colspan="6" class="alignCenter" width="100%">
      <table class="table_collapse text14" style="width:99.8%">   
    <tr  class="listheading" style="height:22px;">
      <td class="alignCenter" style="width:79px;">Vision</td>
      <td class="alignCenter" style="width:250px;">Prism</td>
      <td class="alignCenter" style="width:82px;">DPD</td>
      <td class="alignCenter" style="width:82px;">NPD</td>
	  <td class="alignCenter" style="width:82px;">OC</td>
	  <td class="alignCenter" style="width:82px;">Base Curve</td>
	  <td class="alignCenter" style="width:82px;">Min Seg Ht</td>
    </tr>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      
      <td class="alignCenter blueColor">OD</td>
	  <td class="alignCenter">
      <select name="mr_od_p" id="mr_od_p" style="width:20%">
      <option value=""></option>
       <?php echo prismNumbers($lensRes['mr_od_p']); ?>
      </select>
      <select name="mr_od_sel" id="mr_od_sel" style="width:20%">
      <option value=""></option>
      <option value="BI">BI</option>
      <option value="BO">BO</option>
      </select>/<select name="mr_od_splash" id="mr_od_splash" style="width:20%">
      <option value=""></option>
       <?php echo prismNumbers($lensRes['mr_od_splash']); ?>
      </select>
      <select name="mr_od_prism" id="mr_od_prism" style="width:20%">
      <option value=""></option>
      <option value="BD">BD</option>
      <option value="BU">BU</option>
      </select></td>
      <td class="alignCenter"><select name="od_dpd_c" id="od_dpd_c" style="width:70px;display:none;"><option value=""></option><option value="SC">SC</option><option value="CC">CC</option><option value="CL-S">CL-S</option><option value="GPCL">GPCL</option>
</select>
<input type="text" name="od_dpd_a" id="od_dpd_a" value="" style="width:80%"><input type="text" name="od_dpd_b" id="od_dpd_b" value="" style="width:40px;display:none;"></td>
      <td class="alignCenter"><select name="od_npd_c" id="od_npd_c" style="width:70px;display:none;"><option value=""></option><option value="SC">SC</option><option value="CC">CC</option><option value="CL-S">CL-S</option><option value="GPCL">GPCL</option><option value="MV">MV</option>
</select>
<input type="text" name="od_npd_a" id="od_npd_a" value="" style="width:80%"><input type="text" name="od_npd_b" id="od_npd_b" value="" style="width:40px;display:none;">
      </td>
	  <td class="alignCenter"><input type="text" name="od_oc_a" id="od_oc_a" value="" style="width:80%"></td>
	  <td class="alignCenter" style="background-color:#C4C1C1;border-color:#C4C1C1;border-right-color:rgb(158, 150, 150);"><input type="text" name="od_base" id="od_base" value="" style="width:80%"></td>
	  <td class="alignCenter" style="background-color:#C4C1C1;border-color:#C4C1C1;"><input type="text" name="od_min_seg" id="od_min_seg" value="" style="width:80%"  onChange="copyVal(this,'os_min_seg')"></td>
    </tr>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td class="alignCenter greenColor">OS</td>
	  <td class="alignCenter"><select name="mr_os_p" id="mr_os_p" style="width:20%">
      <option value=""></option>
       <?php echo prismNumbers($lensRes['mr_os_p']); ?>
      </select>
      <select name="mr_os_sel" id="mr_os_sel" style="width:20%">
      <option value=""></option>
      <option value="BI">BI</option>
      <option value="BO">BO</option>
      </select>/<select name="mr_os_splash" id="mr_os_splash" style="width:20%">
      <option value=""></option>
       <?php echo prismNumbers($lensRes['mr_os_splash']); ?>
      </select>
      <select name="mr_os_prism" id="mr_os_prism" style="width:20%">
      <option value=""></option>
	  <option value="BD">BD</option>
      <option value="BU">BU</option>
      </select></td>
      <td class="alignCenter"><select name="os_dpd_c" id="os_dpd_c" style="width:70px;display:none;"><option value=""></option><option value="SC">SC</option><option value="CC">CC</option><option value="CL-S">CL-S</option><option value="GPCL">GPCL</option>
</select>
<input type="text" name="os_dpd_a" id="os_dpd_a" value="" style="width:80%"><input type="text" name="os_dpd_b" id="os_dpd_b" value="" style="width:40px;display:none;"></td>
      <td class="alignCenter"><select name="os_npd_c" id="os_npd_c" style="width:70px;display:none;"><option value=""></option><option value="SC">SC</option><option value="CC">CC</option><option value="CL-S">CL-S</option><option value="GPCL">GPCL</option><option value="MV">MV</option>
</select>
<input type="text" name="os_npd_a" id="os_npd_a" value="" style="width:80%"><input type="text" name="os_npd_b" id="os_npd_b" value="" style="width:40px;display:none;"></td>
	<td class="alignCenter"><input type="text" name="os_oc_a" id="os_oc_a" value="" style="width:80%"></td>
	<td class="alignCenter" style="background-color:#C4C1C1;border-color:#C4C1C1;border-right-color:rgb(158, 150, 150);"><input type="text" name="os_base" id="os_base" value="" style="width:80%"></td>
	<td class="alignCenter" style="background-color:#C4C1C1;border-color:#C4C1C1;"><input type="text" name="os_min_seg" id="os_min_seg" value="" style="width:80%"></td>
    </tr>
</table>
      </td>
      </tr>
</table>
<input type="hidden" name="save" id="save" value="Save">
<input type="hidden" name="rx_custom_physician_name" id="rx_custom_physician_name" value="" />
<input type="hidden" name="rx_custom_physician_id" id="rx_custom_physician_id" value="" />
<input type="hidden" name="rx_custom_id" id="rx_custom_id" />
</form>
</div>
	<div class="btn_cls mt10" style="width: 95%; float: left;">
      <div id="rx_listing_btn">
      <input type="button" name="Cancel_lst" value="Close" onClick="javascript:window.close();"/>
      <input type="button" name="Custom_lst" value="Custom Rx" onClick="custom('form');"/>
      </div>
      
      <div id="rx_custom_btn" style="display:none">
      <input type="button" name="close" value="Close" onClick="javascript:window.close();"/>
      <input type="button" name="cancel" value="Cancel" onClick="custom('list');"/>
      <input type="submit" name="save" id="save" value="Save" onClick="save_cRx();" > 
      </div>
      <script type="text/javascript">
	  function save_cRx()
	  {
		  
		if(!$("#od_sphere").val() && !$("#od_cylinder").val() && !$("#od_axis").val() && !$("#od_add").val() && !$("#od_base").val() && !$("#mr_od_p").val() && !$("#mr_od_prism").val() && !$("#mr_od_splash").val() && !$("#mr_od_sel").val() && !$("#os_sphere").val() && !$("#os_cylinder").val() && !$("#os_axis").val() && !$("#os_add").val() && !$("#os_base").val() && !$("#mr_os_p").val() && !$("#mr_os_prism").val() && !$("#mr_os_splash").val() && !$("#mr_os_sel").val())
		{
			falert('Please enter values');
			return false;
		}
		else
		$("#custom_rx_form").submit();	  
	}
function custom(typ){
	if(typ=='form'){
		
		$('#rx_custom').show();
		$('#rx_custom_btn').show();
		$('#rxDataOptions').show();
		// $('#rx_custom_physician_select').show();
		$('#rx_listing').hide();
		$('#rx_listing_head').hide();
		$('#rx_listing_btn').hide();
		$('#rx_not_found').hide();
	}
	else{
		
		$('#custom_rx_form')[0].reset();
		$('#previousRx').val('');
		$('#rx_custom_physician_name').val('');
		$('#rx_custom_physician_id').val('');
		$('#rx_custom_id').val('');
		$('#rx_custom_physician_select').val('');
		
		$('#dos').val('<?php echo date('m-d-Y'); ?>');
		$('#rx_custom').hide();
		$('#rx_custom_btn').hide();
		$('#rxDataOptions').hide();
		// $('#rx_custom_physician_select').hide();
		$('#rx_listing').show();
		$('#rx_listing_head').show();
		$('#rx_listing_btn').show();
		$('#rx_not_found').show();	  	  
	}
}

document.addEventListener("click", function(e) {
	var context_menu = $("#contextMenu");
	$(context_menu).hide();
	$(context_menu).children("#incorrect_rx_id").val('');
});

function customMenu(e, rxId){
	e.preventDefault();
	var left = e.clientX;
	if(left>640){left=640;}
	var top = e.clientY;
	var context_menu = $("#contextMenu");
	$(context_menu).css({'top':top+'px', 'left':left+'px'});
	$(context_menu).children("#incorrect_rx_id").val(rxId);
	$(context_menu).show();
}
function incorrectRx(){
	var rxId = $("#incorrect_rx_id").val();
	if(rxId!=""){
		
		$.ajax({
			url: 'ajax.php',
			data: 'type=markIncorrectRx&rxId='+rxId,
			method: 'POST',
			success: function(resp){
				//console.log(resp);
				location.reload();
			}
		});
	}
}
function rxPhysicianChange(){
	var value	= $('#rx_custom_physician_select').val();
	$('#rx_custom_physician_name').val(value);
}
/*Typeahead fot Physician*/
$("#rx_custom_physician_select").ajaxTypeahead({
	url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
	type: 'refPhysicianName',
	hidIDelem: document.getElementById('rx_custom_physician_id'),
	showAjaxVals: 'phyName'
});
var customRx = <?php echo json_encode($customRx); ?>;
function editRx(rxId){
	
	if( typeof(customRx[rxId]) != 'undefined' ){
		
		var od = customRx[rxId].OD;
		var os = customRx[rxId].OS;
		
		$('#rx_custom_id').val(rxId);
		$('#dos').val(od.DOS);
		$('#rx_custom_physician_select').val(od.physician_name);
		$('#rx_custom_physician_name').val(od.physician_name);
		$('#rx_custom_physician_id').val(od.physician_id);
		
		$('#od_sphere').val(od.Sphere);
		$('#od_cylinder').val(od.Cylinder);
		$('#od_axis').val(od.Axis);
		$('#od_axis_va').val(od.Axis_VA);
		$('#od_add').val(od.Add);
		$('#od_add_va').val(od.Add_VA);
		
		$('#mr_od_p').val(od.mr_od_p);
		$('#mr_od_prism').val(od.mr_od_prism);
		$('#mr_od_splash').val(od.mr_od_splash);
		$('#mr_od_sel').val(od.mr_od_sel);
		
		$('#od_dpd_a').val(od.DPD);
		$('#od_npd_a').val(od.NPD);
		$('#od_oc_a').val(od.OC);
		$('#od_base').val(od.Base);
		$('#od_min_seg').val(od.MINSEG);
		
		$('#os_sphere').val(os.Sphere);
		$('#os_cylinder').val(os.Cylinder);
		$('#os_axis').val(os.Axis);
		$('#os_axis_va').val(os.Axis_VA);
		$('#os_add').val(os.Add);
		$('#os_add_va').val(os.Add_VA);
		
		$('#mr_os_p').val(os.mr_os_p);
		$('#mr_os_prism').val(os.mr_os_prism);
		$('#mr_os_splash').val(os.mr_os_splash);
		$('#mr_os_sel').val(os.mr_os_sel);
		
		$('#os_dpd_a').val(os.DPD);
		$('#os_npd_a').val(os.NPD);
		$('#os_oc_a').val(os.OC);
		$('#os_base').val(os.Base);
		$('#os_min_seg').val(os.MINSEG);
		
		custom('form');
	}
}

function copy_rx(){
	
	var sno = parseInt($('#previousRx').val());
	
	if(sno==0){
		return;
	}
	
	var rxDetailsOD=document.getElementById('lens_rxDetailsOD'+sno).value.split('~');
	var rxDetailsOS=document.getElementById('lens_rxDetailsOS'+sno).value.split('~');
	
	
	$('#od_sphere').val(rxDetailsOD[0]);
	$('#od_cylinder').val(rxDetailsOD[1]);
	$('#od_axis').val(rxDetailsOD[2]);
	
	$('#od_axis_va').val(rxDetailsOD[11]);
	$('#od_add').val(rxDetailsOD[9]);
	$('#od_add_va').val(rxDetailsOD[12]);
	
	$('#mr_od_p').val(rxDetailsOD[3]);
	$('#mr_od_prism').val(rxDetailsOD[4]);
	$('#mr_od_splash').val(rxDetailsOD[5]);
	$('#mr_od_sel').val(rxDetailsOD[6]);
	
	$('#od_dpd_a').val(rxDetailsOD[8]);
	$('#od_npd_a').val(rxDetailsOD[7]);
	$('#od_oc_a').val(rxDetailsOD[14]);
	$('#od_base').val(rxDetailsOD[10]);
	$('#od_min_seg').val(rxDetailsOD[13]);
	
	$('#os_sphere').val(rxDetailsOS[0]);
	$('#os_cylinder').val(rxDetailsOS[1]);
	$('#os_axis').val(rxDetailsOS[2]);
	
	$('#os_axis_va').val(rxDetailsOS[11]);
	$('#os_add').val(rxDetailsOS[9]);
	$('#os_add_va').val(rxDetailsOS[12]);
	
	$('#mr_os_p').val(rxDetailsOS[3]);
	$('#mr_os_prism').val(rxDetailsOS[4]);
	$('#mr_os_splash').val(rxDetailsOS[5]);
	$('#mr_os_sel').val(rxDetailsOS[6]);
	
	$('#os_dpd_a').val(rxDetailsOS[8]);
	$('#os_npd_a').val(rxDetailsOS[7]);
	$('#os_oc_a').val(rxDetailsOS[14]);
	$('#os_base').val(rxDetailsOS[10]);
	$('#os_min_seg').val(rxDetailsOS[13]);
}

function copyVal(obj, target)
{
	var targetObj=document.getElementById(target);
	if(obj.value!='' && targetObj.value=='')
		targetObj.value=obj.value;
}
		  
jQ(document).ready(function(){
	jQ( ".date-pick" ).datepicker({
		changeMonth: true,changeYear: true,
		dateFormat: 'mm-dd-yy',
		onSelect: function() {
		$(this).change();
		}
	});
	
	$('#previousRx').html('<?php echo $rxList; ?>');
});
</script>
    </div>
 </div>
<div id="contextMenu">
	<span onClick="incorrectRx();">Mark Incorrect Rx</span>
	<input type="hidden" id="incorrect_rx_id" />
</div>
</body>
</html>