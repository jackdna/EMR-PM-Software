<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
require_once("SaveFile.php");
class CLSEligibility extends SoapClient
{
	private $objCoreLang;
	private $objSOAPClient;
	private $objCommonFunction;
	private $error, $soapUser, $soapPass;
	public $SOAPClientRec;
	private $oSaveFile;
	/********VARIABLE TO DO MULTIPLE rte IN ONE***/
	public $returnHeaderOnly;
	public $returnFooterOnly;
	public $edi270STCounter;
	public $HLsegmentCounter;
	/*********MULTI RETE VARIABLES END************/
	
	function __construct($askFor = "") {
		$this->soapUser = 'MWI_IMEDIC1';
		$this->soapPass = '9Hc6J4Nj';
		$this->objCoreLang = new core_lang();
		$this->objCommonFunction = new CLSCommonFunction();				
		$this->error = "Out of Scope.";
		if($askFor == "commercial"){
			$this->objSOAPClient = new SoapClient(dirname(__FILE__)."/emdeon_wsdl_production.wsdl");
			$arrSOAPClient = get_object_vars($this->objSOAPClient);
			if(empty($arrSOAPClient["sdl"]) == false){
				$this->SOAPClientRec = $arrSOAPClient["sdl"];
			}
			else{
				$this->SOAPClientRec = false;
			}
		}
		$this->returnHeaderOnly 	= false;
		$this->returnFooterOnly 	= false;
		$this->edi270STCounter 		= 0;
		$this->HLsegmentCounter		= 1;
		$this->oSaveFile		= new SaveFile();
	}
	
	function makeSpace($noOfSpace,$spaceSTR = " "){
		$noOfSpace = (int)$noOfSpace;
		$blankSpace = "";
		for($a = 1; $a <= $noOfSpace; $a++){
			$blankSpace .= $spaceSTR;
		}
		return $blankSpace;
	}
	
	function getPayerLevelInfo($arrLevelInfo, $level, $loop, $segment = ""){
		$arrReturn = array();
		foreach($arrLevelInfo as $intKey => $arrVal){
			if(((int)$arrVal["level"] == 1) && ($level == 1)){
				if((trim($arrVal["loop"]) == trim($loop)) && (trim($arrVal["segment_element"]) == trim($segment))){
					$arrReturn = array("codes_and_values" => trim($arrVal["codes_and_values"]));
					break;
				}
			}
			elseif(((int)$arrVal["level"] == 2) && ($level == 2)){				
				if((trim($arrVal["loop"]) == trim($loop)) && (trim($arrVal["segment_element"]) == trim($segment))){
					$arrReturn = array("codes_and_values" => trim($arrVal["codes_and_values"]));
					break;
				}
			}
			elseif(((int)$arrVal["level"] == 3) && ($level == 3)){
				if(trim($arrVal["loop"]) == trim($loop)){
					$arrReturn[] = trim($arrVal["segment_element"]);
					//break;
				}
			}
			elseif(((int)$arrVal["level"] == 4) && ($level == 4)){
				if(trim($arrVal["loop"]) == trim($loop)){					
					$strTemp = trim($arrVal["segment_element"]);
					$arrTemp = explode("=", $strTemp);
					foreach($arrTemp as $intTempKey => $strVal){
						if(empty($strVal) == true){
							unset($arrTemp[$intTempKey]);
						}
					}
					$arrTemp = array_values($arrTemp);
					if(count($arrTemp) == 1){
						$arrReturn[] = trim($arrTemp[0]);
					}
					else{
						$arrReturn[] = $arrTemp;
					}
					//pre($arrReturn);
					//break;
				}
			}
			elseif(((int)$arrVal["level"] == 5) && ($level == 5)){
				if(trim($arrVal["loop"]) == trim($loop)){
					$arrReturn = array();
					$strTemp = trim($arrVal["segment_element"]);
					$arrTemp = explode(",", $strTemp);
					foreach($arrTemp as $intTempKey => $strVal){
						if(empty($strVal) == true){
							unset($arrTemp[$intTempKey]);
						}
					}
					$arrTemp = array_values($arrTemp);
					foreach($arrTemp as $intTempKey => $strVal){
						$arr = array();
						$arr = explode("=", $strVal);						
						$arrReturn[] = array($arr[0] => trim($arr[1]));
					}
					foreach($arrReturn as $strTempKey => $strVal){
						if(empty($strVal) == true){
							unset($arrReturn[$strTempKey]);
						}
					}
					$arrReturn = array_values($arrReturn);	
					//pre($arrReturn);					
					break;
				}
			}
			elseif(((int)$arrVal["level"] == 6) && ($level == 6)){
				$arrReturn = array();
				//pre($arrVal);
				if((trim($arrVal["loop"]) == trim($loop)) && (trim($arrVal["segment_element"]) == trim($segment))){
					$arrReturn = array("codes_and_values" => trim($arrVal["codes_and_values"]));
					//pre($arrReturn,1);
					break;
				}
			}
			elseif(((int)$arrVal["level"] == 7) && ($level == 7)){
				if(trim($arrVal["loop"]) == trim($loop)){
					$strTemp = trim($arrVal["segment_element"]);
					$arrTemp = explode("=", $strTemp);
					foreach($arrTemp as $intTempKey => $strVal){
						if(empty($strVal) == true){
							unset($arrTemp[$intTempKey]);
						}
					}
					$arrTemp = array_values($arrTemp);
					if(count($arrTemp) == 1){
						$arrReturn[] = trim($arrTemp[0]);
					}
					else{
						$arrReturn[] = $arrTemp;
					}
					//break;
				}
			}
			elseif(((int)$arrVal["level"] == 8) && ($level == 8)){
				if(trim($arrVal["loop"]) == trim($loop)){					
					$arrReturn = array();
					$strTemp = trim($arrVal["segment_element"]);
					$arrTemp = explode(",", $strTemp);
					foreach($arrTemp as $intTempKey => $strVal){
						if(empty($strVal) == true){
							unset($arrTemp[$intTempKey]);
						}
					}
					$arrTemp = array_values($arrTemp);
					foreach($arrTemp as $intTempKey => $strVal){
						$arr = array();
						$arr = explode("=", $strVal);						
						$arrReturn[] = array($arr[0] => trim($arr[1]));
					}
					foreach($arrReturn as $strTempKey => $strVal){
						if(empty($strVal) == true){
							unset($arrReturn[$strTempKey]);
						}
					}
					$arrReturn = array_values($arrReturn);	
					//pre($arrReturn);					
					break;					
				}
			}
			elseif(((int)$arrVal["level"] == 9) && ($level == 9)){
				if((trim($arrVal["loop"]) == trim($loop)) && (trim($arrVal["segment_element"]) == trim($segment))){
					$arrReturn = array("codes_and_values" => trim($arrVal["codes_and_values"]));
					break;
				}
			}
		}
		return ((is_array($arrReturn) ? $arrReturn : false));
	}
	
	function inMultiArray($needle, $hayStack){ 
		$inMultiArray = false;
		if(in_array($needle, $hayStack)){ 
			$inMultiArray = true; 
		} 
		else{  
			for($i = 0; $i < count($hayStack); $i++){
				if(is_array($hayStack[$i])){
					if($this->inMultiArray($needle, $hayStack[$i])){
						$inMultiArray = true; 
						break; 
					}
				}
			}
		}
		return $inMultiArray;
	}
	
	function askKeyExitArray($strKey, $hayStack){
		$ansKey = false;
		if(array_key_exists($strKey, $hayStack)){
			//echo $strKey;			
			//pre($hayStack);
			//echo $hayStack[$strKey]."````";
			//die("adsfasfas");
			return $hayStack[$strKey];
		}
		else{
			for($i = 0; $i < count($hayStack); $i++){
				if(is_array($hayStack[$i])){
					//echo "d----";
					//pre($hayStack[$i]);
					$tempVal = "";
					$tempVal = $this->askKeyExitArray($strKey, $hayStack[$i]);					
					if($tempVal !== false){
						return $ansKey = $tempVal;
						//echo "^^^".$ansKey;
						//die("eee");
						//break; 
					}
				}
			}
		}
		return $ansKey;
	}
	
	function replaceNR($str){
		$str = str_replace(array("\\r","\\n"), " ", $str);
		$str = str_replace(array("\r","\n"), " ", $str);
		$str = preg_replace('/\s\s+/', " ", $str);
		return $str;
	}

