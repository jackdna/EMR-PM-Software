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
?>
<?php
require_once($GLOBALS['srcdir']."/classes/cls_common_function.php");

$opArr = array('Ordered','In Progress','Completed');

//--- GET ALL ASSOCIATED ORDERS WITH CHART NOTES --------
$sql = "select order_set_associate_chart_notes.*,
		date_format(order_set_associate_chart_notes.created_date,'%m-%d-%Y %H:%s %i') 
		as created_date_show,users.lname,users.fname,users.mname
		from order_set_associate_chart_notes 
		left join users on users.id = order_set_associate_chart_notes.logged_provider_id
		where order_set_associate_chart_notes.patient_id = '$patient_id' 
		and order_set_associate_chart_notes.plan_num = '$plan_num'
		and order_set_associate_chart_notes.form_id = '$formId'
		order by order_set_associate_chart_notes.created_date desc,
		order_set_associate_chart_notes.order_set_id desc";
$ordersetQryRes = $objManageData->mysqlifetchdata($sql);
$orders_rows_set = false;
$savedOrderSetIdArr = array();
for($i=0,$q=0;$i<count($ordersetQryRes);$i++){
	$orders_rows_set = true;
	$order_set_associate_id = $ordersetQryRes[$i]['order_set_associate_id'];
	$order_set_id = $ordersetQryRes[$i]['order_set_id'];	
	$phyName = $ordersetQryRes[$i]['fname'][0];
	$phyName .= $ordersetQryRes[$i]['lname'][0];
	$phyName = strtoupper(trim($phyName));
	if($phyName[0] == ','){
		$phyName = substr($phyName,1);
	}
	//--- GET ALL ORDER SET DETAILS --------
	if($order_set_id > 0){
		$display = 'table-row';
		$class = 'text_10';
		$orderset_name = $orderSetNameArr[$order_set_id];
		$saved_order_set_options = preg_replace('/__/',', ',$ordersetQryRes[$i]['order_set_options']);
		$set_delete_status = $ordersetQryRes[$i]['delete_status'];
		$deleted_set_rows = '';
		if($set_delete_status > 0){
			$class = 'strikeStyl';
			$deleted_set_rows = 'name="delected_rows_id[]" id="delected_rows_id[]"';
			$action = '';
			$display = 'none';
		}
		else{
			if($order_set_associate_id != $edit_id){
				$savedOrderSetIdArr[$order_set_id] = $order_set_id;
			}
			$action = "
				<a href=\"chart_notes_order_set.php?edit_id=$order_set_associate_id&plan_num=$plan_num&audit_view=1\" title=\"Edit\">
					<img src=\"../../library/images/edit.png\" border=\"0\">
				</a>
				<a href=\"javascript:void(0)\" onClick=\"c_delete('$order_set_associate_id','$plan_num')\" title=\"Delete\">
					<img src=\"../../library/images/del.png\" border=\"0\">
				</a>
			";
		}
		
		//--- GET ALL ORDERS DATA UNDER SINGLE ORDER SET -----
		$sql = "select order_set_associate_chart_notes_details.* from order_set_associate_chart_notes_details
				left join order_details on order_details.id = order_set_associate_chart_notes_details.order_id				
				where order_set_associate_id = '$order_set_associate_id'
				order by order_details.name";
		$ordersQryRes = $objManageData->mysqlifetchdata($sql);
		$orders_data = '';
		$plans_data_arr = array();
		for($o=0;$o<count($ordersQryRes);$o++){
			$main_id = $ordersQryRes[$o]['order_set_associate_details_id'];
			$order_id = $ordersQryRes[$o]['order_id'];
			$orders_details_arr = $ordersDetailsArr[$order_id];
			$schDis = 'table-cell';
			$infDis = 'none';
			$class1 = $class;
			$display1 = $display;
			$delete_status = $ordersQryRes[$o]['delete_status'];
			$instruction = addslashes(trim($ordersQryRes[$o]['instruction_information_txt']));
			$instruction = preg_replace('/\n/'," ",$instruction);
			$orders_options = preg_replace('/__/',', ',$ordersQryRes[$o]['orders_options']);			
			$orders_name = $orders_details_arr['name'];
			$plan_orders_name = $orders_details_arr['name'];
			
			//--- ORDER INFORMATIONAL CHECK ---
			$o_type = $orders_details_arr['o_type'];
			preg_match('/Information/',$o_type,$infCheck);
			if(count($infCheck)>0){
				$schDis = 'none';
				$infDis = 'table-cell';
			}
			
			if(trim($instruction) != ''){
				$plan_orders_name .= ' ( '.$instruction. ' ) ';
			}
			
			$statusChk = false;			
			//--- ORDER RESP PARTY CHECK -------
			$resp_person_arr = preg_split('/,/',$order_arr['resp_person']);
			if(count($resp_person_arr)>0 and trim($_SESSION['authId']) != ''){
				for($f=0;$f<count($resp_person_arr);$f++){
					$personId = trim($resp_person_arr[$f]);					
					if($personId == $_SESSION['authId']){
						$statusChk = true;
						break;
					}
				}
			}
			
			//--- ORDER RES GROUP CHECK -----
			$resp_group_arr = preg_split('/,/',$order_arr['resp_group']);
			if(count($resp_group_arr)>0 and trim($_SESSION['authGroupId']) != ''){
				for($f=0;$f<count($resp_group_arr);$f++){
					$personId = trim($resp_group_arr[$f]);
					if($personId == $_SESSION['authGroupId']){
						$statusChk = true;
						break;
					}
				}
			}
			
			//---- ORDERS STATUS CHECK -----			
			$status_option = '';
			$provider_status = $ordersQryRes[$o]['orders_status'];
			for($p=0;$p<count($opArr);$p++){
				$sel = $p == $provider_status ? 'selected="selected"' : '';
				$status_option .= <<<DATA
					<option value="$p" $sel>$opArr[$p]</option>
DATA;
			}
			
			$statusDis = 'none';
			$statusDis1 = 'table-cell';
			$status_option_val = $opArr[$provider_status];
			if($statusChk == false || ($user_type == 1 || $user_type == 12) || $user_type == 3){
				$statusDis = 'table-cell';
				$statusDis1 = 'none';
			}
			$deleted_rows = '';
			if($delete_status > 0){
				$class1 = 'strikeStyl';
				$display1 = 'none';
				$deleted_rows = 'name="delected_rows_id[]" id="delected_rows_id[]"';
				$instruction = $ordersQryRes[$o]['orders_reason_text'];
				$statusDis = 'none';
				$statusDis1 = 'table-cell';
				$schDis = 'none';
				$infDis = 'table-cell';
			}
			
			//--- ENTRY IN ALLERGIES TABLE -----
			if($provider_status == 2){
				$pid = $_SESSION['patient'];
				$sql = "select count(*) as rowCount from lists where pid='$pid' and (type='5' or type='6')
						and title = '$plan_orders_name'";
				$qryRes = $objManageData->mysqlifetchdata($sql);
				if($qryRes[0]['rowCount'] == 0){
					$dataArr = array();
					$dataArr['pid'] = $pid;
					$dataArr['type'] = '6';
					$dataArr['title'] = $plan_orders_name;
					$insertId = AddRecords($dataArr,'lists');
				}
			}
			
			$orders_site_text = $ordersQryRes[$o]['orders_site_text'];
			$orders_when_text = $ordersQryRes[$o]['orders_when_day_txt'];
			$orders_when_text .= ' '.$ordersQryRes[$o]['orders_when_text'];
			$orders_priority_text = $ordersQryRes[$o]['orders_priority_text'];
			
			if($delete_status == 0){
				$plans_data_arr['orders_name'][] = $plan_orders_name;
				$plans_data_arr['site_text'][] = $orders_site_text;
				$plans_data_arr['schedule'][] = trim($orders_when_text);
				$plans_data_arr['priority'][] = addslashes($orders_priority_text);
				$plans_data_arr['options'][] = addslashes($orders_options);
			}
			
			$orders_data .= <<<DATA
				<tr height="26" valign="top" $deleted_rows style="display:$display1">
					<td class="$class1" bgcolor="#FFFFFF">&nbsp;</td>
					<td class="$class1" bgcolor="#FFFFFF">$orders_name</td>
					<td class="$class1" bgcolor="#FFFFFF" style="display:$schDis;">
						$orders_site_text
					</td>
					<td class="$class1" bgcolor="#FFFFFF" style="display:$schDis;">
						$orders_when_text
					</td>
					<td class="$class1" bgcolor="#FFFFFF" style="display:$schDis;">
						$orders_priority_text
					</td>
					<td class="$class1" colspan="3" bgcolor="#FFFFFF" style="display:$infDis;">
						$instruction
					</td>
					<td class="$class1" bgcolor="#FFFFFF">
						$orders_options
					</td>					
					<td class="$class1" bgcolor="#FFFFFF" style="display:$statusDis">
						<select name="change_order_status[$main_id][]" class="form-control minimal" style="width:90px;">
							$status_option
						</select>
					</td>
					<td class="$class1" bgcolor="#FFFFFF" style="display:$statusDis1">$status_option_val</td>
					<td class="$class1" bgcolor="#FFFFFF"></td>
					<td class="$class1" bgcolor="#FFFFFF"></td>
				</tr>
DATA;
		}
		
		//--- GET DATA FOR PLANS --------
		$unique_site_arr = @array_unique($plans_data_arr['site_text']);
		$header_data = $orderset_name;
		$site_unique = false;
		if(trim($unique_site_arr[0]) != '' and count($unique_site_arr) == 1){
			$header_data .= ' - '.addslashes($unique_site_arr[0]);
			$site_unique = true;
		}
		
		//if Order set do not have orders attached , then add day in single line after site
		if(count($plans_data_arr['orders_name'])==1 && trim($plans_data_arr['orders_name'][0])==""){
			$tmp_day = $plans_data_arr['schedule'][0];
			$header_data .= ' - '.$tmp_day;
		}
		
		if(trim($saved_order_set_options) != ''){
			$header_data .= ' - Option '.addslashes($saved_order_set_options);
		}
		$unique_priority_arr = @array_unique($plans_data_arr['priority']);
		$unique_priorty = false;
		if(trim($unique_priority_arr[0]) != '' and count($unique_priority_arr) == 1){
			$unique_priorty = true;
			$header_data .= ' Priority - '.addslashes($unique_priority_arr[0]);
		}
		
		//--- GET ORDERS DETAIL FOR PLANS UNDER SINGLE ORDER SET -----
		$schedule_arr = $plans_data_arr['schedule'];
		$orders_dis_arr = array();
		for($ps=0;$ps<count($schedule_arr);$ps++){
			$day = $schedule_arr[$ps];
			$order_data = $plans_data_arr['orders_name'][$ps];
			if($site_unique == false and ($plans_data_arr['site_text'][$ps]) != ''){
				$order_data .= ' ('.addslashes($plans_data_arr['site_text'][$ps]).')';
			}			
			if(trim($plans_data_arr['options'][$ps]) != ''){
				$order_data .= ' - Option '.addslashes($plans_data_arr['options'][$ps]);
			}
			if($unique_priorty == false and ($plans_data_arr['priority'][$ps]) != ''){
				$order_data .= ' Priority - '.addslashes($plans_data_arr['priority'][$ps]);
			}
			$orders_dis_arr[$day][] = $order_data;
		}
		$orders_dis_key_arr = array_keys($orders_dis_arr);
		if(count($orders_dis_key_arr) >0){
			for($h=0;$h<count($orders_dis_key_arr);$h++){
				$day = $orders_dis_key_arr[$h];
				$tmp_all_ordrs = join(', ',$orders_dis_arr[$day]); 
				if(strpos($header_data, $day)===false){	$header_data .= '\\n         '.$day.' ';	}
				$header_data .= $tmp_all_ordrs;
			}
		}
		if($set_delete_status == 0){
			$contentArr[] = $header_data; 
		}
		
		$file_content .= <<<DATA
			<tr height="26" valign="top" $deleted_set_rows style="display:$display">
				<td class="$class" bgcolor="#F4F9EE">$orderset_name</td>
				<td class="$class" bgcolor="#F4F9EE"></td>
				<td class="$class" bgcolor="#F4F9EE"></td>
				<td class="$class" bgcolor="#F4F9EE"></td>
				<td class="$class" bgcolor="#F4F9EE"></td>
				<td class="$class" bgcolor="#F4F9EE">$saved_order_set_options</td>
				<td class="$class" bgcolor="#F4F9EE" nowrap="nowrap"></td>
				<td class="$class" bgcolor="#F4F9EE">$phyName</td>
				<td class="$class" bgcolor="#F4F9EE" nowrap="nowrap">$action</td>
			</tr>
			$orders_data
DATA;
	}
	//--- GET SINGLE ORDERS DETAILS ------
	else{
		$sql = "select 
									order_id, order_set_associate_details_id, orders_site_text,orders_when_day_txt,
									orders_when_text,orders_priority_text,orders_options,instruction_information_txt,
									orders_status,delete_status
									from order_set_associate_chart_notes_details
				where order_set_associate_id = '$order_set_associate_id'";				
		$ordersQryRes = $objManageData->mysqlifetchdata($sql);		
		$display = 'table-row';
		$class = 'text_10';
		$schDis = 'table-cell';
		$infDis = 'none';
		
		$order_id = $ordersQryRes[0]['order_id'];
		$main_id = $ordersQryRes[0]['order_set_associate_details_id'];
		$orders_details_arr = $ordersDetailsArr[$order_id];
		$orders_name = $orders_details_arr['name'];
		$site_text_str = $ordersQryRes[0]['orders_site_text'];
		$orders_when_text = $ordersQryRes[0]['orders_when_day_txt'];
		$orders_when_text .= ' '.$ordersQryRes[0]['orders_when_text'];
		$priority_text_str = $ordersQryRes[0]['orders_priority_text'];
		$setOptionStr = preg_replace('/__/',', ',$ordersQryRes[0]['orders_options']);
		$orders_header_data = $orders_details_arr['name'];
		if(trim($site_text_str) != ''){
			$orders_header_data .= ' ('.$site_text_str.')';
		}
		if(trim($orders_when_text) != ''){
			$orders_header_data .= ' - '.$orders_when_text;
		}
		//--- ORDER INFORMATIONAL CHECK ---
		$o_type = $orders_details_arr['o_type'];
		preg_match('/Information/',$o_type,$infCheck);
		$instruction = '';
		if(count($infCheck)>0){
			$schDis = 'none';
			$infDis = 'table-cell';
			$instruction = addslashes($ordersQryRes[0]['instruction_information_txt']);
		}
		
		if(trim($instruction) == ''){
			$instruction = 	addslashes($orders_details_arr['instruction']);
		}
		$instruction = preg_replace('/\n/'," ",$instruction);
		if(trim($instruction) != ''){
			$orders_header_data .= ' ( '.addslashes($instruction).' ) ';
		}
		
		if(trim($priority_text_str) != ''){
			$orders_header_data .= ' Priority - '.addslashes($priority_text_str);
		}
		
		$setOptionStr = preg_replace('/[^a-zA-Z0-9 ,-_`%]/',' ',$setOptionStr);
		if(trim($setOptionStr) != ''){
			$orders_header_data .= ' - Option '.addslashes($setOptionStr);
		}	
		
		//---- ORDERS STATUS CHECK -----			
		$status_option = '';
		$provider_status = $ordersQryRes[0]['orders_status'];
		for($p=0;$p<count($opArr);$p++){
			$sel = $p == $provider_status ? 'selected="selected"' : '';
			$status_option .= <<<DATA
				<option value="$p" $sel>$opArr[$p]</option>
DATA;
		}
		
		$class = 'text_10';
		$action = '';
		$showDropTxt = 'table-row';
		$showOpTxt = 'none';
		$status_option_val = $opArr[$ordersQryRes[0]['orders_status']];
		if($ordersQryRes[0]['delete_status'] > 0){
			$class = "strikeStyl";
			$deleted_rows = 'name="delected_rows_id[]" id="delected_rows_id[]"';
			$display = 'none';			
			$showDropTxt = 'none';
			$showOpTxt = 'table-row';
		}
		else{
			if($order_set_associate_id != $edit_id){
				$savedOrderId[] = $order_id;
			}
			$contentArr1[] = addslashes($orders_header_data);
			$strtalpa=strtoupper(substr($orders_name,0,1));
			$action = "
				<a href=\"chart_notes_order_set.php?edit_id=$order_set_associate_id&plan_num=$plan_num&audit_view=1&strtalpa=$strtalpa\" title=\"Edit\">
					<img src=\"../../library/images/edit.png\" border=\"0\">
				</a>
				<a href=\"javascript:void(0)\" onClick=\"c_delete('$order_set_associate_id','$plan_num')\" title=\"Delete\">
					<img src=\"../../library/images/del.png\" border=\"0\">
				</a>
			";
		}
		
		//--- ENTRY IN ALLERGIES TABLE -----
		if($provider_status == 2){
			$pid = $_SESSION['patient'];
			$sql = "select count(*) as rowCount from lists where pid='$pid' and (type='5' or type='6')
					and title = '$orders_name'";
			$qryRes = $objManageData->mysqlifetchdata($sql);
			if($qryRes[0]['rowCount'] == 0){
				$dataArr = array();
				$dataArr['pid'] = $pid;
				$dataArr['type'] = '6';
				$dataArr['title'] = $orders_name;
				$insertId = AddRecords($dataArr,'lists');
			}
		}

		$bgcolor = $q%2 == 0 ? '#F4F9EE' : '#FFFFFF';
		$q++;
		$order_file_content .= <<<DATA
			<tr height="26" valign="top" $deleted_rows style="display:$display">
				<td class="$class" bgcolor="$bgcolor"></td>
				<td class="$class" bgcolor="$bgcolor">$orders_name</td>
				<td class="$class" bgcolor="$bgcolor" style="display:$schDis;">$site_text_str</td>
				<td class="$class" bgcolor="$bgcolor" style="display:$schDis;">$orders_when_text</td>
				<td class="$class" bgcolor="$bgcolor" style="display:$schDis;">$priority_text_str</td>
				<td class="$class" colspan="3" bgcolor="$bgcolor" style="display:$infDis;">
					$instruction
				</td>
				<td class="$class" bgcolor="$bgcolor">$setOptionStr</td>
				<td class="$class" bgcolor="$bgcolor" style="display:$showDropTxt">
					<select name="change_order_status[$main_id][]" class="form-control minimal" style="width:90px;">
						$status_option
					</select>
				</td>
				<td class="$class" bgcolor="$bgcolor" style="display:$showOpTxt">
					$status_option_val
				</td>
				<td class="$class" bgcolor="$bgcolor">$phyName</td>
				<td class="$class" bgcolor="$bgcolor">$action</td>
			</tr>
DATA;
	}
}

//--- RECORD NOT EXISTS -----
if(count($ordersetQryRes) == 0){
	$file_content = <<<DATA
		<tr height="26">
			<td colspan="8" class="bgcolor failureMsg" width="100%" align="center">No Record Found.</td>
		</tr>
DATA;
}

if($orders_rows_set == true){
	$page_data = <<<DATA
		<table width="100%" border="0" cellspacing="1" cellpadding="1" class="showordersets">		
			<tr height="25" style="background-color: #1b9e95; color: #fff;">
				<td class="text_10b"  width="16%">Order Set</td>
				<td class="text_10b"  width="15%">Order</td>
				<td class="text_10b"  width="10%">Site</td>
				<td class="text_10b"  width="12%">Schedule</td>
				<td class="text_10b"  width="10%">Priority</td>
				<td class="text_10b"  width="12%">Options</td>
				<td class="text_10b"  width="12%">Status</td>
				<td class="text_10b"  width="7%">OP</td>
				<td class="text_10b"  width="6%">Action</td>
			</tr>
			$file_content
			$order_file_content
		</table>
DATA;
}
?>