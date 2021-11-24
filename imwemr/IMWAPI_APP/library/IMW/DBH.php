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
 Purpose: MySQLi Extension Functions
 Access Type: Indirect Access.
*/

namespace IMW;

/**
 * DB
 *
 * Main DB connection Class
 */
class DBH
{
	protected $dbh;
	
	function __construct()
	{
		global $sqlconf;
		
		$conn = mysqli_connect( $sqlconf["host"], $sqlconf["login"], $sqlconf["pass"], constant('IMEDIC_IDOC') );
		$this->dbh = $conn;
	}
	
	
	//Performs a query on the database
	function imw_query($q){
		return mysqli_query($this->dbh,$q);
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
		return mysqli_affected_rows($this->dbh);
	}
	
	//Returns a string description of the last error
	function imw_error(){
		return mysqli_error($this->dbh);
	}
	
	//Returns a number of the last error
	function imw_errno(){
		return mysqli_errno($this->dbh);
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
		return mysqli_insert_id($this->dbh);
	}
	
	//Closes a previously opened database connection
	function imw_close($q){
		return mysqli_close($this->dbh);
	}
	
	//Performs debugging operations
	function imw_debug(){
		return mysqli_debug($this->dbh);
	}
	
	//Free memory with associated result-set
	function imw_free_result($q){
		return mysqli_free_result($q);
	}
	
	//Escape special characters in a string
	function imw_escape_string($q){
		return mysqli_real_escape_string($this->dbh,$q);
	}

}