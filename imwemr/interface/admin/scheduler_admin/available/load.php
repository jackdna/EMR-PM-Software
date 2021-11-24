<?php
 require_once("../../../../config/globals.php");
$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/sch_labels_arr.txt";
$labels_arr_file_flag=0;
$labels_saved_arr=array();
if(file_exists($dir_path))
{
	$labels_arr_file_flag=1;
	$file_content = file_get_contents($dir_path);
	$labels_saved_arr = unserialize($file_content);
}


$total_labels=array();
if(defined('SCHEDULER_SHOW_PROC_ONLY_FOR_AVAL_LIST') && constant('SCHEDULER_SHOW_PROC_ONLY_FOR_AVAL_LIST')==true)
{
	$qry1="select distinct(acronym) from slot_procedures WHERE times = '' AND acronym != '' AND doctor_id = 0 AND active_status!='del' AND source='' ORDER BY acronym";
	$result_str1=imw_query($qry1);
	while($label_row = imw_fetch_array($result_str1))
	{
		$total_labels['Procedure'][]=explode(';',$label_row['acronym']);
	}
}else{
	$qry1="SELECT DISTINCT stl.template_label, stl.label_type FROM schedule_label_tbl as stl WHERE stl.label_type != '' and stl.template_label != ''";
	$result_str1=imw_query($qry1);
	while($label_row = imw_fetch_array($result_str1))
	{
		$total_labels[$label_row['label_type']][]=explode(';',$label_row['template_label']);
	}

	$qry2="SELECT DISTINCT scl.l_show_text,scl.l_type FROM scheduler_custom_labels as scl WHERE provider IN (select id from users where Enable_Scheduler = '1' and delete_status = '0')";
	$result_str2=imw_query($qry2);
	while($label_row = imw_fetch_array($result_str2))
	{
		$total_labels[$label_row['l_type']][]=explode(';',$label_row['l_show_text']);
	}
}

$label_type_arr=array();

foreach($total_labels as $key=> $labels_data)
{
	foreach($labels_data as $labels_data_values)
	{
		foreach($labels_data_values as $value)
		{
			$value=trim($value);
			if($value!="")
			{
				$label_type_arr[$key][]=$value;
			}
		}
	}
}

$label_unique_arr=array();
$checkedStatus='';
$label_unique_arr['NA'] = array('Slot without labels');
foreach($label_type_arr as $key=>$value)
{
	$label_unique_arr[$key]=array_unique($value);
}
$label_unique_arr;
$i=0;
$arr_show_label=array();
foreach($label_unique_arr as $key => $arr_label)
{
	foreach($arr_label as $label)
		{$arr_show_label[]=$label.'~~'.$key;}
}

$respData='<table class="table_collapse cellBorder3">';
$respData.='<tr>';
asort($arr_show_label);
foreach($arr_show_label as $label_val){
	$closeRow++;
	list($label,$key)=explode("~~",$label_val);
	
		$respData.='<td><label for="label_appt_'.$i.'">'.$label.'</label></td>';
		$respData.='<td><label for="label_appt_'.$i.'">'.$key.'</label></td>';
		$arr_show_label[]=$label.'~~'.$key;
		$label_value=$label.'~~'.$key;
		if($labels_arr_file_flag==1)
		{
			$label_exist_status=in_array($label_value,$labels_saved_arr);
			if($label_exist_status==true)
			{
				$checkedStatus='checked="checked"';
			}
			else
			{
				$checkedStatus='';
			}			
		}
		$respData.='<td><div class="checkbox"><input id="label_appt_'.$i.'" type="checkbox" '.$checkedStatus.' value="'.$label_value.'" /><label for="label_appt_'.$i.'"></label></div></td>';
		if($closeRow==2){$respData.='</tr><tr>';$closeRow=0;}
		$i++;	
}
$respData.='</table>';
echo $respData;

?>