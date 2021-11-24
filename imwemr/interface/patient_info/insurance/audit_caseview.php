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
?>
<?php

include_once(dirname(__FILE__)."/../../globals.php");
//audit arrays
$arrAuditTrailPri = array();
$arrAuditTrailSec = array();
$arrAuditTrailTer = array();

//audit vars
$opreaterId = $_SESSION['authId'];	
$ip = getRealIpAddr();
$URL = $_SERVER['PHP_SELF'];													 
//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
$os = getOS();
$browserInfoArr = array();
$browserInfoArr = _browser();
$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
$browserName = str_replace(";","",$browserInfo);													 
$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);

//primary											 
$arrAuditTrailPri [] = array(																								
							"Ins_Type"=> "primary",	
							"Pk_Id"=> $primaryComDetail->id,
							"Table_Name"=>"insurance_data",
							"Data_Base_Field_Name"=> "provider" ,
							"Filed_Text"=> "Patient Primary Insurance Ins. Provider",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"provider") ,
							"Filed_Label"=> "i1provider",																						
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> $_REQUEST['macaddrs'],
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "patient_info",
							"Category_Desc"=> "insurence",
							"Depend_Select"=> "select name as provider" ,
							"Depend_Table"=> "insurance_companies" ,
							"Depend_Search"=> "id" ,
							"Old_Value"=> ($primaryComDetail->provider) ? trim($primaryComDetail->provider) : ""	,
							"pid"=> $_SESSION['patient']																																									
						);
$arrAuditTrailPri [] = array(
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "self_pay_provider" ,
							"Filed_Label"=> "self_pay_provider",
							"Filed_Text"=> "Patient Primary Insurance Self Pay",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"self_pay_provider") ,
							"Old_Value"=> (trim($primaryComDetail->self_pay_provider)=='0') ? '' : trim($primaryComDetail->self_pay_provider)
						);
$arrAuditTrailPri [] = array(
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "policy_number" ,
							"Filed_Label"=> "i1policy_number",
							"Filed_Text"=> "Patient Primary Insurance Policy",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"policy_number") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->policy_number)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "copay" ,
							"Filed_Label"=> "i1copay",
							"Filed_Text"=> "Patient Insurance Primary CoPay",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"copay") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->copay)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "co_ins" ,
							"Filed_Label"=> "i1co_ins",
							"Filed_Text"=> "Patient Insurance Primary Co Ins",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"co_ins") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->co_ins)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "copay_type" ,
							"Filed_Label"=> "pri_copay_type",
							"Filed_Text"=> "Patient Insurance Primary Copay Type",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"copay_type") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->copay_type)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "referal_required" ,
							"Filed_Label"=> "i1referalreq",
							"Filed_Text"=> "Patient Insurance Primary Ref. Req",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"referal_required") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->referal_required)),"\0..\37!@\177..\377")																					
						);						
$arrAuditTrailPri [] = array(
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "auth_required" ,
							"Filed_Label"=> "i1authreq",
							"Filed_Text"=> "Patient Insurance Primary Auth Req",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"auth_required") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->auth_required)),"\0..\37!@\177..\377")																					
						);	
