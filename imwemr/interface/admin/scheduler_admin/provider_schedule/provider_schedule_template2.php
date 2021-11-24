<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

require_once(dirname(__FILE__)."/../../../../config/globals.php");
//require_once("../../admin_header.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');
require_once('../../../../library/classes/cls_common_function.php');
$pdf_css='<style>.calbox_gray {
    margin-bottom: 0px;
    width: 98%;
    background-color: rgb(242, 242, 242) !important;
    padding: 2px;
    border-width: 1px !important;
    border-style: solid !important;
    border-color: rgb(223, 223, 223) !important;
    border-image: initial !important;
}
.calbox_transparent {
    margin-bottom: 0px;
    width: 98%;
    padding: 2px;
    border-width: 1px !important;
    border-style: solid !important;
    border-color: rgb(223, 223, 223) !important;
    border-image: initial !important;
}</style>
';
$df_pro = $_REQUEST['proId'];
$wrdata = isset($_REQUEST['wrdata']) && trim($_REQUEST['wrdata']) != "" ? trim($_REQUEST['wrdata']) : '';
$template_data = '';
$cur_date_arr = explode('_',$_REQUEST['cur_day']);
$cur_year = $_REQUEST['cur_year'];
$cur_month = $_REQUEST['cur_month'];
$time_slot = DEFAULT_TIME_SLOT - 1;//for 10 minutes set it to 9 for Five Minutes set this 4 15 Minutes set this 14
//---- Get Provider Color --------
$pro_id = $df_pro;
$qry = "select provider_color, CONCAT(lname,', ',fname,' ',mname) as doc_name from users where id = '$pro_id'";
$sql_qry = imw_query($qry);
$pro_qry_res = fetchArray($sql_qry);
$doc_name=$pro_qry_res[0]['doc_name'];
//--- Time display variable ------//
$weekdays = date('N',mktime(0,0,0,$cur_month,$cur_date_arr[0],$cur_year));
$week = getWeekCount($cur_date_arr[0]);		

$cur_day_arr = getdate(mktime(0,0,0,$cur_month,$cur_date_arr[0],$cur_year));
$cur_day_num = $cur_day_arr['wday'] == 0 ? 7 : $cur_day_arr['wday']; 
$cur_day_obj = date('Y-m-d',mktime(0,0,0,$cur_month,$cur_date_arr[0],$cur_year));	
$cur_weeks = $cur_date_arr[1].'_'.$cur_day_num.'_'.$cur_date_arr[0];
$cur_date = date('m-d-Y',mktime(0,0,0,$cur_month,$cur_date_arr[0],$cur_year));

//------ get list of patients for that physician and selected date -------
$query='select sa_patient_name, sa_app_starttime, sa_facility_id, id, sch_template_id FROM schedule_appointments where sa_doctor_id = "'.$pro_id.'" and sa_test_id = 0 
and sa_patient_app_status_id NOT IN (203,201,18,19,20) 
AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) 
and "'.$cur_day_obj.'" between sa_app_start_date and sa_app_end_date  ';
//template id check is not imposed

$response=imw_query($query);
while($appdata=imw_fetch_object($response))
{
	$appDataArr[$appdata->sa_facility_id][$appdata->sch_template_id][$appdata->sa_app_starttime][]=$appdata->sa_patient_name;
	$appCountDayArr[$appdata->sa_facility_id][$appdata->sch_template_id]++;
}
//pre($appDataArr);
//template id check is not imposed

//------ Get Main Schedule and appointments and facility Id -------
$res = getSchTmpData($cur_day_obj,$pro_id,$wrdata);
if(count($res)>0){
	$res = array_values($res);
	$provider1 = 0;
	$facility1 = 0;
	$sch_tmp_id1 = 0;			
	for($i=0;$i<count($res);$i++){
		$provider = $res[$i]['provider'];
		$facility = $res[$i]['facility'];
		$sch_tmp_id = $res[$i]['sch_tmp_id'];
		$id = $res[$i]['id'];
		if($provider != $provider1 || $facility != $facility1 || $sch_tmp_id != $sch_tmp_id1){
			$sch_tmp_id_arr[$res[$i]['sch_tmp_id']][$res[$i]['facility']] = $res[$i]['sch_tmp_id'];
			$main_id_arr[$res[$i]['sch_tmp_id']][$res[$i]['facility']] = $res[$i]['id'];
			$id_arr[$res[$i]['id']]=$res[$i]['id'];
			$sch_tmpIdAr[$res[$i]['sch_tmp_id']]=$res[$i]['sch_tmp_id'];
			$facility_arr[$res[$i]['sch_tmp_id']][$res[$i]['facility']] = $res[$i]['facility'];
		}
		$provider1 = $provider;
		$facility1 = $facility;
		$sch_tmp_id1 = $sch_tmp_id;
	}
}

