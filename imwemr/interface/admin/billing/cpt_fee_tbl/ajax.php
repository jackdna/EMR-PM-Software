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
set_time_limit(600);
require_once("../../../../config/globals.php");

/**
 * Security Fixes
 */
$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters */
$cpt_status = in_array($_REQUEST['status'], array('Active', 'Inactive')) ? $_REQUEST['status'] : '';

/* End Security Fixes Block */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'discount_code';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix  */
$search	= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
$cpt_cat= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';

$srchQry="";

$table	= "cpt_fee_tbl";
$pkId	= "cpt_fee_id";

//$chkFieldAlreadyExist = "d_code";
$cpt_cat_qry="";
$cpt_status_qry="";
if($cpt_status){
	$cpt_status_qry=" AND cpt_fee_tbl.status = '".$cpt_status."'";
}
if($cpt_cat){
	$cpt_cat_qry=" AND cpt_category_tbl.cpt_cat_id IN (".$cpt_cat.")";
}else{
	$qry="SELECT cpt_cat_id from cpt_category_tbl ORDER BY cpt_category ASC LIMIT 0,1";	
	$res=imw_query($qry);
	$row=imw_fetch_assoc($res);
	$cat_id=$row['cpt_cat_id'];
	$cpt_cat_qry=" AND cpt_category_tbl.cpt_cat_id =".$cat_id;
}
if($search){
	$arrReplace=array("'",'"',"%");
	$search=str_replace($arrReplace,"",$search);
	$srchQry=" AND (cpt_fee_tbl.cpt4_code LIKE '%".$search."%' OR cpt_fee_tbl.cpt_prac_code LIKE '%".$search."%' OR cpt_fee_tbl.cpt_desc LIKE '%".$search."%')";
	$cpt_cat_qry="";
}

$del_date=date("Y-m-d");
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= " UPDATE ".$table." set cpt_fee_tbl.delete_status = '1',deleted_date='".$del_date."',delete_operator='".$_SESSION['authId']."' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		$last_cnt = $_POST['last_cnt'];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		unset($_POST['rev_code_v']);
		unset($_POST['tos_id_v']);
		unset($_POST['departmentId_v']);
		unset($_POST['elem_poe_v']);	
		unset($_POST['last_cnt']);	
		$dx_code_arr=array();	
		for($i=1;$i<=$last_cnt;$i++){
			if($_POST['dx_code_'.$i]!=""){
				$dx_code_arr[]=$_POST['dx_code_'.$i];
			}
			unset($_POST['dx_code_'.$i]);	
		}
		$dx_code_imp=implode(',',$dx_code_arr);
		$_POST['dx_codes'] = $dx_code_imp;
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		/*if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		*/
		
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
			}
			$res = imw_query($q);
			if($id=='' && trim(strtolower($table))=="cpt_fee_tbl"){
				$ins_id=imw_insert_id();
				$qry_fee_tbl_col=imw_query("select * from fee_table_column");
				while($row_fee_tbl_col=imw_fetch_array($qry_fee_tbl_col)){
					$fee_table_column_id=$row_fee_tbl_col['fee_table_column_id'];
					imw_query("insert into cpt_fee_table set cpt_fee_id='$ins_id',fee_table_column_id='$fee_table_column_id'");
				}
			}
			if($res){
				echo 'Record Saved Successfully.';
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
/*		}else {
			echo "enter_unique";	
		}*/
		break;
	case 'show_list':
		$q = "select cpt_fee_tbl.cpt_fee_id,cpt_category_tbl.cpt_category,cpt_fee_tbl.cpt_category2,cpt_fee_tbl.cpt4_code,cpt_fee_tbl.not_covered,cpt_fee_tbl.cpt_prac_code,cpt_fee_tbl.cpt_desc,cpt_fee_tbl.cpt_comments,cpt_fee_tbl.cvx_code,cpt_fee_tbl.rev_code,cpt_fee_tbl.tos_id,cpt_fee_tbl.units,cpt_fee_tbl.mod1,cpt_fee_tbl.mod2,cpt_fee_tbl.mod3,cpt_fee_tbl.mod4,cpt_fee_tbl.departmentId,cpt_fee_tbl.elem_poe,cpt_fee_tbl.status,cpt_fee_tbl.cpt_cat_id,cpt_fee_tbl.dx_codes,cpt_fee_tbl.valueSet,cpt_fee_tbl.cpt_tax,cpt_fee_tbl.unit_of_measure,cpt_fee_tbl.measurement from cpt_fee_tbl left join cpt_category_tbl on cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id where cpt_fee_tbl.delete_status = '0' ".$srchQry.$cpt_cat_qry.$cpt_status_qry." ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$cpt_cat=cpt_category();
		$dept_code=dept_code();
		$rev_code=rev_code();
		$tos_code=tos_code();
		$poe_code=poe_code();
		$dx_code=dx_code();
		$mod_code=mod_code();
		echo json_encode(array('records'=>$rs_set,'cpt_cat'=>$cpt_cat,'dept_code'=>$dept_code,'rev_code'=>$rev_code,'tos_code'=>$tos_code,'poe_code'=>$poe_code,'dx_code'=>$dx_code,'mod_code'=>$mod_code));
		break;
	case 'checkValidDxCode':
		$str = isset($_REQUEST['p_dx']) ? urldecode($_REQUEST['p_dx']) : "";		
		$ar = dx_code_check($str);
		echo json_encode($ar);
	break;	
	default: 
}
function cpt_category(){
	$qry="SELECT cpt_cat_id,cpt_category from cpt_category_tbl ORDER BY cpt_category ASC";	
	$res=imw_query($qry);
	$rs_cat=array();
	if($res && imw_num_rows($res)>0){
		while($rset=imw_fetch_assoc($res)){
			$rs_cat[]=$rset;
		}	
	}
	return $rs_cat;
}

