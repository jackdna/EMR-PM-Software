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
$table	= "cas_reason_code";

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "DELETE FROM  ".$table."  WHERE cas_id IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id   = $_POST['cas_id'];
		$count_name=$_POST['cas_code'];
		if(!$_POST['cas_update_allowed']){$_POST['cas_update_allowed']=0;}
		if(!$_POST['cas_adjustment_negative']){$_POST['cas_adjustment_negative']=0;}
		if($_POST['cas_action_type']!="Write Off"){
			$_POST['cas_update_allowed']=$_POST['cas_adjustment_negative']=0;
		}
		unset($_POST['cas_id']);
		unset($_POST['task']);
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$qry_con="";
		if($id){$qry_con=" AND cas_id!='".$id."'";}
		$query_part = substr($query_part,0,-2);
		$q_c="SELECT cas_id from ".$table." WHERE cas_code='".$count_name."'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE cas_id='".$id."'";
			}
		}else{
			echo 'enter_unique';exit;;
		}
		$res = imw_query($q);
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.';//.imw_error()."\n".$q;
		}
			
		break;
	case 'show_list':
		$q = "SELECT cas_id,cas_code,cas_desc,cas_action_type,cas_adjustment_negative,cas_update_allowed FROM ".$table." ORDER BY $so $soAD";
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