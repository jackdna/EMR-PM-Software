<?php
/*
File: ajax.php
Coded in PHP7
Purpose: Show Item Information
Access Type: Include file
*/
require_once("../../config/config.php");
function getDateFormatLocal($date)
{
	$mdate = array();
	$mdate = explode("-",$date);
	$year_val=$mdate[0];
	$month_val=$mdate[1];
	$date_val=$mdate[2];		
	return $month_val."-".$date_val."-".$year_val;
}
if($_REQUEST['action']!="" && $_REQUEST['action']=="load")
{
	$where="";
	if($_REQUEST['disp_note']=='All')$where="and (user_id = '".trim($_SESSION['authId'])."' OR patient_id='".$_SESSION['patient_session_id']."')";
	elseif($_REQUEST['disp_note']=='Mine') $where="  and user_id = '".trim($_SESSION['authId'])."' and patient_id=''";
	else $where=" and patient_id='".$_SESSION['patient_session_id']."'";
		
	$qry = imw_query("select * from in_user_notes where status=1 $where order by id desc");
	$nums = imw_num_rows($qry);
	if($nums > 0)
	{
		echo $nums.'~::~';
		//echo'<table class="table_collapse">';
		while($row = imw_fetch_object($qry))
		{
			list($dt,$tm)=explode(' ',$row->dated);
			$dt=getDateFormatLocal($dt);
			$pt_row="";
			if($row->patient_id){$pt_row=$row->patient_name;}
			$pt_row.=" <span>By: $row->user_name<span>";
			/*echo'<tr id="notes_'.$row->id.'">
					<td style="text-align: left">'.$row->note.'</td>
					<td style="width: 170px; text-align: right">'.$dt.' '.$tm.$pt_row.'</td>
					<td style="width: 25px; text-align: right"><img src="'.$GLOBALS['WEB_PATH'].'/images/del.png" onClick="notes_delete(\''.$row->id.'\')" /></td>
				</tr>';*/
			echo'<div class="notes_listing" id="notes_'.$row->id.'">
					<div class="list_head">
						<div class="pt_name">'.$pt_row.'</div>
						<div class="dt">
						<img src="'.$GLOBALS['WEB_PATH'].'/images/del.png" onClick="notes_delete(\''.$row->id.'\')" /></div>
						<div class="dt">'.$dt.' '.$tm.'</div>
					</div>
					<div class="list_body">'.$row->note.'</div>
				</div>';
		}
		//echo'</table>';
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="save")
{
	if(trim($_REQUEST['note'])){
		$str='';
		$note_pt=$_REQUEST['note_pt'];
		if($note_pt)
		{
			$p_name_qry = imw_query("select lname,fname,mname,p_imagename, default_facility,  phone_home, phone_biz, phone_cell, preferr_contact from patient_data where id = '".$note_pt."' ")or die(imw_error());
			if(imw_num_rows($p_name_qry)){
				$p_name_row = imw_fetch_assoc($p_name_qry);
				$patient_name_id = $p_name_row['lname'].", ".$p_name_row['fname']." ".$p_name_row['mname']." - ".$note_pt;
				$str="
					patient_id='".$note_pt."',
					patient_name='$patient_name_id',
					";
			}
		}
		//get user name initials
		$q=imw_query("select fname,mname,lname from users where id='$_SESSION[authId]'")or die(imw_error());
		$rows=imw_fetch_array($q);
		$opr_fname = substr(trim($rows['fname']),0,1);
		$opr_mname = substr(trim($rows['mname']),0,1);
		$opr_lname = substr(trim($rows['lname']),0,1);
		$oper_name = $opr_fname.$opr_mname.$opr_lname;
		
		$qry = imw_query("insert into in_user_notes set user_id = '".trim($_SESSION['authId'])."',
						user_name='$oper_name',$str
						note='". imw_real_escape_string($_REQUEST['note']) ."',
						dated='".date('Y-m-d H:i:s')."',
						status=1")or die(imw_error());
		echo 'saved';
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="delete")
{
	if(trim($_REQUEST['id'])){
	$qry = imw_query("update in_user_notes set status=0, deleted_by = '".trim($_SESSION['authId'])."', deleted_on='".date('Y-m-d H:i:s')."' where id='".$_REQUEST['id']."'");
	echo 'deleted';
	}
}
?>