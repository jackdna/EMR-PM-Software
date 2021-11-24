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

$savedOrdersArr = array();
if(!empty($edit_id)){//if start
//--- GET ORDER SET DETAILS FOR UPDATION ------
$sql = "select order_set_id,order_set_options
		from order_set_associate_chart_notes where order_set_associate_id = '$edit_id'";
$getSetQryRes = $objManageData->mysqlifetchdata($sql);
$saved_order_set_id = $getSetQryRes[0]['order_set_id'];
$saved_order_set_options_arr = preg_split('/__/',$getSetQryRes[0]['order_set_options']);

//--- GET SINGLE ORDERS DETAILS UNDER SAVED ORDER SET FOR UPDATION ----
$sql = "select * from order_set_associate_chart_notes_details 
		where order_set_associate_id = '$edit_id'";
$getOrdersQryRes = $objManageData->mysqlifetchdata($sql);
for($o=0;$o<count($getOrdersQryRes);$o++){
	$order_id = $getOrdersQryRes[$o]['order_id'];
	$savedOrdersArr[$order_id] = $getOrdersQryRes[$o];
}
//---- ADD MORE ORDER SETS -----
if(count($savedOrderSetIdArr)>0){
	$savedOrderSetIdStr = join(',',array_unique($savedOrderSetIdArr));
}
}//ifclose

$sql = "select id,orderset_name,order_id,order_set_option from order_sets where delete_status = '0' and orderset_name like '".$z_tmpalpa."%' ";
if(empty($savedOrderSetIdStr) == false){
	$sql .= " and id not in($savedOrderSetIdStr)";
}
$sql .= " order by orderset_name";