$arrAuditTrailPri [] = array(
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "claims_adjustername" ,
							"Filed_Label"=> "i1claims_adjustername",
							"Filed_Text"=> "Patient Insurance Primary Adj. Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"claims_adjustername") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->claims_adjustername)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(					
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "claims_adjusterphone" ,
							"Filed_Label"=> "i1claims_adjusterphone",
							"Filed_Text"=> "Patient Insurance Primary Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"claims_adjusterphone") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->claims_adjusterphone)),"\0..\37!@\177..\377")																					
						);	
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "group_number" ,
							"Filed_Label"=> "i1group_number",
							"Filed_Text"=> "Patient Primary Insurance Group#",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"group_number") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->group_number)),"\0..\37!@\177..\377")																					
						);						
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "plan_name" ,
							"Filed_Label"=> "i1plan_name",
							"Filed_Text"=> "Patient Primary Insurance Plan Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"plan_name") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->plan_name)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "effective_date" ,
							"Filed_Label"=> "i1effective_date",
							"Filed_Text"=> "Patient Primary Insurance Act. Date",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"effective_date") ,
							"Old_Value"=> addcslashes(addslashes(trim($effective_date)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "expiration_date" ,
							"Filed_Label"=> "i1expiration_date",
							"Filed_Text"=> "Patient Primary Insurance Exp. Date",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"expiration_date") ,
							"Old_Value"=> addcslashes(addslashes(trim($expiration_date)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_relationship" ,
							"Filed_Label"=> "i1subscriber_relationship",
							"Filed_Text"=> "Patient Primary Insurance Sub.Relation",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_relationship") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_relationship)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_ss" ,
							"Filed_Label"=> "i1subscriber_ss",
							"Filed_Text"=> "Patient Primary Insurance S.S",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_ss") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_ss)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_fname" ,
							"Filed_Label"=> "i1subscriber_fname",
							"Filed_Text"=> "Patient Primary Insurance First Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_fname") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_fname)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_mname" ,
							"Filed_Label"=> "i1subscriber_mname",
							"Filed_Text"=> "Patient Primary Insurance Middle Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_mname") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_mname)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_lname" ,
							"Filed_Label"=> "lastName",
							"Filed_Text"=> "Patient Primary Insurance Last Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_lname") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_lname)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_suffix",
							"Filed_Label"=> "suffix_rel_pri",
							"Filed_Text"=> "Patient Primary Insurance Suffix",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_suffix") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_suffix)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_DOB" ,
							"Filed_Label"=> "i1subscriber_DOB",
							"Filed_Text"=> "Patient Primary Insurance DOB",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_DOB") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_DOB)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_sex" ,
							"Filed_Label"=> "i1subscriber_sex",
							"Filed_Text"=> "Patient Primary Insurance Gender",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_sex") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_sex)),"\0..\37!@\177..\377")																					
						);
