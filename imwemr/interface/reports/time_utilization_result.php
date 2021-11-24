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
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
//scheduler object
$obj_scheduler = new appt_scheduler();
$printFile =true;
$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');
$days_limit=7;
$page_data = '';	
if($start_date == ""){
	$start_date = $curDate;
	$end_date = $curDate;
}else{
	$end_date = $start_date;//start and end date wil be equal
}
$curDate.='&nbsp;'.date(" h:i A");

if($_POST['form_submitted']){
		$arr_slot_detail=$arr_appt_detail=array();
		if(!isset($_POST['form_fac_ids']))
		{
			//--- GET ALL FACILITY IDS ----
			$fac_name=array_keys($facilities_arr);
			$arr_fac_id=array_combine($fac_name, $fac_name);
		}else{
			$arr_fac_id=array_combine($_POST['form_fac_ids'], $_POST['form_fac_ids']);
		}
		if(!isset($_POST['form_prov_id']))
		{
			//--- GET ALL USERS IDS ----
			$prov_ids=array_keys($usersArr);
			$arr_prov_id=array_combine($prov_ids, $prov_ids);
		}else{
			$arr_prov_id=array_combine($_POST['form_prov_id'], $_POST['form_prov_id']);
		}
		$arr_include_slots=array();
		if(isset($_POST['include_slots'])){
			$arr_include_slots=array_combine($_POST['include_slots'],$_POST['include_slots']);
		}
		$arrDateRange= $CLSCommonFunction->changeDateSelection();
		if(!$start_date)$start_date= date($phpDateFormat);
		$st_date = getDateFormatDB($start_date);
		list($st_y,$st_m,$st_d)=explode('-',$st_date);
		$en_date = getDateFormatDB( date($phpDateFormat,mktime(0,0,0,$st_m,$st_d+$days_limit,$st_y)));
		$str_prov_id = implode(",",$arr_prov_id);
		$str_fac_id = implode(",",$arr_fac_id);
		//getting custom labels
		$arr_all_custom_labels = array();//this could moved out of loop
		if($str_fac_id && $str_prov_id){
			$qry_cl = "select l_type, label_group, l_text, l_show_text, l_color, start_date, start_time, labels_replaced, facility, provider, start_date from scheduler_custom_labels where facility IN ($str_fac_id) and provider IN ($str_prov_id) and (start_date BETWEEN '$st_date' and '$en_date') order by start_time";					
			$res_cl = imw_query($qry_cl);
			if(imw_num_rows($res_cl) > 0){							
				while($this_cl=imw_fetch_assoc($res_cl)){
					$arr_all_custom_labels[$this_cl["provider"]][$this_cl["facility"]][$this_cl["start_date"]][$this_cl["start_time"]] = $this_cl;
				}
			}			
		}
		//get total booked detail
		$bookedQry = "select id,sa_app_duration, sa_facility_id, sa_doctor_id, sa_app_start_date,sa_app_starttime FROM schedule_appointments USE INDEX(sa_multiplecol) where sa_facility_id IN ($str_fac_id) and sa_doctor_id IN ($str_prov_id) and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and (sa_app_start_date BETWEEN '$st_date' and '$en_date')";
		$bookedRes=imw_query($bookedQry);
		while($booked=imw_fetch_object($bookedRes))
		{
			$min=($booked->sa_app_duration/60);
			$dt=$booked->sa_app_start_date;
			$st_time=$booked->sa_app_starttime;
			list($st_tym_h,$st_tym_m,$st_tym_s)=explode(":",$st_time);
			$doc_id=$booked->sa_doctor_id;
			$fac_id=$booked->sa_facility_id;
			
			$arr_slot_detail[$dt][$doc_id][$fac_id]['book']+=$min;
			$tym_increment=0;
			while($min>0)
			{
				$st_time=date("H:i:s", mktime($st_tym_h,$st_tym_m+$tym_increment,$st_tym_s));
				$arr_appt_detail[$doc_id][$fac_id][$dt][$st_time]=$booked->id;
				$min=$min-DEFAULT_TIME_SLOT;
				$tym_increment+=DEFAULT_TIME_SLOT;
			}
		}
		for($date_incri=0;$date_incri<$days_limit;$date_incri++)
		{	
			$loop_date = date($phpDateFormat , mktime(0,0,0,$st_m,$st_d+$date_incri,$st_y));
			$loop_date = getDateFormatDB($loop_date);
			//create cache file if one does not exist
			$obj_scheduler->cache_prov_working_hrs($loop_date, $arr_prov_id);
			$arr_xml = $obj_scheduler->read_prov_working_hrs($loop_date, $arr_prov_id);
			foreach($arr_xml as $provider_id => $pr_detail){
				if(! is_array($pr_detail) )continue;
				foreach($pr_detail["slots"] as $sl_id => $sl_detail){
					if(!$arr_fac_id[$sl_detail['fac_id']])continue;
					//prepare facility name arr
					$facility_arr[$sl_detail['fac_id']]=$sl_detail['fac_name'];
					//prepare time str
					$intStartHr = substr($sl_id, 0, 2);
					$intStartMin = substr($sl_id, 3, 2);
					$times_from = $intStartHr.":".$intStartMin.":00";	
					//orignally this slot have #no of labels
					$org_total_lbl=0;
					//get custom label detail
					$arr_custom_labels=$arr_all_custom_labels[$provider_id][$sl_detail['fac_id']][$pr_detail["dt"]];
					if($sl_detail["label_type"]=='Procedure')
					{
						if($sl_detail["l_text"])
						{
							if($sl_detail['label_group']==1)
							$lb_arr[0]=$sl_detail["l_text"];
							else
							$lb_arr=explode(';',$sl_detail["l_text"]);
							$org_total_lbl=sizeof($lb_arr);
						}
					}else{
						if($sl_detail["l_text"])$org_total_lbl=sizeof(explode(';',$sl_detail["l_text"]));
					}

					//over write template labels with custom labels if any
					if(isset($arr_custom_labels[$times_from]) && $sl_detail["status"] != "block" && $sl_detail["status"] != "lock"){
						$arr_clbl_temp = explode("; ", $arr_custom_labels[$times_from]["l_show_text"]);
						asort($arr_clbl_temp);
						$slot_color = $arr_custom_labels[$times_from]["l_color"];
						$sl_detail["label"] = implode("; ", $arr_clbl_temp);//open label
						$sl_detail["l_text"]  = $arr_custom_labels[$times_from]["l_text"];//all label
						$sl_detail["label_type"]  = $arr_custom_labels[$times_from]["l_type"];
						$sl_detail["label_group"] = $arr_custom_labels[$times_from]["label_group"];
						$sl_detail["label_replaced"]=$arr_custom_labels[$times_from]["labels_replaced"];
					}$arr_types[$sl_detail["label_type"]]=$sl_detail["label_type"];
					//count lock block labels
					if($sl_detail["status"] == "lock")
					{
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['locked']+=DEFAULT_TIME_SLOT;
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['avail']+=DEFAULT_TIME_SLOT;
					}
					elseif($sl_detail["status"] == "block")
					{
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['blocked']+=DEFAULT_TIME_SLOT;
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['avail']+=DEFAULT_TIME_SLOT;
					}
					else if($sl_detail["status"] == "on" && ($sl_detail["label_type"]=='Procedure' || $sl_detail["label_type"]=='Information' || $sl_detail["label_type"]==''))
					{	
						//count open labels
						if(!isset($arr_appt_detail[$provider_id][$sl_detail['fac_id']][$pr_detail["dt"]][$times_from])){
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['open']+=DEFAULT_TIME_SLOT;}
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['avail']+=DEFAULT_TIME_SLOT;
					}
					else if($sl_detail["status"] == "on" && $sl_detail["label_type"]=='Reserved')
					{	
						//count open labels
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['reserved']+=DEFAULT_TIME_SLOT;
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['avail']+=DEFAULT_TIME_SLOT;
					}
					else if($sl_detail["status"] == "on" && $sl_detail["label_type"]=='Lunch')
					{	
						//count open labels
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['lunch']+=DEFAULT_TIME_SLOT;
						$arr_slot_detail[$pr_detail["dt"]][$provider_id][$sl_detail['fac_id']]['avail']+=DEFAULT_TIME_SLOT;
					}
				}
			}
		}
		if($_POST['summary_detail']=='summary'){
			$arr_date_total=array();
			$sum_cols=$days_limit+2;
			$sum_col_width=number_format((100/$sum_cols),2);
			foreach($arr_prov_id as $int_doc_id){
				$page_content.='<tr>';
				$page_content.='<td style="width:'.$sum_col_width.'%; height:20px;">'.$usersArr[$int_doc_id].'</td>';
				$arr_wk_total=array();
				for($date_incri=0;$date_incri<$days_limit;$date_incri++)
				{	
					$loop_get_date = date("Y-m-d" , mktime(0,0,0,$st_m,$st_d+$date_incri,$st_y));
					$int_row_total=$int_row_blocked=$int_row_open=$int_row_locked=$int_row_booked=$int_row_reserved=$int_row_lunch=0;
					if(isset($arr_slot_detail[$loop_get_date][$int_doc_id]))
					{	
						foreach($arr_slot_detail[$loop_get_date][$int_doc_id] as $int_fac_id => $slot_detail)
						{
							$int_row_total+=$slot_detail['avail'];
							$int_row_open+=$slot_detail['open'];
							$int_row_blocked+=$slot_detail['blocked'];
							$int_row_locked+=$slot_detail['locked'];
							$int_row_reserved+=$slot_detail['reserved'];
							$int_row_lunch+=$slot_detail['lunch'];
							$int_row_booked+=$slot_detail['book'];
						}
						if(!isset($arr_include_slots['blocked']))$int_row_total-=$int_row_blocked;
						if(!isset($arr_include_slots['locked']))$int_row_total-=$int_row_locked;
						if(!isset($arr_include_slots['reserved']))$int_row_total-=$int_row_reserved;
						if(!isset($arr_include_slots['lunch']))$int_row_total-=$int_row_lunch;

						$arr_date_total[$loop_get_date]['avail']+=$int_row_total;
						$arr_date_total[$loop_get_date]['open']+=$int_row_open;
						$arr_date_total[$loop_get_date]['book']+=$int_row_booked;
						
						$arr_wk_total[$int_doc_id]['avail']+=$int_row_total;
						$arr_wk_total[$int_doc_id]['open']+=$int_row_open;
						$arr_wk_total[$int_doc_id]['book']+=$int_row_booked;
					}
					$float_booked=0.00;
					if(isset($int_row_total) && $int_row_total>0 && isset($int_row_booked)){
						$float_booked=number_format((($int_row_booked*100)/$int_row_total),2);
					}
					$str_row_bg=($float_booked>100)?'bg-danger':'bg-white';
					$page_content.='<td class="'.$str_row_bg.'" style="width:'.$sum_col_width.'%; height:20px;">';
					$page_content.=($float_booked)?$float_booked.'%':'';
					$page_content.='</td>';
				}
				$float_booked=0;
				$float_booked=number_format($float_booked,2);
				if(isset($arr_wk_total[$int_doc_id]['avail']) && $arr_wk_total[$int_doc_id]['avail']>0 && isset($arr_wk_total[$int_doc_id]['book'])){
					$float_booked=number_format((($arr_wk_total[$int_doc_id]['book']*100)/$arr_wk_total[$int_doc_id]['avail']),2);
				}
				$str_row_bg=($float_booked>100)?'bg-danger':'bg-white';
				$page_content.='<td class="'.$str_row_bg.'" style="width:'.$sum_col_width.'%">'.$float_booked.'%</td>';
				$page_content.='</tr>';
			}
			$page_content.='<tr>';
			$page_content.='<td class="text_b_w" style="width:'.$sum_col_width.'%; height:20px;">&nbsp;</td>';
			//show grand total
			for($date_incri=0;$date_incri<$days_limit;$date_incri++)
			{	
				$loop_get_date = date("Y-m-d" , mktime(0,0,0,$st_m,$st_d+$date_incri,$st_y));
				$arr_total_sub=$arr_date_total[$loop_get_date];

				$float_booked=0.00;
				if(isset($arr_total_sub['avail']) && $arr_total_sub['avail']>0 && isset($arr_total_sub['book'])){
					$float_booked=number_format((($arr_total_sub['book']*100)/$arr_total_sub['avail']),2);
				}
				$page_content.='<td class="text_b_w" style="width:'.$sum_col_width.'%; height:20px;">';
				$page_content.=($float_booked)?$float_booked.'%':'';
				$page_content.='</td>';
			}
			$page_content.='<td class="text_b_w" style="width:'.$sum_col_width.'%">&nbsp;</td>';
			$page_content.='</tr>';
		}else{
			## DETAIL VIEW
			$int_total_col=sizeof($arr_fac_id)+2;
			$str_col_width="";
			$float_td_width=number_format((100/$int_total_col),2);
			//if we have less than 15 columns only then we will assign them width
			if($int_total_col<15)$str_col_width="style=\"width:$float_td_width%\"";
			$arr_grand_total=array();
			foreach($arr_prov_id as $int_doc_id){
				$arr_wk_total=array();
				for($date_incri=0;$date_incri<$days_limit;$date_incri++)
				{	
					$loop_get_date = date("Y-m-d" , mktime(0,0,0,$st_m,$st_d+$date_incri,$st_y));
					$int_row_total=$int_row_blocked=$int_row_open=$int_row_locked=$int_row_booked=$int_row_reserved=$int_row_lunch=0;
					if(isset($arr_slot_detail[$loop_get_date][$int_doc_id]))
					{	
						foreach($arr_slot_detail[$loop_get_date][$int_doc_id] as $int_fac_id=>$slot_detail)
						{
							$int_row_total=$slot_detail['avail'];							
							if(!isset($arr_include_slots['blocked']))$int_row_total-=$slot_detail['blocked'];
							if(!isset($arr_include_slots['locked']))$int_row_total-=$slot_detail['locked'];
							if(!isset($arr_include_slots['reserved']))$int_row_total-=$slot_detail['reserved'];
							if(!isset($arr_include_slots['lunch']))$int_row_total-=$slot_detail['lunch'];
							
							$arr_wk_total[$int_fac_id]['avail']+=$int_row_total;
							$arr_wk_total[$int_fac_id]['open']+=$slot_detail['open'];
							$arr_wk_total[$int_fac_id]['book']+=$slot_detail['book'];
					
							$arr_grand_total[$loop_get_date]['avail']+=$int_row_total;
							$arr_grand_total[$loop_get_date]['open']+=$slot_detail['open'];
							$arr_grand_total[$loop_get_date]['book']+=$slot_detail['book'];
						}
					}
				}
				$str_row_avail=$str_row_book=$str_row_open=$str_row_ba="";
				$int_wk_avail=$int_wk_book=$int_wk_open=$int_wk_ba=0;
				foreach($arr_fac_id as $int_fac_id)
				{
					$arr_row=array();
					$arr_row['avail']=$arr_wk_total[$int_fac_id]['avail'];
					$arr_row['book']=$arr_wk_total[$int_fac_id]['book'];
					$arr_row['open']=$arr_wk_total[$int_fac_id]['open'];
					if(isset($arr_row['avail']) && $arr_row['avail']>0 && isset($arr_row['book'])){
						$arr_row['ba']=number_format((($arr_row['book']*100)/$arr_row['avail']),2);
					}
					if(!isset($arr_row['avail']) || $arr_row['avail']=='')$arr_row['avail']=0;
					if(!isset($arr_row['book']) || $arr_row['book']=='')$arr_row['book']=0;
					if(!isset($arr_row['open']) || $arr_row['open']=='')$arr_row['open']=0;
					if(!isset($arr_row['ba']) || $arr_row['ba']=='')$arr_row['ba']=0.00;
					
					$str_row_avail.='<td '.$str_col_width.'>'.$arr_row['avail'].'</td>';
					$str_row_book.='<td '.$str_col_width.'>'.$arr_row['book'].'</td>';
					$str_row_open.='<td '.$str_col_width.'>'.$arr_row['open'].'</td>';
					$str_row_ba.='<td '.$str_col_width.'>'.$arr_row['ba'].'%</td>';
					
					$int_wk_avail+=$arr_row['avail'];
					$int_wk_book+=$arr_row['book'];
					$int_wk_open+=$arr_row['open'];
				}
				$str_row_avail.='<td '.$str_col_width.'>'.$int_wk_avail.'</td>';
				$str_row_book.='<td '.$str_col_width.'>'.$int_wk_book.'</td>';
				$str_row_open.='<td '.$str_col_width.'>'.$int_wk_open.'</td>';
				if($int_wk_avail>0){
					$int_wk_ba=number_format((($int_wk_book*100)/$int_wk_avail),2);
				}
				$str_row_ba.='<td '.$str_col_width.'>'.$int_wk_ba.'%</td>';
				
				$page_content.='<tr>';
				$page_content.='<td class="text_b_w" '.$str_col_width.'>'.$usersArr[$int_doc_id].'</td>';
				foreach($arr_fac_id as $int_fac_id)
				{
					$page_content.='<td class="text_b_w" '.$str_col_width.'>'.$facilities_arr[$int_fac_id].'</td>';
				}
				$page_content.='<td class="text_b_w" '.$str_col_width.'>Wk Totals</td>';
				$page_content.='</tr><tr>';
				$page_content.='<td '.$str_col_width.'>Avail Min</td>';
				$page_content.=$str_row_avail;
				$page_content.='</tr><tr>';
				$page_content.='<td '.$str_col_width.'>Book Min</td>';
				$page_content.=$str_row_book;
				$page_content.='</tr><tr>';
				$page_content.='<td '.$str_col_width.'>Open Min</td>';
				$page_content.=$str_row_open;
				$page_content.='</tr><tr>';
				$page_content.='<td '.$str_col_width.'>% B/A</td>';
				$page_content.=$str_row_ba;
				$page_content.='</tr>';
				$page_content.='<tr><td colspan="'.$int_total_col.'" class="total-row"></td></tr>';
			}
			//practice summary block
			$str_summary="";$arr_summary=array();
			$int_wk_avail=$int_wk_book=$int_wk_open=$int_wk_ba=0;
			for($date_incri=0;$date_incri<$days_limit;$date_incri++)
			{	
				$loop_get_date = date("Y-m-d" , mktime(0,0,0,$st_m,$st_d+$date_incri,$st_y));
				$disp_date = date("d-M" , mktime(0,0,0,$st_m,$st_d+$date_incri,$st_y));
				$sub_arr=$arr_grand_total[$loop_get_date];
				$arr_summary['title'].='<td class="b">'.$disp_date.'</td>';
				$arr_summary['avail'].='<td>';
				$arr_summary['avail'].=($sub_arr['avail'])?$sub_arr['avail']:0;
				$arr_summary['avail'].='</td>';
				$arr_summary['book'].='<td>';
				$arr_summary['book'].=($sub_arr['book'])?$sub_arr['book']:0;
				$arr_summary['book'].='</td>';
				$arr_summary['open'].='<td>';
				$arr_summary['open'].=($sub_arr['open'])?$sub_arr['open']:0;
				$arr_summary['open'].='</td>';
				
				$int_wk_avail+=$sub_arr['avail'];
				$int_wk_book+=$sub_arr['book'];
				$int_wk_open+=$sub_arr['open'];
				$ba=0;
				if(isset($sub_arr['avail']) && $sub_arr['avail']>0 && isset($sub_arr['book'])){
					$ba=number_format((($sub_arr['book']*100)/$sub_arr['avail']),2);
				}
				$arr_summary['ba'].='<td>'.$ba.'%</td>';
			}
			
			$arr_summary['title'].='<td class="b">Wk Totals</td>';
			$arr_summary['avail'].='<td>'.$int_wk_avail.'</td>';
			$arr_summary['book'].='<td>'.$int_wk_book.'</td>';
			$arr_summary['open'].='<td>'.$int_wk_open.'</td>';
			$int_wk_ba=0;
			if($int_wk_avail>0){
				$int_wk_ba=number_format((($int_wk_book*100)/$int_wk_avail),2);
			}
			$arr_summary['ba'].='<td>'.$int_wk_ba.'%</td>';
			//create final html for summary block in detail view
			$str_summary='<table class="rpt_table rpt_table-bordered">';
			$str_summary.='<tr><td class="text_b_w" colspan="9">PRACTICE SUMMARY</td></tr>';
			$str_summary.='<tr>';
			$str_summary.='<td class="b"></td>';
			$str_summary.=$arr_summary['title'];
			$str_summary.='</tr><tr>';
			$str_summary.='<td class="b">Avail</td>';
			$str_summary.=$arr_summary['avail'];
			$str_summary.='</tr><tr>';
			$str_summary.='<td class="b">Book</td>';
			$str_summary.=$arr_summary['book'];
			$str_summary.='</tr><tr>';
			$str_summary.='<td class="b">Open</td>';
			$str_summary.=$arr_summary['open'];
			$str_summary.='</tr><tr>';
			$str_summary.='<td class="b">% B/A</td>';
			$str_summary.=$arr_summary['ba'];
			$str_summary.='</tr></table><br/>';
		}
			
		//getting report generator name
		$report_generator_name = NULL;
		if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
			$op_name_arr = preg_split("/, /",$_SESSION["authProviderName"]);
			$report_generator_name = $op_name_arr[1][0];
			$report_generator_name .= $op_name_arr[0][0];
		}
		$page_header='
		<table class="rpt_table rpt_table-bordered rpt_padding">
		<tr class="rpt_headers">
			<td class="rptbx1" style="width:33%">'.$dbtemp_name.' ('.$_POST['summary_detail'].')</td>	
			<td class="rptbx2" style="width:33%">Start Date : '.$start_date.'</td>					
			<td class="rptbx3" style="width:34%">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
		</tr>
		<tr class="rpt_headers">
			<td class="rptbx1">Facility :';
			if(!isset($_POST['form_fac_ids']))$page_header.='All';
			elseif(isset($_POST['form_fac_ids']) && sizeof($_POST['form_fac_ids'])==1)$page_header.=$facilities_arr[$_POST['form_fac_ids'][0]];
			elseif(isset($_POST['form_fac_ids']) && sizeof($_POST['form_fac_ids'])>1 && sizeof($_POST['form_fac_ids'])<sizeof($facilities_arr))$page_header.='Multiple';
			else $page_header.='All';
			$page_header.='</td>	
			<td class="rptbx2">Provider : ';
			if(!isset($_POST['form_prov_id']))$page_header.='All';
			elseif(isset($_POST['form_prov_id']) && sizeof($_POST['form_prov_id'])==1)$page_header.=$usersArr[$_POST['form_prov_id'][0]];
			elseif(isset($_POST['form_prov_id']) && sizeof($_POST['form_prov_id'])>1 && sizeof($_POST['form_prov_id'])<sizeof($usersArr))$page_header.='Multiple';
			else $page_header.='All';
			$page_header.='</td>					
			<td class="rptbx3">Include: ';
			if(!isset($_POST['include_slots']))$page_header.='None';
			elseif(isset($_POST['include_slots']) && sizeof($_POST['include_slots'])==1)$page_header.=$arr_include[$_POST['include_slots'][0]];
			elseif(isset($_POST['include_slots']) && sizeof($_POST['include_slots'])>1 && sizeof($_POST['include_slots'])<sizeof($arr_include))$page_header.='Multiple';
			else $page_header.='All';
			$page_header.='</td>
		</tr>
		</table>';
		if($_POST['summary_detail']=='summary'){
			$page_header.='<table class="rpt_table rpt_table-bordered">
			<tr>';
			$page_header.='<td class="text_b_w" style="width:'.$sum_col_width.'%">Provider</td>';
			for($date_incri=0;$date_incri<$days_limit;$date_incri++)
			{	
				$loop_disp_date = date("d-M" , mktime(0,0,0,$st_m,$st_d+$date_incri,$st_y));
				$page_header.='<td class="text_b_w" style="width:'.$sum_col_width.'%">'.$loop_disp_date.'</td>';
			}
			$page_header.='<td class="text_b_w" style="width:'.$sum_col_width.'%">Wk Total</td>';
			$page_header.='</tr>
			</table>';
		}
	
		$page_data=$page_header;
		$page_data.=$str_summary;
		$page_data.='<table class="rpt_table rpt_table-bordered">
		'.$page_content.'
		</table>';

		$pdf_data= '
			<page backtop="14mm" backbottom="10mm">			
				<page_footer>
					<table style="width:100%;">
						<tr>
							<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>'.$page_header.'</page_header>
			<table style="width:100%" class="rpt_table rpt_table-bordered">'.
			$page_content
			.'</table>
			</page>';


	//--- CREATE PDF FILE FOR PRINTING -----
	$hasData=0;
	if($printFile == true and $page_data != ''){
		$hasData=1;
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
		$csv_file_data= $styleHTML.$page_data;

		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		$strHTML = $stylePDF.$pdf_data;

		$file_location = write_html($strHTML);
	}else{
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}

echo $csv_file_data;

}
?>