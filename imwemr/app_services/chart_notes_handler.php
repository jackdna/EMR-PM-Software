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
/*
File: chart_notes_handler.php
Purpose: This file provides web services for ipad app.
Access Type : Direct
*/
?>
<?php 
/*require_once "inc_classes/chart_notes.php";
$authId = $_REQUEST['phyId'];
$serviceObj = new chart_notes($_REQUEST['patId']);
$serviceObj->reqModule = $reqModule;
$servicesArr = explode(",",$_REQUEST["service"]);
foreach($servicesArr as $key=>$service){
	if(method_exists($serviceObj, trim($service))){
		$responseArray[$service] = call_user_func(array($serviceObj, trim($service)));
	}
	else{
		$responseArray[$service] = "NO SCHEDULER SERVICE EXISTS";	
	}	
}
if($_REQUEST['pre'])
pre($responseArray);
echo json_encode($responseArray);	
die();*/
//require_once "../interface/globals.php";
include_once("../config/globals.php");

define("PTHISTORY", "The Patient has a history of");
$serviceList = trim($_REQUEST["service"]);	
$arrService = explode(",",$serviceList);

foreach($arrService as $service){
	$servicesAr = array();
	$_REQUEST["service"] = $service;
	switch($service)
	{
		case "pvc":
			require_once "inc_classes/pvc.php";
			$authId = $_REQUEST['phyId'];
			$serviceObj = new pvc($_REQUEST['patId']);
			$serviceObj->reqModule = $reqModule;
			$servicesArr = explode(",",$_REQUEST["action"]);
		break;
		case "get_cc_hx_ocular":
			require_once "inc_classes/chart_cc_hx_ocular.php";
			$authId = $_REQUEST['phyId'];
			$serviceObj = new chart_cc_hx_ocular($_REQUEST['patId'],$_REQUEST['form_id']);
			$serviceObj->reqModule = $reqModule;
			$servicesArr = explode(",",$_REQUEST["service"]);
			break;
			//new for app //
			case "get_cc_hx_ocular_app":
			require_once "inc_classes/chart_cc_hx_ocular_app.php";
			$authId = $_REQUEST['phyId'];
			$serviceObj = new chart_cc_hx_ocular_app($_REQUEST['patId'],$_REQUEST['form_id']);
			$serviceObj->reqModule = $reqModule;
			$servicesArr = explode(",",$_REQUEST["service"]);
			break;
			
			
		case "patient_erx_history":
			require_once "inc_classes/patient_erx_data.php";
			$serviceObj = new patient_erx_data();
			$serviceObj->reqModule = $reqModule;
			$servicesArr = explode(",",$_REQUEST["action"]);
			break;	
		case "RFS":
			require_once "inc_classes/RFS.php";
			$serviceObj = new RFS();
			$serviceObj->reqModule = $reqModule;
			$servicesArr = explode(",",$_REQUEST["action"]);
			break;	
		case "PFS":
			require_once "inc_classes/PFS.php";
			$serviceObj = new PFS();
			$serviceObj->reqModule = $reqModule;
			$servicesArr = explode(",",$_REQUEST["action"]);
			break;	
		case "GFS":
			require_once "inc_classes/GFS.php";
			$serviceObj = new GFS();
			$serviceObj->reqModule = $reqModule;
			$servicesArr = explode(",",$_REQUEST["action"]);
			break;			
		case "get_allergies":
		case "get_pt_notes":
		case "get_ocular_pt_notes":
		case "save_pt_notes":
			require_once "inc_classes/chart_notes.php";
			$authId = $_REQUEST['phyId'];
			$serviceObj = new chart_notes($_REQUEST['patId'],$_REQUEST['form_id']);
			$serviceObj->reqModule = $reqModule;
			$servicesArr = explode(",",$_REQUEST["service"]);
		break;
		
		case "PtForms":///imwemr-Dev/app_services/?reqModule=chart_notes&service=PtForms&phyId=<USERID>&patId=<PATIENTID>		
			require_once("inc_classes/patient_app.php");		
			$authId = $_REQUEST['phyId'];
			$serviceObj = new patient_app($_REQUEST['patId']);			
			$responseArray[$_REQUEST["service"]] = $serviceObj->getPtForms();						
		break;
		
		case "getWVSummery":///imwemr/app_services/?reqModule=chart_notes&service=getWVSummery&phyId=1&patId=66656&form_id=57866&cwidth=200px
			case "getWVSummery_app": // new function for app
			require_once "inc_classes/GetWVSummery.php";		
			
			$_SESSION["authId"] = $authId = $_REQUEST['phyId'];				
			$serviceObj = new GetWVSummery($_REQUEST['patId'],$_REQUEST['form_id']);		
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);
		break;
		case "getWVDrawing": ///imwemr/app_services/?reqModule=chart_notes&service=getWVDrawing&phyId=1&patId=66655&form_id=57874
			require_once "inc_classes/GetWVSummery.php";		
			
			$_SESSION["authId"] = $authId = $_REQUEST['phyId'];				
			$serviceObj = new GetWVSummery($_REQUEST['patId'],$_REQUEST['form_id']);		
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);
		break;
		case "getAmsGrdCVF": ///imwemr/app_services/?reqModule=chart_notes&service=getAmsGrdCVF&phyId=1&patId=66655&form_id=57874
			require_once "inc_classes/GetWVSummery.php";		
			
			$_SESSION["authId"] = $authId = $_REQUEST['phyId'];				
			$serviceObj = new GetWVSummery($_REQUEST['patId'],$_REQUEST['form_id']);		
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);
		break;
		
		case "getSuperBill": ///imwemr/app_services/?reqModule=chart_notes&service=getSuperBill&phyId=1&patId=66655&form_id=57892 
			require_once "inc_classes/GetSuperBill.php";
			
			$_SESSION["authId"] = $authId = $_REQUEST['phyId'];				
			$serviceObj = new GetSuperBill($_REQUEST['patId'],$_REQUEST['form_id']);
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);		
		break;
		
		case "getAssessPlan": ///imwemr/app_services/?reqModule=chart_notes&service=getAssessPlan&phyId=1&patId=66670&form_id=57904
			require_once "inc_classes/AssessPlan.php";
			//require_once $GLOBALS["incdir"]."/chart_notes/common/ChartAP.php";
			
			$_SESSION["authId"] = $authId = $_REQUEST['phyId'];
			$serviceObj = new AssessPlan($_REQUEST['patId'],$_REQUEST['form_id']);
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);		
		break;
		case "getVision_app": // new case for vision data for app// 
		case "getVision": ///imwemr/app_services/?reqModule=chart_notes&service=getVision&phyId=1&patId=66650&form_id=57916		
			require_once "inc_classes/VisionData.php";		
			$_SESSION["authId"] = $authId = $_REQUEST['phyId'];
			$serviceObj = new VisionData($_REQUEST['patId'],$_REQUEST['form_id']);
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);		
		break;
		case "finalizeChartNote": ///imwemr/app_services/?reqModule=chart_notes&service=finalizeChartNote&phyId=1&patId=1&form_id=2
			//require_once $GLOBALS['srcdir']."/library/classes/work_view/ChartNote.php";
			include_once($GLOBALS['fileroot']."/library/classes/work_view/ChartNote.php");
			$authId = $_REQUEST['phyId'];
			$_SESSION["authId"]=$authId;	
			$serviceObj = new ChartNote($_REQUEST['patId'],$_REQUEST['form_id']);
			$serviceObj->reqModule = $reqModule;
			$servicesArr = explode(",",$_REQUEST["service"]);
		break;
		case "listChartNotes_app":  // new case for vision data for app//
		case "listChartNotes": ///imwemr/app_services/?reqModule=chart_notes&service=listChartNotes&patId=66656
			require_once "inc_classes/ListChartNotes.php";		
			$serviceObj = new ListChartNotes($_REQUEST['patId']);
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);		
		break;
		case "get_chart_print":
		break;
		case "get_future_appointments":
			require_once "inc_classes/future_appointments.php";	
			$serviceObj = new future_appointment($_REQUEST['patId'],$_REQUEST['form_id']);
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);
		break;
		case "procedures":
			require_once "inc_classes/procedures.php";	
			$serviceObj = new procedures($_REQUEST['patId'],$_REQUEST['form_id']);
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["action"]);
		break;
		case "getRVS":
			require_once "inc_classes/RVS.php";				
			$serviceObj = new RVS($_REQUEST['patId'],$_REQUEST['form_id']);			
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);
		break;
		case "getOculerSx":
			require_once "inc_classes/PtSurgery.php";				
			$serviceObj = new PtSurgery($_REQUEST['patId']);
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);
		break;
		case "getWVSignatures":///imwemr/app_services/?reqModule=chart_notes&service=getWVSignatures&phyId=1&patId=66656&form_id=57866&cwidth=200px
			require_once "inc_classes/WVSignatures.php";		
			
			$_SESSION["authId"] = $authId = $_REQUEST['phyId'];				
			$serviceObj = new WVSignatures($_REQUEST['patId'],$_REQUEST['form_id']);		
			$serviceObj->reqModule = $reqModule;
			$serviceObj->ar_req = $_REQUEST;
			$servicesArr = explode(",",$_REQUEST["service"]);
		break;
	

		default:
			//No Service exits
			exit("NO SCHEDULER SERVICE EXISTS");
		break;		
		
	}

	if(count($servicesArr)>0){
	foreach($servicesArr as $key=>$service){
		if(method_exists($serviceObj, trim($service))){
			$responseArray[$service] = call_user_func(array($serviceObj, trim($service)));
							//-----------start of new changes-------------------
				  if($_REQUEST['app']=='android'){
				   
				   
							   if(empty($responseArray[trim($service)])){
								$responseArray =array_merge(array("Data_status"=>0) ,$responseArray );
							   }
							   else {
								$responseArray =array_merge(array("Data_status"=>1) ,$responseArray );
							   }
				   
				  }
		  //-----------end of new changes-------------------
		}
		else{
			$responseArray[$service] = "NO SCHEDULER SERVICE EXISTS";	
		}	
	}
	}
}
//=====KEY WAS GETTING EMPTY DEFAULT======
if(empty($responseArray['getSuperBill']))
{
	$responseArray['getSuperBill'] = array();	
} 
//========================================

if($_REQUEST['pre'])
pre($responseArray);
//print_r($responseArray);
echo json_encode($responseArray);
	
?>