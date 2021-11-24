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
set_time_limit(180);
//require_once(dirname(__FILE__).'/../main/Functions.php');
//require_once(dirname(__FILE__)."/../common/functions.inc.php");
//$objManageData = new ManageData;
//$objDataManage = new DataManage;
//$dispChart = 0;
$FCName= $_SESSION['authId'];
//$dispChart = (int)$_REQUEST['disp_chart'];
$dateFormat= get_sql_date_format();
// Set Date Range Search for Patient Created date
//$_REQUEST['daterangefor'] = 'pat_created';

/*if($dispChart == 1){
	include("../../library/FusionCharts/App/Includes/FusionCharts.php");
	include("../../library/FusionCharts/App/Includes/PageLayout.php");
	include("../../library/FusionCharts/App/DataGen.php");
}*/

$strHTML = $pdfFooter = $strHeader = '';
$strHTML_Label_St = $strHTML_Label_Footer = $strHTML_Label = '';

// GET Physicians Ids
$physicianIds = '';
if(empty($_REQUEST['phyId']) == false) {
	$physicianIds = implode(",",$_REQUEST['phyId']);
}
// GET Operator Ids
$operatorIds = '';
if(empty($_REQUEST['operator_id']) == false) {
	$operatorIds = implode(",",$_REQUEST['operator_id']);
}
// GET Procedure Ids
$procedureIds = '';
if(empty($_REQUEST['rep_proc']) == false) {
	$procedureIds = join(",",$_REQUEST['rep_proc']);
}

// GET Status Ids
$statusIds = '';
if(empty($_REQUEST['status_id']) == false) {
	$statusIds = join(",",$_REQUEST['status_id']);
}

//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
$arrDateRange= $CLSCommonFunction->changeDateSelection();
if($dayReport=='Daily'){
	$_REQUEST['Start_date'] = $_REQUEST['End_date']= date($phpDateFormat);
}else if($dayReport=='Weekly'){
	$_REQUEST['Start_date'] = $arrDateRange['WEEK_DATE'];
	$_REQUEST['End_date'] = date($phpDateFormat);
}else if($dayReport=='Monthly'){
	$_REQUEST['Start_date'] = $arrDateRange['MONTH_DATE'];
	$_REQUEST['End_date'] = date($phpDateFormat);
}else if($dayReport=='Quarterly'){
	$_REQUEST['Start_date'] = $arrDateRange['QUARTER_DATE_START'];
	$_REQUEST['End_date'] = $arrDateRange['QUARTER_DATE_END'];
}

//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
$arrDateRange= $CLSCommonFunction->changeDateSelection();
if($dayReport2=='Daily'){
	$_REQUEST['revenue_from'] = $_REQUEST['revenue_to']= date($phpDateFormat);
}else if($dayReport2=='Weekly'){
	$_REQUEST['revenue_from'] = $arrDateRange['WEEK_DATE'];
	$_REQUEST['revenue_to'] = date($phpDateFormat);
}else if($dayReport2=='Monthly'){
	$_REQUEST['revenue_from'] = $arrDateRange['MONTH_DATE'];
	$_REQUEST['revenue_to'] = date($phpDateFormat);
}else if($dayReport2=='Quarterly'){
	$_REQUEST['revenue_from'] = $arrDateRange['QUARTER_DATE_START'];
	$_REQUEST['revenue_to'] = $arrDateRange['QUARTER_DATE_END'];
}


// GET Status Ids
$heardAbtIds = '';
if(empty($_REQUEST['heardAbtUs']) == false) {
	$heardAbtIds = $_REQUEST['heardAbtUs'];
}

$heardResArr = array();
$heardIdArr = array();
$query = "select heard_id,heard_options from heard_about_us order by heard_options";
$rsQuery = imw_query($query);
while($rowQuery = imw_fetch_array($rsQuery)){
	$heard_id = 0;
	$heard_options = "";
	$heard_id = $rowQuery['heard_id'];
	$heard_options = $rowQuery['heard_options'];
	$heardResArr[$heard_id] = $heard_options;
	$heardIdArr[] =	$heard_id;
	$arrHeardAbtUsAll[$heard_id] = $heard_id;
}
$allHeardCount=sizeof($heardResArr);

$strSelType = "";
$arrHeardAbtUs = $_REQUEST['heardAbtUs'];

$_REQUEST['heardAbtUsValue']=ucfirst(strtolower($_REQUEST['heardAbtUsValue']));

if(sizeof($arrHeardAbtUs)<=0){
	if(empty($_REQUEST['heardAbtUsValue'])==false){ //IN CASE VALUE SELECTED BUT HEARD TYPE NOT SELECTED THEN O IS COMPULSARY TO GET THOSE RECORDS.
		$arrHeardAbtUsAll[]=0;	
		//ADDING IN MASTER ARRAY FOR FURTHER RESULT LOOP  
		$heardIdArr[$_REQUEST['heardAbtUsValue']]=$_REQUEST['heardAbtUsValue']; 
		$heardResArr[$_REQUEST['heardAbtUsValue']]=$_REQUEST['heardAbtUsValue'];
	}
	$arrHeardAbtUs = $arrHeardAbtUsAll;
}
$strSelType =  implode(",", $arrHeardAbtUs);

if(empty($strSelType) == true){
	$strSelType = $_REQUEST['heardAbtAll'];
	$arrHeardAbtUs = explode(",", $strSelType);
}
$facilityIds = implode(",", $_REQUEST['facility_name']);


$strShowSelType = $CLSReports->report_display_selected(implode(',',$_REQUEST['heardAbtUs']),'heard_abt_us',1, $allHeardCount);
if($strShowSelType=='All' && empty($_REQUEST['heardAbtUsValue'])==false){
	$strShowSelType='Other ('.$_REQUEST['heardAbtUsValue'].')';
}else if($strShowSelType!='All' && empty($_REQUEST['heardAbtUsValue'])==false){
	$strShowSelType.=' ('.$_REQUEST['heardAbtUsValue'].')';
}

$strLayout = $_REQUEST['summary_detail'];

//changing date format
if((isset($_REQUEST['Start_date'])) && (empty($_REQUEST['Start_date']) == false)){
	$dtEffectiveDate1 = $_REQUEST['Start_date'];
	$dtDBEffectDate1 = getDateFormatDB($dtEffectiveDate1);
}

//changing date format
if((isset($_REQUEST['End_date'])) && (empty($_REQUEST['End_date']) == false)){
	$dtEffectiveDate2 = $_REQUEST['End_date'];
	$dtDBEffectDate2 = getDateFormatDB($dtEffectiveDate2);
}

//changing date format
if((isset($_REQUEST['revenue_from'])) && (empty($_REQUEST['revenue_from']) == false)){
	$revenue_from = $_REQUEST['revenue_from'];
	$revenue_from = getDateFormatDB($revenue_from);
}

//changing date format
if((isset($_REQUEST['revenue_to'])) && (empty($_REQUEST['revenue_to']) == false)){
	$revenue_to = $_REQUEST['revenue_to'];
	$revenue_to = getDateFormatDB($revenue_to);
}

//getting operator name is session
if($_REQUEST['daterangefor']=='pat_created'){ $dispSearch='Pat. Created Date';}
else if($_REQUEST['daterangefor']=='appointment'){ $dispSearch='Date of Appt';}
else{ $dispSearch='Appt Created Date';}
$ArrC= getUserDetails($_SESSION['authId']);
$createdBy = strtoupper(substr($ArrC['fname'],0,1).substr($ArrC['lname'],0,1));
$createdOn = get_date_format(date('Y-m-d'));
$createdOn.= date(' H:i A');
		
$strCSS= '
			<style>
				.text_b_w{
					font-size:12px;
					font-weight:bold;
					color:#000000;
					background-color:#c7c7c7;
					border-style:solid;
					border-color:#FFFFFF;
					border-width: 1px; 
				}
				.tb_heading{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#FFFFFF;
					background-color:#4684AB;
				}
				.text_10b{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					background-color:#FFFFFF;
				}
				.text_b_date{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#F3F3F3;
				}				
				.text{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
				}
				.text_10{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background:#FFFFFF;
				}
				.report_head_text{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
					color:#4684ab;
					font-weight:bold;
				}
			</style>
			<page backtop="10mm" backbottom="10mm">';

