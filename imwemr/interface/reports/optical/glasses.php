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
  FILE : glasses.php
  PURPOSE : OPTICAL ORDER DETAIL
  ACCESS TYPE : INCLUDED
 */

//$objManageData = new ManageData;
$dateFormat = get_sql_date_format();
$phpDateFormat = phpDateFormat();
$Start_date = getDateFormatDB($_POST["Start_date"]);
$End_date = getDateFormatDB($_POST["End_date"]);

$op_name_arr = preg_split('/, /', strtoupper($_SESSION['authProviderName']));
$op_name = $op_name_arr[1][0];
$op_name .= $op_name_arr[0][0];

$status = '0';
if (isset($_GET['confirm_id']) && $_GET['confirm_id'] != '') {
    $operator_id = $_SESSION['authId'];

    $confirm_id = $_GET['confirm_id'];
    $status_name = $_GET['name'];
    switch ($status_name) {
        case 'Pending':
            $status = '0';
            break;
        case 'Ordered':
            $status = '1';
            break;
        case 'Received':
            $status = '2';
            break;
        case 'Dispensed':
            $status = '3';
            break;
    }
    if (isset($_GET['name']) && $_GET['name'] == '') {
        $status = '4';
    }

    $qry = "update optical_order_form set order_status = '$status', back_order_operator_id = '$operator_id' where Optical_Order_Form_id = '$confirm_id'";
    imw_query($qry);
}

