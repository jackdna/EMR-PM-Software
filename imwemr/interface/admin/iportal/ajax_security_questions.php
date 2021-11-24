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
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'name';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "iportal_sec_questions";
$pkId	= "id";
$chkFieldAlreadyExist = "name";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." set del_status='1' WHERE ".$pkId." IN (".$id.")";
		$res 	=imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$arr_exam=array();
		$_POST['del_status'] = isset($_REQUEST["del_status"]) ? 1 : 0;
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
			$res =imw_query($q);
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
		$q = "SELECT id, name, date_format(last_modified,'".get_sql_date_format()." %h:%i %p') as modified_on, del_status FROM ".$table." ORDER BY $so $soAD";
		$r =imw_query($q);
		$rs_set = array();
		if($r &&imw_num_rows($r)>0){

			while($rs =imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set));
		break;
	case 'change_status':
		$id = $_POST['rid'];
		$value = $_POST['value'];
		$q 		= "UPDATE $table set del_status='$value' WHERE $pkId = $id";
		$res 	=imw_query($q);
		if($res){
			echo "true";
		}else{
			echo "false";//.imw_error()."\n".$q;
		}
		break;
	default: 
}

?>