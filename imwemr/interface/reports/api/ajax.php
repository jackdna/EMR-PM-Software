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


$strSelUsers= ( isset($_POST['apiUsers']) )? join(',',$_POST['apiUsers']) : '';

$curDate = date($phpDateFormat);
if( $Start_date == "" )
{
	$Start_date = $curDate;
	$End_date = $curDate;
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

if( $reportType === 'AccessLog' )
{
	$sql = 'SELECT
				`t`.`token`,
				`t`.`create_date_time`,
				`t`.`expire_date_time`,
				`u`.`fname`,
				`u`.`lname`,
				`u`.`mname`
			FROM
				`fmh_api_token_log` `t`
			INNER JOIN
				`users` `u` ON(`t`.`user_id` = `u`.`id`)';
	
	$sql .= ' WHERE DATE(`t`.`create_date_time`) BETWEEN \''.$StartDate.'\' AND \''.$EndDate.'\'';
	
	if( empty($strSelUsers) === false )
		$sql .= ' AND `t`.`user_id` IN('.$strSelUsers.')';
	
	$sql .= ' ORDER BY `t`.`id` DESC';
	
	$resp = imw_query($sql);
	
	if($resp && imw_num_rows($resp) > 0)
	{
		/*Report Title*/
$data = <<<END
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tbody>
				<tr>
					<td class="rptbx1" style="width:33%">API Access Log Report</td>
					<td class="rptbx2" style="width:33.3%;">Report Period: $Start_date to $End_date</td>
					<td class="rptbx3" style="width:33.3%;">Created By $createdBy on $curDate</td>
				</tr>
			</tbody>
		</table>
		
		<table class="rpt_table rpt rpt_table-bordered">
			<tbody>
				<tr>
					<td class="text_b_w" style="text-align:center; width:94px;">#</td>
					<td class="text_b_w" style="text-align:center;">Token</td>
					<td class="text_b_w" style="text-align:center;">Created Date Time</td>
					<td class="text_b_w" style="text-align:center;">Expire Date Time</td>
					<td class="text_b_w" style="text-align:center;">User</td>
				</tr>
END;
		$i = 0;
		while($row = imw_fetch_assoc($resp))
		{
			$i++;
			$userName = ucfirst($row['lname']);
			$userName .= (trim($row['mname']) !== '')? ' '.ucfirst($row['mname']):'';
			$userName .= ', '.$row['fname'];
			
			$createDateTime = date($phpDateFormat.' h:i A', strtotime($row['create_date_time']));
			$expireDateTime = date($phpDateFormat.' h:i A', strtotime($row['expire_date_time']));
$data .= <<<END
			<tr class="#FFFFFF">
				<td class="valignTop text_10" style="text-align:center">{$i}</td>
				<td class="valignTop text_10" style="text-align:left">{$row['token']}</td>
				<td class="valignTop text_10" style="text-align:left">{$createDateTime}</td>
				<td class="valignTop text_10" style="text-align:left">{$expireDateTime}</td>
				<td class="valignTop text_10" style="text-align:left">{$userName}</td>
			</tr>

END;
		}
		
		$data .= '</tbody></table>';
	}
	else
		$data .= '<div class="text-center alert alert-info">Data does not exists.</div>';
}
elseif( $reportType === 'CallLog' )
{
	$APIToken = trim($_POST['APIToken']);
	
	$sql = 'SELECT
				TRIM( TRAILING \'?\' FROM TRIM(BOTH \'/\' FROM `c`.`path`) ) AS \'path\',
				`c`.`method`,
				`c`.`ip`,
				`c`.`response_code`,
				`c`.`call_date_time`,
				`u`.`fname`,
				`u`.`lname`,
				`u`.`mname`
			FROM
				`fmh_api_token_log` `t`
			RIGHT JOIN
				`fmh_api_call_log` `c` ON(`t`.`id`=`c`.`token_id`)
			LEFT JOIN
				`users` `u` ON(`t`.`user_id` = `u`.`id`)';
	
	$sql .= ' WHERE DATE(`c`.`call_date_time`) BETWEEN \''.$StartDate.'\' AND \''.$EndDate.'\'';
	
	if( empty($strSelUsers) === false )
		$sql .= ' AND `t`.`user_id` IN('.$strSelUsers.')';
	
	if( empty($APIToken) === false )
		$sql .= ' AND `t`.`token` = \''.$APIToken.'\'';
	
	$sql .= ' ORDER BY `c`.`id` DESC';
	
	$resp = imw_query($sql);
	
	if($resp && imw_num_rows($resp) > 0)
	{
		/*Report Title*/
$data = <<<END
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tbody>
				<tr>
					<td class="rptbx1" style="width:37%">API Call Log Report</td>
					<td class="rptbx2" style="width:31.3%;">Report Period: $Start_date to $End_date</td>
					<td class="rptbx3" style="width:31.3%;">Created By $createdBy on $curDate</td>
				</tr>
				<tr>
					<td class="rptbx1">Token: $APIToken</td>
					<td class="rptbx2"></td>
					<td class="rptbx3"></td>
				</tr>
			</tbody>
		</table>
		
		<table class="rpt_table rpt rpt_table-bordered">
			<tbody>
				<tr>
					<td class="text_b_w" style="text-align:center; width:94px;">#</td>
					<td class="text_b_w" style="text-align:center;">Function</td>
					<td class="text_b_w" style="text-align:center;">Method</td>
					<td class="text_b_w" style="text-align:center;">IP</td>
					<td class="text_b_w" style="text-align:center; width: 200px;">Response Code</td>
					<td class="text_b_w" style="text-align:center;">Call Date Time</td>
					<td class="text_b_w" style="text-align:center;">User</td>
				</tr>
END;
		$i=0;
		while($row = imw_fetch_assoc($resp))
		{
			$i++;
			$userName = ucfirst($row['lname']);
			$userName .= (trim($row['mname']) !== '')? ' '.ucfirst($row['mname']):'';
			$userName .= ', '.$row['fname'];
			
			$userName = trim($userName, ',');
			
			$callDateTime = date($phpDateFormat.' h:i A', strtotime($row['call_date_time']));
			
			$color = ($row['response_code']!='200')?'style="color:red;"':'';
			
			
$data .= <<<END
			<tr class="#FFFFFF" $color>
				<td class="valignTop text_10" style="text-align:center">{$i}</td>
				<td class="valignTop text_10" style="text-align:left">{$row['path']}</td>
				<td class="valignTop text_10" style="text-align:left">{$row['method']}</td>
				<td class="valignTop text_10" style="text-align:left">{$row['ip']}</td>
				<td class="valignTop text_10" style="text-align:left">{$row['response_code']}</td>
				<td class="valignTop text_10" style="text-align:left">{$callDateTime}</td>
				<td class="valignTop text_10" style="text-align:left">{$userName}</td>
			</tr>

END;
		}
		
		$data .= '</tbody></table>';
		
	}
	else
		$data .= '<div class="text-center alert alert-info">Data does not exists.</div>';
	
}
else
{
	$data = '<div class="text-center alert alert-info">Report does not exists.</div>';
}


print $data;


?>