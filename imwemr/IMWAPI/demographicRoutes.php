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
 * Purpose: Routes for Demographic API calls.
*/

$patientId = 0;

/*Validate Patient ID*/
$this->respond('*', function($request, $response, $service, $app) use(&$patientId) {
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
	    $response->append('Invalid Patient ID. ');
	    $this->abort(400);
	}
	
	
	/* Demographics Data */
	    /*  Patient & Gurantor Data */
	    $pt_demo_arr = $guarantor_arr = $family_data_arr = $insurance_arr = $employer_arr = array();
	    $demo_query = "
		    SELECT 
			pd.id as PatientID,
			pd.External_MRN_4 AS ExternalID,
			pd.title as Title, 
			pd.fname as FirstName, 
			pd.lname as LastName, 
			pd.mname as MiddleName,
			pd.mname_br as BirthName,
			pd.suffix AS Suffix,
			pd.DOB as DateOfBirth, 
			pd.sex as Sex,
			pd.race as Race,
			pd.ethnicity as Ethnicity,
			pd.language as Language,
			pd.status as MaritalStatus, 
			pd.providerID as ProviderID, 
			pd.primary_care_phy_id as PrimaryCarePhysicianId,
			pd.phone_home as HomePhone, 
			pd.phone_biz as WorkPhone,
			pd.phone_cell as MobilePhone,
			(
				CASE 
					WHEN pd.preferr_contact = 0 THEN 'HomePhone'
					WHEN pd.preferr_contact = 1 THEN 'WorkPhone'
					WHEN pd.preferr_contact = 2 THEN 'MobilePhone'
				END
			) AS PreferredContact,
			pd.email as Email, 
			pd.contact_relationship as ContactName,
			pd.street as Street1, 
			pd.street2 as Street2,
			pd.city as City, 
			pd.state as State,
			pd.postal_code as Zip,
			pd.zip_ext as ZipExtention,
			pd.country_code as Country,
			pd.contact_relationship as EmergencyContactName,
			pd.emergencyRelationship as EmergencyContactRelationship,
			pd.phone_contact as PhoneNumber,

			rp.id as Resp_Id,
			rp.title as Resp_Title, 
			rp.fname as Resp_FirstName, 
			rp.mname as Resp_MiddleName, 
			rp.lname as Resp_LastName,
			rp.suffix as Resp_Suffix,
			rp.relation as Resp_Relationship,
			rp.marital as Resp_MaritalStatus,
			(
				CASE 
					WHEN rp.hippa_release_status = 1 THEN 'Yes'
					WHEN rp.hippa_release_status = 0 THEN 'No'
				END
			) AS Resp_HippaRelease,
			rp.address as Resp_Street1,
			rp.address2 as Resp_Street2,
			rp.city as Resp_City,
			rp.state as Resp_State,
			rp.zip as Resp_Zip,
			rp.zip_ext as Resp_ZipExt,
			rp.home_ph as Resp_HomePhone,
			rp.work_ph as Resp_WorkPhone,
			rp.mobile as Resp_MobilePhone,
			rp.email as Resp_Email

		    FROM patient_data pd
		    LEFT JOIN resp_party rp ON rp.patient_id = pd.id 
		    WHERE pd.id = ".$patientId."";

		    $resp  = $app->dbh->imw_query($demo_query);
		    if($resp && $app->dbh->imw_num_rows($resp) > 0){
			    while($row = $app->dbh->imw_fetch_assoc($resp)){
				    foreach($row as $key => $val){
					    if(strpos($key, 'Resp_') !== false){
						    if($val !== null){
							    $key = str_replace('Resp_','',$key);
							    $guarantor_arr[$key] = $val;
						    }
					    }else{
						    if($key == 'PrimaryCarePhysicianId'){
							    $val = ($val == 0 || empty($val)) ? '' : $val; 
						    }
							if($key == 'MaritalStatus') $val = ucwords($val);
						    if($val !== null){
							    $pt_demo_arr[$key] = $val;
						    }
					    }
				    }
			    }
			    $app->dbh->imw_free_result($resp);
			    unset($resp);
		    }

	    /* Patient multi address */
	    $pt_add_qry = "
		    SELECT 
			id as Id,
			street as Street1,
			street2 as Street2,
			city as City,
			state as State,
			postal_code as Zip
		    FROM patient_multi_address 
		    WHERE patient_id = ".$patientId." AND del_status = 0";
		    $resp = $app->dbh->imw_query($pt_add_qry);
		    if($resp && $app->dbh->imw_num_rows($resp) > 0){
			    while($row = $app->dbh->imw_fetch_assoc($resp)){
				    if(count($pt_demo_arr) > 0){
					    $pt_demo_arr['Address'][] = $row;
				    }
			    }

			    $app->dbh->imw_free_result($resp);
			    unset($resp);
		    }

	    /* Patient Family Data */	
	    $pt_fam_qry = "
		    SELECT
			id as Id,
			title as Title, 
			fname as FirstName, 
			mname as MiddleName, 
			lname as LastName,
			if( LOWER(patient_relation) = 'other', name_of_other_relation, patient_relation) as Relationship,
			street1 as Street1,
			street2 as Street2,
			city as City,
			state as State,
			postal_code as Zip,
			zip_ext as ZipExt,
			email_id as Email,
			home_phone as HomePhone,
			work_phone as WorkPhone,
			mobile_phone as MobilePhone,
			(
				CASE 
					WHEN hippa_release_status = 1 THEN 'Yes'
					WHEN hippa_release_status = 0 THEN 'No'
				END
			) AS HippaRelease
		    FROM patient_family_info	
		    WHERE patient_id = ".$patientId."";
		    $resp = $app->dbh->imw_query($pt_fam_qry);
		    if($resp && $app->dbh->imw_num_rows($resp) > 0){
			    while($row = $app->dbh->imw_fetch_assoc($resp)){
				    $family_data_arr[] = $row;
			    }
			    $app->dbh->imw_free_result($resp);
			    unset($resp);
		    }

	    /* Patient Insurance Data */	
	    $pt_insurance_qry = "
		    SELECT 
			ins_data.id as ins_id,
			ins_case.ins_caseid as ins_case_id,
			ins_cs_type.case_name as ins_case_name,
			ins_data.type as ins_type,
			ins_data.policy_number as policy_number,
			DATE(ins_data.effective_date) as effective_date,
			ins_data.provider as provider,
			ins_comp.name as ins_provider,
			ins_data.copay as copay,
			ins_data.group_number as 'group',
			ins_data.subscriber_relationship as relation,
			(
				CASE 
					WHEN ins_cs_type.normal = 1 THEN 'Medical'
					WHEN ins_cs_type.vision = 1 THEN 'Vision'
				END
			) AS case_type
			FROM insurance_case ins_case
			    LEFT JOIN insurance_case_types ins_cs_type ON (ins_case.ins_case_type = ins_cs_type.case_id) AND (ins_cs_type.normal = 1 || ins_cs_type.vision = 1)
			    LEFT JOIN insurance_data ins_data ON (ins_case.ins_caseid = ins_data.ins_caseid)
			    LEFT JOIN insurance_companies ins_comp ON (ins_data.provider = ins_comp.id)
		    WHERE ins_case.patient_id = ".$patientId." 
			    AND ins_data.actInsComp = 1
			    AND ins_case.case_status = 'Open'
			    AND ins_case.del_status = 0";	
		    $resp = $app->dbh->imw_query($pt_insurance_qry);
		    if($resp && $app->dbh->imw_num_rows($resp) > 0){
			    while($row = $app->dbh->imw_fetch_assoc($resp)){
				    $tmp_arr = $type_arr = array();
				    $tmp_arr['Id'] = $row['ins_id'];
				    $tmp_arr['Type'] = $row['ins_type'];
				    //$tmp_arr['Name'] = $row['ins_provider'];
				    $tmp_arr['ProviderId'] = $row['provider'];
				    $tmp_arr['CaseId'] = $row['ins_case_id'];
				    $tmp_arr['CaseName'] = $row['ins_case_name'];
				    $tmp_arr['CaseType'] = $row['case_type'];
				    $tmp_arr['PolicyNumber'] = $row['policy_number'];
				    $tmp_arr['Copay'] = $row['copay'];
				    $tmp_arr['GroupNumber'] = $row['group'];
				    $tmp_arr['Relationship'] = $row['relation'];
				    $tmp_arr['ActivationDate'] = $row['effective_date'];
				    $insurance_arr[] = $tmp_arr;
			    }
			    $app->dbh->imw_free_result($resp);
			    unset($resp);
		    }	
			
		/* Employer Data */
		$pt_employer_dt = "
			SELECT 
				id as ID,
				pid as PatientID,
				name as Name,
				street as Street1,
				street2 as Street2,
				postal_code as Zip,
				city as City,
				state as State
			FROM 
				employer_data 
			WHERE 
				pid = '".$patientId."' and 
				name != ''
		";
		$resp = $app->dbh->imw_query($pt_employer_dt);
		if($resp && $app->dbh->imw_num_rows($resp) > 0){
			while($row = $app->dbh->imw_fetch_assoc($resp)){
				$employer_arr = $row;
			}
			$app->dbh->imw_free_result($resp);
			unset($resp);
		}

	    /* Merging all array into 1 main array */
	    $main_demo_arr = $tmp_arr = array();

	    $tmp_arr['Demographic'] = (count($pt_demo_arr) > 0) ? $pt_demo_arr : 'No record.';			//Patient Demographics
	    $tmp_arr['Guarantor'] = (count($guarantor_arr) > 0) ? $guarantor_arr : 'No record.';		//Patient responsible party / guarantor 
	    $tmp_arr['Family'] = (count($family_data_arr) > 0) ? $family_data_arr : 'No record.';		//Patient Family 
	    $tmp_arr['Insurance'] = (count($insurance_arr) > 0) ? $insurance_arr : 'No record.';		//Patient Insurance 
	    $tmp_arr['Employer'] = (count($employer_arr) > 0) ? $employer_arr : 'No record.';			//Patient Employer 
	    $main_demo_arr = $tmp_arr;						//Merged Array

	    /* Setting array to be accessable globally */	
	    $service->__set('MainResponseContainer',$main_demo_arr);
	    $service->__set('Demographic',$pt_demo_arr);
	    $service->__set('Guarantor',$guarantor_arr);
	    $service->__set('Family',$family_data_arr);
	    $service->__set('Insurance',$insurance_arr);
	    $service->__set('Employer',$employer_arr);
	
});

