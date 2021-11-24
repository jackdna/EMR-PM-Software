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

set_time_limit(0);
$ignoreAuth = true;
require_once(dirname(__FILE__)."/../../config/globals.php");
$rqBackDay = 0;
$rqBackDay = (int)$_REQUEST['backday'];
$dataDelimeter = "|";
if($rqBackDay > 0){
	//$csvFileName = 'IDX-iMW-Providers-Mapping-30052012_TUFTS2.csv';
	$csvFileName = 'IDX-iMW-Providers-Mapping-30052012_TUFTS2_Updates_10.3.2019.csv';
	$fileContentsFac = fopen($csvFileName,"r");
	if($fileContentsFac){
		while(($data = fgetcsv($fileContentsFac,10000,',')) !== FALSE){
			if($row > 0){
				$arrfac[] = array("SchProvNum" => (int)$data[1], "SchDept" => trim(strtoupper(addslashes($data[2]))), "iMW_FACILITY_MAP" => trim(strtoupper(addslashes($data[4]))));
			}
			$row++;
		}
		fclose($fileContentsFac);
	}

	//pre($arrfac, 1);
	$checkDate = date('Y-m-d');
	$dDate = explode('-',$checkDate);
	if($rqBackDay>0){
		$checkDate = date('Y-m-d', mktime(0,0,0, $dDate[1], $dDate[2] - $rqBackDay, $dDate[0]));
	}
	$dDate = explode('-',$checkDate);
	$dbYr = $dbMon = $dbDay = "";
	$dbYr = $dDate[0];
	$dbMon = $dDate[1];
	$dbDay = $dDate[2];
	
	$dispDate = $dDate[1].'-'.$dDate[2].'-'.$dDate[0];	
	
	$arrFac = array();
	if((boolean) constant("APP_FACILITY_INCLUDE_EXPORT") == true){
		$srySelFac = "select id from facility where cbk_include_in_app_export = '1'";
		$rsSelFac = imw_query($srySelFac);
		if(imw_num_rows($rsSelFac) > 0){
			while($rowSelFac = imw_fetch_array($rsSelFac)){
				$arrFac[] = $rowSelFac["id"];
			}
			imw_free_result($rsSelFac);
		}
	}
	$strFacQry = $strFacQryPre = "";
	if(count($arrFac) > 0){
		$strFac = "";
		$strFac = implode(",",$arrFac);
		$strFacQry = " and sch.sa_facility_id IN (".$strFac.") ";
		$strFacQryPre = " old_facility IN (".$strFac.") and new_facility IN (".$strFac.") AND ";
	}
	$arrPatId = $arrSCHId = array();
	
	$df_sch_status = " (status = 0 AND old_status = 0) ";
	$resch_status = $_REQUEST["rs"];
	if($resch_status == "true")
	{
		$df_sch_status = " ((status = 0 AND old_status = 0) OR status = 202 ) ";		
	}

	$strPatQryPrev = "SELECT sch_id, patient_id from previous_status where ".$df_sch_status." AND ".$strFacQryPre." 
					YEAR(dateTime) = '".$dbYr."' 
					AND MONTH(dateTime) = '".$dbMon."' 
					AND DAYOFMONTH(dateTime) = '".$dbDay."' 
					GROUP BY patient_id,new_provider,new_facility ORDER BY id";
	$rsPatQryPrev = imw_query($strPatQryPrev);
	if(imw_num_rows($rsPatQryPrev)){
		while($rowPatQryPrev = imw_fetch_array($rsPatQryPrev)){
			$arrPatId[] = $rowPatQryPrev["patient_id"];
			$arrSCHId[] = $rowPatQryPrev["sch_id"];
		}
		imw_free_result($rsPatQryPrev);
	}
	
	if(count($arrPatId) > 0){
		$strPatId = "";
		$strPatId = implode(",", $arrPatId);
		
		$strSCHId = "";
		$strSCHId = implode(",", $arrSCHId);
		
		
		$patQry="SELECT 
			pd.External_MRN_1  as mr_no,
			pd.External_MRN_2  as mr_no2,
			pd.email as pt_email, 
			sch.sa_patient_name as patient_name,
			sch.facility_dept as clinic,
			concat(us.lname,',',us.fname,' ',us.mname) as provider_name,
			us.external_id as provider_num,
			CONCAT(refPhy.Title,' ', refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName) as referring_md_name,
			refPhy.external_id as referral_num,
			CONCAT(pcp.Title,' ', pcp.LastName, ', ', pcp.FirstName, ' ', pcp.MiddleName) as pcp_md_name,
			pcp.external_id as pcp_num,
			DATE_FORMAT(sch.sa_app_start_date,'%m%d%y') as dos,
			DATE_FORMAT(sch.sa_app_starttime,'%H%i') as time,
			sch.sa_comments  as Reason,
			(SELECT insurance_companies.name FROM insurance_companies INNER JOIN insurance_data ON insurance_data.provider = insurance_companies.id  
			WHERE insurance_data.pid = sch.sa_patient_id and insurance_data.type = 'primary' and actInsComp=1 LIMIT 0,1)
			as insurance, 
			(SELECT insurance_data.rco_code FROM insurance_data   
			WHERE insurance_data.pid = sch.sa_patient_id and insurance_data.type = 'primary' and actInsComp=1 LIMIT 0,1)
			as insurance_rco_code,
			sp.acronym as Type,
			concat(pd.street,' ', pd.street2) as Address,
			concat(pd.city, ', ',pd.state) as city_state,
			pd.postal_code as zip,
			pd.zip_ext as zipExt,
			pd.phone_home as phone,
			DATE_FORMAT(pd.DOB,'%m%d%y') as DOB,
			REPLACE(pd.language,'Other -- ','')  as language,
			pd.race as race,
			pd.sex as sex,
			pd.status as marital_status,
			DATE_FORMAT(sch.sa_app_time,'%m%d%y') as day_scheduled,
			DATE_FORMAT(sch.sa_app_start_date,'%m/%d/%y') as dosz,
			DATE_FORMAT(sch.sa_app_starttime,'%H%i') as timez,
			pd.id iMW_MRN,
			fac.name as facName,
			DATE_FORMAT(pd.DOB,'%m%d%Y') as DOB2
			
			FROM schedule_appointments sch LEFT JOIN users us ON us.id = sch.sa_doctor_id
			LEFT JOIN facility fac ON fac.id = sch.sa_facility_id
			LEFT JOIN patient_data pd ON pd.id = sch.sa_patient_id 
			LEFT JOIN slot_procedures sp ON sp.id = sch.procedureid
			LEFT JOIN refferphysician refPhy ON refPhy.physician_Reffer_id = pd.primary_care_id
			LEFT JOIN refferphysician pcp ON pcp.physician_Reffer_id = pd.primary_care_phy_id 
			WHERE sch.id IN(".$strSCHId.") 
			AND sch.sa_patient_app_status_id NOT IN(201,18,19,20,203) 
			".$strFacQry."
			";
		$tableRows = $resultHTML='';
		$patRs = imw_query($patQry);
		if(imw_num_rows($patRs) > 0){
			/*****check if base diretory exists***/
			$csv_path = data_path().'users/UserId_'.$_SESSION['authId'].'/wsscript';
			if(!file_exists($csv_path) || !is_dir($csv_path)){mkdir($csv_path,0777,true);}
			
			$fpH1 = fopen($csv_path."/patient_appointments.csv",'w');
			//fputcsv ( $fpH1, array ( "mr_no ", "Patient Name", "Clinic", "Provider Name", "provider_num", "referring_md_name", "referral_num", "DOS", "Time", "Reason", "Insurance", "insurance_rco_code", "Type", "Address", "City, State", "Zip", "Phone", "DOB", "Language", "Race", "Sex", "Marital Status", "Scheduled Date", "dosz", "timez", "iMW_MRN", "pcp_md_name", "pcp_num"), ",", '"' );
			$content = '"mr_no"'.$dataDelimeter.'"Patient Name"'.$dataDelimeter.'"Clinic"'.$dataDelimeter.'"Provider Name"'.$dataDelimeter.'"provider_num"'.$dataDelimeter.'"referring_md_name"'.$dataDelimeter.'"referral_num"'.$dataDelimeter.'"DOS"'.$dataDelimeter.'"Time"'.$dataDelimeter.'"Reason"'.$dataDelimeter.'"Insurance"'.$dataDelimeter.'"insurance_rco_code"'.$dataDelimeter.'"Type"'.$dataDelimeter.'"Address"'.$dataDelimeter.'"City, State"'.$dataDelimeter.'"Zip"'.$dataDelimeter.'"Phone"'.$dataDelimeter.'"DOB"'.$dataDelimeter.'"Language"'.$dataDelimeter.'"Race"'.$dataDelimeter.'"Sex"'.$dataDelimeter.'"Marital Status"'.$dataDelimeter.'"Scheduled Date"'.$dataDelimeter.'"dosz"'.$dataDelimeter.'"timez"'.$dataDelimeter.'"iMW_MRN"'.$dataDelimeter.'"pcp_md_name"'.$dataDelimeter.'"pcp_num"'.$dataDelimeter.'"DOBz"'.$dataDelimeter.'"e-mail"';
			fwrite($fpH1, $content."\n");
			$arrTemp = array();
			while($patRes = imw_fetch_array($patRs)){
				$patMRN = NULL;
				if(empty($patRes['mr_no']) == false){
					$patMRN = (string)$patRes['mr_no'];
					$patMRN = '"'.trim($patMRN).'"';
					
				}else if(empty($patRes['mr_no2']) == false){
					$patMRN = (string)$patRes['mr_no2'];
					$patMRN = '"'.trim($patMRN).'"';
				}
				else{
					$patMRN = '"'." ".'"';
				}
				//echo $patMRN;
				//die;
				$patName = NULL;
				if(empty($patRes['patient_name']) == true){
					$patName = '"'." ".'"';
				}
				else{
					$patName = (string)$patRes['patient_name'];
					$patName = '"'.trim($patName).'"';
				}
				
				$patEmail = NULL;//pt_email
				if(empty($patRes['pt_email']) == true){
					$patEmail = '"'." ".'"';
				}
				else{
					$patEmail = (string)$patRes['pt_email'];
					$patEmail = '"'.trim($patEmail).'"';
				}
				
				$patProName = NULL;
				if(empty($patRes['provider_name']) == true){
					$patProName = '"'." ".'"';
				}
				else{
					$patProName = (string)$patRes['provider_name'];
					$patProName = '"'.trim($patProName).'"';
				}
				$facNameDB = $strFacCode = NULL;
				if(empty($patRes['clinic']) == true){
					$proExID = 0;
					$proExID = $patRes['provider_num'];
					$facNameDB = trim(strtolower(stripslashes($patRes['facName'])));
					if(stristr($facNameDB,'-')){
						list($boston,$fac)=explode('-',$facNameDB);
						if($boston=='boston'){
							$facNameDB=$fac;
						}
					}
					$strProDeptFac = "";
					foreach($arrfac as $facKey => $arrFacVal){
						if( ((int)$arrFacVal["SchProvNum"] == (int)$proExID) && (trim(strtolower(stripslashes($arrFacVal["iMW_FACILITY_MAP"]))) == $facNameDB) ){
							$strProDeptFac = $arrFacVal["SchDept"];
							break;
						}
					}				
					switch($strProDeptFac){
						case "NEMC CORNEA":
							$strFacCode = "COR";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						case "NEMC GLAUCOMA":
							$strFacCode = "GLA";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						case "NEMC NEURO OPHTHALMOLOGY":
							$strFacCode = "NOF";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						case "NEMC OCULOPLASTICS/AESTHETICS":
							$strFacCode = "OCP";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						case "NEMC OPTOMETRICS":
							$strFacCode = "OPT";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						case "NEMC PEDIATRIC OPHTHALMOLOGY":
							$strFacCode = "POF";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						case "NEMC REFRACTIVE SURGERY":
							$strFacCode = "OPRK";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						case "NEMC RETINA":
							$strFacCode = "RET";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						case "NEMC VISUAL PHYSIOLOGY":
							$strFacCode = "VIS";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						case "DAY SURGERY ROOM 22":
							$strFacCode = "DAY";
							$strFacCode = '"'.trim($strFacCode).'"';
						break;
						default:
							$strFacCode = '"'." ".'"';
							if(strtoupper($facNameDB)=='COS'){
								$strFacCode = '"COS"';
							}
						break;
					}
				}
				else{
					$strFacCode = (string)$patRes['clinic'];
					$strFacCode = '"'.trim($strFacCode).'"';
				}
				
				$patProNo = NULL;
				if(empty($patRes['provider_num']) == true){
					$patProNo = '"'." ".'"';
				}
				else{
					$patProNo = (string)$patRes['provider_num'];
					$patProNo = '"'.str_pad(trim($patProNo),6,"0",STR_PAD_LEFT).'"';//'"'.trim($patProNo).'"';
				}
				
				$patRfPhyName = NULL;
				if(empty($patRes['referring_md_name']) == true){
					$patRfPhyName = '"'." ".'"';
				}
				else{
					$patRfPhyName = (string)$patRes['referring_md_name'];
					$patRfPhyName = '"'.trim($patRfPhyName).'"';
				}
				
				$patRfPhyNo = NULL;
				if(empty($patRes['referral_num']) == true){
					$patRfPhyNo = '"'." ".'"';
				}
				else{
					$patRfPhyNo = (string)$patRes['referral_num'];
					$patRfPhyNo = '"'.str_pad(trim($patRfPhyNo),6,"0",STR_PAD_LEFT).'"';//'"'.trim($patRfPhyNo).'"';
				}
				
				$patDOS = NULL;
				if(empty($patRes['dos']) == true){
					$patDOS = '"'." ".'"';
				}
				else{
					$patDOS = (string)$patRes['dos'];
					$patDOS = '"'.trim($patDOS).'"';
				}
				
				$patDOSTime = NULL;
				if(empty($patRes['time']) == true){
					$patDOSTime = '"'." ".'"';
				}
				else{
					$patDOSTime = (string)$patRes['time'];
					$patDOSTime = '"'.trim($patDOSTime).'"';
				}
				
				$strComment = NULL;
				if(empty($patRes['Reason']) == true){
					$strComment = "Visit";
					$strComment = '"'.trim($strComment).'"';
				}
				else{
					$strComment = (string)$patRes['Reason'];
//					$strComment = '"'.trim($strComment).'"';
					$strComment  = '"'.trim(preg_replace( "/<br>|\n|\r/", " ", $strComment)).'"';
				}
				
				$patInsurance = NULL;
				if(empty($patRes['insurance']) == true){
					$patInsurance = '"'." ".'"';
				}
				else{
					$patInsurance = (string)$patRes['insurance'];
					$patInsurance = '"'.trim($patInsurance).'"';
				}
				
				$patInsuranceRCO = NULL;
				if(empty($patRes['insurance_rco_code']) == true){
					$patInsuranceRCO = '"'." ".'"';
				}
				else{
					$patInsuranceRCO = (string)$patRes['insurance_rco_code'];
					$patInsuranceRCO = '"'.trim($patInsuranceRCO).'"';
				}
				
				$patType = NULL;
				if(empty($patRes['Type']) == true){
					$patType = '"'." ".'"';
				}
				else{
					$patType = (string)$patRes['Type'];
					$patType = '"'.trim($patType).'"';
				}
				
				$patAddress = NULL;
				if(empty($patRes['Address']) == true){
					$patAddress = '"'." ".'"';
				}
				else{
					$patAddress = (string)$patRes['Address'];
					$patAddress = '"'.trim($patAddress).'"';
				}
				
				$patCityState = NULL;
				if(empty($patRes['city_state']) == true){
					$patCityState = '"'." ".'"';
				}
				else{
					$patCityState = (string)$patRes['city_state'];
					$patCityState = '"'.trim($patCityState).'"';
				}
				
				$patZip = NULL;
				if(empty($patRes['zipExt']) == false){
					$patZip = $patRes['zip']."-".$patRes['zipExt'];
					$patZip = '"'.trim($patZip).'"';
				}
				else{
					$patZip = (string)$patRes['zip'];
					$patZip = '"'.trim($patZip).'"';
				}
				if(empty($patZip) == true){
					$patZip = '"'." ".'"';
				}
				
				$patPhome = NULL;
				if(empty($patRes['phone']) == true){
					$patPhome = '"'." ".'"';
				}
				else{
					$patPhome = (string)$patRes['phone'];
					$patPhome = '"'.trim($patPhome).'"';
				}
				
				$patDOB = NULL;
				if(empty($patRes['DOB']) == true){
					$patDOB = '"'." ".'"';
					$patDOB2 = '"'." ".'"';
				}
				else{
					$patDOB = (string)$patRes['DOB'];
					$patDOB2 = (string)$patRes['DOB2'];
					$patDOB = '"'.trim($patDOB).'"';
					$patDOB2 = '"'.trim($patDOB2).'"';
				}
				
				$patLanguage = NULL;
				if(empty($patRes['language']) == true){
					$patLanguage = '"'." ".'"';
				}
				else{
					$patLanguage = (string)$patRes['language'];
					$patLanguage = '"'.trim($patLanguage).'"';
				}
				
				$patRace = NULL;
				if(empty($patRes['race']) == true){
					$patRace = '"'." ".'"';
				}
				else{
					$patRace = (string)$patRes['race'];
					$patRace = '"'.trim($patRace).'"';
				}
				
				$patGender = NULL;
				if(empty($patRes['sex']) == true){
					$patGender = '"'." ".'"';
				}
				else{
					$patGender = (string)$patRes['sex'];
					$patGender = '"'.trim($patGender).'"';
				}
				
				$patMaritalStatus = NULL;
				if(empty($patRes['marital_status']) == true){
					$patMaritalStatus = '"'." ".'"';
				}
				else{
					$patMaritalStatus = (string)$patRes['marital_status'];
					$patMaritalStatus = '"'.trim($patMaritalStatus).'"';
				}
	
				$patAppDaySch = NULL;
				if(empty($patRes['day_scheduled']) == true){
					$patAppDaySch = '"'." ".'"';
				}
				else{
					$patAppDaySch = (string)$patRes['day_scheduled'];
					$patAppDaySch = '"'.trim($patAppDaySch).'"';
				}
				
				$patDOSZ = NULL;
				if(empty($patRes['dosz']) == true){
					$patDOSZ = '"'." ".'"';
				}
				else{
					$patDOSZ = (string)$patRes['dosz'];
					$patDOSZ = '"'.trim($patDOSZ).'"';
				}
				
				$patTimez = NULL;
				if(empty($patRes['timez']) == true){
					$patTimez = '"'." ".'"';
				}
				else{
					$patTimez = (string)$patRes['timez'];
					$patTimez = '"'.trim($patTimez).'"';
				}
				
				$patiMW_MRN = NULL;
				if(empty($patRes['iMW_MRN']) == true){
					$patiMW_MRN = '"'." ".'"';
				}
				else{
					$patiMW_MRN = (string)$patRes['iMW_MRN'];
					$patiMW_MRN = '"'.trim($patiMW_MRN).'"';
				}
				
				$patPCPName = NULL;
				if(empty($patRes['pcp_md_name']) == true){
					$patPCPName = '"'." ".'"';
				}
				else{
					$patPCPName = (string)$patRes['pcp_md_name'];
					$patPCPName = '"'.trim($patPCPName).'"';
				}
				
				$patPCPNo = NULL;
				if(empty($patRes['pcp_num']) == true){
					$patPCPNo = '"'." ".'"';
				}
				else{
					$patPCPNo = (string)$patRes['pcp_num'];
					$patPCPNo = '"'.str_pad(trim($patPCPNo),6,"0",STR_PAD_LEFT).'"';//'"'.trim($patPCPNo).'"';
				}
				
				$arrTemp[] = array($patMRN, $patDOS, $patDOSTime);
				//pre($arrTemp,1);
				$intCont = 0;
				foreach($arrTemp as $intTempKey => $arrTempVal){
					if(($arrTempVal[0] == $patMRN) && ($arrTempVal[1] == $patDOS) && ($arrTempVal[2] == $patDOSTime)){
						$intCont++;
					}
				}
				if($intCont == 1){
					//fputcsv ( $fpH1, array ( $patMRN, $patName, $strFacCode, $patProName, $patProNo, $patRfPhyName, $patRfPhyNo, $patDOS, $patDOSTime, trim($strComment), $patInsurance, $patInsuranceRCO, $patType, $patAddress, $patCityState, $patZip, $patPhome, $patDOB, $patLanguage, $patRace, $patGender, $patMaritalStatus, $patAppDaySch, $patDOSZ, $patTimez, $patiMW_MRN, $patPCPName, $patPCPNo), ",", '"' );
					$content = $patMRN.$dataDelimeter.$patName.$dataDelimeter.$strFacCode.$dataDelimeter.$patProName.$dataDelimeter.$patProNo.$dataDelimeter.$patRfPhyName.$dataDelimeter.$patRfPhyNo.$dataDelimeter.$patDOS.$dataDelimeter.$patDOSTime.$dataDelimeter.trim($strComment).$dataDelimeter.$patInsurance.$dataDelimeter.$patInsuranceRCO.$dataDelimeter.$patType.$dataDelimeter.$patAddress.$dataDelimeter.$patCityState.$dataDelimeter.$patZip.$dataDelimeter.$patPhome.$dataDelimeter.$patDOB.$dataDelimeter.$patLanguage.$dataDelimeter.$patRace.$dataDelimeter.$patGender.$dataDelimeter.$patMaritalStatus.$dataDelimeter.$patAppDaySch.$dataDelimeter.$patDOSZ.$dataDelimeter.$patTimez.$dataDelimeter.$patiMW_MRN.$dataDelimeter.$patPCPName.$dataDelimeter.$patPCPNo.$dataDelimeter.$patDOB2.$dataDelimeter.$patEmail;
					fwrite($fpH1, $content."\n");
	
				}
			}
			fclose($fpH1);
			header("location: downloadExcelFile.php");
		}
	}
	else{
		echo "No appointment where made on ".$dispDate."";	
	}
}
else{
	echo "Please Enter Integer Value for 'backday'!";
}
?>
