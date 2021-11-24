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
require_once(dirname(__FILE__).'/../../../config/globals.php');
include_once("schedule_functions.php");
	
if($_GET['proc_id'] != ""){
	$str_appt_procedure = default_proc_to_doctor_proc($_GET['proc_id'], $_GET['pro_id']);
	$arr_appt_procedure = explode("~", $str_appt_procedure);
	//print "<pre>";
	//print_r($arr_appt_procedure);

	$tm_hour = $_GET['time_hr'];
	$tm_min = $_GET['time_min'];
	
	if($_GET['time_ampm'] == 'PM'){
		if($tm_hour != 12){
			$time_hr = $tm_hour + 12;				
		}else{
			$time_hr = $tm_hour;
		}
	}else{
		$time_hr = $tm_hour;
	}

	if($arr_appt_procedure[1] != ""){
		$time_to = date("h:i:A", mktime($time_hr, $tm_min + $arr_appt_procedure[1]));
	}else{
		$time_to = date("h:i:A", mktime($time_hr, $tm_min + 15));		
	}
	$procedure_message=$arr_appt_procedure[2];
	$arrProc=$new_arr_proc=$arrProc_prev=array();
	$proc_id=$_GET['proc_id'];
	if($_SESSION["proc_alert_disable"]){
		$arrProc_prev=$_SESSION["proc_alert_disable"];
	}
	if($_SESSION["proc_pat"]==$_SESSION["patient"]){
		if(in_array($proc_id,$_SESSION["proc_alert_disable"])){
			$procedure_message="";
		}
	}else{
		unset($_SESSION["proc_alert_disable"]);	
		$_SESSION["proc_alert_disable"]="";	
		$arrProc=$new_arr_proc=$arrProc_prev=array();
	}
	echo $time_to."~".$procedure_message;
	$arrProc[$proc_id]=$proc_id;
	$new_arr_proc=array_merge($arrProc_prev,$arrProc);
	$_SESSION["proc_alert_disable"]=$new_arr_proc;
	$_SESSION["proc_pat"]=$_SESSION["patient"];
}
?>