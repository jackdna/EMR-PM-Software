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
$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'title';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "icd10_laterality";
$pkId	= "id";
$chkFieldAlreadyExist = "title";

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "delete from ".$table." WHERE ".$pkId." IN (".$id.")";
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
		$q_c="SELECT ".$pkId." from ".$table." WHERE under='".intval($_POST['under'])."' AND ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."' ".$qry_con;	
		$r_c=imw_query($q_c)or die(imw_error());
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
		$q = "SELECT id,title,abbr,code,under FROM ".$table." WHERE deleted=0 and under=0 ORDER BY $so $soAD";
		$r = imw_query($q);echo imw_error();
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
				$q1 = "SELECT id,title,abbr,code,under FROM ".$table." WHERE deleted=0 and under='".$rs['id']."' ORDER BY $so $soAD";
				$r2 = imw_query($q1);echo imw_error();
				while($rs2 = imw_fetch_assoc($r2)){
					$rs_set[] = $rs2;
				}
			}
		}
		
		$icd10_l=icd10_laterality();
		echo json_encode(array('records'=>$rs_set,'icd10_l'=>$icd10_l));
		break;
	default: 
}
function icd10_laterality($under=0){
	$q_cat = "SELECT i1.id, if (GROUP_CONCAT(i2.abbr) IS NULL, i1.title, CONCAT(i1.title,' (',GROUP_CONCAT(i2.abbr ORDER BY i2.id),')')) AS title FROM `icd10_laterality` i1 LEFT JOIN icd10_laterality i2 ON (i1.id=i2.under) WHERE i1.under=0 AND  i1.deleted=0 GROUP BY i1.id";
	$r_cat = imw_query($q_cat);
	$rs_set = array();
	if($r_cat && imw_num_rows($r_cat)>0){
		while($rs = imw_fetch_assoc($r_cat)){
			$id 		= $rs['id'];
			$rs_set[$id]	= $rs;
		}
	}
	return $rs_set;
}
?>