$priComments = (trim($primaryComDetail->comments)=='') ? 'comments...' : addcslashes(addslashes(trim($primaryComDetail->comments)),"\0..\37!@\177..\377");
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "comments" ,
							"Filed_Label"=> "i1comments",
							"Filed_Text"=> "Patient Primary Insurance Comments",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"comments") ,
							"Old_Value"=> $priComments		
						);						
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_street" ,
							"Filed_Label"=> "i1subscriber_street",
							"Filed_Text"=> "Patient Primary Insurance Address 1",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_street") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_street)),"\0..\37!@\177..\377")
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_street_2" ,
							"Filed_Label"=> "i1subscriber_street_2",
							"Filed_Text"=> "Patient Primary Insurance Address 2",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_street_2") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_street_2)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] =  array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_postal_code" ,
							"Filed_Label"=> "i1subscriber_postal_code",
							"Filed_Text"=> "Patient Primary Insurance Zip",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_postal_code") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_postal_code)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] =  array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "zip_ext" ,
							"Filed_Label"=> "i1subscriber_zip_ext",
							"Filed_Text"=> "Patient Primary Insurance Zip Ext.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"zip_ext") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->zip_ext)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_city" ,
							"Filed_Label"=> "i1subscriber_city",
							"Filed_Text"=> "Patient Primary Insurance City",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_city") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_city)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] =  array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_state" ,
							"Filed_Label"=> "i1subscriber_state",
							"Filed_Text"=> "Patient Primary Insurance State",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_state") ,
							"Old_Value"=> addcslashes(addslashes(trim($primaryComDetail->subscriber_state)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_phone" ,
							"Filed_Label"=> "i1subscriber_phone",
							"Filed_Text"=> "Patient Primary Insurance Home Tel.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_phone") ,
							"Old_Value"=> addcslashes(addslashes(trim(core_phone_format($primaryComDetail->subscriber_phone))),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_biz_phone" ,
							"Filed_Label"=> "i1subscriber_biz_phone",
							"Filed_Text"=> "Patient Primary Insurance Work Tel.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_biz_phone") ,
							"Old_Value"=> addcslashes(addslashes(trim(core_phone_format($primaryComDetail->subscriber_biz_phone))),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailPri [] = array(							
							"Ins_Type"=> "primary",																
							"Data_Base_Field_Name"=> "subscriber_mobile" ,
							"Filed_Label"=> "i1subscriber_mobile",
							"Filed_Text"=> "Patient Primary Insurance Mobile",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_mobile") ,
							"Old_Value"=> addcslashes(addslashes(trim(core_phone_format($primaryComDetail->subscriber_mobile))),"\0..\37!@\177..\377")																					
						);

// primary auth
//$arrAuditTrailPriAuth= array();
/*$arrAuditTrailPriAuth [] = array(							
								"Ins_Type"=> "primary",			
								"Pk_Id"=> $reffRes->reff_id,
								"Table_Name"=>"patient_reff",															
								"Data_Base_Field_Name"=> "reff_phy_id" ,
								"Filed_Label"=> "ref1_phyId",
								"Filed_Text"=> "Patient Primary Referral Ref. Physician",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_phy_id") ,																				
								"Operater_Id"=> $opreaterId,
								"Operater_Type"=> getOperaterType($opreaterId) ,
								"IP"=> $ip,
								"MAC_Address"=> $_REQUEST['macaddrs'],
								"URL"=> $URL,
								"Browser_Type"=> $browserName,
								"OS"=> $os,
								"Machine_Name"=> $machineName,
								"Category"=> "patient_info",
								"Category_Desc"=> "insurence",
								"Depend_Select"=> "select CONCAT_WS(',',FirstName,LastName) as refPhy" ,
								"Depend_Table"=> "refferphysician" ,
								"Depend_Search"=> "physician_Reffer_id" ,
								"Old_Value"=> addcslashes(addslashes(trim($reffRes->reff_phy_id)),"\0..\37!@\177..\377"),																				
								"pid"=> $_SESSION['patient']
							);*/
//primary referral
/*$arrAuditTrailPriRef = array();
$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",			
								"Pk_Id"=> $reffRes->reff_id,
								"Table_Name"=>"patient_reff",															
								"Data_Base_Field_Name"=> "reff_phy_id" ,
								"Filed_Label"=> "ref1_phyId",
								"Filed_Text"=> "Patient Primary Referral Ref. Physician",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_phy_id") ,																				
								"Operater_Id"=> $opreaterId,
								"Operater_Type"=> getOperaterType($opreaterId) ,
								"IP"=> $ip,
								"MAC_Address"=> $_REQUEST['macaddrs'],
								"URL"=> $URL,
								"Browser_Type"=> $browserName,
								"OS"=> $os,
								"Machine_Name"=> $machineName,
								"Category"=> "patient_info",
								"Category_Desc"=> "insurence",
								"Depend_Select"=> "select CONCAT_WS(',',FirstName,LastName) as refPhy" ,
								"Depend_Table"=> "refferphysician" ,
								"Depend_Search"=> "physician_Reffer_id" ,
								"Old_Value"=> addcslashes(addslashes(trim($reffRes->reff_phy_id)),"\0..\37!@\177..\377"),																				
								"pid"=> $_SESSION['patient']
							);
$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "effective_date" ,
								"Filed_Label"=> "eff1_date",
								"Filed_Text"=> "Patient Primary Referral Start Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"effective_date") ,
								"Old_Value"=> addcslashes(addslashes(trim($reffRes->effective_date)),"\0..\37!@\177..\377")																					
							);
$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "end_date" ,
								"Filed_Label"=> "end1_date",
								"Filed_Text"=> "Patient Primary Referral End Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"end_date") ,
								"Old_Value"=> addcslashes(addslashes(trim($reffRes->end_date)),"\0..\37!@\177..\377")																					
							);
$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "no_of_reffs" ,
								"Filed_Label"=> "priNoRef",
								"Filed_Text"=> "Patient Primary Referral No. of Visits",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"no_of_reffs") ,
								"Old_Value"=> addcslashes(addslashes(trim($reffRes->no_of_reffs)),"\0..\37!@\177..\377")																					
							);
$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "reff_used" ,
								"Filed_Label"=> "priUsedRef",
								"Filed_Text"=> "Patient Primary Referral Used Visits",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_used") ,
								"Old_Value"=> addcslashes(addslashes(trim($reffRes->reff_used)),"\0..\37!@\177..\377")																					
							);
$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "reffral_no" ,
								"Filed_Label"=> "reffral_no1",
								"Filed_Text"=> "Patient Primary Referral#",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reffral_no") ,
								"Old_Value"=> addcslashes(addslashes(trim($reffRes->reffral_no)),"\0..\37!@\177..\377")																					
							);
$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "reff_date" ,
								"Filed_Label"=> "reff1_date",
								"Filed_Text"=> "Patient Primary Referral Ref. Date",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_date") ,
								"Old_Value"=> addcslashes(addslashes(trim($reffRes->reff_date)),"\0..\37!@\177..\377")																					
							);
$arrAuditTrailPriRef [] = array(							
								"Ins_Type"=> "primary",																
								"Data_Base_Field_Name"=> "note" ,
								"Filed_Label"=> "note1",
								"Filed_Text"=> "Patient Primary Referral Notes",
								"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"note") ,
								"Old_Value"=> addcslashes(addslashes(trim($reffRes->note)),"\0..\37!@\177..\377")																					
							);*/
//secondary
$arrAuditTrailSec [] = array(																								
							"Ins_Type"=> "secondary",	
							"Pk_Id"=> $secondaryComDetail->id,
							"Table_Name"=>"insurance_data",
							"Data_Base_Field_Name"=> "provider" ,
							"Filed_Text"=> "Patient Secondary Insurance Ins. Provider",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"provider") ,
							"Filed_Label"=> "i2provider",																						
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> $_REQUEST['macaddrs'],
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "patient_info",
							"Category_Desc"=> "insurence",
							"Depend_Select"=> "select name as provider" ,
							"Depend_Table"=> "insurance_companies" ,
							"Depend_Search"=> "id" ,
							"Old_Value"=> ($secondaryComDetail->provider) ? trim($secondaryComDetail->provider) : "",
							"pid"=> $_SESSION['patient']																																										
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "policy_number" ,
							"Filed_Label"=> "i2policy_number",
							"Filed_Text"=> "Patient Secondary Insurance Policy",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"policy_number") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->policy_number)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "group_number" ,
							"Filed_Label"=> "i2group_number",
							"Filed_Text"=> "Patient Secondary Insurance Group No.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"group_number") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->group_number)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "copay" ,
							"Filed_Label"=> "i2copay",
							"Filed_Text"=> "Patient Insurance Secondary CoPay",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"copay") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->copay)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "copay_type" ,
							"Filed_Label"=> "sec_copay_type",
							"Filed_Text"=> "Patient Insurance Secondary Copay Type",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"copay_type") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->copay_type)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "referal_required" ,
							"Filed_Label"=> "i2referalreq",
							"Filed_Text"=> "Patient Insurance Secondary Ref. Req",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"referal_required") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->referal_required)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "auth_required" ,
							"Filed_Label"=> "i2authreq",
							"Filed_Text"=> "Patient Insurance Secondary Auth Req",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"auth_required") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->auth_required)),"\0..\37!@\177..\377")																					
						);						
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "claims_adjustername" ,
							"Filed_Label"=> "i2claims_adjustername",
							"Filed_Text"=> "Patient Insurance Secondary Adj. Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"claims_adjustername") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->claims_adjustername)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "claims_adjusterphone" ,
							"Filed_Label"=> "i2claims_adjusterphone",
							"Filed_Text"=> "Patient Insurance Secondary Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"claims_adjusterphone") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->claims_adjusterphone)),"\0..\37!@\177..\377")																					
						);	
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "plan_name" ,
							"Filed_Label"=> "i2plan_name",
							"Filed_Text"=> "Patient Secondary Insurance Plan Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"plan_name") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->plan_name)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "effective_date" ,
							"Filed_Label"=> "i2effective_date",
							"Filed_Text"=> "Patient Secondary Insurance Act. Date",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"effective_date") ,
							"Old_Value"=> addcslashes(addslashes(trim($effective_date2)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "expiration_date" ,
							"Filed_Label"=> "i2expiration_date",
							"Filed_Text"=> "Patient Secondary Insurance Exp. Date",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"expiration_date") ,
							"Old_Value"=> addcslashes(addslashes(trim($expiration_date2)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_relationship" ,
							"Filed_Label"=> "i2subscriber_relationship",
							"Filed_Text"=> "Patient Secondary Insurance Sub.Relation",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_relationship") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_relationship)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_ss" ,
							"Filed_Label"=> "i2subscriber_ss",
							"Filed_Text"=> "Patient Secondary Insurance S.S",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_ss") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_ss)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_fname" ,
							"Filed_Label"=> "i2subscriber_fname",
							"Filed_Text"=> "Patient Secondary Insurance First Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_fname") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_fname)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_mname" ,
							"Filed_Label"=> "i2subscriber_mname",
							"Filed_Text"=> "Patient Secondary Insurance Middle Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_mname") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_mname)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_lname" ,
							"Filed_Label"=> "lastName2",
							"Filed_Text"=> "Patient Secondary Insurance Last Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_lname") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_lname)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_suffix",
							"Filed_Label"=> "suffix_rel_sec",
							"Filed_Text"=> "Patient Secondary Insurance Suffix",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_suffix") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_suffix)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_DOB" ,
							"Filed_Label"=> "i2subscriber_DOB",
							"Filed_Text"=> "Patient Secondary Insurance DOB",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_DOB") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_DOB)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] =  array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_sex" ,
							"Filed_Label"=> "i2subscriber_sex",
							"Filed_Text"=> "Patient Secondary Insurance Gender",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_sex") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_sex)),"\0..\37!@\177..\377")																					
						);
