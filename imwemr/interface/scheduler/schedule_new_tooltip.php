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
		
$ap_id = $_REQUEST['tool_sch_id'];
$pt_id = $_REQUEST['pate_id'];
$sel_proc_id = $_REQUEST['sel_proc_idR'];
$sec_sel_proc_id = $_REQUEST['sec_sel_proc_id'];
$ter_sel_proc_id = $_REQUEST['ter_sel_proc_id'];
$render_html = "";

$qry = "select id, fname, mname, lname, phone_home from patient_data where id = '".$pt_id."'";
$res = imw_query($qry);
if(imw_num_rows($res) > 0){
	$arr = imw_fetch_assoc($res);
	$render_html .= "<div style=\"height:35px;text-align:left;padding-left:3px;padding-top:5px;padding-right:3px;\">[{(_PROC_)}]&nbsp;-&nbsp;".$arr["fname"]."&nbsp;".$arr["mname"]."&nbsp;".$arr["lname"]."&nbsp;-&nbsp;".$arr["id"]." ".core_phone_format($arr["phone_home"]);
}

if($ap_id != ""){
 	$qry = "select users.fname, users.lname, users.mname, procedureid, sa_doctor_id, TIME_FORMAT(sa_app_starttime, '%h:%i %p') as sa_app_starttime, TIME_FORMAT(sa_app_endtime, '%h:%i %p') as sa_app_endtime from schedule_appointments left join users on users.id = schedule_appointments.sa_doctor_id where schedule_appointments.id = '".$ap_id."'";
	$res = imw_query($qry);
	if(imw_num_rows($res) > 0){
	$arr = imw_fetch_assoc($res);
		$sel_proc_id = $arr["procedureid"];
		$render_html .= " (".$arr["sa_app_starttime"]."&nbsp;-&nbsp;".$arr["sa_app_endtime"]."&nbsp;".strtoupper(substr($arr["fname"], 0, 1).substr($arr["lname"], 0, 1)).")";
	}
}

$qry = "select acronym, proc, labels from slot_procedures where id IN('".$sel_proc_id."','".$sec_sel_proc_id."','".$ter_sel_proc_id."')";
$res = imw_query($qry);
	if(imw_num_rows($res) > 0){
	while($arr_tmp = imw_fetch_assoc($res))
	{
		$arr[]=	$arr_tmp;
	}
	if($arr[0]["labels"])$labels[]=$arr[0]["labels"];
	if($arr[1]["labels"])$labels[]=$arr[1]["labels"];
	if($arr[2]["labels"])$labels[]=$arr[2]["labels"];
	$labelStr=implode('~:~',$labels);
	$labelStr=str_replace('~:~',',',$labelStr);
	if(strlen($labelStr)>3)$labelStr.="<br/>";
	$proc_name = ($arr[0]["acronym"] != "") ? $arr[0]["acronym"] : $arr[0]["proc"];
	$sec_proc_name = ($arr[1]["acronym"] != "") ? $arr[1]["acronym"] : $arr[1]["proc"];
	$ter_proc_name = ($arr[2]["acronym"] != "") ? $arr[2]["acronym"] : $arr[2]["proc"];
	if($sec_proc_name != ""){$proc_name .= ",".$sec_proc_name;}
	if($ter_proc_name != ""){$proc_name .= ",".$ter_proc_name;}
	$render_html = str_replace("[{(_PROC_)}]", "$labelStr(".$proc_name.")", $render_html);
}
$render_html = str_replace("[{(_PROC_)}]", "", $render_html);

$render_html .= "</div>";
echo $render_html;
?>