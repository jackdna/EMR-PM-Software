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

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize arbitrary values - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'lab_radiology_name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix  */

$table	= "lab_radiology_tbl";
$pkId	= "lab_radiology_tbl_id";
$chkFieldAlreadyExist = "lab_radiology_tbl_id";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." SET lab_radiology_status = '2' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		$_POST['lab_radiology_phone'] = core_phone_unformat($_POST['lab_radiology_phone']);
		$_POST['lab_radiology_fax'] = core_phone_unformat($_POST['lab_radiology_fax']);
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
		// $q = "SELECT lab_radiology_tbl_id,lab_radiology_name,lab_indication,lab_loinc,lab_instructions,lab_contact_name,
		// 		if(length(lab_radiology_phone)>9,concat(substr(lab_radiology_phone,1,3),'-',substr(lab_radiology_phone,4,3),'-',substr(lab_radiology_phone,7,4)) ,lab_radiology_phone)as lab_radiology_phone,
		// 		if(length(lab_radiology_fax)>9,concat(substr(lab_radiology_fax,1,3),'-',substr(lab_radiology_fax,4,3),'-',substr(lab_radiology_fax,7,4)) ,lab_radiology_fax)as lab_radiology_fax,
		// 		lab_radiology_address,lab_radiology_zip,lab_radiology_city,lab_radiology_state,lab_type, dss_lab_id FROM ".$table." WHERE lab_radiology_status = '0' ORDER BY $so $soAD";

		$q = "SELECT lab_radiology_tbl_id,lab_radiology_name,lab_indication,lab_loinc,lab_instructions,lab_contact_name, lab_radiology_phone, lab_radiology_fax, lab_radiology_address, lab_radiology_zip, lab_radiology_city, lab_radiology_state, lab_type, dss_lab_id FROM ".$table." WHERE lab_radiology_status = '0' ORDER BY $so $soAD";

		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs["lab_instructions"] = addslashes($rs["lab_instructions"]);
				$rs["lab_radiology_address"] = addslashes($rs["lab_radiology_address"]);
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set,'qry'=>$q));
		break;
	default: 
}

?>