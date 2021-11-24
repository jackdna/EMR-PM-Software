<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$ediDate1 = $ediDate2 = $ediTime = $ediTime1 = "";
$ediDate1 = date('ymd');
$ediDate2 = date('Ymd');
$ediTime = date('Hi');
$ediTime1 = date('His');
$transactionSetPurposeCode = '13';
$refTJsegment = $infoReceiverAddress = $subcAddress = $subsDate = true;
$PRV2100B = false;
//INTERCHANGE CONTROL HEADER/ISA
if(in_array($dbInsCompPayId,array('00267'))){
	$infoReceiverAddress = $subcAddress = $subsDate = $refTJsegment = false;
	//$dbInsCompName = 'BCBS OF FL';
}

$edi270TranDataFinal = '';
if(!$NoHeaderFooter){
	$edi270TranDataFinal  .= 'ISA*00*'.$authorInfo.'*00*'.$secretInfo.'*ZZ*'.$strEmdeonTID.$submitterSpace.'*ZZ*'.$interchangeReceiverID.$recieveSpace.'*'.$ediDate1.'*'.$ediTime.'*'.$interchange_control_standard_id.'*00501*'.$InterCtrlNumber.'*0*P*:~';
	//$edi270TranDataFinal .= 'IEA*1*'.$InterCtrlNumber.'~';
	
	//FUNCTIONAL GROUP HEADER/GS
	$edi270TranDataFinal .= 'GS*HS*'.$strEmdeonTID.'*'.$interchangeReceiverID.'*'.$ediDate2.'*'.$ediTime.'*'.$InterCtrlNumber.'*X*005010X279A1~';
	//$edi270TranDataFinal .= 'GE*1*'.$newRTMEMAXID.'~';
	
	//TRANSACTION SET HEADER/ ST
	$edi270TranDataFinal .= 'ST*270*'.$InterCtrlNumber.'*005010X279A1~';
	$this->edi270STCounter++;
	$edi270TranDataFinal .= 'BHT*0022*'.$transactionSetPurposeCode.'*ALL*'.$ediDate2.'*'.$ediTime.'~';
	$this->edi270STCounter++;
}

//LOOP 2100A – INFORMATION SOURCE NAME
$edi270TranDataFinal .= 'HL*'.$this->HLsegmentCounter.'**20*1~';
$this->HLsegmentCounter++;
$this->edi270STCounter++;

$edi270TranDataFinal .= 'NM1*PR*2*'.$dbInsCompName.'*****PI*'.$dbInsCompPayId.'~';
$this->edi270STCounter++;

//LOOP 2100B – INFORMATION RECEIVER NAME
$edi270TranDataFinal .= 'HL*'.$this->HLsegmentCounter.'*1*21*1~';
$this->HLsegmentCounter++;
$this->edi270STCounter++;

$edi270TranDataFinal .= 'NM1*1P*2*'.trim($dbGroupName).'*****XX*'.trim($dbGroupNPI).'~';
$this->edi270STCounter++;
//Provider Specialty Code from the Health Care Provider Taxonomy code list (PRV segment)
if($PRV2100B){
	$edi270TranDataFinal .= 'PRV*BI*PXC*207W00000X~';
	$this->edi270STCounter++;
}