/*Hack to Accept blank  subCategory*/
$this->respond('GET', '', function(){});

/*Return Only Demographics*/
$this->get('/demographicsOnly', function($request, $response, $service) {
    $pt_resp_arr['Demographic'] = (count($service->__get('Demographic')) > 0) ? $service->__get('Demographic') : 'No record';
    $service->__set('MainResponseContainer',$pt_resp_arr);
});

/*Return Only Insurance*/
$this->get('/insurance', function($request, $response, $service) {
    $pt_resp_arr['Insurance'] =  (count($service->__get('Insurance')) > 0) ? $service->__get('Insurance') : 'No record';
    $service->__set('MainResponseContainer',$pt_resp_arr);
});

/*Return Only Guarantor*/
$this->get('/guarantor', function($request, $response, $service)  {
    $pt_resp_arr['Guarantor'] =  (count($service->__get('Guarantor')) > 0) ? $service->__get('Guarantor') : 'No record';
    $service->__set('MainResponseContainer',$pt_resp_arr);
});

/*Return Only Family*/
$this->get('/family', function($request, $response, $service) {
    $fam_arr['Family'] = (count($service->__get('Family')) > 0) ? $service->__get('Family') : 'No record';
    $service->__set('MainResponseContainer',$fam_arr);
});

/*Return Only employer data*/
$this->get('/employer', function($request, $response, $service) {
    $fam_arr['Employer'] = (count($service->__get('Employer')) > 0) ? $service->__get('Employer') : 'No record';
    $service->__set('MainResponseContainer',$fam_arr);
});