	function parseXML271Response($strXMLPath, $parseFor = 1){
		//$parseFor: 1 = Detail, 2 = Summary;
		if(file_exists($strXMLPath)){
			$arrXMLvalues = array();
			$arrEBInfo = array('1','2','3','4','5','6','7','8','I','K','F','A','E','R');
			
			$XML = file_get_contents($strXMLPath);			
			$arrXMLvalues = $this->objCommonFunction->XMLToArray($XML);
			//pre($arrXMLvalues,1);
			$blDTPLoopSt = $blDTPDateSt = $blEBLoopSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = $blEBDTPDateSt = false;
			$blEBConditionRespSt = $blEBPlanConditionRespSt = false;
			$intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = $intEBDTPId = 0;
			$srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = $srtEBDTPVal = $srtEBDTPFormat = $srtEBDTPDate = $srtEBDTPDate1 = $srtEBDTPDate2 = "";
			$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
			$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
			$strMAMBRow = $strDeductibleRow = $strCopayRow = $strOtherRow = "";
			$intTempCounterD = $intTempCounterC = $intTempCounterB = $intTempCounterV = $intTempCounterPC = $intTempCounterEBinfo = $intTempCounterOtinfo = 2;
			
			$strDTPLoopName = $strEBLoopName = "";
			$blDTPInternal = $blEBLoopInternal = false;
			$intDTPLoopCo = $intEBLoopCo = 0;
			$strMSGValue = "";
			$strBenefitRow = $strVisionRow = $strPerCertRow = "";
			
			$blMSGLoopSt = $blEBDTPLoopSt = false;
			
			foreach($arrXMLvalues as $key => $val){
				if(($val["tag"] == "DTPLOOPINFO") && ($val["type"] == "open") && ($blDTPLoopSt == false)){					
					$blDTPLoopSt = true;					
				}
				elseif(($blDTPLoopSt == true) && ($blDTPInternal == false) && ($strDTPLoopName == "")){
					$valTemp = $val["tag"];					
					if(strpos($valTemp, "DTPLOOP_") !== false){
						$arrTemp = explode("_", $valTemp);
						$intDTPLoopCo = $arrTemp[1];
						$strDTPLoopName = $valTemp;
						$blDTPInternal = true;
					}
				}
				elseif(($val["tag"] == $strDTPLoopName) && ($val["type"] == "close") && ($blDTPInternal == true)){
					$strDTPLoopName = "";					
					//$blDTPInternal = false;
					//echo $key;
					//pre($val,1);
					//pre(current($arrXMLvalues));
					//pre(next($arrXMLvalues));
					//die('zzfff');
				}
				elseif((trim($val["tag"]) == "DTPLOOPINFO") && (trim($val["type"]) == "close") && ($blDTPLoopSt == true)){					
					$blDTPLoopSt = $blDTPInternal = false;
					//$intDTPLoopCo = 0;
					//die('zzfff444');
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "open") && ($blEBLoopSt == false)){
					//die('qqq');
					$blEBLoopSt = true;
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "close") && ($blEBLoopSt == true)){
					$blEBLoopSt = false;
					$intDTPLoopCo = 0;
				}
				
				elseif(($val["tag"] == "MSG") && ($val["type"] == "complete") && (empty($val["value"]) == false) && ($strMSGValue == "")){
					//die('qqq');
					$strMSGValue = $val["value"];
				}
				elseif(($val["tag"] == "EBDTP") && ($val["type"] == "open") && ($blEBDTPLoopSt == false)){					
					//die('qqq'.$key);					
					$blEBDTPLoopSt = true;
				}
				elseif(($val["tag"] == "EBDTP") && ($val["type"] == "close") && ($blEBDTPLoopSt == true)){					
					//die('rr'.$key);
					$blEBDTPLoopSt = false;
				}
				elseif($blEBDTPLoopSt == true){
					if(($val["tag"] == "DateTimeQualifierID") && ($val["type"] == "complete") && ($intEBDTPId == 0)){
						$intEBDTPId = $val["value"];
					}
					elseif(($val["tag"] == "DateTimeQualifierIDVal") && ($val["type"] == "complete") && ($srtEBDTPVal == "")){
						$srtEBDTPVal = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriodFormatQualifier") && ($val["type"] == "complete") && ($srtEBDTPFormat == "")){
						$srtEBDTPFormat = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "open") && ($blEBDTPDateSt == false)){
						$blEBDTPDateSt = true;
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "close") && ($blEBDTPDateSt == true)){
						$blEBDTPDateSt = false;
					}					
					if($blEBDTPDateSt == true){
						if(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtEBDTPFormat == "D8") && ($srtEBDTPDate == "")){
							$srtEBDTPDate = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtEBDTPFormat == "RD8") && ($srtEBDTPDate1 == "")){
							$srtEBDTPDate1 = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate2") && ($val["type"] == "complete") && ($srtEBDTPFormat == "RD8") && ($srtEBDTPDate2 == "")){
							$srtEBDTPDate2 = $val["value"];
						}
					}
					//echo $intEBDTPId."--".$srtEBDTPVal."--".$srtEBDTPFormat."--".$srtEBDTPDate."--".$key."<br>";
					//die('EBDTP');					
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "complete")){
					//die('lllll');
					$blDTPLoopSt = $blDTPDateSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = $blEBDTPDateSt = false;
					$blEBConditionRespSt = $blEBPlanConditionRespSt = false;
					$intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = $intEBDTPId = 0;
					$srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = $srtEBDTPVal = $srtEBDTPFormat = $srtEBDTPDate = $srtEBDTPDate1 = $srtEBDTPDate2 = "";
					$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
					$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
															
					$strDTPLoopName = $strEBLoopName = "";//$strMSGValue = "";
					$blDTPInternal = $blEBLoopInternal = false;
					$intEBLoopCo = 0;
				}				
				elseif(($val["tag"] == $strEBLoopName) && ($val["type"] == "close") && ($blEBLoopInternal == true)){
					//echo $strEBLoopName;
					//die('aaaaa');
					/*if($strEBLoopName == "EBLOOP_1_0"){
						die($strEBLoopName.'===='.$valTemp.'--'.$key);
					}*/
					$blDTPInternal = $blEBLoopInternal = false;
					
					if(in_array(trim($strEBInfoId), $arrEBInfo) == true){
						//$strMAMBRow .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";		
						$class = ($intTempCounterEBinfo % 2) == 0 ? "bgcolor" : "";				
							$strMAMBRow .= "<tr class=\"$class\" title=\"$strMSGValue\">";
								$strMAMBRow .= "<td>";
								if(trim($strEBInsTypeVal) != ""){
									$strMAMBRow .= '<b>'.$strEBInsTypeVal.'</b>';
									
									$strMAMBRow .= "<div><i>";
									if(trim($strEBSerTypeVal) != ""){
										$strMAMBRow .= $strEBSerTypeVal;
									}
									else{
										$str_TempServTypeVal = $this->getServiceTypeCodeValueById($intEBSerTypeId);
										if($str_TempServTypeVal!=''){
											$strMAMBRow .= $str_TempServTypeVal;
										}else{
											$strMAMBRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
										}
									}
									$strMAMBRow .= "</i></div>";
									
								}
								else{									
									$strMAMBRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
									if(is_string($intEBSerTypeId) == true){
										$arrTempServiceCode = array();
										$arrTempServiceCode = explode("^", $intEBSerTypeId);
										foreach($arrTempServiceCode as $intTempServiceCodeKey => $strTempServiceCodeVal){
											$orival = $refineVal = "";
											$orival = $this->objCoreLang->get_vocabulary("vision_share_271", "Service_Type_Code", $strTempServiceCodeVal);
											$refineVal = ((is_string($orival) && empty($orival) == false) ? $orival : "");
											if(empty($refineVal) == false){
												$strEBSerTypeVal .= $refineVal."<br>";
											}
										}
									}
									$strMAMBRow .= "<div><i>";
									if(trim($strEBSerTypeVal) != ""){
										$strMAMBRow .= $strEBSerTypeVal;
									}
									else{
										$strMAMBRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
									}
									$strMAMBRow .= "</i></div>";
								}
								$strMAMBRow .= "</td>";
								$strMAMBRow .= "<td>";
								if(trim($strEBInfoVal) != ""){
									$strMAMBRow .= $strEBInfoVal;	
								}
								else{
									$strMAMBRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
								}
								$strMAMBRow .= "</td>";
								$strMAMBRow .= "<td>";
								if(trim($strEBCovLevelVal) != ""){
									$strMAMBRow .= $strEBCovLevelVal;	
								}
								else{
									$strMAMBRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								}
								$strMAMBRow .= "</td>";
								//echo $srtEBDTPFormat."--".$srtEBDTPDate."--".$srtEBDTPDate1."--".$srtEBDTPDate2;
								//die;
								if((empty($srtEBDTPFormat) == false) && ((empty($srtEBDTPDate) == false) || (empty($srtEBDTPDate1) == false) || (empty($srtEBDTPDate2) == false))){
									$strMAMBRow .= "<td>";
									if($srtEBDTPFormat == "D8"){
										$intDOBYear = substr($srtEBDTPDate,0,4);
										$intDOBMon = substr($srtEBDTPDate,4,2);
										$intDOBDay = substr($srtEBDTPDate,6,2);
										$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
										$strMAMBRow .= $srtDTPDateNew;
										if(trim($srtEBDTPVal) != ""){										
											$strMAMBRow .= "<div><i>".$srtEBDTPVal."</i></div>";
										}
										else{
											$strMAMBRow .= ((empty($intEBDTPId) == false) ? "<div><i>".$intEBDTPId."(Unknown)</i></div>" : "");
										}	
									}
									elseif($srtEBDTPFormat == "RD8"){
										$intDOBYear = substr($srtEBDTPDate1,0,4);
										$intDOBMon = substr($srtEBDTPDate1,4,2);
										$intDOBDay = substr($srtEBDTPDate1,6,2);
										$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
										$intDOBYear = substr($srtEBDTPDate2,0,4);
										$intDOBMon = substr($srtEBDTPDate2,4,2);
										$intDOBDay = substr($srtEBDTPDate2,6,2);
										$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
										$strMAMBRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
										if(trim($srtEBDTPVal) != ""){
											$strMAMBRow .= "<div><i>".$srtEBDTPVal."</i></div>";
										}
										else{
											$strMAMBRow .= ((empty($intEBDTPId) == false) ? "<div><i>".$intEBDTPId."(Unknown)</i></div>" : "");
										}
									}								
									$strMAMBRow .= "</td>";
								}
								else{
									$strMAMBRow .= "<td>";
									if($srtDTPFormat == "D8"){
										$intDOBYear = substr($srtDTPDate,0,4);
										$intDOBMon = substr($srtDTPDate,4,2);
										$intDOBDay = substr($srtDTPDate,6,2);
										$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
										$strMAMBRow .= $srtDTPDateNew;
										if(trim($srtDTPVal) != ""){										
											$strMAMBRow .= "<div><i>".$srtDTPVal."</i></div>";
										}
										else{
											$strMAMBRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
										}	
									}
									elseif($srtDTPFormat == "RD8"){
										$intDOBYear = substr($srtDTPDate1,0,4);
										$intDOBMon = substr($srtDTPDate1,4,2);
										$intDOBDay = substr($srtDTPDate1,6,2);
										$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
										$intDOBYear = substr($srtDTPDate2,0,4);
										$intDOBMon = substr($srtDTPDate2,4,2);
										$intDOBDay = substr($srtDTPDate2,6,2);
										$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
										$strMAMBRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
										if(trim($srtDTPVal) != ""){
											$strMAMBRow .= "<div><i>".$srtDTPVal."</i></div>";
										}
										else{
											$strMAMBRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
										}
									}								
									$strMAMBRow .= "</td>";
								}
	
								$strMAMBRow .= "<td>";
								$strMAMBRow .= $strEBPlanCoverageDesc;
								$strMAMBRow .= "</td>";
								#### Message from Information Source 
								$strMAMBRow .= "<td>";						
									$strMAMBRow .= $strMSGValue;
								$strMAMBRow .= "</td>";
								$strMSGValue = "";					
								####
							$strMAMBRow .= "</tr>";
							$intTempCounterEBinfo++;
						//$strMAMBRow .= "</table>";
					}
					elseif((trim($strEBInfoId) == "C") || (trim($strEBInfoId) == "B") || (trim($strEBInfoId) == "D")){
						if(trim($strEBInfoId) == "C"){ //Deductible information
							$class = ($intTempCounterC % 2) == 0 ? "bgcolor" : "";
							$strDeductibleRow .= "<tr class=\"$class\" title=\"$strMSGValue\" >";
							$strDeductibleRow .= "<td>";
							if(trim($strEBInsTypeVal) != ""){
								$strDeductibleRow .= '<b>'.$strEBInsTypeVal.'</b>';	
								$strDeductibleRow .= "<div><i>";
								if(trim($strEBSerTypeVal) != ""){
									$strDeductibleRow .= $strEBSerTypeVal;
								}
								else{
									$temp_strEBSerTypeVal = $this->getServiceTypeCodeValueById($intEBSerTypeId);
									if($temp_strEBSerTypeVal!=''){
										$strDeductibleRow .= $temp_strEBSerTypeVal;	
									}else{
										$strDeductibleRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
									}
								}
								$strDeductibleRow .= "</i></div>";
							}
							else{
								$strDeductibleRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
								if(is_string($intEBSerTypeId) == true){
									$arrTempServiceCode = array();
									$arrTempServiceCode = explode("^", $intEBSerTypeId);
									foreach($arrTempServiceCode as $intTempServiceCodeKey => $strTempServiceCodeVal){
										$orival = $refineVal = "";
										$orival = $this->objCoreLang->get_vocabulary("vision_share_271", "Service_Type_Code", $strTempServiceCodeVal);
										$refineVal = ((is_string($orival) && empty($orival) == false) ? $orival : "");
										if(empty($refineVal) == false){
											$strEBSerTypeVal .= $refineVal."<br>";
										}
									}
								}
								//$strDeductibleRow .= $strEBInsTypeId;
								$strDeductibleRow .= "<div><i>";
								if(trim($strEBSerTypeVal) != ""){
									$strDeductibleRow .= $strEBSerTypeVal;
								}
								else{
									$strDeductibleRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "Unknown");
								}
								$strDeductibleRow .= "</i></div>";
							}
							$strDeductibleRow .= "</td>";
							
							$strDeductibleRow .= "<td>";
							if(trim($strEBCovLevelVal) != ""){
								$strDeductibleRow .= $strEBCovLevelVal;	
							}
							else{
								$strDeductibleRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
							}
							$strDeductibleRow .= "</td>";
							
							$strDeductibleRow .= "<td>";						
							$strDeductibleRow .= "$".(float)$intEBMonetaryAmount;	
							$strDeductibleRow .= "<div><i>";
							if(trim($strEBTimePeriodQuVal) != ""){
								$strDeductibleRow .= " ".$strEBTimePeriodQuVal;	
							}
							else{
								$strDeductibleRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
							}
							$strDeductibleRow .= "</i></div>";
							$strDeductibleRow .= "</td>";						
							##
							
							if((empty($srtEBDTPFormat) == false) && ((empty($srtEBDTPDate) == false) || (empty($srtEBDTPDate1) == false) || (empty($srtEBDTPDate2) == false))){
								$strDeductibleRow .= "<td>";
								if($srtEBDTPFormat == "D8"){
									$intDOBYear = substr($srtEBDTPDate,0,4);
									$intDOBMon = substr($srtEBDTPDate,4,2);
									$intDOBDay = substr($srtEBDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strDeductibleRow .= $srtDTPDateNew;
									if(trim($srtEBDTPVal) != ""){										
										$strDeductibleRow .= "<div><i>".$srtEBDTPVal."</i></div>";
									}
									else{
										$strDeductibleRow .= ((empty($intEBDTPId) == false) ? "<div><i>".$intEBDTPId."(Unknown)</i></div>" : "");
									}	
								}
								elseif($srtEBDTPFormat == "RD8"){
									$intDOBYear = substr($srtEBDTPDate1,0,4);
									$intDOBMon = substr($srtEBDTPDate1,4,2);
									$intDOBDay = substr($srtEBDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtEBDTPDate2,0,4);
									$intDOBMon = substr($srtEBDTPDate2,4,2);
									$intDOBDay = substr($srtEBDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strDeductibleRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtEBDTPVal) != ""){
										$strDeductibleRow .= "<div><i>".$srtEBDTPVal."</i></div>";
									}
									else{
										$strDeductibleRow .= ((empty($intEBDTPId) == false) ? "<div><i>".$intEBDTPId."(Unknown)</i></div>" : "");
									}
								}								
								$strDeductibleRow .= "</td>";
							}
							else{			
								$strDeductibleRow .= "<td>";
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strDeductibleRow .= $srtDTPDateNew;
									if(trim($srtDTPVal) != ""){										
										$strDeductibleRow .= "<div><i>".$srtDTPVal."</i></div>";
									}
									else{
										$strDeductibleRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strDeductibleRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtDTPVal) != ""){
										$strDeductibleRow .= "<div><i>".$srtDTPVal."</i></div>";
									}
									else{
										$strDeductibleRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
									}
								}								
								$strDeductibleRow .= "</td>";
							}
							##
							####					
							$strDeductibleRow .= "<td>";						
							$strDeductibleRow .= $intEBQuantity;
							if(trim($strEBQuantityQuVal) != ""){										
								$strDeductibleRow .= "<div><i>".$strEBQuantityQuVal."</i></div>";
							}
							else{
								$strDeductibleRow .= ((empty($strEBQuantityQuId) == false) ? "<div><i>".$strEBQuantityQuId."(Unknown)</i></div>" : "");
							}							
							$strDeductibleRow .= "</td>";
							####
							
							#### Authorization or Certification Indicator
							$strDeductibleRow .= "<td>";						
							if(trim($strEBConditionRespVal) != ""){										
								$strDeductibleRow .= $strEBConditionRespVal;
							}
							else{
								$strDeductibleRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
							}					
							$strDeductibleRow .= "</td>";
							####
							
							#### Plan Network Indicator
							$strDeductibleRow .= "<td>";						
							if(trim($strEBPlanConditionRespVal) != ""){										
								$strDeductibleRow .= $strEBPlanConditionRespVal;
							}
							else{
								$strDeductibleRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
							}			
							$strDeductibleRow .= "</td>";							
							#### 
							#### Message from Information Source 
							$strDeductibleRow .= "<td>";						
								$strDeductibleRow .= $strMSGValue;
							$strDeductibleRow .= "</td>";
							$strMSGValue = "";							
							####
							$strDeductibleRow .= "</tr>";
							$intTempCounterC++;
						}
						elseif(trim($strEBInfoId) == "B"){ //Co-Payment information
							$class = ($intTempCounterD % 2) == 0 ? "bgcolor" : "";
							$strCopayRow .= "<tr class=\"$class\" title=\"$strMSGValue\" >";
							$strCopayRow .= "<td>";
							if(trim($strEBInsTypeVal) != ""){								
								$strCopayRow .= $strEBInsTypeVal;	
								$strCopayRow .= "<div><i>";
								if(trim($strEBSerTypeVal) != ""){
									$strCopayRow .= $strEBSerTypeVal;
								}
								else{
									$strCopayRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}
								$strCopayRow .= "</i></div>";
							}
							else{
								$strCopayRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
								if(is_string($intEBSerTypeId) == true){
									$arrTempServiceCode = array();
									$arrTempServiceCode = explode("^", $intEBSerTypeId);
									foreach($arrTempServiceCode as $intTempServiceCodeKey => $strTempServiceCodeVal){
										$orival = $refineVal = "";
										$orival = $this->objCoreLang->get_vocabulary("vision_share_271", "Service_Type_Code", $strTempServiceCodeVal);
										$refineVal = ((is_string($orival) && empty($orival) == false) ? $orival : "");
										if(empty($refineVal) == false){
											$strEBSerTypeVal .= $refineVal."<br>";
										}
									}
								}
								//$strCopayRow .= $strEBInsTypeId;
								$strCopayRow .= "<div><i>";
								if(trim($strEBSerTypeVal) != ""){
									$strCopayRow .= $strEBSerTypeVal;
								}
								else{
									$strCopayRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}
								$strCopayRow .= "</i></div>";
							}
							$strCopayRow .= "</td>";
							
							$strCopayRow .= "<td>";
							if(trim($strEBCovLevelVal) != ""){
								$strCopayRow .= $strEBCovLevelVal;	
							}
							else{
								$strCopayRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
							}
							$strCopayRow .= "</td>";
							
							$strCopayRow .= "<td>";						
							//$strCopayRow .= "$".(float)$intEBMonetaryAmount;
							$strCopayRow .= ($intEBMonetaryAmount) ? "$".(float)$intEBMonetaryAmount : "";
							
							$strCopayRow .= "<div><i>";
							if((trim($strEBTimePeriodQuVal) != "") && (empty($intEBMonetaryAmount) == false)){
								$strCopayRow .= " ".$strEBTimePeriodQuVal;	
							}
							elseif(empty($intEBMonetaryAmount) == false){
								$strCopayRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
							}
							$strCopayRow .= "</i></div>";
							$strCopayRow .= "</td>";						
							##						
							
							if((empty($srtEBDTPFormat) == false) && ((empty($srtEBDTPDate) == false) || (empty($srtEBDTPDate1) == false) || (empty($srtEBDTPDate2) == false))){
								$strCopayRow .= "<td>";
								if($srtEBDTPFormat == "D8"){
									$intDOBYear = substr($srtEBDTPDate,0,4);
									$intDOBMon = substr($srtEBDTPDate,4,2);
									$intDOBDay = substr($srtEBDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCopayRow .= $srtDTPDateNew;
									if(trim($srtEBDTPVal) != ""){										
										$strCopayRow .= "<div><i>".$srtEBDTPVal."</i></div>";
									}
									else{
										$strCopayRow .= ((empty($intEBDTPId) == false) ? "<div><i>".$intEBDTPId."(Unknown)</i></div>" : "");
									}	
								}
								elseif($srtEBDTPFormat == "RD8"){
									$intDOBYear = substr($srtEBDTPDate1,0,4);
									$intDOBMon = substr($srtEBDTPDate1,4,2);
									$intDOBDay = substr($srtEBDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtEBDTPDate2,0,4);
									$intDOBMon = substr($srtEBDTPDate2,4,2);
									$intDOBDay = substr($srtEBDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCopayRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtEBDTPVal) != ""){
										$strCopayRow .= "<div><i>".$srtEBDTPVal."</i></div>";
									}
									else{
										$strCopayRow .= ((empty($intEBDTPId) == false) ? "<div><i>".$intEBDTPId."(Unknown)</i></div>" : "");
									}
								}								
								$strCopayRow .= "</td>";
							}
							else{
								$strCopayRow .= "<td>";
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCopayRow .= $srtDTPDateNew;
									if(trim($srtDTPVal) != ""){										
										$strCopayRow .= "<div><i>".$srtDTPVal."</i></div>";
									}
									else{
										$strCopayRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCopayRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtDTPVal) != ""){
										$strCopayRow .= "<div><i>".$srtDTPVal."</i></div>";
									}
									else{
										$strCopayRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
									}
								}								
								$strCopayRow .= "</td>";
							}
							##
							####					
							$strCopayRow .= "<td>";						
							$strCopayRow .= $intEBQuantity;
							if(trim($strEBQuantityQuVal) != ""){										
								$strCopayRow .= "<div><i>".$strEBQuantityQuVal."</i></div>";
							}
							else{
								$strCopayRow .= ((empty($strEBQuantityQuId) == false) ? "<div><i>".$strEBQuantityQuId."(Unknown)</i></div>" : "");
							}							
							$strCopayRow .= "</td>";
							####
							
							#### Authorization or Certification Indicator
							$strCopayRow .= "<td>";						
							if(trim($strEBConditionRespVal) != ""){										
								$strCopayRow .= $strEBConditionRespVal;
							}
							else{
								$strCopayRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
							}					
							$strCopayRow .= "</td>";
							####
							
							#### Plan Network Indicator
							$strCopayRow .= "<td>";						
							if(trim($strEBPlanConditionRespVal) != ""){										
								$strCopayRow .= $strEBPlanConditionRespVal;
							}
							else{
								$strCopayRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
							}			
							$strCopayRow .= "</td>";
							#### 
							#### Message from Information Source 
							$strCopayRow .= "<td>";						
								$strCopayRow .= $strMSGValue;
							$strCopayRow .= "</td>";
							$strMSGValue = "";							
							$strCopayRow .= "</tr>";
							$intTempCounterD++;
						}						
						elseif((trim($strEBInfoId) == "D") && ($parseFor == 2)){ //Benefit Description
							$strBenefitRowInsType = $strBenefitRowInsCov = $strBenefitRowInsSer = $strBenefitRowInsPlan = "";															
							if(trim($strEBInsTypeVal) != ""){
								//Insurance Type - start
								$strBenefitRowInsType = $strEBInsTypeVal;	
								//Insurance Type - end
							}
							else{
								//Insurance Type - start
								$strBenefitRowInsType = ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "");
								//Insurance Type - end
							}
							
							if(trim($strEBCovLevelVal) != ""){
								//Insurance Coverage Level - start
								$strBenefitRowInsCov = $strEBCovLevelVal;	
								//Insurance Coverage Level - end
							}
							else{
								//Insurance Coverage Level - start
								$strBenefitRowInsCov = ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								//Insurance Coverage Level - end
							}
							
							if(trim($strEBSerTypeVal) != ""){
								//Insurance Service Type - start
								$strBenefitRowInsSer = $strEBSerTypeVal;
								//Insurance Service Type - end
							}
							else{
								//Insurance Service Type - start
								$strBenefitRowInsSer = ((empty($strEBSerTypeId) == false) ? $strEBSerTypeId."(Unknown)" : "");
								//Insurance Service Type - end
							}
							
							if(trim($strEBPlanCoverageDesc) != ""){
								//Insurance Plan Coverage Description - Start
								$strBenefitRowInsPlan = $strEBPlanCoverageDesc;
								//Insurance Plan Coverage Description - end
							}						
							
							if(($strBenefitRowInsSer != "") || ($strBenefitRowInsPlan != "")){
								$class = ($intTempCounterB % 2) == 0 ? "bgcolor" : "";
								$strBenefitRow .= "<tr class=\"$class\" title=\"$strMSGValue\" >";
								$strBenefitRow .= "<td>".$strBenefitRowInsType."</td><td>".$strBenefitRowInsCov."</td><td>".$strBenefitRowInsSer."</td><td>".$strBenefitRowInsPlan."</td>";
								$strBenefitRow .= "</tr>";
								$intTempCounterB++;
							}
						}												
					}
					elseif(((trim($strEBSerTypeId) == "AL") || (trim($strEBSerTypeId) == "AM") || (trim($strEBSerTypeId) == "AN") || (trim($strEBSerTypeId) == "AO"))  && ($parseFor == 2)){ //
						$strVisionRowInsType = $strVisionRowInsStatus = $strVisionRowInsCov = $strVisionRowInsSer = $strVisionRowInsPlan = "";															
						if(trim($strEBInsTypeVal) != ""){
							//Insurance Type - start
							$strVisionRowInsType = $strEBInsTypeVal;	
							//Insurance Type - end
						}
						else{
							//Insurance Type - start
							$strVisionRowInsType = ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "");
							//Insurance Type - end
						}
																				
						if(trim($strEBInfoVal) != ""){
							//Insurance statue - start
							$strVisionRowInsStatus = $strEBInfoVal;	
							//Insurance statue - end
						}
						else{
							//Insurance statue - start
							$strVisionRowInsStatus = ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
							//Insurance statue - end
						}
						
						
						if(trim($strEBCovLevelVal) != ""){
							//Insurance Coverage Level - start
							$strVisionRowInsCov = $strEBCovLevelVal;	
							//Insurance Coverage Level - end
						}
						else{
							//Insurance Coverage Level - start
							$strVisionRowInsCov = ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
							//Insurance Coverage Level - end
						}
						
						if(trim($strEBSerTypeVal) != ""){
							//Insurance Service Type - start
							$strVisionRowInsSer = $strEBSerTypeVal;
							//Insurance Service Type - end
						}
						else{
							//Insurance Service Type - start
							$strVisionRowInsSer = ((empty($strEBSerTypeId) == false) ? $strEBSerTypeId."(Unknown)" : "");
							//Insurance Service Type - end
						}
						
						if(trim($strEBPlanCoverageDesc) != ""){
							//Insurance Plan Coverage Description - Start
							$strVisionRowInsPlan = $strEBPlanCoverageDesc;
							//Insurance Plan Coverage Description - end
						}						
						
						if(($strVisionRowInsCov != "")){
							$class = ($intTempCounterD % 2) == 0 ? "bgcolor" : "";
							$strVisionRow .= "<tr class=\"$class\" title=\"$strMSGValue\" >";
							$strVisionRow .= "<td>".$strVisionRowInsType."</td><td>".$strVisionRowInsStatus."</td><td>".$strVisionRowInsCov."</td><td>".$strVisionRowInsSer."</td><td>".$strVisionRowInsPlan."</td>";
							$strVisionRow .= "</tr>";	
							$$intTempCounterV++;
						}
					}
					elseif(((empty($strEBConditionRespId) == false) || (empty($strEBConditionRespVal) == false)) && ($parseFor == 2)){
						$class = ($intTempCounterPC % 2) == 0 ? "bgcolor" : "";				
						$strPerCertRow .= "<tr class=\"$class\" title=\"$strMSGValue\" >";
						$strPerCertRow .= "<td nowrap>"; //Insurance Type - start
						if(trim($strEBInsTypeVal) != ""){
							$strPerCertRow .= $strEBInsTypeVal;
							if(trim($strEBSerTypeVal) != ""){
								$strPerCertRow .= "<div>".$strEBSerTypeVal."</div>";	
							}
							else{
								$strPerCertRow .= "<div>".((empty($strEBSerTypeId) == false) ? $strEBSerTypeId."(Unknown)" : "")."</div>";	
							}
						}
						else{
							$strPerCertRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "");
							if(trim($strEBSerTypeVal) != ""){
								$strPerCertRow .= "<div>".$strEBSerTypeVal."</div>";	
							}
							else{
								$strPerCertRow .= "<div>".((empty($strEBSerTypeId) == false) ? $strEBSerTypeId."(Unknown)" : "")."</div>";	
							}
						}
						$strPerCertRow .= "</td>"; //Insurance Type - end
						
						$strPerCertRow .= "<td>"; //Insurance statue - start
						if(trim($strEBInfoVal) != ""){
							$strPerCertRow .= $strEBInfoVal;	
						}
						else{
							$strPerCertRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
						}
						$strPerCertRow .= "</td>"; //Insurance statue - end
						
						$strPerCertRow .= "<td>"; //Insurance Coverage Level - start
						if(trim($strEBCovLevelVal) != ""){
							$strPerCertRow .= $strEBCovLevelVal;	
						}
						else{
							$strPerCertRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
						}
						$strPerCertRow .= "</td>"; //Insurance Coverage Level - end
																				
						$strPerCertRow .= "<td>"; //Insurance Authorization or Certification Indicator - start
						if(trim($strEBConditionRespVal) != ""){										
							$strPerCertRow .= $strEBConditionRespVal;
						}
						else{
							$strPerCertRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
						}							
						$strPerCertRow .= "</td>"; //Insurance Authorization or Certification Indicator   - end
						
						$strPerCertRow .= "<td>"; //Insurance In Plan Network Indicator - start
						if(trim($strEBPlanConditionRespVal) != ""){										
							$strPerCertRow .= $strEBPlanConditionRespVal;
						}
						else{
							$strPerCertRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
						}							
						$strPerCertRow .= "</td>"; //Insurance In Plan Network Indicator - end
						$intTempCounterPC++;
					}
					elseif((empty($strEBPercent) == false) || (empty($strEBConditionRespId) == false) || (empty($strEBConditionRespVal) == false) || (empty($strEBPlanConditionRespId) == false) || (empty($strEBPlanConditionRespVal) == false)){						
						$class = ($intTempCounterOtinfo % 2) == 0 ? "bgcolor" : "";				
						$strOtherRow .= "<tr class=\"$class\" title=\"$strMSGValue\" >";
							$strOtherRow .= "<td nowrap>"; //Insurance Type - start
							if(trim($strEBInsTypeVal) != ""){
								$strOtherRow .= $strEBInsTypeVal;	
							}
							else{
								$strOtherRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
							}
							$strOtherRow .= "</td>"; //Insurance Type - end
							
							$strOtherRow .= "<td>"; //Insurance statue - start
							if(trim($strEBInfoVal) != ""){
								$strOtherRow .= $strEBInfoVal;	
							}
							else{
								$strOtherRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
							}
							$strOtherRow .= "</td>"; //Insurance statue - end
							
							$strOtherRow .= "<td>"; //Insurance Coverage Level - start
							if(trim($strEBCovLevelVal) != ""){
								$strOtherRow .= $strEBCovLevelVal;	
							}
							else{
								$strOtherRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
							}
							$strOtherRow .= "</td>"; //Insurance Coverage Level - end
							
							$strOtherRow .= "<td>"; //Insurance Service Type - start
							if(trim($strEBSerTypeVal) != ""){
								$strOtherRow .= $strEBSerTypeVal;
							}
							else{
								$str_TempEBSerTypeVal = $this->getServiceTypeCodeValueById($intEBSerTypeId);
								if($str_TempEBSerTypeVal!=''){
									$strOtherRow .= $str_TempEBSerTypeVal;
								}else{
									$strOtherRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}
							}							
							$strOtherRow .= "</td>"; //Insurance Service Type - end
							
							$strOtherRow .= "<td>"; //Insurance Plan Coverage Description - Start
							if(trim($strEBPlanCoverageDesc) != ""){
								$strOtherRow .= $strEBPlanCoverageDesc;
							}						
							$strOtherRow .= "</td>"; //Insurance Plan Coverage Description - end
							
							$strOtherRow .= "<td>"; //Insurance Amount - start
							$strOtherRow .= ($intEBMonetaryAmount) ? "$".$intEBMonetaryAmount : "";
							$strOtherRow .= "</td>"; //Insurance Amount - End
							
							$strOtherRow .= "<td>"; //Insurance Time Period - Start
							if(trim($strEBTimePeriodQuVal) != ""){
								$strOtherRow .= $strEBTimePeriodQuVal;	
							}
							else{
								$strOtherRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
							}
							$strOtherRow .= "</td>"; //Insurance Time Period - End
							
							$strOtherRow .= "<td>"; //Insurance Percent  - start
							$strOtherRow .= ($strEBPercent) ? (float)$strEBPercent * 100 : "";
							$strOtherRow .= "</td>"; //Insurance Percent  - end
							
							$strOtherRow .= "<td>"; //Insurance Quantity   - start
							$strOtherRow .= $intEBQuantity;
							if(trim($strEBQuantityQuVal) != ""){										
								$strOtherRow .= "<div>".$strEBQuantityQuVal."</div>";
							}
							else{
								$strOtherRow .= ((empty($strEBQuantityQuId) == false) ? "<div>".$strEBQuantityQuId."(Unknown) </div>" : "");
							}							
							$strOtherRow .= "</td>"; //Insurance Quantity   - end
							
							$strOtherRow .= "<td>"; //Insurance Authorization or Certification Indicator - start
							if(trim($strEBConditionRespVal) != ""){										
								$strOtherRow .= $strEBConditionRespVal;
							}
							else{
								$strOtherRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
							}							
							$strOtherRow .= "</td>"; //Insurance Authorization or Certification Indicator   - end
							
							$strOtherRow .= "<td>"; //Insurance In Plan Network Indicator - start
							if(trim($strEBPlanConditionRespVal) != ""){										
								$strOtherRow .= $strEBPlanConditionRespVal;
							}
							else{
								$strOtherRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
							}							
							$strOtherRow .= "</td>"; //Insurance In Plan Network Indicator - end
													
							if((empty($srtEBDTPFormat) == false) && ((empty($srtEBDTPDate) == false) || (empty($srtEBDTPDate1) == false) || (empty($srtEBDTPDate2) == false))){
								$strOtherRow .= "<td>"; //Insurance Date - start
								if($srtEBDTPFormat == "D8"){
									$intDOBYear = substr($srtEBDTPDate,0,4);
									$intDOBMon = substr($srtEBDTPDate,4,2);
									$intDOBDay = substr($srtEBDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strOtherRow .= $srtDTPDateNew;
									if(trim($srtEBDTPVal) != ""){										
										$strOtherRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strOtherRow .= ((empty($intEBDTPId) == false) ? "<div>".$intEBDTPId."(Unknown) </div>" : "");
									}	
								}
								elseif($srtEBDTPFormat == "RD8"){
									$intDOBYear = substr($srtEBDTPDate1,0,4);
									$intDOBMon = substr($srtEBDTPDate1,4,2);
									$intDOBDay = substr($srtEBDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtEBDTPDate2,0,4);
									$intDOBMon = substr($srtEBDTPDate2,4,2);
									$intDOBDay = substr($srtEBDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strOtherRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtEBDTPVal) != ""){
										$strOtherRow .= "<div>".$srtEBDTPVal."</div>";
									}
									else{
										$strOtherRow .= ((empty($intEBDTPId) == false) ? "<div>".$intEBDTPId."(Unknown) </div>" : "");									
									}
								}								
								$strOtherRow .= "</td>"; //Insurance Date - start
							}
							else{
								$strOtherRow .= "<td>"; //Insurance Date - start
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strOtherRow .= $srtDTPDateNew;
									if(trim($srtDTPVal) != ""){										
										$strOtherRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strOtherRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strOtherRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtDTPVal) != ""){
										$strOtherRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strOtherRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");									
									}
								}								
								$strOtherRow .= "</td>"; //Insurance Date - start
							}
							#Message from Information Source 
							$strOtherRow .= "<td>";						
								$strOtherRow .= $strMSGValue;
							$strOtherRow .= "</td>";
							$strMSGValue = "";						
							#Message from Information Source 
							$strOtherRow .= "</tr>";
							$intTempCounterOtinfo++;
					}
					
					$blDTPLoopSt = $blDTPDateSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
					$blEBConditionRespSt = $blEBPlanConditionRespSt = false;
					$intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
					$strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
					$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
					$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
					
					$strDTPLoopName = $strEBLoopName = "";
					$blDTPInternal = $blEBLoopInternal = false;
					$intEBLoopCo = 0;
					
					$blEBDTPDateSt = false;
					$intEBDTPId = 0;
					$srtEBDTPVal = $srtEBDTPFormat = $srtEBDTPDate = $srtEBDTPDate1 = $srtEBDTPDate2 = "";
					//$strMSGValue = "";
					//die('sssssss');
				}
				if($blDTPLoopSt == true){					
					if(($val["tag"] == "DateTimeQualifierID") && ($val["type"] == "complete") && ($intDTPId == 0)){
						$intDTPId = $val["value"];
					}
					elseif(($val["tag"] == "DateTimeQualifierIDVal") && ($val["type"] == "complete") && ($srtDTPVal == "")){
						$srtDTPVal = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriodFormatQualifier") && ($val["type"] == "complete") && ($srtDTPFormat == "")){
						$srtDTPFormat = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "open") && ($blDTPDateSt == false)){
						$blDTPDateSt = true;
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "close") && ($blDTPDateSt == true)){
						$blDTPDateSt = false;
					}					
					if($blDTPDateSt == true){
						if(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "D8") && ($val["value"] != "")){
							$srtDTPDate = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($val["value"] != "")){
							$srtDTPDate1 = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate2") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($val["value"] != "")){
							$srtDTPDate2 = $val["value"];
						}
					}
					//echo $intDTPId."--".$srtDTPVal."ss".$srtDTPFormat."hh".$srtDTPDate;
					//die('DTP');					
				}
				elseif($blEBLoopSt == true){
					//echo $intDTPLoopCo;
					//die('EB');		
					/*if($key == 197){
						echo $intDTPLoopCo;
						pre(var_dump($blEBLoopInternal));
						pre($strEBLoopName);
						die('ddddddddd');
					}*/			
					if(($blEBLoopInternal == false) && ($strEBLoopName == "")){
						$valTemp = $val["tag"];
						$strTemp = "";
						$strTemp = "EBLOOP_".$intDTPLoopCo."_";
						if((strpos($valTemp, $strTemp) !== false) && ($val["type"] == "open")){
							$blEBLoopInternal = true;
							$arrTemp = explode("-", $valTemp);
							$intEBLoopCo = $arrTemp[2];
							$strEBLoopName = $valTemp;
							//if($intDTPLoopCo == 2 && $key == 146){
								//die($strEBLoopName.'===='.$valTemp.'--'.$key);
							//}	
						}
											
					}
					if($blEBLoopInternal == true){
						if(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "open") && ($blEBInfoSt == false)){
							$blEBInfoSt = true;						
						}
						elseif(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "close") && ($blEBInfoSt == true)){
							$blEBInfoSt = false;
						}
						elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "open") && ($blEBCovLevelSt == false)){
							$blEBCovLevelSt = true;
						}
						elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "close") && ($blEBCovLevelSt == true)){
							$blEBCovLevelSt = false;
						}
						elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "open") && ($blEBSerTypeSt == false)){
							$blEBSerTypeSt = true;
						}
						elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "close") && ($blEBSerTypeSt == true)){
							$blEBSerTypeSt = false;
						}
						elseif(($val["tag"] == "PlanCoverageDescription") && ($val["type"] == "complete") && ($strEBPlanCoverageDesc == "")){
							$strEBPlanCoverageDesc = $val["value"];
						}
						elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "open") && ($blEBInsTypeSt == false)){
							$blEBInsTypeSt = true;
						}
						elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "close") && ($blEBInsTypeSt == true)){
							$blEBInsTypeSt = false;
						}
						elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "open") && ($blEBTimePeriodQuSt == false)){
							$blEBTimePeriodQuSt = true;
						}
						elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "close") && ($blEBTimePeriodQuSt == true)){
							$blEBTimePeriodQuSt = false;
						}
						elseif(($val["tag"] == "MonetaryAmount") && ($val["type"] == "complete")){
							$intEBMonetaryAmount = $val["value"];
						}
						elseif(($val["tag"] == "Percent") && ($val["type"] == "complete") && ($strEBPercent == "")){
							$strEBPercent = $val["value"];
						}
						elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "open") && ($blEBQuantityQuSt == false)){
							$blEBQuantityQuSt = true;
						}
						elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "close") && ($blEBQuantityQuSt == true)){
							$blEBQuantityQuSt = false;
						}
						elseif(($val["tag"] == "Quantity") && ($val["type"] == "complete") && ($intEBQuantity == 0)){
							$intEBQuantity = $val["value"];
						}
						elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "open") && ($blEBConditionRespSt == false)){
							$blEBConditionRespSt = true;
						}
						elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "close") && ($blEBConditionRespSt == true)){
							$blEBConditionRespSt = false;
						}
						elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "open") && ($blEBPlanConditionRespSt == false)){
							$blEBPlanConditionRespSt = true;
						}
						elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "close") && ($blEBPlanConditionRespSt == true)){
							$blEBPlanConditionRespSt = false;
						}
						
						if($blEBInfoSt == true){						
							if(($val["tag"] == "EligibilityOrBenefitInformationId") && ($val["type"] == "complete") && ($strEBInfoId == "")){
								$strEBInfoId = $val["value"];
							}
							elseif(($val["tag"] == "EligibilityOrBenefitInformationVal") && ($val["type"] == "complete") && ($strEBInfoVal == "")){
								$strEBInfoVal = $val["value"];
							}
						}
						elseif($blEBCovLevelSt == true){
							if(($val["tag"] == "CoverageLevelCodeId") && ($val["type"] == "complete") && ($strEBCovLevelId == "")){
								$strEBCovLevelId = $val["value"];
							}
							elseif(($val["tag"] == "CoverageLevelCodeVal") && ($val["type"] == "complete") && ($strEBCovLevelVal == "")){
								$strEBCovLevelVal = $val["value"];
							}
						}
						elseif($blEBSerTypeSt == true){
							if(($val["tag"] == "ServiceTypeCodeId") && ($val["type"] == "complete") && ($intEBSerTypeId == 0)){
								$intEBSerTypeId = $val["value"];
							}
							elseif(($val["tag"] == "ServiceTypeCodeVal") && ($val["type"] == "complete") && ($strEBSerTypeVal == "")){
								$strEBSerTypeVal = $val["value"];
							}
						}
						elseif($blEBInsTypeSt == true){
							if(($val["tag"] == "InsuranceTypeCodeId") && ($val["type"] == "complete") && ($strEBInsTypeId == "")){
								$strEBInsTypeId = $val["value"];
							}
							elseif(($val["tag"] == "InsuranceTypeCodeVal") && ($val["type"] == "complete") && ($strEBInsTypeVal == "")){
								$strEBInsTypeVal = $val["value"];
							}
						}												
						elseif($blEBTimePeriodQuSt == true){
							if(($val["tag"] == "TimePeriodQualifierId") && ($val["type"] == "complete") && ($intEBTimePeriodQuId == 0)){
								$intEBTimePeriodQuId = $val["value"];
							}
							elseif(($val["tag"] == "TimePeriodQualifierVal") && ($val["type"] == "complete") && ($strEBTimePeriodQuVal == "")){
								$strEBTimePeriodQuVal = $val["value"];
							}
						}
						elseif($blEBQuantityQuSt == true){
							if(($val["tag"] == "QuantityQualifierId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
								$strEBQuantityQuId = $val["value"];
							}
							elseif(($val["tag"] == "QuantityQualifierVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
								$strEBQuantityQuVal = $val["value"];
							}
						}
						elseif($blEBConditionRespSt == true){
							if(($val["tag"] == "ConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
								$strEBConditionRespId = $val["value"];
							}
							elseif(($val["tag"] == "ConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
								$strEBConditionRespVal = $val["value"];
							}
						}
						elseif($blEBPlanConditionRespSt == true){
							if(($val["tag"] == "PlanConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBPlanConditionRespId == "")){
								$strEBPlanConditionRespId = $val["value"];
							}
							elseif(($val["tag"] == "PlanConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBPlanConditionRespVal == "")){
								$strEBPlanConditionRespVal = $val["value"];
							}
						}						
					}
				}
			}
		}
		if($parseFor == 1){
			return array($strMAMBRow, $strDeductibleRow, $strCopayRow, $strOtherRow);
		}
		elseif($parseFor == 2){
			return array($strDeductibleRow, $strCopayRow, $strBenefitRow, $strVisionRow, $strPerCertRow);
		}
	}
	
    function getTotalAmt271Response($strXMLPath){
        if(file_exists($strXMLPath)){
            $arrXMLvalues = $arrPatCopay = array();
            $XML = file_get_contents($strXMLPath);			
            $arrXMLvalues = $this->objCommonFunction->XMLToArray($XML);
            //pre($arrXMLvalues,1);
            $blDTPLoopSt = $blDTPDateSt = $blEBLoopSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
            
            $blEBConditionRespSt = $blEBPlanConditionRespSt = false;
            
            $intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
            $srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
            $strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
            $strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
            $strMAMBRow = $strDeductibleRow = $strCopayRow = $strOtherRow = "";
            $intTempCounterD = $intTempCounterC = $intTempCounterB = $intTempCounterV = $intTempCounterPC = $intTempCounterEBinfo = $intTempCounterOtinfo = 2;
            
            $strDTPLoopName = $strEBLoopName = "";
            $blDTPInternal = $blEBLoopInternal = false;
            $intDTPLoopCo = $intEBLoopCo = 0;
            $strBenefitRow = $strVisionRow = $strPerCertRow = "";
            
            $intTotDeductibleAmt = $intTotCopayAmt = 0;
            
            foreach($arrXMLvalues as $key => $val){
                if(($val["tag"] == "DTPLOOPINFO") && ($val["type"] == "open") && ($blDTPLoopSt == false)){					
                    $blDTPLoopSt = true;					
                }
                elseif(($blDTPLoopSt == true) && ($blDTPInternal == false) && ($strDTPLoopName == "")){
                    $valTemp = $val["tag"];					
                    if(strpos($valTemp, "DTPLOOP_") !== false){
                        $arrTemp = explode("_", $valTemp);
                        $intDTPLoopCo = $arrTemp[1];
                        $strDTPLoopName = $valTemp;
                        $blDTPInternal = true;
                    }
                }
                elseif(($val["tag"] == $strDTPLoopName) && ($val["type"] == "close") && ($blDTPInternal == true)){
                    $strDTPLoopName = "";					
                    //$blDTPInternal = false;
                    //echo $key;
                    //pre($val,1);
                    //pre(current($arrXMLvalues));
                    //pre(next($arrXMLvalues));
                    //die('zzfff');
                }
                elseif((trim($val["tag"]) == "DTPLOOPINFO") && (trim($val["type"]) == "close") && ($blDTPLoopSt == true)){					
                    $blDTPLoopSt = $blDTPInternal = false;
                    //$intDTPLoopCo = 0;
                    //die('zzfff444');
                }
                elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "open") && ($blEBLoopSt == false)){
                    //die('qqq');
                    $blEBLoopSt = true;
                }
                elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "close") && ($blEBLoopSt == true)){
                    $blEBLoopSt = false;
                    $intDTPLoopCo = 0;
                }
                elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "complete")){
                    //die('lllll');
                    $blDTPLoopSt = $blDTPDateSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
                    $blEBConditionRespSt = $blEBPlanConditionRespSt = false;
                    $intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
                    $srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
                    $strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
                    $strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
                                                            
                    $strDTPLoopName = $strEBLoopName = "";
                    $blDTPInternal = $blEBLoopInternal = false;
                    $intEBLoopCo = 0;
                }				
                elseif(($val["tag"] == $strEBLoopName) && ($val["type"] == "close") && ($blEBLoopInternal == true)){
                    //echo $strEBLoopName;
                    //die('aaaaa');
                    /*if($strEBLoopName == "EBLOOP_2_0"){
                        die($strEBLoopName.'===='.$valTemp.'--'.$key);
                    }*/
                    $blDTPInternal = $blEBLoopInternal = false;
                    if((trim($strEBInfoId) == "C") || (trim($strEBInfoId) == "B")){
                        if(trim($strEBInfoId) == "C"){ //Deductible information
                            $intTotDeductibleAmt += (float)$intEBMonetaryAmount;
                        }
                        elseif((trim($strEBInfoId) == "B") && ((float)$intEBMonetaryAmount > 0)){ //Co-Payment information
							$patCopay = 0;
							$patCopay = (float)$intEBMonetaryAmount;
                            $intTotCopayAmt += $patCopay;
							$arrPatCopay[] = $patCopay;
                        }
                    }
                    
                    $blDTPLoopSt = $blDTPDateSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
                    $blEBConditionRespSt = $blEBPlanConditionRespSt = false;
                    $intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
                    $srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
                    $strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
                    $strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
                    
                    $strDTPLoopName = $strEBLoopName = "";
                    $blDTPInternal = $blEBLoopInternal = false;
                    $intEBLoopCo = 0;
                    //die('sssssss');
                }
                if($blDTPLoopSt == true){					
                    if(($val["tag"] == "DateTimeQualifierID") && ($val["type"] == "complete") && ($intDTPId == 0)){
                        $intDTPId = $val["value"];
                    }
                    elseif(($val["tag"] == "DateTimeQualifierIDVal") && ($val["type"] == "complete") && ($srtDTPVal == "")){
                        $srtDTPVal = $val["value"];
                    }
                    elseif(($val["tag"] == "DateTimePeriodFormatQualifier") && ($val["type"] == "complete") && ($srtDTPFormat == "")){
                        $srtDTPFormat = $val["value"];
                    }
                    elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "open") && ($blDTPDateSt == false)){
                        $blDTPDateSt = true;
                    }
                    elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "close") && ($blDTPDateSt == true)){
                        $blDTPDateSt = false;
                    }					
                    if($blDTPDateSt == true){
                        if(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "D8") && ($srtDTPDate == "")){
                            $srtDTPDate = $val["value"];
                        }
                        elseif(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($srtDTPDate1 == "")){
                            $srtDTPDate1 = $val["value"];
                        }
                        elseif(($val["tag"] == "DateTimePeriodDate2") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($srtDTPDate2 == "")){
                            $srtDTPDate2 = $val["value"];
                        }
                    }
                    //echo $intDTPId."--".$srtDTPVal."ss".$srtDTPFormat."hh".$srtDTPDate;
                    //die('DTP');					
                }
                elseif($blEBLoopSt == true){
                    //echo $intDTPLoopCo;
                    //die('EB');		
                    /*if($key == 197){
                        echo $intDTPLoopCo;
                        pre(var_dump($blEBLoopInternal));
                        pre($strEBLoopName);
                        die('ddddddddd');
                    }*/			
                    if(($blEBLoopInternal == false) && ($strEBLoopName == "")){
                        $valTemp = $val["tag"];
                        $strTemp = "";
                        $strTemp = "EBLOOP_".$intDTPLoopCo."_";
                        if((strpos($valTemp, $strTemp) !== false) && ($val["type"] == "open")){
                            $blEBLoopInternal = true;
                            $arrTemp = explode("-", $valTemp);
                            $intEBLoopCo = $arrTemp[2];
                            $strEBLoopName = $valTemp;
                            //if($intDTPLoopCo == 2 && $key == 146){
                                //die($strEBLoopName.'===='.$valTemp.'--'.$key);
                            //}	
                        }
                                            
                    }
                    if($blEBLoopInternal == true){
                        if(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "open") && ($blEBInfoSt == false)){
                            $blEBInfoSt = true;						
                        }
                        elseif(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "close") && ($blEBInfoSt == true)){
                            $blEBInfoSt = false;
                        }
                        elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "open") && ($blEBCovLevelSt == false)){
                            $blEBCovLevelSt = true;
                        }
                        elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "close") && ($blEBCovLevelSt == true)){
                            $blEBCovLevelSt = false;
                        }
                        elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "open") && ($blEBSerTypeSt == false)){
                            $blEBSerTypeSt = true;
                        }
                        elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "close") && ($blEBSerTypeSt == true)){
                            $blEBSerTypeSt = false;
                        }
                        elseif(($val["tag"] == "PlanCoverageDescription") && ($val["type"] == "complete") && ($strEBPlanCoverageDesc == "")){
                            $strEBPlanCoverageDesc = $val["value"];
                        }
                        elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "open") && ($blEBInsTypeSt == false)){
                            $blEBInsTypeSt = true;
                        }
                        elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "close") && ($blEBInsTypeSt == true)){
                            $blEBInsTypeSt = false;
                        }
                        elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "open") && ($blEBTimePeriodQuSt == false)){
                            $blEBTimePeriodQuSt = true;
                        }
                        elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "close") && ($blEBTimePeriodQuSt == true)){
                            $blEBTimePeriodQuSt = false;
                        }
                        elseif(($val["tag"] == "MonetaryAmount") && ($val["type"] == "complete")){
                            $intEBMonetaryAmount = $val["value"];
                        }
                        elseif(($val["tag"] == "Percent") && ($val["type"] == "complete") && ($strEBPercent == "")){
                            $strEBPercent = $val["value"];
                        }
                        elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "open") && ($blEBQuantityQuSt == false)){
                            $blEBQuantityQuSt = true;
                        }
                        elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "close") && ($blEBQuantityQuSt == true)){
                            $blEBQuantityQuSt = false;
                        }
                        elseif(($val["tag"] == "Quantity") && ($val["type"] == "complete") && ($intEBQuantity == 0)){
                            $intEBQuantity = $val["value"];
                        }
                        elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "open") && ($blEBConditionRespSt == false)){
                            $blEBConditionRespSt = true;
                        }
                        elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "close") && ($blEBConditionRespSt == true)){
                            $blEBConditionRespSt = false;
                        }
                        elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "open") && ($blEBPlanConditionRespSt == false)){
                            $blEBPlanConditionRespSt = true;
                        }
                        elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "close") && ($blEBPlanConditionRespSt == true)){
                            $blEBPlanConditionRespSt = false;
                        }
                        
                        if($blEBInfoSt == true){						
                            if(($val["tag"] == "EligibilityOrBenefitInformationId") && ($val["type"] == "complete") && ($strEBInfoId == "")){
                                $strEBInfoId = $val["value"];
                            }
                            elseif(($val["tag"] == "EligibilityOrBenefitInformationVal") && ($val["type"] == "complete") && ($strEBInfoVal == "")){
                                $strEBInfoVal = $val["value"];
                            }
                        }
                        elseif($blEBCovLevelSt == true){
                            if(($val["tag"] == "CoverageLevelCodeId") && ($val["type"] == "complete") && ($strEBCovLevelId == "")){
                                $strEBCovLevelId = $val["value"];
                            }
                            elseif(($val["tag"] == "CoverageLevelCodeVal") && ($val["type"] == "complete") && ($strEBCovLevelVal == "")){
                                $strEBCovLevelVal = $val["value"];
                            }
                        }
                        elseif($blEBSerTypeSt == true){
                            if(($val["tag"] == "ServiceTypeCodeId") && ($val["type"] == "complete") && ($intEBSerTypeId == 0)){
                                $intEBSerTypeId = $val["value"];
                            }
                            elseif(($val["tag"] == "ServiceTypeCodeVal") && ($val["type"] == "complete") && ($strEBSerTypeVal == "")){
                                $strEBSerTypeVal = $val["value"];
                            }
                        }
                        elseif($blEBInsTypeSt == true){
                            if(($val["tag"] == "InsuranceTypeCodeId") && ($val["type"] == "complete") && ($strEBInsTypeId == "")){
                                $strEBInsTypeId = $val["value"];
                            }
                            elseif(($val["tag"] == "InsuranceTypeCodeVal") && ($val["type"] == "complete") && ($strEBInsTypeVal == "")){
                                $strEBInsTypeVal = $val["value"];
                            }
                        }												
                        elseif($blEBTimePeriodQuSt == true){
                            if(($val["tag"] == "TimePeriodQualifierId") && ($val["type"] == "complete") && ($intEBTimePeriodQuId == 0)){
                                $intEBTimePeriodQuId = $val["value"];
                            }
                            elseif(($val["tag"] == "TimePeriodQualifierVal") && ($val["type"] == "complete") && ($strEBTimePeriodQuVal == "")){
                                $strEBTimePeriodQuVal = $val["value"];
                            }
                        }
                        elseif($blEBQuantityQuSt == true){
                            if(($val["tag"] == "QuantityQualifierId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){

                                $strEBQuantityQuId = $val["value"];
                            }
                            elseif(($val["tag"] == "QuantityQualifierVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
                                $strEBQuantityQuVal = $val["value"];
                            }
                        }
                        elseif($blEBConditionRespSt == true){
                            if(($val["tag"] == "ConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
                                $strEBConditionRespId = $val["value"];
                            }
                            elseif(($val["tag"] == "ConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
                                $strEBConditionRespVal = $val["value"];
                            }
                        }
                        elseif($blEBPlanConditionRespSt == true){
                            if(($val["tag"] == "PlanConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBPlanConditionRespId == "")){
                                $strEBPlanConditionRespId = $val["value"];
                            }
                            elseif(($val["tag"] == "PlanConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBPlanConditionRespVal == "")){
                                $strEBPlanConditionRespVal = $val["value"];
                            }
                        }						
                    }
                }
            }
        }
        $arrReturn = array($intTotDeductibleAmt, $intTotCopayAmt, $arrPatCopay);
		return $arrReturn;
    }

	function getAmt271Response($strXMLPath, $arrDB){
		if(file_exists($strXMLPath)){
			$arrXMLvalues = array();
			$XML = file_get_contents($strXMLPath);			
			$arrXMLvalues = $this->objCommonFunction->XMLToArray($XML);
			//pre($arrXMLvalues,1);
			$blDTPLoopSt = $blDTPDateSt = $blEBLoopSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
			$blEBConditionRespSt = $blEBPlanConditionRespSt = false;
			$intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
			$srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
			$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
			$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
			$strMAMBRow = $strDeductibleRow = $strCopayRow = $strCoIns = "";
			$intTempCounterA = $intTempCounterB = $intTempCounterD = $intTempCounterC = $intTempCounterEBinfo = $intTempCounterOtinfo = 2;
			
			$strDTPLoopName = $strEBLoopName = "";
			$blDTPInternal = $blEBLoopInternal = false;
			$intDTPLoopCo = $intEBLoopCo = 0;
			$intChkCounterA = $intChkCounterB = $intChkCounterD = 1;
			
			foreach($arrXMLvalues as $key => $val){
				if(($val["tag"] == "DTPLOOPINFO") && ($val["type"] == "open") && ($blDTPLoopSt == false)){					
					$blDTPLoopSt = true;					
				}
				elseif(($blDTPLoopSt == true) && ($blDTPInternal == false) && ($strDTPLoopName == "")){
					$valTemp = $val["tag"];					
					if(strpos($valTemp, "DTPLOOP_") !== false){
						$arrTemp = explode("_", $valTemp);
						$intDTPLoopCo = $arrTemp[1];
						$strDTPLoopName = $valTemp;
						$blDTPInternal = true;
					}
				}
				elseif(($val["tag"] == $strDTPLoopName) && ($val["type"] == "close") && ($blDTPInternal == true)){
					$strDTPLoopName = "";					
					//$blDTPInternal = false;
					//echo $key;
					//pre($val,1);
					//pre(current($arrXMLvalues));
					//pre(next($arrXMLvalues));
					//die('zzfff');
				}
				elseif((trim($val["tag"]) == "DTPLOOPINFO") && (trim($val["type"]) == "close") && ($blDTPLoopSt == true)){					
					$blDTPLoopSt = $blDTPInternal = false;
					//$intDTPLoopCo = 0;
					//die('zzfff444');
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "open") && ($blEBLoopSt == false)){
					//die('qqq');
					$blEBLoopSt = true;
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "close") && ($blEBLoopSt == true)){
					$blEBLoopSt = false;
					$intDTPLoopCo = 0;
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "complete")){
					//die('lllll');
					$blDTPLoopSt = $blDTPDateSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
					$blEBConditionRespSt = $blEBPlanConditionRespSt = false;
					$intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
					$srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
					$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
					$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
															
					$strDTPLoopName = $strEBLoopName = "";
					$blDTPInternal = $blEBLoopInternal = false;
					$intEBLoopCo = 0;
				}				
				elseif(($val["tag"] == $strEBLoopName) && ($val["type"] == "close") && ($blEBLoopInternal == true)){
					//echo $strEBLoopName;
					//die('aaaaa');
					/*if($strEBLoopName == "EBLOOP_2_0"){
						die($strEBLoopName.'===='.$valTemp.'--'.$key);
					}*/
					
					$blDTPInternal = $blEBLoopInternal = false;
					if((trim($strEBInfoId) == "A") || (trim($strEBInfoId) == "B") || (trim($strEBInfoId) == "C")){
						if(trim($strEBInfoId) == "A"){ //Co-Insurance
							$class = ($intTempCounterA % 2) == 0 ? "bgcolor" : "";				
							$strCoIns .= "<tr class=\"$class\">";
							$tempCp = $tempCoIns = "";
							$tempCp = ($intEBMonetaryAmount) ? (float)$intEBMonetaryAmount : "";
							$tempCoIns = ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strValue = $strEBInsTypeId."-".$intEBSerTypeId."-".$strEBCovLevelId."-".$strEBPlanCoverageDesc."-".$tempCp."-".$intEBTimePeriodQuId."-".$tempCoIns."-".$intEBQuantity."-".$strEBQuantityQuId."-".$strEBConditionRespId."-".$strEBPlanConditionRespId;
								$chkChecked = "";
								if($strValue == $arrDB["dbCoins"]){
									$chkChecked = "checked";
								}
								$strCoIns .= "<td>";
								$strCoIns .= "<input type=\"checkbox\" id=\"cbkCoIns$intChkCounterA\" name=\"cbkCoIns\" $chkChecked value=\"".$strValue."\" onclick=\"makeOneCbk(this);\">";
								$strCoIns .= "</td>";
								
								/*$strCoIns .= "<td nowrap>"; //Insurance Type - start
								if(trim($strEBInsTypeVal) != ""){
									$strCoIns .= $strEBInsTypeVal;	
								}
								else{
									$strCoIns .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
								}
								$strCoIns .= "</td>"; //Insurance Type - end
								*/
								/*$strCoIns .= "<td>"; //Insurance statue - start
								if(trim($strEBInfoVal) != ""){
									$strCoIns .= $strEBInfoVal;	
								}
								else{
									$strCoIns .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
								}
								$strCoIns .= "</td>"; //Insurance statue - end
								*/
								$strCoIns .= "<td>"; //Insurance Service Type - start
								if(trim($strEBSerTypeVal) != ""){
									$strCoIns .= $strEBSerTypeVal;
								}
								else{
									$strCoIns .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}							
								$strCoIns .= "</td>"; //Insurance Service Type - end
								
								$strCoIns .= "<td>"; //Insurance Coverage Level - start
								if(trim($strEBCovLevelVal) != ""){
									$strCoIns .= $strEBCovLevelVal;	
								}
								else{
									$strCoIns .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								}
								$strCoIns .= "</td>"; //Insurance Coverage Level - end
								
								$strCoIns .= "<td>"; //Insurance Plan Coverage Description - Start
								if(trim($strEBPlanCoverageDesc) != ""){
									$strCoIns .= $strEBPlanCoverageDesc;
								}						
								$strCoIns .= "</td>"; //Insurance Plan Coverage Description - end
								
								$strCoIns .= "<td>"; //Insurance Amount - start
								$strCoIns .= ($intEBMonetaryAmount) ? "$".(float)$intEBMonetaryAmount : "";
								$strCoIns .= "</td>"; //Insurance Amount - End
								
								$strCoIns .= "<td>"; //Insurance Time Period - Start
								if(trim($strEBTimePeriodQuVal) != ""){
									$strCoIns .= $strEBTimePeriodQuVal;	
								}
								else{
									$strCoIns .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
								}
								$strCoIns .= "</td>"; //Insurance Time Period - End
								
								$strCoIns .= "<td>"; //Insurance Percent  - start								
								$strCoIns .= ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strCoIns .= "</td>"; //Insurance Percent  - end
								
								$strCoIns .= "<td>"; //Insurance Quantity   - start
								$strCoIns .= $intEBQuantity;
								if(trim($strEBQuantityQuVal) != ""){										
									$strCoIns .= "<div>".$strEBQuantityQuVal."</div>";
								}
								else{
									$strCoIns .= ((empty($strEBQuantityQuId) == false) ? "<div>".$strEBQuantityQuId."(Unknown) </div>" : "");
								}							
								$strCoIns .= "</td>"; //Insurance Quantity   - end
								
								$strCoIns .= "<td>"; //Insurance Authorization or Certification Indicator - start
								if(trim($strEBConditionRespVal) != ""){										
									$strCoIns .= $strEBConditionRespVal;
								}
								else{
									$strCoIns .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
								}							
								$strCoIns .= "</td>"; //Insurance Authorization or Certification Indicator   - end
								
								$strCoIns .= "<td>"; //Insurance In Plan Network Indicator - start
								if(trim($strEBPlanConditionRespVal) != ""){										
									$strCoIns .= $strEBPlanConditionRespVal;
								}
								else{
									$strCoIns .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
								}							
								$strCoIns .= "</td>"; //Insurance In Plan Network Indicator - end
								
								$strCoIns .= "<td>"; //Insurance Date - start
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCoIns .= $srtDTPDateNew;
									if(trim($srtDTPVal) != ""){										
										$strCoIns .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strCoIns .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCoIns .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtDTPVal) != ""){
										$strCoIns .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strCoIns .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");									
									}
								}								
								$strCoIns .= "</td>"; //Insurance Date - start
							$strCoIns .= "</tr>";
							$intTempCounterA++;
							$intChkCounterA++;
						}
						elseif(trim($strEBInfoId) == "B"){ //Co-Payment information
							$class = ($intTempCounterB % 2) == 0 ? "bgcolor" : "";
							$strCopayRow .= "<tr class=\"$class\">";
								$tempCp = $tempCoIns = "";
								$tempCp = ($intEBMonetaryAmount) ? (float)$intEBMonetaryAmount : "";
								$tempCoIns = ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strValue = $strEBInsTypeId."-".$intEBSerTypeId."-".$strEBCovLevelId."-".$strEBPlanCoverageDesc."-".$tempCp."-".$intEBTimePeriodQuId."-".$tempCoIns."-".$intEBQuantity."-".$strEBQuantityQuId."-".$strEBConditionRespId."-".$strEBPlanConditionRespId;
								$chkChecked = "";
								if($strValue == $arrDB["dbCopay"]){
									$chkChecked = "checked";
								}
								$strCopayRow .= "<td>";
								$strCopayRow .= "<input type=\"checkbox\" id=\"cbkCoPay$intChkCounterB\" name=\"cbkCoPay\" $chkChecked value=\"".$strValue."\" onclick=\"makeOneCbk(this);\">";
								$strCopayRow .= "</td>";
								
								/*$strCopayRow .= "<td nowrap>"; //Insurance Type - start
								if(trim($strEBInsTypeVal) != ""){
									$strCopayRow .= $strEBInsTypeVal;	
								}
								else{
									$strCopayRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
								}
								$strCopayRow .= "</td>"; //Insurance Type - end
								*/
								/*$strCopayRow .= "<td>"; //Insurance statue - start
								if(trim($strEBInfoVal) != ""){
									$strCopayRow .= $strEBInfoVal;	
								}
								else{
									$strCopayRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
								}
								$strCopayRow .= "</td>"; //Insurance statue - end
								*/
								$strCopayRow .= "<td>"; //Insurance Service Type - start
								if(trim($strEBSerTypeVal) != ""){
									$strCopayRow .= $strEBSerTypeVal;
								}
								else{
									$strCopayRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}							
								$strCopayRow .= "</td>"; //Insurance Service Type - end
								
								$strCopayRow .= "<td>"; //Insurance Coverage Level - start
								if(trim($strEBCovLevelVal) != ""){
									$strCopayRow .= $strEBCovLevelVal;	
								}
								else{
									$strCopayRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								}
								$strCopayRow .= "</td>"; //Insurance Coverage Level - end
								
								$strCopayRow .= "<td>"; //Insurance Plan Coverage Description - Start
								if(trim($strEBPlanCoverageDesc) != ""){
									$strCopayRow .= $strEBPlanCoverageDesc;
								}						
								$strCopayRow .= "</td>"; //Insurance Plan Coverage Description - end
								
								$strCopayRow .= "<td>"; //Insurance Amount - start								
								$strCopayRow .= ($intEBMonetaryAmount) ? "$".(float)$intEBMonetaryAmount : "";
								$strCopayRow .= "</td>"; //Insurance Amount - End
								
								$strCopayRow .= "<td>"; //Insurance Time Period - Start
								if(trim($strEBTimePeriodQuVal) != ""){
									$strCopayRow .= $strEBTimePeriodQuVal;	
								}
								else{
									$strCopayRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
								}
								$strCopayRow .= "</td>"; //Insurance Time Period - End
								
								$strCopayRow .= "<td>"; //Insurance Percent  - start								
								$strCopayRow .= ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strCopayRow .= "</td>"; //Insurance Percent  - end
								
								$strCopayRow .= "<td>"; //Insurance Quantity   - start
								$strCopayRow .= $intEBQuantity;
								if(trim($strEBQuantityQuVal) != ""){										
									$strCopayRow .= "<div>".$strEBQuantityQuVal."</div>";
								}
								else{
									$strCopayRow .= ((empty($strEBQuantityQuId) == false) ? "<div>".$strEBQuantityQuId."(Unknown) </div>" : "");
								}							
								$strCopayRow .= "</td>"; //Insurance Quantity   - end
								
								$strCopayRow .= "<td>"; //Insurance Authorization or Certification Indicator - start
								if(trim($strEBConditionRespVal) != ""){										
									$strCopayRow .= $strEBConditionRespVal;
								}
								else{
									$strCopayRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
								}							
								$strCopayRow .= "</td>"; //Insurance Authorization or Certification Indicator   - end
								
								$strCopayRow .= "<td>"; //Insurance In Plan Network Indicator - start
								if(trim($strEBPlanConditionRespVal) != ""){										
									$strCopayRow .= $strEBPlanConditionRespVal;
								}
								else{
									$strCopayRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
								}							
								$strCopayRow .= "</td>"; //Insurance In Plan Network Indicator - end
								
								$strCopayRow .= "<td>"; //Insurance Date - start
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCopayRow .= $srtDTPDateNew;
									if(trim($srtDTPVal) != ""){										
										$strCopayRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strCopayRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCopayRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtDTPVal) != ""){
										$strCopayRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strCopayRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");									
									}
								}								
								$strCopayRow .= "</td>"; //Insurance Date - start
							$strCopayRow .= "</tr>";
							$intTempCounterB++;
							$intChkCounterB++;
						}
						else if(trim($strEBInfoId) == "C"){ //Deductible information
							$class = ($intTempCounterC % 2) == 0 ? "bgcolor" : "";
							$strDeductibleRow .= "<tr class=\"$class\">";
								$tempCp = $tempCoIns = "";
								$tempCp = ($intEBMonetaryAmount) ? (float)$intEBMonetaryAmount : "";
								$tempCoIns = ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strValue = $strEBInsTypeId."-".$intEBSerTypeId."-".$strEBCovLevelId."-".$strEBPlanCoverageDesc."-".$tempCp."-".$intEBTimePeriodQuId."-".$tempCoIns."-".$intEBQuantity."-".$strEBQuantityQuId."-".$strEBConditionRespId."-".$strEBPlanConditionRespId;
								$chkChecked = "";
								if($strValue == $arrDB["dbDeductible"]){
									$chkChecked = "checked";
								}
								$strDeductibleRow .= "<td>";
								$strDeductibleRow .= "<input type=\"checkbox\" id=\"cbkDeductible$intChkCounterD\" name=\"cbkDeductible\" $chkChecked value=\"".$strValue."\" onclick=\"makeOneCbk(this);\">";
								$strDeductibleRow .= "</td>";
								
								/*$strDeductibleRow .= "<td nowrap>"; //Insurance Type - start
								if(trim($strEBInsTypeVal) != ""){
									$strDeductibleRow .= $strEBInsTypeVal;	
								}
								else{
									$strDeductibleRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
								}
								$strDeductibleRow .= "</td>"; //Insurance Type - end
								*/
								/*$strDeductibleRow .= "<td>"; //Insurance statue - start
								if(trim($strEBInfoVal) != ""){
									$strDeductibleRow .= $strEBInfoVal;	
								}
								else{
									$strDeductibleRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
								}
								$strDeductibleRow .= "</td>"; //Insurance statue - end
								*/
								$strDeductibleRow .= "<td>"; //Insurance Service Type - start
								if(trim($strEBSerTypeVal) != ""){
									$strDeductibleRow .= $strEBSerTypeVal;
								}
								else{
									$strDeductibleRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}							
								$strDeductibleRow .= "</td>"; //Insurance Service Type - end
								
								$strDeductibleRow .= "<td>"; //Insurance Coverage Level - start
								if(trim($strEBCovLevelVal) != ""){
									$strDeductibleRow .= $strEBCovLevelVal;	
								}
								else{
									$strDeductibleRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								}
								$strDeductibleRow .= "</td>"; //Insurance Coverage Level - end
								
								$strDeductibleRow .= "<td>"; //Insurance Plan Coverage Description - Start
								if(trim($strEBPlanCoverageDesc) != ""){
									$strDeductibleRow .= $strEBPlanCoverageDesc;
								}						
								$strDeductibleRow .= "</td>"; //Insurance Plan Coverage Description - end
								
								$strDeductibleRow .= "<td>"; //Insurance Amount - start
								$strDeductibleRow .= ($intEBMonetaryAmount) ? "$".(float)$intEBMonetaryAmount : "";
								$strDeductibleRow .= "</td>"; //Insurance Amount - End
								
								$strDeductibleRow .= "<td>"; //Insurance Time Period - Start
								if(trim($strEBTimePeriodQuVal) != ""){
									$strDeductibleRow .= $strEBTimePeriodQuVal;	
								}
								else{
									$strDeductibleRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
								}
								$strDeductibleRow .= "</td>"; //Insurance Time Period - End
								
								$strDeductibleRow .= "<td>"; //Insurance Percent  - start								
								$strDeductibleRow .= ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strDeductibleRow .= "</td>"; //Insurance Percent  - end
								
								$strDeductibleRow .= "<td>"; //Insurance Quantity   - start
								$strDeductibleRow .= $intEBQuantity;
								if(trim($strEBQuantityQuVal) != ""){										
									$strDeductibleRow .= "<div>".$strEBQuantityQuVal."</div>";
								}
								else{
									$strDeductibleRow .= ((empty($strEBQuantityQuId) == false) ? "<div>".$strEBQuantityQuId."(Unknown) </div>" : "");
								}							
								$strDeductibleRow .= "</td>"; //Insurance Quantity   - end
								
								$strDeductibleRow .= "<td>"; //Insurance Authorization or Certification Indicator - start
								if(trim($strEBConditionRespVal) != ""){										
									$strDeductibleRow .= $strEBConditionRespVal;
								}
								else{
									$strDeductibleRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
								}							
								$strDeductibleRow .= "</td>"; //Insurance Authorization or Certification Indicator   - end
								
								$strDeductibleRow .= "<td>"; //Insurance In Plan Network Indicator - start
								if(trim($strEBPlanConditionRespVal) != ""){										
									$strDeductibleRow .= $strEBPlanConditionRespVal;
								}
								else{
									$strDeductibleRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
								}							
								$strDeductibleRow .= "</td>"; //Insurance In Plan Network Indicator - end
								//echo $srtDTPFormat."--".$srtDTPDate1."--".$srtDTPDate2;
								//die;
								$strDeductibleRow .= "<td>"; //Insurance Date - start
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strDeductibleRow .= $srtDTPDateNew;
									if(trim($srtDTPVal) != ""){										
										$strDeductibleRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strDeductibleRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strDeductibleRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtDTPVal) != ""){
										$strDeductibleRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strDeductibleRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");									
									}
								}								
								$strDeductibleRow .= "</td>"; //Insurance Date - start
							$strDeductibleRow .= "</tr>";
							$intTempCounterC++;
							$intChkCounterD++;
						}
						
					}
					
					$blDTPLoopSt = $blDTPDateSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
					$blEBConditionRespSt = $blEBPlanConditionRespSt = false;
					$intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
					$strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
					$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
					$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
					
					$strDTPLoopName = $strEBLoopName = "";
					$blDTPInternal = $blEBLoopInternal = false;
					$intEBLoopCo = 0;
					//die('sssssss');
				}
				if($blDTPLoopSt == true){					
					if(($val["tag"] == "DateTimeQualifierID") && ($val["type"] == "complete") && ($intDTPId == 0)){
						$intDTPId = $val["value"];
					}
					elseif(($val["tag"] == "DateTimeQualifierIDVal") && ($val["type"] == "complete") && ($val["value"] != "")){
						$srtDTPVal = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriodFormatQualifier") && ($val["type"] == "complete") && ($srtDTPFormat == "")){
						$srtDTPFormat = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "open") && ($blDTPDateSt == false)){
						$blDTPDateSt = true;
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "close") && ($blDTPDateSt == true)){
						$blDTPDateSt = false;
					}					
					if($blDTPDateSt == true){
						if(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "D8") && ($val["value"] != "")){
							$srtDTPDate = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($val["value"] != "")){
							$srtDTPDate1 = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate2") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($val["value"] != "")){
							$srtDTPDate2 = $val["value"];
						}
					}
					//echo $intDTPId."--".$srtDTPVal."ss".$srtDTPFormat."hh".$srtDTPDate;
					//die('DTP');					
				}
				elseif($blEBLoopSt == true){
					//echo $intDTPLoopCo;
					//die('EB');		
					/*if($key == 197){
						echo $intDTPLoopCo;
						pre(var_dump($blEBLoopInternal));
						pre($strEBLoopName);
						die('ddddddddd');
					}*/			
					if(($blEBLoopInternal == false) && ($strEBLoopName == "")){
						$valTemp = $val["tag"];
						$strTemp = "";
						$strTemp = "EBLOOP_".$intDTPLoopCo."_";
						if((strpos($valTemp, $strTemp) !== false) && ($val["type"] == "open")){
							$blEBLoopInternal = true;
							$arrTemp = explode("-", $valTemp);
							$intEBLoopCo = $arrTemp[2];
							$strEBLoopName = $valTemp;
							if($intDTPLoopCo == 2 && $key == 146){
								//die($strEBLoopName.'===='.$valTemp.'--'.$key);
							}	
						}
											
					}
					if($blEBLoopInternal == true){
						if(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "open") && ($blEBInfoSt == false)){
							$blEBInfoSt = true;						
						}
						elseif(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "close") && ($blEBInfoSt == true)){
							$blEBInfoSt = false;
						}
						elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "open") && ($blEBCovLevelSt == false)){
							$blEBCovLevelSt = true;
						}
						elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "close") && ($blEBCovLevelSt == true)){
							$blEBCovLevelSt = false;
						}
						elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "open") && ($blEBSerTypeSt == false)){
							$blEBSerTypeSt = true;
						}
						elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "close") && ($blEBSerTypeSt == true)){
							$blEBSerTypeSt = false;
						}
						elseif(($val["tag"] == "PlanCoverageDescription") && ($val["type"] == "complete") && ($strEBPlanCoverageDesc == "")){
							$strEBPlanCoverageDesc = $val["value"];
						}
						elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "open") && ($blEBInsTypeSt == false)){
							$blEBInsTypeSt = true;
						}
						elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "close") && ($blEBInsTypeSt == true)){
							$blEBInsTypeSt = false;
						}
						elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "open") && ($blEBTimePeriodQuSt == false)){
							$blEBTimePeriodQuSt = true;
						}
						elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "close") && ($blEBTimePeriodQuSt == true)){
							$blEBTimePeriodQuSt = false;
						}
						elseif(($val["tag"] == "MonetaryAmount") && ($val["type"] == "complete")){
							$intEBMonetaryAmount = $val["value"];
						}
						elseif(($val["tag"] == "Percent") && ($val["type"] == "complete") && ($strEBPercent == "")){
							$strEBPercent = $val["value"];
						}
						elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "open") && ($blEBQuantityQuSt == false)){
							$blEBQuantityQuSt = true;
						}
						elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "close") && ($blEBQuantityQuSt == true)){
							$blEBQuantityQuSt = false;
						}
						elseif(($val["tag"] == "Quantity") && ($val["type"] == "complete") && ($intEBQuantity == 0)){
							$intEBQuantity = $val["value"];
						}
						elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "open") && ($blEBConditionRespSt == false)){
							$blEBConditionRespSt = true;
						}
						elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "close") && ($blEBConditionRespSt == true)){
							$blEBConditionRespSt = false;
						}
						elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "open") && ($blEBPlanConditionRespSt == false)){
							$blEBPlanConditionRespSt = true;
						}
						elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "close") && ($blEBPlanConditionRespSt == true)){
							$blEBPlanConditionRespSt = false;
						}
						
						if($blEBInfoSt == true){						
							if(($val["tag"] == "EligibilityOrBenefitInformationId") && ($val["type"] == "complete") && ($strEBInfoId == "")){
								$strEBInfoId = $val["value"];
							}
							elseif(($val["tag"] == "EligibilityOrBenefitInformationVal") && ($val["type"] == "complete") && ($strEBInfoVal == "")){
								$strEBInfoVal = $val["value"];
							}
						}
						elseif($blEBCovLevelSt == true){
							if(($val["tag"] == "CoverageLevelCodeId") && ($val["type"] == "complete") && ($strEBCovLevelId == "")){
								$strEBCovLevelId = $val["value"];
							}
							elseif(($val["tag"] == "CoverageLevelCodeVal") && ($val["type"] == "complete") && ($strEBCovLevelVal == "")){
								$strEBCovLevelVal = $val["value"];
							}
						}
						elseif($blEBSerTypeSt == true){
							if(($val["tag"] == "ServiceTypeCodeId") && ($val["type"] == "complete") && ($intEBSerTypeId == 0)){
								$intEBSerTypeId = $val["value"];
							}
							elseif(($val["tag"] == "ServiceTypeCodeVal") && ($val["type"] == "complete") && ($strEBSerTypeVal == "")){
								$strEBSerTypeVal = $val["value"];
							}
						}
						elseif($blEBInsTypeSt == true){
							if(($val["tag"] == "InsuranceTypeCodeId") && ($val["type"] == "complete") && ($strEBInsTypeId == "")){
								$strEBInsTypeId = $val["value"];
							}
							elseif(($val["tag"] == "InsuranceTypeCodeVal") && ($val["type"] == "complete") && ($strEBInsTypeVal == "")){
								$strEBInsTypeVal = $val["value"];
							}
						}												
						elseif($blEBTimePeriodQuSt == true){
							if(($val["tag"] == "TimePeriodQualifierId") && ($val["type"] == "complete") && ($intEBTimePeriodQuId == 0)){
								$intEBTimePeriodQuId = $val["value"];
							}
							elseif(($val["tag"] == "TimePeriodQualifierVal") && ($val["type"] == "complete") && ($strEBTimePeriodQuVal == "")){
								$strEBTimePeriodQuVal = $val["value"];
							}
						}
						elseif($blEBQuantityQuSt == true){
							if(($val["tag"] == "QuantityQualifierId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
								$strEBQuantityQuId = $val["value"];
							}
							elseif(($val["tag"] == "QuantityQualifierVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
								$strEBQuantityQuVal = $val["value"];
							}
						}
						elseif($blEBConditionRespSt == true){
							if(($val["tag"] == "ConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
								$strEBConditionRespId = $val["value"];
							}
							elseif(($val["tag"] == "ConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
								$strEBConditionRespVal = $val["value"];
							}
						}
						elseif($blEBPlanConditionRespSt == true){
							if(($val["tag"] == "PlanConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBPlanConditionRespId == "")){
								$strEBPlanConditionRespId = $val["value"];
							}
							elseif(($val["tag"] == "PlanConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBPlanConditionRespVal == "")){
								$strEBPlanConditionRespVal = $val["value"];
							}
						}						
					}
				}
			}
		}
		return array($strDeductibleRow, $strCopayRow, $strCoIns);
	}

	function parseXMLVS($strXMLPath, $parseFor = 1){		
		if(file_exists($strXMLPath)){
			$arrXMLvalues = array();
			$arrEBInfo = array(1,2,3,4,5,6,7,8);
			$XML = file_get_contents($strXMLPath);			
			$arrXMLvalues = $this->objCommonFunction->XMLToArray($XML);
			//pre($arrXMLvalues,1);
			$blDTPLoopSt = $blDTPDateSt = $blEBLoopSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
			$blEBConditionRespSt = $blEBPlanConditionRespSt = $blDTPInternal = false;
			$intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
			$srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
			$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
			$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
			$strMAMBRow = $strDeductibleRow = $strCopayRow = $strOtherRow = "";
			$intTempCounterD = $intTempCounterC = $intTempCounterB = $intTempCounterV = $intTempCounterPC = $intTempCounterEBinfo = $intTempCounterOtinfo = 2;
			$strBenefitRow = $strVisionRow = $strPerCertRow = "";
			foreach($arrXMLvalues as $key => $val){				
				if(($val["tag"] == "DTPLOOPINFO") && ($val["type"] == "open") && ($blDTPLoopSt == false)){
					$blDTPLoopSt = true;
				}
				elseif(($val["tag"] == "DTPLOOPINFO") && ($val["type"] == "close") && ($blDTPLoopSt == true)){
					$blDTPLoopSt = false;
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "open") && ($blEBLoopSt == false)){
					$blEBLoopSt = true;
				}
				elseif(($blDTPLoopSt == true) && ($blDTPInternal == false)){
					$valTemp = $val["tag"];
					if(strpos($valTemp, "DTPLOOP") !== false){
						$arrTemp = explode("-", $valTemp);
						$intDTPLoopCo = $arrTemp[1];
					}
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "close") && ($blEBLoopSt == true)){					
					if(in_array((int)trim($strEBInfoId), $arrEBInfo) == true){
						//$strMAMBRow .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";		
						$class = ($intTempCounterEBinfo % 2) == 0 ? "bgcolor" : "";				
							$strMAMBRow .= "<tr class=\"$class\">";
								$strMAMBRow .= "<td>";
								if(trim($strEBInsTypeVal) != ""){
									$strMAMBRow .= $strEBInsTypeVal;
									$strMAMBRow .= "<div>";
									if(trim($strEBSerTypeVal) != ""){
										$strMAMBRow .= $strEBSerTypeVal;
									}
									else{
										$strMAMBRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId : "");
									}
									$strMAMBRow .= "</div>";
								}
								else{
									$strMAMBRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
									if(is_string($intEBSerTypeId) == true){
										$arrTempServiceCode = array();
										$arrTempServiceCode = explode("^", $intEBSerTypeId);
										foreach($arrTempServiceCode as $intTempServiceCodeKey => $strTempServiceCodeVal){
											$orival = $refineVal = "";
											$orival = $this->objCoreLang->get_vocabulary("vision_share_271", "Service_Type_Code", $strTempServiceCodeVal);
											$refineVal = ((is_string($orival) && empty($orival) == false) ? $orival : "");
											if(empty($refineVal) == false){
												$strEBSerTypeVal .= $refineVal."<br>";
											}
										}
									}
									$strMAMBRow .= "<div>";
									if(trim($strEBSerTypeVal) != ""){
										$strMAMBRow .= $strEBSerTypeVal;
									}
									else{
										$strMAMBRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
									}
									$strMAMBRow .= "</div>";
								}
								$strMAMBRow .= "</td>";
								$strMAMBRow .= "<td>";
								if(trim($strEBInfoVal) != ""){
									$strMAMBRow .= $strEBInfoVal;	
								}
								else{
									$strMAMBRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
								}
								$strMAMBRow .= "</td>";
								$strMAMBRow .= "<td>";
								if(trim($strEBCovLevelVal) != ""){
									$strMAMBRow .= $strEBCovLevelVal;	
								}
								else{
									$strMAMBRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								}
								$strMAMBRow .= "</td>";
								$strMAMBRow .= "<td>";
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDate = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strMAMBRow .= $srtDTPDate;
									if(trim($srtDTPVal) != ""){										
										$strMAMBRow .= "<div><i>".$srtDTPVal."</i></div>";
									}
									else{
										$strMAMBRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1 = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2 = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strMAMBRow .= $srtDTPDate1." - ".$srtDTPDate2;	
									if(trim($srtDTPVal) != ""){
										$strMAMBRow .= "<div><i>".$srtDTPVal."</i></div>";
									}
									else{
										$strMAMBRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
									}
								}								
								$strMAMBRow .= "</td>";
								
								$strMAMBRow .= "<td>";
								$strMAMBRow .= $strEBPlanCoverageDesc;
								$strMAMBRow .= "</td>";
								#### Message from Information Source 
								$strMAMBRow .= "<td>";						
									$strMAMBRow .= $strMSGValue;
								$strMAMBRow .= "</td>";							
								####
							$strMAMBRow .= "</tr>";
							$intTempCounterEBinfo++;
						//$strMAMBRow .= "</table>";
					}
					elseif((trim($strEBInfoId) == "C") || (trim($strEBInfoId) == "B") || (trim($strEBInfoId) == "D")){
						if(trim($strEBInfoId) == "C"){  //Deductible information
							$class = ($intTempCounterC % 2) == 0 ? "bgcolor" : "";
							$strDeductibleRow .= "<tr class=\"$class\">";
							$strDeductibleRow .= "<td>";
							if(trim($strEBInsTypeVal) != ""){
								$strDeductibleRow .= $strEBInsTypeVal;	
								$strDeductibleRow .= "<div><i>";
								if(trim($strEBSerTypeVal) != ""){
									$strDeductibleRow .= $strEBSerTypeVal;
								}
								else{
									$strDeductibleRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}
								$strDeductibleRow .= "</i></div>";
							}
							else{
								$strDeductibleRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
								if(is_string($intEBSerTypeId) == true){
									$arrTempServiceCode = array();
									$arrTempServiceCode = explode("^", $intEBSerTypeId);
									foreach($arrTempServiceCode as $intTempServiceCodeKey => $strTempServiceCodeVal){
										$orival = $refineVal = "";
										$orival = $this->objCoreLang->get_vocabulary("vision_share_271", "Service_Type_Code", $strTempServiceCodeVal);
										$refineVal = ((is_string($orival) && empty($orival) == false) ? $orival : "");
										if(empty($refineVal) == false){
											$strEBSerTypeVal .= $refineVal."<br>";
										}
									}
								}
								//$strDeductibleRow .= $strEBInsTypeId;
								$strDeductibleRow .= "<div><i>";
								if(trim($strEBSerTypeVal) != ""){
									$strDeductibleRow .= $strEBSerTypeVal;
								}
								else{
									$strDeductibleRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}
								$strDeductibleRow .= "</i></div>";
							}
							
							$strDeductibleRow .= "<td>";
							if(trim($strEBCovLevelVal) != ""){
								$strDeductibleRow .= $strEBCovLevelVal;	
							}
							else{
								$strDeductibleRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
							}
							$strDeductibleRow .= "</td>";
							
							$strDeductibleRow .= "</td>";
							$strDeductibleRow .= "<td>";						
							$strDeductibleRow .= "$".(float)$intEBMonetaryAmount;	
							$strDeductibleRow .= "<div><i>";
							if(trim($strEBTimePeriodQuVal) != ""){
								$strDeductibleRow .= " ".$strEBTimePeriodQuVal;	
							}
							else{
								$strDeductibleRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
							}
							$strDeductibleRow .= "</i></div>";
							$strDeductibleRow .= "</td>";						
							##						
							$strDeductibleRow .= "<td>";
							if($srtDTPFormat == "D8"){
								$intDOBYear = substr($srtDTPDate,0,4);
								$intDOBMon = substr($srtDTPDate,4,2);
								$intDOBDay = substr($srtDTPDate,6,2);
								$srtDTPDate = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
								$strDeductibleRow .= $srtDTPDate;
								if(trim($srtDTPVal) != ""){										
									$strDeductibleRow .= "<div><i>".$srtDTPVal."</i></div>";
								}
								else{
									$strDeductibleRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
								}	
							}
							elseif($srtDTPFormat == "RD8"){
								$intDOBYear = substr($srtDTPDate1,0,4);
								$intDOBMon = substr($srtDTPDate1,4,2);
								$intDOBDay = substr($srtDTPDate1,6,2);
								$srtDTPDate1 = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
								$intDOBYear = substr($srtDTPDate2,0,4);
								$intDOBMon = substr($srtDTPDate2,4,2);
								$intDOBDay = substr($srtDTPDate2,6,2);
								$srtDTPDate2 = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
								$strDeductibleRow .= $srtDTPDate1." - ".$srtDTPDate2;	
								if(trim($srtDTPVal) != ""){
									$strDeductibleRow .= "<div><i>".$srtDTPVal."</i></div>";
								}
								else{
									$strDeductibleRow .= ((empty($intDTPId) == false) ? "<div><i>".$intDTPId."(Unknown)</i></div>" : "");
								}
							}								
							$strDeductibleRow .= "</td>";
							##
							####					
							$strDeductibleRow .= "<td>";						
							$strDeductibleRow .= $intEBQuantity;
							if(trim($strEBQuantityQuVal) != ""){										
								$strDeductibleRow .= "<div><i>".$strEBQuantityQuVal."</i></div>";
							}
							else{								
								$strDeductibleRow .= ((empty($strEBQuantityQuId) == false) ? "<div><i>".$strEBQuantityQuId."(Unknown)</i></div>" : "");
							}							
							$strDeductibleRow .= "</td>";
							####
							#### Authorization or Certification Indicator
							$strDeductibleRow .= "<td>";						
							if(trim($strEBConditionRespVal) != ""){										
								$strDeductibleRow .= $strEBConditionRespVal;
							}
							else{
								$strDeductibleRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
							}					
							$strDeductibleRow .= "</td>";
							####
							
							#### Plan Network Indicator
							$strDeductibleRow .= "<td>";						
							if(trim($strEBPlanConditionRespVal) != ""){										
								$strDeductibleRow .= $strEBPlanConditionRespVal;
							}
							else{
								$strDeductibleRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
							}			
							$strDeductibleRow .= "</td>";
							#### 
							#### Message from Information Source 
							$strDeductibleRow .= "<td>";						
								$strDeductibleRow .= $strMSGValue;
							$strDeductibleRow .= "</td>";							
							####
							$strDeductibleRow .= "</tr>";
							$intTempCounterC++;
						}
						elseif(trim($strEBInfoId) == "B"){ //Co-Payment information
							$class = ($intTempCounterB % 2) == 0 ? "bgcolor" : "";
							$strCopayRow .= "<tr class=\"$class\">";
							$strCopayRow .= "<td>";
							if(trim($strEBInsTypeVal) != ""){
								$strCopayRow .= $strEBInsTypeVal;	
								$strCopayRow .= "<div>";
								if(trim($strEBSerTypeVal) != ""){
									$strCopayRow .= $strEBSerTypeVal;
								}
								else{
									$strCopayRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}
								$strCopayRow .= "</div>";
							}
							else{
								$strCopayRow .= $strEBInsTypeId;
								if(is_string($intEBSerTypeId) == true){
									$arrTempServiceCode = array();
									$arrTempServiceCode = explode("^", $intEBSerTypeId);
									foreach($arrTempServiceCode as $intTempServiceCodeKey => $strTempServiceCodeVal){
										$orival = $refineVal = "";
										$orival = $this->objCoreLang->get_vocabulary("vision_share_271", "Service_Type_Code", $strTempServiceCodeVal);
										$refineVal = ((is_string($orival) && empty($orival) == false) ? $orival : "");
										if(empty($refineVal) == false){
											$strEBSerTypeVal .= $refineVal."<br>";
										}
									}
								}	
								if(trim($strEBSerTypeVal) != ""){
									$strCopayRow .= $strEBSerTypeVal;
								}
								else{
									$strCopayRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}
								$strCopayRow .= "</div>";
							}
							$strCopayRow .= "</td>";
							
							$strCopayRow .= "<td>";
							if(trim($strEBCovLevelVal) != ""){
								$strCopayRow .= $strEBCovLevelVal;	
							}
							else{
								$strCopayRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
							}
							$strCopayRow .= "</td>";
							
							$strCopayRow .= "<td>";						
							//$strCopayRow .= "$".(float)$intEBMonetaryAmount;
							$strCopayRow .= ($intEBMonetaryAmount) ? "$".(float)$intEBMonetaryAmount : "";							
							$strCopayRow .= "<div><i>";
							if((trim($strEBTimePeriodQuVal) != "") && (empty($intEBMonetaryAmount) == false)){
								$strCopayRow .= " ".$strEBTimePeriodQuVal;	
							}
							elseif(empty($intEBMonetaryAmount) == false){
								$strCopayRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
							}
							$strCopayRow .= "</i></div>";
							$strCopayRow .= "</td>";							
							##						
							$strCopayRow .= "<td>";
							if($srtDTPFormat == "D8"){
								$intDOBYear = substr($srtDTPDate,0,4);
								$intDOBMon = substr($srtDTPDate,4,2);
								$intDOBDay = substr($srtDTPDate,6,2);
								$srtDTPDate = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
								$strCopayRow .= $srtDTPDate;
								if(trim($srtDTPVal) != ""){										
									$strCopayRow .= "<div>".$srtDTPVal."</div>";
								}
								else{
									$strCopayRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
								}	
							}
							elseif($srtDTPFormat == "RD8"){
								$intDOBYear = substr($srtDTPDate1,0,4);
								$intDOBMon = substr($srtDTPDate1,4,2);
								$intDOBDay = substr($srtDTPDate1,6,2);
								$srtDTPDate1 = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
								$intDOBYear = substr($srtDTPDate2,0,4);
								$intDOBMon = substr($srtDTPDate2,4,2);
								$intDOBDay = substr($srtDTPDate2,6,2);
								$srtDTPDate2 = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
								$strCopayRow .= $srtDTPDate1." - ".$srtDTPDate2;	
								if(trim($srtDTPVal) != ""){
									$strCopayRow .= "<div>".$srtDTPVal."</div>";
								}
								else{
									$strCopayRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
								}
							}								
							$strCopayRow .= "</td>";
							##
							####					
							$strCopayRow .= "<td>";						
							$strCopayRow .= $intEBQuantity;
							if(trim($strEBQuantityQuVal) != ""){										
								$strCopayRow .= "<div>".$strEBQuantityQuVal."</div>";
							}
							else{
								$strCopayRow .= ((empty($strEBQuantityQuId) == false) ? "<div>".$strEBQuantityQuId."(Unknown) </div>" : "");
							}							
							$strCopayRow .= "</td>";
							#### Message from Information Source 
							$strCopayRow .= "<td>";						
								$strCopayRow .= $strMSGValue;
							$strCopayRow .= "</td>";							
							####
							####
							$strCopayRow .= "</tr>";
							$intTempCounterB++;
						}						
						elseif((trim($strEBInfoId) == "D") && ($parseFor == 2)){ //Benefit Description
							$strBenefitRowInsType = $strBenefitRowInsCov = $strBenefitRowInsSer = $strBenefitRowInsPlan = "";															
							if(trim($strEBInsTypeVal) != ""){
								//Insurance Type - start
								$strBenefitRowInsType = $strEBInsTypeVal;	
								//Insurance Type - end
							}
							else{
								//Insurance Type - start
								$strBenefitRowInsType = ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "");
								//Insurance Type - end
							}
							
							if(trim($strEBCovLevelVal) != ""){
								//Insurance Coverage Level - start
								$strBenefitRowInsCov = $strEBCovLevelVal;	
								//Insurance Coverage Level - end
							}
							else{
								//Insurance Coverage Level - start
								$strBenefitRowInsCov = ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								//Insurance Coverage Level - end
							}
							
							if(trim($strEBSerTypeVal) != ""){
								//Insurance Service Type - start
								$strBenefitRowInsSer = $strEBSerTypeVal;
								//Insurance Service Type - end
							}
							else{
								//Insurance Service Type - start
								$strBenefitRowInsSer = ((empty($strEBSerTypeId) == false) ? $strEBSerTypeId."(Unknown)" : "");
								//Insurance Service Type - end
							}
							
							if(trim($strEBPlanCoverageDesc) != ""){
								//Insurance Plan Coverage Description - Start
								$strBenefitRowInsPlan = $strEBPlanCoverageDesc;
								//Insurance Plan Coverage Description - end
							}						
							
							if(($strBenefitRowInsSer != "") || ($strBenefitRowInsPlan != "")){
								$class = ($intTempCounterD % 2) == 0 ? "bgcolor" : "";
								$strBenefitRow .= "<tr class=\"$class\">";
								$strBenefitRow .= "<td>".$strBenefitRowInsType."</td><td>".$strBenefitRowInsCov."</td><td>".$strBenefitRowInsSer."</td><td>".$strBenefitRowInsPlan."</td>";
								$strBenefitRow .= "</tr>";
								$intTempCounterD++;
							}
						}
					}					
					elseif(((trim($strEBSerTypeId) == "AL") || (trim($strEBSerTypeId) == "AM") || (trim($strEBSerTypeId) == "AN") || (trim($strEBSerTypeId) == "AO")) && ($parseFor == 2)){ //
						$strVisionRowInsType = $strVisionRowInsStatus = $strVisionRowInsCov = $strVisionRowInsSer = $strVisionRowInsPlan = "";															
						if(trim($strEBInsTypeVal) != ""){
							//Insurance Type - start
							$strVisionRowInsType = $strEBInsTypeVal;	
							//Insurance Type - end
						}
						else{
							//Insurance Type - start
							$strVisionRowInsType = ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "");
							//Insurance Type - end
						}
																				
						if(trim($strEBInfoVal) != ""){
							//Insurance statue - start
							$strVisionRowInsStatus = $strEBInfoVal;	
							//Insurance statue - end
						}
						else{
							//Insurance statue - start
							$strVisionRowInsStatus = ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
							//Insurance statue - end
						}
						
						if(trim($strEBCovLevelVal) != ""){
							//Insurance Coverage Level - start
							$strVisionRowInsCov = $strEBCovLevelVal;	
							//Insurance Coverage Level - end
						}
						else{
							//Insurance Coverage Level - start
							$strVisionRowInsCov = ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
							//Insurance Coverage Level - end
						}
						
						if(trim($strEBSerTypeVal) != ""){
							//Insurance Service Type - start
							$strVisionRowInsSer = $strEBSerTypeVal;
							//Insurance Service Type - end
						}
						else{
							//Insurance Service Type - start
							$strVisionRowInsSer = ((empty($strEBSerTypeId) == false) ? $strEBSerTypeId."(Unknown)" : "");
							//Insurance Service Type - end
						}
						
						if(trim($strEBPlanCoverageDesc) != ""){
							//Insurance Plan Coverage Description - Start
							$strVisionRowInsPlan = $strEBPlanCoverageDesc;
							//Insurance Plan Coverage Description - end
						}						
						
						if(($strVisionRowInsSer != "") || ($strVisionRowInsPlan != "")){
							$class = ($intTempCounterV % 2) == 0 ? "bgcolor" : "";
							$strVisionRow .= "<tr class=\"$class\">";
							$strVisionRow .= "<td>".$strVisionRowInsType."</td><td>".$strVisionRowInsStatus."</td><td>".$strVisionRowInsCov."</td><td>".$strVisionRowInsSer."</td><td>".$strVisionRowInsPlan."</td>";
							$strVisionRow .= "</tr>";
							$intTempCounterV++;
						}
					}
					elseif(((empty($strEBConditionRespId) == false) || (empty($strEBConditionRespVal) == false)) && ($parseFor == 2)){
						$class = ($intTempCounterPC % 2) == 0 ? "bgcolor" : "";				
						$strPerCertRow .= "<tr class=\"$class\">";
						$strPerCertRow .= "<td nowrap>"; //Insurance Type - start
						if(trim($strEBInsTypeVal) != ""){
							$strPerCertRow .= $strEBInsTypeVal;
							if(trim($strEBSerTypeVal) != ""){
								$strPerCertRow .= "<div>".$strEBSerTypeVal."</div>";	
							}
							else{
								$strPerCertRow .= "<div>".((empty($strEBSerTypeId) == false) ? $strEBSerTypeId."(Unknown)" : "")."</div>";	
							}
						}
						else{
							$strPerCertRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "");
							if(trim($strEBSerTypeVal) != ""){
								$strPerCertRow .= "<div>".$strEBSerTypeVal."</div>";	
							}
							else{
								$strPerCertRow .= "<div>".((empty($strEBSerTypeId) == false) ? $strEBSerTypeId."(Unknown)" : "")."</div>";	
							}
						}
						$strPerCertRow .= "</td>"; //Insurance Type - end
						
						$strPerCertRow .= "<td>"; //Insurance statue - start
						if(trim($strEBInfoVal) != ""){
							$strPerCertRow .= $strEBInfoVal;	
						}
						else{
							$strPerCertRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
						}
						$strPerCertRow .= "</td>"; //Insurance statue - end
						
						$strPerCertRow .= "<td>"; //Insurance Coverage Level - start
						if(trim($strEBCovLevelVal) != ""){
							$strPerCertRow .= $strEBCovLevelVal;	
						}
						else{
							$strPerCertRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
						}
						$strPerCertRow .= "</td>"; //Insurance Coverage Level - end
																				
						$strPerCertRow .= "<td>"; //Insurance Authorization or Certification Indicator - start
						if(trim($strEBConditionRespVal) != ""){										
							$strPerCertRow .= $strEBConditionRespVal;
						}
						else{
							$strPerCertRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
						}							
						$strPerCertRow .= "</td>"; //Insurance Authorization or Certification Indicator   - end
						
						$strPerCertRow .= "<td>"; //Insurance In Plan Network Indicator - start
						if(trim($strEBPlanConditionRespVal) != ""){										
							$strPerCertRow .= $strEBPlanConditionRespVal;
						}
						else{
							$strPerCertRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
						}							
						$strPerCertRow .= "</td>"; //Insurance In Plan Network Indicator - end
						$intTempCounterPC++;
					}					
					elseif((empty($strEBPercent) == false) || (empty($strEBConditionRespId) == false) || (empty($strEBConditionRespVal) == false) || (empty($strEBPlanConditionRespId) == false) || (empty($strEBPlanConditionRespVal) == false)){						
						$class = ($intTempCounterOtinfo % 2) == 0 ? "bgcolor" : "";				
						$strOtherRow .= "<tr class=\"$class\">";
							$strOtherRow .= "<td nowrap>"; //Insurance Type - start
							if(trim($strEBInsTypeVal) != ""){
								$strOtherRow .= $strEBInsTypeVal;	
							}
							else{
								$strOtherRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "");
							}
							$strOtherRow .= "</td>"; //Insurance Type - end
							
							$strOtherRow .= "<td>"; //Insurance statue - start
							if(trim($strEBInfoVal) != ""){
								$strOtherRow .= $strEBInfoVal;	
							}
							else{
								$strOtherRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
							}
							$strOtherRow .= "</td>"; //Insurance statue - end
							
							$strOtherRow .= "<td>"; //Insurance Coverage Level - start
							if(trim($strEBCovLevelVal) != ""){
								$strOtherRow .= $strEBCovLevelVal;	
							}
							else{
								$strOtherRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
							}
							$strOtherRow .= "</td>"; //Insurance Coverage Level - end
							
							$strOtherRow .= "<td>"; //Insurance Service Type - start
							if(trim($strEBSerTypeVal) != ""){
								$strOtherRow .= $strEBSerTypeVal;
							}
							else{
								$strOtherRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
							}							
							$strOtherRow .= "</td>"; //Insurance Service Type - end
							
							$strOtherRow .= "<td>"; //Insurance Plan Coverage Description - Start
							if(trim($strEBPlanCoverageDesc) != ""){
								$strOtherRow .= $strEBPlanCoverageDesc;
							}						
							$strOtherRow .= "</td>"; //Insurance Plan Coverage Description - end
							
							$strOtherRow .= "<td>"; //Insurance Amount - start
							$strOtherRow .= $intEBMonetaryAmount;
							$strOtherRow .= "</td>"; //Insurance Amount - End
							
							$strOtherRow .= "<td>"; //Insurance Time Period - Start
							if(trim($strEBTimePeriodQuVal) != ""){
								$strOtherRow .= $strEBTimePeriodQuVal;	
							}
							else{
								$strOtherRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
							}
							$strOtherRow .= "</td>"; //Insurance Time Period - End
							
							$strOtherRow .= "<td>"; //Insurance Percent  - start							
							$strOtherRow .= ($strEBPercent) ? (float)$strEBPercent * 100 : "";
							$strOtherRow .= "</td>"; //Insurance Percent  - end
							
							$strOtherRow .= "<td>"; //Insurance Quantity   - start
							$strOtherRow .= $intEBQuantity;
							if(trim($strEBQuantityQuVal) != ""){										
								$strOtherRow .= "<div>".$strEBQuantityQuVal."</div>";
							}
							else{
								$strOtherRow .= ((empty($strEBQuantityQuId) == false) ? "<div>".$strEBQuantityQuId."(Unknown) </div>" : "");
							}							
							$strOtherRow .= "</td>"; //Insurance Quantity   - end
							
							$strOtherRow .= "<td>"; //Insurance Authorization or Certification Indicator - start
							if(trim($strEBConditionRespVal) != ""){										
								$strOtherRow .= $strEBConditionRespVal;
							}
							else{
								$strOtherRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
							}							
							$strOtherRow .= "</td>"; //Insurance Authorization or Certification Indicator   - end
							
							$strOtherRow .= "<td>"; //Insurance In Plan Network Indicator - start
							if(trim($strEBPlanConditionRespVal) != ""){										
								$strOtherRow .= $strEBPlanConditionRespVal;
							}
							else{
								$strOtherRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
							}							
							$strOtherRow .= "</td>"; //Insurance In Plan Network Indicator - end
							
							$strOtherRow .= "<td>"; //Insurance Date - start
							if($srtDTPFormat == "D8"){
								$intDOBYear = substr($srtDTPDate,0,4);
								$intDOBMon = substr($srtDTPDate,4,2);
								$intDOBDay = substr($srtDTPDate,6,2);
								$srtDTPDate = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
								$strOtherRow .= $srtDTPDate;
								if(trim($srtDTPVal) != ""){										
									$strOtherRow .= "<div>".$srtDTPVal."</div>";
								}
								else{
									$strOtherRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
								}	
							}
							elseif($srtDTPFormat == "RD8"){
								$intDOBYear = substr($srtDTPDate1,0,4);
								$intDOBMon = substr($srtDTPDate1,4,2);
								$intDOBDay = substr($srtDTPDate1,6,2);
								$srtDTPDate1 = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
								$intDOBYear = substr($srtDTPDate2,0,4);
								$intDOBMon = substr($srtDTPDate2,4,2);
								$intDOBDay = substr($srtDTPDate2,6,2);
								$srtDTPDate2 = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
								$strOtherRow .= $srtDTPDate1." - ".$srtDTPDate2;	
								if(trim($srtDTPVal) != ""){
									$strOtherRow .= "<div>".$srtDTPVal."</div>";
								}
								else{
									$strOtherRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");									
								}
							}								
							$strOtherRow .= "</td>"; //Insurance Date - start
							#### Message from Information Source 
							$strOtherRow .= "<td>";						
								$strOtherRow .= $strMSGValue;
							$strOtherRow .= "</td>";							
							####
							$strOtherRow .= "</tr>";
							$intTempCounterOtinfo++;
					}
					$blDTPLoopSt = $blDTPDateSt = $blEBLoopSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
					$blEBConditionRespSt = $blEBPlanConditionRespSt = false;
					$intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
					$srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
					$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
					$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
					$blEBLoopSt = false;
				}
				if($blDTPLoopSt == true){
					if(($val["tag"] == "DateTimeQualifierID") && ($val["type"] == "complete") && ($intDTPId == 0)){
						$intDTPId = $val["value"];
					}
					elseif(($val["tag"] == "DateTimeQualifierIDVal") && ($val["type"] == "complete") && ($srtDTPVal == "")){
						$srtDTPVal = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriodFormatQualifier") && ($val["type"] == "complete") && ($srtDTPFormat == "")){
						$srtDTPFormat = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "open") && ($blDTPDateSt == false)){
						$blDTPDateSt = true;
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "close") && ($blDTPDateSt == true)){
						$blDTPDateSt = false;
					}					
					if($blDTPDateSt == true){
						if(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "D8") && ($srtDTPDate == "")){
							$srtDTPDate = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($srtDTPDate1 == "")){
							$srtDTPDate1 = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate2") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($srtDTPDate2 == "")){
							$srtDTPDate2 = $val["value"];
						}
					}
				}
				elseif($blEBLoopSt == true){
					if(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "open") && ($blEBInfoSt == false)){
						$blEBInfoSt = true;						
					}
					elseif(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "close") && ($blEBInfoSt == true)){
						$blEBInfoSt = false;
					}
					elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "open") && ($blEBCovLevelSt == false)){
						$blEBCovLevelSt = true;
					}
					elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "close") && ($blEBCovLevelSt == true)){
						$blEBCovLevelSt = false;
					}
					elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "open") && ($blEBSerTypeSt == false)){
						$blEBSerTypeSt = true;
					}
					elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "close") && ($blEBSerTypeSt == true)){
						$blEBSerTypeSt = false;
					}
					elseif(($val["tag"] == "PlanCoverageDescription") && ($val["type"] == "complete") && ($strEBPlanCoverageDesc = "")){
						$strEBPlanCoverageDesc = $val["value"];
					}
					elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "open") && ($blEBInsTypeSt == false)){
						$blEBInsTypeSt = true;
					}
					elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "close") && ($blEBInsTypeSt == true)){
						$blEBInsTypeSt = false;
					}
					elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "open") && ($blEBTimePeriodQuSt == false)){
						$blEBTimePeriodQuSt = true;
					}
					elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "close") && ($blEBTimePeriodQuSt == true)){
						$blEBTimePeriodQuSt = false;
					}
					elseif(($val["tag"] == "MonetaryAmount") && ($val["type"] == "complete")){
						$intEBMonetaryAmount = $val["value"];
					}
					elseif(($val["tag"] == "Percent") && ($val["type"] == "complete") && ($strEBPercent == "")){
						$strEBPercent = $val["value"];
					}
					elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "open") && ($blEBQuantityQuSt == false)){
						$blEBQuantityQuSt = true;
					}
					elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "close") && ($blEBQuantityQuSt == true)){
						$blEBQuantityQuSt = false;
					}
					elseif(($val["tag"] == "Quantity") && ($val["type"] == "complete") && ($intEBQuantity == 0)){
						$intEBQuantity = $val["value"];
					}
					elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "open") && ($blEBConditionRespSt == false)){
						$blEBConditionRespSt = true;
					}
					elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "close") && ($blEBConditionRespSt == true)){
						$blEBConditionRespSt = false;
					}
					elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "open") && ($blEBPlanConditionRespSt == false)){
						$blEBPlanConditionRespSt = true;
					}
					elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "close") && ($blEBPlanConditionRespSt == true)){
						$blEBPlanConditionRespSt = false;
					}
					
					if($blEBInfoSt == true){						
						if(($val["tag"] == "EligibilityOrBenefitInformationId") && ($val["type"] == "complete") && ($strEBInfoId == 0)){
							$strEBInfoId = $val["value"];
						}
						elseif(($val["tag"] == "EligibilityOrBenefitInformationVal") && ($val["type"] == "complete") && ($strEBInfoVal == "")){
							$strEBInfoVal = $val["value"];
						}
					}
					elseif($blEBCovLevelSt == true){
						if(($val["tag"] == "CoverageLevelCodeId") && ($val["type"] == "complete") && ($strEBCovLevelId == "")){
							$strEBCovLevelId = $val["value"];
						}
						elseif(($val["tag"] == "CoverageLevelCodeVal") && ($val["type"] == "complete") && ($strEBCovLevelVal == "")){
							$strEBCovLevelVal = $val["value"];
						}
					}
					elseif($blEBInsTypeSt == true){
						if(($val["tag"] == "InsuranceTypeCodeId") && ($val["type"] == "complete") && ($strEBInsTypeId == "")){
							$strEBInsTypeId = $val["value"];
						}
						elseif(($val["tag"] == "InsuranceTypeCodeVal") && ($val["type"] == "complete") && ($strEBInsTypeVal == "")){
							$strEBInsTypeVal = $val["value"];
						}
					}
					elseif($blEBSerTypeSt == true){
						if(($val["tag"] == "ServiceTypeCodeId") && ($val["type"] == "complete") && ($intEBSerTypeId == 0)){
							$intEBSerTypeId = $val["value"];
						}
						elseif(($val["tag"] == "ServiceTypeCodeVal") && ($val["type"] == "complete") && ($strEBSerTypeVal == "")){
							$strEBSerTypeVal = $val["value"];
						}
					}
					elseif($blEBTimePeriodQuSt == true){
						if(($val["tag"] == "TimePeriodQualifierId") && ($val["type"] == "complete") && ($intEBTimePeriodQuId == 0)){
							$intEBTimePeriodQuId = $val["value"];
						}
						elseif(($val["tag"] == "TimePeriodQualifierVal") && ($val["type"] == "complete") && ($strEBTimePeriodQuVal == "")){
							$strEBTimePeriodQuVal = $val["value"];
						}
					}
					elseif($blEBQuantityQuSt == true){
						if(($val["tag"] == "QuantityQualifierId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
							$strEBQuantityQuId = $val["value"];
						}
						elseif(($val["tag"] == "QuantityQualifierVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
							$strEBQuantityQuVal = $val["value"];
						}
					}
					elseif($blEBConditionRespSt == true){
						if(($val["tag"] == "ConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
							$strEBConditionRespId = $val["value"];
						}
						elseif(($val["tag"] == "ConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
							$strEBConditionRespVal = $val["value"];
						}
					}
					elseif($blEBPlanConditionRespSt == true){
						if(($val["tag"] == "PlanConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBPlanConditionRespId == "")){
							$strEBPlanConditionRespId = $val["value"];
						}
						elseif(($val["tag"] == "PlanConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBPlanConditionRespVal == "")){
							$strEBPlanConditionRespVal = $val["value"];
						}
					}
				}
			}
		}
		if($parseFor == 1){
			return array($strMAMBRow, $strDeductibleRow, $strCopayRow, $strOtherRow);
		}
		elseif($parseFor == 2){
			return array($strDeductibleRow, $strCopayRow, $strBenefitRow, $strVisionRow, $strPerCertRow);
		}
	}
	
	function getTotalAmtVS($strXMLPath){		
        if(file_exists($strXMLPath)){
            $arrXMLvalues = $arrPatCopay = array();
            $XML = file_get_contents($strXMLPath);			
            $arrXMLvalues = $this->objCommonFunction->XMLToArray($XML);
            //pre($arrXMLvalues,1);
            $blDTPLoopSt = $blDTPDateSt = $blEBLoopSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
            $blEBConditionRespSt = $blEBPlanConditionRespSt = $blDTPInternal = false;
            $intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
            $srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
            $strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
            $strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
            $strMAMBRow = $strDeductibleRow = $strCopayRow = $strOtherRow = "";
            $intTempCounterD = $intTempCounterC = $intTempCounterB = $intTempCounterV = $intTempCounterPC = $intTempCounterEBinfo = $intTempCounterOtinfo = 2;
            $strBenefitRow = $strVisionRow = $strPerCertRow = "";
            $intTotDeductibleAmt = $intTotCopayAmt = 0;
            foreach($arrXMLvalues as $key => $val){				
                if(($val["tag"] == "DTPLOOPINFO") && ($val["type"] == "open") && ($blDTPLoopSt == false)){
                    $blDTPLoopSt = true;
                }
                elseif(($val["tag"] == "DTPLOOPINFO") && ($val["type"] == "close") && ($blDTPLoopSt == true)){
                    $blDTPLoopSt = false;
                }
                elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "open") && ($blEBLoopSt == false)){
                    $blEBLoopSt = true;
                }
                elseif(($blDTPLoopSt == true) && ($blDTPInternal == false)){
                    $valTemp = $val["tag"];
                    if(strpos($valTemp, "DTPLOOP") !== false){
                        $arrTemp = explode("-", $valTemp);
                        $intDTPLoopCo = $arrTemp[1];
                    }
                }
                elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "close") && ($blEBLoopSt == true)){					
                    if((trim($strEBInfoId) == "C") || (trim($strEBInfoId) == "B")){
                        if(trim($strEBInfoId) == "C"){  //Deductible information
                            $intTotDeductibleAmt += (int)$intEBMonetaryAmount;
                        }
						elseif((trim($strEBInfoId) == "B") && ((float)$intEBMonetaryAmount > 0)){ //Co-Payment information
							$patCopay = 0;
							$patCopay = (float)$intEBMonetaryAmount;
                            $intTotCopayAmt += $patCopay;
							$arrPatCopay[] = $patCopay;
                        }
                    }					
                    
                    $blDTPLoopSt = $blDTPDateSt = $blEBLoopSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
                    $blEBConditionRespSt = $blEBPlanConditionRespSt = false;
                    $intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
                    $srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
                    $strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
                    $strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
                    $blEBLoopSt = false;
                }
                if($blDTPLoopSt == true){
                    if(($val["tag"] == "DateTimeQualifierID") && ($val["type"] == "complete") && ($intDTPId == 0)){
                        $intDTPId = $val["value"];
                    }
                    elseif(($val["tag"] == "DateTimeQualifierIDVal") && ($val["type"] == "complete") && ($srtDTPVal == "")){
                        $srtDTPVal = $val["value"];
                    }
                    elseif(($val["tag"] == "DateTimePeriodFormatQualifier") && ($val["type"] == "complete") && ($srtDTPFormat == "")){
                        $srtDTPFormat = $val["value"];
                    }
                    elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "open") && ($blDTPDateSt == false)){
                        $blDTPDateSt = true;
                    }
                    elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "close") && ($blDTPDateSt == true)){
                        $blDTPDateSt = false;
                    }					
                    if($blDTPDateSt == true){
                        if(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "D8") && ($srtDTPDate == "")){
                            $srtDTPDate = $val["value"];
                        }
                        elseif(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($srtDTPDate1 == "")){
                            $srtDTPDate1 = $val["value"];
                        }
                        elseif(($val["tag"] == "DateTimePeriodDate2") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($srtDTPDate2 == "")){
                            $srtDTPDate2 = $val["value"];
                        }
                    }
                }
                elseif($blEBLoopSt == true){
                    if(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "open") && ($blEBInfoSt == false)){
                        $blEBInfoSt = true;						
                    }
                    elseif(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "close") && ($blEBInfoSt == true)){
                        $blEBInfoSt = false;
                    }
                    elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "open") && ($blEBCovLevelSt == false)){
                        $blEBCovLevelSt = true;
                    }
                    elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "close") && ($blEBCovLevelSt == true)){
                        $blEBCovLevelSt = false;
                    }
                    elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "open") && ($blEBSerTypeSt == false)){
                        $blEBSerTypeSt = true;
                    }
                    elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "close") && ($blEBSerTypeSt == true)){
                        $blEBSerTypeSt = false;
                    }
                    elseif(($val["tag"] == "PlanCoverageDescription") && ($val["type"] == "complete") && ($strEBPlanCoverageDesc = "")){
                        $strEBPlanCoverageDesc = $val["value"];
                    }
                    elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "open") && ($blEBInsTypeSt == false)){
                        $blEBInsTypeSt = true;
                    }
                    elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "close") && ($blEBInsTypeSt == true)){
                        $blEBInsTypeSt = false;
                    }
                    elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "open") && ($blEBTimePeriodQuSt == false)){
                        $blEBTimePeriodQuSt = true;
                    }
                    elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "close") && ($blEBTimePeriodQuSt == true)){
                        $blEBTimePeriodQuSt = false;
                    }
                    elseif(($val["tag"] == "MonetaryAmount") && ($val["type"] == "complete")){
                        $intEBMonetaryAmount = $val["value"];
                    }
                    elseif(($val["tag"] == "Percent") && ($val["type"] == "complete") && ($strEBPercent == "")){
                        $strEBPercent = $val["value"];
                    }
                    elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "open") && ($blEBQuantityQuSt == false)){
                        $blEBQuantityQuSt = true;
                    }
                    elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "close") && ($blEBQuantityQuSt == true)){
                        $blEBQuantityQuSt = false;
                    }
                    elseif(($val["tag"] == "Quantity") && ($val["type"] == "complete") && ($intEBQuantity == 0)){
                        $intEBQuantity = $val["value"];
                    }
                    elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "open") && ($blEBConditionRespSt == false)){
                        $blEBConditionRespSt = true;
                    }
                    elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "close") && ($blEBConditionRespSt == true)){
                        $blEBConditionRespSt = false;
                    }
                    elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "open") && ($blEBPlanConditionRespSt == false)){
                        $blEBPlanConditionRespSt = true;
                    }
                    elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "close") && ($blEBPlanConditionRespSt == true)){
                        $blEBPlanConditionRespSt = false;
                    }
                    
                    if($blEBInfoSt == true){						
                        if(($val["tag"] == "EligibilityOrBenefitInformationId") && ($val["type"] == "complete") && ($strEBInfoId == 0)){
                            $strEBInfoId = $val["value"];
                        }
                        elseif(($val["tag"] == "EligibilityOrBenefitInformationVal") && ($val["type"] == "complete") && ($strEBInfoVal == "")){
                            $strEBInfoVal = $val["value"];
                        }
                    }
                    elseif($blEBCovLevelSt == true){
                        if(($val["tag"] == "CoverageLevelCodeId") && ($val["type"] == "complete") && ($strEBCovLevelId == "")){
                            $strEBCovLevelId = $val["value"];
                        }
                        elseif(($val["tag"] == "CoverageLevelCodeVal") && ($val["type"] == "complete") && ($strEBCovLevelVal == "")){
                            $strEBCovLevelVal = $val["value"];
                        }
                    }
                    elseif($blEBInsTypeSt == true){
                        if(($val["tag"] == "InsuranceTypeCodeId") && ($val["type"] == "complete") && ($strEBInsTypeId == "")){
                            $strEBInsTypeId = $val["value"];
                        }
                        elseif(($val["tag"] == "InsuranceTypeCodeVal") && ($val["type"] == "complete") && ($strEBInsTypeVal == "")){
                            $strEBInsTypeVal = $val["value"];
                        }
                    }
                    elseif($blEBSerTypeSt == true){
                        if(($val["tag"] == "ServiceTypeCodeId") && ($val["type"] == "complete") && ($intEBSerTypeId == 0)){
                            $intEBSerTypeId = $val["value"];
                        }
                        elseif(($val["tag"] == "ServiceTypeCodeVal") && ($val["type"] == "complete") && ($strEBSerTypeVal == "")){
                            $strEBSerTypeVal = $val["value"];
                        }
                    }
                    elseif($blEBTimePeriodQuSt == true){
                        if(($val["tag"] == "TimePeriodQualifierId") && ($val["type"] == "complete") && ($intEBTimePeriodQuId == 0)){
                            $intEBTimePeriodQuId = $val["value"];
                        }
                        elseif(($val["tag"] == "TimePeriodQualifierVal") && ($val["type"] == "complete") && ($strEBTimePeriodQuVal == "")){
                            $strEBTimePeriodQuVal = $val["value"];
                        }
                    }
                    elseif($blEBQuantityQuSt == true){
                        if(($val["tag"] == "QuantityQualifierId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
                            $strEBQuantityQuId = $val["value"];
                        }
                        elseif(($val["tag"] == "QuantityQualifierVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
                            $strEBQuantityQuVal = $val["value"];
                        }
                    }
                    elseif($blEBConditionRespSt == true){
                        if(($val["tag"] == "ConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
                            $strEBConditionRespId = $val["value"];
                        }
                        elseif(($val["tag"] == "ConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
                            $strEBConditionRespVal = $val["value"];
                        }
                    }
                    elseif($blEBPlanConditionRespSt == true){
                        if(($val["tag"] == "PlanConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBPlanConditionRespId == "")){
                            $strEBPlanConditionRespId = $val["value"];
                        }
                        elseif(($val["tag"] == "PlanConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBPlanConditionRespVal == "")){
                            $strEBPlanConditionRespVal = $val["value"];
                        }
                    }
                }
            }
        }
        $arrReturn = array($intTotDeductibleAmt, $intTotCopayAmt, $arrPatCopay);
        return $arrReturn;
    }
	
	function getAmtVS($strXMLPath, $arrDB){	
		if(file_exists($strXMLPath)){
			$arrXMLvalues = array();
			$arrEBInfo = array(1,2,3,4,5,6,7,8);
			$XML = file_get_contents($strXMLPath);			
			$arrXMLvalues = $this->objCommonFunction->XMLToArray($XML);
			//pre($arrXMLvalues,1);
			$blDTPLoopSt = $blDTPDateSt = $blEBLoopSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
			$blEBConditionRespSt = $blEBPlanConditionRespSt = $blDTPInternal = false;
			$intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
			$srtDTPVal = $srtDTPFormat = $srtDTPDate = $srtDTPDate1 = $srtDTPDate2 = $strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
			$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
			$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
			$strMAMBRow = $strDeductibleRow = $strCopayRow = $strOtherRow = "";
			$intTempCounterA = $intTempCounterC = $intTempCounterB = $intTempCounterD = $intTempCounterV = $intTempCounterPC = $intTempCounterEBinfo = $intTempCounterOtinfo = 2;
			$strBenefitRow = $strVisionRow = $strPerCertRow = "";
			$strCoInsRow = "";
			
			$intChkCounterA = $intChkCounterB = $intChkCounterD = 1;
			
			foreach($arrXMLvalues as $key => $val){				
				if(($val["tag"] == "DTPLOOPINFO") && ($val["type"] == "open") && ($blDTPLoopSt == false)){
					$blDTPLoopSt = true;
				}
				elseif(($val["tag"] == "DTPLOOPINFO") && ($val["type"] == "close") && ($blDTPLoopSt == true)){
					$blDTPLoopSt = false;
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "open") && ($blEBLoopSt == false)){
					$blEBLoopSt = true;
				}
				elseif(($blDTPLoopSt == true) && ($blDTPInternal == false)){
					$valTemp = $val["tag"];
					if(strpos($valTemp, "DTPLOOP") !== false){
						$arrTemp = explode("-", $valTemp);
						$intDTPLoopCo = $arrTemp[1];
					}
				}
				elseif(($val["tag"] == "EBLOOPINFO") && ($val["type"] == "close") && ($blEBLoopSt == true)){					
					if((trim($strEBInfoId) == "C") || (trim($strEBInfoId) == "B") || (trim($strEBInfoId) == "D")){
						if(trim($strEBInfoId) == "A"){
							$class = ($intTempCounterA % 2) == 0 ? "bgcolor" : "";				
							$strCoInsRow .= "<tr class=\"$class\">";
							$tempCp = $tempCoIns = "";
							$tempCp = ($intEBMonetaryAmount) ? (float)$intEBMonetaryAmount : "";
							$tempCoIns = ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strValue = $strEBInsTypeId."-".$intEBSerTypeId."-".$strEBCovLevelId."-".$strEBPlanCoverageDesc."-".$tempCp."-".$intEBTimePeriodQuId."-".$tempCoIns."-".$intEBQuantity."-".$strEBQuantityQuId."-".$strEBConditionRespId."-".$strEBPlanConditionRespId;
								$chkChecked = "";
								if($strValue == $arrDB["dbCoins"]){
									$chkChecked = "checked";
								}
								$strCoIns .= "<td>";
								$strCoIns .= "<input type=\"checkbox\" id=\"cbkCoIns$intChkCounterA\" name=\"cbkCoIns\" $chkChecked value=\"".$strValue."\" onclick=\"makeOneCbk(this);\">";
								$strCoIns .= "</td>";
							
								/*$strCoInsRow .= "<td nowrap>"; //Insurance Type - start
								if(trim($strEBInsTypeVal) != ""){
									$strCoInsRow .= ((empty($strEBInsTypeVal) == false) ? $strEBInsTypeVal : "Unknown");	
								}
								else{
									$strCoInsRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
								}
								$strCoInsRow .= "</td>"; //Insurance Type - end
								*/
								/*$strCoInsRow .= "<td>"; //Insurance statue - start
								if(trim($strEBInfoVal) != ""){
									$strCoInsRow .= $strEBInfoVal;	
								}
								else{
									$strCoInsRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
								}
								$strCoInsRow .= "</td>"; //Insurance statue - end
								*/
								$strCoInsRow .= "<td>"; //Insurance Service Type - start
								if(trim($strEBSerTypeVal) != ""){
									$strCoInsRow .= $strEBSerTypeVal;
								}
								else{
									$strCoInsRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}							
								$strCoInsRow .= "</td>"; //Insurance Service Type - end
								
								$strCoInsRow .= "<td>"; //Insurance Coverage Level - start
								if(trim($strEBCovLevelVal) != ""){
									$strCoInsRow .= $strEBCovLevelVal;	
								}
								else{
									$strCoInsRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								}
								$strCoInsRow .= "</td>"; //Insurance Coverage Level - end
								
								$strCoInsRow .= "<td>"; //Insurance Plan Coverage Description - Start
								if(trim($strEBPlanCoverageDesc) != ""){
									$strCoInsRow .= $strEBPlanCoverageDesc;
								}						
								$strCoInsRow .= "</td>"; //Insurance Plan Coverage Description - end
								
								$strCoInsRow .= "<td>"; //Insurance Amount - start
								$strCoInsRow .= ($intEBMonetaryAmount) ? "$".(float)$intEBMonetaryAmount : "";
								$strCoInsRow .= "</td>"; //Insurance Amount - End
								
								$strCoInsRow .= "<td>"; //Insurance Time Period - Start
								if(trim($strEBTimePeriodQuVal) != ""){
									$strCoInsRow .= $strEBTimePeriodQuVal;	
								}
								else{
									$strCoInsRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
								}
								$strCoInsRow .= "</td>"; //Insurance Time Period - End
								
								$strCoInsRow .= "<td>"; //Insurance Percent  - start								
								$strCoInsRow .= ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strCoInsRow .= "</td>"; //Insurance Percent  - end
								
								$strCoInsRow .= "<td>"; //Insurance Quantity   - start
								$strCoInsRow .= $intEBQuantity;
								if(trim($strEBQuantityQuVal) != ""){										

									$strCoInsRow .= "<div>".$strEBQuantityQuVal."</div>";
								}
								else{
									$strCoInsRow .= ((empty($strEBQuantityQuId) == false) ? "<div>".$strEBQuantityQuId."(Unknown) </div>" : "");
								}							
								$strCoInsRow .= "</td>"; //Insurance Quantity   - end
								
								$strCoInsRow .= "<td>"; //Insurance Authorization or Certification Indicator - start
								if(trim($strEBConditionRespVal) != ""){										
									$strCoInsRow .= $strEBConditionRespVal;
								}
								else{
									$strCoInsRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
								}							
								$strCoInsRow .= "</td>"; //Insurance Authorization or Certification Indicator   - end
								
								$strCoInsRow .= "<td>"; //Insurance In Plan Network Indicator - start
								if(trim($strEBPlanConditionRespVal) != ""){										
									$strCoInsRow .= $strEBPlanConditionRespVal;
								}
								else{
									$strCoInsRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
								}							
								$strCoInsRow .= "</td>"; //Insurance In Plan Network Indicator - end
								
								$strCoInsRow .= "<td>"; //Insurance Date - start
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCoInsRow .= $srtDTPDateNew;
									if(trim($srtDTPVal) != ""){										
										$strCoInsRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strCoInsRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCoInsRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtDTPVal) != ""){
										$strCoInsRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strCoInsRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");									
									}
								}								
								$strCoInsRow .= "</td>"; //Insurance Date - start
							$strCoInsRow .= "</tr>";
							$$intTempCounterA++;
							$intChkCounterA++;
						}
						elseif(trim($strEBInfoId) == "B"){
							$class = ($intTempCounterB % 2) == 0 ? "bgcolor" : "";				
							$strCopayRow .= "<tr class=\"$class\">";
								$tempCp = $tempCoIns = "";
								$tempCp = ($intEBMonetaryAmount) ? (float)$intEBMonetaryAmount : "";
								$tempCoIns = ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strValue = $strEBInsTypeId."-".$intEBSerTypeId."-".$strEBCovLevelId."-".$strEBPlanCoverageDesc."-".$tempCp."-".$intEBTimePeriodQuId."-".$tempCoIns."-".$intEBQuantity."-".$strEBQuantityQuId."-".$strEBConditionRespId."-".$strEBPlanConditionRespId;
								$chkChecked = "";
								if($strValue == $arrDB["dbCopay"]){
									$chkChecked = "checked";
								}
								$strCopayRow .= "<td>";
								$strCopayRow .= "<input type=\"checkbox\" id=\"cbkCoPay$intChkCounterB\" name=\"cbkCoPay\" $chkChecked value=\"".$strValue."\" onclick=\"makeOneCbk(this);\">";
								$strCopayRow .= "</td>";
							
								/*$strCopayRow .= "<td nowrap>"; //Insurance Type - start
								if(trim($strEBInsTypeVal) != ""){
									$strCopayRow .= ((empty($strEBInsTypeVal) == false) ? $strEBInsTypeVal : "Unknown");	
								}
								else{
									$strCopayRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
								}
								$strCopayRow .= "</td>"; //Insurance Type - end
								*/
								/*$strCopayRow .= "<td>"; //Insurance statue - start
								if(trim($strEBInfoVal) != ""){
									$strCopayRow .= $strEBInfoVal;	
								}
								else{
									$strCopayRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
								}
								$strCopayRow .= "</td>"; //Insurance statue - end
								*/
								$strCopayRow .= "<td>"; //Insurance Service Type - start
								if(trim($strEBSerTypeVal) != ""){
									$strCopayRow .= $strEBSerTypeVal;
								}
								else{
									$strCopayRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}							
								$strCopayRow .= "</td>"; //Insurance Service Type - end
								
								$strCopayRow .= "<td>"; //Insurance Coverage Level - start
								if(trim($strEBCovLevelVal) != ""){
									$strCopayRow .= $strEBCovLevelVal;	
								}
								else{
									$strCopayRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								}
								$strCopayRow .= "</td>"; //Insurance Coverage Level - end
								
								$strCopayRow .= "<td>"; //Insurance Plan Coverage Description - Start
								if(trim($strEBPlanCoverageDesc) != ""){
									$strCopayRow .= $strEBPlanCoverageDesc;
								}						
								$strCopayRow .= "</td>"; //Insurance Plan Coverage Description - end
								
								$strCopayRow .= "<td>"; //Insurance Amount - start
								$strCopayRow .= ($intEBMonetaryAmount) ? "$".(float)$intEBMonetaryAmount : "";
								$strCopayRow .= "</td>"; //Insurance Amount - End
								
								$strCopayRow .= "<td>"; //Insurance Time Period - Start
								if(trim($strEBTimePeriodQuVal) != ""){
									$strCopayRow .= $strEBTimePeriodQuVal;	
								}
								else{
									$strCopayRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
								}
								$strCopayRow .= "</td>"; //Insurance Time Period - End
								
								$strCopayRow .= "<td>"; //Insurance Percent  - start								
								$strCopayRow .= ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strCopayRow .= "</td>"; //Insurance Percent  - end
								
								$strCopayRow .= "<td>"; //Insurance Quantity   - start
								$strCopayRow .= $intEBQuantity;
								if(trim($strEBQuantityQuVal) != ""){										
									$strCopayRow .= "<div>".$strEBQuantityQuVal."</div>";
								}
								else{
									$strCopayRow .= ((empty($strEBQuantityQuId) == false) ? "<div>".$strEBQuantityQuId."(Unknown) </div>" : "");
								}							
								$strCopayRow .= "</td>"; //Insurance Quantity   - end
								
								$strCopayRow .= "<td>"; //Insurance Authorization or Certification Indicator - start
								if(trim($strEBConditionRespVal) != ""){										
									$strCopayRow .= $strEBConditionRespVal;
								}
								else{
									$strCopayRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
								}							
								$strCopayRow .= "</td>"; //Insurance Authorization or Certification Indicator   - end
								
								$strCopayRow .= "<td>"; //Insurance In Plan Network Indicator - start
								if(trim($strEBPlanConditionRespVal) != ""){										
									$strCopayRow .= $strEBPlanConditionRespVal;
								}
								else{
									$strCopayRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
								}							
								$strCopayRow .= "</td>"; //Insurance In Plan Network Indicator - end
								
								$strCopayRow .= "<td>"; //Insurance Date - start
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCopayRow .= $srtDTPDateNew;
									if(trim($srtDTPVal) != ""){										
										$strCopayRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strCopayRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strCopayRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtDTPVal) != ""){
										$strCopayRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strCopayRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");									
									}
								}								
								$strCopayRow .= "</td>"; //Insurance Date - start
							$strCopayRow .= "</tr>";
							$intTempCounterB++;
							$intChkCounterB++;
						}
						elseif(trim($strEBInfoId) == "C"){													
							$class = ($intTempCounterC % 2) == 0 ? "bgcolor" : "";				
							$strDeductibleRow .= "<tr class=\"$class\">";
								$temCp = $tempCoIns = "";
								$temCp = ($intEBMonetaryAmount) ? (float)$intEBMonetaryAmount : "";
								$tempCoIns = ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strValue = $strEBInsTypeId."-".$intEBSerTypeId."-".$strEBCovLevelId."-".$strEBPlanCoverageDesc."-".$temCp."-".$intEBTimePeriodQuId."-".$tempCoIns."-".$intEBQuantity."-".$strEBQuantityQuId."-".$strEBConditionRespId."-".$strEBPlanConditionRespId;
								$chkChecked = "";
								if($strValue == $arrDB["dbDeductible"]){
									$chkChecked = "checked";
								}
								$strDeductibleRow .= "<td>";
								$strDeductibleRow .= "<input type=\"checkbox\" id=\"cbkDeductible$intChkCounterD\" name=\"cbkDeductible\" $chkChecked value=\"".$strValue."\" onclick=\"makeOneCbk(this);\">";
								$strDeductibleRow .= "</td>";
							
								/*$strDeductibleRow .= "<td nowrap>"; //Insurance Type - start
								if(trim($strEBInsTypeVal) != ""){
									$strDeductibleRow .= ((empty($strEBInsTypeVal) == false) ? $strEBInsTypeVal : "Unknown");	
								}
								else{
									$strDeductibleRow .= ((empty($strEBInsTypeId) == false) ? $strEBInsTypeId : "Unknown");
								}
								$strDeductibleRow .= "</td>"; //Insurance Type - end
								*/
								/*$strDeductibleRow .= "<td>"; //Insurance statue - start
								if(trim($strEBInfoVal) != ""){
									$strDeductibleRow .= $strEBInfoVal;	
								}
								else{
									$strDeductibleRow .= ((empty($strEBInfoId) == false) ? $strEBInfoId."(Unknown)" : "");
								}
								$strDeductibleRow .= "</td>"; //Insurance statue - end
								*/
								$strDeductibleRow .= "<td>"; //Insurance Service Type - start
								if(trim($strEBSerTypeVal) != ""){
									$strDeductibleRow .= $strEBSerTypeVal;
								}
								else{
									$strDeductibleRow .= ((empty($intEBSerTypeId) == false) ? $intEBSerTypeId."(Unknown)" : "");
								}							
								$strDeductibleRow .= "</td>"; //Insurance Service Type - end
								
								$strDeductibleRow .= "<td>"; //Insurance Coverage Level - start
								if(trim($strEBCovLevelVal) != ""){
									$strDeductibleRow .= $strEBCovLevelVal;	
								}
								else{
									$strDeductibleRow .= ((empty($strEBCovLevelId) == false) ? $strEBCovLevelId."(Unknown)" : "");
								}
								$strDeductibleRow .= "</td>"; //Insurance Coverage Level - end
								
								$strDeductibleRow .= "<td>"; //Insurance Plan Coverage Description - Start
								if(trim($strEBPlanCoverageDesc) != ""){
									$strDeductibleRow .= $strEBPlanCoverageDesc;
								}						
								$strDeductibleRow .= "</td>"; //Insurance Plan Coverage Description - end
								
								$strDeductibleRow .= "<td>"; //Insurance Amount - start
								$strDeductibleRow .= ($intEBMonetaryAmount) ? "$".(float)$intEBMonetaryAmount : "";
								$strDeductibleRow .= "</td>"; //Insurance Amount - End
								
								$strDeductibleRow .= "<td>"; //Insurance Time Period - Start
								if(trim($strEBTimePeriodQuVal) != ""){
									$strDeductibleRow .= $strEBTimePeriodQuVal;	
								}
								else{
									$strDeductibleRow .= ((empty($intEBTimePeriodQuId) == false) ? $intEBTimePeriodQuId."(Unknown)" : "");
								}
								$strDeductibleRow .= "</td>"; //Insurance Time Period - End
								
								$strDeductibleRow .= "<td>"; //Insurance Percent  - start								
								$strDeductibleRow .= ($strEBPercent) ? (float)$strEBPercent * 100 : "";
								$strDeductibleRow .= "</td>"; //Insurance Percent  - end
								
								$strDeductibleRow .= "<td>"; //Insurance Quantity   - start
								$strDeductibleRow .= $intEBQuantity;
								if(trim($strEBQuantityQuVal) != ""){										
									$strDeductibleRow .= "<div>".$strEBQuantityQuVal."</div>";
								}
								else{
									$strDeductibleRow .= ((empty($strEBQuantityQuId) == false) ? "<div>".$strEBQuantityQuId."(Unknown) </div>" : "");
								}							
								$strDeductibleRow .= "</td>"; //Insurance Quantity   - end
								
								$strDeductibleRow .= "<td>"; //Insurance Authorization or Certification Indicator - start
								if(trim($strEBConditionRespVal) != ""){										
									$strDeductibleRow .= $strEBConditionRespVal;
								}
								else{
									$strDeductibleRow .= ((empty($strEBConditionRespId) == false) ? $strEBConditionRespId."(Unknown)" : "");
								}							
								$strDeductibleRow .= "</td>"; //Insurance Authorization or Certification Indicator   - end
								
								$strDeductibleRow .= "<td>"; //Insurance In Plan Network Indicator - start
								if(trim($strEBPlanConditionRespVal) != ""){										
									$strDeductibleRow .= $strEBPlanConditionRespVal;
								}
								else{
									$strDeductibleRow .= ((empty($strEBPlanConditionRespId) == false) ? $strEBPlanConditionRespId."(Unknown)" : "");
								}							
								$strDeductibleRow .= "</td>"; //Insurance In Plan Network Indicator - end
								
								$strDeductibleRow .= "<td>"; //Insurance Date - start
								if($srtDTPFormat == "D8"){
									$intDOBYear = substr($srtDTPDate,0,4);
									$intDOBMon = substr($srtDTPDate,4,2);
									$intDOBDay = substr($srtDTPDate,6,2);
									$srtDTPDateNew = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strDeductibleRow .= $srtDTPDateNew;
									if(trim($srtDTPVal) != ""){										
										$strDeductibleRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strDeductibleRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");
									}	
								}
								elseif($srtDTPFormat == "RD8"){
									$intDOBYear = substr($srtDTPDate1,0,4);
									$intDOBMon = substr($srtDTPDate1,4,2);
									$intDOBDay = substr($srtDTPDate1,6,2);
									$srtDTPDate1New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$intDOBYear = substr($srtDTPDate2,0,4);
									$intDOBMon = substr($srtDTPDate2,4,2);
									$intDOBDay = substr($srtDTPDate2,6,2);
									$srtDTPDate2New = $intDOBMon."-".$intDOBDay."-".$intDOBYear;
									$strDeductibleRow .= $srtDTPDate1New." - ".$srtDTPDate2New;	
									if(trim($srtDTPVal) != ""){
										$strDeductibleRow .= "<div>".$srtDTPVal."</div>";
									}
									else{
										$strDeductibleRow .= ((empty($intDTPId) == false) ? "<div>".$intDTPId."(Unknown) </div>" : "");									
									}
								}								
								$strDeductibleRow .= "</td>"; //Insurance Date - start
							$strDeductibleRow .= "</tr>";
							$intTempCounterC++;
							$intChkCounterD++;
						}
					}					
					
					$blDTPLoopSt = $blDTPDateSt = $blEBLoopSt = $blEBInfoSt = $blEBCovLevelSt = $blEBInsTypeSt = $blEBSerTypeSt = $blEBTimePeriodQuSt = $blEBQuantityQuSt = false;
					$blEBConditionRespSt = $blEBPlanConditionRespSt = false;
					$intDTPId = $intEBSerTypeId = $intEBTimePeriodQuId = $intEBMonetaryAmount = $intEBQuantity = 0;
					$strEBCovLevelId = $strEBCovLevelVal = $strEBInsTypeId = $strEBInsTypeVal = "";
					$strEBInfoVal = $strEBSerTypeVal = $strEBTimePeriodQuVal = $strEBQuantityQuId = $strEBQuantityQuVal = $strEBInfoId = $strEBPlanCoverageDesc = "";
					$strEBPercent = $strEBConditionRespId = $strEBConditionRespVal = $strEBPlanConditionRespId = $strEBPlanConditionRespVal = "";
					$blEBLoopSt = false;
				}
				if($blDTPLoopSt == true){
					if(($val["tag"] == "DateTimeQualifierID") && ($val["type"] == "complete") && ($intDTPId == 0)){
						$intDTPId = $val["value"];
					}
					elseif(($val["tag"] == "DateTimeQualifierIDVal") && ($val["type"] == "complete") && ($srtDTPVal == "")){
						$srtDTPVal = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriodFormatQualifier") && ($val["type"] == "complete") && ($srtDTPFormat == "")){
						$srtDTPFormat = $val["value"];
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "open") && ($blDTPDateSt == false)){
						$blDTPDateSt = true;
					}
					elseif(($val["tag"] == "DateTimePeriod") && ($val["type"] == "close") && ($blDTPDateSt == true)){
						$blDTPDateSt = false;
					}					
					if($blDTPDateSt == true){
						if(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "D8") && ($val["value"] != "")){
							$srtDTPDate = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate1") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($val["value"] != "")){
							$srtDTPDate1 = $val["value"];
						}
						elseif(($val["tag"] == "DateTimePeriodDate2") && ($val["type"] == "complete") && ($srtDTPFormat == "RD8") && ($val["value"] != "")){
							$srtDTPDate2 = $val["value"];
						}
					}
				}
				elseif($blEBLoopSt == true){
					if(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "open") && ($blEBInfoSt == false)){
						$blEBInfoSt = true;						
					}
					elseif(($val["tag"] == "EligibilityOrBenefitInformation") && ($val["type"] == "close") && ($blEBInfoSt == true)){
						$blEBInfoSt = false;
					}
					elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "open") && ($blEBCovLevelSt == false)){
						$blEBCovLevelSt = true;
					}
					elseif(($val["tag"] == "CoverageLevelCode") && ($val["type"] == "close") && ($blEBCovLevelSt == true)){
						$blEBCovLevelSt = false;
					}
					elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "open") && ($blEBSerTypeSt == false)){
						$blEBSerTypeSt = true;
					}
					elseif(($val["tag"] == "ServiceTypeCode") && ($val["type"] == "close") && ($blEBSerTypeSt == true)){
						$blEBSerTypeSt = false;
					}
					elseif(($val["tag"] == "PlanCoverageDescription") && ($val["type"] == "complete") && ($strEBPlanCoverageDesc = "")){
						$strEBPlanCoverageDesc = $val["value"];
					}
					elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "open") && ($blEBInsTypeSt == false)){
						$blEBInsTypeSt = true;
					}
					elseif(($val["tag"] == "InsuranceTypeCode") && ($val["type"] == "close") && ($blEBInsTypeSt == true)){
						$blEBInsTypeSt = false;
					}
					elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "open") && ($blEBTimePeriodQuSt == false)){
						$blEBTimePeriodQuSt = true;
					}
					elseif(($val["tag"] == "TimePeriodQualifier") && ($val["type"] == "close") && ($blEBTimePeriodQuSt == true)){
						$blEBTimePeriodQuSt = false;
					}
					elseif(($val["tag"] == "MonetaryAmount") && ($val["type"] == "complete")){
						$intEBMonetaryAmount = $val["value"];
					}
					elseif(($val["tag"] == "Percent") && ($val["type"] == "complete") && ($strEBPercent == "")){
						$strEBPercent = $val["value"];
					}
					elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "open") && ($blEBQuantityQuSt == false)){
						$blEBQuantityQuSt = true;
					}
					elseif(($val["tag"] == "QuantityQualifier") && ($val["type"] == "close") && ($blEBQuantityQuSt == true)){
						$blEBQuantityQuSt = false;
					}
					elseif(($val["tag"] == "Quantity") && ($val["type"] == "complete") && ($intEBQuantity == 0)){
						$intEBQuantity = $val["value"];
					}
					elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "open") && ($blEBConditionRespSt == false)){
						$blEBConditionRespSt = true;
					}
					elseif(($val["tag"] == "ConditionResponseCode") && ($val["type"] == "close") && ($blEBConditionRespSt == true)){
						$blEBConditionRespSt = false;
					}
					elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "open") && ($blEBPlanConditionRespSt == false)){
						$blEBPlanConditionRespSt = true;
					}
					elseif(($val["tag"] == "PlanConditionResponseCode") && ($val["type"] == "close") && ($blEBPlanConditionRespSt == true)){
						$blEBPlanConditionRespSt = false;
					}
					
					if($blEBInfoSt == true){						
						if(($val["tag"] == "EligibilityOrBenefitInformationId") && ($val["type"] == "complete") && ($strEBInfoId == 0)){
							$strEBInfoId = $val["value"];
						}
						elseif(($val["tag"] == "EligibilityOrBenefitInformationVal") && ($val["type"] == "complete") && ($strEBInfoVal == "")){
							$strEBInfoVal = $val["value"];
						}
					}
					elseif($blEBCovLevelSt == true){
						if(($val["tag"] == "CoverageLevelCodeId") && ($val["type"] == "complete") && ($strEBCovLevelId == "")){
							$strEBCovLevelId = $val["value"];
						}
						elseif(($val["tag"] == "CoverageLevelCodeVal") && ($val["type"] == "complete") && ($strEBCovLevelVal == "")){
							$strEBCovLevelVal = $val["value"];
						}
					}
					elseif($blEBInsTypeSt == true){
						if(($val["tag"] == "InsuranceTypeCodeId") && ($val["type"] == "complete") && ($strEBInsTypeId == "")){
							$strEBInsTypeId = $val["value"];
						}
						elseif(($val["tag"] == "InsuranceTypeCodeVal") && ($val["type"] == "complete") && ($strEBInsTypeVal == "")){
							$strEBInsTypeVal = $val["value"];
						}
					}
					elseif($blEBSerTypeSt == true){
						if(($val["tag"] == "ServiceTypeCodeId") && ($val["type"] == "complete") && ($intEBSerTypeId == 0)){
							$intEBSerTypeId = $val["value"];
						}
						elseif(($val["tag"] == "ServiceTypeCodeVal") && ($val["type"] == "complete") && ($strEBSerTypeVal == "")){
							$strEBSerTypeVal = $val["value"];
						}
					}
					elseif($blEBTimePeriodQuSt == true){
						if(($val["tag"] == "TimePeriodQualifierId") && ($val["type"] == "complete") && ($intEBTimePeriodQuId == 0)){
							$intEBTimePeriodQuId = $val["value"];
						}
						elseif(($val["tag"] == "TimePeriodQualifierVal") && ($val["type"] == "complete") && ($strEBTimePeriodQuVal == "")){
							$strEBTimePeriodQuVal = $val["value"];
						}
					}
					elseif($blEBQuantityQuSt == true){
						if(($val["tag"] == "QuantityQualifierId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
							$strEBQuantityQuId = $val["value"];
						}
						elseif(($val["tag"] == "QuantityQualifierVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
							$strEBQuantityQuVal = $val["value"];
						}
					}
					elseif($blEBConditionRespSt == true){
						if(($val["tag"] == "ConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBQuantityQuId == "")){
							$strEBConditionRespId = $val["value"];
						}
						elseif(($val["tag"] == "ConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBQuantityQuVal == "")){
							$strEBConditionRespVal = $val["value"];
						}
					}
					elseif($blEBPlanConditionRespSt == true){
						if(($val["tag"] == "PlanConditionResponseCodeId") && ($val["type"] == "complete") && ($strEBPlanConditionRespId == "")){
							$strEBPlanConditionRespId = $val["value"];
						}
						elseif(($val["tag"] == "PlanConditionResponseCodeVal") && ($val["type"] == "complete") && ($strEBPlanConditionRespVal == "")){
							$strEBPlanConditionRespVal = $val["value"];
						}
					}
				}
			}
		}
		return array($strDeductibleRow, $strCopayRow, $strCoIns);
	}

		
	function getEDITranReport($module, $subModule, $strTAReasonCode){
		return $this->objCoreLang->get_vocabulary($module, (string)trim($subModule), (string)$strTAReasonCode);
	}
	function getDateOfServiceFinalized($pid){
		$strDOS = "";
		$qryGetDOS = "SELECT DATE_FORMAT(chart_master_table.date_of_service, '%m-%d-%Y') AS date_of_service FROM chart_master_table 
						INNER JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id WHERE chart_master_table.patient_id = '".$pid."' 
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
				$qryGetDOSNew = "SELECT DATE_FORMAT(chart_master_table.update_date, '%m-%d-%Y') AS date_of_service FROM chart_master_table 
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
	
	function get270Header(){
		$interchange_control_standard_id = '^';
		$edi270TranDataFooter = '';
		$edi270TranDataFinal = '';
		$dbFacGroupID = 0;
		$retError = array();
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
			$strEmdeonTID = $dbGroupName = $dbGroupNPI = $dbGroupFederalTaxID = $dbGroupAdd1 = $dbGroupAdd2 = $dbGroupCity = $dbGroupState = $dbGroupZip = "";
			$dbGroupConName = $dbGroupTelephone = $dbGroupFax = $dbGroupEmail = "";
			$qryGetMedRecSubID = "select name as groupName, group_NPI as groupNPI, replace(group_Federal_EIN,'-','') as groupFederalTaxID, group_Address1 as groupAdd1, group_Address2 as groupAdd2, 
									group_City as groupCity, group_State as groupState, group_Zip as groupZip, Contact_Name as groupConName, prod_tid,  
									replace(group_Telephone,'-','') as groupTelephone, replace(group_Fax,'-','') as groupFax, group_Email as groupEmail 
									from groups_new where gro_id = '".$dbFacGroupID."'";
			$rsGetMedRecSubID = imw_query($qryGetMedRecSubID);
			if($rsGetMedRecSubID){
				if(imw_num_rows($rsGetMedRecSubID) > 0){		
					$rowGetMedRecSubID = imw_fetch_array($rsGetMedRecSubID);					
					$dbGroupName = trim($rowGetMedRecSubID["groupName"]);
					$dbGroupName = substr($dbGroupName, 0, 60);
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
					$strEmdeonTID = trim($rowGetMedRecSubID["prod_tid"]);//"16515462";					
				}
				imw_free_result($rsGetDefaultGroup);
			}
			
			if((empty($strEmdeonTID) == false) && (empty($dbGroupNPI) == false)){	
				$authorInfo = $secretInfo = $strNewRTMEMAXID = $strNewRTMEMAXIDRest = "";
				$intRecIdRest = $intEmdeonTDIRest = $newRTMEMAXID = 0;				
				
				$authorInfo = $this->makeSpace(10);
				$secretInfo = $this->makeSpace(10);
				$interchangeReceiverID = "EMDEON";
				
				$intRecIdRest = 15 - strlen($interchangeReceiverID);
				$recieveSpace = "";
				$recieveSpace = $this->makeSpace($intRecIdRest);				
				$intEmdeonTDIRest = 15 - strlen($strEmdeonTID);
				$submitterSpace = "";
				$submitterSpace = $this->makeSpace($intEmdeonTDIRest);
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
				
				$ediDate1 = $ediDate2 = $ediTime = $ediTime1 = "";
				$ediDate1 = date('ymd');
				$ediDate2 = date('Ymd');
				$ediTime = date('Hi');
				$ediTime1 = date('His');
				$transactionSetPurposeCode = '13';
				
				$edi270TranDataFinal  = 'ISA*00*'.$authorInfo.'*00*'.$secretInfo.'*ZZ*'.$strEmdeonTID.$submitterSpace.'*ZZ*'.$interchangeReceiverID.$recieveSpace.'*'.$ediDate1.'*'.$ediTime.'*'.$interchange_control_standard_id.'*00501*'.$InterCtrlNumber.'*0*P*:~';
				//$edi270TranDataFinal .= 'IEA*1*'.$InterCtrlNumber.'~';
				
				//FUNCTIONAL GROUP HEADER/GS
				$edi270TranDataFinal .= 'GS*HS*'.$strEmdeonTID.'*'.$interchangeReceiverID.'*'.$ediDate2.'*'.$ediTime.'*'.$InterCtrlNumber.'*X*005010X279A1~';
				//$edi270TranDataFinal .= 'GE*1*'.$newRTMEMAXID.'~';
				
				//TRANSACTION SET HEADER/ ST
				$edi270TranDataFinal .= 'ST*270*'.$InterCtrlNumber.'*005010X279A1~';
				$this->edi270STCounter++;
				$edi270TranDataFinal .= 'BHT*0022*'.$transactionSetPurposeCode.'*ALL*'.$ediDate2.'*'.$ediTime.'~';
				$this->edi270STCounter++;
				
				$edi270TranDataFooter  = 'SE*SE_SEGMENT_LINE_COUNTER_TO_REPLACE*'.$InterCtrlNumber.'~';
				
				$edi270TranDataFooter .= 'GE*1*'.$InterCtrlNumber.'~';
				$edi270TranDataFooter .= 'IEA*1*'.$InterCtrlNumber.'~';
			}
			else{
				$retError[] = " - Group NPI or TID is Missing!";
			}
		}
		else{
			$retError[] = " - No Default Facility Found!";
		}
		return array($edi270TranDataFinal,$edi270TranDataFooter,$retError);
	}
	
	function make270EDIEmdeon5010($rqInsRecId, $from = "insTab", $patId = 0, $elReqDate = "", $intInsCaseId = 0,$NoHeaderFooter=false){
		//$rqInsRecId -> Insurence Data Id, $from -> Insurence Tab or Scheduler, $patId -> Eligibility ask for patient
		//$elReqDate -> Eligibility date
		//Getting default group of head quarter facility
		$dbFacGroupID = 0;
		$imedicDOS = "";
		$retError = array();
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
			$strEmdeonTID = $dbGroupName = $dbGroupNPI = $dbGroupFederalTaxID = $dbGroupAdd1 = $dbGroupAdd2 = $dbGroupCity = $dbGroupState = $dbGroupZip = "";
			$dbGroupConName = $dbGroupTelephone = $dbGroupFax = $dbGroupEmail = "";
			$qryGetMedRecSubID = "select name as groupName, group_NPI as groupNPI, replace(group_Federal_EIN,'-','') as groupFederalTaxID, group_Address1 as groupAdd1, group_Address2 as groupAdd2, 
									group_City as groupCity, group_State as groupState, group_Zip as groupZip, Contact_Name as groupConName, prod_tid,  
									replace(group_Telephone,'-','') as groupTelephone, replace(group_Fax,'-','') as groupFax, group_Email as groupEmail 
									from groups_new where gro_id = '".$dbFacGroupID."'";
			$rsGetMedRecSubID = imw_query($qryGetMedRecSubID);
			if($rsGetMedRecSubID){
				if(imw_num_rows($rsGetMedRecSubID) > 0){		
					$rowGetMedRecSubID = imw_fetch_array($rsGetMedRecSubID);					
					$dbGroupName = trim($rowGetMedRecSubID["groupName"]);
					$dbGroupName = substr($dbGroupName, 0, 60);
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
					$strEmdeonTID = trim($rowGetMedRecSubID["prod_tid"]);//"16515462";					
				}
				imw_free_result($rsGetDefaultGroup);
			}
			
			if((empty($strEmdeonTID) == false) && (empty($dbGroupNPI) == false)){	
				$authorInfo = $secretInfo = $strNewRTMEMAXID = $strNewRTMEMAXIDRest = "";
				$intRecIdRest = $intEmdeonTDIRest = $newRTMEMAXID = 0;				
				
				$authorInfo = $this->makeSpace(10);
				$secretInfo = $this->makeSpace(10);
				$interchangeReceiverID = "EMDEON";
				
				$intRecIdRest = 15 - strlen($interchangeReceiverID);
				$recieveSpace = "";
				$recieveSpace = $this->makeSpace($intRecIdRest);				
				$intEmdeonTDIRest = 15 - strlen($strEmdeonTID);
				$submitterSpace = "";
				$submitterSpace = $this->makeSpace($intEmdeonTDIRest);
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
				$dbInsCompName = $dbInsCompPayId = $dbSubscriberPlan = $dbSubscriberGroup = $dbSubscriberLName = $dbSubscriberFName = $dbSubscriberMName = $dbInsPolicyClaimNo = $dbSubscriberAdd1 = $dbSubscriberAdd2 = "";
				$dbSubscriberCity = $dbSubscriberState = $dbSubscriberZip = $dbSubscriberDOB = $dbSubscriberGender = $dbSubscriberDate = $dbSubscriberSS = $dbSubscriberRelShip = "";
				$dbPDFName = $dbPDLName = $dbPDMName = $dbPDSS = $dbPDStreet1 = $dbPDStreet2 = $dbPDZip = $dbPDCity = $dbPDState = $dbPDDOB = $dbPDGender = "";
				$dbInsPatId = $dbInsId = 0;
				if($from == "insTab"){
					$getPatInsData = "select insComp.name as insCompName, insComp.emdeon_payer_eligibility as insCompPayId, insData.plan_name as subscriberPlan, insData.group_number as subscriberGroup, 
										insData.subscriber_lname as subscriberLName, insData.subscriber_fname as subscriberFName, insData.subscriber_suffix as subscriberSuffix, 
										insData.subscriber_mname as subscriberMName, insData.policy_number as insPolicyClaimNo, insData.subscriber_street as subscriberAdd1, 
										insData.subscriber_street_2 as subscriberAdd2, insData.subscriber_city as subscriberCity, insData.subscriber_state as subscriberState, 
										insData.subscriber_postal_code as subscriberZip, replace(DATE_FORMAT(insData.subscriber_DOB, '%Y%m%d'),'-','') as subscriberDOB, insData.subscriber_sex as subscriberGender, 
										replace(DATE_FORMAT(insData.effective_date, '%Y%m%d'),'-','') as subscriberDate, insData.pid as insPatId, insData.id as insId,insData.type AS InsDataTypePriSec,
										replace(insData.subscriber_ss,'-','') as subscriberSS, insData.subscriber_relationship as insRelShip, pd.fname as pdFName, pd.lname as pdLName,
										pd.mname as pdMName, replace(pd.ss,'-','') as pdSS, pd.street as pdStreet1, pd.street2 as pdStreet2, pd.postal_code as pdZip, pd.city as pdCity, 
										pd.state as pdState, replace(DATE_FORMAT(pd.DOB, '%Y%m%d'),'-','') as pdDOB, pd.sex as pdSex
										from insurance_data insData 
										LEFT JOIN insurance_companies insComp on insComp.id = insData.provider
										LEFT JOIN patient_data pd on pd.id = insData.pid
										where insData.id = '".$rqInsRecId."';
										";
				}
				elseif($from == "scheduler"){
					$getPatInsData = "select insComp.name as insCompName, insComp.emdeon_payer_eligibility as insCompPayId, insData.plan_name as subscriberPlan, insData.group_number as subscriberGroup,
										insData.subscriber_lname as subscriberLName, insData.subscriber_fname as subscriberFName, insData.subscriber_suffix as subscriberSuffix, 
										insData.subscriber_mname as subscriberMName, insData.policy_number as insPolicyClaimNo, insData.subscriber_street as subscriberAdd1, 
										insData.subscriber_street_2 as subscriberAdd2, insData.subscriber_city as subscriberCity, insData.subscriber_state as subscriberState, 
										insData.subscriber_postal_code as subscriberZip, replace(DATE_FORMAT(insData.subscriber_DOB, '%Y%m%d'),'-','') as subscriberDOB, 
										insData.subscriber_sex as subscriberGender, replace(DATE_FORMAT(insData.effective_date, '%Y%m%d'),'-','') as subscriberDate, 
										insData.pid as insPatId, insData.id as insId, replace(insData.subscriber_ss,'-','') as subscriberSS, insData.subscriber_relationship as insRelShip,insData.type AS InsDataTypePriSec,
										pd.fname as pdFName, pd.lname as pdLName, pd.mname as pdMName, replace(pd.ss,'-','') as pdSS, pd.street as pdStreet1, pd.street2 as pdStreet2, 
										pd.postal_code as pdZip, pd.city as pdCity, pd.state as pdState, replace(DATE_FORMAT(pd.DOB, '%Y%m%d'),'-','') as pdDOB, pd.sex as pdSex
										from insurance_data insData 
										LEFT JOIN insurance_companies insComp on insComp.id = insData.provider
										LEFT JOIN patient_data pd on pd.id = insData.pid
										where insData.pid = '".$patId."' and insData.actInsComp = '1' ";
										if((int)$intInsCaseId > 0){
											$getPatInsData .= " and insData.ins_caseid = '".$intInsCaseId."' ";
										} 
										$getPatInsData .= " and insData.type = 'primary' 
										and insComp.claim_type = '0'
										ORDER BY insData.id DESC LIMIT 1;
										";
				}
				$rsPatInsData = imw_query($getPatInsData);
				
				if($rsPatInsData){
					if(imw_num_rows($rsPatInsData) > 0){
						$rowPatInsData = imw_fetch_array($rsPatInsData);
						$dbInsCompName = trim($rowPatInsData["insCompName"]);
						$dbInsCompPayId = trim($rowPatInsData["insCompPayId"]);
						$dbSubscriberPlan = trim($rowPatInsData["subscriberPlan"]);
						$dbSubscriberGroup = trim($rowPatInsData["subscriberGroup"]);
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
						$dbSubscriberDOB = (int)trim($rowPatInsData["subscriberDOB"]);
						$dbSubscriberGender = trim($rowPatInsData["subscriberGender"]);
						$dbSubscriberDate = trim($rowPatInsData["subscriberDate"]);
						$dbInsPatId = trim($rowPatInsData["insPatId"]);
						$dbInsId = trim($rowPatInsData["insId"]);
						$dbInsDataTypePriSec = trim($rowPatInsData["InsDataTypePriSec"]);
						$dbSubscriberSS = (int)trim($rowPatInsData["subscriberSS"]);
						$dbSubscriberRelShip = trim($rowPatInsData["insRelShip"]);						
						$dbPDFName = trim($rowPatInsData["pdFName"]);
						$dbPDLName = trim($rowPatInsData["pdLName"]);
						$dbPDMName = trim($rowPatInsData["pdMName"]);
						$dbPDSS = trim($rowPatInsData["pdSS"]);
						$dbPDStreet1 = trim($rowPatInsData["pdStreet1"]);
						$dbPDStreet2 = trim($rowPatInsData["pdStreet2"]);
						$dbPDZip = trim($rowPatInsData["pdZip"]);
						$dbPDCity = trim($rowPatInsData["pdCity"]);
						$dbPDState = trim($rowPatInsData["pdState"]);
						$dbPDDOB = trim($rowPatInsData["pdDOB"]);
						$dbPDGender = trim($rowPatInsData["pdSex"]);
						
						//Geting Payer information from the Emdeon database
						$intEmInsCompID = 0;
						$strEmInsCompName = $strEmInsCompPayId = "";
						$arrSearchOption = array();
						
						$interchange_control_standard_id = '^';
						include(dirname(__FILE__)."/createCommon270EDI.php");
					}
					else{
						$retError[] = " - Subscriber Data Not Found!";
					}
				}
				else{
					$retError[] = " - Subscriber Data Not Found!";
				}	
			}
			else{
				$retError[] = " - Group NPI or TID is Missing!";
			}
		}
		else{
			$retError[] = " - No Default Facility Found!";
		}
		return array($edi270TranDataFinal, $dbInsPatId, $dbInsId, $imedicDOS, $dbFacGroupID, $retError,$dbInsDataTypePriSec);
	}

	function send270RequestEmdeon5010($EDI270, $patId, $patInsId, $imedicDOS, $groupID, $intPatAppIdSA = 0,$CL_url='',$dbInsDataTypePriSec=''){
		$ins_type = 'commercial';

		//GETTING GROUP CREDENTIALS.
		require_once(dirname(__FILE__)."/class.electronic_billing.php");
		
		$objEBilling = new ElectronicBilling();

		if(intval($groupID)==0){
			$HQ_fac		= $objEBilling->get_facility_detail();
			$groupID	= $HQ_fac['default_group'];
		}
		if(intval($groupID)>0){
			$GRP_rs		= $objEBilling->get_groups_detail($groupID);
			$this->soapUser = $GRP_rs['user_id'];
			$this->soapPass = $GRP_rs['user_pwd'];			
		}

		if($patInsId != "")
		{
			$ins_type_qry = 'SELECT id FROM vision_share_cert_config WHERE ins_comp_id = (SELECT provider FROM insurance_data WHERE id = '.$patInsId.' LIMIT 1)';
			$ins_type_qry_obj = imw_query($ins_type_qry);
			if(imw_num_rows($ins_type_qry_obj) > 0)
			{
				$ins_type = 'medicare';
			}
		}
		$intPatAppIdSA = trim($intPatAppIdSA);
		$rtme_doctor = 0;
		$rtme_facility = 0;
		if($intPatAppIdSA != 0 && $intPatAppIdSA != "")
		{
			$ap_req_qry = "SELECT sa_doctor_id, sa_facility_id FROM schedule_appointments WHERE id = ".$intPatAppIdSA;
			$ap_result_obj = imw_query($ap_req_qry);
			if(imw_num_rows($ap_result_obj) == 1)
			{
				$result_ap_data = imw_fetch_assoc($ap_result_obj);
				$rtme_doctor = $result_ap_data['sa_doctor_id'];
				$rtme_facility = $result_ap_data['sa_facility_id'];				
			}
		}
		$strValueCoIns = $strValueD = $strValueCP = "";
		$blCopayExits = false;
		$retErrorAns = array();
		$transectionError = "";
		$intNewId = 0;
		if(empty($EDI270) == false){
			$EDI270 = strtoupper($EDI270);
			
			if($this->SOAPClientRec !== false || $CL_url!=''){
				$fileNameEDI = $dataEDI = $pathToWrite270EDI = $ediToSend = "";
				$fileNameEDI = "RTE_".date("Y-m-d-H-i-s_").$patId."_270_5010";
				$upLoadPath = $this->oSaveFile->upDir.'/';
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
					$ecData = "";
					$ecData = file_get_contents($pathToWrite270EDI);
					if($CL_url=='') $ecData = base64_encode($ecData); //encrypting for Emdeon
					
					$sqlEDIPath = "";
					$sqlEDIPath = $patientDir.$patientDirVs270Dir.$fileNameEDI.".txt";
					$qryInsert = "insert into real_time_medicare_eligibility (request_270_file_path, request_date_time, request_operator, patient_id, ins_data_id,ins_data_type, imedic_DOS,eligibility_ask_from,hipaa_5010,doctor_id,facility,ins_type) 
									values ('".$sqlEDIPath."', NOW(), '".$_SESSION['authId']."', '".$patId."', '".$patInsId."', '".$dbInsDataTypePriSec."', '".$imedicDOS."','1','1','".$rtme_doctor."','".$rtme_facility."','".$ins_type."')";
					$rsQryInsert =  imw_query($qryInsert);				
					$intNewId = imw_insert_id();
					
					if($CL_url==''){
						$arrParam = array("sUserID" => $this->soapUser,"sPassword" => $this->soapPass, "sMessageType" => "X12", "sEncodedRequest" => $ecData);
						$sendRq = $this->objSOAPClient->SendRequest($arrParam);
						//pre($sendRq,1);
						$arrSendRq = $arrSendRequestResult = array();
						$arrSendRq = get_object_vars($sendRq);
						$arrSendRequestResult = get_object_vars($arrSendRq["SendRequestResult"]);					
						//pre($arrSendRequestResult,1);
						$arrByPass = array(false,0);
					}else{
						//----SENDING REQUEST FOR PI------
						$cur 		= curl_init($CL_url."transfer/rt_elig.php?output=HTML");
						curl_setopt($cur, CURLOPT_POST, 1);
						curl_setopt($cur, CURLOPT_POSTFIELDS,'data='.$ecData);
						curl_setopt($cur, CURLOPT_USERPWD, $this->soapUser.":".$this->soapPass);
						curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
						curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, FALSE);
						curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
						curl_setopt($cur, CURLOPT_CONNECTTIMEOUT, 10);
						curl_setopt($cur, CURLOPT_TIMEOUT, 90);
						$output	=  curl_exec($cur);
						$error	= curl_error($cur);
						curl_close($cur);
					 	file_put_contents(dirname(__FILE__).'/../../html/temp/RTE_'.date('YmdHis').'.txt',$output."\n\n\n".$error);

										
					}

					if($CL_url != '' && isset($error) && !empty($error)){
						$retErrorAns[] = " - Error: Curl Error"."\n"." - Response: ".$error."\n";
					}else if($CL_url == '' && in_array($this->askKeyExitArray("ErrorCode",$arrSendRequestResult),$arrByPass) == false){
						$strErrorCode = $strErrorDesc = $errorResolution = "";
						$strErrorCode = $arrSendRequestResult["ErrorCode"];
						$strErrorDesc = base64_decode($arrSendRequestResult["Response"]);
						$errorResolution = $this->getEDITranReport("emdeon", "ITS", $strErrorCode);
						$errorResolution = (empty($errorResolution) == false ? $errorResolution : $this->error);
						$retErrorAns[] = " - Error: ".$strErrorCode."\n"." - Response: ".$strErrorDesc."\n"." - Resolution: ".$errorResolution;
					}else if($CL_url != '' && (!isset($error) || empty($error)) && ($output == strip_tags($output))){
						$strErrorCode = $strErrorDesc = $errorResolution = "";
						$strErrorDesc = $output;
						//$errorResolution = $this->getEDITranReport("emdeon", "ITS", $strErrorCode);
						//$errorResolution = (empty($errorResolution) == false ? $errorResolution : $this->error);
						$retErrorAns[] = " - Error: ".$output."\n";
					}else{
						$EDI271Response = $arrSendRequestResult["Response"];
						if($CL_url=='') $EDI271Response = base64_decode($EDI271Response);
						else if($CL_url!=''){
							$RTEHTML_data	= $output;
							$EDI271Response = $objEBilling->getCommentRTEdata($RTEHTML_data);
						}
	
						$arrEDI271Response = $arrVisionShareResponseXML = array();					
						$arrEDI271Response = explode("\n", $EDI271Response);													
						//pre($arrEDI271Response,1);						
						$blACKExits = $bl997Exits = $bl999Exits = $bl271Exits = false;
						$strACK = $returnTA1Error = $strEDI997 = $strEDI999 = $strEDI271 = "";						
						foreach($arrEDI271Response as $key => $val){					
							if((strpos($val, "TA1") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl271Exits == false)){
								$strACK = trim($val);
								$blACKExits = true;
							}
							elseif((strpos($val, "ST*997*") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl271Exits == false)){
								$strEDI997 = trim($val);
								$bl997Exits = true;
							}
							elseif((strpos($val, "ST*999*") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl999Exits == false) && ($bl271Exits == false)){
								$strEDI999 = trim(strtoupper($val));
								$bl999Exits = true;
							}
							elseif((strpos($val, "ST*271*") !== false) && ($blACKExits == false) && ($bl997Exits == false) && ($bl271Exits == false)){
								$strEDI271 = trim($val);
								$bl271Exits = true;
							}
						}
						//pre(var_dump($blACKExits),1);
						//ww
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
								$errorMsg = $this->getEDITranReport("vision_share", "TA", $strTAReasonCode);
								$errorMsg = (empty($errorMsg) == false ? $errorMsg : $this->error);
								$returnTA1Error = "Error INTERCHANGE ACKNOWLEDGMENT: \n ".$errorMsg." \n ";
								$transectionError .= $returnTA1Error;
								$retErrorAns[] = " - ".$returnTA1Error;
							}
						}						
						elseif($bl997Exits == true){
							$str997 = $strTAReasonCode = "";
							$arr997 = $arrTA = $dataArr = array();
							//echo $strEDI997;						
							$dataArr = preg_split('/(AK2\*270\*)|(AK5\*)|(AK9\*)/',$strEDI997);							
							//pre($dataArr,1);
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
										//pre($arrVal,1);
										if(trim($arrVal[0]) == "AK3"){
											$intAk3ReasonCode = (int)trim($arrVal[4]);
											$errorMsg = "";
											$errorMsg = $this->getEDITranReport("vision_share", "AK3", $intAk3ReasonCode);
											$errorMsg = (empty($errorMsg) == false ? $errorMsg : $this->error);
											$return997Error = "Error in Segment : ".trim($arrVal[1])." \n";
											$return997Error .= "Loop : ".trim($arrVal[3])." \n";
											$return997Error .= "Due to : ".trim($errorMsg)." \n";
											$retErrorAns[] = " - ".$return997Error;
											$transectionError .= $return997Error;
										}
										elseif(trim($arrVal[0]) == "AK4"){
											$intAk3ReasonCode = (int)trim($arrVal[3]);
											$errorMsg = "";
											$errorMsg = $this->getEDITranReport("vision_share", "AK3", $intAk3ReasonCode);
											$errorMsg = (empty($errorMsg) == false ? $errorMsg : $this->error);
											$return997Error = "Position in Segment : ".trim($arrVal[1])." \n";
											$return997Error .= "Data Elemnt Ref Number : ".trim($arrVal[2])." \n";
											$return997Error .= "Data Element Syntax Error : ".trim($errorMsg)." \n";
											$retErrorAns[] = " - ".$return997Error;
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
											$errorMsg = $this->getEDITranReport("vision_share", "IK3", $intAk3ReasonCode);
											$return999Error = "Error in Segment : ".trim($arrVal[1])." \n";
											$return999Error .= "Position in Segment : ".trim($arrVal[2])." \n";
											$return999Error .= "Loop : ".trim($arrVal[3])." \n";
											$return999Error .= "Due to : ".trim($errorMsg)." \n";										
											$retErrorAns[] = " - ".$return999Error;
											$transectionError .= $return999Error;
										}
										elseif(trim($arrVal[0]) == "IK4"){
											$intAk3ReasonCode = trim($arrVal[3]);										
											$errorMsg = $this->getEDITranReport("vision_share", "IK4", $intAk3ReasonCode);
											$return999Error = "Position in Segment : ".trim($arrVal[1])." \n";
											$return999Error .= "Data Elemnt Ref Number : ".trim($arrVal[2])." \n";
											$return999Error .= "Data Element Syntax Error : ".trim($errorMsg)." \n";										
											$retErrorAns[] = " - ".$return999Error;
											$transectionError .= $return999Error;
										}
										elseif(trim($arrVal[0]) == "IK5"){
											$strIk5ErrorCode = "";
											$strIk5ErrorCode = trim($arrVal[1]);
											$errorMsg = $this->getEDITranReport("vision_share", "IK5", $strIk5ErrorCode);
											$return999Error = "Status : ".trim($errorMsg)." \n";
											$strIk3ReasonCode = "";
											$strIk3ReasonCode = trim($arrVal[2]);										
											$errorMsg = $this->getVisionShareError("vision_share", "IK5", $strIk3ReasonCode);
											$return999Error .= "Implementation Transaction Set Syntax Error : ".trim($errorMsg)." \n";										
											$retErrorAns[] = " - ".$return999Error;
											$transectionError .= $return999Error;
										}
									}
								}								
							}
						}
						elseif($bl271Exits == true){
							$arrEDI271 = array();
							$arrEDI271 = explode("~",$strEDI271);							
							//pre($arrEDI271,1);
							$return271Error = 
							$strHL3RQLName = 
							$strHL3RQFName = $strHL3RQMName = $strHL3RQPolicyNo = $strHL3RQAdd1 = $strHL3RQAdd2 = $strHL3RQCity = $strHL3RQState = $strHL3RQZip = $strHL3RQPatDOB = "";
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
									$strHL1RQ = trim($val);
									$arrHL1RQ = explode("*", $strHL1RQ);
									$strHL1ReasonCode = trim($arrHL1RQ[3]);
									$errorMsg = "";
									$errorMsg = $this->getEDITranReport("vision_share_271", "HL1", $strHL1ReasonCode);
									$errorMsg = (empty($errorMsg) == false ? $errorMsg : $this->error);
									$return271Error .= " - Error : INFORMATION SOURCE LEVEL \n";
									$return271Error .= " - Loop : 2000A \n";
									$return271Error .= " - Due to : ".trim($errorMsg)." \n";
									$FollowUPAction  = "";
									$FollowUPAction = $this->getEDITranReport("vision_share_271", "HL1-Follow-upAction", trim($arrHL1RQ[4]));
									$FollowUPAction = (empty($FollowUPAction) == false ? $FollowUPAction : $this->error);
									$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";									
								}
								elseif((strpos($val, "AAA") !== false) && ($blHL2RQSt == true) && ($blHL2AAA == false)){
									$blHL2AAA = true;
									$strHL2RQ = trim($val);
									$arrHL2RQ = explode("*", $strHL2RQ);
									$strHL2ReasonCode = trim($arrHL2RQ[3]);
									$errorMsg = "";
									$errorMsg = $this->getEDITranReport("vision_share_271", "HL2", $strHL2ReasonCode);
									$errorMsg = (empty($errorMsg) == false ? $errorMsg : $this->error);
									$return271Error .= " - Error : INFORMATION RECEIVER LEVEL \n";
									$return271Error .= " - Loop : 2100B \n";
									$return271Error .= " - Due to : ".trim($errorMsg)." \n";
									$FollowUPAction  = "";
									$FollowUPAction = $this->getEDITranReport("vision_share_271", "HL2-Follow-upAction", trim($arrHL2RQ[4]));
									$FollowUPAction = (empty($FollowUPAction) == false ? $FollowUPAction : $this->error);
									$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";
								}
								elseif((strpos($val, "AAA") !== false) && ($blHL3RQSt == true) && ($blHL3AAA == false)){
									$blHL3AAA = true;
									$strHL3RQ = trim($val);
									$arrHL3RQ = explode("*", $strHL3RQ);
									//pre($arrHL3RQ,1);																
									$strHL3ReasonCode = trim($arrHL3RQ[3]);
									$errorMsg = "";
									$errorMsg = $this->getEDITranReport("vision_share_271", "HL3", $strHL3ReasonCode);
									$errorMsg = (empty($errorMsg) == false ? $errorMsg : $this->error);
									$return271Error .= " - Error : SUBSCRIBER NAME LEVEL \n";
									$return271Error .= " - Loop : 2100C \n";
									$return271Error .= " - Due to : ".trim($errorMsg)." \n";
									$FollowUPAction  = "";
									$FollowUPAction = $this->getEDITranReport("vision_share_271", "HL3-Follow-upAction", trim($arrHL3RQ[4]));
									$FollowUPAction = (empty($FollowUPAction) == false ? $FollowUPAction : $this->error);
									$return271Error .= " - Follow-up Action : ".trim($FollowUPAction)." \n";
								}
								elseif((strpos($val, "AAA") !== false) && ($blHL4RQSt == true) && ($blHL4AAA == false)){
									$blHL4AAA = true;									
									$strHL4RQ = trim($val);
									$arrHL4RQ = explode("*", $strHL4RQ);
									//pre($arrHL4RQ,1);																
									$strHL4ReasonCode = trim($arrHL4RQ[3]);
									$errorMsg = "";
									$errorMsg = $this->getEDITranReport("vision_share_271", "HL4", $strHL4ReasonCode);
									$errorMsg = (empty($errorMsg) == false ? $errorMsg : $this->error);
									$return271Error .= " - Error : DEPENDENT NAME LEVEL \n";
									$return271Error .= " - Loop : 2100D \n";
									$return271Error .= " - Due to : ".trim($errorMsg)." \n";
									$FollowUPAction  = "";
									$FollowUPAction = $this->getEDITranReport("vision_share_271", "HL4-Follow-upAction", trim($arrHL4RQ[4]));
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
									$strHL3RQAddSeg = "";
									$strHL3RQAddSeg = trim($val);
									$arrHL3RQAdd = explode("*", $strHL3RQAddSeg);																
									$strHL3RQAdd1 = ucwords(strtolower(trim($arrHL3RQAdd[1])));
									$strHL3RQAdd2 = ucwords(strtolower(trim($arrHL3RQAdd[2])));
									$blPatSAdd = true;
								}
								elseif((strpos($val, "N4") !== false) && ($blHL3RQSt == true) && ($blPatCSZAdd == false)){
									$strHL3RQCSZSeg = "";
									$strHL3RQCSZSeg = trim($val);
									$arrHL3RQCSZ = explode("*", $strHL3RQCSZSeg);																
									$strHL3RQCity = ucwords(strtolower(trim($arrHL3RQCSZ[1])));
									$strHL3RQState = trim($arrHL3RQCSZ[2]);
									$strHL3RQZip = trim($arrHL3RQCSZ[3]);
									$blPatCSZAdd = true;
								}
								elseif((strpos($val, "DMG") !== false) && ($blHL3RQSt == true) && ($blPatDOB == false)){
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
										$blPatDOB = true;
									}
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
						else if($EDI271Response==''){
							$transectionError = 'No response from clearing house';
							$errorDetailArr = $errorString = array();
						}
						
						if(empty($return271Error) == false){					
							$transectionError .= $return271Error;
							$retErrorAns[] = $return271Error;
							$errorDetailArr = $errorString = array();
						}				
						$patientDirVs271Dir = "vs_271_response/";
						if(!is_dir($upLoadPath.$patientDir.$patientDirVs271Dir)){
							//Create patient VS 271 responce directory
							mkdir($upLoadPath.$patientDir.$patientDirVs271Dir, 0700, true);
						}
						$fileNameEDI271 = "RTE_".date("Y-m-d-H-i-s_").$patId."_271_5010";
						$pathToWrite271EDI = $upLoadPath.$patientDir.$patientDirVs271Dir.$fileNameEDI271.".txt";
						file_put_contents($pathToWrite271EDI,$EDI271Response);
						$sqlEDIPathResp = "";
						$sqlEDIPathResp = $patientDir.$patientDirVs271Dir.$fileNameEDI271.".txt";
						//eeeeee						
						if(($intNewId > 0) && (file_exists($pathToWrite271EDI) == true)){
							//erererere
							$strXMLEB = $strEBResponce = $strEBInsuranceTypeCode = "";
							$intServiceTypeCode = 0;
							//$arrEBResponce = $arrEBInsuranceTypeCode = array();
							$strXMLEB .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
							$strXMLEB .= "<DTPEBLOOP>";
							$arrLastEB = $DTPDetailArr = $EBDTPDetailArr = array();
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
							foreach($DTPDetailArr as $key => $val){
								$strDTPValueMian = "";
								$strDTPValueMian = trim($val["DTP"]);
								//echo $strDTPValue;
								//die;
								$arrTempEBDTP = array();
								foreach($EBDTPDetailArrRefined as $keyEBDTP => $valEBDTP){
									//pre($valEBDTP,1);
									$strDTPValueInternal = "";
									$strDTPValueInternal = trim($valEBDTP["DTP"]);
									
									if($strDTPValueMian == $strDTPValueInternal){
										//$DTPDetailArr[$key]["EB"] = array(trim($valEBDTP[$keyEBDTP]["EB"]));
										//$DTPDetailArr[$key]["EB"] = array(array("EB" => array("EB" => trim($valEBDTP["EB"]), "MSG" => trim($valEBDTP["MSG"])), "EB_DTP" => $strDTPValueInternal));
										$arrTempEBDTP[] = array("EB" => array("EB" => trim($valEBDTP["EB"]), "MSG" => trim($valEBDTP["MSG"])), "EB_DTP" => $strDTPValueInternal);
										//break;
									}
									elseif(empty($strDTPValueInternal) == true){
										$arrTempEBDTP[] = array("EB" => array("EB" => trim($valEBDTP["EB"]), "MSG" => trim($valEBDTP["MSG"])), "EB_DTP" => $strDTPValueInternal);
									}
									//pre($valEBDTP,1);
								}
								$DTPDetailArr[$key]["EB"] = $arrTempEBDTP;
							}
							//pre($DTPDetailArr,1);
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
														$oriVal = $this->getEDITranReport("vision_share_271", "DTP", $strDTPValVal);
															$strXMLEB .= "<DateTimeQualifierID>";
															$strXMLEB .= $strDTPValVal;
															$strXMLEB .= "</DateTimeQualifierID>";
															
															$strXMLEB .= "<DateTimeQualifierIDVal>";
															$strXMLEB .= ((empty($oriVal) == false) ? $oriVal : "");
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
											//if((is_array($valval) == true) && (count($valval) > 0)){
												//echo count($valval);
												//pre($valval,1);
												$strXMLEB .= "<EBLOOPINFO>";
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
													//$strXMLEB .= "<EBLOOPINFO>";
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
																$oriVal = $this->getEDITranReport("vision_share_271", "DTP", $strEBDTPVal);
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
													//pre($arrEBVal,1);
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
														
														if(empty($strValueD) == true){// Deductible
															//pre($arrEBVal,1);	
															if((trim(strtoupper($arrEBVal[0])) == "C") && (trim(strtoupper($arrEBVal[1])) == "IND") && ((int)$arrEBVal[5] == 29)){
																$strValueD = trim($arrEBVal[3])."-".$arrEBVal[2]."-"."IND"."-".$arrEBVal[4]."-".$arrEBVal[6]."-"."29"."-".$arrEBVal[7]."-".$arrEBVal[9]."-".$arrEBVal[8]."-".$arrEBVal[10]."-".$arrEBVal[11];
															}
														}
														
														$orival = "";
														$strEBValVal = trim($strEBValVal);
														switch ($intEBValKey):
															case 0:
																$strXMLEB .= "<EligibilityOrBenefitInformation>";																					
																$orival = $this->getEDITranReport("vision_share_271", "EB", $strEBValVal);
																$strEBValVal = (string)$strEBValVal;
																//die("aaaaaaa".$strEBValVal."");
																/*if(
																($strEBValVal == "1") || ($strEBValVal == "2") || ($strEBValVal == "3") || ($strEBValVal == "4") || ($strEBValVal == "5") || 
																($strEBValVal == "6") || ($strEBValVal == "7") || ($strEBValVal == "8")
																){
																	if(empty($strEBResponceTemp) == true){
																		$strEBResponceTemp = $strEBValVal;
																	}
																}*/
																	if(empty($strEBResponceTemp) == true){
																		$strEBResponceTemp = $strEBValVal;
																	}
																	//$arrEBResponce[] = $strEBValVal; 
																	$strXMLEB .= "<EligibilityOrBenefitInformationId>";
																	$strXMLEB .= $strEBValVal;
																	$strXMLEB .= "</EligibilityOrBenefitInformationId>";
																	
																	$strXMLEB .= "<EligibilityOrBenefitInformationVal>";
																	$strXMLEB .= ((is_string($orival) == true) ? $orival : "");
																	$strXMLEB .= "</EligibilityOrBenefitInformationVal>";
																$strXMLEB .= "</EligibilityOrBenefitInformation>";
																
																break;
															case 1:
																$strXMLEB .= "<CoverageLevelCode>";								
																$orival = $this->getEDITranReport("vision_share_271", "CoverageLevelCode", $strEBValVal);
																	$strXMLEB .= "<CoverageLevelCodeId>";
																	$strXMLEB .= $strEBValVal;
																	$strXMLEB .= "</CoverageLevelCodeId>";
																	
																	$strXMLEB .= "<CoverageLevelCodeVal>";
																	$strXMLEB .= ((is_string($orival) == true) ? $orival : "");
																	$strXMLEB .= "</CoverageLevelCodeVal>";								
																$strXMLEB .= "</CoverageLevelCode>";
																break;
															case 2:
																$strXMLEB .= "<ServiceTypeCode>";
																$orival = $this->getEDITranReport("vision_share_271", "Service_Type_Code", $strEBValVal);
																if(((int)$strEBValVal == 30) && (empty($strEBResponceTemp) == false) && (empty($strEBResponce) == true) && ($intServiceTypeCode == 0)){
																	$intServiceTypeCode = 30;
																	$strEBResponce = $strEBResponceTemp;
																	//$strEBInsuranceTypeCode = $strEBValVal;
																}
																	$strXMLEB .= "<ServiceTypeCodeId>";
																	$strXMLEB .= $strEBValVal;
																	$strXMLEB .= "</ServiceTypeCodeId>";
																	
																	$strXMLEB .= "<ServiceTypeCodeVal>";
																	$strXMLEB .= ((is_string($orival) == true) ? $orival : "");
																	$strXMLEB .= "</ServiceTypeCodeVal>";
																$strXMLEB .= "</ServiceTypeCode>";
																break;
															case 3:
																$strXMLEB .= "<InsuranceTypeCode>";
																$orival = $this->getEDITranReport("vision_share_271", "Insurance_Type_Code", $strEBValVal);
																if(((string)$strEBValVal == "MB") && (empty($strEBResponceTemp) == false) && (empty($strEBResponce) == true)){
																	$strEBResponce = $strEBResponceTemp;
																	$strEBInsuranceTypeCode = $strEBValVal;
																}
																elseif(($intServiceTypeCode == 30) && (empty($strEBResponceTemp) == false) && (empty($strEBResponce) == true) && (empty($strEBInsuranceTypeCode) == true)){
																	$strEBResponce = $strEBResponceTemp;
																	$strEBInsuranceTypeCode = $strEBValVal;
																}
																	//$arrEBInsuranceTypeCode[] = $strEBValVal;										
																	$strXMLEB .= "<InsuranceTypeCodeId>";
																	$strXMLEB .= trim($strEBValVal);
																	$strXMLEB .= "</InsuranceTypeCodeId>";
																	
																	$strXMLEB .= "<InsuranceTypeCodeVal>";
																	$strXMLEB .= ((is_string($orival) == true) ? $orival : "");
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
																$orival = $this->getEDITranReport("vision_share_271", "Time_Period_Qualifier", $strEBValVal);										
																	$strXMLEB .= "<TimePeriodQualifierId>";
																	$strXMLEB .= $strEBValVal;
																	$strXMLEB .= "</TimePeriodQualifierId>";
																	
																	$strXMLEB .= "<TimePeriodQualifierVal>";
																	$strXMLEB .= ((is_string($orival) == true) ? $orival : "");
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
																$orival = $this->getEDITranReport("vision_share_271", "Quantity_Qualifier", $strEBValVal);
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
																$orival = $this->getEDITranReport("vision_share_271", "Condition_Response_Code", $strEBValVal);
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
																$orival = $this->getEDITranReport("vision_share_271", "plan_condition_response_code", $strEBValVal);
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
																$orival = $this->getEDITranReport("vision_share_271", "composite_medical_procedure_identifier", trim($arrValtemp[0]));
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
													$strXMLEB .= "</EBLOOP_".$key."_".$intKeyValVal.">";							
													//$strXMLEB .= "</EBLOOP_".$key.">";
													//$strXMLEB .= "</EBLOOPINFO>";
												}
												$strXMLEB .= "</EBLOOPINFO>";
											//}
										}
									}
								}
							}
							$strXMLEB .= "</DTPEBLOOP>";
							//echo $strXMLEB;
							//die();
							//erererere
							//$strEBResponce = implode(",", $arrEBResponce);
							if(empty($strEBResponce) == true){
								$strEBResponce = $strEBResponceTemp;
							}
							$patientDirVsXMLDir = "vs_271_response_xml/";
							if(!is_dir($upLoadPath.$patientDir.$patientDirVsXMLDir)){
								//Create patient VS 271 responce directory
								mkdir($upLoadPath.$patientDir.$patientDirVsXMLDir, 0700, true);
							}
							$fileNameXML = "RTE_".date("Y-m-d-H-i-s_").$patId."_5010_XML";
							$pathToWrite271xml = $upLoadPath.$patientDir.$patientDirVsXMLDir.$fileNameXML.".xml";		
							file_put_contents($pathToWrite271xml,$strXMLEB);					
							$sqlEDIPathRespXML = "";
							$sqlEDIPathRespXML = $patientDir.$patientDirVsXMLDir.$fileNameXML.".xml";		
							//die($pathToWrite271xml."done");
							$qryUpdate = "update real_time_medicare_eligibility set response_271_file_path = '".$sqlEDIPathResp."', 
											xml_271_responce = '".$sqlEDIPathRespXML."', EB_responce = '".$strEBResponce."', responce_date_time = NOW(), 
											transection_error = '".$transectionError."', responce_pat_policy_no = '".$strHL3RQPolicyNo."', responce_pat_add1 = '".addslashes($strHL3RQAdd1)."', 
											responce_pat_add2 = '".addslashes($strHL3RQAdd2)."', responce_pat_city = '".addslashes($strHL3RQCity)."', responce_pat_state = '".addslashes($strHL3RQState)."', 
											responce_pat_zip  = '".$strHL3RQZip."', responce_pat_dob = '".$strHL3RQPatDOB."', responce_pat_fname = '".addslashes($strHL3RQFName)."', 
											responce_pat_lname = '".addslashes($strHL3RQLName)."', responce_pat_mname  = '".addslashes($strHL3RQMName)."', 
											response_deductible = '".$strValueD."', response_copay = '".$strValueCP."', response_co_insurance = '".$strValueCoIns."',
											
											responce_dep_policy_no = '".$strHL4RQPolicyNo."', responce_dep_add1 = '".addslashes($strHL4RQAdd1)."', 
											responce_dep_add2 = '".addslashes($strHL4RQAdd2)."', responce_dep_city = '".addslashes($strHL4RQCity)."', responce_dep_state = '".addslashes($strHL4RQState)."', 
											responce_dep_zip  = '".$strHL4RQZip."', responce_dep_dob = '".$strHL4RQPatDOB."', responce_dep_fname = '".addslashes($strHL4RQFName)."', 
											responce_dep_lname = '".addslashes($strHL4RQLName)."', responce_dep_mname  = '".$strHL4RQMName."', responce_dep_relation  = '".$strDepRel."',
											html_response = '".addslashes(htmlentities($RTEHTML_data))."' 
											
											where id = '".$intNewId."'";
											
							$rsQryUpdate =  imw_query($qryUpdate);
							//if(imw_error()!=''){file_put_contents(dirname(__FILE__).'/rte_response_error_'.date('Ymd_His').'.txt',$qryUpdate."\n\n".imw_error());}
							if($rsQryUpdate && (int)$intPatAppIdSA > 0){
								/***CHECK IF VALID RESPONSE RECEIVED****/
								if($strEBResponce != '' && stripos($strEBResponce,'error')===false && stripos($transectionError,'error')===false){
									$qryUpdateApp = "update schedule_appointments set rte_id = '".$intNewId."' where id = '".$intPatAppIdSA."'";
									$rsUpdateApp = imw_query($qryUpdateApp);
								}
							}
						}
						else{
							$retErrorAns[] = " - File Writing at Server Cause Problem during Response!";
						}
						//eeeeee
						//ww
					}
				}
				else{
					$retErrorAns[] = " - File Writing at Server Cause Problem during Request!";
				}
			}
			else{
				$retErrorAns[] = " - Server Error: No WSDL Found!";
			}
		}
		else{
			$retErrorAns[] = " - EDI Not Found!";
		}
		$blTranError = false;
		if(empty($transectionError) == false){
			$blTranError = true;
		}
		$strShowMsg = "no";
		if($blCopayExits == true){
			$strShowMsg = "yes";
		}
		
		$arrRetAns = array($strEBResponce, $strEBInsuranceTypeCode, $retErrorAns, $blTranError, $intNewId, $strShowMsg);
		//pre($arrRetAns,1);
		return $arrRetAns;
	}
	
	function FormatCaretString($str){
		$arr = explode('^',$str);
		if(count($arr)>1){
			return implode(', ',$arr);
		}
		return $str;
	}

	function getServiceTypeCodeValueById($str_serviceTypeCodeId){
		$str_ServiceValues = ''; $arr_STCI = array();
		$arr_STCI['1']	= 'Medical Care';
		$arr_STCI['2']	= 'Surgical';
		$arr_STCI['3']	= 'Consultation';
		$arr_STCI['4']	= 'Diagnostic X-Ray';
		$arr_STCI['5']	= 'Diagnostic Lab';
		$arr_STCI['6']	= 'Radiation Therapy';
		$arr_STCI['7']	= 'Anesthesia';
		$arr_STCI['8']	= 'Surgical Assistance';
		// index 9 not available.
		$arr_STCI['10']	= 'Blood';
		$arr_STCI['11']	= 'Durable Medical Equipment Used';
		$arr_STCI['12']	= 'Durable Medical Equipment Purchased';
		// index 13 not available.
		$arr_STCI['14']	= 'Renal Supplies';
		// index 15, 16 not available.
		$arr_STCI['17']	= 'Pre-Admission Testing';
		$arr_STCI['18']	= 'Durable Medical Equipment Rental';
		$arr_STCI['19']	= 'Pneumonia Vaccine';
		$arr_STCI['20']	= 'Second Surgical Opinion';
		$arr_STCI['21']	= 'Third Surgical Opinion';
		$arr_STCI['22']	= 'Social Work';
		$arr_STCI['23']	= 'Diagnostic Dental';
		$arr_STCI['24']	= 'Periodontics';
		$arr_STCI['25']	= 'Restorative';
		$arr_STCI['26']	= 'Endodontics';
		$arr_STCI['27']	= 'Maxillofacial Prosthetics';
		$arr_STCI['28']	= 'Adjunctive Dental Services';
		// index 29 not available.
		$arr_STCI['30']	= 'Health Benefit Plan Coverage';
		// index 31 not available.
		$arr_STCI['32']	= 'Plan Waiting Period';
		$arr_STCI['33']	= 'Chiropractic';
		$arr_STCI['34']	= 'Chiropractic Modality';
		$arr_STCI['35']	= 'Dental Care';
		$arr_STCI['36']	= 'Dental Crowns';
		$arr_STCI['37']	= 'Dental Accident';
		$arr_STCI['38']	= 'Orthodontics';
		$arr_STCI['39']	= 'Prosthodontics';
		$arr_STCI['40']	= 'Oral Surgery';
		$arr_STCI['41']	= 'Preventive Dental';
		$arr_STCI['42']	= 'Home Health Care';
		$arr_STCI['43']	= 'Home Health Prescriptions';
		// index 44 not available.
		$arr_STCI['45']	= 'Hospice';
		$arr_STCI['46']	= 'Respite Care';
		$arr_STCI['47']	= 'Hospitalization';
		// index 48 not available.
		$arr_STCI['49']	= 'Hospital - Room and Board';
		// index 50,51,52,53 not available.
		$arr_STCI['54']	= 'Long Term Care';
		$arr_STCI['55']	= 'Major Medical';
		$arr_STCI['56']	= 'Medically Related Transportation';
		// index 57,58,59 not available.
		$arr_STCI['60']	= 'General Benefits';
		$arr_STCI['61']	= 'In-vitro Fertilization';
		$arr_STCI['62']	= 'MRI Scan';
		$arr_STCI['63']	= 'Donor Procedures';
		$arr_STCI['64']	= 'Acupuncture';
		$arr_STCI['65']	= 'Newborn Care';
		$arr_STCI['66']	= 'Pathology';
		$arr_STCI['67']	= 'Smoking Cessation';
		$arr_STCI['68']	= 'Well Baby Care';
		$arr_STCI['69']	= 'Maternity';
		$arr_STCI['70']	= 'Transplants';
		$arr_STCI['71']	= 'Audiology';
		$arr_STCI['72']	= 'Inhalation Therapy';
		$arr_STCI['73']	= 'Diagnostic Medical';
		$arr_STCI['74']	= 'Private Duty Nursing';
		$arr_STCI['75']	= 'Prosthetic Device';
		$arr_STCI['76']	= 'Dialysis';
		$arr_STCI['77']	= 'Otology';
		$arr_STCI['78']	= 'Chemotherapy';
		$arr_STCI['79']	= 'Allergy Testing';
		$arr_STCI['80']	= 'Immunizations';
		$arr_STCI['81']	= 'Routine Physical';
		$arr_STCI['82']	= 'Family Planning';
		$arr_STCI['83']	= 'Infertility';
		$arr_STCI['84']	= 'Abortion';
		$arr_STCI['85']	= 'HIV - AIDS Treatment';
		$arr_STCI['86']	= 'Emergency Services';
		$arr_STCI['87']	= 'Cancer Treatment';
		$arr_STCI['88']	= 'Pharmacy';
		$arr_STCI['89']	= 'Free Standing Prescription Drug';
		$arr_STCI['90']	= 'Mail Order Prescription Drug';
		$arr_STCI['91']	= 'Brand Name Prescription Drug';
		$arr_STCI['92']	= 'Generic Prescription Drug';
		$arr_STCI['93']	= 'Podiatry';
		// Alphabetic code start.
		$arr_STCI['A4']	= 'Psychiatric';
		$arr_STCI['A6']	= 'Psychotherapy';
		$arr_STCI['A7']	= 'Psychiatric - Inpatient';
		$arr_STCI['A8']	= 'Psychiatric - Outpatient';
		$arr_STCI['A9']	= 'Rehabilitation';
		$arr_STCI['AB']	= 'Rehabilitation - Inpatient';
		$arr_STCI['AC']	= 'Rehabilitation - Outpatient';
		$arr_STCI['AD']	= 'Occupational Therapy';
		$arr_STCI['AE']	= 'Physical Medicine';
		$arr_STCI['AF']	= 'Speech Therapy';
		$arr_STCI['AG']	= 'Skilled Nursing Care';
		$arr_STCI['AI']	= 'Substance Abuse';
		$arr_STCI['AJ']	= 'Alcoholism Treatment';
		$arr_STCI['AK']	= 'Drug Addiction';
		$arr_STCI['AL']	= 'Optometry';
		$arr_STCI['AM']	= 'Frames';
		$arr_STCI['AO']	= 'Lenses';
		$arr_STCI['AP']	= 'Routine Eye Exam';
		$arr_STCI['AQ']	= 'Nonmedically Necessary Physical';
		$arr_STCI['AR']	= 'Experimental Drug Therapy';
		$arr_STCI['B1']	= 'Burn Care';
		$arr_STCI['B2']	= 'Brand Name Prescription Drug - Formulary';
		$arr_STCI['B3']	= 'Brand Name Prescription Drug - Non-Formulary';
		$arr_STCI['BA']	= 'Independent Medical Evaluation';
		$arr_STCI['BB']	= 'Psychiatric Treatment Partial Hospitalization';
		$arr_STCI['BC']	= 'Day Care (Psychiatric)';
		$arr_STCI['BD']	= 'Cognitive Therapy';
		$arr_STCI['BE']	= 'Massage Therapy';
		$arr_STCI['BF']	= 'Pulmonary Rehabilitation';
		$arr_STCI['BG']	= 'Cardiac Rehabilitation';
		$arr_STCI['BH']	= 'Pediatric';
		$arr_STCI['BI']	= 'Nursery Room and Board';
		$arr_STCI['BK']	= 'Orthopedic';
		$arr_STCI['BL']	= 'Cardiac';
		$arr_STCI['BM']	= 'Lymphatic';
		$arr_STCI['BN']	= 'Gastrointestinal';
		$arr_STCI['BP']	= 'Endocrine';
		$arr_STCI['BQ']	= 'Neurology';
		$arr_STCI['BT']	= 'Gynecological';
		$arr_STCI['BU']	= 'Obstetrical';
		$arr_STCI['BV']	= 'Obstetrical/Gynecological';
		$arr_STCI['BW']	= 'Mail Order Prescription Drug: Brand Name';
		$arr_STCI['BX']	= 'Mail Order Prescription Drug: Generic';
		$arr_STCI['BY']	= 'Physician Visit - Sick';
		$arr_STCI['BZ']	= 'Physician Visit - Well';
		$arr_STCI['C1']	= 'Coronary Care';
		$arr_STCI['CK']	= 'Screening X-ray';
		$arr_STCI['CL']	= 'Screening laboratory';
		$arr_STCI['CM']	= 'Mammogram, High Risk Patient';
		$arr_STCI['CN']	= 'Mammogram, Low Risk Patient';
		$arr_STCI['CO']	= 'Flu Vaccination';
		$arr_STCI['CP']	= 'Eyewear Accessories';
		$arr_STCI['CQ']	= 'Case Management';
		$arr_STCI['DG']	= 'Dermatology';
		$arr_STCI['DM']	= 'Durable Medical Equipment';
		$arr_STCI['DS']	= 'Diabetic Supplies';
		$arr_STCI['E0']	= 'Allied Behavioral Analysis Therapy';
		$arr_STCI['E1']	= 'Non-Medical Equipment (non DME)';
		$arr_STCI['E2']	= 'Psychiatric Emergency';
		$arr_STCI['E3']	= 'Step Down Unit';
		$arr_STCI['E4']	= 'Skilled Nursing Facility Head Level of Care';
		$arr_STCI['E5']	= 'Skilled Nursing Facility Ventilator Level of Care';
		$arr_STCI['E6']	= 'Level of Care 1';
		$arr_STCI['E7']	= 'Level of Care 2';
		$arr_STCI['E8']	= 'Level of Care 3';
		$arr_STCI['E9']	= 'Level of Care 4';
		$arr_STCI['E10']= 'Radiographs';
		$arr_STCI['E11']= 'Diagnostic Imaging';
		$arr_STCI['E12']= 'Basic Restorative - Dental';
		$arr_STCI['E13']= 'Major Restorative - Dental';
		$arr_STCI['E14']= 'Fixed Prosthodontics';
		$arr_STCI['E15']= 'Removable Prosthodontics';
		$arr_STCI['E16']= 'Intraoral Images - Complete Series';
		$arr_STCI['E17']= 'Oral Evaluation';
		$arr_STCI['E18']= 'Dental Prophylaxis';
		$arr_STCI['E19']= 'Panoramic Images';
		$arr_STCI['E20']= 'Sealants';
		$arr_STCI['E21']= 'Flouride Treatments';
		$arr_STCI['E22']= 'Dental Implants';
		$arr_STCI['E23']= 'Temporomandibular Joint Dysfunction';
		$arr_STCI['E24']= 'Retail Pharmacy Prescription Drug';
		$arr_STCI['E25']= 'Long Term Care Pharmacy';
		$arr_STCI['E26']= 'Comprehensive Medication Therapy Management Review';
		$arr_STCI['E27']= 'Targeted Medication Therapy Management Review';
		$arr_STCI['E28']= 'Dietary/Nutritional Services';
		$arr_STCI['E29']= 'Technical Cardiac Rehabilitation Services Component';
		$arr_STCI['E30']= 'Professional Cardiac Rehabilitation Services Component';
		$arr_STCI['E31']= 'Professional Intensive Cardiac Rehabilitation Services Component';
		$arr_STCI['EA']	= 'Preventive Services';
		$arr_STCI['EB']	= 'Specialty Pharmacy';
		$arr_STCI['EC']	= 'Durable Medical Equipment New';
		$arr_STCI['ED']	= 'CAT Scan';
		$arr_STCI['EE']	= 'Ophthalmology';
		$arr_STCI['EF']	= 'Contact Lenses';
		$arr_STCI['GF']	= 'Generic Prescription Drug - Formulary';
		$arr_STCI['GN']	= 'Generic Prescription Drug - Non-Formulary';
		$arr_STCI['GY']	= 'Allergy';
		$arr_STCI['IC']	= 'Intensive Care';
		$arr_STCI['MH']	= 'Mental Health';
		$arr_STCI['NI']	= 'Neonatal Intensive Care';
		$arr_STCI['ON']	= 'Oncology';
		$arr_STCI['PE']	= 'Positron Emission Tomography (PET) Scan';
		$arr_STCI['PT']	= 'Physical Therapy';
		$arr_STCI['PU']	= 'Pulmonary';
		$arr_STCI['RN']	= 'Renal';
		$arr_STCI['RT']	= 'Residential Psychiatric Treatment';
		$arr_STCI['SMH']= 'Serious Mental Health';
		$arr_STCI['TC']	= 'Transitional Care';
		$arr_STCI['TN']	= 'Transitional Nursery Care';
		$arr_STCI['UC']	= 'Urgent Care';

		$str_serviceTypeCodeId = trim($str_serviceTypeCodeId);
		if(empty($str_serviceTypeCodeId)==false){
			$arr_ServiceCodes = explode('^',$str_serviceTypeCodeId);
			if(count($arr_ServiceCodes)>1){
				$arr_ServiceValues = array();
				foreach($arr_ServiceCodes as $val){
					$value = $arr_STCI[$val];
					if($value && !empty($arr_STCI[$val])){
						$arr_ServiceValues[] = $arr_STCI[$val];
					}
				}
				$str_ServiceValues = implode(', ',$arr_ServiceValues);				
			}
		}
		return $str_ServiceValues;
	}
	
	/*function to get patient today active appointment*/
	function pt_today_appt($pt_id)
	{
		$sch_id=0;
		if($pt_id){
			$q=imw_query("select id from schedule_appointments where sa_patient_id=$pt_id and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_start_date='".date('Y-m-d')."' order by sa_app_starttime asc LIMIT 0,1");
			if(imw_num_rows($q)>0)
			{
				$d=imw_fetch_assoc($q);
				$sch_id=$d['id'];
			}
		}
		return $sch_id;
	}
}
?>