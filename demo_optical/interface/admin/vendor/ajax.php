<?php
/*
File: ajax.php
Coded in PHP7
Purpose: Get Data from Zip Code
Access Type: Include File
*/
require_once("../../../config/sql_conf.php");

if($_REQUEST['action']!="" && $_REQUEST['action']=="getcitystate")
{
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
?>