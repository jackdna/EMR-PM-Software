<?php
/*
File: day_report_result.php
Coded in PHP7
Purpose: Daily Transaction Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");


if($_POST['generateRpt'])
{
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

	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	$status='';
	if(empty($_POST['status'])===false){
		$status = str_replace(",", "','", "'".$_POST['status']."'");
	}

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

	if(stristr($status, 'stock_added') || $status==""){
/*		if(empty($_POST['operators'])==false){
			$whr=' AND entered_by IN('.$_POST['operators'].')';
		}
		if(empty($_POST['facility'])==false){
			$whr=" AND loc_tot.loc_id in (".$_POST['facility'].")";
		}
		$itemQry="select in_item.id, in_item.module_type_id, in_item.manufacturer_id, in_item.upc_code, in_item.name,
		sum(loc_tot.stock) as qty_on_hand, ROUND(in_item.retail_price, 2) as retailprice, in_item.entered_By 
		from in_item left join in_item_loc_total as loc_tot on loc_tot.item_id = in_item.id 
		where in_item.module_type_id>0 and (in_item.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."') $whr group by loc_tot.item_id";

		$itemRs=imw_query($itemQry);
		$itemNumRs=imw_num_rows($itemRs);
		while($itemRes=imw_fetch_array($itemRs)){
			$cur_stock_added[] = $itemRes;
		}		*/
		
		$mainQry="Select stockDet.item_id, stockDet.stock, stockDet.trans_type, stockDet.operator_id, DATE_FORMAT(stockDet.entered_date, '%m-%d-%Y') as 'enteredDate',
		stockDet.order_id,
		in_item.vendor_id, in_item.brand_id, in_item.name, in_item.upc_code, in_item.manufacturer_id, in_item.module_type_id, in_manufacturer_details.manufacturer_name, in_module_type.module_type_name,
		users.fname, users.lname, stockDet.loc_id
		FROM in_stock_detail stockDet
		LEFT JOIN in_item ON in_item.id = stockDet.item_id 
		LEFT JOIN in_manufacturer_details ON in_manufacturer_details.id = in_item.manufacturer_id 
		LEFT JOIN in_module_type ON in_module_type.id = in_item.module_type_id 
		LEFT JOIN users ON users.id = stockDet.operator_id
		WHERE (stockDet.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";

		if(empty($_POST['operators'])==false){
			$mainQry.=' AND stockDet.operator_id IN('.$_POST['operators'].')';
		}
		if(empty($_POST['facility'])==false){
			$mainQry.=' AND stockDet.loc_id IN('.$_POST['facility'].')';
		}
		$mainQry.=' ORDER BY stockDet.entered_date,stockDet.entered_date DESC';
		
		$mainRs=imw_query($mainQry);
		$itemNumRs=imw_num_rows($mainRs);
		while($mainRes=imw_fetch_array($mainRs)){
			$entered_date=$mainRes['entered_date'];
			$arrMainStock[$entered_date][] = $mainRes;
		}		
	}
	
	$sel_ret=imw_query("select order_id,patient_id,order_detail_id,return_qty,DATE_FORMAT(entered_date, '%m-%d-%y') as fn_entered_date from in_order_return where del_status='0' group by order_id");
	while($row_ret=imw_fetch_array($sel_ret)){
		$ret_ord_arr[$row_ret['order_id']]=$row_ret['order_id'];
		$ret_ord_det_arr[$row_ret['order_detail_id']]=$row_ret['order_detail_id'];
		$ret_ord_det_date_arr[$row_ret['order_detail_id']]=$row_ret['fn_entered_date'];
		$ret_ord_det_qty_arr[$row_ret['order_detail_id']]=$row_ret['return_qty'];
	}
	
	$mainQry="Select in_order.id, in_order.operator_id, in_order.payment_mode, in_order.checkNo, in_order.tax_prac_code,
	in_order.tax_payable, in_order.tax_pt_paid, in_order.tax_pt_resp,
	DATE_FORMAT(ordDet.entered_date, '%m-%d-%y') as entered_date,DATE_FORMAT(ordDet.del_date, '%m-%d-%y') as fn_del_date,
	ordDet.id as 'order_detail_id', ordDet.item_name, ordDet.upc_code, ordDet.module_type_id,
	ordDet.pt_paid, ordDet.ins_amount, ordDet.pt_resp, ordDet.discount, ordDet.discount_val, ordDet.manufacturer_id,
	ordDet.price, ordDet.qty, ordDet.qty_right, ordDet.total_amount, ordDet.order_status,
	ordDet.item_name_os, ordDet.upc_code_os, ordDet.manufacturer_id_os, ordDet.price_os, ordDet.discount_os, ordDet.ins_amount_os, ordDet.pt_paid_os, ordDet.pt_resp_os,
	ordSts.order_qty, ordDet.modified_by as 'actionBy', ordDet.operator_id as 'madeBy',ordDet.del_status,in_order.re_make_id,in_order.re_order_id,
	patient_data.fname,	patient_data.lname, in_order.patient_id 
	FROM in_order 
	JOIN in_order_details ordDet ON ordDet.order_id = in_order.id 
	JOIN in_order_fac as ord_fac on ord_fac.order_det_id = ordDet.id
	JOIN facility as fac on fac.id=ord_fac.facility_id 
	JOIN in_location as loc on loc.pos=fac.fac_prac_code 
	LEFT JOIN in_order_detail_status ordSts ON ordSts.order_detail_id = ordDet.id
	LEFT JOIN patient_data ON patient_data.id = in_order.patient_id 
	WHERE in_order.id>'0'";
	if(empty($dateFrom)==false && empty($dateTo)==false){
		$mainQry.=" AND (ordDet.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";
	}

	if(empty($status)==false){
		if(stristr($status, 'cancelled')){
			$cancel_whr1=" or ordDet.del_status='1'";
		}else{
			$cancel_whr=" and ordDet.del_status='0'";
		}
		if(stristr($status, 'remake')){
			$remake_whr=" or in_order.re_make_id>'0'";
		}
		if(stristr($status, 'reorder')){
			$reorder_whr=" or in_order.re_order_id>'0'";
		}
		if(stristr($status, 'returned')){
			$ret_ord_det_imp=implode("','",$ret_ord_det_arr);
			$returned_whr=" or ordDet.id in('".$ret_ord_det_imp."')";
		}
		if(stristr($status, 'pending')){
			$mainQry.=" AND (ordDet.order_status IN (".$status.") OR ordDet.order_status ='' $cancel_whr1 $remake_whr $reorder_whr $returned_whr) $cancel_whr";
		}else{
			$mainQry.=" AND (ordDet.order_status IN (".$status.") $cancel_whr1 $remake_whr $reorder_whr $returned_whr) $cancel_whr";
		}
	}
	if(empty($_POST['operators'])==false){
		$mainQry.=' AND ordDet.operator_id IN('.$_POST['operators'].')';
	}
	if(empty($_POST['facility'])==false){
		$mainQry.=' AND loc.id IN('.$_POST['facility'].')';
	}
	if(empty($_POST['iportal_orders'])==false){
		$mainQry.=" AND in_order.iportal_cl_order_id>'0'";
	}
	
	$mainQry.=' group by ord_fac.order_det_id ORDER BY in_order.id, ordDet.order_index, ordDet.module_type_id';
	//echo $mainQry;
	//die;
	$arrPaymentAdded = array();
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_array($mainRs)){
		$mainRes['operName']='';
		$ordId=$mainRes['id'];
		$ordDetId=$mainRes['order_detail_id'];
		$arrMainOrder[$ordId]['operator'] = $mainRes['operator_id'];

		if($mainRes['del_status']>0 && (stristr($status, 'cancelled') || empty($status)==true)){
			$arrAllcancelled[$ordDetId] = $mainRes;
		}
		if($mainRes['re_make_id']>0 && (stristr($status, 'remake') || empty($status)==true)){
			$arrAllRemake[$ordDetId] = $mainRes;
		}
		if($mainRes['re_order_id']>0 && (stristr($status, 'reorder') || empty($status)==true)){
			$arrAllReorder[$ordDetId] = $mainRes;
		}
		if($ret_ord_det_arr[$ordDetId]>0 && (stristr($status, 'returned') || empty($status)==true)){
			$arrAllReturned[$ordDetId] = $mainRes;
		}
		if($mainRes['order_status']=='pending' && $mainRes['del_status']==0){
			$arrAllPending[$ordDetId] = $mainRes;
		}elseif($mainRes['order_status']=='ordered' && $mainRes['del_status']==0){
			$arrAllOrdered[$ordDetId] = $mainRes;
		}elseif($mainRes['order_status']=='received' && $mainRes['del_status']==0){
			$arrAllReceived[$ordDetId] = $mainRes;
		}
		elseif($mainRes['order_status']=='notified' && $mainRes['del_status']==0){
			$arrAllNotified[$ordDetId] = $mainRes;
		}
		elseif($mainRes['order_status']=='dispensed' && $mainRes['del_status']==0){
			$arrAllDispensed[$ordDetId] = $mainRes;
		}elseif($mainRes['del_status']==0){
			$arrAllPending[$ordDetId]= $mainRes;
		}
		
		//PAYMENT BREAKDOWN
		if($mainRes['module_type_id'] != 2){
			//$totPaid=$mainRes['pt_paid']+$mainRes['ins_amount'];
			$totPaid=$mainRes['pt_paid'];
			$method=ucfirst($mainRes['payment_mode']);
			$arrPaymentBreakdown[$method]+=$totPaid;
			array_push($arrPaymentAdded, $mainRes['order_detail_id']);
		}
		$showOpr=$mainRes['operator_id'];
	}
	$totPaid = 0;


	//HTML
	//STOCK
	$html=$htmlPDF=$reportHtml=$reportHtmlPDF='';
	$tax_added = array();
	if(count($arrMainStock)>0){
		$grandAmount=0; $totQry= $totPrice=$added=$deduct=0;
		foreach($arrMainStock as $entered_date => $itemData){
			foreach($itemData as $itemDetails){
				$subTotAmt =0;
	
				$catName=$arrTypes[$itemDetails['module_type_id']];
				$manufacName= $arrManufac[$itemDetails['manufacturer_id']];
				$oprName = $arrUsersTwoChar[$itemDetails['entered_By']];
	
				if($itemDetails['trans_type']=='minus'){
					$itemDetails['stock']='-'.$itemDetails['stock'];
					$deduct+=$itemDetails['stock'];
				}else{
					$added+=$itemDetails['stock'];
				}
				$subTotQty+=$itemDetails['stock'];
				
				if($itemDetails['fname']!='' || $itemDetails['lname']){
					$operName=$itemDetails['lname'].', '.$itemDetails['fname'];
				}
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$itemDetails['name'].' - '.$itemDetails['upc_code'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$itemDetails['enteredDate'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrfacility[$itemDetails['loc_id']].'</td>			
				<td class="whiteBG rptText13 alignCenter">'.$itemDetails['order_id'].'</td>
				<td class="whiteBG rptText13 alignRight">'.$itemDetails['stock'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$operName.'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:200px;">&nbsp;'.$itemDetails['name'].' - '.$itemDetails['upc_code'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$itemDetails['enteredDate'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:180px;">&nbsp;'.$manufacName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:180px;">&nbsp;'.$arrfacility[$itemDetails['loc_id']].'</td>
				<td class="whiteBG rptText13 alignCenter">'.$itemDetails['order_id'].'</td>
				<td class="whiteBG rptText13 alignRight">'.$itemDetails['stock'].'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:150px;">&nbsp;'.$operName.'</td>
				</tr>';
			}
		}
		
		//Final html
		$totalAdded=+$subTotQty;
		$reportHtml.='
		<div id="day_report_result_html_table"><table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1" >
			<tr><td class="reportTitle rptText13b" colspan="8">Stock</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:120px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:220px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Entered Date</td>		
				<td class="reportHeadBG1" style="text-align:center; width:150px;">Manufacturer</td>
				<td class="reportHeadBG1" style="text-align:center; width:150px;">Facility</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Order Id</td>		
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:auto;">Operator</td>
			</tr>'.$html.'
			<tr>
			<td colspan="8" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="6">Added : </td>
				<td class="whiteBG rptText13b alignRight">'.$added.'&nbsp;&nbsp;</td>
				<td class="whiteBG"></td>
			</tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="6">Deducted : </td>
				<td class="whiteBG rptText13b alignRight">'.$deduct.'&nbsp;&nbsp;</td>
				<td class="whiteBG"></td>
			</tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="6">Total Added : </td>
				<td class="whiteBG rptText13b alignRight">'.$totalAdded.'&nbsp;&nbsp;</td>
				<td class="whiteBG"></td>
			</tr>
		</table></div>';		

		$itemreportHtmlPDF.='
		<table style="width:1050px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="8">Stock</td></tr>
		<tr style="height:25px;">
			<td style="width:120px;"></td>			
			<td style="width:180px;"></td>
			<td style="width:100px;"></td>
			<td style="width:160px;"></td>
			<td style="width:170px;"></td>
			<td style="width:80px;"></td>
			<td style="width:80px;"></td>
			<td style="width:150px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="8" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="6">Added : </td>
				<td class="whiteBG rptText13b alignRight">'.$added.'&nbsp;&nbsp;</td>
				<td class="whiteBG"></td>
			</tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="6">Deducted : </td>
				<td class="whiteBG rptText13b alignRight">'.$deduct.'&nbsp;&nbsp;</td>
				<td class="whiteBG"></td>
			</tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="6">Total Added : </td>
				<td class="whiteBG rptText13b alignRight">'.$totalAdded.'&nbsp;&nbsp;</td>
				<td class="whiteBG"></td>
			</tr>
		</table>';
	}
	
	$arrPaymentModes=array("cash"=>"Cash", "check"=>"Check", "credit card"=>"CC", "money order"=>"MO", "eft"=>"EFT");	
	
	// PENDING
	$html=$htmlPDF='';
	if(count($arrAllPending)>0){
		$grandAmount=0; $totQry= $totPrice=0;
		
		$tax_data = array();
		$prev_ord_id = false;
		$tax_totals = array('total'=>array(), 'paid'=>array(), 'bal'=>array());
		
		foreach($arrAllPending as $ordDetails){
			$subTotAmt =0;
			
			$catName=$arrTypes[$ordDetails['module_type_id']];
			$manufacName= $arrManufac[$ordDetails['manufacturer_id']];
			$oprName = $arrUsersTwoChar[$ordDetails['actionBy']];
			
			if( $prev_ord_id && $prev_ord_id  != $ordDetails['id']){
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="7">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				//TOTALS
				$totTotalAmt+=$tax_data['amt'];
				$totPatPaid+=$tax_data['paid'];
				$totPaid+=$tax_data['paid'];
				$totBalance+=$tax_data['resp'];
				
				if( !in_array($prev_ord_id, $tax_added) ){
					$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
					array_push($tax_added, $prev_ord_id);
				}
				array_push($tax_totals['total'], $tax_data['amt']);
				array_push($tax_totals['paid'], $tax_data['paid']);
				array_push($tax_totals['bal'], $tax_data['resp']);
				//-------
				$tax_data = array();
			}
			
			$prev_ord_id = $ordDetails['id'];
			$tax_data['id']		= $ordDetails['id'];
			$tax_data['entered_date'] = $ordDetails['entered_date'];
			$tax_data['prac']	= $ordDetails['tax_prac_code'];
			$tax_data['amt']	= $ordDetails['tax_payable'];
			$tax_data['paid']	= $ordDetails['tax_pt_paid'];
			$tax_data['resp']	= $ordDetails['tax_pt_resp'];
			$tax_data['method']	= $arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'];
			$tax_data['mode']	= ucfirst($ordDetails['payment_mode']);
			$tax_data['opr']	= $oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']];

			//BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$ordDetails['qty'];
			//if($ordDetails['module_type_id']==3){ //FOR CL
			//	$qty+=$ordDetails['qty_right'];
			//}

			if($ordDetails['module_type_id']==2){
				/*Get Lens Details*/
				$sqlLensDetail = "SELECT SUM(`wholesale_price`) AS 'price', SUM(`ins_amount`) AS 'ins_amount', SUM(`pt_paid`) AS 'pt_paid', SUM(`total_amt`) AS 'total_amount', SUM(`pt_resp`) AS 'pt_resp' FROM `in_order_lens_price_detail` WHERE `order_detail_id`='".$ordDetails['order_detail_id']."' AND `del_status`=0";
				$lensData = imw_query($sqlLensDetail);
				$lensData = imw_fetch_array($lensData);
				
				$ordDetails['discount']		= $ordDetails['discount_val'];
				$ordDetails['price']		= $lensData['price'];
				$ordDetails['ins_amount']	= $lensData['ins_amount'];
				$ordDetails['pt_paid']		= $lensData['pt_paid'];
				$ordDetails['total_amount']	= $lensData['total_amount'];
				$ordDetails['pt_resp']		= $lensData['pt_resp'];
				
				if( !in_array($ordDetails['order_detail_id'], $arrPaymentAdded) ){
					//$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'] + $ordDetails['ins_amount'];
					$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'];
					array_push($arrPaymentAdded, $ordDetails['order_detail_id']);
				}
			}
			
			$price=$ordDetails['price'];
			$totAmt=$price*$qty;
			$disc = $ordDetails['discount'];
			if($disc=="" || $disc==0.00){
				$disc = 0;
			}
			
			$discoutableAmt=$totAmt-$ordDetails['ins_amount'];
			$discount = cal_discount($discoutableAmt,$disc);
			$total_amount = $totAmt-$discount;
			//$tot_paid=$ordDetails['pt_paid']+$ordDetails['ins_amount'];
			$tot_paid=$ordDetails['pt_paid'];
			
			//TOTALS
			//do no count if it is lens
			if($ordDetails['module_type_id']!=2){
				$totQty+=$qty;
			}
			$totPrice+=$ordDetails['price'];
			$totDisc+=$discount;
			$totTotalAmt+=$total_amount;
			$totPatPaid+=$ordDetails['pt_paid'];
			$totInsPaid+=$ordDetails['ins_amount'];
			$totPaid+=$tot_paid;
			$totBalance+=$ordDetails['pt_resp'];
			//-------
			
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$html.=$qty;}
			$html.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$htmlPDF.=$qty;}
			$htmlPDF.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			if($ordDetails['module_type_id']==3){ //FOR CL - For OS
			
				$manufacName_os= $arrManufac[$ordDetails['manufacturer_id_os']];
				$qty=$ordDetails['qty_right'];

				$price=$ordDetails['price_os'];
				$totAmt=$price*$qty;
				$disc = $ordDetails['discount_os'];
				if($disc=="" || $disc==0.00){
					$disc = 0;
				}
				
				$discoutableAmt=$totAmt-$ordDetails['ins_amount_os'];
				$discount = cal_discount($discoutableAmt,$disc);
				$total_amount = $totAmt-$discount;
				$tot_paid=$ordDetails['pt_paid_os'];
				
				//TOTALS
				$totQty+=$qty;
				$totPrice+=$ordDetails['price_os'];
				$totDisc+=$discount;
				$totTotalAmt+=$total_amount;
				$totPatPaid+=$ordDetails['pt_paid_os'];
				$totInsPaid+=$ordDetails['ins_amount_os'];
				$totPaid+=$tot_paid;
				$totBalance+=$ordDetails['pt_resp_os'];
				//-------
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';			
			}
		}
		
		/*Last Tax Row*/
		if( count($tax_data) > 0 ){
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="7">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			//TOTALS
			$totTotalAmt+=$tax_data['amt'];
			$totPatPaid+=$tax_data['paid'];
			$totPaid+=$tax_data['paid'];
			$totBalance+=$tax_data['resp'];
			if( !in_array($prev_ord_id, $tax_added) ){
				$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
				array_push($tax_added, $prev_ord_id);
			}
			array_push($tax_totals['total'], $tax_data['amt']);
			array_push($tax_totals['paid'], $tax_data['paid']);
			array_push($tax_totals['bal'], $tax_data['resp']);
			//-------
			$tax_data = array();
		}
		/*End Last Tax Row*/
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr><td class="reportTitle rptText13b" colspan="15">New Orders</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Entered Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:160px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Manufacturer</td>		
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Tot. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Operators<br>M / C</td>
			</tr>'.$html.'
			<tr>
			<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';
		
		$reportHtml.='<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr></table>';

		$reportHtmlPDF.='<br />
		<table style="width:1050px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="16">New Orders</td></tr>
		<tr style="height:25px;">
			<td style="width:40px;"></td>
			<td style="width:60px;"></td>
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>			
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>
			<td style="width:40px;"></td>
			<td style="width:65px;"></td>
			<td style="width:50px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:40px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="16" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="6">Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG"></td>
		</tr>';
		
		$reportHtmlPDF.='<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="9">Total Tax : </td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr></table>';
	}

	// ORDERED
	$html=$htmlPDF='';
	$totQty=$totPrice=$totDisc=$totTotalAmt=$totPatPaid=$totInsPaid=$totPaid=$totBalance=0;	
	if(count($arrAllOrdered)>0){
		$grandAmount=0; $totQry= $totPrice=0;
		
		$tax_data = array();
		$prev_ord_id = false;
		$tax_totals = array('total'=>array(), 'paid'=>array(), 'bal'=>array());
		
		foreach($arrAllOrdered as $ordDetails){
			$subTotAmt =0;
			
			$catName=$arrTypes[$ordDetails['module_type_id']];
			$manufacName= $arrManufac[$ordDetails['manufacturer_id']];
			$oprName = $arrUsersTwoChar[$ordDetails['actionBy']];
			
			if( $prev_ord_id && $prev_ord_id  != $ordDetails['id']){
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				//TOTALS
				$totTotalAmt+=$tax_data['amt'];
				$totPatPaid+=$tax_data['paid'];
				$totPaid+=$tax_data['paid'];
				$totBalance+=$tax_data['resp'];
				if( !in_array($prev_ord_id, $tax_added) ){
					$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
					array_push($tax_added, $prev_ord_id);
				}
				array_push($tax_totals['total'], $tax_data['amt']);
				array_push($tax_totals['paid'], $tax_data['paid']);
				array_push($tax_totals['bal'], $tax_data['resp']);
				//-------
				$tax_data = array();
			}
			
			$prev_ord_id = $ordDetails['id'];
			$tax_data['id']		= $ordDetails['id'];
			$tax_data['entered_date'] = $ordDetails['entered_date'];
			$tax_data['prac']	= $ordDetails['tax_prac_code'];
			$tax_data['amt']	= $ordDetails['tax_payable'];
			$tax_data['paid']	= $ordDetails['tax_pt_paid'];
			$tax_data['resp']	= $ordDetails['tax_pt_resp'];
			$tax_data['method']	= $arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'];
			$tax_data['mode']	= ucfirst($ordDetails['payment_mode']);
			$tax_data['opr']	= $oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']];
			
			//BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$ordDetails['qty'];
			//if($ordDetails['module_type_id']==3){ //FOR CL
			//	$qty+=$ordDetails['qty_right'];
			//}
			
			if($ordDetails['module_type_id']==2){
				/*Get Lens Details*/
				$sqlLensDetail = "SELECT SUM(`wholesale_price`) AS 'price', SUM(`ins_amount`) AS 'ins_amount', SUM(`pt_paid`) AS 'pt_paid', SUM(`total_amt`) AS 'total_amount', SUM(`pt_resp`) AS 'pt_resp' FROM `in_order_lens_price_detail` WHERE `order_detail_id`='".$ordDetails['order_detail_id']."' AND `del_status`=0";
				$lensData = imw_query($sqlLensDetail);
				$lensData = imw_fetch_array($lensData);
				
				$ordDetails['discount']		= $ordDetails['discount_val'];
				$ordDetails['price']		= $lensData['price'];
				$ordDetails['ins_amount']	= $lensData['ins_amount'];
				$ordDetails['pt_paid']		= $lensData['pt_paid'];
				$ordDetails['total_amount']	= $lensData['total_amount'];
				$ordDetails['pt_resp']		= $lensData['pt_resp'];
				
				if( !in_array($ordDetails['order_detail_id'], $arrPaymentAdded) ){
					//$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'] + $ordDetails['ins_amount'];
					$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'];
					array_push($arrPaymentAdded, $ordDetails['order_detail_id']);
				}
			}
			
			$price=$ordDetails['price'];
			$totAmt=$price*$qty;
			$disc = $ordDetails['discount'];
			if($disc=="" || $disc==0.00){
				$disc = 0;
			}
			
			$discoutableAmt=$totAmt-$ordDetails['ins_amount'];
			$discount = cal_discount($discoutableAmt,$disc);
			$total_amount = $totAmt-$discount;
			//$tot_paid=$ordDetails['pt_paid']+$ordDetails['ins_amount'];
			$tot_paid=$ordDetails['pt_paid'];
			
			//TOTALS
			
			//do no count if it is lens
			if($ordDetails['module_type_id']!=2){
				$totQty+=$qty;
			}
			$totPrice+=$ordDetails['price'];
			$totDisc+=$discount;
			$totTotalAmt+=$total_amount;
			$totPatPaid+=$ordDetails['pt_paid'];
			$totInsPaid+=$ordDetails['ins_amount'];
			$totPaid+=$tot_paid;
			$totBalance+=$ordDetails['pt_resp'];
			//-------
			
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$html.=$qty;}
			$html.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:120px;">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:100px;">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$htmlPDF.=$qty;}
			$htmlPDF.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:75px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			if($ordDetails['module_type_id']==3){ //FOR CL - For OS
			
				$manufacName_os= $arrManufac[$ordDetails['manufacturer_id_os']];
				$qty=$ordDetails['qty_right'];

				$price=$ordDetails['price_os'];
				$totAmt=$price*$qty;
				$disc = $ordDetails['discount_os'];
				if($disc=="" || $disc==0.00){
					$disc = 0;
				}
				
				$discoutableAmt=$totAmt-$ordDetails['ins_amount_os'];
				$discount = cal_discount($discoutableAmt,$disc);
				$total_amount = $totAmt-$discount;
				$tot_paid=$ordDetails['pt_paid_os'];
				
				//TOTALS
				$totQty+=$qty;
				$totPrice+=$ordDetails['price_os'];
				$totDisc+=$discount;
				$totTotalAmt+=$total_amount;
				$totPatPaid+=$ordDetails['pt_paid_os'];
				$totInsPaid+=$ordDetails['ins_amount_os'];
				$totPaid+=$tot_paid;
				$totBalance+=$ordDetails['pt_resp_os'];
				//-------
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';			
			}			
		}
		
		/*Last Tax Row*/
		if( count($tax_data) > 0 ){
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			//TOTALS
			$totTotalAmt+=$tax_data['amt'];
			$totPatPaid+=$tax_data['paid'];
			$totPaid+=$tax_data['paid'];
			$totBalance+=$tax_data['resp'];
			if( !in_array($prev_ord_id, $tax_added) ){
				$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
				array_push($tax_added, $prev_ord_id);
			}
			array_push($tax_totals['total'], $tax_data['amt']);
			array_push($tax_totals['paid'], $tax_data['paid']);
			array_push($tax_totals['bal'], $tax_data['resp']);
			//-------
			$tax_data = array();
		}
		/*End Last Tax Row*/
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr><td class="reportTitle rptText13b" colspan="15">Ordered</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Entered Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:160px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Manufacturer</td>		
				<td class="reportHeadBG1" style="text-align:right; width:40px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:right; width:60px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Tot. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:70px;">Operators<br>M / C</td>
			</tr>'.$html.'
			<tr>
			<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';
		$reportHtml.='<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr></table>';

		$reportHtmlPDF.='
		<table style="border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="15">Ordered</td></tr>
		<tr style="height:25px;">
			<td style="width:40px;"></td>
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>			
			<td style="width:120px;"></td>
			<td style="width:90px;"></td>
			<td style="width:40px;"></td>
			<td style="width:65px;"></td>
			<td style="width:50px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:40px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG"></td>
		</tr>';
		$reportHtmlPDF.='<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr></table>';
	}

	// RECEIVED
	$html=$htmlPDF='';
	$totQty=$totPrice=$totDisc=$totTotalAmt=$totPatPaid=$totInsPaid=$totPaid=$totBalance=0;
	if(count($arrAllReceived)>0){
		$grandAmount=0; $totQry= $totPrice=0;
		
		$tax_data = array();
		$prev_ord_id = false;
		$tax_totals = array('total'=>array(), 'paid'=>array(), 'bal'=>array());
		
		foreach($arrAllReceived as $ordDetails){
			$subTotAmt =0;
			
			$catName=$arrTypes[$ordDetails['module_type_id']];
			$manufacName= $arrManufac[$ordDetails['manufacturer_id']];
			$oprName = $arrUsersTwoChar[$ordDetails['actionBy']];
			
			if( $prev_ord_id && $prev_ord_id  != $ordDetails['id']){
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				//TOTALS
				$totTotalAmt+=$tax_data['amt'];
				$totPatPaid+=$tax_data['paid'];
				$totPaid+=$tax_data['paid'];
				if( !in_array($prev_ord_id, $tax_added) ){
					$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
					array_push($tax_added, $prev_ord_id);
				}
				array_push($tax_totals['total'], $tax_data['amt']);
				array_push($tax_totals['paid'], $tax_data['paid']);
				array_push($tax_totals['bal'], $tax_data['resp']);
				//-------
				$tax_data = array();
			}
			
			$prev_ord_id = $ordDetails['id'];
			$tax_data['id']		= $ordDetails['id'];
			$tax_data['entered_date'] = $ordDetails['entered_date'];
			$tax_data['prac']	= $ordDetails['tax_prac_code'];
			$tax_data['amt']	= $ordDetails['tax_payable'];
			$tax_data['paid']	= $ordDetails['tax_pt_paid'];
			$tax_data['resp']	= $ordDetails['tax_pt_resp'];
			$tax_data['method']	= $arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'];
			$tax_data['mode']	= ucfirst($ordDetails['payment_mode']);
			$tax_data['opr']	= $oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']];
			
			//BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$ordDetails['qty'];
			//if($ordDetails['module_type_id']==3){ //FOR CL
			//	$qty+=$ordDetails['qty_right'];
			//}
			
			if($ordDetails['module_type_id']==2){
				/*Get Lens Details*/
				$sqlLensDetail = "SELECT SUM(`wholesale_price`) AS 'price', SUM(`ins_amount`) AS 'ins_amount', SUM(`pt_paid`) AS 'pt_paid', SUM(`total_amt`) AS 'total_amount', SUM(`pt_resp`) AS 'pt_resp' FROM `in_order_lens_price_detail` WHERE `order_detail_id`='".$ordDetails['order_detail_id']."' AND `del_status`=0";
				$lensData = imw_query($sqlLensDetail);
				$lensData = imw_fetch_array($lensData);
				
				$ordDetails['discount']		= $ordDetails['discount_val'];
				$ordDetails['price']		= $lensData['price'];
				$ordDetails['ins_amount']	= $lensData['ins_amount'];
				$ordDetails['pt_paid']		= $lensData['pt_paid'];
				$ordDetails['total_amount']	= $lensData['total_amount'];
				$ordDetails['pt_resp']		= $lensData['pt_resp'];
				
				if( !in_array($ordDetails['order_detail_id'], $arrPaymentAdded) ){
					//$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'] + $ordDetails['ins_amount'];
					$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'];
					array_push($arrPaymentAdded, $ordDetails['order_detail_id']);
				}
			}
			
			$price=$ordDetails['price'];
			$totAmt=$price*$qty;
			$disc = $ordDetails['discount'];
			if($disc=="" || $disc==0.00){
				$disc = 0;
			}
			
			$discoutableAmt=$totAmt-$ordDetails['ins_amount'];
			$discount = cal_discount($discoutableAmt,$disc);
			$total_amount = $totAmt-$discount;
			//$tot_paid=$ordDetails['pt_paid']+$ordDetails['ins_amount'];
			$tot_paid=$ordDetails['pt_paid'];
			
			//TOTALS
			//do no count if it is lens
			if($ordDetails['module_type_id']!=2){
				$totQty+=$qty;
			}
			$totPrice+=$ordDetails['price'];
			$totDisc+=$discount;
			$totTotalAmt+=$total_amount;
			$totPatPaid+=$ordDetails['pt_paid'];
			$totInsPaid+=$ordDetails['ins_amount'];
			$totPaid+=$tot_paid;
			$totBalance+=$ordDetails['pt_resp'];
			//-------
			
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$html.=$qty;}
			$html.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:120px;">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:100px;">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$htmlPDF.=$qty;}
			$htmlPDF.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:75px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			if($ordDetails['module_type_id']==3){ //FOR CL - For OS
			
				$manufacName_os= $arrManufac[$ordDetails['manufacturer_id_os']];
				$qty=$ordDetails['qty_right'];

				$price=$ordDetails['price_os'];
				$totAmt=$price*$qty;
				$disc = $ordDetails['discount_os'];
				if($disc=="" || $disc==0.00){
					$disc = 0;
				}
				
				$discoutableAmt=$totAmt-$ordDetails['ins_amount_os'];
				$discount = cal_discount($discoutableAmt,$disc);
				$total_amount = $totAmt-$discount;
				$tot_paid=$ordDetails['pt_paid_os'];
				
				//TOTALS
				$totQty+=$qty;
				$totPrice+=$ordDetails['price_os'];
				$totDisc+=$discount;
				$totTotalAmt+=$total_amount;
				$totPatPaid+=$ordDetails['pt_paid_os'];
				$totInsPaid+=$ordDetails['ins_amount_os'];
				$totPaid+=$tot_paid;
				$totBalance+=$ordDetails['pt_resp_os'];
				//-------
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';			
			}				
		}
		
		/*Last Tax Row*/
		if( count($tax_data) > 0 ){
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			//TOTALS
			$totTotalAmt+=$tax_data['amt'];
			$totPatPaid+=$tax_data['paid'];
			$totPaid+=$tax_data['paid'];
			$totBalance+=$tax_data['resp'];
			if( !in_array($prev_ord_id, $tax_added) ){
				$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
				array_push($tax_added, $prev_ord_id);
			}
			array_push($tax_totals['total'], $tax_data['amt']);
			array_push($tax_totals['paid'], $tax_data['paid']);
			array_push($tax_totals['bal'], $tax_data['resp']);
			//-------
			$tax_data = array();
		}
		/*End Last Tax Row*/
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr><td class="reportTitle rptText13b" colspan="15">Orders Received</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Entered Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:160px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Manufacturer</td>		
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Tot. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:70px;">Operators<br>M / C</td>
			</tr>'.$html.'
			<tr>
			<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';
		$reportHtml.='<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr></table>';

		$reportHtmlPDF.='
		<table style="width:1050px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="15">Orders Received</td></tr>
		<tr style="height:25px;">
			<td style="width:40px;"></td>
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>			
			<td style="width:120px;"></td>
			<td style="width:90px;"></td>
			<td style="width:40px;"></td>
			<td style="width:65px;"></td>
			<td style="width:50px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:40px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG"></td>
		</tr>';
		$reportHtmlPDF.='<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr></table>';
	}
	
	
	// Notified	
	$html=$htmlPDF='';
	$totQty=$totPrice=$totDisc=$totTotalAmt=$totPatPaid=$totInsPaid=$totPaid=$totBalance=0;
	if(count($arrAllNotified)>0){
		$grandAmount=0; $totQry= $totPrice=0;
		
		$tax_data = array();
		$prev_ord_id = false;
		$tax_totals = array('total'=>array(), 'paid'=>array(), 'bal'=>array());
		
		foreach($arrAllNotified as $ordDetails){
			$subTotAmt =0;
			
			$catName=$arrTypes[$ordDetails['module_type_id']];
			$manufacName= $arrManufac[$ordDetails['manufacturer_id']];
			$oprName = $arrUsersTwoChar[$ordDetails['actionBy']];
			
			if( $prev_ord_id && $prev_ord_id  != $ordDetails['id']){
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				//TOTALS
				$totTotalAmt+=$tax_data['amt'];
				$totPatPaid+=$tax_data['paid'];
				$totPaid+=$tax_data['paid'];
				$totBalance+=$tax_data['resp'];
				if( !in_array($prev_ord_id, $tax_added) ){
					$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
					array_push($tax_added, $prev_ord_id);
				}
				array_push($tax_totals['total'], $tax_data['amt']);
				array_push($tax_totals['paid'], $tax_data['paid']);
				array_push($tax_totals['bal'], $tax_data['resp']);
				//-------
				$tax_data = array();
			}
			
			$prev_ord_id = $ordDetails['id'];
			$tax_data['id']		= $ordDetails['id'];
			$tax_data['entered_date'] = $ordDetails['entered_date'];
			$tax_data['prac']	= $ordDetails['tax_prac_code'];
			$tax_data['amt']	= $ordDetails['tax_payable'];
			$tax_data['paid']	= $ordDetails['tax_pt_paid'];
			$tax_data['resp']	= $ordDetails['tax_pt_resp'];
			$tax_data['method']	= $arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'];
			$tax_data['mode']	= ucfirst($ordDetails['payment_mode']);
			$tax_data['opr']	= $oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']];
			
			//BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$ordDetails['qty'];
			//if($ordDetails['module_type_id']==3){ //FOR CL
			//	$qty+=$ordDetails['qty_right'];
			//}
			
			if($ordDetails['module_type_id']==2){
				/*Get Lens Details*/
				$sqlLensDetail = "SELECT SUM(`wholesale_price`) AS 'price', SUM(`ins_amount`) AS 'ins_amount', SUM(`pt_paid`) AS 'pt_paid', SUM(`total_amt`) AS 'total_amount', SUM(`pt_resp`) AS 'pt_resp' FROM `in_order_lens_price_detail` WHERE `order_detail_id`='".$ordDetails['order_detail_id']."' AND `del_status`=0";
				$lensData = imw_query($sqlLensDetail);
				$lensData = imw_fetch_array($lensData);
				
				$ordDetails['discount']		= $ordDetails['discount_val'];
				$ordDetails['price']		= $lensData['price'];
				$ordDetails['ins_amount']	= $lensData['ins_amount'];
				$ordDetails['pt_paid']		= $lensData['pt_paid'];
				$ordDetails['total_amount']	= $lensData['total_amount'];
				$ordDetails['pt_resp']		= $lensData['pt_resp'];
				
				if( !in_array($ordDetails['order_detail_id'], $arrPaymentAdded) ){
					//$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'] + $ordDetails['ins_amount'];
					$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'];
					array_push($arrPaymentAdded, $ordDetails['order_detail_id']);
				}
			}
			
			$price=$ordDetails['price'];
			$totAmt=$price*$qty;
			$disc = $ordDetails['discount'];
			if($disc=="" || $disc==0.00){
				$disc = 0;
			}
			
			$discoutableAmt=$totAmt-$ordDetails['ins_amount'];
			$discount = cal_discount($discoutableAmt,$disc);
			$total_amount = $totAmt-$discount;
			//$tot_paid=$ordDetails['pt_paid']+$ordDetails['ins_amount'];
			$tot_paid=$ordDetails['pt_paid'];
			
			//TOTALS
			//do no count if it is lens
			if($ordDetails['module_type_id']!=2){
				$totQty+=$qty;
			}
			$totPrice+=$ordDetails['price'];
			$totDisc+=$discount;
			$totTotalAmt+=$total_amount;
			$totPatPaid+=$ordDetails['pt_paid'];
			$totInsPaid+=$ordDetails['ins_amount'];
			$totPaid+=$tot_paid;
			$totBalance+=$ordDetails['pt_resp'];
			//-------
			
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$html.=$qty;}
			$html.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:120px;">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:100px;">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$htmlPDF.=$qty;}
			$htmlPDF.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:75px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			if($ordDetails['module_type_id']==3){ //FOR CL - For OS
			
				$manufacName_os= $arrManufac[$ordDetails['manufacturer_id_os']];
				$qty=$ordDetails['qty_right'];

				$price=$ordDetails['price_os'];
				$totAmt=$price*$qty;
				$disc = $ordDetails['discount_os'];
				if($disc=="" || $disc==0.00){
					$disc = 0;
				}
				
				$discoutableAmt=$totAmt-$ordDetails['ins_amount_os'];
				$discount = cal_discount($discoutableAmt,$disc);
				$total_amount = $totAmt-$discount;
				$tot_paid=$ordDetails['pt_paid_os'];
				
				//TOTALS
				$totQty+=$qty;
				$totPrice+=$ordDetails['price_os'];
				$totDisc+=$discount;
				$totTotalAmt+=$total_amount;
				$totPatPaid+=$ordDetails['pt_paid_os'];
				$totInsPaid+=$ordDetails['ins_amount_os'];
				$totPaid+=$tot_paid;
				$totBalance+=$ordDetails['pt_resp_os'];
				//-------
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';			
			}				
		}
		
		/*Last Tax Row*/
		if( count($tax_data) > 0 ){
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			//TOTALS
			$totTotalAmt+=$tax_data['amt'];
			$totPatPaid+=$tax_data['paid'];
			$totPaid+=$tax_data['paid'];
			$totBalance+=$tax_data['resp'];
			if( !in_array($prev_ord_id, $tax_added) ){
				$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
				array_push($tax_added, $prev_ord_id);
			}
			array_push($tax_totals['total'], $tax_data['amt']);
			array_push($tax_totals['paid'], $tax_data['paid']);
			array_push($tax_totals['bal'], $tax_data['resp']);
			//-------
			$tax_data = array();
		}
		/*End Last Tax Row*/
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr><td class="reportTitle rptText13b" colspan="15">Orders Notified</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Entered Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:160px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Manufacturer</td>		
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Tot. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:70px;">Operators<br>M / C</td>
			</tr>'.$html.'
			<tr>
			<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';
		$reportHtml.='<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr></table>';

		$reportHtmlPDF.='
		<table style="width:1050px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="15">Orders Notified</td></tr>
		<tr style="height:25px;">
			<td style="width:40px;"></td>
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>			
			<td style="width:120px;"></td>
			<td style="width:90px;"></td>
			<td style="width:40px;"></td>
			<td style="width:65px;"></td>
			<td style="width:50px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:40px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG"></td>
		</tr>';
		$reportHtmlPDF.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';
	}
	
	
	// DISPENSED
	$html=$htmlPDF='';
	$totQty=$totPrice=$totDisc=$totTotalAmt=$totPatPaid=$totInsPaid=$totPaid=$totBalance=0;
	if(count($arrAllDispensed)>0){
		$grandAmount=0; $totQry= $totPrice=0;
		
		$tax_data = array();
		$prev_ord_id = false;
		$tax_totals = array('total'=>array(), 'paid'=>array(), 'bal'=>array());
		
		foreach($arrAllDispensed as $ordDetails){
			$subTotAmt =0;
			
			$catName=$arrTypes[$ordDetails['module_type_id']];
			$manufacName= $arrManufac[$ordDetails['manufacturer_id']];
			$oprName = $arrUsersTwoChar[$ordDetails['actionBy']];
			
			if( $prev_ord_id && $prev_ord_id  != $ordDetails['id']){
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				//TOTALS
				$totTotalAmt+=$tax_data['amt'];
				$totPatPaid+=$tax_data['paid'];
				$totPaid+=$tax_data['paid'];
				$totBalance+=$tax_data['resp'];
				if( !in_array($prev_ord_id, $tax_added) ){
					$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
					array_push($tax_added, $prev_ord_id);
				}
				array_push($tax_totals['total'], $tax_data['amt']);
				array_push($tax_totals['paid'], $tax_data['paid']);
				array_push($tax_totals['bal'], $tax_data['resp']);
				//-------
				$tax_data = array();
			}
			
			$prev_ord_id = $ordDetails['id'];
			$tax_data['id']		= $ordDetails['id'];
			$tax_data['entered_date'] = $ordDetails['entered_date'];
			$tax_data['prac']	= $ordDetails['tax_prac_code'];
			$tax_data['amt']	= $ordDetails['tax_payable'];
			$tax_data['paid']	= $ordDetails['tax_pt_paid'];
			$tax_data['resp']	= $ordDetails['tax_pt_resp'];
			$tax_data['method']	= $arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'];
			$tax_data['mode']	= ucfirst($ordDetails['payment_mode']);
			$tax_data['opr']	= $oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']];
			
			//BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$ordDetails['qty'];
			//if($ordDetails['module_type_id']==3){ //FOR CL
			//	$qty+=$ordDetails['qty_right'];
			//}
			
			if($ordDetails['module_type_id']==2){
				/*Get Lens Details*/
				$sqlLensDetail = "SELECT SUM(`wholesale_price`) AS 'price', SUM(`ins_amount`) AS 'ins_amount', SUM(`pt_paid`) AS 'pt_paid', SUM(`total_amt`) AS 'total_amount', SUM(`pt_resp`) AS 'pt_resp' FROM `in_order_lens_price_detail` WHERE `order_detail_id`='".$ordDetails['order_detail_id']."' AND `del_status`=0";
				$lensData = imw_query($sqlLensDetail);
				$lensData = imw_fetch_array($lensData);
				
				$ordDetails['discount']		= $ordDetails['discount_val'];
				$ordDetails['price']		= $lensData['price'];
				$ordDetails['ins_amount']	= $lensData['ins_amount'];
				$ordDetails['pt_paid']		= $lensData['pt_paid'];
				$ordDetails['total_amount']	= $lensData['total_amount'];
				$ordDetails['pt_resp']		= $lensData['pt_resp'];
				
				if( !in_array($ordDetails['order_detail_id'], $arrPaymentAdded) ){
					//$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'] + $ordDetails['ins_amount'];
					$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'];
					array_push($arrPaymentAdded, $ordDetails['order_detail_id']);
				}
			}
			
			$price=$ordDetails['price'];
			$totAmt=$price*$qty;
			$disc = $ordDetails['discount'];
			if($disc=="" || $disc==0.00){
				$disc = 0;
			}
			
			$discoutableAmt=$totAmt-$ordDetails['ins_amount'];
			$discount = cal_discount($discoutableAmt,$disc);
			$total_amount = $totAmt-$discount;
			//$tot_paid=$ordDetails['pt_paid']+$ordDetails['ins_amount'];
			$tot_paid=$ordDetails['pt_paid'];
			
			//TOTALS
			//do no count if it is lens
			if($ordDetails['module_type_id']!=2){
			$totQty+=$qty;
			}
			$totPrice+=$ordDetails['price'];
			$totDisc+=$discount;
			$totTotalAmt+=$total_amount;
			$totPatPaid+=$ordDetails['pt_paid'];
			$totInsPaid+=$ordDetails['ins_amount'];
			$totPaid+=$tot_paid;
			$totBalance+=$ordDetails['pt_resp'];
			//-------
			
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$html.=$qty;}
			$html.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:120px;">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:100px;">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$htmlPDF.=$qty;}
			$htmlPDF.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:75px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			if($ordDetails['module_type_id']==3){ //FOR CL - For OS
			
				$manufacName_os= $arrManufac[$ordDetails['manufacturer_id_os']];
				$qty=$ordDetails['qty_right'];

				$price=$ordDetails['price_os'];
				$totAmt=$price*$qty;
				$disc = $ordDetails['discount_os'];
				if($disc=="" || $disc==0.00){
					$disc = 0;
				}
				
				$discoutableAmt=$totAmt-$ordDetails['ins_amount_os'];
				$discount = cal_discount($discoutableAmt,$disc);
				$total_amount = $totAmt-$discount;
				$tot_paid=$ordDetails['pt_paid_os'];
				
				//TOTALS
				$totQty+=$qty;
				$totPrice+=$ordDetails['price_os'];
				$totDisc+=$discount;
				$totTotalAmt+=$total_amount;
				$totPatPaid+=$ordDetails['pt_paid_os'];
				$totInsPaid+=$ordDetails['ins_amount_os'];
				$totPaid+=$tot_paid;
				$totBalance+=$ordDetails['pt_resp_os'];
				//-------
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';			
			}				
		}
		
		/*Last Tax Row*/
		if( count($tax_data) > 0 ){
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			//TOTALS
			$totTotalAmt+=$tax_data['amt'];
			$totPatPaid+=$tax_data['paid'];
			$totPaid+=$tax_data['paid'];
			$totBalance+=$tax_data['resp'];
			if( !in_array($prev_ord_id, $tax_added) ){
				$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
				array_push($tax_added, $prev_ord_id);
			}
			array_push($tax_totals['total'], $tax_data['amt']);
			array_push($tax_totals['paid'], $tax_data['paid']);
			array_push($tax_totals['bal'], $tax_data['resp']);
			//-------
			$tax_data = array();
		}
		/*End Last Tax Row*/
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr><td class="reportTitle rptText13b" colspan="15">Orders Dispensed</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Entered Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:160px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Manufacturer</td>		
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Tot. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:70px;">Operators<br>M / C</td>
			</tr>'.$html.'
			<tr>
			<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';
		$reportHtml.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';

		$reportHtmlPDF.='
		<table style="width:1050px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="15">Orders Dispensed</td></tr>
		<tr style="height:25px;">
			<td style="width:40px;"></td>
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>			
			<td style="width:120px;"></td>
			<td style="width:90px;"></td>
			<td style="width:40px;"></td>
			<td style="width:65px;"></td>
			<td style="width:50px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:40px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG"></td>
		</tr>';
		$reportHtmlPDF.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';
	}	
	
	// CANCELLED
	$html=$htmlPDF='';
	$totQty=$totPrice=$totDisc=$totTotalAmt=$totPatPaid=$totInsPaid=$totPaid=$totBalance=0;
	if(count($arrAllcancelled)>0){
		$grandAmount=0; $totQry= $totPrice=0;
		
		$tax_data = array();
		$prev_ord_id = false;
		$tax_totals = array('total'=>array(), 'paid'=>array(), 'bal'=>array());
		
		foreach($arrAllcancelled as $ordDetails){
			$subTotAmt =0;
			
			$catName=$arrTypes[$ordDetails['module_type_id']];
			$manufacName= $arrManufac[$ordDetails['manufacturer_id']];
			$oprName = $arrUsersTwoChar[$ordDetails['actionBy']];
			
			if( $prev_ord_id && $prev_ord_id  != $ordDetails['id']){
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				//TOTALS
				$totTotalAmt+=$tax_data['amt'];
				$totPatPaid+=$tax_data['paid'];
				$totPaid+=$tax_data['paid'];
				$totBalance+=$tax_data['resp'];
				if( !in_array($prev_ord_id, $tax_added) ){
					$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
					array_push($tax_added, $prev_ord_id);
				}
				array_push($tax_totals['total'], $tax_data['amt']);
				array_push($tax_totals['paid'], $tax_data['paid']);
				array_push($tax_totals['bal'], $tax_data['resp']);
				//-------
				$tax_data = array();
			}
			
			$prev_ord_id = $ordDetails['id'];
			$tax_data['id']		= $ordDetails['id'];
			$tax_data['entered_date'] = $ordDetails['entered_date'];
			$tax_data['prac']	= $ordDetails['tax_prac_code'];
			$tax_data['amt']	= $ordDetails['tax_payable'];
			$tax_data['paid']	= $ordDetails['tax_pt_paid'];
			$tax_data['resp']	= $ordDetails['tax_pt_resp'];
			$tax_data['method']	= $arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'];
			$tax_data['mode']	= ucfirst($ordDetails['payment_mode']);
			$tax_data['opr']	= $oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']];
			
			//BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$ordDetails['qty'];
			//if($ordDetails['module_type_id']==3){ //FOR CL
			//	$qty+=$ordDetails['qty_right'];
			//}
			
			if($ordDetails['module_type_id']==2){
				/*Get Lens Details*/
				$sqlLensDetail = "SELECT SUM(`wholesale_price`) AS 'price', SUM(`ins_amount`) AS 'ins_amount', SUM(`pt_paid`) AS 'pt_paid', SUM(`total_amt`) AS 'total_amount', SUM(`pt_resp`) AS 'pt_resp' FROM `in_order_lens_price_detail` WHERE `order_detail_id`='".$ordDetails['order_detail_id']."' AND `del_status`=0";
				$lensData = imw_query($sqlLensDetail);
				$lensData = imw_fetch_array($lensData);
				
				$ordDetails['discount']		= $ordDetails['discount_val'];
				$ordDetails['price']		= $lensData['price'];
				$ordDetails['ins_amount']	= $lensData['ins_amount'];
				$ordDetails['pt_paid']		= $lensData['pt_paid'];
				$ordDetails['total_amount']	= $lensData['total_amount'];
				$ordDetails['pt_resp']		= $lensData['pt_resp'];
				
				if( !in_array($ordDetails['order_detail_id'], $arrPaymentAdded) ){
					//$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'] + $ordDetails['ins_amount'];
					$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'];
					array_push($arrPaymentAdded, $ordDetails['order_detail_id']);
				}
			}
			
			$price=$ordDetails['price'];
			$totAmt=$price*$qty;
			$disc = $ordDetails['discount'];
			if($disc=="" || $disc==0.00){
				$disc = 0;
			}
			
			$discoutableAmt=$totAmt-$ordDetails['ins_amount'];
			$discount = cal_discount($discoutableAmt,$disc);
			$total_amount = $totAmt-$discount;
			//$tot_paid=$ordDetails['pt_paid']+$ordDetails['ins_amount'];
			$tot_paid=$ordDetails['pt_paid'];
			
			//TOTALS
			//do no count if it is lens
			if($ordDetails['module_type_id']!=2){
			$totQty+=$qty;
			}
			$totPrice+=$ordDetails['price'];
			$totDisc+=$discount;
			$totTotalAmt+=$total_amount;
			$totPatPaid+=$ordDetails['pt_paid'];
			$totInsPaid+=$ordDetails['ins_amount'];
			$totPaid+=$tot_paid;
			$totBalance+=$ordDetails['pt_resp'];
			//-------
			
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['fn_del_date'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$html.=$qty;}
			$html.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:120px;">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:100px;">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$htmlPDF.=$qty;}
			$htmlPDF.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:75px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			if($ordDetails['module_type_id']==3){ //FOR CL - For OS
			
				$manufacName_os= $arrManufac[$ordDetails['manufacturer_id_os']];
				$qty=$ordDetails['qty_right'];

				$price=$ordDetails['price_os'];
				$totAmt=$price*$qty;
				$disc = $ordDetails['discount_os'];
				if($disc=="" || $disc==0.00){
					$disc = 0;
				}
				
				$discoutableAmt=$totAmt-$ordDetails['ins_amount_os'];
				$discount = cal_discount($discoutableAmt,$disc);
				$total_amount = $totAmt-$discount;
				$tot_paid=$ordDetails['pt_paid_os'];
				
				//TOTALS
				$totQty+=$qty;
				$totPrice+=$ordDetails['price_os'];
				$totDisc+=$discount;
				$totTotalAmt+=$total_amount;
				$totPatPaid+=$ordDetails['pt_paid_os'];
				$totInsPaid+=$ordDetails['ins_amount_os'];
				$totPaid+=$tot_paid;
				$totBalance+=$ordDetails['pt_resp_os'];
				//-------
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';			
			}				
		}
		
		/*Last Tax Row*/
		if( count($tax_data) > 0 ){
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			//TOTALS
			$totTotalAmt+=$tax_data['amt'];
			$totPatPaid+=$tax_data['paid'];
			$totPaid+=$tax_data['paid'];
			$totBalance+=$tax_data['resp'];
			if( !in_array($prev_ord_id, $tax_added) ){
				$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
				array_push($tax_added, $prev_ord_id);
			}
			array_push($tax_totals['total'], $tax_data['amt']);
			array_push($tax_totals['paid'], $tax_data['paid']);
			array_push($tax_totals['bal'], $tax_data['resp']);
			//-------
			$tax_data = array();
		}
		/*End Last Tax Row*/
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr><td class="reportTitle rptText13b" colspan="15">Orders Cancelled</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Cancelled Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:160px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Manufacturer</td>		
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Tot. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:70px;">Operators<br>M / C</td>
			</tr>'.$html.'
			<tr>
			<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';
		$reportHtml.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';

		$reportHtmlPDF.='
		<table style="width:1050px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="15">Orders Cancelled</td></tr>
		<tr style="height:25px;">
			<td style="width:40px;"></td>
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>			
			<td style="width:120px;"></td>
			<td style="width:90px;"></td>
			<td style="width:40px;"></td>
			<td style="width:65px;"></td>
			<td style="width:50px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:40px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG"></td>
		</tr>';
		$reportHtmlPDF.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';
	}
	
	// Remake
	$html=$htmlPDF='';
	$totQty=$totPrice=$totDisc=$totTotalAmt=$totPatPaid=$totInsPaid=$totPaid=$totBalance=0;
	if(count($arrAllRemake)>0){
		$grandAmount=0; $totQry= $totPrice=0;
		
		$tax_data = array();
		$prev_ord_id = false;
		$tax_totals = array('total'=>array(), 'paid'=>array(), 'bal'=>array());
		
		foreach($arrAllRemake as $ordDetails){
			$subTotAmt =0;
			
			$catName=$arrTypes[$ordDetails['module_type_id']];
			$manufacName= $arrManufac[$ordDetails['manufacturer_id']];
			$oprName = $arrUsersTwoChar[$ordDetails['actionBy']];
			
			if( $prev_ord_id && $prev_ord_id  != $ordDetails['id']){
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				//TOTALS
				$totTotalAmt+=$tax_data['amt'];
				$totPatPaid+=$tax_data['paid'];
				$totPaid+=$tax_data['paid'];
				$totBalance+=$tax_data['resp'];
				if( !in_array($prev_ord_id, $tax_added) ){
					$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
					array_push($tax_added, $prev_ord_id);
				}
				array_push($tax_totals['total'], $tax_data['amt']);
				array_push($tax_totals['paid'], $tax_data['paid']);
				array_push($tax_totals['bal'], $tax_data['resp']);
				//-------
				$tax_data = array();
			}
			
			$prev_ord_id = $ordDetails['id'];
			$tax_data['id']		= $ordDetails['id'];
			$tax_data['entered_date'] = $ordDetails['entered_date'];
			$tax_data['prac']	= $ordDetails['tax_prac_code'];
			$tax_data['amt']	= $ordDetails['tax_payable'];
			$tax_data['paid']	= $ordDetails['tax_pt_paid'];
			$tax_data['resp']	= $ordDetails['tax_pt_resp'];
			$tax_data['method']	= $arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'];
			$tax_data['mode']	= ucfirst($ordDetails['payment_mode']);
			$tax_data['opr']	= $oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']];

			//BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$ordDetails['qty'];
			//if($ordDetails['module_type_id']==3){ //FOR CL
			//	$qty+=$ordDetails['qty_right'];
			//}
			
			if($ordDetails['module_type_id']==2){
				/*Get Lens Details*/
				$sqlLensDetail = "SELECT SUM(`wholesale_price`) AS 'price', SUM(`ins_amount`) AS 'ins_amount', SUM(`pt_paid`) AS 'pt_paid', SUM(`total_amt`) AS 'total_amount', SUM(`pt_resp`) AS 'pt_resp' FROM `in_order_lens_price_detail` WHERE `order_detail_id`='".$ordDetails['order_detail_id']."' AND `del_status`=0";
				$lensData = imw_query($sqlLensDetail);
				$lensData = imw_fetch_array($lensData);
				
				$ordDetails['discount']		= $ordDetails['discount_val'];
				$ordDetails['price']		= $lensData['price'];
				$ordDetails['ins_amount']	= $lensData['ins_amount'];
				$ordDetails['pt_paid']		= $lensData['pt_paid'];
				$ordDetails['total_amount']	= $lensData['total_amount'];
				$ordDetails['pt_resp']		= $lensData['pt_resp'];
				
				if( !in_array($ordDetails['order_detail_id'], $arrPaymentAdded) ){
					//$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'] + $ordDetails['ins_amount'];
					$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'];
					array_push($arrPaymentAdded, $ordDetails['order_detail_id']);
				}
			}
			
			$price=$ordDetails['price'];
			$totAmt=$price*$qty;
			$disc = $ordDetails['discount'];
			if($disc=="" || $disc==0.00){
				$disc = 0;
			}
			
			$discoutableAmt=$totAmt-$ordDetails['ins_amount'];
			$discount = cal_discount($discoutableAmt,$disc);
			$total_amount = $totAmt-$discount;
			//$tot_paid=$ordDetails['pt_paid']+$ordDetails['ins_amount'];
			$tot_paid=$ordDetails['pt_paid'];
			
			//TOTALS
			//do no count if it is lens
			if($ordDetails['module_type_id']!=2){
			$totQty+=$qty;
			}
			$totPrice+=$ordDetails['price'];
			$totDisc+=$discount;
			$totTotalAmt+=$total_amount;
			$totPatPaid+=$ordDetails['pt_paid'];
			$totInsPaid+=$ordDetails['ins_amount'];
			$totPaid+=$tot_paid;
			$totBalance+=$ordDetails['pt_resp'];
			//-------
			
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$html.=$qty;}
			$html.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:120px;">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$htmlPDF.=$qty;}
			$htmlPDF.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			if($ordDetails['module_type_id']==3){ //FOR CL - For OS
			
				$manufacName_os= $arrManufac[$ordDetails['manufacturer_id_os']];
				$qty=$ordDetails['qty_right'];

				$price=$ordDetails['price_os'];
				$totAmt=$price*$qty;
				$disc = $ordDetails['discount_os'];
				if($disc=="" || $disc==0.00){
					$disc = 0;
				}
				
				$discoutableAmt=$totAmt-$ordDetails['ins_amount_os'];
				$discount = cal_discount($discoutableAmt,$disc);
				$total_amount = $totAmt-$discount;
				$tot_paid=$ordDetails['pt_paid_os'];
				
				//TOTALS
				$totQty+=$qty;
				$totPrice+=$ordDetails['price_os'];
				$totDisc+=$discount;
				$totTotalAmt+=$total_amount;
				$totPatPaid+=$ordDetails['pt_paid_os'];
				$totInsPaid+=$ordDetails['ins_amount_os'];
				$totPaid+=$tot_paid;
				$totBalance+=$ordDetails['pt_resp_os'];
				//-------
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';			
			}				
		}
		
		/*Last Tax Row*/
		if( count($tax_data) > 0 ){
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			//TOTALS
			$totTotalAmt+=$tax_data['amt'];
			$totPatPaid+=$tax_data['paid'];
			$totPaid+=$tax_data['paid'];
			$totBalance+=$tax_data['resp'];
			if( !in_array($prev_ord_id, $tax_added) ){
				$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
				array_push($tax_added, $prev_ord_id);
			}
			array_push($tax_totals['total'], $tax_data['amt']);
			array_push($tax_totals['paid'], $tax_data['paid']);
			array_push($tax_totals['bal'], $tax_data['resp']);
			//-------
			$tax_data = array();
		}
		/*End Last Tax Row*/
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr><td class="reportTitle rptText13b" colspan="15">Orders Remake</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Entered Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:160px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Manufacturer</td>		
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Tot. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Operators<br>M / C</td>
			</tr>'.$html.'
			<tr>
			<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';
		$reportHtml.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';

		$reportHtmlPDF.='
		<table style="width:1050px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="15">Orders Remake</td></tr>
		<tr style="height:25px;">
			<td style="width:40px;"></td>
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>			
			<td style="width:120px;"></td>
			<td style="width:90px;"></td>
			<td style="width:40px;"></td>
			<td style="width:65px;"></td>
			<td style="width:50px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:40px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG"></td>
		</tr>';
		$reportHtmlPDF.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';
	}
	
	// Reorder
	$html=$htmlPDF='';
	$totQty=$totPrice=$totDisc=$totTotalAmt=$totPatPaid=$totInsPaid=$totPaid=$totBalance=0;
	if(count($arrAllReorder)>0){
		$grandAmount=0; $totQry= $totPrice=0;
		
		$tax_data = array();
		$prev_ord_id = false;
		$tax_totals = array('total'=>array(), 'paid'=>array(), 'bal'=>array());
		
		foreach($arrAllReorder as $ordDetails){
			$subTotAmt =0;
			
			$catName=$arrTypes[$ordDetails['module_type_id']];
			$manufacName= $arrManufac[$ordDetails['manufacturer_id']];
			$oprName = $arrUsersTwoChar[$ordDetails['actionBy']];
			
			if( $prev_ord_id && $prev_ord_id  != $ordDetails['id']){
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				//TOTALS
				$totTotalAmt+=$tax_data['amt'];
				$totPatPaid+=$tax_data['paid'];
				$totPaid+=$tax_data['paid'];
				$totBalance+=$tax_data['resp'];
				if( !in_array($prev_ord_id, $tax_added) ){
					$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
					array_push($tax_added, $prev_ord_id);
				}
				array_push($tax_totals['total'], $tax_data['amt']);
				array_push($tax_totals['paid'], $tax_data['paid']);
				array_push($tax_totals['bal'], $tax_data['resp']);
				//-------
				$tax_data = array();
			}
			
			$prev_ord_id = $ordDetails['id'];
			$tax_data['id']		= $ordDetails['id'];
			$tax_data['entered_date'] = $ordDetails['entered_date'];
			$tax_data['prac']	= $ordDetails['tax_prac_code'];
			$tax_data['amt']	= $ordDetails['tax_payable'];
			$tax_data['paid']	= $ordDetails['tax_pt_paid'];
			$tax_data['resp']	= $ordDetails['tax_pt_resp'];
			$tax_data['method']	= $arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'];
			$tax_data['mode']	= ucfirst($ordDetails['payment_mode']);
			$tax_data['opr']	= $oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']];
			
			//BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$ordDetails['qty'];
			//if($ordDetails['module_type_id']==3){ //FOR CL
			//	$qty+=$ordDetails['qty_right'];
			//}
			
			if($ordDetails['module_type_id']==2){
				/*Get Lens Details*/
				$sqlLensDetail = "SELECT SUM(`wholesale_price`) AS 'price', SUM(`ins_amount`) AS 'ins_amount', SUM(`pt_paid`) AS 'pt_paid', SUM(`total_amt`) AS 'total_amount', SUM(`pt_resp`) AS 'pt_resp' FROM `in_order_lens_price_detail` WHERE `order_detail_id`='".$ordDetails['order_detail_id']."' AND `del_status`=0";
				$lensData = imw_query($sqlLensDetail);
				$lensData = imw_fetch_array($lensData);
				
				$ordDetails['discount']		= $ordDetails['discount_val'];
				$ordDetails['price']		= $lensData['price'];
				$ordDetails['ins_amount']	= $lensData['ins_amount'];
				$ordDetails['pt_paid']		= $lensData['pt_paid'];
				$ordDetails['total_amount']	= $lensData['total_amount'];
				$ordDetails['pt_resp']		= $lensData['pt_resp'];
				
				if( !in_array($ordDetails['order_detail_id'], $arrPaymentAdded) ){
					$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'] + $ordDetails['ins_amount'];
					array_push($arrPaymentAdded, $ordDetails['order_detail_id']);
				}
			}
			
			$price=$ordDetails['price'];
			$totAmt=$price*$qty;
			$disc = $ordDetails['discount'];
			if($disc=="" || $disc==0.00){
				$disc = 0;
			}
			
			$discoutableAmt=$totAmt-$ordDetails['ins_amount'];
			$discount = cal_discount($discoutableAmt,$disc);
			$total_amount = $totAmt-$discount;
			//$tot_paid=$ordDetails['pt_paid']+$ordDetails['ins_amount'];
			$tot_paid=$ordDetails['pt_paid'];
			
			//TOTALS
			//do no count if it is lens
			if($ordDetails['module_type_id']!=2){
			$totQty+=$qty;
			}
			$totPrice+=$ordDetails['price'];
			$totDisc+=$discount;
			$totTotalAmt+=$total_amount;
			$totPatPaid+=$ordDetails['pt_paid'];
			$totInsPaid+=$ordDetails['ins_amount'];
			$totPaid+=$tot_paid;
			$totBalance+=$ordDetails['pt_resp'];
			//-------
			
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$html.=$qty;}
			$html.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:120px;">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$htmlPDF.=$qty;}
			$htmlPDF.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			if($ordDetails['module_type_id']==3){ //FOR CL - For OS
			
				$manufacName_os= $arrManufac[$ordDetails['manufacturer_id_os']];
				$qty=$ordDetails['qty_right'];

				$price=$ordDetails['price_os'];
				$totAmt=$price*$qty;
				$disc = $ordDetails['discount_os'];
				if($disc=="" || $disc==0.00){
					$disc = 0;
				}
				
				$discoutableAmt=$totAmt-$ordDetails['ins_amount_os'];
				$discount = cal_discount($discoutableAmt,$disc);
				$total_amount = $totAmt-$discount;
				$tot_paid=$ordDetails['pt_paid_os'];
				
				//TOTALS
				$totQty+=$qty;
				$totPrice+=$ordDetails['price_os'];
				$totDisc+=$discount;
				$totTotalAmt+=$total_amount;
				$totPatPaid+=$ordDetails['pt_paid_os'];
				$totInsPaid+=$ordDetails['ins_amount_os'];
				$totPaid+=$tot_paid;
				$totBalance+=$ordDetails['pt_resp_os'];
				//-------
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';			
			}				
		}
		
		/*Last Tax Row*/
		if( count($tax_data) > 0 ){
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			//TOTALS
			$totTotalAmt+=$tax_data['amt'];
			$totPatPaid+=$tax_data['paid'];
			$totPaid+=$tax_data['paid'];
			$totBalance+=$tax_data['resp'];
			if( !in_array($prev_ord_id, $tax_added) ){
				$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
				array_push($tax_added, $prev_ord_id);
			}
			array_push($tax_totals['total'], $tax_data['amt']);
			array_push($tax_totals['paid'], $tax_data['paid']);
			array_push($tax_totals['bal'], $tax_data['resp']);
			//-------
			$tax_data = array();
		}
		/*End Last Tax Row*/
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr><td class="reportTitle rptText13b" colspan="15">Reorder</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Entered Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:160px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Manufacturer</td>		
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Tot. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Operators<br>M / C</td>
			</tr>'.$html.'
			<tr>
			<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';
		$reportHtml.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';

		$reportHtmlPDF.='
		<table style="width:1050px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="15">Reorder</td></tr>
		<tr style="height:25px;">
			<td style="width:40px;"></td>
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>			
			<td style="width:120px;"></td>
			<td style="width:90px;"></td>
			<td style="width:40px;"></td>
			<td style="width:65px;"></td>
			<td style="width:50px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:40px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG"></td>
		</tr>';
		$reportHtmlPDF.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';
	}
	
	// Returned
	$html=$htmlPDF='';
	$totQty=$totPrice=$totDisc=$totTotalAmt=$totPatPaid=$totInsPaid=$totPaid=$totBalance=0;
	if(count($arrAllReturned)>0){
		$grandAmount=0; $totQry= $totPrice=0;
		
		$tax_data = array();
		$prev_ord_id = false;
		$tax_totals = array('total'=>array(), 'paid'=>array(), 'bal'=>array());
			
		foreach($arrAllReturned as $ordDetails){
			$subTotAmt =0;
			
			$catName=$arrTypes[$ordDetails['module_type_id']];
			$manufacName= $arrManufac[$ordDetails['manufacturer_id']];
			$oprName = $arrUsersTwoChar[$ordDetails['actionBy']];
			
			if( $prev_ord_id && $prev_ord_id  != $ordDetails['id']){
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
				</tr>';
				
				//TOTALS
				$totTotalAmt+=$tax_data['amt'];
				$totPatPaid+=$tax_data['paid'];
				$totPaid+=$tax_data['paid'];
				$totBalance+=$tax_data['resp'];
				if( !in_array($prev_ord_id, $tax_added) ){
					$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
					array_push($tax_added, $prev_ord_id);
				}
				array_push($tax_totals['total'], $tax_data['amt']);
				array_push($tax_totals['paid'], $tax_data['paid']);
				array_push($tax_totals['bal'], $tax_data['resp']);
				//-------
				$tax_data = array();
			}
			
			$prev_ord_id = $ordDetails['id'];
			$tax_data['id']		= $ordDetails['id'];
			$tax_data['entered_date'] = $ordDetails['entered_date'];
			$tax_data['prac']	= $ordDetails['tax_prac_code'];
			$tax_data['amt']	= $ordDetails['tax_payable'];
			$tax_data['paid']	= $ordDetails['tax_pt_paid'];
			$tax_data['resp']	= $ordDetails['tax_pt_resp'];
			$tax_data['method']	= $arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'];
			$tax_data['mode']	= ucfirst($ordDetails['payment_mode']);
			$tax_data['opr']	= $oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']];
			
			//BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$ordDetails['qty'];
			//if($ordDetails['module_type_id']==3){ //FOR CL
			//	$qty+=$ordDetails['qty_right'];
			//}
			
			if($ordDetails['module_type_id']==2){
				/*Get Lens Details*/
				$sqlLensDetail = "SELECT SUM(`wholesale_price`) AS 'price', SUM(`ins_amount`) AS 'ins_amount', SUM(`pt_paid`) AS 'pt_paid', SUM(`total_amt`) AS 'total_amount', SUM(`pt_resp`) AS 'pt_resp' FROM `in_order_lens_price_detail` WHERE `order_detail_id`='".$ordDetails['order_detail_id']."' AND `del_status`=0";
				$lensData = imw_query($sqlLensDetail);
				$lensData = imw_fetch_array($lensData);
				
				$ordDetails['discount']		= $ordDetails['discount_val'];
				$ordDetails['price']		= $lensData['price'];
				$ordDetails['ins_amount']	= $lensData['ins_amount'];
				$ordDetails['pt_paid']		= $lensData['pt_paid'];
				$ordDetails['total_amount']	= $lensData['total_amount'];
				$ordDetails['pt_resp']		= $lensData['pt_resp'];
				
				if( !in_array($ordDetails['order_detail_id'], $arrPaymentAdded) ){
					//$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'] + $ordDetails['ins_amount'];
					$arrPaymentBreakdown[ucfirst($ordDetails['payment_mode'])] += $ordDetails['pt_paid'];
					array_push($arrPaymentAdded, $ordDetails['order_detail_id']);
				}
			}
			
			$price=$ordDetails['price'];
			$totAmt=$price*$qty;
			$disc = $ordDetails['discount'];
			if($disc=="" || $disc==0.00){
				$disc = 0;
			}
			
			$discoutableAmt=$totAmt-$ordDetails['ins_amount'];
			$discount = cal_discount($discoutableAmt,$disc);
			$total_amount = $totAmt-$discount;
			//$tot_paid=$ordDetails['pt_paid']+$ordDetails['ins_amount'];
			$tot_paid=$ordDetails['pt_paid'];
			
			//TOTALS
			//do no count if it is lens
			if($ordDetails['module_type_id']!=2){
			$totQty+=$qty;
			}
			$totPrice+=$ordDetails['price'];
			$totDisc+=$discount;
			$totTotalAmt+=$total_amount;
			$totPatPaid+=$ordDetails['pt_paid'];
			$totInsPaid+=$ordDetails['ins_amount'];
			$totPaid+=$tot_paid;
			$totBalance+=$ordDetails['pt_resp'];
			//-------
			
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ret_ord_det_date_arr[$ordDetails['order_detail_id']].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">'.$ret_ord_det_qty_arr[$ordDetails['order_detail_id']].'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:900px;">&nbsp;'.$ordDetails['item_name'].' - '.$ordDetails['upc_code'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:600px;">&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName.'</td>
			<td class="whiteBG rptText13 alignRight">';
			if($ordDetails['module_type_id']!=2){$htmlPDF.=$qty;}
			$htmlPDF.='&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
			</tr>';

			if($ordDetails['module_type_id']==3){ //FOR CL - For OS
			
				$manufacName_os= $arrManufac[$ordDetails['manufacturer_id_os']];
				$qty=$ordDetails['qty_right'];

				$price=$ordDetails['price_os'];
				$totAmt=$price*$qty;
				$disc = $ordDetails['discount_os'];
				if($disc=="" || $disc==0.00){
					$disc = 0;
				}
				
				$discoutableAmt=$totAmt-$ordDetails['ins_amount_os'];
				$discount = cal_discount($discoutableAmt,$disc);
				$total_amount = $totAmt-$discount;
				$tot_paid=$ordDetails['pt_paid_os'];
				
				//TOTALS
				$totQty+=$qty;
				$totPrice+=$ordDetails['price_os'];
				$totDisc+=$discount;
				$totTotalAmt+=$total_amount;
				$totPatPaid+=$ordDetails['pt_paid_os'];
				$totInsPaid+=$ordDetails['ins_amount_os'];
				$totPaid+=$tot_paid;
				$totBalance+=$ordDetails['pt_resp_os'];
				//-------
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$ordDetails['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$catName.'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$ordDetails['item_name_os'].' - '.$ordDetails['upc_code_os'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$ordDetails['lname'].' - '.$ordDetails['fname'].' - '.$ordDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:90px;">&nbsp;'.$manufacName_os.'</td>
				<td class="whiteBG rptText13 alignRight">'.$qty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['price_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($discount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($total_amount,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_paid_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['ins_amount_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($tot_paid,2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$arrPaymentModes[strtolower($ordDetails['payment_mode'])].' '.$ordDetails['checkNo'].'</td>
				<td class="whiteBG rptText13 alignRight">'.numberFormat($ordDetails['pt_resp_os'],2).'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.' / '.$arrUsersTwoChar[$ordDetails['madeBy']].'</td>
				</tr>';			
			}				
		}
		
		/*Last Tax Row*/
		if( count($tax_data) > 0 ){
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">'.$tax_data['mode'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$tax_data['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" colspan="6">&nbsp;Tax:</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['amt'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['paid'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:65px;">'.$tax_data['method'].'</td>
			<td class="whiteBG rptText13 alignRight">'.numberFormat($tax_data['resp'],2).'&nbsp;</td>
			<td class="whiteBG rptText13 alignCenter">'.$tax_data['opr'].'</td>
			</tr>';
			
			//TOTALS
			$totTotalAmt+=$tax_data['amt'];
			$totPatPaid+=$tax_data['paid'];
			$totPaid+=$tax_data['paid'];
			$totBalance+=$tax_data['resp'];
			if( !in_array($prev_ord_id, $tax_added) ){
				$arrPaymentBreakdown[$tax_data['mode']] += $tax_data['paid'];
				array_push($tax_added, $prev_ord_id);
			}
			array_push($tax_totals['total'], $tax_data['amt']);
			array_push($tax_totals['paid'], $tax_data['paid']);
			array_push($tax_totals['bal'], $tax_data['resp']);
			//-------
			$tax_data = array();
		}
		/*End Last Tax Row*/
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr><td class="reportTitle rptText13b" colspan="15">Orders Returned</td></tr>
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Returned Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:160px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Manufacturer</td>		
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Returned Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Tot. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:85px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Operators<br>M / C</td>
			</tr>'.$html.'
			<tr>
			<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG">&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';
		$reportHtml.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';

		$reportHtmlPDF.='
		<table style="width:1050px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr><td class="reportTitle rptText13b" colspan="15">Orders Returned</td></tr>
		<tr style="height:25px;">
			<td style="width:40px;"></td>
			<td style="width:60px;"></td>
			<td style="width:90px;"></td>			
			<td style="width:120px;"></td>
			<td style="width:90px;"></td>
			<td style="width:40px;"></td>
			<td style="width:65px;"></td>
			<td style="width:50px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:65px;"></td>
			<td style="width:40px;"></td>
		</tr>
		'.$htmlPDF.'
		<tr>
		<td colspan="15" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="5">Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG"></td>
		</tr>';
		$reportHtmlPDF.='<tr style="height:25px">
			<td class="whiteBG rptText13 alignRight" colspan="8">Total Tax : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['total']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).'0.00&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['paid']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG">&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($tax_totals['bal']), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
		</tr></table>';
	}
	
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
	</style>';		

	//SELECTION TO SHOW
	
	if(empty($_POST['operators'])==false){ $arrSel=explode(',', $_POST['operators']); }
	if(empty($_POST['facility'])==false){ $arrfac=explode(',', $_POST['facility']); }
	$selOpr='All';
	$selFac='All';
	$selOpr=(count($arrSel)>1)? 'Multi' : ((count($arrSel)=='1')? ucfirst($arrUsers[$showOpr]): $selOpr);
	$selFac=(count($arrfac)>1)? 'Multi' : ((count($arrfac)=='1')? ucfirst($arrfacility[$_POST['facility']]): $selFac);

	//PAYMENT BREAKDOWN
	$htmlPaymentBreakdown='';
	if(sizeof($arrPaymentBreakdown)>0){
		$htmlPaymentBreakdown=
		'<table style="border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr style="height:25px;"><td class="whiteBG rptText13b alignCenter" colspan="2">Payment Breakdown</td></tr>';
		
		foreach($arrPaymentBreakdown as $method =>$paidAmt){
			if($paidAmt>0){
				$totMethods+=$paidAmt;
				$htmlPaymentBreakdown.=
				'<tr style="height:25px;">
					<td class="whiteBG rptText13 alignRight" style="text-align:right; width:100px">'.$method.'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight" style="text-align:right; width:150px">'.currency_symbol(true).number_format($paidAmt, 2, '.', '').'&nbsp;</td>
				</tr>';
			}
		}
		$htmlPaymentBreakdown.=
		'<tr style="height:25px">
			<td class="whiteBG rptText13b alignRight">Total Payments : </td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totMethods, 2, '.', '').'&nbsp;</td>
		</tr>
		</table>';				
	}//--------------	

	//FINAL HTML
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" width="220px">&nbsp;Day Report</td>
		<td style="text-align:left;" class="reportHeadBG" width="auto" >&nbsp;Report for Date : From '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
		<td style="text-align:left;" class="reportHeadBG" width="auto">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
	</tr>
	<tr style="height:20px;">
		<td class="reportHeadBG">&nbsp;Operator : '.$selOpr.'</td>
		<td class="reportHeadBG" colspan="2">&nbsp;Facility : '.$selFac.'</td>
	</tr>
	</table>
	'.$reportHtml.
	$htmlPaymentBreakdown;

	if(count($arrMainStock)>0)
	{
	//FINAL PDF
		$finalReportHtmlPDF.='
			<page backtop="13mm" backbottom="5mm">
			<page_footer>
					<table style="width: 1050px;">
						<tr>
							<td style="text-align: center;	width: 1050px">Page [[page_cu]]/[[page_nb]]</td>
						</tr>	
					</table>
			</page_footer>
			<page_header>		
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
			<tr style="height:20px;">
				<td style="text-align:left;" class="reportHeadBG" width="350">&nbsp;Day Report</td>
				<td style="text-align:left;" class="reportHeadBG" width="310">&nbsp;Report for Date : From '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
				<td style="text-align:left;" class="reportHeadBG" width="400">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
			</tr>
			<tr style="height:20px;">
				<td class="reportHeadBG">&nbsp;Operator : '.$selOpr.'</td>
				<td class="reportHeadBG" colspan="2">&nbsp;Facility : '.$selFac.'</td>
			</tr>
			</table>
			<table width="1050" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:150px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:200px;">Product Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Entered Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:180px;">Manufacturer</td>			
				<td class="reportHeadBG1" style="text-align:center; width:180px;">Facility</td>
				<td class="reportHeadBG1" style="text-align:center; width:80px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:150px;">Operator</td>
			</tr>
		</table></page_header>'.
			$itemreportHtmlPDF.'
		</page>';
	}	
	if(count($arrAllPending)>0 || count($arrAllOrdered)>0 || count($arrAllReceived)>0 || count($arrAllNotified)>0 || count($arrAllDispensed)>0 || count($arrAllcancelled)>0 || count($arrAllRemake)>0 || count($arrAllReorder)>0 || count($arrAllReturned)>0)
	{
	//FINAL PDF
		$finalReportHtmlPDF.='
			<page backtop="17mm" backbottom="5mm">
			<page_footer>
					<table style="width: 1050px;">
						<tr>
							<td style="text-align: center;	width: 1050px">Page [[page_cu]]/[[page_nb]]</td>
						</tr>	
					</table>
			</page_footer>
			<page_header>		
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
			<tr style="height:20px;">
				<td style="text-align:left;" class="reportHeadBG" width="350">&nbsp;Day Report</td>
				<td style="text-align:left;" class="reportHeadBG" width="310">&nbsp;Report for Date : From '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
				<td style="text-align:left;" class="reportHeadBG" width="407">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
			</tr>
			<tr style="height:20px;">
				<td class="reportHeadBG" width="350">&nbsp;Operator : '.$selOpr.'</td>
				<td class="reportHeadBG" colspan="2">&nbsp;Facility : '.$selFac.'</td>
			</tr>
			</table>
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
			<tr style="height:100px;">
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Ord#</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Entered Date</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Category</td>
				<td class="reportHeadBG1" style="text-align:center; width:90px;">Item Name-UPC Code</td>
				<td class="reportHeadBG1" style="text-align:center; width:60px;">Patient Name-Id</td>
				<td class="reportHeadBG1" style="text-align:center; width:90px;">Manufacturer</td>			
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:65px;">Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:50px;">Disc.</td>
				<td class="reportHeadBG1" style="text-align:center; width:65px;">Total Amt.</td>
				<td class="reportHeadBG1" style="text-align:center; width:65px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:65px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:65px;">Total Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:65px;">Method</td>
				<td class="reportHeadBG1" style="text-align:center; width:65px;">Balance</td>
				<td class="reportHeadBG1" style="text-align:center; width:40px;">Operators<br>M / C</td>
			</tr>
		</table></page_header>'.
			$reportHtmlPDF.
			$htmlPaymentBreakdown.'
		</page>';	
	}
  $pdfText = $css.$finalReportHtmlPDF;
  file_put_contents('../../library/new_html2pdf/day_report_result.html',$pdfText);	
	
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
if(count($arrAllPending)>0 || count($arrAllOrdered)>0 || count($arrAllReceived)>0 || count($arrAllNotified)>0|| count($arrAllDispensed)>0 || count($arrMainStock)>0 || count($arrAllcancelled)>0 || count($arrAllRemake)>0 || count($arrAllReorder)>0 || count($arrAllReturned)>0)
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
<form name="searchFormResult" action="day_report_result.php" method="post">
<input type="hidden" name="operators" id="operators" value="" />
<input type="hidden" name="status" id="status" value="" />
<input type="hidden" name="facility" id="facility" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="iportal_orders" id="iportal_orders" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=l&file_name=../../library/new_html2pdf/day_report_result';
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
	// if(numr>0 || numr2>0){
	// 	mainBtnArr[2] = new Array("generate_day_report_csv_btn","Export","top.main_iframe.reports_iframe.printcsv()");
	// }
	top.btn_show("admin",mainBtnArr);
	
	top.main_iframe.loading('none');
});
</script>

</body>
</html>
