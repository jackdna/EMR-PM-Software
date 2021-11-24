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
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'status_text';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$provider_id	= isset($_REQUEST['provider_id']) ? trim($_REQUEST['provider_id']) : '0';
$table	= "imonitor_ready_for";
$pkId	= "id";
$chkFieldAlreadyExist = "status_text";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." SET delete_status='1', deleted_by='".$_SESSION['authId']."', deleted_on='".date('Y-m-d H:i:s')."' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';
		}
		break;
	case 'save_update':
		$arr_exam=array();
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		$_POST['status_color'] 		= str_replace('#','',$_POST['status_color']);
		$_POST['status_text_color'] = str_replace('#','',$_POST['status_text_color']);
		$_POST['created_on']=date("Y-m-d H:i:s");
		$_POST['created_by']=$_SESSION['authId'];
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE delete_status='0' AND ".$chkFieldAlreadyExist." LIKE '".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
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
		$q = "SELECT id,status_text,REPLACE(status_color,'#','') AS status_color ,REPLACE(status_text_color,'#','') AS status_text_color, provider_id FROM ".$table." WHERE delete_status='0' AND provider_id = '".$provider_id."' ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$pro_q = "SELECT id,lname, fname,mname FROM users where delete_status='0' AND Enable_Scheduler='1' ORDER BY lname,fname,mname";
		$pro_res = imw_query($pro_q);
		$pro_options = '<option value="0">Select</option>';
		if($pro_res && imw_num_rows($pro_res)>0){
			while($pro_rs = imw_fetch_assoc($pro_res)){
				$selected = '';
				if($provider_id==$pro_rs['id']){$selected=' selected=selected';}
				$pro_options .= '<option value="'.$pro_rs['id'].'"'.$selected.'>'.$pro_rs['lname'].', '.trim($pro_rs['fname'].' '.$pro_rs['mname']).'</option>';
			}
		}
		echo json_encode(array('records'=>$rs_set,'providers'=>$pro_options));
		break;
	case 'save_imon_settings':
		$casename	= isset($_POST['casename']) 	? 	trim($_POST['casename']) 	: 	'';
		$casevalue	= isset($_POST['casevalue']) 	? 	trim($_POST['casevalue']) 	: 	'';
		$q = "SELECT id FROM imonitor_settings WHERE setting_name='".$casename."'";
		$res = imw_query($q);
		$res1 = false;
		if($res && imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);
			$q = "UPDATE imonitor_settings SET practice_value='".$casevalue."', last_operator='".$_SESSION['authId']."', update_datetime='".date('Y-m-d H:i:s')."' WHERE id='".$rs['id']."' LIMIT 1";
			$res1 = imw_query($q);
		}else if($res && imw_num_rows($res)==0){
			//This code will create new setting entry as soon a new form element kept in iMM settings. ONLY HTML and JS work need to do.
			$q = "INSERT INTO imonitor_settings SET setting_name='".$casename."', default_value='', practice_value='".$casevalue."', last_operator='".$_SESSION['authId']."', update_datetime='".date('Y-m-d H:i:s')."'";
			$res1 = imw_query($q);
		}
		if($res1) echo 'Saved Successfully.';
		else {echo 'Failed to save settings.';}
		break;
	default: 
}

?>