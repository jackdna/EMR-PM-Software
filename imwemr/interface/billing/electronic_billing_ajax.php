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

?><?php
/*
File: electronic_billing_ajax.php
Purpose: Received all requests made from Electronic Billing Main interface.
Access Type: Direct Access (in ajax requests) 
*/
set_time_limit(600);
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
require_once(dirname(__FILE__).'/../../library/classes/billing_functions.php');
$objEBilling = new ElectronicBilling();
$do = isset($_REQUEST['eb_task']) ? trim($_REQUEST['eb_task']) : '';
$setFileStatus = ($do=='Delete') ? '1' : (($do=='Archive') ? '2' : (($do=='Un-Archive') ? '0' : ''));
switch($do){
	case 'Archive':
	case 'Delete':
	case 'Un-Archive':
		if($setFileStatus != ''){
			$fileIDs = isset($_REQUEST['fileIDs']) ? trim($_REQUEST['fileIDs']) : '';
			$resp = $objEBilling->set_file_status($fileIDs,intval($setFileStatus));
			echo json_encode($resp);
		}
		break;
	case 'Re-Generate':
		$fileIDs = isset($_REQUEST['fileIDs']) ? trim($_REQUEST['fileIDs']) : '';
		$bType = isset($_REQUEST['bType']) ? trim($_REQUEST['bType']) : '';
		$resp = $objEBilling->regenerate_file($fileIDs,$bType);
		echo json_encode($resp);
		break;
	case 'batch_list':
		$PayerClearingHouseWise = $objEBilling->getPayersClearingHouseWise();
		$fs 		= isset($_REQUEST['filestatus']) ? intval($_REQUEST['filestatus']) : '0';
		$insCompIDs = isset($_REQUEST['insCompIDs']) ? trim($_REQUEST['insCompIDs']) : '';
		$insCompIDs	= isset($PayerClearingHouseWise[$insCompIDs]) ? $PayerClearingHouseWise[$insCompIDs] : '';
		$bypatient 	= isset($_REQUEST['bypatient']) ? trim($_REQUEST['bypatient']) : '';
		$result_array = $objEBilling->get_batch_list($fs,$insCompIDs,$bypatient);
		//echo $result_array;
		echo json_encode($result_array);
		break;
	case 'ProcessClaims':
		$PayerClearingHouseWise = $objEBilling->getPayersClearingHouseWise();
		$insCompIDs = isset($_REQUEST['instype']) ? trim($_REQUEST['instype']) : '';
		$_REQUEST['instype']	= $PayerClearingHouseWise[$insCompIDs];
		//$_REQUEST['ProcessClaims'] = $_COOKIE['ClaimsToBatch'];
		$_COOKIE['ClaimsToBatch'] = '';
		unset($_COOKIE['ClaimsToBatch']);
		$objEBilling->process_claims($_REQUEST);
		//echo json_encode($_POST);
		break;
	case 'getEBReports':
		ini_set("memory_limit","1024M");
		$st					= isset($_REQUEST['st']) ? intval($_REQUEST['st']) : '0';
		if($st>0){$st 	= ($st*$upto)-$upto;}
		$upto				= isset($_REQUEST['upto']) ? intval($_REQUEST['upto']) : '10';
		$EmdGroupSelVal 	= isset($_REQUEST['EmdGroupSelVal']) ? trim($_REQUEST['EmdGroupSelVal']) : '';
		$getReportsEmdeon 	= $objEBilling->getDBReportsEmdeon($st,$upto,$EmdGroupSelVal);
		/******GETTING GROUP DETAILS*********/
		$groupsDataArr		= $objEBilling->get_groups_detail();
		$groupArr 			= array();
		$i = 0;
		foreach($groupsDataArr as $groupRS){
			$groupArr[$i]['grp_id'] = $groupRS['gro_id'];
			$groupArr[$i]['grp_name'] = $groupRS['name'];
			$i++;
		}
		
		$returnArray = array('GroupData'=>$groupArr,'EmdReports'=>$getReportsEmdeon);
		echo json_encode($returnArray);
		break;
	case 'getEBReportsVS':
		$st					= isset($_REQUEST['st']) ? intval($_REQUEST['st']) : '0'; $st 	= ($st*$upto)-$upto;
		$upto				= isset($_REQUEST['upto']) ? intval($_REQUEST['upto']) : '10';
		$VisGroupSelVal 	= isset($_REQUEST['VisGroupSelVal']) ? trim($_REQUEST['VisGroupSelVal']) : '';
		$getReportsVision	= $objEBilling->getDBReportsVision($st,$upto,$VisGroupSelVal);
		
		/******GETTING GROUP DETAILS*********/
		$groupsDataArr		= $objEBilling->get_groups_detail();
		$groupArr 			= array();
		$i = 0;
		foreach($groupsDataArr as $groupRS){
			$groupArr[$i]['grp_id'] = $groupRS['gro_id'];
			$groupArr[$i]['grp_name'] = $groupRS['name'];
			$i++;
		}//pre($getReportsVision);
		$returnArray = array('GroupData'=>$groupArr,'VisReports'=>$getReportsVision);
		echo json_encode($returnArray);		
		break;
	case 'emd_reports_del':
	case 'vis_reports_del':
		$fileIDs = isset($_REQUEST['fileIDs']) ? trim($_REQUEST['fileIDs']) : '';
		$resp = $objEBilling->delete_reports($do,$fileIDs);
		echo $resp;
		break;
	case 'emd_reports_get':
		$CL_res	= $objEBilling->ClearingHouse();
		$CL		= $CL_res[0]['abbr'];
		if($CL == 'PI') require_once(dirname('__FILE__')."/get_reports_pi.php");
		else require_once(dirname('__FILE__')."/get_reports_emdeon.php");
		break;
	case 'vis_reports_get':
		require_once(dirname('__FILE__')."/get_reports_vision.php");		
		break;
	case 'get_batch_file_log':
		if(isset($_GET['showbatchhx']) && trim($_GET['showbatchhx'])=='yes' && intval($_GET['batchfileId'])>0){
			echo $objEBilling->show_batch_file_log(intval($_GET['batchfileId']));
		}
		break;
	default: 

}

?>
