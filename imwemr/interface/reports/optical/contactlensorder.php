<?php
//---- File To view The All Pending Contact Lens Orders ----------
require_once("../../chart_notes/cl_functions.php");
require_once('../../../library/classes/cls_common_function.php');

$selPhysicians='';
if ($_REQUEST['Physician'] || $_GET['selPhysicians']) {
    $selPhysicians = implode(',', $_REQUEST['Physician']);
    if($_GET['selPhysicians']) {
        $selPhysicians = $_GET['selPhysicians'];
    }
}
$selFacilities='';
if ($_REQUEST['sc_name'] || $_GET['selFacilities']) {
    $selFacilities = implode(',', $_REQUEST['sc_name']);
    if($_GET['selFacilities']) {
        $selFacilities = $_GET['selFacilities'];
    }
}

$op_name_arr = preg_split('/, /', strtoupper($_SESSION['authProviderName']));
$op_name = $op_name_arr[1][0];
$op_name .= $op_name_arr[0][0];

$Start_date = getDateFormatDB($_POST["Start_date"]);
$End_date = getDateFormatDB($_POST["End_date"]);

$show_remove_btn = false;
$printFile=true;
$status = '0';
switch ($_REQUEST["orderMainStatus"]) {
    case '0':
        $status = 'Pending';
        break;
    case '1':
        $status = 'Ordered';
        break;
    case '2':
        $status = 'Received';
        break;
    case '3':
        $status = 'Dispensed';
        break;
}

$order = $_REQUEST["orderMainStatus"]+1;
switch ($order) {
    case '0':
        $order = 'Pending';
        break;
    case '1':
        $order = 'Ordered';
        break;
    case '2':
        $order = 'Received';
        break;
    case '3':
        $order = 'Dispensed';
        break;
    case '4':
        $order = '0';
        break;
}

