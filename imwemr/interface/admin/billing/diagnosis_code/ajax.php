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

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'category';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix  */
$search	= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';

$cat_id	= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';
$cat_id = xss_rem($cat_id);	/* Reject arbitrary code - Security Fix */

$table	= "diagnosis_code_tbl";
$pkId	= "diagnosis_id";
$chkFieldAlreadyExist = "pos_id";
$srchQry=$srchQryCat="";

if($cat_id){
	$srchQryCat=" AND diagnosis_category.diag_cat_id IN (".$cat_id.")";
}else{
	$q="select diag_cat_id,category from diagnosis_category order by category ASC LIMIT 0,1";
	$res=imw_query($q);
	if(imw_num_rows($res)>0){
		$row=imw_fetch_assoc($res);	
		$cat_d_id=$row['diag_cat_id'];
		$srchQryCat=" AND diagnosis_category.diag_cat_id = ".$cat_d_id."";
	}
}
if($search){
	$arrReplace=array("'",'"',"%");
	$search=str_replace($arrReplace,"",$search);
	$srchQry=" AND (diagnosis_code_tbl.dx_code LIKE '%".$search."%' OR diagnosis_code_tbl.d_prac_code LIKE '%".$search."%' OR diagnosis_code_tbl.diag_description LIKE '%".$search."%')";
	$srchQryCat="";
}
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "delete from ".$table." WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
			$qrDel="delete from diagnosis_category where diag_cat_id NOT IN(select diag_cat_id from diagnosis_code_tbl)";
			$reDel=imw_query($qrDel);
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		if((!$_POST['diag_cat_id']||$_POST['diag_cat_id']=="") && trim($_POST['category'])){
			$q_cat="Select diag_cat_id from diagnosis_category Where category='".trim($_POST['category'])."'";
			$r_cat=imw_query($q_cat);
			if(imw_num_rows($r_cat)>0){
				$row_cat=imw_fetch_array($r_cat);
				$_POST['diag_cat_id']=$row_cat['diag_cat_id'];
			}else{
				$q_cat_ins="INSERT into diagnosis_category set category='".trim($_POST['category'])."'";
				$r_cat_ins=imw_query($q_cat_ins);		
				$_POST['diag_cat_id']=imw_insert_id();
			}
		}
		unset($_POST['category']);
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id==''){
			$q = "INSERT INTO ".$table." SET ".$query_part;
		}else{
			$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
		}
		$res = imw_query($q);
		if($_POST['headquarter']==1 && $id){
			$qry="UPDATE ".$table." set headquarter='0' WHERE ".$pkId."!=".$id;
			$res=imw_query($qry);
		}
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.'.imw_error()."\n".$q;
		}
		break;
	case 'show_list':
		$q = "SELECT diagnosis_code_tbl.diagnosis_id,diagnosis_category.category,diagnosis_code_tbl.dx_code,diagnosis_code_tbl.d_prac_code,diagnosis_code_tbl.pqriCode,diagnosis_code_tbl.recall,diagnosis_code_tbl.diag_description,diagnosis_code_tbl.snowmed_ct,diagnosis_code_tbl.diag_cat_id FROM diagnosis_code_tbl INNER JOIN diagnosis_category ON diagnosis_code_tbl.diag_cat_id = diagnosis_category.diag_cat_id WHERE 1=1 ".$srchQry.$srchQryCat." ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$dx_cat=dx_catagory();
		echo json_encode(array('records'=>$rs_set,'dx_cat'=>$dx_cat));
		break;
	default: 
}
function dx_catagory()	{
	$q="select diag_cat_id,category from diagnosis_category order by category ASC";
	$res=imw_query($q);
	if(imw_num_rows($res)>0){
		$result=array();
		while($rs=imw_fetch_assoc($res)){
			$result[]=$rs;
		}
		return $result;
	}
	return false;
}

?>