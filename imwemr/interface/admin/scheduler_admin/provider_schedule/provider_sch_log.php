<?php
require_once("../../../../config/globals.php");
$show_record_limit=100;
if($sel_pro_month && $logType=='schedule'){
	
//get total number of records
$qCount=imw_query("select id from schedule_template_log where provider_id='$sel_pro_month'");
$total_records=imw_num_rows($qCount);
	
$q=imw_query("SELECT stl.*,DATE_FORMAT(stl.on_date,'".get_sql_date_format('','Y','-')."') AS on_date, fac.name as facility_name FROM `schedule_template_log` as stl
LEFT JOIN facility as fac on stl.facility_id=fac.id
WHERE provider_id='$sel_pro_month' ORDER BY `stl`.`act_datetime` DESC LIMIT $limit, $show_record_limit ");
if(imw_num_rows($q)>0){
$str1='
<table class="table table-bordered adminnw tbl_fixed table-condensed">    
<thead>
	<tr>
		<th class="text-center" style="width: 50px ">Sr.</th>
		<th>Week Day</th>            
		<th>Cal. Date</th>
		<th>Facility</th>
		<th>Template</th>
		<th>Summary</th>
		<th>For Future </th>
		<th>User </th>
		<th>IP </th>
		<th>Timestamp </th>
	</tr>
</thead>
<tbody id="divLoadSchTmp">';

	$sr=0+$limit;
	while($d=imw_fetch_object($q)){
		$sr++;
		$week_day_arr=array();
		$week_day_arr=explode(' /',$d->weekday);
		$week_day=$week_day_arr[0].' day'.$week_day_arr[1];

		$vrs=array();$status_time=$status_date='';
		$vrs=explode(' ',$d->act_datetime);
		if ($vrs['0'] && get_number($vrs['0']) != "0") {									
			$tmp_date = $vrs['0'];
			$status_date=get_date_format($tmp_date);
		}
		$appoint_time_to=$vrs['1'];
		$time_hourt=substr($appoint_time_to,0,2);
		$time_minutet=substr($appoint_time_to,3,2);
		$status_time=date("h:i A", mktime($time_hourt,$time_minutet));

		$tmp_name='';
		$len=strlen(trim($d->template_name));
		if((substr(trim($d->template_name),$len-1,1))==',')
		{
			$tmp_name=substr(trim($d->template_name),0,$len-1);
		}else $tmp_name=trim($d->template_name);

		if($d->for_future=='all')$d->for_future='yes';
		$str2.= "<tr class='alt3'>
		<td>$sr</td>
		<td>$week_day</td>
		<td>$d->on_date</td>
		<td>$d->facility_name</td>
		<td>$tmp_name</td>
		<td>$d->summary</td>
		<td>$d->for_future</td>
		<td>$d->logged_user_name</td>
		<td>$d->ip</td>
		<td>$status_date $status_time</td>
		</tr>";
	}
	$str3='</tbody>
	</table>';
	$page_link='<br/><div class="row"><div class="col-sm-7"></div><div class="col-sm-2 text-right">';
	$page_link.="Showing ";
	$page_link.=(($limit+$show_record_limit)>$total_records)?$total_records:$limit+$show_record_limit;
	$page_link.='/'.$total_records;
	$page_link.='</div><div class="col-sm-1"></div><div class="col-sm-2 text-left">';
	$page_link.=(($limit+$show_record_limit)<$total_records)?"<a href='javascript:void(0)' onClick=\"viewLog('$sel_pro_month', 'schedule',".($limit+$show_record_limit).");\" class='text_purple'>Show Next ".$show_record_limit." records </a>":'';
	$page_link.='</div></div>';
	if(!$limit){
		$paging='<div style="text-align: center" id="paging">';
		$paging.=$page_link;
		$paging.='</div>';
	}else $paging=$page_link;
	echo($limit)?$str2:$str1.$str2.$str3;
	echo '~::~'.$paging;
	
 } else echo 'No log found~::~No Page';
}elseif($sel_pro_month && $logType=='custom_label'){
	//get total number of records
	$qCount=imw_query("select l_log_id from scheduler_custom_labels_log where l_provider='$sel_pro_month'");
	$total_records=imw_num_rows($qCount);
	
	$q=imw_query("SELECT scl.*,DATE_FORMAT(scl.l_date,'".get_sql_date_format('','Y','-')."') AS on_date, fac.name as facility_name, CONCAT(u.lname,', ',u.fname) as operator, st.schedule_name FROM `scheduler_custom_labels_log` as scl
	LEFT JOIN facility as fac ON scl.l_facility=fac.id
	LEFT JOIN users as u ON scl.operator_id=u.id
	LEFT JOIN schedule_templates AS st ON scl.temp_id=st.id
	WHERE l_provider='$sel_pro_month' ORDER BY `scl`.`l_log_id` DESC LIMIT $limit, $show_record_limit ")or die(imw_error());
if(imw_num_rows($q)>0){

	$str1='<table class="table table-bordered adminnw tbl_fixed table-condensed">    
	<thead>
		<tr>
			<th class="text-center" style="width: 50px ">Sr.</th>
			<th>Facility</th>            
			<th>Date</th>
			<th>Template</th>
			<th>Timing</th>
			<th>Action</th>
			<th>Label Before</th>
			<th>Label After</th>
			<th>Operator</th>
			<th>Timestamp</th>
		</tr>
	</thead>
	<tbody id="divLoadSchTmp">';
	$sr=0+$limit;
	while($d=imw_fetch_object($q)){
		$sr++;
		
		list($h,$i,$s)=explode(":",$d->l_start_time);
		$start_time=date("h:i A", mktime($h,$i));
		list($h,$i,$s)=explode(":",$d->l_end_time);
		$end_time=date("h:i A", mktime($h,$i));
		
		$vrs=explode(' ',$d->time_stamp);
		if ($vrs['0'] && get_number($vrs['0']) != "0") {								
			$tmp_date = $vrs['0'];
			$status_date=get_date_format($tmp_date);
		}
		list($time_hourt,$time_minutet,$sec)=explode(":",$vrs['1']);
		$appoint_time_to=$vrs['1'];
		$status_time=date("h:i:s A", mktime($time_hourt,$time_minutet,$sec));

		$tmp_name='';
		$tmp_name=trim($d->schedule_name);

		$str2.= "<tr class='alt3'>
		<td>$sr</td>
		<td>$d->facility_name</td>
		<td>$d->on_date</td>
		<td>$d->schedule_name</td>
		<td>$start_time to $end_time</td>
		<td>$d->act</td>
		<td>$d->l_text_before</td>
		<td>$d->l_text_after</td>
		<td>$d->operator</td>
		<td>$status_date $status_time</td>
		</tr>";
	}
	$str3='</tbody>
	</table>';
	$page_link='<br/><div class="row"><div class="col-sm-7"></div><div class="col-sm-2 text-right">';
	$page_link.="Showing ";
	$page_link.=(($limit+$show_record_limit)>$total_records)?$total_records:$limit+$show_record_limit;
	$page_link.='/'.$total_records;
	$page_link.='</div><div class="col-sm-1"></div><div class="col-sm-2 text-left">';
	$page_link.=(($limit+$show_record_limit)<$total_records)?"<a href='javascript:void(0)' onClick=\"viewLog('$sel_pro_month', 'custom_label',".($limit+$show_record_limit).");\" class='text_purple'>Show Next ".$show_record_limit." records </a>":'';
	$page_link.='</div></div>';
	
	if(!$limit){
		$paging='<div style="text-align: center" id="paging">';
		$paging.=$page_link;
		$paging.='</div>';
	}else $paging=$page_link;
	echo($limit)?$str2:$str1.$str2.$str3;
	echo '~::~'.$paging;
 } else echo 'No log found~::~No Page';
}else echo 'Please select physician first~::~No Page';?>