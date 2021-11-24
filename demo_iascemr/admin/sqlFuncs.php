<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../common/conDb.php");
include_once("logout.php");
function sqlInsert($sql)
{
	$inserId = imw_query($sql) or die("<br>Err: <b>".imw_error()."</b><br>ErrNo: ".imw_errno());
	$inserId = imw_insert_id();
	return $inserId;
}

function sqlQuery($sql)
{
	$row = imw_query($sql);
	return $row;
}

function sqlStatement($sql)
{
	$res = imw_query($sql);
	$row = imw_fetch_array($res);
	return (imw_num_rows($res)>0) ? $row : false;
}

function sqlFetchArray($res)
{
	return imw_fetch_array($res);
}
?>