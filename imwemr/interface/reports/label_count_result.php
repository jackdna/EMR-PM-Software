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

FILE : label_count_result.php
PURPOSE : to produce count of labels on a day
ACCESS TYPE : INCLUDED
*/
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
//scheduler object
$obj_scheduler = new appt_scheduler();

$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$page_data = '';	$printFile= false;

if($start_date == ""){
	$start_date = $curDate;
	$end_date = $curDate;
}else{
	$end_date = $start_date;//start and end date wil be equal
}
$curDate.='&nbsp;'.date(" h:i A");

if($_POST['form_submitted']){
	
		if(count($facility_name)<=0)
		{
			//--- GET ALL FACILITY IDS ----
			$fac_query = "select id from facility";
			$fac_query_res = imw_query($fac_query);
			while ($fac_res = imw_fetch_array($fac_query_res)) {
				$fac_id = $fac_res['id'];
				$facility_name[$fac_id] = $fac_id;
			}
		}else{
			foreach($facility_name as $fac_id)
			{
				$new_tmp_fac_arr[$fac_id]=$fac_id;
			}
			//now overwrite posted facility arr
			$facility_name=$new_tmp_fac_arr;
		}
		if(count($providerID)<=0)
		{
			//--- GET ALL USERS IDS ----
			$user_query = "select id from users where Enable_Scheduler=1";
			$user_query_res = imw_query($user_query);
			while ($user_res = imw_fetch_array($user_query_res)) {
				$sel='';
				$user_id = $user_res['id'];
				$providerID[$user_id] = $user_id;
			}
		}
		$printFile = true;
		//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
		$arrDateRange= $CLSCommonFunction->changeDateSelection();

		if($dayReport=='Daily'){
			$Start_date = $End_date= date($phpDateFormat);
		}else if($dayReport=='Weekly'){
			$Start_date = $arrDateRange['WEEK_DATE'];
			$End_date= date($phpDateFormat);
		}else if($dayReport=='Monthly'){
			$Start_date = $arrDateRange['MONTH_DATE'];
			$End_date= date($phpDateFormat);
		}else if($dayReport=='Quarterly'){
			$Start_date = $arrDateRange['QUARTER_DATE_START'];
			$End_date = $arrDateRange['QUARTER_DATE_END'];
		}

		$st_date = getDateFormatDB($start_date);
		$en_date = getDateFormatDB($end_date);
		$primaryProviderId = implode(",",$providerID);
		$facility_name_str = implode(",",$facility_name);
		//create cache file if one does not exist
		$obj_scheduler->cache_prov_working_hrs($st_date, $providerID);
		$arr_xml = $obj_scheduler->read_prov_working_hrs($st_date, $providerID);
		//pre($arr_xml);
		foreach($arr_xml as $provider_id => $pr_detail){
			if(! is_array($pr_detail) )continue;
			//getting custom labels
			$arr_custom_labels = array();//this could moved out of loop
			if($facility_name_str && $provider_id && $pr_detail["dt"]){
				$qry_cl = "select l_type, label_group, l_text, l_show_text, l_color, start_date, start_time, labels_replaced from scheduler_custom_labels where facility IN (".$facility_name_str.") and provider = '".$provider_id."' and start_date = '".$pr_detail["dt"]."' order by start_time";					
				$res_cl = imw_query($qry_cl);
				if(imw_num_rows($res_cl) > 0){							
					while($this_cl=imw_fetch_assoc($res_cl)){
						$arr_custom_labels[$this_cl["start_time"]] = $this_cl;
					}
				}			
			}
			//prepare provider name arr
			$provider_arr[$pr_detail['id']]=$pr_detail['hover_name'];
			foreach($pr_detail["slots"] as $sl_id => $sl_detail){
				if(!$facility_name[$sl_detail['fac_id']])continue;
				//prepare facility name arr
				$facility_arr[$sl_detail['fac_id']]=$sl_detail['fac_name'];
				//prepare time str
				$intStartHr = substr($sl_id, 0, 2);
				$intStartMin = substr($sl_id, 3, 2);
				$times_from = $intStartHr.":".$intStartMin.":00";	
				//orignally this slot have #no of labels
				$org_total_lbl=0;
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
				if(isset($arr_custom_labels[$times_from]) && $sl_detail["status"] != "block" && $sl_detail["status"] != "lock"){// && ($sl_detail["status"] != "block" && $sl_detail["status"] != "lock")
					$arr_clbl_temp = explode("; ", $arr_custom_labels[$times_from]["l_show_text"]);
					asort($arr_clbl_temp);
					$slot_color = $arr_custom_labels[$times_from]["l_color"];
					$sl_detail["label"] = implode("; ", $arr_clbl_temp);//open label
					$sl_detail["l_text"]  = $arr_custom_labels[$times_from]["l_text"];//all label
					$sl_detail["label_type"]  = $arr_custom_labels[$times_from]["l_type"];
					$sl_detail["label_group"] = $arr_custom_labels[$times_from]["label_group"];
					$sl_detail["label_replaced"]=$arr_custom_labels[$times_from]["labels_replaced"];
				}
				//count lock block labels
				if($sl_detail["status"] == "block" || $sl_detail["status"] == "lock")
				{
					$locked=0;
					if($sl_detail["label_type"]=='Procedure')
					{
						if($sl_detail["l_text"])
						{
							//check no of procedure are there	
							if($sl_detail['label_group']==1)
							$lb_arr[0]=$sl_detail["l_text"];
							else
							$lb_arr=explode(';',$sl_detail["l_text"]);

							$locked=sizeof($lb_arr);
						}
					}elseif($sl_detail["label_type"]=='Information' || $sl_detail["label_type"]==''){
						if($sl_detail["l_text"])$locked=sizeof(explode(';',$sl_detail["l_text"]));
					}
					$lbl_count[$provider_id][$sl_detail['fac_id']]['locked']+=$locked;
					$lbl_count[$provider_id][$sl_detail['fac_id']]['total']+=$locked;
				}
				else if($sl_detail["status"] == "on" && ($sl_detail["label_type"]=='Procedure' || $sl_detail["label_type"]=='Information' || $sl_detail["label_type"]==''))
				{	
					//count open labels
					$open=0;
					if($sl_detail["label_type"]=='Procedure')
					{
						if($sl_detail["label"])
						{
							if($sl_detail['label_group']==1)
							$lb_arr[0]=$sl_detail["label"];
							else
							$lb_arr=explode(';',$sl_detail["label"]);

							$open=sizeof($lb_arr);
						}
					}elseif($sl_detail["label_type"]=='Information' || $sl_detail["label_type"]==''){
						if($sl_detail["label"])$open=sizeof(explode(';',$sl_detail["label"]));
					}
					$lbl_count[$provider_id][$sl_detail['fac_id']]['open']+=$open;
					$lbl_count[$provider_id][$sl_detail['fac_id']]['total']+=$open;

					//count booked
					$booked=0;$replaced_arr='';
					if($sl_detail["label_replaced"] && $sl_detail["label_type"])
					{
						$replaced_arr=explode("::",$sl_detail["label_replaced"]);
						$booked=(sizeof($replaced_arr)-1);
					}
					$lbl_count[$provider_id][$sl_detail['fac_id']]['booked']+=$booked;
					$lbl_count[$provider_id][$sl_detail['fac_id']]['total']+=$booked;
					
					//count removed
					$total_available=$removed=0;
					$total_available=sizeof(explode(";",$sl_detail["l_text"]));
					if($sl_detail["label_type"])
					{
						if(!$sl_detail["l_text"] && !$sl_detail["label"])
						{	
							$lbl_count[$provider_id][$sl_detail['fac_id']]['removed']+=$org_total_lbl;
							$lbl_count[$provider_id][$sl_detail['fac_id']]['total']+=$org_total_lbl;
						}elseif(($open+$booked) != $total_available)
						{	
							$removed=$total_available-($open+$booked);
							$lbl_count[$provider_id][$sl_detail['fac_id']]['removed']+=$removed;
							$lbl_count[$provider_id][$sl_detail['fac_id']]['total']+=$removed;

							//$lbl_count[$provider_id][$sl_detail['fac_id']]['conflict'].="<br/>$times_from=$sl_detail[l_text]<br>$total_available-($open+$booked)";
						}
					}

				}
			}
		}
		//getting report generator name
		$report_generator_name = NULL;
		if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
			$op_name_arr = preg_split("/, /",$_SESSION["authProviderName"]);
			$report_generator_name = $op_name_arr[1][0];
			$report_generator_name .= $op_name_arr[0][0];
		}


		if(count($lbl_count) > 0){
			//$strHTML = file_get_contents(dirname(_FILE__)."/../themes/default/pdf.css");

			foreach($lbl_count as $docId => $fac_arr){
				$j=1;
				$page_content.='<tr><td class="text_b_w alignLeft nowrap" colspan="7">Physician : '.$provider_arr[$docId].'</td></tr>';

				foreach($fac_arr as $fac_id=>$lbl_counts){
					$page_content.='
					<tr>
						<td class="text alignCenter white" style="width:5%; height:20px;">'.$j.'</td>
						<td class="text alignLeft white" style="width:20%;">&nbsp;'.$facility_arr[$fac_id].'</td>
						<td class="text alignLeft white" style="width:25%;">&nbsp;'.number_format($lbl_counts['total']).'</td>
						<td class="text alignLeft white" style="width:10%;">&nbsp;'.number_format($lbl_counts['removed']).'</td>
						<td class="text alignLeft white" style="width:10%;">&nbsp;'.number_format($lbl_counts['locked']).'</td>
						<td class="text alignLeft white" style="width:15%;">&nbsp;'.number_format($lbl_counts['booked']).'</td>
						<td class="text alignLeft white" style="width:15%;">&nbsp;'.number_format($lbl_counts['open']).'</td>
					</tr>';
					$j++;
				}

			}

			$page_data='
			<table class="rpt_table rpt_table-bordered rpt_padding">
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:20%">'.$dbtemp_name.'</td>	
				<td class="rptbx2" style="width:40%">DOS : '.$start_date.'</td>					
				<td class="rptbx3" style="width:40%">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
			</tr>
			</table>
			<table class="rpt_table rpt_table-bordered">
			<tr>
				<td class="text_b_w alignCenter" style="width:5%;">#</td>
				<td class="text_b_w alignCenter" style="width:20%;">Facility</td>				
				<td class="text_b_w alignCenter" style="width:25%;">Total</td>
				<td class="text_b_w alignCenter" style="width:10%;">Removed</td>
				<td class="text_b_w alignCenter" style="width:10%;">Locked/Blocked</td>
				<td class="text_b_w alignCenter" style="width:15%;">Booked</td>
				<td class="text_b_w alignCenter" style="width:15%;">Open</td>
			</tr>
			</table>
			<table class="rpt_table rpt_table-bordered">
			'.$page_content.'
			</table>';

			$pdf_data= '
				<page backtop="11mm" backbottom="10mm">			
					<page_footer>
						<table style="width:100%;">
							<tr>
								<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
							</tr>
						</table>
					</page_footer>
					<page_header>
						<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
						<tr>
							<td class="rptbx1" style="width:20%">'.$dbtemp_name.'</td>	
							<td class="rptbx2" style="width:40%">From : '.$start_date.' To : '.$end_date.'</td>					
							<td class="rptbx3" style="width:40%">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
						</tr>
						</table>
						<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
						<tr>
							<td class="text_b_w alignCenter" style="width:5%;">#</td>
							<td class="text_b_w alignCenter" style="width:20%;">Facility</td>				
							<td class="text_b_w alignCenter" style="width:25%;">Total</td>
							<td class="text_b_w alignCenter" style="width:10%;">Removed</td>
							<td class="text_b_w alignCenter" style="width:10%;">Locked/Blocked</td>
							<td class="text_b_w alignCenter" style="width:15%;">Booked</td>
							<td class="text_b_w alignCenter" style="width:15%;">Open</td>
						</tr></table>
					</page_header>
				<table style="width:100%" class="rpt_table rpt_table-bordered"  style="width:100%">'.
				$page_content
				.'</table>
				</page>';						
	} // outermost IF	


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