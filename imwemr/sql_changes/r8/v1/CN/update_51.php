<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = array();
$msg_info = array();

$ar = array(
	'test_bscan' => array('test_bscan_id', 'patientId'),
	'test_cellcnt' => array('test_cellcnt_id', 'patientId'),
	'icg' => array('icg_id', 'patient_id'),
	'vf_gl' => array('vf_gl_id', 'patientId'),
	'oct_rnfl' => array('oct_rnfl_id', 'patient_id'),	
	'test_gdx' => array('gdx_id', 'patient_id'),
	'iol_master_tbl' => array('iol_master_id', 'patient_id'),
	'test_custom_patient' => array('test_id', 'patientId'),	
);

foreach($ar as $k => $v){
	$sql = "SELECT * FROM merge_patient_tables WHERE table_name = '".$k."' ";
	$row = sqlQuery($sql);	
	if($row==false){
		$sql = "INSERT INTO merge_patient_tables(table_name, pk_id, pt_id, status) VALUES( '".$k."', '".$v[0]."', '".$v[1]."', 1 );";
		$row = sqlQuery($sql);
	}
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 51  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 51  run successfully!</b>";
    $color = "green";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 51 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>