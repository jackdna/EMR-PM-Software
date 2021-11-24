<?php

function get_auth($isoDate='') {
	$return=array();
	$user=RX_NOTIFICATION_USER;
	$pass=RX_NOTIFICATION_PASS;
	
	$data=array();
	$data['SentDate']=$isoDate;
	$data['Authenticate']['Username']=$user;
	$data['Authenticate']['Password']=$pass;

	$result = curl_request($data,'Authentication');

	if($result && isset($result['Status']) && isset($result['Status']['Result']) && $result['Status']['Result']=='OK'){
		
		if(isset($result['AuthenticationResponse']['SecurityToken']) && $result['AuthenticationResponse']['SecurityToken']!='') {
			$return['token']=$result['AuthenticationResponse']['SecurityToken'];
		}
		
	} else if($result && isset($result['Status']) && isset($result['Status']['Result']) && $result['Status']['Result']=='ERR'){
		$return['error']=$result['Status']['Description'];
	}
	
	return $return;
}


function checkSMSCapabilities($isoDate='',$token='',$phone) {
	$return=array();
	if($token) {
		$data=array();
		$data['SentDate']=$isoDate;
		$data['CheckSMSCapabilities']['PhoneNumber']=$phone;
		
		$result = curl_request($data,'CheckSMSCapabilities',$token);
		
		if($result && isset($result['Status']) && isset($result['Status']['Result']) && $result['Status']['Result']=='OK'){
			$SMSCapabilities=$result['CheckSMSCapabilitiesResponse']['SMSCapabilities'];
			foreach($SMSCapabilities as $row) {
				if( isset($row['PhoneNumber']) && $row['PhoneNumber']==$phone && isset($row['SMSCapable']) && $row['SMSCapable']=='1'  ) {
					$return['SendSMSInvitation']=true;
				}
			}				
		} else if($result && isset($result['Status']) && isset($result['Status']['Result']) && $result['Status']['Result']=='ERR'){
			$return['error']=$result['Status']['Description'];
		}
	}
	
	return $return;
}


