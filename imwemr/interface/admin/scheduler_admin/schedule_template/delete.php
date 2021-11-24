<?php                                                  
require_once("../../../../config/globals.php");
if(isset($_REQUEST['temp_id'])){
	$temp_ids = explode(',',$_REQUEST['temp_id']);	
}
$del_Reason=$_GET['del_reason'];
if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
	if($_REQUEST['action']=='delete')
	{
			foreach($temp_ids as $key => $val)
			{
				//get name of schedule template
				$q=imw_query("select schedule_name from schedule_templates WHERE id=".$val);
				$d=imw_fetch_object($q);
				$template_name=imw_real_escape_string($d->schedule_name);      
				//archive template
				$vquery_cdel4 = "UPDATE schedule_templates set del_status='".$_SESSION['authUserID']."::".date('Y-m-d H:i A')."::DelReason:".$del_Reason."' where id=".$val;         imw_query($vquery_cdel4);
				//archive template labels
				$vquery_cdel4 = "UPDATE schedule_label_tbl set del_status='".$_SESSION['authUserID']."::".date('Y-m-d H:i A')."::DelReason:".$del_Reason."'  where sch_template_id =".$val;
				imw_query($vquery_cdel4);  
				//archive child template
				$q_child=imw_query("select id from schedule_templates where parent_id=".$val);
				while($d_child=imw_fetch_object($q_child))
				{         
					//archive child template labels
					$vquery_cdel4 = "UPDATE schedule_label_tbl set del_status='".$_SESSION['authUserID']."::".date('Y-m-d H:i A')."::DelReason:".$del_Reason."'  where sch_template_id =".$d_child->id;
					imw_query($vquery_cdel4);  
				}
				//archive child template
				$vquery_cdel4 = "UPDATE schedule_templates set del_status='".$_SESSION['authUserID']."::".date('Y-m-d H:i A')."::DelReason:".$del_Reason."' where parent_id=".$val;
				imw_query($vquery_cdel4);

				tmp_log('Deleted', "Template deleted", $val, $template_name);
			}
	}elseif($_REQUEST['action']=='restore')
	{
		      
		//restore template
		$vquery_cdel4 = "UPDATE schedule_templates set del_status='' where id=".$_REQUEST['temp_id'];         
		imw_query($vquery_cdel4);
		//restore template labels
		$vquery_cdel4 = "UPDATE schedule_label_tbl set del_status='' where sch_template_id =".$_REQUEST['temp_id'];
		imw_query($vquery_cdel4);  
	}
    echo "success";
}else{
	echo "move";
}

//function to keep log of added and deleted provider schedules
function tmp_log($act_typ, $summary, $template_id, $template_name)
{
	$act_typ="Template/$act_typ";
	imw_query("insert into schedule_template_log set `act_typ` = '$act_typ',
				`summary` = '$summary',
				`template_id` = '$template_id',
				`template_name` = '$template_name',
				`act_datetime` = '".date('Y-m-d H:i:s')."',
				`logged_user_id` = '$_SESSION[authUserID]',
				`logged_user_name` = '$_SESSION[authProviderName]',
				`logged_facility` = '$_SESSION[login_facility]',
				`ip` = '$_SERVER[REMOTE_ADDR]'");//or die(imw_error().' unable to log action');
}


?>