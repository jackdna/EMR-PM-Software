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
$andQry='';
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$global_date_format = phpDateFormat();
$showCurrencySymbol = showCurrency();
$getSqlDateFormat = get_sql_date_format();
$getSqlDateFormatSmall = str_replace("Y","y",get_sql_date_format());
$masterPatBal = unserialize(html_entity_decode($letter_pat_bal));
$masterPatAmt = unserialize(html_entity_decode($letter_pat_amt));

if($_REQUEST['collection_template_id']){
	$collectionTemplateId=$_REQUEST['collection_template_id'];
	$andQry=" AND id='".$collectionTemplateId."'";
}

$coll_qry = imw_query("select * from collection_letter_template WHERE 1=1 $andQry order by id desc");
$qryRes_template = imw_fetch_array($coll_qry);
$collection_data=html_entity_decode(stripslashes($qryRes_template['collection_data']));
$template_id = $qryRes_template['id'];

$pt_coll_qry = "select 
		patient_data.id as patient_id,patient_data.lname,patient_data.fname,
		patient_data.mname,patient_data.street,patient_data.street2,patient_data.suffix,
		patient_data.phone_home,patient_data.phone_biz,patient_data.phone_cell,patient_data.title,
		
		patient_data.title,patient_data.city,patient_data.state,patient_data.postal_code,
		date_format(patient_data.DOB,'".$getSqlDateFormat."') as pat_dob,patient_data.External_MRN_1,patient_data.External_MRN_2,
		patient_data.race,patient_data.otherRace,patient_data.language,patient_data.ethnicity,patient_data.otherEthnicity,
		patient_data.pat_account_status,
		
		report_enc_detail.encounter_id,
		date_format(report_enc_detail.date_of_service,'".$getSqlDateFormatSmall."') as date_of_service,
		
		users.lname as physicianLname,users.fname as physicianFname,
		users.mname as physicianMname
		
		from report_enc_detail 
		join patient_data on patient_data.id = report_enc_detail.patient_id
		join users on users.id = report_enc_detail.primary_provider_id_for_reports
		where 
		report_enc_detail.encounter_id in($letter_chk_imp)";

$pt_coll_qry .= " order by report_enc_detail.date_of_service, 
		patient_data.lname,patient_data.fname,patient_data.id";

$pt_coll_res = imw_query($pt_coll_qry);

$main_encounter_id_arr = array();
$mainEncResArr = array();
$main_patient_id_arr = array();
$mainPatResArr = array();
$mainPatACStsArr=array();
while($pt_coll_row = imw_fetch_array($pt_coll_res)) {	
	$encounter_id = $pt_coll_row['encounter_id'];
	$main_encounter_id_arr[] = $encounter_id;
	$mainEncResArr[$encounter_id][] = $pt_coll_row;
	$main_patient_id_arr[$pt_coll_row['patient_id']] = $pt_coll_row['patient_id'];
	$mainPatACStsArr[$pt_coll_row['patient_id']] = $pt_coll_row['pat_account_status'];
	$mainPatResArr[$pt_coll_row['patient_id']][] = $pt_coll_row;
	$mainPatTotBalArr[$pt_coll_row['patient_id']] = $masterPatBal[$pt_coll_row['patient_id']];
	$mainPatTotAmtArr[$pt_coll_row['patient_id']] = $masterPatAmt[$pt_coll_row['patient_id']];
	$mainPatDOSArr[$pt_coll_row['patient_id']][$pt_coll_row['date_of_service']] = $pt_coll_row['date_of_service'];
	
}
$pat_imp=implode(',',$main_patient_id_arr);