$hasData = 0;
if($strLayout == "summary"){		
	//getting schedule appointments
	$patientIDArr = $patApptDetArr = $grandCountArr = $patientIDArr_lab = array();

	//GET PATIENT IDS IF SEARCHED FOR PAT CREATED DATE
	$arrTempPatIds=array();
	$strTempPatIds='';
	if($_REQUEST['daterangefor']=='pat_created'){
		$qry="Select id FROM patient_data where (DATE_FORMAT(date, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrTempPatIds[$res['id']]=$res['id'];
		}unset($rs);
		
		if(sizeof($arrTempPatIds)>0){
			$strTempPatIds=implode(',', $arrTempPatIds);
		}
	}
	
	//GETTING FIRST APPOINTMENT OF PATIENT BETWEEN SELECTED DATE RANGE
	$arrApptIds=array();
	if($_REQUEST['daterangefor']=='appointment' || $_REQUEST['daterangefor']=='appt_created' || $_REQUEST['daterangefor']=='pat_created'){
		$qry="Select sa.id, sa.sa_patient_id FROM schedule_appointments sa WHERE 1=1";
		if($_REQUEST['daterangefor']=='appointment'){
			$qry .= " AND (sa.sa_app_start_date BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}else if($_REQUEST['daterangefor']=='appt_created'){
			$qry .= " AND (DATE_FORMAT(sa.sa_app_time, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}else if($_REQUEST['daterangefor']=='pat_created'){
			if(empty($strTempPatIds)==false){
				$qry .= " AND sa.sa_patient_id IN(".$strTempPatIds.")";
			}
		}
		$qry.=" ORDER BY sa.sa_app_start_date ASC";
		$rs=imw_query($qry);
		$arrTempAppt=array();
		while($res=imw_fetch_array($rs)){
			if(!$arrTempAppt[$res['sa_patient_id']]){
				$arrApptIds[$res['id']]=$res['id'];
				$arrTempAppt[$res['sa_patient_id']]=$res['sa_patient_id'];
			}
		}unset($rs);
		unset($arrTempAppt);
	}
	
	$intTotAppts = $j = 0;
	$qryAppt = "Select sa.id, sa.sa_patient_app_status_id, sa.procedureid, sa.status_update_operator_id, sp.proc, sa.sa_app_start_date,
	ss.status_name, us.fname as dFname, us.mname as dMname, us.lname as dLname, pd.id as 'patient_id', pd.heard_abt_us, pd.heard_abt_desc   
	  FROM patient_data pd 
	  LEFT JOIN schedule_appointments sa ON sa.sa_patient_id = pd.id 
	  LEFT JOIN users us ON us.id = sa.status_update_operator_id  
	  LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid 
	  LEFT JOIN schedule_status ss ON ss.id = sa.sa_patient_app_status_id 
	  WHERE pd.heard_abt_us IN (".$strSelType.") ";
	
	if(sizeof($arrApptIds)>0){
		$strApptIds=implode(',', $arrApptIds);
		$qryAppt .= " AND sa.id IN(".$strApptIds.")";
	}
	if(isset($_REQUEST['Start_date']) && empty($_REQUEST['Start_date']) == false && isset($_REQUEST['End_date']) && empty($_REQUEST['End_date']) == false ){
		if($_REQUEST['daterangefor']=='pat_created'){
			$qryAppt .= " AND (DATE_FORMAT(pd.date, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}else if($_REQUEST['daterangefor']=='appointment'){
			$qryAppt .= " AND (sa.sa_app_start_date BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}else{
			$qryAppt .= " AND (DATE_FORMAT(sa.sa_app_time, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}
	}
	if(empty($_REQUEST['heardAbtUsValue'])==false){
		$qryAppt .= " AND pd.heard_abt_desc='".trim($_REQUEST['heardAbtUsValue'])."'";
	}
	if(empty($procedureIds) == false){
		$qryAppt .= " AND sa.procedureid IN(".$procedureIds.")";
	}		
	if(empty($statusIds) == false){
		$qryAppt .= " AND sa.sa_patient_app_status_id IN(".$statusIds.")";
	}
	if(empty($operatorIds) == false){
		$qryAppt .= " AND sa.status_update_operator_id IN(".$operatorIds.")";
	}
	if(empty($facilityIds) == false){
		$qryAppt .=" AND pd.default_facility IN(".$facilityIds.")";
	}
	if(empty($physicianIds) == false){
		$qryAppt .=" AND pd.providerID IN(".$physicianIds.")";
	}	
	//$qryAppt .= " ORDER BY sa.id";
	$qryAppt .= " ORDER BY pd.providerID,sa.status_update_operator_id, pd.default_facility,  pd.heard_abt_us, pd.heard_about_us_date DESC";
	
	$rsQryAppt = imw_query($qryAppt);
	$arrPatId = array();
	$tempHeardInfo=array();
	$intTotAppts = imw_num_rows($rsQryAppt);
		if($intTotAppts > 0){
			while($rowQryAppt = imw_fetch_array($rsQryAppt)){
				$heardId = $rowQryAppt['heard_abt_us'];
				if($heardId<=0 && empty($rowQryAppt['heard_abt_desc'])==false){
					$rowQryAppt['heard_abt_desc']=ucfirst(strtolower($rowQryAppt['heard_abt_desc']));
					$heardId=$rowQryAppt['heard_abt_desc'];
				}
				$arrPatId[$rowQryAppt['patient_id']] = $rowQryAppt['patient_id'];
				$arrPatIdAppt[$rowQryAppt['patient_id']] = $rowQryAppt['sa_app_start_date'];
				$tempHeardInfo[$rowQryAppt['patient_id']]=$heardId;
			}
	
			//IF NEW PATIENT SELECTED THEN PATIENTS HAVING FIRST APPOINTMENT ONLY.
			if($_REQUEST['new_patient']=='1' && sizeof($arrPatId)>0){
				$strPatId=implode(',', $arrPatId);
				$qry="Select sa_patient_id FROM schedule_appointments WHERE sa_patient_id IN(".$strPatId.")"; 
				if($_REQUEST['daterangefor']=='appt_created'){			
					$qry.=" AND DATE_FORMAT(sa_app_time, '%Y-%m-%d')<'".$dtDBEffectDate1."'";
				}else{
					$qry.=" AND sa_app_start_date<'".$dtDBEffectDate1."'";				
				}			
				$rs=imw_query($qry);
				while($res=imw_fetch_assoc($rs)){
					unset($arrPatId[$res['sa_patient_id']]);
				}unset($rs);
			}
			
			//COUNT FOR  GRAND TOTALS
			foreach($arrPatId as $patientid){
				$heardId= $tempHeardInfo[$patientid];
				$grandCountArr[$heardId]['COUNT'][] = 1;
			}
			unset($tempHeardInfo);
			
			$arrChrgs = array();
			if(count($arrPatId) > 0){
				$strPatId = "";
				$strPatId = implode(",", $arrPatId);
				$strChrgsQry = "select pcl.patient_id, pcl.encounter_id as encId, pcl.date_of_service, pd.heard_abt_us, pd.heard_abt_desc 
								from patient_charge_list pcl
								INNER JOIN patient_data pd ON pd.id = pcl.patient_id
								where pcl.patient_id IN (".$strPatId.") order by pcl.patient_id";
				$rsChrgsQry = imw_query($strChrgsQry);
				$arrEncId = $arrPayMentDetail = $arrPayMentDetailChild =  $arrAdjustmentAmountDetail = $arrAdjustmentAmountDetailChild = array();
				while($rowChrgsQry = imw_fetch_array($rsChrgsQry)){
					$pid = 0;
					$pid = $rowChrgsQry["patient_id"];
					
					//ONLY GET THOSE ENCOUNTERS HAVING SAME APPT DATE
					if($arrPatIdAppt[$pid]==$rowChrgsQry["date_of_service"]){
						$heardId = $rowChrgsQry['heard_abt_us'];
						if($heardId<=0 && empty($rowChrgsQry['heard_abt_desc'])==false){
							$heardId=ucfirst(strtolower($rowChrgsQry['heard_abt_desc']));
						}				
		
						$arrChrgs[] = array("pID" => $pid, "encID" => $rowChrgsQry["encId"], "heard_abt_us" => $heardId);
						$arrEncId[] = $rowChrgsQry["encId"];
						$patApptDetArr[$pid]['CHARGES_UNFORMAT'] = "";
						$patApptDetArr[$pid]['PatPaid_UNFORMAT'] = "";
						$patApptDetArr[$pid]['InsPaid_UNFORMAT'] = "";
						$patApptDetArr[$pid]['Adjust_UNFORMAT'] = "";
					}
				}
				//pre($arrChrgs,1);
				//pre($patApptDetArr,1);			
				$strEncId = "";
				$strEncId = implode(",", $arrEncId);
				if($strEncId !='' ){
					$strGetPaymentDetails = "select pcpi.paid_by, pcpi.encounter_id, pcdpi.paidForProc + pcdpi.overPayment as paidForProc 
												from patient_chargesheet_payment_info pcpi 
												INNER JOIN patient_charges_detail_payment_info pcdpi on	pcdpi.payment_id = pcpi.payment_id 
												where pcdpi.deletePayment != '1' and 
												pcpi.encounter_id in (".$strEncId.") 
												AND (pcpi.date_of_payment between '".$revenue_from."' AND '".$revenue_to."')";
					$strGetPaymentDetails .= " ORDER BY pcpi.encounter_id ";
					$rsGetPaymentDetails = imw_query($strGetPaymentDetails);
					$i = 0;
					$arrTemp = array();
					$tempEncId = 0;
					while($rowGetPaymentDetails = imw_fetch_array($rsGetPaymentDetails)){
					$eid=$rowGetPaymentDetails["encounter_id"];
						$arrPayments[$eid][$rowGetPaymentDetails["paid_by"]]+=$rowGetPaymentDetails["paidForProc"];
											
					}
					$strGetAdjustmentAmount = "select id,patient_id,encounter_id,payment_type, replace(payment_amount,',','') as  payment_amount 
												from account_payments where encounter_id in (".$strEncId.") and del_status = 0 and payment_type = 'Adjustment' 
												AND (payment_date BETWEEN '".$revenue_from."' AND '".$revenue_to."') ORDER BY encounter_id ";
					$rsGetAdjustmentAmount = imw_query($strGetAdjustmentAmount);
					$i = 0;
					$arrTemp = array();
					$tempEncId = 0;
					while($rowsGetAdjustmentAmount = imw_fetch_array($rsGetAdjustmentAmount)){
						$eid=$rowsGetAdjustmentAmount["encounter_id"];
						$arrAdjustments[$eid]+=$rowsGetAdjustmentAmount["payment_amount"];
					}
					//pre($arrAdjustmentAmountDetailChild,1);
				}
				foreach($arrChrgs as $intChrgsKey => $arrChrgdVal){
					$intCharsPatID = $strCharsEncID = $totPaid = $paidByPat = $paidByIns = $adjustAmt = $heardTypeId = "";
					$arrEncId = array();
					$intCharsPatID = (int)$arrChrgdVal["pID"];
					$strCharsEncID = $arrChrgdVal["encID"];
					$heardTypeId = $arrChrgdVal['heard_abt_us'];
					$arrEncId = explode(",", $strCharsEncID);
					foreach($arrEncId as $intValEncId){
						$srtTempIndexId = "";
						$srtTempIndexId = $arrPayMentDetailChild[$intValEncId];
						if(sizeof($arrPayments[$intValEncId])>0){
							$totPaid+= $arrPayments[$intValEncId]['Patient'] + $arrPayments[$intValEncId]['Insurance'];
							if($arrPayments[$intValEncId]['Patient']>0){
								$paidByPat+=$arrPayments[$intValEncId]['Patient'];
							}
							if($arrPayments[$intValEncId]['Insurance']>0){
								$paidByIns+=$arrPayments[$intValEncId]['Insurance'];
							}
						}					
						
						$srtTempIndexId = "";
							if(sizeof($arrAdjustments[$intValEncId])>0){
							$adjustAmt+= $arrAdjustments[$intValEncId];
						}				
					}
					//pre($patApptDetArr,1);
					if (array_key_exists($intCharsPatID, $patApptDetArr) == true){
						$patApptDetArr[$intPatApptDetKey]['CHARGES_UNFORMAT'] = $totPaid;
						$patApptDetArr[$intPatApptDetKey]['PatPaid_UNFORMAT'] = $paidByPat;
						$patApptDetArr[$intPatApptDetKey]['InsPaid_UNFORMAT'] = $paidByIns;
						$patApptDetArr[$intPatApptDetKey]['Adjust_UNFORMAT'] = $adjustAmt;
	
						$grandHeards[$heardTypeId] = $heardTypeId;
						$grandCountArr[$heardTypeId]['AMOUNT'][] = $totPaid;
						$grandCountArr[$heardTypeId]['PATIENT'][] = $paidByPat;
						$grandCountArr[$heardTypeId]['INSURANCE'][] = $paidByIns;
						$grandCountArr[$heardTypeId]['ADJUST'][] = $adjustAmt;
						
						$patientIDArr_lab[$intCharsPatID] = $intCharsPatID;
					}
				}
			}
		}
	
	if(sizeof($arrPatId)>0){
		$pdfFooter = '<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>';
		$pdfHeader = '<page_header>
						<table class="rpt_padding rpt rpt_table rpt_table-bordered" width="100%">
							<tr class="rpt_headers">
								<td width="530" class="rptbx1">&nbsp;<b>Heard About Us Summary Report</b></td>
								<td width="530" class="rptbx2">Created by '.$createdBy.' on '.$createdOn.'</td>
							</tr>
							<tr class="rpt_headers">
								<td class="rptbx1">Heard Type: '.str_replace("'","",$strShowSelType).'</td>
								<td class="rptbx2">Report for: '.$dispSearch.'&nbsp;&nbsp;&nbsp;Report Period: '.$dtEffectiveDate1.' to '.$dtEffectiveDate2.'</td>
							</tr>			
						</table>
					</page_header>';
		$strHeader = '<table class="rpt rpt_table rpt_table-bordered" width="100%">
						<tr class="rpt_headers">
							<td width="50%" class="rptbx1">Heard About Us Summary Report</td>
							<td width="50%" nowrap="nowrap" class="rptbx2 text-right" >Created by '.$createdBy.' on '.$createdOn.'</td>
						</tr>
						<tr class="rpt_headers">
							<td class="rptbx1">Heard Type: '.str_replace("'","",$strShowSelType).'</td>
							<td class="rptbx2 text-right">Report for: '.$dispSearch.'&nbsp;&nbsp;&nbsp;Report Period: '.$dtEffectiveDate1.' to '.$dtEffectiveDate2.'</td>
						</tr>			
					</table>';
		$pdfHTML = '<table class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr> <td colspan="6" class="text_b_w" >Summary Detail</td></tr>
						<tr>
							<td style="width:175px;" class="text_b_w">Heard Type</td>
							<td style="width:175px;"  class="text_b_w">Count</td>
							<td style="width:175px;" class="text_b_w">Insurance</td>
							<td style="width:175px;" class="text_b_w">Patient</td>
							<td style="width:175px;" class="text_b_w">Adjustment</td>
							<td style="width:175px;" class="text_b_w">Revenue</td>
						</tr>';
		$strHTML = '<table class="rpt rpt_table rpt_table-bordered " >
					<tr> <td colspan="6" class="text_b_w ">Summary Detail</td></tr>
					<tr>
						<td style="width:310px;" class="text_b_w">Heard Type</td>
						<td style="width:180px;" class="text_b_w text-center">Count</td>
						<td style="width:180px;" class="text_b_w text-center">Insurance</td>
						<td style="width:180px;" class="text_b_w text-center">Patient</td>
						<td style="width:180px;" class="text_b_w text-center">Adjustment</td>
						<td style="width:180px;" class="text_b_w text-center">Revenue</td>
					</tr>';				
		$totCount = $totIns = $totPat = $totAdu = $totAmt = 0;			
		foreach($heardIdArr as $heard_key => $heard) {
			if(count($grandCountArr[$heard]['COUNT']) > 0){
				$totCount += (int)count($grandCountArr[$heard]['COUNT']);
				$totIns += array_sum($grandCountArr[$heard]['INSURANCE']);
				$totPat += array_sum($grandCountArr[$heard]['PATIENT']);
				$totAdu += array_sum($grandCountArr[$heard]['ADJUST']);
				$totAmt += array_sum($grandCountArr[$heard]['AMOUNT']);			
				
				$pdfHTML.='<tr><td >'.ucfirst($heardResArr[$heard]).'</td>
						<td class="text-right" >'.count($grandCountArr[$heard]['COUNT']).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['INSURANCE']),2).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['PATIENT']),2).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['ADJUST']),2).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['AMOUNT']),2).'&nbsp;</td></tr>';
				
				$strHTML.='<tr><td >'.ucfirst($heardResArr[$heard]).'</td>
						<td class="text-right" >'.count($grandCountArr[$heard]['COUNT']).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['INSURANCE']),2).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['PATIENT']),2).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['ADJUST']),2).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['AMOUNT']),2).'&nbsp;</td></tr>';
			}
		}
		$pdfHTML.='<tr><td >Grand Total: </td>
						<td style="padding-right:3px;" >'.$totCount.'&nbsp;</td>
						<td style="padding-right:3px;" >'.numberformat($totIns,2).'&nbsp;</td>
						<td style="padding-right:3px;" >'.numberformat($totPat,2).'&nbsp;</td>
						<td style="padding-right:3px;" >'.numberformat($totAdu,2).'&nbsp;</td>
						<td style="padding-right:3px;" >'.numberformat($totAmt,2).'&nbsp;</td></tr>';
				
		$strHTML.='<tr><td ><b>Grand Total:</b></td>
				<td class="text-right" ><b>'.$totCount.'&nbsp;</b></td>
				<td class="text-right" ><b>'.numberformat($totIns,2).'&nbsp;</b></td>
				<td class="text-right" ><b>'.numberformat($totPat,2).'&nbsp;</b></td>
				<td class="text-right" ><b>'.numberformat($totAdu,2).'&nbsp;</b></td>
				<td class="text-right" ><b>'.numberformat($totAmt,2).'&nbsp;</b></td></tr>';
		$pdfHTML.='</table></page>';
		$strHTML.='</table>';
	}
}
elseif($strLayout == "detail"){
	// GET ALL FACILITY ARRAY
	$arrFacility = $CLSReports->getFacilityArr();
	
	//GET PATIENT IDS IF SEARCHED FOR PAT CREATED DATE
	$arrTempPatIds=array();
	$strTempPatIds='';
	if($_REQUEST['daterangefor']=='pat_created'){
		$qry="Select id FROM patient_data WHERE (DATE_FORMAT(date, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrTempPatIds[$res['id']]=$res['id'];
		}unset($rs);
		
		if(sizeof($arrTempPatIds)>0){
			$strTempPatIds=implode(',', $arrTempPatIds);
		}
	}
	
	//GETTING FIRST APPOINTMENT OF PATIENT BETWEEN SELECTED DATE RANGE
	$arrApptIds=array();
	if($_REQUEST['daterangefor']=='appointment' || $_REQUEST['daterangefor']=='appt_created' || $_REQUEST['daterangefor']=='pat_created'){
		$qry="Select sa.id, sa.sa_patient_id FROM schedule_appointments sa WHERE 1=1";
		if($_REQUEST['daterangefor']=='appointment'){
			$qry .= " AND (sa.sa_app_start_date BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}else if($_REQUEST['daterangefor']=='appt_created'){
			$qry .= " AND (DATE_FORMAT(sa.sa_app_time, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}else if($_REQUEST['daterangefor']=='pat_created'){
			if(empty($strTempPatIds)==false){
				$qry .= " AND sa.sa_patient_id IN(".$strTempPatIds.")";
			}
		}
		$qry.=" ORDER BY sa.sa_app_start_date ASC";
		$rs=imw_query($qry);
		$arrTempAppt=array();
		while($res=imw_fetch_array($rs)){
			if(!$arrTempAppt[$res['sa_patient_id']]){
				$arrApptIds[$res['id']]=$res['id'];
				$arrTempAppt[$res['sa_patient_id']]=$res['sa_patient_id'];
			}
		}unset($rs);
		unset($arrTempAppt);
	}
		
	$intTotAppts = $j = 0;
	$qryAppt = "Select sa.id, sa.sa_patient_app_status_id, sa.procedureid, sa.status_update_operator_id, sp.proc, ss.status_name,
				  sa.sa_app_start_date as 'appt_date',	
				  DATE_FORMAT(sa.sa_app_start_date, '".$dateFormat."') as sa_app_start_date , DATE_FORMAT(sa.sa_app_time, '".$dateFormat."') as appt_created_date,
				  us.fname as dFname, us.mname as dMname, us.lname as dLname, pd.id as 'patient_id', pd.heard_abt_us, pd.heard_abt_desc, pd.default_facility, pd.providerID, pd.fname, pd.lname,
				  DATE_FORMAT(pd.date, '".$dateFormat."') as 'pat_created_date',
				  us1.fname as saUpdFname, us1.mname as saUpdMname, us1.lname as saUpddLname,
				  us1.fname as oFname, us1.lname as oLname 
				  FROM patient_data pd 
				  LEFT JOIN schedule_appointments sa ON sa.sa_patient_id = pd.id 
				  LEFT JOIN users us1 ON us1.id = sa.status_update_operator_id 
				  LEFT JOIN users us ON us.id = pd.providerID 
				  LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid 
				  LEFT JOIN schedule_status ss ON ss.id = sa.sa_patient_app_status_id 
				  WHERE pd.heard_abt_us IN (".$strSelType.") ";

	if(sizeof($arrApptIds)>0){
		$strApptIds=implode(',',$arrApptIds);
		$qryAppt .= " AND sa.id IN(".$strApptIds.")";
	}
	if(isset($_REQUEST['Start_date']) && empty($_REQUEST['Start_date']) == false && isset($_REQUEST['End_date']) && empty($_REQUEST['End_date']) == false ){
		if($_REQUEST['daterangefor']=='pat_created'){
			$qryAppt .= " AND (DATE_FORMAT(pd.date, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}else if($_REQUEST['daterangefor']=='appointment'){
			$qryAppt .= " AND (sa.sa_app_start_date BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}else{
			$qryAppt .= " AND (DATE_FORMAT(sa.sa_app_time, '%Y-%m-%d') BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."')";
		}
	}
	if(empty($_REQUEST['heardAbtUsValue'])==false){
		$qryAppt .= " AND pd.heard_abt_desc='".trim($_REQUEST['heardAbtUsValue'])."'";
	}
	if(empty($procedureIds) == false){
		$qryAppt .= " AND sa.procedureid IN(".$procedureIds.")";
	}		
	if(empty($statusIds) == false){
		$qryAppt .= " AND sa.sa_patient_app_status_id IN(0,".$statusIds.")";
	}
	if(empty($operatorIds) == false){
		$qryAppt .= " AND sa.status_update_operator_id IN(".$operatorIds.")";
	}
	if(empty($facilityIds) == false){
		$qryAppt .=" AND pd.default_facility IN(".$facilityIds.")";
	}
	if(empty($physicianIds) == false){
		$qryAppt .=" AND pd.providerID IN(".$physicianIds.")";
	}	
	$qryAppt .= " ORDER BY pd.providerID,sa.status_update_operator_id, pd.default_facility,  pd.heard_abt_us, pd.heard_about_us_date DESC";
	
	$rsQryAppt = imw_query($qryAppt);
	$arrPatId = $arrAppData = array();
	$intTotAppts = imw_num_rows($rsQryAppt);
	if($intTotAppts > 0){
		while($rowQryAppt = imw_fetch_array($rsQryAppt)){
			$pid = $rowQryAppt['patient_id'];
			$arrPatId[$pid] = $pid;
			$arrPatIdAppt[$pid] = $rowQryAppt['appt_date'];
			
			if($rowQryAppt['sa_patient_app_status_id']<=0 || $rowQryAppt['sa_patient_app_status_id']==''){
				$rowQryAppt["status_name"]='Active';
			}
			if($rowQryAppt["heard_abt_desc"]!=''){ $rowQryAppt["heard_abt_desc"]=ucfirst(strtolower($rowQryAppt["heard_abt_desc"]));}
			
			$arrAppData[$pid] = array("pid" => $pid, "proLname" => $rowQryAppt["dLname"], "proFname" => $rowQryAppt["dFname"],
			 "proMname" => $rowQryAppt["dMname"], "status_name" => $rowQryAppt["status_name"], "proc" => $rowQryAppt["proc"],
			 "status_update_operator_id" => $rowQryAppt["status_update_operator_id"], "heard_abt_us" => $rowQryAppt["heard_abt_us"],
			 "heard_abt_desc" => $rowQryAppt["heard_abt_desc"], "default_facility" => $rowQryAppt["default_facility"],
			 "providerID" => $rowQryAppt["providerID"], "fname" => $rowQryAppt["fname"], "lname" => $rowQryAppt["lname"],
			 "oFname" => $rowQryAppt["oFname"], "oLname" => $rowQryAppt["oLname"], "saUpdFname" => $rowQryAppt["saUpdFname"],
			 "saUpdMname" => $rowQryAppt["saUpdMname"], "saUpddLname" => $rowQryAppt["saUpddLname"],
			 "sa_app_start_date" => $rowQryAppt["sa_app_start_date"], "pat_created_date" => $rowQryAppt["pat_created_date"]);
		}

		//IF NEW PATIENT SELECTED THEN PATIENTS HAVING FIRST APPOINTMENT ONLY.
		if($_REQUEST['new_patient']=='1' && sizeof($arrPatId)>0){
			
			$strPatId=implode(',', $arrPatId);
			$qry="Select sa_patient_id FROM schedule_appointments WHERE sa_patient_id IN(".$strPatId.")";
			if($_REQUEST['daterangefor']=='appt_created'){			
				$qry.=" AND DATE_FORMAT(sa_app_time, '%Y-%m-%d')<'".$dtDBEffectDate1."'";
			}else{
				$qry.=" AND sa_app_start_date<'".$dtDBEffectDate1."'";				
			}
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				unset($arrPatId[$res['sa_patient_id']]);
				unset($arrAppData[$res['sa_patient_id']]);
			}unset($rs);
		}
		
		if(count($arrPatId) > 0){
			$strPatId = "";
			$strPatId = implode(",", $arrPatId);
			$strChrgsQry = "select pcl.patient_id, pcl.date_of_service, pcl.encounter_id as encId 
							from patient_charge_list pcl
							INNER JOIN patient_data pd ON pd.id = pcl.patient_id
							where pcl.patient_id IN (".$strPatId.") order by pcl.patient_id";
			//die($strChrgsQry);
			$rsChrgsQry = imw_query($strChrgsQry);
			$arrEncId = $arrPayMentDetail = $arrAdjustmentAmountDetail = array();
			while($rowChrgsQry = imw_fetch_array($rsChrgsQry)){
				$pid = 0;
				$pid = $rowChrgsQry["patient_id"];
				
				//ONLY ENCOUNTERS WHICH HAVE SAME DOS DATE AS IN APPOINTMENT
				if($arrPatIdAppt[$pid]==$rowChrgsQry["date_of_service"]){
					$arrChrgs[$pid] = array("pID" => $pid, "encID" => $rowChrgsQry["encId"]);
					$arrEncId[] = $rowChrgsQry["encId"];
					$patApptDetArr[$pid]['CHARGES_UNFORMAT'] = "";
					$patApptDetArr[$pid]['PatPaid_UNFORMAT'] = "";
					$patApptDetArr[$pid]['InsPaid_UNFORMAT'] = "";
					$patApptDetArr[$pid]['Adjust_UNFORMAT'] = "";
				}
			}
			//echo count($arrChrgs);
			//pre($arrChrgs,1);
			//pre($patApptDetArr,1);
			
			$strEncId = implode(",", $arrEncId);
			if($strEncId!=''){
				$strGetPaymentDetails = "select pcpi.paid_by, pcpi.encounter_id, pcdpi.paidForProc + pcdpi.overPayment as paidForProc 
											from patient_chargesheet_payment_info pcpi 
											INNER JOIN patient_charges_detail_payment_info pcdpi on	pcdpi.payment_id = pcpi.payment_id 
											where pcdpi.deletePayment != '1' and 
											pcpi.encounter_id in (".$strEncId.") 
											AND (pcpi.date_of_payment between '".$revenue_from."' AND '".$revenue_to."')";
				$strGetPaymentDetails .= " ORDER BY pcpi.encounter_id ";
				//die($strGetPaymentDetails);
				$rsGetPaymentDetails = imw_query($strGetPaymentDetails);
				$i = 0;
				$arrTemp = array();
				$tempEncId = 0;
				//__getPaymentDetails Getting Record Start
				while($rowGetPaymentDetails = imw_fetch_array($rsGetPaymentDetails)){
					$eid=$rowGetPaymentDetails["encounter_id"];
					$arrPayments[$eid][$rowGetPaymentDetails["paid_by"]]+=$rowGetPaymentDetails["paidForProc"];
				}

				//__getPaymentDetails Getting Record End
				
				//__getAdjustmentAmount Getting Record Start
				$strGetAdjustmentAmount = "select id,patient_id,encounter_id,payment_type, replace(payment_amount,',','') as  payment_amount
											from account_payments where encounter_id in (".$strEncId.") and del_status = 0 and payment_type = 'Adjustment' 
											AND (payment_date between '".$revenue_from."' AND '".$revenue_to."')
											ORDER BY encounter_id ";
				$rsGetAdjustmentAmount = imw_query($strGetAdjustmentAmount);
				$i = 0;
				$arrTemp = array();
				$tempEncId = 0;
				while($rowsGetAdjustmentAmount = imw_fetch_array($rsGetAdjustmentAmount)){
					$eid=$rowsGetAdjustmentAmount["encounter_id"];
					$arrAdjustments[$eid]+=$rowsGetAdjustmentAmount["payment_amount"];
				}
				//__getAdjustmentAmount Getting Record End
				
				//pre($arrPayMentDetail,1);
			}
			
			foreach($arrChrgs as $intChrgsKey => $arrChrgdVal){
				$intCharsPatID = $strCharsEncID = $totPaid = $paidByPat = $paidByIns = $adjustAmt = $heardTypeId = $facilityId = $docId = "";
				$arrEncId = array();
				$intCharsPatID = (int)$arrChrgdVal["pID"];
				$strCharsEncID = $arrChrgdVal["encID"];
				$arrEncId = explode(",", $strCharsEncID);
				//pre($arrEncId,1);

				foreach($arrEncId as $intValEncId){
					//__getPaymentDetails Start
					$srtTempIndexId = "";
					$srtTempIndexId = $arrPayMentDetailChild[$intValEncId];

					if(sizeof($arrPayments[$intValEncId])>0){
						$totPaid+= $arrPayments[$intValEncId]['Patient'] + $arrPayments[$intValEncId]['Insurance'];
						if($arrPayments[$intValEncId]['Patient']>0){
							$paidByPat+=$arrPayments[$intValEncId]['Patient'];
						}
						if($arrPayments[$intValEncId]['Insurance']>0){
							$paidByIns+=$arrPayments[$intValEncId]['Insurance'];
						}
					}
					//__getPaymentDetails End
					
					//__getAdjustmentAmount Start
					$srtTempIndexId = "";
					if(sizeof($arrAdjustments[$intValEncId])>0){
						$adjustAmt+= $arrAdjustments[$intValEncId];
					}				
						
					//__getAdjustmentAmount End
				}
				//pre($patApptDetArr,1);
				
				if (array_key_exists($intCharsPatID, $patApptDetArr) == true){
					$opInitial = $strStatus = $strStatusProcedure = "";
					if (array_key_exists($intCharsPatID, $arrAppData) == true){
						$opInitial = strtoupper($arrAppData[$intCharsPatID]["saUpdFname"][0].$arrAppData[$intCharsPatID]["saUpddLname"][0]);	
						$strStatus = $arrAppData[$intCharsPatID]['status_name'];
						$strStatusProcedure = $arrAppData[$intCharsPatID]['proc'];	
					}
					
					$patApptDetArr[$intCharsPatID]['STATUS'] = $strStatus;
					$patApptDetArr[$intCharsPatID]['PROCEDURE'] = $strStatusProcedure;
					$patApptDetArr[$intCharsPatID]['CHARGES_UNFORMAT'] = $totPaid;
					$patApptDetArr[$intCharsPatID]['PatPaid_UNFORMAT'] = $paidByPat;
					$patApptDetArr[$intCharsPatID]['InsPaid_UNFORMAT'] = $paidByIns;
					$patApptDetArr[$intCharsPatID]['Adjust_UNFORMAT'] = $adjustAmt;
					
					$totPaid = numberformat($totPaid,2);
					$patApptDetArr[$intCharsPatID]['CHARGES'] = $totPaid;
					$patApptDetArr[$intCharsPatID]['OPERATOR'] = $opInitial;
				}
			}
		}
	}
	
	if(sizeof($arrPatId)>0){
		$pdfFooter = '<page_footer>
						<table style="width: 100%;">
							<tr>
								<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
							</tr>
						</table>
					</page_footer>';	
				
		$pdfHeader = '<page_header>
						<table class="rpt_padding rpt rpt_table rpt_table-bordered" width="100%">
							<tr class="rpt_headers">
								<td width="530" class="rptbx1" align="left">Heard About Us Detail Report</td>
								<td width="530" class="rptbx2">Created by '.$createdBy.' on '.$createdOn.'</td>
							</tr>
							<tr class="rpt_headers">
								<td class="rptbx1">Heard Type: '.str_replace("'","",$strShowSelType).'</td>
								<td class="rptbx2">Report for: '.$dispSearch.'&nbsp;&nbsp;&nbsp;Report Period: '.$dtEffectiveDate1.' to '.$dtEffectiveDate2.'</td>
							</tr>
						</table>
					</page_header>';
				
		$strHeader = '<table class="rpt rpt_table rpt_table-bordered" width="100%">
						<tr class="rpt_headers">
							<td width="50%" class="rptbx1" align="left">Heard About Us Detail Report</td>
							<td width="50%" nowrap="nowrap" class="rptbx2 text-right">Created by '.$createdBy.' on '.$createdOn.'</td>
						</tr>
						<tr class="rpt_headers">
							<td class="rptbx1">Heard Type: '.str_replace("'","",$strShowSelType).'</td>
							<td class="rptbx2 text-right">Report for: '.$dispSearch.'&nbsp;&nbsp;&nbsp;Report Period: '.$dtEffectiveDate1.' to '.$dtEffectiveDate2.'</td>
						</tr>
					</table>';
				
		$strHTML_TITLE = '';
		$cols = 8;
		$patColWidth = 170;
		if($heardAbtIds=='' && $procedureIds!='' && $operatorIds!='') { 
			$patColWidth+= 50;
		}else{
			$strHTML_TITLE='<td width="auto" class="text_b_w">Operator</td>';
			$pdfHTML_TITLE='<td width="125" class="text_b_w">OP</td>';
			$cols = 9;
		}
		$pdfHTML = '<table class="rpt rpt_table rpt_table-bordered" width="100%">
					<tr>
						<td width="25" class="text_b_w" align="left">&nbsp;S.No.</td>
						<td width="120" class="text_b_w" align="left">Date of Appt</td>
						<td width="120" class="text_b_w" align="left">Created Date</td>
						<td width="120" class="text_b_w" align="left">Patient Name - ID</td>
						<td width="120" class="text_b_w" align="left">Procedure</td>
						<td width="120" class="text_b_w" align="left">Appt. Status</td>
						<td width="120" class="text_b_w" align="center" style="text-align:center;">Paid</td>
						<td width="100" class="text_b_w" align="left">Type</td>'.
						$pdfHTML_TITLE.'
					</tr>';
		$strHTML = '<table class="rpt rpt_table rpt_table-bordered" width="100%">
					<tr>
						<td width="47" class="text_b_w" >&nbsp;S.No.</td>
						<td width="97" class="text_b_w" >Date of Appt</td>
						<td width="105" class="text_b_w">Pat. Created Date</td>
						<td width="220" class="text_b_w">Patient Name - ID</td>
						<td width="200" class="text_b_w">Procedure</td>
						<td width="145" class="text_b_w">Appt. Status</td>
						<td width="145" class="text_b_w text-center">Paid</td>
						<td width="200" class="text_b_w">Heard Type</td>'.
						$strHTML_TITLE.'
					</tr>';
		$oldFacility = $oldDoctor = $oldOperator = "";
		$facHeardCountArr = array();
		$operHeardCountArr = array();
		$fac = $inOperator = 0;
		$i = 0;
		//pre($arrChrgs,1);
		//pre($arrAppData,1);
		
		foreach($arrAppData as $intAppDataKey => $arrAppDataVal){
			$intCharsPatID = "";
			$intCharsPatID = (int)$intAppDataKey;
			$opName = $docName = $operID = $heardTypeId = $heardDesc= $facilityId = $docId = $oLname = $oFname = $OperatorInitial = "";
			$lname = $fname = "";
			//if(array_key_exists($intCharsPatID, $arrChrgs) == true){
				//echo $intCharsPatID;
				$heardTypeId = $arrAppData[$intCharsPatID]['heard_abt_us'];
				$heardDesc = $arrAppData[$intCharsPatID]['heard_abt_desc'];
				$facilityId = $arrAppData[$intCharsPatID]['default_facility'];
				$docId = $arrAppData[$intCharsPatID]['providerID'];
				$operID = $arrAppData[$intCharsPatID]['status_update_operator_id'];
				$tempHeardTypeId=$heardTypeId;
				
				if(empty($heardDesc)==false && $heardTypeId<=0){
					$heardTypeId=$heardDesc;
				}
				
				$heardTypeArr[$facilityId][$heardTypeId] = $heardTypeId;	
				$heardTypeOperArr[$operID][$heardTypeId] = $heardTypeId;
				$patientIDArr_lab[$intCharsPatID] = $intCharsPatID;
				$y = $m = $d = 0;
				
				//Operator
				$OperatorInitial = strtoupper($arrAppData[$intCharsPatID]["saUpdFname"][0].$arrAppData[$intCharsPatID]["saUpddLname"][0]);
				
				$sa_app_start_date = $arrAppData[$intCharsPatID]['sa_app_start_date'];
				$pat_created_date = $arrAppData[$intCharsPatID]['pat_created_date'];
				
				$dtHeardDate = get_date_format($heardAboutUsDate);
				if($heardAboutUsDate == "0000-00-00"){
					$dtHeardDate = "N/A";
				}
				
				$proNameArr = array();
				$proNameArr["LAST_NAME"] = $arrAppData[$intCharsPatID]["proLname"];
				$proNameArr["FIRST_NAME"] = $arrAppData[$intCharsPatID]["proFname"];
				$proNameArr["MIDDLE_NAME"] = $arrAppData[$intCharsPatID]["proMname"];
				$docName = changeNameFormat($proNameArr);
				
				$oLname = $arrAppData[$intCharsPatID]['oLname'];
				$oFname = $arrAppData[$intCharsPatID]['oFname'];
							
				if($docName == ""){
					$docName = "No Physician";
				}	
				
				$lname = $arrAppData[$intCharsPatID]['lname'];
				$fname = $arrAppData[$intCharsPatID]['fname'];
				// SET OPERATOR
				if($heardAbtIds=='' && $procedureIds!='' && $operatorIds!='' && ($operID != $oldOperator)) { 
					if($i > 0){
						$strHTML.='<tr><td colspan="'.$cols.'" class="text">
							<table class="rpt rpt_table rpt_table-bordered" width="100%">
							<tr>
								<td style="width:175px;" class="text_10b text-center">Heard Type</td>
								<td style="width:175px;"  class="text_10b text-center">Count</td>
								<td style="width:175px;" class="text_10b text-center">Insurance</td>
								<td style="width:175px;" class="text_10b text-center">Patient</td>
								<td style="width:175px;" class="text_10b text-center">Adjustment</td>
								<td style="width:175px;" class="text_10b text-center">Revenue</td>
							</tr>
							';
						foreach($heardIdArr as $heard_key => $heard) {	
							if(count($operHeardCountArr[$heard]['COUNT'][$oldOperator]) > 0){
								$strHTML.='<tr><td >'.$heardResArr[$heard].'</td>
										<td class="text-right" >'.count($operHeardCountArr[$heard]['COUNT'][$oldOperator]).'&nbsp;</td>
										<td class="text-right">'.numberformat(array_sum($operHeardCountArr[$heard]['INSURANCE'][$oldOperator]),2).'&nbsp;</td>
										<td class="text-right" >'.numberformat(array_sum($operHeardCountArr[$heard]['PATIENT'][$oldOperator]),2).'&nbsp;</td>
										<td class="text-right" >'.numberformat(array_sum($operHeardCountArr[$heard]['ADJUST'][$oldOperator]),2).'&nbsp;</td>
										<td class="text-right" >'.numberformat(array_sum($operHeardCountArr[$heard]['AMOUNT'][$oldOperator]),2).'&nbsp;</td></tr>';
					
							}
						}
						$strHTML.='</table></td></tr>';
					
						$pdfHTML.='<tr><td colspan="'.$cols.'" class="text">
							<table class="rpt rpt_table rpt_table-bordered" width="100%">
							<tr>
								<td style="width:175px;" class="text_10b">Heard Type</td>
								<td style="width:175px;" class="text_10b">Count</td>
								<td style="width:175px;" class="text_10b">Insurance</td>
								<td style="width:175px;" class="text_10b">Patient</td>
								<td style="width:175px;" class="text_10b">Adjustment</td>
								<td style="width:175px;" class="text_10b">Revenue</td>
							</tr>
							';
							foreach($heardIdArr as $heard_key => $heard) {	
								if(count($operHeardCountArr[$heard]['COUNT'][$oldOperator]) > 0){
									$pdfHTML.='<tr><td >'.$heardResArr[$heard].'</td>
											<td style="text-align:right" >'.count($operHeardCountArr[$heard]['COUNT'][$oldOperator]).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['INSURANCE'][$oldOperator]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['PATIENT'][$oldOperator]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['ADJUST'][$oldOperator]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['AMOUNT'][$oldOperator]),2).'&nbsp;</td></tr>';
					
								}
							}
						$pdfHTML.='</table></td></tr>';
					}
					$strHTML.='<tr><td colspan="'.$cols.'" class="text_b_w" style="background-color:#8CC1DA; height:20px;">Operator : '.$oLname.', '.$oFname.'</td></tr>';
					$pdfHTML.='<tr><td colspan="'.$cols.'" class="text_b_w" style="background-color:#8CC1DA; height:20px;">Operator : '.$oLname.', '.$oFname.'</td></tr>';
					$inOperator = 1;				
				}
				// SET FACILITY
				if($facilityId != $oldFacility){

					if($i>0 && $inOperator==0){
						// TOTAL FOR LAST FACILITY
						$strHTML.='<tr><td colspan="'.$cols.'" class="pd0">
							<table class="rpt rpt_table rpt_table-bordered mt2" width="100%">
							<tr>
								<td style="width:175px;" class="text_10b">Heard Type</td>
								<td style="width:175px;" class="text_10b text-center">Count</td>
								<td style="width:175px;" class="text_10b text-center">Insurance</td>
								<td style="width:175px;" class="text_10b text-center">Patient</td>
								<td style="width:175px;" class="text_10b text-center">Adjustment</td>
								<td style="width:175px;" class="text_10b text-center">Revenue</td>
							</tr>
							';
						foreach($heardIdArr as $heard_key => $heard) {	
							if(count($facHeardCountArr[$heard]['COUNT'][$oldFacility])>0){
								$strHTML.='<tr><td >'.$heardResArr[$heard].'</td>
										<td class="text-right" >'.count($facHeardCountArr[$heard]['COUNT'][$oldFacility]).'&nbsp;</td>
										<td class="text-right" >'.numberformat(array_sum($facHeardCountArr[$heard]['INSURANCE'][$oldFacility]),2).'&nbsp;</td>
										<td class="text-right" >'.numberformat(array_sum($facHeardCountArr[$heard]['PATIENT'][$oldFacility]),2).'&nbsp;</td>
										<td class="text-right" >'.numberformat(array_sum($facHeardCountArr[$heard]['ADJUST'][$oldFacility]),2).'&nbsp;</td>
										<td class="text-right" >'.numberformat(array_sum($facHeardCountArr[$heard]['AMOUNT'][$oldFacility]),2).'&nbsp;</td></tr>';
					
							}
						}
						$strHTML.='</table></td></tr>';
					
						$pdfHTML.='<tr><td colspan="'.$cols.'" class="text">
							<table class="rpt rpt_table rpt_table-bordered" width="100%">
							<tr>
								<td style="width:175px;" class="text_10b">Heard Type</td>
								<td style="width:175px;" class="text_10b">Count</td>
								<td style="width:175px;" class="text_10b">Insurance</td>
								<td style="width:175px;" class="text_10b">Patient</td>
								<td style="width:175px;" class="text_10b">Adjustment</td>
								<td style="width:175px;" class="text_10b">Revenue</td>
							</tr>
							';
							foreach($heardIdArr as $heard_key => $heard) {
								if(count($facHeardCountArr[$heard]['COUNT'][$oldFacility])>0){
									$pdfHTML.='<tr><td >'.$heardResArr[$heard].'</td>
											<td style="text-align:right" >'.count($facHeardCountArr[$heard]['COUNT'][$oldFacility]).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['INSURANCE'][$oldFacility]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['PATIENT'][$oldFacility]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['ADJUST'][$oldFacility]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['AMOUNT'][$oldFacility]),2).'&nbsp;</td></tr>';
								}
							}
						$pdfHTML.='</table></td></tr>';
					
					}
					$strHTML.='<tr><td colspan="'.$cols.'" class="text_b_w" style="height:20px;">Facility : '.($facilityId ? $arrFacility[$facilityId] : $arrFacility[0]).'</td></tr>';
					$pdfHTML.='<tr><td colspan="'.$cols.'" class="text_b_w" >Facility : '.$arrFacility[$facilityId].'</td></tr>';
					$fac =1;
				}
				// SET DOCTOR
				if($docId != $oldDoctor){
					$strHTML.='<tr><td colspan="'.$cols.'" class="text_b_w" align="left" style="height:20px;">'.$docName.'</td></tr>';
					$pdfHTML.='<tr><td colspan="'.$cols.'" class="text_b_w" align="left">'.$docName.'</td></tr>';
				}
				// GET Totals OF Facilites types
				$fac++;
				$facHeardCountArr[$heardTypeId]['COUNT'][$facilityId][] = $fac;
				$facHeardCountArr[$heardTypeId]['AMOUNT'][$facilityId][]= $patApptDetArr[$intCharsPatID]['CHARGES_UNFORMAT'];
				$facHeardCountArr[$heardTypeId]['PATIENT'][$facilityId][]= $patApptDetArr[$intCharsPatID]['PatPaid_UNFORMAT'];
				$facHeardCountArr[$heardTypeId]['INSURANCE'][$facilityId][]= $patApptDetArr[$intCharsPatID]['InsPaid_UNFORMAT'];
				$facHeardCountArr[$heardTypeId]['ADJUST'][$facilityId][]= $patApptDetArr[$intCharsPatID]['Adjust_UNFORMAT'];
				
				// Totals for Operarots
				$operHeardCountArr[$heardTypeId]['COUNT'][$operID][] = $fac;
				$operHeardCountArr[$heardTypeId]['AMOUNT'][$operID][]= $patApptDetArr[$intCharsPatID]['CHARGES_UNFORMAT'];
				$operHeardCountArr[$heardTypeId]['PATIENT'][$operID][]= $patApptDetArr[$intCharsPatID]['PatPaid_UNFORMAT'];
				$operHeardCountArr[$heardTypeId]['INSURANCE'][$operID][]= $patApptDetArr[$intCharsPatID]['InsPaid_UNFORMAT'];
				$operHeardCountArr[$heardTypeId]['ADJUST'][$operID][]= $patApptDetArr[$intCharsPatID]['Adjust_UNFORMAT'];
		
				// GRAND TOTAL FACILITY
				$grandHeards[$heardTypeId] = $heardTypeId;
				$grandCountArr[$heardTypeId]['COUNT'][] = $fac;
				$grandCountArr[$heardTypeId]['AMOUNT'][]= $patApptDetArr[$intCharsPatID]['CHARGES_UNFORMAT'];
				$grandCountArr[$heardTypeId]['PATIENT'][]= $patApptDetArr[$intCharsPatID]['PatPaid_UNFORMAT'];
				$grandCountArr[$heardTypeId]['INSURANCE'][]= $patApptDetArr[$intCharsPatID]['InsPaid_UNFORMAT'];
				$grandCountArr[$heardTypeId]['ADJUST'][]= $patApptDetArr[$intCharsPatID]['Adjust_UNFORMAT'];
				// -----------------------------
				
				$heardDescShow='';
				if(empty($heardDesc)==false && $tempHeardTypeId>0){
					$heardDescShow=' ('.$heardDesc.')';
				}else if(empty($heardDesc)==false && $tempHeardTypeId<=0){
					$heardDescShow='';
				}
				$pdfHTML.= '<tr>
								<td  align="left">&nbsp;'.($i+1).'</td>
								<td  align="left">'.$sa_app_start_date.'</td>
								<td  align="left">'.$pat_created_date.'</td>
								<td  align="left">'.$fname.' '.$lname.' - '.$intCharsPatID.'</td>
								<td  align="left">'.$arrAppData[$intCharsPatID]["proc"].'</td>
								<td  align="left">'.$arrAppData[$intCharsPatID]["status_name"].'</td>
								<td  align="left">'.$patApptDetArr[$intCharsPatID]['CHARGES'].'</td>
								<td  style="width:40px;" align="left">'.$heardResArr[$heardTypeId].$heardDescShow.'</td>';
				if($heardAbtIds=='' && $procedureIds!='' && $operatorIds!='') { 
				}
				else{
					$pdfHTML.='<td  align="left">&nbsp;'.$OperatorInitial.'</td>';
				}
				$pdfHTML.='</tr>';			
			
				$strHTML.= '<tr height="20px">
							<td class="text" align="left">&nbsp;'.($i+1).'</td>
							<td class="text" align="left">&nbsp;'.$sa_app_start_date.'</td>
							<td class="text" align="left">&nbsp;'.$pat_created_date.'</td>
							<td class="text" align="left">&nbsp;'.$fname.' '.$lname.' - '.$intCharsPatID.'</td>
							<td class="text" align="left">&nbsp;'.$arrAppData[$intCharsPatID]["proc"].'</td>
							<td class="text" align="left">&nbsp;'.$arrAppData[$intCharsPatID]["status_name"].'</td>
							<td class="text" align="right">&nbsp;'.$patApptDetArr[$intCharsPatID]['CHARGES'].'&nbsp;</td>
							<td class="text" align="left">&nbsp;&nbsp;'.$heardResArr[$heardTypeId].$heardDescShow.'</td>';
				if($heardAbtIds=='' && $procedureIds!='' && $operatorIds!='') { 
				}
				else{
					$strHTML.='<td  class="text" align="left">&nbsp;'.$OperatorInitial.'</td>';
				}
				$strHTML.='</tr>';
				
				if($intTotAppts == ($i+1)){
					if($inOperator==0){
						// TOTAL FOR LAST FACILITY
						$strHTML.='<tr><td colspan="'.$cols.'" class="pd0">
							<table class="rpt rpt_table rpt_table-bordered mt2" width="100%">
							<tr>
								<td style="width:175px;" class="text_10b">Heard Type</td>
								<td style="width:175px;" class="text_10b text-center">Count</td>
								<td style="width:175px;" class="text_10b text-center">Insurance</td>
								<td style="width:175px;" class="text_10b text-center">Patient</td>
								<td style="width:175px;" class="text_10b text-center">Adjustment</td>
								<td style="width:175px;" class="text_10b text-center">Revenue</td>
							</tr>
							';
						foreach($heardIdArr as $heard_key => $heard) {	
							if(count($facHeardCountArr[$heard]['COUNT'][$facilityId])>0){
								$strHTML.='<tr><td >'.$heardResArr[$heard].'</td>
										<td style="text-align:right" >'.count($facHeardCountArr[$heard]['COUNT'][$facilityId]).'&nbsp;</td>
										<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['INSURANCE'][$facilityId]),2).'&nbsp;</td>
										<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['PATIENT'][$facilityId]),2).'&nbsp;</td>
										<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['ADJUST'][$facilityId]),2).'&nbsp;</td>
										<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['AMOUNT'][$facilityId]),2).'&nbsp;</td></tr>';
							}
						}
						$strHTML.='</table></td></tr>';
					
						$pdfHTML.='<tr><td colspan="'.$cols.'" class="text">
							<table class="rpt rpt_table rpt_table-bordered" width="100%">
							<tr>
								<td style="width:175px;" class="text_10b">Heard Type</td>
								<td style="width:175px;" class="text_10b">Count</td>
								<td style="width:175px;" class="text_10b">Insurance</td>
								<td style="width:175px;" class="text_10b">Patient</td>
								<td style="width:175px;" class="text_10b">Adjustment</td>
								<td style="width:175px;" class="text_10b">Revenue</td>
							</tr>
							';
							foreach($heardIdArr as $heard_key => $heard) {	
								if(count($facHeardCountArr[$heard]['COUNT'][$facilityId])>0){
									$pdfHTML.='<tr><td >'.$heardResArr[$heard].'</td>
											<td style="text-align:right" >'.count($facHeardCountArr[$heard]['COUNT'][$facilityId]).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['INSURANCE'][$facilityId]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['PATIENT'][$facilityId]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['ADJUST'][$facilityId]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($facHeardCountArr[$heard]['AMOUNT'][$facilityId]),2).'&nbsp;</td></tr>';					
								}
							}
						$pdfHTML.='</table></td></tr>';
					}
					
					if($inOperator==1){
						// GRAND TOTAL FOR LAST OPERATOR
						$strHTML.='<tr><td colspan="'.$cols.'" class="text">
							<table class="rpt rpt_table rpt_table-bordered" width="100%">
							<tr>
								<td style="width:150px;" class="text_10b">Heard Type</td>
								<td style="width:90px;" class="text_10b text-center">Count</td>
								<td style="width:120px;" class="text_10b text-center">Insurance</td>
								<td style="width:120px;" class="text_10b text-center">Patient</td>
								<td style="width:120px;" class="text_10b text-center">Adjustment</td>
								<td style="width:120px;" class="text_10b text-center">Revenue</td>
							</tr>
							';
						foreach($heardIdArr as $heard_key => $heard) {	
							if(count($operHeardCountArr[$heard]['COUNT'][$oldOperator])>0){
								$strHTML.='<tr><td >'.$heardResArr[$heard].'</td>
										<td style="text-align:right" >'.count($operHeardCountArr[$heard]['COUNT'][$oldOperator]).'&nbsp;</td>
										<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['INSURANCE'][$oldOperator]),2).'&nbsp;</td>
										<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['PATIENT'][$oldOperator]),2).'&nbsp;</td>
										<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['ADJUST'][$oldOperator]),2).'&nbsp;</td>
										<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['AMOUNT'][$oldOperator]),2).'&nbsp;</td></tr>';
				
							}
						}
						$strHTML.='</table></td></tr>';
					
						$pdfHTML.='<tr><td colspan="'.$cols.'" class="text">
							<table class="rpt rpt_table rpt_table-bordered" width="100%">
							<tr>
								<td style="width:175px;" class="text_10b">Heard Type</td>
								<td style="width:175px;" class="text_10b">Count</td>
								<td style="width:175px;" class="text_10b">Insurance</td>
								<td style="width:175px;" class="text_10b">Patient</td>
								<td style="width:175px;" class="text_10b">Adjustment</td>
								<td style="width:175px;" class="text_10b">Revenue</td>
							</tr>
							';
							foreach($heardIdArr as $heard_key => $heard) {	
								if(count($operHeardCountArr[$heard]['COUNT'][$oldOperator])>0){
									$pdfHTML.='<tr><td >'.$heardResArr[$heard].'</td>
											<td style="text-align:right" >'.count($operHeardCountArr[$heard]['COUNT'][$oldOperator]).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['INSURANCE'][$oldOperator]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['PATIENT'][$oldOperator]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['ADJUST'][$oldOperator]),2).'&nbsp;</td>
											<td style="text-align:right" >'.numberformat(array_sum($operHeardCountArr[$heard]['AMOUNT'][$oldOperator]),2).'&nbsp;</td></tr>';
					
								}
							}
						$pdfHTML.='</table></td></tr>';
					}
				}
				$oldOperator = $operID;
				$oldFacility = $facilityId;
				$oldDoctor = $docId;
				
			//}
			$i++;
		}
		$strHTML.= '</table>';
		$pdfHTML.= '</table></page>';
		
		// GET Patients with NO HEARD ABOUT US SELECTED
		//include 'heard_about_us_no_report.php';
		//GRAND TOTAL OF All Having Heard About Us				
		$strHTML.='<br>
					<table class="rpt rpt_table rpt_table-bordered" width="100%">
					<tr> <td colspan="6" class="text_b_w">Grand Total</td></tr>
					<tr>
						<td style="width:175px;" class="text_10b">Heard Type</td>
						<td style="width:175px;" class="text_10b text-center">Count</td>
						<td style="width:175px;" class="text_10b text-center">Insurance</td>
						<td style="width:175px;" class="text_10b text-center">Patient</td>
						<td style="width:175px;" class="text_10b text-center">Adjustment</td>
						<td style="width:175px;" class="text_10b text-center">Revenue</td>
					</tr>
					';
		$totCount = $totIns = $totPat = $totAdu = $totAmt = "";
		foreach($heardIdArr as $heard_key => $heard) {
			if(count($grandCountArr[$heard]['COUNT'])>0){
				$totCount += (int)count($grandCountArr[$heard]['COUNT']);
				$totIns += array_sum($grandCountArr[$heard]['INSURANCE']);
				$totPat += array_sum($grandCountArr[$heard]['PATIENT']);
				$totAdu += array_sum($grandCountArr[$heard]['ADJUST']);
				$totAmt += array_sum($grandCountArr[$heard]['AMOUNT']);
				
				$strHTML.='<tr><td >'.$heardResArr[$heard].'</td>
						<td class="text-right" >'.count($grandCountArr[$heard]['COUNT']).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['INSURANCE']),2).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['PATIENT']),2).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['ADJUST']),2).'&nbsp;</td>
						<td class="text-right" >'.numberformat(array_sum($grandCountArr[$heard]['AMOUNT']),2).'&nbsp;</td></tr>';
			}
		}
		$strHTML.='<tr><td ><b>Grand Total:</b></td>
				<td class="text-right" ><b>'.$totCount.'&nbsp;</b></td>
				<td class="text-right" ><b>'.numberformat($totIns,2).'&nbsp</b></td>
				<td class="text-right" ><b>'.numberformat($totPat,2).'&nbsp;</b></td>
				<td class="text-right" ><b>'.numberformat($totAdu,2).'&nbsp;</b></td>
				<td class="text-right" ><b>'.numberformat($totAmt,2).'&nbsp;</b></td></tr>';
		$strHTML.='</table>';			
		
		$pdfHTML.='<page backtop="10mm" backbottom="10mm">';
				$pdfHTML.='<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>';
				
				$pdfHTML.='<page_header>
							<table class="rpt_table rpt rpt_table-bordered rpt_padding">
								<tr>
									<td width="530" class="text_b_w" align="left">&nbsp;<b>Grand Total - Heard About US</b></td>
									<td width="530" class="text_b_w" align="right">Created by '.$createdBy.' on '.$createdOn.'&nbsp;&nbsp;</td>
								</tr>
								<tr>
									<td width="530" class="text_b_w" align="left">Heard Type: '.str_replace("'","",$strShowSelType).'</td>
									<td width="530" class="text_b_w" align="right">Report for: '.$dispSearch.'&nbsp;&nbsp;&nbsp;Report Period: '.$dtEffectiveDate1.' to '.$dtEffectiveDate2.'&nbsp;&nbsp;</td>
								</tr>			
							</table>
							</page_header>';
		$pdfHTML.='<br>
					<table class="rpt_table rpt rpt_table-bordered rpt_padding">
					<tr> <td colspan="6" class="text_b_w" >Grand Total</td></tr>
					<tr>
						<td style="width:175px;" class="text_10b">Heard Type</td>
						<td style="width:175px;" class="text_10b">Count</td>
						<td style="width:175px;" class="text_10b">Insurance</td>
						<td style="width:175px;" class="text_10b">Patient</td>
						<td style="width:175px;" class="text_10b">Adjustment</td>
						<td style="width:175px;" class="text_10b">Revenue</td>
					</tr>
					';
		$totCount = $totIns = $totPat = $totAdu = $totAmt = "";			
		foreach($heardIdArr as $heard_key => $heard) {	
			if(count($grandCountArr[$heard]['COUNT'])>0){
				$totCount += (int)count($grandCountArr[$heard]['COUNT']);
				$totIns += array_sum($grandCountArr[$heard]['INSURANCE']);
				$totPat += array_sum($grandCountArr[$heard]['PATIENT']);
				$totAdu += array_sum($grandCountArr[$heard]['ADJUST']);
				$totAmt += array_sum($grandCountArr[$heard]['AMOUNT']);
				$pdfHTML.='<tr><td >'.$heardResArr[$heard].'</td>
						<td style="text-align:right" >'.count($grandCountArr[$heard]['COUNT']).'&nbsp;</td>
						<td style="text-align:right" >'.numberformat(array_sum($grandCountArr[$heard]['INSURANCE']),2).'&nbsp;</td>
						<td style="text-align:right" >'.numberformat(array_sum($grandCountArr[$heard]['PATIENT']),2).'&nbsp;</td>
						<td style="text-align:right" >'.numberformat(array_sum($grandCountArr[$heard]['ADJUST']),2).'&nbsp;</td>
						<td style="text-align:right" >'.numberformat(array_sum($grandCountArr[$heard]['AMOUNT']),2).'&nbsp;</td></tr>';
			}
		}
		$pdfHTML.='<tr><td >Grand Total: </td>
						<td class="text-right" >'.$totCount.'&nbsp;</td>
						<td class="text-right" >'.numberformat($totIns,2).'&nbsp;</td>
						<td class="text-right" >'.numberformat($totPat,2).'&nbsp;</td>
						<td class="text-right" >'.numberformat($totAdu,2).'&nbsp;</td>
						<td class="text-right" >'.numberformat($totAmt,2).'&nbsp;</td></tr>';
		$pdfHTML.='</table></page>';
	}
}
	
