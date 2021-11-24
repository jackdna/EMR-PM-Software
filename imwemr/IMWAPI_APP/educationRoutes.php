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
 * index.php
 * Access Type: InClude
 * Purpose: Routes for Education , Statement , Patient Message API calls.
*/

$patientId = 0;
	
/*Validate Patient ID*/

$this->respond(array('POST','GET'), '*', function($request, $response, $service, $app) use(&$patientId) {
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
});

/* Return Education List */
$this->respond(array('POST','GET'), '/getInstruction', function($request, $response, $service,$app) use(&$patientId){
	$result = array();
	$fetchInner = array();
	$fetch = array();
		
	$qryDocRel = "SELECT id,name,date_time,form_id,operator_id,scan_id,doc_id from document_patient_rel 
		where p_id ='".((int)$patientId)."' AND status = '0' AND doc_id!='0' GROUP BY DATE_FORMAT(date_time,'%y-%m-%d') ORDER BY date_time desc" ;
	$record = $app->dbh->imw_query($qryDocRel);
	
	$i=0;
	while($fetch = $app->dbh->imw_fetch_assoc($record)){
			
		$dateFirst = date('m-d-Y',strtotime($fetch['date_time']));
		
		$result[$i] = array("level"=>1,"name"=>$dateFirst);
			
		$qryDocInner = "SELECT id,name,date_time from document_patient_rel where p_id ='".((int)$patientId)."' AND status = '0' AND doc_id!='0' ORDER BY date_time desc" ;
		$recordInner = $app->dbh->imw_query($qryDocInner);
		while($fetchInner = $app->dbh->imw_fetch_assoc($recordInner)){
			$date = date('m-d-Y',strtotime($fetchInner['date_time']));
			$time = date('H:i',strtotime($fetchInner['date_time']));
			$fetchInner['name_date'] = $fetchInner['name'].'('.$time.')';
			unset($fetchInner['date_time']);
			unset($fetchInner['name']);
			$fetchInner['id'] = (int)$fetchInner['id'];
			
			if($dateFirst == $date){
				$result[$i]['Objects'][] = array("level"=>2,"name"=>$fetchInner['name_date'],"id"=>$fetchInner['id']);
			}
		}
		$i++;
	}
	unset($fetchInner);unset($fetch);
	$response = array("getInstruction"=>$result);
	return json_encode($response);
	
});

/* Create PDF of Education */
$this->respond(array('POST','GET'), '/createInstruction', function($request, $response, $service,$app) use(&$patientId){
	// To Generate PDF of saved Inst.

	$b64Doc = '';
	if($request->__isset('inst')){
		$service->validateParam('inst', 'Please provide valid Id.')->notNull();
		$inst	= $app->dbh->imw_escape_string( $request->__get('inst') );
	}
	if($inst!=''){
		$qryDocRel = "SELECT description, upload_doc_file_path from document_patient_rel where id='".$inst."'" ;
		$record=$app->dbh->imw_query($qryDocRel);
		$fetchRecords=$app->dbh->imw_fetch_assoc($record);
		if(!empty($fetchRecords['description'])){
		
			$contents = $fetchRecords['description'];
			
			$contents = htmlspecialchars_decode($contents);
			$contents = str_ireplace('</br>','<br />',$contents);
			
			$contents = str_ireplace('src="../../../data','src="../data',$contents);
			$contents = str_ireplace("src='../../../data","src='../data",$contents);
			$contents = stripslashes(html_entity_decode($contents,true));
			$contents = str_ireplace('�','',$contents);
			
			$html2pdf = new HTML2PDF('P','A4','en');
			$html2pdf->setTestTdInOnePage(true);
			$html2pdf->WriteHTML($contents);
			
			$b64Doc = $html2pdf->Output('', 'S');
			$b64Doc = base64_encode($b64Doc);
		}
		else if(!empty($fetchRecords['upload_doc_file_path'])){
			$pdfPath = data_path().$fetchRecords['upload_doc_file_path'];
			$pdf = file_get_contents($pdfPath);
			$b64Doc= base64_encode($pdf);
		}
	}
	$response = array('printInstruction' => $b64Doc);
	return json_encode($response);
	
});


// Get Statement //
$this->respond(array('POST','GET'), '/getStatement', function($request, $response, $service, $app) use(&$patientId){
	
	$qry2="select date_format(created_date,'%m-%d-%Y') as createdDate,patient_id,previous_statement_id,statement_balance 
                             from previous_statement where statement_acc_status=1 and patient_id='$patientId' order by created_date desc";
	$rows = array();
	$statement = array();
	$res = $app->dbh->imw_query($qry2);
	if($res && $app->dbh->imw_num_rows($res)>0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$rows[] = $row;
		}
	}
	$qryPatientAcess="SELECT iportal_billing_statement,iportal_billing_statement_desc,iportal_eve,iportal_prescription_desc,iportal_default_conf_msg from facility where facility_type = 1";
	$result = $app->dbh->imw_query($qryPatientAcess);
	if($result && $app->dbh->imw_num_rows($result)>0){
		$state_rows = $app->dbh->imw_fetch_assoc($result);
	}
	if(!empty($rows) && $state_rows['iportal_billing_statement']!=1){
		$statement['status'] = 1;
		$statement['data']=$rows;
	}
	else{
		$statement['status'] = 0;
		$statement['message'] = $state_rows['iportal_billing_statement_desc'];
	}
	$response = array('statements'=>$statement);
	return json_encode($response);
});

/* Create PDF of Statement */
$this->respond(array('POST','GET'), '/printStatement', function($request, $response, $service,$app) use(&$patientId){
	
	$b64Doc['data'] = '';
	if($request->__isset('id')){
		$service->validateParam('id', 'Please provide valid Id.')->notNull();
		$id	= $app->dbh->imw_escape_string( $request->__get('id') );
	}
	if($id!=''){
		$qry="SELECT statement_data,statement_txt_data FROM previous_statement_detail WHERE previous_statement_id IN ($id)";
		
		$record=$app->dbh->imw_query($qry);
		while($fetchRecords=$app->dbh->imw_fetch_assoc($record)){
			$printDataArr[]	= $fetchRecords['statement_data'];
			$printDataTxtArr[]	= $fetchRecords['statement_txt_data'];
		}
		if(count($printDataArr)>0){
			$printData = implode(' ',$printDataArr);
			$printDataTxt = implode("\r\n",$printDataTxtArr);
			$save_html = $printData;
		}
		
		$contents = htmlspecialchars_decode($save_html);
		$contents = str_ireplace('</br>','<br />',$contents);
		$contents = utf8_encode($contents);
		$contents = str_ireplace('src="','src="'.$GLOBALS['php_server'].'/library/images/',$contents);
		$contents = str_ireplace("src='","src='".$GLOBALS['php_server'].'/library/images/',$contents);
		$contents = stripslashes(html_entity_decode($contents,true));
		$contents = str_ireplace('�','',$contents);
		
		$html2pdf = new HTML2PDF('P','A4','en');
		$html2pdf->setTestTdInOnePage(true);
		$html2pdf->WriteHTML($contents);
		
		$b64Doc = $html2pdf->Output('', 'S');
		$b64['data'] = base64_encode($b64Doc);
		//$data = fopen('test.pdf','w');
		//fwrite($data, base64_decode($b64Doc));
	}
	$response = array('printStatement' => $b64);
	return json_encode($response);
	
});

