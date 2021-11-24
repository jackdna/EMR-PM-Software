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
set_time_limit(900);
// require_once("../common/functions.inc.php");
// require_once("../main/Functions.php");
// require_once("../main/common_functions.php");	
// require_once("../patient_access/common/config.php");	
// require_once("../patient_access/common/functions.php");	
// require_once("../main/main_functions.php");
// require_once("../admin/chart_more_functions.php");
// require_once("../common/audit_common_function.php");
// require_once("../chart_notes/fu_functions.php");
// require_once(getcwd()."/../main/chartNotesPrinting.php");
// require_once(getcwd()."/../chart_notes/chartNotesSaveFunction.php");
// require_once(getcwd()."/../chart_notes/common/ChartApXml.php");
//require_once("../Medical_history/common/common_functions.php");

//$objManageData = new DataManage;
$delimiter = '~|~';
$hifen = ' - ';
function underLine($to){
	$NBSP = "<u>";
	for($counter = 1; $counter<=$to; $counter++){
		$NBSP .= "&nbsp;";	
	}
	$NBSP .= "</u>";
	return $NBSP;
}
function getShowTitle( $pid, $type, $titleDef ){
	$elem_showTitle = $title=$pnotes="";
	$sql = "SELECT showTitle,title,pnotes FROM pnote_cat WHERE pid='".$pid."' and title = '".$type."' ";
	$row=sqlQuery($sql);
	if( $row != false ){
		$elem_showTitle = trim($row["showTitle"]);
		if(empty($elem_showTitle)){
			$elem_showTitle = trim( $row["title"] );		
		}
		$pnotes = $row["pnotes"];
	}
	$retTitle = !empty($elem_showTitle) ? $elem_showTitle : $titleDef;
	return array("title"=>$elem_showTitle,"pnotes"=>$pnotes);
}

//START FUNCTIONS FOR A-SCAN

//================= FUNCTION TO GET K HEADING NAMES
function getKHeadingName($ID){		
	$getKreadingIdStr = "SELECT * FROM kheadingnames WHERE kheadingId = '$ID'";
	$getKreadingIdQry = imw_query($getKreadingIdStr);
	$getKreadingIdRow = imw_fetch_array($getKreadingIdQry);
		$kReadingHeadingName = 'K['.$getKreadingIdRow['kheadingName'].']';
	return $kReadingHeadingName;
}
//================= FUNCTION TO GET K HEADING NAMES

//================= FUNCTION TO GET LENSE TYPE
function getLenseName($lenseID){
	$getLenseTypeStr = "SELECT * FROM lenses_iol_type WHERE iol_type_id = '$lenseID'";
	$getLenseTypeQry = imw_query($getLenseTypeStr);
	$getLenseTypeRow = imw_fetch_array($getLenseTypeQry);
	$lenses_iol_type = $getLenseTypeRow['lenses_iol_type'];
	return $lenses_iol_type;
}
//================= FUNCTION TO GET LENSE TYPE

//================= FUNCTION TO GET LENSES FORMULA HEADING NAME
function getFormulaHeadName($id){
	$getFormulaheadingsStr = "SELECT * FROM formulaheadings WHERE formula_id = '$id'";
	$getFormulaheadingsQry = imw_query($getFormulaheadingsStr);
	$getFormulaheadingsRow = imw_fetch_array($getFormulaheadingsQry);
	$formula_heading_name = $getFormulaheadingsRow['formula_heading_name'];
	return $formula_heading_name;
}
//================= FUNCTION TO GET LENSES FORMULA HEADING NAME

//END FUNTIONS FOR A-SCAN
 
if($toMakePdfFor == "Iolink"){
	$pid = $patient_id_from_iolink;
}else {	
	$pid = $patient_id;
}

//in case all facilities are selected
$str_rep_fac = implode(",", $rep_fac);
if($rep_fac == ''){
	$qry = imw_query("select id from facility");
	$res = array();
	$fac_arr = array();
	while ($fac_res = imw_fetch_assoc($qry)) {
		$res[] = $fac_res;
	}
	for($i=0;$i<count($res);$i++){
		$fac_arr[] = $res[$i]['id'];
	}
	$str_rep_fac = implode(',',$fac_arr);
}
//getting selected provider ids
$strProviderIds = implode(",",$providerID);

if(trim($strProviderIds) == ""){
	$provQry = imw_query("select id from users where Enable_Scheduler = 1");
	$provQryRes = array();
	$$provid = array();
	while ($pro_res = imw_fetch_assoc($provQry)) {
		$provQryRes[] = $pro_res;
	}
	for($i=0;$i<count($provQryRes);$i++){
		$provid[] = $provQryRes[$i]['id'];
	}
	$strProviderIds = implode(',',$provid);
}

$blIncludePatientAddress = false;
if(isset($_REQUEST['include_pat_Add']) && $_REQUEST['include_pat_Add'] == 1){
	$blIncludePatientAddress = true;
}

//changing date format
$dtDBEffectDate = get_date_format($eff_date);

list($m,$d,$y) = preg_split('/-/', $_REQUEST['eff_date']);
$intTimeStamp = mktime(0, 0, 0, $m, $d, $y);
$dtShowEffectDate = date("m-d-Y", $intTimeStamp);
$strDayName = date('l', $intTimeStamp);

