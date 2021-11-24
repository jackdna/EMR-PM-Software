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
require_once(dirname(__FILE__).'/../../config/globals.php');
//require_once($GLOBALS['fileroot'].'/library/classes/billing_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/class.electronic_billing.php');

$objEBilling = new ElectronicBilling();
set_time_limit(0);

$objEBilling = new ElectronicBilling();
$soapUser = 'MWI_IMEDIC1';
$soapPass = '9Qy5DeVv';


$pre_auth_str 		= isset($_GET['pre_auth_str']) ? trim($_GET['pre_auth_str']) : 0;
$pre_auth_arr		= explode('~~',$pre_auth_str);
if(count($pre_auth_arr)==0){die('Required data missing.');}
$sch_id				= $pre_auth_arr[0];
$patient_id			= $pre_auth_arr[1];
$ptPriPhy			= $pre_auth_arr[2];
$ptGroupId			= $pre_auth_arr[3];
$ptInsDataId		= $pre_auth_arr[4];
$onsetDate			= $pre_auth_arr[5];
$template_id		= (isset($_REQUEST['template_id']) && trim($_REQUEST['template_id'])!='') ? intval($_REQUEST['template_id']) : 0;

$template_q				= "SELECT medical_type, dx_codes FROM pre_auth_templates WHERE id = '$template_id' LIMIT 0,1";
$template_res			= imw_query($template_q);
$template_rs			= imw_fetch_assoc($template_res);
$db_service_type		= trim($template_rs['medical_type']) != '' ? trim($template_rs['medical_type']) : 1;
$db_diagnosis			= $template_rs['dx_codes'];

//DIAGNOSIS ARRAY. (from posted values, or exploded from template record)
$ptDiagnosis_arr		= array(); //array('366.53','375.15','362.51','367.4');
if($db_diagnosis != '')	{
	$ptDiagnosis_arr = unserialize(html_entity_decode($db_diagnosis));
	$ptDiagnosis_arr = array_filter($ptDiagnosis_arr);	
}

//Line Item (SV1, SV2) details (from posted values, or exploded from template record)
$cpt_q					= "SELECT * FROM pre_auth_templates_details WHERE pre_auth_id = '$template_id'";
$cpt_res				= imw_query($cpt_q);
$LineItem				= array();
$LineItemCnt			= 1;
if($cpt_res && imw_num_rows($cpt_res) > 0){
	while($cpt_rs = imw_fetch_assoc($cpt_res)){
		$LineItem[$LineItemCnt]['cpt']		= $cpt_rs['procedure_name'];
		$LineItem[$LineItemCnt]['rev']		= $cpt_rs['proc_code'];
		$LineItem[$LineItemCnt]['mod']		= $cpt_rs['proc_code'];
		$LineItem[$LineItemCnt]['unit']		= $cpt_rs['unit'];
		$LineItem[$LineItemCnt]['amount']	= $cpt_rs['proc_code'];
		$LineItem[$LineItemCnt]['dx']		= $cpt_rs['diagnosis'];
		$LineItemCnt++;
	}
}

//ASSIGNING DEFAULT VALUES FOR FUNCTION TO CREATE EDI. 
$objEBilling->patientId				= $patient_id;
$objEBilling->groupId				= $ptGroupId;
$objEBilling->priPhysician			= $ptPriPhy;
$objEBilling->priPayer				= '';//$ptPriPayer;//this will be over written by INs.Data ID payer.
$objEBilling->InsDataId				= $ptInsDataId; 
$objEBilling->reviewServiceType		= $db_service_type;//1 //demo value, may code from template.
$objEBilling->onSetDate				= $objEBilling->date_for_db($onsetDate);
$objEBilling->ptDiagArr				= $ptDiagnosis_arr;
$objEBilling->LineItemArr			= $LineItem;

