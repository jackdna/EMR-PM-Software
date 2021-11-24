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

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'vendor_name';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$name	= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : 'a';
$table	= "optical_lenses";
$pkId	= "optical_lenses_id";
$chkFieldAlreadyExist = "vendor_name";
if($name){
	$name=" AND vendor_name LIKE '".$name."%' ";
}

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." SET lens_status = '1' WHERE ".$pkId." IN (".$id.")";
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
		$firstChar = $_POST['patient_discount_actual'][0];
		if($firstChar != '$'){
			$_POST['patient_discount_actual'] = substr($_POST['patient_discount_actual'],0,-1);
			//$_POST['patient_discount_actual'] = NULL;
		}
		else{
			$_POST['patient_discount_actual'] = substr($_POST['patient_discount_actual'],1);
			//$_POST['discount_patient'] = NULL;
		}
		$firstCharNew = $_POST['family_discount_actual'][0];
		if($firstCharNew != '$'){
			$_POST['family_discount_actual'] = substr($_POST['family_discount_actual'],0,-1);
			//$_POST['family_discount_actual'] = NULL;
		}
		else{
			$_POST['family_discount_actual'] = substr($_POST['family_discount_actual'],1);
			//$_POST['discount_family_friend'] = NULL;
		}	
		
		$firstCharCost = $_POST['Cost_Price'][0];
		if($firstCharCost == '$'){
			$_POST['Cost_Price'] = substr($_POST['Cost_Price'],1);
		}
		$firstCharRetail = $_POST['Retail_Price'][0];
		if($firstCharRetail == '$'){
			$_POST['Retail_Price'] = substr($_POST['Retail_Price'],1);
		}
		if($_POST['progresive_text']=="Other"){
			$_POST['progresive_text'] = $_POST['progresive_text1'];
			unset($_POST['progresive_text1']);
		} else{
			unset($_POST['progresive_text1']);
		}
		if($_POST['lens_material']=="Other"){
			$_POST['lens_material'] = $_POST['lens_material1'];
			unset($_POST['lens_material1']);
		} else{
			unset($_POST['lens_material1']);
		}
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."'";unset($_SESSION["OPT_VENDOR_PIC"]);}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
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
		$q = "select optical_lenses_id, vendor_name, Tab_val, lens_type, lens_material, vendor_name as lab_name, Cost_Price, Retail_Price, pos_facility_id, Bifocal, Trifocal, progresive_text, progresive_text as progresive_text1, lens_material as lens_material1, lens_cooment, patient_discount_actual, family_discount_actual, bar_code_id from $table where lens_status = '0' ".$name." ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$pos_facility_list = pos_facility_fun();
		echo json_encode(array('records'=>$rs_set,'pos_facility_list'=>$pos_facility_list));
		break;
	default: 
}

function pos_facility_fun(){
	$qry="Select a.pos_facility_id,a.facilityPracCode,b.pos_prac_code from pos_facilityies_tbl a,pos_tbl b
			WHERE a.pos_id = b.pos_id order by a.facilityPracCode ASC, a.headquarter DESC";	
	$res=imw_query($qry);
	$rs_cat=array();
	if($res && imw_num_rows($res)>0){
		while($rset=imw_fetch_assoc($res)){
			$rs_cat[]=$rset;
		}	
	}
	return $rs_cat;
}
?>