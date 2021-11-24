<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$fields=array("Doctor error","Optician error","Optician damage","Lab error","Product defect", "Poor performance", "Buyer's remorse", "Warranty");
$sql[]="ALTER TABLE  `in_return_reason` ADD  `prac_code` VARCHAR( 50 ) NOT NULL";
//get all saved records
$q=imw_query("select * from in_return_reason where del_status=0 order by return_reason");
while($d=imw_fetch_object($q))
{
	$arr[$d->return_reason]=$d->return_reason;	
}
$entered_date=date('Y-m-d');
$entered_time=date('H:i:s');
foreach($fields as $rec)
{
	if(!$arr[$rec])
	{
		$sql[]="INSERT INTO in_return_reason SET return_reason = '".imw_real_escape_string($rec)."', entered_date='$entered_date', entered_time='$entered_time', entered_by=1";	
	}
}
$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}
if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 92 run successfully...</div>';	
}

?>