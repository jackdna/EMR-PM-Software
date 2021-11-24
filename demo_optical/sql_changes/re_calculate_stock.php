<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
require_once($GLOBALS['DIR_PATH']."/library/classes/functions.php"); 

if($_REQUEST['start_val']>0){
	$start_val = $_REQUEST['start_val'];
}else{
	$start_val = 0;
}
$end = 1;
if($start_val==0){
	$loc_id_str="";
	$sql = imw_query("select id from in_location where del_status='0'");
	while($row=imw_fetch_array($sql)){
		$loc_id_arr[]=$row['id'];
	}
	$loc_id_str=implode("','",$loc_id_arr);
	
	imw_query("DELETE FROM in_item_loc_total WHERE item_id=0");
	imw_query("DELETE FROM in_item_lot_total WHERE item_id=0");
	
	imw_query("delete from in_item_loc_total where loc_id not in('$loc_id_str')");
	imw_query("delete from in_item_lot_total where loc_id not in('$loc_id_str')");
}
$item_qry = imw_query("select id from in_item where del_status='0' order by id asc limit $start_val , $end");
$fch_item_qry = imw_fetch_array($item_qry);
$item_id=$fch_item_qry['id'];
recalculate_stock($item_id);
?>
<html>
<head>
<title>Mysql Updates - Stock Correction</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2">
    	<?php 
			echo "Total item Corrected ".$start_val."<br><br>";
		?>
    </font>
	<form action="" method="post" name="submit_frm" id="submit_frm">
		<input type="hidden" name="start_val" value="<?php print $start_val + $end; ?>">
	</form>
	<?php	
	
	if(imw_num_rows($item_qry) > 0){
	?>
	<script type="text/javascript">
		document.getElementById("submit_frm").submit();
	</script>
	<?php
	}
	?>
</body>
</html>