//getting week
$week = ceil($d/7);

//getting week day
$weekDay = date("N",$intTimeStamp);
$patient_print_data = NULL;
$queryStaringString = NULL;
$tmpCptCode=(!empty($GLOBALS["cpt_code_poe"])) ? $GLOBALS["cpt_code_poe"] : "999";
$str_appt_qry = "SELECT sb.patientId, sb.formId, sb.dateOfService
				FROM superbill sb
				INNER JOIN procedureinfo pi ON ( pi.idSuperBill = sb.idSuperBill
				AND pi.cptCode = '92004' ) 
				WHERE sb.dateOfService <= '".$dtDBEffectDate."'	
				AND sb.formId != '0'
				AND pi.delete_status = '0'
				ORDER BY `sb`.`patientId` ASC , sb.dateOfService DESC ";
$rs_appt = imw_query($str_appt_qry);
$int_tot_appt = imw_num_rows($rs_appt);
if($int_tot_appt > 0){
	$int_appt = 0;
	$ptIdArr = array();
	while($row_appt = imw_fetch_array($rs_appt)){
		$pid 			= $row_appt["patientId"];
		$patient_id		= $row_appt["patientId"];
		if(!in_array($patient_id,$ptIdArr)) { 	
			
			$ptIdArr[] = $patient_id;
			ob_start();
			$form_id 		= $row_appt['formId'];
			$superBillDos 	= $row_appt['dateOfService'];
			
			$tdate=date("m-d-Y");
	
			$qry =imw_query("select * from patient_data where id = $pid");
			$patientDetails = array();
			while($row_pat = imw_fetch_assoc($qry)){
				$patientDetails[] = $row_pat;
			}
			$patientNameArr = array();
			$patientNameArr["LAST_NAME"] = $patientDetails[0]['lname'];
			$patientNameArr["FIRST_NAME"] = $patientDetails[0]['fname'];
			$patientNameArr["MIDDLE_NAME"] = $patientDetails[0]['mname'];
			$patientName = changeNameFormat($patientNameArr);
			
			$age = show_age($patientDetails[0]['DOB']) ;//date('Y') - $y ;
			
			//--- Get Default Facility Details -------
			$qry = imw_query("select default_group from facility where facility_type = '1'");
			$facilityDetail = array();	
			while($rowfd = imw_fetch_assoc($qry)){	
				$facilityDetail[] = $rowfd;
			}
			if(count($facilityDetail)>0){
				$gro_id = $facilityDetail[0]['default_group'];
				$qry = imw_query("select * from groups_new where gro_id = '$gro_id'");
				$groupDetails = array();	
				while($rowgd = imw_fetch_assoc($qry)){	
					$groupDetails[] = $rowgd;
				}
			}
			
			//get flag for surgery sx schedule
			$arr_order_set_associate_id = array();
			$arr_order_id				= array();
			$schSxFlag					= false;
			$ordersetAssocRes = imw_query("SELECT order_set_associate_id FROM order_set_associate_chart_notes WHERE patient_id='".$patient_id."' AND form_id='".$form_id."' AND delete_status='0'");
			if(imw_num_rows($ordersetAssocRes)>0) {
				while($ordersetAssocRow = imw_fetch_array($ordersetAssocRes)) {
					$arr_order_set_associate_id[] = $ordersetAssocRow['order_set_associate_id'];
				}
				$implode_order_set_associate_id = implode(',',$arr_order_set_associate_id);
				
				$ordersetAssocDetailRes = imw_query("SELECT order_id FROM order_set_associate_chart_notes_details WHERE order_set_associate_id IN(".$implode_order_set_associate_id.") AND delete_status='0'");
				if(imw_num_rows($ordersetAssocDetailRes)>0) {
					while($ordersetAssocDetailRow = imw_fetch_array($ordersetAssocDetailRes)) {
						$arr_order_id[] = $ordersetAssocDetailRow['order_id'];
					}
					$implode_order_id = implode(',',$arr_order_id);
					
					$orderDetailRes = imw_query("SELECT id FROM order_details WHERE id IN(".$implode_order_id.") AND o_type='Surgery' AND delete_status='0'");
					if(imw_num_rows($ordersetAssocDetailRes)>0) {
						$schSxFlag = true;
					}
				}
			}
			//end get flag for surgery sx schedule
			if($schSxFlag==true) {
				$int_appt++;
				$reportName="Visit Notes";
				include("visionPrintWithNotesReport.php");
				$patient_print_data .= $patient_workprint_data;
			}
		}
	}
}
$visit_plan = 0;
if(empty($patient_print_data) == false){
	$file_location = write_html($patient_print_data);
	$visit_plan = 1;
	if($_REQUEST["hidexport_report"] == "Yes"){
		$setDownLoadWindowSize = setDownLoadWindowSize();
		$fileNamewith;
		$encPassword;
		$ChartNoteImagesStringFinal;
		$setDownLoadWindowSize;
	}
	echo '<div class="text-center alert alert-info">Please Check PDF.</div>';
}else {
	echo '<div class="text-center alert alert-info">No Recod Exists.</div>';
	$visit_plan = 0;
}
?>