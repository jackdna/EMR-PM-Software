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

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'cat_id';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix  */

$cat_id	= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';
$cat_id = xss_rem($cat_id);	/* Reject arbitrary code - Security Fix */

$del_id = isset($_REQUEST['del_ids']) ? trim($_REQUEST['del_ids']) : ''; 
$del_id = xss_rem($del_id);	/* Reject arbitrary code - Security Fix */

$entered_date_time = date('Y-m-d H:i:s');

//-------BEGIN CALCULATE RECORDS LIMIT----------
$page = (isset($_REQUEST['page']) && $_REQUEST['page']!="")?$_REQUEST['page']:1;
$record_limit = (isset($_REQUEST['record_limit']) && $_REQUEST['record_limit']!="")?$_REQUEST['record_limit']:30;
$offset = ($page-1) * $record_limit;
$count = $record_limit;
	
//-------END CALCULATE RECORDS LIMIT----------
    
$operator_id = $_SESSION['authUserID'];
if($del_id){
	$task='delete';
	$_POST['pkId']=$del_id;
}
$table	= "icd10_data";
if($cat_id){
	$srchQryCat=" AND cat.id IN (".$cat_id.")";
}else{
	$q="SELECT id,title FROM icd10_categories where deleted='0' and title!='' order by title ASC LIMIT 0,1";
	$res=imw_query($q);
	if(imw_num_rows($res)>0){
		$row=imw_fetch_assoc($res);	
		$cat_d_id=$row['id'];
		$srchQryCat=" AND cat.id = ".$cat_d_id."";
	}
	$srchQryCat = '';
}
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "update ".$table." set deleted='1',del_operator_id='$operator_id',del_date_time='$entered_date_time' WHERE id IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST['id'];
		unset($_POST['id']);
		unset($_POST['task']);
		unset($_POST['breadCrumb']);
		
		if($_POST['no_bilateral']==""){
			$_POST['no_bilateral']=0;
		}
		$parent_all_codes_arr=explode(';',str_replace('; ',';',$_POST['master_codes']));
		$parent_all_codes="'".implode("','",$parent_all_codes_arr)."'";
		$_POST['master_ids']="";
		$cpt_qry=imw_query("select group_concat(id) as cpt_ids from icd10_data where icd10 in($parent_all_codes) and icd10!='' and deleted='0'");
		$cpt_row=imw_fetch_array($cpt_qry);
		$_POST['master_ids']=$cpt_row['cpt_ids'];
		$_POST['icd10_desc']=strtoupper($_POST['icd10_desc']);
		if($_POST['node_count']=="" ||$_POST['node_count']=="0"){
			unset($_POST['node_count']);
			$query_part = "";
			foreach($_POST as $k=>$v){
				$query_part .= $k."='".addslashes($v)."', ";
			}
			$query_part = substr($query_part,0,-2);
			if($id==''){
				$date_time_qry=" operator_id='".$operator_id."', entered_date_time='".$entered_date_time."',";
				$q = "INSERT INTO ".$table." SET $date_time_qry ".$query_part;
			}else{
				$date_time_qry=" modified_by='".$operator_id."', modified_date_time='".$entered_date_time."',";
				$q = "UPDATE ".$table." SET $date_time_qry ".$query_part." WHERE id='".$id."'";
			}
			$res = imw_query($q);
			if($res){
				echo 'Record Saved Successfully.'.$qy;
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		}else if($_POST['node_count']>0){
			$cnt=$_POST['node_count'];
			$q_r=' INSERT INTO ';
			$date_time_qry=" operator_id='".$operator_id."', entered_date_time='".$entered_date_time."',";
			$whr='';
			if($id!=""){
				$q_r=" UPDATE ";
				$whr=" WHERE id='".$id."'";
				$date_time_qry=" modified_by='".$operator_id."', modified_date_time='".$entered_date_time."',";
			}
			$qryicd=$q_r." icd10_data set $date_time_qry cat_id='".$_POST['cat_id']."',icd10_desc='".addslashes($_POST['icd10_desc'])."'".$whr;
			$q_i=imw_query($qryicd)or die(imw_error().$qryicd);
			$par_id=array();
			$c=$a=0;
			for($j=1;$j<=$cnt;$j++){
				if($j>0){
					if($_POST['node_desc'.($cnt-$a)]==""){
						$c++;	
					}else{
						break;	
					}
				}
				$a++;
			}
			for($i=1;$i<=$cnt;$i++){
				$qry_con=' INSERT INTO ';
				$whr=' ';
				$date_time_qry=" operator_id='".$operator_id."', entered_date_time='".$entered_date_time."',";
				if($i==1 && $id!=''){
					$parent_id=$id;
				}else if(imw_insert_id()){
					$parent_id = imw_insert_id();
				}else if(count($par_id)>0){
					$parent_id=end($par_id);
					unset($par_id);
					if($i==2 && $parent_id==0){$parent_id=$id;}
				}else{
					$parent_id = $_POST['node_id'.($i-1)];
				}
				if(trim($_POST['node_id'.$i])){
					$qry_con=' UPDATE ';
					$whr=' WHERE id='.$_POST['node_id'.$i];
					$date_time_qry=" modified_by='".$operator_id."', modified_date_time='".$entered_date_time."',";
				}
				$cat_id=$pqri_val=$cmnt_val=$icd9_v=$icd10_v=$icd9_desc=$icd_lat=$icd10_desc="";
				$cat_id=trim($_POST['cat_id']);
				$pqri_val=trim(addslashes($_POST['pqri']));
				$cmnt_val=trim(addslashes($_POST['cmnts']));
				$icd10_desc=addslashes($_POST['node_desc'.$i]);
				
				$deleted=0;
				$icd_10="";
				if(trim($icd10_desc)){
					$icd_10=" icd10_desc='".$icd10_desc."', ";
				}
				if($qry_con==' INSERT INTO ' && $icd10_desc==''){
					continue;	
				}else if($icd10_desc==''){
					$deleted=1;
					$par_id[]=$_POST['node_id'.($i-1)];
				}
				if($c>0){$cnt_c=($cnt-$c);}else{$cnt_c=$cnt;}
				if($i==($cnt_c)){
					$icd9_v=trim(addslashes($_POST['icd9']));
					$icd10_v=trim(addslashes($_POST['icd10']));
					$icd9_desc=trim(addslashes($_POST['icd9_desc']));	
					$icd_lat=trim(addslashes($_POST['laterality']));
				}
				
				$qry=$qry_con." icd10_data set $date_time_qry cat_id='".$cat_id."',icd9='".$icd9_v."',icd10='".$icd10_v."',
								laterality='".$icd_lat."',pqri='".$pqri_val."',cmnts='".$cmnt_val."',icd9_desc='".$icd9_desc."',
								".$icd_10." deleted='".$deleted."',parent_id='".$parent_id."'".$whr;
				$res=imw_query($qry);
			}
			if($res){
				echo 'Record Saved Sucessfully.';
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$qy;
			}
		}
		
		break;
	case 'show_list':
		$icd10_cat 			= icd10_cat();
		$icd10_laterality	= icd10_laterality();
		$unique_parents		= get_unique_parent_ids();
		$q = "SELECT ic.id,ic.cat_id,ic.icd9,ic.icd9_desc,ic.icd10,ic.icd10_desc,'' AS breadCrumb, ic.laterality,ic.parent_id,ic.staging,ic.severity,ic.master_codes,ic.group_heading,ic.no_bilateral,ic.status FROM ".$table." as ic LEFT JOIN icd10_categories as cat on ic.cat_id=cat.id WHERE ic.deleted='0' ".$srchQryCat." ORDER BY ic.$so $soAD";
		$total_pages = ceil(imw_num_rows(imw_query($q))/$count);
		$q .= " LIMIT $offset,$count"; 
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$id 		= $rs['id'];
				$parent_id	= $rs['parent_id'];
				if($parent_id>0){
					$parentArray = get_parent($id);
					$breadCrumb = makeBreadCrumb($parentArray);
					$rs['breadCrumb'] = $breadCrumb;
				}
				$rs_set[]	= $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set,'icd10_cat'=>$icd10_cat,'icd10_laterality'=>$icd10_laterality,'unique_parents'=>$unique_parents,'total_pages'=>$total_pages));
		break;
	case 'loadnode':
		$return = array();
		$id = $_REQUEST['pkId'];
		$parentArray = get_parent($id);
		$record 		= array();
		$record['id'] 			= $parentArray['id'];
		$record['cat_id'] 		= $parentArray['cat_id'];
		$record['icd10_desc'] 	= $parentArray['icd10_desc'];
		$record['parent_id'] 	= $parentArray['parent_id'];
		$breadCrumb	 			= makeBreadCrumb($parentArray);
		$return['breadCrumb'] 	= $breadCrumb;
		$return['nodes']	 	= get_nodes($id);
		$return['record']	  	= $record;
		echo json_encode($return);
		break;
	default: 
}

