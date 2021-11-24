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
File: make_270_edi.php
Purpose: 270 functionality implemented
Access Type: Include 
*/
require_once("../../../config/globals.php");
set_time_limit(180);
require_once("../../../library/classes/cls_common_function.php");	
require_once("../../../library/classes/class.language.php");
require_once("../../../library/classes/class.electronic_billing.php");
$OBJCommonFunction = new CLSCommonFunction;
$objCoreLang = new core_lang;
$objEBilling = new ElectronicBilling();

$ClearingHouse	= $objEBilling->ClearingHouse();
$CL		 		= $ClearingHouse[0]['abbr'];
$CL_mode 		= $ClearingHouse[0]['connect_mode'];
$CL_url			= ($CL_mode=='T') ? $ClearingHouse[0]['test_url'] : $ClearingHouse[0]['prod_url'];


$_REQUEST = array_map('xss_rem',$_REQUEST);
extract($_REQUEST);

$data = ''; $return = array('action'=>$action,'data'=>'');
$insRecId = (int)$insRecId;
$askElFrom = (int)$askElFrom;
$schId =	(isset($schId) && $schId) ? (int)$schId : 0 ;
$strAppDate =	(isset($strAppDate) && $strAppDate) ? $strAppDate : '' ;

function RemoveUnwantedChars($Str) {  
  $StrArr = str_split($Str); $NewStr = '';
  foreach ($StrArr as $Char) {    
    $CharNo = ord($Char);
    if ($CharNo == 163) { $NewStr .= $Char; continue; } // keep £ 
    if ($CharNo > 31 && $CharNo < 127) {
      $NewStr .= $Char;    
    }
  }  
  return $NewStr;
}
		
