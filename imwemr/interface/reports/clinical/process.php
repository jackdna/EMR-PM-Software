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
FILE : process.php
PURPOSE : 
ACCESS TYPE : INCLUDED
*/
include("../../../config/globals.php");
set_time_limit(0);
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
include("../../../library/classes/ChartApXml.php");
require_once('../../../library/classes/cls_common_function.php');
$xml_arr = new ChartApXml;
$CLSCommonFunction = new CLSCommonFunction;

$cpt_DetailsArr = array();
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = $row['cpt_prac_code'];
	$cpt_DetailsArr[$cpt_fee_id] = $cpt_prac_code;
}

$printFile = true;
$_REQUEST['dxcodes'] = (isset($_REQUEST['dxcodes'])) ? $_REQUEST['dxcodes'] : ''; 
$_REQUEST['dxcodes10'] = (isset($_REQUEST['dxcodes10'])) ? $_REQUEST['dxcodes10'] : '';
$_REQUEST['dxcodes10'] = implode(',',$_REQUEST['dxcodes10']);
$_REQUEST['medications'] = (isset($_REQUEST['medications'])) ? $_REQUEST['medications'] : '';
$_REQUEST['medications'] = implode(',',$_REQUEST['medications']);
//Code for proc_codes commented and passing blank in R7
$_REQUEST['proc_codes'] = (isset($_REQUEST['proc_codes'])) ? $_REQUEST['proc_codes'] : '';
$proc_codes_id_imp 		= implode(",",$_REQUEST['proc_codes']);
$proc_codes_id_arr 		= $_REQUEST['proc_codes'];
$proc_codes_imp = "";
if(count($proc_codes_id_arr)>0) {
	foreach($proc_codes_id_arr as $proc_codes_id) {
		$proc_codes_arr[] = $cpt_DetailsArr[$proc_codes_id];
	}
	$proc_codes_imp = implode(",",$proc_codes_arr);
}

$_REQUEST['cd_ratio'] = (isset($_REQUEST['cdRatio'])) ? $_REQUEST['cdRatio'] : '';
$_REQUEST['iop_pressure'] = (isset($_REQUEST['iop_pressure'])) ? $_REQUEST['iop_pressure'] : ''; 
//Code for inc_exc_condition commented and passing blank in R7
$_REQUEST['inc_exc_condition'] = (isset($_REQUEST['inc_exc_condition'])) ? $_REQUEST['inc_exc_condition'] : ''; 

$_REQUEST['ethnicity'] = (isset($_REQUEST['ethnicity'])) ? $_REQUEST['ethnicity'] : '';
$_REQUEST['ethnicity'] = implode(',',$_REQUEST['ethnicity']);
$_REQUEST['race'] = (isset($_REQUEST['race'])) ? $_REQUEST['race'] : ''; 
$_REQUEST['race'] = implode(',',$_REQUEST['race']);
$_REQUEST['language'] = (isset($_REQUEST['language'])) ? $_REQUEST['language'] : ''; 
$_REQUEST['language'] = implode(',',$_REQUEST['language']);
$_REQUEST['hippa_compliant'] = (isset($_REQUEST['hippa_compliant'])) ? 'yes' : 'no'; 
//passing blank in R7
$_REQUEST['sort_result'] = (isset($_REQUEST['sort_result'])) ? $_REQUEST['sort_result'] : ''; 
$_REQUEST['and_or'] = (isset($_REQUEST['sel_and_or'])) ? $_REQUEST['sel_and_or'] : ''; 
$_REQUEST['physicians'] = (isset($_REQUEST['physicians'])) ? $_REQUEST['physicians'] : '';
$_REQUEST["physicians"] = implode(',',$_REQUEST["physicians"]);
$_REQUEST['rd_med_allergy_list'] = (isset($_REQUEST['rd_med_allergy'])) ? $_REQUEST['rd_med_allergy'] : '1'; 
$_REQUEST['hippa_mail'] = (isset($_REQUEST['hippa_mail'])) ? $_REQUEST['hippa_mail'] : '0';
$_REQUEST['hippa_email'] = (isset($_REQUEST['hippa_email'])) ? $_REQUEST['hippa_email'] : '0';
$_REQUEST['hippa_voice'] = (isset($_REQUEST['hippa_voice'])) ? $_REQUEST['hippa_voice'] : '0';
//pre($_REQUEST);die;
$_REQUEST['suitableTime']='';
if(($_REQUEST['hourFrom']!='' && $_REQUEST['minFrom']!='') || ($_REQUEST['hourTo']!='' && $_REQUEST['minTo']!='')) {
    $_REQUEST['suitableTime']= $_REQUEST['hourFrom'].':'.$_REQUEST['minFrom'].':'.$_REQUEST['ampmFrom'].'-'.$_REQUEST['hourTo'].':'.$_REQUEST['minTo'].':'.$_REQUEST['ampmTo'];
}
$_REQUEST['enteredTime']='';
if(($_REQUEST['enHourFrom']!='' && $_REQUEST['enMinFrom']!='') || ($_REQUEST['enHourTo']!='' && $_REQUEST['enMinTo']!='')) {
    $_REQUEST['enteredTime']= $_REQUEST['enHourFrom'].':'.$_REQUEST['enMinFrom'].':'.$_REQUEST['enAmpmFrom'].'-'.$_REQUEST['enHourTo'].':'.$_REQUEST['enMinTo'].':'.$_REQUEST['enAmpmTo'];
}

$_REQUEST['mrGiven'] = (isset($_REQUEST['mrGiven']) && $_REQUEST['mrGiven']=='on') ? '1' : '0';
$_REQUEST['physicians_new'] = (isset($_REQUEST['physicians_new'])) ? $_REQUEST['physicians_new'] : '';

$included_excluded = "";
$str_selection_criteria = "";
$dateFormat= get_sql_date_format();
$phpDateFormat=phpDateFormat();
$phpDateSlash=str_replace('-','/', $phpDateFormat);

$curDate = date($phpDateFormat . ' h:i A');
$op_name_arr = preg_split('/, /', strtoupper($_SESSION['authProviderName']));
$createdBy = ucfirst(trim($op_name_arr[1][0]));
$createdBy .= ucfirst(trim($op_name_arr[0][0]));

$arrSelPhy = explode(',',$_REQUEST["physicians"]);
for($i=0; $i<sizeof($arrSelPhy);$i++){
	$arrSelPhysicians[$arrSelPhy[$i]]=$arrSelPhy[$i];
}
$arrSelEthnicity = explode(',',$_REQUEST["ethnicity"]);
foreach($arrSelEthnicity as $val){
	if(strtolower($val)!='other'){
		$arrEthWithoutOther[$val]=$val;
	}
}
$arrSelRace = explode(',',$_REQUEST["race"]);
foreach($arrSelRace as $val){
	if(strtolower($val)!='other'){
		$arrRaceWithoutOther[$val]=$val;
	}
}
$arrSelLanguage = explode(',',$_REQUEST["language"]);
foreach($arrSelLanguage as $val){
	if(strtolower($val)!='other'){
		$arrLangWithoutOther[$val]=$val;
	}
}
$phyNewExists = 0;
$arrSelPhyNew = explode(',',$_REQUEST["physicians_new"]);
for($i=0; $i<sizeof($arrSelPhyNew);$i++){
	$arrSelPhysiciansNew[$arrSelPhyNew[$i]]=$arrSelPhyNew[$i];
	if(trim($arrSelPhyNew[$i])) {
		$phyNewExists = 1;	
	}
}
if($phyNewExists==1){
	$phyNewImp = implode(",",$arrSelPhyNew);	
}
$diabetic_exam = trim($_REQUEST['diabetic_exam']);
//echo $phyNewExists;pre($arrSelPhyNew);die;
//print "<pre>";
//print_r($arrSelEthnicity);
//$_REQUEST["and_or"];
$arr_pt_ids=array();
//changing date format
$dtEffectiveDate = "";

//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
$arrDateRange= $CLSCommonFunction->changeDateSelection();


if($dayReport=='Daily'){
	$_REQUEST['eff_date'] = $_REQUEST['eff_date2']= date($phpDateFormat);
}else if($dayReport=='Weekly'){
	$_REQUEST['eff_date'] = $arrDateRange['WEEK_DATE'];
	$_REQUEST['eff_date2'] = date($phpDateFormat);
}else if($dayReport=='Monthly'){
	$_REQUEST['eff_date'] = $arrDateRange['MONTH_DATE'];
	$_REQUEST['eff_date2'] = date($phpDateFormat);
}else if($dayReport=='Quarterly'){
	$_REQUEST['eff_date'] = $arrDateRange['QUARTER_DATE_START'];
	$_REQUEST['eff_date2'] = $arrDateRange['QUARTER_DATE_END'];
}

if(isset($_REQUEST['eff_date']) && $_REQUEST['eff_date'] != "" && $_REQUEST['eff_date'] != "00-00-0000"){
	$dtEffectiveDate = $_REQUEST['eff_date'];
	$dtDBEffectDate = getDateFormatDB($dtEffectiveDate);
	$dtShowEffectDate = $dtEffectiveDate;
	
	//changing date format
	if(isset($_REQUEST['eff_date2']) && $_REQUEST['eff_date2'] != "" && $_REQUEST['eff_date2'] != "00-00-0000"){
		$dtEffectiveDate2 = $_REQUEST['eff_date2'];	
		$dtDBEffectDate2 = getDateFormatDB($dtEffectiveDate2);
		$dtShowEffectDate2 = $dtEffectiveDate2;
	}else{
		$dtDBEffectDate2 = $dtDBEffectDate;
		$dtShowEffectDate2 = $dtShowEffectDate;
	}
}

if($dtEffectiveDate != ""){
	$str_selection_criteria .= "Date Range: ".$dtShowEffectDate." - ".$dtShowEffectDate2." ";
}