// Patient Sent Message //
$this->respond(array('POST','GET'), '/patientSentMessage', function($request, $response, $service, $app) use(&$patientId){
	
	$qryStr = "SELECT pt_msg_id,msg_subject , msg_data , 
				DATE_FORMAT(msg_date_time, '%m-%d-%Y') as msg_date ,
				DATE_FORMAT(msg_date_time, '%H:%i:%s') as msg_time FROM patient_messages  WHERE sender_id = '".((int)$patientId)."' AND communication_type = 2 AND del_status_by_pt = 0 ORDER BY pt_msg_id DESC";
	$result = array();
	$res = $app->dbh->imw_query($qryStr);
	if($res && $app->dbh->imw_num_rows($res)>0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$row['msg_data'] = stripslashes(html_entity_decode($row['msg_data']));
			$result[] = $row;
		}
	}
	unset($row);
	$response = array("patientSentMessage"=>$result);
	$response = json_encode($response);
	// Data Formatted According to IOS Developer //
	$response = str_replace('\r','',$response);
	$response = str_replace('\n','<br />',$response);
	$response = preg_replace('/\s+/',' ', $response);
	$response = urldecode($response);
	return $response;
});

// Patient Inbox Message //
$this->respond(array('POST','GET'), '/patientInboxMessage', function($request, $response, $service, $app) use(&$patientId){
	
	$qryStr = "SELECT pt_msg_id,msg_subject , msg_data , DATE_FORMAT(msg_date_time, '%m-%d-%Y') as msg_date,
				DATE_FORMAT(msg_date_time,'%H:%i:%s') as msg_time, is_read ,flagged
				FROM patient_messages 
				WHERE receiver_id = '".((int)$patientId)."' AND communication_type = 1 AND del_status_by_pt = 0 
				ORDER BY pt_msg_id DESC";
	$result = array();
	$res = $app->dbh->imw_query($qryStr);
	if($res && $app->dbh->imw_num_rows($res)>0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$row['msg_data'] = stripslashes(html_entity_decode($row['msg_data']));
			$result[] = $row;
		}
	}
	unset($row);
	$response = array("patientInboxMessage"=>$result);
	$response = json_encode($response);
	$response = str_replace('\r','',$response);
	$response = str_replace('\n','<br />',$response);
	$response = urldecode($response);
	return ($response);
});

// Send/Reply Patient Message //
$this->respond(array('POST','GET'), '/sendMessages', function($request, $response, $service, $app) use(&$patientId){
	
	$result = false;
	$qry_part = "";
	$pt_msg_id = addslashes($request->__get('rly_id'));
	$doctor_name = addslashes($request->__get('doc_name'));
	$message_urgent = ($request->__get('is_urgent')!=NULL  && $request->__get('is_urgent')!='' && $request->__get('is_urgent')!=0 )? 1 : 0;
	$msg_subject = addslashes($request->__get('msg_sub'));
	$msg_data = addslashes($request->__get('msg_data'));
	
	if($pt_msg_id != "")
	{
		$qry_part = ", replied_id = ".$pt_msg_id." ";
		$replied_qry = "SELECT *, DATE_FORMAT(msg_date_time,'%m-%d-%Y %h:%i %p') AS msg_date_time FROM patient_messages WHERE pt_msg_id = '".$pt_msg_id."'";
		$replied_qry_obj = $app->dbh->imw_query($replied_qry);
		$replied_qry_data = $app->dbh->imw_fetch_assoc($replied_qry_obj);
		$getPatientName = "SELECT CONCAT(lname,', ',fname) as name FROM patient_data WHERE id = '".$replied_qry_data["receiver_id"]."'";
		$rsGetPatientName = $app->dbh->imw_query($getPatientName);
		$rowGetPatientName = $app->dbh->imw_fetch_assoc($rsGetPatientName);
		$name_sendTo = $rowGetPatientName['name'];
		
		$ORsenderName = "Patient Co-ordinator";
		$sentDate = $replied_qry_data["msg_date_time"];
		$originalSubject = $replied_qry_data["msg_subject"];
		$originalTextPrefix .= '<br /><br />----ORIGINAL MESSAGE----<br />';
		$originalTextPrefix .= 'From: '.$ORsenderName.'<br />';
		$originalTextPrefix .= 'To: '.$name_sendTo.'<br />';
		$originalTextPrefix .= 'Sent: '.$sentDate.'<br />';
		$originalTextPrefix .= 'Subject: '.$originalSubject.'<br /><br />';
			
		$originalTextPrefix .= $replied_qry_data["msg_data"];
		$msg_data = $msg_data.$originalTextPrefix;
	}
		
	$msg_subject = addslashes($msg_subject);
	$msg_data = addslashes($msg_data);
				
	$req_qry = "INSERT INTO patient_messages SET receiver_id = '".$doctor_name."', sender_id = '".((int)$patientId)."', communication_type = 2, msg_subject = '".$msg_subject."', msg_data = '".$msg_data."', delivery_date = '".date('Y-m-d')."' , message_urgent='".$message_urgent."'".$qry_part;		
	$result = $app->dbh->imw_query($req_qry);	
	if($result){		
		if($pt_msg_id != ""){
			$req_up_qry = "UPDATE patient_messages SET msg_icon = 1 WHERE pt_msg_id = ".$pt_msg_id;	
			$result = $app->dbh->imw_query($req_up_qry);
		}
	}
	if($pt_msg_id!="") $response =  array("replyMessage"=>array("Status"=>$result));
	
	else $response =  array("sendMessage"=>array("Status"=>$result));
	
	return json_encode($response);
});

// Delete Patient Inbox/Sent Messages //
$this->respond(array('POST','GET'), '/deleteMessages', function($request, $response, $service, $app) use(&$patientId){
	
	$result['status'] = false;
	$chs_ids = addslashes($request->__get('chs_ids'));
	if($chs_ids!='' & $chs_ids!=0){
		$req_qry = "UPDATE patient_messages SET del_status_by_pt = 1 WHERE pt_msg_id IN(".$chs_ids.")";
		$result['status'] = $app->dbh->imw_query($req_qry);
	}
	$response = array("deleteMessages"=>$result);
	return json_encode($response);
});

// Delete Patient Inbox/Sent Messages //
$this->respond(array('POST','GET'), '/readMessages', function($request, $response, $service, $app) use(&$patientId){
	
	$result = false;
	$chs_ids = addslashes($request->__get('id'));
	$req_qry = "UPDATE patient_messages SET `is_read` = 1 WHERE pt_msg_id =".$chs_ids;
	$result = $app->dbh->imw_query($req_qry);
	$response = array("readMessages"=>$result);
	return json_encode($response);
});

// Get Patient PGHD //
$this->respond(array('POST','GET'), '/PGHD', function($request, $response, $service, $app) use(&$patientId){
	
	$req_qry_select="SELECT new_val_lbl,date_format(reqDateTime, '%m-%d-%Y %h:%i %p') as  reqDateTime,if(is_approved='1','Approved','Pending') as status FROM iportal_req_changes WHERE tb_name='user_messages' AND col_name='PGHD' AND pt_id='".((int)$patientId)."' AND del_status='0' ORDER BY id DESC ";
	$result = array();
	$res = $app->dbh->imw_query($req_qry_select);
	if($res && $app->dbh->imw_num_rows($res)>0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$result['pghd'][] = $row;
		}
	}
	unset($row);

	$response = json_encode($result);
	$response = str_replace('\r','',$response);
	$response = str_replace('\n','<br>',$response);
	$response = str_replace('<br>',' ',$response);
	$response = (preg_replace('/\s+/',' ', $response));
	$response = urldecode($response);
	return $response;
});

