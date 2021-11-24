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

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'survey_title';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "survey_tbl";
$table_questions = "survey_question";
$pkId	= "survey_id";
$chkFieldAlreadyExist = "survey_title";
$chkFieldAlreadyExist1 = "survey_title";

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." set status='1' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';
		}
		break;
	case 'save_update':
		$arr_exam=array();
		$_POST['status'] = isset($_REQUEST["status"]) ? 1 : 0;
		$_POST['survey_description'] = $_REQUEST["survey_description"];
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		//$_POST['date_time']=date("Y-m-d H:i:s");
		//$_POST['providerID']="0";
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
		$q = "SELECT survey_id, survey_title, survey_description FROM ".$table." WHERE status!='1' ORDER BY $so $soAD";
		$r = imw_query($q) or die(imw_error());
		
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set));
		break;
	case 'change_status':
		$id = $_POST['rid'];
		$value = $_POST['value'];
		break;
		case 'save_update_questions':
		$arr_exam=array();
		$_POST['question_description'] = $_REQUEST["question_description"];
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
		$q_c="SELECT ".$pkId." from ".$table_questions." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		
		
			if($id==''){
				$q = "INSERT INTO ".$table_questions." SET ".$query_part;
			}else{
				$q = "UPDATE ".$table_questions." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
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
	default: 
}

?>