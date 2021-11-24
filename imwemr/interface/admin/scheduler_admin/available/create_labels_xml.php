<?php
	
	include_once(dirname(__FILE__)."/../../../../config/globals.php");
	$labels=$_POST['labels'];
	$labels_arr=explode('-*-',$labels);
	
	$file_data='';
	asort($labels_arr);
	$top_label_op=$top_label_lunch="";
	foreach($labels_arr as $label)
	{
		if(trim($label)!="")
		{
			list($label_name,$label_type)=explode('~~',$label);
			if($label_name=="Slot without labels"){
				$top_label_op='<option value="'.$label.'">'.$label_name.'-'.$label_type.'</option>';
			}else if(strtolower($label_name)=="lunch" && strtolower($label_type)=="lunch"){
				$top_label_lunch='<option value="'.$label.'">'.$label_name.'-'.$label_type.'</option>';
			}else{
				$file_data.='<option value="'.$label.'">'.$label_name.'-'.$label_type.'</option>';			
			}
		}
	}
	$file_data=$top_label_op.$top_label_lunch.$file_data;
	// create file for labels in the scheduler
	if(!$dir_path)$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/";
	//check is that directory exist
	if(!is_dir($dir_path))
	{
		mkdir($dir_path,0777,true);
                chmod($dir_path,0777);
	}
	$file_name='sch_labels.txt';
	$file_arr_container='sch_labels_arr.txt';
	$fp=file_put_contents($dir_path.$file_name,$file_data);
	$serialize_arr=serialize($labels_arr);
	$fp_arr=file_put_contents($dir_path.$file_arr_container,$serialize_arr);
	
	if($fp)
	{
		echo 'File created';
	}
	else
	{
		echo 'File creation error';
	}
?>