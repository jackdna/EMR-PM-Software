<?php
require_once("../../../../config/globals.php");
$zipcode = $_GET['zipcode'];
	 $code_qry="Select * from zip_codes where zip_code='".imw_real_escape_string($zipcode)."'";
		$code_res=imw_query($code_qry) or die(imw_error());
		if(imw_num_rows($code_res)>0)	{
			$row=imw_fetch_array($code_res);
			
			$state = (isset($GLOBALS['state_val']) && $GLOBALS['state_val'] == 'full') ? $row['state'] : 
					 ((isset($GLOBALS['state_val']) && $GLOBALS['state_val'] == 'abb') ? $row['state_abb'] : $row['state_abb']);
			
			$city_state=$row['city']."-".$state;
			if($_REQUEST['zipext']=="y"){
				$city_state=$city_state."-".$row['zip_ext'];	
			}else{$city_state=$city_state."-";	}
			$city_state=$city_state."-".$row['country']."-".$row['county'];	
			echo $city_state;
		}	
?>
