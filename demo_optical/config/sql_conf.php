<?php
// Open a connection to the MySQL server
if (isset($GLOBALS['dbh'])==false){
	$conLink = mysqli_connect($sqlconf["host"],$sqlconf["login"],$sqlconf["pass"],$GLOBALS['IMW_DB_NAME']);
	$GLOBALS['dbh'] = $conLink;
}

//Returns a string description of the last connect error
if (!$GLOBALS['dbh']) {
  echo "Could not connect to DB! ".mysqli_connect_errno();
  exit;
}
function imw_select_db($dbname,$dbLink){
	return mysqli_select_db($dbLink,$dbname);
}

//Performs a query on the database
function imw_query($q){
	return mysqli_query($GLOBALS['dbh'],$q);
}

//Fetch a result row as an associative, a numeric array, or both
function imw_fetch_array($q){
	return mysqli_fetch_array($q);
}

//Fetch a result row as an associative array
function imw_fetch_assoc($q){
	return mysqli_fetch_assoc($q);
}

//Get a result row as an enumerated array
function imw_fetch_row($q){
	return mysqli_fetch_row($q);
}

//Returns the current row of a result set as an object
function imw_fetch_object($q){
	return mysqli_fetch_object($q);
}

//Gets the number of rows in a result
function imw_num_rows($q){
	return mysqli_num_rows($q);
}

//Gets the number of affected rows in a previous MySQL operation
function imw_affected_rows(){
	return mysqli_affected_rows($GLOBALS['dbh']);
}

//Returns a string description of the last error
function imw_error(){
	return mysqli_error($GLOBALS['dbh']);
}

//Returns a number of the last error
function imw_errno(){
	return mysqli_errno($GLOBALS['dbh']);
}

//Returns a number of fields in a result
function imw_num_fields($q)
{
	return mysqli_num_fields($q);
}

//Returns definition information of Field at requested index -$i
function imw_fetch_fields($q)
{
	return mysqli_fetch_fields($q);
}

//Returns definition information of Field at requested index -$i
function imw_fetch_field_direct($q,$i)
{
	return mysqli_fetch_field_direct($q,$i);
}

//Returns the auto generated id used in the last query
function imw_insert_id(){
	return mysqli_insert_id($GLOBALS['dbh']);
}

//Closes a previously opened database connection
function imw_close($q){
	if($q) {
		return mysqli_close($q);
	}else {
		return mysqli_close($GLOBALS['dbh']);	
	}
}

//Performs debugging operations
function imw_debug(){
	return mysqli_debug($GLOBALS['dbh']);
}

//Execute mysql sql qry and return result
function imw_exec($q){
	$res = imw_query($q);
	return imw_fetch_assoc($res);
}

//Free memory with associated result-set
function imw_free_result($q){
	return mysqli_free_result($q);
}

//Escape special characters in a string
function imw_real_escape_string($q){
	return mysqli_real_escape_string($GLOBALS['dbh'],$q);
}
//Escape special characters in a string
function imw_escape_string($q){
	return imw_real_escape_string($q);
}

//Execute mysql sql qry and return result or false on failure; used in wv
function sqlQuery ($statement){
  $query = imw_query($statement) or 
  HelpfulDie("query failed: $statement (" . imw_error() . ")");
  if(is_bool($query)===false){	
	$rez = imw_fetch_assoc($query);
  }
  elseif( is_bool($query) === TRUE )
  {
  	$rez = $query;
  }
  if ($rez == FALSE)
  return FALSE;
  return $rez;
}
//-- mysql function : created temporarily for previous code --
//Return resultset used in wv
function sqlStatement($statement){
  //----------run a mysql query, return the handle
  $query = imw_query($statement) or 
    HelpfulDie("query failed: $statement (" . imw_error() . ")");
  return $query;
}

//Escape string used in wv
function sqlEscStr($s){
	return mysqli_real_escape_string($GLOBALS['dbh'], $s);
}
function sqlFetchArray($resource){
  if ($resource == FALSE)
    return false;
  return imw_fetch_assoc($resource);
}

function sqlInsert($statement){
  //----------run a mysql insert, return the last id generated
  imw_query($statement) or 
    HelpfulDie("insert failed: $statement (" . imw_error() . ")");
  return imw_insert_id();
}

//fmg: Much more helpful that way...
function HelpfulDie ($statement, $sqlerr=''){
  echo "<p><p><font color='red'>ERROR:</font> $statement<p>";
  if ($sqlerr) {
    echo "Error: <font color='red'>$sqlerr</font><p>";
  }//if error
  exit;
}

//Returns type of Field at requested index -$i
function imw_field_type($q,$i)
{
	return mysql_field_type($q,$i);
}

//Returns name of Field at requested index -$i
function imw_field_name($q,$i)
{
	return mysql_field_name($q,$i);
}
//--

// Adjusts the result pointer to an arbitrary row in the result
function imw_data_seek($q,$i){
	return mysqli_data_seek($q,$i);
}
//--

//Messages Management
function imw_msg($key=""){
	$msg_arr['no_rec']="No record found.";
	$msg_arr['drop_sel']="Please Select";
	$msg_arr['sel_rec']="Please select a record.";
	$msg_arr['del_rec']="Are you sure to delete the selected record(s).";
	$msg_arr['del_succ']="Record(s) deleted successfully.";
	if($key!=""){
		return $msg_arr[$key];
	}else{
		return $msg_arr;
	}
}
require_once(dirname(__DIR__)."/library/vendor/autoload.php");
?>