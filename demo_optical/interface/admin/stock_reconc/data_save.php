<?php 
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
//error_reporting(E_All);
//ini_set('display_errors', 1);
$batch_last_id="";
$date=date("Y-m-d h:i:s");
$quan=$_REQUEST['item_quan'];
$item_id=$_REQUEST['item_id'];
$item_upc=$_REQUEST['upc_code'];
$lot_no=$_REQUEST['lot_no'];
$bat_rec_id=$_REQUEST['bat_rec_id'];
$reson=$_REQUEST['resaon'];
$fac_qty=$_REQUEST['fac_quan'];
$fac_lot_qty=$_REQUEST['fac_lot_quan'];
$tot_qt=$_REQUEST['tot_qnty'];
$prod_names = $_REQUEST['prod_name'];
$module_types = $_REQUEST['module_type'];
$retail_flags = $_REQUEST['retail_flags'];
$discounts = $_REQUEST['discounts'];
$wholesale_price_existings = $_REQUEST['wholesale_price_existings'];
$retail_price_existings = $_REQUEST['retail_price_existings'];
$purchase_price_existings = $_REQUEST['purchase_price_existings'];

/*Fields only for New Item to be added*/
$manfacturers	= $_REQUEST['manfacturers'];
$vendor			= $_REQUEST['vendors'];
$brands			= $_REQUEST['brands'];
$wholesale_price= $_REQUEST['wholesale_price'];
$retail_price	= $_REQUEST['retail_price'];
$purchase_price	= $_REQUEST['purchase_price'];
/*Only for Frames*/
$ed				= $_REQUEST['ed'];
$dbl			= $_REQUEST['dbl'];
$temple			= $_REQUEST['temple'];

$previous_stock	= $_REQUEST['previous_stock'];
$new_stock		= $_REQUEST['new_stock'];

$upc_code_arr=explode(",",rtrim($item_upc,","));
$lot_no_arr=explode(",",rtrim($lot_no,","));
$item_id_arr=explode(",",rtrim($item_id,","));
$item_quan_arr=explode(",",rtrim($quan,","));
$bat_rec_id_arr=explode(",",rtrim($bat_rec_id,","));
$reson_arr=explode(",",rtrim($reson,","));
$batch_id=$_REQUEST['batch'];
$fac_qty_ar=explode(",",rtrim($fac_qty,","));
$fac_lot_qty_ar=explode(",",rtrim($fac_lot_qty,","));
$prev_tot_ar=explode(",",rtrim($tot_qt,","));
$prod_name_ar= explode(",",rtrim($prod_names,","));
$module_type_ar = explode(",",rtrim($module_types,","));
$retail_flag_arr = explode(",",rtrim($retail_flags,","));
$discount_arr = explode(",",rtrim($discounts,","));
$wholesale_exis_arr = explode(",",rtrim($wholesale_price_existings,","));
$retail_exis_arr = explode(",",rtrim($retail_price_existings,","));
$purchase_exis_arr = explode(",",rtrim($purchase_price_existings,","));

/*Fields only for New Item to be added*/
$manfacturers_arr	= explode(",",rtrim($manfacturers,","));
$vendor_arr			= explode(",",rtrim($vendor,","));
$brands_arr			= explode(",",rtrim($brands,","));
$wholesale_price_arr= explode(",",rtrim($wholesale_price,","));
$retail_price_arr	= explode(",",rtrim($retail_price,","));
$purchase_price_arr	= explode(",",rtrim($purchase_price,","));
/*Only for Frames*/
$ed_arr				= explode(",",rtrim($ed,","));
$dbl_arr			= explode(",",rtrim($dbl,","));
$temple_arr			= explode(",",rtrim($temple,","));