function dx_code_check($str){
	$arr=array();
	$arr["flg"] = "";
	$arr["icd10"] = "";
	$str = trim($str);
	if(!empty($str)){
		$str=imw_real_escape_string($str);
		//added ICD 10 codes
		$qry_dx="SELECT id as diagnosis_id, icd10 as d_prac_code, icd10_desc, icd9 FROM `icd10_data` WHERE (icd10='".$str."' || icd9='".$str."' || icd10_desc='".$str."') AND status!=1  AND deleted!='1' order by icd10";	
		$row=sqlQuery($qry_dx);
		if($row!=false){
			$arr["flg"] = "OK";
			$arr["icd10"] = $row["d_prac_code"];
		}
	}			
	return $arr;
}
function dx_code(){
	//added ICD 10 codes
	$qry_dx="SELECT id as diagnosis_id, icd10 as d_prac_code, icd10_desc, icd9 FROM `icd10_data` WHERE icd10!='' AND status!=1 AND deleted!='1' order by icd10";	
	$res_dx=imw_query($qry_dx);
	$rs_dx=array();
	if(imw_num_rows($res_dx)>0){
		while($rset_dx=imw_fetch_assoc($res_dx)){
			$rs_dx[]=$rset_dx;
		}	
	}
	
	return $rs_dx;
}
function mod_code(){
	//added Modofier codes
	$qry_dx="SELECT modifiers_id,mod_prac_code,mod_description FROM modifiers_tbl WHERE mod_prac_code!='' AND status!='Inactive' AND delete_status!='1' order by mod_prac_code";	
	$res_dx=imw_query($qry_dx);
	$rs_dx=array();
	if(imw_num_rows($res_dx)>0){
		while($rset_dx=imw_fetch_assoc($res_dx)){
			$rs_dx[]=$rset_dx;
		}	
	}
	
	return $rs_dx;
}
function dept_code(){
	$qry="select DepartmentId,DepartmentCode from department_tbl ";	
	$res=imw_query($qry);
	$rs_cat=array();
	if($res && imw_num_rows($res)>0){
		while($rset=imw_fetch_assoc($res)){
			$rs_cat[]=$rset;
		}	
	}
	return $rs_cat;
}
function rev_code(){
	$qry="Select r_id,r_code from revenue_code";	
	$res=imw_query($qry);
	$rs_cat=array();
	if($res && imw_num_rows($res)>0){
		while($rset=imw_fetch_assoc($res)){
			$rs_cat[]=$rset;
		}	
	}
	return $rs_cat;
}
function tos_code(){
	$qry="select tos_id,tos_prac_cod from tos_tbl";	
	$res=imw_query($qry);
	$rs_cat=array();
	if($res && imw_num_rows($res)>0){
		while($rset=imw_fetch_assoc($res)){
			$rs_cat[]=$rset;
		}	
	}
	return $rs_cat;
}
function poe_code(){
	$qry="select poe_messages_id,poe_name from poe_messages";	
	$res=imw_query($qry);
	$rs_cat=array();
	if($res && imw_num_rows($res)>0){
		while($rset=imw_fetch_assoc($res)){
			$rs_cat[]=$rset;
		}	
	}
	return $rs_cat;
}
?>