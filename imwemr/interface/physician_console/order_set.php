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
//require_once('../common/functions.inc.php');
require_once(dirname(__FILE__) . '/../../config/globals.php');
//require_once($GLOBALS['fileroot'].'/library/classes/msgConsole.php');
//--- EDIT ORDERS STATUS UNDER ORDER SET -------
if (count($change_order_status) > 0) {
	$orderSetId = array_keys($change_order_status);
	for ($i = 0; $i < count($orderSetId); $i++) {
		$id = $orderSetId[$i];
		$val = join(',', $change_order_status[$id]);
		$dataArr = array();
		$dataArr['orders_status'] = $val;
		$dataArr['modified_date'] = date('Y-m-d');
		$dataArr['modified_operator'] = $_SESSION['authId'];
		$insertId = UpdateRecords($id, 'order_set_associate_details_id', $dataArr, 'order_set_associate_chart_notes_details');
	}
	$msg = 'Status successfully changed.';
}

//--- GET ALL PROVIDER NAMES ----
$q1 = imw_query("select id,lname,fname,mname from users");
$providerDetails = array();
while ($usersQryRes = imw_fetch_assoc($q1)) {
	$id = $usersQryRes['id'];
	$name = $usersQryRes['lname'] . ', ';
	$name .= $usersQryRes['fname'] . ' ';
	$name .= $usersQryRes['mname'];
	$name = ucwords(trim($name));
	if ($name[0] == ',') {
		$name = substr($name, 1);
	}
	$providerDetails[$id] = $name;
}

//--- GET ALL ORDER SET DETAILS ----
$q2 = imw_query("select * from order_sets order by createdy_on desc");
$orderSetArr = array();
while ($orderSetDetails = imw_fetch_assoc($q2)) {
	$id = $orderSetDetails['id'];
	$orderSetArr[$id] = $orderSetDetails;
}

//--- GET ALL ORDERS DETAILS ----
$q3 = imw_query("select * from order_details order by created_on desc");
$ordersDetailsArr = array();
while ($ordersQryRes = imw_fetch_assoc($q3)) {
	$id = $ordersQryRes['id'];
	$ordersDetailsArr[$id] = $ordersQryRes;
}

$operatorId = $_SESSION['authId'];
$operatorGroupId = $_SESSION['authGroupId'];

//--- GET ALL ORDER SET AND ORDERS AS LOGGED PROVIDER -----
if (empty($operatorGroupId) == false) {
	$q4 = imw_query("select id from order_details where delete_status = '0' and (resp_group = '$operatorGroupId' or resp_group like '%,$operatorGroupId,%'
			or resp_group like '%,$operatorGroupId' or resp_group like '$operatorGroupId,%')");
}
$ordersIdArr = array();
while ($ordersDetails = imw_fetch_assoc($q4)) {
	$id = $ordersDetails['id'];
	$ordersIdArr[] = $ordersDetails['id'];
}
$ordersIdStr = join(',', $ordersIdArr);
$opArr = array('Ordered', 'In Progress', 'Completed');

$ordersIdStr_phrs = "";
if (!empty($ordersIdStr)) {
	$ordersIdStr_phrs = " order_set_associate_chart_notes_details.order_id in ($ordersIdStr) OR ";
}

