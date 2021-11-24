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
require_once("../../../../config/globals.php");

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'folder_name';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= constant("IMEDIC_SCAN_DB").".folder_categories";
$pkId	= "folder_categories_id";
$chkFieldAlreadyExist = "folder_name";

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
		$arr_exam=array();
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		unset($_POST['sub_folder']);
		$_POST['patient_id']=0;
		$_POST['created_by']=$_SESSION['authId'];
		$_POST['date_created']=date('Y-m-d H:i:s');
		$_POST['modified_section']='adminConMF';
		if($id){
			$_POST['modified_by']=$_SESSION['authId'];
			$_POST['date_modified']=date('Y-m-d H:i:s');
		}
		if(!$_POST['parent_id']){$_POST['parent_id']=0;}
		if(!$_POST['alertPhysician']){$_POST['alertPhysician']=0;}
		if(!$_POST['favourite']){$_POST['favourite']=0;}
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes(trim($v))."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){
			$qry_con=" AND ".$pkId."!='".$id."' ";
			if($_POST['parent_id']){$qry_con.=' AND parent_id="'.$_POST['parent_id'].'"';}
		}
		
		$q_c="SELECT ".$pkId." from ".$table." WHERE patient_id=0 ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
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
		$arr_folder_id=array();
		$q = "SELECT folder_categories_id,folder_name,alertPhysician,folder_status,favourite,parent_id,if(parent_id!='','1','0') as sub_folder FROM ".$table." WHERE parent_id=0 AND patient_id='0' ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[]= $rs;
				$rs1=getSubCateFolders($rs['folder_categories_id']);
				foreach($rs1 as $arrVal){
					if(count($arrVal)>0){
						$q1 = "SELECT folder_categories_id,folder_name,alertPhysician,folder_status,favourite,parent_id,if(parent_id!='','1','0') as sub_folder FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE folder_categories_id='".$arrVal."'  ORDER BY $so $soAD";
						$r1 = imw_query($q1);
						$row=imw_fetch_assoc($r1);
						$rs_set[]= $row;
					}
				}
			}
		}
		echo json_encode(array('records'=>$rs_set,'parent_cat'=>$rs_set));
		break;
	default:""; 
}
function getSubCateFolders($parentId,$arrRet=array()){
	$arrSubCats = array();
	$sql="Select folder_categories_id from ".constant("IMEDIC_SCAN_DB").".folder_categories where parent_id='$parentId' and patient_id='0' ORDER BY folder_name";
	$rez=imw_query($sql);
	while($row=imw_fetch_assoc($rez)){
		$arrSubCats[] = $row["folder_categories_id"];		
	}
	if(count($arrSubCats)>0){
		$arrRet = array_merge($arrRet,$arrSubCats);
		foreach($arrSubCats as $key => $val){
			$arrRet = getSubCateFolders($val,$arrRet);
		}
	}
	return $arrRet;
}

?>