$id_str=implode(',',$id_arr);
//get child template on basis of date 
$qStr="select facility, pid, sch_tmp_id, sch_tmp_pid from provider_schedule_tmp_child 
		WHERE status=1 
		AND IF(UNIX_TIMESTAMP(start_date) != 0, '$cur_day_obj' BETWEEN start_date AND end_date, 1=1) 
		AND pid IN($id_str)";
$query=imw_query($qStr);
while($data=imw_fetch_object($query))
{
	$childTemplate[$data->sch_tmp_id][$data->facility]=$data->sch_tmp_pid;
	$sch_tmp_id_arr[$data->sch_tmp_pid][$data->facility]=$data->sch_tmp_id;
	$sch_tmpIdAr[$data->sch_tmp_pid]=$data->sch_tmp_pid;
}

$tmp_id_str=implode(',',$sch_tmpIdAr);

//get total number of appt for select week day and provider
$qryTotal = "SELECT COUNT(id) as total_appt, sa_facility_id, sch_template_id FROM `schedule_appointments` 
WHERE sa_doctor_id = '".$pro_id."' 
AND sa_app_start_date >= '".$cur_day_obj."'	
AND CEIL( SUBSTRING( sa_app_start_date, 9, 2 ) /7 ) = '".$week."' 
AND DATE_FORMAT( sa_app_start_date, '%w' ) = '".$weekdays."' 
AND sa_patient_app_status_id NOT IN (203,201,18,19,20) 
AND sch_template_id IN ($tmp_id_str)
GROUP BY sa_facility_id,sch_template_id
ORDER BY sa_app_start_date DESC ";
$response=imw_query($qryTotal);
while($appdata=imw_fetch_object($response))
{
	$appCountTotalArr[$appdata->sa_facility_id][$appdata->sch_template_id]=$appdata->total_appt;
}
//------- Main AS per schedule Id Loop ---------
foreach($sch_tmp_id_arr as $key=>$fac_arr){
	foreach($fac_arr as $fac_id=>$val){
	
	$sch_tmp_id = $val;
	$qry = "select * from schedule_templates where id = '$sch_tmp_id'";
	$sql_qry = imw_query($qry);
	$qryRes = fetchArray($sql_qry);
	
	$sch_parent_status = $qryRes[0]['parent_id'];
	$child_json_data = '';
	$selected_child_temp_id = '';
	$schedule_name_parent1 = '';
	if($sch_parent_status != 0)
	{
		$qry = "select schedule_name from schedule_templates where id = '$sch_parent_status'";
		$sql_qry = imw_query($qry);
		$childParQryRes = fetchArray($sql_qry);		
		$schedule_name_parent1 = $childParQryRes[0]['schedule_name'];			
	}
	
	if($sch_parent_status == 0)
	{
		$sch_parent_status = $sch_tmp_id;
	}
	
	$mor_start_time = $qryRes[0]['morning_start_time'];
	$mor_end_time = $qryRes[0]['morning_end_time'];	
	$schedule_name1 = $qryRes[0]['schedule_name'];
	if($schedule_name_parent1 == '')
	{
		$schedule_name_parent1 = $schedule_name1;	
	}
	$date_status = $qryRes[0]['date_status'];
	$morning_start_time = '';
	$morning_start_times = '';
	//--- Time display variable ------
	$start_time_hour_var = '00';
	$start_time_hour_var1 = '00';
	$start_time_min_var = '00';
	$template_data1 = '';
	$template_data1_pdf = '';
	
	
	$optsQry = "SELECT id,schedule_name FROM schedule_templates WHERE parent_id = '$sch_parent_status'";
	$optsQryObj = imw_query($optsQry);	
	$schedule_option = "<option value=''>&nbsp;&nbsp;-- Select --</option>";
	$sel_target_temp = $val.'<>'.$schedule_name1;
	while($fac_res = imw_fetch_assoc($optsQryObj))
	{
		$id = $fac_res['id'];
		$schedule_name_get = $fac_res['schedule_name'];
		$opts_val = $id.'<>'.$schedule_name_get;	
		$selected = '';
		if($sel_target_temp == $opts_val)
		{
			$selected = " selected = 'selected' ";	
		}
		$schedule_option .= '<option value="'.$opts_val.'" '.$selected.' >'.$schedule_name_get.'</option>';
	}
	$child_json_data = urlencode($schedule_option);	
	
	for($m=0;$m<1440;$m++){
		$debug = "";
		$hour = false;
		$time_status = 'AM';
		$start_time_min_var += $time_slot + 1;
		
		
		
		if($start_time_min_var > 0){
			$start_time_min_var1 = $start_time_min_var - DEFAULT_TIME_SLOT;//10 for 10 minute time slot set 5 for five minute time slot
			$start_time_hour_var2 = $start_time_hour_var1;
		}
		else{
			$start_time_min_var1 = 60 - DEFAULT_TIME_SLOT;//55 for five minute time slot,50 for 10 minutes 
		}
		if($start_time_min_var >= 60){
			$start_time_min_var = '00';
			$start_time_hour_var++;
			$start_time_hour_var1++;
			$hour = true;			
			if($start_time_hour_var < 10){
				$start_time_hour_var = '0'.$start_time_hour_var;
			}
			if($start_time_hour_var1<10 && $start_time_hour_var1>0){
				$start_time_hour_var1 = '0'.$start_time_hour_var1;
			}
			if($start_time_hour_var >= 12){
				$start_time_hour_var = '01';			
				$time_check = true;
			}
			
		}
		
		if($start_time_min_var1<10){
			$start_time_min_var1 = '0'.$start_time_min_var1;
		}
		if($start_time_min_var2<10){
			$start_time_min_var2 = '0'.$start_time_min_var2;
		}
		if($start_time_min_var < 10){
			$start_time_min_var = '0'.$start_time_min_var;
		}

		$start_time = $start_time_hour_var2.':'.$start_time_min_var1.':00';
		$end_time = $start_time_hour_var1.':'.$start_time_min_var.':00';
		$morning_start_time = '';
		$provider_color = '#FFF';
		$schedule_name = '';
		//--- Open Time And Close Time Check ----------
		
		if($start_time == $mor_start_time || $time_chaeck == true){
			$time_chaeck = true;
			$provider_color = $pro_qry_res[0]['provider_color'];
			$schedule_name = $qryRes[0]['schedule_name'];
			//$debug = $end_time." > ".$mor_end_time;
			if($end_time > $mor_end_time){
				$morning_start_time = '';
				$time_chaeck = false;
				$provider_color = '#FFF';
				$schedule_name = '';
			}
		}
		
		if($start_time_hour_var2 >= 12){			
			$time_status = "PM";
		}
		if($start_time_hour_var2 > 12){
			$start_time_hour_var2 = $start_time_hour_var2 - 12;
			if($start_time_hour_var2<10)
				$start_time_hour_var2 = '0'.$start_time_hour_var2;			
		}
		$class=($schedule_name)?'calbox_transparent':'calbox_gray';
		
		$facility = $facility_arr[$key][$fac_id];
		$sch_app_id = $main_id_arr[$key][$fac_id];
		//------- Time Slot Div -----------
		$provider_color=($provider_color)?$provider_color:'#FFF';
		$template_data1 .= '
			<tr>
				<td class="">
					<div id="div_'.$start_time.'_'.$facility.'" style="background-color:'.$provider_color.'" class="'.$class.'">
						&nbsp;<span class="">'.$start_time_hour_var2.':'.$start_time_min_var1.' '.$time_status.' '.$debug.'</span>
							&nbsp;&nbsp;<b>'.$schedule_name.'</b>';
							$template_data1.=($appDataArr[$facility][$sch_tmp_id][$start_time])?' - '.implode('; ',$appDataArr[$facility][$sch_tmp_id][$start_time]):'';
						$template_data1.='</font>
					</div>
				</td>
			</tr>				
		';
		if($schedule_name){
		$template_data1_pdf .= '
			<tr>
				<td>
					<div id="div_'.$start_time.'_'.$facility.'" style="background-color:'.$provider_color.'" class="'.$class.'">
						&nbsp;<span class="">'.$start_time_hour_var2.':'.$start_time_min_var1.' '.$time_status.' '.$debug.'</span>
							&nbsp;&nbsp;<b>'.$schedule_name.'</b>';
							$template_data1_pdf.=($appDataArr[$facility][$sch_tmp_id][$start_time])?' - '.implode('; ',$appDataArr[$facility][$sch_tmp_id][$start_time]):'';
						$template_data1_pdf.='</font>
					</div>
				</td>
			</tr>				
		';
		}
		$m += $time_slot;
	}
	
	//------Get Facility Name --------
	$qry = "select name from facility where id = '$facility'";
	$sql_qry = imw_query($qry);
	$fac_res = fetchArray($sql_qry);
	//--- Shedule count Td ----------
	$width = ceil(730/count($sch_tmp_id_arr));
	$template_data2 .= '
		<td width="'.$width.'"  valign="top" >
			<table width="100%" cellpading="0" cellspacing="0" border="0" class="boxsch" >
				'.$template_data1.'
			</table>
		</td>
	';
	$template_data2_pdf .= '
		
			<table width="100%" cellpading="0" cellspacing="0" border="0">
				'.$template_data1_pdf.'
			</table>
		
	';
	$cur_date_arr = explode('-',$cur_date);
	$weekDays = date('N',mktime(0,0,0,$cur_date_arr[0],$cur_date_arr[1],$cur_date_arr[2]));
	$week = getWeekCount($cur_date_arr[1]);	
	$task_date_set = $cur_date_arr[2].'-'.$cur_date_arr[0].'-'.$cur_date_arr[1];
	$wrdata = trim($wrdata);
	if($wrdata!= "" && isset($wrdata))
	{
		$wrdata_arr = explode('|',$wrdata);
		if(count($wrdata_arr) == 2)
		{
			$week = $wrdata_arr[0];
			$weekDays = $wrdata_arr[1];					
		}
	}	
	
	if($childTemplate[$sch_tmp_id][$fac_id])
	{$provider_sch_tmp_id=$main_id_arr[$childTemplate[$sch_tmp_id][$fac_id]][$fac_id];}
	else
	{$provider_sch_tmp_id=$main_id_arr[$sch_tmp_id][$fac_id];}
	/*onClick="sel_sch(\''.$facility.'\',\''.$sch_parent_status.'<>'.addslashes($schedule_name_parent1).'\',\''.$mor_start_time.'\',\''.$mor_end_time.'\',\''.$sch_app_id.'\',\''.$cur_date.'\',\''.$date_status.'\',\''.$child_json_data.'\',\''.$temp_expiry_date.'\',\''.$provider_sch_tmp_id.'\');"*/
	$template_data3 .= '
		<td align="left" width="'.$width.'"  valign="top" >
			<div id="parent_'.$start_time.'_'.$facility.'" class="provsectemp">
				<div class="row">
			<div class="col-sm-8"><a href="javascript:void(0)" class="a text_12b_purple" onClick="sel_sch(\''.$provider_sch_tmp_id.'\', \''.$task_date_set.'\',\''.(int)$appCountTotalArr[$facility][$sch_tmp_id].'\',\''.(int)$appCountTotalArr[$facility][$sch_tmp_id].'\');">'.$fac_res[0]['name'].'</a></div>
			<div class="col-sm-4 text-right">
			<span class="glyphicon glyphicon-print btn-info pointer" title="Print" onClick="sel_sch(\''.$provider_sch_tmp_id.'\',\''.$task_date_set.'\',\''.(int)$appCountTotalArr[$facility][$sch_tmp_id].'\',\''.(int)$appCountTotalArr[$facility][$sch_tmp_id].'\'); printTemplate(\''.$facility.'-'.$sch_tmp_id.'\');"></span>
			&nbsp; 
			<span class="glyphicon glyphicon-trash pointer btn-danger" title="Delete" onClick="sel_sch(\''.$provider_sch_tmp_id.'\', \''.$task_date_set.'\',\''.(int)$appCountTotalArr[$facility][$sch_tmp_id].'\',\''.(int)$appCountTotalArr[$facility][$sch_tmp_id].'\');delete_me('.(int)$appCountTotalArr[$facility][$sch_tmp_id].','.(int)$appCountDayArr[$facility][$sch_tmp_id].',\''.$provider_sch_tmp_id.'\');"></span>
			&nbsp; 
			<span class="glyphicon glyphicon-transfer btn-success pointer" title="Replace" onClick="sel_sch(\''.$provider_sch_tmp_id.'\',\''.$task_date_set.'\',\''.(int)$appCountTotalArr[$facility][$sch_tmp_id].'\',\''.(int)$appCountTotalArr[$facility][$sch_tmp_id].'\'); show_replace_cnt();"></span>
			</div>
			
			</div>
			</div>
		</td>
	';
	
	$pdf[$facility.'-'.$sch_tmp_id]=$pdf_css.'
	<page>
	<table style="width:100%" cellpading="0" cellspacing="1" border="0">
		<tr>
			<td class="text_b_w" style="width:24%">Provider: '.$doc_name.' </td>
			<td class="text_b_w" style="width:24%">Facility:'.$fac_res[0]['name'].' </td>
			<td class="text_b_w" style="width:24%">Template: '.$qryRes[0]['schedule_name'].'</td>
			<td class="text_b_w" style="auto">Timing:'.$mor_start_time.' To '.$mor_end_time.'</td>
		</tr>
		</table>
		'.$template_data2_pdf.'
		
	</page>';
} }
//----------------- Main Table ----------
$template_data .= '
	<table width="100%" cellpading="1" cellspacing="1" border="0">
		<tr>
			'.$template_data2.'
		</tr>
	</table>
