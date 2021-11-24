<?php 
require_once("../../../../config/globals.php");
$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'heard_options';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';

$table	= "reason_list";
$pkId	= "reason_id";
$chkFieldAlreadyExist="reason_name";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		imw_query("update chain_event set del_status=1,
					del_date='".date('Y-m-d')."',
					del_time='".date('H:i:s')."',
					del_by='$_SESSION[authId]'
					WHERE event_id IN (".$id.")");
		$res=imw_query("update chain_event_detail set del_status=1 WHERE event_id IN (".$id.")");
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save':
		if($_POST['event_id'])
		{
			$event_id=$_POST['event_id'];
			$consolidation_time=$_POST['consolidation_time'];
			//update event name 
			imw_query("update chain_event set event_name='".imw_real_escape_string($_POST['event_name'])."',
			modified_date='".date('Y-m-d')."',
			modified_time='".date('H:i:s')."',
			modified_by='$_SESSION[authId]',
			master_setting='".$_POST[master_setting]."'
			 where event_id=$event_id");
			$i=0;					
			//get existing detail ids
			$q=imw_query("select event_det_id from chain_event_detail where event_id='$event_id'")or die(imw_error());
			while($d=imw_fetch_object($q))
			{
				//if($_POST['procedure_'.$i]){
					//save event detail.
					imw_query("update chain_event_detail set procedure_id='".$_POST['procedure_'.$i]."',
					provider_id='".$_POST['provider_'.$i]."',
					master_setting='".$_POST[master_setting]."',
					consolidation_time='".$consolidation_time."'
					where event_det_id='".$d->event_det_id."'");
				//}
				$i++;
			}	
			echo 'Record updated successfully.';	
		}
		else
		{
			//save event name 
			imw_query("insert into chain_event set event_name='".imw_real_escape_string($_POST['event_name'])."',
			entered_date='".date('Y-m-d')."',
			entered_time='".date('H:i:s')."',
			master_setting='".$_POST[master_setting]."',
			entered_by='$_SESSION[authId]'");
			$event_id=imw_insert_id();
			for($i=0;$i<=4;$i++)
			{
				//if($_POST['procedure_'.$i]){
					//save event detail.
					imw_query("insert into chain_event_detail set event_id='$event_id',
					procedure_id='".$_POST['procedure_'.$i]."',
					provider_id='".$_POST['provider_'.$i]."',
					master_setting='".$_POST[master_setting]."',
					consolidation_time='".$consolidation_time."'");
				//}
			}
			echo 'Record saved successfully.';
		}
		break;
	case 'show_list':
		$q = "SELECT event_id,event_name FROM chain_event where del_status=0 order by event_name";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
				
			}
		}
		echo json_encode(array('records'=>$rs_set));
		break;
	case 'item_detail':
		$q = "SELECT procedure_id, provider_id, consolidation_time FROM chain_event_detail where event_id='$_REQUEST[id]' and del_status=0";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$consolidation_time=($rs['consolidation_time'])?$rs['consolidation_time']:1;
				unset($rs['consolidation_time']);
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set,'consolidation_time'=>$consolidation_time));
		break;
	case 'master_setting':
		if($_POST['setting']==1 || $_POST['setting']==2 || $_POST['setting']==3)
		{
			imw_query("update chain_event set master_setting='".$_POST['setting']."'");
			imw_query("update chain_event_detail set master_setting='".$_POST['setting']."'");
			echo 1;
		}
		break;
	default: 
}

?>