function get_nodes($parent_id){
	$q = "SELECT id,icd10_desc FROM icd10_data WHERE parent_id='$parent_id'";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$rs = array();
		while($rss = imw_fetch_assoc($res)){
			$rs[] = $rss;
		}
		return $rs;
	}
	return false;
}


function get_unique_parent_ids(){
	$q = "SELECT DISTINCT(parent_id) AS id FROM icd10_data WHERE parent_id>0 and deleted='0'";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$rs = array();
		while($rss = imw_fetch_assoc($res)){
			$id	  = $rss['id'];
			$rs[$id] = $id;
		}
		return $rs;
	}
	return false;
}

function get_parent($icd10_id){
	$icd10_id = intval($icd10_id);
	if($icd10_id>0){
		$q = "SELECT * FROM icd10_data WHERE id= '$icd10_id' and deleted='0'";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$array = array();
			$rs = imw_fetch_assoc($res);
			if(intval($rs['parent_id'])>0){
				$parentInfoArr = get_parent($rs['parent_id']);
				$rs['parent_rs'] = $parentInfoArr;
			}
			return $rs;
		}
		
	}
	return false;
}

function makeBreadCrumb($get_parent_array,$bold=true){
	$string = $get_parent_array['id'].':~:'.$get_parent_array['icd10_desc'];//if($bold){$string = '<b>'.$string.'</b>';}
	if($get_parent_array['parent_id']>0 && is_array($get_parent_array['parent_rs'])){
		$string = makeBreadCrumb($get_parent_array['parent_rs'],false).'~::~'.$string;
	}
	return $string;
}

function icd10_cat(){
	$q_cat = "SELECT id,title FROM icd10_categories where deleted='0' ORDER BY title";
	$r_cat = imw_query($q_cat);
	$rs_set = array();
	if($r_cat && imw_num_rows($r_cat)>0){
		while($rs = imw_fetch_assoc($r_cat)){
			$rs_set[] = $rs;
		}
	}
	return $rs_set;
}

function icd10_laterality($under=0){
	$q_cat = "SELECT i1.id, if (GROUP_CONCAT(i2.abbr) IS NULL, i1.title, CONCAT(i1.title,' (',GROUP_CONCAT(i2.abbr ORDER BY i2.id),')')) AS title FROM `icd10_laterality` i1 LEFT JOIN icd10_laterality i2 ON (i1.id=i2.under) WHERE i1.under=0 AND  i1.deleted=0 AND i2.deleted =0 GROUP BY i1.id";
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