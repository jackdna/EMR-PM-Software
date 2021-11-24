<?php
include_once(dirname(__FILE__) .'/../../config/globals.php');
include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
include_once($GLOBALS['fileroot']."/library/classes/cls_common_function.php");

$OBJCommonFunction = new CLSCommonFunction;
$OBJRabbitmqExchange = new Rabbitmq_exchange();

class Patients
{
    
    public function addUpdatePatient($patient_id=0)
    {
        if($patient_id==0) return false;
        
        global $OBJRabbitmqExchange;
        
        $patient_arr=array();
        $patient_arr = $this->getPatientDataArray($patient_id);

        $resource='Patients';
        $method='POST';
        /*Rabbit MQ call to create patient at Portal*/
        $response=$OBJRabbitmqExchange->send_request($patient_arr,$patient_id,$resource,$method);
        
        $response=json_decode($response, true);
        
        if(count($response) > 0 && $response['externalId']==$patient_id) {
            $this->updateErpPatient($response,$patient_arr);
        }
        
    }

    public function updateErpPatient($response=array(),$patient_arr=array()) {
        $patient_id = $response['externalId'];
        $erp_patient_id = $response['id'];
        $erp_username = $response['username'];
        $erp_password = $response['password'];
        $resp_id = $response['representative']['externalId'];
        $erp_resp_username = $response['representative']['username'];
        $erp_resp_password = $response['representative']['password'];
        $erp_pt_contact_id = $response['contact']['id'];
        $erp_pt_cellph_id = '';
        $erp_pt_bizph_id = '';
        $erp_pt_homeph_id = '';
        $erp_pt_email_id = $response['contact']['emailAddresses'][0]['id'];
        $erp_comm_category_id = $response['communicationPreferences'][0]['categoryId'];
        $phoneNumbers = $response['contact']['phoneNumbers'];
        foreach($phoneNumbers as $phone) {
            if( $phone['alias'] == 'Mobile' ){$erp_pt_cellph_id = $phone['id'];}
            if( $phone['alias'] == 'Work' ){$erp_pt_bizph_id = $phone['id'];}
            if( $phone['alias'] == 'Home' ){$erp_pt_homeph_id = $phone['id'];}
        }
        
        $postalAddresses=$response['contact']['postalAddresses'];
        $sql_12="select default_address from patient_data where id = ".$patient_id." ";
        $sql_res=imw_query($sql_12);
        $row=imw_fetch_assoc($sql_res);
        if(count($postalAddresses)==1 && $row['default_address']==''){
            $address = $response['contact']['postalAddresses'][0];
            $erp_pt_postaladd_id = $response['contact']['postalAddresses'][0]['id'];
        } else {
            foreach($postalAddresses as $address) {
                $zipcode = $address['zip'];
                if( $zipcode!='' && strlen($zipcode)>5 ){
                    $zipcode_arr=explode('-',$zipcode);
                    $zipcode=$zipcode_arr[0];
                }
                $qryERP_U3="Update patient_multi_address
                            SET erp_pt_multiadd_id='".$address['id']."'
                        WHERE patient_id=".$patient_id." 
                        AND street='".$address['address1']."' 
                        AND city='".$address['city']."' 
                        AND state='".$address['state']."' 
                        AND postal_code='".$zipcode."' ";
                
                $sqlERP_U3 = imw_query($qryERP_U3);
            }
        }
        
        $qry=' ';
        if($erp_password!='') {
            $qry = " erp_password = '".$erp_password."',  ";
        }
        
        $qryERP_U1 = "Update patient_data Set erp_patient_id = '$erp_patient_id', 
										 erp_username = '".$erp_username."', $qry
										 erp_pt_contact_id = '".$erp_pt_contact_id."', 
										 erp_pt_email_id = '".$erp_pt_email_id."', 
										 erp_pt_cellph_id = '".$erp_pt_cellph_id."', 
										 erp_pt_bizph_id = '".$erp_pt_bizph_id."', 
										 erp_pt_homeph_id = '".$erp_pt_homeph_id."', 
										 erp_pt_postaladd_id = '".$erp_pt_postaladd_id."', 
										 erp_pt_comm_category_id = '".$erp_comm_category_id."' 
								Where id = ".$patient_id." ";

		$sqlERP_U1 = imw_query($qryERP_U1);
        
        $qry=' ';
        if($erp_resp_password!='') {
            $qry = " , erp_resp_password = '".$erp_resp_password."'  ";
        }
        $qryERP_U2 = "Update resp_party Set erp_resp_username = '$erp_resp_username' $qry
								Where patient_id =".$patient_id." 
								and id =".$resp_id." ";

		$sqlERP_U2 = imw_query($qryERP_U2);
        
		
		/*Upload Patient Communication Preferences */
		$this->updateCommPreferences($patient_id);
    }
    
