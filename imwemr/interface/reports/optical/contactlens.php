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
  PURPOSE : PRINTING INSTRUCION
  ACCESS TYPE : INCLUDED
 */
require_once("../../chart_notes/cl_functions.php");
//--- Get All Pending orders ----
//GET ALL LENS MANUFACTURER IN ARRAY
$arrLensManuf = getLensManufacturer();
$arr_colorIDname = getLensColorArr(false);
$arr_lensIDname = getLensCodeArr(false);

$dateFormat = get_sql_date_format();
$phpDateFormat = phpDateFormat();

$Start_date = getDateFormatDB($_POST["Start_date"]);
$End_date = getDateFormatDB($_POST["End_date"]);

$patientSearch = $_REQUEST['patientSearch'];
$searchKeyWord = $_POST['findBy'];
$searchBy = $_POST['patientFields'];
$flatSearch = 0;

$printFile = true;
$arrFacility = $_REQUEST['sc_name'];
$selPhysiciansDD = $physicians = implode(",", $_REQUEST['Physician']);

if (strstr($physicians, 'others')) {
    $flatSearch = 1;
    $selPhysiciansDD = substr($physicians, 0, -7);
}

$strFacility = '';
if (sizeof($arrFacility) > 0) {
    $strFacility = implode(",", $arrFacility);
}
$whereQuery = " pd.lname != 'doe'";
if ($_POST["patientId"] != '') {
    //$whereQuery.=" AND clpm.patient_id='".trim($_POST["patientId"])."'";
    $whereQuery .= " AND clm.patient_id='" . trim($_POST["patientId"]) . "'";
}

if ($strFacility != '') {
    $whereQuery .= " AND sa.sa_facility_id IN(" . $strFacility . ")";
}

if ($selPhysiciansDD != '' && $flatSearch == '0') {
    $whereQuery .= " AND clm.provider_id IN(" . $selPhysiciansDD . ")";
}
/* --GETTING TIME PERIOD-------- */
$day = date('w') - 1;
if ($day < 0) {
    $StartDay = 6;
} else {
    $StartDay = $day;
}
$newDate = date($phpDateFormat, mktime(0, 0, 0, date("m"), date("d") - $StartDay, date("Y")));
$monthDate = date($phpDateFormat, mktime(0, 0, 0, date("m"), '01', date("Y")));

$arr_quaMon = array('1' => 1, '2' => 4, '3' => 7, '4' => 10);
$quarter = $arr_quaMon[ceil(date('n') / 3)];

$quarter = $quarter < 10 ? '0' . $quarter : $quarter;
$quater_month_start = date($phpDateFormat, mktime(0, 0, 0, $quarter, 1, date('Y')));
$quater_month_end = date($phpDateFormat, mktime(0, 0, 0, $quarter + 3, 1 - 1, date('Y')));

//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}

	//--- CHANGE DATE FORMAT -------
	$Start_date = getDateFormatDB($Start_date);
	$End_date = getDateFormatDB($End_date);

/* --GETTING TIME PERIOD-------- */
if ($Start_date != "" && $End_date != "") {
    if ($whereQuery != '') {
        $whereQuery .= ' AND ';
    }
    $whereQuery .= "(DATE_FORMAT(clm.clws_savedatetime,'%Y-%m-%d') >='" . $Start_date . "' AND DATE_FORMAT(clm.clws_savedatetime,'%Y-%m-%d') <='" . $End_date . "')";
}

$chartJoin = '';
if ($_POST["orderMainStatus"] == "cl_order") {
    $cl_order=true;
    $whereQuery .= " AND chart_master_table.cl_order = 1 ";
    $chartJoin = " LEFT JOIN chart_master_table ON chart_master_table.id=clm.form_id";
}

//START CODE TO GET ORDER HISTORY
if ($whereQuery != '') {
    $whereQuery = ' WHERE ' . $whereQuery;
}

