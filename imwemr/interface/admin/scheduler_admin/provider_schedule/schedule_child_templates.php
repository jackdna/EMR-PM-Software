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


require_once("../../../../config/globals.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');
$sch_tmp_id = $_GET["sch_tmp_id"];
$returnString="";
if($sch_tmp_id != ""){
	$optsQry = "SELECT id,schedule_name FROM schedule_templates WHERE parent_id = '$sch_tmp_id'"; 
	$optsQryObj = imw_query($optsQry);	
	$schedule_option = "<option value=''>".imw_msg('drop_sel')."</option>";
	while($fac_res = imw_fetch_assoc($optsQryObj))
	{
		$id = $fac_res['id'];
		$schedule_name = $fac_res['schedule_name'];						
		$schedule_option .= '<option value="'.$id.'<>'.$schedule_name.'" >'.$schedule_name.'</option>';
	}
	echo $schedule_option;
	

	$show_tmp_timings = $_REQUEST["show_tmp_timings"];
	if($show_tmp_timings == 1)
	{
		$get_tmp_timings_qry = "SELECT date_format(morning_start_time, '%r') as start_time, date_format(morning_end_time, '%r') as end_time FROM schedule_templates WHERE id = '$sch_tmp_id'";
		$get_tmp_timings_obj = imw_query($get_tmp_timings_qry);
		$result_time_data = imw_fetch_assoc($get_tmp_timings_obj);
		echo '~~||~~'.$result_time_data["start_time"].' - '.$result_time_data["end_time"];
	}	
}

?>