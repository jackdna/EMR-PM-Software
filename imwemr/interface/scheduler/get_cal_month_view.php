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
File: get_cal_month_view.php
Purpose: For Calendar in scheduler 
Access Type: Direct
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
//scheduler object
$obj_scheduler = new appt_scheduler();
$_SESSION['last_sch_date']=$_REQUEST['sel_dat'];
if(!empty($_REQUEST["int_appt"])){
	//Schedular slot time setting
	$qry = "insert into current_time_locator set sch_id = '".$_REQUEST["int_appt"]."', uid = '".$_SESSION["authId"]."', `dated`='".date('Y-m-d')."'";
	imw_query($qry);
}
//get previous date
$dated=date('Y-m-d',mktime(0,0,0, date('m'), date('d')-1, date('Y')));
//check data to clear previous one 
$chk=imw_query("select current_time_id from current_time_locator where dated<'$dated' LIMIT 0,1");
if(imw_num_rows($chk)>0)
{
	imw_query("delete from current_time_locator where dated<'$dated'");
}

list($sel_year, $sel_month, $sel_dt) = explode("-", $_REQUEST['sel_dat']);

$curr_month = (isset($_REQUEST["curr_month"]) && !empty($_REQUEST["curr_month"])) ? $_REQUEST["curr_month"] : 0;
$curr_year = (isset($_REQUEST["curr_year"]) && !empty($_REQUEST["curr_year"])) ? $_REQUEST["curr_year"] : 0;

//getting next 2 months from the current month
if($curr_month == 11){
	$next_curr_month1 = date("m", mktime(0, 0, 0, $curr_month + 1, 1, $curr_year));
	$next_curr_month2 = date("m", mktime(0, 0, 0, $curr_month + 2, 1, $curr_year + 1));
	$next_curr_year1 = $curr_year;
	$next_curr_year2 = $curr_year + 1;
}else if($curr_month == 12){
	$next_curr_month1 = date("m", mktime(0, 0, 0, $curr_month + 1, 1, $curr_year + 1));
	$next_curr_month2 = date("m", mktime(0, 0, 0, $curr_month + 2, 1, $curr_year + 1));
	$next_curr_year1 = $curr_year + 1;
	$next_curr_year2 = $curr_year + 1;
}else{
	$next_curr_month1 = date("m", mktime(0, 0, 0, $curr_month + 1, 1, $curr_year));
	$next_curr_month2 = date("m", mktime(0, 0, 0, $curr_month + 2, 1, $curr_year));
	$next_curr_year1 = $curr_year;
	$next_curr_year2 = $curr_year;
}

//echo "$curr_month > 0 && $curr_year > 0 && ( ($curr_month == $sel_month && $curr_year == $sel_year) || ($sel_month == $next_curr_month1 && $sel_year == $next_curr_year1) || ($sel_month == $next_curr_month2 && $sel_year == $next_curr_year2) )";
if($curr_month > 0 && $curr_year > 0 && ( ((int)$curr_month == (int)$sel_month && (int)$curr_year == (int)$sel_year) || ((int)$sel_month == (int)$next_curr_month1 && (int)$sel_year == (int)$next_curr_year1) || ((int)$sel_month == (int)$next_curr_month2 && (int)$sel_year == (int)$next_curr_year2) )){
	die("nonono");
}

//getting next 2 months
if($sel_month == 11){
	$next_month1 = date("m", mktime(0, 0, 0, $sel_month + 1, 1, $sel_year));
	$next_month2 = date("m", mktime(0, 0, 0, $sel_month + 2, 1, $sel_year + 1));
	$next_year1 = $sel_year;
	$next_year2 = $sel_year + 1;
}else if($sel_month == 12){
	$next_month1 = date("m", mktime(0, 0, 0, $sel_month + 1, 1, $sel_year + 1));
	$next_month2 = date("m", mktime(0, 0, 0, $sel_month + 2, 1, $sel_year + 1));
	$next_year1 = $sel_year + 1;
	$next_year2 = $sel_year + 1;
}else{
	$next_month1 = date("m", mktime(0, 0, 0, $sel_month + 1, 1, $sel_year));
	$next_month2 = date("m", mktime(0, 0, 0, $sel_month + 2, 1, $sel_year));
	$next_year1 = $sel_year;
	$next_year2 = $sel_year;
}

list($thisc, $nextc, $next2c) = $obj_scheduler->load_calendar($sel_year."-".$sel_month."-".intval($sel_dt));
?>
<div class="fl cl_otln">
	<?php echo $thisc; ?>
</div>
<div class="fl cl_otln">
	<?php echo $nextc; ?>
</div>
<div class="fl cl_otln">
	<?php echo $next2c; ?>
</div>~~~~~<?php echo $sel_year."-".$sel_month."-01"; ?>