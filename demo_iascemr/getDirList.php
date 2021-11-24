<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData	=	new manageData;
$return =	array();
extract($_POST);
if($cid)
{
	$return 	=	$objManageData->getDirContentStatus($cid,3,3,urldecode($dirName));
}
echo json_encode($return);

?>
