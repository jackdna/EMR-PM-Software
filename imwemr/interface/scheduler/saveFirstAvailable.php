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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

//scheduler object
$obj_scheduler = new appt_scheduler();

$keep_orignal=($_REQUEST['keep_orignal'])?$_REQUEST['keep_orignal']:'';

$sel_month = (isset($_REQUEST['sel_month']) && !empty($_REQUEST['sel_month'])) ? $_REQUEST['sel_month'] : "";
$sel_week = (isset($_REQUEST['sel_week']) && !empty($_REQUEST['sel_week'])) ? $_REQUEST['sel_week'] : "";
$sel_time = (isset($_REQUEST['sel_time']) && !empty($_REQUEST['sel_time'])) ? $_REQUEST['sel_time'] : "";

$sch_id = (isset($_REQUEST['sch_id']) && !empty($_REQUEST['sch_id'])) ? $_REQUEST['sch_id'] : "";
$sch_id_arr=explode(',',$sch_id);

$reason = (isset($_REQUEST['reason']) && !empty($_REQUEST['reason'])) ? $_REQUEST['reason'] : "";


foreach($sch_id_arr as $sch_id_val)
{
    $sch_id=(int)$sch_id_val;
	
	//check it there any previous entry regarding this sch appt if yes then delete it
	if($sch_id)imw_query("delete from schedule_first_avail where sch_id=$sch_id");


	list($y,$m)=explode('-', $sel_month);
	//get schedule appointment detail 
	$qry="insert into schedule_first_avail set keep_orignal='$keep_orignal',
			sch_id='$sch_id',
			sel_year='$y',
			sel_month='$m',
			sel_week='$sel_week',
			sel_time='$sel_time',
			sel_reason='". addslashes($reason) ."',
			date_time_of_act='".date('Y-m-d H:i:s')."',
			date_of_act='".date('Y-m-d')."',
			operator_id='$_SESSION[authUserID]'";
			
	imw_query($qry); 
	 
	
	//update scheduler table to set optional paramenter show=0 to show appoint ment in real time schedule in case of user is in todo list/first available
	$qry="update schedule_appointments set sa_patient_app_show=$keep_orignal where id=$sch_id";
	imw_query($qry);
	 
}

?>