if ($_POST['form_submitted']) {
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
	
	$qryPart = '';
    if (empty($selPhysicians) == false) {
        $qryPart = " AND if(clpm.provider_id>0, clpm.provider_id IN(". $selPhysicians."), clpm.technician_id IN(". $selPhysicians."))";
    }
    if (empty($selFacilities) == false) {
		$qryPart .= " AND pd.default_facility IN(" . $selFacilities . ")";
    }

    if ($_POST["Start_date"] != "" && $_POST["End_date"] != "") {
        $qryPart .= " AND (DATE_FORMAT(clpm.print_order_savedatetime,'%Y-%m-%d') BETWEEN '" . $Start_date . "' AND '" . $End_date . "')";
    }

    $sql_clpm_facility = "	SELECT clpm.patient_id, pd.default_facility as facility_id,posft.facility_name, clpm.dos 
                            FROM clprintorder_master clpm
                            INNER JOIN patient_data pd ON (pd.id = clpm.patient_id)
                            LEFT JOIN pos_facilityies_tbl posft ON (posft.pos_facility_id = pd.default_facility)
                            WHERE clpm.order_status = '".$status."' AND pd.lname != 'doe' $qryPart 
                            GROUP BY pd.default_facility
                            ORDER BY posft.facility_name ASC ";
    $res_clpm_facility = imw_query($sql_clpm_facility) or die(imw_error());
    $num_rows_clpm_facility = imw_num_rows($res_clpm_facility);

    if ($num_rows_clpm_facility > 0) {
        if (isset($_GET["update_order_id"]) && $_GET["update_order_id"] > 0) {
            $order_IDs = $_GET["update_order_id"];
            $orderUpQuery = "UPDATE clprintorder_master SET order_status='".$order."' WHERE print_order_id IN (".$order_IDs.") AND order_status='".$status."'";
            $orderHxRes = imw_query($orderUpQuery) or die(imw_error());
        }
        ?>
        <table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
                <tr class="rpt_headers">
                    <td class="rptbx1" style="width:342px;">&nbsp;Contact Lens <?php echo $status;?> Orders Report</td>
                    <td class="rptbx2" style="width:350px;">&nbsp;Date: From <?php echo $Start_date . ' To ' . $End_date; ?></td>
                    <td class="rptbx3" style="width:350px;">&nbsp;Created by <?php echo $op_name . ' on ' . $curDate; ?>&nbsp;</td>
                </tr>
            </table>
            <table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
                <tr>
                    <td class="text_b_w" style="width:22px;"><input type="checkbox" name="select_all" id="chk_select_all"></td>
                    <td class="text_b_w" style="padding-left:8px; width:145px;">Patient Name</td>
                    <td class="text_b_w" style="width:30px;">Eye</td>
                    <td class="text_b_w" style="width:125px;">Type</td>
                    <td class="text_b_w" style="width:45px;">Color</td>
                    <td class="text_b_w" style="width:35px;">LC</td>
                    <td class="text_b_w" style="width:45px;">S</td>
                    <td class="text_b_w" style="width:45px;">C</td>
                    <td class="text_b_w" style="width:45px;">A</td>
                    <td class="text_b_w" style="width:25px;">Dia</td>
                    <td class="text_b_w" style="width:25px;">BC</td>
                    <td class="text_b_w" style="width:30px;" >Add</td>
                    <td class="text_b_w" style="width:45px;" >PC/W</td>
                    <td class="text_b_w" style="width:30px;">Qty.</td>
                    <td class="text_b_w" style="width:75px;">Cost</td>
                    <td class="text_b_w nowrap" style="width:85px;">Auth Amt</td>
                    <td class="text_b_w" style="width:78px;">Balance</td>
                    <td class="text_b_w" style="width:70px;">Total</td>
                    <td class="text_b_w" style="width:80px;">Date</td>
                    <td class="text_b_w" style="width:80px;">Ship To</td>
                    <td class="text_b_w" style="width:80px;">Action</td>
                </tr>
            </table>
<?php
        while ($row = imw_fetch_assoc($res_clpm_facility)) {
            $facility_id = $row['facility_id'];
            $authUserID = $_SESSION['authId'];
            $flatSearch = $_REQUEST['flatSearch'];
            $dayReport = $_REQUEST['dayReport'];
            $date_of_service_contactlen=$row['dos'];
            $patient_id_contactlen=$row['patient_id'];

            $facility_name = $row['facility_id']=='' ? getFacilityByDOS($patient_id_contactlen, $date_of_service_contactlen) : $row['facility_name'];

            //GET ALL LENS MANUFACTURER IN ARRAY
            $arrLensManuf = getLensManufacturer();

            $dateFormat = get_sql_date_format();
            $phpDateFormat = phpDateFormat();


            $orderHxQuery = "SELECT clm.cpt_evaluation_fit_refit, 
                            clpm.auth_number, clpm.auth_amount, clpm.patient_id,
                            DATE_FORMAT(clpm.print_order_savedatetime,'" . $dateFormat . "') AS print_order_savedatetime, 
                            clpm.operator_id, clpm.print_order_id, clpm.clws_id, clpm.checkBoxShipToHomeAddress, clpm.ShipToHomeAddress, clpm.OrderedTrialSupply, 
                            clpd.LensBoxOD, clpd.LensBoxOD_ID, clpd.SubTotalOD, clpd.TotalOD, clpd.PaidOD, clpd.BalanceOD, clpd.DiscountOD, clpd.PriceOD, clpd.QtyOD, clpd.colorNameIdList, clpd.lensNameIdList,
                            clpd.LensBoxOS, clpd.LensBoxOS_ID, clpd.SubTotalOS, clpd.TotalOS, clpd.PaidOS, clpd.BalanceOS, clpd.DiscountOS, clpd.PriceOS, clpd.QtyOS, clpd.colorNameIdListOS, clpd.lensNameIdListOS,
                            clpd.LensBoxOU, clpd.LensBoxOU_ID, clpd.SubTotalOU, clpd.TotalOU, clpd.PaidOU, clpd.BalanceOU, clpd.DiscountOU, clpd.PriceOU, clpd.QtyOU, clpd.colorNameIdListOU, clpd.lensNameIdListOU,
                            clpm.totalCharges, clpd.colorNameIdList, clpd.lensNameIdList,
                            clpm.prescripClwsId, clpd.cl_det_id, 
                            clpm.lensComment, clpm.OrderedComment, clpm.ReceivedComment, clpm.NotifiedComment, clpm.PickedUpComment, clpm.order_status, 
                            pd.lname, pd.fname, pd.mname,
                            (SELECT count(clprintorder_det.id) AS ttotal FROM clprintorder_det where 
                            clprintorder_det.print_order_id=clpm.print_order_id AND clpm.order_status='".$status."' GROUP BY clprintorder_det.print_order_id
            )as totalrows
                            FROM 
                            clprintorder_master clpm 
                            LEFT JOIN 
                            contactlensmaster clm ON clm.clws_id = clpm.clws_id  
                            LEFT JOIN 
                            clprintorder_det clpd ON (clpd.print_order_id = clpm.print_order_id) 
                            LEFT JOIN 
                            patient_data pd ON (pd.id = clpm.patient_id) 
                            WHERE 
                            clpm.order_status='" . $status . "' AND pd.lname != 'doe' 
                            AND pd.default_facility = '" . $facility_id . "' $qryPart
                            ORDER BY   clpm.print_order_savedatetime DESC";
            //echo $orderHxQuery;
            $orderHxRes = imw_query($orderHxQuery) or die(imw_error());
            $orderHxNumRow = imw_num_rows($orderHxRes);

            //END CODE TO GET ORDER HISTORY
            $spaceNbsp = '&nbsp;';
            ?>
            
            <table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
                <tr><td class="text_b_w" style="font-weight: bold;">Facility: <?php echo  $facility_name;?></td></tr>
            </table>
            <table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
                <tr>
                    <td style="width:22px;"></td>
                    <td style="padding-left:8px; width:145px;"></td>
                    <td style="width:30px;"></td>
                    <td style="width:125px;"></td>
                    <td style="width:45px;"></td>
                    <td style="width:35px;"></td>
                    <td style="width:45px;"></td>
                    <td style="width:45px;"></td>
                    <td style="width:45px;"></td>
                    <td style="width:25px;"></td>
                    <td style="width:25px;"></td>
                    <td style="width:30px;" ></td>
                    <td style="width:45px;"></td>
                    <td style="width:30px;"></td>
                    <td style="width:75px;"></td>
                    <td style="width:45px;"></td>
                    <td style="width:85px;"></td>
                    <td style="width:65px;"></td>
                    <td style="width:70px;"></td>
                    <td style="width:80px;"></td>
                    <td style="width:80px;"></td>
                </tr>
            <?php
            if ($orderHxNumRow > 0) {
                $show_remove_btn = true;
                $counterForClass = 1;
                $rowspan = 1;
                $prev_ptid = 0;
                $new_ptrow = false;
                $new_facility = '000';
                $cssclasssName = "alt";
                $old_facility = '';

                while ($orderHxRow = imw_fetch_array($orderHxRes)) {
                    if ($prev_ptid != $orderHxRow['print_order_id']) {
                        $prev_ptid = $orderHxRow['print_order_id'];
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

                    if ($orderHxRow['PriceOD'] != '') {
                        $orderHxclEye = 'OD';
                    } else if ($orderHxRow['PriceOS'] != '') {
                        $orderHxclEye = 'OS';
                    } else if ($orderHxRow['PriceOU'] != '') {
                        $orderHxclEye = 'OU';
                    }

                    $unusedAuthorization = $orderHxRow['auth_number'];
                    $AuthAmount = $orderHxRow['auth_amount'];

                    $orderHxDateTime = $orderHxRow['print_order_savedatetime'];
                    if ($orderHxDateTime != '0000-00-00') {
                        $orderHxDate = $orderHxDateTime;
                    }

                    $orderHxOperatorId = $orderHxRow['operator_id'];
                    $operatorInitial = '';

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

                    $orderHxType = $arrLensManuf[$orderHxRow['LensBox' . $orderHxclEye . "_ID"]]['det'];
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
                    $shipto = $orderHxRow['checkBoxShipToHomeAddress'] == 'HomeAddressYes' ? 'Home' . '<br>' . $orderHxRow['ShipToHomeAddress'] : 'Pt.Pick';
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


                    if ($orderHxRow['cl_det_id'] > 0) {
                        /* ----------------------------- */
                        $clw_query = "SELECT clw.clEye, clw.clType, 
		clw.sclTypeOD, clw.SclsphereOD, clw.SclCylinderOD, clw.SclaxisOD, clw.SclDiameterOD, clw.SclBcurveOD, clw.SclAddOD, clw.RgpTypeOD,
		clw.RgpPowerOD, clw.RgpCylinderOD, clw.RgpAxisOD, clw.RgpDiameterOD, clw.RgpBCOD, clw.RgpAddOD, clw.RgpCustomTypeOD,
		clw.RgpCustomPowerOD, clw.RgpCustomCylinderOD, clw.RgpCustomAxisOD, clw.RgpCustomDiameterOD, clw.RgpCustomBCOD, clw.RgpAddOD, RgpCustomPCWOD,
		
		clw.sclTypeOS, clw.SclsphereOS, clw.SclCylinderOS, clw.SclaxisOS, clw.SclDiameterOS, clw.SclBcurveOS, clw.SclAddOS, clw.RgpTypeOS,
		clw.RgpPowerOS, clw.RgpCylinderOS, clw.RgpAxisOS, clw.RgpDiameterOS, clw.RgpBCOS, clw.RgpAddOS, clw.RgpCustomTypeOS, clw.RgpCustomPowerOS,
		clw.RgpCustomCylinderOS, clw.RgpCustomAxisOS, clw.RgpCustomDiameterOS, clw.RgpCustomBCOS, clw.RgpAddOS, RgpCustomPCWOS,
		
		clw.sclTypeOU, clw.SclsphereOU, clw.SclCylinderOU, clw.SclaxisOU, clw.SclDiameterOU, clw.SclBcurveOU, clw.SclAddOU, clw.RgpTypeOU,
		clw.RgpPowerOU, clw.RgpDiameterOU, clw.RgpBCOU, clw.RgpAddOU, clw.RgpCustomTypeOU, clw.RgpCustomPowerOU, clw.RgpCustomDiameterOU, clw.RgpCustomBCOU,
		clw.RgpAddOU 
		
		FROM contactlensworksheet_det clw WHERE clw.id = '" . $orderHxRow['cl_det_id'] . "'";
                        $clw_result = imw_query($clw_query);
                        if ($clw_result && imw_num_rows($clw_result) > 0) {
                            $sclrgpcust_data = '
			<table cellpadding="0" cellspacing="0" class="text11" border="0">';
                            while ($workSheetrs = imw_fetch_array($clw_result)) {
                                $clEye = $workSheetrs['clEye'];
                                if ($workSheetrs['clType'] == 'scl') {
                                    $sclrgpcust_data .= '
					<tr class="alignCenter">
						<td style="width:45px;" valign="top">' . $workSheetrs['Sclsphere' . $clEye] . '</td>
						<td style="width:45px;" valign="top">' . $workSheetrs['SclCylinder' . $clEye] . '</td>
						<td style="width:30px;" valign="top">' . $workSheetrs['Sclaxis' . $clEye] . '</td>
						<td style="width:35px;" valign="top">' . $workSheetrs['SclDiameter' . $clEye] . '</td>
						<td style="width:35px;" valign="top">' . $workSheetrs['SclBcurve' . $clEye] . '</td>
						<td style="width:40px;" valign="top">' . $workSheetrs['SclAdd' . $clEye] . '</td>
						<td style="width:40px;" valign="top">&nbsp;</td>
				   </tr>';
                                }//end of scl
                                else if ($workSheetrs['clType'] == 'rgp') {
                                    $sclrgpcust_data .= '
					<tr class="alignCenter">
						<td style="width:45px;" valign="top">' . $workSheetrs['RgpPower' . $clEye] . '</td>
						<td style="width:45px;" valign="top">' . $workSheetrs['RgpCylinder' . $clEye] . '</td>
						<td style="width:30px;" valign="top">' . $workSheetrs['RgpAxis' . $clEye] . '</td>
						<td style="width:35px;" valign="top">' . $workSheetrs['RgpDiameter' . $clEye] . '</td>
						<td style="width:35px;" valign="top">' . $workSheetrs['RgpBC' . $clEye] . '</td>
						<td style="width:40px;" valign="top">' . $workSheetrs['RgpAdd' . $clEye] . '</td>
						<td style="width:40px;" valign="top">&nbsp;</td>
				   </tr>';
                                }//end of rgp
                                else if ($workSheetrs['clType'] == 'cust_rgp') {
                                    $sclrgpcust_data .= '
					<tr class="alignCenter">
						<td style="width:45px;" valign="top">' . $workSheetrs['RgpCustomPower' . $clEye] . '</td>
						<td style="width:45px;" valign="top">' . $workSheetrs['RgpCustomCylinder' . $clEye] . '</td>
						<td style="width:45px;" valign="top">' . $workSheetrs['RgpCustomAxis' . $clEye] . '</td>
						<td style="width:40px;" valign="top">' . $workSheetrs['RgpCustomDiameter' . $clEye] . '</td>
						<td style="width:40px;" valign="top">' . $workSheetrs['RgpCustomBC' . $clEye] . '</td>
						<td style="width:45px;" valign="top">' . $workSheetrs['RgpCustomAdd' . $clEye] . '</td>
						<td style="width:45px;" valign="top">' . $workSheetrs['RgpCustomPCW' . $clEye] . '</td>
				   </tr>';
                                }//end of custom rgp		
                            }//end of while.
                            $sclrgpcust_data .= '
			</table>';
                        }
                        /* ----------------------------- */
                    } else {
                        $sclrgpcust_data = '';
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
                    ?>	

                        <tr class="valignTop alignRight <?php echo($cssclasssName); ?>">
                    <?php
                    //$rowspan = 1; $prev_ptid = 0; $new_ptrow = false;
                    if ($new_ptrow) {
                        ?>
                                <td rowspan="<?php echo $orderHxRow['totalrows']; ?>" style="width:15px;" class="alignCenter valignTop"><input type="checkbox" class="chk_cl_pen" name="update_order_id[]" value="<?php echo $print_order_id; ?>"></td>
                                <td rowspan="<?php echo $orderHxRow['totalrows']; ?>" style="width:150px;" class="text_10 alignLeft valignTop topborder"><?php echo $patientName; ?>
                        <?php
                        if ($lensComment || $displayOtherCmnt != '<br>') {
                            if ($lensComment) {
                                echo "<br>" . $lensComment . '<br>';
                            }
                            if ($displayOtherCmnt) {
                                echo $displayOtherCmnt;
                            }
                        } else {
                            echo $spaceNbsp;
                        }
                        ?> </td>
                                <?php } ?>
                            <td style="width:30px;" class="txt_11b blue_color alignLeft valignTop topborder"><?php echo $orderHxclEye; ?></td>
                            <td style="width:120px;" class="text_10 alignLeft valignTop topborder" ><?php
                    if ($orderHxType) {
                        echo $orderHxType;
                    } else {
                        echo $spaceNbsp;
                    } echo($LabelsTrial);
                                ?></td>
                            <td style="width:40px;" class="text_10 alignLeft valignTop topborder"><?php
                                if ($colorNameList) {
                                    echo $colorNameList;
                                } else {
                                    echo $spaceNbsp;
                                }
                                ?></td>
                            <td style="width:30px;" class="text_10 alignLeft valignTop topborder" ><?php
                                if ($lensNameList) {
                                    echo $lensNameList;
                                } else {
                                    echo $spaceNbsp;
                                }
                                ?></td>
                            <td style="width:230px;" class="text_10 <?php echo $bgclass; ?> valignTop topborder" colspan="7"><?php echo $sclrgpcust_data; ?></td>
                            <td style="width:25px;" class="text_10 valignTop topborder"><?php
                    if ($orderHxQty) {
                        echo $orderHxQty;
                    } else {
                        echo $spaceNbsp;
                    }
                                ?></td>
                            <td style="width:70px;" class="text_10 valignTop topborder"><?php
                                if ($orderHxCost) {
                                    echo $dlr . $orderHxCost;
                                } else {
                                    echo $spaceNbsp;
                                }
                                ?></td>
                    <!--			<td style="width:45px;" class="text_10 valignTop topborder"><?php
                                if ($Discount) {
                                    echo $dlr . $Discount;
                                } else {
                                    echo $spaceNbsp;
                                }
                                ?></td>-->
                            <?php if ($new_ptrow) { ?>
                                <td rowspan="<?php echo $orderHxRow['totalrows']; ?>" style="width:80px;" class="text_10 nowrap valignTop topborder"><?php
                                if ($AuthAmount) {
                                    echo $dlr . $AuthAmount;
                                } else {
                                    echo $spaceNbsp;
                                }
                                ?></td>
                                <?php } ?>

                            <td style="width:75px;" class="text_10 valignTop topborder"><?php
                    if ($Balance) {
                        echo $dlr . $Balance;
                    } else {
                        echo $spaceNbsp;
                    }
                                ?></td>

                                <?php if ($new_ptrow) { ?>
                                                <!--			<td rowspan="<?php echo $orderHxRow['totalrows']; ?>" class="text_10 valignTop topborder" style="width:60px;"><?php echo $clExam; ?></td>-->
                                <td rowspan="<?php echo $orderHxRow['totalrows']; ?>" style="width:70px;" class="text_10 valignTop topborder"><?php
                        if ($TotalBalanceODOS) {
                            echo $dlr . $TotalBalanceODOS;
                        } else {
                            echo $spaceNbsp;
                        }
                            if($order=='0') {
                                $actionhref = '<strong><a href="javascript:;" style="color:#673782;" onClick="changeStatusForOrder('.$print_order_id.', true);"><img src="../../../library/images/del.png" class="noborder"></a></strong>';
                            } else {
                                $actionhref = '<strong><a href="javascript:;" style="color:#673782;" onClick="changeStatusForOrder('.$print_order_id.', true);">'.$order.'</a></strong>';
                            }
                        
                                    ?></td>
                                <td rowspan="<?php echo $orderHxRow['totalrows']; ?>" style="width:75px;" class="text_10 valignTop topborder"><?php echo $orderHxDate; ?></td>
                                <td rowspan="<?php echo $orderHxRow['totalrows']; ?>" style="width:75px;" class="text_10 valignTop topborder"><?php echo $shipto; ?></td>
                                <td rowspan="<?php echo $orderHxRow['totalrows']; ?>" style="width:80px;" class=""><?php echo $actionhref;?></td>
                    <?php } ?>
                        </tr> 
                            <?php
                            $counterForClass++;
                            $old_facility = $orderHxRow['default_facility'];
                        }
                    } else {
                        echo '<div class="text-center alert alert-info">No Record Found.</div>';
                    }
                    ?>
            </table>




            <?php
        }
        
    } else {
        echo '<div class="text-center alert alert-info">No Record Found.</div>';
    }
}

?>



<script type="text/javascript">
    $('#chk_select_all').click(function(){$('.chk_cl_pen').prop('checked',$(this).prop('checked'));});
	$('.chk_cl_pen').click(function(){if($(this).prop('checked')==false)$('#chk_select_all').prop('checked',$(this).prop('checked'));});
    
    var selPhysicians = '<?php echo $selPhysicians;?>';
    var selFacilities = '<?php echo $selFacilities;?>';
    function changeStatusForOrder(OrderId, status) {
        phyurl='';
        facurl='';
        if(selPhysicians) {
            phyurl = "&selPhysicians="+selPhysicians;
        }
        if(selFacilities) {
            facurl = "&selFacilities="+selFacilities;
        }
        if (status == true) {
            top.show_loading_image('hide');
            top.show_loading_image('show');
                
            document.frm_reports.action = "index.php?showpage=" + optical_showpage + "&update_order_id=" + OrderId+phyurl+facurl;
            document.frm_reports.submit();
        }
    }

    
    function statusSubmit() {
        var OrderId=[];
        $('.chk_cl_pen').each(function (index, element) {
            if ($(this).attr('checked') == true || $(this).prop('checked') == true) {
                OrderId.push(element.value);
            }
        });
        changeStatusForOrder(OrderId, true);
    }
</script>