$orderHxQuery = "SELECT clm.clws_id, clm.patient_id, clm.provider_id, clm.cpt_evaluation_fit_refit, clm.clws_type, clm.clws_trial_number, 
clw.id as CLDETID, clw.clEye, clw.clType, clw.sclTypeOD_ID, clw.SclsphereOD, 
clw.SclCylinderOD, clw.SclaxisOD, clw.SclDiameterOD, clw.SclBcurveOD, clw.SclAddOD, clw.sclTypeOS_ID, clw.SclsphereOS, 
clw.SclCylinderOS, clw.SclaxisOS, clw.SclDiameterOS, clw.SclBcurveOS, clw.SclAddOS, 
clw.RgpTypeOD_ID, clw.RgpPowerOD, clw.RgpCylinderOD, clw.RgpAxisOD, clw.RgpDiameterOD, clw.RgpBCOD, clw.RgpAddOD, clw.RgpTypeOS_ID, clw.RgpPowerOS, clw.RgpCylinderOS, clw.RgpAxisOS, clw.RgpDiameterOS, 
clw.RgpBCOS, clw.RgpAddOS, clw.RgpCustomTypeOD_ID, clw.RgpCustomPowerOD, clw.RgpCustomCylinderOD, clw.RgpCustomAxisOD, clw.RgpCustomDiameterOD, clw.RgpCustomBCOD,
clw.RgpCustomPCWOD, 
clw.RgpAddOD, clw.RgpCustomTypeOS_ID, clw.RgpCustomPowerOS, clw.RgpCustomCylinderOS, clw.RgpCustomAxisOS, clw.RgpCustomDiameterOS, clw.RgpCustomBCOS, clw.RgpAddOS,
clw.RgpCustomPCWOS,
clw.sclTypeOU, clw.SclsphereOU, clw.SclCylinderOU, clw.SclaxisOU, clw.SclDiameterOU, clw.SclBcurveOU, clw.SclAddOU, 
clw.RgpTypeOU, clw.RgpPowerOU, clw.RgpDiameterOU, clw.RgpBCOU, clw.RgpAddOU, clw.RgpCustomTypeOU, clw.RgpCustomPowerOU, 
clw.RgpCustomDiameterOU, clw.RgpCustomBCOU, clw.RgpAddOU, 
clpm.auth_number, clpm.auth_amount, 
DATE_FORMAT(clm.clws_savedatetime,'" . $dateFormat . "') AS clws_savedatetime, clpm.operator_id, clpm.print_order_id, 
clpm.checkBoxShipToHomeAddress, clpm.ShipToHomeAddress, clpm.OrderedTrialSupply, clpd.LensBoxOD, clpd.LensBoxOD_ID, 
clpd.SubTotalOD, clpd.PriceOD, clpd.SubTotalOS, clpd.DiscountOD, clpd.DiscountOS, clpd.TotalOD, clpd.TotalOS, clpd.PaidOD, clpd.PaidOS, 
clpd.BalanceOD, clpd.LensBoxOS, clpd.BalanceOS, clpd.LensBoxOS_ID, clpd.PriceOS, clpm.totalCharges, clpd.colorNameIdList, clpd.lensNameIdList,
clpd.colorNameIdListOS, clpd.lensNameIdListOS, clpm.prescripClwsId, clpd.QtyOD, clpd.QtyOS, clpd.cl_det_id, 
clpd.LensBoxOU, clpd.LensBoxOU_ID, clpd.SubTotalOU, clpd.TotalOU, clpd.PaidOU, clpd.BalanceOU, clpd.DiscountOU, clpd.PriceOU, clpd.QtyOU, 
clpd.colorNameIdListOU, clpd.lensNameIdListOU, clpm.lensComment, clpm.OrderedComment, clpm.ReceivedComment, clpm.NotifiedComment, 
clpm.PickedUpComment, clpm.order_status, 
pd.lname, pd.fname, pd.mname, sa.sa_facility_id, facility.id as 'facility_id', facility.name as 'facility_name',   
(SELECT count(contactlensworksheet_det.id) AS ttotal FROM contactlensworksheet_det where contactlensworksheet_det.clws_id=clm.clws_id 
GROUP BY contactlensworksheet_det.clws_id) as totalrows
FROM contactlensmaster clm 
JOIN contactlensworksheet_det clw ON clw.clws_id= clm.clws_id 
LEFT JOIN schedule_appointments sa ON (sa.sa_patient_id = clm.patient_id AND sa.sa_app_start_date = clm.dos)
LEFT JOIN patient_data pd ON (pd.id = clm.patient_id) 
LEFT JOIN facility ON (facility.id = sa.sa_facility_id)
LEFT JOIN clprintorder_master clpm ON clpm.clws_id= clm.clws_id 
LEFT JOIN clprintorder_det clpd ON clpd.cl_det_id = clw.id  
" . $chartJoin . " 
" . $whereQuery . " 
GROUP BY clw.id 
ORDER BY ISNULL(facility.id), facility.id, clm.clws_savedatetime DESC, clw.clws_id DESC, clw.clEye";