//START CODE FOR DIABETIC EXAM
if($diabetic_exam) {
	$consult_and_qry = "";
	if($dtEffectiveDate != ""){
		$consult_and_qry .= " AND (cmt.date_of_service BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
	}
	if(trim($_REQUEST["physicians"])) {
		$consult_and_qry .= " AND cmt.providerId IN(".trim($_REQUEST["physicians"]).") ";	
	}
	$andDiabExmQry = "";
	if($diabetic_exam == "all") {
		$andDiabExmQry = " AND (pcl.templateName LIKE '%Red Alert%' OR templateName LIKE '%Green Alert%') ";
	}elseif($diabetic_exam == "green") {
		$andDiabExmQry = " AND (pcl.templateName LIKE '%Green Alert%') ";
	}elseif($diabetic_exam == "red") {
		$andDiabExmQry = " AND (pcl.templateName LIKE '%Red Alert%') ";
	}
	
	$ptConsultQry = "SELECT pcl.patient_consult_id, pcl.patient_id, pcl.patient_form_id, pcl.templateName, 
							cmt.providerId, CONCAT(usr.lname,', ',usr.fname) as provider_name, 
							CONCAT(pd.lname,', ',pd.fname,' ',pd.mname) AS patient_name,
							date_format(pd.dob,'".$dateFormat."') as dob, pd.primary_care_phy_name, pd.primary_care_phy_id,
							ROUND(((DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'),pd.dob))/365), 0) as age,
							co.cd_val_od AS chart_optic_cd_val_od, co.cd_val_os  AS chart_optic_cd_val_os,
							ci.multiple_pressure AS chart_iop,
							sb.arr_dx_codes,
							insd.plan_name,insd.policy_number,
							rp.physician_fax AS pcp_fax_number,
							rp.physician_email AS pcp_email
					 FROM patient_consult_letter_tbl pcl
					 INNER JOIN chart_master_table cmt ON (cmt.id = pcl.patient_form_id ".$consult_and_qry.")	
					 INNER JOIN patient_data pd ON (pd.pid = pcl.patient_id )
					 LEFT JOIN users usr ON (usr.id = cmt.providerId)
					 LEFT JOIN chart_optic co ON (co.form_id = pcl.patient_form_id)
					 LEFT JOIN chart_iop ci ON (ci.form_id = pcl.patient_form_id)
					 LEFT JOIN superbill sb ON (sb.formId = pcl.patient_form_id)
					 LEFT JOIN insurance_data insd ON (insd.ins_caseid = cmt.caseId AND insd.pid = cmt.patient_id AND insd.type='primary' AND insd.del_status='0' AND insd.provider!='' AND insd.provider!='0' AND insd.actInsComp='1')
					 LEFT JOIN refferphysician rp ON (rp.physician_Reffer_id = pd.primary_care_phy_id)
					 WHERE 1=1 AND pcl.patient_form_id !='0' ".$andDiabExmQry. " ORDER BY pd.lname, pd.fname ASC, usr.lname, pcl.templateName LIKE '%Red Alert%' DESC, pcl.templateName LIKE '%Green Alert%' DESC, pcl.patient_id,pcl.patient_consult_id ";
	$ptConsultRes = imw_query($ptConsultQry) or die(imw_error());
	if(imw_num_rows($ptConsultRes)>0) {
		while($ptConsultRow = imw_fetch_assoc($ptConsultRes)) {
			$consult_patient_id 	= $ptConsultRow["patient_id"];
			$consult_form_id 		= $ptConsultRow["patient_form_id"];
			$consult_providerId		= $ptConsultRow["providerId"];
			$consult_provider_name	= $ptConsultRow["provider_name"];
			$consult_patient_name 	= $ptConsultRow["patient_name"];
			$consult_template_name	= $ptConsultRow["templateName"];
			
			$consult_diab_exam_color= "";
			if(stristr(strtolower($consult_template_name),"red alert")) {
				$consult_diab_exam_color = "#EB1F23";	
			}elseif(stristr(strtolower($consult_template_name),"green alert")) {
				$consult_diab_exam_color = "#4F9821";	
			}
			
			$consult_chart_optic_cd_val_od	= trim($ptConsultRow["chart_optic_cd_val_od"]);
			$consult_chart_optic_cd_val_os	= trim($ptConsultRow["chart_optic_cd_val_os"]);
			$consult_cd_ratio_html = "";
			if($consult_chart_optic_cd_val_od || $consult_chart_optic_cd_val_os) {
				$consult_cd_ratio_html .= "<table style=\"width:99%;font-size:12px;text-decoration:none; vertical-align:top;border-bottom:0px;\" align=\"left\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";

				if($consult_chart_optic_cd_val_od) {
					$consult_cd_ratio_html .= "<tr><td style=\"width:10px; vertical-align:top;border-bottom:0px;\"  align=\"left\">OD:</td><td style=\"width:50px;border-bottom:0px;\"  align=\"left\">".$consult_chart_optic_cd_val_od."</td></tr>";				
				}
				if($consult_chart_optic_cd_val_os) {
					$consult_cd_ratio_html .= "<tr><td style=\"width:10px; vertical-align:top;border-bottom:0px;\"  align=\"left\">OS:</td><td style=\"width:50px;border-bottom:0px;\"  align=\"left\">".$consult_chart_optic_cd_val_os."</td></tr>";				
				}
				$consult_cd_ratio_html .= "</table>";
			}
			
			$consult_chart_iop 		= array();
			$consult_chart_iop		= unserialize($ptConsultRow["chart_iop"]);
			
			$consult_chart_ta_od 	= $consult_chart_iop["multiplePressuer"]["elem_appOd"];
			$consult_chart_ta_os 	= $consult_chart_iop["multiplePressuer"]["elem_appOs"];
			$consult_chart_ta_time 	= $consult_chart_iop["multiplePressuer"]["elem_appTime"];
			
			$consult_chart_tp_od 	= $consult_chart_iop["multiplePressuer"]["elem_puffOd"];
			$consult_chart_tp_os 	= $consult_chart_iop["multiplePressuer"]["elem_puffOs"];
			$consult_chart_tp_time 	= $consult_chart_iop["multiplePressuer"]["elem_puffTime"];

			$consult_chart_tx_od 	= $consult_chart_iop["multiplePressuer"]["elem_appTrgtOd"];
			$consult_chart_tx_os 	= $consult_chart_iop["multiplePressuer"]["elem_appTrgtOs"];
			$consult_chart_tx_time 	= $consult_chart_iop["multiplePressuer"]["elem_xTime"];

			$consult_chart_tt_od 	= $consult_chart_iop["multiplePressuer"]["elem_tactTrgtOd"];
			$consult_chart_tt_os 	= $consult_chart_iop["multiplePressuer"]["elem_tactTrgtOs"];
			$consult_chart_tt_time 	= $consult_chart_iop["multiplePressuer"]["elem_ttTime"];
			
			$consult_chart_iop_html = "";
			if($consult_chart_ta_od || $consult_chart_ta_os || $consult_chart_tp_od 
				|| $consult_chart_tp_os || $consult_chart_tx_od || $consult_chart_tx_os
				|| $consult_chart_tt_od || $consult_chart_tt_os) {
					
				$consult_chart_iop_html .= "<table style=\"width:99%;font-size:12px;text-decoration:none;\" align=\"left\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
				
				if($consult_chart_ta_od) {
					$consult_chart_iop_html .= "<tr><td style=\"width:20px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">T<sub>A</sub>&nbsp;OD:</td><td style=\"width:70px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">".$consult_chart_ta_od." ".$consult_chart_ta_time."</td></tr>";					
				}
				
				if($consult_chart_ta_os) {
					$consult_chart_iop_html .= "<tr><td style=\"width:20px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">T<sub>A</sub>&nbsp;OS:</td><td style=\"width:70px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">".$consult_chart_ta_os." ".$consult_chart_ta_time."</td></tr>";					
				}
				
				if($consult_chart_tp_od) {
					$consult_chart_iop_html .= "<tr><td style=\"width:20px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">T<sub>P</sub>&nbsp;OD:</td><td style=\"width:70px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">".$consult_chart_tp_od." ".$consult_chart_tp_time."</td></tr>";					
				}
				if($consult_chart_tp_os) {
					$consult_chart_iop_html .= "<tr><td style=\"width:20px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">T<sub>P</sub>&nbsp;OS:</td><td style=\"width:70px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">".$consult_chart_tp_os." ".$consult_chart_tp_time."</td></tr>";					
				}
				
				if($consult_chart_tx_od) {
					$consult_chart_iop_html .= "<tr><td style=\"width:20px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">T<sub>P</sub>&nbsp;OD:</td><td style=\"width:70px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">".$consult_chart_tx_od." ".$consult_chart_tx_time."</td></tr>";					
				}
				if($consult_chart_tx_os) {
					$consult_chart_iop_html .= "<tr><td style=\"width:20px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">T<sub>P</sub>&nbsp;OS:</td><td style=\"width:70px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">".$consult_chart_tx_os." ".$consult_chart_tx_time."</td></tr>";					
				}
				
				if($consult_chart_tt_od) {
					$consult_chart_iop_html .= "<tr><td style=\"width:20px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">T<sub>T</sub>&nbsp;OD:</td><td style=\"width:70px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">".$consult_chart_tt_od." ".$consult_chart_tt_time."</td></tr>";					
				}
				if($consult_chart_tt_os) {
					$consult_chart_iop_html .= "<tr><td style=\"width:20px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">T<sub>T</sub>&nbsp;OS:</td><td style=\"width:70px; vertical-align:top; white-space:nowrap; border-bottom:0px;\"  align=\"left\">".$consult_chart_tt_os." ".$consult_chart_tt_time."</td></tr>";					
				}
				
				
				$consult_chart_iop_html .= "</table>";
			}
			
			
			$consult_chart_dx_arr	= unserialize($ptConsultRow["arr_dx_codes"]);
			$consult_chart_dx_arr_new = array();
			if(is_array($consult_chart_dx_arr)) {
				foreach($consult_chart_dx_arr as $consult_chart_dx) {
					if($consult_chart_dx) {
						$consult_chart_dx_arr_new[] = $consult_chart_dx;	
					}
				}
			}
			$consult_chart_dx_html = implode(", ",$consult_chart_dx_arr_new);

			
			$consult_ins_plan_html = "";
			$consult_ins_plan_name	= trim($ptConsultRow["plan_name"]);
			$consult_ins_policy_num	= trim($ptConsultRow["policy_number"]);
			if($consult_ins_plan_name) {
				$consult_ins_plan_html .= $consult_ins_plan_name;	
				if($consult_ins_policy_num) {
					$consult_ins_plan_html .= ' - '.$consult_ins_policy_num;	
				}
			}
			
			
			$consult_patient_id_arr[$consult_form_id] 			= $consult_patient_id;
			$consult_providerId_arr[$consult_form_id]			= $consult_providerId;
			$consult_provider_name_arr[$consult_providerId]		= $consult_provider_name;
			
			$consultPtArr[$consult_patient_id]["pt_name"] 		= $ptConsultRow["patient_name"];
			$consultPtArr[$consult_patient_id]["pt_dob"] 		= $ptConsultRow["dob"];
			$consultPtArr[$consult_patient_id]["pt_age"] 		= $ptConsultRow["age"];
			$consultPtArr[$consult_patient_id]["pt_pcp"] 		= $ptConsultRow["primary_care_phy_name"];
			$consultPtArr[$consult_patient_id]["pt_pcp_id"]		= $ptConsultRow["primary_care_phy_id"];
			$consultPtArr[$consult_patient_id]["pt_pcp_fax_no"]	= $ptConsultRow["pcp_fax_number"];
			$consultPtArr[$consult_patient_id]["pt_pcp_email"]	= $ptConsultRow["pcp_email"];
			
			$consultFormArr[$consult_form_id]["pt_diab_color"] 	= $consult_diab_exam_color;
			$consultFormArr[$consult_form_id]["pt_cd_ratio"] 	= $consult_cd_ratio_html;
			$consultFormArr[$consult_form_id]["pt_iop"] 		= $consult_chart_iop_html;
			$consultFormArr[$consult_form_id]["pt_chart_dx"]	= $consult_chart_dx_html;
			$consultFormArr[$consult_form_id]["pt_ins_plan"]	= $consult_ins_plan_html;
			$consultFormArr[$consult_form_id]["pt_tmplte_name"]	= $consult_template_name;
			$consultFormArr[$consult_form_id]["pt_consult_id"]	= $ptConsultRow["patient_consult_id"];
			
		}
	}
}
//END CODE FOR DIABETIC EXAM

$showEnTimeFrom=$showEnTimeTo=$enTimeFrom=$enTimeTo='';
if(trim($_REQUEST["enteredTime"])!= ""){
	$timePart=explode('-', $_REQUEST["enteredTime"]);
	
	$tFrom=explode(':', $timePart[0]);
	$showEnTimeFrom=$tFrom[0].':'.$tFrom[1].' '.$tFrom[2];
	if($tFrom[2]=='PM'){
		$tFrom[0] = 12 + $tFrom[0];
		if($tFrom[0]=='24'){ $tFrom[0]='00'; }
	}

	$tTo=explode(':', $timePart[1]);
	$showEnTimeTo=$tTo[0].':'.$tTo[1].' '.$tTo[2];
	if($tTo[2]=='PM'){
		$tTo[0] = 12 + $tTo[0];
		if($tTo[0]=='24'){ $tTo[0]='00'; }
	}
	
	$enTimeFrom=$tFrom[0].':'.$tFrom[1].':00';
	$enTimeTo=$tTo[0].':'.$tTo[1].':00';
}

if($_REQUEST["enteredTime"] != ""){
	$str_selection_criteria .= "Entered Time: ".$showEnTimeFrom." - ".$showEnTimeTo." ";
}

//ICD9
$dx_codes = (isset($_REQUEST["dxcodes"]) && trim($_REQUEST["dxcodes"]) != "") ? "'".str_replace(",","','",$_REQUEST["dxcodes"])."'" : "";
$str_selection_criteria .= (isset($_REQUEST["dxcodes"]) && trim($_REQUEST["dxcodes"]) != "") ? "ICD9: ".str_replace(",",", ",$_REQUEST["dxcodes"])." " : "";

//ICD10
$dx_codes10 = (isset($_REQUEST["dxcodes10"]) && trim($_REQUEST["dxcodes10"]) != "") ? "'".str_replace(",","','",$_REQUEST["dxcodes10"])."'" : "";
$str_selection_criteria .= (isset($_REQUEST["dxcodes10"]) && trim($_REQUEST["dxcodes10"]) != "") ? "ICD10: ".str_replace(",",", ",$_REQUEST["dxcodes10"])."," : "";

//proc_codes
$procCodes = (isset($proc_codes_imp) && trim($proc_codes_imp) != "") ? "'".str_replace(",","','",$proc_codes_imp)."'" : "";
$str_selection_criteria .= (isset($proc_codes_imp) && trim($proc_codes_imp) != "") ? "CPT: ".str_replace(",",", ",$proc_codes_imp)."," : "";

$patient_id = array();
$i = 0;
$dx_code_patient=true;
$pt_dx_codes = array();
if($dx_codes != "" || $dx_codes10 != "" || $procCodes != ""){
	$dx_code_patient=false;
	$dx_condition1 = ($_REQUEST["rd_problem_list"] == "1") ? "IN" : "NOT IN";
	$dx_condition2 = ($_REQUEST["rd_problem_list"] == "1") ? "OR" : "AND";
	$dx_condition3 = ($_REQUEST["rd_cpt_list"] == "1") 	   ? "IN" : "NOT IN";
	$included_excluded .= ($_REQUEST["rd_problem_list"] == "1") ? "Dx Code: Included " : "Dx Code: Excluded ";
	$included_excluded .= ($_REQUEST["rd_cpt_list"] == "1") ? "CPT Code: Included " : "CPT Code: Excluded ";

	//GET ICD9 DESCRIPTION
	$dx_master_val=array();
	if($dx_codes!=''){
		$dx_master_qry = "SELECT dx_code, diag_description FROM diagnosis_code_tbl WHERE dx_code IN (".$dx_codes.")";
		$dx_master_res = imw_query($dx_master_qry);
		$dx_master_val = array();
		if(imw_num_rows($dx_master_res) > 0){
            while($dx_this_arr = imw_fetch_array($dx_master_res)) {
                if(is_array($dx_master_arr) && count($dx_master_arr) > 0){
                    $dx_master_val[$dx_this_arr["dx_code"]] = $dx_this_arr["diag_description"];
                }
            }
			
//			if(is_array($dx_master_arr) && count($dx_master_arr) > 0){
//				foreach($dx_master_arr as $dx_this_arr){
//					$dx_master_val[$dx_this_arr["dx_code"]] = $dx_this_arr["diag_description"];
//				}
//			}
		}
	}
	//GET ICD10 DESCRIPTION
	$dx10_master_val=array();
	if($dx_codes10!=''){

		$arrMasterIcd10Codes=array();
		//GETTTING SOURCE ICD10 CODES FROM MASTER TABLE
		$rs = imw_query("SELECT icd10 FROM icd10_data WHERE icd10!=''");
		while($res=imw_fetch_assoc($rs)){
			$icd10=str_replace('-', '', $res['icd10']);
			if(preg_match("/".$icd10."/", $dx_codes10)){
				$arrMasterIcd10Codes[$res['icd10']]=$res['icd10'];
			}
		}
		
		if(sizeof($arrMasterIcd10Codes)>0){
			$strMasterIcd10Codes= "'".implode("','",$arrMasterIcd10Codes)."'";
			$dx_master_qry = "SELECT icd10, icd10_desc FROM icd10_data WHERE icd10 IN (".$strMasterIcd10Codes.")";
			$dx_master_res = imw_query($dx_master_qry);
			$dx10_master_val = array();
			$dx10_master_arr = array();
			if(imw_num_rows($dx_master_res) > 0){
				while($dx10_this_arr = imw_fetch_array($dx_master_res)) {
                    if(is_array($dx10_this_arr) && count($dx10_this_arr) > 0){
                        $dx10_master_val[$dx10_this_arr["icd10"]] = $dx10_this_arr["icd10_desc"];
                    }
                }
//				if(is_array($dx10_master_arr) && count($dx10_master_arr) > 0){
//					foreach($dx10_master_arr as $dx10_this_arr){
//						$dx10_master_val[$dx10_this_arr["icd10"]] = $dx10_this_arr["icd10_desc"];
//					}
//				}
			}
		}
	}

	$dx9And10_master_val=array();
	$dx9And10_master_val=array_merge($dx_master_val, $dx10_master_val);

	$arr_dx_codes=array();
	$arr_dx_codes10=array();
	$arr_cpt_codes=array();
	$arr_dx_temp = explode(",", $_REQUEST["dxcodes"]);
	$arr_dx_codes=array_combine($arr_dx_temp, $arr_dx_temp);
	$arr_dx_temp10 = explode(",", $_REQUEST["dxcodes10"]);
	$arr_dx_codes10=array_combine($arr_dx_temp10, $arr_dx_temp10);
	$arr_dx_codes10=array_merge($arr_dx_codes, $arr_dx_codes10);
	$arr_cpt = explode(",", $proc_codes_imp);
	unset($arr_dx_temp);
	unset($arr_dx_temp10);
	$sup_exclude_arr=array();
	if($dx_condition3=="NOT IN" && $procCodes != ""){
		$sel_dx_qry = "	SELECT pi.idSuperBill FROM superbill as sb, procedureinfo as pi WHERE pi.idSuperBill = sb.idSuperBill AND sb.postedStatus='0' AND pi.delete_status='0'";	
		if($procCodes != "") {
			$sel_dx_qry.=" AND (pi.cptCode in (".$procCodes.")) ";	
		}			
		if($dtEffectiveDate != ""){
			$sel_dx_qry .= " AND (sb.dateOfService BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
		}
		if($enTimeFrom!='' || $enTimeTo!=''){
			$sel_dx_qry .= " AND (sb.timeSuperBill BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
		}
		$sel_dx_qry .= "GROUP BY pi.idSuperBill";
		$sel_chlist=imw_query($sel_dx_qry);
		while($row_chlist = imw_fetch_array($sel_chlist)){
			$sup_exclude_arr[$row_chlist['idSuperBill']]=$row_chlist['idSuperBill'];
		}
	}
	
	$sel_dx_qry = "	SELECT pi.idSuperBill,sb.patientId, sb.dateOfService, pi.dx1, pi.dx2, pi.dx3, pi.dx4, GROUP_CONCAT(pi.cptCode) AS cptCode
					FROM superbill as sb, procedureinfo as pi 
					WHERE pi.idSuperBill = sb.idSuperBill 
					AND sb.postedStatus='0'
					AND pi.delete_status='0'
					";	
					
					$andOR="";
					if($dx_codes != "" || $dx_codes10 != ""){
						$sel_dx_qry.=" AND (";
						if($dx_codes != ""){
							$sel_dx_qry .= "
							(pi.dx1 ".$dx_condition1." (".$dx_codes.") 
								".$dx_condition2." pi.dx2 ".$dx_condition1." (".$dx_codes.") 
								".$dx_condition2." pi.dx3 ".$dx_condition1." (".$dx_codes.") 
								".$dx_condition2." pi.dx4 ".$dx_condition1." (".$dx_codes."))";
							$andOR=" OR ";	
						}
						if($dx_codes10 != ""){
							$sel_dx_qry .=$andOR." 
							(pi.dx1 ".$dx_condition1." (".$dx_codes10.") 
								".$dx_condition2." pi.dx2 ".$dx_condition1." (".$dx_codes10.") 
								".$dx_condition2." pi.dx3 ".$dx_condition1." (".$dx_codes10.") 
								".$dx_condition2." pi.dx4 ".$dx_condition1." (".$dx_codes10."))";
						}		
						$sel_dx_qry .=")";
					}
					if($procCodes != "") {
						$sel_dx_qry.=" AND (pi.cptCode ".$dx_condition3." (".$procCodes.")) ";	
					}
					
	if($dtEffectiveDate != ""){
		$sel_dx_qry .= " AND (sb.dateOfService BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
	}
	if($enTimeFrom!='' || $enTimeTo!=''){
		$sel_dx_qry .= " AND (sb.timeSuperBill BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
	}
					$sel_dx_qry .= "GROUP BY sb.patientId 
									ORDER BY sb.dateOfService DESC";
	//echo '<br>'.$sel_dx_qry.'<br><br>';
	$sel_dx = imw_query($sel_dx_qry);
	while($row_dx = imw_fetch_array($sel_dx)){
		if($sup_exclude_arr[$row_dx['idSuperBill']]<=0){
			$dx_code_patient=true;
			$arr_pt_ids[$row_dx['patientId']]=$row_dx['patientId'];
			$pt_dx_codes[$i]["id"] = $row_dx['patientId'];
			$pt_dx_codes[$i]["dos"] = $row_dx['dateOfService'];
			$tmp_dx = "";
			if($_REQUEST["rd_problem_list"] == "1"){
				if($row_dx['dx1'] != "" && ($arr_dx_codes[$row_dx['dx1']] || $arr_dx_codes10[$row_dx['dx1']])){
					$tmp_dx .= $row_dx['dx1'].", ";
				}
				if($row_dx['dx2'] != "" && ($arr_dx_codes[$row_dx['dx2']] || $arr_dx_codes10[$row_dx['dx2']])){
					$tmp_dx .= $row_dx['dx2'].", ";
				}
				if($row_dx['dx3'] != "" && ($arr_dx_codes[$row_dx['dx3']] || $arr_dx_codes10[$row_dx['dx3']])){
					$tmp_dx .= $row_dx['dx3'].", ";
				}
				if($row_dx['dx4'] != "" && ($arr_dx_codes[$row_dx['dx4']] || $arr_dx_codes10[$row_dx['dx4']])){
					$tmp_dx .= $row_dx['dx4'].", ";
				}
			}else if($_REQUEST["rd_problem_list"] == "0"){
				if($row_dx['dx1'] != "" && !($arr_dx_codes[$row_dx['dx1']] && $arr_dx_codes10[$row_dx['dx1']])){
					$tmp_dx .= $row_dx['dx1'].", ";
				}
				if($row_dx['dx2'] != "" && !($arr_dx_codes[$row_dx['dx2']] && $arr_dx_codes10[$row_dx['dx2']])){
					$tmp_dx .= $row_dx['dx2'].", ";
				}
				if($row_dx['dx3'] != "" && !($arr_dx_codes[$row_dx['dx3']] && $arr_dx_codes10[$row_dx['dx3']])){
					$tmp_dx .= $row_dx['dx3'].", ";
				}
				if($row_dx['dx4'] != "" && !($arr_dx_codes[$row_dx['dx4']] && $arr_dx_codes10[$row_dx['dx4']])){
					$tmp_dx .= $row_dx['dx4'].", ";
				}
			}
			$tmp_dx = substr($tmp_dx, 0 , -2);
			//$patient_id[$i]["clinical_act"] = $dx_master_val[$tmp_dx]." - ".$tmp_dx;
			$pt_dx_codes[$i]["clinical_act"] = $tmp_dx;
			$dbCptCodeArr 	= array_unique(explode(",",$row_dx['cptCode']));
			$dbCptCode = implode(",",$dbCptCodeArr);
			if($_REQUEST["rd_cpt_list"] == "1"){
				if($dbCptCode && array_intersect($dbCptCodeArr,$arr_cpt)){
					$pt_dx_codes[$i]["clinical_act_cpt"] = $dbCptCode;	
				}
			}else if($_REQUEST["rd_cpt_list"] == "0"){
				if($dbCptCode && !array_intersect($dbCptCodeArr,$arr_cpt)){
					$pt_dx_codes[$i]["clinical_act_cpt"] = $dbCptCode;	
				}
			}
	
			$i++;
		}
	}
	
	$chl_exclude_arr=array();
	if($dx_condition3=="NOT IN" && trim($proc_codes_id_imp) != ""){
		$sel_cpt_qry = "SELECT pcld.charge_list_id FROM patient_charge_list_details pcld, patient_charge_list pcl 
					WHERE pcld.charge_list_id = pcl.charge_list_id AND newBalance > 0 ";					
		if(trim($proc_codes_id_imp) != "") {
			$sel_cpt_qry.=" AND (pcld.procCode in (".$proc_codes_id_imp.")) ";	
		}
		if($dtEffectiveDate != ""){
			$sel_cpt_qry .= " AND (pcl.entered_date BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
		}
		if($enTimeFrom != "" || $enTimeTo!=""){
			$sel_cpt_qry .= " AND (pcl.entered_time BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
		}
		$sel_cpt_qry .= "GROUP BY pcld.charge_list_id";
		$sel_chlist=imw_query($sel_cpt_qry);
		while($row_chlist = imw_fetch_array($sel_chlist)){
			$chl_exclude_arr[$row_chlist['charge_list_id']]=$row_chlist['charge_list_id'];
		}
	}
	$sel_cpt_qry = "SELECT pcld.charge_list_id,pcld.patient_id, pcl.date_of_service, pcld.diagnosis_id1, pcld.diagnosis_id2, pcld.diagnosis_id3, pcld.diagnosis_id4, GROUP_CONCAT(pcld.procCode) AS procCode 
					FROM patient_charge_list_details pcld, patient_charge_list pcl 
					WHERE pcld.charge_list_id = pcl.charge_list_id 
					AND newBalance > 0 ";					
					$andOR="";
					if($dx_codes != "" || $dx_codes10 != ""){
						$sel_cpt_qry.=" AND (";
						if($dx_codes != ""){
							$sel_cpt_qry .= "
							 (diagnosis_id1 ".$dx_condition1." (".$dx_codes.") 
							  ".$dx_condition2." diagnosis_id2 ".$dx_condition1." (".$dx_codes.") 
							  ".$dx_condition2." diagnosis_id3 ".$dx_condition1." (".$dx_codes.") 
							  ".$dx_condition2." diagnosis_id4 ".$dx_condition1." (".$dx_codes."))";
							  $andOR=" OR ";
						}
						if($dx_codes10 != ""){
							$sel_cpt_qry .= $andOR."
							 (diagnosis_id1 ".$dx_condition1." (".$dx_codes10.") 
							  ".$dx_condition2." diagnosis_id2 ".$dx_condition1." (".$dx_codes10.") 
							  ".$dx_condition2." diagnosis_id3 ".$dx_condition1." (".$dx_codes10.") 
							  ".$dx_condition2." diagnosis_id4 ".$dx_condition1." (".$dx_codes10."))";
						}					
						$sel_cpt_qry.=")";
					}
					if(trim($proc_codes_id_imp) != "") {
						$sel_cpt_qry.=" AND (pcld.procCode ".$dx_condition3." (".$proc_codes_id_imp.")) ";	
					}
	if($dtEffectiveDate != ""){
		//$sel_cpt_qry .= " AND (pcl.date_of_service BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
		$sel_cpt_qry .= " AND (pcl.entered_date BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
	}
	if($enTimeFrom != "" || $enTimeTo!=""){
		$sel_cpt_qry .= " AND (pcl.entered_time BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
	}
	$sel_cpt_qry .= "GROUP BY pcld.patient_id 
					ORDER BY pcl.date_of_service DESC";
	//echo '<br><br>'.$sel_cpt_qry.'<br><br>';
	$sel_chlist=imw_query($sel_cpt_qry);
	while($row_chlist = imw_fetch_array($sel_chlist)){
		if($chl_exclude_arr[$row_chlist['charge_list_id']]<=0){
			$dx_code_patient=true;
			$arr_pt_ids[$row_chlist['patient_id']]=$row_chlist['patient_id'];
			$pt_dx_codes[$i]["id"] = $row_chlist['patient_id'];
			$pt_dx_codes[$i]["dos"] = $row_chlist['date_of_service'];
			$tmp_dx = "";
			if($_REQUEST["rd_problem_list"] == "1"){
				if($row_chlist['diagnosis_id1'] != "" && ($arr_dx_codes[$row_chlist['diagnosis_id1']] || $arr_dx_codes10[$row_chlist['diagnosis_id1']])){
					$tmp_dx .= $row_chlist['diagnosis_id1'].", ";
				}
				if($row_chlist['diagnosis_id2'] != "" && ($arr_dx_codes[$row_chlist['diagnosis_id2']] || $arr_dx_codes10[$row_chlist['diagnosis_id2']])){
					$tmp_dx .= $row_chlist['diagnosis_id2'].", ";
				}
				if($row_chlist['diagnosis_id3'] != "" && ($arr_dx_codes[$row_chlist['diagnosis_id3']] || $arr_dx_codes10[$row_chlist['diagnosis_id3']])){
					$tmp_dx .= $row_chlist['diagnosis_id3'].", ";
				}
				if($row_chlist['diagnosis_id4'] != "" && ($arr_dx_codes[$row_chlist['diagnosis_id4']] || $arr_dx_codes10[$row_chlist['diagnosis_id4']])){
					$tmp_dx .= $row_chlist['diagnosis_id4'].", ";
				}
			}else if($_REQUEST["rd_problem_list"] == "0"){
				if($row_chlist['diagnosis_id1'] != "" && !($arr_dx_codes[$row_chlist['diagnosis_id1']] || $arr_dx_codes10[$row_chlist['diagnosis_id1']])){
					$tmp_dx .= $row_chlist['diagnosis_id1'].", ";
				}
				if($row_chlist['diagnosis_id2'] != "" && !($arr_dx_codes[$row_chlist['diagnosis_id2']] || $arr_dx_codes10[$row_chlist['diagnosis_id2']])){
					$tmp_dx .= $row_chlist['diagnosis_id2'].", ";
				}
				if($row_chlist['diagnosis_id3'] != "" && !($arr_dx_codes[$row_chlist['diagnosis_id3']] || $arr_dx_codes10[$row_chlist['diagnosis_id3']])){
					$tmp_dx .= $row_chlist['diagnosis_id3'].", ";
				}
				if($row_chlist['diagnosis_id4'] != "" && !($arr_dx_codes[$row_chlist['diagnosis_id4']] || $arr_dx_codes10[$row_chlist['diagnosis_id4']])){
					$tmp_dx .= $row_chlist['diagnosis_id4'].", ";
				}
			}
			$tmp_dx = substr($tmp_dx, 0 , -2);
			//$patient_id[$i]["clinical_act"] = $dx_master_val[$tmp_dx]." - ".$tmp_dx;
			$pt_dx_codes[$i]["clinical_act"] = $tmp_dx;
			$dbCptCodeIdArr=explode(",",$row_chlist['procCode']);
			$dbCptCodeArr = array();
			foreach($dbCptCodeIdArr as $dbCptCodeId) {
				$dbCptCodeArr[] = $cptDetailsArr[$dbCptCodeId];		
			}
			$dbCptCodeArr = array_unique($dbCptCodeArr);
			$dbCptCode = implode(",",$dbCptCodeArr);
			if($_REQUEST["rd_cpt_list"] == "1"){
				if($row_chlist['procCode'] && array_intersect($dbCptCodeArr,$arr_cpt)){
					$pt_dx_codes[$i]["clinical_act_cpt"] = $dbCptCode;	
				}
			}else if($_REQUEST["rd_cpt_list"] == "0"){
				if($row_chlist['procCode'] && !array_intersect($dbCptCodeArr,$arr_cpt)){
					$pt_dx_codes[$i]["clinical_act_cpt"] = $dbCptCode;
				}
			}
			$i++;
		}
	}
	
	//pt  problem list search
	$pt_id_dx=array();

	if(count($dx9And10_master_val) > 0 ){$j=0;
		if($dx_condition2=="AND"){ 
			foreach($dx9And10_master_val as $this_dx_code => $this_dx_desc){
				$qryDxCodes="SELECT pt_id FROM pt_problem_list where status='Active' AND problem_name like '%".trim($this_dx_code)."%'";
				$resDxCodes=imw_query($qryDxCodes);
				while($rowDxCodes=imw_fetch_assoc($resDxCodes)){
					if($rowDxCodes['pt_id']){
						$arr_pt_ids[$rowDxCodes['pt_id']]=$rowDxCodes['pt_id'];
						$pt_id_dx[$rowDxCodes['pt_id']]=$rowDxCodes['pt_id'];	
					}
				}
			}
		}
	}

	$pt_ids_dx="";
	if(count($dx9And10_master_val) > 0){
		if(count($pt_id_dx)>0){
			$ptIdsDdx=implode(",",$pt_id_dx);
		}
		foreach($dx9And10_master_val as $this_dx_code => $this_dx_desc){
			$likeQ=" LIKE ";
			if($dx_condition2=="AND"){ 
				$likeQ=" NOT LIKE ";
			}
			//$qryPt_id="  problem_name ".$likeQ." '%- ".$this_dx_code."%'";
			$qryPt_id="  problem_name ".$likeQ." '%".trim($dx9And10_master_val[$this_dx_code])."%'";
			if($dx_condition2=="AND" && count($pt_id_dx)>0){ $qryPt_id=" pt_id NOT IN(".$ptIdsDdx.")";}
			$pl_qry = "SELECT DISTINCT(pt_id), onset_date, timestamp FROM pt_problem_list where status='Active' AND ".$qryPt_id;
            $pl_res = imw_query($pl_qry);
			if(imw_num_rows($pl_res) > 0){
                while($this_pl = imw_fetch_array($pl_res)) {
                    if(is_array($this_pl) && count($this_pl) > 0){
                        $dx_code_patient=true;
                        //foreach($pl_arr as $this_pl){
                            $added=0;
                            $dt=explode(' ',$this_pl["timestamp"]);
                            if($dtEffectiveDate != ""){
                                if($dtDBEffectDate<=$dt[0] && $dtDBEffectDate2>=$dt[0]){
                                    if($enTimeFrom!='' || $enTimeTo!=''){
                                        if($enTimeFrom<=$dt[1] && $enTimeTo>=$dt[1]){
                                            $arr_pt_ids[$this_pl["pt_id"]]=$this_pl["pt_id"];
                                            $pt_dx_codes[$i]["id"] = $this_pl["pt_id"];
                                            $pt_dx_codes[$i]["dos"] = $this_pl["onset_date"];
                                            $pt_dx_codes[$i]["clinical_act"] = $this_dx_code;
                                            $i++;
                                            $added=1;
                                        }else{ $added='-1'; }
                                    }else{
                                        $arr_pt_ids[$this_pl["pt_id"]]=$this_pl["pt_id"];
                                        $pt_dx_codes[$i]["id"] = $this_pl["pt_id"];
                                        $pt_dx_codes[$i]["dos"] = $this_pl["onset_date"];
                                        $pt_dx_codes[$i]["clinical_act"] = $this_dx_code;
                                        $i++;
                                        $added=1;
                                    }
                                }else{ $added='-1'; }
                            }else{
                                if($added!='-1'){
                                    if($enTimeFrom!='' || $enTimeTo!='' && $added=='0'){
                                        if($enTimeFrom<=$dt[1] && $enTimeTo>=$dt[1]){
                                            $arr_pt_ids[$this_pl["pt_id"]]=$this_pl["pt_id"];
                                            $pt_dx_codes[$i]["id"] = $this_pl["pt_id"];
                                            $pt_dx_codes[$i]["dos"] = $this_pl["onset_date"];
                                            $pt_dx_codes[$i]["clinical_act"] = $this_dx_code;
                                            $i++;
                                        }
                                    }else{
                                        if($added=='0'){
                                            $arr_pt_ids[$this_pl["pt_id"]]=$this_pl["pt_id"];
                                            $pt_dx_codes[$i]["id"] = $this_pl["pt_id"];
                                            $pt_dx_codes[$i]["dos"] = $this_pl["onset_date"];
                                            $pt_dx_codes[$i]["clinical_act"] = $this_dx_code;
                                            $i++;
                                        }
                                    }
                                }
                            }
                        //}
                    }
                }
			}
		}
		if($dx_condition2=="AND"){
			$pt_ids_in_patient=implode(",",array_unique($arr_pt_ids));
			$qryConcatIds="";
			if(count($pt_ids_in_patient)>0){
				$qryConcatIds=" AND id not in(".$pt_ids_in_patient.")";	
			}
			$qryPatNoProblem="SELECT id from patient_data where 1=1 ".$qryConcatIds;//which patients has no problem list
			$resPatNoProblem=imw_query($qryPatNoProblem);
			if(imw_num_rows($resPatNoProblem)>0){
				while($rowPatNoProblem=imw_fetch_assoc($resPatNoProblem)){
						$arr_pt_ids[$rowPatNoProblem["id"]]=$rowPatNoProblem["id"];
						$pt_dx_codes[]["id"] = $rowPatNoProblem["id"];
				}	
			}
		}
	}
	//$pt_dx_codes = array_unique($pt_dx_codes);
}

//print "<pre>";
//print_r(array_unique($pt_dx_codes));
//die("problem list");

//medications
//if($dx_code_patient==true){echo "t";}
$effectedDate= $effectedTime = 0;
$pt_medications = array();$med_patients=true;
if(trim($_REQUEST["medications"]) != ""){$med_patients=false;
	
	$included_excluded .= ($_REQUEST["rd_med_list"] == "1") ? "Med.: Included " : "Med.: Excluded ";
	
	$str_medication_list = $_REQUEST["medications"];
	
	$str_selection_criteria .= (isset($_REQUEST["medications"]) && trim($_REQUEST["medications"]) != "") ? "Med.: ".$_REQUEST["medications"]." " : "";

	$arr_medication_list = explode(",",$str_medication_list);
	$strMedicines="'".implode("','", $arr_medication_list)."'";
		
	//GETTING LOT#
	$qry="Select med.medicine_name, med.opt_med_upc, med.opt_med_id, DATE_FORMAT(in_item.expiry_date, '".$dateFormat."') as 'expiry_date' 
	FROM medicine_data med LEFT JOIN in_item ON in_item.id=med.opt_med_id WHERE med.medicine_name IN(".$strMedicines.")";
    if(isset($_REQUEST['lot_number']) && $_REQUEST['lot_number'] != '') {
        $lot_number = $_REQUEST['lot_number'];
        $qry .= " AND opt_med_upc='".$lot_number."' ";
    }
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		if($res['opt_med_id']>0){
			$arrMedicineInfo[$res['medicine_name']]['LotNo']= $res['opt_med_upc'];
		}
		if($res['expiry_date']!=null && $res['expiry_date']!='0000-00-00'){
			$arrMedicineInfo[$res['medicine_name']]['expiry_date']=$res['expiry_date'];
		}
	}unset($rs);

    
	
	$str_medication_list = "";
	
	if(is_array($arr_medication_list) && count($arr_medication_list) > 0){
		$k=0;
		$OrVal='';
		foreach($arr_medication_list as $med_val){
			if($k>0){ $OrVal=' OR '; }
			$str_medication_list_like .= $OrVal."title LIKE '%".trim(stripcslashes($med_val))."%'";
			$str_medication_list .= "'".trim(stripcslashes($med_val))."',";
			$k++;
		}
		$str_medication_list = substr($str_medication_list, 0, -1);
	}
	$arr_med_patients=array();
	$qryMedCond="";
	if($_REQUEST["rd_med_list"]!=1){
		$sel_med_qry_pat = "select pid, date_format(date, '%Y-%m-%d') as med_date, title, date_format(enddate, '%Y-%m-%d') as enddate 
							from lists 
							where (type = 1 or type = 4) and allergy_status='Active'
							and ".$str_medication_list;
		$res_med_qry_pat=imw_query($sel_med_qry_pat);
		while($row_med_qry_pat=imw_fetch_assoc($res_med_qry_pat)){
			$arr_med_patients[]=$row_med_qry_pat['pid'];
		}
		$ptIds=implode(",",$arr_med_patients);
		if(count($arr_med_patients)>0){
			$qryMedCond=" AND pid NOT IN (".$ptIds.")";
		}
	}
	$qryAndCond="";
	if(count($arr_pt_ids)>0){
		$arr_ptIds_dx=implode(",",$arr_pt_ids);
		$qryAndCond=" AND pid IN (".$arr_ptIds_dx.")";
	}
	
	if($_REQUEST["rd_med_list"] == "1"){
		 $med_condition ="IN";
		 $search_titles=$str_medication_list_like;
	}else{
		 $med_condition ="NOT IN";
		 $search_titles=" title NOT IN (".$str_medication_list.")";
	}
    
	if($str_medication_list != ""){
		$sel_med_qry = "select pid, date_format(date, '%Y-%m-%d') as med_date, date_format(date, '%H:%m:%i') as med_time, title,
						date_format(enddate, '%Y-%m-%d') as enddate
						from lists 
						where (type = 1 or type = 4) and allergy_status='Active' 
						and (".$search_titles.")  ".$qryMedCond.$qryAndCond;
						
		if($dtEffectiveDate != ""){
			$sel_med_qry .= " AND (date_format(date, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
			$effectedDate=1;
		}
		if($enTimeFrom != "" || $enTimeTo != ""){
			$sel_med_qry .= " AND (date_format(date, '%H:%m:%i') BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
			$effectedTime=1;
		}

		$sel_med_qry .= "	GROUP BY pid 
							ORDER BY med_date DESC";
		//echo $sel_med_qry;
		$sel_med=imw_query($sel_med_qry);
		if(imw_num_rows($sel_med)>0){$med_patients=true;//$arr_pt_ids=array();
			while($row_med=imw_fetch_array($sel_med)){
                //if(($arrMedicineInfo[$row_med['title']]['LotNo']=='' || $chmlarrMedicineInfo[$row_med["pid"]][$row_med['title']]['LotNo']== '') && $_REQUEST['lot_number'] != ''){
                if(($arrMedicineInfo[$row_med['title']]['LotNo']=='') && $_REQUEST['lot_number'] != ''){
                    continue;
                }
				$medInfo='';
				if($arrMedicineInfo[$row_med['title']]['LotNo']!=''){
					$medInfo=' Lot#'.$arrMedicineInfo[$row_med['title']]['LotNo'];
				}
//                if(in_array($row_med["pid"],$cpml_patient_id) && $chmlarrMedicineInfo[$row_med["pid"]][$row_med['title']]['LotNo'] != '') {
//                    $medInfo=' Lot#'.$chmlarrMedicineInfo[$row_med["pid"]][$row_med['title']]['LotNo'];
//                }
				if($arrMedicineInfo[$row_med['title']]['expiry_date']!=''){
					$medInfo.=' Exp. Date:'.$arrMedicineInfo[$row_med['title']]['expiry_date'];
				}
				$added=0;
				if($effectedDate=='1'){
					if($dtDBEffectDate<=$row_med['med_date'] && $dtDBEffectDate2>=$row_med['med_date']){
						if($effectedTime=='1'){
							if($enTimeFrom<=$row_med['med_time'] && $enTimeTo>=$row_med['med_time']){
								$arr_pt_ids[$row_med["pid"]]=$row_med["pid"];
								$pt_medications[$i]["id"] = $row_med['pid'];
								$pt_medications[$i]["dos"] = $row_med['med_date'];
								$pt_medications[$i]["clinical_act"] = $row_med['title'].$medInfo;
								$i++;
							}
						}else{
							$arr_pt_ids[$row_med["pid"]]=$row_med["pid"];
							$pt_medications[$i]["id"] = $row_med['pid'];
							$pt_medications[$i]["dos"] = $row_med['med_date'];
							$pt_medications[$i]["clinical_act"] = $row_med['title'].$medInfo;
							$i++;
						}
					}
				}else{
					if($effectedTime=='1'){
						if($enTimeFrom<=$row_med['med_time'] && $enTimeTo>=$row_med['med_time']){
							$arr_pt_ids[$row_med["pid"]]=$row_med["pid"];
							$pt_medications[$i]["id"] = $row_med['pid'];
							$pt_medications[$i]["dos"] = $row_med['med_date'];
							$pt_medications[$i]["clinical_act"] = $row_med['title'].$medInfo;
							$i++;
						}
					}else{
						$arr_pt_ids[$row_med["pid"]]=$row_med["pid"];
						$pt_medications[$i]["id"] = $row_med['pid'];
						$pt_medications[$i]["dos"] = $row_med['med_date'];
						$pt_medications[$i]["clinical_act"] = $row_med['title'].$medInfo;
						$i++;
					}
				}
			}
		}
		if($_REQUEST["rd_med_list"]!=1){
			$pt_ids_in_patient=implode(",",array_unique($arr_pt_ids));
			$qryConcateMed="";
			if(count($arr_pt_ids)>0){
				$qryConcateMed=" AND id not in(".$pt_ids_in_patient.")";	
			}
			$qryPatNoProblem="SELECT id from patient_data where 1=1  ".$qryConcateMed.$qryMedCond;//which patients has no problem list
			$resPatNoProblem=imw_query($qryPatNoProblem);
			if(imw_num_rows($resPatNoProblem)>0){
				while($rowPatNoProblem=imw_fetch_assoc($resPatNoProblem)){
					$arr_pt_ids[$rowPatNoProblem["id"]]=$rowPatNoProblem["id"];
					$pt_medications[]["id"] = $rowPatNoProblem["id"];
				}	
			}
		}
        
        $chmlarrMedicineInfo = array();
        $cpml_patient_id = array();
        if($str_medication_list != ""){
            $sel_cpml_qry = "SELECT chml.med_name,chml.chart_procedure_id,chml.lot_number,chrt_proc.id,chrt_proc.patient_id,chrt_proc.exam_date
                    FROM chart_procedures_med_lot chml
                    LEFT JOIN chart_procedures chrt_proc ON (chrt_proc.id = chml.chart_procedure_id)
                    WHERE chml.lot_number!='' ";
            if(isset($_REQUEST['lot_number']) && $_REQUEST['lot_number'] != '') {
                $lot_number = $_REQUEST['lot_number'];
                $sel_cpml_qry .= " AND chml.lot_number='".$lot_number."' ";
            }
            if($dtEffectiveDate != ""){
                $sel_cpml_qry .= " AND (date_format(chrt_proc.exam_date, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
            }
            if($enTimeFrom != "" || $enTimeTo != ""){
                $sel_cpml_qry .= " AND (date_format(chrt_proc.exam_date, '%H:%m:%i') BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
            }
            $sel_med_qry .= "	GROUP BY chrt_proc.patient_id 
							ORDER BY chrt_proc.exam_date DESC";
            
            $cpml_rs=imw_query($sel_cpml_qry);
            while($cpml_res=imw_fetch_assoc($cpml_rs)){$med_patients=true;
                    $cpml_patient_id[] = $cpml_res['patient_id'];
                    $chmlarrMedicineInfo[$cpml_res['patient_id']][$cpml_res['med_name']]['LotNo']= $cpml_res['lot_number'];

                    $medInfo='';
                    if(!empty($chmlarrMedicineInfo) && $chmlarrMedicineInfo[$cpml_res['patient_id']][$cpml_res['med_name']]['LotNo'] != '') {
                        $medInfo=' Lot#'.$chmlarrMedicineInfo[$cpml_res['patient_id']][$cpml_res['med_name']]['LotNo'];
                    }
                    $arr_pt_ids[$cpml_res['patient_id']]=$cpml_res['patient_id'];
                    $pt_medications[$i]["id"] = $cpml_res['patient_id'];
                    $pt_medications[$i]["dos"] = $cpml_res['exam_date'];
                    $pt_medications[$i]["clinical_act"] = $cpml_res['med_name'].$medInfo;
                    $i++;
            }
        }        
	}
}
$cpxarrMedicineInfo = array();
if($str_medication_list == "" && isset($_REQUEST['lot_number']) && $_REQUEST['lot_number'] != ''){
	$sel_cpx_qry = "SELECT cpx.chart_proc_id,cpx.lot,chrt_proc.id,chrt_proc.patient_id,chrt_proc.exam_date
			FROM chart_procedures_botox cpx
			LEFT JOIN chart_procedures chrt_proc ON (chrt_proc.id = cpx.chart_proc_id)
			WHERE cpx.lot!='' ";
	if(isset($_REQUEST['lot_number']) && $_REQUEST['lot_number'] != '') {
		$lot_number = $_REQUEST['lot_number'];
		$sel_cpx_qry .= " AND cpx.lot='".$lot_number."' ";
	}
	if($dtEffectiveDate != ""){
		$sel_cpx_qry .= " AND (date_format(chrt_proc.exam_date, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
	}
	if($enTimeFrom != "" || $enTimeTo != ""){
		$sel_cpx_qry .= " AND (date_format(chrt_proc.exam_date, '%H:%m:%i') BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
	}
    $sel_cpx_qry .= "	GROUP BY chrt_proc.patient_id ORDER BY chrt_proc.exam_date DESC";

	$cpx_rs=imw_query($sel_cpx_qry);
	while($cpx_res=imw_fetch_assoc($cpx_rs)){$med_patients=true;
			$cpx_patient_id[] = $cpx_res['patient_id'];
			$cpxarrMedicineInfo[$cpx_res['patient_id']]['LotNo']= $cpx_res['lot'];

			$medInfo='';
			if(!empty($cpxarrMedicineInfo) && $cpxarrMedicineInfo[$cpx_res['patient_id']]['LotNo'] != '') {
				$medInfo=' Lot# '.$cpxarrMedicineInfo[$cpx_res['patient_id']]['LotNo'];
			}
			$arr_pt_ids[$cpx_res['patient_id']]=$cpx_res['patient_id'];
			$pt_medications[$i]["id"] = $cpx_res['patient_id'];
			$pt_medications[$i]["dos"] = $cpx_res['exam_date'];
			$pt_medications[$i]["clinical_act"] = 'Botox'.$medInfo;
			$i++;
	}
}



//print "<pre>";
//print_r($pt_medications);
//die("medications");

//medication allergy
$effectedDate= $effectedTime = 0;
$pt_medications_allergy = array();$allergy_pat=true;
if(trim($_REQUEST["medication_allergy"]) != ""){$allergy_pat=false;
	$included_excluded .= ($_REQUEST["rd_med_allergy_list"] == "1") ? "Allergy.: Included " : "Allergy.: Excluded ";
	
	$str_selection_criteria .= (isset($_REQUEST["medication_allergy"]) && trim($_REQUEST["medication_allergy"]) != "") ? "Allergy.: ".$_REQUEST["medication_allergy"]." " : "";

	$arr_medication_allergy = explode(",",$_REQUEST["medication_allergy"]);
	
	$str_medication_allergy = "";
	if(is_array($arr_medication_allergy) && count($arr_medication_allergy) > 0){
		$OrVal=''; $k=0;
		foreach($arr_medication_allergy as $med_val){
			if($k>0){ $OrVal=' OR ';}
			$str_medication_allergy_like .= $OrVal."title LIKE '%".trim(stripcslashes($med_val))."%'";
			$str_medication_allergy .= "'".trim(stripcslashes($med_val))."',";
		}
		$str_medication_allergy = substr($str_medication_allergy, 0, -1);
	}
	$qryAllCond="";
	if($_REQUEST["rd_med_allergy_list"]!=1){
		$sel_all_qry_pat = "select DISTINCT(pid), date_format(date, '%Y-%m-%d') as med_date, date_format(date, '%H:%m:%i') as med_time, title 
						from lists 
						where (type = 3 or type = 7) and allergy_status='Active' 
						and title IN (".$str_medication_allergy.")    ";
		$res_all_qry_pat=imw_query($sel_all_qry_pat);
		while($row_all_qry_pat=imw_fetch_assoc($res_all_qry_pat)){
			$arr_all_patients[$row_all_qry_pat['pid']]=$row_all_qry_pat['pid'];
		}
		$ptAllIds=implode(",",$arr_all_patients);
		if(count($arr_all_patients)>0){
			$qryAllCond=" AND pid NOT IN (".$ptAllIds.")";
		}
	}
	$qryAndCond="";
	if(count($arr_pt_ids)>0){
		$arr_ptIds_dx=implode(",",$arr_pt_ids);
		$qryAndCond=" AND pid IN (".$arr_ptIds_dx.")";
	}

	if($_REQUEST["rd_med_list"] == "1"){
		 $med_condition ="IN";
		 $search_titles=$str_medication_allergy_like;
	}else{
		 $med_condition ="NOT IN";
		 $search_titles=" title NOT IN (".$str_medication_allergy.")";
	}
	
	if($str_medication_allergy != ""){
		$sel_med_qry = "select pid, date_format(date, '%Y-%m-%d') as med_date, date_format(date, '%H:%m:%i') as med_time, title 
						from lists 
						where (type = 3 or type = 7) and allergy_status='Active' 
						and ".$search_titles." ".$qryAllCond.$qryAndCond;
						
		if($dtEffectiveDate != ""){
			$sel_med_qry .= " AND (date_format(date, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
			$effectedDate=1;
		}
		if($enTimeFrom != "" || $enTimeTo != ""){
			$sel_med_qry .= " AND (date_format(date, '%H:%m:%i') BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
			$effectedTime=1;
		}
		
		$sel_med_qry .= "	GROUP BY pid 
							ORDER BY med_date DESC";
		//echo $sel_med_qry;
		$sel_med=imw_query($sel_med_qry);
		if(imw_num_rows($sel_med)>0){$allergy_pat=true;//$arr_pt_ids=array();
			while($row_med=imw_fetch_array($sel_med)){
				$added=0;
				if($effectedDate=='1'){
					if($dtDBEffectDate<=$row_med['med_date'] && $dtDBEffectDate2>=$row_med['med_date']){
						if($effectedTime=='1'){
							if($enTimeFrom<=$row_med['med_time'] && $enTimeTo>=$row_med['med_time']){
								$pt_medications_allergy[$i]["id"] = $row_med['pid'];
								$pt_medications_allergy[$i]["dos"] = $row_med['med_date'];
								$pt_medications_allergy[$i]["clinical_act"] = $row_med['title'];
								$arr_pt_ids[$row_med['pid']]=$row_med['pid'];
								$i++;
							}
						}else{
							$pt_medications_allergy[$i]["id"] = $row_med['pid'];
							$pt_medications_allergy[$i]["dos"] = $row_med['med_date'];
							$pt_medications_allergy[$i]["clinical_act"] = $row_med['title'];
							$arr_pt_ids[$row_med['pid']]=$row_med['pid'];
							$i++;
						}
					}
				}else{
					if($effectedTime=='1'){
						if($enTimeFrom<=$row_med['med_time'] && $enTimeTo>=$row_med['med_time']){
							$pt_medications_allergy[$i]["id"] = $row_med['pid'];
							$pt_medications_allergy[$i]["dos"] = $row_med['med_date'];
							$pt_medications_allergy[$i]["clinical_act"] = $row_med['title'];
							$arr_pt_ids[$row_med['pid']]=$row_med['pid'];
							$i++;
						}
					}else{
						$pt_medications_allergy[$i]["id"] = $row_med['pid'];
						$pt_medications_allergy[$i]["dos"] = $row_med['med_date'];
						$pt_medications_allergy[$i]["clinical_act"] = $row_med['title'];
						$arr_pt_ids[$row_med['pid']]=$row_med['pid'];
						$i++;
					}
				}
			}
		}
		if($_REQUEST["rd_med_allergy_list"]!=1){
			$pt_ids_in_patient=implode(",",$arr_pt_ids);
			$qryPatNoProblem="SELECT DISTINCT(id) from patient_data where id not in(".$pt_ids_in_patient.") ".$qryAllCond;//which patients has no problem list
			$resPatNoProblem=imw_query($qryPatNoProblem);
			if(imw_num_rows($resPatNoProblem)>0){
				while($rowPatNoProblem=imw_fetch_assoc($resPatNoProblem)){
						$arr_pt_ids[$rowPatNoProblem["id"]]=$rowPatNoProblem["id"];
						$pt_medications_allergy[]["id"] = $rowPatNoProblem["id"];
				}	
			}
		}		
	}
}

//print_r(array_unique($arr_pt_ids));
//die('Lab='.$_REQUEST["rd_lab_results"]);
if(trim($_REQUEST["tests"]) != "" || trim($_REQUEST["cd_ratio"]) != "" || trim($_REQUEST["iopPressure"]) != ""){
	$included_excluded .= ($_REQUEST["rd_lab_results"] == "1") ? "Test: Included " : "Test: Excluded ";	
}


//Laboratory
$pt_tests = array();
if($_REQUEST["labVals"]!=''){
	
	$str_selection_criteria .= (isset($_REQUEST["labLabel"]) && trim($_REQUEST["labLabel"]) != "") ? "Laboratory: ".$_REQUEST["labLabel"] : "";
	
	$labQry="Select lab_obs.observation, lab_obs.result, lab_test.lab_patient_id as 'pid', lab_obs.result_entered_date as 'dos'  FROM lab_observation_result lab_obs 
	LEFT JOIN lab_test_data lab_test ON lab_test.lab_test_data_id = lab_obs.lab_test_id 
	WHERE (".$_REQUEST["labVals"].")";
	if($dtEffectiveDate != ""){
		$labQry .= " AND (lab_obs.result_entered_date BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
	}
	if($enTimeFrom != "" || $enTimeTo != ""){
		$labQry .= " AND (result_time BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
	}
	
	$labRs=imw_query($labQry);
	while($labRes=imw_fetch_array($labRs)){
		$pt_tests[$i]["id"] = $labRes['pid'];
		$pt_tests[$i]["dos"] = $labRes['dos'];
		$pt_tests[$i]["clinical_act"] = $labRes['observation'];
		$i++;
	}
}

//CD Ratio
$pt_cd_ratio = array();	$ptCDRatio=true;
//print_r($arr_pt_ids);
if(trim($_REQUEST["cd_ratio"]) != ""){$ptCDRatio=false;
	$str_cd_ratio = trim($_REQUEST["cd_ratio"]);
	$qryCdRatio="";
	if(count($arr_pt_ids)>0){
		
		$arr_SrchPt_ids=implode(",",$arr_pt_ids);
		$qryCdRatio=" AND  patient_id IN (".$arr_SrchPt_ids.")";
	}
	$cd_ratio_condition1 = "=";
	$cd_ratio_condition2 = "OR";
	$str_selection_criteria .= (isset($_REQUEST["cd_ratio"]) && trim($_REQUEST["cd_ratio"]) != "") ? "Test - CD Ratio: ".$_REQUEST["cd_ratio"]." " : "";
/*	$arrPtIdCd=array();
	if($_REQUEST["rd_lab_results"]!=1){
		$sel_main2_qry = "select patient_id, date_format(exam_date,'%Y-%m-%d') as dos 
						from chart_optic 
						where cd_val_od ='".$str_cd_ratio."' 
						OR cd_val_os ='".$str_cd_ratio."' ";
		
		if($dtEffectiveDate != ""){
			$sel_main2_qry .= " AND (date_format(exam_date,'%Y-%m-%d') BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
		}
		if($enTimeFrom != "" || $enTimeTo != ""){
			$sel_main2_qry .= " AND (date_format(exam_date,'%H:%m:%i') BETWEEN '".$enTimeFrom."' AND '".$enTimeTo."') ";
		}

		$sel_main2_qry .= "
						group by patient_id 
						order by exam_date desc";
		//echo $sel_main2_qry;
		$sel_main2 = imw_query($sel_main2_qry);
		while($row_main2=imw_fetch_array($sel_main2)){
			$arrPtIdCd[]=$row_main2['patient_id'];
		}
		
	}
	//print_r($arrPtIdCd);
	if(count($arrPtIdCd)>0){
		$arrPtIdssCd=implode(",",$arrPtIdCd);
		$qryCdRatioNotpat="  AND patient_id NOT IN (".$arrPtIdssCd.") ";	
	}*/
	$sel_main2_qry = "select patient_id, date_format(exam_date,'%Y-%m-%d') as dos 
					from chart_optic 
					where cd_val_od ".$cd_ratio_condition1." '".$str_cd_ratio."' 
					".$cd_ratio_condition2." cd_val_os ".$cd_ratio_condition1." '".$str_cd_ratio."' ".$qryCdRatioNotpat.$qryCdRatio;
	
	if($dtEffectiveDate != ""){
		$sel_main2_qry .= " AND (date_format(exam_date,'%Y-%m-%d') BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
		$effectedDate=1;
	}
	
	$sel_main2_qry .= "
					group by patient_id 
					order by exam_date desc";

	$sel_main2 = imw_query($sel_main2_qry);
	while($row_main2 = imw_fetch_array($sel_main2)){$ptCDRatio=true;
		if($effectedDate=='1'){
			if($dtDBEffectDate<= $row_main2['dos'] && $dtDBEffectDate2>=$row_main2['dos']){
				$arr_pt_ids[$row_main2['patient_id']]= $row_main2['patient_id'];
				$pt_cd_ratio[$i]["id"] = $row_main2['patient_id'];
				$pt_cd_ratio[$i]["dos"] = $row_main2['dos'];
				$pt_cd_ratio[$i]["clinical_act"] = "C:D Ratio";
				$i++;
			}
		}else{
			$arr_pt_ids[$row_main2['patient_id']]= $row_main2['patient_id'];
			$pt_cd_ratio[$i]["id"] = $row_main2['patient_id'];
			$pt_cd_ratio[$i]["dos"] = $row_main2['dos'];
			$pt_cd_ratio[$i]["clinical_act"] = "C:D Ratio";
			$i++;
		}
		
	}
}



//AMITWORKINGTARANCALLED
//IOP Pressure
$pt_iop_pressure = array();$iopPat=true;
$qryIOPNotpat="";
$effectedDate=0;
if(count($arrPtIdCd)>0){
	$arrPtIdssCd=implode(",",$arrPtIdCd);
	$qryIOPNotpat="  AND patient_id IN (".$arrPtIdssCd.") ";	
}
if(trim($_REQUEST["iop_pressure"]) != ""){$iopPat=false;
	$sel_main3_qry = "select patient_id, multiple_pressure, date_format(exam_date,'%Y-%m-%d') as dos 
					from chart_iop ".$qryIOPNotpat;
					
	if($dtEffectiveDate != ""){
		$sel_main3_qry .= " AND (date_format(exam_date,'%Y-%m-%d') BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
		$effectedDate=1;
	}
	$sel_main3_qry .= " 
					group by patient_id 
					order by exam_date desc";
	//echo $sel_main3_qry;
	$sel_main3 = imw_query($sel_main3_qry);
	while($row_main3 = imw_fetch_array($sel_main3)){
		$multiple_pressure=unserialize($row_main3['multiple_pressure']);
		//pre($multiple_pressure);
		$iopPressure_od=$multiple_pressure['multiplePressuer']['elem_puffOd'];
		$iopPressure_os=$multiple_pressure['multiplePressuer']['elem_puffOs'];
		switch($_REQUEST["iop_criteria"]){
			case "equalsto":
				if($_REQUEST["rd_lab_results"] == "1"){
					if($iopPressure_os != "" && $iopPressure_od == $_REQUEST["iop_pressure"] || $iopPressure_os == $_REQUEST["iop_pressure"]){
						$pt_iop_pressure[$i]["id"] = $row_main3['patient_id'];$iopPat=true;
						$pt_iop_pressure[$i]["dos"] = $row_main3['dos'];
						$pt_iop_pressure[$i]["clinical_act"] = "IOP Pressure";
						$i++;						
					}
				}else if($_REQUEST["rd_lab_results"] == "0"){					
					if($iopPressure_os != "" && $iopPressure_od != $_REQUEST["iop_pressure"] && $iopPressure_os != $_REQUEST["iop_pressure"]){
						$pt_iop_pressure[$i]["id"] = $row_main3['patient_id'];$iopPat=true;
						$pt_iop_pressure[$i]["dos"] = $row_main3['dos'];
						$pt_iop_pressure[$i]["clinical_act"] = "IOP Pressure";
						$i++;						
					}
				}
				break;
			case "less":
				if($_REQUEST["rd_lab_results"] == "1"){
					if($iopPressure_os != "" && $iopPressure_od < $_REQUEST["iop_pressure"] || $iopPressure_os < $_REQUEST["iop_pressure"]){
						$pt_iop_pressure[$i]["id"] = $row_main3['patient_id'];$iopPat=true;
						$pt_iop_pressure[$i]["dos"] = $row_main3['dos'];
						$pt_iop_pressure[$i]["clinical_act"] = "IOP Pressure";
						$i++;						
					}
				}else if($_REQUEST["rd_lab_results"] == "0"){
					if($iopPressure_os != "" && $iopPressure_od >= $_REQUEST["iop_pressure"] && $iopPressure_os >= $_REQUEST["iop_pressure"]){
						$pt_iop_pressure[$i]["id"] = $row_main3['patient_id'];$iopPat=true;
						$pt_iop_pressure[$i]["dos"] = $row_main3['dos'];
						$pt_iop_pressure[$i]["clinical_act"] = "IOP Pressure";
						$i++;
					}	
				}
				break;
			case "greater":
				if($_REQUEST["rd_lab_results"] == "1"){
					if($iopPressure_os != "" && $iopPressure_od > $_REQUEST["iop_pressure"] || $iopPressure_os > $_REQUEST["iop_pressure"]){
						$pt_iop_pressure[$i]["id"] = $row_main3['patient_id'];$iopPat=true;
						$pt_iop_pressure[$i]["dos"] = $row_main3['dos'];
						$pt_iop_pressure[$i]["clinical_act"] = "IOP Pressure";
						$i++;
					}
				}else if($_REQUEST["rd_lab_results"] == "0"){
					if($iopPressure_os != "" && $iopPressure_od <= $_REQUEST["iop_pressure"] && $iopPressure_os <= $_REQUEST["iop_pressure"]){
						$pt_iop_pressure[$i]["id"] = $row_main3['patient_id'];$iopPat=true;
						$pt_iop_pressure[$i]["dos"] = $row_main3['dos'];
						$pt_iop_pressure[$i]["clinical_act"] = "IOP Pressure";
						$i++;
					}
				}
				break;
		}
	}
	switch($_REQUEST["iop_criteria"]){
		case "equalsto":
			$cond_operator = "";
			break;
		case "less":
			$cond_operator = " < ";
			break;
		case "greater":
			$cond_operator = " > ";
			break;
	}

	$str_selection_criteria .= (isset($_REQUEST["iop_pressure"]) && trim($_REQUEST["iop_pressure"]) != "") ? "Test - IOP Pressure: ".$cond_operator." ".$_REQUEST["iop_pressure"]." " : "";
}


//print "<pre>";
//print_r($patient_id);
//die("lab results");
//print_r($arr_pt_ids);
$qryPt_wights="";
$effectedDate=0;
if(count($arr_pt_ids)>0){
	$arr_SrchPt_ids=implode(",",$arr_pt_ids);
	$qryPt_wights=" AND  vsm.patient_id IN (".$arr_SrchPt_ids.")";
}
if(trim($_REQUEST["weight_val"]) != "" || trim($_REQUEST["height_val"]) != ""){
	$included_excluded .= ($_REQUEST["rd_ht_wt"] == "1") ? "Ht./Wt.: Included " : "Ht./Wt.: Excluded ";
}
if(trim($_REQUEST["age_val"]) != ""|| trim($_REQUEST["zip"]) != ""|| trim($_REQUEST["city"]) != ""|| trim($_REQUEST["state"]) != ""|| trim($_REQUEST["ethnicity"]) != ""|| trim($_REQUEST["race"]) != "" 
|| trim($_REQUEST["language"]) != ""|| trim($_REQUEST["hippa_mail"]) != 0|| trim($_REQUEST["hippa_email"]) != 0|| trim($_REQUEST["hippa_voice"]) != 0|| trim($_REQUEST["voiceType"]) != "" || trim($_REQUEST["suitableTime"])!= ""){
	$included_excluded .= ($_REQUEST["rd_demographics"] == "1") ? "Demographics: Included " : "Demographics: Excluded ";	
}

$pt_wt = array();$pat_weight=true;
if(trim($_REQUEST["weight_val"]) != ""){ //1 pound = 0.45359237 kilograms
	$str_wt_val = $_REQUEST["weight_val"];
	$str_wt_cri = $_REQUEST["weight_criteria"];
	$qryAndCondHeight="";
	if(count($arr_pt_ids)>0){
		$arr_ptIds_dx=implode(",",$arr_pt_ids);
		$qryAndCondHeight=" AND vsm.patient_id IN (".$arr_ptIds_dx.")";
	}
	$arr_checked_patients = array();
	$wt_qry = "select vsp.range_vital, vsm.patient_id, vsm.date_vital, vsp.unit  
				from vital_sign_patient vsp 
				left join vital_sign_master vsm on vsm.id = vsp.vital_master_id 
				where vsm.status=0 AND vsp.vital_sign_id = '8' ".$qryPt_wights.$qryAndCondHeight;
	if($dtEffectiveDate != ""){
		$wt_qry .= " AND (vsm.date_vital BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
		$effectedDate=1;
	}
	
	$wt_qry .= "
				group by vsm.patient_id 
				order by vsm.date_vital desc";
	//echo $wt_qry;
	$wt_sel = imw_query($wt_qry);
	if(imw_num_rows($wt_sel)){
	while($wt_row = imw_fetch_array($wt_sel)){
		$wt_val = $wt_row["range_vital"];
		$wt_patient_id = $wt_row["patient_id"];
		$wt_unit = $wt_row["unit"];
		if($wt_unit == "lbs"){
			$wt_val = round(($wt_val * 0.45359237),2);
		}
		if($arr_checked_patients[$wt_patient_id] == false){
			switch($str_wt_cri){
				case "equalsto":
					if($_REQUEST["rd_ht_wt"] == "1"){$pat_weight=true;
						if(!empty($wt_val) && $wt_val == round($_REQUEST["weight_val"],2)){
							$arr_pt_ids[$wt_patient_id]= $wt_patient_id;
							$pt_wt[$i]["id"] = $wt_patient_id;
							$pt_wt[$i]["dos"] = $wt_row['date_vital'];
							$pt_wt[$i]["clinical_act"] = "Weight";
							$i++;
						}
					}else if($_REQUEST["rd_ht_wt"] == "0"){$pat_weight=true;
						if(!empty($wt_val) && $wt_val != round($_REQUEST["weight_val"],2)){
							$arr_pt_ids[$wt_patient_id]=$wt_patient_id;
							$pt_wt[$i]["id"] = $wt_patient_id;
							$pt_wt[$i]["dos"] = $wt_row['date_vital'];
							$pt_wt[$i]["clinical_act"] = "Weight";
							$i++;
						}
					}
					break;
				case "less":
					if($_REQUEST["rd_ht_wt"] == "1"){$pat_weight=true;
						if(!empty($wt_val) && $wt_val < round($_REQUEST["weight_val"],2)){
							$arr_pt_ids[$wt_patient_id]=$wt_patient_id;
							$pt_wt[$i]["id"] = $wt_patient_id;
							$pt_wt[$i]["dos"] = $wt_row['date_vital'];
							$pt_wt[$i]["clinical_act"] = "Weight";
							$i++;
						}
					}else if($_REQUEST["rd_ht_wt"] == "0"){$pat_weight=true;
						if(!empty($wt_val) && $wt_val >= round($_REQUEST["weight_val"],2)){
							$arr_pt_ids[$wt_patient_id]=$wt_patient_id;
							$pt_wt[$i]["id"] = $wt_patient_id;
							$pt_wt[$i]["dos"] = $wt_row['date_vital'];
							$pt_wt[$i]["clinical_act"] = "Weight";
							$i++;
						}
					}
					break;
				case "greater":
					if($_REQUEST["rd_ht_wt"] == "1"){$pat_weight=true;
						if(!empty($wt_val) && $wt_val > round($_REQUEST["weight_val"],2)){
							$arr_pt_ids[$wt_patient_id]=$wt_patient_id;
							$pt_wt[$i]["id"] = $wt_patient_id;
							$pt_wt[$i]["dos"] = $wt_row['date_vital'];
							$pt_wt[$i]["clinical_act"] = "Weight";
							$i++;
						}
					}else if($_REQUEST["rd_ht_wt"] == "0"){$pat_weight=true;
						if(!empty($wt_val) && $wt_val <= round($_REQUEST["weight_val"],2)){
							$arr_pt_ids[$wt_patient_id]=$wt_patient_id;
							$pt_wt[$i]["id"] = $wt_patient_id;
							$pt_wt[$i]["dos"] = $wt_row['date_vital'];
							$pt_wt[$i]["clinical_act"] = "Weight";
							$i++;
						}
					}
					break;
			}
			$arr_checked_patients[$wt_patient_id]=$wt_patient_id;
		}		
	}
	}

	switch($_REQUEST["weight_criteria"]){
		case "equalsto":
			$cond_operator = "";
			break;
		case "less":
			$cond_operator = " < ";
			break;
		case "greater":
			$cond_operator = " > ";
			break;
	}
	$str_selection_criteria .= (isset($_REQUEST["weight_val"]) && trim($_REQUEST["weight_val"]) != "") ? "Wt.: ".$cond_operator." ".$_REQUEST["weight_val"]." kg " : "";

}

$pt_ht = array();$pat_height=true;
if(trim($_REQUEST["height_val"]) != ""){ //1 inch = 0.0254 meters
	$qryHeight="";
	if(count($arr_pt_ids)>0){
		$implod_height_ids=implode(",",array_unique($arr_pt_ids));
		$qryHeight=" AND vsm.patient_id  IN(".$implod_height_ids.")";
	}
	$str_ht_val = $_REQUEST["height_val"];
	$str_ht_cri = $_REQUEST["height_criteria"];
	
	$arr_checked_patients = array();
	$ht_qry = "select vsp.range_vital,vsm.patient_id, vsm.date_vital, vsp.unit  
				from vital_sign_patient vsp 
				left join vital_sign_master vsm on vsm.id = vsp.vital_master_id 
				where vsm.status=0 AND  vsp.vital_sign_id = '7' ".$qryHeight;
				
	if($dtEffectiveDate != ""){
		$ht_qry .= " AND (vsm.date_vital BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
	}
	
	$ht_qry .= "
				group by vsm.patient_id 
				order by vsm.date_vital desc";
	//echo "<br><br>".$ht_qry; 
	$ht_sel = imw_query($ht_qry);
	while($ht_row = imw_fetch_array($ht_sel)){
		$ht_val = $ht_row["range_vital"];
		$ht_patient_id = $ht_row["patient_id"];
		$ht_unit = $ht_row["unit"];
		if($ht_unit == "inch"){
			$ht_val = round(($ht_val * 0.0254),2);
		}
		//echo $ht_val;
		if($arr_checked_patients[$ht_patient_id] == false){
			switch($str_ht_cri){
				case "equalsto":
					if($_REQUEST["rd_ht_wt"] == "1"){$pat_height=false;
						
						if(!empty($ht_val) && $ht_val == round($_REQUEST["height_val"],2)){$pat_height=true;
							$arr_pt_ids[$ht_patient_id]=$ht_patient_id;
							$pt_ht[$i]["id"] = $ht_patient_id;
							$pt_ht[$i]["dos"] = $ht_row['date_vital'];
							$pt_ht[$i]["clinical_act"] = "Height";
							$i++;
						}
					}else if($_REQUEST["rd_ht_wt"] == "0"){$pat_height=false;
						
						if(!empty($ht_val) && $ht_val != round($_REQUEST["height_val"],2)){$pat_height=true;
							$arr_pt_ids[$ht_patient_id]=$ht_patient_id;
							$pt_ht[$i]["id"] = $ht_patient_id;
							$pt_ht[$i]["dos"] = $ht_row['date_vital'];
							$pt_ht[$i]["clinical_act"] = "Height";
							$i++;
						}
					}
					break;
				case "less":
					if($_REQUEST["rd_ht_wt"] == "1"){$pat_height=false;
						if(!empty($ht_val) && $ht_val < round($_REQUEST["height_val"],2)){$pat_height=true;
							$arr_pt_ids[$ht_patient_id]=$ht_patient_id;
							$pt_ht[$i]["id"] = $ht_patient_id;
							$pt_ht[$i]["dos"] = $ht_row['date_vital'];
							$pt_ht[$i]["clinical_act"] = "Height";
							$i++;
						}
					}else if($_REQUEST["rd_ht_wt"] == "0"){$pat_height=false;
						if(!empty($ht_val) && $ht_val >= round($_REQUEST["height_val"],2)){$pat_height=true;
							$pt_ht[$i]["id"] = $ht_patient_id;
							$pt_ht[$i]["dos"] = $ht_row['date_vital'];
							$pt_ht[$i]["clinical_act"] = "Height";
							$i++;
						}
					}
					break;
				case "greater":
					if($_REQUEST["rd_ht_wt"] == "1"){$pat_height=false;
						if(!empty($ht_val) && $ht_val > round($_REQUEST["height_val"],2)){$pat_height=true;
							$arr_pt_ids[$ht_patient_id]=$ht_patient_id;
							$pt_ht[$i]["id"] = $ht_patient_id;
							$pt_ht[$i]["dos"] = $ht_row['date_vital'];
							$pt_ht[$i]["clinical_act"] = "Height";
							$i++;
						}
					}else if($_REQUEST["rd_ht_wt"] == "0"){$pat_height=false;
						if(!empty($ht_val) && $ht_val <= round($_REQUEST["height_val"],2)){$pat_height=true;
							$arr_pt_ids[$ht_patient_id]=$ht_patient_id;
							$pt_ht[$i]["id"] = $ht_patient_id;
							$pt_ht[$i]["dos"] = $ht_row['date_vital'];
							$pt_ht[$i]["clinical_act"] = "Height";
							$i++;
						}
					}
					break;
			}
			$arr_checked_patients[$ht_patient_id] = $ht_patient_id;
		}		
	}
	switch($_REQUEST["height_criteria"]){
		case "equalsto":
			$cond_operator = "";
			break;
		case "less":
			$cond_operator = " < ";
			break;
		case "greater":
			$cond_operator = " > ";
			break;
	}
	$str_selection_criteria .= (isset($_REQUEST["height_val"]) && trim($_REQUEST["height_val"]) != "") ? "Ht.: ".$cond_operator." ".$_REQUEST["height_val"]." m " : "";

}


//immunizations / vaccines
$pt_imm = array();$immuzation=true;
if(trim($_REQUEST["immunizations"]) != ""){$immuzation=false;
	$imm_condition = ($_REQUEST["rd_imm_list"] == "1") ? "IN" : "NOT IN";
	$included_excluded .= ($_REQUEST["rd_imm_list"] == "1") ? "Immunizations/Vaccines: Included " : "Immunizations/Vaccines: Excluded ";
	
	$str_imm_list = nl2br($_REQUEST["immunizations"]);
	$str_selection_criteria .= (isset($_REQUEST["immunizations"]) && trim($_REQUEST["immunizations"]) != "") ? "Imm.: ".$_REQUEST["immunizations"]." " : "";

	$arr_imm_list = explode("<br />",$str_imm_list);
	
	//$str_imm_list = "";
	//if(is_array($arr_imm_list) && count($arr_imm_list) > 0){
	//	foreach($arr_imm_list as $imm_val){
	//		$str_imm_list .= "'".trim(stripcslashes($imm_val))."',";
	//	}
	//	$str_imm_list = substr($str_imm_list, 0, -1);
	//}
	//echo count($arr_imm_list);
	if($_REQUEST["rd_imm_list"]!=1){
		
		$sel_imm_qry = "select patient_id, administered_date, immunization_id  
						from immunizations WHERE 1=1 ";
		if($dtEffectiveDate != ""){
			$sel_imm_qry .= " AND  (administered_date BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
		}
		
		$sel_imm_qry .= "
						GROUP BY patient_id 
						ORDER BY administered_date DESC";
		//echo $sel_med_qry."";
		$sel_imm=imw_query($sel_imm_qry);
		while($row_imm=imw_fetch_array($sel_imm)){
			if(strstr($row_imm['immunization_id'], " - ") == true){
				$arr_imm_name = explode(" - ", $row_imm['immunization_id']);
				$str_imm_name = trim($arr_imm_name[1]);	
			}else{
				$str_imm_name = trim($row_imm['immunization_id']);	
			}
			if(in_array($str_imm_name, $arr_imm_list)){
				$pt_id_imm[$row_imm['patient_id']] = $row_imm['patient_id'];
				
				$i++;
			}
		}
		
		
	}
	//pre($pt_id_imm);
	if(count($arr_imm_list) > 0){
		$qryImmu=$qryImmuJa="";
		if(count($pt_id_imm)>0){
			$implod_height_ids=implode(",",array_unique($pt_id_imm));
			$qryImmu=" AND patient_id NOT IN(".$implod_height_ids.")";
			$qryImmuJa=" AND id NOT IN(".$implod_height_ids.")";
		}
		$sel_imm_qry = "select patient_id, administered_date, immunization_id  
						from immunizations WHERE 1=1 ".$qryImmu;
		if($dtEffectiveDate != ""){
			$sel_imm_qry .= "  AND  (administered_date BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
		}
		
		$sel_imm_qry .= "
						GROUP BY patient_id 
						ORDER BY administered_date DESC";
		//echo $sel_imm_qry;
		$sel_imm=imw_query($sel_imm_qry);
		if($imm_condition == "IN"){
			while($row_imm=imw_fetch_array($sel_imm)){
				if(strstr($row_imm['immunization_id'], " - ") == true){
					$arr_imm_name = explode(" - ", $row_imm['immunization_id']);
					$str_imm_name = trim($arr_imm_name[1]);	
				}else{
					$str_imm_name = trim($row_imm['immunization_id']);	
				}
				if(in_array($str_imm_name, $arr_imm_list)){$immuzation=true;
					$arr_pt_ids[$row_imm['patient_id']]= $row_imm['patient_id'];
					$pt_imm[$i]["id"] = $row_imm['patient_id'];
					$pt_imm[$i]["dos"] = $row_imm['administered_date'];
					$pt_imm[$i]["clinical_act"] = $row_imm['immunization_id'];
					$i++;
				}
			}
		}else if($imm_condition == "NOT IN"){
			while($row_imm=imw_fetch_array($sel_imm)){$immuzation=true;
				if(strstr($row_imm['immunization_id'], " - ") == true){
					$arr_imm_name = explode(" - ", $row_imm['immunization_id']);
					$str_imm_name = trim($arr_imm_name[1]);	
				}else{
					$str_imm_name = trim($row_imm['immunization_id']);	
				}
				if(!in_array($str_imm_name, $arr_imm_list)){$immuzation=true;
					$arr_pt_ids[$row_imm['patient_id']]= $row_imm['patient_id'];
					$pt_imm[$i]["id"] = $row_imm['patient_id'];
					$pt_imm[$i]["dos"] = $row_imm['administered_date'];
					$pt_imm[$i]["clinical_act"] = $row_imm['immunization_id'];
					$i++;
				}
			}
		}
	}
	if($_REQUEST["rd_imm_list"]!=1){
		$pt_ids_in_patient=implode(",",array_unique($arr_pt_ids));
		$qryConcatIds="";
		if(count($arr_patt_ids)>0){
			$qryConcatIds=" AND id not in(".$pt_ids_in_patient.")";	
		}
		$qryPatNoProblem="SELECT id from patient_data where 1=1 ".$qryConcatIds.$qryImmuJa;//which patients has no problem list
		$resPatNoProblem=imw_query($qryPatNoProblem);
		if(imw_num_rows($resPatNoProblem)>0){
			while($rowPatNoProblem=imw_fetch_assoc($resPatNoProblem)){
				$arr_pt_ids[$rowPatNoProblem["id"]]=$rowPatNoProblem["id"];
				$pt_imm[]["id"] = $rowPatNoProblem["id"];
			}	
		}
	}
}


//print "<pre>";
//print_r($patient_id);
//die;

//print "<pre>";
//echo "pt_dx_code";
//print_r($pt_dx_codes);
//echo "pt_medications";
//print_r($pt_medications);
//echo "pt_tests";
//print_r($pt_tests);
//echo "pt_cd_ratio";
//print_r($pt_cd_ratio);
//echo "pt_iop_pressure";
//print_r($pt_iop_pressure);
//echo "pt_wt";
//print_r($pt_wt);
//echo "pt_ht";
//print_r($pt_ht);
//echo "pt_imm";
//print_r($pt_imm);
//echo "pt_cholesterol";
//print_r($pt_cholesterol);
//print_r($pt_id_dx);
//print_r($pt_id_dx);
//
//print_r($pt_dx_codes);
//die;
if($dx_code_patient==true && $med_patients==true && $allergy_pat==true && $pat_weight==true && $pat_height==true && $ptCDRatio==true && $immuzation==true && $iopPat==true){
	if(is_array($pt_dx_codes) && count($pt_dx_codes) > 0){
		foreach($pt_dx_codes as $this_arr){
			if(!in_array($this_arr["id"],$pt_id_dx)){
				$pt_dx_codes_new[] = $this_arr["id"];
			}
		}
	}

//print_r($pt_dx_codes_new);
	if(is_array($pt_medications) && count($pt_medications) > 0){
		foreach($pt_medications as $this_arr){
			$pt_medications_new[] = $this_arr["id"];
		}
	}

	if(is_array($pt_medications_allergy) && count($pt_medications_allergy) > 0){
		foreach($pt_medications_allergy as $this_arr){
			$pt_medications_allergy_new[] = $this_arr["id"];
		}
	}

	if(is_array($pt_wt) && count($pt_wt) > 0){
		foreach($pt_wt as $this_arr){
			$pt_wt_new[] = $this_arr["id"];
		}
	}
	if(is_array($pt_ht) && count($pt_ht) > 0){
		foreach($pt_ht as $this_arr){
			$pt_ht_new[] = $this_arr["id"];
		}
	}
	if(is_array($pt_tests) && count($pt_tests) > 0){
		foreach($pt_tests as $this_arr){
			$pt_tests_new[] = $this_arr["id"];
		}
	}
	if(is_array($pt_cd_ratio) && count($pt_cd_ratio) > 0){
		foreach($pt_cd_ratio as $this_arr){
			$pt_cd_ratio_new[] = $this_arr["id"];
		}
	}
	if(is_array($pt_imm) && count($pt_imm) > 0){
		foreach($pt_imm as $this_arr){
			$pt_imm_new[] = $this_arr["id"];
		}
	}	
	if(is_array($pt_iop_pressure) && count($pt_iop_pressure) > 0){
		foreach($pt_iop_pressure as $this_arr){
			$pt_iop_pressure_new[] = $this_arr["id"];
		}
	}
	if(is_array($pt_cholesterol) && count($pt_cholesterol) > 0){
		foreach($pt_cholesterol as $this_arr){
			$pt_cholesterol_new[] = $this_arr["id"];
		}
	}

}
if(isset($_REQUEST["and_or"]) && $_REQUEST["and_or"] == "A"){
	$patient_tmp_id = array_merge($pt_dx_codes, $pt_medications, $pt_medications_allergy, $pt_tests, $pt_cd_ratio, $pt_iop_pressure, $pt_wt, $pt_ht, $pt_imm);
	//print "<pre>sdsfsdfds";
//print_r($patient_tmp_id);

	$pt_combined_cont = array();
	$arrDates=array();
	$arr_temp = array();
	if(is_array($patient_tmp_id) && count($patient_tmp_id) > 0){
		foreach($patient_tmp_id as $this_arr){
			if(in_array($this_arr["id"], $arr_temp)){
				$pt_combined_cont[$this_arr["id"]]["clinical_act"] = $pt_combined_cont[$this_arr["id"]]["clinical_act"].", ".$this_arr["clinical_act"];
				$arrDates[$this_arr["id"]]=$this_arr["id"];
			}else{
				$pt_combined_cont[$this_arr["id"]]["id"] = $this_arr["id"];
				$pt_combined_cont[$this_arr["id"]]["dos"] = $this_arr["dos"];
				$pt_combined_cont[$this_arr["id"]]["clinical_act"] = $this_arr["clinical_act"];
				$pt_combined_cont[$this_arr["id"]]["clinical_act_cpt"] = $this_arr["clinical_act_cpt"];
				$arrDates[$this_arr["id"]]=$this_arr["id"];
			}
			array_push($arr_temp, $this_arr["id"]);
		}
	}
	// SET DOS OF PATIENT
/*	if(sizeof($arrDates)>0){
		foreach($arrDates as $patId => $datesData){
			$largesDate='';
			$largesDate = max($datesData);
			if(empty($largesDate)==false){
				$dosRs=imw_query("Select date_of_service FROM chart_master_table WHERE patient_id =".$patId." AND date_of_service<='".$largesDate."' 
				ORDER BY date_of_service DESC LIMIT 0,1");
				$dosRes=imw_fetch_array($dosRs);
				$patDos=$dosRes['date_of_service'];
				$pt_combined_cont[$patId]["dos"] = $patDos;
			}
		}
	}
*/
	if(sizeof($arrDates)>0){
		foreach($arrDates as $patId){
			$dosRs=imw_query("Select date_of_service FROM chart_master_table WHERE patient_id =".$patId."  
			ORDER BY date_of_service DESC LIMIT 0,1");
			$dosRes=imw_fetch_array($dosRs);
			$patDos=$dosRes['date_of_service'];
			$pt_combined_cont[$patId]["dos"] = $patDos;
		}
	}	
	$pt_combined_cont = array_values($pt_combined_cont);

	$patient_id = array();
	$int_cnt3 = 0;
	if(is_array($pt_combined_cont) && count($pt_combined_cont) > 0){
		foreach($pt_combined_cont as $this_arr){
			//print "<pre>";
			//print_r($this_arr);
			$bl_dx_process = 0;
			$bl_med_process = 0;
			$bl_med_allergy_process = 0;
			$bl_test_process = 0;
			$bl_cd_process = 0;
			$bl_iop_process = 0;
			$bl_wt_process = 0;
			$bl_ht_process = 0;
			$bl_imm_process = 0;
			$bl_ch_process = 0;

			if(count($pt_dx_codes) > 0){
				//echo "dxh";
				if(in_array($this_arr["id"],$pt_dx_codes_new)){
					$bl_dx_process = 1;
				}else{
					$bl_dx_process = 0;
				}
			}else{
				//echo "dxt";
				$bl_dx_process = 1;
			}
			
			
			if(count($pt_medications) > 0){
				//echo "mh";
				if(in_array($this_arr["id"],$pt_medications_new)){
					$bl_med_process = 1;
				}else{
					$bl_med_process = 0;
				}
			}else{
				//echo "mt";
				$bl_med_process = 1;
			}

			if(count($pt_medications_allergy) > 0){
				//echo "mh";
				if(in_array($this_arr["id"],$pt_medications_allergy_new)){
					$bl_med_allergy_process = 1;
				}else{
					$bl_med_allergy_process = 0;
				}
			}else{
				//echo "mt";
				$bl_med_allergy_process = 1;
			}
			
			
			if(count($pt_tests) > 0){
				//echo "th";
				if(in_array($this_arr["id"],$pt_tests_new)){
					$bl_test_process = 1;
				}else{
					$bl_test_process = 0;
				}
			}else{
				//echo "tt";
				$bl_test_process = 1;
			}
			
			
			if(count($pt_cd_ratio) > 0){
				//echo "cdh";
				if(in_array($this_arr["id"],$pt_cd_ratio_new)){
					$bl_cd_process = 1;
				}else{
					$bl_cd_process = 0;
				}
			}else{
				//echo "cdt";
				$bl_cd_process = 1;
			}
			
			
			if(count($pt_iop_pressure) > 0){
				//echo "ih";
				if(in_array($this_arr["id"],$pt_iop_pressure_new)){
					$bl_iop_process = 1;
				}else{
					$bl_iop_process = 0;
				}
			}else{
				//echo "it";
				$bl_iop_process = 1;
			}
			
			
			if(count($pt_wt) > 0){
				//echo "wh";
				if(in_array($this_arr["id"],$pt_wt_new)){
					$bl_wt_process = 1;
				}else{
					$bl_wt_process = 0;
				}
			}else{
				//echo "wt";
				$bl_wt_process = 1;
				if($_REQUEST["weight_val"]!=""){
					$bl_wt_process = 0;
				}
			}
			
			
			if(count($pt_ht) > 0){
				//echo "hh";
				if(in_array($this_arr["id"],$pt_ht_new)){
					$bl_ht_process = 1;
				}else{
					
					$bl_ht_process = 0;
				}
			}else{
				//echo "ht";
				$bl_ht_process = 1;
				if($_REQUEST['height_val']){
					$bl_ht_process = 0;
				}
				
			}
			
			
			if(count($pt_imm) > 0){
				//echo "immh";
				if(in_array($this_arr["id"],$pt_imm_new)){
					$bl_imm_process = 1;
				}else{
					$bl_imm_process = 0;
				}
			}else{
				//echo "immt";
				$bl_imm_process = 1;
			}
			
			
			if(count($pt_cholesterol) > 0){
				//echo "chh";
				if(in_array($this_arr["id"],$pt_cholesterol_new)){
					$bl_ch_process = 1;
				}else{
					$bl_ch_process = 0;
				}
			}else{
				//echo "cht";
				$bl_ch_process = 1;
			}

			//echo "$bl_dx_process && $bl_med_process && $bl_test_process && $bl_cd_process && $bl_iop_process && $bl_wt_process && $bl_ht_process && $bl_imm_process && $bl_ch_process ";

			if($bl_dx_process == 1 && $bl_med_process == 1 && $bl_med_allergy_process == 1 && $bl_test_process == 1 && $bl_cd_process == 1 && $bl_iop_process == 1 && $bl_wt_process == 1 && $bl_ht_process == 1 && $bl_imm_process == 1 && $bl_ch_process == 1){
				$pid = $this_arr["id"];
				$arrAllPats[$pid]=$pid;
				
				$patient_id[$pid]["id"] = $pid;
				$patient_id[$pid]["dos"] = $this_arr["dos"];
				$patient_id[$pid]["clinical_act"] = $this_arr["clinical_act"];
				$patient_id[$pid]["clinical_act_cpt"] = $this_arr["clinical_act_cpt"];
				$int_cnt3++;
			}
		}
	}
}else if(isset($_REQUEST["and_or"]) && $_REQUEST["and_or"] == "O"){

	$patient_tmp_id = array_merge($pt_dx_codes, $pt_medications, $pt_medications_allergy, $pt_tests, $pt_cd_ratio, $pt_iop_pressure, $pt_wt, $pt_ht, $pt_imm);
	//print "<pre>s";
//print_r($patient_tmp_id);

	$pt_combined_cont = array();
	$arr_temp = array();
	if(is_array($patient_tmp_id) && count($patient_tmp_id) > 0){
		foreach($patient_tmp_id as $this_arr){
			if(in_array($this_arr["id"], $arr_temp)){
				$pt_combined_cont[$this_arr["id"]]["clinical_act"] = $pt_combined_cont[$this_arr["id"]]["clinical_act"].", ".$this_arr["clinical_act"];
			}else{
				$pt_combined_cont[$this_arr["id"]]["id"] = $this_arr["id"];
				$pt_combined_cont[$this_arr["id"]]["dos"] = $this_arr["dos"];
				$pt_combined_cont[$this_arr["id"]]["clinical_act"] = $this_arr["clinical_act"];
				$pt_combined_cont[$this_arr["id"]]["clinical_act_cpt"] = $this_arr["clinical_act_cpt"];
			}
			array_push($arr_temp, $this_arr["id"]);
		}
	}
	$pt_combined_cont = array_values($pt_combined_cont);
	//print "<pre>s";
//print_r($pt_combined_cont);

	$patient_id = array();
	$int_cnt3 = 0;
	if(is_array($pt_combined_cont) && count($pt_combined_cont) > 0){
		foreach($pt_combined_cont as $this_arr){
			//print "<pre>";
			//print_r($this_arr);
			$bl_dx_process = 0;
			$bl_med_process = 0;
			$bl_med_allergy_process = 0;
			$bl_test_process = 0;
			$bl_cd_process = 0;
			$bl_iop_process = 0;
			$bl_wt_process = 0;
			$bl_ht_process = 0;
			$bl_imm_process = 0;
			$bl_ch_process = 0;

			if(count($pt_dx_codes) > 0){
				//echo "dxh";
				if(in_array($this_arr["id"],$pt_dx_codes_new)){
					$bl_dx_process = 1;
				}else{
					$bl_dx_process = 0;
				}
			}else{
				//echo "dxt";
				$bl_dx_process = 1;
			}
			
			
			if(count($pt_medications) > 0){
				//echo "mh";
				if(in_array($this_arr["id"],$pt_medications_new)){
					$bl_med_process = 1;
				}else{
					$bl_med_process = 0;
				}
			}else{
				//echo "mt";
				$bl_med_process = 1;
			}

			if(count($pt_medications_allergy) > 0){
				//echo "mh";
				if(in_array($this_arr["id"],$pt_medications_allergy_new)){
					$bl_med_allergy_process = 1;
				}else{
					$bl_med_allergy_process = 0;
				}
			}else{
				//echo "mt";
				$bl_med_allergy_process = 1;
			}
			
			if(count($pt_tests) > 0){
				//echo "th";
				if(in_array($this_arr["id"],$pt_tests_new)){
					$bl_test_process = 1;
				}else{
					$bl_test_process = 0;
				}
			}else{
				//echo "tt";
				$bl_test_process = 1;
			}
			
			
			if(count($pt_cd_ratio) > 0){
				//echo "cdh";
				if(in_array($this_arr["id"],$pt_cd_ratio_new)){
					$bl_cd_process = 1;
				}else{
					$bl_cd_process = 0;
				}
			}else{
				//echo "cdt";
				$bl_cd_process = 1;
			}
			
			
			if(count($pt_iop_pressure) > 0){
				//echo "ih";
				if(in_array($this_arr["id"],$pt_iop_pressure_new)){
					$bl_iop_process = 1;
				}else{
					$bl_iop_process = 0;
				}
			}else{
				//echo "it";
				$bl_iop_process = 1;
			}
			
			
			if(count($pt_wt) > 0){
				//echo "wh";
				if(in_array($this_arr["id"],$pt_wt_new)){
					$bl_wt_process = 1;
				}else{
					$bl_wt_process = 0;
				}
			}else{
				//echo "wt";
				$bl_wt_process = 1;
			}
			
			
			if(count($pt_ht) > 0){
				//echo "hh";
				if(in_array($this_arr["id"],$pt_ht_new)){
					$bl_ht_process = 1;
				}else{
					$bl_ht_process = 0;
				}
			}else{
				//echo "ht";
				$bl_ht_process = 1;
			}
			
			
			if(count($pt_imm) > 0){
				//echo "immh";
				if(in_array($this_arr["id"],$pt_imm_new)){
					$bl_imm_process = 1;
				}else{
					$bl_imm_process = 0;
				}
			}else{
				//echo "immt";
				$bl_imm_process = 1;
			}
			
			
			if(count($pt_cholesterol) > 0){
				//echo "chh";
				if(in_array($this_arr["id"],$pt_cholesterol_new)){
					$bl_ch_process = 1;
				}else{
					$bl_ch_process = 0;
				}
			}else{
				//echo "cht";
				$bl_ch_process = 1;
			}

			//echo "$bl_dx_process && $bl_med_process && $bl_test_process && $bl_cd_process && $bl_iop_process && $bl_wt_process && $bl_ht_process && $bl_imm_process && $bl_ch_process ";

			if($bl_dx_process == 1 || $bl_med_process == 1 || $bl_med_allergy_process == 1 || $bl_test_process == 1 || $bl_cd_process == 1 || $bl_iop_process == 1 || $bl_wt_process == 1 || $bl_ht_process == 1 || $bl_imm_process == 1 || $bl_ch_process == 1){
				$pid=$this_arr["id"];
				$arrAllPats[$pid]=$pid;
				
				$patient_id[$pid]["id"] = $pid;
				$patient_id[$pid]["dos"] = $this_arr["dos"];
				$patient_id[$pid]["clinical_act"] = $this_arr["clinical_act"];
				$patient_id[$pid]["clinical_act_cpt"] = $this_arr["clinical_act_cpt"];
				$int_cnt3++;
			}
			
		}
	}
}
//print "<pre>";
//print_r($pt_cholesterol);
//die("cholesterol");

//print "<pre>d";
//print_r($patient_id);
//die("height and weight");

//patient age
if(trim($_REQUEST["age_val"]) != ""){
	$str_ht_val = $_REQUEST["age_val"];
	$str_ht_cri = $_REQUEST["age_criteria"];

	switch($str_ht_cri){
		case "equalsto":
			$cond_operator = "";
			break;
		case "less":
			$cond_operator = " < ";
			break;
		case "greater":
			$cond_operator = " > ";
			break;
		case "greater_equal":
			$cond_operator = " >= ";
			break;
		case "less_equal":
			$cond_operator = " <= ";
			break;
	}
	$str_selection_criteria .= (isset($_REQUEST["age_val"]) && trim($_REQUEST["age_val"]) != "") ? "Age: ".$cond_operator." ".$_REQUEST["age_val"]." yrs. " : "";
}

$voiceType='';
switch($_REQUEST["voiceType"]){
 case '0':
 $voiceType='Home Phone'; break;
 case '1':
 $voiceType='Work Phone'; break;
 case '2':
 $voiceType='Mobile Phone'; break;
 default:
 $voiceType='';
}

$included_excluded .= ($_REQUEST["rd_demographics"] == "1") ? "Demographics: Included " : "Demographics: Excluded ";

$str_selection_criteria .= (isset($_REQUEST["zip"]) && trim($_REQUEST["zip"]) != "") ? "Zip Code: ".$_REQUEST["zip"]." " : "";
$str_selection_criteria .= (isset($_REQUEST["city"]) && trim($_REQUEST["city"]) != "") ? "City: ".$_REQUEST["city"]." " : "";
$str_selection_criteria .= (isset($_REQUEST["state"]) && trim($_REQUEST["state"]) != "") ? "State: ".$_REQUEST["state"]." " : "";
$str_selection_criteria .= (isset($_REQUEST["ethnicity"]) && trim($_REQUEST["ethnicity"]) != "") ? "Ethnicity: ".$_REQUEST["ethnicity"]." " : "";
$str_selection_criteria .= (isset($_REQUEST["race"]) && trim($_REQUEST["race"]) != "") ? "Race: ".$_REQUEST["race"]." " : "";
$str_selection_criteria .= (isset($_REQUEST["language"]) && trim($_REQUEST["language"]) != "") ? "Language: ".$_REQUEST["language"]." " : "";
$str_selection_criteria .= (isset($_REQUEST["hippa_mail"]) && trim($_REQUEST["hippa_mail"]) != "0") ? "Postal Mail: Yes " : "";
$str_selection_criteria .= (isset($_REQUEST["hippa_email"]) && trim($_REQUEST["hippa_email"]) != "0") ? "Email: Yes " : "";
$str_selection_criteria .= (isset($_REQUEST["hippa_voice"]) && trim($_REQUEST["hippa_voice"]) != "0") ? "Voice: Yes " : "";
$str_selection_criteria .= (isset($_REQUEST["voiceType"]) && trim($_REQUEST["voiceType"]) != "") ? "Voice Type: ".$voiceType." " : "";
$str_selection_criteria .= (isset($_REQUEST["sex"]) && trim($_REQUEST["sex"]) != "") ? "Gender: ".ucfirst($_REQUEST["sex"])." " : "";
$showTimeFrom=$showTimeTo=$suitTimeFrom=$suitTimeTo='';
if(trim($_REQUEST["suitableTime"])!= ""){
	$timePart=explode('-', $_REQUEST["suitableTime"]);
	
	$tFrom=explode(':', $timePart[0]);
	$showTimeFrom=$tFrom[0].':'.$tFrom[1].' '.$tFrom[2];
	if($tFrom[2]=='PM'){
		$tFrom[0] = 12 + $tFrom[0];
		if($tFrom[0]=='24'){ $tFrom[0]='00'; }
	}

	$tTo=explode(':', $timePart[1]);
	$showTimeTo=$tTo[0].':'.$tTo[1].' '.$tTo[2];
	if($tTo[2]=='PM'){
		$tTo[0] = 12 + $tTo[0];
		if($tTo[0]=='24'){ $tTo[0]='00'; }
	}
	
	$suitTimeFrom=$tFrom[0].':'.$tFrom[1].':00';
	$suitTimeTo=$tTo[0].':'.$tTo[1].':00';
}
$str_selection_criteria .= (isset($_REQUEST["suitableTime"]) && trim($_REQUEST["suitableTime"]) != "") ? "Call Time: From ".$showTimeFrom." To ".$showTimeTo." " : "";

$demographics=0;
if($_REQUEST["zip"]!='' || $_REQUEST["city"]!='' || $_REQUEST["state"]!='' || $_REQUEST["ethnicity"]!='' || $_REQUEST["race"]!='' || $_REQUEST["language"]!='' || 
$_REQUEST["hippa_mail"]!='0' || $_REQUEST["hippa_email"]!='0' || $_REQUEST["hippa_voice"]!='0' || $_REQUEST["voiceType"]!='' || $_REQUEST["age_val"]!='' || $_REQUEST["physicians"]!='' 
|| $_REQUEST["suitableTime"]!='' || $_REQUEST["sex"]!=''){
	$demographics=1;
}


/* Modification
 
 * Purpose: Add Functionality of Search bty string in Chart Summary
 */
//$count = 500;	/*Paging*/
$prevIds = 0;
$searchString = 0;
$flagMr = 1; /*Flag for MR Given*/
$ssIds = array(); /*Array to hold patient Ids from all chart Tables*/
if(trim($_REQUEST['searchString']!="")){
	
	$qryWhere = "";
	$prevIds = 0;
	if(count($arrAllPats)>0){
		$strAllPats=implode(',', $arrAllPats);
		$qryWhere = " AND `patientId` IN(".$strAllPats.")";
		$qryWhere1 = " AND `patient_id` IN(".$strAllPats.")";
		$prevIds = 1;
	}
	$arrAllPats= array();
	$string = trim($_REQUEST['searchString']);
	$str_selection_criteria .= "Keyword: ".$string."<br />";
		
		/*Query to `chart_left_cc_history` Table*/
		if(count($arrAllPats)>0){
			$splitted_patients = array_chunk($arrAllPats,1500);
			foreach($splitted_patients as $arr){
				$strPatId='';
				$strPatId=implode(',', $arr);
				$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_left_cc_history` WHERE `ccompliant` LIKE '%".$string."%' AND patient_id IN(".$strPatId.")";
				$rs=imw_query($qry);	
				while($row=imw_fetch_assoc($rs)){
					if(isset($ssIds[$row['patient_id']]['selCriteria'])){
						$ssIds[$row['patient_id']]['selCriteria'] .= "Chief Complain, ";
					}else{
						$ssIds[$row['patient_id']]['selCriteria'] = "Chief Complain, ";
					}
					$arrAllPats[$row['patient_id']] = $row['patient_id'];
				} unset($rs);
			}
		} else {
			$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_left_cc_history` WHERE `ccompliant` LIKE '%".$string."%'";
			$rs=imw_query($qry);	
			while($row=imw_fetch_assoc($rs)){
				if(isset($ssIds[$row['patient_id']]['selCriteria'])){
					$ssIds[$row['patient_id']]['selCriteria'] .= "Chief Complain, ";
				}else{
					$ssIds[$row['patient_id']]['selCriteria'] = "Chief Complain, ";
				}
				$arrAllPats[$row['patient_id']] = $row['patient_id'];
			}unset($rs);
		}
		
		/*Query to `chart_pupil` Table*/
		if(count($arrAllPats)>0){
			$splitted_patientspupil = array_chunk($arrAllPats,1500);
			foreach($splitted_patientspupil as $arr){
				$strPatIdpupil='';
				$strPatIdpupil=implode(',', $arr);
				$qry= "SELECT DISTINCT(`patientId`) FROM `chart_pupil` WHERE (`sumOdPupil` LIKE '%".$string."%' OR `sumOsPupil` LIKE '%".$string."%') AND patientId IN(".$strPatIdpupil.")";
				$rs=imw_query($qry);	
				while($row=imw_fetch_assoc($rs)){
					if(isset($ssIds[$row['patient_id']]['selCriteria'])){
						$ssIds[$row['patient_id']]['selCriteria'] .= "Pupil, ";
					}else{
						$ssIds[$row['patient_id']]['selCriteria'] = "Pupil, ";
					}
					$arrAllPats[$row['patientId']] = $row['patientId'];
				}unset($rs);	
				
			}
		}else{
			$qry= "SELECT DISTINCT(`patientId`) FROM `chart_pupil` WHERE (`sumOdPupil` LIKE '%".$string."%' OR `sumOsPupil` LIKE '%".$string."%')";
			$rs=imw_query($qry);	
			while($row=imw_fetch_assoc($rs)){
				if(isset($ssIds[$row['patient_id']]['selCriteria'])){
					$ssIds[$row['patient_id']]['selCriteria'] .= "Pupil, ";
				}else{
					$ssIds[$row['patient_id']]['selCriteria'] = "Pupil, ";
				}
				$arrAllPats[$row['patientId']] = $row['patientId'];
			}unset($rs);
		}
				
		/*Query to `chart_eom` Table*/
		if(count($arrAllPats)>0){
			$splitted_patientsEom = array_chunk($arrAllPats,1500);
			foreach($splitted_patientsEom as $arr){
				$strPatIdEom='';
				$strPatIdEom=implode(',', $arr);
				$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_eom` WHERE `sumEom` LIKE '%".$string."%' AND patient_id IN(".$strPatIdEom.")";
				$rs=imw_query($qry);	
				while($row=imw_fetch_assoc($rs)){
					if(isset($ssIds[$row['patient_id']]['selCriteria'])){
						$ssIds[$row['patient_id']]['selCriteria'] .= "EOM, ";
					}else{
						$ssIds[$row['patient_id']]['selCriteria'] = "EOM, ";
					}
					$arrAllPats[$row['patient_id']] = $row['patient_id'];
				}unset($rs);	
				
			}
		}else{
			$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_eom` WHERE `sumEom` LIKE '%".$string."%')";
			$rs=imw_query($qry);	
			while($row=imw_fetch_assoc($rs)){
				if(isset($ssIds[$row['patient_id']]['selCriteria'])){
					$ssIds[$row['patient_id']]['selCriteria'] .= "EOM, ";
				}else{
					$ssIds[$row['patient_id']]['selCriteria'] = "EOM, ";
				}
				$arrAllPats[$row['patient_id']] = $row['patient_id'];
			}unset($rs);
		}
		
		/*Query to `chart_external_exam` Table*/
		if(count($arrAllPats)>0){
			$splitted_patientsExam = array_chunk($arrAllPats,1500);
			foreach($splitted_patientsExam as $arr){
				$strPatIdExam='';
				$strPatIdExam=implode(',', $arr);
				$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_external_exam` WHERE (`external_exam_summary` LIKE '%".$string."%' OR `sumOsEE` LIKE '%".$string."%') AND patient_id IN(".$strPatIdExam.")";
				$rs=imw_query($qry);	
				while($row=imw_fetch_assoc($rs)){
					if(isset($ssIds[$row['patient_id']]['selCriteria'])){
						$ssIds[$row['patient_id']]['selCriteria'] .= "External Exam, ";
					}else{
						$ssIds[$row['patient_id']]['selCriteria'] = "External Exam, ";
					}
					$arrAllPats[$row['patient_id']] = $row['patient_id'];
				}unset($rs);	
				
			}
		}else{
			$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_external_exam` WHERE (`external_exam_summary` LIKE '%".$string."%' OR `sumOsEE` LIKE '%".$string."%')";
			$rs=imw_query($qry);	
			while($row=imw_fetch_assoc($rs)){
				if(isset($ssIds[$row['patient_id']]['selCriteria'])){
					$ssIds[$row['patient_id']]['selCriteria'] .= "External Exam, ";
				}else{
					$ssIds[$row['patient_id']]['selCriteria'] = "External Exam, ";
				}
				$arrAllPats[$row['patient_id']] = $row['patient_id'];
			}unset($rs);
		}
		
		/*Query to `chart_la` Table*/
		$arr_la = array("chart_lids" => array('lid_conjunctiva_summary','sumLidsOs'), 
						"chart_lesion" => array('lesion_summary','sumLidPosOs'), 
						"chart_lid_pos" => array('lid_deformity_position_summary','sumLesionOs'), 
						"chart_lac_sys" => array('lacrimal_system_summary','sumLacOs')
						);
			foreach($arr_la as $tbl_la => $v_la){				
				if(count($arrAllPats)>0){
					$splitted_patientsLA = array_chunk($arrAllPats,1500);
					foreach($splitted_patientsLA as $arr){
						$strPatIdLA='';
						$strPatIdLA=implode(',', $arr);
						$qry= "SELECT DISTINCT(`patient_id`) FROM ".$tbl_la." WHERE (".$v_la[0]." LIKE '%".$string."%' OR ".$v_la[1]." LIKE '%".$string."%' ) AND patient_id IN(".$strPatIdLA.")";
						$rs=imw_query($qry);	
						while($row=imw_fetch_assoc($rs)){
							if(isset($ssIds[$row['patient_id']]['selCriteria'])){
								$ssIds[$row['patient_id']]['selCriteria'] .= "L&A, ";
							}else{
								$ssIds[$row['patient_id']]['selCriteria'] = "L&A, ";
							}
							$arrAllPats[$row['patient_id']] = $row['patient_id'];
						}unset($rs);
					}
				}else{
						$qry= "SELECT DISTINCT(`patient_id`) FROM ".$tbl_la." WHERE (".$v_la[0]." LIKE '%".$string."%' OR ".$v_la[1]." LIKE '%".$string."%' )";
						$rs=imw_query($qry);	
						while($row=imw_fetch_assoc($rs)){
							if(isset($ssIds[$row['patient_id']]['selCriteria'])){
								$ssIds[$row['patient_id']]['selCriteria'] .= "L&A, ";
							}else{
								$ssIds[$row['patient_id']]['selCriteria'] = "L&A, ";
							}
							$arrAllPats[$row['patient_id']] = $row['patient_id'];
						}unset($rs);
					}
			}
			
			
			/*Query to `chart_gonio` Table*/	
			if(count($arrAllPats)>0){
			$splitted_patientsGonio = array_chunk($arrAllPats,1500);
			foreach($splitted_patientsGonio as $arr){
				$strPatIdGonio='';
				$strPatIdGonio=implode(',', $arr);
				$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_gonio` WHERE (`gonio_od_summary` LIKE '%".$string."%' OR `gonio_os_summary` LIKE '%".$string."%') AND patient_id IN(".$strPatIdGonio.")";
				$rs=imw_query($qry);	
				while($row=imw_fetch_assoc($rs)){
					if(isset($ssIds[$row['patient_id']]['selCriteria'])){
						$ssIds[$row['patient_id']]['selCriteria'] .= "IOP/Gonioscopy, ";
					}else{
						$ssIds[$row['patient_id']]['selCriteria'] = "IOP/Gonioscopy, ";
					}
					$arrAllPats[$row['patient_id']] = $row['patient_id'];
				}unset($rs);	
				
			}
		}else{
			$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_gonio` WHERE (`gonio_od_summary` LIKE '%".$string."%' OR `gonio_os_summary` LIKE '%".$string."%')";
			$rs=imw_query($qry);	
			while($row=imw_fetch_assoc($rs)){
				if(isset($ssIds[$row['patient_id']]['selCriteria'])){
					$ssIds[$row['patient_id']]['selCriteria'] .= "IOP/Gonioscopy, ";
				}else{
					$ssIds[$row['patient_id']]['selCriteria'] = "IOP/Gonioscopy, ";
				}
				$arrAllPats[$row['patient_id']] = $row['patient_id'];
			}unset($rs);
		}
			
	
		/*Query to `chart_slit_lamp_exam` Table*/
		$arr_sle = array("chart_conjunctiva" => array('conjunctiva_od_summary','conjunctiva_os_summary'), 
						"chart_lesion" => array('cornea_od_summary','cornea_os_summary'), 
						"chart_ant_chamber" => array('anf_chamber_od_summary','anf_chamber_os_summary'), 
						"chart_iris" => array('iris_pupil_od_summary','iris_pupil_os_summary'), 
						"chart_lens" => array('lens_od_summary','lens_os_summary')
						);
		foreach($arr_sle as $tbl_sle => $v_sle){
			if(count($arrAllPats)>0){
			$splitted_patientsSLE = array_chunk($arrAllPats,1500);
				foreach($splitted_patientsSLE as $arr){
					$strPatIdSLE='';
					$strPatIdSLE=implode(',', $arr);
					$qry= "SELECT DISTINCT(`patient_id`) FROM ".$tbl_sle." WHERE (".$v_sle[0]." LIKE '%".$string."%' OR ".$v_sle[1]." LIKE '%".$string."%' ) AND patient_id IN(".$strPatIdSLE.")";
					$rs=imw_query($qry);	
					while($row=imw_fetch_assoc($rs)){
						if(isset($ssIds[$row['patient_id']]['selCriteria'])){
							$ssIds[$row['patient_id']]['selCriteria'] .= "SLE, ";
						}else{
							$ssIds[$row['patient_id']]['selCriteria'] = "SLE, ";
						}
						$arrAllPats[$row['patient_id']] = $row['patient_id'];
					}unset($rs);	
				}
			}else{
				$qry= "SELECT DISTINCT(`patient_id`) FROM ".$tbl_sle." WHERE (".$v_sle[0]." LIKE '%".$string."%' OR ".$v_sle[1]." LIKE '%".$string."%' )";
				$rs=imw_query($qry);	
				while($row=imw_fetch_assoc($rs)){
					if(isset($ssIds[$row['patient_id']]['selCriteria'])){
						$ssIds[$row['patient_id']]['selCriteria'] .= "SLE, ";
					}else{
						$ssIds[$row['patient_id']]['selCriteria'] = "SLE, ";
					}
					$arrAllPats[$row['patient_id']] = $row['patient_id'];
				}unset($rs);
			}
		}
			
		/*Query to `chart_optic` Table*/	
		if(count($arrAllPats)>0){
			$splitted_patientsOptic = array_chunk($arrAllPats,1500);
				foreach($splitted_patientsOptic as $arr){
					$strPatIdOptic='';
					$strPatIdOptic=implode(',', $arr);
					$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_optic` WHERE (`od_text` LIKE '%".trim(str_replace("C:D", "", $string))."%' OR `os_text` LIKE '%".trim(str_replace("C:D", "", $string))."%' OR `optic_nerve_od_summary` LIKE '%".$string."%' OR `optic_nerve_os_summary` LIKE '%".$string."%') AND patient_id IN(".$strPatIdOptic.")";
					$rs=imw_query($qry);	
					while($row=imw_fetch_assoc($rs)){
						if(isset($ssIds[$row['patient_id']]['selCriteria'])){
							$ssIds[$row['patient_id']]['selCriteria'] .= "Optic Nerve, ";
						}else{
							$ssIds[$row['patient_id']]['selCriteria'] = "Optic Nerve, ";
						}
						$arrAllPats[$row['patient_id']] = $row['patient_id'];
					}unset($rs);	
				}
			}else{
				$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_optic` WHERE (`od_text` LIKE '%".trim(str_replace("C:D", "", $string))."%' OR `os_text` LIKE '%".trim(str_replace("C:D", "", $string))."%' OR `optic_nerve_od_summary` LIKE '%".$string."%' OR `optic_nerve_os_summary` LIKE '%".$string."%')";
				$rs=imw_query($qry);	
				while($row=imw_fetch_assoc($rs)){
					if(isset($ssIds[$row['patient_id']]['selCriteria'])){
						$ssIds[$row['patient_id']]['selCriteria'] .= "Optic Nerve, ";
					}else{
						$ssIds[$row['patient_id']]['selCriteria'] = "Optic Nerve, ";
					}
				$arrAllPats[$row['patient_id']] = $row['patient_id'];
				}unset($rs);
			}
						
			/*Query to `chart_rv` Table*/
			$arr_rv = array("chart_vitreous" => array('vitreous_od_summary','vitreous_os_summary'), 
						"chart_retinal_exam" => array('retinal_od_summary','retinal_os_summary')						
						);
			foreach($arr_rv as $tbl_rv => $v_rv){
				if(count($arrAllPats)>0){
				$splitted_patientsRV = array_chunk($arrAllPats,1500);
					foreach($splitted_patientsRV as $arr){
						$strPatIdRV='';
						$strPatIdRV=implode(',', $arr);
						$qry= "SELECT DISTINCT(`patient_id`) FROM ".$tbl_rv." WHERE ('".$v_rv[0]."' LIKE '%".$string."%' OR '".$v_rv[1]."' LIKE '%".$string."%') AND patient_id IN(".$strPatIdRV.")";
						$rs=imw_query($qry);	
						while($row=imw_fetch_assoc($rs)){
							if(isset($ssIds[$row['patient_id']]['selCriteria'])){
								$ssIds[$row['patient_id']]['selCriteria'] .= "Retina & Vitreous, ";
							}else{
								$ssIds[$row['patient_id']]['selCriteria'] = "Retina & Vitreous, ";
							}
							$arrAllPats[$row['patient_id']] = $row['patient_id'];
						}unset($rs);	
					}
				}else{
					$qry= "SELECT DISTINCT(`patient_id`) FROM ".$tbl_rv." WHERE ('".$v_rv[0]."' LIKE '%".$string."%' OR '".$v_rv[1]."' LIKE '%".$string."%')";
					$rs=imw_query($qry);	
					while($row=imw_fetch_assoc($rs)){
						if(isset($ssIds[$row['patient_id']]['selCriteria'])){
							$ssIds[$row['patient_id']]['selCriteria'] .= "Retina & Vitreous, ";
						}else{
							$ssIds[$row['patient_id']]['selCriteria'] = "Retina & Vitreous, ";
						}
						$arrAllPats[$row['patient_id']] = $row['patient_id'];
					}unset($rs);
				}
			}
			
			/*Query to `chart_ref_surgery` Table*/
			$data = imw_query("SELECT DISTINCT(`patient_id`) FROM `chart_ref_surgery` WHERE (`sumOdRefSurg` LIKE '%".$string."%' OR `sumOsRefSurg` LIKE '%".$string."%') ".$qryWhere1);
			while($row = imw_fetch_assoc($data)){
				if(isset($ssIds[$row['patient_id']]['selCriteria'])){
					$ssIds[$row['patient_id']]['selCriteria'] .= "Refractive Surgery, ";
				}else{
					$ssIds[$row['patient_id']]['selCriteria'] = "Refractive Surgery, ";
				}
				$arrAllPats[$row['patient_id']] = $row['patient_id'];
			}
			
			
			
			/*Query to `chart_optic` Table*/	
		if(count($arrAllPats)>0){
			$splitted_patientsRF = array_chunk($arrAllPats,1500);
				foreach($splitted_patientsRF as $arr){
					$strPatIdRF='';
					$strPatIdRF=implode(',', $arr);
					$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_ref_surgery` WHERE (`sumOdRefSurg` LIKE '%".$string."%' OR `sumOsRefSurg` LIKE '%".$string."%') AND patient_id IN(".$strPatIdRF.")";
					$rs=imw_query($qry);	
					while($row=imw_fetch_assoc($rs)){
						if(isset($ssIds[$row['patient_id']]['selCriteria'])){
							$ssIds[$row['patient_id']]['selCriteria'] .= "Refractive Surgery, ";
						}else{
							$ssIds[$row['patient_id']]['selCriteria'] = "Refractive Surgery, ";
						}
						$arrAllPats[$row['patient_id']] = $row['patient_id'];
					}unset($rs);	
				}
			}else{
				$qry= "SELECT DISTINCT(`patient_id`) FROM `chart_ref_surgery` WHERE (`sumOdRefSurg` LIKE '%".$string."%' OR `sumOsRefSurg` LIKE '%".$string."%')";
				$rs=imw_query($qry);	
				while($row=imw_fetch_assoc($rs)){
					if(isset($ssIds[$row['patient_id']]['selCriteria'])){
						$ssIds[$row['patient_id']]['selCriteria'] .= "Refractive Surgery, ";
					}else{
						$ssIds[$row['patient_id']]['selCriteria'] = "Refractive Surgery, ";
					}
					$arrAllPats[$row['patient_id']] = $row['patient_id'];
				}unset($rs);
			}
			
			
			
			
		if(count($ssIds)>0){
			$searchString = 1;
		}
		
		if($prevIds==0 && $searchString==1){
			if(count($arrAllPats)>0){
			$splitted_patientIDs = array_chunk($arrAllPats,1500);
				foreach($splitted_patientIDs as $arr){
					$strPatIds='';
					$strPatIds=implode(',', $arr);
					$qry= "SELECT `patient_id`, `date_of_service` FROM (SELECT * FROM `chart_master_table` ORDER BY `update_date` DESC) AS `my_table` WHERE `patient_id` IN(".$strPatIds.") GROUP BY `patient_id`";
					$rs=imw_query($qry);	
					while($dt=imw_fetch_assoc($rs)){
						$ssIds[$dt['patient_id']]['dos']=$dt['date_of_service'];
						$ssIds[$dt['patient_id']]['selCriteria'] = trim(trim($ssIds[$dt['patient_id']]['selCriteria']),",");
					}unset($rs);
				}
			}
		}
		elseif($searchString==1){
			foreach($ssIds as $key => $val){
				$ssIds[$key]['selCriteria']=trim(trim($val['selCriteria']),",");
			}
		}
		/*
		$paging = "";
		$patients = count($arrAllPats);
		$pages = ceil($patients/$count);
		if(isset($_REQUEST['page'])){$page = $_REQUEST['page'];}
		else{$page = 1;}
		$next = $count;
		$prev = ($page-1)*$count;
		if($pages>1){
			$arrAllPats = array_slice($arrAllPats,$prev,$next,true);
			for($i=1;$i<=$pages;$i++){
				$cpage = "";
				if($page==$i){$cpage=" cpage";}
				$paging .= '<span class="paging'.$cpage.'" onclick="npage('.$i.')">'.$i.'</span>'; 
			}
		}
		*/
	if(count($arrAllPats)==0){
		$patient_id  = array();
		$flagMr = 0;
	}
}

//if "MR Given" is checked
$mrgiven = array();
$mrGiven = 0;
if(isset($_REQUEST['mrGiven']) && $_REQUEST['mrGiven']==1 && $flagMr==1){
	
	$paging = "";
	
	$str_selection_criteria .= "MR Given<br />";
	
	$mrGiven = $_REQUEST['mrGiven'];
	
	$where = $where1 = $date_search="";
	if(count($arrAllPats)>0){
		$where = " AND `cv`.`patient_id` IN(".implode(",", $arrAllPats).")";
		$where1 = " AND cv.patient_id` IN(".implode(",", $arrAllPats).")";
	}
	if($dtEffectiveDate!= "" && $dtDBEffectDate2!=""){
		$date_search= " AND (cm.date_of_service BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') ";
	}
	
	$arrAllPats = array();

	$sql = "SELECT COUNT(DISTINCT(cv.patient_id)) AS 'patients' FROM `chart_vis_master` cv  
	INNER JOIN `chart_master_table` `cm` ON (`cv`.`form_id`=`cm`.`id`) 
	INNER JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = cv.id
	WHERE c1.mr_none_given NOT IN('None Given', 'None', '') AND c1.ex_type='MR' ".$where1.$date_search;
	
	$patients = imw_query($sql);
	$patients = imw_fetch_assoc($patients);
	$patients = $patients['patients'];
	
	/*
	$pages = ceil($patients/$count);
	if(isset($_REQUEST['page'])){$page = $_REQUEST['page'];}
	else{$page = 1;}
	$next = $count;
	$prev = ($page-1)*$count;
	
	if($pages>1){
		for($i=1;$i<=$pages;$i++){
			$cpage = "";
			if($page==$i){$cpage=" cpage";}
			$paging .= '<span class="paging'.$cpage.'" onclick="npage('.$i.')">'.$i.'</span>'; 
		}
	}
    */
	$sql = "SELECT `cv`.`patient_id`, `cv`.`id`, 
	`c2`.sph as `vis_mr_od_s`, `c2`.cyl as `vis_mr_od_c`, `c2`.axs as `vis_mr_od_a`,
	`c3`.sph as `vis_mr_os_s`, `c3`.cyl as `vis_mr_os_c`, `c3`.axs as `vis_mr_os_a`, 
	`cm`.`date_of_service` FROM `chart_vis_master` `cv` 
	INNER JOIN `chart_master_table` `cm` ON (`cv`.`form_id`=`cm`.`id`) 
	INNER JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = cv.id
	LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
	LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
	WHERE `c1`.`mr_none_given` NOT IN('None Given', 'None', '') AND c1.ex_type='MR' AND c1.`ex_number`='1' ".$where.$date_search."
	GROUP BY `cv`.`patient_id` ORDER BY `cm`.`date_of_service` DESC  ";
	
	$data = imw_query($sql);
	while($row = imw_fetch_assoc($data)){

		$row['vis_mr_od_s'] = ($row['vis_mr_od_s'] == NULL || $row['vis_mr_od_s'] == 'NULL')?$row['vis_mr_od_s']='':$row['vis_mr_od_s'];
		$row['vis_mr_od_c'] = ($row['vis_mr_od_c'] == NULL || $row['vis_mr_od_c'] == 'NULL')?$row['vis_mr_od_c']='':$row['vis_mr_od_c'];
		$row['vis_mr_od_a'] = ($row['vis_mr_od_a'] == NULL || $row['vis_mr_od_a'] == 'NULL')?$row['vis_mr_od_a']='':$row['vis_mr_od_a'];
		$row['vis_mr_os_s'] = ($row['vis_mr_os_s'] == NULL || $row['vis_mr_os_s'] == 'NULL')?$row['vis_mr_os_s']='':$row['vis_mr_os_s'];
		$row['vis_mr_os_c'] = ($row['vis_mr_os_c'] == NULL || $row['vis_mr_os_c'] == 'NULL')?$row['vis_mr_os_c']='':$row['vis_mr_os_c'];
		$row['vis_mr_os_a'] = ($row['vis_mr_os_a'] == NULL || $row['vis_mr_os_a'] == 'NULL')?$row['vis_mr_os_a']='':$row['vis_mr_os_a'];
		
		$arrAllPats[$row['patient_id']] = $row['patient_id'];
		$mrgiven[$row['patient_id']]['dos'] = $row['date_of_service'];
		$mrgiven[$row['patient_id']]['od'] = $row['vis_mr_od_s']."/".$row['vis_mr_od_c']."/".$row['vis_mr_od_a'];
		$mrgiven[$row['patient_id']]['os'] = $row['vis_mr_os_s']."/".$row['vis_mr_os_c']."/".$row['vis_mr_os_a'];
	}
	
	if(count($arrAllPats)==0){
		$patient_id  = array();
	}
}
/*End Modification By Pankaj Raturi*/

//saving this report
$query_string = "dxcodes=".$_REQUEST["dxcodes"]."&dxcodes10=".$_REQUEST["dxcodes10"]."&proc_codes=".$proc_codes_imp."&tests=".$_REQUEST["tests"]."&medications=".$_REQUEST["medications"]."&cd_ratio=".$_REQUEST["cd_ratio"]."&iop_criteria=".$_REQUEST["iop_criteria"]."&iop_pressure=".$_REQUEST["iop_pressure"]."&immunizations=".$_REQUEST["immunizations"]."&inc_exc_condition=".$_REQUEST["inc_exc_condition"]."&zip=".$_REQUEST["zip"]."&state=".$_REQUEST["state"]."&city=".$_REQUEST["city"]."&ethnicity=".$_REQUEST["ethnicity"]."&race=".$_REQUEST["race"]."&weight_criteria=".$_REQUEST["weight_criteria"]."&weight_val=".$_REQUEST["weight_val"]."&height_criteria=".$_REQUEST["height_criteria"]."&height_val=".$_REQUEST["height_val"]."&age_criteria=".$_REQUEST["age_criteria"]."&age_val=".$_REQUEST["age_val"]."&hippa_compliant=".$_REQUEST["hippa_compliant"]."&rd_problem_list=".$_REQUEST["rd_problem_list"]."&rd_med_list=".$_REQUEST["rd_med_list"]."&rd_demographics=".$_REQUEST["rd_demographics"]."&rd_lab_results=".$_REQUEST["rd_lab_results"]."&rd_ht_wt=".$_REQUEST["rd_ht_wt"]."&rd_imm_list=".$_REQUEST["rd_imm_list"]."&rd_cholesterol=".$_REQUEST["rd_cholesterol"]."&tot_criteria=".$_REQUEST["tot_criteria"]."&tot_value=".$_REQUEST["tot_value"]."&ldl_criteria=".$_REQUEST["ldl_criteria"]."&ldl_value=".$_REQUEST["ldl_value"]."&hdl_criteria=".$_REQUEST["hdl_criteria"]."&hdl_value=".$_REQUEST["hdl_value"]."&trig_criteria=".$_REQUEST["trig_criteria"]."&trig_value=".$_REQUEST["trig_value"]."&cond_cholesterol=".$_REQUEST["cond_cholesterol"]."&eff_date=".$_REQUEST["eff_date"]."&eff_date2=".$_REQUEST["eff_date2"]."&and_or".$_REQUEST["and_or"]."&physicians".$_REQUEST["physicians"];
$query_string.= "&language=".$_REQUEST["language"]."&hippa_mail=".$_REQUEST["hippa_mail"]."&hippa_email=".$_REQUEST["hippa_email"]."&hippa_voice=".$_REQUEST["hippa_voice"]."&voiceType=".$_REQUEST["voiceType"]."&suitableTime=".$_REQUEST["suitableTime"]."&and_or=".$_REQUEST['and_or']."&searchString=".$_REQUEST['searchString']."&mrGiven=".$_REQUEST['mrGiven']; 
save_report_type($_REQUEST["save_report_name"],$query_string);

$str_selection_criteria_html = "<u class=\"text_10b\">Selection Criteria</u>:  ".$str_selection_criteria;
$str_selection_condition_html = "<u class=\"text_10b\">Inclusion/Exclusion of Condition</u>:  ".$included_excluded;


//main query for report
if((is_array($patient_id) && count($patient_id) > 0) || $demographics==1 || (($mrGiven==1 || $searchString==1) && count($arrAllPats)>0)){
	$new_patient_id = array(); //echo ' '.count($patient_id).'@@'.$demographics.'@@'.$mrGiven.'@@'.$searchString.'@@'.count($arrAllPats);
	$j = 0;
	
	//for($k = 0; $k < count($patient_id); $k++){
		$bl_process = true;

		$strAllPats='';
		$qryWhere='';
		if(count($arrAllPats)>0){
			$strAllPats=implode(',', $arrAllPats);
			$qryWhere=" AND pd.pid IN(".$strAllPats.")";
		}
		$main_qry = "Select pid, ROUND(((DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'),pd.dob))/365), 0) as age, pd.reportExemption, pd.fname, pd.lname, pd.mname, pd.title, pd.suffix, pd.id, date_format(pd.dob,'".$dateFormat."') as dob, pd.street, pd.street2, pd.postal_code, pd.state, pd.city, pd.phone_home, pd.ethnicity, pd.otherEthnicity, pd.race, pd.otherRace,  pd.providerID,
					pd.language, pd.hipaa_mail, pd.hipaa_email, pd.hipaa_voice, pd.preferr_contact, pd.sex, pd.email   
					FROM patient_data pd 
					WHERE 1=1 ".$qryWhere." ORDER BY pd.lname, pd.fname";//echo " ";
					//echo '<br>'.$main_qry;
		$main_res = imw_query($main_qry);
	if(imw_num_rows($main_res) > 0){
			
			while($main_row = imw_fetch_array($main_res)){
			
			$bl_process = true;				
			$pid = $main_row['pid'];

			if(isset($_REQUEST["zip"]) && trim($_REQUEST["zip"]) != ""){
				if($_REQUEST["rd_demographics"] == "1"){
					if(trim($_REQUEST["zip"]) != $main_row["postal_code"]){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if(trim($_REQUEST["zip"]) == $main_row["postal_code"]){
						$bl_process = false;
					}
				}
			}

			if(isset($_REQUEST["state"]) && trim($_REQUEST["state"]) != ""){
				if($_REQUEST["rd_demographics"] == "1"){
					if(trim($_REQUEST["state"]) != $main_row["state"]){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if(trim($_REQUEST["state"]) == $main_row["state"]){
						$bl_process = false;	
					}
				}
			}
			
			if(isset($_REQUEST["city"]) && trim($_REQUEST["city"]) != ""){
				if($_REQUEST["rd_demographics"] == "1"){
					if(trim($_REQUEST["city"]) != $main_row["city"]){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if(trim($_REQUEST["city"]) == $main_row["city"]){
						$bl_process = false;	
					}	
				}
			}

			if(isset($_REQUEST["sex"]) && trim($_REQUEST["sex"]) != ""){
				if($_REQUEST["rd_demographics"] == "1"){
					if(strtolower($_REQUEST["sex"]) != strtolower($main_row["sex"])){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if(strtolower($_REQUEST["sex"]) == strtolower($main_row["sex"])){
						$bl_process = false;	
					}	
				}
			}

			if(isset($_REQUEST["ethnicity"]) && trim($_REQUEST["ethnicity"]) != ""){
				$arrEthnicity=explode(',',$main_row["ethnicity"]);
				$disp=0;
				if($_REQUEST["other_ethnicity"]!=''){
					if(trim($_REQUEST["other_ethnicity"])== $main_row["otherEthnicity"]){
						$disp=1;
					}
				}
				if($disp=='0'){
					if(in_array('Other', $arrEthnicity) && $_REQUEST["other_ethnicity"]!=''){
						if(array_intersect($arrEthWithoutOther, $arrEthnicity)){
							$disp=1;
						}
					}else{
						if(array_intersect($arrSelEthnicity, $arrEthnicity)){
							$disp=1;
						}
					}
				}

				if($_REQUEST["rd_demographics"] == "1"){
					if($disp=='0'){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if($disp=='1'){
						$bl_process = false;
					}
				}
			}

			if(isset($_REQUEST["race"]) && trim($_REQUEST["race"]) != ""){
				$arrRace=explode(',',$main_row["race"]);
				$disp=0;

				if($_REQUEST["other_race"]!=''){
					if(trim($_REQUEST["other_race"])== $main_row["otherRace"]){
						$disp=1;
					}
				}
				if($disp=='0'){
					if(in_array('Other', $arrRace) && $_REQUEST["other_race"]!=''){
						if(array_intersect($arrRaceWithoutOther, $arrRace)){
							$disp=1;
						}
					}else{
						if(array_intersect($arrSelRace, $arrRace)){
							$disp=1;
						}
					}
				}

				if($_REQUEST["rd_demographics"] == "1"){
					if($disp=='0'){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if($disp=='1'){
						$bl_process = false;
					}
				}
			}


			if(isset($_REQUEST["language"]) && trim($_REQUEST["language"]) != ""){
				$disp=0;

				if($_REQUEST["other_language"]!=''){
					if(preg_match("/\b".$_REQUEST["other_language"]."\b/i", $main_row["language"])){	
						$disp=1;
					}
				}
				if($disp=='0'){
					if(in_array('Other', $arrSelLanguage) && $_REQUEST["other_language"]==''){
						if(preg_match("/\bOther\b/i", $main_row["language"])){	
							$disp=1;
						}
					}
					if(in_array($main_row["language"], $arrSelLanguage)){
						$disp=1;
					}
				}

				if($_REQUEST["rd_demographics"] == "1"){
					if($disp=='0'){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if($disp=='1'){
						$bl_process = false;
					}
				}
			}

			if(isset($_REQUEST["hippa_mail"]) && trim($_REQUEST["hippa_mail"]) != "0"){
				if($_REQUEST["rd_demographics"] == "1"){
					if(trim($_REQUEST["hippa_mail"]) != $main_row["hipaa_mail"]){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if(trim($_REQUEST["hippa_mail"]) == $main_row["hipaa_mail"]){
						$bl_process = false;
					}
				}
			}
			if(isset($_REQUEST["hippa_email"]) && trim($_REQUEST["hippa_email"]) != "0"){
				if($_REQUEST["rd_demographics"] == "1"){
					if(trim($_REQUEST["hippa_email"]) != $main_row["hipaa_email"]){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if(trim($_REQUEST["hippa_email"]) == $main_row["hipaa_email"]){
						$bl_process = false;
					}
				}
			}
			if(isset($_REQUEST["hippa_voice"]) && trim($_REQUEST["hippa_voice"]) != "0"){
				if($_REQUEST["rd_demographics"] == "1"){
					if(trim($_REQUEST["hippa_voice"]) != $main_row["hipaa_voice"]){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if(trim($_REQUEST["hippa_voice"]) == $main_row["hipaa_voice"]){
						$bl_process = false;
					}
				}
			}	

			if(isset($_REQUEST["voiceType"]) && trim($_REQUEST["voiceType"]) != ""){
				if($_REQUEST["rd_demographics"] == "1"){
					if(trim($_REQUEST["voiceType"]) != $main_row["preferr_contact"]){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if(trim($_REQUEST["voiceType"]) == $main_row["preferr_contact"]){
						$bl_process = false;
					}
				}
			}	

			if($suitTimeFrom!='' || $suitTimeTo!=''){
				//GET SUITABLE CALL TIMMINGS
				$recExist=0;
				$timeRs=imw_query("Select id, time_from, time_to FROM patient_call_timmings 
				WHERE patient_id='".$pid."' AND del_status='0'");
				while($timeRes=imw_fetch_array($timeRs)){
					if($suitTimeFrom < $timeRes["time_to"] && $suitTimeTo > $timeRes["time_from"]){
						$recExist = 1;
					}
				}

				if($recExist=='1'){
					if($_REQUEST["rd_demographics"] == "0"){
						$bl_process = false;
					}
				}else if($recExist=='0'){
					if($_REQUEST["rd_demographics"] == "1"){
						$bl_process = false;
					}
				}
			}										
						
			if(isset($_REQUEST["age_val"]) && trim($_REQUEST["age_val"]) != ""){
				if($_REQUEST["rd_demographics"] == "1"){
					if($_REQUEST["age_criteria"] == "equalsto"){	
						//echo round(trim($_REQUEST["age_val"]),0)." == ".$main_row["age"]."<br>";		
						if(round(trim($_REQUEST["age_val"]),0) == $main_row["age"]){
							//null
						}else{
							$bl_process = false;
							//echo "f";
						}
					}else if($_REQUEST["age_criteria"] == "less"){
						if(round(trim($_REQUEST["age_val"]),0) > $main_row["age"]){
							//null
						}else{
							$bl_process = false;
							//echo "g";
						}
					}else if($_REQUEST["age_criteria"] == "greater"){
						if(round(trim($_REQUEST["age_val"]),0) < $main_row["age"]){
							//null
						}else{
							$bl_process = false;
							//echo "h";
						}
					}
					else if($_REQUEST["age_criteria"] == "greater_equal"){
						if(round(trim($_REQUEST["age_val"]),0) < $main_row["age"]){
							//null
						}else{
							$bl_process = false;
							//echo "h";
						}
					}
					else if($_REQUEST["age_criteria"] == "less_equal"){
						if(round(trim($_REQUEST["age_val"]),0) > $main_row["age"]){
							//null
						}else{
							$bl_process = false;
							//echo "h";
						}
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if($_REQUEST["age_criteria"] == "equalsto"){	
						//echo round(trim($_REQUEST["age_val"]),0)." == ".$main_row["age"]."<br>";		
						if(round(trim($_REQUEST["age_val"]),0) == $main_row["age"]){
							$bl_process = false;
						}else{
							//null
						}
					}else if($_REQUEST["age_criteria"] == "less"){
						if(round(trim($_REQUEST["age_val"]),0) > $main_row["age"]){
							$bl_process = false;
						}else{
							//null
						}
					}else if($_REQUEST["age_criteria"] == "greater"){
						if(round(trim($_REQUEST["age_val"]),0) < $main_row["age"]){
							$bl_process = false;
						}else{
							//null
						}
					}
				}
			}
			
			if(empty($main_row["providerID"]) == true){
				$schRs = imw_query("select sa_doctor_id from schedule_appointments
						where sa_patient_id = '".$main_row["id"]."' and sa_app_start_date <= now()
						order by sa_app_start_date desc, sa_app_starttime desc limit 0, 1"); 
				$schRes=imw_fetch_array($schRs);
				$main_row["providerID"]= $schRes['sa_doctor_id'];
			}
			if(isset($_REQUEST["physicians"]) && trim($_REQUEST["physicians"]) != ""){
				if($_REQUEST["rd_demographics"] == "1"){
					if($arrSelPhysicians[$main_row["providerID"]] != $main_row["providerID"]){
						$bl_process = false;
					}
				}else if($_REQUEST["rd_demographics"] == "0"){
					if($arrSelPhysicians[$main_row["providerID"]] == $main_row["providerID"]){
						$bl_process = false;	
					}
				}
			}


			if($bl_process == true){

				if(trim($main_row["lname"]) != "" && trim($main_row["fname"]) != ""){
					$pt_name_to_show = trim($main_row["lname"]).", ".trim($main_row["fname"]);
				}else if(trim($main_row["lname"]) != ""){
					$pt_name_to_show = trim($main_row["lname"]);
				}else if(trim($main_row["fname"]) != ""){
					$pt_name_to_show = trim($main_row["fname"]);
				}
				if(trim($main_row["mname"])!=""){
					$pt_name_to_show .= " ".substr(trim($main_row["mname"]), 0, 1).".";
				}
				
				$new_patient_id[$j]["id"] = $pid;
				
				if($prevIds==0 && $searchString==1){
					$new_patient_id[$j]["dos"] = $ssIds[$pid]["dos"];
				}else{
					$new_patient_id[$j]["dos"] = $patient_id[$pid]["dos"];
				}
				
				//$ssIds[$dt['patient_id']]['dos']=$dt['date_of_service'];
				//$ssIds[$dt['patient_id']]['selCriteria'] = trim(trim($ssIds[$dt['patient_id']]['selCriteria']),",");
				
				if($searchString==1){
					$new_patient_id[$j]["clinical_act"] = trim($patient_id[$pid]["clinical_act"].", ".$ssIds[$pid]['selCriteria'],",");
				}else{
					$new_patient_id[$j]["clinical_act"] = $patient_id[$pid]["clinical_act"];
					$new_patient_id[$j]["clinical_act_cpt"] = $patient_id[$pid]["clinical_act_cpt"];
				}
				
				
				$new_patient_id[$j]["name"] = $pt_name_to_show;
				$new_patient_id[$j]["dob"] = ($main_row["dob"] != "00/00/00") ? $main_row["dob"] : "N/A";
				$new_patient_id[$j]["age"] = $main_row["age"];
				$new_patient_id[$j]["address"] = trim($main_row["street"]." ".$main_row["street2"].", ".$main_row["city"]." ".$main_row["state"]." ".$main_row["postal_code"]);
				$new_patient_id[$j]["phone"] = trim($main_row["phone_home"]." ".$main_row["phone_biz"]." ".$main_row["phone_cell"]);
				$new_patient_id[$j]["email"] = $main_row["email"];
			}
			$j++;
		}// END WHILE
	}
	
	//}
}

//print "<pre>";
//print_r($new_patient_id);
//print_r($consult_patient_id_arr);
//START CODE FOR DIABETIC EXAM
$new_patient_exists = false;
foreach($new_patient_id as $npt) {
	if(trim($npt)) { $new_patient_exists = true;}
}

//start sort br red color then green color
$consult_patient_id_main_arr = array();
foreach($consult_patient_id_arr as $cp_key => $cp_val) {
	$cp_tmplt_nme = strtolower($consultFormArr[$cp_key]["pt_tmplte_name"]);
	if(stristr($cp_tmplt_nme,"red alert")) {
		$consult_patient_id_main_arr[$cp_key] = $cp_val;	
	}
}
foreach($consult_patient_id_arr as $cp_key => $cp_val) {
	$cp_tmplt_nme = strtolower($consultFormArr[$cp_key]["pt_tmplte_name"]);
	if(stristr($cp_tmplt_nme,"green alert")) {
		$consult_patient_id_main_arr[$cp_key] = $cp_val;	
	}
}
//end sort br red color then green color
//$consultFormArr[$consult_form_id]["pt_diab_color"]

$consult_patient_id_new_arr = array();
foreach($consult_patient_id_main_arr as $consult_patient_key => $consult_patient_val) {
	foreach($new_patient_id as $np_key => $np_val) {
		if($new_patient_id[$np_key]["id"] == $consult_patient_val) {
			$consult_patient_id_new_arr[$consult_patient_key] = $consult_patient_val;	
		}
	}
}
if(count($consult_patient_id_new_arr)==0 && $new_patient_exists == false) {
	$consult_patient_id_new_arr = $consult_patient_id_main_arr;	
}
//print_r($consult_patient_id_new_arr);

//Start get physicians of diabetic exam
$consult_providerId_new_arr = array();
foreach($consult_patient_id_new_arr as $nr_key => $nr_val) {
	$consult_providerId_new_arr[$nr_key] = $consult_providerId_arr[$nr_key];
}
$consult_providerId_uniq_arr = array_unique($consult_providerId_new_arr);

//End get physicians of diabetic exam

//END CODE FOR DIABETIC EXAM


//MAKING OUTPUT DATA
$file_name="clinical_report.csv";
$csv_file_name= write_html("", $file_name);

if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$arr=array();
$arr[]="Clinical Report";
$arr[]="Date :" .$dtShowEffectDate." To :" .$dtShowEffectDate2;
$arr[]="Created by" .$createdBy." on " .$curDate;
fputcsv($fp,$arr, ",","\"");

$str_html_final_head.="<table class=\"rpt rpt_table rpt_table-bordered rpt_padding\" style='width:100%;'>
        <tbody><tr class=\"rpt_headers\">
            <td class=\"rptbx1\" style=\"width:342px;\">Clinical Report</td>
            <td class=\"rptbx2\" style=\"width:350px;\">&nbsp;Date: From $dtShowEffectDate To $dtShowEffectDate2</td>
            <td class=\"rptbx3\" style=\"width:350px;\">&nbsp;Created by $createdBy on $curDate &nbsp;</td>
        </tr>
    </tbody></table>";
//pre($new_patient_id);
if(is_array($new_patient_id) && count($new_patient_id) > 0 && (!trim($diabetic_exam) && $phyNewExists == 0)){
	if($mrGiven==1){
		$str_html = "<table class=\"rpt rpt_table rpt_table-bordered rpt_padding sortable\" style='width:100%;'  id=\"ajax_res_tbl\">
            <tr>
				<td style='width:262px;' align=\"center\" class=\"text_b_w\">Patient Name-ID</td>
				<td style='width:262px;' align=\"center\" class=\"text_b_w\">DOS(MR Given)</td>
				<td style='width:262px;' align=\"center\" class=\"text_b_w\">OD</td>
				<td style='width:262px;' align=\"center\"  class=\"text_b_w\">OS</td>
				
			</tr>";
		$arr=array();	
		$arr[]="Patient Name-ID";
		$arr[]="DOS(MR Given)";
		$arr[]="OD";
		$arr[]="OS";
		fputcsv($fp,$arr, ",","\"");		
	}
	else{
		$str_html = "<table class=\"rpt rpt_table rpt_table-bordered rpt_padding sortable\" style='width:100%;'  id=\"ajax_res_tbl\">
					<tr>
						<td width=\"180\" align=\"center\" class=\"text_b_w\">Patient Name-ID</td>
						<td width=\"160\" align=\"center\" class=\"text_b_w\">Selection Criteria</td>
						<td width=\"120\"  align=\"center\"  class=\"text_b_w\">DOB(Age)</td>
						<td width=\"160\"  align=\"center\" class=\"text_b_w\">Address</td>
						<td width=\"100\"  align=\"center\" class=\"text_b_w\">Phone</td>
						<td width=\"100\"  align=\"center\" class=\"text_b_w\">Email-ID</td>
						<td width=\"80\"  align=\"center\" class=\"text_b_w\">Last DOS</td>
					</tr>";
		$arr=array();	
		$arr[]="Patient Name-ID";
		$arr[]="Selection Criteria";
		$arr[]="DOB(Age)";
		$arr[]="Address";
		$arr[]="Phone";
		$arr[]="Email-ID";
		$arr[]="Last DOS";
		fputcsv($fp,$arr, ",","\"");			
	}
	$cnt = 1;
	foreach($new_patient_id as $main_row){
		//print "<pre>";
		//print_r($main_row);
		$class = "";
		if($cnt % 2 == 0){
			$class = "bgcolor";
		}

		//patient DOS
		$pat_dos = "N/A";
		if($main_row["dos"] != "" && $main_row["dos"] != "0000-00-00"){
			$arr_dos = explode("-", $main_row["dos"]);
			$ts_dos = mktime(0,0,0,$arr_dos[1],$arr_dos[2],$arr_dos[0]);
			$pat_dos = date($phpDateSlash,$ts_dos);
		}
		
        $clini_act = $main_row["clinical_act"];
		$clini_act_cpt = $main_row["clinical_act_cpt"];
        $clini_act = $clini_act ? $clini_act.', '.$clini_act_cpt : $clini_act_cpt;
		$clini_act = str_ireplace(", , ","",$clini_act);
        //$clini_act =  wordwrap($clini_act, 15, '<br>',true);
        
		if(isset($_REQUEST["hippa_compliant"]) && $_REQUEST["hippa_compliant"] == "yes" && (!trim($diabetic_exam) && $phyNewExists == 1)){
			$str_html .= "	<tr>
							<td width=\"180\" class=\"\" align=\"left\">".$main_row["name"]."&nbsp;-&nbsp;".$main_row["id"]."</td>
							<td width=\"160\" class=\"\" align=\"left\">".$clini_act."</td>
							<td width=\"120\" class=\"\" align=\"center\">Not Shown</td>
							<td width=\"160\" class=\"\" align=\"left\">Not Shown</td>
							<td width=\"100\" class=\"\" align=\"left\">Not Shown</td>
							<td width=\"100\" class=\"\" align=\"left\">Not Shown</td>
							<td width=\"80\" class=\"\" align=\"left\">".$pat_dos."</td>
						</tr>
						";
						$arr=array();	
						$arr[]=$main_row["name"]." - ".$main_row["id"];
						$arr[]=$clini_act;
						$arr[]="Not Shown";
						$arr[]="Not Shown";
						$arr[]="Not Shown";
						$arr[]="Not Shown";
						$arr[]=$pat_dos;
						fputcsv($fp,$arr, ",","\"");				
			$arr_data[$cnt-1]["patient_name_id"] = $main_row["name"]."&nbsp;-&nbsp;".$main_row["id"];
			$arr_data[$cnt-1]["patient_dob"] = "Not Shown";
			$arr_data[$cnt-1]["patient_address"] = "Not Shown";
			$arr_data[$cnt-1]["patient_phone"] = "Not Shown";
			$arr_data[$cnt-1]["patient_dos"] = $pat_dos;
		} elseif($mrGiven==1){
			$str_html .= "	<tr>
							<td align=\"left\">".$main_row["name"]."&nbsp;-&nbsp;".$main_row["id"]."</td>
							<td align=\"left\">".$mrgiven[$main_row["id"]]["dos"]."</td>
							<td align=\"center\">".$mrgiven[$main_row["id"]]["od"]."</td>
							<td align=\"center\">".$mrgiven[$main_row["id"]]["os"]."</td>
						</tr>
						";
						$arr=array();	
						$arr[]=$main_row["name"]." - ".$main_row["id"];
						$arr[]=$mrgiven[$main_row["id"]]["dos"];
						$arr[]=$mrgiven[$main_row["id"]]["od"];
						$arr[]=$mrgiven[$main_row["id"]]["os"];
						fputcsv($fp,$arr, ",","\"");
			$arr_data[$cnt-1]["patient_name_id"] = $main_row["name"]."&nbsp;-&nbsp;".$main_row["id"];
			$arr_data[$cnt-1]["patient_dob"] = "Not Shown";
			$arr_data[$cnt-1]["patient_address"] = "Not Shown";
			$arr_data[$cnt-1]["patient_phone"] = "Not Shown";
			$arr_data[$cnt-1]["patient_dos"] = $mrgiven[$main_row["id"]]["dos"];
		}
		else{
			$str_html .= "	<tr>
							<td width=\"180\" class=\"\" align=\"left\">".$main_row["name"]."&nbsp;-&nbsp;".$main_row["id"]."</td>
							<td width=\"160\" class=\"\" align=\"left\">".$clini_act."</td>
							<td width=\"120\" class=\"\" align=\"left\">".$main_row["dob"]."&nbsp;(".$main_row["age"].")"."</td>
							<td width=\"160\" class=\"\" align=\"left\">".$main_row["address"]."</td>
							<td width=\"100\" class=\"\" align=\"left\">".core_phone_format($main_row["phone"])."</td>
							<td width=\"100\" class=\"\" align=\"left\">".$main_row["email"]."</td>
							<td width=\"80\" class=\"\" align=\"left\">".$pat_dos."</td>
						</tr>
						";
						$arr=array();	
						$arr[]=$main_row["name"]." - ".$main_row["id"];
						$arr[]=$clini_act;
						$arr[]=$main_row["dob"]." (".$main_row["age"].").";
						$arr[]=$main_row["address"];
						$arr[]=core_phone_format($main_row["phone"]);
						$arr[]=$main_row["email"];
						$arr[]=$pat_dos;
						fputcsv($fp,$arr, ",","\"");
			$arr_data[$cnt-1]["patient_name_id"] = $main_row["name"]."&nbsp;-&nbsp;".$main_row["id"];
			$arr_data[$cnt-1]["patient_dob"] = $main_row["dob"]."&nbsp;(".$main_row["age"].")";
			$arr_data[$cnt-1]["patient_address"] = $main_row["address"];
			$arr_data[$cnt-1]["patient_phone"] = core_phone_format($main_row["phone"]);
			$arr_data[$cnt-1]["patient_dos"] = $pat_dos;
		}
		$cnt++;
	}
	$str_html .= "</table>";
			
	$str_html_final = $str_html;
//	$str_html_final .= "<form name=\"frm_more_options\" method=\"post\" action=\"more_options.php\" target=\"_blank\"><input type=\"hidden\" id=\"serialized_output\" name=\"serialized_output\" value=\"".urlencode(htmlentities(addslashes($str_html)))."\"><input type=\"hidden\" id=\"serialized_data\" name=\"serialized_data\" value=\"".urlencode(htmlentities(serialize($arr_data)))."\">
//	<input type=\"hidden\" id=\"serialized_condition\" name=\"serialized_condition\" value=\"".urlencode(htmlentities(addslashes($str_selection_criteria_html)))."\">
//	<input type=\"hidden\" id=\"serialized_cond_criteria\" name=\"serialized_cond_criteria\" value=\"".urlencode(htmlentities(addslashes($str_selection_condition_html)))."\"><input type=\"hidden\" id=\"serialized_action\" name=\"serialized_action\" value=\"\"></form>";
	
    //$str_html_final."<><><>"."yes"."<><><>".$str_selection_criteria_html."<><><>".$str_selection_condition_html."<><><>".$paging;;
    $str_html_final=$str_html_final_head.$str_html_final;

}else if(trim($diabetic_exam) && count($consult_providerId_uniq_arr)>0){
		$str_html = "<table class=\"rpt rpt_table rpt_table-bordered rpt_padding sortable\" style='width:100%;'  id=\"ajax_res_tbl\">
			<tr>
				<td width=\"30\"   align=\"left\" class=\"text_b_w\" >&nbsp;</td>
				<td width=\"180\"  align=\"left\" class=\"text_b_w\" >Patient Name-ID</td>
				<td width=\"120\"   align=\"left\" class=\"text_b_w\" >DOB</td>
				<td width=\"60\"   align=\"left\" class=\"text_b_w\" >C:D</td>
				<td width=\"100\"  align=\"left\"  class=\"text_b_w\" >IOP</td>
				<td width=\"180\"  align=\"left\"  class=\"text_b_w\" >Dx Code</td>
				<td width=\"200\"  align=\"left\"  class=\"text_b_w\" >Insurance Plan - Plan#</td>
				<td width=\"140\"  align=\"left\"  class=\"text_b_w\" >PCP</td>
			</tr>";
			$arr=array();	
			$arr[]="Patient Name-ID";
			$arr[]="DOB";
			$arr[]="C:D";
			$arr[]="IOP";
			$arr[]="Dx Code";
			$arr[]="Insurance Plan - Plan#";
			$arr[]="PCP";
			fputcsv($fp,$arr, ",","\"");
	//START CODE TO DISPLAY DIABETIC EXAM
	foreach($consult_providerId_uniq_arr as $consult_user_id) {
		$usrName = $consult_provider_name_arr[$consult_user_id];
		$str_html .= "	<tr>
						<td colspan=\"8\"  class=\"text_b_w\" align=\"left\" style=\"background:#4684ab;\"><input type=\"checkbox\" onclick=\"checkAllChkBox(this,'$consult_user_id')\" />&nbsp;Physician: $usrName</td>
						</tr>
					";
					
		foreach($consult_patient_id_new_arr as $consult_form_id_key => $consult_patient_id_new) {
			
			if($consult_providerId_arr[$consult_form_id_key] == $consult_user_id) {
				
				$consult_pt_name 		= $consultPtArr[$consult_patient_id_new]["pt_name"];
				$pt_name_id 			= $consult_pt_name."&nbsp;-&nbsp;".$consult_patient_id_new;
				$consult_pt_dob 		= $consultPtArr[$consult_patient_id_new]["pt_dob"];
				$consult_pt_age 		= $consultPtArr[$consult_patient_id_new]["pt_age"];
				$consult_pt_pcp 		= $consultPtArr[$consult_patient_id_new]["pt_pcp"];
				$consult_pt_pcp_id 		= $consultPtArr[$consult_patient_id_new]["pt_pcp_id"];
				$consult_pt_pcp_fax_no	= $consultPtArr[$consult_patient_id_new]["pt_pcp_fax_no"];
				$consult_pt_pcp_email	= $consultPtArr[$consult_patient_id_new]["pt_pcp_email"];
				
				$consult_pt_color 		= $consultFormArr[$consult_form_id_key]["pt_diab_color"];
				$consult_pt_cd_ratio	= $consultFormArr[$consult_form_id_key]["pt_cd_ratio"];	
				$consult_pt_iop 		= $consultFormArr[$consult_form_id_key]["pt_iop"];
				$consult_pt_chart_dx	= $consultFormArr[$consult_form_id_key]["pt_chart_dx"];
				$consult_pt_ins_plan	= $consultFormArr[$consult_form_id_key]["pt_ins_plan"];
				$consult_pt_consult_id	= $consultFormArr[$consult_form_id_key]["pt_consult_id"];
				
				$str_html .= "	<tr >
								<td width=\"30\"  class=\"text_10\"	align=\"left\" style=\" vertical-align:top; white-space:nowrap;\"><input type=\"checkbox\" name=\"chbx_diab_exam[]\" id=\"chbx_diab_exam_$consult_pt_consult_id\" class=\"chk_box_$consult_user_id\" value=\"$consult_pt_consult_id\"><span style=\"background:$consult_pt_color;border-radius:10px; padding-left:10px; margin-left:5px; height:1px;width:1px;\">&nbsp;</span></td>
								<td width=\"180\" class=\"text_10\" align=\"left\" style=\" vertical-align:top;\">".$pt_name_id."<input type=\"hidden\" name=\"hidd_pt_name_id[]\" id=\"hidd_pt_name_id_$consult_pt_consult_id\" value=\"$pt_name_id\"></td>
								<td width=\"120\"  class=\"text_10\" align=\"left\" style=\" vertical-align:top;white-space:nowrap;\">".$consult_pt_dob."&nbsp;(".$consult_pt_age.")"."</td>
								<td width=\"60\"  class=\"text_10\" align=\"left\" style=\" vertical-align:top;\">".$consult_pt_cd_ratio."</td>
								<td width=\"100\"  class=\"text_10\" align=\"left\" style=\" vertical-align:top;\">".$consult_pt_iop."</td>
								<td width=\"180\" class=\"text_10\" align=\"left\" style=\" vertical-align:top;\">".$consult_pt_chart_dx."</td>
								<td width=\"200\" class=\"text_10\" align=\"left\" style=\" vertical-align:top;\">".$consult_pt_ins_plan."</td>
								<td width=\"140\" class=\"text_10\" align=\"left\" style=\" vertical-align:top;\">".$consult_pt_pcp."<input type=\"hidden\" name=\"hidd_pt_pcp_id[]\" id=\"hidd_pt_pcp_id_$consult_pt_consult_id\" value=\"$consult_pt_pcp_id\"><input type=\"hidden\" name=\"hidd_pt_pcp[]\" id=\"hidd_pt_pcp_$consult_pt_consult_id\" value=\"$consult_pt_pcp\"><input type=\"hidden\" name=\"hidd_pt_pcp_fax_no[]\" id=\"hidd_pt_pcp_fax_no_$consult_pt_consult_id\" value=\"$consult_pt_pcp_fax_no\"><input type=\"hidden\" name=\"hidd_pt_pcp_email[]\" id=\"hidd_pt_pcp_email_$consult_pt_consult_id\" value=\"$consult_pt_pcp_email\"></td>
							</tr>
							";
						$arr=array();	
						$arr[]="Physician";
						$arr[]=$usrName;
						fputcsv($fp,$arr, ",","\"");
						$arr=array();	
						$arr[]=$pt_name_id;
						$arr[]=$consult_pt_dob." (".$consult_pt_age.")";
						$arr[]=$consult_pt_iop;
						$arr[]=$consult_pt_chart_dx;
						$arr[]=$consult_pt_ins_plan;
						$arr[]=$consult_pt_pcp;
						fputcsv($fp,$arr, ",","\"");						
				
			}
		}
	
	}
	$str_html .= "</table>";
		$str_html_final = $str_html;
//		$str_html_final .= "<form name=\"frm_more_options\" method=\"post\" action=\"more_options.php\" target=\"_blank\"><input type=\"hidden\" id=\"serialized_output\" name=\"serialized_output\" value=\"".urlencode(htmlentities(addslashes($str_html)))."\"><input type=\"hidden\" id=\"serialized_data\" name=\"serialized_data\" value=\"".urlencode(htmlentities(serialize($arr_data)))."\">
//		<input type=\"hidden\" id=\"serialized_condition\" name=\"serialized_condition\" value=\"".urlencode(htmlentities(addslashes($str_selection_criteria_html)))."\">
//		<input type=\"hidden\" id=\"serialized_cond_criteria\" name=\"serialized_cond_criteria\" value=\"".urlencode(htmlentities(addslashes($str_selection_condition_html)))."\"><input type=\"hidden\" id=\"serialized_action\" name=\"serialized_action\" value=\"\">
//		<input type=\"hidden\" id=\"chkbx_id_comma\" name=\"chkbx_id_comma\" value=\"\"><input type=\"hidden\" id=\"hidd_consult_mod\" name=\"hidd_consult_mod\" value=\"\"></form>";
		
        //$str_html_final."<><><>"."yes"."<><><>".$str_selection_criteria_html."<><><>".$str_selection_condition_html."<><><>".$paging;;
        $str_html_final=$str_html_final_head.$str_html_final;
	//END CODE TO DISPLAY DIABETIC EXAM
}else{
	$str_html = "<div class=\"text-center alert alert-info\">No record found.</div>";
//	$str_html_final = $str_html;
//	$str_html_final .= "<input type=\"hidden\" id=\"serialized_output\" value=\"no_output\">";
	$str_html_final."<><><>"."no"."<><><>".$str_selection_criteria_html."<><><>".$str_selection_condition_html;
}

$finalData = array();
if ($printFile == true and $str_html_final != '') {
   // echo '<div id="generated_report_data" style="margin:0px;border:1px solid #000000">'.$str_html_final.'</div><div class="text-center" id="paging">'.$paging.'</div>';
	//$print = 1;
	/* //$styleHTML = '<style>' . file_get_contents('../css/reports_html.css') . '</style>';
	$csv_file_data = $styleHTML . $str_html_final;

	$stylePDF = '<style>' . file_get_contents('../css/reports_pdf.css') . '</style>';
	$strHTML = $stylePDF . $str_html_final;
	//$file_location = write_html($strHTML);
	
	$finalData['html'] =$str_html_final;
	$finalData['print'] = $print;
	$finalData['pdf'] = $file_location;
	if($output_option == 'output_csv'){
		$finalData['csv'] = $output_option;
	} */
	$finalData['csv'] = 'output_csv';
	$finalData['html'] = 'File created';
	$finalData['csvFile'] = $csv_file_name;
} else {
    $finalData['msg'] = 1;
}
echo json_encode($finalData);
exit;
/* if ($printFile == true and $str_html_final != '') {
    $styleHTML = '<style>' . file_get_contents('../css/reports_html.css') . '</style>';
    $csv_file_data = $styleHTML . $str_html_final;

    $stylePDF = '<style>' . file_get_contents('../css/reports_pdf.css') . '</style>';
    $strHTML = $stylePDF . $str_html_final;

    $file_location = write_html($strHTML);
}
if ($output_option == 'view' || $output_option == 'output_csv') {
    echo $csv_file_data;
} */


function save_report_type($report_name = "", $report_string = ""){
	if(isset($report_name) && trim($report_name) != ""){
		$arr_replace = array("'","\""," ","&","&amp;");
		$report_name = htmlentities(str_replace($arr_replace,"",$report_name));

		$ins_qry = "insert into clinical_report_history set
						cr_created_by = '".$_SESSION["authId"]."',
						cr_created_on = now(),
						cr_report_name = '".$report_name."',
						cr_report_query_string = '".$report_string."'
						";
		imw_query($ins_qry);
	}
}

?>