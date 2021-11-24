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
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'folder_name';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';


//user_messages_folder
//folder_id, folder_name, folder_status, provider_id, created_by, date_created, date_modified, modified_by


switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "delete from user_messages_folder WHERE folder_id IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
        
        //pre($_POST); die;
		$arr_exam=array();
		$id = $_POST['folder_id'];
		unset($_POST['folder_id']);
		unset($_POST['task']);

		$_POST['created_by']=$_SESSION['authId'];
		$_POST['date_created']=date('Y-m-d H:i:s');
		if($id){
			$_POST['modified_by']=$_SESSION['authId'];
			$_POST['date_modified']=date('Y-m-d H:i:s');
		}
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes(trim($v))."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){
			$qry_con=" AND folder_id!='".$id."' ";
		}

		$q_c="SELECT folder_id from user_messages_folder WHERE folder_status=0 AND folder_name='".$_POST['folder_name']."'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){
			if($id==''){
				$q = "INSERT INTO user_messages_folder SET ".$query_part;
			}else{
				$q = "UPDATE user_messages_folder SET ".$query_part." WHERE folder_id='".$id."' ";
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
		$arr_folder_id=array();
		$q = "SELECT * FROM user_messages_folder ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[]= $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set));
		break;
	default:""; 
}


?>