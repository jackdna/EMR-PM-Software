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

$table	= "cl_charges";
$pkId	= "cl_charge_id";
$chkFieldAlreadyExist = "name";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "delete from ".$table." WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$arr_exam=array();
		
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		unset($_POST['cpt_practice_code']);
		
		$defaultFeeId=get_default_field();
		$cpt_fee_id = $_POST['cpt_fee_id'];		
		if($_POST['price']>0 && $cpt_fee_id>0){
			$cptQry = "Update cpt_fee_table SET cpt_fee='".$_POST['price']."' WHERE fee_table_column_id='".$defaultFeeId."' AND cpt_fee_id='".$cpt_fee_id."'";
			$rs=imw_query($cptQry);
		}else {
			$_POST['price']='0';	
		}
		

		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		
		
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
		}else {
			echo "enter_unique";	
		}
		break;
	case 'show_list':
		$q = "SELECT cc.cl_charge_id,cc.name AS name,
				IF(cft.cpt_prac_code IS NULL,'',cft.cpt_prac_code) AS cpt_practice_code,
				IF(dct.icd10 IS NULL,'',dct.icd10) AS icd10,
				IF(cftb.cpt_fee IS NULL,'',cftb.cpt_fee) AS price,
				IF(cc.del_status!=0,'In-Active','Active') AS charge_list_status,del_status,
				cc.cpt_fee_id,cc.dx_code_id
				FROM cl_charges cc 
				LEFT JOIN cpt_fee_tbl cft ON (cft.cpt_fee_id=cc.cpt_fee_id)
				LEFT JOIN icd10_data dct ON (dct.id=cc.dx_code_id)
				LEFT JOIN cpt_fee_table cftb ON (cftb.cpt_fee_id=cft.cpt_fee_id AND cftb.fee_table_column_id=(SELECT fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)= 'default'))
				ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		
		$dx_code_list=diagnosis_code_fun();
		$cpt_prac_code_list = cpt_prac_code();
		echo json_encode(array('records'=>$rs_set,'dx_code_list'=>$dx_code_list,'cpt_prac_code_list'=>$cpt_prac_code_list));
		break;
	default: 
}


function diagnosis_code_fun(){
	$qry="Select id, icd10 from icd10_data WHERE deleted=0 AND icd10!='' order by icd10 asc";	
	$res=imw_query($qry);
	$rs_cat=array();
	if($res && imw_num_rows($res)>0){
		while($rset=imw_fetch_assoc($res)){
			$rs_cat[]=$rset;
		}	
	}
	return $rs_cat;
}

function get_default_field(){
	$defaultFeeId='';
	$res=imw_query("Select fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)='default'");
	if($res && imw_num_rows($res)>0){
		$row=imw_fetch_array($res);
		$defaultFeeId = $row['fee_table_column_id'];
	}
	return $defaultFeeId;
}

function removeLineBreaks($str)
{
	return preg_replace("(\r\n|\n|\r)", " ", $str);
}
function cpt_prac_code() {
	/* $rs=imw_query("Select cpt_cat_id FROM cpt_category_tbl");
	while($res=imw_fetch_array($rs)){
		$arrCLCats[$res['cpt_cat_id']]=$res['cpt_cat_id'];
	} */

	$retCPTPracStrArr = array();
	$retCPTPracStr='';
	//$strCLCats=implode(',',$arrCLCats);
	$qryCPT = "Select cpt_fee_id, cpt4_code, cpt_prac_code from cpt_fee_tbl WHERE delete_status=0 ORDER BY cpt_prac_code";
	$qryRes=imw_query($qryCPT);
	while($qryRow=imw_fetch_array($qryRes)) {
		$retCPTPracStrArr[]=$qryRow;
	}
	return $retCPTPracStrArr;
}

?>