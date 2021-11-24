<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

	if($_REQUEST['table'] && $_REQUEST['field'] && $_REQUEST['id_field_name'] && $_REQUEST['id'] && $_REQUEST['newDate'] && $_REQUEST['newTime'])
	{
		extract($_REQUEST);
		list($m,$d,$y)=explode('-',$newDate);
		$newDate="$y-$m-$d";
		//$newTime=date("H:i:s", strtotime($newTime));
		$newTime=$objManageData->setTmFormat($_REQUEST['newTime']);
		echo "update $table set $field='$newDate $newTime' where $id_field_name=$id";
		imw_query("update $table set $field='$newDate $newTime' where $id_field_name=$id")or die(imw_error());
	}
?>            

