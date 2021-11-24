<?php
$page_header_val = '
<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
		<tr>
			<td class="rptbx1" style="width:33%;">Monitoring Report '.ucfirst($report_view).'</td>
			<td class="rptbx2" style="width:34%;">From '.$Start_date.' To '.$End_date.'</td>
			<td class="rptbx3" style="width:33%;">Created by: '.$op_name.' on '.$curDate.'</td>
		</tr>
</table>';
$csvpdfData ="";
if($task_type== 'reminders' || $task_type == ""){
	if(count($userFutureArr)){
	$userFutureMsgHtml="";
	foreach($userFutureArr as $userFuturekey => $userFutureval){
		$userFutureUserName = $providerNameArr[$userFuturekey];
		$userFutureMsgHtml .="<tr><td class='text_b_w' colspan='6'>Operator: ".$userFutureUserName."</td></tr>";
		$counter = 1;
		foreach($userFutureval as $subuserFutureval){
			$rowId=$subuserFutureval['user_message_id'];
			$message_to = $subuserFutureval['message_to'];
			$assigned_to = $providerNameArr[$message_to];
			$message_sender_id = $subuserFutureval['message_sender_id'];
			$created_by = $providerNameArr[$message_sender_id];
			$userFutureMsgHtml .="<tr>
					<td align=\"center\" onClick=\"updateAssigne('$rowId', '$message_sender_id', 'user_messages', 'message_sender_id', 'user_message_id')\"; class='text_10b_purpule pointer'>".$counter."</td>	
					<td>".$subuserFutureval["msg_send_date"]."</td>
					<td>".$subuserFutureval["delivery_date"]."</td>
					<td>".$subuserFutureval["message_subject"]."</td>
					<td>".$assigned_to."</td>
					<td>".$created_by."</td>
				</tr>";
				$counter++;
			}
		}
		$userFutureMsghtmlDetial = '
			<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<tr><td colspan="6" class="text_b_w">Reminders</td></tr>
			<tr>
				<td class="text_b_w" style=\'width:50px;\' align=\"center\" class=\"text_b_w\">Sr. No.</td>
				<td class="text_b_w" style=\'width:190px;\' align=\"center\" class=\"text_b_w\">Date</td>
				<td class="text_b_w" style=\'width:190px;\' align=\"center\" class=\"text_b_w\">Delivery Date</td>
				<td class="text_b_w" style=\'width:190px;\' align=\"center\" class=\"text_b_w\">Message Subject</td>
				<td class="text_b_w" style=\'width:190px;\' align=\"center\" class=\"text_b_w\">Assigned to</td>
				<td class="text_b_w" style=\'width:190px;\' align=\"center\" class=\"text_b_w\">Created by</td>
			</tr>';
			$csvpdfData .= $userFutureMsghtmlDetial.$userFutureMsgHtml.'</table>';
	}
}

if($task_type== 'tasks' || $task_type == ""){	
	if(count($testsArr)){
	$testsHtml="";
	foreach($testsArr as $testskey => $testsArrval){
		$testUserName = $providerNameArr[$testskey];
		$testsHtml .="<tr><td class='text_b_w' colspan='5'>Operator: ".$testUserName."</td></tr>";
		$counter = 1;
		foreach($testsArrval as $subtestsArrval){
			
			$rowId=$subtestsArrval['main_id'];
			$ordrby=$subtestsArrval['ordrby'];
			$testName=$subtestsArrval['testName'];
			$id_colums_Name=$subtestsArrval['id_colums_Name'];
			
			$pQuery=imw_query("select if(TRIM(patient_data.fname)!='',CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id),'') AS patient_name from patient_data where id='".$subtestsArrval['patient_id']."'");
			$pData=imw_fetch_array($pQuery);
			$pt_Name = $pData['patient_name'];
			$testsHtml .="<tr>
					<td align=\"center\" onClick=\"updateAssigne($rowId, '$ordrby', '$testName', 'ordrby', '$id_colums_Name')\"; class='text_10b_purpule pointer'>".$counter."</td>
					<td>".$pt_Name."</td>
					<td>".$subtestsArrval["testDesc"]."</td>
					<td>".$subtestsArrval["comments"]."</td>
					<td>".$subtestsArrval["taskDate"]."</td>
				</tr>";
				$counter++;
			}
		}
		$testsHtmlhtmlDetial = '
			<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<tr><td colspan="5" class="text_b_w">Tasks / Tests</td></tr>
			<tr>
				<td class="text_b_w" style=\'width:50px;\' align=\"center\" class=\"text_b_w\">Sr. No.</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Patient Name - Id</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Test Desc.</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Test Comments</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Task Date</td>
			</tr>';
		$csvpdfData .= $testsHtmlhtmlDetial.$testsHtml.'</table>';
	}
}

