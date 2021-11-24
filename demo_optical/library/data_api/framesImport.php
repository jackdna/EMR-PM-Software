<?php
ini_set('max_execution_time', 0);
$ignoreAuth = true;
$vars = $_REQUEST['option'];
$msg="Please do not hit back button or refresh page untill sync complete message displays.<br/>";
$msg.="<br/>Sync begin";
importInProgress($msg,true);

$path = dirname(__FILE__);
require_once($path."/../../config/config.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);


include($path."/framesData.php");
require_once($path.'/framesDataDetails.php');	/*FramesData Credentials*/

$obj = new framesData();

$noComplete = false;

try{
	$obj->setConfig($configs);
	$operator_id = (int)$_SESSION['authId'];
	
	foreach($vars as $option){
		
		switch($option){
			case "1":
				/*Log Import status in file*/
				importInProgress("<br/>Manufacturer sync is in progress",false);
				/*Import And Update Manufacturers*/
				$manuf_list = 'SELECT `id`, TRIM(`manufacturer_name`) AS \'name\', TRIM(`ManufacturerFramesMasterID`) AS \'FDID\'
							  FROM `in_manufacturer_details`';
				$manufacturers = imw_query($manuf_list) or errorLog( imw_error() );
				
				$imedicManufacturers	= array();	/*Manufacturers without FramesData Id*/
				$framesDatamanufacturers= array();	/*Manufacturers with FramesData Id*/
				
				/*Preparre list of Manufacturers Linked to or not with FramesData*/
				while($manufacturer = imw_fetch_object($manufacturers)){
					
					/*Without FramesData Id*/
					if( $manufacturer->FDID == "" )
						$imedicManufacturers[$manufacturer->id] = $manufacturer->name;
					
					/*Having FramesData Id*/
					else
						$framesDatamanufacturers[$manufacturer->FDID] = (int)$manufacturer->id;
				}
				imw_free_result($manufacturers);
				
				/*Pull Updated List of Manufacturers from FramesData API*/
				$manufacturers = $obj->get('manufacturers');
				$manufacturers = $manufacturers->Manufacturers;
				
				/*Updated/Inserted and deactivated Manufactures Counter*/
				$uc=$ic=$dc=0;
				
				/*Traverse through the list of Manufacturers returned from FramesData*/
				foreach( $manufacturers as $manufacturer ){
					
					/*Manufacturer's Name*/
					$name	= $obj->htEscape($manufacturer->ManufacturerName);
					/*FramesData Id*/
					$fdId	= $obj->escape($manufacturer->ManufacturerFramesMasterID);
					/*Manufacturer's FramesData market*/
					$market	= $obj->escape($manufacturer->Market);
					$status	= $manufacturer->Status;
					
					$manufId = 0;
					
					if( $obj->match($name, $imedicManufacturers) ){
						
						/*Get iMedic Manufacturer's Id*/
						$key = array_search( stripslashes($name), $imedicManufacturers );
						if(!$key || $key==NULL || $key==''){
							$key = array_search(html_entity_decode(stripslashes($name)), $imedicManufacturers);
						}
						
						$manufId = (int)$key;
						
						$sql_manuf = 'UPDATE `in_manufacturer_details`
									SET 
										`ManufacturerFramesMasterID`=\''.$fdId.'\', 
										`Market`=\''.$market.'\', 
										`frames_chk`=1,
										`modified_date`=\''.date('Y-m-d').'\', 
										`modified_time`=\''.date('H:i:s').'\',
										`modified_by`='.$operator_id.'
									WHERE 
										`id`='.$manufId;
						imw_query($sql_manuf) or errorLog( imw_error() );
						$uc++;	/*Increase Updated Counter*/
					}
					
					/*Get Manufacturer id if match found in existing FramesData Manufacturers*/
					elseif( isset($framesDatamanufacturers[$fdId]) ){
						$manufId = $framesDatamanufacturers[$fdId];
					}
					else{
						
						/*Add New if Manufacturer does not exists*/
						$sql_manuf = 'INSERT INTO `in_manufacturer_details`
									SET
										`manufacturer_name` = \''.$name.'\',
										`ManufacturerFramesMasterID` = \''.$fdId.'\',
										`Market` = \''.$market.'\',
										`frames_chk` = 1,
										`entered_date` = \''.date('Y-m-d').'\', 
										`entered_time` = \''.date('H:i:s').'\',
										`entered_by` = '.$operator_id;
						if( imw_query($sql_manuf) ){
							$ic++;
							$manufId = imw_insert_id();
						}
						else
							errorLog( imw_error() );
					}
					
					/*Mark as deactivated if inventory does not exists and FramesData status is Discontinued*/
					if( $manufId > 0 && $status == 'D' ){
					
						$manufStockSql	= 'SELECT `id` FROM `in_item`
											WHERE 
												`manufacturer_id` = '.$manufId.' AND 
												`qty_on_hand` > 0 AND 
												`del_status` = 0';
						$manufStockResp	= imw_query( $manufStockSql );
						if( $manufStockResp && imw_num_rows($manufStockResp) == 0 ){
							
							$sqlManuf = 'UPDATE
											`in_manufacturer_details`
										SET
											`del_status` = 1,
											`modified_date`=\''.date('Y-m-d').'\',
											`modified_time`=\''.date('H:i:s').'\',
											`modified_by`='.$operator_id.'
										WHERE
											`id`='.$manufId;
							if( imw_query($sqlManuf) )
								$dc++;
							else
								errorLog( imw_error() );
						}
					}
					/*We are not updating existing FramesData Manufacturers*/
				}
				unset( $manufacturers );
				
				$log = "<br/><span class='success'>Manufacturers sync complete [inserted:$ic, updated:$uc, deactivated: $dc]</span>";
				importInProgress( $log, false );
				
				$log_sql = 'INSERT INTO `in_options`
							SET
								`opt_val`=\''.trim( $obj->escape($log) ).'\',
								`opt_type`=6,
								`module_id`=8,
								`entered_date`=\''.date('Y-m-d').'\',
								`entered_time`=\''.date('H:i:s').'\',
								`entered_by`='.$operator_id;
				imw_query($log_sql) or errorLog( imw_error() );
			break;
			
			case "2":
				/*Log import status*/
				importInProgress("<br/>Brands sync is in progress",false);
				
				/*Preparre list of Brands Linked to or not with FramesData*/
				$brandsList = 'SELECT `id`, TRIM(`frame_source`) AS \'name\', TRIM(`BrandFramesMasterID`) AS \'FDID\'
								FROM `in_frame_sources`';
				$brands = imw_query($brandsList);
				
				$imedicBrands		= array();	/*Brands without FramesData Id*/
				$framesDataBrands	= array();	/*Brands with FramesData Id*/
				
				while($brand = imw_fetch_assoc($brands)){
					
					/*Without FramesData Id*/
					if( $brand['FDID'] == '' )
						$imedicBrands[$brand['id']] = $brand['name'];
						
					/*Having FramesData Id*/
					elseif( $brand['FDID'] != '' ){
						$framesDataBrands[$brand['FDID']]['name']	= $brand['name'];
						$framesDataBrands[$brand['FDID']]['id']		= (int)$brand['id'];
					}
				}
				
				/*List all Manufacturers for Brands-manufacturers Association*/
				$manufacturers = array();
				$manufList = 'SELECT `id`, TRIM(`ManufacturerFramesMasterID`) AS \'manuf_id\'
								FROM 
									`in_manufacturer_details`
								WHERE 
									TRIM(`ManufacturerFramesMasterID`) != \'\'
									AND `frames_chk` = 1
									AND `del_status` != 2';
				
				$manufListResp = imw_query($manufList);
				if( $manufListResp && imw_num_rows($manufListResp) > 0 ){
					
					while( $row = imw_fetch_object($manufListResp) ){
						
						$manufacturers[$row->manuf_id] = $row->id;
					}
				}
				imw_free_result( $manufListResp );
				
				
				/*Pull Updated list of Brands from FramesData*/
				$brands = $obj->get('brands');
				$brands = $brands->Brands;
				
				/*Conter of brands Ignored because associated manufacturer does not exists*/
				$ignored = 0;
				
				/*Inserted/Updated/Deactivated counter*/
				$ic=$uc=$dc=0;
				
				/*Traverse through the list of Brands returned from FramesData*/
				foreach( $brands as $brand ){
					
					$mfId	= $obj->escape( $brand->ManufacturerFramesMasterID );
					$fdId	= $obj->escape( $brand->BrandFramesMasterID );
					$name	= $obj->htEscape( $brand->BrandName );
					$market	= $obj->escape( $brand->Market );
					$status = $brand->Status;
					
					if( !isset($manufacturers[$mfId]) ){
						$ignored++;
						continue;
					}
					
					$manufId = (int)$manufacturers[$mfId];	/*Brand's Manufacturer Id*/
					$brandId = 0;
					
					/*Updated if matched in Brands without FramesData Id*/
					if( $obj->match($name, $imedicBrands) ){
						
						/*Get iMedic Manufacturer's Id*/
						$key = array_search( stripslashes($name), $imedicBrands );
						
						if(!$key || $key==NULL || $key==''){
							
							$key = array_search(html_entity_decode(stripslashes($name)), $imedicBrands);
						}
						$brandId = (int)$key;
						
						$sqlBrand = 'UPDATE
										`in_frame_sources`
									SET
										`BrandFramesMasterID` = \''.$fdId.'\', 
										`Market` = \''.$market.'\', 
										`modified_date` = \''.date('Y-m-d').'\', 
										`modified_time` = \''.date('H:i:s').'\',
										`modified_by` = '.$operator_id.'
									WHERE 
										`id`='.$brandId;
						
						imw_query( $sqlBrand ) or errorLog( imw_error() );
						$uc++;	/*Increase Updated Counter*/
					}
					
					/*Get Brand id if match found in existing FramesData Brands*/
					elseif( isset($framesDataBrands[$fdId]) ){
						$brandId = $framesDataBrands[$fdId]['id'];
					}
					
					/*Add new Brand if no match found in existing data*/
					else{
						
						$sqlBrand = 'INSERT INTO `in_frame_sources`
									SET
										`frame_source`=\''.$name.'\',
										`BrandFramesMasterID` = \''.$fdId.'\',
										`Market` = \''.$market.'\',
										`entered_date` = \''.date('Y-m-d').'\', 
										`entered_time` = \''.date('H:i:s').'\',
										`entered_by` = '.$operator_id;
						
						if( imw_query($sqlBrand) ){
							$brandId = imw_insert_id();
							$ic++;	/*Increase inserted Counter*/
						}
						else
							errorLog( imw_error() );
					}
					
					/*Associate Brand with manufacturer if not already associated*/
					if( $brandId > 0 ){
						
						/*Mark as deactivated if inventory does not exists and FramesData status is Discontinued*/
						if( $status == 'D' ){
							
							$brandStockSql	= 'SELECT `id` FROM `in_item`
												WHERE
													`brand_id`='.$brandId.' AND
													`qty_on_hand`>0 AND
													`del_status`=0';
							$brandStockResp	= imw_query( $brandStockSql );
							if( $brandStockResp && imw_num_rows($brandStockResp) == 0 ){
								
								$sqlManuf = 'UPDATE `in_frame_sources`
											SET
												`del_status` = 1,
												`modified_date` = \''.date('Y-m-d').'\',
												`modified_time` = \''.date('H:i:s').'\',
												`modified_by` = '.$operator_id.'
											WHERE
												`id`='.$brandId;
								if( imw_query($sqlManuf) ){
									$dc++;
								}
								else
									errorLog( imw_error() );
							}
						}
						
						$brandManufSql = 'SELECT `id`
										FROM 
											`in_brand_manufacture`
										WHERE
											`brand_id`='.$brandId.' AND
											`manufacture_id`='.$manufId;
						$brandManufResp = imw_query( $brandManufSql );
						if( $brandManufResp && imw_num_rows($brandManufResp) == 0 ){
							
							$brandManufSql = 'INSERT INTO `in_brand_manufacture`
											SET
												`brand_id`='.$brandId.',
												`manufacture_id`='.$manufId;
							imw_query( $brandManufSql ) or errorLog( imw_error() );
						}
					}
				}
				
				$log = "<br/><span class='success'>Brands sync complete [inserted:$ic, updated:$uc, deactivated:$dc]</span>";
				importInProgress( $log, false );
				
				$log_sql = 'INSERT INTO `in_options`
							SET
								`opt_val`=\''.trim( $obj->escape($log) ).'\',
								`opt_type`=7,
								`module_id`=8,
								`entered_date`=\''.date('Y-m-d').'\',
								`entered_time`=\''.date('H:i:s').'\',
								`entered_by`='.$operator_id;
				imw_query($log_sql) or errorLog( imw_error() );
			break;
			
			case "3":
				/*Log import status in file*/
				importInProgress("<br/>Colors sync is in progress",false);
				
				/*List of existing colors*/
				$colorList	= 'SELECT `id`, TRIM(`color_name`) AS \'name\', TRIM(`import_id`) AS \'fdId\' FROM `in_frame_color` WHERE `del_status`=0';
				$colors		= imw_query( $colorList ) or errorLog( imw_error() );
				
				/*Colors without FramesData Id*/
				$imedicwareColors	= array();
				/*Colors with FramesData Id*/
				$framesDataColors	= array();
				
				while( $row = imw_fetch_object($colors) )
				{
					if( $row->fdId == '' )
						$imedicwareColors[$row->id]	= $row->name;
					else
						$framesDataColors[$row->fdId]	= $row->id;
				}
				imw_free_result($colors);
				
				/*Pull updated list of colors from FramesData*/
				$colors = $obj->get('colors');
				$colors = $colors->Colors;
				
				/*Inserted/Updated counter*/
				$ic=$uc = 0;
				
				/*Traverse list of colors returnet from FramesData*/
				foreach( $colors as $color ){
					
					/*Colors's Name*/
					$name	= $obj->htEscape($color->ColorValue);
					/*FramesData Id*/
					$fdId	= $obj->escape($color->ColorID);
					
					/*If found in existing colors without FramesData Id*/
					if( $obj->match($name, $imedicwareColors) ){
						
						/*Get iMedic Color Id*/
						$colorId = array_search( stripslashes($name), $imedicwareColors );
						if(!$colorId || $colorId==NULL || $colorId==''){
							$colorId = array_search(html_entity_decode(stripslashes($name)), $imedicwareColors);
						}
						$colorId = (int)$colorId;
						
						$colorSql = 'UPDATE `in_frame_color`
									SET
										`import_id` = \''.$fdId.'\',
										`modified_date` = \''.date('Y-m-d').'\', 
										`modified_time` = \''.date('H:i:s').'\',
										`modified_by` = '.$operator_id.'
									WHERE
										`id`='.$colorId;
						
						if( imw_query($colorSql) )
							$uc++;
						else
							errorLog( imw_error() );
					}
					
					/*If not match fount in FramesData colors also, then Add new color*/
					elseif( !isset($framesDataColors[$fdId]) ){
						
						$colorSql = 'INSERT INTO `in_frame_color`
										SET 
											`color_name` = \''.$name.'\',
											`import_id` = \''.$fdId.'\',
											`entered_date` = \''.date('Y-m-d').'\',
											`entered_time` = \''.date('H:i:s').'\',
											`entered_by` = '.$operator_id;
						if( imw_query($colorSql) )
							$ic++;
						else
							errorLog( imw_error() );
					}
				}
				
				$log = "<br/><span class='success'>Color sync complete [inserted:$ic, updated:$uc]</span>";
				importInProgress( $log, false );
				
				$log_sql = 'INSERT INTO `in_options`
							SET
								`opt_val` = \''.trim( $obj->escape($log) ).'\',
								`opt_type` = 8,
								`module_id` = 8,
								`entered_date` = \''.date('Y-m-d').'\',
								`entered_time` = \''.date('H:i:s').'\',
								`entered_by` = '.$operator_id;
				imw_query( $log_sql ) or errorLog( imw_error() );
			break;
			
			case "4":
				
				$startDate	= ( isset($_REQUEST['startDate']) && $_REQUEST['startDate']!='' ) ? $_REQUEST['startDate'] : false;
				$endDate	= ( isset($_REQUEST['endDate']) && $_REQUEST['endDate']!='' ) ? $_REQUEST['endDate'] : false;
				
				$dateFilter = false;
				if( $startDate && $endDate )
					$dateFilter = true;
				
				/*Log import status in file*/
				importInProgress("<br/>Frames sync is in progress",false);
				
				$collectionsDir = $GLOBALS['DIR_PATH'].'/interface/patient_interface/uploaddir/framesCollections';
				
				if( !isset($_REQUEST['fileDl']) && !$dateFilter ){
					/*Fetch List of Collections for the Manufaccturers*/
					$manufacturersSql = 'SELECT `ManufacturerFramesMasterID` AS \'mfId\'
										FROM 
											`in_manufacturer_details`
										WHERE
											`frames_chk` = 1 AND
											`del_status` = 0 AND
											`ManufacturerFramesMasterID` != \'\'';
					$manufaturersResp = imw_query( $manufacturersSql );
					if( $manufaturersResp && imw_num_rows( $manufaturersResp ) > 0 ){
						
						/*Truncate Collections Table*/
						$collectionsTrunc = 'TRUNCATE TABLE `xml_frames_collections`';
						imw_query( $collectionsTrunc ) or errorLog( imw_error() );
						
						/*FramesData Deleted Brands*/
						$disabledBrands = array();
						$sqlDelBrands = 'SELECT `BrandFramesMasterID` AS \'fdId\' FROM `in_frame_sources`
										WHERE `BrandFramesMasterID` !=\'\' AND `del_status` != 0';
						$brandsResps = imw_query($sqlDelBrands);
						if($brandsResps && imw_num_rows($brandsResps)){
							while($disRow = imw_fetch_object($brandsResps)){
								array_push($disabledBrands, $disRow->fdId);
							}
						}
						imw_free_result($brandsResps);
						
						
						while( $row = imw_fetch_object($manufaturersResp) ){
							
							$collections = $obj->get('manufacturersColection', $row->mfId);
							$collections = $collections->Collections;
							
							/*Traverse the list of collections pulled from FramesData for the Manufacturers*/
							foreach( $collections as $collection ){
								
								/*Collection Name*/
								$name	= $obj->htEscape($collection->CollectionName);
								/*FramesData Id*/
								$fdId	= $obj->escape($collection->CollectionFramesMasterID);
								/*Manufacturer's FramesData market*/
								$market	= $obj->escape($collection->Market);
								/*Colletion's Current Status*/
								$status	= $collection->Status;
								/*ManufacturerId*/
								$mfId	= $obj->escape($collection->ManufacturerFramesMasterID);
								/*BrandId*/
								$bId	= $obj->escape($collection->BrandFramesMasterID);
								
								if(in_array($bId, $disabledBrands)){
									$status = 'D';
								}
								
								$colloectionSql = 'INSERT INTO `xml_frames_collections`
													SET
														`CollectionFramesMasterID` = \''.$fdId.'\',
														`CollectionName` = \''.$name.'\',
														`ManufacturerFramesMasterID` = \''.$mfId.'\',
														`BrandFramesMasterID` = \''.$bId.'\',
														`Market` = \''.$market.'\',
														`Status` = \''.$status.'\'';
								imw_query( $colloectionSql ) or errorLog( imw_error() );
							}
						}
					}
					imw_free_result( $manufaturersResp );
					unset($disabledBrands);
					
					/*Direcotry for Temperory Storage of Collections Data*/
					if( !is_dir($collectionsDir) ){
						mkdir( $collectionsDir, 0755, true );
						chown( $collectionsDir, 'apache' );
					}
				}
				
				/*List FramesData Manufacturers*/
				$manufacturers = array();
				$manufacturerSql = 'SELECT `id`, TRIM(`ManufacturerFramesMasterID`) AS \'mfId\'
									FROM
										`in_manufacturer_details`
									WHERE
										`del_status` != 2 AND
										`ManufacturerFramesMasterID` != \'\'';
				$manufactureResp = imw_query( $manufacturerSql ) or errorLog( imw_error() );
				if( $manufactureResp && imw_num_rows($manufactureResp) > 0 ){
					while( $row = imw_fetch_object($manufactureResp) ){
						$manufacturers[$row->mfId] = $row->id;
					}
				}
				imw_free_result( $manufactureResp );
				
				/*List FramesData Brands*/
				$brands	= array();
				$brandSql = 'SELECT `id`, TRIM(`BrandFramesMasterID`) AS \'bId\'
									FROM
										`in_frame_sources`
									WHERE
										`del_status` != 2 AND
										`BrandFramesMasterID` != \'\'';
				$brandResp = imw_query( $brandSql ) or errorLog( imw_error() );
				if( $brandResp && imw_num_rows($brandResp) > 0 ){
					while( $row = imw_fetch_object($brandResp) ){
						$brands[$row->bId] = $row->id;
					}
				}
				imw_free_result( $brandResp );
				
				/*List FramesData Colors*/
				$colors		= array();
				$colorsFDId	= array();
				$colorSql = 'SELECT `id`, TRIM(`color_name`) AS \'name\', `import_id` AS \'fdId\'
									FROM
										`in_frame_color`
									WHERE
										`del_status` != 2';
				$colorResp = imw_query( $colorSql ) or errorLog( imw_error() );
				if( $colorResp && imw_num_rows($colorResp) > 0 ){
					while( $row = imw_fetch_object($colorResp) ){
						$colors[$row->id]		= $row->name;
						$colorsFDId[$row->id]	= $row->fdId;
					}
				}
				imw_free_result( $colorResp );
				
				/*FramesData Styles*/
				$styles		= array();
				$styleStatus= array();
				$stylesSql	= 'SELECT `id`, `StyleFramesMasterID` AS \'fdId\', del_status
								FROM
									`in_frame_styles`';
				$stylesResp = imw_query( $stylesSql ) or errorLog( imw_error() );
				if( $stylesResp && imw_num_rows($stylesResp) > 0 ){
					while( $row = imw_fetch_object($stylesResp) ){
						$styles[$row->fdId]		= $row->id;
						$styleStatus[$row->fdId]= $row->del_status;
					}
				}
				imw_free_result( $stylesResp );
				
				/*FramesData Types*/
				$types			= array();
				$typesStatus	= array();
				$typesSql	= 'SELECT `id`, `type_name` AS \'name\', del_status
								FROM
									`in_frame_types`';
				$typesResp = imw_query( $typesSql ) or errorLog( imw_error() );
				if( $typesResp && imw_num_rows($typesResp) > 0 ){
					while( $row = imw_fetch_object($typesResp) ){
						$types[$row->id]		= $row->name;
						$typesStatus[$row->id]	= $row->del_status;
					}
				}
				imw_free_result( $typesResp );
				
				/*FramesData Shapes*/
				$shapes			= array();
				$shapesStatus	= array();
				$shapesSql	= 'SELECT `id`, `shape_name` AS \'name\', del_status
								FROM
									`in_frame_shapes`';
				$shapesResp = imw_query( $shapesSql ) or errorLog( imw_error() );
				if( $shapesResp && imw_num_rows($shapesResp) > 0 ){
					while( $row = imw_fetch_object($shapesResp) ){
						$shapes[$row->id]		= $row->name;
						$shapesStatus[$row->id]	= $row->del_status;
					}
				}
				imw_free_result( $shapesResp );
				
				/*Insert/Update Counter*/
				$ic = ( isset($_REQUEST['ic']) ) ? (int)$_REQUEST['ic'] : 0;
				$uc = ( isset($_REQUEST['uc']) ) ? (int)$_REQUEST['uc'] : 0;
				$noBrand = ( isset($_REQUEST['noBrand']) ) ? (int)$_REQUEST['noBrand'] : 0;
				$noManuf = ( isset($_REQUEST['noManuf']) ) ? (int)$_REQUEST['noManuf'] : 0;
				$blankUpc = ( isset($_REQUEST['blankUpc']) ) ? (int)$_REQUEST['blankUpc'] : 0;
				$discontinued = ( isset($_REQUEST['discontinued']) ) ? (int)$_REQUEST['discontinued'] : 0;
				$fileDl = ( isset($_REQUEST['fileDl']) ) ? (bool)$_REQUEST['fileDl'] : true;
				
				/*Download the Json Files* /
				if($fileDl){
					$sqlCollections = 'SELECT `id`, `CollectionFramesMasterID` AS \'fdId\' , `Status`
										FROM `xml_frames_collections`
										WHERE `updated` = 0 AND `Status`=\'A\' LIMIT 50';
					$respCollections = imw_query( $sqlCollections ) or errorLog( imw_error() );
					$collectionCount = imw_num_rows($respCollections);
					if( $respCollections && imw_num_rows($respCollections) > 0 ){
						
						/*Direcotry for Temperory Storage of Collections Data* /
						if( !is_dir($collectionsDir) ){
							mkdir( $collectionsDir, 0755, true );
							chown( $collectionsDir, 'apache' );
						}
						
						while( $row = imw_fetch_object($respCollections) ){
							
							/*Pul the updated list Styles and Configurations for the Collection* /
							$styleConfigurations = $obj->get('styleConfigration', $row->fdId);
							/*Put the contents in JSON files on the server for fast processing of the data later* /
							$fWriteStatus = file_put_contents( $collectionsDir.'/'.$row->fdId.'.json', $styleConfigurations );
							if( $fWriteStatus !== false ){
								$collectionStatus = 'UPDATE `xml_frames_collections`
													SET
														`updated` = 1
													WHERE
														`id` = '.$row->id;
								imw_query( $collectionStatus ) or errorLog( imw_error() );
							}
						}
					}
					imw_free_result( $respCollections );
					if($collectionCount == 50){
						$noComplete = true;
						$msg = 'Please do not hit back button or refresh page untill sync complete message displays.<br/>';
						$msg .= '<br/>Sync begin';
						importInProgress( $msg, true );
						
						echo "<script type='text/javascript'>window.location.href='framesImport.php?option[]=4&sync_now=Sync Now&uc=".$uc."&ic=".$ic."&noBrand=".$noBrand."&noManuf=".$noManuf."&blankUpc=".$blankUpc."&discontinued=".$discontinued."&fileDl=true';</script>";
						exit;
					}
				}/**/
				
				/*Update Data*/
				$sqlCollections = 'SELECT `id`, `CollectionFramesMasterID` AS \'fdId\' , `Status`
									FROM `xml_frames_collections`
									WHERE `updated` = 0 AND `Status` = \'A\' LIMIT 50';
				$respCollections = imw_query( $sqlCollections ) or errorLog( imw_error() );
				$collectionCount = imw_num_rows($respCollections);
				if( ($respCollections && $collectionCount > 0) || $dateFilter ){
					
					if( !$dateFilter ){
						$row = imw_fetch_object($respCollections);
					}
					
					do{
						$styleConfigurations = '';
						/*Pull the updated list Styles and Configurations for the Collection*/
						if($dateFilter){
							$otherOptions = array();
							$otherOptions['startDate'] = $startDate;
							$otherOptions['endDate'] = $endDate;
							
							$styleConfigurations = $obj->get('styleConfigration', false, $otherOptions);
						}
						else
							$styleConfigurations = $obj->get('styleConfigration', $row->fdId);
						
						/*JSON file path and name* /
						$jsonFileName = $collectionsDir.'/'.(($dateFilter)?'updated':$row->fdId).'.json';
						/*Put the contents in JSON files on the server for fast processing of the data later* /
						file_put_contents( $jsonFileName, $styleConfigurations );/** /
						if( file_exists($jsonFileName) ){
							$styleConfigurations = file_get_contents( $jsonFileName );
						}
						/**/
						
						/*Check if json file exists for the collection*/
						if( $styleConfigurations != '' ){
							
							/*Collection's Data*/
							$styleConfig = json_decode( $styleConfigurations );
							$styleConfig = $styleConfig->StyleConfigurations;
							
							foreach( $styleConfig as $style ){
								
								$manufFdId	= $obj->escape( $style->ManufacturerFramesMasterID );
								$brandfFdId	= $obj->escape( $style->BrandFramesMasterID );
								$stylefFdId	= $obj->escape( $style->StyleFramesMasterID );
								$styleName	= $obj->htEscape( $style->StyleName );
								$collectId	= $obj->htEscape( $style->CollectionFramesMasterID );
								$itemName	= $obj->htEscape( $style->CollectionName );
								$itemGender	= $obj->htEscape( strtolower($style->Gender) );
								
								/*Skip processing if manufacturer not found in the list*/
								if( !isset($manufacturers[$manufFdId]) ){
									$noManuf++;
									continue;
								}
								/*Skip processing if brand not found in the list*/
								if( !isset($brands[$brandfFdId]) ){
									$noBrand++;
									continue;
								}
								
								/*FramesData status of the Style*/
								$styleStatusFd= $style->StyleStatus;
								
								$imwStyleid	= ( isset($styles[$stylefFdId]) ) ? $styles[$stylefFdId]: false;
								
								/*Skip the style processing if Style is not active in imwDB*/
								if( $imwStyleid && $styleStatus[$stylefFdId] != '0' ){
									continue;
								}
								
								/*If style is discontinued, then mark the Style and items as deleted if No Inventory exists.*/
								if( $styleStatusFd == 'D' ){
									
									if( $imwStyleid ){
										$stylStockSql = 'SELECT `id` FROM `in_item`
														WHERE
															`StyleFramesMasterID` = \''.$stylefFdId.'\' AND
															`qty_on_hand` > 0 AND
															`del_status` = 0';
										$styleStockResp = imw_query( $stylStockSql ) or errorLog( imw_error()."\tin_item Select" );
										$itemsCount		= imw_num_rows($styleStockResp);
										if( $styleStockResp && $itemsCount == 0 ){
											
											/*mark the items as deleted*/
											$itemsDelSql = 'UPDATE `in_item`
															SET
																`del_status` = 1,
																`del_date` = \''.date('Y-m-d').'\',
																`del_time` = \''.date('H:i:s').'\',
																`del_by` = '.$operator_id.'
															WHERE
																`StyleFramesMasterID` = \''.$stylefFdId.'\' AND
																`qty_on_hand` = 0 AND
																`del_status` = 0';
											
											if( imw_query($itemsDelSql) ){
												$discontinued = $discontinued+imw_affected_rows();
											}
											else{
												errorLog( $itemsDelSql );
											}
											
											/*mark style as deleted*/
											$styleDelSql = 'UPDATE `in_frame_styles`
															SET
																`del_status`=1,
																`modified_date` = \''.date('Y-m-d').'\',
																`modified_time` = \''.date('H:i:s').'\',
																`modified_by` = '.$operator_id.'
															WHERE
																`StyleFramesMasterID` = \''.$stylefFdId.'\' AND
																`del_status` = 0';
											imw_query( $styleDelSql ) or errorLog( imw_error()."\tin_item Update" );
										}
									}
									continue;
								}
								/*end discontinued Style Action*/
								
								/*Add New Frame Style if not found in previously added styles*/
								if( !$imwStyleid ){
									
									$styleSql = 'INSERT INTO `in_frame_styles`
												SET
													`style_name` = \''.$styleName.'\',
													`StyleFramesMasterID` = \''.$stylefFdId.'\',
													`CollectionFramesMasterID` = \''.(($dateFilter)?$collectId:$row->fdId).'\',
													`entered_date` = \''.date('Y-m-d').'\',
													`entered_time` = \''.date('H:i:s').'\',
													`entered_by` = '.$operator_id;
									$styleResp = imw_query( $styleSql );
									if( $styleResp ){
										$imwStyleid = imw_insert_id();
										$styles[$stylefFdId] = $imwStyleid;
										$styleStatus[$stylefFdId] = '0';
									}
									else{
										errorLog( imw_error() );
									}
								}
								/*End style insertion*/
								
								/*Brand-Style Association*/
								if( $imwStyleid ){
									
									/*check if entry already exists*/
									$styleBrandSql = 'SELECT `id`
													FROM
														`in_style_brand`
													WHERE
														`style_id` = '.$imwStyleid.' AND
														`brand_id` = '.((int)$brands[$brandfFdId]);
									$styleBrandSql = imw_query( $styleBrandSql );
									if( $styleBrandSql ){
										if( imw_num_rows($styleBrandSql)==0 ){
											
											$styleBrandSql = 'INSERT INTO `in_style_brand`
															SET
																`style_id` = '.$imwStyleid.',
																`brand_id` = '.((int)$brands[$brandfFdId]);
											imw_query( $styleBrandSql ) or errorLog( $styleBrandSql );
										}
									}
									else
										errorLog( imw_error() );
								}
								
								/*Frame type*/
								$typeName	= $obj->htEscape( $style->ProductGroup );
								$imwtypeId	= 0;
								
								if( $typeName != '' ){
									if( $obj->match($typeName, $types) ){
										
										/*Get iMedic Type Id*/
										$imwtypeId = array_search( stripslashes($typeName), $types );
										if(!$imwtypeId || $imwtypeId==NULL || $imwtypeId==''){
											$imwtypeId = array_search(html_entity_decode(stripslashes($typeName)), $types);
										}
										$imwtypeId = (int)$imwtypeId;
										
										/*Set TypeId to 0 if it's del_status is 2*/
										if( $typesStatus[$imwtypeId] == '2' ){
											$imwtypeId = 0;
										}
									}
									else{
										
										/*Add new Type*/
										$typeSql = 'INSERT INTO `in_frame_types`
													SET
														`type_name` = \''.$typeName.'\',
														`StyleFramesMasterID` = \''.$stylefFdId.'\',
														`CollectionFramesMasterID` = \''.(($dateFilter)?$collectId:$row->fdId).'\',
														`entered_date` = \''.date('Y-m-d').'\',
														`entered_time` = \''.date('H:i:s').'\',
														`entered_by` = '.$operator_id;
										$typeResp = imw_query( $typeSql );
										if( $typeResp ){
											$imwtypeId = imw_insert_id();
											$types[$imwtypeId]		= $typeName;
											$typesStatus[$imwtypeId]= '0';
										}
										else{
											errorLog( imw_error() );
										}
									}
								}
								/*End frame type*/
								
								/*Frame Shape*/
								$shapeName	= $obj->htEscape( $style->FrameShape );
								$imwShapeId	= 0;
								if( $shapeName != '' ){
									
									if( $obj->match($shapeName, $shapes) ){
										
										/*Get iMedic Type Id*/
										$imwShapeId = array_search( stripslashes($shapeName), $shapes );
										if(!$imwShapeId || $imwShapeId==NULL || $imwShapeId==''){
											$imwShapeId = array_search(html_entity_decode(stripslashes($shapeName)), $shapes);
										}
										$imwShapeId = (int)$imwShapeId;
										
										/*Set TypeId to 0 if it's del_status is 2*/
										if( $shapesStatus[$imwShapeId] == '2' ){
											$imwShapeId = 0;
										}
									}
									else{
										
										/*Add new Type*/
										$shapeSql = 'INSERT INTO `in_frame_shapes`
													SET
														`shape_name` = \''.$shapeName.'\',
														`StyleFramesMasterID` = \''.$stylefFdId.'\',
														`CollectionFramesMasterID` = \''.(($dateFilter)?$collectId:$row->fdId).'\',
														`entered_date` = \''.date('Y-m-d').'\',
														`entered_time` = \''.date('H:i:s').'\',
														`entered_by` = '.$operator_id;
										$shapeResp = imw_query( $shapeSql );
										if( $shapeResp ){
											$imwShapeId = imw_insert_id();
											$shapes[$imwShapeId]	= $shapeName;
											$shapesStatus[$imwShapeId]= '0';
										}
										else{
											errorLog( imw_error() );
										}
									}
									
								}
								/*End frame shape*/
								
								/*Frames/Items*/
								$fdIems = $style->Configurations;
								if( count($fdIems) == 0 )
									continue;
								
								foreach( $fdIems as $fdItem ){
									
									$itemFdId	= $obj->htEscape( $fdItem->ConfigurationFramesMasterID );
									$itemFdFpc	= $obj->htEscape( $fdItem->ConfigurationFPC );
									$itemFdUpc	= $obj->htEscape( $fdItem->UPC );
									$itemStatus	= $fdItem->ConfigurationStatus;
									$itemA		= $obj->htEscape( $fdItem->A );
									$itemB		= $obj->htEscape( $fdItem->B );
									$itemED		= $obj->htEscape( $fdItem->ED );
									$itemDBL	= $obj->htEscape( $fdItem->DBL );
									$itemTemple	= $obj->htEscape( $fdItem->TempleLength );
									$itemBridge	= $obj->htEscape( $fdItem->BridgeSize );
									$itemWSCost	= $obj->htEscape( $fdItem->CompletePrice );
									$priceModDate = $fdItem->PriceLastModifiedOn;
									$priceModFlag = $fdItem->ChangedPrice;
									
									
									if( $priceModDate!='' ){
										$priceModDate1	= explode('/', $priceModDate);
										$priceModDate	= $priceModDate1[2].'-'.$priceModDate1[0].'-'.$priceModDate1[1];
									}
									else
										$priceModDate = '000-00-00';
									
									
									$itemColor		= trim($fdItem->FrameColor, '", \'');
									$itemColorCode	= $fdItem->FrameColorCode;
									$itemColorGroup	= $fdItem->FrameColorGroup;
									
									$imwItemId		= false;
									$imwItemStatus	= '0';
									$imwPriceModDate= '0000-00-00';
									
									$itemIdSql	= 'SELECT `id`, del_status, `fd_price_modified_on`
													FROM `in_item`
													WHERE `upc_code` = \''.$itemFdUpc.'\' AND `module_type_id` = 1';
									$itemIdResp = imw_query($itemIdSql) or errorLog( imw_error() );
									if( $itemIdResp && imw_num_rows($itemIdResp) > 0 ){
										
										$imwItemResp= imw_fetch_assoc($itemIdResp);
										$imwItemId		= (int)$imwItemResp['id'];
										$imwItemStatus	= $imwItemResp['del_status'];
										$imwPriceModDate = $imwItemResp['fd_price_modified_on'];
									}
									
									if( $imwItemId && $imwItemStatus != '0' )
										continue;
									
									if( $itemStatus == 'D' ){
										
										if( $imwItemId && $imwItemStatus == 0 ){
											
											$itemSql = 'UPDATE `in_item`
														SET
															`del_status` = 1,
															`del_date` = \''.date('Y-m-d').'\',
															`del_time` = \''.date('H:i:s').'\',
															`del_by` = '.$operator_id.'
														WHERE
															`qty_on_hand` == 0 AND
															`id`= '.$imwItemId;
											if( imw_query($itemSql) ){
												$discontinued = $discontinued+imw_affected_rows();
											}
											else
												errorLog( imw_error() );
										}
										continue;
									}
									
									/*Frame Color*/
									/*If color name is blank, then set color name to colorGroup*/
									if( $itemColor == '' ){
										$itemColor = $itemColorGroup;
									}
									
									$imwColorId = '';
									if( $itemColor !='' ){
										if( $obj->match($itemColor, $colors) ){
											/*Get iMedic Color Id*/
											$imwColorId = array_search( stripslashes($itemColor), $colors );
											if(!$imwColorId || $imwColorId==NULL || $imwColorId==''){
												$imwColorId = array_search(html_entity_decode(stripslashes($itemColor)), $colors);
											}
											$imwColorId = $imwColorId;
										}
										else{
											/*Add New Color*/
											$colorSql = 'INSERT INTO `in_frame_color`
															SET 
																`color_name` = \''.$itemColor.'\',
																`color_code` = \''.$itemColorCode.'\',
																`entered_date` = \''.date('Y-m-d').'\',
																`entered_time` = \''.date('H:i:s').'\',
																`entered_by` = '.$operator_id;
											if( imw_query($colorSql) ){
												$imwColorId = imw_insert_id();
												$colors[$imwColorId] = $itemColor;
											}
											else
												errorLog( imw_error() );
										}
									}
									/*End Frame Color*/
									
									if( $itemFdUpc=='' ){
										$blankUpc++;
										continue;
									}
									
									/*Add New Item*/
									if( !$imwItemId ){
										$itemSql = 'INSERT INTO `in_item`
													SET
														`manufacturer_id` = '.((int)$manufacturers[$manufFdId]).',
														`module_type_id` = 1,
														`brand_id` = '.((int)$brands[$brandfFdId]).',
														`upc_code` = \''.$itemFdUpc.'\',
														`name` = \''.$itemName.'\',
														`type_id` = '.$imwtypeId.',
														`frame_style` = '.$imwStyleid.',
														`frame_shape` = '.$imwShapeId.',
														`a` = \''.$itemA.'\',
														`b` = \''.$itemB.'\',
														`ed` = \''.$itemED.'\',
														`dbl` = \''.$itemDBL.'\',
														`temple` = \''.$itemTemple.'\',
														`bridge` = \''.$itemBridge.'\',
														`gender` = \''.$itemGender.'\',
														`color` = \''.$imwColorId.'\',
														`wholesale_cost` = \''.$itemWSCost.'\',
														`color_code` = \''.$itemColorCode.'\',
														`entered_date` = \''.date('Y-m-d').'\',
														`entered_time` = \''.date('H:i:s').'\',
														`entered_by` = '.$operator_id.',
														`CollectionFramesMasterID` = \''.(($dateFilter)?$collectId:$row->fdId).'\',
														`StyleFramesMasterID` = \''.$stylefFdId.'\',
														`ConfigurationFramesMasterID` = \''.$itemFdId.'\',
														`ConfigurationFPC` = \''.$itemFdFpc.'\',
														`fd_price_modified_on` = \''.$priceModDate.'\',
														`fd_price_change_alert` = 0';
										if( imw_query($itemSql) )
											$ic++;	
										else
											errorLog( $itemSql."\n\n" );
									}
									else{
										$date1 = date_create($imwPriceModDate);
										$date2 = date_create($priceModDate);
										
										$itemSql = 'UPDATE `in_item` SET
													`CollectionFramesMasterID` = \''.(($dateFilter)?$collectId:$row->fdId).'\',
													`StyleFramesMasterID` = \''.$stylefFdId.'\',
													`ConfigurationFramesMasterID` = \''.$itemFdId.'\',
													`ConfigurationFPC` = \''.$itemFdFpc.'\',
													`color` = \''.$imwColorId.'\',
													`color_code` = \''.$itemColorCode.'\'';
										
										/*Update All Details* /
											$itemSql .= ', `manufacturer_id` = '.((int)$manufacturers[$manufFdId]).',
														`module_type_id` = 1,
														`brand_id` = '.((int)$brands[$brandfFdId]).',
														`upc_code` = \''.$itemFdUpc.'\',
														`name` = \''.$itemName.'\',
														`type_id` = '.$imwtypeId.',
														`frame_style` = '.$imwStyleid.',
														`frame_shape` = '.$imwShapeId.',
														`a` = \''.$itemA.'\',
														`b` = \''.$itemB.'\',
														`ed` = \''.$itemED.'\',
														`dbl` = \''.$itemDBL.'\',
														`temple` = \''.$itemTemple.'\',
														`bridge` = \''.$itemBridge.'\',
														`gender` = \''.$itemGender.'\',
														`color` = \''.$imwColorId.'\',
														`wholesale_cost` = \''.$itemWSCost.'\',
														`color_code` = \''.$itemColorCode.'\',
														`entered_date` = \''.date('Y-m-d').'\',
														`entered_time` = \''.date('H:i:s').'\',
														`entered_by` = '.$operator_id.'
														`modified_date` = \''.date('Y-m-d').'\',
														`modified_time` = \''.date('H:i:s').'\',
														`modified_by` = '.$operator_id;
										/*End Update All Details*/
										
										/*Update existing Items*/
										if( $priceModFlag==true || $date1 < $date2 ){
											$itemSql .= ', `fd_price_temp`=\''.$itemWSCost.'\',
														`fd_price_change_alert` = 1,
														`fd_price_modified_on` = \''.$priceModDate.'\'';
											/**/
											$itemSql .= ', `modified_date` = \''.date('Y-m-d').'\',
														`modified_time` = \''.date('H:i:s').'\',
														`modified_by` = '.$operator_id;
											/**/
										}
										$itemSql .= ' WHERE `id`='.$imwItemId;
										
										$itemUpdStatus = imw_query($itemSql);
										if( $itemUpdStatus ){
											$uc = (int)$uc+imw_affected_rows();
										}
										else
											errorLog( $itemSql );
									}
								}
								/*End Frames/Items*/
							}
							/*Mark file status Processed*/
							if(!$dateFilter){
								$collectionStatus = 'UPDATE `xml_frames_collections`
													SET
														`updated` = 2
													WHERE
														`id` = '.$row->id;
								imw_query( $collectionStatus ) or errorLog( $collectionStatus );
							}
						}
						else{
							if(!$dateFilter){
								/*Mark file status no found*/
								$collectionStatus = 'UPDATE `xml_frames_collections`
													SET
														`updated` = 3
													WHERE
														`id` = '.$row->id;
								imw_query( $collectionStatus ) or errorLog( $collectionStatus );
							}
						}
					} while( ($row = imw_fetch_object($respCollections)) && !$dateFilter );
				}
				imw_free_result( $respCollections );
				
				/*End Loop for 5 Days untill current Date*/
				if($collectionCount == 50 && !$dateFilter){
					$noComplete = true;
					$msg = 'Please do not hit back button or refresh page untill sync complete message displays.<br/>';
					$msg .= '<br/>Sync begin';
					importInProgress( $msg, true );
					
					echo "<script type='text/javascript'>window.location.href='framesImport.php?option[]=4&sync_now=Sync Now&uc=".$uc."&ic=".$ic."&noBrand=".$noBrand."&noManuf=".$noManuf."&blankUpc=".$blankUpc."&discontinued=".$discontinued."&fileDl=false';</script>";
				}
				else{
					$log = "<br/><span class='success'>Frames sync complete [inserted:$ic, updated:$uc]</span>";
					$log .= "<br/><span class='error'>Collections Ignored:<br />Manufacturer not found: $noManuf<br/>Brand not found: $noBrand<br/>Items Ignored:<br/>Blank UPC: $blankUpc<br/>Items: Discontinued: $discontinued<br/></span>";
					importInProgress($log, false);
					imw_query("INSERT INTO `in_options`(`opt_val`, `opt_type`, `module_id`, `entered_date`, `entered_time`, `entered_by`) VALUES('".$obj->escape($log)."', '9', '8', '".date('Y-m-d')."', '".date('H:i:s')."', '".$_SESSION['authId']."')");
				}
			break;
			
			case "5":
				
				importInProgress('<br/>Images Sync in progress', false);
				
				/*List inactive manufacturers*/
				$inactive_manuf = array();
				$manuf_sql = imw_query('SELECT `id` FROM `in_manufacturer_details` WHERE frames_chk = 1 AND `del_status` != 0');
				if( $manuf_sql && imw_num_rows($manuf_sql) > 0 ) {
					while( $row = imw_fetch_assoc($manuf_sql) ) {
						array_push($inactive_manuf, $row['id']);
					}
				}
				imw_free_result($manuf_sql);
				/*End List inactive manufacturers*/
				/*List inactive brands*/
				$inactive_brands = array();
				$brand_sql = imw_query('SELECT `id` FROM `in_frame_sources` WHERE `del_status` != 0');
				if( $brand_sql && imw_num_rows($brand_sql) > 0 ) {
					while( $row = imw_fetch_assoc($brand_sql) ) {
						array_push($inactive_brands, $row['id']);
					}
				}
				imw_free_result($brand_sql);
				/*End List inactive brands*/
					
				/*$sqlImageFPC = "SELECT `i`.`id`, `i`.`upc_code`, `i`.`ConfigurationFPC`, `i`.`manufacturer_id`, `i`.`brand_id`
												FROM `in_item` `i` INNER JOIN `in_manufacturer_details` `m` ON(`i`.`manufacturer_id` = `m`.`id` AND `m`.`del_status` = 0)
												INNER JOIN `in_frame_sources` `b` ON(`i`.`brand_id` = `b`.`id` AND `b`.`del_status` = 0)
												WHERE `i`.`module_type_id` = 1 AND `i`.`del_status` = 0 AND `i`.`ConfigurationFPC` != '' AND `i`.`stock_image` = ''
												ORDER BY `i`.`id` ASC LIMIT ".((!isset($_REQUEST['start']) || $_REQUEST['start']=="")?0:$_REQUEST['start']).", 50";*/
				$sqlImageFPC = "SELECT `i`.`id`, `i`.`upc_code`, `i`.`ConfigurationFPC`, `i`.`manufacturer_id`, `i`.`brand_id`
												FROM `in_item` `i` INNER JOIN `in_manufacturer_details` `m` ON(`i`.`manufacturer_id` = `m`.`id` AND `m`.`del_status` = 0)
												INNER JOIN `in_frame_sources` `b` ON(`i`.`brand_id` = `b`.`id` AND `b`.`del_status` = 0)
												WHERE `i`.`module_type_id` = 1 AND `i`.`del_status` = 0 AND `i`.`ConfigurationFPC` != '' AND `i`.`stock_image` = ''
												ORDER BY `i`.`id` ASC LIMIT 0,50";
				$respImageFPC = imw_query($sqlImageFPC);
				$rowsReturned = imw_num_rows($respImageFPC);
				
				$imageSizes = array('Thumb', 'Small', 'Large', 'XL');	/*Frames Data Image Sizes to be downloaded*/
				$image_base_path = $GLOBALS['DIR_PATH'].'/images/frame_stock';
				
				/*Make Sure directories for every image size exists in frame_stock*/
				if( !isset($_REQUEST['start']) || $_REQUEST['start']=='' || $_REQUEST['start']=='0' ){
					
					foreach($imageSizes as $imageSize){
						$size_path = $image_base_path.'/'.strtolower($imageSize);
						if( !is_dir($size_path) ){
							mkdir( $size_path, 0755, true );
							chown( $size_path, 'apache' );
						}
					}
				}
				/*End Make Sure directories for every image size exists in frame_stock*/
				
				if( $rowsReturned > 0 ){
					
					$loopInsertCounter = 0;
					while( $row = imw_fetch_object($respImageFPC) ){
						
						/*Ignore items with inactive manufacturer or brand*/
						if( in_array($row->manufacturer_id, $inactive_manuf) || in_array($row->brand_id, $inactive_brands) )
							continue;
						
						$imageName	= trim($row->upc_code.'_'.trim($row->id).'.jpg');
						
						foreach($imageSizes as $imageSize){
							
							$other_data_qry = array( 'image_size' => $imageSize );
							$imageData	= $obj->get('imgeData', $row->ConfigurationFPC, $other_data_qry);
							
							$size_path	= $image_base_path.'/'.strtolower($imageSize);
							
							$fh = fopen( $size_path.'/'.$imageName, 'wb' );
							fwrite( $fh, $imageData );
							fclose( $fh );	
						}
						
						$sqlStockImageSave = "UPDATE `in_item` SET `stock_image`='".imw_real_escape_string($imageName)."' WHERE `id`=".$row->id;
						imw_query($sqlStockImageSave);
						
						$loopInsertCounter++;
						
						$msg = 'Please do not hit back button or refresh page untill sync complete message displays.<br/>';
						$msg .= '<br/>Sync begin';
						importInProgress( $msg, true );
						importInProgress('<br/>Images Sync in progress', false);
						
						$log = '<br/><span class=\'success\'>Imges: [Downloaded:'.( (int)$_REQUEST['start'] + $loopInsertCounter ).']</span>';
						importInProgress($log,false);
					}
					
					/*Loop To next batch of 50 Frame Images*/
					$start = ((int)$_REQUEST['start'])+50;
					
					$log = "<br/><span class='success'>Imges: [Downloaded:".( (int)$_REQUEST['start'] + $loopInsertCounter )."]</span>";
					importInProgress( $log, false );
					echo "<script type='text/javascript'>window.location.href='framesImport.php?start=".$start."&option[]=5&sync_now=Sync Now&image_size=".$_REQUEST['image_size']."&downloaded=".( (int)$_REQUEST['start'] + $loopInsertCounter )."'</script>";
				}
				else{
					
					$log = "<br/><span class='success'>Image sync complete [Downloaded:".($_REQUEST['downloaded'])."]</span>";
					importInProgress($log,false);
					imw_query("INSERT INTO `in_options`(`opt_val`, `opt_type`, `module_id`, `entered_date`, `entered_time`, `entered_by`) VALUES('".$obj->escape($log)."', '10', '8', '".date('Y-m-d')."', '".date('H:i:s')."', '".$_SESSION['authId']."')");
				}
				imw_free_result($respImageFPC);
				
			break;
		}
	}
	if(!$noComplete){
		$msg="<br /><br /><span class='successB'>Sync complete</span>";
		importInProgress($msg,false);
	}
}
catch(Exception $e){
	importInProgress("<br /><br /><span class='error'>".$e->getMessage()."</span>", false);
	$msg="<br /><br /><span class='error'>Sync not completed</span>";
	importInProgress($msg,false);
}

function importInProgress($msg, $new=true){
	
	$file	=	$GLOBALS['DIR_PATH'].'/interface/patient_interface/uploaddir/importStatus.txt';
	$msg	.=	" ".date('d M Y h:i:s');
	if(!$new){
		$fh = fopen($file, "a+");
	}
	else{
		$fh = fopen($file, "w");
	}
	fwrite($fh, $msg);
	fclose($fh);
}

function errorLog($msg, $new=false){
	
	$file	=	$GLOBALS['DIR_PATH'].'/interface/patient_interface/uploaddir/importErrors.txt';
	
	$msg	= str_replace(array("\r", "\n", "\t"), array(' ', ' ', ''), $msg);
	$msg	= date('d M Y h:i:s')."\r\n".$msg."\r\n";
	if(!$new){
		$fh = fopen($file, "a+");
	}
	else{
		$fh = fopen($file, "w");
	}
	fwrite($fh, $msg);
	fclose($fh);
}

?>