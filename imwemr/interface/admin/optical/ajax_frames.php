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

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'make_frame';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$name	= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : 'a';
$name = xss_rem($name);	/** Reject parameter with arbitrary characters - Security Fix */

$table	= "optical_frames";
$pkId	= "optical_frames_id";
$chkFieldAlreadyExist = "optical_frames_id";
if($name){
	$name=" AND make_frame LIKE '".$name."%' ";
}

if($_FILES['files']!=''){
	$upload = $_FILES['files'];
	$filename=$upload['name'][0];
	$filetype=$upload['type'][0];
	$filesize=$upload['size'][0];
	$fileError=$upload['error'][0];
	$file_tmp=$upload['tmp_name'][0];
	$uploadPath = "vendor_images/";		

	$imageName = $uploadPath.session_id().'_'.$filename;
	$_FILES['files']['name'][0] = $imageName;
	
	if(is_uploaded_file($file_tmp)){
		if($fileError <= 0){
			$imgErr = 1;
			$logoName = $filename;
			$logoType = $filetype;	
			
			if((strtolower($logoType) == "image/jpeg") || (strtolower($logoType) == "image/png") || (strtolower($logoType) == "image/png") || (strtolower($logoType) == "image/gif")){
				if($file_tmp && $imageName){
					$imgErr = 0;							
				
					if(trim($_REQUEST['opt_frame_id'])) {
						$qry	= "UPDATE ".$table." SET picture_vendor = '".$imageName."' WHERE ".$pkId." = ".$_REQUEST['opt_frame_id'];
						$res = 	imw_query($qry);
					}else {
						$_SESSION["OPT_VENDOR_PIC"] = $imageName;	
					}
				}
			}
		}
	} 
	require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');
	$uploadPath = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/vendor_images/";
	$uploadPath_web = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/vendor_images/";
	// Change Default Option for upload handler before calling class constructor
	$options = array(
		'script_url' => $GLOBALS['php_server'].'/interface/admin/optical/upload_win.php',
		'upload_dir' => $uploadPath,
		'upload_url' => $uploadPath_web,
		'access_control_allow_origin' => '*','access_control_allow_credentials' => false,
		'access_control_allow_methods' => array('OPTIONS','HEAD','GET','POST','PUT','PATCH','DELETE'),
		'access_control_allow_headers' => array('Content-Type','Content-Range','Content-Disposition'),
		'inline_file_types' => '/\.(gif|jpe?g|png)$/i', 'accept_file_types' => '/\.(gif|jpe?g|png|pdf|tif|tiff)$/i',
		'max_file_size' => null,'min_file_size' => 1,'max_number_of_files' => null,'max_width'=>null,'max_height'=>null,'min_width'=>1,'min_height'=>1,
		'discard_aborted_uploads'=>true,'orient_image'=>false,'image_versions'=>''
	);
	$upload_handler = new UploadHandler($options,true);
} 

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "UPDATE ".$table." SET frame_status = '1' WHERE ".$pkId." IN (".$id.")";
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
		unset($_POST['MAX_FILE_SIZE']);
		$firstChar = $_POST['patient_discount_actual'][0];
		if($firstChar != '$'){
			$_POST['discount_patient'] = substr($_POST['patient_discount_actual'],0,-1);
			$_POST['patient_discount_actual'] = NULL;
		}
		else{
			$_POST['patient_discount_actual'] = substr($_POST['patient_discount_actual'],1);
			$_POST['discount_patient'] = NULL;
		}
		$firstCharNew = $_POST['family_discount_actual'][0];
		if($firstCharNew != '$'){
			$_POST['discount_family_friend'] = substr($_POST['family_discount_actual'],0,-1);
			$_POST['family_discount_actual'] = NULL;
		}
		else{
			$_POST['family_discount_actual'] = substr($_POST['family_discount_actual'],1);
			$_POST['discount_family_friend'] = NULL;
		}	
		
		if(trim($_POST['date_received'])) {
			$_POST['date_received'] = getDateFormatNew(trim($_POST['date_received']));	
		}
		if(trim($_POST['date_sold'])) {
			$_POST['date_sold'] = getDateFormatNew(trim($_POST['date_sold']));	
		}
		$firstCharCost = $_POST['cost_price'][0];
		if($firstCharCost == '$'){
			$_POST['cost_price'] = substr($_POST['cost_price'],1);
		}
		$firstCharRetail = $_POST['retail_price'][0];
		if($firstCharRetail == '$'){
			$_POST['retail_price'] = substr($_POST['retail_price'],1);
		}
		$firstCharOffPrice = $_POST['off_price'][0];
		if($firstCharOffPrice == '$'){
			$_POST['off_price'] = substr($_POST['off_price'],1);
		}
		
		unset($_POST["picture_vendor"]);
		if(trim($_SESSION["OPT_VENDOR_PIC"])) {
			$_POST["picture_vendor"] = $_SESSION["OPT_VENDOR_PIC"];
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
		
		if(trim($_SESSION["OPT_VENDOR_PIC"])) {
			unset($_SESSION["OPT_VENDOR_PIC"]);
		}
		$q = "SELECT optical_frames_id,replace(opf.vendor_name,'&amp;','&') AS vendor_name,opf.make_frame,
				opf.frame_style,opf.frame_color,
				opf.cost_price,opf.retail_price,
				IF(opf.qty_left!='0',opf.qty_left,'')AS qty_left,
				IF(opf.qty_ordered!='0',opf.qty_ordered,'')AS qty_ordered,
				opf.bar_code_id,
				IF(pst.facilityPracCode!='',facilityPracCode,'')AS facilityPracCode,
				IF(opf.date_received!='0000-00-00',DATE_FORMAT( opf.date_received, '%m-%d-%Y' ) ,'')AS date_received,
				IF(opf.date_sold!='0000-00-00',DATE_FORMAT( opf.date_sold, '%m-%d-%Y' ) ,'')AS date_sold,
				IF(opf.discount_family_friend!='0',CONCAT(discount_family_friend,'%') ,'')AS discount_family_friend,
				IF(opf.family_discount_actual!='0',CONCAT('$',FORMAT(family_discount_actual,2)),'')AS family_discount_actual,
				IF(opf.discount_patient!='0',CONCAT(discount_patient,'%') ,'')AS discount_patient,
				IF(opf.patient_discount_actual!='0',CONCAT('$',FORMAT(patient_discount_actual,2)),'')AS patient_discount_actual,
				opf.picture_vendor as picture_vendor,opf.comments,opf.operator_id,opf.frame_status,opf.frame_created_date,opf.family_friend,
				opf.patient,opf.horizontal,opf.bridge,opf.vertical,opf.diagonal,opf.qty_recieved,opf.off_price,opf.pos_facility_id
				FROM optical_frames opf  
				LEFT JOIN pos_facilityies_tbl pst ON (pst.pos_facility_id=opf.pos_facility_id)
				WHERE frame_status = '0'  ".$name."
				ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs['cost_price'] = $rs['cost_price'] ? numberFormat($rs['cost_price'],2) : "";
				$rs['retail_price'] = $rs['retail_price'] ? numberFormat($rs['retail_price'],2)  : "";
				$rs['off_price'] = $rs['off_price'] ? numberFormat($rs['off_price'],2)  : "";
				if(!trim($rs['patient_discount_actual']))  	{
					$rs['patient_discount_actual'] = $rs['discount_patient'];		
				}
				unset($rs['discount_patient']);
				if(!trim($rs['family_discount_actual']))  	{
					$rs['family_discount_actual'] = $rs['discount_family_friend'];	
				}
				unset($rs['discount_family_friend']);
				$rs_set[] = $rs;
			}
		}
		$vender_name_list = vender_name();
		$pos_facility_list = pos_facility_fun();
		echo json_encode(array('records'=>$rs_set,'vender_name_list'=>$vender_name_list,'pos_facility_list'=>$pos_facility_list,'q'=>$q));
		break;
	default: 
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

function getDateFormatNew($date){
	$date = substr($date,0,10);
	$old_format = preg_split('/-/',$date);
	$date = preg_replace('/[^0-9]/','',$date);
	$date_result = '';
	$date = substr($date,0,8);
	if(empty($date) == false && $date != '00000000'){
		if(strlen(end($old_format)) == 2){
			$date_result = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/","$2-$3-$1",$date);
		}
		else{
			$date_result = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{4})/","$3-$1-$2",$date);
		}
	}
	return $date_result;
}
?>