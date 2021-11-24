<?php 
class mPay{
	private $objDb;
	function __construct(){
		$this->objDb = $GLOBALS['adodb']['db'];
	}//end of constructor.
	
	/*
	//
	Why: to log request to mpay and response of Mpay.
	Return: Nothing.	
	*/
	function mpay_sent_log($arr_data, $http_response, $xml_response){
		$query = "INSERT INTO mpay_log SET 
				  patient_id = '".$arr_data['patient_id']."', 
				  encounter_id = '".$arr_data['encounterId']."', 
				  sent_from = '".$arr_data['SENT_FROM']."', 
				  date_time = '".date('Y-m-d H:i:s')."', ";
				  unset($arr_data['phone3']);
				  unset($arr_data['SENT_FROM']);
				  unset($arr_data['sch_id']);
				  unset($arr_data['encounterId']);
		$query .= "sent_data = '".urlencode(serialize($arr_data))."', 
				  http_response = '".$http_response."', 
				  xml_response = '".$xml_response."'";
		$result = imw_query($query);
	}
	
	/*
	//
	Why: to read log request
	Return: RESULTSET or FALSE.
	*/
	function mpay_read_sent_log($NumOfRecords,$startFrom){
		$query = "SELECT * FROM mpay_log ORDER BY id desc LIMIT $startFrom,$NumOfRecords";
		$result = imw_query($query);
		if($result && imw_num_rows($result)>0){
			return $result;
		}else{
			return false;
		}
	}
	
	/*
	//
	Why: to print log resultset
	Return: HTML
	*/
	function mpay_print_log(){
		$html = '';
		$result = $this->mpay_read_sent_log(20,0);
		if($result){
			$total = imw_num_rows($result);
			$html .= '
			<table width="100%" cellpadding="2" cellspacing="0" border="1" class="result_data" style="vertical-align:top">
				<tr class="page_block_heading_patch">
					<th width="50">ID</th>
					<th width="90">Patient ID</th>
					<th width="90">Encounter ID</th>					
					<th width="90">Sent From</th>					
					<th width="110">Date/Time</th>
					<th width="400">Sent Data</th>
					<th width="100">HTTP Resp.</th>
					<th width="250">XML Response</th>
				</tr>
				';
			while($rs = imw_fetch_array($total)){
				
				$arr_sentData = unserialize(urldecode($rs['sent_data']));
				$sentData = '<table width="100%" cellpadding="1" cellspacing="0" border="0" class="resultset">';
				foreach($arr_sentData as $key=>$val){
					$sentData .= '<tr><td width="150">'.$key.'</td><th width="20">:</th><td>'.$val.'&nbsp;</td></tr>';
				}
				$sentData .= '</table>';
				$html .= '<tr>
					<td>'.$rs['id'].'</td>
					<td>'.$rs['patient_id'].'</td>
					<td>'.$rs['encounter_id'].'</td>
					<td>'.$rs['sent_from'].'</td>					
					<td>'.$rs['date_time'].'</td>
					<td>'.$sentData.'</td>
					<td>'.$rs['http_response'].'</td>
					<td>'.htmlentities($rs['xml_response']).'</td>
				</tr>';
			}
			$html .= '
			</table>';
		}
		else{
			$html = '<div class="warning padd2 border m2" align="center"><b>No Record Found!</b></div>';
		}
		return $html;		
	}	
	
