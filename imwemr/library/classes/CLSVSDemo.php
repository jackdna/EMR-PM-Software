<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
/*
File: CLSVSDemo.php
Purpose: Class to perform action with Ability (vision share)
Access Type: Include File
*/
require_once(dirname(__FILE__).'/class.language.php');
class CLSVSDemo{
	private $objCoreLang;	
	private $strCustomMsg;
	private $arrCertDirInfo;
	public $arrCertInfo;
	
	function __construct() {
		$this->objCoreLang = new core_lang();
		$this->strCustMsg = "No response received from 'Vision Share'. Please try again after some time.";
		$qryGetGroup = "select gro_id, name, vs_cert_in_use, vs_paaword, allocated_ins_comp from groups_new where del_status='0' order by gro_id ";
		$rsGetGroup = imw_query($qryGetGroup);
		while($rowGetGroup = imw_fetch_array($rsGetGroup)){
			$dbVSCertInUse = $certDir = $vsCertInUse = "";
			$strGroupUFC = "";
			$groupNameArr = $arrGroupUCF = array();
			$groupNameArr = preg_split('/ /',$rowGetGroup['name']);	
			for($s = 0;$s < count($groupNameArr); $s++){
				$arrGroupUCF[] = $groupNameArr[$s][0];
			}
			$strGroupUFC = strtoupper(trim(join('',$arrGroupUCF)));
				
			$qryGetVisConf = "select * from vision_share_cert_config where group_id = '".$rowGetGroup['gro_id']."' AND ins_comp_id IN (".$rowGetGroup['allocated_ins_comp'].")";
			$rsGetVisConf = imw_query($qryGetVisConf);
			while($rowGetVisConf = imw_fetch_array($rsGetVisConf)){				
				$dbVSCertInUse = $certDir = $vsCertInUse = $strInsCompId = "";
				$arrVSCertInUse = array();
				$dbVSCertInUse = trim($rowGetVisConf['vs_cert_in_use']);
				$arrVSCertInUse = explode("-", $dbVSCertInUse);
				$certDir = trim($arrVSCertInUse[0]);
				$vsCertInUse = trim($arrVSCertInUse[1]);
				$this->arrCertDirInfo[$vsCertInUse] = array("certDir" => $certDir, "vsCertInUse" => $vsCertInUse);
				define($vsCertInUse, trim($rowGetVisConf['vs_paaword']));
				$this->arrCertInfo[] = array("groupId" => $rowGetVisConf['group_id'], "groupUFC" => $strGroupUFC, "insCompId" => $rowGetVisConf['ins_comp_id'], "certName" => $vsCertInUse);					
			}
		}
	}
	function getCertNameConstent($groupId, $insId = 0){
		$certNameConstent = "";
		foreach($this->arrCertInfo as $intKey => $arrVal){
			if(($arrVal["groupId"] == $groupId) && ($arrVal["insCompId"]) == ($insId)){
				$certNameConstent = $arrVal["certName"];
				break;
			}
			elseif($arrVal["groupId"] == $groupId){
				$certNameConstent = $arrVal["certName"];
				break;
			}
		}
		return $certNameConstent;
	}
	function services($certUse = "VS_PASS_WORD"){		
		$serviceOutput = "";
		$certSSLKeyPath = $certPath = $certClientPath = $vsPassword = "";
		$arrCertInfo = array();
		$strCertInfoCertDir = $strCertInfoCertInUse = "";
		$strCertInfoCertDir = $this->arrCertDirInfo[$certUse]["certDir"];
		$strCertInfoCertInUse = $this->arrCertDirInfo[$certUse]["vsCertInUse"];
		$certSSLKeyPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vskey.pem";
		$certPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsca.pem";
		$certClientPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsclient.pem";
		$vsPassword = constant($strCertInfoCertInUse);
		
		$curl_site_url = "https://portal.visionshareinc.com:443/portal/seapi/services";
		$headers = array("GET /portal/seapi/services HTTP/1.1", 
						"User-Agent: imwemr-292944",
						"X-SEAPI-Version: 1",
						"Host: portal.visionshareinc.com:443",
						"Content-Type: text/xml; charset=utf-8"				
					);
		$cur = curl_init();
		curl_setopt($cur, CURLOPT_URL, $curl_site_url);
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($cur, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, 2); 
		curl_setopt($cur, CURLOPT_SSLCERTTYPE, 'PEM');
		curl_setopt($cur, CURLOPT_SSLKEYTYPE, 'PEM'); 
		curl_setopt($cur, CURLOPT_VERBOSE, '0'); 
		curl_setopt($cur, CURLOPT_SSLKEY, $certSSLKeyPath);
		curl_setopt($cur, CURLOPT_CAINFO, $certPath);
		curl_setopt($cur, CURLOPT_SSLCERT, $certClientPath); 
		curl_setopt($cur, CURLOPT_SSLCERTPASSWD, $vsPassword);
		curl_setopt($cur, CURLOPT_TIMEOUT, 60); 
		$serviceOutput = curl_exec($cur);
		if (curl_errno($cur)){
			if (curl_errno($cur) == 28){
				echo $this->strCustMsg;
			}
			else{
				echo "Please contact your Administrator to resolve Curl Error: " . curl_error($cur);     
			}
			die;	
		}
		curl_close($cur);		
		return $serviceOutput;
	}
	
	function getVSReceiveList($batchReceiveListURI, $certUse = "VS_PASS_WORD"){	
		$certSSLKeyPath = $certPath = $certClientPath = $vsPassword = "";
		$arrCertInfo = array();
		$strCertInfoCertDir = $strCertInfoCertInUse = "";
		$strCertInfoCertDir = $this->arrCertDirInfo[$certUse]["certDir"];
		$strCertInfoCertInUse = $this->arrCertDirInfo[$certUse]["vsCertInUse"];
		$certSSLKeyPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vskey.pem";
		$certPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsca.pem";
		$certClientPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsclient.pem";
		$vsPassword = constant($strCertInfoCertInUse);
		
		$batchReceiveListXML = "";
		$curl_site_url = $batchReceiveListURI;
		$headers = array("User-Agent: imwemr-292944",
						"Host: portal.visionshareinc.com:443",
						"X-SEAPI-Version: 1",								
						);  
		$cur = curl_init();
		curl_setopt($cur, CURLOPT_URL, $curl_site_url);
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($cur, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($cur, CURLOPT_HTTPGET, TRUE); 
		curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, 2); 
		curl_setopt($cur, CURLOPT_SSLCERTTYPE, 'PEM');
		curl_setopt($cur, CURLOPT_SSLKEYTYPE, 'PEM'); 
		curl_setopt($cur, CURLOPT_VERBOSE, '0'); 
		curl_setopt($cur, CURLOPT_SSLKEY, $certSSLKeyPath);
		curl_setopt($cur, CURLOPT_CAINFO, $certPath);
		curl_setopt($cur, CURLOPT_SSLCERT, $certClientPath); 
		curl_setopt($cur, CURLOPT_SSLCERTPASSWD, $vsPassword);	
		curl_setopt($cur, CURLOPT_TIMEOUT, 60); 
		$batchReceiveListXML = curl_exec($cur);
		if (curl_errno($cur)){
			if (curl_errno($cur) == 28){
				echo $this->strCustMsg;     	
			}
			else{
				echo "Please contact your Administrator to resolve Curl Error: " . curl_error($cur);     
			}
			die;	
		}		
		curl_close($cur);
		//echo $batchReceiveListXML;
		//die;
		return $batchReceiveListXML;
	}

	function getVSReceiveResponce($vs_uri, $certUse = "VS_PASS_WORD"){
		$certSSLKeyPath = $certPath = $certClientPath = $vsPassword = "";
		$arrCertInfo = array();
		$strCertInfoCertDir = $strCertInfoCertInUse = "";
		$strCertInfoCertDir = $this->arrCertDirInfo[$certUse]["certDir"];
		$strCertInfoCertInUse = $this->arrCertDirInfo[$certUse]["vsCertInUse"];
		$certSSLKeyPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vskey.pem";
		$certPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsca.pem";
		$certClientPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsclient.pem";
		$vsPassword = constant($strCertInfoCertInUse);
		
		$visionShareOutput = $curlOutput = "";
		$curl_site_url = $vs_uri;
		$headers = array("User-Agent: imwemr-292944",
						"Host: portal.visionshareinc.com:443",
						"X-SEAPI-Version: 1",								
						);  
		$cur = curl_init();
		curl_setopt($cur, CURLOPT_URL, $curl_site_url);
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($cur, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($cur, CURLOPT_HTTPGET, TRUE); 
		curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, 2); 
		curl_setopt($cur, CURLOPT_SSLCERTTYPE, 'PEM');
		curl_setopt($cur, CURLOPT_SSLKEYTYPE, 'PEM'); 
		curl_setopt($cur, CURLOPT_VERBOSE, '0'); 
		curl_setopt($cur, CURLOPT_SSLKEY, $certSSLKeyPath);
		curl_setopt($cur, CURLOPT_CAINFO, $certPath);
		curl_setopt($cur, CURLOPT_SSLCERT, $certClientPath); 
		curl_setopt($cur, CURLOPT_SSLCERTPASSWD, $vsPassword);	
		curl_setopt($cur, CURLOPT_TIMEOUT, 60); 
		$visionShareOutput = curl_exec($cur);
		if (curl_errno($cur)){
			if (curl_errno($cur) == 28){
				echo $this->strCustMsg;     	
			}
			else{
				echo "Please contact your Administrator to resolve Curl Error: " . curl_error($cur);     
			}
		}		
		curl_close($cur);
		return array($visionShareOutput, $curlOutput);
	}
	function getVSIDURI($arr){		
		$realMedCareElgblID = $realMedCareElgblURI = $batchSubmitID  = $batchSubmitURI = $batchReceiveListID = $batchReceiveListURI = "";
		$batchSubmitIDOther = $batchSubmitID5010 = $batchSubmitURI5010 = "";
		$batchReceiveListIDOther = $batchReceiveListID5010 = $batchReceiveListURI5010 = "";
		$blGetUri = false;
		//echo $this->strInsCompName;	
		//pre($arr);
		//die;
		$insCompNameVS = "";
		$isLastServiceBatchSubmit = $isLastServiceBatchReceiveList = false;
		foreach($arr as $key => $val){			
			
			if(is_array($val) == true){
				if($val["tag"] == "service"){					
					foreach($val as $valKey => $valVal){										
						if($valKey == "attributes" && is_array($valVal) == true){							
							if($valVal["type"] == "RealtimeMedicareEligibility"){
								if(empty($realMedCareElgblID) == true){
									$realMedCareElgblID = $valVal["id"];
									break;
								}
							}
							elseif($valVal["type"] == "BatchSubmit"){
								if((empty($batchSubmitID) == true) && ($isLastServiceBatchSubmit == false)){
									$batchSubmitID = $valVal["id"];
									$isLastServiceBatchSubmit = true;
									break;
								}
								elseif((empty($batchSubmitID) == false) && (empty($batchSubmitIDOther) == true) && ($isLastServiceBatchSubmit == false)){
									$batchSubmitIDOther = $valVal["id"];
									$isLastServiceBatchSubmit = true;
									break;
								}
							}
							elseif($valVal["type"] == "BatchReceiveList"){
								if((empty($batchReceiveListID) == true) && ($isLastServiceBatchReceiveList == false)){
									$batchReceiveListID = $valVal["id"];
									$isLastServiceBatchReceiveList = true;
									break;
								}
								elseif((empty($batchReceiveListID) == false) && (empty($batchReceiveListIDOther) == true) && ($isLastServiceBatchReceiveList == false)){
									$batchReceiveListIDOther = $valVal["id"];
									$isLastServiceBatchReceiveList = true;
									break;
								}
							}
						}					
					}
				}				
				elseif($val["tag"] == "name"){
					if(((empty($batchSubmitID) == false) || (empty($batchSubmitIDOther) == false)) && ($isLastServiceBatchSubmit == true)){
						$insCompNameVS = $val["value"];
						if(strpos($insCompNameVS, "5010") !== false){
							if((empty($batchSubmitIDOther) == false) && (empty($batchSubmitID5010) == true)){
								$batchSubmitID5010 = (int)$batchSubmitIDOther;
								$isLastServiceBatchSubmit = false;
							}
							elseif((empty($batchSubmitID) == false) && (empty($batchSubmitID5010) == true)){
								$batchSubmitID5010 = (int)$batchSubmitID;
								$batchSubmitID = "";
								$isLastServiceBatchSubmit = false;
							}
						}
						$isLastServiceBatchSubmit = false;	
					}
					if(((empty($batchReceiveListID) == false) ||(empty($batchReceiveListIDOther) == false)) && ($isLastServiceBatchReceiveList == true)){
						$insCompNameVS = $val["value"];
						if(strpos($insCompNameVS, "5010") !== false){
							if((empty($batchReceiveListIDOther) == false) && (empty($batchReceiveListID5010) == true)){
								$batchReceiveListID5010 = (int)$batchReceiveListIDOther;
								$isLastServiceBatchReceiveList = false;
							}
							elseif((empty($batchReceiveListID) == false) && (empty($batchReceiveListID5010) == true)){
								$batchReceiveListID5010 = (int)$batchReceiveListID;
								$batchReceiveListID = "";
								$isLastServiceBatchReceiveList = false;
							}
						}
						$isLastServiceBatchReceiveList = false;	
					}
				}
				elseif($val["tag"] == "uri"){
					$lastItem = "";
					$arrTemp = array();
					$arrTemp = explode("/", trim($val["value"]));
					$lastItem = end($arrTemp);					
					if((empty($realMedCareElgblURI) == true) && (empty($realMedCareElgblID) == false) && ((int)$realMedCareElgblID == (int)$lastItem)){						
						$realMedCareElgblURI = $val["value"];
					}
					elseif((empty($batchSubmitURI) == true) && (empty($batchSubmitID) == false) && ((int)$batchSubmitID == (int)$lastItem)){
						$batchSubmitURI = $val["value"];
					}
					elseif((empty($batchSubmitURI5010) == true) && (empty($batchSubmitID5010) == false) && ((int)$batchSubmitID5010 == (int)$lastItem)){
						$batchSubmitURI5010 = $val["value"];
					}
					elseif((empty($batchReceiveListURI) == true) && (empty($batchReceiveListID) == false) && ((int)$batchReceiveListID == (int)$lastItem)){
						$batchReceiveListURI = $val["value"];						
					}
					elseif((empty($batchReceiveListURI5010) == true) && (empty($batchReceiveListID5010) == false) && ((int)$batchReceiveListID5010 == (int)$lastItem)){
						$batchReceiveListURI5010 = $val["value"];						
					}
				}
			}
		}
		$arrRet = array();
		$arrRet = array($realMedCareElgblID, $realMedCareElgblURI, 
						$batchSubmitID, $batchSubmitURI, 
						$batchReceiveListID, $batchReceiveListURI,
						$batchSubmitID5010, $batchSubmitURI5010,
						$batchReceiveListID5010, $batchReceiveListURI5010);
		return $arrRet;
	}
	function submit_837_batch($batchSubmitURI, $rqBatchFileSubmitteDd, $certUse = "VS_PASS_WORD"){
		$msg = $groupNPI = "";
		$blTysonSurgeryGroup = false;
		$certSSLKeyPath = $certPath = $certClientPath = $vsPassword = "";
		$qryGetFileData = "select file_name,file_data from batch_file_submitte where Batch_file_submitte_id = '$rqBatchFileSubmitteDd'";		
		$rsGetFileData = imw_query($qryGetFileData);
		if($rsGetFileData){
			$fileNameEDI = $dataEDI = $pathToWriteEDI = $ediToSend = "";
			$rowGetFileData = imw_fetch_array($rsGetFileData);
			$fileNameEDI = $rowGetFileData['file_name'];
			$dataEDI = $rowGetFileData['file_data'];
			$arrCertInfo = array();
			$strCertInfoCertDir = $strCertInfoCertInUse = "";
			$strCertInfoCertDir = $this->arrCertDirInfo[$certUse]["certDir"];
			$strCertInfoCertInUse = $this->arrCertDirInfo[$certUse]["vsCertInUse"];
			$certSSLKeyPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vskey.pem";
			$certPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsca.pem";
			$certClientPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsclient.pem";
			$vsPassword = constant($strCertInfoCertInUse);
			
//			$fileNameEDI = date("Y-m-d-H-i-s_").$fileNameEDI;			
			$pathToWriteEDI = realpath('../BatchFiles/vision_share').'/'.$fileNameEDI;		
			file_put_contents($pathToWriteEDI,stripslashes($dataEDI));			
			imw_free_result($rsGetFileData);
			
			$ediToSend = file_get_contents($pathToWriteEDI);
			$ediLen = filesize($pathToWriteEDI);
			$curl_site_url = trim($batchSubmitURI)."/".trim($fileNameEDI);			
			
			$headers = array("User-Agent: imwemr-292944",
							"Content-Type: application/EDI-X12",
							"Host: portal.visionshareinc.com:443",
							"X-SEAPI-Version: 1",
							"Content-Length: ".$ediLen.""								
						);  		
			$cur = curl_init();
			curl_setopt($cur, CURLOPT_URL, $curl_site_url);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($cur, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($cur, CURLOPT_POST, TRUE); 
			curl_setopt($cur, CURLOPT_POSTFIELDS, $ediToSend);
			curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, TRUE);
			curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, 2); 
			curl_setopt($cur, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($cur, CURLOPT_SSLKEYTYPE, 'PEM'); 
			curl_setopt($cur, CURLOPT_VERBOSE, '0'); 
			curl_setopt($cur, CURLOPT_SSLKEY, $certSSLKeyPath);
			curl_setopt($cur, CURLOPT_CAINFO, $certPath);
			curl_setopt($cur, CURLOPT_SSLCERT, $certClientPath); 
			curl_setopt($cur, CURLOPT_SSLCERTPASSWD, $vsPassword);
			curl_setopt($cur, CURLOPT_TIMEOUT, 60); 
			$output = curl_exec($cur);	
				
			if (curl_errno($cur)){
				if (curl_errno($cur) == 28){
					echo $this->strCustMsg;     	
				}
				else{
					echo "Please contact your Administrator to resolve Curl Error: " . curl_error($cur);     
				}
				die;				
			} 
			else{	
				$arrResponce = $this->XMLToArray($output);
				if(isset($arrResponce[2]["value"]) && $arrResponce[2]["value"] != ""){
					$msg = "File submitted";
					$qryInsert = "insert into vision_share_submit (file_name, submit_time_date, responce, submit_operator) values ('".addslashes(trim($fileNameEDI))."', NOW(), '".addslashes($output)."', '".$_SESSION['authId']."')";
					$rsInsert = imw_query($qryInsert);
				}
				//pre($this->XMLToArray($output));
			}
			curl_close($cur);
			return $msg;
		}
	}
	function page_data($css_patient, $css_header, $msg){
		$pageData = "<html>
					<head>
					<link rel=\"stylesheet\" href=".$GLOBALS['webroot']."/interface/themes/default/css_billing.php\" type=\"text/css\" media=\"screen\">
					<script type=\"text/javascript\" src=\"../common/script_function.js\"></script> 
					</head>
					<body class=\"body_c\">
					<table width=\"100%\" align=\"center\" border=\"0\" bgcolor=\"#FFF3E8\" cellpadding=\"2\" cellspacing=\"2\">
						<tr>
							<td align=\"center\" class=\"input_text_10\"><b>".$msg."</b></td>
						</tr>
						<tr>
							<td>
								<table width=\"100%\" align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
									<tr>
										<td bgcolor=\"#FFFFFF\" width=\"375\" align=\"right\"><input style=\"cursor:pointer;\" type=\"button\" id=\"btn\" onMouseOver=\"button_over('btn')\" onMouseOut=\"button_over('btn', ' ')\" name=\"btn\" class=\"dff_button\" value=\"Back\" onClick=\"javascript:history.go(-1)\" ></td>
										<td bgcolor=\"#FFFFFF\" width=\"5\">&nbsp;</td>
										<td bgcolor=\"#FFFFFF\" align=\"left\"><input style=\"cursor:pointer;\" type=\"button\" id=\"btn2\" onMouseOver=\"button_over('btn2')\" onMouseOut=\"button_over('btn2', ' ')\" name=\"btn2\" class=\"dff_button\" value=\"Close Window\" onClick=\"javascript:window.close()\" ></td>
									</tr>
								</table>
							</td>
						</tr>	
					</table>
					</body>
					</html>";
		return $pageData;		
	}
	
