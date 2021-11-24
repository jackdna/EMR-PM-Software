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

?><?php
/*
File: schedule_template_time.php
Purpose: show timing for scheduler template
Access Type: Direct
*/
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');
$DEFAULT_TIME_SLOT=constant("DEFAULT_TIME_SLOT");
$sch_tmp_id = $_GET["sch_tmp_id"];
$returnString=$select_options="";
if($sch_tmp_id<>""){
	$qry = "select * from schedule_templates where id = '$sch_tmp_id'";
	$qryRes = imw_fetch_array(imw_query($qry));
	$mor_start_time = $qryRes['morning_start_time'];
	$mor_end_time = $qryRes['morning_end_time'];	
	$schedule_name1 = $qryRes['schedule_name'];
	$date_status = $qryRes['date_status'];
	$returnString=$mor_start_time."---".$mor_end_time."---".$date_status;
}	
print $returnString;
?>