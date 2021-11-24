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

File: prescription.php
Purpose: This file provides Prescription section in work view.
Access Type : Include
*/
//============GLOBAL FILE INCLUSION=========================
include_once(dirname(__FILE__).'/../../config/globals.php');
//============OTHER FILES INCLUSION=========================
require_once(dirname(__FILE__).'/../../library/classes/Functions.php');
require_once(dirname(__FILE__).'/../../library/classes/SaveFile.php');

//============BELOW OBJECT USED TO CALL MAIN/FUNCTIONS FILE FUNCTIONS
$objManageData = new ManageData;

//==========PHP HEADER ADDED=================================
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

$patientId = $_SESSION['patient'];

$printType = $_REQUEST['printType']; 
$autoId = $_REQUEST['preId']; 

//==========GET PATIENT DATA WORKS START HERE=========
$qryGetpatientDetail = "SELECT 
							*,
							date_format(DOB,'".get_sql_date_format()."') as pat_dob,
							date_format(date,'".get_sql_date_format()."') as reg_date, 
							DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS ptAge
						FROM
							`patient_data`
						WHERE	
							id = '$patientId'
						";
						
$rsGetpatientDetail	= imw_query($qryGetpatientDetail)	or die($qryGetpatientDetail.imw_error());
$numRowGetpatientDetail	= imw_num_rows($rsGetpatientDetail);

if($numRowGetpatientDetail)
{
	extract(imw_fetch_array($rsGetpatientDetail));
	
	$patientname = $fname.','.$lname; 
	if($street)
	{
		$patientAddressFull = $street;
	}
	if($street2)
	{
		$patientAddressFull .= ' '.$street2.',';
	}
	if($city)
	{
		if(!$street2)
		{
			$patientAddressFull .= ',';
		}
		$patientAddressFull .= ' '.$city.', '.$state.' '.$postal_code;
	}
	
	$ptAgeShow = "";
	if($ptAge != ""){
		$ptAgeShow = $ptAge."&nbsp;Yr.";
	}
}
//===========END PATIENT DATA WORK HERE======================	 
//===========GET DOS FOR CHART NOTES HERE====================	 
if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"]))	
{
	$form_id = $_SESSION["form_id"];	
	$finalize_flag = 0;		
}
else if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"]))
{	
	$form_id = $_SESSION["finalize_id"];		
	$finalize_flag = 1;						
}

$print_form_id = $_REQUEST['print_form_id']; // IF PRINT THEN FORM ID
if($print_form_id){
	$form_id = $print_form_id;
}

$sqlQry = "	SELECT 
				* 
			FROM
				`chart_master_table`
			WHERE
				patient_id='$patientId' 
			AND 
				id='$form_id'
		 ";
$qryRes=imw_query($sqlQry);
$co	=	imw_num_rows($qryRes);
if(($co > 0))
{
	$crow	=	imw_fetch_array($qry1);
	$date_of_service =  get_date_format(date("Y-m-d", strtotime($crow["date_of_service"])));	
}
//============END DOS WORK HERE=================================

$today = get_date_format(date('Y-m-d'));

$qryGetSpacialCharValue = "	SELECT 
								drug as medicationName,
								CONCAT(size,'&nbsp;',dosage) as strength, 
								CONCAT(quantity,'&nbsp;',quantity_unit) as quantity,							
								unit as direction1,
								eye as direction2,
								usage_1 as direction3,							 				   
								refills as refill, 		
								substitute as subsitution,
								note as notes		   
						   	FROM
								`prescriptions`
							WHERE 						   
								patient_id = $patientId
							AND
								id=$autoId
						  ";
$rsGetSpacialCharValue = imw_query($qryGetSpacialCharValue)	or die($qryGetSpacialCharValue.imw_error());
$numRowGetSpacialCharValue = imw_num_rows($rsGetSpacialCharValue);
if($numRowGetSpacialCharValue)
{
	extract(imw_fetch_array($rsGetSpacialCharValue));	
}