	function process277CA($fileData,$sortOrder=0){
		$interchange_num = '';
		$contentsHTML = "";;
		$strEDI277CA = "";
		$strEDI277CA = trim($fileData);
		$arrEDI2777CA = array();
		$arrEDI2777CA = explode("~",$strEDI277CA);							
		//pre($arrEDI2777CA,1);
		$total_claims = $total_amount = array();
		$blHL1RQSt = $blHL2RQSt = $blHL3RQSt = false;
		$blInfoSource = $blInfoSourceName = $blHL1TRN = $blHL1DTPReceivedDate = $blHL1DTPProcessDate = false;
		$arrHL1 = $arrHL1RQName = $arrHL1TRN = $arrHL1DTP = array();
		$strHL1Seg = $strHL1RQName = $strHL1RQLName = $strHL1RQFName = $strHL1RQMName = $strHL1RQPayerIdCode = $strHL1TRNNumber = $strHL1ReceivedDate = $strHL1ProcessDate = "";
		
		$arrHL2 = $arrHL2RQName = $arrHL2STC = $arrHL2QTY = $arrHL2AMT = array();
		$blInfoReceiver = $blHL2STC = $blHL2QTYAckQtyStatus = $blHL2QTYUnAckQtyStatus = $blHL2AMTInProStatus = $blHL2AMTReturnedStatus = false;
		$strHL2Seg = $strHL2RQName = $strHL2RQLName = $strHL2RQFName = $strHL2RQMName = $strHL2InfoRecCategory = $strHL2InfoRecStatus = $strHL2InfoRecEntity = "";
		$strHL2InfoRecStatusDate = $strHL2InfoRecStatusCode = $strHL2InfoRecAmount = "";
		$strHL2QtyAckQtyStatus = $strHL2QtyAckQtyQuantity = $strHL2QtyUnAckQtyStatus = $strHL2QtyUnAckQtyQuantity = $strHL2AMTInProStatus = $strHL2AMTInProMonetaryAmount = "";
		$strHL2AMTReturnedStatus = $strHL2AMTReturnedMonetaryAmount = "";
		
		$strHL3Seg = $strHL3RQName = $strHL3RQLName = $strHL3RQFName = $strHL3RQMName = $strHL3NPI = $strHL3EmpIdNumber = $strHL3SSN = "";
		$strHL3InfoRecCategory = $strHL3InfoRecStatus = $strHL3InfoRecEntity = $strHL3InfoRecStatusDate = $strHL3InfoRecStatusCode = $strHL3InfoRecAmount = "";
		$strHL3QtyTotalAcceptedStatus = $strHL3QtyTotalAcceptedQuantity = $strHL3QtyTotalRejectedStatus = $strHL3QtyTotalRejectedQuantity = "";
		$strHL3AMTInProStatus = $strHL3AMTInProMonetaryAmount = $strHL3AMTReturnedStatus = $strHL3AMTReturnedMonetaryAmount = "";
		$arrHL3 = $arrHL3RQName = $arrHL3RQName = array();
		$blBillingProviderService = $blBillingProviderServiceName = $blHL3STC = $blHL3QTYTotalAcceptedStatus = $blHL3QTYTotalRejectedStatus = false;
		$blHL3AMTInProStatus = $blHL3AMTReturnedStatus = false;
		
		$blPatientLevel = $blPatientLevelName = $blPatientLevelTRN = $blPatientLevelSTC = $blPatientLevelDTP = false;
		$arr277CA = $arrNM1 = $arrTRN = $arrSTC = $arrREF1K = $arrREFD9 = $arrREFBLT = $arrDTP = array();
		
		foreach($arrEDI2777CA as $key => $val){
			if(strpos($val, "ISA*") !== false && $interchange_num==''){
				$tempISAarr = explode('*',$val);
				$interchange_num = intval($tempISAarr[13]);
				$interchange_num_len = strlen($interchange_num);
				if($interchange_num_len<4){
					$icn_len_diff = 4-$interchange_num_len;
					$icn_prefix = str_repeat('0',$icn_len_diff);
					$interchange_num = $icn_prefix.$interchange_num;
				}
			}elseif((strpos($val, "HL*1*") !== false) && ($blHL1RQSt == false)){								
				$blHL1RQSt = true;
				$strHL1Seg = trim($val);
				$arrHL1 = explode("*", $strHL1Seg);
				//pre($arrHL1,1);
				if($arrHL1[3] == "20"){
					$blInfoSource = true;
				}
				elseif($arrHL1[4] == "20"){
					$blInfoSource = true;
				}
			}
			elseif((strpos($val, "HL*2*") !== false) && ($blHL2RQSt == false)){								
				$blHL1RQSt = false;
				$blHL2RQSt = true;
				$strHL2Seg = trim($val);
				$arrHL2 = explode("*", $strHL2Seg);
				//pre($arrHL2,1);
				if($arrHL2[3] == "21"){
					$blInfoReceiver = true;
				}
				elseif($arrHL2[4] == "21"){
					$blInfoReceiver = true;
				}
			}
			elseif((strpos($val, "HL*3*") !== false) && ($blHL3RQSt == false)){								
				$blHL1RQSt = false;
				$blHL2RQSt = false;
				$blHL3RQSt = true;
				
				$strHL3Seg = trim($val);
				$arrHL3 = explode("*", $strHL3Seg);
				//pre($arrHL3,1);
				if($arrHL3[3] == "19"){
					$blBillingProviderService = true;
				}
				elseif($arrHL3[4] == "19"){
					$blBillingProviderService = true;
				}
			}
			elseif((strpos($val, "HL*") !== false)){	
				$strHL4Seg = trim($val);
				$arrHL4 = explode("*", $strHL4Seg);				
				if($arrHL4[3] == "PT"){
					//pre($arrHL4);
					$blHL1RQSt = false;
					$blHL2RQSt = false;
					$blHL3RQSt = false;
					$blPatientLevel = true;
					if(count($arrNM1) > 0){
						//pre($arrNM1,1);
						$arr277CA[] = array("NM1" => $arrNM1, "TRN" => $arrTRN , "STC" => $arrSTC, "REF1K" => $arrREF1K, "REFD9" => $arrREFD9 , "REFBLT" => $arrREFBLT, "DTP" => $arrDTP);
						if($arrSTC['STCActionCode']=='Accept'){
							$total_amount['accepted'][]	= $arrSTC['STCAmount'];
						}else{
							$total_amount['rejected'][]	= $arrSTC['STCAmount'];
						}
					}
					$arrNM1 = $arrTRN = $arrSTC = $arrREF1K = $arrREFD9 = $arrREFBLT = $arrDTP = array();
					$blPatientLevelName = $blPatientLevelTRN = $blPatientLevelSTC = $blPatientLevelDTP = false;
				}
				elseif($arrHL4[4] == "PT"){
					//pre($arrHL4);
					$blHL1RQSt = false;
					$blHL2RQSt = false;
					$blHL3RQSt = false;
					$blPatientLevel = true;
					if(count($arrNM1) > 0){
						//pre($arrNM1,1);
						$arr277CA[] = array("NM1" => $arrNM1, "TRN" => $arrTRN , "STC" => $arrSTC, "REF1K" => $arrREF1K, "REFD9" => $arrREFD9 , "REFBLT" => $arrREFBLT, "DTP" => $arrDTP);
						if($arrSTC['STCActionCode']=='Accept'){
							$total_amount['accepted'][]	= $arrSTC['STCAmount'];
						}else{
							$total_amount['rejected'][]	= $arrSTC['STCAmount'];
						}
					}
					$arrNM1 = $arrTRN = $arrSTC = $arrREF1K = $arrREFD9 = $arrREFBLT = $arrDTP = array();
					$blPatientLevelName = $blPatientLevelTRN = $blPatientLevelSTC = $blPatientLevelDTP = false;
				}
			}
			elseif(strpos($val, "IEA") !== false){				
				//Patient Level							
				$blHL1RQSt = false;
				$blHL2RQSt = false;
				$blHL3RQSt = false;
				$blPatientLevel = true;
				if(count($arrNM1) > 0){
					//echo "med";
					//pre($arrNM1);
					$arr277CA[] = array("NM1" => $arrNM1, "TRN" => $arrTRN , "STC" => $arrSTC, "REF1K" => $arrREF1K, "REFD9" => $arrREFD9 , "REFBLT" => $arrREFBLT, "DTP" => $arrDTP);
					if($arrSTC['STCActionCode']=='Accept'){
						$total_amount['accepted'][]	= $arrSTC['STCAmount'];
					}else{
						$total_amount['rejected'][]	= $arrSTC['STCAmount'];
					}
				}
				$arrNM1 = $arrTRN = $arrSTC = $arrREF1K = $arrREFD9 = $arrREFBLT = $arrDTP = array();
				$blPatientLevelName = $blPatientLevelTRN = $blPatientLevelSTC = $blPatientLevelDTP = false;
			}
			
			//INFORMATION SOURCE LEVEL
			if(($blHL1RQSt == true) && ($blInfoSource == true) && (strpos($val, "NM1") !== false) && ($blInfoSourceName == false)){																										
				$strHL1RQNameSeg = "";
				$strHL1RQNameSeg = trim($val);
				$arrHL1RQName = explode("*", $strHL1RQNameSeg);
				//pre($arrHL1RQName,1);
				if(($arrHL1RQName[1] == "PR") && ($arrHL1RQName[2] == "2")){
					$strHL1RQName = ucwords(strtolower(trim($arrHL1RQName[3])));	
				}else{
					$strHL1RQLName = ucfirst(strtolower(trim($arrHL1RQName[3])));
					$strHL1RQFName = ucfirst(strtolower(trim($arrHL1RQName[4])));
					$strHL1RQMName = ucfirst(strtolower(trim($arrHL1RQName[5])));
					if(trim($strHL1RQMName[9]) != ""){
						$strHL1RQPayerIdCode = trim($strHL1RQMName[9]);
					}
				}
				$blInfoSourceName = true;	
			}
			elseif(($blHL1RQSt == true) && ($blInfoSource == true) && (strpos($val, "TRN") !== false) && ($blHL1TRN == false)){
				$strHL1TRNSeg = "";
				$strHL1TRNSeg = trim($val);
				$arrHL1TRN = explode("*", $strHL1TRNSeg);																
				//pre($arrHL1TRN,1);
				$strHL1TRNNumber = trim($arrHL1TRN[2]);
				$blHL1TRN = true;
			}
			elseif(($blHL1RQSt == true) && ($blInfoSource == true) && (strpos($val, "DTP") !== false) ){
				$strHL1DTPSeg = "";
				$strHL1DTPSeg = trim($val);
				$arrHL1DTP = explode("*", $strHL1DTPSeg);																
				//pre($arrHL1DTP,1);
				if((trim($arrHL1DTP[1]) == "050") && ($blHL1DTPReceivedDate == false)){
					$strHL1ReceivedDate = trim($arrHL1DTP[3]);
					$blHL1DTPReceivedDate = true;
				}
				elseif((trim($arrHL1DTP[1]) == "009") && ($blHL1DTPProcessDate == false)){
					$strHL1ProcessDate = trim($arrHL1DTP[3]);
					$blHL1DTPProcessDate = true;
				}
				elseif((trim($arrHL1DTP[2]) == "050") && ($blHL1DTPReceivedDate == false)){
					$strHL1ReceivedDate = trim($arrHL1DTP[4]);
					$blHL1DTPReceivedDate = true;
				}
				elseif((trim($arrHL1DTP[2]) == "009") && ($blHL1DTPProcessDate == false)){
					$strHL1ProcessDate = trim($arrHL1DTP[4]);
					$blHL1DTPProcessDate = true;
				}
			}
			
			//INFORMATION RECEIVER LEVEL
			if(($blHL2RQSt == true) && ($blInfoReceiver == true) && (strpos($val, "NM1") !== false) && ($blInfoReceiverName == false)){																										
				$strHL2RQNameSeg = "";
				$strHL2RQNameSeg = trim($val);
				$arrHL2RQName = explode("*", $strHL2RQNameSeg);
				//pre($arrHL2RQName,1);
				if(($arrHL2RQName[1] == "41") && ($arrHL2RQName[2] == "2")){
					$strHL2RQName = ucfirst(strtolower(trim($arrHL2RQName[3])));	
				}
				elseif($arrHL2RQName[2] == "1"){
					$strHL2RQLName = ucfirst(strtolower(trim($arrHL2RQName[3])));
					$strHL2RQFName = ucfirst(strtolower(trim($arrHL2RQName[4])));
					$strHL2RQMName = ucfirst(strtolower(trim($arrHL2RQName[5])));
				}
				$blInfoReceiverName = true;	
			}
			elseif(($blHL2RQSt == true) && ($blInfoReceiver == true) && (strpos($val, "STC") !== false) && ($blHL2STC == false)){
				$strHL2STCSeg = $strHL2InfoRecStatus = "";
				$arrHL2Status = array();
				$strHL2STCSeg = trim($val);
				$arrHL2STC = explode("*", $strHL2STCSeg);
				//pre($arrHL2STC,1);
				if(strpos($arrHL2STC[1], ":") !== false){
					$strHL2InfoRecStatus = trim($arrHL2STC[1]);
					
					$strHL2InfoRecStatusDate = trim($arrHL2STC[2]);				
					$strHL2InfoRecStatusCode = $this->getVisionShareError("277CA", "STC_Action_Code", $arrHL2STC[3]);
					if(is_string($strHL2InfoRecStatusCode) == false){
						$strHL2InfoRecStatus = "";
					}
					$strHL2InfoRecAmount = trim($arrHL2STC[4]);
				}
				elseif(strpos($arrHL2STC[2], ":") !== false){
					$strHL2InfoRecStatus = trim($arrHL2STC[2]);
					
					$strHL2InfoRecStatusDate = trim($arrHL2STC[3]);				
					$strHL2InfoRecStatusCode = $this->getVisionShareError("277CA", "STC_Action_Code", $arrHL2STC[4]);
					if(is_string($strHL2InfoRecStatusCode) == false){
						$strHL2InfoRecStatus = "";
					}
					$strHL2InfoRecAmount = trim($arrHL2STC[5]);
					
				}
				$arrHL2Status = explode(":", $strHL2InfoRecStatus);
				//pre($arrHL2Status,1);
				$strHL2InfoRecCategory = $this->getVisionShareError("277CA", "STC_Claim_Status_Category_Codes", $arrHL2Status[0]);
				if(is_string($strHL2InfoRecCategory) == false){
					$strHL2InfoRecCategory = "";
				}
				$strHL2InfoRecStatus = $this->getVisionShareError("277CA", "STC_Health_Care_Claim_Status_Codes", $arrHL2Status[1]);
				if(is_string($strHL2InfoRecStatus) == false){
					$strHL2InfoRecStatus = "";
				}
				$strHL2InfoRecEntity = trim($arrHL2Status[2]);
				
				$blHL2STC = true;
			}
			elseif(($blHL2RQSt == true) && ($blInfoReceiver == true) && (strpos($val, "QTY") !== false)){
				$strHL2QTYSeg = "";
				$strHL2QTYSeg = trim($val);
				$arrHL2QTY = array();
				$arrHL2QTY = explode("*", $strHL2QTYSeg);
				//pre($arrHL2QTY,1);
				
				if((trim($arrHL2QTY[1]) == "90") && ($blHL2QTYAckQtyStatus == false)){
					$strHL2QtyAckQtyStatus = $this->getVisionShareError("277CA", "QTY_Information_Receiver_Quantity_Qualifier", $arrHL2QTY[1]);
					$strHL2QtyAckQtyQuantity = trim($arrHL2QTY[2]);
					$blHL2QTYAckQtyStatus = true;
				}
				elseif((trim($arrHL2QTY[1]) == "AA") && ($blHL2QTYUnAckQtyStatus == false)){
					$strHL2QtyUnAckQtyStatus = $this->getVisionShareError("277CA", "QTY_Information_Receiver_Quantity_Qualifier", $arrHL2QTY[1]);
					$strHL2QtyUnAckQtyQuantity = trim($arrHL2QTY[2]);
					$blHL2QTYUnAckQtyStatus = true;
				}
				elseif((trim($arrHL2QTY[2]) == "90") && ($blHL2QTYAckQtyStatus == false)){
					$strHL2QtyAckQtyStatus = $this->getVisionShareError("277CA", "QTY_Information_Receiver_Quantity_Qualifier", $arrHL2QTY[2]);
					$strHL2QtyAckQtyQuantity = trim($arrHL2QTY[3]);
					$blHL2QTYAckQtyStatus = true;
				}
				elseif((trim($arrHL2QTY[2]) == "AA") && ($blHL2QTYUnAckQtyStatus == false)){
					$strHL2QtyUnAckQtyStatus = $this->getVisionShareError("277CA", "QTY_Information_Receiver_Quantity_Qualifier", $arrHL2QTY[2]);
					$strHL2QtyUnAckQtyQuantity = trim($arrHL2QTY[3]);
					$blHL2QTYUnAckQtyStatus = true;
				}
			}
			elseif(($blHL2RQSt == true) && ($blInfoReceiver == true) && (strpos($val, "AMT") !== false)){
				$strHL2AMTSeg = "";
				$strHL2AMTSeg = trim($val);
				$arrHL2AMT = array();
				$arrHL2AMT = explode("*", $strHL2AMTSeg);
				//pre($arrHL2AMT,1);
				
				if((trim($arrHL2AMT[1]) == "YU") && ($blHL2AMTInProStatus == false)){
					$strHL2AMTInProStatus = $this->getVisionShareError("277CA", "AMT_Information_Receiver_Amount_Quantity_Qualifier", $arrHL2AMT[1]);
					$strHL2AMTInProMonetaryAmount = trim($arrHL2AMT[2]);
					$blHL2AMTInProStatus = true;
				}
				elseif((trim($arrHL2AMT[1]) == "YY") && ($blHL2AMTReturnedStatus == false)){
					$strHL2AMTReturnedStatus = $this->getVisionShareError("277CA", "AMT_Information_Receiver_Amount_Quantity_Qualifier", $arrHL2AMT[1]);
					$strHL2AMTReturnedMonetaryAmount = trim($arrHL2AMT[2]);
					$blHL2AMTReturnedStatus = true;
				}
			}
			
			//BILLING/PAY-TO-PROVIDER OF SERVICE LEVEL
			if(($blHL3RQSt == true) && ($blBillingProviderService == true) && (strpos($val, "NM1") !== false) && ($blBillingProviderServiceName == false)){																										
				$strHL3RQNameSeg = "";
				$strHL3RQNameSeg = trim($val);
				$arrHL3RQName = explode("*", $strHL3RQNameSeg);
				//pre($arrHL3RQName,1);
				if(($arrHL3RQName[1] == "85") && ($arrHL3RQName[2] == "2")){
					$strHL3RQName = ucfirst(strtolower(trim($arrHL3RQName[3])));	
				}
				elseif(($arrHL3RQName[1] == "85") && ($arrHL2RQName[2] == "1")){
					$strHL3RQLName = ucfirst(strtolower(trim($arrHL3RQName[3])));
					$strHL3RQFName = ucfirst(strtolower(trim($arrHL3RQName[4])));
					$strHL3RQMName = ucfirst(strtolower(trim($arrHL3RQName[5])));
				}
				if($arrHL3RQName[8] == "XX"){
					$strHL3NPI = trim($arrHL3RQName[9]);
				}
				elseif($arrHL3RQName[8] == "24"){
					$strHL3EmpIdNumber = trim($arrHL3RQName[9]);
				}
				elseif($arrHL3RQName[8] == "34"){
					$strHL3SSN = trim($arrHL3RQName[9]);
				}
				$blBillingProviderServiceName = true;	
			}
			elseif(($blHL3RQSt == true) && ($blBillingProviderService == true) && (strpos($val, "STC") !== false) && ($blHL3STC == false)){
				$strHL3STCSeg = $strHL3InfoRecStatus = "";
				$arrHL3Status = array();
				$strHL3STCSeg = trim($val);
				$arrHL3STC = explode("*", $strHL3STCSeg);
				//pre($arrHL3STC,1);
				if(strpos($arrHL3STC[1], ":") !== false){
					$strHL3InfoRecStatus = trim($arrHL3STC[1]);
					
					$strHL3InfoRecStatusDate = trim($arrHL3STC[2]);
				
					$strHL3InfoRecStatusCode = $this->getVisionShareError("277CA", "STC_Action_Code", $arrHL3STC[3]);
					
					$strHL3InfoRecAmount = trim($arrHL3STC[4]);

				}
				elseif(strpos($arrHL3STC[2], ":") !== false){
					$strHL3InfoRecStatus = trim($arrHL3STC[2]);
					
					$strHL3InfoRecStatusDate = trim($arrHL3STC[3]);
				
					$strHL3InfoRecStatusCode = $this->getVisionShareError("277CA", "STC_Action_Code", $arrHL3STC[4]);
					
					$strHL3InfoRecAmount = trim($arrHL3STC[5]);
				
				}
				$arrHL3Status = explode(":", $strHL3InfoRecStatus);
				//pre($arrHL3Status,1);
				$strHL3InfoRecCategory = $this->getVisionShareError("277CA", "STC_Claim_Status_Category_Codes", $arrHL3Status[0]);
				if(is_string($strHL3InfoRecCategory) == false){
					$strHL3InfoRecCategory = "";
				}				
				$strHL3InfoRecStatus = $this->getVisionShareError("277CA", "STC_Health_Care_Claim_Status_Codes", $arrHL3Status[1]);
				if(is_string($strHL3InfoRecStatus) == false){
					$strHL3InfoRecStatus = "";
				}
				$strHL3InfoRecEntity = trim($arrHL3Status[2]);
				
				$blHL3STC = true;
			}
			elseif(($blHL3RQSt == true) && ($blBillingProviderService == true) && (strpos($val, "QTY") !== false)){
				$strHL3QTYSeg = "";
				$strHL3QTYSeg = trim($val);
				$arrHL3QTY = array();
				$arrHL3QTY = explode("*", $strHL3QTYSeg);
				//pre($arrHL3QTY,1);
				
				if((trim($arrHL3QTY[1]) == "QA") && ($blHL3QTYTotalAcceptedStatus == false)){
					$strHL3QtyTotalAcceptedStatus = $this->getVisionShareError("277CA", "QTY_Information_Receiver_Quantity_Qualifier", $arrHL3QTY[1]);
					$strHL3QtyTotalAcceptedQuantity = trim($arrHL3QTY[2]);
					$blHL3QTYTotalAcceptedStatus = true;
				}
				elseif((trim($arrHL3QTY[1]) == "QC") && ($blHL3QTYTotalRejectedStatus == false)){
					$strHL3QtyTotalRejectedStatus = $this->getVisionShareError("277CA", "QTY_Information_Receiver_Quantity_Qualifier", $arrHL3QTY[1]);
					$strHL3QtyTotalRejectedQuantity = trim($arrHL3QTY[2]);
					$blHL3QTYTotalRejectedStatus = true;
				}
				elseif((trim($arrHL3QTY[2]) == "QA") && ($blHL3QTYTotalAcceptedStatus == false)){
					$strHL3QtyTotalAcceptedStatus = $this->getVisionShareError("277CA", "QTY_Information_Receiver_Quantity_Qualifier", $arrHL3QTY[2]);
					$strHL3QtyTotalAcceptedQuantity = trim($arrHL3QTY[3]);
					$blHL3QTYTotalAcceptedStatus = true;
				}
				elseif((trim($arrHL3QTY[2]) == "QC") && ($blHL3QTYTotalRejectedStatus == false)){
					$strHL3QtyTotalRejectedStatus = $this->getVisionShareError("277CA", "QTY_Information_Receiver_Quantity_Qualifier", $arrHL3QTY[2]);
					$strHL3QtyTotalRejectedQuantity = trim($arrHL3QTY[3]);
					$blHL3QTYTotalRejectedStatus = true;
				}
			}
			elseif(($blHL3RQSt == true) && ($blBillingProviderService == true) && (strpos($val, "AMT") !== false)){
				$strHL3AMTSeg = "";
				$strHL3AMTSeg = trim($val);
				$arrHL3AMT = array();
				$arrHL3AMT = explode("*", $strHL3AMTSeg);
				//pre($arrHL3AMT,1);
				
				if((trim($arrHL3AMT[1]) == "YU") && ($blHL3AMTInProStatus == false)){
					$strHL3AMTInProStatus = $this->getVisionShareError("277CA", "AMT_Information_Receiver_Amount_Quantity_Qualifier", $arrHL3AMT[1]);
					$strHL3AMTInProMonetaryAmount = trim($arrHL3AMT[2]);
					$blHL3AMTInProStatus = true;
				}
				elseif((trim($arrHL3AMT[1]) == "YY") && ($blHL3AMTReturnedStatus == false)){
					$strHL3AMTReturnedStatus = $this->getVisionShareError("277CA", "AMT_Information_Receiver_Amount_Quantity_Qualifier", $arrHL3AMT[1]);
					$strHL3AMTReturnedMonetaryAmount = trim($arrHL3AMT[2]);
					$blHL3AMTReturnedStatus = true;
				}
			}
			//PATIENT LEVEL
			if($blPatientLevel == true){
				
				if((strpos($val, "NM1") !== false) && ($blPatientLevelName == false)){
					$strPatLevelNameSeg = $strPatLevelRQLName = $strPatLevelRQFName = $strPatLevelRQMName = $strPatLevelRQMemberId = "";
					$strPatLevelNameSeg = trim($val);
					$arrPatLevelName = array();
					$arrPatLevelName = explode("*", $strPatLevelNameSeg);
					//pre($arrPatLevelName,1);
					$strPatLevelRQLName = ucfirst(strtolower(trim($arrPatLevelName[3])));
					$strPatLevelRQFName = ucfirst(strtolower(trim($arrPatLevelName[4])));
					$strPatLevelRQMName = ucfirst(strtolower(trim($arrPatLevelName[5])));
					if(trim($arrPatLevelName[8]) == "MI"){
						$strPatLevelRQMemberId = trim($arrPatLevelName[9]);
					}
					$arrNM1 = array("patLname" => $strPatLevelRQLName, "patFname" => $strPatLevelRQFName, "patMname" => $strPatLevelRQMName, "patMemberId" => $strPatLevelRQMemberId);
					$blPatientLevelName = true;
					//pre($arrNM1,1);
				}
				elseif((strpos($val, "TRN") !== false) && ($blPatientLevelTRN == false)){
					$strPatLevelTRNSeg = $strPatLevelTRNTraceNumber = "";
					$strPatLevelTRNSeg = trim($val);
					$arrPatLevelTRN = array();
					$arrPatLevelTRN = explode("*", $strPatLevelTRNSeg);
					//pre($arrPatLevelTRN,1);
					$strPatLevelTRNTraceNumber = trim($arrPatLevelTRN[2]);
					$arrTRN = array("TRNTraceNumber" => $strPatLevelTRNTraceNumber);
					$blPatientLevelTRN = true;
				}
				elseif((strpos($val, "STC") !== false) && ($blPatientLevelSTC == false)){
					$strPatLevelSTCSeg = $strPatLevelSTCStatus = $strPatLevelSTCCategory = $strPatLevelSTCStatus = $strPatLevelSTCEntity = $strPatLevelSTCStatusDate = $strPatLevelSTCStatusCode = $strPatLevelSTCAmount  = "";
					$strPatLevelSTCSeg = trim($val);
					$arrPatLevelSTC = $arrPatLevelSTCStatus = array();
					$arrPatLevelSTC = explode("*", $strPatLevelSTCSeg);
					//pre($arrPatLevelSTC,1);
					if(strpos($arrPatLevelSTC[1], ":") !== false){
						$strPatLevelSTCStatus = trim($arrPatLevelSTC[1]);
						
						$strPatLevelSTCStatusDate = trim($arrPatLevelSTC[2]);
					
						$strPatLevelSTCStatusCode = $this->getVisionShareError("277CA", "STC_Action_Code", $arrPatLevelSTC[3]);
						if(is_string($strPatLevelSTCStatusCode) == false){
							$strPatLevelSTCStatusCode = "";
						}
						$strPatLevelSTCAmount = trim($arrPatLevelSTC[4]);

					}
					elseif(strpos($arrPatLevelSTC[1], ":") !== false){
						$strPatLevelSTCStatus = trim($arrPatLevelSTC[2]);
						
						$strPatLevelSTCStatusDate = trim($arrPatLevelSTC[3]);
					
						$strPatLevelSTCStatusCode = $this->getVisionShareError("277CA", "STC_Action_Code", $arrPatLevelSTC[4]);
						if(is_string($strPatLevelSTCStatusCode) == false){
							$strPatLevelSTCStatusCode = "";
						}
						$strPatLevelSTCAmount = trim($arrPatLevelSTC[5]);
					}
					
					$ARPatLevelSTCStatus = array(); $strPatLevelSTCStatus = '';
					foreach($arrPatLevelSTC as $STCvals){//STC*A7:21:PR*20130520*U*392.46*A7:153:PR*A7:153:40
						$strHLPATInfoRecStatus1 = $strHLPATInfoRecStatus2 = '';
						if(strpos($STCvals, ":") !== false){
							$arrPatLevelSTCStatus = explode(":", $STCvals);
							$strPatLevelSTCCategory = $this->getVisionShareError("277CA", "STC_Claim_Status_Category_Codes", $arrPatLevelSTCStatus[0]);
							if(is_string($strPatLevelSTCCategory) == false){$strPatLevelSTCCategory = "";}
							if($arrPatLevelSTCStatus[1]=='570'){
								$strPatLevelFreeText  = trim($arrPatLevelSTC[12]);
								$ARPatLevelSTCStatus[] = ucfirst(strtolower($strPatLevelFreeText));
							}else{
								$strHLPATInfoRecStatus1 = $this->getVisionShareError("277CA", "STC_Health_Care_Claim_Status_Codes", $arrPatLevelSTCStatus[1]);
								if(is_string($strHLPATInfoRecStatus1) != false){$ARPatLevelSTCStatus[] = $strHLPATInfoRecStatus1;}
								if(is_string($arrPatLevelSTCStatus[2]) == false){
									$strHLPATInfoRecStatus2 = $this->getVisionShareError("277CA", "STC_Health_Care_Claim_Status_Codes", $arrPatLevelSTCStatus[2]);
									if(is_string($strHLPATInfoRecStatus2) != false){$ARPatLevelSTCStatus[] = $strHLPATInfoRecStatus2;}
								}else{
									$strPatLevelSTCEntity = trim($arrPatLevelSTCStatus[2]);	
								}
							}
						}					
					}
					$strPatLevelSTCStatus = '<li>'.implode('</li><li>',$ARPatLevelSTCStatus).'</li>';
					
					$arrSTC = array("STCcategoryCode" => $strPatLevelSTCCategory, "STCstatusCode" => $strPatLevelSTCStatus, "STCEntityIdentifier" => $strPatLevelSTCEntity, "STCDate" => $strPatLevelSTCStatusDate, "STCActionCode" => $strPatLevelSTCStatusCode, "STCAmount" => $strPatLevelSTCAmount,"STCFreeText"=>$strPatLevelFreeText);
					$blPatientLevelSTC = true;
				}
				elseif(strpos($val, "REF") !== false){
					$strPatLevelREFSeg = "";
					$strPatLevelREFSeg = trim($val);
					$arrPatLevelREF = array();
					$arrPatLevelREF = explode("*", $strPatLevelREFSeg);
					//pre($arrPatLevelREF,1);
					
					if(trim($arrPatLevelREF[1]) == "1K"){
						$arrREF1K = array("REFPayerClaimNumber" => $arrPatLevelREF[2]);	
					}
					elseif(trim($arrPatLevelREF[1]) == "D9"){
						$arrREFD9 = array("REFClaimNumber" => $arrPatLevelREF[2]);	
					}
					elseif(trim($arrPatLevelREF[1]) == "BLT"){
						$arrREFBLT = array("REFBillingType" => $arrPatLevelREF[2]);	
					}
				}
				elseif((strpos($val, "DTP") !== false) && ($blPatientLevelDTP == false)){
					$strPatLevelDTPSeg = $strPatLevelDTPDateQualifier = $strDate1 = $strDate2 = "";
					$strPatLevelDTPSeg = trim($val);
					$arrPatLevelDTP = array();
					$arrPatLevelDTP = explode("*", $strPatLevelDTPSeg);
					//pre($arrPatLevelDTP,1);
					
					//$strPatLevelDTPDateQualifier = $this->getVisionShareError("vision_share_271", "DTP", $arrPatLevelDTP[2]);
					
					if(trim($arrPatLevelDTP[2]) == "RD8"){
						$arrTemp = array();
						$arrTemp = explode("-", trim($arrPatLevelDTP[3]));
						$strDate1 = $arrTemp[0];
						$strDate2 = $arrTemp[1];
						$strPatLevelDTPDateQualifier = $this->getVisionShareError("vision_share_271", "DTP", $arrPatLevelDTP[1]);
					}
					elseif(trim($arrPatLevelDTP[2]) == "D8"){
						$strDate1 = trim($arrPatLevelDTP[3]);
						$strPatLevelDTPDateQualifier = $this->getVisionShareError("vision_share_271", "DTP", $arrPatLevelDTP[1]);
					}
					elseif(trim($arrPatLevelDTP[3]) == "RD8"){
						$arrTemp = array();
						$arrTemp = explode("-", trim($arrPatLevelDTP[4]));
						$strDate1 = $arrTemp[0];
						$strDate2 = $arrTemp[1];
						$strPatLevelDTPDateQualifier = $this->getVisionShareError("vision_share_271", "DTP", $arrPatLevelDTP[2]);
					}
					elseif(trim($arrPatLevelDTP[3]) == "D8"){
						$strDate1 = trim($arrPatLevelDTP[4]);
						$strPatLevelDTPDateQualifier = $this->getVisionShareError("vision_share_271", "DTP", $arrPatLevelDTP[2]);
					}
					$arrDTP = array("DTPClaimDateTimeQualifier" => $strPatLevelDTPDateQualifier, "DTPClaimDateTimePeriod1" => $strDate1, "DTPClaimDateTimePeriod2" => $strDate2);
					$blPatientLevelDTP = true;
				}
				
			}
		}
		
		$global_date_format = get_sql_date_format();
		$showCurrencySymbol=show_currency();
		
		ob_start();
		?>
        <table class="table table-striped table-hover">
        	<tr class="grythead">
            	<td colspan="2">Information Source</td>
                <td colspan="4" style="text-align:right;"><b>Batch Claim File#:</b> <?php echo $interchange_num;?></td>
        	</tr>
            <tr>
            	<td width="169" >
                	<b>Name:</b>
                </td>
                <td width="383" >
                	<?php
					if(empty($strHL1RQName) == false){
						echo $strHL1RQName;
					}
					else{
						if(trim($strHL1RQLName)=='' && trim($strHL1RQFName)=='' && trim($strHL1RQMName)==''){return '';}
						echo $strHL1RQLName.", ".$strHL1RQFName." ".$strHL1RQMName;
					}					
					?>
                </td>
                <td width="334" >
                	<b>Current Transimission Trace Numbers:</b>
                </td>
                <td width="847" colspan="3">
                	<?php
						echo $strHL1TRNNumber;
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	<b>Receipt Date:</b>
                </td>
                <td>
                	<?php
						//By Karan
						if($global_date_format == "d-m-Y")
						{
							echo substr($strHL1ReceivedDate,6,2).'-'.substr($strHL1ReceivedDate,4,2).'-'.substr($strHL1ReceivedDate,0,4);
						}
						else
						{
							echo substr($strHL1ReceivedDate,4,2).'-'.substr($strHL1ReceivedDate,6,2).'-'.substr($strHL1ReceivedDate,0,4);
						}
						
						//echo substr($strHL1ReceivedDate,4,2).'-'.substr($strHL1ReceivedDate,6,2).'-'.substr($strHL1ReceivedDate,0,4);
					?>
                </td>
                <td>
                	<b>Process Date:</b>
                </td>
                <td colspan="3">
                	<?php
						//By Karan
						if($global_date_format == "d-m-Y")
						{
							echo substr($strHL1ProcessDate,6,2).'-'.substr($strHL1ProcessDate,4,2).'-'.substr($strHL1ProcessDate,0,4);
						}
						else
						{
							echo substr($strHL1ProcessDate,4,2).'-'.substr($strHL1ProcessDate,6,2).'-'.substr($strHL1ProcessDate,0,4);
						}
						
						//echo substr($strHL1ProcessDate,4,2).'-'.substr($strHL1ProcessDate,6,2).'-'.substr($strHL1ProcessDate,0,4);
					?>
                </td>
                
            </tr>
  
        	<tr class="grythead">
            	<td colspan="6">Information Receiver</td>
        	</tr>
            <tr>
            	<td width="19%">
                	<b>Name:</b>
                </td>
                <td width="20%">
                	<?php
					if(empty($strHL2RQName) == false){
						echo $strHL2RQName;
					}
					else{
						echo $strHL2RQLName.", ".$strHL2RQFName." ".$strHL2RQMName;
					}					
					?>
                </td>
                <td width="18%">
                	<b>Entity:</b>
                </td>
                <td width="10%">
                	<?php
						echo $strHL2InfoRecEntity;
					?>
                </td>
                <td width="13%">
                	<b>Date:</b>
                </td>
                <td width="20%">
                	<?php
					//By Karan
						if($global_date_format == "d-m-Y")
						{
							echo substr($strHL2InfoRecStatusDate,6,2).'-'.substr($strHL2InfoRecStatusDate,4,2).'-'.substr($strHL2InfoRecStatusDate,0,4);
						}
						else
						{
							echo substr($strHL2InfoRecStatusDate,4,2).'-'.substr($strHL2InfoRecStatusDate,6,2).'-'.substr($strHL2InfoRecStatusDate,0,4);
						}
					
						//echo substr($strHL2InfoRecStatusDate,4,2).'-'.substr($strHL2InfoRecStatusDate,6,2).'-'.substr($strHL2InfoRecStatusDate,0,4);
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	<b>Category:</b>
                </td>
                <td>
                	<?php
						echo $strHL2InfoRecCategory;
					?>
                </td>
                <td>
                	<b>Status:</b>
                </td>
                <td>
                	<?php
						echo $strHL2InfoRecStatus;
					?>
                </td>
                <td>
                	<b>Total Submitted Charges:</b>
                </td>
                <td>
                	<?php
						//echo '$'.$strHL2InfoRecAmount;
						$total_submitted_amount = array_sum($total_amount['accepted']) + array_sum($total_amount['rejected']);
						echo $showCurrencySymbol.number_format($total_submitted_amount,2);
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	<b>Acknowledgement Total Accepted Quantity:</b>
                </td>
                <td>
                	<?php 
						echo count($total_amount['accepted']);//$strHL2QtyAckQtyQuantity;
					?>
                </td>
                <td >
                	<b>Acknowledgement Total Rejected Quantity:</b>
                </td>
                <td colspan="3">
                	<?php
						echo count($total_amount['rejected']);//$strHL2QtyUnAckQtyQuantity;
					?>
                </td>       
            </tr>
            <tr>
            	<td >
                	<b>Acknowledgement Total Accept Amount:</b>
                </td>
                <td>
                	<?php
						echo $showCurrencySymbol.number_format(array_sum($total_amount['accepted']),2);//$strHL2AMTInProMonetaryAmount;
					?>
                </td>
                <td >
                	<b>Acknowledgement Total Rejected Amount</b>
                </td>
                <td colspan="3">
                	<?php
						echo $showCurrencySymbol.number_format(array_sum($total_amount['rejected']),2);//$strHL2AMTReturnedMonetaryAmount;
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	<b>Action Code:</b>
                </td>
                <td colspan="5">
                	<?php
						echo $strHL2InfoRecStatusCode;
					?>
                </td>       
            </tr>
        
        	<tr class="grythead">
            	<td colspan="6">Billing/Pay-To-Provider</td>
        	</tr>
            <tr>
            	<td width="10%">
                	<b>Name:</b>
                </td>
                <td width="27%">
                	<?php
					if(empty($strHL3RQName) == false){
						echo $strHL3RQName;
					}
					else{
						echo $strHL3RQLName.", ".$strHL3RQFName." ".$strHL3RQMName;
					}					
					?>
                </td>
                <td width="12%">                
                	<?php
						if(empty($strHL3NPI) == false){
							echo "<b>NPI:</b>";
						}
						elseif(empty($strHL3EmpIdNumber) == false){
							echo "<b>Employer's Identification Number:</b>";
						}
						elseif(empty($strHL3SSN) == false){
							echo "<b>Social Security Number:</b>";
						}
					?>
                </td>
                <td width="20%">
                	<?php
						if(empty($strHL3NPI) == false){
							echo $strHL3NPI;
						}
						elseif(empty($strHL3EmpIdNumber) == false){
							echo $strHL3EmpIdNumber;
						}
						elseif(empty($strHL3SSN) == false){
							echo $strHL3SSN;
						}
					?>
                </td>
                <td width="4%">
                	<b>Date:</b>
                </td>
                <td width="27%">
                	<?php
						echo $strHL3InfoRecStatusDate;
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	<b>Category:</b>
                </td>
                <td>
                	<?php
						echo $strHL3InfoRecCategory;
					?>
                </td>
                <td>
                	<b>Status:</b>
                </td>
                <td>
                	<?php
						echo $strHL3InfoRecStatus;
					?>
                </td>
                <td>
                	<b>Entity:</b>
                </td>
                <td>
                	<?php
						echo $strHL3InfoRecEntity;
					?>
                </td>
            </tr>
            <tr>
            	<td >
                	<b>Total Accepted Quantity:</b>
                </td>
                <td>
                	<?php
						echo count($total_amount['accepted']);//$strHL3QtyTotalAcceptedQuantity;
					?>
                </td>
                <td >
                	<b>Total Rejected Quantity:</b>
                </td>
                <td>
                	<?php
						echo count($total_amount['rejected']);//$strHL3QtyTotalRejectedQuantity;
					?>
                </td>                
                <td>
                </td>
                <td>
                </td>
            </tr>
            <tr>
            	<td>
                	<b>Total Accept Amount:</b>
                </td>
                <td>
                	<?php
						echo $showCurrencySymbol.number_format(array_sum($total_amount['accepted']),2);//$strHL3AMTInProMonetaryAmount;
					?>
                </td>
                <td>
                	<b>Total Rejected Amount:</b>
                </td>
                <td>
                	<?php
						echo $showCurrencySymbol.number_format(array_sum($total_amount['rejected']),2);//$strHL3AMTReturnedMonetaryAmount;
					?>
                </td>
                <td>
                </td>
                <td>
                </td>
            </tr>
            <tr>
            	<td>
                	<b>Action Code:</b>
                </td>
                <td colspan="5">
                	<?php
						echo $strHL3InfoRecStatusCode;
					?>
                </td>       
            </tr>
        </table> 
        <table class="table table-striped table-hover">
        	<tr class="grythead">
            	<td width="auto">Patient Level Claim(s)</td>
            	<td width="250" class="text-center sort_link">Sort:&nbsp; <a class="text_12b_purple" href="view_reports.php?id=<?php echo $_GET['id'];?>&page=<?php echo $_GET['page'];?>&process=<?php echo $_GET['process'];?>&sortOrder=<?php echo $_GET['sortOrder']==0 ? 1 : 0;?>">Accept/Reject</a></td>
        	</tr>
        </table>    
        <?php
		$contentsHTML = ob_get_contents();
		ob_end_clean();
		
		$strPDFPatData = "";	$Accept277 = $Reject277 = "";
		foreach($arr277CA as $intKey => $arrVal){
			$strPatName = $strPatMemberId = $strPatTRN = $strPatSTCcategory = $strPatSTCStatus = $strPatSTCEntity = $strPatSTCDate = $strPatSTCAction = $strPatSTCAmt = "";
			$strPatPayerClaimNumber = $strPatClaimNumber = $strPatBillingType = $strPatDTPQualifier = $strPatDTPDt1 = $strPatDTPDt2 = "";
			//pre($arrVal,1);	
			foreach($arrVal as $strKey => $arrValVal){
				switch($strKey){
					case "NM1":
					//pre($arrValVal,1);
					$strPatName = $arrValVal["patLname"].", ".$arrValVal["patFname"]." ".$arrValVal["patMname"];
					$strPatMemberId = $arrValVal["patMemberId"];
					break;
					case "TRN":
					//pre($arrValVal,1);
					$strPatTRN = $arrValVal["TRNTraceNumber"];
					break;
					case "STC":
					//pre($arrValVal,1);
					$strPatSTCcategory = $arrValVal["STCcategoryCode"];
					$strPatSTCStatus = $arrValVal["STCstatusCode"];
					$strPatSTCEntity = $arrValVal["STCEntityIdentifier"];
					$strPatSTCDate = $arrValVal["STCDate"];
					$strPatSTCAction = $arrValVal["STCActionCode"];
					$strPatSTCAmt = $arrValVal["STCAmount"];
					break;
					case "REF1K":
					//pre($arrValVal,1);
					$strPatPayerClaimNumber = $arrValVal["REFPayerClaimNumber"];
					break;
					case "REFD9":
					//pre($arrValVal,1);
					$strPatClaimNumber = $arrValVal["REFClaimNumber"];
					break;
					case "REFBLT":
					//pre($arrValVal,1);
					$strPatBillingType = $arrValVal["REFBillingType"];
					break;
					case "REFBLT":
					//pre($arrValVal,1);
					$strPatBillingType = $arrValVal["REFBillingType"];
					break;
					case "DTP":
					//pre($arrValVal,1);
					$strPatDTPQualifier = $arrValVal["DTPClaimDateTimeQualifier"];
					$strPatDTPDt1 = $arrValVal["DTPClaimDateTimePeriod1"];
					$strPatDTPDt2 = $arrValVal["DTPClaimDateTimePeriod2"];
					break;
				}
			}
			if(empty($strPatName) == false){
				ob_start();
				?>
                <table class="table table-striped table-hover">
                	<thead>
                    <tr class="bg-danger">
                    	<td width="10%">
                        	<b>Name:</b>
                        </td>
                        <td width="13%">
                        	<?php 
								echo $strPatName;
							?>
                        </td>
                        <td width="8%" valign="top">
                        	<b>Member ID:</b>
                        </td>
                        <td width="31%">
                        	<?php 
								echo $strPatMemberId;
							?>
                        </td>
                        <td width="17%">
                        	<b>Transaction Trace#:</b>
                        </td>
                        <td width="4%">
                        	<?php 
								echo $strPatTRN;
							?>
                        </td>
                        <td width="8%">
                        	<b>Date:</b>
                        </td>
                        <td width="9%">
                        	<?php 
								//By Karan
								if($global_date_format == "d-m-Y")
								{
									echo substr($strPatSTCDate,6,2).'-'.substr($strPatSTCDate,4,2).'-'.substr($strPatSTCDate,0,4);
								}	
								else
								{
									echo substr($strPatSTCDate,4,2).'-'.substr($strPatSTCDate,6,2).'-'.substr($strPatSTCDate,0,4);
								}	
									
								//echo substr($strPatSTCDate,4,2).'-'.substr($strPatSTCDate,6,2).'-'.substr($strPatSTCDate,0,4);
							?>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    	<td>
                        	<b>Amount:</b>
                        </td>
                        <td>
                        	<?php 
								echo $showCurrencySymbol.$strPatSTCAmt;
							?>
                        </td>
                        <td valign="top">
                        	<b>Status:</b>
                        </td>
                        <td>
                        	<?php 
								echo $strPatSTCStatus;
							?>
                        </td>
                        <td>
                        	<b>Entity:</b>
                        </td>
                        <td>
                        	<?php 
								echo $strPatSTCEntity;
							?>
                        </td>
                        <td>
                        	<b>Action:</b>
                        </td>
                        <td>
                        	<?php 
								echo $strPatSTCAction;
							?>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<b>Payer's Claim Number:</b>
                        </td>
                        <td>
                        	<?php 
								echo $strPatPayerClaimNumber;
							?>
                        </td>
                        <td>
                        	<b>Claim Number:</b>
                        </td>
                        <td>
                        	<?php 
								echo $strPatClaimNumber;
							?>
                        </td>
                        <td>
                        	<b>Billing Type:</b>
                        </td>
                        <td>
                        	<?php 
								echo $strPatBillingType;
							?>
                        </td>
                        <td>
                        	<b>Claim Service Date:</b>
                        </td>
                        <td>
                        	<?php 
								if(empty($strPatDTPDt2) == false){
									echo $strPatDTPDt1." - ".$strPatDTPDt2." (".$strPatDTPQualifier.")";
								}
								else{
									
									if($global_date_format == "d-m-Y")
									{
										echo substr($strPatDTPDt1,6,2).'-'.substr($strPatDTPDt1,4,2).'-'.substr($strPatDTPDt1,0,4)." (".$strPatDTPQualifier.")";
									}
									else
									{
										echo substr($strPatDTPDt1,4,2).'-'.substr($strPatDTPDt1,6,2).'-'.substr($strPatDTPDt1,0,4)." (".$strPatDTPQualifier.")";
									}
								
									//echo substr($strPatDTPDt1,4,2).'-'.substr($strPatDTPDt1,6,2).'-'.substr($strPatDTPDt1,0,4)." (".$strPatDTPQualifier.")";
								}
								
							?>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<b>Category:</b>
                        </td>
                        <td colspan="7">
                        	<?php 
								echo $strPatSTCcategory;
							?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php
				//
				$strPDFPatData .= "<table border=\"0\" style=\"width:100%\" cellpadding=\"0\" cellspacing=\"0\">
										<tr>
											<td style=\"width:100px; vertical-align:top;\">
												<b>Name:</b>
											</td>
											<td style=\"width:100px; vertical-align:top;\">".$strPatName."</td>";
						 $strPDFPatData .= "<td style=\"width:100px; vertical-align:top;\">
												<b>Member ID Number:</b>
											</td>
											<td style=\"width:100px; vertical-align:top;\">".$strPatMemberId."</td>";
						$strPDFPatData .= "<td style=\"width:100px; vertical-align:top;\" >
												<b>Referenced Transaction Trace Numbers:</b>
											</td>
											<td ststyle=\"width:50px; vertical-align:top;\">".$strPatTRN."</td>";
						$strPDFPatData .= "<td style=\"width:50px; vertical-align:top;\">
												<b>Date:</b>
											</td>
											<td style=\"width:100px; vertical-align:top;\" >".$strPatSTCDate."</td>";
						$strPDFPatData .= "</tr>
										<tr>
											<td style=\"width:100px; vertical-align:top;\">
												<b>Amount:</b>
											</td>
											<td style=\"width:100px; vertical-align:top;\">".$strPatSTCAmt."</td>";
						$strPDFPatData .= "<td style=\"width:100px; vertical-align:top;\">
												<b>Status:</b>
											</td>
											<td style=\"width:100px; vertical-align:top;\">".$strPatSTCStatus."</td>";
						$strPDFPatData .= "<td style=\"width:100px; vertical-align:top;\">
												<b>Entity:</b>
											</td>
											<td style=\"width:50px; vertical-align:top;\">".$strPatSTCEntity."</td>";
						$strPDFPatData .= "<td style=\"width:100px; vertical-align:top;\">
												<b>Action:</b>
											</td>
											<td style=\"width:100px; vertical-align:top;\">".$strPatSTCAction."</td>";
						$strPDFPatData .= "</tr>
										<tr>
											<td style=\"width:100px; vertical-align:top;\">
												<b>Payer's Claim Number:</b>
											</td>
											<td style=\"width:100px; vertical-align:top;\">".$strPatPayerClaimNumber."</td>";
						$strPDFPatData .= "<td style=\"width:100px; vertical-align:top;\">
												<b>Claim Number:</b>
											</td>
											<td style=\"width:100px; vertical-align:top;\">".$strPatClaimNumber."</td>";
						$strPDFPatData .= "<td style=\"width:100px; vertical-align:top;\">
												<b>Billing Type:</b>
											</td>
											<td style=\"width:50px; vertical-align:top;\">".$strPatBillingType."</td>";
						
						$strClaimSerDatePrint = "";
						if(empty($strPatDTPDt2) == false){
							$strClaimSerDatePrint = $strPatDTPDt1." - ".$strPatDTPDt2." (".$strPatDTPQualifier.")";
						}
						else{
							$strClaimSerDatePrint = $strPatDTPDt1." (".$strPatDTPQualifier.")";
						}
													
						$strPDFPatData .= "<td style=\"width:100px; vertical-align:top;\">
												<b>Claim Service Date:</b>
											</td>
											<td style=\"width:100px; vertical-align:top;\">".$strClaimSerDatePrint."</td>";
						$strPDFPatData .= "</tr>
										<tr>
											<td style=\"width:100px; vertical-align:top;\">
												<b>Category:</b>
											</td>
											<td colspan=\"7\" style=\"vertical-align:top;\">".$strPatSTCcategory."</td>";
						$strPDFPatData .= "</tr>
										<tr>
											<td colspan=\"8\" style=\"width:100%; height:1px; background-color:#FFC;\"></td>
										</tr>
									</table>";
				//
				
				if(strtolower($strPatSTCAction)=='accept'){
					$Accept277 .= ob_get_clean();
				}else{
					$Reject277 .= ob_get_clean();					
				}
			}
		}
		if($sortOrder==0) $contentsHTML .= $Reject277.$Accept277;
		else if($sortOrder==1) $contentsHTML .= $Accept277.$Reject277;
		
		//$contentsHTML = ob_get_contents();
		ob_end_clean();
		
		$strPDFData = "";
		$strPDFData = "<style>
						.text_b_w{
							font-size:11px;
							font-family:Arial, Helvetica, sans-serif;
							font-weight:bold;
							color:#FFFFFF;
							background-color:#4684ab;
						}
						.text_10b{
							font-size:11px;
							font-family:Arial, Helvetica, sans-serif;
							font-weight:bold;
							background-color:#FFFFFF;
						}
						.text_10{
							font-size:11px;
							font-family:Arial, Helvetica, sans-serif;
							background-color:#FFFFFF;
						}
						.bgcolor{	
							background-color:#FFFFFF;
						}
					</style>";
	$strPDFData .= "<page backtop=\"10mm\" backbottom=\"10mm\">	
						<page_header>
						<table style=\"width:100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
							<tr>
								<td style=\"width:40%\" class=\"text_b_w\">
									<b>Vision Share Details</b>
								</td>
								<td style=\"width:60%;\" class=\"text_b_w\">
									<b>Claim Financial Inquiry</b>
								</td>
							</tr>
						</table>						
						</page_header>
						<page_footer>
							<table style=\"width:100%;\">								
								<tr>
									<td style=\"text-align:center;width:100%\">Page [[page_cu]]/[[page_nb]]</td>
								</tr>
							</table>
						</page_footer>";
						
		$strHL1RQNamePrint = "";				
		if(empty($strHL1RQName) == false){
			$strHL1RQNamePrint = $strHL1RQName;
		}
		else{
			$strHL1RQNamePrint = $strHL1RQLName.", ".$strHL1RQFName." ".$strHL1RQMName;
		}	
						
		$strPDFData .= "<table border=\"0\" style=\"width:100%;\" cellpadding=\"0\" cellspacing=\"0\">
							<tr>
								<td colspan=\"4\" style=\"width:100%;\" class=\"text_b_w\">Information Source</td>
							</tr>
							<tr>
								<td style=\"width:65px; vertical-align:top;\" >
									<b>Name:</b>
								</td>
								<td style=\"width:150px; vertical-align:top;\" >".$strHL1RQNamePrint."</td>";
				$strPDFData .= "<td style=\"width:180px; vertical-align:top;\">
									<b>Current Transimission Trace Numbers:</b>
								</td>
								<td style=\"width:10px; vertical-align:top;\">".$strHL1TRNNumber."</td>";
				$strPDFData .= "</tr>
							<tr>
								<td style=\"width:65px; vertical-align:top;\">
									<b>Receipt Date:</b>
								</td>
								<td style=\"width:150px; vertical-align:top;\">".$strHL1ReceivedDate."</td>";
				$strPDFData .= "<td style=\"width:150px; vertical-align:top;\">
									<b>Process Date:</b>
								</td>
								<td style=\"width:10px; vertical-align:top;\">".$strHL1ProcessDate."</td>";
				$strPDFData .= "</tr>
						</table>";
		
		$strHL2RQNamePrint = "";				
		if(empty($strHL2RQName) == false){
			$strHL2RQNamePrint = $strHL2RQName;
		}
		else{
			$strHL2RQNamePrint = $strHL2RQLName.", ".$strHL2RQFName." ".$strHL2RQMName;
		}			
				
		$strPDFData .= 	"<table border=\"0\" style=\"width:100%\" cellpadding=\"0\" cellspacing=\"0\">
							<tr >
								<td colspan=\"6\" style=\"width:100%\" class=\"text_b_w\">Information Receiver</td>
							</tr>
							<tr>
								<td style=\"width:65px; vertical-align:top;\" >
									<b>Name:</b>
								</td>
								<td style=\"width:20px; vertical-align:top;\">".$strHL2RQNamePrint."</td>";
				$strPDFData .= "<td style=\"width:25px; vertical-align:top;\">
									<b>Entity:</b>
								</td>
								<td style=\"width:20px; vertical-align:top;\" >".$strHL2InfoRecEntity."</td>";
				$strPDFData .= "<td style=\"width:25px; vertical-align:top;\" >
									<b>Date:</b>
								</td>
								<td style=\"width:20px; vertical-align:top;\">".$strHL2InfoRecStatusDate."</td>";
				$strPDFData .= "</tr>
							<tr>
								<td style=\"width:65px; vertical-align:top;\">
									<b>Category:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL2InfoRecCategory."</td>";
				$strPDFData .= "<td style=\"width:25px; vertical-align:top;\">
									<b>Status:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL2InfoRecStatus."</td>";
				$strPDFData .= "<td style=\"width:25px; vertical-align:top;\">
									<b>Total Submitted Charges:</b>
								</td>
								<td style=\"width:20px; vertical-align:top;\">".$strHL2InfoRecAmount."</td>";
				$strPDFData .= "</tr>
							<tr>
								<td style=\"width:75px; vertical-align:top;\">
									<b>Acknowledgement Total Accepted Quantity:</b>
								</td>
								<td style=\"width:20px; vertical-align:top;\">".$strHL2QtyAckQtyQuantity."</td>";
				$strPDFData .= "<td style=\"width:75px; vertical-align:top;\">
									<b>Acknowledgement Total Rejected Quantity:</b>
								</td>
								<td style=\"width:20px; vertical-align:top;\">".$strHL2QtyUnAckQtyQuantity."</td>";
				$strPDFData .= "<td >
								</td>
								<td>
								</td>                
							</tr>
							<tr>
								<td style=\"width:75px; vertical-align:top;\">
									<b>Acknowledgement Total Accept Amount:</b>
								</td>
								<td style=\"width:20px; vertical-align:top;\">".$strHL2AMTInProMonetaryAmount."</td>";
				$strPDFData .= "<td style=\"width:75px; vertical-align:top;\">
									<b>Acknowledgement Total Rejected Amount</b>
								</td>
								<td style=\"width:20px; vertical-align:top;\">".$strHL2AMTReturnedMonetaryAmount."</td>";
				$strPDFData .= "<td>
								</td>                
							</tr>
							<tr>
								<td style=\"width:75px; vertical-align:top;\">
									<b>Action Code:</b>
								</td>
								<td colspan=\"5\" style=\"width:20px; vertical-align:top;\">".$strHL2InfoRecStatusCode."</td>";
				$strPDFData .= "</tr>
						</table>";
		
		$strHL3RQNamePrint = "";
		if(empty($strHL3RQName) == false){
			$strHL3RQNamePrint = $strHL3RQName;
		}
		else{
			$strHL3RQNamePrint = $strHL3RQLName.", ".$strHL3RQFName." ".$strHL3RQMName;
		}
									
		$strPDFData .= "<table border=\"0\" style=\"width:100%\" cellpadding=\"0\" cellspacing=\"0\">
							<tr>
								<td colspan=\"6\" style=\"width:100%\" class=\"text_b_w\">Billing/Pay-To-Provider</td>
							</tr>
							<tr>
								<td style=\"width:100px; vertical-align:top;\" >
									<b>Name:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL3RQNamePrint."</td>";
				$tempCap = $tempCapVal = "";
				if(empty($strHL3NPI) == false){
					$tempCap = "<b>NPI:</b>";
				}
				elseif(empty($strHL3EmpIdNumber) == false){
					$tempCap = "<b>Employer's Identification Number:</b>";
				}
				elseif(empty($strHL3SSN) == false){
					$tempCap = "<b>Social Security Number:</b>";
				}
				if(empty($strHL3NPI) == false){
					$tempCapVal = $strHL3NPI;
				}
				elseif(empty($strHL3EmpIdNumber) == false){
					$tempCapVal = $strHL3EmpIdNumber;
				}
				elseif(empty($strHL3SSN) == false){
					$tempCapVal = $strHL3SSN;
				}
				$strPDFData .= "<td style=\"width:100px; vertical-align:top;\">".$tempCap."</td>";
				$strPDFData .= "<td style=\"width:100px; vertical-align:top;\" >".$tempCapVal."</td>";
				$strPDFData .= "<td style=\"width:100px; vertical-align:top;\" >
									<b>Date:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\" >".$strHL3InfoRecStatusDate."</td>";
				$strPDFData .= "</tr>
							<tr>
								<td style=\"width:100px; vertical-align:top;\">
									<b>Category:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL3InfoRecCategory."</td>";
				$strPDFData .= "<td style=\"width:100px; vertical-align:top;\">
									<b>Status:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL3InfoRecStatus."</td>";
				$strPDFData .= "<td style=\"width:100px; vertical-align:top;\">
									<b>Entity:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL3InfoRecEntity."</td>";
				$strPDFData .= "</tr>
							<tr>
								<td style=\"width:100px; vertical-align:top;\">
									<b>Total Accepted Quantity:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL3QtyTotalAcceptedQuantity."</td>";
				$strPDFData .= "<td style=\"width:100px; vertical-align:top;\">
									<b>Total Rejected Quantity:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL3QtyTotalRejectedQuantity."</td>";
				$strPDFData .= "<td>
								</td>
								<td>
								</td>
							</tr>
							<tr>
								<td style=\"width:100px; vertical-align:top;\">
									<b>Total Accept Amount:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL3AMTInProMonetaryAmount."</td>";
				$strPDFData .= "<td style=\"width:100px; vertical-align:top;\">
									<b>Total Rejected Amount:</b>
								</td>
								<td style=\"width:100px; vertical-align:top;\">".$strHL3AMTReturnedMonetaryAmount."</td>";
				$strPDFData .= "<td>
								</td>
								<td>
								</td>
							</tr>
							<tr>
								<td style=\"width:100px; vertical-align:top;\">
									<b>Action Code:</b>
								</td>
								<td colspan=\"5\" style=\"width:100px; vertical-align:top;\">".$strHL3InfoRecStatusCode."</td>";
				$strPDFData .= "</tr>
						</table>";
						
		$strPDFData .= "<table border=\"0\" style=\"width:100%\" cellpadding=\"0\" cellspacing=\"0\">
						<tr >
							<td style=\"width:100%\" class=\"text_b_w\">Patient Level Claim(s)</td>
						</tr>
					</table>";				
		$strPDFData = $strPDFData.$strPDFPatData."</page>";
		$htmlFileNameCA = "print_pdf_file_".$_SESSION['authId'].session_id();
		$htmlFilePathCA = $GLOBALS['fileroot'].'/interface/common/new_html2pdf/'.$htmlFileNameCA.".html";
		file_put_contents($htmlFilePathCA,$strPDFData);
		return array($contentsHTML, $htmlFileNameCA);
	}
	
