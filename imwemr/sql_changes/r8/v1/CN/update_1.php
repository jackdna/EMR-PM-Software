<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="ALTER TABLE `tests_name` ADD `test_table_pk_id` VARCHAR( 25 ) NOT NULL DEFAULT 'test_other_id' COMMENT 'PKID column of test table' AFTER `test_table`";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2="ALTER TABLE `tests_name` ADD `performed_key` VARCHAR( 25 ) NOT NULL DEFAULT 'performedBy' COMMENT 'performed_by key of test table' AFTER `exam_date_key`";
imw_query($sql2) or $msg_info[] = imw_error();

$sql3="ALTER TABLE `tests_name` ADD `script_file` VARCHAR(45) NOT NULL DEFAULT 'test_template.php' COMMENT 'php script file' AFTER `performed_key`";
imw_query($sql3) or $msg_info[] = imw_error();

/*********UPDATING DEFAULT VALUES****/
	$q1 = array();
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='surgical_id' WHERE test_table='surgical_tbl'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='test_bscan_id' WHERE test_table='test_bscan'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='test_labs_id' WHERE test_table='test_labs'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='test_cellcnt_id' WHERE test_table='test_cellcnt'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='icg_id' WHERE test_table='icg'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='nfa_id' WHERE test_table='nfa'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='vf_id' WHERE test_table='vf'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='vf_gl_id' WHERE test_table='vf_gl'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='oct_id' WHERE test_table='oct'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='oct_rnfl_id' WHERE test_table='oct_rnfl'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='gdx_id' WHERE test_table='test_gdx'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='pachy_id' WHERE test_table='pachy'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='vf_id' WHERE test_table='ivfa'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='disc_id' WHERE test_table='disc'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='disc_id' WHERE test_table='disc_external'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='topo_id' WHERE test_table='topography'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='iol_master_id' WHERE test_table='iol_master_tbl'";
	$q1[]	= "UPDATE tests_name SET test_table_pk_id='test_other_id' WHERE test_table='test_other'";
	foreach($q1 as $q){imw_query($q);}
	
	
	$q2 = array();
	$q2[]	= "UPDATE tests_name SET performed_key='performedByOD' WHERE test_table='surgical_tbl'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='test_bscan'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='test_labs'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='test_cellcnt'";
	$q2[]	= "UPDATE tests_name SET performed_key='performed_by' WHERE test_table='icg'";
	$q2[]	= "UPDATE tests_name SET performed_key='performBy' WHERE test_table='nfa'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='vf'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='vf_gl'";
	$q2[]	= "UPDATE tests_name SET performed_key='performBy' WHERE test_table='oct'";
	$q2[]	= "UPDATE tests_name SET performed_key='performBy' WHERE test_table='oct_rnfl'";
	$q2[]	= "UPDATE tests_name SET performed_key='performBy' WHERE test_table='test_gdx'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='pachy'";
	$q2[]	= "UPDATE tests_name SET performed_key='performed_by' WHERE test_table='ivfa'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='disc'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='disc_external'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='topography'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedByOD' WHERE test_table='iol_master_tbl'";
	$q2[]	= "UPDATE tests_name SET performed_key='performedBy' WHERE test_table='test_other'";
	foreach($q2 as $q){imw_query($q);}
	

	$q3 = array();
	$q3[]	= "UPDATE tests_name SET script_file='ascan.php' WHERE test_table='surgical_tbl'";
	$q3[]	= "UPDATE tests_name SET script_file='test_bscan.php' WHERE test_table='test_bscan'";
	$q3[]	= "UPDATE tests_name SET script_file='test_labs.php' WHERE test_table='test_labs'";
	$q3[]	= "UPDATE tests_name SET script_file='test_cellcount.php' WHERE test_table='test_cellcnt'";
	$q3[]	= "UPDATE tests_name SET script_file='test_icg.php' WHERE test_table='icg'";
	$q3[]	= "UPDATE tests_name SET script_file='test_nfa.php' WHERE test_table='nfa'";
	$q3[]	= "UPDATE tests_name SET script_file='test_vf.php' WHERE test_table='vf'";
	$q3[]	= "UPDATE tests_name SET script_file='test_vf_gl.php' WHERE test_table='vf_gl'";
	$q3[]	= "UPDATE tests_name SET script_file='test_oct.php' WHERE test_table='oct'";
	$q3[]	= "UPDATE tests_name SET script_file='test_oct_rnfl.php' WHERE test_table='oct_rnfl'";
	$q3[]	= "UPDATE tests_name SET script_file='test_gdx.php' WHERE test_table='test_gdx'";
	$q3[]	= "UPDATE tests_name SET script_file='test_pacchy.php' WHERE test_table='pachy'";
	$q3[]	= "UPDATE tests_name SET script_file='test_ivfa.php' WHERE test_table='ivfa'";
	$q3[]	= "UPDATE tests_name SET script_file='test_disc.php' WHERE test_table='disc'";
	$q3[]	= "UPDATE tests_name SET script_file='test_external.php' WHERE test_table='disc_external'";
	$q3[]	= "UPDATE tests_name SET script_file='test_topography.php' WHERE test_table='topography'";
	$q3[]	= "UPDATE tests_name SET script_file='iol_master.php' WHERE test_table='iol_master_tbl'";
	$q3[]	= "UPDATE tests_name SET script_file='test_other.php' WHERE test_table='test_other' AND test_type='0'";
	$q3[]	= "UPDATE tests_name SET script_file='test_template.php' WHERE test_table='test_other' AND test_type='1'";
	foreach($q3 as $q){imw_query($q);}
	

/*************************************/

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 1 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 1 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 1 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>