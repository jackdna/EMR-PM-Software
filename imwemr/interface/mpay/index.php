<?php
	require_once("../../config/globals.php"); 
	include_once("../../library/classes/class.mpay.php");
	//$objDb = $GLOBALS['adodb']['db'];
	$objMpay = new mPay;
	$arr_mpay_data1 = array();
	$arr_mpay_data2 = array();
	
	function jsAlert($text){echo '<script language="javascript" type="text/javascript">alert("'.$text.'"); window.parent.hide_mpay_div();</script>';die();}
	$arr_mpay_data2['SENT_FROM'] = isset($_REQUEST['from']) ? $_REQUEST['from'] : '';
	$arr_mpay_data2['copay_paid'] = isset($_REQUEST['copay']) ? $_REQUEST['copay'] : '0.00';
	$arr_mpay_data2['non_copay_paid'] = isset($_REQUEST['paid']) ? $_REQUEST['paid'] : '0.00';
	$arr_mpay_data2['balance'] = isset($_REQUEST['balance']) ? $_REQUEST['balance'] : '0.00';
	$arr_mpay_data1['patient_id'] = isset($_REQUEST['patient_id']) ? $_REQUEST['patient_id'] : '';
	$arr_mpay_data2['patient_id'] = $arr_mpay_data1['patient_id'];
	$sch_id = isset($_REQUEST['sch_id']) ? $_REQUEST['sch_id'] : '';
	
	//IF ANY BASIC INVALID DATA FOUND, THEN EXIT WITH A MESSAGE
	if((isset($_REQUEST['patient_id']) && $arr_mpay_data1['patient_id']=='')  || (isset($_REQUEST['sch_id']) && $sch_id=='')){
		jsAlert('Invalid Patient ID or Scheduler ID');
		exit();
	}

	$sch_query = "SELECT sa.sa_app_start_date, sa.sa_app_starttime, sa.case_type_id, cmt.encounterId, clch.form_id FROM schedule_appointments sa 
					LEFT JOIN chart_left_cc_history clch ON (clch.date_of_service=sa.sa_app_start_date 
					AND clch.patient_id = '".$arr_mpay_data1['patient_id']."') 
					LEFT JOIN chart_master_table cmt ON (cmt.id = clch.form_id AND cmt.patient_id = '".$arr_mpay_data1['patient_id']."')
					WHERE sa.id = '$sch_id'";
	$sch_result = imw_query($sch_query);
	if($sch_result){
		$sch_rs = imw_fetch_array($sch_result);
		$arr_mpay_data2['sch_date'] 	= $sch_rs['sa_app_start_date'];
		$arr_mpay_data2['sch_time'] 	= $sch_rs['sa_app_starttime'];
		$current_caseid = $sch_rs['case_type_id'];
		$arr_mpay_data2['encounterId'] = $sch_rs['encounterId'];
		$temp_sch_date = explode('-',$arr_mpay_data2['sch_date']);
		$arr_mpay_data2['schedule_instance_sid'] = $temp_sch_date[0].$temp_sch_date[1].$temp_sch_date[2].'_'.$arr_mpay_data1['patient_id'];
		
		$pt_query = "SELECT pd.lname, pd.fname, pd.mname, pd.sex, pd.DOB, pd.street, pd.city, pd.state, pd.postal_code, pd.phone_home, pd.phone_biz, 
				  pd.phone_cell, pd.default_facility, 
				  pft.mpay_locid, idata.copay, idata.policy_number, icomp.in_house_code, icomp.name 
				  FROM patient_data pd 
				  LEFT JOIN pos_facilityies_tbl pft ON (pd.default_facility = pft.pos_facility_id) 
				  LEFT JOIN insurance_data idata ON (pd.id = idata.pid AND idata.actInsComp='1' AND idata.type='primary' AND idata.provider>0 
				  AND idata.ins_caseid = '$current_caseid') 
				  LEFT JOIN insurance_companies icomp ON (icomp.id = idata.provider) 
				  WHERE pd.id = '".$arr_mpay_data1['patient_id']."' ORDER BY idata.id DESC LIMIT 0,1";

		$pt_result = imw_query($pt_query);
		if($pt_result && imw_num_rows($pt_result) ==1){
			$pt_rs = imw_fetch_array($pt_result);
			$arr_mpay_data1['fname'] 		= $pt_rs["fname"];
			$arr_mpay_data1['mname'] 		= $pt_rs["mname"];
			$arr_mpay_data1['lname'] 		= $pt_rs["lname"];
			$arr_mpay_data1['sex'] 			= $pt_rs["sex"];
			$arr_mpay_data1['dob'] 			= $pt_rs["DOB"];
			$arr_mpay_data1['street'] 		= $pt_rs["street"];
			$arr_mpay_data1['city'] 			= $pt_rs["city"];
			$arr_mpay_data1['state'] 		= $pt_rs["state"];
			$arr_mpay_data1['postal_code'] 	= $pt_rs["postal_code"];
			$arr_mpay_data1['phone1'] 		= $pt_rs["phone_home"];
			$arr_mpay_data1['phone2'] 		= $pt_rs["phone_biz"];
			$arr_mpay_data1['phone3'] 		= $pt_rs["phone_cell"];
			$arr_mpay_data1['mpay_locid'] 	= empty($pt_rs["mpay_locid"]) ? 1 :  $pt_rs["mpay_locid"];
			$arr_mpay_data2['mpay_locid']	= $arr_mpay_data1['mpay_locid'];
			/*jsAlert('Mpay Location Id Not Found!') : */
			if($arr_mpay_data1['phone1']==''){
				$arr_mpay_data1['phone1'] = $arr_mpay_data1['phone3'];
			}
			else if($arr_mpay_data1['phone1']==''){
				$arr_mpay_data1['phone1'] = $arr_mpay_data1['phone2'];
				$arr_mpay_data1['phone2'] = '';
			}
			$arr_mpay_data1['inhousecode'] 	= $pt_rs['in_house_code'];
			$arr_mpay_data1['icompname'] 	= $pt_rs['name'];
			$arr_mpay_data1['policy_number'] = $pt_rs['policy_number'];
			$arr_mpay_data1['idata_copay'] 	= $pt_rs['copay'];
		}//end of main 1st if.
		
		$xml_data = $objMpay->mpay_get_xml($arr_mpay_data1, 1);//getting formed XML data for patient_demographics.
		$curl_response = $objMpay->mpay_curl_send($xml_data); //sending formed XML data to mpay VIA curl.
		$objMpay->mpay_sent_log($arr_mpay_data1, $curl_response[1]['http_code'], $curl_response[0]); //saving LOG.
		if($curl_response[1]['http_code']=='200'){
			$xml_data2 = $objMpay->mpay_get_xml($arr_mpay_data2, 2);//getting formed XML data for patient_encounter.
			$curl_response2 = $objMpay->mpay_curl_send($xml_data2); //sending formed XML data2 to mpay VIA curl.
			$objMpay->mpay_sent_log($arr_mpay_data2, $curl_response2[1]['http_code'], $curl_response2[0]); //saving LOG.
		}else{
			jsAlert('Process Failed! Patient demographics data is not complete.');
		}
		$curl_msg = $objMpay->mpay_work_done_message($curl_response2, 'script');
		echo $curl_msg;
		if(!eregi("failed",$curl_msg)){
			echo '<script language="javascript">window.parent.hide_mpay_div();window.open(\'https://www.mpaygateway.com/mpay\',null,\'location=no, status=no, menubar=no\')</script>';
		}else if(eregi("failed",$curl_msg)){
			echo '<script language="javascript">window.parent.hide_mpay_div();</script>';
		}
	}else if(isset($_REQUEST['patient_id'])){
		jsAlert('Unable to process Patient Data. Contact Administrator');
	}	
?>