    private function getPatientDataArray($patient_id=0)
    {
        if($patient_id==0) return false;
        
        global $OBJCommonFunction;
        
        $row=array();
        $sql="SELECT * FROM `patient_data` WHERE id=".$patient_id." ";
        $res=imw_query($sql);
        $row=imw_fetch_assoc($res);

        $patientStatus=true;
        if($row['patientStatus']!='Active'){
            $patientStatus=false;
        }
        //$data = $representative = $locations = $contact = $phoneNumbers = $postalAddresses = $emailAddresses = $communicationPreferences = array();
        
        $locations=$locations_arr=array();
        $fac_sql="SELECT id FROM facility WHERE default_facility=".$row['default_facility']." and erp_id!='' ";
        $fac_res=imw_query($fac_sql);
        if($fac_res && imw_num_rows($fac_res)>0){
            while( $facility = imw_fetch_assoc($fac_res) ) {
                $locations_arr[$facility['id']]=$facility['id'];
            }
        }
        
        $ethnicity_id = $race_id = $marital_status_id = $gender_id = '';
        
        $races_sql = "select race_id,erp_race_id from race where is_deleted=0 and race_name='".$row['race']."' ";
        $races_res=imw_query($races_sql);
        if($races_res && imw_num_rows($races_res)>0){
            $race = imw_fetch_assoc($races_res);
            $race_id=$race['race_id'];
        }

        $ethnicity_sql = "select ethnicity_id,erp_ethn_id from ethnicity where is_deleted=0 and ethnicity_name='".$row['ethnicity']."' ";
        $ethnicity_res=imw_query($ethnicity_sql);
        if($ethnicity_res && imw_num_rows($ethnicity_res)>0){
            $ethnicity = imw_fetch_assoc($ethnicity_res);
            $ethnicity_id=$ethnicity['ethnicity_id'];
        }

        $marital_sql = "select mstatus_id,erp_marital_id from marital_status where is_deleted=0 and mstatus_name='".$row['status']."' ";
        $marital_res=imw_query($marital_sql);
        if($marital_res && imw_num_rows($marital_res)>0){
            $marital = imw_fetch_assoc($marital_res);
            $marital_status_id=$marital['mstatus_id'];
        }

        $qry = "Select gender_id,erp_gender_id from gender_code Where is_deleted = 0 and gender_name='".$row['sex']."' ";
        $sql=imw_query($qry);
        if($sql && imw_num_rows($sql)>0){
            $gender = imw_fetch_assoc($sql);
            $gender_id=$gender['gender_id'];
        }
        
        $recall_date=array();
        $qry2 = "Select recalldate FROM patient_app_recall WHERE patient_id=".$patient_id." AND recalldate>'".date('Y-m-d')."' ORDER BY recalldate ASC LIMIT 0,2 ";
        $rs1= imw_query($qry2);
        if($rs1 && imw_num_rows($rs1)>0){
            $count=0;
            while($res1=imw_fetch_assoc($rs1)) {
                $count++;
                $recall_date['recalldate_'.$count]=$res1['recalldate'];
            }
        }
            
        
        /* Responsible party data*/
        /* Create representative array starts here*/
        $representative=array();
        $qry1 = "Select id,patient_id,fname,mname,lname,email,erp_resp_username,erp_resp_password,erp_resp_imw_password from resp_party where patient_id =".$patient_id." ";
        $sql1=imw_query($qry1);
        if($sql1 && imw_num_rows($sql1)>0){
            $row1=imw_fetch_assoc($sql1);
            
            $representative['externalId']=$row1['id'];
            $representative['username']=$row1['erp_resp_username'];
			//if(isset($row1['erp_resp_password']) && $row1['erp_resp_password']!='') {
				//$representative['password']=$row1['erp_resp_password'];
			//}else {
				$representative['password']=$row1['erp_resp_imw_password'];
			//}
            $representative['email']=$row1['email'];
            $representative['firstName']=$row1['fname'];
            $representative['lastName']=$row1['lname'];
        }
        /* Create representative array ends here*/
        
       
        /* Create email array starts here*/
        $emailAddresses=$row_email=array();
        if($row['email'] != '') {
            $row_email['id'] = $row['erp_pt_email_id'];
            $row_email['alias'] = "Email";
            $row_email['address'] = $row['email'];
            $row_email['default'] = true;
            $row_email['sortOrder'] = 0;
			$emailAddresses[]=$row_email;
        }
        /* Create email array ends here*/

        
        /* Create phone numbers array starts here*/
        $home=$work=$mobile=false;
        switch($row['preferr_contact']) {
            case '0':
                $home=true;
                break;
            case '1':
                $work=true;
                break;
            case '2':
                $mobile=true;
                break;
        }
		
        //Home phone
        $phoneNumbers=array();
        if($row['phone_home'] != '') {
            $phoneHome=array();
            $phoneHome['id'] = $row['erp_pt_homeph_id'];
            $phoneHome['alias'] = "Home";
            $phoneHome['number'] = $row['phone_home'];
            $phoneHome['default'] = $home;
            $phoneHome['useForSms'] = $home;
            $phoneHome['sortOrder'] = 0;
            $phoneNumbers[] = $phoneHome;
        }
        //Work phone
        if($row['phone_biz'] != '') {
            $phoneBiz=array();
            $phoneBiz['id'] = $row['erp_pt_bizph_id'];
            $phoneBiz['alias'] = "Work";
            $phoneBiz['number'] = $row['phone_biz'].($row['phone_biz_ext'] ? $row['phone_biz_ext'] : '');
            $phoneBiz['default'] = $work;
            $phoneBiz['useForSms'] = $work;
            $phoneBiz['sortOrder'] = 0;
            $phoneNumbers[] = $phoneBiz;
        }
        //Mobile phone
        if($row['phone_cell'] != '') {
            $phoneCell=array();
            $phoneCell['id'] = $row['erp_pt_cellph_id'];
            $phoneCell['alias'] = "Mobile";
            $phoneCell['number'] = $row['phone_cell'];
            $phoneCell['default'] = $mobile;
            $phoneCell['useForSms'] = $mobile;
            $phoneCell['sortOrder'] = 0;
            $phoneNumbers[] = $phoneCell;
        }
        /* Create phone numbers array ends here*/


        /* Create postal address array starts here*/
        $postalAddresses=array();
        if($row['default_address']!=''){
            $sql="SELECT * FROM `patient_multi_address` WHERE patient_id=".$patient_id." and del_status=0 ";
            $res=imw_query($sql);
            while($address=imw_fetch_assoc($res)) {
                $default_address=false;
                if($row['default_address']==$address['id']) {
                    $default_address=true;
                }
                $postalAddresses_arr=array();
                $postalAddresses_arr['id'] = $address['erp_pt_multiadd_id']; 
                $postalAddresses_arr['alias'] = ""; 
                $postalAddresses_arr['address1'] = $address['street']; 
                $postalAddresses_arr['address2'] = $address['street2']; 
                $postalAddresses_arr['city'] = $address['city']; 
                $postalAddresses_arr['state'] = $address['state']; 
                $postalAddresses_arr['countryId'] = ""; 
                $postalAddresses_arr['countryCode'] = "USA"; 
                $postalAddresses_arr['countryName'] = "United States"; 
                $postalAddresses_arr['zip'] = $address['postal_code'].($address['zip_ext'] ? '-'.$address['zip_ext'] : ''); 
                $postalAddresses_arr['default'] = $default_address; 
                
                $postalAddresses[]=$postalAddresses_arr;
            }
        } else {
            $postalAddresses_arr=array();
            $postalAddresses_arr['id'] = $row['erp_pt_postaladd_id']; 
            $postalAddresses_arr['alias'] = ""; 
            $postalAddresses_arr['address1'] = $row['street']; 
            $postalAddresses_arr['address2'] = $row['street2']; 
            $postalAddresses_arr['city'] = $row['city']; 
            $postalAddresses_arr['state'] = $row['state']; 
            $postalAddresses_arr['countryId'] = ""; 
            $postalAddresses_arr['countryCode'] = "USA"; 
            $postalAddresses_arr['countryName'] = "United States"; 
            $postalAddresses_arr['zip'] = $row['postal_code'].($row['zip_ext'] ? '-'.$row['zip_ext'] : ''); 
            $postalAddresses_arr['default'] = true; 

            $postalAddresses[]=$postalAddresses_arr;
        }
        /* Create postal address array ends here*/
        
        
        /* Create contact details array starts here*/
        $contact=array();
        $contact['id'] = $row['erp_pt_contact_id']; 
        $contact['firstName'] = $row['fname']; 
        $contact['middleName'] = $row['mname']; 
        $contact['lastName'] = $row['lname']; 
        $contact['suffix'] = $row['suffix']; 
        $contact['prefix'] = $row['title']; 
        $fullname = $row['fname'].($row['mname']?' '.$row['mname']:'').' '.$row['lname'];
        $contact['fullName'] = $fullname; 
        $contact['companyName'] = ""; 
        $contact['jobTitle'] = ""; 
        $contact['emailAddresses'] = $emailAddresses;
        $contact['phoneNumbers'] = $phoneNumbers;
        $contact['postalAddresses'] = $postalAddresses;
        $contact['notes'] = ""; 
        /* Create contact details array ends here*/
        
        $communicationPreferences=array();
        $communicationPreferences['categoryId']=$row['erp_pt_comm_category_id'];
        $communicationPreferences['allowVoice']=$row['hipaa_voice'];
        $communicationPreferences['allowEmail']=$row['hipaa_email'];
        $communicationPreferences['allowSms']=$row['hipaa_text'];
        
        $data=array();
        $data['username']=""; //$row['username'];
        $data['password']=""; //$row['password'];
        $data['representative']=$representative;
        //"birthday": "2020-03-11T02:29:31.5424981-04:00",
        //$dob = $row['DOB'].'T'.'00:00:00'.'.'.date('vP');
        //$dob = $row['DOB'].'T'.'00:00:00'.'.'.date('uP');
        $dob = $row['DOB'].'T00:00:00';
        $data['birthday']=$dob;  
        $data['recallDate1']=(isset($recall_date['recalldate_1']) && $recall_date['recalldate_1']!='')?$recall_date['recalldate_1']:"";  
        $data['recallDate2']=(isset($recall_date['recalldate_2']) && $recall_date['recalldate_2']!='')?$recall_date['recalldate_2']:"";  
        $data['notes']="";
        $data['active']=$patientStatus;  
        $data['invitedToPortal']=false;  
        $data['locations']=($row['default_facility'])?$locations_arr[$row['default_facility']]:array();  
        $data['contact']=$contact;  
        // get last 4 digit of ssn number
        $last4SSN=($row['ss'])?substr(str_replace("-", "", $row['ss']),-4):"";
        $data['last4SSN']=$last4SSN;  
        $data['nextLogOnSecurityVerificationPatient']=true;  
        $data['nextLogOnSecurityVerificationRepresentative']=false;  
        
        $data['sexExternalId']=$gender_id;  
        $data['maritalStatusExternalId']=$marital_status_id;  
        $data['raceExternalId']=$race_id;  
        $data['ethnicityExternalId']=$ethnicity_id; 
        $data['languageName']=$row['lang_code'];  
        
                
        /*Get insurance data for a patient*/
        $sql="SELECT insd.id,insd.type,insd.policy_number,insd.group_number,insd.expiration_date,insd.self_pay_provider,insct.normal,insct.vision,inscm.name as insurance_company, 
            insd.subscriber_street,insd.subscriber_street_2,insd.subscriber_city,insd.subscriber_state,insd.subscriber_postal_code,insd.subscriber_fname,insd.subscriber_mname,
            insd.subscriber_lname,insd.subscriber_DOB,insd.subscriber_relationship
            FROM insurance_data insd
            INNER JOIN insurance_case insc ON(insd.ins_caseid=insc.ins_caseid)
            INNER JOIN insurance_case_types insct ON(insc.ins_case_type=insct.case_id)
            INNER JOIN insurance_companies inscm ON(inscm.id = insd.provider)
            WHERE insct.status=0
            AND insc.patient_id = ".$patient_id."
            AND insc.case_status = 'Open' 
            AND inscm.in_house_code != 'n/a'
            ORDER BY insct.normal DESC ";
        $res=imw_query($sql);
        if($res && imw_num_rows($res)>0) {
            while($ins_data=imw_fetch_assoc($res)) {
                /* Self, Spouse, Child, Other */
				$relationship = "Other";
				switch(strtolower($ins_data['subscriber_relationship']) ) {
					case 'self':
					case 'spouse':
						$relationship = $ins_data['subscriber_relationship'];
					break;
					case 'son':
					case 'daughter':
					case 'step child':
						$relationship = 'Child';
					break;
				}

                if($ins_data['normal']=='1') {
                    if($ins_data['type']=='primary') {
						if($ins_data['insurance_company']!='' && $ins_data['policy_number']!='' && $ins_data['subscriber_fname']!='' && $ins_data['subscriber_lname']!='' && $ins_data['subscriber_DOB']!='0000-00-00' && $ins_data['subscriber_DOB']!='' && $ins_data['subscriber_relationship']!='' ) {
							$data['primaryInsuranceName']=$ins_data['insurance_company'];
							$data['primaryInsuranceIdNumber']=$ins_data['policy_number'];
							$data['primaryInsuranceGroupNumber']=$ins_data['group_number'];
							$data['primaryInsuranceEmployerName']="";
							$data['primaryInsuranceAddress1']=$ins_data['subscriber_street'];
							$data['primaryInsuranceAddress2']=$ins_data['subscriber_street_2'];
							$data['primaryInsuranceCity']=$ins_data['subscriber_city'];
							$data['primaryInsuranceState']=$ins_data['subscriber_state'];
							$data['primaryInsuranceZip']=$ins_data['subscriber_postal_code'];
							$data['primaryInsuranceInsuredPersonFirstName']=$ins_data['subscriber_fname'];
							$data['primaryInsuranceInsuredPersonMiddleName']=$ins_data['subscriber_mname'];
							$data['primaryInsuranceInsuredPersonLastName']=$ins_data['subscriber_lname'];
							$pri_subscriber_DOB=$ins_data['subscriber_DOB'].'T00:00:00';
							$data['primaryInsuranceInsuredPersonBirthday']=$pri_subscriber_DOB; //"2020-03-16T23:59:54.4203886-04:00",
							$data['primaryInsuranceRelationshipToInsured']=$relationship;
						}
                    }
                    if($ins_data['type']=='secondary') {
						if($ins_data['insurance_company']!='' && $ins_data['policy_number']!='' && $ins_data['subscriber_fname']!='' && $ins_data['subscriber_lname']!='' && $ins_data['subscriber_DOB']!='0000-00-00' && $ins_data['subscriber_DOB']!='' && $ins_data['subscriber_relationship']!='' ) {
						
							$data['secondaryInsuranceName']=$ins_data['insurance_company'];
							$data['secondaryInsuranceIdNumber']=$ins_data['policy_number'];
							$data['secondaryInsuranceGroupNumber']=$ins_data['group_number'];
							$data['secondaryInsuranceEmployerName']="";
							$data['secondaryInsuranceAddress1']=$ins_data['subscriber_street'];
							$data['secondaryInsuranceAddress2']=$ins_data['subscriber_street_2'];
							$data['secondaryInsuranceCity']=$ins_data['subscriber_city'];
							$data['secondaryInsuranceState']=$ins_data['subscriber_state'];
							$data['secondaryInsuranceZip']=$ins_data['subscriber_postal_code'];
							$data['secondaryInsuranceInsuredPersonFirstName']=$ins_data['subscriber_fname'];
							$data['secondaryInsuranceInsuredPersonMiddleName']=$ins_data['subscriber_mname'];
							$data['secondaryInsuranceInsuredPersonLastName']=$ins_data['subscriber_lname'];
							$sec_subscriber_DOB=$ins_data['subscriber_DOB'].'T00:00:00';
							$data['secondaryInsuranceInsuredPersonBirthday']=$sec_subscriber_DOB; //"2020-03-16T23:59:54.4203886-04:00",
							$data['secondaryInsuranceRelationshipToInsured']=$relationship;
						}
                    }
                    if($ins_data['type']=='tertiary') {
						if($ins_data['insurance_company']!='' && $ins_data['policy_number']!='' && $ins_data['subscriber_fname']!='' && $ins_data['subscriber_lname']!='' && $ins_data['subscriber_DOB']!='0000-00-00' && $ins_data['subscriber_DOB']!='' && $ins_data['subscriber_relationship']!='' ) {
							if($data['primaryInsuranceName']!='' && $data['secondaryInsuranceName']!='') {
								$data['tertiaryInsuranceName']=$ins_data['insurance_company'];
								$data['tertiaryInsuranceIdNumber']=$ins_data['policy_number'];
								$data['tertiaryInsuranceGroupNumber']=$ins_data['group_number'];
								$data['tertiaryInsuranceEmployerName']="";
								$data['tertiaryInsuranceAddress1']=$ins_data['subscriber_street'];
								$data['tertiaryInsuranceAddress2']=$ins_data['subscriber_street_2'];
								$data['tertiaryInsuranceCity']=$ins_data['subscriber_city'];
								$data['tertiaryInsuranceState']=$ins_data['subscriber_state'];
								$data['tertiaryInsuranceZip']=$ins_data['subscriber_postal_code'];
								$data['tertiaryInsuranceInsuredPersonFirstName']=$ins_data['subscriber_fname'];
								$data['tertiaryInsuranceInsuredPersonMiddleName']=$ins_data['subscriber_mname'];
								$data['tertiaryInsuranceInsuredPersonLastName']=$ins_data['subscriber_lname'];
								$ter_subscriber_DOB=$ins_data['subscriber_DOB'].'T00:00:00';
								$data['tertiaryInsuranceInsuredPersonBirthday']=$ter_subscriber_DOB; //"2020-03-16T23:59:54.4203886-04:00",
								$data['tertiaryInsuranceRelationshipToInsured']=$relationship;
							}
						}
                    }
                }
                if($ins_data['type']=='primary' && $ins_data['vision']=='1') {
					if($ins_data['insurance_company']!='' && $ins_data['policy_number']!='' && $ins_data['subscriber_fname']!='' && $ins_data['subscriber_lname']!='' && $ins_data['subscriber_DOB']!='0000-00-00' && $ins_data['subscriber_DOB']!='' && $ins_data['subscriber_relationship']!='' ) {
						$data['visionInsuranceName']=$ins_data['insurance_company'];
						$data['visionInsuranceIdNumber']=$ins_data['policy_number'];
						$data['visionInsuranceGroupNumber']=$ins_data['group_number'];
						$data['visionInsuranceEmployerName']="";
						$data['visionInsuranceAddress1']=$ins_data['subscriber_street'];
						$data['visionInsuranceAddress2']=$ins_data['subscriber_street_2'];
						$data['visionInsuranceCity']=$ins_data['subscriber_city'];
						$data['visionInsuranceState']=$ins_data['subscriber_state'];
						$data['visionInsuranceZip']=$ins_data['subscriber_postal_code'];
						$data['visionInsuranceInsuredPersonFirstName']=$ins_data['subscriber_fname'];
						$data['visionInsuranceInsuredPersonMiddleName']=$ins_data['subscriber_mname'];
						$data['visionInsuranceInsuredPersonLastName']=$ins_data['subscriber_lname'];
						$pri_vis_subscriber_DOB=$ins_data['subscriber_DOB'].'T00:00:00';
						$data['visionInsuranceInsuredPersonBirthday']=$pri_vis_subscriber_DOB; //"2020-03-16T23:59:54.4203886-04:00",
						$data['visionInsuranceRelationshipToInsured']=$relationship;
					}
                }
                
     
            }
        }
        
        $prov_sql="select id,fname,lname,mname,user_type from users where id=".$row['providerID']." and user_type=1 and delete_status=0 ";
        $prov_res=imw_query($prov_sql);
        $pcp_arr=imw_fetch_assoc($prov_res);
        
        $pcp_name=$pcp_arr['fname'].($pcp_arr['mname']?' '.$pcp_arr['mname']:'').' '.$pcp_arr['lname'];
        $data['primaryCarePhysician']=$pcp_name;  
        $data['emergencyContact1Name']=$row['contact_relationship'];  
        $data['emergencyContact1Relationship']=$row['emergencyRelationship'];  
        $data['emergencyContact1CellphoneNumber']=$row['phone_contact'];  
        $data['communicationPreferences']=$communicationPreferences;  
        $data['practiceManagementSystemId']="";  
        $data['medicalRecordNumber']="";  
        $data['id']=$row['erp_patient_id'];  
        $data['externalId']=$patient_id;  
        
        
        return $data;
    }
   
    
    public function updateCommPreferences($patient_id=0)
    {
        if($patient_id==0) return false;
        
        $row=array();
        $sql="SELECT id,hipaa_email,email,preferr_contact,hipaa_voice,hipaa_text,phone_home,phone_biz,phone_cell,erp_pt_comm_pref_completed,erp_use_diff_method_comm FROM `patient_data` WHERE id=".$patient_id." AND erp_patient_id!='' ";
        $res=imw_query($sql);
        $row=imw_fetch_assoc($res);
		
		$home=$work=$mobile=false;
        switch($row['preferr_contact']) {
            case '0':
                $home=true;
                break;
            case '1':
                $work=true;
                break;
            case '2':
                $mobile=true;
                break;
        }
		
		/*Creating array for api/PatientCommunicationPreferences*/
		$allowEmail=($row['hipaa_email'] && $row['hipaa_email']=='1')?true:false;
		if($row['email'] && $row['email']!='') {
			$communicationsEmail=$row['email'];
		}
		$allowVoice=($row['hipaa_voice'] && $row['hipaa_voice']=='1')?true:false;
		$allowText=($row['hipaa_text'] && $row['hipaa_text']=='1')?true:false;
		$textPhone='';
		$voicePhone='';
		if($home) {
			if($row['phone_home'] && $row['phone_home']!='' ) {
				$voicePhone=$row['phone_home'];
			}
		}else if($work) {
			if($row['phone_biz'] && $row['phone_biz']!='' ) {
				$voicePhone=$row['phone_biz'];
			}
		}else if($mobile) {
			if($row['phone_cell'] && $row['phone_cell']!='' ) {
				$voicePhone=$row['phone_cell'];
			}
		}
		
		if($row['phone_cell'] && $row['phone_cell']!='' ) {
			$textPhone=$row['phone_cell'];
		}
		
		$erp_use_diff_method_comm = true;
		if($row['erp_use_diff_method_comm'] && $row['erp_use_diff_method_comm']==0) {
			$erp_use_diff_method_comm = false;
		}
		
        $commPref=array();
		$commPref['patientExternalId']=$patient_id;  
		$commPref['communicationsEmail']=$communicationsEmail;
		$commPref['allowEmailNotifications']=$allowEmail; 
		$commPref['communicationsTextPhone']=$textPhone;
		$commPref['allowSmsNotifications']=$allowText;
		$commPref['communicationsVoicePhone']=$voicePhone;  
		$commPref['allowVoiceNotifications']=$allowVoice;
		$commPref['useDifferentEmailAndPhoneForCommunications']=$erp_use_diff_method_comm;
		/*Creating array for api/PatientCommunicationPreferences Ends*/
		
		if(count($commPref)>0 ) {
			
			global $OBJRabbitmqExchange;
			$method="POST";
			$resource='PatientCommunicationPreferences';
			/*Rabbit MQ call to create Patient Communication Preferences at Portal*/
			$commPrefRes=$OBJRabbitmqExchange->send_request($commPref,$patient_id,$resource,$method);
			if($commPrefRes) {
				$commPrefRes=json_decode($commPrefRes, true);
				$useDiffEmailPhoneForComm=0;
				if($commPrefRes['useDifferentEmailAndPhoneForCommunications'] && $commPrefRes['useDifferentEmailAndPhoneForCommunications']==true){
					$useDiffEmailPhoneForComm=1;
				}
				$sql1="update patient_data set erp_pt_comm_pref_completed=1,erp_use_diff_method_comm='".$useDiffEmailPhoneForComm."' where id='".$patient_id."' ";
				imw_query($sql1);
			}
			
		}
	}
	
	
	/*Get communication preferences for individual patient*/
	public function getCommunicationPref($patient_id=0) {
		if($patient_id==0) return false;
		
		global $OBJRabbitmqExchange;
		$method="GET";
		$resource='PatientCommunicationPreferences?externalId='.$patient_id;
		$params=array();
		$messageId=$patient_id.'-'.time();
		/*Rabbit MQ call to create Patient Communication Preferences at Portal*/
		$commPref=$OBJRabbitmqExchange->send_request($params,$messageId,$resource,$method);
		
		if($commPref) {
			$commPrefArr=array();
			$commPrefArr[]=json_decode($commPref, true);
			$this->updateCommPrefForPatient($commPrefArr);
		}
	}
	
	
	/* hipaa_email,email,preferr_contact,hipaa_voice,hipaa_text,phone_home,phone_biz,phone_cell,erp_pt_comm_pref_completed,erp_use_diff_method_comm */
	public function updateCommPrefForPatient($commPrefArr=array()) {
		if(count($commPrefArr)>0) {
			foreach($commPrefArr as $commPref) {
				$patient_id=$commPref['patientExternalId'];
				$useDiffEmailPhoneForComm=0;
				if($commPref['useDifferentEmailAndPhoneForCommunications'] && $commPref['useDifferentEmailAndPhoneForCommunications']==true){
					$useDiffEmailPhoneForComm=1;
				}
				$hipaa_email=0;
				if($commPref['allowEmailNotifications'] && $commPref['allowEmailNotifications']==true){
					$hipaa_email=1;
				}
				$communicationsEmail="";
				if($commPref['communicationsEmail'] && $commPref['communicationsEmail']!=""){
					$communicationsEmail=$commPref['communicationsEmail'];
				}
				$hipaa_voice=0;
				if($commPref['allowVoiceNotifications'] && $commPref['allowVoiceNotifications']==true){
					$hipaa_voice=1;
				}
				$hipaa_text=0;
				if($commPref['allowSmsNotifications'] && $commPref['allowSmsNotifications']==true){
					$hipaa_text=1;
				}
				$communicationsVoicePhone="";
				if($commPref['communicationsVoicePhone'] && $commPref['communicationsVoicePhone']!=""){
					$communicationsVoicePhone=str_ireplace('+1','',trim($commPref['communicationsVoicePhone']));
					$communicationsVoicePhone=core_phone_unformat($communicationsVoicePhone);
				}
				$communicationsTextPhone="";
				if($commPref['communicationsTextPhone'] && $commPref['communicationsTextPhone']!=""){
					$communicationsTextPhone=str_ireplace('+1','',trim($commPref['communicationsTextPhone']));
					$communicationsTextPhone=core_phone_unformat($communicationsTextPhone);
				}
				$preferr_contact="";
				$existing_preferr_contact="";
				$sql="SELECT id,preferr_contact,phone_home,phone_biz,phone_cell FROM `patient_data` WHERE id=".$patient_id." AND erp_patient_id!='' ";
				$res=imw_query($sql);
				$row=imw_fetch_assoc($res);
				$existing_preferr_contact=$row['preferr_contact'];
				
				if($row['phone_home']=="" && $row['phone_biz']=="" && $row['phone_cell']==""){
					if($communicationsVoicePhone!="" && $communicationsTextPhone!="" && $communicationsVoicePhone!=$communicationsTextPhone){
						$preferr_contact=0;
					} else if($communicationsVoicePhone!="" && $communicationsTextPhone!="" && $communicationsVoicePhone==$communicationsTextPhone){
						$preferr_contact=2;
					} else if($communicationsVoicePhone=="" && $communicationsTextPhone!="") {
						$preferr_contact=2;
					} else if($communicationsVoicePhone!="" && $communicationsTextPhone=="") {
						$preferr_contact=$existing_preferr_contact;
					} else if($communicationsVoicePhone=="" && $communicationsTextPhone=="") {
						$preferr_contact=$existing_preferr_contact;
					}
				} else if($row['phone_home']!="" || $row['phone_biz']!="" || $row['phone_cell']!="") {
					$preferr_contact=$existing_preferr_contact;
				}

				$home=$row['phone_home'];
				$work=$row['phone_biz'];
				$mobile=$row['phone_cell'];
				switch($preferr_contact) {
					case '0':
						$home=$communicationsVoicePhone;
						if($communicationsTextPhone!=""){
							$mobile=$communicationsTextPhone;
						}
						if($hipaa_text==1 && $communicationsTextPhone=="") {
							$mobile=$communicationsTextPhone;
						}
						break;
					case '1':
						$work=$communicationsVoicePhone;
						if($communicationsTextPhone!=""){
							$mobile=$communicationsTextPhone;
						}
						if($hipaa_text==1 && $communicationsTextPhone=="") {
							$mobile=$communicationsTextPhone;
						}
						break;
					case '2':
						$mobile=$communicationsTextPhone;
						if($communicationsTextPhone=="" && $communicationsVoicePhone!="") {
							$mobile=$communicationsVoicePhone;
						}
						if($communicationsVoicePhone!="" && $communicationsTextPhone!='' && $communicationsVoicePhone==$communicationsTextPhone){
							$mobile=$communicationsTextPhone;
						}
						if($communicationsVoicePhone!="" && $communicationsTextPhone!='' && $communicationsVoicePhone!=$communicationsTextPhone){
							$home=$communicationsVoicePhone;
						}
						
						break;
				}
				
				
				$sql1=" update patient_data set 
					erp_pt_comm_pref_completed=1,
					hipaa_email='".$hipaa_email."',
					hipaa_voice='".$hipaa_voice."' ";
					$sql1.=", preferr_contact='".$preferr_contact."' ";
					$sql1.=", email='".$communicationsEmail."'  ";
					$sql1.=", phone_home='".$home."' ";
					$sql1.=", phone_biz='".$work."' ";
					$sql1.=", phone_cell='".$mobile."' ";
					$sql1.=", hipaa_text='".$hipaa_text."',
					erp_use_diff_method_comm='".$useDiffEmailPhoneForComm."'
					where id='".$patient_id."' AND erp_patient_id!='' ";
				
				imw_query($sql1);
			}
		}
	}
	

