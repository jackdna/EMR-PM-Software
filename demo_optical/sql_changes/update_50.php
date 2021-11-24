<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$rs=imw_query("Update in_item SET gender='men' WHERE LOWER(gender)='male'");
echo 'Total male values changed: '.imw_affected_rows();

$rs=imw_query("Update in_item SET gender='women' WHERE LOWER(gender)='female'");
echo '<br><br>Total female values changed: '.imw_affected_rows();
?>