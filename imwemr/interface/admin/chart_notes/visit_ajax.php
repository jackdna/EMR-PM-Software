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

function copy_wnl_statements(){
	$sid = $_POST["sid"];
	$artid = $_POST["tid"];
	$cti = $_POST["cti"];

	$ar_tid_rec_id = array();
	$str_tid = implode(",",$artid);
	if(empty($str_tid)){ exit("0"); }
	$sql = "SELECT id, exam, phyid FROM chart_admin_wnl WHERE chart_template_id='".$cti."' AND phyid IN (".sqlEscStr($str_tid).") AND deleted='0' ";
	$rez = sqlStatement($sql);
	for($i=1;$row=sqlFetchArray($rez);$i++){
		$ar_tid_rec_id[$row["phyid"]][$row["exam"]] = $row["id"];
	}

	$sql_in = "";
	$sql = "SELECT id, exam, wnl FROM chart_admin_wnl WHERE chart_template_id='".$cti."' AND phyid='".$sid."' AND deleted='0' ";
	$rez = sqlStatement($sql);
	for($i=1;$row=sqlFetchArray($rez);$i++){
		$exm = $row["exam"];
		$s_wnl = $row["wnl"];

		foreach($artid as $k => $tid){
			if(empty($tid)){ $tid=0; }
			$tmp_id = isset($ar_tid_rec_id[$tid][$exm]) ? $ar_tid_rec_id[$tid][$exm] : 0;
			if(!empty($tmp_id)){
				$sql = "UPDATE chart_admin_wnl SET wnl = '".$s_wnl."' WHERE id = '".$tmp_id."'  ";
				$rr = sqlQuery($sql);
			}else{
				$sql_in .= "(NULL, '".$s_wnl."', '".$exm."', '".$cti."', '".$tid."'),";
			}
		}
	}

	$sql_in = trim($sql_in, ",");
	if(!empty($sql_in)){
		$sql_in = "INSERT INTO chart_admin_wnl (id, wnl, exam, chart_template_id, phyid) VALUES ".$sql_in;
		$rr = sqlQuery($sql_in);
	}

	echo "-1";
}
//--------------

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize arbitrary values - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'heard_options';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$table	= "tech_tbl";
$pkId	= "tech_id";
$chkFieldAlreadyExist = "name";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "DELETE FROM ".$table." WHERE ".$pkId." IN (".$id.")";
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
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."' ".$qry_con;
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
		$q = "SELECT tech_id,ptVisit FROM ".$table." ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set));
		break;
	case "copy_wnl":
		copy_wnl_statements();
		exit();
		break;


	default:
}
?>
