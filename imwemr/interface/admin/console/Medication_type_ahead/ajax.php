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
$table	= "medicine_data";
$pkId	= "id";
$chkFieldAlreadyExist = "medicine_name";

//-------BEGIN CALCULATE RECORDS LIMIT----------
	$page = (isset($_REQUEST['page']) && $_REQUEST['page']!="")?$_REQUEST['page']:1;
	$record_limit = (isset($_REQUEST['record_limit']) && $_REQUEST['record_limit']!="")?$_REQUEST['record_limit']:20;
	$offset = ($page-1) * $record_limit;
	$count = $record_limit;
	
//-------END CALCULATE RECORDS LIMIT----------
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q_od = "UPDATE order_details
					SET delete_status = 1
					WHERE med_id IN (".$id.")
						AND (order_type_id = 1 
							OR LOWER(o_type) = 'meds'
							OR LOWER(o_type) = 'med'
							)
				";	
		imw_query($q_od);			
		//$q 		= "delete from ".$table." WHERE ".$pkId." IN (".$id.")";
		$q 		= "UPDATE ".$table." SET del_status = 1 WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
			$OBJCommonFunction -> create_medications_xml();
			//START delete from erp portal data
			include_once($GLOBALS['srcdir']."/erp_portal/master_data.php");
            $obj_Master_data = new Master_data(); 
            $result = $obj_Master_data->delete_medication_master(array(),$id);
            //END delete from erp portal data
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$arr_exam=array();
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		if(!$_POST['ocular']){$_POST['ocular']="";}
		if(!$_POST['glucoma']){$_POST['glucoma']="";}
		if(!$_POST['ret_injection']){$_POST['ret_injection']="";}
		if(!$_POST['prescription']){$_POST['prescription']="";}
		if(!$_POST['alert'] || trim($_POST['alertmsg'])==""){$_POST['alert']="";}
		if(!$_POST['tracked_inventory']){$_POST['tracked_inventory']="";}
		$_POST['provider_id']="0";
		$query_part = "";
		foreach($_POST as $k=>$v){
			switch($k){
				case "dosage":
				case "qty":
				case "sig":
				case "refill":
				case "ndccode":
				break;
				default:
				$query_part .= $k."='".addslashes(trim($v))."', ";
			}
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = " AND del_status = 0 ";
		if($id){$qry_con=" AND ".$pkId."!='".$id."' AND del_status = 0";}		
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		
		
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
			}
			$res = imw_query($q);
			if($id == "")
			$id = imw_insert_id();
			if($res){
				//------BEGIN CREATE/UPDATE ORDERS FOR MEDS ---------------
				//if($_POST['ocular'] == 1){
				if(!empty($id)){
					$q_od = "SELECT id FROM order_details WHERE med_id = '".$id."' AND delete_status = 0";
					$r_od = imw_query($q_od);
					if(imw_num_rows($r_od)>0){
						$row_order = imw_fetch_assoc($r_od);
						$q_od = "UPDATE order_details SET name = '".addslashes(trim($_POST['medicine_name']))."', 
						   			 order_type_id='1',
									 o_type='Meds',
									 med_id = '".$id."',
									 dosage = '".$_POST['dosage']."',
									 qty = '".$_POST['qty']."',
									 sig = '".$_POST['sig']."',
									 refill = '".$_POST['refill']."',
									 ndccode = '".$_POST['ndccode']."',
									 modified_by = '".$_SESSION['authId']."',
									 modified_on = '".date('Y-m-d H:i:s')."'
							  WHERE id = '".$row_order['id']."'";
						imw_query($q_od);
					}else{
						$q_od ="INSERT INTO order_details 
							 SET name = '".addslashes(trim($_POST['medicine_name']))."',
								order_type_id='1',
								o_type='Meds',
								med_id = '".$id."',
								dosage = '".$_POST['dosage']."',
							 	qty = '".$_POST['qty']."',
							 	sig = '".$_POST['sig']."',
							 	refill = '".$_POST['refill']."',
								ndccode = '".$_POST['ndccode']."',
								created_by = '".$_SESSION['authId']."',
								created_on = '".date('Y-m-d H:i:s')."'
							";
						imw_query($q_od);
					}

					/** START update erp portal */
					$medicinSql="SELECT id,medicine_name, ocular, glucoma, ret_injection, prescription, alert, tracked_inventory, erp_medication_id FROM ".$table." WHERE ".$pkId." = '".$id."'";
					$medicinRes=imw_query($medicinSql);
		            if($medicinRes && imw_num_rows($medicinRes)>0){

		                $medicin = imw_fetch_assoc($medicinRes);
		                $data=array();
		                $data['name']=$medicin['medicine_name'];
		                $data['active']=true;
		                $data['id']=$medicin['erp_medication_id'];
						$data['Strength']= 'blank';
		                $data['externalId']=$medicin['id'];
		                include_once($GLOBALS['srcdir']."/erp_portal/master_data.php");
		                $obj_Master_data = new Master_data(); 
		                $medicinArr = $obj_Master_data->sync_medication_master($data);
		                if(count($medicinArr)>0){
		                    $updateMedicin = "update ".$table." set erp_medication_id='".$medicinArr['id']."' where id=".$medicin['id']." ";
		                    imw_query($updateMedicin);
		                }
		            }
					/** END update erp portal */
				}
				//------END CREATE/UPDATE ORDERS FOR MEDS ---------------
				echo 'Record Saved Successfully.';
				$OBJCommonFunction -> create_medications_xml();
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		}else {
			echo "enter_unique";	
		}
		break;
	case 'show_list':
			$q = "SELECT md.id, md.medicine_name, md.ocular, md.glucoma,
				md.ret_injection, md.alias, md.recall_code, md.med_procedure, md.description, md.prescription,
				md.alertmsg, md.alert, md.ccda_code, md.fdb_id, md.tracked_inventory,opt_med_name,opt_med_id,opt_med_upc
				FROM ".$table." md
				WHERE md.del_status = 0";
		
		if(isset($_REQUEST['alpha']) && $_REQUEST['alpha'] !="" && $_REQUEST['searchStr']==""){
			if($_REQUEST['alpha']=='0-9'){
				$q .= " AND (md.medicine_name LIKE '1%' OR md.medicine_name LIKE '2%'  OR md.medicine_name LIKE '3%'  OR md.medicine_name LIKE '4%' OR md.medicine_name LIKE '5%' OR md.medicine_name LIKE '6%' OR md.medicine_name LIKE '7%' OR md.medicine_name LIKE '8%' OR md.medicine_name LIKE '9%' OR md.medicine_name LIKE '0%' )";
			}else{
				$q .= " AND md.medicine_name LIKE '".$_REQUEST['alpha']."%' ";
			}
		}
		if(isset($_REQUEST['searchStr']) && $_REQUEST['searchStr']!=""){
			$q .= " And md.medicine_name LIKE '".$_REQUEST['searchStr']."%' ";
		}
		
		$q .= " ORDER BY md.$so $soAD ";
		$total_pages = ceil(imw_num_rows(imw_query($q))/$count);
		$q .= ($total_pages < $page ) ? "LIMIT 0,$record_limit" : "LIMIT $offset,$count"; 
		
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$q_od = "SELECT od.dosage, od.qty , od.sig, od.refill, od.ndccode 
						 FROM order_details od 
						 WHERE med_id = '".$rs['id']."'
						 AND delete_status = 0
						 ORDER BY id ASC
						 LIMIT 1
						 ";
				$rs_od = imw_fetch_assoc(imw_query($q_od));
				$rs['dosage'] = $rs_od['dosage'];
				$rs['qty'] = $rs_od['qty'];
				$rs['sig'] = $rs_od['sig'];
				$rs['refill'] = $rs_od['refill'];
				$rs['ndccode'] = $rs_od['ndccode'];
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set,"total_pages"=>$total_pages));
		break;
	default: 
}

?>