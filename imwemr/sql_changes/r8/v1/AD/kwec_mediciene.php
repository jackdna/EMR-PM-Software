<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

if(imw_num_rows(imw_query("SHOW TABLES LIKE 'medicine_data_kwec' "))!=1)  {
    echo 'medicine_data_kwec table does not exists.';
    return;
}
    
$qry = "Select * from medicine_data WHERE del_status = 0";
$query = imw_query($qry);
while($item = imw_fetch_assoc($query)) {
    $medicine_name[] = strtolower(trim($item['medicine_name']));
}

$counter=0;
$qry = "Select * from medicine_data_kwec WHERE del_status = 0";
$query = imw_query($qry);
while($row = imw_fetch_assoc($query)) {
    if(in_array(strtolower(trim($row['medicine_name'])), $medicine_name)) {
        $skipped[$row['id']] = $row['medicine_name'];
        $counter++;
        continue;
    } else {
        //echo $row['id']." => ". $row['medicine_name']. "<br />";
        $sql = "INSERT INTO `medicine_data` (`id`, `medicine_name`, `alias`, `recall_code`, `med_procedure`, `description`, `provider_id`, `ocular`, `alert`, `alertmsg`, `glucoma`, `prescription`, `med_class`, `ret_injection`, `fdb_id`, `ccda_code`, `ccda_code_system`, ` 	ccda_code_system_name`, `del_status`, `tracked_inventory`, `opt_med_name`, `opt_med_id`, `opt_med_upc`) VALUES (NULL, '".$row['medicine_name']."', '".$row['alias']."', '".$row['recall_code']."', '".$row['med_procedure']."', '".$row['description']."', '".$row['provider_id']."', '".$row['ocular']."', '".$row['alert']."', '".$row['alertmsg']."', '".$row['glucoma']."', '".$row['prescription']."', '".$row['med_class']."', '".$row['ret_injection']."', '".$row['fdb_id']."', '".$row['ccda_code']."', '".$row['ccda_code_system']."', '".$row['ccda_code_system_name']."', '".$row['del_status']."', '".$row['tracked_inventory']."', '".$row['opt_med_name']."', '".$row['opt_med_id']."', '".$row['opt_med_upc']."') ";
        
        imw_query($sql) or $msg_info[] = imw_error();
        
    }
}
if(count($msg_info)>0){
    $msg_info[] = "<br><br><b>Update Failed!</b>";
    $color = "red";
}else{
    $msg_info[] = "<br><br><b>Update completed successfully.</b>";
    $color = "green";
}

//die;
?>
<html>
<head>
<title>Update KWEC Mediciene</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
    
    <?php /*
        echo $counter. "<br />";
        echo "Skipped : ". count($skipped). "<br />";
        foreach($skipped as $key => $val) {
                echo $key .' => '. $val . "<br />";
        } */
    ?>
</body>
</html>