function send_rx_notification_consent($params=array(),$phone='') {
	$dt = new DateTime();
	$dt_last= substr($dt->format('u'),0,3);
	$isoDate = $dt->format('Y-m-d\TH:i:s.').$dt_last.'Z';
	
	//$isoDate = date(DateTime::ISO8601);
	$msg=array();
	try {
		$auth = get_auth($isoDate);
	
		$SendSMSInvitation=false;
		if(isset($auth['token']) && $auth['token']!='') {
			$token = $auth['token'];
			$phone='3058424179';
			
			/* checkSMSCapabilities code starts here*/
			$retArr=checkSMSCapabilities($isoDate,$token,$phone);
			if(isset($retArr['SendSMSInvitation']) && $retArr['SendSMSInvitation']) {
				$SendSMSInvitation=$retArr['SendSMSInvitation'];
			} else {
				throw new Exception('CheckSMSCapabilities Failed Error : '. $return['error']);
			}
			
			/* SendInvitations code starts here*/
			if($SendSMSInvitation) {
				/*
				$Address=array();
				$Address['ZipCode']='21047';
				
				$Patient=array();
				$Patient['ExternalId']='22116008487';
				$Patient['FirstName']='Steven';
				$Patient['LastName']='Testpatient';
				$Patient['MobilePhone']='9515551212';
				$Patient['DateOfBirth']='1970-03-01';
				$Patient['Gender']='M';
				$Patient['Address']=$Address;
				
				$Insurance=array();
				$Insurance['MemberId']='102713146';
				$Insurance['Group']='DFSTS';
				$Insurance['RxBin']='123456';
				$Insurance['RxPcn']='BNRX';
				
				$Doctor=array();
				$Doctor['NPI']='1834203097';
				$Doctor['FirstName']='One';
				$Doctor['LastName']='PrescriberOne';
				$Doctor['Specialty']='Cardiology';
				
				$pharmacy_address=array();
				$pharmacy_address['Address1']='2101 FALLSTON ROAD';
				$pharmacy_address['City']='FALLSTON';
				$pharmacy_address['State']='MD';
				$pharmacy_address['ZipCode']='210471425';
				
				$Pharmacy=array();
				$Pharmacy['StoreName']='RITE AID-2101 FALLSTON RD';
				$Pharmacy['NCPDPID']='2121527';
				$Pharmacy['Address']=$pharmacy_address;
				$Pharmacy['Phone']='4108777849';
				$Pharmacy['Fax']='4108779150';
				$Pharmacy['Flags']='Retail';
				
				$Medication=array();
				$Medication['NDC']='00781202076';
				$Medication['DrugDescription']='amoxicillin';
				
				$Prescription=array();
				$Prescription['ExternalId']='1522091219296';
				$Prescription['Status']='Prescribed';
				
				$dt1 = new DateTime();
				$dt1_last= substr($dt1->format('u'),0,3);
				$WrittenDate = $dt1->format('Y-m-d\TH:i:s.').$dt1_last.'Z';
				
				$WrittenDate='2018-03-26T19:06:39.273Z';
				
				$Prescription['WrittenDate']=$WrittenDate;
				$Prescription['Doctor']=$Doctor;
				$Prescription['Pharmacy']=$Pharmacy;
				$Prescription['Medication']=$Medication;
				$Prescription['Quantity']='30';
				$Prescription['QuantityUnit']='each';
				$Prescription['Directions']='take one tablet each day at bedtime';
				$Prescription['DeliveryType']='PickupInStore';
				
				$PrescriptionInfo=array();
				$PrescriptionInfo['ReferenceId']='221060999';
				$PrescriptionInfo['PatientConsentObtained']=true;
				$PrescriptionInfo['Patient']=$Patient;
				$PrescriptionInfo['Insurance']=$Insurance;
				$PrescriptionInfo['Prescription']=$Prescription;
				
				$data=array();
				$data['SentDate']=$isoDate;
				$data['SendSMSInvitation']['PrescriptionInfo']=$PrescriptionInfo;
				*/
				$data=$params;
				
				$final_result = curl_request($data,'SendSMSInvitation',$token);
				
				if($final_result && isset($final_result['Status']) && isset($final_result['Status']['Result']) && $final_result['Status']['Result']=='ERR'){
					throw new Exception('SendSMSInvitation Failed Error : '. $final_result['Status']['Description']);
				} else if($final_result && isset($final_result['Status']) && isset($final_result['Status']['Result']) && $final_result['Status']['Result']=='OK'){
					$msg['success'] = $final_result['Status']['Description'];
				}
				
			} else {
				throw new Exception('SendSMSInvitation Failed Error : '. $return['error']);
			}
			/* SendInvitations code ends here*/

		} else {
			throw new Exception('Auth Failed Error : '. $return['error']);
		}
	
	} catch(Exception $e) {
		$msg['error']=$e->getMessage();
	}

	return $msg;
	
	
}

function curl_request($params,$endpoint='Authentication',$token='')
{
	$request_headers = array();
	$request_headers[] = 'Accept:application/json';
	$request_headers[] = 'Content-Type:application/json';
	if($endpoint!='Authentication'){
		$request_headers[] = 'Authorization:Bearer '.$token;
	}
	
	$payload=json_encode($params);

	// API End Point
	$url = RX_NOTIFICATION_URL;
	
	
	$date_time = date('Y-m-d H:i:s');
	$log_req_qry = "INSERT INTO rx_notification_consent_log (request_header,request_url,request_to,request_data,request_date_time,operator_id)
					VALUES ('".json_encode($request_headers)."','".$url."','".$endpoint."','".$payload."','".$date_time."','".$_SESSION['authId']."') ";
	$log_req_sql = imw_query($log_req_qry);
	$log_req_id = imw_insert_id();

	try
	{
		// Initiate Curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /* Return the response */
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_HEADER, false); /* Include header in Output/Response */
		curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

		// Execute Curl Request
		$result = curl_exec($ch);
		// Get data response code
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		
		$log_res_qry = "UPDATE rx_notification_consent_log SET response_data = '".$result."', response_date_time = '".date('Y-m-d H:i:s')."' WHERE id = ".$log_req_id." ";
		$log_res_sql = imw_query($log_res_qry);
		
		$result = json_decode($result, true);
	}
	catch(Exception $e) {
		echo $e->getMessage();die;
	}

	return $result;
}

?>