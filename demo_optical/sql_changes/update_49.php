<?php 
//update to add expiry date column in medication
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
imw_query("ALTER TABLE  `in_item` ADD  `expiry_date` DATE NOT NULL;");
echo "<b><em>Update complete</em></b>";
?>