$qryGetTempData = "	SELECT 
						prescription_template_content as prescriptionTemplateContentData,
						printOption 
					FROM
						`prescription_template`
					WHERE
						prescription_template_type = $printType
					";
					
$rsGetTempData = imw_query($qryGetTempData)	or die($qryGetTempData.imw_error());
$numRowGetTempData = imw_num_rows($rsGetTempData);
if($numRowGetTempData>0)
{
	extract(imw_fetch_array($rsGetTempData));	
	$prescriptionTemplateContentData = stripslashes($prescriptionTemplateContentData);
	$printOptionType = $printOption;
	
	/* TEXTBOX WORK COMMENTED, IT CAN BE ENABLE FROM HERE ANYTIME
	$arrStr = array("{TEXTBOX_XSMALL}","{TEXTBOX_SMALL}","{TEXTBOX_MEDIUM}","{TEXTBOX_LARGE}");
	for($j = 0;$j<count($arrStr);$j++){
		if($arrStr[$j] == '{TEXTBOX_XSMALL}')
		{
			$name = 'xsmall';
			$size = 1;
		}
		else if($arrStr[$j] == '{TEXTBOX_SMALL}')
		{
			$name = 'small';
			$size = 30;
		}
		else if($arrStr[$j] == '{TEXTBOX_MEDIUM}')
		{
			$name = 'medium';
			$size = 60;
		}
		else if($arrStr[$j] == '{TEXTBOX_LARGE}')
		{
			$name = 'large';
			$size = 120;
		}
		$repVal = '';
		if(substr_count($prescriptionTemplateContentData,$arrStr[$j]) > 1)
		{
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$prescriptionTemplateContentData);				
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<input type="text"  name="'.$name.$c.'" value="'.$_POST[$name.$c].'" size="'.$size.'"  maxlength="'.$size.'">';
					$c++;
				}
				$repVal .= end($arrExp);
				$prescriptionTemplateContentData = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$prescriptionTemplateContentData);				
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<textarea rows="2" cols="100" name="'.$name.$c.'"> '.$_POST[$name.$c].' </textarea>';
					$c++;
				}
				$repVal .= end($arrExp);
				$prescriptionTemplateContentData = $repVal;
			}
		}
		else
		{
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$repVal = str_replace($arrStr[$j],'<input type="text" name="'.$name.'" value="'.$_POST[$name].'" size="'.$size.'" >',$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$repVal = str_replace($arrStr[$j],'<textarea rows="2" cols="100" name="'.$name.'"> '.$_POST[$name].' </textarea>',$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = $repVal;
			}
		}		 
	}
	*/
	switch ($direction2):
		case 0:
			$strEye = "PO";
		break;
		case 1:
			$strEye = "OU";
		break;
		case 2:
			$strEye = "OS";
		break;
		case 3:
			$strEye = "OD";
		break;
		case 4:
			$strEye = "RLL";
		break;
		case 5:
			$strEye = "RUL";
		break;
		case 6:
			$strEye = "LLL";
		break;
		case 7:
			$strEye = "LUL";
		break;
		case 8:
			$strEye = "O/O";
		break;
		case 9:
			$strEye = "IV";
		break;
		case 10:
			$strEye = "IM";
		break;
		case 11:
			$strEye = "Topical";
		break;
		case 12:
			$strEye = "L/R Ear";
		break;
		case 13:
			$strEye = "Both Ears";
		break;
	endswitch;
	
	switch ($direction3):
		case 0:
			$strUsage = "qd";
		break;
		case 1:
			$strUsage = "qhs";
		break;
		case 2:
			$strUsage = "qAM";
		break;
		case 3:
			$strUsage = "qid";
		break;
		case 4:
			$strUsage = "bid";
		break;
		case 5:
			$strUsage = "tid";
		break;
		case 6:
			$strUsage = "qod";
		break;
		case 7:
			$strUsage = "__hrs";
		break;
		case 8:
			$strUsage = "__Xdaily";
		break;		
	endswitch;
	switch ($subsitution):
		case 0:
			$strSubsitution = "Permissible";
		break;
		case 1:
			$strSubsitution = "Not Permissible";
		break;
		case 2:
			$strSubsitution = "Brand";
		break;		
	endswitch;
	
	
	//===============MODIFIED VARIABLES==============================================
	$prescriptionTemplateContentData = str_ireplace('{DOB}',$pat_dob,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{DATE}',$today,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{SEX}',$sex,$prescriptionTemplateContentData);
	
	//===============NEW VARIABLES ADDED==============================================
	$prescriptionTemplateContentData = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PatientID}',$_SESSION['patient'],$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{STATE, ZIP CODE}',$patientGeoData,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentData);
	
	$raceShow						 = trim($race);
	$otherRace						 = trim($otherRace);
	if($otherRace){ $raceShow		 = $otherRace;	}
	$languageShow					 = str_ireplace("Other -- ","",$language);
	$ethnicityShow					 = trim($ethnicity);			
	$otherEthnicity					 = trim($otherEthnicity);
	if($otherEthnicity){ $ethnicityShow	= $otherEthnicity;	}
	$prescriptionTemplateContentData = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentData);	
	
	$direction = $direction1.' '.$strEye.' '.$strUsage;
	$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentData);

	$prescriptionTemplateContentData = str_ireplace('{MEDICATION NAME}',$medicationName,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{STRENGTH}',$strength,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{QUANTITY}',$quantity,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{DIRECTION}',$direction,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{REFILL}',$refill,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{SUBSITUTION}',$strSubsitution,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{NOTES}',$notes,$prescriptionTemplateContentData);
	
	//=========PATIENT APPOINTMENT VOCABULARY REPLACEMENT WORKS STARTS HERE===========
	$apptFacInfo = $objManageData->__getApptInfo($_SESSION['patient'],'','','');
	$apptFacname = $apptFacInfo[2];
	
	if(!empty($apptFacInfo[10])){
		$apptFacstreet = $apptFacInfo[10].', ';	
	}	
	if(!empty($apptFacInfo[11])){
		$apptFaccity = $apptFacInfo[11].', ';	
	}
	
	$apptFacaddress =  $apptFacstreet.$apptFaccity.$apptFacInfo[12].'&nbsp;'.$apptFacInfo[13].' - '.$apptFacInfo[3]; 	
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY NAME}',$apptFacname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY ADDRESS}',$apptFacaddress,$prescriptionTemplateContentData);		
	
	//=========LOGGED IN FACILITY INFO VOCABULARY REPLACEMENTS STARTS HERE==========================
	$loggedfacCity = $loggedfacState = $loggedfacCountry = $loggedfacPostalcode = $loggedfacExt = $loginFacility = $loginFacAddress = "";
	$loggedfacilityInfoArr 	= $objManageData->logged_in_facility_info($_SESSION['login_facility']);
	$loggedfacstreet 			= $loggedfacilityInfoArr[1];
	$loggedfacity 		= $loggedfacilityInfoArr[2];
	$loggedfacstate	= $loggedfacilityInfoArr[3];
	$loggedfacPostalcode	= $loggedfacilityInfoArr[4];
	$loggedfacExt	   		= $loggedfacilityInfoArr[5];
	if($loggedfacPostalcode && $loggedfacExt)
	{
		$loggedzipcodext = $loggedfacPostalcode.'-'.$loggedfacExt;
	}
	else
	{
		$loggedzipcodext = $loggedfacPostalcode;
	}
	
	$loginFacility 	= $loggedfacilityInfoArr[0];
	$loginFacAddress = $loggedfacstreet.', '.$loggedfacity.',&nbsp;'.$loggedfacstate.'&nbsp;'.$loggedzipcodext;
	
	$prescriptionTemplateContentData = str_ireplace('{LOGGED_IN_FACILITY_NAME}',$loginFacility,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{LOGGED_IN_FACILITY_ADDRESS}',$loginFacAddress,$prescriptionTemplateContentData);	 				
	//=============================ENDS HERE=============================================================
	
	//=============================TASK ENDS=============================================================
	
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_XSMALL}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_SMALL}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_MEDIUM}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace($web_root.'/interface/common/new_html2pdf/','',$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace($web_root.'/interface/main/uploaddir/document_logos/','../../main/uploaddir/document_logos/',$prescriptionTemplateContentData); //CODE TO REPLACE LOGO PANEL IMAGE
	
	$qryGetSig ="	SELECT 
						id,
						doctorId,
						sign_coords,
						sign_path 
					FROM 
						`chart_assessment_plans`
					WHERE
						form_id = $form_id
					AND
						patient_id = $patientId 
				";	
				  
	$rsGetSig = imw_query($qryGetSig)	or die($qryGetSig.imw_error());
	$numRowGetSig = imw_num_rows($rsGetSig);
	if($numRowGetSig)
	{
		extract(imw_fetch_array($rsGetSig));	
		
		//======PHYSICIAN NAME VOCABULARY REPLACEMENTS WORK STARTS HERE======================
		$getNameQry = imw_query("SELECT 
									CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME,
									fname,
									mname,
									lname,
									pro_suffix,
									licence,
									user_npi,
									sign_path 
								FROM 
									`users` 
								WHERE 
									id = '".$doctorId."'
								");	
		if($doctorId>0)
		{
			$getNameRow = imw_fetch_assoc($getNameQry);
			$PHYSICIANNAME = $getNameRow['PHYSICIANNAME'];
			$phy_fname = $getNameRow['fname'];
			$phy_mname = $getNameRow['mname'];
			$phy_lname = $getNameRow['lname'];
			$phy_suffix = $getNameRow['pro_suffix'];
			$phy_licence = $getNameRow['licence'];
			$phy_npi = $getNameRow['user_npi'];
			$sign_path= ltrim($getNameRow['sign_path'],'/');
			$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',$PHYSICIANNAME,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',$phy_fname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',$phy_mname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',$phy_lname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',$phy_suffix,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NPI}',$phy_npi,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',$phy_licence,$prescriptionTemplateContentData);
		}		
		//===========================TASK ENDS==================================================
		//===========================SIGNATURE WORK STARTS HERE=================================
		$TData = "";
		
		if(!empty($sign_path))
		{ 
			$gdFilenamePath = data_path().$sign_path;
		}
		else if(trim($sign_coords)!='')
		{
			$id = $id;
			$tblName = "chart_assessment_plans";
			$pixelFieldName = "sign_coords";
			$idFieldName = "id";
			$imgPath = "";
			$saveImg = "3";
			include(dirname(__FILE__)."../patient_info/complete_pt_rec/imgGd.php");
			if(!empty($gdFilename))
			{	
				$gdFilenamePath = data_path()."tmp/".$gdFilename;
			}
		}
		if(!empty($gdFilenamePath) && file_exists($gdFilenamePath))
		{
			$TData = "<img align='left' src='".$gdFilenamePath."' height='83' width='225'>";
		}	
		$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',$TData,$prescriptionTemplateContentData);		
	}
	else
	{
		$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NPI}',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',"",$prescriptionTemplateContentData);	
	}
	//==========HTML FILE CREATION==============================
	$file_location = write_html($prescriptionTemplateContentData); 
	
	if(!empty($file_location))
	{
	?>
	<script>
		window.focus();			
		var parWidth = 595;
		var parHeight = 841;
		<?php  
			if($printOptionType == 0)
			{
		?>
				printOptionStyle = 'l';
		<?php	
			}
			else if($printOptionType == 1)
			{
		?>
				printOptionStyle = 'p';
		<?php	
			}  
			$tmp_url = "../../library/html_to_pdf/createPdf.php";  //PDF CREATION FILE
		?>
		window.open('<?php echo $tmp_url; ?>?printType='+printOptionStyle+'&file_location=<?php echo $file_location;?>','_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
	</script>
	<?php 
	}
}
else
{
	echo "<script>alert('Please create your Medical Rx template to proceed print.');</script>";
}
?>