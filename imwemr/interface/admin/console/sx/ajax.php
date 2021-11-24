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
require_once('../../../../library/classes/cls_common_function.php'); 
$OBJCommonFunction = new CLSCommonFunction;

/** 
 * Parameters Sanitization to prevent arbitrary values - Security Fixes
 **/
$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters */
$_REQUEST['alpha'] = xss_rem($_REQUEST['alpha'], 2);	/* Reject parameter with unwanted characters */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'title';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$table	= "lists_admin";
$pkId	= "id";
$chkFieldAlreadyExist="title";

//-------BEGIN CALCULATE RECORDS LIMIT----------
$page = (isset($_REQUEST['page']) && $_REQUEST['page']!="")?$_REQUEST['page']:1;
$record_limit = (isset($_REQUEST['record_limit']) && $_REQUEST['record_limit']!="")?$_REQUEST['record_limit']:20;
$offset = ($page-1) * $record_limit;
$count = $record_limit;
    
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." set delete_status='1' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
			$OBJCommonFunction->create_sx_procedures_xml();	
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		if($id){$_POST['date_time_modified']=date("Y-m-d H:i:s");
		}else{  $_POST['date_time_added']=date("Y-m-d H:i:s");}
		if($_POST['type']=="" || !$_POST['type']){$_POST['type']='5';}
		$_POST['operator_id']=$_SESSION['authId'];
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."' AND  delete_status!='1'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		
		
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
				$res = imw_query($q);
				$rid=imw_insert_id();
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
				$res = imw_query($q);
				$rid=$id;
			}
			if($res){
				/** update erp portal start */
				$surgery_sql="SELECT id,title,erp_surgery_id FROM ".$table." WHERE ".$pkId." = '".$rid."'";
				$surgery_res=imw_query($surgery_sql);
				if($surgery_res && imw_num_rows($surgery_res)>0){
					$surgery = imw_fetch_assoc($surgery_res);
					$data=array();
					$data['name']=$surgery['title'];
					$data['active']=true;
					$data['id']=$surgery['erp_surgery_id'];
					$data['externalId']=$surgery['id'];
					include_once($GLOBALS['srcdir']."/erp_portal/master_data.php");
					$obj_Master_data = new Master_data(); 
					$surgery_arr = $obj_Master_data->sync_surgery($data);
					if(count($surgery_arr)>0){
						$update_ethnicity = "update lists_admin set erp_surgery_id='".$surgery_arr['id']."' where id=".$surgery['id']." ";
						imw_query($update_ethnicity);
					}			
				}
				/** update erp portal end */
				echo 'Record Saved Successfully.';
				$OBJCommonFunction->create_sx_procedures_xml();
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		}else {
			echo "enter_unique";	
		}
		break;
	case 'show_list':
		$q = "SELECT id,(title),type FROM ".$table." lt_admn WHERE type in (5,6) and delete_status=0";
        
        if(isset($_REQUEST['alpha']) && $_REQUEST['alpha'] !="" && $_REQUEST['searchStr']==""){
			if($_REQUEST['alpha']=='0-9'){
				$q .= " AND (lt_admn.title LIKE '1%' OR lt_admn.title LIKE '2%'  OR lt_admn.title LIKE '3%'  OR lt_admn.title LIKE '4%' OR lt_admn.title LIKE '5%' OR lt_admn.title LIKE '6%' OR lt_admn.title LIKE '7%' OR lt_admn.title LIKE '8%' OR lt_admn.title LIKE '9%' OR lt_admn.title LIKE '0%' )";
			}else{
				$q .= " AND lt_admn.title LIKE '".$_REQUEST['alpha']."%' ";
			}
		}
		if(isset($_REQUEST['searchStr']) && $_REQUEST['searchStr']!=""){
			$q .= " And lt_admn.title LIKE '".$_REQUEST['searchStr']."%' ";
		}
        $q .= "group by lt_admn.title  ORDER BY lt_admn.$so $soAD ";
        $total_pages = ceil(imw_num_rows(imw_query($q))/$count);
		$q .= ($total_pages < $page ) ? "LIMIT 0,$record_limit" : "LIMIT $offset,$count"; 
        
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set,"total_pages"=>$total_pages));
		break;
	default: 
}

?>