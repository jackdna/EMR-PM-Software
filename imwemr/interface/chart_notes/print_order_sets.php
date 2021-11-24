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
		and order_set_associate_chart_notes.delete_status = '0'
		order by order_set_associate_chart_notes.created_date desc,
		order_set_associate_chart_notes.order_set_id desc";
$ordersetQryRes = $clsCommon->mysqlifetchdata($sql);
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
		if($order_set_associate_id != $edit_id){
			$savedOrderSetIdArr[$order_set_id] = $order_set_id;
		}
		
		//--- GET ALL ORDERS DATA UNDER SINGLE ORDER SET -----
		$sql1 = "select order_set_associate_chart_notes_details.* from order_set_associate_chart_notes_details
				join order_details on order_details.id = order_set_associate_chart_notes_details.order_id				
				where order_set_associate_chart_notes_details.order_set_associate_id = '$order_set_associate_id' 
				and order_set_associate_chart_notes_details.delete_status = '0'
				order by order_details.name";
		$ordersQryRes = $clsCommon->mysqlifetchdata($sql1);
		$orders_data = '';
		$plans_data_arr = array();
		for($o=0;$o<count($ordersQryRes);$o++){
			$main_id = $ordersQryRes[$o]['order_set_associate_details_id'];
			$order_id = $ordersQryRes[$o]['order_id'];
			$orders_details_arr = $ordersDetailsArr[$order_id];

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
			}
			
			//---- ORDERS STATUS CHECK -----			
			$status_option = $opArr[$ordersQryRes[$o]['orders_status']];
			
			$orders_site_text = $ordersQryRes[$o]['orders_site_text'];
			$orders_when_text = $ordersQryRes[$o]['orders_when_day_txt'];
			$orders_when_text .= ' '.$ordersQryRes[$o]['orders_when_text'];
			$orders_priority_text = $ordersQryRes[$o]['orders_priority_text'];		
			
			$orders_data .= <<<DATA
				<tr>
					<td valign="top">$orders_name</td>
					<td valign="top">
						$orders_site_text
					</td>
					<td valign="top" width="330">
						$instruction
					</td>
					<td valign="top" width="150" style="padding-left:10px;">
						$orders_options
					</td>
				</tr>
DATA;
		}
		
		$file_content .= <<<DATA
			$orders_data
DATA;
	}
	//--- GET SINGLE ORDERS DETAILS ------
	else{
		$sql2 = "select * from order_set_associate_chart_notes_details
				where order_set_associate_id = '$order_set_associate_id' and delete_status = '0'";
		$ordersQryRes = $clsCommon->mysqlifetchdata($sql2);		
		
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

		//--- ORDER INFORMATIONAL CHECK ---
		$o_type = $orders_details_arr['o_type'];
		preg_match('/Information/',$o_type,$infCheck);
		$instruction = NULL;
		$schDis = NULL;
		if(count($infCheck)>0){
			$schDis = 'none';
			$instruction = addslashes($ordersQryRes[0]['instruction_information_txt']);
		}
		
		if(trim($instruction) == ''){
			$instruction = 	addslashes($orders_details_arr['instruction']);
		}
		
		$instruction = preg_replace('/\n/'," ",$instruction);
		
		$setOptionStr = preg_replace('/[^a-zA-Z0-9 ,-_`%]/',' ',$setOptionStr);
		
		//---- ORDERS STATUS CHECK -----			
		$order_status = $opArr[$ordersQryRes[0]['orders_status']];
		
		$order_file_content .= <<<DATA
			<tr valign="top">
				<td>$orders_name</td>
				<td valign="top">
					$site_text_str
				</td>
				<td valign="top" width="330">
					$instruction
				</td>
				<td valign="top" width="150" style="padding-left:10px;">
					$setOptionStr
				</td>
			</tr>
DATA;
	}
}


if($orders_rows_set == true){
	$page_data = <<<DATA
		<table width="100%" border="0" cellspacing="0" cellpadding="0">		
			<tr>
				<td bgcolor="#6C7100" style="color:#FFFFFF" width="200">Order</td>
				<td bgcolor="#6C7100" style="color:#FFFFFF" width="40">Site</td>
				<td bgcolor="#6C7100" style="color:#FFFFFF" width="330">Instructions</td>
				<td bgcolor="#6C7100" style="color:#FFFFFF" width="150">Options</td>
			</tr>
			$file_content
			$order_file_content
		</table>
DATA;
}
?>