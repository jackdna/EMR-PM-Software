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
FILE : patient_recall_result.php
PURPOSE : PATIENT APPOINTMENT RECALL REPORT
ACCESS TYPE : INCLUDED
*/
$page_data = '';	$printFile= false;
$curDate = date($phpDateFormat);

$filename="press_ganey_report.csv";
$csv_file_name= write_html("", $filename);

$pfx=",";


if($start_date == ""){
	$start_date = $curDate;
	$end_date = $curDate;
}
$curDate.='&nbsp;'.date(" h:i A");
$printFile = false;
	
if($_POST['form_submitted']){
	$contentPart='';


	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport=='Daily'){
		$start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$start_date = $arrDateRange['WEEK_DATE'];
		$end_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$start_date = $arrDateRange['MONTH_DATE'];
		$end_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$start_date = $arrDateRange['QUARTER_DATE_START'];
		$end_date = $arrDateRange['QUARTER_DATE_END'];
	}	
	$st_date = getDateFormatDB($start_date);
	$en_date = getDateFormatDB($end_date);

	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	
	$facility_id='';
	if(sizeof($facility_name)>0){
		$facility_id= implode(',', $facility_name);		
	}
	//IF NO FACILITY SELECTED THEN GET ALL SATELLITE FACILITIES
	if(empty($facility_id)==true){
		$qry = "SELECT f.id as 'fac_id', f.name as 'facility_name' FROM facility f 
				   LEFT JOIN server_location sl ON (f.server_location=sl.id) WHERE f.server_location IN (1,2,3,4,5,6,7)";												
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$fac_id_arr[$res['fac_id']] = $res['fac_id'];
		}
		if(sizeof($fac_id_arr)>0){
			$facility_id= implode(',', $fac_id_arr);		
		}
	}
	
	//--- GET SCHEDULER First FACILITY NAME ----
	$qry = "Select id, name from facility";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$arrAllFacilities[$res['id']] = $res['name'];
	}

	function addDoubleQuaotes($stringVal){
		if($stringVal!=""){
		 $stringVal='"'.$stringVal.'"';
		 }
		 return $stringVal;
	}
	
	//LANGUAGES CODES
	$arr_language_codes=array(
	"Albanian"=>"57", "Arabic"=>"22", "Armenian"=>"31", "Bengali"=>"60", "Bosnian"=>"50", "Bosnian-Croatian"=>"49",
	"Bosnian-Muslim"=>"48", "Bosnian-Serbian"=>"32", "Cambodian"=>"34", "Chao-Chou"=>"41", "Chinese-Simplified"=>"12",
	"Chinese-Traditional"=>"10", "Chuukese"=>"23", "Creole"=>"21", "Croatian"=>"52", "English/Spanish"=>"33", "English"=>"0", "Farsi"=>"59",
	"French-Canadian"=>"35", "French-France"=>"20", "German"=>"4", "Greek"=>"7", "Haitian-Creole"=>"36", "Hebrew"=>"37", "Hindi"=>"38",
	"Hmong"=>"26", "Ilocano"=>"56", "Indonesian"=>"42", "Italian"=>"5", "Japanese"=>"28", "Korean"=>"29", "Laotian"=>"43", "Malayalam"=>"58",
	"Malayan"=>"44", "Marshallese"=>"24", "Polish"=>"6", "Portuguese-Brazilian"=>"8", "Portuguese-Continental"=>"47", "Punjabi"=>"54",
	"Romanian"=>"55", "Russian"=>"3", "Samoan"=>"25", "Serbian"=>"51", "Somali"=>"27", "Spanish"=>"1", "Swahili"=>"45", "Tagalog"=>"30", "Thai"=>"46",
	"Turkish"=>"53", "Urdu"=>"39", "Vietnamese"=>"13", "Yiddish"=>"40");


	//MAIN QUERY
	$sch_query = "Select ch.patient_id, ch.providerId, ch.encounterId, ch.date_of_service,
	sa.id, sa.sa_facility_id, sa.sa_doctor_id, DATE_FORMAT(sa.sa_app_start_date, '".$date_format_SQL."') as 'sa_app_start_date',
	sa.sa_app_starttime, sa.sa_patient_app_status_id, us.id as 'docID', us.user_npi,
	us.fname, us.mname, us.lname, pd.fname as 'pfname', pd.mname as 'pmname', pd.lname as 'plname',
	pd.street, pd.street2, pd.postal_code, pd.city, pd.state, pd.phone_home, pd.phone_biz, pd.phone_contact, pd.phone_cell, pd.email,
	pd.sex, DATE_FORMAT(pd.DOB, '".$date_format_SQL."') as 'dob',
	pd.race, pd.language, superbill.procOrder    
	FROM chart_master_table ch
	LEFT JOIN schedule_appointments sa ON (sa.sa_patient_id=ch.patient_id AND sa.sa_app_start_date=ch.date_of_service)
	LEFT JOIN superbill ON superbill.formId = ch.id 
	JOIN patient_data pd ON pd.id = ch.patient_id 
	JOIN users us on us.id = ch.providerId  
	WHERE (ch.date_of_service BETWEEN '$st_date' and '$en_date') 
	AND ch.delete_status='0'";
	if(empty($facility_id)==false){
		$sch_query.=" AND sa.sa_facility_id IN(".$facility_id.")";
	}
	$sch_query.=" ORDER BY ch.date_of_service";
	//sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3)

	$rs = imw_query($sch_query);

	$totalAppts=0;
	$arrPatientData = array();
	$arrFacility = array();
	$j = 0; $k =  0;
	while($res=imw_fetch_assoc($rs)){	
		$printFile = true;
		$minitial='';
		$facility='';	$docNameArr= array();	$patNameArr= array();
		$facility = $res['sa_facility_id'];
		$apptStsId= $res['sa_patient_app_status_id'];

		$phone_default = $res["phone_home"];
		$prefer_contact = $res["preferr_contact"];
		if($prefer_contact == 0)
		{
			if(trim($res["phone_home"]) != ""){$phone_default = $res["phone_home"]; }
		}
		else if($prefer_contact == 1)
		{
			if(trim($res["phone_biz"]) != ""){$phone_default = $res["phone_biz"]; }				
		}
		else if($prefer_contact == 2)
		{
			if(trim($res["phone_cell"]) != ""){$phone_default = $res["phone_cell"]; }				
		}	
					
		//IF STILL PHONE IS EMPTY
		if(empty($phone_default)==true){
			if(trim($res["phone_cell"]) != ""){$phone_default = $res["phone_cell"]; }
			else if(trim($res["phone_contact"]) != ""){$phone_default = $res["phone_contact"]; }
			else if(trim($res["phone_biz"]) != ""){$phone_default = $res["phone_biz"]; }
		}
		
		//GENDER
		if(strtolower($res["sex"])=='male')$gender='1';
	    else if(strtolower($res["sex"])=='female')$gender='2';
		else $gender='M';
		
		//PHYSICIAN NAME
		$doc_name = core_name_format($res['lname'],$res['fname'],$res['mname']);

		//LANGUAGE CODE
		if($res['language']=='French')$res['language']='French-France';
		else if($res['language']=='Portuguese')$res['language']='Portuguese-Continental';
		$languange_code = $arr_language_codes[$res['language']];
		
		//SUPERBILL CPT CODES
		$cptPart='';
		$arrCPT=explode(',', $res['procOrder']);
		for($j=0; $j<=5; $j++){
			if($arrCPT[$j]){
				$cptPart.= addDoubleQuaotes($arrCPT[$j]).$pfx;
			}else{
				$cptPart.= "".$pfx;
			}
		}

		//EMPTY APPT DETAIL IF STATUS IS BELOW
		if($apptStsId=="203" || $apptStsId=="201" || $apptStsId=="18" || $apptStsId=="19" || $apptStsId=="20" || $apptStsId=="3"){
			$res["sa_app_start_date"]='';
			$res["sa_app_starttime"]='';
			$res["sa_facility_id"]='';
		}
		
		//MIDDLE NAME INITIAL
		$pmname_initial='';
		if($res['mlname']!=''){
			$pmname_initial= strtoupper(substr($res['mlname'], 0, 1));
		}

		$contentPart.=
		'MD0101'.$pfx.
		'29286'.$pfx.
		addDoubleQuaotes($res['plname']).$pfx.
		addDoubleQuaotes($pmname_initial).$pfx.
		addDoubleQuaotes($res['pfname']).$pfx.
		addDoubleQuaotes($res['street']).$pfx.
		addDoubleQuaotes($res['street2']).$pfx.
		addDoubleQuaotes($res['city']).$pfx.
		addDoubleQuaotes($res['state']).$pfx.
		addDoubleQuaotes($res['postal_code']).$pfx.
		addDoubleQuaotes($phone_default).$pfx.
		addDoubleQuaotes($gender).$pfx.
		addDoubleQuaotes($res["race"]).$pfx.
		addDoubleQuaotes($res["dob"]).$pfx.
		addDoubleQuaotes($languange_code).$pfx.
		addDoubleQuaotes($res["patient_id"]).$pfx.
		addDoubleQuaotes($res["encounterId"]).$pfx.
		addDoubleQuaotes($res["sa_facility_id"]).$pfx.
		addDoubleQuaotes($arrAllFacilities[$res["sa_facility_id"]]).$pfx.
		addDoubleQuaotes($res["user_npi"]).$pfx.
		addDoubleQuaotes($doc_name).$pfx.
		addDoubleQuaotes($res["sa_app_start_date"]).$pfx.
		addDoubleQuaotes($res["sa_app_starttime"]).$pfx.
		addDoubleQuaotes($res["email"]).$pfx.
		$cptPart.
		"$";
		
		$contentPart.="\n";
	}
	

	if(empty($contentPart)==false){
		$fileContent="";
		$fileContent.="Survey Designator".$pfx;
		$fileContent.="Client ID".$pfx;
		$fileContent.="Last Name".$pfx;
		$fileContent.="Middle Initial".$pfx;
		$fileContent.="First Name".$pfx;
		$fileContent.="Address 1".$pfx;
		$fileContent.="Address 2".$pfx;
		$fileContent.="City".$pfx;
		$fileContent.="State".$pfx;
		$fileContent.="Zip Code".$pfx;
		$fileContent.="Telephone Number".$pfx;
		$fileContent.="Gender".$pfx;
		$fileContent.="Race".$pfx;
		$fileContent.="Date of Birth".$pfx;
		$fileContent.="Language".$pfx;
		$fileContent.="Medical Record Number".$pfx;
		$fileContent.="Unique ID".$pfx;
		$fileContent.="Location Code".$pfx;
		$fileContent.="Location Name".$pfx;
		$fileContent.="Attending Physician NPI".$pfx;
		$fileContent.="Attending Physician Name".$pfx;
		$fileContent.="Visit Date".$pfx;
		$fileContent.="Visit Time".$pfx;
		$fileContent.="E-mail".$pfx;
		$fileContent.="Procedure Code 1".$pfx;
		$fileContent.="Procedure Code 2".$pfx;
		$fileContent.="Procedure Code 3".$pfx;
		$fileContent.="Procedure Code 4".$pfx;
		$fileContent.="Procedure Code 5".$pfx;
		$fileContent.="Procedure Code 6".$pfx;
		$fileContent.="E.O.R Indicator";
		$fileContent.="\n";
		
		$fileContent.= $contentPart;

		$fp=@fopen($csv_file_name,"w");
		@fwrite($fp,$fileContent);
		@fclose($fp);
	}


	//--- CREATE PDF FILE FOR PRINTING -----
	$hasData=0;
	if($printFile == true){
		$hasData=1;
		echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
	}else{
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>