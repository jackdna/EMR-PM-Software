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
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'group_name';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "prov_group";
$pkId	= "id";
$chkFieldAlreadyExist = "group_name";
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
		
		$query_part = "";
		$query_part .= $chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."', ";
		
		$idstrs="";
		foreach($_POST as $k=>$v){
			if(strpos($k,"el_prov_phy")!==false){
				$idstrs.=$v.",";
			}
		}
		
		$idstrs=trim($idstrs, ",");
		
		$query_part .="phy='".$idstrs."', ";
		$query_part = substr($query_part,0,-2);
		
		//$qry_con = "";
		//if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'";
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)>0){ $rs = imw_fetch_assoc($r_c);  $id=$rs[$pkId];	}
		
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
		$q = "SELECT id,group_name,phy FROM ".$table." ORDER BY $so $soAD";		
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				
				$phy_nm = get_prov_grp_names($rs["phy"]);
				$rs["phy_nm"] = $phy_nm;
			
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set));
		break;
	default: 
}

//---
function get_prov_grp_names($strids){
	$al_nm="";
	$sql = "select fname,mname,lname from users where id IN (".$strids.") ORDER BY fname, mname, lname ";
	$r = imw_query($sql);
	while($rs = imw_fetch_assoc($r)){
		$tmp = "";
		if(!empty($rs["fname"])){ $tmp .= $rs["fname"]." ";   }
		if(!empty($rs["mname"])){ $tmp .= $rs["mname"]." ";   }
		if(!empty($rs["lname"])){ $tmp .= $rs["lname"]." ";   }
		$al_nm.=$tmp.", ";
	}
	$al_nm=trim($al_nm);
	$al_nm=trim($al_nm,",");
	return $al_nm;
}
?>