/*if($blIncludePatientAddress == 1){
	$strHTML ='';
	$pat_for_sch_imp=implode(',',$patientIDArr_lab);
	if(count($patientIDArr_lab)>0){
		$report_type="Address Labels";
		include_once "new_account_recall_letter.php";
	}
}
else*/
//{	
	
	$strPDF ='<style>'.file_get_contents('css/reports_pdf.css').'</style>
	<page backtop="10mm" backbottom="10mm">';
	if(sizeof($arrPatId) > 0 || $intTotNoHeard > 0){
		$showBtn = true;
		$csvData = $strCSS.$strHeader.$strHTML;
		$page_data =  $strCSS.$strHeader.$strHTML."<br>".$strHTML_Label_st.$strHTML_Label;
		$strHTML1= $strPDF.$pdfFooter.$pdfHeader.$pdfHTML.$chartImg.$strHTML_Label_St.$strHTML_Label_Footer.$strHTML_Label;
		$hasData = 1;
	}
	else{
		$page_data = ''; $strHTML1 ='';
	}
	$html_file_name = 'test_heard_about_us';
	file_put_contents('new_html2pdf/'.$html_file_name.'.html',$strHTML1);
	
	$printPdFBtn = 1;
	$file_location = write_html($strHTML1);
	if( $page_data )
		echo $page_data;
	else
		echo '<div class="text-center alert alert-danger">No Records Exists.</div>';
//}
?>
<div id="fcexpDiv" align="center"></div>