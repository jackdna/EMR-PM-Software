<?php
require_once("../../../config/globals.php");	
if(isset($_POST['ajaxReq']) && $_POST['ajaxReq'] == "report_data") {
	$edit_id = $_POST['edit_id'];
	$temp_name = addslashes($_POST['templateName']);
	$form_data = $_POST['form_data'];
	$template_fields =  serialize($form_data);
	
	if($edit_id==''){
		$res_check = imw_query("Select * from custom_reports where template_name='".$temp_name."' and delete_status='0' and report_type = 'practice_analytic'");
		if(imw_num_rows($res_check)>0){
			echo "Please Enter Unique Template Name";
			exit;
		}
	}
	if($edit_id==''){
		$sql = "INSERT INTO `custom_reports` SET `template_name`='".$temp_name."', `template_fields`='".$template_fields."', `report_type` = 'practice_analytic'";
	} else {
		$sql = "update `custom_reports` set `template_name`='".$temp_name."', `template_fields`='".$template_fields."' where `id`='$edit_id'";
	}
	$res = imw_query($sql);
	if($res){
		echo 'Record Saved Successfully.';
	}else{
		echo 'Record Saving failed.';//.imw_error()."\n".$q;
	}
	exit;
}

if(isset($_POST['ajaxReq']) && $_POST['ajaxReq'] == "del_template") {
	$del_id = $_POST['del_id'];
	if($del_id){
		$sql = "update `custom_reports` set `delete_status`='1' where `report_type` = 'practice_analytic' and `id`='$del_id'";
	}
	$res = imw_query($sql);
	if($res){
		echo 'Record Deleted Successfully.';
	}else{
		echo 'Record deletion failed.';//.imw_error()."\n".$q;
	}
	exit;
}
?>