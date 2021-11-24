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

list($server_name, $port, $uname, $pass, $dbname) = $gbl_sc_connect;
if($scemr_fac_id && count($scemr_facid_db_arr)>0) {
	$dbname = $scemr_facid_db_arr[$scemr_fac_id];	
}
imw_select_db($dbname,$GLOBALS['dbh']) or die('Could not select DB: ' . imw_error());
?>