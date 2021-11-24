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
File: get_reports_vision.php
Purpose: Download reports from Ability (vision share)
Access Type: Include File
*/
set_time_limit(0);
require_once(dirname(__FILE__).'/../globals.php');
require_once(dirname(__FILE__).'/../common/functions.inc.php');
require_once(dirname(__FILE__).'/../common/CLSCommonFunction.php');
require_once(dirname(__FILE__).'/billing_globals.php');
$objManageData = new DataManage;

$group_details = $objManageData->__getGroupsDetails();
$group_data = array();
$arrGroupId = $arrGroupName = $arrGroupUFC = array();
for($i=0; $i <= count($group_details); $i++){	
	$gro_id = 0;
	$name = $strGroupUFC = "";
	
	if($i > 0){
		$gro_id = $group_details[$i-1]['gro_id'];
		$name = $group_details[$i-1]['name'];	
		
		$group_data['group_id'][$i] = $gro_id;
		$arrGroupId[] = $gro_id;
		$group_data['group_name'][$i] = $name;
		$arrGroupName[] = $name;
		$groupNameArr = $arrGroupUCF = array();
		$groupNameArr = preg_split('/ /',$name);	
		for($s = 0;$s < count($groupNameArr); $s++){
			$arrGroupUCF[] = $groupNameArr[$s][0];
		}
		$strGroupUFC = join('',$arrGroupUCF);
		$group_data['groupUCF'][$i] = $strGroupUFC;
		$arrGroupUFC[] = $strGroupUFC;
	}
	else{
		$group_data['group_id'][0] = 0;
		$group_data['group_name'][0] = "All";
		$group_data['groupUCF'][0] = "";
	}
}
$group_data['group_id'][0] = implode(",", $arrGroupId);
$group_data['groupUCF'][0] = implode(",", $arrGroupUFC);
	
//---- GET REPORTS FROM Vision Share -----
$arrCertUse = array();
$strCertUse = "";
require_once(dirname(__FILE__).'/CLSVSDemo.php');
$objVsDemo = new CLSVSDemo;
$arrAllCert = array();
foreach($objVsDemo->arrCertInfo as $intKey => $valArr){
	$arrAllCert[] = "'".$valArr["certName"]."'";
}
$strCertUse = implode(",", $arrAllCert);

