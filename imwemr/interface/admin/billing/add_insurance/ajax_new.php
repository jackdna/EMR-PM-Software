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

?><?php 
/*
File: ajax_new.php
Purpose: Add/modify/save records of insurances
Access Type: Include
*/
require_once(dirname('__FILE__')."/../../../globals.php");
require_once(dirname('__FILE__')."/../../../main/Functions.php");
require_once('../../../common/CLSCommonFunction.php'); 
$OBJCommonFunction = new CLSCommonFunction;
$task	= isset($_REQUEST['ajax_task']) ? trim($_REQUEST['ajax_task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'to_do_id';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "insurance_companies";
$pkId	= "id";
$chkFieldAlreadyExist="task";
//-------BEGIN CALCULATE RECORDS LIMIT----------
	$page = (isset($_REQUEST['page']) && $_REQUEST['page']!="")?$_REQUEST['page']:1;
	$record_limit = (isset($_REQUEST['record_limit']) && $_REQUEST['record_limit']!="")?$_REQUEST['record_limit']:19;
	$offset = ($page-1) * $record_limit;
	$count = $record_limit;
	
//-------END CALCULATE RECORDS LIMIT----------
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		//$q 		= "DELETE FROM ".$table." WHERE ".$pkId." IN (".$id.")";
		$q 		= "UPDATE ".$table." SET ins_del_status = '1' WHERE ".$pkId." IN (".$id.")";
		//echo $q;die();
		$res 	= mysql_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.mysql_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['ajax_task']);
		$_POST['created_date'] = date('y-m-d');
		$_POST['source'] = '1';
		$query_part = "";
		foreach($_POST as $k=>$v){
			switch($k){
				case "ref_phy_group":
					$ref_phy_grp = $_POST['ref_phy_group'];
				break;
				case "confirm_password":
				case "hid_password":
				break;
				case "password":
				if($v!=""){
					$query_part .= $k."='".md5($v)."', ";
				}
				break;
				case "start_date":
				case "end_date":
				$query_part .= $k."='".FormatDateInsert($v)."', ";
				break;
				case "physician_fax":
				case "physician_phone":
				$query_part .= $k."='".core_phone_unformat($v)."', ";
				break;
				case "access_pri":
				$access_pri = implode(",",$v);
				$query_part .= $k."='".$access_pri."', ";
				break;
				case "referedPhysician":
				$query_part .= $k."='".implode(",",$v)."', ";
				break;
				case "default_facility":
				$query_part .= $k."='".implode(",",$v)."', ";
				break;
				default:
				$query_part .= $k."='".addslashes($v)."', ";
			}
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."' ";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE  AND ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."' ".$qry_con;
		$r_c=mysql_query($q_c);
		if(mysql_num_rows($r_c)==0){		
		
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
				$res = mysql_query($q);
				$insert_id = mysql_insert_id();
				
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
				$res = mysql_query($q);
			}
			if($res){
				echo 'Record Saved Successfully.';
			}else{
				echo 'Record Saving failed.'.mysql_error()."\n".$q;
			}
		}else {
			echo "enter_unique";	
		}
		break;
	case "set_status":
		$sql = "UPDATE ".$table." SET ins_del_status = ".$_REQUEST['status']." WHERE ".$pkId." = ".$_REQUEST['id'];
		mysql_query($sql);
		
	break;
	case 'show_list':
		$q = "SELECT * FROM ".$table." 
			  WHERE ";
			  
		$status = (isset($_REQUEST['s'])	&& $_REQUEST['s'] !="")?$_REQUEST['s']:0;
		if($status == 0)
		$q .= " (ins_del_status = 0 || ins_del_status IS NULL)";
		else if($status != 'all')
		$q .= " ins_del_status = ".$status;
		
		if(isset($_REQUEST['alpha']) && $_REQUEST['alpha'] !="" && $_REQUEST['searchStr']=="")
		$q .= " AND name LIKE '".$_REQUEST['alpha']."%'";
		if(isset($_REQUEST['searchStr']) && $_REQUEST['searchStr']!=""){
			$q .= " AND (name LIKE '".$_REQUEST['searchStr']."%' OR in_house_code LIKE '".$_REQUEST['searchStr']."%')";
		}
		$q .= " ORDER BY $so $soAD ";
		$total_pages = ceil(mysql_num_rows(mysql_query($q))/$count);
		$q .= "LIMIT $offset,$count";//echo $q;die();
		$r = mysql_query($q);
		$rs_set =array();
		if($r && mysql_num_rows($r)>0){
			while($rs = mysql_fetch_assoc($r)){
				//$rs_set[] = $rs;
				$insCompFullAdd = "";
				if ($rs['contact_address']){
					$insCompFullAdd .= trim($rs['contact_address']);
				}
				if($rs['City'] != '' && $rs['State'] != ''){
					$insCompFullAdd .= " ".trim($rs['City']).', '.trim($rs['State']).' '.trim($rs['Zip']);
				}
				if($rs['zip_ext'] != ''){
					$insCompFullAdd .= "-".trim($rs['zip_ext']);
				}
				$rs['address'] = $insCompFullAdd;
				
				if($rs['Insurance_payment']=='Electronics'){$rs['Insurance_payment']='Electronic';}
				if($rs['secondary_payment_method']=='Electronics'){$rs['secondary_payment_method']='Electronic';}
				$rs['pri_sec_pay'] = ucfirst($rs['Insurance_payment']).'&nbsp;/&nbsp;'.ucfirst($rs['secondary_payment_method']);
				$rs['claim_type']= get_default_claim_type();
				if($rs['claim_type'] > 0){
					$rs['claim_type'] = 'Medicare';
				}
				if($rs['ins_del_status']==1) {
					$rs['status'] = "<a href='javascript:void()'  onclick='set_status(1,".$rs['id'].")' target='_parent' class='text_10ab'><img src='../../../../images/inactive.jpg' title='Inactive' class='noborder'></a>";
                }else{ 
					$rs['status'] = "<a href='javascript:void()'  onclick='set_status(1,".$rs['id'].")' target='_parent' class='text_10ab'><img src='../../../../images/active.jpg' title='Active' class='noborder'></a>";
                }
				$rs_set[] = $rs;
			}
		}
		//echo count($rs_set);die();
		echo json_encode(array('records'=>$rs_set,"total_pages"=>$total_pages));
		break;
	default: 
}
function get_default_claim_type(){
	$returnStr = '';
	$q = "select name from copay_policies";		
	$r = mysql_query($q);
	$row = mysql_fetch_assoc($r);
	$returnStr = $row['name'];
	return $returnStr;
}
?>