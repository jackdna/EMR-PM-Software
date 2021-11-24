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
$separation_done = true;
$arr_inst_only = array();
$arr_inst_prof = array();
$arr_valid = array();
$arr_invalid = array();
$arr_res = array();
$separation_qry = "SELECT ic.id, ic.institutional_type, pcl.encounter_id, pcl.charge_list_id   FROM patient_charge_list pcl LEFT JOIN insurance_companies ic ON pcl.$InsComp = ic.id LEFT JOIN patient_charge_list_details pcld ON pcld.charge_list_id = pcl.charge_list_id WHERE pcld.del_status='0' and pcl.submitted = 'true' and pcl.gro_id = '$gro_id' and pcl.totalAmt > '0' and pcl.charge_list_id in ($main_charge_list_id) and pcl.$setField = '0'  and pcld.differ_insurance_bill != 'true' and pcld.proc_selfpay != '1'";

$sep_res = imw_query($separation_qry);

if(imw_num_rows($sep_res)>0){
	while($sep_rs = imw_fetch_assoc($sep_res)){
		if($sep_rs["institutional_type"] == "INST_PROF"){
			$arr_inst_prof[] = $sep_rs["charge_list_id"];
		}else{
			$arr_inst_only[] = $sep_rs["charge_list_id"];
		}
	}	
	$arr_inst_prof = array_unique($arr_inst_prof);	
	$arr_inst_only = array_unique($arr_inst_only);
	$arr_inst_prof = array_values($arr_inst_prof);
	$arr_inst_only = array_values($arr_inst_only);
}
//print "<pre>";
//print_r($arr_inst_prof);
//print_r($arr_inst_only);
//die("debugging end");
if(count($arr_inst_prof) > 0){
	$main_charge_list_id = implode(",", $arr_inst_prof);
	require_once('navicure_electronic_file_5010.php');
}

if(count($arr_inst_only) > 0){
	$main_charge_list_id = implode(",", $arr_inst_only);
	
	//require_once('emdeon_electronic_file_i.php');
	require_once('ghn_electronic_file_i_5010.php');
}
?>