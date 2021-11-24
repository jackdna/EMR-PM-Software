<?php
/*
File: ajax.php
Coded in PHP7
Purpose: Get Zip Code
Access Type: Include File
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
if($_REQUEST['action']==""){
	exit;
}

if($_REQUEST['action']=="getcitystate"){
	
 $code_qry="Select * from zip_codes where zip_code='".stripslashes($_REQUEST['zipcode'])."'";

		$code_res=imw_query($code_qry) or die(imw_error());
		if(imw_num_rows($code_res)>0)	{
			$row=imw_fetch_array($code_res);
			$city_state=$row['city']."-".$row['state_abb'];
			echo $city_state;
		}	
		else
		{
			echo "false";	
		}
}
elseif($_REQUEST['action']=="copyManufToVendors" && $_POST['manufId']!=''){
	$manufId = (int)$_POST['manufId'];
	if($manufId<=0 || !$manufId){
		echo 'Wrong data provided.';
	}
	
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s"); 	
	
	$sqlManufData = 'SELECT `manufacturer_name`, `manufacturer_address`, `tel_num`, `mobile`, `fax`, `email`, `city`, `zip`, `zip_ext`, `state` FROM `in_manufacturer_details` WHERE `id`='.$manufId;
	$manufResp = imw_query($sqlManufData);
	if($manufResp && imw_num_rows($manufResp)>0){
		$manufData = imw_fetch_object($manufResp);
		
		$vendorInsert = 'INSERT INTO `in_vendor_details`
						SET
							`vendor_name`="'.$manufData->manufacturer_name.'",
							`vendor_address`="'.$manufData->manufacturer_address.'",
							`tel_num`="'.$manufData->tel_num.'",
							`mobile`="'.$manufData->mobile.'",
							`fax`="'.$manufData->fax.'",
							`email`="'.$manufData->email.'",
							`city`="'.$manufData->city.'",
							`zip`="'.$manufData->zip.'",
							`zip_ext`="'.$manufData->zip_ext.'",
							`state`="'.$manufData->state.'",
							`entered_date`="'.$date.'",
							`entered_time`="'.$time.'",
							`entered_by`="'.$opr_id.'",
							`contact_name`="'.$manufData->contact_name.'"';
		if(imw_query($vendorInsert)){
			/*Associate the New vendor with tne Manufacturer*/
			$vendorId =  imw_insert_id();
			
			/*Check if entry in relations table already exists*/
			$sqlRel = 'SELECT `id` FROM `in_vendor_manufacture` WHERE `vendor_id`='.$vendorId.' AND `manufacture_id`='.$manufId;
			$respRel = imw_query($sqlRel);
			if($respRel && imw_num_rows($respRel)==0){
				$sqlVendorManuf = 'INSERT INTO `in_vendor_manufacture` SET `vendor_id`='.$vendorId.', `manufacture_id`='.$manufId;
				imw_query($sqlVendorManuf);
			}
			echo 'success';
		}
	}
	else{
		echo 'Wrong data provided.';
	}
}
?>