	function process997($fileData, $filePath, $rqVSRecId){
		$fileData = trim($fileData);
		$insert_id_arr = $fileDataArrMain = array();
		$fileDataArrMain = preg_split('/ISA/',$fileData);				
		if($fileDataArrMain[0] == ''){
			array_shift($fileDataArrMain);					
		}	
		//pre($fileDataArrMain);
		for($counter=0; $counter < count($fileDataArrMain); $counter++){
			$fileData = 'ISA'.$fileDataArrMain[$counter];	
			$fileData = preg_replace('/[^0-9A-Za-z*~:]/','',$fileData);			
			$dataArr = preg_split('/(AK2\*837\*)|(AK5\*)|(AK9\*)/',$fileData);			
			$arrDateEDITemp = explode("~", $dataArr[0]);
			$arrDateEDI = array();
			$strEDIDate = $strEDIYear = $strEDIMonth = $strEDIDate = $strEDIDateDt = "";
			$arrDateEDI = explode("*", $arrDateEDITemp[0]);
			$strEDIDate = $arrDateEDI[9];
			$strEDIYear = "20".trim($strEDIDate[0]).trim($strEDIDate[1]);
			$strEDIMonth = trim($strEDIDate[2]).trim($strEDIDate[3]);
			$strEDIDate = trim($strEDIDate[4]).trim($strEDIDate[5]);
			if(checkdate( $strEDIMonth, $strEDIDate, $strEDIYear ) == true){
				$strEDIDateDt = $strEDIYear.'-'.$strEDIMonth.'-'.$strEDIDate;	
			}
			else{
				$strEDIDateDt = date("Y-m-d");
			}
			$trans_data_arr = preg_split('/~/',$dataArr[1]);
			$setNumberData = (int)$trans_data_arr[0];
			$status = $vsStatus = 0;			
			if((strtoupper($dataArr[2][0]) == 'A') || (strtoupper($dataArr[3][0]) == 'A')){
				$status = 1;
				$vsStatus = 1;
			}
			else if((strtoupper($dataArr[2][0]) == 'E') || (strtoupper($dataArr[3][0]) == 'E')){
				$status = 2;
				$vsStatus = 1;
			}
			else if((strtoupper($dataArr[2][0]) == 'R') || (strtoupper($dataArr[3][0]) == 'R')){
				$vsStatus = 2;				
			}

			//---- CHANGE 837 FILE STATUS -----------
			if($setNumberData > 0){
				$qry = "update batch_file_submitte set status = '".$status."' where Transaction_set_unique_control = '".$setNumberData."'";				
				$rsQry = imw_query($qry);				
				$qryUpdateFileData = "update vision_share_batch_receive_list set process_997 = '".$vsStatus."' where id = '".$rqVSRecId."'";
				$rsUpdateFileData = imw_query($qryUpdateFileData);
				//die($qryUpdateFileData);
				//--- FILE ALREADY UPLOADED EXISTS CHECK ----
				$qryCountQryRes = "select count(*) as rowCount from electronic_997_file	where file_status = '1' and set_number_id = '$setNumberData'";
				$rsCountQryRes = imw_query($qryCountQryRes);
				$countQryRes = imw_fetch_array($rsCountQryRes);
				
				if($countQryRes['rowCount'] == 0){
					if($status == 1 || $status == 2){						
						$selQry = "select encounter_id,ins_comp from batch_file_submitte where Transaction_set_unique_control = '$setNumberData'";
						$rsQry = imw_query($selQry);
						if(imw_num_rows($rsQry) > 0){
							$rowQry = imw_fetch_array($rsQry);
							$allEncounter = explode(',',$rowQry['encounter_id']);
							foreach($allEncounter as $encounter){
								$selQryPCL = "select * from patient_charge_list where del_status='0' and encounter_id = '$encounter'";
								$rsQryPCL = imw_query($selQryPCL);
								if(imw_num_rows($rsQryPCL) > 0){
									$rowQryPCL = imw_fetch_array($rsQryPCL);
									if($rowQryPCL["encounter_id"] > 0){	
										if($rowQry['ins_comp'] == 'primary'){
											$insCompId = $rowQryPCL["primaryInsuranceCoId"];
										}
										else{
											$insCompId = $rowQryPCL["secondaryInsuranceCoId"];
										}									
										$qryInsert = "insert into submited_record 
														(encounter_id, patient_id, Ins_type, Ins_company_id, posted_amount, operator_id, submited_date) 
														values ('".htmlentities(addslashes(trim($encounter)))."',
																'".htmlentities(addslashes(trim($rowQryPCL["patient_id"])))."',
																'".htmlentities(addslashes(trim($rowQry['ins_comp'])))."',
																'".htmlentities(addslashes(trim($insCompId)))."',
																'".htmlentities(addslashes(trim($rowQryPCL["totalBalance"])))."',
																'".htmlentities(addslashes(trim($_SESSION['authUserID'])))."',
																'".htmlentities(addslashes(trim($strEDIDateDt)))."'
																)";														
										$rsInsert = imw_query($qryInsert);
										$insertIds = imw_insert_id();
									}	
								}
							}
						}
					}
					
					$qryInsert = "insert into electronic_997_file 
									(File_name, set_number_id, file_data, file_upload_date, operator_id, file_status) 
									values ('".htmlentities(addslashes(trim($filePath)))."',
											'".htmlentities(addslashes(trim($setNumberData)))."',
											'".htmlentities(addslashes(trim($fileData)))."',
											'".htmlentities(addslashes(trim($strEDIDateDt)))."',
											'".htmlentities(addslashes(trim($_SESSION['authId'])))."',
											'".htmlentities(addslashes(trim($status)))."'
											)";		
					$rsInsert = imw_query($qryInsert);
					$insertId = imw_insert_id();
					$insert_id_arr[] = $insertId;
					####
					$insert_id_str = join(',',$insert_id_arr);
					if($vsStatus = 2){
						$qryGetData = "select * from electronic_997_file where File_id in ($insert_id_str) order by File_id desc";
						$rsGetData = imw_query($qryGetData);
						$data = '';
						while($rowGetData = imw_fetch_array($rsGetData)){
							$set_number_id = $rowGetData['set_number_id'];
							$getFileData = $rowGetData['file_data'];
							$fileDataArr = explode('~',$getFileData);						
							$qryGetDataBFS = "select * from batch_file_submitte where Transaction_set_unique_control = '".$set_number_id."'";
							$rsGetDataBFS = imw_query($qryGetDataBFS);
							if($rsGetDataBFS){
								$rowGetDataBFS = imw_fetch_array($rsGetDataBFS);
							}
							$errorDetailArr = explode('AK',$getFileData);
							$errorString = array();
							foreach($errorDetailArr as $val){
								$firstVal = substr($val,0,1);		
								if($firstVal > 2 && $firstVal < 5){
									$errorString[] = 'AK'.$val;
								}
							}
							if($rowGetData['file_status'] == 0){
								$getFileEncounterIds = explode(',',$rowGetDataBFS["encounter_id"]);
								$getFileSegmentStarts = explode(',',$rowGetDataBFS["segment_start"]);
								$getFileSegmentEnds = explode(',',$rowGetDataBFS["segment_end"]);
								
								foreach($errorString as $val){
									$patientName = '';
									$fileEncounterId = 0;
									$errors = '';
									$segmentString = '';
									$errorArr = explode('AK3*',$val);
									$segment_count = 0;	
									if(count($errorArr)>1){				
										if($segment_count == 0){
											$error_arr = explode('DMG*',$errorArr[1]);
											if(count($error_arr)>1){
												$segment_count = substr($error_arr[1],0,strpos($error_arr[1],'*'));
												$errors = ' Gender information is missing.';
												$error_arr[0] = 'AK3*DMG*';
											}
										}
										
										if($segment_count == 0){
											$error_arr = explode('N3*',$errorArr[1]);
											if(count($error_arr)>1){
												$segment_count = substr($error_arr[1],0,strpos($error_arr[1],'~'));
												$errors = ' Pos Facility Address is missing.';
												$error_arr[0] = 'AK3*N3*';
											}
										}
										
										if($segment_count == 0){
											$error_arr = explode('N4*',$errorArr[1]);
											if(count($error_arr)>1){
												$segment_count = substr($error_arr[1],0,strpos($error_arr[1],'~'));
												$errors = ' Pos Facility City, State Zip code is missing.';
												$error_arr[0] = 'AK3*N4*';
											}
										}
										
										if($segment_count == 0){
											$error_arr = explode('NM1*',$errorArr[1]);
											if(count($error_arr)>1){
												$segment_count = substr($error_arr[1],0,strpos($error_arr[1],'~'));
												$errors = ' Pos Facility Name is missing.';
												$error_arr[0] = 'AK3*NM1*';
											}
										}
										
										//--- Get Encounter Id For Single Segment --------
										if($segment_count > 0){
											$segmentCount = $segment_count + 2;
											$segmentStr = str_replace($segment_count,$segmentCount,$error_arr[1]);
											$segmentString = $error_arr[0].$segmentStr;
											for($s=0;$s<count($getFileSegmentStarts);$s++){
												$segStart = $getFileSegmentStarts[$s];
												$segEnd = $getFileSegmentEnds[$s];
												if(($segment_count >= $segStart) && ($segment_count <= $segEnd)){
													$fileEncounterId = $getFileEncounterIds[$s];
												}				
											}
										}
										
										//------- Get Patient Detai;s For Single Encounter Id ------
										if($fileEncounterId > 0){
											$qry = "select patient_data.fname,patient_data.lname,patient_data.mname,
													patient_data.id
													from patient_charge_list join patient_data on
													patient_data.id = patient_charge_list.patient_id
													where patient_charge_list.del_status='0' and patient_charge_list.encounter_id = $fileEncounterId
													group by patient_charge_list.encounter_id";
											$qryRs =  imw_query($qry);
											while($rowQryRs = imw_fetch_array($qryRs)){
												$patientName = $rowQryRs['lname'].', ';
												$patientName .= $rowQryRs['fname'].' ';
												$patientName .= $rowQryRs['mname'];
												$patientId = $rowQryRs['id'];
											}												
											$patientName = $patientName.'-'.$patientId;					
										}
									}
									if($segment_count == 0){
										$errorArr = explode('AK4*',$val);
										if(count($errorArr)>1){				
											if($segment_count == 0){
												$error_arr = explode('DMG*',$errorArr[1]);
												if(count($error_arr)>1){
													$segment_count = substr($error_arr[1],0,strpos($error_arr[1],'*'));
													$errors = ' Gender information is missing.';
													$error_arr[0] = 'AK3*DMG*';
												}
											}
											
											if($segment_count == 0){
												$error_arr = explode('N3*',$errorArr[1]);
												if(count($error_arr)>1){
													$segment_count = substr($error_arr[1],0,strpos($error_arr[1],'~'));
													$errors = ' Pos Facility Address is missing.';
													$error_arr[0] = 'AK3*N3*';
												}
											}
											
											if($segment_count == 0){
												$error_arr = explode('N4*',$errorArr[1]);
												if(count($error_arr)>1){
													$segment_count = substr($error_arr[1],0,strpos($error_arr[1],'~'));
													$errors = ' Pos Facility City, State Zip code is missing.';
													$error_arr[0] = 'AK3*N4*';
												}
											}
											
											if($segment_count == 0){
												$error_arr = explode('NM1*',$errorArr[1]);
												if(count($error_arr)>1){
													$segment_count = substr($error_arr[1],0,strpos($error_arr[1],'~'));
													$errors = ' Pos Facility Name is missing.';
													$error_arr[0] = 'AK3*NM1*';
												}
											}
											
											//--- Get Encounter Id For Single Segment --------
											if($segment_count > 0){
												$segmentCount = $segment_count + 2;
												$segmentStr = str_replace($segment_count,$segmentCount,$error_arr[1]);
												$segmentString = $error_arr[0].$segmentStr;
												for($s=0;$s<count($getFileSegmentStarts);$s++){
													$segStart = $getFileSegmentStarts[$s];
													$segEnd = $getFileSegmentEnds[$s];
													if(($segment_count >= $segStart) && ($segment_count <= $segEnd)){
														$fileEncounterId = $getFileEncounterIds[$s];
													}				
												}
											}
											
											//------- Get Patient Detais For Single Encounter Id ------
											if($fileEncounterId > 0){
												$qry = "select patient_data.fname,patient_data.lname,patient_data.mname,
														patient_data.id
														from patient_charge_list join patient_data on
														patient_data.id = patient_charge_list.patient_id
														where patient_charge_list.del_status='0' and patient_charge_list.encounter_id = $fileEncounterId
														group by patient_charge_list.encounter_id";
												$qryRs =  imw_query($qry);
												while($rowQryRs = imw_fetch_array($qryRs)){
													$patientName = $rowQryRs['lname'].', ';
													$patientName .= $rowQryRs['fname'].' ';
													$patientName .= $rowQryRs['mname'];
													$patientId = $rowQryRs['id'];
												}												
												$patientName = $patientName.'-'.$patientId;													
											}
										}
									}
									if($fileEncounterId > 0){
										$data1 .= '
											<tr bgcolor="#FFFFFF">
												<td align="left" width="50" class="text_10">'.$fileEncounterId.'</td>
												<td align="left" width="200" class="text_10">'.trim($patientName).'</td>
												<td align="left" class="text_10" colspan="2">'.$errors.'</td>
												<td align="left" width="100" class="text_10">'.$segmentString.'</td>
											</tr>
										';
									}
									
								}
								
								if($data1){
									$data2 .= '
										<tr bgcolor="#FFFFFF">
											<td colspan="4">
												<table width="100%" bgcolor="#FFF3E8">
													<tr bgcolor="#FFFFFF">
														<td align="center" width="50" class="text_10b">E-Id</td>
														<td align="center" width="250" class="text_10b">Patient Name</td>
														<td align="center" class="text_10b" colspan="2">Reasons</td>
														<td align="center" width="100" class="text_10b">Segment</td>
													</tr>
													'.$data1.'
												</table>
											</td>
										</tr>
									';
								}
								else{
									$data2 = '
										<tr bgcolor="#FFFFFF">
											<td align="center" width="50" class="text_10b"></td>
											<td align="center" class="text_10b" colspan="2">Tx Incomplete</td>
											<td align="center" width="100" class="text_10b"></td>
										</tr>
									';
								}
							}
						}
						$tempData = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
						$tempData .= $data2;;
						$tempData .= "</table>";
						$qryUpdateFileData1 = "update vision_share_batch_receive_list set process_error_report_997 = '".htmlentities(addslashes(trim($tempData)))."' where id = '".$rqVSRecId."'";
						$rsUpdateFileData1 = imw_query($qryUpdateFileData1);
					}
				}
			}
		}
	}
	function makeSpace($noOfSpace,$spaceSTR = " "){
		$noOfSpace = (int)$noOfSpace;
		$blankSpace = "";
		for($a = 1; $a <= $noOfSpace; $a++){
			$blankSpace .= $spaceSTR;
		}
		return $blankSpace;
	}
	function make270EDI($rqInsRecId, $from = "insTab", $patId = 0, $elReqDate = "", $intInsCaseId = 0){
		$retError = array();
		//Getting default group of head quarter facility
		$dbFacGroupID = 0;
		$qryGetDefaultGroup = "select default_group as facGroup from facility where facility_type = '1'";
		$rsGetDefaultGroup = imw_query($qryGetDefaultGroup);
		if($rsGetDefaultGroup){
			if(imw_num_rows($rsGetDefaultGroup) > 0){		
				$rowGetDefaultGroup = imw_fetch_array($rsGetDefaultGroup);
				$dbFacGroupID = $rowGetDefaultGroup["facGroup"];		
			}
			imw_free_result($rsGetDefaultGroup);
		}
		if($dbFacGroupID > 0){
			//Getting group Medicare Receiver Id and Medicare Submitter Id 
			$dbMedicareReceiverId = $dbMedicareSubmitterId = $dbGroupName = $dbGroupNPI = $dbGroupFederalTaxID = $dbGroupAdd1 = $dbGroupAdd2 = $dbGroupCity = $dbGroupState = $dbGroupZip = "";
			$dbGroupConName = $dbGroupTelephone = $dbGroupFax = $dbGroupEmail = "";
			$qryGetMedRecSubID = "select name as groupName, group_NPI as groupNPI, group_Federal_EIN as groupFederalTaxID, group_Address1 as groupAdd1, group_Address2 as groupAdd2, group_City as groupCity, 
									group_State as groupState, group_Zip as groupZip, Contact_Name as groupConName, replace(group_Telephone,'-','') as groupTelephone, replace(group_Fax,'-','') as groupFax, group_Email as groupEmail, 
									MedicareReceiverId, MedicareSubmitterId from groups_new where gro_id = '".$dbFacGroupID."'";
			$rsGetMedRecSubID = imw_query($qryGetMedRecSubID);
			if($rsGetMedRecSubID){
				if(imw_num_rows($rsGetMedRecSubID) > 0){		
					$rowGetMedRecSubID = imw_fetch_array($rsGetMedRecSubID);
					$dbMedicareReceiverId = trim($rowGetMedRecSubID["MedicareReceiverId"]);
					$dbMedicareSubmitterId = trim($rowGetMedRecSubID["MedicareSubmitterId"]);
					$dbGroupName = trim($rowGetMedRecSubID["groupName"]);
					$dbGroupName = substr($dbGroupName, 0, 21);
					$dbGroupNPI = trim($rowGetMedRecSubID["groupNPI"]);
					$dbGroupFederalTaxID = trim($rowGetMedRecSubID["groupFederalTaxID"]);
					$dbGroupAdd1 = trim($rowGetMedRecSubID["groupAdd1"]);
					$dbGroupAdd2 = trim($rowGetMedRecSubID["groupAdd2"]);
					$dbGroupCity = trim($rowGetMedRecSubID["groupCity"]);
					$dbGroupState = trim($rowGetMedRecSubID["groupState"]);
					$dbGroupZip = trim($rowGetMedRecSubID["groupZip"]);
					$dbGroupConName = trim($rowGetMedRecSubID["groupConName"]);
					$dbGroupTelephone = trim($rowGetMedRecSubID["groupTelephone"]);
					$dbGroupFax = trim($rowGetMedRecSubID["groupFax"]);
					$dbGroupEmail = trim($rowGetMedRecSubID["groupEmail"]);				
				}
				imw_free_result($rsGetDefaultGroup);
			}
			if((empty($dbMedicareReceiverId) == false) && (empty($dbMedicareSubmitterId) == false)){
				$authorInfo = $secretInfo = $strNewRTMEMAXID = $strNewRTMEMAXIDRest = "";
				$medRecIdRest = $medSubIdRest = $newRTMEMAXID = 0;				
				
				$authorInfo = $this->makeSpace(10);
				$secretInfo = $this->makeSpace(10);
				$interchangeReceiverID = "CMS";
				$medRecIdRest = 15 - strlen($interchangeReceiverID);
				$recieveSpace = "";
				$recieveSpace = $this->makeSpace($medRecIdRest);				
				$medSubIdRest = 15 - strlen($dbMedicareSubmitterId);
				$submitterSpace = "";
				$submitterSpace = $this->makeSpace($medSubIdRest);
				$qryGetMaxRealTimeId = "select max(id) as RTMEMAXID from real_time_medicare_eligibility";
				$rsGetMaxRealTimeId =  imw_query($qryGetMaxRealTimeId);
				if($rsGetMaxRealTimeId){
					$rowGetMaxRealTimeId = imw_fetch_array($rsGetMaxRealTimeId);
					$newRTMEMAXID = $rowGetMaxRealTimeId["RTMEMAXID"];
					imw_free_result($rsGetMaxRealTimeId);
					$newRTMEMAXID++;
					$strNewRTMEMAXID = (string)$newRTMEMAXID;
					$strNewRTMEMAXIDRest = 9 - strlen($strNewRTMEMAXID);
					$InterCtrlNumber = "";
					$InterCtrlNumber = $this->makeSpace($strNewRTMEMAXIDRest,"0");
					$InterCtrlNumber = $InterCtrlNumber.$strNewRTMEMAXID;
				}
				$dbinsCompName = $dbSubscriberLName = $dbSubscriberFName = $dbSubscriberMName = $dbInsPolicyClaimNo = $dbSubscriberAdd1 = $dbSubscriberAdd2 = "";
				$dbSubscriberCity = $dbSubscriberState = $dbSubscriberZip = $dbSubscriberDOB = $dbSubscriberGender = $dbSubscriberDate = $dbSubscriberSS = "";
				$dbInsPatId = $dbInsId = 0;
				if($from == "insTab"){
					$getPatInsData = "select insComp.name as insCompName, insData.subscriber_lname as subscriberLName, insData.subscriber_fname as subscriberFName,
										insData.subscriber_mname as subscriberMName, insData.policy_number as insPolicyClaimNo, insData.subscriber_street as subscriberAdd1, 
										insData.subscriber_street_2 as subscriberAdd2, insData.subscriber_city as subscriberCity, insData.subscriber_state as subscriberState, 
										insData.subscriber_postal_code as subscriberZip, replace(DATE_FORMAT(insData.subscriber_DOB, '%Y%m%d'),'-','') as subscriberDOB, insData.subscriber_sex as subscriberGender, 
										replace(DATE_FORMAT(insData.effective_date, '%Y%m%d'),'-','') as subscriberDate, insData.pid as insPatId, insData.id as insId,
										replace(insData.subscriber_ss,'-','') as subscriberSS									 
										from insurance_data insData 
										LEFT JOIN insurance_companies insComp on insComp.id = insData.provider
										where insData.id = '".$rqInsRecId."' 
										LIMIT 1;
										";
				}
				elseif($from == "scheduler"){
					$getPatInsData = "select insComp.name as insCompName, insData.subscriber_lname as subscriberLName, insData.subscriber_fname as subscriberFName,
										insData.subscriber_mname as subscriberMName, insData.policy_number as insPolicyClaimNo, insData.subscriber_street as subscriberAdd1, 
										insData.subscriber_street_2 as subscriberAdd2, insData.subscriber_city as subscriberCity, insData.subscriber_state as subscriberState, 
										insData.subscriber_postal_code as subscriberZip, replace(DATE_FORMAT(insData.subscriber_DOB, '%Y%m%d'),'-','') as subscriberDOB, 
										insData.subscriber_sex as subscriberGender, replace(DATE_FORMAT(insData.effective_date, '%Y%m%d'),'-','') as subscriberDate, 
										insData.pid as insPatId, insData.id as insId, replace(insData.subscriber_ss,'-','') as subscriberSS									 
										from insurance_data insData 
										LEFT JOIN insurance_companies insComp on insComp.id = insData.provider
										where insData.pid = '".$patId."' and insData.actInsComp = '1' and insData.subscriber_relationship = 'self' ";
										if((int)$intInsCaseId > 0){
											$getPatInsData .= " and insData.ins_caseid = '".$intInsCaseId."' ";
										} 
										$getPatInsData .= " and insData.type = 'primary'
										and insComp.claim_type = '1' 
										ORDER BY insData.id DESC LIMIT 1;
										";
				}
				$rsPatInsData = imw_query($getPatInsData);
								
				if($rsPatInsData){
					if(imw_num_rows($rsPatInsData) > 0){
						$rowPatInsData = imw_fetch_array($rsPatInsData);
						$dbinsCompName = trim($rowPatInsData["insCompName"]);
						$dbSubscriberLName = trim($rowPatInsData["subscriberLName"]);
						$dbSubscriberFName = trim($rowPatInsData["subscriberFName"]);
						$dbSubscriberMName = trim($rowPatInsData["subscriberMName"]);
						$dbInsPolicyClaimNo = trim($rowPatInsData["insPolicyClaimNo"]);
						$dbInsPolicyClaimNo = str_replace(array("-"," "), "", $dbInsPolicyClaimNo);
						$dbSubscriberAdd1 = trim($rowPatInsData["subscriberAdd1"]);
						$dbSubscriberAdd2 = trim($rowPatInsData["subscriberAdd2"]);
						$dbSubscriberCity = trim($rowPatInsData["subscriberCity"]);
						$dbSubscriberState = trim($rowPatInsData["subscriberState"]);
						$dbSubscriberZip = trim($rowPatInsData["subscriberZip"]);
						$dbSubscriberDOB = trim($rowPatInsData["subscriberDOB"]);
						$dbSubscriberGender = trim($rowPatInsData["subscriberGender"]);
						$dbSubscriberDate = trim($rowPatInsData["subscriberDate"]);
						$dbInsPatId = trim($rowPatInsData["insPatId"]);
						$dbInsId = trim($rowPatInsData["insId"]);
						$dbSubscriberSS = trim($rowPatInsData["subscriberSS"]);																		
						if(empty($dbSubscriberSS) == false){
							$ediDate1 = $ediDate2 = $ediTime = $ediTime1 = "";
							$ediDate1 = date('ymd');
							$ediDate2 = date('Ymd');
							$ediTime = date('Hi');
							$ediTime1 = date('His');
							$edi270CrlHeader = $edi270CrlTrailer = "";
							$edi270FunGrpHeader = $edi270FunGrpTrailer = "";
							$edi270CrlHeader = "ISA*00*".$authorInfo."*00*".$secretInfo."*ZZ*".$dbMedicareSubmitterId.$submitterSpace."*ZZ*".$interchangeReceiverID.$recieveSpace."*".$ediDate1."*".$ediTime."*U*00401*".$InterCtrlNumber."*0*P*:~";
							$edi270CrlTrailer = "IEA*1*".$InterCtrlNumber."~";
							$edi270FunGrpHeader = "GS*HS*".$dbMedicareSubmitterId."*".$interchangeReceiverID."*".$ediDate2."*".$ediTime1."*".$newRTMEMAXID."*X*004010X092A1~";
							$edi270FunGrpTrailer = "GE*1*".$newRTMEMAXID."~";
							$edi270TranData = "";
							$edi270STCounter = 0;
							$edi270TranData = "ST*270*".$InterCtrlNumber."~"; //TRANSACTION SET HEADER
							$edi270STCounter++;
							$edi270TranData .=	"BHT*0022*13*ALL*".$ediDate2."*".$ediTime."~"; //BEGINNING OF HIERARCHICAL TRANSACTION
							$edi270STCounter++;
							$edi270TranData .=	"HL*1**20*1~"; //Loop: 2000A
							$edi270STCounter++;
							$edi270TranData .=	"NM1*PR*2*".$interchangeReceiverID."*****PI*".$interchangeReceiverID."~"; //Loop: 2100A
							$edi270STCounter++;
							$edi270TranData .=	"HL*2*1*21*1~"; //Loop: 2000B
							$edi270STCounter++;
							$edi270TranData .=	"NM1*1P*2*".$dbGroupName."*****XX*".$dbGroupNPI."~"; //Loop: 2100B	
							//$edi270TranData .=	"NM1*1P*2*".$dbGroupName."*****XX*9999999997~"; //Loop: 2100B	
							
							$edi270STCounter++;
							if(empty($dbGroupFederalTaxID) == false){
								$edi270TranData .=	"REF*TJ*".$dbGroupFederalTaxID."~"; //Loop: 2100B ->INFORMATION RECEIVER ADDITIONAL IDENTIFICATION
								$edi270STCounter++;
							}
							if((empty($dbGroupAdd1) == false) && (empty($dbGroupAdd2) == false)){
								$edi270TranData .=	"N3*".$dbGroupAdd1."*".$dbGroupAdd2."~"; //Loop: 2100B -> INFORMATION RECEIVER ADDRESS	
								$edi270STCounter++;
							}
							else{
								$edi270TranData .=	"N3*".$dbGroupAdd1."~"; //Loop: 2100B -> INFORMATION RECEIVER ADDRESS	
								$edi270STCounter++;
							}
							if((empty($dbGroupCity) == false) && (empty($dbGroupState) == false) && (empty($dbGroupZip) == false)){
								$edi270TranData .=	"N4*".$dbGroupCity."*".$dbGroupState."*".$dbGroupZip."~"; //Loop: 2100B -> INFORMATION RECEIVER CITY/STATE/ZIP CODE	
								$edi270STCounter++;
							}
							if((empty($dbGroupConName) == false) && ((empty($dbGroupTelephone) == false) || (empty($dbGroupFax) == false) || (empty($dbGroupEmail) == false))){
								$recConInfo = "PER*";
								if(empty($dbGroupConName) == false){
									$recConInfo .=	"IC*".$dbGroupConName; //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION Name
								}
								if(empty($dbGroupTelephone) == false){
									$recConInfo .=	"*TE*".$dbGroupTelephone; //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION Telephone
								}
								if(empty($dbGroupFax) == false){
									$recConInfo .=	"*FX*".$dbGroupFax; //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION Facsimile
								}
								if(empty($dbGroupEmail) == false){
									$recConInfo .=	"*EM*".$dbGroupEmail; //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION Electronic Mail
								}
								$recConInfo .=	"~";
								$edi270TranData .=	$recConInfo; //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION	
								$edi270STCounter++;
							}
							$edi270TranData .=	"HL*3*2*22*0~"; //Loop: 2000C -> SUBSCRIBER LEVEL
							$edi270STCounter++;
							/*if(empty($dbGroupFederalTaxID) == false){
								$edi270TranData .=	"TRN*1*".$newRTMEMAXID."*1".$dbGroupFederalTaxID."~"; //Loop: 2000C -> SUBSCRIBER TRACE NUMBER	
								$edi270STCounter++;
							}
							else{
								*/
								//$edi270TranData .= "TRN*1*".$newRTMEMAXID."*9".$dbGroupName."~"; //Loop: 2000C -> SUBSCRIBER TRACE NUMBER
								//$edi270TranData .= "TRN*1*TEST-TEST*9TEST~";
								//$edi270STCounter++;
							//}
						
							if(($dbSubscriberLName != "") && ($dbSubscriberFName != "") && ($dbInsPolicyClaimNo != "")){
								$edi270TranData .=	"NM1*IL*1*".$dbSubscriberLName."*".$dbSubscriberFName."*".$dbSubscriberMName."***MI*".$dbInsPolicyClaimNo."~"; //Loop: 2100C -> SUBSCRIBER NAME	
								$edi270STCounter++;
							}
							$edi270TranData .=	"REF*SY*".$dbSubscriberSS."~"; //Loop: 2100C -> SUBSCRIBER ADDITIONAL IDENTIFICATION	
							$edi270STCounter++;				
							if((empty($dbSubscriberAdd1) == false) || (empty($dbSubscriberAdd2) == false)){
								$subAdd = "N3";
								if(empty($dbSubscriberAdd1) == false){
									$subAdd .=	"*".$dbSubscriberAdd1; //Loop: 2100C -> SUBSCRIBER ADDRESS1
								}
								if(empty($dbSubscriberAdd2) == false){
									$subAdd .=	"*".$dbSubscriberAdd2; //Loop: 2100C -> SUBSCRIBER ADDRESS2
								}
								$subAdd .=	"~";
								$edi270TranData .=	$subAdd; //Loop: 2100C -> SUBSCRIBER ADDRESS
								$edi270STCounter++;
							}
							if((empty($dbSubscriberCity) == false) || (empty($dbSubscriberState) == false) || (empty($dbSubscriberZip) == false)){
								$subCSZ = "N4";
								if(empty($dbSubscriberCity) == false){
									$subCSZ .=	"*".$dbSubscriberCity; //Loop: 2100C -> SUBSCRIBER CITY
								}
								if(empty($dbSubscriberState) == false){
									$subCSZ .=	"*".$dbSubscriberState; //Loop: 2100C -> SUBSCRIBER STATE
								}
								if(empty($dbSubscriberZip) == false){
									$subCSZ .=	"*".$dbSubscriberZip; //Loop: 2100C -> SUBSCRIBER ZIP CODE
								}
								$subCSZ .=	"~";
								$edi270TranData .=	$subCSZ; //Loop: 2100C -> SUBSCRIBER CITY/STATE/ZIP CODE
								$edi270STCounter++;
							}
							if((empty($dbSubscriberDOB) == false) || (empty($dbSubscriberGender) == false)){
								$sumDemoInfo = "DMG";
								if(empty($dbSubscriberDOB) == false){
									$sumDemoInfo .=	"*D8*".$dbSubscriberDOB; //Loop: 2100C -> SUBSCRIBER DOB
								}
								if(empty($dbSubscriberGender) == false){
									$sumDemoInfo .=	"*".$dbSubscriberGender[0]; //Loop: 2100C -> SUBSCRIBER GENDER
								}
								$sumDemoInfo .=	"~";
								$edi270TranData .=	$sumDemoInfo; //Loop: 2100C -> SUBSCRIBER DEMOGRAPHIC INFORMATION			
								$edi270STCounter++;
							}				
							//if(empty($dbSubscriberDate) == false){
							$month = $day = $year = $imedicDOS = $subDate = "";	
							$pastdate = $futuredate = "";
							if($elReqDate == ""){
								$currentDOS = $this->getDateOfServiceFinalized($dbInsPatId);				
								$ArrCurrentDOS = explode("-", $currentDOS);
								$month	= $ArrCurrentDOS[0];
								$day	= $ArrCurrentDOS[1]; 
								$year	= $ArrCurrentDOS[2];
								if((empty($month) == false) && (empty($day) == false) && (empty($year) == false)){					
									if(checkdate ((int)$month , (int)$day , (int)$year) == true){
										//date past 27 month
										$pastdate = date('Y-m-d',mktime(date('H'), date('i'), date('s'), date('m')-27 , date('d'), date('Y')));
										//date Future 4 month
										$futuredate = date('Y-m-d',mktime(date('H'), date('i'), date('s'), date('m')+4 , date('d'), date('Y')));
										$datetoCheck = $year."-".$month."-".$day;
										$pastdate_ts = strtotime($pastdate);
										$futuredate_ts = strtotime($futuredate);
										$datetoCheck_ts = strtotime($datetoCheck);						
										if (($datetoCheck_ts >= $pastdate_ts) && ($datetoCheck_ts <= $futuredate_ts)){
											$subDate = $year.$month.$day;
											$imedicDOS = $year."-".$month."-".$day;
										}
										else{
											$subDate = date("Ymd");
										}
									}
								}
								else{
									$subDate = date("Ymd");
								}
							}
							else{							
								$subDate = str_replace("-","",$elReqDate);
								$imedicDOS = $elReqDate;							
							}
							$edi270TranData .=	"DTP*307*D8*".$subDate."~"; //Loop: 2100C -> SUBSCRIBER DATE
							
							$edi270STCounter++;
							//}
							$edi270TranData .=	"EQ*30**IND~"; //Loop: 2110C -> SUBSCRIBER ELIGIBILITY OR BENEFIT INQUIRY INFORMATION
							$edi270STCounter++;
							//$edi270TranData .=	"EQ*AL**IND~"; //Loop: 2110C -> Vision (Optometry)
							//$edi270STCounter++;
							$edi270STCounter++;
							$edi270TranData .=	"SE*".$edi270STCounter."*".$InterCtrlNumber."~"; //TRANSACTION SET TRAILER
							
							$edi270TranDataFinal = "";
							$edi270TranDataFinal .= $edi270CrlHeader; //INTERCHANGE CONTROL HEADER
							$edi270TranDataFinal .= $edi270FunGrpHeader; //FUNCTIONAL GROUP HEADER
							$edi270TranDataFinal .= $edi270TranData; //EDI 270 TRANSACTION DATA
							$edi270TranDataFinal .= $edi270FunGrpTrailer; //FUNCTIONAL GROUP TRAILER
							$edi270TranDataFinal .= $edi270CrlTrailer; //INTERCHANGE CONTROL TRAILER				
							//return array($edi270TranDataFinal, $dbInsPatId, $dbInsId, $imedicDOS, $dbFacGroupID, $retError);
						}
						else{
							$retError[] = " - Subscriber SSN Not Found!";
						}
					}
					else{
						$retError[] = " - Subscriber Data Not Found!";
					}
					imw_free_result($rsPatInsData);
				}
				else{
					$retError[] = " - Subscriber Data Not Found!";
				}
			}
			else{
				$retError[] = " - Group Medicare Receiver Id and Medicare Submitter Id Not Found!";
			}
		}
		else{
			$retError[] = " - No Default Facility Found!";
		}
		return array($edi270TranDataFinal, $dbInsPatId, $dbInsId, $imedicDOS, $dbFacGroupID, $retError);
	}
	