$orderHxRes = imw_query($orderHxQuery) or die(imw_error());
$orderHxNumRow = imw_num_rows($orderHxRes);


//END CODE TO GET ORDER HISTORY
$spaceNbsp = '&nbsp;';
$statusOptions = array('Pending', 'Ordered', 'Received', 'Dispensed');
ob_start();
$arr_data = array();
$cnt = 1;
?>


<script type="text/javascript">
    // FUNCTION TO COLLAPSE EXPAND
    function toggleTbl(tbl_id) {
        $("." + tbl_id).toggle("fast");

//        if ($('#icon_' + tbl_id).get(0) != 'undefined') {
//
//            if ($('#icon_' + tbl_id).attr('class') == 'ui-icon ui-icon-circle-arrow-n fl') {
//                $('#icon_' + tbl_id).removeClass('ui-icon ui-icon-circle-arrow-n fl');
//                $('#icon_' + tbl_id).addClass('ui-icon ui-icon-circle-arrow-s fl');
//            } else {
//                $('#icon_' + tbl_id).removeClass('ui-icon ui-icon-circle-arrow-s fl');
//                $('#icon_' + tbl_id).addClass('ui-icon ui-icon-circle-arrow-n fl');
//            }
//        }
    }
</script>
<?php
$htmldata='';
$pdfdata='';
if ($orderHxNumRow > 0) {
    $str_html = '<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
        <tr class="rpt_headers">
            <td class="rptbx1" style="width:342px;">&nbsp;Contact Lens ' . $status . ' Orders Report</td>
            <td class="rptbx2" style="width:350px;">&nbsp;Date: From ' . $Start_date . ' To ' . $End_date . '</td>
            <td class="rptbx3" style="width:350px;">&nbsp;Created by ' . $op_name . ' on ' . $curDate . '&nbsp;</td>
        </tr>
    </table>

    <table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
        <tr>
            <td class="text_b_w" style="padding-left:8px; width:80px;">Patient Name</td>
            <td class="text_b_w" style="width:20px;">Eye</td>
            <td class="text_b_w" style="width:45px;">Type</td>
            <td class="text_b_w" style="width:20px;">Color</td>
            <td class="text_b_w" style="width:20px;">LC</td>
            <td class="text_b_w" style="width:20px;">S</td>
            <td class="text_b_w" style="width:20px;">C</td>
            <td class="text_b_w" style="width:20px;">A</td>
            <td class="text_b_w" style="width:20px;">Dia</td>
            <td class="text_b_w" style="width:20px;">BC</td>
            <td class="text_b_w" style="width:20px;" >Add</td>
            <td class="text_b_w" style="width:35px;" >PC/W</td>
            <td class="text_b_w" style="width:20px;">Qty.</td>
            <td class="text_b_w" style="width:30px;">Cost</td>
            <td class="text_b_w" style="width:75px;">Auth Amt</td>
            <td class="text_b_w" style="width:50px;">Balance</td>
            <td class="text_b_w" style="width:40px;">Total</td>
            <td class="text_b_w" style="width:65px;">Date</td>
            <td class="text_b_w" style="width:70px;">Ship To</td>
            <td class="text_b_w" style="width:50px;">Order</td>
            <td class="text_b_w" style="width:60px;">Status</td>
        </tr>';

    $counterForClass = 1;
    $rowspan = 1;
    $prev_ptid = 0;
    $new_ptrow = false;
    $new_facility = '000';
    $prev_clws_id = 0;
    $cssclasssName = "alt";
    $old_facility = '';
    while ($orderHxRow = imw_fetch_array($orderHxRes)) {

        if ($prev_clws_id != $orderHxRow['clws_id']) {//echo 'prev_clws_id='.$prev_clws_id." clws_id=".$orderHxRow['clws_id'].'<br>';
            if ($orderHxRow['clws_id'] != NULL)
                $prev_clws_id = $orderHxRow['clws_id'];
            $new_ptrow = true;
            if ($cssclasssName != "") {
                $cssclasssName = "";
                $bgclass = 'bg1';
            } else {
                $cssclasssName = "alt";
                $bgclass = 'bg2';
            }
        } else {
            $rowspan++;
            $new_ptrow = false;
        }

        $orderHxclEye = $orderHxRow['clEye'];

        $unusedAuthorization = $orderHxRow['auth_number'];
        $AuthAmount = $orderHxRow['auth_amount'];

        $orderHxDateTime = $orderHxRow['clws_savedatetime'];
        if ($orderHxDateTime != '0000-00-00') {
            $orderHxDate = $orderHxDateTime;
        }

        $orderHxOperatorId = $orderHxRow['operator_id'];
        $operatorInitial = '';
        $dispNotes = '';

        $print_order_id = $orderHxRow['print_order_id'];
        $clws_id = $orderHxRow['clws_id'];
        $SubTotalOD = $orderHxRow['SubTotalOD'];
        $SubTotalOS = $orderHxRow['SubTotalOS'];
        $SubTotalOU = $orderHxRow['SubTotalOU'];
        //$Discount	 		= $orderHxRow['Discount'.$orderHxclEye];
        $TotalOD = $orderHxRow['TotalOD'];
        $TotalOS = $orderHxRow['TotalOS'];
        $TotalOU = $orderHxRow['TotalOU'];
        $PaidOD = $orderHxRow['PaidOD'];
        $PaidOS = $orderHxRow['PaidOS'];
        $PaidOU = $orderHxRow['PaidOU'];

        if ($orderHxRow['clType'] == 'scl') {
            $orderHxType = $arrLensManuf[$orderHxRow['sclType' . $orderHxclEye . "_ID"]]['det'];
        } else if ($orderHxRow['clType'] == 'rgp') {
            $orderHxType = $arrLensManuf[$orderHxRow['RgpType' . $orderHxclEye . "_ID"]]['det'];
        } else if ($orderHxRow['clType'] == 'cust_rgp') {
            $orderHxType = $arrLensManuf[$orderHxRow['RgpCustomType' . $orderHxclEye . "_ID"]]['det'];
        }

        $Balance = $orderHxRow['Balance' . $orderHxclEye];
        //$clExam				= $orderHxRow['cpt_evaluation_fit_refit'];
        $TotalBalanceODOS = $orderHxRow['totalCharges'];
        $colorNameIdList = $orderHxRow['colorNameIdList'];
        $lensNameIdList = $orderHxRow['lensNameIdList'];
        $colorNameIdListOS = $orderHxRow['colorNameIdListOS'];
        $lensNameIdListOS = $orderHxRow['lensNameIdListOS'];
        $colorNameIdListOU = $orderHxRow['colorNameIdListOU'];
        $lensNameIdListOU = $orderHxRow['lensNameIdListOU'];
        $prescripClwsId = $orderHxRow['prescripClwsId'];
        $orderHxCost = $orderHxRow['SubTotal' . $orderHxclEye];
        $orderHxQty = $orderHxRow['Qty' . $orderHxclEye];
        $lensComment = $orderHxRow['lensComment'];
        $orderstatus = $orderHxRow['order_status'];
        $OrderedTrialSupply = $orderHxRow['OrderedTrialSupply'];
        $shipto = '';
        if ($orderHxRow['checkBoxShipToHomeAddress']) {
            $shipto = $orderHxRow['checkBoxShipToHomeAddress'] == 'HomeAddressYes' ? 'Home' . '<br>' . $orderHxRow['ShipToHomeAddress'] : 'Office';
        }
        $OrderedComment = trim(stripslashes($orderHxRow['OrderedComment']));
        $ReceivedComment = trim(stripslashes($orderHxRow['ReceivedComment']));
        $NotifiedComment = trim(stripslashes($orderHxRow['NotifiedComment']));
        $PickedUpComment = trim(stripslashes($orderHxRow['PickedUpComment']));

        $displayOtherCmnt = '<br>';
        if ($OrderedComment) {
            $displayOtherCmnt .= $OrderedComment . '<br><br>';
        } //'Date Ordered: '.		
        if ($ReceivedComment) {
            $displayOtherCmnt .= $ReceivedComment . '<br><br>';
        }//'Date Received: '.
        if ($NotifiedComment) {
            $displayOtherCmnt .= $NotifiedComment . '<br><br>';
        }//'Date Notified: '.			
        if ($PickedUpComment) {
            $displayOtherCmnt .= $PickedUpComment . '<br><br>';
        }//'Date Picked Up: '.		
        //START CODE TO GET COLOR-NAME/LENS-CODE
        $arr_colorIDname = getLensColorArr(false);
        if ($orderHxclEye == 'OD') {
            $colorNameList = $arr_colorIDname[$colorNameIdList];
        } else if ($orderHxclEye == 'OS') {
            $colorNameList = $arr_colorIDname[$colorNameIdListOS];
        } else if ($orderHxclEye == 'OU') {
            $colorNameList = $arr_colorIDname[$colorNameIdListOU];
        }

        $arr_lensIDname = getLensCodeArr(false);
        if ($orderHxclEye == 'OD') {
            $lensNameList = $arr_lensIDname[$lensNameIdList];
        } else if ($orderHxclEye == 'OS') {
            $lensNameList = $arr_lensIDname[$lensNameIdListOS];
        } else if ($orderHxclEye == 'OU') {
            $lensNameList = $arr_lensIDname[$lensNameIdListOU];
        }
        //END CODE TO GET LENS-CODE

        $clws_type = '';
        $clws_type = '<br><br><strong>' . $orderHxRow['clws_type'] . '</strong>';
        if ($clws_type == 'Current Trial') {
            $clws_type = '<br><br><strong>Trial-' . $orderHxRow['clws_trial_number'] . '</strong>';
        }

        //---GET DISPENSED NOTES--------
        if ($orderstatus == 'Dispensed') {
            $dispNotes = $arrDispNotes[$print_order_id];
        }
        //------------------------------

        if ($orderHxRow['CLDETID'] > 0) {
            $clEye = $orderHxRow['clEye'];
            if ($orderHxRow['clType'] == 'scl') {
                $sdata = $orderHxRow['Sclsphere' . $clEye];
				$cdata = $orderHxRow['SclCylinder' . $clEye];
				$adata = $orderHxRow['Sclaxis' . $clEye];
				$diadata = $orderHxRow['SclDiameter' . $clEye];
				$bcdata = $orderHxRow['SclBcurve' . $clEye];
				$adddata = $orderHxRow['SclAdd' . $clEye];
				$pcwdata = '&nbsp;';
            }//end of scl
            else if ($orderHxRow['clType'] == 'rgp') {
                $sdata = $orderHxRow['RgpPower' . $clEye];
				$cdata = $orderHxRow['RgpCylinder' . $clEye];
				$adata = $orderHxRow['RgpAxis' . $clEye];
				$diadata = $orderHxRow['RgpDiameter' . $clEye];
				$bcdata = $orderHxRow['RgpBC' . $clEye];
				$adddata = $orderHxRow['RgpAdd' . $clEye];
				$pcwdata = '&nbsp;';
            }//end of rgp
            else if ($orderHxRow['clType'] == 'cust_rgp') {
                $sdata = $orderHxRow['RgpCustomPower' . $clEye];
				$cdata = $orderHxRow['RgpCustomCylinder' . $clEye];
				$adata = $orderHxRow['RgpCustomAxis' . $clEye];
				$diadata = $orderHxRow['RgpCustomDiameter' . $clEye];
				$bcdata = $orderHxRow['RgpCustomBC' . $clEye];
				$adddata = $orderHxRow['RgpCustomAdd' . $clEye];
				$pcwdata = $orderHxRow['RgpCustomPCW' . $clEye];
            }//end of custom rgp		
        }

        $dlr = $GLOBALS['currency'];
        $orderStatus = $orderHxRow['order_status'];
        $patient_name = $orderHxRow['lname'] . ', ';
        $patient_name .= $orderHxRow['fname'] . ' ';
        $patient_name .= $orderHxRow['mname'];
        $patient_name = ucfirst(trim($patient_name));
        if ($patient_name[0] == ',') {
            $patient_name = substr($patient_name, 1);
        }
        $patientName = $patient_name . "-" . $orderHxRow['patient_id'];

        if ($orderHxRow['facility_id'] == 'NULL' || $orderHxRow['facility_id'] == '') {
            $orderHxRow['facility_id'] = 'noDefined';
        }

        if ($old_facility != $orderHxRow['facility_id']) {
            $orderHxRow['facility_name'] = ($orderHxRow['facility_name'] == '' || $orderHxRow['facility_name'] == 'NULL') ? 'Not Defined' : $orderHxRow['facility_name'];
            $htmldata .= '<tr><td colspan="22" class="text_b_w" style="font-weight: bold; cursor:pointer;" onclick="javascript:toggleTbl(\'facID'.$orderHxRow['facility_id'].'\');">Facility: ' . $orderHxRow['facility_name'] . '</td></tr>';
            //$htmldata .= '<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" id="facID'.$orderHxRow['facility_id'].'">';
            $pdfdata .= '<tr><td colspan="22" class="text_b_w" style="font-weight: bold;">Facility: ' . $orderHxRow['facility_name'] . '</td></tr>';
        }

        
        $htmldata .= '<tr class="facID'.$orderHxRow['facility_id'].'">';
        $pdfdata .= '<tr>';
        if ($new_ptrow) {
            $htmldata .= '<td style="padding-left:8px; width:80px;">'.$patientName.$clws_type.'</td>';
            $pdfdata .= '<td style="padding-left:8px; width:80px;" rowspan="' . $orderHxRow['totalrows'] . '">'.$patientName.'</td>';
        } else {
            if(strtolower($orderHxclEye) == 'os') {
                $htmldata .='<td style="width:80px;">';
                if ($lensComment || $displayOtherCmnt != '<br>') {
                    $htmldata .= '<br><strong>Comments:</strong> ';
                    if ($lensComment) {
                        $htmldata .= "<br>" . $lensComment . '<br>';
                    }
                    if ($displayOtherCmnt) {
                        $htmldata .= $displayOtherCmnt;
                    }
                } else {
                    $htmldata .= $spaceNbsp;
                }
                $htmldata .='</td>';
            }
            if(strtolower($orderHxclEye) == 'ou') {
                $htmldata .='<td style="width:80px;"></td>';
            }
            if(strtolower($orderHxclEye) == 'od') {
                $htmldata .='<td style="width:80px;"></td>';
            }
        }
        $htmldata .= '<td style="width:20px;color:#3f51b5;">' . $orderHxclEye . '</td>';
        $pdfdata .= '<td style="width:20px;color:#3f51b5;">' . $orderHxclEye . '</td>';
        $htmldata .= '<td style="width:45px;">'.$orderHxType . $LabelsTrial.'</td>';
        $pdfdata .= '<td style="width:45px;">'.$orderHxType . $LabelsTrial.'</td>';
        $htmldata .= '<td style="width:30px;">'.$colorNameList. '</td>';
        $pdfdata .= '<td style="width:30px;">'.$colorNameList. '</td>';
        $htmldata .= '<td style="width:20px;">'.$lensNameList. '</td>';
        $pdfdata .= '<td style="width:20px;">'.$lensNameList. '</td>';
        $htmldata .= '<td style="width:20px;">'.$sdata.'</td>
                    <td style="width:20px;">'.$cdata.'</td>
                    <td style="width:20px;">'.$adata.'</td>
                    <td style="width:20px;">'.$diadata.'</td>
                    <td style="width:20px;">'.$bcdata.'</td>
                    <td style="width:20px;" >'.$adddata.'</td>
                    <td style="width:35px;" >'.$pcwdata.'</td>';
                    
        $pdfdata .= '<td style="width:20px;">'.$sdata.'</td>
                    <td style="width:20px;">'.$cdata.'</td>
                    <td style="width:20px;">'.$adata.'</td>
                    <td style="width:20px;">'.$diadata.'</td>
                    <td style="width:20px;">'.$bcdata.'</td>
                    <td style="width:20px;" >'.$adddata.'</td>
                    <td style="width:35px;" >'.$pcwdata.'</td>';
                    
        $htmldata .= '<td style="width:20px;">'.$orderHxQty. '</td>';
        $pdfdata .= '<td style="width:20px;">'.$orderHxQty. '</td>';
        $htmldata .= '<td style="width:30px;">'.$orderHxCost. '</td>';
        $pdfdata .= '<td style="width:30px;">'.$orderHxCost. '</td>';
        $htmldata .= '<td style="width:75px;">'.$dlr . $AuthAmount. '</td>';
        $pdfdata .= '<td style="width:65px;">'.$dlr . $AuthAmount. '</td>';
        $htmldata .= '<td style="width:50px;">'.$dlr . $Balance. '</td>';
        $pdfdata .= '<td style="width:50px;">'.$dlr . $Balance. '</td>';
        $htmldata .= '<td style="width:40px;">'.$dlr . $TotalBalanceODOS. '</td>';
        $pdfdata .= '<td style="width:40px;">'.$dlr . $TotalBalanceODOS. '</td>';
        $htmldata .= '<td style="width:65px;">'.$orderHxDate. '</td>';
        $pdfdata .= '<td style="width:65px;">'.$orderHxDate. '</td>';
        $htmldata .= '<td style="width:70px;">'.$shipto. '</td>';
        $pdfdata .= '<td style="width:70px;">'.$shipto. '</td>';
        $htmldata .= '<td style="width:50px;">'.$OrderedTrialSupply. '</td>';
        $pdfdata .= '<td style="width:50px;">'.$OrderedTrialSupply. '</td>';
        $htmldata .= '<td style="width:60px;">'.$orderstatus."<br>" . $dispNotes.'</td>';
        $pdfdata .= '<td style="width:60px;">'.$orderstatus."<br>" . $dispNotes.'</td>';
        $htmldata .= '</tr>';
        $pdfdata .= '</tr>';

        $counterForClass++;
        $old_facility = $orderHxRow['facility_id'];
    }
    $htmldata .= '</table>';
    $pdfdata .= '</table>';
}

if ($htmldata != '') {
    echo $str_html . $htmldata;
} else {
    echo '<div class="text-center alert alert-info">No Record Found.</div>';
}


if ($printFile == true && $pdfdata != '') {
    $styleHTML = '<style>' . file_get_contents('../css/reports_html.css') . '</style>';
    $csv_file_data = $styleHTML . $str_html . $htmldata;

    $stylePDF = '<style>' . file_get_contents('../css/reports_pdf.css') . '</style>';
    $strHTML = $stylePDF . $str_html . $pdfdata;

    $file_location = write_html($strHTML);
}
if ($output_option == 'view' || $output_option == 'output_csv') {
    echo $csv_file_data;
}
?>