$arrCertUse = array();
$arrCertUse = explode(",", $strCertUse);
foreach($arrCertUse as $intKey => $strVal){		
	$strCertUseMain = str_replace("'", "", $strVal);
	//require_once(dirname(__FILE__).'/CLSVSDemo.php');
	$serviceOutput = $realMedCareElgblID = $realMedCareElgblURI = $batchSubmitID = $batchSubmitURI = $batchReceiveListID = $batchReceiveListURI = "";
	$batchSubmitID5010 = $batchSubmitURI5010 = $batchReceiveListID5010 = $batchReceiveListURI5010 = "";
	//$objVsDemo = new CLSVSDemo;
	$serviceOutput = $objVsDemo->services($strCertUseMain);	
	$arrServices = $arrReceiveListXML = $arrVSBRLAll = array();
	$receiveListXML = "";
	$arrServices = $objVsDemo->XMLToArray($serviceOutput);

	$ArrVSIDURI = $objVsDemo->getVSIDURI($arrServices);
	$realMedCareElgblID		= $ArrVSIDURI[0];
	$realMedCareElgblURI 	= $ArrVSIDURI[1];
	$batchSubmitID			= $ArrVSIDURI[2];
	$batchSubmitURI			= $ArrVSIDURI[3];
	$batchReceiveListID		= $ArrVSIDURI[4];
	$batchReceiveListURI	= $ArrVSIDURI[5];
	$batchSubmitID5010		= $ArrVSIDURI[6];
	$batchSubmitURI5010		= $ArrVSIDURI[7];
	$batchReceiveListID5010	= $ArrVSIDURI[8];
	$batchReceiveListURI5010= $ArrVSIDURI[9];
	
	$arrBatchReceiveListURI = array($batchReceiveListURI, $batchReceiveListURI5010);
	//pre($arrBatchReceiveListURI,1);
	foreach($arrBatchReceiveListURI as $intBatchReceiveListURIKey => $strBatchReceiveListURIVal){
		if(empty($strBatchReceiveListURIVal) == false){
			$receiveListXML = $objVsDemo->getVSReceiveList($strBatchReceiveListURIVal, $strCertUseMain);
			$arrReceiveListXML = $objVsDemo->XMLToArray($receiveListXML);
			$objManageData->QUERY_STRING = "select vs_file_name, vs_file_state, vs_uuid  from vision_share_batch_receive_list order by id";
			$arrVSBRLAll = $objManageData->mysqlifetchdata();	
			//pre($arrReceiveListXML);
			//die;	
			$blGetFileName = false;	
			$pathToWriteXML = "";
			$fileName = $fileType = $fileState = $fileCreated = $fileUUID = $fileURI = "";
			foreach($arrReceiveListXML as $key => $val){
				if(is_array($val) == true){
					if((trim($val["tag"]) == "file") && (trim($val["type"]) == "open")){
						$blGetFileName = true;
					}
					elseif((trim($val["tag"]) == "file") && (trim($val["type"]) == "close")){				
						$blChkRecExit = false;
						foreach($arrVSBRLAll as $key => $val){
							if(is_array($val) == true){
								if((trim($val["vs_file_state"]) == trim($fileState))  && (trim($val["vs_uuid"]) == trim($fileUUID))){
									$blChkRecExit = true;
									//unset($arrVSBRLAll[$key]);
									break;
								}
							}
						}
						if($blChkRecExit == false){
							if(empty($pathToWriteXML) == true){
								$newVSBRLMAXID = 0;
								$objManageData->QUERY_STRING = "select max(id) as VSBRLMAXID from vision_share_batch_receive_list";		
								$arrMaxAns = $objManageData->mysqlifetchdata();
								$newVSBRLMAXID = $arrMaxAns[0]['VSBRLMAXID'] + 1;
								$fileNameXML = "vs_ReceiveListXML_".$newVSBRLMAXID;					
								$pathToWriteXML = realpath('../BatchFiles/vision_share').'/'.$fileNameXML;		
								$pathToWriteXML = $pathToWriteXML.".xml";
								file_put_contents($pathToWriteXML,$receiveListXML);		
							}
							
							$visionShareOutput = "";
							$ARRgetVSReceiveResponse = $objVsDemo->getVSReceiveResponce($fileURI, trim($strCertUseMain));
							$visionShareOutput	= $ARRgetVSReceiveResponse[0];
							$curlOutput			= $ARRgetVSReceiveResponse[1];
							
							if(strtoupper(trim($fileType)) == "UNKNOWN"){
								$visionShareOutput = $objManageData->__changeFileFormat($visionShareOutput);
								$visionShareOutput = strtoupper($visionShareOutput);
								if(strpos($visionShareOutput, "ST*835*") !== false){
									$arrVSFileName = array();
									$arrVSFileName = explode(".", $fileName);
									if(trim($arrVSFileName[0]) != "835"){
										// this action to be taken because from vision share in their file name there is no 997 or 835 indication
										$fileName = "835.".$fileName;
									}
									$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
													(batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, 
													imedic_process, del_status, vs_file_data, cert_use) 
													values 
													('".addslashes(trim($pathToWriteXML))."',
													 NOW(),
													 '".$_SESSION['authId']."',
													 '".addslashes(trim($fileName))."',
													 '".addslashes(trim($fileState))."',
													 '".addslashes(trim($fileCreated))."',
													 '".addslashes(trim($fileUUID))."',
													 '".addslashes(trim($fileURI))."',
													 '1',
													 '1',
													 '".addslashes(trim($visionShareOutput))."',
													 '".trim($strCertUseMain)."'
													)";		
									$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
									if(!$rsInsertVSBRL){
										echo "Error: ".$qryInsertVSBRL;
									}
									else{
										$intNewId = 0;
										$intNewId = mysql_insert_id();
										require(getcwd()."/get_vision_835.php");								
									}
								}
								else if(strpos($visionShareOutput, "ST*997*") !== false){
									$arrVSFileName = array();
									$arrVSFileName = explode(".", $fileName);
									if(trim($arrVSFileName[0]) != "997"){
										// this action to be taken because from vision share in their file name there is no 997 or 835 indication
										$fileName = "997.".$fileName;
									}
									$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
													(batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, cert_use) 
													values 
													('".addslashes(trim($pathToWriteXML))."',
													 NOW(),
													 '".$_SESSION['authId']."',
													 '".addslashes(trim($fileName))."',
													 '".addslashes(trim($fileState))."',
													 '".addslashes(trim($fileCreated))."',
													 '".addslashes(trim($fileUUID))."',
													 '".addslashes(trim($fileURI))."',
													 '".trim($strCertUseMain)."'
													)";		
									$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
									if(!$rsInsertVSBRL){
										echo "Error: ".$qryInsertVSBRL;
									}
								}else if(strpos($visionShareOutput, "ST*999*") !== false){
									$arrVSFileName = array();
									$arrVSFileName = explode(".", $fileName);
									if(trim($arrVSFileName[0]) != "999"){
										// this action to be taken because from vision share in their file name there is no 997 or 835 indication
										$fileName = "999.".$fileName;
									}
									$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
													(batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, cert_use) 
													values 
													('".addslashes(trim($pathToWriteXML))."',
													 NOW(),
													 '".$_SESSION['authId']."',
													 '".addslashes(trim($fileName))."',
													 '".addslashes(trim($fileState))."',
													 '".addslashes(trim($fileCreated))."',
													 '".addslashes(trim($fileUUID))."',
													 '".addslashes(trim($fileURI))."',
													 '".trim($strCertUseMain)."'
													)";		
									$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
									if(!$rsInsertVSBRL){
										echo "Error: ".$qryInsertVSBRL;
									}
								}
								else if(strpos($visionShareOutput, "ST*277*") !== false){
									$arrVSFileName = array();
									$arrVSFileName = explode(".", $fileName);
									if(trim($arrVSFileName[0]) != "277CA"){
										// this action to be taken because from vision share in their file name there is no 997 or 835 or 277CA indication
										$fileName = "277CA.".$fileName;
									}
									$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
													(imedic_process, batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, vs_file_data, cert_use) 
													values 
													('0',
														'".addslashes(trim($pathToWriteXML))."',
														NOW(),
														'".$_SESSION['authId']."',
														'".addslashes(trim($fileName))."',
														'".addslashes(trim($fileState))."',
														'".addslashes(trim($fileCreated))."',
														'".addslashes(trim($fileUUID))."',
														'".addslashes(trim($fileURI))."',
														'".addslashes(trim($visionShareOutput))."',
														'".trim($strCertUseMain)."'
													)";		
									$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
									if(!$rsInsertVSBRL){
										echo "Error: ".$qryInsertVSBRL;
									}
								}
								else{
									$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
														(batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, cert_use) 
														values 
														('".addslashes(trim($pathToWriteXML))."',
														 NOW(),
														 '".$_SESSION['authId']."',
														 '".addslashes(trim($fileName))."',
														 '".addslashes(trim($fileState))."',
														 '".addslashes(trim($fileCreated))."',
														 '".addslashes(trim($fileUUID))."',
														 '".addslashes(trim($fileURI))."',
														 '".trim($strCertUseMain)."'
														)";
									$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
									if(!$rsInsertVSBRL){
										echo "Error: ".$qryInsertVSBRL;
									}
								}
							}
							else{						
								if(strtoupper(trim($fileType)) == "X12_835"){	
									$visionShareOutput = "";
									$ArrgetVSReceiveResponse = $objVsDemo->getVSReceiveResponce($fileURI, trim($strCertUseMain));
									$visionShareOutput	 = $ArrgetVSReceiveResponse[0];
									$curlOutput			 = $ArrgetVSReceiveResponse[1];
									$visionShareOutput = $objManageData->__changeFileFormat($visionShareOutput);
									if(trim($visionShareOutput) != ""){
										$arrVSFileName = array();
										$arrVSFileName = explode(".", $fileName);
										if(trim($arrVSFileName[0]) != "835"){
											// this action to be taken because from vision share in their file name there is no 997 or 835 indication
											$fileName = "835.".$fileName;
										}
										$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
														(batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, 
														imedic_process, del_status, vs_file_data, cert_use) 
														values 
														('".addslashes(trim($pathToWriteXML))."',
														 NOW(),
														 '".$_SESSION['authId']."',
														 '".addslashes(trim($fileName))."',
														 '".addslashes(trim($fileState))."',
														 '".addslashes(trim($fileCreated))."',
														 '".addslashes(trim($fileUUID))."',
														 '".addslashes(trim($fileURI))."',
														 '1',
														 '1',
														 '".addslashes(trim($visionShareOutput))."',
														 '".trim($strCertUseMain)."'
														)";		
										$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
										if(!$rsInsertVSBRL){
											echo "Error: ".$qryInsertVSBRL;
										}
										else{
											$intNewId = 0;
											$intNewId = mysql_insert_id();
											require(getcwd()."/get_vision_835.php");								
										}
									}
								}
								elseif(strtoupper(trim($fileType)) == "X12_997"){	
									$arrVSFileName = array();
									$arrVSFileName = explode(".", $fileName);
									if(trim($arrVSFileName[0]) != "997"){
										// this action to be taken because from vision share in their file name there is no 997 or 835 indication
										$fileName = "997.".$fileName;
									}							
									$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
														(batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, cert_use) 
														values 
														('".addslashes(trim($pathToWriteXML))."',
														 NOW(),
														 '".$_SESSION['authId']."',
														 '".addslashes(trim($fileName))."',
														 '".addslashes(trim($fileState))."',
														 '".addslashes(trim($fileCreated))."',
														 '".addslashes(trim($fileUUID))."',
														 '".addslashes(trim($fileURI))."',
														 '".trim($strCertUseMain)."'
														)";
									$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
									if(!$rsInsertVSBRL){
										echo "Error: ".$qryInsertVSBRL;
									}
								}
								elseif(strtoupper(trim($fileType)) == "X12_999"){	
									$arrVSFileName = array();
									$arrVSFileName = explode(".", $fileName);
									if(trim($arrVSFileName[0]) != "999"){
										// this action to be taken because from vision share in their file name there is no 997 or 835 indication
										$fileName = "999.".$fileName;
									}							
									$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
														(batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, cert_use) 
														values 
														('".addslashes(trim($pathToWriteXML))."',
														 NOW(),
														 '".$_SESSION['authId']."',
														 '".addslashes(trim($fileName))."',
														 '".addslashes(trim($fileState))."',
														 '".addslashes(trim($fileCreated))."',
														 '".addslashes(trim($fileUUID))."',
														 '".addslashes(trim($fileURI))."',
														 '".trim($strCertUseMain)."'
														)";
									$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
									if(!$rsInsertVSBRL){
										echo "Error: ".$qryInsertVSBRL;
									}
								}
								elseif(strtoupper(trim($fileType)) == "X12_277CA"){	
									$arrVSFileName = array();
									$arrVSFileName = explode(".", $fileName);
									if(trim($arrVSFileName[0]) != "277CA"){
										// this action to be taken because from vision share in their file name there is no 997 or 835 or 277CA indication
										$fileName = "277CA.".$fileName;
									}
									
									$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
													(imedic_process, batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, vs_file_data, cert_use) 
													values 
													('0',
														'".addslashes(trim($pathToWriteXML))."',
														NOW(),
														'".$_SESSION['authId']."',
														'".addslashes(trim($fileName))."',
														'".addslashes(trim($fileState))."',
														'".addslashes(trim($fileCreated))."',
														'".addslashes(trim($fileUUID))."',
														'".addslashes(trim($fileURI))."',
														'".addslashes(trim($visionShareOutput))."',
														'".trim($strCertUseMain)."'
													)";		
									$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
									if(!$rsInsertVSBRL){
										echo "Error: ".$qryInsertVSBRL;
									}
								}
								else{
									$qryInsertVSBRL = "insert into vision_share_batch_receive_list 
														(batch_receive_list_xml, receive_time_date, receive_operator, vs_file_name, vs_file_state, vs_created_date_time, vs_uuid, vs_uri, cert_use) 
														values 
														('".addslashes(trim($pathToWriteXML))."',
														 NOW(),
														 '".$_SESSION['authId']."',
														 '".addslashes(trim($fileName))."',
														 '".addslashes(trim($fileState))."',
														 '".addslashes(trim($fileCreated))."',
														 '".addslashes(trim($fileUUID))."',
														 '".addslashes(trim($fileURI))."',
														 '".trim($strCertUseMain)."'
														)";
									$rsInsertVSBRL = mysql_query($qryInsertVSBRL);
									if(!$rsInsertVSBRL){
										echo "Error: ".$qryInsertVSBRL;
									}
								}
							}
						}
						$fileName = $fileType = $fileState = $fileCreated = $fileUUID = $fileURI = "";
						$blGetFileName = false;
					}
					if($blGetFileName == true){
						if((trim($val["tag"]) == "name") && (trim($val["type"]) == "complete") && (empty($fileName) == true)){
							$fileName = trim($val["value"]);
						}
						elseif((trim($val["tag"]) == "type") && (trim($val["type"]) == "complete") && (empty($fileState) == true)){
							$fileType = trim($val["value"]);
						}
						elseif((trim($val["tag"]) == "state") && (trim($val["type"]) == "complete") && (empty($fileState) == true)){
							$fileState = trim($val["value"]);
						}
						elseif((trim($val["tag"]) == "created") && (trim($val["type"]) == "complete") && (empty($fileCreated) == true)){
							$fileCreated = trim($val["value"]);
						}
						elseif((trim($val["tag"]) == "uuid") && (trim($val["type"]) == "complete") && (empty($fileUUID) == true)){
							$fileUUID = trim($val["value"]);
						}
						elseif((trim($val["tag"]) == "uri") && (trim($val["type"]) == "complete") && (empty($fileURI) == true)){
							$fileURI = trim($val["value"]);
						}	
					}
				}
			}
		}
	}
}

echo 'done';
?>