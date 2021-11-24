<?php
/*
	File: lensFrameAction.php
	Coded in PHP7
	Purpose: Action script for Frame & Lens Order
	Access Type: Direct access
*/

require_once(dirname(__FILE__)."/../../config/config.php"); 
require_once(dirname(__FILE__)."/../../library/classes/functions.php"); 	
require_once(dirname(__FILE__)."/../../library/classes/common_functions.php"); 

$patient_id=$_SESSION['patient_session_id'];
$order_id=$_SESSION['order_id'];

$pt_wear_pic=$_REQUEST['pt_wear_pic_1'];
$order_detail_id=$_REQUEST['order_detail_id'];

$action=$_REQUEST['frm_method'];
$action1 = $action;
$action = ($action=="auto_save")?'save':$action;
$frame_order_detail_id=$_REQUEST['order_detail_id_1'];

$postData = $_POST;

if($action!="previous" && $action!="new_form" && ($action=="save" || $action =="order_post" || $action =="dispensed_post" || $action=="VisionWeb")){
	
	if($action =="dispensed_post"){ /*Dispensed Post*/
		foreach($_POST as $key=>$dt){
			if(strpos($key, "pos_")!== false && strpos($key, "pos_")=="0"){
				$key1= str_replace("pos_", "", $key);
				$_POST[$key1] = $dt;
				unset($_POST[$key]);
			}
		}
	}
	
	$tempAction = $action;
	$action = ( $action=="order_post" || $action=="dispensed_post" ) ? 'save' : $action;
	
	/*Submit Frame Data*/
		other_order_action($action,$_POST);
	/*End Submit Frame Data*/
	
/*Submit Lens Data*/
	$postData = $_POST;	
	foreach($postData as $key=>$dt){
		if(strpos($key, "_lensD")!= false){
			$key1= trim($key);
			$key1= str_replace("_lensD","",$key1);
			$postData[$key1] = $dt;
			unset($postData[$key]);
		}
	}
	
	$remKeys = array('manufacturer_id', 'brand_id'); /*Remove elements from frame Section*/
	foreach($remKeys as $key){
		$remVals = preg_grep("/\b".$key."/", array_keys($postData));
		foreach($remVals as $remVal){
			unset($postData[$remVal]);
		}
	}
	
	$postData['page_name'] = "lens_selection";
	$_POST = $postData;
	other_order_action($action,$postData);
	
	if ( $tempAction == 'dispensed_post' || $tempAction == 'order_post' )
	{
		$order_id = $_SESSION['order_id'];
		post_charges($order_id);
		$action = $tempAction;
	}
/*End Submit Lens Data*/
}
if($action =="reorder" || $action =="remake"){
	make_reorder($order_id, array(), $action);
}
if($action=="order_post" || $action =="dispensed_post"){
	echo "<script type='text/javascript'>top.WindowDialog.closeAll();</script>";
	echo "<script type='text/javascript'>window.location.href='pt_frame_selection_1.php?frm_method=".$action."&frm_method_lens=".$action."'</script>";
}
elseif($action=="next"){
	echo "<script type='text/javascript'>window.location.href='pt_frame_selection_1.php'</script>";
}
elseif($action1=="auto_save"){
	echo "<script type='text/javascript'>top.page_link(top.link_selected, false);</script>";
}
elseif($action=="save" || $action=="dispensed_post" || $action=="reorder" || $action=="remake" || $action=="VisionWeb"){
	echo "<script type='text/javascript'>window.location.href='pt_frame_selection_1.php?frm_method=".$action."&frm_method_lens=".$action."'</script>";
}
elseif($action=="previous"){
	echo "<script type='text/javascript'>window.location.href='pt_picture.php'</script>";
}
elseif($action=="cancel" || $action=="new_form"){
	echo "<script type='text/javascript'>window.location.href='pt_frame_selection_1.php?frm_method=".$action."&frm_method_lens=".$action."'</script>";
}
elseif($action=='order_post_unchanged'){
	$order_id = $_SESSION['order_id'];
	post_charges($order_id);
	
	echo "<script type='text/javascript'>top.WindowDialog.closeAll();</script>";
	echo "<script type='text/javascript'>window.location.href='pt_frame_selection_1.php?frm_method=order_post&frm_method_lens=order_post';</script>";
}
?>