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
include_once("../../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
//include_once($GLOBALS['fileroot']."/library/html_to_pdf/html2pdf.class.php");

/*
$onePage = false;
$op = 'p';
$html2pdfObj = new HTML2PDF($op,'A4','en');
$html2pdfObj->setTestTdInOnePage($onePage);
*/
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
$onePage = false;
$op = 'p';
$html2pdfObj = new Html2Pdf($op,'A4','en');
$html2pdfObj->setTestTdInOnePage($onePage);

$browserIpad = 'no';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
}
$htmlFolder = "html_to_pdf";
$htmlV2Class = true;
$htmlFilePth = "html_to_pdf/createPdf.php";
$htmlFilename= "file_location";

if(!$createdDate){$createdDate=$_REQUEST['formcreated'];}
if(!$consent_form_id) { $consent_form_id = $_REQUEST['consent_form_id']; }
if(!$consent) { $consent = $_REQUEST['consent']; }
if(!$form_information_id) { $form_information_id = $_REQUEST['form_information_id']; }


$savedDate = ($createdDate) ? date('Y-m-d',strtotime($createdDate)) : '';

//$patient_id = (int) $_SESSION['patient'];
if(!$patient_id) { $patient_id = (int) $_SESSION['patient']; }
$oSaveFile = new SaveFile($patient_id);

if(!is_dir(data_path().'iOLink')) {
	mkdir(data_path().'iOLink');
}

if(!is_dir(data_path().'iOLink/PatientId_'.$patient_id)) {
	mkdir(data_path().'iOLink/PatientId_'.$patient_id);
}
//---- get Patient details ---------
$qry = "select *, date_format(DOB,'".get_sql_date_format()."') as pat_dob, 
									date_format(date,'".get_sql_date_format()."') as reg_date
									from patient_data where id = '". $patient_id."' ";
$patientDetails = get_array_records_query($qry);
$patient_initial = substr($patientDetails[0]['fname'],0,1);
$patient_initial .= substr($patientDetails[0]['lname'],0,1);

$curentDate = date("Y-m-d");

$qryGetPatientSite  = "SELECT sa.procedure_site,sp.proc as saProc, us.pro_title as proTitle, 	
															us.fname as proFName, us.mname as proMname, us.lname as proLName 
															FROM schedule_appointments sa 
															inner join slot_procedures sp on sp.id = sa.procedureid 
															left join users us on us.id = sa.sa_doctor_id 
															WHERE 
															sa.sa_app_start_date >= '".$curentDate."' and  sa.sa_patient_id = '".$patient_id."'  
															and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_start_date asc limit 1";
$patientSiteDetails = get_array_records_query($qryGetPatientSite);
$patientSite = $patientSiteDetails[0]['procedure_site'];
$patientProcedure = $patientSiteDetails[0]['saProc'];
$phy_name = trim($patientSiteDetails[0]['proTitle']).' '.trim($patientSiteDetails[0]['proLName']).', '.trim($patientSiteDetails[0]['proFName']).' '.trim($patientSiteDetails[0]['proMname']);
$phy_name = trim($phy_name);

//--- get physician name -------- it is now geting from Scheduler 29-july-2010
$pro_id = $patientDetails[0]['providerID'];
$qry = "select concat(lname,', ',fname) as name,mname from users where id = '$pro_id'";
$phyDetail = get_array_records_query($qry);
$phy_name_demo = "";
$phy_name_demo = ucwords(trim($phyDetail[0]['name'].' '.$phyDetail[0]['mname']));


// get reffering physician name
$primary_care_id = $patientDetails[0]['primary_care_id'];
$qry = "select concat(LastName,', ',FirstName) as name , MiddleName from refferphysician
		where physician_Reffer_id = '$primary_care_id'";
$reffPhyDetail = get_array_records_query($qry);
$reffer_name = ucwords(trim($reffPhyDetail[0]['name'].' '.$reffPhyDetail[0]['MiddleName']));

//--- get pos facility name -------
$default_facility = $patientDetails[0]['default_facility'];
$qry = "select facilityPracCode from pos_facilityies_tbl 
					where pos_facility_id = '$default_facility'";
$posFacilityDetail = get_array_records_query($qry);
$pos_facility_name = $posFacilityDetail[0]['facilityPracCode'];

//--- get responsible party information ------
$qry = "select *,date_format(dob,'".get_sql_date_format()."') as res_dob 
		from resp_party where patient_id = '$patient_id'";