$previous_stock_arr	= explode(",",rtrim($previous_stock,","));
$new_stock_arr		= explode(",",rtrim($new_stock,","));
//file_put_contents('test.txt', print_r($_REQUEST,true));
if($item_upc!="")
{
	if($batch_id!="")
	{
		/*$sql_batch = "SELECT `lot_no`, item_id FROM `in_item_lot_total` WHERE `loc_id`='".$_SESSION['pro_fac_id']."' ORDER BY `id`";//`item_id`='".$item_id_arr[$i]."'
		$batch_resp = imw_query($sql_batch);
		if($batch_resp && imw_num_rows($batch_resp)>0){
			while($prev_lot_no = imw_fetch_assoc($batch_resp)){
				$arr_lot_no[$item_lot_no['item_id']] = $item_lot_no['lot_no'];
			}
		}
*/
		$stock_recons_batch_sql = imw_query("SELECT `lot_no`,batch_record_id, item_id FROM `in_batch_lot_records` WHERE `batch_record_id` IN (".rtrim($bat_rec_id,",").")");
		if($stock_recons_batch_sql && imw_num_rows($stock_recons_batch_sql)>0){
			while($rec_row = imw_fetch_object($stock_recons_batch_sql)){
				$arr_rec_lots[$rec_row->batch_record_id][$rec_row->item_id][$rec_row->lot_no] = true;
			}
		}
	
		$batchHistory = array('user_id'=>$_SESSION["authId"],'action'=>'saved','date'=>$date,"page"=>"Stock Reconciliation");
		
		$user_detail=imw_query("select * from in_batch_table where id='".$batch_id."'");
		$user_data=imw_fetch_array($user_detail);
		
		$un_ar=json_decode($user_data['user_detail']);
		$un_ar[]=$batchHistory;
		
		$query=imw_query("UPDATE in_batch_table SET user_id='".$_SESSION["authId"]."',save_date='".$date."',status='saved',user_detail='".json_encode($un_ar)."' WHERE id='".$batch_id."'");
		
		//get detail of already saved records
		$q_rec_detail=imw_query("select item_upc_code, in_item_id, lot_no, id in_batch_records where in_batch_id='$batch_id'");
		while($rec_detail=imw_fetch_object($q_rec_detail))
		{
			$records_saved[$rec_detail->item_upc_code][$rec_detail->in_item_id][$rec_detail->lot_no]=$rec_detail->id;
		}
		
		for($i=0;$i<count($upc_code_arr);$i++)
		{
			/*Enter Inventory from Stock Reconciliation*/
			$nItemFlag = false;
			$batch_record_id = $bat_rec_id_arr[$i];
			//$prev_lot_no = "";
			/*if($arr_lot_no[$item_id_arr[$i]]){
				$prev_lot_no = $arr_lot_no[$item_id_arr[$i]];
			}*/
			//get existing lot number if any
			//if($lot_no_arr[$i])$prev_lot_no = $lot_no_arr[$i];
			//create new stock number if required
			$new_lot_no=date('Ymd').$_SESSION["authId"].$i;
			$lot_no=($lot_no_arr[$i])?$lot_no_arr[$i]:$new_lot_no;
			$stock=($item_id_arr[$i]=="new")?$new_stock_arr[$i]:$previous_stock_arr[$i];
			
			if($item_id_arr[$i]=="new"){
				$nItemFlag = true;
				$item_name = "";
				$new_item_qry = "INSERT INTO `in_item` SET `upc_code`='".$upc_code_arr[$i]."', 
				`name`='".$prod_name_ar[$i]."', 
				`module_type_id`='".$module_type_ar[$i]."', 
				`manufacturer_id`='".$manfacturers_arr[$i]."', 
				`vendor_id`='".$vendor_arr[$i]."', 
				`brand_id`='".$brands_arr[$i]."', 
				`wholesale_cost`='".$wholesale_price_arr[$i]."', 
				`purchase_price`='".$purchase_price_arr[$i]."', 
				`retail_price`='".$retail_price_arr[$i]."', 
				`retail_price_flag`='".$retail_flag_arr[$i]."', 
				`discount`='".$discount_arr[$i]."', 
				`entered_date`='".(date("Y-m-d"))."', 
				`entered_time`='".(date('h:i:s'))."', 
				`entered_by`='".$_SESSION["authId"]."'";
				
				$retail_exis_arr[$i] = $retail_price_arr[$i];
				$purchase_exis_arr[$i] = $purchase_price_arr[$i];
				$wholesale_exis_arr[$i] = $wholesale_price_arr[$i];
				
				/*Fields for Frames Only*/
				if( $module_type_ar[$i] == '1' ){
					$new_item_qry .= ", `ed`='".$ed_arr[$i]."', `dbl`='".$dbl_arr[$i]."', `temple`='".$temple_arr[$i]."'";
				}
				
				$sql_item = imw_query($new_item_qry)or die(imw_error().'129');
				if($sql_item){
					$item_id_arr[$i] = imw_insert_id();
				}
			}
			
			if($batch_record_id!="" && $batch_record_id!="0")
			{
				$q1='';
				
				/*Get Lot Numbers from stock reconciliations batch records*/
				$rec_lots = array();
				$rec_lots=$arr_rec_lots[$batch_record_id];
				
				$where=$update="";
				if($nItemFlag!=true)
				{$where=" and lot_no='$lot_no'";}
				else
				{$update=",lot_no='$lot_no'";}
					
				$q1="UPDATE in_batch_records SET in_item_quant='".$item_quan_arr[$i]."',
				`reason`='".$reson_arr[$i]."', 
				`retail_price_flag`='".$retail_flag_arr[$i]."', 
				`wholesale_price`='".$wholesale_exis_arr[$i]."', 
				`retail_price`='".$retail_exis_arr[$i]."', 
				`purchase_price`='".$purchase_exis_arr[$i]."', 
				`discount`='".$discount_arr[$i]."' $update
				WHERE id='".$batch_record_id."'";
				/*WHERE in_batch_id=".$batch_id." and in_item_id ='".$item_id_arr[$i]."' $where";*/
				$batch_save=imw_query($q1)or die(imw_error().'166');
				//file_put_contents('test.txt', "\n #lot item=$item_id_arr[$i], $lot_no, new stock= ".$new_stock_arr[$i]."\n\n".print_r($rec_lots, true), FILE_APPEND);
				if($batch_save){
					
					/*If Stock entered for Previous Batch*/
					if($rec_lots[$item_id_arr[$i]][$lot_no]){
					//	file_put_contents('test.txt', "\n 1.have lot:$lot_no stock=".$stock, FILE_APPEND);
						imw_query("UPDATE `in_batch_lot_records` SET `stock`='".$stock."'
									WHERE `batch_record_id`='".$batch_record_id."'");
						 
								/*	AND `lot_no`='".$lot_no."' 
									AND item_id='".$item_id_arr[$i]."'*/
					}/*newly added item in stock*/
					elseif($new_stock_arr[$i]!="0" && $new_stock_arr[$i]!=" "){
						//file_put_contents('test.txt', '\n 1.do not have lot: '.$previous_stock_arr[$i], FILE_APPEND);
						imw_query("INSERT INTO `in_batch_lot_records` SET `batch_record_id`='".$batch_record_id."',
									`item_id`='".$item_id_arr[$i]."',
									`lot_no`='".$lot_no."', 
									`stock`='".$stock."'");
					}else{ /*item is neither in old batch nor in new stock*/
						imw_query("INSERT INTO `in_batch_lot_records` SET `stock`='".$stock."',
									`batch_record_id`='".$batch_record_id."',
									`lot_no`='".$lot_no."', 
									item_id='".$item_id_arr[$i]."'");
						
					}
				}	
			}
			else
			{
				
				$loop_lot_no=$loop_item_id=$loop_upc='';
				$loop_lot_no=$lot_no;
				$loop_item_id=$item_id_arr[$i];
				$loop_upc=$upc_code_arr[$i];
				
				$records_str="in_item_quant= '".$item_quan_arr[$i]."',
				in_fac_prev_qty='".$fac_qty_ar[$i]."',
				in_fac_lot_prev_qty='".$fac_lot_qty_ar[$i]."',
				prev_tot_qty='".$prev_tot_ar[$i]."',
				in_batch_id='".$batch_id."',
				reason='".$reson_arr[$i]."',
				retail_price_flag='".$retail_flag_arr[$i]."', 
				wholesale_price='".$wholesale_exis_arr[$i]."',
				retail_price='".$retail_exis_arr[$i]."', 
				discount='".$discount_arr[$i]."', 
				purchase_price='".$purchase_exis_arr[$i]."',";
				
				$batch_save=imw_query("INSERT INTO in_batch_records set item_upc_code='".$loop_upc."',
				in_item_id='".$loop_item_id."',
				$records_str
				lot_no='".$loop_lot_no."'") or $sqlError[]=imw_error().' ln297';
				$batch_record_id = imw_insert_id();
				//add this record in existing records arr
				$records_saved[$loop_upc][$loop_item_id][$loop_lot_no]=$batch_record_id;
				
				if($batch_save){

					/*If Stock entered for Previous Batch*/
					/*if($previous_stock_arr[$i]!="0" && $previous_stock_arr[$i]!=" "){
						//file_put_contents('test.txt', '\n 2.have lot: '.$previous_stock_arr[$i], FILE_APPEND);*/
						imw_query("INSERT INTO `in_batch_lot_records` SET
									`batch_record_id`='".$batch_record_id."',
									`item_id`='".$loop_upc."',
									`lot_no`='".$lot_no."', `stock`='".$stock."'");
					/*}
				
					If Stock entered for New Batch
					if($new_stock_arr[$i]!="0" && $new_stock_arr[$i]!=" "){
						//file_put_contents('test.txt', '\n 2.do not have lot: '.$new_stock_arr[$i], FILE_APPEND);
						imw_query("INSERT INTO `in_batch_lot_records` SET
									`batch_record_id`='".$batch_record_id."',
									`item_id`='".$item_id_arr[$i]."',
									`lot_no`='".$lot_no."', `stock`='".$stock."'");
					}*/
				}
			}
		}
		unset($rec_lots);
	}
	else
	{
		$batchHistory[]=array('user_id'=>$_SESSION["authId"],'action'=>'saved','date'=>$date,"page"=>"Stock Reconciliation");
		
		$batch_data=imw_query("INSERT INTO in_batch_table(user_id,save_date,status,facility,user_detail) VALUES (".$_SESSION["authId"].",'".$date."','saved', '".$_SESSION['pro_fac_id']."','".json_encode($batchHistory)."')");
		if($batch_data)
		{
			$batch_last_id=imw_insert_id();
		}
		for($i=0;$i<count($upc_code_arr);$i++)
		{
			/*
			$prev_lot_no = "";
			if($arr_lot_no[$item_id_arr[$i]]){
				$prev_lot_no = $arr_lot_no[$item_id_arr[$i]];
			}*/
			
			//get existing lot number if any
			//if($lot_no_arr[$i])$prev_lot_no = $lot_no_arr[$i];
			//create new stock number if required
			$new_lot_no=date('Ymd').$_SESSION["authId"].$i;
			$lot_no=($lot_no_arr[$i])?$lot_no_arr[$i]:$new_lot_no;
			/*Enter Inventory from Stock Reconciliation*/
			$nItemFlag = false;
			if($item_id_arr[$i]=="new"){
				$nItemFlag = true;
				$item_name = "";

				$new_item_qry = "INSERT INTO `in_item` SET `upc_code`='".$upc_code_arr[$i]."', `name`='".$prod_name_ar[$i]."', `module_type_id`='".$module_type_ar[$i]."', `manufacturer_id`='".$manfacturers_arr[$i]."', `vendor_id`='".$vendor_arr[$i]."', `brand_id`='".$brands_arr[$i]."', `wholesale_cost`='".$wholesale_price_arr[$i]."', `purchase_price`='".$purchase_price_arr[$i]."', `retail_price`='".$retail_price_arr[$i]."', `retail_price_flag`='".$retail_flag_arr[$i]."', `discount`='".$discount_arr[$i]."', `entered_date`='".(date("Y-m-d"))."', `entered_time`='".(date('h:i:s'))."', `entered_by`='".$_SESSION["authId"]."'";

				$retail_exis_arr[$i] = $retail_price_arr[$i];
				$purchase_exis_arr[$i] = $purchase_price_arr[$i];
				$wholesale_exis_arr[$i] = $wholesale_price_arr[$i];

				/*Fields for Frames Only*/
				if( $module_type_ar[$i] == '1' ){
					$new_item_qry .= ", `ed`='".$ed_arr[$i]."', `dbl`='".$dbl_arr[$i]."',`temple`='".$temple_arr[$i]."'";
				}
				$sql_item = imw_query($new_item_qry)or die(imw_error().' 279');
				if($sql_item){
					$item_id_arr[$i] = imw_insert_id();
				}
			}
			
			$add="";
			if($nItemFlag)
			{
				$add=",lot_no='$lot_no'";
				$stock=$new_stock_arr[$i];
			}
			else
			{
				$add=",lot_no='$lot_no'";
				$stock=$previous_stock_arr[$i];
			}
			
			$batch_save=imw_query("INSERT INTO in_batch_records set item_upc_code='".$upc_code_arr[$i]."',
			in_item_id='".$item_id_arr[$i]."',
			in_item_quant='".$item_quan_arr[$i]."',
			in_fac_prev_qty='".$fac_qty_ar[$i]."',
			in_fac_lot_prev_qty='".$fac_lot_qty_ar[$i]."',
			prev_tot_qty='".$prev_tot_ar[$i]."',
			in_batch_id='".$batch_last_id."',
			reason='".$reson_arr[$i]."',
			retail_price_flag='".$retail_flag_arr[$i]."',
			wholesale_price='".$wholesale_exis_arr[$i]."',
			retail_price='".$retail_exis_arr[$i]."',
			discount='".$discount_arr[$i]."',
			purchase_price='".$purchase_exis_arr[$i]."' $add")or die(imw_error().'311');
			
			if($batch_save){
				$batch_record_id = imw_insert_id();
				imw_query("INSERT INTO `in_batch_lot_records` SET `batch_record_id`='".$batch_record_id."',
							`lot_no`='".$lot_no."', 
							`stock`='".$stock."', 
							`item_id`='".$item_id_arr[$i]."'");
				
			}
		}
	}
	//remove data from temporary table if any
	imw_query("delete from in_temp_batch_record where `user_id`='".$_SESSION["authId"]."'");
}
?>