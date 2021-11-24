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

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$s		= isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'mac_address';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$p		= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
$f		= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';
//ajax.php?task=show_list&s=Active&so=pos_prac_code&soAD=ASC
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE mac_room_desc SET delete_status=1 WHERE id IN ($id)";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST['id'];
		unset($_POST['id']);
		unset($_POST['task']);
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		if($id==''){
			$q = "INSERT INTO mac_room_desc SET ".$query_part;
		}else{
			$q = "UPDATE mac_room_desc SET ".$query_part." WHERE id='".$id."'";
		}
		$res = imw_query($q);
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.';//.imw_error()."\n".$q;
		}
		break;
	case 'show_list':
        $q = "Select * FROM mac_room_desc WHERE mac_address != '' AND delete_status=0 ORDER BY $so $soAD";
        if($so == 'name'){
           $q = "Select mac_room_desc.* FROM mac_room_desc
                LEFT JOIN facility ON facility.id = mac_room_desc.fac_id
                WHERE mac_room_desc.mac_address != '' AND mac_room_desc.delete_status = 0 ORDER BY facility.$so $soAD ";
        }
        $r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				unset($rs['delete_status']);
				$rs_set[] = $rs;
			}
		}
		
		$facs = array();
		$q2 = "SELECT id as facid,name as facname FROM facility ORDER BY facility_type";
		$res2 = imw_query($q2);
		if($res2 && imw_num_rows($res2)>0){
			while($rs2 = imw_fetch_assoc($res2)){
				$facs[] = $rs2;
			}
		}
		
		echo json_encode(array('records'=>$rs_set,'facilities'=>$facs));
		break;
	case 'match_record':
		$pc_name = $_POST['pc_name'];
		$return = 0;
		$qry = "SELECT * FROM mac_room_desc WHERE mac_address = '".$pc_name."'";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$arr = imw_fetch_array($res);
			$return = $arr[0]["id"]."~~|~~".urlencode($arr[0]["mac_address"])."~~|~~".urlencode($arr[0]["room_no"])."~~|~~".urlencode($arr[0]["descs"]);
		}
		echo $return;
		break;
	default: 
}
?>
