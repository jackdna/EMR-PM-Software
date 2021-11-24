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
$table	= "ref_group_tbl";
$pkId	= "ref_group_id";
$chkFieldAlreadyExist = "ref_group_name";
$op_id = $_SESSION['authId'];
$curDate = date("Y-m-d H:i:s");

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." set ref_group_status='1', modified_operator_id = '".$op_id."', modified_date = '".$curDate."' WHERE ".$pkId." IN (".$id.")";
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
		unset($_POST['new_reff_ids']);
		unset($_POST['ref_phy_id_db']);
		if($_POST['ref_phy_id']) {
			$_POST['ref_id'] = $_POST['ref_phy_id'];
		}
		unset($_POST['ref_phy_id']);
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
		$q = "SELECT ref_group_id,ref_group_name,ref_id,if(ref_group_status!='1','Active','In-Active') as status,ref_group_status,ref_id as ref_phy_id ,ref_id as ref_phy_id_db FROM ".$table." ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		$ref_code_arr=ref_code_arr();
		if($r && imw_num_rows($r)>0){
				$rs1=array();
			while($rs = imw_fetch_assoc($r)){
				$rs_set[] =($rs);
			}
		}
		echo json_encode(array('records'=>$rs_set,'ref_code_arr'=>$ref_code_arr));
		break;
	default: 
}
function ref_code_arr(){
//"select FirstName,MiddleName,LastName,physician_Reffer_id from refferphysician where delete_status = '0'";	
	
	$prac_code_qry = "select physician_Reffer_id,IF(MiddleName!='',CONCAT(LastName,', ',FirstName,' ',MiddleName,'; '),CONCAT(LastName,', ',FirstName,'; ')) as reff_name from refferphysician where LastName != '' and FirstName!=''  and delete_status = '0' ORDER BY LastName";
	$prac_code_qry_obj = imw_query($prac_code_qry);
	$rows_reff=array();
	if(imw_num_rows($prac_code_qry_obj)>0){
		while($rs_cpt=imw_fetch_assoc($prac_code_qry_obj)){
			$rows_reff[]=$rs_cpt;
		}	
	}
	return $rows_reff;		
}


?>