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

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'title';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$table	= "common_prescription";
$pkId	= "presc_id";
$chkFieldAlreadyExist="pres_key";

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "DELETE from ".$table." WHERE ".$pkId." IN (".$id.")";
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
		$_POST['date_time2']=date("Y-m-d H:i:s");
		$_POST['providerID']=$_SESSION['authId'];
		$_POST['adminPresc']='1';
		if(!$_POST['chk_high_risk_medicine']){
			$_POST['chk_high_risk_medicine']=0;
		}
		if(!$_POST['chk_generic_drug_class']){
			$_POST['chk_generic_drug_class']=0;
		}
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
		$q = "SELECT presc_id,pres_key,drug,if(dosage_unit!='',concat(dosage,' ',dosage_unit),dosage) as dosage_unit_con,if(qty_unit!='',concat(qty,' ',qty_unit),qty) as qty_unit_con,direction,refill,substitute,if(chk_generic_drug_class='1','Yes','No') as chk_generic_drug_class,if(chk_high_risk_medicine='1','Yes','No') as chk_high_risk_medicine,dosage,dosage_unit,qty,qty_unit,eye,usage_1,usage_2,refill FROM ".$table." ORDER BY $so $soAD";
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

?>