<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");
$date="backup";
$qryBackTable="CREATE TABLE IF NOT EXISTS users_".$date." ENGINE = MYISAM   select * from users;";
$resBackTable=imw_query($qryBackTable);
$p=$i=0;
$ptrnString='/^[0-9 ,]+$/';
$qryUpdatePracName="SELECT DISTINCT(practiceName) FROM `users` where practiceName!='' ORDER BY practiceName ";
$resUpdatePracName=imw_query($qryUpdatePracName)or die(imw_error());
if(imw_num_rows($resUpdatePracName)>0){
	while($rowUpdatePracName=imw_fetch_assoc($resUpdatePracName)){
		$practiceName=$rowUpdatePracName['practiceName'];
		if(!preg_match($ptrnString,trim($practiceName))){
			$qryadminPrac="INSERT into practice_name set name='".trim(addslashes($practiceName))."'";
			$resadminPrac=imw_query($qryadminPrac)or $msg_info[] = imw_error().$qryadminPrac;
			$insertID=imw_insert_id();
			if($insertID){
				$p++;	
			}
			$qryUpdPrac="UPDATE users set practiceName='".$insertID."' WHERE practiceName='".trim(addslashes($practiceName))."'";
			$resUpdPrac=imw_query($qryUpdPrac)or $msg_info[] = imw_error().$qryUpdPrac;
			if($resUpdPrac){
				//$i++;	
			}
		}
	}
}
echo "TOTAL ".$p." Record Inserted in practice_name Table";
//echo "<br>TOTAL ".$i." Record Updated in USERS Table";


?>

<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}