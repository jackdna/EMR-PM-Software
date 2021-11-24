<?php
/*pro_fac_id*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/common_functions.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
//remove data from temporary table if any
imw_query("delete from in_temp_batch_record where user_id='".$_SESSION["authId"]."'");
$column_data_limit=13;
//pre($_POST);die();
$batch_id=$_REQUEST['batch'];
if(!empty($_POST['save_qnty']))
{
	ini_set('max_execution_time', 0);
	if($_REQUEST['batch_id_field']){
		$batch_id=$_REQUEST['batch_id_field'];
	}
	$date=date("Y-m-d");
	$time=date("h:i:s");
	$batch_date=date("Y-m-d h:i:s");
	$action="";
	$quant=$_REQUEST['item_quan'];
	$prev_fac_qty=$_REQUEST['fac_quant'];
	$prev_fac_lot_qty=$_REQUEST['fac_lot_quant'];
	$item_id=$_REQUEST['item_id'];
	$upcCode=$_REQUEST['upc_code'];
	$lotNo=$_REQUEST['lot_no'];
	$reason=$_REQUEST['resn_sel'];
	$prev_tot_qnt=$_REQUEST['tot_qnt'];
	$prod_names = $_REQUEST['prod_name'];
	$module_types = $_REQUEST['module_type'];
	$retail_flags = $_REQUEST['retail_price_flag'];
	$purchase_flags = $_REQUEST['purchase_price_flag'];
	$discounts = $_REQUEST['discount'];
	$wholesale_price_existings = $_REQUEST['wholesale_price_exis'];
	$retail_price_existings = $_REQUEST['retail_price_exis'];
	$purchase_price_existings = $_REQUEST['purchase_price_exis'];
	
	/*Fields only for New Item to be added*/
	$manfacturers	= $_REQUEST['prod_manuf'];
	$vendor			= $_REQUEST['prod_vendor'];
	$brands			= $_REQUEST['prod_brand'];
	$wholesale_price= $_REQUEST['prod_wholesale'];
	$retail_price	= $_REQUEST['prod_retail'];
	$purchase_price	= $_REQUEST['prod_purchase'];
	/*Only for Frames*/
	$ed				= $_REQUEST['prod_ed'];
	$dbl			= $_REQUEST['prod_dbl'];
	$temple			= $_REQUEST['prod_temple'];
	
	$previous_stock	= $_REQUEST['previous_stock'];
	$new_stock		= $_REQUEST['new_stock'];

	if($batch_id=="")
	{
		$batchHistory[]=array('user_id'=>$_SESSION["authId"],'action'=>'updated','date'=>$batch_date,"page"=>"Stock Reconciliation");
		
		$batch_data=imw_query("INSERT INTO in_batch_table(user_id,save_date,status,facility,user_detail) VALUES (".$_SESSION["authId"].",'".$batch_date."','updated', '".$_SESSION['pro_fac_id']."','".json_encode($batchHistory)."')") or $sqlError[]=imw_error().' ln52';
		if($batch_data)
		{
			$batch_last_id=imw_insert_id();
		}
		
		//get detail of already saved records
		$q_rec_detail=imw_query("select item_upc_code, in_item_id, lot_no, id in_batch_records where in_batch_id='$batch_last_id'");
		while($rec_detail=imw_fetch_object($q_rec_detail))
		{
			$records_saved[$rec_detail->item_upc_code][$rec_detail->in_item_id][$rec_detail->lot_no]=$rec_detail->id;
		}
		
		//for($i=0;$i<count($upcCode);$i++)
		foreach($item_id as $loop_id=>$itm_id)
		{
			/*Enter Inventory from Stock Reconciliation*/
			$nItemFlag = false;
			
			$new_lot_no=date('Ymd').$_SESSION["authId"].$i;
			$lot_no=($lotNo[$loop_id])?$lotNo[$loop_id]:$new_lot_no;
			//$stock=($item_id[$loop_id]=="new")?$new_stock[$loop_id]:$previous_stock[$loop_id];
			$qunatity=$quant[$loop_id];
			
			$loop_lot_no=$loop_item_id=$loop_upc='';
			$loop_lot_no=$lot_no;
			$loop_upc=$upcCode[$loop_id];
			
			if($item_id[$loop_id]=="new"){
				$nItemFlag = true;
				$item_name = "";
				
				$new_item_qry = "INSERT INTO in_item SET upc_code='".$loop_upc."', name='".$prod_names[$loop_id]."', module_type_id='".$module_types[$loop_id]."', manufacturer_id='".$manfacturers[$loop_id]."', vendor_id='".$vendor[$loop_id]."', brand_id='".$brands[$loop_id]."', wholesale_cost='".$wholesale_price[$loop_id]."', purchase_price='".$purchase_price[$loop_id]."', retail_price='".$retail_price[$loop_id]."', retail_price_flag='".$retail_flags[$loop_id]."', discount='".$discounts[$loop_id]."', entered_date='".(date("Y-m-d"))."', entered_time='".(date('h:i:s'))."', entered_by='".$_SESSION["authId"]."'";
				$retail_price_existings[$loop_id] = $retail_price[$loop_id];
				$purchase_price_existings[$loop_id] = $purchase_price[$loop_id];
				/*Fields for Frames Only*/
				if( $module_type_ar[$loop_id] == '1' ){
					$new_item_qry .= ", ed='".$ed_arr[$loop_id]."', dbl='".$dbl_arr[$loop_id]."', temple='".$temple_arr[$loop_id]."'";
				}
				
				$sql_item = imw_query($new_item_qry) or $sqlError[]=imw_error().' ln73';
				if($sql_item){
					$item_id[$loop_id] = imw_insert_id();
					$itm_id=$item_id[$loop_id];
				}
			}
			$loop_item_id=$item_id[$loop_id];
			$records_str="in_item_quant='".$quant[$loop_id]."',
			in_fac_prev_qty='".$prev_fac_qty[$loop_id]."',
			in_fac_lot_prev_qty='".$prev_fac_lot_qty[$loop_id]."',
			prev_tot_qty='".$prev_tot_qnt[$loop_id]."',
			in_batch_id='".$batch_last_id."',
			reason='".$reason[$loop_id]."',
			retail_price_flag='".$retail_flags[$loop_id]."',
			retail_price='".$retail_price_existings[$loop_id]."',
			wholesale_price='".$wholesale_price_existings[$loop_id]."',
			purchase_price='".$purchase_price_existings[$loop_id]."',
			discount='".$discounts[$loop_id]."',";
			
			if($in_batch_record_id=$records_saved[$loop_upc][$loop_item_id][$loop_lot_no])
			{
				$batch_save=imw_query("update in_batch_records set $records_str where id=$in_batch_record_id") or $sqlError[]=imw_error().' ln90';
				$batch_record_id = $in_batch_record_id;
			}
			else
			{
				$batch_save=imw_query("INSERT INTO in_batch_records set item_upc_code= '".$upcCode[$loop_id]."',
				in_item_id='".$item_id[$loop_id]."',
				$records_str
				lot_no='".$lot_no."'") or $sqlError[]=imw_error().' ln90';

				if($batch_save){
					$batch_record_id = imw_insert_id();
					$values = "";
					imw_query("INSERT INTO in_batch_lot_records SET batch_record_id='".$batch_record_id."',
								lot_no='".$lot_no."', stock='".$qunatity."', item_id='".$item_id[$loop_id]."'") or $sqlError[]=imw_error().' ln99';

					//add this record in existing records arr
					$records_saved[$loop_upc][$loop_item_id][$loop_lot_no]=$batch_record_id;

				}
			}
		//}
		//foreach($item_id as $loop_id=>$itm_id)
		//{
			$query1=imw_query("select stock, id from in_item_lot_total where item_id='".$itm_id."' AND loc_id='".$_SESSION['pro_fac_id']."' and lot_no='".$lot_no."' ORDER BY id DESC") or $sqlError[]=imw_error().' ln135';
			$row=imw_fetch_array($query1);
			if($row['stock']<$qunatity){
				$action="added";
				$action1="add";
				$new_qn=$qunatity-$row['stock'];
			}
			elseif($row['stock']==$quant[$loop_id]){
				$action="";
			}
			else{
				$action="deleted";
				$action1="minus";
				$new_qn=$row['stock']-$qunatity;
			}
			
			if($action!=""){
				
				/*$sel_batc_rec=imw_query("select * from in_batch_records 
				where in_batch_id=".$batch_last_id." 
				and item_upc_code='".$upcCode[$loop_id]."' 
				and	lot_no='".$lot_no."'") or $sqlError[]=imw_error().' ln157';
				$sel_batch_rec=imw_fetch_array($sel_batc_rec);*/
				
				$stock_detail=imw_query("insert into in_stock_detail set item_id='".$itm_id."',
				loc_id='".$_SESSION['pro_fac_id']."',
				stock='".$new_qn."',
				trans_type='".$action1."',
				reason='".$reason[$loop_id]."',
				operator_id='".$_SESSION["authId"]."',
				entered_date='".$date."',
				entered_time='".$time."',
				lot_no= '".$lot_no."', 
				source='Reconcile'") or $sqlError[]=imw_error().' ln168';
				
				imw_query("INSERT INTO in_log_quant_edit set upc_code='".$upcCode[$loop_id]."',
				modified_by='".$_SESSION["authId"]."',
				changed_date='".$date."',
				changed_time='".$time."',
				existing_quant='".$row['stock']."',
				updated_quant='".$qunatity."',
				batch_rec_id='".$batch_record_id."',
				action='".$action."',
				lot_no= '".$lot_no."'") or $sqlError[]=imw_error().' ln178';
				
				$stock_qry=imw_query("Select id from in_item_loc_total where loc_id='$_SESSION[pro_fac_id]' and item_id='$itm_id'");
				if(imw_num_rows($stock_qry)>0){
					$stock_res=imw_fetch_object($stock_qry);
					if($action1=="add")
					imw_query("update in_item_loc_total set stock=stock+$qunatity where id='$stock_res->id'");
					else
					imw_query("update in_item_loc_total set stock=stock-$qunatity where id='$stock_res->id'");
				}else{
					if($action1=="add")
					imw_query("insert into in_item_loc_total set stock='$qunatity',item_id='$itm_id',loc_id='$_SESSION[pro_fac_id]'");
				}
				
				/*Add Update Batch Records for the Items*/
				$rec_lots = imw_query("SELECT stock, lot_no FROM in_batch_lot_records WHERE batch_record_id='".$batch_record_id."'") or $sqlError[]=imw_error().' ln195';
				if($rec_lots && imw_num_rows($rec_lots)>0){
					while($rec_lot_row = imw_fetch_object($rec_lots)){
						
						/*Check if Lot already exists for the Item*/
						$exist_lot = imw_query("SELECT id FROM in_item_lot_total WHERE lot_no='".$rec_lot_row->lot_no."'
												  AND item_id='".$itm_id."' 
												  AND loc_id='".$_SESSION['pro_fac_id']."'") or $sqlError[]=imw_error().' ln202';
						if($exist_lot && imw_num_rows($exist_lot)>0){
							$lot_res=imw_fetch_assoc($exist_lot);
							$lot_id=$lot_res['id'];
							imw_query("UPDATE in_item_lot_total SET stock='".$rec_lot_row->stock."',
										purchase_price='".$purchase_price_existings[$loop_id]."',
										wholesale_price='".$wholesale_price_existings[$loop_id]."'
										WHERE id=$lot_id") or $sqlError[]=imw_error().' ln209';
						}
						else{
							imw_query("INSERT INTO in_item_lot_total SET stock='".$rec_lot_row->stock."',
										purchase_price='".$purchase_price_existings[$loop_id]."',
										wholesale_price='".$wholesale_price_existings[$loop_id]."',
										lot_no='".$rec_lot_row->lot_no."', 
										item_id='".$itm_id."',
										loc_id='".$_SESSION['pro_fac_id']."'") or $sqlError[]=imw_error().' ln217';
						}
					}
				}
				/*End Add Update Batch Records for the Items*/
				
				$sql2 = imw_query("SELECT SUM(stock) AS 'qty_on_hand' FROM in_item_loc_total WHERE item_id='".$itm_id."'") or $sqlError[]=imw_error().' ln225';
				$qty_on_hand = "0";
				if($sql2){
					$qty_on_hand = imw_fetch_assoc($sql2);
					$qty_on_hand = $qty_on_hand['qty_on_hand'];
				}
				
				$sql3 = imw_query("SELECT retail_price FROM in_item WHERE id='".$itm_id."'") or $sqlError[]=imw_error().' ln230';
				$retail_price = 0;
				if($sql3){
					$retail_price = imw_fetch_assoc($sql3);
					$retail_price = $retail_price['retail_price'];
				}
				$amount = $retail_price*$qty_on_hand;
				
				$query=imw_query("update in_item set 
				qty_on_hand='".$qty_on_hand."', 
				amount='".$amount."', 
				retail_price_flag='".$retail_flags[$loop_id]."', 
				retail_price='".$retail_price_existings[$loop_id]."', 
				wholesale_cost='".$wholesale_price_existings[$loop_id]."', 
				purchase_price='".$purchase_price_existings[$loop_id]."', 
				discount='".$discounts[$loop_id]."', 
				modified_date='".(date("Y-m-d"))."', 
				modified_time='".(date('h:i:s'))."', 
				modified_by='".$_SESSION["authId"]."' 
				where id='".$itm_id."'") or $sqlError[]=imw_error().' ln249';
				
				$batchHistory=array('user_id'=>$_SESSION["authId"],'action'=>'updated','date'=>$batch_date,"page"=>"Stock Reconciliation");
				
				$user_detail=imw_query("select * from in_batch_table where id='".$batch_last_id."'") or $sqlError[]=imw_error().' ln252';
				$user_data=imw_fetch_array($user_detail);
				$un_ar=json_decode($user_data['user_detail']);
				$un_ar[]=$batchHistory;
				
				$query2=imw_query("update in_batch_table set updated_date='".$batch_date."',status='updated',user_detail='".json_encode($un_ar)."' where id=".$batch_last_id."") or $sqlError[]=imw_error().' ln256';
			}
			if($query2){
				echo "<script type='text/javascript'>window.onload=function(){show_alert_div();}</script>";
			}
		}
	}
	else
	{
		$batch_record_id=$_REQUEST['in_bat_rec_id'];
		$batch_details = array();
		$sql0 = imw_query("SELECT facility FROM in_batch_table WHERE id='".$batch_id."'") or $sqlError[]=imw_error().' ln267';
		if($sql0){
			$batch_details = imw_fetch_assoc($sql0);
		}
		
		//get detail of already saved records
		$q_rec_detail=imw_query("select item_upc_code, in_item_id, lot_no, id in_batch_records where in_batch_id='$batch_id'");
		while($rec_detail=imw_fetch_object($q_rec_detail))
		{
			$records_saved[$rec_detail->item_upc_code][$rec_detail->in_item_id][$rec_detail->lot_no]=$rec_detail->id;
		}
		//for($i=0;$i<count($upcCode);$i++)
		foreach($item_id as $loop_id=>$itm_id)
		{
			if($batch_record_id[$loop_id]!="" && $batch_record_id[$loop_id]!="0")
			{
				$batch_save=imw_query("UPDATE in_batch_records SET in_item_quant='".$quant[$loop_id]."',reason='".$reason[$loop_id]."' WHERE id=".$batch_record_id[$loop_id]) or die(imw_error()) or $sqlError[]=imw_error().' ln276';
				$in_batch_record_id=$batch_record_id[$loop_id];
			}
			else
			{
				$loop_lot_no=$loop_item_id=$loop_upc='';
				$loop_lot_no=$lotNo[$loop_id];
				$loop_item_id=$item_id[$loop_id];
				$loop_upc=$upcCode[$loop_id];
				$records_str="
				in_item_quant='".$quant[$loop_id]."',
				in_fac_prev_qty='".$prev_fac_qty[$loop_id]."',
				in_fac_lot_prev_qty='".$prev_fac_lot_qty[$loop_id]."',
				prev_tot_qty='".$prev_tot_qnt[$loop_id]."',
				in_batch_id='".$batch_id."',
				reason='".$reason[$loop_id]."',
				wholesale_price='".$wholesale_price_existings[$loop_id]."',
				retail_price_flag='".$retail_flags[$loop_id]."',
				retail_price='".$retail_price_existings[$loop_id]."',
				purchase_price='".$purchase_price_existings[$loop_id]."',
				discount='".$discounts[$loop_id]."',";
				
				if($in_batch_record_id=$records_saved[$loop_upc][$loop_item_id][$loop_lot_no])
				{
					imw_query("update in_batch_records set $records_str where id=$in_batch_record_id") or $sqlError[]=imw_error().' ln290';
					$in_batch_record_id=imw_insert_id();
				}
				else{
					imw_query("INSERT INTO in_batch_records set item_upc_code='".$loop_upc."',
					in_item_id='".$loop_item_id."',
					$records_str
					lot_no='".$loop_lot_no."'") or $sqlError[]=imw_error().' ln297';
					$in_batch_record_id=imw_insert_id();
					//add this record in existing records arr
					$records_saved[$loop_upc][$loop_item_id][$loop_lot_no]=$in_batch_record_id;
				}
			}
		
			$query1=imw_query("select stock,id from in_item_lot_total where item_id='".$itm_id."' AND loc_id='".$batch_details['facility']."' ORDER BY id DESC") or $sqlError[]=imw_error().' ln299';
			$row=imw_fetch_array($query1);
			
			/*$sel_batc_rec=imw_query("select * from in_batch_records where in_batch_id=".$batch_id." and item_upc_code='".$upcCode[$loop_id]."' and lot_no='".$lotNo[$loop_id]."'") or $sqlError[]=imw_error().' ln301';
			$row_bat_rec_id=imw_fetch_array($sel_batc_rec);*/
				
			$qunatity=$quant[$loop_id];//+$row['qty_on_hand'];
			if($row['stock']<$qunatity)
			{
				$action="added";
				$action1="add";
				$new_qn=$qunatity-$row['stock'];
			}
			elseif($row['stock']==$quant[$loop_id])
			{
				$action=$action1="";
			}
			else
			{
				$action="deleted";
				$action1="minus";
				$new_qn=$row['stock']-$qunatity;
			}
			if($action!="")
			{
				imw_query("insert into in_stock_detail(item_id,loc_id,stock,trans_type,reason,operator_id,entered_date,entered_time, lot_no, source) values('".$itm_id."','".$_SESSION['pro_fac_id']."','".$new_qn."','".$action1."','".$reason[$loop_id]."','".$_SESSION["authId"]."','".$date."','".$time."','".$lotNo[$loop_id]."', 'Reconcile')") or $sqlError[]=imw_error().' ln325';	
				
				imw_query("INSERT INTO in_log_quant_edit(upc_code,modified_by,changed_date,changed_time,existing_quant,updated_quant,batch_rec_id,action, lot_no) VALUES('".$upcCode[$loop_id]."','".$_SESSION["authId"]."','".$date."','".$time."','".$row['stock']."','".$qunatity."','".$in_batch_record_id."','".$action."', '".$lotNo[$loop_id]."')") or $sqlError[]=imw_error().' ln326';
				
				$stock_qry=imw_query("Select id from in_item_loc_total where loc_id='$_SESSION[pro_fac_id]' and item_id='$itm_id'");
				if(imw_num_rows($stock_qry)>0){
					$stock_res=imw_fetch_object($stock_qry);
					if($action1=="add")
					imw_query("update in_item_loc_total set stock=stock+$qunatity where id='$stock_res->id'");
					else
					imw_query("update in_item_loc_total set stock=stock-$qunatity where id='$stock_res->id'");
				}else{
					if($action1=="add")
					imw_query("insert into in_item_loc_total set stock='$qunatity',item_id='$itm_id',loc_id='$_SESSION[pro_fac_id]'");
				}
				
				/*Add Update Batch Records for the Items*/
				$rec_lots = imw_query("SELECT lot_no, stock FROM in_batch_lot_records WHERE batch_record_id='".$in_batch_record_id."'") or $sqlError[]=imw_error().' ln338';
				if($rec_lots && imw_num_rows($rec_lots)>0){
					while($rec_lot_row = imw_fetch_object($rec_lots)){
						/*Check if Lot already exists for the Item*/
						$exist_lot = imw_query("SELECT id FROM in_item_lot_total WHERE lot_no='".$rec_lot_row->lot_no."'
												  AND item_id='".$itm_id."' AND loc_id='".$_SESSION['pro_fac_id']."'") or $sqlError[]=imw_error().' ln343';
						if($exist_lot && imw_num_rows($exist_lot)>0){
							$lot_res=imw_fetch_assoc($exist_lot);
							 imw_query("UPDATE in_item_lot_total SET stock='".$rec_lot_row->stock."',
										purchase_price='".$purchase_price_existings[$loop_id]."',
										wholesale_price='".$wholesale_price_existings[$loop_id]."'
									   	WHERE id='".$lot_res['id']."'") or $sqlError[]=imw_error().' ln350';
						}
						else{
							 imw_query("INSERT INTO in_item_lot_total SET stock='".$rec_lot_row->stock."',
										purchase_price='".$purchase_price_existings[$loop_id]."',
										wholesale_price='".$wholesale_price_existings[$loop_id]."',
									   	lot_no='".$rec_lot_row->lot_no."', 
										item_id='".$itm_id."',
									   	loc_id='".$_SESSION['pro_fac_id']."'") or $sqlError[]=imw_error().' ln357';
						}
					}
				}
				/*End Add Update Batch Records for the Items*/
				
				$sql2 = imw_query("SELECT SUM(stock) AS 'qty_on_hand' FROM in_item_loc_total WHERE item_id='".$itm_id."'") or $sqlError[]=imw_error().' ln363';
				$qty_on_hand = "0";
				if($sql2){
					$qty_on_hand_res = imw_fetch_assoc($sql2);
					$qty_on_hand = $qty_on_hand_res['qty_on_hand'];
				}
				$sql3 = imw_query("SELECT retail_price FROM in_item WHERE id='".$itm_id."'") or $sqlError[]=imw_error().' ln369';
				$retail_price = 0;
				if($sql3){
					$retail_price_res = imw_fetch_assoc($sql3);
					$retail_price = $retail_price_res['retail_price'];
				}
				$amount = $retail_price*$qty_on_hand;
				$query=imw_query("update in_item set 
				qty_on_hand='".$qty_on_hand."', 
				amount='".$amount."', 
				wholesale_cost='".$wholesale_price_existings[$loop_id]."',
				retail_price_flag='".$retail_flags[$loop_id]."', 
				retail_price='".$retail_price_existings[$loop_id]."', 
				purchase_price='".$purchase_price_existings[$loop_id]."', 
				discount='".$discounts[$loop_id]."', 
				modified_date='".(date("Y-m-d"))."', 
				modified_time='".(date('h:i:s'))."', 
				modified_by='".$_SESSION["authId"]."' 
				where id='".$itm_id."'") or $sqlError[]=imw_error().' ln387';
			}
		}
		
		$batchHistory=array('user_id'=>$_SESSION["authId"],'action'=>'updated','date'=>$batch_date,"page"=>"Stock Reconciliation");
		$user_detail=imw_query("select * from in_batch_table where id='".$batch_id."'") or $sqlError[]=imw_error().' ln392';
		$user_data=imw_fetch_array($user_detail);
		$un_ar=json_decode($user_data['user_detail']);
		$un_ar[]=$batchHistory;
		$query2=imw_query("update in_batch_table set updated_date='".$batch_date."',status='updated', user_detail='".json_encode($un_ar)."' where id=".$batch_id."") or $sqlError[]=imw_error().' ln396';
		
		if($query2)
		{
			echo "<script type='text/javascript'>window.onload=function(){show_alert_div();}</script>";
		}
	}
	
}

if(sizeof($sqlError)>0){pre($sqlError);die('------------------------------');}
/*Get Facility Name*/
	$location_name = "";
	$resp_fac = imw_query("SELECT loc_name FROM in_location WHERE id='".$_SESSION["pro_fac_id"]."'");
	if($resp_fac && imw_num_rows($resp_fac)>0){
		$location_name_data = imw_fetch_object($resp_fac);
		$location_name = $location_name_data->loc_name;
	}
	$user_qry=imw_query("select * from users where id=".$_SESSION['authId']."");
  	$user_row=imw_fetch_array($user_qry);
	$user_name=$user_row['lname'].", ". $user_row['fname'];
/*End Get Facility Name*/

/*Resons List*/
$reason_arr = array();
$query5=imw_query("select id,reason_name from in_reason where del_status='0' order by reason_name");
while($sel_row5=imw_fetch_array($query5)){ 
	$reason_arr[$sel_row5['id']]=$sel_row5['reason_name'];
}

/*Item/Module Types List*/
$module_arr = array();
$query2=imw_query("select * from in_module_type");
while($row2=imw_fetch_array($query2)){
	$module_arr[$row2['id']]=$row2['module_type_name'];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->


<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-ui.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/dymo/DYMO.Label.Framework.latest.js?<?php echo constant("cache_version"); ?>"></script>
<script>
var new_lot_name='<?php echo date('Ymd').$_SESSION["authId"];?>';
var new_lot_counter=1;
var searchUPc = new Array();
$(document).ready(function(){
	var val="";
	var sr_no="";
	$("#selectall").click(function(){		
		if($(this).is(":checked")){
			$(".print_item").prop('checked', true);
		}else{
			$(".print_item").prop('checked', false);
		}
	});
	
	$("#recon_form").submit(function(event){
		
		$(".highlighted").removeClass('highlighted');
		event.preventDefault();
	 	val = $.trim($("#scan_image").val());
		 if(val!=="")
		 {
			 /*Flag Indocator for New Lot/Batch for the Item to be added*/
			 var newStock = parseInt($('#new_stock').val());
			 
			/* if($.inArray(val, searchUPc)!= -1){
				 $('.err_div').css("display","none");
				 var id = val.replace(" ", "_");
				 var prevVal = parseInt($("#"+id).val());
				 var nval = prevVal+1;
				 
				 var prevNSval = parseInt($('#'+id+'_new').val());
				 var nNSval = prevNSval;
				 var prevPSval = parseInt($('#'+id+'_prev').val());
				 var nPSval = prevPSval;
				 
				 var itemId = $('tr#'+id+'_r>td:first-child').children('input[name="item_id[]"]').val();
				 
				 if(newStock || itemId=="new"){
					 nNSval = prevNSval+1;
					 $('#'+id+'_new').val(nNSval);
				 }
				 else{
					 nPSval = prevPSval+1;
					 $('#'+id+'_prev').val(nPSval);
				 }
				 
				 $("#"+id).val(nval).attr('title', 'Previous Stock: '+nPSval+'\nNew Stock: '+nNSval).addClass('highlighted');
				 $("#"+id).focus();
			 }
			 else{*/
				$.ajax({type:"POST",
						url:"bar_code.php",
						data:"bar_code="+val,
						cache:false,
						beforeSend: function(){
							$("#loading").show();
						},
						success: function(data){
							
							data = $.parseJSON(data);
							
							var keysCount = Object.keys(data).length;
						/*Make Table Row*/
						
							/*If item found in Inventory*/
								if(keysCount>0 && data.stock){
								var row_id=(data.upc).replace(' ', '_')+"_r_"+data.lot_no;	
								
								if($("#"+row_id).length>0)
								{
									$('.err_div').css("display","none");
									var id = (data.upc).replace(' ', '_')+data.lot_no;	
									 var prevVal = parseInt($("#"+id).val());
									 var nval = prevVal+1;

									 var prevNSval = parseInt($('#'+id+'_new').val());
									 var nNSval = prevNSval;
									 var prevPSval = parseInt($('#'+id+'_prev').val());
									 var nPSval = prevPSval;

									 var itemId = $('tr#'+row_id+'>td:first-child').children('input[name="item_id[]"]').val();

									 if(newStock || itemId=="new"){
										 nNSval = prevNSval+1;
										 $('#'+id+'_new').val(nNSval);
									 }
									 else{
										 nPSval = prevPSval+1;
										 $('#'+id+'_prev').val(nPSval);
									 }

									 $("#"+id).val(nval).attr('title', 'Previous Stock: '+nPSval+'\nNew Stock: '+nNSval).addClass('highlighted');
									 $("#"+id).focus();
								}
								else
								{
									/*New Table Row*/
									//var row = $('<tr></tr>').attr('id', (data.upc).replace(' ', '_')+"_r"); //backup
									var row = $('<tr></tr>').attr('id', row_id);

									/*print checkbox*/
									var chkbox=$('<td></td>');
									$('<input>').attr( {type:'checkbox', class: 'print_item', name: 'print_item[]', value: data.upc} ).appendTo(chkbox);
									$(row).append(chkbox);

									/*<td> for upc_code and hidden fields*/
									var upc = $('<td title="#lot:'+data.lot_no+'"></td>').html(data.upc);

									/*Upc Container*/
									$('<input>').attr( {type:'hidden', name: 'upc_code[]', value: data.upc} ).appendTo(upc);
									/*lot Container*/
									$('<input>').attr( {type:'hidden', name: 'lot_no[]', value: data.lot_no} ).appendTo(upc);
									/*Batch Record Counter*/
									$('<input>').attr( {type:'hidden', name: 'in_bat_rec_id[]', value: 0} ).appendTo(upc);
									/*Total Stock*/
									$('<input>').attr( {type:'hidden', name: 'tot_qnt[]', value: data.qty} ).appendTo(upc);
									/*Stock at Facility*/
									$('<input>').attr( {type:'hidden', name: 'fac_quant[]', value: data.fac_qty} ).appendTo(upc);
									/*Stock at Facility lot wise*/
									$('<input>').attr( {type:'hidden', name: 'fac_lot_quant[]', value: data.fac_lot_qty} ).appendTo(upc);
									/*Unique Item Id*/
									$('<input>').attr( {type:'hidden', name: 'item_id[]', value: data.id} ).appendTo(upc);
									/*Item Module Type Id*/
									$('<input>').attr( {type:'hidden', name: 'module_type[]', class: 'module_type',
														value: data.mod_id} ).appendTo(upc);
									/*Item Name*/
									$('<input>').attr( {type:'hidden', name: 'prod_name[]', value: data.name} ).appendTo(upc);

									/*Retails price flg, calculated/fixed*/
									$('<input>').attr( {type:'hidden', name: 'retail_price_flag[]', value: data.retail_price_flag, class:'retail_flag'} ).appendTo(upc);

									/*Purchase price flg, calculated/fixed*/
									$('<input>').attr( {type:'hidden', name: 'purchase_price_flag[]', value: data.purchase_price_flag, class:'purchase_flag'} ).appendTo(upc);

									/*Fields for New Item to be added from Stock Reconciliation - Added here to supoorting functionality only*/
									/*Manufacturer Id*/
									$('<input>').attr( {type:'hidden', name: 'prod_manuf[]', value: 0} ).appendTo(upc);
									/*Vendor Id*/
									$('<input>').attr( {type:'hidden', name: 'prod_vendor[]', value: 0} ).appendTo(upc);
									/*Vendor Id*/
									$('<input>').attr( {type:'hidden', name: 'prod_brand[]', value: 0} ).appendTo(upc);
									/*Wholesale price*/
									$('<input>').attr( {type:'hidden', name: 'prod_wholesale[]', value: 0} ).appendTo(upc);
									/*Purchase price*/
									$('<input>').attr( {type:'hidden', name: 'prod_purchase[]', value: 0} ).appendTo(upc);
									/*Retail price*/
									$('<input>').attr( {type:'hidden', name: 'prod_retail[]', value: 0} ).appendTo(upc);

									/*Add UPC to Row*/
									$(row).append(upc);

									/*Module Name*/
									$('<td></td>').text(data.mod_name).appendTo(row);

									$('<td></td>').text(data.name).appendTo(row);
									$('<td></td>').html(data.size).appendTo(row);
									$('<td></td>').text(data.brand).appendTo(row);
									$('<td></td>').text(data.color).appendTo(row);
									$('<td></td>').text(data.style).appendTo(row);

									/*Discount*/
									var discVal = $('<td>');
									$('<input>').attr( {type:'text', name:'discount[]', id: (data.upc).replace(' ', '_')+data.lot_no+'_disc',
															class:'disc_input', value:data.discount} ).appendTo(discVal);
									$(row).append(discVal);

									/*Wholesale Price*/
									var wpriceVal = $('<td>');
									$('<input>').attr( {type:'text', name:'wholesale_price_exis[]', id: (data.upc).replace(' ', '_')+data.lot_no+'_wPriceExis',
															class:'wprice_input', value:data.wholesale_price} ).appendTo(wpriceVal);
									$(row).append(wpriceVal);

									/*Retail Price*/
									var rpriceVal = $('<td>');
									$('<input>').attr( {type:'text', name:'retail_price_exis[]', id: (data.upc).replace(' ', '_')+data.lot_no+'_rPriceExis',
															class:'rprice_input', value:data.retail_price, onChange:"retailPriceChanged('"+row_id+"')"} ).appendTo(rpriceVal);
									$(row).append(rpriceVal);

									/*Purchase Price*/
									var ppriceVal = $('<td>');
									$('<input>').attr( {type:'text', name:'purchase_price_exis[]', id: (data.upc).replace(' ', '_')+data.lot_no+'_pPriceExis',
															class:'pprice_input', value:data.purchase_price} ).appendTo(ppriceVal);
									$(row).append(ppriceVal);

									//$('<td></td>').text(data.discount).appendTo(row);
									//$('<td></td>').text(data.retail_price).appendTo(row);


									$('<td></td>').text(data.fac_lot_qty).appendTo(row);

									/*<td> Reconciled Quantity Counter*/
									var recQty = $('<td>');
									var qty_id=(data.upc).replace(' ', '_')+data.lot_no
									$('<input>').attr( {type:'text', name:'item_quan[]', id: qty_id,
														class:'quant_input highlighted numberOnly', value:1, readonly:true} ).appendTo(recQty);
									$('<input>').attr( {type:'hidden', name:'item_quan_prev_batch[]',
														id: qty_id+'_prev', class:'quant_input_prev',
														value:(!newStock)?1:0} ).appendTo(recQty);
									$('<input>').attr( {type:'hidden', name:'item_quan_new_batch[]',
														id: qty_id+'_new', class:'quant_input_new',
														value:(newStock)?1:0} ).appendTo(recQty);
									/*Add Reconciled Quantity to Row*/
									$(row).append(recQty);

									/*<td> for Reasons DD*/
									var reasonTd = $('<td>');
									/*<select> for Options*/
									var reasonSel = $('<select></select>').attr( {name:'resn_sel[]', class:'reason_sel'} ).css( {
																			height:'23px', width:'80px'} );
									/*Add Blank Option to Select*/
									$('<option></option>').attr('value', '0').text('Select').appendTo(reasonSel);

									/*Append Reason Options*/
									$.each(reasons, function(index, value){
										$('<option></option>').attr('value', value).text(index).appendTo(reasonSel);
									});
									/*Append Select to <td>*/
									$(reasonTd).append(reasonSel);
									/*End <select> for Options*/
									/*Add Reasons DD to Row*/
									$(row).append(reasonTd);


									/*Push Row to Table*/
									$("#TestTable").prepend(row);

									searchUPc.push(data.upc);
								}
							}
							else{
								/*Option to Add New Item if Upc not found in Inventory*/
								$('#nItemUpc').val(val);
								$('#newStock').show();
							}
							
						/*End Table Row*/
							
							//;
							$('#show_con').css("display","block");
							$('#back_btn').css("display","none");
							//$("#TestTable").prepend(msg);
							//$('#sr_no').html(i);
							
							//BUTTONS
							/*var mainBtnArr = new Array();
							mainBtnArr[0] = new Array("frame","Back","top.main_iframe.admin_iframe.reload_page();");
							mainBtnArr[1] = new Array("frame","Save","top.main_iframe.admin_iframe.save_rec();");
							mainBtnArr[2] = new Array("frame","Save & Reconcile","top.main_iframe.admin_iframe.submit_form();");
							mainBtnArr[3] = new Array("frame","Print Labels","top.main_iframe.admin_iframe.print();");
							top.btn_show("admin",mainBtnArr);	*/	
							$("#action_buttons").show();
						},
						complete: function(){
							$("#loading").hide();
						}
					});
				/* }*/
		 }
		 else
		 {
			$('.err_div').css({"display":"block","color":"#F00","width":"200px","position":"absolute","left": "320px","top":"-5px"});
			$(".err_div").html("Please Enter UPC Code");
			$('.err_div').delay(3000).hide(10);
		 }
		$("#scan_image").val("").focus();
	});
	

	//BUTTONS
	<?php if($_REQUEST['batch']==''){?>
		$("#action_buttons").hide();
	<?php }?>
	<?php if($_REQUEST['status']=='updated'){?>
		
		$("#action_buttons").show();
		/*var mainBtnArr = new Array();
		mainBtnArr[0] = new Array("frame","Back","top.main_iframe.admin_iframe.reload_page();");
		mainBtnArr[1] = new Array("frame","Print","top.main_iframe.admin_iframe.print_batch();");
		mainBtnArr[2] = new Array("frame","Print Labels","top.main_iframe.admin_iframe.print();");
		top.btn_show("admin",mainBtnArr);*/	
    <?php }?>
	
});

function retailPriceChanged(row_id){
	var flag = $('#'+row_id).find('.retail_flag');
	if ( flag.length > 0)
	{
		flag = flag[0];
		$( flag ).val(1);
	}
}
	
//function purchasePriceChanged(row_id){
//	var flag = $('#'+row_id).find('.purchase_flag');
//	if ( flag.length > 0)
//	{
//		flag = flag[0];
//		$( flag ).val(1);
//	}
//}
	
	
function submit_form(){
	//document.quan_form.save_qnty.click();
	document.getElementById('save_qnty').click();
}

function save_rec()
{
	
	var msg="";
	var batch_id="";
	var len=document.getElementsByName('upc_code[]').length;
	var upc_code=document.getElementsByName('upc_code[]');
	var lot_no=document.getElementsByName('lot_no[]');
	var item_id=document.getElementsByName('item_id[]');
	var item_qua=document.getElementsByName('item_quan[]');
	var fac_qt=document.getElementsByName('fac_quant[]');
	var fac_lot_qt=document.getElementsByName('fac_lot_quant[]');
	var bat_rec_id_arr=document.getElementsByName('in_bat_rec_id[]');
	var tot_qty=document.getElementsByName('tot_qnt[]');
	var res=document.getElementsByName('resn_sel[]');
	var prod_name = document.getElementsByName('prod_name[]');
	var module_type = document.getElementsByName('module_type[]');
	var retail_price_flag = document.getElementsByName('retail_price_flag[]');
	var purchase_price_flag = document.getElementsByName('purchase_price_flag[]');
	var discount = document.getElementsByName('discount[]');
	var wholesale_price_existing = document.getElementsByName('wholesale_price_exis[]');	/*Retail for existing item only*/
	var retail_price_existing = document.getElementsByName('retail_price_exis[]');	/*Retail for existing item only*/
	var purchase_price_existing = document.getElementsByName('purchase_price_exis[]');	/*purchase for existing item only*/
	
	/*Fiels only for New Item to be added from Stock Reconciliation*/
	var manufacturers_fields	= document.getElementsByName('prod_manuf[]');
	var vendors_fields			= document.getElementsByName('prod_vendor[]');
	var brands_fields			= document.getElementsByName('prod_brand[]');
	var wholesale_price_fields	= document.getElementsByName('prod_wholesale[]');
	var retail_price_fields		= document.getElementsByName('prod_retail[]');
	var purchase_price_fields	= document.getElementsByName('prod_purchase[]');
	/*Fields for Frame Only*/
	var ed_fields				= document.getElementsByName('prod_ed[]');
	var dbl_fields				= document.getElementsByName('prod_dbl[]');
	var temple_fields			= document.getElementsByName('prod_temple[]');
	
	var previous_stock_fields	= document.getElementsByName('item_quan_prev_batch[]');
	var new_stock_fields		= document.getElementsByName('item_quan_new_batch[]');
	
	if(document.getElementById('batch_id_field'))
	{
		batch_id=document.getElementById('batch_id_field').value;
	}
	var upc="";
	var lot="";
	var item_det ="";
	var item_qn="";
	var bat_rec_id="";
	var reason="";
	var tot="";
	var fac_qty="";
	var fac_lot_qty="";
	var prod_names="";
	var module_types="";
	var retail_price_flags="";
	var purchase_price_flags="";
	var discounts="";
	var wholesale_price_existings="";
	var retail_price_existings="";
	var purchase_price_existings="";
	
	/*Fiels only for New Item to be added from Stock Reconciliation*/
	var manufacturers	= "";
	var vendors			= "";
	var brands			= "";
	var wholesale_price	= "";
	var retail_price	= "";
	var purchase_price	= "";
	/*Frame Only*/
	var ed				= "";
	var dbl				= "";
	var temple			= "";
	
	var previous_stock	= "";
	var new_stock		= "";
	for(i=0;i<len;i++)
	{
		upc+=upc_code[i].value+",";
		lot+=lot_no[i].value+",";
		item_det+=item_id[i].value+",";
		item_qn+=item_qua[i].value+",";
		reason+=res[i].value+",";
		fac_qty+=fac_qt[i].value+",";
		fac_lot_qty+=fac_lot_qt[i].value+",";
		tot+=tot_qty[i].value+",";
		prod_names+=prod_name[i].value+",";
		module_types+=module_type[i].value+",";
		if(typeof(bat_rec_id_arr[i])!="undefined"){
			bat_rec_id+=bat_rec_id_arr[i].value+",";
		}
		else{
			bat_rec_id+=0;
		}
		
		if(typeof(discount[i])!="undefined"){discounts+=discount[i].value+",";}else{discounts+=',';}
		
		if(typeof(wholesale_price_existing[i])!="undefined"){ wholesale_price_existings+=wholesale_price_existing[i].value+",";} 
		else{wholesale_price_existings+=',';}
		
		if(typeof(retail_price_flag[i])!="undefined"){retail_price_flags+=retail_price_flag[i].value+",";}else{retail_price_flags+=',';}
		if(typeof(retail_price_existing[i])!="undefined"){retail_price_existings+=retail_price_existing[i].value+",";}else{retail_price_existings+=',';}
		
		if(typeof(purchase_price_flag[i])!="undefined"){purchase_price_flags+=purchase_price_flag[i].value+",";}else{purchase_price_flags+=',';}
		if(typeof(purchase_price_existing[i])!="undefined"){purchase_price_existings+=purchase_price_existing[i].value+",";}else{purchase_price_existings+=',';}
		
		
		if(typeof(manufacturers_fields[i])!="undefined"){manufacturers+=manufacturers_fields[i].value+",";}else{manufacturers+=',';}
		if(typeof(vendors_fields[i])!="undefined"){vendors+=vendors_fields[i].value+",";}else{vendors+=',';}
		if(typeof(brands_fields[i])!="undefined"){brands+=brands_fields[i].value+",";}else{brands+=',';}
		if(typeof(wholesale_price_fields[i])!="undefined"){wholesale_price+=wholesale_price_fields[i].value+",";}else{wholesale_price+=',';}
		if(typeof(retail_price_fields[i])!="undefined"){retail_price+=retail_price_fields[i].value+",";}else{retail_price+=',';}
		if(typeof(purchase_price_fields[i])!="undefined"){purchase_price+=purchase_price_fields[i].value+",";}else{purchase_price+=',';}
		/*Frame Only*/
		if(typeof(ed_fields[i])!="undefined"){ed+=ed_fields[i].value+",";}else{ed+=',';}
		if(typeof(dbl_fields[i])!="undefined"){dbl+=dbl_fields[i].value+",";}else{dbl+=',';}
		if(typeof(temple_fields[i])!="undefined"){temple+=temple_fields[i].value+",";}else{temple+=',';}
		
		if(typeof(previous_stock_fields[i])!="undefined"){previous_stock+=previous_stock_fields[i].value+",";}else{previous_stock+=',';}
		if(typeof(new_stock_fields[i])!="undefined"){new_stock+=new_stock_fields[i].value+",";}else{new_stock+=',';}
	}
	$(document).ready(function() {
        $.ajax({type:"POST",
				url:"data_save.php",
				data:"upc_code="+upc+"&lot_no="+lot+"&item_id="+item_det+"&item_quan="+item_qn+"&batch="+batch_id+"&bat_rec_id="+bat_rec_id+"&resaon="+reason+"&fac_quan="+fac_qty+"&fac_lot_quan="+fac_lot_qty+"&tot_qnty="+tot+"&prod_name="+prod_names+"&module_type="+module_types+"&manfacturers="+manufacturers+"&vendors="+vendors+"&brands="+brands+"&wholesale_price="+wholesale_price+"&purchase_price="+purchase_price+"&retail_price="+retail_price+"&ed="+ed+"&dbl="+dbl+"&temple="+temple+"&previous_stock="+previous_stock+"&new_stock="+new_stock+"&retail_flags="+retail_price_flags+"&purchase_flags="+purchase_price_flags+"&discounts="+discounts+"&wholesale_price_existings="+wholesale_price_existings+"&retail_price_existings="+retail_price_existings+"&purchase_price_existings="+purchase_price_existings,
				cache:false,
				beforeSend: function(){
					$("#loading").show();
				},
				success: function()
				{
					/*$('.err_div').css({"display":"block","color":"#F00","width":"200px","position":"absolute","left": "430px","top":"-5px"});
					$(".err_div").html("Data Saved Successfully");
					$('.err_div').delay(3000).hide(1);*/
					//window.opener.top.document.getElementById('admin_iframe').contentWindow.location.href='index.php?batch_status=Saved';
					
					try{
						window.opener.location.href="index.php?batch_status=Saved";
						window.opener.$("#loading").show();
					}catch(err)
					{
						//opener window not found 
					}
					self.close();
				},
				complete: function(){
					$("#loading").hide();
				}
			});
    });
}

function load_batches()
{
	window.opener.top.WindowDialog.closeAll();
	var Add_new_popup=window.opener.top.WindowDialog.open('Add_new_popup','stock_pop_up.php','stock_batches','width=1000,height=500,left=200,scrollbars=no,top=50');
	Add_new_popup.focus();
}
function show_div()
{
	$('#show_con').css("display","block");
	document.getElementById('scan_image').focus();	
	
	$("#action_buttons").show();
	//BUTTONS
	/*var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Back","top.main_iframe.admin_iframe.reload_page();");
	mainBtnArr[1] = new Array("frame","Save","top.main_iframe.admin_iframe.save_rec();");
	mainBtnArr[2] = new Array("frame","Save & Reconcile","top.main_iframe.admin_iframe.submit_form();");
	mainBtnArr[3] = new Array("frame","Print Labels","top.main_iframe.admin_iframe.print();");
	top.btn_show("admin",mainBtnArr);	*/	
}
function show_alert_div(a)
{
	//window.location.href="index.php?batch_status=Updated";	
	try{
		window.opener.location.href="index.php?batch_status=Updated";
		window.opener.$("#loading").show();
	}catch(err)
	{
		//opener window not found 
	}
	window.self.close()
}
function alert_msg_update()
{
	top.falert("You can't Update records now");
}
function get_batch(batch_id,status)
{
	//window.location.href="index.php?batch="+batch_id+"&status="+status;
	try{
		window.opener.location.href="index.php?batch="+batch_id+"&status="+status;
		window.opener.$("#loading").show();
	}catch(err)
	{
		//opener window not found 
	}
	window.self.close()
}
function show_recon()
{
	document.getElementById('batches_div').style.display="none";
	document.getElementById('search_btn').removeAttribute("disabled");
	document.getElementById('new_stock_btn').removeAttribute("disabled");
	document.getElementById('scan_image').removeAttribute("disabled");
	//document.getElementById('back_btn').style.display="block";
	//BUTTONS
	/*var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Back","top.main_iframe.admin_iframe.reload_page();");
	top.btn_show("admin",mainBtnArr);*/	
	
	document.getElementById('scan_image').focus();
}
function reload_page(){
	//window.location.href=WEB_PATH+'/interface/admin/stock_reconc/index.php';
	try{
		window.opener.location.href=WEB_PATH+'/interface/admin/stock_reconc/index.php';
		window.opener.$("#loading").show();
	}catch(err)
	{
		//opener window not found 
	}
	window.self.close();
}
function load_batches()
{
	//window.location.href="index.php";
	try{
		window.opener.location.reload();
		window.opener.$("#loading").show();
	}catch(err)
	{
		//opener window not found 
	}
	
	window.self.close()
}
function print_batch()
{
	var upc_len=document.getElementsByName('upc_code[]').length;
	var batch=document.getElementsByName('in_bat_rec_id[]');
	var items=document.getElementsByName('item_id[]');
	var upc_cs=document.getElementsByName('upc_code[]');
	var batch_id=document.getElementById('batch_id_field').value;
	var item_id="";
	var upc=""
	var data="";
	var data1="";
	var data2="";
	for(i=0;i<upc_len;i++)
	{
		data+=(items[i].value)+",";
		data1+=(upc_cs[i].value)+",";
		data2+=(batch[i].value)+",";
	}
	$.ajax({
		type:"POST",
		url:"batch_print.php",
		data:"upc="+data1+"&items="+data+"&batch="+data2+"&batch_id="+batch_id,
		beforeSend: function(){
			$("#loading").show();
		},
		success: function(msg)
		{
			var url='<?php echo $GLOBALS['WEB_PATH']?>/library/new_html2pdf/createPdf.php?op=l&file_name=batch_data';
			//var url='<?php echo $GLOBALS['WEB_PATH']?>/library/new_html2pdf/batch_data.html';
			window.opener.top.WindowDialog.closeAll();
			var Add_new_popup=window.opener.top.WindowDialog.open('Add_new_popup',url);
			$("#loading").hide();
		}
	});
}
function reason_sel(b)
{
	$('.reason_sel').val(b);
}
function adv_reason_sel(b)
{
	$('.adv_reason_sel .reason_sel').val(b);
}
$(document).ready(function(e) {
	var type_id =$("#type_optical_id").val();
	get_type_manufacture1(type_id,'0');
});
</script>
<style>
	.printing_upc, .printing_data{
	display:none;
}

.btn_cls1 {
	position: absolute;
	margin: auto;
	bottom: 0;
	width: 98%;
}
.batches_div a {
	text-decoration: none;
}
.batch_div_msg {
	float: right;
	width: 590px;
	margin: 0 0 0 0;
	color: #0D6030;
	font-weight: bold;
}
.head_tab th {
	border-right: 1px solid #E8E8E8;
}
.disc_input, .pprice_input, .rprice_input, .wprice_input{width: 50px;}
/*New Stock Style*/
#newStock{width: 100%; height: 100%; display: block; position: absolute; background-color: rgba(0,0,0,0.4);}
.stockdiv{background-color:#fff; height:54%; width:95%; margin:0 auto; top:4%; position:relative; border-radius:7px;box-shadow:0px 0px 2px 1px #FFF;}
.itemOptions>table{width:100%}
.itemOptions>table tr.bgCol{background-color:#EEE}
.itemOptions>table tr>td:nth-child(odd){width:150px;padding-left:10px;}
.hide{display:none;}
.nbtnDiv{bottom:0px;position:absolute;width:93.2%;}
.nHide{float:right;margin-right:10px;margin-top:0px;background:#eee;padding:5px;border-radius:20px;cursor:pointer;}
.errMsg{color:#f00;display:none;}
#advancedSearch{width: 100%; height: 100%; display: block; position: absolute; background-color: rgba(0,0,0,0.4);}

#print_label_div{width: 80%; left:8%; display: block; position: absolute; background-color: rgba(0,0,0,0.4);}
.resultdiv{background-color:#fff; height:88%; width:95%; margin:0 auto; top:4%; position:relative; border-radius:7px;box-shadow:0px 0px 2px 1px #FFF;}
select[disabled]{background-color:rgb(235,235,228);}
	
</style>
</head>
<body>

<!-- Add New Inventory Container -->
<div id="newStock" style="display:none;">
	<div class="stockdiv">
		<div class="listheading" style="border-radius:2px;padding-left:10px;background-size:3.5px;height:26px;">Add New Item
			<img class="nHide" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="$('#newStock').hide();clearNform();" />
		</div>
		<div class="itemOptions">
			<table>
				<tr>
					<td><label for="nItemUpc">UPC</label></td>
					<td>
						<input type="text" id="nItemUpc" style="width:192.5px;background-color:rgb(235,235,228);" readonly />
						<span class="errMsg">UPC code should not be blank</span>
					</td>
					<td><label for="nItemName">Item Name</label></td>
					<td>
						<input type="text" id="nItemName" style="width:192.5px" />
						<span class="errMsg">Please enter Item Name</span>
					</td>
					<td><label for="nModType">Item Type</label></td>
					<td>
						<select id="nModType" onChange="mod_manufacturers('manufacturer', this.value)" style="width:200px">
							<option value="0">Please Select</option>
<?php					foreach($module_arr as $key=>$value){ ?>
							<option value="<?php echo $key; ?>"><?php echo ucwords($value); ?></option>
<?php					} ?>
						</select>
						<span class="errMsg">Pleae Select the Item Type</span>
					</td>
				</tr>
				<tr class="bgCol">
					<td><label for="nManuf">Manufacturer</label></td>
					<td>
						<select id="nManuf" onChange="mod_manufacturers('vendor', this.value);mod_manufacturers('brands', this.value);" style="width:200px"></select>
					</td>
					<td><label for="nVendor">Vendor</label></td>
					<td>
						<select id="nVendor" style="width:200px"></select>
					</td>
					<td><label for="nBrand">Brand</label></td>
					<td>
						<select id="nBrand" style="width:200px" disabled></select>
					</td>
				</tr>
				<tr>
					<td><label for="nItemWholesale">Wholesale Price</label></td>
					<td>
						<input type="text" id="nItemWholesale" style="width:192.5px" onChange="convert_float(this);" />
					</td>
					<td><label for="nItemPurchase">Purchase Price</label></td>
					<td>
						<input type="text" id="nItemPurchase" style="width:192.5px" onChange="convert_float(this);" />
					</td>
					<td><label for="nItemRetail">Retail Price</label></td>
					<td>
						<input type="text" id="nItemRetail" style="width:192.5px" onChange="convert_float(this);" />
					</td>
				</tr>
				<tr class="bgCol hide" id="nFramesFields">
					<td><label for="nItemEd">ED</label></td>
					<td>
						<input type="text" id="nItemEd" style="width:192.5px" />
					</td>
					<td><label for="nItemDbl">DBL</label></td>
					<td>
						<input type="text" id="nItemDbl" style="width:192.5px" />
					</td>
					<td><label for="nItemTemple">Temple</label></td>
					<td>
						<input type="text" id="nItemTemple" style="width:192.5px" />
					</td>
				</tr>
			</table>
		</div>
		<div class="btn_cls nbtnDiv">
			<input type="button" class="dff_button" id="nInsert" value="Submit" onClick="addNewItem();"/>
		</div>
	</div>
</div>
<!-- Emnd Add New Inventory Container -->

<div id="advancedSearch" style="display:none;">
	<div class="resultdiv">
		<div class="listheading" style="border-radius:2px;padding-left:10px;background-size:3.5px;height:26px;">Advanced Search
			<img class="nHide" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="$('#advance_srh_result_div').empty();$('#advancedSearch').hide();" />
		</div>
		<div class="itemOptions" style="height:100%;">
			<div style="padding:0px 0px 10px 10px;">
				<span class="text14"><strong>Type</strong></span>
				<span>
					<select name="type_optical_id" id="type_optical_id" style="width:200px;" onChange="javascript:get_type_manufacture1(this.value,'0');">
                        <?php  $rowsType="";
                          $rowsType = data("select * from in_module_type order by module_type_name asc");
                          foreach($rowsType as $rsultType)
                          { 
						  if($rsultType['module_type_name']=="medicine" || $rsultType['module_type_name']=="supplies" || $rsultType['module_type_name']=="accessories"){ $style_upc="126";}else{$style_upc="90";}
						  ?>
                            <option value="<?php echo $rsultType['id']; ?>" <?php if($rsultType['id']==$search_id) { echo "selected"; }?>><?php echo ucfirst($rsultType['module_type_name']); ?></option>	
                    	<?php }	?>
                    </select>
				</span>
				<span class="text14" style="padding-left:10px;"><strong>Manufacturer</strong></span>
				<span>
					<select name="manufacturer_Id_Srch" id="manufacturer_Id_Srch" style="width:200px;" onChange="get_vendorFromManufacturer1(this.value,'0');">
                    	<option value="0">Select Manufacturer</option>
                    </select>
				</span>
				<span class="text14" style="padding-left:10px;"><strong>Vendor</strong></span>
				<span>
					<select name="opt_vendor_id" id="opt_vendor_id" style="width:200px;">
                    	<option value="0">Select Vendor</option>
                    </select>
				</span>
				<span style="padding-left:20px;" class="btn_cls">
					<input type="button" class="dff_button" id="item_srh" value="Search" onClick="advance_srh_result();"/>
				</span>
			</div>
			<div style="overflow-y:scroll; width:99.7%; display:none; height:75%;" id="advance_srh_result_div">
			</div>
		</div>
		<div class="btn_cls nbtnDiv">
			<input type="button" class="dff_button" id="nInsert" value="Done" onClick="set_new_rec();"/>
		</div>
	</div>
</div>
<script type="text/javascript">
/*Add New Item to the Batch*/
function addNewItem(){
	
	$('.errMsg').hide();
	
	var upc_code		= $('#nItemUpc').val();
	var item_name		= $('#nItemName').val();
	var item_type		= $('#nModType').val();
	var manufacturer	= $('#nManuf').val();
	var vendor			= $('#nVendor').val();
	var brand			= $('#nBrand').val();
	var wholesale_price	= $('#nItemWholesale').val();
	var retail_price	= $('#nItemRetail').val();
	var purchase_price	= $('#nItemPurchase').val();
	/*Frame Only*/
	var ed				= $('#nItemEd').val();
	var dbl				= $('#nItemDbl').val();
	var temple			= $('#nItemTemple').val();
	/*End Frame Only*/
	var fsize="--"+temple;
	/*fsize+="<table class='' style='width:100%;'>";
	fsize+="<tr>";
	fsize+="<td width='25%'><strong>A</strong></td>";
	fsize+="<td width='25%'><strong>B</strong></td>";
	fsize+="<td width='25%'><strong>ED</strong> "+ed+"</td>";
	fsize+="<td width='25%'><strong>DBL</strong> "+dbl+"</td>";
	fsize+="</tr></table>";
	fsize+="<table style='width:100%;'>";
	fsize+="<tr>";
	fsize+="<td width='33%'><strong>Temple</strong> "+temple+"</td>";
	fsize+="<td width='33%'><strong>Bridge</strong> </td>";
	fsize+="<td width='auto'><strong>FPD</strong> </td>";
	fsize+="</tr>";
	fsize+="</table>";*/
	var error = false;
	
	/*Check Mendatory Data*/
	if(upc_code==""){
		$('#nItemUpc').next('.errMsg').show();
		error = true;
	}
	if(item_name==""){
		$('#nItemName').next('.errMsg').show();
		error = true;
	}
	if(item_type=="0"){
		 $('#nModType').next('.errMsg').show();
		 error = true;
	}
	
	/*Return if any Required field is missing*/
	if(error)
		return;
	
	/*Add New Row to the Batch in Progress for New Item to be added in Inventory on Saving the batch*/
	var new_lot=new_lot_name+new_lot_counter;
	new_lot_counter++;
	/*New Table Row*/
	var row = $('<tr></tr>').attr('id', upc_code.replace(' ', '_')+"_r"+new_lot);
	
	/*print checkbox*/
	var chkbox=$('<td></td>');
	$('<input>').attr( {type:'checkbox', class: 'print_item', name: 'print_item[]', value: upc_code} ).appendTo(chkbox);
	$(row).append(chkbox);
	
	/*<td> for upc_code and hidden fields*/
	var upc = $('<td title="#lot:'+new_lot+'"></td>').html(upc_code);
	/*Upc Container*/
	$('<input>').attr( {type:'hidden', name: 'upc_code[]', value: upc_code} ).appendTo(upc);
	/*lot_no Container*/
	
	$('<input>').attr( {type:'hidden', name: 'lot_no[]', value: new_lot} ).appendTo(upc);
	/*Batch Record Counter*/
	$('<input>').attr( {type:'hidden', name: 'in_bat_rec_id[]', value: 0} ).appendTo(upc);
	/*Total Stock*/
	$('<input>').attr( {type:'hidden', name: 'tot_qnt[]', value: 0} ).appendTo(upc);
	/*Stock at Facility*/
	$('<input>').attr( {type:'hidden', name: 'fac_quant[]', value: 0} ).appendTo(upc);
	/*Stock at Facility lot wise*/
	$('<input>').attr( {type:'hidden', name: 'fac_lot_quant[]', value: 0} ).appendTo(upc);
	/*Unique Item Id*/
	$('<input>').attr( {type:'hidden', name: 'item_id[]', value: 'new'} ).appendTo(upc);
	/*Item Module Type Id*/
	$('<input>').attr( {type:'hidden', name: 'module_type[]', class: 'module_type',
						value: item_type} ).appendTo(upc);
	/*Item Name*/
	$('<input>').attr( {type:'hidden', name: 'prod_name[]', value: item_name} ).appendTo(upc);

	/*Retails price flg, Default modified for New Item*/
	$('<input>').attr( {type:'hidden', name: 'retail_price_flag[]', value: 1, class:'retail_flag'} ).appendTo(upc);

	/*Purchase price flg, Default modified for New Item*/
	$('<input>').attr( {type:'hidden', name: 'purchase_price_flag[]', value: 1, class:'purchase_flag'} ).appendTo(upc);
		
	/*Fields for New Item to be added from Stock Reconciliation*/
	/*Manufacturer Id*/
	$('<input>').attr( {type:'hidden', name: 'prod_manuf[]', value: manufacturer} ).appendTo(upc);
	/*Vendor Id*/
	$('<input>').attr( {type:'hidden', name: 'prod_vendor[]', value: vendor} ).appendTo(upc);
	/*Vendor Id*/
	$('<input>').attr( {type:'hidden', name: 'prod_brand[]', value: brand} ).appendTo(upc);
	/*Wholesale price for New Item*/
	$('<input>').attr( {type:'hidden', name: 'prod_wholesale[]', value: wholesale_price} ).appendTo(upc);
	/*Purchase price for New Item Batch*/
	$('<input>').attr( {type:'hidden', name: 'prod_purchase[]', value: purchase_price} ).appendTo(upc);
		
	/*Fields For Frames only*/
	/*ED*/
	$('<input>').attr( {type:'hidden', name: 'prod_ed[]', value: ed} ).appendTo(upc);
	/*DBL*/
	$('<input>').attr( {type:'hidden', name: 'prod_dbl[]', value: dbl} ).appendTo(upc);
	/*Temple*/
	$('<input>').attr( {type:'hidden', name: 'prod_temple[]', value: temple} ).appendTo(upc);
	/*End Fields For Frames only*/
		
	/*Add UPC to Row*/
	$(row).append(upc);
	
	/*Module Name*/
	$('<td></td>').text($('#nModType>option[value="'+item_type+'"]').text()).appendTo(row);
	$('<td></td>').text(item_name).appendTo(row);
	$('<td></td>').html(fsize).appendTo(row);
	$('<td></td>').text($('#nBrand>option[value="'+brand+'"]').text()).appendTo(row);
	$('<td></td>').text('').appendTo(row);
	$('<td></td>').text('').appendTo(row);
	
	/*Discount*/
	var discVal = $('<td>');
	$('<input>').attr( {type:'text', name:'discount[]', id: (upc_code).replace(' ', '_')+new_lot+'_disc',
							class:'disc_input'} ).appendTo(discVal);
	$(row).append(discVal);
	
	/*Wholesale price for New Item*/
	var wpriceVal = $('<td>');
	$('<input>').attr( {type:'text', name: 'prod_wholesale[]', value: wholesale_price, class:'wprice_input'} ).appendTo(wpriceVal);
	$('<input>').attr( {type:'hidden', name:'wholesale_price_exis[]', id: (upc_code).replace(' ', '_')+new_lot+'_wPriceExis',
								class:'wprice_input', value:0}).appendTo(wpriceVal);
	$(row).append(wpriceVal);
	
	/*Retail price for New Item*/
	var rpriceVal = $('<td>');
	$('<input>').attr( {type:'text', name: 'prod_retail[]', value: retail_price, class:'rprice_input'} ).appendTo(rpriceVal);
	$('<input>').attr( {type:'hidden', name:'retail_price_exis[]', id: (upc_code).replace(' ', '_')+new_lot+'_rPriceExis',
								class:'rprice_input', value:0}).appendTo(rpriceVal);
	$(row).append(rpriceVal);
	
	/*purchase price for New Item*/
	var ppriceVal = $('<td>');
	$('<input>').attr( {type:'text', name: 'prod_purchase[]', value: purchase_price, class:'pprice_input'} ).appendTo(ppriceVal);
	$('<input>').attr( {type:'hidden', name:'purchase_price_exis[]', id: (upc_code).replace(' ', '_')+new_lot+'_pPriceExis',
								class:'pprice_input', value:0}).appendTo(ppriceVal);
	$(row).append(ppriceVal);
	
	$('<td></td>').text(0).appendTo(row);
	
	/*<td> Reconciled Quantity Counter*/
	var recQty = $('<td>');//belongs to new stock item
	$('<input>').attr( {type:'text', name:'item_quan[]', id: (upc_code).replace(' ', '_')+new_lot,
						class:'quant_input highlighted numberOnly', value:1, readonly:true} ).appendTo(recQty);
	$('<input>').attr( {type:'hidden', name:'item_quan_prev_batch[]',
						id: ((upc_code).replace(' ', '_'))+new_lot+'_prev', class:'quant_input_prev',
						value:0} ).appendTo(recQty);
	$('<input>').attr( {type:'hidden', name:'item_quan_new_batch[]',
						id: ((upc_code).replace(' ', '_'))+new_lot+'_new', class:'quant_input_new',
						value:1} ).appendTo(recQty);
	/*Add Reconciled Quantity to Row*/
	$(row).append(recQty);
	
	/*<td> for Reasons DD*/
	var reasonTd = $('<td>');
	/*<select> for Options*/
	var reasonSel = $('<select></select>').attr( {name:'resn_sel[]', class:'reason_sel'} ).css( {
											height:'23px', width:'80px'} );
	/*Add Blank Option to Select*/
	$('<option></option>').attr('value', '0').text('Select').appendTo(reasonSel);

	/*Append Reason Options*/
	$.each(reasons, function(index, value){
		$('<option></option>').attr('value', value).text(index).appendTo(reasonSel);
	});
	/*Append Select to <td>*/
	$(reasonTd).append(reasonSel);
	/*End <select> for Options*/
	/*Add Reasons DD to Row*/
	$(row).append(reasonTd);
	
	/*Push Row to Table*/
	$("#TestTable").prepend(row);
	
	/*Hide PopUp and clear it's Values*/
	$('#newStock').hide();
	clearNform();
	searchUPc.push(upc_code);
}

/*Load Values in New Item PopUp DropDowns*/
function mod_manufacturers(dt, modId){
	
	if(modId=="0")
		return;
	
	/*Ajax Query Parameters*/
	var params = {};
	var selId = "";
	
	switch(dt){
		
		case 'manufacturer':
			params.action = 'get_mod_manufacturer';
			selId = "#nManuf";
			
			var moduleType = $('#nModType').val();
			if(moduleType==="1")
				$('#nFramesFields').show();
			else
				$('#nFramesFields').hide();
			if(moduleType==="1" || moduleType==="3")
				$('#nBrand').prop('disabled', false);
			else
				$('#nBrand').empty().prop('disabled', true);
		break;
		case 'brands':
			var moduleType = $('#nModType').val();
			if(moduleType==="1" || moduleType==="3"){
				params.action = 'get_manuf_brands';
				params.module = moduleType;
				selId = "#nBrand";
			}
			else
				return;
		break;
		case 'vendor':
			params.action = 'get_manuf_vendors';
			selId = "#nVendor";
		break;
		default:
			return;
		break;
	}
	/*Query For Id*/
	params.mod_id =	modId;
	
	$.ajax({
		type		: 'POST',
		data		: params,
		url			: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/admin/ajax.php',
		beforeSend	: function(){
			$("#loading").show();
		},
		success		: function(data){
			
			data	= $.parseJSON(data);
			count	= Object.keys(data).length;
			
			/*Manufacturer <select>*/
			var manufSelect = $(selId);
			$(manufSelect).empty();	/*Remove Previous Item Type's Data*/
			
			if(count>0){
				
				$('<option></option>').attr('value', '0').text('Please Select').appendTo(manufSelect);
				
				/*Append Elements to the Select*/
				$.each(data, function(index, value){
					$('<option></option>').attr('value', value).text(index).appendTo(manufSelect);
				});
			}
		},
		complete	: function(){
			$("#loading").hide();
		}
	});
}

function clearNform(){
	$('#nItemUpc, #nItemName, #nItemWholesale, #nItemPurchase, #nItemRetail, #nItemEd, #nItemDbl, #nItemTemple').val('');
	$('#nModType').val(0);
	$('#nManuf, #nVendor, #nBrand').val(0).empty();
	$('#nFramesFields').hide();
}
function adv_srh_fun(){
	$('#advancedSearch').show();
}
function advance_srh_result(){
	$('#loading').show();
	var type_id =$("#type_optical_id").val();
	var mid =$("#manufacturer_Id_Srch").val();
	var vid =$("#opt_vendor_id").val();
	var string = 'action=get_detail&tid='+type_id+'&mid='+mid+'&vid='+vid;
	$.ajax({
		type: "POST",
		url: "advanced_search.php",
		data: string,
		cache: false,
		success: function(response)
		{
			var opt_data = response;
			$('#advance_srh_result_div').html(opt_data);
			$('#loading').hide();	
			$("#selectall").click(function(){		
				if($(this).is(":checked")){
					$(".rec_chk_box").prop('checked', true);
				}else{
					$(".rec_chk_box").prop('checked', false);
				}
			});
			$("#advance_srh_result_div .quant_input").change(function(){		
				var chk_box_id=$(this).attr('id');
				$("#"+chk_box_id+"_r_chk").prop('checked', true);
			});
			get_old_rec();
		}
	});
	$('#advance_srh_result_div').show();
}
function get_old_rec(){
	var back_quantititd = $('#TestTable .quant_input');
	var popUP = $('#advance_srh_result_div > table > tbody');
	
	$.each(back_quantititd, function(index, obj){
		var item_id = $(obj).attr('id');
		var back_reason = $(obj).parent('td').next('td').find('.reason_sel').val();
		var back_batch_id = $(obj).parent('td').siblings('input[name=\'in_bat_rec_id[]\']').val();
		
		var find_in_popup = $(popUP).find('#'+item_id);
		var find_reason = $(find_in_popup).parent('td').next('td').find('.reason_sel');
		var find_batch_id = $(find_in_popup).parent('td').siblings('td').find('input[name=\'in_bat_rec_id[]\']');
		
		if(find_in_popup.length > 0){
			$(find_in_popup).val($(obj).val());
			$(find_reason).val(back_reason);
			$(find_batch_id).val(back_batch_id);
		}
	});
}
function set_new_rec(){
	$('#show_con').css("display","block");
	$.each($('.rec_chk_box'), function(index, obj){
		if($(obj).prop('checked') == true){
			var chkVal = $(obj).val();
			
			//Changing Attributes
			if($(obj).hasClass('rec_chk_box') == true) $(obj).removeClass('rec_chk_box');
			if($(obj).hasClass('print_item') == false) $(obj).addClass('print_item');
			
			$(obj).attr('name', 'print_item[]');
			
			//Parent Table Row
			var element = $('#'+chkVal);
			element.attr('id',$(obj).val());
			
			//Appending to Back Table
			$("#TestTable").prepend(element);
			
			var org_upc_code = $(obj).val();
			org_upc_code = org_upc_code.replace("_r","");
			
			searchUPc.push(org_upc_code);
			//element.remove();
		}
		
		
	});
	
	/*$.each($('.rec_chk_box'), function(index, obj){
		if($(this).is(":checked")){
			var chk_id=$(obj).val();
			var org_upc_code=chk_id.replace("_r","");
			var chk_val=$('#'+chk_id).contents();
			console.log(chk_val);
			chk_val=chk_val.html(function(i, oldHTML) {if(typeof(oldHTML)!='undefined')return oldHTML.replace(/rec_chk_box/g, 'print_item');});
			
			//chk_val=$(chk_val).replaceAll('rec_chk_box','print_item');
			$('#TestTable #'+chk_id).remove();
			var row = $('<tr></tr>').attr('id',chk_id);
			$(row).append(chk_val);
			//$(row).find('.rec_chk_box_td').remove();
			//$(row).prepend("<input type='checkbox' class='print_item' name='print_item[]' value=''>");
			$("#TestTable").prepend(row);
			searchUPc.push(org_upc_code);
		}
	});*/
	$('#advance_srh_result_div').empty();
	$('#advancedSearch').hide();
	//BUTTONS
	/*var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Back","top.main_iframe.admin_iframe.reload_page();");
	mainBtnArr[1] = new Array("frame","Save","top.main_iframe.admin_iframe.save_rec();");
	mainBtnArr[2] = new Array("frame","Save & Reconcile","top.main_iframe.admin_iframe.submit_form();");
	mainBtnArr[3] = new Array("frame","Print Labels","top.main_iframe.admin_iframe.print();");
	top.btn_show("admin",mainBtnArr);*/	
	$("#action_buttons").show();
}
</script>



<div class="mt10 rec_con">
  <div class="listheading">
  	Stock Reconciliation - <?php echo $location_name; ?>
	<?php if($_REQUEST['batch']!=""){
		$qur_bat=imw_query("SELECT bt.save_date, bt.updated_date, bt.status,
							  CONCAT(u.lname, ', ', u.fname) AS username
							  FROM in_batch_table bt LEFT JOIN users u ON(u.id = bt.user_id) 
							  WHERE bt.id = ".$_REQUEST['batch']);
		$bat_row=imw_fetch_array($qur_bat);
		
		$savedate = "";
		$savetime = "";
		if($bat_row['save_date']!="0000-00-00 00:00:00"){
			$savedate=date("m-d-Y",strtotime($bat_row['save_date']));
			$savetime=date("h:i:s",strtotime($bat_row['save_date']));
		}
		
		$updtdate = "";
		$updttime = "";
		if($bat_row['updated_date']!="0000-00-00 00:00:00"){
			$updtdate=date("m-d-Y",strtotime($bat_row['updated_date']));
			$updttime=date("h:i:s",strtotime($bat_row['updated_date']));
		}
		$batch_user_name = $bat_row['username'];
	?>
	<div class="batch_div_msg" id="batch_div_msg">
		<?php if($bat_row['status']=="saved"){
			echo "Batch Saved On: ".$savedate." " .$savetime."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "Saved By: ".$batch_user_name;
		}
		else{ 
			echo "Batch Reconciled on: ".$updtdate." " .$updttime."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "Reconciled by: ".$batch_user_name;
		}
	?>
	</div>
	<?php } ?>
  </div>
  
 	<form name="recon_form" id="recon_form" action="" method="post" style="width: 100%">
	<!-- Hidden Field for new stock Indocator -->
	<input type="hidden" name="new_stock" id="new_stock" value="0" />
	<table style="width:100%;border:0px none;" class="btn_cls">
	  <tr><td width="50%" align="left">
	  <?php if($_REQUEST['status']!="updated"){?>
	  <table>
	  <tr>
		<th>Enter UPC Code</th>
		<td><input type="text" name="scan_image[]"  id="scan_image" autocomplete="off" autofocus></td>
		<td colspan="2">
		<input type="submit" value="Search" id="search_btn">
		<input type="submit" value="New Stock" id="new_stock_btn">
		<input type="button" value="Advanced Search" id="advanced_search_btn" onClick="adv_srh_fun();">
		  </td>
	  </tr>
	</table>
	<?php }?>
	</td>
	<td width="50%" align="right"><div id="action_buttons" style="display: <?php echo($_REQUEST['status'])?'block':'none';?>">
	<?php if($_REQUEST['status']=='updated'){?>		
	<input type="button" value="Print" id="Print" onClick="print_batch();">
	<?php }else{?>
	<input type="button" value="Save" id="Save" onClick="save_rec();">
	<input type="button" value="Save & Reconcile" id="Save & Reconcile" onClick="submit_form();">
	<?php }?>
	<input type="button" value="Print Labels" id="Print Labels" onClick="print();">
	<input type="button" value="Close" id="Back" onClick="reload_page();">
	</div>
	</td></tr></table>
  </form>  
</div>
<div class="back_btn" id="back_btn" style="display:none;">
  <div class="btn_cls btn_cls1">
    <input type="button" value="Back" onClick="window.location.href='./index.php'">
  </div>
</div>
<div class="err_div"></div>
<div id="show_con" style="width: 100%">
<?php
	$minus_hg="260";
	if($_REQUEST['status']=='updated'){$minus_hg="260";}
	$item_div=($_REQUEST['status']=='updated')?"288":"242";
	$summary_div=230;
?>

<div class="uper_cont" style="height:<?php echo $_SESSION['wn_height']-$minus_hg;?>px;overflow:hidden;">

<div style="height:<?php echo $_SESSION['wn_height']-($minus_hg+$summary_div)?>px;overflow-y:scroll;overflow-x:hidden; width:100%">
   
   <form action="" method="post" name="quan_form" id="quan_form" style="width:100%;">
		<div style="display:none">
			<input type="submit" value="Save & Reconcile" name="save_qnty" id="save_qnty">
		</div>
    <table style="width:100%;border:0px none;">
      <thead>
      <?php if($_REQUEST['status']=='updated'){?>
        <tr class="listheading sepTH">
          <th style="width:3%;"><input type="checkbox" name="select_all" id="selectall" style="display: none"><label for="selectall">S.No</label></th>
          <th style="width:10%;">UPC Code</th>
          <th style="width:8%;" title="Product Type">P. Type</th>
          <th style="width:10%;">P. Name</th>
          <th style="width:10%;">Size</th>
          <th style="width:11%;">Brand</th>
		  <th style="width:8%;">Color</th>
		  <th style="width:5%;">Style</th>
          <th style="width:5%;">Discount</th>
		  <th style="width:5%;" title="Wholesale Price">W. Price</th>
		  <th style="width:5%;" title="Retail Price">R. Price</th>
		  <th style="width:5%;" title="Purchase Price">P. Price</th>
          <th style="width:5%;" title="Fac. Qty.">F. Qty.</th>
          <th style="width:5%;" title="Reconciled Quantity">Rec. Qty.</th>
          <th style="width:5%;"><?php if($bat_row['status']=="saved" || !isset($bat_row['status']))
		  {?>
            <select style="height:23px;width:80px;" class="reason_sel" onChange="reason_sel(this.value);">
              <option value="0">Reason</option>
              <?php $query=imw_query("select * from in_reason where del_status='0' order by reason_name");
		  			while($sel_row=imw_fetch_array($query)){
				?>
              <option value="<?php echo $sel_row['id'];?>"><?php echo $sel_row['reason_name'];?></option>
              <?php } ?>
            </select>
            <?php }else
		  {
			  echo "<label style='width:80px;'>Reason</label>";
		  }?>
          </th>
        </tr>
        <?php }else{?>
        <tr class="listheading sepTH">
          <th style="width:3%;"><input type="checkbox" name="select_all" id="selectall"></th>
          <th style="width:10%;">UPC Code</th>
          <th style="width:8%;" title="Product Type">P Type</th>
          <th style="width:10%;">P. Name</th>
          <!--<th style="width:111px;">Manufacturer</th>-->
          <th style="width:10%;">Size</th>
          <th style="width:11%;">Brand</th>
		  <th style="width:8%;">Color</th>
		  <th style="width:5%;">Style</th>
          <th style="width:5%;">Discount</th>
		  <th style="width:5%;" title="Wholesale Price">W. Price</th>
		  <th style="width:5%;" title="Retail Price">R. Price</th>
		  <th style="width:5%;" title="Purchase Price">P. Price</th>
          <th style="width:5%;" title="Fac. Qty.">F. Qty.</th>
          <th style="width:5%;">Rec. Qty.</th>
          <th style="width:5%;"><?php if($bat_row['status']=="saved" || !isset($bat_row['status']))
		  {?>
            <select style="height:23px;width:80px;" class="reason_sel" onChange="reason_sel(this.value);">
              <option value="0">Reason</option>
              <?php foreach($reason_arr as $res_key=>$res_val){	?>
              <option value="<?php echo $res_key;?>"><?php echo $res_val;?></option>
              <?php } ?>
            </select>
            <?php }else
		  {
			  echo "<label style='width:80px;'>Reason</label>";
		  }?>
          </th>
        </tr>
        <?php }?>
      </thead>
 
        <tbody id="TestTable">
          <?php if(isset($_REQUEST['batch'])){
			 
			$query1=imw_query("select id,vendor_name from in_vendor_details");
            while($row1=imw_fetch_array($query1)){
				$vendor_arr[$row1['id']]=$row1['vendor_name'];
			}
			
			$query3=imw_query("select id,frame_source from in_frame_sources");
            while($row3=imw_fetch_array($query3)){
				$frame_source_arr[$row3['id']]=$row3['frame_source'];
			}
			$query4=imw_query("select id,brand_name from in_contact_brand");
            while($row4=imw_fetch_array($query4)){
				$contact_brand_arr[$row4['id']]=$row4['brand_name'];
			}
			$query5=imw_query("select id,color_name,color_code from in_color");
            while($row5=imw_fetch_array($query5)){
				$color_name_arr[$row5['id']]=$row5['color_name'];
			}
			
			$query6=imw_query("select id,color_name,color_code from in_frame_color");
            while($row6=imw_fetch_array($query6)){
				$frame_color_name_arr[$row6['id']]=$row6['color_name'];
			}
			
			$query7=imw_query("select id,style_name from in_frame_styles");
            while($row7=imw_fetch_array($query7)){
				$frame_style_name_arr[$row7['id']]=$row7['style_name'];
			}
	
			 
        $i="";
		$j=1;
        $d="";
        $prev_upc = array();
        echo "<script>show_div();</script>";
        $query_u=imw_query("select * from in_batch_records where in_batch_id=".$_REQUEST['batch']."");
        $i=imw_num_rows($query_u);
        print "<script type=\"text/javascript\">
            i = ".($i+1).";
        </script>";
        echo "<input type='hidden' id='batch_id_field' name='batch_id_field' value='".$_REQUEST['batch']."'>";	
        while($row_u=imw_fetch_array($query_u))
        {
            $upc=$row_u['in_item_id'];
            array_push($prev_upc,"'".$row_u['item_upc_code']."'");
            $query=imw_query("select * from in_item where id='".$upc."'");
        if(imw_num_rows($query)>0)
        {
                
        while($row=imw_fetch_array($query)) {
			
            $query5=imw_query("select *,IFNULL(SUM(stock), 0) AS 'stock' from in_item_loc_total where loc_id='".$_SESSION['pro_fac_id']."' and item_id='".$row['id']."' GROUP BY item_id");
            $row5=imw_fetch_array($query5);
            if($_REQUEST['status']=='saved'){
				$upc_code_tr=str_replace(' ','_',$row['upc_code'])."_r_".$row_u['lot_no'];
				$id=str_replace(' ','_',$row['upc_code']).$row_u['lot_no'];
					echo "<tr id='".$upc_code_tr."'>";
					echo "<input type='hidden' name='upc_code[]' value='".$row['upc_code']."'>";
					echo "<input type='hidden' name='lot_no[]' value='".$row_u['lot_no']."'>";
					echo "<input type='hidden' name='in_bat_rec_id[]' value='".$row_u['id']."'>";
					echo "<input type='hidden' name='fac_quant[]' value=".$row5['stock'].">";
					echo "<input type='hidden' name='fac_lot_quant[]' value=".$row_u['in_fac_lot_prev_qty'].">";
					echo "<input type='hidden' name='tot_qnt[]' value=".$row['qty_on_hand'].">";
					echo "<input type='hidden' name='item_id[]' value=".$row['id'].">";
					echo "<input type='hidden' name=\"module_type[]\" class=\"module_type\" value='".$row['module_type_id']."'>";
					
					echo "<input type='hidden' name=\"retail_price_flag[]\" class=\"retail_flag\" value='".$row_u['retail_price_flag']."'>";
					echo "<input type='hidden' name=\"purchase_price_flag[]\" class=\"purchase_flag\" value='".$row_u['purchase_price_flag']."'>";
					echo "<input type='hidden' name=\"item_quan_prev_batch[]\" id=\"".$id."_prev\" value='".$row_u['in_item_quant']."'>";
					echo "<input type='hidden' name=\"item_quan_new_batch[]\" id=\"".$id."_new\" value=''>";
					echo "<input type='hidden' name=\"prod_name[]\" class=\"prod_name\" value=\"".$row['name']."\" />";
				    echo "<td><input type='checkbox' class='print_item' name='print_item[]' value='".$row['upc_code']."'></td>";
					echo "<td ";
					echo " data-title=\"#lot:$row_u[lot_no]\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\">";
					echo $row['upc_code']."</td>";
					echo "<td>".$module_arr[$row['module_type_id']]."</td>";
					//brand
					echo "<td";
					if(strlen($row['name'])>$column_data_limit){
						$pro_name=substr($row['name'],0,$column_data_limit).'..';
						echo " data-title=\"".$row['name']."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
					}else {$pro_name=$row['name'];}
					echo ">".$pro_name."</td>";
					/*//vendor
					echo "<td";
					$vendorName=$vendor_arr[$row['vendor_id']];
					if(strlen($vendorName)>$column_data_limit){
						$vendor=substr($vendorName,0,$column_data_limit).'..';
						echo " data-title=\"".$vendorName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
					}else {$vendor=$vendorName;}
					echo ">".$vendor."</td>";*/
				
					//size of frame
					echo "<td>";
				if($row['module_type_id']==1){
					echo $row[fpd]."-".$row[bridge]."-".$row[temple];
				/*echo"<table style='width:100%;'>
						<tr>
							<td width='25%'><strong>A</strong> $row[a]</td>
							<td width='25%'><strong>B</strong> $row[b]</td>
							<td width='25%'><strong>ED</strong> $row[ed]</td>
							<td width='25%'><strong>DBL</strong> $row[dbl]</td>
						</tr></table>
						<table style='width:100%;'>
						<tr>
							<td width='33%'><strong>Temple</strong> $row[temple]</td>
							<td width='33%'><strong>Bridge</strong> $row[bridge]</td>
							<td width='auto'><strong>FPD</strong> $row[fpd]</td>
						</tr>
					</table>";*/
				}
				echo"</td>";
				
					if($row['module_type_id']==3){
						//brand
                  	 	echo "<td";
						$brandName=$contact_brand_arr[$row['brand_id']];
						if(strlen($brandName)>$column_data_limit){
							$brand=substr($brandName,0,$column_data_limit).'..';
							echo " data-title=\"".$brandName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
						}else {$brand=$brandName;}
						echo ">".$brand."</td>";
						//color
						echo "<td";
						$colorName=$color_name_arr[$row['color']];
						if(strlen($colorName)>$column_data_limit){
							$color=substr($colorName,0,$column_data_limit).'..';
							echo " data-title=\"".$colorName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
						}else {$color=$colorName;}
						echo ">".$color."</td>";
					}else{
						//brand
						echo "<td";
						$brandName=$frame_source_arr[$row['brand_id']];
						if(strlen($brandName)>$column_data_limit){
							$brand=substr($brandName,0,$column_data_limit).'..';
							echo " data-title=\"".$brandName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
						}else {$brand=$brandName;}
						echo ">".$brand."</td>";
						//color
						echo "<td";
						$colorName=$frame_color_name_arr[$row['color']];
						if(strlen($colorName)>$column_data_limit){
							$color=substr($colorName,0,$column_data_limit).'..';
							echo " data-title=\"".$colorName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
						}else {$color=$colorName;}
						echo ">".$color."</td>";
					}
					echo "<td";
					$styleName=$frame_style_name_arr[$row['frame_style']];
					if(strlen($styleName)>$column_data_limit){
						$style=substr($styleName,0,$column_data_limit).'..';
						echo " data-title=\"".$styleName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
					}else {$style=$styleName;}
					echo ">".$style."</td>";
				
					echo "<td><input type='text' name=\"discount[]\" value='".$row_u['discount']."' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."_disc' class='disc_input' data-discount='".$upc_code_tr."'></td>";
				
					echo "<td><input type='text' name=\"wholesale_price_exis[]\" value='".$row_u['wholesale_price']."' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."_wPriceExis' class='wprice_input' data-wholesale='".$upc_code_tr."'></td>";
				
					echo "<td><input type='text' name=\"retail_price_exis[]\" value='".$row_u['retail_price']."' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."_rPriceExis' class='rprice_input' onChange=\"retailPriceChanged('".$upc_code_tr."')\" data-retail='".$upc_code_tr."'></td>";
				
					echo "<td><input type='text' name=\"purchase_price_exis[]\" value='".$row_u['purchase_price']."' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."_pPriceExis' class='pprice_input' data-purchase='".$upc_code_tr."'></td>";
					
				//echo "<td>".$row_u['discount']."</td>";
				//echo "<td>".$row_u['retail_price']."</td>";
				
                echo "<td>".$row_u['in_fac_lot_prev_qty']."</td>";
                echo "<td><input type='text' class='quant_input numberOnly' value=\"".$row_u['in_item_quant']."\" name='item_quan[]' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."' data-quant='".$upc_code_tr."' readonly=\"true\"></td>";
                echo "<td style='width:80px;'><select style='height:23px;width:80px;' name='resn_sel[]' class='reason_sel'><option value='0'>Select</option>";
                    foreach($reason_arr as $res_key=>$res_val){
                      if($row_u['reason']==$res_key)
                      {
                          $selected="selected";
                      }
                      else
                      {
                          $selected="";
                      }
						echo '<option value="'.$res_key.'" '.$selected.'>'.$res_val.'</option>';
					}
          			echo '</select>';
          		echo '</td>';
          echo '</tr>';
			}else{
				$upc_code_tr=str_replace(' ','_',$row['upc_code'])."_r".$row_u['lot_no'];
				$id=str_replace(' ','_',$row['upc_code']).$row_u['lot_no'];
					echo "<tr id='".$upc_code_tr."'>";
					echo "<input type='hidden' name='upc_code[]' value='".$row['upc_code']."'>";
					echo "<input type='hidden' name='lot_no[]' value='".$row['upc_code']."'>";
					echo "<input type='hidden' name='in_bat_rec_id[]' value='".$row_u['lot_no']."'>";
					echo "<input type='hidden' name='fac_quant[]' value=".$row5['stock'].">";
					echo "<input type='hidden' name='fac_lot_quant[]' value=".$row_u['in_fac_lot_prev_qty'].">";
					echo "<input type='hidden' name='tot_qnt[]' value=".$row['qty_on_hand'].">";
					echo "<input type='hidden' name='item_id[]' value=".$row['id'].">";
					echo "<input type='hidden' name=\"module_type[]\" class=\"module_type\" value='".$row['module_type_id']."'>";
					
					echo "<input type='hidden' name=\"retail_price_flag[]\" class=\"retail_flag\" value='".$row_u['retail_price_flag']."'>";
					echo "<input type='hidden' name=\"purchase_price_flag[]\" class=\"purchase_flag\" value='".$row_u['purchase_price_flag']."'>";
					
					echo "<input type='hidden' name=\"prod_name[]\" class=\"prod_name\" value=\"".$row['id']."\" />";
					echo "<td id='sr_no'><input type='checkbox' class='print_item' name='print_item[]' value='".$row['upc_code']."'>".$j."</td>";
					
					echo "<input type='hidden' name=\"item_quan_prev_batch[]\" id=\"".$id."_prev\" value='".$row_u['in_item_quant']."'>";
					echo "<input type='hidden' name=\"item_quan_new_batch[]\" id=\"".$id."_new\" value=''>";
					echo "<td title='#lot:$row_u[lot_no]'>".$row['upc_code']."</td>";
					echo "<td>".$module_arr[$row['module_type_id']]."</td>";
					echo "<td>".$row['name']."</td>";
					echo "<td>".$vendor_arr[$row['vendor_id']]."</td>";
						if($row['module_type_id']==3){
							echo "<td>".$contact_brand_arr[$row['brand_id']]."</td>";
							echo "<td>".$color_name_arr[$row['color']]."</td>";
						}else{
							echo "<td>".$frame_source_arr[$row['brand_id']]."</td>";
							echo "<td>".$frame_color_name_arr[$row['color']]."</td>";
						}
					echo "<td";
					$styleName=$frame_style_name_arr[$row['frame_style']];
					if(strlen($styleName)>$column_data_limit){
						$style=substr($styleName,0,$column_data_limit).'..';
						echo " data-title=\"".$styleName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
					}else {$style=$styleName;}
					echo ">".$style."</td>";
				
						echo "<td><input type='text' name=\"discount[]\" value='".$row_u['discount']."' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."_disc' class='disc_input' disabled></td>";
				
					echo "<td><input type='text' name=\"wholesale_price_exis[]\" value='".$row_u['wholesale_price']."' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."_wPriceExis' class='wprice_input' disabled></td>";
				
					echo "<td><input type='text' name=\"retail_price_exis[]\" value='".$row_u['retail_price']."' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."_rPriceExis' class='rprice_input' onChange=\"retailPriceChanged('".$upc_code_tr."')\" disabled></td>";
				
					echo "<td><input type='text' name=\"purchase_price_exis[]\" value='".$row_u['purchase_price']."' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."_pPriceExis' class='pprice_input'  disabled></td>";	
						//echo "<td>".$row_u['discount']."</td>";
						//echo "<td>".$row_u['retail_price']."</td>";
                        echo "<td>".$row_u['in_fac_lot_prev_qty']."</td>";
                        echo "<td><input type='text' class='quant_input numberOnly' value=\"".$row_u['in_item_quant']."\" name='item_quan[]' id='".str_replace(" ", "_",$row['upc_code']).$row_u['lot_no']."' disabled></td>";
                        echo "<td style='width:80px;'>";
                          if($reason_arr[$row_u['reason']]!=""){echo $reason_arr[$row_u['reason']];}else{ echo ""; }
                       
                      echo '</td>';
          		echo '</tr>';
         }
        $i--;
		$j++;
        }
        }
        }
        if(count($prev_upc)>0){
            
            print "<script type=\"text/javascript\">
                searchUPc = new Array(".implode(",",$prev_upc).");
                $.each(searchUPc, function(i, val){
                    searchUPc[i] = String(val);
                });
                </script>";
        }
}
		$rec_array=array();
		$rec_array2=array();
?>
          </tbody>
      </table>
	
    </form>
    
  </div>
      <?php if($_REQUEST['status']=='saved' || $_REQUEST['status']=='updated') {?>
      <div class="summary_div" style="margin-top:10px;">
        <div class="listheading">Reconciliation Summary</div>

        <table id='tabl_sum' style="padding:5px;">
<?php 
	$sum_query = 'SELECT br.*, i.module_type_id, lot.wholesale_price as wholesale_cost, lt.stock  
	FROM in_batch_records br 
	LEFT JOIN in_item i ON(br.in_item_id=i.id) 
	LEFT JOIN in_item_loc_total lt ON(br.in_item_id=lt.item_id AND lt.loc_id='.((int)$_SESSION['pro_fac_id']).') 
	LEFT JOIN in_item_lot_total lot on (br.in_item_id=lot.item_id and br.lot_no=lot.lot_no)
	WHERE br.in_batch_id='.((int)$_REQUEST['batch'].' GROUP BY br.in_item_id,br.lot_no');
	$sum_query=imw_query($sum_query);
	
	$retail_price=$wholesal=$purchase_price=0;
	if(imw_num_rows($sum_query)>0)
	{
		/*<th style='width:70px;padding:5px;'>Int. Qty.</th>*/
		echo "<tr style='background:#e2e2e2;'>
		<th style='width:120px;padding:5px;text-align:left;'>Product Type</th>
		<th style='width:70px;padding:5px;'>Fac. Qty.</th>
		<th style='width:70px;padding:5px;'>Rec. Qty.</th>
		<th style='width:100px;padding:5px;'>Int. Amount</th>
		<th style='width:100px;padding:5px;'>Adj. Amount</th>
		<th style='width:130px;padding:5px;'>Total Rec. Amount</th>
		</tr>";
		while($sum_row=imw_fetch_array($sum_query))
		{
			if($_REQUEST['status']=='updated')
			{
				$qty=$sum_row['in_item_quant']-$sum_row['in_fac_lot_prev_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec']+=$qty;
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot']=$sum_row['prev_tot_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['fac']=$sum_row['in_fac_prev_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric']+=($qty*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_pric']=($sum_row['prev_tot_qty']*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rem_pric']=($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric'])+($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_pric']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_fac_pric']+=($sum_row['in_fac_lot_prev_qty']*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rem_fac_pric']=($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric'])+($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_fac_pric']);
			}
			else
			{
				$qty=$sum_row['in_item_quant']-$sum_row['in_fac_lot_prev_qty'];
				//$str.= "$qty=$sum_row[in_item_quant]-$sum_row[stock] \n price = $sum_row[in_fac_prev_qty]*$sum_row[wholesale_cost]\n";
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec']+=$qty;
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot']=$sum_row['prev_tot_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['fac']=$sum_row['in_fac_prev_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric']+=($qty*$sum_row['wholesale_cost']);	
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_pric']=($sum_row['prev_tot_qty']*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rem_pric']=($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric'])+($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_pric']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_fac_pric']+=($sum_row['in_fac_lot_prev_qty']*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rem_fac_pric']=($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric'])+($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_fac_pric']);
			}
			//$wholesal=bcadd($wholesal,($sum_row['wholesale_cost']*$sum_row['prev_tot_qty']),2);
			$wholesal=number_format($wholesal+($sum_row['wholesale_cost']*$sum_row['prev_tot_qty']), 2);
		}
		//file_put_contents('test.txt', print_r($rec_array,true)."\n\n", FILE_APPEND);
		$module_ar1=$arra=array();
		foreach($rec_array as $key=>$value)
		{
			echo "<tr><th style='text-align:left;padding-left:5px;'>".ucwords($key)."</th>";
			foreach($value as $key1=>$val1)
			{
				foreach($val1 as $key2=>$val2)
				{
					$module_ar1[$key][$key2][]=$val2;
				}
			}
			
			//echo "<td>".array_sum($module_ar1[$key]['tot'])."</td>";
			echo "<td>".array_sum($module_ar1[$key]['fac'])."</td>";
			echo "<td>".array_sum($module_ar1[$key]['rec'])."</td>";
			echo "<td style='text-align:right;padding-right:5px;'>".number_format(array_sum($module_ar1[$key]['tot_fac_pric']),2,".","")."</td>";
			echo "<td style='text-align:right;padding-right:5px;'>".number_format(array_sum($module_ar1[$key]['rec_pric']),2,".","")."</td>";
			echo "<td style='text-align:right;padding-right:5px;'>".number_format(array_sum($module_ar1[$key]['rem_fac_pric']),2,".","")."</td>";
			echo "</tr>";
		}
	}
?>
        </table>
      </div>
      <?php } ?>
      
      </div>
</div>
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>

<div id="print_label_div" style="display: none; height: <?php echo $_SESSION['wn_height']-450?>px">
	<div class="resultdiv">
		<div class="listheading" style="border-radius:2px;padding-left:10px;background-size:3.5px;height:26px;">Print Labels
			<img class="nHide" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="$('#print_label_div').hide();" />
		</div>
		<div class="itemOptions row">
			<div class="col-sm-12 btn_cls">
			<label for="dymoPrinter">Select Printer</label>
			<select id="dymoPrinter"></select>

			<label for="dymoPaper" style="margin-left:20px;">Paper Size</label>
			<select id="dymoPaper">
				<!--<option value="PriceTag.label">Price Tag 22mm x 24mm</option>-->
				<option value="PriceTag1.label">Price Tag 25.4mm x 76.2mm</option>
				<!--<option value="Address.label">Address 28mm x 89mm</option>
				<option value="ExtraSmall_2UP.label">Extra Small (2-Up) 13mm x 25mm</option>-->
			</select>
			<input type="button" name="printFinalLabel" id="printFinalLabel" onclick="printLabel();" value="Print" style="display: none">
			</div>
			<div class="row">
				<div class="col-sm-12" id="printable_data">
					<!--data will be here-->
					<div style="height:<?php echo $_SESSION['wn_height']-628?>px;overflow:scroll;overflow-x:hidden;margin:5px 0 0 0">
					  <div id="tab_data_cat">
						Loading Printable Data...
					  </div>
				  </div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	
// stores loaded label info
var label;
	
// Printer's List
var printersSelect = document.getElementById('dymoPrinter');
// Label's List
var labelSelected = document.getElementById('dymoPaper');
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';

var reasons ='';
var i=1;
function print()
{
	var upc_name="";
	var upc_qty="";
	var len=document.getElementsByName('print_item[]').length;
	var item1=document.getElementsByName('print_item[]');
	for(var i=0;i<len;i++)
	{
		if(item1[i].checked==true)
		{
			upc_name+=(item1[i].value)+",";
			//get added qty
			upc_qty+=($("#"+item1[i].value).val())+",";
		}
	}
	if(upc_name=="")
	{
		falert("Please Select record(s) to print");
		return false;
	}
	upc_name=upc_name.substring(0, ((upc_name.length)-1));
	$("#print_label_div").show();
	// load printers list on startup
	//loadPrinters();
	$('#tab_data_cat').html('Loading Printable Data...');
	$.ajax({
		type:"POST",
		url:"stock_print_ajax.php",
		data:"reconcile=1&upc_name="+upc_name+"&upc_qty="+upc_qty,
		success: function(msg)
		{
			
			$('#tab_data_cat').html(msg);
			if(msg)
			{
				//document.getElementById('content_tab_print').style.display="block";	
				//BUTTONS
				//var mainBtnArr = new Array();
				//mainBtnArr[0] = new Array("frame","Print","top.main_iframe.admin_iframe.print_rec();");
				//top.btn_show("admin",mainBtnArr);						
			}else{
				//BUTTONS
				//var mainBtnArr = new Array();
				//top.btn_show("admin",mainBtnArr);						
			}
		},
		complete: function(){
			//$('#load_img').hide();
			//$("#printable_data").html('');
			//SHOW PRINT BUTTON
			$("#printFinalLabel").show();
			
		}
	});
	
}	
// called when the document completly loaded
	// To load Dymo Printer
	function onload(){
		// loads all supported printers into a Select List 
		function loadPrinters()
		{
			var printers = dymo.label.framework.getLabelWriterPrinters();
			if (printers.length == 0)
			{
				//alert("No DYMO LabelWriter printers are installed. Install DYMO LabelWriter printers.");
				//return;
			}
	
			for (var i = 0; i < printers.length; ++i)
			{
				var printer = printers[i];
				var printerName = printer.name;
	
				var option = document.createElement('option');
				option.value = printerName;
				option.appendChild(document.createTextNode(printerName));
				printersSelect.appendChild(option);
			}
		}
		
		// load printers list on startup
		loadPrinters();
	};
	
	// register onload event
	$(window).on('load', function(){
		onload();
	});
	
	$(document).ready(function(){
		$('body').on('change', '.quant_input', function(){
			var id_str=$(this).attr('id');
			var qty=$("#"+id_str+"_new").val();
			if(qty>0)
			{
				$("#"+id_str+"_new").val($(this).val());
			}
			else
			{
				$("#"+id_str+"_prev").val($(this).val());
			}
		});
		
		//called when key is pressed in textbox
		$(".numberOnly").keypress(function (e) {
		 //if the letter is not digit then display error and don't type anything
		 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				return false;
			}
		});
	});
	
	
function loadLabelFromWeb(){

	// use jQuery API to load label
	$.ajax({
		url: window.opener.top.WRP+"/library/dymo/"+labelSelected.value,
		async:false,
		success:function(data){
			label = dymo.label.framework.openLabelXml(data);
		}
	});
}

/*Print Labels for selected items*/
function printLabel(){

	try{
		// Load label Structure
		loadLabelFromWeb();

		if (!label){
			falert("Load label before printing");
			return;
		}
		if(printersSelect.value==""){
			falert("No DYMO LabelWriter printers are installed. Install DYMO LabelWriter printers.");
			return;
		}

		// set data using LabelSet and text markup
		//var labelSet = new dymo.label.framework.LabelSetRecord();
		var labelSet = new dymo.label.framework.LabelSetBuilder();
		var record; 
		//alert(label);
		/*Getting Data from Tabele*/
		var selectedRecords = document.getElementsByName('print_item[]');
		var selectedUPC = new Array();
		var innerI=0;
		$.each(selectedRecords, function(i,obj){
			if(obj.checked===true){
				printCount = 0;
				if(typeof($(".printing_upc",document).get(innerI))!='undefined')
				{	
					upc_data = $(".printing_upc",document).get(innerI).innerHTML;
					print_data = $(".printing_data",document).get(innerI).innerHTML;

					if(labelSelected.value === 'ExtraSmall_2UP.label'){
						print_data = print_data.replace(/-/g, "<br/>");
					}

					print_data = print_data.replace(/<br>/g, "<br/>");
					printCount = $(".labelCount").get(innerI).value;
					printCount = printCount.replace(/[^\d]/g, "");

					if(printCount==""){printCount=0;}
					printCount = parseInt(printCount);
					for(i=printCount; i>0; i--){
						/*Add Data to Dymo LabelSet*/
						record = labelSet.addRecord();
						record.setText('BARCODE', upc_data);
						record.setTextMarkup('TEXT', print_data);
						/*End Add Data to Dymo LabelSet*/
					}
				}
				innerI++;
			}
		});
		/*End Getting Data from Table*/

		label.print(printersSelect.value, null, labelSet.toString());
		delete labelSet;
	}
	catch(e){
		falert(e.message || e);
	}
}

</script>
</body>
</html>