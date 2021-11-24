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
include_once("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/medications.class.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
include_once($GLOBALS['srcdir']."/classes/class.cls_review_med_hx.php");
$medication = new Medications($medical->current_tab);
$OBJReviewMedHx = new CLSReviewMedHx;

/** 
 * Parameters Sanitization to prevent arbitrary values - Security Fixes
 **/
$_REQUEST['prv_frmid'] = xss_rem($_REQUEST['prv_frmid'], 3);
$erp_error=array();
$arr_info_alert = array();
if(isset($_REQUEST["info_alert"]) && count($_REQUEST["info_alert"]) > 0){
	$arr_info_alert = unserialize(urldecode($_REQUEST["info_alert"]));
}

//--- CHECK IF MEDICATION VALUE CHANGED ----
if(trim($change_data) != '')
{
	$med_id_str = $_REQUEST["hidMedIdVizChange"];	
	$med_id_str = substr(trim($med_id_str), 0, -1);  		
	$arrMedIdVizChange = array();
	$arrMedIdVizChange = explode(",", $med_id_str);
	$query = "select * from lists where id in (".$med_id_str.") ";
	$sql = imw_query($query);
	$medDataArr = array();
	$arrLists_prv_charts = array();
	
	while($row = imw_fetch_assoc($sql) ) 
	{
		$id = $row['id'];
		if($row['begdate'] == '0000-00-00'){
			$row['begdate'] = '';
		}
		if($row['enddate'] == '0000-00-00'){
			$row['enddate'] = '';
		}
		if($row['allergy_status'] == '0'){
			$row['allergy_status'] = '';
		}
		$medDataArr[$id] = $row;
	}
	
	//--- SAVE IN DATA BASE ---
	for($i=0;$i<=$last_cnt;$i++)
	{

		/** 
		 * Parameters Sanitization to prevent arbitrary values - Security Fixes
		 **/

		/** Allow numeric only */
		$_POST['med_id'.$i] = xss_rem($_POST['med_id'.$i], 3);

		$mid = $_POST['med_id'.$i];
		$med_data_arr = array();
		$type = ($_POST['md_occular'.$i] > 0) ? 4 : 1;
		//$med_data_arr['type'] = $type;
		$med_type = ($_POST['ocular_med_chkbox_'.$i] == 1 ) ? 4 : 1;
		$med_data_arr['type'] =$med_type;
		$med_data_arr['title'] = trim($_POST['md_medication'.$i]);
		$med_data_arr['destination'] = trim($_POST['md_dosage'.$i]);
		$med_data_arr['sig'] = trim($_POST['md_sig'.$i]);
		$med_data_arr['qty'] = trim($_POST['md_qty'.$i]);
		$med_data_arr['refills'] = trim($_POST['md_refills'.$i]);
		$med_data_arr['referredby'] = trim($_POST['md_prescribedby'.$i]);
		$med_data_arr['begdate'] = getDateFormatDB($_POST['md_begindate'.$i]);
		$med_data_arr['begtime'] = $_POST['md_begtime'.$i];		
		
		if($_POST['md_enddate'.$i]=='' && ($_POST['cbMedicationStatus'.$i]=='Stop' || $_POST['cbMedicationStatus'.$i]=='Discontinue')){
			//$_POST['md_enddate'.$i] = date('m-d-Y');
		}
		$med_data_arr['enddate'] = getDateFormatDB($_POST['md_enddate'.$i]);
		$med_data_arr['endtime'] = $_POST['md_endtime'.$i];
        
		$med_data_arr['allergy_status'] = trim($_POST['cbMedicationStatus'.$i]) == '' ? 'Active' : trim($_POST['cbMedicationStatus'.$i]);
		$med_data_arr['med_comments'] = addslashes(trim($_POST['med_comments'.$i]));
		$med_data_arr['pid'] = $_SESSION['patient'];
		$med_data_arr['sites'] = ($_POST['md_occular'.$i]!='') ? $_POST['md_occular'.$i] : 0;
		$med_data_arr['med_route'] = ($type == 1) ? $_POST['md_route'.$i] : '';
		$med_data_arr['compliant'] = ($_POST['compliant'.$i]!='') ? $_POST['compliant'.$i] : 2;
		$med_data_arr['ccda_code'] = trim($_POST['ccda_code'.$i]);
		$med_data_arr['ccda_code_system'] = "2.16.840.1.113883.6.88";
		$med_data_arr['ccda_code_system_name'] = "RxNorm";
		$med_data_arr['fdb_id'] = trim($_POST['fdb_id'.$i]);
		$med_data_arr['user'] = $_SESSION['authId'];
		//last taken time
		$med_data_arr['last_take_time'] = format_date($_POST['md_lasttakendate'.$i],0,3,'insert');	
		$med_data_arr['refusal'] = $_POST['refusal'.$i];
		$med_data_arr['refusal_reason'] = $_POST['refusal_reason'.$i];
		$med_data_arr['refusal_snomed'] = $_POST['refusal_snomed'.$i];
        if($_POST['refusal_snomed'.$i] == '') {
            $med_data_arr['refusal'] =0;
            $med_data_arr['refusal_reason'] = '';
            $med_data_arr['refusal_snomed'] = '';
        }

        /*TW Integration*/
        if( is_allscripts() )
        {
        	$med_data_arr['as_ddi'] = $_POST['med_tw_ddi'.$i];
        }
        $med_data_arr['service_eligibility']=0;
        if(isDssEnable() && $_POST['service_eligibility'.$i]){
            $med_data_arr['service_eligibility'] = $_POST['service_eligibility'.$i];
        }
		
		//if Prev Chart Meds Save
		if(!empty($_REQUEST["prv_frmid"])){
			$med_data_arr['title'] = trim($med_data_arr['title']);
			if(!empty($med_data_arr['title'])){
				if(empty($mid) === true){$med_data_arr['date'] = date('Y-m-d H:i:s');}
				$med_data_arr['id'] = $mid;
				$arrLists_prv_charts[] = $med_data_arr;
			}	
		}else{	
		//--- GET SAVED MEDICATION DATA ----
		$med_data_exists_arr = $medDataArr[$mid];
		
		//--- COMPARE OLD AND NEW VALUES FROM ARRAY ----
		$checkCompareArr = check_two_array($med_data_arr,$med_data_exists_arr);
		
		if($checkCompareArr == true and $med_data_arr['title'] != ''){
			if(empty($mid) === true){
				$md_action = 'add';
				$med_data_arr['date'] = date('Y-m-d H:i:s');
				$mid = AddRecords($med_data_arr,'lists');

				/*Add Records to Tw*/
				if( is_allscripts() && $_SESSION['as_id']!=='' && $med_data_arr['as_ddi'] != '' && $mid)
				{
					$GLOBALS['rethrow'] = true;
					include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );
					try{
						$patientObj = new as_patient();
						$response = $patientObj->addMedication($med_data_arr['as_ddi'], $med_data_arr['sig']);

						if( isset($response->transid) )
						{
							$sqlTW = "UPDATE `lists` SET `as_id`='".$response->transid."' WHERE `id`='".$mid."'";
							imw_query($sqlTW);
						}
					}
					catch(asException $e)
					{

					}
				}
				/*End Add Records to Tw*/
			}
			else{
				$md_action = 'update';
				if(in_array($mid, $arrMedIdVizChange) == true){
					$mid = UpdateRecords($mid,'id',$med_data_arr,'lists');	
				}

				/*Update medications to TW*/
				$sqlTW = "SELECT `as_id` FROM `lists` WHERE `id`='".$mid."' AND TRIM(`as_id`) != '' ";
				$respTW = imw_query($sqlTW);
				if( $respTW && imw_num_rows($respTW) == 0 )
				{
					if( is_allscripts() && $_SESSION['as_id']!=='' && $med_data_arr['as_ddi'] != '' && $mid)
					{
						$GLOBALS['rethrow'] = true;
						include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );
						try{
							$patientObj = new as_patient();
							$response = $patientObj->addMedication($med_data_arr['as_ddi'], $med_data_arr['sig']);

							if( isset($response->transid) )
							{
								$sqlTW = "UPDATE `lists` SET `as_id`='".$response->transid."' WHERE `id`='".$mid."'";
								imw_query($sqlTW);
							}
						}
						catch(asException $e)
						{

						}
					}
				}
				/*End Update medications to TW*/
			}
			
			/* ERP PORTAL Medication Records */
			if(isERPPortalEnabled()) {
				try {

					// firstly check which option is enabled on iportal setting 
					// based on these setting allow only those entries to be pushed on portal
					$allowMedicationErpApiCall = false;
					$portalDefMedication = null;
					$cred_sql = "SELECT * FROM erp_api_credentials limit 1";
	                $cred_row=sqlQuery($cred_sql);
	                if($cred_row!=false){
	                	$portalDefMedication = $cred_row["portal_def_medication"];
	                	if ($portalDefMedication == 0) {
	                		$allowMedicationErpApiCall = true;
	                	}
	                	else if($portalDefMedication == $type){
	                		$allowMedicationErpApiCall = true;
	                	}
	                }

	                // only allowMedicationErpApiCall will be pushed
	                if ($allowMedicationErpApiCall) {
					
					    //For ERP Patient Portal API
					    include_once($GLOBALS['srcdir']."/erp_portal/patient_medications.php");
					    $obj_patients = new patient_medications();
						if($med_data_arr['allergy_status']=='Stop'){
							$obj_patients->deleteMedicationRecords($mid);
						}
						else{
							$medicationRouteExternalId = '';
						    $medicationRouteName = '';
						    $medicationId = '';

						    // find External medicationRouteExternalId
						    if (isset($med_data_arr['med_route']) && $med_data_arr['med_route'] !='') {
						    	$route_sql = "SELECT * FROM route_codes WHERE route_name='".$med_data_arr['med_route']."' limit 1";
			                	$route_row=sqlQuery($route_sql);
			                	if($route_row!=false){
			                		$medicationRouteName = $route_row["route_name"];
			                		$medicationRouteExternalId = $route_row["id"];
			                	}
						    }

						    // find the external medicationId
						    if (isset($med_data_arr['title']) && $med_data_arr['title'] !='') {
						    	$med_sql = "SELECT * FROM medicine_data WHERE medicine_name='".$med_data_arr['title']."' limit 1";
			                	$med_row=sqlQuery($med_sql);
			                	if($med_row!=false){
			                		$medicationId = $med_row["id"];
			                	}
						    }

						    // find weather this medicin is for eye or not
						    $eyeMedication = false;
						    if ($med_type == 4) {
						    	$eyeMedication = true;
						    }
		                
			                $medicationApi = array();
						    $medicationApi['patientExternalId']=$med_data_arr['pid'];
						    $medicationApi['MedicationExternalId']=$medicationId;
						    $medicationApi['medicationName']=$med_data_arr['title'];
						    $medicationApi['MedicationDosageName']=$med_data_arr['destination'];
						    $medicationApi['medicationRouteExternalId']= $medicationRouteExternalId;
						    $medicationApi['medicationRouteName']= $medicationRouteName;
						    $medicationApi['active']= ($med_data_arr['allergy_status']=='Active')? true : false;
						    $medicationApi['eyeMedication']= $eyeMedication;
						    $medicationApi['id']='';
							$medicationApi['externalId']=$mid;
						    $obj_patients->addUpdateMedicationRecords($med_data_arr['pid'], $medicationApi);
						}
	                }

				} catch(Exception $e) {
					$erp_error[]='Unable to connect to ERP Portal';
				}
			}


			/* ERP PORTAL CREATE NEW PATIENT */
			if(isERPPortalEnabled()) {
				try {
					//For ERP Patient Portal API
					include_once($GLOBALS['srcdir']."/erp_portal/patient_medications.php");
					$obj_patients = new patient_medications();
					$arrSites=array(1=>'OS', 2=>'OD', 3=>'OU', 4=>'PO');
				
					if($med_data_arr['allergy_status']=='Stop' || $med_data_arr['allergy_status']=='Discontinue'){
						$obj_patients->deleteMedication($mid);
					}else{
						$arrInstructions=array();
						$arrInstructions[]=$arrSites[$med_data_arr['sites']];
						$arrInstructions[]=$med_data_arr['destination'];
						$arrInstructions[]=$med_data_arr['sig']; 
						$arrInstructions[]=$med_data_arr['med_comments'];
						$arrInstructions=array_filter($arrInstructions); //REMOVE EMPTY STRINGS

						$arrppApi['name']=$med_data_arr['title'];
						$arrppApi['instructions']=implode(', ', $arrInstructions);
						$arrppApi['patientExternalId']=$med_data_arr['pid'];
						$arrppApi['doctorExternalId']=$med_data_arr['referredby'];				
						$arrppApi['active']= ($med_data_arr['allergy_status']=='Stop' || $med_data_arr['allergy_status']=='Discontinue')? false : true;
						$arrppApi['id']='';		
						$arrppApi['externalId']=$mid;	
												
						$obj_patients->addUpdateMedication($med_data_arr['pid'], $arrppApi);
					}
				} catch(Exception $e) {
					$erp_error[]='Unable to connect to ERP Portal';
				}
			}	
			//---------------------------	
						
			$blDoRewiev = false;
			if($md_action == 'update'){
				if(in_array($mid, $arrMedIdVizChange) == true){					
					$blDoRewiev = true;
				}
			}
			elseif($md_action == 'add'){
				$blDoRewiev = true;
			}
			
			
			if($blDoRewiev == true){
				//--- REVIEWED CODE ----
				$med_reviewed_arr = array();
				//require_once(dirname(__FILE__).'/../../common/audit_common_function.php');
				//$medDataFields = makeFieldTypeArray("select * from lists LIMIT 0 , 1");
				$medDataFields = make_field_type_array("lists");
				//--- REVIEWED FOR MEDICATION TYPE ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'med_type';
				$reviewed_data_arr['Data_Base_Field_Name']= "type";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"type");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Ocular - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['type'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['type'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION TITLE ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_medication'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "title";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"title");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Name - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['title'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['title'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION DOSES ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_dosage'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "destination";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"destination");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Doses - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['destination'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['destination'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION SITE ---
				$st='';$stDbase='';
				$medSite=trim($_POST['md_occular'.$i]);
				if($medSite=='1') { $st='OS';
				}elseif($medSite=='2') { $st='OD';
				}elseif($medSite=='3') { $st='OU';
				}elseif($medSite=='4') { $st='PO';
				}
				
				$medSiteDbase=$med_data_exists_arr['sites'];
				if($medSiteDbase=='1') { $stDbase='OS';
				}elseif($medSiteDbase=='2') { $stDbase='OD';
				}elseif($medSiteDbase=='3') { $stDbase='OU';
				}elseif($medSiteDbase=='4') { $stDbase='PO';
				}
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_occular'.$i;
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Site - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $stDbase;
				$reviewed_data_arr['New_Value'] = $st;
				$med_reviewed_arr[] = $reviewed_data_arr;

				//--- REVIEWED FOR MEDICATION SIG ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_sig'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "sig";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"sig");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Sig - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['sig'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['sig'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION COMPLAINT ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				//$reviewed_data_arr['UI_Filed_Name'] = 'compliant'.$i;
				$reviewed_data_arr['UI_Filed_Name'] = 'compliant';
				$reviewed_data_arr['Data_Base_Field_Name']= "compliant";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"compliant");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Compliant - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['compliant'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['compliant'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION BEGIN DATE---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_begindate'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "begdate";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"begdate");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Begin Date - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['begdate'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['begdate'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION END DATE ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_enddate'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "enddate";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"enddate");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication End Date  - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['enddate'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['enddate'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION Comments ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_comments'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "comments";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"comments");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Comments  - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['med_comments'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['med_comments'];
				$med_reviewed_arr[] = $reviewed_data_arr;

				//--- REVIEWED FOR MEDICATION Ordered BY ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'referredby'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "referredby";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"referredby");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Ordered By - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Depend_Select'] = "select CONCAT_WS(', ',lname,fname) as orderedBy";
				$reviewed_data_arr['Depend_Table'] = "users";
				$reviewed_data_arr['Depend_Search'] = "id";
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['referredby'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['referredby'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION REFERRED BY ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'cbMedicationStatus'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "allergy_status";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"allergy_status");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Status  - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['allergy_status'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['allergy_status'];
				$med_reviewed_arr[] = $reviewed_data_arr;

				//--- REVIEWED FOR MEDICATION RxNorm Code ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'ccda_code'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "ccda_code";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"ccda_code");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication RxNorm Code  - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['ccda_code'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['ccda_code'];
				$med_reviewed_arr[] = $reviewed_data_arr;

				//--- REVIEWED FOR MEDICATION QTY ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_qty'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "qty";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"qty");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Qty - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['qty'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['qty'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				//--- REVIEWED FOR MEDICATION REFILLS ---
				$reviewed_data_arr = array();
				$reviewed_data_arr['Pk_Id'] = $mid;
				$reviewed_data_arr['Table_Name'] = 'lists';
				$reviewed_data_arr['UI_Filed_Name'] = 'md_refills'.$i;
				$reviewed_data_arr['Data_Base_Field_Name']= "refills";
				$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"refills");
				$reviewed_data_arr['Field_Text'] = 'Patient Medication Refills - '.trim($_POST['md_medication'.$i]);
				$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
				$reviewed_data_arr['Action'] = $md_action;
				$reviewed_data_arr['Old_Value'] = $med_data_exists_arr['refills'];
				$reviewed_data_arr['New_Value'] = $med_data_arr['refills'];
				$med_reviewed_arr[] = $reviewed_data_arr;
				
				$OBJReviewMedHx->reviewMedHx($med_reviewed_arr,$_SESSION['authId'],"Medications",$_SESSION['patient'],0,0);
			}
		}
		}//else
	}

	//
	if(!empty($_REQUEST["prv_frmid"])){
		$qryMed = "select lists from chart_genhealth_archive WHERE patient_id='".$_SESSION['patient']."'  AND form_id='".$_REQUEST["prv_frmid"]."' ";
		$row = sqlQuery($qryMed);
		if($row!=false){
			$arrLists = unserialize($row["lists"]);
			//$arrLists[4] = array();
			$medQryRes = $arrLists[4];
			
			if(count($arrLists_prv_charts)>0){
				foreach($arrLists_prv_charts as $k => $arm){
					$flg=1;
					if(!empty($arm["id"])){
						//edit
						if(count($medQryRes)>0){
							foreach($medQryRes as $j => $arMQ){
								if($arMQ["id"] == $arm["id"]){
									$medQryRes[$j] = array_merge($arMQ, $arm);
									$flg=0;
								}
							}
						}
					}
					
					if(!empty($flg)){
						$arm["allergy_status"] = "Deleted";
						$mid =0;
						$mid = AddRecords($arm,'lists');
						if(!empty($mid)){
							$sql = "SELECT * FROM lists WHERE id='".$mid."' ";
							$row=sqlQuery($sql);
							if($row!=false){ $row["allergy_status"] = "Active"; $medQryRes[] = $row; }
						}
					}
				}
			}
			
			$arrLists[4] = $medQryRes;
			$lists = sqlEscStr(serialize($arrLists));
			
			//Save
			$sql="UPDATE chart_genhealth_archive SET lists='".$lists."' WHERE patient_id='".$_SESSION['patient']."' AND form_id='".$_REQUEST["prv_frmid"]."' ";
			$row=sqlQuery($sql);
		}
	}
}

//remove
if(!empty($_REQUEST["prv_frmid"])){}
else{
	/// Save Update No Surgery  Option//
	$getRES = commonNoMedicalHistoryAddEdit($moduleName="Medication",$_REQUEST["commonNoMedications"],$mod="save");
}

//End Save Updte No Surgery Option//
if($_REQUEST["callFrom"] == "WV" && $_REQUEST["subcallFrom"] == "")
{
	//echo '<script type="text/javascript" src="../../../js/jquery.js"></script >';
	echo '<script>
		//update PMH in WV ---
		var ofmain = window.opener.top.fmain;
		if(ofmain && typeof(ofmain.showMedList) != "undefined"){ ofmain.showMedList("PMH",1);}
		if(ofmain && typeof(ofmain.getMedHx) != "undefined"){ ofmain.getMedHx();}
		top.window.close();
		</script>';
}
if($_REQUEST["subcallFrom"] == "grid")
{
	echo '<script>
		var ofmain = window.opener.top.fmain;
		if(ofmain && typeof(ofmain.getMedHx) != "undefined"){ ofmain.getMedHx();}
		top.window.close();
		</script>';
}//redirecting...

$curr_tab = xss_rem($_REQUEST["curr_tab"]);
$curr_dir = "medications";
$next_tab = xss_rem($_REQUEST["next_tab"]);
$next_dir = xss_rem($_REQUEST["next_dir"]);  
if($next_tab != ""){
	$curr_tab = $next_tab;
}
if($next_dir != ""){
	$curr_dir = $next_dir;
}
$buttons_to_show = xss_rem($_REQUEST["buttons_to_show"]);

// Remove Remote Sync Code

if($_REQUEST["callFrom"] != "WV")
{
?>
<script type="text/javascript">
	var curr_tab = '<?php echo $curr_tab; ?>';
	top.show_loading_image("show", 100);
	if(top.document.getElementById('medical_tab_change')) {
		if(top.document.getElementById('medical_tab_change').value!='yes') {
			top.alert_notification_show('<?php echo $arr_info_alert["save"];?>');
		}
		if(top.document.getElementById('medical_tab_change').value=='yes') {
			top.chkConfirmSave('yes','set');		
		}
		top.document.getElementById('medical_tab_change').value='';
	}
	top.fmain.location.href = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/index.php?showpage='+curr_tab;
	top.show_loading_image("hide");
</script>
<?php }?>