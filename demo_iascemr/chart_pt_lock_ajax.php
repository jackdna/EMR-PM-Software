<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
session_start();
include_once("common/conDb.php");
/*include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
*/
$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$chart_login_lock_user_id=trim($_REQUEST['chart_login_lock_user']);
$chart_login_pass=trim(addslashes($_REQUEST['login_pass']));
$chart_lock_id=trim($_REQUEST['chart_lock_id']);	
$patient_id=$_REQUEST['patient_id'];
$conf_id=$_REQUEST['conf_id'];
$form_name=$_REQUEST['form_name'];
$login_user=$_REQUEST['login_user'];
/*$fp = fopen('data.txt', 'w');
fwrite($fp, implode(",",$_REQUEST));
fclose($fp);*/

switch($task){
	case 'login':
	$sucess=0;
	if($chart_login_lock_user_id && $chart_login_pass){
		$qry_login="SELECT usersId from users where usersId='".$chart_login_lock_user_id."' and user_password=PASSWORD('".$chart_login_pass."')";
		$res_login=imw_query($qry_login)or die(imw_error());
		if(imw_num_rows($res_login)>0){
			$sucess=1;
			$del_status=chart_lock_del($chart_login_lock_user_id,$conf_id,$form_name);
			if($del_status){
				chart_lock_insert($patient_id,$conf_id,$form_name,$login_user);
			}
			
		}
	}
	echo $sucess;
	break;
	case 'delete_log':
		$del_status=chart_lock_del($login_user,$conf_id,$form_name);
	default: 
}
function chart_lock_del($user_id,$conf_id,$form_name){
	$return=0;
	if($user_id && $conf_id && $form_name){
		$qry_del_chart="DELETE FROM chart_pt_lock_tbl WHERE user_id='".$user_id."' and confirmation_id='".$conf_id."' and form_name='".$form_name."'";
		$res_del_chart=imw_query($qry_del_chart);
		$return=1;
	}
	return $return;
}
function chart_lock_insert($patient_id,$pConfId,$form_name,$login_user){
	unset($arrayChartLockRecord);
	$arrayChartLockRecord['user_id'] 			= $_SESSION['loginUserId'];
	$arrayChartLockRecord['patient_id'] 		= $patient_id;
	$arrayChartLockRecord['confirmation_id'] 	= $pConfId;
	$arrayChartLockRecord['form_name'] 			= $form_name;
	$arrayChartLockRecord['action_date_time'] 	= date('Y-m-d H:i:s');
	$arrayChartLockRecord['sess_id'] 			= session_id();
	$q_insert="INSERT INTO chart_pt_lock_tbl set user_id='".$login_user."',patient_id='".$patient_id."',confirmation_id='".$pConfId."',form_name='".$form_name."',action_date_time='".date('Y-m-d H:i:s')."',sess_id='". session_id()."'";
	$r_insert=imw_query($q_insert)or die(imw_error());
	//$objManageData->addRecords($arrayChartLockRecord, 'chart_pt_lock_tbl');		
}
?>