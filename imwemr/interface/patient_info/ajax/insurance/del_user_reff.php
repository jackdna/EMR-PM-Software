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
?>
<?php
/*	
File: del_user_reff.php
Purpose: Delete insurance referral
Access Type: Direct 
*/
	require_once '../../../../config/globals.php';
	$reff_id = (int)$_REQUEST['del_id'];
	$op_id = (int) $_SESSION['authId'];
	$cur_date = date('Y-m-d H:i:s');
	$sql_req = 'UPDATE patient_reff SET del_status = 1, del_operator = '.$op_id.', del_datetime = "'.$cur_date.'"  WHERE reff_id ='.$reff_id;
	imw_query($sql_req);
?>