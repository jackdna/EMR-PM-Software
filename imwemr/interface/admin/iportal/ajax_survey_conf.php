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
require_once("../../../config/globals.php");

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$table	= "survey_tbl_set";
$pkId	= "id";
$chkFieldAlreadyExist = "name";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." set status='1' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$arr_exam=array();
		$_POST['status'] = isset($_REQUEST["status"]) ? 1 : 0;
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		//$_POST['date_time']=date("Y-m-d H:i:s");
		//$_POST['providerID']="0";
		if(!$_POST["enable_comment"] || $_POST["enable_comment"]==""){
			$_POST["enable_comment"]=0;
		}
		if(!$_POST["problem_list"]){$_POST["problem_list"]="";}
		if(!$_POST["appointment_proc"]){$_POST["appointment_proc"]="";}		
		if($_POST['survey_id_set']){
			list($_POST['survey_id_set'],$_POST['survey_name'])=explode("-||-",$_POST['survey_id_set']);
		}else{
			$_POST['survey_id_set']="";
		}
		if($_POST['survey_active_date']){
			$_POST['survey_active_date']=getDateFormatDB($_POST['survey_active_date']);
		}
		if($_POST['survey_expire_date']){
			$_POST['survey_expire_date']=getDateFormatDB($_POST['survey_expire_date']);
		}
		if($_POST['appt_start_date']){
			$_POST['appt_start_date']=getDateFormatDB($_POST['appt_start_date']);
		}
		if($_POST['appt_end_date']){
			$_POST['appt_end_date']=getDateFormatDB($_POST['appt_end_date']);
		}
		$query_part = "";
		foreach($_POST as $k=>$v){
			if(is_array($_POST[$k])){$v=implode("|~~|",$_POST[$k]);}
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
		if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
			}
			$res = imw_query($q);
			if($res){
				echo 'Record Saved Successfully.';
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		break;
	case 'show_list':
		$q = "SELECT id,concat(survey_id_set,'-||-',survey_name) as survey_id_set,survey_name,concat(date_format(survey_active_date,'".get_sql_date_format()."'),' to ',date_format(survey_expire_date,'".get_sql_date_format()."')) as survey_active_date_from,concat(age_group_start,'-',age_group_end) as age_group,age_group_start,age_group_end,date_format(survey_active_date,'".get_sql_date_format()."') as survey_active_date,date_format(survey_expire_date,'".get_sql_date_format()."') as survey_expire_date,gender,problem_list,appointment_proc,
		concat(date_format(appt_start_date,'".get_sql_date_format()."'),' to ',date_format(appt_end_date,'".get_sql_date_format()."')) as appt_date,date_format(appt_start_date,'".get_sql_date_format()."') as appt_start_date,date_format(appt_end_date,'".get_sql_date_format()."') as appt_end_date,survey_message,survey_thanks_message,enable_comment FROM ".$table." WHERE status!='1' ORDER BY $so $soAD";
		$r = imw_query($q)or die(imw_error().$q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$array_survey=get_all_survey();
		$get_all_proc=get_all_proc();
		$get_problem_list=get_problem_list();
		echo json_encode(array('records'=>$rs_set,'array_survey'=>$array_survey,'get_all_proc'=>$get_all_proc,'get_problem_list'=>$get_problem_list));
		break;
	case 'change_status':
		$id = $_POST['rid'];
		$value = $_POST['value'];
		$q 		= "UPDATE $table set del_status='$value' WHERE $pkId = $id";
		$res 	= imw_query($q);
		if($res){
			echo "true";
		}else{
			echo "false";
		}
		break;
	default: 
}
function get_all_survey(){
	$array_survey=array();
	$qry="select survey_id,survey_title from survey_tbl WHERE status!='1' order by survey_title";
	$res=imw_query($qry);
	while($row=imw_fetch_assoc($res)){
		$survey_id=$row["survey_id"];
		$survey_title=$row["survey_title"];
		$array_survey[$survey_id]=$survey_title;
	}
	return $array_survey;
}
function get_all_proc(){
	$arr_proc=array();
	$qry_proc="SELECT id,acronym FROM slot_procedures WHERE acronym!='' and doctor_id='0' AND active_status='yes' order by acronym asc";
	$res_proc=imw_query($qry_proc);
	while($row_proc=imw_fetch_assoc($res_proc)){
		$id=$row_proc["id"];
		$acronym=$row_proc["acronym"];
		$arr_proc[][$id]=$acronym;
	}	
	return $arr_proc; 
}
function get_problem_list(){
	$arr_prob_list=array();
	$diag_qry = "select dx_code,diagnosis_id,diag_description from diagnosis_code_tbl order by diag_description asc";
	$diag_run = imw_query($diag_qry);
	while($diag_fet=imw_fetch_assoc($diag_run)){
		//Get ICD 10 for the current ICD 9 
		$icd10Desc = '';
		$chkQry = imw_query('SELECT icd10 from icd10_data where icd9 = "'.$diag_fet['dx_code'].'" OR LOWER(icd9_desc) = "'.strtolower($diag_fet['diag_description']).'" limit 0,1');
		if(imw_num_rows($chkQry) > 0){
			$rowIcd10 = imw_fetch_assoc($chkQry);
			$icd10Desc = (isset($rowIcd10['icd10']) && empty($rowIcd10['icd10']) == false ) ? $rowIcd10['icd10'] : '';
		}
		
		//$arr_prob_list[][$diag_fet['diagnosis_id']]=$diag_fet['diag_description'].' - '.$diag_fet['dx_code'];	
		$arr_prob_list[][$diag_fet['diagnosis_id']]=$diag_fet['diag_description'].' - '.$icd10Desc;	
	}	
	return $arr_prob_list;
}
?>