if($task_type== 'messages' || $task_type == ""){
	if(count($userMsgArr)){
	$userMsgHtml="";
	foreach($userMsgArr as $userMsgArrkey => $userMsgArrval){
		$userMsgUserName = $providerNameArr[$userMsgArrkey];
		$userMsgHtml .="<tr><td class='text_b_w' colspan='4'>Operator: ".$userMsgUserName."</td></tr>";
		$counter = 1;
		foreach($userMsgArrval as $subuserMsgArrval){
			$rowId=$subuserMsgArrval['user_message_id'];
			$message_to=$subuserMsgArrval['message_to'];
			$userMsgHtml .="<tr>
					<td align=\"center\" onClick=\"updateAssigne($rowId, $message_to, 'user_messages', 'message_to', 'user_message_id')\"; class='text_10b_purpule pointer'>".$counter."</td>
					<td>".$subuserMsgArrval["message_subject"]."</td>
					<td>".$userMsgUserName."</td>
					<td>".$subuserMsgArrval["msg_send_date"]."</td>
				</tr>";
				$counter++;
			}
		}
		$userMsghtmlDetial = '
			<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<tr><td colspan="4" class="text_b_w">User Messages</td></tr>
			<tr>
				<td class="text_b_w" style=\'width:50px;\' align=\"center\" class=\"text_b_w\">Sr. No.</td>
				<td class="text_b_w" style=\'width:330px;\' align=\"center\" class=\"text_b_w\">Message Subject</td>
				<td class="text_b_w" style=\'width:330px;\' align=\"center\" class=\"text_b_w\">Message Send By</td>
				<td class="text_b_w" style=\'width:330px;\' align=\"center\" class=\"text_b_w\">Message Send Date</td>
			</tr>';
		$csvpdfData .= $userMsghtmlDetial.$userMsgHtml.'</table>';
	}	
}	

if($task_type== 'orders' || $task_type == ""){	
	if(count($userOdrArr)){
	$userOdrMsgHtml="";
	foreach($userOdrArr as $userOdrkey => $userOdrval){
		$userOdrName = $providerNameArr[$userOdrkey];
		$userOdrName = $userOdrName ?: 'No Operator';
		$userOdrMsgHtml .="<tr><td class='text_b_w' colspan='5'>Operator: ".$userOdrName."</td></tr>";
		$counter = 1;
		foreach($userOdrval as $subuserOdrval){
		$pQuery=imw_query("select if(TRIM(patient_data.fname)!='',CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id),'') AS patient_name from patient_data where id='".$subuserOdrval['patient_id']."'");
		$pData=imw_fetch_array($pQuery);
		$pt_Name = $pData['patient_name'];	
		$order_id =	$subuserOdrval['order_id'];
		$order_detail_arr = $ordersDetailsArr[$order_id];
		$order_name = $order_detail_arr['name'];	
			
		$rowId=$subuserOdrval['order_set_associate_details_id'];
		$resp_person=$subuserOdrval['resp_person'];
		$userOdrMsgHtml .="<tr>
					<td align=\"center\" onClick=\"updateAssigne($rowId, '$resp_person', 'order_set_associate_chart_notes_details', 'resp_person', 'order_set_associate_details_id')\"; class='text_10b_purpule pointer'>".$counter."</td>
					<td>".$pt_Name."</td>
					<td>".$order_name."</td>
					<td>".$userOdrName."</td>
					<td>".$subuserOdrval["c_date"]."</td>
				</tr>";
				$counter++;
			}
		}
		$userOdrMsgHtmlDetial = '
			<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<tr><td colspan="5" class="text_b_w">Orders</td></tr>
			<tr>
				<td class="text_b_w" style=\'width:50px;\' align=\"center\" class=\"text_b_w\">Sr. No.</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Patient Name - ID</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Order Name</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Assigned by</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Date</td>
			</tr>';
		$csvpdfData .= $userOdrMsgHtmlDetial.$userOdrMsgHtml.'</table>';
	}
}	

if($task_type== 'to_do' || $task_type == ""){
	if(count($toDoArr)){
	$str_html="";
	foreach($toDoArr as $toDokey => $toDoval){
		$toDoUserName = $providerNameArr[$toDokey];
		$str_html .="<tr><td class='text_b_w' colspan='5'>Operator: ".$toDoUserName."</td></tr>";
		$counter = 1;
		foreach($toDoval as $subtoDoval){
			$rowId=$subtoDoval['id'];
			$provider_id=$subtoDoval['provider_id'];
			$str_html .="<tr>
					<td align=\"center\" onClick=\"updateAssigne($rowId, $provider_id, 'patient_notes', 'provider_id', 'id')\"; class='text_10b_purpule pointer'>".$counter."</td>	
					<td>".$subtoDoval["patientName"]. '-' .$subtoDoval["patient_id"]. "</td>
					<td>".$subtoDoval["patient_note"]."</td>
					<td>".$toDoUserName."</td>
					<td>".$subtoDoval["note_date"]."</td>
				</tr>";
				$counter++;
			}
		}
		$toDOhtmlDetial = '
			<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<tr><td colspan="5" class="text_b_w">To Do List</td></tr>
			<tr>
				<td class="text_b_w" style=\'width:50px;\' align=\"center\" class=\"text_b_w\">Sr. No.</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Patient Name-ID</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Patient Note</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Created by</td>
				<td class="text_b_w" style=\'width:240px;\' align=\"center\" class=\"text_b_w\">Date</td>
			</tr>';
		$csvpdfData .= $toDOhtmlDetial.$str_html.'</table>';
	}
}
if($csvpdfData){
	$printFile = 1;
	$op='l';
	$cssHTML =  '<style>' . file_get_contents('../css/reports_pdf.css') . '</style>';
	$pdfData = $page_header_val.$cssHTML.$csvpdfData;
	$file_location = write_html($pdfData);
	echo $page_header_val.$csvpdfData;
}else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>