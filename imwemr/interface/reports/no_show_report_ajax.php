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
include_once(dirname(__FILE__)."/../../config/globals.php");
require_once('../../library/classes/cls_common_function.php');

if($_POST['action']=='update' && $_POST['appt_id'])
{
	
	//getting operator intials
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	
	if($_POST['cmnts'])
	{	
		$cmnt_arr=explode(':',$_POST['cmnts']);
		$cmnt_arr_tmp=$cmnt_arr;
		for($i=1; $i<= sizeof($cmnt_arr);$i++)
		{
			$compare = trim(str_replace("\n", "", $cmnt_arr[$i]));
			if(empty($compare)){continue;}
			if($cmnt_arr[0]==$compare)unset($cmnt_arr_tmp[$i]);
			else {break;}
		}
		$comments=implode(':',$cmnt_arr_tmp);
	}else $comments='';
	imw_query("update schedule_appointments set sa_comments='".core_refine_user_input(rawurldecode($comments))."', sa_app_time = '".date('Y-m-d H:i:s')."' where id=$_POST[appt_id]");
	echo $comments;
}
?>