// Patient PGHD //
$this->respond(array('POST','GET'), '/submitPGHD', function($request, $response, $service, $app) use(&$patientId){
	$res['status'] =false;
	$date_time = date("Y-m-d H:i:s");
	$health_information_txt = addslashes(trim($request->__get('txt')));
	if($health_information_txt!=''){
		$req_qry = "INSERT INTO iportal_req_changes set pt_id='".((int)$patientId)."',tb_name='user_messages',title_msg='PGHD- Patient Health Information',new_val_lbl='".$health_information_txt."',action='add',col_name='PGHD',col_lbl='PGHD',reqDateTime='".$date_time."' ";
		$res['status'] = $app->dbh->imw_query($req_qry);
	}
	$response = array("submitPGHD"=>$res);
	$response = json_encode($response);
	return ($response);
});

// Patient Log //
$this->respond(array('POST','GET'), '/patientlog', function($request, $response, $service, $app) use(&$patientId){
	
	$result['Patient_Access_log'] = array();
	$result['Login_History'] = array();
	$result['resp'] = array();
	$result['Resp_Access_Log'] = array();
	$result['Resp_Login_His'] = array();
	
	$qry_part = " and pt_rp_id = 0";	
	$req_qry = "SELECT `u_action`, `desc`, date_format(logtime, '%m-%d-%Y %h:%i %p') as log_time FROM pt_and_rp_logs WHERE patient_id = '".((int)$patientId)."' ".$qry_part." ORDER BY id DESC LIMIT 10";
	$res = $app->dbh->imw_query($req_qry);
	while($row = $app->dbh->imw_fetch_assoc($res)){
		$result['Patient_Access_log'][] = $row;
	}
	
	$qry_part_login = " and pt_rp_id = 0";
	$req_qry_login = "SELECT date_format(logindatetime, '%m-%d-%Y') as login_date, date_format(logindatetime, '%h:%i:%s %p') as login_time FROM patient_loginhistory WHERE patient_id = '".((int)$patientId)."' ".$qry_part_login." ORDER BY id DESC LIMIT 10";
	$res = $app->dbh->imw_query($req_qry_login);
	while($row = $app->dbh->imw_fetch_assoc($res)){
		$result['Login_History'][] = $row;
	}
	
	$pt_auth_rp_qry = "SELECT lname, fname, mname FROM resp_party WHERE patient_id = '".((int)$patientId)."' and resp_username != '' and resp_password != '' ";
	$res = $app->dbh->imw_query($pt_auth_rp_qry);
	$row = $app->dbh->imw_fetch_assoc($res);
	if(!empty($row)){
		$result['resp'] = $row;
	}
	
	$qry_part_resp = " and pt_rp_id != 0";	
	$req_qry_resp = "SELECT `u_action`, `desc`, date_format(logtime, '%m-%d-%Y %h:%i %p') as log_time FROM pt_and_rp_logs WHERE patient_id = '".((int)$patientId)."' ".$qry_part_resp." ORDER BY id DESC LIMIT 10";
	$res = $app->dbh->imw_query($req_qry_resp);
	while($row = $app->dbh->imw_fetch_assoc($res)){
		$result['Resp_Access_Log'][] = $row;
	}
	
	$qry_part_resp_login_hx = " and pt_rp_id != 0";
	$req_qry_resp_login_hx = "SELECT date_format(logindatetime, '%m-%d-%Y') as login_date, date_format(logindatetime, '%h:%i:%s %p') as login_time FROM patient_loginhistory WHERE patient_id = '".((int)$patientId)."' ".$qry_part_resp_login_hx." ORDER BY id DESC LIMIT 10";
	$res = $app->dbh->imw_query($req_qry_resp_login_hx);
	while($row = $app->dbh->imw_fetch_assoc($res)){
		$result['Resp_Login_His'][] = $row;
	}

	$response =  array("patientlog"=>$result);
	return json_encode($response);
});

// Patient Log by Date //
$this->respond(array('POST','GET'), '/patientlogDate', function($request, $response, $service, $app) use(&$patientId){
	
	$result = array();
	$type	= addslashes($request->__get('type'));
	$startDate	= addslashes($request->__get('startDate'));
	$endDate	= $request->__get('endDate') != '' ? addslashes($request->__get('endDate')) : $patAccessLog;
	
	if($type == 1 ){
		$result['Patient_Access_log'] = array();
		$qry_part = " and pt_rp_id = 0 and date_format(logtime, '%Y-%m-%d')  BETWEEN  '".($startDate)."' AND '".($endDate)."'";	
		$req_qry = "SELECT `u_action`, `desc`, date_format(logtime, '%m-%d-%Y %h:%i %p') as log_time FROM pt_and_rp_logs WHERE patient_id = '".((int)$patientId)."' ".$qry_part." ORDER BY id DESC";
		$res = $app->dbh->imw_query($req_qry);
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$result['Patient_Access_log'][] = $row;
		}
	}
	if($type == 2 ){
		$result['Login_History'] = array();
		$qry_part_login = " and pt_rp_id = 0 and date_format(logindatetime, '%Y-%m-%d') BETWEEN '".($startDate)."' AND '".($endDate)."'";
		$req_qry_login = "SELECT date_format(logindatetime, '%m-%d-%Y') as login_date, date_format(logindatetime, '%h:%i:%s %p') as login_time FROM patient_loginhistory WHERE patient_id = '".((int)$patientId)."' ".$qry_part_login." ORDER BY id DESC";
		$res = $app->dbh->imw_query($req_qry_login);
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$result['Login_History'][] = $row;
		}
	}
	
	if($type == 3 ){
		$result['Resp_Access_Log'] = array();
		$qry_part_resp = " and pt_rp_id != 0 and date_format(logtime, '%Y-%m-%d') BETWEEN '".($startDate)."' AND '".($endDate)."'";	
		$req_qry_resp = "SELECT `u_action`, `desc`, date_format(logtime, '%m-%d-%Y %h:%i %p') as log_time FROM pt_and_rp_logs WHERE patient_id = '".((int)$patientId)."' ".$qry_part_resp." ORDER BY id DESC";
		$res = $app->dbh->imw_query($req_qry_resp);
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$result['Resp_Access_Log'][] = $row;
		}
	}
	if($type == 4 ){
		$result['Resp_Login_His'] = array();
		$qry_part_resp_login_hx = " and pt_rp_id != 0 and date_format(logindatetime, '%Y-%m-%d') BETWEEN '".($startDate)."' AND '".($endDate)."'";
		$req_qry_resp_login_hx = "SELECT date_format(logindatetime, '%m-%d-%Y') as login_date, date_format(logindatetime, '%h:%i:%s %p') as login_time FROM patient_loginhistory WHERE patient_id = '".((int)$patientId)."' ".$qry_part_resp_login_hx." ORDER BY id DESC";
		$res = $app->dbh->imw_query($req_qry_resp_login_hx);
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$result['Resp_Login_His'][] = $row;
		}
	}
	$response =  array("patientlogDate"=>$result);
	$response = json_encode($result);
	return ($response);
});