$orderSetArr = $objManageData->mysqlifetchdata($sql);
$selectedOrderset = false;
for($i=0,$j=0;$i<count($orderSetArr);$i++,$j++){
	$id = $orderSetArr[$i]['id'];
	$priTxtDis = 'none';
	$priDropDis = 'inline';
	$drop_dis = 'disabled="disabled"';
	$orderDayDis = 'none';
	$rowDis = 'none';
	$orderSetSel = NULL;	
	if($saved_order_set_id == $id){
		$selectedOrderset = true;
		$drop_dis = '';
		$orderSetSel = 'checked="checked"';
		$rowDis = 'table-row';
	}
	
	//--- GET ALL ORDERS DETAILS UNDER SINGLE ORDER SET ----
	$order_id_arr = preg_split('/,/',$orderSetArr[$i]['order_id']);
	$ordersData = '';
	for($o=0;$o<count($order_id_arr);$o++){
		$order_id = $order_id_arr[$o];
				
		//--- GET SINGLE ORDER DETAILS ------
		$saved_order_arr = $savedOrdersArr[$order_id];
		$order_id_details = $ordersDetailsArr[$order_id];
		$order_name = ucfirst($order_id_details['name']);
		$reasonDis = 'none';
		$informationDis = 'none';
		$orderDis = 'table-cell'; //'inline';		
		$o_type = $order_id_details['o_type'];		
		preg_match('/Information/',$o_type,$infCheck);
		if(count($infCheck) > 0){
			$informationDis = 'table-cell'; //'inline';
			$orderDis = 'none';			
		}
		$instruction = preg_replace('/[^a-zA-Z0-9 ,-_ %`]/',' ',$order_id_details['instruction']);
		if(trim($saved_order_arr['instruction_information_txt']) != ''){
			$instruction = trim($saved_order_arr['instruction_information_txt']);
		}
		
		//--- ORDER DELETE STATUS -----
		$ordersSel = 'checked="checked"';
		$reasonData = '';
		if($saved_order_arr['delete_status'] > 0){
			$ordersSel = '';
			$reasonDis = 'inline';
			$informationDis = 'none';
			$orderDis = 'none';			
			$reasonData = $saved_order_arr['orders_reason_text'];
		}
		
		//--- GET OPTIONS FOR SINGLE ORDER ----
		$ordersOptionArr = preg_split('/__/',$saved_order_arr['orders_options']);
		$orders_option_arr = preg_split('/\n/',$order_id_details['order_set_option']);
		$orders_option_chkbox = '';
		for($op=0;$op<count($orders_option_arr);$op++){
			$op_name = trim($orders_option_arr[$op]);
			$op_sel = '';
			if(count($ordersOptionArr)>0 and empty($orderSetSel) === false){
				if(in_array($op_name,$ordersOptionArr) == true){
					$op_sel = 'checked="checked"';
				}
			}		
			if(empty($op_name) == false){				
				if(!empty($orders_option_chkbox)){$orders_option_chkbox.="<br/>";}
				$orders_option_chkbox .=<<<DATA
					<span>
					<div class="checkbox">
					<input type='checkbox' $op_sel style="cursor:pointer;" id="orders_options$order_id$op" name="orders_options[$order_id][]" value="$op_name">
					<label for="orders_options$order_id$op" >$op_name</label></div>
					</span>
DATA;
			}
		}
		
		if($orders_option_chkbox != ''){
			$orders_option_chkbox = <<<DATA
				<tr class="text_10b" name="orderSet_row[$id][]" style="display:$rowDis">
					<td bgcolor="#DDE6D7" align="center"></td>
					<td class="options_orders" bgcolor="#DDE6D7" colspan="4" valign="top">$orders_option_chkbox</td>
				</tr>
DATA;
		}
		
		//---- SITE DROP DOWN FOR SINGLE ORDER ------
		$orders_site_text = $saved_order_arr['orders_site_text'];
		$siteDropTxt = '';
		$siteTxtVal = true;
		for($d=0;$d<count($siteDropArr);$d++){
			$val = $siteDropArr[$d];
			$sel = '';
			if($val == trim($orders_site_text) and empty($ordersSel) == false){
				$siteTxtVal = false;
				$sel = 'selected="selected"';
			}
			$siteDropTxt .=<<<DATA
				<option value="$val" $sel>$val</option>
DATA;
		}
		
		$priTxtDis = 'none';
		$priDropDis = 'inline';
		$priTxtVal = '';
		if($orders_site_text != '' and $siteTxtVal == true){
			$priTxtDis = 'inline';
			$priDropDis = 'none';
			$priTxtVal = $orders_site_text;
		}
		
		//---- GET SCHEDULE DROP DOWN FOR SINGLE ORDER ------	
		$orders_when_text = $saved_order_arr['orders_when_text'];	
		$whenDropTxt = '';
		$orderDayDis = 'none';
		for($w=0;$w<count($whenDropArr);$w++){
			$val = $whenDropArr[$w];
			$whenSel = '';
			if($val == $orders_when_text and empty($ordersSel) == false){
				$dayDataArr = array('Days','Weeks','Months','Years');
				if(in_array($val,$dayDataArr) == true){
					$orderDayDis = 'inline';
				}
				$whenSel = 'selected="selected"';
			}
			$whenDropTxt.= <<<DATA
				<option value="$val" $whenSel>$val</option>
DATA;
		}
		
		//---- GET DAY DROP DOWN FOR ORDERS IN ORDER SET ---
		$orders_when_day_txt = $saved_order_arr['orders_when_day_txt'];
		$dayOption = '';
		for($d=0;$d<count($dayArr);$d++){
			$daySel = '';
			if(($d+1) == $orders_when_day_txt and empty($ordersSel) == false){
				$daySel = 'selected="selected"';
			}
			$dayOption .= <<<DATA
				<option value="$dayArr[$d]" $daySel>$dayArr[$d]</option>
DATA;
		}
		
		//--- GET PRIORITY DROP DOWN AND TEXT VALUE FOR ORDERS IN ORDER SET ------	
		$orders_priority_text = $saved_order_arr['orders_priority_text'];
		$priorityDropTxt = '';
		$prSelval = true;
		for($p=0;$p<count($priorityDropArr);$p++){
			$val = $priorityDropArr[$p];
			$prsel = '';			
			if($val == $orders_priority_text and empty($ordersSel) == false){
				$prSelval = false;
				$prsel = 'selected="selected"';
			}
			$priorityDropTxt .= <<<DATA
				<option value="$val" $prsel>$val</option>
DATA;
		}
		$priorityTxtDis = 'none';
		$priorityDropDis = 'inline';
		$sel_order_set_priority_text = '';
		if(trim($orders_priority_text) != '' and $prSelval == true){
			$priorityTxtDis = 'inline';
			$priorityDropDis = 'none';
			$sel_order_set_priority_text = $orders_priority_text;
		}
		
		
		
		$ordersData .= <<<DATA
			<tr height="30" valign="top" name="orderSet_row[$id][]" style="display:$rowDis" bgcolor="#DDE6D7">
				<td class="text_10" style="padding-left:10px; cursor:pointer;">
					<div class="checkbox">
					<input type="checkbox" id="new_orderSetOrders$id$o" name="new_orderSetOrders[$id][]" value="$order_id" onClick="show_orders(this.checked,'$j','$o','$id','$o_type');" $ordersSel>
					<label for="new_orderSetOrders$id$o" >$order_name</label></div>
				</td>
				<td class="text_9b" id="site_td_id_$j$o" style="display:$orderDis;">
					<span name="new_orders_text_val_id[$id][]" id="new_orders_text_val_id[$id][]" style="display:$priTxtDis;">
						<input type="text" class="input_text_10" size="8" name="new_orders_site_txt_id[$id][]" value="$priTxtVal" onBlur="otherOrderVal(this,'$o','site','$id');">
					</span>
					<span name="new_orders_option_val_id[$id][]" id="new_orders_option_val_id[$id][]" style="display:$priDropDis;">
						<select name="new_orders_site_id[$id][]" style="width:70px;" class="form-control minimal input_text_10" onChange="otherOrderVal(this,'$o','site','$id');">
							<option value="">Site</option>
							$siteDropTxt
						</select>
					</span>
				</td>
				<td class="text_10b" nowrap="nowrap" width="60" id="day_td_id_$j$o" style="display:$orderDis;">
					<span name="new_schedule_day_td[$id][]" style="width:50px; display:$orderDayDis;">
					<select style="width:40px;" class="form-control minimal input_text_10" name="new_orders_set_when_day[$id][]">
						$dayOption
					</select>
					</span>
				</td>
				<td class="text_10b" nowrap="nowrap" id="schedule_td_id_$j$o" style="display:$orderDis;">
					<select name="new_order_schedule[$id][]" class="form-control minimal input_text_10" onChange="ordersShceduleDay(this,'$id','$o');">
						<option value="">Schedule</option>
						$whenDropTxt
					</select>
				</td>
				<td class="text_10b" id="priority_td_id_$j$o" style="display:$orderDis;">
					<span name="new_priority_orders_text_span[$id][]" id="new_priority_orders_text_span[$id][]" style="display:$priorityTxtDis">
						<input type="text" class="input_text_10" size="8" name="new_orders_priority_txt[$id][]" value="$sel_order_set_priority_text" onBlur="otherOrderVal(this,'$o','priority','$id');">
					</span>
					<span name="new_priority_orders_option_span[$id][]" id="new_priority_orders_option_span[$id][]" style="display:$priorityDropDis">
						<select name="new_orders_priority[$id][]" style="width:70px;" class="form-control minimal input_text_10" onChange="otherOrderVal(this,'$o','priority','$id');">
							<option value="">Priority</option>
							$priorityDropTxt
						</select>
					</span>
				</td>
				<td class="text_10b" colspan="4" id="reason_td[$id][]" style="display:$reasonDis;">
					<input type="text" size="45" name="reasonTxtArr[$id][]" class="input_text_10" value="$reasonData" onBlur="changeReasonTxt(this,'$id','$o');"> 
				</td>
				<td class="text_10" colspan="4" id="information_td[$id][]" style="display:$informationDis;">
					<input type="text" size="45" name="information_name[$id][]" value="$instruction" class="input_text_10">
					<input type="hidden" name="orderType[$id][]" value="$o_type">
				</td>
			</tr>
			$orders_option_chkbox
DATA;
	}
	
	//--- GET ORDER SET OPTIONS -----
	$order_set_option_chkbox = '';
	$order_set_option_arr = preg_split('/\n/',$orderSetArr[$i]['order_set_option']);
	for($op=0;$op<count($order_set_option_arr);$op++){
		$op_name = trim($order_set_option_arr[$op]);
		$op_sel = '';
		if(count($saved_order_set_options_arr)>0){
			if(in_array($op_name,$saved_order_set_options_arr) === true){
				$op_sel = 'checked="checked"';
			}
		}		
		
		if(empty($op_name) == false){			
			if(!empty($order_set_option_chkbox)){$order_set_option_chkbox.="<br/>";}
			$order_set_option_chkbox .=<<<DATA
			<span>
			<div class="checkbox">
			<input type='checkbox' $op_sel style="cursor:pointer;" id="set_options$id$op" name="set_options[$id][]" $drop_dis id="set_options_$id" value="$op_name">
			<label for="set_options$id$op" >$op_name</label></div>
			</span>
DATA;
		}
	}
	
	if($order_set_option_chkbox != ''){
		$order_set_option_chkbox = <<<DATA
			<tr class="text_10b">
				<td bgcolor="#A7C091" align="center"></td>
				<td class="options_orders" bgcolor="#A7C091" colspan="4" valign="top">$order_set_option_chkbox</td>
			</tr>
DATA;
	}
	
	//---- SITE DROP DOWN FOR ORDER SET ------
	$siteOrdersDropTxt = '';
	for($d=0;$d<count($siteDropArr);$d++){
		$val = $siteDropArr[$d];
		$siteOrdersDropTxt .=<<<DATA
			<option value="$val">$val</option>
DATA;
	}
	
	//---- GET SCHEDULE DROP DOWN FOR SINGLE ORDER ------		
	$orderSetSchDropTxt = '';
	for($w=0;$w<count($whenDropArr);$w++){
		$val = $whenDropArr[$w];
		$orderSetSchDropTxt.= <<<DATA
			<option value="$val" $whenSel>$val</option>
DATA;
	}
	
	//---- GET DAY DROP DOWN FOR ORDER SET ---
	$dayOption = '';
	for($d=0;$d<count($dayArr);$d++){
		$dayOption .= <<<DATA
			<option value="$dayArr[$d]">$dayArr[$d]</option>
DATA;
	}
	
	//--- GET PRIORITY DROP DOWN AND TEXT VALUE FOR ORDER SET ------	
	$priorityDropTxt = '';
	$sel_order_set_priority_text = trim($allOrdersSavedDataArr[$order_id]['order_set_priority_text']);
	for($p=0;$p<count($priorityDropArr);$p++){
		$val = $priorityDropArr[$p];
		$priorityDropTxt .= <<<DATA
			<option value="$val">$val</option>
DATA;
	}
	//--- MAIN SINGLE ORDER SET DATA -------
	$OrderSetData .= <<<DATA
		<tr>
			<td>
				<div class="checkbox"><input type="checkbox" name="new_order_set[]" id="new_order_set$id$o" value="$id" style="cursor:pointer;" onClick="selectAll(this.checked,'$id','$j');" $orderSetSel>
				<label for="new_order_set$id$o" >$orderSetNameArr[$id]</label></div>
			</td>
			<td >
				<span id="new_orders_text_val_$j" style="display:none">
					<input type="text" class="form-control" size="8" name="new_orders_site_txt[$id]" value="" onBlur="hideOrderTxt(this,'$j','site','$id');">
				</span>
				<span id="new_orders_option_val_$j" style="display:inline">
					<select name="new_orders_site[$id]" id="set_site_drop_$id" style="width:70px;" $drop_dis class="form-control minimal " onchange="getOtherOrderVal(this,'$j','site','$id');">
						<option value="">Site</option>
						$siteOrdersDropTxt
					</select>
				</span>
			</td>
			<td >
				<span id="new_orders_when_day_$j" style="width:50px; display:$orderDayDis;">
				<select style="width:40px;" class="form-control minimal " id="set_day_drop_$id" $drop_dis name="new_orders_set_when_day[$id]" onChange="changeDayValue(this,'$id');">
					$dayOption
				</select>
				</span>
			</td>
			<td >
				<select name="new_orders_when[$id]" class="form-control minimal " id="set_when_drop_$id" $drop_dis onChange="selOrderSet('schedule',this,'$j','$id');">
					<option value="">Schedule</option>
					$orderSetSchDropTxt
				</select>
			</td>
			<td >
				<span id="new_priority_orders_text_val_$j" style="display:none">
					<input type="text" class="form-control" size="8" name="new_orders_priority_txt[$id]" value="" onBlur="hideOrderTxt(this,'$j','Priority','$id');">
				</span>
				<span id="new_priority_orders_option_val_$j" style="display:inline">
					<select name="new_orders_priority[$id]" id="set_priority_drop_$id" style="width:70px;" $drop_dis class="form-control minimal " onchange="getOtherOrderVal(this,'$j','Priority','$id');">
						<option value="">Priority</option>
						$priorityDropTxt
					</select>
				</span>
			</td>
		</tr>	
		$order_set_option_chkbox
		$ordersData
DATA;
}
?>