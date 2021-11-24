<?php
if($_POST['form_submitted']){
	$dateFormat= get_sql_date_format();
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$curDate = date(phpDateFormat().' h:i A');	

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
	
	//--- CHANGE DATE FORMAT ---
	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);
	$operator_ids = implode(",",$_REQUEST['operator_id']);
	
	
	// MAKE Search Criteria Vars
	$varCriteria=$operator_ids.'~'.$dayReport.'~'.$startDate.'~'.$endDate.'~'.$task_type.'~'.$report_view;
	//---------------------
	
	$toDoArr = array();
	$toDo = "SELECT pn.id, pn.patient_id, pn.provider_id, pn.patient_note, pn.add_date, 
	date_format(pn.note_date,'".$dateFormat."') as note_date,
	CONCAT_WS(', ',pd.lname ,pd.fname) as patientName 
	FROM patient_notes pn
	JOIN patient_data pd ON pd.id=pn.patient_id
	WHERE 1=1 AND (pn.note_date BETWEEN '$startDate' AND '$endDate')";
	if($operator_ids){
		$toDo .= " and provider_id IN ($operator_ids) ";
	}
	$toDoQuery = imw_query($toDo);
	$toDoCount = imw_num_rows($toDoQuery);
	while($toDoRes = imw_fetch_assoc($toDoQuery)) {
		$id = $toDoRes['id'];
		$providerId = $toDoRes['provider_id'];
		if($providerId)
		$toDoArr[$providerId][] = $toDoRes; 
	}
	
	$userMsgArr = array();
	$userMsg = "SELECT user_messages.user_message_id, user_messages.message_read_status, user_messages.message_subject, user_messages.message_text, user_messages.message_urgent, CONCAT(users.fname,' ',users.mname,', ',users.lname) AS message_sender_name, user_messages.message_sender_id as message_sender_id, user_groups.name as user_group, if(TRIM(patient_data.fname)!='',CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id),'') AS patient_name, patient_data.id AS message_patient_id, DATE_FORMAT(message_send_date,'%m-%d-%Y %h:%i %p') AS msg_send_date, user_messages.flagged, user_messages.msg_icon, user_messages.message_to FROM user_messages USE INDEX (usermsg_multicol) LEFT JOIN patient_data ON patient_data.id = user_messages.patientId LEFT JOIN users ON user_messages.message_sender_id = users.id LEFT JOIN user_groups ON user_groups.id = users.user_group_id where user_messages.message_status = 0 and user_messages.receiver_delete=0 and user_messages.message_sender_id > 0 and user_messages.delivery_date <= CURDATE() and user_messages.saved_folder_id=0 and user_messages.Pt_Communication = 0 and user_messages.message_sender_id != 0 
	AND DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'";
	if($operator_ids){
		$userMsg .=" and user_messages.message_to IN ($operator_ids) ";
	}
	$userMsg .=" order by user_messages.user_message_id desc";
	$userMsgQuery = imw_query($userMsg);
	while($userMsgRes = imw_fetch_assoc($userMsgQuery)) {
		$userMsgArr[$userMsgRes['message_to']][] = $userMsgRes;
	}
	
	$userFutureArr = array();
	$userFutureMsg = "SELECT 
	user_messages.user_message_id,
	user_messages.message_read_status,
	user_messages.message_subject,
	user_messages.message_text,
	user_messages.message_urgent,
	user_messages.message_sender_id as message_sender_id,
	DATE_FORMAT(user_messages.message_send_date,'%m-%d-%Y %h:%i %p') AS msg_send_date, DATE_FORMAT(user_messages.delivery_date,'%m-%d-%Y %h:%i %p') AS delivery_date, user_messages.flagged,user_messages.message_to
	FROM user_messages
	where user_messages.message_status = 0 and user_messages.del_future_alert=0 and user_messages.del_status ='0' and date_format(user_messages.delivery_date,'%Y-%m-%d') > CURDATE() AND user_messages.delivery_date != '0000-00-00' and user_messages.saved_folder_id=0 and user_messages.Pt_Communication = 0 and user_messages.message_sender_id != 0
	AND DATE_FORMAT(user_messages.delivery_date,'%Y-%m-%d') BETWEEN '$startDate' AND '$endDate' ";
	if($operator_ids){
		$userFutureMsg .=" and user_messages.message_sender_id IN ($operator_ids) ";
	}
	$userFutureMsg .=" order by user_messages.user_message_id desc";
	$userMsgFutQuery = imw_query($userFutureMsg);
	while($userMsgFutRes = imw_fetch_assoc($userMsgFutQuery)) {
		if($userMsgFutRes['message_sender_id'])
		$userFutureArr[$userMsgFutRes['message_sender_id']][] = $userMsgFutRes;
	}
	
	$userOdrArr = array();	
	$userOdr = "select order_set_associate_chart_notes.order_set_associate_id as primary_set_id, order_set_associate_chart_notes.order_set_id, order_set_associate_chart_notes.patient_id , order_set_associate_chart_notes.logged_provider_id , order_set_associate_chart_notes.order_set_options, 
	DATE_FORMAT(order_set_associate_chart_notes.created_date,'%m-%d-%Y %h:%i %p') AS c_date,
	order_set_associate_chart_notes.logged_provider_id , order_set_associate_chart_notes.delete_status as set_delete_status, order_set_associate_chart_notes.order_set_reason_text , order_set_associate_chart_notes_details.*, patient_data.lname,patient_data.fname,patient_data.mname, order_set_associate_chart_notes_details.resp_person from order_set_associate_chart_notes left join order_set_associate_chart_notes_details on order_set_associate_chart_notes.order_set_associate_id = order_set_associate_chart_notes_details.order_set_associate_id join patient_data on patient_data.id = order_set_associate_chart_notes.patient_id where order_set_associate_chart_notes_details.orders_status != '2' and order_set_associate_chart_notes_details.delete_status = '0' 
	AND DATE_FORMAT(order_set_associate_chart_notes.created_date,'%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'";
	if($operator_ids){
		$userOdr .=" and order_set_associate_chart_notes_details.resp_person IN ($operator_ids) ";
	}
	$userOdr .=" order by order_set_associate_chart_notes.created_date desc";
	$userOdrQuery = imw_query($userOdr);
	while($userOdrRes = imw_fetch_assoc($userOdrQuery)) {
		if($userOdrRes['resp_person'])
		$userOdrArr[$userOdrRes['resp_person']][] = $userOdrRes;
	}
	
	$activeTests = array();
	$tests_name = "SELECT * FROM tests_name WHERE del_status=0 ORDER BY temp_name";
	$tests_name_res = imw_query($tests_name);
	if($tests_name_res && imw_num_rows($tests_name_res)>0){
		while($rs_test=imw_fetch_assoc($tests_name_res)){
			if($rs_test['id'])
			$activeTests[$rs_test['id']] = $rs_test;
		}
	}
	
	foreach($activeTests as $thisTest){
		$where_part = "";
		if($thisTest['test_table']=='test_other' || $thisTest['test_table']=='test_custom_patient'){
			$where_part = " AND test_template_id = '0'";
			if($thisTest['test_type']=='1'){$where_part = " AND test_template_id = '".$thisTest['id']."'";}
		}
		$id_colums_Name = $thisTest['test_table_pk_id'];
		$thisTest['show_test_table'] = $thisTest['test_table'];
		$thisTest['comment_col'] = 'comments';
		if($thisTest['test_table']=='ivfa') $thisTest['comment_col'] = 'ivfaComments';
		elseif(in_array($thisTest['test_table'],array('disc','disc_external'))) $thisTest['comment_col'] = 'discComments';
		elseif(in_array($thisTest['test_table'],array('test_bscan','test_labs','test_other','test_cellcnt','test_custom_patient'))) $thisTest['comment_col'] = 'techComments';
		elseif($thisTest['test_table']=='icg') $thisTest['comment_col'] = 'comments_icg';
		elseif(in_array($thisTest['test_table'],array('surgical_tbl','iol_master_tbl'))) $thisTest['comment_col'] = '""';
		$thisTest['orderby_col'] = 'ordrby';
		
		if($operator_ids){
			$where_part .= " AND (".$thisTest['orderby_col']." IN ($operator_ids) )";
		}
		$where_part .= " AND DATE_FORMAT(".$thisTest['exam_date_key'].", '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'";
		
		$yr1backtime = strtotime("-1 year", time());
		$yr1backdate = date("Y-m-d", $yr1backtime);
		
		$activeTest = "SELECT '".$thisTest['test_table']."' AS testName,
		'".$thisTest['temp_name']."' AS testDesc, 
		".$thisTest['test_table_pk_id']." AS main_id, 
		".$thisTest['phy_id_key']." AS phyName, 
		DATE_FORMAT(".$thisTest['exam_date_key'].", '%m-%d-%Y %h:%i %p') AS taskDate, 
		".$thisTest['comment_col']." AS comments,
		".$thisTest['orderby_col']." AS ordrby, 
		".$thisTest['patient_key']." AS patient_id, 
		".$thisTest['test_type']." AS test_type 
		FROM ".$thisTest['test_table']." 
		WHERE del_status='0' AND purged = '0' AND finished = '0' 
		AND (".$thisTest['phy_id_key']."='' || ".$thisTest['phy_id_key']."='0') 
		AND (".$thisTest['exam_date_key'].">'".$yr1backdate."') ".
		$where_part." ORDER BY ".$thisTest['exam_date_key']." DESC";
		$activeTestres = imw_query($activeTest);
			
			if($activeTestres && imw_num_rows($activeTestres)>0){//echo $q.'<br>'.$thisTest['id'].'<hr>';
			if(!$testsArr) $testsArr = array();
			while($rs = imw_fetch_assoc($activeTestres)){
				/*****IF ORDER BY IS BLANK, THEN CHECK IF LOGGED IN USER IS PRIMARY PHY FOR PATIENT, IF NOT SKIP THIS RECORD**/
				$pt_id = $rs['patient_id'];
				$pt_res = imw_query("SELECT id,providerID FROM patient_data WHERE id = '".$pt_id."' LIMIT  0,1");
				if($pt_res && imw_num_rows($pt_res)==1){
					$pt_rs = imw_fetch_assoc($pt_res);
					$pt_pro_id = $pt_rs['providerID'];
					if(($rs['ordrby']=='' || $rs['ordrby']=='0') && (!in_array($pt_pro_id, $_REQUEST['operator_id']))) {
						continue;
					}
					unset($pt_id); imw_free_result($pt_res); unset($pt_rs); unset($pt_pro_id);
				}else{
					continue;
				}
				/*****RECORD SKIP LOGIC END******/
				
				$test_case_key = $thisTest['test_table'];
				if($test_case_key=='test_other') $test_case_key .= $thisTest['test_type'];
				if($thisTest['test_table']=='test_other' && $thisTest['test_type']=='0'){
					$rs['show_name']	= isset($rs['test_other']) ? $rs['test_other'] : '';
				}
				$rs['test_js_key'] = $test_case_key;
				$rs['id_colums_Name'] = $id_colums_Name;
				$testsArr[$rs['ordrby']][] = $rs;
			}
		}
	}
	if($report_view=='summary'){
		include('monitoring_summary_html.php');
	}else{
		include('monitoring_detail_html.php');
	}

	// SAVE Search Criteria
if(!isset($callFrom) || $callFrom != 'scheduled'){
	if((isset($search_name) && $search_name!='' && empty($varCriteria)==false) || ($chkSaveSearch=='1' && empty($varCriteria)==false)){
		$search_name=trim($search_name);
		$qryPart='Insert into';
		$fieldPart=", report_name='".addslashes($search_name)."'";
		$qryWhere='';
		if($savedCriteria!='' && $chkSaveSearch=='1'){
			$qryPart='Update'; 
			$fieldPart='';
			$qryWhere=" WHERE id='".$savedCriteria."'";
		}
		
		$qry="Select id FROM reports_searches WHERE report_name='".$search_name."' AND report='monitoring_report_criteria'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)<=0 || (imw_num_rows($rs)>0 && $qryPart=='Update')){
			$qry="$qryPart reports_searches SET uid='".$_SESSION['authId']."', report='monitoring_report_criteria',
			search_data='".addslashes($varCriteria)."', saved_date='".date('Y-m-d H:i:s')."' ".$fieldPart.$qryWhere;
			imw_query($qry);
		}
	}
}
//---------------------
}
?>
<script type="text/javascript">	
function SaveOprName(){
	var postData = $('#update_opr_frm').serialize();
	$.ajax({
		type: "POST",
		url: "<?php echo $GLOBALS['webroot']; ?>/interface/reports/clinical/monitoring_ajax.php",
		data: postData,
		success: function (callSts) {
			$('#myModal').modal('hide');
			get_report();
		}
	});
}

function updateAssigne(primaryID,providerId,tableName,fieldName,updateColum){
	$('#myModal .modal-header .modal-title').text('Update Operator');
	$('#myModal').on('show.bs.modal', function () {
		$('#operator_name', $(this)).val(providerId);
		$('#primaryID', $(this)).val(primaryID);
		$('#providerId', $(this)).val(providerId);
		$('#tableName', $(this)).val(tableName);
		$('#fieldName', $(this)).val(fieldName);
		$('#updateColum', $(this)).val(updateColum);
	})
	$('#myModal').modal('show');
}
</script>