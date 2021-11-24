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
require_once(dirname(__FILE__).'/../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html

ob_start();
$userId = $_SESSION['authId'];

//--- get all users ----  
$qry = imw_query("select id,lname,fname,mname from users");
$usernameArr = array();
while($userQryRes=imw_fetch_assoc($qry)){
	$id = $userQryRes['id'];
	$name = $userQryRes['lname'].', ';
	$name .= $userQryRes['fname'].' ';
	$name .= $userQryRes['mname'];
	if($name[0] == ','){
		$name = preg_replace("/, /","",$name);
	}
	$name = trim(ucfirst($name));
	$usernameArr[$id] = $name;
}

//--- GET ALL USER GROUPS ----
$qry = imw_query("select * from user_groups");
$groupsNameArr = array();
while($userGroupsQryRes=imw_fetch_assoc($qry)){
	$id = $userGroupsQryRes['id'];
	$groupsNameArr[$id] = $userGroupsQryRes;
}
//-- Get all task as responseble persone ----
$qry = imw_query("select phy_todo_task.*,
		date_format(phy_todo_task.task_created_date,'".get_sql_date_format('','y')."') as created_date,
		patient_data.lname,patient_data.fname,patient_data.mname
		from phy_todo_task left join patient_data on
		patient_data.id = phy_todo_task.patientId
		where phy_todo_task.task_by = '$userId'
		and task_to != '$userId'
		and phy_todo_task.task_status = '0' 
		order by phy_todo_task.phy_todo_task_id desc");
$msgData = '';
while($qryRes=imw_fetch_assoc($qry)){
	$phy_todo_task_id = $qryRes['phy_todo_task_id'];
	$task_subject12 = ucfirst($qryRes['task_subject']);
	$task_text12 = ucfirst($qryRes['task_text']); 
	$task_by = $qryRes['task_by'];
	//--- PATIENT NAME -----
	$patient_name = $qryRes['patient_name'];
	$patientId = $qryRes['patientId'];
	if($patientId>0 && empty($patient_name) != true){
		$patient_name .= ' - '.$patientId;
	}
	
	$created_date = $qryRes['created_date'];
	$task_status = $qryRes['task_status'];
	$task_read_status = $qryRes['task_read_status'];
	$status = 'Done';
	if($task_read_status == 0){
		$status = 'No Done';
	}
	
	$groupName = $qryRes['sent_to_groups'];
	if(is_numeric($groupName) == true){
		$groupName = '';
	}
	$username = $usernameArr[$task_by];
	
	$msgData .= '
		<tr>
			<td colspan="4" height="1px"></td>
		</tr>
		<tr>
			<td valign="top" class="text_9"> '.$created_date.'</td>
			<td class="text_9" colspan="2" valign="top">'.$task_subject12.'</td>
		</tr>
		<tr>			
			<td class="text_9" valign="top" width="300">'.$patient_name.'</td>
			<td class="text_9" valign="top" width="230">'.$username.'</td>
			<td class="text_9" valign="top" width="115">'.$groupName.'</td>
			<td class="text_9" valign="top" width="115">'.$status.'</td>
		</tr>
		<tr>
			<td colspan="4" class="text_9" valign="top">'.$task_text12.'</td>
		</tr>
		<tr>
			<td colspan="4" height="1px" bgcolor="#000000"></td>
		</tr>';
}

//-- Get all messages as responseble persone ----
$qry = imw_query("select user_messages.*,
		date_format(user_messages.message_send_date,'".get_sql_date_format('','y')."') as message_date ,
		patient_data.lname,patient_data.fname,patient_data.mname
		from user_messages left join patient_data on
		patient_data.id = user_messages.patientId
		where user_messages.message_sender_id = '$userId'
		and user_messages.message_to != '$userId'
		and user_messages.message_status = '0' 
		order by user_messages.user_message_id desc");
while($qryRes=imw_fetch_assoc($qry)){
	$message_text = ucfirst($qryRes['message_text']);
	$message_sender_id = $qryRes['message_sender_id']; 
	$message_date = $qryRes['message_date'];
	$message_subject = ucfirst($qryRes['message_subject']);
	//--- PATIENT NAME ------
	$patient_name = $qryRes['lname'].', ';
	$patient_name .= $qryRes['fname'].' ';
	$patient_name .= $qryRes['mname'];	
	$patient_name = ucwords(trim($patient_name));	
	if($patient_name[0] == ','){
		$patient_name = substr($patient_name,1);
	}
	$patient_name .= ' - '.$qryRes['patientId'];
	
	if($qryRes['patientId'] == 0){
		$patient_name = '';
	}
	//--- USER GROUPS ------
	$groupName = $qryRes['sent_to_groups'];
	if(is_numeric($groupName) == true){
		$groupName = '';
	}
	$username = $usernameArr[$message_sender_id];
	
	$msgData .= '
		<tr>
			<td colspan="4" height="1px"></td>
		</tr>
		<tr>
			<td valign="top" class="text_9">'.$message_date.'</td>
			<td class="text_9" colspan="2" valign="top">'.$message_subject.'</td>
		</tr>
		<tr>			
			<td class="text_9" valign="top" width="300">'.$patient_name.'</td>
			<td class="text_9" valign="top" width="230">'.$username.'</td>
			<td class="text_9" valign="top" width="115">'.$groupName.'</td>
			<td class="text_9" valign="top" width="115">'.$status.'</td>
		</tr>
		<tr>
			<td colspan="4" class="text_9" valign="top">'.$message_text.'</td>
		</tr>
		<tr>
			<td colspan="4" height="1px" bgcolor="#000000"></td>
		</tr>';
}

//--- GET ALL ORDER SETS NAME ----
$qry = imw_query("select * from order_sets");
$orderSetNameArr = array();
while($orderSetQryRes=imw_fetch_assoc($qry)){
	$id = $orderSetQryRes['id'];
	$orderset_name = $orderSetQryRes['orderset_name'];
	$orderSetNameArr[$id] = $orderset_name;
}

//--- GET ALL ORDERS NAME ----
$qry = imw_query("select * from order_details");
$ordersNameArr = array();
while($orderSetQryRes=imw_fetch_assoc($qry)){
	$id = $orderSetQryRes[$i]['id'];
	$ordersNameArr[$id] = $orderSetQryRes[$i];
}


//-- GET ALL ORDERS/ ORDER SET AS PERSONSIBLE PERSON ----
$QRY = imw_query("select distinct(order_set_associate_chart_notes.order_set_id),order_set_associate_chart_notes.order_id,
		order_set_associate_chart_notes.order_set_associate_id,patient_data.id as patient_id,
		patient_data.lname,patient_data.fname,patient_data.mname,
		date_format(order_set_associate_chart_notes.created_date,'".get_sql_date_format('','y')."') as created_date
		from order_set_associate_chart_notes join patient_data on 
		patient_data.id = order_set_associate_chart_notes.patient_id
		where logged_provider_id='$userId' 
		and delete_status='0'");

while($orderSetIdArr=imw_fetch_assoc($QRY)){
	$patient_id = $orderSetIdArr['patient_id'];	
	$orderSetId = $orderSetIdArr['order_set_id'];
	$order_set_associate_id = $orderSetIdArr['order_set_associate_id'];
	$created_date = $orderSetIdArr['created_date'];
	$orderset_name = $orderSetNameArr[$orderSetId];
	//-- GET PATIENT NAME ----
	$patientName = $orderSetIdArr['lname'].', ';
	$patientName .= $orderSetIdArr['fname'].' ';
	$patientName .= $orderSetIdArr['mname'];
	$patientName = ucwords(trim($patientName));
	if($patientName[0] == ','){
		$patientName = substr($patientName,1);
	}
	$patientName .= ' - '.$patient_id;
	
	//--- GET ORDERS NAME ----
	$orders_name_arr = array();
	$respPersonArr = array();
	$groupNameArr = array();
	$order_row_display = false;
	
	$orderIdArr = preg_split('/,/',$orderSetIdArr['order_id']);
	for($or=0;$or<count($orderIdArr);$or++){
		$id = trim($orderIdArr[$or]);
		if(empty($id) == false){			
			//--- GET RESPONSIBLE PERSON NAME -----
			$resp_person_arr = preg_split('/,/',$ordersNameArr[$id]['resp_person']);
			$resp = false;
			for($p=0;$p<count($resp_person_arr);$p++){
				$pid = trim($resp_person_arr[$p]);
				if($pid != $_SESSION['authId']){
					if(empty($usernameArr[$pid]) == false){
						$respPersonArr[$pid] = $usernameArr[$pid];
					}
					$resp = true;
				}
			}
			
			//--- GET RESPONSIBLE PERSON NAME -----
			$resp_group_arr = preg_split('/,/',$ordersNameArr[$id]['resp_group']);
			for($p=0;$p<count($resp_group_arr);$p++){
				$pid = trim($resp_group_arr[$p]);
				if(($pid != $_SESSION['authGroupId'] or $pid == '') and $_SESSION['authGroupId'] != ''){
					if(empty($groupsNameArr[$pid]['name']) == false){
						$groupNameArr[$pid] = $groupsNameArr[$pid]['name'];
					}
					$resp = true;
				}
			}
			
			if($resp == true){
				$orders_name_arr[] = $ordersNameArr[$id]['name'];
				$order_row_display = true;
			}
		}
	}	
	
	$orders_name_str = join(', ',$orders_name_arr);
	$respPersonStr = join(', ',$respPersonArr);
	$groupNameStr = join(', ',$groupNameArr);
	
	if(trim($username) != '' and $order_row_display == true){
		$msgData .= '
			<tr>
				<td colspan="4" height="1px"></td>
			</tr>
			<tr>
				<td valign="top" class="text_9">'.$created_date.'</td>
				<td class="text_9" colspan="2" valign="top">'.$orderset_name.'</td>
			</tr>
			<tr>			
				<td class="text_9" valign="top" width="300">'.$patientName.'</td>
				<td class="text_9" valign="top" width="230">'.$respPersonStr.'</td>
				<td class="text_9" valign="top" width="115">'.$groupNameStr.'</td>
				<td class="text_9" valign="top" width="115"></td>
			</tr>
			<tr>
				<td colspan="4" class="text_9" valign="top">'.$orders_name_str.'</td>
			</tr>';
	}
}

?>
<style>
.tb_heading{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#BCD5E1;
	border-style:solid;
	border-color:#FFFFFF;
	border-width: 1px; 
}
.text_b{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	background-color:#FFFFFF;
}
.text_9{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
}
.font_14b{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	background-color:#FFFFFF;
	border-style:solid;
	border-color:#000000;
	border-width: 1px;
}
</style>
<page backtop="5mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>
		<table width="100%">
			<tr>
				<td width="745" class="tb_heading">
					Responsible Person
				</td>
			</tr>	
		</table>
	</page_header>
	<table width="100%">
	<?php
		print $msgData;	
	?>
</table>
</page>
<?php
$strHTML = ob_get_contents();
ob_clean();
$file_location = write_html($strHTML);
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	top.html_to_pdf('<?php echo $file_location; ?>','p','',true,false);
</script>