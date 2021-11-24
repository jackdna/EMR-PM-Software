<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$counter=0;
$q="Select * from cpt_category_tbl where LOWER(cpt_category)='injectible' ";

$query_rs=imw_query($q);
while ($row = imw_fetch_assoc($query_rs)) {
    $rq = "UPDATE cpt_category_tbl SET cpt_category = 'Injectable' WHERE cpt_cat_id = '".$row['cpt_cat_id']."'";
    $rq_obj = imw_query($rq);
    $counter++;
}

$msg_info[]= "Records Updated $counter";
?>
<html>
<head>
<title>Update 72</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>