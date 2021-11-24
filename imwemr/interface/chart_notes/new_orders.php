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

$savedOrderId = @array_unique($savedOrderId);
@sort($savedOrderId);
if($savedOrderId[0] == ''){
	@array_shift($savedOrderId);
}

//--- GET ALL NON SAVED ORDERS ------
$savedOrdersIdStr = @join(',',$savedOrderId);

//--- GET SINGLE ORDERS DETAILS ----
$sql = "select id,name,order_set_option from order_details where delete_status = '0' AND name like '".$z_tmpalpa."%' ";
if(empty($savedOrdersIdStr) == false){
	$sql .= " and id not in ($savedOrdersIdStr)";
}
$sql .= " order by name";
$ordersQryRes = $objManageData->mysqlifetchdata($sql);

$newOrdersData = '';
for($o=0;$o<count($ordersQryRes);$o++,$j++){
	$id = $ordersQryRes[$o]['id'];
	
	//---  GET SAVED ORDER DETAIL FOR UPDATION ----	
	$sql = "select order_id,instruction_information_txt,orders_options,orders_site_text,orders_when_text,orders_when_day_txt,orders_priority_text from order_set_associate_chart_notes_details 
			where order_set_associate_id = '$edit_id' 
			and order_id = '$id'";
	$getOrdersQryRes = $objManageData->mysqlifetchdata($sql);
	
	//--- GET SINGLE ORDER DETAILS ------
	$order_id_details = $ordersDetailsArr[$id];
	$name = $order_id_details['name'];
	$drop_dis = 'disabled="disabled"';
	$siteTxtDis = 'none';
	$siteDropDis = 'inline';
	$informationDis = 'none';
	$orderDis = 'table-cell'; //'inline';
	$dayDis = 'none';
	$order_sel = '';
	if($id == $getOrdersQryRes[0]['order_id'] and $selectedOrderset == false){
		$drop_dis = '';
		$order_sel = 'checked="checked"';
	}
	$o_type = $order_id_details['o_type'];
	preg_match('/Information/',$o_type,$infCheck);
	if(count($infCheck) > 0){
		$informationDis = 'table-cell';
		$orderDis = 'none';
	}
	$instruction = preg_replace('/[^a-zA-Z0-9 ,-_ %`]/',' ',$order_id_details['instruction']);
	if(empty($getOrdersQryRes[0]['instruction_information_txt']) == false){
		$instruction = trim($getOrdersQryRes[0]['instruction_information_txt']);
	}
	
	//---- OPTIONS FOR SINGLE ORDERS -----
	$order_set_option_arr = preg_split('/\n/',$ordersQryRes[$o]['order_set_option']);
	$saved_options_arr = preg_split('/__/',$getOrdersQryRes[0]['orders_options']);
	$orders_option_data = '';
	for($op=0;$op<count($order_set_option_arr);$op++){
		$op_name = trim($order_set_option_arr[$op]);
		$op_sel = '';
		if(count($saved_options_arr) > 0 and empty($edit_id) === false){
			if(in_array($op_name,$saved_options_arr) === true){
				$op_sel = 'checked="checked"';
			}
		}
		if(empty($op_name) == false){
			if(!empty($orders_option_data)){ $orders_option_data.="<br/>"; }
			$orders_option_data .= <<<DATA
				<span>
					<div class="checkbox"><input type="checkbox" style="cursor:pointer;" $drop_dis $op_sel name="orders_option_arr[$id][]" id="orders_option_arr$id$op" value="$op_name">
					<label for="orders_option_arr$id$op" >$op_name</label></div>
				</span>
DATA;
		}
	}
	
	if($orders_option_data != ''){
		$orders_option_data = <<<DATA
			<tr bgcolor="#DDE6D7">
				<td></td>
				<td class="options_orders" colspan="4" valign="top">$orders_option_data</td>
			</tr>
DATA;
	}
	
	//---- SITE DROP DOWN FOR SINGLE ORDER ------
	$orders_site_text = $getOrdersQryRes[0]['orders_site_text'];
	$siteDropTxt = '';
	$siteTxtVal = true;
	for($d=0;$d<count($siteDropArr);$d++){
		$val = $siteDropArr[$d];
		$sel = '';
		if($val == trim($orders_site_text) and empty($order_sel) == false){
			$siteTxtVal = false;
			$sel = 'selected="selected"';
		}
		$siteDropTxt .=<<<DATA
			<option value="$val" $sel>$val</option>
DATA;
	}
	
	$siteTxtDis = 'none';
	$siteDropDis = 'inline';
	$siteTxtData = '';
	if($orders_site_text != '' and $siteTxtVal == true and empty($order_sel) == false){
		$siteTxtDis = 'inline';
		$siteDropDis = 'none';
		$siteTxtData = $orders_site_text;
	}
	
	//---- GET SCHEDULE DROP DOWN FOR SINGLE ORDER ------	
	$orders_when_text = $getOrdersQryRes[0]['orders_when_text'];	
	$whenDropTxt = '';
	$orderDayDis = 'none';
	for($w=0;$w<count($whenDropArr);$w++){
		$val = $whenDropArr[$w];
		$whenSel = '';
		if($val == $orders_when_text and empty($order_sel) == false){			
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
	$orders_when_day_txt = $getOrdersQryRes[0]['orders_when_day_txt'];
	$dayOption = '';
	for($d=0;$d<count($dayArr);$d++){
		$daySel = '';
		if(($d+1) == $orders_when_day_txt and empty($order_sel) == false){
			$daySel = 'selected="selected"';
		}
		$dayOption .= <<<DATA
			<option value="$dayArr[$d]" $daySel>$dayArr[$d]</option>
DATA;
	}
	
	//--- GET PRIORITY DROP DOWN AND TEXT VALUE FOR ORDERS IN ORDER SET ------	
	$orders_priority_text = $getOrdersQryRes[0]['orders_priority_text'];
	$priorityDropTxt = '';
	$prSelval = true;
	for($p=0;$p<count($priorityDropArr);$p++){
		$val = $priorityDropArr[$p];
		$prsel = '';			
		if($val == $orders_priority_text and empty($order_sel) == false){
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
	
	if(empty($name) == false){		
		$name_s = (strlen($name) > 50) ? substr($name,0,50).".." : $name;
		$newOrdersData .= <<<DATA
			<tr height="25" valign="top" bgcolor="#DDE6D7">
				<td class="text_10b" style="cursor:pointer;" nowrap="nowrap" title="$name" >
					<div class="checkbox"><input type="checkbox" name="new_orders[]" id="new_orders$id"  value="$id" $order_sel onClick="show_orders_val(this.checked,'$o_type','$id');">
					<label for="new_orders$id" >$name_s</label></div>
				</td>
				<td class="text_10b" id="orders_site_td_$id" style="display:$orderDis">
					<span id="newOrdersTextVal_$j" style="display:$siteTxtDis">
						<input type="text" class="form-control" size="8" name="newOrdersSiteTxt[$id]" value="$siteTxtData" onBlur="getOtherOrdersValue(this,'$j','site','$id');">
					</span>
					<span style="display:$siteDropDis" id="newOrdersOptionVal_$j">
						<select name="newOrdersSite[$id]" id="newOrdersSite_$id" style="width:70px;" $drop_dis class="form-control minimal" onchange="getOtherOrdersValue(this,'$j','site','$id');">
							<option value="">Site</option>
							$siteDropTxt
						</select>
					</span>
				</td>
				<td class="text_10b" nowrap="nowrap" style="display:$orderDis">
					<span id="new_orders_when_day_$j" style="width:50px; display:$orderDayDis;">
					<select style="width:40px;" class="form-control minimal" $drop_dis name="new_orders_when_day[$id]" id="new_orders_day_$id">
						$dayOption
					</select>
					</span>
				</td>
				<td class="text_10b" style="display:$orderDis">
					<select name="newOrdersWhen[$id]" id="newOrdersWhen_$id" class="form-control minimal" $drop_dis onchange="getOtherOrdersValue(this,'$j','schedule','$id');">
						<option value="">Schedule</option>
						$whenDropTxt
					</select>
				</td>
				<td class="text_10b" style="display:$orderDis">
					<span id="newPriorityOrdersTextVal_$j" style="display:$priorityTxtDis">
						<input type="text" class="form-control" size="6" name="newOrdersPriorityText[$id]" value="$sel_order_set_priority_text" onBlur="getOtherOrdersValue(this,'$j','Priority','$id');">
					</span>
					<span id="newPriorityOrdersOptionVal_$j" style="display:$priorityDropDis">
						<select name="newOrdersPriority[$id]" id="newOrdersPriority_$id" style="width:70px;" $drop_dis class="form-control minimal" onchange="getOtherOrdersValue(this,'$j','Priority','$id');">
							<option value="">Priority</option>
							$priorityDropTxt
						</select>
					</span>
				</td>
				<td class="text_10" colspan="4" style="display:$informationDis;">
					<input type="text" class="form-control" $drop_dis size="50" value="$instruction" name="order_information_$id" id="order_information_$id">
				</td>
				
			</tr>
			$orders_option_data
			
DATA;
		}
}

?>