if($insRecId)
{
	/*****CHECKING IF RTE ENABLED FOR THIS PAYER***/
	$rte_check_flag = 1;
	/*$query = "SELECT icc.rte_chk FROM insurance_companies icc JOIN insurance_data idd ON (icc.id = idd.provider) 
							WHERE idd.id = '".$insRecId."'";
	$sql= imw_query($query); $data = imw_error();
	$cnt = imw_num_rows($sql);
	if($cnt == 1)
	{
		$row = imw_fetch_assoc($sql);
		$rte_check_flag = (int) $row['rte_chk'];
	}*/
	
	if($rte_check_flag)
	{
		
		if($askElFrom == 0)
		{
			require_once(dirname(__FILE__).'/../../billing/CLSVSDemo.php');
			$objVsDemo = new CLSVSDemo;
			
			$EDI270Request = $dbInsPatId = $dbInsId = $imedicDOS = $strShowMsg = "";
			$groupID = $intElId = 0;
			$retError = $retErrorAns = array();
			list($EDI270Request,$dbInsPatId,$dbInsId,$imedicDOS,$groupID,$retError)=$objVsDemo->make270EDI5010($insRecId, "insTab", 0, $strAppDate);
					
			if(count($retError) == 0)
			{	
					if(empty($EDI270Request) == false)
					{
						$EDI270Responce = $objVsDemo->send270Request5010($EDI270Request, $dbInsPatId, $dbInsId, $imedicDOS, $groupID, $schId);
						if(is_array($EDI270Responce) == true)
						{
							$strEBResp = $strEBInsuranceTypeCodeResp = "";
							$strEBResponce = $strEBInsuranceTypeCode = "";
							list($strEBResp, $strEBInsuranceTypeCodeResp, $intNewId, $strShowMsg) = $EDI270Responce;
							$strEBResp = (string)$strEBResp;
							$elStatus = "";
							if(($strEBResp != "6") && ($strEBResp != "7") && ($strEBResp != "8") && ($strEBResp != "V"))
							{
								$elStatus = "A";
							}
							else
							{
								$elStatus = "INA";
							}
							$strEBResponce = $objCoreLang->get_vocabulary("vision_share_271", "EB", (string)$strEBResp);
							$strEBInsuranceTypeCode = $objCoreLang->get_vocabulary("vision_share_271", "Insurance_Type_Code", (string)$strEBInsuranceTypeCodeResp);
						
							$data = "1"."~~".$strEBResponce."~~".$strEBInsuranceTypeCode."~~".$elStatus."~~".$intElId."~~".$strShowMsg;
						}
						else{
							$data = "2"."~~".$EDI270Responce;
						}
					}
					else
					{
						$data = "Patient Insurance Data is not proper for Eligibility Checking!";
					}
			}
			else
			{
				$strError = "";
				foreach($retError as $intKey => $strVal){
					$strError .= $strVal."\n";
				}
				$data = "Data is not proper for Eligibility Checking during Request!\n".$strError;
			}
		}
		
		elseif($askElFrom == 1)
		{	
			require_once(dirname(__FILE__).'/../../../library/classes/class.cls_eligibility.php');
			$objEligibility = new CLSEligibility("commercial");
			if($objEligibility->SOAPClientRec !== false || $CL=='PI')
			{
				$EDI270Request = $dbInsPatId = $dbInsId = $imedicDOS = "";	
				$strEBResponce = $strEBInsuranceTypeCode = $strShowMsg = "";
				$blTranError = false;
				$groupID = $intElId = 0;
				$retError = $retErrorAns = array();
				if($CL=='PI'){
					list($EDI270Request, $dbInsPatId, $dbInsId, $imedicDOS, $groupID, $retError,$dbInsDataTypePriSec)= $pireq = $objEBilling->makePI270request($insRecId, "insTab", 0, $strAppDate);
					//pre($pireq,1);
				}else{
					list($EDI270Request, $dbInsPatId, $dbInsId, $imedicDOS, $groupID, $retError,$dbInsDataTypePriSec) = $objEligibility->make270EDIEmdeon5010($insRecId, "insTab", 0, $strAppDate);
				}
				if(count($retError) == 0)
				{
					if(empty($EDI270Request) == false)
					{
						$schId=($schId)?$schId:$objEligibility->pt_today_appt($dbInsPatId);
						if($CL=='PI'){
							list($strEBResponce, $strEBInsuranceTypeCode, $retErrorAns, $blTranError, $intElId, $strShowMsg) = $pires = $objEligibility->send270RequestEmdeon5010($EDI270Request, $dbInsPatId, $dbInsId, $imedicDOS, $groupID, $schId,$CL_url,$dbInsDataTypePriSec);
							//pre($pires,1);
						}else{
							list($strEBResponce, $strEBInsuranceTypeCode, $retErrorAns, $blTranError, $intElId, $strShowMsg) = $objEligibility->send270RequestEmdeon5010($EDI270Request, $dbInsPatId, $dbInsId, $imedicDOS, $groupID, $schId,'',$dbInsDataTypePriSec);
						}
						if(count($retErrorAns) == 0)
						{
							$strEBResponce = (string)$strEBResponce;
							$elStatus = "";
							if(($strEBResponce != "6") && ($strEBResponce != "7") && ($strEBResponce != "8") && ($strEBResponce != "V")){
								$elStatus = "A";
							}
							else{
								$elStatus = "INA";
							}
							if($strEBResponce!=''){
								$strEBResponce = $objCoreLang->get_vocabulary("vision_share_271", "EB", (string)$strEBResponce);
							}else{
								$strEBResponce= 'No response from clearing house.';
							}
							$strEBInsuranceTypeCode = $objCoreLang->get_vocabulary("vision_share_271", "Insurance_Type_Code", (string)$strEBInsuranceTypeCode);
							$strEBInsuranceTypeCode = (is_string($strEBInsuranceTypeCode) ? $strEBInsuranceTypeCode : "");
							
							$data = "1"."~~".$strEBResponce."~~".$strEBInsuranceTypeCode."~~".$elStatus."~~".$intElId."~~".$strShowMsg;
						}
						else
						{
							$strTranError = "";
							foreach($retErrorAns as $intKey => $strVal){
								$strVal = RemoveUnwantedChars($strVal);
								$strTranError .= $strVal."\n";
							}
							if($blTranError == false || count($retErrorAns)>0){
								$data = "Transaction failed during response!\n".$strTranError;
							}
							else{
								$data = "2"."~~"."Transaction failed during response!\n".$strTranError;
							}
						}
					}
					else{
						$data = "Patient Insurance Data is not proper for Eligibility Checking!";
					}
				}
				else
				{
					$strError = "";
					foreach($retError as $intKey => $strVal){
						$strError .= $strVal."\n";
					}
					$data = "Data is not proper for Eligibility Checking during Request!\n".$strError;
				}
			}
			else
			{
				$data = "Server Error: No WSDL Found!";
			}
		}	

	}else{
		$data = 'This feature is not enabled for this Payer.';
	}
}
$return['data'] = $data;
echo json_encode($return);
?>