if((empty($dbGroupFederalTaxID) == false) && (strlen($dbGroupFederalTaxID) >= 9) && $refTJsegment){
	$edi270TranDataFinal .=	"REF*TJ*".trim($dbGroupFederalTaxID)."~"; //Loop: 2100B ->INFORMATION RECEIVER ADDITIONAL IDENTIFICATION											
	$this->edi270STCounter++;
}
if($infoReceiverAddress){
	if((empty($dbGroupAdd1) == false) && (empty($dbGroupAdd2) == false)){
		$edi270TranDataFinal .=	"N3*".trim($dbGroupAdd1)."*".trim($dbGroupAdd2)."~";
		$this->edi270STCounter++;
	}elseif(empty($dbGroupAdd1) == false ){
		$edi270TranDataFinal .=	"N3*".trim($dbGroupAdd1)."~";
		$this->edi270STCounter++;
	}
	if((empty($dbGroupCity) == false) && (empty($dbGroupState) == false) && (empty($dbGroupZip) == false)){
		$edi270TranDataFinal .=	"N4*".trim($dbGroupCity)."*".trim($dbGroupState)."*".trim($dbGroupZip)."~";
		$this->edi270STCounter++;
	}

	if(!$NoHeaderFooter && (empty($dbGroupConName) == false) && ((empty($dbGroupTelephone) == false) || (empty($dbGroupFax) == false) || (empty($dbGroupEmail) == false))){
		$recConInfo = "PER*IC*";
		$recConInfo .=	$dbGroupConName; //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION Name									
		if(empty($dbGroupTelephone) == false){
			$recConInfo .=	"*TE*".trim($dbGroupTelephone); //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION Telephone
		}
		if(empty($dbGroupFax) == false){
			$recConInfo .=	"*FX*".trim($dbGroupFax); //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION Facsimile
		}
		if(empty($dbGroupEmail) == false){
			$recConInfo .=	"*EM*".trim($dbGroupEmail); //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION Electronic Mail
		}
		$recConInfo .=	"~";
		$edi270TranDataFinal .=	$recConInfo; //Loop: 2100B -> INFORMATION RECEIVER CONTACT INFORMATION	
		$this->edi270STCounter++;
	}
}
//LOOP 2100C – SUBSCRIBER OR DEPENDENT INFORMATION
$blDepLoopExit = false; //dependent loop
if(strtolower($dbSubscriberRelShip) == "self"){											
	$edi270TranDataFinal .=	'HL*'.$this->HLsegmentCounter.'*2*22*0~'; //Loop: 2000C -> SUBSCRIBER LEVEL -> 0 = Subscriber search
	$this->HLsegmentCounter++;
	$this->edi270STCounter++;
}
else{
	$edi270TranDataFinal .=	'HL*'.$this->HLsegmentCounter.'*2*22*1~'; //Loop: 2000C -> SUBSCRIBER LEVEL -> 1 = Dependent search
	$this->HLsegmentCounter++;
	$this->edi270STCounter++;
	$blDepLoopExit = true;
}

$edi270TranDataFinal .= 'NM1*IL*1*'.trim($dbSubscriberLName).'*'.trim($dbSubscriberFName).'*'.trim($dbSubscriberMName).'**'.trim($dbSubscriberSuffix).'*MI*'.trim($dbInsPolicyClaimNo).'~';
$this->edi270STCounter++;