// Get Signed Consent //
$this->respond(array('POST','GET'), '/getConsent', function($request, $response, $service, $app) use(&$patientId){
	
	$arrReturn = array();
	$consent_obj = new IMW\CL($app->dbh,1);
	$packageData = $consent_obj->consentPackage();
	
	$arrReturn[0] = array("level"=>0,"name"=>"Signed Forms");
	$arrReturn[1] = array("level"=>0,"name"=>"Signed Packages"); 
	//------------GET SIGNED FORMS DATA------------------------------------------
	$qry = "SELECT DATE_FORMAT(pcf.form_created_date,'%m/%d/%y') AS date, chart_procedure_id, GROUP_CONCAT(chart_procedure_id)as c_proc,
									GROUP_CONCAT(pcf.form_information_id) as form_information_id,pcf.package_category_id
									FROM patient_consent_form_information pcf
									WHERE pcf.patient_id='".((int)$patientId)."'
									AND  movedToTrash=0
									GROUP BY DATE_FORMAT(pcf.form_created_date,'%y-%m-%d')
									ORDER BY pcf.form_created_date DESC
									";
		
	$arrImage = $app->dbh->imw_query($qry);
	$i=0;
	while($arr = $app->dbh->imw_fetch_assoc($arrImage)){
		//Check procedure notes related consent form
				
		$arrReturn[0]['Objects'][$i] = array("level"=>1,"name"=>$arr['date']);
		$arrReturn[1]['Objects'][$i] = array("level"=>1,"name"=>$arr['date']);
		
		{
			$qryInner = "SELECT pcf.consent_form_name, pcf.form_created_date,pcf.form_information_id AS id,pcf.chart_procedure_id,
								pcf.package_category_id,pcf.package_category_id
								FROM patient_consent_form_information pcf
								WHERE pcf.form_information_id  IN (".$arr['form_information_id'].")
								AND  movedToTrash=0
								ORDER BY pcf.form_created_date DESC
								";
			
			$arrTemp = $app->dbh->imw_query($qryInner);
			
			while($tempArr = $app->dbh->imw_fetch_assoc($arrTemp)){
				$package = $packageData[$tempArr['package_category_id']];
				if($tempArr['package_category_id']==0){
					$mod_date 	= date("g:i A",strtotime($tempArr['form_created_date']));
					$str 		= '('.$mod_date.')';
					$arrReturn[0]['Objects'][$i]['Objects'][] = array("level"=>2,"name"=>$tempArr['consent_form_name'].$str,"id"=>(int)$tempArr['id']);
				}
				else{
					$mod_date 	= date("g:i A",strtotime($tempArr['form_created_date']));
					$str 		= '('.$mod_date.')';
					$arrReturn[1]['Objects'][$i]['Objects'][$package][] = array("level"=>3,"package"=>$package,"name"=>$tempArr['consent_form_name'].$str,"id"=>(int)$tempArr['id']);
				}
			}
		}
		$i++;
	}unset($tempArr);
	$t=0;
	foreach($arrReturn[1]['Objects'] as $arr){
		if(!array_key_exists('Objects',$arr)){
			unset($arrReturn[1]['Objects'][$t]);
		}
		$t++;
	}
	$t=0;
	foreach($arrReturn[0]['Objects'] as $arr){
		if(!array_key_exists('Objects',$arr)){
			unset($arrReturn[0]['Objects'][$t]);
		}
		$t++;
	}
	
	$arrReturn[0]['Objects'] = array_values($arrReturn[0]['Objects']);
	$arrReturn[1]['Objects'] = array_values($arrReturn[1]['Objects']);
	
	$t=0;
	foreach($arrReturn[1]['Objects'] as $arr){
		$k=0;
		foreach($arr['Objects'] as $key=>$sub_arr){
			$data = array("level"=>2,"name"=>$key,"Objects"=>$sub_arr);			
			$arrReturn[1]['Objects'][$t]['Objects'][$k] = $data;
			unset($arrReturn[1]['Objects'][$t]['Objects'][$sub_arr[0]['package']]);
			$k++;
		}
		$t++;
	}unset($arr);

	$response =  array("GetConsent"=>$arrReturn);
	return json_encode($response);
});

// Get Consent Packages //
$this->respond(array('POST','GET'), '/package', function($request, $response, $service, $app) use(&$patientId){
	
	$arrReturn = array();
	$iportalPack = "SELECT iportal_package_consent,iportal_consent FROM  facility where facility_type=1";
	$resFac = $app->dbh->imw_query($iportalPack);
	$rowFac = $app->dbh->imw_fetch_assoc($resFac);
	$iportal_ids = $rowFac['iportal_package_consent'];
	if($iportal_ids != '' && !empty($iportal_ids)){
		$iportalQry = "SELECT package_category_id, package_category_name, package_consent_form 
						FROM consent_package WHERE delete_status!='yes' AND 
						package_category_id IN (".$iportal_ids.") ORDER BY package_category_name";
		$resConsent = $app->dbh->imw_query($iportalQry);
		$i = 0;
		while($rowConsent = $app->dbh->imw_fetch_assoc($resConsent)){
			$arrReturn[$i] = array("level"=>1,"id"=>(int)$rowConsent['package_category_id'],"name"=>$rowConsent['package_category_name']);
			$q_consent="Select consent_form_id,consent_form_name from consent_form where consent_form_id IN ( ".$rowConsent['package_consent_form'].")";
			$resConsentForm = $app->dbh->imw_query($q_consent);
			while($rowConsentForm = $app->dbh->imw_fetch_assoc($resConsentForm)){
				$arrReturn[$i]['Objects'][] = array("level"=>2,"id"=>(int)$rowConsentForm['consent_form_id'],"name"=>$rowConsentForm['consent_form_name'] );
			}
			$i++;
		}
	}
	unset($rowConsent);
	unset($rowConsentForm);

	$response =  array("package"=>$arrReturn);
	return json_encode($response);
	
});

/* Create PDF of Consent */
$this->respond(array('POST','GET'), '/createConsent', function($request, $response, $service,$app) use(&$patientId){
	// To Generate PDF of saved Inst.
	
	$b64Doc = '';
	if($request->__isset('id')){
		$service->validateParam('id', 'Please provide valid Id.')->notNull();
		$id	= $app->dbh->imw_escape_string( $request->__get('id') );
	}
	if($id!=''){
		$qryDocRel = "SELECT consent_form_content_data FROM patient_consent_form_information WHERE 	form_information_id = '".$id."'"; 
		$record = $app->dbh->imw_query($qryDocRel);
		$fetchRecords = $app->dbh->imw_fetch_assoc($record);
		if(!empty($fetchRecords)){
		
			$contents = stripslashes(html_entity_decode($fetchRecords['consent_form_content_data']));
			// Code To Replace TextBox //
			$arrTextBox = array(
								array('name'=>'name="xsmall','size'=>'1','Oname'=>'xsmall'),
								array('name'=>'name="small','size'=>'30','Oname'=>'small'),
								array('name'=>'name="medium','size'=>'60','Oname'=>'medium'),
								array('name'=>'name="large','size'=>'','Oname'=>'large')
								);
			foreach($arrTextBox as $individualBox){	
				if(substr_count($contents,$individualBox['name']) >= 1){
					$countBox = substr_count($contents,$individualBox['name']);
					if($individualBox['Oname'] != 'large'){
						if($countBox == 1){
							if($individualBox['Oname'] === 'xsmall'){
								$contents = str_replace('<input type="text" class="form-control " name="'.$individualBox['Oname'].'" value="',' ',$contents);
								$contents = str_replace('" size="'.$individualBox['size'].'" maxlength="'.$individualBox['size'].'">',' ',$contents);
							}
							else if($individualBox['Oname'] === 'small' || $individualBox['Oname'] === 'medium'){
								$contents = str_replace('<input class="form-control " type="text" name="'.$individualBox['Oname'].'" value="',' ',$contents);
								$contents = str_replace('" size="'.$individualBox['size'].'" >',' ',$contents);
							}
						}
						else if($countBox >1){
							for($i=1; $i<=$countBox; $i++){
								$contents = str_replace('<input type="text" class="form-control " name="'.$individualBox['Oname'].$i.'" value="',' ',$contents);
								$contents = str_replace('" size="'.$individualBox['size'].'"  maxlength="'.$individualBox['size'].'">',' ',$contents);
							}
						}
					}
					else{
						if($countBox == 1){
							$contents = str_replace('<textarea rows="2" cols="100" name="'.$individualBox['Oname'].'">',' ',$contents);
							$contents = str_replace('</textarea>',' ',$contents);
						}
						else if($countBox >1){
							for($i=1; $i<=$countBox; $i++){
								$contents = str_replace('<textarea rows="2" cols="100" name="'.$individualBox['Oname'].$i.'">',' ',$contents);
								$contents = str_replace('</textarea>',' ',$contents);
							}
						}
					}
				}
			}
			// End Of Code To Replace TextBox //
			$contents = htmlspecialchars_decode($contents);
			$contents = str_ireplace('</br>','<br />',$contents);
			
			$protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
			$contents = str_ireplace('src="','src="'.$protocol.$_SERVER['SERVER_NAME'],$contents);
			$contents = str_ireplace("src='","src='".$protocol.$_SERVER['SERVER_NAME'],$contents);
			$contents = stripslashes(html_entity_decode($contents,true));
			$contents = str_ireplace('�','',$contents);
			
			$html2pdf = new HTML2PDF('P','A4','en');
			$html2pdf->setTestTdInOnePage(true);
			$data = $html2pdf->WriteHTML($contents);
			
			$b64Doc = $html2pdf->Output('', 'S');
			$b64Doc = base64_encode($b64Doc);
			//$data = fopen('test.pdf','w');
			//fwrite($data, base64_decode($b64Doc));
		}
	}
	$response = array('printConsent' => $b64Doc);
	return json_encode($response);

});