';
//---- Display time if no schedule Id ---------
$start_time_hour_var = '00';
if(count($sch_tmp_id_arr) == 0){
	$template_data1 = '';
	for($m=1;$m<=1440;$m++){
		$start_time_min_var += $time_slot + 1;
		if($start_time_min_var >= 60){
			$start_time_min_var = '00';
			$start_time_min_var1 = 60 - DEFAULT_TIME_SLOT;//set 55 for 5 minute time slot set 50 for 10 minute time slot
			$start_time_hour_var++;
			if($start_time_hour_var < 10){
				$start_time_hour_var = '0'.$start_time_hour_var;
			}
			if($start_time_hour_var >= 12){
				$time_check = true;
			}
		}
		$time_status = 'AM';
		if($start_time_hour_var >= 12 || $m == 1430){
			$time_status = 'PM';
		}
		$start_time_hour_var1 = $start_time_hour_var;
		if($start_time_hour_var > 12){
			$start_time_hour_var1 = $start_time_hour_var - 12;
			if($start_time_hour_var1<10)
				$start_time_hour_var1 = '0'.$start_time_hour_var1;			
		}
		if($start_time_min_var == 0){
			$start_time_min_var1 = 60 - DEFAULT_TIME_SLOT;//set 55 for 5 minute time slot set 50 for 10 minute time slot
			$start_time_hour_var1 = $start_time_hour_var1 - 1;
			if($start_time_hour_var1<10){
				$start_time_hour_var1 = '0'.$start_time_hour_var1;
			}
		}
		else{
			$start_time_min_var1 = $start_time_min_var - DEFAULT_TIME_SLOT;//set 5 for 5 minute time slot set 10 for 10 minute time slot
		}
		if($start_time_min_var1<10){
			$start_time_min_var1 = '0'.$start_time_min_var1;
			
		}
		$template_data1 .= '
			<div  id="div_'.$m.'_'.$facility.'" class="calbox">			
				&nbsp;<span class="">'.$start_time_hour_var1.':'.$start_time_min_var1.' '.$time_status.'</span>
			</div>
		';
		$m += $time_slot;
	}
	$template_data .= '
		<!--<div style="calbox_gray">
			<font size="1">
				&nbsp;&nbsp;&nbsp;&nbsp;
			</font>
		</div>-->'.$template_data1.'
	';
}
?>
<table width="100%" cellpading="0" cellspacing="0" border="0">
	<tr>
	<?php
		print $template_data3;
	?>
	</tr>
</table>
<?php
	if($template_data3){
		$hg = $_SESSION["wn_height"] - 395;
	}
	else{
		$hg = $_SESSION["wn_height"] - 355;
	}
foreach($pdf as $key=>$content)
{
	echo'<div id="'.$key.'" style="display:none">'.$content.'</div>';
}
?>
<div id="template_div_id1" style="height:<?php print $hg; ?>px; overflow:auto; position:relative;">
<input type="hidden" name="get_date" value="<?php print $get_cur_date; ?>" />
<table width="100%" cellpadding="0" bgcolor="#f5f5f5" cellspacing="0" border="0">
	<tr>
		<td align="left" valign="middle">
			<?php
				print $template_data;
			?>
		</td>
	</tr>
</table>
</div>