$secComments = (trim($secondaryComDetail->comments)=='') ? 'comments...' : addcslashes(addslashes(trim($secondaryComDetail->comments)),"\0..\37!@\177..\377");
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "comments" ,
							"Filed_Label"=> "i2comments",
							"Filed_Text"=> "Patient Secondary Insurance Comments",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"comments") ,
							"Old_Value"=> $secComments
						);						
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_street" ,
							"Filed_Label"=> "i2subscriber_street",
							"Filed_Text"=> "Patient Secondary Insurance Address 1",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_street") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_street)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_street_2" ,
							"Filed_Label"=> "i2subscriber_street_2",
							"Filed_Text"=> "Patient Secondary Insurance Address 2",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_street_2") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_street_2)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_postal_code" ,
							"Filed_Label"=> "i2subscriber_postal_code",
							"Filed_Text"=> "Patient Secondary Insurance Zip",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_postal_code") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_postal_code)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "zip_ext" ,
							"Filed_Label"=> "i2subscriber_zip_ext",
							"Filed_Text"=> "Patient Secondary Insurance Zip Ext.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"zip_ext") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->zip_ext)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_city" ,
							"Filed_Label"=> "i2subscriber_city",
							"Filed_Text"=> "Patient Secondary Insurance City",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_city") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_city)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_state" ,
							"Filed_Label"=> "i2subscriber_state",
							"Filed_Text"=> "Patient Secondary Insurance State",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_state") ,
							"Old_Value"=> addcslashes(addslashes(trim($secondaryComDetail->subscriber_state)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] =  array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_phone" ,
							"Filed_Label"=> "i2subscriber_phone",
							"Filed_Text"=> "Patient Secondary Insurance Home Tel.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_phone") ,
							"Old_Value"=> addcslashes(addslashes(trim(core_phone_format($secondaryComDetail->subscriber_phone))),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_biz_phone" ,
							"Filed_Label"=> "i2subscriber_biz_phone",
							"Filed_Text"=> "Patient Secondary Insurance Work Tel.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_biz_phone") ,
							"Old_Value"=> addcslashes(addslashes(trim(core_phone_format($secondaryComDetail->subscriber_biz_phone))),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSec [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "subscriber_mobile" ,
							"Filed_Label"=> "i2subscriber_mobile",
							"Filed_Text"=> "Patient Secondary Insurance Mobile",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_mobile") ,
							"Old_Value"=> addcslashes(addslashes(trim(core_phone_format($secondaryComDetail->subscriber_mobile))),"\0..\37!@\177..\377")																					
						);

//secondary referral
/*$arrAuditTrailSecRef = array();
$arrAuditTrailSecRef [] = array(							
							"Ins_Type"=> "secondary",				
							"Table_Name"=>"patient_reff",	
							"Pk_Id"=> $reffRes2->reff_id,												
							"Data_Base_Field_Name"=> "reff_phy_id" ,
							"Filed_Label"=> "ref2_phyId",
							"Filed_Text"=> "Patient Secondary Referral Ref. Physician",
							"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_phy_id") ,
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> $_REQUEST['macaddrs'],
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "patient_info",
							"Category_Desc"=> "insurence",
							"Depend_Select"=> "select CONCAT_WS(',',FirstName,LastName) as refPhy" ,
							"Depend_Table"=> "refferphysician" ,
							"Depend_Search"=> "physician_Reffer_id" ,
							"Old_Value"=> addcslashes(addslashes(trim($reffRes2->reff_phy_id)),"\0..\37!@\177..\377"),
							"pid"=> $_SESSION['patient']																					
						);
$arrAuditTrailSecRef [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "effective_date" ,
							"Filed_Label"=> "eff2_date",
							"Filed_Text"=> "Patient Secondary Referral Start Date",
							"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"effective_date") ,
							"Old_Value"=> addcslashes(addslashes(trim($reffRes2->effective_date)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSecRef [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "end_date" ,
							"Filed_Label"=> "end2_date",
							"Filed_Text"=> "Patient Secondary Referral End Date",
							"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"end_date") ,
							"Old_Value"=> addcslashes(addslashes(trim($reffRes2->end_date)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSecRef [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "no_of_reffs" ,
							"Filed_Label"=> "secNoRef",
							"Filed_Text"=> "Patient Secondary Referral No. of Visits",
							"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"no_of_reffs") ,
							"Old_Value"=> addcslashes(addslashes(trim($reffRes2->no_of_reffs)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSecRef [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "reff_used" ,
							"Filed_Label"=> "secUsedRef",
							"Filed_Text"=> "Patient Secondary Referral Used Visits",
							"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_used"));
							
$arrAuditTrailSecRef [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "reffral_no" ,
							"Filed_Label"=> "reffral_no2",
							"Filed_Text"=> "Patient Secondary Referral#",
							"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reffral_no") ,
							"Old_Value"=> addcslashes(addslashes(trim($reffRes2->reffral_no)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSecRef [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "reff_date" ,
							"Filed_Label"=> "reff2_date",
							"Filed_Text"=> "Patient Secondary Referral Ref. Date",
							"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_date") ,
							"Old_Value"=> addcslashes(addslashes(trim($reffRes2->reff_date)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailSecRef [] = array(							
							"Ins_Type"=> "secondary",																
							"Data_Base_Field_Name"=> "note" ,
							"Filed_Label"=> "note2",
							"Filed_Text"=> "Patient Secondary Referral Notes",
							"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"note") ,
							"Old_Value"=> addcslashes(addslashes(trim($reffRes2->note)),"\0..\37!@\177..\377")																					
						);
*/

//Tertiary
$arrAuditTrailTer [] = array(																								
							"Ins_Type"=> "tertiary",	
							"Pk_Id"=> $tertiaryComDetail->id,
							"Table_Name"=>"insurance_data",
							"Data_Base_Field_Name"=> "provider" ,
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"provider") ,
							"Filed_Label"=> "i3provider",								
							"Filed_Text"=> "Patient Tertiary Insurance Ins. Provider",														
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> $_REQUEST['macaddrs'],
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "patient_info",
							"Category_Desc"=> "insurence",
							"Depend_Select"=> "select name as provider" ,
							"Depend_Table"=> "insurance_companies" ,
							"Depend_Search"=> "id" ,
							"Old_Value"=> ($tertiaryComDetail->provider) ? trim($tertiaryComDetail->provider) : "",
							"pid"=> $_SESSION['patient']																																										
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "policy_number" ,
							"Filed_Label"=> "i3policy_number",
							"Filed_Text"=> "Patient Tertiary Insurance Policy",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"policy_number") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->policy_number)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "group_number" ,
							"Filed_Label"=> "i3group_number",
							"Filed_Text"=> "Patient Tertiary Insurance Group No.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"group_number") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->group_number)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "copay" ,
							"Filed_Label"=> "i3copay",
							"Filed_Text"=> "Patient Insurance Tertiary CoPay",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"copay") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->copay)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "copay_type" ,
							"Filed_Label"=> "tri_copay_type",
							"Filed_Text"=> "Patient Insurance Tertiary Copay Type",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"copay_type") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->copay_type)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "referal_required" ,
							"Filed_Label"=> "i3referalreq",
							"Filed_Text"=> "Patient Insurance Tertiary Ref. Req",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"referal_required") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->referal_required)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "auth_required" ,
							"Filed_Label"=> "i3authreq",
							"Filed_Text"=> "Patient Insurance Tertiary Auth Req",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"auth_required") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->auth_required)),"\0..\37!@\177..\377")																					
						);						
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "claims_adjustername" ,
							"Filed_Label"=> "i3claims_adjustername",
							"Filed_Text"=> "Patient Insurance Tertiary Adj. Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"claims_adjustername") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->claims_adjustername)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "claims_adjusterphone" ,
							"Filed_Label"=> "i3claims_adjusterphone",
							"Filed_Text"=> "Patient Insurance Tertiary Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"claims_adjusterphone") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->claims_adjusterphone)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "plan_name" ,
							"Filed_Label"=> "i3plan_name",
							"Filed_Text"=> "Patient Tertiary Insurance Plan Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"plan_name") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->plan_name)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "effective_date" ,
							"Filed_Label"=> "i3effective_date",
							"Filed_Text"=> "Patient Tertiary Insurance Act. Date",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"effective_date") ,
							"Old_Value"=> addcslashes(addslashes(trim($effective_date3)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "expiration_date" ,
							"Filed_Label"=> "i3expiration_date",
							"Filed_Text"=> "Patient Tertiary Insurance Exp. Date",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"expiration_date") ,
							"Old_Value"=> addcslashes(addslashes(trim($expiration_date3)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_relationship" ,
							"Filed_Label"=> "i3subscriber_relationship",
							"Filed_Text"=> "Patient Tertiary Insurance Sub.Relation",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_relationship") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_relationship)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_ss" ,
							"Filed_Label"=> "i3subscriber_ss",
							"Filed_Text"=> "Patient Tertiary Insurance S.S",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_ss") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_ss)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_fname" ,
							"Filed_Label"=> "i3subscriber_fname",
							"Filed_Text"=> "Patient Tertiary Insurance First Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_fname") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_fname)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_mname" ,
							"Filed_Label"=> "i3subscriber_mname",
							"Filed_Text"=> "Patient Tertiary Insurance Middle Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_mname") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_mname)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_lname" ,
							"Filed_Label"=> "lastName3",
							"Filed_Text"=> "Patient Tertiary Insurance Last Name",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_lname") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_lname)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_suffix",
							"Filed_Label"=> "suffix_rel_ter",
							"Filed_Text"=> "Patient Tertiary Insurance Suffix",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_suffix") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_suffix)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_DOB" ,
							"Filed_Label"=> "i3subscriber_DOB",
							"Filed_Text"=> "Patient Tertiary Insurance DOB",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_DOB") ,
							"Old_Value"=> addcslashes(addslashes(trim($terDOB)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_sex" ,
							"Filed_Label"=> "i3subscriber_sex",
							"Filed_Text"=> "Patient Tertiary Insurance Gender",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_sex") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_sex)),"\0..\37!@\177..\377")																					
						);
$terComments = (trim($tertiaryComDetail->comments)=='') ? 'comments...' : addcslashes(addslashes(trim($tertiaryComDetail->comments)),"\0..\37!@\177..\377");
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "comments" ,
							"Filed_Label"=> "i3comments",
							"Filed_Text"=> "Patient Tertiary Insurance Comments",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"comments") ,
							"Old_Value"=> $terComments
						);						
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_street" ,
							"Filed_Label"=> "i3subscriber_street",
							"Filed_Text"=> "Patient Tertiary Insurance Address 1",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_street") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_street)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_street_2" ,
							"Filed_Label"=> "i3subscriber_street_2",
							"Filed_Text"=> "Patient Tertiary Insurance Address 2",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_street_2") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_street_2)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_postal_code" ,
							"Filed_Label"=> "i3subscriber_postal_code",
							"Filed_Text"=> "Patient Tertiary Insurance Zip",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_postal_code") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_postal_code)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "zip_ext" ,
							"Filed_Label"=> "i3subscriber_zip_ext",
							"Filed_Text"=> "Patient Tertiary Insurance Zip Ext.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"zip_ext") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->zip_ext)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_city" ,
							"Filed_Label"=> "i3subscriber_city",
							"Filed_Text"=> "Patient Tertiary Insurance City",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_city") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_city)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_state" ,
							"Filed_Label"=> "i3subscriber_state",
							"Filed_Text"=> "Patient Tertiary Insurance State",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_state") ,
							"Old_Value"=> addcslashes(addslashes(trim($tertiaryComDetail->subscriber_state)),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_phone" ,
							"Filed_Label"=> "i3subscriber_phone",
							"Filed_Text"=> "Patient Tertiary Insurance Home Tel.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_phone") ,
							"Old_Value"=> addcslashes(addslashes(trim(core_phone_format($tertiaryComDetail->subscriber_phone))),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_biz_phone" ,
							"Filed_Label"=> "i3subscriber_biz_phone",
							"Filed_Text"=> "Patient Tertiary Insurance Work Tel.",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_biz_phone") ,
							"Old_Value"=> addcslashes(addslashes(trim(core_phone_format($tertiaryComDetail->subscriber_biz_phone))),"\0..\37!@\177..\377")																					
						);
$arrAuditTrailTer [] = array(							
							"Ins_Type"=> "tertiary",																
							"Data_Base_Field_Name"=> "subscriber_mobile" ,
							"Filed_Label"=> "i3subscriber_mobile",
							"Filed_Text"=> "Patient Tertiary Insurance Mobile",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"subscriber_mobile") ,
							"Old_Value"=> addcslashes(addslashes(trim(core_phone_format($tertiaryComDetail->subscriber_mobile))),"\0..\37!@\177..\377")																					
						);
?>