// Get Pending Approval //
$this->respond(array('POST','GET'), '/pendingApproval', function($request, $response, $service, $app) use(&$patientId){
	
	$req_qry = "SELECT *,DATE_FORMAT(reqDateTime,'%m-%d-%Y') as reqDate , DATE_FORMAT(reqDateTime,'%h:%i %p') as reqTime FROM iportal_req_changes WHERE pt_id = '".((int)$patientId)."' and del_status=0 order by id DESC;";
	$result = array();
	$res = $app->dbh->imw_query($req_qry);
	if($res && $app->dbh->imw_num_rows($res)>0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$row['old_val_lbl'] = stripslashes(html_entity_decode($row['old_val_lbl']));
			$row['new_val_lbl'] = stripslashes(html_entity_decode($row['new_val_lbl']));
			$result[] = $row;
		}
	}unset($row);
	$response =  array("PendingApproval"=>$result);
	$response = json_encode($response);
	$response = urldecode($response);
	return $response;
});

// Delete Pending Approval //
$this->respond(array('POST','GET'), '/deleteApproval', function($request, $response, $service, $app) use(&$patientId){
	
	$result = false;
	$chs_ids	= $request->__get('id');
	if($chs_ids!=0 && $chs_ids!=''){
		$req_qry = "UPDATE iportal_req_changes SET del_status = 1 WHERE id  IN(".$chs_ids.")";
		$result = $app->dbh->imw_query($req_qry);
	}
	$response =  array("DeleteApproval"=>array("Status"=>$result));
	return json_encode($response);
});

// Status Of Request Appointment  //
$this->respond(array('POST','GET'), '/isAppointment', function($request, $response, $service, $app) use(&$patientId){
	
	$result = array();
	$result['Request'] = array();
	$result['Make'] = array();
	$load_inbox_qry = "SELECT * FROM patient_messages WHERE  communication_type = 2 and del_status_by_pt = 0 and is_done = 0 and sender_id = '".((int)$patientId)."' and is_appt = 1 ORDER BY pt_msg_id DESC LIMIT 0,1";
	$res = $app->dbh->imw_query($load_inbox_qry);

	if($app->dbh->imw_num_rows($res)>0){
		$row = $app->dbh->imw_fetch_assoc($res);
		$upcoming_appt_arr=explode("<b>",$row['msg_data']);
		
		list($lblphy,$upcoming_provider)=explode("-",$upcoming_appt_arr[5]);
		list($lblreason,$upcoming_proc)=explode("-",$upcoming_appt_arr[7]);
		$arr_update_appt_date=explode("-",$upcoming_appt_arr[8]);
		array_shift($arr_update_appt_date);
		$upcoming_appt_date=implode("-",$arr_update_appt_date);
		$result['Request']['Phy_Name'] = str_replace('<br />','',$upcoming_provider);
		$result['Request']['Reason'] = str_replace('<br />','',$upcoming_proc);
		$upcoming_appt_date = str_replace('<br />','',$upcoming_appt_date);
		$result['Request']['app_date'] = $upcoming_appt_date;
	}
	$chkAppt = ("select schedule_appointments.id, schedule_appointments.sa_facility_id, schedule_appointments.procedureid,TIME_FORMAT(schedule_appointments.sa_app_starttime, '%h:%i %p') as sa_app_starttime, DATE_FORMAT(schedule_appointments.sa_app_start_date, '%m-%d-%y') as sa_app_start_date, schedule_appointments.sa_doctor_id, schedule_appointments.sa_patient_app_status_id, slot_procedures.acronym, slot_procedures.proc, slot_procedures.id from schedule_appointments schedule_appointments LEFT JOIN slot_procedures ON slot_procedures.id=schedule_appointments.procedureid where (sa_patient_id='".$patientId."'  and sa_patient_app_status_id NOT IN('18', '203')) AND (sa_app_start_date >='".date('Y-m-d')."' and sa_patient_id='".$patientId."' and sa_patient_app_status_id NOT IN('18', '203')) order by sa_app_start_date,sa_app_starttime limit 0,1");
	$resChkAppt = $app->dbh->imw_query($chkAppt);
	if($app->dbh->imw_num_rows($resChkAppt)>0){
		$rowChkAppt = $app->dbh->imw_fetch_assoc($resChkAppt);
		$qry_users="SELECT if(mname!='',concat(lname,', ',fname,' ',mname),concat(lname,', ',fname)) as user_name FROM users where id =".$rowChkAppt['sa_doctor_id'];
		$resUser = $app->dbh->imw_query($qry_users);
		$rowUser = $app->dbh->imw_fetch_assoc($resUser);
		$result['Make']['Phy_Name'] = $rowUser['user_name'];
		$result['Make']['Reason'] = $rowChkAppt['proc'];
		$result['Make']['app_date'] = $rowChkAppt['sa_app_start_date'].' '.$rowChkAppt['sa_app_starttime'];
	}
	$response = array("isAppointment"=>$result);
	return json_encode($response);
});

