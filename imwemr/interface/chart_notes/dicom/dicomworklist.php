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
File: dicomworklist.php
Purpose: This file provides processing functions for WL.
Access Type : Include file
*/
?>
<?php
//dicomworklist.php
class DicomWorkList{
	private $arPtTag=array();
	private $arPhyTag=array();
	private $DICOM_AE_WLM_DB, $DICOM_MODALITY, $DICOM_AE_WLM, $DICOM_DB_PATH;
	function __construct($ae_wlm_db="", $mod="", $ae_wlm="", $db_path="") {
	
		$this->DICOM_AE_WLM_DB = $ae_wlm_db;
		$this->DICOM_MODALITY = $mod; 
		$this->DICOM_AE_WLM = $ae_wlm; 
		$this->DICOM_DB_PATH = $db_path;
		
		/*
		//
		//array("(0008,0020)", "DA", "(no value available)"),                     //  0, 0 StudyDate
		//array("(0008,0030)", "TM", "(no value available)"),                     //  0, 0 StudyTime
		//
		//array("(0008,0080)", "LO", "(no value available)"),                     //  0, 0 InstitutionName
		
		//array("(0008,1110)", "SQ", "(Sequence with explicit length #=1)      // 24, 1 ReferencedStudySequence
		//	
		//	     array("(0008,1150)", "UI", "(no value available)"),                     //  0, 0 ReferencedSOPClassUID
		//	     array("(0008,1155)", "UI", "(no value available)"),                     //  0, 0 ReferencedSOPInstanceUID
		
		
		//array("(0010,1000)", "LO", "(no value available)"),                     //  0, 0 OtherPatientIDs
		//array("(0010,1001)", "PN", "(no value available)"),                     //  0, 0 OtherPatientNames
		
		//
		//
		
		//array("(0010,21b0)", "LT", "(no value available)"),                     //  0, 0 AdditionalPatientHistory
		
		//
		
		//array("(0032,1033)", "LO", "(no value available)"),                     //  0, 0 RequestingService
		//
		//array("(0032,1064)", "SQ", "(Sequence with explicit length #=1)      // 32, 1 RequestedProcedureCodeSequence
		//	array("(fffe,e000)", "na", "(Item with explicit length #=3)          // 24, 1 Item
		//	     array("(0008,0100)", "SH", "(no value available)"),                     //  0, 0 CodeValue
		//	     array("(0008,0102)", "SH", "(no value available)"),                     //  0, 0 CodingSchemeDesignator
		//	     array("(0008,0104)", "LO", "(no value available)"),                     //  0, 0 CodeMeaning
		//   array("(fffe,e00d)", "na", "(ItemDelimitationItem for re-encoding)   //  0, 0 ItemDelimitationItem
		//array("(fffe,e0dd)", "na", "(SequenceDelimitationItem for re-encod.) //  0, 0 SequenceDelimitationItem
		//array("(0038,0008)", "CS", "(no value available)"),                     //  0, 0 VisitStatusID
		//array("(0038,0050)", "LO", "(no value available)"),                     //  0, 0 SpecialNeeds
		
		//
		//	array("(fffe,e000)", "na", "(Item with explicit length #=16)         //164, 1 Item
		//
		//
		//
		//
		//
		//array("(0040,0004)", "DA", "(no value available)"),                     //  0, 0 ScheduledProcedureStepEndDate
		//array("(0040,0005)", "TM", "(no value available)"),                     //  0, 0 ScheduledProcedureStepEndTime
		//
		//
		//array("(0040,0008)", "SQ", "(Sequence with explicit length #=1)      // 32, 1 ScheduledProtocolCodeSequence
		//      array("(fffe,e000)", "na", "(Item with explicit length #=3)          // 24, 1 Item
		//	 array("(0008,0100)", "SH", "(no value available)"),                     //  0, 0 CodeValue
		//	 array("(0008,0102)", "SH", "(no value available)"),                     //  0, 0 CodingSchemeDesignator
		//	 array("(0008,0104)", "LO", "(no value available)"),                     //  0, 0 CodeMeaning
		//       array("(fffe,e00d)", "na", "(ItemDelimitationItem for re-encoding)   //  0, 0 ItemDelimitationItem
		//     array("(fffe,e0dd)", "na", "(SequenceDelimitationItem for re-encod.) //  0, 0 SequenceDelimitationItem
		
		//array("(0040,0020)", "CS", "(no value available)"),                     //  0, 0 ScheduledProcedureStepStatus
		//
		//	array("(fffe,e00d)", "na", "(ItemDelimitationItem for re-encoding)   //  0, 0 ItemDelimitationItem
		//	array("(fffe,e0dd)", "na", "(SequenceDelimitationItem for re-encod.) //  0, 0 SequenceDelimitationItem
		//
		//array("(0040,1002)", "LO", "(no value available)"),                     //  0, 0 ReasonForTheRequestedProcedure
		//
		//array("(0040,1010)", "PN", "(no value available)"),                     //  0, 0 NamesOfIntendedRecipientsOfResults
		//array("(0040,1400)", "LT", "(no value available)"),                     //  0, 0 RequestedProcedureComments
		//array("(0040,2001)", "LO", "(no value available)"),                     //  0, 0 RETIRED_ReasonForTheImagingServiceRequest
		//array("(0040,2400)", "LT", "(no value available)")                     //  0, 0 ImagingServiceRequestComments
		
		*/
		//
		$v_0040_0001 = "".$this->DICOM_AE_WLM_DB;
		$v_0008_0060 = "".$this->DICOM_MODALITY;
		if(empty($v_0040_0001)) { $v_0040_0001 = $this->DICOM_AE_WLM ;}
		if(empty($v_0008_0060)) { $v_0008_0060 = "OPT" ;}		
		
		//patients
		$this->arPtTag = array(	
						/*
						array("(0008,0050)", "SH", "(no value available)"),                     //  0, 0 AccessionNumber
						array("(0008,0005)", "CS", "(no value available)"),                             //  10, 1 SpecificCharacterSet
						
						array("(0008,0090)", "PN", "ReferringPhysicianName"),                     //  0, 0 ReferringPhysicianName
						
						array("(0010,0010)", "PN", "PatientName"),                     //  0, 0 PatientName
						array("(0010,0020)", "LO", "PatientID"),                     //  0, 0 PatientID
						//array("(0010,0021)", "LO", "IssuerOfPatientID"),                     //  0, 0 IssuerOfPatientID
						array("(0010,0030)", "DA", "PatientBirthDate"),                     //  0, 0 PatientBirthDate
						array("(0010,0040)", "CS", "PatientSex"),                     //  0, 0 PatientSex
						
						array("(0010,1020)", "DS", "PatientSize"),                     //  0, 0 PatientSize
						array("(0010,1030)", "DS", "PatientWeight"),                     //  0, 0 PatientWeight
						array("(0010,2000)", "LO", "(no value available)"),                     //  0, 0 MedicalAlerts
						array("(0010,2110)", "LO", "(no value available)"),                     //  0, 0 Allergies
						array("(0010,2154)", "SH", "PatientTelephoneNumbers"),                     //  0, 0 PatientTelephoneNumbers
						array("(0010,2160)", "SH", "EthnicGroup"),                     //  0, 0 EthnicGroup
						array("(0010,21a0)", "CS", "SmokingStatus"),                     //  0, 0 SmokingStatus
						
						array("(0010,21c0)", "US", "PregnancyStatus"),                     //  0, 0 PregnancyStatus
						array("(0010,4000)", "LT", "PatientComments"),                     //  0, 0 PatientComments
						array("(0020,000d)", "UI", "1.2.276.0.7230010.3.2.101"),                     //  0, 0 StudyInstanceUID
						array("(0032,1032)", "PN", "RequestingPhysician"),                     //  0, 0 RequestingPhysician
						array("(0032,1060)", "LO", "PatientWL"),                     //  0, 0 RequestedProcedureDescription
						array("(0038,0300)", "LO", "CurrentPatientLocation"),                     //  0, 0 CurrentPatientLocation
						array("(0038,0500)", "LO", "PatientState"),                     //  0, 0 PatientState
						array("(0040,0100)", "SQ", ""),      //172, 1 ScheduledProcedureStepSequence
						array("(fffe,e000)", "na", ""),          // 16, 1 Item
						array("(0008,0060)", "CS", $v_0008_0060),                     //  0, 0 Modality
						array("(0032,1070)", "LO", "(no value available)"),                     //  0, 0 RequestedContrastAgent
						array("(0040,0001)", "AE", "".$v_0040_0001),                     //  0, 0 ScheduledStationAETitle
						array("(0040,0002)", "DA", "".date("Ymd")),                     //  0, 0 ScheduledProcedureStepStartDate
						array("(0040,0003)", "TM", "".date("His")),                     //  0, 0 ScheduledProcedureStepStartTime
						array("(0040,0006)", "PN", "(no value available)"),                     //  0, 0 ScheduledPerformingPhysicianName
						array("(0040,0007)", "LO", "PatientWL2"),                     //  0, 0 ScheduledProcedureStepDescription
						array("(0040,0009)", "SH", "PWL2"),                     //  0, 0 ScheduledProcedureStepID
						array("(0040,0010)", "SH", "(no value available)"),                     //  0, 0 ScheduledStationName
						array("(0040,0011)", "SH", "(no value available)"),                     //  0, 0 ScheduledProcedureStepLocation
						array("(0040,0012)", "LO", "(no value available)"),                     //  0, 0 PreMedication
						array("(0040,0400)", "LT", "(no value available)"),                     //  0, 0 CommentsOnTheScheduledProcedureStep
						array("(fffe,e00d)", "na", ""),   //  0, 0 ItemDelimitationItem
						array("(fffe,e0dd)", "na", ""), //  0, 0 SequenceDelimitationItem
						array("(0040,1001)", "SH", "PWL"),                     //  0, 0 RequestedProcedureID
						array("(0040,1003)", "SH", "(no value available)")                     //  0, 0 RequestedProcedurePriority
						*/
						
						//array("(0008,0000)", "UL", "144"),                                      //   4, 1 GenericGroupLength
						array("(0008,0005)", "CS", "(no value available)"),                     //   0, 0 SpecificCharacterSet
						array("(0008,0020)", "DA", "(no value available)"),                     //   0, 0 StudyDate
						array("(0008,0030)", "TM", "(no value available)"),                     //   0, 0 StudyTime
						array("(0008,0050)", "SH", "AccessionNumber"),                     //   0, 0 AccessionNumber
						array("(0008,0090)", "PN", "ReferringPhysicianName"),                     //   0, 0 ReferringPhysicianName
						array("(0008,1110)", "SQ", "(Sequence with undefined length #=1)"),     // u/l, 1 ReferencedStudySequence
						array("  (fffe,e000)", "na", "(Item with undefined length #=3)"),         // u/l, 1 Item
						//array("    (0008,0000)", "UL", "16"),                                       //   4, 1 GenericGroupLength
						array("    (0008,1150)", "UI", "(no value available)"),                     //   0, 0 ReferencedSOPClassUID
						array("    (0008,1155)", "UI", "(no value available)"),                     //   0, 0 ReferencedSOPInstanceUID
						array("  (fffe,e00d)", "na", "(ItemDelimitationItem)"),                   //   0, 0 ItemDelimitationItem
						array("(fffe,e0dd)", "na", "(SequenceDelimitationItem)"),               //   0, 0 SequenceDelimitationItem
						array("(0008,1120)", "SQ", "(Sequence with undefined length #=1)"),     // u/l, 1 ReferencedPatientSequence
						array("  (fffe,e000)", "na", "(Item with undefined length #=3)"),         // u/l, 1 Item
						//array("    (0008,0000)", "UL", "16"),                                       //   4, 1 GenericGroupLength
						array("    (0008,1150)", "UI", "(no value available)"),                     //   0, 0 ReferencedSOPClassUID
						array("    (0008,1155)", "UI", "(no value available)"),                     //   0, 0 ReferencedSOPInstanceUID
						array("  (fffe,e00d)", "na", "(ItemDelimitationItem)"),                   //   0, 0 ItemDelimitationItem
						array("(fffe,e0dd)", "na", "(SequenceDelimitationItem)"),               //   0, 0 SequenceDelimitationItem
						//array("(0010,0000)", "UL", "112"),                                      //   4, 1 GenericGroupLength
						array("(0010,0010)", "PN", "PatientName"),                     //   0, 0 PatientName
						array("(0010,0020)", "LO", "PatientID"),                     //   0, 0 PatientID
						array("(0010,0021)", "LO", "(no value available)"),                     //   0, 0 IssuerOfPatientID
						array("(0010,0030)", "DA", "PatientBirthDate"),                     //   0, 0 PatientBirthDate
						array("(0010,0032)", "TM", "(no value available)"),                     //   0, 0 PatientBirthTime
						array("(0010,0040)", "CS", "PatientSex"),                     //   0, 0 PatientSex
						array("(0010,1000)", "LO", "(no value available)"),                     //   0, 0 RETIRED_OtherPatientIDs
						array("(0010,1001)", "PN", "(no value available)"),                     //   0, 0 OtherPatientNames
						array("(0010,1020)", "DS", "PatientSize"),                     //  0, 0 PatientSize
						array("(0010,1030)", "DS", "PatientWeight"),                     //   0, 0 PatientWeight
						array("(0010,2000)", "LO", "(no value available)"),                     //   0, 0 MedicalAlerts
						array("(0010,2110)", "LO", "(no value available)"),                     //   0, 0 Allergies
						array("(0010,2154)", "SH", "PatientTelephoneNumbers"),                     //  0, 0 PatientTelephoneNumbers
						array("(0010,2160)", "SH", "EthnicGroup"),                     //   0, 0 EthnicGroup
						array("(0010,21a0)", "CS", "SmokingStatus"),                     //  0, 0 SmokingStatus
						array("(0010,21c0)", "US", "PregnancyStatus"),                     //   0, 0 PregnancyStatus
						array("(0010,4000)", "LT", "PatientComments"),                     //   0, 0 PatientComments
						//array("(0020,0000)", "UL", "8"),                                        //   4, 1 GenericGroupLength
						array("(0020,000d)", "UI", "StudyInstanceUID"),                     //   0, 0 StudyInstanceUID
						//array("(0032,0000)", "UL", "100"),                                      //   4, 1 GenericGroupLength
						array("(0032,1032)", "PN", "RequestingPhysician"),                     //   0, 0 RequestingPhysician
						array("(0032,1060)", "LO", "RequestedProcedureDescription"),                     //   0, 0 RequestedProcedureDescription
						array("(0032,1064)", "SQ", "(Sequence with undefined length #=1)"),     // u/l, 1 RequestedProcedureCodeSequence
						array("  (fffe,e000)", "na", "(Item with undefined length #=5)"),         // u/l, 1 Item
						    //array("(0008,0000)", "UL", "32"),                                       //   4, 1 GenericGroupLength
						    array("(0008,0100)", "SH", "(no value available)"),                     //   0, 0 CodeValue
						    array("(0008,0102)", "SH", "(no value available)"),                     //   0, 0 CodingSchemeDesignator
						    array("(0008,0103)", "SH", "(no value available)"),                     //   0, 0 CodingSchemeVersion
						    array("(0008,0104)", "LO", "(no value available)"),                     //   0, 0 CodeMeaning
						array("  (fffe,e00d)", "na", "(ItemDelimitationItem)"),                   //   0, 0 ItemDelimitationItem
						array("(fffe,e0dd)", "na", "(SequenceDelimitationItem)"),               //   0, 0 SequenceDelimitationItem
						array("(0032,4000)", "LT", "(no value available)"),                     //   0, 0 RETIRED_StudyComments
						//array("(0038,0000)", "UL", "32"),                                       //   4, 1 GenericGroupLength
						array("(0038,0010)", "LO", "(no value available)"),                     //   0, 0 AdmissionID
						array("(0038,0050)", "LO", "(no value available)"),                     //   0, 0 SpecialNeeds
						array("(0038,0300)", "LO", "CurrentPatientLocation"),                     //   0, 0 CurrentPatientLocation
						array("(0038,0500)", "LO", "PatientState"),                     //   0, 0 PatientState
						//array("(0040,0000)", "UL", "280"),                                      //   4, 1 GenericGroupLength
						array("(0040,0100)", "SQ", "(Sequence with undefined length #=1)"),     // u/l, 1 ScheduledProcedureStepSequence
						array("  (fffe,e000)", "na", "(Item with undefined length #=16)"),        // u/l, 1 Item
						    //array("(0008,0000)", "UL", "8"),                                        //   4, 1 GenericGroupLength
						    array("(0008,0060)", "CS", $v_0008_0060),                     //   0, 0 Modality
						    //array("(0032,0000)", "UL", "8"),                                        //   4, 1 GenericGroupLength
						    array("(0032,1070)", "LO", "(no value available)"),                     //   0, 0 RequestedContrastAgent
						    //array("(0040,0000)", "UL", "156"),                                      //   4, 1 GenericGroupLength
						    array("(0040,0001)", "AE", "".$v_0040_0001),                     //   0, 0 ScheduledStationAETitle
						    array("(0040,0002)", "DA", "ScheduledProcedureStepStartDate"),                     //   0, 0 ScheduledProcedureStepStartDate
						    array("(0040,0003)", "TM", "ScheduledProcedureStepStartTime"),                     //   0, 0 ScheduledProcedureStepStartTime
						    array("(0040,0006)", "PN", "(no value available)"),                     //   0, 0 ScheduledPerformingPhysicianName
						    array("(0040,0007)", "LO", "ScheduledProcedureStepDescription"),                     //   0, 0 ScheduledProcedureStepDescription
						    array("(0040,0008)", "SQ", "(Sequence with undefined length #=1)"),     // u/l, 1 ScheduledProtocolCodeSequence
						array("      (fffe,e000)", "na", "(Item with undefined length #=5)"),         // u/l, 1 Item
							//array("(0008,0000)", "UL", "32"),                                       //   4, 1 GenericGroupLength
							array("(0008,0100)", "SH", "(no value available)"),                     //   0, 0 CodeValue
							array("(0008,0102)", "SH", "(no value available)"),                     //   0, 0 CodingSchemeDesignator
							array("(0008,0103)", "SH", "(no value available)"),                     //   0, 0 CodingSchemeVersion
							array("(0008,0104)", "LO", "(no value available)"),                     //   0, 0 CodeMeaning
						array("      (fffe,e00d)", "na", "(ItemDelimitationItem)"),                   //   0, 0 ItemDelimitationItem
						array("    (fffe,e0dd)", "na", "(SequenceDelimitationItem)"),               //   0, 0 SequenceDelimitationItem
						    array("(0040,0009)", "SH", "ScheduledProcedureStepID"),                     //   0, 0 ScheduledProcedureStepID
						    array("(0040,0010)", "SH", "(no value available)"),                     //   0, 0 ScheduledStationName
						    array("(0040,0011)", "SH", "(no value available)"),                     //   0, 0 ScheduledProcedureStepLocation
						    array("(0040,0012)", "LO", "(no value available)"),                     //   0, 0 PreMedication
						    array("(0040,0020)", "CS", "(no value available)"),                     //   0, 0 ScheduledProcedureStepStatus
						    array("(0040,0400)", "LT", "(no value available)"),                     //  0, 0 CommentsOnTheScheduledProcedureStep
						array("  (fffe,e00d)", "na", "(ItemDelimitationItem"),                   //   0, 0 ItemDelimitationItem
						array("(fffe,e0dd)", "na", "(SequenceDelimitationItem)"),               //   0, 0 SequenceDelimitationItem
						array("(0040,1001)", "SH", "RequestedProcedureID"),                     //   0, 0 RequestedProcedureID
						array("(0040,1003)", "SH", "(no value available)"),                     //   0, 0 RequestedProcedurePriority
						array("(0040,1004)", "LO", "(no value available)"),                     //   0, 0 PatientTransportArrangements
						array("(0040,1400)", "LT", "(no value available)"),                     //   0, 0 RequestedProcedureComments
						array("(0040,2016)", "LO", "(no value available)"),                     //   0, 0 PlacerOrderNumberImagingServiceRequest
						array("(0040,3001)", "LO", "(no value available)"),                     //   0, 0 ConfidentialityConstraintOnPatientDataDescription						
						
				);
		
		
		
	}
	