//$this->edi270STCounter++;										
if($blDepLoopExit == false){
	if($subcAddress){
		if((empty($dbSubscriberSS) == false) && (strlen((string)$dbSubscriberSS) == 9)){
			$edi270TranDataFinal .= "REF*SY*".trim($dbSubscriberSS)."~"; //Loop: 2100C -> SUBSCRIBER ADDITIONAL IDENTIFICATION
			$this->edi270STCounter++;
		}

		if((empty($dbSubscriberAdd1) == false) || (empty($dbSubscriberAdd2) == false)){
			$subAdd = "N3";
			if(empty($dbSubscriberAdd1) == false){
				$subAdd .=	"*".trim($dbSubscriberAdd1); //Loop: 2100C -> SUBSCRIBER ADDRESS1
			}
			if(empty($dbSubscriberAdd2) == false){
				$subAdd .=	"*".trim($dbSubscriberAdd2); //Loop: 2100C -> SUBSCRIBER ADDRESS2
			}
			$subAdd .=	"~";
			$edi270TranDataFinal .=	trim($subAdd); //Loop: 2100C -> SUBSCRIBER ADDRESS
			$this->edi270STCounter++;
		}
		if((empty($dbSubscriberCity) == false) || (empty($dbSubscriberState) == false) || (empty($dbSubscriberZip) == false)){
			$subCSZ = "N4";
			if(empty($dbSubscriberCity) == false){
				$subCSZ .=	"*".trim($dbSubscriberCity); //Loop: 2100C -> SUBSCRIBER CITY
			}
			if(empty($dbSubscriberState) == false){
				$subCSZ .=	"*".trim($dbSubscriberState); //Loop: 2100C -> SUBSCRIBER STATE
			}
			if(empty($dbSubscriberZip) == false){
				$subCSZ .=	"*".trim($dbSubscriberZip); //Loop: 2100C -> SUBSCRIBER ZIP CODE
			}
			$subCSZ .=	"~";
			$edi270TranDataFinal .=	trim($subCSZ); //Loop: 2100C -> SUBSCRIBER CITY/STATE/ZIP CODE
			$this->edi270STCounter++;
		}
	}
	if((empty($dbSubscriberDOB) == false) || (empty($dbSubscriberGender) == false)){
		$subDemoInfo = "DMG";
		if(empty($dbSubscriberDOB) == false){
			$subDemoInfo .=	"*D8*".trim($dbSubscriberDOB); //Loop: 2100C -> SUBSCRIBER DOB
		}
		if(empty($dbSubscriberGender) == false){
			$subDemoInfo .=	"*".trim($dbSubscriberGender[0]); //Loop: 2100C -> SUBSCRIBER GENDER
		}
		$subDemoInfo .=	"~";
		$edi270TranDataFinal .=	trim($subDemoInfo); //Loop: 2100C -> SUBSCRIBER DEMOGRAPHIC INFORMATION			
		$this->edi270STCounter++;
	}
	
	//SUBSCRIBER DATE
	if($subsDate){
		$subDate = "";
		if($elReqDate == ""){
			$subDate = date("Ymd");
		}
		else{							
			$subDate = date("Ymd");
			$imedicDOS = date("Y-m-d");																				
		}
		$ediDOSSegment = "";
		$DOS_segment = true;
		if($DOS_segment != false && !$NoHeaderFooter){
			$ediDTP01 = "307";
			$subDate = date("Ymd")."-".$subDate;
			$ediDTP02 = "D8";
			$ediDOSSegment = "DTP*".trim($ediDTP01)."*".trim($ediDTP02)."*".trim($subDate)."~"; //Loop: 2100C -> SUBSCRIBER DATE
			$edi270TranDataFinal .=	trim($ediDOSSegment); //Loop: 2100C -> SUBSCRIBER DATE
			$this->edi270STCounter++;
		}
	}
	
	$edi270TranDataFinal .= 'EQ*30***~'; //Loop: 2110C -> SUBSCRIBER ELIGIBILITY OR BENEFIT INQUIRY INFORMATION
	$this->edi270STCounter++;
}elseif($blDepLoopExit == true){
	$edi270TranDataFinal .=	'HL*'.$this->HLsegmentCounter.'*3*23*0~'; //2000D — DEPENDENT LEVEL
	$this->HLsegmentCounter++;
	$this->edi270STCounter++;
	$ediDepLName = $ediDepFName = $ediDepMName = $ediDepId = $ediDepAddInfo = "";
	$ansDepLevel = true;
	if($ansDepLevel == true){
		$edi270TranDataFinal .= "NM1*03*1*".trim($dbPDLName)."*".trim($dbPDFName)."*".trim($dbPDMName)."***".trim($dbInsPolicyClaimNo)."~"; //Loop: 2000D —> DEPENDENT LEVEL	
		$this->edi270STCounter++;
		
		if((empty($dbPDSS) == false) && (strlen((string)$dbPDSS) == 9)){
			$edi270TranDataFinal .= "REF*SY*".trim($dbPDSS)."~"; //Loop: 2100D -> DEPENDENT ADDITIONAL IDENTIFICATION
			$this->edi270STCounter++;
		}
		
		if((empty($dbPDStreet1) == false) || (empty($dbPDStreet2) == false)){
			$depAdd = "N3";
			if(empty($dbPDStreet1) == false){
				$depAdd .=	"*".trim($dbPDStreet1); //Loop: 2100D -> DEPENDENT ADDRESS1
			}
			if(empty($dbPDStreet2) == false){
				$depAdd .=	"*".trim($dbPDStreet2); //Loop: 2100D -> DEPENDENT ADDRESS2
			}
			$depAdd .=	"~";
			$edi270TranDataFinal .=	trim($depAdd); //Loop: 2100D -> DEPENDENT ADDRESS
			$this->edi270STCounter++;
		}
		if((empty($dbPDCity) == false) || (empty($dbPDState) == false) || (empty($dbPDZip) == false)){
			$depCSZ = "N4";
			if(empty($dbPDCity) == false){
				$depCSZ .=	"*".trim($dbPDCity); //Loop: 2100D -> DEPENDENT CITY
			}
			if(empty($dbPDState) == false){
				$depCSZ .=	"*".trim($dbPDState); //Loop: 2100D -> DEPENDENT STATE
			}
			if(empty($dbPDZip) == false){
				$depCSZ .=	"*".trim($dbPDZip); //Loop: 2100D -> DEPENDENT ZIP CODE
			}
			$depCSZ .=	"~";
			$edi270TranDataFinal .=	trim($depCSZ); //Loop: 2100D -> DEPENDENT CITY/STATE/ZIP CODE
			$this->edi270STCounter++;
		}
		
		if((empty($dbPDDOB) == false) || (empty($dbPDGender) == false)){
			$depDemoInfo = "DMG";
			if(empty($dbPDDOB) == false){
				$depDemoInfo .=	"*D8*".trim($dbPDDOB); //Loop: 2100D -> DEPENDENT DOB
			}
			if(empty($dbPDGender) == false){
				$depDemoInfo .=	"*".trim($dbPDGender[0]); //Loop: 2100D -> DEPENDENT GENDER
			}
			$depDemoInfo .=	"~";
			$edi270TranDataFinal .=	trim($depDemoInfo); //Loop: 2100D -> DEPENDENT DEMOGRAPHIC INFORMATION			
			$this->edi270STCounter++;
		}
		
		if((strtolower($dbSubscriberRelShip) == "daughter") || (strtolower($dbSubscriberRelShip) == "son")){
			$edi270TranDataFinal .= "INS*N*19~"; //Loop: 2100D -> DEPENDENT RELATIONSHIP
			$this->edi270STCounter++;
		}
		elseif(strtolower($dbSubscriberRelShip) == "spouse"){
			$edi270TranDataFinal .= "INS*N*01~"; //Loop: 2100D -> DEPENDENT RELATIONSHIP
			$this->edi270STCounter++;
		}
		elseif(empty($dbSubscriberRelShip) == false){
			$edi270TranDataFinal .= "INS*N*34~"; //Loop: 2100D -> DEPENDENT RELATIONSHIP
			$this->edi270STCounter++;
		}

		//DEPENDENT DATE
		$depDate = "";
		if($elReqDate == ""){
			$depDate = date("Ymd");
		}
		else{							
			$depDate = date("Ymd");
			$imedicDOS = date("Y-m-d");
		}
		$ediDOSDepSegment = "";
		$ansDepDOS = true;
		if($ansDepDOS !== false && !$NoHeaderFooter){
			$ediDTP01 = "307";
			$ediDTP02 = "D8";
			$ediDOSDepSegment = "DTP*".trim($ediDTP01)."*".trim($ediDTP02)."*".trim($depDate)."~"; //Loop: 2100D -> DEPENDENT DATE
			$edi270TranDataFinal .=	trim($ediDOSDepSegment); //Loop: 2100D -> DEPENDENT DATE
			$this->edi270STCounter++;
		}

		$edi270TranDataFinal .= 'EQ*30***~'; //Loop: 2110D -> SUBSCRIBER ELIGIBILITY OR BENEFIT INQUIRY INFORMATION
		$this->edi270STCounter++;
	}
}

if(!$NoHeaderFooter){
	$this->edi270STCounter++; //incrementing by 1 for last SE segment.
	$edi270TranDataFinal .= 'SE*'.$this->edi270STCounter.'*'.$InterCtrlNumber.'~';
	$edi270TranDataFinal .= 'GE*1*'.$newRTMEMAXID.'~';
	$edi270TranDataFinal .= 'IEA*1*'.$InterCtrlNumber.'~';
}
$edi270TranDataFinal = preg_replace('/[^a-zA-Z0-9_*\- ~.:|@^\']/',' ',$edi270TranDataFinal);

?>