// Request Appointment //
$this->respond(array('POST','GET'), '/requestAppointment', function($request, $response, $service, $app) use(&$patientId){
	$result = false;
	$load_inbox_qry = "SELECT * FROM patient_messages WHERE  communication_type = 2 and del_status_by_pt = 0 and is_done = 0 and sender_id = '".((int)$patientId)."' and is_appt = 1 ORDER BY pt_msg_id DESC LIMIT 0,1";
	$resInbox = $app->dbh->imw_query($load_inbox_qry);

	if($app->dbh->imw_num_rows($resInbox)==0){
	
		$msg_subject = "Patient - Appointment Request ";
		$textName = $request->__get("textName");
		//$fac_id = $request->__get('fac_id');
		$fac_name = $request->__get('fac_name');
		$other = $request->__get('other_reason');
		$doc_name = $request->__get("doc_name");
		$address = $request->__get("Address");
		$phone = $request->__get("Phone");
		$eMail = $request->__get("eMail");
		$appt = $request->__get('appt');
		$add_info = $request->__get("add_info");
		$proc_name = $request->__get("proc_name");
		if($proc_name == 'Other'){
			if($other != ''){
				$proc_name = $other;
			}
			else{
				$proc_name = 'Other';
			}
		}
		/*if(is_numeric($doc_id)){
			$doc_qry = "select fname,lname,mname,id from users WHERE id = '".$doc_id."'";
			$doc_qry_obj = $app->dbh->imw_query($doc_qry);
			$doc_data = $app->dbh->imw_fetch_assoc($doc_qry_obj);
			if($doc_data["mname"] != "") { $mname = " ".$doc_data["mname"].". "; }
			$doc_name = $doc_data["lname"].", ".$doc_data["fname"].$mname;
		}*/
		$msg_data = "<b> Patient Name </b> - ".addslashes($textName)."<br />";
		$msg_data .= "<b> Email </b> - ".addslashes($eMail)."<br />";
		$msg_data .= "<b> Phone </b> - ".addslashes($phone)."<br />";
		$msg_data .= "<b> Address </b> - ".addslashes($address)."<br />";
		$msg_data .= "<b> Physician Name </b> - ".addslashes($doc_name)."<br />";
		$msg_data .= "<b> Selected Facility </b> - ".addslashes($fac_name)."<br />";
		$msg_data .= "<b> Appointment Reason </b> - ".addslashes($proc_name)."<br />";
			
		$msg_data .= "<b> Appointment Date </b> - ".addslashes($appt)."<br />";
			
		$msg_data .= "<b> Appointment Time </b> - ".addslashes($HH.":".$II." ".$AA)."<br />";
			//$msg_data .= "<b> Appointment Reason </b> - ".addslashes($_POST["appt_reason"])."<br />";
		$msg_data .= "<b> Additional Information </b> - ".addslashes($add_info)."<br />";

		$req_qry = "INSERT INTO patient_messages SET is_appt = 1, receiver_id = '".addslashes($doc_id)."', sender_id = '".((int)$patientId)."', communication_type = 2, msg_subject = '".addslashes($msg_subject)."', msg_data = '".$msg_data."'";		
		$result = $app->dbh->imw_query($req_qry);
	}
	$response = array("requestAppointment"=>array("Status"=>$result));
	return json_encode($response);
});