/*Update Demographics. iPOrtal*/
$this->post('/demographicsOnly', function($request, $response, $service, $app) {
    if($request->__isset('PrimaryCarePhysicianId') && $request->__get('PrimaryCarePhysicianId') != '')
		$service->validateParam('PrimaryCarePhysicianId', 'Please provide valid Care Physician ID.')->isInt()->notNull()->isRefPhysician($this);
    
	try
    {
		/*Parameters to be saved for this API Call*/
		$parameters = $app->saveParameters->demographics;
		$this->saveField($parameters);
    }
    catch (Exception $e)
    {
		$response->append($e->getMessage());
		$this->abort(503);
    }
    
    $service->__set('MainResponseContainer', 'Request Saved. Data is pending for approval.');
});


/*Update Guarantor/Rep. party. iPOrtal*/
$this->post('/guarantor', function($request, $response, $service, $app) {
    $request->__set('newAdd', 0);
	
	if($request->__isset('GuarantorId') && trim($request->__get('GuarantorId')) != ''){
		$service->validateParam('GuarantorId', 'Please provide valid Guarantor ID.')->isInt()->notNull()->isGuarantor($this);
	}else{
		//Check if a guarantor already exists abort the call
		$pt_resp_arr = (count($service->__get('Guarantor')) > 0) ? $service->__get('Guarantor') : '';		
		if(empty($pt_resp_arr) == false){
			$response->append('Guarantor already exists for the provided patient id. Unable to add new guarantor.');
			$this->abort(406);
		}
		$request->__set('newAdd', 1);
	}		
	
	try
    {
		/*Parameters to be saved for this API Call*/
		$parameters = $app->saveParameters->gurantor;
		
		if($request->__isset('newAdd') && trim($request->__get('newAdd')) == 1) $this->addField( $parameters );
		else $this->saveField( $parameters );
    }
    catch (Exception $e)
    {
		$response->append($e->getMessage());
		$this->abort(503);
    }
    
    $service->__set('MainResponseContainer', 'Request Saved. Data is pending for approval.');
});

