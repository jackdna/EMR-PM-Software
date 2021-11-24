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
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'manufacturer,style';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "contactlensemake";
$pkId	= "make_id";
$chkFieldAlreadyExist = "make_id";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." SET del_status = '1' WHERE ".$pkId." IN (".$id.")";
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
		
		$price = $_POST['price'];
		$pos = strpos($_POST['price'], '$');
		if($pos===false){
			$price = $_POST['price'];
		}else{
			$price = substr($_POST['price'],1);		
		}

		$cpt_fee_id = $_POST['cpt_fee_id'];		
		//if($price>0 && $cpt_fee_id>0){
		if($cpt_fee_id>0){
			$cptQry = "Update cpt_fee_table SET cpt_fee='".$price."' WHERE cpt_fee_id='".$cpt_fee_id."' AND fee_table_column_id=(SELECT fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)= 'default')";

			$rs=imw_query($cptQry);
		}else if($cpt_fee_id=='' || $cpt_fee_id<=0){
				$qry = "INSERT INTO cpt_fee_tbl 
						SET 
						cpt_cat_id = (SELECT cpt_cat_id FROM cpt_category_tbl WHERE LOWER(cpt_category)= 'contact lens'),
						cpt4_code = '".$_POST['cpt4_code']."',
						cpt_prac_code = '".$_POST['cpt_practice_code']."',
						cpt_desc = '".$_POST['manufacturer']."-".$_POST['style']."',
						status = 'Active'
						";
				imw_query($qry);
				$cpt_fee_id = imw_insert_id();
				
				//if($price>0){
				if($cpt_fee_id){
					$qry = "INSERT INTO cpt_fee_table
							SET cpt_fee_id = '".$cpt_fee_id."',
								fee_table_column_id = (SELECT fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)= 'default'),
								cpt_fee = '".$price."'
						   ";
					imw_query($qry);	   
				}
		}
		$_POST['cpt_fee_id'] = $cpt_fee_id;
		
		
		unset($_POST['cpt4_code']);
		unset($_POST['cpt_practice_code']);
		unset($_POST['price']);
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
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
		$q = "SELECT clmk.make_id,clmk.manufacturer,clmk.style,clmk.type,clmk.base_curve,clmk.diameter, clmk.cpt_fee_id,
        		IF(cft.cpt4_code IS NULL,'',cft.cpt4_code) AS cpt4_code,
                IF(cft.cpt_prac_code IS NULL,'',cft.cpt_prac_code) AS cpt_practice_code,
        	 	IF(cftb.cpt_fee IS NULL,'',CONCAT('$',cftb.cpt_fee)) AS price 
				FROM contactlensemake clmk 
                LEFT JOIN cpt_fee_tbl cft ON (cft.cpt_fee_id=clmk.cpt_fee_id)
				LEFT JOIN cpt_fee_table cftb ON (cftb.cpt_fee_id=cft.cpt_fee_id AND cftb.fee_table_column_id=(SELECT fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)= 'default'))
				WHERE clmk.del_status=0 AND source=0 
				ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		
		$cpt_prac_code_list = cpt_prac_code();
		$vender_name_list = vender_name();
		
		echo json_encode(array('records'=>$rs_set,'dx_code_list'=>$dx_code_list,'cpt_prac_code_list'=>$cpt_prac_code_list,'vender_name_list'=>$vender_name_list));
		break;
	default: 
}

function get_default_field(){
	$defaultFeeId='';
	$res=imw_query("Select fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)='default'");
	if($res && imw_num_rows($res)>0){
		$row=imw_fetch_array($res);
		$defaultFeeId = $row['fee_table_column_id'];
	}
	return $defaultFeeId;
}

function removeLineBreaks($str)
{
	return preg_replace("(\r\n|\n|\r)", " ", $str);
}
function cpt_prac_code() {
	$qryCPT = "SELECT cft.cpt_fee_id, cft.cpt4_code, cft.cpt_prac_code, IF(cfta.cpt_fee IS NULL,'',CONCAT('$',cfta.cpt_fee)) AS cpt_fee
				FROM cpt_fee_tbl cft
				JOIN cpt_category_tbl cpt_cat ON cpt_cat.cpt_cat_id = cft.cpt_cat_id
				JOIN cpt_fee_table cfta ON cft.cpt_fee_id = cfta.cpt_fee_id
				JOIN fee_table_column ftc ON cfta.fee_table_column_id = ftc.fee_table_column_id
				WHERE LOWER(cpt_cat.cpt_category) LIKE  '%contact lens%'
				AND cft.delete_status=0 
				AND cft.status = 'Active' 
				AND ftc.column_name = 'Default'
				ORDER BY cft.cpt_prac_code";
	$qryRes=imw_query($qryCPT);
	$retCPTPracStrArr = array();
	if($qryRes && imw_num_rows($qryRes)>0){
		while($qryRow=imw_fetch_array($qryRes)) {
			$retCPTPracStrArr[]=$qryRow;
		}
	}
	return $retCPTPracStrArr;
}

function vender_name() {
	$qry = "SELECT DISTINCT replace(vendor_name,'&amp;','&') as vendor_name FROM vendor_details WHERE vendor_status = '0'";
	$qryRes=imw_query($qry);
	$rows = array();
	if($qryRes && imw_num_rows($qryRes)>0){
		while($qryRow=imw_fetch_array($qryRes)) {
			$rows[]=$qryRow;
		}
	}
	return $rows;
}
?>