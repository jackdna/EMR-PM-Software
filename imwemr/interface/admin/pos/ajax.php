<?php

set_time_limit(600);
require_once("../../../config/globals.php");
require_once('../../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$s		= isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : '';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$p		= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
$f		= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';

						
switch($task){
	case 'delete_merchant':
		$ids = trim($_POST['merchant_ids']);
		$q 		= "update tsys_merchant SET merchant_status=1 WHERE id IN ($ids) ";
        $res 	= imw_query($q);
        if($res){
            echo 'Record deleted successfully.';
        }else{
            echo 'Record deleting failed.';
        }
		break;
              
	case 'delete':
		$ids = trim($_POST['device_ids']);
		$q 		= "update tsys_device_details SET device_status=1 WHERE id IN ($ids) ";
        $res 	= imw_query($q);
        if($res){
            echo 'Record deleted successfully.';
        }else{
            echo 'Record deleting failed.';
        }
		break;
              
	case 'save_update':
        $id = $_POST['id'];
		unset($_POST['id']);
		unset($_POST['task']);
		$query_part = "";
		
        foreach($_POST as $k=>$v){
			$query_part.= $k."='".trim($v)."', ";
		}
		$query_part.= " developerID='003066', applicationID='B900', ";
        $query_part = substr($query_part,0,-2);
        
        if($id==''){
			$q = "INSERT INTO tsys_device_details SET ".$query_part;
		}else{
			$q = "UPDATE tsys_device_details SET ".$query_part." WHERE id='".$id."'";
        }
        $res = imw_query($q);
		if($id==''){
			$id  =imw_insert_id();
		}
        
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.';//.imw_error()."\n".$q;
		}
		break; 
	case 'save_update_merchant':
        $id = $_POST['mrchnt_id'];
		unset($_POST['mrchnt_id']);
		unset($_POST['task']);
		$query_part = "";
		
        foreach($_POST as $k=>$v){
			$query_part.= $k."='".trim($v)."', ";
		}
        $query_part = substr($query_part,0,-2);
        
        if($id==''){
			$q = "INSERT INTO tsys_merchant SET ".$query_part;
		}else{
			$q = "UPDATE tsys_merchant SET ".$query_part." WHERE id='".$id."'";
        }
        $res = imw_query($q);
		if($id==''){
			$id  =imw_insert_id();
		}
        
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.';//.imw_error()."\n".$q;
		}
		break; 
	case 'show_devices_list':
        /* Get Task Manager Categories and rules */
        $merchant = array();
        $sql = "SELECT * FROM tsys_merchant where merchant_status=0 ";
        $resp = imw_query($sql);
        if ($resp && imw_num_rows($resp) > 0) {
            while ($row = imw_fetch_assoc($resp)) {
                $merchant[$row['id']] = $row['merchantName'];
            }
        }
        
        $facilityArr = array();
        $sql1 = "select id, name from `facility` order by `name`";
        $resp1 = imw_query($sql1);
        $facility_option = "";
        if ($resp1 && imw_num_rows($resp1) > 0) {
            while ($row1 = imw_fetch_assoc($resp1)) {
                $facilityArr[$row1['id']] = $row1['name'];
            }
        }
        
        $devices = array();
        $devices_sql = "SELECT TDD.*, TM.userID, TM.mid_paswrd, TM.mid FROM tsys_device_details TDD JOIN tsys_merchant TM ON TDD.merchant_id = TM.id where device_status=0 ";

        $devices_rs = imw_query($devices_sql);
        if ($devices_rs && imw_num_rows($devices_rs) > 0) {
            while ($devices_row = imw_fetch_assoc($devices_rs)) {
							$ipAddress=$devices_row['ipAddress'];
        			$port=$devices_row['port'];
        			$device_url=$phpHTTPProtocol.$ipAddress.':'.$port;
							$devices_row['SETVAR'] = array('m_user' => $devices_row['userID'],
																						 'm_pass' => $devices_row['mid_paswrd'],
																						 'mid' => $devices_row['mid'],
																						 'did' => $devices_row['deviceID'],
																						 'd_url' => $device_url);
								unset($devices_row['userID']);
								unset($devices_row['mid_paswrd']);
								unset($devices_row['mid']);
                $devices[] = $devices_row;
            }
        }
		
		echo json_encode(array('records'=>$devices,'merchant'=>$merchant,'facilityArr'=>$facilityArr));
		break;
        
	case 'show_merchant_list':
        /* Get Task Manager Categories and rules */
        $merchant = array();
        $sql = "SELECT * FROM tsys_merchant where merchant_status=0 ";

        $sql_rs = imw_query($sql);
        if ($sql_rs && imw_num_rows($sql_rs) > 0) {
            while ($merchant_row = imw_fetch_assoc($sql_rs)) {
                $merchant[] = $merchant_row;
            }
        }
		
		echo json_encode(array('merchant'=>$merchant));
		break; 
}

?>