	function XMLToArray($XML){
		$values = array();
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $XML, $values);
		xml_parser_free($parser);
		return $values;
	}
	
	function send270Request($EDI270, $patId, $patInsId, $imedicDOS, $groupID, $intPatAppIdSA = 0){
		if(empty($EDI270) == false){
			$strValueCoIns = $strValueD = $strValueCP = "";
			$blCopayExits = false;
			$EDI270 = strtoupper($EDI270);
			$certSSLKeyPath = $certPath = $certClientPath = $vsPassword = "";
			
			$serviceOutput = $realMedCareElgblID = $realMedCareElgblURI = $batchSubmitID = $batchSubmitURI = $batchReceiveListID = $batchReceiveListURI = $certUse = "";
			$batchSubmitID5010 = $batchSubmitURI5010 = $batchReceiveListID5010 = $batchReceiveListURI5010 = "";
			$serviceOutput = $this->services();			
			$arrServices = $arrReceiveListXML = $arrVSBRLAll = $arrCertUse = array();
			$receiveListXML = "";
			$arrServices = $this->XMLToArray($serviceOutput);	
			
			$ArrgetVSIDURI = $this->getVSIDURI($arrServices);
			$realMedCareElgblID			= $ArrgetVSIDURI[0];
			$realMedCareElgblURI		= $ArrgetVSIDURI[1];
			$batchSubmitID				= $ArrgetVSIDURI[2];
			$batchSubmitURI				= $ArrgetVSIDURI[3];
			$batchReceiveListID			= $ArrgetVSIDURI[4];
			$batchReceiveListURI		= $ArrgetVSIDURI[5];
			$batchSubmitID5010			= $ArrgetVSIDURI[6];
			$batchSubmitURI5010			= $ArrgetVSIDURI[7];
			$batchReceiveListID5010		= $ArrgetVSIDURI[8];
			$batchReceiveListURI5010	= $ArrgetVSIDURI[9];
			
			if((empty($realMedCareElgblID) == false) && (empty($realMedCareElgblURI) == false)){				
				$fileNameEDI = $dataEDI = $pathToWrite270EDI = $ediToSend = "";
				$fileNameEDI = date("Y-m-d-H-i-s_").$patId."_270";
				$upLoadPath = $GLOBALS['fileroot'].'/interface/main/uploaddir/';
				$patientDir = "PatientId_".$patId."/";
				if(!is_dir($upLoadPath.$patientDir)){
					//Create patient directory
					mkdir($upLoadPath.$patientDir, 0700, true);
				}
				$patientDirVs270Dir = "vs_270_request/";
				if(!is_dir($upLoadPath.$patientDir.$patientDirVs270Dir)){
					//Create patient VS 270 request directory
					mkdir($upLoadPath.$patientDir.$patientDirVs270Dir, 0700, true);
				}				
				$pathToWrite270EDI = $upLoadPath.$patientDir.$patientDirVs270Dir.$fileNameEDI.".txt";		
				file_put_contents($pathToWrite270EDI,stripslashes($EDI270));
				$ediToSend = file_get_contents($pathToWrite270EDI);
				$ediLen = filesize($pathToWrite270EDI);
				$intNewId = 0;
				$qrySelPatCompId = "select provider from insurance_data where id = '".$patInsId."'";
				$rsSelPatCompId = imw_query($qrySelPatCompId);
				$rowSelPatCompId = imw_fetch_row($rsSelPatCompId);
				$patCompId = $rowSelPatCompId[0];				
				$qryInsert = "insert into real_time_medicare_eligibility (request_270_file_path, request_date_time, request_operator, patient_id, ins_data_id, imedic_DOS) 
								values ('".$pathToWrite270EDI."', NOW(), '".$_SESSION['authId']."', '".$patId."', '".$patInsId."', '".$imedicDOS."')";				
				$rsQryInsert =  imw_query($qryInsert);				
				$intNewId = imw_insert_id();
				
				$curl_site_url = $realMedCareElgblURI;
				
				$certUse = $this->getCertNameConstent($groupID,$patCompId);
				
				$arrCertInfo = array();
				$strCertInfoCertDir = $strCertInfoCertInUse = "";
				$strCertInfoCertDir = $this->arrCertDirInfo[$certUse]["certDir"];
				$strCertInfoCertInUse = $this->arrCertDirInfo[$certUse]["vsCertInUse"];
				$certSSLKeyPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vskey.pem";
				$certPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsca.pem";
				$certClientPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsclient.pem";
				$vsPassword = constant($strCertInfoCertInUse);
				//echo $certSSLKeyPath."------".$certPath."------".$certClientPath;
				//die();
				$headers = array("User-Agent: imwemr-292944",
								"Host: seapitest.visionshareinc.com:443",
								"X-SEAPI-Version: 1",
								"Content-Type: application/EDI-X12",	
								"Content-Length: ".$ediLen.""				
							); 		
				//$curl_site_url = "https://seapitest.visionshareinc.com/portal/seapi/services/RealtimeMedicareEligibility/11000";								
				$cur = curl_init();
				curl_setopt($cur, CURLOPT_URL, $curl_site_url);
				curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($cur, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($cur, CURLOPT_HEADER, TRUE);
				curl_setopt($cur, CURLOPT_POST, TRUE);
				curl_setopt($cur, CURLOPT_POSTFIELDS, $ediToSend);
				curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, TRUE);
				curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, 2); 
				curl_setopt($cur, CURLOPT_SSLCERTTYPE, 'PEM');
				curl_setopt($cur, CURLOPT_SSLKEYTYPE, 'PEM'); 
				curl_setopt($cur, CURLOPT_VERBOSE, '0');
				
				curl_setopt($cur, CURLOPT_SSLKEY, $certSSLKeyPath);
				curl_setopt($cur, CURLOPT_CAINFO, $certPath);
				curl_setopt($cur, CURLOPT_SSLCERT, $certClientPath); 
				curl_setopt($cur, CURLOPT_SSLCERTPASSWD, $vsPassword);
				
				curl_setopt($cur, CURLOPT_TIMEOUT, 60);
				
				$EDI271Response = curl_exec($cur);
				#For Testing
				//$EDI271Response = file_get_contents(dirname(__FILE__)."/2012-01-30-01-58-04_138730_271.txt");//
				//pre($EDI271Response,1);
				if (curl_errno($cur)){
					$errorCURL = curl_error($cur);
					curl_close($cur);					
					if (curl_errno($cur) == 28){
						return $this->strCustMsg;     	
					}
					else{
						return "Please contact your Administrator to resolve Curl Error: " . $errorCURL; 
					}
					die;
				}
				$transectionError = "";
				//pre(curl_getinfo($cur));
				$arrCurlInfo = array();
				//pre($arrCurlInfo,1);
				$arrCurlInfo = curl_getinfo($cur);				
				//curl_close($cur);
				if($arrCurlInfo["http_code"] != 200){
					$arrEDI271Response = $arrVisionShareResponseXML = array();					
					$arrEDI271Response = explode("\n", $EDI271Response);													
					//pre($arrEDI271Response);
					$blXMLerror = false;
					$strReplace = "";
					$strXMLerror = "";
					$strXMLValidation = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";					
					foreach($arrEDI271Response as $key => $val){					
						if(($blXMLerror == false) && (trim($val) == $strXMLValidation)){							
							$blXMLerror = true;							
							break;
						}						
					}
					
					if($blXMLerror == true){						
						foreach($arrEDI271Response as $key => $val){
							if(trim($val) == $strXMLValidation){
								break;
							}
							else{
								unset($arrEDI271Response[$key]);
							}
						}
						$strMsg = "";
						$arrError = array();
						$strXMLerror = implode("\n", $arrEDI271Response);
						$arrVisionShareResponseXML = $this->XMLToArray($strXMLerror);
						//return pre($arrVisionShareResponseXML);
						foreach($arrVisionShareResponseXML as $key => $val){
							if(is_array($val) == true){
								if(($val["tag"] == "message") && ($strMsg == "") && ($val["type"] == "complete")){
									$strMsg = trim($val["value"]);
								}
								elseif(($val["tag"] == "detail") && ($val["type"] == "complete") && (is_array($val["attributes"]) == true)){
									$arrError[] = array("key" => $val["attributes"]["key"], "val" => $val["attributes"]["value"]);	
								}
							}
						}
						$returnXMLError = "Error: ".$strMsg." \n";
						foreach($arrError as $key => $val){
							$returnXMLError .= "Expected Value:".strtoupper($val["key"]).". Actual Value:".strtoupper($val["val"])." \n";
						}
						$transectionError .= $returnXMLError;
						//echo $returnXMLError;
					}
				}
				elseif($arrCurlInfo["http_code"] == 200){
					$arrEDI271Response = $arrVisionShareResponseXML = array();					
					$arrEDI271Response = explode("\n", $EDI271Response);													
					//pre($arrEDI271Response,1);
					$blACKExits = $bl997Exits = $bl271Exits = false;
					$strACK = $returnTA1Error = $strEDI997 = $strEDI271 = "";
					foreach($arrEDI271Response as $key => $val){					
						if((strpos($val, "TA1") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl271Exits == false)){
							$strACK = trim($val);
							$blACKExits = true;
						}
						elseif((strpos($val, "ST*997*") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl271Exits == false)){
							$strEDI997 = trim($val);
							$bl997Exits = true;
						}
						elseif((strpos($val, "ST*271*") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl271Exits == false)){
							$strEDI271 = trim($val);
							$bl271Exits = true;
						}
					}
					//pre(var_dump($blACKExits));
					if($blACKExits == true){
						$strTA = $strTAReasonCode = "";
						$arrACK = $arrTA = array();
						$arrACK = explode("~",$strACK);
						//pre($arrACK);
						$strTA = $arrACK[1];
						$arrTA = explode("*",$strTA);
						//pre($arrTA);
						if(trim($arrTA[4]) == "R"){
							$strTAReasonCode = $arrTA[5];
							$errorMsg = $this->getVisionShareError("vision_share", "TA", $strTAReasonCode);
							$returnTA1Error = "Error INTERCHANGE ACKNOWLEDGMENT: \n ".$errorMsg." \n ";
							$transectionError .= $returnTA1Error;
						}
					}
					elseif($bl997Exits == true){
						$str997 = $strTAReasonCode = "";
						$arr997 = $arrTA = $dataArr = $transDataArr = array();
						//echo $strEDI997;						
						$dataArr = preg_split('/(AK2\*270\*)|(AK5\*)|(AK9\*)/',$strEDI997);
						$transDataArr = preg_split('/~/',$dataArr[1]);						
						if(ucfirst($dataArr[2][0]) == 'A' || ucfirst($dataArr[3][0]) == 'A'){
							echo "270 is Accepted";
						}
						elseif(ucfirst($dataArr[2][0]) == 'R' || ucfirst($dataArr[3][0]) == 'R'){
							$errorDetailArr = $errorString = array();
							$errorDetailArr = explode('AK',$strEDI997);
							//pre($errorDetailArr);
							foreach($errorDetailArr as $val){
								$firstVal = substr($val,0,1);		
								if(($firstVal > 2) && ($firstVal < 5) && (in_array('AK'.$val, $errorString) == false)){
									$errorString[] = 'AK'.$val;
								}
							}
							//pre($errorString);
							if($errorString[0] != ""){
								foreach($errorString as $key => $val){
									$arrVal = array();
									$arrVal = explode("*", $val);
									if(trim($arrVal[0]) == "AK3"){
										$intAk3ReasonCode = (int)trim($arrVal[4]);										
										$errorMsg = $this->getVisionShareError("vision_share", "AK3", $intAk3ReasonCode);
										$return997Error = "Error in Segment : ".trim($arrVal[1])." \n";
										$return997Error .= "Loop : ".trim($arrVal[3])." \n";
										$return997Error .= "Due to : ".trim($errorMsg)." \n";										
										$transectionError .= $return997Error;
									}
									elseif(trim($arrVal[0]) == "AK4"){
										$intAk3ReasonCode = (int)trim($arrVal[3]);										
										$errorMsg = $this->getVisionShareError("vision_share", "AK4", $intAk3ReasonCode);
										$return997Error = "Position in Segment : ".trim($arrVal[1])." \n";
										$return997Error .= "Data Elemnt Ref Number : ".trim($arrVal[2])." \n";
										$return997Error .= "Data Element Syntax Error : ".trim($errorMsg)." \n";										
										$transectionError .= $return997Error;
									}
								}
							}
							
						}
					}
					elseif($bl271Exits == true){
						$arrEDI271 = array();
						$arrEDI271 = explode("~",$strEDI271);
						//pre($arrEDI271);
						$return271Error = $strHL3RQLName = $strHL3RQFName = $strHL3RQMName = $strHL3RQPolicyNo = $strHL3RQAdd1 = $strHL3RQAdd2 = $strHL3RQCity = $strHL3RQState = $strHL3RQZip = $strHL3RQPatDOB = "";
						$blHL1RQSt = $blHL2RQSt = $blHL3RQSt = $blHL4RQSt = false;
						$blPatName = $blPatSAdd = $blPatCSZAdd = $blPatDOB = false;
						
						$blDepName = $blDepSAdd = $blPatCSZAdd = $blDepDOB = $blDepRel = false;
						$arrHL4RQName = $arrHL4RQAdd = $arrHL4RQCSZ = $arrHL4RQDOB = $arrHL4RQRel = array();
						$strHL4RQLName = $strHL4RQFName = $strHL4RQMName = $strHL4RQPolicyNo = $strHL4RQAdd1 = $strHL4RQAdd2 = $strHL4RQCity = $strHL4RQState = $strHL4RQZip = $strHL4RQPatDOB = $strDepRel = "";					
						
						$strHL1RQ = $strHL2RQ = $strHL3RQ = $strHL4RQ = "";
						$strHL1ReasonCode = $strHL2ReasonCode = $strHL3ReasonCode = $strHL4ReasonCode = "";
						$arrHL3RQName = $arrHL1RQ = $arrHL2RQ = $arrHL3RQ = $arrHL4RQ = $arrHL3RQAdd = $arrHL3RQCSZ = $arrHL3RQDOB = array();
						
						$blHL1AAA = $blHL2AAA = $blHL3AAA = $blHL4AAA = false;
						
						foreach($arrEDI271 as $key => $val){
							if((strpos($val, "HL*1*") !== false) && ($blHL1RQSt == false)){								
								$blHL1RQSt = true;
							}
							elseif((strpos($val, "HL*2*") !== false) && ($blHL2RQSt == false)){								
								$blHL1RQSt = false;
								$blHL2RQSt = true;								
							}
							elseif((strpos($val, "HL*3*") !== false) && ($blHL3RQSt == false)){								
								$blHL1RQSt = false;
								$blHL2RQSt = false;
								$blHL3RQSt = true;
							}
							elseif((strpos($val, "LS*2120") !== false) && ($blHL3RQSt == true)){								
								$blHL1RQSt = false;
								$blHL2RQSt = false;
								$blHL3RQSt = false;
							}
							elseif((strpos($val, "HL*4*") !== false) && ($blHL4RQSt == false)){								
								$blHL1RQSt = false;
								$blHL2RQSt = false;
								$blHL3RQSt = false;
								$blHL4RQSt = true;
							}
							elseif((strpos($val, "LS*2120") !== false) && ($blHL4RQSt == true)){								
								$blHL1RQSt = false;
								$blHL2RQSt = false;
								$blHL3RQSt = false;
								$blHL4RQSt = false;
							}
							if((strpos($val, "AAA") !== false) && ($blHL1RQSt == true) && ($blHL1AAA == false)){
								$blHL1AAA = true;
								$arrHL1RQ = array();
								$strHL1RQ = trim($val);
								$arrHL1RQ = explode("*", $strHL1RQ);
								$strHL1ReasonCode = trim($arrHL1RQ[3]);
								$errorMsg = "";
								$errorMsg = $this->getVisionShareError("vision_share_271", "HL1", $strHL1ReasonCode);
								$return271Error .= " - Error : INFORMATION SOURCE LEVEL \n";
								$return271Error .= " - Loop : 2000A \n";
								$return271Error .= " - Due to : ".trim($errorMsg)." \n";
								$FollowUPAction  = "";
								$FollowUPAction = $this->getVisionShareError("vision_share_271", "HL1-Follow-upAction", trim($arrHL1RQ[4]));
								$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";
							}
							elseif((strpos($val, "AAA") !== false) && ($blHL2RQSt == true) && ($blHL2AAA == false)){
								$blHL2AAA = true;
								$arrHL2RQ = array();
								$strHL2RQ = trim($val);
								$arrHL2RQ = explode("*", $strHL2RQ);
								$strHL2ReasonCode = trim($arrHL2RQ[3]);
								$errorMsg = "";
								$errorMsg = $this->getVisionShareError("vision_share_271", "HL2", $strHL2ReasonCode);
								$return271Error .= " - Error : INFORMATION RECEIVER LEVEL \n";
								$return271Error .= " - Loop : 2100B \n";
								$return271Error .= " - Due to : ".trim($errorMsg)." \n";
								$FollowUPAction  = "";
								$FollowUPAction = $this->getVisionShareError("vision_share_271", "HL2-Follow-upAction", trim($arrHL2RQ[4]));
								$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";								
							}
							elseif((strpos($val, "AAA") !== false) && ($blHL3RQSt == true) && ($blHL3AAA == false)){
								$blHL3AAA = true;
								$arrHL3RQ = array();
								$strHL3RQ = trim($val);
								$arrHL3RQ = explode("*", $strHL3RQ);																
								$strHL3ReasonCode = trim($arrHL3RQ[3]);
								$errorMsg = "";
								$errorMsg = $this->getVisionShareError("vision_share_271", "HL3", $strHL3ReasonCode);
								$return271Error .= " - Error : SUBSCRIBER NAME LEVEL \n";
								$return271Error .= " - Loop : 2100C \n";
								$return271Error .= " - Due to : ".trim($errorMsg)." \n";
								$FollowUPAction  = "";
								$FollowUPAction = $this->getVisionShareError("vision_share_271", "HL3-Follow-upAction", trim($arrHL3RQ[4]));
								$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";								
							}
							elseif((strpos($val, "AAA") !== false) && ($blHL4RQSt == true) && ($blHL4AAA == false)){
								$blHL4AAA = true;									
								$strHL4RQ = trim($val);
								$arrHL4RQ = explode("*", $strHL4RQ);
								//pre($arrHL4RQ,1);																
								$strHL4ReasonCode = trim($arrHL4RQ[3]);
								$errorMsg = "";
								$errorMsg = $this->getVisionShareError("vision_share_271", "HL4", $strHL4ReasonCode);
								$errorMsg = (empty($errorMsg) == false ? $errorMsg : $this->error);
								$return271Error .= " - Error : DEPENDENT NAME LEVEL \n";
								$return271Error .= " - Loop : 2100D \n";
								$return271Error .= " - Due to : ".trim($errorMsg)." \n";
								$FollowUPAction  = "";
								$FollowUPAction = $this->getVisionShareError("vision_share_271", "HL4-Follow-upAction", trim($arrHL4RQ[4]));
								$FollowUPAction = (empty($FollowUPAction) == false ? $FollowUPAction : $this->error);
								$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";
							}
							elseif((strpos($val, "MSG") !== false) && (($blHL1AAA == true) || ($blHL2AAA == true) || ($blHL3AAA == true) || ($blHL4AAA == true))){
								$strAAAMSG = "";
								$arrAAAMSG = array();
								$strAAAMSG = trim($val);
								$arrAAAMSG = explode("*", $strAAAMSG);
								//pre($arrHL3MSG,1);																
								$strHL3Message = trim($arrAAAMSG[1]);
								$return271Error .= " - Message from Information Source: ";
								$return271Error .= trim($strHL3Message)." \n";
							}
							if((strpos($val, "NM1") !== false) && ($blHL3RQSt == true) && ($blPatName == false)){
								$arrHL3RQName = array();
								$strHL3RQNameSeg = "";
								$strHL3RQNameSeg = trim($val);
								$arrHL3RQName = explode("*", $strHL3RQNameSeg);																
								$strHL3RQLName = ucfirst(strtolower(trim($arrHL3RQName[3])));
								$strHL3RQFName = ucfirst(strtolower(trim($arrHL3RQName[4])));
								$strHL3RQMName = ucfirst(strtolower(trim($arrHL3RQName[5])));
								if(trim($arrHL3RQName[9]) != ""){
									$strHL3RQPolicyNo = trim($arrHL3RQName[9]);
								}
								$blPatName = true;
							}
							elseif((strpos($val, "N3") !== false) && ($blHL3RQSt == true) && ($blPatSAdd == false)){							
								$arrHL3RQAdd = array();
								$strHL3RQAddSeg = "";
								$strHL3RQAddSeg = trim($val);
								$arrHL3RQAdd = explode("*", $strHL3RQAddSeg);																
								$strHL3RQAdd1 = ucwords(strtolower(trim($arrHL3RQAdd[1])));
								$strHL3RQAdd2 = ucwords(strtolower(trim($arrHL3RQAdd[2])));
								$blPatSAdd = true;
							}
							elseif((strpos($val, "N4") !== false) && ($blHL3RQSt == true) && ($blPatCSZAdd == false)){								
								$arrHL3RQCSZ = array();
								$strHL3RQCSZSeg = "";
								$strHL3RQCSZSeg = trim($val);
								$arrHL3RQCSZ = explode("*", $strHL3RQCSZSeg);																
								$strHL3RQCity = ucwords(strtolower(trim($arrHL3RQCSZ[1])));
								$strHL3RQState = trim($arrHL3RQCSZ[2]);
								$strHL3RQZip = trim($arrHL3RQCSZ[3]);
								$blPatCSZAdd = true;
							}
							elseif((strpos($val, "DMG") !== false) && ($blHL3RQSt == true) && ($blPatDOB == false)){								
								$arrHL3RQDOB = array();
								$strHL3RQDOBSeg = "";
								$strHL3RQDOBSeg = trim($val);
								$arrHL3RQDOB = explode("*", $strHL3RQDOBSeg);																
								if(trim($arrHL3RQDOB[1]) == "D8"){
									$strHL3RQDOBSeg = trim($arrHL3RQDOB[2]);
									$intDOBYear = $intDOBMon = $intDOBDay = 0;
									$intDOBYear = (int)substr($strHL3RQDOBSeg,0,4);
									$intDOBMon = (int)substr($strHL3RQDOBSeg,4,2);
									$intDOBDay = (int)substr($strHL3RQDOBSeg,6,2);
									if(($intDOBYear > 0) && ($intDOBMon > 0) && ($intDOBDay > 0) && (checkdate($intDOBMon, $intDOBDay, $intDOBYear) == true)){
										$strHL3RQPatDOB = $intDOBYear."-".$intDOBMon."-".$intDOBDay;
									}
									else{
										$strHL3RQPatDOB = "";
									}
								}
								$blPatDOB = true;
							}
							//Dependent
							if((strpos($val, "NM1") !== false) && ($blHL4RQSt == true) && ($blDepName == false)){																										
								$strHL4RQNameSeg = "";
								$strHL4RQNameSeg = trim($val);
								$arrHL4RQName = explode("*", $strHL4RQNameSeg);
								
								$strHL4RQLName = ucfirst(strtolower(trim($arrHL4RQName[3])));
								$strHL4RQFName = ucfirst(strtolower(trim($arrHL4RQName[4])));
								$strHL4RQMName = ucfirst(strtolower(trim($arrHL4RQName[5])));
								if(trim($arrHL4RQName[9]) != ""){
									$strHL4RQPolicyNo = trim($arrHL4RQName[9]);
								}
								$blDepName = true;
															
							}
							elseif((strpos($val, "N3") !== false) && ($blHL4RQSt == true) && ($blDepSAdd == false)){
								$strHL4RQAddSeg = "";
								$strHL4RQAddSeg = trim($val);
								$arrHL4RQAdd = explode("*", $strHL4RQAddSeg);																
								$strHL4RQAdd1 = ucwords(strtolower(trim($arrHL4RQAdd[1])));
								$strHL4RQAdd2 = ucwords(strtolower(trim($arrHL4RQAdd[2])));
								$blDepSAdd = true;
							}
							elseif((strpos($val, "N4") !== false) && ($blHL4RQSt == true) && ($blPatCSZAdd == false)){
								$strHL4RQCSZSeg = "";
								$strHL4RQCSZSeg = trim($val);
								$arrHL4RQCSZ = explode("*", $strHL4RQCSZSeg);																
								$strHL4RQCity = ucwords(strtolower(trim($arrHL4RQCSZ[1])));
								$strHL4RQState = trim($arrHL4RQCSZ[2]);
								$strHL4RQZip = trim($arrHL4RQCSZ[3]);
								$blPatCSZAdd = true;
							}
							elseif((strpos($val, "DMG") !== false) && ($blHL4RQSt == true) && ($blDepDOB == false)){
								$strHL4RQDOBSeg = "";
								$strHL4RQDOBSeg = trim($val);
								$arrHL4RQDOB = explode("*", $strHL4RQDOBSeg);																
								if(trim($arrHL4RQDOB[1]) == "D8"){
									$strHL4RQDOBSeg = trim($arrHL4RQDOB[2]);
									$intDOBYear = $intDOBMon = $intDOBDay = 0;
									$intDOBYear = (int)substr($strHL4RQDOBSeg,0,4);
									$intDOBMon = (int)substr($strHL4RQDOBSeg,4,2);
									$intDOBDay = (int)substr($strHL4RQDOBSeg,6,2);
									if(($intDOBYear > 0) && ($intDOBMon > 0) && ($intDOBDay > 0) && (checkdate($intDOBMon, $intDOBDay, $intDOBYear) == true)){
										$strHL4RQPatDOB = $intDOBYear."-".$intDOBMon."-".$intDOBDay;
									}
									else{
										$strHL4RQPatDOB = "";
									}
									$blDepDOB = true;
								}
							}
							//Dependent Relatinship
							if((strpos($val, "INS") !== false) && ($blHL4RQSt == true) && ($blDepRel == false)){																										
								$strHL4RQRelSeg = "";
								$strHL4RQRelSeg = trim($val);
								$arrHL4RQRel = explode("*", $strHL4RQRelSeg);
								
								switch ($arrHL4RQRel[2]):
									case "01":
										$strDepRel = "Spouse";
									break;
									case "19":
										$strDepRel = "Child";
									break;
									case "21":
										$strDepRel = "Unknown";
									break;
									case "34":
										$strDepRel = "Other Adult";
									break;
								endswitch;
								
								$blDepRel = true;																
							}
							//						
						}
					}
				}
				if(empty($return271Error) == false){					
					$transectionError .= $return271Error;
					$errorDetailArr = $errorString = array();
				}				
				$patientDirVs271Dir = "vs_271_response/";
				if(!is_dir($upLoadPath.$patientDir.$patientDirVs271Dir)){
					//Create patient VS 271 responce directory
					mkdir($upLoadPath.$patientDir.$patientDirVs271Dir, 0700, true);
				}
				$fileNameEDI271 = date("Y-m-d-H-i-s_").$patId."_271";
				$pathToWrite271EDI = $upLoadPath.$patientDir.$patientDirVs271Dir.$fileNameEDI271.".txt";		
				file_put_contents($pathToWrite271EDI,$EDI271Response);
				if($intNewId > 0){
					$strXMLEB = $strEBResponce = $strEBInsuranceTypeCode = "";
					//$arrEBResponce = $arrEBInsuranceTypeCode = array();
					$strXMLEB .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
					$strXMLEB .= "<DTPEBLOOP>";
					$EBDetailArr = $arrLastEB = $DTPDetailArr = $EBDTPDetailArr = array();
					//$EBDetailArr = explode('~EB',$EDI271Response);
					$DTPDetailArr = explode('~DTP',$EDI271Response);					
					//pre($DTPDetailArr,1);
					$EBDTPDetailArr = explode('~EB',$EDI271Response);
					//pre($EBDTPDetailArr,1);
					unset($EBDTPDetailArr[0]);
					//pre($EBDTPDetailArr,1);
					$EBDTPDetailArr = array_values($EBDTPDetailArr);
					foreach($EBDTPDetailArr as $key => $val){
						$EBDTPDetailArr[$key] = "EB".$val;
					}
					//pre($EBDTPDetailArr,1);
					$EBDTPDetailArrRefined = array();
					foreach($EBDTPDetailArr as $key => $val){
						$arrVal = $arrTemp = $arrMSG = array();
						$arrVal = explode("~", trim($val));
						//pre($arrVal,1);
						foreach($arrVal as $intValkey => $strValVal){
							if((strpos($strValVal, "EB") !== false) || (strpos($strValVal, "DTP") !== false) || (strpos($strValVal, "MSG") !== false)){
								if(strpos($strValVal, "DTP") !== false){
									$arrTemp["DTP"]	= $strValVal;
								}
								elseif(strpos($strValVal, "EB") !== false){
									$arrTemp["EB"]	= $strValVal;
								}
								elseif(strpos($strValVal, "MSG") !== false){
									$arr = array();
									$arr = explode("*", $strValVal);
									$arrMSG[] = $arr[1];
								}										
							}
							else{
								unset($arrVal[$intValkey]);
							}
						}
						$arrTemp["MSG"]	= implode(",", $arrMSG);
						//pre($arrVal,1);
						$EBDTPDetailArrRefined[] = $arrTemp;
					}
					//pre($EBDTPDetailArrRefined,1);

					//pre($DTPDetailArr,1);
					unset($DTPDetailArr[0]);
					//pre($DTPDetailArr,1);
					$DTPDetailArr = array_values($DTPDetailArr);
					foreach($DTPDetailArr as $key => $val){
						$DTPDetailArr[$key] = "DTP".$val;
					}					
					//pre($DTPDetailArr,1);
					foreach($DTPDetailArr as $key => $val){
						$arrVal = $arrTemp = $arrMSG = array();
						$arrVal = explode("~", trim($val));
						//pre($arrVal,1);
						foreach($arrVal as $intValkey => $strValVal){
							//pre($arrVal);
							$nxtStrValVal = "";
							//echo $arrVal[$intValkey];
							//echo "<br>";
							$nxtStrValVal = $arrVal[$intValkey + 1];							
							if((strpos($strValVal, "EB") !== false)){
								$arrMsgTemp = explode("*", $nxtStrValVal);
								if((empty($arrMsgTemp[1]) == false) && ($arrMsgTemp[0] == "MSG")){
									//pre($arrMsgTemp,1);
									$arrMsgAll = array();
									$intMsg = 0;
									$intStMsg =  $intValkey + 1;
									$intEndMsg = $intStMsg + 10;
									for($intMsg = $intStMsg; $intMsg <= $intEndMsg; $intMsg++){
										if((strpos($arrVal[$intMsg], "MSG") !== false)){
											$arrMsgTemp1 = explode("*", $arrVal[$intMsg]);
											$arrMsgAll[] = $arrMsgTemp1[1];
										}
										elseif((strpos($arrVal[$intMsg], "EB") !== false)){
											$intMsg = $intEndMsg + 100;
										}
									}
									$arrTemp[] = array(array("EB" => $strValVal, "MSG" => implode(",", $arrMsgAll)));
								}
								else{
									$arrTemp[] = $strValVal;
								}
							}
							elseif(strpos($strValVal, "EB") !== false){
								$arrTemp[] = $strValVal;
							}
						}
						//pre($arrTemp);
						$DTPDetailArr[$key] = array("DTP" => $arrVal[0], "EB" => $arrTemp);
					}
					//pre($EBDTPDetailArrRefined,1);
					//pre($DTPDetailArr,1);
					//pre($EBDetailArr);										
					//$EBDetailArr = array_values($EBDetailArr);
					//pre($EBDetailArr);					
					//die;
					foreach($DTPDetailArr as $key => $val){
						if(is_array($val) == true){
							foreach($val as $valkey => $valval){
								if($valkey == "DTP"){
									$arrDTPVal = array();
									$strDTPVal = "";
									$strXMLEB .= "<DTPLOOPINFO>";
									$strXMLEB .= "<DTPLOOP_".$key.">";
									$strDTPVal = trim($valval);
									$arrDTPVal = explode("*", $strDTPVal);
									unset($arrDTPVal[0]);
									$arrDTPVal = array_values($arrDTPVal);								
									foreach($arrDTPVal as $intDTPValKey => $strDTPValVal){
										$strDTPValVal = trim($strDTPValVal);
										$oriVal = "";
										switch ($intDTPValKey):
											case 0:
												$strXMLEB .= "<DateTimeQualifier>";									
												$oriVal = $this->getVisionShareError("vision_share_271", "DTP", $strDTPValVal);
													$strXMLEB .= "<DateTimeQualifierID>";
													$strXMLEB .= $strDTPValVal;
													$strXMLEB .= "</DateTimeQualifierID>";
													
													$strXMLEB .= "<DateTimeQualifierIDVal>";
													$strXMLEB .= (is_string($oriVal) ? $oriVal : "");
													$strXMLEB .= "</DateTimeQualifierIDVal>";
												$strXMLEB .= "</DateTimeQualifier>";
											break;
											case 1:
												$strXMLEB .= "<DateTimePeriodFormatQualifier>";
												$strXMLEB .= $strDTPValVal;
												$strXMLEB .= "</DateTimePeriodFormatQualifier>";
											break;
											case 2:										
												$strDTPDate = "";
												$arrDTPDate = array();
												$strDTPDate = $strDTPValVal;
												$arrDTPDate = explode("-", $strDTPDate);										
												$strXMLEB .= "<DateTimePeriod>";
													$strXMLEB .= "<DateTimePeriodDate1>";
													$strXMLEB .= trim($arrDTPDate[0]);
													$strXMLEB .= "</DateTimePeriodDate1>";
													
													$strXMLEB .= "<DateTimePeriodDate2>";
													$strXMLEB .= trim($arrDTPDate[1]);
													$strXMLEB .= "</DateTimePeriodDate2>";
												$strXMLEB .= "</DateTimePeriod>";
											break;
										endswitch;
									}
									$intCountDTPVal = 0;
									$intCountDTPVal = count($arrDTPVal);
									for($a = $intCountDTPVal; $a <= 2; $a++){
										$a = (int)$a;
										switch ($a):
											case 0:
													$strXMLEB .= "<DateTimeQualifier>
																	<DateTimeQualifierID></DateTimeQualifierID>
																	<DateTimeQualifierIDVal></DateTimeQualifierIDVal>
																</DateTimeQualifier>";
												break;
												case 1:
													$strXMLEB .= "<DateTimePeriodFormatQualifier></DateTimePeriodFormatQualifier>";
												break;
												case 2:
													$strXMLEB .= "<DateTimePeriod>
																	<DateTimePeriodDate1></DateTimePeriodDate1>
																	<DateTimePeriodDate2></DateTimePeriodDate2>
																</DateTimePeriod>";
												break;
											endswitch;	
									}
									$strXMLEB .= "</DTPLOOP_".$key.">";
									$strXMLEB .= "</DTPLOOPINFO>";
								}
								elseif($valkey == "EB"){
									$strXMLEB .= "<EBLOOPINFO>";
									//$strXMLEB .= "<EBLOOP_".$key.">";
									foreach($valval as $intKeyValVal => $strValValVal){
										//pre($strValValVal,1);													
										$strEBMsg = $strTemp = $strEBDTP = "";
										if(is_array($strValValVal) == true){						
											$strTemp = $strValValVal["EB"]["EB"];														
											$strEBMsg = $strValValVal["EB"]["MSG"];
											$strEBDTP = $strValValVal["EB_DTP"];
											unset($strValValVal);
											$strValValVal = $strTemp;
										}
										$strEBResponceTemp = "";
										$blEBResponceTemp = false;
										$strXMLEB .= "<EBLOOP_".$key."_".$intKeyValVal.">";
										
										##MSG Start
										$strXMLEB .= "<MSG>";
										if(empty($strEBMsg) == false){
											$strXMLEB .= "<![CDATA[".$strEBMsg."]]>";
										}
										$strXMLEB .= "</MSG>";
										##MSG End
										
										##EBDTP start
										$strXMLEB .= "<EBDTP>";
										$arrEBDTPVal = array();
										$arrEBDTPVal = explode("*", trim($strEBDTP));
										unset($arrEBDTPVal[0]);
										$arrEBDTPVal = array_values($arrEBDTPVal);
										//pre($arrEBDTPVal,1);		
										foreach($arrEBDTPVal as $intEBDTPValKey => $strEBDTPVal){
											$strEBDTPVal = trim($strEBDTPVal);
											$oriVal = "";
											switch ($intEBDTPValKey):
												case 0:
													$strXMLEB .= "<DateTimeQualifier>";									
													$oriVal = $this->getVisionShareError("vision_share_271", "DTP", $strEBDTPVal);
														$strXMLEB .= "<DateTimeQualifierID>";
														$strXMLEB .= $strEBDTPVal;
														$strXMLEB .= "</DateTimeQualifierID>";
														
														$strXMLEB .= "<DateTimeQualifierIDVal>";
														$strXMLEB .= ((empty($oriVal) == false) ? $oriVal : "");
														$strXMLEB .= "</DateTimeQualifierIDVal>";
													$strXMLEB .= "</DateTimeQualifier>";
												break;
												case 1:
													$strXMLEB .= "<DateTimePeriodFormatQualifier>";
													$strXMLEB .= $strEBDTPVal;
													$strXMLEB .= "</DateTimePeriodFormatQualifier>";
												break;
												case 2:										
													$strDTPDate = "";
													$arrDTPDate = array();
													$strDTPDate = $strEBDTPVal;
													$arrDTPDate = explode("-", $strDTPDate);										
													$strXMLEB .= "<DateTimePeriod>";
														$strXMLEB .= "<DateTimePeriodDate1>";
														$strXMLEB .= trim($arrDTPDate[0]);
														$strXMLEB .= "</DateTimePeriodDate1>";
														
														$strXMLEB .= "<DateTimePeriodDate2>";
														$strXMLEB .= trim($arrDTPDate[1]);
														$strXMLEB .= "</DateTimePeriodDate2>";
													$strXMLEB .= "</DateTimePeriod>";
												break;
											endswitch;
										}
										$intCountDTPVal = 0;
										$intCountDTPVal = count($arrEBDTPVal);
										for($a = $intCountDTPVal; $a <= 2; $a++){
											$a = (int)$a;
											switch ($a):
												case 0:
														$strXMLEB .= "<DateTimeQualifier>
																		<DateTimeQualifierID></DateTimeQualifierID>
																		<DateTimeQualifierIDVal></DateTimeQualifierIDVal>
																	</DateTimeQualifier>";
													break;
													case 1:
														$strXMLEB .= "<DateTimePeriodFormatQualifier></DateTimePeriodFormatQualifier>";
													break;
													case 2:
														$strXMLEB .= "<DateTimePeriod>
																		<DateTimePeriodDate1></DateTimePeriodDate1>
																		<DateTimePeriodDate2></DateTimePeriodDate2>
																	</DateTimePeriod>";
													break;
												endswitch;	
										}
										$strXMLEB .= "</EBDTP>";
										##EBDTP End
																			
										$arrEBVal = array();
										$strEBVal = trim($strValValVal);
										$arrEBVal = explode("*", trim($strEBVal));	
										unset($arrEBVal[0]);
										$arrEBVal = array_values($arrEBVal);
										//pre($arrEBVal);
										//die;	
										///////////////////////////////////////////
										foreach($arrEBVal as $intEBValKey => $strEBValVal){
											if(empty($strValueCoIns) == true){// Co-Insurance
												//pre($arrEBVal,1);	EB*A*IND*A7*OT*NJ DIRECT*27**.5****N
												if((trim(strtoupper($arrEBVal[0])) == "A") && (trim(strtoupper($arrEBVal[1])) == "IND") && ((int)$arrEBVal[2] == 98) && ((float)$arrEBVal[7] > 0)){
													$strValueCoIns = trim($arrEBVal[3])."-".$arrEBVal[2]."-"."IND"."-".$arrEBVal[4]."-".$arrEBVal[6]."-".$arrEBVal[5]."-".$arrEBVal[7]."-".$arrEBVal[9]."-".$arrEBVal[8]."-".$arrEBVal[10]."-".$arrEBVal[11];
												}
											}
											
											if(empty($strValueCP) == true){// Co-Payment
												//pre($arrEBVal,1);	
												if((trim(strtoupper($arrEBVal[0])) == "B") && (trim(strtoupper($arrEBVal[1])) == "IND") && ((int)$arrEBVal[2] == 98)){
													$strValueCP = trim($arrEBVal[3])."-".$arrEBVal[2]."-"."IND"."-".$arrEBVal[4]."-".$arrEBVal[6]."-".$arrEBVal[5]."-".$arrEBVal[7]."-".$arrEBVal[9]."-".$arrEBVal[8]."-".$arrEBVal[10]."-".$arrEBVal[11];
													$blCopayExits = true;
												}
												elseif((trim(strtoupper($arrEBVal[0])) == "B") && (empty($arrEBVal[6]) == false)){
													$blCopayExits = true;
												}
											}
											
											if(empty($strValueD) == true){
												//pre($arrEBVal,1);	
												if((trim(strtoupper($arrEBVal[0])) == "C") && (trim(strtoupper($arrEBVal[1])) == "IND") && (trim(strtoupper($arrEBVal[3])) == "MB") && ((int)$arrEBVal[5] == 29)){
													$strValueD = trim($arrEBVal[3])."-".$arrEBVal[2]."-"."IND"."-".$arrEBVal[4]."-".$arrEBVal[6]."-"."29"."-".$arrEBVal[7]."-".$arrEBVal[9]."-".$arrEBVal[8]."-".$arrEBVal[10]."-".$arrEBVal[11];
												}
												elseif((trim(strtoupper($arrEBVal[0])) == "C") && (trim($arrEBVal[1]) == "") && (trim(strtoupper($arrEBVal[3])) == "MB") && ((int)$arrEBVal[5] == 29)){
													$strValueD = trim($arrEBVal[3])."-".$arrEBVal[2]."-".""."-".$arrEBVal[4]."-".$arrEBVal[6]."-"."29"."-".$arrEBVal[7]."-".$arrEBVal[9]."-".$arrEBVal[8]."-".$arrEBVal[10]."-".$arrEBVal[11];
												}
											}
											
											$orival = "";
											$strEBValVal = trim($strEBValVal);
											switch ($intEBValKey):
												case 0:
													$strXMLEB .= "<EligibilityOrBenefitInformation>";																					
													$orival = $this->getVisionShareError("vision_share_271", "EB", $strEBValVal);
													$strEBValVal = (string)$strEBValVal;
													if(
													($strEBValVal == "1") || ($strEBValVal == "2") || ($strEBValVal == "3") || ($strEBValVal == "4") || ($strEBValVal == "5") || 
													($strEBValVal == "6") || ($strEBValVal == "7") || ($strEBValVal == "8")
													){
														if(empty($strEBResponceTemp) == true){
															$strEBResponceTemp = $strEBValVal;
														}
													}
														//$arrEBResponce[] = $strEBValVal; 
														$strXMLEB .= "<EligibilityOrBenefitInformationId>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</EligibilityOrBenefitInformationId>";
														
														$strXMLEB .= "<EligibilityOrBenefitInformationVal>";
														$strXMLEB .= (is_string($orival) ? $orival : "");
														$strXMLEB .= "</EligibilityOrBenefitInformationVal>";
													$strXMLEB .= "</EligibilityOrBenefitInformation>";
													break;
												case 1:
													$strXMLEB .= "<CoverageLevelCode>";								
													$orival = $this->getVisionShareError("vision_share_271", "CoverageLevelCode", $strEBValVal);
														$strXMLEB .= "<CoverageLevelCodeId>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</CoverageLevelCodeId>";
														
														$strXMLEB .= "<CoverageLevelCodeVal>";
														$strXMLEB .= (is_string($orival) ? $orival : "");
														$strXMLEB .= "</CoverageLevelCodeVal>";								
													$strXMLEB .= "</CoverageLevelCode>";
													break;
												case 2:
													$strXMLEB .= "<ServiceTypeCode>";
													$orival = $this->getVisionShareError("vision_share_271", "Service_Type_Code", $strEBValVal);
														$strXMLEB .= "<ServiceTypeCodeId>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</ServiceTypeCodeId>";
														
														$strXMLEB .= "<ServiceTypeCodeVal>";
														$strXMLEB .= (is_string($orival) ? $orival : "");
														$strXMLEB .= "</ServiceTypeCodeVal>";
													$strXMLEB .= "</ServiceTypeCode>";
													break;
												case 3:
													$strXMLEB .= "<InsuranceTypeCode>";
													$orival = $this->getVisionShareError("vision_share_271", "Insurance_Type_Code", $strEBValVal);
													if(((string)$strEBValVal == "MB") && (empty($strEBResponceTemp) == false) && (empty($strEBResponce) == true)){
														$strEBResponce = $strEBResponceTemp;
														$strEBInsuranceTypeCode = $strEBValVal;
													}
														//$arrEBInsuranceTypeCode[] = $strEBValVal;										
														$strXMLEB .= "<InsuranceTypeCodeId>";
														$strXMLEB .= trim($strEBValVal);
														$strXMLEB .= "</InsuranceTypeCodeId>";
														
														$strXMLEB .= "<InsuranceTypeCodeVal>";
														$strXMLEB .= (is_string($orival) ? $orival : "");
														$strXMLEB .= "</InsuranceTypeCodeVal>";
													$strXMLEB .= "</InsuranceTypeCode>";
													break;
												case 4:
													$strXMLEB .= "<PlanCoverageDescription>";
													$strXMLEB .= $strEBValVal;
													$strXMLEB .= "</PlanCoverageDescription>";
													break;
												case 5:
													$strXMLEB .= "<TimePeriodQualifier>";
													$orival = $this->getVisionShareError("vision_share_271", "Time_Period_Qualifier", $strEBValVal);										
														$strXMLEB .= "<TimePeriodQualifierId>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</TimePeriodQualifierId>";
														
														$strXMLEB .= "<TimePeriodQualifierVal>";
														$strXMLEB .= (is_string($orival) ? $orival : "");
														$strXMLEB .= "</TimePeriodQualifierVal>";
													$strXMLEB .= "</TimePeriodQualifier>";
													break;
												case 6:
													$strXMLEB .= "<MonetaryAmount>";
													$strXMLEB .= $strEBValVal;
													$strXMLEB .= "</MonetaryAmount>";
													break;
												case 7:
													$strXMLEB .= "<Percent>";
													$strXMLEB .= $strEBValVal;
													$strXMLEB .= "</Percent>";
													break;
												case 8:
													$strXMLEB .= "<QuantityQualifier>";
													$orival = $this->getVisionShareError("vision_share_271", "Quantity_Qualifier", $strEBValVal);
														$strXMLEB .= "<QuantityQualifierId>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</QuantityQualifierId>";
														
														$strXMLEB .= "<QuantityQualifierVal>";
														$strXMLEB .= (is_string($orival) ? $orival : "");
														$strXMLEB .= "</QuantityQualifierVal>";
													$strXMLEB .= "</QuantityQualifier>";
													break;
												case 9:
													$strXMLEB .= "<Quantity>";
													$strXMLEB .= $strEBValVal;
													$strXMLEB .= "</Quantity>";
													break;
												case 10:
													$strXMLEB .= "<ConditionResponseCode>";
													$orival = $this->getVisionShareError("vision_share_271", "Condition_Response_Code", $strEBValVal);
														$strXMLEB .= "<ConditionResponseCodeId>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</ConditionResponseCodeId>";
														
														$strXMLEB .= "<ConditionResponseCodeVal>";
														$strXMLEB .= (is_string($orival) ? $orival : "");
														$strXMLEB .= "</ConditionResponseCodeVal>";
													$strXMLEB .= "</ConditionResponseCode>";
													break;					
												case 11:
													$strXMLEB .= "<PlanConditionResponseCode>";
													$orival = $this->getVisionShareError("vision_share_271", "plan_condition_response_code", $strEBValVal);
														$strXMLEB .= "<PlanConditionResponseCodeId>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</PlanConditionResponseCodeId>";
														
														$strXMLEB .= "<PlanConditionResponseCodeVal>";
														$strXMLEB .= (is_string($orival) ? $orival : "");
														$strXMLEB .= "</PlanConditionResponseCodeVal>";
													$strXMLEB .= "</PlanConditionResponseCode>";
													break;	
												case 12:
													$strXMLEB .= "<CompositeMedicalProcedureIdentifier>";
													$strValtemp = "";
													$arrValtemp = array();
													$strValtemp = trim($strEBValVal);
													$arrValtemp = explode("|",$strValtemp);										
													$orival = $this->getVisionShareError("vision_share_271", "composite_medical_procedure_identifier", trim($arrValtemp[0]));
														$strXMLEB .= "<CompositeMedicalProcedureIdentifier_1>";
															$strXMLEB .= "<CompositeMedicalProcedureIdentifierId_1>";
															$strXMLEB .= trim($arrValtemp[0]);
															$strXMLEB .= "</CompositeMedicalProcedureIdentifierId_1>";
															
															$strXMLEB .= "<CompositeMedicalProcedureIdentifierVal_1>";
															$strXMLEB .= (is_string($orival) ? $orival : "");
															$strXMLEB .= "</CompositeMedicalProcedureIdentifierVal_1>";
														$strXMLEB .= "</CompositeMedicalProcedureIdentifier_1>";
													
														$strXMLEB .= "<CompositeMedicalProcedureIdentifier_2>";
														$strXMLEB .= trim($arrValtemp[1]);
														$strXMLEB .= "</CompositeMedicalProcedureIdentifier_2>";
													
														$strXMLEB .= "<CompositeMedicalProcedureIdentifier_3>";
														$strXMLEB .= trim($arrValtemp[2]);
														$strXMLEB .= "</CompositeMedicalProcedureIdentifier_3>";
														
														$strXMLEB .= "<CompositeMedicalProcedureIdentifier_4>";
														$strXMLEB .= trim($arrValtemp[3]);
														$strXMLEB .= "</CompositeMedicalProcedureIdentifier_4>";
														
														$strXMLEB .= "<CompositeMedicalProcedureIdentifier_5>";
														$strXMLEB .= trim($arrValtemp[4]);
														$strXMLEB .= "</CompositeMedicalProcedureIdentifier_5>";
														
														$strXMLEB .= "<CompositeMedicalProcedureIdentifier_6>";
														$strXMLEB .= trim($arrValtemp[5]);
														$strXMLEB .= "</CompositeMedicalProcedureIdentifier_6>";
														
														$strXMLEB .= "<CompositeMedicalProcedureIdentifier_7>";
														$strXMLEB .= trim($arrValtemp[6]);
														$strXMLEB .= "</CompositeMedicalProcedureIdentifier_7>";
													$strXMLEB .= "</CompositeMedicalProcedureIdentifier>";
													break;							
											endswitch;
										}
										$intCountEBVal = count($arrEBVal);
										for($a = $intCountEBVal; $a <= 12; $a++){
											$a = (int)$a;
											switch ($a):
												case 0:
													$strXMLEB .= "<EligibilityOrBenefitInformation>
																	<EligibilityOrBenefitInformationId></EligibilityOrBenefitInformationId>
																	<EligibilityOrBenefitInformationVal></EligibilityOrBenefitInformationVal>
																</EligibilityOrBenefitInformation>";
													break;
												case 1:
													$strXMLEB .= "<CoverageLevelCode>
																	<CoverageLevelCodeId></CoverageLevelCodeId>
																	<CoverageLevelCodeVal></CoverageLevelCodeVal>
																</CoverageLevelCode>";
													break;
												case 2:
													$strXMLEB .= "<ServiceTypeCode>
																	<ServiceTypeCodeId></ServiceTypeCodeId>
																	<ServiceTypeCodeVal></ServiceTypeCodeVal>
																</ServiceTypeCode>";
													break;
												case 3:
													$strXMLEB .= "<InsuranceTypeCode>
																	<InsuranceTypeCodeId></InsuranceTypeCodeId>
																	<InsuranceTypeCodeVal></InsuranceTypeCodeVal>
																</InsuranceTypeCode>";
													break;
												case 4:
													$strXMLEB .= "<PlanCoverageDescription></PlanCoverageDescription>";
													break;
												case 5:
													$strXMLEB .= "<TimePeriodQualifier>
																	<TimePeriodQualifierId></TimePeriodQualifierId>
																	<TimePeriodQualifierVal></TimePeriodQualifierVal>
																</TimePeriodQualifier>";
													break;
													break;
												case 6:
													$strXMLEB .= "<MonetaryAmount></MonetaryAmount>";
													break;
												case 7:
													$strXMLEB .= "<Percent></Percent>";
													break;
												case 8:
													$strXMLEB .= "<QuantityQualifier>
																	<QuantityQualifierId></QuantityQualifierId>
																	<QuantityQualifierVal></QuantityQualifierVal>
																</QuantityQualifier>";
													break;
												case 9:
													$strXMLEB .= "<Quantity></Quantity>";
													break;
												case 10:
													$strXMLEB .= "<ConditionResponseCode>
																	<ConditionResponseCodeId></ConditionResponseCodeId>
																	<ConditionResponseCodeVal></ConditionResponseCodeVal>
																</ConditionResponseCode>";
													break;					
												case 11:
													$strXMLEB .= "<PlanConditionResponseCode>
																	<PlanConditionResponseCodeId></PlanConditionResponseCodeId>
																	<PlanConditionResponseCodeVal></PlanConditionResponseCodeVal>
																</PlanConditionResponseCode>";
													break;	
												case 12:
													$strXMLEB .= "<CompositeMedicalProcedureIdentifier>
																	<CompositeMedicalProcedureIdentifier_1>
																		<CompositeMedicalProcedureIdentifierId_1></CompositeMedicalProcedureIdentifierId_1>
																		<CompositeMedicalProcedureIdentifierVal_1></CompositeMedicalProcedureIdentifierVal_1>
																	</CompositeMedicalProcedureIdentifier_1>
																	<CompositeMedicalProcedureIdentifier_2></CompositeMedicalProcedureIdentifier_2>
																	<CompositeMedicalProcedureIdentifier_3></CompositeMedicalProcedureIdentifier_3>
																	<CompositeMedicalProcedureIdentifier_4></CompositeMedicalProcedureIdentifier_4>
																	<CompositeMedicalProcedureIdentifier_5></CompositeMedicalProcedureIdentifier_5>
																	<CompositeMedicalProcedureIdentifier_6></CompositeMedicalProcedureIdentifier_6>
																	<CompositeMedicalProcedureIdentifier_7></CompositeMedicalProcedureIdentifier_7>
																</CompositeMedicalProcedureIdentifier>";
													break;													
											endswitch;
										}
										///////////////////////////////////////////
										$strXMLEB .= "</EBLOOP_".$key."_".$intKeyValVal.">";							
									}
									//$strXMLEB .= "</EBLOOP_".$key.">";
									$strXMLEB .= "</EBLOOPINFO>";
								}
							}
						}
					}
					$strXMLEB .= "</DTPEBLOOP>";
					//$strEBResponce = implode(",", $arrEBResponce);
					
					$patientDirVsXMLDir = "vs_271_response_xml/";
					if(!is_dir($upLoadPath.$patientDir.$patientDirVsXMLDir)){
						//Create patient VS 271 responce directory
						mkdir($upLoadPath.$patientDir.$patientDirVsXMLDir, 0700, true);
					}
					$fileNameXML = date("Y-m-d-H-i-s_").$patId."_XML";
					$pathToWrite271xml = $upLoadPath.$patientDir.$patientDirVsXMLDir.$fileNameXML.".xml";		
					file_put_contents($pathToWrite271xml,$strXMLEB);					
					$qryUpdate = "update real_time_medicare_eligibility set response_271_file_path = '".$pathToWrite271EDI."', 
									xml_271_responce = '".$pathToWrite271xml."', EB_responce = '".$strEBResponce."', responce_date_time = NOW(), 
									transection_error = '".$transectionError."', responce_pat_policy_no = '".$strHL3RQPolicyNo."', responce_pat_add1 = '".$strHL3RQAdd1."', 
									responce_pat_add2 = '".$strHL3RQAdd2."', responce_pat_city = '".$strHL3RQCity."', responce_pat_state = '".$strHL3RQState."', 
									responce_pat_zip  = '".$strHL3RQZip."', responce_pat_dob = '".$strHL3RQPatDOB."', responce_pat_fname = '".$strHL3RQFName."', 
									responce_pat_lname = '".$strHL3RQLName."', responce_pat_mname  = '".$strHL3RQMName."', 
									response_deductible = '".$strValueD."', response_copay = '".$strValueCP."', response_co_insurance = '".$strValueCoIns."', 
									
									responce_dep_policy_no = '".$strHL4RQPolicyNo."', responce_dep_add1 = '".$strHL4RQAdd1."', 
									responce_dep_add2 = '".$strHL4RQAdd2."', responce_dep_city = '".$strHL4RQCity."', responce_dep_state = '".$strHL4RQState."', 
									responce_dep_zip  = '".$strHL4RQZip."', responce_dep_dob = '".$strHL4RQPatDOB."', responce_dep_fname = '".$strHL4RQFName."', 
									responce_dep_lname = '".$strHL4RQLName."', responce_dep_mname  = '".$strHL4RQMName."', responce_dep_relation  = '".$strDepRel."'
									
									where id = '".$intNewId."'";
					$rsQryUpdate =  imw_query($qryUpdate);
					if((int)$intPatAppIdSA > 0 && stristr($transectionError,'error')==false){
						$qryUpdateApp = "update schedule_appointments set rte_id = '".$intNewId."' where id = '".$intPatAppIdSA."'";
						$rsUpdateApp = imw_query($qryUpdateApp);
					}
				}
				
				$strShowMsg = "no";
				if($blCopayExits == true){
					$strShowMsg = "yes";
				}
				
				if(empty($transectionError) == false){
					return $transectionError;
				}
				else{										
					return array($strEBResponce, $strEBInsuranceTypeCode, $intNewId, $strShowMsg);
					//return array($arrEBResponce, $arrEBInsuranceTypeCode);					
				}
			}
		}		
	}
	function getVisionShareError($module, $subModule, $strTAReasonCode){
		return $this->objCoreLang->get_vocabulary($module, (string)trim($subModule), (string)$strTAReasonCode);
	}
	
	function getDateOfServiceFinalized($pid){
		$getSqlDateFormat = getSqlDateFormat();
		$strDOS = "";
		$qryGetDOS = "SELECT DATE_FORMAT(chart_master_table.date_of_service, '$getSqlDateFormat') AS date_of_service FROM chart_master_table ".
						//"INNER JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
						"WHERE chart_master_table.patient_id = '".$pid."' 
						AND chart_master_table.finalize = '0' ORDER BY chart_master_table.update_date DESC,chart_master_table.id DESC Limit 0,1";		
		$rsGetDOS =  imw_query($qryGetDOS);
		if($rsGetDOS){
			if(imw_num_rows($rsGetDOS) > 0){
				$rowGetDOS = imw_fetch_array($rsGetDOS);
				$strDOS = $rowGetDOS["date_of_service"];
				imw_free_result($rsGetDOS);
				return $strDOS;
			}
			else{
				$qryGetDOSNew = "SELECT DATE_FORMAT(chart_master_table.update_date, '$getSqlDateFormat') AS date_of_service FROM chart_master_table 
								WHERE chart_master_table.patient_id = '".$pid."' AND chart_master_table.finalize = '0' 
								ORDER BY chart_master_table.update_date DESC,chart_master_table.id DESC Limit 0,1";	
				$rsGetDOSNew = imw_query($qryGetDOSNew);
				if($rsGetDOSNew){
					if(imw_num_rows($rsGetDOSNew) > 0){
						$rowGetDOSNew = imw_fetch_array($rsGetDOSNew);						
						$strDOS = $rowGetDOSNew["date_of_service"];
						imw_free_result($rsGetDOSNew);
						return $strDOS;
					}
					else{
						imw_free_result($rsGetDOSNew);
						return $strDOS;
					}
				}
			}
		}
	}
	
	function make270EDI5010($rqInsRecId, $from = "insTab", $patId = 0, $elReqDate = "", $intInsCaseId = 0){
		$retError = array();
		//Getting default group of head quarter facility
		$dbFacGroupID = 0;
		$qryGetDefaultGroup = "select default_group as facGroup from facility where facility_type = '1'";
		$rsGetDefaultGroup = imw_query($qryGetDefaultGroup);
		if($rsGetDefaultGroup){
			if(imw_num_rows($rsGetDefaultGroup) > 0){		
				$rowGetDefaultGroup = imw_fetch_array($rsGetDefaultGroup);
				$dbFacGroupID = $rowGetDefaultGroup["facGroup"];		
			}
			imw_free_result($rsGetDefaultGroup);
		}
		if($dbFacGroupID > 0){
			//Getting group Medicare Receiver Id and Medicare Submitter Id 
			$dbMedicareReceiverId = $dbMedicareSubmitterId = $dbGroupName = $dbGroupNPI = $dbGroupFederalTaxID = $dbGroupAdd1 = $dbGroupAdd2 = $dbGroupCity = $dbGroupState = $dbGroupZip = "";
			$dbGroupConName = $dbGroupTelephone = $dbGroupFax = $dbGroupEmail = "";
			$qryGetMedRecSubID = "select name as groupName, group_NPI as groupNPI, group_Federal_EIN as groupFederalTaxID, group_Address1 as groupAdd1, group_Address2 as groupAdd2, group_City as groupCity, 
									group_State as groupState, group_Zip as groupZip, Contact_Name as groupConName, replace(group_Telephone,'-','') as groupTelephone, replace(group_Fax,'-','') as groupFax, group_Email as groupEmail, 
									MedicareReceiverId, MedicareSubmitterId from groups_new where gro_id = '".$dbFacGroupID."'";
			$rsGetMedRecSubID = imw_query($qryGetMedRecSubID);
			if($rsGetMedRecSubID){
				if(imw_num_rows($rsGetMedRecSubID) > 0){		
					$rowGetMedRecSubID = imw_fetch_array($rsGetMedRecSubID);
					$dbMedicareReceiverId = trim($rowGetMedRecSubID["MedicareReceiverId"]);
					$dbMedicareSubmitterId = trim($rowGetMedRecSubID["MedicareSubmitterId"]);
					$dbGroupName = trim($rowGetMedRecSubID["groupName"]);
					$dbGroupName = substr($dbGroupName, 0, 21);
					$dbGroupNPI = trim($rowGetMedRecSubID["groupNPI"]);
					$dbGroupFederalTaxID = trim($rowGetMedRecSubID["groupFederalTaxID"]);
					$dbGroupAdd1 = trim($rowGetMedRecSubID["groupAdd1"]);
					$dbGroupAdd2 = trim($rowGetMedRecSubID["groupAdd2"]);
					$dbGroupCity = trim($rowGetMedRecSubID["groupCity"]);
					$dbGroupState = trim($rowGetMedRecSubID["groupState"]);
					$dbGroupZip = trim($rowGetMedRecSubID["groupZip"]);
					$dbGroupConName = trim($rowGetMedRecSubID["groupConName"]);
					$dbGroupTelephone = trim($rowGetMedRecSubID["groupTelephone"]);
					$dbGroupFax = trim($rowGetMedRecSubID["groupFax"]);
					$dbGroupEmail = trim($rowGetMedRecSubID["groupEmail"]);				
				}
				imw_free_result($rsGetDefaultGroup);
			}
			if((empty($dbMedicareReceiverId) == false) && (empty($dbMedicareSubmitterId) == false)){
				$authorInfo = $secretInfo = $strNewRTMEMAXID = $strNewRTMEMAXIDRest = "";
				$medRecIdRest = $medSubIdRest = $newRTMEMAXID = 0;				
				
				$authorInfo = $this->makeSpace(10);
				$secretInfo = $this->makeSpace(10);
				$interchangeReceiverID = "CMS";
				$medRecIdRest = 15 - strlen($interchangeReceiverID);
				$recieveSpace = "";
				$recieveSpace = $this->makeSpace($medRecIdRest);				
				$medSubIdRest = 15 - strlen($dbMedicareSubmitterId);
				$submitterSpace = "";
				$submitterSpace = $this->makeSpace($medSubIdRest);
				$qryGetMaxRealTimeId = "select max(id) as RTMEMAXID from real_time_medicare_eligibility";
				$rsGetMaxRealTimeId =  imw_query($qryGetMaxRealTimeId);
				if($rsGetMaxRealTimeId){
					$rowGetMaxRealTimeId = imw_fetch_array($rsGetMaxRealTimeId);
					$newRTMEMAXID = $rowGetMaxRealTimeId["RTMEMAXID"];
					imw_free_result($rsGetMaxRealTimeId);
					$newRTMEMAXID++;
					$strNewRTMEMAXID = (string)$newRTMEMAXID;
					$strNewRTMEMAXIDRest = 9 - strlen($strNewRTMEMAXID);
					$InterCtrlNumber = "";
					$InterCtrlNumber = $this->makeSpace($strNewRTMEMAXIDRest,"0");
					$InterCtrlNumber = $InterCtrlNumber.$strNewRTMEMAXID;
				}
				$dbinsCompName = $dbSubscriberLName = $dbSubscriberFName = $dbSubscriberMName = $dbInsPolicyClaimNo = $dbSubscriberAdd1 = $dbSubscriberAdd2 = "";
				$dbSubscriberCity = $dbSubscriberState = $dbSubscriberZip = $dbSubscriberDOB = $dbSubscriberGender = $dbSubscriberDate = $dbSubscriberSS = "";
				$dbInsPatId = $dbInsId = 0;
				if($from == "insTab"){
					$getPatInsData = "select insComp.name as insCompName, insData.subscriber_lname as subscriberLName, insData.subscriber_fname as subscriberFName, insData.subscriber_suffix as subscriberSuffix, 
										insData.subscriber_mname as subscriberMName, insData.policy_number as insPolicyClaimNo, insData.subscriber_street as subscriberAdd1, 
										insData.subscriber_street_2 as subscriberAdd2, insData.subscriber_city as subscriberCity, insData.subscriber_state as subscriberState, 
										insData.subscriber_postal_code as subscriberZip, replace(DATE_FORMAT(insData.subscriber_DOB, '%Y%m%d'),'-','') as subscriberDOB, insData.subscriber_sex as subscriberGender, 
										replace(DATE_FORMAT(insData.effective_date, '%Y%m%d'),'-','') as subscriberDate, insData.pid as insPatId, insData.id as insId,
										replace(insData.subscriber_ss,'-','') as subscriberSS									 
										from insurance_data insData 
										LEFT JOIN insurance_companies insComp on insComp.id = insData.provider
										where insData.id = '".$rqInsRecId."' 
										LIMIT 1;
										";
				}
				elseif($from == "scheduler"){
					$getPatInsData = "select insComp.name as insCompName, insData.subscriber_lname as subscriberLName, insData.subscriber_fname as subscriberFName, insData.subscriber_suffix as subscriberSuffix, 
										insData.subscriber_mname as subscriberMName, insData.policy_number as insPolicyClaimNo, insData.subscriber_street as subscriberAdd1, 
										insData.subscriber_street_2 as subscriberAdd2, insData.subscriber_city as subscriberCity, insData.subscriber_state as subscriberState, 
										insData.subscriber_postal_code as subscriberZip, replace(DATE_FORMAT(insData.subscriber_DOB, '%Y%m%d'),'-','') as subscriberDOB, 
										insData.subscriber_sex as subscriberGender, replace(DATE_FORMAT(insData.effective_date, '%Y%m%d'),'-','') as subscriberDate, 
										insData.pid as insPatId, insData.id as insId, replace(insData.subscriber_ss,'-','') as subscriberSS									 
										from insurance_data insData 
										LEFT JOIN insurance_companies insComp on insComp.id = insData.provider
										where insData.pid = '".$patId."' and insData.actInsComp = '1' and insData.subscriber_relationship = 'self' ";
										if((int)$intInsCaseId > 0){
											$getPatInsData .= " and insData.ins_caseid = '".$intInsCaseId."' ";
										} 
										$getPatInsData .= " and insData.type = 'primary'
										and insComp.claim_type = '1' 
										ORDER BY insData.id DESC LIMIT 1;
										";
				}
				$rsPatInsData = imw_query($getPatInsData);
								
				if($rsPatInsData){
					if(imw_num_rows($rsPatInsData) > 0){
						$rowPatInsData = imw_fetch_array($rsPatInsData);
						$dbinsCompName = trim($rowPatInsData["insCompName"]);
						$dbSubscriberLName = trim($rowPatInsData["subscriberLName"]);
						$dbSubscriberFName = trim($rowPatInsData["subscriberFName"]);
						$dbSubscriberMName = trim($rowPatInsData["subscriberMName"]);
						$dbSubscriberSuffix = trim($rowPatInsData["subscriberSuffix"]);
						$dbInsPolicyClaimNo = trim($rowPatInsData["insPolicyClaimNo"]);
						$dbInsPolicyClaimNo = str_replace(array("-"," "), "", $dbInsPolicyClaimNo);
						$dbSubscriberAdd1 = trim($rowPatInsData["subscriberAdd1"]);
						$dbSubscriberAdd2 = trim($rowPatInsData["subscriberAdd2"]);
						$dbSubscriberCity = trim($rowPatInsData["subscriberCity"]);
						$dbSubscriberState = trim($rowPatInsData["subscriberState"]);
						$dbSubscriberZip = trim($rowPatInsData["subscriberZip"]);
						$dbSubscriberDOB = trim($rowPatInsData["subscriberDOB"]);
						$dbSubscriberGender = trim($rowPatInsData["subscriberGender"]);
						$dbSubscriberDate = trim($rowPatInsData["subscriberDate"]);
						$dbInsPatId = trim($rowPatInsData["insPatId"]);
						$dbInsId = trim($rowPatInsData["insId"]);
						$dbSubscriberSS = trim($rowPatInsData["subscriberSS"]);																		
						if(empty($dbSubscriberSS) == false){
							$ediDate1 = $ediDate2 = $ediTime = $ediTime1 = "";
							$ediDate1 = date('ymd');
							$ediDate2 = date('Ymd');
							$ediTime = date('Hi');
							$ediTime1 = date('His');
							$edi270CrlHeader = $edi270CrlTrailer = "";
							$edi270FunGrpHeader = $edi270FunGrpTrailer = "";
							$edi270CrlHeader = "ISA*00*".$authorInfo."*00*".$secretInfo."*ZZ*".$dbMedicareSubmitterId.$submitterSpace."*ZZ*".$interchangeReceiverID.$recieveSpace."*".$ediDate1."*".$ediTime."*^*00501*".$InterCtrlNumber."*0*P*:~";
							$edi270CrlTrailer = "IEA*1*".$InterCtrlNumber."~";
							$edi270FunGrpHeader = "GS*HS*".$dbMedicareSubmitterId."*".$interchangeReceiverID."*".$ediDate2."*".$ediTime1."*".$newRTMEMAXID."*X*005010X279A1~";
							$edi270FunGrpTrailer = "GE*1*".$newRTMEMAXID."~";
							$edi270TranData = "";
							$edi270STCounter = 0;
							$edi270TranData = "ST*270*".$InterCtrlNumber."*005010X279A1~"; //TRANSACTION SET HEADER
							$edi270STCounter++;
							$edi270TranData .=	"BHT*0022*13*ALL*".$ediDate2."*".$ediTime."~"; //BEGINNING OF HIERARCHICAL TRANSACTION
							$edi270STCounter++;
							$edi270TranData .=	"HL*1**20*1~"; //Loop: 2000A
							$edi270STCounter++;
							$edi270TranData .=	"NM1*PR*2*".$interchangeReceiverID."*****PI*".$interchangeReceiverID."~"; //Loop: 2100A
							$edi270STCounter++;
							$edi270TranData .=	"HL*2*1*21*1~"; //Loop: 2000B
							$edi270STCounter++;
							$edi270TranData .=	"NM1*1P*2*".$dbGroupName."*****XX*".$dbGroupNPI."~"; //Loop: 2100B	
							//$edi270TranData .=	"NM1*1P*2*".$dbGroupName."*****XX*9999999997~"; //Loop: 2100B	
							
							$edi270STCounter++;
							if(empty($dbGroupFederalTaxID) == false){
								$edi270TranData .=	"REF*TJ*".$dbGroupFederalTaxID."~"; //Loop: 2100B ->INFORMATION RECEIVER ADDITIONAL IDENTIFICATION
								$edi270STCounter++;
							}
							/*if((empty($dbGroupAdd1) == false) && (empty($dbGroupAdd2) == false)){
								$edi270TranData .=	"N3*".$dbGroupAdd1."*".$dbGroupAdd2."~"; //Loop: 2100B -> INFORMATION RECEIVER ADDRESS	
								$edi270STCounter++;
							}
							else{
								$edi270TranData .=	"N3*".$dbGroupAdd1."~"; //Loop: 2100B -> INFORMATION RECEIVER ADDRESS	
								$edi270STCounter++;
							}
							if((empty($dbGroupCity) == false) && (empty($dbGroupState) == false) && (empty($dbGroupZip) == false)){
								$edi270TranData .=	"N4*".$dbGroupCity."*".$dbGroupState."*".$dbGroupZip."~"; //Loop: 2100B -> INFORMATION RECEIVER CITY/STATE/ZIP CODE	
								$edi270STCounter++;
							}*/
							$edi270TranData .=	"HL*3*2*22*0~"; //Loop: 2000C -> SUBSCRIBER LEVEL
							$edi270STCounter++;
							/*if(empty($dbGroupFederalTaxID) == false){
								$edi270TranData .=	"TRN*1*".$newRTMEMAXID."*1".$dbGroupFederalTaxID."~"; //Loop: 2000C -> SUBSCRIBER TRACE NUMBER	
								$edi270STCounter++;
							}
							else{
								*/
								//$edi270TranData .= "TRN*1*".$newRTMEMAXID."*9".$dbGroupName."~"; //Loop: 2000C -> SUBSCRIBER TRACE NUMBER
								//$edi270TranData .= "TRN*1*TEST-TEST*9TEST~";
								//$edi270STCounter++;
							//}
						
							if(($dbSubscriberLName != "") && ($dbSubscriberFName != "") && ($dbInsPolicyClaimNo != "")){
								$edi270TranData .=	"NM1*IL*1*".$dbSubscriberLName."*".$dbSubscriberFName."*".$dbSubscriberMName."**".trim($dbSubscriberSuffix)."*MI*".$dbInsPolicyClaimNo."~"; //Loop: 2100C -> SUBSCRIBER NAME	
								$edi270STCounter++;
							}
							$edi270TranData .=	"REF*SY*".$dbSubscriberSS."~"; //Loop: 2100C -> SUBSCRIBER ADDITIONAL IDENTIFICATION	
							$edi270STCounter++;				
							if((empty($dbSubscriberAdd1) == false) || (empty($dbSubscriberAdd2) == false)){
								$subAdd = "N3";
								if(empty($dbSubscriberAdd1) == false){
									$subAdd .=	"*".$dbSubscriberAdd1; //Loop: 2100C -> SUBSCRIBER ADDRESS1
								}
								if(empty($dbSubscriberAdd2) == false){
									$subAdd .=	"*".$dbSubscriberAdd2; //Loop: 2100C -> SUBSCRIBER ADDRESS2
								}
								$subAdd .=	"~";
								//$edi270TranData .=	$subAdd; //Loop: 2100C -> SUBSCRIBER ADDRESS
								//$edi270STCounter++;
							}
							if((empty($dbSubscriberCity) == false) || (empty($dbSubscriberState) == false) || (empty($dbSubscriberZip) == false)){
								$subCSZ = "N4";
								if(empty($dbSubscriberCity) == false){
									$subCSZ .=	"*".$dbSubscriberCity; //Loop: 2100C -> SUBSCRIBER CITY
								}
								if(empty($dbSubscriberState) == false){
									$subCSZ .=	"*".$dbSubscriberState; //Loop: 2100C -> SUBSCRIBER STATE
								}
								if(empty($dbSubscriberZip) == false){
									$subCSZ .=	"*".$dbSubscriberZip; //Loop: 2100C -> SUBSCRIBER ZIP CODE
								}
								$subCSZ .=	"~";
								//$edi270TranData .=	$subCSZ; //Loop: 2100C -> SUBSCRIBER CITY/STATE/ZIP CODE
								//$edi270STCounter++;
							}
							if((empty($dbSubscriberDOB) == false) || (empty($dbSubscriberGender) == false)){
								$sumDemoInfo = "DMG";
								if(empty($dbSubscriberDOB) == false){
									$sumDemoInfo .=	"*D8*".$dbSubscriberDOB; //Loop: 2100C -> SUBSCRIBER DOB
								}
								if(empty($dbSubscriberGender) == false){
									$sumDemoInfo .=	"*".$dbSubscriberGender[0]; //Loop: 2100C -> SUBSCRIBER GENDER
								}
								$sumDemoInfo .=	"~";
								$edi270TranData .=	$sumDemoInfo; //Loop: 2100C -> SUBSCRIBER DEMOGRAPHIC INFORMATION			
								$edi270STCounter++;
							}				
							//if(empty($dbSubscriberDate) == false){
							$month = $day = $year = $imedicDOS = $subDate = "";	
							$pastdate = $futuredate = "";
							if($elReqDate == ""){
								$subDate = date("Ymd");
								$imedicDOS = date("Y-m-d");
							}
							else{							
								$subDate = str_replace("-","",$elReqDate);
								$imedicDOS = $elReqDate;							
							}
							$edi270TranData .=	"DTP*291*D8*".$subDate."~"; //Loop: 2100C -> SUBSCRIBER DATE
							
							$edi270STCounter++;
							//}
							$edi270TranData .=	"EQ*30~"; //Loop: 2110C -> SUBSCRIBER ELIGIBILITY OR BENEFIT INQUIRY INFORMATION
							$edi270STCounter++;
							//$edi270TranData .=	"EQ*AL**IND~"; //Loop: 2110C -> Vision (Optometry)
							//$edi270STCounter++;
							$edi270STCounter++;
							$edi270TranData .=	"SE*".$edi270STCounter."*".$InterCtrlNumber."~"; //TRANSACTION SET TRAILER
							
							$edi270TranDataFinal = "";
							$edi270TranDataFinal .= $edi270CrlHeader; //INTERCHANGE CONTROL HEADER
							$edi270TranDataFinal .= $edi270FunGrpHeader; //FUNCTIONAL GROUP HEADER
							$edi270TranDataFinal .= $edi270TranData; //EDI 270 TRANSACTION DATA
							$edi270TranDataFinal .= $edi270FunGrpTrailer; //FUNCTIONAL GROUP TRAILER
							$edi270TranDataFinal .= $edi270CrlTrailer; //INTERCHANGE CONTROL TRAILER				
							//return array($edi270TranDataFinal, $dbInsPatId, $dbInsId, $imedicDOS, $dbFacGroupID, $retError);
						}
						else{
							$retError[] = " - Subscriber SSN Not Found!";
						}
					}
					else{
						$retError[] = " - Subscriber Data Not Found!";
					}
					imw_free_result($rsPatInsData);
				}
				else{
					$retError[] = " - Subscriber Data Not Found!";
				}
			}
			else{
				$retError[] = " - Group Medicare Receiver Id and Medicare Submitter Id Not Found!";
			}
		}
		else{
			$retError[] = " - No Default Facility Found!";
		}
		return array($edi270TranDataFinal, $dbInsPatId, $dbInsId, $imedicDOS, $dbFacGroupID, $retError);
	}
	
	function send270Request5010($EDI270, $patId, $patInsId, $imedicDOS, $groupID, $intPatAppIdSA = 0){
		if(empty($EDI270) == false){
			$strValueCoIns = $strValueD = $strValueCP = "";
			$blCopayExits = false;
			$EDI270 = strtoupper($EDI270);
			$certSSLKeyPath = $certPath = $certClientPath = $vsPassword = "";
					
			$serviceOutput = $realMedCareElgblID = $realMedCareElgblURI = $batchSubmitID = $batchSubmitURI = $batchReceiveListID = $batchReceiveListURI = $certUse = "";
			$batchSubmitID5010 = $batchSubmitURI5010 = $batchReceiveListID5010 = $batchReceiveListURI5010 = "";
			$strCertUse = $this->getCertNameConstent($groupID);
			$serviceOutput = $this->services($strCertUse);
			$arrServices = $arrReceiveListXML = $arrVSBRLAll = $arrCertUse = array();
			$receiveListXML = "";
			$arrServices = $this->XMLToArray($serviceOutput);	
			
			$ArrgetVSIDURI = $this->getVSIDURI($arrServices);
			$realMedCareElgblID			= $ArrgetVSIDURI[0];
			$realMedCareElgblURI		= $ArrgetVSIDURI[1];
			$batchSubmitID				= $ArrgetVSIDURI[2];
			$batchSubmitURI				= $ArrgetVSIDURI[3];
			$batchReceiveListID			= $ArrgetVSIDURI[4];
			$batchReceiveListURI		= $ArrgetVSIDURI[5];
			$batchSubmitID5010			= $ArrgetVSIDURI[6];
			$batchSubmitURI5010			= $ArrgetVSIDURI[7];
			$batchReceiveListID5010		= $ArrgetVSIDURI[8];
			$batchReceiveListURI5010	= $ArrgetVSIDURI[9];
			
			//$realMedCareElgblID = $realMedCareElgblURI = "sdf";
			if((empty($realMedCareElgblID) == false) && (empty($realMedCareElgblURI) == false)){				
				$fileNameEDI = $dataEDI = $pathToWrite270EDI = $ediToSend = "";
				$fileNameEDI = date("Y-m-d-H-i-s_").$patId."_270_5010";
				$upLoadPath = $GLOBALS['fileroot'].'/interface/main/uploaddir/';
				$patientDir = "PatientId_".$patId."/";
				if(!is_dir($upLoadPath.$patientDir)){
					//Create patient directory
					mkdir($upLoadPath.$patientDir, 0700, true);
				}
				$patientDirVs270Dir = "vs_270_request/";
				if(!is_dir($upLoadPath.$patientDir.$patientDirVs270Dir)){
					//Create patient VS 270 request directory
					mkdir($upLoadPath.$patientDir.$patientDirVs270Dir, 0700, true);
				}				
				$pathToWrite270EDI = $upLoadPath.$patientDir.$patientDirVs270Dir.$fileNameEDI.".txt";		
				file_put_contents($pathToWrite270EDI,$EDI270);
				if(file_exists($pathToWrite270EDI) == true){
					$sqlEDIPath = "";
					$sqlEDIPath = $patientDir.$patientDirVs270Dir.$fileNameEDI.".txt";
					
					$ediToSend = file_get_contents($pathToWrite270EDI);
					$ediLen = filesize($pathToWrite270EDI);
					$intNewId = 0;
					$qrySelPatCompId = "select provider from insurance_data where id = '".$patInsId."'";
					$rsSelPatCompId = imw_query($qrySelPatCompId);
					$rowSelPatCompId = imw_fetch_row($rsSelPatCompId);
					$patCompId = $rowSelPatCompId[0];				
					$qryInsert = "insert into real_time_medicare_eligibility (request_270_file_path, request_date_time, request_operator, patient_id, ins_data_id, imedic_DOS, hipaa_5010) 
									values ('".$sqlEDIPath."', NOW(), '".$_SESSION['authId']."', '".$patId."', '".$patInsId."', '".$imedicDOS."', '1')";				
					$rsQryInsert =  imw_query($qryInsert);				
					$intNewId = imw_insert_id();
					//$intNewId = 2;
					$curl_site_url = $realMedCareElgblURI;
					
					$certUse = $this->getCertNameConstent($groupID,$patCompId);
					
					$arrCertInfo = array();
					$strCertInfoCertDir = $strCertInfoCertInUse = "";
					$strCertInfoCertDir = $this->arrCertDirInfo[$certUse]["certDir"];
					$strCertInfoCertInUse = $this->arrCertDirInfo[$certUse]["vsCertInUse"];
					$certSSLKeyPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vskey.pem";
					$certPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsca.pem";
					$certClientPath = $GLOBALS['fileroot']."/".$strCertInfoCertDir."/vsclient.pem";
					$vsPassword = constant($strCertInfoCertInUse);
					//echo $certSSLKeyPath."------".$certPath."------".$certClientPath;
					//die();
					$headers = array("User-Agent: imwemr-292944",
									"Host: seapitest.visionshareinc.com:443",
									"X-SEAPI-Version: 1",
									"Content-Type: application/EDI-X12",	
									"Content-Length: ".$ediLen.""				
								); 		
					//$curl_site_url = "https://seapitest.visionshareinc.com/portal/seapi/services/RealtimeMedicareEligibility/11000";								
					$cur = curl_init();
					curl_setopt($cur, CURLOPT_URL, $curl_site_url);
					curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($cur, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($cur, CURLOPT_HEADER, TRUE);
					curl_setopt($cur, CURLOPT_POST, TRUE);
					curl_setopt($cur, CURLOPT_POSTFIELDS, $ediToSend);
					curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, TRUE);
					curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, 2); 
					curl_setopt($cur, CURLOPT_SSLCERTTYPE, 'PEM');
					curl_setopt($cur, CURLOPT_SSLKEYTYPE, 'PEM'); 
					curl_setopt($cur, CURLOPT_VERBOSE, '0');
					
					curl_setopt($cur, CURLOPT_SSLKEY, $certSSLKeyPath);
					curl_setopt($cur, CURLOPT_CAINFO, $certPath);
					curl_setopt($cur, CURLOPT_SSLCERT, $certClientPath); 
					curl_setopt($cur, CURLOPT_SSLCERTPASSWD, $vsPassword);
					
					curl_setopt($cur, CURLOPT_TIMEOUT, 60);
					
					$EDI271Response = curl_exec($cur);
					$EDI271Response = strtoupper($EDI271Response);
					#For Testing
					//$EDI271Response = file_get_contents(dirname(__FILE__)."/2012-07-02-01-23-52_7132228_271_5010.txt");//
					//pre($EDI271Response,1);
					if (curl_errno($cur)){
						$errorCURL = curl_error($cur);
						curl_close($cur);					
						if (curl_errno($cur) == 28){
							return $this->strCustMsg;     	
						}
						else{
							return "Please contact your Administrator to resolve Curl Error: " . $errorCURL; 
						}
						die;
					}
					$transectionError = "";
					//pre(curl_getinfo($cur));
					$arrCurlInfo = array();
					//pre($arrCurlInfo,1);
					$arrCurlInfo = curl_getinfo($cur);				
					//$arrCurlInfo["http_code"] = 200;
					curl_close($cur);
					if($arrCurlInfo["http_code"] != 200){
						$arrEDI271Response = $arrVisionShareResponseXML = array();					
						$arrEDI271Response = explode("\n", $EDI271Response);													
						//pre($arrEDI271Response);
						$blXMLerror = false;
						$strReplace = "";
						$strXMLerror = "";
						$strXMLValidation = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";					
						foreach($arrEDI271Response as $key => $val){					
							if(($blXMLerror == false) && (trim($val) == $strXMLValidation)){							
								$blXMLerror = true;
								break;
							}						
						}
						
						if($blXMLerror == true){						
							foreach($arrEDI271Response as $key => $val){
								if(trim($val) == $strXMLValidation){
									break;
								}
								else{
									unset($arrEDI271Response[$key]);
								}
							}
							$strMsg = "";
							$arrError = array();
							$strXMLerror = implode("\n", $arrEDI271Response);
							$arrVisionShareResponseXML = $this->XMLToArray($strXMLerror);
							//return pre($arrVisionShareResponseXML);
							foreach($arrVisionShareResponseXML as $key => $val){
								if(is_array($val) == true){
									if(($val["tag"] == "message") && ($strMsg == "") && ($val["type"] == "complete")){
										$strMsg = trim($val["value"]);
									}
									elseif(($val["tag"] == "detail") && ($val["type"] == "complete") && (is_array($val["attributes"]) == true)){
										$arrError[] = array("key" => $val["attributes"]["key"], "val" => $val["attributes"]["value"]);	
									}
								}
							}
							$returnXMLError = "Error: ".$strMsg." \n";
							foreach($arrError as $key => $val){
								$returnXMLError .= "Expected Value:".strtoupper($val["key"]).". Actual Value:".strtoupper($val["val"])." \n";
							}
							$transectionError .= $returnXMLError;
							//echo $returnXMLError;
						}
					}
					elseif($arrCurlInfo["http_code"] == 200){
						$arrEDI271Response = $arrVisionShareResponseXML = array();					
						$arrEDI271Response = explode("\n", $EDI271Response);													
						//pre($arrEDI271Response,1);
						$blACKExits = $bl997Exits = $bl999Exits = $bl271Exits = false;
						$strACK = $returnTA1Error = $strEDI997 = $strEDI999 = $strEDI271 = "";
						foreach($arrEDI271Response as $key => $val){					
							if((strpos($val, "TA1") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl271Exits == false)){
								$strACK = trim(strtoupper($val));
								$blACKExits = true;
							}
							elseif((strpos($val, "ST*997*") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl271Exits == false)){
								$strEDI997 = trim(strtoupper($val));
								$bl997Exits = true;
							}
							elseif((strpos($val, "ST*999*") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl999Exits == false) && ($bl271Exits == false)){
								$strEDI999 = trim(strtoupper($val));
								$bl999Exits = true;
							}
							elseif((strpos($val, "ST*271*") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl271Exits == false)){
								$strEDI271 = trim(strtoupper($val));
								$bl271Exits = true;
							}
						}
						//pre(var_dump($blACKExits));
						if($blACKExits == true){
							$strTA = $strTAReasonCode = "";
							$arrACK = $arrTA = array();
							$arrACK = explode("~",$strACK);
							//pre($arrACK);
							$strTA = $arrACK[1];
							$arrTA = explode("*",$strTA);
							//pre($arrTA);
							if(trim($arrTA[4]) == "R"){
								$strTAReasonCode = $arrTA[5];
								$errorMsg = "";
								$errorMsg = $this->getVisionShareError("vision_share", "TA", $strTAReasonCode);
								$returnTA1Error = "Error INTERCHANGE ACKNOWLEDGMENT: \n ".$errorMsg." \n ";
								$transectionError .= $returnTA1Error;
							}
						}
						elseif($bl997Exits == true){
							$str997 = $strTAReasonCode = "";
							$arr997 = $arrTA = $dataArr = $transDataArr = array();
							//echo $strEDI997;						
							$dataArr = preg_split('/(AK2\*270\*)|(AK5\*)|(AK9\*)/',$strEDI997);
							$transDataArr = preg_split('/~/',$dataArr[1]);						
							if(strtoupper($dataArr[2][0]) == 'A' || strtoupper($dataArr[3][0]) == 'A'){
								echo "270 is Accepted";
							}
							elseif(strtoupper($dataArr[2][0]) == 'R' || strtoupper($dataArr[3][0]) == 'R'){
								$errorDetailArr = $errorString = array();
								$errorDetailArr = explode('AK',$strEDI997);
								//pre($errorDetailArr);
								foreach($errorDetailArr as $val){
									$firstVal = substr($val,0,1);		
									if(($firstVal > 2) && ($firstVal < 5) && (in_array('AK'.$val, $errorString) == false)){
										$errorString[] = 'AK'.$val;
									}
								}
								//pre($errorString);
								if($errorString[0] != ""){
									foreach($errorString as $key => $val){
										$arrVal = array();
										$arrVal = explode("*", $val);
										if(trim($arrVal[0]) == "AK3"){
											$intAk3ReasonCode = (int)trim($arrVal[4]);										
											$errorMsg = $this->getVisionShareError("vision_share", "AK3", $intAk3ReasonCode);
											$return997Error = "Error in Segment : ".trim($arrVal[1])." \n";
											$return997Error .= "Loop : ".trim($arrVal[3])." \n";
											$return997Error .= "Due to : ".trim($errorMsg)." \n";										
											$transectionError .= $return997Error;
										}
										elseif(trim($arrVal[0]) == "AK4"){
											$intAk3ReasonCode = (int)trim($arrVal[3]);										
											$errorMsg = $this->getVisionShareError("vision_share", "AK4", $intAk3ReasonCode);
											$return997Error = "Position in Segment : ".trim($arrVal[1])." \n";
											$return997Error .= "Data Elemnt Ref Number : ".trim($arrVal[2])." \n";
											$return997Error .= "Data Element Syntax Error : ".trim($errorMsg)." \n";										
											$transectionError .= $return997Error;
										}
									}
								}
							}
						}
						elseif($bl999Exits == true){
							$str999 = $strTAReasonCode = "";
							$arr999 = $arrTA = $dataArr = $transDataArr = array();
							//echo $strEDI999;						
							$dataArr = preg_split('/(AK2\*270\*)|(AK5\*)|(AK9\*)/',$strEDI999);
							//pre($dataArr,1);
							$transDataArr = preg_split('/~/',$dataArr[1]);						
							if(strtoupper($dataArr[2][0]) == 'A' || strtoupper($dataArr[3][0]) == 'A'){
								echo "270 is Accepted";
							}
							elseif(strtoupper($dataArr[2][0]) == 'R' || strtoupper($dataArr[3][0]) == 'R'){
								$errorDetailArr = $errorString = array();
								$errorDetailArr = explode('IK',$strEDI999);
								//pre($errorDetailArr,1);
								foreach($errorDetailArr as $val){
									$firstVal = substr($val,0,1);		
									if(($firstVal > 2) && ($firstVal <= 5) && (in_array('IK'.$val, $errorString) == false)){
										$errorString[] = 'IK'.$val;
									}
								}
								//pre($errorString,1);
								if($errorString[0] != ""){
									foreach($errorString as $key => $val){
										$arrVal = array();
										$val = str_replace("~","",$val);
										$arrVal = explode("*", $val);
										if(trim($arrVal[0]) == "IK3"){
											$intAk3ReasonCode = trim($arrVal[4]);
											$errorMsg = $this->getVisionShareError("vision_share", "IK3", $intAk3ReasonCode);
											$return999Error = "Error in Segment : ".trim($arrVal[1])." \n";
											$return999Error .= "Position in Segment : ".trim($arrVal[2])." \n";
											$return999Error .= "Loop : ".trim($arrVal[3])." \n";
											$return999Error .= "Due to : ".trim($errorMsg)." \n";										
											$transectionError .= $return999Error;
										}
										elseif(trim($arrVal[0]) == "IK4"){
											$intAk3ReasonCode = trim($arrVal[3]);										
											$errorMsg = $this->getVisionShareError("vision_share", "IK4", $intAk3ReasonCode);
											$return999Error = "Position in Segment : ".trim($arrVal[1])." \n";
											$return999Error .= "Data Elemnt Ref Number : ".trim($arrVal[2])." \n";
											$return999Error .= "Data Element Syntax Error : ".trim($errorMsg)." \n";										
											$transectionError .= $return999Error;
										}
										elseif(trim($arrVal[0]) == "IK5"){
											$strIk5ErrorCode = "";
											$strIk5ErrorCode = trim($arrVal[1]);
											$errorMsg = $this->getVisionShareError("vision_share", "IK5", $strIk5ErrorCode);
											$return999Error = "Status : ".trim($errorMsg)." \n";
											$strIk3ReasonCode = "";
											$strIk3ReasonCode = trim($arrVal[2]);										
											$errorMsg = $this->getVisionShareError("vision_share", "IK5", $strIk3ReasonCode);
											$return999Error .= "Implementation Transaction Set Syntax Error : ".trim($errorMsg)." \n";										
											$transectionError .= $return999Error;
										}
									}
								}								
							}
						}
						elseif($bl271Exits == true){
							$arrEDI271 = array();
							$arrEDI271 = explode("~",$strEDI271);
							//pre($arrEDI271);
							$return271Error = $strHL3RQLName = $strHL3RQFName = $strHL3RQMName = $strHL3RQPolicyNo = $strHL3RQAdd1 = $strHL3RQAdd2 = $strHL3RQCity = $strHL3RQState = $strHL3RQZip = $strHL3RQPatDOB = "";
							$blHL1RQSt = $blHL2RQSt = $blHL3RQSt = $blHL4RQSt = false;
							$blPatName = $blPatSAdd = $blPatCSZAdd = $blPatDOB = false;
							
							$blDepName = $blDepSAdd = $blPatCSZAdd = $blDepDOB = $blDepRel = false;
							$arrHL4RQName = $arrHL4RQAdd = $arrHL4RQCSZ = $arrHL4RQDOB = $arrHL4RQRel = array();
							$strHL4RQLName = $strHL4RQFName = $strHL4RQMName = $strHL4RQPolicyNo = $strHL4RQAdd1 = $strHL4RQAdd2 = $strHL4RQCity = $strHL4RQState = $strHL4RQZip = $strHL4RQPatDOB = $strDepRel = "";					
							
							$strHL1RQ = $strHL2RQ = $strHL3RQ = $strHL4RQ = "";
							$strHL1ReasonCode = $strHL2ReasonCode = $strHL3ReasonCode = $strHL4ReasonCode = "";
							$arrHL3RQName = $arrHL1RQ = $arrHL2RQ = $arrHL3RQ = $arrHL4RQ = $arrHL3RQAdd = $arrHL3RQCSZ = $arrHL3RQDOB = array();
							
							$blHL1AAA = $blHL2AAA = $blHL3AAA = $blHL4AAA = false;
							
							foreach($arrEDI271 as $key => $val){
								if((strpos($val, "HL*1*") !== false) && ($blHL1RQSt == false)){								
									$blHL1RQSt = true;
								}
								elseif((strpos($val, "HL*2*") !== false) && ($blHL2RQSt == false)){								
									$blHL1RQSt = false;
									$blHL2RQSt = true;								
								}
								elseif((strpos($val, "HL*3*") !== false) && ($blHL3RQSt == false)){								
									$blHL1RQSt = false;
									$blHL2RQSt = false;
									$blHL3RQSt = true;
								}
								elseif((strpos($val, "LS*2120") !== false) && ($blHL3RQSt == true)){								
									$blHL1RQSt = false;
									$blHL2RQSt = false;
									$blHL3RQSt = false;
								}
								elseif((strpos($val, "HL*4*") !== false) && ($blHL4RQSt == false)){								
									$blHL1RQSt = false;
									$blHL2RQSt = false;
									$blHL3RQSt = false;
									$blHL4RQSt = true;
								}
								elseif((strpos($val, "LS*2120") !== false) && ($blHL4RQSt == true)){								
									$blHL1RQSt = false;
									$blHL2RQSt = false;
									$blHL3RQSt = false;
									$blHL4RQSt = false;
								}
								if((strpos($val, "AAA") !== false) && ($blHL1RQSt == true) && ($blHL1AAA == false)){
									$blHL1AAA = true;
									$arrHL1RQ = array();
									$strHL1RQ = trim($val);
									$arrHL1RQ = explode("*", $strHL1RQ);
									$strHL1ReasonCode = trim($arrHL1RQ[3]);
									$errorMsg = "";
									$errorMsg = $this->getVisionShareError("vision_share_271", "HL1", $strHL1ReasonCode);
									$return271Error .= " - Error : INFORMATION SOURCE LEVEL \n";
									$return271Error .= " - Loop : 2000A \n";
									$return271Error .= " - Due to : ".trim($errorMsg)." \n";
									$FollowUPAction  = "";
									$FollowUPAction = $this->getVisionShareError("vision_share_271", "HL1-Follow-upAction", trim($arrHL1RQ[4]));
									$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";
								}
								elseif((strpos($val, "AAA") !== false) && ($blHL2RQSt == true) && ($blHL2AAA == false)){
									$blHL2AAA = true;
									$arrHL2RQ = array();
									$strHL2RQ = trim($val);
									$arrHL2RQ = explode("*", $strHL2RQ);
									$strHL2ReasonCode = trim($arrHL2RQ[3]);
									$errorMsg = "";
									$errorMsg = $this->getVisionShareError("vision_share_271", "HL2", $strHL2ReasonCode);
									$return271Error .= " - Error : INFORMATION RECEIVER LEVEL \n";
									$return271Error .= " - Loop : 2100B \n";
									$return271Error .= " - Due to : ".trim($errorMsg)." \n";
									$FollowUPAction  = "";
									$FollowUPAction = $this->getVisionShareError("vision_share_271", "HL2-Follow-upAction", trim($arrHL2RQ[4]));
									$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";								
								}
								elseif((strpos($val, "AAA") !== false) && ($blHL3RQSt == true) && ($blHL3AAA == false)){
									$blHL3AAA = true;
									$arrHL3RQ = array();
									$strHL3RQ = trim($val);
									$arrHL3RQ = explode("*", $strHL3RQ);																
									$strHL3ReasonCode = trim($arrHL3RQ[3]);
									$errorMsg = "";
									$errorMsg = $this->getVisionShareError("vision_share_271", "HL3", $strHL3ReasonCode);
									$return271Error .= " - Error : SUBSCRIBER NAME LEVEL \n";
									$return271Error .= " - Loop : 2100C \n";
									$return271Error .= " - Due to : ".trim($errorMsg)." \n";
									$FollowUPAction  = "";
									$FollowUPAction = $this->getVisionShareError("vision_share_271", "HL3-Follow-upAction", trim($arrHL3RQ[4]));
									$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";								
								}
								elseif((strpos($val, "AAA") !== false) && ($blHL4RQSt == true) && ($blHL4AAA == false)){
									$blHL4AAA = true;									
									$strHL4RQ = trim($val);
									$arrHL4RQ = explode("*", $strHL4RQ);
									//pre($arrHL4RQ,1);																
									$strHL4ReasonCode = trim($arrHL4RQ[3]);
									$errorMsg = "";
									$errorMsg = $this->getVisionShareError("vision_share_271", "HL4", $strHL4ReasonCode);
									$errorMsg = (empty($errorMsg) == false ? $errorMsg : $this->error);
									$return271Error .= " - Error : DEPENDENT NAME LEVEL \n";
									$return271Error .= " - Loop : 2100D \n";
									$return271Error .= " - Due to : ".trim($errorMsg)." \n";
									$FollowUPAction  = "";
									$FollowUPAction = $this->getVisionShareError("vision_share_271", "HL4-Follow-upAction", trim($arrHL4RQ[4]));
									$FollowUPAction = (empty($FollowUPAction) == false ? $FollowUPAction : $this->error);
									$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";
								}
								elseif((strpos($val, "MSG") !== false) && (($blHL1AAA == true) || ($blHL2AAA == true) || ($blHL3AAA == true) || ($blHL4AAA == true))){
									$strAAAMSG = "";
									$arrAAAMSG = array();
									$strAAAMSG = trim($val);
									$arrAAAMSG = explode("*", $strAAAMSG);
									//pre($arrHL3MSG,1);																
									$strHL3Message = trim($arrAAAMSG[1]);
									$return271Error .= " - Message from Information Source: ";
									$return271Error .= trim($strHL3Message)." \n";
								}
								if((strpos($val, "NM1") !== false) && ($blHL3RQSt == true) && ($blPatName == false)){
									$arrHL3RQName = array();
									$strHL3RQNameSeg = "";
									$strHL3RQNameSeg = trim($val);
									$arrHL3RQName = explode("*", $strHL3RQNameSeg);																
									$strHL3RQLName = ucfirst(strtolower(trim($arrHL3RQName[3])));
									$strHL3RQFName = ucfirst(strtolower(trim($arrHL3RQName[4])));
									$strHL3RQMName = ucfirst(strtolower(trim($arrHL3RQName[5])));
									if(trim($arrHL3RQName[9]) != ""){
										$strHL3RQPolicyNo = trim($arrHL3RQName[9]);
									}
									$blPatName = true;
								}
								elseif((strpos($val, "N3") !== false) && ($blHL3RQSt == true) && ($blPatSAdd == false)){							
									$arrHL3RQAdd = array();
									$strHL3RQAddSeg = "";
									$strHL3RQAddSeg = trim($val);
									$arrHL3RQAdd = explode("*", $strHL3RQAddSeg);																
									$strHL3RQAdd1 = ucwords(strtolower(trim($arrHL3RQAdd[1])));
									$strHL3RQAdd2 = ucwords(strtolower(trim($arrHL3RQAdd[2])));
									$blPatSAdd = true;
								}
								elseif((strpos($val, "N4") !== false) && ($blHL3RQSt == true) && ($blPatCSZAdd == false)){								
									$arrHL3RQCSZ = array();
									$strHL3RQCSZSeg = "";
									$strHL3RQCSZSeg = trim($val);
									$arrHL3RQCSZ = explode("*", $strHL3RQCSZSeg);																
									$strHL3RQCity = ucwords(strtolower(trim($arrHL3RQCSZ[1])));
									$strHL3RQState = trim($arrHL3RQCSZ[2]);
									$strHL3RQZip = trim($arrHL3RQCSZ[3]);
									$blPatCSZAdd = true;
								}
								elseif((strpos($val, "DMG") !== false) && ($blHL3RQSt == true) && ($blPatDOB == false)){								
									$arrHL3RQDOB = array();
									$strHL3RQDOBSeg = "";
									$strHL3RQDOBSeg = trim($val);
									$arrHL3RQDOB = explode("*", $strHL3RQDOBSeg);																
									if(trim($arrHL3RQDOB[1]) == "D8"){
										$strHL3RQDOBSeg = trim($arrHL3RQDOB[2]);
										$intDOBYear = $intDOBMon = $intDOBDay = 0;
										$intDOBYear = (int)substr($strHL3RQDOBSeg,0,4);
										$intDOBMon = (int)substr($strHL3RQDOBSeg,4,2);
										$intDOBDay = (int)substr($strHL3RQDOBSeg,6,2);
										if(($intDOBYear > 0) && ($intDOBMon > 0) && ($intDOBDay > 0) && (checkdate($intDOBMon, $intDOBDay, $intDOBYear) == true)){
											$strHL3RQPatDOB = $intDOBYear."-".$intDOBMon."-".$intDOBDay;
										}
										else{
											$strHL3RQPatDOB = "";
										}
									}
									$blPatDOB = true;
								}
								//Dependent
								if((strpos($val, "NM1") !== false) && ($blHL4RQSt == true) && ($blDepName == false)){																										
									$strHL4RQNameSeg = "";
									$strHL4RQNameSeg = trim($val);
									$arrHL4RQName = explode("*", $strHL4RQNameSeg);
									
									$strHL4RQLName = ucfirst(strtolower(trim($arrHL4RQName[3])));
									$strHL4RQFName = ucfirst(strtolower(trim($arrHL4RQName[4])));
									$strHL4RQMName = ucfirst(strtolower(trim($arrHL4RQName[5])));
									if(trim($arrHL4RQName[9]) != ""){
										$strHL4RQPolicyNo = trim($arrHL4RQName[9]);
									}
									$blDepName = true;
																
								}
								elseif((strpos($val, "N3") !== false) && ($blHL4RQSt == true) && ($blDepSAdd == false)){
									$strHL4RQAddSeg = "";
									$strHL4RQAddSeg = trim($val);
									$arrHL4RQAdd = explode("*", $strHL4RQAddSeg);																
									$strHL4RQAdd1 = ucwords(strtolower(trim($arrHL4RQAdd[1])));
									$strHL4RQAdd2 = ucwords(strtolower(trim($arrHL4RQAdd[2])));
									$blDepSAdd = true;
								}
								elseif((strpos($val, "N4") !== false) && ($blHL4RQSt == true) && ($blPatCSZAdd == false)){
									$strHL4RQCSZSeg = "";
									$strHL4RQCSZSeg = trim($val);
									$arrHL4RQCSZ = explode("*", $strHL4RQCSZSeg);																
									$strHL4RQCity = ucwords(strtolower(trim($arrHL4RQCSZ[1])));
									$strHL4RQState = trim($arrHL4RQCSZ[2]);
									$strHL4RQZip = trim($arrHL4RQCSZ[3]);
									$blPatCSZAdd = true;
								}
								elseif((strpos($val, "DMG") !== false) && ($blHL4RQSt == true) && ($blDepDOB == false)){
									$strHL4RQDOBSeg = "";
									$strHL4RQDOBSeg = trim($val);
									$arrHL4RQDOB = explode("*", $strHL4RQDOBSeg);																
									if(trim($arrHL4RQDOB[1]) == "D8"){
										$strHL4RQDOBSeg = trim($arrHL4RQDOB[2]);
										$intDOBYear = $intDOBMon = $intDOBDay = 0;
										$intDOBYear = (int)substr($strHL4RQDOBSeg,0,4);
										$intDOBMon = (int)substr($strHL4RQDOBSeg,4,2);
										$intDOBDay = (int)substr($strHL4RQDOBSeg,6,2);
										if(($intDOBYear > 0) && ($intDOBMon > 0) && ($intDOBDay > 0) && (checkdate($intDOBMon, $intDOBDay, $intDOBYear) == true)){
											$strHL4RQPatDOB = $intDOBYear."-".$intDOBMon."-".$intDOBDay;
										}
										else{
											$strHL4RQPatDOB = "";
										}
										$blDepDOB = true;
									}
								}
								//Dependent Relatinship
								if((strpos($val, "INS") !== false) && ($blHL4RQSt == true) && ($blDepRel == false)){																										
									$strHL4RQRelSeg = "";
									$strHL4RQRelSeg = trim($val);
									$arrHL4RQRel = explode("*", $strHL4RQRelSeg);
									
									switch ($arrHL4RQRel[2]):
										case "01":
											$strDepRel = "Spouse";
										break;
										case "19":
											$strDepRel = "Child";
										break;
										case "21":
											$strDepRel = "Unknown";
										break;
										case "34":
											$strDepRel = "Other Adult";
										break;
									endswitch;
									
									$blDepRel = true;																
								}
								//						
							}
						}
					}
					if(empty($return271Error) == false){					
						$transectionError .= $return271Error;
						$errorDetailArr = $errorString = array();
					}				
					$patientDirVs271Dir = "vs_271_response/";
					if(!is_dir($upLoadPath.$patientDir.$patientDirVs271Dir)){
						//Create patient VS 271 responce directory
						mkdir($upLoadPath.$patientDir.$patientDirVs271Dir, 0700, true);
					}
					$fileNameEDI271 = date("Y-m-d-H-i-s_").$patId."_271_5010";
					$pathToWrite271EDI = $upLoadPath.$patientDir.$patientDirVs271Dir.$fileNameEDI271.".txt";
					file_put_contents($pathToWrite271EDI,$EDI271Response);
					
					$sqlEDIPathResp = "";
					$sqlEDIPathResp = $patientDir.$patientDirVs271Dir.$fileNameEDI271.".txt";
					
					if(($intNewId > 0) && (file_exists($pathToWrite271EDI) == true)){	
						$strXMLEB = $strEBResponce = $strEBInsuranceTypeCode = "";
						//$arrEBResponce = $arrEBInsuranceTypeCode = array();
						$strXMLEB .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
						$strXMLEB .= "<DTPEBLOOP>";
						$EBDetailArr = $arrLastEB = $DTPDetailArr = $EBDTPDetailArr = array();
						//$EBDetailArr = explode('~EB',$EDI271Response);
						$DTPDetailArr = explode('~DTP',$EDI271Response);					
						//pre($DTPDetailArr,1);
						$EBDTPDetailArr = explode('~EB',$EDI271Response);
						//pre($EBDTPDetailArr,1);
						unset($EBDTPDetailArr[0]);
						//pre($EBDTPDetailArr,1);
						$EBDTPDetailArr = array_values($EBDTPDetailArr);
						foreach($EBDTPDetailArr as $key => $val){
							$EBDTPDetailArr[$key] = "EB".$val;
						}
						//pre($EBDTPDetailArr,1);
						$EBDTPDetailArrRefined = array();
						foreach($EBDTPDetailArr as $key => $val){
							$arrVal = $arrTemp = $arrMSG = array();
							$arrVal = explode("~", trim($val));
							//pre($arrVal,1);
							foreach($arrVal as $intValkey => $strValVal){
								if((strpos($strValVal, "EB") !== false) || (strpos($strValVal, "DTP") !== false) || (strpos($strValVal, "MSG") !== false)){
									if(strpos($strValVal, "DTP") !== false){
										$arrTemp["DTP"]	= $strValVal;
									}
									elseif(strpos($strValVal, "EB") !== false){
										$arrTemp["EB"]	= $strValVal;
									}
									elseif(strpos($strValVal, "MSG") !== false){
										$arr = array();
										$arr = explode("*", $strValVal);
										$arrMSG[] = $arr[1];
									}										
								}
								else{
									unset($arrVal[$intValkey]);
								}
							}
							$arrTemp["MSG"]	= implode(",", $arrMSG);
							//pre($arrVal,1);
							$EBDTPDetailArrRefined[] = $arrTemp;
						}
						//pre($EBDTPDetailArrRefined,1);
	
						//pre($DTPDetailArr,1);
						unset($DTPDetailArr[0]);
						//pre($DTPDetailArr,1);
						$DTPDetailArr = array_values($DTPDetailArr);
						foreach($DTPDetailArr as $key => $val){
							$DTPDetailArr[$key] = "DTP".$val;
						}					
						//pre($DTPDetailArr,1);
						foreach($DTPDetailArr as $key => $val){
							$arrVal = $arrTemp = $arrMSG = array();
							$arrVal = explode("~", trim($val));
							//pre($arrVal,1);
							foreach($arrVal as $intValkey => $strValVal){
								//pre($arrVal);
								$nxtStrValVal = "";
								//echo $arrVal[$intValkey];
								//echo "<br>";
								$nxtStrValVal = $arrVal[$intValkey + 1];							
								if((strpos($strValVal, "EB") !== false)){
									$arrMsgTemp = explode("*", $nxtStrValVal);
									if((empty($arrMsgTemp[1]) == false) && ($arrMsgTemp[0] == "MSG")){
										//pre($arrMsgTemp,1);
										$arrMsgAll = array();
										$intMsg = 0;
										$intStMsg =  $intValkey + 1;
										$intEndMsg = $intStMsg + 10;
										for($intMsg = $intStMsg; $intMsg <= $intEndMsg; $intMsg++){
											if((strpos($arrVal[$intMsg], "MSG") !== false)){
												$arrMsgTemp1 = explode("*", $arrVal[$intMsg]);
												$arrMsgAll[] = $arrMsgTemp1[1];
											}
											elseif((strpos($arrVal[$intMsg], "EB") !== false)){
												$intMsg = $intEndMsg + 100;
											}
										}
										$arrTemp[] = array(array("EB" => $strValVal, "MSG" => implode(",", $arrMsgAll)));
									}
									else{
										$arrTemp[] = $strValVal;
									}
								}
								elseif(strpos($strValVal, "EB") !== false){
									$arrTemp[] = $strValVal;
								}
							}
							//pre($arrTemp);
							$DTPDetailArr[$key] = array("DTP" => $arrVal[0], "EB" => $arrTemp);
						}
						//pre($EBDTPDetailArrRefined,1);
						//pre($DTPDetailArr,1);
						//pre($EBDetailArr);										
						//$EBDetailArr = array_values($EBDetailArr);
						//pre($EBDetailArr);					
						//die;
						foreach($DTPDetailArr as $key => $val){
							if(is_array($val) == true){
								foreach($val as $valkey => $valval){
									if($valkey == "DTP"){
										$arrDTPVal = array();
										$strDTPVal = "";
										$strXMLEB .= "<DTPLOOPINFO>";
										$strXMLEB .= "<DTPLOOP_".$key.">";
										$strDTPVal = trim($valval);
										$arrDTPVal = explode("*", $strDTPVal);
										unset($arrDTPVal[0]);
										$arrDTPVal = array_values($arrDTPVal);								
										foreach($arrDTPVal as $intDTPValKey => $strDTPValVal){
											$strDTPValVal = trim($strDTPValVal);
											$oriVal = "";
											switch ($intDTPValKey):
												case 0:
													$strXMLEB .= "<DateTimeQualifier>";									
													$oriVal = $this->getVisionShareError("vision_share_271", "DTP", $strDTPValVal);
														$strXMLEB .= "<DateTimeQualifierID>";
														$strXMLEB .= $strDTPValVal;
														$strXMLEB .= "</DateTimeQualifierID>";
														
														$strXMLEB .= "<DateTimeQualifierIDVal>";
														$strXMLEB .= (is_string($oriVal) ? $oriVal : "");
														$strXMLEB .= "</DateTimeQualifierIDVal>";
													$strXMLEB .= "</DateTimeQualifier>";
												break;
												case 1:
													$strXMLEB .= "<DateTimePeriodFormatQualifier>";
													$strXMLEB .= $strDTPValVal;
													$strXMLEB .= "</DateTimePeriodFormatQualifier>";
												break;
												case 2:										
													$strDTPDate = "";
													$arrDTPDate = array();
													$strDTPDate = $strDTPValVal;
													$arrDTPDate = explode("-", $strDTPDate);										
													$strXMLEB .= "<DateTimePeriod>";
														$strXMLEB .= "<DateTimePeriodDate1>";
														$strXMLEB .= trim($arrDTPDate[0]);
														$strXMLEB .= "</DateTimePeriodDate1>";
														
														$strXMLEB .= "<DateTimePeriodDate2>";
														$strXMLEB .= trim($arrDTPDate[1]);
														$strXMLEB .= "</DateTimePeriodDate2>";
													$strXMLEB .= "</DateTimePeriod>";
												break;
											endswitch;
										}
										$intCountDTPVal = 0;
										$intCountDTPVal = count($arrDTPVal);
										for($a = $intCountDTPVal; $a <= 2; $a++){
											$a = (int)$a;
											switch ($a):
												case 0:
														$strXMLEB .= "<DateTimeQualifier>
																		<DateTimeQualifierID></DateTimeQualifierID>
																		<DateTimeQualifierIDVal></DateTimeQualifierIDVal>
																	</DateTimeQualifier>";
													break;
													case 1:
														$strXMLEB .= "<DateTimePeriodFormatQualifier></DateTimePeriodFormatQualifier>";
													break;
													case 2:
														$strXMLEB .= "<DateTimePeriod>
																		<DateTimePeriodDate1></DateTimePeriodDate1>
																		<DateTimePeriodDate2></DateTimePeriodDate2>
																	</DateTimePeriod>";
													break;
												endswitch;	
										}
										$strXMLEB .= "</DTPLOOP_".$key.">";
										$strXMLEB .= "</DTPLOOPINFO>";
									}
									elseif($valkey == "EB"){
										$strXMLEB .= "<EBLOOPINFO>";
										//$strXMLEB .= "<EBLOOP_".$key.">";
										foreach($valval as $intKeyValVal => $strValValVal){
											//pre($strValValVal,1);													
											$strEBMsg = $strTemp = $strEBDTP = "";
											if(is_array($strValValVal) == true){						
												$strTemp = $strValValVal["EB"]["EB"];														
												$strEBMsg = $strValValVal["EB"]["MSG"];
												$strEBDTP = $strValValVal["EB_DTP"];
												unset($strValValVal);
												$strValValVal = $strTemp;
											}
											$strEBResponceTemp = "";
											$blEBResponceTemp = false;
											$strXMLEB .= "<EBLOOP_".$key."_".$intKeyValVal.">";
											
											##MSG Start
											$strXMLEB .= "<MSG>";
											if(empty($strEBMsg) == false){
												$strXMLEB .= "<![CDATA[".$strEBMsg."]]>";
											}
											$strXMLEB .= "</MSG>";
											##MSG End
											
											##EBDTP start
											$strXMLEB .= "<EBDTP>";
											$arrEBDTPVal = array();
											$arrEBDTPVal = explode("*", trim($strEBDTP));
											unset($arrEBDTPVal[0]);
											$arrEBDTPVal = array_values($arrEBDTPVal);
											//pre($arrEBDTPVal,1);		
											foreach($arrEBDTPVal as $intEBDTPValKey => $strEBDTPVal){
												$strEBDTPVal = trim($strEBDTPVal);
												$oriVal = "";
												switch ($intEBDTPValKey):
													case 0:
														$strXMLEB .= "<DateTimeQualifier>";									
														$oriVal = $this->getVisionShareError("vision_share_271", "DTP", $strEBDTPVal);
															$strXMLEB .= "<DateTimeQualifierID>";
															$strXMLEB .= $strEBDTPVal;
															$strXMLEB .= "</DateTimeQualifierID>";
															
															$strXMLEB .= "<DateTimeQualifierIDVal>";
															$strXMLEB .= ((empty($oriVal) == false) ? $oriVal : "");
															$strXMLEB .= "</DateTimeQualifierIDVal>";
														$strXMLEB .= "</DateTimeQualifier>";
													break;
													case 1:
														$strXMLEB .= "<DateTimePeriodFormatQualifier>";
														$strXMLEB .= $strEBDTPVal;
														$strXMLEB .= "</DateTimePeriodFormatQualifier>";
													break;
													case 2:										
														$strDTPDate = "";
														$arrDTPDate = array();
														$strDTPDate = $strEBDTPVal;
														$arrDTPDate = explode("-", $strDTPDate);										
														$strXMLEB .= "<DateTimePeriod>";
															$strXMLEB .= "<DateTimePeriodDate1>";
															$strXMLEB .= trim($arrDTPDate[0]);
															$strXMLEB .= "</DateTimePeriodDate1>";
															
															$strXMLEB .= "<DateTimePeriodDate2>";
															$strXMLEB .= trim($arrDTPDate[1]);
															$strXMLEB .= "</DateTimePeriodDate2>";
														$strXMLEB .= "</DateTimePeriod>";
													break;
												endswitch;
											}
											$intCountDTPVal = 0;
											$intCountDTPVal = count($arrEBDTPVal);
											for($a = $intCountDTPVal; $a <= 2; $a++){
												$a = (int)$a;
												switch ($a):
													case 0:
															$strXMLEB .= "<DateTimeQualifier>
																			<DateTimeQualifierID></DateTimeQualifierID>
																			<DateTimeQualifierIDVal></DateTimeQualifierIDVal>
																		</DateTimeQualifier>";
														break;
														case 1:
															$strXMLEB .= "<DateTimePeriodFormatQualifier></DateTimePeriodFormatQualifier>";
														break;
														case 2:
															$strXMLEB .= "<DateTimePeriod>
																			<DateTimePeriodDate1></DateTimePeriodDate1>
																			<DateTimePeriodDate2></DateTimePeriodDate2>
																		</DateTimePeriod>";
														break;
													endswitch;	
											}
											$strXMLEB .= "</EBDTP>";
											##EBDTP End
																				
											$arrEBVal = array();
											$strEBVal = trim($strValValVal);
											$arrEBVal = explode("*", trim($strEBVal));	
											unset($arrEBVal[0]);
											$arrEBVal = array_values($arrEBVal);
											//pre($arrEBVal);
											//die;	
											///////////////////////////////////////////
											foreach($arrEBVal as $intEBValKey => $strEBValVal){
												if(empty($strValueCoIns) == true){// Co-Insurance
													//pre($arrEBVal,1);	EB*A*IND*A7*OT*NJ DIRECT*27**.5****N
													if((trim(strtoupper($arrEBVal[0])) == "A") && (trim(strtoupper($arrEBVal[1])) == "IND") && ((int)$arrEBVal[2] == 98) && ((float)$arrEBVal[7] > 0)){
														$strValueCoIns = trim($arrEBVal[3])."-".$arrEBVal[2]."-"."IND"."-".$arrEBVal[4]."-".$arrEBVal[6]."-".$arrEBVal[5]."-".$arrEBVal[7]."-".$arrEBVal[9]."-".$arrEBVal[8]."-".$arrEBVal[10]."-".$arrEBVal[11];
													}
												}
												
												if(empty($strValueCP) == true){// Co-Payment
													//pre($arrEBVal,1);	
													if((trim(strtoupper($arrEBVal[0])) == "B") && (trim(strtoupper($arrEBVal[1])) == "IND") && ((int)$arrEBVal[2] == 98)){
														$strValueCP = trim($arrEBVal[3])."-".$arrEBVal[2]."-"."IND"."-".$arrEBVal[4]."-".$arrEBVal[6]."-".$arrEBVal[5]."-".$arrEBVal[7]."-".$arrEBVal[9]."-".$arrEBVal[8]."-".$arrEBVal[10]."-".$arrEBVal[11];
														$blCopayExits = true;
													}
													elseif((trim(strtoupper($arrEBVal[0])) == "B") && (empty($arrEBVal[6]) == false)){
														$blCopayExits = true;
													}
												}
												
												if(empty($strValueD) == true){
													//pre($arrEBVal,1);	
													if((trim(strtoupper($arrEBVal[0])) == "C") && (trim(strtoupper($arrEBVal[1])) == "IND") && (trim(strtoupper($arrEBVal[3])) == "MB") && ((int)$arrEBVal[5] == 29)){
														$strValueD = trim($arrEBVal[3])."-".$arrEBVal[2]."-"."IND"."-".$arrEBVal[4]."-".$arrEBVal[6]."-"."29"."-".$arrEBVal[7]."-".$arrEBVal[9]."-".$arrEBVal[8]."-".$arrEBVal[10]."-".$arrEBVal[11];
													}
													elseif((trim(strtoupper($arrEBVal[0])) == "C") && (trim($arrEBVal[1]) == "") && (trim(strtoupper($arrEBVal[3])) == "MB") && ((int)$arrEBVal[5] == 29)){
														$strValueD = trim($arrEBVal[3])."-".$arrEBVal[2]."-".""."-".$arrEBVal[4]."-".$arrEBVal[6]."-"."29"."-".$arrEBVal[7]."-".$arrEBVal[9]."-".$arrEBVal[8]."-".$arrEBVal[10]."-".$arrEBVal[11];
													}
												}
												
												$orival = "";
												$strEBValVal = trim($strEBValVal);
												switch ($intEBValKey):
													case 0:
														$strXMLEB .= "<EligibilityOrBenefitInformation>";																					
														$orival = $this->getVisionShareError("vision_share_271", "EB", $strEBValVal);
														$strEBValVal = (string)$strEBValVal;
														if(
														($strEBValVal == "1") || ($strEBValVal == "2") || ($strEBValVal == "3") || ($strEBValVal == "4") || ($strEBValVal == "5") || 
														($strEBValVal == "6") || ($strEBValVal == "7") || ($strEBValVal == "8")
														){
															if(empty($strEBResponceTemp) == true){
																$strEBResponceTemp = $strEBValVal;
															}
														}
															//$arrEBResponce[] = $strEBValVal; 
															$strXMLEB .= "<EligibilityOrBenefitInformationId>";
															$strXMLEB .= $strEBValVal;
															$strXMLEB .= "</EligibilityOrBenefitInformationId>";
															
															$strXMLEB .= "<EligibilityOrBenefitInformationVal>";
															$strXMLEB .= (is_string($orival) ? $orival : "");
															$strXMLEB .= "</EligibilityOrBenefitInformationVal>";
														$strXMLEB .= "</EligibilityOrBenefitInformation>";
														break;
													case 1:
														$strXMLEB .= "<CoverageLevelCode>";								
														$orival = $this->getVisionShareError("vision_share_271", "CoverageLevelCode", $strEBValVal);
															$strXMLEB .= "<CoverageLevelCodeId>";
															$strXMLEB .= $strEBValVal;
															$strXMLEB .= "</CoverageLevelCodeId>";
															
															$strXMLEB .= "<CoverageLevelCodeVal>";
															$strXMLEB .= (is_string($orival) ? $orival : "");
															$strXMLEB .= "</CoverageLevelCodeVal>";								
														$strXMLEB .= "</CoverageLevelCode>";
														break;
													case 2:
														$strXMLEB .= "<ServiceTypeCode>";
														$orival = $this->getVisionShareError("vision_share_271", "Service_Type_Code", $strEBValVal);
															$strXMLEB .= "<ServiceTypeCodeId>";
															$strXMLEB .= $strEBValVal;
															$strXMLEB .= "</ServiceTypeCodeId>";
															
															$strXMLEB .= "<ServiceTypeCodeVal>";
															$strXMLEB .= (is_string($orival) ? $orival : "");
															$strXMLEB .= "</ServiceTypeCodeVal>";
														$strXMLEB .= "</ServiceTypeCode>";
														break;
													case 3:
														$strXMLEB .= "<InsuranceTypeCode>";
														$orival = $this->getVisionShareError("vision_share_271", "Insurance_Type_Code", $strEBValVal);
														if(((string)$strEBValVal == "MB") && (empty($strEBResponceTemp) == false) && (empty($strEBResponce) == true)){
															$strEBResponce = $strEBResponceTemp;
															$strEBInsuranceTypeCode = $strEBValVal;
														}
															//$arrEBInsuranceTypeCode[] = $strEBValVal;										
															$strXMLEB .= "<InsuranceTypeCodeId>";
															$strXMLEB .= trim($strEBValVal);
															$strXMLEB .= "</InsuranceTypeCodeId>";
															
															$strXMLEB .= "<InsuranceTypeCodeVal>";
															$strXMLEB .= (is_string($orival) ? $orival : "");
															$strXMLEB .= "</InsuranceTypeCodeVal>";
														$strXMLEB .= "</InsuranceTypeCode>";
														break;
													case 4:
														$strXMLEB .= "<PlanCoverageDescription>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</PlanCoverageDescription>";
														break;
													case 5:
														$strXMLEB .= "<TimePeriodQualifier>";
														$orival = $this->getVisionShareError("vision_share_271", "Time_Period_Qualifier", $strEBValVal);										
															$strXMLEB .= "<TimePeriodQualifierId>";
															$strXMLEB .= $strEBValVal;
															$strXMLEB .= "</TimePeriodQualifierId>";
															
															$strXMLEB .= "<TimePeriodQualifierVal>";
															$strXMLEB .= (is_string($orival) ? $orival : "");
															$strXMLEB .= "</TimePeriodQualifierVal>";
														$strXMLEB .= "</TimePeriodQualifier>";
														break;
													case 6:
														$strXMLEB .= "<MonetaryAmount>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</MonetaryAmount>";
														break;
													case 7:
														$strXMLEB .= "<Percent>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</Percent>";
														break;
													case 8:
														$strXMLEB .= "<QuantityQualifier>";
														$orival = $this->getVisionShareError("vision_share_271", "Quantity_Qualifier", $strEBValVal);
															$strXMLEB .= "<QuantityQualifierId>";
															$strXMLEB .= $strEBValVal;
															$strXMLEB .= "</QuantityQualifierId>";
															
															$strXMLEB .= "<QuantityQualifierVal>";
															$strXMLEB .= (is_string($orival) ? $orival : "");
															$strXMLEB .= "</QuantityQualifierVal>";
														$strXMLEB .= "</QuantityQualifier>";
														break;
													case 9:
														$strXMLEB .= "<Quantity>";
														$strXMLEB .= $strEBValVal;
														$strXMLEB .= "</Quantity>";
														break;
													case 10:
														$strXMLEB .= "<ConditionResponseCode>";
														$orival = $this->getVisionShareError("vision_share_271", "Condition_Response_Code", $strEBValVal);
															$strXMLEB .= "<ConditionResponseCodeId>";
															$strXMLEB .= $strEBValVal;
															$strXMLEB .= "</ConditionResponseCodeId>";
															
															$strXMLEB .= "<ConditionResponseCodeVal>";
															$strXMLEB .= (is_string($orival) ? $orival : "");
															$strXMLEB .= "</ConditionResponseCodeVal>";
														$strXMLEB .= "</ConditionResponseCode>";
														break;					
													case 11:
														$strXMLEB .= "<PlanConditionResponseCode>";
														$orival = $this->getVisionShareError("vision_share_271", "plan_condition_response_code", $strEBValVal);
															$strXMLEB .= "<PlanConditionResponseCodeId>";
															$strXMLEB .= $strEBValVal;
															$strXMLEB .= "</PlanConditionResponseCodeId>";
															
															$strXMLEB .= "<PlanConditionResponseCodeVal>";
															$strXMLEB .= (is_string($orival) ? $orival : "");
															$strXMLEB .= "</PlanConditionResponseCodeVal>";
														$strXMLEB .= "</PlanConditionResponseCode>";
														break;	
													case 12:
														$strXMLEB .= "<CompositeMedicalProcedureIdentifier>";
														$strValtemp = "";
														$arrValtemp = array();
														$strValtemp = trim($strEBValVal);
														$arrValtemp = explode("|",$strValtemp);										
														$orival = $this->getVisionShareError("vision_share_271", "composite_medical_procedure_identifier", trim($arrValtemp[0]));
															$strXMLEB .= "<CompositeMedicalProcedureIdentifier_1>";
																$strXMLEB .= "<CompositeMedicalProcedureIdentifierId_1>";
																$strXMLEB .= trim($arrValtemp[0]);
																$strXMLEB .= "</CompositeMedicalProcedureIdentifierId_1>";
																
																$strXMLEB .= "<CompositeMedicalProcedureIdentifierVal_1>";
																$strXMLEB .= (is_string($orival) ? $orival : "");
																$strXMLEB .= "</CompositeMedicalProcedureIdentifierVal_1>";
															$strXMLEB .= "</CompositeMedicalProcedureIdentifier_1>";
														
															$strXMLEB .= "<CompositeMedicalProcedureIdentifier_2>";
															$strXMLEB .= trim($arrValtemp[1]);
															$strXMLEB .= "</CompositeMedicalProcedureIdentifier_2>";
														
															$strXMLEB .= "<CompositeMedicalProcedureIdentifier_3>";
															$strXMLEB .= trim($arrValtemp[2]);
															$strXMLEB .= "</CompositeMedicalProcedureIdentifier_3>";
															
															$strXMLEB .= "<CompositeMedicalProcedureIdentifier_4>";
															$strXMLEB .= trim($arrValtemp[3]);
															$strXMLEB .= "</CompositeMedicalProcedureIdentifier_4>";
															
															$strXMLEB .= "<CompositeMedicalProcedureIdentifier_5>";
															$strXMLEB .= trim($arrValtemp[4]);
															$strXMLEB .= "</CompositeMedicalProcedureIdentifier_5>";
															
															$strXMLEB .= "<CompositeMedicalProcedureIdentifier_6>";
															$strXMLEB .= trim($arrValtemp[5]);
															$strXMLEB .= "</CompositeMedicalProcedureIdentifier_6>";
															
															$strXMLEB .= "<CompositeMedicalProcedureIdentifier_7>";
															$strXMLEB .= trim($arrValtemp[6]);
															$strXMLEB .= "</CompositeMedicalProcedureIdentifier_7>";
														$strXMLEB .= "</CompositeMedicalProcedureIdentifier>";
														break;							
												endswitch;
											}
											$intCountEBVal = count($arrEBVal);
											for($a = $intCountEBVal; $a <= 12; $a++){
												$a = (int)$a;
												switch ($a):
													case 0:
														$strXMLEB .= "<EligibilityOrBenefitInformation>
																		<EligibilityOrBenefitInformationId></EligibilityOrBenefitInformationId>
																		<EligibilityOrBenefitInformationVal></EligibilityOrBenefitInformationVal>
																	</EligibilityOrBenefitInformation>";
														break;
													case 1:
														$strXMLEB .= "<CoverageLevelCode>
																		<CoverageLevelCodeId></CoverageLevelCodeId>
																		<CoverageLevelCodeVal></CoverageLevelCodeVal>
																	</CoverageLevelCode>";
														break;
													case 2:
														$strXMLEB .= "<ServiceTypeCode>
																		<ServiceTypeCodeId></ServiceTypeCodeId>
																		<ServiceTypeCodeVal></ServiceTypeCodeVal>
																	</ServiceTypeCode>";
														break;
													case 3:
														$strXMLEB .= "<InsuranceTypeCode>
																		<InsuranceTypeCodeId></InsuranceTypeCodeId>
																		<InsuranceTypeCodeVal></InsuranceTypeCodeVal>
																	</InsuranceTypeCode>";
														break;
													case 4:
														$strXMLEB .= "<PlanCoverageDescription></PlanCoverageDescription>";
														break;
													case 5:
														$strXMLEB .= "<TimePeriodQualifier>
																		<TimePeriodQualifierId></TimePeriodQualifierId>
																		<TimePeriodQualifierVal></TimePeriodQualifierVal>
																	</TimePeriodQualifier>";
														break;
														break;
													case 6:
														$strXMLEB .= "<MonetaryAmount></MonetaryAmount>";
														break;
													case 7:
														$strXMLEB .= "<Percent></Percent>";
														break;
													case 8:
														$strXMLEB .= "<QuantityQualifier>
																		<QuantityQualifierId></QuantityQualifierId>
																		<QuantityQualifierVal></QuantityQualifierVal>
																	</QuantityQualifier>";
														break;
													case 9:
														$strXMLEB .= "<Quantity></Quantity>";
														break;
													case 10:
														$strXMLEB .= "<ConditionResponseCode>
																		<ConditionResponseCodeId></ConditionResponseCodeId>
																		<ConditionResponseCodeVal></ConditionResponseCodeVal>
																	</ConditionResponseCode>";
														break;					
													case 11:
														$strXMLEB .= "<PlanConditionResponseCode>
																		<PlanConditionResponseCodeId></PlanConditionResponseCodeId>
																		<PlanConditionResponseCodeVal></PlanConditionResponseCodeVal>
																	</PlanConditionResponseCode>";
														break;	
													case 12:
														$strXMLEB .= "<CompositeMedicalProcedureIdentifier>
																		<CompositeMedicalProcedureIdentifier_1>
																			<CompositeMedicalProcedureIdentifierId_1></CompositeMedicalProcedureIdentifierId_1>
																			<CompositeMedicalProcedureIdentifierVal_1></CompositeMedicalProcedureIdentifierVal_1>
																		</CompositeMedicalProcedureIdentifier_1>
																		<CompositeMedicalProcedureIdentifier_2></CompositeMedicalProcedureIdentifier_2>
																		<CompositeMedicalProcedureIdentifier_3></CompositeMedicalProcedureIdentifier_3>
																		<CompositeMedicalProcedureIdentifier_4></CompositeMedicalProcedureIdentifier_4>
																		<CompositeMedicalProcedureIdentifier_5></CompositeMedicalProcedureIdentifier_5>
																		<CompositeMedicalProcedureIdentifier_6></CompositeMedicalProcedureIdentifier_6>
																		<CompositeMedicalProcedureIdentifier_7></CompositeMedicalProcedureIdentifier_7>
																	</CompositeMedicalProcedureIdentifier>";
														break;													
												endswitch;
											}
											///////////////////////////////////////////
											$strXMLEB .= "</EBLOOP_".$key."_".$intKeyValVal.">";							
										}
										//$strXMLEB .= "</EBLOOP_".$key.">";
										$strXMLEB .= "</EBLOOPINFO>";
									}
								}
							}
						}
						$strXMLEB .= "</DTPEBLOOP>";
						//$strEBResponce = implode(",", $arrEBResponce);
						
						$patientDirVsXMLDir = "vs_271_response_xml/";
						if(!is_dir($upLoadPath.$patientDir.$patientDirVsXMLDir)){
							//Create patient VS 271 responce directory
							mkdir($upLoadPath.$patientDir.$patientDirVsXMLDir, 0700, true);
						}
						$fileNameXML = date("Y-m-d-H-i-s_").$patId."_5010_XML";
						$pathToWrite271xml = $upLoadPath.$patientDir.$patientDirVsXMLDir.$fileNameXML.".xml";		
						file_put_contents($pathToWrite271xml,$strXMLEB);
						$sqlEDIPathRespXML = "";
						$sqlEDIPathRespXML = $patientDir.$patientDirVsXMLDir.$fileNameXML.".xml";		
												
						$qryUpdate = "update real_time_medicare_eligibility set response_271_file_path = '".$sqlEDIPathResp."', 
										xml_271_responce = '".$sqlEDIPathRespXML."', EB_responce = '".$strEBResponce."', responce_date_time = NOW(), 
										transection_error = '".$transectionError."', responce_pat_policy_no = '".$strHL3RQPolicyNo."', responce_pat_add1 = '".$strHL3RQAdd1."', 
										responce_pat_add2 = '".$strHL3RQAdd2."', responce_pat_city = '".$strHL3RQCity."', responce_pat_state = '".$strHL3RQState."', 
										responce_pat_zip  = '".$strHL3RQZip."', responce_pat_dob = '".$strHL3RQPatDOB."', responce_pat_fname = '".$strHL3RQFName."', 
										responce_pat_lname = '".$strHL3RQLName."', responce_pat_mname  = '".$strHL3RQMName."', 
										response_deductible = '".$strValueD."', response_copay = '".$strValueCP."', response_co_insurance = '".$strValueCoIns."', 
										
										responce_dep_policy_no = '".$strHL4RQPolicyNo."', responce_dep_add1 = '".$strHL4RQAdd1."', 
										responce_dep_add2 = '".$strHL4RQAdd2."', responce_dep_city = '".$strHL4RQCity."', responce_dep_state = '".$strHL4RQState."', 
										responce_dep_zip  = '".$strHL4RQZip."', responce_dep_dob = '".$strHL4RQPatDOB."', responce_dep_fname = '".$strHL4RQFName."', 
										responce_dep_lname = '".$strHL4RQLName."', responce_dep_mname  = '".$strHL4RQMName."', responce_dep_relation  = '".$strDepRel."'
										
										where id = '".$intNewId."'";
						$rsQryUpdate =  imw_query($qryUpdate);
						if((int)$intPatAppIdSA > 0 && stristr($transectionError,'error')==false){
							$qryUpdateApp = "update schedule_appointments set rte_id = '".$intNewId."' where id = '".$intPatAppIdSA."'";
							$rsUpdateApp = imw_query($qryUpdateApp);
						}
					}
				}
				
				$strShowMsg = "no";
				if($blCopayExits == true){
					$strShowMsg = "yes";
				}
				
				if(empty($transectionError) == false){
					return $transectionError;
				}
				else{										
					return array($strEBResponce, $strEBInsuranceTypeCode, $intNewId, $strShowMsg);
					//return array($arrEBResponce, $arrEBInsuranceTypeCode);					
				}
			}
		}		
	}
}
?>