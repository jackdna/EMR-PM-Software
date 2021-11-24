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
require_once("../../../config/globals.php");
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'pos_facility_group';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "pos_facility_group";
$pkId	= "pos_fac_grp_id";

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "delete from ".$table." WHERE ".$pkId." IN (".$id.") ";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		$query_part = "";
		
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
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

		break;
	case 'show_list':
		$q = "SELECT * FROM ".$table." WHERE delete_status=0 ORDER BY $so $soAD";
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
function pos_tbl()	{
	$q="select pos_id,pos_prac_code,pos_description from pos_tbl";
	$res=imw_query($q);
	if(imw_num_rows($res)>0){
		$result=array();
		while($rs=imw_fetch_assoc($res)){
			$result[]=$rs;
		}
		return $result;
	}
	return false;
}

?>