	/*Deffered code started here this may be used in future*/
	/*Get communication preferences update for all patients*/
	/*
	public function getListCommunicationUpdatePatients() {
		global $OBJRabbitmqExchange;
		$method="GET";
		$resource='communicationupdatespatients/search?alreadySent=false';
		$params=array();
		$messageId=$_SESSION["authId"].'-'.time();
		//Rabbit MQ call to create Patient Communication Preferences at Portal
		$commPref=$OBJRabbitmqExchange->send_request($params,$messageId,$resource,$method);
		
		if($commPref) {
			$commPref=json_decode($commPref, true);
			$commPrefArr=array();
			if(isset($commPref['rows']) && count($commPref['rows'])>0) {
				$commPrefArr=$commPref['rows'];
				$this->updateCommPrefForAllPatient($commPrefArr);
			}
			
		}
	}
	
	public function updateCommPrefForAllPatient($commPrefArr=array()) {
		$idsArr=array();
		if(count($commPrefArr)>0) {
			foreach($commPrefArr as $commPref) {
				$patient_id=$commPref['patientExternalID'];
				$is_valid_pt = $this->is_valid_pt($patient_id);
                if (!$is_valid_pt)
                    continue;

				$patientUpdateID=$commPref['patientUpdateID'];
				
				$hipaa_email=0;
				if($commPref['updateAllowEmailTo'] && $commPref['updateAllowEmailTo']==true){
					$hipaa_email=1;
				}
				$hipaa_voice=0;
				if($commPref['updateAllowVoiceTo'] && $commPref['updateAllowVoiceTo']==true){
					$hipaa_voice=1;
				}
				$hipaa_text=0;
				if($commPref['updateAllowSmsTo'] && $commPref['updateAllowSmsTo']==true){
					$hipaa_text=1;
				}
				
				$sql1=" update patient_data set 
					hipaa_email='".$hipaa_email."',
					hipaa_voice='".$hipaa_voice."',
					hipaa_text='".$hipaa_text."'
					where id='".$patient_id."' AND erp_patient_id!='' ";
				
				$status=imw_query($sql1);
				
				if($status && $patientUpdateID!='') {
					$idsArr[]=trim($patientUpdateID);
				}
			}
		}

		if(count($idsArr)>0) {
			//$this->communicationUpdatesSent($idsArr);
		}
		
	}
	
	
	public function communicationUpdatesSent($idsArr=array()){
		if(count($idsArr)>0) {
			$idsChunkArr=array_chunk($idsArr,5);
			global $OBJRabbitmqExchange;
			foreach($idsChunkArr as $chunkArr) {
				
				foreach($chunkArr as $patientUpdateID) {
					$data=array();
					$data['InternalID']=$patientUpdateID;
					$data['Success']=true;
					$data['ResultMessage']='Success';
					
					$messageId=$patientUpdateID.'-'.time();
					$method="POST";
					$resource='CommunicationUpdatesSent';
					//Rabbit MQ call to send acknowledege regarding received Communication Preferences at Portal
					$result=$OBJRabbitmqExchange->send_request($data,$messageId,$resource,$method);
	
				}
				unset($chunkArr);
			}
		}
	}
	
	
	public function is_valid_pt($id='') {
        $ret = 0;
        $id = trim($id);
        if (!empty($id)) {
            $sql = "Select id from patient_data where id=$id";
            $rs = imw_query($sql);
            if ($rs && imw_num_rows($rs) == 1) {
                $ret = 1;
            }
        }
        return $ret;
    }
	
	/*Deffered code ends here this may be used in future*/
}

