<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$qry_str="TRUNCATE TABLE in_optical_order_form;<br>";
$qry_str.="TRUNCATE TABLE in_order;<br>";
$qry_str.="TRUNCATE TABLE in_order_details;<br>";
$qry_str.="TRUNCATE TABLE in_order_detail_status;<br>";
$qry_str.="TRUNCATE TABLE in_order_fac;<br>";
$qry_str.="TRUNCATE TABLE in_order_item_price_details;<br>";
$qry_str.="TRUNCATE TABLE in_order_lens_price_detail;<br>";
$qry_str.="TRUNCATE TABLE in_order_sell;<br>";
$qry_str.="TRUNCATE TABLE in_order_return;<br>";
$qry_str.="TRUNCATE TABLE in_order_history;<br>";
$qry_str.="TRUNCATE TABLE in_patient_pictures;<br>";

echo $qry_str;

?>