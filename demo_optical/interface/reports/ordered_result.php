<?php
/*
File: ordered_result.php
Coded in PHP7
Purpose: Show Order Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

if($_POST['generateRpt'])
{
	
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	//MASTER TABLES
	if($_POST['groupBy']!='manufac')
	{
		$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details";
		$manu_detail_rs = imw_query($manu_detail_qry);
		while($manu_detail_res=imw_fetch_array($manu_detail_rs))
		{
			$arrManufac[$manu_detail_res['id']]=$manu_detail_res['manufacturer_name'];
		}
	}
/*	//VENDORS
	$vendorRs = imw_query("select * from in_vendor_details");
	while($vendorRes=imw_fetch_array($vendorRs)){
		$arrVendors[$vendorRes['id']]=$vendorRes['vendor_name'];
	}
	//BRANDS
	$brandRs = imw_query("select * from in_frame_sources");
    while($brandRes=imw_fetch_array($brandRs)){
		$arrBrands[$brandRes['id']]=$brandRes['frame_source'];
  	}	*/
	//TYPES
	if($_POST['groupBy']!='type'){
	   $typeRs = imw_query("select * from in_module_type");
	   while($typeRes=imw_fetch_array($typeRs)){
		   $arrTypes[$typeRes['id']]=$typeRes['module_type_name'];
	   }
	}
	//OPERATORS
   $usersRs = imw_query("select id, fname,lname from users");
   while($usersRes=imw_fetch_array($usersRs)){
	   if($usersRes['lname']!='' || $usersRes['fname']!=''){
			$arrUsers[$usersRes['id']]=$usersRes['lname'].', '.$usersRes['fname']; 
			//TWO CHARACTERS
			$opInit = substr($usersRes['lname'],0,1);
			$opInit .= substr($usersRes['fname'],0,1);
			$arrUsersTwoChar[$usersRes['id']] = strtoupper($opInit);
	   }
   }
   //Facility
   $facRs = imw_query("select * from in_location");
   while($facRes=imw_fetch_array($facRs)){
	   $arrfacility[$facRes['id']]=$facRes['loc_name'];
   }
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	
	//SET UPC CODE
	$str_UPC='';
	if(empty($_POST['upc_code'])==false){
		$searchedUPC=explode(',',$_POST['upc_code']);
		for($i=0; $i<sizeof($searchedUPC); $i++){
			$schUPC.="'".trim($searchedUPC[$i])."',";
		}
		$str_UPC=substr($schUPC, 0, strlen($schUPC)-1);
	}
	
	$mainQry="Select in_order.id, in_order.patient_id, in_order.operator_id, in_order.tax_prac_code,
	in_order.tax_payable, DATE_FORMAT(in_order.entered_date, '%m-%d-%Y') as 'enteredDate', ordDet.id as 'order_detail_id',
	ordDet.item_name, ordDet.upc_code, ordDet.module_type_id, ordDet.manufacturer_id, ordDet.qty, ordDet.qty_right,
	ordDet.total_amount, DATE_FORMAT(ordDet.entered_date, '%m-%d-%Y') as 'detEnterDate',
	patient_data.fname, patient_data.lname, ordDet.qty_reduced 
	FROM in_order 
	LEFT JOIN in_order_details ordDet ON ordDet.order_id = in_order.id 
	LEFT JOIN in_order_fac as ord_fac on ord_fac.order_det_id = ordDet.id
	LEFT JOIN facility as fac on fac.id=ord_fac.facility_id 
	LEFT JOIN in_location as loc on loc.pos=fac.fac_prac_code
	LEFT JOIN in_order_detail_status ordSts ON ordSts.order_detail_id = ordDet.id 
	LEFT JOIN patient_data ON patient_data.id = in_order.patient_id 
	WHERE ordDet.del_status='0' $whmainQry AND (ordDet.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";
	
	if(empty($_POST['manufac'])==false){
		$mainQry.=' AND ordDet.manufacturer_id IN('.$_POST['manufac'].')';
	}
	if(empty($_POST['product_type'])==false){
		$mainQry.=' AND ordDet.module_type_id IN('.$_POST['product_type'].')';
	}
	if(empty($_POST['status'])==false){
		if($_POST['status']=='pending'){
			$mainQry.=" AND (ordDet.order_status ='".$_POST['status']."' OR ordDet.order_status ='')";
		}else{
			$mainQry.=" AND ordDet.order_status ='".$_POST['status']."'";
		}
	}
	if(empty($_POST['operators'])==false){
		$mainQry.=' AND in_order.operator_id IN('.$_POST['operators'].')';
	}
	if(empty($_POST['facility'])==false){
		$mainQry.=' AND in_order.loc_id IN('.$_POST['facility'].')';
	}
	if(empty($_POST['upc_code'])==false){
		$mainQry.=" AND ordDet.upc_code IN(".$str_UPC.")";
	}
	if(empty($_POST['pat_id'])==false){
		$mainQry.=' AND in_order.patient_id IN('.$_POST['pat_id'].')';
	}
	$mainQry.=' group by ord_fac.order_det_id ORDER BY in_order.id, in_order.entered_date DESC, ordSts.order_date';
	//echo $mainQry;
	
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	$tax_data = array();
	$prev_ord_id = false;
	
	while($mainRes=imw_fetch_assoc($mainRs)){
		
		if( $prev_ord_id != $mainRes['id'] && count($tax_data) > 0 ){
			$arrMainDetail[$prev_ord_id]['TAX']['CATEGORY']	= 'TAX';
			$arrMainDetail[$prev_ord_id]['TAX']['PRAC']	= $tax_data['prac'];
			$arrMainDetail[$prev_ord_id]['TAX']['AMOUNT']= $tax_data['amt'];
			$tax_data = array();
		}
		
		$mainRes['operName']='';
		$pid=$mainRes['patient_id'];
		$ordId=$mainRes['id'];
		$ordDetId=$mainRes['order_detail_id'];
		
		$arrMainOrder[$ordId]['name'] = $mainRes['lname'].', '.$mainRes['fname'].' - '.$pid;
		$arrMainOrder[$ordId]['order_date'] = $mainRes['enteredDate'];
		$arrMainOrder[$ordId]['operator'] = $mainRes['operator_id'];

		if($mainRes['module_type_id']==3){
			$qty=$mainRes['qty']+$mainRes['qty_right'];
		}else{
			$qty=$mainRes['qty'];
		}
		
		/*if($mainRes['order_qty']>0){
			$qty=$mainRes['order_qty'];
		}*/
		
		//DETAILS ARRAY
		$arrMainDetail[$ordId][$ordDetId]['CATEGORY'] = $mainRes['module_type_id'];
		$arrMainDetail[$ordId][$ordDetId]['ITEM_NAME'] = $mainRes['item_name'];
		$arrMainDetail[$ordId][$ordDetId]['UPC'] = $mainRes['upc_code'];
		$arrMainDetail[$ordId][$ordDetId]['QTY'] = $qty;
		$arrMainDetail[$ordId][$ordDetId]['AMOUNT'] = $mainRes['total_amount'];
		$arrMainDetail[$ordId][$ordDetId]['MANUFAC'] = $mainRes['manufacturer_id'];
		$arrMainDetail[$ordId][$ordDetId]['qty_reduced'] = $mainRes['qty_reduced'];
		
		$showType=$arrTypes[$mainRes['module_type_id']];
		$showPatient=$mainRes['lname'].', '.$mainRes['fname'];
		$tempArr[$ordDetId]=$ordDetId;
		
		$prev_ord_id 		= $ordId;
		$tax_data['id']		= $ordId;
		$tax_data['prac']	= $mainRes['tax_prac_code'];
		$tax_data['amt']	= $mainRes['tax_payable'];
	}
	
	if( count($tax_data) > 0 ){
		$arrMainDetail[$prev_ord_id]['TAX']['CATEGORY']	= 'TAX';
		$arrMainDetail[$prev_ord_id]['TAX']['PRAC']	= $tax_data['prac'];
		$arrMainDetail[$prev_ord_id]['TAX']['AMOUNT']= $tax_data['amt'];
		$tax_data = array();
	}
	
	if(sizeof($tempArr)>0){
		$orderStr=implode(',', 	$tempArr);
		$qry="Select ordSts.order_qty, ordSts.order_id, ordSts.order_detail_id, ordSts.order_status, DATE_FORMAT(ordSts.order_date, '%m-%d-%Y') as 'stsDate',
		ordSts.operator_id as 'actionBy'  
		FROM in_order_detail_status ordSts 
		WHERE order_detail_id IN(".$orderStr.") 
		ORDER BY ordSts.order_detail_id DESC, ordSts.order_date DESC, ordSts.order_time DESC";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$ordId=$res['order_id'];
			$ordDetId=$res['order_detail_id'];
			
			if($res['order_status']=='pending'){
				$arrMainDetail[$ordId][$ordDetId]['PENDING_DATE'] = $res['stsDate'];
				$arrTempQty[$ordDetId]['PENDING']+=$res['order_qty'];
			}elseif($res['order_status']=='ordered'){
				$arrMainDetail[$ordId][$ordDetId]['ORDERED_DATE'] = $res['stsDate'];
				$arrMainDetail[$ordId][$ordDetId]['ORDERED_BY'] = $res['actionBy'];
				$arrTempQty[$ordDetId]['ORDERED']+=$res['order_qty'];
			}elseif($res['order_status']=='received'){
				$arrMainDetail[$ordId][$ordDetId]['RECEIVED_DATE'] = $res['stsDate'];
				$arrMainDetail[$ordId][$ordDetId]['RECEIVED_BY'] = $res['actionBy'];
				$arrTempQty[$ordDetId]['RECEIVED']+=$res['order_qty'];
			}elseif($res['order_status']=='notified'){
				$arrMainDetail[$ordId][$ordDetId]['NOTIFIED_DATE'] = $res['stsDate'];
				$arrMainDetail[$ordId][$ordDetId]['NOTIFIED_BY'] = $res['actionBy'];
				$arrTempQty[$ordDetId]['NOTIFIED']+=$res['order_qty'];
			}elseif($res['order_status']=='dispensed'){
				$arrMainDetail[$ordId][$ordDetId]['DISPENSED_DATE'] = $res['stsDate'];
				$arrMainDetail[$ordId][$ordDetId]['DISPENSED_BY'] = $res['actionBy'];
				$arrTempQty[$ordDetId]['DISPENSED']+=$res['order_qty'];
			}else{
				$arrMainDetail[$ordId][$ordDetId]['PENDING_DATE'] = $res['detEnterDate'];
			}	
			
		}unset($rs);
		
		
		//WORK in process
		foreach($arrTempQty as $ordDetId => $data){
			if($data['DISPENSED']){
				if($arrTempQty[$ordDetId]['RECEIVED']>0){ $arrTempQty[$ordDetId]['RECEIVED']-=$data['DISPENSED']; }
				if($arrTempQty[$ordDetId]['ORDERED']>0){ $arrTempQty[$ordDetId]['ORDERED']-=$data['DISPENSED']; }
				if($arrTempQty[$ordDetId]['PENDING']>0){ $arrTempQty[$ordDetId]['PENDING']-=$data['DISPENSED']; }
				if($arrTempQty[$ordDetId]['NOTIFIED']>0){ $arrTempQty[$ordDetId]['NOTIFIED']-=$data['DISPENSED']; }
			}
			if($data['NOTIFIED']){
				if($arrTempQty[$ordDetId]['RECEIVED']>0){ $arrTempQty[$ordDetId]['RECEIVED']-=$data['NOTIFIED']; }
				if($arrTempQty[$ordDetId]['ORDERED']>0){ $arrTempQty[$ordDetId]['ORDERED']-=$data['NOTIFIED']; }
				if($arrTempQty[$ordDetId]['PENDING']>0){ $arrTempQty[$ordDetId]['PENDING']-=$data['NOTIFIED']; }
			}
			if($data['RECEIVED']){
				if($arrTempQty[$ordDetId]['ORDERED']>0){ $arrTempQty[$ordDetId]['ORDERED']-=$data['RECEIVED']; }
				if($arrTempQty[$ordDetId]['PENDING']>0){ $arrTempQty[$ordDetId]['PENDING']-=$data['RECEIVED']; }
			}
			if($data['ORDERED']){
				if($arrTempQty[$ordDetId]['PENDING']>0){ $arrTempQty[$ordDetId]['PENDING']-=$data['ORDERED']; }
			}
		}

		foreach($arrTempQty as $ordDetId => $data){
			if($data['PENDING']>0)$arrQty['PENDING']+=$data['PENDING'];
			if($data['ORDERED']>0)$arrQty['ORDERED']+=$data['ORDERED'];
			if($data['RECEIVED']>0)$arrQty['RECEIVED']+=$data['RECEIVED'];
			if($data['NOTIFIED']>0)$arrQty['NOTIFIED']+=$data['NOTIFIED'];
			if($data['DISPENSED']>0)$arrQty['DISPENSED']+=$data['DISPENSED'];
		}
		unset($arrTempQty);
		
	}
	
	// MAKE HTML
	if(count($arrMainDetail)>0){
		
		
		$css = '
		<style type="text/css">
		.reportHeadBG{ font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; background-color:#D9EDF8;}
		.reportHeadBG1{ font-family: Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; background-color:#67B9E8; color:#FFF;}
		.reportTitle { font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; background-color:#7B7B7B; color:#FFF }
		.rptText13 { font-family: Arial, Helvetica, sans-serif; font-size:13px; }
		.rptText13b { font-family: Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; }
		.rptText12b { font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; }		
		.whiteBG{ background:#fff; } 
.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}		
		</style>
		';		
		

		
		$grandAmount=0;
		$tax_total = array();
		foreach($arrMainOrder as $ordId => $ordDet){
			$subTotQty= $subTotAmt =0;
			$patName=$ordDet['name'];
			$html.='<tr>
					<td class="reportTitle rptText13b" colspan="2">Patient : '.$patName.'</td>
					<td class="reportTitle rptText13b" >&nbsp;Order No.: '.$ordId.'</td>
					<td class="reportTitle rptText13b" colspan="2">&nbsp;Ordered On : '.$ordDet['order_date'].'</td>
					<td class="reportTitle rptText13b" colspan="4">&nbsp;Operator : '.$arrUsers[$ordDet['operator']].'</td>
				</tr>';
				
			$htmlPDF.='<tr>
					<td class="reportTitle rptText13b" colspan="2">Patient : '.$patName.'</td>
					<td class="reportTitle rptText13b" >&nbsp;Order No.: '.$ordId.'</td>
					<td class="reportTitle rptText13b" colspan="3">&nbsp;Ordered On : '.$ordDet['order_date'].'</td>
					<td class="reportTitle rptText13b" colspan="3">&nbsp;Operator : '.$arrUsers[$ordDet['operator']].'</td>
				</tr>';
				
			$ordDetails=array_values($arrMainDetail[$ordId]);	
			
			$detLength = sizeof($ordDetails);
			for($i=0; $i<$detLength; $i++){
				
				if( $ordDetails[$i]['CATEGORY'] == 'TAX' ){
					array_push($tax_total, $ordDetails[$i]['AMOUNT']);
					$html.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignRight" colspan="3">&nbsp;Tax : </td>
					<td class="whiteBG rptText13 alignRight">&nbsp;&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails[$i]['AMOUNT'],2).'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter" colspan="4">&nbsp</td>
					</tr>';
					
					$htmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignRight" colspan="3">&nbsp;Tax : </td>
					<td class="whiteBG rptText13 alignRight" style="width:40px;">'.$ordDetails[$i]['QTY'].'&nbsp;&nbsp;</td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.numberFormat($ordDetails[$i]['AMOUNT'],2).'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter" style="width:45px;">&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter" style="width:45px;">&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter" style="width:45px;">&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter" style="width:45px;">&nbsp;</td>
					</tr>';
					
					$subTotAmt+=$ordDetails[$i]['AMOUNT'];
					continue;
				}
				
				$subTotQty+=$ordDetails[$i]['QTY'];
				$subTotAmt+=$ordDetails[$i]['AMOUNT'];
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrTypes[$ordDetails[$i]['CATEGORY']].'-'.$ordDetails[$i]['qty_reduced'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails[$i]['ITEM_NAME'].' - '.$ordDetails[$i]['UPC'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrManufac[$ordDetails[$i]['MANUFAC']].'</td>
				<td class="whiteBG rptText13 alignRight">'.$ordDetails[$i]['QTY'].'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails[$i]['AMOUNT'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">&nbsp;'.$ordDetails[$i]['ORDERED_DATE'].' ' .$arrUsersTwoChar[$ordDetails[$i]['ORDERED_BY']].'</td>
				<td class="whiteBG rptText13 alignCenter">&nbsp;'.$ordDetails[$i]['RECEIVED_DATE'].' ' .$arrUsersTwoChar[$ordDetails[$i]['RECEIVED_BY']].'</td>
				<td class="whiteBG rptText13 alignCenter">&nbsp;'.$ordDetails[$i]['NOTIFIED_DATE'].' ' .$arrUsersTwoChar[$ordDetails[$i]['NOTIFIED_BY']].'</td>
				<td class="whiteBG rptText13 alignCenter">&nbsp;'.$ordDetails[$i]['DISPENSED_DATE'].' ' .$arrUsersTwoChar[$ordDetails[$i]['DISPENSED_BY']].'</td>
				</tr>';

$blankDate = '<span style="color:white;">00-00-0000 00</span>';

				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft" style="width:100px;">&nbsp;'.$arrTypes[$ordDetails[$i]['CATEGORY']].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:130px;">&nbsp;'.$ordDetails[$i]['ITEM_NAME'].' - '.$ordDetails[$i]['UPC'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:100px;">&nbsp;'.$arrManufac[$ordDetails[$i]['MANUFAC']].'</td>
				<td class="whiteBG rptText13 alignRight" style="width:40px;">'.$ordDetails[$i]['QTY'].'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13 alignRight" style="width:80px;">'.numberFormat($ordDetails[$i]['AMOUNT'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter" style="width:45px;">&nbsp;'.(($ordDetails[$i]['ORDERED_DATE']=='')?$blankDate:$ordDetails[$i]['ORDERED_DATE']).' ' .$arrUsersTwoChar[$ordDetails[$i]['ORDERED_BY']].'</td>
				<td class="whiteBG rptText13 alignCenter" style="width:45px;">&nbsp;'.(($ordDetails[$i]['RECEIVED_DATE']=='')?$blankDate:$ordDetails[$i]['RECEIVED_DATE']).' ' .$arrUsersTwoChar[$ordDetails[$i]['RECEIVED_BY']].'</td>
				<td class="whiteBG rptText13 alignCenter" style="width:45px;">&nbsp;'.(($ordDetails[$i]['NOTIFIED_DATE']=='')?$blankDate:$ordDetails[$i]['NOTIFIED_DATE']).' ' .$arrUsersTwoChar[$ordDetails[$i]['NOTIFIED_BY']].'</td>
				<td class="whiteBG rptText13 alignCenter" style="width:45px;">&nbsp;'.(($ordDetails[$i]['DISPENSED_DATE']=='')?$blankDate:$ordDetails[$i]['DISPENSED_DATE']).' ' .$arrUsersTwoChar[$ordDetails[$i]['DISPENSED_BY']].'</td>
				</tr>';
			}
			//SUB TOTAL
			$grandAmount+=$subTotAmt;
			$html.='<tr style="height:25px; vertical-align:top;">
			<td class="whiteBG rptText13b alignRight" colspan="3">Order Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$subTotQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.numberFormat($subTotAmt,2).'&nbsp;</td>
			<td class="whiteBG rptText13b" colspan="4"></td>
			</tr>';
			$htmlPDF.='<tr style="height:25px; vertical-align:top;">
			<td class="whiteBG rptText13b alignRight" colspan="3">Order Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$subTotQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.numberFormat($subTotAmt,2).'&nbsp;</td>
			<td class="whiteBG rptText13b"></td>
			<td class="whiteBG rptText13b"></td>
			<td class="whiteBG rptText13b"></td>
			<td class="whiteBG rptText13b"></td>
			</tr>';
		}
		//GRAND TOTAL
		$grandQty=$arrQty['PENDING'] + $arrQty['ORDERED'] + $arrQty['RECEIVED'] + $arrQty['NOTIFIED'] + $arrQty['DISPENSED'];
		$html.='
		<tr>
		<td colspan="9" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Pending (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['PENDING'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Ordered (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['ORDERED'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Received (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['RECEIVED'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Notified (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['NOTIFIED'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Dispensed (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['DISPENSED'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr>
		<td class="reportTitle rptText13b alignRight" colspan="3">Grand Total : </td>
		<td class="reportTitle rptText13b alignRight">'.$grandQty.'&nbsp;&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.numberFormat($grandAmount,2).'&nbsp;</td>
		<td class="reportTitle" colspan="5"></td>
		</tr>
		<tr>
		<td class="reportTitle rptText13b alignRight" colspan="3">Total Tax : </td>
		<td class="reportTitle rptText13b alignRight">&nbsp;&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.numberFormat(array_sum($tax_total),2).'&nbsp;</td>
		<td class="reportTitle" colspan="5"></td>
		</tr>';
		
		$htmlPDF.='
		<tr>
		<td colspan="9" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Pending (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['PENDING'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Ordered (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['ORDERED'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Received (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['RECEIVED'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Notified (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['NOTIFIED'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="3">Total Dispensed (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$arrQty['DISPENSED'].'&nbsp;&nbsp;</td>
		<td class="whiteBG" colspan="5"></td>
		</tr>
		<tr>
		<td class="reportTitle rptText13b alignRight" colspan="3">Grand Total : </td>
		<td class="reportTitle rptText13b alignRight">'.$grandQty.'&nbsp;&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.numberFormat($grandAmount,2).'&nbsp;</td>
		<td class="reportTitle" colspan="5"></td>
		</tr>
		<tr>
		<td class="reportTitle rptText13b alignRight" colspan="3">Total Tax : </td>
		<td class="reportTitle rptText13b alignRight">&nbsp;&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.numberFormat(array_sum($tax_total),2).'&nbsp;</td>
		<td class="reportTitle" colspan="5"></td>
		</tr>';		
	
		// FINAL TABLE
		if(empty($_POST['product_type'])==false){$arr_t=explode(',',$_POST['product_type']);}
		if(empty($_POST['pat_id'])==false){$arr_p=explode(',',$_POST['pat_id']);}
		if(empty($_POST['facility'])==false){ $arrfac=explode(',', $_POST['facility']); }
		$selType='All';
		$selStatus='All';
		$selUPC='All';
		$selPatient='All';
		$selFac='All';
		$selType=(count($arr_t)>1)? 'Multi' : ((count($arr_t)=='1')? $showType : $selType);
		$selStatus=(empty($_POST['status'])==false)? ucfirst($_POST['status']): $selStatus;
		$selUPC=(empty($_POST['upc_code'])==false)? ucfirst($_POST['upc_code']): $selUPC;
		$selPatient=(count($arr_p)>1)? 'Multi' : ((count($arr_p)=='1')? $showPatient : $selPatient);
		$selFac=(count($arrfac)>1)? 'Multi' : ((count($arrfac)=='1')? ucfirst($arrfacility[$_POST['facility']]): $selFac);
	
		$reportHtml.='
		<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" width="220px">&nbsp;Patient Orders Report</td>
			<td style="text-align:left;" class="reportHeadBG" width="auto" colspan="2">&nbsp;From : '.$_POST['date_from'].' To : '.$_POST['date_to'].'</td>
			<td style="text-align:left;" class="reportHeadBG" width="auto" colspan="2">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG">&nbsp;Category : '.$selType.'</td>
			<td class="reportHeadBG">&nbsp;Status : '.$selStatus.'</td>
			<td class="reportHeadBG">&nbsp;UPC Code : '.$selUPC.'</td>
			<td class="reportHeadBG">&nbsp;Patient : '.$selPatient.'</td>
			<td class="reportHeadBG">&nbsp;Facility : '.$selFac.'</td>
		</tr>';
	$reportHtml.='
	</table>';
	
		$reportHtmlPDF.='
		
		<page backtop="21mm" backbottom="5mm">
		
		<page_footer>
				<table style="width: 700px;">
					<tr>
						<td style="text-align: center;	width: 700px">Page [[page_cu]]/[[page_nb]]</td>
					</tr>	
				</table>
		</page_footer>
		
		<page_header>		
		<table width="700" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" width="249">&nbsp;Patient Orders Report</td>
			<td style="text-align:left;" class="reportHeadBG" width="249" colspan="2">&nbsp;From : '.$_POST['date_from'].' To : '.$_POST['date_to'].'</td>
			<td style="text-align:left;" class="reportHeadBG" width="249" colspan="2">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG" width="249">&nbsp;Category : '.$selType.'</td>
			<td class="reportHeadBG" width="124.5">&nbsp;Status : '.$selStatus.'</td>
			<td class="reportHeadBG" width="124.5">&nbsp;UPC Code : '.$selUPC.'</td>
			<td class="reportHeadBG" width="124.5">&nbsp;Patient : '.$selPatient.'</td>
			<td class="reportHeadBG" width="124.5">&nbsp;Facility : '.$selFac.'</td>
		</tr>
		</table>
		<table width="700" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
		<tr style="height:25px;">
			<td class="reportHeadBG1" style="text-align:left; width:100px;">Category</td>
			<td class="reportHeadBG1" style="text-align:left; width:130px;">Item Name - UPC Code</td>
			<td class="reportHeadBG1" style="text-align:left; width:100px;">Manufacturer</td>			
			<td class="reportHeadBG1" style="text-align:right; width:45px;">Qty</td>
			<td class="reportHeadBG1" style="text-align:right; width:80px;">Amount</td>
			<td class="reportHeadBG1" style="text-align:center; width:60px;">Ordered</td>
			<td class="reportHeadBG1" style="text-align:center; width:80px;">Received</td>
			<td class="reportHeadBG1" style="text-align:center; width:60px;">Notified</td>
			<td class="reportHeadBG1" style="text-align:center; width:70px;">Dispensed</td>
		</tr>
		
		';
		
	$reportHtmlPDF.='
	</table></page_header>';

	$reportHtml.='
	<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr style="height:25px;">
			<td class="reportHeadBG1" style="text-align:left; width:100px;">Category</td>
			<td class="reportHeadBG1" style="text-align:left; width:140px;">Product Name - UPC Code</td>
			<td class="reportHeadBG1" style="text-align:left; width:120px;">Manufacturer</td>			
			<td class="reportHeadBG1" style="text-align:right; width:40px;">Qty</td>
			<td class="reportHeadBG1" style="text-align:right; width:90px;">Amount</td>
			<td class="reportHeadBG1" style="text-align:center; width:100px;">Ordered</td>
			<td class="reportHeadBG1" style="text-align:center; width:100px;">Received</td>
			<td class="reportHeadBG1" style="text-align:center; width:100px;">Notified</td>
			<td class="reportHeadBG1" style="text-align:center; width:100px;">Dispensed</td>
		</tr>
		'.$html.'
	</table>';
	
	/**/
	$reportHtmlPDF.='
	<table style="width:700px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
	<tr style="height:25px;">
			<td style="width:100px;"></td>
			<td style="width:130px;"></td>
			<td style="width:100px;"></td>			
			<td style="width:40px;"></td>
			<td style="width:80px;"></td>
			<td style="width:45px;"></td>
			<td style="width:45px;"></td>
			<td style="width:45px;"></td>
			<td style="width:45px;"></td>
		</tr>'.$htmlPDF.'
	</table></page>';

	}
	


  $pdfText = $css.$reportHtmlPDF;
  
  file_put_contents('../../library/new_html2pdf/report_ordered_result.html',$pdfText);	
		
	
	
}
?>
<html>
<head>
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
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
if(count($arrMainDetail)>0)
{
 echo $reportHtml;
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
<form name="searchFormResult" action="ordered_result.php" method="post">
<input type="hidden" name="manufac" id="manufac" value="" />
<input type="hidden" name="operators" id="operators" value="" />
<input type="hidden" name="product_type" id="product_type" value="" />
<input type="hidden" name="status" id="status" value="" />
<input type="hidden" name="facility" id="facility" value="" />
<input type="hidden" name="upc_code" id="upc_code" value="" />
<input type="hidden" name="pat_id" id="pat_id" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/report_ordered_result';
window.location.href = url;
</script>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
	var numr = '<?php echo $mainNumRs; ?>';		

	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Search","top.main_iframe.reports_iframe.submitForm();");
	if(numr>0){
		mainBtnArr[1] = new Array("frame","Print","top.main_iframe.reports_iframe.printreport()");
	}
	top.btn_show("admin",mainBtnArr);
	
	top.main_iframe.loading('none');
});
</script>

</body>
</html>
