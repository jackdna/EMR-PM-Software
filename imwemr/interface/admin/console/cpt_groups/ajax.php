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

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'epost_pre_defines';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "cpt_group_tbl";
$pkId	= "cpt_group_id";
$chkFieldAlreadyExist = "cpt_group_name";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." set cpt_group_status='1' WHERE ".$pkId." IN (".$id.")";
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
		
		$query_part = "";
		if($id){
			$_POST['modified_date']=date("Y-m-d H:i:s");
			$_POST['modified_operator_id']=$_SESSION['authId'];
		}else{
			$_POST['created_date']=date("Y-m-d H:i:s");
			$_POST['operator_id']=$_SESSION['authId'];	
		}
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){
			$_POST['modified_date']=date("Y-m-d H:i:s");
			$_POST['modified_operator_id']=$_SESSION['authId'];
			$qry_con=" AND ".$pkId."!='".$id."'";
		}else{
			$_POST['created_date']=date("Y-m-d H:i:s");
			$_POST['operator_id']=$_SESSION['authId'];	
		}
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
		$q = "SELECT cpt_group_id,cpt_group_name,cpt_code_name,if(cpt_group_status!='1','Active','In-Active') as status,cpt_group_status  FROM ".$table." ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$cpt_code_arr=cpt_code_arr();
		echo json_encode(array('records'=>$rs_set,'cpt_code_arr'=>$cpt_code_arr));
		break;
	default: 
}
function cpt_code_arr(){
	$prac_code_qry = "select cpt_prac_code from cpt_fee_tbl where cpt_prac_code != '' and delete_status = '0' group by cpt_prac_code";
	$prac_code_qry_obj = imw_query($prac_code_qry);
	$rows_cpt=array();
	if(imw_num_rows($prac_code_qry_obj)>0){
		while($rs_cpt=imw_fetch_assoc($prac_code_qry_obj)){
			$rows_cpt[]=$rs_cpt;
		}	
	}
	return $rows_cpt;		
}

?>