// GET RESP PARTY ARRAY
$qry_party = imw_query("select lname as res_lname,fname as res_fname,
			mname as res_mname,address,address2,suffix as res_suffix,
			title as res_title,patient_id,home_ph,work_ph,mobile,city,state,zip from resp_party WHERE patient_id IN(".$pat_imp.")");
$res_party_arr = array();
while ($qry_res_party = imw_fetch_array($qry_party)) {
	$patient_id = $qry_res_party[$i]['patient_id'];
	$res_party_arr[$patient_id][] = $qry_res_party[$i];
}

if(count($mainEncResArr)>0){
	$enc_imp=implode(",",$main_encounter_id_arr);
	$today_date=date('Y-m-d');
	imw_query("update patient_charge_list set collection_sent='1',letter_sent_date='$today_date', letter_sent_id='".$template_id."' where encounter_id in($enc_imp)");
	imw_query("update report_enc_detail set collection_sent='1',letter_sent_date='$today_date', letter_sent_id='".$template_id."' where encounter_id in($enc_imp)");
	
	$collection_data_pdf="";
	$data_pdf = "<style>
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
		</style>";
	//for($e=0;$e<count($main_patient_id_arr);$e++)
	foreach($main_patient_id_arr  as $pat_key => $pat_val){	
		//--- GET Encounter ID ARRAY ----
		//$encounterId=$main_encounter_id_arr[$e];
		$patient_id=$pat_val;
		$pat_id=$mainPatResArr[$patient_id][0]['patient_id'];
		$pat_name_arr['TITLE'] = $mainPatResArr[$patient_id][0]['title'];
		$pat_name_arr['LAST_NAME'] = $mainPatResArr[$patient_id][0]['lname'];
		$pat_name_arr['FIRST_NAME'] = $mainPatResArr[$patient_id][0]['fname'];
		$pat_name_arr['MIDDLE_NAME'] = $mainPatResArr[$patient_id][0]['mname'];
		$patientName = changeNameFormat($pat_name_arr);
		
		$res_name_arr['TITLE'] = $mainPatResArr[$patient_id][0]['res_title'];
		$res_name_arr['LAST_NAME'] = $res_party_arr[$pat_id][0]['res_lname'];
		$res_name_arr['FIRST_NAME'] = $res_party_arr[$pat_id][0]['res_fname'];
		$res_name_arr['MIDDLE_NAME'] = $res_party_arr[$pat_id][0]['res_mname'];
		$resName = changeNameFormat($res_name_arr);
		
		$phy_name=$mainPatResArr[$patient_id][0]['physicianLname'].', '.$mainPatResArr[$patient_id][0]['physicianFname'];
		
		$encounterId = $main_encounter_id_arr[$e];
		$collection_data_pdf=$collection_data;

		$raceShow				= trim($mainPatResArr[$patient_id][0]["race"]);
		$otherRace				= trim($mainPatResArr[$patient_id][0]["otherRace"]);
		if($otherRace) { 
			$raceShow			= $otherRace;
		}
		$language				= str_ireplace("Other -- ","",$mainPatResArr[$patient_id][0]["language"]);
		$ethnicityShow			= trim($mainPatResArr[$patient_id][0]["ethnicity"]);			
		$otherEthnicity			= trim($mainPatResArr[$patient_id][0]["otherEthnicity"]);
		if($otherEthnicity) { 
			$ethnicityShow		= $otherEthnicity;
		}
		
		//new variable added
		$collection_data_pdf = str_ireplace('{PATIENT MRN}',ucwords($mainPatResArr[$patient_id][0]['External_MRN_1']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{PATIENT MRN2}',ucwords($mainPatResArr[$patient_id][0]['External_MRN_2']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{RACE}',$raceShow,$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{LANGUAGE}',$language,$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{ETHNICITY}',$ethnicityShow,$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{PATIENT NAME TITLE}',ucwords($mainPatResArr[$patient_id][0]['title']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{MIDDLE NAME}',ucwords($mainPatResArr[$patient_id][0]['mname']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{PATIENT CITY}',ucwords($mainPatResArr[$patient_id][0]['city']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{STATE ZIP CODE}',ucwords($mainPatResArr[$patient_id][0]['state'].' '.$mainPatResArr[$patient_id][0]['postal_code']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{DOB}',ucwords($mainPatResArr[$patient_id][0]['pat_dob']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{PatientID}',$pat_id,$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{DATE}',date(''.$global_date_format.''),$collection_data_pdf);
		//new variable added
		
		$collection_data_pdf = str_ireplace('{FULL NAME}',ucwords($patientName),$collection_data_pdf);
		//$collection_data_pdf = str_ireplace('{FIRST NAME}',ucwords($mainPatResArr[$patient_id][0]['fname']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{PATIENT FIRST NAME}',ucwords($mainPatResArr[$patient_id][0]['fname']),$collection_data_pdf);
		
		$collection_data_pdf = str_ireplace('{LAST NAME}',ucwords($mainPatResArr[$patient_id][0]['lname']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{SUFFIX}',ucwords($mainPatResArr[$patient_id][0]['suffix']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{ADDRESS1}',ucwords($mainPatResArr[$patient_id][0]['street']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{ADDRESS2}',ucwords($mainPatResArr[$patient_id][0]['street2']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{HOME PHONE}',core_phone_format($mainPatResArr[$patient_id][0]['phone_home']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{WORK PHONE}',core_phone_format($mainPatResArr[$patient_id][0]['phone_biz']),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{MOBILE PHONE}',core_phone_format($mainPatResArr[$patient_id][0]['phone_cell']),$collection_data_pdf);
		
		//==============RESPONSIBLE PARTY VARIABLE REPLACEMENT STARTS HERE==============
		//==IF PATIENT HAVE NO RESPONSIBLE PERSON THEN PATIENT DETAILS WILL REPLACE WITH RESPONSIBLE PERSON DETAILS
		if(!empty($resName) || count($res_party_arr[$pat_id][0])>0){
		  $collection_data_pdf = str_ireplace('{RES FULL NAME}',ucwords($resName),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($res_party_arr[$pat_id][0]['res_fname']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY Last NAME}',ucwords($res_party_arr[$pat_id][0]['res_lname']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES SUFFIX}',ucwords($res_party_arr[$pat_id][0]['res_suffix']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY CITY}',ucwords($res_party_arr[$pat_id][0]['city']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY STATE}',ucwords($res_party_arr[$pat_id][0]['state']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY ZIP}',ucwords($res_party_arr[$pat_id][0]['zip']),$collection_data_pdf);
		  //$collection_data_pdf = str_ireplace('{RES PARTY HOME PH}',core_phone_format($res_party_arr[$pat_id][0]['home_ph']),$collection_data_pdf);
		  //$collection_data_pdf = str_ireplace('{RES PARTY WORK PH}',core_phone_format($res_party_arr[$pat_id][0]['work_ph']),$collection_data_pdf);
		  //$collection_data_pdf = str_ireplace('{RES PARTY MOBILE PH}',core_phone_format($res_party_arr[$pat_id][0]['mobile']),$collection_data_pdf);
		  //$collection_data_pdf = str_ireplace('{RES PARTY ADDRESS1}',ucwords($res_party_arr[$pat_id][0]['address']),$collection_data_pdf);
		  //$collection_data_pdf = str_ireplace('{RES PARTY ADDRESS2}',ucwords($res_party_arr[$pat_id][0]['address2']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY HOME PH.}',core_phone_format($res_party_arr[$pat_id][0]['home_ph']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY WORK PH.}',core_phone_format($res_party_arr[$pat_id][0]['work_ph']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY MOBILE PH.}',core_phone_format($res_party_arr[$pat_id][0]['mobile']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($res_party_arr[$pat_id][0]['address']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($res_party_arr[$pat_id][0]['address2']),$collection_data_pdf);

		}else{
			
		  $collection_data_pdf = str_ireplace('{RES FULL NAME}',ucwords($patientName),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($mainPatResArr[$patient_id][0]['fname']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY Last NAME}',ucwords($mainPatResArr[$patient_id][0]['lname']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES SUFFIX}',ucwords($mainPatResArr[$patient_id][0]['suffix']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY CITY}',ucwords($mainPatResArr[$patient_id][0]['city']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY STATE}',ucwords($mainPatResArr[$patient_id][0]['state']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY ZIP}',ucwords($mainPatResArr[$patient_id][0]['postal_code']),$collection_data_pdf);
		 $collection_data_pdf = str_ireplace('{RES.PARTY HOME PH.}',core_phone_format($mainPatResArr[$patient_id][0]['phone_home']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY WORK PH.}',core_phone_format($mainPatResArr[$patient_id][0]['phone_biz']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY MOBILE PH.}',core_phone_format($mainPatResArr[$patient_id][0]['phone_cell']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($mainPatResArr[$patient_id][0]['street']),$collection_data_pdf);
		  $collection_data_pdf = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($mainPatResArr[$patient_id][0]['street2']),$collection_data_pdf);
			
		}
		//=========================RESPONSIBLE PARTY DATA REPLACEMENT ENDS HERE==========================	
		
		$collection_data_pdf = str_ireplace('{TOTAL OUTSTANDING CHARGES}','$'.number_format($mainPatTotBalArr[$patient_id],2),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{DOS}',implode(',',$mainPatDOSArr[$patient_id]),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{CHARGES}','$'.number_format($mainPatTotAmtArr[$patient_id],2),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{DOS & CHARGES}',implode(',',$mainPatDOSArr[$patient_id]).' & '.$showCurrencySymbol.''.number_format(array_sum($mainPatTotAmtArr[$patient_id]),2),$collection_data_pdf);
		
		//$collection_data_pdf = str_ireplace('{PHYSICIAN}',ucwords($phy_name),$collection_data_pdf);
		$collection_data_pdf = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$collection_data_pdf);
		
		 //=========================IMAGE PATH REPLACEMENT WORK START HERE ==========================	
		//global $myExternalIP, $RootDirectoryName;
		//if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
	
		$collection_data_pdf = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$collection_data_pdf);
		$collection_data_pdf = str_ireplace($web_root.'/interface/reports/new_html2pdf/','',$collection_data_pdf);
		$collection_data_pdf = str_ireplace('%20',' ',$collection_data_pdf);
		$collection_data_pdf = mb_convert_encoding($collection_data_pdf, "HTML-ENTITIES", 'UTF-8');
		
	   /*<page_header>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="450" style="text-align:left;" class="text_b_w">Collection Letter</td>
				<td width="300" style="text-align:right;" class="text_b_w">
					Created by '.$opInitial.' on '.$curDate.'
				</td>
			</tr>
			<tr><td colspan="3" height="1px"></td></tr>
		</table>
		</page_header>*/
	
		$curDate = date(''.$global_date_format.' m:i A');		
		//-- OPERATOR INITIAL -------
		$authProviderNameArr = preg_split('/ /',strtoupper($_SESSION['authProviderName']));
		$opInitial = $authProviderNameArr[1][0];
		$opInitial .= $authProviderNameArr[0][0];
		$opInitial = strtoupper($opInitial);
		$data_pdf.='<page backtop="0mm" backbottom="5mm">
					<page_footer>
						<table style="width: 100%;">
							<tr>
								<td style="text-align:center; width:100%">Page [[page_cu]]/[[page_nb]]</td>
							</tr>
						</table>
					</page_footer>
					
					'.$collection_data_pdf.'
					</page>';

		//DATA SAVING FRO PT DOCS
		$pt_doc_collection_pdf='<page backtop="0mm" backbottom="5mm">
					<page_footer>
					</page_footer>
					'.$collection_data_pdf.'
					</page>';

		/*	REPLACING SMART TAG OPTONS WITH NON-ANCHOR STRING. */
		$regpattern='|<a class=\"cls_smart_tags_link\" id=(.*) href=(.*)>(.*)<\/a>|U'; 
		$pt_doc_collection_pdf = preg_replace($regpattern, "\\3", $pt_doc_collection_pdf);
		$regpattern='|<a id=(.*) class=\"cls_smart_tags_link\" href=(.*)>(.*)<\/a>|U'; 
		$pt_doc_collection_pdf = preg_replace($regpattern, "\\3", $pt_doc_collection_pdf);
		preg_replace('/[^`~!<>@$?a-zA-Z0-9_{}:; ,#%\[\]\.\(\)%&-\/\\r\\n\\\\]/s','',$pt_doc_collection_pdf);	
					
		$qry="Insert INTO pt_docs_collection_letters SET 
		patient_id='".$pat_id."',
		template_id='".$collectionTemplateId."',
		template_content='".htmlentities(addslashes(trim($pt_doc_collection_pdf)))."',
		created_date='".date('Y-m-d H:i:s')."',
		operator_id='".$_SESSION['authId']."'";					
		$rs=imw_query($qry);
		//-----------------------
	}
	
	$data_pdf=utf8_decode($data_pdf);
	$letter_file_location = write_html($data_pdf,'collection_letter_pat.html');
}
?>
<script>
	var letter_file_location = '<?php echo $letter_file_location; ?>';
	top.html_to_pdf(letter_file_location, 'p');
</script>