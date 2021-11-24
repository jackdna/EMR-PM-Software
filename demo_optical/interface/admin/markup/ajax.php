<?php
/*
File: ajax.php
Coded in PHP7
Purpose: Retail Price Markup Action
Access Type: Include File
*/
require_once('../../../config/config.php');
include_once($GLOBALS['DIR_PATH'].'/library/classes/functions.php');


if( isset($_POST['action']) && $_POST['action']!='' ){
	
	$dateTime	= date('Y-m-d h:i:s');
	$operator	= (int)$_SESSION['authId'];
	
	if( $_POST['action'] == 'saveData' ){
		
		$existing	= $_POST['exis'];
		$new		= $_POST['add'];
		$updStatus	= ($_POST['updateItems'] === 'true') ? true : false;
		
		/*Save modified existing records.*/
		if( count( $existing ) > 0 ){
			
			foreach( $existing as $record ){
				
				$formula = trim($record['formula']);
				$formula = htmlentities($formula);
				$sql = 'UPDATE `in_retail_price_markup` SET
						`formula`=\''.$formula.'\',
						`modified_by`='.$operator.',
						`modified_data_time`=\''.$dateTime.'\'
						WHERE `id`='.$record['id'];
				imw_query($sql);
			}
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			if($updStatus){
				
				foreach( $existing as $rows ){
					
					$formulaId	= (int)$rows['id'];
					$formula	= $rows['formula'];
					$sqlCount = 'SELECT `module_type_id`, `manufacturer_id`, `brand_id`, `vendor_id`, `style_id` FROM `in_retail_price_markup` WHERE `id`='.$formulaId.' AND `del_status`=0';
					$formulaResp = imw_query($sqlCount);
					if($formulaResp && imw_num_rows($formulaResp)>0){
						while($row = imw_fetch_object($formulaResp)){
							
							$sqlItemCount = 'SELECT `id`, `wholesale_cost`, `purchase_price` FROM `in_item`
											WHERE
												`module_type_id`='.$row->module_type_id.' AND 
												`del_status`=0 AND
												`retail_price_flag`=1 AND
												`formula`=\'\'';
							if( $row->manufacturer_id!='0' )
								$sqlItemCount .= ' AND `manufacturer_id`='.$row->manufacturer_id;
							
							if( $row->module_type_id=='1' || $row->module_type_id=='3' ){
								if($row->brand_id!='0')
									$sqlItemCount .= ' AND `brand_id`='.$row->brand_id;
							}
							
							if( $row->module_type_id=='1' && $row->style_id!='0')
								$sqlItemCount .= ' AND `frame_style`='.$row->style_id;
								
							if( $row->module_type_id=='5' || $row->module_type_id=='6' ){
								if($row->vendor_id!='0')
									$sqlItemCount .= ' AND `vendor_id`='.$row->vendor_id;
							}
							
							$countResp = imw_query($sqlItemCount);
							if( $countResp && imw_num_rows($countResp)>0 ){
								
								while( $itemsData = imw_fetch_object($countResp) ){
									
									$retailPrice = calculate_markup_price($formula, $itemsData->wholesale_cost, $itemsData->purchase_price);
									$retailPrice = number_format($retailPrice, 2);
									print $sqlRetailUpdate = 'UPDATE `in_item` SET `retail_price`=\''.$retailPrice.'\' WHERE `id`='.$itemsData->id;
									print "\n\n";
									imw_query($sqlRetailUpdate);
								}
							}
						}
					}
				}
			}
		}
		
		/*Save New Records*/
		if( count($new) > 0 ){
			
			$module_type_id = (int)$_POST['moduleTypeId'];
			foreach( $new as $record ){
				
				$formula = trim($record['formula']);
				$formula = htmlentities($formula);
				
				$sql = 'INSERT INTO `in_retail_price_markup` SET
						`module_type_id`='.$module_type_id.',
						`manufacturer_id`='.((int)$record['manuf']).',
						`brand_id`='.((int)$record['brand']).',
						`style_id`='.((int)$record['style']).',
						`vendor_id`='.((int)$record['vendor']).',
						`formula`=\''.$formula.'\',
						`entered_by`='.$operator.',
						`entered_data_time`=\''.$dateTime.'\'';
				imw_query($sql);
			}
		}
	}
	elseif( $_POST['action'] == 'getCount' ){
		
		$existing	= $_POST['exis'];
		
		/*Get the count of the modified Items for which the formula is applicable*/
		$itemsCount = array();
		
		foreach( $existing as $rows ){
			
			$formulaId = (int)$rows['id'];
			$sqlCount = 'SELECT `module_type_id`, `manufacturer_id`, `brand_id`, `vendor_id`, `style_id` FROM `in_retail_price_markup` WHERE `id`='.$formulaId.' AND `del_status`=0';
			$formulaResp = imw_query($sqlCount);
			if($formulaResp && imw_num_rows($formulaResp)>0){
				while($row = imw_fetch_object($formulaResp)){
					
					$sqlItemCount = 'SELECT COUNT(`id`) AS \'count\' FROM `in_item`
									WHERE
										`module_type_id`='.$row->module_type_id.' AND 
										`del_status`=0 AND
										`retail_price_flag`=1 AND
										`formula`=\'\'';
					if( $row->manufacturer_id!='0' )
						$sqlItemCount .= ' AND `manufacturer_id`='.$row->manufacturer_id;
					
					if( $row->module_type_id=='1' || $row->module_type_id=='3' ){
						if($row->brand_id!='0')
							$sqlItemCount .= ' AND `brand_id`='.$row->brand_id;
					}
					
					if( $row->module_type_id=='1' && $row->style_id!='0')
						$sqlItemCount .= ' AND `frame_style`='.$row->style_id;
						
					if( $row->module_type_id=='5' || $row->module_type_id=='6' ){
						if($row->vendor_id!='0')
							$sqlItemCount .= ' AND `vendor_id`='.$row->vendor_id;
					}
					
					$countResp = imw_query($sqlItemCount);
					if( $countResp && imw_num_rows($countResp)>0){
						$itemsCountData = imw_fetch_assoc($countResp);
						$itemsCount[$formulaId] = $itemsCountData['count'];
					}
				}
			}
			else{
				$itemsCount[$formulaId] = 0;
			}
		}
		$itemsCount = array_sum($itemsCount);
		
		print json_encode(array('count'=>$itemsCount));
	}
	elseif( $_POST['action'] == 'delRow' ){
		
		$row_id = $_POST['rowId'];
		
		/*Mark row as deleted*/
		$sql = 'UPDATE `in_retail_price_markup` SET
				`del_status`=1,
				`del_by`='.$operator.',
				`del_data_time`=\''.$dateTime.'\'
				WHERE `id`='.$row_id;
		imw_query($sql);
	}
}

?>