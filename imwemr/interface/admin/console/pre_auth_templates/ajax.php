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
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'title';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$table	= "pre_auth_templates";
$pkId	= "id";
$chkFieldAlreadyExist="template_name";
$arr_save_field=array("template_name","medical_type","dx_codes");
$del_field="del_status";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." set ".$del_field."='1' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.mysql_error()."\n".$q;
		}
		break;
	case 'delete_sub':
		$id = $_POST['pkId'];
		$q 		= "UPDATE pre_auth_templates_details set ".$del_field."='1' WHERE ".$pkId."=".$id;
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.mysql_error()."\n".$q;
		}
		break;	
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		$cnt=($_POST['last_cnt']);
		unset($_POST['last_cnt']);
		$arr_dx=array();
		for($i=1;$i<=12;$i++){
			$arr_dx['dx_'.$i]=$_POST['dx_'.$i];
			unset($_POST['dx_'.$i]);
		}
		
		if(count($arr_dx)>0){
			$dx_serialize_array=htmlentities(serialize($arr_dx));
			$_POST['dx_codes']=$dx_serialize_array;
		}
		
		$query_part = "";
		foreach($_POST as $k=>$v){
			if(in_array($k,$arr_save_field)){
				$query_part .= $k."='".addslashes($v)."', ";
			}
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."' AND  ".$del_field."!='1'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
		$r_c=imw_query($q_c)or die(imw_error().$q_c);
		$q_coc='';$hr='';
		if(imw_num_rows($r_c)==0){		
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
				$q_coc=" INSERT INTO ";
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
			}
			$res = imw_query($q)or die(imw_error().$q);
			if($res){
				if($id==''){$insert_id=imw_insert_id();}else{$insert_id=$id;}
				if($insert_id){
					for($j=1;$j<=$cnt;$j++){
						$detail_id=addslashes($_POST['detail_id_'.$j]);
						$proc_name=addslashes($_POST['procedureText_'.$j]);
						$rev_name=addslashes($_POST['revcode_'.$j]);
						$dx_code=implode(",",str_ireplace("_",".",$_POST['diagText_all_'.$j]));
						$mod1_code=addslashes($_POST['mod1Text_'.$j]);
						$mod2_code=addslashes($_POST['mod2Text_'.$j]);
						$mod3_code=addslashes($_POST['mod3Text_'.$j]);
						$units_code=addslashes($_POST['units_'.$j]);
						$charges=addslashes($_POST['charges_'.$j]);
						$comments=addslashes($_POST['comments_'.$j]);
						
						if($proc_name){
						if($detail_id==''){$qry_con=" INSERT INTO ";$whr="";}else{$qry_con=" UPDATE ";$whr=" WHERE id='".$detail_id."'";}
						$q_u=$qry_con." pre_auth_templates_details set 
							  procedure_name='".$proc_name."',
							  proc_code='".$rev_name."',
							  diagnosis='".$dx_code."',
							  mod1='".$mod1_code."',
							  mod2='".$mod2_code."',
							  mod3='".$mod3_code."',
							  unit='".$units_code."',
							  charges='".$charges."',
							  comments='".$comments."',
							  pre_auth_id='".$insert_id."'
							  ".$whr;
						$r_c=imw_query($q_u)or die(imw_error());
					 	}
					}
				}
						 
				echo 'Record Saved Successfully.';
			}else{
				echo 'Record Saving failed.'.mysql_error()."\n".$q;
			}
		}else {
			echo "enter_unique";	
		}
		break;
	case 'show_list':

		$q = "SELECT id,template_name,medical_type,dx_codes,del_status FROM ".$table." WHERE ".$del_field."='0' ORDER BY $so $soAD";
		$r = imw_query($q)or die(imw_error().$q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$arr_sub_row_ret=array();
				$arr_sub_row_ret=get_sub_details($rs['id']);
				$dxcode_arr=array();
				$dxcode_arr=unserialize(html_entity_decode($rs['dx_codes']));
				unset($rs['dx_codes']);
				if(count($dxcode_arr)>0){
					$rs=array_merge($rs,$dxcode_arr);
				}
				if(count($arr_sub_row_ret)>0){$rs=array_merge($rs,$arr_sub_row_ret);}
				$rs_set[] = ($rs);
				
			}
		}
		//pre($rs_set);die();
		$dx_code_arr=dx_code_arr();
		$cpt_code_arr=cpt_code_arr();
		$cpt_fee_column=cpt_fee_column();
		$revenue_code_arr=revenue_code();
		$mod_code_arr=mod_code();
		//pre($cpt_fee_column);die();
		echo json_encode(array('records'=>$rs_set,'dx_code_arr'=>$dx_code_arr,'cpt_code_arr'=>$cpt_code_arr,'cpt_fee_column'=>$cpt_fee_column,'revenue_code_arr'=>$revenue_code_arr,'mod_code_arr'=>$mod_code_arr));
		break;
	default: 
}
function dx_code_arr(){
	$dx_code_qry="Select dx_code from diagnosis_code_tbl Where delete_status=0 order by dx_code";
	$dx_code_res=imw_query($dx_code_qry);
	$dx_code_arr=array();
	if(imw_num_rows($dx_code_res)>0){
		while($rs_cpt=imw_fetch_assoc($dx_code_res)){
			$dx_code_arr[]=$rs_cpt;
		}	
	}
	return $dx_code_arr;		
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
function get_sub_details($id){
	$arr_sub_row=array();
	$q_s="SELECT id,procedure_name,proc_code,diagnosis,mod1,mod2,mod3,if(unit='','1',unit)as unit,charges,comments from pre_auth_templates_details where pre_auth_id='".$id."' and del_status=0 order by id";
	$r_s=imw_query($q_s)or die(imw_error());
	$num_rows=imw_num_rows($r_s);
	if($num_rows>0){
		$j=1;
		$arr_sub_row['last_cnt']=$num_rows;
		while($row_sub=imw_fetch_assoc($r_s)){
			$arr_sub_row['detail_id_'.$j]=$row_sub['id'];
			$arr_sub_row['procedureText_'.$j]=$row_sub['procedure_name'];
			$arr_sub_row['revcode_'.$j]=$row_sub['proc_code'];
			$arr_sub_row['diagText_all_'.$j]=str_ireplace(".","_",$row_sub['diagnosis']);
			$arr_sub_row['mod1Text_'.$j]=$row_sub['mod1'];
			$arr_sub_row['mod2Text_'.$j]=$row_sub['mod2'];
			$arr_sub_row['mod3Text_'.$j]=$row_sub['mod3'];
			$arr_sub_row['units_'.$j]=$row_sub['unit'];
			$arr_sub_row['charges_'.$j]=$row_sub['charges'];
			$arr_sub_row['comments_'.$j]=$row_sub['comments'];
			$j++;
		}
	}	
	return $arr_sub_row;
}
function cpt_fee_column(){
	$q_fee='SELECT cft.cpt_prac_code,ft.cpt_fee FROM cpt_fee_tbl as cft inner join cpt_fee_table as ft on cft.cpt_fee_id=ft.cpt_fee_id where fee_table_column_id in(Select fee_table_column_id from fee_table_column WHERE column_name="Default")';
	$r_fee=imw_query($q_fee);
	$arr_fee=array();
	while($row_f=imw_fetch_assoc($r_fee)){
		$arr_fee[]=$row_f;
	}
	return $arr_fee;
}
function revenue_code(){
	$q_rev='SELECT r_code FROM revenue_code';
	$r_rev=imw_query($q_rev);
	$arr_rev=array();
	while($row_rev=imw_fetch_assoc($r_rev)){
		$arr_rev[]=$row_rev;
	}
	return $arr_rev;
}
function mod_code(){
	$q_mod='SELECT mod_prac_code FROM modifiers_tbl WHERE delete_status=0';
	$r_mod=imw_query($q_mod);
	$arr_mod=array();
	while($row_mod=imw_fetch_assoc($r_mod)){
		$arr_mod[]=$row_mod;
	}
	return $arr_mod;
}
?>