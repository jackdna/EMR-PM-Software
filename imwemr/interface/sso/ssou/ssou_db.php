<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
global $sqlconf;
// Open a connection to the MySQL server
if (isset($GLOBALS['dbhs'])==false){
	$conLink = mysqli_connect($sqlconf["host"],$sqlconf["login"],$sqlconf["pass"],constant('IMW_SSO'), $sqlconf["port"]);
	$GLOBALS['dbhs'] = $conLink;
}

//Returns a string description of the last connect error
if (!$GLOBALS['dbhs']) {
  echo "Could not connect to DB! ".mysqli_connect_errno();
  exit;
}

//Performs a query on the database
function sso_query($q){
	return mysqli_query($GLOBALS['dbhs'],$q);
}

//Fetch a result row as an associative array
function sso_fetch_assoc($q){
	return mysqli_fetch_assoc($q);
}

//Returns a number of the last error
function sso_error(){
	return mysqli_errno($GLOBALS['dbhs']);
}

//Closes a previously opened database connection
function sso_close(){	
	return mysqli_close($GLOBALS['dbhs']);
}
//Execute mysql sql qry and return result or false on failure; used in wv
function sso_sqlQuery ($statement){
  $query = sso_query($statement) or 
  sso_HelpfulDie("query failed: $statement (" . sso_error() . ")");
  if(is_bool($query)===false){	
	$rez = sso_fetch_assoc($query);
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
function sso_sqlStatement($statement){
  //----------run a mysql query, return the handle
  $query = sso_query($statement) or 
    sso_HelpfulDie("query failed: $statement (" . sso_error() . ")");
  return $query;
}

//Escape string used in wv
function sso_sqlEscStr($s){
	return mysqli_real_escape_string($GLOBALS['dbhs'], $s);
}
function sso_sqlFetchArray($resource){
  if ($resource == FALSE)
    return false;
  return sso_fetch_assoc($resource);
}

//fmg: Much more helpful that way...
function sso_HelpfulDie ($statement, $sqlerr=''){
  echo "<p><p><font color='red'>ERROR:</font> $statement<p>";
  if ($sqlerr) {
    echo "Error: <font color='red'>$sqlerr</font><p>";
  }//if error
  exit;
}

?>