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

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize arbitrary values - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'tos_prac_cod';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix  */

$table	= "tos_tbl";
$pkId	= "tos_id";
$chkFieldAlreadyExist = "tos_prac_cod";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "delete from ".$table." WHERE ".$pkId." IN (".$id.") AND headquarter!='1'";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		$tos_cat_id = $_POST['tos_cat_id'];
		$tos_category_description = $_POST['tos_category_description'];
		$tos_category = $_POST['tos_category'];
		if($tos_cat_id) {
			$updCatQry = "UPDATE tos_category_tbl SET tos_category_description='".addslashes($tos_category_description)."' WHERE tos_cat_id='".$tos_cat_id."'";
			$updCatRes=imw_query($updCatQry);
		}else{
			$chkCatQry = "SELECT tos_cat_id FROM tos_category_tbl WHERE tos_category='".trim($tos_category)."' ORDER BY tos_cat_id DESC LIMIT 0,1";
			$chkCatRes=imw_query($chkCatQry);	
			if(imw_num_rows($chkCatRes)>0){
				$chkCatRow = imw_fetch_assoc($chkCatRes);
				$tos_cat_id = $chkCatRow['tos_cat_id'];
			
			}else {
				$insCatQry = "INSERT INTO tos_category_tbl SET tos_category='".$tos_category."',tos_category_description='".$tos_category_description."'";
				$insCatRes=imw_query($insCatQry);
				$tos_cat_id = imw_insert_id();
			}
		}
		
		unset($_POST[$pkId]);
		unset($_POST['task']);
		unset($_POST['tos_category_description']);
		unset($_POST['tos_category']);
		$_POST['tos_cat_id'] = $tos_cat_id;
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."' AND tos_cat_id='".$tos_cat_id."'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		
		
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
			}
			$res = imw_query($q);
			if($id==''){$id=imw_insert_id(); }
			if($_POST['headquarter']=='1') {
				$hq_qry = "UPDATE ".$table." SET headquarter='0' WHERE ".$pkId." != '".$id."'";	
				$hq_res = imw_query($hq_qry);
			}
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
		$soNew = "tos_tbl.".$so;
		if($so) {
				
		}
		$q = "SELECT tos_tbl.tos_id,tos_category_tbl.tos_category, tos_category_tbl.tos_category_description,tos_tbl.tos_code,tos_tbl.tos_prac_cod, tos_tbl.tos_description,tos_tbl.status,if(tos_tbl.headquarter='1','Yes','No') as headquarter,tos_tbl.tos_cat_id
		FROM tos_tbl INNER JOIN tos_category_tbl ON tos_category_tbl.tos_cat_id = tos_tbl.tos_cat_id ORDER BY $so $soAD";				   
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$tos_categories = tosCategory();
		$hq_tos_id = headquaterId();
		echo json_encode(array('records'=>$rs_set,'tos_categories'=>$tos_categories,'hq_tos_id'=>$hq_tos_id));
		break;
	default: 
}

function tosCategory()	{
	$arrTosCats4TypeAhead = array();
	$q="select tos_cat_id, tos_category,tos_category_description from  tos_category_tbl";
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
function headquaterId()	{
	$q="select tos_id from tos_tbl where headquarter='1' ORDER BY tos_id DESC LIMIT 0,1";
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
function replaceLineBreaksNew($str,$repStr)
{	
	return preg_replace("(\r\n|\n|\r)", $repStr, $str);
}
?>