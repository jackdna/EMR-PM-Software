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

$error_reported1 = false;
$error_reported2 = false;

$created_file_id1 = 0;
$created_file_id2 = 0;

$arr_invalid1 = array();
$arr_invalid2 = array();

$error_msg1 = array();
$error_msg2 = array();

$separation_qry = "SELECT ic.id, ic.institutional_type, pcl.encounter_id FROM patient_charge_list pcl LEFT JOIN insurance_companies ic ON pcl.$insComp = ic.id LEFT JOIN patient_charge_list_details pcld ON pcld.charge_list_id = pcl.charge_list_id WHERE pcld.del_status='0' and  pcl.submitted = 'true' and pcl.gro_id = '$default_group_id' and pcl.encounter_id in ($encounter_id) and pcld.proc_selfpay != '1' and pcl.totalAmt > '0'";
$sep_res = imw_query($separation_qry);

if(imw_num_rows($sep_res)>0){
	while($sep_rs = imw_fetch_assoc($sep_res)){
		if($sep_rs["institutional_type"] == "INST_PROF"){
			$arr_inst_prof[] = $sep_rs["encounter_id"];
		}else{
			$arr_inst_only[] = $sep_rs["encounter_id"];
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

//removing old file
$del_file = join(",",$filesName).".txt";
$qry = "update batch_file_submitte set delete_status = '1' where Batch_file_submitte_id = '".$fileDetail->Batch_file_submitte_id."'";
imw_query($qry);

//INST PROF
if(count($arr_inst_prof) > 0){
	$encounter_id = implode(",", $arr_inst_prof);
	
	$fileName = join(",",$filesName);
	$arr_file_name = explode("_", $fileName);
	$orig_seq_no = 0;
	if($arr_file_name[2] == "i"){
		$dt_val = $arr_file_name[3];
		$orig_seq_no = (int)$arr_file_name[4];
	}else{
		$dt_val = $arr_file_name[2];
		$orig_seq_no = (int)$arr_file_name[3];
	}

	$new_dt = substr($dt_val, 4)."-".substr($dt_val, 0, 2)."-".substr($dt_val, 2, 2);	
	
	$seq_no = 0;
	$qry = "SELECT file_name FROM batch_file_submitte WHERE create_date = '".$new_dt."' AND group_id = '".$default_group_id."' ORDER BY Batch_file_submitte_id	DESC LIMIT 1";
	$ress = imw_query($qry);
	$res = imw_fetch_assoc($ress);
	//TOHI0002.8375010.clm
	$arr_file = explode("_", $res["file_name"]);
	if($arr_file[2] == "i"){
		$seq_no = (int)$arr_file[4] + 1;
	}else{
		$seq_no = (int)$arr_file[3] + 1;
	}

	if(count($arr_inst_only) > 0){
		$fileName = $arr_file_name[0]."_".$arr_file_name[1]."_".$dt_val."_00".$seq_no.".txt";
	}else{
		$fileName = $arr_file_name[0]."_".$arr_file_name[1]."_".$dt_val."_00".$orig_seq_no.".txt";
	}
	//echo $fileName." p";

	//---- GET UNIQUE HEADER CONTROL IDENTIFIER AND UNIQUE TRANSACTION NUMBER --------
	$batch_unique_headers 			= $this->get_unique_headers();
	$new_interchange_num			= $batch_unique_headers['new_interchange_num'];
	$InterchangeControlNumber 		= $batch_unique_headers['interchange_control_num'];
	$Transaction_set_unique_control = $batch_unique_headers['transaction_set_unique_control'];
	$header_control_identifier 		= $batch_unique_headers['header_control_identifier'];
	

	$submitterSpaceCount = 15 - strlen($submitterId);
	$submitterSpaceStr = NULL;
	for($s=1;$s<=$submitterSpaceCount;$s++){
		$submitterSpaceStr .= ' ';
	}
	$recieveSpaceCount = 15 - strlen($recieverId);
	$recieveSpaceStr = NULL;
	for($s=1;$s<=$recieveSpaceCount;$s++){
		$recieveSpaceStr .= ' ';
	}

	//if($navicureFile == true){
	require_once('reprint_navicure_electronic_file_5010.php');
//	}else{
	//	require_once('reprint_emdeon_electronic_file_5010.php');
//	}
}
//INST ONLY
if(count($arr_inst_only) > 0){
	$encounter_id = implode(",", $arr_inst_only);

	$fileName = join(",",$filesName);
	$orig_seq_no = 0;
	$arr_file_name = explode("_", $fileName);
	if($arr_file_name[2] == "i"){
		$dt_val = $arr_file_name[3];
		$orig_seq_no = (int)$arr_file_name[4];
	}else{
		$dt_val = $arr_file_name[2];
		$orig_seq_no = (int)$arr_file_name[3];
	}

	$new_dt = substr($dt_val, 4)."-".substr($dt_val, 0, 2)."-".substr($dt_val, 2, 2);	
	
	$seq_no = 0;
	$qry	= "SELECT file_name FROM batch_file_submitte WHERE create_date = '".$new_dt."' AND group_id = '".$default_group_id."' ORDER BY Batch_file_submitte_id DESC LIMIT 1";
	$ress	= imw_query($qry);
	$res	= imw_fetch_assoc($ress);
	$arr_file = explode("_", $res["file_name"]);
	if($arr_file[2] == "i"){
		$seq_no = (int)$arr_file[4] + 1;
	}else{
		$seq_no = (int)$arr_file[3] + 1;
	}

	if(count($arr_inst_prof) > 0){
		$fileName = $arr_file_name[0]."_".$arr_file_name[1]."_i_".$dt_val."_00".$seq_no.".txt";
	}else{
		$fileName = $arr_file_name[0]."_".$arr_file_name[1]."_i_".$dt_val."_00".$orig_seq_no.".txt";
	}
	//echo $fileName." i";

	//---- GET UNIQUE HEADER CONTROL IDENTIFIER AND UNIQUE TRANSACTION NUMBER --------
	$batch_unique_headers 			= $this->get_unique_headers();
	$new_interchange_num			= $batch_unique_headers['new_interchange_num'];
	$InterchangeControlNumber 		= $batch_unique_headers['interchange_control_num'];
	$Transaction_set_unique_control = $batch_unique_headers['transaction_set_unique_control'];
	$header_control_identifier 		= $batch_unique_headers['header_control_identifier'];

	$submitterSpaceCount = 15 - strlen($submitterId);
	$submitterSpaceStr = NULL;
	for($s=1;$s<=$submitterSpaceCount;$s++){
		$submitterSpaceStr .= ' ';
	}
	$recieveSpaceCount = 15 - strlen($recieverId);
	$recieveSpaceStr = NULL;
	for($s=1;$s<=$recieveSpaceCount;$s++){
		$recieveSpaceStr .= ' ';
	}
	
	require_once('reprint_ghn_electronic_file_i_5010.php');
}
?>