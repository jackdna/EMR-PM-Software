<?php

require_once(dirname(__FILE__)."/../../../config/globals.php");
require_once($GLOBALS['fileroot'] .'/library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;

if( !isset($_POST['reportType']) )
{
	echo 'Please select report type.';
	exit;
}

$phpDateFormat = phpDateFormat();

$curDate = date($phpDateFormat.' h:i A');
$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$createdBy = ucfirst(trim($op_name_arr[1][0]));
$createdBy .= ucfirst(trim($op_name_arr[0][0]));

$reportType = $_POST['reportType'];
$data = '';


$strpatientId = trim($_POST['patientId']);

$curDate = date($phpDateFormat);
if( $Start_date == "" )
{
	$Start_date = $curDate;
	$End_date = $curDate;
}

/*List All the User*/
$usersList = array();
$sqlUsers = "SELECT `id`, CONCAT(`lname`, ', ', `fname`, ' - ', `id`) AS 'name' FROM `users`";
$respUsers = imw_query($sqlUsers);
if( $respUsers && imw_num_rows($respUsers) > 0 )
{
	while($userRow = imw_fetch_assoc($respUsers) )
	{
		$usersList[$userRow['id']] = $userRow['name'];
	}
}

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
//---------------------

//DATE FORMAT
$StartDate = getDateFormatDB($Start_date);
$EndDate = getDateFormatDB($End_date);

if( $reportType === 'WvCallLog' )
{
	$sql = 'SELECT
			    `api`.`id`,
			    `api`.`parameters_sent`,
			    `api`.`response_data`,
			    `api`.`date_time`,
			    `api`.`date_of_service`,
			    `api`.`user_id`,
			    `api`.`response_code`,
			    `api`.`error_message`,
			    `cm`.`finalize`,
			    `cm`.`providerId`,
			    `cm`.`as_encounterId`,
			    `cm`.`as_date_time`,
			    `cm`.`as_document_ids`,
			    `cm`.`id` AS \'cnId\',
			    CONCAT(
			        `pd`.`lname`,
			        \', \',
			        `pd`.`fname`,
			        \' - \',
			        `pd`.`id`
			    ) AS \'patient\'
			FROM
			    `chart_master_table` `cm` USE INDEX (chartmastertbl_dos)
			LEFT JOIN `as_api_call_log` `api` ON (`api`.`cn_id` = `cm`.`id` AND `api`.`date_of_service` IS NOT NULL and `api`.`action` = \'SaveDocumentImage\')
			INNER JOIN `patient_data` `pd` ON (`cm`.`patient_id` = `pd`.`id`)
			WHERE `cm`.`date_of_service` >= \''.$StartDate.'\' AND `cm`.`date_of_service` <= \''.$EndDate.'\'
';
	
	if( empty($strpatientId) === false )
		$sql .= ' AND `cm`.`patient_id` = '.$strpatientId;

	$sql .= ' ORDER BY `cm`.`date_of_service`, `api`.`cn_id` DESC';
	
	$resp = imw_query($sql);

	if($resp && imw_num_rows($resp) > 0)
	{
		/*Report Title*/
$data = <<<END
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tbody>
				<tr>
					<td class="rptbx1" style="width:37%">Workview - TW API Log Report</td>
					<td class="rptbx2" style="width:31.3%;">Report Period: $Start_date to $End_date</td>
					<td class="rptbx3" style="width:31.3%;">Created By $createdBy on $curDate</td>
				</tr>
			</tbody>
		</table>
		
		<table class="rpt_table rpt rpt_table-bordered">
			<tbody>
				<tr>
					<td class="text_b_w" style="text-align:center; width:94px;">#</td>
					<td class="text_b_w" style="text-align:left;">Patient</td>
					<td class="text_b_w" style="text-align:left;">Request Date Time</td>
					<td class="text_b_w" style="text-align:center;">DOS</td>
					<td class="text_b_w" style="text-align:left;">Finalized</td>
					<td class="text_b_w" style="text-align:left;">Reuest Sent By</td>
					<td class="text_b_w" style="text-align:left;">Chart Note Provider</td>
					<td class="text_b_w" style="text-align:center;">Response Code</td>
					<td class="text_b_w" style="text-align:center;" title="TW Encounter Id Id">Req. Date Time</td>
					<td class="text_b_w" style="text-align:center;" title="TW Encounter Id Id">TW Enc. Id</td>
					<td class="text_b_w" style="text-align:center;" title="TW Document Id">TW Doc. Id</td>
				</tr>
END;
		$i=0;
		$prevCnId = 0;
		while($row = imw_fetch_assoc($resp))
		{
			$i++;
			
			$rowStyle = 'style="'; 

			$rowStyle .= ($row['response_code']!='200')?'color:red;':'';
			$finalized =  ($row['finalize']!='1')? 'No': 'Yes';

			if( $prevCnId != $row['cnId'])
			{
				$bgColor = ($bgColor==='') ? '#deeff3' : '';
			}

			$rowStyle .= 'background-color:'.$bgColor.'"';

			$prevCnId = $row['cnId'];

$data .= <<<END
			<tr class="#FFFFFF" $rowStyle>
				<td class="valignTop text_10" style="text-align:center">{$i}</td>
				<td class="valignTop text_10" style="text-align:left">{$row['patient']}</td>
				<td class="valignTop text_10" style="text-align:left">{$row['date_time']}</td>
				<td class="valignTop text_10" style="text-align:center">{$row['date_of_service']}</td>
				<td class="valignTop text_10" style="text-align:left">{$finalized}</td>
				<td class="valignTop text_10" style="text-align:left">{$usersList[$row['user_id']]}</td>
				<td class="valignTop text_10" style="text-align:left">{$usersList[$row['providerId']]}</td>
				<td class="valignTop text_10 cursor" style="text-align:center; text-decoration:underline;" onClick="showDetails({$row['id']});">{$row['response_code']}</td>
				<td class="valignTop text_10" style="text-align:center">{$row['as_date_time']}</td>
				<td class="valignTop text_10" style="text-align:center">{$row['as_encounterId']}</td>
				<td class="valignTop text_10" style="text-align:center">{$row['as_document_ids']}</td>
			</tr>

END;

		}
		
		$data .= '</tbody></table>';
		
	}
	else
		$data .= '<div class="text-center alert alert-info">Data does not exists.</div>';
	
}
elseif( $reportType === 'logDetails' && array_key_exists('logId', $_POST) && trim($_POST['logId']) != '' && (int)$_POST['logId'] > 0 )
{
	$logId = (int)$_POST['logId'];

	$sql = "SELECT `action`, `url_endpoint`, `parameters_sent`, `response_code`, `response_data`, `error_message` FROM `as_api_call_log` WHERE `id`=".$logId;

	$resp = imw_query($sql);

	if( imw_num_rows($resp) == 1 )
	{
		$row = imw_fetch_assoc($resp);

		$resp = array();

		$resp['action'] = $row['action'];
		$resp['url'] = $row['url_endpoint'];
		$resp['respCode'] = $row['response_code'];

		$resp['errroMessage'] = trim($row['error_message']);

		$params = $row['parameters_sent'];
		$params = trim($params);
		$resp['params'] = json_decode($params);

		$response = $row['response_data'];
		$response = trim($response);
		$resp['response'] = json_decode($response);

		$data['status'] = 'success';
		$data['message'] = $resp;
	}
	else
	{
		$data['status'] = 'error';
		$data['message'] = 'Details not found';
	}

	$data = json_encode($data);
}
else
{
	$data = '<div class="text-center alert alert-info">Report does not exists.</div>';
}


print $data;


?>