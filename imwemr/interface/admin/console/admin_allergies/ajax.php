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
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'allergie_name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$table	= "allergies_data";
$pkId	= "allergies_id";
$chkFieldAlreadyExist = "allergie_name";
//-------BEGIN CALCULATE RECORDS LIMIT----------
	$page = (isset($_REQUEST['page']) && $_REQUEST['page']!="")?$_REQUEST['page']:1;
	$record_limit = (isset($_REQUEST['record_limit']) && $_REQUEST['record_limit']!="")?$_REQUEST['record_limit']:20;
	$offset = ($page-1) * $record_limit;
	$count = $record_limit;
	
//-------END CALCULATE RECORDS LIMIT----------

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$externalIds=explode(',',$id);
		include_once($GLOBALS['srcdir']."/erp_portal/master_data.php");
		$obj_Master_data = new Master_data();
		foreach($externalIds as $key=>$externalId){
			$externalId=trim($externalId);
			$allergy_arr = $obj_Master_data->sync_allergy_delete(array(),$externalId);
			
		} 
		
		$q 		= "delete from ".$table." WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
			$OBJCommonFunction -> create_allergies_xml();
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$arr_exam=array();
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		$_POST['provider_id']="0";
		$query_part = "";
		foreach($_POST as $k=>$v){
			if($k=="procedure"){$k="`procedure`";}
			$query_part .= $k."='".addslashes(trim($v))."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
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
				$allergy_sql="SELECT allergies_id,allergie_name,erp_allergy_id FROM ".$table." WHERE ".$pkId." = '".$rid."'";
				$allergy_res=imw_query($allergy_sql);
				if($allergy_res && imw_num_rows($allergy_res)>0){

					$allergy = imw_fetch_assoc($allergy_res);
					$data=array();
					$data['name']=$allergy['allergie_name'];
					$data['active']=true;
					$data['id']=$allergy['erp_allergy_id'];
					$data['externalId']=$allergy['allergies_id'];
					include_once($GLOBALS['srcdir']."/erp_portal/master_data.php");
					$obj_Master_data = new Master_data(); 
					$allergy_arr = $obj_Master_data->sync_allergy($data);
					if(count($allergy_arr)>0){
						$update_ethnicity = "update allergies_data set erp_allergy_id='".$allergy_arr['id']."' where allergies_id=".$allergy['allergies_id']." ";
						imw_query($update_ethnicity);
		
					}
					
				}

				/** update erp portal end */

				echo 'Record Saved Successfully.';
				$OBJCommonFunction -> create_allergies_xml();
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		}else {
			echo "enter_unique";	
		}
		break;
	case 'show_list':
		$q = "SELECT allergies_id,allergie_name,alias,recall_code,`procedure`,description FROM ".$table;
		$arrWhere = array();
		if(isset($_REQUEST['alpha']) && $_REQUEST['alpha'] !="" && $_REQUEST['searchStr']==""){
			if($_REQUEST['alpha']=='0-9'){
				$arrWhere[] = "  allergie_name LIKE '1%' OR allergie_name LIKE '2%'  OR allergie_name LIKE '3%'  OR allergie_name LIKE '4%' OR allergie_name LIKE '5%' OR allergie_name LIKE '6%' OR allergie_name LIKE '6%' OR allergie_name LIKE '8%' OR allergie_name LIKE '9%' OR allergie_name LIKE '0%'";
			}else{
				$arrWhere[] = "   allergie_name LIKE '".$_REQUEST['alpha']."%'";
			}
		}
		if(isset($_REQUEST['searchStr']) && $_REQUEST['searchStr']!=""){
			$arrWhere[] = "  (allergie_name LIKE '".$_REQUEST['searchStr']."%')";
		}
		if(count($arrWhere)>0){
			$q .= " WHERE ".implode(" AND ",$arrWhere);
		}
		$q .= " ORDER BY $so $soAD ";
		$total_pages = ceil(imw_num_rows(imw_query($q))/$count);
		if( $page >  $total_pages ) 
			$q .= "LIMIT 0,$count";
		else
			$q .= "LIMIT $offset,$count"; 
		
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