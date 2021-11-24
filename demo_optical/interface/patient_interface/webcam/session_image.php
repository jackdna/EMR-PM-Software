<?php 
require_once(dirname('__FILE__')."/../../../config/config.php");
echo $_SESSION['img_src']."~~".$_SESSION['img_id'];
unset($_SESSION['img_src']);
unset($_SESSION['img_id']);
?>