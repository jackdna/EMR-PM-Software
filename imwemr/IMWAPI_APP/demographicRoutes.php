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
$this->respond(array('POST','GET'), '*', function($request, $response, $service, $app) use(&$patientId) {
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
	
	/* Demographics Data */
		/*  Patient & Gurantor Data */
		$pt_demo_arr = $guarantor_arr = $family_data_arr = $insurance_arr = array();
		$demo_query = "
			SELECT 
				pd.id as PatientID,
				pd.title as Title, 
				pd.fname as FirstName, 
				pd.lname as LastName, 
				pd.mname as MiddleName,
				pd.mname_br as BirthName,
				pd.suffix AS Suffix,
				DATE_FORMAT(pd.`DOB`, '%m-%d-%Y') as DateOfBirth,
				pd.sex as Sex,
				pd.race as Race,
				pd.ethnicity as Ethnicity,
				pd.language as Language,
				pd.status as MaritalStatus, 
				pd.providerID as ProviderID,
				pd.primary_care_phy_name as PCP,
				pd.primary_care_phy_id as PrimaryCarePhysicianId,
				pd.phone_home as HomePhone, 
				pd.phone_biz as WorkPhone,
				pd.phone_cell as MobilePhone, 
				pd.email as Email, 
				pd.emergencyRelationship as EmerRelation,
				pd.contact_relationship as ContactName, 
				pd.phone_contact as PhoneNumber,
				pd.street as Street1, 
				pd.street2 as Street2,
				pd.city as City, 
				pd.state as State,
				pd.postal_code as Zip,
				pd.zip_ext aS Zip_Ext,
				pd.country_code as Country,
				
				rp.title as Resp_Title, 
				rp.fname as Resp_FirstName, 
				rp.mname as Resp_MiddleName, 
				rp.lname as Resp_LastName,
				rp.relation as Resp_Relationship,
				rp.address as Resp_Street1,
				rp.address2 as Resp_Street2,
				rp.city as Resp_City,
				rp.state as Resp_State,
				rp.zip as Resp_Zip,
				rp.zip_ext as Resp_ext,
				rp.home_ph as Resp_HomePhone,
				rp.work_ph as Resp_WorkPhone,
				rp.mobile as Resp_MobilePhone,
				rp.email as Resp_email,
				rp.marital as Resp_marital,
				rp.suffix as Resp_suffix,
				rp.hippa_release_status as Resp_hippa
				
				
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
				home_phone as HomePhone,
				work_phone as WorkPhone,
				mobile_phone as MobilePhone,
				email_id as Email,
				suffix As Suffix,
				hippa_release_status as Hippa_Status,
				zip_ext as Zip_Ext
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
				ins_case.ins_caseid as ins_case_id,
				ins_case.ins_case_name as ins_case_name,
				ins_cs_type.case_name as ins_case_type,
				ins_data.type as ins_type,
				ins_data.policy_number as policy_number,
				ins_comp.name as ins_provider,
				DATE_FORMAT(ins_data.effective_date, '%m-%d-%Y') as effect_date,
				ins_data.copay as Copay,
				ins_data.group_number as group_no,
				ins_data.subscriber_relationship as relation
			FROM insurance_case ins_case
				LEFT JOIN insurance_case_types ins_cs_type ON (ins_case.ins_case_type = ins_cs_type.case_id)
				LEFT JOIN insurance_data ins_data ON (ins_case.ins_caseid = ins_data.ins_caseid)
				LEFT JOIN insurance_companies ins_comp ON (ins_data.provider = ins_comp.id)
			WHERE ins_case.patient_id = ".$patientId." 
				AND ins_data.actInsComp = 1
				AND ins_case.case_status = 'Open'
				AND ins_case.del_status = 0
				AND ins_data.type != 'tertiary'
				AND ins_cs_type.case_id IN(1,5)";	
			$resp = $app->dbh->imw_query($pt_insurance_qry);
			if($resp && $app->dbh->imw_num_rows($resp) > 0){
				while($row = $app->dbh->imw_fetch_assoc($resp)){
					$tmp_arr = $type_arr = array();
					
					$tmp_arr['Case_Type'] = ucfirst($row['ins_type']).' Insurance - '.$row['ins_case_type'].' Section ';
					$tmp_arr['Type'] = $row['ins_type'];
					$tmp_arr['Ins_Provider'] = $row['ins_provider'];
					$tmp_arr['Relation'] = $row['relation'];
					$tmp_arr['CaseId'] = $row['ins_case_id'];
					$tmp_arr['CaseName'] = $row['ins_case_name'];
					$tmp_arr['PolicyNumber'] = $row['policy_number'];
					$tmp_arr['Group'] = $row['group_no'];
					$tmp_arr['Activation'] = $row['effect_date'];
					$tmp_arr['Copay'] = $row['Copay'];
					$insurance_arr[] = $tmp_arr;
				}
				$app->dbh->imw_free_result($resp);
				unset($resp);
			}	
			
		/* Merging all array into 1 main array */
		$main_demo_arr = $tmp_arr = array();
		
		$tmp_arr['Demographic'] = $pt_demo_arr ;			//Patient Demographics
		$tmp_arr['Guarantor'] =  $guarantor_arr ;		//Patient responsible party / guarantor 
		$tmp_arr['Family'] =  $family_data_arr ;		//Patient Family 
		$tmp_arr['Insurance'] = $insurance_arr ;		//Patient Insurance 
		$main_demo_arr = $tmp_arr;						//Merged Array
		
		/* Setting array to be accessable globally */	
		$service->__set('MainResponseContainer',$main_demo_arr);
		$service->__set('Demographic',$pt_demo_arr);
		$service->__set('Guarantor',$guarantor_arr);
		$service->__set('Family',$family_data_arr);
		$service->__set('Insurance',$insurance_arr);
	
});

/*Hack to Accept blank  subCategory*/
$this->respond(array('POST','GET'), '', function(){});

/*Return Only Demographics*/
$this->respond(array('POST','GET'), '/demographicsOnly', function($request, $response, $service) {
	$pt_resp_arr['Demographic'] = (count($service->__get('Demographic')) > 0) ? $service->__get('Demographic') : 'No record';
	$service->__set('MainResponseContainer',$pt_resp_arr);
});

/*Return Only Insurance*/
$this->respond(array('POST','GET'), '/insurance', function($request, $response, $service) {
	$pt_resp_arr['Insurance'] =  (count($service->__get('Insurance')) > 0) ? $service->__get('Insurance') : 'No record';
	$service->__set('MainResponseContainer',$pt_resp_arr);
});

/*Return Only Guarantor*/
$this->respond(array('POST','GET'), '/guarantor', function($request, $response, $service)  {
	$pt_resp_arr['Guarantor'] =  (count($service->__get('Guarantor')) > 0) ? $service->__get('Guarantor') : 'No record';
	$service->__set('MainResponseContainer',$pt_resp_arr);
});

/*Return Only Family*/
$this->respond(array('POST','GET'), '/family', function($request, $response, $service) {
	$fam_arr['Family'] = (count($service->__get('Family')) > 0) ? $service->__get('Family') : 'No record';
	$service->__set('MainResponseContainer',$fam_arr);
});

	
$this->respond(function($request, $response, $service) use(&$patientId) {
	$main_arr = $service->__get('MainResponseContainer');
	//array_walk_recursive($main_arr, $converToString);
	
	return json_encode($main_arr);
});