/*Update Family members iPOrtal*/
$this->post('/family', function($request, $response, $service, $app) {
	$request->__set('newAdd', 0);
	if($request->__isset('MemberId') && trim($request->__get('MemberId')) != '')
		$service->validateParam('MemberId', 'Please provide valid Member ID.')->isInt()->notNull()->isFamily($this);
	else
		$request->__set('newAdd', 1);	
	
	try
    {
		/*Parameters to be saved for this API Call*/
		$parameters = $app->saveParameters->family;
		
		if($request->__isset('newAdd') && trim($request->__get('newAdd')) == 1) $this->addField( $parameters );
		else $this->saveField( $parameters );
    }
    catch (Exception $e)
    {
		$response->append($e->getMessage());
		$this->abort(503);
    }
    
    $service->__set('MainResponseContainer', 'Request Saved. Data is pending for approval.');
});

/*Update Patient Insurance iPOrtal*/
$this->post('/insurance', function($request, $response, $service, $app) {
    $ptTmpInsArr = $ptInsArr = array();
	$caseArr = array(0 => 'medical', 1 => 'vision');
	$insType = array(0 => 'primary', 1 => 'secondary');
	$request->__set('newAdd', 0);
	
	$service->validateParam('InsProvider', 'Please provide valid Insurance Company ID.')->isInt()->notNull()->isInsuranceCompany($this);
	
	$service->validateParam('InsuranceType', 'Please choose a insurance type.')->isInt()->notNull();
	$service->validateParam('CaseType', 'Please choose a case type.')->isInt()->notNull();
	
	if($request->__isset('ActivationDate') && trim($request->__get('ActivationDate') != '')){
		$service->validateParam('ActivationDate', 'Please provide valid Activation Date.')->isDate();		
	}
	
	if($request->__isset('PatientInsId') && trim($request->__get('PatientInsId')) != ''){
		$service->validateParam('PatientInsId', 'Please provide valid Insurance ID.')->isInt()->notNull()->isInsurance($this);
	}else{
		//Check if a insurance already exists abort the call
		$ptTmpInsArr = (count($service->__get('Insurance')) > 0) ? $service->__get('Insurance') : '';		
		if(empty($ptTmpInsArr) == false && count($ptTmpInsArr) > 0){
			
			foreach($ptTmpInsArr as $obj){
				$type = (isset($obj['Type']) && empty($obj['Type']) == false) ? $obj['Type'] : '';
				$caseType = (isset($obj['CaseType']) && empty($obj['CaseType']) == false) ? $obj['CaseType'] : '';
				
				if(empty($type) == false && empty($caseType) == false) $ptInsArr[strtolower($caseType)][strtolower($type)] = $obj;
			}
		}
		
		if(isset($ptInsArr[$caseArr[$request->__get('CaseType')]][$insType[$request->__get('InsuranceType')]]) && count($ptInsArr[$caseArr[$request->__get('CaseType')]][$insType[$request->__get('InsuranceType')]]) > 0){
			$response->append('Selected insurance for selected case already exists for the provided patient id. Unable to add new insurance.');
			$this->abort(406);
		}
		$request->__set('newAdd', 1);
	}
	
	try
    {
		/*Parameters to be saved for this API Call*/
		$parameters = $app->saveParameters->insurance;
		
		if($request->__isset('newAdd') && trim($request->__get('newAdd')) == 1){
			
			$request->__set('actInsComp', 1);
			
			$case = ($request->__get('CaseType') != '' && $request->__isset('CaseType')) ? $request->__get('CaseType') : '';
			$caseType = (empty($case) == false && ($case == 1)) ? 'vision' : 'normal';
			
			$sel = "select case_id from insurance_case_types where ".$caseType." = '1' limit 0,1";
			$chkCase = $this->app->dbh->imw_query("select ins_caseid from insurance_case where patient_id='".$request->__get('patientId')."' and ins_case_type=($sel) and case_status='open' and del_status='0' limit 0,1");
			
			$insuranceCaseId = '';
			if($chkCase && $this->app->dbh->imw_num_rows($chkCase) > 0){
				$rowFetch = $this->app->dbh->imw_fetch_assoc($chkCase);
				$insuranceCaseId = $rowFetch['ins_caseid'];
			}else{
				//Insert new type in DB
				$ins_case_qry = $this->app->dbh->imw_query("INSERT INTO insurance_case SET ins_case_type=($sel), patient_id='".$request->__get('patientId')."', start_date=NOW(),case_status='Open',timestamp=NOW(); ");
				
				if($ins_case_qry) $insuranceCaseId = $this->app->dbh->imw_insert_id();
			}
			
			if(empty($insuranceCaseId) == false) $request->__set('CaseId', $insuranceCaseId);
			
			$this->addField( $parameters );
		}else{
			$this->saveField( $parameters );
		}	
		
    }
    catch (Exception $e)
    {
		$response->append($e->getMessage());
		$this->abort(503);
    }
    
    $service->__set('MainResponseContainer', 'Request Saved. Data is pending for approval.');
});


	
$this->respond(function($request, $response, $service) use(&$patientId) {
    $main_arr = $service->__get('MainResponseContainer');
    return json_encode($main_arr);
});