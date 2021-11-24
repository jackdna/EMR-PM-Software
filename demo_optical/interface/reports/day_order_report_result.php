<?php
/*
File: day_order_report_result.php
Coded in PHP7
Purpose: Day Order Report
Access Type: Direct access
*/
// last updated : 8/1/2018 4:12PM G
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

$class_c="whiteBG rptText13 alignCenter";
$class_l="whiteBG rptText13 alignLeft";
$class_r="whiteBG rptText13 alignRight";

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
	   $typeRs = imw_query("select * from in_module_type");
	   while($typeRes=imw_fetch_array($typeRs)){
		   $arrTypes[$typeRes['id']]=$typeRes['module_type_name'];
	   }
	   //add additional changes type manually
	 	$arrTypes[9]='Additional Charges';
   //Facility
   $facRs = imw_query("select * from in_location");
   while($facRes=imw_fetch_array($facRs)){
	   $arrfacility[$facRes['id']]=$facRes['loc_name'];
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

	//GETTING ALL DISINFECTING FOR CL
	$arr_all_disinfecting=array();
	$sel_ret=imw_query("select id, name FROM in_cl_disinfecting");
	while($row_ret=imw_fetch_assoc($sel_ret)){
		$arr_all_disinfecting[$row_ret['id']]=$row_ret['name'];
	}
	
	
	$sel_ret=imw_query("select order_id,patient_id,order_detail_id,return_qty,DATE_FORMAT(entered_date, '%m-%d-%y') as fn_entered_date from in_order_return where del_status='0' group by order_id");
	while($row_ret=imw_fetch_array($sel_ret)){
		$ret_ord_arr[$row_ret['order_id']]=$row_ret['order_id'];
		$ret_ord_det_arr[$row_ret['order_detail_id']]=$row_ret['order_detail_id'];
		$ret_ord_det_date_arr[$row_ret['order_detail_id']]=$row_ret['fn_entered_date'];
		$ret_ord_det_qty_arr[$row_ret['order_detail_id']]=$row_ret['return_qty'];
	}
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	$show_report=$_POST['show_report'];
	

	/*$mainQry="Select ord_sts.order_id, ord_sts.order_detail_id, ord_sts.order_qty, ord_sts.order_date, DATE_FORMAT(ord_sts.order_date, '%m-%d-%y') as 'order_date_disp',
	ord_sts.operator_id, ord_sts.item_id, ord_sts.order_status,
	ord.order_id, ord.id, ord.price, ord.price_os, ord.loc_id, ord.qty, ord.qty_right,
	ord.discount,ord.discount_os, ord.total_amount, ord.total_amount_os, ord.patient_id, ord.module_type_id, ord.upc_code, ord.upc_code_os,in_order.overall_discount,
	ord.pt_paid, ord.pt_paid_os,in_order.total_price, ord.ins_amount,ord.order_id as order_id_detail_table, ord.ins_amount_os, ord.pt_resp, ord.pt_resp_os, ord.del_status,ord.operator_id as 'madeBy',
	cl_disinfecting.price as 'cl_disinfect_price', cl_disinfecting.qty as 'cl_disinfect_qty',
	cl_disinfecting.pt_paid as 'cl_disinfect_pt_paid',cl_disinfecting.pt_resp as 'cl_disinfect_pt_resp', cl_disinfecting.ins_amount as 'cl_disinfect_ins_amount',
	cl_disinfecting.discount as 'cl_disinfect_discount', cl_disinfecting.total_amount as 'cl_disinfect_total_amount', cl_disinfecting.item_id as 'cl_disinfect_item_id',
	in_order.re_make_id,in_order.re_order_id,
	in_order.payment_mode,in_order.order_status as final_order_status, in_order.checkNo, in_order.tax_prac_code, in_order.tax_payable, in_order.tax_pt_paid,
	in_order.tax_pt_resp, ord.item_name, ord.item_name_os, DATE_FORMAT(ord.entered_date, '%m-%d-%Y') as 'enteredDate', patient_data.fname,
	patient_data.lname 
	FROM in_order_detail_status ord_sts 
	RIGHT JOIN in_order_details ord ON ord.id= ord_sts.order_detail_id 
	RIGHT JOIN in_order ON in_order.id= ord.order_id 
	LEFT JOIN in_order_cl_detail cl_disinfecting ON cl_disinfecting.order_detail_id = ord.id 
	LEFT JOIN patient_data ON patient_data.id = ord.patient_id 
	WHERE (ord_sts.order_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";*/
	
	$mainQry="Select ord.order_id, ord.id order_detail_id, SUM(cl_det.discount) cl_line_discount, ord.entered_date order_date, DATE_FORMAT(ord.entered_date, '%m-%d-%y') as 'order_date_disp',
	ord.operator_id, ord.item_id, ord.order_status,
	ord.order_id, ord.id, ord.price, ord.price_os, ord.loc_id, ord.qty, ord.qty_right,
	ord.discount,ord.discount_os, ord.total_amount, ord.total_amount_os, ord.patient_id, ord.module_type_id, ord.upc_code, ord.upc_code_os,in_order.overall_discount,
	ord.pt_paid, ord.pt_paid_os,in_order.total_price, ord.ins_amount,ord.order_id as order_id_detail_table, ord.ins_amount_os, ord.pt_resp, ord.pt_resp_os, ord.del_status,ord.operator_id as 'madeBy',
	cl_disinfecting.price as 'cl_disinfect_price', cl_disinfecting.qty as 'cl_disinfect_qty',
	cl_disinfecting.pt_paid as 'cl_disinfect_pt_paid',cl_disinfecting.pt_resp as 'cl_disinfect_pt_resp', cl_disinfecting.ins_amount as 'cl_disinfect_ins_amount',
	cl_disinfecting.discount as 'cl_disinfect_discount', cl_disinfecting.total_amount as 'cl_disinfect_total_amount', cl_disinfecting.item_id as 'cl_disinfect_item_id',
	in_order.re_make_id, in_order.re_order_id, in_order.total_qty,
	in_order.payment_mode,in_order.order_status as final_order_status, in_order.checkNo, in_order.tax_prac_code, in_order.tax_payable, in_order.tax_pt_paid,
	in_order.tax_pt_resp, ord.item_name, ord.item_name_os, patient_data.fname,
	patient_data.lname 
	FROM in_order_details ord 
	LEFT JOIN in_order_lens_price_detail cl_det ON ord.id=cl_det.order_detail_id
	RIGHT JOIN in_order ON in_order.id= ord.order_id 
	LEFT JOIN in_order_cl_detail cl_disinfecting ON cl_disinfecting.order_detail_id = ord.id 
	LEFT JOIN patient_data ON patient_data.id = ord.patient_id 
	LEFT JOIN in_optical_order_form rx ON rx.order_id = in_order.id
	WHERE (ord.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";

	if(empty($_POST['order_status'])==false){
		if($_POST['order_status']=='reorder'){
			$mainQry.=" AND in_order.re_order_id>'0' AND ord.del_status='0'";
		}elseif($_POST['order_status']=='remake'){
			$mainQry.=" AND in_order.re_make_id>'0' AND ord.del_status='0'";
		}elseif($_POST['order_status']=='cancelled'){
			$mainQry.=" AND ord.del_status='1'";
		}elseif($_POST['order_status']=='returned'){
			$ret_ord_det_imp=implode("','",$ret_ord_det_arr);
			$mainQry.=" AND ord.id in('".$ret_ord_det_imp."') AND ord.del_status='0'";
		}else{
			//$mainQry.=" AND ord.del_status='0' AND ord_sts.order_status='".$_POST['order_status']."'";
			$mainQry.=" AND ord.del_status='0' AND ord.order_status='".$_POST['order_status']."'";
		}
	}else{
		$mainQry.=" AND ord.del_status='0'";
	}
	if(empty($_POST['product_type'])==false){
		$mainQry.=' AND ord.module_type_id IN('.$_POST['product_type'].')';
	}
	if(empty($_POST['operators'])==false){
		$mainQry.=' AND ord.operator_id IN('.$_POST['operators'].')';
	}
	if(empty($_POST['physician'])==false){
		$mainQry.=' AND rx.physician_id IN('.$_POST['physician'].')';
	}
	if(empty($_POST['faclity'])==false){
		$mainQry.=' AND ord.loc_id IN('.$_POST['faclity'].')';
	}
	if(empty($_POST['iportal_orders'])==false){
		$mainQry.=" AND in_order.iportal_cl_order_id>'0'";
	}

	$mainQry.=' GROUP BY ord.id ORDER BY ord.entered_date DESC, ord.entered_time DESC, ord.order_id desc ';
	
	$prv_order_id = false;
	$prv_status	  = false;
	$tax_data = array();
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_assoc($mainRs)){
		$qty=$totAmt=$disc=0;
		$ord_id= $mainRes['order_id'];
		$ord_det_id= $mainRes['order_detail_id'];
		$ord_sts= $mainRes['order_status'];
		$date=$mainRes['order_date'];
		$item_type=$mainRes['module_type_id'];
		
		if($ord_sts=='pending'){ //BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$mainRes['qty'];
			if($item_type==3){ //FOR CL
				$qty+=$mainRes['qty_right'];
			}
		}else{
			$qty=$mainRes['qty'];
			if($item_type==3){$qty+=$mainRes['qty_right'];}
		}
		if($mainRes['re_make_id']>0){
			$ord_sts=$ord_sts.' / Remake';
		}
		if($mainRes['re_order_id']>0){
			$ord_sts=$ord_sts.' / Reorder';
		}
		if($mainRes['del_status']>0){
			$ord_sts=$ord_sts.' / Cancelled';
		}
		if($ret_ord_det_arr[$ord_det_id]>0){
			$ord_sts=$ord_sts.' / Returned';
		}
		//FOR DETAIL
		if($item_type!=3 || $mainRes['qty_right']>0){
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['ord_id']=$ord_id;
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['date']=$mainRes['order_date_disp'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['loc_id']=$mainRes['loc_id'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['item']=$mainRes['upc_code'].' - '.$mainRes['item_name'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['qty']=($item_type==3)?$mainRes['qty_right']:$mainRes['qty'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['price']=$mainRes['price'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['total_price']=$mainRes['total_price'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['total_amount']=$mainRes['total_amount'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['discount']=$mainRes['discount']+$mainRes['cl_line_discount'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['overall_discount']=$mainRes['overall_discount'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['payment_mode']=$mainRes['payment_mode'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['check_no']=$mainRes['checkNo'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['pat_paid']=$mainRes['pt_paid'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['ins_paid']=$mainRes['ins_amount'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['balance']=$mainRes['pt_resp'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['operator']=$mainRes['operator_id'];
		$arrMainDetail[$date][$ord_det_id][$ord_sts]['madeBy']=$mainRes['madeBy'];
		}
		if($item_type==3 && $mainRes['qty']>0){ //FOR CL - FOR OS ROW
			$ord_det_id='0'.$ord_det_id;
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['ord_id']=$ord_id;
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['date']=$mainRes['order_date_disp'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['loc_id']=$mainRes['loc_id'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['item']= $mainRes['upc_code_os'].' - '.$mainRes['item_name_os'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['qty']=$mainRes['qty'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['price']=$mainRes['price_os'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['total_amount']=$mainRes['total_amount_os'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['discount']=$mainRes['discount_os'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['payment_mode']=$mainRes['payment_mode'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['check_no']=$mainRes['checkNo'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['pat_paid']=$mainRes['pt_paid_os'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['ins_paid']=$mainRes['ins_amount_os'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['balance']=$mainRes['pt_resp_os'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['operator']=$mainRes['operator_id'];
			$arrMainDetail[$date][$ord_det_id][$ord_sts]['madeBy']=$mainRes['madeBy'];		

			if($mainRes['cl_disinfect_price']>0){
				$ord_det_id='00'.$ord_det_id;
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['ord_id']=$ord_id;
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['date']=$mainRes['order_date_disp'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['loc_id']=$mainRes['loc_id'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['item']= $arr_all_disinfecting[$mainRes['cl_disinfect_item_id']];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['qty']=$mainRes['cl_disinfect_qty'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['price']=$mainRes['cl_disinfect_price'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['total_amount']=$mainRes['cl_disinfect_total_amount'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['discount']=$mainRes['cl_disinfect_discount'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['payment_mode']=$mainRes['payment_mode'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['check_no']=$mainRes['checkNo'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['pat_paid']=$mainRes['cl_disinfect_pt_paid'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['ins_paid']=$mainRes['cl_disinfect_ins_amount'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['balance']=$mainRes['cl_disinfect_pt_resp'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['operator']=$mainRes['operator_id'];
				$arrMainDetail[$date][$ord_det_id][$ord_sts]['madeBy']=$mainRes['madeBy'];		
			}
		}
			
		if($mainRes['re_make_id']>0){
			$ord_sts='remake';
		}
		if($mainRes['re_order_id']>0){
			$ord_sts='reorder';
		}
		if($ret_ord_det_arr[$ord_det_id]>0){
			$ord_sts='returned';
		}
		if($mainRes['del_status']>0){
			$ord_sts='cancelled';
		}
		
		
		
		//FOR SUMMARY
		if($item_type==3){ //FOR CL
			if($mainRes['qty_right']>0)$clTotOd= $mainRes['price'] * $mainRes['qty_right'];
			if($mainRes['qty']>0)$clTotOs= $mainRes['price_os'] * $mainRes['qty'];
			$totAmt=$clTotOd + $clTotOs;
			
			$mainRes['pt_paid']+= $mainRes['pt_paid_os'];
			$mainRes['ins_amount']+= $mainRes['ins_amount_os'];
			$mainRes['pt_resp']+= $mainRes['pt_resp_os'];
			
			//IF DISINFECTING EXISTS
			if($mainRes['cl_disinfect_price']>0){
				$totAmt+= $mainRes['cl_disinfect_price'] * $mainRes['cl_disinfect_qty'];
				$mainRes['pt_paid']+= $mainRes['cl_disinfect_pt_paid'];
				$mainRes['pt_resp']+= $mainRes['cl_disinfect_pt_resp'];
				$mainRes['ins_amount']+= $mainRes['cl_disinfect_ins_amount'];
			}
		}else{
			$price=$mainRes['price'];
			$totAmt=$price*$qty;
		}
	
		$disc=$mainRes['overall_discount'];
		if($disc=="" || $disc==0.00){
			$disc = 0;
		}
		$discoutableAmt=$mainRes['total_price']-$mainRes['ins_amount'];
		$overall_discount = cal_discount($discoutableAmt,$disc);
		// SUMMARY block ends here
		
		
		//$total_amount = ($totAmt+$mainRes['tax_pt_resp'])-$overall_discount;
		$total_amount = $totAmt-$overall_discount;

		//ADD TAX
		$tax_payable=$tax_pt_paid=$tax_pt_resp=0;
		if(!$arrOrdTemp[$ord_id]){
			$tax_payable = $mainRes['tax_payable'];
			$tax_pt_paid = $mainRes['tax_pt_paid'];
			$tax_pt_resp = $mainRes['tax_pt_resp'];
			//echo "$ord_id = $tax_pt_resp <br/>";
			$arrOrdTemp[$ord_id]=$ord_id;//if we do comment this then tax being added multiple time total tax being added in each item in summary view
		}
		$dicount=$mainRes['discount']+$mainRes['cl_line_discount'];
		if($item_type==3){$dicount+=$mainRes['discount_os']+$mainRes['cl_disinfect_discount'];}
		//$total_ins = 0;
		//SUMMARY - FIRST BLOCK
		$arrSummary[$date][$ord_id]['ord_id']=$ord_id;
		$arrSummary[$date][$ord_id]['date']=$mainRes['order_date_disp'];
		$arrSummary[$date][$ord_id]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
		$arrSummary[$date][$ord_id]['loc_id']=$mainRes['loc_id'];
		$arrSummary[$date][$ord_id]['total_amount']=$mainRes['total_price'];
		$arrSummary[$date][$ord_id]['total_tax']=$mainRes['tax_payable'];
		$arrSummary[$date][$ord_id]['pat_paid'][$ord_det_id]=$mainRes['pt_paid'] + $tax_pt_paid;		
		$arrSummary[$date][$ord_id]['ins_paid'][$ord_det_id]=$mainRes['ins_amount'];
		$arrSummary[$date][$ord_id]['balance'][$ord_det_id]=$mainRes['pt_resp'] + $tax_pt_resp;
		$arrSummary[$date][$ord_id]['operator']=$mainRes['operator_id'];
		$arrSummary[$date][$ord_id]['madeBy']=$mainRes['madeBy'];
		$arrSummary[$date][$ord_id]['discount'][$ord_det_id]=$dicount;
		$arrSummary[$date][$ord_id]['overall_discount']=$overall_discount;
		$arrSummary[$date][$ord_id]['order_status'][$mainRes['order_status']]= $mainRes['order_status'];	
				
		//$totPaid=$mainRes['pt_paid']+$mainRes['ins_amount'];		
		$totPaid=$mainRes['pt_paid'];		
		
		if($mainRes['final_order_status'] == $mainRes['order_status'])
		{
			$arrMainSummaryDemo[$mainRes['final_order_status']][$mainRes['module_type_id']][$mainRes['order_detail_id']] =  $mainRes; 
		}
		//echo'<pre>';print_r($mainRes);echo'</pre>';
		//Grand Totals
		if(!$arrTemp[$ord_det_id]){
			$totInsPaid+=$mainRes['ins_amount'];
			$totPatPaid+=$mainRes['pt_paid']+$tax_pt_paid;
			
			//PAYMENT METHOD BREAKDOWN
			$method=ucfirst($mainRes['payment_mode']);
			$arrPaymentBreakdown[$method]+=$mainRes['pt_paid']+$tax_pt_paid;
			array_push($arrPaymentAdded, $mainRes['order_detail_id']);
			
			$arrTemp[$ord_det_id]=$ord_det_id;

			$tax_data[$ord_sts][$method]['amt'][$mainRes['order_id']] = $tax_payable;
			$tax_data[$ord_sts][$method]['paid'][$mainRes['order_id']] = $tax_pt_paid;
			$tax_data[$ord_sts][$method]['resp'][$mainRes['order_id']] = $tax_pt_resp;
		}
	}
	//echo "<pre>";
	//print_r($arrSummary);
	foreach( $tax_data as $tax_ord_status => $tax_dt_payment ){
		
		foreach( $tax_dt_payment as $tax_payment_method => $tax_dt ){
			$tax_amt_total	= array_sum($tax_dt['amt']);
			$tax_paid_total	= array_sum($tax_dt['paid']);
			$tax_resp_total	= array_sum($tax_dt['resp']);
			
			//SUMMARY - SECOND BLOCK
			$arrMainSummary[$tax_ord_status]['tax']['total_amount']	+= $tax_amt_total;
			$arrMainSummary[$tax_ord_status]['tax']['pat_paid']		+= $tax_paid_total;
			$arrMainSummary[$tax_ord_status]['tax']['balance']		+= $tax_resp_total;
			
			//$totPatPaid+=$tax_paid_total;
			//$arrPaymentBreakdown[$tax_payment_method]+=$tax_paid_total;
		}
	}

	//WORK FOR SUMMARY
	$summaryLoopArray['pending']='pending';
	$summaryLoopArray['ordered']='ordered';
	$summaryLoopArray['received']='received';
	$summaryLoopArray['notified']='notified';
	$summaryLoopArray['dispensed']='dispensed';
	$summaryLoopArray['remake']='remake';
	$summaryLoopArray['reorder']='reorder';
	$summaryLoopArray['cancelled']='cancelled';
	$summaryLoopArray['returned']='returned';
	$arrPaymentModes=array("cash"=>"Cash", "check"=>"Check", "credit card"=>"CC", "money order"=>"MO", "eft"=>"EFT");


	//order detail
	$html=$htmlPDF=$reportHtml=$reportHtmlPDF='';
	$arrTemp=array();
	if(count($arrMainDetail)>0){
		$grandAmount=0; 
		$totPaid=0;

		if($show_report=="detail")
		{//echo'<pre>';print_r($arrMainDetail);echo'</pre>';
			foreach($arrMainDetail as $date => $dateData){
				foreach($dateData as $ord_det_id => $ordData){
					foreach($ordData as $ord_sts => $itemDetails){
						$tot_paid=0;
						$oprName = $arrUsersTwoChar[$itemDetails['operator']];
						if($arrUsersTwoChar[$itemDetails['madeBy']] != ''){
							$madeByName = ' / '.$arrUsersTwoChar[$itemDetails['madeBy']];
						}else{
							$madeByName = '';	
						}
						$price=$itemDetails['price'];
						$totAmt=$price*$itemDetails['qty'];
						$disc = $itemDetails['discount'];
						if($disc=="" || $disc==0.00){
							$disc = 0;
						}
						
						$discoutableAmt=$totAmt-$itemDetails['ins_paid'];
						$discount = cal_discount($discoutableAmt,$disc);
						$total_amount = $totAmt;

						
						if($itemDetails['total_price'])
						{
							//$total_amount = $itemDetails['total_price'];	
							//$grandTotalAmt[$itemDetails['ord_id']][] = $itemDetails['total_price'];	
						}					
						
						$tot_paid=$itemDetails['pat_paid']+$itemDetails['ins_paid'];
						//$tot_paid=$itemDetails['pat_paid'];
						$show_all_status=0;
						
						if(constant('SHOW_ALL_STS_REPORT')=="TRUE" || !defined('SHOW_ALL_STS_REPORT'))
						{
							$show_all_status=1;
						}else{
							if(!$arrTemp[$ord_det_id])$show_all_status=1;
						}
						if(!$arrTemp[$ord_det_id]){
							$totQty+=$itemDetails['qty'];
							$totPrice+=$price;
							$totDisc+=$discount;
							$totTotalAmt+=$total_amount;
							$totPtPaid+=$itemDetails['pat_paid'];
							//$totInsPaid+=$itemDetails['ins_paid'];
							$totPaid+=$tot_paid;
							$totBalance+=$itemDetails['balance'];

							//PAYMENT METHOD BREAKDOWN
							//$method=ucfirst($itemDetails['payment_mode']);
							//$arrPaymentBreakdown[$method]+=$tot_paid;
							$arrTemp[$ord_det_id]=$ord_det_id;
						}

												
						if($show_all_status==1){
							$item_row='<tr style="height:20px;">
							<td class="'.$class_c.'">'.$itemDetails['ord_id'].'</td>
							<td class="'.$class_c.'">'.$itemDetails['date'].'</td>
							<td class="'.$class_l.'" style="width:105px;word-wrap: break-word">'.$itemDetails['patient_name'].'</td>
							<td class="'.$class_l.'" style="width:80px;word-wrap: break-word">'.$arrfacility[$itemDetails['loc_id']].'</td>
							<td class="'.$class_l.'" style="width:90px;word-wrap: break-word">'.$itemDetails['item'].'</td>
							<td class="'.$class_r.'">'.$itemDetails['qty'].'&nbsp;</td>
							<td class="'.$class_r.'">'.currency_symbol(true).$itemDetails['price'].'&nbsp;</td>
							<td class="'.$class_r.'">'.currency_symbol(true).number_format($discount, 2, '.', '').'&nbsp;</td>
							<td class="'.$class_r.'">'.currency_symbol(true).number_format($total_amount, 2, '.', '').'&nbsp;</td>
							<td class="'.$class_r.'">'.currency_symbol(true).number_format($itemDetails['pat_paid'], 2, '.', '').'&nbsp;</td>
							<td class="'.$class_r.'">'.currency_symbol(true).number_format($itemDetails['ins_paid'], 2, '.', '').'&nbsp;</td>
							<td class="'.$class_r.'">'.currency_symbol(true).number_format($tot_paid, 2, '.', '').'&nbsp;</td>
							<td class="'.$class_l.'">'.$arrPaymentModes[strtolower($itemDetails['payment_mode'])].' '.$itemDetails['check_no'].'</td>
							<td class="'.$class_r.'">'.currency_symbol(true).number_format($itemDetails['balance'], 2, '.', '').'&nbsp;</td>
							<td class="'.$class_c.'">'.ucfirst($ord_sts).'</td>
							<td class="'.$class_c.'">'.$oprName.$madeByName.'</td>
							</tr>';
							$html.=$item_row;
							$htmlPDF.=$item_row;
						}
					}
				}
			}	
		}
		
		if($show_report=="summary"){ //FIRST PART OF SUMMARY VIEW
			$sum_html1='<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr style="height:25px;">
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:98px;">Order #</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:98px;">Date</td>
				<td class="reportHeadBG1 alignTop" style="text-align:left; width:150px;">Patient Name - Id</td>
				<td class="reportHeadBG1 alignTop" style="text-align:left; width:150px;">Facility</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:98px;">Total Amt.</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:98px;">Tax</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:98px;">Discount</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:98px;">Pat. Paid</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:98px;">Ins. Resp</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:98px;">Balance</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:98px;">Status</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:98px;">Operators<br>M / C</td>
			</tr>';
			
			$sum_pdf1='<table width="1020px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">';						
			foreach($arrSummary as $date => $dateData){
				foreach($dateData as $ord_id => $itemDetails){
					$tot_paid=0;
					$oprName = $arrUsersTwoChar[$itemDetails['operator']];
					if($arrUsersTwoChar[$itemDetails['madeBy']] != ''){
						$madeByName = ' / '.$arrUsersTwoChar[$itemDetails['madeBy']];
					}else{
						$madeByName = '';	
					}
					$show_all_status=0;
					if(constant('SHOW_ALL_STS_REPORT')=="TRUE" || !defined('SHOW_ALL_STS_REPORT'))
					{
						$show_all_status=1;
					}else{
						if(!$arrTempId[$ord_id])$show_all_status=1;
					}
					
					if(!$arrTempId[$ord_id]){
						$totTotalAmt+=$itemDetails['total_amount']+$itemDetails['total_amount_os'];
						$orderTotalAmt[]+=$itemDetails['total_amount']+$itemDetails['total_amount_os'];
						//$totPatPaid+=array_sum($itemDetails['pat_paid']);
						//$totInsPaid+=array_sum($itemDetails['ins_paid']);
						$totBalance+=array_sum($itemDetails['balance'])-$itemDetails['overall_discount'];
						$totTax+=$itemDetails['total_tax'];

						$overAllBalance[]=array_sum($itemDetails['balance'])-$itemDetails['overall_discount'];
						$overAllInsurance[]=array_sum($itemDetails['ins_paid']);
					}

					//PAYMENT METHOD BREAKDOWN
					//$method=ucfirst($itemDetails['payment_mode']);
					//$arrPaymentBreakdown[$method]+=$tot_paid;

					$arrTemp[$ord_det_id]=$ord_det_id;
					$arrTempId[$ord_id]=$ord_id;
					//STATUS
					$sts='';
					foreach($itemDetails['order_status'] as $key=>$finalSts)
					{
						$finalSts=strtolower($finalSts);
						if($finalSts=='pending')$sts='Pending';
						else if($finalSts=='ordered')$sts='Ordered';
						else if($finalSts=='received')$sts='Received';
						else if($finalSts=='notified')$sts='Notified';
						else if($finalSts=='dispensed')$sts='Dispensed';
						break;
					}
						
if($show_all_status==1){
	$row_discount=$item_discount=0;
	$item_discount=array_sum($itemDetails['discount']);
	$row_discount=$itemDetails['overall_discount']+$item_discount;
	$totDiscount+=$row_discount;
	$sum_html1.='<tr style="height:20px;">
	<td class="'.$class_c.'">'.$itemDetails['ord_id'].' </td>
	<td class="'.$class_c.'">'.$itemDetails['date'].'</td>
	<td class="'.$class_l.'">'.$itemDetails['patient_name'].'</td>
	<td class="'.$class_l.'">'.$arrfacility[$itemDetails['loc_id']].'</td>
	<td class="'.$class_r.'">'.currency_symbol(true).number_format(($itemDetails['total_amount']+$itemDetails['total_amount_os']), 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'">'.currency_symbol(true).number_format($itemDetails['total_tax'], 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'">'.currency_symbol(true).number_format($row_discount, 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($itemDetails['pat_paid']), 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($itemDetails['ins_paid']), 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($itemDetails['balance'])-$itemDetails['overall_discount'] , 2, '.', '').'&nbsp;</td>
	<td class="'.$class_c.'">'.$sts.'</td>
	<td class="'.$class_c.'">'.$oprName.$madeByName.'</td>
	</tr>';

	$sum_pdf1.='<tr style="height:20px;">
	<td class="'.$class_c.'" style="width:60px">'.$itemDetails['ord_id'].'</td>
	<td class="'.$class_c.'" style="width:80px">'.$itemDetails['date'].'</td>
	<td class="'.$class_l.'" style="width:150px">'.$itemDetails['patient_name'].'</td>
	<td class="'.$class_l.'" style="width:150px">'.$arrfacility[$itemDetails['loc_id']].'</td>
	<td class="'.$class_r.'" style="width:63px">'.currency_symbol(true).number_format(($itemDetails['total_amount']+$itemDetails['total_amount_os']), 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'" style="width:63px">'.currency_symbol(true).number_format($itemDetails['total_tax'], 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'" style="width:64px">'.currency_symbol(true).number_format($row_discount, 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'" style="width:63px">'.currency_symbol(true).number_format(array_sum($itemDetails['pat_paid']), 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'" style="width:63px">'.currency_symbol(true).number_format(array_sum($itemDetails['ins_paid']), 2, '.', '').'&nbsp;</td>
	<td class="'.$class_r.'" style="width:63px">'.currency_symbol(true).number_format(array_sum($itemDetails['balance'])- $itemDetails['overall_discount'], 2, '.', '').'&nbsp;</td>
	<td class="'.$class_c.'" style="width:100px">'.$sts.'</td>
	<td class="'.$class_c.'" style="width:100px">'.$oprName.$madeByName.'</td>
	</tr>';
}

				}
			}

			$total_row='<tr style="height:20px;">
			<td class="whiteBG rptText13b alignRight" colspan="4">Total:</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($orderTotalAmt), 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTax, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDiscount, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignCenter">&nbsp;</td>
			<td class="whiteBG rptText13b alignCenter">&nbsp;</td>
			</tr>
			</table>';
			$sum_html1.=$total_row;
			$sum_pdf1.=$total_row;		
		}
		
		$tax_total = array('total'=>array(), 'paid'=>array(), 'resp'=>array());
		//SUMMARY
		//echo "<pre>";print_r($arrMainSummaryDemo);
		foreach($summaryLoopArray as $ord_sts){
			if(sizeof($arrMainSummaryDemo[$ord_sts])>0){
				$stsTotQty= $stsTotPrice= $stsTotDisc= $stsTotTotalAmt=$stsTotPatPaid=$stsTotInsPaid=$stsTotTotPaid=$stsTotBalance=0;
				if($ord_sts=='pending'){
					$order_title='New Orders';
				}else{
					$order_title=ucfirst($ord_sts).' Orders';
				}
				$html_sum.='<tr><td class="reportTitle" colspan="8">'.$order_title.'</td></tr>';
				$htmlPDF_sum.='<tr><td class="reportTitle" colspan="8">'.$order_title.'</td></tr>';
				//echo'<pre>';print_r($arrMainSummaryDemo[$ord_sts]);
				foreach($arrMainSummaryDemo[$ord_sts] as $item_type_id => $itemDetails){
					
					$itemPrice = array();
					$itemTotalAmount = array();
					$itemPatPaid = array();
					$itemInsPaid = array();
					$itemTotPaid = array();
					$itemBalance = array();
					$itemDiscountTotal = array();
					$itemQty = array();
					$itemDiscount = array();
					
					$taxTotal 	= array();
					$taxPaid	= array();
					$taxPending	= array();

					if(!empty($itemDetails))
					{
						foreach ($itemDetails as $key => $value) {			
							$finalQty=0;
							if($value['module_type_id'] == 3)
							{
								$item_total=0;
								if($value['qty']>0)$item_total+=$value['total_amount_os'];
								if($value['qty_right'])$item_total+=$value['total_amount'];
								if($value['cl_disinfect_qty'])$item_total+=$value['cl_disinfect_total_amount'];
								
							}else{
								$item_total=$value['total_amount']+$value['total_amount_os']+$value['cl_disinfect_total_amount'];
							}
							$itemTotal[]=$item_total;
								
							if($value['module_type_id'] == 3)$finalQty = $value['qty']+$value['qty_right']+$value['cl_disinfect_qty'];
							else $finalQty = $value['qty'];
							$itemQty[]=$finalQty;
							
							$item_price=$value['price']+$value['price_os']+$value['cl_disinfect_price'];
							$itemPrice[] = $item_price;
							
							//$itemTotalAmount[] = $finalQty*$item_price;
							$itemTotalAmount[] = $item_total;
							$itemPatPaid[] = $value['pt_paid'];
							$itemInsPaid[] = $value['ins_amount'];
							$itemTotPaid[] = $value['tot_paid'];
							$itemPatResp[] = $value['pt_resp'];	
							
							if(!$arrOrdTempSum[$value['order_id']]){
								$taxTotal[] 	= $value['tax_payable'];
								$taxPaid[]		= $value['tax_pt_paid'];
								$taxPending[]	= $value['tax_pt_resp'];
								
								$disc=$value['overall_discount'];
								if($disc=="" || $disc==0.00){
									$disc = 0;
								}
								$discoutableAmt=$value['total_price']-$value['ins_amount'];
								$overall_discount = cal_discount($discoutableAmt,$disc);
								$itemDiscount[]=$overall_discount;
								//$itemwisedisount[$value['order_id']]+=$overall_discount;// being used to debug purpose only
								$arrOrdTempSum[$value['order_id']]=$value['order_id'];
							}
							
							if(!$arrOrdTempSumDet[$value['order_detail_id']]){
								
								$dicount=$value['discount']+$value['cl_line_discount'];
								if($value['module_type_id']==3){$dicount+=$value['discount_os']+$value['cl_disinfect_discount'];}
								$itemDiscount[]=$dicount;
								//$itemwisedisount[$value['order_id']]+=$dicount;// being used to debug purpose only
								$arrOrdTempSumDet[$value['order_detail_id']]=$value['order_detail_id'];
							}
							
						}
						
					}
					
					$summaryOrderQty[]=array_sum($itemQty);
					$summaryOrderTotalAmount[]=array_sum($itemTotalAmount);
					//$summaryOrderPrice[]=array_sum($itemPrice);
					$itemPriceByDiv=number_format(array_sum($itemTotalAmount)/array_sum($itemQty), 2, '.', '');
					$summaryOrderPrice[]=$itemPriceByDiv;
					//$summaryOrderTotalAmount[]=array_sum($itemQty)*array_sum($itemPrice);
					$summaryOrderPatPaid[]=array_sum($itemPatPaid);
					$summaryOrderInsPaid[]=array_sum($itemInsPaid);
					$summaryOrderDiscount[]=array_sum($itemDiscount);
					$summaryOrderTotPaid[] = array_sum($itemPatPaid) + array_sum($itemInsPaid);
					$summaryOrderPatResp[] = array_sum($itemPatResp);
					$summaryOrderTaxTotal[] = array_sum($taxTotal);
					$summaryOrderTaxPaid[] = array_sum($taxPaid);
					$summaryOrderTaxPending[] = array_sum($taxPending);
					$summaryOrderTotBal[] = array_sum($itemTotalAmount) - (array_sum($itemPatPaid) + array_sum($itemInsPaid));

					//$summaryOrderDiscount[]=$itemDiscountOverallTotal;
					$stsTotDisc+=$itemDetails['discount'];
					$stsTotTotalAmt+=($itemDetails['total_amount']+$itemDetails['total_amount_os']);
					$stsTotPatPaid+=$itemDetails['pat_paid'];
					$stsTotInsPaid+=$itemDetails['ins_paid'];
					$stsTotTotPaid+=$itemDetails['tot_paid'];
					$stsTotBalance+=$itemDetails['balance'];

					//<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($itemDiscount), 2, '.', '').'&nbsp;</td>
					$item_name =ucfirst($arrTypes[$item_type_id]);
					
					$item_sum='<tr style="height:20px;">
					<td class="'.$class_l.'">'.$item_name.'</td>
					<td class="'.$class_r.'">'.array_sum($itemQty).'&nbsp;</td>
					<td class="'.$class_r.'">'.currency_symbol(true).$itemPriceByDiv.'&nbsp;</td>
					<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($itemTotalAmount), 2, '.', '').'&nbsp;</td>
					<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($itemPatPaid), 2, '.', '').'&nbsp;</td>
					<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($itemInsPaid), 2, '.', '').'&nbsp;</td>
					<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($itemPatPaid) + array_sum($itemInsPaid) , 2, '.', '').'&nbsp;</td>
					<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($itemTotalAmount) - (array_sum($itemPatPaid) + array_sum($itemInsPaid)), 2, '.', '').'&nbsp;</td>
					</tr>';
					$html_sum.=$item_sum;
					$htmlPDF_sum.=$item_sum;

					$totalPaidIns[] = array_sum($itemInsPaid);
					$totalPatPaid[] = array_sum($itemPatPaid);								
				}
				//Totals
				
				//<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($summaryOrderDiscount), 2, '.', '').'&nbsp;</td>
				//adding tax row manually
				$tax_row='
				<tr style="height:20px;">
				<td class="'.$class_l.'">Tax: </td>
				<td class="'.$class_r.'">&nbsp;</td>
				<td class="'.$class_r.'">&nbsp;</td>
				<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($summaryOrderTaxTotal), 2, '.', '').'&nbsp;</td>
				<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($summaryOrderTaxPaid), 2, '.', '').'&nbsp;</td>
				<td class="'.$class_r.'">&nbsp;</td>
				<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($summaryOrderTaxPaid), 2, '.', '').'&nbsp;</td>
				<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($summaryOrderTaxPending), 2, '.', '').'&nbsp;</td>
				</tr>';
				
				//adding discount row manually
				$discount_row='
				<tr style="height:20px;">
				<td class="'.$class_l.'">Discount: </td>
				<td class="'.$class_r.'">&nbsp;</td>
				<td class="'.$class_r.'">&nbsp;</td>
				<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($summaryOrderDiscount), 2, '.', '').'&nbsp;</td>
				<td class="'.$class_r.'">&nbsp;</td>
				<td class="'.$class_r.'">&nbsp;</td>
				<td class="'.$class_r.'">&nbsp;</td>
				<td class="'.$class_r.'">'.currency_symbol(true).number_format(array_sum($summaryOrderDiscount), 2, '.', '').'&nbsp;</td>
				</tr>';
				
				$totSumQty+=array_sum($summaryOrderQty);
				$totSumPrice+=array_sum($summaryOrderPrice);
				$totSumTotalAmt+=array_sum($summaryOrderTotalAmount)+array_sum($summaryOrderTaxTotal);
				$totSumPatPaid+=array_sum($summaryOrderPatPaid)+array_sum($summaryOrderTaxPaid);
				$totSumInsPaid+=array_sum($summaryOrderInsPaid);
				$totSumBalance+=(array_sum($summaryOrderTotBal)+array_sum($summaryOrderTaxPending))-array_sum($summaryOrderDiscount);
					
				$total_row=$tax_row.$discount_row.'
				<tr style="height:20px;">
				<td class="whiteBG rptText13b alignRight">Total: </td>
				<td class="whiteBG rptText13b alignRight">'.array_sum($summaryOrderQty).'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).array_sum($summaryOrderPrice).'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($summaryOrderTotalAmount)+array_sum($summaryOrderTaxTotal), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($summaryOrderPatPaid)+array_sum($summaryOrderTaxPaid), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($summaryOrderInsPaid), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format(array_sum($summaryOrderTotPaid)+array_sum($summaryOrderTaxPaid), 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format((array_sum($summaryOrderTotBal)+array_sum($summaryOrderTaxPending))-array_sum($summaryOrderDiscount), 2, '.', '').'&nbsp;</td>
				</tr>';
				
				$html_sum.=$total_row;
				$htmlPDF_sum.=$total_row;

				unset($summaryOrderQty, $summaryOrderPrice, $summaryOrderTotalAmount, $summaryOrderPatPaid, $summaryOrderInsPaid, $summaryOrderTotPaid, $summaryOrderTotBal, $summaryOrderDiscount, $summaryOrderTaxTotal, $summaryOrderTaxPaid, $summaryOrderTaxPending);
				
			}
		}

		//echo"<pre>";print_r($itemwisedisount);
		//Final html
		if($show_report=="summary" || $show_report=="detail")
		{
			//PAYMENT BREAKDOWN
			$htmlPaymentBreakdown='';
			if(sizeof($arrPaymentBreakdown)>0){
				$htmlPaymentBreakdown=
				'<tr style="height:25px;">
					<td class="whiteBG rptText13b alignRight" colspan="8">&nbsp;</td>
				</tr>
				<tr style="height:25px;">
					<td class="whiteBG rptText13b alignRight" colspan="5">&nbsp;</td>
					<td class="whiteBG rptText13b alignRight" style="text-align:center;" colspan="2">Payment Breakdown</td>
					<td class="whiteBG rptText13b alignRight">&nbsp;</td>
				</tr>';
				foreach($arrPaymentBreakdown as $method =>$paidAmt){
					if($paidAmt>0){
						$totMethods+=$paidAmt;
						$htmlPaymentBreakdown.=
						'<tr style="height:25px;">
							<td class="'.$class_r.'" colspan="5">&nbsp;</td>
							<td class="'.$class_r.'" style="text-align:right;">'.$method.'&nbsp;</td>
							<td class="'.$class_r.'" style="text-align:right;">'.currency_symbol(true).number_format($paidAmt, 2, '.', '').'&nbsp;</td>
							<td class="whiteBG rptText13b alignRight">&nbsp;</td>
						</tr>';
					}
				}
				$htmlPaymentBreakdown.=
				'<tr style="height:25px">
					<td class="whiteBG rptText13b alignRight" colspan="6">Total Payments : </td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totMethods, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b">&nbsp;</td>
				</tr>';				
			}//--------------

			$reportHtml_sum=$sum_html1;
			$reportHtml_sum.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">';
			if($show_report=="detail"){
				$reportHtml_sum.='
				<tr style="height:25px"><td colspan="9" class="whiteBG pt2 pb2">&nbsp;</td></tr><tr>
				<tr><td colspan="9" class="reportHeadBG1">Summary View</td></tr><tr>
				';
			}

					//<td class="reportHeadBG1" style="text-align:center; width:150px;">Discount</td>
			$reportHtml_sum.='
				<tr style="height:25px;">
					<td class="reportHeadBG1" style="text-align:center; width:200px;">Product Type</td>
					<td class="reportHeadBG1" style="text-align:center; width:150px;">Quantity</td>
					<td class="reportHeadBG1" style="text-align:center; width:150px;">Ave. Price</td>
					<td class="reportHeadBG1" style="text-align:center; width:150px;">Total Amount</td>
					<td class="reportHeadBG1" style="text-align:center; width:150px;">Pat. Paid</td>
					<td class="reportHeadBG1" style="text-align:center; width:150px;">Ins. Resp</td>
					<td class="reportHeadBG1" style="text-align:center; width:150px;">Total Paid</td>
					<td class="reportHeadBG1" style="text-align:center; width:150px;">Balance</td>
				</tr>'.$html_sum.'
				<tr>
				<td colspan="8" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:25px">
					<td class="whiteBG rptText13b alignRight">Grand Total: </td>
					<td class="whiteBG rptText13b alignRight">'.$totSumQty.'</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumPrice, 2, '.', '').'</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumTotalAmt, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumPatPaid, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumInsPaid, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumPatPaid + $totSumInsPaid, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumBalance, 2, '.', '').'&nbsp;</td>
				</tr>
				'.$htmlPaymentBreakdown.'
			</table>';
						
			$reportHtmlPDF_sum.='
			<table style="width:1030px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">';
			if($show_report=="detail"){
				$reportHtmlPDF_sum.='<tr><td colspan="8" class="reportHeadBG1">Summary View</td></tr>';
			}
			$reportHtmlPDF_sum.='
			<tr style="height:0px;">
				<td style="width:300px;"></td>
				<td style="width:70px;"></td>			
				<td style="width:110px;"></td>
				<td style="width:110px;"></td>
				<td style="width:110px;"></td>
				<td style="width:110px;"></td>
				<td style="width:110px;"></td>	
				<td style="width:110px;"></td>
			</tr>
			'.$htmlPDF_sum.'
			<tr>
			<td colspan="8" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
			</tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13b alignRight">Grand Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totSumQty.'</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumPrice, 2, '.', '').'</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumTotalAmt, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumPatPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumPatPaid+$totSumInsPaid, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumBalance, 2, '.', '').'&nbsp;</td>
			</tr>	
			'.$htmlPaymentBreakdown.'	
			</table>';
		}
		
		if($show_report=="detail")
		{
			//PAYMENT BREAKDOWN
/*			$htmlPaymentBreakdown='';
			if(sizeof($arrPaymentBreakdown)>0){
				$htmlPaymentBreakdown=
				'<tr style="height:25px;">
					<td class="whiteBG rptText13b alignRight" colspan="16">&nbsp;</td>
				</tr>
				<tr style="height:25px;">
					<td class="whiteBG rptText13b alignRight" colspan="9">&nbsp;</td>
					<td class="whiteBG rptText13b alignRight" style="text-align:center;" colspan="3">Payment Breakdown</td>
					<td class="whiteBG rptText13b alignRight" colspan="4">&nbsp;</td>
				</tr>';
				foreach($arrPaymentBreakdown as $method =>$paidAmt){
					$totMethods+=$paidAmt;
					$htmlPaymentBreakdown.=
					'<tr style="height:25px;">
						<td class="'.$class_r.'" colspan="9">&nbsp;</td>
						<td class="'.$class_r.'" style="text-align:right;" colspan="2">'.$method.'&nbsp;</td>
						<td class="'.$class_r.'" style="text-align:right;">'.currency_symbol(true).number_format($paidAmt, 2, '.', '').'&nbsp;</td>
						<td class="whiteBG rptText13b alignRight" colspan="4">&nbsp;</td>
					</tr>';
				}
				$htmlPaymentBreakdown.=
				'<tr style="height:25px">
					<td class="whiteBG rptText13b alignRight" colspan="11">Total Payments : </td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totMethods, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b" colspan="4">&nbsp;</td>
				</tr>';				
			}*///--------------

			$grandTotalAmtFilterd = '';
			if(!empty($grandTotalAmt))
			{
				foreach ($grandTotalAmt as $key => $value) {
					$grandTotalAmtFilterd[] = array_sum(array_unique($value));
				}
			}
			
			$reportHtml.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr style="height:25px;">
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:60px;">Order #</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:65px;">Order Date</td>
					<td class="reportHeadBG1 alignTop" style="text-align:left; width:145px;">Patient Name - Id</td>
					<td class="reportHeadBG1 alignTop" style="text-align:left; width:70px;">Facility</td>
					<td class="reportHeadBG1 alignTop" style="text-align:left; width:120px;">Upc Code - Item Name</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:50px;">Qty</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:60px;">Price</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:60px;">Disc.</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:85px;">Total Amt.</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:85px;">Pat. Paid</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:75px;">Ins. Resp</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:85px;">Tot. Paid</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:70px;">Method</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:75px;">Balance</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:60px;">Status</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:60px;">Operators<br>M / C</td>
				</tr>'.$html.'
				<tr>
				<td colspan="16" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:25px">
					<td class="whiteBG rptText13b alignRight" colspan="5">Grand Total : </td>
					<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPtPaid, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG">&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG" colspan="2"></td>
				</tr>
			</table>'
			.$reportHtml_sum;		
	
			$reportHtmlPDF='
			<table style="border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr>
				<td style="width:40px;"></td>
				<td style="width:75px;"></td>			
				<td style="width:105px;"></td>
				<td style="width:80px;"></td>
				<td style="width:90px;"></td>
				<td style="width:30px;"></td>
				<td style="width:65px;"></td>
				<td style="width:50px;"></td>
				<td style="width:65px;"></td>
				<td style="width:55px;"></td>
				<td style="width:55px;"></td>
				<td style="width:65px;"></td>
				<td style="width:45px;"></td>
				<td style="width:65px;"></td>
				<td style="width:60px;"></td>
				<td style="width:48px;"></td>
			</tr>
			'.$htmlPDF.'
			<tr>
				<td colspan="16" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:25px">
					<td class="'.$class_r.'" colspan="5">Grand Total : </td>
					<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPrice, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totDisc, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totTotalAmt, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPatPaid, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totInsPaid, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totPaid, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG">&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totBalance, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG" colspan="2"></td>
				</tr>
			</table>';
		}
	}
	
	$css = '
	<style type="text/css">
	.reportHeadBG{ font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; background-color:#D9EDF8;}
	.reportHeadBG1{ font-family: Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; background-color:#67B9E8; color:#FFF;}
	.reportTitle { font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; background-color:#7B7B7B; color:#FFF }
	.rptText13 { font-family: Arial, Helvetica, sans-serif; font-size:11px; }
	.rptText13b { font-family: Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; }
	.rptText12b { font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; }		
	.whiteBG{ background:#fff; } 
	.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
	.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}		
	</style>';		

	//SELECTION TO SHOW
	if(empty($_POST['operators'])===false){ $arrSel=explode(',', $_POST['operators']); }
	if(empty($_POST['physician'])===false){ $arrSel=explode(',', $_POST['physician']); }
	if(empty($_POST['faclity'])===false){ $arrfacly=explode(',', $_POST['faclity']); }
	if(empty($_POST['product_type'])===false){ $arrproducts=explode(',', $_POST['product_type']); }
	if(empty($_POST['order_status'])===false){ $arrstatus=explode(',', $_POST['order_status']); }
	
	$selOpr='All';
	$selfacility='All';
	$selprotype='All';
	$selstatus='All';
	$selOpr=(count($arrSel)>1)? 'Multi' : ((count($arrSel)=='1')? ucfirst($arrUsers[$showOpr]): $selOpr);
	$selfacility=(count($arrfacly)>1)? 'Multi' :((count($arrfacly)=='1')?ucfirst($arrfacility[$_POST['faclity']]): $selfacility);
	$selprotype=(count($arrproducts)>1)? 'Multi' : ((count($arrproducts)=='1')? ucfirst($arrTypes[$_POST['product_type']]): $selprotype);
	$selstatus=(count($arrstatus)>1)? 'Multi' : ((count($arrstatus)=='1')? ucfirst($_POST['order_status']): $selstatus);
	$selfacility=(strlen($selfacility)>28)?substr($selfacility,0,25).'...':$selfacility;
	if($show_report=="summary"){
		$reportHtml=$reportHtml_sum;
		//$reportHtmlPDF=$reportHtmlPDF_sum;
	}
	
	//FINAL HTML
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" >&nbsp;Day Order Report</td>
		<td style="text-align:left;" class="reportHeadBG" colspan="2" >&nbsp;Report for Date : '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
		<td style="text-align:left;" class="reportHeadBG" colspan="2">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
	</tr>
	<tr style="height:20px;">
		<td class="reportHeadBG" width="220">&nbsp;Operator : '.$selOpr.'</td>
		<td class="reportHeadBG" width="220">&nbsp;Facility : '.$selfacility.'</td>
		<td class="reportHeadBG" width="220">&nbsp;Product Type : '.$selprotype.'</td>
		<td class="reportHeadBG" width="220">&nbsp;Status : '.$selstatus.'</td>
		<td class="reportHeadBG" width="220">&nbsp;Report Type : '.ucfirst($_POST['show_report']).'</td>		
	</tr>
	</table>
	'.$reportHtml;


	//FINAL PDF
	if($show_report=="detail" && count($arrMainDetail)>0)
	{
		$mm = 15;
		$finalReportHtmlPDF='
			<page backtop="'.$mm.'mm" backbottom="5mm">
			<page_footer>
					<table>
						<tr>
							<td style="text-align: center;	width: 1030px">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
			</page_footer>
			<page_header>		
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
			<tr style="height:20px;">
				<td style="text-align:left;" class="reportHeadBG">&nbsp;Day Order Report</td>
				<td style="text-align:left;" class="reportHeadBG" colspan="2">&nbsp;Report for Date : '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
				<td style="text-align:left;" class="reportHeadBG" colspan="2">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
			</tr>
			<tr style="height:20px;">
				<td class="reportHeadBG" width="210">&nbsp;Operator : '.$selOpr.'</td>
				<td class="reportHeadBG" width="210">&nbsp;Facility : '.$selfacility.'</td>
				<td class="reportHeadBG" width="210">&nbsp;Product Type : '.$selprotype.'</td>
				<td class="reportHeadBG" width="210">&nbsp;Status : '.$selstatus.'</td>
				<td class="reportHeadBG" width="210">&nbsp;Report Type : '.ucfirst($_POST['show_report']).'</td>
			</tr>
			</table>
			<table width="1030px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
			<tr>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Order #</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:75px;">Order Date</td>
				<td class="reportHeadBG1 alignTop" style="text-align:left; width:105px;">Patient Name - Id</td>
				<td class="reportHeadBG1 alignTop" style="text-align:left; width:80px;">Facility</td>
				<td class="reportHeadBG1 alignTop" style="text-align:left; width:90px;">Upc Code - Item Name</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:30px;">Qty</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:65px;">Price</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:50px;">Disc.</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:65px;">Total Amt.</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:55px;">Pat. Paid</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:55px;">Ins. Resp</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:65px;">Total Paid</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:45px;">Method</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:65px;">Balance</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:60px;">Status</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:48px;">Operator<br>M / C</td>
			</tr>
		</table></page_header>'.
			$reportHtmlPDF.'
		</page>';
	}

	if(count($arrMainSummary)>0)
	{
		//FINAL PDF
		$mm = 12;
		
		$pageHeader='
		<page_footer>
			<table>
				<tr>
					<td style="text-align: center;	width: 700px">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>		
		<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG">&nbsp;Day Order Report</td>
			<td style="text-align:left;" class="reportHeadBG" colspan="2">&nbsp;Report for Date : '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
			<td style="text-align:left;" class="reportHeadBG" colspan="2">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG" width="210">&nbsp;Operator : '.$selOpr.'</td>
			<td class="reportHeadBG" width="210">&nbsp;Facility : '.$selfacility.'</td>
			<td class="reportHeadBG" width="210">&nbsp;Product Type : '.$selprotype.'</td>
			<td class="reportHeadBG" width="210">&nbsp;Status : '.$selstatus.'</td>
			<td class="reportHeadBG" width="210">&nbsp;Report Type : '.ucfirst($_POST['show_report']).'</td>
		</tr>
		</table>';
		if($sum_pdf1){
		$finalReportHtmlPDF.='
			<page backtop="'.$mm.'mm" backbottom="5mm">
			'.$pageHeader.'
			<table width="1020px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
			<tr style="height:25px;">
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:60px;">Order #</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Order Date</td>
				<td class="reportHeadBG1 alignTop" style="text-align:left; width:150px;">Patient Name - Id</td>
				<td class="reportHeadBG1 alignTop" style="text-align:left; width:150px;">Facility</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:63px;">Total Amt.</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:63px;">Tax</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:64px;">Discount</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:63px;">Pat. Paid</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:63px;">Ins. Paid</td>
				<td class="reportHeadBG1 alignTop" style="text-align:right; width:63px;">Balance</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Status</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Operators M/C</td>
			</tr>
			</table></page_header>
				'.$sum_pdf1.'
			</page>';
		}
			$finalReportHtmlPDF.='<page backtop="'.$mm.'mm" backbottom="5mm">
			'.$pageHeader.'
			<table width="1000px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">';
			$finalReportHtmlPDF.='
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:300px;">Product Type</td>
				<td class="reportHeadBG1" style="text-align:center; width:70px;">Quantity</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Ave. Price</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Total Amount</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Pat. Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Ins. Resp</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Total Paid</td>
				<td class="reportHeadBG1" style="text-align:center; width:110px;">Balance</td>
			</tr>';
		$finalReportHtmlPDF.='</table></page_header>'.
			$reportHtmlPDF_sum.'
		</page>';
	}
  $pdfText = $css.$finalReportHtmlPDF;
  file_put_contents('../../library/new_html2pdf/day_order_report_result.html',$pdfText);
}
?>
<html>
<head>
<title></title>
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
<form name="searchFormResult" action="day_order_report_result.php" method="post">
<input type="hidden" name="operators" id="operators" value="" />
<input type="hidden" name="physician" id="physician" value="" />
<input type="hidden" name="faclity" id="faclity" value="" />
<input type="hidden" name="product_type" id="product_type" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="show_report" id="show_report" value="" />
<input type="hidden" name="order_status" id="order_status" value="" />
<input type="hidden" name="iportal_orders" id="iportal_orders" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=l&file_name=../../library/new_html2pdf/day_order_report_result';
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
	if(numr>0 || numr2>0){
		mainBtnArr[2] = new Array("generate_day_report_csv_btn","Export","top.main_iframe.reports_iframe.printcsv()");
	}
	top.btn_show("admin",mainBtnArr);	
	top.main_iframe.loading('none');
});
</script>

</body>
</html>