$now		= date('Y-m-d H:i:s');
$EDI278Data = $objEBilling->make278EDI();
//pre($EDI278Data); die('<br>----end----');
//$EDI278Data['error'] 	= '';
//$EDI278Data['response']	= 'ISA*00*          *00*          *ZZ*16515462       *ZZ*EMDEON         *20141118*0654*^*00501*000000005*0*P*:~GS*HI*16515462*EMDEON*20141118*0654*000000005*X*005010X217~ST*278*0005*005010X217~BHT*0010*13*500005*20141118*0654~HL*1**20*1~NM1*X3*2*UNITED HEALTHCARE*****PI*00112~HL*2*1*21*1~NM1*1P*2*SHORE EYE ASSOCIATES  PA*****XX*208326251~N3*530 LAKEHURST ROAD~N4*TOMS RIVER*NJ*087558063~PER*IC*DEBBIE FRENVILLE*TE*7323414733~HL*3*0*22*0~NM1*IL*1*BALCERZAK*RICHARD*S***MI*832381267~REF*SY*142407092~UM*HS*I*1~DTP*431*D8*20141022~HI*BK:36201*BF:37251*BF:36616*BF:36615~SV1*HC:92014*136.54*UN*1*11**1:2:3:4~SV1*HC:92250*87.48*UN*1*11**1~SV1*HC:2021F*0.01*UN*1*11**1~NM1*SJ*1*WNOROWSKI*BRIAN****XX*1780669101~PRV*PE*PXC*152W00000X~SE*21*000000005~GE*1*5~IEA*1*000000005~';

if($EDI278Data['error']==''){
	//TEST
	//$soapClient = new SoapClient("../../tmp/emdeon276demo/emdeon_wsdl_test.wsdl");
	//$param = array("sUserID" => "ITSTCS_IMEDIC3","sPassword" => "6943u3ze");
	
	//PRODUCTION
	$soapClient = new SoapClient($GLOBALS['fileroot']."/library/classes/emdeon_wsdl_production.wsdl");
	$param = array("sUserID" => $soapUser,"sPassword" => $soapPass);
	
	//CHECKING AUTHORIZATION
	$authResult = $soapClient->Authenticate($param);
	//pre($authResult);
	
	$param = array("sUserID" => $soapUser,
					"sPassword" => $soapPass,
					"sMessageType" => "X12",
					"sEncodedRequest" => base64_encode($EDI278Data['response'])
				  );
	//pre($param,1);
	$sendRq = $soapClient->SendRequest($param);
	//pre($sendRq);

	$arrSendRq = get_object_vars($sendRq);
	$arrSendRequestResult = get_object_vars($arrSendRq["SendRequestResult"]);
	//pre($arrSendRequestResult);		
	$ErrorCode =  $arrSendRequestResult["ErrorCode"];
	if(intval($ErrorCode)>0 && is_numeric($ErrorCode)){
		$responsetext = $ErrorCode.': '.$arrSendRequestResult["Response"];
	}else{
		$ErrorCode='';
		$now2			= date('Y-m-d H:i:s');
		$edi278Response	= base64_decode($arrSendRequestResult["Response"]);
		$response		= $objEBilling->read278EDI($edi278Response);
		$responsetext	= $objEBilling->responseArray2Text($response);
		
		$insert_q = "INSERT INTO claim_pre_auth 
				SET patient_id 			= '$patient_id', 
				ins_data_id				= '$ptInsDataId',
				ins_comp_id				= '$ptPriPayer',
				provider_id				= '$ptPriPhy',
				group_id				= '$ptGroupId',
				sch_id					= '".$sch_id."', 
				template_id				= '".$template_id."', 
				pt_sub_rel				= '',
				diagnosis				= '".implode(',',$ptDiagnosis_arr)."',
				procedures				= '',
				request_datetime		= '".$now."',
				request_data			= '".addslashes($EDI278Data['response'])."',
				request_by				= '".$_SESSION['authId']."',
				request_from			= '2',
				response_data			= '".addslashes($edi278Response)."',
				response_datetime		= '".$now2."',
				response_status			= '".addslashes($responsetext)."',
				response_details		= '".addslashes(htmlentities(serialize($response)))."',
				delete_status			= '0',
				authorization_number 	= ''
				";
		$insert_res = imw_query($insert_q);
		echo $responsetext.'<br>';
		if(!$insert_res){echo imw_error();}
	}
}else{
	echo '<div class="warning"><b>Error: </b>'. $EDI278Data['error'].'</div><br><br>';
}
sleep(1);
?>