	/*
	//
	Why: to make XML tags.
	Return: return XML with data.
	*/
	function mpay_get_xml($arr_mpay_data, $type=1){
		$string_xml = '';
		$xml_data = '';
		switch($type){
			case 1:
				$string_pt_demo_xml = '
					<exchange_request>	
						<request>
							<create_update_patient_demographic>
								<patient_identifier>
									<patient_sid>'.$arr_mpay_data['patient_id'].'</patient_sid>
								</patient_identifier>
								<first_name>'.strtoupper($arr_mpay_data['fname']).'</first_name>
								<middle_name>'.strtoupper($arr_mpay_data['mname']).'</middle_name>
								<last_name>'.strtoupper($arr_mpay_data['lname']).'</last_name>
								<sex>'.strtoupper(substr($arr_mpay_data['sex'],0,1)).'</sex>
								<birth_date>'.$arr_mpay_data['dob'].'</birth_date>
								<address>
									<domestic_address>
										<address_1>'.$arr_mpay_data['street'].'</address_1>
										<city>'.$arr_mpay_data['city'].'</city>
										<state_abreviation>'.$arr_mpay_data['state'].'</state_abreviation>
										<zip5>'.$arr_mpay_data['postal_code'].'</zip5>
									</domestic_address>
								</address>
								<phone_number>'.$arr_mpay_data['phone1'].'</phone_number>
								<alternate_phone_number>'.$arr_mpay_data['phone2'].'</alternate_phone_number>
								<healthplan_identifier>'.$arr_mpay_data['inhousecode'].'</healthplan_identifier>
								<healthplan_name>'.$arr_mpay_data['icompname'].'</healthplan_name>
								<healthplan_member_identifier>'.$arr_mpay_data['policy_number'].'</healthplan_member_identifier>
								<copay_amount>'.$arr_mpay_data['idata_copay'].'</copay_amount>
								<location_sid>'.$arr_mpay_data['mpay_locid'].'</location_sid>
							</create_update_patient_demographic>
						</request>
					</exchange_request>';
					$xml_data = $string_pt_demo_xml;
				break;
			case 2:
				$string_pt_encounter_xml = '
					<exchange_request>	
						<request>
							<create_update_patient_encounter>
								<patient_encounter>
									<patient_encounter_identifier>
										<patient_identifier>
											<patient_sid>'.$arr_mpay_data['patient_id'].'</patient_sid>
										</patient_identifier>
										<schedule_instance_sid>'.$arr_mpay_data['schedule_instance_sid'].'</schedule_instance_sid>
									</patient_encounter_identifier>
									<location_sid>'.$arr_mpay_data['mpay_locid'].'</location_sid>
									<schedule_date>'.$arr_mpay_data['sch_date'].'</schedule_date>
									<schedule_time>'.$arr_mpay_data['sch_time'].'</schedule_time>
									<url_type>none</url_type>
									<service_item>
										<pre_calculated_line_item>
											<description>CoPay Collected</description>
											<service_amount>'.$arr_mpay_data['copay_paid'].'</service_amount>
											<service_item_type>pay_now_copay</service_item_type>
										</pre_calculated_line_item>
										<pre_calculated_line_item>
											<description>Addition Amount Collected</description>
											<service_amount>'.$arr_mpay_data['non_copay_paid'].'</service_amount>
											<service_item_type>pay_now_non_copay</service_item_type>
										</pre_calculated_line_item>
										<pre_calculated_line_item>
											<description>Amount Due</description>
											<service_amount>'.$arr_mpay_data['balance'].'</service_amount>
											<service_item_type>pay_later</service_item_type>
										</pre_calculated_line_item>
									</service_item>
								</patient_encounter>
							</create_update_patient_encounter>
						</request>
					</exchange_request>';
					$xml_data = $string_pt_encounter_xml;
				break;
			default:
				//DO NOTHING.
		}//end of switch.
		return $xml_data;
	}
	
	
	/*
	//
	Why: to send CURL requst.
	Return: "true" on success.
	*/
	function mpay_curl_send($xml_data){
		$mpay_data = $this->mpay_auth_details();
		if(is_array($mpay_data)){
			$url = $mpay_data['mpay_url'];
			$headers = array("Content-Type: text/xml",
						"Host: ".$mpay_data['mpay_url']); 
			$ch=curl_init($url);
			curl_setopt($ch,CURLOPT_VERBOSE,0);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,1);
			curl_setopt($ch,CURLOPT_POST,0);
			curl_setopt($ch, CURLOPT_USERPWD, $mpay_data['mpay_user'].':'.$mpay_data['mpay_pass']);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_NOPROGRESS,0);
			curl_setopt($ch,CURLOPT_HTTPDHEADER,$headers);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $xml_data);
			$request_result=curl_exec($ch);
			$array_curl_response = curl_getinfo($ch);
			return array($request_result,$array_curl_response);
		}else{
			//log error.
			$this->mpay_sent_log(array(), '', 'URL, UserName, Password not found in Database (copay_policies table)');
			return false;
		}
	}

	/*
	//
	Why: To retrive mpayURL, mpayUSERNAME, mpayPASSWORD from Database.
	Return: array('URL','USER','PASS');
	*/
	function mpay_auth_details(){
		$arr_values = array();
		$query = "SELECT mpay_url, mpay_user, mpay_pass FROM copay_policies WHERE policies_id = 1";
		$result = imw_query($query);
		if($result && imw_num_rows($result)==1){
			$rs = imw_fetch_array($result);
			$arr_values['mpay_url'] = $rs['mpay_url'];
			$arr_values['mpay_user'] = $rs['mpay_user'];
			$arr_values['mpay_pass'] = $rs['mpay_pass'];
			return $arr_values;
		}else{
			return false;
		}
	}
	
	/*
	//
	Why: make custom alert message to tell the user what curl tells.
	Return: MESSAGE STRING or SCRIPT ALERT according to parameter.
	*/
	function mpay_work_done_message($curl_response, $msgType='text'){
		$curl_msg = '';
		if(is_array($curl_response) && $curl_response[1]['http_code']==200){
			$curl_msg = "Data posted to Mpay successfully.";
		}else{
			$curl_msg = "Data posting to Mpay failed.";
		}
		if($msgType=='script')
			$curl_msg = '<script language="javascript">alert("'.$curl_msg.'");</script>';
		
		return $curl_msg;
	}
	
	/*
	//
	Why: to check mpay existence by patient id.
	Return: true/false
	*/
	function pt_mpay_exists($patient_id){
		if(!verify_payment_method("MPAY")){return false;}
		$return = false;
		$query = "SELECT sent_data FROM mpay_log WHERE patient_id = '$patient_id' AND http_response = '200'";
		$result = imw_query($query);
		if($result && imw_num_rows($result)>0){
			$return = true;
		}else{
			$return = false;
		}
		return $return;
	}	

	/*
	//
	Why: to check mpay existence by encounter id & patient id.
	Return: true/false
	*/
	function pt_encounter_mpay_exists($patient_id,$enc_id){
		if(!verify_payment_method("MPAY")){return false;}
		$return = false;
		$query = "SELECT sent_data FROM mpay_log WHERE patient_id = '$patient_id' AND encounter_id = '$enc_id' AND http_response = '200'";
		$result = imw_query($query);
		if($result && imw_num_rows($result)>0){
			$return = true;
		}else{
			$return = false;
		}
		return $return;
	}	


}//end of class.
?>