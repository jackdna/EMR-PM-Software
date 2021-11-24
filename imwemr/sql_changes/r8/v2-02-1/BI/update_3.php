<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info="";
imw_query("CREATE TABLE ar_worksheet (ar_col_id int(11) NOT NULL,ar_detail_column varchar(500) NOT NULL)") or $msg_info = imw_error();

if(!$msg_info){
imw_query("INSERT INTO ar_worksheet (ar_col_id, ar_detail_column) VALUES
(1, 'a:20:{i:0;s:8:\"Facility\";i:1;s:17:\"Patient Name - ID\";i:2;s:3:\"DOB\";i:3;s:9:\"Ins. Type\";i:4;s:7:\"Ins. ID\";i:5;s:8:\"Provider\";i:6;s:3:\"CFD\";i:7;s:2:\"PD\";i:8;s:3:\"DOS\";i:9;s:3:\"DOC\";i:10;s:3:\"CPT\";i:11;s:5:\"ICD10\";i:12;s:6:\"Charge\";i:13;s:1:\"R\";i:14;s:5:\"Aging\";i:15;s:7:\"Balance\";i:16;s:9:\"1st Claim\";i:17;s:9:\"Prt Pt St\";i:18;s:9:\"Note/Date\";i:19;s:9:\"Case Type\";}')");
}
if($msg_info)
{
    $msg_info .= '<br><br><b>V2.02.1 - Update 3 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info .= "<br><br><b>V2.02.1 - Update 3 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>V2.02.1 - Update 3</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo($msg_info);?></font>
</body>
</html>