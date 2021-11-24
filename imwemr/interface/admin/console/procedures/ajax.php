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

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'procedure_name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$table	= "operative_procedures";
$pkId	= "procedure_id";
$chkFieldAlreadyExist="procedure_name";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." set del_status='1' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		if($_POST['consent_form_id']){$_POST['consent_form_id']=implode($_POST['consent_form_id'],",");}else if(!$_POST['consent_form_id']){$_POST['consent_form_id']="";}
		if($_POST['op_report_id']){$_POST['op_report_id']=implode($_POST['op_report_id'],",");}else if(!$_POST['op_report_id']){$_POST['op_report_id']="";}
		$_POST['operator_id']=$_SESSION['authId'];
		if($_POST['pre_op_meds']){$_POST['pre_op_meds']=str_ireplace("\n","|",$_POST['pre_op_meds']);}
		if($_POST['intraviteral_meds']){$_POST['intraviteral_meds']=str_ireplace("\n","|",$_POST['intraviteral_meds']);}
		if($_POST['post_op_meds']){$_POST['post_op_meds']=str_ireplace("\n","|",$_POST['post_op_meds']);}
		$query_part = "";
		$arr_cpt=$arr_mod=array();
		if($_POST['last_cnt']){
			$cnt_end=$_POST['last_cnt'];
			for($i=1;$i<=($cnt_end);$i++){
				if(trim($_POST['cpt_code1_'.$i])){
					$arr_cpt['cpt_code1_'.$i]=$_POST['cpt_code1_'.$i];
				}
				if(trim($_POST['cpt_code2_'.$i])){
					$arr_cpt['cpt_code2_'.$i]=$_POST['cpt_code2_'.$i];
				}
				if(trim($_POST['cpt_code3_'.$i])){
					$arr_cpt['cpt_code3_'.$i]=$_POST['cpt_code3_'.$i];
				}
				if(trim($_POST['mod_code1_'.$i])){
					$arr_mod['mod_code1_'.$i]=$_POST['mod_code1_'.$i];
				}
				if($_POST['mod_code2_'.$i]){
					$arr_mod['mod_code2_'.$i]=$_POST['mod_code2_'.$i];
				}
				if($_POST['mod_code3_'.$i]){
					$arr_mod['mod_code3_'.$i]=$_POST['mod_code3_'.$i];
				}
				unset($_POST['cpt_code1_'.$i]);
				unset($_POST['cpt_code2_'.$i]);
				unset($_POST['cpt_code3_'.$i]);
				unset($_POST['mod_code1_'.$i]);
				unset($_POST['mod_code2_'.$i]);
				unset($_POST['mod_code3_'.$i]);
			}
			$arr_cpt_mod=array_merge($arr_cpt,$arr_mod);
			$_POST['cpt_code']=htmlentities(serialize($arr_cpt_mod));
			
		}
		unset($_POST['last_cnt']);
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."' ";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE del_status!='1' AND ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."' ".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		
		
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE del_status!='1' AND ".$pkId." = '".$id."'";
			}
			$res = imw_query($q);
			if($res){
				echo 'Record Saved Successfully.';
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q."<b>-".$_POST['last_cnt']."-</b>";
			}
		}else {
			echo "enter_unique";	
		}
		break;
	case 'show_list':
		$q = "SELECT procedure_id,procedure_name,ret_gl ,cpt_code,dx_code,dx_code_id,time_out_request,pre_op_meds,intraviteral_meds,post_op_meds,consent_form_id,op_report_id,consent_form_id_sel,op_report_id_sel,laser_procedure_note,spot_duration,spot_size,power,shots,total_energy,degree_of_opening,exposure,count FROM ".$table." WHERE del_status !='1' ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = $arr_cpt_mod=array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$cpr_code_str=substr(trim($rs['cpt_code']),-1);
				$cpr_code_str_filter=$rs['cpt_code'];
				$arr_cpt_mod=array();$rs['cpt_code_str']="";
				$rs["dx_code"] = $rs["dx_code"]; 
				$rs["dx_code"]=str_replace(";","; ",$rs["dx_code"]);
				if($rs['cpt_code']!="" && !is_numeric($rs['cpt_code']) && $cpr_code_str!=";" && substr($rs['cpt_code'],0,2)=="a:"){
					$arr_cpt_mod=unserialize(html_entity_decode(trim($cpr_code_str_filter)));					
				}else{
					//$rs['cpt_code1_1']=$rs['cpt_code'];$rs['cpt_code_str']=$rs['cpt_code'];
					$arr__cpt_val=explode(",",$rs['cpt_code']);
					$c=0;
					foreach($arr__cpt_val as  $cpt_val_set){
						$c++;
						$arr_cpt_mod['cpt_code1_'.$c] =trim($cpt_val_set);	
					}
				}
				unset($rs['cpt_code']);
				$cnt=1;
				if(count($arr_cpt_mod)>0){
					
					$tmp_cpt_code="";
					foreach($arr_cpt_mod as $tkey =>$tval){
						if(!empty($tval) && strpos($tkey,"cpt_code")!==false){ if(!empty($tmp_cpt_code)){  $tmp_cpt_code.=", ";}	$tmp_cpt_code.=$tval;	}
					}
					if($tmp_cpt_code!=""){ $rs['cpt_code_str']=$tmp_cpt_code; }
				
					$rs=array_merge($rs,$arr_cpt_mod);
					$cnt=ceil(count($arr_cpt_mod)/6);
					$cnt=$cnt+1;
				}			
				
				if(count($rs)>0){
					$rs['last_cnt']=$cnt;				
					$rs_set[] = $rs;
				}
			}
		}
		$consent_forms=get_consent_form();
		$operative_forms=get_op_reports();
		$cpt_code_arr=cpt_code_arr();
		$dx_code_arr=dx_code_arr();
		$get_med_name=get_med_name();
		$get_mod_code=mod_code();
		echo json_encode(array('records'=>$rs_set,'consent_form'=>$consent_forms,'op_report'=>$operative_forms,'cpt_code_arr'=>$cpt_code_arr,'dx_code_arr'=>$dx_code_arr,'get_med_name'=>$get_med_name,'get_mod_code'=>$get_mod_code));
		break;
		case 'checkValidDxCode':
		$str = isset($_REQUEST['p_dx']) ? urldecode($_REQUEST['p_dx']) : "";		
		$ar = dx_code_check($str);
		echo json_encode($ar);
	break;	
	default: 
}
function get_consent_form(){
	$qryConsentForm="SELECT consent_form_id,consent_form_name from consent_form order by consent_form_name";
	$resConsentForm=imw_query($qryConsentForm);
	$rows=array();
	if(imw_num_rows($resConsentForm)>0){
		while($rs=imw_fetch_assoc($resConsentForm)){
			$rows[]=$rs;
		}	
	}
	return $rows;	
}
function get_op_reports(){
	$qryOpReport="SELECT temp_id,temp_name from pn_template order by temp_name";
	$resOpReport=imw_query($qryOpReport);
	$rows_op=array();
	if(imw_num_rows($resOpReport)>0){
		while($rs_op=imw_fetch_assoc($resOpReport)){
			$rows_op[]=$rs_op;
		}	
	}
	return $rows_op;		
}
function cpt_code_arr(){
	$prac_code_qry = "select cpt_prac_code from cpt_fee_tbl where cpt_prac_code != '' and delete_status = '0' group by cpt_prac_code order by cpt_prac_code";
	$prac_code_qry_obj = imw_query($prac_code_qry);
	$rows_cpt=array();
	if(imw_num_rows($prac_code_qry_obj)>0){
		while($rs_cpt=imw_fetch_assoc($prac_code_qry_obj)){
			$rows_cpt[]=$rs_cpt;
		}	
	}
	return $rows_cpt;		
}
function dx_code_check($str){
	$arr=array();
	$arr["flg"] = "";
	$arr["icd10"] = "";
	$str = trim($str);
	if(!empty($str)){
		$str=imw_real_escape_string($str);
		//added ICD 10 codes
		$qry_dx="SELECT id as diagnosis_id, icd10 as d_prac_code, icd10_desc, icd9 FROM `icd10_data` 
					WHERE (icd10='".$str."' || icd9='".$str."' || icd10_desc='".$str."')
					AND deleted!='1' 
					order by icd10";	
		$row=sqlQuery($qry_dx);
		if($row!=false){
			$arr["flg"] = "OK";
			$arr["icd10"] = $row["d_prac_code"];
		}
	}			
	return $arr;
}
function dx_code_arr(){
	//$dx_code_qry="Select concat(dx_code,'; ') as dx_code  from diagnosis_code_tbl Where delete_status=0 order by dx_code";
	$dx_code_qry="SELECT concat(icd10,'; ') as dx_code, icd10_desc, icd9, id FROM `icd10_data` WHERE icd10!='' AND deleted!='1' order by icd10";
	$dx_code_res=imw_query($dx_code_qry);
	$dx_code_arr=array();
	if(imw_num_rows($dx_code_res)>0){
		while($rs_cpt=imw_fetch_assoc($dx_code_res)){
			$dx_code_arr[]=$rs_cpt;
		}	
	}
	return $dx_code_arr;		
}
function get_med_name(){
	$qry_med="SELECT medicine_name from medicine_data WHERE del_status = '0' order by medicine_name";
	$res_med=imw_query($qry_med);
	$med_arr=array();
	if(imw_num_rows($res_med)>0){
		while($rs_med=imw_fetch_assoc($res_med)){
			$med_arr[]=$rs_med;
		}	
	}
	return $med_arr;		
}
function mod_code(){
	$q_mod='SELECT concat(mod_prac_code,"; ") as mod_prac_code FROM modifiers_tbl WHERE delete_status=0';
	$r_mod=imw_query($q_mod);
	$arr_mod=array();
	while($row_mod=imw_fetch_assoc($r_mod)){
		$arr_mod[]=$row_mod;
	}
	return $arr_mod;
}
function unserialize_array($arr_seri_val){
	$return_arr=array();
	if($arr_seri_val){
		$return_arr=unserialize(html_entity_decode($arr_seri_val));		
	}
	return $return_arr;
}
?>