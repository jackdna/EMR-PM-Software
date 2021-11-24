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
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'poe_name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix  */

$table	= "statements_messages";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "update ".$table." set statements_messages_status='1' WHERE statements_messages_id IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id   = $_POST['statements_messages_id'];
		$count_name=$_POST['statements_messages_count'];
		unset($_POST['statements_messages_id']);
		unset($_POST['task']);
		$_POST['oprerator_id']=$_SESSION["authId"];
		$_POST['created_date']=="NOW()";
		$query_part = "";
		foreach($_POST as $k=>$v){
			if($v=="NOW()"){
				$query_part .= $k."=".addslashes($v).", ";
			}else{
				$query_part .= $k."='".addslashes($v)."', ";
			}
			
		}
		$qry_con="";
		if($id){$qry_con=" AND statements_messages_id!='".$id."'";}
		$query_part = substr($query_part,0,-2);
		$q_c="SELECT statements_messages_id from ".$table." WHERE statements_messages_count='".$count_name."' and statements_messages_status='0'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE statements_messages_id='".$id."'";
			}
		}else{
			echo 'enter_unique';exit;;
		}
		$res = imw_query($q);
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.'.imw_error()."\n".$q;
		}
			
		break;
	case 'show_list':
		$q = "SELECT statements_messages_id,statements_messages_count,statements_messages, if(oprerator_id!='',if((Select  concat(lname,', ',fname) from users where id=oprerator_id) IS NULL, '', (Select  concat(lname,', ',fname) from users where id=oprerator_id)),'') as oprerator_id FROM ".$table." where  statements_messages_status='0' ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set));
		break;
	default: 
}
?>