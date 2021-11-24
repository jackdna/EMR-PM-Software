<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
## Surgery Center
function getSurgeryCenters()
{
	$sql="SELECT * FROM surgerycenter ORDER BY name, address";
	return sqlQuery($sql);
}
## Users
function getUsers()
{
	$sql = "SELECT * FROM users ORDER BY lname, address";
	return sqlQuery($sql);
}
function getUserInfo($id)
{
	$sql = "SELECT * FROM users 
		  WHERE usersId='".$id."' ";
	return sqlStatement($sql);  
}
function getSergeonProfile()
{
	$andProfileDelCond = "  AND del_status ='' ";
	$sql = "SELECT * FROM surgeonprofile".$andProfileDelCond;	
	return sqlFetchArray(sqlQuery($sql));
}
?>