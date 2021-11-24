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

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'vendor_name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$name	= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : 'a';
$name	= xss_rem($name);	/** Reject parameter with arbitrary values - Security Fix */

$table	= "vendor_details";
$pkId	= "vendor_id";
$chkFieldAlreadyExist = "vendor_id";
if($name){
	$name=" AND vendor_name LIKE '".$name."%' ";
}
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." SET vendor_status = '1' WHERE ".$pkId." IN (".$id.")";
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
		
		$_POST['tel_num'] = core_phone_unformat($_POST['tel_num']);
		$_POST['mobile'] = core_phone_unformat($_POST['mobile']);
		$_POST['fax'] = core_phone_unformat($_POST['fax']);
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
		$q = "SELECT vendor_id,vendor_name,contact_name,vendor_address,
				if(length(tel_num)>9,concat(substr(tel_num,1,3),'-',substr(tel_num,4,3),'-',substr(tel_num,7,4)) ,tel_num)as tel_num,
				if(length(mobile)>9,concat(substr(mobile,1,3),'-',substr(mobile,4,3),'-',substr(mobile,7,4)),mobile)as mobile,
				if(length(fax)>9,concat(substr(fax,1,3),'-',substr(fax,4,3),'-',substr(fax,7,4)),fax)as fax,
				email 
				FROM vendor_details 
				WHERE vendor_status = '0'  ".$name."
				ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}

		echo json_encode(array('records'=>$rs_set,'q'=>$q));
		break;
	default: 
}
?>