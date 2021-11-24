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
$return = "yes";
$sch_overrider_privilege=(core_check_privilege(array("priv_Sch_Override")) == false) ? 0 : 1;

//get variables
$selected_proc = trim($_REQUEST["selected_proc"]);
$landing_proc = trim($_REQUEST["landing_proc"]);
$label_type = trim($_REQUEST["label_type"]);

if($landing_proc != "" && $selected_proc != "" && $label_type == "Procedure"){
	$landing_proc_arr_templ=explode(';',strtolower($landing_proc));
	foreach($landing_proc_arr_templ as $val){
	$landing_proc_arr[trim($val)]=trim($val);}
	
	$acronym=$label=false;
	$qry = "SELECT acronym, labels FROM slot_procedures WHERE proc != '' AND doctor_id = '0' AND active_status = 'yes' and id='$selected_proc'";
	$res = imw_query($qry);
	$total_result=imw_num_rows($res);
	if($total_result > 0){
		$arr = imw_fetch_assoc($res);
		$lbl_str=str_replace('~:~',';',strtolower($arr['labels']));
		$lbl_str=str_replace('; ',';',$lbl_str);
		//match label first
		$lbl_arr=explode(';',$lbl_str);

		foreach($lbl_arr as $lbl_val)
		{
			if($landing_proc_arr[trim($lbl_val)])
			{
				$label=true;
				break;
			}	
		}
		//match acronym here
		if($landing_proc_arr[trim(strtolower($arr['acronym']))])
		{
			$acronym=true;
		}
	}
	
	if($total_result<=0 || ($label!=true && $acronym!=true))
	{
		if($sch_overrider_privilege==1)$return = 'schovrtrue';
		else $return = "no";
	}else{ /*do othing*/}
	
	
}
echo $return;

?>