	function createTxtDbFile($arr){
		$str="";
		foreach($arr as $key => $val){
			$tmp=$val[2];
			if(empty($tmp)){ $tmp=""; }
			$str.=$val[0]." ".$val[1]." ".$val[2]."\n";
		}
		return $str;
	}
	
	function guid()
	{
	    if (function_exists('com_create_guid') === true)
	    {
		return trim(com_create_guid(), '{}');
	    }
		
	    return sprintf('%04u%04u.%04u.%04u.%04u.%04u%04u%04u', mt_rand(1, 65535), mt_rand(1, 65535), mt_rand(1, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(1, 65535), mt_rand(1, 65535), mt_rand(1, 65535));
	    
	}

	
	function gen_SOPInsUID(){
		$u = "1.2.840.10008";
		$t = explode(" ",microtime());
		$i = date("nd.Gis", $t[1]).substr((string)$t[0],1,4);
		$d = $this->guid();
		$i = str_replace(".0",".",$i); //remove zero
		
		$r = trim("".$u.".".$i.".".$d);
		$r = str_replace(".0",".",$r); //remove zero
		$r = preg_replace("/\.0+/", ".", $r);
		$r = strlen($r) > 64 ? substr($r, 0, 64) : $r;
		return $r;	
	}
	
	function getReferrName($id){
		$str="";
		if(!empty($id)){
			$sql = " SELECT FirstName, MiddleName, LastName FROM refferphysician WHERE physician_Reffer_id = '".$id."' ";
			$row = sqlQuery($sql);
			if($row != false){
				if(!empty($row["FirstName"])){ $str.=$row["FirstName"]." "; }
				if(!empty($row["MiddleName"])){ $str.=$row["MiddleName"]." "; }
				if(!empty($row["LastName"])){ $str.=$row["LastName"]." "; }
			}
		}
		
		return trim($str);
		
	}
	
	function createWLofPatient($arrPtInfo){
		
		$ReferringPhysicianName = $arrPtInfo["ReferringPhysicianName"];		
		$PatientName = $arrPtInfo["PatientName"];		
		$PatientName = trim($PatientName);		
		$PatientID = $arrPtInfo["PatientID"];		
		$IssuerOfPatientID = $arrPtInfo["IssuerOfPatientID"];		
		$PatientBirthDate = $arrPtInfo["PatientBirthDate"];				
		$PatientSex = $arrPtInfo["PatientSex"];		
		$PatientSize = $arrPtInfo["PatientSize"];
		$PatientWeight = $arrPtInfo["PatientWeight"];
		$PatientTelephoneNumbers = $arrPtInfo["PatientTelephoneNumbers"];
		$EthnicGroup = $arrPtInfo["EthnicGroup"];
		$SmokingStatus = $arrPtInfo["SmokingStatus"];
		$PregnancyStatus = $arrPtInfo["PregnancyStatus"];
		$PatientComments = $arrPtInfo["PatientComments"];
		$RequestingPhysician = $arrPtInfo["RequestingPhysician"];
		$CurrentPatientLocation = $arrPtInfo["CurrentPatientLocation"];
		$PatientState = $arrPtInfo["PatientState"];
		$ScheduledProcedureStepStartDate = $arrPtInfo["ScheduledProcedureStepStartDate"];
		$ScheduledProcedureStepStartTime = $arrPtInfo["ScheduledProcedureStepStartTime"];
		$SchId = $arrPtInfo["SchId"];
		$ScheduledProcedureStepDescription = $arrPtInfo["ScheduledProcedureStepDescription"];
		$ScheduledProcedureStepID = $arrPtInfo["ScheduledProcedureStepID"];
		$RequestedProcedureID = $arrPtInfo["RequestedProcedureID"];
		$RequestedProcedureDescription = $arrPtInfo["RequestedProcedureDescription"];	
		$StudyInstanceUID = $this->gen_SOPInsUID();
		$AccessionNumber = $arrPtInfo["SchId"];

		//Processing
		$arrPTags_tmp=array();
		foreach($this->arPtTag as $key => $arTags ){
			$artmp=array();
			$tag=$arTags[0];
			$artmp[]=$arTags[0];
			$artmp[]=$arTags[1];
			$tmpField = $arTags[2];			
			if($tmpField=="(no value available)" || $tmpField=="(Sequence with undefined length #=1)" || $tmpField=="(Item with undefined length #=3)" || 
						$tmpField=="(Item with undefined length #=5)" ||
							$tmpField=="(Item with undefined length #=16)" || $tmpField=="(ItemDelimitationItem)" || $tmpField=="(SequenceDelimitationItem)"){
				$tmpVal = "";
			}else if( $tag=="(0040,0001)" || 
					$tag=="(0008,0060)" ){ //|| $tag=="(0040,0007)" || $tag=="(0040,0009)" || $tag=="(0040,1001)" || $tag=="(0032,1060) || $tag=="(0020,000d)" ||"
				$tmpVal = $tmpField;
			}else{				
				$tmpVal = (!empty($tmpField)) ? $$tmpField : "";
				$tmpVal = trim($tmpVal);
				if($tag == "(0010,0010)"){ //PN
					$tmpVal=str_replace(" ","^",$tmpVal);
				}
			}			
			
			$artmp[]=$tmpVal;				
			
			$arrPTags_tmp[$key] = $artmp;
		}
		
		//Create Db--
		if(count($arrPTags_tmp) > 0){
			$txt = $this->createTxtDbFile($arrPTags_tmp);			
			
			//DICOM_DB_PATH
			$DICOM_AE="".$this->DICOM_AE_WLM;
			$DICOM_DB_PATH="".$this->DICOM_DB_PATH;
			$fileName="/S".$SchId."P".$PatientID.".dump";
			$fileDb_tmp=$DICOM_DB_PATH."/Dump/".$DICOM_AE."".$fileName;
			$dirname = dirname($fileDb_tmp);
			if (!is_dir($dirname)){    mkdir($dirname, 0777, true); }
			
			$dbfile = fopen($fileDb_tmp, "w") or die("Unable to open file!");
			fwrite($dbfile, $txt);
			fclose($dbfile);
			chmod($fileDb_tmp, 0777);
			
			//if file exists, create wlfile
			if(file_exists($fileDb_tmp)){
				$fileNameWL="/S".$SchId."P".$PatientID.".wl";				
				$wl_cmd = TOOLKIT_DIR."/dump2dcm -g ".$fileDb_tmp." ".$DICOM_DB_PATH."/".$DICOM_AE.$fileNameWL;				
				$last_line = exec($wl_cmd);
				chmod($DICOM_DB_PATH."/".$DICOM_AE.$fileNameWL, 0777);
			}
			
		}
		
	}
	
	//clear worklist
	function clearworklist(){
		$ar = array($this->DICOM_DB_PATH."/".$this->DICOM_AE_WLM, $this->DICOM_DB_PATH."/Dump/".$this->DICOM_AE_WLM);		
		foreach($ar as $k => $v){
			$dir = $v;
			foreach (glob($dir."/*") as $filename) {				
				if (is_file($filename)) {
					//if(strpos($filename,"lockfile")===false){						
					if(!unlink($filename)){ echo "<br/>file not removed.".$filename;  }
					//}
				}
			}
			if(file_exists($dir) && rmdir($dir)){}
			if(!file_exists($dir) && mkdir($dir, 0777, true)){
				chmod($dir, 0777);
				if(strpos($dir,"Dump")===false){
					$h=fopen($dir."/lockfile","w");
					fclose($h);
					chmod($dir."/lockfile", 0777);
				}
			}
		}
	}
	
	//get patient id 
	function getScheduledPt($facId=""){
		
		$cur_time = time();
		$days_ago = date('Y-m-d', strtotime('-3 days', $cur_time));
		$days_after = date('Y-m-d', strtotime('+7 days', $cur_time));
		$sql = "SELECT c2.*, 
					c1.procedureid, c3.proc,
					DATE_FORMAT(c1.sa_app_start_date, '%Y%m%d') AS sa_app_start_date, 
					DATE_FORMAT(c1.sa_app_starttime, '%H%i%s') AS sa_app_starttime,
					c1.id AS sch_id   
				FROM schedule_appointments c1
				LEFT JOIN patient_data c2 ON c1.sa_patient_id = c2.id
				LEFT JOIN slot_procedures c3 ON c1.procedureid = c3.id
				where c1.sa_app_start_date<='".$days_after."%' and c1.sa_app_start_date>='".$days_ago."%' 
				AND c1.sa_patient_app_status_id NOT IN(18)
				";
		if(!empty($facId)){  $sql .= " AND c1.sa_facility_id='".$facId."' "; }
		
		$sql .= " GROUP BY c1.sa_patient_id, c1.sa_app_start_date ORDER BY c1.sa_app_start_date ASC";
		
		/*
		$rez=sqlStatement($sql);
		$ar = array();
		for($i=1; $row=sqlFetchArray($rez); $i++){
			if(!empty($row["sa_patient_id"])){
				$ar[] = $row["sa_patient_id"];
			}
		}
		$str=implode(",",$ar);
		
		if(empty($str)){ $str="1"; } //return default 1	
		*/	
		return $sql;		
	}
	
	function createDB($ptId="", $facId=""){
		
		if(empty($this->DICOM_AE_WLM_DB) && empty($this->DICOM_MODALITY) && empty($this->DICOM_AE_WLM) && empty($this->DICOM_DB_PATH)){
			return false;
		}
	
		$phrase="";
		if(!empty($ptId)){  
			
			if($ptId=="scheduled"){
				$sql = $this->getScheduledPt($facId);
				//if(!empty($ptId)){	$phrase=" WHERE id IN (".$ptId.") ";	}
			}else{	

				$sql = "

				SELECT * FROM patient_data ".$phrase." ORDER BY id 
				
				"; //Limit 0, 10
				$phrase=" WHERE id='".$ptId."' ";
			}
			
		}		
		
		
		
		$rez=sqlStatement($sql);
		
		for($i=1; $row=sqlFetchArray($rez); $i++){			
			
			$ReferringPhysicianName = "";
			if(!empty($row["referrer"])){ $ReferringPhysicianName = $row["referrer"];}
			elseif(!empty($row["referrerID"])){ $ReferringPhysicianName = $this->getReferrName($row["referrerID"]); }
			
			$PatientName = "";
			if(!empty($row["lname"])){ $PatientName .= "".$row["lname"]." "; }
			if(!empty($row["fname"])){ $PatientName .= "".$row["fname"]." "; }
			if(!empty($row["mname"])){ $PatientName .= "".$row["mname"]." "; }			
			$PatientName = trim($PatientName);
			
			$PatientID = $row["id"];
			
			// if some MRN is used to show --
			$tmp_use_mrn = "".DICOM_USE_MRN;
			if(!empty($tmp_use_mrn)){  $PatientID = $row[$tmp_use_mrn];  }
			//--
			
			$IssuerOfPatientID = "";
			
			$PatientBirthDate = "";
			$PatientBirthDate = $row["DOB"];
			$PatientBirthDate = str_replace("-","",$PatientBirthDate);
			
			$PatientSex = "";
			$PatientSex = strtoupper($row["sex"]);
			if($PatientSex=="MALE"){$PatientSex="M";}else if($PatientSex=="FEMALE"){$PatientSex="F";}
			
			$PatientSize = "";			
			
			$PatientWeight = "";
			
			$PatientTelephoneNumbers = "";
			if(empty($PatientTelephoneNumbers) && !empty($row["phone_cell"])){$PatientTelephoneNumbers = $row["phone_cell"];}
			if(empty($PatientTelephoneNumbers) && !empty($row["phone_home"])){$PatientTelephoneNumbers = $row["phone_home"];}
			if(empty($PatientTelephoneNumbers) && !empty($row["phone_biz"])){
				$PatientTelephoneNumbers = $row["phone_biz"];
				if(!empty($row["phone_biz_ext"])){$PatientTelephoneNumbers .= "-".$row["phone_biz_ext"];}
			}
			if(empty($PatientTelephoneNumbers) && !empty($row["phone_contact"])){$PatientTelephoneNumbers = $row["phone_contact"];}			
			
			$EthnicGroup = "".$row["ethnoracial"];			
			
			$SmokingStatus = "";			
			
			$PregnancyStatus = "";
			$PatientComments = "";
			$RequestingPhysician = "";
			$CurrentPatientLocation = "";
			if(!empty($row["street"])){ $CurrentPatientLocation .= $row["street"]." "; }
			if(!empty($row["street2"])){ $CurrentPatientLocation .= $row["street2"]." "; }
			if(!empty($row["postal_code"])){ $CurrentPatientLocation .= $row["postal_code"]." "; }
			if(!empty($row["city"])){ $CurrentPatientLocation .= $row["city"]." "; }
			if(!empty($row["state"])){ $CurrentPatientLocation .= $row["state"]." "; }
			if(!empty($row["country_code"])){ $CurrentPatientLocation .= $row["country_code"]." "; }
			
			$PatientState = "".$row["state"];
			
			$arrPtInfo=array();
			$arrPtInfo["ReferringPhysicianName"] = $ReferringPhysicianName;		
			$arrPtInfo["PatientName"] = trim($PatientName) ;		
			
			$arrPtInfo["PatientID"] = $PatientID;		
			$arrPtInfo["IssuerOfPatientID"] = $IssuerOfPatientID;		
			$arrPtInfo["PatientBirthDate"] = $PatientBirthDate;				
			$arrPtInfo["PatientSex"] = $PatientSex;		
			$arrPtInfo["PatientSize"] = $PatientSize;
			$arrPtInfo["PatientWeight"] = $PatientWeight;
			$arrPtInfo["PatientTelephoneNumbers"] = $PatientTelephoneNumbers;
			$arrPtInfo["EthnicGroup"] = $EthnicGroup;
			$arrPtInfo["SmokingStatus"] = $SmokingStatus;
			$arrPtInfo["PregnancyStatus"] = $PregnancyStatus;
			$arrPtInfo["PatientComments"] = $PatientComments;
			$arrPtInfo["RequestingPhysician"] = $RequestingPhysician;
			$arrPtInfo["CurrentPatientLocation"] = $CurrentPatientLocation;
			$arrPtInfo["PatientState"] = $PatientState;
			
			$arrPtInfo["ScheduledProcedureStepStartDate"] =  $row["sa_app_start_date"];
			$arrPtInfo["ScheduledProcedureStepStartTime"] =  $row["sa_app_starttime"];
			$arrPtInfo["SchId"] = $row["sch_id"];
			
			$proc = !empty($row["proc"]) ? $row["proc"] : "00000000";
			$procid = !empty($row["procedureid"]) ? str_pad($row["procedureid"],8,"0",STR_PAD_LEFT) : "00000000";
			$arrPtInfo["ScheduledProcedureStepDescription"] = $proc;
			$arrPtInfo["ScheduledProcedureStepID"] = $procid;
			$arrPtInfo["RequestedProcedureID"] = $procid;
			$arrPtInfo["RequestedProcedureDescription"] = $proc;
			
			$this->createWLofPatient($arrPtInfo);
			
		}
	}	
	
	static function get_facility_db_nms(){
		$t = DICOM_AE_WLM;
		$ts = substr($t, 0, 3);
		
		$ret = array();
		$sql = "SELECT id, name FROM facility Order By id ";
		$rez=sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez); $i++){
			if(!empty($row["id"])){
				$n = strtoupper($row["name"]);
				$n = str_replace(" ", "", $n);
				$ns = substr($n, 0, 3);	
				$ret[] = $t."~!~".$ts.$ns."_*_".$row["id"];	
			}
		}
		
		$str="";
		$str = implode(",", $ret);
		return $str;
	}
}

?>