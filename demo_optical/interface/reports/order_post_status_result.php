<?php
/*
File: day_order_report_result.php
Coded in PHP7
Purpose: Day Order Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");


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
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	$show_report=$_POST['show_report'];
	

	/*$mainQry="Select ord_sts.order_id, ord.order_chld_id, ord_sts.order_detail_id, ord_sts.order_qty, ord_sts.order_date, DATE_FORMAT(ord_sts.order_date, '%m-%d-%y') as 'order_date_disp',
	ord_sts.operator_id, ord_sts.item_id, ord_sts.order_status,
	ord.order_id, ord.id, ord.price, ord.loc_id, ord.qty, ord.qty_right,
	ord.discount, ord.total_amount, ord.patient_id, ord.module_type_id, ord.upc_code,
	ord.pt_paid, ord.ins_amount, ord.pt_resp,
	in_order.payment_mode, in_order.checkNo, 
	ord.item_name, DATE_FORMAT(ord.entered_date, '%m-%d-%Y') as 'enteredDate', patient_data.fname, patient_data.lname 
	FROM in_order join in_order_details ord ON ord.id= ord_sts.order_detail_id 
	JOIN in_order ON in_order.id= ord.order_id 
	LEFT JOIN patient_data ON patient_data.id = ord.patient_id 
	WHERE ord.del_status='0' AND (ord_sts.order_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";*/
	
	$mainQry="Select ord.entered_date,order_detail_id,ord.id,ord.order_chl_id,ord_det.item_name,DATE_FORMAT(ord.entered_date, '%m-%d-%Y') as 'order_date_disp',ord_det.order_chld_id,ord_det.order_chld_id_os,ord_det.module_type_id,ord_det.qty,ord_det.qty_right,ord_det.loc_id,
	ord_det.price,ord_det.total_amount,ord.patient_id,ord_det.upc_code,patient_data.fname, patient_data.lname,ord.del_status
	FROM in_order as ord 
	join  
	(SELECT MAX(id) order_detail_id, order_id,upc_code,del_status as del_status_det,item_name,order_chld_id,order_chld_id_os,module_type_id,qty,qty_right,loc_id,price,total_amount 
		FROM in_order_details as ord_get 
		GROUP BY id
		ORDER BY id DESC
    )
	as ord_det ON ord.id = ord_det.order_id 
	LEFT JOIN patient_data ON patient_data.id = ord.patient_id 	
	WHERE (ord.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."')
	AND module_type_id !=2
	";

	if(empty($_POST['order_status'])==false){
		if($_POST['order_status']=="posted"){
			$mainQry.=" AND ord.order_chl_id>'0'";
		}else{
			$mainQry.=" AND ord.order_chl_id='0'";
		}
	}
	if(empty($_POST['product_type'])==false){
		$mainQry.=' AND ord_det.module_type_id IN('.$_POST['product_type'].')';
	}
	if(empty($_POST['operators'])==false){
		$mainQry.=' AND ord.operator_id IN('.$_POST['operators'].')';
	}
	if(empty($_POST['faclity'])==false){
		$mainQry.=' AND ord_det.loc_id IN('.$_POST['faclity'].')';
	}
	$mainQry.=' GROUP BY ord.id';
	$mainQry.=' ORDER BY ord.id DESC';

	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	$totSumQty = 0;
	$cnt = 0;
	while($mainRes=imw_fetch_assoc($mainRs)){
		$ord_id= $mainRes['id'];
		$ord_det_id= $mainRes['order_detail_id'];
		$order_chld_id = $mainRes['order_chld_id'];
		$order_chld_id_os = $mainRes['order_chld_id_os'];
		$order_chl_id = $mainRes['order_chl_id'];

		$date=$mainRes['order_date_disp'];
		$item_type=$mainRes['module_type_id'];
		$qty=$mainRes['qty']+$mainRes['qty_right'];	

		if($ord_id==$old_ord_id){
			$arrMainDetail[$ord_det_id]['ord_id']='';
			$arrMainDetail[$ord_det_id]['date']='';
			$arrMainDetail[$ord_det_id]['patient_name']='';
			$arrMainDetail[$ord_det_id]['loc_id']='';
		}else{
			$arrMainDetail[$ord_det_id]['ord_id']=$ord_id;
			$arrMainDetail[$ord_det_id]['date']=$mainRes['order_date_disp'];
			$arrMainDetail[$ord_det_id]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
			$arrMainDetail[$ord_det_id]['loc_id']=$mainRes['loc_id'];
		}
		
		
		$arrMainDetail[$ord_det_id]['item']=$mainRes['upc_code'].' - '.$mainRes['item_name'];
		$arrMainDetail[$ord_det_id]['qty']=$qty;
		$arrMainDetail[$ord_det_id]['total_amount']=$mainRes['total_amount'];
		$arrMainDetail[$ord_det_id]['ord_sts']=$ord_sts;
		$arrMainDetail[$ord_det_id]['del_status']=$mainRes['del_status_det'];
			
		// $arrMainSummary[$ord_id]['ord_id']=$ord_id;
		// $arrMainSummary[$ord_id]['date']=$mainRes['order_date_disp'];
		// $arrMainSummary[$ord_id]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
		// $arrMainSummary[$ord_id]['loc_id']=$mainRes['loc_id'];
		// $arrMainSummary[$ord_id]['del_status']=$mainRes['del_status'];
		// if($ord_id==$old_ord_id){
		// 	$arrMainSummary[$ord_id]['qty']=$arrMainSummary[$ord_id]['qty']+$qty;
		// 	$arrMainSummary[$ord_id]['total_amount']=$arrMainSummary[$ord_id]['total_amount']+$mainRes['total_amount'];
		// 	if($arrMainSummary[$ord_id]['ord_sts']!=$ord_sts){
		// 		$arrMainSummary[$ord_id]['ord_sts']="Partially Posted";
		// 	}
		// }else{
		// 	$arrMainSummary[$ord_id]['qty']=$qty;
		// 	$arrMainSummary[$ord_id]['total_amount']=$mainRes['total_amount'];
		// 	$arrMainSummary[$ord_id]['ord_sts']=$ord_sts;
		// }

		if($order_chl_id > 0 && ($order_chld_id > 0 || $order_chld_id_os >0))
		{
			$ord_sts= 'posted';
			$arrMainSummary[$ord_id]['ord_id']=$ord_id;
			$arrMainSummary[$ord_id]['date']=$mainRes['order_date_disp'];
			$arrMainSummary[$ord_id]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
			$arrMainSummary[$ord_id]['loc_id']=$mainRes['loc_id'];
			$arrMainSummary[$ord_id]['del_status']=$mainRes['del_status'];
			$arrMainSummary[$ord_id]['qty']=$qty;
			$arrMainSummary[$ord_id]['total_amount']=$mainRes['total_amount'];
			$arrMainSummary[$ord_id]['ord_sts']=$ord_sts;
			$totSumQty+=$qty;
		}
		if($order_chl_id == 0 && $order_chld_id == 0 )
		{
			$ord_sts= 'not posted';
			$arrMainSummary[$ord_id]['ord_id']=$ord_id;
			$arrMainSummary[$ord_id]['date']=$mainRes['order_date_disp'];
			$arrMainSummary[$ord_id]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
			$arrMainSummary[$ord_id]['loc_id']=$mainRes['loc_id'];
			$arrMainSummary[$ord_id]['del_status']=$mainRes['del_status'];
			$arrMainSummary[$ord_id]['qty']=$qty;
			$arrMainSummary[$ord_id]['total_amount']=$mainRes['total_amount'];
			$arrMainSummary[$ord_id]['ord_sts']=$ord_sts;
			$totSumQty+=$qty;			
		}
		if($order_chl_id > 0 && $order_chld_id == 0 &&  $order_chld_id == 0 && $order_chld_id_os == 0 && $_POST['order_status']=="" ) 
		{
			$ord_sts= 'Partially posted';
			$arrMainSummary[$ord_id]['ord_id']=$ord_id;
			$arrMainSummary[$ord_id]['date']=$mainRes['order_date_disp'];
			$arrMainSummary[$ord_id]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
			$arrMainSummary[$ord_id]['loc_id']=$mainRes['loc_id'];
			$arrMainSummary[$ord_id]['del_status']=$mainRes['del_status'];
			$arrMainSummary[$ord_id]['qty']=$qty;
			$arrMainSummary[$ord_id]['total_amount']=$mainRes['total_amount'];
			$arrMainSummary[$ord_id]['ord_sts']=$ord_sts;
			$totSumQty+=$qty;				
		}
		
		
		
		$totSumTotalAmt+=$mainRes['total_amount'];	
		$old_ord_id=$ord_id;
		$cnt++;
		
	}
	
	$html=$htmlPDF=$reportHtml=$reportHtmlPDF='';
	$arrTemp=array();
	$del_row="color: rgb(255, 0, 0); text-decoration: line-through;";
	if(count($arrMainSummary)>0){
		$grandAmount=0; 
		$totPaid=0;
		if($show_report=="detail")
		{	 
			if(sizeof($arrMainSummary)>0){
				foreach($arrMainSummary as $ord_key => $itemDetails){
					$ord_id = $itemDetails['ord_id'];
					$ord_sts = $itemDetails['ord_sts'];
					
					$total_amount=$itemDetails['total_amount'];
					$show_del_row="";
					if($itemDetails['del_status']==1){
						$show_del_row=$del_row;
					}
					$html.='<tr style="height:20px; '.$show_del_row.'">
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['ord_id'].'</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['patient_name'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.$arrfacility[$itemDetails['loc_id']].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['item'].'</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['qty'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($total_amount, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">'.ucfirst($ord_sts).'</td>
					</tr>';
					
					$htmlPDF.='<tr style="height:20px; '.$show_del_row.'">
					<td class="whiteBG rptText13 alignCenter">'.$itemDetails['ord_id'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$itemDetails['patient_name'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">'.$arrfacility[$itemDetails['loc_id']].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['item'].'</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['qty'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($total_amount, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">'.ucfirst($ord_sts).'</td>
					</tr>';	
				}
					
			}
		}else{
			if(count($arrMainSummary)>0){
				foreach($arrMainSummary as $ord_key => $itemDetails){
					$ord_id = $itemDetails['ord_id'];
					$ord_sts = $itemDetails['ord_sts'];
					
					$total_amount=$itemDetails['total_amount'];
					$show_del_row="";
					if($itemDetails['del_status']==1){
						$show_del_row=$del_row;
					}		
					$html_sum.='<tr style="height:20px; '.$show_del_row.'">
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['ord_id'].'</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['patient_name'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.$arrfacility[$itemDetails['loc_id']].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['qty'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($total_amount, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">'.ucfirst($ord_sts).'</td>
					</tr>';
					
					$htmlPDF_sum.='<tr style="height:20px; '.$show_del_row.'">
					<td class="whiteBG rptText13 alignCenter">'.$itemDetails['ord_id'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$itemDetails['patient_name'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">'.$arrfacility[$itemDetails['loc_id']].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['qty'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($total_amount, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">'.ucfirst($ord_sts).'</td>
					</tr>';
				}
					
			}
		}
		
		//SUMMARY
				
		
		//Final html
		if($show_report=="summary")
		{
			//PAYMENT BREAKDOWN
			$htmlPaymentBreakdown='';

			$reportHtml_sum.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">';
			if($show_report=="detail"){
				$reportHtml_sum.='
				<tr style="height:25px"><td colspan="9" class="whiteBG pt2 pb2">&nbsp;</td></tr><tr>
				<tr><td colspan="9" class="reportHeadBG1">Summary View</td></tr><tr>
				';
			}
			$reportHtml_sum.='
				<tr style="height:25px;">
					<td class="reportHeadBG1" style="text-align:center; width:75px;">Order#</td>
					<td class="reportHeadBG1" style="text-align:center; width:325px;">Patient Name-ID</td>
					<td class="reportHeadBG1" style="text-align:center; width:300px;">Facility</td>
					<td class="reportHeadBG1" style="text-align:center; width:75px;">Total Qty</td>
					<td class="reportHeadBG1" style="text-align:center; width:100px;">Total Amount</td>
					<td class="reportHeadBG1" style="text-align:center; width:170px;">Status</td>
				</tr>'.$html_sum.'
				<tr>
				<td colspan="7" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:25px">
					<td class="whiteBG rptText13b alignRight" colspan="3">Grand Total : </td>
					<td class="whiteBG rptText13b alignRight">'.$totSumQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumTotalAmt, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">&nbsp;</td>
				</tr>
				'.$htmlPaymentBreakdown.'
			</table>';
	//Detail View Print 
			$reportHtmlPDF_sum.='
			<table style="width:700px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">';
			if($show_report=="detail"){
				$reportHtmlPDF_sum.='
				<tr style="height:25px"><td colspan="6" class="whiteBG pt2 pb2">&nbsp;</td></tr>
				<tr><td colspan="6" class="reportHeadBG1">Summary View</td></tr>
				';
			}
			$reportHtmlPDF_sum.='
			<tr style="height:25px;">
				<td style="width:75px;"></td>			
				<td style="width:325px;"></td>
				<td style="width:325px;"></td>
				<td style="width:75px;"></td>
				<td style="width:75px;"></td>
				<td style="width:170px;"></td>
			</tr>
			'.$htmlPDF_sum.'
			<tr>
			<td colspan="6" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
			</tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13b alignRight" colspan="3">Grand Total : </td>
					<td class="whiteBG rptText13b alignRight">'.$totSumQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumTotalAmt, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>	
			'.$htmlPaymentBreakdown.'	
			</table>';
		}
		
		if($show_report=="detail")
		{
		$reportHtml.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr style="height:25px;">
					<td class="reportHeadBG1" style="text-align:center; width:70px;">Order#</td>
					<td class="reportHeadBG1" style="text-align:center; width:175px;">Patient Name-ID</td>
					<td class="reportHeadBG1" style="text-align:center; width:175px;">Facility</td>
					<td class="reportHeadBG1" style="text-align:center; width:275px;">Upc - Item Name</td>
					<td class="reportHeadBG1" style="text-align:center; width:75px;">Total Qty</td>
					<td class="reportHeadBG1" style="text-align:center; width:100px;">Total Amount</td>
					<td class="reportHeadBG1" style="text-align:center; width:170px;">Status</td>
				</tr>'.$html.'
				<tr>
				<td colspan="16" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<!-- Detail Grand Total -->
				<tr style="height:25px">
					<td class="whiteBG rptText13b alignRight" colspan="4">Grand Total : </td>
					<td class="whiteBG rptText13b alignRight">'.$totSumQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumTotalAmt, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">&nbsp;</td>
				</tr>
			</table>';		
	
			$reportHtmlPDF.='
			<table style="border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr>
				<td style="width:70px;"></td>
				<td style="width:175px;"></td>			
				<td style="width:175px;"></td>
				<td style="width:275px;"></td>
				<td style="width:75px;"></td>
				<td style="width:100px;"></td>
				<td style="width:170px;"></td>
			</tr>
			'.$htmlPDF.'
			<tr>
				<td colspan="16" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:25px">
					<td class="whiteBG rptText13b alignRight" colspan="4">Grand Total : </td>
					<td class="whiteBG rptText13b alignRight">'.$totSumQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totSumTotalAmt, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">&nbsp;</td>
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
	
	if($show_report=="summary"){
		$reportHtml=$reportHtml_sum;
		//$reportHtmlPDF=$reportHtmlPDF_sum;
	}
	
	//FINAL HTML
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" >&nbsp;Order Post Status Report</td>
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
		$finalReportHtmlPDF.='
			<page backtop="'.$mm.'mm" backbottom="5mm">
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
				<td style="text-align:left;" class="reportHeadBG">&nbsp;Order Post Status Report</td>
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
			<table width="700px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">';
			$finalReportHtmlPDF.='
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:70px;">Order#</td>
				<td class="reportHeadBG1" style="text-align:center; width:175px;">Patient Name-ID</td>
				<td class="reportHeadBG1" style="text-align:center; width:175px;">Facility</td>
				<td class="reportHeadBG1" style="text-align:center; width:275px;">Upc - Item Name</td>
				<td class="reportHeadBG1" style="text-align:center; width:75px;">Total Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:100px;">Total Amount</td>
				<td class="reportHeadBG1" style="text-align:center; width:170px;">Status</td>
			</tr>';
		$finalReportHtmlPDF.='</table></page_header>'.
			$reportHtmlPDF.'
		</page>';
	}

	if(count($arrMainSummary)>0 && $show_report == "summary")
	{
		//FINAL PDF
		$mm = 12;
		$finalReportHtmlPDF.='
			<page backtop="'.$mm.'mm" backbottom="5mm">
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
				<td style="text-align:left;" class="reportHeadBG">&nbsp;Order Post Status Report</td>
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
			<table width="700px" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">';
			$finalReportHtmlPDF.='
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:center; width:75px;">Order#</td>
				<td class="reportHeadBG1" style="text-align:center; width:325px;">Patient Name-ID</td>
				<td class="reportHeadBG1" style="text-align:center; width:325px;">Facility</td>
				<td class="reportHeadBG1" style="text-align:center; width:75px;">Total Qty</td>
				<td class="reportHeadBG1" style="text-align:center; width:75px;">Total Amount</td>
				<td class="reportHeadBG1" style="text-align:center; width:170px;">Status</td>
				
				
			</tr>';
		$finalReportHtmlPDF.='</table></page_header>'.
			$reportHtmlPDF_sum.'
		</page>';
	}
  $pdfText = $css.$finalReportHtmlPDF;
  file_put_contents('../../library/new_html2pdf/order_post_status_result.html',$pdfText);
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
<form name="searchFormResult" action="order_post_status_result.php" method="post">
<input type="hidden" name="operators" id="operators" value="" />
<input type="hidden" name="faclity" id="faclity" value="" />
<input type="hidden" name="product_type" id="product_type" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="show_report" id="show_report" value="" />
<input type="hidden" name="order_status" id="order_status" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=l&file_name=../../library/new_html2pdf/order_post_status_result';
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