$printFile = true;
if ($_POST['form_submitted']) {

    if ($dayReport == 'Daily') {
        $Start_date = $End_date = date($phpDateFormat);
    } else if ($dayReport == 'Weekly') {
        $Start_date = $arrDateRange['WEEK_DATE'];
        $End_date = date($phpDateFormat);
    } else if ($dayReport == 'Monthly') {
        $Start_date = $arrDateRange['MONTH_DATE'];
        $End_date = date($phpDateFormat);
    } else if ($dayReport == 'Quarterly') {
        $Start_date = $arrDateRange['QUARTER_DATE_START'];
        $End_date = $arrDateRange['QUARTER_DATE_END'];
    }

	//--- CHANGE DATE FORMAT -------
	$Start_date = getDateFormatDB($Start_date);
	$End_date = getDateFormatDB($End_date);
//--- Get All Pending orders ----
    $statusOptions = array('Pending', 'Ordered', 'Received', 'Dispensed');
    $data = "";
    $whereQuery = "1";
    if ($_POST["patientId"] > 0) {
        $whereQuery = "optical_order_form.patient_id='" . trim($_POST["patientId"]) . "'";
    }
    if ($Start_date != "" && $End_date != "") {
        if ($whereQuery != "1") {
            $whereQuery .= " and (date_format(optical_order_form.order_date,'%m-%d-%Y')>='" . $Start_date . "' or date_format(optical_order_form.order_date,'%m-%d-%Y')<='" . $End_date . "')";
            //$whereQuery .= " AND (optical_order_form.order_date BETWEEN '$Start_date' AND '$End_date')";
        } else {
            $whereQuery = " (date_format(optical_order_form.order_date,'%m-%d-%Y')>='" . $Start_date . "' or date_format(optical_order_form.order_date,'%m-%d-%Y')<='" . $End_date . "')";
            //$whereQuery = " (optical_order_form.order_date BETWEEN '$Start_date' AND '$End_date')";
        }
    }

    if ($_POST["orderMainStatus"] != "" && $_POST["orderMainStatus"] != "-1") {
        if ($whereQuery != "1") {
            $whereQuery .= "and optical_order_form.order_status='" . $_POST["orderMainStatus"] . "'";
        } else {
            $whereQuery = " optical_order_form.order_status='" . $_POST["orderMainStatus"] . "'";
        }
    }
    if ($whereQuery != "") {
        $whereQuery = $whereQuery;
    }
    if ($whereQuery != "1") {
        $whereQuery = $whereQuery;
    }
    $qry = "select optical_order_form.*,date_format(optical_order_form.order_date,'" . $dateFormat . "') as place_date,
		patient_data.lname,patient_data.fname,patient_data.mname
		from optical_order_form left join patient_data on patient_data.id = optical_order_form.patient_id
		where  " . $whereQuery . " order by optical_order_form.order_date desc";
    $qryRs = imw_query($qry);

    $arr_data = array();
    $cnt = 1;
    while ($orderDetails = imw_fetch_assoc($qryRs)) {
        if ($orderDetails['order_status'] == '4') {
            continue;
        }
        //--- Start Get The Patient Details -------
        $patient_id = $orderDetails['patient_id'];
        $patient_name = $orderDetails['lname'] . ', ';
        $patient_name .= $orderDetails['fname'] . ' ';
        $patient_name .= $orderDetails['mname'];
        $patient_name = ucfirst(trim($patient_name));
        if ($patient_name[0] == ',') {
            $patient_name = substr($patient_name, 1);
        }

        //--- End Get The Patient Details -------
        $id = $orderDetails['Optical_Order_Form_id'];
        $frame_name = $orderDetails['frame_name'];
        $frame_eye = $orderDetails['frame_eye'];
        $frame_color = $orderDetails['frame_color'];
        $frame_style = $orderDetails['frame_style'];
        $place_date = $orderDetails['place_date'];
        $order_status = $statusOptions[$orderDetails['order_status']];
        $order_status_action = $statusOptions[$orderDetails['order_status'] + 1];
        $lens_opt = str_replace('_', ' ', $orderDetails['lens_opt']);
        $bifocal_opt = str_replace('_', ' ', $orderDetails['bifocal_opt']);
        if ($bifocal_opt == 'Other') {
            $bifocal_opt = $orderDetails['other_lens'];
        }
        if ($order_status_action == '') {
            $actionhref = '<b><a style="color:#673782;" href="javascript:order_status(' . $id . ',\'\',true);"><img src="../../../library/images/del.png" class="noborder"></a></b>';
        } else {
            $actionhref = '<b><a style="color:#673782;" href="javascript:order_status(' . $id . ',\'' . $order_status_action . '\',true);">' . $order_status_action . '</a></b>';
        }
        $trifocal_opt = str_replace('_', ' ', $orderDetails['trifocal_opt']);
        $total_charges = numberFormat($orderDetails['total'], 2);
        $balance = numberFormat($orderDetails['balance'], 2);
        $data .= '
		<tr bgcolor="#FFFFFF">
			<td class="">' . trim($patient_name) . ' - ' . $patient_id . '</td>
			<td class="">' . trim($frame_name) . '</td>			
			<td class="">' . ucfirst($frame_color) . '</td>
			<td class="">' . ucfirst($frame_eye) . '</td>
			<td class="">' . ucwords($lens_opt) . ' ' . ucwords($bifocal_opt) . '</td>
			<td class="">' . $place_date . '</td>
			<td class="">' . $total_charges . '</td>
			<td class="">' . $order_status . '</td>
			<td class="">' . $balance . '</td>
            <td class="">' . $actionhref . '</td>
			';
        $data .= '</tr>';

        $arr_data[$cnt - 1]["patient_name_id"] = trim($patient_name) . ' - ' . $patient_id;
        $arr_data[$cnt - 1]["Frame_Name"] = trim($frame_name);
        $arr_data[$cnt - 1]["Frame_Color"] = ucfirst($frame_color);
        $arr_data[$cnt - 1]["Eye"] = ucfirst($frame_eye);
        $arr_data[$cnt - 1]["Lense_Type"] = ucwords($lens_opt) . ' ' . ucwords($bifocal_opt);
        $arr_data[$cnt - 1]["Order_Placed"] = $place_date;
        $arr_data[$cnt - 1]["T._Charges"] = $total_charges;
        $arr_data[$cnt - 1]["order_status"] = $order_status;
        $arr_data[$cnt - 1]["Balance"] = $balance;

        $last_patient_id = $patient_id;
        $cnt++;
    }
    $str_html_final = '
    <table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
        <tr class="rpt_headers">
            <td class="rptbx1" style="width:342px;">&nbsp;Glasses Report</td>
            <td class="rptbx2" style="width:350px;">&nbsp;Date: From ' . $Start_date . ' To ' . $End_date . '</td>
            <td class="rptbx3" style="width:350px;">&nbsp;Created by ' . $op_name . ' on ' . $curDate . '&nbsp;</td>
        </tr>
    </table>
    <table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
        <tr>
            <td class="text_b_w" width="170">Patient Name</td>
            <td class="text_b_w" width="100">Frame Name</td>		
            <td class="text_b_w" width="100">Frame Color</td>
            <td class="text_b_w" width="60">Eye</td>
            <td class="text_b_w" width="120">Lense Type</td>
            <td class="text_b_w" width="90">Order Placed</td>
            <td class="text_b_w" width="100">T. Charges</td>
            <td class="text_b_w" width="100">Status</td>
            <td class="text_b_w" width="100">Balance</td>
            <td class="text_b_w" width="83">Action</td>
        </tr>' . $data . '</table>';
}

if ($data != '') {
    echo $str_html_final;
} else {
    echo '<div class="text-center alert alert-info">No Record Found.</div>';
}

if ($printFile == true and $data != '') {
    $styleHTML = '<style>' . file_get_contents('../css/reports_html.css') . '</style>';
    $csv_file_data = $styleHTML . $csv_file_data;

    $stylePDF = '<style>' . file_get_contents('../css/reports_pdf.css') . '</style>';
    $strHTML = $stylePDF . $str_html_final;

    $file_location = write_html($strHTML);
}
if ($output_option == 'view' || $output_option == 'output_csv') {
    echo $csv_file_data;
}
?>