/*Providers List*/
$this->respond(array('POST','GET'), '/getProPhy', function($request, $response, $service, $app) use(&$patientId){
	$returnData = array();
	$returnDataPro = array();
	$response = array();
	$returnDataFac =array();
	$qry = $app->dbh->imw_query("SELECT 
									id as ProviderId,
									fname as FirstName,
									lname as LastName,
									user_npi as NPI				
								FROM users
								WHERE 
									user_type = 1 
								AND delete_status = 0
								AND superuser = 'no'
								ORDER BY lname ASC");
	if($qry && $app->dbh->imw_num_rows($qry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($qry)){
			$row['name'] = $row['LastName'].' '.$row['FirstName'];
			unset($row['FirstName']);
			unset($row['LastName']);
			array_push($returnData, $row);
		}
	}unset($row);
	$response['physician'] = $returnData;
	
	$qryPro = $app->dbh->imw_query("SELECT 
										proc as ProdecureName,
										acronym as ProdecureAlias			
									FROM slot_procedures
									WHERE 
										active_status = 'yes'
									ORDER BY proc ASC");
	
	if($qryPro && $app->dbh->imw_num_rows($qryPro) > 0){
		while($rowPro = $app->dbh->imw_fetch_assoc($qryPro)){
			$rowPro['ProdecureName'] = trim($rowPro['ProdecureName']);
			if( empty($rowPro['ProdecureName']) == false ){
				array_push($returnDataPro, $rowPro);
			}
		}
		$app->dbh->imw_free_result($qryPro);
	}unset($rowPro);
	
	$response['procedure'] = $returnDataPro;
	
	$query_fac = "select id, name from facility order by name ASC";
	$resFac = $app->dbh->imw_query($query_fac);
	if($resFac && $app->dbh->imw_num_rows($resFac) > 0){
		while($rowFac = $app->dbh->imw_fetch_assoc($resFac)){
			$rowFac['name'] = trim($rowFac['name']);
			if(!empty($rowFac['name'])){
				array_push($returnDataFac, $rowFac);
			}
		}
	}unset($rowFac);

	$response['facility'] = $returnDataFac;
	$returnFinalArray = array("getProPhy"=>$response);
	return json_encode($returnFinalArray);
});

// Get Contact Lens //
$this->respond(array('POST','GET'), '/getContactLens', function($request, $response, $service, $app) use(&$patientId){
	$arrCL['clws_id'] = '';
	$arrCL['OD'] = array();
	$arrCL['OS'] = array();
	$contactLens_obj = new IMW\CL($app->dbh,1);
	$makeData = $contactLens_obj->contactLensMakeData();
	
	$qry="Select clws_id FROM contactlensmaster WHERE patient_id='".((int)$patientId)."' ORDER BY clws_id DESC LIMIT 0,1";
	$result = $app->dbh->imw_query($qry);
	$rowContact = $app->dbh->imw_fetch_assoc($result);
	
	$qryDet="Select cldet.* 
			FROM contactlensmaster clm 
			JOIN contactlensworksheet_det cldet ON cldet.clws_id=clm.clws_id 
			WHERE clm.clws_id='".$rowContact['clws_id']."' AND clm.del_status='0' ORDER BY cldet.clEye, cldet.id DESC";
	$resultDet = $app->dbh->imw_query($qryDet);
	
	if($resultDet && $app->dbh->imw_num_rows($resultDet) > 0){
		$i=$j=0;
		while($res = $app->dbh->imw_fetch_assoc($resultDet)){
			$arrCL['clws_id']=$res['clws_id'];
			if($res['clEye']=='OD'){
				if($res['clType']=='scl'){
					$od_type_id=$res['SclTypeOD_ID'];
					$arrCL['OD'][$i]['eye']=$res['clEye'];
					$arrCL['OD'][$i]['sphere']=$res['SclsphereOD'];
					$arrCL['OD'][$i]['cylinder']=$res['SclCylinderOD'];
					$arrCL['OD'][$i]['bc']=$res['SclBcurveOD'];
					$arrCL['OD'][$i]['diameter']=$res['SclDiameterOD'];
					$arrCL['OD'][$i]['axis']=$res['SclaxisOD'];
					$arrCL['OD'][$i]['brand']=$makeData[$od_type_id]['style'];
					$arrCL['OD'][$i]['manufacturer']=$makeData[$od_type_id]['manufacturer'];
					$arrCL['OD'][$i]['type']=$makeData[$od_type_id]['type'];
	
				}else if($res['clType']=='rgp'){
					$od_type_id=$res['RgpTypeOD_ID'];
					$arrCL['OD'][$i]['eye']=$res['clEye'];
					$arrCL['OD'][$i]['sphere']=$res['RgpPowerOD'];
					$arrCL['OD'][$i]['cylinder']=$res['RgpCylinderOD'];
					$arrCL['OD'][$i]['bc']=$res['RgpBCOD'];
					$arrCL['OD'][$i]['diameter']=$res['RgpDiameterOD'];
					$arrCL['OD'][$i]['axis']=$res['RgpAxisOD'];
					$arrCL['OD'][$i]['brand']=$makeData[$od_type_id]['style'];
					$arrCL['OD'][$i]['manufacturer']=$makeData[$od_type_id]['manufacturer'];
					$arrCL['OD'][$i]['type']=$makeData[$od_type_id]['type'];
					
				}else if($res['clType']=='cust_rgp'){
					$od_type_id=$res['RgpCustomTypeOD_ID'];
					$arrCL['OD'][$i]['eye']=$res['clEye'];
					$arrCL['OD'][$i]['sphere']=$res['RgpCustomPowerOD'];
					$arrCL['OD'][$i]['cylinder']=$res['RgpCustomCylinderOD'];
					$arrCL['OD'][$i]['bc']=$res['RgpCustomBCOD'];
					$arrCL['OD'][$i]['diameter']=$res['RgpCustomDiameterOD'];
					$arrCL['OD'][$i]['axis']=$res['RgpCustomAxisOD'];
					$arrCL['OD'][$i]['brand']=$makeData[$od_type_id]['style'];
					$arrCL['OD'][$i]['manufacturer']=$makeData[$od_type_id]['manufacturer'];
					$arrCL['OD'][$i]['type']=$makeData[$od_type_id]['type'];
				}
				$i++;
			}
			if($res['clEye']=='OS'){		
				if($res['clType']=='scl'){
					$os_type_id=$res['SclTypeOS_ID'];
					$arrCL['OS'][$j]['eye']=$res['clEye'];
					$arrCL['OS'][$j]['sphere']=$res['SclsphereOS'];
					$arrCL['OS'][$j]['cylinder']=$res['SclCylinderOS'];
					$arrCL['OS'][$j]['bc']=$res['SclBcurveOS'];
					$arrCL['OS'][$j]['diameter']=$res['SclDiameterOS'];
					$arrCL['OS'][$j]['axis']=$res['SclaxisOS'];
					$arrCL['OS'][$j]['brand']=$makeData[$os_type_id]['style'];
					$arrCL['OS'][$j]['manufacturer']=$makeData[$os_type_id]['manufacturer'];
					$arrCL['OS'][$j]['type']=$makeData[$os_type_id]['type'];
	
				}else if($res['clType']=='rgp'){
					$os_type_id=$res['RgpTypeOS_ID'];
					$arrCL['OS'][$j]['eye']=$res['clEye'];
					$arrCL['OS'][$j]['sphere']=$res['RgpPowerOS'];
					$arrCL['OS'][$j]['cylinder']=$res['RgpCylinderOS'];
					$arrCL['OS'][$j]['bc']=$res['RgpBCOS'];
					$arrCL['OS'][$j]['diameter']=$res['RgpDiameterOS'];
					$arrCL['OS'][$j]['axis']=$res['RgpAxisOS'];
					$arrCL['OS'][$j]['brand']=$makeData[$os_type_id]['style'];
					$arrCL['OS'][$j]['manufacturer']=$makeData[$os_type_id]['manufacturer'];
					$arrCL['OS'][$j]['type']=$makeData[$os_type_id]['type'];
					
				}else if($res['clType']=='cust_rgp'){
					$os_type_id=$res['RgpCustomTypeOS_ID'];
					$arrCL['OS'][$j]['eye']=$res['clEye'];
					$arrCL['OS'][$j]['sphere']=$res['RgpCustomPowerOS'];
					$arrCL['OS'][$j]['cylinder']=$res['RgpCustomCylinderOS'];
					$arrCL['OS'][$j]['bc']=$res['RgpCustomBCOS'];
					$arrCL['OS'][$j]['diameter']=$res['RgpCustomDiameterOS'];
					$arrCL['OS'][$j]['axis']=$res['RgpCustomAxisOS'];
					$arrCL['OS'][$j]['brand']=$makeData[$os_type_id]['style'];
					$arrCL['OS'][$j]['manufacturer']=$makeData[$os_type_id]['manufacturer'];
					$arrCL['OS'][$j]['type']=$makeData[$os_type_id]['type'];
				}
				$j++;
			}
		}
	}unset($res);
	$in_contact_arr = array();
	$in_contact="Select id, cat_name FROM in_contact_cat WHERE del_status='0' order by cat_name asc";
	$result_in_contact = $app->dbh->imw_query($in_contact);
	if($result_in_contact && $app->dbh->imw_num_rows($result_in_contact) > 0){
		while($res_in_contact = $app->dbh->imw_fetch_assoc($result_in_contact)){
			$in_contact_arr[] = $res_in_contact;
		}
	}unset($res_in_contact);
	$in_supply_arr = array();
	$in_supply="Select id, supply_name FROM in_supply WHERE del_status='0' order by supply_name asc";
	$result_in_supply = $app->dbh->imw_query($in_supply);
	if($result_in_supply && $app->dbh->imw_num_rows($result_in_supply) > 0){
		while($res_in_supply = $app->dbh->imw_fetch_assoc($result_in_supply)){
			$in_supply_arr[] = $res_in_supply;
		}
	}unset($res_in_supply);
	$in_options_arr = array();
	$in_options="Select in_options.id, in_options.opt_val, in_options.opt_sub_type, in_contact_cat.cat_name FROM in_options 
	JOIN in_contact_cat ON in_contact_cat.id= in_options.opt_sub_type 
	WHERE in_options.opt_type='5' and in_options.module_id='3' and in_options.del_status='0' 
	order by CAST(in_options.opt_val AS UNSIGNED) asc";
	$result_in_options = $app->dbh->imw_query($in_options);
	if($result_in_options && $app->dbh->imw_num_rows($result_in_options) > 0){
		while($res_in_options = $app->dbh->imw_fetch_assoc($result_in_options)){
			$in_options_arr[] = $res_in_options;
		}
	}unset($res_in_options);
	$address['facility_address'] = '';
	$address['patient_address'] = '';
	$qry = "SELECT pd.id,pd.fname,pd.mname,pd.lname,pd.street,pd.city,pd.state,pd.postal_code,pd.zip_ext,
	pos_fac.pos_facility_address, pos_fac.pos_facility_city, pos_fac.pos_facility_state, pos_fac.pos_facility_zip,
	pos_fac.zip_ext as 'pos_zip_ext'  
	FROM patient_data pd 
	LEFT JOIN pos_facilityies_tbl pos_fac ON pos_fac.pos_facility_id= pd.default_facility 
	WHERE pd.id = '".((int)$patientId)."'";
	$rs = $app->dbh->imw_query($qry);
	$res = $app->dbh->imw_fetch_assoc($rs);
	$patient_address = $res['fname'].' '.$res['lname'].' - '.$res['id'].',\n';
	$patient_address.=trim(stripslashes($res['street'])).'\n';
	$patient_address.=trim(stripslashes($res['city'].", ".$res['state']." ".$res['postal_code']));
	$patient_address.=(empty($res['zip_ext'])==false) ? '-'.$res['zip_ext'] : '';	
	$address['patient_address']=$patient_address;
	
	if($res['default_facility']>0){
		$facility_address.=trim(stripslashes($res['pos_facility_address'])).'\n';
		$facility_address.=trim(stripslashes($res['pos_facility_city'].", ".$res['pos_facility_state']." ".$res['pos_facility_zip']));
		$facility_address.=(empty($res['pos_zip_ext'])==false) ? '-'.$res['pos_zip_ext'] : '';	
		$address['facility_address']=$facility_address;
	}else{
		//GETTING LAST APPOINTMENT FACILITY ADDRESS
		$qry="Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".((int)$patientId)."' ORDER BY id DESC limit 0,1";
		$rs = $app->dbh->imw_query($qry);
		$res = $app->dbh->imw_fetch_assoc($rs);
		$facility_id=$res['sa_facility_id'];
		unset($rs);
		
		if($facility_id>0){
			$qry="Select pos_fac.pos_facility_address, pos_fac.pos_facility_city, pos_fac.pos_facility_state, pos_fac.pos_facility_zip,
			pos_fac.zip_ext as 'pos_zip_ext' FROM facility 
			JOIN pos_facilityies_tbl pos_fac ON pos_fac.pos_facility_id = facility.fac_prac_code 
			WHERE facility.id='".$facility_id."'";
			$rs = $app->dbh->imw_query($qry);
			$res = $app->dbh->imw_fetch_assoc($rs);
			$facility_address.=trim(stripslashes($res['pos_facility_address'])).'\n';
			$facility_address.=trim(stripslashes($res['pos_facility_city'].", ".$res['pos_facility_state']." ".$res['pos_facility_zip']));
			$facility_address.=(empty($res['pos_zip_ext'])==false) ? '-'.$res['pos_zip_ext'] : '';
			$address['facility_address']=$facility_address;
		}
	}unset($rs);
	$finalArray['lens_hx'] =  $arrCL;
	$finalArray['lens_hx']['patient_address'] = $address['patient_address'];
	$finalArray['lens_hx']['facility_address'] = $address['facility_address'];
	$finalArray['disposal'] =  $in_contact_arr;
	$finalArray['supply'] =  $in_supply_arr;
	$finalArray['weight'] =  $in_options_arr;
	$response = array('getContactLens'=>$finalArray);
	return json_encode($response);
});

/* Get Contact Lens Order */
$this->respond(array('POST','GET'), '/getContactLensOrder', function($request, $response, $service, $app) use(&$patientId){
	$returnData = array();
	$contactOrder="Select `temp_order_num`,`clws_id`,`eye`,`brand`,`manufacturer`,`type`,`disposable`,`package`,`supplies`,`boxes`,`is_approved`, DATE_FORMAT(ordered_date, '%m-%d-%Y') as 'orderedDate' 
					FROM iportal_req_orders WHERE patient_id='".((int)$patientId)."' AND order_for='cl' ORDER BY id DESC";
	$resultSet = $app->dbh->imw_query($contactOrder);
	if($resultSet && $app->dbh->imw_num_rows($resultSet) > 0){
		while($row = $app->dbh->imw_fetch_assoc($resultSet)){
			if($row['supplies']==1) $row['supplies'] = $row['supplies'].' Month';
			else if($row['supplies']>1) $row['supplies'] = $row['supplies'].' Months';
			if($row['supplies']==12) $row['supplies'] = $row['supplies'].' Year';
			array_push($returnData, $row);
		}
	}
	$response = array("getContactLensOrder"=>$returnData);
	return json_encode($response);
});

/* Submit Contact Lens Order */
$this->respond(array('POST','GET'), '/submitContactLensOrder', function($request, $response, $service, $app) use(&$patientId){
	$resultSet['Order_Data_Status'] = false;
	//$handle = file_get_contents('php://input');
	//$handle = stripslashes($handle);
	//$orderData = json_decode($handle,true);
	$axis = stripcslashes(urldecode($request->__get('axis')));
	$boxes = stripcslashes(urldecode($request->__get('boxes')));
	$brand = urldecode(rawurldecode($request->__get('brand')));
	$cylinder = stripcslashes(urldecode($request->__get('cylinder')));
	$diameter = stripcslashes(urldecode($request->__get('diameter')));
	$disposable	= stripcslashes(urldecode($request->__get('disposable')));
	$disposable_id = stripcslashes(urldecode($request->__get('disposable_id')));
	$eye = stripcslashes(urldecode($request->__get('eye')));
	$manufacturer = stripcslashes(urldecode($request->__get('manufacturer')));
	$package = stripcslashes(urldecode($request->__get('package')));
	$package_id	= stripcslashes(urldecode($request->__get('package_id')));
	$sphere	= stripcslashes(urldecode($request->__get('sphere')));
	$supplies = stripcslashes(urldecode($request->__get('supplies')));
	$supplies_id = stripcslashes(urldecode($request->__get('supplies_id')));
	$type = stripcslashes(urldecode($request->__get('type')));
	$comment = stripcslashes(urldecode($request->__get('comment')));
	$ship_to = stripcslashes(urldecode($request->__get('ship_to')));
	$shipping_address = stripcslashes(urldecode($request->__get('address')));
	$clws_id	= $request->__get('clws_id');
	$tempOrderNum = time().$clws_id;
	$ordered_date = date('Y-m-d');
	$ordered_time = date('h:i:s');
	$flag = true;
	//foreach($orderData['orderData'] as $value){
		$arr_ordered_data = array();
		$arr_ordered_data['sphere'] = $sphere;
		$arr_ordered_data['cylinder'] = $cylinder;
		$arr_ordered_data['bc'] = $bc;
		$arr_ordered_data['diameter'] = $diameter;
		$arr_ordered_data['axis'] = $axis;
		$arr_ordered_data['eye'] = $eye;
		$arr_ordered_data['disposable'] = $disposable;
		$arr_ordered_data['package'] = $package;
		$ordered_data = serialize($arr_ordered_data);
		if($flag){
			$qry = "Insert INTO iportal_req_orders SET 
					temp_order_num='".$tempOrderNum."',
					patient_id='".((int)$patientId)."',
					order_for='cl',
					eye='".$eye."',
					clws_id='".$clws_id."',
					brand='".$brand."',
					manufacturer='".$manufacturer."',
					type='".$type."',
					ordered_data='".$ordered_data."',
					disposable='".$disposable."',
					disposable_id='".$disposable_id."',
					package='".$package."',
					package_id='".$package_id."',
					supplies='".$supplies."',
					supplies_id='".$supplies_id."',
					boxes='".$boxes."',
					ship_to='".$ship_to."',
					shipping_address='".$shipping_address."',
					comments='".$comment."',
					ordered_date='".$ordered_date."',
					ordered_time='".$ordered_time."',
					is_approved='0',
					operator_id='0',
					optical_order_id='0',
					del_status='0'";
			$flag = $app->dbh->imw_query($qry);
			$resultSet['Order_Data_Status'] = $flag;
		}	
	//}
	$response = array("submitContactLensOrder"=>$resultSet);
	return json_encode($response);
});

/* Diagnosis Image Report */
$this->respond(array('POST','GET'), '/diaImageReport', function($request, $response, $service, $app) use(&$patientId){

	$resultSet['Diagnosis'] = array();
	
	$qry =  "SELECT 
				rt_data.*,
				DATE_FORMAT(rt_data.rad_order_date, '%m-%d-%Y') AS ordered_date,
				DATE_FORMAT(rt_data.rad_results_date,'%m-%d-%Y') AS radResultsDate,
				usr.lname as order_lname,
				usr.fname as order_fname
			FROM 
				rad_test_data rt_data
				LEFT JOIN users usr ON usr.id = rt_data.rad_order_by
			WHERE 
				rt_data.rad_status != '3' AND
				rt_data.rad_patient_id = '".$patientId."'
			ORDER BY rt_data.rad_order_date DESC";
			$flag = $app->dbh->imw_query($qry);
			$i=0;
			while($result = $app->dbh->imw_fetch_assoc($flag)){
				$resultSet['Diagnosis'][$i]['ID'] = $result['rad_test_data_id'];
				$resultSet['Diagnosis'][$i]['CONTACT_NAME'] = $result['rad_fac_name'];
				$resultSet['Diagnosis'][$i]['RADIOLOGY_NAME'] = $result['rad_name'];
				$resultSet['Diagnosis'][$i]['RESULTS'] = $result['rad_results'];
				$resultSet['Diagnosis'][$i]['ORDER_DATE'] = date('m-d-Y',strtotime($result['rad_order_date']));
				//$resultSet[$i]['ORDER_DATE'] = $result['rad_order_date'];
				if($result['rad_status']==2){
					$resultSet['Diagnosis'][$i]['STATUS'] = 'Completed';
				}
				else{
					$resultSet['Diagnosis'][$i]['STATUS'] = 'Pending';
				}
				
				if(!empty($result['order_lname']) && !empty($result['order_fname'])){
					$lname = substr($result['order_lname'],0,1);
					$fname = substr($result['order_fname'],0,1);
					$resultSet['Diagnosis'][$i]['ORDER_BY'] = $lname.$fname;
				}
				else{
					$resultSet['Diagnosis'][$i]['ORDER_BY'] = "";
				}
				$i++;
			}

	$response = array("diaImageReport"=>$resultSet);
	return json_encode($response);
});

// Hack to Accept blank  subCategory //
$this->respond(array('GET'), '', function(){});

$this->respond(function($request, $response, $service) use(&$patientId) {

});