$resDetails = get_array_records_query($qry);
//--- get epmoyee detail of patient ---
$qry = "select * from employer_data where pid = '".$patient_id."'";
$empDetails = get_array_records_query($qry);
$patientConsentFormName = "";
$patientCreatedDateTime = "";

$qry = "select surgery_consent_name as patientConsentFormName,
								form_created_date as patientCreatedDateTime,
								surgery_consent_data as consent_form_content_data,iolink_pdf_path as iolink_pdf_path_db 
								from surgery_consent_filled_form
								where consent_template_id = '".$consent_form_id."' and patient_id = '".$patient_id."' 
								and surgery_consent_id  = '".$form_information_id."'";
$consentDetail = get_array_records_query($qry);
$chk = count($consentDetail);
if(count($consentDetail) == 0){
	$qry = "select *,consent_data as consent_form_content_data 
						from surgery_center_consent_forms_template where consent_id = '$consent_form_id'";
	$consentDetail = get_array_records_query($qry);
}
else{
	$patientConsentFormName = $consentDetail[0]['patientConsentFormName'];
	$patientCreatedDateTime = $consentDetail[0]['patientCreatedDateTime'];
	$iolink_pdf_path_db 	= urldecode($consentDetail[0]['iolink_pdf_path_db']);
}

for($i=0;$i<count($consentDetail);$i++)
{	
	$consentDetail[$i]['consent_form_content_data'] = html_entity_decode(stripslashes($consentDetail[$i]['consent_form_content_data']));
	
	//--- change value between curly brackets -------	
	$patientConsentFormName = $consentDetail[0]['patientConsentFormName'];
	$patientCreatedDateTime = $consentDetail[0]['patientCreatedDateTime'];
	
	$consent_form_content2 = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consentDetail[$i]['consent_form_content_data']);
	
	//$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$consent_form_content2);
	$consent_form_content2 = str_ireplace("interface/common/html2pdf/","",$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot']."/library/common/html_to_pdf/","",$consent_form_content2);
	$consent_form_content2 = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/common/'.$htmlFolder.'/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/common/html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace('interface/common/html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace('interface/common/new_html2pdf/','',$consent_form_content2);
	if($GLOBALS['webroot']!=''){
		$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/',$consent_form_content2);
	}
	$consent_form_content2 = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$consent_form_content2);
	$consent_form_content2 = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($protocol.$myExternalIP.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($webServerRootDirectoryName.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace('../../main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);	
	$consent_form_content2 = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/redactor/images/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/redactor/images/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($protocol.$http_host.$web_root.'/interface/common/new_html2pdf/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($web_root.'/interface/common/'.$htmlFolder.'/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($web_root.'/interface/common/html2pdf/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($web_root.'/interface/common/new_html2pdf/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace('interface/common/html2pdf/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace('interface/common/new_html2pdf/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
	//$consent_form_content2 = str_ireplace($web_root.'/data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/SigPlus_images/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace('common/html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace('common/new_html2pdf/','',$consent_form_content2);
	
	$consent_form_content2 = str_ireplace('%20',' ',$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content2);
	
	$consent_form_content2 = str_ireplace("{MIDDLE INITIAL}",ucwords($patientDetails[0]['mname']),$consent_form_content2);

	$consent_form_content2 = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT_NICK_NAME}',ucwords($patientDetails[0]['nick_name']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content2);
	
	//=============START WORK TO SHOW THE LAST 4 DIGIT OF PATIENT SS==========================
	if(trim($patientDetails[0]['ss'])!=''){
		$consent_form_content2 = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)),$consent_form_content2);
	}else{
		$consent_form_content2 = str_ireplace('{PATIENT_SS4}','',$consent_form_content2);
	}
	//===========================END WORK===================================================
	$consent_form_content2 = str_ireplace('{SURGEON NAME}',ucwords($phy_name),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name_demo),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{MARITAL STATUS}',ucwords($patientDetails[0]['status']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{ADDRESS1}',ucwords($patientDetails[0]['street']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{ADDRESS2}',ucwords($patientDetails[0]['street2']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{HOME PHONE}',ucwords($patientDetails[0]['phone_home']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{EMERGENCY CONTACT}',ucwords($patientDetails[0]['contact_relationship']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{EMERGENCY CONTACT PH}',ucwords($patientDetails[0]['phone_contact']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{MOBILE PHONE}',ucwords($patientDetails[0]['phone_cell']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{WORK PHONE}',ucwords($patientDetails[0]['phone_biz']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT CITY}',ucwords($patientDetails[0]['city']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT STATE}',ucwords($patientDetails[0]['state']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT ZIP}',ucwords($patientDetails[0]['postal_code']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{REGISTRATION DATE}',ucwords($patientDetails[0]['reg_date']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PatientID}',$_SESSION['patient'],$consent_form_content2);
	
	$consent_form_content2 = str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$consent_form_content2);
	$consent_form_content2 = str_ireplace("{SITE}","<b>".$patientSite."</b>",$consent_form_content2);
	$consent_form_content2 = str_ireplace("{PROCEDURE}","<b>".$patientProcedure."</b>",$consent_form_content2);
	$consent_form_content2 = str_ireplace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$consent_form_content2);
	$consent_form_content2 = str_ireplace("{DATE}","<b>".get_date_format(date('Y-m-d'))."</b>",$consent_form_content2);
	
	$srgnSig = addslashes("{Surgeon's Signature}");
	$consent_form_content2= str_ireplace($srgnSig,"{SIGNATURE}",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Surgeon's Signature}","{SIGNATURE}",$consent_form_content2);
	$consent_form_content2= str_ireplace("{ASSISTANT_SURGEON_SIGNATURE}","{SIGNATURE}",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Nurse's Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Anesthesiologist's Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Witness's Signature}"," ",$consent_form_content2);
	
	$consent_form_content2= str_ireplace("{Surgeon's&nbsp;Signature}","{SIGNATURE}",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Nurse's&nbsp;Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Anesthesiologist's&nbsp;Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Witness's&nbsp;Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Signature}","{SIGNATURE}",$consent_form_content2);
	

	$consent_form_content2 = str_ireplace('{PATIENT INITIAL}',ucwords($patient_initial),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{TEXTBOX_XSMALL}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('{TEXTBOX_SMALL}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('{TEXTBOX_MEDIUM}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('{TEXTBOX_LARGE}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('<p> <br />','',$consent_form_content2);
	$consent_form_content2 = stripslashes($consent_form_content2);
	
	if ($chk == 0)
	{
		$consent_form_content2 = str_ireplace('{SIGNATURE}',"",$consent_form_content2);
		
	}
}

//-- get signature value -------
$qry = "select signature_image_path,signature_count from surgery_consent_form_signature 
							 where surgery_consent_auto_id = '".$form_information_id."' and patient_id = '".$patient_id."'
							 and consent_template_id = '".$consent_form_id."' order by signature_count";
$sigDetail = get_array_records_query($qry);
if ($sigDetail)
{
	$sig_con = array();
	for($s=0;$s<count($sigDetail);$s++){
		$sig_con[$s] = $sigDetail[$s]['signature_image_path'];
		$signature_count[$s] = $sigDetail[$s]['signature_count'];
	}
	$deletePath=array();
	for($ps=0;$ps<count($sig_con);$ps++){
			$row_arr = explode('{START APPLET ROW}',$consent_form_content2);
			$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
			
			$sig_data = '';
			$ds=0;
			$coun=0;
			for($s=1;$s<count($sig_arr);$s++){
				if($s==$signature_count[$ds]){
					$postData = $sig_con[$coun];
					$path1 = explode("/",$postData);
					if(isset($path1[1]) && !empty($path1[1])){
						$sig_data = '<table><tr><td><img src="'.$path1[1].'" height="10%" width="30%"></td></tr></table>';
						$str_data = $sig_arr[$s];
						$sig_arr[$s] = $sig_data;
						$sig_arr[$s] .= $str_data;
						$hiddenFields[] = true;
					}
					$coun++;
					$ds++;
				}
			}
			$consent_form_content2 = implode(' ',$sig_arr);
			$content_row = '';
			for($ro=1;$ro<count($row_arr);$ro++){
				if($row_arr[$ro]){
					$sig_arr1 = explode('{SIGNATURE}',$row_arr[$ro]);
					$td_sign = '';
					for($t=0;$t<count($sig_arr1)-1;$t++,$ds++){
						$sig_arr1[$t] = str_ireplace('&nbsp;','',$sig_arr1[$t]);
						$td_sign .= '
									<td align="left">
										<table border="0">
											<tr><td>'.$sig_arr1[$t].'</td></tr>
											<tr>
												<td style="border:solid 1px" bordercolor="#FF9900">
													{SIGNATURE}
												</td>
											</tr>
										</table>
									</td>	
								';
						$s++;
						$hiddenFields[] = true;
					}
					$content_row .= '
									<table width="145" border="1" align="center">
										<tr>
											'.$td_sign.'						
										</tr>
									</table>
								';
				}
			}
			$jh = 1;
			$consent_form_content2 .= $content_row;
	}
}
else
{
	$consent_form_content2 = str_ireplace('{SIGNATURE}',"",$consent_form_content2);
}
$consent_form_content2 = str_ireplace('{PHYSICIAN SIGNATURE}',"",$consent_form_content2);
$consent_form_content2 = str_ireplace('{WITNESS SIGNATURE}',"",$consent_form_content2);

//By Karan
$currencyReplaceArray = array("$","£");
$consent_filter_data1 = str_replace($currencyReplaceArray,"".showCurrency()."",$consent_form_content2); 
$consent_form_content2 = $consent_filter_data1;

$matchesArr = array();
preg_match_all('@font-family(\s*):(.*?)(\s?)("|;|$)@i', $consent_form_content2, $matchesArr);
if (count($matchesArr[2])>0) {
	foreach($matchesArr[0] as $matchesKey=> $matches ) {
		$matchesVal=str_ireplace('"','',$matches);
		$consent_form_content2=str_ireplace($matchesVal,'',$consent_form_content2);	
	}
}

//--- get all content of consent forms -------	
$consent_content2 .= '
<table id="content_'.$consent_form_id.'" style="display:'.$display.'" width="100%" align="center" cellpadding="1" cellspacing="1" border="0">
	<tr><td align="center" colspan="'.count($sig_arr).'">'.$consent_form_content2.'</td><tr>
</table>';

$consent_form_content2 = str_ireplace('&nbsp;',' ',$consent_form_content2);
$consent_form_content2 = str_ireplace("text' name='medium' size='60' maxlength='60'>",'',$consent_form_content2);
$inputVal = explode('<input',$consent_form_content2);
//pre($inputVal, 1);
$consent_form_content2 = $inputVal[0];
for($i=1;$i<count($inputVal);$i++){
	$inputVals = "";
	$pos = 0;
	$pos = strpos($inputVal[$i],'value="');
	$str = substr($inputVal[$i],$pos+7);
	$pos1 = strpos($str,'"');
	$inputVals = substr($str,0,$pos1);
	$pos2 = strpos($str,'>');
	$lastVal = substr($str,$pos2+1);	
	$consent_form_content2 .= $inputVals.' '.$lastVal;
}

$inputValTextarea = explode('<textarea rows="2" cols="100" name="large',$consent_form_content2);
//pre($inputValTextarea, 1);
if(is_array($inputValTextarea)){
	for($i=1;$i<count($inputValTextarea);$i++){
		$consent_form_content2 = str_ireplace('<textarea rows="2" cols="100" name="large">','',$consent_form_content2);
		$consent_form_content2 = str_ireplace('<textarea rows="2" cols="100" name="large'.$i.'">','',$consent_form_content2);
		$consent_form_content2 = str_ireplace('</textarea>','',$consent_form_content2);
	}
}

$consent_form_content2 = str_ireplace($web_root.'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$consent_form_content2);
$consent_form_content2 = str_ireplace($web_root.'/library/images/','../../library/images/',$consent_form_content2);
$consent_form_content2 = str_ireplace('../../../library/images/','../../library/images/',$consent_form_content2);


if(strncasecmp(PHP_OS, 'WIN', 3) == 0) {	//IF OPERATING SYSTEM IS WINDOW THEN
	$consent_form_content2 = utf8_decode($consent_form_content2);
}

$randomInt = rand(2,12);
$pdf_htm="pdffile".$randomInt;

$consent_form_content2 = html_entity_decode($consent_form_content2);
$fp = fopen(data_path().'iOLink/'.$pdf_htm.'.html','w');
$write_data = fwrite($fp,html_entity_decode($consent_form_content2,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1'));

$html_file_name='';
$fld_path='../../../library/'.$htmlFolder.'/';
//die($consent_form_content2.'hlo'.$insert_id.'@@'.$_REQUEST["consent_form_id"]);
//$html_file_name='pdffile.html';
//if(constant("CONSENT_FORM_VERSION")=="consent_v2" && $htmlFolder == "html_to_pdf") {
if(!file_exists($fld_path.'consent_form')){
	mkdir($fld_path.'consent_form');
}
$html_file_name='consent_form/pdffile_'.$_SESSION['authId'].'.html';
//}
$html_file_name='consent_form/pdffile_'.$_SESSION['authId'].'.html';
$path_set=$fld_path.$html_file_name;
$file_path = write_html($consent_form_content2);
?>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<?php
$strContent2 = "";
if($file_path){
	if($_REQUEST["consent"]){ 
	 	//$oSaveFile->ptDir('iOLink');
		//$iolinkDirPath = data_path(1) . substr($oSaveFile->pDir,1) .'/iolink';
		$iolinkDirPath = data_path().'iOLink/'. substr($oSaveFile->pDir,1);
		if($patientConsentFormName && $patientCreatedDateTime){
			$patientCreatedDateTime = str_ireplace(" ","_",$patientCreatedDateTime);
			$patientCreatedDateTime = str_ireplace(":","-",$patientCreatedDateTime);			
			
			$patientConsentFormName = str_ireplace("&","",$patientConsentFormName);
			$patientConsentFormName = str_ireplace("\\","",$patientConsentFormName);
			$patientConsentFormName = str_ireplace("'","",$patientConsentFormName);
			$patientConsentFormName = str_ireplace("/","_",$patientConsentFormName);
			
			$pdfFileName = $patientConsentFormName.'_'.$patient_id.'_'.$patientCreatedDateTime.'.pdf';
			$pdfFilePath = urldecode($iolinkDirPath.'\\'.$pdfFileName);
			$pdfFilePath = str_ireplace("\\","/",$pdfFilePath);
			$pdfFilePath = str_ireplace(' ','_',$pdfFilePath);		
			
			$qry   = "update surgery_consent_filled_form set iolink_pdf_path = '".$pdfFilePath."'
									where consent_template_id = '".$consent_form_id."' and 
									patient_id = '".$patient_id."' and 
									surgery_consent_id  = '".$form_information_id."'";
			$rsQry = imw_query($qry);
			//file_put_contents('test1.html',(html_entity_decode(html_entity_decode($consent_form_content2,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1'))));
			$strContent2 = str_ireplace("../../data/",$GLOBALS['fileroot']."/data/",$consent_form_content2);
			//if(!file_exists($pdfFilePath)) {
				//$html2pdfObj->WriteHTML(html_entity_decode(html_entity_decode($strContent2,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1')), isset($_GET['vuehtml']));
				//$html2pdfObj->Output($pdfFilePath,'F');
				try {
					$html2pdfObj->writeHTML(html_entity_decode(html_entity_decode($strContent2,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1')), isset($_GET['vuehtml']));
					$newFileName=$html2pdfObj->output($pdfFilePath,'F');
				} catch (Html2PdfException $e) {
					$html2pdfObj->clean();
					$formatter = new ExceptionFormatter($e);
					echo $formatter->getHtmlMessage();
				}
				
			//}
		}
		
?>
		<script type="text/javascript">
		var htmlFilePth = "<?php echo $htmlFilePth;?>";
		var browserIpad = "<?php echo $browserIpad;?>";
		var html_File_name="<?php echo $html_file_name; ?>";
		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';

		if(browserIpad=="yes") {
			//window.open('../../common/<?php echo $htmlFilePth; ?>?font_size=10&page=4&copyPathIolink=<?php echo $pdfFilePath; ?>&<?php echo $htmlFilename; ?>=<?php echo $pdf_htm;?>','_blank','');
			html_to_pdf('<?php echo $file_path; ?>','p');
		}else{ 
			window.focus();
			//top.fmain.all_data.document.getElementById('consent_data_id_surgery').contentWindow.location = "../../common/<?php echo $htmlFilePth; ?>?font_size=10&page=4&copyPathIolink=<?php echo $pdfFilePath; ?>&<?php echo $htmlFilename; ?>=<?php echo $pdf_htm;?>";
			//top.btn_show();
			var file_name = '<?php echo $print_file_name; ?>';
			html_to_pdf('<?php echo $file_path; ?>','p','',true);
			if(typeof(top.btn_show)=="function")top.btn_show();
		}
			
		</script>
<?php 
	
	}
	else{
		//$oSaveFile->ptDir('iOLink');
		//$iolinkDirPath = data_path() . substr($oSaveFile->pDir,1).'/iolink';
		$iolinkDirPath = data_path().'iOLink/'. substr($oSaveFile->pDir,1);
		if($patientConsentFormName && $patientCreatedDateTime){
			$patientCreatedDateTime = str_ireplace(" ","_",$patientCreatedDateTime);
			$patientCreatedDateTime = str_ireplace(":","-",$patientCreatedDateTime);
							
			$patientConsentFormName = str_ireplace("&","",$patientConsentFormName);
			$patientConsentFormName = str_ireplace("\\","",$patientConsentFormName);
			$patientConsentFormName = str_ireplace("'","",$patientConsentFormName);
			$patientConsentFormName = str_ireplace("/","_",$patientConsentFormName);
			$pdfFileName = $patientConsentFormName.'_'.$patient_id.'_'.$patientCreatedDateTime.'.pdf';
			$pdfFilePath = urldecode($iolinkDirPath.'\\'.$pdfFileName);
			$pdfFilePath = str_ireplace("\\","/",$pdfFilePath);
			$pdfFilePath = str_ireplace(' ','_',$pdfFilePath);
			$qry = "update surgery_consent_filled_form set iolink_pdf_path = '$pdfFilePath'
								where consent_template_id = '$consent_form_id' and 
								patient_id = '".$patient_id."' and 
								surgery_consent_id  = '$form_information_id'";
			$rsQry = imw_query($qry);
			$strContent2 = str_ireplace("../../data/",$GLOBALS['fileroot']."/data/",$consent_form_content2);
			//$html2pdfObj->WriteHTML(html_entity_decode(html_entity_decode($strContent2,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1')), isset($_GET['vuehtml']));
			//$html2pdfObj->Output($pdfFilePath,'F');
			try {
				$html2pdfObj->writeHTML(html_entity_decode(html_entity_decode($strContent2,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1')), isset($_GET['vuehtml']));
				$newFileName=$html2pdfObj->output($pdfFilePath,'F');
			} catch (Html2PdfException $e) {
				$html2pdfObj->clean();
				$formatter = new ExceptionFormatter($e);
				echo $formatter->getHtmlMessage();
			}
			
		}
		?>
		<script type="text/javascript">
			<?php if($comeFrom == ""){ ?>				
				top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
				var html_File_name="<?php echo $html_file_name; ?>";	
				if(typeof(top.btn_show)=="function")top.btn_show();
				var htmlFilePth = "<?php echo $htmlFilePth;?>";
				html_to_pdf('<?php echo $file_path; ?>','p','',true);
				
				//window.open('../../../library/<?php echo $htmlFilePth; ?>?pdf_name=<?php echo $pdfFilePath; ?>&<?php echo $htmlFilename; ?>=<?php echo $pdf_htm;?>','_parent','');
		<?php } ?>
			
		</script>
<?php 
	}
?> 
<?php
	/*
	if($comeFrom != "" || (trim($iolink_pdf_path_db) && !file_exists($iolink_pdf_path_db))){
		$font_size = 10;
		$page = 4;
		$copyPathIolink = $pdfFilePath;
		$copyPathIolink = str_ireplace(' ','_',$copyPathIolink);
		$pdfFilePath = urldecode($copyPathIolink);
		$pdfFilePath = stripslashes($pdfFilePath);
		$arrProtocol = (explode("/",$_SERVER['SERVER_PROTOCOL']));
		$arrPathPart = pathinfo($_SERVER['PHP_SELF']);
		$arrPathPart = explode("/",($arrPathPart['dirname']));

		$dir = explode('/',$_SERVER['HTTP_REFERER']);
		$httpPro = $dir[0];
		$httpHost = $dir[2];
		$httpfolder = $dir[3];
		$ip = $_SERVER['REMOTE_ADDR'];
		
		//$myHTTPAddress = $httpPro.'//'.$myExternalIP.'/'.$web_RootDirectoryName.'/library/'.$htmlFilePth;
		$myHTTPAddress = $httpPro.'//'.$myExternalIP.'/'.$web_RootDirectoryName.'/library/html_to_pdf/iolinkMakePdf.php';
		$curNew = curl_init();
		$urlPdfFile = $myHTTPAddress."?copyPathIolink=$pdfFilePath&height=4&font_size=10&ignoreAuth=true&name=$pdf_htm";
		curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
		curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
		echo $data = curl_exec($curNew);
		curl_close($curNew);
	}
	*/
}
?>