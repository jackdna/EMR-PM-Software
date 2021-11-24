<?php
/*
File: cost_of_goods_result.php
Coded in PHP7
Purpose: Cost of Goods Report
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
	

	$mainQry="Select ord_sts.order_id, ord_sts.order_detail_id, ord_sts.order_qty, ord_sts.order_date, DATE_FORMAT(ord_sts.order_date, '%m-%d-%y') as 'order_date_disp',
	ord_sts.operator_id, ord_sts.item_id, ord_sts.order_status,
	ord.order_id, ord.id, ord.price, ord.price_os, ord.loc_id, ord.qty, ord.qty_right,
	ord.discount, ord.discount_os, ord.total_amount, ord.total_amount_os, ord.patient_id, ord.module_type_id, ord.upc_code, ord.upc_code_os,
	ord.pt_paid, ord.pt_paid_os,in_order.total_price, ord.ins_amount, ord.ins_amount_os, ord.pt_resp, ord.pt_resp_os, ord.del_status,ord.operator_id as 'madeBy',
	cl_disinfecting.price as 'cl_disinfect_price', cl_disinfecting.total_amount as 'cl_disinfect_total_amount', cl_disinfecting.qty as 'cl_disinfect_qty',
	cl_disinfecting.pt_paid as 'cl_disinfect_pt_paid',cl_disinfecting.pt_resp as 'cl_disinfect_pt_resp', cl_disinfecting.ins_amount as 'cl_disinfect_ins_amount',
	cl_disinfecting.discount as 'cl_disinfect_discount', cl_disinfecting.item_id as 'cl_disinfect_item_id',
	in_order.re_make_id,in_order.re_order_id,in_order.overall_discount,
	in_order.payment_mode, in_order.checkNo, in_order.tax_prac_code, in_order.tax_payable, in_order.tax_pt_paid,
	in_order.tax_pt_resp, ord.item_name, ord.item_name_os, DATE_FORMAT(ord.entered_date, '%m-%d-%Y') as 'enteredDate', patient_data.fname,
	patient_data.lname, in_item.purchase_price, in_item.wholesale_cost, in_item.retail_price
	FROM in_order_detail_status ord_sts 
	JOIN in_order_details ord ON ord.id= ord_sts.order_detail_id 
	JOIN in_order ON in_order.id= ord.order_id 
	LEFT JOIN in_order_cl_detail cl_disinfecting ON cl_disinfecting.order_detail_id = ord.id 
	LEFT JOIN patient_data ON patient_data.id = ord.patient_id
	LEFT JOIN in_item on in_item.id=ord.item_id
	WHERE (ord_sts.order_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";

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
			$mainQry.=" AND ord.del_status='0' AND ord_sts.order_status='".$_POST['order_status']."'";
		}
	}else{
		$mainQry.=" AND ord.del_status='0'";
	}
	if(empty($_POST['product_type'])==false){
		$mainQry.=' AND ord.module_type_id IN('.$_POST['product_type'].')';
	}
	if(empty($_POST['operators'])==false){
		$mainQry.=' AND ord_sts.operator_id IN('.$_POST['operators'].')';
	}
	if(empty($_POST['faclity'])==false){
		$mainQry.=' AND ord.loc_id IN('.$_POST['faclity'].')';
	}
	if(empty($_POST['iportal_orders'])==false){
		$mainQry.=" AND in_order.iportal_cl_order_id>'0'";
	}

	$mainQry.=' ORDER BY ord_sts.order_date DESC, ord_sts.order_id DESC';
	
	$prv_order_id = false;
	$prv_status	  = false;
	$tax_data = array();
	
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_assoc($mainRs)){
		$order_id=$mainRes['order_id'];
		$ord_det_id= $mainRes['order_detail_id'];
	
		if($mainRes['order_status']=='pending'){ //BECAUSE QTY MAY CHANGE LATER THAT IS UPDATING IN DETAIL TABLE BUT NOT IN STATUS TABLE
			$qty=$mainRes['qty'];
			if($mainRes['module_type_id']==3){ //FOR CL
				$qty=$mainRes['qty_right'];
			}
		}else{
			$qty=$mainRes['order_qty'];
		}
		if($item_type!=3 || $mainRes['qty_right']>0){
		$ord_data_arr[$order_id][$ord_det_id]['order_date']=$mainRes['order_date_disp'];
		$ord_data_arr[$order_id][$ord_det_id]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
		$ord_data_arr[$order_id][$ord_det_id]['item']=$mainRes['upc_code'].' - '.$mainRes['item_name'];
		$ord_data_arr[$order_id][$ord_det_id]['qty']=($item_type==3)?$mainRes['qty_right']:$mainRes['qty'];
		$ord_data_arr[$order_id][$ord_det_id]['price']=$mainRes['price'];
		$ord_data_arr[$order_id][$ord_det_id]['total_price']=$mainRes['total_price'];
		$ord_data_arr[$order_id][$ord_det_id]['total_amount']=$mainRes['total_amount'];
		$ord_data_arr[$order_id][$ord_det_id]['ins_adj']=$mainRes['ins_amount'];
		$ord_data_arr[$order_id][$ord_det_id]['discount']=$mainRes['discount'];
		$ord_data_arr[$order_id][$ord_det_id]['overall_discount']=$mainRes['overall_discount'];
		$ord_data_arr[$order_id][$ord_det_id]['pat_paid']=$mainRes['pt_paid'];
		$ord_data_arr[$order_id][$ord_det_id]['purchase_price']=$mainRes['purchase_price'];
		$ord_data_arr[$order_id][$ord_det_id]['wholesale_cost']=$mainRes['wholesale_cost'];
		$ord_data_arr[$order_id][$ord_det_id]['retail_price']=$mainRes['retail_price'];
		$ord_data_arr[$order_id][$ord_det_id]['module_type_id']=$mainRes['module_type_id'];
		}
		if($mainRes['module_type_id']==3 && $mainRes['qty']>0){ //FOR CL - FOR OS ROW
			$ord_det_id='0'.$ord_det_id;
			
			$ord_data_arr[$order_id][$ord_det_id]['order_date']=$mainRes['order_date_disp'];
			$ord_data_arr[$order_id][$ord_det_id]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
			$ord_data_arr[$order_id][$ord_det_id]['item']=$mainRes['upc_code_os'].' - '.$mainRes['item_name_os'];
			$ord_data_arr[$order_id][$ord_det_id]['qty']=$mainRes['qty'];
			$ord_data_arr[$order_id][$ord_det_id]['price']=$mainRes['price_os'];
			$ord_data_arr[$order_id][$ord_det_id]['total_amount']=$mainRes['total_amount_os'];
			$ord_data_arr[$order_id][$ord_det_id]['ins_adj']=$mainRes['ins_amount_os'];
			$ord_data_arr[$order_id][$ord_det_id]['discount']=$mainRes['discount_os'];
			$ord_data_arr[$order_id][$ord_det_id]['pat_paid']=$mainRes['pt_paid_os'];
			$ord_data_arr[$order_id][$ord_det_id]['balance']=$mainRes['pt_resp_os'];
			$ord_data_arr[$order_id][$ord_det_id]['purchase_price']=$mainRes['purchase_price'];
			$ord_data_arr[$order_id][$ord_det_id]['wholesale_cost']=$mainRes['wholesale_cost'];
			$ord_data_arr[$order_id][$ord_det_id]['retail_price']=$mainRes['retail_price'];
			$ord_data_arr[$order_id][$ord_det_id]['module_type_id']=$mainRes['module_type_id'];

			if($mainRes['cl_disinfect_price']>0){
				$ord_det_id='00'.$ord_det_id;
				
				$ord_data_arr[$order_id][$ord_det_id]['order_date']=$mainRes['order_date_disp'];
				$ord_data_arr[$order_id][$ord_det_id]['patient_name']=$mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
				$ord_data_arr[$order_id][$ord_det_id]['item']=$arr_all_disinfecting[$mainRes['cl_disinfect_item_id']];
				$ord_data_arr[$order_id][$ord_det_id]['qty']=$mainRes['cl_disinfect_qty'];
				$ord_data_arr[$order_id][$ord_det_id]['price']=$mainRes['cl_disinfect_price'];
				$ord_data_arr[$order_id][$ord_det_id]['total_amount']=$mainRes['cl_disinfect_total_amount'];
				$ord_data_arr[$order_id][$ord_det_id]['ins_adj']=$mainRes['cl_disinfect_ins_amount'];
				$ord_data_arr[$order_id][$ord_det_id]['discount']=$mainRes['cl_disinfect_discount'];
				$ord_data_arr[$order_id][$ord_det_id]['pat_paid']=$mainRes['cl_disinfect_pt_paid'];
				$ord_data_arr[$order_id][$ord_det_id]['purchase_price']=$mainRes['purchase_price'];
				$ord_data_arr[$order_id][$ord_det_id]['wholesale_cost']=$mainRes['wholesale_cost'];
				$ord_data_arr[$order_id][$ord_det_id]['retail_price']=$mainRes['retail_price'];
				$ord_data_arr[$order_id][$ord_det_id]['module_type_id']=$mainRes['module_type_id'];
			}
		}
		
		if($mainRes['tax_payable']>0){
			$tax_data[$order_id]['price'] = $mainRes['tax_payable'];
			$tax_data[$order_id]['pat_paid'] = $mainRes['tax_pt_paid'];
			$tax_data[$order_id]['pat_resp'] = $mainRes['tax_pt_resp'];
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

	//SELECTION TO SHOW
	if(empty($_POST['operators'])===false){ $arrSel=explode(',', $_POST['operators']); }
	if(empty($_POST['faclity'])===false){ $arrfacly=explode(',', $_POST['faclity']); }
	if(empty($_POST['product_type'])===false){ $arrproducts=explode(',', $_POST['product_type']); }
	if(empty($_POST['order_status'])===false){ $arrstatus=explode(',', $_POST['order_status']); }
	
	$selOpr=$selfacility=$selprotype=$selstatus='All';
	$selOpr=(count($arrSel)>1)? 'Multi' : ((count($arrSel)=='1')? ucfirst($arrUsers[$showOpr]): $selOpr);
	$selfacility=(count($arrfacly)>1)? 'Multi' :((count($arrfacly)=='1')?ucfirst($arrfacility[$_POST['faclity']]): $selfacility);
	$selprotype=(count($arrproducts)>1)? 'Multi' : ((count($arrproducts)=='1')? ucfirst($arrTypes[$_POST['product_type']]): $selprotype);
	$selstatus=(count($arrstatus)>1)? 'Multi' : ((count($arrstatus)=='1')? ucfirst($_POST['order_status']): $selstatus);

	//echo "<pre>";
	//print_r($ord_data_arr);
	if(count($ord_data_arr)>0){
		foreach($ord_data_arr as $ord_id => $ord_data){
			$tot_ret_arr=$tot_wh_arr=$tot_amt_arr=$tot_pat_paid_arr=$tot_discount_arr=$tot_ins_adj_arr=$frame_cost_arr=$lenses_cost_arr=$cl_cost_arr=
			$supplies_cost_arr=$meds_cost_arr=$access_cost_arr=$module_type_id_arr=array();
			$net_profit=0;
			foreach($ord_data as $ord_det_id => $ord_det_data){
				$disc =0;
				$price=$ord_det_data['price'];
				//$totAmt=$price*$ord_det_data['qty'];
				$totAmt=$ord_det_data['total_amount'];
				$discount = floatval($ord_det_data['discount']);
				$discoutableAmt=$totAmt-$ord_det_data['ins_adj'];
				//calculate overall discount seperately for detail view
				if($ord_det_data['overall_discount'])
				{
					$disc=$ord_det_data['overall_discount'];
					if(!$disc || $disc==0.00){
						$disc = 0;
					}
					$overall_discount[$ord_id] = cal_discount($discoutableAmt,$disc);
				}
				$tot_amt_arr[]=$totAmt;
				$tot_wh_arr[]=$ord_det_data['wholesale_cost'];
				if($show_report=="detail"){$tot_ret_arr[]=$price;}
				else{$tot_ret_arr[0]=$ord_det_data['total_price'];}
				$tot_pat_paid_arr[]=$ord_det_data['pat_paid'];
				$tot_discount_arr[]=$discount;
				if(!$overallDiscAddedFor[$ord_id]){
					$tot_discount_arr[]=$overall_discount[$ord_id];
					$overallDiscAddedFor[$ord_id]=$ord_id;
				}
				
				
				$tot_ins_adj_arr[]=$ord_det_data['ins_adj'];
				$module_type_id_arr[$ord_det_data['module_type_id']]=$ord_det_data['module_type_id'];
				$grand_module_type_id_arr[$ord_det_data['module_type_id']]=$ord_det_data['module_type_id'];
				/*if($ord_det_data['module_type_id']=="1"){
					$frame_cost_arr[]=$ord_det_data['price']*$ord_det_data['qty'];
				}else if($ord_det_data['module_type_id']=="2"){
					$lenses_cost_arr[]=$ord_det_data['price']*$ord_det_data['qty'];
				}else if($ord_det_data['module_type_id']=="3"){
					$cl_cost_arr[]=$ord_det_data['price']*$ord_det_data['qty'];
				}else if($ord_det_data['module_type_id']=="5"){
					$supplies_cost_arr[]=$ord_det_data['price']*$ord_det_data['qty'];
				}else if($ord_det_data['module_type_id']=="6"){
					$meds_cost_arr[]=$ord_det_data['price']*$ord_det_data['qty'];
				}else if($ord_det_data['module_type_id']=="7"){
					$access_cost_arr[]=$ord_det_data['price']*$ord_det_data['qty'];
				}*/
				if($ord_det_data['module_type_id']=="1"){
					$frame_cost_arr[]=$ord_det_data['total_amount'];
				}else if($ord_det_data['module_type_id']=="2"){
					$lenses_cost_arr[]=$ord_det_data['total_amount'];
				}else if($ord_det_data['module_type_id']=="3"){
					$cl_cost_arr[]=$ord_det_data['total_amount'];
				}else if($ord_det_data['module_type_id']=="5"){
					$supplies_cost_arr[]=$ord_det_data['total_amount'];
				}else if($ord_det_data['module_type_id']=="6"){
					$meds_cost_arr[]=$ord_det_data['total_amount'];
				}else if($ord_det_data['module_type_id']=="7"){
					$access_cost_arr[]=$ord_det_data['total_amount'];
				}
				
				if($show_report=="detail"){
					$show_order_id=$ord_id;
					$show_order_date=$ord_det_data['order_date'];
					$show_patient_name=$ord_det_data['patient_name'];
					if($old_ord_id==$ord_id){
						$show_order_id=$show_order_date=$show_patient_name="";
					}
					$sum_html_data.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignCenter" style="width:85px;">'.$show_order_id.'</td>
					<td class="whiteBG rptText13 alignCenter" style="width:100px;">'.$show_order_date.'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:180px;">'.$show_patient_name.'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:200px;">'.$ord_det_data['item'].'</td>
					
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).number_format($ord_det_data['wholesale_cost'],2).'</td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).number_format($price,2).'</td>
					
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).number_format($totAmt,2).'</td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).number_format($ord_det_data['ins_adj'],2).'</td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).number_format($discount,2).'</td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).number_format($ord_det_data['pat_paid'],2).'</td>
					</tr>';
				}
				$old_ord_id=$ord_id;
			}
			
			$tot_amt_arr[]=$tax_data[$ord_id]['price'];
			$tot_pat_paid_arr[]=$tax_data[$ord_id]['pat_paid'];
			
			if($tax_data[$ord_id]['price']>0 && $show_report=="detail"){
				$sum_html_data.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignCenter" style="width:85px;"></td>
					<td class="whiteBG rptText13 alignCenter" style="width:100px;"></td>
					<td class="whiteBG rptText13 alignLeft" style="width:180px;"></td>
					<td class="whiteBG rptText13 alignLeft" style="width:200px;">Sales Tax</td>
					<td class="whiteBG rptText13 alignLeft" style="width:80px;"></td>
					<td class="whiteBG rptText13 alignLeft" style="width:80px;"></td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).number_format($tax_data[$ord_id]['price'],2).'</td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;"></td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;"></td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).number_format($tax_data[$ord_id]['pat_paid'],2).'</td>
					</tr>';
			}
			
			if($overall_discount[$ord_id]>0 && $show_report=="detail"){
				$sum_html_data.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignCenter" style="width:85px;"></td>
					<td class="whiteBG rptText13 alignCenter" style="width:100px;"></td>
					<td class="whiteBG rptText13 alignLeft" style="width:180px;"></td>
					<td class="whiteBG rptText13 alignLeft" style="width:200px;">Overall Discount</td>
					<td class="whiteBG rptText13 alignLeft" style="width:80px;"></td>
					<td class="whiteBG rptText13 alignLeft" style="width:80px;"></td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;"></td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;"></td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).number_format($overall_discount[$ord_id],2).'</td>
					<td class="whiteBG rptText13 alignRight" style="width:80px;"></td>
					</tr>';
			}
			
			/*$net_profit=array_sum($tot_amt_arr)-(array_sum($frame_cost_arr)+array_sum($lenses_cost_arr)+array_sum($cl_cost_arr)+array_sum($supplies_cost_arr)+
			array_sum($meds_cost_arr)+array_sum($access_cost_arr)+array_sum($tot_ins_adj_arr)+array_sum($tot_discount_arr));*/
			$net_profit=array_sum($tot_amt_arr)-(array_sum($tot_wh_arr)+array_sum($tot_discount_arr));
			
			$show_rptText13b="rptText13";
			$add_width_in_sum=47.5;
			$sum_html_data.='<tr style="height:20px;">';
			if($show_report=="detail"){
				$sum_html_data.='<td class="whiteBG rptText13 alignCenter" style="width:85px;"></td>
					<td class="whiteBG rptText13 alignCenter" style="width:100px;"></td>
					<td class="whiteBG rptText13 alignCenter" style="width:180px;"></td>
					<td class="whiteBG rptText13 alignCenter" style="width:200px;"></td>';
					$show_rptText13b="rptText13b";
					$add_width_in_sum=0;
			}else{
				$sum_html_data.='<td class="whiteBG rptText13 alignCenter" style="width:'.(85+$add_width_in_sum).'px;">'.$ord_id.'</td>
				<td class="whiteBG rptText13 alignCenter" style="width:'.(100+$add_width_in_sum).'px;">'.$ord_det_data['order_date'].'</td>';
			}
			$sum_html_data.='
					<td class="whiteBG '.$show_rptText13b.' alignRight" style="width:'.(80+$add_width_in_sum).'px;">'.currency_symbol(true).number_format(array_sum($tot_wh_arr),2).'</td>
					<td class="whiteBG '.$show_rptText13b.' alignRight" style="width:'.(80+$add_width_in_sum).'px;">'.currency_symbol(true).number_format(array_sum($tot_ret_arr),2).'</td>
					<td class="whiteBG '.$show_rptText13b.' alignRight" style="width:'.(80+$add_width_in_sum).'px;">'.currency_symbol(true).number_format(array_sum($tot_amt_arr),2).'</td>
					<td class="whiteBG '.$show_rptText13b.' alignRight" style="width:'.(80+$add_width_in_sum).'px;">'.currency_symbol(true).number_format(array_sum($tot_ins_adj_arr),2).'</td>
					<td class="whiteBG '.$show_rptText13b.' alignRight" style="width:'.(80+$add_width_in_sum).'px;">'.currency_symbol(true).number_format(array_sum($tot_discount_arr),2).'</td>
					<td class="whiteBG '.$show_rptText13b.' alignRight" style="width:'.(80+$add_width_in_sum).'px;">'.currency_symbol(true).number_format(array_sum($tot_pat_paid_arr),2).'</td>
					</tr>';
				
			$sum_html_data.='<tr style="height:35px;"><td style="width:85px;"></td>';
			if($show_report=="detail"){
				$sum_html_data.='<td style="width:10px;"></td><td></td>';
			}
			if(count($module_type_id_arr)==1){
				$sum_html_data.='<td></td><td></td>';
			}else if(count($module_type_id_arr)==2){
				$sum_html_data.='';
			}else{
				$sum_html_data.='';
			}
			
			$sum_cogs_arr=array();
			foreach($module_type_id_arr as $mod_type_key => $mod_type_val){
				if($mod_type_key=="1"){
					$sum_cogs_arr[2]='<td class="rptText13b alignRight" nowrap colspan=2>Frame Cost : '.currency_symbol(true).number_format(array_sum($frame_cost_arr),2).'</td>';
				}
				if($mod_type_key=="2"){
					$sum_cogs_arr[1]='<td class="rptText13b alignRight" nowrap colspan=2>Lab Cost : '.currency_symbol(true).number_format(array_sum($lenses_cost_arr),2).'</td>';
				}
				if($mod_type_key=="3"){
					$sum_cogs_arr[3]='<td class="rptText13b alignRight" nowrap colspan=2>CL Cost : '.currency_symbol(true).number_format(array_sum($cl_cost_arr),2).'</td>';
				}
				if($mod_type_key=="5"){
					$sum_cogs_arr[5]='<td class="rptText13b alignRight" nowrap>Supplies Cost : '.currency_symbol(true).number_format(array_sum($supplies_cost_arr),2).'</td>';
				}
				if($mod_type_key=="6"){
					$sum_cogs_arr[6]='<td class="rptText13b alignRight" nowrap>Medicines Cost : '.currency_symbol(true).number_format(array_sum($meds_cost_arr),2).'</td>';
				}
				if($mod_type_key=="7"){
					$sum_cogs_arr[7]='<td class="rptText13b alignRight" nowrap>Accessories Cost : '.currency_symbol(true).number_format(array_sum($access_cost_arr),2).'</td>';
				}
			}
			ksort($sum_cogs_arr);
			$sum_cogs_data=implode('',$sum_cogs_arr);
			$sum_html_data.=$sum_cogs_data.'<td class="rptText13b alignRight" nowrap colspan=2>Net Profit : '.currency_symbol(true).number_format($net_profit,2).'</td>
					<td class="rptText13b alignRight" nowrap><!--COGS : 0%--></td>
				</tr>';
			$grand_charges[]=array_sum($tot_amt_arr);
			$grand_ins_adj_arr[]=array_sum($tot_ins_adj_arr);
			$grand_discount_arr[]=array_sum($tot_discount_arr);
			$grand_pat_paid_arr[]=array_sum($tot_pat_paid_arr);
				
			$grand_lenses_cost_arr[]=array_sum($lenses_cost_arr);
			$grand_frame_cost_arr[]=array_sum($frame_cost_arr);
			$grand_cl_cost_arr[]=array_sum($cl_cost_arr);
			$grand_supplies_cost_arr[]=array_sum($supplies_cost_arr);
			$grand_meds_cost_arr[]=array_sum($meds_cost_arr);
			$grand_access_cost_arr[]=array_sum($access_cost_arr);
			$grand_net_profit[]=$net_profit;
		}
		
		$cog_srh_html='<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
			<tr style="height:20px;">
				<td style="text-align:left;" class="reportHeadBG" >&nbsp;Cost of Goods Report</td>
				<td style="text-align:left;" class="reportHeadBG" colspan="2" >&nbsp;Report for Date : '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
				<td style="text-align:left;" class="reportHeadBG" colspan="2">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
			</tr>
			<tr style="height:20px;">
				<td class="reportHeadBG" width="220">&nbsp;Operator : '.$selOpr.'</td>
				<td class="reportHeadBG" width="220">&nbsp;Facility : '.$selfacility.'</td>
				<td class="reportHeadBG" width="220">&nbsp;Product Type : '.$selprotype.'</td>
				<td class="reportHeadBG" width="220">&nbsp;Status : '.$selstatus.'</td>
				<td class="reportHeadBG" width="180">&nbsp;Report Type : '.ucfirst($_POST['show_report']).'</td>		
			</tr>
		</table>';
		
		$sum_label_html='<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr style="height:25px;">
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:'.(85+$add_width_in_sum).'px;">Order #</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:'.(100+$add_width_in_sum).'px;">Order Date</td>';
		if($show_report=="detail"){
		$sum_label_html.='<td class="reportHeadBG1 alignTop" style="text-align:center; width:180px;">Patient Name - Id</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:200px;">Upc Code - Item Name</td>';	
		}
		$sum_label_html.='<td class="reportHeadBG1 alignTop" style="text-align:center; width:'.(80+$add_width_in_sum).'px;">Wholesale</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:'.(80+$add_width_in_sum).'px;">Retail</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:'.(80+$add_width_in_sum).'px;">Amount</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:'.(80+$add_width_in_sum).'px;">Ins. Resp.</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:'.(80+$add_width_in_sum).'px;">Discount</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:'.(80+$add_width_in_sum).'px;">Pat Paid</td>
			</tr>';
		
		$sum_grand_cogs_arr=array();
		foreach($grand_module_type_id_arr as $mod_type_key => $mod_type_val){
			if($mod_type_key=="1"){
				$sum_grand_cogs_arr[2]='<td class="rptText13b" style="width:265px;">Frame Cost : '.currency_symbol(true).number_format(array_sum($grand_frame_cost_arr),2).'</td>';
			}
			if($mod_type_key=="2"){
				$sum_grand_cogs_arr[1]='<td class="rptText13b" style="width:265px;">Lab Cost : '.currency_symbol(true).number_format(array_sum($grand_lenses_cost_arr),2).'</td>';
			}
			if($mod_type_key=="3"){
				$sum_grand_cogs_arr[3]='<td class="rptText13b" style="width:265px;">CL Cost : '.currency_symbol(true).number_format(array_sum($grand_cl_cost_arr),2).'</td>';
			}
			if($mod_type_key=="5"){
				$sum_grand_cogs_arr[5]='<td class="rptText13b" style="width:265px;">Supplies Cost : '.currency_symbol(true).number_format(array_sum($grand_supplies_cost_arr),2).'</td>';
			}
			if($mod_type_key=="6"){
				$sum_grand_cogs_arr[6]='<td class="rptText13b" style="width:265px;">Medicines Cost : '.currency_symbol(true).number_format(array_sum($grand_meds_cost_arr),2).'</td>';
			}
			if($mod_type_key=="7"){
				$sum_grand_cogs_arr[7]='<td class="rptText13b" style="width:265px;">Accessories Cost : '.currency_symbol(true).number_format(array_sum($grand_access_cost_arr),2).'</td>';
			}
		}
		$sum_grand_cogs_arr[11]='<td class="rptText13b" style="width:265px;">Net Profit : '.currency_symbol(true).number_format(array_sum($grand_net_profit),2).'</td>';
		//$sum_grand_cogs_arr[12]='<td class="rptText13b" style="width:260px;"><!--Cost of Goods : 0%--></td>';
		ksort($sum_grand_cogs_arr);
		
		$cog_grand_data='</table><table style="width:100%; border:none;" cellpadding="0" cellspacing="0">
			<tr style="height:10px;">
				<td style="width:10px;">&nbsp;</td>
				<td style="width:260px;"></td>
				<td style="width:260px;">&nbsp;</td>
				<td style="width:260px;">&nbsp;</td>
				<td style="width:260px;">&nbsp;</td>
			</tr>
			<tr style="height:35px;">
				<td class="reportTitle" style="width:10px;">&nbsp;</td>
				<td class="alignLeft reportTitle" style="width:260px;">Grand Total</td>
				<td class="reportTitle" style="width:260px;">&nbsp;</td>
				<td class="reportTitle" style="width:260px;">&nbsp;</td>
				<td class="reportTitle" style="width:260px;">&nbsp;</td>
			</tr>
			<tr style="height:35px;background:#E3E3E3;">
				<td style="width:10px;"></td>
				<td class="rptText13b" style="width:260px;">Charges : '.currency_symbol(true).number_format(array_sum($grand_charges),2).'</td>
				<td class="rptText13b" style="width:260px;">Ins Resp. : '.currency_symbol(true).number_format(array_sum($grand_ins_adj_arr),2).'</td>
				<td class="rptText13b" style="width:260px;">Discount : '.currency_symbol(true).number_format(array_sum($grand_discount_arr),2).'</td>
				<td class="rptText13b" style="width:260px;">Pat Paid : '.currency_symbol(true).number_format(array_sum($grand_pat_paid_arr),2).'</td>
			</tr>
			<tr style="height:35px;background:#E3E3E3;">
				<td style="width:10px;"></td>';
		$cog_i=0;		
		foreach($sum_grand_cogs_arr as $sum_grand_cogs_key => $sum_grand_cogs_val){	
			$cog_i++;
			if($cog_i==5){
				$cog_grand_data.='</tr><tr style="height:35px;background:#E3E3E3;"><td style="width:10px;"></td>';	
			}
			$cog_grand_data.=$sum_grand_cogs_arr[$sum_grand_cogs_key];	
		}
		$cog_grand_data.='</tr></table>';
		
		echo $cog_srh_html.$sum_label_html.$sum_html_data.$cog_grand_data;

		$mm = 15;
		$finalReportHtmlPDF ='
			<page backtop="'.$mm.'mm" backbottom="5mm">
			<page_footer>
					<table>
						<tr>
							<td style="text-align: center;	width: 700px">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
			</page_footer>
			<page_header>		
			'.$cog_srh_html.$sum_label_html.'</table></page_header>
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">'.
			$sum_html_data.$cog_grand_data.'
		</page>';
		
		
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
	$pdfText = $css.$finalReportHtmlPDF;
  	file_put_contents('../../library/new_html2pdf/cost_of_goods_result.html',$pdfText);
}
?>
<html>
<head>
<title></title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
</head>
<body>
<?php
if(count($ord_data_arr)>0)
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
<form name="searchFormResult" action="cost_of_goods_result.php" method="post">
<input type="hidden" name="operators" id="operators" value="" />
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
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=l&file_name=../../library/new_html2pdf/cost_of_goods_result';
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