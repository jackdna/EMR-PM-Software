<?php
require_once("../../../config/config.php");
require_once("../../../library/classes/functions.php");

$sql = '';
$i=1;
if(isset($_POST['action'])){
	if($_POST['action']=="get" && isset($_POST['moduleId']) && $_POST['moduleId']!=""){
		if($_POST['moduleId']==1001 || $_POST['moduleId']==1002)
		{			
			$q=imw_query("select value,margin from in_print_header where id='".imw_real_escape_string($_POST['moduleId'])."'")or die(imw_error());
			$d=imw_fetch_object($q);
			$rows['value']=stripslashes($d->value);
			$rows['margin']=stripslashes($d->margin);
		}
		else
		{
			$sql = "SELECT `option_chk` FROM `in_print_option_stock` WHERE `status`='1' AND `module_id`='".imw_real_escape_string($_POST['moduleId'])."'";
			$resp = imw_query($sql);
			$rows = array();
			if($resp && imw_num_rows($resp)>0){
				while($row = imw_fetch_assoc($resp)){
					$rows[] = $row['option_chk'];
				}
			}
			
			$q=imw_query("select label,value,margin from in_print_header where pid='".imw_real_escape_string($_POST['moduleId'])."'")or die(imw_error());
			while($d=imw_fetch_object($q))
			{
				$rows[$d->label]=stripslashes($d->value);
				if($d->label=='Header'){
				if($d->margin>0)$rows['header_margin']=stripslashes($d->margin);
				}
				if($d->label=='Footer'){
				if($d->margin>0)$rows['footer_margin']=stripslashes($d->margin);
				}
			}
		}
		print json_encode($rows);
	}
	elseif($_POST['action']=="save"){
		$module=$_REQUEST['sel_cat'];
		$options = $_POST['options'];
		if($module==1001 || $module==1002)
		{
			imw_query("update in_print_header set value ='". imw_real_escape_string($_POST['content']) ."', margin='". imw_real_escape_string($_POST[height])."' where id='$module'")or die(imw_error());
		}
		else
		{
			$sql1=imw_query("update in_print_option_stock set status=0 where module_id='".$module."'");
			foreach($options as $key=>$val){
				if(is_array($val)){
					foreach($val as $key1=>$vl){
						$sql = "update in_print_option_stock SET status=1 where module_id='".$module."' and option_chk='".$key1."'";
						imw_query($sql);
					}
				}
				else{
					$sql = "update in_print_option_stock SET status=1 where module_id='".$module."' and option_chk='".$key."'";
					imw_query($sql);
				}
				
				
				//$sql=("insert into print_option_stock set option_chk='".$key."',status=1,module_id=".$module);
			}
			if($module==1)
			{
				imw_query("update in_print_header set value ='". imw_real_escape_string($_POST['frame_header']) ."', margin='". imw_real_escape_string($_POST['frame_header_height'])."' where pid='$module' and label='Header'")or die(imw_error());	
				imw_query("update in_print_header set value ='". imw_real_escape_string($_POST['frame_footer']) ."', margin='". imw_real_escape_string($_POST['frame_footer_height'])."' where pid='$module' and label='Footer'")or die(imw_error());	
			}
			elseif($module==3)
			{
				imw_query("update in_print_header set value ='". imw_real_escape_string($_POST['lenses_header']) ."', margin='". imw_real_escape_string($_POST['lenses_header_height'])."' where pid='$module' and label='Header'")or die(imw_error());	
				imw_query("update in_print_header set value ='". imw_real_escape_string($_POST['lenses_footer']) ."', margin='". imw_real_escape_string($_POST['lenses_footer_height'])."' where pid='$module' and label='Footer'")or die(imw_error());	
			}
		}
	}
}
?>

