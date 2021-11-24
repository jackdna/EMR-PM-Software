<?php
/*
File: capture_report_result.php
Coded in PHP7
Purpose: Day Order Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

function cal_discount($amt,$dis)
{
	$total = 0;
	if(strstr($dis, '%'))
	{
		$disc = str_replace('%','',$dis);
		$total = ($amt*$disc)/100;
	}
	else if(strstr($dis, '$') || $dis>0)
	{
		$total = str_replace('$','',$dis);
	}
	return $total;
}

if($_POST['generateRpt'])
{
	/*rx.vis_id, 
	rx.vis_statusElements, 
	rx.vis_mr_none_given,*/
	
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	
	$groupIds = trim($_POST['group_ids']);
	$facilityIds = trim($_POST['facility_ids']);
	$providerId = (int)trim($_POST['provider_id']);
	
	/*Facilities and Groups name for Summary Data*/
	$groupNames = 'All';
	$facilitiesNames = 'All';
	$providerName = (isset($_POST['provider_name']) && trim($_POST['provider_name'])!='') ? $_POST['provider_name'] : 'All';
	
	if($groupIds!='')
	{
		$sqlGroups = 'SELECT `name` FROM `groups_new` WHERE `del_status`=0 AND gro_id IN('.$groupIds.') ORDER BY `name` ASC';
		$respGroups = imw_query($sqlGroups);
		if($respGroups && imw_num_rows($respGroups)>0)
		{
			$groupNames = array();
			while($rowGroup = imw_fetch_assoc($respGroups))
			{
				array_push($groupNames, $rowGroup['name']);
			}
			$groupNames = implode(', ', $groupNames);
		}
	}
	
	if($facilityIds!='')
	{
		$sqlFacilities = 'SELECT `name` FROM `facility` WHERE id IN('.$facilityIds.') ORDER BY `name` ASC';
		$respFacilities = imw_query($sqlFacilities);
		if($respFacilities && imw_num_rows($respFacilities)>0)
		{
			$facilitiesNames = array();
			while($rowFacility = imw_fetch_assoc($respFacilities))
			{
				array_push($facilitiesNames, $rowFacility['name']);
			}
			$facilitiesNames = implode(', ', $facilitiesNames);
		}
	}
	/*End Facilities and Groups name for Summary Data*/
	
	$groupCheck = '';
	if( preg_match('/^[0-9,]+$/', $groupIds) > 0 )
		$groupCheck = ' AND fac.default_group IN('.$groupIds.') ';
	
	$facilityCheck = '';
	if( preg_match('/^[0-9,]+$/', $facilityIds) > 0 )
		$facilityCheck = ' AND cm.facilityid IN('.$facilityIds.') ';
		
	$providerCheck = '';
	if( $providerId > 0 )
		$providerCheck = ' AND cm.providerId='.$providerId;
		
	$reportView = trim($_POST['show_report']);
	
	$mainQry = "SELECT 
					rx.patient_id, 
					DATE_FORMAT(rx.date_of_service, '%m-%d-%Y') AS 'date_of_service', 
					rx.count AS 'rx_given', 
					COUNT(ordf.id) AS 'orders_count',
					SUM(ord.grand_total) AS 'g_total',
					SUM(
						IF(ord.re_make_id > 0, 1, 0)
					) AS 're_make_count', 
					SUM(
						IF(ord.re_order_id > 0, 1, 0)
					) AS 're_order_count', 
					GROUP_CONCAT(
						DISTINCT(loc.loc_name) SEPARATOR ', '
					) AS 'locations', 
					IF(
						TRIM(
							BOTH ' ' 
							FROM 
								pd.mname
						) = '', 
						CONCAT(pd.lname, ',', pd.fname), 
						CONCAT(
							pd.lname, ',', pd.fname, ' ', pd.mname
						)
					) AS 'pt_name',
					rx.fac_name,
					rx.fac_id,
					rx.phy_name,
					rx.phy_id,
					rx.phy_del_status,
					rx.vis_statusElements,
					rx.vis_mr_none_given
				FROM 
					(
						SELECT 
							cm.patient_id, 
							cm.date_of_service, 
							cv.vis_id, 
							cv.vis_statusElements, 
							cv.vis_mr_none_given,
							fac.name AS 'fac_name',
							fac.id AS 'fac_id',
							CONVERT(
								(
									LENGTH(cv.vis_mr_none_given) - LENGTH(
										REPLACE(cv.vis_mr_none_given, 'MR', '')
									)
								)/ LENGTH('MR'), 
								UNSIGNED INTEGER
							) AS 'count',
							IF(
								TRIM(
									BOTH ' ' 
									FROM 
										usr.mname
								) = '', 
								CONCAT(usr.lname, ',', usr.fname), 
								CONCAT(
									usr.lname, ',', usr.fname, ' ', usr.mname
								)
							) AS 'phy_name',
							usr.id AS 'phy_id',
							usr.delete_status AS 'phy_del_status'
						FROM 
							chart_vision cv 
							RIGHT JOIN chart_master_table cm ON cm.id = cv.form_id 
							LEFT JOIN facility fac ON cm.facilityid = fac.id
							LEFT JOIN users usr ON cm.providerId = usr.id
						WHERE 
							cv.vis_mr_none_given != '' 
							AND cm.date_of_service != '' 
							AND (
								cv.vis_statusElements like '%elem_mrNoneGiven1=1%' 
								OR cv.vis_statusElements like '%elem_mrNoneGiven2=1%' 
								OR cv.vis_statusElements like '%elem_mrNoneGiven3=1%'
							)
							AND cm.date_of_service BETWEEN '".$dateFrom."' AND '".$dateTo."'".$groupCheck.$facilityCheck.$providerCheck." 
						HAVING 
							count > 0
					) rx 
					LEFT JOIN in_optical_order_form ordf ON(
						ordf.rx_dos = rx.date_of_service 
						AND ordf.custom_rx = 0 
						AND rx.patient_id = ordf.patient_id
					) 
					LEFT JOIN in_order ord ON(ord.id = ordf.order_id) 
					LEFT JOIN in_location loc ON(ord.loc_id = loc.id) 
					LEFT JOIN patient_data pd ON(rx.patient_id = pd.id)
				GROUP BY 
					rx.vis_id 
				ORDER BY 
					rx.date_of_service DESC";
	$mainQry;
	$mainRs	= imw_query($mainQry);
	$mainNumRs = imw_num_rows($mainRs);
	
	$html = '';
	$htmlPDF = '';
	
	$totalRxGiven = 0;
	$orderGrandTotal = 0;
	$rxOrdered = 0;
	$reMakeCount = 0;
	$reOrderCount = 0;
	
	$summaryViewData = array();
	
	while( $mainRes=imw_fetch_assoc($mainRs) )
	{
		$mainRes['locations'] = str_replace(',', '<br />', $mainRes['locations']);
		$mainRes['rx_given']=0;
		$orderTotal=0;
		$arrMRGiven_all = explode(",", strtolower($mainRes['vis_mr_none_given']));
		$mr_statusElements = explode(",",$mainRes['vis_statusElements']);
		if(in_array('elem_mrNoneGiven1=1',$mr_statusElements))
		{
			if(in_array('mr 1',$arrMRGiven_all))
			{
				$mainRes['rx_given']++;
				$orderTotal+=$mainRes['g_total'];
			}
		}
		if(in_array('elem_mrNoneGiven2=1',$mr_statusElements))
		{
			if(in_array('mr 2',$arrMRGiven_all))
			{
				$mainRes['rx_given']++;
				$orderTotal+=$mainRes['g_total'];
			}
		}
		if(in_array('elem_mrNoneGiven3=1',$mr_statusElements))
		{
			if(in_array('mr 3',$arrMRGiven_all))
			{
				$mainRes['rx_given']++;
				$orderTotal+=$mainRes['g_total'];
			}
		}
		
		
		if( $reportView =='summary' )
		{
			if( !isset($summaryViewData[$mainRes['fac_id']]) )
			{
				$summaryViewData[$mainRes['fac_id']] = array();
				$summaryViewData[$mainRes['fac_id']]['name'] = $mainRes['fac_name'];
			}
			
			/*Facility Data Container for Summary*/
			$summaryFac = &$summaryViewData[$mainRes['fac_id']];
			
			if( !isset($summaryFac['phy_data'][$mainRes['phy_id']]) )
			{
				$summaryFac['phy_data'][$mainRes['phy_id']] = array();
				$summaryFac['phy_data'][$mainRes['phy_id']]['name'] = $mainRes['phy_name'];
				$summaryFac['phy_data'][$mainRes['phy_id']]['phy_del_status'] = $mainRes['phy_del_status'];
			}
			
			/*Physician Data Container for Summary*/
			$summaryPhy = &$summaryFac['phy_data'][$mainRes['phy_id']];
			
			if( !isset($summaryPhy['data']) ){
				$summaryPhy['data'] = array('rx_given'=>0, 'orders_count'=>0, 're_make_count'=>0, 're_order_count'=>0);
			}
			
			$summaryPhy['data']['rx_given'] += (int)$mainRes['rx_given'];
			$summaryPhy['data']['orders_count'] += (int)$mainRes['orders_count'];
			$summaryPhy['data']['re_make_count'] += (int)$mainRes['re_make_count'];
			$summaryPhy['data']['re_order_count'] += (int)$mainRes['re_order_count'];
			$summaryPhy['data']['orderTotal'] += $orderTotal;
			
			$totalRxGiven += (int)$mainRes['rx_given'];
			$orderGrandTotal +=$orderTotal;
			$rxOrdered += (int)$mainRes['orders_count'];
			$reMakeCount += (int)$mainRes['re_make_count'];
			$reOrderCount += (int)$mainRes['re_order_count'];
			
			continue;
		}
		
		$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignCenter">'.$mainRes['date_of_service'].'</td>
			<td class="whiteBG rptText13 alignLeft">'.$mainRes['pt_name'].' - '.$mainRes['patient_id'].'</td>
			<td class="whiteBG rptText13 alignLeft '.(($mainRes['phy_del_status']=='1')?'redColor':'').'" style="width:120px; word-wrap: break-word; word-break: break-all;">'.$mainRes['phy_name'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:110px; word-wrap: break-word; word-break: break-all;">'.$mainRes['fac_name'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:80px; word-wrap: break-word; word-break: break-all;">'.$mainRes['locations'].'</td>
			<td class="whiteBG rptText13 alignCenter">'.$mainRes['rx_given'].'</td>
			<td class="whiteBG rptText13 alignCenter">'.$mainRes['orders_count'].'</td>
			<td class="whiteBG rptText13 alignCenter">'.$mainRes['re_make_count'].'</td>
			<td class="whiteBG rptText13 alignCenter">'.$mainRes['re_order_count'].'</td>
			<td class="whiteBG rptText13 alignCenter">$'.$orderTotal.'</td>
		</tr>';

		$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignCenter" style="width:77px;">'.$mainRes['date_of_service'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:125px;">'.$mainRes['pt_name'].' - '.$mainRes['patient_id'].'</td>
			<td class="whiteBG rptText13 alignLeft '.(($mainRes['phy_del_status']=='1')?'redColor':'').'" style="width:115px;word-wrap: break-word; word-break: break-all;">'.$mainRes['phy_name'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:80px;word-wrap: break-word; word-break: break-all;">'.$mainRes['fac_name'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:110px;word-wrap: break-word; word-break: break-all;">'.$mainRes['locations'].'</td>
			<td class="whiteBG rptText13 alignCenter" style="width:40px;">'.$mainRes['rx_given'].'</td>
			<td class="whiteBG rptText13 alignCenter" style="width:40px;">'.$mainRes['orders_count'].'</td>
			<td class="whiteBG rptText13 alignCenter" style="width:40px;">'.$mainRes['re_make_count'].'</td>
			<td class="whiteBG rptText13 alignCenter" style="width:40px;">'.$mainRes['re_order_count'].'</td>
			<td class="whiteBG rptText13 alignCenter" style="width:40px;">$'.$orderTotal.'</td>
		</tr>';
		
		$totalRxGiven += (int)$mainRes['rx_given'];
		$rxOrdered += (int)$mainRes['orders_count'];
		$reMakeCount += (int)$mainRes['re_make_count'];
		$reOrderCount += (int)$mainRes['re_order_count'];
		$orderGrandTotal +=$orderTotal;
	}
	if( count($summaryViewData)>0 )
	{
		foreach($summaryViewData as $rowData)
		{
			$html .= '<tr style="height:20px;">';
				$html .= '<td class="whiteBG rptText13 alignLeft reportTitle" style="padding-left:4px;" colspan="6">'.$rowData['name'].'</td>';
			$html .= '</tr>';
			
			$htmlPDF .= '<tr style="height:20px;">';
				$htmlPDF .= '<td class="whiteBG rptText13 alignLeft reportTitle" style="padding-left:4px;" colspan="6">'.$rowData['name'].'</td>';
			$htmlPDF .= '</tr>';
			
			foreach($rowData['phy_data'] as $phyData)
			{
				$html.='<tr class="'.(($phyData['phy_del_status']=='1')?'redColor':'').'" style="height:20px;">
					<td class="whiteBG rptText13 alignLeft" style="padding-left:4px;">'.$phyData['name'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$phyData['data']['rx_given'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$phyData['data']['orders_count'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$phyData['data']['re_make_count'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$phyData['data']['re_order_count'].'</td>
					<td class="whiteBG rptText13 alignCenter">$'.$phyData['data']['orderTotal'].'</td>
				</tr>';
				
				
				$htmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft '.(($phyData['phy_del_status']=='1')?'redColor':'').'" style="padding-left:4px; width:245px;">'.$phyData['name'].'</td>
					<td class="whiteBG rptText13 alignCenter '.(($phyData['phy_del_status']=='1')?'redColor':'').'" style="width:80px;">'.$phyData['data']['rx_given'].'</td>
					<td class="whiteBG rptText13 alignCenter '.(($phyData['phy_del_status']=='1')?'redColor':'').'" style="width:100px;">'.$phyData['data']['orders_count'].'</td>
					<td class="whiteBG rptText13 alignCenter '.(($phyData['phy_del_status']=='1')?'redColor':'').'" style="width:100px;">'.$phyData['data']['re_make_count'].'</td>
					<td class="whiteBG rptText13 alignCenter '.(($phyData['phy_del_status']=='1')?'redColor':'').'" style="width:100px;">'.$phyData['data']['re_order_count'].'</td>
					<td class="whiteBG rptText13 alignCenter '.(($phyData['phy_del_status']=='1')?'redColor':'').'" style="width:100px;">$'.$phyData['data']['orderTotal'].'</td>
				</tr>';
			}
		}
	}
	
	if( count($summaryViewData)>0 )
	{
		/*Total count*/
		$html .= '<tr style="height:20px;">';
			$html .= '<td class="whiteBG rptText13b alignRight">Total: </td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">'.$totalRxGiven.'</td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">'.$rxOrdered.'</td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">'.$reMakeCount.'</td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">'.$reOrderCount.'</td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">$'.$orderGrandTotal.'</td>';
		$html .= '</tr>';
		
		$htmlPDF .= '<tr style="height:20px;">';
			$htmlPDF .= '<td class="whiteBG rptText13b alignRight">Total: </td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">'.$totalRxGiven.'</td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">'.$rxOrdered.'</td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">'.$reMakeCount.'</td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">'.$reOrderCount.'</td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">$'.$orderGrandTotal.'</td>';
		$htmlPDF .= '</tr>';
	}
	else
	{
		/*Total count*/
		$html .= '<tr style="height:20px;">';
			$html .= '<td colspan="5" class="whiteBG rptText13b alignRight">Total: </td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">'.$totalRxGiven.'</td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">'.$rxOrdered.'</td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">'.$reMakeCount.'</td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">'.$reOrderCount.'</td>';
			$html .= '<td class="whiteBG rptText13b alignCenter">$'.$orderGrandTotal.'</td>';
		$html .= '</tr>';
		
		/*Total count*/
		$htmlPDF .= '<tr style="height:20px;">';
			$htmlPDF .= '<td colspan="5" class="whiteBG rptText13b alignRight">Total: </td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">'.$totalRxGiven.'</td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">'.$rxOrdered.'</td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">'.$reMakeCount.'</td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">'.$reOrderCount.'</td>';
			$htmlPDF .= '<td class="whiteBG rptText13b alignCenter">$'.$orderGrandTotal.'</td>';
		$htmlPDF .= '</tr>';
	}
	
	if( $html != '' )
	{
		if( count($summaryViewData)>0 )
		{
			$html = '<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
						<tr><td colspan="6" class="reportTitle">Lens</td></tr>
						<tr style="height:25px;">
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:220px;">Provider</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Rx. given</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Order count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Re Make Count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Re Order Count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Order Total</td>
						</tr>'.$html.'</table>';
			
			$htmlPDF = '<table width="700px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
							<tr><td colspan="6" class="reportTitle">Lens</td></tr>
							<tr>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:245px;">Provider</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Rx. given</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Order count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Re Make Count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Re Order Count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Order Total</td>
							</tr>'.$htmlPDF.'</table>';
		}
		else
		{
			$html = '<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
						<tr><td colspan="10" class="reportTitle">Lens-</td></tr>
						<tr style="height:25px;">
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:70px;">DOS</td>
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:200px;">Patient Name - ID</td>
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:120px;">Provider</td>
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:110px;">Facility (iDoc)</td>
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:80px;" title="Facility (Optical)">Facility (Opt.)</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:84px;">Rx. given</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:84px;">Order count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:106px;">Re Make Count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:106px;">Re Order Count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:84px;">Order Total</td>
						</tr>'.$html.'</table>';
			
			$htmlPDF = '<table width="700px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
							<tr><td colspan="10" class="reportTitle">Lens</td></tr>
							<tr>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:77px;">DOS</td>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:125px;">Patient Name - ID</td>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:115px;">Provider</td>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:80px;">Facility (iDoc)</td>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:110px;" title="Facility (Optical)">Facility (Opt.)</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Rx. given</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Order count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Re Make Count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Re Order Count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Order Total</td>
							</tr>'.$htmlPDF.'</table>';
		}
	}
	
	/*Contact Lens Section*/
	$mainQryCL = "SELECT 
					rx.patient_id, 
					DATE_FORMAT(rx.date_of_service, '%m-%d-%Y') AS 'date_of_service', 
					rx.count AS 'rx_given', 
					COUNT(ordf.id) AS 'orders_count',
					SUM(ord.grand_total) AS 'orderTotal',
					SUM(
						IF(ord.re_order_id > 0, 1, 0)
					) AS 're_order_count', 
					GROUP_CONCAT(
						DISTINCT(loc.loc_name) SEPARATOR ', '
					) AS 'locations', 
					IF(
						TRIM(
							BOTH ' ' 
							FROM 
								pd.mname
						) = '', 
						CONCAT(pd.lname, ',', pd.fname), 
						CONCAT(
							pd.lname, ',', pd.fname, ' ', pd.mname
						)
					) AS 'pt_name',
					rx.fac_name,
					rx.fac_id,
					rx.phy_name,
					rx.phy_id,
					rx.phy_del_status
				FROM
					(
						SELECT 
							cm.patient_id, 
							cm.date_of_service, 
							clm.clws_id, 
							fac.name AS 'fac_name', 
							fac.id AS 'fac_id', 
							IF(
								TRIM(
									BOTH ' ' 
									FROM 
										usr.mname
								) = '', 
								CONCAT(usr.lname, ',', usr.fname), 
								CONCAT(
									usr.lname, ',', usr.fname, ' ', usr.mname
								)
							) AS 'phy_name', 
							usr.id AS 'phy_id', 
							usr.delete_status AS 'phy_del_status', 
							count(`cm`.`patient_id`) AS 'count' 
						FROM 
							contactlensmaster clm 
							LEFT JOIN chart_master_table cm ON cm.id = clm.form_id 
							LEFT JOIN facility fac ON cm.facilityid = fac.id 
							LEFT JOIN users usr ON cm.providerId = usr.id 
						WHERE 
							cm.date_of_service != '' 
							AND cm.date_of_service BETWEEN '".$dateFrom."' AND '".$dateTo."'".$groupCheck.$facilityCheck.$providerCheck." 
						GROUP BY 
							cm.patient_id, cm.date_of_service
					) rx
					LEFT JOIN in_cl_prescriptions ordf ON(
						ordf.rx_dos = rx.date_of_service 
						AND rx.patient_id = ordf.patient_id
					) 
					LEFT JOIN in_order ord ON(ord.id = ordf.order_id) 
					LEFT JOIN in_location loc ON(ord.loc_id = loc.id) 
					LEFT JOIN patient_data pd ON(rx.patient_id = pd.id)
				GROUP BY 
					rx.clws_id
				ORDER BY 
					rx.date_of_service DESC";
	
	$mainRsCL	= imw_query($mainQryCL);
	$mainNumRsCL = imw_num_rows($mainRsCL);
	
	$htmlCL = '';
	$htmlPDFCL = '';
	
	$totalRxGivenCL = 0;
	$rxOrderedCL = 0;
	$reOrderCountCL = 0;
	$orderGrandTotalCL = 0;
	
	$summaryViewDataCL = array();
	
	while( $mainResCL=imw_fetch_assoc($mainRsCL) )
	{
		$mainResCL['locations'] = str_replace(',', '<br />', $mainResCL['locations']);
		
		if( $reportView =='summary' )
		{
			if( !isset($summaryViewDataCL[$mainResCL['fac_id']]) )
			{
				$summaryViewDataCL[$mainResCL['fac_id']] = array();
				$summaryViewDataCL[$mainResCL['fac_id']]['name'] = $mainResCL['fac_name'];
			}
			
			/*Facility Data Container for Summary*/
			$summaryFacCL = &$summaryViewDataCL[$mainResCL['fac_id']];
			
			if( !isset($summaryFacCL['phy_data'][$mainResCL['phy_id']]) )
			{
				$summaryFacCL['phy_data'][$mainResCL['phy_id']] = array();
				$summaryFacCL['phy_data'][$mainResCL['phy_id']]['name'] = $mainResCL['phy_name'];
				$summaryFacCL['phy_data'][$mainResCL['phy_id']]['phy_del_status'] = $mainResCL['phy_del_status'];
			}
			
			/*Physician Data Container for Summary*/
			$summaryPhyCL = &$summaryFacCL['phy_data'][$mainResCL['phy_id']];
			
			if( !isset($summaryPhyCL['data']) ){
				$summaryPhyCL['data'] = array('rx_given'=>0, 'orders_count'=>0, 're_order_count'=>0);
			}
			
			$summaryPhyCL['data']['rx_given'] += (int)$mainResCL['rx_given'];
			$summaryPhyCL['data']['orderTotal'] += $mainResCL['orderTotal'];
			$summaryPhyCL['data']['orders_count'] += (int)$mainResCL['orders_count'];
			$summaryPhyCL['data']['re_order_count'] += (int)$mainResCL['re_order_count'];
			
			$totalRxGivenCL += (int)$mainResCL['rx_given'];
			$rxOrderedCL += (int)$mainResCL['orders_count'];
			$reOrderCountCL += (int)$mainResCL['re_order_count'];
			$orderGrandTotalCL += $mainResCL['orderTotal'];
			
			continue;
		}
		
		$htmlCL.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignCenter">'.$mainResCL['date_of_service'].'</td>
			<td class="whiteBG rptText13 alignLeft">'.$mainResCL['pt_name'].' - '.$mainResCL['patient_id'].'</td>
			<td class="whiteBG rptText13 alignLeft '.(($mainResCL['phy_del_status']=='1')?'redColor':'').'" style="width:120px; word-wrap: break-word; word-break: break-all;">'.$mainResCL['phy_name'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:110px; word-wrap: break-word; word-break: break-all;">'.$mainResCL['fac_name'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:80px; word-wrap: break-word; word-break: break-all;">'.$mainResCL['locations'].'</td>
			<td class="whiteBG rptText13 alignCenter">'.$mainResCL['rx_given'].'</td>
			<td class="whiteBG rptText13 alignCenter">'.$mainResCL['orders_count'].'</td>
			<td class="whiteBG rptText13 alignCenter">'.$mainResCL['re_order_count'].'</td>
			<td class="whiteBG rptText13 alignCenter">$'.$mainResCL['orderTotal'].'</td>
		</tr>';

		$htmlPDFCL.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignCenter" style="width:77px;">'.$mainResCL['date_of_service'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:125px;">'.$mainResCL['pt_name'].' - '.$mainResCL['patient_id'].'</td>
			<td class="whiteBG rptText13 alignLeft '.(($mainResCL['phy_del_status']=='1')?'redColor':'').'" style="width:165px;word-wrap: break-word; word-break: break-all;">'.$mainResCL['phy_name'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:80px;word-wrap: break-word; word-break: break-all;">'.$mainResCL['fac_name'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:110px;word-wrap: break-word; word-break: break-all;">'.$mainResCL['locations'].'</td>
			<td class="whiteBG rptText13 alignCenter" style="width:40px;">'.$mainResCL['rx_given'].'</td>
			<td class="whiteBG rptText13 alignCenter" style="width:40px;">'.$mainResCL['orders_count'].'</td>
			<td class="whiteBG rptText13 alignCenter" style="width:40px;">'.$mainResCL['re_order_count'].'</td>
			<td class="whiteBG rptText13 alignCenter" style="width:40px;">$'.$mainResCL['orderTotal'].'</td>
		</tr>';
		
		$totalRxGivenCL += (int)$mainResCL['rx_given'];
		$rxOrderedCL += (int)$mainResCL['orders_count'];
		$reOrderCountCL += (int)$mainResCL['re_order_count'];
		$orderGrandTotalCL += $mainResCL['orderTotal'];
	}
	
	
	
	if( count($summaryViewDataCL)>0 )
	{
		foreach($summaryViewDataCL as $rowDataCL)
		{
			$htmlCL .= '<tr style="height:20px;">';
				$htmlCL .= '<td class="whiteBG rptText13 alignLeft reportTitle" style="padding-left:4px;" colspan="5">'.$rowDataCL['name'].'</td>';
			$htmlCL .= '</tr>';
			
			$htmlPDFCL .= '<tr style="height:20px;">';
				$htmlPDFCL .= '<td class="whiteBG rptText13 alignLeft reportTitle" style="padding-left:4px;" colspan="5">'.$rowDataCL['name'].'</td>';
			$htmlPDFCL .= '</tr>';
			
			foreach($rowDataCL['phy_data'] as $phyDataCL)
			{
				$htmlCL.='<tr class="'.(($phyDataCL['phy_del_status']=='1')?'redColor':'').'" style="height:20px;">
					<td class="whiteBG rptText13 alignLeft" style="padding-left:4px;">'.$phyDataCL['name'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$phyDataCL['data']['rx_given'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$phyDataCL['data']['orders_count'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$phyDataCL['data']['re_order_count'].'</td>
					<td class="whiteBG rptText13 alignCenter">$'.$phyDataCL['data']['orderTotal'].'</td>
				</tr>';
				
				
				$htmlPDFCL.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft '.(($phyDataCL['phy_del_status']=='1')?'redColor':'').'" style="padding-left:4px; width:365px;">'.$phyDataCL['name'].'</td>
					<td class="whiteBG rptText13 alignCenter '.(($phyDataCL['phy_del_status']=='1')?'redColor':'').'" style="width:90px;">'.$phyDataCL['data']['rx_given'].'</td>
					<td class="whiteBG rptText13 alignCenter '.(($phyDataCL['phy_del_status']=='1')?'redColor':'').'" style="width:90px;">'.$phyDataCL['data']['orders_count'].'</td>
					<td class="whiteBG rptText13 alignCenter '.(($phyDataCL['phy_del_status']=='1')?'redColor':'').'" style="width:90px;">'.$phyDataCL['data']['re_order_count'].'</td>
					<td class="whiteBG rptText13 alignCenter '.(($phyDataCL['phy_del_status']=='1')?'redColor':'').'" style="width:90px;">$'.$phyDataCL['data']['orderTotal'].'</td>
				</tr>';
			}
		}
	}
	
	if( count($summaryViewDataCL)>0 )
	{
		/*Total count*/
		$htmlCL .= '<tr style="height:20px;">';
			$htmlCL .= '<td class="whiteBG rptText13b alignRight">Total: </td>';
			$htmlCL .= '<td class="whiteBG rptText13b alignCenter">'.$totalRxGivenCL.'</td>';
			$htmlCL .= '<td class="whiteBG rptText13b alignCenter">'.$rxOrderedCL.'</td>';
			$htmlCL .= '<td class="whiteBG rptText13b alignCenter">'.$reOrderCountCL.'</td>';
			$htmlCL .= '<td class="whiteBG rptText13b alignCenter">$'.$orderGrandTotalCL.'</td>';
		$htmlCL .= '</tr>';
		
		$htmlPDFCL .= '<tr style="height:20px;">';
			$htmlPDFCL .= '<td class="whiteBG rptText13b alignRight">Total: </td>';
			$htmlPDFCL .= '<td class="whiteBG rptText13b alignCenter">'.$totalRxGivenCL.'</td>';
			$htmlPDFCL .= '<td class="whiteBG rptText13b alignCenter">'.$rxOrderedCL.'</td>';
			$htmlPDFCL .= '<td class="whiteBG rptText13b alignCenter">'.$reOrderCountCL.'</td>';
			$htmlPDFCL .= '<td class="whiteBG rptText13b alignCenter">$'.$orderGrandTotalCL.'</td>';
		$htmlPDFCL .= '</tr>';
	}
	else
	{
		/*Total count*/
		$htmlCL .= '<tr style="height:20px;">';
			$htmlCL .= '<td colspan="5" class="whiteBG rptText13b alignRight">Total: </td>';
			$htmlCL .= '<td class="whiteBG rptText13b alignCenter">'.$totalRxGivenCL.'</td>';
			$htmlCL .= '<td class="whiteBG rptText13b alignCenter">'.$rxOrderedCL.'</td>';
			$htmlCL .= '<td class="whiteBG rptText13b alignCenter">'.$reOrderCountCL.'</td>';
			$htmlCL .= '<td class="whiteBG rptText13b alignCenter">$'.$orderGrandTotalCL.'</td>';
		$htmlCL .= '</tr>';
		
		/*Total count*/
		$htmlPDFCL .= '<tr style="height:20px;">';
			$htmlPDFCL .= '<td colspan="5" class="whiteBG rptText13b alignRight">Total: </td>';
			$htmlPDFCL .= '<td class="whiteBG rptText13b alignCenter">'.$totalRxGivenCL.'</td>';
			$htmlPDFCL .= '<td class="whiteBG rptText13b alignCenter">'.$rxOrderedCL.'</td>';
			$htmlPDFCL .= '<td class="whiteBG rptText13b alignCenter">'.$reOrderCountCL.'</td>';
			$htmlPDFCL .= '<td class="whiteBG rptText13b alignCenter">$'.$orderGrandTotalCL.'</td>';
		$htmlPDFCL .= '</tr>';
	}
	
	if( $htmlCL != '' )
	{
		if( count($summaryViewDataCL)>0 )
		{
			$htmlCL = '<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
						<tr><td colspan="5" class="reportTitle">Contact Lens</td></tr>
						<tr style="height:25px;">
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:340px;">Provider</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:90px;">Rx. given</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:90px;">Order count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:90px;">Re Order Count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:90px;">Order Total</td>
						</tr>'.$htmlCL.'</table>';
			
			$htmlPDFCL = '<table width="700px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
							<tr><td colspan="5" class="reportTitle">Contact Lens</td></tr>
							<tr>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:365px;">Provider</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:90px;">Rx. given</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:90px;">Order count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:90px;">Re Order Count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:90px;">Order Total</td>
							</tr>'.$htmlPDFCL.'</table>';
		}
		else
		{
			$htmlCL = '<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
						<tr><td colspan="10" class="reportTitle">Contact Lens</td></tr>
						<tr style="height:25px;">
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:70px;">DOS</td>
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:200px;">Patient Name - ID</td>
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:226px;">Provider</td>
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:110px;">Facility (iDoc)</td>
							<td class="reportHeadBG1 alignTop" style="text-align:left; width:80px;" title="Facility (Optical)">Facility (Opt.)</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:68px;">Rx. given</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:69px;">Order count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:68px;">Re Order Count</td>
							<td class="reportHeadBG1 alignTop" style="text-align:center; width:69px;">Order Total</td>
						</tr>'.$htmlCL.'</table>';
			
			$htmlPDFCL = '<table width="700px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
							<tr><td colspan="10" class="reportTitle">Contact Lens</td></tr>
							<tr>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:77px;">DOS</td>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:125px;">Patient Name - ID</td>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:165px;">Provider</td>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:80px;">Facility (iDoc)</td>
								<td class="reportHeadBG1 alignTop" style="text-align:left; width:100px;" title="Facility (Optical)">Facility (Opt.)</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Rx. given</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Order count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Re Order Count</td>
								<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Re Order Count</td>
							</tr>'.$htmlPDFCL.'</table>';
		}
	}
	/*Contact Lens Section*/
	
	$css = '
	<style type="text/css">
	.reportHeadBG{ font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; background-color:#D9EDF8;}
	.reportHeadBG1{ font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; background-color:#67B9E8; color:#FFF;}
	.reportTitle { font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; background-color:#7B7B7B; color:#FFF }
	.rptText13 { font-family: Arial, Helvetica, sans-serif; font-size:10px; }
	.rptText13b { font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; }
	.rptText12b { font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; }		
	.whiteBG{ background:#fff; } 
	.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
	.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}
	.redColor{color:red;}
	</style>';
	
	//FINAL HTML
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" >&nbsp;Capture Report</td>
		<td style="text-align:left;" class="reportHeadBG" colspan="2" >&nbsp;Report for Date : '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
		<td style="text-align:left;" class="reportHeadBG" colspan="2">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
	</tr>
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG">&nbsp;Provider: '.$providerName.'</td>
		<td style="text-align:left;" class="reportHeadBG">&nbsp;Group (iDoc): '.$groupNames.'</td>
		<td style="text-align:left;" class="reportHeadBG">&nbsp;Facility (iDoc): '.$facilitiesNames.'</td>
		<td class="reportHeadBG" colspan="2">&nbsp;</td>
	</tr>
	</table>
	'.$html.$htmlCL;

//FINAL PDF
	//if($show_report=="detail" && count($arrMainDetail)>0)
	//{
		$mm = 15;
		$finalReportHtmlPDF.='
			<page backtop="'.$mm.'mm" backbottom="5mm">
			<page_footer>
					<table>
						<tr>
							<td style="text-align: center;	width: 1050px">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
			</page_footer>
			<page_header>		
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF; width:700px;" >
				<tr style="height:20px;">
					<td style="text-align:left; width:200px;" class="reportHeadBG">&nbsp;Capture Report</td>
					<td style="text-align:left;width:260px;" class="reportHeadBG" >&nbsp;Report for Date : '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
					<td style="text-align:left;width:280px;" class="reportHeadBG" >&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
				</tr>
				<tr style="height:20px;">
					<td style="text-align:left; width:200px;" class="reportHeadBG">&nbsp;Provider: '.$providerName.'</td>
					<td style="text-align:left;width:260px;" class="reportHeadBG" >&nbsp;Group (iDoc): '.$groupNames.'</td>
					<td style="text-align:left;width:280px;" class="reportHeadBG" >&nbsp;Facility (iDoc): '.$facilitiesNames.'</td>
				</tr>
			</table>
			</page_header>';
			$finalReportHtmlPDF.=$htmlPDF.$htmlPDFCL.'
		</page>';
	//}

  $pdfText = $css.$finalReportHtmlPDF;
  file_put_contents('../../library/new_html2pdf/capture_report_result.html',$pdfText);
}
?>
<html>
<head>
<title></title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<style type="text/css">
.redColor{color:red;}
</style>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script>
$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
</script>
</head>
<body>
<?php
if($mainNumRs>0)
{
 echo $finalReportHtml;
}
else
{
	if($_REQUEST['print'])
	{
		$d="display:none;";
	}
	echo '<br><div style="text-align:center; '.$d.'"><strong>No Record Found.</strong></div>';
}

 ?>
<form name="searchFormResult" action="capture_report_result.php" method="post">
	<input type="hidden" name="date_from" id="date_from" value="" />
	<input type="hidden" name="date_to" id="date_to" value="" />
	<input type="hidden" name="facility_ids" id="facility_ids" value="" />
	<input type="hidden" name="group_ids" id="group_ids" value="" />
	<input type="hidden" name="provider_id" id="provider_id" value="" />
	<input type="hidden" name="provider_name" id="provider_name" value="" />
	<input type="hidden" name="show_report" id="show_report" value="" />
	<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/capture_report_result';
window.location.href = url;
</script>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
	var numr = '<?php echo $mainNumRs; ?>';
	var numr2 = '<?php echo $itemNumRs; ?>';		
	
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Search","top.main_iframe.reports_iframe.submitForm();");
	if(numr>0 || numr2>0){
		mainBtnArr[1] = new Array("frame","Print","top.main_iframe.reports_iframe.printreport()");
	}
	top.btn_show("admin",mainBtnArr);	
	top.main_iframe.loading('none');
});
</script>

</body>
</html>