//--- GET ALL ASSOCIATED ORDERS WITH CHART NOTES --------
$q5 = imw_query("select order_set_associate_chart_notes.order_set_associate_id as primary_set_id,
		order_set_associate_chart_notes.order_set_id,
		order_set_associate_chart_notes.patient_id ,
		order_set_associate_chart_notes.logged_provider_id ,
		order_set_associate_chart_notes.order_set_options,
		date_format(order_set_associate_chart_notes.created_date,'" . get_sql_date_format('', 'y') . "') as c_date,
		order_set_associate_chart_notes.logged_provider_id ,
		order_set_associate_chart_notes.delete_status as set_delete_status,
		order_set_associate_chart_notes.order_set_reason_text ,
		order_set_associate_chart_notes_details.*,
		patient_data.lname,patient_data.fname,patient_data.mname
		from order_set_associate_chart_notes left join
		order_set_associate_chart_notes_details on
		order_set_associate_chart_notes.order_set_associate_id = 
		order_set_associate_chart_notes_details.order_set_associate_id
		join patient_data on patient_data.id = 
		order_set_associate_chart_notes.patient_id
		where order_set_associate_chart_notes_details.orders_status != '2'
		and order_set_associate_chart_notes_details.delete_status = '0'
		and ( " . $ordersIdStr_phrs . " " .
		" (order_set_associate_chart_notes_details.resp_person = '$operatorId' or order_set_associate_chart_notes_details.resp_person like '%,$operatorId,%' " .
		"	or order_set_associate_chart_notes_details.resp_person like '%,$operatorId' or order_set_associate_chart_notes_details.resp_person like '$operatorId,%') " .
		" )
		order by order_set_associate_chart_notes.created_date desc");
$exists_order_set = false;

$previous_primary_set_id = 0;
while ($ordersQryRes = imw_fetch_assoc($q5)) {
	$row_class = ($row_class != "even-odd-test-task-1") ? "even-odd-test-task-1" : "even-odd-test-task-2";
	$exists_order_set = true;
	$order_set_id = $ordersQryRes['order_set_id'];
	$patient_name = $ordersQryRes['lname'] . ', ';
	$patient_name .= $ordersQryRes['fname'] . ' ';
	$patient_name .= $ordersQryRes['mname'];
	$patient_name = trim(ucfirst($patient_name));
	if ($patient_name[0] == ',') {
		$patient_name = substr($patient_name, 1);
	}
	$patient_name .= ' - ' . $ordersQryRes['patient_id'];
	//$patient_name = '<a class="a_clr1" title="Click to load Chart" href="javascript:;" onclick="top.LoadWorkView(\''.$ordersQryRes['patient_id'].'\');">'.$patient_name.'</a>';
	$patient_name = '<span class="text_purple" onclick="top.LoadWorkView(\'' . $ordersQryRes['patient_id'] . '\');">' . $patient_name . '</span>';
	//---  GET ALL ORDER SETS  ---------
	if ($order_set_id > 0) {
		$c_date = $ordersQryRes['c_date'];
		$order_id = $ordersQryRes['order_id'];
		$main_id = $ordersQryRes['order_set_associate_details_id'];
		$class = 'text_10';
		$showDis = 'table-row';
		$infDis = 'none';
		$order_detail_arr = $ordersDetailsArr[$order_id];
		$order_name = $order_detail_arr['name'];
		$orders_site_text = $ordersQryRes['orders_site_text'];
		$orders_when_text = $ordersQryRes['orders_when_day_txt'];
		$orders_when_text .= ' ' . $ordersQryRes['orders_when_text'];
		$orders_priority_text = $ordersQryRes['orders_priority_text'];
		$delete_status = $ordersQryRes['delete_status'];
		$orders_reason_text = $ordersQryRes['orders_reason_text'];
		$orders_options = preg_replace('/__/', ', ', $ordersQryRes['orders_options']);
		$logged_provider_id = $ordersQryRes['logged_provider_id'];
		//--- OPERATOR INITIAL ------
		$provider_name_arr = preg_split('/, /', $providerDetails[$logged_provider_id]);
		$provider_name = $provider_name_arr[1][0];
		$provider_name .= $provider_name_arr[0][0];
		$provider_name = strtoupper($provider_name);
		$instruction = '';
		$o_type = $order_detail_arr['o_type'];
		preg_match('/Information/', $o_type, $infCheck);
		$tmp = trim($ordersQryRes['instruction_information_txt']);
		$tmp_con = trim($ordersQryRes['template_content']);
		if (count($infCheck) > 0 || !empty($tmp)) {
			$showDis = 'none';
			$infDis = 'table-row';
			$instruction = $ordersQryRes['instruction_information_txt'];
		}
		//---- ORDERS STATUS CHECK -----			
		$status_option = '';
		$provider_status = $ordersQryRes['orders_status'];
		for ($p = 0; $p < count($opArr); $p++) {
			$sel = $p == $provider_status ? 'selected="selected"' : '';
			$status_option .= '<option value="'.$p.'" '.$sel.'>'.$opArr[$p].'</option>';
		}
		$primary_set_id = $ordersQryRes['primary_set_id'];
		if ($primary_set_id != $previous_primary_set_id) {
			$order_set_arr = $orderSetArr[$order_set_id];
			$orderset_name = $order_set_arr['orderset_name'];
			$order_set_options = preg_replace('/__/', ', ', $ordersQryRes['order_set_options']);
			$orderSetContentData .= '<tr class="'.$row_class.'">
					<td class="'.$class.'" nowrap="nowrap">'.$c_date.'</td>
					<td class="'.$class.'">'.$patient_name.'</td>
					<td class="'.$class.'">'.$orderset_name.'</td>
					<td class="'.$class.'"></td>
					<td class="'.$class.'">&nbsp;</td>
					<td class="'.$class.'">&nbsp;</td>
					<td class="'.$class.'">&nbsp;</td>
					<td class="'.$class.'">'.$order_set_options.'</td>
					<td class="'.$class.'">'.$provider_name.'</td>
					<td class="'.$class.'">&nbsp;</td>
				</tr>';

			$previous_primary_set_id = $primary_set_id;
		}

		//--- ENTRY IN ALLERGIES TABLE -----
		if ($provider_status == 2) {
			$pid = $_SESSION['patient'];
			$q6 = imw_query("select count(*) as rowCount from lists where pid='$pid' and (type='5' or type='6')
					and title = '$order_name'");
			$qryRes = imw_fetch_assoc($q6);
			if (imw_num_rows($q6) == 0) {
				$dataArr = array();
				$dataArr['pid'] = $pid;
				$dataArr['type'] = '6';
				$dataArr['title'] = $order_name;
				$insertId = AddRecords($dataArr, 'lists');
			}
		}
		
		//Instruction
		$inst_data="";
		if(!empty($instruction)|| !empty($tmp_con)){
			if(!empty($tmp_con)){
				$tmp_con = html_entity_decode($tmp_con);
				$tmp_con = trim($tmp_con,"\\n");				
				$tmp_con = nl2br($tmp_con);
				$tmp_con = trim($tmp_con);
				//if(!empty($instruction)){ $instruction.= "<br/>"; }
			}
			$inst_data = '
			<tr class="'.$row_class.'">
				<td class="'.$class.' purple_bar" nowrap="nowrap">Instruction</td>
				<td class="'.$class.'" colspan="9">'.$instruction.$tmp_con.'</td>
			</tr>	';
		}

		$orderSetContentData .= '<tr class="'.$row_class.'">
				<td class="'.$class.'" nowrap="nowrap">&nbsp;</td>
				<td class="'.$class.'">&nbsp;</td>
				<td class="'.$class.'">&nbsp;</td>
				<td class="'.$class.'">'.$order_name.'</td>
				<td class="'.$class.'" >'.$orders_site_text.'</td>
				<td class="'.$class.'" >'.$orders_when_text.'</td>
				<td class="'.$class.'" >'.$orders_priority_text.'</td>
				<td class="'.$class.'">'.$orders_options.'</td>
				<td class="'.$class.'"></td>
				<td class="'.$class.'">
					<select name="change_order_status['.$main_id.'][]" class="form-control minimal" onChange="frm_sub();">
						'.$status_option.'
					</select>
				</td>
			</tr>'.$inst_data.' ';
		
	} else {
		//---  GET ALL SINGLE ORDERS WITHOUT ORDER SETS ---------
		$c_date = $ordersQryRes['c_date'];
		$order_id = $ordersQryRes['order_id'];
		$main_id = $ordersQryRes['order_set_associate_details_id'];
		$class = 'text_10';
		$bgcolor = $q % 2 == 0 ? '#F4F9EE' : '#FFFFFF';
		$q++;
		$showDis = 'table-row';
		$infDis = 'none';
		$order_detail_arr = $ordersDetailsArr[$order_id];
		$order_name = $order_detail_arr['name'];
		$orders_site_text = $ordersQryRes['orders_site_text'];
		$orders_when_text = $ordersQryRes['orders_when_day_txt'];
		$orders_when_text .= ' ' . $ordersQryRes['orders_when_text'];
		$orders_priority_text = $ordersQryRes['orders_priority_text'];
		$delete_status = $ordersQryRes['delete_status'];
		$orders_reason_text = $ordersQryRes['orders_reason_text'];
		$orders_options = preg_replace('/__/', ', ', $ordersQryRes['orders_options']);
		$logged_provider_id = $ordersQryRes['logged_provider_id'];
		//--- OPERATOR INITIAL ------
		$provider_name_arr = preg_split('/, /', $providerDetails[$logged_provider_id]);
		$provider_name = $provider_name_arr[1][0];
		$provider_name .= $provider_name_arr[0][0];
		$provider_name = strtoupper($provider_name);
		$instruction = '';
		$o_type = $order_detail_arr['o_type'];
		preg_match('/Information/', $o_type, $infCheck);
		$tmp = trim($ordersQryRes['instruction_information_txt']);
		$tmp_con = trim($ordersQryRes['template_content']);
		if (count($infCheck) > 0 || !empty($tmp)) {
			$showDis = 'none';
			$infDis = 'table-row';
			$instruction = $ordersQryRes['instruction_information_txt'];
		}
		//---- ORDERS STATUS CHECK -----			
		$status_option = '';
		$provider_status = $ordersQryRes['orders_status'];
		for ($p = 0; $p < count($opArr); $p++) {
			$sel = $p == $provider_status ? 'selected="selected"' : '';
			$status_option .= '<option value="'.$p.'" '.$sel.'>'.$opArr[$p].'</option>';
		}

		//--- ENTRY IN ALLERGIES TABLE -----
		if ($provider_status == 2) {
			$pid = $_SESSION['patient'];
			$q7 = imw_query("select count(*) as rowCount from lists where pid='$pid' and (type='5' or type='6') and title = '$order_name'");
			$qryRes = imw_fetch_assoc($q7);
			if (imw_num_rows($q7) == 0) {
				$dataArr = array();
				$dataArr['pid'] = $pid;
				$dataArr['type'] = '6';
				$dataArr['title'] = $order_name;
				$insertId = AddRecords($dataArr, 'lists');
			}
		}
		
		//Instruction
		$inst_data="";
		
		if(!empty($instruction)|| !empty($tmp_con)){
			if(!empty($tmp_con)){
				$tmp_con = html_entity_decode($tmp_con);
				$tmp_con = trim($tmp_con,"\\n");				
				$tmp_con = nl2br($tmp_con);
				$tmp_con = trim($tmp_con);
				//if(!empty($instruction)){ $instruction.= "<br/>"; }
			}
			$inst_data = '
				<tr class="'.$row_class.'">
				<td class="'.$class.' purple_bar" nowrap="nowrap">Instruction</td>
				<td class="'.$class.'" colspan="9">'.$instruction.$tmp_con.'</td>
				</tr>';
		}			

		$ordersContentData .= '<tr class="'.$row_class.'">
				<td class="'.$class.'" nowrap="nowrap">'.$c_date.'</td>
				<td class="'.$class.'">'.$patient_name.'</td>
				<td class="'.$class.'">&nbsp;</td>
				<td class="'.$class.'">'.$order_name.'</td>
				<td class="'.$class.'" >'.$orders_site_text.'</td>
				<td class="'.$class.'" >'.$orders_when_text.'</td>
				<td class="'.$class.'" >'.$orders_priority_text.'</td>
				<td class="'.$class.'">'.$orders_options.'</td>
				<td class="'.$class.'">'.$provider_name.'</td>
				<td class="'.$class.'">
					<select name="change_order_status['.$main_id.'][]" class="form-control minimal" onChange="frm_sub();">
						'.$status_option.'
					</select>
				</td>
			</tr>'.$inst_data.' ';

	}
}
?>

<form name="order_frm" id="order_frm" action="" method="post">
	<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding" id="ap_pol_prov" style="height:<?php echo ($_SESSION['wn_height'] - 330); ?>px;overflow-y:auto;overflow-x:hidden;">
		<?php
			if ($exists_order_set == true) {
				?>
		<table class="table table-bordered" id="tests-tasks">
				<thead>
					<tr class="purple_bar">
						<th>Date</th>
						<th>Patient Name</th>
						<th>Order Set Name</th>
						<th>Order Name</th>
						<th>Site</th>
						<th>Schedule</th>
						<th>Priority</th>
						<th style="min-width:100px">Options</th>
						<th>Ass. By</th>
						<th>Status</th>
					</tr>
				</thead>
				<?php
				print $orderSetContentData;
				?>
				<?php
				print $ordersContentData;
			?>
		</table>
			<?php	} else {
				?>
				
					<div class="alert alert-danger">No Record Exists.</div>
				
				<?php
			}
			?>	
	</div>
</form>