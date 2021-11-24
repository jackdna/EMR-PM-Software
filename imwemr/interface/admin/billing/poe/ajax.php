<?php
set_time_limit(600);
require_once("../../../../config/globals.php");

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize arbitrary values - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'poe_name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix  */
$table	= "poe_messages";

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "update ".$table." set poe_status='1' WHERE poe_messages_id IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST['poe_messages_id'];
		$poe_name=$_POST['poe_name'];
		$_POST['operator_id']=$_SESSION["authId"];
		$_POST['poe_modified_date']='NOW()';
		unset($_POST['poe_messages_id']);
		unset($_POST['task']);
		if(!$_POST['poe_scheduler']){$_POST['poe_scheduler']=0;}
		if(!$_POST['poe_medical']){$_POST['poe_medical']=0;}
		if(!$_POST['poe_billing']){$_POST['poe_billing']=0;}
				$query_part = "";
				foreach($_POST as $k=>$v){
					if($v=="NOW()"){
						$query_part .= $k."=".addslashes($v).", ";
					}else{
						$query_part .= $k."='".addslashes($v)."', ";
					}
					
				}
				$query_part = substr($query_part,0,-2);
				if($id==''){
					if(trim($poe_name)){
						$q_c="SELECT poe_messages_id from ".$table." WHERE poe_name='".$poe_name."' and poe_status='0'";
						$r_c=imw_query($q_c);
						if(imw_num_rows($r_c)==0){
							$q = "INSERT INTO ".$table." SET ".$query_part;
						}else{
							echo 'enter_unique_POE';exit;;
						}
					}else{echo 'enter_unique_POE';exit;}
				}else{
					$q = "UPDATE ".$table." SET ".$query_part." WHERE poe_messages_id='".$id."'";
				}
				$res = imw_query($q);
				if($res){
					echo 'Record Saved Successfully.';
				}else{
					echo 'Record Saving failed.';//.imw_error()."\n".$q;
				}
			
		break;
	case 'show_list':
		$q = "SELECT poe_messages_id,poe_name,if(poe_days!= '0', poe_days, poe_other_days) as poe_days,poe_pat_message, trim(concat(if(poe_scheduler!= '0', concat('Scheduler, '), ''),if(poe_medical!= '0', concat('Medical, '), ''),if(poe_billing!= '0', concat('Billing'), ''))) as poe_alert,poe_scheduler,poe_medical,poe_billing,poe_other_days FROM ".$table." where poe_status='0' ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set));
		break;
	default: 
}
?>